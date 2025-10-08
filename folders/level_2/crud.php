<?php

ini_set('display_errors', 1);
error_reporting(E_ALL);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

include '../../config/dbconfig.php';
include '../../config/new_db.php';

// require_once __DIR__ . '/mail.php';

error_log("crud.php loaded\n", 3, "qc_log.txt");

ob_start();

$table              = "purchase_order";
$sub_list_table     = "purchase_order_items";
$documents_upload   = 'po_uploads';

$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

$action             = $_POST["action"];
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
        $company_query = $pdo->select(["company_creation", ["company_name"]], ["unique_id" =>$company_id, "is_delete" => 0]);
        $company_name = ($company_query->status && !empty($company_query->data)) ? $company_query->data[0]["company_name"] : "NA";
        $company_name_clean = preg_replace('/[^A-Za-z0-9]/', '', $company_name);

        // Check if record exists
        $exists = $pdo->select([$table, ["COUNT(*) AS count", "purchase_order_no"]], "unique_id = '$unique_id' AND is_delete = 0");

        // Generate PO Number only on create
        if (!$exists->status || !$exists->data[0]["count"]) {
            $po_prefix = "PO/$company_name_clean/$acc_year/";
            $serial_query = "SELECT COUNT(*) AS po_count FROM $table WHERE sess_company_id = '$company_id' AND is_delete = 0";
            $serial_result = $pdo->query($serial_query);
            $next_serial = str_pad(($serial_result->data[0]["po_count"] ?? 0) + 1, 3, "0", STR_PAD_LEFT);
            $purchase_order_no = $po_prefix . $next_serial;
        } else {
            $po_number = $exists->data[0]["purchase_order_no"];  // Fetch PO number from the existing record
            $msg = "update";
        }
        $lvl2_status = $_POST["appr_status"];

        $columns = [
            "lvl_2_net_amount"          => $_POST["net_amount"],
            "lvl_2_gross_amount"        => $_POST["gross_amount"],
            
            "lvl_2_status"              => $lvl2_status,
            "lvl_2_reason"              => $_POST["cancelReason"],

            "lvl_2_user_id"             => $user_id,
            "lvl_2_created_dt"          => $date
        ];
       // 🛑 IF LEVEL 2 CANCEL FLOW
if ($lvl2_status == '3') {
    $columns["is_delete"] = 1;
    $action_obj = $pdo->update($table, $columns, ["unique_id" => $unique_id]);

    error_log("✅ L2 PO Cancel approved, resetting PR items...\n", 3, "qc_log.txt");
    error_log("🧾 screen_unique_id: $screen_unique_id\n", 3, "qc_log.txt");

    $pr_ids_stmt = $pdo->select(
        ["purchase_order_items", ["pr_sub_unique_id"]],
        ["screen_unique_id" => $screen_unique_id, "is_delete" => 0]
    );

    error_log("🔍 Fetching PR Item IDs...\n", 3, "qc_log.txt");
    error_log("🔍 Query Status: " . ($pr_ids_stmt->status ? "Success" : "Fail") . "\n", 3, "qc_log.txt");
    error_log("🔍 Result: " . print_r($pr_ids_stmt->data, true) . "\n", 3, "qc_log.txt");

    if ($pr_ids_stmt->status && !empty($pr_ids_stmt->data)) {
        foreach ($pr_ids_stmt->data as $row) {
            $pr_sub_unique_id = $row['pr_sub_unique_id'];
            if (!empty($pr_sub_unique_id)) {
                error_log("🔄 Resetting PR Item: $pr_sub_unique_id\n", 3, "qc_log.txt");
                $item_update = $pdo->update(
                    'purchase_requisition_items',
                    ["po_add_item" => 0],
                    ["unique_id" => $pr_sub_unique_id]
                );
            }
        }
    } else {
        error_log("⚠️ No PR items found to reset.\n", 3, "qc_log.txt");
    }

    $response = [
        "status" => $item_update->status,
        "data"   => ["unique_id" => $unique_id],
        "error"  => $item_update->error,
        "msg"    => "update",
        "sql"    => $item_update->sql
    ];

    error_log("✅ L2 Cancel Final Response: " . print_r($response, true) . "\n", 3, "qc_log.txt");
    echo json_encode($response);
    return; // 💥 Prevent double response
}

// 🟠 NORMAL UPDATE FLOW
if (!$exists->status || !$exists->data[0]["count"]) {
    // Create case (currently skipped/commented)
    // $columns["purchase_order_no"] = $purchase_order_no;
    // $action_obj = $pdo->insert($table, $columns);
    // $msg = "create";
} else {
    $action_obj = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
    $msg = "update";

    if ($action_obj->status) {
        error_log("po_number: " . $po_number . "\n", 3, "qc_log.txt");
        try {
            ob_start();
            $qc_result = into_qc($po_number);
            ob_end_clean();
            error_log("QC Result: " . json_encode($qc_result, JSON_PRETTY_PRINT) . "\n", 3, "qc_log.txt");
        } catch (Throwable $e) {
            ob_end_clean();
            error_log("❌ Exception in into_qc: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n", 3, "qc_log.txt");
        }
    }
}

$response = [
    "status" => $action_obj->status,
    "data"   => ["unique_id" => $unique_id],
    "error"  => $action_obj->error,
    "msg"    => $msg,
    "sql"    => $action_obj->sql
];

error_log("Response JSON: " . json_encode($response, JSON_PRETTY_PRINT) . "\n", 3, "qc_log.txt");
echo json_encode($response);
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
                        $image_path = "../blue_planet_beta/uploads/purchase_order_test/" . trim($image_file);
                        $view_button = "<button type='button' onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'> <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                        </button>";
                        $image_buttons .= $view_button;
                    }
                    $value['file_attach'] = "<td style='text-align:center'>" . $image_buttons . "</td>";
                }

                $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);

                $value['unique_id'] = $btn_delete ;

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


        
        case 'datatable':
        // DataTable Variables
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start  = $_POST['start'];
        $draw   = $_POST['draw'];
        $limit  = ($length == '-1') ? "" : $length;
    
        $data = [];
    
        // Query Variables
        $columns = [
            "@a:=@a+1 s_no", 
            "entry_date",
            "purchase_order_no",
            "company_id",
            "project_id",
            "supplier_id",
            "net_amount",
            "gross_amount",
            "CASE WHEN appr_net_amount IS NULL OR appr_net_amount = 0.00 THEN net_amount ELSE appr_net_amount END AS appr_net_amount",
            "CASE WHEN appr_gross_amount IS NULL OR appr_gross_amount = 0.00 THEN gross_amount ELSE appr_gross_amount END AS appr_gross_amount",
            "lvl_2_net_amount",
            "lvl_2_gross_amount",
            "lvl_3_gross_amount",
            "status",
            "lvl_2_status",
            "lvl_3_status",
            "unique_id",
            "status as edit_status"
        ];
    
        $table_details = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
    
        // WHERE conditions
        $where = "is_delete = '0'";
        $conditions = [];
        
        if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
            if ($_POST['from_date'] == $_POST['to_date']) {
                $conditions[] .= "entry_date = '{$_POST['from_date']}'";
            } else {
                $conditions[] .= "entry_date >= '{$_POST['from_date']}' AND entry_date <= '{$_POST['to_date']}'";
            }
        }
        if (!empty($_POST['company_name'])) {
            $conditions[] .= "company_id = '{$_POST['company_name']}'";
        }
        if (!empty($_POST['project_name'])) {
            $conditions[] .= "project_id = '{$_POST['project_name']}'";
        }
        error_log("post: " . print_r($_POST,true) . "\n", 3, "post.log");
        // Approval Status filter (mapped)
        if (isset($_POST['appr_status']) && $_POST['appr_status'] !== '') {
            switch ($_POST['appr_status']) {
                case 'pending_l1':
                    $conditions[] .= "(status = '0' OR status IS NULL)";
                    break;
                
                case 'pending_l2':
                    $conditions[] .= "status = '1' 
                                     AND appr_gross_amount BETWEEN 300001 AND 1000000
                                     AND (lvl_2_status = '0' OR lvl_2_status IS NULL)";
                    break;
                case 'approved_l2':
                    $conditions[] .= "lvl_2_status = '1' AND gross_amount <= 1000000";
                    break;
                case 'rejected_l2':
                    $conditions[] .= "lvl_2_status = '2'";
                    break;
        
                case 'pending_l3':
                    $conditions[] .= "status = '1' 
                                     AND appr_gross_amount > 1000000 
                                     AND lvl_2_status = '1' 
                                     AND lvl_2_gross_amount > 1000000
                                     AND (lvl_3_status = '0' OR lvl_3_status IS NULL)";
                    break;
                case 'approved_l3':
                    $conditions[] .= "lvl_3_status = '1'";
                    break;
                case 'rejected_l3':
                    $conditions[] .= "lvl_3_status = '2'";
                    break;
            }
        } else {
            error_log("true" . "\n", 3, "check_appr.log");
            $conditions[] = "status = '1' AND appr_gross_amount > 300000";
            error_log("cond: " . print_r($conditions, true) . "\n", 3, "cond.log");
        }
    
        if (!empty($conditions)) {
            $where .= " AND " . implode(" AND ", $conditions);
        }
        
        error_log("where: " . $where . "\n", 3, "where.log");
    
        // Ordering and searching
        $order_column = $_POST["order"][0]["column"];
        $order_dir    = $_POST["order"][0]["dir"];
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);
    
        $search       = datatable_searching($search, $columns);
        if ($search) {
            $where .= " AND " . $search;
        }
        
        $sql_function  = "SQL_CALC_FOUND_ROWS";
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();
        
        if ($result->status) {
            $res_array = $result->data;
    
            foreach ($res_array as $key => $value) {
                // Format entry_date
                $value['entry_date'] = (!empty($value['entry_date']) && $value['entry_date'] != "0000-00-00")
                    ? disdate($value['entry_date'])
                    : '-';
    
                // Replace IDs with names
                $company_data = company_name($value['company_id']);
                $value['company_id'] = $company_data[0]['company_name'];
                
                $supplier_data = supplier($value['supplier_id']);
                $value['supplier_id'] = $supplier_data[0]['supplier_name'];
                
                $project_data = project_name($value['project_id']);
                $value['project_id'] = $project_data[0]['project_code'] . " / " . $project_data[0]['project_name'];
                
                $raw_id = $res_array[$key]['unique_id'];
    
                // Buttons
                $btn_update = btn_update($folder_name, $raw_id);
                $btn_delete = btn_delete($folder_name, $raw_id);
                $btn_upload = btn_docs($folder_name, $raw_id);
                $btn_view   = btn_views($folder_name, $raw_id);
                $btn_print  = btn_prints($folder_name, $raw_id);
    
                // Unified Approval Status
                $approval = get_final_approval_status($value);
                $value['approval_status'] = $approval['html'];
                $approval_state           = $approval['state'];
                $approval_view            = $approval['view'] ?? '';
    
                // 🚫 Skip rows still pending at L1
                if ($approval_state == 'pending_l1' || $approval_view == 'approved_l1') {
                    continue;
                }
    
                // Action buttons
                $action_buttons = "";
                if ($approval_state == 'approved_l2' || $approval_state == 'pending_l2') {
                    $action_buttons .= $btn_update;
                }
                $action_buttons .= $btn_delete . $btn_upload;
    
                // Assign final buttons
                $value['view']      = $btn_view;
                $value['print']     = $btn_print;
                $value['unique_id'] = $action_buttons;
    
                $view        = $value['view'];
                $print       = $value['print'];
                $unique_id   = $value['unique_id'];
                $edit_status = $value['edit_status'];
                
                // Clean up unused fields
                unset(
                    $value['view'], $value['print'], $value['unique_id'], $value['edit_status'],
                    $value['status'], $value['lvl_2_status'], $value['lvl_3_status'],
                    $value['lvl_3_gross_amount']
                );
                
                // Re-append UI fields
                $value['view']        = $view;
                $value['print']       = $print;
                $value['unique_id']   = $unique_id;
                $value['edit_status'] = $edit_status;
    
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
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

        

    // Sublist Add/Update (by screen_unique_id)
    case "po_sub_add_update":
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id = $_POST["sublist_id"];

        $columns = [
            "lvl_2_quantity"      => $_POST["quantity"],
            "lvl_2_amount"        => $_POST["amount"]
        ];

        if (!empty($sublist_unique_id)) {
            $columns["lvl_2_user_id"]       = $user_id;
            $columns["lvl_2_created_dt"]    = $date;
            $action_obj = $pdo->update($sub_list_table, $columns, ["unique_id" => $sublist_unique_id]);
            $msg = "update";
           
        } else {
            // $columns["unique_id"] = unique_id();
            // $columns["created_user_id"] = $user_id;
            // $columns["created"] = $date;
            // $action_obj = $pdo->insert($sub_list_table, $columns);
            // $msg = "add";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
    break;
        
    case "po_sub_add_update_modal":
        $screen_unique_id     = $_POST["screen_unique_id"];
        $sublist_unique_id    = $_POST["sublist_unique_id"];
        $pr_unique_id         = $_POST["pr_unique_id"];
    
        $columns = [
            "screen_unique_id"        => $screen_unique_id,
            "item_code"               => $_POST["item_code"],
            "quantity"                => $_POST["quantity"],
            "uom"                     => $_POST["uom"],
            "pr_sub_unique_id"    => $pr_unique_id, // ✅ Store in this column
        ];
    
        if (!empty($sublist_unique_id)) {
            $columns["updated_user_id"] = $user_id;
            $columns["updated"]         = $date;
    
            $action_obj = $pdo->update($sub_list_table, $columns, ["unique_id" => $sublist_unique_id]);
    
            $coulum_pr["po_add_item"] = 1;
            $action_pr = $pdo->update('purchase_requisition_items', $coulum_pr, ["unique_id" => $pr_unique_id]);
    
            $msg = "update";
        } else {
            $columns["unique_id"]         = unique_id();
            $columns["created_user_id"]   = $user_id;
            $columns["created"]           = $date;
    
            $action_obj = $pdo->insert($sub_list_table, $columns);
    
            $coulum_pr["po_add_item"] = 1;
            $action_pr = $pdo->update('purchase_requisition_items', $coulum_pr, ["unique_id" => $pr_unique_id]);
    
            $msg = "add";
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
            "appr_quantity",
            "lvl_2_quantity",
            // "CASE WHEN appr_quantity IS NULL OR appr_quantity = 0.00 THEN quantity ELSE appr_quantity END AS quantity",
            "rate",
            "discount_type",
            "discount",
            "tax",
            "CASE WHEN lvl_2_amount IS NULL OR lvl_2_amount = 0.00 THEN appr_amount ELSE lvl_2_amount END AS amount",
            "unique_id"
        ];

        $table_details = ["purchase_order_items, (SELECT @a:=0) AS a", $columns];
        $where = ["screen_unique_id" => $screen_unique_id, "is_delete" => 0];

        $result = $pdo->select($table_details, $where);
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
                $row["item_code"]       = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];
                
                $tax_data               = tax($row["tax"]);
                $row["tax"]             = $tax_data[0]["tax_name"];
                
                $unit_data              = unit($row["uom"]);
                $row["uom"]             = $unit_data[0]["unit_name"];
                
                if($row['appr_quantity'] != '' || $row['appr_quantity'] != 0){
                    $appr_quantity = $row['appr_quantity'];
                } else {
                    $appr_quantity = $row['quantity'];
                }
                
                $row['quantity']        = !empty($row['quantity']) ? round($row['quantity']) : "-";
                
                $row['appr_quantity']   = !empty($appr_quantity) ? round($appr_quantity) : "-";
                
                $row['lvl_2_quantity']  = !empty($row['lvl_2_quantity']) ? round($row['lvl_2_quantity']) : "-";
                
                $row['discount']        = !empty($row['discount']) ? round($row['discount']) : "-";


                $edit                   = btn_edit($btn_prefix, $row["unique_id"]);

                $row["unique_id"] = $edit ;
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
            "appr_quantity",
            "lvl_2_quantity",
            // "CASE WHEN appr_quantity IS NULL OR appr_quantity = 0.00 THEN quantity ELSE appr_quantity END AS quantity",
            "uom",
            "rate",
            "discount_type",
            "discount",
            "tax",
            "CASE WHEN lvl_2_amount IS NULL OR lvl_2_amount = 0.00 THEN appr_amount ELSE lvl_2_amount END AS amount"
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

    // Sublist Delete
case "po_sub_delete":
    $unique_id = $_POST["unique_id"];

    // Step 1: Mark the sublist item as deleted
    $columns = ["is_delete" => 1];
    $where = ["unique_id" => $unique_id];
    $action_obj = $pdo->update($sub_list_table, $columns, $where);

    // Step 2: Fetch pr_sublist_unique_id from the deleted row
    $pr_ref_query = $pdo->select([$sub_list_table, ["pr_sub_unique_id"]], ["unique_id" => $unique_id]);

    if ($pr_ref_query->status && !empty($pr_ref_query->data[0]["pr_sub_unique_id"])) {
        $pr_sublist_unique_id = $pr_ref_query->data[0]["pr_sub_unique_id"];

        // Step 3: If valid, update the original PR item's po_add_item = 0
        $update_pr = $pdo->update("purchase_requisition_items", ["po_add_item" => 0], ["unique_id" => $pr_sublist_unique_id]);
    }

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $action_obj->status ? "delete_success" : "delete_error",
        "error"  => $action_obj->error,
        "sql"    => $action_obj->sql
    ]);
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
                    "uom" => $uom_name
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
        
        $table     = "purchase_requisition_items as sub join purchase_requisition as main on main.unique_id = sub.main_unique_id ";
        $columns   = [
            "main.pr_number AS pr_number",
            "sub.item_code",
            "sub.item_description",
            "sub.new_quantity",
            "sub.uom",
            "sub.required_delivery_date",
            "sub.unique_id",
            "sub.item_code as item_id",
        ];
        
        $where     = "sub.main_unique_id != '' and  main.company_id ='".$company_id."' and  main.project_id ='".$project_id."' and main.is_delete = 0 and sub.is_delete = 0 and sub.po_add_item = 0 and sub.status = 1";

        $result = $pdo->select([$table, $columns], $where);
       // print_r($result);
        if ($result->status) {

            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>#</th><th>PR Number</th><th>Item</th><th>Item Description</th><th>Qty</th><th>UOM</th><th>Delivery Date</th><th>Action</th></tr></thead><tbody>";
            $i = 1;
            foreach ($result->data as $row) {
                $item_data = item_name_list($row["item_code"]);
                $row["item_code"] = isset($item_data[0]["item_name"]) && isset($item_data[0]["item_code"])
                    ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
                    : "-";
                    $unit_details = unit_name($row['uom']);
                    $unit = $unit_details[0]['unit_name'];
                    $delivery_date = disdate($row['required_delivery_date']);
                echo "<tr>";
                echo "<td>{$i}</td>";
                echo "<td>{$row['pr_number']}</td>";
                echo "<td>{$row['item_code']}</td>";
                echo "<td>{$row['item_description']}</td>";
                echo "<td>{$row['new_quantity']}</td>";
                echo "<td>{$unit}</td>";
                echo "<td>{$delivery_date}</td>";
                echo "<td> <button id= 'sub_add' class='btn btn-success po_sublist_add_modal_btn' 
                onclick=\"po_sublist_add_update_pop_up('{$row['item_id']}','{$row['uom']}', '{$row['new_quantity']}','{$row['unique_id']}')\">Add</button>&nbsp;<button class='btn btn-danger'> Cancel</button></td>";
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

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
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

/* ------------------------------------------------------------------
   switch ($action) { … }  ← keep your existing switch
------------------------------------------------------------------ */
case 'sendmail': 
    $unique_id = $_POST['unique_id'] ?? '';

    $table = 'purchase_order';
    $columns = [
        'purchase_order_no',
        'entry_date',
        'shipping_address',
        'billing_address',
        'supplier_id',
        'project_id',
        'company_id'
    ];
    $table_details = [$table, $columns];
    $where = ['unique_id' => $unique_id];

    $po_details = $pdo->select($table_details, $where);
    error_log("po_details: " . print_r($po_details, true) . "\n", 3, 'error_log_mail.txt');

    if (
        !$po_details ||
        empty($po_details->data) ||
        !isset($po_details->data[0])
    ) {
        echo json_encode(['success' => false, 'message' => 'PO not found']);
        break;
    }

    $row = $po_details->data[0];

    $po_number = $row['purchase_order_no'] ?? 'N/A';
    $entry_date = $row['entry_date'] ?? 'N/A';
    $shipping_address = $row['shipping_address'] ?? '';
    $billing_address = $row['billing_address'] ?? '';

    $html_body = "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <title>PO Details</title>
        </head>
        <body>
            <p><strong>TO:</strong> Gunvant Bearings<br>
            Gunvant Complex, 7/190, Addis Street, Grey Town, Coimbatore-641018.</p>
            
            <p>Dear Sir/Madam,</p>
            
            <p>Greetings from <strong>Zigma Global Environ Solutions Pvt Ltd.</strong><br>
            With reference to the above subject, please find the attached PO details.</p>
            
            <table cellspacing='0' cellpadding='4' border='1'>
                <tr><th align='left'>PO No.</th><td>{$po_number}</td></tr>
                <tr><th align='left'>Date</th><td>{$entry_date}</td></tr>
            </table>
            
            <p><strong>Despatch and Billing Address:</strong><br>{$shipping_address}</p>
            <p><strong>GST No:</strong> None</p>
            
            <p>Goods shall be despatched to the above address as confirmed by us prior to despatch.<br>
            A copy of the despatch particulars shall be marked to:</p>
            
            <p>
                M/s. Zigma Global Environ Solutions Pvt. Ltd.,<br>
                24, Kalaimagal Kalvinilayam Road,<br>
                Erode, Tamil Nadu – 638001.<br>
                E‑mail: <a href='mailto:purchase@zigma.in'>purchase@zigma.in</a><br>
                Mobile: 9384062731
            </p>
            
            <p>If you have any queries, please contact us.</p>
            <p>Thanks & Regards,<br><em>This is an auto‑generated mail.</em></p>
        </body>
        </html>";

    error_log("HTML Body: " . $text_body . "\n", 3, 'error_log_mail.txt');

    // plain-text version
    $plain_body = html_entity_decode(strip_tags(
        preg_replace('/<br\s*\/?>/i', "\n", $html_body)
    ));

    $mail = new PHPMailer(true);
        
        try {
            //Server settings
            $mail->SMTPDebug  = 2;               // verbose client/server dialog
            $mail->Debugoutput = function ($str, $level) {
                error_log("SMTP: $str\n", 3, 'error_log_mail.txt');
            };
            $mail->isSMTP();
            $mail->Host       = 'zigma.in'; // ✅ this one works
            $mail->SMTPAuth   = true;
            $mail->Username   = 'test@zigma.in';
            $mail->Password   = 'r{AOAhKdRyIX'; // 🔒 Use the actual password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // ✅ SSL encryption
            $mail->Port       = 465; // ✅ SSL port

        
            //Recipients
            $mail->setFrom('test@zigma.in', 'Mailer');
            $mail->addAddress('recieve@zigma.in', 'Joe User');

            $mail->isHTML(true);
            $mail->Subject = 'PO Details';
            $mail->Body = $html_body;
            $mail->AltBody = $plain_body;

            $mail_sent = $mail->send();
            error_log("Mail Sent Status: " . ($mail_sent ? 'Success' : 'Failed') . "\n", 3, 'error_log_mail.txt');
        } catch (Exception $e) {
            error_log("Mail Error: " . $mail->ErrorInfo . "\n", 3, 'error_log_mail.txt');
            $mail_sent = false;
        }

    error_log("Mail Sent Status: " . ($mail_sent ? 'Success' : 'Failed') . "\n", 3, 'error_log_mail.txt');

    echo json_encode(['message' => $mail_sent]);
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


function fetch_qc_number($table)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the grn_number)
    $table_columns = [
        "qc_number"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,  // Specify the table name
        $table_columns  // Specify the columns to fetch
    ];

    // Perform the query (assuming your PDO object has a select() method)
    $result = $pdo->select($table_details);

    $qc_numbers = [];

    // Check if the query was successful and if data is returned
    if ($result->status && !empty($result->data)) {
        // Loop through the data and collect all the grn_number values
        foreach ($result->data as $row) {
            $qc_numbers[] = $row['qc_number'];
        }
        error_log($qc_numbers . "\n", 3, "qc_log.txt");
        return $qc_numbers;
    }
}

function generateQC($label, &$labelData) {
    $year = $_SESSION['acc_year'];
    $number = 1;

    do {
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        $qc = "QC/$label/$year/$paddedNumber";
        $number++;
    } while (in_array($qc, $labelData));

    // Optionally store the new GRN
    $labelData[] = $qc;

    return $qc;
}

// function into_qc($po_number){
//     // Global PDO variable
//     global $pdo;

//     // Table, columns, and where condition
//     $table = "purchase_order";

//     $columns = [
//         "po_number",
//         "company_id",
//         "screen_unique_id"
//     ];

//     $table_details = [
//         $table,
//         $columns
//     ];

//     $where = [
//         "lvl_2" => 1,
//         "purchase_order_no" => $po_number   
//     ];  // Filter for lvl_2 = 1

//     // Fetch po_number and screen_unique_id using the custom PDO select method
//     $po_data = $pdo->select($table_details, $where);

//     $po_number = '';
//     $screen_unique_id = '';
//     $company_id = '';

//     // Check if data was fetched successfully
//     if (empty($po_data)) {
//         echo json_encode(["status" => "error", "message" => "PO not found"]);
//     } else {
//         // Assuming we have the data
//         $po_number = $po_data[0]['po_number'];  // Get the first row's po_number
//         $screen_unique_id = $po_data[0]['screen_unique_id'];  // Get the first row's screen_unique_id
//         $company_id = $po_data[0]['company_id'];  // Get the first row's screen_unique_id
//     }

//     $table2 = "purchase_order_items";

//     $columns2 = [
//         "item_code",
//         "qty",
//         "uom"
//     ];

//     $table_details2 = [
//         $table2,
//         $columns2
//     ];

//     $where2 = [
//         "screen_unique_id" => $screen_unique_id
//     ];

//     $po_sub_data = $pdo->select($table_details2, $where2);

//     $item_id = '';
//     $quantity = '';
//     $uom = '';

//     // Check if data was fetched successfully
//     if (empty($po_sub_data)) {
//         echo json_encode(["status" => "error", "message" => "PO items not found"]);
//     } else {
//         // Assuming we have the data
//         $item_id = $po_sub_data[0]['item_code'];
//         $quantity = $po_sub_data[0]['quantity'];  
//         $uom = $po_sub_data[0]['uom'];  
//     }

//     $table3 = "item_master";

//     $columns3 = [
//         "item_code",
//         "description"
//     ];

//     $table_details3 = [
//         $table3,
//         $columns3
//     ];

//     $where3 = [
//         "qc_approval" => 1,
//         "unique_id"   => $item_id
//     ];

//     $item_data = $pdo->select($table_details3, $where3);

//     $item_code   = '';
//     $description = '';

//     // Check if data was fetched successfully
//     if (empty($item_data)) {
//         echo json_encode(["status" => "error", "message" => "PO items not found"]);
//     } else {
//         // Assuming we have the data
//         $item_code   = $item_data[0]['item_code'];
//         $description = $item_data[0]['description'];

//         $unique_id = unique_id();

//         $table4 = "qc_approval";

//         $labelData = [];
//         $labelData = fetch_grn_number($table4);

//         $comapny_name = company_name($company_id);
        
//         $qc_number = generateQC($company_name, $labelData);

//         $insert_data = [
//             "unique_id"             => $unique_id,
//             "company_id"            => $company_id,
//             "po_number"             => $po_number,
//             "item_code"             => $item_code,
//             "quantity"              => $quantity,
//             "uom"                   => $uom,
//             "item_description"      => $description,
//             "qc_number"             => $qc_number
//         ];

//         $qc_insert = $pdo->insert($table4, $insert_data, true);

//         if ($qc_insert) {
//             echo "Insert successful. New ID: " . $qc_insert;  // Assuming the function returns the new inserted ID
//         } else {
//             echo "Insert failed.";
//         }
//     }

// }

function into_qc($input_po_number) {
    global $pdo;
    error_log("Received PO Number: {$input_po_number}\n", 3, "qc_log.txt");

    // STEP 1: Fetch main PO
    $rawPo = $pdo->select(
        ["purchase_order", ["purchase_order_no","company_id","project_id","screen_unique_id","lvl_2_status"]],
        ["lvl_2_status" => '1', "purchase_order_no" => $input_po_number]
    );
    error_log("Raw PO result: " . print_r($rawPo, true) . "\n", 3, "qc_log.txt");
    $poData = (is_object($rawPo) && isset($rawPo->data)) ? $rawPo->data : [];
    if (empty($poData)) {
        error_log("❌ PO not found: {$input_po_number}\n", 3, "qc_log.txt");
        echo json_encode(["status"=>"error","message"=>"PO not found"]);
        return;
    }
    $po_number        = $poData[0]['purchase_order_no'];
    $company_id       = $poData[0]['company_id'];
    $project_id       = $poData[0]['project_id'];
    $screen_unique_id = $poData[0]['screen_unique_id'];
    error_log("✅ PO fetched: {$po_number} / Company ID: {$company_id} / Screen ID: {$screen_unique_id}\n", 3, "qc_log.txt");

    // STEP 2: Fetch PO items
    $rawItems = $pdo->select(
        ["purchase_order_items", ["item_code","quantity","uom"]],
        ["screen_unique_id" => $screen_unique_id]
    );
    error_log("Raw PO-items result: " . print_r($rawItems, true) . "\n", 3, "qc_log.txt");
    $itemList = (is_object($rawItems) && isset($rawItems->data)) ? $rawItems->data : [];
    if (empty($itemList)) {
        error_log("❌ No items for Screen ID: {$screen_unique_id}\n", 3, "qc_log.txt");
        echo json_encode(["status"=>"error","message"=>"PO items not found"]);
        return;
    }
    $item_id  = $itemList[0]['item_code'];
    $quantity = $itemList[0]['quantity'];
    $uom      = $itemList[0]['uom'];
    error_log("✅ Item fetched: Code={$item_id}, Qty={$quantity}, UOM={$uom}\n", 3, "qc_log.txt");

    // STEP 3: Fetch item master
    $rawItem = $pdo->select(
        ["item_master", ["item_code","description"]],
        ["qc_approval" => 1, "unique_id" => $item_id]
    );
    error_log("Raw item-master result: " . print_r($rawItem, true) . "\n", 3, "qc_log.txt");
    $itemData = (is_object($rawItem) && isset($rawItem->data)) ? $rawItem->data : [];
    if (empty($itemData)) {
        error_log("❌ Master record missing for Item ID: {$item_id}\n", 3, "qc_log.txt");
        echo json_encode(["status"=>"error","message"=>"Item not found"]);
        return;
    }
    $item_code   = $itemData[0]['item_code'];
    $description = $itemData[0]['description'];
    error_log("✅ Master item: Code={$item_code}, Desc={$description}\n", 3, "qc_log.txt");

    // STEP 4: QC-number generation
    try {
        $unique_id = unique_id();
        error_log("Generated unique_id: {$unique_id}\n", 3, "qc_log.txt");

        $labelData = [];

        // 1) Fetch QC label data
        error_log(">> Calling fetch_qc_number('qc_approval')\n", 3, "qc_log.txt");
        $rawLabel = fetch_qc_number("qc_approval");
        error_log("<< raw fetch_qc_number response: " . print_r($rawLabel, true) . "\n", 3, "qc_log.txt");
        
        // 2) Resolve company name
        error_log(">> Calling company_name({$company_id})\n", 3, "qc_log.txt");
        $companyRows = company_name($company_id);
        error_log("<< Raw company_name() response: " . print_r($companyRows, true) . "\n", 3, "qc_log.txt");

        if (is_array($companyRows)
            && isset($companyRows[0]['company_name'])
            && $companyRows[0]['company_name'] !== ''
        ) {
            $company_name = $companyRows[0]['company_name'];
        } else {
            throw new Exception("company_name() returned invalid or empty data");
        }
        error_log("Resolved company_name: '{$company_name}'\n", 3, "qc_log.txt");

        // 3) Generate the QC number
        error_log(">> Calling generateQC('{$company_name}', labelData)\n", 3, "qc_log.txt");
        $qc_number = generateQC($company_name, $rawLabel);
        if (empty($qc_number)) {
            throw new Exception("generateQC returned empty");
        }
        error_log("<< QC number: '{$qc_number}'\n", 3, "qc_log.txt");
        error_log("✅ Passed QC-generation\n", 3, "qc_log.txt");

    } catch (Exception $e) {
        error_log("❌ QC-generation failed: " . $e->getMessage() . "\n", 3, "qc_log.txt");
        echo json_encode(["status" => "error", "message" => "QC gen failed: " . $e->getMessage()]);
        return;
    }

    // STEP 5: Build & insert
    $insert_data = [
        "unique_id"         => $unique_id,
        "company_id"        => $company_id,      // NOTE schema typo
        "project_id"        => $project_id,      // NOTE schema typo
        "po_number"         => $po_number,
        "item_code"         => $item_code,
        "quantity"          => (int)$quantity,   // cast to INT
        "uom"               => $uom,
        "item_description"  => $description,
        "qc_number"         => $qc_number
    ];
    error_log("Final insert_data: " . print_r($insert_data, true) . "\n", 3, "qc_log.txt");

    $newId = $pdo->insert("qc_approval", $insert_data, true);
    error_log("newId: " . print_r($newId, true) . "\n", 3, "qc_log.txt");

    if ($newId) {
        error_log("✅ Insert successful. New ID: {$newId}\n", 3, "qc_log.txt");
        echo json_encode(["status"=>"success","new_id"=>$newId]);
    } else {
        error_log("❌ Insert failed.\n", 3, "qc_log.txt");
        echo json_encode(["status"=>"error","message"=>"Insert failed"]);
    }
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
        return ['html'=>'<span style="color: green; font-weight: bold;">Approved (L1)</span>', 'state'=>'approved', 'view'=>'approve_l1'];
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
