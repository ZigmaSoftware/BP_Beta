<?php



function price_bid_competitor_update($unique_id) {
    
    if (!($unique_id)) {

        $unique_id          = $_POST['unique_id'];

    }

    global $pdo;

    $competitor_id          = $_POST['price_bid_competitor_name'];
    $competitor_status      = $_POST['price_bid_competitor_status'];
    $competitor_reason      = $_POST['competitor_reason'];
    // $competitor_position    = $_POST['competitor_position'];
    // $bidders_total          = $_POST['bidders_total'];

    foreach ($competitor_id as $com_key => $com_value) {

        // echo $com_key;
        // echo $com_value;

        $update_where   = [
            "price_bid_id"      => $unique_id,
            "competitor_id"     => $competitor_id[$com_key]
        ];

        $update_columns = [
            "competitor_status" => $competitor_status[$com_key],
            "reason"            => $competitor_reason[$com_key],
            "position"          => "",
            "total"             => 0
        ];

        // if (array_key_exists($com_key,$competitor_position)) {
        //     $update_columns['position'] = $competitor_position[$com_key];
        //     $update_columns['total']    = $_POST['bidders_total_'.$competitor_id[$com_key]];
        // }

        if (isset($_POST['bidders_total_'.$competitor_id[$com_key]])) {
            $update_columns['position'] = $_POST['competitor_position_'.$competitor_id[$com_key]];
            $update_columns['total']    = $_POST['bidders_total_'.$competitor_id[$com_key]];
        }

        $update_table_details = [
            "price_bid_competitor",
            $update_columns
        ];

        $update_obj     = $pdo->update("price_bid_competitor",$update_columns,$update_where);

        if (!($update_obj->status)) {
            print_r($update_obj);
            return false;
        }
    }

    return true;
}

function price_bid_products_insert_update ($unique_id = "") {

    if ($unique_id) {

        global $pdo;
        
        $table                  = "price_bid_competitor_rates";

        $product_details        = json_decode($_POST['product_details']);

        // print_r($product_details);

        foreach ($product_details as $pro_key => $pro_value) {
            
            $update_where   = [
                "price_bid_id"  => $unique_id,
                "competitor_id" => $pro_value->bidder,
                "item_id"       => $pro_value->product,
            ];

            $columns        = [
                "rate"      => $pro_value->value
            ];

            $update_obj     = $pdo->update($table,$columns,$update_where);

            if (!($update_obj->status)) {
                print_r($update_obj);
                return false;
            }
        }
    }
    return true;
}


function price_bid_products ($unique_id) {
    $return_result = [];

    if ($unique_id) {

        global $pdo;

        // Get Price Bid ID based on bid Unique ID
        $tender_sql     = "SELECT  unique_id FROM price_bid WHERE bid_unique_id = '".$unique_id."' ";

        $select_result  = $pdo->query($tender_sql);

        // print_r($select_result);
        if ($select_result->status && !empty($select_result->data)) {
            
            $unique_id = $select_result->data[0]['unique_id'];
            // return false;
        } else {
            return false;
        }

        $table                  = "price_bid_competitor_rates";

        $where                  = [
            "is_delete"      => 0,
            "price_bid_id"   => $unique_id
        ];

        $product_details        = [];

        $columns                = [
            "competitor_id",      
            "item_id",
            "competitor_product_name", 
            "rate"           
        ];

        $table_details          = [
            $table,
            $columns
        ];

        $select_result          = $pdo->select($table_details,$where);

        if (!($select_result->status)) {

            print_r($select_result);

        } else {
            $select_result      = $select_result->data;

            // print_r($select_result);

            if (!(empty($select_result))) {
                
                // $product_details        = [];

                // foreach ($select_result as $select_key => $select_value) {

                //     $competitor                = $select_value['competitor_id'];
                //     $product                   = $select_value['item_id'];
                //     $key_val                   = $competitor."_".$product;

                //     $product_details[$key_val] = $select_value['rate'];
                //     $product_details["product_name"] = $select_value['rate'];

                // }

                $return_result  = $select_result;
            }
        }
    }

    return json_encode($return_result);
}
?>