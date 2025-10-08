<style>
.col-2-5 {
    flex: 0 0 20.833333%;
    max-width: 18.33333%;
}
</style>
<?php
include 'crud.php';
file_put_contents("form_debug.txt", "✅ form.php loaded\n", FILE_APPEND);

error_log("checkpoint 1!!" . "\n", 3, "grn_debug.txt");
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";
$screen_unique_id = "";

if (!isset($_GET["screen_unique_id"])) {
    $screen_unique_id = unique_id();
} else {
    $screen_unique_id = $_GET["screen_unique_id"];
}
error_log("screen_unique_id: " . $screen_unique_id . "\n", 3, "grn_debug.txt");

$is_update_mode = isset($_GET['unique_id']) && !empty($_GET['unique_id']);
error_log("is_update_mode: " . $is_update_mode . "\n", 3, "grn_debug.log");
if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        error_log("Checking GRN update load for UID: $unique_id", 3, "grn_debug.log");

        $table      =  "grn";

        $columns    = [
            "company_id",
            "project_id",
            "supplier_invoice_no",
            "invoice_date",
            "dc_no",
            "inward_type",
            "supplier_name",
            "eway_bill_no",
            "eway_bill_date",
            "paf",
            "freight",
            "other",
            "round",
            "gst_paf",
            "gst_freight",
            "gst_other",
            "po_number",
            "grn_number",
            "screen_unique_id",
            "description",
            "check_status",
            "checked_by",
            "check_remarks"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $city_values            = $pdo->select($table_details,$where);
        error_log("City values fetch result: " . print_r($city_values, true), 3, "grn_debug.log");

        if ($city_values->status) {

            $state_id = '';
            $city_values        = $city_values->data;

            $company_name         = $city_values[0]["company_id"];
            $project_id           = $city_values[0]["project_id"];
            $supplier_invoice_no          = $city_values[0]["supplier_invoice_no"];
            $invoice_date          = $city_values[0]["invoice_date"];
            $dc_no          = $city_values[0]["dc_no"];
            $inward_type          = $city_values[0]["inward_type"];
            $supplier_name          = $city_values[0]["supplier_name"];
            $eway_bill_no          = $city_values[0]["eway_bill_no"];
            $eway_bill_date          = $city_values[0]["eway_bill_date"];
            $paf                = $city_values[0]['paf'];
            $freight                = $city_values[0]['freight'];
            $other                = $city_values[0]['other'];
            $round                = $city_values[0]['round'];
            $gst_paf                = $city_values[0]['gst_paf'];
            $gst_freight               = $city_values[0]['gst_freight'];
            $gst_other                = $city_values[0]['gst_other'];
            $purchase_number        = $city_values[0]["po_number"];
            $grn_number        = $city_values[0]["grn_number"];
            $screen_unique_id        = $city_values[0]["screen_unique_id"];
            $description        = $city_values[0]["description"];
            $check_status        = $city_values[0]["check_status"];
            $checked_by        = $city_values[0]["checked_by"];
            $check_remarks        = $city_values[0]["check_remarks"];

            error_log("check: " . $gst_paf . $paf . "\n", 3, "check_log.txt");

            $state_options      = state();
            $state_options      = select_option($state_options,"Select the State",$state_id); 

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
            error_log("GRN Load failed: " . print_r($city_values->error, true), 3, "grn_debug.log");
        }
    }
} else {
    $is_update_mode = false;
}

$tax = 0;
$discount = 0;

if ($is_update_mode) {
    $po_sc_unique_id = fetch_po_sc_unique_id($purchase_number); // Ensure $purchase_number is valid
    $td_data = fetch_tax_discount($po_sc_unique_id);

    if (is_array($td_data) && isset($td_data['tax'], $td_data['discount'])) {
        $tax = $td_data['tax'];
        $discount = $td_data['discount'];
    }
}

$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);


$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id,"Select", $uom);

$product_unique_id      = product_name();
$product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);

$purchase_order_no  = get_po_number();
$purchase_order_no = select_option($purchase_order_no, "Select Purchase Order No",$purchase_number);

$gst_paf_options     = select_option(tax(), "Select GST", $gst_paf);
$gst_freight_options = select_option(tax(), "Select GST", $gst_freight);
$gst_other_options   = select_option(tax(), "Select GST", $gst_other);

$supplier_name_options     = supplier($supplier_name);
error_log("Supplier return for '$supplier_name': " . print_r($supplier_name_options, true), 3, "grn_debug.log");

// $supplier_name_options     = select_option($supplier_name_options,"Select", $supplier_name);
if (!empty($supplier_name_options) && is_array($supplier_name_options)) {
    $supplier_data = $supplier_name_options[0]; // First result

    $supplier_id = $supplier_data['unique_id'];
    $supplier_name = $supplier_data['supplier_name'];
}

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated funnel_form" id="grn_new_form" name="funnel_form">
    <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id ?>">
    <input type="hidden" id="screen_unique_id" name="screen_unique_id" value="<?= $screen_unique_id ?>">
    <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="" <?php echo $is_update_mode ? 'readonly' : ''; ?>>
    <!-- Hidden Inputs for project_id and po_number -->
    <?php if ($is_update_mode): ?>
        <input type="hidden" id="project_id" name="project_id" value="<?= $project_id ?>">
        <input type="hidden" id="purchase_order_no" name="purchase_order_no" value="<?= $purchase_number ?>">
    <?php endif; ?>
    <input type="hidden" id="is_update_mode" name="is_update_mode" value="<?= $is_update_mode ? 'true' : 'false' ?>">
    <div class="form-group row" <?= $is_update_mode ? '' : 'style="display:none;"' ?>>
        <label class="col-md-1 col-form-label" for="grn_number">GRN Number</label>
        <div class="col-md-3 col-2-5">
            <input type="text" name="grn_number" id="grn_number" class="form-control"
                value = "<?= $grn_number ?>" readonly>
        </div>
        <div class="col-md-9"></div>
    </div>
    <div class="row">
        <!-- Column 1 -->
        <div class="col-md-3 col-sm-12 col-12">
            <div class="form-group row">
                <label class="col-md-4 col-form-label">Company Name</label>
                    <div class="col-md-8">
                        <select name="company_id" id="company_id"  class="form-control select2" <?= $is_update_mode ? 'disabled' : '' ?> onchange="get_project_name(this.value);" required>
                                <?= $company_name_options ?>
                           </select>
                    </div>
                </div>
            <div class="form-group row">

                <label class="col-md-4 col-form-label" for="supplier_invoice_no">Supplier Invoice No</label>
                <div class="col-md-8">
                    <input type="text" id="supplier_invoice_no" name="supplier_invoice_no" class="form-control" value="<?= $supplier_invoice_no ?>" required>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label" for="eway_bill_no">eWay Bill No</label>
                <div class="col-md-8">
                    <input type="text" id="eway_bill_no" name="eway_bill_no" class="form-control" value="<?= $eway_bill_no ?>" required>
                </div>
            </div>

            <!--<div class="form-group row">-->
            <!--    <label class="col-md-4 col-form-label" for="mode_type">Mode Type</label>-->
            <!--    <div class="col-md-8">-->
            <!--        <input type="text" id="mode_type" name="mode_type" class="form-control" required>-->
            <!--    </div>-->
            <!--</div>-->
        </div>

        <!-- Column 2 -->
        <div class="col-md-3 col-sm-12 col-12">
            <div class ="form-group row">
                <label class="col-md-4 col-form-label">Project Name</label>
                <div class="col-md-8">
                    <select name="project_id" id="project_id" class="form-control select2" onchange="get_purchase_order_no(this.value);" <?= $is_update_mode ? 'disabled' : '' ?> required>
                        <?= $project_options ?>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label class="col-md-4 col-form-label" for="po_number">PO Number</label>
                <div class="col-md-8">
                    <select name="purchase_order_no" id="purchase_order_no" class="form-control select2" <?= $is_update_mode ? 'disabled' : '' ?>  required>
                        <?= $purchase_order_no ?>
                    </select>
                </div>
            </div>
            <br>
            <div class="form-group row">
                <label class="col-md-4 col-form-label" for="eway_bill_date">eWay Bill Date</label>
                <div class="col-md-8">
                    <input type="date" id="eway_bill_date" name="eway_bill_date" class="form-control" value="<?= $eway_bill_date ?>">
                </div>
            </div>
        </div>

        <!-- Column 3 -->
        <div class="col-md-3 col-sm-12 col-12">
            <div class="form-group row">
                <label class="col-md-4 col-form-label" for="invoice_date">Invoice Date</label>
                <div class="col-md-8">
                    <input type="date" id="invoice_date" name="invoice_date" class="form-control" value="<?= $invoice_date ?>" required>
                </div>
            </div>
            
            <div class="form-group row">
                <label class="col-md-4 col-form-label" for="supplier_name">Supplier Name</label>
                <div class="col-md-8">
                    <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="<?= $supplier_name ?>" <?= $is_update_mode ? 'disabled' : '' ?> required>
                    <input type="hidden" name="supplier_id" id="supplier_id"  value="<?= $supplier_id ?>">
                </div>
            </div>
        </div>

        <!-- Column 4 -->
        <div class="col-md-3 col-sm-12 col-12">
            <div class="form-group row">
                <label class="col-md-4 col-form-label" for="dc_no">DC No</label>
                <div class="col-md-8">
                    <input type="text" id="dc_no" name="dc_no" class="form-control" value="<?= $dc_no ?>" required>
                </div>
            </div>
        </div>
    </div>                          
                    <div class="col-12">                            
                       <div class="row">
                            <div class="col-12">
                            <!-- Table Begiins -->
                                <table id="grn_sublist_datatable" class="table dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Order Qty</th>
                                            <!-- <th>Prev Receipt</th> -->
                                            <th>UOM</th>
                                            <th>Previously Received Qty</th>
                                            <th>Now Received Qty</th>
                                            <th>Rate</th>
                                            <th>Tax (%)</th>
                                            <th>Discount Type</th>
                                            <th>Discount</th>
                                            <th>Amount</th>
                                            <!-- <th>Accepted Qty</th>
                                            <th>Rejected Qty</th>
                                            <th>Rejected Reason</th> -->
                                            <th>Action</th>
                                        </tr>
                                            <tr id="requisition_details_form">
        
                                            <th>#</th>
                                            <th> 
                                            <select id="item_code" name="item_code" class="form-control select2" <?php echo $is_update_mode ? 'disabled' : ''; ?>>
                                                <?= select_option(item_name_list(), "Select Item", "") ?>
                                            </select>
                                            </th>
                                            <th>
                                                <input type="text" id="order_qty" name="order_qty" class="form-control" value="" onkeypress='number_only(event);' readonly>
                                            </th>
                                            <th>
                                                <select name="uom" id="uom" class="select2 form-control" <?php echo $is_update_mode ? 'disabled' : ''; ?>>  
                                                    <?php echo $uom_unique_id; ?>
                                                </select>
                                            </th>
                                            <th>
                                                <input type="text" id="already_received_qty" name="already_received_qty" class="form-control" value="" readonly>
                                            </th>
                                            <th>
                                                <input type="text" id="now_received_qty" name="now_received_qty" class="form-control" value="" onkeypress='number_only(event);' >
                                            </th>
                                            <th>
                                                <input type="text" id="rate" name="rate" class="form-control" value="" onkeypress='number_only(event);' readonly>
                                            </th>
                                            <th>
                                                <input type="text" id="tax" name="tax" class="form-control" value="" readonly>
                                            </th>
                                            <th>
                                                <select id="discount_type" name="discount_type" class="form-control" <?php echo $is_update_mode ? 'disabled' : 'disabled'; ?>>
                                                    <option value="0">Select Discount Type</option>
                                                    <option value="1">Percentage</option>
                                                    <option value="2">Amount</option>
                                                </select>
                                            </th>
                                            <th>
                                                <input type="text" id="discount" name="discount" class="form-control" value="" onkeypress='number_only(event);' readonly>
                                            </th>
                                            <th>
                                                <input type="text" id="amount" name="amount" class="form-control" value="" onkeypress='number_only(event);' readonly>
                                            </th>
                                            <th>
                                                <button type="button" class=" btn btn-success waves-effect  waves-light grn_add_update_btn" onclick="grn_sublist_add_update()">ADD</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                       
                                    </tbody>
                                    
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                        <!-- <div class="row form-group"> -->
                            <!-- <label class="col-md-2 font-weight-normal" for="po_status"> PO Status/ Request Status</label> -->
                            <!-- <div class="col-md-2"> -->
                                <!--<select name="po_status" id="po_status" class="select2 form-control" ></select>-->
                                <!-- <input type="text" id="po_status" name="po_status" class="form-control" required> -->
                                
                            <!-- </div> -->
                        <!-- </div> -->
                        <div class="row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label for="basic">Basic</label>
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control" id="basic" name="basic" placeholder="Basic total amount" onkeypress='number_only(event);' readonly>
                            </div>

                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label for="paf">PAF</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="paf" name="paf" placeholder="Enter PAF" onkeypress='number_only(event);' step="0.01" value=<?= $paf; ?>>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="gst_paf" name="gst_paf">
                                    <?= $gst_paf_options ?>
                                </select>
                            </div>
                            
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label for="freight">Freight Charges</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="freight" name="freight" placeholder="Enter Freight" onkeypress='number_only(event);' step="0.01" value=<?= $freight; ?>>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="gst_freight" name="gst_freight">
                                    <?= $gst_freight_options ?>
                                </select>
                            </div>

                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label for="other_charges">Other Charges</label>
                            </div>
                            <div class="col-md-3">
                                <input type="text" class="form-control" id="other_charges" name="other" placeholder="Enter Other Charges" onkeypress='number_only(event);' step="0.01" value=<?= $other; ?>>
                            </div>
                            <div class="col-md-3">
                                <select class="form-control" id="gst_other" name="gst_other">
                                    <?= $gst_other_options ?>
                                </select>
                            </div>

                        </div>

                        <div class="form-group row">
                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label for="tot_gst">Total GST</label>
                            </div>
                            <div class="col-md-6">
                                <input type="hidden" class="form-control" id="tot_gst1" name="tot_gst1" placeholder="Total GST" onkeypress='number_only(event);' step="0.01" readonly> 
                                <input type="text" class="form-control" id="tot_gst" name="tot_gst" placeholder="Total GST" onkeypress='number_only(event);' step="0.01" readonly> 
                            </div>

                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <label for="round_off">Round Off</label>
                            </div>
                            <div class="col-md-6">
                                <input type="number" class="form-control" id="round_off" name="round_off" placeholder="Enter Round Off" step="0.01" min="-10" max="10" value=<?= $round ?>> 
                            </div>

                            <div class="col-md-3"></div>
                            <div class="col-md-3">
                                <h5>Total Amount</h5>
                            </div>
                            <div class="col-md-6">
                                <span id="final_total">0.00</span>
                            </div>
                        </div>

                        <div class="row form-group">
                            <label class="col-md-2 col-form-label" for="description"> Description</label>
                        </div>
                        <div class="row form-group">
                           <div class="col-md-8">
                                <textarea id="description" name="description" rows="5" class="form-control"  ><?= $description ?></textarea>
                            </div>
                        </div>

                        <div class="form-group row" <?php if ($check_status != 2) echo 'style="display: none;"'; ?>>
                            <label for="grn_approval" class="col-md-2 col-form-label">Approval Status</label>
                            <div class="col-md-4">
                                <select name="grn_approval" id="grn_approval" class="form-control" onchange="toggleRemark()" disabled>
                                    <option value="0" <?= $check_status == 0 ? 'selected' : '' ?>>Select Approval Status</option>
                                    <option value="1" <?= $check_status == 1 ? 'selected' : ''?>>Approve</option>
                                    <option value="2" <?= $check_status == 2 ? 'selected' : ''?>>Reject</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group row" id="status_remark_div" <?php if ($check_status != 2) echo 'style="display: none;"'; ?>">
                            <label for="status_remark" class="col-md-2 col-form-label">Status Remark</label>
                            <div class="col-md-6">
                                <textarea name="status_remark" id="status_remark" class="form-control" rows="3" placeholder="Enter your remark" readonly><?= $check_remarks ?></textarea>
                            </div>
                        </div>

                        </div>

                        <div class="form-group row ">
                            <div class="col-md-12">
                                <!-- Cancel,save and update Buttons -->
                                <?php echo btn_cancel($btn_cancel);?>
                                <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                            </div>                                
                        </div>
                    </div>
                </div>
                    <input type="hidden" id="tax_val" value="" readonly>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  
