<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table_group             = 'group_product_master';
$table              = 'product_master'; 
$sub_list_table     = 'product_sublist'; 
$item_table     = 'item_master'; 
$documents_upload   = 'standard_bom_uploads';


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
            error_log("POST: " . print_r($_POST, true) . "\n", 3, "post_log.txt");
    
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
            error_log("result 1: " . print_r($result_1, true) . "\n", 3, "res_1.log");
            
            if ($result_1->status && !empty($result_1->data[0]['product_code'])) {
                $pro_code_1 = $result_1->data[0]['product_code'];
            }
            
            $result_2 = $pdo->select([$table_2, $column_12], ["id" => $sub_group_unique_id]);
            error_log("result 2: " . print_r($result_2, true) . "\n", 3, "res_1.log");
    
            if($result_2->status && !empty($result_2->data[0]['product_code'])){
                $pro_code_2 = $result_2->data[0]['product_code'];
            }
            
            $result_3 = $pdo->select([$table_3, $column_3], ["unique_id" => $company_id]);
            error_log("result 3: " . print_r($result_3, true) . "\n", 3, "res_1.log");
            
            if($result_3->status && !empty($result_3->data[0]['company_code'])){
                $comp_code = $result_3->data[0]['company_code'];
            }
            
            error_log("result final: " . $pro_code_1 . " " . $pro_code_2 . " " . $comp_code . "\n", 3, "res_1.log");
            
            $product_code = $comp_code . "-" . $pro_code_1 . "-" . $pro_code_2 . "-" . $num;

            error_log("result final: " . $product_code . "\n", 3, "res_code.log");
    
            // Step 2: Check for duplicate
            $table_details = [$table, ["COUNT(unique_id) AS count"]];
    
            // ЁЯЫая╕П FIXED this line (broken quotes and missing AND)
            $select_where = 'group_unique_id = "' . $group_unique_id . '" AND sub_group_unique_id = "' . $sub_group_unique_id . '" AND product_name = "' . $product_name . '" AND is_delete = 0';
    
            if ($unique_id) {
                $select_where .= ' AND unique_id != "' . $unique_id . '"';
            }
    
            $debug_log[] = "Step 2: Duplicate check WHERE = $select_where";
    
            $action_obj = $pdo->select($table_details, $select_where);
    
            error_log("action obj (duplicate check): " . print_r($action_obj, true) . "\n", 3, "action_log.txt");
    
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
    
                error_log("update action obj: " . print_r($action_obj, true) . "\n", 3, "action_log.txt");
    
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
    
                error_log("insert action obj: " . print_r($action_obj, true) . "\n", 3, "action_log.txt");
    
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
    
        error_log("debug_log: " . print_r($debug_log, true) . "\n", 3, "error_log_cu.txt");
        error_log("json: " . print_r($json_array, true) . "\n", 3, "json_log.txt");
    
        echo json_encode($json_array);
        exit;
    
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
            $target_dir = "../../uploads/standard_bom/";
            $folder_path = "standard/";

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
            "bom_unique_id"              => $upload_unique_id,
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
            "bom_unique_id" => $upload_unique_id,
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
                        $image_path = "../blue_planet_beta/uploads/standard_bom/" . trim($image_file);
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
        "t.is_active"
    ];
    
    if (!empty($_POST['prod_unique_id'])) {
        // Direct filter when product is specified
        $table_details = [
            "(SELECT DISTINCT ps.prod_unique_id, ps.is_active
              FROM {$sub_list_table} ps
              WHERE ps.is_delete = '0'
                AND ps.prod_unique_id = '{$_POST['prod_unique_id']}'
            ) AS t, (SELECT @a:={$start}) AS a",
            $columns
        ];
    
    } else {
        // Original join + dynamic filters
        $table_details = [
            "(SELECT DISTINCT ps.prod_unique_id, ps.is_active
              FROM {$sub_list_table} ps
              LEFT JOIN {$table} pm 
                  ON pm.unique_id = ps.prod_unique_id
              LEFT JOIN {$item_table} it
                  ON it.unique_id = ps.prod_unique_id
              WHERE ps.is_delete = '0'"
                . (
                    (!empty($_POST['group_unique_id']) && !empty($_POST['sub_group_unique_id']) && !empty($_POST['company_unique_id']))
                    ? " AND pm.group_unique_id = '{$_POST['group_unique_id']}'
                        AND pm.sub_group_unique_id = '{$_POST['sub_group_unique_id']}'
                        AND pm.company_id = '{$_POST['company_unique_id']}'"
                    : (
                        ($extra = array_filter([
                            !empty($_POST['data_type']) ? "pm.data_type = '{$_POST['data_type']}'" : null,
                            !empty($_POST['group_unique_id']) ? "pm.group_unique_id = '{$_POST['group_unique_id']}'" : null,
                            !empty($_POST['sub_group_unique_id']) ? "pm.sub_group_unique_id = '{$_POST['sub_group_unique_id']}'" : null,
                            !empty($_POST['company_unique_id']) ? "pm.company_id = '{$_POST['company_unique_id']}'" : null,
                            !empty($_POST['item_category']) ? "it.category_id = '{$_POST['item_category']}'" : null
                        ]))
                        ? " AND " . implode(" AND ", $extra)
                        : ""
                    )
                )
            . ") AS t, (SELECT @a:={$start}) AS a",
            $columns
        ];

    }
    
    // $where = "1"; // datatable wrapper
    
    $where = "0"; // default: no rows

    if (!empty($product_ids)) {
        $conditions = [];
        foreach (array_unique($product_ids) as $pid) {
            $pid = preg_replace('/[^a-zA-Z0-9_-]/', '', $pid); // basic sanitization
            $conditions[] = "prod_unique_id = '$pid'";
        }
        $where = '(' . implode(' OR ', $conditions) . ')';
    }
    

 // already handled delete in subquery

    $order_column = $_POST["order"][0]["column"];
    $order_dir    = $_POST["order"][0]["dir"];

    // Datatable Ordering 
    $order_by = datatable_sorting($order_column, $order_dir, $columns);

    // Datatable Searching
    $search_sql = datatable_searching($search, $columns);

    if ($search_sql) {
        if ($where) {
            $where .= " AND ";
        }
        $where .= $search_sql;
    }

    $sql_function = "SQL_CALC_FOUND_ROWS";

    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    error_log("datatable result: " . print_r($result, true) . "\n", 3, "dtble.log");

    $total_records = total_records();

    if ($result->status) {
        $res_array = $result->data;
        error_log("res_array: " . print_r($res_array, true) . "\n", 3, "res_array.log");

        foreach ($res_array as $key => $value) {
            $prod_unique_id = $value['prod_unique_id'];
            error_log("puid: " . $prod_unique_id . "\n", 3, "puid.log");

            // Get product name from prod_unique_id
            $prod_data = product_name($prod_unique_id);
            error_log("prod_data: " . print_r($prod_data, true) . "\n", 3, "prod.log");
            $product_name = !empty($prod_data[0]['product_name']) ? $prod_data[0]['product_name'] : '-';
            
            if ($product_name == '-'){
                $item_data = product_name_semi_finished($prod_unique_id);
                $product_name = !empty($item_data[0]['item_name']) ? $item_data[0]['item_name'] : '-';
            }

            // Buttons
            $btn_views  = btn_views_dev($folder_name, $prod_unique_id);
            $btn_update = btn_update($folder_name, $prod_unique_id);
            $btn_upload = btn_docs($folder_name, $prod_unique_id);

            // $btn_toggle = ($value['is_active'] == 1)
            //     ? btn_toggle_on($folder_name, $prod_unique_id)
            //     : btn_toggle_off($folder_name, $prod_unique_id);

            // Prepare row
            $row = [];
            $row[] = $value['s_no'];
            $row[] = $product_name;
            $row[] = $btn_views;
            $row[] = $btn_update . $btn_toggle.$btn_upload;

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

    error_log("json: " . print_r($json_array, true) . "\n", 3, "json.log");
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
        $type = $_POST['type'];
        $sub_group_name_options = "";
        $msg = "";
        if($type == 1){
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select";
        } else if($type == 2){
            $sub_group_name_options  = category_name("","",$group_id);
            $msg = "Select";
        } else if($type == 3){
            $sub_group_name_options  = category_item("","","",$group_id);
            $msg = "Select";
        } else {
            $sub_group_name_options  = product_type_name("",$group_id);
            $msg = "Select the sub groups";
        }
        $sub_group_name_options  = select_option($sub_group_name_options,$msg);
        echo $sub_group_name_options;
        
        break;
        
        
    case 'product_add_update':

        $prod_unique_id                 = $_POST["prod_unique_id"];
        $product_name                   = $_POST["product_name"];
        $unique_id                      = $_POST["unique_id"];
        $group_unique_id_sub            = $_POST["group_unique_id_sub"];
        $sub_group_unique_id_sub_list   = $_POST["sub_group_unique_id_sub_list"];
        $category_unique_id_sub         = $_POST["category_unique_id_sub"];
        $item_unique_id_sub             = $_POST["item_unique_id_sub"];
        $material_type = $_POST["material_type"] ?? '';
        $qty                            = $_POST["qty"];
        $id                 = category_item($item_unique_id_sub);
        $uom_unique_id      = unit_name($id[0]['uom_unique_id']);
        $is_actice                      = $_POST["is_active_sub"];
        $remarks                        = $_POST["remarks"];
        $update_where                   = "";

        $columns            = [
            "prod_unique_id"                => $product_name,
            "group_unique_id"               => $group_unique_id_sub,
            "sub_group_unique_id"           => $sub_group_unique_id_sub_list,
            "category_unique_id"            => $category_unique_id_sub,
            "item_unique_id"                => $item_unique_id_sub,
            "material_type"         => $material_type,  // ✅ store here
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
            $sub_list_table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'prod_unique_id ="'.$product_name.'" AND  group_unique_id ="'.$group_unique_id_sub.'" AND  sub_group_unique_id ="'.$sub_group_unique_id_sub_list.'" AND  category_unique_id ="'.$category_unique_id_sub.'" AND  item_unique_id ="'.$item_unique_id_sub.'" AND is_delete = 0';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);

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
                $columns['material_type'] = $material_type;
                
                
                $columns['updated_user_id'] = $user_id;
                $columns['updated'] = $date;

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($sub_list_table,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($sub_list_table,$columns);
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
    
    case 'product_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "prod";
        
        // Fetch Data
        $prod_unique_id = $_POST['prod_unique_id']; 
        $product_name = $_POST['product_name']; 

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
            $sub_list_table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "prod_unique_id"    => $product_name,
            // "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        
        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $group_data                     = group_name($value['group_unique_id']);
                $sub_group_data                 = sub_group_name($value['sub_group_unique_id']);
                $category_data                  = category_name($value['category_unique_id']);
                $item_data                      = category_item($value['item_unique_id']);
                $uom_data                       = unit_name($value['uom_unique_id']);
                $value['group_unique_id']       = !empty($group_data[0]['group_name'])     ? $group_data[0]['group_name']     : '-';
                $value['sub_group_unique_id']   = !empty($sub_group_data[0]['sub_group_name']) ? $sub_group_data[0]['sub_group_name'] : '-';
                $value['category_unique_id']    = !empty($category_data[0]['category_name'])  ? $category_data[0]['category_name']  : '-';
                
            if (strtoupper($value['category_unique_id']) == 'FABRICATION') {
            $url = 'index.php?file=' . $folder_name . '/view&unique_id=' . $value['item_unique_id'] . '&date=&form=';
            $windowName = 'viewWindow_' . $value['item_unique_id']; // unique per item
            $value['item_unique_id'] = '<a href="javascript:void(0);" 
                onclick="window.open(\'' . $url . '\', \'' . $windowName . '\', \'width=1200,height=800,scrollbars=yes,resizable=yes\');">' 
                . $item_data[0]['item_name'] . '</a>';
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
    
    case 'prod_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($sub_list_table,$columns,$update_where);

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
            $sub_list_table,
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