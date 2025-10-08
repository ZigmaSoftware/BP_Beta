<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "user_type";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$user_type          = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $user_type          = $_POST["user_type"];
        $under_user_type    = $_POST["under_user_type"];
        $is_active          = $_POST["is_active"];
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "user_type"           => $user_type,
            "under_user_type"     => $under_user_type,
            "is_active"           => $is_active,
            "unique_id"           => unique_id($prefix)
        ];

        // Update Begins
        if($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table,$columns,$update_where);

        // Update Ends
        } else {
        // Insert Begins
           
            $action_obj     = $pdo->insert($table,$columns);
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
            "user_type",
            "is_active",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        // $where          = [
        //     "is_delete"     => 0
        // ];
        // $order_by       = "";

        $where = " is_delete = '0' ";

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
                    $value['user_type'] = disname($value['user_type']);
                
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_toggle = ($value['is_active'] == 1)
                        ? btn_toggle_on($folder_name, $value['unique_id'])
                        : btn_toggle_off($folder_name, $value['unique_id']);
                
                    // If it's the admin super user, disable toggle and update
                    if ($value['unique_id'] == "5f97fc3257f2525529") {
                        $btn_update = "";
                        $btn_toggle = "";
                    }
                
                    $value['is_active'] = ($value['is_active'] == 1)
                        ? "<span style='color:green'>Active</span>"
                        : "<span style='color:red'>In Active</span>";
                
                    $value['unique_id'] = $btn_update . $btn_toggle;
                
                    $data[] = array_values($value);
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
    
    
    case 'toggle':
            $unique_id = $_POST['unique_id'];
            $is_active = $_POST['is_active']; // 1 or 0
        
            // Prevent toggle for admin super user
            if ($unique_id == "5f97fc3257f2525529") {
                echo json_encode([
                    "status" => false,
                    "msg" => "Action not allowed for Super Admin",
                    "sql" => "",
                    "error" => ""
                ]);
                break;
            }
        
            $columns = [
                "is_active" => $is_active
            ];
        
            $update_where = [
                "unique_id" => $unique_id
            ];
        
            $action_obj = $pdo->update($table, $columns, $update_where);
        
            if ($action_obj->status) {
                $status = true;
                $msg = $is_active ? "Activated Successfully" : "Deactivated Successfully";
            } else {
                $status = false;
                $msg = "Toggle failed!";
            }
        
            echo json_encode([
                "status" => $status,
                "msg" => $msg,
                "sql" => $action_obj->sql,
                "error" => $action_obj->error
            ]);
            break;
        

         case 'user_type_options':

        $user_type          = $_POST['user_type'];

        $user_type_options  = under_user_type($user_type);

        $user_type_options  = select_option($user_type_options,"Select");

        echo $user_type_options;
        
        break;

    
    default:
        
        break;
}

?>