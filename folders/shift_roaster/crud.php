<?php
// ======================================================
// ✅ SHIFT ROSTER CRUD
// ======================================================

// Get folder name from current URL
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// Table name
$table = "shift_roster_main";

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
$project_id = "";
$month_year = "";
$unique_id  = "";
$prefix     = "srf"; // For unique_id generation
$data       = "";
$msg        = "";
$error      = "";
$status     = "";
$sess_user_type = $_SESSION['sess_user_type'] ?? "";

// ======================================================
// ✅ SWITCH ACTIONS
// ======================================================
switch ($action) {

    // ======================================================
    // ✅ CREATE / UPDATE
    // ======================================================
    case 'createupdate':
        $project_id = $_POST["project_id"] ?? '';
        $month_year = $_POST["month_year"] ?? '';
        $unique_id  = $_POST["unique_id"] ?? '';

        $columns = [
            "project_id"  => $project_id,
            "month_year"  => $month_year,
            "unique_id"   => unique_id($prefix)
        ];

        if (!empty($unique_id)) {
            unset($columns['unique_id']);
            $update_where = 'unique_id="' . $unique_id . '"';
            $action_obj = $pdo->update($table, $columns, $update_where);
        } else {
            $action_obj = $pdo->insert($table, $columns);
        }

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
            "project_id",
            "month_year",
            "is_active",
            "unique_id"
        ];

        $table_details = [
            $table . " , (SELECT @a:=" . $start . ") AS a ",
            $columns
        ];

        $where = "is_delete = 0";

        // 🔍 Search filter
        if ($search) {
            $search_like = mysql_like($search);
            $where .= " AND (project_id LIKE '$search_like' OR month_year LIKE '$search_like')";
        }

        $order_column = $_POST["order"][0]["column"] ?? 0;
        $order_dir    = $_POST["order"][0]["dir"] ?? "asc";
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

        $sql_function = "SQL_CALC_FOUND_ROWS";
        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;
            foreach ($res_array as $value) {
                $project_name = project_name($value['project_id'])[0]['project_name'] ?? '-';
                $value['month_year'] = date("M-Y", strtotime($value['month_year'] . "-01"));
                
                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);

                $value['unique_id'] = $btn_update . $btn_toggle;
                unset($value['is_active']);

                $data[] = [
                    $value['s_no'],
                    $project_name,
                    $value['month_year'],
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
        
        
    case 'get_shift_list':
    $table = "shift_creation";
    $columns = ["shift_name"];
    $where = "is_active = 1 AND is_delete = 0";
    $result = $pdo->select([$table, $columns], $where);

    if ($result->status) {
        echo json_encode(["status" => 1, "data" => $result->data]);
    } else {
        echo json_encode(["status" => 0, "data" => []]);
    }
    exit;
    break;


    // ======================================================
    // ✅ ROSTER SUBTABLE GENERATION (called by AJAX)
    // ======================================================
    case 'get_roster_table':
        $project_id = $_POST['project_id'] ?? '';
        $month_year = $_POST['month_year'] ?? '';

        if (empty($project_id) || empty($month_year)) {
            echo "<div class='alert alert-warning'>Please select both Project and Month.</div>";
            exit;
        }

        // Fetch staff for this project
        $staff_list = staff_name("", "", $project_id);
        if (!$staff_list) {
            echo "<div class='alert alert-danger'>No active staff found for this project.</div>";
            exit;
        }

        // Prepare month days
        $start_date = date('Y-m-01', strtotime($month_year));
        $end_date   = date('Y-m-t', strtotime($month_year));
        $period = new DatePeriod(new DateTime($start_date), new DateInterval('P1D'), (new DateTime($end_date))->modify('+1 day'));

        echo "<div class='table-responsive mt-3'>
            <table class='table table-bordered text-center align-middle'>
            <thead class='table-light'>
                <tr><th style='min-width:220px;'>Employee Name</th>";

        foreach ($period as $date) {
            $label = $date->format('d-m-Y');
            $day   = strtolower($date->format('D'));
            echo "<th><div>$label</div><small class='text-muted'>$day</small></th>";
        }

        echo "<th>Action</th></tr></thead><tbody>";

foreach ($staff_list as $staff) {

    // ✅ Convert department ID → department name
    $dept_data = department($staff['department']);
    $dept_name = is_array($dept_data) ? ($dept_data[0]['department'] ?? '-') : $dept_data;

    echo "<tr>
            <td class='text-start'>
                {$staff['staff_name']}<br>
                <small class='text-muted'>" . ($dept_name ?: '-') . "</small>
            </td>";

    foreach ($period as $date) {
        $d = $date->format('Y-m-d');
        echo "<td>
                <input type='text' class='form-control form-control-sm shift_input' placeholder='Shift' 
                       data-emp='{$staff['unique_id']}' data-date='$d'>
                <div class='form-check text-center mt-1'>
                    <input type='checkbox' class='form-check-input weekoff_check' 
                           data-emp='{$staff['unique_id']}' data-date='$d'>
                </div>
              </td>";
    }

    echo "<td>
            <button type='button' class='btn btn-success btn-sm add_row_btn'>ADD</button>
          </td></tr>";
}

        echo "</tbody></table></div>";
        exit;
        break;
        
        

    default:
        echo json_encode(["status" => 0, "error" => "Invalid action"]);
        break;
}
?>
