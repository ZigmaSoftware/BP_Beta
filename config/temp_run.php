<?php
   
 include "dbconfig.php";

 $where = [];

 $table = "view_staff_check_in_out";

  $where = " entry_date between '2023-08-26' and '2023-09-25' and check_out_time IS NULL";
 
//  $having = " count(id) > 1";
 $table_columns = [
        "staff_id",
        "entry_date",
    ];

    $table_details = [
        $table,
        $table_columns
    ];
$prefix = "";
$result         = $pdo->select($table_details,$where);
  //print_r($result);     
        if ($result->status) {
    
            $res_array      = $result->data;
    
            foreach ($res_array as $key => $value) {
    		 $table_att = "daily_attendance";

  $where_att = " entry_date = '".$value['entry_date']."' and staff_id = '".$value['staff_id']."' and attendance_type = 1";
// print_r($where_att);
//  $having = " count(id) > 1";
 $table_columns_att = [
        "staff_id",
        "entry_date",
        "latitude",
        "longitude",
        "day_status",
        "day_type",
    ];

    $table_details_att = [
        $table_att,
        $table_columns_att
    ];
$prefix = "";
$continents_arr_att = $pdo->select($table_details_att, $where_att);
//print_r($continents_arr_att)."<br>";
        $columns_ins = [
            "staff_id"          => $value['staff_id'],
            "entry_date"        => $value['entry_date'],
            "entry_time"        => "20:00",
            "latitude"          => $value['latitude'],
            "longitude"         => $value['longitude'],
            "attendance_type"   => "2",
            "day_status"        => $value['day_status'],
            "day_type"          => $value['day_type'],
            "unique_id"         => unique_id($prefix)
        ];

	 $action_obj     = $pdo->insert($table_att,$columns_ins);
//         $where   = [
//             "city_id" => $value["city_id"]
//         ];


//         $update_val = $pdo->update($table,$columns,$where);
// print_r($update_val);
        if ($action_obj->status) {
            echo "Update ID = ".$action_obj->data;
        } else {
            print_r($action_obj);
        }
    }
 }

?>