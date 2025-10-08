<?php

function designation_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "designation_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " designation LIKE '".mysql_like($search_key)."' ";

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


?>