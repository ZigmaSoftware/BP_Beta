<?php
// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

// Database Country Table Name
$table             = "travel_master";

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

$country_name       = "";
$state_name         = "";
$prefix             = "sta";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $designation       = $_POST["designation"];
        $grade             = $_POST["grade"];
        $transport_type    = $_POST["transport_type"];
        $unique_id         = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "designation"     => $designation,
            "grade"            => $grade,
            "transport_type"   => $transport_type,
            "unique_id"             => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            // [
            //     "COUNT(unique_id) AS count"
            // ]
        ];
       $select_where       =  'is_delete = 0';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="' . $unique_id . '" ';
        }

        $action_obj         = $pdo->select($table_details, $select_where);

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
            if ($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table, $columns, $update_where);

                // Update Ends
            } else {

                // Insert Begins            
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
            "(SELECT grade_type FROM grade_type AS v_grade_type WHERE v_grade_type.unique_id = ".$table .".grade) AS grade",
            "(SELECT designation FROM designation_creation AS con WHERE con.unique_id = ".$table .".designation) AS designation",
            "transport_type",
            "is_active",
            "unique_id"
        ];

        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        // $where          = [
        //     "is_active"     => 1,
        //     "is_delete"     => 0
        // ];

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
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_update = btn_update($folder_name, $value['unique_id']);
            $btn_toggle = $value['is_active'] == 1
                        ? btn_toggle_on($folder_name, $value['unique_id'])
                        : btn_toggle_off($folder_name, $value['unique_id']);

            unset($value['is_active']); // Do not show status column
            $value['unique_id'] = $btn_update . $btn_toggle;

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
    $is_active = $_POST['is_active']; // 1 or 0

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    $status = $action_obj->status;
    $msg    = $status ? ($is_active ? "Activated Successfully" : "Deactivated Successfully") : "Toggle failed!";

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;


        case 'grade':

            $designation          = $_POST['designation'];
    
            $designation_options  = grade_type_name("",$designation);
    
            $designation_options  = select_option($designation_options,"Select the Grade");
    
            echo $designation_options;
            
            break;

            case 'get_designation':

                $grade_type          = $_POST['grade_type'];
        
                $grade_type_options  = designation($grade_type);
        
                $grade_type_options  = select_option($grade_type_options,"Select Designation");
        
                echo $grade_type_options;
                
                break;

    default:

        break;
}
