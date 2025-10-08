<?php 

//Work From Home count
function get_count_of_work_from_home($entry_date) {
    global $pdo;

    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_type) as work_from_home",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'is_delete = 0 AND attendance_type = 1  AND entry_date = "'.$entry_date.'" and day_type = 3';
    

    $work_from_home_count = $pdo->select($table_details, $where);

    if (!($work_from_home_count->status)) {

        print_r($work_from_home_count);

    } else {

        $work_from_home_count  = $work_from_home_count->data[0];

        $work_from_home_count    = $work_from_home_count['work_from_home'];
        
    }
        return $work_from_home_count;
}

// Full Day count
function get_count_of_full_day_leave($entry_date) {
    global $pdo;

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

    $where  = 'staff_id NOT IN (select staff_id from daily_attendance where entry_date = "'.$entry_date.'" and attendance_type = 1 and day_status != 5) and from_date = "'.$entry_date.'"';

    $full_day_count = $pdo->select($table_details, $where);

    if (!($full_day_count->status)) {

        print_r($full_day_count);

    } else {

        $full_day_count  = $full_day_count->data[0];

        $full_day_count    = $full_day_count['full_day'];
        
    }
        return $full_day_count;
}

//Half Day count
function get_count_of_half_day_leave($entry_date) {
    global $pdo;

    $table_name    = "daily_attendance";
    $date          = date('Y-m-d');

    $where         = [];
    $table_columns = [
        "COUNT(day_type) as half_day",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'is_delete = 0 AND attendance_type = 1  AND entry_date = "'.$entry_date.'" and (day_type = 2 or day_status = 4)';
    

    $half_day_count = $pdo->select($table_details, $where);

    if (!($half_day_count->status)) {

        print_r($half_day_count);

    } else {

        $half_day_count    = $half_day_count->data[0];

        $half_day_count    = floatval($half_day_count['half_day']);
        
    }
        return $half_day_count;
}

//Permission Leave count
function get_count_of_permission($entry_date) {
    global $pdo;

    $table_name    = "view_staff_permission";
    $where         = [];
    $table_columns = [
        "COUNT(staff_id) as leave_permission",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = " entry_date = '".$entry_date."' and staff_id NOT IN (select staff_id from leave_details_sub where from_date = '".$entry_date."'  and hr_approved = 1)";
    

    $leave_permission_count = $pdo->select($table_details, $where);

    if (!($leave_permission_count->status)) {

        print_r($leave_permission_count);

    } else {

        $leave_permission_count  = $leave_permission_count->data[0];

        $leave_permission_count    = $leave_permission_count['leave_permission'];
        
    }
        return $leave_permission_count;
}

//Idle count
function get_count_of_idle($entry_date) {
    global $pdo;

    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_type) as idle",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = 'is_delete = 0 AND attendance_type = 1  AND entry_date = "'.$entry_date.'" and day_type = 4';
    

    $idle_count = $pdo->select($table_details, $where);

    if (!($idle_count->status)) {

        print_r($idle_count);

    } else {

        $idle_count  = $idle_count->data[0];

        $idle_count    = $idle_count['idle'];
        
    }
        return $idle_count;
}


//On Duty count
function get_count_of_on_duty($entry_date) {
    global $pdo;

    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_type) as on_duty",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = 'is_delete = 0 AND attendance_type = 1  AND entry_date = "'.$entry_date.'" and day_type = 5';
    

    $on_duty_count = $pdo->select($table_details, $where);

    if (!($on_duty_count->status)) {

        print_r($on_duty_count);

    } else {

        $on_duty_count  = $on_duty_count->data[0];

        $on_duty_count    = $on_duty_count['on_duty'];
        
    }
        return $on_duty_count;
}

//Staff count
function get_total_staff($entry_date) {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "COUNT(staff_id) as staff",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where  = "is_active = 1 AND is_delete = 0 ";
    

    $staff_count = $pdo->select($table_details, $where);
   
    if (!($staff_count->status)) {

        print_r($staff_count);

    } else {

        $staff_count  = $staff_count->data[0];

        $staff_count    = $staff_count['staff'];
        
    }
        return $staff_count;
}

//Present count
function get_present_count($entry_date) {
    global $pdo;

    $table_name = "view_bp_attendance_test";
    $where = [];
    $table_columns = [
        "COUNT(*) as present_count",
    ];

    if ($entry_date === date('Y-m-d')) {
        // For the current date, count records with either worked_hours > '06:00:00' and exit_punch is not NULL
        // OR records with no exit_punch
        $where = "punch_date = '" . $entry_date . "' AND 
                 ((worked_hours > '06:00:00' AND exit_punch IS NOT NULL) OR exit_punch IS NULL)";
    } else {
        // For other dates, only count records with worked_hours > '06:00:00' and exit_punch is not NULL
        $where = "punch_date = '" . $entry_date . "' AND worked_hours > '06:00:00' AND exit_punch IS NOT NULL";
    }

    $table_details = [
        $table_name,
        $table_columns
    ];

    // Execute the query
    $present_count = $pdo->select($table_details, $where);
    if (!($present_count->status)) {
        print_r($present_count);
    } else {
        $present_count = $present_count->data[0];
        $present_cnt = $present_count['present_count'];
    }

    return $present_cnt;
}


//Late count
function get_count_of_late($entry_date) {
    global $pdo;

    $table_name    = "view_staff_check_in";
    $where         = [];
    $table_columns = [
        "COUNT(entry_date) as late_count",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];
    
    $where  = "entry_date = '".$entry_date."' AND day_status = 2 and staff_id NOT IN (select staff_id from leave_details_sub where from_date = '".$entry_date."' and hr_approved = 1)";

    $late_count = $pdo->select($table_details, $where);

    if (!($late_count->status)) {

        print_r($late_count);

    } else {

        $late_count  = $late_count->data[0];

        $late_cnt    = $late_count['late_count'];
    }
        return $late_cnt;
}

//absent count
function get_absent_count($entry_date) {
    global $pdo;

    $where         = [];
    $table_columns = [
        "COUNT(distinct unique_id) as absent_count",
    ];

    $table_details = [
        "staff join view_staff_current_date_status",
        $table_columns
    ];
    
    $where  = 'unique_id not in (select staff_id from view_staff_current_date_status where entry_date = "'.$entry_date.'") and staff.is_active = 1 and staff.is_delete = 0';


    $absent_count = $pdo->select($table_details, $where);

    if (!($absent_count->status)) {

        print_r($absent_count);

    } else {

        $absent_count  = $absent_count->data[0];

        $absent_cnt    = $absent_count['absent_count'];
    }
        return $absent_cnt;
}

function btn_map_att ($lat_long = '',$check_time = '') {
    if ($lat_long) {
        $final_str = '<a target="_blank" href="https://www.google.com/maps/search/?api=1&query='.$lat_long.'">'.$check_time.'</a>';

        return $final_str;
    }
    return '';
}

// //Wrok from home check in
// function get_wfh_check_in_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_in_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 1 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $wfh = $pdo->select($table_details, $where);
    

//     if (!($wfh->status)) {

//         print_r($wfh);

//     } else {

//        return $wfh->data;
//     }
// }

// //Wrok from home check Out
// function get_wfh_check_out_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_out_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 2 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $wfh_out = $pdo->select($table_details, $where);

//     if (!($wfh_out->status)) {

//         print_r($wfh_out);

//     } else {

//        return $wfh_out->data;
//     }
// }

// //Idle check in
// function get_idle_check_in_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_in_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 1 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $idle = $pdo->select($table_details, $where);

//     if (!($idle->status)) {

//         print_r($idle);

//     } else {

//        return $idle->data;
//     }
// }

// //Idle check out
// function get_idle_check_out_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_out_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 2 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $idle_out = $pdo->select($table_details, $where);

//     if (!($idle_out->status)) {

//         print_r($idle_out);

//     } else {

//        return $idle_out->data;
//     }
// }


// // ON Duty check in
// function get_onduty_check_in_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_in_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 1 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $on_duty = $pdo->select($table_details, $where);

//     if (!($on_duty->status)) {

//         print_r($on_duty);

//     } else {

//        return $on_duty->data;
//     }
// }

// // ON Duty check out
// function get_onduty_check_out_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_out_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 2 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $on_duty = $pdo->select($table_details, $where);

//     if (!($on_duty->status)) {

//         print_r($on_duty);

//     } else {

//        return $on_duty->data;
//     }
// }


// // Half day check in
// function get_half_day_check_in_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_in_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 1 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $on_duty = $pdo->select($table_details, $where);

//     if (!($on_duty->status)) {

//         print_r($on_duty);

//     } else {

//        return $on_duty->data;
//     }
// }

// // Half day check out
// function get_half_day_check_out_time($from_date,$to_date,$staff_id) {
//     global $pdo;

//     $date    = date('Y-m-d');
//     $table_name    = "daily_attendance";
//     $where         = [];
//     $table_columns = [
//         "entry_time as check_out_time",
//         "latitude",
//         "longitude",
//         "day_status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];
    
//     $where  = "entry_date >= '".$from_date."' AND entry_date <= '".$to_date."' AND attendance_type = 2 and entry_date = '".$date."' and staff_id = '".$staff_id."'";

//     $on_duty = $pdo->select($table_details, $where);

//     if (!($on_duty->status)) {

//         print_r($on_duty);

//     } else {

//        return $on_duty->data;
//     }
// }
?>