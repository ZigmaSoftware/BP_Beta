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

case 'product_type_add':
    try {
        $vertical_id   = trim($_POST['vertical_id'] ?? '');
        $product_type  = trim($_POST['product_type'] ?? '');
        $product_code  = strtoupper(trim($_POST['product_code'] ?? ''));

        // Basic validation
        if ($product_type === '' || $product_code === '') {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => "Missing product_type or product_code"
            ]);
            exit;
        }
        if (!preg_match('/^[A-Z0-9]{1,6}$/', $product_code)) {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => "Invalid product code format (A–Z / 0–9, 1–6 chars)"
            ]);
            exit;
        }

        // NULL-ify vertical_id if empty/"null"
        $verticalParam = ($vertical_id === '' || strcasecmp($vertical_id, 'null') === 0) ? null : $vertical_id;

        // Duplicate check via wrapper (NULL-safe)
        $dup_tbl   = ['product_type', ['COUNT(id) AS cnt']];
        $dup_where = ($verticalParam === null)
            ? 'vertical_id IS NULL AND (product_type = "' . addslashes($product_type) . '" OR product_code = "' . addslashes($product_code) . '")'
            : 'vertical_id = "' . addslashes($verticalParam) . '" AND (product_type = "' . addslashes($product_type) . '" OR product_code = "' . addslashes($product_code) . '")';

        $dup_res = $pdo->select($dup_tbl, $dup_where);
        if (!$dup_res->status) {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => $dup_res->error ?: "Duplicate check failed"
            ]);
            exit;
        }
        if ((int)($dup_res->data[0]['cnt'] ?? 0) > 0) {
            echo json_encode([
                "status" => false,
                "msg"    => "already",
                "error"  => "Product Type or Code already exists under this Group."
            ]);
            exit;
        }

        // Supply the columns your wrapper injects so binding succeeds
        // (Make sure these keys match exactly what your wrapper expects.)
        $acc_year        = $_SESSION['acc_year']        ?? date('Y');         // VARCHAR(50) in your table; year as string OK
        $session_id_val  = session_id()                 ?: ($_SESSION['session_id'] ?? null);
        $sess_user_type  = $_SESSION['sess_user_type']  ?? null;
        $sess_user_id    = $_SESSION['sess_user_id']    ?? null;
        $sess_company_id = $_SESSION['sess_company_id'] ?? null;
        $sess_branch_id  = $_SESSION['sess_branch_id']  ?? null;

        // Insert via wrapper — include ONLY real + wrapper-required columns
        $columns = [
            "product_type"     => $product_type,
            "product_code"     => $product_code,
            "vertical_id"      => $verticalParam,

            // wrapper-injected audit/session fields:
            "acc_year"         => $acc_year,
            "session_id"       => $session_id_val,
            "sess_user_type"   => $sess_user_type,
            "sess_user_id"     => $sess_user_id,
            "sess_company_id"  => $sess_company_id,
            "sess_branch_id"   => $sess_branch_id
        ];

        $ins = $pdo->insert('product_type', $columns);

        if (!$ins->status) {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => $ins->error ?: "Insert failed",
                "sql"    => $ins->sql ?? null
            ]);
            exit;
        }

        // New id from wrapper (fallback to last_insert_id if needed)
        $newId = $ins->data['id'] ?? ($ins->last_insert_id ?? null);

        echo json_encode([
            "status" => true,
            "msg"    => "create",
            "data"   => [
                "id"   => $newId,
                "text" => $product_type
            ],
            "sql"    => $ins->sql ?? null
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "status" => false,
            "msg"    => "error",
            "error"  => $e->getMessage()
        ]);
        exit;
    }
break;




    case 'datatable':
        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no", 
            "group_unique_id",
            "sub_group_unique_id",
            "company_id",
            "product_name",
            "description",
            "is_active",
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
        
        if (!empty($_POST['group_unique_id']) && !empty($_POST['sub_group_unique_id']) && !empty($_POST['company_unique_id'])) {
            $where .= " AND group_unique_id = '{$_POST['group_unique_id']}' AND sub_group_unique_id = '{$_POST['sub_group_unique_id']}' AND company_id = '{$_POST['company_unique_id']}'";
        } else {
            $conditions = [];
        
            if (!empty($_POST['data_type'])) {
                $conditions[] = "data_type = '{$_POST['data_type']}'";
            }
            if (!empty($_POST['group_unique_id'])) {
                $conditions[] = "group_unique_id = '{$_POST['group_unique_id']}'";
            }
            if (!empty($_POST['sub_group_unique_id'])) {
                $conditions[] = "sub_group_unique_id = '{$_POST['sub_group_unique_id']}'";
            }
            if (!empty($_POST['company_unique_id'])) {
                $conditions[] = "company_id = '{$_POST['company_unique_id']}'";
            }
        
            if (!empty($conditions)) {
                $where .= " AND " . implode(" AND ", $conditions);
            }
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
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        error_log("datatable result: " . print_r($result, true) . "\n", 3, "dtble.log");
        
        $total_records  = total_records();
        
        if ($result->status) {

            $res_array      = $result->data;
            error_log("res_array: " . print_r($res_array, true) . "\n", 3, "res_array.log");

            foreach ($res_array as $key => $value) {
                
                error_log("value: " . print_r($value, true) . "\n", 3, "values.log");
                $group_data                 = product_group_name($value['group_unique_id']);
                $value['group_unique_id']   = !empty($group_data[0]['product_vertical']) ? $group_data[0]['product_vertical'] : '-';
                $sub_group_data                 = product_type_name($value['sub_group_unique_id']);
                $value['sub_group_unique_id']   = !empty($sub_group_data[0]['product_type']) ? $sub_group_data[0]['product_type'] : '-';
                $company_data                 = company_name($value['company_id']);
                error_log("comp: " . print_r($company_data, true) . "\n", 3, "comp.log");
                if (empty($value['company_id'])) {
                    // company_id is "", null, 0, "0", false, or not set
                    $value['company_id'] = '-';
                } else {
                    $value['company_id'] = !empty($company_data[0]['company_name'])
                        ? $company_data[0]['company_name']
                        : '-';
                }

                if($value['description'] == ' ' || empty($value['description']) || $value['description'] == ''){
                    $description = '-';
                } else {
                    $description = $value['description'];
                }
                
                $value['description']           = $description;
               $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
            
                $value['is_active'] = ($value['is_active'] == 1)
                    ? "<span style='color: green'>Active</span>"
                    : "<span style='color: red'>In Active</span>";
            
                $value['unique_id'] = $btn_update . $btn_toggle;
            
                $data[] = array_values($value);
                
                error_log("data: " . print_r($value, true) . "\n", 3, "data.log");
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            $json_array = [
                "error" => true,
                "message" => "Query failed",
                "details" => $result
            ];
        }
        error_log("json: " . print_r($json_array, true) . "\n" , 3, "json.log");
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
        $unique_id                      = $_POST["unique_id"];
        $group_unique_id_sub            = $_POST["group_unique_id_sub"];
        $sub_group_unique_id_sub_list   = $_POST["sub_group_unique_id_sub_list"];
        $category_unique_id_sub         = $_POST["category_unique_id_sub"];
        $item_unique_id_sub             = $_POST["item_unique_id_sub"];
        $qty                            = $_POST["qty"];
        $id                 = category_item($item_unique_id_sub);
        $uom_unique_id      = unit_name($id[0]['uom_unique_id']);
        $is_actice                      = $_POST["is_active_sub"];
        $remarks                        = $_POST["remarks"];
        $update_where                   = "";

        $columns            = [
            "prod_unique_id"                => $prod_unique_id,
            "group_unique_id"               => $group_unique_id_sub,
            "sub_group_unique_id"           => $sub_group_unique_id_sub_list,
            "category_unique_id"            => $category_unique_id_sub,
            "item_unique_id"                => $item_unique_id_sub,
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
        $select_where       = 'prod_unique_id ="'.$prod_unique_id.'" AND  group_unique_id ="'.$group_unique_id_sub.'" AND  sub_group_unique_id ="'.$sub_group_unique_id_sub_list.'" AND  category_unique_id ="'.$category_unique_id_sub.'" AND  item_unique_id ="'.$item_unique_id_sub.'" AND is_delete = 0';

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
            "prod_unique_id"    => $prod_unique_id,
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
                $value['item_unique_id']        = !empty($item_data[0]['item_name'])      ? $item_data[0]['item_name']      : '-';
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
    
   case 'product_vertical_add':
    try {
        $product_vertical = trim($_POST['product_vertical'] ?? '');
        $product_code     = strtoupper(trim($_POST['product_code'] ?? ''));

        if ($product_vertical === '' || $product_code === '') {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => "Missing product_vertical or product_code"
            ]);
            exit;
        }

        if (!preg_match('/^[A-Z0-9]{1,6}$/', $product_code)) {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => "Invalid code format (A–Z/0–9, 1–6 chars)"
            ]);
            exit;
        }

        $acc_year        = $_SESSION['acc_year']        ?? date('Y');
        $session_id_val  = session_id()                 ?: ($_SESSION['session_id'] ?? null);
        $sess_user_type  = $_SESSION['sess_user_type']  ?? null;
        $sess_user_id    = $_SESSION['sess_user_id']    ?? null;
        $sess_company_id = $_SESSION['sess_company_id'] ?? null;
        $sess_branch_id  = $_SESSION['sess_branch_id']  ?? null;

        $columns = [
            "product_vertical" => $product_vertical,
            "product_code"     => $product_code,
            "acc_year"         => $acc_year,
            "session_id"       => $session_id_val,
            "sess_user_type"   => $sess_user_type,
            "sess_user_id"     => $sess_user_id,
            "sess_company_id"  => $sess_company_id,
            "sess_branch_id"   => $sess_branch_id
        ];

        $ins = $pdo->insert('product_vertical', $columns);

        if (!$ins->status) {
            echo json_encode([
                "status" => false,
                "msg"    => "error",
                "error"  => $ins->error ?: "Insert failed"
            ]);
            exit;
        }

        $newId = $ins->data['id'] ?? ($ins->last_insert_id ?? null);

        echo json_encode([
            "status" => true,
            "msg"    => "create",
            "data"   => [
                "id"   => $newId,
                "text" => $product_vertical
            ]
        ]);
        exit;

    } catch (Exception $e) {
        echo json_encode([
            "status" => false,
            "msg"    => "error",
            "error"  => $e->getMessage()
        ]);
        exit;
    }
break;

    case "product_type":
    $vertical_id = $_POST['vertical_id'] ?? null;

    // Build the normal options HTML
    $options_html = product_type_name("", $vertical_id);
    $options_html = select_option($options_html, "Select"); // your helper builds the <option> list

    // Our special "Add new..." option (value MUST match your JS constant)
    $ADD_VALUE = '__add_new_product_type__';
    $add_new_option = '<option value="'.$ADD_VALUE.'">+ Add new product type…</option>';

    // Remove any existing "Add new" that might have come back already (idempotent)
    $options_html = preg_replace(
        '/<option[^>]*value="__add_new_product_type__"[^>]*>.*?<\/option>/i',
        '',
        $options_html
    );

    // Insert after "Select" (value="") if present; otherwise prepend to top
    if (preg_match('/<option[^>]*value=""[^>]*>.*?<\/option>/i', $options_html)) {
        // insert right after the first placeholder option
        $options_html = preg_replace(
            '/(<option[^>]*value=""[^>]*>.*?<\/option>)/i',
            '$1'.$add_new_option,
            $options_html,
            1
        );
    } else {
        // no placeholder -> just put it at the top
        $options_html = $add_new_option . $options_html;
    }

    echo $options_html;
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