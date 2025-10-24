<?php
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


switch ($action) {

    case 'createupdate':
    echo json_encode(["status" => 1, "msg" => "Shift roster form saved successfully (dummy)"]);
    exit;
    break;
    
    

// ✅ DATATABLE LOAD
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

 
// ✅ ROSTER SUBTABLE GENERATION (Prefill on Edit)
case 'get_roster_table':
    $project_id = $_POST['project_id'] ?? '';
    $month_year = $_POST['month_year'] ?? '';

    if (empty($project_id) || empty($month_year)) {
        echo "<div class='alert alert-warning'>Please select both Project and Month.</div>";
        exit;
    }

    // Find main_unique_id
    $main_unique_id = '';
    $main_check = $pdo->select(['shift_roster_main', ['unique_id']], [
        "project_id" => $project_id,
        "month_year" => $month_year,
        "is_delete" => 0
    ]);
    if ($main_check->status && !empty($main_check->data)) {
        $main_unique_id = $main_check->data[0]['unique_id'];
    }

    // Fetch staff for this project
    $staff_list = staff_name("", "", $project_id);
    if (!$staff_list) {
        echo "<div class='alert alert-danger'>No active staff found for this project.</div>";
        exit;
    }

    // Build existing shift data (if main exists)
    $existing = [];
    if ($main_unique_id) {
        $res = $pdo->select(['shift_roster_details', ['employee_id', 'shift_date', 'shift_name', 'is_weekoff']], [
            "main_unique_id" => $main_unique_id
        ]);
        if ($res->status && !empty($res->data)) {
            foreach ($res->data as $row) {
                $existing[$row['employee_id']][$row['shift_date']] = [
                    "shift_name" => $row['shift_name'],
                    "is_weekoff" => $row['is_weekoff']
                ];
            }
        }
    }

    // Prepare date range
    $start_date = date('Y-m-01', strtotime($month_year));
    $end_date   = date('Y-m-t', strtotime($month_year));
    $period = new DatePeriod(
        new DateTime($start_date),
        new DateInterval('P1D'),
        (new DateTime($end_date))->modify('+1 day')
    );

    // Build table
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
        $emp_id = $staff['unique_id'];
        $dept_data = department($staff['department']);
        $dept_name = is_array($dept_data) ? ($dept_data[0]['department'] ?? '-') : $dept_data;

        echo "<tr>
                <td class='text-start'>
                    {$staff['staff_name']}<br>
                    <small class='text-muted'>" . ($dept_name ?: '-') . "</small>
                </td>";

        // 🧩 Detect if this employee already has any existing shift data
        $has_existing = !empty($existing[$emp_id]);

        foreach ($period as $date) {
            $d = $date->format('Y-m-d');
            $shift_val = $existing[$emp_id][$d]['shift_name'] ?? '';
            $is_weekoff = $existing[$emp_id][$d]['is_weekoff'] ?? 0;
            $checked = $is_weekoff ? "checked" : "";
            $existing_flag = $shift_val ? "data-existing='1'" : "data-existing='0'";

            echo "<td>
                    <input type='text' class='form-control form-control-sm shift_input' 
                           placeholder='Shift'
                           value='{$shift_val}'
                           data-emp='{$emp_id}' 
                           data-date='{$d}'
                           {$existing_flag}>
                    <div class='form-check text-center mt-1'>
                        <input type='checkbox' class='form-check-input weekoff_check' 
                               data-emp='{$emp_id}' data-date='{$d}' {$checked}>
                    </div>
                  </td>";
        }

        // 🧩 Button changes dynamically based on existing data
        $btn_label = $has_existing ? 'Update' : 'Add';
        $btn_class = $has_existing ? 'btn-warning' : 'btn-success';

        echo "<td>
                <button type='button' class='btn {$btn_class} btn-sm add_row_btn'>{$btn_label}</button>
              </td></tr>";
    }

    echo "</tbody></table></div>";
    exit;
    break;

                                                                        
// ✅ ADD SHIFT DETAILS — store in main + details tables
     case 'add_shift_details':
            $project_id = $_POST['project_id'] ?? '';
            $month_year = $_POST['month_year'] ?? '';
            $employee_id = $_POST['employee_id'] ?? '';
            $shifts = $_POST['shifts'] ?? [];
        
            if (empty($project_id) || empty($month_year) || empty($employee_id) || empty($shifts)) {
                echo json_encode(["status" => 0, "msg" => "Missing required data"]);
                exit;
            }
        
            $main_table = "shift_roster_main";
            $details_table = "shift_roster_details";
            $main_unique_id = '';
        
            // 1️⃣ Check if main record exists for same project + month
            $check_main = $pdo->select([$main_table, ['unique_id']], [
                "project_id" => $project_id,
                "month_year" => $month_year,
                "is_delete" => 0
            ]);
        
            if ($check_main->status && !empty($check_main->data)) {
                $main_unique_id = $check_main->data[0]['unique_id'];
            } else {
                // create new main record
                $main_unique_id = unique_id("srf");
                $pdo->insert($main_table, [
                    "unique_id"  => $main_unique_id,
                    "project_id" => $project_id,
                    "month_year" => $month_year,
                    "is_active"  => 1
                ]);
            }
        
            // 2️⃣ Loop through each shift entry (date => shift info)
            foreach ($shifts as $date => $shiftData) {
                $shift_name = $shiftData['shift_name'] ?? '';
                $is_weekoff = $shiftData['is_weekoff'] ?? 0;
        
                if (empty($shift_name)) continue;
        
                // lookup shift_unique_id from shift_creation
                $shift_lookup = $pdo->select(['shift_creation', ['unique_id']], [
                    "shift_name" => $shift_name,
                    "is_active" => 1,
                    "is_delete" => 0
                ]);
        
                $shift_unique_id = ($shift_lookup->status && !empty($shift_lookup->data))
                    ? $shift_lookup->data[0]['unique_id']
                    : null;
        
                // check if already exists for that employee/date
                $existing = $pdo->select([$details_table, ['id']], [
                    "main_unique_id" => $main_unique_id,
                    "employee_id" => $employee_id,
                    "shift_date" => $date
                ]);
        
                $columns = [
                    "main_unique_id" => $main_unique_id,
                    "employee_id"    => $employee_id,
                    "shift_date"     => $date,
                    "shift_unique_id" => $shift_unique_id,
                    "shift_name"     => $shift_name,
                    "is_weekoff"     => $is_weekoff
                ];
        
                if ($existing->status && count($existing->data) > 0) {
                    $pdo->update($details_table, $columns, [
                        "main_unique_id" => $main_unique_id,
                        "employee_id" => $employee_id,
                        "shift_date" => $date
                    ]);
                } else {
                    $columns["shift_unique_id"] = $shift_unique_id ?? unique_id("srd");
                    $pdo->insert($details_table, $columns);
                }
            }
        
            echo json_encode(["status" => 1, "msg" => "Shift roster details saved successfully"]);
            exit;
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
        
// ✅ TOGGLE ACTIVE / INACTIVE
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
