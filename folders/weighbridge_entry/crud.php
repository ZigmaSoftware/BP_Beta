<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "weighbridge_entry";

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

$no_of_trip          = "";
$entry_date          = "";
$slip_no          = "";
$vehicle_no          = "";
$material_name          = "";
$gross_weight          = "";
$tare_weight          = "";
$net_weight          = "";

$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $no_of_trip         = $_POST["no_of_trip"];
        $entry_date         = $_POST["entry_date"];
        $slip_no         = $_POST["slip_no"];
        $vehicle_no         = $_POST["vehicle_no"];
        $material_name         = $_POST["material_name"];
        $gross_weight         = $_POST["gross_weight"];
        $tare_weight         = $_POST["tare_weight"];
        $net_weight         = $_POST["net_weight"];
        
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "no_of_trip"           => $no_of_trip,
            "entry_date"           => $entry_date,
            "slip_no"           => $slip_no,
            "vehicle_no"           => $vehicle_no,
            "material_name"           => $material_name,
            "gross_weight"           => $gross_weight,
            "tare_weight"           => $tare_weight,
            "net_weight"           => $net_weight,
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
        $columns    = [
            "@a:=@a+1 s_no",
            "no_of_trip",
            "entry_date",
            "slip_no",
            "(SELECT vehicle_no FROM vehicle_master AS vm WHERE vm.unique_id = ".$table.".vehicle_no ) AS vehicle_no",
            "(SELECT material_name FROM source_of_waste_master AS swm WHERE swm.unique_id = ".$table.".material_name ) AS material_name",
            "gross_weight",
            "tare_weight",
            "net_weight",
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

        $where = "entry_date BETWEEN '$_POST[from_date]' AND '$_POST[to_date]'";

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

            $where .= "(".$search.")";
        }
        $where .= " AND is_delete = '0'";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['entry_date'] = disdate($value['entry_date']);

                $btn_update         = weighbridge_entry_btn_update($folder_name,$value['unique_id']);
                $btn_delete         = btn_delete($folder_name,$value['unique_id']);

                if ( $value['unique_id'] == "5f97fc3257f2525529") {
                    $btn_update         = "";
                    $btn_delete         = "";
                } 

                $value['unique_id'] = $btn_update.$btn_delete;
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