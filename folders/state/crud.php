<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "states";

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

$country_name       = "";
$state_name         = "";
$prefix             = "sta";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $country_name       = $_POST["country_name"];
        $state_name         = $_POST["state_name"];
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "country_unique_id"     => $country_name,
            "state_name"            => $state_name,
            "unique_id"             => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'country_unique_id = "'.$country_name.'" AND state_name="'.$state_name.'" AND is_delete = 0  ';

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
            "(SELECT name FROM countries AS con WHERE con.unique_id = ".$table.".country_unique_id ) AS country_name",
            "state_name",
            "is_active", 
            "unique_id"
        ];

        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        // $where          = [
        //     "is_active"     => 1,
        //     "is_delete"     => 0
        // ];

        $where = "is_delete = '0'"; // ✅ Don't filter by is_active


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
                    $value['country_name'] = disname($value['country_name']);
                
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    if ($value['is_active'] == 1) {
                        $btn_toggle = btn_toggle_on($folder_name, $value['unique_id']);
                    } else {
                        $btn_toggle = btn_toggle_off($folder_name, $value['unique_id']);
                    }
                
                    // Replace 'unique_id' value with buttons
                    $value['unique_id'] = $btn_update . $btn_toggle;
                
                    // ✅ Remove second-last element (likely 'is_active')
                    $keys = array_keys($value);
                    $second_last_key = $keys[count($keys) - 2];
                    unset($value[$second_last_key]);
                
                    // Reindex and add to $data
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

    default:
        
        break;
}


?>