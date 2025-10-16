<?php
function get_sunday_date($entry_date,$date)
{
    $sunday= "";
    for($i=1;$i<=$date;$i++){
        if(date('N',strtotime($entry_date))==7){
            $sunday = "<span class='font-weight-bold' style = 'color :#a216ea'>S</span>";
        }
    }

return $sunday;
}

function get_attendance_type($staff_id, $entry_date) {
    global $pdo;

    // 1. Check present table first
    $present_table = "view_bp_present";
    $present_cols  = ["employee_id"];
    $present_query = [$present_table, $present_cols];
    $where_present = "punch_date = '".$entry_date."' and employee_id = '".$staff_id."'";

    $present_res = $pdo->select($present_query, $where_present);

    if ($present_res->status && !empty($present_res->data[0])) {
        return "Present";
    }

    // 2. If not present, check absence view
    $absent_table = "erp_absence_view";
    $absent_cols  = ["absence_reason"];
    $absent_query = [$absent_table, $absent_cols];
    $where_absent = "punch_date = '".$entry_date."' and staff_id = '".$staff_id."'";

    $absent_res = $pdo->select($absent_query, $where_absent);

    if ($absent_res->status && !empty($absent_res->data[0])) {
        return "Absent"; // you can also return $absent_res->data[0]['absence_reason']
    }

    // 3. Default if no rows in either table
    return null;
}

function get_leave_status($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_att_history";
    $where         = [];
    $table_columns = [
        "leave_type",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $leave_status = $pdo->select($table_details, $where);

    if (!($leave_status->status)) {

        print_r($leave_status);

    } else {
        
        if(!empty($leave_status->data[0])) {
            $leave_sts    = $leave_status->data[0]['leave_type'];
        }else{
            $leave_sts    = "";
        }
        
    }
        return $leave_sts;
}
function get_holiday_date($entry_date){
    global $pdo;

    $table_name    = "attendance_holidays";
    $where         = [];
    $table_columns = [
        "COUNT(holiday_date) AS count",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];
    
   $where  = "holiday_date = '".$entry_date."'";
    

    $holiday = $pdo->select($table_details, $where);

    if (!($holiday->status)) {

        print_r($holiday);

    } else {
        
        if(!empty($holiday->data[0])) {
            $holiday_sts    = $holiday->data[0]['count'];
        }else{
            $holiday_sts    = "";
        }
        
    }
        return $holiday_sts;
}

function total_sundays($start_date,$end_date){
    $sundays=0;
    
    $start = new DateTime($start_date);
    $end = new DateTime($end_date);
    $days = $start->diff($end, true)->days;

    $sundays = intval($days / 7) + ($start->format('N') + $days % 7 >= 7);
    
    return $sundays;
}

function total_holidays($start_date,$end_date){
     global $pdo;

    //$current_month = $year."-".$month;
    $table_name    = "attendance_holidays";
    $where         = [];
    $table_columns = [
        "COUNT(holiday_date) AS count",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = "holiday_date between '".$start_date."' and '".$end_date."' and is_delete != 1";
    

    $holiday_count = $pdo->select($table_details, $where);
    if (!($holiday_count->status)) {

        print_r($holiday_count);

    } else {

        $holiday_count  = $holiday_count->data[0];

        $holiday    = floatval($holiday_count['count']);
        
    }
        return $holiday;
}

function get_present_count($staff_id,$start_date,$end_date){
    global $pdo;

    $table_name    = "view_att_history";
    $where         = [];
    $table_columns = [
        "count(present) as present_count",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date between '".$start_date."' and '".$end_date."' and staff_id = '".$staff_id."'";
    

    $day_status = $pdo->select($table_details, $where);

    if (!($day_status->status)) {

        print_r($day_status);

    } else {
        
        if(!empty($day_status->data[0])) {
            $day_sts    = $day_status->data[0]['present_count'];
        }else{
            $day_sts    = "";
        }
        
    }
        return $day_sts;
}

function get_late_count($staff_id,$start_date,$end_date){
    global $pdo;

    $table_name    = "view_att_history";
    $where         = [];
    $table_columns = [
        "count(present) as late_count",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date between '".$start_date."' and '".$end_date."' and staff_id = '".$staff_id."' and present = 2";
    

    $day_status = $pdo->select($table_details, $where);

    if (!($day_status->status)) {

        print_r($day_status);

    } else {
        
        if(!empty($day_status->data[0])) {
            $day_sts    = $day_status->data[0]['late_count'];
        }else{
            $day_sts    = "";
        }
        
    }
        return $day_sts;
}

function get_permission_count($staff_id,$start_date,$end_date){
    global $pdo;

    $table_name    = "view_att_history";
    $where         = [];
    $table_columns = [
        "count(present) as permission_count",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date between '".$start_date."' and '".$end_date."' and staff_id = '".$staff_id."' and present = 3";
    

    $day_status = $pdo->select($table_details, $where);

    if (!($day_status->status)) {

        print_r($day_status);

    } else {
        
        if(!empty($day_status->data[0])) {
            $day_sts    = $day_status->data[0]['permission_count'];
        }else{
            $day_sts    = "";
        }
        
    }
        return $day_sts;
}

function get_half_day_count($staff_id,$start_date,$end_date){
    global $pdo;

    $table_name    = "view_att_history";
    $where         = [];
    $table_columns = [
        "count(present) as half_day_count",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date between '".$start_date."' and '".$end_date."' and staff_id = '".$staff_id."' and present = 4";
    

    $day_status = $pdo->select($table_details, $where);

    if (!($day_status->status)) {

        print_r($day_status);

    } else {
        
        if(!empty($day_status->data[0])) {
            $day_sts    = $day_status->data[0]['half_day_count'];
        }else{
            $day_sts    = "";
        }
        
    }
        return $day_sts;
}

function get_full_day_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "leave_details_sub";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "COUNT(day_type) as full_day",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'is_delete = 0 AND hr_approved = 1  and day_type = 1 and from_date like "%'.$current_month.'%"  and cancel_status = 0 and staff_id ="'.$staff_id.'" and leave_type != 2 and leave_type != 8 and leave_type != 4 and leave_type != 10';

    $full_leave_count = $pdo->select($table_details, $where);
    if (!($full_leave_count->status)) {

        print_r($full_leave_count);

    } else {

        $full_leave_count  = $full_leave_count->data[0];

        $full_day_count    = $full_leave_count['full_day'];
        
    }
        return $full_day_count;
}
function get_full_day_cl_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = " view_cl_half_count";
    $where         = [];
    $table_columns = [
        "COUNT(leave_type) as full_cl_leave",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = 'date = "'.$current_month.'" and staff_id ="'.$staff_id.'" and cl_leave_type = 2';
    

    $full_cl_leave_count = $pdo->select($table_details, $where);
    if (!($full_cl_leave_count->status)) {

        print_r($full_cl_leave_count);

    } else {

        $full_cl_leave_count  = $full_cl_leave_count->data[0];

        $full_cl_leave    = $full_cl_leave_count['full_cl_leave'];
        
    }
        return $full_cl_leave;
}
function get_cl_day_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = " view_cl_half_count";
    $where         = [];
    $table_columns = [
        "COUNT(leave_type) as cl_leave",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'date = "'.$current_month.'" and staff_id ="'.$staff_id.'" and cl_leave_type = 8';
    

    $leave_count = $pdo->select($table_details, $where);

    if (!($leave_count->status)) {

        print_r($leave_count);

    } else {

        $leave_count  = $leave_count->data[0];

         $day_count    = floatval($leave_count['cl_leave'])/2;
        
    }
        return $day_count;
}
function get_absent_count($month,$year,$staff_id,$total_days) {
    global $pdo;

    $current_month = $year."-".$month;
    $current_days = $year."-".$month."-".$total_days;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(attendance_type) as absent_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date < '".$current_days."' and day_status != 5";
    

    $absent_count = $pdo->select($table_details, $where);

    if (!($absent_count->status)) {

        print_r($absent_count);

    } else {

        $absent_count  = $absent_count->data[0];

        $absent_cnt    = $absent_count['absent_count'];
        
    }
        return $absent_cnt;
}
function get_half_day_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "leave_details_sub";
    $where         = [];
    $table_columns = [
        "COUNT(leave_days) as half_day_leave",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

   $where  = 'is_delete = 0 AND hr_approved = 1  and day_type = 2 and from_date like "%'.$current_month.'%" and cancel_status = 0 and staff_id ="'.$staff_id.'" and leave_type != 2 and leave_type != 8 and leave_type != 4 and leave_type != 10';
    

    $half_leave_count = $pdo->select($table_details, $where);

    if (!($half_leave_count->status)) {

        print_r($half_leave_count);

    } else {

        $half_leave_count  = $half_leave_count->data[0];

         $half_day_count    = floatval($half_leave_count['half_day_leave'])/2;
        
    }
        return $half_day_count;
}
function get_check_in_time($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "check_in_time",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $check_in = $pdo->select($table_details, $where);

    if (!($check_in->status)) {

        print_r($check_in);

    } else {
        
        if(!empty($check_in->data[0])) {
            $check_in_time    = $check_in->data[0]['check_in_time'];
        }else{
            $check_in_time    = "";
        }
        
    }
        return $check_in_time;
}

function get_check_out_time($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "check_out_time",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
   
    $check_out = $pdo->select($table_details, $where);

    if (!($check_out->status)) {

        print_r($check_out);

    } else {
        
        if(!empty($check_out->data[0])) {
            $check_out_time    = $check_out->data[0]['check_out_time'];
        }else{
            $check_out_time    = "";
        }
        
    }
        return $check_out_time;
}