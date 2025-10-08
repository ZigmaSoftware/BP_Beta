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
        "freight_percentage",
        "freight_amount",
        "other_charges",
        "other_tax",
        "other_charges_percentage",
        "tcs_percentage",
        "tcs_amount",
        "round_off",
        "gross_amount",
        "contact_person",
        "quote_no",
        "quote_date",
        "delivery",
        "ship_via",
        "delivery_term_fright",
        "delivery_site",
        "payment_days",
        "dealer_reference",
        "document_throught",
        "billing_address",
        "billing_information",
        "approve_status",
        "status",
        "reason"
    ];
    $result = $pdo->select([$table, $columns], $where);
    if ($result->status) {
        $data = $result->data[0];
        // Assign variables individually for clarity
        $screen_unique_id            = $data["screen_unique_id"];
        $company_id                  = $data["company_id"];
        $project_id                  = $data["project_id"];
        $purchase_order_no           = $data["purchase_order_no"];
        $entry_date                  = disdate($data["entry_date"]);
        $supplier_id                 = $data["supplier_id"];
        $branch_id                   = $data["branch_id"];
        $purchase_type               = $data["purchase_type"];
        $purchase_request_no         = $data["purchase_request_no"];
        $net_amount                  = $data["net_amount"];
        $freight_percentage          = $data["freight_percentage"];
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
        $billing_information         = $data["billing_information"];
        $approve_status              = $data["approve_status"];
        $status                      = $data["status"];
        $reason                      = $data["reason"];
        $btn_text                    = "Update";
        $btn_action                  = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
} 
$company_name_options           = company_name($company_id);

$project_options                = get_project_name($project_id);
// //Supplier Name
$tax_options                    = tax();
$tax_options                    = select_option($tax_options, "Select Tax", $tax);
// Product Name
$product_unique_id              = product_name();
$product_unique_id              = select_option($product_unique_id, "Select", $group_unique_id);
// Unit Name
$uom_unique_id                  = unit_name();
$uom_unique_id                  = select_option($uom_unique_id, "Select", $uom);

$supplier_name_options          = supplier($supplier_id);

$pr_number_options              = get_pr_number();
$pr_number_options              = select_option($pr_number_options, "Select the Project Name");

if($status == 1){
    $color = "green";
} else {
    $color = "red";
}

$po_approval_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Approve"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Cancel"
    ]
];

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" id="purchase_order_form">
                    <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id ?>">
                    <input type="hidden" name="unique_id" id="unique_id" value="<?= $unique_id ?>">
                    <input type="hidden" name="sub_counter" id="sub_counter" value="1">
                    <input type="hidden" name="sublist_data" id="sublist_data" value=''>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Purchase Order No : </label>
                        <div class="col-md-4">
                            <p><?= $purchase_order_no ?> </p>
                        </div>
                        <label class="col-md-2 col-form-label">Entry Date : </label>
                        <div class="col-md-4">
                            <p> <?= $entry_date ?> </p>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Company Name : </label>
                        <div class="col-md-4">
                            <p><?= $company_name_options['0']['company_name'] ?></p>
                        </div>
                        <label class="col-md-2 col-form-label">Project Name : </label>
                        <div class="col-md-4">
                            <p><?= $project_options['0']['project_name'] ?></p>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label">Supplier Name : </label>
                        <div class="col-md-4">
                            <p><?= $supplier_name_options['0']['supplier_name'] ?></p>
                        </div>
                        
                    </div>

                </form>
                <div class="col-12">
                    <form id="po_sublist_form">
                        <table id="po_sub_datatable_print" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Product Name</th>
                                    <th>UOM</th>
                                    <th>Qty</th>
                                    <th>Appr Qty</th>
                                    <th>lvl 2 Qty</th>
                                    <th>Rate</th>
                                    <th>Discount(%)</th>
                                    <th>Tax</th>
                                    <th>Appr Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $columns = [
                                    "@a:=@a+1 as s_no",
                                    "item_code",
                                    "uom",
                                    "quantity",
                                    "appr_quantity",
                                    "lvl_2_quantity",
                                    // "CASE WHEN appr_quantity IS NULL OR appr_quantity = 0.00 THEN quantity ELSE appr_quantity END AS quantity",
                                    "rate",
                                    "discount",
                                    "tax",
                                    "CASE WHEN appr_amount IS NULL OR appr_amount = 0.00 THEN amount ELSE appr_amount END AS amount",
                                    // "unique_id"
                                ];
                        
                                $table_details = ["purchase_order_items, (SELECT @a:=0) AS a", $columns];
                                $where = ["screen_unique_id" => $screen_unique_id, "is_delete" => 0];
                        
                                $result_sub = $pdo->select($table_details, $where);
                        
                                if ($result_sub->status) {
                                    foreach ($result_sub->data as $row) {
                                        $item_data = item_name_list($row["item_code"]);
                                        $item_name= $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];
                                        
                                        $tax_data = tax($row["tax"]);
                                        $tax_name = $tax_data[0]["tax_name"];
                                        
                                        $unit_data = unit($row["uom"]);
                                        $unit_name= $unit_data[0]["unit_name"];
                                        if($row["appr_quantity"] == ""){
                                            $appr_qty = round($row["quantity"]);
                                        } else {
                                            $appr_qty = round($row["appr_quantity"]);  
                                        }
                                        if($row["appr_quantity"] == ""){
                                            $lvl_2_qty = round($row["quantity"]);
                                        } else if( $row["lvl_2_quantity"] == ""){
                                            $lvl_2_qty = round($row["appr_quantity"]);
                                        } else {
                                            $lvl_2_qty = round($row["lvl_2_quantity"]);  
                                        }
                                        $quantity = round($row["quantity"]);
                                        $amount = $row["amount"];
                                        ?>
                                        <tr>
                                            <td><?= $row["s_no"]; ?></td>
                                            <td><?= $item_name; ?></td>
                                            <td><?= $unit_name; ?></td>
                                            <td><?= $quantity; ?></td>
                                            <td><p style="color: <?= $color; ?>;"><?= $appr_qty; ?></p></td>
                                            <td><p style="color: <?= $color; ?>;"><?= $lvl_2_qty; ?></p></td>
                                            <td><?= round($row["rate"]); ?></td>
                                            <td><?= round($row["discount"]); ?></td>
                                            <td><?= $tax_name; ?></td>
                                            <td><?= $amount; ?></td>
                                        </tr>
                                        <?php
                                        $total_appr_qty     += $appr_qty;
                                        $total_lvl_2_qty    += $lvl_2_qty;
                                        $total_quantity     += $quantity;
                                        $total_amount       += $amount;
                                    }
                                }
                                ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-right">Total:</th>
                                    <th>
                                        <p><?= $total_quantity ?></p>
                                    </th>
                                    <th>
                                        <p><?= $total_appr_qty ?></p>
                                    </th>
                                    <th>
                                        <p><?= $total_lvl_2_qty ?></p>
                                    </th>
                                    <th colspan="3"></th>
                                    <th>
                                        <p><?= $total_amount ?></p>
                                    </th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </form>
                </div>
                <form class="was-validated form_second" id="form_second">
                    <div class="form-group row">
                        <div class="col-md-12">
                            <table width="100%" border="0">
                                <tr>
                                    <td height="30" align="right" style="padding: 10px; width: 70%;">
                                        <strong>Net Amount : </strong>
                                    </td>
                                    <td colspan="2">&nbsp;</td>
                                    <td height="30" width="20%" align="right">
                                        <p><?php echo $net_amount; ?></p>
                                    </td>
                                </tr>
                    
                                <tr>
                                    <td height="30" align="right" style="padding: 10px;">
                                        <strong>Freight Value (%) : </strong>
                                    </td>
                                    <td colspan="1">&nbsp;</td>
                                    <td width="10%" align="right">
                                        <p><?php echo round($freight_percentage) . "(%)"; ?></p>
                                    </td>
                                    <td height="30" width="20%" align="right">
                                        <p><?php echo $freight_amount; ?></p>
                                    </td>
                                </tr>
                    
                                <tr>
                                    <td height="30" align="right" style="padding: 10px;">
                                        <strong>Other Charge : </strong>
                                    </td>
                                    <td colspan="1">&nbsp;</td>
                                    <td width="10%" align="right">
                                        <p><?php echo $other_charges; ?></p>
                                    </td>
                                    <td height="30" width="20%" align="right">
                                        <p><?php echo $other_charges_percentage; ?></p>
                                    </td>
                                </tr>
                    
                                <tr>
                                    <td height="30" align="right" style="padding: 10px;">
                                        <strong>TCS(%) : </strong>
                                    </td>
                                    <td colspan="1">&nbsp;</td>
                                    <td width="10%" align="right">
                                        <p><?php echo round($tcs_percentage) . "(%)"; ?></p>
                                    </td>
                                    <td height="30" width="20%" align="right">
                                        <p><?php echo $tcs_amount; ?></p>
                                    </td>
                                </tr>
                    
                                <tr>
                                    <td height="30" align="right" style="padding: 10px;">
                                        <strong>Round off : </strong>
                                    </td>
                                    <td colspan="2">&nbsp;</td>
                                    <td height="30" width="20%" align="right">
                                        <p><?php echo $round_off; ?></p>
                                    </td>
                                </tr>
                    
                                <tr>
                                    <td height="30" align="right" style="padding: 10px;">
                                        <strong>Gross Amount : </strong>
                                    </td>
                                    <td colspan="2">&nbsp;</td>
                                    <td height="30" width="20%" align="right">
                                        <p><?php echo $gross_amount; ?></p>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                     <div class="form-group row" >
                        <label class="col-md-2 col-form-label">Approval Status : </label>
                        <div class="col-md-4">
                            <p><?= $po_approval_options[$status]['value']; ?></p>
                        </div>
                        <?php
                        if($status == 2){
                        ?>
                        <label class="col-md-2 col-form-label" id="cancelReasonLabel" >Cancel Reason : </label>
                        <div class="col-md-4">
                            <p><?= $reason; ?></p>
                            
                        </div>
                        <?php
                        }
                        ?>
                    </div>
                    
                </form>
               
                </form>
            </div>
        </div>
    </div>
</div>
