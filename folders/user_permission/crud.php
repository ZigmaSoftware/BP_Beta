<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "user_screen_permission";
$table_log         = "user_screen_permission_log";

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

$main_screen        = "";
$section_name       = "";
$screen_name        = "";
$screen_folder_name = "";
$icon_name          = "";
$order_no           = "";
$user_actions       = "";
$is_active          = "";
$description        = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    // case 'createupdate':

    //     $json_data          = json_decode($_POST['json_data']);
    //     $rows               = [];
    //     $columns_data       = [];
    //     if (isset($_POST["user_type"])) {
    //     $user_type          = $_POST["user_type"];
    //     } else {
    //     $user_type          = $_POST["unique_id"];
    //     }
    //     $main_screen        = $_POST["main_screen"];
    //     $unique_id          = $_POST["unique_id"];

    //     $update_where       = "";

    //     $columns            = [
    //         "unique_id",
    //         "user_type",
    //         "main_screen_unique_id",
    //         "section_unique_id",
    //         "screen_unique_id",
    //         "action_unique_id"
    //     ];

    //     foreach ($json_data as $data_key => $data_value) {
    //         $columns_data            = [
    //             "unique_id"             => unique_id($prefix),
    //             "user_type"             => $user_type,
    //             "main_screen_unique_id" => $main_screen,
    //             "section_unique_id"     => $data_value->section, 
    //             "screen_unique_id"      => $data_value->screen,
    //             "action_unique_id"      => $data_value->action
    //         ];

    //         $rows[] = $columns_data;
    //     }

    //     // check already Exist Or not
    //     $table_details      = [
    //         $table,
    //         [
    //             "COUNT(unique_id) AS count"
    //         ]
    //     ];
    //     $select_where       = ' main_screen_unique_id = "'.$main_screen.'"  AND user_type = "'.$user_type.'" AND is_delete = 0 ';

    //     // When Update Check without current id
    //     if ($unique_id) {
    //         $select_where   .= ' AND user_type !="'.$unique_id.'" ';
    //     }

    //     $action_obj         = $pdo->select($table_details,$select_where);

    //     // print_r($action_obj);

    //     if ($action_obj->status) {
    //         $status     = $action_obj->status;
    //         $data       = $action_obj->data;
    //         $error      = "";
    //         $sql        = $action_obj->sql;

    //     } else {
    //         $status     = $action_obj->status;
    //         $data       = $action_obj->data;
    //         $error      = $action_obj->error;
    //         $sql        = $action_obj->sql;
    //         $msg        = "error";
    //     }
    //     if ($data[0]["count"]) {
    //         $msg        = "already";
    //     } else if (($data[0]["count"] == 0) && ($msg != "error")) {
    //         // Update Begins
    //         if($unique_id) {

    //             // $columns['user_type'] = $unique_id ;

    //             $update_where   = [
    //                 "user_type"             => $unique_id,
    //                 "main_screen_unique_id" => $main_screen
    //             ];

    //             $action_obj     = $pdo->delete($table,$update_where);

    //         // Update Ends
    //         } 

    //         // Insert Begins            
    //         $action_obj     = $pdo->insertMultiple($table,$columns,$rows);
    //         // Insert Ends



    //         if ($action_obj->status) {

    //             // Log data Entry
    //             $pdo->insertMultiple($table_log,$columns,$rows);

    //             $status         = $action_obj->status;
    //             $data           = $action_obj->data;
    //             $error          = "";
    //             $sql            = $action_obj->sql;

    //             if ($unique_id) {
    //                 $msg        = "update";
    //             } else {
    //                 $msg        = "create";
    //             }
    //         } else {
    //             $status     = $action_obj->status;
    //             $data       = $action_obj->data;
    //             $error      = $action_obj->error;
    //             $sql        = $action_obj->sql;
    //             $msg        = "error";
    //         }
    //     }

    //     $json_array   = [
    //         "status"    => $status,
    //         "data"      => $data,
    //         "error"     => $error,
    //         "msg"       => $msg,
    //         "sql"       => $sql,
    //         "test"      => $columns
    //     ];

    //     echo json_encode($json_array);

    //     break;
    
    case 'createupdate':

    $json_data    = json_decode($_POST['json_data']);
    $rows         = [];
    $columns_data = [];

    if (isset($_POST["user_type"])) {
        $user_type = $_POST["user_type"];
    } else {
        $user_type = $_POST["unique_id"];
    }

    $main_screen  = $_POST["main_screen"];
    $unique_id    = $_POST["unique_id"];
    $update_where = "";

    $columns = [
        "unique_id",
        "user_type",
        "main_screen_unique_id",
        "section_unique_id",
        "screen_unique_id",
        "action_unique_id"
    ];

    // Build rows only if there is checkbox data
    if (!empty($json_data)) {
        foreach ($json_data as $data_key => $data_value) {
            $columns_data = [
                "unique_id"             => unique_id($prefix),
                "user_type"             => $user_type,
                "main_screen_unique_id" => $main_screen,
                "section_unique_id"     => $data_value->section, 
                "screen_unique_id"      => $data_value->screen,
                "action_unique_id"      => $data_value->action
            ];
            $rows[] = $columns_data;
        }
    }

    // Check duplicates
    $table_details = [
        $table,
        [ "COUNT(unique_id) AS count" ]
    ];
    $select_where  = ' main_screen_unique_id = "'.$main_screen.'"  
                       AND user_type = "'.$user_type.'" 
                       AND is_delete = 0 ';

    if ($unique_id) {
        $select_where .= ' AND user_type !="'.$unique_id.'" ';
    }

    $action_obj = $pdo->select($table_details, $select_where);

    if ($action_obj->status) {
        $status = $action_obj->status;
        $data   = $action_obj->data;
        $error  = "";
        $sql    = $action_obj->sql;
    } else {
        $status = $action_obj->status;
        $data   = $action_obj->data;
        $error  = $action_obj->error;
        $sql    = $action_obj->sql;
        $msg    = "error";
    }

    if ($msg != "error") {
        // Always allow update/create even if no checkbox selected
        if ($unique_id) {
            $update_where = [
                "user_type"             => $unique_id,
                "main_screen_unique_id" => $main_screen
            ];
            $pdo->delete($table, $update_where);
        }

        // Only insert if there is permission data
        if (!empty($rows)) {
            $action_obj = $pdo->insertMultiple($table, $columns, $rows);
            if ($action_obj->status) {
                $pdo->insertMultiple($table_log, $columns, $rows);
            }
        } else {
            // Simulate a successful action when no rows are given
            $action_obj = (object)[
                "status" => true,
                "data"   => [],
                "sql"    => "",
                "error"  => ""
            ];
        }

        if ($action_obj->status) {
            $status = true;
            $error  = "";
            $sql    = $action_obj->sql;
            $msg    = $unique_id ? "update" : "create";
        } else {
            $status = false;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = "error";
        }
    }

    $json_array = [
        "status" => $status,
        "data"   => $data ?? [],
        "error"  => $error ?? "",
        "msg"    => $msg ?? "",
        "sql"    => $sql ?? "",
        "test"   => $columns
    ];

    echo json_encode($json_array);
    break;


    case 'datatable':
        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            // "@a:=@a+1 s_no",
            "(SELECT user_type FROM user_type AS ut WHERE ut.unique_id = ".$table.".user_type ) AS user_type",
                "is_active",
            "user_type as unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
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
        $group_by       = "user_type";

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function,$group_by);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            $s_no           = 1;

            foreach ($res_array as $key => $value) {
                // $value['s_no']                = $s_no++;
                array_unshift($value, $s_no++); // S.No as first column

                $value['user_type'] = disname($value['user_type']);
            
                // Generate buttons
                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == "1")
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
            
                // Replace 'unique_id' with buttons
                $value['unique_id'] = $btn_update . $btn_toggle;
            
                // Replace is_active with styled text
                $value['is_active'] = ($value['is_active'] == "1")
                    ? "<span style='color:green'>Active</span>"
                    : "<span style='color:red'>Inactive</span>";
            
                // Remove raw 'is_active' before reindexing (in case it's not at the same key position)
                $keys = array_keys($value);
                $second_last_key = $keys[count($keys) - 2]; // removes the original is_active field
                unset($value[$second_last_key]);
            
                // Reindex and push
                $data[] = array_values($value);
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
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
        "user_type" => $unique_id
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


    case 'sections':

            $main_screen_id        = $_POST['main_screen_id'];

            $section_name_options  = section_name('',$main_screen_id);

            $section_name_options  = select_option($section_name_options,"Select the Screen Section");
    
            echo $section_name_options;
            
            break;
    
    case 'permission_ui':

        $main_screen_id         = $_POST['main_screen'];
        $user_type              = $_POST['user_type'];

        $perm_ui               = user_permission_ui($main_screen_id,$user_type);

        // $section_name_options  = section_name('',$main_screen_id);

        // $section_name_options  = select_option($section_name_options,"Select the Screen Section");

        echo $perm_ui;
        
        break;

    default:
            
            break;
}

?>