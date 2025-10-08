<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "leave_details";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';
// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$call_type          = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$day_type_options   = [
    [
        "id"    => 1,
        "text"  => "Full Day"
    ],
    [
        "id"    => 2,
        "text"  => "Half Day"
    ],
    [
        "id"    => 3,
        "text"  => "Work From Home"
    ],
    [
        "id"    => 4,
        "text"  => "Idle"
    ],
    [
        "id"    => 5,
        "text"  => "On-Duty"
    ],
    [
        "id"    => 6,
        "text"  => "Permission"
    ]
];
$day_status            = [
    0 => [
        "id"    => 0,
        "text"  => "Leave"
    ],
    1 => [
        "id"    => 1,
        "text"  => "Present"
    ],
    2 => [
        "id"    => 2,
        "text"  => "Late"
    ],
    3 => [
        "id"    => 3,
        "text"  => "Permission"
    ],
    4 => [
        "id"    => 4,
        "text"  => "Half-Day Leave"
    ],
    5 => [
        "id"    => 5,
        "text"  => "Leave"
    ],
    6 => [
        "id"    => 6,
        "text"  => "Absent"
    ]
];

$projects = [];
if (isset($_POST['project'])) {
    // Always cast to array, even if only one selected
    $projects = (array) $_POST['project']; 
}


switch ($action) {

    case 'fullday_leave_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];
        $projects   = $_POST['project'] ?? [];

        if($length == '-1') {
            $limit  = "";
        }
        

            $total_staff    = get_total_staff($entry_date, null, $projects);
            // $full_day_leave = get_count_of_full_day_leave($entry_date);
            // $half_day_leave = get_count_of_half_day_leave($entry_date);
            // $work_from_home = get_count_of_work_from_home($entry_date);
            // $idle           = get_count_of_idle($entry_date);
            // $on_duty        = get_count_of_on_duty($entry_date);
            // $permission     = get_count_of_permission($entry_date);
            // $late           = get_count_of_late($entry_date);
            $present_staff  = get_present_count($entry_date, null, $projects);
            $absent_staff   = $total_staff - $present_staff;

            // $total_staff      = $full_day_leave +  $half_day_leave + $work_from_home + $idle + $on_duty + $permission + $late + $present_staff + $absent_staff;
           
            
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = view_full_day_leave.staff_id) as employee_id",
            "(select staff_name from staff where staff.unique_id = view_full_day_leave.staff_id) as employee_name",
            "(select work_location from staff where staff.unique_id = view_full_day_leave.staff_id) as work_location",
            "day_type",
            "unique_id"
        ];

        $table_details  = [
            "view_full_day_leave , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        $where  = 'staff_id NOT IN (select staff_id from daily_attendance where entry_date = "'.$entry_date.'" and attendance_type = 1 and day_status != 5) and from_date = "'.$entry_date.'"';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['day_type'] == 5){
                    $value['day_type']       = "Full Day";
                }else{
                    $value['day_type']       = $day_type_options[$value['day_type']-1]['text'];
                }
                
                $data[]             = array_values($value);
            }
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                "total_staff"       => $total_staff,
                "full_day_leave"    => $full_day_leave,
                "half_day_leave"    => $half_day_leave,
                "work_from_home"    => $work_from_home,
                "idle"              => $idle,
                "on_duty"           => $on_duty,
                "permission"        => $permission, 
                "late"              => $late, 
                "present_staff"     => $present_staff, 
                "non_present_staff" => $absent_staff, 
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
        
    case 'halfday_leave_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];

        if($length == '-1') {
            $limit  = "";
        }

                
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = daily_attendance.staff_id) as employee_id",
            "(select work_location from staff where staff.unique_id = daily_attendance.staff_id) as work_location",
            "(select staff_name from staff where staff.unique_id = daily_attendance.staff_id) as staff_name",
            "entry_time as check_in_time",
            "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_time",
            "'' as total_work_time",
            "day_status",
            "unique_id",
            "latitude",
            "longitude",
            "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_latitude",
            "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_longitude",
           
        ];
        $table_details  = [
            "daily_attendance , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

       $where  = 'is_delete = 0 AND attendance_type = 1  AND entry_date = "'.$entry_date.'" and (day_type = 2 or day_status = 4)';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['check_in_time']){
                    $check_in_time_val  = date_create($value['check_in_time']);
                    $check_in_time      = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time      = "";
                }
                if($value['check_out_time']){
                    $check_out_time_val  = date_create($value['check_out_time']);
                    $check_out_time      = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time      = "";
                } 

                $time1 = new DateTime($value['check_in_time']);
                $time2 = new DateTime($value['check_out_time']);
                if(($time2 == '')&&($entry_date != $today)){
                    $time2 = new DateTime("19:00:00");
                }
                $timediff = $time1->diff($time2);
                if($timediff){
                    $total_work_time   = $timediff->format('%H:%i');
                }else{
                    $total_work_time   = "-";
                }
                $work_time = strtotime($total_work_time);
                $working_time = strtotime("09:00");
                if($work_time < $working_time){
                    $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
                }else{
                    $value['total_work_time'] = $total_work_time;
                }


                $value['day_status']       = $day_status[$value['day_status']]['text'];
                $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
                $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);
                $value['staff_name']       = $value['staff_name'];

                if($_SESSION['sess_user_type'] == '5ff71f5fb5ca556748'){
                    $btn_update                = btn_update('daily_attendances',$value['unique_id'],'','',$entry_date,"day_attendance_report");
                        $btn_create                = btn_create('daily_attendances','','',$entry_date,"day_attendance_report");
                }else{
                    $btn_create                = '';
                    $btn_update                = '';
                }

                $value['unique_id']        = $btn_update.$btn_create;

               

                $data[]             = array_values($value);
            }
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'work_from_home_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];

        if($length == '-1') {
            $limit  = "";
        }


        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = daily_attendance.staff_id) as employee_id",
            "(select work_location from staff where staff.unique_id = daily_attendance.staff_id) as work_location",
            "(select staff_name from staff where staff.unique_id = daily_attendance.staff_id) as staff_name",
            "entry_time as check_in_time",
            "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_time",
            "'' as total_work_time",
            "day_status",
            "latitude",
            "longitude",
            "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_latitude",
            "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_longitude",
            "unique_id"
        ];
        $table_details  = [
            "daily_attendance , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

       $where  = 'is_delete = 0 AND attendance_type = 1 AND  entry_date = "'.$entry_date.'" and day_type = 3';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['check_in_time']){
                    $check_in_time_val  = date_create($value['check_in_time']);
                    $check_in_time      = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time      = "";
                }
                if($value['check_out_time']){
                    $check_out_time_val  = date_create($value['check_out_time']);
                    $check_out_time      = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time      = "";
                } 

                $time1 = new DateTime($value['check_in_time']);
                $time2 = new DateTime($value['check_out_time']);
                if(($time2 == '')&&($entry_date != $today)){
                    $time2 = new DateTime("19:00:00");
                }
                $timediff = $time1->diff($time2);
                $timediff = $time1->diff($time2);
                if($timediff){
                    $total_work_time   = $timediff->format('%H:%i');
                }else{
                    $total_work_time   = "-";
                }
                $work_time = strtotime($total_work_time);
                $working_time = strtotime("09:00");
                if($work_time < $working_time){
                    $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
                }else{
                    $value['total_work_time'] = $total_work_time;
                }


                $value['day_status']       = $day_status[$value['day_status']]['text'];
                $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
                $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);
                $value['staff_name']       = $value['staff_name'];

                

                $data[]             = array_values($value);
            }
            
            $json_array = [
                 "draw"             => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'idle_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = daily_attendance.staff_id) as employee_id",
            "(select work_location from staff where staff.unique_id = daily_attendance.staff_id) as work_location",
            "(select staff_name from staff where staff.unique_id = daily_attendance.staff_id) as staff_name",
            "entry_time as check_in_time",
            "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_time",
            "'' as total_work_time",
            "day_status",
            "latitude",
            "longitude",
            "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_latitude",
            "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_longitude",
            "unique_id"
        ];
        $table_details  = [
            "daily_attendance , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

       $where  = 'is_delete = 0 AND attendance_type = 1 AND entry_date = "'.$entry_date.'" and day_type = 4';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['check_in_time']){
                    $check_in_time_val  = date_create($value['check_in_time']);
                    $check_in_time      = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time      = "";
                }
                if($value['check_out_time']){
                    $check_out_time_val  = date_create($value['check_out_time']);
                    $check_out_time      = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time      = "";
                } 

                $time1 = new DateTime($value['check_in_time']);
                $time2 = new DateTime($value['check_out_time']);
                if(($time2 == '')&&($entry_date != $today)){
                    $time2 = new DateTime("19:00:00");
                }
                $timediff = $time1->diff($time2);
                $timediff = $time1->diff($time2);
                if($timediff){
                    $total_work_time   = $timediff->format('%H:%i');
                }else{
                    $total_work_time   = "-";
                }
                $work_time = strtotime($total_work_time);
                $working_time = strtotime("09:00");
                if($work_time < $working_time){
                    $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
                }else{
                    $value['total_work_time'] = $total_work_time;
                }

                $value['day_status']       = $day_status[$value['day_status']]['text'];
                $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
                $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);
                $value['staff_name']       = $value['staff_name'];

                


                $data[]             = array_values($value);
            }
            
            $json_array = [
                 "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'onduty_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];

        if($length == '-1') {
            $limit  = "";
        }


        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = daily_attendance.staff_id) as employee_id",
            "(select work_location from staff where staff.unique_id = daily_attendance.staff_id) as work_location",
            "(select staff_name from staff where staff.unique_id = daily_attendance.staff_id) as staff_name",
            "entry_time as check_in_time",
            "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_time",
            "'' as total_work_time",
            "day_status",
            "latitude",
            "longitude",
            "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_latitude",
            "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."') as check_out_longitude",
            "unique_id"
        ];
        $table_details  = [
            "daily_attendance , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

       $where  = 'is_delete = 0 AND attendance_type = 1  AND entry_date = "'.$entry_date.'" and day_type = 5';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['check_in_time']){
                    $check_in_time_val  = date_create($value['check_in_time']);
                    $check_in_time      = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time      = "";
                }
                if($value['check_out_time']){
                    $check_out_time_val  = date_create($value['check_out_time']);
                    $check_out_time      = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time      = "";
                } 

                $time1 = new DateTime($value['check_in_time']);
                $time2 = new DateTime($value['check_out_time']);
                if(($time2 == '')&&($entry_date != $today)){
                    $time2 = new DateTime("19:00:00");
                }
                $timediff = $time1->diff($time2);
                $timediff = $time1->diff($time2);
                if($timediff){
                    $total_work_time   = $timediff->format('%H:%i');
                }else{
                    $total_work_time   = "-";
                }
                $work_time = strtotime($total_work_time);
                $working_time = strtotime("09:00");
                if($work_time < $working_time){
                    $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
                }else{
                    $value['total_work_time'] = $total_work_time;
                }

                $value['day_status']       = $day_status[$value['day_status']]['text'];
                $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
                $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);
                $value['staff_name']       = $value['staff_name'];

                


                $data[]             = array_values($value);
            }
            
            $json_array = [
                 "draw"             => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                   
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'permission_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];

        if($length == '-1') {
            $limit  = "";
        }
        
        $date = date('Y-m-d');

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = view_staff_permission.staff_id) as employee_id",
            "(select work_location from staff where staff.unique_id = view_staff_permission.staff_id) as work_location",
            "(select staff_name from staff where staff.unique_id = view_staff_permission.staff_id) as staff_name",
            "(select entry_time from view_staff_check_in where view_staff_check_in.staff_id = view_staff_permission.staff_id and entry_date = '".$entry_date."') as check_in_time",
            "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = view_staff_permission.staff_id and entry_date = '".$entry_date."') as check_out_time",
            "'' as total_work_time",
            "'' as day_type",
            "unique_id",
            "(select latitude from view_staff_check_in where view_staff_check_in.staff_id = view_staff_permission.staff_id and entry_date = '".$entry_date."') as latitude",
            "(select longitude from view_staff_check_in where view_staff_check_in.staff_id = view_staff_permission.staff_id and entry_date = '".$entry_date."') as longitude",
            "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = view_staff_permission.staff_id and entry_date = '".$entry_date."') as check_out_latitude",
            "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = view_staff_permission.staff_id and entry_date = '".$entry_date."') as check_out_longitude",
            "table_type"
           
        ];
        $table_details  = [
            "view_staff_permission , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        $where  = 'entry_date = "'.$entry_date.'" and staff_id NOT IN (select staff_id from leave_details_sub where from_date = "'.$entry_date.'" and hr_approved = 1)';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;
// echo $result->sql;
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['check_in_time']){
                    $check_in_time_val  = date_create($value['check_in_time']);
                    $check_in_time      = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time      = "";
                }
                if($value['check_out_time']){
                    $check_out_time_val  = date_create($value['check_out_time']);
                    $check_out_time      = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time      = "";
                } 

                $time1 = new DateTime($value['check_in_time']);
                $time2 = new DateTime($value['check_out_time']);
                if(($time2 == '')&&($entry_date != $today)){
                    $time2 = new DateTime("19:00:00");
                }
                $timediff = $time1->diff($time2);
                if($timediff){
                    $total_work_time   = $timediff->format('%H:%i');
                }else{
                    $total_work_time   = "-";
                }
                $work_time = strtotime($total_work_time);
                $working_time = strtotime("09:00");
                if($work_time < $working_time){
                    $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
                }else{
                    $value['total_work_time'] = $total_work_time;
                }

                $value['day_type']   = "Permission";
                $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
                $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);

                if($_SESSION['sess_user_type'] == '5ff71f5fb5ca556748'){
                    if($value['table_type'] == 'daily_attendance'){
                        $btn_update                = btn_update('daily_attendances',$value['unique_id'],'','',$entry_date,"day_attendance_report");
                        $btn_create                = btn_create('daily_attendances','','',$entry_date,"day_attendance_report");
                    }else{
                        $btn_create                = '';
                        $btn_update                = ''; 
                    }
                }else{
                    $btn_update                = '';
                    $btn_create                = '';
                }

                $value['unique_id']        = $btn_update.$btn_create;

                
                $data[]             = array_values($value);
            }
           
            $json_array = [
                 "draw"             => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
               
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    // case 'present_staff_report_datatable':
    //     // DataTable Variables
    //     $search     = $_POST['search']['value'];
    //     $length     = $_POST['length'];
    //     $start      = $_POST['start'];
    //     $draw       = $_POST['draw'];
    //     $limit      = $length;

    //     $data       = [];
    //     $where_arr  = [];
    //     $total      = 0;

    //     $entry_date = $_POST['entry_date'];

    //     if($length == '-1') {
    //         $limit  = "";
    //     }

               
    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "@a:=@a+1 s_no",
    //         "(select employee_id from staff where staff.unique_id = daily_attendance.staff_id) as employee_id",
    //         "(select work_location from staff where staff.unique_id = daily_attendance.staff_id) as work_location",
    //         "(select staff_name from staff where staff.unique_id = daily_attendance.staff_id) as staff_name",
    //         "entry_time as check_in_time",
    //         "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."' order by entry_date DESC LIMIT 1) as check_out_time",
    //         "'' as total_work_time",
    //         "day_status",
    //         "edit_reason",
    //         "unique_id",
    //         "latitude",
    //         "longitude",
    //         "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."' order by entry_date DESC LIMIT 1) as check_out_latitude",
    //         "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."' order by entry_date DESC LIMIT 1) as check_out_longitude",
    //         "unique_id"
    //     ];

    //     $table_details  = [
    //         "daily_attendance , (SELECT @a:= ".$start.") AS a ",
    //         $columns
    //     ];

    //     $where  = 'is_delete = 0 AND attendance_type = 1 AND day_status = 1 AND (day_type = 7 or day_type = 1) AND entry_date = "'.$entry_date.'"';

    //     $order_by       = " ";
    //     $sql_function   = "SQL_CALC_FOUND_ROWS";

    //     $result         = $pdo->select($table_details,$where,$limit,$start,"",$sql_function);
    //     $total_records  = total_records();

    //     $s_no           = 1 + $start;

    //     if ($result->status) {

    //         $res_array      = $result->data;

    //         foreach ($res_array as $key => $value) {

    //             if($value['check_in_time']){
    //                 $check_in_time_val  = date_create($value['check_in_time']);
    //                 $check_in_time      = date_format($check_in_time_val,"H:i a");
    //             }else{
    //                 $check_in_time      = "";
    //             }
    //             if($value['check_out_time']){
    //                 $check_out_time_val  = date_create($value['check_out_time']);
    //                 $check_out_time      = date_format($check_out_time_val,"H:i a");
    //             }else{
    //                 $check_out_time      = "";
    //             } 


    //             $time1 = new DateTime($value['check_in_time']);
    //             $time2 = new DateTime($value['check_out_time']);
    //             if(($time2 == '')&&($entry_date != $today)){
    //                 $time2 = new DateTime("19:00:00");
    //             }
    //             $timediff = $time1->diff($time2);
    //             $timediff = $time1->diff($time2);
    //             if($timediff){
    //                 $total_work_time   = $timediff->format('%H:%i');
    //             }else{
    //                 $total_work_time   = "-";
    //             }
    //             $work_time = strtotime($total_work_time);
    //             $working_time = strtotime("09:00");
    //             if($work_time < $working_time){
    //                 $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
    //             }else{
    //                 $value['total_work_time'] = $total_work_time;
    //             }

    //             $value['day_status']       = $day_status[$value['day_status']]['text'];
    //             $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
    //             $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);
    //             $value['staff_name']       = $value['staff_name'];

    //             if($_SESSION['sess_user_type'] == '5ff71f5fb5ca556748'){
    //                 $value['edit_reason']   = $value['edit_reason'];
    //             }else{
    //                 $value['edit_reason']   = '';
    //             }

    //             if($_SESSION['sess_user_type'] == '5ff71f5fb5ca556748'){
    //                 $btn_update                = btn_update('daily_attendances',$value['unique_id'],'','',$entry_date,"day_attendance_report");
    //                 $btn_create                = btn_create('daily_attendances','','',$entry_date,"day_attendance_report");
    //             }else{
    //                 $btn_create                = '';
    //                 $btn_update                = '';
    //             }

    //             $value['unique_id']        = $btn_update.$btn_create;

                

    //             $data[]             = array_values($value);
    //         }
            
    //         $json_array = [
    //             "draw"              => intval($draw),
    //             "recordsTotal"      => intval($total_records),
    //             "recordsFiltered"   => intval($total_records),
    //             "data"              => $data,
    //             "testing"           => $result->sql,
               
    //         ];
    //     } else {
    //         print_r($result);
    //     }
        
    //     echo json_encode($json_array);
    //     break;

   case 'present_staff_report_datatable':
    // DataTable Variables
    $search     = $_POST['search']['value'] ?? '';
    $length     = $_POST['length'] ?? 10;
    $start      = $_POST['start'] ?? 0;
    $draw       = $_POST['draw'] ?? 1;
    $limit      = ($length == '-1') ? "" : $length;

    $data       = [];
    $entry_date = $_POST['entry_date'] ?? '';

    // Get selected project(s) from POST
    $project = $_POST['project'] ?? '';
    $project_condition = "";
    if (!empty($project)) {
        // if it's an array from multiselect, implode it
        if (is_array($project)) {
            $project_list = array_map('trim', $project);
            $project_condition = " AND work_location IN ('" . implode("','", $project_list) . "')";
        } else {
            $project_condition = " AND work_location = '" . trim($project) . "'";
        }
    }

    // Query Variables
    $json_array = "";
    $columns    = [
        "@a:=@a+1 s_no",
        "employee_id",
        "punch_date",
        "employee_name",
        "entry_punch",
        "exit_punch",
        "worked_hours",
        "work_location"
    ];

    $table_details = [
        "view_bp_present, (SELECT @a:= " . $start . ") AS a",
        $columns
    ];

    if ($entry_date === date('Y-m-d')) {
        $where = "punch_date = '" . $entry_date . "' 
                  AND ((worked_hours >= '06:00:00' AND exit_punch IS NOT NULL) 
                  OR exit_punch IS NULL)" 
                  . $project_condition;
    } else {
        $where = "punch_date = '" . $entry_date . "' 
                  AND worked_hours >= '06:00:00' 
                  AND exit_punch IS NOT NULL" 
                  . $project_condition;
    }
    // error_log("Present Staff WHERE: " . $where, 3, "present_debug.log");

    $order_by     = " ";
    $sql_function = "SQL_CALC_FOUND_ROWS";

    $result        = $pdo->select($table_details, $where, $limit, $start, "", $sql_function);
    $total_records = total_records();

    $s_no = 1 + $start;

    if ($result->status) {
        $res_array = $result->data;

        foreach ($res_array as $key => $value) {
            $entry_punch = $value['entry_punch'] ? date('H:i:s', strtotime($value['entry_punch'])) : "-";
            $exit_punch  = $value['exit_punch'] ? date('H:i:s', strtotime($value['exit_punch'])) : "-";

            $worked_hours = $value['worked_hours'] ?? "-";
            if ($value['exit_punch'] === NULL) {
                $worked_hours = "-";
            }

            $employee_id = strtoupper($value['employee_id']);

            $data[] = [
                $s_no++,
                $value['punch_date'],
                $employee_id,
                $value['employee_name'],
                $entry_punch,
                $exit_punch,
                $worked_hours,
                $value['work_location']
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
        // error_log("PDO Error: " . print_r($result, true), 3, "present_debug.log");
        $json_array = [
            "draw" => intval($draw),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($json_array);
    exit;

    break;


    case 'late_report_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $entry_date = $_POST['entry_date'];

        if($length == '-1') {
            $limit  = "";
        }

               
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select employee_id from staff where staff.unique_id = daily_attendance.staff_id) as employee_id",
            "(select work_location from staff where staff.unique_id = daily_attendance.staff_id) as work_location",
            "(select staff_name from staff where staff.unique_id = daily_attendance.staff_id) as staff_name",
            "entry_time as check_in_time",
            "(select entry_time from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."' order by entry_date DESC LIMIT 1) as check_out_time",
            "'' as total_work_time",
            "day_status",
            "unique_id",
            "latitude",
            "longitude",
            "(select latitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."' order by entry_date DESC LIMIT 1) as check_out_latitude",
            "(select longitude from view_staff_check_out where view_staff_check_out.staff_id = daily_attendance.staff_id and entry_date = '".$entry_date."' order by entry_date DESC LIMIT 1) as check_out_longitude",
            
        ];
        
        $table_details  = [
            "daily_attendance, (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        $where  = 'is_delete = 0 AND attendance_type = 1 AND day_status = 2 AND entry_date = "'.$entry_date.'" and staff_id NOT IN (select staff_id from leave_details_sub where from_date = "'.$entry_date.'" and hr_approved = 1)';

        $order_by       = " employee_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,"",$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if($value['check_in_time']){
                    $check_in_time_val  = date_create($value['check_in_time']);
                    $check_in_time      = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time      = "";
                }
                if($value['check_out_time']){
                    $check_out_time_val  = date_create($value['check_out_time']);
                    $check_out_time      = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time      = "";
                } 
                $time1 = new DateTime($value['check_in_time']);
                $time2 = new DateTime($value['check_out_time']);
                if(($time2 == '')&&($entry_date != $today)){
                    $time2 = new DateTime("19:00:00");
                }
                $timediff = $time1->diff($time2);
                if($timediff){
                    $total_work_time   = $timediff->format('%H:%i');
                }else{
                    $total_work_time   = "-";
                }
                $work_time = strtotime($total_work_time);
                $working_time = strtotime("09:00");
                if($work_time < $working_time){
                    $value['total_work_time'] = "<span><strong>".$total_work_time."</strong></span>";
                }else{
                    $value['total_work_time'] = $total_work_time;
                }

                $value['day_status']       = $day_status[$value['day_status']]['text'];
                $value['check_in_time']    = btn_map_att($value['latitude'].','.$value['longitude'],$check_in_time);
                $value['check_out_time']   = btn_map_att($value['check_out_latitude'].','.$value['check_out_longitude'],$check_out_time);
                if($_SESSION['sess_user_type'] == '5ff71f5fb5ca556748'){
                    $btn_update                = btn_update('daily_attendances',$value['unique_id'],'','',$entry_date,"day_attendance_report");
                        $btn_create                = btn_create('daily_attendances','','',$entry_date,"day_attendance_report");
                }else{
                    $btn_create                = '';
                    $btn_update                = '';
                }

                $value['unique_id']        = $btn_update.$btn_create;

               

                $data[]             = array_values($value);
            }
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
               
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'absent_staff_report_datatable':
    // DataTable Variables
    $search     = $_POST['search']['value'] ?? '';
    $length     = $_POST['length'] ?? 10;
    $start      = $_POST['start'] ?? 0;
    $draw       = $_POST['draw'] ?? 1;
    $limit      = ($length == '-1') ? "" : $length;

    $data       = [];
    $entry_date = $_POST['entry_date'] ?? '';

    // Get selected project(s) from POST
    $project = $_POST['project'] ?? '';
    $project_condition = "";
    if (!empty($project)) {
        if (is_array($project)) {
            $project_list = array_map('trim', $project);
            $project_condition = " AND work_location IN ('" . implode("','", $project_list) . "')";
        } else {
            $project_condition = " AND work_location = '" . trim($project) . "'";
        }
    }

    // Columns to select
    $columns = [
        "@a:=@a+1 AS s_no",
        "employee_id",
        "staff_name",
        "absence_reason",
        "condition_source",
        "work_location"
    ];

    $table_details = [
        "erp_absence_view , (SELECT @a:=" . intval($start) . ") AS a ",
        $columns
    ];

    // Build WHERE clause
    $where = "1=1"; // always true, then append filters
    if (!empty($entry_date)) {
        $where .= " AND (punch_date = '" . $entry_date . "' OR punch_date IS NULL)";
    }
    $where .= $project_condition;

    error_log("Absent Staff WHERE: " . $where, 3, "absent_debug.log");

    $order_by     = " employee_id ";
    $sql_function = "SQL_CALC_FOUND_ROWS";

    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records = total_records();

    if ($result->status) {
        $res_array = $result->data;

        foreach ($res_array as $key => $value) {
            $data[] = array_values($value);
        }

        $json_array = [
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data,
            "testing"         => $result->sql
        ];
    } else {
        error_log("PDO Error: " . print_r($result, true), 3, "absent_debug.log");
        $json_array = [
            "draw" => intval($draw),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ];
    }

    header('Content-Type: application/json');
    echo json_encode($json_array);
    exit;

    break;

    default:
        
        break;
}

?>