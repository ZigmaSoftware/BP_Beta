<?php
// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];
// Database Country Table Name
$table             = "festival_creation";
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
$entry_date          = "";
$title          = "";
$description          = "";
$unique_id          = "";
$prefix             = "";
$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose
$sess_user_type  = $_SESSION['sess_user_type'];

switch ($action) {
    case 'createupdate':
        $description       = $_POST["description"];
        $datepicker       = $_POST["datepicker"];
        $title           = $_POST["title"];
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";
        $columns            = [

            "datepicker"          => $datepicker,
            "title"               => $title,
            "description"          => $description,
            "unique_id"           => unique_id($prefix)
        ];
        // Update Begins
        if ($unique_id) {
            unset($columns['unique_id']);
            $update_where   = 'unique_id="' . $unique_id . '"';

            // ];

            $action_obj     = $pdo->update($table, $columns, $update_where);
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
        $title=$_POST['title'];
        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "datepicker",
            "description",
            "title",
            "is_active",
            "unique_id"
        ];
        // }
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where = " is_delete = 0 ";


        if($_POST['title']){
            $where .= " AND title ='".$title."' AND is_delete = '0'";
        }
        // if($_POST['title']){
        //     $where .= " AND title ='".$title."' AND is_delete = '0'";
        // }
        // $group_by= 
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];
        
        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // Datatable Searching
        // $search         = datatable_searching($search, $columns);
        if ($_POST['search']['value']) {
            $where .= " AND (title LIKE '".mysql_like($_POST['search']['value'])."') ";
            // $where .= " OR mobile_no LIKE '".mysql_like($_POST['search']['value'])."' ";
            // $where .= " OR person_name LIKE '".mysql_like($_POST['search']['value'])."' ";
            // $where .= " OR (title LIKE '".mysql_like($_POST['search']['value'])."')";
        }
        // if ($search) {
        //     if ($where) {
        //         $where .= " AND ";
        //     }
        //     $where .= $search;
        // }
        $sql_function   = "SQL_CALC_FOUND_ROWS";
        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        
        $total_records  = total_records();
        if ($result->status) {
            $res_array      = $result->data;
            foreach ($res_array as $key => $value) {
                    $value['datepicker'] = disname($value['datepicker']);
                    $timestamp = strtotime($value['datepicker']);
                    $value['datepicker'] = date("d-m-Y", $timestamp);
                    $value['description'] = disdate($value['description']);
                    $value['title'] = disname($value['title']);
                
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    if ($value['is_active'] == 1) {
                        $btn_toggle = btn_toggle_on($folder_name, $value['unique_id']);
                    } else {
                        $btn_toggle = btn_toggle_off($folder_name, $value['unique_id']);
                    }
                
                    if ($value['unique_id'] == "5f97fc3257f2525529") {
                        $btn_update = "";
                        $btn_toggle = "";
                    }
                
                    $value['unique_id'] = $btn_update . $btn_toggle;
                    unset($value['is_active']);
                
                    $data[] = array_values($value);
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

 case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active'];

    $columns = [
        "is_active" => $is_active
    ];
    $update_where = [
        "unique_id" => $unique_id
    ];
    $action_obj = $pdo->update($table, $columns, $update_where);

    if ($action_obj->status) {
        $status = true;
        $msg = $is_active ? "Activated Successfully" : "Deactivated Successfully";
    } else {
        $status = false;
        $msg = "Toggle failed!";
    }

    echo json_encode([
        "status" => $status,
        "msg" => $msg,
        "sql" => $action_obj->sql,
        "error" => $action_obj->error
    ]);
    break;

    default:
        break;
}
