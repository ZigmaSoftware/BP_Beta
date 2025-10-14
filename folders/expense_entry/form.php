<?php include 'function.php'; ?>

<?php
// var_dump($_SESSION);
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
 
// $requisition_number = generate_requisition_number();

$invoice_date = date('Y-m-d');
$due_date     = "";
// $invoice_no   = generate_invoice_number(); // <- create your own function for auto-numbering
$customer_id  = "";
$remarks      = "";

if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];
    $btn_text  = "Update";
    $btn_action = "update";
} else {
    $prefix = "Pe";
     $unique_id =  unique_id($prefix);
}


if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $table = "expense_entry";
   $main_columns = [
    "company_id",
    "project_id",
    "customer_id",
    "invoice_date",
    "due_date",
    "invoice_no",
    "remarks"
];

    $main_result = $pdo->select([$table, $main_columns], ["unique_id" => $unique_id]);

    if ($main_result->status && !empty($main_result->data)) {
    $main_data     = $main_result->data[0];
    $company_id    = $main_data['company_id'];   
    $project_id    = $main_data['project_id'];   
    $customer_id   = $main_data['customer_id'];
    $invoice_date  = $main_data['invoice_date'];
    $due_date      = $main_data['due_date'];
    $invoice_no    = $main_data['invoice_no'];
    $remarks       = $main_data['remarks'];
}

}


// item_name_list

$item_name_list_options        = item_name_list();
$item_name_list_options        = select_option($item_name_list_options,"Select the Item/Code",$item_name_list);



// Company Name
$company_name_options = company_name();
$company_name_options = select_option($company_name_options,"Select the Company",$company_id);

// Project Name
$project_options  = get_project_name_all();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);

// Customer
$supplier_name_options = customers();
$supplier_name_options = select_option($supplier_name_options,"Select", $customer_id);



$sales_order_options        = sales_order();
$sales_order_options        = select_option($sales_order_options,"Select the Sales Order",$sales_order);


$purchase_requisition_category_options        = purchase_requisition_category();
$purchase_requisition_category_options        = select_option($purchase_requisition_category_options,"Select the Purchase Requisition category",$purchase_requisition_category);


$expense_category_list = expense_category();
$payment_type_list     = payment_type();

// Convert to <option> tags
$category_options = select_option($expense_category_list, "Select Category", $category_id);
$payment_type_options = select_option($payment_type_list, "Select Payment Type", $payment_type_id);




// Product Name
$product_unique_id      = product_name();
$product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

// Unit Name
$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id,"Select", $uom);


$tax_options     = tax();
$tax_options     = select_option($tax_options, "Select Tax", $tax);


$tax_query = $pdo->select(["tax", ["unique_id", "tax_name", "tax_value"]], ["is_delete" => 0]);
$tax_options = "<option value='0'>Select Tax</option>";

if ($tax_query->status && !empty($tax_query->data)) {
    foreach ($tax_query->data as $tax) {
        // Use tax_value (e.g., 5, 12, 18) as value for calculation
        $tax_options .= "<option value='{$tax['tax_value']}' data-id='{$tax['unique_id']}'>{$tax['tax_name']}</option>";
    }
}

$today = date('Y-m-d');
?>

<style>

    /* Clickable FAB items with sublist */
    .fab-toggle {
        color: #e97027;              /* Bootstrap blue */
        cursor: pointer;             /* Hand cursor */
        transition: color 0.2s ease-in-out;
        user-select: none;           /* Prevent text selection */
    }
    .fab-toggle:hover {
        color: #e97027;              /* Darker blue on hover */
        text-decoration: none;  /* Optional */
    }
    
    /* Non-clickable items */
    .no-sublist {
        color: --inherit;                 /* Greyish normal text */
        cursor: default;             /* Normal arrow cursor */
        user-select: none;           /* Still prevent selection */
    }
    .no-sublist:hover {
        color: --inherit;                 /* No color change on hover */
        text-decoration: none;       /* No underline */
    }

    .requisition_sublist_add_btn.btn-primary a,
    .requisition_sublist_add_btn.btn-primary a span {
        color: #fff !important;
    }


</style>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form class="was-validated" id="expense_entry_form">
          <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id ?>">
          <input type="hidden" id="sublist_unique_id" name="sublist_unique_id" value="">

          
        <div class="form-group row">
  <label class="col-md-2 col-form-label labelright">Category Name</label>
  <div class="col-md-3">
    <select name="category_id" id="category_id" class="form-control select2" required>
      <?= $category_options ?>
    </select>
  </div>

  <label class="col-md-2 col-form-label labelright">Payment Type</label>
  <div class="col-md-3">
    <select name="payment_type_id" id="payment_type_id" class="form-control select2" required>
      <?= $payment_type_options ?>
    </select>
  </div>
</div>


<div class="form-group row"> <label class="col-md-2 col-form-label labelright">Customer Name</label> <div class="col-md-3"> <select name="customer_id" id="customer_id" class="form-control select2" required> <?= $supplier_name_options ?> </select> </div>

  <label class="col-md-2 col-form-label labelright">Expense Date</label>
  <div class="col-md-3">
    <input type="date" name="invoice_date" id="invoice_date" class="form-control" value="<?= $invoice_date ?>" required>
  </div>
</div>

<div class="form-group row">
  <label class="col-md-2 col-form-label labelright">Remarks</label>
  <div class="col-md-3">
    <textarea name="remarks" id="remarks_main" class="form-control" rows="2" placeholder="Enter Remarks"><?= htmlspecialchars($remarks ?? '', ENT_QUOTES) ?></textarea>
  </div>
</div>


                
          <!-- Invoice Number -->
            <?php if (!empty($unique_id)) : ?>
              <div class="form-group row">
                <label class="col-md-2 col-form-label labelright">Invoice Number</label>
                <div class="col-md-3">
                  <input type="text" name="invoice_no" id="invoice_no" class="form-control" 
                         value="<?= $invoice_no ?>" readonly>
                </div>
              </div>
            <?php else: ?>
              <!-- Hidden during creation -->
              <input type="hidden" name="invoice_no" id="invoice_no" value="">
            <?php endif; ?>

        </div>


          <hr>

          <!-- Sublist for Invoice Items -->
          <div class="col-12">
            <div class="table-responsive">
              <table id="expense_items_datatable" class="table table-bordered table-striped w-100">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Product Name</th>
                    <th>UOM</th>
                    <th>Qty</th>
                    <th>Rate</th>
                    <th>Discount Type</th> 
                    <th>Discount(%)</th>
                    <th id="tax_title">Tax</th>
                    <th>Amount</th>
                    <!--<th>Delivery Date</th>-->
                    <th>Remarks</th>
                    <th>Action</th>
                  </tr>
                   
                  <tr id="invoice_items_form">
                  <td>#</td>
                  <td>
                    
                   <select id="item_name" name="item_name" class="form-control select2"
                            onchange="fetch_item_details(this.value);">
                      <?= select_option(item_name_list(), "Select Item", "") ?>
                    </select>


                    
                  </td>
                  <td>
                    <select id="unit" name="unit" class="form-control select2">
                      <?= $uom_unique_id ?>
                    </select>
                  </td>
                  <td>
                    <input type="number" id="quantity" name="quantity" class="form-control"
                           placeholder="Qty" onkeyup="calculate_amount();" step="1">
                  </td>
                  <td>
                  <input type="number" id="rate" name="rate" class="form-control"placeholder="Rate"onkeyup="calculate_amount();"step="0.01" min="0">
                </td>

                  <td>
                    <select id="discount_type" name="discount_type" class="form-control" onchange="calculate_amount();">
                      <option value="0">Select Discount Type</option>
                      <option value="1">Percentage (%)</option>
                      <option value="2">Amount (₹)</option>
                    </select>
                  </td>
                  <td>
                    <input type="number" id="discount" name="discount" class="form-control"
                           placeholder="Discount" onkeyup="calculate_amount();" step="1">
                  </td>
                 <td>
                      <select id="tax" name="tax" class="form-control select2" onchange="calculate_amount();">
                        <?= $tax_options ?>
                      </select>
                    </td>

                  <td>
                    <input type="text" id="amount" name="amount" class="form-control" readonly placeholder="Amount">
                  </td>
                  <td>
                    <input type="text" id="remarks" name="remarks" class="form-control" placeholder="Remarks">
                  </td>
                  <td>
                    <button type="button" class="btn btn-success invoice_sublist_add_btn" onclick="expense_item_add_update()">
                      <span id="sublist_btn_text">Add</span>
                    </button>
                  </td>
                </tr>

                  
                  
                </thead>
                <tbody>
                  <!-- Rows will be added dynamically by JS -->
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="col-12">
             
              <div class="row mt-3">
    <div class="col-md-6"></div>
    <div class="col-md-3 text-end">
        <label for="basic">Basic</label>
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" id="basic" name="basic" readonly>
    </div>

    <div class="col-md-6"></div>
    <div class="col-md-3 text-end">
        <label for="total_gst">Total GST</label>
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" id="total_gst" name="total_gst" readonly>
    </div>

    <div class="col-md-6"></div>
    <div class="col-md-3 text-end">
        <label for="roundoff">Round Off</label>
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" id="roundoff" name="roundoff">
    </div>

    <div class="col-md-6"></div>
    <div class="col-md-3 text-end">
        <label for="tot_amount">Total Amount</label>
    </div>
    <div class="col-md-3">
        <input type="text" class="form-control" id="tot_amount" name="tot_amount" readonly>
    </div>
</div>

          </div>

          <div class="col-md-12 text-end">
            <?= btn_cancel($btn_cancel); ?>
            <?= btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>
