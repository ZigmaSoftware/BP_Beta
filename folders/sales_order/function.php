<?php

function form_calculation($unique_id = ""){

            global $pdo;
            $table          = "purchase_order AS po";
            $table_sub      = "purchase_order_sublist AS pos";

            $where = [
                "is_delete"                     => 0,
                "is_active"                     => 1,
                "purchase_order_main_unique_id" => $unique_id
            ];

            $columns    = [
                "pos.quantity",
                "pos.rate",
                "pos.discount",
                "(SELECT t.tax_value FROM tax t WHERE t.unique_id = pos.tax_id) AS tax_value",
                "(SELECT t.tax_name FROM tax t WHERE t.unique_id = pos.tax_id) AS tax_name",
                "(SELECT inc.unit_unique_id FROM item_names_code inc WHERE inc.unique_id = pos.item_name_id) AS unit_id"    
            ];

            print_r($unique_id);

            $table_details =[
                $table_sub,
                $columns
            ];

            $select_result          = $pdo->select($table_details,$where);

            if ($select_result->status) {
                $status     = $select_result->status;
                $data       = $select_result->data;
                $error      = "";
                $sql        = $select_result->sql;


                //update sub list

                foreach ($data as $sub_key => $sub_value) {
                   $sub_total_amount = $qty * $rate * ($discount/100);
                }
                
            } else {
                $status     = $select_result->status;
                $data       = $select_result->data;
                $error      = $select_result->error;
                $sql        = $select_result->sql;
                $msg        = "error";
            }


            print_r($select_result);



}

?>