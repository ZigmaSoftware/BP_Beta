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
print_r($_GET['unique_id']);
error_log("is_update_mode: " . $is_update_mode . "\n", 3, "grn_debug.log");
if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        error_log("Checking GRN update load for UID: $unique_id", 3, "grn_debug.log");

        $table      =  "lwf_entry";

        $columns    = [
            "project_id",
            "state",
            "amount"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $city_values            = $pdo->select($table_details,$where);
        error_log("City values fetch result: " . print_r($city_values, true), 3, "debug.log");

        if ($city_values->status) {

            $state_id = '';
            $city_values        = $city_values->data;

            
            $project_id           = $city_values[0]["project_id"];
            $state = $city_values[0]["state"] ?? '';
            $amount = $city_values[0]["amount"] ?? '';

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

// $tax = 0;
// $discount = 0;

// if ($is_update_mode) {
//     $po_sc_unique_id = fetch_po_sc_unique_id($purchase_number); // Ensure $purchase_number is valid
//     $td_data = fetch_tax_discount($po_sc_unique_id);

//     if (is_array($td_data) && isset($td_data['tax'], $td_data['discount'])) {
//         $tax = $td_data['tax'];
//         $discount = $td_data['discount'];
//     }
// }

// $company_name_options        = company_name();
// $company_name_options        = select_option($company_name_options,"Select the Company",$company_name);


// $uom_unique_id      = unit_name();
// $uom_unique_id      = select_option($uom_unique_id,"Select", $uom);

// $product_unique_id      = product_name();
// $product_unique_id      = select_option($product_unique_id, "Select", $group_unique_id);

$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);

// $purchase_order_no  = get_po_number();
// $purchase_order_no = select_option($purchase_order_no, "Select Purchase Order No",$purchase_number);

// $gst_paf_options     = select_option(tax(), "Select GST", $gst_paf);
// $gst_freight_options = select_option(tax(), "Select GST", $gst_freight);
// $gst_other_options   = select_option(tax(), "Select GST", $gst_other);

// $supplier_name_options     = supplier($supplier_name);
// error_log("Supplier return for '$supplier_name': " . print_r($supplier_name_options, true), 3, "grn_debug.log");

// // $supplier_name_options     = select_option($supplier_name_options,"Select", $supplier_name);
// if (!empty($supplier_name_options) && is_array($supplier_name_options)) {
//     $supplier_data = $supplier_name_options[0]; // First result

//     $supplier_id = $supplier_data['unique_id'];
//     $supplier_name = $supplier_data['supplier_name'];
// }

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated funnel_form" id="grn_new_form" name="funnel_form">
                    <input type="hidden" id="unique_id" value="<?php echo $_GET['unique_id']; ?>" >
                    <div class ="form-group row">
                        <label class="col-md-4 col-form-label">Project Name</label>
                        <div class="col-md-8">
                            <select name="project_id" id="project_id" class="form-control select2" onchange="get_state(this.value);" required>
                                <?= $project_options ?>
                            </select>
                        </div>
                    </div>
                    <br>
                    <div class ="form-group row">
                        <label class="col-md-4 col-form-label">State</label>
                        <div class="col-md-8">
                            <input type="text" id="state" name="state" class="form-control" value="<?= $state ?>" readonly required>
                        </div>
                    </div>
                    
                    <div class ="form-group row">
                        <label class="col-md-4 col-form-label">Amount</label>
                        <div class="col-md-8">
                            <input type="text" id="amount" name="amount" class="form-control" onkeypress='number_only(event);' value="<?= $amount ?>" required>
                        </div>
                    </div>
                    <div class="form-group row ">
                        <div class="col-md-12">
                            <!-- Cancel,save and update Buttons -->
                            <?php echo btn_cancel($btn_cancel);?>
                            <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                        </div>                                
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  
