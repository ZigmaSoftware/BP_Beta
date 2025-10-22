<?php
include '../../config/dbconfig.php';
include '../../config/new_db.php';

$table              = "purchase_order";
$sub_list_table     = "purchase_order_items";
$documents_upload   = 'po_uploads';

$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

$action             = $_POST["action"];
file_put_contents("debug_po_filter.txt", print_r($_POST, true));
$user_id            = $_SESSION['sess_user_id'];
$date               = date("Y-m-d H:i:s");

$acc_year           = $_SESSION['acc_year'];
$session_id         = session_id();
$sess_user_type     = $_SESSION['sess_user_type'];
$sess_user_id       = $_SESSION['sess_user_id'];
$sess_company_id    = $_SESSION['sess_company_id'];
$sess_branch_id     = $_SESSION['sess_branch_id'];

switch ($action) {

    case "createupdate":
        $screen_unique_id = $_POST["screen_unique_id"];
        $unique_id        = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();
        $company_id       = $_POST["company_id"];
    
        // Company Name from company_creation
        $company_query = $pdo->select(["company_creation", ["company_code"]], ["unique_id" => $company_id, "is_delete" => 0]);
        $company_name = ($company_query->status && !empty($company_query->data)) ? $company_query->data[0]["company_code"] : "NA";
        $company_name_clean = preg_replace('/[^A-Za-z0-9]/', '', $company_name);
    
        // Check if record exists
        $exists = $pdo->select([$table, ["COUNT(*) AS count"]], "unique_id = '$unique_id' AND is_delete = 0");
    
        $po_prefix = "PO/$acc_year/$company_name_clean/";
        // Fetch latest PO number for this company and acc_year
        $serial_query = "
            SELECT purchase_order_no 
            FROM $table 
            WHERE purchase_order_no LIKE '$po_prefix%' 
              AND acc_year = '$acc_year'
            ORDER BY id DESC 
            LIMIT 1
        ";
        $serial_result = $pdo->query($serial_query);
        
        if ($serial_result->status && !empty($serial_result->data)) {
            // --- Case 1: Previous POs exist for same company in this acc_year
            $last_po_no = $serial_result->data[0]['purchase_order_no'];
            preg_match('/(\d{3})$/', $last_po_no, $matches);
            $last_serial = isset($matches[1]) ? (int)$matches[1] : 0;
            $next_serial = str_pad($last_serial + 1, 3, "0", STR_PAD_LEFT);
        } else {
            // --- Case 2: No previous PO found for this company in this acc_year
            if ($acc_year === "2025-2026") {
                // Default starting numbers (base + 1)
                $company_base = [
                    "XWM"     => 146,
                    "BPB"     => 93,
                    "BPIWS"   => 204,
                    "BPESIPL" => 72
                ];
        
                $base_serial = isset($company_base[$company_name_clean])
                    ? $company_base[$company_name_clean] + 1
                    : 1;
        
                $next_serial = str_pad($base_serial, 3, "0", STR_PAD_LEFT);
            } else {
                // --- Case 3: Different acc_year → reset numbering
                $next_serial = "001";
            }
        }
        $purchase_order_no = $po_prefix . $next_serial;

        $purchase_order_no = $po_prefix . $next_serial;
    
        $columns = [
            "unique_id"                 => $unique_id,
            "screen_unique_id"          => $screen_unique_id,
            "company_id"               => $_POST["company_id"],
            "project_id"               => $_POST["project_id"],
            "from_comp"                 => $_POST["from_comp"] ?? 0,
            "supplier_id"              => $_POST["supplier_id"],
            "branch_id"                => $_POST["branch_id"],
            "purchase_request_no"      => $_POST["purchase_request_no"],
            "entry_date"               => $_POST["entry_date"],
            "purchase_type"            => $_POST["purchase_type"],
            "revision_no"               => $_POST["revision_no"],
            "revision_date"             => $_POST["revision_date"],
            "revision_remarks"         => $_POST["revision_remarks"],
            "gst_no"                   => $_POST["gst_no"],
            "pan_no"                   => $_POST["pan_no"],
            "msme_type_display"        => $_POST["msme_type_display"],
            "msme_no"                  => $_POST["msme_no"],
            "contact_person"           => $_POST["contact_person"],
            "vendor_contact_no"        => $_POST["vendor_contact_no"],
            "quotation_no"             => $_POST["quotation_no"],
            "quotation_date"           => $_POST["quotation_date"],
            "net_amount"               => $_POST["net_amount"],
            "freight_value"            => $_POST["freight_value"],
            "freight_tax"              => $_POST["freight_tax"],
            "freight_amount"           => $_POST["freight_amount"],
            "other_charges"            => $_POST["other_charges"],
            "other_tax"                => $_POST["other_tax"],
            "other_charges_percentage" => $_POST["other_charges_percentage"],
            "packing_forwarding"       => $_POST["packing_forwarding"],
            "packing_forwarding_tax"   => $_POST["packing_forwarding_tax"],
            "packing_forwarding_amount"=> $_POST["packing_forwarding_amount"],
            "tcs_percentage"           => $_POST["tcs_percentage"],
            "tcs_amount"               => $_POST["tcs_amount"],
            "round_off"                => $_POST["round_off"],
            "gross_amount"             => $_POST["gross_amount"],
            "total_gst_amount"         => $_POST["total_gst_amount"],
            "delivery"                 => $_POST["delivery"],
            "payment_days"             => $_POST["payment_days"],
            "shipping_address"         => $_POST["shipping_address"],
            "billing_address"          => $_POST["billing_address"],
            "remarks"                  => $_POST["remarks"],
            "purchase_order_type"      => $_POST["po_for"],
            "acc_year"                 => $acc_year,
            "status"                   => 0, // Level 1 → Pending
            "lvl_2_status"             => 0, // Level 2 → Pending
            "session_id"               => $session_id,
            "sess_user_type"           => $sess_user_type,
            "sess_user_id"             => $sess_user_id,
            "sess_company_id"          => $sess_company_id,
            "sess_branch_id"           => $sess_branch_id,
            "created_user_id"          => $user_id,
            "created"                  => $date
        ];
    
        if (!$exists->status || !$exists->data[0]["count"]) {
            // Insert Mode
            $columns["purchase_order_no"] = $purchase_order_no;
            $action_obj = $pdo->insert($table, $columns);
            $msg = "create";
        } else {
            // Update Mode
            unset($columns["unique_id"], $columns["created_user_id"], $columns["created"], $columns["purchase_order_no"]);
    
            // ✅ Check existing status before resetting
            $status_query = $pdo->select([$table, ["status", "lvl_2_status"]], ["unique_id" => $unique_id, "is_delete" => 0]);
            if ($status_query->status && !empty($status_query->data)) {
                $current_status = (int)$status_query->data[0]["status"];
                $current_lvl_2  = (int)$status_query->data[0]["lvl_2_status"];
    
                if ($current_status === 1) {
                    unset($columns["status"]);
                }
                if ($current_lvl_2 === 1) {
                    unset($columns["lvl_2_status"]);
                }
            }
    
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;
    
            $action_obj = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
            $msg = "update";
        }
    
        echo json_encode([
            "status" => $action_obj->status,
            "data"   => ["unique_id" => $unique_id],
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
    break;
    
    
    case 'foreclose':
    $unique_id = $_POST['unique_id'] ?? '';
    $response = [];

    if (!empty($unique_id)) {
        $update = $pdo->update(
            "purchase_order",
            ["foreclose_status" => 1, "foreclose_date" => date('Y-m-d H:i:s')],
            ["unique_id" => $unique_id]
        );

        if ($update->status) {
            $response = ["status" => "success", "message" => "Purchase Order foreclosed successfully"];
        } else {
            $response = ["status" => "error", "message" => "Database update failed"];
        }
    } else {
        $response = ["status" => "error", "message" => "Invalid PO ID"];
    }

    echo json_encode($response);
    break;


        
  case 'datatable':

    ini_set('display_errors', 1);
    error_reporting(E_ALL);

    // ---------------------------- INPUTS ---------------------------- //
    $search = $_POST['search']['value'] ?? '';
    $length = $_POST['length'] ?? 10;
    $start  = $_POST['start'] ?? 0;
    $draw   = $_POST['draw'] ?? 1;
    $limit  = ($length == '-1') ? '' : $length;
    $data   = [];
    $debug_trace = []; // 🧩 store debug info for all POs

    // ---------------------------- BASE QUERY ---------------------------- //
    $columns = [
        "@a:=@a+1 s_no",
        "purchase_order_no",
        "company_id",
        "project_id",
        "supplier_id",
        "entry_date",
        "net_amount",
        "gross_amount",
        "appr_gross_amount",
        "lvl_2_gross_amount",
        "lvl_3_gross_amount",
        "status",
        "lvl_2_status",
        "lvl_3_status",
        "foreclose_status",
        "unique_id",
        "status as edit_status",
        "purchase_order_type",
        "closed"
    ];

    $table_details = [
        $table . " , (SELECT @a:=" . $start . ") AS a ",
        $columns
    ];

    $where = "is_delete = 0";
    $conditions = [];

    if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
        $conditions[] = "DATE(created) BETWEEN '{$_POST['from_date']}' AND '{$_POST['to_date']}'";
    }
    if (!empty($_POST['company_name'])) {
        $conditions[] = "company_id = '{$_POST['company_name']}'";
    }
    if (!empty($_POST['project_name'])) {
        $conditions[] = "project_id = '{$_POST['project_name']}'";
    }

    // ---------------------------- STATUS FILTER ---------------------------- //
    if (!empty($_POST['status'])) {
        switch ($_POST['status']) {
            case 'pending_l1':
                $conditions[] = "(status = 0 OR status IS NULL)";
                break;
            case 'approved_l1':
                $conditions[] = "status = 1 AND gross_amount <= 300000";
                break;
            case 'rejected_l1':
                $conditions[] = "status = 2";
                break;
            case 'pending_l2':
                $conditions[] = "status = 1 AND appr_gross_amount BETWEEN 300001 AND 1000000 AND (lvl_2_status = 0 OR lvl_2_status IS NULL)";
                break;
            case 'approved_l2':
                $conditions[] = "lvl_2_status = 1 AND gross_amount <= 1000000";
                break;
            case 'rejected_l2':
                $conditions[] = "lvl_2_status = 2";
                break;
            case 'pending_l3':
                $conditions[] = "status = 1 AND appr_gross_amount > 1000000 AND lvl_2_status = 1 AND (lvl_3_status = 0 OR lvl_3_status IS NULL)";
                break;
            case 'approved_l3':
                $conditions[] = "lvl_3_status = 1";
                break;
            case 'rejected_l3':
                $conditions[] = "lvl_3_status = 2";
                break;
        }
    }

    if (!empty($conditions)) {
        $where .= " AND " . implode(" AND ", $conditions);
    }

    $order_column = $_POST["order"][0]["column"] ?? 0;
    $order_dir    = $_POST["order"][0]["dir"] ?? 'asc';
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);
    $search_sql   = datatable_searching($search, $columns);
    if ($search_sql) $where .= " AND $search_sql";

    // ---------------------------- FETCH DATA ---------------------------- //
    $sql_function  = "SQL_CALC_FOUND_ROWS";
    $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records = total_records();

    if ($result->status) {
        foreach ($result->data as $value) {

            // ---------------------------- MAP NAMES ---------------------------- //
            $company_data = company_name($value['company_id']);
            $value['company_id'] = $company_data[0]['company_name'] ?? '';

            $project_data = project_name($value['project_id']);
            $value['project_id'] = ($project_data[0]['project_code'] ?? '') . " / " . ($project_data[0]['project_name'] ?? '');

            $supplier_data = supplier($value['supplier_id']);
            $value['supplier_id'] = $supplier_data[0]['supplier_name'] ?? '';

            $raw_id = $value['unique_id'];
            $btn_view   = btn_views($folder_name, $raw_id);
            $btn_print  = btn_prints($folder_name, $raw_id);
            $btn_upload = btn_docs($folder_name, $raw_id);
            $btn_update = btn_update($folder_name, $raw_id);
            $btn_delete = btn_delete($folder_name, $raw_id);

            // ---------------------------- APPROVAL ---------------------------- //
            $approval = get_final_approval_status($value);
            $value['approval_status'] = $approval['html'];
            $approval_state           = $approval['state'];

            // ============================================================
            // FORECLOSE LOGIC (PO vs GRN / SRN) — FIXED + DEBUG TRACE
            // ============================================================
            $foreclose_btn    = "<span class='badge bg-secondary text-light fs-4'>Not Available</span>";
            $foreclose_status = $value['foreclose_status'] ?? 0;
            $closed_status    = $value['closed'] ?? 0;
            $po_id            = $value['unique_id'];
            $po_sc_id         = fetch_po_sc_unique_id($po_id);
            $po_type_id       = $value['purchase_order_type'] ?? '';

            // 🔹 Determine PO Type → SRN (service) or GRN (regular)
            if ($po_type_id === '683568ca2fe8263239') {
                $receipt_sub_table = "srn_sublist";
                $receipt_label     = "SRN";
            } else {
                $receipt_sub_table = "grn_sublist";
                $receipt_label     = "GRN";
            }

            // ------------------------------------------------------------
            // 1️⃣ Fetch all PO items
            // ------------------------------------------------------------
            $item_result = $pdo->select(["purchase_order_items", ["item_code", "quantity"]], [
                "screen_unique_id" => $po_sc_id,
                "is_delete" => 0
            ]);
            $po_items = ($item_result->status && !empty($item_result->data)) ? $item_result->data : [];

            $total_items = count($po_items);
            $fully_received = 0;
            $partial_received = false;
            $debug_rows = [];

            // ------------------------------------------------------------
            // 2️⃣ Loop each PO item and total up receipts
            // ------------------------------------------------------------
            foreach ($po_items as $item) {
                $po_item_id = $item['item_code'];
                $req_qty    = (float)$item['quantity'];
                $max_received = 0;
                $srn_grn_no = '-';
                $used_source = 'none';
            
                // fetch from GRN or SRN sublist using po_unique_id
                $recv_result = $pdo->select(
                    [$receipt_sub_table, [
                        "now_received_qty",
                        "update_qty",
                        "order_qty",
                        "screen_unique_id AS main_no"
                    ]],
                    [
                        "po_unique_id" => $po_id,
                        "item_code"    => $po_item_id,
                        "is_delete" => 0
                    ]
                );
            
                if ($recv_result->status && !empty($recv_result->data)) {
                    $max_now = 0;
                    $max_update = 0;
                    $max_order = 0;
            
                    foreach ($recv_result->data as $r) {
                        $now_val    = (float)($r['now_received_qty'] ?? 0);
                        $update_val = (float)($r['update_qty'] ?? 0);
                        $order_val  = (float)($r['order_qty'] ?? 0);
            
                        if ($now_val > $max_now) $max_now = $now_val;
                        if ($update_val > $max_update) $max_update = $update_val;
                        if ($order_val > $max_order) $max_order = $order_val;
            
                        // keep last non-empty main_no
                        if (!empty($r['main_no'])) {
                            $srn_grn_no = $r['main_no'];
                        }
                    }
            
                    // Fallback hierarchy: now_received_qty → update_qty → order_qty
                    if ($max_now > 0) {
                        $max_received = $max_now;
                        $used_source = 'now_received_qty';
                    } elseif ($max_order > 0) {
                        $max_received = $max_order;
                        $used_source = 'order_qty';
                    }
                }
            
                // compare quantities
                $status_text = 'Pending';
                if ($max_received > 0 && $max_received < $req_qty) {
                    $partial_received = true;
                    $status_text = 'Partial';
                }
                if ($max_received >= $req_qty && $req_qty > 0) {
                    $fully_received++;
                    $status_text = 'Completed';
                }
            
                // 🧩 Collect item debug trace
                $debug_rows[] = [
                    'PO_No'        => $value['purchase_order_no'],
                    'PO_Item_ID'   => $po_item_id,
                    'Order_Qty'    => $req_qty,
                    "{$receipt_label}_Main_No" => $srn_grn_no,
                    'Max_Received_Qty' => $max_received,
                    'Used_Source'  => $used_source,
                    'Status'       => $status_text
                ];
            }


            $all_received_done = ($total_items > 0 && $fully_received === $total_items);

            // ------------------------------------------------------------
            // 3️⃣ Decide display / update logic
            // ------------------------------------------------------------
            $l1_status = $value['status']       ?? 0;
            $l2_status = $value['lvl_2_status'] ?? 0;
            $l3_status = $value['lvl_3_status'] ?? 0;

            if ($foreclose_status == 1) {
                $foreclose_btn = "<span class='badge bg-success text-light fs-4'>Foreclosed</span>";
            } elseif ($closed_status == 1) {
                $foreclose_btn = "<span class='badge bg-success text-light fs-4'>Closed</span>";
            }
            elseif ($all_received_done) {
                $foreclose_btn = "<span class='badge bg-info text-dark fs-4'>{$receipt_label} Raised</span>";
                // auto-mark as foreclosed
                
            } elseif ($partial_received) {
                $foreclose_btn = "<button type='button' class='btn btn-sm btn-dark' onclick=\"foreclosePO('{$po_id}')\">Foreclose</button>";
            } elseif (in_array(2, [$l1_status, $l2_status, $l3_status])) {
                $foreclose_btn = "<span class='badge bg-secondary text-light fs-4'>Not Available</span>";
            } elseif ($l1_status == 1 && ($l2_status == 0 || $l3_status == 0)) {
                $foreclose_btn = "<button type='button' class='btn btn-sm btn-dark' onclick=\"foreclosePO('{$po_id}')\">Foreclose</button>";
            }

            // keep this PO’s debug rows
            $debug_trace[$value['purchase_order_no']] = $debug_rows;

            // ============================================================
            // ACTIONS
            // ============================================================
            $receipts_exist = has_grn_or_srn($raw_id);
            $action_buttons = "";
            if (!$receipts_exist) {
                if ($approval_state == 'approved' || $approval_state == 'pending_l1') {
                    $action_buttons .= $btn_update;
                }
            }
            $action_buttons .= $btn_delete . $btn_upload;

            // ============================================================
            // FINAL BUILD
            // ============================================================
            unset(
                $value['status'], $value['lvl_2_status'], $value['lvl_3_status'],
                $value['appr_gross_amount'], $value['lvl_2_gross_amount'], $value['lvl_3_gross_amount']
            );

            $value['view']       = $btn_view;
            $value['print']      = $btn_print;
            $value['foreclose']  = $foreclose_btn;
            $value['actions']    = $action_buttons;

            $data[] = [
                $value['s_no'],
                $value['purchase_order_no'],
                $value['company_id'],
                $value['project_id'],
                $value['supplier_id'],
                $value['entry_date'],
                $value['net_amount'],
                $value['gross_amount'],
                $value['approval_status'],
                $value['view'],
                $value['print'],
                $value['foreclose'],
                $value['actions']
            ];
        }

        $json_array = [
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data,
            "debug_trace"     => $debug_trace   // 🧠 full breakdown per PO
        ];
    } else {
        $json_array = [
            "draw" => 0,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
            "error" => $result->error ?? 'Unknown error'
        ];
    }

    echo json_encode($json_array);
    break;


        
    case 'documents_add_update':

        $upload_unique_id = $_POST["upload_unique_id"] ?? null;
        $type             = $_POST["type"] ?? null;
        $unique_id        = $_POST["unique_id"] ?? null;
        
        // Log incoming POST data
        error_log("POST: " . print_r($_POST, true) . "\n", 3, "doc_logs.txt");
        
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
            $target_dir = "../../uploads/purchase_order_test/";
            $folder_path = "purchase_order_test/";

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
            "po_unique_id"              => $upload_unique_id,
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
        
        error_log("action_obj: " . print_r($action_obj, true) . "\n", 3, "doc_logs.txt");
        
        $data_array = [
            "insert_id" => $action_obj->data,     // if it's lastInsertId()
            "upload"    => $upload_unique_id
        ];
        
        error_log("json_response: " . print_r([
            "status" => $action_obj->status,
            "data"   => $data_array,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ], true) . "\n", 3, "doc_logs.txt");

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
            "po_unique_id"               => $upload_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        error_log("documents datatable query: " . $result->sql . "\n", 3, "debug.txt");

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
                        $image_path = "../blue_planet_erp/uploads/purchase_order_test/" . trim($image_file);
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
    
    case "compare_states":
        
        $project_id = $_POST['project_id'];
        $supplier_id = $_POST['supplier_id'];
        
        $project_state = get_state_unique_id("project_creation", $project_id) 
              ?: get_state_unique_id("company_creation", $project_id);
        $supplier_state = get_state_unique_id("supplier_profile", $supplier_id);
        
        error_log($project_id, 3, "project_id.log");
        error_log($project_state, 3, "project_id.log");
        error_log($supplier_state, 3, "project_id.log");
        
        if($project_state === $supplier_state){
            $same_state = 1;  // true
            $igst = 0;        // false
        } else {
            $same_state = 0;  // false
            $igst = 1;        // true
        }

        
        header('Content-Type: application/json'); // ensure JSON header

        echo json_encode([
            "status"         => "success",
            "same_state"     => $same_state,
            "igst_applicable"=> $igst,
            "project_state"  => $project_state,
            "supplier_state" => $supplier_state
        ]);
        
    break;


    // Sublist Add/Update (by screen_unique_id)
    case "po_sub_add_update":
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id = $_POST["sublist_unique_id"];

        $columns = [
            "screen_unique_id"   => $screen_unique_id,
            "item_code"          => $_POST["item_code"],
            "quantity"           => $_POST["quantity"],
            "appr_quantity"      => $_POST["quantity"],
            "lvl_2_quantity"     => $_POST["quantity"],
            "uom"                => $_POST["uom"],
            "rate"               => $_POST["rate"],
            "discount"           => $_POST["discount"],
            "discount_type"      => $_POST["discount_type"], // ✅ NEW
            "tax"                => $_POST["tax"],
            "amount"             => $_POST["amount"],
            "gst_amount"         => $_POST["gst_amount"],
            "item_remarks"       => $_POST["item_remarks"],
            "delivery_date"      => $_POST["delivery_date"],
            "acc_year"           => $acc_year,
            "session_id"         => $session_id,
            "sess_user_type"     => $sess_user_type,
            "sess_user_id"       => $sess_user_id,
            "sess_company_id"    => $sess_company_id,
            "sess_branch_id"     => $sess_branch_id
        ];

        if (!empty($sublist_unique_id)) {
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;
            $action_obj = $pdo->update($sub_list_table, $columns, ["unique_id" => $sublist_unique_id]);
            $msg = "update";
        } else {
            $columns["unique_id"] = unique_id();
            $columns["created_user_id"] = $user_id;
            $columns["created"] = $date;
            $action_obj = $pdo->insert($sub_list_table, $columns);
            $msg = "add";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
        break;
        
     case "po_sub_add_update_modal":
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id = $_POST["sublist_unique_id"];
        $pr_unique_id = $_POST["pr_unique_id"];
        $remarks    = $_POST["remarks"];
        $delivery_date    = $_POST["delivery_date"];
    
        $columns = [
            "screen_unique_id"   => $screen_unique_id,
            "item_code"          => $_POST["item_code"],
            "quantity"           => $_POST["quantity"],
            "uom"                => $_POST["uom"],
            "pr_sub_unique_id"   => $pr_unique_id,
            "item_remarks"       => $remarks,
            "delivery_date"      => $delivery_date,
        ];
    
        if (!empty($sublist_unique_id)) {
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;
            $action_obj = $pdo->update($sub_list_table, $columns, ["unique_id" => $sublist_unique_id]);
            error_log(print_r($action_obj, true), 3, "po_insert.log");
            $msg = "update";
        } else {
            $columns["unique_id"] = unique_id();
            $columns["created_user_id"] = $user_id;
            $columns["created"] = $date;
    
            $action_obj = $pdo->insert($sub_list_table, $columns);
            error_log(print_r($action_obj, true), 3, "po_insert.log");
            $msg = "add";
        }
    
        // ✅ Update flag in parent table first
        $coulum_pr = ["po_add_item" => 1];
        $action_pr = $pdo->update('purchase_requisition_items', $coulum_pr, ["unique_id" => $pr_unique_id]);
        error_log(print_r($action_pr, true), 3, "logs/po_add.log");
    
        // ✅ If no rows updated in parent, try child table
        if ($action_pr->rowCount == 0) {
            $action_pr = $pdo->update('obom_child_table', $coulum_pr, ["unique_id" => $pr_unique_id]);
            error_log(print_r($action_pr, true), 3, "logs/po_add1.log");
        }
    
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
    break;


    // Sublist List via screen_unique_id
    case "purchase_order_sublist_datatable":
        $screen_unique_id = $_POST["screen_unique_id"];
        $btn_prefix = "po_sub";

        $columns = [
            "@a:=@a+1 as s_no",
            "item_code",
            // "quantity",
            "uom",
            "quantity",
            "rate",
            "discount_type", // ✅ NEW
            "discount",
            "tax",
            "amount",
            "delivery_date",
            "item_remarks",
            "unique_id"
        ];

        $table_details = ["purchase_order_items, (SELECT @a:=0) AS a", $columns];
        $where = ["screen_unique_id" => $screen_unique_id, "is_delete" => 0];

        $result = $pdo->select($table_details, $where);
        error_log(print_r($result, true), 3, "logs/result.log");
        $data = [];

        if ($result->status) {
            foreach ($result->data as $row) {
                if ($row["discount_type"] == 2) {
                    $row["discount_type"] = "₹";
                } else if ($row["discount_type"] == 1) {
                    $row["discount_type"] = "%";
                } else {
                    $row["discount_type"] = "-";
                }
                
               $item_data = item_name_list($row["item_code"]);
                $display_item = isset($item_data[0]["item_name"], $item_data[0]["item_code"])
                    ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
                    : "-";
                $row['item_code'] = $display_item;
            
                // Convert UOM ID to name
                $uom_data = unit_name($row["uom"]);
                $row["uom"] = !empty($uom_data[0]["unit_name"]) ? $uom_data[0]["unit_name"] : $row["uom"];
            
                // Convert Tax ID to name
                $tax_data = tax($row["tax"]);
                $row["tax"] = !empty($tax_data[0]["tax_name"]) ? $tax_data[0]["tax_name"] : $row["tax"];
                
                $row['delivery_date'] = disdate($row['delivery_date']);
            
                $edit = btn_edit($btn_prefix, $row["unique_id"]);
                $del  = btn_delete($btn_prefix, $row["unique_id"]);
                $row["unique_id"] = $edit . $del;
            
                $data[] = array_values($row);
            }


            echo json_encode([
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
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

    // Sublist Edit
    case "po_sub_edit":
        $unique_id = $_POST["unique_id"];
        $columns = [
            "unique_id",
            "screen_unique_id",
            "item_code",
            "quantity",
            "uom",
            "rate",
            "discount",
            "discount_type", // ✅ NEW
            "tax",
            "amount",
            "delivery_date",
            "item_remarks"
        ];
        $where = ["unique_id" => $unique_id, "is_delete" => 0];
        $result = $pdo->select([$sub_list_table, $columns], $where);

        echo json_encode([
            "status" => $result->status,
            "data"   => $result->status ? $result->data[0] : [],
            "msg"    => $result->status ? "edit_data" : "error",
            "error"  => $result->error
        ]);
        break;

case "po_sub_delete":
    $unique_id = $_POST["unique_id"];
    $response = [];

    // Step 1: Soft delete the sublist record
    $update_data = ["is_delete" => 1];
    $update_where = ["unique_id" => $unique_id];

    $delete_status = $pdo->update($sub_list_table, $update_data, $update_where);

    $response["delete_status"] = $delete_status->status;
    $response["delete_msg"]    = $delete_status->status ? "success_delete" : "error";
    $response["delete_sql"]    = $delete_status->sql;
    $response["delete_error"]  = $delete_status->error;

    // Step 2: Get pr_sub_unique_id from the same sublist record
    $select_columns = ["pr_sub_unique_id"];
    $select_where   = ["unique_id" => $unique_id];

    $pr_sub_result = $pdo->select([$sub_list_table, $select_columns], $select_where);

    $response["select_status"] = $pr_sub_result->status;
    $response["select_sql"]    = $pr_sub_result->sql ?? "";
    $response["select_error"]  = $pr_sub_result->error ?? "";

    if ($pr_sub_result->status && !empty($pr_sub_result->data)) {
       $pr_sub_unique_id = $pr_sub_result->data[0]["pr_sub_unique_id"];

        $response["pr_sub_unique_id"] = $pr_sub_unique_id;

        if (!empty($pr_sub_unique_id)) {
            // Step 3: Reset po_add_item = 0 in purchase_requisition_items
            $pr_update_data = ["po_add_item" => 0];
            $pr_update_where = ["unique_id" => $pr_sub_unique_id];

            $po_add_reset = $pdo->update("purchase_requisition_items", $pr_update_data, $pr_update_where);

            $response["po_add_item_reset"] = $po_add_reset->status;
            $response["po_add_item_sql"]   = $po_add_reset->sql;
            $response["po_add_item_error"] = $po_add_reset->error;
        } else {
            // pr_sub_unique_id is null or empty
            $response["pr_sub_unique_id_note"] = "pr_sub_unique_id is empty or null, skipping PR update";
        }
    } else {
        $response["select_note"] = "No matching sublist record found for unique_id.";
    }

    echo json_encode($response);
    break;



    // Item Details for Select2
    case "get_item_details_by_code":
        $item_code = $_POST["item_code"];
        $columns = ["description", "uom_unique_id"];
        $where = ["unique_id" => $item_code, "is_delete" => 0];
        $result = $pdo->select(["item_master", $columns], $where);

        if ($result->status && !empty($result->data)) {
            $description = $result->data[0]['description'];
            $uom_id = $result->data[0]['uom_unique_id'];
            $uom_data = unit_name($uom_id);
            $uom_name = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";

echo json_encode([
    "status" => true,
    "data" => [
        "description" => $description,
        "uom" => $uom_name,
        "uom_id" => $uom_id
    ]
]);

        } else {
            echo json_encode([
                "status" => false,
                "error" => "Item not found"
            ]);
        }
        break;

    case 'get_tax_val':
        $unique_id = $_POST['code'];

        $json_array     = "";

        $tax_data       = tax($unique_id);

        if ($unique_id) {
            $json_array = [
                'status' => 'success',
                'data' => $tax_data[0]['tax_value']
            ];
            echo json_encode($json_array);
        } else {
            $json_array = [
                'status' => 'empty',
                'message' => 'No matching data found.'
            ];
            echo json_encode($json_array);
        }


        break;

    case "get_pr_sublist":
    $company_id = $_POST["company_id"];
    $project_id = $_POST["project_id"];
    $po_type    = $_POST["po_type"];

    $table = "purchase_requisition_items AS sub 
              JOIN purchase_requisition AS main 
              ON main.unique_id = sub.main_unique_id";

    $columns = [
        "main.pr_number AS pr_number",
        "main.sales_order_id",
        "sub.item_code",
        "sub.item_description",
        "sub.quantity",
        "sub.uom",
        "sub.required_delivery_date",
        "sub.unique_id",
        "sub.item_code AS item_id",
        "sub.item_remarks AS remarks",
        "sub.po_add_item"
    ];

    $where = "sub.main_unique_id != '' 
              AND sub.main_unique_id = main.unique_id
              AND main.company_id = '$company_id'
              AND main.project_id = '$project_id'
              AND main.requisition_type = '$po_type'
              AND main.is_delete = 0 
              AND sub.is_delete = 0 
              AND lvl_2_status = 1";

    $result = $pdo->select([$table, $columns], $where);

    error_log(print_r($result, true), 3, "logs/pr_sub.log");
    error_log(print_r($where, true), 3, "logs/pr_sub.log");

    $data = [];

    if ($result->status) {
        foreach ($result->data as $row) {
            $data[] = $row; // base row (main PR item)

            // --- Fetch possible child rows like in purchase_sublist_datatable ---
            $prod_unique_id = $row["item_code"];
            $so_id          = $row["sales_order_id"];

            $sublist_res = $pdo->select(
                ["obom_child_table", ["unique_id", "item_unique_id", "qty", "uom_unique_id", "remarks", "po_add_item"]],
                ["prod_unique_id" => $prod_unique_id, "so_unique_id" => $so_id, "is_delete" => 0]
            );

            error_log(print_r($sublist_res, true), 3, "logs/pr_sub.log");

            if ($sublist_res->status && !empty($sublist_res->data)) {
                foreach ($sublist_res->data as $child) {
                    $child_row = $row; // clone base row

                    $child_row["item_code"]        = $child["item_unique_id"];
                    $child_row["quantity"]         = number_format($child["qty"] * $row["quantity"], 2);
                    $child_row["uom"]              = $child["uom_unique_id"];
                    $child_row["remarks"]          = $child["remarks"];
                    $child_row["item_description"] = item_name_list($row["item_code"])[0]["item_name"] . " - (BOM Child Item)";
                    $child_row["unique_id"]        = $child["unique_id"];  
                    $child_row["po_add_item"]      = $child["po_add_item"];  
                    $child_row["is_child"]         = 1;

                    $data[] = $child_row;
                }
            }
        }

        // --- Header with Add All button ---
        echo "
        <div class='d-flex justify-content-between align-items-center mb-2'>
            <h5 class='mb-0'>Purchase Requisition Sublist</h5>
            <button type='button' class='btn btn-success' id='add_all_btn'>
                <i class='bx bx-plus'></i> Add All
            </button>
        </div>";

        // --- Render table ---
        echo "<table class='table table-bordered table-striped'>";
        echo "<thead>
                <tr>
                    <th>#</th>
                    <th>PR Number</th>
                    <th>Item</th>
                    <th>Item Description</th>
                    <th>Qty</th>
                    <th>UOM</th>
                    <th>Remarks</th>
                    <th>Delivery Date</th>
                    <th>Action</th>
                </tr>
              </thead>
              <tbody>";

        $i = 1;
        foreach ($data as $row) {
            // Skip PR rows already added
            if (isset($row["po_add_item"]) && $row["po_add_item"] == 1) {
                continue;
            }

            // --- Item name ---
            $item_data = item_name_list($row["item_code"]);
            $display_item = isset($item_data[0]["item_name"], $item_data[0]["item_code"])
                ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
                : "-";

            // --- UOM ---
            $unit_details = unit_name($row["uom"]);
            $unit = $unit_details[0]["unit_name"] ?? "";

            $delivery_date = disdate($row["required_delivery_date"]);

            // --- Fallback for empty description ---
            if (
                empty($row["item_description"]) ||
                strtolower(trim($row["item_description"])) === "null" ||
                $row["item_description"] === "0"
            ) {
                $row["item_description"] = $item_data[0]["item_name"] ?? "-";
            }

            // --- Row output with data attributes for Add-All JS ---
            $remarks_safe = htmlspecialchars($row["remarks"] ?? "", ENT_QUOTES);

            echo "<tr
                    data-item_code='{$row["item_code"]}'
                    data-uom='{$row["uom"]}'
                    data-quantity='{$row["quantity"]}'
                    data-pr_unique_id='{$row["unique_id"]}'
                    data-delivery_date='{$row["required_delivery_date"]}'
                    data-remarks='{$remarks_safe}'
                  >";

            echo "<td>{$i}</td>";
            echo "<td>{$row["pr_number"]}</td>";
            echo "<td>{$display_item}</td>";
            echo "<td>{$row["item_description"]}</td>";
            echo "<td>{$row["quantity"]}</td>";
            echo "<td>{$unit}</td>";
            echo "<td>{$row["remarks"]}</td>";
            echo "<td>{$delivery_date}</td>";
            echo "<td>
                    <button type='button' id='sub_add' class='btn btn-success po_sublist_add_modal_btn'
                        onclick=\"po_sublist_add_update_pop_up(
                            '{$row["item_code"]}',
                            '{$row["uom"]}',
                            '{$row["quantity"]}',
                            '{$row["unique_id"]}',
                            '{$row["required_delivery_date"]}',
                            '{$remarks_safe}'
                        )\">
                        Add
                    </button>
                  </td>";
            echo "</tr>";

            $i++;
        }

        echo "</tbody></table>";
    } else {
        echo "<div class='text-danger'>No sublist found for this PR number.</div>";
    }
    break;


    case 'project_name':
        $company_id          = $_POST['company_id'];
        $project_name_options  = get_project_name("", $company_id);
        $project_name_options  = select_option($project_name_options, "Select the Project Name");
        echo $project_name_options;
        break;
        
   case 'delete':

    $unique_id = $_POST['unique_id'];
    $screen_unique_id = fetch_po_sc_unique_id($unique_id);
    $pr_sub_id = fetch_pr_sub_uid($screen_unique_id);
    $pr_main_id = fetch_pr_main_uid($pr_sub_id);
    $remarks   = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    $columns = [
        "is_delete"          => 1,
        "is_delete_remarks"  => $remarks
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];
    
    $action_obj = $pdo->update($table, $columns, $update_where);
    
    $action_obj1 = $pdo->update($sub_list_table, $columns, ["screen_unique_id" => $screen_unique_id]);
    
    $action_obj2 = $pdo->update("purchase_requisition_items", ["po_add_item" => 0], ["unique_id" => $pr_sub_id]);
    
    $action_obj3 = $pdo->update("purchase_requisition", ["is_active" => 1], ["unique_id" => $pr_main_id]);
    
    $status = $action_obj->status;
    $data   = $action_obj->data;
    $error  = $action_obj->error ?? '';
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
   
    case 'cancel':

    $screen_unique_id = $_POST['screen_unique_id'];

    // Fetch all PR sub IDs from the PO sublist for this screen
    $sublist_rows = $pdo->select(
        [$sub_list_table, ["pr_sub_unique_id"]],
        ["screen_unique_id" => $screen_unique_id]
    );

    if ($sublist_rows->status) {
        if (!empty($sublist_rows->data)) {
            foreach ($sublist_rows->data as $row) {
                $pr_sub_id = $row["pr_sub_unique_id"];

                // Reset in purchase_requisition_items
                $action_obj = $pdo->update(
                    "purchase_requisition_items",
                    ["po_add_item" => 0],
                    ["unique_id" => $pr_sub_id]
                );

                // If nothing was reset in PR items, try obom_child_table
                if ($action_obj->rowCount == 0) {
                    $pdo->update(
                        "obom_child_table",
                        ["po_add_item" => 0],
                        ["unique_id" => $pr_sub_id]
                    );
                }

                // Reactivate the main requisition for this sub_id
                $pr_main_id = fetch_pr_main_uid($pr_sub_id);
                if ($pr_main_id) {
                    $pdo->update(
                        "purchase_requisition",
                        ["is_active" => 1],
                        ["unique_id" => $pr_main_id]
                    );
                }
            }
        }

        // ✅ Treat both “with PRs” and “no PRs” as success
        $status = true;
        $msg    = "success_delete";
    } else {
        $status = false;
        $msg    = "db_error";
    }

    echo json_encode([
        "status" => $status,
        "msg"    => $msg
    ]);
    break;

case "get_company_address":
    $company_id = $_POST["company_id"];
    $columns = ["address", "country", "state", "city"];
    $where = ["unique_id" => $company_id, "is_delete" => 0];
    $result = $pdo->select(["company_creation", $columns], $where);

    if ($result->status && !empty($result->data)) {
        $row = $result->data[0];

        $country_name = get_country_name($row["country"]);
        $state_name   = get_state_name($row["state"]);
        $city_name    = get_city_name($row["city"]);

        $full_address = trim($row["address"]) . ",\n" . $city_name . ", " . $state_name . ", " . $country_name;

        echo json_encode(["status" => true, "address" => $full_address]);
    } else {
        echo json_encode(["status" => false, "address" => ""]);
    }
    break;

case "get_project_address":
    $project_id = $_POST["project_id"];
    $columns = ["address", "country", "state", "city"];
    $where = ["unique_id" => $project_id, "is_delete" => 0];
    $result = $pdo->select(["project_creation", $columns], $where);

    if ($result->status && !empty($result->data)) {
        $row = $result->data[0];

        $country_name = get_country_name($row["country"]);
        $state_name   = get_state_name($row["state"]);
        $city_name    = get_city_name($row["city"]);

        $full_address = trim($row["address"]) . ",\n" . $city_name . ", " . $state_name . ", " . $country_name;

        echo json_encode(["status" => true, "address" => $full_address]);
    } else {
        echo json_encode(["status" => false, "address" => ""]);
    }
    break;

case "get_supplier_details_json":
    $supplier_id = $_POST["supplier_id"];

    $table = "supplier_profile as sp
              LEFT JOIN supplier_contact_person as scp ON sp.unique_id = scp.supplier_profile_unique_id AND scp.is_delete = 0
              LEFT JOIN msme_type as mt ON sp.msme_type = mt.unique_id";

    $columns = [
        "sp.gst_no",
        "sp.pan_no",
        "sp.msme_value",
        "mt.msme_type",
        "scp.contact_person_name",
        "scp.contact_person_contact_no"
    ];

    $where = "sp.unique_id = '$supplier_id'AND sp.is_delete = 0";

    $result = $pdo->select([$table, $columns], $where);

    if ($result->status && !empty($result->data)) {
        $row = (array)$result->data[0]; // convert stdClass to array for easy access

        // Fallback defaults
        $row["gst_no"] = $row["gst_no"] ?: "-";
        $row["pan_no"] = $row["pan_no"] ?: "-";
        $row["msme_value"] = $row["msme_value"] ?: "-";
        $row["msme_type"] = $row["msme_type"] ?: "-";
        $row["contact_person_name"] = $row["contact_person_name"] ?: "-";
        $row["contact_person_contact_no"] = $row["contact_person_contact_no"] ?: "-";

        echo json_encode([
            "status" => true,
            "data" => $row
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "error" => "No supplier details found."
        ]);
    }

    break;



    default:
        echo json_encode(["status" => false, "error" => "Invalid action"]);
        break;
}


function get_country_name($id = "") {
    if (!$id) return "";
    $country_data = country($id);
    return !empty($country_data[0]["name"]) ? $country_data[0]["name"] : "";
}

function get_state_name($id = "") {
    if (!$id) return "";
    $state_data = state($id);
    return !empty($state_data[0]["state_name"]) ? $state_data[0]["state_name"] : "";
}

function get_city_name($id = "") {
    if (!$id) return "";
    $city_data = city($id);
    return !empty($city_data[0]["city_name"]) ? $city_data[0]["city_name"] : "";
}

function get_state_unique_id($table, $id) {
    global $pdo; // Ensure $pdo is accessible

    $result = $pdo->select([$table, ["state"]], ["unique_id" => $id]);

    if ($result->status && !empty($result->data[0]['state'])) {
        return $result->data[0]['state']; // This is the state_unique_id
    }

    return '';
}

function get_final_approval_status($row) {
    $gross      = floatval($row['gross_amount']);
    $appr_gross = floatval($row['appr_gross_amount']);
    $lvl2_gross = floatval($row['lvl_2_gross_amount']);
    $lvl3_gross = floatval($row['lvl_3_gross_amount']);
    
    $status = $row['status'];       // L1
    $lvl2   = $row['lvl_2_status']; // L2
    $lvl3   = $row['lvl_3_status']; // L3

    // -----------------------------
    // Step 1: Approved/Rejected (priority L3 > L2 > L1)
    // -----------------------------
    if ($lvl3 == '1') return ['html'=>'<span style="color: green; font-weight: bold;">Approved (L3)</span>', 'state'=>'approved'];
    if ($lvl3 == '2') return ['html'=>'<span style="color: red; font-weight: bold;">Rejected (L3)</span>', 'state'=>'rejected_l3'];

    if ($lvl2 == '1' && $gross <= 1000000) {
        return ['html'=>'<span style="color: green; font-weight: bold;">Approved (L2)</span>', 'state'=>'approved'];
    }
    if ($lvl2 == '2') {
        return ['html'=>'<span style="color: red; font-weight: bold;">Rejected (L2)</span>', 'state'=>'rejected_l2'];
    }

    if ($status == '1' && $gross <= 300000) {
        return ['html'=>'<span style="color: green; font-weight: bold;">Approved (L1)</span>', 'state'=>'approved'];
    }
    if ($status == '2') {
        return ['html'=>'<span style="color: red; font-weight: bold;">Rejected (L1)</span>', 'state'=>'rejected_l1'];
    }

    // -----------------------------
    // Step 2: Pending (priority L1 > L2 > L3)
    // -----------------------------
    if ($status == '0' || $status == '' || $gross <= 300000) {
        return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L1)</span>', 'state'=>'pending_l1'];
    }

    if ($gross > 300000 && $gross <= 1000000) {
        if ($status == '1' && $appr_gross > 300000 && $appr_gross <= 1000000) {
            if ($lvl2 == '0' || $lvl2 == '') {
                return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L2)</span>', 'state'=>'pending_l2'];
            }
        }
        return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L1)</span>', 'state'=>'pending_l1'];
    }

    if ($gross > 1000000) {
        if ($status == '1' && $appr_gross > 1000000) {
            if ($lvl2 == '1' && $lvl2_gross > 1000000) {
                if ($lvl3 == '0' || $lvl3 == '') {
                    return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L3)</span>', 'state'=>'pending_l3'];
                }
            }
            return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L2)</span>', 'state'=>'pending_l2'];
        }
        return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L1)</span>', 'state'=>'pending_l1'];
    }

    return ['html'=>'<span style="color: orange; font-weight: bold;">Pending (L1)</span>', 'state'=>'pending_l1'];
}
