<?php
// ======================================================
// ✅ SHIFT CREATION CRUD
// ======================================================

// Get folder Name From Current URL
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// Table name
$table = "shift_creation";

// Include DB config
include '../../config/dbconfig.php';

// Variables Declaration
$action     = $_POST['action'] ?? '';
$action_obj = (object)[
    "status" => 0,
    "data"   => "",
    "error"  => "Action Not Performed"
];

$json_array = "";
$sql        = "";
$shift_name = "";
$start_time = "";
$end_time   = "";
$description = "";
$unique_id  = "";
$prefix     = "shf"; // For unique_id generation
$data       = "";
$msg        = "";
$error      = "";
$status     = "";
$sess_user_type = $_SESSION['sess_user_type'] ?? "";

switch ($action) {

    // ======================================================
    // ✅ CREATE / UPDATE
    // ======================================================
    case 'createupdate':
        $shift_name  = $_POST["shift_name"];
        $start_time  = $_POST["start_time"];
        $end_time    = $_POST["end_time"];
        $shift_duration = $_POST["shift_duration"];
        $description = $_POST["description"];
        $unique_id   = $_POST["unique_id"];

        $columns = [
            "shift_name"  => $shift_name,
            "start_time"  => $start_time,
            "end_time"    => $end_time,
            "shift_duration"  => $shift_duration,
            "description" => $description,
            "unique_id"   => unique_id($prefix)
        ];

        // Update if existing record
        if (!empty($unique_id)) {
            unset($columns['unique_id']);
            $update_where = 'unique_id="' . $unique_id . '"';
            $action_obj = $pdo->update($table, $columns, $update_where);
        } else {
            // Insert new record
            $action_obj = $pdo->insert($table, $columns);
        }

        // Prepare response
        if ($action_obj->status) {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $sql    = $action_obj->sql;
            $msg    = $unique_id ? "update" : "create";
        } else {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = "error";
        }

        echo json_encode([
            "status" => $status,
            "data"   => $data,
            "error"  => $error,
            "msg"    => $msg,
            "sql"    => $sql
        ]);
        break;


    // ======================================================
    // ✅ DATATABLE LOAD
    // ======================================================
    case 'datatable':
        $search  = $_POST['search']['value'] ?? '';
        $length  = $_POST['length'] ?? 10;
        $start   = $_POST['start'] ?? 0;
        $draw    = $_POST['draw'] ?? 1;
        $limit   = ($length == '-1') ? "" : $length;

        $data = [];

        $columns = [
            "@a:=@a+1 s_no",
            "shift_name",
            "start_time",
            "end_time",
            "shift_duration",
            "description",
            "is_active",
            "unique_id"
        ];

        $table_details = [
            $table . " , (SELECT @a:=" . $start . ") AS a ",
            $columns
        ];

        $where = "is_delete = 0";

        // 🔍 Apply search filters
        if ($search) {
            $search_like = mysql_like($search);
            $where .= " AND (shift_name LIKE '$search_like' 
                        OR start_time LIKE '$search_like' 
                        OR end_time LIKE '$search_like' 
                        OR description LIKE '$search_like')";
        }

        // 🔽 Sorting
        $order_column = $_POST["order"][0]["column"] ?? 0;
        $order_dir    = $_POST["order"][0]["dir"] ?? "asc";
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

        // 🔹 Query
        $sql_function = "SQL_CALC_FOUND_ROWS";
        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $value) {
                $value['shift_name']  = disname($value['shift_name']);
                $value['start_time']  = date("h:i A", strtotime($value['start_time']));
                $value['end_time']    = date("h:i A", strtotime($value['end_time']));
                $value['shift_duration'] = disname($value['shift_duration']);
                if (!empty($value['shift_duration'])) {
                    $value['shift_duration'] .= ' Hours';   // ✅ append text for UI only
                }
                $value['description'] = disname($value['description']);

                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);

                $value['unique_id'] = $btn_update . $btn_toggle;
                unset($value['is_active']);
                $data[] = [
                    $value['s_no'],
                    $value['shift_name'],
                    $value['start_time'],
                    $value['end_time'],
                    $value['shift_duration'],
                    $value['description'],
                    $value['unique_id']
                ];
            }
            $json_array = [
                "draw"            => intval($draw),
                "recordsTotal"    => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data"            => $data,
                "testing"         => $result->sql
            ];
        } else {
            $json_array = [
                "draw"            => intval($draw),
                "recordsTotal"    => 0,
                "recordsFiltered" => 0,
                "data"            => [],
                "error"           => $result->error
            ];
        }

        echo json_encode($json_array);
        break;


    // ======================================================
    // ✅ TOGGLE ACTIVE / INACTIVE
    // ======================================================
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

    default:
        echo json_encode(["status" => 0, "error" => "Invalid action"]);
        break;
}
?>
