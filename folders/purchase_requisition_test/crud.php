<?php

// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

// Database Country Table Name
$table              = "purchase_requisition";
$sub_list_table     = "purchase_requisition_items";
$documents_upload   = "purchase_requisition_uploads";
// Include DB file and Common Functions
include '../../config/dbconfig.php';
include '../../config/new_db.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$random_sc          = "";
$random_no          = "";
$sub_group_unique_id = "";
$product_name       = "";
$description        = "";

$is_active          = "";
$unique_id          = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$user_id = $_SESSION['sess_user_id'];
$date = date('Y-m-d H:i:s', time());



switch ($action) {
    case "requisition_sub_add_update":
        $main_unique_id         = $_POST["main_unique_id"];
        $sublist_unique_id      = $_POST["sublist_unique_id"];
        $item_code              = $_POST["item_code"];
        $item_description       = $_POST["item_description"];
        $quantity               = $_POST["quantity"];
        $uom                    = $_POST["uom"];
        $item_remarks           = $_POST["item_remarks"];
        $required_delivery_date = $_POST["required_delivery_date"];
        $from_sales_order       = isset($_POST["from_sales_order"]) ? 1 : 0;

        $columns = [
            "main_unique_id"         => $main_unique_id,
            "item_code"              => $item_code,
            "item_description"       => $item_description,
            "quantity"               => $quantity,
            "uom"                    => $uom,
            "item_remarks"           => $item_remarks,
            "required_delivery_date" => $required_delivery_date,
            "from_sales_order"       => $from_sales_order
        ];

        if (!empty($sublist_unique_id)) {
            $columns["updated"] = $date;
            $columns["updated_user_id"] = $user_id;
            $where = ["unique_id" => $sublist_unique_id];
            $action_obj = $pdo->update("purchase_requisition_items", $columns, $where);
            $msg = "update";
        } else {
            $columns["unique_id"] = unique_id();
            $columns["created"] = $date;
            $columns["created_user_id"] = $user_id;
            $action_obj = $pdo->insert("purchase_requisition_items", $columns);
            $msg = "add";
        }
        
        error_log("action_obj: " . print_r($action_obj, true) . "\n", 3, "reqaddup.log");

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
        break;


    case "createupdate":

        $company_id         = $_POST["company_id"];
        $project_id         = $_POST["project_id"];
        $service_type       = $_POST["service_type"];
        $requisition_for    = $_POST["requisition_for"];
        $requisition_type   = $_POST["requisition_type"];
        $requisition_date   = $_POST["requisition_date"];
        $requested_by       = $_SESSION['user_name'];
        $sales_order_id     = ($requisition_for == 2 || $requisition_for == 3) ? $_POST["sales_order_id"] : null;
        $remarks            = $_POST["remarks"];
        $unique_id          = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();

        $columns = [
            "unique_id"          => $unique_id,
            "company_id"         => $company_id,
            "project_id"         => $project_id,
            "service_type"       => $service_type,
            "requisition_for"    => $requisition_for,
            "requisition_type"   => $requisition_type,
            "requisition_date"   => $requisition_date,
            "requested_by"       => $requested_by,
            "sales_order_id"     => $sales_order_id,
            "remarks"            => $remarks,
            "created_user_id"    => $user_id,
            "created"            => $date
        ];

        // Check if it exists
        $check_query = [$table, ["COUNT(unique_id) AS count"]];
        $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

        $action_obj = $pdo->select($check_query, $check_where);

        if ($action_obj->status && $action_obj->data[0]["count"]) {
            // Update mode — do NOT change pr_number
            unset($columns["unique_id"], $columns["created_user_id"], $columns["created"]);
            $columns["updated_user_id"] = $user_id;
            $columns["updated"]         = $date;

            $update_where = ["unique_id" => $unique_id];
            $action_obj   = $pdo->update($table, $columns, $update_where);
            $msg          = "update";
        } else {
            // INSERT mode — generate PR number
            $company_code_arr = company_code("", $company_id);
            $company_code = $company_code_arr[0]["company_code"];

            $acc_year = $_SESSION["acc_year"];
            $prefix = "PR/{$acc_year}/{$company_code}/";

            // Get last PR number
            $pr_no_result = $pdo->select(
                [$table, ["pr_number"]],
                "pr_number LIKE '{$prefix}%' ORDER BY pr_number DESC LIMIT 1"
            );

            $next_number = "001";
            if ($pr_no_result->status && !empty($pr_no_result->data)) {
                $last_pr = $pr_no_result->data[0]["pr_number"];
                $last_split = explode("/", $last_pr);
                $last_num = isset($last_split[3]) ? (int)$last_split[3] : 0;
                $next_number = str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
            }

            $columns["pr_number"] = "{$prefix}{$next_number}";

            // Now insert
            $action_obj = $pdo->insert($table, $columns);
            $msg        = "create";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => ["unique_id" => $unique_id],
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
        break;





    case 'datatable':

    // ---------------------------- INPUTS ---------------------------- //
    $search     = $_POST['search']['value'] ?? '';
    $length     = $_POST['length'] ?? 10;
    $start      = $_POST['start'] ?? 0;
    $draw       = $_POST['draw'] ?? 1;
    $limit      = ($length == '-1') ? '' : $length;
    $data       = [];

    // ---------------------------- BASE QUERY ---------------------------- //
    $columns = [
        "@a:=@a+1 AS s_no",
        "pr_number",
        "company_id",
        "project_id",
        "requisition_for",
        "requisition_type",
        "requisition_date",
        "requested_by",
        "remarks",
        "foreclose_status",
        "unique_id"
    ];

    $table_details = [
        $table . " , (SELECT @a:= " . $start . ") AS a",
        $columns
    ];

    $where = "is_delete = 0";

    if (!empty($_POST['pr_number'])) {
        $where .= " AND unique_id = '" . trim($_POST['pr_number']) . "'";
    }
    if (!empty($_POST['company_name'])) {
        $where .= " AND company_id = '" . trim($_POST['company_name']) . "'";
    }
    if (!empty($_POST['project_name'])) {
        $where .= " AND project_id = '" . trim($_POST['project_name']) . "'";
    }
    if (!empty($_POST['type_of_service'])) {
        $where .= " AND requisition_type = '" . trim($_POST['type_of_service']) . "'";
    }
    if (!empty($_POST['requisition_for'])) {
        $where .= " AND requisition_for = '" . trim($_POST['requisition_for']) . "'";
    }
    if (!empty($_POST['requisition_date'])) {
        $where .= " AND requisition_date = '" . trim($_POST['requisition_date']) . "'";
    }

    // ---------------------------- OPTIONS ---------------------------- //
    $requisition_type_options = [
        1 => ["unique_id" => "1", "value" => "Regular"],
        '683568ca2fe8263239' => ["unique_id" => "683568ca2fe8263239", "value" => "Service"],
        '683588840086c13657' => ["unique_id" => "683588840086c13657", "value" => "Capital"]
    ];

    $requisition_for_options = [
        1 => ["unique_id" => "1", "value" => "Direct"],
        2 => ["unique_id" => "2", "value" => "SO"],
        3 => ["unique_id" => "3", "value" => "Ordered BOM"]
    ];

    // ---------------------------- ORDER / SEARCH ---------------------------- //
    $order_column = $_POST["order"][0]["column"] ?? 0;
    $order_dir    = $_POST["order"][0]["dir"] ?? 'asc';
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);
    $search_sql   = datatable_searching($search, $columns);
    if ($search_sql) $where .= " AND $search_sql";

    // ---------------------------- MAIN SELECT ---------------------------- //
    $sql_function = "SQL_CALC_FOUND_ROWS";
    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records = total_records();

    if ($result->status) {
        foreach ($result->data as $value) {

            // ---------------- COMPANY / PROJECT ---------------- //
            $company_data = company_name($value['company_id']);
            $value['company_id'] = $company_data[0]['company_name'] ?? '';

            $proj_data = project_name($value['project_id']);
            $value['project_id'] = ($proj_data[0]['project_code'] ?? '') . " / " . ($proj_data[0]['project_name'] ?? '');

            // ---------------- REQUISITION MAPPINGS ---------------- //
            $value['requisition_for']  = $requisition_for_options[$value['requisition_for']]['value'] ?? '-';
            $value['requisition_type'] = $requisition_type_options[$value['requisition_type']]['value'] ?? '-';

            // ========================================================
            // STATUS SUMMARY (L1/L2)
            // ========================================================
            $columns_status = [
                "SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) AS l1_approved",
                "SUM(CASE WHEN status = 2 THEN 1 ELSE 0 END) AS l1_rejected",
                "SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) AS l1_pending",
                "SUM(CASE WHEN lvl_2_status = 1 THEN 1 ELSE 0 END) AS l2_approved",
                "SUM(CASE WHEN lvl_2_status = 2 THEN 1 ELSE 0 END) AS l2_rejected",
                "SUM(CASE WHEN lvl_2_status = 0 THEN 1 ELSE 0 END) AS l2_pending"
            ];
            $where_status = [
                "main_unique_id" => $value['unique_id'],
                "is_delete"      => 0
            ];
            $status_res = $pdo->select(["purchase_requisition_items", $columns_status], $where_status);

            $status_label = 'Pending (L1)';
            $btn_color = 'warning';
            $l1_approved = $l1_rejected = $l2_approved = $l2_rejected = 0;

            if ($status_res->status && !empty($status_res->data)) {
                $s = $status_res->data[0];
                $l1_approved = $s['l1_approved'];
                $l1_rejected = $s['l1_rejected'];
                $l2_approved = $s['l2_approved'];
                $l2_rejected = $s['l2_rejected'];

                if ($l1_rejected > 0) {
                    $status_label = 'Rejected (L1)'; $btn_color = 'danger';
                } elseif ($l2_rejected > 0) {
                    $status_label = 'Rejected (L2)'; $btn_color = 'danger';
                } elseif ($s['l1_pending'] > 0) {
                    $status_label = 'Pending (L1)'; $btn_color = 'warning';
                } elseif ($l1_approved > 0 && $s['l2_pending'] > 0) {
                    $status_label = 'Pending (L2)'; $btn_color = 'info';
                } elseif ($l1_approved > 0 && $l2_approved > 0) {
                    $status_label = 'Approved'; $btn_color = 'success';
                }
            }

            $value['remarks'] = "<button type='button' class='btn btn-sm btn-$btn_color' onclick=\"showStatusModal('{$value['unique_id']}')\">$status_label</button>";

            // ========================================================
            // FORECLOSE LOGIC WITH QUANTITY CHECK
            // ========================================================
            $foreclose_btn = "<span class='badge bg-secondary text-light fs-5'>Not Available</span>";
            $detail_logs = [];
            $all_items_fully_ordered = true;
            $has_po = false;

            // ---- STEP 1: Fetch all PR items ----
            $pr_items = $pdo->select(
                ["purchase_requisition_items", ["unique_id", "quantity"]],
                ["main_unique_id" => $value['unique_id'], "is_delete" => 0]
            );

            if ($pr_items->status && !empty($pr_items->data)) {
                foreach ($pr_items->data as $pr_item) {
                    $pr_sub_id = $pr_item['unique_id'];
                    $req_qty   = (float)$pr_item['quantity'];
            
                    // ---- STEP 2: Get total ordered qty + screen IDs ----
                    $po_items = $pdo->select(
                        ["purchase_order_items", ["SUM(quantity) AS total_order_qty", "GROUP_CONCAT(DISTINCT screen_unique_id SEPARATOR ', ') AS screen_ids"]],
                        ["pr_sub_unique_id" => $pr_sub_id, "is_delete" => 0]
                    );
            
                    $ordered_qty = ($po_items->status && !empty($po_items->data[0]['total_order_qty']))
                        ? (float)$po_items->data[0]['total_order_qty']
                        : 0;
            
                    $screen_id_list = $po_items->data[0]['screen_ids'] ?? '';
            
                    if ($ordered_qty > 0) $has_po = true;
            
                    // Log each PR item detail
                    $detail_logs[] = "PR_SUB=$pr_sub_id | REQ_QTY=$req_qty | ORDER_QTY=$ordered_qty | SCREEN_IDS=$screen_id_list";
            
                    // If any PR item not fully ordered, mark as incomplete
                    if ($ordered_qty < $req_qty) {
                        $all_items_fully_ordered = false;
                    }
                }
            } else {
                $all_items_fully_ordered = false;
            }


            // ---- STEP 3: Button decision ----
            $foreclose_status = $value['foreclose_status'] ?? 0;

            if ($foreclose_status == 1) {
                $foreclose_btn = "<span class='badge bg-success text-light fs-5'>Foreclosed</span>";
            } elseif ($all_items_fully_ordered && $has_po) {
                $foreclose_btn = "<span class='badge bg-info text-dark fs-5'>PO Raised</span>";
            } elseif ($l1_rejected > 0 || $l2_rejected > 0) {
                $foreclose_btn = "<span class='badge bg-secondary text-light fs-5'>Not Available</span>";
            } elseif (($l1_approved > 0 || $l2_approved > 0) && $l1_rejected == 0 && $l2_rejected == 0) {
                $foreclose_btn = "<button type='button' class='btn btn-sm btn-dark' onclick=\"foreclosePR('{$value['unique_id']}')\">Foreclose</button>";
            } else {
                $foreclose_btn = "<span class='badge bg-secondary text-light fs-5'>Not Available</span>";
            }

            // ---- STEP 4: Log results ----
            $log_line = "PR_NUMBER={$value['pr_number']} | PR_UNIQUE={$value['unique_id']} | ALL_ITEMS_FULLY_ORDERED=" . ($all_items_fully_ordered ? "YES" : "NO") . " | HAS_PO=" . ($has_po ? "YES" : "NO");
            $log_line .= " | FORECLOSE_STATUS={$foreclose_status}";
            if (!empty($detail_logs)) $log_line .= " | DETAILS=" . implode(' || ', $detail_logs);
            file_put_contents(__DIR__ . "/foreclose_debug.log", $log_line . "\n", FILE_APPEND);

            // ---------------- BUTTONS ---------------- //
            $btn_view    = btn_views($folder_name, $value['unique_id']);
            $btn_print   = btn_prints($folder_name, $value['unique_id']);
            $btn_upload  = btn_docs($folder_name, $value['unique_id']);
            $btn_update  = btn_update($folder_name, $value['unique_id']);
            $btn_delete  = btn_delete($folder_name, $value['unique_id']);

            unset($value['foreclose_status']);

            $value['unique_id'] = $btn_update . $btn_delete . $btn_upload;
            array_splice($value, -1, 0, [$btn_view, $btn_print, $foreclose_btn]);

            $data[] = array_values($value);
        }

        $json_array = [
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data,
            "testing"         => $result->sql
        ];
    } else {
        $json_array = [
            "draw"            => intval($draw),
            "recordsTotal"    => 0,
            "recordsFiltered" => 0,
            "data"            => [],
            "error"           => $result->error
        ];
    }

    echo json_encode($json_array);
    break;

    
   case 'fetch_item_status':

    $main_id = $_POST['main_unique_id'] ?? '';

    $columns = [
        "item_code",
        "item_description",
        "quantity",
        "uom",
        "required_delivery_date",
        "status",
        "lvl_2_status",
        "reason",
        "lvl_2_reason"
    ];

    $where = [
        "main_unique_id" => $main_id,
        "is_delete"      => 0
    ];

    $result = $pdo->select(["purchase_requisition_items", $columns], $where);
    $data_array = [];

    if ($result->status && !empty($result->data)) {
        foreach ($result->data as $row) {
            // ===== ITEM NAME + CODE =====
            $display_item = $row['item_code'];
            $item_info = item_name_list($row['item_code']);
            if (!empty($item_info) && isset($item_info[0]['item_name'])) {
                $display_item = $item_info[0]['item_name'] . " / " . $item_info[0]['item_code'];
            }

            // ===== UOM TEXT =====
            $uom_name = '';
            if (!empty($row['uom'])) {
                $uom_info = unit_name($row['uom']);
                if (!empty($uom_info) && isset($uom_info[0]['unit_name'])) {
                    $uom_name = $uom_info[0]['unit_name'];
                }
            }

            // ===== LOGIC: if L1 rejected, wipe L2 =====
            $lvl_1_status = (int) $row['status'];
            $lvl_2_status = ($lvl_1_status === 2) ? null : (int) $row['lvl_2_status']; // null if rejected
            $lvl_2_reason = ($lvl_1_status === 2) ? null : $row['lvl_2_reason'];

            // ===== BUILD CLEAN ARRAY =====
            $data_array[] = [
                "item_code"             => $display_item,
                "item_description"      => $row['item_description'],
                "quantity"              => $row['quantity'],
                "uom"                   => $uom_name,
                "required_delivery_date"=> $row['required_delivery_date'],
                "status"                => $lvl_1_status,
                "lvl_2_status"          => $lvl_2_status,
                "reason"                => $row['reason'],
                "lvl_2_reason"          => $lvl_2_reason
            ];
        }

        $json_array = [
            "status" => true,
            "data"   => $data_array,
            "error"  => "",
            "sql"    => $result->sql
        ];
    } else {
        $json_array = [
            "status" => false,
            "data"   => [],
            "error"  => $result->error,
            "sql"    => $result->sql
        ];
    }

    echo json_encode($json_array);
    break;




        case 'delete':

            $unique_id = $_POST['unique_id'];
            $remarks   = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
        
            $columns = [
                "is_delete"           => 1,
                "is_delete_remarks"   => $remarks
            ];
        
            $update_where = [
                "unique_id" => $unique_id
            ];
        
            $action_obj = $pdo->update($table, $columns, $update_where);
        
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = $status ? "success_delete" : "error";
        
            $json_array = [
                "status" => $status,
                "data"   => $data,
                "error"  => $error,
                "msg"    => $msg,
                "sql"    => $sql
            ];
        
            echo json_encode($json_array);
            break;
        







    case 'project_name':

        $company_id          = $_POST['company_id'];

        $project_name_options  = get_project_name("", $company_id);

        $project_name_options  = select_option($project_name_options, "Select the Project Name");

        echo $project_name_options;

        break;
        
case 'linked_so':

    $project_id      = $_POST['project_id'] ?? '';
    $requisition_for = $_POST['requisition_for'] ?? '';

    $so_id = "";

    // Get SO for project
    $so_data = get_project_so($project_id);
    if (!empty($so_data) && !empty($so_data[0]["sales_order_id"])) {
        $so_id = $so_data[0]["sales_order_id"];
    }
    
    error_log("Before if check | so_id={$so_id}\n", 3, "result.log");
    // Extra OBOM validation if requisition_for == 3
    if ($requisition_for == 3 && !empty($so_id)) {
        error_log("Before OBOM check | so_id={$so_id}\n", 3, "result.log");
        $result_check = obom_check($so_id);
        error_log(print_r($result_check, true), 3, "result.log");
        if ($result_check === 0) {
            $so_id = "";
        }
    }
    
    if ($requisition_for == 2 && !empty($so_id)) {
        error_log("Before OBOM check | so_id={$so_id}\n", 3, "result.log");
        $result_check = obom_check($so_id);
        error_log(print_r($result_check, true), 3, "result.log");
        if ($result_check === 1) {
            $so_id = "";
        }
    }

    error_log("linked_so | project_id: {$project_id}, requisition_for: {$requisition_for}, so_id: {$so_id}\n", 3, "so.log");

    if (!empty($so_id)) {
        $sales_order_options = sales_order($so_id);
        $sales_order_options = select_option($sales_order_options, "Select the Sales Order");
    } else {
        $sales_order_options = "<option value=''>Not Applicable</option>";
    }

    echo $sales_order_options;

break;


    case "purchase_sublist_datatable":
    $main_unique_id = $_POST["main_unique_id"];
    $btn_prefix     = "pr_sub";
    $type = $_POST['type'];
    $so_id = $_POST['so_id'];

    $columns = [
        "@a:=@a+1 as s_no",
        "item_code",
        "item_description",
        "quantity",
        "uom",
        "item_remarks",
        "required_delivery_date",
        "unique_id",
        "from_sales_order"
    ];

    $table_details = [
        "purchase_requisition_items, (SELECT @a:=0) as a",
        $columns
    ];

    $where = [
        "main_unique_id" => $main_unique_id,
        "is_delete"      => 0
    ];

    $result = $pdo->select($table_details, $where);
    
    error_log(print_r($result, true) . "\n", 3, "psl.log");

    $data = [];

    if ($result->status) {
        foreach ($result->data as $row) {
            // Fetch item data
            $item_data = item_name_list($row["item_code"]);
            error_log("item_data: " . print_r($item_data, true) . "\n", 3, "log/item.log");
            
            // If item_data empty, try product_master
            if (empty($item_data)) {
                $prod_res = $pdo->select(
                    ["product_master", ["product_name", "product_code", "description"]],
                    ["unique_id" => $row["item_code"], "is_delete" => 0]
                );
                if ($prod_res->status && !empty($prod_res->data)) {
                    $item_data[0] = [
                        "item_name" => $prod_res->data[0]["product_name"],
                        "item_code" => $prod_res->data[0]["product_code"],
                        "description" => $prod_res->data[0]["description"]
                    ];
                }
            }
            
           // Check if item is FAB
            $is_fab = !empty($item_data[0]["item_code"]) && strpos($item_data[0]["item_code"], "-FAB-") !== false;
            
            // Build display_code smartly
            if (!empty($item_data[0]["item_name"]) && !empty($item_data[0]["item_code"])) {
                $display_code = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];
            } elseif (empty($item_data[0]["item_code"])) {
                $display_code = $item_data[0]["item_name"];
            } elseif (empty($item_data[0]["item_name"])) {
                $display_code = $item_data[0]["item_code"];
            } else {
                $display_code = "-";
            }

            
            // Default class
            $display_class = "no-sublist";
            
            // If FAB, check if it has a sublist
            $sublist = [];
            if ($is_fab) {
                $prod_unique_id = $row["item_code"];
            
                // First, fetch the type from obom_list for the given product
                $obom_res = $pdo->select(
                    ["obom_list", ["type"]],
                    ["so_unique_id" => $so_id, "is_delete" => 0]
                );
                
                error_log(print_r($obom_res, true) . "\n", 3, "obom.log");
            
                // Default type = 0 if no record found
                $prod_type = ($obom_res->status && !empty($obom_res->data))
                    ? intval($obom_res->data[0]["type"])
                    : 0;
            
                // If type == 1, force empty sublist and no toggle
                if ($prod_type == 1) {
                    $display_class = "no-sublist";
                    $sublist = [];
                } else {
                    // Fetch product sublist as before
                    $sublist_res = $pdo->select(
                        ["obom_child_table", ["item_unique_id", "qty", "uom_unique_id", "remarks"]],
                        ["so_unique_id" => $so_id, "is_delete" => 0]
                    );
            
                    if ($sublist_res->status && !empty($sublist_res->data)) {
                        $display_class = "fab-toggle"; // <-- only FAB with sublist gets this class
            
                        foreach ($sublist_res->data as $idx => $sub) {
                            $sub_item = $pdo->select(
                                ["item_master", ["item_name", "item_code"]],
                                ["unique_id" => $sub["item_unique_id"], "is_delete" => 0]
                            );
                            $sub_name = ($sub_item->status && !empty($sub_item->data))
                                ? $sub_item->data[0]["item_name"] . " / " . $sub_item->data[0]["item_code"]
                                : $sub["item_unique_id"];
                        
                            $uom = unit_name($sub["uom_unique_id"]);
                        
                            // Action buttons for child items
                            $sub_edit = btn_edit($btn_prefix, $sub["item_unique_id"]);
                            $sub_del  = btn_delete($btn_prefix, $sub["item_unique_id"]);
                        
                            // Add s.no dynamically: parent_sno.child_sno
                            $sublist[] = [
                                "sno"     => $row["s_no"] . "." . ($idx + 1),
                                "item"    => $sub_name,
                                "qty"     => $sub["qty"],
                                "uom"     => $uom[0]['unit_name'],
                                "remarks" => $sub["remarks"]   
                            ];
                            
                            print_r("sublist: " . print_r($sublist, true) . "\n", 3, "sublist1.log");
                        }

                    } else {
                        // No sublist found, mark it as no-sublist
                        $display_class = "no-sublist";
                    }
                }
            }

            
            // Wrap display_code with a span + dynamic class
            $display_code = "<span class='{$display_class}'>" . $display_code . "</span>";

            // Update UOM name
            $uom_data = unit_name($row["uom"]);
            $row["uom"] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";
            $row['quantity'] = round($row['quantity']);

            // Action buttons
            $edit = btn_edit($btn_prefix, $row["unique_id"]);
            $del  = btn_delete($btn_prefix, $row["unique_id"]);

            // Final row
            $data[] = [
                "s_no"          => $row["s_no"],
                "item_code"     => $display_code,
                "item_desc"     => $row["item_description"],
                "quantity"      => $row["quantity"],
                "uom"           => $row["uom"],
                "remarks"       => $row["item_remarks"],
                "req_date"      => $row["required_delivery_date"],
                "actions"       => $edit . $del,
                "sublist"       => $sublist // <-- Important for child rows
            ];
        }

        echo json_encode([
            "draw"            => intval($_POST["draw"] ?? 1),
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data
        ]);
    } else {
        echo json_encode([
            "draw"            => intval($_POST["draw"] ?? 1),
            "recordsTotal"    => 0,
            "recordsFiltered" => 0,
            "data"            => [],
            "error"           => $result->error
        ]);
    }
    break;

case "item_refresh":
    $main_unique_id = $_POST['main_unique_id'] ?? '';
    $selected_id    = $_POST['selected_id'] ?? '';

    if (!$main_unique_id) {
        echo json_encode([
            "status" => false,
            "error"  => "Missing main_unique_id"
        ]);
        break;
    }

    // Step 1: Get the normal filtered list (excluding duplicates)
    $available_items = dynamic_list_exclude("item_master", "unique_id", null, $main_unique_id);

    // Step 2: Prepare a fresh array where selected item (if exists) will go first
    $final_items = [];

    // Step 3: Fetch selected item details and push to the top if valid
    if (!empty($selected_id)) {
        $exists = array_search($selected_id, array_column($available_items, 'unique_id'));

        // If selected item not already in the list, fetch it manually
        if ($exists === false) {
            $table_details = [
                "item_master",
                [
                    "unique_id",
                    "CONCAT(item_name, ' / ', item_code) AS text",
                    "item_name",
                    "item_code"
                ]
            ];
            $where = "unique_id = '$selected_id' AND is_delete = 0";
            $selected_item = $pdo->select($table_details, $where);

            if ($selected_item->status && !empty($selected_item->data)) {
                $final_items[] = $selected_item->data[0];
            }
        } else {
            // Already in list — move it to the top
            $final_items[] = $available_items[$exists];
            unset($available_items[$exists]); // remove it from main array to avoid duplication
        }
    }

    // Step 4: Merge the selected item (if any) on top of the remaining list
    $final_items = array_merge($final_items, array_values($available_items));

    // Step 5: Generate <option> list, marking the selected one
    $options_html = select_option($final_items, "Select the Item/Code", $selected_id);

    echo json_encode([
        "status" => true,
        "options_html" => $options_html
    ]);
    break;



    case "pr_sub_edit":
        $unique_id = $_POST["unique_id"];
        $columns = [
            "unique_id",
            "main_unique_id",
            "item_code",
            "item_description",
            "quantity",
            "uom",
            "item_remarks",
            "required_delivery_date",
            "from_sales_order"
        ];
        

        $table_details = ["purchase_requisition_items", $columns];
        $where = ["unique_id" => $unique_id, "is_delete" => 0];
        $result = $pdo->select($table_details, $where);

        $item_code_text = "-";
        if (!empty($result->data[0]["item_code"])) {
            $item_info = item_name_list($result->data[0]["item_code"]);
            if (!empty($item_info)) {
                $item_code_text = $item_info[0]["item_name"] . " / " . $item_info[0]["item_code"];
            }
        }

        $data = $result->data[0];
        $data["item_code_text"] = $item_code_text;

        echo json_encode([
            "status" => $result->status,
            "data"   => $data,
            "msg"    => $result->status ? "edit_data" : "error",
            "error"  => $result->error
        ]);
    break;

    case "pr_sub_delete":
        $unique_id = $_POST["unique_id"];

        $columns = [
            "is_delete" => 1
        ];
        $where = [
            "unique_id" => $unique_id
        ];

        $action_obj = $pdo->update("purchase_requisition_items", $columns, $where);

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "delete_success" : "delete_error",
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
        break;

    case "get_item_details_by_code":
        $item_code = $_POST["item_code"];

        $table = "item_master";
        $columns = ["description", "uom_unique_id"];
        $where = ["unique_id" => $item_code, "is_delete" => 0];

        $result = $pdo->select([$table, $columns], $where);

        if ($result->status && !empty($result->data)) {
            $description = $result->data[0]['description'];
            $uom_id = $result->data[0]['uom_unique_id'];

            // Convert uom_unique_id to readable UOM name
            $uom_data = unit_name($uom_id);
            $uom_name = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";

            echo json_encode([
                "status" => true,
                "data" => [
                    "description" => $description,
                    "uom" => $uom_name,      // For display
                    "uom_id" => $uom_id      // For saving
                ]
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "error" => "Item not found"
            ]);
        }
        break;
    case "get_items_by_group":
        $group_id = $_POST["group_id"];

        $excluded_ids = ["683568ca2fe8263239", "683588840086c13657"]; // Service, Capital
        $table = "item_master";
        $columns = ["unique_id", "item_name", "item_code"];
        $where = "is_delete = 0";

        if ($group_id === "1") {
            // Regular: fetch all groups that are NOT Service or Capital
            $where .= " AND group_unique_id NOT IN ('" . implode("','", $excluded_ids) . "')";
        } elseif (in_array($group_id, $excluded_ids)) {
            // Specifically fetch items matching the group (Service or Capital)
            $where .= " AND group_unique_id = '$group_id'";
        } else {
            // Unknown or fallback group, return empty
            $where .= " AND 1=0";
        }

        $result = $pdo->select([$table, $columns], $where);
        $options = "<option value=''>Select the Item/Code</option>";

        if ($result->status && !empty($result->data)) {
            foreach ($result->data as $row) {
                $text = $row['item_name'] . " / " . $row['item_code'];
                $options .= "<option value='{$row['unique_id']}'>$text</option>";
            }
        } else {
            $options .= "<option value=''>No items found</option>";
        }

        echo $options;
        // error_log("options: " . print_r($options, true) . "\n", 3, "items_log.txt");
    break;


    // case "get_items_by_sales_order":
    // $sales_order_id = $_POST["sales_order_id"];
    // $type           = (int)$_POST['type'];

    // $sales_order_table       = "sales_order_sublist";
    // $product_table           = "product_master";
    // $product_sublist_table   = ($type == 2) ? "sales_order_sublist" : "obom_list"; // your original choice
    // $item_table              = "item_master";

    // // --- safe log helper ---
    // $LOG_FILE = __DIR__ . "/so_debug.log";
    // $log = function($label, $data = null) use ($LOG_FILE) {
    //     $ts = date('Y-m-d H:i:s');
    //     if ($data !== null) {
    //         if (!is_string($data)) {
    //             $j = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    //             if ($j === false) $j = print_r($data, true);
    //         } else $j = $data;
    //         error_log("[$ts] $label :: $j\n", 3, $LOG_FILE);
    //     } else {
    //         error_log("[$ts] $label\n", 3, $LOG_FILE);
    //     }
    // };

    // // --- fetcher: item_master -> product_master fallback, normalized fields ---
    // $fetchCatalogRecord = function($uniqueId) use ($pdo, $item_table, $product_table, $log) {
    //     // 1) Try item_master
    //     try {
    //         $item_res = $pdo->select([$item_table, [
    //             "item_name","item_code","description","uom_unique_id","category_unique_id"
    //         ]], ["unique_id" => $uniqueId, "is_delete" => 0]);
    //     } catch (Throwable $e) {
    //         $log("ITEM_MASTER ERROR", $e->getMessage());
    //         $item_res = (object)["status"=>false,"data"=>[]];
    //     }

    //     if ($item_res->status && !empty($item_res->data)) {
    //         $r = $item_res->data[0];
    //         return [
    //             "source"              => "item",
    //             "item_unique_id"      => $uniqueId,
    //             "item_code"           => $r["item_code"],
    //             "item_name"           => $r["item_name"],
    //             "description"         => $r["description"],
    //             "uom_id"              => $r["uom_unique_id"],
    //             "category_unique_id"  => $r["category_unique_id"] ?? null,
    //         ];
    //     }

    //     // 2) Fallback: product_master
    //     try {
    //         $prod_res = $pdo->select([$product_table, [
    //             "product_name","product_code","description"
    //         ]], ["unique_id" => $uniqueId, "is_delete" => 0]);
    //     } catch (Throwable $e) {
    //         $log("PRODUCT_MASTER ERROR", $e->getMessage());
    //         $prod_res = (object)["status"=>false,"data"=>[]];
    //     }

    //     if ($prod_res->status && !empty($prod_res->data)) {
    //         $r = $prod_res->data[0];
    //         error_log(print_r($r, true) . "\n", 3, "prod.log");
    //         return [
    //             "source"              => "product",
    //             "item_unique_id"      => $uniqueId,
    //             "item_code"           => $r["product_code"],
    //             "item_name"           => $r["product_name"],
    //             "description"         => $r["description"],
    //             "uom_id"              => null,      // not present in product_master
    //             "category_unique_id"  => null,      // not present in product_master
    //         ];
    //     }

    //     // Not found anywhere
    //     return null;
    // };

    // // --- Step: fetch SO lines ---
    // $columns = ["item_name_id", "quantity"];
    // $where   = ["so_main_unique_id" => $sales_order_id, "is_delete" => 0];

    // $result = $pdo->select([$sales_order_table, $columns], $where);
    // $log("SO_LINES", [
    //     "status"=>$result->status ?? null,
    //     "rows"=> isset($result->data)?count($result->data):null,
    //     "sample"=>$result->data[0] ?? null
    // ]);

    // $items = [];

    // if ($result->status && !empty($result->data)) {
    //     foreach ($result->data as $row) {
    //         $item_name_id = $row["item_name_id"];
    //         $qty          = (int)$row["quantity"];

    //         // Step: get item_unique_id refs from sublist table (your original rule)
    //         $sublist_columns = ["item_unique_id"];
    //         $sublist_where   = ["prod_unique_id" => $item_name_id, "is_delete" => 0];

    //         if ($product_sublist_table === "obom_list") {
    //             $sublist_columns[]           = "type";
    //             $sublist_where["so_unique_id"] = $sales_order_id;
    //         }

    //         $sublist_res = $pdo->select([$product_sublist_table, $sublist_columns], $sublist_where);
            

    //         if (!$sublist_res->status || empty($sublist_res->data)) {
    //             // If no rows in sublist, you may want to treat the SO line as direct item/product:
    //             // try direct fetch on $item_name_id (optional fallback)

    //             $rec = $fetchCatalogRecord($item_name_id);
    //             error_log(print_r($rec, true) . "\n", 3, "rec.log");
    //             if ($rec) {
    //                 // optional skip rule applies only if we have category + obom type
    //                 $items[] = [
    //                     "item_unique_id" => $rec["item_unique_id"],
    //                     "item_code"      => $rec["item_code"],
    //                     "item_name"      => $rec["item_name"],
    //                     "description"    => $rec["description"],
    //                     "uom_id"         => $rec["uom_id"],
    //                     "quantity"       => $qty
    //                 ];
    //             }
    //             continue;
    //         }

    //         foreach ($sublist_res->data as $subrow) {
    //             $item_unique_id = $subrow["item_unique_id"];
    //             $obom_type      = $subrow["type"] ?? null;

    //             // Fetch from item_master; fallback to product_master if missing
    //             $rec = $fetchCatalogRecord($item_unique_id);
    //             if (!$rec) {
    //                 $log("CATALOG MISS", ["item_unique_id"=>$item_unique_id]);
    //                 continue;
    //             }

    //             // Keep your skip rule (only when category is known)
    //             if ($product_sublist_table === "obom_list"
    //                 && $obom_type == 1
    //                 && !empty($rec["category_unique_id"])
    //                 && $rec["category_unique_id"] === "689c7932650be49774") {
    //                 $log("SKIP BY CATEGORY", ["item_unique_id"=>$item_unique_id,"cat"=>$rec["category_unique_id"]]);
    //                 continue;
    //             }

    //             $items[] = [
    //                 "item_unique_id" => $rec["item_unique_id"],
    //                 "item_code"      => $rec["item_code"],
    //                 "item_name"      => $rec["item_name"],
    //                 "description"    => $rec["description"],
    //                 "uom_id"         => $rec["uom_id"],     // null when source=product
    //                 "quantity"       => $qty
    //             ];
    //         }
    //     }
    // }

    // echo json_encode([
    //     "status" => count($items) > 0,
    //     "data"   => $items
    // ]);
    // break;

case "get_items_by_sales_order":
    $sales_order_id = $_POST["sales_order_id"];
    $type           = (int)$_POST['type'];

    $sales_order_table = "sales_order_sublist";
    $product_table     = "product_master";
    $item_table        = "item_master";

    // --- safe log helper ---
    $LOG_FILE = __DIR__ . "/so_debug.log";
    $log = function($label, $data = null) use ($LOG_FILE) {
        $ts = date('Y-m-d H:i:s');
        if ($data !== null) {
            if (!is_string($data)) {
                $j = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
                if ($j === false) $j = print_r($data, true);
            } else $j = $data;
            error_log("[$ts] $label :: $j\n", 3, $LOG_FILE);
        } else {
            error_log("[$ts] $label\n", 3, $LOG_FILE);
        }
    };

    // --- fetcher: item_master -> product_master fallback ---
    $fetchCatalogRecord = function($uniqueId) use ($pdo, $item_table, $product_table, $log) {
        // Try item_master
        try {
            $item_res = $pdo->select([$item_table, [
                "item_name","item_code","description","uom_unique_id","category_unique_id"
            ]], ["unique_id" => $uniqueId, "is_delete" => 0]);
        } catch (Throwable $e) {
            $log("ITEM_MASTER ERROR", $e->getMessage());
            $item_res = (object)["status"=>false,"data"=>[]];
        }

        if ($item_res->status && !empty($item_res->data)) {
            $r = $item_res->data[0];
            return [
                "item_unique_id"      => $uniqueId,
                "item_code"           => $r["item_code"],
                "item_name"           => $r["item_name"],
                "description"         => $r["description"],
                "uom_id"              => $r["uom_unique_id"],
                "category_unique_id"  => $r["category_unique_id"] ?? null,
            ];
        }

        // Fallback: product_master
        try {
            $prod_res = $pdo->select([$product_table, [
                "product_name","product_code","description"
            ]], ["unique_id" => $uniqueId, "is_delete" => 0]);
        } catch (Throwable $e) {
            $log("PRODUCT_MASTER ERROR", $e->getMessage());
            $prod_res = (object)["status"=>false,"data"=>[]];
        }

        if ($prod_res->status && !empty($prod_res->data)) {
            $r = $prod_res->data[0];
            return [
                "item_unique_id"      => $uniqueId,
                "item_code"           => $r["product_code"],
                "item_name"           => $r["product_name"],
                "description"         => $r["description"],
                "uom_id"              => null,
                "category_unique_id"  => null,
            ];
        }

        // Not found anywhere
        return null;
    };

    // --- Step: fetch SO lines ---
    $columns = ["item_name_id", "quantity"];
    $where   = ["so_main_unique_id" => $sales_order_id, "is_delete" => 0];

    $result = $pdo->select([$sales_order_table, $columns], $where);
    $log("SO_LINES", [
        "status"=>$result->status ?? null,
        "rows"=> isset($result->data)?count($result->data):null,
        "sample"=>$result->data[0] ?? null
    ]);

    $items = [];

    if ($result->status && !empty($result->data)) {
        foreach ($result->data as $row) {
            $item_name_id = $row["item_name_id"];
            $qty          = (int)$row["quantity"];

            // fetch parent only
            $rec = $fetchCatalogRecord($item_name_id);
            if ($rec) {
                $items[] = [
                    "item_unique_id" => $rec["item_unique_id"],
                    "item_code"      => $rec["item_code"],
                    "item_name"      => $rec["item_name"],
                    "description"    => $rec["description"],
                    "uom_id"         => $rec["uom_id"],
                    "quantity"       => $qty,
                    "sublist"        => []   // always empty
                ];
            }
        }
    }

    echo json_encode([
        "status" => count($items) > 0,
        "data"   => $items
    ]);
    break;


    case 'documents_datatable':
        // Function Name button prefix
        $btn_edit_delete = "documents";

        // Fetch Data
        $upload_unique_id = $_POST['upload_unique_id']; 
        
        // DataTable Inputs
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // SQL Column Selections
        $columns = [
            "@a:=@a+1 AS s_no",
            "type",
            "file_attach",
            "unique_id"
        ];

        $table_details = [
            "$documents_upload, (SELECT @a:=$start) AS a",
            $columns
        ];

        $where = [
            "pr_unique_id" => $upload_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        // error_log("documents datatable query: " . $result->sql . "\n", 3, "debug.txt");

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                // Get document type name from doc_type_options(type)
                $type_data = doc_type_options($value['type']);
                $type_name = '';
                if (is_array($type_data) && isset($type_data[0]['name'])) {
                    $type_name = $type_data[0]['name'];
                }
                $value['type'] = $type_name;

                if (is_null($value['file_attach']) || $value['file_attach'] == '') {
                    $value['file_attach'] = "<td style='text-align:center'><span class='font-weight-bold'>No Image Uploaded</span></td>";
                } else {
                    $image_files = explode(',', $value['file_attach']);
                    $image_buttons = "";
                    foreach ($image_files as $image_file) {
                        $image_path = "../blue_planet_erp/uploads/purchase_requisition_test/" . trim($image_file);
                        $view_button = "<button type='button' onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'> <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                        </button>";
                        $image_buttons .= $view_button;
                    }
                    $value['file_attach'] = "<td style='text-align:center'>" . $image_buttons . "</td>";
                }

                $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_delete;

                $data[] = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
    break;
    
     case 'documents_add_update':

        $upload_unique_id = $_POST["upload_unique_id"] ?? null;
        $type             = $_POST["type"] ?? null;
        $unique_id        = $_POST["unique_id"] ?? null;
        
        // Log incoming POST data
        // error_log("POST: " . print_r($_POST, true) . "\n", 3, "doc_logs.txt");
        
        // Validate required fields
        if (!$upload_unique_id || !$type) {
            echo json_encode([
                "status" => false,
                "error"  => "Missing required fields: 'upload_unique_id' or 'type'.",
                "msg"    => "missing_fields"
            ]);
            exit;
        }
        
        // Check if no new file is uploaded AND no existing file is provided
        if (empty($_FILES["test_file"]["name"][0])) {
            echo json_encode([
                "status" => false,
                "error"  => "No file selected.",
                "msg"    => "no_file_selected"
            ]);
            exit;
        }


        $doc_up_filenames = [];     
        $allowed_exts = [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
            
            // PDF
            'pdf',
        
            // Word documents
            'doc', 'docx',
        
            // Text files
            'txt',
        
            // Excel files
            'xls', 'xlsx',
        
            // CSV files
            'csv'
        ];

        if (!empty($_FILES["test_file"]["name"])) {                              
            $target_dir = "../../uploads/purchase_requisition_test/";
            $folder_path = "purchase_requisition_test/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES["test_file"]["name"] as $key => $name) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                finfo_close($finfo);

               $allowed_mime_types = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv'
                ];
                
                if (!in_array($file_extension, $allowed_exts) || !in_array($mime_type, $allowed_mime_types)) {
                    echo json_encode([
                        "status" => false,
                        "error"  => "Invalid file format. Only images, PDF, Word, Excel, CSV, and text files are allowed.",
                        "msg"    => "invalid_file_format"
                    ]);
                    exit;
                }


                $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_extension;
                $target_file = $target_dir . $unique_filename;

                if (move_uploaded_file($_FILES["test_file"]["tmp_name"][$key], $target_file)) {
                    $doc_up_filenames[] = $unique_filename;
                }
            }

            $doc_up_filename = implode(',', $doc_up_filenames);
        } else {
            $doc_up_filename = $_POST['existing_file_attach'];
        }

        $columns = [
            "pr_unique_id"              => $upload_unique_id,
            "type"                       => $type,
            "file_attach"                => $doc_up_filename,
        ];

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Direct insert or update — no duplicate check
        if ($unique_id) {
            $update_where = [ "unique_id" => $unique_id ];
            $action_obj = $pdo->update($documents_upload, $columns, $update_where);
            $msg = $action_obj->status ? "update" : "error";
        } else {
            $action_obj = $pdo->insert($documents_upload, $columns);
            $msg = $action_obj->status ? "add" : "error";
        }
        
        // error_log("action_obj: " . print_r($action_obj, true) . "\n", 3, "doc_logs.txt");
        
        $data_array = [
            "insert_id" => $action_obj->data,     // if it's lastInsertId()
            "upload"    => $upload_unique_id
        ];
        
        // error_log("json_response: " . print_r([
        //     "status" => $action_obj->status,
        //     "data"   => $data_array,
        //     "error"  => $action_obj->error,
        //     "msg"    => $msg,
        //     "sql"    => $action_obj->sql
        // ], true) . "\n", 3, "doc_logs.txt");

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $data_array,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql,
        ]);

    break;
    
     case 'documents_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($documents_upload,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        
        break;
        
    case 'foreclose':
    $unique_id = $_POST['unique_id'] ?? '';
    $response = [];
    $session_user_id = $_SESSION['sess_user_id'] ?? 'SYSTEM';

    if (!empty($unique_id)) {
        $update = $pdo->update(
            "purchase_requisition",
            ["foreclose_status" => 1, "foreclose_date" => date('Y-m-d H:i:s'), "foreclosed_by" => $session_user_id],
            ["unique_id" => $unique_id]
        );

        if ($update->status) {
            $response = ["status" => "success", "message" => "PR foreclosed successfully"];
        } else {
            $response = ["status" => "error", "message" => "Database update failed"];
        }
    } else {
        $response = ["status" => "error", "message" => "Invalid PR ID"];
    }

    echo json_encode($response);
    break;

    

        break;
}
