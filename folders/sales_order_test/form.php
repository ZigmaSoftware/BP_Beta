<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$company_name      = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        //  $where ='unique_id = "'.$unique_id.'"';

        $table      =  "currency_creation";

        $columns    = [
            "currency_name",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $currency_name  = $result_values[0]["currency_name"];
            $is_active          = $result_values[0]["is_active"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$active_status_options= active_status($is_active);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="currency_name"> Currency Name </label>
                                <div class="col-md-4">
                                    <input type="text" id="currency_name" name="currency_name" class="form-control" value="<?php echo $currency_name; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="is_active"> Active Status</label>
                                <div class="col-md-4">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
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
                
                 <h5>Order Items</h5>
                    <table class="table table-bordered" id="sales_order_items_table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Description</th>
                                <th>Qty</th>
                                <th>Rate</th>
                                <th>Discount Type</th>
                                <th>Discount Value</th>
                                <th>Item Rate</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="order_items_body"></tbody>
                    </table>
                    <button type="button" class="btn btn-sm btn-info" onclick="add_sales_order_item_row()">+ Add Item</button>

                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  