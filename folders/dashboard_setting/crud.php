<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "dashboard_settings";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$dashboard_setting          = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "cty";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "user_type_id"      => $_POST['user_type'],
            "staff_id"          => $_POST['staff_name'],
            "menus"             => isset($_POST['menus']) ? implode(",",$_POST['menus']) : '',
            "is_active"         => $_POST['is_active'],
            "unique_id"         => unique_id($prefix)
        ];

        if($unique_id) {
            
            // Update Begins
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
            "user_type_id",
            "staff_id",
            // "(SELECT staff_name FROM staff where staff.unique_id = dashboard_settings.staff_id) AS staff_id",
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

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
      //  $search         = datatable_searching($search,$columns);
        

       if ($_POST['search']['value']) {
            $where .= " AND staff_id IN (".staff_name_like($_POST['search']['value']).") ";
            // $where .= " AND staff_id IN (".staff_name_like($_POST['search']['value']).") ";
         
        }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
       // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['user_type_id'] = disname(user_type($value['user_type_id'])[0]['user_type']);
                if ($value['staff_id']) {

                    $value['staff_id']  = disname(staff_name($value['staff_id'])[0]['staff_name']);
                } else {
                    $value['staff_id'] = "All ".$value['user_type_id'];
                }

                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == "1")
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
                
                $value['unique_id'] = $btn_update . $btn_toggle;
                
                // stylize is_active
                $value['is_active'] = ($value['is_active'] == "1")
                    ? "<span style='color:green'>Active</span>"
                    : "<span style='color:red'>Inactive</span>";
                
              
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

    case "get_staffs":
        $user_type     = $_POST['user_type'];

        // $staff_options = "<option value=''>Select Staff Name</option>";
        $staff_options = [];

        if ($user_type) {
            // Get User Type Based Staff Details
            $staff_where = [
                "user_type_unique_id" => $user_type,
                "is_delete"           => 0,
                "is_active"           => 1
            ];

            $staff_columns      = [
                "staff_unique_id AS id",
                "(SELECT a.staff_name FROM staff a WHERE a.unique_id = user.staff_unique_id) AS text",
            ];

            $staff_table_details= [
                "user",
                $staff_columns
            ];

            $staff_select       = $pdo->select($staff_table_details,$staff_where);

            if ($staff_select->status) {
                $staff_options = $staff_select->data;
            } else {
                print_r($staff_select);
            }
        }

        $staff_options  = select_option($staff_options,"Select Staff Name");

        echo $staff_options;
        break;
    
    default:
        
        break;
}

?>