<?php
// crud.php for Purchase Requisition Level 2 Approval

$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

include '../../config/dbconfig.php';
include '../../config/new_db.php';

$action = $_POST['action'];
$action_obj = (object) ["status" => 0, "data" => "", "error" => "Action Not Performed"];
$json_array = "";
$user_id = $_SESSION['sess_user_id'];
$date = date('Y-m-d H:i:s', time());

$documents_upload   = "purchase_requisition_uploads";


switch ($action) {

    case 'datatable':
        $table = "purchase_requisition";
        $sub_list_table = "purchase_requisition_items";
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start = $_POST['start'];
        $draw = $_POST['draw'];
        $limit = ($length == '-1') ? "" : $length;

        $columns = [
            "@a:=@a+1 s_no",
            "pr.pr_number",
            "pr.company_id",
            "pr.project_id",
            "pr.requisition_for",
            "pr.requisition_type",
            "pr.requisition_date",
            "pr.requested_by",
            "pr.remarks",
            // "(
            //     CASE
            //         WHEN EXISTS (
            //             SELECT 1 FROM purchase_requisition_items pi 
            //             WHERE pi.main_unique_id = pr.unique_id AND pi.status = 1
            //         ) THEN 1
            //         WHEN EXISTS (
            //             SELECT 1 FROM purchase_requisition_items pi 
            //             WHERE pi.main_unique_id = pr.unique_id AND pi.status = 2
            //         ) THEN 2
            //         ELSE 0
            //     END
            // ) AS item_status",
            "(
                CASE
                    WHEN EXISTS (
                        SELECT 1 FROM purchase_requisition_items pi 
                        WHERE pi.main_unique_id = pr.unique_id AND pi.lvl_2_status = 1
                    ) THEN 1
                    WHEN EXISTS (
                        SELECT 1 FROM purchase_requisition_items pi 
                        WHERE pi.main_unique_id = pr.unique_id AND pi.lvl_2_status = 2
                    ) THEN 2
                    ELSE 0
                END
            ) AS lvl_2_status",
            "pr.unique_id"
        ];

        $table_details = [
            $table . " pr, (SELECT @a:= " . $start . ") AS a",
            $columns
        ];

        $where = "pr.is_delete = '0' AND EXISTS (
            SELECT 1 FROM purchase_requisition_items pi 
            WHERE pi.main_unique_id = pr.unique_id AND pi.status = 1
        )";

        if (!empty($_POST['pr_number'])) $where .= " AND pr.unique_id = '" . trim($_POST['pr_number']) . "'";
        if (!empty($_POST['company_name'])) $where .= " AND pr.company_id = '" . trim($_POST['company_name']) . "'";
        if (!empty($_POST['project_name'])) $where .= " AND pr.project_id = '" . trim($_POST['project_name']) . "'";
        if (!empty($_POST['type_of_service'])) $where .= " AND pr.service_type = '" . trim($_POST['type_of_service']) . "'";
        if (!empty($_POST['requisition_for'])) $where .= " AND pr.requisition_for = '" . trim($_POST['requisition_for']) . "'";
        if (!empty($_POST['requisition_date'])) $where .= " AND pr.requisition_date = '" . trim($_POST['requisition_date']) . "'";

if (isset($_POST['lvl_2_status']) && $_POST['lvl_2_status'] !== '') {
    $lvl_2_status = trim($_POST['lvl_2_status']);

    if ($lvl_2_status === '1') { // Approved
        $where .= " AND EXISTS (
            SELECT 1 FROM purchase_requisition_items pi 
            WHERE pi.main_unique_id = pr.unique_id AND pi.lvl_2_status = 1
        )";
    } elseif ($lvl_2_status === '2') { // Rejected
        $where .= " AND EXISTS (
            SELECT 1 FROM purchase_requisition_items pi 
            WHERE pi.main_unique_id = pr.unique_id AND pi.lvl_2_status = 2
        )";
    } elseif ($lvl_2_status === '0') { // Pending (i.e., no item has status 1 or 2)
        $where .= " AND NOT EXISTS (
            SELECT 1 FROM purchase_requisition_items pi 
            WHERE pi.main_unique_id = pr.unique_id AND pi.lvl_2_status IN (1, 2)
        )";
    }
}


        $order_column = $_POST["order"][0]["column"];
        $order_dir = $_POST["order"][0]["dir"];
        $order_by = datatable_sorting($order_column, $order_dir, $columns);
        $search = datatable_searching($search, $columns);

        if ($search) $where .= " AND ($search)";

        $sql_function = "SQL_CALC_FOUND_ROWS";
        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        $data = [];
        if ($result->status) {
            foreach ($result->data as $value) {
                $company_data = company_name($value['company_id']);
                $project_data = project_name($value['project_id']);

                $value['company_id'] = $company_data[0]['company_name'];
                $value['project_id'] = $project_data[0]['project_code'] . " / " . $project_data[0]['project_name'];

                $value['requisition_for'] = ["1" => "Direct", "2" => "SO", "3" => "Ordered BOM"][$value['requisition_for']];
                $value['requisition_type'] = [
                    "1" => "Regular",
                    "683568ca2fe8263239" => "Service",
                    "683588840086c13657" => "Capital"
                ][$value['requisition_type']];
                
                

                $value['lvl_2_status'] = $value['lvl_2_status'] == 1 ? '<span style="color:green;font-weight:bold">Approved</span>' : ($value['lvl_2_status'] == 2 ? '<span style="color:red;font-weight:bold">Rejected</span>' : '<span style="color:#DAA520; font-weight:bold;">Pending</span> <!-- Dark Goldenrod -->');

                $btn_view  = btn_views($folder_name, $value['unique_id']);
                $btn_print = btn_prints($folder_name, $value['unique_id']);
                $btn_upload = btn_docs($folder_name, $value['unique_id']);
            
                $btn_update                     = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);

                $value['unique_id'] = '<i class="fe-edit" style="font-size: 22px; cursor: pointer;" onclick="open_lvl_2_modal(\'' . $value['unique_id'] . '\')" title="Level 2 Approval"></i>' . $btn_upload;

                array_splice($value, -1, 0, [$btn_view, $btn_print]);

                $data[] = array_values($value);
            }
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data" => $data,
            "testing" => $result->sql
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
                        $image_path = "../blue_planet_beta/uploads/purchase_requisition_test/" . trim($image_file);
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
    case 'handle_lvl_2_status':
        $selectedValue = $_POST['selectedValue'];
        $cancelReason = $_POST['cancelReason'];
        $new_quantity = $_POST['quantity'];
        $unique_id = $_POST['unique_id'];
        $updated_by = $_SESSION['user_id'];
        $updated_at = date("Y-m-d");


        $table_sub = "purchase_requisition_items";
        $columns = [
            "lvl_2_status" => $selectedValue,
            "lvl_2_reason" => $cancelReason,
            "lvl_2_quantity" => $new_quantity,
            "updated_user_id" => $updated_by,
            "updated" => $updated_at
        ];

        if ($unique_id) {
            $update_where = ["unique_id" => $unique_id];
            $action_obj = $pdo->update($table_sub, $columns, $update_where);
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data" => $action_obj->data,
            "error" => $action_obj->error,
            "msg" => $action_obj->status ? "update" : "error",
            "sql" => $action_obj->sql
        ]);
        break;
    // case 'approval_modal':

    //     $search     = $_POST['search']['value'];
    //     $length     = $_POST['length'];
    //     $start      = $_POST['start'];
    //     $draw       = $_POST['draw'];
    //     $id         = $_POST['id'];
    //     $limit      = ($length == '-1') ? "" : $length;
    
    //     $unique_id  = $_POST["id"];
    //     $table_sub  = "purchase_requisition_items";
    //     $data       = [];
    
    //     $columns = [
    //         "@a:=@a+1 s_no",
    //         "item_code",
    //         "item_description",
    //         "quantity AS pr_qty",         // Original PR Quantity
    //         "new_quantity AS l1_qty",     // Approved in Level 1
    //         "lvl_2_quantity",             // Level 2 input quantity
    //         "uom",
    //         "budgetary_rate",
    //         "item_remarks",
    //         "required_delivery_date",
    //         "lvl_2_status",
    //         "lvl_2_reason",
    //         "unique_id",
    //         "new_quantity",
    //     ];
    
    
    //     $table_details  = [$table_sub . ", (SELECT @a:=" . $start . ") AS a", $columns];
    //     $where = [
    //         "main_unique_id" => $unique_id,
    //         "is_delete" => '0',
    //         "status" => 1  // ✅ Only Level 1 Approved rows should be visible in Level 2 modal
    //     ];
    
    //     $order_column   = $_POST["order"][0]["column"];
    //     $order_dir      = $_POST["order"][0]["dir"];
    //     $order_by       = datatable_sorting($order_column, $order_dir, $columns);
    //     $sql_function   = "SQL_CALC_FOUND_ROWS";
    
    //     $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    //     $total_records  = total_records();
    
    //     $main_data = [];
    //     $extra_result = $pdo->select("purchase_requisition", "unique_id = '$id'");
    
    //     if ($extra_result->status && !empty($extra_result->data)) {
    //         $main = $extra_result->data[0];
    
    //         $main_data['pr_number']         = $main['pr_number'];
    //         $main_data['date']              = date('d-m-Y');
    //         $main_data['requisition_date']  = date("d-m-Y", strtotime($main['requisition_date']));
    //         $main_data['requisition_for']   = $main['requisition_for'];
    //         $main_data['requisition_type']  = $main['requisition_type'];
    
    //         $company_data = company_name($main['company_id']);
    //         // $project_data = project_name($main['project_id']);
    
    //         $main_data['company_id'] = $company_data[0]['company_name'];
    //                     $project_data                   = project_name($main['project_id']);
    //             $main_data['project_id'] = $project_data[0]['project_code'] . " / " . $project_data[0]['project_name'];
    //     }
    
    //     if ($result->status) {
    //         $res_array = $result->data;
    
    //         foreach ($res_array as $key => $value) {
    //             $item_data = item_name_list($value["item_code"]);
    //             $value["item_code"] = isset($item_data[0]["item_name"], $item_data[0]["item_code"])
    //                 ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
    //                 : "-";
            
    //             $uom_data = unit_name($value["uom"]);
    //             $value["uom"] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";
            
    //             $value['required_delivery_date'] = (!empty($value['required_delivery_date']) && $value['required_delivery_date'] != "0000-00-00") ? date("d-m-Y", strtotime($value['required_delivery_date'])) : "-";
            
    //             if ($value['lvl_2_reason'] == "") $value['lvl_2_reason'] = "-";
            
    //             // STATUS DISPLAY (Dropdown for pending, label for approved/rejected)
    //             if ($value['lvl_2_status'] == "1") {
    //                 $status_display = "<span style='color: green; font-weight: bold;'>Approved</span>";
    //             } elseif ($value['lvl_2_status'] == "2") {
    //                 $status_display = "<span style='color: red; font-weight: bold;'>Rejected</span>";
    //             } else {
    //                 $status_display = '<select class="form-control status-select" id="status_val_' . $value['unique_id'] . '" onchange="handle_status(this.value, \''.$value['unique_id'].'\')">
    //                                         <option value="">Select Status</option>
    //                                         <option value="1">Approve</option>
    //                                         <option value="2">Reject</option>
    //                                   </select>
    //                                   <div id="cancelReasonDiv_'.$value['unique_id'].'" style="display: none; margin-top: 10px;">
    //                                         <textarea id="cancelReason_' . $value['unique_id'] . '" placeholder="Reason for rejection" class="form-control" rows="3"></textarea>
    //                                         <button type="button" class="btn btn-danger mt-1" onclick="handle_status(document.getElementById(\'status_val_' . $value['unique_id'] . '\').value, \'' . $value['unique_id'] . '\', document.getElementById(\'cancelReason_' . $value['unique_id'] . '\').value)">Submit</button>
    //                                     </div>';
    //             }
            
    //             // DISPLAY 3 TYPES OF QUANTITIES
    //             $pr_quantity = '<span>' . htmlspecialchars($value['qty']) . '</span>';
    //             $l1_quantity = '<span>' . htmlspecialchars($value['new_quantity']) . '</span>';
            
    //             $default_qty = ($value['lvl_2_quantity'] != null && $value['lvl_2_quantity'] != "") ? $value['lvl_2_quantity'] : $value['new_quantity'];
            
    //             $edit_quantity = ($value['lvl_2_status'] === null || $value['lvl_2_status'] === "0")
    //                 ? '<input type="number" class="form-control" id="quantity_' . $value['unique_id'] . '" value="' . htmlspecialchars($default_qty) . '" min="1">'
    //                 : '<span>' . htmlspecialchars($default_qty) . '</span>';
            
            
    //             $value['qty'] = $pr_quantity;
    //             $value['quantity'] = $l1_quantity;
    //             $value['lvl_2_status'] = $status_display;
    //             $value['budgetary_rate'] = $value['budgetary_rate'];
            
    //             // Add new column for editable quantity
    //             $value['lvl_2_quantity'] = $edit_quantity;
            
    //             $data[] = array_values($value);
                
    //             error_log(print_r($data, true) . "\n", 3, "logs/approval_modal_data.log");  
    //         }
            
    
    //         echo json_encode([
    //             "draw" => intval($draw),
    //             "recordsTotal" => intval($total_records),
    //             "recordsFiltered" => intval($total_records),
    //             "data" => $data,
    //             "main_data" => $main_data,
    //             "testing" => $result->sql
    //         ]);
    //     } else {
    //         echo json_encode([
    //             "draw" => intval($draw),
    //             "recordsTotal" => 0,
    //             "recordsFiltered" => 0,
    //             "data" => [],
    //             "main_data" => $main_data,
    //             "testing" => $result->sql
    //         ]);
    //     }

    // break;
    
  case 'approval_modal':

    // DataTable Variables
    $search     = $_POST['search']['value'];
    $length     = $_POST['length'];
    $start      = $_POST['start'];
    $draw       = $_POST['draw'];
    $id         = $_POST['id'];
    $limit      = $length;

    $unique_id  = $_POST["id"];

    $table_sub  = "purchase_requisition_items";
    $data       = [];

    if ($length == '-1') {
        $limit  = "";
    }

    $columns  = [
        "@a:=@a+1 s_no",
        "item_code",
        "item_description",
        "quantity AS pr_qty",         // Original PR Quantity
        "new_quantity AS l1_qty",     // Approved in Level 1
        "lvl_2_quantity",        
        "uom",
        "budgetary_rate",
        "item_remarks",
        "required_delivery_date",
        "lvl_2_status",
        "lvl_2_reason",
        "unique_id",
        "main_unique_id",
        "unique_id",
        "new_quantity"
    ];

    $table_details  = [
        $table_sub . " , (SELECT @a:= " . $start . ") AS a ",
        $columns
    ];

    $where = [
        "main_unique_id" => $unique_id,
        "is_delete" => '0',
        "status"  => 1
    ];

    $order_column   = $_POST["order"][0]["column"];
    $order_dir      = $_POST["order"][0]["dir"];

    $order_by       = datatable_sorting($order_column, $order_dir, $columns);

    $sql_function   = "SQL_CALC_FOUND_ROWS";

    $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records  = total_records();


    $main_data = [];
    
    $extra_result = $pdo->select("purchase_requisition", "unique_id = " . "'".$id."'");

    if ($extra_result->status && !empty($extra_result->data)) {
        $main = $extra_result->data[0];

        $current_date = date('d-m-Y');
        
        $main_data['pr_number']        = $main['pr_number'];
        $main_data['date']             = $current_date;
        $main_data['requisition_date'] = date("d-m-Y", strtotime($main['requisition_date']));
        $main_data['requisition_for']  = $main['requisition_for'];
        $main_data['requisition_type'] = $main['requisition_type'];

        $company_data                  = company_name($main['company_id']);
        $main_data['company_id']       = $company_data[0]['company_name'];

        $project_data                  = project_name($main['project_id']);
        $main_data['project_id']       = $project_data[0]['project_code'] . " / " . $project_data[0]['project_name'];
    }


    if ($result->status) {
        $res_array = $result->data;

        foreach ($res_array as $key => $value) {
            // Fetch item data
            $item_data = item_name_list($value["item_code"]);
            
            if (empty($item_data)) {
                $prod_res = $pdo->select(
                    ["product_master", ["product_name", "product_code", "description"]],
                    ["unique_id" => $value["item_code"], "is_delete" => 0]
                );
                if ($prod_res->status && !empty($prod_res->data)) {
                    $item_data[0] = [
                        "item_name" => $prod_res->data[0]["product_name"],
                        "item_code" => $prod_res->data[0]["product_code"],
                        "description" => $prod_res->data[0]["description"]
                    ];
                }
            }
            
            $is_fab = !empty($item_data[0]["item_code"]) && strpos($item_data[0]["item_code"], "-FAB-") !== false;

            if (!empty($item_data[0]["item_name"]) && !empty($item_data[0]["item_code"])) {
                $display_code = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];
            } elseif (empty($item_data[0]["item_code"])) {
                $display_code = $item_data[0]["item_name"];
            } elseif (empty($item_data[0]["item_name"])) {
                $display_code = $item_data[0]["item_code"];
            } else {
                $display_code = "-";
            }

            $display_class = "no-sublist";
            $sublist = [];

            if ($is_fab) {
                $prod_unique_id = $value["item_code"];
                $pr_unique_id = $value["main_unique_id"];
                $so_id = fetch_pr_so_uid($pr_unique_id);
            
                $obom_res = $pdo->select(
                    ["obom_list", ["type"]],
                    ["so_unique_id" => $so_id, "is_delete" => 0]
                );
            
                $prod_type = ($obom_res->status && !empty($obom_res->data))
                    ? intval($obom_res->data[0]["type"])
                    : 0;
            
                if ($prod_type != 1) {
                    $sublist_res = $pdo->select(
                        ["obom_child_table", ["item_unique_id", "qty", "uom_unique_id", "remarks"]],
                        ["so_unique_id" => $so_id, "is_delete" => 0]
                    );
            
                    if ($sublist_res->status && !empty($sublist_res->data)) {
                        $display_class = "fab-toggle";
            
                        foreach ($sublist_res->data as $idx => $sub) {
                            $sub_item = $pdo->select(
                                ["item_master", ["item_name", "item_code"]],
                                ["unique_id" => $sub["item_unique_id"], "is_delete" => 0]
                            );
                            $sub_name = ($sub_item->status && !empty($sub_item->data))
                                ? $sub_item->data[0]["item_name"] . " / " . $sub_item->data[0]["item_code"]
                                : $sub["item_unique_id"];
                        
                            $uom = unit_name($sub["uom_unique_id"]);
                        
                            $sublist[] = [
                                "sno"     => $idx + 1,
                                "item"    => $sub_name,
                                "qty"     => $sub["qty"],
                                "uom"     => $uom[0]['unit_name'],
                                "remarks" => $sub["remarks"]
                            ];
                        }
                    }
                }
            }

            $display_code = "<span class='{$display_class}'>" . $display_code . "</span>";

            $uom_data = unit_name($value["uom"]);
            $value["uom"] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";
    
            $value['required_delivery_date'] = (!empty($value['required_delivery_date']) && $value['required_delivery_date'] != "0000-00-00")
                ? date("d-m-Y", strtotime($value['required_delivery_date']))
                : "-";

            if ($value['lvl_2_reason'] == "") {
                $value['lvl_2_reason'] = "-";
            }
                    
            if ($value['lvl_2_status'] == "1") {
                $status_display = "<span style='color: green; font-weight: bold;'>Approved</span>";
            } elseif ($value['lvl_2_status'] == "2") {
                $status_display = "<span style='color: red; font-weight: bold;'>Cancelled</span>";
            } else {
                $status_display = '<select class="form-control status-select-lvl2" id="status_val_' . $value['unique_id'] . '" onchange="handle_status(this.value, \''.$value['unique_id'].'\')">
                                        <option value="">Select the Status</option>
                                        <option value="1">Approve</option>
                                        <option value="2">Cancel</option>
                                   </select>
                                   <div id="cancelReasonDiv_'.$value['unique_id'].'" style="display:none;margin-top:10px;">
                                        <textarea id="cancelReason_' . $value['unique_id'] . '" placeholder="Enter reason for cancellation" class="form-control" rows="4"></textarea>
                                        <button type="button" class="btn btn-primary mt-2" id="submitCancelReason" 
                                            onclick="handle_status(document.getElementById(\'status_val_' . $value['unique_id'] . '\').value, \'' . $value['unique_id'] . '\', document.getElementById(\'cancelReason_' . $value['unique_id'] . '\').value)">
                                            Submit Reason
                                        </button>
                                   </div>';
            }
               
            if ($value['lvl_2_status'] === null || $value['lvl_2_status'] === "") {
                $quantity_display = '<input type="number" class="form-control quantity-input" id="quantity_'.$value['unique_id'].'" value="'.htmlspecialchars($value['pr_qty']).'" min="1">';
            } else {
                $display_quantity = !empty($value['lvl_2_quantity']) ? $value['lvl_2_quantity'] : $value['pr_qty'];
                $quantity_display = '<span>'.htmlspecialchars($display_quantity).'</span>';
            }
            
            $quantity_display1 = '<input type="number" class="form-control quantity-input" id="quantity_'.$value['unique_id'].'" value="'.htmlspecialchars($value['l1_qty']).'" min="1">';
               
            $value['pr_qty'] = $quantity_display;
            $value['lvl_2_status'] = $status_display;
            $value['budgetary_rate'] = $value['budgetary_rate'];

            $data[] = [
                "s_no"                    => $value["s_no"],
                "item_code"               => $display_code,
                "item_description"        => $value["item_description"],
                "pr_qty"                  => $quantity_display,
                "l1_qty"                  => $value["l1_qty"],
                "lvl_2_quantity"          => $quantity_display1,
                "uom"                     => $value["uom"],
                "item_remarks"            => $value["item_remarks"],
                "required_delivery_date"  => $value["required_delivery_date"],
                "lvl_2_status"            => $status_display,
                "lvl_2_reason"            => $value["lvl_2_reason"],
                "sublist"                 => $sublist
            ];
        }

        // ✅ Compute bulk_status_lvl_2 for read-only dropdown logic
        $status_check_lvl2 = $pdo->select(
            ["purchase_requisition_items", ["lvl_2_status"]],
            ["main_unique_id" => $unique_id, "is_delete" => 0, "status" => 1]
        );

        $all_lvl2_status = array_column($status_check_lvl2->data ?? [], "lvl_2_status");

        if (!empty($all_lvl2_status)) {
            $unique_lvl2_status = array_unique($all_lvl2_status);
            if (count($unique_lvl2_status) === 1 && $unique_lvl2_status[0] !== null && $unique_lvl2_status[0] !== "") {
                $main_data['bulk_status_lvl_2'] = $unique_lvl2_status[0];
            } else {
                $main_data['bulk_status_lvl_2'] = "0";
            }
        } else {
            $main_data['bulk_status_lvl_2'] = "0";
        }

        $json_array = [
            "draw"              => intval($draw),
            "recordsTotal"      => intval($total_records),
            "recordsFiltered"   => intval($total_records),
            "data"              => $data,
            "main_data"         => $main_data,
            "testing"           => $result->sql
        ];

    } else {
        $json_array = [
            "draw"              => intval($draw),
            "recordsTotal"      => 0,
            "recordsFiltered"   => 0,
            "data"              => [],
            "main_data"         => $main_data,
            "testing"           => $result->sql
        ];
    }

    echo json_encode($json_array);
    break;

    case 'bulk_update_status_lvl_2':
    $main_unique_id = $_POST['main_unique_id'];
    $selectedValue  = $_POST['selectedValue']; // 1 = Approve All, 2 = Reject All

    $table_sub = "purchase_requisition_items";

    if (!$main_unique_id || !in_array($selectedValue, [1, 2])) {
        echo json_encode([
            "status" => false,
            "error"  => "Missing or invalid parameters",
            "msg"    => "invalid_input"
        ]);
        exit;
    }

    // Update all items under that requisition for level 2
    $columns = [
        "lvl_2_status" => $selectedValue,
        "lvl_2_reason" => ($selectedValue == 2 ? "Rejected in bulk action (L2)" : null)
    ];

    $where = [
        "main_unique_id" => $main_unique_id,
        "is_delete" => 0
    ];

    $action_obj = $pdo->update($table_sub, $columns, $where);

    $msg = $action_obj->status ? "bulk_update_lvl2_success" : "bulk_update_lvl2_failed";

    echo json_encode([
        "status" => $action_obj->status,
        "error"  => $action_obj->error,
        "msg"    => $msg,
        "sql"    => $action_obj->sql
    ]);
break;

    
    default:
        break;
} 
?>