<?php 

$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name)-2];

// Use Mandi Gobindgad table
$table = "mandi_gobindgad_log";

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
$test = "";

// ---- Main Switch Block ---- //
switch ($action) {
    case 'createupdate':
        
        $project_id            = $_POST["project_id"];
        $entry_date           = $_POST["entry_date"];
        $waste_received       = $_POST["waste_received"];
        $waste_reject         = $_POST["waste_reject"];
        $feed_to_digester     = $_POST["feed_to_digester"];
        $black_water_liters   = $_POST["black_water_liters"];
        $water_liters         = $_POST["water_liters"];
        $feeding_ph           = $_POST["feeding_ph"];
        $outlet_ph            = $_POST["outlet_ph"];
        $flowmeter_start      = $_POST["flowmeter_start"];
        $flowmeter_stop       = $_POST["flowmeter_stop"];
        $genset_start_hrs     = $_POST["genset_start_hrs"];
        $genset_stop_hrs      = $_POST["genset_stop_hrs"];
        $start_kwh            = $_POST["start_kwh"];
        $stop_kwh             = $_POST["stop_kwh"];
        $remarks              = $_POST["remarks"];
        $unique_id            = $_POST["unique_id"];

        $columns = [
            "project_id"            => $project_id,
            "entry_date"         => $entry_date,
            "waste_received"     => $waste_received,
            "waste_reject"       => $waste_reject,
            "feed_to_digester"   => $feed_to_digester,
            "black_water_liters" => $black_water_liters,
            "water_liters"       => $water_liters,
            "feeding_ph"         => $feeding_ph,
            "outlet_ph"          => $outlet_ph,
            "flowmeter_start"    => $flowmeter_start,
            "flowmeter_stop"     => $flowmeter_stop,
            "genset_start_hrs"   => $genset_start_hrs,
            "genset_stop_hrs"    => $genset_stop_hrs,
            "start_kwh"          => $start_kwh,
            "stop_kwh"           => $stop_kwh,
            "remarks"            => $remarks,
            "created_by"         => isset($_SESSION['staff_id']) ? $_SESSION['staff_id'] : 'unknown',
            "unique_id"          => unique_id($prefix)
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
            "entry_date",
            "project_id",
            "waste_received",
            "waste_reject",
            "feed_to_digester",
            "black_water_liters",
            "water_liters",
            "feeding_ph",
            "outlet_ph",
            "flowmeter_start",
            "flowmeter_stop",
            "genset_start_hrs",
            "genset_stop_hrs",
            "start_kwh",
            "stop_kwh",
            "remarks",
            "unique_id",
        ];

        $table_details = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where = " is_delete = '0' ";
        
         // ðŸ”¹ Apply custom filters
        $from_date  = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
        $to_date    = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
        $project_id = isset($_POST['project_id']) ? trim($_POST['project_id']) : '';
    
        if ($from_date && $to_date) {
            $where .= " AND entry_date BETWEEN '" . $from_date . "' AND '" . $to_date . "' ";
        } elseif ($from_date) {
            $where .= " AND entry_date >= '" . $from_date . "' ";
        } elseif ($to_date) {
            $where .= " AND entry_date <= '" . $to_date . "' ";
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
                $value['entry_date'] = disdate($value['entry_date']);
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
