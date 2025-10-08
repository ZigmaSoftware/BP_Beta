<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "staff";

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
$prefix             = "cty";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    

    case 'datatable':
        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
        $where_arr  = [];
        $total      = 0;

		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "employee_id",
            "work_location",
            "date_of_join",
            "staff_name",
            "(SELECT designation FROM designation_creation as designation where designation.unique_id = staff.designation_unique_id) AS designation_name",
            "department",
            "'' as total_days",
            "'' as holidays",
            "'' as company_working_days",
            "'' as work_from_home",
            "'' as special_leave",
            "'' as casual_leave ",
            "'' as late_present",
            "'' as permission",
            "'' as permission_with_leave",
            "'' as absent",
            "'' as days_present",
            "'' as lop",
            "'' as salary_days",
            "unique_id"
        ]
        ;
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        
       

        $where  = 'is_delete = 0';

        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        $s_no           = 1 + $start;

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {


                

                $month_explode  = explode('-',$_POST['year_month'] );

                $year  = $month_explode[0];
                $month = $month_explode[1];

                $stardate    = strtotime(date('"'.$_POST['year_month'].'-01"'));
                $enddate     = strtotime(date('"'.$_POST['year_month'].'-d"'));

                $dateDiff = abs($enddate - $stardate);

                $numberDays = $dateDiff/86400;  // 86400 seconds in one day

                // and you might want to convert to integer
                $numberDays = intval($numberDays + 1);

                $value['total_days']     = cal_days_in_month(CAL_GREGORIAN,$month,$year);
                $value['holidays']       = get_holidays($_POST['year_month']);
                $sundays                 = total_sundays($month,$year);
                $work_from_home          = get_work_from_home($_POST['year_month'],$value['unique_id']);
                $special_leave           = get_special_leave_full_day($_POST['year_month'],$value['unique_id']) + get_special_leave_half_day($_POST['year_month'],$value['unique_id']);
                $casual_leave            = get_casual_leave_full_day($_POST['year_month'],$value['unique_id']) + get_casual_leave_half_day($_POST['year_month'],$value['unique_id']);
                $late_present            = get_late_count($_POST['year_month'],$value['unique_id']);
                $permission              = get_permission_count($_POST['year_month'],$value['unique_id']);
                $absent_count            = get_absent_count($_POST['year_month'],$value['unique_id']);
                $permission_with_leave   = get_permission_leave_count($_POST['year_month'],$value['unique_id']);
                $lop                     = get_lop_count($_POST['year_month'],$value['unique_id']);
                $day_present             = get_present_count($_POST['year_month'],$value['unique_id']);

                if($work_from_home == ''){
                    $value['work_from_home'] = 0;
                }else {
                    $value['work_from_home'] = $work_from_home;
                }

                if($special_leave == ''){
                    $value['special_leave'] = 0;
                }else {
                    $value['special_leave'] = $special_leave;
                }

                if($casual_leave == ''){
                    $value['casual_leave'] = 0;
                }else {
                    $value['casual_leave'] = $casual_leave;
                }

                if($late_present == ''){
                    $value['late_present'] = 0;
                }else {
                    $value['late_present'] = $late_present;
                }

                if($permission == ''){
                    $value['permission'] = 0;
                }else {
                    $value['permission'] = $permission;
                }

                if($permission_with_leave == ''){
                    $value['permission_with_leave'] = 0;
                }else {
                    $value['permission_with_leave'] = $permission_with_leave;
                }

                if($lop == ''){
                    $value['lop'] = 0;
                }else {
                    $value['lop'] = $lop;
                }

                if($day_present == ''){
                    $value['days_present'] = 0;
                }else {
                    $value['days_present'] = $day_present;
                }


                $value['company_working_days']  = $value['total_days'] - $value['holidays'] - $sundays;

                $value['absent']                =  abs($numberDays - $absent_count - $value['special_leave'] - $value['casual_leave']);

                $value['salary_days']           = $numberDays - $value['lop'];
                
                $data[]             = array_values($value);
            }
            
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data"              => $data,
                "testing"			=> $result->sql,
                
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