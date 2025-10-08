<?php 

function total_sundays($month,$year)
{
    $sundays=0;
    $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);
    for($i=1;$i<=$total_days;$i++){
        if(date('N',strtotime($year.'-'.$month.'-'.$i))==7){
            $sundays++;
        }
    }

return $sundays;
}


//Holiday count
function get_holidays($month_year) {
    global $pdo;

    $table_name    = "attendance_holidays";
    $where         = [];
    $table_columns = [
        "COUNT(id) as holiday",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

   $where  = "is_active = 1 AND is_delete = 0  AND holiday_date like '%".$month_year."%' AND attendance_set_unique_id != '' ";
    

    $holiday = $pdo->select($table_details, $where);

    if (!($holiday->status)) {

        print_r($holiday);

    } else {

        $holiday  = $holiday->data[0];

        $holiday_count    = $holiday['holiday'];
        
    }
        return $holiday_count;
}

//Work From Home count
function get_work_from_home($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $where         = [];
    $table_columns = [
        "SUM(leave_days) as work_from_home",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

    $where  = "is_active = 1 AND is_delete = 0 AND day_type = 3 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $work_from_home_count = $pdo->select($table_details, $where);

    if (!($work_from_home_count->status)) {

        print_r($work_from_home_count);

    } else {

        $work_from_home_count  = $work_from_home_count->data[0];

        $work_from_home_count    = $work_from_home_count['work_from_home'];
        
    }
        return $work_from_home_count;
}

//Special Leave Full Day count
function get_special_leave_full_day($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "SUM(leave_days) as special_leave_full_day",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];


    $where  = "is_active = 1 AND is_delete = 0 AND day_type = 1 AND leave_type = 5 AND entry_date like '%".$month_year."%' AND from_date <= '".$date."' AND staff_id = '".$staff_id."'";
    

    $special_leave_full_day_count = $pdo->select($table_details, $where);

    if (!($special_leave_full_day_count->status)) {

        print_r($special_leave_full_day_count);

    } else {

        $special_leave_full_day_count  = $special_leave_full_day_count->data[0];

        $special_leave_full_day_count    = $special_leave_full_day_count['special_leave_full_day'];
        
    }
        return $special_leave_full_day_count;
}

//Special Leave Half Day count
function get_special_leave_half_day($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "SUM(leave_days) as special_leave_half_day",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];


    $where  = "is_active = 1 AND is_delete = 0 AND day_type = 2 AND leave_type = 5 AND entry_date like '%".$month_year."%' AND half_date <= '".$date."' AND staff_id = '".$staff_id."'";
    

    $special_leave_half_day_count = $pdo->select($table_details, $where);

    if (!($special_leave_half_day_count->status)) {

        print_r($special_leave_half_day_count);

    } else {

        $special_leave_half_day_count    = $special_leave_half_day_count->data[0];

        $special_leave_half_day_count    = floatval($special_leave_half_day_count['special_leave_half_day']/2);
        
    }
        return $special_leave_half_day_count;
}

//Casual Leave Full Day count
function get_casual_leave_full_day($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "SUM(leave_days) as casual_leave_full_day",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = "is_active = 1 AND is_delete = 0 AND day_type = 1  AND leave_type = 2 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $casual_leave_full_day_count = $pdo->select($table_details, $where);

    if (!($casual_leave_full_day_count->status)) {

        print_r($casual_leave_full_day_count);

    } else {

        $casual_leave_full_day_count  = $casual_leave_full_day_count->data[0];

        $casual_leave_full_day_count    = $casual_leave_full_day_count['casual_leave_full_day'];
        
    }
        return $casual_leave_full_day_count;
}

//Casual Leave Half Day count
function get_casual_leave_half_day($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "SUM(leave_days) as casual_leave_half_day",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = "is_active = 1 AND is_delete = 0 AND day_type = 1  AND leave_type = 2 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $casual_leave_half_day_count = $pdo->select($table_details, $where);

    if (!($casual_leave_half_day_count->status)) {

        print_r($casual_leave_half_day_count);

    } else {

        $casual_leave_half_day_count  = $casual_leave_half_day_count->data[0];

        $casual_leave_half_day_count  = floatval($casual_leave_half_day_count['casual_leave_half_day']/2);
        
    }
        return $casual_leave_half_day_count;
}

//Permission Leave count
function get_permission_leave_count($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $where         = [];
    $table_columns = [
        "COUNT(id) as leave_permission",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = "is_active = 1 AND is_delete = 0 AND day_type = 6 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $leave_permission_count = $pdo->select($table_details, $where);

    if (!($leave_permission_count->status)) {

        print_r($leave_permission_count);

    } else {

        $leave_permission_count  = $leave_permission_count->data[0];

        $leave_permission_count    = $leave_permission_count['leave_permission'];
        
    }
        return $leave_permission_count;
}

//LOP count
function get_lop_count($month_year,$staff_id) {
    global $pdo;

    $table_name    = "leave_details";
    $where         = [];
    $table_columns = [
        "COUNT(id) as lop",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = "is_active = 1 AND is_delete = 0 AND leave_type = 6 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $lop_count = $pdo->select($table_details, $where);

    if (!($lop_count->status)) {

        print_r($lop_count);

    } else {

        $lop_count  = $lop_count->data[0];

        $lop_count    = $lop_count['lop'];
        
    }
        return $lop_count;
}


//Late count
function get_late_count($month_year,$staff_id) {
    global $pdo;

    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as late_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = "is_active = 1 AND is_delete = 0 AND day_status = 2 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $late_count = $pdo->select($table_details, $where);

    if (!($late_count->status)) {

        print_r($late_count);

    } else {

        $late_count  = $late_count->data[0];

        $late_cnt    = $late_count['late_count'];
        
    }
        return $late_cnt;
}

//Permission count
function get_permission_count($month_year,$staff_id) {
    global $pdo;

    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as permission_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 3 AND attendance_type = 1 AND entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $permission_count = $pdo->select($table_details, $where);

    if (!($permission_count->status)) {

        print_r($permission_count);

    } else {

        $permission_count  = $permission_count->data[0];

        $permission_cnt    = $permission_count['permission_count'];
        
    }
        return $permission_cnt;
}

//Absent count
function get_absent_count($month_year,$staff_id) {
    global $pdo;

    $table_name    = "daily_attendance";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "COUNT(attendance_type) as absent_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    
    $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date like '%".$month_year."%' AND entry_date <= '".$date."' and staff_id = '".$staff_id."'";
    

    $absent_count = $pdo->select($table_details, $where);

    if (!($absent_count->status)) {

        print_r($absent_count);

    } else {

        $absent_count  = $absent_count->data[0];

        $absent_cnt    = $absent_count['absent_count'];
        
    }
        return $absent_cnt;
}

//Present count
function get_present_count($month_year,$staff_id) {
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "COUNT(entry_date) as present_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    
    $where  = "entry_date like '%".$month_year."%' and staff_id = '".$staff_id."'";
    

    $present_count = $pdo->select($table_details, $where);

    if (!($present_count->status)) {

        print_r($present_count);

    } else {

        $present_count  = $present_count->data[0];

        $present_cnt    = $present_count['present_count'];
        
    }
        return $present_cnt;
}
?>