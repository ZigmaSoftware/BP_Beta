<?php 
include '../../config/dbconfig.php';

	global $pdo;

    $entry_date    = $_POST['entry_date'];
    $table_name    = "daily_attendance";
    $where         = [];

    $prefix        = "";
    $id  		   = 1;

    for($id = 1; $id <= 6; $id++){
    	$staff_id            = "XWM".$id;
        $entry_date          = $_POST["entry_date"];
        $entry_time          = "09:30:00";
        $latitude            = "11.337724651637656";
        $longitude           = "77.71901608807755";
        $attendance_type     = 1;
        $day_status          = 1;
        $day_type            = 7;
        
        $update_where       = "";

        $staff_details   = $conn->prepare("SELECT unique_id FROM staff WHERE employee_id = '".$staff_id."'");
        $staff_details->execute();
        $staff           = $staff_details->fetch();

        $columns            = [
            "staff_id"          => $staff['unique_id'],
            "entry_date"        => $entry_date,
            "entry_time"        => $entry_time,
            "latitude"          => $latitude,
            "longitude"         => $longitude,
            "attendance_type"   => $attendance_type,
            "day_status"        => $day_status,
            "day_type"          => $day_type,
            "unique_id"         => unique_id($prefix)
        ];
        $table_details      = [
            $table_name,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        if (($attendance_type != 2) && ($attendance_type != 3) && ($attendance_type != 4)) {

            $select_where       = 'staff_id = "'.$staff_id.'" AND entry_date = "'.$entry_date.'" AND attendance_type = "'.$attendance_type.'" AND is_delete = 0  ';
        
            // When Update Check without current id
            // if ($staff_id) {
            //     $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
            // }

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
                // if($unique_id) {
                //     // Update Begins
                //     unset($columns['unique_id']);

                //     $update_where   = [
                //         "unique_id"     => $unique_id
                //     ];

                //     $action_obj     = $pdo->update($table,$columns,$update_where);
                //     // Update Ends
                // } else {
                    // Insert Begins                
                    $action_obj     = $pdo->insert($table_name,$columns);
                    // Insert Ends
                //}
            }
        }

        $entry_time          = "19:00:00";
        $attendance_type     = 2;
        $columns            = [
            "staff_id"          => $staff['unique_id'],
            "entry_date"        => $entry_date,
            "entry_time"        => $entry_time,
            "latitude"          => $latitude,
            "longitude"         => $longitude,
            "attendance_type"   => $attendance_type,
            "day_status"        => $day_status,
            "day_type"          => $day_type,
            "unique_id"         => unique_id($prefix)
        ];
        $table_details      = [
            $table_name,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

       // if (($attendance_type != 2) {

            $select_where       = 'staff_id = "'.$staff_id.'" AND entry_date = "'.$entry_date.'" AND attendance_type = "'.$attendance_type.'" AND is_delete = 0  ';
        
            // When Update Check without current id
            // if ($unique_id) {
            //     $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
            // }

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
                // if($unique_id) {
                //     // Update Begins
                //     unset($columns['unique_id']);

                //     $update_where   = [
                //         "unique_id"     => $unique_id
                //     ];

                //     $action_obj     = $pdo->update($table,$columns,$update_where);
                //     // Update Ends
                // } else {
                    // Insert Begins                
                    $action_obj     = $pdo->insert($table_name,$columns);
                    // Insert Ends
               // }
            }
       // }
    }
?>