<?php 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = 'srn'; 
$sub_table          = 'srn_sublist'; 
$documents_upload   = 'srn_uploads';

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
        $approve_status = $_POST['approve_status'];
        $status_remark  = $_POST['status_remark'];
        $sess_user_id   = $_POST['sess_user_id'];
        $unique_id      = $_POST['unique_id'];

        // Set base response
        $response = [
            "status" => false,
            "data"   => ["unique_id" => $unique_id],
            "error"  => null,
            "msg"    => "Unknown error"
        ];

        // First, check if the record exists and is not deleted
        $check_query = [$table, ["COUNT(*) AS count"]];
        $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

        $check_result = $pdo->select($check_query, $check_where);

        if (!$check_result->status || $check_result->data[0]['count'] == 0) {
            $response["error"] = "Invalid SRN record";
            $response["msg"]   = "Record not found";
            echo json_encode($response);
            break;
        }

        // === CANCEL ===
        if ($approve_status == 3) {
            $columns = ["is_delete" => 1];
            $update_where = ["unique_id" => $unique_id];

            $update_result = $pdo->update($table, $columns, $update_where);

            $response["status"] = $update_result->status;
            $response["error"]  = $update_result->error;
            $response["msg"]    = $update_result->status ? "SRN cancelled successfully" : "Failed to cancel SRN";
            $response["sql"]    = $update_result->sql ?? '';

            echo json_encode($response);
            break;
        }

        // === APPROVE / REJECT ===
        $columns = [
            "check_status"    => $approve_status,
            "check_remarks"   => $status_remark,
            "checked_by"      => $sess_user_id,
            "updated_user_id" => $user_id,
            "updated"         => $date
            ];

        $update_where = ["unique_id" => $unique_id];
        $update_result = $pdo->update($table, $columns, $update_where);

        $response["status"] = $update_result->status;
        $response["error"]  = $update_result->error;
        $response["msg"]    = $update_result->status ? "SRN updated successfully" : "Failed to update SRN";
        $response["sql"]    = $update_result->sql ?? '';

        echo json_encode($response);
    break;

case "update_qty":
    $screen_unique_id = $_POST['screen_unique_id'];
    $is_update = $_POST['is_update'];
    
    // Check if the screen_unique_id is provided
    if (!empty($screen_unique_id)) {

        $po_unique_id_data = fetch_po_unique_id($sub_table, $screen_unique_id);
        $po_unique_id = is_array($po_unique_id_data) ? $po_unique_id_data[0]["po_unique_id"] ?: null : $po_unique_id_data;

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
                "IFNULL(srn_sub.total_received_qty, 0) AS prev_received_qty"
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
                ) AS srn_sub 
                ON gs.item_code = srn_sub.item_code 
                AND gs.po_unique_id = srn_sub.po_unique_id",
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
            $target_dir = "../../uploads/srn/";
            $folder_path = "srn_new/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES["test_file"]["name"] as $key => $name) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                finfo_close($finfo);
                
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                error_log("Detected MIME: $mime_type\n", 3, "doc_logs_1.txt");
                error_log("Detected ext: $file_extension\n", 3, "doc_logs_1.txt");

               $allowed_mime_types = [
                // Images
                'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
            
                // Documents
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'text/plain',
            
                // Excel
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/x-msexcel',
                'application/xls',
                'application/octet-stream', // fallback
            
                // CSV
                'text/csv'
            ];
                
                if (!in_array($file_extension, $allowed_exts) && !in_array($mime_type, $allowed_mime_types)) {
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
            "srn_unique_id"              => $upload_unique_id,
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
            "srn_unique_id" => $upload_unique_id,
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
                        $image_path = "../blue_planet_erp/uploads/srn/" . trim($image_file);
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


    case 'entry_date':

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
        $status = isset($_POST['status']) ? $_POST['status'] : '';

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
            "srn_number",
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
        
        if($status != ''){
            $where = " is_delete = '0' AND entry_date >= '$from' AND entry_date <= '$to' AND check_status = $status";
        } else {
            $where = " is_delete = '0' AND entry_date >= '$from' AND entry_date <= '$to'";
        }
         
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

        error_log("sql: " . print_r($result, true) . "\n", 3, "logs/datatable_init.txt");

        error_log("result: " . $result->sql . "\n", 3, "logs/sql_error_log.txt");
        
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $srn_no = $value['srn_number'];

                $srn_check_status_arr = fetch_srn_status($srn_no);
                $srn_check_status = isset($srn_check_status_arr[0]['check_status']) ? $srn_check_status_arr[0]['check_status'] : '';
                $srn_approve_status = isset($srn_check_status_arr[0]['approve_status']) ? $srn_check_status_arr[0]['approve_status'] : '';

                error_log("srn_check_status: " . print_r($srn_check_status_arr, true) . "\n", 3, "logs/srn_check_status_log.txt");
                
                $company_data = company_name($value['company_id']);
                $value['company_id'] = $company_data[0]['company_name'];

                $project_options = get_project_name($value['project_id']);
                $value['project_id'] = $project_options[0]['label'];

                $project_code = $project_options[0]['project_code'] ?: '';
                error_log("project_code: " . print_r($project_options, true) . "\n", 3, "logs/project_code_log.txt");

                $purchase_order_no = get_po_number($value['po_number']);
                error_log("po_number 1: " . print_r($puchase_order_no, true) . "\n", 3, "logs/project_code_log.txt");
                $value['po_number'] = $purchase_order_no[0]['purchase_order_no'] ? $purchase_order_no[0]['purchase_order_no'] : '';
                error_log("po_number: " . $value['po_number'] . "\n", 3, "logs/project_code_log.txt");


                $supplier_names = supplier($value['supplier_name']);
                $value['supplier_name'] = isset($supplier_names[0]['supplier_name']) ? $supplier_names[0]['supplier_name'] : '';
                error_log("supplier_name: " . $value['supplier_name'] . "\n", 3, "logs/project_code_log.txt");
                // Button and status logic
                $status = '';
                $btns = '';
                $is_admin = isset($_SESSION['sess_user_type']) && $_SESSION['sess_user_type'] == $admin_user_type;
                
                $btn_view  = btn_views($folder_name, $value['unique_id']);
                $btn_print = btn_prints($folder_name, $value['unique_id']);
                $btn_upload = btn_docs($folder_name, $value['unique_id']);

                 if ($srn_check_status == 1 && $srn_approve_status == 0) {
                    $status = '<span class="text-success fw-bold">Checked</span>';
                    // No update/delete buttons if checked
                    $btns = '';
                } elseif ($srn_check_status == 1 && $srn_approve_status == 2) {
                    $status = '<span class="text-danger fw-bold">Approval Rejected</span>';
                    // No update/delete buttons if checked
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $btns = $btn_update . $btn_delete;
                } elseif ($srn_approve_status == 1) {
                    $status = '<span class="text-success fw-bold">Approved</span>';
                    // Only show delete button if admin
                    $btns = '';
                } elseif ($srn_check_status == 2 && $srn_approve_status == 2) {
                    $status = '<span class="text-danger fw-bold">Check & Approval Rejected</span>';
                    // No update/delete buttons if checked
                    $btns = '';
                } elseif ($srn_check_status == 2 && $srn_approve_status == 0) {
                    $status = '<span class="text-danger fw-bold">Check Rejected</span>';
                    // No update/delete buttons if checked
                    $btns = '';
                } else {
                    // Only show buttons if not checked or approved
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $btns = $btn_update . $btn_delete;
                    $status = '<span class="text-warning fw-bold">Pending</span>';
                }

                // $btn_view = btn_info($folder_name, $value['unique_id']);
                $btns = $btns . $btn_upload;

                // Prepare row as indexed array
                $row = array_values($value); // converts associative to numeric
                
                // Ensure at least 9 fields exist before setting index 9 and 10
                while (count($row) < 9) {
                    $row[] = ''; // Fill with empty values to prevent undefined index
                }
                
                // Insert project_code after project_name (which is at index 2)
                // So insert at index 3
                // array_splice($row, 3, 0, $project_code); // now project_code is at index 3
                
                // Again ensure now at least 11 fields (0-10) exist
                while (count($row) < 10) {
                    $row[] = '';
                }
                
                // Set status and buttons at index 9 and 10 (after adding project_code earlier)
                $row[8] = $status;
                $row[9] = $btns;
                
                array_splice($row, 9, 0, [$btn_view, $btn_print]);
                
                // Finalize
                $data[] = $row;

            }

            error_log("data: " . print_r($data, true) . "\n", 3, "logs/data_log.txt");
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data
                // "testing"           => $result->sql
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
    $unique_id = $_POST['unique_id'] ?: '';

    if (empty($unique_id)) {
        echo json_encode([
            "status" => false,
            "msg" => "",
            "error" => "Missing unique_id"
        ]);
        break;
    }

    $screen_unique_id = fetch_srn_sc_unique_id($unique_id);
    $po_unique_id = fetch_po_unique_id($sub_table, $screen_unique_id);
    $po_unique_id = is_array($po_unique_id) ? $po_unique_id[0]["po_unique_id"] : $po_unique_id;

    $srn_unique_id = $unique_id;

    if (!empty($srn_unique_id)) {
        $srn_data = fetch_srn_data($srn_unique_id);
        $freight_value         = $srn_data[0]['freight'] ?: 0;
        $freight_tax           = $srn_data[0]['gst_freight'] ?: 0;
        $other_charges         = $srn_data[0]['other'] ?: 0;
        $other_tax             = $srn_data[0]['gst_other'] ?: 0;
        $packing_forwarding    = $srn_data[0]['paf'] ?: 0;
        $packing_forwarding_tax= $srn_data[0]['gst_paf'] ?: 0;
        $round_off             = $srn_data[0]['round'] ?: 0;
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
        "IF('$po_unique_id' = 0, 0, COALESCE(srn_sub.total_received_qty, 0)) AS now_received_qty",
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
                FROM srn_sublist as gs2
                LEFT JOIN srn as g ON g.screen_unique_id = gs2.screen_unique_id
                WHERE gs2.po_unique_id = '$po_unique_id' 
                AND gs2.screen_unique_id = '$screen_unique_id' 
                AND gs2.is_delete = 0 
                AND g.is_delete = 0
                GROUP BY gs2.item_code, gs2.po_unique_id
            ) AS srn_sub 
            ON gs.item_code = srn_sub.item_code 
            AND gs.po_unique_id = srn_sub.po_unique_id
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
    error_log("SQL: " . $result->sql . "\n", 3, "logs/srn_info_sql_log.txt");

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
            $row["uom"] = $uom_data[0]["unit_name"] ?: $row["uom"];
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
            error_log("Row: " . print_r($row, true) . "\n", 3, "logs/srn_info_row_log.txt");
        }
    }

    $iframe_src = "/blue_planet_beta/blue_planet_beta/index.php?file=srn/srn_sublist_iframe&unique_id=" . urlencode($unique_id);

    error_log("iframe_src: " . $iframe_src . "\n", 3, "logs/srn_info_iframe_log.txt");
    error_log("response: " . print_r($data, true) . "\n", 3, "srn_info_data_log.txt");

    if (!$result->status) {
        error_log("Error in info case: " . $result->error . "\n", 3, "logs/srn_info_error_log.txt");
    }
    $response = [
        "status" => $result->status,
        "msg" => $result->status ? "Iframe loaded successfully" : "Failed to load info",
        "iframe_src" => $iframe_src,
        "error" => $result->status ? "" : $result->error,
        "data" => $data,
        "total" => $total_amount
    ];
    error_log("srn info response: " . json_encode($response) . "\n", 3, "logs/srn_info_response_log.txt");
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
        
        
    case "srn_sub_add_update":

        $now_received_qty = 0;
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id      = $_POST["sublist_unique_id"];
        
        $item_code      = $_POST["item_code"];
        $order_qty              = $_POST["order_qty"];
        $uom                    = $_POST["uom"];
        $now_received_qty       = $_POST["tot_qty"];
        $update_qty             = $_POST["update_qty"];
        $remarks                = $_POST["remarks"];
        
        error_log("POST: " . print_r($_POST, true) . "\n", 3, "post_log.txt");
       
        $columns = [
            "srn_main_unique_id"    => $unique_id, // Use actual form's unique_id if needed
            "screen_unique_id"      => $screen_unique_id,
            "item_code"             => $item_code,
            "order_qty"             => $order_qty,
            "uom"                   => $uom,
            "now_received_qty"      => $now_received_qty,
            "remarks"               => $remarks,
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

    case "srn_sublist_datatable":
        $screen_unique_id = $_POST["screen_unique_id"];
        error_log("screen_unique_id: " . $screen_unique_id . "\n", 3, "logs/srn_sublist_datatable_log.txt");
        $btn_prefix = "srn_sub";
        $is_update = isset($_POST['is_update']) ? $_POST['is_update'] : false;

        $srn_unique_id = $_POST['unique_id'];

        $po_unique_id = fetch_po_unique_id($sub_table, $screen_unique_id);
        $po_unique_id = is_array($po_unique_id) ? $po_unique_id[0]["po_unique_id"] : $po_unique_id;
        $unique_id = fetch_unique_id($sub_table, $screen_unique_id);
        $unique_id = is_array($unique_id) ? $unique_id[0]["unique_id"] : $unique_id;

        // If no srn unique id, set all PO charges fields to 0
        if (empty($srn_unique_id)) {
            // No srn unique id, get PO charges fields from PO
            $po_data = fetch_po_data($po_unique_id);
            error_log("po_data: " . print_r($po_data, true) . "\n", 3, "logs/po_data_log.txt");
            $freight_value         = isset($po_data[0]['freight_value']) && $po_data[0]['freight_value'] !== '' ? $po_data[0]['freight_value'] : 0;
            $freight_tax           = isset($po_data[0]['freight_tax']) && $po_data[0]['freight_tax'] !== '' ? $po_data[0]['freight_tax'] : 0;
            $other_charges         = isset($po_data[0]['other_charges']) && $po_data[0]['other_charges'] !== '' ? $po_data[0]['other_charges'] : 0;
            $other_tax             = isset($po_data[0]['other_tax']) && $po_data[0]['other_tax'] !== '' ? $po_data[0]['other_tax'] : 0;
            $packing_forwarding    = isset($po_data[0]['packing_forwarding']) && $po_data[0]['packing_forwarding'] !== '' ? $po_data[0]['packing_forwarding'] : 0;
            $packing_forwarding_tax= isset($po_data[0]['packing_forwarding_tax']) && $po_data[0]['packing_forwarding_tax'] !== '' ? $po_data[0]['packing_forwarding_tax'] : 0;
            $round_off             = isset($po_data[0]['round_off']) && $po_data[0]['round_off'] !== '' ? $po_data[0]['round_off'] : 0;
        } 
        else {
            // srn unique id exists, get charges fields from srn
            $srn_data = fetch_srn_data($srn_unique_id);
            error_log("srn_data: " . print_r($srn_data, true) . "\n", 3, "logs/srn_data_log.txt");
            $freight_value         = isset($srn_data[0]['freight']) && $srn_data[0]['freight'] !== '' ? $srn_data[0]['freight'] : 0;
            $freight_tax           = isset($srn_data[0]['gst_freight']) && $srn_data[0]['gst_freight'] !== '' ? $srn_data[0]['gst_freight'] : 0;
            $other_charges         = isset($srn_data[0]['other']) && $srn_data[0]['other'] !== '' ? $srn_data[0]['other'] : 0;
            $other_tax             = isset($srn_data[0]['gst_other']) && $srn_data[0]['gst_other'] !== '' ? $srn_data[0]['gst_other'] : 0;
            $packing_forwarding    = isset($srn_data[0]['paf']) && $srn_data[0]['paf'] !== '' ? $srn_data[0]['paf'] : 0;
            $packing_forwarding_tax= isset($srn_data[0]['gst_paf']) && $srn_data[0]['gst_paf'] !== '' ? $srn_data[0]['gst_paf'] : 0;
            $round_off             = isset($srn_data[0]['round']) && $srn_data[0]['round'] !== '' ? $srn_data[0]['round'] : 0;
        }

        error_log("po_unique_id: " . $po_unique_id . "\n", 3, "logs/po_unique_id_log.txt");

        $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
        $po_sc_unique_id = is_array($po_sc_unique_id) ? $po_sc_unique_id[0]["screen_unique_id"] : $po_sc_unique_id;

        $td_data = fetch_tax_discount($po_sc_unique_id);

        $tax = $td_data['tax'];
        $tax_name = tax($tax)[0]['tax_name'];
        $tax = tax($tax)[0]['tax_value'];
        $discount = $td_data['discount'];
        $discount_type = $td_data['discount_type'];

        error_log("tax: " . $tax . "\n" . "discount: " . $discount . "\n" . "discount_type: " . $discount_type . "\n", 3, "logs/td_log.txt");

        $total_amount = 0;
        $taxed_val = 0;
        
       $columns = [
        "@a := @a + 1 AS s_no",
        "gs.item_code",
        "gs.order_qty",
        "gs.uom",
        "IF(gs.po_unique_id = '0', 0, COALESCE(srn_sub.total_received_qty, 0)) AS now_received_qty",
        "IF(gs.po_unique_id = '0', 
            gs.order_qty, 
            IF(gs.update_qty IS NULL OR gs.update_qty = 0, 
                GREATEST(gs.order_qty - COALESCE(srn_sub.total_received_qty, 0), 0), 
                gs.update_qty
            )
        ) AS update_qty",
        "poi_items.rate",
        "poi_items.tax AS tax",
        "poi_items.discount_type AS discount_type",
        "poi_items.discount AS discount",
        // Amount calculation block
        "ROUND(
            CASE 
                WHEN poi_items.discount_type = 1 THEN 
                    -- Percentage discount
                    (COALESCE(gs.update_qty, 0) * COALESCE(poi_items.rate, 0)) 
                    * (1 - (COALESCE(poi_items.discount, 0) / 100)) 
                    * 1.12
                WHEN poi_items.discount_type = 2 THEN 
                    -- Flat discount
                    (
                        (COALESCE(gs.update_qty, 0) * COALESCE(poi_items.rate, 0)) 
                        - COALESCE(poi_items.discount, 0)
                    ) * 1.12
                ELSE 
                    -- No discount
                    (COALESCE(gs.update_qty, 0) * COALESCE(poi_items.rate, 0)) * 1.12
            END,
            2
        ) AS amount",
        "COALESCE(gs.remarks, poi_items.item_remarks) AS remarks",
        "gs.unique_id"
    ];

        $pdo->query("SET @a := 0;");

    $table_details = [
        "$sub_table gs 
            LEFT JOIN ( 
                SELECT 
                    gs2.item_code, 
                    gs2.po_unique_id, 
                    SUM(gs2.update_qty) AS total_received_qty
                FROM srn_sublist as gs2
                LEFT JOIN srn as g ON g.screen_unique_id = gs2.screen_unique_id
                WHERE gs2.po_unique_id = '$po_unique_id' 
                AND gs2.screen_unique_id != '$screen_unique_id' 
                AND gs2.is_delete = 0 
                AND g.is_delete = 0
                GROUP BY gs2.item_code, gs2.po_unique_id
            ) AS srn_sub 
            ON gs.item_code = srn_sub.item_code 
            AND gs.po_unique_id = srn_sub.po_unique_id
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
        
        error_log("result: " . print_r($result, true) . "\n", 3, "logs/row_log_new.txt");


        $data = [];
        if ($result->status) {
    foreach ($result->data as $row) {
        $qty           = floatval($row['update_qty']);
        $prev_qty      = floatval($row['now_received_qty']);
        $order_qty     = floatval($row['order_qty']);
        $rate          = floatval($row['rate']);
        $discount      = floatval($row['discount']);
        $discount_type = $row['discount_type'];

        // Get tax data from tax unique_id
        $tax_data = tax($row['tax']);
        $tax_name = $tax_data[0]['tax_name'];
        $tax_val  = floatval($tax_data[0]['tax_value']);

        // Base amount
        $base = $qty * $rate;

        // Discount
        if ($discount_type == 1) {
            $discountAmt = ($base * $discount) / 100;
        } else if ($discount_type == 2) {
            $discountAmt = $discount;
        } else {
            $discountAmt = 0;
        }

        // After discount
        $afterDiscount = $base - $discountAmt;

        // Tax calculation
        $taxAmount = ($afterDiscount * $tax_val) / 100;

        // Final amount
        $finalAmount = $afterDiscount + $taxAmount;

            if ($finalAmount < 0) {
                $finalAmount = 0;
            }
            $row['amount'] = round($finalAmount, 2);

            $total_amount += $afterDiscount;

            error_log("row: " . print_r($row, true) . "\n", 3, "logs/row_logs.txt");

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

            $row['tax'] = $tax_name;



            error_log("discount_type_display: " . $row['discount_type_display'] . "\n", 3, "logs/discount_log.txt");

                $edit = btn_edit($btn_prefix, $row["unique_id"]);
                $del = btn_delete($btn_prefix, $row["unique_id"]);
                $row["unique_id"] = $edit . $del;

                $data[] = array_merge(array_values($row), [
                    'item_code' => $row["item_code"],
                    'order_qty' => $row["order_qty"],
                    'uom' => $row["uom"],
                    'now_received_qty' => $row["now_received_qty"],
                    'update_qty' => $row["update_qty"],
                    'rate' => $row["rate"],
                    'remarks' => $row["remarks"],
                    'tax' => $tax_value,
                    'discount_type' => $row["discount_type"],
                    'discount' => $row["discount"],
                    'discount_type_display' => $row["discount_type_display"],
                    'amount' => $row["amount"],
                    'unique_id' => $row["unique_id"],
                ]);

            }

            echo json_encode([
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data,
                "cur_qty" => $qty,
                "prev_qty" => $prev_qty,
                "order_qty" => $order_qty,
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


    case "srn_sub_edit":
        $unique_id = $_POST["unique_id"];
        $is_update = isset($_POST['is_update']) ? $_POST['is_update'] : false;

        $po_unique_id = fetch_po_unique_id1($sub_table, $unique_id);

        $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
        error_log("sc_un_id: " . print_r($po_sc_unique_id, true) . "\n", 3, "logs/po_sc_log.txt");

        $td_data = fetch_tax_discount($po_sc_unique_id);

        $tax = $td_data['tax'];
        $tax_name = tax($tax)[0]['tax_name'];
        $tax = tax($tax)[0]['tax_value'];
        $discount = $td_data['discount'];
        $discount_type = $td_data['discount_type']; // Get the discount_type value directly

        error_log("tax: " . $tax . "\n" . "discount: " . $discount . "\n" . "discount_type: " . $discount_type . "\n", 3, "logs/td_log.txt");

        $columns = [
        "@a := @a + 1 AS s_no",
        "gs.item_code",
        "gs.order_qty",
        "gs.uom",
        "IF(gs.po_unique_id = '0', 0, COALESCE(srn_sub.total_received_qty, 0)) AS now_received_qty",
        "IF(gs.po_unique_id = '0', 
            gs.order_qty, 
            IF(gs.update_qty IS NULL OR gs.update_qty = 0, 
                GREATEST(gs.order_qty - COALESCE(srn_sub.total_received_qty, 0), 0), 
                gs.update_qty
            )
        ) AS update_qty",
        "poi_items.rate",
        "poi_items.tax AS tax",
        "poi_items.discount_type AS discount_type",
        "poi_items.discount AS discount",
        // Amount calculation block
        "ROUND(
            CASE 
                WHEN poi_items.discount_type = 1 THEN 
                    -- Percentage discount
                    (COALESCE(gs.update_qty, 0) * COALESCE(poi_items.rate, 0)) 
                    * (1 - (COALESCE(poi_items.discount, 0) / 100)) 
                    * 1.12
                WHEN poi_items.discount_type = 2 THEN 
                    -- Flat discount
                    (
                        (COALESCE(gs.update_qty, 0) * COALESCE(poi_items.rate, 0)) 
                        - COALESCE(poi_items.discount, 0)
                    ) * 1.12
                ELSE 
                    -- No discount
                    (COALESCE(gs.update_qty, 0) * COALESCE(poi_items.rate, 0)) * 1.12
            END,
            2
        ) AS amount",
        "COALESCE(gs.remarks, poi_items.item_remarks) AS remarks",
        "gs.unique_id"
    ];

        $table_details = [
        "$sub_table gs 
            LEFT JOIN ( 
                SELECT 
                    gs2.item_code, 
                    gs2.po_unique_id, 
                    SUM(gs2.update_qty) AS total_received_qty
                FROM srn_sublist as gs2
                LEFT JOIN srn as g ON g.screen_unique_id = gs2.screen_unique_id
                WHERE gs2.po_unique_id = '$po_unique_id' 
                AND gs2.unique_id != '$unique_id' 
                AND gs2.is_delete = 0 
                AND g.is_delete = 0
                GROUP BY gs2.item_code, gs2.po_unique_id
            ) AS srn_sub 
            ON gs.item_code = srn_sub.item_code 
            AND gs.po_unique_id = srn_sub.po_unique_id
        LEFT JOIN purchase_order poi 
            ON gs.po_unique_id = poi.unique_id
        LEFT JOIN purchase_order_items poi_items 
            ON poi_items.screen_unique_id = poi.screen_unique_id 
            AND poi_items.item_code = gs.item_code
        WHERE gs.unique_id = '$unique_id' 
        AND gs.is_delete = 0",
        $columns
    ];

        $result = $pdo->select($table_details);
        error_log("result: " . print_r($result, true) . "\n", 3, "logs/row_log.txt");

        if ($result->status) {
            $row = $result->data[0];
            error_log("row: " . print_r($row, true) . "\n", 3, "logs/rows_log.txt");
            
            $tax_data = tax($row['tax']); // assuming 'tax' in row is the unique_id of tax
            error_log("tax_data: " . print_r($tax_data, true) . "\n", 3, "logs/tax.txt");
            $tax_name = $tax_data[0]['tax_name'];
            $tax_value = floatval($tax_data[0]['tax_value']);
            
            $row['tax'] = $tax_name;

    
            
            echo json_encode([
                "status" => true,
                "data"   => $row,
                "tax"    => $tax_value,
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


    
    case "srn_sub_delete":
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
            
        // Fetch all purchase orders
        $purchase_order_options = get_po_number("", $project_id);
        
        // Log original data
        error_log("po: " . print_r($purchase_order_options, true) . "\n", 3, "logs/po_number.txt");
        error_log("po_id: " . $project_id . "\n", 3, "logs/po_number.txt");
        
        // Filter to keep only purchase_order_type = '683568ca2fe8263239'
        $purchase_order_options = array_filter($purchase_order_options, function ($po) {
            return $po['purchase_order_type'] === '683568ca2fe8263239';
        });
        
        // Reindex array (optional, depending on how select_option uses it)
        $purchase_order_options = array_values($purchase_order_options);
        
        // Convert to HTML select options
        $purchase_order_options = select_option($purchase_order_options, "Select Purchase Order No");
        
        // Return as response
        echo $purchase_order_options;
        break;
case "get_po_items_for_srn":

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


case "clear_srn_sublist":
    ob_clean(); // flush any previous output
    $screen_unique_id = $_POST["screen_unique_id"];

    $columns = [ "is_delete" => 1 ];
    $where   = [ "screen_unique_id" => $screen_unique_id ];

    $action_obj = $pdo->update("srn_sublist", $columns, $where);

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $action_obj->status ? "cleared" : "clear_failed",
        "error"  => $action_obj->error
    ]);
    break;
    
case "get_purchase_address":
    $project = $_POST['project_id'];

    $table = "project_creation";
    $columns = ['address'];
    $table_details = [$table, $columns];
    $where = ['unique_id' => $project];

    $result = $pdo->select($table_details, $where);
    
    error_log("result: " . print_r($result, true) . "\n", 3, "project_address.log");

    header('Content-Type: application/json');

    if ($result->status && !empty($result->data)) {
        echo json_encode([
            "status"  => true,
            "address" => $result->data[0]['address']
        ]);
    } else {
        echo json_encode([
            "status"  => false,
            "msg"     => "Address not found",
            "error"   => $result->error ?? null
        ]);
    }
    break;
    
    case "get_po_date":
        
        $po_uid = $_POST['po_uid'];
        
        $table = "purchase_order";
        $columns = ['entry_date'];
        $table_details = [$table, $columns];
        $where = ['unique_id' => $po_uid];
        
        $result = $pdo->select($table_details, $where);
        
        header('Content-Type: application/json');

    if ($result->status && !empty($result->data)) {
        echo json_encode([
            "status"  => true,
            "date" => $result->data[0]['entry_date']
        ]);
    } else {
        echo json_encode([
            "status"  => false,
            "msg"     => "Address not found",
            "error"   => $result->error ?? null
        ]);
    }
        
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

function generatesrn($label, &$labelData) {
    $year = $_SESSION['acc_year'];
    $number = 1;

    do {
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        $srn = "SRN/$label/$year/$paddedNumber";
        $number++;
    } while (in_array($srn, $labelData));

    // Optionally store the new srn
    $labelData[] = $srn;

    return $srn;
}

function fetch_srn_number($table)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the srn_number)
    $table_columns = [
        "srn_number"
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

    $srn_numbers = [];

    // Check if the query was successful and if data is returned
    if ($result->status && !empty($result->data)) {
        // Loop through the data and collect all the srn_number values
        foreach ($result->data as $row) {
            $srn_numbers[] = $row['srn_number'];
        }
        error_log($srn_numbers . "\n", 3, "logs/srn_log.txt");
        return $srn_numbers;
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

function fetch_srn_status($srn_number = "")
{
    global $pdo;

    $table_name    = "srn";
    $where         = [];
    $table_columns = [
        "unique_id",
        "check_status",
        "approve_status"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_delete" => 0,
        "is_active" => 1
    ];

    if ($srn_number) {
        $where              = [];
        $where["srn_number"] = $srn_number;
    }

    $srn_status = $pdo->select($table_details, $where);

    if ($srn_status->status) {
        return $srn_status->data;
    } else {
        print_r($srn_status);
        return 0;
    }
}

function fetch_srn_data($unique_id) {
    global $pdo;

    $table_name = "srn";
    $table_columns = [
        "paf",
        "freight",
        "other",
        "round",
        "gst_paf",
        "gst_freight",
        "gst_other"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = [
        "is_delete" => 0
    ];

    if ($unique_id) {
        $where = [];
        $where["unique_id"] = $unique_id;
    }

    $srn_data = $pdo->select($table_details, $where);

    if ($srn_data->status) {
        return $srn_data->data;
    } else {
        print_r($srn_data);
        return 0;
    }
}

?>