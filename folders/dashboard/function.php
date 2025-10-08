<?php

function month_start_end_date ($query_date = "") {
    $start_date     = date('Y-m-01');
    $end_date       = date('Y-m-t');

    $month          = [];

    if ($query_date) {
        $start_date     = date('Y-m-01',strtotime($query_date));
        $end_date       = date('Y-m-t',strtotime($query_date));
    }

    $month['start_date'] = $start_date; 
    $month['end_date']   = $end_date;
    
    return $month;
}

function target_amount($team_id,$report_type,$quaterly_monthly)
{
    global $pdo;

    if($report_type == 2){
        if($quaterly_monthly == 1){
            $target_amount_columns = [
                "SUM(quater_1_amt) as target_amount"
            ];
        }else if($quaterly_monthly == 2){
            $target_amount_columns = [
                "SUM(quater_2_amt) as target_amount"
            ];
        }else if($quaterly_monthly == 3){
            $target_amount_columns = [
                "SUM(quater_3_amt) as target_amount"
            ];
        }else{
            $target_amount_columns = [
                "SUM(quater_4_amt) as target_amount"
            ];
        }
    }else if($report_type == 3){
        if(($quaterly_monthly == 4)||($quaterly_monthly == 5)||($quaterly_monthly == 6)){
            $target_amount_columns = [
                "(SUM(quater_1_amt)/3) as target_amount"
            ];
        }else if(($quaterly_monthly == 7)||($quaterly_monthly == 8)||($quaterly_monthly == 9)){
            $target_amount_columns = [
                "(SUM(quater_2_amt)/3) as target_amount"
            ];
        }else if(($quaterly_monthly == 10)||($quaterly_monthly == 11)||($quaterly_monthly == 12)){
            $target_amount_columns = [
                "(SUM(quater_3_amt)/3) as target_amount"
            ];
        }else{
            $target_amount_columns = [
                "(SUM(quater_4_amt)/3) as target_amount"
            ];
        }
    }else{
        $target_amount_columns = [
                "SUM(amount) as target_amount"
            ];
    }

    $target_amount_details = [
        "view_team_head_target", // Table Name 
        $target_amount_columns
    ];

    $target_amount_where   = [
        "team_id"   => $team_id,
    ];
       



    $target_amount_result = $pdo->select($target_amount_details,$target_amount_where);

    if ($target_amount_result->status) {
        return $target_amount_result->data;
    } else {
        print_r($target_amount_result);
    }
    return [];
}

function member_target_amount($staff_id,$report_type,$quaterly_monthly)
{
    global $pdo;

    if($report_type == 2){
        if($quaterly_monthly == 1){
            $member_target_amount_columns = [
                "SUM(quater_1_amt) as target_amount"
            ];
        }else if($quaterly_monthly == 2){
            $member_target_amount_columns = [
                "SUM(quater_2_amt) as target_amount"
            ];
        }else if($quaterly_monthly == 3){
            $member_target_amount_columns = [
                "SUM(quater_3_amt) as target_amount"
            ];
        }else{
            $member_target_amount_columns = [
                "SUM(quater_4_amt) as target_amount"
            ];
        }
    }else if($report_type == 3){
        if(($quaterly_monthly == 4)||($quaterly_monthly == 5)||($quaterly_monthly == 6)){
            $member_target_amount_columns = [
                "(SUM(quater_1_amt)/3) as target_amount"
            ];
        }else if(($quaterly_monthly == 7)||($quaterly_monthly == 8)||($quaterly_monthly == 9)){
            $member_target_amount_columns = [
                "(SUM(quater_2_amt)/3) as target_amount"
            ];
        }else if(($quaterly_monthly == 10)||($quaterly_monthly == 11)||($quaterly_monthly == 12)){
            $member_target_amount_columns = [
                "(SUM(quater_3_amt)/3) as target_amount"
            ];
        }else{
            $member_target_amount_columns = [
                "(SUM(quater_4_amt)/3) as target_amount"
            ];
        }
    }else{
        $member_target_amount_columns = [
            "SUM(amount) as target_amount"
        ];
    }

    $member_target_amount_details = [
        "view_team_members_target", // Table Name 
        $member_target_amount_columns
    ];

    $member_target_amount_where   = [
        "staff_id"   => $staff_id,
    ];

    $member_target_amount_result = $pdo->select($member_target_amount_details,$member_target_amount_where);

    if ($member_target_amount_result->status) {
        return $member_target_amount_result->data;
    } else {
        print_r($member_target_amount_result);
    }
    return [];
}


function array_by_string($arr_string = "",$extract_by = ",")
{
    $ext_arr    = [];

    if ($arr_string) {
        $ext_arr = explode($extract_by,$arr_string);
    }

    return $ext_arr;
}

function year_quarter_month ($type = "",$input = "") {
    
    $account_year = $_SESSION['acc_year'];

    $account_year = explode("-",$account_year);

    $from_date  = "";
    $to_date    = "";

    switch ($type) {
        case 'quarter':
            
            switch ($input) {
                // First Quarter
                case 1:
                    $from_date = $account_year[0]."-04-01";
                    $to_date   = $account_year[0]."-06-30"; 
                    break;
                    
                // Second Quarter
                case 2:
                    $from_date = $account_year[0]."-07-01";
                    $to_date   = $account_year[0]."-09-31";
                    break;
                    
                // Third Quarter
                case 3:
                    $from_date = $account_year[0]."-10-01";
                    $to_date   = $account_year[0]."-12-30";
                    break;
                    
                // Fourth Quarter
                default:
                    $from_date = $account_year[1]."-01-01";
                    $to_date   = $account_year[1]."-03-31";
                    break;
            }
            break;

        case 'month':
            $year = $account_year[0];

            if ($input < 4) {
                $year = $account_year[1];
            }

            $from_date = $year."-".$input."-01";
            $to_date   = date('Y-m-t', strtotime($from_date));
        
            break;
        
        default:
            // Accounting Year  
            $from_date = $account_year[0]."-04-01";
            $to_date   = $account_year[1]."-03-31";
            break;
    }
    return [
        "from_date" => $from_date,
        "to_date"   => $to_date
    ];
}


function team_head_dispaly($user_id)
{
    global $pdo;

    $team_heads_sql = "SELECT unique_id,staff_unique_id AS staff_id,team_members FROM user WHERE is_delete = 0 AND is_team_head = 1";

    $team_head_columns = [
        "unique_id",
        "staff_unique_id",
        "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
        "team_members",
        "team_id",
        "profile_image"
    ];

    $team_head_details = [
        "user", // Table Name 
        $team_head_columns
    ];

    $team_head_where   = [
        "is_delete"    => 0,
        "is_team_head" => 1,
        "unique_id"    => $user_id
    ];

    $team_head_result = $pdo->select($team_head_details,$team_head_where);

    if ($team_head_result->status) {
        return $team_head_result->data;
    } else {
        print_r($team_head_result);
    }
    return [];
}

function team_members_display($user_id)
{
    global $pdo;
            $team_members_columns = [
            "unique_id",
            "staff_unique_id",
            "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
            "(SELECT file_name FROM staff WHERE unique_id = user.staff_unique_id) AS user_image",
            "profile_image"
        ];
    
        $team_members_details = [
            "user", // Table Name 
            $team_members_columns
        ];

       $team_members_where   = [
            "is_delete"    => 0,
            "unique_id"    => $user_id
        ];
    
        $team_members_result = $pdo->select($team_members_details,$team_members_where);
    
        if ($team_members_result->status) {
            return $team_members_result->data;
        } else {
            print_r($team_members_result);
        }
        
    return [];
}


function business_forecast_tbody_sql ($stage = 1,$where = "",$commit_where) {

    $sql = "";

    if ($stage) {
        switch ($stage) {
            case 1:

                $sql         = "SELECT (SELECT IFNULL(SUM(lead),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed, IFNULL(SUM(lpd.total),0.00) AS achieved FROM lead_product_details AS lpd LEFT JOIN follow_ups AS fu ON lpd.follow_up_unique_id = fu.unique_id WHERE fu.is_delete = 0 $where";
        
                break;
        
            case 2:
                // $column_name = "funnel_upside";
                $sql        = "SELECT (SELECT IFNULL(SUM(funnel_upside),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,IFNULL(SUM(fpd.total),0.00) AS achieved FROM funnel_product_details AS fpd LEFT JOIN follow_ups AS fu ON fpd.follow_up_unique_id = fu.unique_id WHERE fu.is_delete = 0 AND fu.funnel_type = 1 $where";
                break;
        
            case 3:
                $column_name = "funnel_commit";
                $sql        = "SELECT (SELECT IFNULL(SUM(funnel_commit),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,IFNULL(SUM(fpd.total),0.00) AS achieved FROM funnel_product_details AS fpd LEFT JOIN follow_ups AS fu ON fpd.follow_up_unique_id = fu.unique_id WHERE fu.is_delete = 0 AND fu.funnel_type = 2 $where";
                break;
        
            case 4:
                $column_name = "purchase";
                $sql        =  "SELECT (SELECT IFNULL(SUM(purchase_order),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,0 AS achieved";
                break;
        
            case 5:
                $column_name = "billing";
                $sql        = "SELECT (SELECT IFNULL(SUM(billing),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,0 AS achieved";
                break;
        
            case 6:
                $column_name = "order";
                $sql        = "SELECT (SELECT IFNULL(SUM(payment),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,0 AS achieved";
                break;
            
            default:
                # code...
                break;
        }
    }

    return $sql;
}


function progess_bar ($value) {
    
    $class_name = "";
    
    switch (true) {
        case (($value >= 0) && ($value < 26)):
            $class_name = "bg-danger";
            break;
        
        case (($value >= 26) && ($value < 51)):
            $class_name = "bg-warning";
            break;
            
        case (($value >= 51) && ($value < 76)):
            $class_name = "bg-info";
            break;
            
        case (($value >= 76) && ($value <= 100)):
            $class_name = "bg-success";
        break;
        
        default:
            $class_name = "bg-success";
        break;
    }

    $return_arr = [
        "class" => $class_name
    ];

    return $return_arr;
}
    

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

function total_sundays_days($date,$month,$year)
{
    $sundays=0;
    $total_days= $date;
    for($i=1;$i<=$total_days;$i++){
        if(date('N',strtotime($year.'-'.$month.'-'.$i))==7){
            $sundays++;
        }
    }

return $sundays;
}

//holiday leave count
function get_holiday_leave($month,$year) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "attendance_holidays";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "COUNT(id) as holiday",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'is_delete = 0 AND holiday_date like "%'.$current_month.'%" and holiday_date <= "'.$date.'"'; 
    $holiday_leave_count = $pdo->select($table_details, $where);

    if (!($holiday_leave_count->status)) {

        print_r($holiday_leave_count);

    } else {

        $holiday_leave_count  = $holiday_leave_count->data[0];

        $holiday_count    = $holiday_leave_count['holiday'];
        
    }
        return $holiday_count;
}


//full day leave count
function get_full_day_leave($month,$year,$staff_id) {
    global $pdo;
    $date          =   date('Y-m-d');
    $current_month = $year."-".$month;
    $table_name    = "view_full_day_leave";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "COUNT(day_type) as full_day",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   // $where  = 'is_delete = 0 AND hr_approved = 1  and day_type = 1 and from_date like "%'.$current_month.'%"  and cancel_status = 0 and staff_id ="'.$staff_id.'"';
    $where  = 'from_date like "%'.$current_month.'%" and from_date<="'.$date.'"  and staff_id ="'.$staff_id.'"';
    
    $full_leave_count = $pdo->select($table_details, $where);
    if (!($full_leave_count->status)) {

        print_r($full_leave_count);

    } else {

        $full_leave_count  = $full_leave_count->data[0];

        $full_day_count    = $full_leave_count['full_day'];
        
    }
        return $full_day_count;
}


//half day leave count
function get_half_day_leave($month,$year,$staff_id) {
    global $pdo;
    $date          =   date('Y-m-d');
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

    

   $where  = 'is_delete = 0 AND hr_approved = 1  and day_type = 2 and from_date like "%'.$current_month.'%" and from_date<="'.$date.'" and cancel_status = 0 and staff_id ="'.$staff_id.'"';
    

    $half_leave_count = $pdo->select($table_details, $where);
// echo $half_leave_count->sql;
    if (!($half_leave_count->status)) {

        print_r($half_leave_count);

    } else {

        $half_leave_count  = $half_leave_count->data[0];

         $half_day_count    = floatval($half_leave_count['half_day_leave'])/2;
        
    }
        return $half_day_count;
}

//leave count
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


//Check Out count
function get_check_out_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $date = date('Y-m-d');
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "entry_date",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];
    $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 1 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date <= '".$date."'";
    

    $check_out_count = $pdo->select($table_details, $where);
    $result_array = $check_out_count->data;
    $chk_out_cnt = '0';
    $main_cnt    = '0';
    foreach($result_array as $val)
    {
        $main_cnt = $main_cnt;
        $main_cnt++;
        $table_columns_sub = [
            "entry_date",
            
        ];
    
        $table_details_sub = [
            $table_name,
            $table_columns_sub
        ];
        $where_sub  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date='".$val['entry_date']."' and staff_id = '".$staff_id."'";
        $check_out_count_sub = $pdo->select($table_details_sub, $where_sub);
        $result_array_sub = $check_out_count_sub->data;

        foreach($result_array_sub as $val_sub)
        {
            $chk_out_cnt = $chk_out_cnt;
            $chk_out_cnt++;
        }
    }
    $count  =   $main_cnt - $chk_out_cnt;

        return $count;
}


//Late count
function get_late_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
     $date = date('Y-m-d');
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as late_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

   $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 1 AND day_status = 2 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date < '".$date."'";
    

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
     $date = date('Y-m-d');
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

    

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 3 AND attendance_type = 1  AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date < '".$date."'";
    

    $permission_count = $pdo->select($table_details, $where);

    if (!($permission_count->status)) {

        print_r($permission_count);

    } else {

        $permission_count  = $permission_count->data[0];

        $permission_cnt    = $permission_count['permission_count'];
        
    }
        return $permission_cnt;
}

//absent count
function get_absent_count($month,$year,$staff_id) {
    global $pdo;
    $date = date('Y-m-d');
    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(attendance_type) as absent_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];



   $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date < '".$date."'";
    
$group_by = "";
    $absent_count = $pdo->select($table_details, $where, "", "", "","",$group_by);

    if (!($absent_count->status)) {

        print_r($absent_count);

    } else {

        $absent_count  = $absent_count->data[0];

        $absent_cnt    = $absent_count['absent_count'];
        
    }
        return $absent_cnt;
}


//absent count for leave
function get_absent_count_leave($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(attendance_type) as absent_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $date  = date('Y-m-d');

   $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date like '%".$current_month."%' and entry_date < '".$date."' and staff_id = '".$staff_id."'";
    

    $absent_count = $pdo->select($table_details, $where);

    if (!($absent_count->status)) {

        print_r($absent_count);

    } else {

        $absent_count  = $absent_count->data[0];

        $absent_cnt    = $absent_count['absent_count'];
        
    }
        return $absent_cnt;
}


//half Present count
function get_half_present_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as half_present_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 4 AND attendance_type = 1 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."'";
    

    $half_present_count = $pdo->select($table_details, $where);
    if (!($half_present_count->status)) {

        print_r($half_present_count);

    } else {

        $half_present_count  = $half_present_count->data[0];

        $half_present_cnt    = ($half_present_count['half_present_count']/2);
        
    }
        return $half_present_cnt;
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

    $table_name    = "view_staff_check_in";
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

    $table_name    = "view_staff_check_out";
    $where         = [];
    $table_columns = [
        "entry_time",
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
            $check_out_time    = $check_out->data[0]['entry_time'];
        }else{
            $check_out_time    = "";
        }
        
    }
        return $check_out_time;
}

function get_check_in_time($staff_id,$entry_date){
    global $pdo;

    $table_name    = "view_staff_check_in";
    $where         = [];
    $table_columns = [
        "entry_time",
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
            $check_in_time    = $check_in->data[0]['entry_time'];
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

function btn_map_att ($lat_long = '',$entry_time = '') {

    if ($lat_long) {
        $final_str = '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query='.$lat_long.'">'.$entry_time.'</a>';

        return $final_str;
    }

    return '';

}


function get_birthday_cnt($today) {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "COUNT(staff_id) as cnt",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "date_of_birth like '%".$today."%' AND is_active = 1 and is_delete = 0";
    

    $cnt = $pdo->select($table_details, $where);

    if (!($cnt->status)) {

        print_r($cnt);

    } else {

        $cnt  = $cnt->data[0];

        $birthday_count    = $cnt['cnt'];
        
    }
        return $birthday_count;
}

function get_doj_cnt($today) {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "COUNT(staff_id) as cnt",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "date_of_join like '%".$today."%' AND is_active = 1 and is_delete = 0";
    

    $cnt = $pdo->select($table_details, $where);

    if (!($cnt->status)) {

        print_r($cnt);

    } else {

        $cnt  = $cnt->data[0];

        $doj_count    = $cnt['cnt'];
        
    }
        return $doj_count;
}

?>