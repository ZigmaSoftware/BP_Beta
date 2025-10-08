<?php 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = 'grn'; 
$sub_table          = 'grn_sublist'; 

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

$supplier_invoice_no    = "";
$mode_type              = "";
$payment_terms          = "";
$invoice_date           = "";
$inward_type            = "";
$pa_status              = "";
$dc_no                  = "";
$po_number              = "";
$branch                 = "";
$supplier_name          = "";
$po_status              = "";
$description            = "";

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
    
    case "createupdate":
        $supplier_invoice_no    = $_POST["supplier_invoice_no"];
        $invoice_date           = $_POST["invoice_date"];
        $inward_type            = $_POST["inward_type"];
        $dc_no                  = $_POST["dc_no"];
        $po_number              = $_POST["po_number"];
        $eway_bill_no           = $_POST["eway_bill_no"];
        $eway_bill_date         = $_POST["eway_bill_date"];
        $paf                    = $_POST["paf"];
        $freight                = $_POST["freight"];
        $other                  = $_POST["other"];
        $round                  = $_POST["round"];
        $gst_paf                = $_POST["gst_paf"];
        $gst_freight            = $_POST["gst_freight"];
        $gst_other              = $_POST["gst_other"];
        $supplier_name          = $_POST["supplier_id"];        
        $po_status              = $_POST["po_status"];
        $company_id             = $_POST["company_id"];
        $project_id             = $_POST["project_id"];
        $purchase_order_no      = $_POST["purchase_order_no"];
		$description            = isset($_POST["description"]) && trim($_POST["description"]) !== '' ? $_POST["description"] : null;
        $unique_id = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();
        $screen_unique_id = !empty($_POST["screen_unique_id"]) ? $_POST["screen_unique_id"] : unique_id();

        $labelData = [];
        $labelData = fetch_grn_number($table);
        error_log("labelData: " . print_r($labelData, true) . "\n", 3, "cu_error_log.txt");

        $company_data                   = company_name($company_id);
        $company_name                   = $company_data[0]['company_name'];

        error_log("company_label: " . $company_name . "\n", 3, "cu_error_log.txt");


        if (!empty($_POST["unique_id"])) {
            // If unique_id exists, fetch the existing grn_number for this unique_id
            $existing_grn = $pdo->select([$table, ["grn_number"]], ['unique_id' => $_POST["unique_id"], 'is_delete' => 0]);
            if ($existing_grn->status && !empty($existing_grn->data[0]['grn_number'])) {
            $grn_number = $existing_grn->data[0]['grn_number'];
            } else {
            // Fallback: generate a new GRN if not found (should not happen in normal update)
            $grn_number = generateGRN($company_name, $labelData);
            }
        } else {
            // No unique_id, so generate a new GRN number
            $grn_number = generateGRN($company_name, $labelData);
        }
        error_log("grn_no: " . $grn_number . "\n", 3, "cu_error_log.txt");
       
        $columns = [
            "supplier_invoice_no"   => $supplier_invoice_no,
            // "mode_type"             => $mode_type,
            "screen_unique_id" => $screen_unique_id,
            "eway_bill_no"          => $eway_bill_no,
            "invoice_date"          => $invoice_date,
            "inward_type"           => $inward_type,
            "eway_bill_date"        => $eway_bill_date,
            "dc_no"                 => $dc_no,
            "po_number"             => $purchase_order_no,
            "grn_number"            => $grn_number,
            "paf"                   => $paf,
            "freight"               => $freight,
            "other"                 => $other,
            "round"                 => $round,
            "gst_paf"               => $gst_paf,
            "gst_freight"           => $gst_freight,
            "gst_other"             => $gst_other,
            "supplier_name"         => $supplier_name,
            "po_status"             => $po_status,
            "check_status"          => 0, // Default value for check status
            "checked_by"            => null,
            "check_remarks"         => null,
            "approve_status"        => 0, // Default value for approve status
            "approved_by"           => null,
            "status_remark"         => null,
            "description"           => $description,
            "company_id"            => $company_id,
            "project_id"            => $project_id,  
            "created_user_id"       => $user_id,
            "created"               => $date,
            "unique_id"             => $unique_id
        ];
    
        // Check if it exists
        $check_query = [$table, ["COUNT(unique_id) AS count"]];
        $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';
    
        $action_obj = $pdo->select($check_query, $check_where);
    
        if ($action_obj->status && $action_obj->data[0]["count"]) {
            // Update mode — do NOT change pr_number
            unset($columns["unique_id"], $columns["created_user_id"], $columns["created"], $columns["customer_id"], $columns["company_id"], $columns["entry_date"]);
            $columns["updated_user_id"] = $user_id;
            $columns["updated"]         = $date;
    
            $update_where = ["unique_id" => $unique_id];
            $action_obj   = $pdo->update($table, $columns, $update_where);
            $msg          = "update";
        } else {
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

case "update_qty":
    $screen_unique_id = $_POST['screen_unique_id'];
    $is_update = $_POST['is_update'];
    
    // Check if the screen_unique_id is provided
    if (!empty($screen_unique_id)) {

        $po_unique_id_data = fetch_po_unique_id($sub_table, $screen_unique_id);
        $po_unique_id = is_array($po_unique_id_data) ? $po_unique_id_data[0]["po_unique_id"] ?? null : $po_unique_id_data;

        if (empty($po_unique_id)) {
            echo json_encode([
                "status" => false,
                "msg" => "No PO Unique ID found for this screen ID"
            ]);
        }

        if ($is_update) {
            // Step 1: Prepare JOIN query to get previous total_received_qty (excluding this screen)
            $columns = [
                "gs.unique_id",
                "gs.update_qty",
                "gs.item_code",
                "gs.po_unique_id",
                "IFNULL(grn_sub.total_received_qty, 0) AS prev_received_qty"
            ];
            
            $select_query = [
                "$sub_table gs 
                LEFT JOIN ( 
                    SELECT 
                        item_code, 
                        po_unique_id, 
                        SUM(now_received_qty) AS total_received_qty
                    FROM $sub_table 
                    WHERE po_unique_id = '$po_unique_id' AND screen_unique_id != '$screen_unique_id' AND is_delete = 0
                    GROUP BY item_code, po_unique_id
                ) AS grn_sub 
                ON gs.item_code = grn_sub.item_code 
                AND gs.po_unique_id = grn_sub.po_unique_id",
                $columns
            ];
            
            $select_where = "gs.screen_unique_id = '$screen_unique_id' AND gs.is_delete = 0";
            $action_obj = $pdo->select($select_query, $select_where);

            if ($action_obj->status && count($action_obj->data) > 0) {
                foreach ($action_obj->data as $row) {
                    $new_now_received_qty = $row['prev_received_qty'] + $row['update_qty'];

                    $update_columns = [
                        "now_received_qty" => $new_now_received_qty,
                        "updated_user_id" => $user_id,
                        "updated" => $date
                    ];

                    $update_where = ["unique_id" => $row['unique_id']];
                    $pdo->update($sub_table, $update_columns, $update_where);
                }
                $msg = "Quantities updated successfully!";
                $status = true;
            } else {
                $msg = "No matching records found to update.";
                $status = false;
            }
        } else {
            // Conditionally set the select query columns based on whether it's an update or not
        $select_query = [
                $sub_table,
                ["unique_id", "now_received_qty", "update_qty"]  // Fetch all relevant columns when in create mode
            ];
        $select_where = "screen_unique_id = '$screen_unique_id' AND is_delete = 0";
        
        // Fetch the records
        $action_obj = $pdo->select($select_query, $select_where);
        
        if ($action_obj->status && count($action_obj->data) > 0) {
            // Loop through the results
            foreach ($action_obj->data as $row) {
                $current_now_received_qty = $row['now_received_qty'];
                $current_update_qty = $row['update_qty'];

                // Add the existing now_received_qty with the update_qty
                $new_now_received_qty = $current_now_received_qty + $current_update_qty;

                // Prepare the update query to update the now_received_qty
                $update_columns = [
                    "now_received_qty" => $new_now_received_qty,
                    "updated_user_id" => $user_id,
                    "updated" => $date
                ];

                // Update the record with the new now_received_qty value
                $update_where = ["unique_id" => $row['unique_id']];
                $pdo->update($sub_table, $update_columns, $update_where);
            }
            $msg = "Quantities updated successfully!";
            $status = true;
        } else {
            $msg = "No matching records found to update.";
            $status = false;
        }
        }
        // SQL to fetch existing records with the matching screen_unique_id
        
    } else {
        $msg = "Screen Unique ID is required.";
        $status = false;
    }

    // Return the response
    echo json_encode([
        "status" => $status,
        "msg" => $msg
    ]);
break;


    case 'datatable':

        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        
        // Date Filters
        $from = isset($_POST['from']) ? $_POST['from'] : '';
        $to   = isset($_POST['to']) ? $_POST['to'] : '';

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 AS s_no", 
            "company_id",
            "project_id",
            "supplier_name",
            "invoice_date",
            "po_number",
            "grn_number",
            "supplier_invoice_no",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' AND invoice_date >= '$from' AND invoice_date <= '$to'";
         
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
        $approve_status_options = [
            1 => [
                "unique_id" => "1",
                "value"     => "Not Completed"
            ],
            2 => [
                "unique_id" => "2",
                "value"     => "Completed"
            ]
        ];
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        error_log("sql: " . print_r($result, true) . "\n", 3, "datatable_init.txt");

        error_log("result: " . $result->sql . "\n", 3, "sql_error_log.txt");
        
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $grn_no = $value['grn_number'];

                $grn_check_status_arr = fetch_grn_status($grn_no);
                $grn_check_status = isset($grn_check_status_arr[0]['check_status']) ? $grn_check_status_arr[0]['check_status'] : '';
                $grn_approve_status = isset($grn_check_status_arr[0]['approve_status']) ? $grn_check_status_arr[0]['approve_status'] : '';

                error_log("grn_check_status: " . print_r($grn_check_status, true) . "\n", 3, "grn_check_status_log.txt");
                
                $company_data = company_name($value['company_id']);
                $value['company_id'] = $company_data[0]['company_name'];

                $project_options = get_project_name($value['project_id']);
                $value['project_id'] = $project_options[0]['project_name'];

                $project_code = $project_options[0]['project_code'] ?? '';
                error_log("project_code: " . $project_code . "\n", 3, "project_code_log.txt");

                $purchase_order_no = get_po_number($value['po_number']);
                $value['po_number'] = $purchase_order_no[0]['purchase_order_no'];

                $supplier_names = supplier($value['supplier_name']);
                $value['supplier_name'] = $supplier_names[0]['supplier_name'];
                // Button and status logic
                $status = '';
                $btns = '';
                $is_admin = isset($_SESSION['sess_user_type']) && $_SESSION['sess_user_type'] == $admin_user_type;

                if ($grn_check_status == 1 && $grn_approve_status != 1) {
                    $status = '<span class="text-success fw-bold">Checked</span>';
                    // No update/delete buttons if checked
                    $btns = '';
                } elseif ($grn_approve_status == 1) {
                    $status = '<span class="text-success fw-bold">Approved</span>';
                    // Only show delete button if admin
                    $btns = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                } else {
                    // Only show buttons if not checked or approved
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $btns = $btn_update . $btn_delete;
                    $status = '<span class="text-warning fw-bold">Pending</span>';
                }

                $btn_view = btn_info($folder_name, $value['unique_id']);
                $btns = $btn_view . $btns;

                // Prepare row as indexed array and append status and btns at the end
                $row = array_values($value);

                // Insert project_code after project_name (which is at index 2)
                array_splice($row, 3, 0, $project_code);

                $row[9] = $status;
                $row[10] = $btns;
                $data[] = $row;
            }

            error_log("data: " . print_r($data, true) . "\n", 3, "data_log.txt");
            
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
    
    
    case 'delete':
        
        $unique_id      = $_POST['unique_id'];

        $columns        = [
            "is_delete"   => 1
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table,$columns,$update_where);

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

case 'info':
    $unique_id = $_POST['unique_id'] ?? '';

    if (empty($unique_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "",
            "error" => "Missing unique_id"
        ]);
        break;
    }

    $screen_unique_id = fetch_grn_sc_unique_id($unique_id);
    $po_unique_id = fetch_po_unique_id($sub_table, $screen_unique_id);
    $po_unique_id = is_array($po_unique_id) ? $po_unique_id[0]["po_unique_id"] : $po_unique_id;

    $grn_unique_id = $unique_id;

    if (!empty($grn_unique_id)) {
        $grn_data = fetch_grn_data($grn_unique_id);
        $freight_value         = $grn_data[0]['freight'] ?? 0;
        $freight_tax           = $grn_data[0]['gst_freight'] ?? 0;
        $other_charges         = $grn_data[0]['other'] ?? 0;
        $other_tax             = $grn_data[0]['gst_other'] ?? 0;
        $packing_forwarding    = $grn_data[0]['paf'] ?? 0;
        $packing_forwarding_tax= $grn_data[0]['gst_paf'] ?? 0;
        $round_off             = $grn_data[0]['round'] ?? 0;
    }

    $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
    $po_sc_unique_id = is_array($po_sc_unique_id) ? $po_sc_unique_id[0]["screen_unique_id"] : $po_sc_unique_id;

    $td_data = fetch_tax_discount($po_sc_unique_id);

    $tax = tax($td_data['tax'])[0]['tax_value'];
    $tax_name = tax($td_data['tax'])[0]['tax_name'];
    $discount = $td_data['discount'];
    $discount_type = $td_data['discount_type'];

    $total_amount = 0;
    $taxed_val = 0;

    $pdo->query("SET @a := 0;");

    $columns = [
        "@a := @a + 1 AS s_no",
        "gs.item_code",
        "gs.order_qty",
        "gs.uom",
        "IF('$po_unique_id' = 0, 0, COALESCE(grn_sub.total_received_qty, 0)) AS now_received_qty",
        "gs.update_qty",
        "poi_items.rate",
        "'$tax_name' AS tax_name",
        "$discount_type AS discount_type",
        "$discount AS discount",
        "ROUND(((gs.update_qty * poi_items.rate) - ((gs.update_qty * poi_items.rate * $discount) / 100)) + (((gs.update_qty * poi_items.rate - (gs.update_qty * poi_items.rate * $discount / 100)) * $tax) / 100), 2) AS amount",
        "gs.unique_id"
    ];

    $table_details = [
        "$sub_table gs 
            LEFT JOIN ( 
                SELECT 
                    gs2.item_code, 
                    gs2.po_unique_id, 
                    SUM(gs2.update_qty) AS total_received_qty
                FROM grn_sublist as gs2
                LEFT JOIN grn as g ON g.screen_unique_id = gs2.screen_unique_id
                WHERE gs2.po_unique_id = '$po_unique_id' 
                AND gs2.screen_unique_id = '$screen_unique_id' 
                AND gs2.is_delete = 0 
                AND g.is_delete = 0
                GROUP BY gs2.item_code, gs2.po_unique_id
            ) AS grn_sub 
            ON gs.item_code = grn_sub.item_code 
            AND gs.po_unique_id = grn_sub.po_unique_id
        LEFT JOIN purchase_order poi 
            ON gs.po_unique_id = poi.unique_id
        LEFT JOIN purchase_order_items poi_items 
            ON poi_items.screen_unique_id = poi.screen_unique_id 
            AND poi_items.item_code = gs.item_code
        WHERE gs.screen_unique_id = '$screen_unique_id' 
        AND gs.is_delete = 0",
        $columns
    ];

    $result = $pdo->select($table_details);
    error_log("SQL: " . $result->sql . "\n", 3, "grn_info_sql_log.txt");

    $data = [];

    if ($result->status) {
        foreach ($result->data as $row) {
            $qty = floatval($row['update_qty']);
            $rate = floatval($row['rate']);
            $discount_val = floatval($row['discount']);
            $discount_type_val = $row['discount_type'];

            $base = $qty * $rate;
            if ($discount_type_val == 1 || $discount_type_val === 'Percentage') {
                $discountAmt = ($base * $discount_val) / 100;
            } elseif ($discount_type_val == 2 || $discount_type_val === 'Amount') {
                $discountAmt = $discount_val;
            } else {
                $discountAmt = 0;
            }

            $afterDiscount = $base - $discountAmt;
            $taxed = ($tax * $afterDiscount) / 100;
            $finalAmount = max(0, $afterDiscount + $taxed);
            $row['amount'] = round($finalAmount, 2);

            $total_amount += $row['amount'];
            $taxed_val += $taxed;

            $item_data = item_name_list($row["item_code"]);
            $row["item_code"] = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];

            $uom_data = unit_name($row["uom"]);
            $row["uom"] = $uom_data[0]["unit_name"] ?? $row["uom"];

            switch ($discount_type_val) {
                case 1:
                case 'Percentage':
                    $discount_type_display = 'Percentage';
                    break;
            
                case 2:
                case 'Amount':
                    $discount_type_display = 'Amount';
                    break;
            
                default:
                    $discount_type_display = 'No Type';
            }
            $row['discount_type_display'] = $discount_type_display;
            $row['discount_type'] = $discount_type_display;

            $data[] = $row;
            error_log("Row: " . print_r($row, true) . "\n", 3, "grn_info_row_log.txt");
        }
    }

    $iframe_src = "/blue_planet_beta/blue_planet_beta/index.php?file=grn_new/grn_sublist_iframe&unique_id=" . urlencode($unique_id);

    error_log("iframe_src: " . $iframe_src . "\n", 3, "grn_info_iframe_log.txt");
    error_log("response: " . print_r($data, true) . "\n", 3, "grn_info_data_log.txt");

    if (!$result->status) {
        error_log("Error in info case: " . $result->error . "\n", 3, "grn_info_error_log.txt");
    }
    $response = [
        "status" => $result->status,
        "msg" => $result->status ? "Iframe loaded successfully" : "Failed to load info",
        "iframe_src" => $iframe_src,
        "error" => $result->status ? "" : $result->error,
        "data" => $data,
        "total" => $total_amount
    ];
    error_log("GRN info response: " . json_encode($response) . "\n", 3, "grn_info_response_log.txt");
    echo json_encode($response);
break;

    
    case 'sub_group_name':

        $group_id = $_POST['group_id'];
        $type = $_POST['type'];
        $sub_group_name_options = "";
        $msg = "";
        if($type == 1){
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select";
        } else if($type == 2){
            $sub_group_name_options  = category_name("",$group_id);
            $msg = "Select";
        } else if($type == 3){
            $sub_group_name_options  = category_item("",$group_id);
            $msg = "Select";
        } else {
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select";
        }
        $sub_group_name_options  = select_option($sub_group_name_options,$msg);
        echo $sub_group_name_options;
        
        break;
        
        
    case "grn_sub_add_update":

        $now_received_qty = 0;
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id      = $_POST["sublist_unique_id"];
        
        $item_code      = $_POST["item_code"];
        $order_qty              = $_POST["order_qty"];
        $uom                    = $_POST["uom"];
        $now_received_qty       = $_POST["tot_qty"];
        $update_qty             = $_POST["update_qty"];
       
        $columns = [
            "grn_main_unique_id"    => $unique_id, // Use actual form's unique_id if needed
            "screen_unique_id"      => $screen_unique_id,
            "item_code"             => $item_code,
            "order_qty"             => $order_qty,
            "uom"                   => $uom,
            "now_received_qty"      => $now_received_qty,
            "update_qty"            => $update_qty ? $update_qty : 0,
        ];

            // If po_unique_id is set, add it to the columns
        if (isset($_POST["po_unique_id"])) {
            $po_unique_id = $_POST["po_unique_id"];
            $columns["po_unique_id"] = $po_unique_id;
        }
        
        if (!empty($sublist_unique_id)) {
            // Update existing sublist row
            $columns["updated"] = $date;
            $columns["updated_user_id"] = $user_id;
            
            $where = ["unique_id" => $sublist_unique_id];
            
            $action_obj = $pdo->update($sub_table, $columns, $where);
            $msg = "update";
        } else {
            // Insert new sublist row
            $columns["unique_id"] = unique_id();
            $columns["created"]   = $date;
            $columns["created_user_id"] = $user_id;
        
            $action_obj = $pdo->insert($sub_table, $columns);
            $msg = "add";
        }
        
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error
        ]);

    break;

    case "grn_sublist_datatable":
        $screen_unique_id = $_POST["screen_unique_id"];
        error_log("screen_unique_id: " . $screen_unique_id . "\n", 3, "grn_sublist_datatable_log.txt");
        $btn_prefix = "grn_sub";
        $is_update = isset($_POST['is_update']) ? $_POST['is_update'] : false;

        $grn_unique_id = $_POST['unique_id'];

        $po_unique_id = fetch_po_unique_id($sub_table, $screen_unique_id);
        $po_unique_id = is_array($po_unique_id) ? $po_unique_id[0]["po_unique_id"] : $po_unique_id;
        $unique_id = fetch_unique_id($sub_table, $screen_unique_id);
        $unique_id = is_array($unique_id) ? $unique_id[0]["unique_id"] : $unique_id;

        // If no GRN unique id, set all PO charges fields to 0
        if (empty($grn_unique_id)) {
            // No GRN unique id, get PO charges fields from PO
            $po_data = fetch_po_data($po_unique_id);
            error_log("po_data: " . print_r($po_data, true) . "\n", 3, "po_data_log.txt");
            $freight_value         = isset($po_data[0]['freight_value']) && $po_data[0]['freight_value'] !== '' ? $po_data[0]['freight_value'] : 0;
            $freight_tax           = isset($po_data[0]['freight_tax']) && $po_data[0]['freight_tax'] !== '' ? $po_data[0]['freight_tax'] : 0;
            $other_charges         = isset($po_data[0]['other_charges']) && $po_data[0]['other_charges'] !== '' ? $po_data[0]['other_charges'] : 0;
            $other_tax             = isset($po_data[0]['other_tax']) && $po_data[0]['other_tax'] !== '' ? $po_data[0]['other_tax'] : 0;
            $packing_forwarding    = isset($po_data[0]['packing_forwarding']) && $po_data[0]['packing_forwarding'] !== '' ? $po_data[0]['packing_forwarding'] : 0;
            $packing_forwarding_tax= isset($po_data[0]['packing_forwarding_tax']) && $po_data[0]['packing_forwarding_tax'] !== '' ? $po_data[0]['packing_forwarding_tax'] : 0;
            $round_off             = isset($po_data[0]['round_off']) && $po_data[0]['round_off'] !== '' ? $po_data[0]['round_off'] : 0;
        } 
        else {
            // GRN unique id exists, get charges fields from GRN
            $grn_data = fetch_grn_data($grn_unique_id);
            error_log("grn_data: " . print_r($grn_data, true) . "\n", 3, "grn_data_log.txt");
            $freight_value         = isset($grn_data[0]['freight']) && $grn_data[0]['freight'] !== '' ? $grn_data[0]['freight'] : 0;
            $freight_tax           = isset($grn_data[0]['gst_freight']) && $grn_data[0]['gst_freight'] !== '' ? $grn_data[0]['gst_freight'] : 0;
            $other_charges         = isset($grn_data[0]['other']) && $grn_data[0]['other'] !== '' ? $grn_data[0]['other'] : 0;
            $other_tax             = isset($grn_data[0]['gst_other']) && $grn_data[0]['gst_other'] !== '' ? $grn_data[0]['gst_other'] : 0;
            $packing_forwarding    = isset($grn_data[0]['paf']) && $grn_data[0]['paf'] !== '' ? $grn_data[0]['paf'] : 0;
            $packing_forwarding_tax= isset($grn_data[0]['gst_paf']) && $grn_data[0]['gst_paf'] !== '' ? $grn_data[0]['gst_paf'] : 0;
            $round_off             = isset($grn_data[0]['round']) && $grn_data[0]['round'] !== '' ? $grn_data[0]['round'] : 0;
        }

        error_log("po_unique_id: " . $po_unique_id . "\n", 3, "po_unique_id_log.txt");

        $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
        $po_sc_unique_id = is_array($po_sc_unique_id) ? $po_sc_unique_id[0]["screen_unique_id"] : $po_sc_unique_id;

        $td_data = fetch_tax_discount($po_sc_unique_id);

        $tax = $td_data['tax'];
        $tax_name = tax($tax)[0]['tax_name'];
        $tax = tax($tax)[0]['tax_value'];
        $discount = $td_data['discount'];
        $discount_type = $td_data['discount_type'];

        error_log("tax: " . $tax . "\n" . "discount: " . $discount . "\n" . "discount_type: " . $discount_type . "\n", 3, "td_log.txt");

        $total_amount = 0;
        $taxed_val = 0;

        $pdo->query("SET @a := 0;");

        $columns = [
            "@a := @a + 1 AS s_no",
            "gs.item_code",
            "gs.order_qty",
            "gs.uom",
            "IF('$po_unique_id' = 0, 0, COALESCE(grn_sub.total_received_qty, 0)) AS now_received_qty",
            "gs.update_qty",
            "poi_items.rate",
            "'$tax_name' AS tax_name",
            $discount_type . " AS discount_type",
            $discount . " AS discount",
            "ROUND(((gs.update_qty * poi_items.rate) - ((gs.update_qty * poi_items.rate * $discount) / 100)) + (((gs.update_qty * poi_items.rate - (gs.update_qty * poi_items.rate * $discount / 100)) * $tax) / 100), 2) AS amount",
            "gs.unique_id"
        ];

        if ($is_update) {
            $table_details = [
                "$sub_table gs 
                    LEFT JOIN ( 
                        SELECT 
                            gs2.item_code, 
                            gs2.po_unique_id, 
                            SUM(gs2.update_qty) AS total_received_qty
                        FROM grn_sublist as gs2
                        LEFT JOIN grn as g
                            ON g.screen_unique_id = gs2.screen_unique_id
                        WHERE gs2.po_unique_id = '$po_unique_id' AND gs2.screen_unique_id = '$screen_unique_id' AND gs2.is_delete = 0 AND g.is_delete = 0
                        GROUP BY gs2.item_code, gs2.po_unique_id
                    ) AS grn_sub 
                    ON gs.item_code = grn_sub.item_code 
                    AND gs.po_unique_id = grn_sub.po_unique_id
                LEFT JOIN purchase_order poi 
                    ON gs.po_unique_id = poi.unique_id
                LEFT JOIN purchase_order_items poi_items 
                    ON poi_items.screen_unique_id = poi.screen_unique_id 
                    AND poi_items.item_code = gs.item_code
                WHERE gs.screen_unique_id = '$screen_unique_id' 
                AND gs.is_delete = 0",
                $columns
            ];
        } else {
            $table_details = [
                "$sub_table gs 
                    LEFT JOIN ( 
                        SELECT 
                            gs2.item_code, 
                            gs2.po_unique_id, 
                            SUM(gs2.update_qty) AS total_received_qty
                        FROM grn_sublist as gs2
                        LEFT JOIN grn as g
                            ON g.screen_unique_id = gs2.screen_unique_id
                        WHERE gs2.po_unique_id = '$po_unique_id' AND gs2.screen_unique_id = '$screen_unique_id' AND gs2.is_delete = 0 AND g.is_delete = 0
                        GROUP BY gs2.item_code, gs2.po_unique_id
                    ) AS grn_sub 
                    ON gs.item_code = grn_sub.item_code 
                    AND gs.po_unique_id = grn_sub.po_unique_id
                LEFT JOIN purchase_order poi 
                    ON gs.po_unique_id = poi.unique_id
                LEFT JOIN purchase_order_items poi_items 
                    ON poi_items.screen_unique_id = poi.screen_unique_id 
                    AND poi_items.item_code = gs.item_code
                WHERE gs.screen_unique_id = '$screen_unique_id' 
                AND gs.is_delete = 0",
                $columns
            ];
        }

        $result = $pdo->select($table_details);
        error_log("result: " . print_r($result, true) . "\n", 3, "row_log.txt");

        $data = [];
        if ($result->status) {
            foreach ($result->data as $row) {
            // Calculate total_amount using the same logic as recalculateAmount JS function
            $qty = floatval($row['update_qty']);
            $rate = floatval($row['rate']);
            $discount = floatval($row['discount']);
            $discount_type = $row['discount_type'];

            $base = $qty * $rate;
            if ($discount_type == 1) {
                $discountAmt = ($base * $discount) / 100;
            } else if ($discount_type == 2) {
                $discountAmt = $discount;
            } else {
                $discountAmt = 0;
            }
            $afterDiscount = $base - $discountAmt;
            $taxed_val += ($tax * $afterDiscount) / 100;
            $finalAmount = $afterDiscount + $taxed_val;
            if ($finalAmount < 0) {
                $finalAmount = 0;
            }
            $row['amount'] = round($finalAmount, 2);

            $total_amount += $row['amount'];

            error_log("row: " . print_r($row, true) . "\n", 3, "row_logs.txt");

            if (!$is_update) {
                $item_code = $row['item_code'];
                $row['now_received_qty'] = isset($latest_qty_map[$item_code]) ? $latest_qty_map[$item_code] : 0;
            }

            $item_data = item_name_list($row["item_code"]);
            $row["item_code"] = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];

            $uom_data = unit_name($row["uom"]);
            $row["uom"] = !empty($uom_data[0]["unit_name"]) ? $uom_data[0]["unit_name"] : $row["uom"];

            // Map discount_type to display value
            $discount_type_display = '';
            if ($row['discount_type'] == 1 || $row['discount_type'] === 'Percentage') {
                $discount_type_display = 'Percentage';
            } else if ($row['discount_type'] == 2 || $row['discount_type'] === 'Amount') {
                $discount_type_display = 'Amount';
            } else {
                $discount_type_display = 'No Type';
            }
            $row['discount_type_display'] = $discount_type_display;
            $row['discount_type'] = $discount_type_display;

            error_log("discount_type_display: " . $row['discount_type_display'] . "\n", 3, "discount_log.txt");

                $edit = btn_edit($btn_prefix, $row["unique_id"]);
                $del = btn_delete($btn_prefix, $row["unique_id"]);
                $row["unique_id"] = $edit . $del;

                $data[] = array_merge(array_values($row), [
                    'item_code' => $row["item_code"],
                    'order_qty' => $row["order_qty"],
                    'uom' => $row["uom"],
                    'now_received_qty' => $row["now_received_qty"],
                    'discount_type_display' => $row["discount_type_display"],
                    'unique_id' => $row["unique_id"],
                ]);

            }

            echo json_encode([
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data,
                "total" => $total_amount,
                "taxed" => $taxed_val,
                "freight_value" => $freight_value,
                "freight_tax" => $freight_tax,
                "other_charges" => $other_charges,
                "other_tax" => $other_tax,
                "packing_forwarding" => $packing_forwarding,
                "packing_forwarding_tax" => $packing_forwarding_tax,
                "round_off" => $round_off
            ]);
        } else {
            echo json_encode([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => $result->error
            ]);
        }
    break;


    case "grn_sub_edit":
        $unique_id = $_POST["unique_id"];
        $is_update = isset($_POST['is_update']) ? $_POST['is_update'] : false;

        $po_unique_id = fetch_po_unique_id1($sub_table, $unique_id);

        $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
        error_log("sc_un_id: " . print_r($po_sc_unique_id, true) . "\n", 3, "po_sc_log.txt");

        $td_data = fetch_tax_discount($po_sc_unique_id);

        $tax = $td_data['tax'];
        $tax_name = tax($tax)[0]['tax_name'];
        $tax = tax($tax)[0]['tax_value'];
        $discount = $td_data['discount'];
        $discount_type = $td_data['discount_type']; // Get the discount_type value directly

        error_log("tax: " . $tax . "\n" . "discount: " . $discount . "\n" . "discount_type: " . $discount_type . "\n", 3, "td_log.txt");

        $columns = [
            "gs.grn_main_unique_id",
            "gs.item_code",
            "gs.order_qty",
            "gs.uom",
            "IF('$po_unique_id' = 0, 0, COALESCE(grn_sub.total_received_qty, 0)) AS now_received_qty",
            "gs.update_qty",
            "poi_items.rate",
            "'$tax_name' AS tax_name",
            $discount_type . " AS discount_type", // Send discount_type value directly
            $discount . " AS discount",
            "ROUND(((gs.update_qty * poi_items.rate) - ((gs.update_qty * poi_items.rate * $discount) / 100)) + (((gs.update_qty * poi_items.rate - (gs.update_qty * poi_items.rate * $discount / 100)) * $tax) / 100), 2) AS amount",
            "gs.unique_id"
        ];

        if ($is_update) {
            $table_details = [
                "$sub_table gs 
                    LEFT JOIN ( 
                        SELECT 
                            gs2.item_code, 
                            gs2.po_unique_id, 
                            SUM(gs2.update_qty) AS total_received_qty
                        FROM grn_sublist as gs2
                        LEFT JOIN grn as g
                            ON g.screen_unique_id = gs2.screen_unique_id
                        WHERE gs2.po_unique_id = '$po_unique_id' AND gs2.screen_unique_id = '$screen_unique_id' AND gs2.is_delete = 0 AND g.is_delete = 0
                        GROUP BY gs2.item_code, gs2.po_unique_id
                    ) AS grn_sub 
                    ON gs.item_code = grn_sub.item_code 
                    AND gs.po_unique_id = grn_sub.po_unique_id
                LEFT JOIN purchase_order poi 
                    ON gs.po_unique_id = poi.unique_id
                LEFT JOIN purchase_order_items poi_items 
                    ON poi_items.screen_unique_id = poi.screen_unique_id 
                    AND poi_items.item_code = gs.item_code
                WHERE gs.unique_id = '$unique_id' 
                AND gs.is_delete = 0",
                $columns
            ];
        } else {
            $table_details = [
                "$sub_table gs 
                    LEFT JOIN ( 
                        SELECT 
                            gs2.item_code, 
                            gs2.po_unique_id, 
                            SUM(gs2.update_qty) AS total_received_qty
                        FROM grn_sublist as gs2
                        LEFT JOIN grn as g
                            ON g.screen_unique_id = gs2.screen_unique_id
                        WHERE gs2.po_unique_id = '$po_unique_id' AND gs2.screen_unique_id = '$screen_unique_id' AND gs2.is_delete = 0 AND g.is_delete = 0
                        GROUP BY gs2.item_code, gs2.po_unique_id
                    ) AS grn_sub 
                    ON gs.item_code = grn_sub.item_code 
                    AND gs.po_unique_id = grn_sub.po_unique_id
                LEFT JOIN purchase_order poi 
                    ON gs.po_unique_id = poi.unique_id
                LEFT JOIN purchase_order_items poi_items 
                    ON poi_items.screen_unique_id = poi.screen_unique_id 
                    AND poi_items.item_code = gs.item_code
                WHERE gs.unique_id = '$unique_id' 
                AND gs.is_delete = 0",
                $columns
            ];
        }

        $result = $pdo->select($table_details);
        error_log("result: " . print_r($result, true) . "\n", 3, "row_log.txt");

        if ($result->status) {
            $row = $result->data[0];
            error_log("row: " . print_r($row, true) . "\n", 3, "rows_log.txt");

            echo json_encode([
                "status" => true,
                "data"   => $row,
                "tax"    => $tax,
                "discount" => $discount,
                "discount_type" => $discount_type, // Send discount_type value directly
                "msg"    => "edit_data",
                "error"  => null
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "data"   => [],
                "msg"    => "error",
                "error"  => $result->error
            ]);
        }
    break;


    
    case "grn_sub_delete":
        $unique_id = $_POST["unique_id"];
    
        $columns = [
            "is_delete" => 1
        ];
        $where = [
            "unique_id" => $unique_id
        ];
    
        $action_obj = $pdo->update("$sub_table", $columns, $where);
    
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "delete_success" : "delete_error",
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
    break;
    
        case 'project_name':

        $company_id          = $_POST['company_id'];

        $project_name_options  = get_project_name("",$company_id);

        $project_name_options  = select_option($project_name_options,"Select the Project Name");

        echo $project_name_options;
        
        break;    
case 'get_purchase_order_no':

    $project_id = $_POST['project_id'];

    // Assuming get_purchase_orders_by_project returns an array of [id => value]
    $purchase_order_options  = get_po_number("",$project_id);

    $purchase_order_options = select_option($purchase_order_options, "Select Purchase Order No");

    echo $purchase_order_options;
    break;
case "get_po_items_for_grn":

    $po_unique_id = $_POST["unique_id"];

    // Step 1: Fetch screen_unique_id and supplier from purchase_order
    $po_result = $pdo->select(["purchase_order", ["screen_unique_id", "supplier_id"]], ["unique_id" => $po_unique_id]);

    if (!$po_result->status || empty($po_result->data)) {
        echo json_encode([
            "status" => false,
            "msg"    => "PO not found",
            "data"   => [],
            "error"  => $po_result->error
        ]);
        break;
    }

    $po_screen_unique_id = $po_result->data[0]["screen_unique_id"];
    $supplier_id = $po_result->data[0]["supplier_id"];

    // Step 2: Get supplier name
    $supplier_name = '';
    if ($supplier_id) {
        $supp = $pdo->select(["supplier_profile", ["supplier_name"]], ["unique_id" => $supplier_id]);
        if ($supp->status && count($supp->data)) {
            $supplier_name = $supp->data[0]["supplier_name"];
        }
    }

    // Step 3: Fetch matching items from purchase_order_items
    $po_item_table = "purchase_order_items";
    $columns = ["item_code", "lvl_2_quantity", "uom", "rate", "tax"];
    $table_details = [$po_item_table, $columns];
    $where = [
        "screen_unique_id" => $po_screen_unique_id,
        "is_delete" => 0
    ];
    $items_result = $pdo->select($table_details, $where);
    
    

    echo json_encode([
        "status"         => $items_result->status,
        "msg"            => $items_result->status ? "data_fetched" : "no_items",
        "data"           => $items_result->status ? $items_result->data : [],
        "supplier_name"  => $supplier_name,
        "supplier_id"    => $supplier_id, 
        "error"          => $items_result->error
    ]);
    break;


case "clear_grn_sublist":
    ob_clean(); // flush any previous output
    $screen_unique_id = $_POST["screen_unique_id"];

    $columns = [ "is_delete" => 1 ];
    $where   = [ "screen_unique_id" => $screen_unique_id ];

    $action_obj = $pdo->update("grn_sublist", $columns, $where);

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $action_obj->status ? "cleared" : "clear_failed",
        "error"  => $action_obj->error
    ]);
    break;

    
    default:
        
        break;
}

function batch_creation($table_name, $company_unique_id, $prefixs, $conn) {
  
  // Fetch the last batch ID for the given item_name
  $stmt = $conn->prepare("SELECT * FROM $table_name WHERE company_id = :company ORDER BY id DESC LIMIT 1");
  $stmt->execute([':company' => $company_unique_id]);
  
  // Fetch the results
  if ($pit_query = $stmt->fetch(PDO::FETCH_ASSOC)) {
      // Create new batch ID with the prefix
      $billno = $prefixs;

      // Generate a sequential ID
      $bill_order_no = generate_order_number($table_name, $conn, $prefixs);

      // Append the generated number to the prefix
      $billno .= sprintf("%03d", $bill_order_no);
      return $billno;
  } else {
      // Create new batch ID with the prefix
      $billno = $prefixs;

      // Generate a sequential ID
      $bill_order_no = generate_order_number($table_name, $conn, $prefixs);

      // Append the generated number to the prefix
      $billno .= sprintf("%03d", $bill_order_no);

      return $billno;
  }
}

function generate_order_number($table_name, $conn, $prefix) {
  // Query the database to find the highest existing number for the given prefix and increment it by one
  $stmt = $conn->prepare("SELECT MAX(sales_order_no) AS max_id FROM $table_name WHERE sales_order_no LIKE :prefix and is_delete = 0");
  $stmt->execute([':prefix' => $prefix . '%']);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Extract the numeric part of the batch_id and increment it
  $max_id = isset($result['max_id']) ? intval(substr($result['max_id'], strlen($prefix))) : 0;
  $new_order_number = $max_id + 1;

  return $new_order_number;
}

function generateGRN($label, &$labelData) {
    $year = $_SESSION['acc_year'];
    $number = 1;

    do {
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        $grn = "GRN/$label/$year/$paddedNumber";
        $number++;
    } while (in_array($grn, $labelData));

    // Optionally store the new GRN
    $labelData[] = $grn;

    return $grn;
}

function fetch_grn_number($table)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the grn_number)
    $table_columns = [
        "grn_number"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,  // Specify the table name
        $table_columns  // Specify the columns to fetch
    ];

    // Set the WHERE condition to filter by unique_id, is_active, and is_delete
    // $where = [
    //     "is_active" => 1,     // Optional: depending on your use case
    //     "is_delete" => 0      // Optional: depending on your use case
    // ];

    // Perform the query (assuming your PDO object has a select() method)
    $result = $pdo->select($table_details);

    $grn_numbers = [];

    // Check if the query was successful and if data is returned
    if ($result->status && !empty($result->data)) {
        // Loop through the data and collect all the grn_number values
        foreach ($result->data as $row) {
            $grn_numbers[] = $row['grn_number'];
        }
        error_log($grn_numbers . "\n", 3, "grn_log.txt");
        return $grn_numbers;
    }
}

function fetch_tax_discount($screen_unique_id)
{
    global $pdo; // ensure $pdo is available

    $table = "purchase_order_items";
    $columns = ["tax", "discount", "discount_type"];

    $table_details = [$table, $columns];

    $where = [
        "screen_unique_id" => $screen_unique_id,
        "is_delete" => 0
    ];

    $result = $pdo->select($table_details, $where);

    if ($result->status && !empty($result->data)) {
        return [
            'tax' => $result->data[0]['tax'],
            'discount' => $result->data[0]['discount'],
            'discount_type' => $result->data[0]['discount_type']
        ];
    } else {
        return [
            'tax' => 0,
            'discount' => 0,
            'discount_type' => 0
        ];
    }
}

?>