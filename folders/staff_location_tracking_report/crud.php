<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];
// Database Country Table Name
$table             = "zigfly_recognized";
// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';
ini_set('max_execution_time', 0);
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

    if ($length == '-1') {
        $limit = "";
    }

    // Query Variables
    $json_array = "";
    $columns    = [
        "@a:=@a+1 s_no",
        "emp_id",
        "name as staff_name",
        "recognition_date",
        "recognition_time",
    ];
    $table_details = [
        $table . " , (SELECT @a:= " . $start . ") AS a ",
        $columns
    ];

    if ($_POST['executive_name'] != '') {
        $executive_name = "emp_id = '" . $_POST['executive_name'] . "' AND ";
    } else {
        $executive_name = "";
    }

    $where        = $executive_name . "recognition_date = '" . $_POST['year_month'] . "' ";
    $order_by     = "";
    $sql_function = "SQL_CALC_FOUND_ROWS";
    $result       = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    // print_r($result);
    $total_records = total_records();
    $s_no          = 1 + $start;

    if ($result->status) {
        $res_array = $result->data;
        foreach ($res_array as $key => $value) {
            $value['s_no']           = $s_no++;
            $value['emp_id']         = strtoupper($value['emp_id']) ?? "-";
            $value['staff_name']     = $value['staff_name'] ?? "-";
            $value['recognition_time'] = $value['recognition_time'] ?? "-";
            
            $data[] = array_values($value);
        }
        // $employee_id = strtoupper($value['employee_id']);

        $json_array = [
            "draw"              => intval($draw),
            "recordsTotal"      => intval($total_records),
            "recordsFiltered"   => intval($total_records),
            "data"              => $data,
            "testing"           => $result->sql
        ];
    } else {
        $json_array = [
            "draw"              => intval($draw),
            "recordsTotal"      => 0,
            "recordsFiltered"   => 0,
            "data"              => [],
            "error"             => $result->message
        ];
    }

    echo json_encode($json_array);
    break;

    
        default:
        
        break;
}
