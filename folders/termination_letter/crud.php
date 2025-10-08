<?php

// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

// Database Country Table Name
$table             = "termination_letter";

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
$offer_letter       = "";
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


        $unique_id          = $_POST['unique_id'];

        if ($unique_id == '') {
            $unique_ids          = unique_id($prefix);
        } else {
            $unique_ids         = $_POST['unique_id'];
        }
        $entry_dates          = $_POST['entry_date'];

        if ($_POST['entry_date'] == '') {
            $entry_date          = date('Y-m-d');
        } else {
            $entry_date         = $_POST['entry_date'];
        }
        $columns            = [

            "staff_name"                    => $_POST["staff_name"],
            "designation"                   => $_POST["designation"],
            "department"                    => $_POST["department"],
            "work_location"                      => $_POST["work_location"],
            "date_of_termination"           => $_POST["date_of_termination"],
            "entry_date"                    => $entry_date,
            "unique_id"                     =>  $unique_ids

        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            $columns
        ];

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table, $columns, $update_where);

            // Update Ends
        } else {

            // Insert Begins 
            $where_termination                  = "acc_year = '" . $_SESSION["acc_year"] . "'";

            $termination_letter_no                     = bill_no($table, $where_termination, "XWM/HR/TER/", 1, 0, 0, false, "/");

            $columns["termination_letter_no"]   = $termination_letter_no;

            $action_obj     = $pdo->insert($table, $columns);
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


        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(SELECT staff_name FROM staff as staff  WHERE staff.unique_id = " . $table . ".staff_name ) AS staff_name",
            "designation",
            "department",
            "unique_id",
            "entry_date",
            "termination_letter_no"
        ];
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where = " is_delete = '0' ";

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

                $value['staff_name']        = disname($value['staff_name']);
                $btn_view                   = btn_print1($folder_name, $value['unique_id'], 'termination_letter',"","","");
                $btn_update                 = btn_update($folder_name, $value['unique_id']);
                $btn_delete                 = btn_delete($folder_name, $value['unique_id']);
                $value['unique_id']         = $btn_view . $btn_update . $btn_delete;
                $value['entry_date'] = '<b>' . $value['termination_letter_no'] . '</b><br>' . $value['entry_date'];
                $data[]                     = array_values($value);
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

    case 'get_staffdetails':

        global $pdo;
        $staff_name      = $_POST['staff_name'];
        $table_name    = "staff";
        $where         = [];
        $table_columns = [
            "staff_name",
            "employee_id",
            //"(SELECT branch_name FROM company_and_branch_creation AS company_and_branch_creation WHERE company_and_branch_creation.unique_id = " . $table_name . ".company_name ) AS company_name",
            "(SELECT department FROM department_creation AS department WHERE department.unique_id = " . $table_name . ".department ) AS department",
            "date_of_join",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = " . $table_name . ".designation_unique_id ) AS designation_type",
            "(SELECT work_location FROM work_location_creation AS work_location WHERE work_location.unique_id = " . $table_name . ".work_location ) AS work_location",
            "unique_id"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];


        $where = "is_delete = 0 and is_active = 1 and unique_id='$staff_name'";



        $staff_name_list = $pdo->select($table_details, $where);
        //print_r($staff_name_list);
        if ($staff_name_list->status) {
            if ($staff_name_list->data) {

                $result_values      = $staff_name_list->data[0];

                $jsonResponse = json_encode(['values' => $result_values]);
            }
            echo $jsonResponse;
        }

    default:

        break;
}
