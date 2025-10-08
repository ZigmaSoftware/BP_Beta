<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php

$today_date = date('Y-m-d');

// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";
$sub_group_unique_id = "";
$product_name       = "";
$readonly           = "";
$disabled           = "";

$description        = "";
$is_active              = 1;


if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "product_master";

        $columns    = [
            "sub_group_unique_id",
            "product_name",
            "description",
            "is_active",
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values          = $result_values->data;

            $sub_group_unique_ids   = $result_values[0]["sub_group_unique_id"];
            $product_names          = $result_values[0]["product_name"];
            $description            = $result_values[0]["description"];

            $is_active              = $result_values[0]["is_active"];

            $readonly = "readonly";
            $disabled = "disabled";


            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
        
    }
} else {

    $unique_id              = unique_id($prefix);
}


$active_status_options = active_status($is_active);

$product_unique_id      = product_name();
$product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

//Other Tax Percentage
$currency_options         = currency_creation_name();
$currency_options         = select_option($currency_options,"Select",$currency);

// Company Name
$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select",$company_name);

//Supplier Name
$supplier_name_options     = supplier();
$supplier_name_options     = select_option($supplier_name_options,"Select",$supplier_id);

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


$approve_status_options    = select_option($approve_status_options,"Select",$approve_status);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="sales_order_main_data">
                    <div class="form-group row ">
                        <div class="col-md-4">
                            <input type="text" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>" >
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="purchase_order_no"> Sales Order No </label>
                        <div class="col-md-4">
                            <!--<input type="text" name="purchase_order_no" id="purchase_order_no" class="form-control" value="<?php echo $purchase_order_no; ?>" required>-->
                        </div>

                        <label class="col-md-2 col-form-label" for="company_name"> Company Name </label>
                        <div class="col-md-4">
                            <select name="company_name" id="company_name" class="select2 form-control" required>
                                <?= $company_name_options; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="entry_date"> Entry Date </label>
                        <div class="col-md-4">
                            <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo isset($entry_date) ? $entry_date : $today_date; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label" for="customer_name"> Customer Name </label>
                        <div class="col-md-4">
                            <select name="customer_name" id="customer_name" class="select2 form-control" required>
                                <?= $supplier_name_options; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="currency"> Currency </label>
                        <div class="col-md-4">
                            <select name="currency" id="currency" class="select2 form-control" required>
                                <?= $currency_options; ?>
                            </select>
                        </div>

                        <label class="col-md-2 col-form-label" for="exchange_rate"> Exchange Rate </label>
                        <div class="col-md-4">
                            <input type="text" id="exchange_rate" name="exchange_rate" class="form-control" value="<?php echo $exchange_rate; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="contact_person_name"> Contact Person Name</label>
                        <div class="col-md-4">
                            <input type="text" id="contact_person_name" name="contact_person_name" class="form-control" value="<?php echo $contact_person_name; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label" for="customer_po_no"> Customer PO Number</label>
                        <div class="col-md-4">
                            <input type="text" id="customer_po_no" name="customer_po_no" class="form-control" value="<?php echo $customer_po_no; ?>" required>
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="customer_po_date"> Customer PO Date </label>
                        <div class="col-md-4">
                            <!--<input type="date" name="customer_po_date" id="customer_po_date" class="form-control" value="<?php echo $customer_po_date; ?>">-->
                            <input type="date" name="customer_po_date" id="customer_po_date" class="form-control" value="<?php echo isset($customer_po_date) ? $customer_po_date : $today_date; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label" for="status"> Active Status</label>
                        <div class="col-md-4">
                            <select name="status" id="status" class="select2 form-control" required>
                                <?php echo $approve_status_options; ?>
                            </select>
                        </div>
                    </div>

                </div>
                <div class="col-12">
                    <!-- Table Begiins -->
                    <table id="sale_order_sub_datatable" class="table dt-responsive nowrap w-100">
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
                                <th><button type="button" class="addRow btn btn-success d-none">Add</button>Action</th>
                            </tr>
                            <tr id="sale_order_sublist_data">
                                <th>
                                    #
                                </th>
                                <th>
                                    <select name="product_unique_id" id="product_unique_id" class="select2 form-control"  >
                                        <?php echo $product_unique_id; ?>
                                    </select>
                                </th>
                                <th>
                                    <input type="text" id="uom" name="uom" class="form-control" value="" >
                                </th>
                                <th>
                                    <input type="text" id="qty" name="qty" class="form-control" value="" >
                                </th>
                                <th>
                                    <input type="text" id="rate" name="rate" class="form-control" value="" >
                                </th>
                                <th>
                                    <input type="text" id="discount" name="discount" class="form-control" value="" >
                                </th>
                                <th>
                                    <input type="text" id="tax" name="tax" class="form-control" value="" >
                                </th>
                                <th>
                                    <input type="text" id="amount" name="amount" class="form-control" value="" >
                                </th>
                                <th>
                                    <button type="button" class=" btn btn-success waves-effect  waves-light sale_order_creation_add_update_btn" onclick="sale_order_creation_add_update()">ADD</button>
                                </th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                    <!-- Table Ends -->
                </div>
                <div class="col-md-12">
                    <!-- Cancel,save and update Buttons -->
                    <?php echo btn_cancel($btn_cancel); ?>
                    <?php 
                    if (!empty($_GET["unique_id"])) {
                        echo btn_createupdate($folder_name_org, $unique_id, $btn_text); 
                    }else {
                        echo btn_createupdate($folder_name_org,$unique_ids, $btn_text); 
                    }
                    ?>
                </div>

            </div>
        </div>
    </div>

</div> <!-- end card-body -->
</div> <!-- end card -->
</div><!-- end col -->
</div>