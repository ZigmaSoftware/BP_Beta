<?php 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table_group             = 'group_product_master';
$table              = 'product_master'; 
$sub_list_table     = 'product_sublist'; 
$item_table     = 'item_master'; 
$documents_upload   = 'ordered_bom_uploads';

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
$sub_group_unique_id= "";
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
    case 'createupdate_drop_down':

        $group_unique_id    = $_POST["group_unique_id"];
    
        $update_where       = "";

        $columns            = [
            "group_unique_id"       => $group_unique_id,
            "created_user_id"       => $user_id,
            "created"               => $date,
            "unique_id"             => unique_id($prefix)
        ];

      
        // Insert Begins
        $action_obj     = $pdo->insert($table_group, $columns);
        // Insert Ends

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

            if ($unique_id) {
                $msg        = "update";
            } else {
                $msg        = "create";
            }
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
        
    case 'createupdate':

        $debug_log = [];
    
        try {
            ////error_log("POST: " . print_r($_POST, true) . "\n", 3, "post_log.txt");
    
            $group_unique_id     = $_POST["group_unique_id"] ?? '';
            $sub_group_unique_id = $_POST["sub_group_unique_id"] ?? '';
            $company_id          = $_POST["company_id_display"] ?? '';
            $product_name        = $_POST["product_name"] ?? '';
            $description         = !empty(trim($_POST["description"] ?? '')) ? trim($_POST["description"]) : null;
            $is_active           = $_POST["is_active"] ?? 1;
            $unique_id           = $_POST["unique_id"] ?? '';
            $update_condition    = $_POST["update_condition"] ?? '';
            $update              = $_POST["update"] ?? 0;
    
            $debug_log[] = "Step 1: Data collected from POST.";
            
            // $product_code = create_product_code($group_unique_id, $sub_group_unique_id, $company_id);
            
            $num = get_next_product_code_number();  // Will return 001, 002, etc.
            
            $table_1 = "product_vertical";
            $table_2 = "product_type";
            $table_3 = "company_creation";
    
            $column_12 = [
                "product_code"  
            ];
            
            $column_3 = [
                "company_code"  
            ];
            
            $result_1 = $pdo->select([$table_1, $column_12], ["id" => $group_unique_id]);
            //error_log("result 1: " . print_r($result_1, true) . "\n", 3, "res_1.log");
            
            if ($result_1->status && !empty($result_1->data[0]['product_code'])) {
                $pro_code_1 = $result_1->data[0]['product_code'];
            }
            
            $result_2 = $pdo->select([$table_2, $column_12], ["id" => $sub_group_unique_id]);
            //error_log("result 2: " . print_r($result_2, true) . "\n", 3, "res_1.log");
    
            if($result_2->status && !empty($result_2->data[0]['product_code'])){
                $pro_code_2 = $result_2->data[0]['product_code'];
            }
            
            $result_3 = $pdo->select([$table_3, $column_3], ["unique_id" => $company_id]);
            //error_log("result 3: " . print_r($result_3, true) . "\n", 3, "res_1.log");
            
            if($result_3->status && !empty($result_3->data[0]['company_code'])){
                $comp_code = $result_3->data[0]['company_code'];
            }
            
            //error_log("result final: " . $pro_code_1 . " " . $pro_code_2 . " " . $comp_code . "\n", 3, "res_1.log");
            
            $product_code = $comp_code . "-" . $pro_code_1 . "-" . $pro_code_2 . "-" . $num;

            //error_log("result final: " . $product_code . "\n", 3, "res_code.log");
    
            // Step 2: Check for duplicate
            $table_details = [$table, ["COUNT(unique_id) AS count"]];
    
            // ЁЯЫая╕П FIXED this line (broken quotes and missing AND)
            $select_where = 'group_unique_id = "' . $group_unique_id . '" AND sub_group_unique_id = "' . $sub_group_unique_id . '" AND product_name = "' . $product_name . '" AND is_delete = 0';
    
            if ($unique_id) {
                $select_where .= ' AND unique_id != "' . $unique_id . '"';
            }
    
            $debug_log[] = "Step 2: Duplicate check WHERE = $select_where";
    
            $action_obj = $pdo->select($table_details, $select_where);
    
            //error_log("action obj (duplicate check): " . print_r($action_obj, true) . "\n", 3, "action_log.txt");
    
            if (!$action_obj->status) {
                throw new Exception("Duplicate check failed: " . $action_obj->error);
            }
    
            $count = $action_obj->data[0]["count"] ?? 0;
    
            if ($count > 0) {
                $debug_log[] = "Step 3: Duplicate found.";
                $json_array = [
                    "status" => false,
                    "msg"    => "already",
                    "data"   => [],
                    "error"  => "Duplicate record exists.",
                    "sql"    => $action_obj->sql ?? '',
                    "log"    => $debug_log
                ];
                echo json_encode($json_array);
                exit;
            }
    
            $debug_log[] = "Step 4: No duplicate found, proceeding.";
    
            // Step 5: Common columns
            $columns = [
                "description" => $description,
                "is_active"   => $is_active
            ];
    
            if ($update == 1) {
                // Step 6: Update existing record
                $columns["updated_user_id"] = $user_id;
                $columns["updated"]         = $date;
    
                $update_where = ["unique_id" => $unique_id];
    
                $debug_log[] = "Step 6: Updating record with unique_id = $unique_id";
    
                $action_obj = $pdo->update($table, $columns, $update_where);
    
                //error_log("update action obj: " . print_r($action_obj, true) . "\n", 3, "action_log.txt");
    
                if (!$action_obj->status) {
                    throw new Exception("Update failed: " . $action_obj->error);
                }
    
                $msg = "update";
                $debug_log[] = "Step 7: Update successful.";
    
            } else {
                // Step 7: Insert new record
                $columns["group_unique_id"]     = $group_unique_id;
                $columns["sub_group_unique_id"] = $sub_group_unique_id;
                $columns["company_id"]          = $company_id;
                $columns["product_name"]        = $product_name;
                $columns["product_code"]        = $product_code;
                $columns["created_user_id"]     = $user_id;
                $columns["created"]             = $date;
                $columns["unique_id"]           = unique_id();
    
                $debug_log[] = "Step 8: Inserting new record.";
    
                $action_obj = $pdo->insert($table, $columns);
    
                //error_log("insert action obj: " . print_r($action_obj, true) . "\n", 3, "action_log.txt");
    
                if (!$action_obj->status) {
                    throw new Exception("Insert failed: " . $action_obj->error);
                }
    
                $msg = "create";
                $debug_log[] = "Step 9: Insert successful.";
            }
    
            $json_array = [
                "status" => true,
                "msg"    => $msg,
                "data"   => $action_obj->data ?? [],
                "error"  => "",
                "sql"    => $action_obj->sql ?? '',
                "log"    => $debug_log
            ];
    
        } catch (Exception $e) {
            $error = $e->getMessage();
            $debug_log[] = "Exception caught: $error";
    
            $json_array = [
                "status" => false,
                "msg"    => "error",
                "data"   => [],
                "error"  => $error,
                "sql"    => $action_obj->sql ?? '',
                "log"    => $debug_log
            ];
        }
    
        //error_log("debug_log: " . print_r($debug_log, true) . "\n", 3, "//error_log_cu.txt");
        //error_log("json: " . print_r($json_array, true) . "\n", 3, "json_log.txt");
    
        echo json_encode($json_array);
        exit;
    
    break;
    
    case 'to_list':
    $so_unique_id = $_POST['so_unique_id'] ?? '';
    $type         = $_POST['type'] ?? '';

    if (!empty($so_unique_id) && !empty($type)) {
        $update = $pdo->update("obom_list", 
            ["to_list" => 1], 
            ["so_unique_id" => $so_unique_id, "type" => $type]
        );

        echo json_encode([
            "status"  => $update ? true : false,
            "message" => $update ? "Updated successfully" : "Update failed"
        ]);
    } else {
        echo json_encode([
            "status"  => false,
            "message" => "Invalid params"
        ]);
    }
    break;
    
    case "cancel":
    $type         = $_POST["type"] ?? "";
    $so_unique_id = $_POST["so_unique_id"] ?? "";

    $response = ["status" => false, "msg" => ""];

    if (!empty($type) && !empty($so_unique_id)) {
        // 1. Fetch prod_unique_id(s) for this SO+Type
        $prod_result = $pdo->select(
            ['obom_list', ['prod_unique_id', 'item_unique_id']],
            ['so_unique_id' => $so_unique_id, 'type' => $type, 'is_delete' => 0]
        );
        
        error_log(print_r($prod_result, true), 3, "log/cancel/prod_res.log");
        
        if ($prod_result->status && !empty($prod_result->data)) {
            foreach ($prod_result->data as $row) {
                $prod_id = $row['prod_unique_id'];
                $item_id = $row['item_unique_id'];

                // 2. Mark parent deleted
                $res1 = $pdo->update(
                    "obom_list",
                    ["is_delete" => 1],
                    ["so_unique_id" => $so_unique_id, "type" => $type, "prod_unique_id" => $prod_id]
                );
                
                error_log(print_r($res1, true), 3, "log/cancel/parent.log");

                // 3. Mark child deleted (using both SO + prod_id + type)
                $res2 = $pdo->update(
                    "obom_child_table",
                    ["is_delete" => 1],
                    ["so_unique_id" => $so_unique_id, "prod_unique_id" => $item_id]
                );
                
                error_log("so: " . $so_unique_id . "\n" .  "prod: " . $prod_id . "\n" . "item: " . $item_id . "\n" . "type: " . $type . "\n", 3, "log/cancel/data.log");
                
                error_log(print_r($res2, true), 3, "log/cancel/child.log");
            }

            $response["status"] = true;
            $response["msg"] = "Cancel success";
        } else {
            $response["msg"] = "No active OBOM found for this SO/Type";
        }
    } else {
        $response["msg"] = "Missing type or SO";
    }

    echo json_encode($response);
    break;






 case 'documents_add_update':

        $upload_unique_id = $_POST["upload_unique_id"] ?? null;
        $type             = $_POST["type"] ?? null;
        $unique_id        = $_POST["unique_id"] ?? null;
        
        // Log incoming POST data
        //error_log("POST: " . print_r($_POST, true) . "\n", 3, "doc_logs.txt");
        
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
            $target_dir = "../../uploads/ordered_bom/";
            $folder_path = "ordered/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES["test_file"]["name"] as $key => $name) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                finfo_close($finfo);
                
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                //error_log("Detected MIME: $mime_type\n", 3, "doc_logs_1.txt");
                //error_log("Detected ext: $file_extension\n", 3, "doc_logs_1.txt");

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
            "ordered_bom_unique_id"              => $upload_unique_id,
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
        
        //error_log("action_obj: " . print_r($action_obj, true) . "\n", 3, "doc_logs.txt");
        
        $data_array = [
            "insert_id" => $action_obj->data,     // if it's lastInsertId()
            "upload"    => $upload_unique_id
        ];
        
        // //error_log("json_response: " . print_r([
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
            "ordered_bom_unique_id" => $upload_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        //error_log("documents datatable query: " . $result->sql . "\n", 3, "debug.txt");

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
                        $image_path = "../blue_planet_beta/uploads/ordered_bom/" . trim($image_file);
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



case 'datatable':
    // DataTable Variables
    $search  = $_POST['search']['value'];
    $length  = $_POST['length'];
    $start   = $_POST['start'];
    $draw    = $_POST['draw'];
    $limit   = $length;

    $data = [];

    if ($length == '-1') {
        $limit  = "";
    }

    // Query Variables
    $json_array = "";
    $columns = [
        "@a:=@a+1 s_no",
        "t.prod_unique_id",
        "t.so_unique_id",
        "t.so_type",       // ✅ From sales_order
        "t.type",
        "t.is_active"
    ];
    
    $table_details = [
        "(SELECT DISTINCT 
              ps.prod_unique_id, 
              ps.so_unique_id, 
              so.so_type,          -- ✅ join sales_order
              ps.type, 
              ps.is_active
          FROM obom_list ps
          LEFT JOIN {$table} pm ON pm.unique_id = ps.prod_unique_id
          LEFT JOIN {$item_table} it ON it.unique_id = ps.prod_unique_id
          LEFT JOIN sales_order so ON so.unique_id = ps.so_unique_id   -- ✅ join
          WHERE ps.is_delete = '0' AND ps.to_list = '1'"
            . (
                ($extra = array_filter([
                    !empty($_POST['prod_unique_id']) ? "ps.prod_unique_id = '{$_POST['prod_unique_id']}'" : null,
                    !empty($_POST['so_unique_id'])   ? "ps.so_unique_id = '{$_POST['so_unique_id']}'" : null,
                    !empty($_POST['data_type'])      ? "pm.data_type = '{$_POST['data_type']}'" : null,
                    !empty($_POST['group_unique_id'])? "pm.group_unique_id = '{$_POST['group_unique_id']}'" : null,
                    !empty($_POST['sub_group_unique_id']) ? "pm.sub_group_unique_id = '{$_POST['sub_group_unique_id']}'" : null,
                    !empty($_POST['company_unique_id'])  ? "so.company_id = '{$_POST['company_unique_id']}'" : null,
                    !empty($_POST['item_category'])  ? "it.category_id = '{$_POST['item_category']}'" : null,
                    !empty($_POST['type'])  ? "ps.type = '{$_POST['type']}'" : null,
                    !empty($_POST['so_type'])  ? "so.so_type = '{$_POST['so_type']}'" : null
                ]))
                ? " AND " . implode(" AND ", $extra)
                : ""
            )
        . ") AS t, (SELECT @a:={$start}) AS a",
        $columns
    ];

    // Default where
    $where = "0"; 

    if (!empty($product_ids)) {
        $conditions = [];
        foreach (array_unique($product_ids) as $pid) {
            $pid = preg_replace('/[^a-zA-Z0-9_-]/', '', $pid); // basic sanitization
            $conditions[] = "prod_unique_id = '$pid'";
        }
        $where = '(' . implode(' OR ', $conditions) . ')';
    }
    
    $order_column = $_POST["order"][0]["column"];
    $order_dir    = $_POST["order"][0]["dir"];

    $order_by = datatable_sorting($order_column, $order_dir, $columns);
    $search_sql = datatable_searching($search, $columns);

    if ($search_sql) {
        if ($where) {
            $where .= " AND ";
        }
        $where .= $search_sql;
    }

    $sql_function = "SQL_CALC_FOUND_ROWS";

    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records = total_records();

    if ($result->status) {
        $res_array = $result->data;

        foreach ($res_array as $key => $value) {
            $prod_unique_id = $value['prod_unique_id'];
            $so_unique_id   = $value['so_unique_id'];
            $so_type        = $value['so_type'];
            $type           = $value['type'];

            // Type (With/Without Materials)
            $type_name = ($type == 1) ? "With Materials" : "Without Materials";

            // SO Type Labels
            switch ($so_type) {
                case "1": $so_type_name = "Product"; break;
                case "2": $so_type_name = "Project"; break;
                case "3": $so_type_name = "Spare"; break;
                case "4": $so_type_name = "Maintenance"; break;
                default:  $so_type_name = "-";
            }

            // Product name (only meaningful for so_type 1/2)
            $product_name = "-";
            if ($so_type == "1" || $so_type == "2" || $so_type == "3") {
                $prod_data = product_name($prod_unique_id);
                $product_name = !empty($prod_data[0]['product_name']) ? $prod_data[0]['product_name'] : '-';
                
                if ($product_name == '-') {
                    $item_data = product_name_semi_finished($prod_unique_id);
                    $product_name = !empty($item_data[0]['item_name']) ? $item_data[0]['item_name'] : '-';
                }
            }

            $sales_order = sales_order($so_unique_id)[0]['sales_order_no'];

            // Buttons
            $btn_views  = btn_views_dev($folder_name, $so_unique_id, $type);
            $btn_update = btn_update($folder_name, $so_unique_id);
            $btn_upload = btn_docs($folder_name, $so_unique_id);

            // Prepare row
            $row = [];
            $row[] = $value['s_no'];
            $row[] = $sales_order;
            // $row[] = $product_name;   // still keeps product_unique_id mapping
            $row[] = $so_type_name;   // ✅ SO Type from sales_order

            $row[] = $type_name;
            $row[] = $btn_views;
            $row[] = $btn_update . $btn_toggle . $btn_upload;

            $data[] = $row;
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
            "error"   => true,
            "message" => "Query failed",
            "details" => $result
        ];
    }

    echo json_encode($json_array);
    break;


    case 'sub_list_cnt':
		$unique_id 		= $_POST['unique_id'];

		$data	    = [];
		
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no", 
            "unique_id",
        ];
        $table_details  = [
            $sub_list_table,
            $columns
        ];
        
        $where = 'prod_unique_id ="'.$unique_id.'"  AND is_delete = "0" ';
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();
        
        if ($result->status) {

            $res_array      = $result->data;
            if($total_records == 0){
                $msg = "sub_list";
            }else{
                $msg = "completed";
            }
            
            $json_array   = [
                "status"    => $status,
                "data"      => $data,
                "error"     => $error,
                "msg"       => $msg,
                "sql"       => $sql
                
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
    
    
   case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active'];

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    $status = $action_obj->status;
    $msg    = $status
        ? ($is_active == 1 ? "Activated Successfully" : "Deactivated Successfully")
        : "Toggle failed!";

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

    
    case 'get_group_code':
        $unique_id = $_POST['code'];
        $id                 = category_item($unique_id);
        $uom_unique_id      = unit_name($id[0]['uom_unique_id']);
        $json_array     = "";
        
        if ($unique_id) {
           
            $json_array = [
                'status' => 'success',
                'data' => $uom_unique_id[0]['unit_name']
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
        
    case 'sub_group_name':

        $group_id = $_POST['group_id'];
        $type     = $_POST['type'];
        $m_type   = $_POST['m_type'];
    
        $sub_group_name_options = [];
        $msg = "Select";
    
        if ($type == 1) {
            $sub_group_name_options = sub_group_name("", $group_id);
        } elseif ($type == 2) {
            $sub_group_name_options = category_name("", "", $group_id);
    
            // 🔹 Exclude SEMI-FINISHED when m_type = 1
            if ($m_type == 1) {
                $sub_group_name_options = array_values(array_filter(
                    $sub_group_name_options,
                    function($row) {
                        return $row['unique_id'] !== "689c7932650be49774";
                    }
                ));
            }
        } elseif ($type == 3) {
            $sub_group_name_options = category_item("", "", "", $group_id);
        } else {
            $sub_group_name_options = product_type_name("", $group_id);
            $msg = "Select the sub groups";
        }
    
        // 🔹 Now build the <option> list
        $sub_group_name_options = select_option($sub_group_name_options, $msg);
        echo $sub_group_name_options;
    
        break;

    case 'obom_update':

    $type  = $_POST['type'] ?? '';
    $so_id = $_POST['so_id'] ?? '';

    $response = [
        'status' => false,
        'msg'    => '',
        'error'  => ''
    ];

    if (!empty($type) && !empty($so_id)) {
        $table_o = 'obom_list';

        try {
            // 🔍 Step 1: Check if record exists
            $check = $pdo->select($table_o, 
                ["COUNT(*) AS cnt"], 
                [
                    "so_unique_id" => $so_id,
                    "type"         => $type,
                    "is_delete"    => 0
                ]
            );

            $count = $check[0]['cnt'] ?? 0;

            if ($count > 0) {
                // 📝 Step 2a: Update
                $columns = ["type" => $type];
                $where   = ["so_unique_id" => $so_id, "is_delete" => 0];

                $result = $pdo->update($table_o, $columns, $where);

                if ($result) {
                    $response['status'] = true;
                    $response['msg']    = "OBOM updated successfully.";
                } else {
                    $response['error'] = "No rows updated. Possibly invalid SO ID.";
                }
            } else {
                // ➕ Step 2b: Insert
                $data = [
                    "so_unique_id" => $so_id,
                    "type"         => $type,
                    "is_delete"    => 0
                ];

                $insert = $pdo->insert($table_o, $data);

                if ($insert) {
                    $response['status'] = true;
                    $response['msg']    = "OBOM inserted successfully.";
                } else {
                    $response['error'] = "Insert failed.";
                }
            }
        } catch (Exception $e) {
            $response['error'] = "DB Error: " . $e->getMessage();
        }
    } else {
        $response['error'] = "Missing required parameters (type/so_id).";
    }

    echo json_encode($response);
    break;


        
    case 'product_add_update':

        $prod_unique_id                 = $_POST["prod_unique_id"];
        $product_name                   = $_POST["product_name"];
        $so_id                          = $_POST["so_id"];
        $unique_id                      = $_POST["unique_id"];
        $group_unique_id_sub            = $_POST["group_unique_id_sub"];
        $sub_group_unique_id_sub_list   = $_POST["sub_group_unique_id_sub_list"];
        $category_unique_id_sub         = $_POST["category_unique_id_sub"];
        $item_unique_id_sub             = $_POST["item_unique_id_sub"];
        $type                           = $_POST["type"] ?? '';
        $qty                            = $_POST["qty"];
        $id                             = category_item($item_unique_id_sub);
        $uom_unique_id                  = unit_name($id[0]['uom_unique_id']);
        $is_actice                      = $_POST["is_active_sub"];
        $remarks                        = $_POST["remarks"];
        $update_where                   = "";

        $columns            = [
            "prod_unique_id"                => $product_name,
            "so_unique_id"                  => $so_id,
            "group_unique_id"               => $group_unique_id_sub,
            "sub_group_unique_id"           => $sub_group_unique_id_sub_list,
            "category_unique_id"            => $category_unique_id_sub,
            "item_unique_id"                => $item_unique_id_sub,
            "type"                          => $type,  // ✅ store here
            "qty"                           => $qty,
            "uom_unique_id"                 => $uom_unique_id[0]['unique_id'],
            "is_active"                     => $is_actice,
            "remarks"                       => $remarks,
            "created_user_id"               => $user_id,
            "created"                       => $date,
            "unique_id"                     => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            'obom_list',
            [
                "COUNT(unique_id) AS count"
            ]
        ];
    $select_where = 'so_unique_id = "' . $so_id . '" 
                 AND type = "' . $type . '" 
                 AND to_list = "' . $to_list . '" 
                 AND is_delete = 0 
                 AND prod_unique_id = "' . $product_name . '"';


        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);
        //error_log(print_r($action_obj, true) ,3, "count.log");

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);
                
                unset($columns['created_user_id']);
                unset($columns['created']);
                $columns['type'] = $type;
                
                
                $columns['updated_user_id'] = $user_id;
                $columns['updated'] = $date;

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update('obom_list',$columns,$update_where);
                
                //error_log("action_obj: " . print_r($action_obj, true) . "\n", 3, "list.log");

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert('obom_list',$columns);
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
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
    
    case 'child_add_update':
        
        error_log(print_r($_POST, true) . "\n", 3, "log/child_logs/post.log");

        $prod_unique_id                 = $_POST["prod_unique_id"];
        $parent_unique_id               = $_POST["parent_unique_id"];
        $product_name                   = $_POST["product_name"];
        $so_id                          = $_POST["so_id"];
        $unique_id                      = $_POST["unique_id"];
        $group_unique_id_sub            = $_POST["group_unique_id_sub"];
        $sub_group_unique_id_sub_list   = $_POST["sub_group_unique_id_sub_list"];
        $category_unique_id_sub         = $_POST["category_unique_id_sub"];
        $item_unique_id_sub             = $_POST["item_unique_id_sub"];
        $type                           = $_POST["type"] ?? '';
        $remarks                        = $_POST["remarks"] ?? '';
        $qty                            = $_POST["qty"];
        $id                             = category_item($item_unique_id_sub);
        $uom_unique_id                  = unit_name($id[0]['uom_unique_id']);
        $is_actice                      = $_POST["is_active_sub"];
        $remarks                        = $_POST["remarks"];
        $update_where                   = "";

        $columns            = [
            "prod_unique_id"                => $prod_unique_id,
            "parent_unique_id"              => $parent_unique_id,
            "so_unique_id"                  => $so_id,
            "group_unique_id"               => $group_unique_id_sub,
            "sub_group_unique_id"           => $sub_group_unique_id_sub_list,
            "category_unique_id"            => $category_unique_id_sub,
            "item_unique_id"                => $item_unique_id_sub,
            "qty"                           => $qty,
            "remarks"                       => $remarks,
            "uom_unique_id"                 => $uom_unique_id[0]['unique_id'],
            "is_active"                     => $is_actice,
            "remarks"                       => $remarks,
            "unique_id"                     => unique_id($prefix)
        ];
        
        error_log(print_r($columns, true) . "\n", 3, "log/child_logs/column.log");

        // check already Exist Or not
        $table_details      = [
            'obom_child_table',
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'prod_unique_id ="'.$prod_unique_id.'" AND so_unique_id ="'.$so_id.'" AND parent_unique_id ="'.$parent_unique_id.'" AND  group_unique_id ="'.$group_unique_id_sub.'" AND  sub_group_unique_id ="'.$sub_group_unique_id_sub_list.'" AND  category_unique_id ="'.$category_unique_id_sub.'" AND  item_unique_id ="'.$item_unique_id_sub.'" AND is_delete = 0';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }
        
        error_log(print_r($select_where, true) . "\n", 3, "log/child_logs/where.log");

        $action_obj         = $pdo->select($table_details,$select_where);
        
        error_log(print_r($action_obj, true) . "\n", 3, "log/child_logs/action_obj.log");

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);
                
                unset($columns['created_user_id']);
                unset($columns['created']);
                // $columns['type'] = $type;
                
                
                // $columns['updated_user_id'] = $user_id;
                // $columns['updated'] = $date;

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update('obom_child_table',$columns,$update_where);
                
                error_log("action_obj_update: " . print_r($action_obj, true) . "\n", 3, "log/child_logs/list.log");

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert('obom_child_table',$columns);
                
                error_log("action_obj_insert: " . print_r($action_obj, true) . "\n", 3, "log/child_logs/list.log");
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];
        
        //error_log(print_r($json_array, true) . "\n", 3, "child_logs/json_array.log");
        echo json_encode($json_array);

    break;
    
    case 'product_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "prod";
        
        // Fetch Data
        $prod_unique_id = $_POST['prod_unique_id']; 
        $product_name = $_POST['product_name']; 
        $so_id = $_POST['so_id']; 
        $type  = $_POST['type'];

        $search     = $_POST['search']['value'];    
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "group_unique_id",
            "sub_group_unique_id",
            "category_unique_id",
            "item_unique_id",
            "qty",
            "uom_unique_id",
            "remarks",
            "is_active",
            "unique_id",
        ];
        $table_details  = [
            'obom_list'." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "so_unique_id"            => $so_id,
            // "is_active"              => 1,
            "is_delete"                 => 0,
            "type"                   => $type
        ];
        error_log(print_r($where, true), 3, "logs/where.log");
        
        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        error_log(print_r($result, true), 3, "logs/result.log");
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
            
            if ($type == 1) {
                $res_array = array_filter($res_array, function ($row) {
                    return $row['category_unique_id'] !== "689c7932650be49774";
                });
                // reset indexes after filtering
                $res_array = array_values($res_array);
            }

            $counter = $start + 1; // keep pagination offset
            foreach ($res_array as $key => $value) {
                // override s_no to be sequential after filtering
                $value['s_no'] = $counter++;
                
                $group_data                     = group_name($value['group_unique_id']);
                $sub_group_data                 = sub_group_name($value['sub_group_unique_id']);
                $category_data                  = category_name($value['category_unique_id']);
                $item_data                      = category_item($value['item_unique_id']);
                $uom_data                       = unit_name($value['uom_unique_id']);
                $value['group_unique_id']       = !empty($group_data[0]['group_name'])     ? $group_data[0]['group_name']     : '-';
                $value['sub_group_unique_id']   = !empty($sub_group_data[0]['sub_group_name']) ? $sub_group_data[0]['sub_group_name'] : '-';
                $value['category_unique_id']    = !empty($category_data[0]['category_name'])  ? $category_data[0]['category_name']  : '-';
                
                
                if ($type == 2 && strtoupper($value['category_unique_id']) == 'FABRICATION') {
                    $url = 'index.php?file=ordered_bom/obom_child'
                         . '&unique_id=' . $value['item_unique_id']
                         . '&so_unique_id=' . $so_id
                         . '&date=&form=&type=' . $type;
                
                    $windowName = 'viewWindow_' . $value['item_unique_id']; // unique per item
                
                    $itemName = !empty($item_data[0]['item_name']) ? $item_data[0]['item_name'] : '-';
                
                    $value['item_unique_id'] = '<a href="javascript:void(0);" 
                        onclick="window.open(\'' . $url . '\', \'' . $windowName . '\', \'width=1200,height=800,scrollbars=yes,resizable=yes\');">' 
                        . $itemName . '</a>';
                } else {
                    $value['item_unique_id'] = !empty($item_data[0]['item_name']) ? $item_data[0]['item_name'] : '-';
                }

                
                $value['uom_unique_id']         = !empty($uom_data[0]['unit_name'])      ? $uom_data[0]['unit_name']      : '-';
                $value['is_active']             = is_active_show($value['is_active']);
                $btn_edit                       = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete                     = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']             = $btn_edit.$btn_delete;
                $data[]                         = array_values($value);
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
    
case 'product_sub_datatable_child':
    // Function Name button prefix
    $btn_edit_delete = "prod";

    // Fetch Data
    $prod_unique_id = $_POST['prod_unique_id']; 
    $product_name   = $_POST['product_name']; 
    $parent_unique_id   = $_POST['parent_unique_id']; 
    $so_id          = $_POST['so_id']; 
    $type           = $_POST['type'];

    $search         = $_POST['search']['value'];    
    $length         = $_POST['length'];
    $start          = $_POST['start'];
    $draw           = $_POST['draw'];
    $limit          = $length;

    $data = [];

    if ($length == '-1') {
        $limit = "";
    }
    
    // Columns to select
    $columns = [
        "group_unique_id",
        "sub_group_unique_id",
        "category_unique_id",
        "item_unique_id",
        "qty",
        "uom_unique_id",
        "remarks",
        "is_active",
        "unique_id" // Needed for edit/delete buttons
    ];

    // Table and columns
    $table_details = [
        "obom_child_table",
        $columns
    ];

    // WHERE conditions
    $where = [
        "prod_unique_id" => $prod_unique_id,
        "so_unique_id"   => $so_id,
        "is_delete"      => 0
    ];

    // ORDER BY
    $order_by = "";

    // Use DISTINCT properly
    $sql_function = "SQL_CALC_FOUND_ROWS DISTINCT";

    // Fetch results
    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    error_log(print_r($result, true) . "\n", 3, "log/logs/result.log");
    $total_records = total_records();

    if ($result->status) {
        $res_array = $result->data;

        $counter = $start + 1; // pagination offset
        foreach ($res_array as $key => $value) {
            // Sequential numbering
           $new_value = ['s_no' => $counter++];

            // Fetch related names
            $group_data   = group_name($value['group_unique_id']);
            $sub_group_data = sub_group_name($value['sub_group_unique_id']);
            $category_data = category_name($value['category_unique_id']);
            $item_data    = category_item($value['item_unique_id']);
            $uom_data     = unit_name($value['uom_unique_id']);

            // Replace IDs with names
            $value['group_unique_id']     = !empty($group_data[0]['group_name']) ? $group_data[0]['group_name'] : '-';
            $value['sub_group_unique_id'] = !empty($sub_group_data[0]['sub_group_name']) ? $sub_group_data[0]['sub_group_name'] : '-';
            $value['category_unique_id']  = !empty($category_data[0]['category_name']) ? $category_data[0]['category_name'] : '-';

            // Special case: FABRICATION category → link
            if (strtoupper($value['category_unique_id']) == 'FABRICATION') {
                $url = 'index.php?file=standard_bom/view&unique_id=' . $value['item_unique_id'] . '&date=&form=';
                $windowName = 'viewWindow_' . $value['item_unique_id']; // unique per item
                $value['item_unique_id'] = '<a href="javascript:void(0);" 
                    onclick="window.open(\'' . $url . '\', \'' . $windowName . '\', \'width=1200,height=800,scrollbars=yes,resizable=yes\');">' 
                    . $item_data[0]['item_name'] . '</a>';
            } else {
                $value['item_unique_id'] = !empty($item_data[0]['item_name']) ? $item_data[0]['item_name'] : '-';
            }

            // UOM name
            $value['uom_unique_id'] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : '-';

            // Active status
            $value['is_active'] = is_active_show($value['is_active']);

            // Buttons
            $btn_edit   = btn_edit_child($btn_edit_delete, $value['unique_id']);
            $btn_delete = btn_delete_child($btn_edit_delete, $value['unique_id']);
            $value['unique_id'] = $btn_edit . $btn_delete;

            $new_value = array_merge($new_value, $value);
            
            // Push into $data
            $data[] = array_values($new_value);
        }

        // Final JSON
        $json_array = [
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data,
            "testing"         => $result->sql // debugging query
        ];
    } else {
        print_r($result);
    }

    echo json_encode($json_array);
break;
    
    // case 'copy_sbom_to_obom':
    //     $prod_unique_id = $_POST['product_id'] ?? '';
    //     $so_unique_id   = $_POST['so_id'] ?? '';
    //     $type           = $_POST['type'] ?? '';
    //     $to_copy           = $_POST['to_copy'] ?? '';
        
    //     $so_main_table = 'sales_order';
    //     $columns_main = ['so_type'];
    //     $where_main = ["unique_id" => $so_unique_id];
    
    //     $result_main = $pdo->select([$so_main_table, $columns_main], $where_main);
        
    //     if($result_main->status && !empty($result_main->data)){
    //         $so_type = $result_main->data[0]['so_type'];
    //     }
    
    //     if (!empty($prod_unique_id) && !empty($so_unique_id)) {
    
    //         // 1. Check if OBOM already exists
    //         $check_existing = $pdo->select(
    //             ['obom_list', ['unique_id']],
    //             ['so_unique_id' => $so_unique_id, 'is_delete' => 0, 'type' => $type, 'to_list' => 1]
    //         );
            
    //         // 2. Block copy if OBOM exists
    //         if ($check_existing->status && !empty($check_existing->data)) {
    //             echo json_encode(['status' => false, 'message' => 'OBOM already exists for this SO']);
    //             exit;
    //         }
            
    //         // 3. Block copy if OBOM does not exist but to_copy = 0
    //         if (empty($check_existing->data) && $to_copy === "0") {
    //             echo json_encode(['status' => false, 'message' => 'Copy not allowed as to_copy is 0']);
    //             exit;
    //         }
    
    //         // 2. Get SBOM sub-items for this product
    //         $sbom_table = 'product_sublist';
    //         $columns = [
    //             'group_unique_id',
    //             'sub_group_unique_id',
    //             'category_unique_id',
    //             'item_unique_id',
    //             'qty',
    //             'uom_unique_id',
    //             'remarks'
    //         ];
    //         $where = ['prod_unique_id' => $prod_unique_id];
    
    //         $sbom_result = $pdo->select([$sbom_table, $columns], $where);
    //         //error_log("sbom_result: " . print_r($sbom_result, true) . "\n", 3, "sbom.log");
    
    //         if ($sbom_result->status && !empty($sbom_result->data)) {
    
    //             // Start tracking insertion success
    //             $all_success = true;
    
    //             foreach ($sbom_result->data as $sbom_row) {
                    
    //                 //error_log(print_r($sbom_row, true) . "\n", 3, "sbom_row.log");
    
    //                 // 3. Insert into OBOM list
    //                 $obom_unique_id = unique_id();
    //                 $insert_data = $sbom_row;
    //                 $insert_data['prod_unique_id'] = $prod_unique_id;
    //                 $insert_data['unique_id']      = $obom_unique_id;
    //                 $insert_data['so_unique_id']   = $so_unique_id;
    //                 $insert_data['type']           = $type;
    //                 $insert_data['is_active']      = 1;
    //                 $insert_data['is_delete']      = 0;
    
    //                 // 4. If category matches and semi-finished exists, insert into OBOM CHILD TABLE
    //                 if ($type == 2 && $sbom_row['category_unique_id'] === '4c32b2db6c65196539') {
                        
    //                     //error_log("true" . "\n", 3, 'if_true.log');
    
    //                     // 4.1 Find all semi-finished sub-items where the current item is the parent
    //                     $semi_finished_result = $pdo->select(
    //                         ['product_sublist', [
    //                             'group_unique_id',
    //                             'sub_group_unique_id',
    //                             'category_unique_id',
    //                             'item_unique_id',
    //                             'qty',
    //                             'uom_unique_id',
    //                             'remarks',
    //                             'prod_unique_id'
    //                         ]],
    //                         [
    //                             'prod_unique_id' => $sbom_row['item_unique_id'],
    //                             'material_type'  => 'semi_finished',
    //                             'is_active'      => 1,
    //                             'is_delete'      => 0
    //                         ]
    //                     );
                        
    //                     //error_log(print_r($semi_finished_result, true) . "\n", 3, "semi_log.log");
    
    //                     if ($semi_finished_result->status && !empty($semi_finished_result->data)) {
    //                         foreach ($semi_finished_result->data as $child_row) {
    
    //                             $child_insert_data = [
    //                                 'unique_id'      => unique_id(),
    //                                 'parent_unique_id'   => $obom_unique_id,
    //                                 'type'   => $type,
    //                                 'so_unique_id'       => $so_unique_id,
    //                                 'prod_unique_id'     => $child_row['prod_unique_id'],
    //                                 'group_unique_id'    => $child_row['group_unique_id'],
    //                                 'sub_group_unique_id' => $child_row['sub_group_unique_id'],
    //                                 'category_unique_id' => $child_row['category_unique_id'],
    //                                 'item_unique_id'    => $child_row['item_unique_id'],
    //                                 'material_type'     => 'semi-finished',
    //                                 'qty'               => $child_row['qty'],
    //                                 'uom_unique_id'     => $child_row['uom_unique_id'],
    //                                 'remarks'           => $child_row['remarks'],
    //                                 'is_active'         => 1,
    //                                 'is_delete'         => 0,
    //                             ];
                                
                                
    //                             $child_insert_action = $pdo->insert('obom_child_table', $child_insert_data);
    //                             //error_log("obom_child_table: " . print_r($child_insert_data, true) . "\n", 3, "obom_child.log");
    //                             //error_log("obom_child_table_action: " . print_r($child_insert_action, true) . "\n", 3, "obom_child.log");
                                
    //                              if (!$child_insert_action->status) {
    //                                 // Child insert failed, stop and mark as failed
    //                                 echo json_encode(['status' => false, 'message' => 'Failed to insert OBOM child']);
    //                             }
    //                         }
    //                     }
    //                 }
                    
    //                 $insert_action = $pdo->insert('obom_list', $insert_data);                            
    //                 //error_log("obom_list: " . print_r($insert_data, true) . "\n", 3, "obom.log");
                    
    //                 if (!$insert_action->status) {
    //                     // Parent insert failed, stop immediately
    //                     echo json_encode(['status' => false, 'message' => 'Failed to insert OBOM list']);
    //                 }
                   
    //             }
    
    //             // If everything succeeded
    //             echo json_encode(['status' => true, 'message' => 'Copied successfully']);
    
    //         } else {
    //             echo json_encode(['status' => false, 'message' => 'No SBOM items found']);
    //         }
    
    //     } else {
    //         echo json_encode(['status' => false, 'message' => 'Invalid product or SO ID']);
    //     }
    // break;
    
    
    case 'copy_sbom_to_obom':
    $prod_unique_id = $_POST['product_id'] ?? '';
    $so_unique_id   = $_POST['so_id'] ?? '';
    $type           = $_POST['type'] ?? '';
    $to_copy        = $_POST['to_copy'] ?? '';

    $so_main_table = 'sales_order';
    $columns_main  = ['so_type'];
    $where_main    = ["unique_id" => $so_unique_id];

    $result_main = $pdo->select([$so_main_table, $columns_main], $where_main);
    $so_type = null;
    if ($result_main->status && !empty($result_main->data)) {
        $so_type = $result_main->data[0]['so_type'];
    }

    if (!empty($prod_unique_id) && !empty($so_unique_id)) {

        $check_existing = $pdo->select(
            ['obom_list', ['unique_id']],
            ['so_unique_id' => $so_unique_id, 'is_delete' => 0, 'type' => $type, 'to_list' => 1]
        );

        if ($check_existing->status && !empty($check_existing->data)) {
            echo json_encode(['status' => false, 'message' => 'OBOM already exists for this SO']);
            exit;
        }

        if (empty($check_existing->data) && $to_copy === "0") {
            echo json_encode(['status' => false, 'message' => 'Copy not allowed as to_copy is 0']);
            exit;
        }

        // ✅ 2. Only now soft delete existing OBOM + child rows
        $pdo->update(
            "obom_list",
            ["is_delete" => 1],
            ["so_unique_id" => $so_unique_id, "type" => $type, "is_delete" => 0]
        );
        $pdo->update(
            "obom_child_table",
            ["is_delete" => 1],
            ["so_unique_id" => $so_unique_id, "type" => $type, "is_delete" => 0]
        );

        // ---------- SOURCE DATA BASED ON so_type ----------
        if ($so_type == 1 || $so_type == 2) {
            $sbom_result = $pdo->select(
                ['product_sublist', [
                    'group_unique_id',
                    'sub_group_unique_id',
                    'category_unique_id',
                    'item_unique_id',
                    'qty',
                    'uom_unique_id',
                    'remarks'
                ]],
                ['prod_unique_id' => $prod_unique_id]
            );
            $source_data = $sbom_result->status ? $sbom_result->data : [];

        } elseif ($so_type == 3) {
            $so_result = $pdo->select(
                ['sales_order_sublist', [
                    'item_name_id',
                    'unit_name',
                    'quantity',
                    'subtask'
                ]],
                ['so_main_unique_id' => $so_unique_id, 'is_active' => 1, 'is_delete' => 0]
            );
            $source_data = $so_result->status ? $so_result->data : [];

        } else {
            echo json_encode(['status' => false, 'message' => 'Copy not allowed for this SO type']);
            exit;
        }

        // ---------- INSERT INTO OBOM ----------
        if (!empty($source_data)) {
            foreach ($source_data as $row) {
                $obom_unique_id = unique_id();

                if ($so_type == 3) {
                    // Lookup group/subgroup/category from item_master
                    $item_info = $pdo->select(
                        ['item_master', ['group_unique_id', 'sub_group_unique_id', 'category_unique_id']],
                        ['unique_id' => $row['item_name_id'], 'is_delete' => 0]
                    );

                    $group_id     = $item_info->status && !empty($item_info->data) ? $item_info->data[0]['group_unique_id'] : null;
                    $sub_group_id = $item_info->status && !empty($item_info->data) ? $item_info->data[0]['sub_group_unique_id'] : null;
                    $cat_id       = $item_info->status && !empty($item_info->data) ? $item_info->data[0]['category_unique_id'] : null;

                    $insert_data = [
                        'unique_id'          => $obom_unique_id,
                        'prod_unique_id'     => $prod_unique_id,
                        'so_unique_id'       => $so_unique_id,
                        'type'               => $type,
                        'group_unique_id'    => $group_id,
                        'sub_group_unique_id'=> $sub_group_id,
                        'category_unique_id' => $cat_id,
                        'item_unique_id'     => $row['item_name_id'],
                        'qty'                => $row['quantity'],
                        'uom_unique_id'      => $row['unit_name'],
                        'remarks'            => $row['subtask'],
                        'is_active'          => 1,
                        'is_delete'          => 0
                    ];
                } else {
                    $insert_data = $row;
                    $insert_data['unique_id']    = $obom_unique_id;
                    $insert_data['prod_unique_id'] = $prod_unique_id;
                    $insert_data['so_unique_id'] = $so_unique_id;
                    $insert_data['type']         = $type;
                    $insert_data['is_active']    = 1;
                    $insert_data['is_delete']    = 0;
                }

                // Insert OBOM
                $insert_action = $pdo->insert('obom_list', $insert_data);
                if (!$insert_action->status) {
                    echo json_encode(['status' => false, 'message' => 'Failed to insert OBOM list']);
                    exit;
                }

                // ---------- CHILD HANDLING ----------
                if (($so_type == 1 || $so_type == 2) && $type == 2 && $row['category_unique_id'] === '4c32b2db6c65196539') {
                    // semi-finished expansion
                    $semi_finished_result = $pdo->select(
                        ['product_sublist', [
                            'group_unique_id',
                            'sub_group_unique_id',
                            'category_unique_id',
                            'item_unique_id',
                            'qty',
                            'uom_unique_id',
                            'remarks',
                            'prod_unique_id'
                        ]],
                        [
                            'prod_unique_id' => $row['item_unique_id'],
                            'material_type'  => 'semi_finished',
                            'is_active'      => 1,
                            'is_delete'      => 0
                        ]
                    );

                    if ($semi_finished_result->status && !empty($semi_finished_result->data)) {
                        foreach ($semi_finished_result->data as $child_row) {
                            $pdo->insert('obom_child_table', [
                                'unique_id'        => unique_id(),
                                'parent_unique_id' => $obom_unique_id,
                                'type'             => $type,
                                'so_unique_id'     => $so_unique_id,
                                'prod_unique_id'   => $child_row['prod_unique_id'],
                                'group_unique_id'  => $child_row['group_unique_id'],
                                'sub_group_unique_id' => $child_row['sub_group_unique_id'],
                                'category_unique_id'  => $child_row['category_unique_id'],
                                'item_unique_id'   => $child_row['item_unique_id'],
                                'material_type'    => 'semi-finished',
                                'qty'              => $child_row['qty'],
                                'uom_unique_id'    => $child_row['uom_unique_id'],
                                'remarks'          => $child_row['remarks'],
                                'is_active'        => 1,
                                'is_delete'        => 0,
                            ]);
                        }
                    }

                } elseif ($so_type == 3) {
                    // check if SO item has SBOM in product_sublist
                    $child_sbom_result = $pdo->select(
                        ['product_sublist', [
                            'group_unique_id',
                            'sub_group_unique_id',
                            'category_unique_id',
                            'item_unique_id',
                            'qty',
                            'uom_unique_id',
                            'remarks',
                            'prod_unique_id'
                        ]],
                        [
                            'prod_unique_id' => $row['item_name_id'],
                            'is_active'      => 1,
                            'is_delete'      => 0
                        ]
                    );

                    if ($child_sbom_result->status && !empty($child_sbom_result->data)) {
                        foreach ($child_sbom_result->data as $child_row) {
                            $pdo->insert('obom_child_table', [
                                'unique_id'        => unique_id(),
                                'parent_unique_id' => $obom_unique_id,
                                'type'             => $type,
                                'so_unique_id'     => $so_unique_id,
                                'prod_unique_id'   => $child_row['prod_unique_id'],
                                'group_unique_id'  => $child_row['group_unique_id'],
                                'sub_group_unique_id' => $child_row['sub_group_unique_id'],
                                'category_unique_id'  => $child_row['category_unique_id'],
                                'item_unique_id'   => $child_row['item_unique_id'],
                                'material_type'    => 'child-sbom',
                                'qty'              => $child_row['qty'],
                                'uom_unique_id'    => $child_row['uom_unique_id'],
                                'remarks'          => $child_row['remarks'],
                                'is_active'        => 1,
                                'is_delete'        => 0,
                            ]);
                        }
                    }
                }
            }

            echo json_encode(['status' => true, 'message' => 'Copied successfully']);
        } else {
            echo json_encode(['status' => false, 'message' => 'No items found to copy']);
        }
    } else {
        echo json_encode(['status' => false, 'message' => 'Invalid product or SO ID']);
    }
    break;



    
    case 'prod_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update('obom_list',$columns,$update_where);

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
    
    case 'prod_delete_child':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update('obom_child_table',$columns,$update_where);

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
    
    case "prod_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "group_unique_id",
            "sub_group_unique_id",
            "category_unique_id",
            "item_unique_id",
            "qty",
            "prod_unique_id",
            "remarks",
            "is_active",
            "unique_id",
        ];
        $table_details  = [
            'obom_list',
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"   => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;
    
    case 'fetch_so':
        $type = $_POST['type'] ?? "";
    
        // fetch all SOs (active, approved, not deleted)
        $so_data = sales_order_type();
    
        // Step 1: Remove so_type = 0
        $so_data = array_filter($so_data, function($so){
            return isset($so['so_type']) && $so['so_type'] != 0;
        });
    
        // Step 2: If type = 2, exclude already used SOs in obom_list
        if (!empty($type)) {
            $obom_query = $pdo->select(
                ["obom_list", ["so_unique_id"]],
                ["type" => $type, "to_list" => 1, "is_delete" => 0]
            );
    
            if ($obom_query->status && !empty($obom_query->data)) {
                $used_so_ids = array_column($obom_query->data, "so_unique_id");
    
                $so_data = array_filter($so_data, function($so) use ($used_so_ids) {
                    return !in_array($so['unique_id'], $used_so_ids);
                });
            }
        }
    
        // Re-index
        $so_data = array_values($so_data);
    
        // Convert to select options
        $so_data = select_option($so_data, "Select Sales Order");
    
        if (!empty($so_data)) {
            echo json_encode([
                "status" => true,
                "data"   => $so_data
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "data"   => []
            ]);
        }
    break;

    
    case "prod_edit_child":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "group_unique_id",
            "sub_group_unique_id",
            "category_unique_id",
            "item_unique_id",
            "qty",
            "prod_unique_id",
            "remarks",
            "is_active",
            "unique_id",
        ];
        $table_details  = [
            'obom_child_table',
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"   => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;
    
    case "product_type":
        
        $vertical_id = $_POST['vertical_id'];
        
        $sub_group_unique_id_sub_list = product_type_name("", $vertical_id);
        
        $sub_group_unique_id_sub_list = select_option($sub_group_unique_id_sub_list, "Select");
        
        echo $sub_group_unique_id_sub_list;
        
    break;
    
    case "prod_names":
        
        $group = $_POST['group_unique_id'] ?? '';
        $sub_group = $_POST['sub_group_unique_id'] ?? '';
        $company = $_POST['company_unique_id'] ?? '';
        
        $product_options = product_name('', $group, $sub_group, $company);
        $product_options = select_option($product_options, "Select Product");
        
        echo $product_options;
        
    break;
    
case "fetch_so_prod":
    $so_id = $_POST['so_id'] ?? '';

    if (!empty($so_id)) {
        $so_table = 'sales_order_sublist';
        $columns = ['item_name_id'];
        $where = ["so_main_unique_id" => $so_id];
        
        $so_main_table = 'sales_order';
        $columns_main = ['so_type'];
        $where_main = ["unique_id" => $so_id];

        $result_main = $pdo->select([$so_main_table, $columns_main], $where_main);
        error_log(print_r($result_main, true), 3, "log/logs/fetch_prod_main.log");
        
        $so_type = '';
        if ($result_main->status && !empty($result_main->data)){
            $so_type = $result_main->data[0]['so_type'];
        }
        
        $result = $pdo->select([$so_table, $columns], $where);
        error_log(print_r($result, true), 3, "log/logs/fetch_prod.log");

        if ($result->status && !empty($result->data)) {
            $item_unique_id = $result->data[0]['item_name_id'];

            // Get product name & id from helper
            if ($so_type === "1" || $so_type === "2") {
                    // Product based SO
                    $product_data           = product_name($item_unique_id);
                    error_log(print_r($product_data, true), 3, "log/logs/prod.log");
                    $product_name = $product_data[0]['product_name'];
                    $product_id   = $product_data[0]['unique_id'];
                } elseif ($so_type === "3") {
                    // Item based SO
                    $product_data              = item_name_list($item_unique_id);
                    $product_name           = "Spare Items";
                    $product_id   = $product_data[0]['unique_id'];
                } else {
                    $product_name = "No items - Maintainance.";
                }

            // --- SBOM → OBOM copy logic here ---
            // For example:
            $sbom_items = $pdo->select(['sbom_list', ['component_id','qty']], ["product_id" => $product_id]);
            if ($sbom_items->status && !empty($sbom_items->data)) {
                foreach ($sbom_items->data as $comp) {
                    // Insert into OBOM table
                    $pdo->insert('obom_list', [
                        "so_id"        => $so_id,
                        "component_id" => $comp['component_id'],
                        "qty"          => $comp['qty']
                    ]);
                }
            }
            
            echo json_encode([
                "status"       => true,
                "type"         => $so_type,
                "product_name" => $product_name,
                "product_id"   => $product_id
            ]);
        } else {
            echo json_encode(["status" => false, "message" => "Product not found"]);
        }
    } else {
        echo json_encode(["status" => false, "message" => "Invalid SO ID"]);
    }
    break;


    
    default:
        
        break;
}

function get_next_product_code_number($counter_file = "product_code_counter.txt") {
    // Default starting number
    $last_number = 0;

    // Read existing number if file exists
    if (file_exists($counter_file)) {
        $last_number = (int)trim(file_get_contents($counter_file));
    }

    // Increment
    $next_number = $last_number + 1;

    // Save updated number back to file
    file_put_contents($counter_file, $next_number);

    // Format it to 3 digits with leading zeros
    return str_pad($next_number, 3, '0', STR_PAD_LEFT);
}

?>