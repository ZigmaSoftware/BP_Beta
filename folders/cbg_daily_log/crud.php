<?php 
session_start();

$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name)-2];

// Table name
$table = "cbg_daily_log";

include '../../config/dbconfig.php';

$action = $_POST['action'];
$action_obj = (object)[
    "status" => 0,
    "data"   => "",
    "error"  => "Action Not Performed"
];

$json_array = "";
$sql = "";

$unique_id = "";
$prefix = "";
$data = "";
$msg = "";
$error = "";
$status = "";

// ---- Main Switch Block ---- //
switch ($action) {
    case 'createupdate':
        $project_id   = $_POST["project_id"];
        $date         = $_POST["date"];

        // Main log fields
        $waste_receive             = $_POST["waste_receive"];
        $waste_reject              = $_POST["waste_reject"];
        $waste_crushing            = $_POST["waste_crushing"];
        $feeding_kgs               = $_POST["feeding_kgs"];
        $water_liters              = $_POST["water_liters"];
        $feeding_ph                = $_POST["feeding_ph"];
        $valve_1_ph                = $_POST["valve_1_ph"];
        $nb                        = $_POST["nb"];
        $wd                        = $_POST["wd"];
        $start_reading             = $_POST["start_reading"];
        $end_reading               = $_POST["end_reading"];
        $total_reading             = $_POST["total_reading"];
        $daily_gas_generation      = $_POST["daily_gas_generation"];
        $start_purification_balloon= $_POST["start_purification_balloon"];
        $stop_purification_balloon = $_POST["stop_purification_balloon"];
        $gas_used_for_cbg          = $_POST["gas_used_for_cbg"];
        $cbg_start_time            = $_POST["cbg_start_time"];
        $cbg_stop_time             = $_POST["cbg_stop_time"];
        $cbg_running_hrs           = $_POST["cbg_running_hrs"];
        $comp_start_time           = $_POST["comp_start_time"];
        $comp_stop_time            = $_POST["comp_stop_time"];
        $comp_total_run_hrs        = $_POST["comp_total_run_hrs"];
        $total_cbg_generation      = $_POST["total_cbg_generation"];
        $start_cascade_pressure    = $_POST["start_cascade_pressure"];
        $stop_cascade_pressure     = $_POST["stop_cascade_pressure"];
        $balance_cascade_pressure  = $_POST["balance_cascade_pressure"];
        $no_of_vehicle_filled      = $_POST["no_of_vehicle_filled"];
        $balance_gas_cascade       = $_POST["balance_gas_cascade"];
        $remark                    = $_POST["remark"];
        $unique_id                 = $_POST["unique_id"];

        $columns = [
            "project_id"               => $project_id,
            "date"                     => $date,
            "waste_receive"            => $waste_receive,
            "waste_reject"             => $waste_reject,
            "waste_crushing"           => $waste_crushing,
            "feeding_kgs"              => $feeding_kgs,
            "water_liters"             => $water_liters,
            "feeding_ph"               => $feeding_ph,
            "valve_1_ph"               => $valve_1_ph,
            "nb"                       => $nb,
            "wd"                       => $wd,
            "start_reading"            => $start_reading,
            "end_reading"              => $end_reading,
            "total_reading"            => $total_reading,
            "daily_gas_generation"     => $daily_gas_generation,
            "start_purification_balloon"=> $start_purification_balloon,
            "stop_purification_balloon"=> $stop_purification_balloon,
            "gas_used_for_cbg"         => $gas_used_for_cbg,
            "cbg_start_time"           => $cbg_start_time,
            "cbg_stop_time"            => $cbg_stop_time,
            "cbg_running_hrs"          => $cbg_running_hrs,
            "comp_start_time"          => $comp_start_time,
            "comp_stop_time"           => $comp_stop_time,
            "comp_total_run_hrs"       => $comp_total_run_hrs,
            "total_cbg_generation"     => $total_cbg_generation,
            "start_cascade_pressure"   => $start_cascade_pressure,
            "stop_cascade_pressure"    => $stop_cascade_pressure,
            "balance_cascade_pressure" => $balance_cascade_pressure,
            "no_of_vehicle_filled"     => $no_of_vehicle_filled,
            "balance_gas_cascade"      => $balance_gas_cascade,
            "remark"                   => $remark,
            "created_by"               => isset($_SESSION['staff_id']) ? $_SESSION['staff_id'] : 'unknown',
            "unique_id"                => unique_id($prefix)
        ];

        if ($unique_id) {
            unset($columns['unique_id']);
            $update_where = ["unique_id" => $unique_id];
            $action_obj = $pdo->update($table, $columns, $update_where);
        } else {
            $action_obj = $pdo->insert($table, $columns);
        }

        if ($action_obj->status) {
            $status = 1;
            $msg = $unique_id ? "update" : "create";
        } else {
            $status = 0;
            $msg = "error";
            $error = $action_obj->error;
        }

        echo json_encode([
            "status" => $status,
            "data"   => $action_obj->data,
            "error"  => $error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
        break;

    case 'datatable':
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start  = $_POST['start'];
        $draw   = $_POST['draw'];
        $limit  = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        $columns = [
            "@a:=@a+1 s_no",
            "date","project_id","waste_receive","waste_reject","waste_crushing","feeding_kgs",
            "daily_gas_generation",
            "total_cbg_generation",
            "no_of_vehicle_filled",
            "remark",
            "unique_id"
        ];

        $table_details = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where = " is_delete = '0' ";

        $from_date  = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
        $to_date    = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
        $project_id = isset($_POST['project_id']) ? trim($_POST['project_id']) : '';

        if ($from_date && $to_date) {
            $where .= " AND date BETWEEN '" . $from_date . "' AND '" . $to_date . "' ";
        } elseif ($from_date) {
            $where .= " AND date >= '" . $from_date . "' ";
        } elseif ($to_date) {
            $where .= " AND date <= '" . $to_date . "' ";
        }

        if ($project_id) {
            $where .= " AND project_id = '" . $project_id . "' ";
        }

        $order_column = $_POST["order"][0]["column"];
        $order_dir    = $_POST["order"][0]["dir"];
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);
        $search       = datatable_searching($search, $columns);

        if ($search) {
            if ($where) $where .= " AND ";
            $where .= $search;
        }

        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                $value['date'] = disdate($value['date']);
                $value['project_id'] = get_project_name($value['project_id'])[0]['label'];

                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_delete = btn_delete($folder_name, $value['unique_id']);

                $value['unique_id'] = $btn_update . $btn_delete;
                $data[] = array_values($value);
            }

            echo json_encode([
                "draw"            => intval($draw),
                "recordsTotal"    => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data"            => $data,
                "testing"         => $result->sql
            ]);
        } else {
            print_r($result);
        }
        break;

    case 'delete':
        $unique_id = $_POST['unique_id'];
        $columns = ["is_delete" => 1];
        $update_where = ["unique_id" => $unique_id];

        $action_obj = $pdo->update($table, $columns, $update_where);

        if ($action_obj->status) {
            $msg = "success_delete";
        } else {
            $msg = "error";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
        break;

    case 'user_type_options':
        $user_type = $_POST['user_type'];
        $user_type_options = under_user_type($user_type);
        $user_type_options = select_option($user_type_options, "Select");
        echo $user_type_options;
        break;
        
    default:
        break;
}
?>
