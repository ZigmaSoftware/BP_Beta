<!-- This file Only PHP Functions -->
<?php
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// register_shutdown_function(function () {
//     $error = error_get_last();
//     if ($error) {
//         echo "<pre>FATAL: " . print_r($error, true) . "</pre>";
//     }
//     echo "<!-- shutdown reached -->";
// });

// Required DB config files
include '../../config/dbconfig.php';
include '../../config/new_db.php';
include '../../config/Db.class.php';

// Default form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$sub_group_unique_id = "";
$product_name       = "";
$readonly           = "";
$disabled           = "";
$description        = "";
$is_active          = 1;

// Get last group unique id
$table_group    = 'group_product_master';
$columns        = ["group_unique_id"];
$table_details  = [$table_group, $columns];

$result = $pdo->select($table_details, "", "", "ORDER BY id DESC LIMIT 1");
if ($result->status) {
    foreach ($result->data as $value) {
        $group_unique_id = $value['group_unique_id'];
    }
}

// If editing existing product
if (!empty($_GET["unique_id"])) {
    $unique_id = $_GET["unique_id"];
    $where     = ["prod_unique_id" => $unique_id];

    $table     = "product_sublist";
    $columns   = [
        "group_unique_id",
        "sub_group_unique_id",
        "category_unique_id",
        "item_unique_id",
        "material_type",   // ✅ fetch material type
        "qty",
        "uom_unique_id",
        "remarks",
        "is_active",
    ];
    $table_details = [$table, $columns];

    $result_values = $pdo->select($table_details, $where);
    
    $data = [];

    if ($result_values->status) {
        $data = $result_values->data[0];
        
        // print_r($data);

        $group_unique_ids       = $data["group_unique_id"];
        $sub_group_unique_ids   = $data["sub_group_unique_id"];
        $category_unique_id     = $data["category_unique_id"];
        $item_unique_id         = $data["item_unique_id"];
        $material_type          = $data["material_type"];

        $qty                    = $data["qty"];
        $uom_unique_id          = $data["uom_unique_id"];
        $remarks                = $data["remarks"];
        $is_active              = $data["is_active"];

        $readonly = "readonly";
        $disabled = "disabled";
        $btn_text = "Update";
        $btn_action = "update";
    } else {
        $btn_text = "Error";
        $btn_action = "error";
        $is_btn_disable = "disabled='disabled'";
    }
} else {
    // For new entry
    $unique_id = unique_id($prefix);
}

// print_r($data);

// Dropdown options
$active_status_options         = active_status($is_active);
$group_options_sub              = select_option(group_name("", "", $group_unique_id), "Select", $group_unique_ids);
$uom_unique_id                  = select_option(unit_name(), "Select", $uom_unique_id);
$item_unique_id                 = select_option(category_item(), "Select", $item_unique_id);
$sub_group_unique_id_sub_list   = select_option(sub_group_name(), "Select", $sub_group_unique_ids);
$category_unique_id             = select_option(category_name(), "Select", $category_unique_id);

// print_r($group_options_sub);

if($_GET['unique_id']){
    $product_name_list      = product_name();
    $product_name_list      = select_option($product_name_list, "Select Product", $_GET['unique_id']);
} else {
    $product_name_list      = product_name();
    $product_name_list      = select_option($product_name_list, "Select Product");
}
$semi_finished_rows  = product_name_semi_finished(); // optionally pass $group_unique_ids, $sub_group_unique_ids, $company_id
$semi_finished_list  = select_option($semi_finished_rows, "Select Semi-Finished", $_GET['unique_id']);


if($_GET['unique_id']){
    $update = 1;
    $disabled = 'disabled';
} else {
    $update = 0;
    $disabled = '';
}

?>

<style>

    a {
        color: inherit;           /* inherit normal text color */
        text-decoration: none;    /* remove underline */
    }

    a:hover {
        color: #e96f26;           /* hover color */
    }

</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Hidden Fields -->
                <div class="form-group row">
                    <div class="col-md-3">
                        <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id; ?>" required>
                        <input type="hidden" id="update" name="update" value="<?= $update; ?>" required>
                    </div>
                </div>

               <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="material_type">Material Type</label>
                                <div class="col-md-3">
                                <select name="material_type" id="material_type" class="form-control" <?= $disabled; ?> onchange="toggleMaterialFields()">
                                    <option value="">Select Material Type</option>
                                    <option value="product" <?= ($material_type == 'product') ? 'selected' : '' ?>>Product</option>
                                    <option value="semi_finished" <?= ($material_type == 'semi_finished') ? 'selected' : '' ?>>Semi-Finished</option>
                                </select>
                                </div>
                            </div>


                          <div class="form-group row" id="product_name_row" style="display:none;">
                            <label class="col-md-2 col-form-label textright" for="product_name">Product Name</label>
                            <div class="col-md-3">
                                <select name="product_name" id="product_name" class="select2 form-control" <?= $disabled; ?>>
                                    <?php echo $product_name_list; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-group row" id="semi_finished_row" style="display:none;">
                            <label class="col-md-2 col-form-label textright" for="semi_finished_item">Semi-Finished Item</label>
                            <div class="col-md-3">
                                <select name="semi_finished_item" id="semi_finished_item" class="select2 form-control" <?= $disabled; ?>>
                                    <?php echo $semi_finished_list; ?>
                                </select>
                            </div>
                        </div>


                <!-- Product Table -->
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
                            <!--<th>-->
                            <!--    <button type="button" class="addRow btn btn-success d-none">Add</button>-->
                            <!--    Action-->
                            <!--</th>-->
                        </tr>
                        <!--<tr id="product_details_form">-->
                        <!--    <th>#</th>-->
                        <!--    <th>-->
                        <!--        <select name="group_unique_id_sub" id="group_unique_id_sub" class="select2 form-control" onchange="get_sub_group(this.value, 1)">-->
                        <!--            <?= $group_options_sub; ?>-->
                        <!--        </select>-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <select name="sub_group_unique_id_sub_list" id="sub_group_unique_id_sub_list" class="select2 form-control" disabled onchange="get_sub_group(this.value, 2)">-->
                        <!--            <?= $sub_group_unique_id_sub_list; ?>-->
                        <!--        </select>-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <select name="category_unique_id_sub" id="category_unique_id_sub" class="select2 form-control" disabled onchange="get_sub_group(this.value, 3)">-->
                        <!--            <?= $category_unique_id; ?>-->
                        <!--        </select>-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <select name="item_unique_id_sub" id="item_unique_id_sub" class="select2 form-control" disabled onchange="get_group_code(this.value)">-->
                        <!--            <?= $item_unique_id; ?>-->
                        <!--        </select>-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <input type="text" id="qty" name="qty" class="form-control" value=<?= $qty ?> oninput="this.value = this.value.replace(/[^0-9]/g, '')">-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <input type="text" id="uom" name="uom" class="form-control"  readonly>-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <textarea name="remarks" id="remarks" rows="1" class="form-control"><?= $description; ?></textarea>-->
                        <!--    </th>-->
                        <!--    <th>-->
                        <!--        <select name="is_active_sub" id="is_active_sub" class="select2 form-control">-->
                        <!--            <?= $active_status_options; ?>-->
                        <!--        </select>-->
                        <!--    </th>-->
                            <!--<th>-->
                            <!--    <button type="button" class="btn btn-success standard_bom_add_update_btn" onclick="standard_bom_add_update()">ADD</button>-->
                            <!--</th>-->
                        <!--</tr>-->
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
