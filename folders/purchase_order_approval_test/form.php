<script>
    var sublist = "";
</script>
<?php
$btn_text         = "Save";
$btn_action       = "create";
$is_btn_disable   = "";
$company_name     = "";
$entry_date  = date('Y-m-d');
$screen_unique_id = "";
$disabled         = "";
$readonly         = "";
$unique_id        = isset($_GET["unique_id"]) ? $_GET["unique_id"] : "";
if (!empty($unique_id)) {
    $disabled         = "disabled";
    $readonly         = "readonly";
    $where = ["unique_id" => $unique_id];
    $table = "purchase_order";
    $columns = [
        "screen_unique_id",
        "company_id",
        "project_id",
        "purchase_order_no",
        "entry_date",
        "supplier_id",
        "branch_id",
        "purchase_type",
        "purchase_request_no",
        "net_amount",
        "freight_amount",
        "other_charges",
        "other_tax",
        "other_charges_percentage",
        "tcs_percentage",
        "tcs_amount",
        "round_off",
        "gross_amount",
        // "contact_person",
        // "quote_no",
        // "quote_date",
        "delivery",
        // "ship_via",
        // "delivery_term_fright",
        // "delivery_site",
        "payment_days",
        // "dealer_reference",
        // "document_throught",
        "billing_address",
        "shipping_address",
        "remarks",
        "packing_forwarding",
        "freight_value",
        "freight_tax",
        "packing_forwarding_tax",
        "packing_forwarding_amount",
        "total_gst_amount",
    ];
    $result = $pdo->select([$table, $columns], $where);
    if ($result->status) {
        $data = $result->data[0];
        // Assign variables individually for clarity
 // Assign variables individually for clarity
        $screen_unique_id            = $data["screen_unique_id"];
        $company_id                  = $data["company_id"];
        $project_id                  = $data["project_id"];
        $purchase_order_no           = $data["purchase_order_no"];
        $entry_date                  = $data["entry_date"];
        $supplier_id                 = $data["supplier_id"];
        $branch_id                   = $data["branch_id"];
        $purchase_type               = $data["purchase_type"];
        $purchase_request_no         = $data["purchase_request_no"];
        $net_amount                  = $data["net_amount"];
        $freight_value             = $data["freight_value"];
        $freight_tax               = $data["freight_tax"];
        $packing_forwarding_tax    = $data["packing_forwarding_tax"];
        $packing_forwarding_amount = $data["packing_forwarding_amount"];
        // $freight_percentage          = $data["freight_percentage"];
        $freight_amount              = $data["freight_amount"];
        $other_charges               = $data["other_charges"];
        $other_tax                   = $data["other_tax"];
        $other_charges_percentage    = $data["other_charges_percentage"];
        $tcs_percentage              = $data["tcs_percentage"];
        $tcs_amount                  = $data["tcs_amount"];
        $round_off                   = $data["round_off"];
        $gross_amount                = $data["gross_amount"];
        $contact_person              = $data["contact_person"];
        $quote_no                    = $data["quote_no"];
        $quote_date                  = disdate($data["quote_date"]);
        $delivery                    = $data["delivery"];
        $ship_via                    = $data["ship_via"];
        $delivery_term_fright        = $data["delivery_term_fright"];
        $delivery_site               = $data["delivery_site"];
        $payment_days                = $data["payment_days"];
        $dealer_reference            = $data["dealer_reference"];
        $document_throught           = $data["document_throught"];
        $billing_address             = $data["billing_address"];
        $shipping_address             = $data["shipping_address"];
        $remarks                    = $data["remarks"];
        $packing_forwarding          = $data["packing_forwarding"];
        $total_gst_amount            = $data["total_gst_amount"];
        $btn_text                    = "Update";
        $btn_action                  = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
} else {
    // For new record
    $screen_unique_id = unique_id();
}
$company_name_options           = company_name();
$company_name_options           = select_option($company_name_options, "Select the Company", $company_id);

$project_options                = get_project_name();
$project_options                = select_option($project_options, "Select the Project Name", $project_id);
// //Supplier Name
$tax_options                    = tax();
$tax_options                    = select_option($tax_options, "Select Tax", $tax);
// Product Name
$product_unique_id              = product_name();
$product_unique_id              = select_option($product_unique_id, "Select", $group_unique_id);
// Unit Name
$uom_unique_id                  = unit_name();
$uom_unique_id                  = select_option($uom_unique_id, "Select", $uom);

$supplier_name_options          = supplier();
$supplier_name_options          = select_option($supplier_name_options, "Select", $supplier_id);

$pr_number_options              = get_pr_number();
$pr_number_options              = select_option($pr_number_options, "Select the Project Name");


$po_approval_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Approve"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Reject" 
    ],
    // 3 => [
    //     "unique_id" => "3",
    //     "value"     => "Cancel"  // ✅ New Option
    // ]
];

$po_approval_options              = select_option($po_approval_options, "Select");

$po_type_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Regular"
    ],
    2 => [
        "unique_id" => "683568ca2fe8263239",
        "value"     => "Service"
    ],
    3 => [
        "unique_id" => "683588840086c13657",
        "value"     => "Capital"
    ]
];

$po_type_options    = select_option($po_type_options,"Select",$po_type);

$freight_tax_options            = select_option(tax(), "Select Tax", $freight_tax);
$packing_forwarding_tax_options = select_option(tax(), "Select Tax", $packing_forwarding_tax);
$other_tax_options              = select_option(tax(), "Select Tax", $other_tax);
?>

<!--<input type="text" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id ?>">-->
<!--<input type="hidden" name="unique_id" id="unique_id" value="<?= $unique_id ?>">-->
<!--<input type="hidden" name="sub_counter" id="sub_counter" value="1">-->
<!--<input type="hidden" name="sublist_data" id="sublist_data" value=''>-->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
        <form class="was-validated" id="purchase_order_form">
            <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id ?>">
            <input type="hidden" name="unique_id" id="unique_id" value="<?= $unique_id ?>">
            <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="">

            <input type="hidden" name="sub_counter" id="sub_counter" value="1"> 
            <input type="hidden" name="sublist_data" id="sublist_data" value=''> 

            <div class="form-group row">
                <label class="col-md-2 col-form-label labelright">Company Name</label>
                <div class="col-md-3">
                    <input type="hidden" name="company_id" value="<?= $company_id ?>">
                    <select name="company_id" id="company_id"  class="form-control select2"  onchange="get_project_name(this.value);" required disabled>
                        <?= $company_name_options ?>
                    </select>
                </div>
                <label class="col-md-2 col-form-label labelright">Project Name</label>
                <div class="col-md-3">
                    <input type="hidden" name="project_id" value="<?= $project_id ?>">
                    <select name="project_id" id="project_id" class="form-control select2" required disabled>
                        <?= $project_options ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-form-label labelright">PO Type</label>
                <div class="col-md-3">
                             <input type="hidden" name="po_for" value="<?= $po_for; ?>">
                            <select name="po_for" id="po_for"  class="form-control select2" required disabled>
                                <?= $po_type_options; ?>
                            </select>
                </div>
              <!--<label class="col-md-2 col-form-label" for="pr_number">PR Number</label>-->
              <div class="col-md-2">
                <div class="input-group">
                  
                </div>
              
              <!-- Button to Open Modal -->
<!--<button type="button" class="btn btn-primary mt-2 mb-2" onclick="show_pr_sublist()">-->
<!--  +-->
<!--</button>-->
<!-- PR Sublist Modal -->
<!-- Modal -->
<div class="modal fade" id="pr_plus_btn" tabindex="-1" aria-labelledby="prSublistModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
    
      <div class="modal-header">
           <h5 class="modal-title" id="prSublistModalLabel">Purchase Requisition Sublist</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      
      <div class="modal-body" id="pr_sublist_content">
        <!-- Sublist data will be loaded here -->
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        
      </div>
      
    </div>
  </div>
</div>
           </div>   
            </div>

          <div class="form-group row">
            <label class="col-md-2 col-form-label labelright">Supplier Name</label>
            <div class="col-md-3">
                <input type="hidden" name="supplier_id" value="<?= $supplier_id; ?>">
              <select name="supplier_id" id="supplier_id" class="select2 form-control" required disabled>
                <?= $supplier_name_options ?>
              </select>
            </div>
            <?php if ($btn_action == "update") : ?>
              <label class="col-md-2 col-form-label labelright">Purchase Order No</label>
              <div class="col-md-3">
                <input type="text" name="purchase_order_no" id="purchase_order_no" class="form-control" value="<?= $purchase_order_no ?>" readonly>
              </div>
            <?php endif; ?>
          </div>

          <div class="form-group row">
            <!-- <label class="col-md-2 col-form-label">Branch</label>
            <div class="col-md-4">
              <select name="branch_id" id="branch_id" class="select2 form-control" required>
                <?= $branch_name_options ?>
              </select>
            </div> -->
            <label class="col-md-2 col-form-label labelright">Entry Date</label>
            <div class="col-md-3">
              <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?= $entry_date ?>" required readonly>
            </div>
          </div>
          
          <div class="form-group row mt-2">
          <label class="col-md-2 col-form-label labelright">GST No</label>
          <div class="col-md-3">
            <input type="text" id="gst_no" name="gst_no" class="form-control" value="<?= $gst_no ?>" readonly>
          </div>
        
          <label class="col-md-2 col-form-label labelright">PAN No</label>
          <div class="col-md-3">
            <input type="text" id="pan_no" name="pan_no" class="form-control" value="<?= $pan_no ?>" readonly>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-md-2 col-form-label labelright">MSME Type</label>
          <div class="col-md-3">
            <input type="text" id="msme_type_display" name="msme_type_display" class="form-control" value="<?= $msme_type_display ?>" readonly>
          </div>
        
          <label class="col-md-2 col-form-label labelright">MSME No</label>
          <div class="col-md-3">
            <input type="text" id="msme_no" name="msme_no" class="form-control" value="<?= $msme_no ?>" readonly>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-md-2 col-form-label labelright">Contact Person</label>
          <div class="col-md-3">
            <input type="text" id="contact_person" name="contact_person" class="form-control" value="<?= $contact_person ?>" readonly>
          </div>
        
          <label class="col-md-2 col-form-label labelright">Contact No</label>
          <div class="col-md-3">
            <input type="text" id="vendor_contact_no" name="vendor_contact_no" class="form-control" value="<?= $vendor_contact_no ?>" readonly>
          </div>
        </div>
        
        <div class="form-group row">
          <label class="col-md-2 col-form-label labelright">Quotation No</label>
          <div class="col-md-3">
            <input type="text" name="quotation_no" id="quotation_no" class="form-control" readonly value="<?= $quotation_no ?>" placeholder="Enter Quotation Number">
          </div>
        
          <label class="col-md-2 col-form-label labelright">Quotation Date</label>
          <div class="col-md-3">
            <input type="date" name="quotation_date" id="quotation_date" readonly value="<?= $quotation_date ?>" class="form-control">
          </div>
        </div>
        
        <!--<div class="form-group row">-->
        <!--  <label class="col-md-2 col-form-label">Revision No</label>-->
        <!--  <div class="col-md-4">-->
        <!--    <input type="text" name="revision_no" id="revision_no" class="form-control" value="<?= $revision_no ?>" placeholder="Enter Revision Number" readonly>-->
        <!--  </div>-->
        
        <!--  <label class="col-md-2 col-form-label">Revision Date</label>-->
        <!--  <div class="col-md-4">-->
        <!--    <input type="date" name="revision_date" id="revision_date" readonly value="<?= $revision_date ?>" class="form-control">-->
        <!--  </div>-->
        <!--</div>-->


</form>
                <div class="col-12">
                    <form id="po_sublist_form">
                        <div class="table-responsive">
                        <table id="purchase_order_sub_datatable" class="table table-striped table-bordered w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>UOM</th>
                                    <th>PO Qty</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <th>Discount Type</th> <!-- ✅ New -->
                                    <th>Discount(%)</th>
                                    <th>Tax</th>
                                    <th>Amount</th>
                                    <th>Action</th>
                                </tr>
                                    <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" >
                                <tr id="po_details_form">
                                    <th>#</th>
                                    <th>
                                        <select id="item_code" name="item_code" class="form-control select2" required <?= $disabled; ?> >
                                            <?= select_option(item_name_list(), "Select Item", "") ?>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" id="uom" name="uom" class="form-control" readonly placeholder="UOM" <?= $disabled; ?> >
                                    </th>
                                    <th>
                                        <input type="number" id="po_quantity" name="po_quantity" class="form-control" placeholder="PO Qty" required <?= $disabled; ?> >
                                    </th>
                                    <th>
                                        <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Qty" onkeyup='sub_total_amount();' step="1" required>
                                    </th>
                                    <th>
                                        <input type="number" id="rate" name="rate" class="form-control" placeholder="Rate" onkeyup='sub_total_amount();' step="1" required <?= $disabled; ?> >
                                    </th>
                                    <th>
                                      <select id="discount_type" name="discount_type" class="form-control" onchange="handleDiscountTypeChange()" <?= $disabled; ?>>
                                        <option value="3">Select Discount Type</option> <!-- Default -->
                                        <option value="2">Amount (₹)</option>
                                        <option value="1">Percentage (%)</option>
                                      </select>
                                    </th>
                                    <th>
                                        <input type="number" id="discount" name="discount" class="form-control" placeholder="%" onkeyup='sub_total_amount();' step="1" required <?= $disabled; ?> >
                                    </th>
                                    <th>
                                        <select id="tax" name="tax" class="form-control select2" onchange="get_tax_val(this.value)" required <?= $disabled; ?> >
                                            <?= $tax_options ?>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" id="amount" name="amount" class="form-control" readonly placeholder="Amount" <?= $readonly; ?> >
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
                                        <input type="text" id="total_po_quantity" name="total_po_quantity" class="form-control" readonly value="0">
                                    </th>
                                    <th>
                                        <input type="text" id="total_quantity" name="total_quantity" class="form-control" readonly value="0">
                                    </th>
                                    <th colspan="4"></th>
                                    <th>
                                        <input type="text" id="total_sub_amount" name="total_sub_amount" class="form-control" readonly value="0.00">
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                        </div>
                    </form>
                </div>
            <form class="was-validated form_second" id="form_second">

            <div class="form-group row ">
              <div class="col-md-12">
                <table width="100%" border="0">
                  <tr>
                    <td height="30" align="right" style="padding: 10px;width: 70%"><strong>Total Basic Value</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td height="30" width="20%" align="right"> <input type="text" name="net_amount" readonly class="form-control" id="net_amount" style="text-align:right;width: 200px;" value="<?php echo $net_amount; ?>"></td>
                  </tr>
                  <!-- <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Freight Value (%)</strong></td>
                    <td>&nbsp;</td>
                    <td width="20%" align="center"><input type="text" name="freight_percentage" class="form-control" id="freight_percentage" style="text-align:right; width: 100px;" onkeyup="total_amount_calculation()" value="<?php echo $freight_percentage; ?>"></td>
                    <td height="30" width="20%" align="right"><input type="text" name="freight_amount" class="form-control" readonly id="freight_amount" style="text-align:right;width: 200px;" value="<?php echo $freight_amount; ?>"></td>
                  </tr> -->
                  <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Freight Charges</strong></td>
                    <td width="20%" align="right">
                      <input type="text" name="freight_value" class="form-control" id="freight_value" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?= $freight_value?>"<?= $disabled; ?>>
                    </td>
                    <td width="20%" align="center">
                      <select name="freight_tax" id="freight_tax" class="form-control select2" onchange="total_amount_calculation()" <?= $disabled; ?>>
                        <?= $freight_tax_options  ?>
                      </select>
                    </td>
                    <td width="20%" align="right">
                      <input type="text" name="freight_amount" class="form-control" readonly id="freight_amount" style="text-align:right;width: 200px;" value="<?= $freight_amount ?>">
                    </td>
                  </tr>
                  <!-- <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Other Charge</strong></td>
                    <td width="20%" align="right"><input type="text" name="other_charges" class="form-control" id="other_charges" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?php echo $other_charges; ?>"></td>
                    <td width="20%" align="center">
                      <select name="other_tax" id="other_tax" class="form-control select2" onchange="total_amount_calculation()">
                        <?= $other_tax_options; ?>
                      </select>
                    </td>
                    <td height="30" width="20%" align="right"><input type="text" name="other_charges_percentage" class="form-control" readonly id="other_charges_percentage" style="text-align:right;width: 200px;" value="<?php echo $other_charges_percentage; ?>"></td>
                  </tr> -->
                  <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Other Charges</strong></td>
                    <td width="20%" align="right">
                      <input type="text" name="other_charges" class="form-control" id="other_charges" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?= $other_charges ?>"<?= $disabled; ?>>
                    </td>
                    <td width="20%" align="center">
                      <select name="other_tax" id="other_tax" class="form-control select2" onchange="total_amount_calculation()" <?= $disabled; ?>>
                        <?= $other_tax_options ?>
                      </select>
                    </td>
                    <td width="20%" align="right">
                      <input type="text" name="other_charges_percentage" class="form-control" readonly id="other_charges_percentage" style="text-align:right;width: 200px;" value="<?= $other_charges_percentage ?>">
                    </td>
                  </tr>
                  <!-- <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Packing & Forwarding</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td height="30" width="20%" align="right"><input type="text" name="packing_forwarding" onkeyup="total_amount_calculation()" class="form-control" id="packing_forwarding" style="text-align:right" value="<?php echo $packing_forwarding; ?>"></td>
                  </tr> -->
                  <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Packing & Forwarding</strong></td>
                    <td width="20%" align="right">
                      <input type="text" name="packing_forwarding" class="form-control" id="packing_forwarding" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?= $packing_forwarding?>"<?= $disabled; ?>>
                    </td>
                    <td width="20%" align="center">
                      <select name="packing_forwarding_tax" id="packing_forwarding_tax" class="form-control select2" onchange="total_amount_calculation()" <?= $disabled; ?>>
                        <?= $packing_forwarding_tax_options ?>
                      </select>
                    </td>
                    <td width="20%" align="right">
                      <input type="text" name="packing_forwarding_amount" class="form-control" readonly id="packing_forwarding_amount" style="text-align:right;width: 200px;" value="<?= $packing_forwarding_amount?>">
                    </td>
                  </tr>
                  <!-- <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>TCS(%)</strong></td>
                    <td>&nbsp;</td>
                    <td width="20%" align="center"><input type="text" name="tcs_percentage" class="form-control" onkeyup="total_amount_calculation()" id="tcs_percentage" style="text-align:right; width: 100px;" value="<?php echo $tcs_percentage; ?>"></td>
                    <td height="30" width="20%" align="right"><input type="text" name="tcs_amount" class="form-control" readonly id="tcs_amount" style="text-align:right;width: 200px;" value="<?php echo $tcs_amount; ?>"></td>
                  </tr> -->
                  <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Round off</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td height="30" width="20%" align="right"><input type="text" name="round_off" onkeyup="total_amount_calculation()" class="form-control" id="round_off" style="text-align:right" value="<?php echo $round_off; ?>"<?= $disabled; ?>></td>
                  </tr>
                  <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Total GST Amount</strong></td>
                    <td colspan="3" align="right">
                      <input type="text" name="total_gst_amount" class="form-control" readonly id="total_gst_amount" style="text-align:right;width: 200px;" value="<?= $total_gst_amount ?>">
                    </td>
                  </tr>
                  <tr>
                    <td height="30" align="right" style="padding: 10px;"><strong>Gross Amount</strong></td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td height="30" width="20%" align="right"><input type="text" readonly name="gross_amount" class="form-control" id="gross_amount" style="text-align:right" value="<?php echo $gross_amount; ?>"></td>
                  </tr>
                </table>
              </div>
            </div>
            <div class="form-group row ">
              <div class="col-md-12">
              <div class="row form-group">
                <label><strong>Terms And Conditions:</strong></label>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class=" form-control-label">&nbsp;<strong>Payment Days:</strong></label>
                </div>
                <div class="col-md-2">  
                <input type="text" name="payment_days" id="payment_days"  class="form-control" value="<?php echo $payment_days; ?>"<?= $disabled; ?>>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class=" form-control-label">&nbsp;<strong>Delivery</strong></label>
                </div>
                <div class="col-md-2">
                <input type="text" name="delivery" id="delivery" class="form-control" value="<?php echo $delivery; ?>"<?= $disabled; ?> >
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class="form-control-label">&nbsp;<strong>Billing Address:</strong></label>
                </div>
                <div class="col-md-4">
                <textarea name="billing_address" id="billing_address" class="form-control" rows="3" placeholder="Enter billing address here..." <?= $disabled; ?>><?php echo isset($billing_address) ? $billing_address : ''; ?></textarea>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class="form-control-label">&nbsp;<strong>Shipping Address:</strong></label>
                </div>
                <div class="col-md-4">
                <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" placeholder="Enter shipping address here..." <?= $disabled; ?>><?php echo isset($shipping_address) ? $shipping_address : ''; ?></textarea>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class="form-control-label">&nbsp;<strong>Remarks:</strong></label>
                </div>
                <div class="col-md-4">
                <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Enter remarks here..." <?= $disabled; ?>><?php echo isset($remarks) ? $remarks : ''; ?></textarea>
                </div>
              </div>
              </div>
                     <div class="form-group row" >
                        <label class="col-md-2 col-form-label">Approval Status</label>
                        <div class="col-md-4">
                            <select class="select2 form-control" id="appr_status" onchange="po_status_approval()">
                               <?= $po_approval_options; ?>
                            </select>
                        </div>
                        <label class="col-md-2 col-form-label" id="cancelReasonLabel" style="display: none;">Cancel Reason</label>
                        <div class="col-md-4">
                            <textarea id="cancelReason" placeholder="Enter reason for cancellation" class="form-control" rows="4" style="display: none;"></textarea>
                        </div>

                    </div>
          </form>
          

          <!-- Buttons -->
          <div class="form-group row ">
            <div class="col-md-12 text-end">
              <!-- Cancel, Save and Update Buttons -->
              <?php echo btn_cancel($btn_cancel); ?>
              <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
            </div>
          </div>

        </form>
            </div>
        </div>
    </div>
</div>
