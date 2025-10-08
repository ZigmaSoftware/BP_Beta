<?php include 'function.php'; ?>

<?php
// var_dump($_SESSION);
$today_date = date('Y-m-d');

$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$disable            = "";

// $requisition_number = generate_requisition_number();
$sales_order_no         ="";
$entry_date             ="";
$company_id             ="";
$customer_id            ="";
$currency_id            ="";
$exchange_rate          ="";
$contact_person_name    ="";
$customer_po_no         ="";
$customer_po_date       ="";
$status                 ="";
if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];
    $btn_text  = "Update";
    $btn_action = "update";
} else {
  
$unique_id =  unique_id();
}
if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $disable            = "disabled";
    $unique_id = $_GET['unique_id'];

    $table = "sales_order";

    $main_columns = [
    "sales_order_no",
    "entry_date",
    "company_id",
    "customer_id",
    "currency_id",
    "exchange_rate",
    "contact_person_name",
    "customer_po_no",
    "customer_po_date",
    "status",
    "so_type", 
    "approve_status",
    "approved_by",
    "approve_remarks",
    "is_active",
    "revision_no",         // ✅ added
    "revision_remarks"     // ✅ added
];


    $main_result = $pdo->select([$table, $main_columns], ["unique_id" => $unique_id]);

    if ($main_result->status && !empty($main_result->data)) {
        $main_data = $main_result->data[0];

        $sales_order_no         = $main_data["sales_order_no"];
        $entry_date             = $main_data["entry_date"];
        $company_id             = $main_data["company_id"];
        $customer_id            = $main_data["customer_id"];
        $currency_id            = $main_data["currency_id"];
        $exchange_rate          = $main_data["exchange_rate"];
        $contact_person_name    = $main_data["contact_person_name"];
        $customer_po_no         = $main_data["customer_po_no"];
        $customer_po_date       = $main_data["customer_po_date"];
        $status                 = $main_data["status"];
        $revision_no            = $main_data["revision_no"] + 1;
        $revision_remarks       = $main_data["revision_remarks"];
        $approve_status         = $main_data["approve_status"];
        $approved_by            = $main_data["approved_by"];
        $approve_remarks        = $main_data["approve_remarks"];
        $so_type                = $main_data["so_type"];
        $is_active              = $main_data["is_active"];

        $btn_text  = "Update";
        $btn_action = "update";
    }
}


// $revision_no = "REV" . sprintf("%03d", $revision_no);

$revision_no = $main_data["revision_no"] + 1;
$revision_no = "REV" . sprintf("%03d", $revision_no);

echo $revision_no;


$revision_date = date("Y-m-d");


$active_status_options = active_status($is_active);

$product_unique_id      = product_name();
$product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

//Other Tax Percentage
$currency_options         = currency_creation_name();
$currency_options         = select_option($currency_options,"Select",$currency_id);

// Company Name
$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select", $company_id);

//Supplier Name
$supplier_name_options     = customers();
$supplier_name_options     = select_option($supplier_name_options,"Select", $customer_id);

//Tax Name
$tax_options     = tax();
$tax_options     = select_option($tax_options,"Select");

$approved_by_name     = user_name($approved_by)[0]['user_name'];

// Approve Option
$approve_status_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Not Completed"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Completed"
    ]
];
$approve_status_options    = select_option($approve_status_options,"Select",$status);


$so_type_options = [
        1 => [
            "unique_id" => "1",
            "value" => "product"
        ],
        2 => [
            "unique_id" => "2",
            "value" => "project"
        ],
        3 => [
            "unique_id" => "3",
            "value" => "spare"
        ],
        4 => [
            "unique_id" => "4",
            "value" => "service"
        ],
    ];
$so_type_options    = select_option($so_type_options, "Select", $so_type);


$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id,"Select", $uom);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" id="purchase_requisition_form">
                    <input type="hidden" name="sublist_unique_id" id="sublist_unique_id" value="">
                    <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id ?>">
                    <div class="form-group row">
                        <?php
                        if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
                        ?>
                        <label class="col-md-2 col-form-label textright" for="sale_order_no"> Sales Order No </label>
                        <div class="col-md-3">
                            <input type="text" name="sale_order_no" id="sale_order_no" class="form-control" value="<?php echo $sales_order_no; ?>" disabled>
                        </div>
                        <?php
                        }
                        ?>
                        <label class="col-md-2 col-form-label textright" for="entry_date"> Entry Date </label>
                        <div class="col-md-3">
                            <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo !empty($entry_date) ? $entry_date : $today_date; ?>" required <?= $disable; ?>>
                        </div>
                        
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="company_name"> Company Name </label>
                        <div class="col-md-3">
                            <select name="company_name" id="company_name" class="select2 form-control" required <?= $disable; ?>>
                                <?= $company_name_options; ?>
                            </select>
                        </div>
                        <label class="col-md-2 col-form-label textright" for="customer_name"> Customer Name </label>
                        <div class="col-md-3">
                            <select name="customer_name" id="customer_name" class="select2 form-control" required <?= $disable; ?>>
                                <?= $supplier_name_options; ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group row">
                    <label class="col-md-2 col-form-label textright" for="so_type">SO Type</label>
                    <div class="col-md-3">
                        <select name="so_type" id="so_type" class="select2 form-control" required <?= $disable; ?>>
                        <?= $so_type_options ?>
                    </select>
                    </div>
                </div>


                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="currency"> Currency </label>
                        <div class="col-md-3">
                            <select name="currency" id="currency" class="select2 form-control" required>
                                <?= $currency_options; ?>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label textright" for="exchange_rate"> Exchange Rate </label>
                        <div class="col-md-3">
                            <input type="text" id="exchange_rate" name="exchange_rate" class="form-control" value="<?php echo $exchange_rate; ?>" >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="contact_person_name"> Contact Person Name</label>
                        <div class="col-md-3">
                            <input type="text" id="contact_person_name" name="contact_person_name" class="form-control" value="<?php echo $contact_person_name; ?>" >
                        </div>

                        <label class="col-md-2 col-form-label textright" for="customer_po_no"> Customer PO Number</label>
                        <div class="col-md-3">
                            <input type="text" id="customer_po_no" name="customer_po_no" class="form-control" value="<?php echo $customer_po_no; ?>" >
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="customer_po_date"> Customer PO Date </label>
                        <div class="col-md-3">
                            <!--<input type="date" name="customer_po_date" id="customer_po_date" class="form-control" value="<?php echo $customer_po_date; ?>">-->
                            <input type="date" name="customer_po_date" id="customer_po_date" class="form-control" value="<?php echo !empty($customer_po_date) ? $customer_po_date : $today_date; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label textright" for="status"> Active Status</label>
                        <div class="col-md-3">
                            <select name="status" id="status" class="select2 form-control" required>
                                <?php echo $approve_status_options; ?>
                            </select>
                        </div>
                    </div>
                    
                    
                    <?php if ($approve_status == 1): ?>
                          <div class="form-group row">
                            <label class="col-md-2 col-form-label">Revision No</label>
                            <div class="col-md-4">
                              <input type="text" name="revision_no" id="revision_no" class="form-control"
                                     value="<?= $revision_no ?>" placeholder="Enter Revision Number" readonly>
                            </div>
                        
                            <label class="col-md-2 col-form-label">Revision Date</label>
                            <div class="col-md-4">
                              <input type="date" name="revision_date" id="revision_date"
                                     value="<?= $revision_date ?>" class="form-control" readonly>
                            </div>
                          </div>
                        
                          <div class="form-group row">
                            <label class="col-md-2 col-form-label">Revision Remarks</label>
                            <div class="col-md-4">
                              <textarea name="revision_remarks" id="revision_remarks"
                                        class="form-control" placeholder="Enter Revision Remarks"><?= $revision_remarks ?></textarea>
                            </div>
                          </div>
                        <?php endif; ?>
                    

                    <hr>

                    <div class="col-12">
                        <!-- Table Begins -->
                        <div class="table-responsive">
                        <table id="so_sublist_datatable" class="table table-striped"  style="width:100%">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th id="type_head">Product Name</th>
                                    <th>UOM</th>
                                    <th>Qty</th>
                                    <th>Rate</th>
                                    <!--<th>Discount(%)</th>-->
                                    <th>Tax</th>
                                    <th>Amount</th>
                                    <th>Sub-task</th>
                                    <th><button type="button" class="addRow btn btn-success d-none">Add</button>Action</th>
                                </tr>
<!-- </thead> -->
                                <!-- Input Form Row -->
                                <tr id="requisition_details_form">
                                    <th>#</th>
                                    <th>
                                        <select name="product_unique_id" id="product_unique_id" class="select2 form-control">
                                            <?php echo $product_unique_id; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="uom" id="uom" class="select2 form-control"  >
                                            <?php echo $uom_unique_id; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" id="qty" name="qty" class="form-control" value="" onkeyup='sub_total_amount_2();' onkeypress='number_only(event);' >
                                    </th>
                                    <th>
                                        <input type="text" id="rate" name="rate" class="form-control" value="" onkeyup='sub_total_amount_2();' onkeypress='number_only(event);' >
                                    </th>
                                    <!--<th>-->
                                    <!--    <input type="text" id="discount" name="discount" class="form-control" value="" onkeyup='sub_total_amount_2();' onkeypress='number_only(event);' >-->
                                    <!--</th>-->
                                    <th>
                                        <select name="tax" id="tax" class="select2 form-control"  onchange="get_tax_val(this.value)">
                                            <?php echo $tax_options; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" id="amount" name="amount" class="form-control" value="" onkeypress='number_only(event);' readonly>
                                    </th>
                                    <th>
                                        <input type="text" id="subtask" name="subtask" class="form-control" value="" >
                                    </th>
                                    <th>
                                        <button type="button" class=" btn btn-success waves-effect  waves-light sale_order_add_update_btn" onclick="sale_order_sublist_add_update()">ADD</button>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Filled by DataTable JS -->
                            </tbody>
                        </table>
                        </div>
                        <!-- Table Ends -->
                    </div>
                    
                    <div class="form-group row" style="<?php echo ($approve_status != 0) ? '' : 'display:none;'; ?>">
                        <!-- Approved / Rejected By -->
                        <?php if ($approve_status == 1): ?>
                            <label class="col-md-2 col-form-label textright">Approved By</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control text-success" value="<?= htmlspecialchars($approved_by_name) ?>" readonly>
                            </div>
                        <?php elseif ($approve_status == 2): ?>
                            <label class="col-md-2 col-form-label textright">Rejected By</label>
                            <div class="col-md-3">
                                <input type="text" class="form-control text-danger" value="<?= htmlspecialchars($approved_by_name) ?>" readonly>
                            </div>
                        <?php endif; ?>
                    
                        <!-- Remarks -->
                        <label class="col-md-2 col-form-label textright" for="approval_remarks">Remarks</label>
                        <div class="col-md-3">
                            <textarea id="approval_remarks" name="approval_remarks" class="form-control" rows="2" readonly><?= htmlspecialchars($approve_remarks ?? "") ?></textarea>
                        </div>
                    </div>


                    <div class="col-md-12 btn-action">
                        <!-- Cancel, Save and Update Buttons -->
                       
                        <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                         <?php echo btn_cancel($btn_cancel); ?>
                    </div>


                </form>
            </div>
        </div>
    </div>
</div>