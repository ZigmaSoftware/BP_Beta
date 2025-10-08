<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = 'sales_order'; 
$sub_table          = 'sales_order_sublist'; 
$documents_upload   = 'so_uploads';


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
    
    case "createupdate":
        $approve_status         = $_POST['approve_status'];
        $approve_remarks        = $_POST['approve_remarks'];
        $unique_id              = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();
        
        session_start();
        
        $approved_by = $_SESSION['sess_user_id'];
       
        $columns = [
            "unique_id"             => $unique_id,
            "approve_status"        => $approve_status,
            "approved_by"           => $approved_by,
            "approve_remarks"       => $approve_remarks
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
            // INSERT mode — generate PR number
            $company_code_arr = "";
            $company_code = "";
            $prefix = "";
            $acc_year = "";
            $company_code_arr = company_code("", "$company_name");
            $company_code = $company_code_arr[0]["company_code"];
    
            $acc_year = $_SESSION["acc_year"];
            $prefix = "SO/{$company_code}/{$acc_year}/";
    
            $bill_no = batch_creation($table, $company_name, $prefix , $conns);
            // echo $bill_no;
            $columns["sales_order_no"] = $bill_no;
    
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
            $target_dir = "../../uploads/sales_order_2/";
            $folder_path = "sales_order_2/";

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
            "so_unique_id"               => $upload_unique_id,
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
            "so_unique_id"               => $upload_unique_id,
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
                        $image_path = "../blue_planet_beta/uploads/sales_order_2/" . trim($image_file);
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
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;
		
		$from_date 		= $_POST['from_date'];
		$to_date 		= $_POST['to_date'];
		$company_name 	= $_POST['company_name'];
		$customer_name 	= $_POST['customer_name'];
		$status 		= $_POST['status'];
		$approve_status 		= $_POST['approve_status'];

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no", 
            "entry_date",
            "sales_order_no",
            "company_id",
            "customer_id",
            "so_type",
            "status",
            "unique_id",
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";
        
       
            $conditions = [];
        
            if (!empty($from_date) && !empty($to_date)) {
                $conditions[] = "entry_date >= '{$from_date}' AND entry_date <= '{$to_date}'";
            }
            if (!empty($company_name)) {
                $conditions[] = "company_id = '{$company_name}'";
            }
            if (!empty($customer_name)) {
                $conditions[] = "customer_id = '{$customer_name}'";
            }
            if (!empty($status)) {
                $conditions[] = "status = '{$status}'";
            }
            if (isset($approve_status) && $approve_status !== "") {
                $conditions[] = "approve_status = '{$approve_status}'";
            }
            
            if (!empty($conditions)) {
                $where .= " AND " . implode(" AND ", $conditions);
            }
            
        error_log("where: " . print_r($where, true) . '\n' ,3, "where.log");
    

        
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
        
        
        $so_type_options = [
        1 => [
            "unique_id" => "1",
            "value" => "product"
        ],
        2 => [
            "unique_id" => "2",
            "value" => "project"
        ],
        3 => [
            "unique_id" => "3",
            "value" => "spare"
        ],
        4 => [
            "unique_id" => "4",
            "value" => "service"
        ],
    ];
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();
        
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
                $company_data                   = company_name($value['company_id']);
                $value['company_id']            = $company_data[0]['company_name'];
                
                $customer_data                  = customers($value['customer_id']);
                $value['customer_id']           = $customer_data[0]['customer_name'];
                
                $value['so_type']                = $so_type_options[$value['so_type']]['value'];
                
                $value['status']                = $approve_status_options[$value['status']]['value'];
                
                $btn_view  = btn_views($folder_name, $value['unique_id']);
                $btn_print = btn_prints($folder_name, $value['unique_id']);
                $btn_update                     = btn_update($folder_name,$value['unique_id']);
                $btn_delete                     = btn_delete($folder_name,$value['unique_id']);
                $btn_upload                     = btn_docs($folder_name, $value['unique_id']);
                
                $approve_status = fetch_approval($value['unique_id']);
                error_log("fetch_approval result for {$value['unique_id']}: " . print_r($approve_status, true) . "\n", 3, "app.log");
                
                // check what exactly is inside approval_status (if result is array)
                if (is_array($approve_status) && isset($approve_status['approval_status'])) {
                    error_log("approval_status for {$value['unique_id']}: " . $approve_status['approve_status'] . "\n", 3, "app.log");
                } else {
                    error_log("approval_status not found or invalid structure for {$value['unique_id']}\n", 3, "app.log");
                }

                if ($approve_status == 0) {
                    // not yet approved → allow update/delete
                    $approve_status     = "<span class='text-warning'>Pending</span>";
                    $value['unique_id'] = $btn_update . $btn_delete . $btn_upload;
                } elseif ($approve_status == 1) {
                    // approved → only upload
                    $approve_status     = "<span class='text-success'>Approved</span>";
                    $value['unique_id'] = $btn_upload;
                } elseif ($approve_status == 2) {
                    // rejected → allow update + upload
                    $approve_status     = "<span class='text-danger'>Rejected</span>";
                    $value['unique_id'] = $btn_upload;
                } else {
                    // fallback
                    $approve_status     = "Unknown";
                    $value['unique_id'] = $btn_upload;
                }
                
                $row = array_values($value);
                
                // insert approve_status before last element
                array_splice($row, -1, 0, $approve_status);
                array_splice($row, -1, 0, [$btn_view, $btn_print]);
                
                $data[] = $row;            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
    
    
    case 'delete':

    $unique_id = $_POST['unique_id'];
    $remarks   = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    $columns = [
        "is_delete"          => 1,
        "is_delete_remarks"  => $remarks
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

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
        
        
    case "so_sub_add_update":

        $main_unique_id         = $_POST["main_unique_id"];
        $sublist_unique_id      = $_POST["sublist_unique_id"];
        
        $product_unique_id      = $_POST["product_unique_id"];
        $uom                    = $_POST["uom"];
        $qty                    = $_POST["qty"];
        $rate                   = $_POST["rate"];
        // $discount               = $_POST["discount"];
        $tax                    = $_POST["tax"];
        $amount                 = $_POST["amount"];
        $subtask                = $_POST["subtask"];

        $columns = [
            "so_main_unique_id"     => $main_unique_id,
            "item_name_id"          => $product_unique_id,
            "unit_name"             => $uom,
            "quantity"              => $qty,
            "rate"                  => $rate,
            // "discount"              => $discount,
            "tax_id"                => $tax,
            "amount"                => $amount,
            "subtask"               => $subtask
        ];
        
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
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);

    break;
    
    case "so_sublist_datatable":
        $main_unique_id = $_POST["main_unique_id"];
        $btn_prefix     = "so_sub";
    
        $columns = [
            "@a:=@a+1 as s_no",
            "item_name_id",
            "unit_name",
            "quantity",
            "rate",
            // "discount",
            "tax_id",
            "amount",
            "subtask",
            "unique_id"
        ];
    
        $table_details = [
            "$sub_table, (SELECT @a:=0) as a",
            $columns
        ];
    
        $where = [
            "so_main_unique_id" => $main_unique_id,
            "is_delete" => 0
        ];
    
        $result = $pdo->select($table_details, $where);
        // print_r($result);
        // die();
        $data = [];
    
        if ($result->status) {
            foreach ($result->data as $row) {
                
                if ($so_type === "1" || $so_type === "2" ) {
                    // Product based SO
                    $product_data           = product_name($row['item_name_id']);
                    $row['item_name_id']    = $product_data[0]['product_name'] ?? $row['item_name_id'];
                
                } elseif ($so_type === "3") {
                    // Item based SO
                    $item_data              = item_name_list($row['item_name_id']);
                    $row['item_name_id']    = ($item_data[0]['item_name'] ?? '') . '/' . ($item_data[0]['item_code'] ?? '');
                
                } else {
                    // Mixed type → first try item, fallback to product
                    $item_data = item_name_list($row['item_name_id']);
                    if (!empty($item_data)) {
                        $row['item_name_id'] = ($item_data[0]['item_name'] ?? '') . '/' . ($item_data[0]['item_code'] ?? '');
                    } else {
                        $product_data        = product_name($row['item_name_id']);
                        $row['item_name_id'] = $product_data[0]['product_name'] ?? $row['item_name_id'];
                    }
                }
                
                $unit_data              = unit_name($row['unit_name']);
                $row['unit_name']       = $unit_data[0]['unit_name']; 
                
                $tax_data               = tax($row['tax_id']);
                $row['tax_id']          = $tax_data[0]['tax_name']; 
                
                $edit                   = btn_edit($btn_prefix, $row["unique_id"]);
                $del                    = btn_delete($btn_prefix, $row["unique_id"]);
    
                $row["unique_id"]       = $edit . $del;
                $data[]                 = array_values($row);
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

    case "so_sub_edit":
        $unique_id = $_POST["unique_id"];
    
        $columns = [
            "so_main_unique_id",
            "item_name_id",
            "unit_name",
            "quantity",
            "rate",
            // "discount",
            "tax_id",
            "amount",
            "subtask",
            "unique_id"
            
        ];
    
        $table_details = [
            $sub_table,
            $columns
        ];
    
        $where = [
            "unique_id" => $unique_id,
            "is_delete" => 0
        ];
    
        $result = $pdo->select($table_details, $where);
    
        echo json_encode([
            "status" => $result->status,
            "data"   => $result->status ? $result->data[0] : [],
            "msg"    => $result->status ? "edit_data" : "error",
            "error"  => $result->error
        ]);
    break;
    
    case "so_sub_delete":
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
            "quantity AS qty",
            "quantity",
            "uom",
            "budgetary_rate",
            "item_remarks",
            "required_delivery_date",
            "status",
            "reason",
            "new_quantity",
            "unique_id"
        ];
    
        $table_details  = [
            $table_sub . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
    
        $where = [
            "main_unique_id" => $unique_id,
            "is_delete" => '0'
        ];
    
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];
    
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
    
        $sql_function   = "SQL_CALC_FOUND_ROWS";
    
        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();
    
    
        $main_data = [];
        
        $extra_result = $pdo->select("purchase_requisition", "unique_id = " . "'".$id."'");
        
        
        if ($extra_result->status && !empty($extra_result->data)) {
            $main = $extra_result->data[0];
    
            $current_date = date('d-m-Y');
            
            $main_data['pr_number']        = $main['pr_number'];
            $main_data['date']              = $current_date;
            
            $main_data['requisition_date']  = date("d-m-Y", strtotime($main['requisition_date']));
            $main_data['requisition_for']   = $main['requisition_for'];
            $main_data['requisition_type']  = $main['requisition_type'];
            
    
            $company_data                   = company_name($main['company_id']);
            $main_data['company_id']        = $company_data[0]['company_name'];
    
            $project_data                   = project_name($main['project_id']);
            $main_data['project_id'] = $project_data[0]['project_code'] . " / " . $project_data[0]['project_name'];
            
        }

    
        if ($result->status) {
            $res_array = $result->data;
    
            foreach ($res_array as $key => $value) {
                $item_data = item_name_list($value["item_code"]);
                $value["item_code"] = isset($item_data[0]["item_name"], $item_data[0]["item_code"])
                    ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
                    : "-";
                
                // $item_data = item_name_list($value['item_code']);
                // $value['item_code'] = $item_data[0]['item_code'];
                // $item_code_display = isset($item_data[0]["item_name"]) && isset($item_data[0]["item_code"]) ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"] : "-";
                $uom_data = unit_name($value["uom"]);
                $value["uom"] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";
        
                $value['required_delivery_date'] = (!empty($value['required_delivery_date']) && $value['required_delivery_date'] != "0000-00-00") ? date("d-m-Y", strtotime($value['required_delivery_date'])) : "-";
    
                    if ($value['reason'] == "") {
                        $value['reason'] = "-";
                    }
                        
                    if ($value['status'] == "1") {
                        $status_display = "<span style='color: green; font-weight: bold;'>Approved</span>";
                    } elseif ($value['status'] == "2") {
                        $status_display = "<span style='color: red; font-weight: bold;'>Cancelled</span>";
                    }
                    else {
                        
                        $status_display = '<select class="form-control status-select" id="status_val_' . $value['unique_id'] . '" onchange="handle_status(this.value, \''.$value['unique_id'].'\')">
                                                <option value="">Select the Status</option>
                                                <option value="1">Approve</option>
                                                <option value="2">Cancel</option>
                                            </select>

                                            <div id="cancelReasonDiv_'.$value['unique_id'].'" style="display: none; margin-top: 10px;">
                                                <textarea id="cancelReason_' . $value['unique_id'] . '" " placeholder="Enter reason for cancellation" class="form-control" rows="4"></textarea>
                                                <button type="button" class="btn btn-primary mt-2" id="submitCancelReason" onclick="handle_status(document.getElementById(\'status_val_' . $value['unique_id'] . '\').value, \'' . $value['unique_id'] . '\', document.getElementById(\'cancelReason_' . $value['unique_id'] . '\').value)">Submit Reason</button>
                                            </div>';
                    }
                   
                   
                    if ($value['status'] === null || $value['status'] === "") {
                        $quantity_display = '<input type="number" class="form-control quantity-input" id="quantity_'.$value['unique_id'].'" value="'.htmlspecialchars($value['quantity']).'" min="1">';
                    } else {
                        $display_quantity = !empty($value['new_quantity']) ? $value['new_quantity'] : $value['quantity'];
                        $quantity_display = '<span>'.htmlspecialchars($display_quantity).'</span>';
                    }
                       
                    $value['quantity'] = $quantity_display;
                    $value['status'] = $status_display;
                    $value['budgetary_rate'] = $value['budgetary_rate'];


                $data[]             = array_values($value);
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
    
    
    case 'handle_status':

    $selectedValue      = $_POST['selectedValue'];
    $cancelReason       = $_POST['cancelReason'];
    $new_quantity       = $_POST['quantity'];
    $unique_id          = $_POST['unique_id'];

    $table_sub          = "purchase_requisition_items";

    $columns            = [
        "status"        => $selectedValue,
        "reason"        => $cancelReason,
        "new_quantity"  => $new_quantity
    ];

    if ($unique_id) {
        $update_where   = [
            "unique_id" => $unique_id
        ];

        $action_obj     = $pdo->update($table_sub, $columns, $update_where);
    } 

    if ($action_obj->status) {
        $status     = $action_obj->status;
        $data       = $action_obj->data;
        $error      = "";
        $sql        = $action_obj->sql;

        $msg        = "update";
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

function fetch_approval($unique_id){
    global $pdo;
    
    $table = "sales_order";
    
    $columns = [
        "approve_status"  
    ];
    
    $table_details = [
        $table,
        $columns
    ];
    
    $where = ["unique_id" => $unique_id];
    
    $result = $pdo->select($table_details, $where);
    error_log("result: " . print_r($result, true) . '\n' ,3, "app.log");
    
     if ($result->status && !empty($result->data)) {
        return $result->data[0]['approve_status'];; // returns first row
    } else {
        return 0;
    }
}

?>