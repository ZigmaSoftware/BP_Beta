<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// // Database Country Table Name
$table             = "main_category_creation";

// // Include DB file and Common Functions
include '../../config/dbconfig.php';

// // Variables Declaration
    $action             = $_POST['action'];

    //$user_type          = "";
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
    
        $department   = $_POST["department"];
        $category_name     = $_POST["category_name"];
        $description       = $_POST["description"];
        $unique_id         = $_POST["unique_id"];
       
        $update_where       = "";

        $columns            = [
            "department"        => $department,
            "category_name"          => $category_name,
            "description"            => $description,
            "unique_id"              => unique_id($prefix)
        ];

            // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = ' is_delete = 0 AND department ="'.$department.'" AND category_name ="'.$category_name.'" ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

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
            // Update Begins
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

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(select department from department_creation where department_creation.unique_id = ".$table.".department) as department_name",
            "category_name",
            "description",
            "unique_id as uni_id",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        if($_POST['department']!=''){

            $department = "AND department = '".$_POST['department']."'";
        } 
        // else {
        //     $department = "";
        // }

        $where      = " is_active = 1 ";
        $where     .= " AND is_delete = 0  ".$department." ";
        
        // if ($_POST['search']['value']) {
        //     $where .= " AND ((category_name LIKE '".mysql_like($_POST['search']['value'])."') ";
        //     $where .= " OR (department in (".department_name_like($_POST['search']['value'])."))) ";
        // }
        
        //SEARCH
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((category_name LIKE '$searchValue') ";
            $where .= " OR (department IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (description LIKE '$searchValue') ";
            $where .= " OR (unique_id LIKE '$searchValue')) ";
}

        
        
        $order_by       = "";
        
        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                // $value['open_date'] =   disdate($value['open_date']);
                $btn_update         = btn_update($folder_name,$value['unique_id']);
                $btn_delete         = btn_delete($folder_name,$value['unique_id']);
                $value['department'] = department_wise($value['department'])[0]['department'];
                // $value['department'] = department($value['department'])[0]['department'];
                $value['unique_id']     = $btn_update.$btn_delete;
                $data[]                 = array_values($value);
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
