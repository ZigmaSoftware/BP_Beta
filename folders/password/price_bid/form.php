<!-- This file Only PHP Functions -->
<?php include 'function.php';?>
<?php

// echo $file_name_org;

$print_status      = 0;

$btn_text          = "Save";
$btn_action        = "create";
$d_none            = "";
$is_btn_disable    = "";
$no_of_bidders     = '';

$unique_id         = "";

$bid_no            = "";
$customer_id       = "";
$bids_tender_type  = "";
$call_no           = "";

$customer_name     = "";
$bid_date          = "";
$is_updated        = 0;

$no_of_bidders     = "";
$bidder_names      = "";

$competitor_status = 1;
$price_bid_date    = $today;

$l1_competitor     = "";
$tender_status     = 1;

$product_details   = "";
$price_difference  = 0.00;

$l1_bidder_name_options = "";

$font_weight_light = "";

$bidders_positions = "";

if(isset($_GET["unique_id"])) {

    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "price_bid";

        $columns    = [
            "bid_no",
            "bid_date",
            "customer_id",
            "tender_status",
            "call_no",
            "is_updated",
            "l1_bidder_id",
            "price_difference",
            "price_bid_date"
            // "no_of_bidders",
            // "bidders_names"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $select_result  = $pdo->select($table_details,$where);

        if ($select_result->status) {

            $select_result      = $select_result->data[0];

            $bid_no             = $select_result['bid_no'];
            $bid_date           = $select_result['bid_date'];
            $customer_id        = $select_result['customer_id'];
            $tender_status      = $select_result['tender_status'];
            $call_no            = $select_result['call_no'];
            $is_updated         = $select_result['is_updated'];
            $l1_competitor      = $select_result['l1_bidder_id'];
            $price_difference   = $select_result['price_difference'];
            $price_bid_date     = $select_result['price_bid_date'];
            // $no_of_bidders      = $select_result['no_of_bidders'];
            // $bidder_names       = $select_result['bidders_names'];

            $is_updated = 1;

            if ($is_updated) {

                // Select bidder Details

                $select_bid_where   = [
                    "price_bid_id"  => $unique_id,
                    "is_delete"     => 0
                ];

                $columns_select     = [
                    "competitor_id",
                    "competitor_status",
                    "reason",
                    "total",
                    "position"
                ];

                $table_price_bid    = [
                    "price_bid_competitor",
                    $columns_select
                ];

                $select_bidders     = $pdo->select($table_price_bid,$select_bid_where);
                
                if (!($select_bidders->status)) {

                    print_r($select_bidders);

                } else {

                    $competitor_list  = $select_bidders->data;
                    $bidder_names   = "";

                    
                    foreach ($competitor_list as $com_key => $com_value) {
                        
                        $bidder_names .= $com_value['competitor_id'].",";

                        // $bidders_position[] = [
                        //     // "id"          => $com_value['competitor_id'],
                        //     $com_value['competitor_id']    => $com_value['position'],
                        // ];
                        $bidders_position[$com_value['competitor_id']] =  $com_value['position'];
                        ;
                    }

                    $bidders_position  = json_encode($bidders_position);
                }

                $bidder_names       = explode(",",$bidder_names);
                $bidder_names       = "'".implode("','",$bidder_names)."'";

                $table_competitor   = "competitor_profile";

                $competitor_columns = [
                    "unique_id",
                    "competitor_name"
                ];

                $competitor_where   = " unique_id IN ($bidder_names) ";

                $table_competitor_details    = [
                    $table_competitor,
                    $competitor_columns
                ];

                $bidder_select    = $pdo->select($table_competitor_details,$competitor_where);

                if (!($bidder_select->status)) {

                    print_r($bidder_select);

                } else {
                    
                    $bidder_details = $bidder_select->data;

                    $competitor_list[0]['competitor_name'] = "Ascent e-Digit Solutions";

                    foreach ($bidder_details as $bid_key => $bid_value) {

                        $bidder_id      = $bid_value["unique_id"];
                        $bidder_name    = $bid_value["competitor_name"];

                        for ($i=1; $i < count($competitor_list); $i++) { 
                            if ($competitor_list[$i]['competitor_id'] == $bidder_id) {
                                $competitor_list[$i]['competitor_name'] = $bidder_name;
                            }
                        }                        
                    }

                    // print_r($competitor_list);

                    $bidder_names   = json_encode($competitor_list);
                }

                // Get Product Details
                
                if ($file_name_org == 'print') {
                    $print_status   = 1;

                    $d_none         = " d-none ";

                    $font_weight_light = " font-weight-light ";

                }
            }
            
            $product_details    = price_bid_products($unique_id);

            // Customer Details
            $customer_details   = customers($customer_id);

            $customer_name      = $customer_details[0]['customer_name'];
            $customer_city      = $customer_details[0]['city_unique_id'];
            $customer_state     = $customer_details[0]['state_unique_id'];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

// Close Call Status Start
$competitor_status_options = [
    0 => [
        "unique_id" => 1,
        "value" => "Accepted"
    ],
    1 => [
        "unique_id" => 0,
        "value" => "Rejected"
    ]
];

$competitor_status_options   = select_option($competitor_status_options,"Select Competitor Status",$competitor_status); 
// Close Call Status End

// Tender Status Start
$tender_status_options = [
    0 => [
        "unique_id" => 1,
        "value" => "Won"
    ],
    1 => [
        "unique_id" => 0,
        "value" => "Loss"
    ]
];

// print_r($competitor_list);

$tender_status_options   = select_option($tender_status_options,"Select Tender Status",$tender_status); 
// Tender Status End
// var_dump($tender_status);

// $tender_loss_div = " d-none ";
// if ($tender_status == 0) {
//     $tender_loss_div = "";
// }
$l1_competitor_options  = [];

foreach ($competitor_list as $compe_key => $compe_value) {

    if ($compe_value['competitor_status'] && $compe_value['competitor_id'] != "default") {

        $l1_competitor_options[] = [
            "id"    => $compe_value['competitor_id'],
            "value" => $compe_value['competitor_name']
        ];
    }
}

$l1_competitor_arr       = $l1_competitor_options;
$l1_competitor_options   = select_option($l1_competitor_options,"Select L1 Competitor",$l1_competitor);


if ($print_status) {

    foreach ($l1_competitor_arr as $l1_com_key => $l1_com_value) {
        
        if ($l1_com_value['id'] == $l1_competitor) {

            $l1_competitor = $l1_com_value['value'];

        }
    }
?>
<style>
.form-group {
    margin-bottom: -0.5rem !important;
}
</style>
<?php } ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated price_bid_form" name="price_bid_form" id="price_bid_form">


                    <input type="hidden" name="unique_id" id="unique_id" value="<?php echo $unique_id; ?>">
                    <input type="hidden" name="bid_no" id="bid_no" value="<?php echo $bid_no; ?>">
                    <input type="hidden" name="bid_date" id="bid_date" value="<?php echo $bid_date; ?>">
                    <input type="hidden" name="price_bid_is_updated" id="price_bid_is_updated" value="<?php echo $is_updated; ?>">
                    <input type="hidden" name="price_bid_bidder_names" id="price_bid_bidder_names" value='<?php echo $bidder_names; ?>'>
                    <input type="hidden" name="price_bid_bidder_position" id="price_bid_bidder_position" value='<?php echo $bidders_position; ?>'>
                    <input type="hidden" name="price_bid_product_details" id="price_bid_product_details" value='<?php echo $product_details; ?>'>
                    <input type="hidden" name="print_status" id="print_status" value='<?php echo $print_status; ?>'>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="bid_id"> Bid No</label>
                                <label class="col-md-4 text-green  mt-1" for="bid_no"> <?php echo $bid_no; ?> </label>
                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="customer_name"> Customer Name</label>
                                <label class="col-md-4 text-green  mt-1" for="customer"> <?php echo $customer_name; ?> </label>
                            </div>
                            <div class="form-group row ">
                                <!-- <label class="col-md-2 col-form-label" for="bid_type"> Bid Type</label>
                                <label class="col-md-4 text-green  mt-1" for="bid_type"> <?php echo $bids_tender_type; ?></label> -->
                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="bid_date"> Bid Creation Date</label>
                                <label class="col-md-4 text-green  mt-1" for="bid_create_date"> <?php echo disdate($bid_date); ?> </label>
                            <!-- </div>
                            <div class="form-group row "> -->
                                <!-- <label class="col-md-2 col-form-label" for="bid_submission_date"> Bid Submission Date</label>
                                <div class="col-md-4">
                                    <input type="date" name="bid_submission_date" id="bid_submission_date" class="form-control" value="<?php echo $today; ?>" max="<?=$today;?>" required>
                                </div> -->
                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="price_bid_opening_date"> Price Bid Opening Date</label>
                                <div class="col-md-4 <?php echo $d_none; ?>">
                                    <input type="date" name="price_bid_opening_date" id="price_bid_opening_date" class="form-control" value="<?php echo $price_bid_date; ?>" max="<?=$today;?>" required>
                                </div>

                                <!-- Print Only View -->
                                <?php if ($print_status) { ?>
                                    
                                    <label class="col-md-4 text-green  mt-1" for="bid_create_date"> <?php echo disdate($price_bid_date); ?> </label>
                                    
                                <?php } ?>


                            </div>
                            <!-- <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="bid_submission_date"> Ascent e-Digit Solutions </label>
                                <div class="col-md-4">
                                   <select name="competitor_status" id="competitor_status" class="select2 form-control" required>
                                       <?php echo $competitor_status_options; ?>
                                   </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="bidder_reason"> Reason </label>
                                <div class="col-md-4">
                                    <textarea name="bidder_reason" plaseholid="bidder_reason" class="form-control" rows="5"></textarea>
                                </div>
                            </div> -->

                            <!-- Don't Delete Or Modify below Line -->
                            <span class="price_bid_competitor_list">
                            </span>
                              
                            <table id="price_bid_product" class="table w-100 nowrap">
                            </table>

                            <div class="form-group row">
                                <!-- <div class="col-md-12"> -->
                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="tender_status">Tenter Status</label>
                                <div class="col-md-4 <?php echo $d_none; ?>">
                                    <select name="tender_status" id="tender_status" onchange="price_bid_tender_change_status(this.value)" class="select2 form-control">
                                        <?php echo $tender_status_options;?>
                                    </select>
                                </div>

                                <!-- Print Only View -->
                                <?php if ($print_status) { ?>
                                    
                                    <label class="col-md-4 mt-1" for="bid_create_date"> <?php echo  ($tender_status) ? "Won" : "Loss"; ?> </label>
                                    
                                <?php } ?>

                            </div>
                            <div class="form-group row tender_loss_ui <?php echo $tender_loss_div; ?>">

                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="won_bidder_name">L1 Bidder Name</label>
                                <div class="col-md-4 <?php echo $d_none; ?>">
                                    <select name="won_bidder_name" id="won_bidder_name" class="select2 form-control tender_loss_inp">
                                        <?php echo $l1_competitor_options;?>
                                    </select>
                                </div>

                                <!-- Print Only View -->
                                <?php if ($print_status) { ?>
                                    <label class="col-md-2 col-form-label" for="won_bidder_name"><?php echo $l1_competitor; ?></label>
                                <?php } ?>
                                
                                <label class="col-md-2 col-form-label <?php echo $font_weight_light; ?>" for="price_difference">Price Difference</label>
                                <div class="col-md-4 <?php echo $d_none; ?>">
                                    <input type="text" name="price_difference" id="price_difference" class="form-control tender_loss_inp" value="<?php echo $price_difference; ?>" >
                                </div>

                                <!-- Print Only View -->
                                <?php if ($print_status) { ?>
                                    
                                    <label class="col-md-4 mt-1" for="bid_create_date"> <?php echo  moneyFormatIndia($price_difference); ?> </label>
                                    
                                <?php } ?>

                            </div>


                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <?php echo btn_cancel($btn_cancel);?>

                                    <?php if ($print_status == "0") { ?>

                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>

                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div><!-- end card-body -->
        </div><!-- end card -->
    </div><!-- end col -->
</div>