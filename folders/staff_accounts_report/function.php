<?php 

function btn_map_att ($lat_long = '',$entry_time = '') {

    if ($lat_long) {
        $final_str = '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query='.$lat_long.'">'.$entry_time.'</a>';

        return $final_str;
    }

    return '';

}

function total_sundays($month,$year,$date)
{
    $sundays=0;
    $total_days=cal_days_in_month(CAL_GREGORIAN, $month, $year);

    for($i=1;$i<=$date;$i++){
        if(date('N',strtotime($year.'-'.$month.'-'.$i))==7){
            $sundays++;
        }
    }

return $sundays;
}

function get_sunday_date($entry_date,$date)
{
    $sunday= "";
    for($i=1;$i<=$date;$i++){
        if(date('N',strtotime($entry_date))==7){
            $sunday = "<span class='font-weight-bold' style = 'color :#a216ea'>Sunday</span>";
        }
    }

return $sunday;
}


//full day leave count
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

//full day cl leave count
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

//full day comp off leave count
function get_full_day_comp_off_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = " view_comp_off_half_count";
    $where         = [];
    $table_columns = [
        "COUNT(leave_type) as full_comp_off_leave",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = 'date = "'.$current_month.'" and staff_id ="'.$staff_id.'" and comp_off_leave_type = 4';
    

    $full_comp_off_leave_count = $pdo->select($table_details, $where);
    if (!($full_comp_off_leave_count->status)) {

        print_r($full_comp_off_leave_count);

    } else {

        $full_comp_off_leave_count  = $full_comp_off_leave_count->data[0];

        $full_comp_off_leave    = $full_comp_off_leave_count['full_comp_off_leave'];
        
    }
        return $full_comp_off_leave;
}

// cl day leave count
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

// comp_off day leave count
function get_comp_off_day_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = " view_comp_off_half_count";
    $where         = [];
    $table_columns = [
        "COUNT(leave_type) as comp_off_leave",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'date = "'.$current_month.'" and staff_id ="'.$staff_id.'" and comp_off_leave_type = 10';
    

    $comp_off_count = $pdo->select($table_details, $where);

    if (!($comp_off_count->status)) {

        print_r($comp_off_count);

    } else {

        $comp_off_count  = $comp_off_count->data[0];

        $comp_offday_count    = floatval($comp_off_count['comp_off_leave'])/2;
        
    }
        return $comp_offday_count;
}


//half day leave count
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

//emergency_leave count
function get_emergency_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as emergency_leave_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 5 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."'";
    

    $emergency_leave_count = $pdo->select($table_details, $where);

    if (!($emergency_leave_count->status)) {

        print_r($emergency_leave_count);

    } else {

        $emergency_leave_count  = $emergency_leave_count->data[0];

        $emergency_leave_cnt    = $emergency_leave_count['emergency_leave_count'];
        
    }
        return $emergency_leave_cnt;
}


//Late count
function get_late_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as late_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 2 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."'";
    

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
function get_permission_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as permission_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 3 AND attendance_type = 1  AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."'";
    

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

function get_break_in_time($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "break_in_time",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $break_in = $pdo->select($table_details, $where);

    if (!($break_in->status)) {

        print_r($break_in);

    } else {
        
        if(!empty($break_in->data[0])) {
            $break_in_time    = $break_in->data[0]['break_in_time'];
        }else{
            $break_in_time    = "";
        }
        
    }
        return $break_in_time;
}

function get_break_out_time($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "break_out_time",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $break_out = $pdo->select($table_details, $where);

    if (!($break_out->status)) {

        print_r($break_out);

    } else {
        
        if(!empty($break_out->data[0])) {
            $break_out_time    = $break_out->data[0]['break_out_time'];
        }else{
            $break_out_time    = "";
        }
        
    }
        return $break_out_time;
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

function get_latitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "latitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $latitude = $pdo->select($table_details, $where);

    if (!($latitude->status)) {

        print_r($latitude);

    } else {
        
        if(!empty($latitude->data[0])) {
            $latitude_in    = $latitude->data[0]['latitude'];
        }else{
            $latitude_in    = "";
        }
        
    }
        return $latitude_in;
}

function get_longitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "longitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $longitude = $pdo->select($table_details, $where);

    if (!($longitude->status)) {

        print_r($longitude);

    } else {
        
        if(!empty($longitude->data[0])) {
            $longitude_in    = $longitude->data[0]['longitude'];
        }else{
            $longitude_in    = "";
        }
        
    }
        return $longitude_in;
}

function get_check_out_latitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "check_out_latitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $check_out_latitude = $pdo->select($table_details, $where);

    if (!($check_out_latitude->status)) {

        print_r($check_out_latitude);

    } else {
        
        if(!empty($check_out_latitude->data[0])) {
            $ck_out_latitude    = $check_out_latitude->data[0]['check_out_latitude'];
        }else{
            $ck_out_latitude    = "";
        }
        
    }
        return $ck_out_latitude;
}

function get_check_out_longitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "check_out_longitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $check_out_longitude = $pdo->select($table_details, $where);

    if (!($check_out_longitude->status)) {

        print_r($check_out_longitude);

    } else {
        
        if(!empty($check_out_longitude->data[0])) {
            $ck_out_longitude    = $check_out_longitude->data[0]['check_out_longitude'];
        }else{
            $ck_out_longitude    = "";
        }
        
    }
        return $ck_out_longitude;
}

function get_break_in_latitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "break_in_latitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $break_in_latitude = $pdo->select($table_details, $where);

    if (!($break_in_latitude->status)) {

        print_r($break_in_latitude);

    } else {
        
        if(!empty($break_in_latitude->data[0])) {
            $bk_in_latitude    = $break_in_latitude->data[0]['break_in_latitude'];
        }else{
            $bk_in_latitude    = "";
        }
        
    }
        return $bk_in_latitude;
}

function get_break_in_longitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "break_in_longitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $break_in_longitude = $pdo->select($table_details, $where);

    if (!($break_in_longitude->status)) {

        print_r($break_in_longitude);

    } else {
        
        if(!empty($break_in_longitude->data[0])) {
            $bk_in_longitude    = $break_in_longitude->data[0]['break_in_longitude'];
        }else{
            $bk_in_longitude    = "";
        }
        
    }
        return $bk_in_longitude;
}

function get_break_out_latitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "break_out_latitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $break_out_latitude = $pdo->select($table_details, $where);

    if (!($break_out_latitude->status)) {

        print_r($break_out_latitude);

    } else {
        
        if(!empty($break_out_latitude->data[0])) {
            $bk_out_latitude    = $break_out_latitude->data[0]['break_out_latitude'];
        }else{
            $bk_out_latitude    = "";
        }
        
    }
        return $bk_out_latitude;
}

function get_break_out_longitude($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "break_out_longitude",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $break_out_longitude = $pdo->select($table_details, $where);

    if (!($break_out_longitude->status)) {

        print_r($break_out_longitude);

    } else {
        
        if(!empty($break_out_longitude->data[0])) {
            $bk_out_longitude    = $break_out_longitude->data[0]['break_out_longitude'];
        }else{
            $bk_out_longitude    = "";
        }
        
    }
        return $bk_out_longitude;
}


function get_day_status($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_attendance_report";
    $where         = [];
    $table_columns = [
        "day_status",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."'";
    

    $day_status = $pdo->select($table_details, $where);

    if (!($day_status->status)) {

        print_r($day_status);

    } else {
        
        if(!empty($day_status->data[0])) {
            $day_sts    = $day_status->data[0]['day_status'];
        }else{
            $day_sts    = "";
        }
        
    }
        return $day_sts;
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

function total_holidays($month,$year){
     global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "attendance_holidays";
    $where         = [];
    $table_columns = [
        "COUNT(holiday_date) AS count",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = "holiday_date like '%".$current_month."%' and is_delete != 1";
    

    $holiday_count = $pdo->select($table_details, $where);
    if (!($holiday_count->status)) {

        print_r($holiday_count);

    } else {

        $holiday_count  = $holiday_count->data[0];

        $holiday    = floatval($holiday_count['count']);
        
    }
        return $holiday;
}

function get_leave_status($staff_id,$entry_date){
    global $pdo;

    $table_name    = "leave_details_sub";
    $where         = [];
    $table_columns = [
        "COUNT(from_date) as count",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "from_date = '".$entry_date."' and staff_id = '".$staff_id."' and hr_approved = 1 and cancel_status = 0";
    

    $leave_status = $pdo->select($table_details, $where);

    if (!($leave_status->status)) {

        print_r($leave_status);

    } else {
        
        if(!empty($leave_status->data[0])) {
            $leave_sts    = $leave_status->data[0]['count'];
        }else{
            $leave_sts    = "";
        }
        
    }
        return $leave_sts;
}


function get_emer_leave_status($staff_id,$entry_date){
    global $pdo;

    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as count",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."' and attendance_type = 1 and day_status = 5";
    

    $emer_leave_status = $pdo->select($table_details, $where);

    if (!($emer_leave_status->status)) {

        print_r($emer_leave_status);

    } else {
        
        if(!empty($emer_leave_status->data[0])) {
            $emer_leave_sts    = $emer_leave_status->data[0]['count'];
        }else{
            $emer_leave_sts    = "";
        }
        
    }
        return $emer_leave_sts;
}
//absent count for leave
// function get_absent_count_leave($month,$year,$staff_id,$date_month) {
//     global $pdo;

//     $current_month = $year."-".$month;
//     $date          = $year."-".$month-$date_month;
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "COUNT(attendance_type) as absent_count",
        
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

   

//    $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date like '%".$current_month."%' and entry_date < '".$date."' and staff_id = '".$staff_id."'";
    

//     $absent_count = $pdo->select($table_details, $where);

//     if (!($absent_count->status)) {

//         print_r($absent_count);

//     } else {

//         $absent_count  = $absent_count->data[0];

//         $absent_cnt    = $absent_count['absent_count'];
        
//     }
//         return $absent_cnt;
// }

// BID Type Function
function staff_name_lead($unique_id = "",$reporting_staff = "") {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_active = 1 and is_delete = 0";

    if($reporting_staff){
        $where .= " AND reporting_officer = '".$reporting_staff."'";
    }
   // $group_by = " reporting_officer ";
    $staff_name_list = $pdo->select($table_details, $where);
    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}
function designation_type_lead($unique_id = "",$reporting_staff = "") {
    global $pdo;

    $table_name    = "designation_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "designation",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_active = 1 and is_delete = 0";

    if($reporting_staff){
        $where .= " AND reporting_officer = '".$reporting_staff."'";
    }
   // $group_by = " reporting_officer ";
    $department_name_list = $pdo->select($table_details, $where);
    if ($department_name_list->status) {
        return $department_name_list->data;
    } else {
        print_r($department_name_list);
        return 0;
    }
}
?>