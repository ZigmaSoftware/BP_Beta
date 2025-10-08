<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "view_staff_attendance_report";

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

switch ($action) {
   
    case 'datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $staff_id  = $_POST['executive_name'];

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "'' as entry_date",
            "staff_id",
            "'' as check_in_time",
            "'' as break_in_time",
            "'' as break_out_time",
            "'' as check_out_time",
            "'' as total_hrs",
            "'' as day_status",
            "'' as latitude",
            "'' as longitude",
            "'' as check_out_latitude",
            "'' as check_out_longitude",
            "'' as break_in_latitude",
            "'' as break_in_longitude",
            "'' as break_out_latitude",
            "'' as break_out_longitude",
            "'' as working_days",
            "'' as no_of_leave",
            "'' as no_of_late",
            "'' as no_of_permission",
            "'' as no_of_absent",
            "'' as total_worked_days"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        
        if($_POST['executive_name']!=''){$executive_name = "staff_id = '".$_POST['executive_name']."' AND " ;}else{$executive_name = "";}

        $where  = $executive_name.'entry_date like "%'.$_POST['year_month'].'%" ';

        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;
        
        if ($result->status) {
            $month_explode  = explode('-',$_POST['year_month'] );

                $year  = $month_explode[0];
                $month = $month_explode[1];

                $current_month   = date('Y-m');
                if($_POST['year_month'] == $current_month){
                    $total_days     = date('d');
                    $day_count      = $total_days - 1;
                }else{
                    $total_days     = cal_days_in_month(CAL_GREGORIAN,$month,$year);
                    $day_count      = $total_days;
                }
                
                $total_count_days     = cal_days_in_month(CAL_GREGORIAN,$month,$year);


            $res_array      = $result->data;
           
            for($d = 1; $d <= $total_days; $d++){

                if($d < 10){
                    $date_month = "0".$d;
                    $entry_date = $_POST['year_month']."-".$date_month;
                }else{
                    $date_month = $d;
                    $entry_date = $_POST['year_month']."-".$date_month;
                }
               
                $res_array[0]['s_no']        = $s_no++;
                $res_array[0]['entry_date']  = disdate($_POST['year_month']."-".$date_month);

                $date                        = $_POST['year_month']."-".$date_month;
                $staff_id                    = $_POST['executive_name'];
                $staff                       = staff_name($_POST['executive_name']);
                $staff_name                  = disname(($staff[0]['staff_name']));
                $res_array[0]['staff_id']    = $staff_name;
                
                $latitude               = get_latitude($staff_id,$entry_date); 
                $longitude              = get_longitude($staff_id,$entry_date);
                $check_out_latitude     = get_check_out_latitude($staff_id,$entry_date);
                $check_out_longitude    = get_check_out_longitude($staff_id,$entry_date);
                $break_in_latitude      = get_break_in_latitude($staff_id,$entry_date);
                $break_in_longitude     = get_break_in_longitude($staff_id,$entry_date);
                $break_out_latitude     = get_break_out_latitude($staff_id,$entry_date);
                $break_out_longitude    = get_break_out_longitude($staff_id,$entry_date);

                $check_holiday          = get_holiday_date($entry_date);
                $check_sunday           = get_sunday_date($entry_date,$date_month);

                $day_status             = get_day_status($staff_id,$entry_date);
                $leave                  = get_leave_status($staff_id,$entry_date);
                $emer_leave             = get_emer_leave_status($staff_id,$entry_date);

                switch($day_status){
                    case 1 :
                        $res_array[0]['day_status']  = "<span class='text-green font-weight-bold'>Present</span>";
                        break;
                    case 2 :
                        $res_array[0]['day_status']  = "<span class='text-warning font-weight-bold'>Late</span>";
                        break;
                    case 3 :
                        $res_array[0]['day_status']  = "<span class='text-warning font-weight-bold'>Permission</span>";
                        break;
                    case 4:
                        $res_array[0]['day_status']  = "<span class='text-warning font-weight-bold'>Half Day</span>";
                        break;
                    default :
                        $res_array[0]['day_status']  = "<span class='text-danger font-weight-bold'>Absent</span>";
                        break;
                }

                $check_in                    = get_check_in_time($staff_id,$entry_date);

                if($check_in){
                    $check_in_time_val       = date_create($check_in);
                    $check_in_time           = date_format($check_in_time_val,"H:i a");
                }else{
                    $check_in_time           = "-";
                    $res_array[0]['day_status']  = $check_sunday;
                    
                    
                }
                $res_array[0]['check_in_time']    = btn_map_att($latitude.','.$longitude,$check_in_time);
               
                $break_in                    = get_break_in_time($staff_id,$entry_date);
                if($break_in){
                    $break_in_time_val       = date_create($break_in);
                    $break_in_time           = date_format($break_in_time_val,"H:i a");
                }else{
                    $break_in_time           = "-";
                }
                $res_array[0]['break_in_time'] = btn_map_att($latitude.','.$longitude,$break_in_time);
               
                $break_out                    = get_break_out_time($staff_id,$entry_date);
                if($break_out){
                    $break_out_time_val       = date_create($break_out);
                    $break_out_time           = date_format($break_out_time_val,"H:i a");
                }else{
                    $break_out_time           = "-";
                }
                $res_array[0]['break_out_time']    = btn_map_att($latitude.','.$longitude,$break_out_time);
               
                $check_out                    = get_check_out_time($staff_id,$entry_date);
                if($check_out){
                    $check_out_time_val       = date_create($check_out);
                    $check_out_time           = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time           = "-";
                }
                $res_array[0]['check_out_time']    = btn_map_att($latitude.','.$longitude,$check_out_time);
                
                $time1 = new DateTime($check_in);
                $time2 = new DateTime($check_out);
                $timediff = $time1->diff($time2);
                if($timediff){
                    $res_array[0]['total_hrs']   = $timediff->format('%H:%i');
                }else{
                    $res_array[0]['total_hrs']   = "-";
                }

                if(($check_out_time == '-')||($check_out_time == '')){
                    if($date != $today){
                       $res_array[0]['day_status'] = "<span class='text-danger font-weight-bold'>Absent</span>";
                    }
                }

                if($check_holiday){
                    $res_array[0]['day_status'] = "<span class='font-weight-bold' style='color :blue'>Holiday</span>";
                }

                if($leave){
                    $res_array[0]['day_status'] = "<span class='font-weight-bold' style='color :#099be4'>Leave</span>";
                }

                if($emer_leave){
                    $res_array[0]['day_status'] = "<span class='font-weight-bold' style='color :#e46409'>Emergency Leave</span>";
                }

                if($check_in_time == '-'){
                    if($check_sunday){
                        $res_array[0]['day_status'] = $check_sunday;
                    }
                }
                
                

                $data[]         = array_values($res_array[0]);
            }

            $month_explode  = explode('-',$_POST['year_month'] );

                $year  = $month_explode[0];
                $month = $month_explode[1];

                $current_month_days   = cal_days_in_month(CAL_GREGORIAN,$month,$year);

                $total_sundays        = total_sundays($month,$year,$date_month);
                $total_holidays       = total_holidays($month,$year);

                $working_days         = $day_count - $total_sundays - $total_holidays;

                $full_day_leave_cnt       = get_full_day_leave($month,$year,$staff_id);

                if($full_day_leave_cnt){
                    $full_day_leave = $full_day_leave_cnt;
                }else{
                    $full_day_leave = 0;
                }

                $emergency_leave_cnt       = get_emergency_leave($month,$year,$staff_id);

                if($emergency_leave_cnt){
                    $emergency_leave = $emergency_leave_cnt;
                }else{
                    $emergency_leave = 0;
                }

                $full_day_cl_leave_cnt       = get_full_day_cl_leave($month,$year,$staff_id);

                if($full_day_cl_leave_cnt){
                    $full_day_cl_leave = $full_day_cl_leave_cnt;
                }else{
                    $full_day_cl_leave = 0;
                }

                $full_day_comp_off_leave_cnt  = get_full_day_comp_off_leave($month,$year,$staff_id);

                if($full_day_comp_off_leave_cnt){
                    $full_day_comp_off_leave  = $full_day_comp_off_leave_cnt;
                }else{
                    $full_day_comp_off_leave  = 0;
                }

                $half_day_leave_cnt       = get_half_day_leave($month,$year,$staff_id);

                if($half_day_leave_cnt){
                    $half_day_leave = $half_day_leave_cnt;
                }else{
                    $half_day_leave = 0;
                }

                $cl_day_leave_cnt      = get_cl_day_leave($month,$year,$staff_id);

                if($cl_day_leave_cnt){
                    $cl_day_leave = $cl_day_leave_cnt;
                }else{
                    $cl_day_leave = 0;
                }

                $comp_off_day_leave_cnt      = get_comp_off_day_leave($month,$year,$staff_id);

                if($comp_off_day_leave_cnt){
                    $comp_off_day_leave = $comp_off_day_leave_cnt;
                }else{
                    $comp_off_day_leave = 0;
                }

                $half_day_leave_cnt       = get_half_day_leave($month,$year,$staff_id);

                if($half_day_leave_cnt){
                    $half_day_leave = $half_day_leave_cnt;
                }else{
                    $half_day_leave = 0;
                }

                $no_of_late_cnt           = get_late_count($month,$year,$staff_id);

                if($no_of_late_cnt){
                    $no_of_late = $no_of_late_cnt;
                }else{
                    $no_of_late = 0;
                }

                $no_of_permission_cnt     = get_permission_count($month,$year,$staff_id);
                if($no_of_permission_cnt){
                    $no_of_permission = $no_of_permission_cnt;
                }else{
                    $no_of_permission = 0;
                }

                // calculation for total working days
                if($no_of_late_cnt){
                    if($no_of_late_cnt > 3){
                        $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
                    } else{
                        $no_of_late_tot_cnt = 0;
                    }
                }else {
                    $no_of_late_tot_cnt = 0;
                }
                // calculation for per total working days
                if($no_of_permission_cnt){
                    if($no_of_late_tot_cnt > 2){
                        $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2)/2);
                    } else{
                        $no_of_permission_tot_cnt = 0;
                    }
                }else {
                    $no_of_permission_tot_cnt = 0;   
                }

                $absent_count_cnt         = get_absent_count($month,$year,$staff_id,$day_count);

                if($absent_count_cnt){
                    $absent_count = $absent_count_cnt;
                }else{
                    $absent_count = 0;
                }

                // if($half_day_leave_cnt){
                //     $half_day_leave = $half_day_leave_cnt;
                // }else{
                //     $half_day_leave = 0;
                // }


                $no_of_leave         = $cl_day_leave + $full_day_cl_leave;
                $no_of_emer_leave    = $emergency_leave;
                $no_of_comp_off      = $comp_off_day_leave + $full_day_comp_off_leave;

                $ab_cnt              = (($day_count - $absent_count) - $total_sundays - $no_of_leave  - $no_of_comp_off) -1; //-1 for current_day

                $no_of_absent        = ($full_day_leave + $half_day_leave + $ab_cnt); //-1 for current_day

                $total_worked_days   = ($day_count - $no_of_leave - $no_of_absent - $no_of_permission_tot_cnt - $total_sundays - $total_holidays - $no_of_emer_leave) ;


            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                "current_month"     => $total_count_days,
                "working_days"      => $working_days,
                "no_of_holiday"     => $total_holidays,
                "no_of_leave"       => $no_of_leave,
                "no_of_late"        => $no_of_late,
                "no_of_permission"  => $no_of_permission,
                "no_of_absent"      => $no_of_absent,
                "no_of_comp_off"    => $no_of_comp_off,
                "total_worked_days" => $total_worked_days,
                "no_of_sunday"      => $total_sundays,
                "no_of_emer_leave"  => $no_of_emer_leave
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
    
        default:
        
        break;
}

?>