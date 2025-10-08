<?php


function staff_name_like($search_key = "") {



    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "staff";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " staff_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}


function staff_designation($staff_id = "") {

    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "designation_unique_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($staff_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where              = [];
        $where["unique_id"] = $staff_id;
    }

    $staff_designation = $pdo->select($table_details, $where);

    
    if ($staff_designation->status) {
        return $staff_designation->data;
    } else {
        print_r($staff_designation);
        return 0;
    }
}

?>