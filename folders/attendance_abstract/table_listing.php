<?php
// ---------------------------
// Error display + logger setup
// ---------------------------
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function log_step($message, $data = null) {
    $logFile = __DIR__ . "/attendance_debug.log"; // same folder
    $time = date("Y-m-d H:i:s");
    $entry = "[$time] $message";
    if ($data !== null) {
        $entry .= " => " . print_r($data, true);
    }
    $entry .= PHP_EOL;
    error_log($entry, 3, $logFile);
}

// ---------------------------
// Input + config
// ---------------------------
if (isset($_POST['year_month'])) {
    log_step("POST received", $_POST);

    include '../../config/dbconfig.php';
    include 'function.php';
    log_step("Includes loaded");

    $year_month     = $_POST['year_month']."-01";
    $exp_month_year = explode('-', $_POST['year_month']);
    $month          = $exp_month_year[1];
    $year           = $exp_month_year[0];
    log_step("Parsed year/month", ['year'=>$year,'month'=>$month]);

    if ($year > date('Y') || ($year == date('Y') && $month > date('m'))) {
        $current_day = 0;
        log_step("Future month detected -> no days");
    } else {
        $current_day = ($month == date('m') && $year == date('Y'))
            ? date('d')
            : cal_days_in_month(CAL_GREGORIAN, $month, $year);
        log_step("Current_day calculated", $current_day);
    }

    $start_date   = $year."-".$month."-01";
    $end_date     = $year."-".$month."-".$current_day;
    $today        = date('Y-m-d');
    $total_sunday = total_sundays($start_date, $end_date);
    log_step("Date range", ['start'=>$start_date,'end'=>$end_date]);
} else {
    include 'function.php';
    $month        = date('m');
    $year         = date('Y');
    $year_month   = $year."-".$month."-01";
    $current_day  = date('d');
    $start_date   = $year."-".$month."-01";
    $end_date     = $year."-".$month."-".$current_day;
    $today        = date('Y-m-d');
    $total_sunday = total_sundays($start_date, $end_date);
    log_step("Default (no POST) date range", ['start'=>$start_date,'end'=>$end_date]);
}

// ---------------------------
// DB query
// ---------------------------
$start = 0;
$s_no = 0;
$where_list = "is_delete = 0 and is_active = 1";

$columns_list = [
    "@a:=@a+1 s_no",
    "employee_id",
    "staff_name",
    "work_location",
    "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = staff_test.designation_unique_id ) AS designation_type",
    "department",
    "'' as date_month",
    "unique_id"
];

$table_details_list = [
    "staff_test",
    $columns_list
];

$order_by     = "employee_id ASC";
$sql_function = "SQL_CALC_FOUND_ROWS";

$result = $pdo->select($table_details_list, $where_list, "", $start, $order_by, $sql_function);
log_step("Employee list query executed", $result);

$total_records = total_records();
log_step("Total records", $total_records);

// ---------------------------
// Table build
// ---------------------------
$table_data = "";

if ($result->status) {
    $res_array = $result->data;

    foreach ($res_array as $key => $value) {
        $cnt = 0;
        log_step("Processing employee", $value);

        $department = department($value['department']);
        $value['department'] = $department[0]['department'] ?? '';
        log_step("Department resolved", $department);

        $project = get_project_name($value['work_location'])[0]['label'] ?? '';
        log_step("Project resolved", $project);

        $table_data .= "<tr>";
        $table_data .= "<td class=''>".($s_no = $s_no + 1)."</td>";
        $table_data .= "<td>".$value['employee_id']."</td>";
        $table_data .= "<td>".$value['staff_name']."</td>";
        $table_data .= "<td>".$project."</td>";
        $table_data .= "<td>".$value['designation_type']."</td>";
        $table_data .= "<td>".$value['department']."</td>";

        if ($current_day > 0) {
            for ($date = 1; $date <= $current_day; $date++) {
                $cnt += 1;
                $date_padded = str_pad($date, 2, "0", STR_PAD_LEFT);
                $entry_date  = $year."-".$month."-".$date_padded;

                $check_sunday = get_sunday_date($entry_date, $date);
                $leave        = get_leave_status($value['employee_id'], $entry_date);
                $check_holiday= get_holiday_date($entry_date);
                $day_status   = get_attendance_type($value['employee_id'], $entry_date);

                log_step("Day iteration", [
                    'employee'=>$value['employee_id'],
                    'date'=>$entry_date,
                    'status'=>$day_status,
                    'leave'=>$leave,
                    'holiday'=>$check_holiday,
                    'sunday'=>$check_sunday
                ]);

                // Mark attendance cell
                $cell = "";
                if ($day_status === "Present") {
                    $cell = "<span class='text-success fw-bold'>P</span>";
                } elseif ($day_status === "Absent") {
                    $cell = "<span class='text-danger fw-bold'>A</span>";
                }

                // Override for leave types
                switch($leave) {
                    case 1: $cell = "<span class='fw-bold' style='color:#099be4'>EL</span>"; break;
                    case 2: $cell = "<span class='fw-bold' style='color:#099be4'>CL</span>"; break;
                    case 3: $cell = "<span class='fw-bold' style='color:#099be4'>SL</span>"; break;
                    case 4: $cell = "<span class='fw-bold' style='color:#099be4'>COL</span>"; break;
                    case 5: $cell = "<span class='fw-bold' style='color:#099be4'>SPL</span>"; break;
                    case 6: $cell = "<span class='fw-bold' style='color:#099be4'>LOP</span>"; break;
                }

                // Sundays / Holidays if no attendance
                if ($check_sunday && $day_status == null) {
                    $cell = $check_sunday;
                }
                if ($check_holiday && $day_status == null) {
                    $cell = "<span class='fw-bold' style='color:blue'>H</span>";
                }

                // Defaults
                if ($cell === "") {
                    $cell = "<span class='text-danger fw-bold'>A</span>";
                }

                $table_data .= "<td>".$cell."</td>";
            }

            $total_holidays   = total_holidays($start_date, $end_date);
            $total_cnt        = $cnt;
            $total_leave_days = $total_holidays + $total_sunday;
            $tot_days         = $total_cnt - $total_leave_days;
            $leave_count      = $tot_days - get_present_count($value['employee_id'], $start_date, $end_date);
            $late_count       = get_late_count($value['employee_id'], $start_date, $end_date);
            $permission_count = get_permission_count($value['employee_id'], $start_date, $end_date);
            $half_day_count   = get_half_day_count($value['employee_id'], $start_date, $end_date);

            log_step("Employee totals", [
                'emp'=>$value['employee_id'],
                'tot_days'=>$tot_days,
                'leave_count'=>$leave_count,
                'late_count'=>$late_count,
                'permission_count'=>$permission_count,
                'half_day_count'=>$half_day_count
            ]);

            $table_data .= "<td>".$total_cnt."</td>";
        }

        $table_data .= "</tr>";
    }
}

log_step("Table generation completed");
?>
<div class="table-responsive">
    <table id="atttendance_summary_report_datatable" class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>Emp ID</th>
            <th>Executive Name</th>
            <th>Location</th>
            <th>Designation</th>
            <th>Department</th>
            <?php 
                if ($current_day > 0) {
                    for ($date = 1; $date <= $current_day; $date++) {
                        $date_padded = str_pad($date, 2, "0", STR_PAD_LEFT);
                        echo "<th>".$date_padded."</th>";
                    }
                }
            ?>
            <th>T.Days</th>
        </tr>
    </thead>
    <tbody>
        <?php echo $table_data; ?>
    </tbody>
</table>

</div>
