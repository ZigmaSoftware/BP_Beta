<!-- This file Only PHP Functions -->

<script>
    var sublist = "";
</script>
<?php 
// // Form variables
$btn_text               = "Save";
$btn_action             = "create";
$is_btn_disable         = "";

// $unique_id              = "";
// $table_sub              = "";
// $sub_counter            = 1;
// $sublist_data           = "";
// $table                  = "";
// $table_sub              = "";


// $supplier_id                     = "";
// $branch_id                       = "";
// $purchase_request_no             = "";
// $entry_date                      = "";
// $purchase_order_no               = "";
// $purchase_type                   = "";
// $net_amount                      = "";
// $freight_percentage              = "";
// $freight_amount                  = "";
// $other_charges                   = "";
// $other_tax                       = "";
// $other_charges_percentage  = "";
// $tcs_percentage                  = "";
// $tcs_amount                      = "";
// $round_off                       = "";
// $gross_amount                    = "";
// $contact_person                  = "";
// $quote_no                        = "";
// $quote_date                      = "";
// $delivery                        = "";
// $ship_via                        = "";
// $delivery_term_fright            = "";
// $delivery_site                   = "";
// $payment_days                    = "";
// $dealer_reference                = "";
// $document_throught               = "";
// $approve_type                    = "";
// $billing_address                 = "";
// $billing_information             = "";
// $approve_status                  = "";




// if(isset($_GET["unique_id"])) {
//     if (!empty($_GET["unique_id"])) {

//         $unique_id  = $_GET["unique_id"];
//         $where      = [
//             "unique_id" => $unique_id
//         ];

//         $table      =  "purchase_order";
//         $table_sub  =  "purchase_order_sub";


//         $columns    = [

//             "purchase_order_no",
//             "entry_date",
//             "supplier_id",
//             "branch_id",
//             "purchase_type",
//             "purchase_request_no",
//             "net_amount",
//             "freight_percentage",
//             "other_charges",
//             "other_tax",
//             "other_charges_percentage",
//             "tcs_percentage",
//             "tcs_amount",
//             "round_off",
//             "gross_amount",
//             "contact_person",
//             "quote_no",
//             "quote_date",
//             "delivery",
//             "ship_via",
//             "delivery_term_fright",
//             "delivery_site",
//             "payment_days",
//             "dealer_reference",
//             "document_throught",
//             "billing_address",
//             "billing_information",
//             "approve_status"

//         ];

//         $table_details   = [
//             $table,
//             $columns
//         ]; 

//         $result_values  = $pdo->select($table_details,$where);

//         if ($result_values->status) {

//             $result_values                    = $result_values->data[0];

//             $purchase_order_no                = $result_values["purchase_order_no"];
//             $today                            = $result_values["entry_date"];
//             $supplier_id                      = $result_values["supplier_id"];
//             $branch_id                        = $result_values["branch_id"];
//             $purchase_type                    = $result_values["purchase_type"];
//             $purchase_request_no              = $result_values["purchase_request_no"];
//             $net_amount                       = $result_values["net_amount"];
//             $freight_percentage               = $result_values["freight_percentage"];
//             $freight_amount                   = $result_values["freight_amount"];
//             $other_charges                    = $result_values["other_charges"];
//             $other_tax                        = $result_values["other_tax"];
//             $other_charges_percentage   = $result_values["other_charges_percentage"];
//             $tcs_percentage                   = $result_values["tcs_percentage"];
//             $tcs_amount                       = $result_values["tcs_amount"];
//             $round_off                        = $result_values["round_off"];
//             $gross_amount                     = $result_values["gross_amount"];
//             $contact_person                   = $result_values["contact_person"];
//             $quote_no                         = $result_values["quote_no"];
//             $quote_date                       = $result_values["quote_date"];
//             $delivery                         = $result_values["delivery"];
//             $ship_via                         = $result_values["ship_via"];
//             $delivery_term_fright             = $result_values["delivery_term_fright"];
//             $delivery_site                    = $result_values["delivery_site"];
//             $payment_days                     = $result_values["payment_days"];
//             $dealer_reference                 = $result_values["dealer_reference"];
//             $document_throught                = $result_values["document_throught"];
//             $approve_status                   = $result_values["approve_status"];
//             $billing_address                  = $result_values["billing_address"];
//             $billing_information              = $result_values["billing_information"];
            

//             $purchase_date                    = disdate($today);
//             $quote_date                       = disdate($quote_date);

            

//             $btn_text                         = "Update";
//             $btn_action                       = "update";


//             //Get Sublist Data
//             $sublist_result       = sub_list($unique_id,$table_sub,"testing_main_unique_id");
            
//             // print_r($sublist_result);

//             if ($sublist_result->status) {

//               $sublist_data = $sublist_result->data;

//             } else {

//             }

        
//         } else {
//             $btn_text                         = "Error";
//             $btn_action                       = "error";
//             $is_btn_disable                   = "disabled='disabled'";

//         }
//     }
// } 

// //Supplier Name
// $supplier_name_options     = supplier();
// $supplier_name_options     = select_option($supplier_name_options,"Select the Supplier Name",$supplier_id);


// //Branch Name
// $branch_name_options     = branch();
// $branch_name_options     = select_option($branch_name_options,"Select the Branch Name",$branch_id);


// // Purchase Type
// $purchase_type_options = [
//     1 => [
//         "unique_id" => "1",
//         "value"     => "Normal Purchase"
//     ],
//     2 => [
//         "unique_id" => "2",
//         "value"     => "Capital Purchase"
//     ]
// ];


// $purchase_type_options    = select_option($purchase_type_options,"Select Purchase Type",$purchase_type); 


// // Approve Option
// $approve_status_options = [
//     1 => [
//         "unique_id" => "1",
//         "value"     => "Not Completed"
//     ],
//     2 => [
//         "unique_id" => "2",
//         "value"     => "Completed"
//     ]
// ];


// $approve_status_options    = select_option($approve_status_options,"Select the Status",$approve_status); 


// //Other Tax Percentage
// $other_tax_options         = tax();
// $other_tax_options         = select_option($other_tax_options,"Select the Tax",$other_tax);


// //Billing Address
// $billing_address_options     = branch();
// $billing_address_options     = select_option($billing_address_options,"Select the Billing Address",$billing_address);


// //Delivery Type
// $delivery_type_options       = delivery_type();
// $delivery_type_options       = select_option($delivery_type_options,"Select the Delivery",$delivery);

// //Delivery Via Type
// $delivery_via_type_options   = delivery_via_type();
// $delivery_via_type_options   = select_option($delivery_via_type_options,"Select the Ship Via",$ship_via);

?>

<style>
.top{ border-top: 1px solid #000000;}
.bottom{ border-bottom: 1px solid #000000;}
.left{ border-left: 1px solid #000000;}
.right{ border-right: 1px solid #000000;}
</style>

<input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id ?>">
<input type="hidden" name="sub_counter" id="sub_counter" value="1">
<input type="hidden" name="sublist_data" id="sublist_data" value=''>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form class="was-validated" id="purchase_order_form">
          <div class="form-group row">
            <label class="col-md-2 col-form-label">Supplier Name</label>
            <div class="col-md-4">
              <select name="supplier_id" id="supplier_id" class="select2 form-control" required>
                <?= $supplier_name_options ?>
              </select>
            </div>
            <label class="col-md-2 col-form-label">Purchase Order No</label>
            <div class="col-md-4">
              <input type="text" name="purchase_order_no" id="purchase_order_no" class="form-control" value="<?= $purchase_order_no ?>" readonly>
            </div>
          </div>

          <div class="form-group row">
            <label class="col-md-2 col-form-label">Branch</label>
            <div class="col-md-4">
              <select name="branch_id" id="branch_id" class="select2 form-control" required>
                <?= $branch_name_options ?>
              </select>
            </div>
            <label class="col-md-2 col-form-label">Entry Date</label>
            <div class="col-md-4">
              <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?= $entry_date ?>">
            </div>
          </div>

                   <div class="col-12">
  <table id="purchase_order_sub_datatable" class="table dt-responsive nowrap w-100">
    <thead>
      <tr>
        <th>#</th>
        <th>Product Name</th>
        <th>UOM</th>
        <th>Qty</th>
        <th>Rate</th>
        <th>Discount(%)</th>
        <th>Tax</th>
        <th>Amount</th>
        <th>Action</th>
      </tr>
      <tr id="po_details_form">
        <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="">
        <th>#</th>
        <th>
          <select id="item_code" name="item_code" class="form-control select2" required>
            <?= select_option(item_name_list(), "Select Item", "") ?>
          </select>
        </th>
        <th>
          <input type="text" id="uom" name="uom" class="form-control" readonly placeholder="UOM">
        </th>
        <th>
          <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Qty" step="0.01" required>
        </th>
        <th>
          <input type="number" id="rate" name="rate" class="form-control" placeholder="Rate" step="0.01" required>
        </th>
        <th>
          <input type="number" id="discount" name="discount" class="form-control" placeholder="%" step="0.01" required>
        </th>
        <th>
          <select id="tax" name="tax" class="form-control select2" required>
            <?= select_option(tax(), "Select Tax", "") ?>
          </select>
        </th>
        <th>
          <input type="text" id="amount" name="amount" class="form-control" readonly placeholder="Amount">
        </th>
        <th>
          <button type="button" class="btn btn-success po_sublist_add_btn" onclick="po_sublist_add_update()">Add</button>
        </th>
      </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
      <tr>
        <th colspan="3" class="text-right">Total:</th>
        <th>
          <input type="text" id="total_quantity" name="total_quantity" class="form-control" readonly value="0">
        </th>
        <th colspan="3"></th>
        <th>
          <input type="text" id="total_sub_amount" name="total_sub_amount" class="form-control" readonly value="0.00">
        </th>
        <th></th>
      </tr>
    </tfoot>
  </table>
</div>



          <form class="was-validated form_second" name = "form_second">
                       
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <table width="100%"  border="0" >
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;width: 70%"><strong>Net Amount</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"> <input type="text" name="net_amount" readonly class="form-control"  id="net_amount" style="text-align:right;width: 200px;" value="<?php echo $net_amount; ?>"></td> 
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Freight Value (%)</strong></td>
                                        <td>&nbsp;</td>
                                        <td width="20%" align="center"><input type="text" name="freight_percentage"  class="form-control"   id="freight_percentage" style="text-align:right; width: 100px;" onkeyup="total_amount_calculation()"  value="<?php echo $freight_percentage; ?>"></td>
                                        <td height="30" width="20%" align="right"><input type="text" name="freight_amount"  class="form-control"  readonly id="freight_amount" style="text-align:right;width: 200px;" value="<?php echo $freight_amount; ?>"></td> 
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Other Charge</strong></td>
                                        <td width="20%" align="right"><input type="text" name="other_charges"  class="form-control"   id="other_charges" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?php echo $other_charges; ?>"></td>
                                        <td width="20%" align="center">
                                        <select name="other_tax"  id="other_tax" class="form-control select2" onchange="total_amount_calculation()"  >
                                            <?=$other_tax_options;?>
                                        </select>
                                        </td>
                                        <td height="30" width="20%" align="right"><input type="text" name="other_charges_percentage"  class="form-control"  readonly id="other_charges_percentage" style="text-align:right;width: 200px;" value="<?php echo $other_charges_percentage; ?>"></td> 
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>TCS(%)</strong></td>
                                        <td>&nbsp;</td>
                                        <td width="20%" align="center"><input type="text" name="tcs_percentage"  class="form-control" onkeyup="total_amount_calculation()"  id="tcs_percentage" style="text-align:right; width: 100px;"  value="<?php echo $tcs_percentage; ?>"></td>
                                        <td height="30" width="20%" align="right"><input type="text" name="tcs_amount"  class="form-control"  readonly id="tcs_amount" style="text-align:right;width: 200px;" value="<?php echo $tcs_amount; ?>"></td>
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Round off</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"><input type="text" name="round_off" onkeyup="total_amount_calculation()"  class="form-control"  id="round_off" style="text-align:right"  value="<?php  echo $round_off; ?>"></td>      
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Gross Amount</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"><input type="text" readonly name="gross_amount"   class="form-control"   id="gross_amount" style="text-align:right" value="<?php echo $gross_amount; ?>"></td>                                   
                                    </tr>
                                </table>  
                            </div>                                
                        </div>
                        <div class="top bottom left right "><br>
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Supply and Delivery  Terms :</strong></label>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Contact Person:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="contact_person" id="contact_person" class="form-control" value="<?php echo $contact_person; ?>" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Quote No:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="quote_no" id="quote_no" class="form-control" value="<?php echo $quote_no; ?>" required>
                                    </div>
                                    <div class="col-md-1">
                                        <label class=" form-control-label">&nbsp;<strong>Quote Date:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="quote_date" id="quote_date" value="<?php echo $quote_date;?>" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Delivery:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="delivery" id="delivery" class="select2 form-control" required  >
                                       <?=$delivery_type_options;?>                               
                                       </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Ship Via:</strong>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                         <select name="ship_via" id="ship_via" class="select2 form-control" required  >
                                       <?=$delivery_via_type_options;?>                               
                                       </select>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Freight:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="delivery_term_fright" id="delivery_term_fright" class="form-control" value="<?php echo $delivery_term_fright; ?>" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Delivery Site:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="delivery_site" id="delivery_site" class="form-control" value="<?php echo $delivery_site; ?>" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Payment Days:</strong></label>
                                    </div>
                                    <div class="col-md-2">  
                                        <input type="text" name="payment_days" id="payment_days" onKeyPress="if((event.keyCode &lt; 46)||(event.keyCode &gt; 57)) event.returnValue = false;" class="form-control" value="<?php echo $payment_days; ?>" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong> Document Through</strong></label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                           <input type="radio" id="document_throught" name="document_throught" value="Bank" <?php if (isset($document_throught) && $document_throught=="Bank") echo "checked";?> >Bank &nbsp;&nbsp;
                                            <input type="radio" name="document_throught" id="document_throught"   value="Direct" <?php if (isset($document_throught) && $document_throught=="Direct") echo "checked";?> />Direct
                                        </div>&nbsp;&nbsp;
                                       
                                    </div>
                                <div class="row form-group" id="dealer_id">
                                    <div class="col-md-2" >
                                        <label class=" form-control-label">&nbsp;<strong>Dealer Reference:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="dealer_reference" id="dealer_reference" class="form-control" value="<?php echo $dealer_reference; ?>" required>
                                    </div>
                                </div>
                                <div class="row form-group" id="dealer_id">
                                    <div class="col-md-2" >
                                        <label class=" form-control-label">&nbsp;<strong>Billing Address:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="billing_address" id="billing_address" class="select2 form-control" required onchange="get_billing_address(this.value)" >
                                       <?=$billing_address_options;?>                                   
                                       </select>
                                    </div>
                                </div>

                                <div class="row form-group" id="dealer_id">
                                    <div class="col-md-2" >
                                        <label class=" form-control-label">&nbsp;<strong>Billing Information:</strong></label>
                                    </div>
                                    <div class="col-md-4">
                                        <label class=" form-control-label" id="billing_info" name="billing_info">&nbsp;<strong></strong></label>
                                        <input type="hidden" name="billing_information" id="billing_information" value="<?php echo $billing_information; ?>">
                                    </div>
                                </div>
                               
                            </div>                                
                        </div>
                        </div>
                        <br> 
                        <div class="row form-group">
                            <label class="col-md-2 col-form-label" for="approve_status"> Approve Status </label>
                            <div class="col-md-2">
                                <select name="approve_status" id="approve_status" class="select2 form-control" required >
                                <?=$approve_status_options;?>                              
                           </select>
                                
                            </div>
                        </div>
                    </form>

          <!-- Buttons -->
          <div class="form-group row">
            <div class="col-md-12">
              <?= btn_cancel("cancel") ?>
              <?= btn_createupdate("purchase_order", $screen_unique_id, $btn_text) ?>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
