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
$prefix             = "";

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