<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php

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

$table_group    = 'group_product_master';
$columns    = [
    "group_unique_id"
];

$table_details   = [
    $table_group,
    $columns
];
$result = $pdo->select($table_details, "", "", "ORDER BY id DESC LIMIT 1");
if ($result->status) {
    $res_array      = $result->data;
    foreach ($res_array as $key => $value) {
        $group_unique_id = $value['group_unique_id'];
    }
}
if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $type       = $_GET["type"] ?? "";   // ✅ fallback empty if not set

        $where = [
            "so_unique_id" => $unique_id
        ];

        // ✅ If type exists and not empty, add to where
        if (!empty($type)) {
            $where["type"] = $type;
        }

        $table      =  "obom_list";

        $columns    = [
            "type",
            "so_unique_id",
            "prod_unique_id",
            "group_unique_id",
            "sub_group_unique_id",
            "category_unique_id",
            "item_unique_id",
            "qty",
            "uom_unique_id",
            "remarks",
            "is_active",
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values          = $result_values->data;

            $type                   = $result_values[0]["type"];
            $so_id                  = $result_values[0]["so_unique_id"];
            $prod_unique_id         = $result_values[0]["prod_unique_id"];
            $group_unique_ids       = $result_values[0]["group_unique_id"];
            $sub_group_unique_ids   = $result_values[0]["sub_group_unique_id"];
            $category_unique_id     = $result_values[0]["category_unique_id"];
            $item_unique_id         = $result_values[0]["item_unique_id"];
            $qty                    = $result_values[0]["qty"];
            $uom_unique_id          = $result_values[0]["uom_unique_id"];
            $remarks                = $result_values[0]["remarks"];
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
        $prod_type_list    = product_type_name($sub_group_unique_ids);
    }
} else {
    $prod_type_list    = product_type_name("", $group_unique_ids);

    $unique_id              = unique_id($prefix);
}



$active_status_options = active_status($is_active);

$group_options      = product_group_name();
$group_options      = select_option($group_options, "Select", $group_unique_ids);

$group_options_sub      = group_name("", "", $group_unique_id);
$group_options_sub      = select_option($group_options_sub, "Select");

$prod_type_list = select_option($prod_type_list, "Select", $sub_group_unique_ids);

$company_options = company_name();
$company_options = select_option($company_options, "Select", $company_id);

$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id, "Select");

$item_unique_id      = category_item();
$item_unique_id      = select_option($item_unique_id, "Select");

if($_GET['unique_id']){
    $so_num_list     = sales_order();
} else {
    $so_num_list     = sales_order_type(); // ready to inject into <select>
    $so_num_list = array_filter($so_num_list, function($so){
        return isset($so['so_type']) && $so['so_type'] != 0;
    });
    $so_num_list = array_values($so_data); // re-index
}
$so_num_list         = select_option($so_num_list, "Select", $so_id);

$sub_group_unique_id_sub_list    = sub_group_name();
$sub_group_unique_id_sub_list     = select_option($sub_group_unique_id_sub_list, "Select");

// print_r($group_options_sub);


$category_unique_id     = category_name();
$category_unique_id     = select_option($category_unique_id, "Select");

if($_GET['unique_id']){
    $product_name_list      = product_name();
    $product_name_list      = select_option($product_name_list, "Select Product", $_GET['unique_id']);
} else {
    $product_name_list      = product_name();
    $product_name_list      = select_option($product_name_list, "Select Product");
}
$semi_finished_rows  = product_name_semi_finished(); // optionally pass $group_unique_ids, $sub_group_unique_ids, $company_id
$semi_finished_list  = select_option($semi_finished_rows, "Select Semi-Finished");


if($_GET['unique_id']){
    $update = 1;
    $disabled = 'disabled';
} else {
    $update = 0;
    $disabled = '';
}


?>

<?php
$type = $_GET['type'] ?? '';
?>

<style>
    <?php if ($type == 2): ?>
        a {
            color: #e96f26;
            text-decoration: none;
            pointer-events: auto;  /* enable clicks */
        }
        a:hover {
            color: #e96f26;
        }
    <?php else: ?>
        a {
            color: inherit;           /* grey out */
            text-decoration: none;
            pointer-events: none;  /* disable click */
            cursor: default;       /* no hand cursor */
        }
    <?php endif; ?>
</style>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <div id="product_details_main_form">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <div class="col-md-3">
                                    <input type="hidden" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>" required>
                                    <input type="hidden" id="update" name="update" class="form-control" value="<?php echo $update; ?>" required>

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="material_type">Material Type</label>
                                <div class="col-md-3">
                                    <select name="type" id="type" class="form-control" required <?= $_GET["unique_id"] ? 'disabled' : ''; ?>>
                                        <option value="0" <?php echo $type == 0 ? 'selected' : ''; ?>>Select Type</option>
                                        <option value="1" <?php echo $type == 1 ? 'selected' : ''; ?>>With Materials</option>
                                        <option value="2" <?php echo $type == 2 ? 'selected' : ''; ?>>Without Materials</option>
                                    </select>
                                </div>
                            </div>

                        <div class="form-group row" id="so_row">
                            <label class="col-md-2 col-form-label textright" for="sales_order_no">Sales Order</label>
                            <div class="col-md-3">
                                <select name="sales_order_no" id="sales_order_no" class="select2 form-control" <?= $disabled; ?> onchange="get_so_prod(this.value);">
                                    <?php echo $so_num_list; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row" id="product_row" style="display:none;">
                            <label class="col-md-2 col-form-label textright" id="product_row_label">Product Name</label>
                            <div class="col-md-3">
                                <input type="text" name="product_display" id="product_display" class="form-control" value="" readonly>
                                <input type="hidden" name="product_id" id="product_id" class="form-control" value="" readonly>
                            </div>
                        </div>



                            
                        </div>
                    </div>
                    <div class="col-12">
                         <!--Table Begiins -->
                        <table id="product_sub_datatable" class="table table-striped dt-responsive nowrap w-100">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Group Name</th>
                                    <th>Sub Group Name</th>
                                    <th>Category Name</th>
                                    <th>Item Name</th>
                                    <th>Qty</th>
                                    <th>Unit</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                               
                            <tbody>

                            </tbody>
                        </table>
                         <!--Table Ends -->
                    </div>
                </div>

            </div>

        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->