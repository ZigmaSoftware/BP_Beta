<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table_group        = 'group_product_master';
$table              = 'sales_order'; 
$sub_list_table     = 'sales_order_sublist'; 
$item_table         = 'item_master'; 

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
    case 'createupdate':

        $customer_name          = $_POST["customer_name"];
        $currency               = $_POST["currency"];
        $exchange_rate          = $_POST["exchange_rate"];
        $contact_person_name    = $_POST["contact_person_name"];
        $customer_po_no         = $_POST["customer_po_no"];
        $customer_po_date       = $_POST["customer_po_date"];
        $status                 = $_POST["status"];
        $company_name           = $_POST["company_name"];
        $entry_date             = $_POST["entry_date"];
        $unique_id              = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "customer_id"           => $customer_name,
            "currency_id"           => $currency,
            "exchange_rate"         => $exchange_rate,
            "contact_person_name"   => $contact_person_name,
            "customer_po_no"        => $customer_po_no,
            "customer_po_date"      => $customer_po_date,
            "company_id"            => $company_name,
            "entry_date"            => $entry_date,
            "status"                => $status,
            "created_user_id"       => $user_id,
            "created"               => $date,
            "unique_id"             => $unique_id
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where      = 'sales_order_no = "'.$purchase_order_no.'" AND is_delete = 0  ';

        // When Update Check without current id
        if ($update_condition == 1) {
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
        } else if ($data[0]["count"] == 0) {
        // Update Begins
        if($update_condition == 1) {
            unset($columns['unique_id']);
            unset($columns['created_user_id']);
            unset($columns['created']);
            
            $columns['updated_user_id'] = $user_id;
            $columns['updated'] = $date;

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table,$columns,$update_where);

        // Update Ends
        } else {
            // Insert Begins
            $bill_no_where   = [
                "acc_year"      => $_SESSION['acc_year']
            ];

            // GET Bill No
            $purchase_order_no             = bill_no($table,$bill_no_where,$po_prefix);
            $columns['sales_order_no']  = $purchase_order_no;

            $action_obj     = $pdo->insert($table,$columns);
            // Insert Ends
            }
        }

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
            "sub_group_unique_id",
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
        
        if (!empty($_POST['group_unique_id']) && !empty($_POST['sub_group_unique_id']) && !empty($_POST['category_unique_id'])) {
            $where .= " AND group_unique_id = '{$_POST['group_unique_id']}' AND sub_group_unique_id = '{$_POST['sub_group_unique_id']}' AND category_unique_id = '{$_POST['category_unique_id']}'";
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
            if (!empty($_POST['category_unique_id'])) {
                $conditions[] = "category_unique_id = '{$_POST['category_unique_id']}'";
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
        $total_records  = total_records();
        
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
                $sub_group_data                 = sub_group_name($value['sub_group_unique_id']);
                $value['sub_group_unique_id']   = !empty($sub_group_data[0]['sub_group_name']) ? $sub_group_data[0]['sub_group_name'] : '-';
                
                if($value['description'] == ' ' || empty($value['description']) || $value['description'] == ''){
                    $description = '-';
                } else {
                    $description = $value['description'];
                }
                
                $value['description']           = $description;
                $value['is_active']             = is_active_show($value['is_active']);
                $btn_update                     = btn_update($folder_name,$value['unique_id']);
                $btn_delete                     = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']             = $btn_update.$btn_delete;
                $data[]             = array_values($value);
            }
            
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
    
    case 'get_group_code':
        $unique_id = $_POST['code'];
        
        $json_array     = "";
        
        $columns        = [
            "uom_unique_id"
        ];
        $table_details  = [
            $item_table,
            $columns
        ];
        
        $where  = " is_delete = '0' AND unique_id = '$unique_id'";
        
        $result = $pdo->select($table_details,$where);
        if ($result->status) {
            if (!empty($result->data)) {
                $id = $result->data[0]['uom_unique_id'];
                $uom_unique_id      = unit_name($id);
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
        } else {
            $json_array = [
                'status' => 'error',
                'message' => 'Query execution failed.'
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
            $msg = "Select the Sub Group Name";
        } else if($type == 2){
            $sub_group_name_options  = category_name("",$group_id);
            $msg = "Select the Category Name";
        } else if($type == 3){
            $sub_group_name_options  = category_item("",$group_id);
            $msg = "Select the Item Name";
        } else {
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select the Sub Group Name";
        }
        $sub_group_name_options  = select_option($sub_group_name_options,$msg);
        echo $sub_group_name_options;
        
        break;
        
    case 'so_add_update':

        $so_unique_id                 = $_POST["prod_unique_id"];
        $unique_id                      = $_POST["unique_id"];
        $product_unique_id              = $_POST["product_unique_id"];
        $uom                            = $_POST["uom"];
        $qty                            = $_POST["qty"];
        $rate                           = $_POST["rate"];
        $discount                       = $_POST["discount"];
        $tax                            = $_POST["tax"];
        $amount                         = $_POST["amount"];
        $update_where                   = "";

        $columns            = [
            "item_name_id"                  => $product_unique_id,
            "unit_name"                     => $uom,
            "quantity"                      => $qty,
            "rate"                          => $rate,
            "discount"                      => $discount,
            "tax_id"                        => $tax,
            "amount"                        => $amount,
            "is_active"                     => $is_active,
            "sales_order_main_unique_id"    => $so_unique_id,
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
        $select_where       = 'sales_order_main_unique_id ="'.$prod_unique_id.'" AND is_delete = 0';

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
            "prod_unique_id",
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
            "is_active"                     => 1,
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
                $value['group_unique_id']       = !empty($group_data[0]['group_name'])     ? $group_data[0]['group_name']     : '-';
                $value['sub_group_unique_id']   = !empty($sub_group_data[0]['sub_group_name']) ? $sub_group_data[0]['sub_group_name'] : '-';
                $value['category_unique_id']    = !empty($category_data[0]['category_name'])  ? $category_data[0]['category_name']  : '-';
                $value['item_unique_id']        = !empty($item_data[0]['item_name'])      ? $item_data[0]['item_name']      : '-';
                $value['prod_unique_id']        = '-';
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
            "is_active"    => 1,
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

    
    default:
        
        break;
}


?>