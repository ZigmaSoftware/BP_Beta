<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table_group        = 'group_product_master';
$table              = 'product_master'; 
$sub_list_table     = 'product_sublist'; 
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

        $sub_group_unique_id    = $_POST["sub_group_unique_id"];
        $group_unique_id        = $_POST["group_unique_id"];
        $product_name           = $_POST["product_name"];
		$description            = $_POST["description"];
        $is_active              = $_POST["is_active"];
        $unique_id           = $_POST["unique_id"];
        $screen_unique_id              = $_POST["screen_unique_id"];
        $created_user_id  = $_SESSION['sess_user_id'];
        $update_where       = "";

        $columns            = [
            "group_unique_id"   => $group_unique_id,
            "sub_group_unique_id"   => $sub_group_unique_id,
            "product_name"          => $product_name,
            "description"           => $description,
            "is_active"             => $is_active,
            "screen_unique_id"      => $screen_unique_id,
            "created_user_id"       => $created_user_id,
            "unique_id"             => $main_unique_id = unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'unique_id = "'.$unique_id.'" AND  group_unique_id =  "'.$group_unique_id.'"AND  sub_group_unique_id =  "'.$sub_group_unique_id.'" AND product_name = "'.$product_name.'" AND is_delete = 0  ';

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
        } else if ($data[0]["count"] == 0) {
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
             $update_where_sub   = [
                "screen_unique_id"     => $screen_unique_id,
                "is_delete"         => 0
            ];
            $columns_sub = [
                'prod_unique_id' => $unique_id
            ];
            
            $action_obj     = $pdo->update($table,$columns,$update_where);
            $action_obj_sub     = $pdo->update($sub_list_table,$columns_sub,$update_where_sub);

        // Update Ends
        } else {
            // Insert Begins
            $update_where_sub   = [
                "screen_unique_id"     => $screen_unique_id,
                "is_delete"         => 0
            ];
           
            
            $columns_sub = [
                'prod_unique_id' =>$main_unique_id
            ];
            $action_obj     = $pdo->insert($table,$columns);
            $action_obj     = $pdo->update($sub_list_table,$columns_sub,$update_where_sub);
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
                $msg        = "create";
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
    

        
    case 'product_sub_add_update':

        $prod_unique_id                 = $_POST["prod_unique_id"];
        $unique_id                      = $_POST["unique_id"];
        $group_unique_id_sub            = $_POST["group_unique_id_sub"];
        $sub_group_unique_id_sub   = $_POST["sub_group_unique_id_sub"];
        $category_unique_id_sub         = $_POST["category_unique_id_sub"];
        $item_unique_id_sub             = $_POST["item_unique_id_sub"];
        $qty                            = $_POST["qty"];
        //$id                 = category_item($item_unique_id_sub);
        $uom_unique_id      = $_POST["uom"];
        $is_active                      = $_POST["is_active_sub"];
        $remarks                        = $_POST["remarks"];
        $screen_unique_id              = $_POST["screen_unique_id"];
        $update_where                   = "";

        $columns            = [
            "prod_unique_id"                => $prod_unique_id,
            "group_unique_id"               => $group_unique_id_sub,
            "sub_group_unique_id"           => $sub_group_unique_id_sub,
            "category_unique_id"            => $category_unique_id_sub,
            "item_unique_id"                => $item_unique_id_sub,
            "qty"                           => $qty,
            "uom_unique_id"                 => $uom_unique_id,
            "is_active"                     => $is_active,
            "remarks"                       => $remarks,
            "screen_unique_id"              => $screen_unique_id,
            "unique_id"                     => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $sub_list_table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'screen_unique_id ="'.$screen_unique_id.'" AND  group_unique_id ="'.$group_unique_id_sub.'" AND  sub_group_unique_id ="'.$sub_group_unique_id_sub.'" AND  category_unique_id ="'.$category_unique_id_sub.'" AND  item_unique_id ="'.$item_unique_id_sub.'" AND is_delete = 0';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);
//print_r($action_obj);
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
    
    case 'po_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "prod_sub";
        
        // Fetch Data
        $screen_unique_id = $_POST['screen_unique_id']; 

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
            "screen_unique_id"
        ];
        $table_details  = [
            $sub_list_table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "screen_unique_id"    => $screen_unique_id,
            // "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        
        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();
        //print_r($result);

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
    
    case 'prod_sub_delete':

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
    
    case "prod_sub_edit":
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
            "uom_unique_id",
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

    case 'sub_group':

        $group_id          = $_POST['group_id'];

        $sub_group_name_options  = sub_group_name("",$group_id);

        $sub_group_name_options  = select_option($sub_group_name_options,"Select");

        echo $sub_group_name_options;
        
        break;
        
    case 'sub_group_sublist':

        $group_id          = $_POST['group_id'];

        $sub_group_name_options  = sub_group_name("",$group_id);

        $sub_group_name_options  = select_option($sub_group_name_options,"Select");

        echo $sub_group_name_options;
        
        break;
    case 'sublist_group':

        $group_id          = $_POST['group_id'];

        $group_name_sublist_options  = group_name("","",$group_id);

        $group_name_sublist_options  = select_option($group_name_sublist_options,"Select");

        echo $group_name_sublist_options;
        
        break;
        
    case 'category':

        $group_id          = $_POST['group_id'];
        $sub_group_id         = $_POST['sub_group_id'];

        $category_sublist_options  = category_name("",$group_id,$sub_group_id);

        $category_sublist_options  = select_option($category_sublist_options,"Select");

        echo $category_sublist_options;
        
        break;
        
    case 'item_name':

        $group_id           = $_POST['group_id'];
        $sub_group_id       = $_POST['sub_group_id'];
        $category       = $_POST['category'];

        $item_sublist_options  = category_item("",$group_id,$sub_group_id,$category);

        $item_sublist_options  = select_option($item_sublist_options,"Select");

        echo $item_sublist_options;
        
        break;
        
    case 'unit_name':

        $item_id           = $_POST['item_id'];
    
        $unit_name  = unit("",$item_id);

        $unit_id  = $unit_name[0]['unit_name']."@@".$unit_name[0]['unique_id'];

        echo $unit_id;
        
        break;
    
    default:
        
        break;
}


?>