<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = "daily_attendance";

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

$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$attendance_type       = [
    1 => [
        "id"    => 1,
        "text"  => "Check-In"
    ],
    2 => [
        "id"    => 2,
        "text"  => "Check-Out"
    ],
    3 => [
        "id"    => 3,
        "text"  => "Break-In"
    ],
    4 => [
        "id"    => 4,
        "text"  => "Break-Out"
    ]
];

$day_status            = [
    0 => [
        "id"    => 0,
        "text"  => "Leave"
    ],
    1 => [
        "id"    => 1,
        "text"  => "Present"
    ],
    2 => [
        "id"    => 2,
        "text"  => "Late"
    ],
    3 => [
        "id"    => 3,
        "text"  => "Permission"
    ],
    4 => [
        "id"    => 4,
        "text"  => "Half-Day Leave"
    ],
    5 => [
        "id"    => 5,
        "text"  => "Leave"
    ],
    6 => [
        "id"    => 6,
        "text"  => "Absent"
    ]
];

switch ($action) {
    case 'createupdate':

        $staff_id            = $_POST["staff_id"];
        $entry_date          = $_POST["entry_date"];
        $entry_time          = $_POST["entry_time"];
        // $entry_time          = date("H:i:s");
        $latitude            = "11.312746";
        $longitude           = "77.670675";
        $attendance_type     = $_POST["attendance_type"];
        $day_status          = $_POST["day_status"];
        $day_type            = $_POST["day_type"];
        $unique_id           = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "staff_id"          => $staff_id,
            "entry_date"        => $entry_date,
            "entry_time"        => $entry_time,
            "latitude"          => $latitude,
            "longitude"         => $longitude,
            "attendance_type"   => $attendance_type,
            "day_status"        => $day_status,
            "day_type"          => $day_type,
            "unique_id"         => unique_id($prefix)
        ];

        // Check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        if (($attendance_type != 2) && ($attendance_type != 3) && ($attendance_type != 4)) {

            $select_where       = 'staff_id = "'.$staff_id.'" AND entry_date = "'.$entry_date.'" AND attendance_type = "'.$attendance_type.'" AND is_delete = 0  ';
        
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
                if($unique_id) {
                    // Update Begins
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
        } else {

            $action_obj     = $pdo->insert($table,$columns);

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
            "entry_date",
            "staff_id",
            "attendance_type",
            "day_status",
            "unique_id",
            "entry_time"
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

        if ($order_by) {
            $order_by .= ",";
        }

        $order_by       .= " id DESC ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $value['entry_date']       = disdate($value['entry_date'])." (".date('h:i:s A', strtotime($value['entry_time'])).")";

                $staff_details             = staff_name($value['staff_id']);

                $value['staff_id']         = $staff_details[0]['staff_name'];
                
                $value['attendance_type']  = $attendance_type[$value['attendance_type']]['text'];

                $value['day_status']       = $day_status[$value['day_status']]['text'];

                $btn_update                = btn_update($folder_name,$value['unique_id']);
                $btn_delete                = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']        = $btn_update.$btn_delete;
                $data[]                    = array_values($value);
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

    case 'staff_name':

        $staff_id         = $_POST['staff_id'];

        $staff_details    = staff_name($staff_id);

        $staff_name       = disname($staff_details[0]['staff_name']);

        echo $staff_name;
        
        break;

    case 'attendance_type':

        $entry_date    = "";
        $is_update     = "";
        $staff_id      = $_POST["staff_id"];

        $attendance_types_already = [];

        // Get All Attendance Types in Database
        // $attendance_types_sql = "SELECT CONCAT(attendance_type) AS attendance_type FROM $table WHERE entry_date = '".$entry_date."' AND is_delete = 0";
        $attendance_types_sql = "SELECT GROUP_CONCAT(attendance_type) AS attendance_type FROM $table WHERE is_delete = 0 AND staff_id = '".$staff_id."' AND entry_date = '".$today."' ";

        $attendance_type_result = $pdo->query($attendance_types_sql);

        if ($attendance_type_result->status) {
            
            // if (!empty($attendance_types_already->data)) {
                $attendance_types_already = $attendance_type_result->data[0]['attendance_type'];

                $attendance_types_already = explode(",",$attendance_types_already);
            // }

        } else {
            print_r($attendance_type_result);
        }

        $attendance_type_remove = [];

        // Control Attendance Showing
        if (!empty($attendance_types_already)) {
            // if (end($attendance_types_already) == 3) {
            //     $attendance_type_remove = [1,2,3];
            // }
            $selected    = "";
            $last_status = end($attendance_types_already);

            switch ($last_status) {
                case 1:
                    $attendance_type_remove = [1,4];

                    break;

                // case 2:
                //     # code...
                //     break;

                case 3:
                    $attendance_type_remove = [1,2,3];
                    $selected   = 4;
                    break;

                case 4:
                    $attendance_type_remove = [1,4];
                    break;
                
                default:
                    $attendance_type_remove = [2,3,4];
                    $selected   = 1;
                    break;
            }
        }

        $attendance_type = array_diff_key($attendance_type, array_flip($attendance_type_remove));

        echo select_option($attendance_type,"Select Attendance Type",$selected);

        break;

    case 'day_status':
        echo day_status($_POST['staff_id']);
        break;

        case 'day_type':
            echo day_type($_POST['staff_id']);
            break;
    
        default:
            
            break;
    }
    
    function day_type ($staff_id = "") {
        global $pdo,$today;
    
        $date = date('Y-m-d');
        $current_message = "Office-Work";
        $current_status  = 7;
        
        if ($staff_id) {
            $day_query  = "SELECT day_type FROM leave_details WHERE staff_id = '".$staff_id."' and (from_date >= '".$date."' or to_date >= '".$date."')  and is_approved = 1 and is_delete = 0";
    
            $day_result = $pdo->query($day_query);
            
            if ($day_result->status) {
                if (!empty($day_result->data)) {
                    $day_result     = $day_result->data[0];
      
                    $day_type         = $day_result['day_type'];
    
                    if ($day_type == 1) {
                        $current_message = "Full Day";
                        $current_status  = 1;
                    } else if ($day_type == 2) {
                        // echo 'Late';
                        $current_message = "Half Day";
                        $current_status  = 2;
                    } else if ($day_type == 3) {
                        // echo 'Permission';
                        $current_message = "Work From Home";
                        $current_status  = 3;
                    } else if ($day_type == 4) {
                        // echo 'Half-day';
                        $current_message = "Idle";
                        $current_status  = 4;
                    } else if ($day_type == 5) {
                        // echo 'Half-day';
                        $current_message = "On-Duty";
                        $current_status  = 5;
                    } else if ($day_type == 6) {
                        // echo 'Half-day';
                        $current_message = "Permission";
                        $current_status  = 6;
                    } 
                }
            } else {
                print_r($day_result);
            }
        }
    
        return json_encode([
            "message"        => $current_message,
            "status"         => $current_status,
            
        ]);
    }
    
    

function day_status ($staff_id = "") {
    global $pdo,$today;

    $current_message = "Leave";
    $current_status  = 0;
    $premises_type   = 0;
    $branch_id       = "";
    $branch_lat      = "";
    $branch_lng      = "";
    $branch_rds      = "";

    if ($staff_id) {
        $attendance_query  = "SELECT attendance_setting_id,premises_type,branch_id FROM staff WHERE unique_id = '".$staff_id."' ";

        $attendance_result = $pdo->query($attendance_query);
        
        if ($attendance_result->status) {
            if (!empty($attendance_result->data)) {
                $attendance_result     = $attendance_result->data[0];

                $attendance_setting_id = $attendance_result['attendance_setting_id'];
                $branch_id             = $attendance_result['branch_id'];
                $premises_type         = $attendance_result['premises_type'];


                // If Set Branch ID Get Branch Details

                if ($branch_id) {
                    $branch_details     = branch($branch_id);

                    if (!empty($branch_details)) {
                        $branch_details  = $branch_details[0];
                        
                        $branch_lat      = $branch_details["latitude"];
                        $branch_lng      = $branch_details["longitude"];
                        $branch_rds      = $branch_details["radius"];
                    }
                }

                if ($attendance_setting_id) {
                    // Get Attendance Details
                    $attendance_details_query  = "SELECT * FROM attendance_setting WHERE unique_id = '".$attendance_setting_id."' ";

                    $attendance_details_result = $pdo->query($attendance_details_query);

                    if ($attendance_details_result->status) {
                        if (!empty($attendance_details_result->data)) {
                            $attendance_details = $attendance_details_result->data[0];
                            $perm_time_start = [];
                            // $start_late
                            // Get Start,End, Late & Permission
                            $start_time         = $attendance_details["working_time_from"];
                            $end_time           = $attendance_details["working_time_to"];
                            $late_time          = $attendance_details["late_hrs"];
                            $permission_time    = $attendance_details["permission_hrs"];

                            // Permission Start time Calculate
                            $perm_time_start[]   = $start_time;
                            $perm_time_start[]   = $late_time;

                            $permission_start_at = AddTime($perm_time_start);

                            // Permission End time Calculate
                            $perm_time_end       = [];

                            $perm_time_end[]     = $permission_start_at;
                            $perm_time_end[]     = $permission_time;

                            $permission_end_at   = AddTime($perm_time_end);

                            $current_ti          = "13:00:00"; // For testing
                            $current_ti          = date("H:i:s");

                            if (strtotime($current_ti) <= strtotime($start_time)) {
                                $current_message = "Present";
                                $current_status  = 1;
                            } else if (check_in_range($start_time,$permission_start_at,$current_ti)) {
                                // echo 'Late';
                                $current_message = "Late";
                                $current_status  = 2;
                            } else if (check_in_range($permission_start_at,$permission_end_at,$current_ti)) {
                                // echo 'Permission';
                                $current_message = "Permission";
                                $current_status  = 3;
                            } else {
                                // echo 'Half-day';
                                $current_message = "Half-day";
                                $current_status  = 4;
                            }

                            if ($_POST['attendance_type'] != 1) {
                                $current_message = '';
                                $current_status  = 1;
                            }
                        }
                    }
                }
            }
        } else {
            print_r($attendance_result);
        }
    }

    return json_encode([
        "message"        => $current_message,
        "status"         => $current_status,
        "premises_type"  => $premises_type,
        "branch_lat"     => $branch_lat,
        "branch_lng"     => $branch_lng,
        "branch_rds"     => $branch_rds
    ]);
}

// $times = array();

// $times[] = "12:59";
// $times[] = "0:58";
// $times[] = "0:02";

// pass the array to the function
// echo AddPlayTime($times);

function AddTime($times) {
    $minutes = 0; //declare minutes either it gives Notice: Undefined variable
    // loop throught all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d:%02d:%02d', $hours, $minutes,"00");
}

function day_status1 () {

    $current_time            = time();
    
    // It Need's Master Data to Continue 
    
    // Forenoon Timings When Check-in 
    $check_in_late_start     = strtotime("09:40:00");
    $check_in_late_end       = strtotime("10:00:00");
    $check_in_permission     = strtotime("11:30:00");
    $check_in_half_day       = strtotime("14:00:00");

    // Afternoon Timings When Check-out
    $check_out_leave         = strtotime("12:00:00");
    $check_out_half_day_end  = strtotime("16:00:00");
    $check_out_permission    = strtotime("19:00:00");

    // For Check-In
    if (($current_time > $check_in_late_start) && ($current_time <= $check_in_late_end))  {
        echo "Late";
    } else if (($current_time >= $check_in_late_end) && ($current_time <= $check_in_permission)) {
        echo "Morning Permission";
    } else if (($current_time >= $check_in_permission) && ($current_time < $check_out_leave)) {
        echo "Morning Half";
    }
    
    // For Check-out
    if (($current_time >= $check_out_leave) && ($current_time <= $check_out_half_day_end)) {
        echo 'Afternoon Half';
    } else if (($current_time >= $check_out_half_day_end) && ($current_time <= $check_out_half_day_end)) {
        echo 'Afternoon Permission';
    }
}

function check_in_range($start_time, $end_time, $current_time = "") {
    
    // echo " Start => ".$start_time;
    // echo " End => ".$end_time;
    // echo " Now => ".$current_time;

    // Convert to timestamp
    $start  = strtotime($start_time);
    $end    = strtotime($end_time);

    if ($current_time) {
        $check  = strtotime($current_time);
    } else {
        $check  = time();
    }

    // print_r($start);
    // echo "   ";
    // print_r($end);
    // echo "   ";
    // print_r($check);
    // echo "   ";
  
    // Check that user time is between start & end
    return (($start <= $check ) && ($check <= $end));

}

?>