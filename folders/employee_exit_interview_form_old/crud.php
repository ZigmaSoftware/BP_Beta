<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "employee_exit_interview_form";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$offer_letter   = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        
        $reason   = $_POST["reason"];
        $likeMost    = $_POST["likeMost"];
        $improvement    = $_POST["improvement"];
        $comeBack    = $_POST["comeBack"];
        $comments    = $_POST["comments"];
        $remarks    = $_POST["remarks"];
        $employee_name    = $_POST["staff_name"];
        $designation    = $_POST["designation_name"];
        $is_active          = $_POST["is_active"];
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "reason"           => $reason,
            "likeMost"          => $likeMost,
            "improvement"          => $improvement,
            "comeBack"          => $comeBack,
            "comments"          => $comments,
            "remarks"          => $remarks,
            "designation"          => $designation,
            "is_active"           => $is_active,
            "unique_id"           => unique_id($prefix)
        ];

        if($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table,$columns,$update_where);

        // Update Ends
        } else {
        // Insert Begins
           
            $action_obj     = $pdo->insert($table,$columns);
        // Insert Ends
        }

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

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;

    case 'datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }
      
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "employee_name",
            "designation",
            
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
       
        $where = " is_delete = '0' ";
        
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        // $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['employee_name'] = disdate($value['staff_name']);
                $value['designation']   = disdate($value['designation']);
                // $value['department']   = disname($value['department']);
                // $value['location']        = disname($value['location']);
                
                $btn_view             = btn_print($folder_name,$value['unique_id'],'employee_exit_interview_form',"","","");
                $btn_update           = btn_update($folder_name,$value['unique_id']);
                $btn_delete           = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']   = $btn_view.$btn_update.$btn_delete;
                $data[]               = array_values($value);
            }
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
    
    
        case 'delete':
        
            $unique_id      = $_POST['unique_id'];
    
            $columns        = [
                "is_delete"   => 1
            ];
    
            $update_where   = [
                "unique_id"     => $unique_id
            ];
    
            $action_obj     = $pdo->update($table,$columns,$update_where);
    
            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;
                $msg        = "success_delete";
    
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
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
    
    
    default:
        
        break;
}

?>