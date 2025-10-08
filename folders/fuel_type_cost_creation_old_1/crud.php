<?php

// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

// Database Country Table Name
$table             = "Fuel_type_cost_creation";

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
// $is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $entry_date              = $_POST["entry_date"];
        $travel_type              = $_POST["travel_type"];
        $fuel_type              = $_POST["fuel_type"];
        $vehicle_type          = $_POST["vehicle_type"];
        $rate                   = $_POST["rate"];
        $unique_id              = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "entry_date"           => $entry_date,
            "travel_type"           => $travel_type,
            "fuel_type"              => $fuel_type,
            "vehicle_type"           => $vehicle_type,
            "rate"                   => $rate,
            // "is_active"           => $is_active,
            "unique_id"           => unique_id($prefix)
        ];

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table, $columns, $update_where);

            // Update Ends
        } else {
            // Insert Begins

            $action_obj     = $pdo->insert($table, $columns);
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
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start         = $_POST['start'];
        $draw         = $_POST['draw'];
        $limit         = $length;

        $data        = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "(SELECT travel_type from travel_type AS travel_type WHERE travel_type.unique_id =" . $table . ".travel_type) AS travel_type",
            // "vehicle_type",
            "(SELECT vehicle_type from vehicle_type AS vehicle_type WHERE vehicle_type.unique_id =".$table.".vehicle_type) AS vehicle_type",
            "fuel_type",
            // "(SELECT fuel_type from fuel_type_cost_creation AS fuel_type_cost_creation WHERE fuel_type_cost_creation.unique_id =".$table.".unique_id) AS fuel_type",
            "rate",
            "unique_id"
        ];
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where = " is_delete = '0' ";

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);

        // Datatable Searching
        $search         = datatable_searching($search, $columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
               
                $demo = $value['rate'];
                $value['rate']  = $demo . '/KM(in Rupees)';

                $btn_update         = btn_update($folder_name, $value['unique_id']);
                $btn_delete         = btn_delete($folder_name, $value['unique_id']);

                if ($value['unique_id'] == "5f97fc3257f2525529") {
                    $btn_update         = "";
                    $btn_delete         = "";
                }

                $value['unique_id'] = $btn_update . $btn_delete;
                $data[]             = array_values($value);
            }

            $json_array = [
                "draw"                => intval($draw),
                "recordsTotal"         => intval($total_records),
                "recordsFiltered"     => intval($total_records),
                "data"                 => $data,
                "testing"            => $result->sql
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

        $action_obj     = $pdo->update($table, $columns, $update_where);

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

    case 'get_fuel_type':

        $vehicle_type          = $_POST['vehicle_type'];
        // print_R($vehicle_type);
        if($vehicle_type=='64ddd2413fbe296497'){
            $vehicle_type_options            = [

            'Petrol' => [
                "id"    => "Petrol",
                "text"  => "Petrol"
            ],
            'Disel' => [
                "id"    => "Disel",
                "text"  => "Disel"
            ],
        ];
    }else{
        $vehicle_type_options  = [
        'Petrol' => [
            "id"    => "Petrol",
            "text"  => "Petrol"
        ],
    ];
}   
// $fuel_type_options  = select_option($fuel_type_option, "Select Fuel Type", $fuel_type);

        // $vehicle_type_options= fuel_type("",$vehicle_type);
        $vehicle_type_options  = select_option($vehicle_type_options, "Select the Fuel Type");

        echo $vehicle_type_options;

        break;

    case 'get_vehicle_type':

        $travel_type           = $_POST['travel_type'];
        $travel_type_options= vehicle_type("",$travel_type);
        $travel_type_options  = select_option($travel_type_options, "Select the Vehicle Type");
        echo $travel_type_options;

        break;

    default:

        break;
}
