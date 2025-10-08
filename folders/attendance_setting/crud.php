<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table                  = "attendance_setting";
$table_late_permission  = "attendance_late_permission";
$table_leave_type       = "attendance_leave_type";
$table_holiday          = "attendance_holidays";

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

$bank_name          = "";
$description        = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose
$permission_leave_type  = [
    1 => [
        "id" => 1,
        "text"     => "Permission",
    ],
    2 => [
        "id" => 2,
        "text"     => "Half Day Leave",
    ],
    3 => [
        "id" => 3,
        "text"     => "Full Day Leave",
    ],
  ];

switch ($action) {
    case 'createupdate':

        $attendance_shift_name      = $_POST["attendance_shift_name"];
        $attendance_shift_hr        = $_POST["attendance_shift_hr"];
        $working_time_from          = $_POST["working_time_from"];
        $working_time_to            = $_POST["working_time_to"];
        $late_hrs                   = $_POST["late_hrs"];
        $permission_hrs             = $_POST["permission_hrs"];
        $late_time                  = $_POST["late_time"];
        $permission_time            = $_POST["permission_time"];
        $unique_id                  = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "attendance_shift_name"     => $attendance_shift_name,
            "attendance_shift_hr"       => $attendance_shift_hr,
            "working_time_from"         => $working_time_from,
            "working_time_to"           => $working_time_to,
            "late_hrs"                  => $late_hrs,
            "permission_hrs"            => $permission_hrs,
            "late_time"                 => $late_time,
            "permission_time"           => $permission_time,
            "unique_id"                 => unique_id($prefix)
        ];
  // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'attendance_shift_name = "'.$attendance_shift_name.'" AND attendance_shift_hr = "'.$attendance_shift_hr.'" AND working_time_from = "'.$working_time_from.'" AND working_time_to = "'.$working_time_to.'" AND is_delete = 0  ';

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
            "@a:=@a+1 s_no",
            "attendance_shift_name",
            "attendance_shift_hr",
            "working_time_from",
            "working_time_to",
            "late_hrs",
            "permission_hrs",
             "is_active",
            "unique_id"
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
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                switch($value['attendance_shift_hr']){

                    case '1':
                        $shift_hr  = "8 hrs";
                        break;
                    case '2':
                        $shift_hr  = "12 hrs";
                        break;
                    case '3':
                        $shift_hr  = "24 hrs";
                        break;
                }

                $value['attendance_shift_hr']  = $shift_hr;
                $value['working_time_from']    = date('h:i a',strtotime($value['working_time_from']));
                $value['working_time_to']      = date('h:i a',strtotime($value['working_time_to']));
                $value['late_hrs']             = date('h:i a',strtotime($value['late_hrs']));
                $value['permission_hrs']       = date('h:i a',strtotime($value['permission_hrs']));
                
                
                $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_toggle = ($value['is_active'] == 1)
                        ? btn_toggle_on($folder_name, $value['unique_id'])
                        : btn_toggle_off($folder_name, $value['unique_id']);
                
                    // Remove is_active from appearing as a column
                    unset($value['is_active']);
                
                    $value['unique_id'] = $btn_update . $btn_toggle;
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
    $is_active = $_POST['is_active'];

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    $status = $action_obj->status;
    $msg    = $status
        ? ($is_active == 1 ? "Activated Successfully" : "Deactivated Successfully")
        : "Toggle failed!";

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

    case 'leave_permission':

        $late_permission      = $_POST["late_permission"];

        
        // Control Attendance Showing
       
            switch ($late_permission) {
                case '1':
                    $leave_permission_remove = [2,3];
                    break;

                case '2':
                    $leave_permission_remove = [1];
                    break;

               
            }
                $permission_leave_type = array_diff_key($permission_leave_type, array_flip($leave_permission_remove));

        echo select_option($permission_leave_type,"Select");

        break;

    case 'late_permission_leave_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "late_permission_leave";
        
        // Fetch Data
        $attendance_setting_unique_id = $_POST['attendance_setting_unique_id']; 

        // DataTable 
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
            "late_permission_type",
            "late_count",
            "permission_leave_type",
            "permission_count",
            "unique_id"
        ];
        $table_details  = [
            $table_late_permission." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "attendance_set_unique_id"  => $attendance_setting_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        $where = " is_delete = '0' ";

       
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,"",$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                switch($value['late_permission_type']){

                    case '1':
                        $late_permission  = "Late";
                        break;
                    case '2':
                        $late_permission  = "Permission";
                        break;
                    
                }
                switch($value['permission_leave_type']){

                    case '1':
                        $permission_leave  = "Permission";
                        break;
                    case '2':
                        $permission_leave  = "Half Day Leave";
                        break;
                    case '3':
                        $permission_leave  = "Full Day Leave";
                        break;
                }

                $value['late_permission_type']  = $late_permission;
                $value['permission_leave_type'] = $permission_leave;
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_delete;
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


        case 'late_permission_leave_add_update':

            $late_permission                  = $_POST["late_permission"];
            $leave_permission                 = $_POST["leave_permission"];
            $late_count                       = $_POST["late_count"];
            $permission_count                 = $_POST["permission_count"];
            $attendance_setting_unique_id     = $_POST["attendance_setting_unique_id"];
            $unique_id                        = $_POST["unique_id"];
    
            $update_where                     = "";
    
            $columns            = [
                "late_permission_type"             => $late_permission,
                "permission_leave_type"            => $leave_permission,
                "late_count"                       => $late_count,
                "permission_count"                 => $permission_count,
                "attendance_set_unique_id"         => $attendance_setting_unique_id,
                "unique_id"                        => unique_id($prefix)
            ];
    
            // check already Exist Or not
            $table_details      = [
                $table_late_permission,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where     = 'late_permission_type ="'.$late_permission.'" AND is_delete = 0  AND late_count = "'.$late_count.'" AND permission_leave_type = "'.$leave_permission.'" AND permission_count = "'.$permission_count.'" AND attendance_set_unique_id = "'.$attendance_setting_unique_id.'" ';
    
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
            } else if (($data[0]["count"] == 0) && ($msg != "error")) {
                // Update Begins
                if($unique_id) {
    
                    unset($columns['unique_id']);
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_late_permission,$columns,$update_where);
    
                // Update Ends
                } else {
    
                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_late_permission,$columns);
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
                        $msg        = "add";
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

    // case "late_permission_leave_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];
        
    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "late_permission_type",
    //         "late_count",
    //         "permission_leave_type",
    //         "permission_count",
            
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_late_permission,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];        

    //     $result         = $pdo->select($table_details,$where);

    //     if ($result->status) {
            
    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }
        
    //     echo json_encode($json_array);
    //     break;

    case 'late_permission_leave_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_late_permission,$columns,$update_where);

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

    case 'leave_type_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "leave_type";
        
        // Fetch Data
        $attendance_setting_unique_id = $_POST['attendance_setting_unique_id']; 

        // DataTable 
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
            "leave_type",
            "leave_days",
            "unique_id"
        ];
        $table_details  = [
            $table_leave_type." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "attendance_set_unique_id"  => $attendance_setting_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        $where = " is_delete = '0' ";

       
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,"",$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_delete;
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


    case 'leave_type_add_update':

        $leave_days                       = $_POST["leave_days"];
        $leave_type                       = $_POST["leave_type"];
        $attendance_setting_unique_id     = $_POST["attendance_setting_unique_id"];
        $unique_id                        = $_POST["unique_id"];

        $update_where                     = "";

        $columns            = [
            "leave_days"             => $leave_days,
            "leave_type"       => $leave_type,
            "attendance_set_unique_id"    => $attendance_setting_unique_id,
            "unique_id"                   => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_leave_type,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where     = ' is_delete = 0  AND leave_type = "'.$leave_type.'" AND attendance_set_unique_id = "'.$attendance_setting_unique_id.'" ';

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
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_leave_type,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_leave_type,$columns);
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
                    $msg        = "add";
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

    // case "late_permission_leave_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];
        
    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "late_permission_type",
    //         "late_count",
    //         "permission_leave_type",
    //         "permission_count",
            
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_leave_type,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];        

    //     $result         = $pdo->select($table_details,$where);

    //     if ($result->status) {
            
    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }
        
    //     echo json_encode($json_array);
    //     break;

    case 'leave_type_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_leave_type,$columns,$update_where);

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

    case 'holidays_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "holiday";
        
        // Fetch Data
        $attendance_setting_unique_id = $_POST['attendance_setting_unique_id']; 

        // DataTable 
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
            "holiday_date",
            "remarks",
            "unique_id"
        ];
        $table_details  = [
            $table_holiday." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "attendance_set_unique_id"  => $attendance_setting_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        $where = " is_delete = '0' ";

       
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,"",$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $value['holiday_date']  = disdate($value['holiday_date']);
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_delete;
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


    case 'holidays_add_update':

        $holiday_date                  = $_POST["holiday_date"];
        $remarks                       = $_POST["remarks"];
        $attendance_setting_unique_id  = $_POST["attendance_setting_unique_id"];
        $unique_id                     = $_POST["unique_id"];

        $update_where                  = "";

        $columns            = [
            "holiday_date"             => $holiday_date,
            "remarks"                  => $remarks,
            "attendance_set_unique_id" => $attendance_setting_unique_id,
            "unique_id"                => unique_id($prefix)
        ];

        $columns_sub  = [
            "cancel_status"  => 1,
        ];
         $table_details_sub      = [
            "leave_details_sub",
            [
                "COUNT(from_date) AS count"
            ]
        ];

        // check already Exist Or not
        $table_details      = [
            $table_holiday,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        $select_where_sub    = ' is_delete = 0  AND from_date = "'.$holiday_date.'"';
        $action_obj_sub      = $pdo->select($table_details_sub,$select_where_sub);

        if ($action_obj_sub->status) {
            $status_sub     = $action_obj_sub->status;
            $data_sub       = $action_obj_sub->data;
            $error_sub      = "";
            $sql_sub        = $action_obj_sub->sql;

        } else {
            $status_sub     = $action_obj_sub->status;
            $data_sub       = $action_obj_sub->data;
            $error_sub      = $action_obj_sub->error;
            $sql_sub       = $action_obj_sub->sql;
            $msg_sub        = "error";
            
        }

        $select_where     = ' is_delete = 0  AND holiday_date = "'.$holiday_date.'" AND attendance_set_unique_id = "'.$attendance_setting_unique_id.'" ';

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
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_holiday,$columns,$update_where);
                $update_where_sub   = [
                    "from_date"     => $holiday_date
                ];
                if ($data_sub[0]["count"]) {
                    $action_obj     = $pdo->update("leave_details_sub",$columns_sub,$update_where_sub);
                }

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_holiday,$columns);
                $update_where_sub   = [
                    "from_date"     => $holiday_date
                ];
                if ($data_sub[0]["count"]) {
                    $action_obj     = $pdo->update("leave_details_sub",$columns_sub,$update_where_sub);
                }


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
                    $msg        = "add";
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

    // case "late_permission_leave_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];
        
    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "late_permission_type",
    //         "late_count",
    //         "permission_leave_type",
    //         "permission_count",
            
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_holiday,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];        

    //     $result         = $pdo->select($table_details,$where);

    //     if ($result->status) {
            
    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }
        
    //     echo json_encode($json_array);
    //     break;

    case 'holiday_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_holiday,$columns,$update_where);

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