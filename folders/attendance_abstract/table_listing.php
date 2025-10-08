<?php
    if(isset($_POST['year_month'])){
        include '../../config/dbconfig.php';
        include 'function.php';
        error_reporting(0);
        $year_month     = $_POST['year_month']."-01";
        $exp_month_year = explode('-',$_POST['year_month']);
        $month          = $exp_month_year[1];
        $year           = $exp_month_year[0];
        
        // Check if selected month is in the future
        if($year > date('Y') || ($year == date('Y') && $month > date('m'))) {
            $current_day = 0; // No days to display for future months
        } else {
            $current_day = ($month == date('m') && $year == date('Y')) ? date('d') : cal_days_in_month(CAL_GREGORIAN, $month, $year);
        }

        $start_date     = $year."-".$month."-01";
        $end_date       = $year."-".$month."-".$current_day;
        $today          = date('Y-m-d');
        $total_sunday   = total_sundays($start_date, $end_date);
    } else {
        include 'function.php';
        $month           = date('m');
        $year            = date('Y');
        $year_month      = $year."-".$month."-01";
        $current_day     = date('d');
        $start_date      = $year."-".$month."-01";
        $end_date        = $year."-".$month."-".$current_day;
        $today           = date('Y-m-d');
        $total_sunday    = total_sundays($start_date, $end_date);
    }

    $start = 0;
    $s_no = 0;
    $where_list = "is_delete = 0 and is_active = 1";

if (isset($_SESSION['work_location']) && !empty($_SESSION['work_location'])) {
    $work_location = $_SESSION['work_location'];
    $where_list .= " AND work_location = '$work_location'";
}


    $columns_list    = [
        "@a:=@a+1 s_no",
        "employee_id",
        "staff_name",
        "work_location",
        "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = staff.designation_unique_id ) AS designation_type",
        "department",
        "'' as date_month",  
        "unique_id"                                            
    ];

    $table_details_list  = [
        "staff",
        $columns_list
    ];

    $order_by       = "employee_id ASC";

    $sql_function   = "SQL_CALC_FOUND_ROWS";

    $result         = $pdo->select($table_details_list, $where_list, "", $start, $order_by, $sql_function);
    
    // print_r($result);
    
    $total_records  = total_records();
    
    if ($result->status) {
        
        $res_array       = $result->data;

        $table_data      = "";
        
        foreach ($res_array as $key => $value) {
            $cnt = 0;

            $work_location = work_location($value['work_location']);
            $value['work_location'] = $work_location[0]['work_location'];
            $department = department($value['department']);
            $value['department'] = $department[0]['department'];
            
            $table_data .= "<tr>";
            $table_data .= "<td class=''>".($s_no = $s_no + 1)."</td>";
            $table_data .= "<td>".$value['employee_id']."</td>";
            $table_data .= "<td>".$value['staff_name']."</td>";
            $table_data .= "<td>".$value['work_location']."</td>";
            $table_data .= "<td>".$value['designation_type']."</td>";
            $table_data .= "<td>".$value['department']."</td>";

            if ($current_day > 0) {
                for ($date = 1; $date <= $current_day; $date++) {
                    if ($date < 10) {
                        $date = "0".$date;
                    }
                    $entry_date = $year."-".$month."-".$date;
                    $check_sunday = get_sunday_date($entry_date, $date);
                    $day_status = get_attendance_type($value['unique_id'], $entry_date);
                    // echo $day_status;
                    $leave = get_leave_status($value['unique_id'], $entry_date);
                    $check_holiday = get_holiday_date($entry_date);
                    
                    $cnt += 1;
                    switch($day_status) {
                        case 'Present':
                            $value['date_month'] = "<span class='text-green font-weight-bold'>P</span>";
                            // echo $value['date_month'];
                            break;
                        case 2:
                            $value['date_month'] = "<span class='text-warning font-weight-bold'>LP</span>";
                            break;
                        case 3:
                            $value['date_month'] = "<span class='text-warning font-weight-bold'>PP</span>";
                            break;
                        case 'Absent':
                            $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                            break;
                        default:
                            $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                            break;
                    }
                    switch($leave) {
                        case 1:
                            $value['date_month'] = "<span class='font-weight-bold' style='color :#099be4'>EL</span>";
                            break;
                        case 2:
                            $value['date_month'] = "<span class='font-weight-bold' style='color :#099be4'>CL</span>";
                            break;
                        case 3:
                            $value['date_month'] = "<span class='font-weight-bold' style='color :#099be4'>SL</span>";
                            break;
                        case 4:
                            $value['date_month'] = "<span class='font-weight-bold' style='color :#099be4'>COL</span>";
                            break;
                        case 5:
                            $value['date_month'] = "<span class='font-weight-bold' style='color :#099be4'>SPL</span>";
                            break;
                        case 6:
                            $value['date_month'] = "<span class='font-weight-bold' style='color :#099be4'>LOP</span>";
                            break;
                    }
                    if (($check_sunday) && ($day_status == null)) {
                        $value['date_month'] = $check_sunday;
                    } else {
                    switch($day_status) {
                        case 'Present':
                            $value['date_month'] = "<span class='text-green font-weight-bold'>P</span>";
                            // echo $value['date_month'];
                            break;
                        case 2:
                            $value['date_month'] = "<span class='text-warning font-weight-bold'>LP</span>";
                            break;
                        case 3:
                            $value['date_month'] = "<span class='text-warning font-weight-bold'>PP</span>";
                            break;
                        case 'Absent':
                            $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                            break;
                        default:
                            $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                            break;
                    }
                    }
                    if (($check_holiday) && ($day_status == null)) {
                        $value['date_month'] = "<span class='font-weight-bold' style='color :blue'>H</span>";
                    } else {
                    switch($day_status) {
                        case 'Present':
                            $value['date_month'] = "<span class='text-green font-weight-bold'>P</span>";
                            // echo $value['date_month'];
                            break;
                        case 2:
                            $value['date_month'] = "<span class='text-warning font-weight-bold'>LP</span>";
                            break;
                        case 3:
                            $value['date_month'] = "<span class='text-warning font-weight-bold'>PP</span>";
                            break;
                        case 'Absent':
                            $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                            break;
                        default:
                            $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                            break;
                    }
                    }
                    $table_data .= "<td>".$value['date_month']."</td>";
                }
                
                $total_holidays = total_holidays($start_date, $end_date);
                $total_cnt = $cnt;
                $total_leave_days = $total_holidays + $total_sunday;
                $tot_days = $total_cnt - $total_leave_days;
                $leave_count = $tot_days - get_present_count($value['unique_id'], $start_date, $end_date);
                $late_count = get_late_count($value['unique_id'], $start_date, $end_date);
                $permission_count = get_permission_count($value['unique_id'], $start_date, $end_date);
                $half_day_count = get_half_day_count($value['unique_id'], $start_date, $end_date);
                
                $table_data .= "<td>".$tot_days."</td>";
                // $table_data .= "<td>".$leave_count."</td>";
                // $table_data .= "<td>".$late_count."</td>";
                // $table_data .= "<td>".$permission_count."</td>";
                // $table_data .= "<td>".$half_day_count."</td>";
            }
        }
    }
?>
<table id="atttendance_summary_report_datatable" class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>Emp ID</th>
            <th>Executive Name</th>
            <th>Location</th>
            <th>Designation </th>
            <th>Department</th>
            <?php 
                if ($current_day > 0) {
                    for ($date = 1; $date <= $current_day; $date++) {
                        if ($date < 10) {
                            $date = "0".$date;
                        } ?>
                        <th><?=$date;?></th>
                <?php } } ?>
            <th>T.Days</th>
            <!--<th>LV</th>-->
            <!--<th>LP</th>-->
            <!--<th>PP</th>-->
            <!--<th>1/2 L</th>-->
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php echo $table_data; ?>
        </tr>
    </tbody>
</table>
