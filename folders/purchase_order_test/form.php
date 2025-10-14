<script>
  var sublist = "";
</script>

<?php
$btn_text         = "Save";
$btn_action       = "create";
$is_btn_disable   = "";
$company_name     = "";

$screen_unique_id = "";
$unique_id        = isset($_GET["unique_id"]) ? $_GET["unique_id"] : "";

if (!empty($unique_id)) {

    $where = ["unique_id" => $unique_id];
    $table = "purchase_order";

    $columns = [
        "screen_unique_id",
        "company_id",
        "project_id",
        "from_comp",
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
        "purchase_order_type",
        "contact_person",
        "vendor_contact_no",
        "gst_no",
        "pan_no",
        "quotation_no",
        "quotation_date",
        "revision_no",
        "revision_date",
        "status",       
        "lvl_2_status",
        "revision_remarks",
        "msme_type_display",
        "msme_no"
    ];

    $result = $pdo->select([$table, $columns], $where);

    if ($result->status) {
        $data = $result->data[0];

        // Assign variables individually for clarity
        $screen_unique_id            = $data["screen_unique_id"];
        $company_id                  = $data["company_id"];
        $project_id                  = $data["project_id"];
        $from_comp                  = $data["from_comp"];
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
        $shipping_address            = $data["shipping_address"];
        $remarks                     = $data["remarks"];
        $packing_forwarding          = $data["packing_forwarding"];
        $total_gst_amount            = $data["total_gst_amount"];
        $po_type                     = $data["purchase_order_type"];
        $contact_person              = $data["contact_person"];
        $vendor_contact_no           = $data["vendor_contact_no"];
        $gst_no                      = $data["gst_no"];
        $pan_no                      = $data["pan_no"];
        $quotation_no                = $data["quotation_no"];
        $quotation_date              = disdate($data["quotation_date"]);
        $revision_no                 = $data["revision_no"];
        $revision_date               = disdate($data["revision_date"]);
        $revision_remarks            = $data["revision_remarks"];
        $msme_display_type           = $data["msme_type_display"];
        $msme_no                     = $data["msme_no"];
        $status                      = $data["status"];
        $lvl_2_status                = $data["lvl_2_status"];



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

if (empty($revision_no)) {
    // First revision
    $revision_no = "REV001";
} else {
    // Extract number part after "REV"
    $num = (int)substr($revision_no, 3);

    // Increment and format back
    $revision_no = "REV" . sprintf("%03d", $num + 1);
}


// $revision_date = date("Y-m-d", strtotime($data["revision_date"]));
$revision_date = date("Y-m-d");


$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_id);

$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);
// //Supplier Name
$tax_options     = tax();
$tax_options     = select_option($tax_options, "Select Tax", $tax);

// Product Name
$product_unique_id      = product_name();
$product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

// Unit Name
$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id,"Select", $uom);

$supplier_name_options     = supplier();
$supplier_name_options     = select_option($supplier_name_options,"Select", $supplier_id);

$pr_number_options  = get_pr_number();
$pr_number_options  = select_option($pr_number_options,"Select the Project Name");

$freight_tax_options            = select_option(tax(), "Select Tax", $freight_tax);
$packing_forwarding_tax_options = select_option(tax(), "Select Tax", $packing_forwarding_tax);
$other_tax_options              = select_option(tax(), "Select Tax", $other_tax);

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
?>

<!--<input type="text" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id ?>">-->
<!--<input type="hidden" name="unique_id" id="unique_id" value="<?= $unique_id ?>">-->
<!--<input type="hidden" name="sub_counter" id="sub_counter" value="1">-->
<!--<input type="hidden" name="sublist_data" id="sublist_data" value=''>-->

<!-- 🔔 Unsaved Warning Banner (initially hidden) -->
<div id="unsaved_warning_banner"
     class="alert alert-warning text-center fw-bold py-2"
     style="display:none; position:sticky; top:0; z-index:1050;">
  ⚠ Unsaved PR items added — please <b>Save</b> or <b>Cancel</b> this PO before leaving or reloading.
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form class="was-validated" id="purchase_order_form">
            <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id ?>">
            <input type="hidden" name="unique_id" id="unique_id" value="<?= $unique_id ?>">
            <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="">

            <!-- <input type="hidden" name="sub_counter" id="sub_counter" value="1"> -->
            <!-- <input type="hidden" name="sublist_data" id="sublist_data" value=''> -->

            <div class="form-group row">
                <label class="col-md-2 col-form-label labelright">Company Name</label>
                <div class="col-md-3">
                    <select name="company_id" id="company_id"  class="form-control select2"  onchange="get_project_name(this.value);" required>
                        <?= $company_name_options ?>
                    </select>
                </div>
                <label class="col-md-2 col-form-label labelright">Project Name</label>
                <div class="col-md-3">
                    <select name="project_id" id="project_id" class="form-control select2" required>
                        <?= $project_options ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-2 col-form-label labelright">PO Type</label>
                <div class="col-md-3">
                    <select name="po_for" id="po_for" class="form-control select2" required>
                        <?= $po_type_options; ?>
                    </select>
                </div>
                <label class="col-md-2 col-form-label labelright" for="pr_number">PR Number</label>
                <div class="col-md-3">
                    <div class="input-group">
                  
                    </div>
              
                    <!-- Button to Open Modal -->
                    <button type="button" class="btn btn-primary mt-2 mb-2" onclick="show_pr_sublist()">
                        +
                    </button>
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
                    <select name="supplier_id" id="supplier_id" class="select2 form-control" required>
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
                    <!-- Show today's date readonly -->
                    <input type="text" class="form-control" value="<?= date('Y-m-d'); ?>" readonly>
                
                    <!-- Hidden field to store value in DB -->
                    <input type="hidden" name="entry_date" id="entry_date" value="<?= date('Y-m-d'); ?>">
                </div>
                <div class="col-md-3 offset-md-2">
                    <div class="form-check">
                        <input type="checkbox" 
                               class="form-check-input" 
                               id="from_comp" 
                               name="from_comp" 
                               value="1"
                               <?php if (!empty($from_comp) && $from_comp == 1) echo 'checked'; ?>>
                        <label class="form-check-label labelright" for="from_comp">From Company</label>
                    </div>
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
                    <input type="text" name="quotation_no" id="quotation_no" class="form-control" value="<?= $quotation_no ?>" placeholder="Enter Quotation Number">
                </div>
            
                <label class="col-md-2 col-form-label labelright">Quotation Date</label>
                <div class="col-md-3">
                    <input type="date" name="quotation_date" id="quotation_date" value="<?= $quotation_date ?>" class="form-control">
                </div>
            </div>
        
        <?php if ($status == 1 && $lvl_2_status == 1): ?>
            <div class="form-group row">
                <label class="col-md-2 col-form-labe labelright">Revision No</label>
                <div class="col-md-3">
                    <input type="text" name="revision_no" id="revision_no" class="form-control"
                     value="<?= $revision_no ?>" placeholder="Enter Revision Number" readonly>
                </div>
        
                <label class="col-md-2 col-form-label labelright">Revision Date</label>
                <div class="col-md-3">
                    <input type="date" name="revision_date" id="revision_date"
                     value="<?= $revision_date ?>" class="form-control" readonly>
                </div>
            </div>
        
            <div class="form-group row">
                <label class="col-md-2 col-form-label labelright">Revision Remarks</label>
                <div class="col-md-3">
                    <textarea name="revision_remarks" id="revision_remarks"
                        class="form-control" placeholder="Enter Revision Remarks" readonly><?= $revision_remarks ?></textarea>
                </div>
            </div>
        <?php endif; ?>



        </form>
          <div class="col-12">
            <form id="po_sublist_form">
                <div class="table-responsive">
              <table id="purchase_order_sub_datatable" class="table table-bordered table-striped w-100">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Discount Type</th> <!-- ✅ New -->
                    <th>Discount(%)</th>
                    <th id="tax_title">Tax</th>
                    <th>Amount</th>
                    <th>Delivery Date</th>
                    <th>Remarks</th>
                    <th>Action</th>
                  </tr>
                  <tr id="po_details_form">
                    <td>#</td>
                    <td>
            <select id="item_code" name="item_code" class="form-control select2" required>
              <?= select_option(item_name_list(), "Select Item", "") ?>
            </select>
                    </td>
                  <td>
                    <select id="uom" name="uom" class="form-control select2" required>
                      <?= $uom_unique_id ?>
                    </select>
                  </td>

                    <td>
                      <input type="number" id="quantity" name="quantity" class="form-control" placeholder="Qty" onkeyup='sub_total_amount();' step="1" required>
                    </td>
                    <td>
                      <input type="number" id="rate" name="rate" class="form-control" placeholder="Rate" onkeyup='sub_total_amount();' step="1" required>
                    </td>
                    <td>
                      <select id="discount_type" name="discount_type" class="form-control" onchange="handleDiscountTypeChange()">
                        <option value="3">Select Discount Type</option> <!-- Default -->
                        <option value="2">Amount (₹)</option>
                        <option value="1">Percentage (%)</option>
                      </select>
                    </td>
                    <td>
                      <input type="number" id="discount" name="discount" class="form-control" readonly placeholder="Discount" onkeyup='sub_total_amount();' step="1" required>
                    </td>
                    <td>
                      <select id="tax" name="tax" class="form-control select2" onchange="get_tax_val(this.value)" required>
                        <?= $tax_options ?>
                      </select>
                    </td>
                    <td>
                      <input type="text" id="amount" name="amount" class="form-control" readonly placeholder="Amount">
                    </td>
                    <td>
                        <input type="date" id="delivery_date" name="delivery_date" class="form-control">
                    </td>
                    <td>
                      <input type="text" id="item_remarks" name="item_remarks" class="form-control" placeholder="Remarks" required>
                    </td>
                    <td>
                      <button type="button" class="btn btn-success po_sublist_add_btn" onclick="po_sublist_add_update()">Add</button>
                    </td>
                  </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                  <tr>
                    <th colspan="3" class="text-right">Total:</th>
                    <th>
                      <input type="text" id="total_quantity" name="total_quantity" class="form-control" readonly value="0">
                    </th>
                    <th colspan="4"></th>
                    <th>
                      <input type="text" id="total_sub_amount" name="total_sub_amount" class="form-control" readonly value="0.00">
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                  </tr>
                </tfoot>
              </table></div>
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
                      <input type="text" name="freight_value" class="form-control" id="freight_value" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?= $freight_value?>">
                    </td>
                    <td width="20%" align="center">
                      <select name="freight_tax" id="freight_tax" class="form-control select2" onchange="total_amount_calculation()">
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
                      <input type="text" name="other_charges" class="form-control" id="other_charges" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?= $other_charges ?>">
                    </td>
                    <td width="20%" align="center">
                      <select name="other_tax" id="other_tax" class="form-control select2" onchange="total_amount_calculation()">
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
                      <input type="text" name="packing_forwarding" class="form-control" id="packing_forwarding" style="text-align:right;width: 150px;" onkeyup="total_amount_calculation()" value="<?= $packing_forwarding?>">
                    </td>
                    <td width="20%" align="center">
                      <select name="packing_forwarding_tax" id="packing_forwarding_tax" class="form-control select2" onchange="total_amount_calculation()">
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
                    <td height="30" width="20%" align="right"><input type="text" name="round_off" onkeyup="total_amount_calculation()" class="form-control" id="round_off" style="text-align:right" value="<?php echo $round_off; ?>"></td>
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
                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Terms And Conditions:</strong></label>
                </div>
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label class=" form-control-label">&nbsp;<strong>Payment Days:</strong></label>
                        </div>
                        <div class="col-md-2">  
                            <textarea name="payment_days" id="payment_days" rows="3" class="form-control"><?php echo $payment_days; ?></textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label class=" form-control-label">&nbsp;<strong>Transport</strong></label>
                        </div>
                        <div class="col-md-2">
                            <textarea name="delivery" id="delivery" class="form-control" value="<?php echo $delivery; ?>"></textarea>
                        </div>
                    </div>
                    <div class="row form-group">
                        <div class="col-md-2">
                            <label class="form-control-label">&nbsp;<strong>Billing Address:</strong></label>
                        </div>
                        <div class="col-md-6">
                            <textarea name="billing_address" id="billing_address" class="form-control" rows="3" placeholder="Enter billing address here..."><?php echo isset($billing_address) ? $billing_address : ''; ?></textarea>
                        </div>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class="form-control-label">&nbsp;<strong>Shipping Address:</strong></label>
                </div>
                <div class="col-md-6">
                <textarea name="shipping_address" id="shipping_address" class="form-control" rows="3" placeholder="Enter shipping address here..."><?php echo isset($shipping_address) ? $shipping_address : ''; ?></textarea>
                </div>
              </div>
              <div class="row form-group">
                <div class="col-md-2">
                <label class="form-control-label">&nbsp;<strong>Remarks:</strong></label>
                </div>
                <div class="col-md-6">
                <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Enter remarks here..."><?php echo isset($remarks) ? $remarks : ''; ?></textarea>
                </div>
              </div>
              </div>
        </form>
          

          <!-- Buttons -->
          <div class="form-group row">
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