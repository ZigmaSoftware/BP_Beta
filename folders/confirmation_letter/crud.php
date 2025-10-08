<?php

// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

// Database Country Table Name
$table             = "confirmation_letter";

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


        $unique_id          = $_POST['unique_id'];

        // if($unique_id == ''){
        //     $unique_ids          = unique_id($prefix);
        // }else{
        //     $unique_ids         = $_POST['unique_id'];
        // }
        $entry_dates          = $_POST['entry_date'];

        if ($_POST['entry_date'] == '') {
            $entry_date          = date('Y-m-d');
        } else {
            $entry_date         = $_POST['entry_date'];
        }
        // if($_POST['confirmation_letter_no'] == ''){
        //     $confirmation_letter_no          = date('Y-m-d');
        // }else{
        //     $confirmation_letter_no         = $_POST['confirmation_letter_no'];
        // }

        // print_r("hii".$unique_id );
        $columns            = [

            "name"                      => $_POST["staff_name"],
            "company_name"              => $_POST["company_name"],
            "company_name_unique_id"    => $_POST["company_name_unique_id"],
            "join_date"                 => $_POST["join_date"],
            "emp_no"                    => $_POST["emp_code"],
            "designation"               => $_POST["designation"],
            "branch"                    => $_POST["department"],
            "gross_salary"              => $_POST["gross_salary"],
            "revised_salary"            => $_POST["revised_salary"],
            "entry_date"                => $entry_date,
            // "unique_id"             =>  $unique_ids
            // "confirmation_letter_no" => $confirmation_letter_no


        ];
        $table_details      = [
            $table,
            // [
            //     "COUNT(unique_id) AS count"
            // ]

            $columns,

        ];
        if ($unique_id) {

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            // Update Begins
            $action_obj     = $pdo->update($table, $columns, $update_where);
            // Update Ends

        } else {
            $where1                  = "acc_year = '" . $_SESSION["acc_year"] . "'";

            $confirmation_no              = bill_no($table, $where1, "XWM/HR/CONF/", 1, 0, 0, false, "/");

            $columns["confirmation_letter_no"]   = $confirmation_no;

            // Unique Id
            $columns["unique_id"]   = unique_id($prefix);

            // Insert Begins
            $action_obj             = $pdo->insert($table, $columns);
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
        // check already Exist Or not

        // $select_where       = ' is_delete  = 0 AND unique_id ="'.$unique_id.'"';

        // When Update Check without current id
        // if ($unique_id) {
        //     $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        // }

        // $action_obj         = $pdo->select($table_details,$select_where);
        // // print_r($action_obj);
        // if ($action_obj->status) {
        //     $status     = $action_obj->status;
        //     $data       = $action_obj->data;
        //     $error      = "";
        //     $sql        = $action_obj->sql;

        // } else {
        //     $status     = $action_obj->status;
        //     $data       = $action_obj->data;
        //     $error      = $action_obj->error;
        //     $sql        = $action_obj->sql;
        //     $msg        = "error";

        // }
        // if ($data[0]["count"] ) {
        //     $msg        = "already";
        // } else if (($data[0]["count"] == 0) ) {
        //     // Update Begins
        //     if($unique_id) {

        //         unset($columns['unique_id']);

        //         $update_where   = [
        //             "unique_id"     => $unique_id
        //         ];

        //         $action_obj     = $pdo->update($table,$columns,$update_where);

        //     // Update Ends
        //     } else {
        //     $where1                  = "acc_year = '".$_SESSION["acc_year"]."'";

        //     $confirmation_no              = bill_no ($table,$where1,"ASCENT/HR/CONF/", 1,0,0,false,"/");

        //     $columns["confirmation_letter_no"]   = $confirmation_no;

        //     // Unique Id
        //     // $columns["unique_id"]   = unique_id($prefix);
        //         // Insert Begins            
        //         $action_obj     = $pdo->insert($table,$columns);
        //         // Insert Ends

        //     }
        //     }
        //     if ($action_obj->status) {
        //         $status     = $action_obj->status;
        //         $data       = $action_obj->data;
        //         $error      = "";
        //         $sql        = $action_obj->sql;

        //         if ($unique_id) {
        //             $msg        = "update";
        //         } else {
        //             $msg        = "add";
        //         }
        //     } else {
        //         $status     = $action_obj->status;
        //         $data       = $action_obj->data;
        //         $error      = $action_obj->error;
        //         $sql        = $action_obj->sql;
        //         $msg        = "error";
        //     }
        // // }


        // $json_array   = [
        //     "status"    => $status,
        //     "data"      => $data,
        //     "error"     => $error,
        //     "msg"       => $msg,
        //     "sql"       => $sql
        // ];

        // echo json_encode($json_array);

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
        $company_name = $_POST['company_name'];
        //print_r($company_name);

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",

            "entry_date",
            "(SELECT staff_name FROM staff as staff  WHERE staff.unique_id = " . $table . ".name ) AS name",
            // "name",
            "company_name",
            // "(SELECT company_name FROM company_name_creation as staff  WHERE staff.unique_id = ".$table.".company_name ) AS company_name",
            // "(SELECT branch_name FROM company_and_branch_creation AS company_and_branch_creation WHERE company_and_branch_creation.unique_id = ".$table.".company_name ) AS company_name",
            "designation",

            "branch",
            "emp_no",
            "join_date",
            "gross_salary",
            "revised_salary",
            "unique_id",

            "confirmation_letter_no",
        ];
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
        // $where          = [
        //     "is_delete"     => 0
        // ];
        $where = " is_delete = '0' ";



        if ($_POST['company_name']) {
            $where .= " AND company_name_unique_id = '$company_name' ";
        }
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

        // $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);

        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                // $value['letter_no']   = disname($value['letter_no']);
                $value['name']        = disname($value['name']);
                $value['company_name'] = disname($value['company_name']);
                $value['entry_date'] = '<b>' . $value['confirmation_letter_no'] . '</b><br>' . $value['entry_date'];

                $value['join_date']   = disdate($value['join_date']);
                // $value['to_date'] = disdate($value['to_date']);            
                $btn_view             = btn_print1($folder_name, $value['unique_id'], 'confirmation_letter', "", "", "");
                $btn_update           = btn_update($folder_name, $value['unique_id']);
                $btn_delete           = btn_delete($folder_name, $value['unique_id']);
                $value['unique_id']   = $btn_view . $btn_update . $btn_delete;
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
            "(SELECT department FROM department_creation AS department WHERE department.unique_id = " . $table_name . ".department ) AS department",
            "date_of_join",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = " . $table_name . ".designation_unique_id ) AS designation_type",
            "(SELECT company_name FROM company_name_creation AS company_name WHERE company_name.unique_id = " . $table_name . ".company_name ) AS company",
            "company_name",
            "unique_id"


        ];

        $table_details = [
            $table_name,
            $table_columns
        ];


        $where = "is_delete = 0 and is_active = 1 and unique_id='$staff_name'";



        $staff_name_list = $pdo->select($table_details, $where);
        // print_r($staff_name_list);
        if ($staff_name_list->status) {
            // return $val = $staff_name_list->data[0];
            if ($staff_name_list->data) {
                // foreach($val as $key =>$value){
                //     $employee_id        = $result_values["employee_id"];
                // }
                $result_values      = $staff_name_list->data[0];

                // print_r($result_values);

                // $employee_id        = $result_values["employee_id"];
                $jsonResponse = json_encode(['values' => $result_values]);
            }
            echo $jsonResponse;
        }








    default:

        break;
}
