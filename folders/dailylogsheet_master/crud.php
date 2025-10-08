<?php 

// Get folder Name From Current Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Table Name
$table             = "dailylogsheet_master";

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

// Form Variables
$company_name       = "";
$project_name       = "";
$type               = "";
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

        $company_name   = $_POST["company_name"];
        $project_name   = $_POST["project_name"];
        $type           = $_POST["type"];
        $is_active      = $_POST["is_active"];
        $unique_id      = $_POST["unique_id"];

        $update_where   = "";

        $columns        = [
            "company_name"  => $company_name,
            "project_name"  => $project_name,
            "type"          => $type,
            "is_active"     => $is_active,
            "unique_id"     => unique_id($prefix)
        ];

        // Update Begins
        if($unique_id) {
            unset($columns['unique_id']);
            $update_where   = ["unique_id" => $unique_id];
            $action_obj     = $pdo->update($table,$columns,$update_where);
        } else {
        // Insert Begins
            $action_obj     = $pdo->insert($table,$columns);
        }

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

            $msg        = $unique_id ? "update" : "create";
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
        $columns        = [
            "@a:=@a+1 s_no",
            "company_name",
            "project_name",
            "type",
            "is_active",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = " is_delete = '0' ";

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);
        if ($search) {
            $where .= " AND ".$search;
        }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {
            $res_array  = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
                
                // Convert values for display
                $value['company_name']  = company_name($value['company_name'])[0]['company_name'];
                $value['project_name']  = project_name($value['project_name'])[0]['project_name'];
                $value['type']          = disname($value['type']);
                $value['is_active']     = is_active_show($value['is_active']);
                $value['unique_id']     = $btn_update . $btn_toggle;
                
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
        $is_active = $_POST['is_active'];

        $columns = ["is_active" => $is_active];
        $update_where = ["unique_id" => $unique_id];

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
            "msg"    => $msg,
            "sql"    => $action_obj->sql,
            "error"  => $action_obj->error
        ]);
        break;
        
        
    case 'project_name':
    $company_id = $_POST['company_id'];
    $project = $_POST['project'];
    $project_name_options  = get_project_name("", $company_id);
    $project_name_options  = select_option($project_name_options, "Select the Project Name", $project);
    echo $project_name_options;
    break;


    default:
        break;
}

?>
