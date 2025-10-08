<?php
// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];
// Database Country Table Name
$table             = "lets_talk";
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
$entry_time          = "";
$entry_date          = "";
$status          = "";
$description          = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";
$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose
$sess_user_type  = $_SESSION['sess_user_type'];
$staff_name             =$_SESSION["staff_name"];

switch ($action) {
    case 'createupdate':
        $entry_time       = $_POST["entry_time"];
        $entry_date       = $_POST["entry_date"];
        $status           = $_POST["status"];
        // if ($_SESSION['sess_user_type']  == '5f97fc3257f2525529') {
         $employee_name    = $_POST["staff_name"];  
        //   }
        // elseif ($_SESSION['sess_user_type']  != '5f97fc3257f2525529') {      
        //  $employee_name    = $_POST["employee_name"];  
        // }  
       
        $description       = $_POST["description"];
        $is_active          = $_POST["is_active"];
        $unique_id          = $_POST["unique_id"];
        $update_where       = "";
        $columns            = [
            "entry_time"          => $entry_time,
            "entry_date"          => $entry_date,
            "employee_name"       => $employee_name,
            // "employee_name"       => $employee_name_1,
            "status"               => $status,
            "description"          => $description,
            // "is_active"           => $is_active,
            "unique_id"           => unique_id($prefix)
        ];
        // Update Begins
        if ($unique_id) {
            unset($columns['unique_id']);
            $update_where   ='unique_id="'.$unique_id.'"';
               
            // ];
            
            $action_obj     = $pdo->update($table, $columns, $update_where);
            // print_R($action_obj);die();
            // Update Ends
        } else {
            // Insert Begins
            $action_obj     = $pdo->insert($table, $columns);
            // print_r($action_obj );die();
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
        $start         = $_POST['start'];
        $draw         = $_POST['draw'];
        $limit         = $length;
        $data        = [];
        if ($length == '-1') {
            $limit  = "";
        }
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "employee_name",
            // "entry_time",
            "entry_date",
            "description",
            "unique_id"
        ];
        // }
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
        
        

        if($_SESSION['sess_user_type']  == '5f97fc3257f2525529') {        
            $where = " is_delete = '0' ";
        } 
        else if($_SESSION['sess_user_type']  
        != '5f97fc3257f2525529'){
            $where = " is_delete = '0' and employee_name='".$staff_name ."'";
           
        }
     // $group_by= 
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];
        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // Datatable Searching
        $search         = datatable_searching($search, $columns);
        if ($search) {
            if ($where) {
                $where .= " AND ";
            }
            $where .= $search;
        }
        $sql_function   = "SQL_CALC_FOUND_ROWS";
        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();
        if ($result->status) {
            $res_array      = $result->data;
            foreach ($res_array as $key => $value) {
                
                $value['employee_name'] = disname($value['employee_name']);

                
               
                $value['entry_date'] = disdate($value['entry_date']);
                $value['status'] = disname($value['status']);
                $sts = $value['status'];
                switch($sts){
                    case 'Approve' :
                        $value['status']  = "<span class='text-danger font-weight-bold'>approve</span>";
                        break;
                    // case 'Pending' :
                    //     $value['status']  = "<span class='text-warning font-weight-bold'>pending</span>";
                    //     break;
                    case 'Cancel' :
                        $value['status']  = "<span class='text-success font-weight-bold'>Cancel</span>";
                        break;
                    }
                $value['description'] = disname($value['description']);
                // $value['is_active'] = is_active_show($value['is_active']);
                $btn_update         = btn_update($folder_name, $value['unique_id']);
                $btn_delete         = btn_delete($folder_name, $value['unique_id']);
                if ($value['unique_id'] == "5f97fc3257f2525529") {
                    $btn_update         = "";
                    $btn_delete         = "";
                }
                $value['unique_id'] = $btn_update . $btn_delete;
                $data[]             = array_values($value);
            }
            $json_array = [
                "draw"                => intval($draw),
                "recordsTotal"         => intval($total_records),
                "recordsFiltered"     => intval($total_records),
                "data"                 => $data,
                "testing"            => $result->sql
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
        $action_obj     = $pdo->update($table, $columns, $update_where);
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
