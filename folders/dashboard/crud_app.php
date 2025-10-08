<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];
error_reporting(0);
// Database Country Table Name
$table             = "user_screen_actions";
$table_attendances = "daily_attendance";
// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';


// Variables Declaration
$action             = $_REQUEST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "sql"       => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$action_name        = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    
    
    case 'device_id_control' :


        $table_name    = "device_id_control";
        $where         = [];
        $table_columns = [
            "id",
            "mobile_no"
        ];
    
        $table_details = [
            $table_name,
            $table_columns
        ];
    
        $where     = [];
            
        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        $ofc_in = $office_in['mobile_no'];

        $json_array = [
                
                "data"              => $ofc_in,
                
            ];
        
        echo json_encode($json_array);
        break;    
        
case 'working_days' :
     
            $staff_id  = $_REQUEST['staff_id'];
    
            $month = date('m');
            $year  = date('Y');
            $date  = date('d');
            // Query Variables
            $json_array     = "";
            $current_month_days   = cal_days_in_month(CAL_GREGORIAN,$month,$year);
            $current_days         = $date;
            $total_sundays        = total_sundays_days($current_days,$month,$year);
            $holiday_leave_cnt    = get_holiday_leave($month,$year);
        
            if($holiday_leave_cnt){
                $holiday_leave = $holiday_leave_cnt;
            }else{
                $holiday_leave = 0;
            }
    
            $working_days         = $current_days - $total_sundays - $holiday_leave_cnt;
            $table_name    = "overall_staff_count_view";
                $where         = [];
                $table_columns = [
                    "unique_id",
                    "present_count",
                    "full_day",
                    "half_present_count",
                    "half_day_leave",
                    "absent_count",
                    "late_count",
                    "permission_count",
                ];
            
                $table_details = [
                    $table_name,
                    $table_columns
                ];
            
                $where                  = " unique_id = '".$staff_id."'";
                $overall_staff          = $pdo->select($table_details, $where);
// echo $overall_staff->sql;
                $overall_staff_cnt      = $overall_staff->data;
                $full_day_leave         = $overall_staff_cnt[0]['full_day'];
                $half_day_leave         = floatval($overall_staff_cnt[0]['half_day_leave'])/2;
                $absent_count           = $overall_staff_cnt[0]['absent_count'];
                $half_day_present       = floatval($overall_staff_cnt[0]['half_present_count'])/2;
                $late_count             = $overall_staff_cnt[0]['late_count'];
                $permission_count       = $overall_staff_cnt[0]['permission_count'];
                $present_count          = $overall_staff_cnt[0]['present_count'];

                if($full_day_leave)     { $full_day_leave   = $full_day_leave;      }   else    { $full_day_leave   = '0';      }
                if($half_day_leave)     { $half_day_leave   = $half_day_leave;      }   else    { $half_day_leave   = '0';      }
                if($absent_count)       { $absent_count     = $absent_count;        }   else    { $absent_count     = '0';      }
                if($half_day_present)   { $half_day_present = $half_day_present;    }   else    { $half_day_present = '0';      }
                if($late_count)         { $late_count       = $late_count;          }   else    { $late_count       = '0';      }
                if($permission_count)   { $permission_count = $permission_count;    }   else    { $permission_count = '0';      }
                $no_of_leave          = $full_day_leave + $half_day_leave;
                $no_of_absent         = $working_days   - $absent_count - $no_of_leave;
                
                $total_worked_days    = $absent_count  - $half_day_present;
                $check_out_count      = $present_count - $absent_count;
                $json_array = [
                        "data"                    => $working_days,
                        "worked_days"             => $total_worked_days,
                        "no_of_leave"             => $no_of_leave,
                        "absent_count"            => $no_of_absent,
                        "permission_count"        => $permission_count,
                        "late_count"              => $late_count,
                        "check_out_count"         => $check_out_count,
                    ];
            
            echo json_encode($json_array);
            break;

case 'attendance_details':
            $staff_id  = $_REQUEST['staff_id'];
    
            $month = date('m');
            $year  = date('Y');
            $date  = date('d');
            // Query Variables
            $json_array     = "";
            
            $no_of_late_cnt         = get_late_count($month,$year,$staff_id);
    
            if($no_of_late_cnt){
                $no_of_late = $no_of_late_cnt;
            }else{
                $no_of_late = 0;
            }
    
            $no_of_permission_cnt    = get_permission_count($month,$year,$staff_id);
            if($no_of_permission_cnt){
                $no_of_permission = $no_of_permission_cnt;
            }else{
                $no_of_permission = 0;
            }
            
            $no_of_check_out_cnt    = get_check_out_count($month,$year,$staff_id);
            if($no_of_check_out_cnt){
                $no_of_check_out = $no_of_check_out_cnt;
            }else{
                $no_of_check_out = 0;
            }
            
            $json_array = [
                    
                "no_of_permission"              => $no_of_permission,
                "no_of_late"                    => $no_of_late,
                "no_of_check_out"               => $no_of_check_out,
                
                
            ]; 
            
            echo json_encode($json_array);
            break;
    
case 'worked_days' :
         
            $staff_id  = $_REQUEST['staff_id'];
    
            $month = date('m');
            $year  = date('Y');
            $date  = date('d');
            // Query Variables
            $json_array     = "";
    
            $current_month_days   = cal_days_in_month(CAL_GREGORIAN,$month,$year);
             $current_days        = $date - 1;
            $total_sundays        = total_sundays($month,$year);
            $holiday_leave_cnt    = get_holiday_leave($month,$year);
        
            if($holiday_leave_cnt){
                $holiday_leave = $holiday_leave_cnt;
            }else{
                $holiday_leave = 0;
            }
            $working_days         = $current_days - $total_sundays -  $holiday_leave;
   
            $full_day_leave_cnt    = get_full_day_leave($month,$year,$staff_id);
    
            if($full_day_leave_cnt){
                $full_day_leave = $full_day_leave_cnt;
            }else{
                $full_day_leave = 0;
            }

            
            $count_half_day       = get_half_present_count($month,$year,$staff_id);
            if($count_half_day){
                $half_day_present = $count_half_day;
            }else{
                $half_day_present = 0;
            }
    
            $half_day_leave_cnt     = get_half_day_leave($month,$year,$staff_id);
    
            if($half_day_leave_cnt){
                $half_day_leave = $half_day_leave_cnt;
            }else{
                $half_day_leave = 0;
            }
    
            $absent_count_cnt         = get_absent_count($month,$year,$staff_id);
            if($absent_count_cnt != 0){
                $absent_count = $absent_count_cnt;
            }else{
                $absent_count = 0;
            }
            if($half_day_leave_cnt){
                $half_day_leave = $half_day_leave_cnt;
            }else{
                $half_day_leave = 0;
            }
    
    
            $no_of_leave          = $full_day_leave + $half_day_leave;
    
            $no_of_absent         = $working_days - $absent_count - $no_of_leave;
    
            $total_worked_days    =   $absent_count - $half_day_present;
          
            $json_array = [
                "data"              => $total_worked_days,
                "data1"              => $no_of_leave,
                "data2"              => $no_of_absent,
            ]; 
            
            echo json_encode($json_array);
            break;        
    
case 'leave_days' :
         
            $staff_id  = $_REQUEST['staff_id'];
            
            $date  = date('d');
            $month = date('m');
            $year  = date('Y');
            // Query Variables
            $json_array     = "";
            
            $current_days         = $date - 1;

            $total_sundays        = total_sundays_days($current_days,$month,$year);

                        
    
            $full_day_leave_cnt    = get_full_day_leave($month,$year,$staff_id);
    
            if($full_day_leave_cnt){
                $full_day_leave = $full_day_leave_cnt;
            }else{
                $full_day_leave = 0;
            }

            
    
            $half_day_leave_cnt     = get_half_day_leave($month,$year,$staff_id);
    
            if($half_day_leave_cnt){
                $half_day_leave = $half_day_leave_cnt;
            }else{
                $half_day_leave = 0;
            }

            $holiday_leave_cnt  = get_holiday_leave($month,$year);
    
            if($holiday_leave_cnt){
                $holiday_leave = $holiday_leave_cnt;
            }else{
                $holiday_leave = 0;
            }
    
            $no_of_leave          = $full_day_leave + $half_day_leave;
            
        //     $no_of_late_cnt         = get_late_count($month,$year,$staff_id);

        // if($no_of_late_cnt){
        //     $no_of_late = $no_of_late_cnt;
        // }else{
            $no_of_late = 0;
        // }

        // $no_of_permission_cnt    = get_permission_count($month,$year,$staff_id);
        // if($no_of_permission_cnt){
        //     $no_of_permission = $no_of_permission_cnt;
        // }else{
            $no_of_permission = 0;
        // }

        // // calculation for total working days
        // if($no_of_late_cnt){
        //     if($no_of_late_cnt > 3){
        //         $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
        //     } else{
        //         $no_of_late_tot_cnt = 0;
        //     }
        // }else {
            $no_of_late_tot_cnt = 0;
        // }
        // // calculation for per total working days
        // if($no_of_permission_cnt){
        //     if($no_of_late_tot_cnt > 2){
        //         $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2)/2);
        //     } else{
        //         $no_of_permission_tot_cnt = 0;
        //     }
        // }else {
            $no_of_permission_tot_cnt = 0;   
        // }

        

        $absent_count_cnt         = get_absent_count_leave($month,$year,$staff_id);

        if($absent_count_cnt != 0){
            $absent_count = $absent_count_cnt;
        }else{
            $absent_count = 0;
        }

        if($half_day_leave_cnt){
            $half_day_leave = $half_day_leave_cnt;
        }else{
            $half_day_leave = 0;
        }
        
        $working_days         = $current_days - $total_sundays  - $holiday_leave;


        $no_of_leave          = $full_day_leave + $half_day_leave +  $no_of_permission_tot_cnt;

        $no_of_absent         =    $no_of_leave;

        $total_worked_days    = $absent_count;
        $absent_days          = $working_days -  $total_worked_days;
        $leave_days           = $no_of_leave;
            
            $json_array = [
                    
                "data"              =>  $leave_days,
                
            ];
        
            echo json_encode($json_array);
            break;

        case 'absent_days' :
         
            $staff_id  = $_REQUEST['staff_id'];
            
            $date  = date('d');
            $month = date('m');
            $year  = date('Y');
            // Query Variables
            $json_array     = "";
            
            $current_days         = $date - 1;

            $total_sundays        = total_sundays_days($current_days,$month,$year);

                        
    
            $full_day_leave_cnt    = get_full_day_leave($month,$year,$staff_id);
    
            if($full_day_leave_cnt){
                $full_day_leave = $full_day_leave_cnt;
            }else{
                $full_day_leave = 0;
            }

    
            $half_day_leave_cnt     = get_half_day_leave($month,$year,$staff_id);
    
            if($half_day_leave_cnt){
                $half_day_leave = $half_day_leave_cnt;
            }else{
                $half_day_leave = 0;
            }

            $holiday_leave_cnt  = get_holiday_leave($month,$year);
    
            if($holiday_leave_cnt){
                $holiday_leave = $holiday_leave_cnt;
            }else{
                $holiday_leave = 0;
            }
    
            $no_of_leave          = $full_day_leave + $half_day_leave;
            
        //     $no_of_late_cnt         = get_late_count($month,$year,$staff_id);

        // if($no_of_late_cnt){
        //     $no_of_late = $no_of_late_cnt;
        // }else{
            $no_of_late = 0;
        // }

        // $no_of_permission_cnt    = get_permission_count($month,$year,$staff_id);
        // if($no_of_permission_cnt){
        //     $no_of_permission = $no_of_permission_cnt;
        // }else{
            $no_of_permission = 0;
        // }

        // calculation for total working days
        // if($no_of_late_cnt){
        //     if($no_of_late_cnt > 3){
        //         $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
        //     } else{
        //         $no_of_late_tot_cnt = 0;
        //     }
        // }else {
            $no_of_late_tot_cnt = 0;
        // }
        // calculation for per total working days
        // if($no_of_permission_cnt){
        //     if($no_of_late_tot_cnt > 2){
        //         $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2)/2);
        //     } else{
        //         $no_of_permission_tot_cnt = 0;
        //     }
        // }else {
            $no_of_permission_tot_cnt = 0;   
        // }

        

        $absent_count_cnt         = get_absent_count_leave($month,$year,$staff_id);

        if($absent_count_cnt != 0){
            $absent_count = $absent_count_cnt;
        }else{
            $absent_count = 0;
        }

        if($half_day_leave_cnt){
            $half_day_leave = $half_day_leave_cnt;
        }else{
            $half_day_leave = 0;
        }

        $count_half_day       = get_half_present_count($month,$year,$staff_id);
                if($count_half_day){
                    $half_day_present = $count_half_day;
                }else{
                    $half_day_present = 0;
                }
        
        $working_days         = $current_days - $total_sundays  - $holiday_leave;

        $no_of_leave          = $full_day_leave + $half_day_leave;
        
        $total_worked_days    =   $absent_count;
        $no_of_absent         =   (($working_days  - $total_worked_days)-$no_of_leave) + $half_day_present;
        $json_array = [
                "data"              => $no_of_absent,
            ];
            
            echo json_encode($json_array);
            break;    
    
    
    case 'office_in' :
     
            $staff_id  = $_REQUEST['staff_id'];
    
            $table_name    = "daily_attendance";
            $where         = [];
            $table_columns = [
                "unique_id",
                "entry_time"
            ];
        
            $table_details = [
                $table_name,
                $table_columns
            ];
        
            $where     = [
                "is_active" => 1,
                "is_delete" => 0,
                "attendance_type" => 1,
                "staff_id" => $staff_id,
                "entry_date" => date('Y-m-d')
            ];
                
            $staff_name_list = $pdo->select($table_details, $where);
            $office_in = $staff_name_list->data[0];
            $ofc_in = date('d-m-Y').' ( '.date('h:i A',strtotime($office_in['entry_time'])).' )';
            if($office_in['entry_time'])
            {
                $ofc_in = date('d-m-Y').' ( '.date('h:i A',strtotime($office_in['entry_time'])).' )';
            }
            else
            {
                $ofc_in = '-';
            }
            $json_array = [
                    
                    "data"              => $ofc_in,
                    
                ];
            
            echo json_encode($json_array);
            break;

            case 'office_out' :
     
                $staff_id  = $_REQUEST['staff_id'];
        
                $table_name    = "daily_attendance";
                $where         = [];
                $table_columns = [
                    "unique_id",
                    "entry_time"
                ];
            
                $table_details = [
                    $table_name,
                    $table_columns
                ];
            
                $where     = [
                    "is_active" => 1,
                    "is_delete" => 0,
                    "attendance_type" => 2,
                    "staff_id" => $staff_id,
                    "entry_date" => date('Y-m-d')
                ];
                    
                $staff_name_list = $pdo->select($table_details, $where);
                $office_in = $staff_name_list->data[0];
                if($office_in['entry_time'])
                {
                    $ofc_in = date('d-m-Y').' ( '.date('h:i A',strtotime($office_in['entry_time'])).' )';
                }
                else
                {
                    $ofc_in = '-';
                }
                $json_array = [
                        
                        "data"              => $ofc_in,
                        
                    ];
                
                echo json_encode($json_array);
                break;

                case 'break_in' :
     
                    $staff_id  = $_REQUEST['staff_id'];
            
                    $table_name    = "daily_attendance";
                    $where         = [];
                    $table_columns = [
                        "unique_id",
                        "entry_time"
                    ];
                
                    $table_details = [
                        $table_name,
                        $table_columns
                    ];
                
                    $where     = [
                        "is_active" => 1,
                        "is_delete" => 0,
                        "attendance_type" => 3,
                        "staff_id" => $staff_id,
                        "entry_date" => date('Y-m-d')
                    ];
                        
                    $staff_name_list = $pdo->select($table_details, $where);
                    $office_in = $staff_name_list->data[0];
                    $ofc_in = date('d-m-Y').' ( '.date('h:i A',strtotime($office_in['entry_time'])).' )';
                    if($office_in['entry_time'])
                    {
                        $ofc_in = date('d-m-Y').' ( '.date('h:i A',strtotime($office_in['entry_time'])).' )';
                    }
                    else
                    {
                        $ofc_in = '-';
                    }
                    $json_array = [
                            
                            "data"              => $ofc_in,
                            
                        ];
                    
                    echo json_encode($json_array);
                    break;
        
                    case 'break_out' :
             
                        $staff_id  = $_REQUEST['staff_id'];
                
                        $table_name    = "daily_attendance";
                        $where         = [];
                        $table_columns = [
                            "unique_id",
                            "entry_time"
                        ];
                    
                        $table_details = [
                            $table_name,
                            $table_columns
                        ];
                    
                        $where     = [
                            "is_active" => 1,
                            "is_delete" => 0,
                            "attendance_type" => 4,
                            "staff_id" => $staff_id,
                            "entry_date" => date('Y-m-d')
                        ];
                            
                        $staff_name_list = $pdo->select($table_details, $where);
                        $office_in = $staff_name_list->data[0];
                        if($office_in['entry_time'])
                        {
                            $ofc_in = date('d-m-Y').' ( '.date('h:i A',strtotime($office_in['entry_time'])).' )';
                        }
                        else
                        {
                            $ofc_in = '-';
                        }
                        $json_array = [
                                
                                "data"              => $ofc_in,
                                
                            ];
                        
                        echo json_encode($json_array);
                        break;

    case 'monthly_report' :

        $search     = $_REQUEST['search']['value'];
        $length     = $_REQUEST['length'];
        $start      = $_REQUEST['start'];
        $draw       = $_REQUEST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $staff_id  = $_REQUEST['staff_id'];

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "'' as entry_date",
            "'' as day_status",
            "'' as check_in_time",
            "'' as check_out_time"        
        ];
        $table_name_report    = "view_staff_attendance_report";
        $table_details  = [
            $table_name_report,
            $columns
        ];

        if($_REQUEST['year_month']=='')
        {   
            $_REQUEST['year_month'] =   date('Y-m');
        }
        
        if($_REQUEST['staff_id']!=''){$executive_name = "staff_id = '".$_REQUEST['staff_id']."' AND " ;}else{$executive_name = "";}

        $where  = $executive_name.'entry_date like "%'.$_REQUEST['year_month'].'%" ';

        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        
        if ($result->status) {
            $month_explode  = explode('-',$_REQUEST['year_month'] );

                $year  = $month_explode[0];
                $month = $month_explode[1];

                $current_month   = date('Y-m');
                if($_REQUEST['year_month'] == $current_month){
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
                    $entry_date = $_REQUEST['year_month']."-".$date_month;
                }else{
                    $date_month = $d;
                    $entry_date = $_REQUEST['year_month']."-".$date_month;
                }
               
                $res_array[0]['entry_date']  = disdate($_REQUEST['year_month']."-".$date_month);

                $date                        = $_REQUEST['year_month']."-".$date_month;
                $staff_id                    = $_REQUEST['staff_id'];
                $staff                       = staff_name($_REQUEST['staff_id']);
                $staff_name                  = disname(($staff[0]['staff_name']));
                $res_array[0]['staff_id']    = $staff_name;
                

                $check_holiday          = get_holiday_date($entry_date);
                $check_sunday           = get_sunday_date($entry_date,$date_month);

                $day_status             = get_day_status($staff_id,$entry_date);
                $leave                  = get_leave_status($staff_id,$entry_date);
                $emer_leave             = get_emer_leave_status($staff_id,$entry_date);

                switch($day_status){
                    case 1 :
                        $res_array[0]['day_status']  = "<span class='text-success font-weight-bold'>Present</span>";
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
                $res_array[0]['check_in_time']    = $check_in_time;
               
                $check_out                    = get_check_out_time($staff_id,$entry_date);
                if($check_out){
                    $check_out_time_val       = date_create($check_out);
                    $check_out_time           = date_format($check_out_time_val,"H:i a");
                }else{
                    $check_out_time           = "-";
                }
                $res_array[0]['check_out_time']    = $check_out_time;
                
                $time1 = new DateTime($check_in);
                $time2 = new DateTime($check_out);
                $timediff = $time1->diff($time2);
               

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
                
                // if($check_out_time!='')
                // {
                //     $check_out_time = date('h:i A',strtotime($value["check_out_time"]));
                // }    
                // else
                // {
                //     $check_out_time = '-';
                // }     
                $ofc_in='<tr>';
                $ofc_in.='<td style="font-size:12px;">'.$res_array[0]["entry_date"].'</td>';
                $ofc_in.='<td style="font-size:12px;">'.$res_array[0]['day_status'].'</td>';
                $ofc_in.='<td style="font-size:12px;">'.$check_in_time.'</td>';
                $ofc_in.='<td style="font-size:12px;text-align:center;">'.$check_out_time.'</td>';
                $ofc_in.='</tr>';

                $data[]         = $ofc_in;
            }

            $month_explode  = explode('-',$_REQUEST['year_month'] );

                $year  = $month_explode[0];
                $month = $month_explode[1];

                $current_month_days   = cal_days_in_month(CAL_GREGORIAN,$month,$year);

            $json_array = [

                "data"              => $data,
                
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
        
        case 'device_check' :
     
            $staff_id  = $_REQUEST['staff_id'];
            $device_id  = $_REQUEST['device_id'];
    
            $table_name    = "user";
            $table_details      = [
                $table_name,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
        
        
            $where     = [
                // "is_active" => 1,
                // "is_delete" => 0,
                "device_id" => $device_id,
                "staff_unique_id" => $staff_id,
            ];
            
            $staff_name_list = $pdo->select($table_details, $where);
            $office_in = $staff_name_list->data[0];
            // echo $staff_name_list->sql;
            
            $json_array = [
                    
                    "data"              => $office_in['count'],
                    
                ];
            
            echo json_encode($json_array);
            break;        

            case 'office_attendance':
                   
                $staff_id            = $_REQUEST["staff_id"];
                $entry_date          = date('Y-m-d');
                $entry_time          = date("H:i:s");
                $latitude            = $_REQUEST["latitude"];
                $longitude           = $_REQUEST["longitude"];
                $attendance_type     = $_REQUEST["attendance_type"];
                // $day_status          = $_REQUEST["day_status"];
                $day_type            = '7';
        

                $table_name    = "daily_attendance";
                $where         = [];
                $table_columns = [
                    "unique_id",
                    "day_status"
                ];
            
                $table_details = [
                    $table_name,
                    $table_columns
                ];
            
                $where     = [
                    "is_active" => 1,
                    "is_delete" => 0,
                    "attendance_type" => 1,
                    "staff_id" => $staff_id,
                    "entry_date" => date('Y-m-d')
                ];
                $entry_time1          = date('H:i');
                $staff_name_list = $pdo->select($table_details, $where);
                $office_in = $staff_name_list->data[0];
                if($office_in['day_status']=='')
                {
                    if($entry_time1>='05:00' && $entry_time1<='09:40'){$day_status =  '1';}
                    if($entry_time1>='09:41' && $entry_time1<='10:30'){$day_status =  '2';}
                    if($entry_time1>='10:31' && $entry_time1<='11:30'){$day_status =  '3';}
                    if($entry_time1>='11:31'){$day_status =  '4';}
                }
                else
                {
                    $day_status =   $office_in['day_status'];
                }

                $update_where       = "";
        
                $columns            = [
                    "staff_id"          => $staff_id,
                    "entry_date"        => $entry_date,
                    "entry_time"        => $entry_time,
                    "latitude"          => $latitude,
                    "longitude"         => $longitude,
                    "attendance_type"   => $attendance_type,
                    "day_status"        => $day_status,
                    "day_type"          => $day_type,
                    "attendance_from"   => "Android App",
                    "unique_id"         => unique_id($prefix)
                ];
        
                // Check already Exist Or not
                $table_details      = [
                    $table_attendances,
                    [
                        "COUNT(unique_id) AS count"
                    ]
                ];
        
                if (($attendance_type != 2) && ($attendance_type != 3) && ($attendance_type != 4)) {
        
                    $select_where       = 'staff_id = "'.$staff_id.'" AND entry_date = "'.$entry_date.'" AND attendance_type = "'.$attendance_type.'" AND is_delete = 0  ';
            
                    $action_obj         = $pdo->select($table_details,$select_where);
        
                    if ($action_obj->status) {
                        $status     = $action_obj->status;
                        $data       = $action_obj->data;
                        $error      = "";
                        $sql        = $action_obj->sql;
        
                    } else {
                        $status     = $action_obj->status;
                        $data       = $action_obj->data;
                        $error      = $action_obj->error;
                        $sql        = $action_obj->sql;
                        $msg        = "error";
                    }
                    
                    if ($data[0]["count"]) {
                        $msg        = "already";
                    } else if ($data[0]["count"] == 0) {
            
                            $action_obj     = $pdo->insert($table_attendances,$columns);
                            
        
                        if ($action_obj->status) {
                            $status     = $action_obj->status;
                            $data       = $action_obj->data;
                            $error      = "";
                            $sql        = $action_obj->sql;
        
                            if ($unique_id) {
                                $msg        = "update";
                            } else {
                                $msg        = "create";
                            }
                        } else {
                            $status     = $action_obj->status;
                            $data       = $action_obj->data;
                            $error      = $action_obj->error;
                            $sql        = $action_obj->sql;
                            $msg        = "error";
                        }
                    }
                } else {
        
                    $action_obj     = $pdo->insert($table_attendances,$columns);
        
                    if ($action_obj->status) {
                            $status     = $action_obj->status;
                            $data       = $action_obj->data;
                            $error      = "";
                            $sql        = $action_obj->sql;
        
                            if ($unique_id) {
                                $msg        = "update";
                            } else {
                                $msg        = "create";
                            }
                        } else {
                            $status     = $action_obj->status;
                            $data       = $action_obj->data;
                            $error      = $action_obj->error;
                            $sql        = $action_obj->sql;
                            $msg        = "error";
                        }
                }
                $json_array   = [
                    "status"    => $status,
                    "data"      => $data,
                    "error"     => $error,
                    "msg"       => $msg,
                    "sql"       => $sql
                ];
        
                echo json_encode($json_array);
                break;    
    
    
        case 'break_in_out_validation' :
     
                $staff_id  = $_REQUEST['staff_id'];
        
                $table_name    = "daily_attendance";
                $where         = [];
                $table_columns = [
                    "unique_id",
                    "attendance_type"
                ];
            
                $table_details = [
                    $table_name,
                    $table_columns
                ];
            
                // $where     = [
                //     "is_active" => 1,
                //     "is_delete" => 0,
                //     "attendance_type" => 1,
                //     "staff_id" => $staff_id,
                //     "entry_date" => date('Y-m-d')
                // ];
                $where = " is_active=1 and is_delete=0 and staff_id='".$staff_id."' and entry_date='".date('Y-m-d')."' order by id DESC limit 1";
                    
                $staff_name_list = $pdo->select($table_details, $where);
                $office_in = $staff_name_list->data[0];
                $ofc_in = $office_in['attendance_type'];
                
                $json_array = [
                        
                        "data"              => $ofc_in,
                        
                    ];
                
                echo json_encode($json_array);
                break;
                
                
        case 'leave_date_details' :

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "leave_details";
        $where         = [];
        $table_columns = [
            "unique_id",
            "from_date",
            "to_date",
            "leave_days",
            "half_day_type",
            "from_time",
            "to_time",
            "permission_hours",
            "day_type"
        ];
    
        $table_details = [
            $table_name,
            $table_columns
        ];
    
        $where = " staff_id='".$staff_id."' and MONTH(from_date) = MONTH(now()) AND YEAR(from_date) = YEAR(now())";
            
        $result = $pdo->select($table_details, $where);
        $data	    = [];
        $res_array      = $result->data;
        foreach ($res_array as $value) {
            if($value['day_type'])
            {   
                if($value['day_type']=='1'){$day_type='Full Day';}
                if($value['day_type']=='2'){$day_type='Half Day';}
                if($value['day_type']=='3'){$day_type='Work From Home';}
                if($value['day_type']=='4'){$day_type='Idle';}
                if($value['day_type']=='5'){$day_type='On Duty';}
                if($value['day_type']=='6'){$day_type='Permission';}
                
                if($value['half_day_type']=='1'){$hlf_day_type='Forenoon';}
                if($value['half_day_type']=='2'){$hlf_day_type='Afternoon';}

                if($value['day_type']=='1' || $value['day_type']=='3' || $value['day_type']=='4')
                {
                    $ofc_in = "<p>".$day_type.' ( '.date("d-m-Y",strtotime($value['from_date'])).' to '.date("d-m-Y",strtotime($value['to_date'])).' - '.$value['leave_days']." Days )</p>";
                }
                if($value['day_type']=='2' || $value['day_type']=='5')
                {
                    $ofc_in = "<p>".$day_type.' ( '.date("d-m-Y",strtotime($value['from_date'])).' - '.$hlf_day_type." )</p>";
                }
                if($value['day_type']=='6')
                {
                    $ofc_in = "<p>".$day_type.' ( '.date("h:i A",strtotime($value['from_time'])).' to '.date("h:i A",strtotime($value['to_time'])).' - '.date("h:i",strtotime($value['permission_hours']))." Hours )</p>";
                }

                //$ofc_in = "<p>".date("d-m-Y",strtotime($value['from_date'])).' - '.$day_type."</p>";
                $data[] = $ofc_in;
            }
            else
            {
                $data[] = '-';
            }
        }
        $json_array = [
                
                "data"              => $data,
                
            ];
        
        echo json_encode($json_array);
        break;


        case 'holiday_details' :

        // $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "attendance_holidays";
        $where         = [];
        $table_columns = [
            "unique_id",
            "holiday_date",
            "remarks"
        ];
    
        $table_details = [
            $table_name,
            $table_columns
        ];
    
        $where = "  MONTH(holiday_date) = MONTH(now()) AND YEAR(holiday_date) = YEAR(now())";
            
        $result = $pdo->select($table_details, $where);
        $data	    = [];
        $res_array      = $result->data;
        foreach ($res_array as $value) {
            if($value['holiday_date'])
            {   
                
                $ofc_in = "<p>".date("d-m-Y",strtotime($value['holiday_date'])).' - '.$value['remarks']."</p>";
                $data[] = $ofc_in;
            }
            else
            {
                $data[] = '-';
            }
        }
        $json_array = [
                
                "data"              => $data,
                
            ];
        
        echo json_encode($json_array);
        break;                
    
}

    
?>    
    