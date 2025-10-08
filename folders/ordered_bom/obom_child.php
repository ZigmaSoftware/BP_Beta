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
        $so_id      = $_GET['so_unique_id'];
        $type       = $_GET['type'] ?? '';
        $where      = [
            "prod_unique_id" => $unique_id,
            "so_unique_id"  => $so_id
        ];
        
        if(!empty($type)){
            $where['type'] = $type;
        }

        $table = "obom_child_table";

        $columns = [
            "DISTINCT material_type",
            "parent_unique_id",
            "so_unique_id",
            "prod_unique_id",
            "group_unique_id",
            "sub_group_unique_id",
            "category_unique_id",
            "item_unique_id",
            "qty",
            "uom_unique_id",
            "remarks",
            "is_active"
        ];
        
        $table_details = [
            $table,
            $columns
        ];

        $result_values = $pdo->select($table_details, $where);
        
        // print_r($result_values);

        if ($result_values->status && !empty($result_values->data)) {
        
            $result_values = $result_values->data;
        
            $material_type          = $result_values[0]["material_type"];
            $parent_unique_id       = $result_values[0]["parent_unique_id"];
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
        
            $btn_text   = "Update";
            $btn_action = "update";
        } else {
            $btn_text       = "Error";
            $btn_action     = "error";
            $is_btn_disable = "disabled='disabled'";
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

$so_num_list         = sales_order();
$so_num_list         = select_option($so_num_list, "Select", $so_id);

$sub_group_unique_id_sub_list    = sub_group_name();
$sub_group_unique_id_sub_list     = select_option($sub_group_unique_id_sub_list, "Select");

// print_r($group_options_sub);

$item_name = item_name_list($_GET["unique_id"])[0]['item_name'];


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
                <div class="row">
                    <div class="col-12">
                        <div id="product_details_main_form">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <div class="col-md-3">
                                    <input type="hidden" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>" required>
                                    <input type="hidden" id="update" name="update" class="form-control" value="<?php echo $update; ?>" required>
                                    <input type="hidden" id="so_id" name="so_id" class="form-control" value="<?php echo $so_id; ?>" required>
                                    <input type="hidden" id="prod_unique_id" name="prod_unique_id" class="form-control" value="<?php echo $prod_unique_id; ?>" required>
                                    <input type="hidden" id="parent_unique_id" name="parent_unique_id" class="form-control" value="<?php echo $parent_unique_id; ?>" required>
                                    <input type="hidden" id="type" name="type" class="form-control" value="<?php echo $type; ?>" required>
                                    <input type="hidden" id="to_copy" name="to_copy" class="form-control" value="0" required>

                                </div>
                            </div>


                          <div id="product_name_row">
                            <div class="form-group row" >
                                <label class="col-md-2 col-form-label textright" for="product_name">Sales Order</label>
                                <div class="col-md-3">
                                    <select name="sales_order_no" id="sales_order_no" class="select2 form-control" <?= $disabled; ?> onchange="get_so_prod(this.value);">
                                        <?php echo $so_num_list; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row">
                                
                                <label class="col-md-2 col-form-label textright" for="product_name">Semi-Finished Item Name</label>
                                <div class="col-md-3">
                                    <input type="text" name="product_name" id="product_name" class="form-control" value="<?= $item_name ?>" readonly>
                                </div>
                            </div>
                        </div>


                            
                        </div>
                    </div>
                    <div class="col-12">
                         <!--Table Begiins -->
                        <table id="product_sub_datatable_child" class="table table-striped dt-responsive nowrap w-100">
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
                                    <th>Action</th>
                                </tr>
                                                                 <tr id="product_details_form">
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        <select name="group_unique_id_sub" id="group_unique_id_sub" class="select2 form-control" onchange="get_sub_group(this.value, 1)">
                                            <?php echo $group_options_sub; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="sub_group_unique_id_sub_list" id="sub_group_unique_id_sub_list" class="select2 form-control" disabled onchange="get_sub_group(this.value, 2)">
                                            <?php echo $sub_group_unique_id_sub_list; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="category_unique_id_sub" id="category_unique_id_sub" class="select2 form-control" disabled onchange="get_sub_group(this.value, 3)">
                                            <?php echo $category_unique_id; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="item_unique_id_sub" id="item_unique_id_sub" class="select2 form-control" disabled onchange="get_group_code(this.value)">
                                            <?php echo $item_unique_id; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="text" id="qty" name="qty" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                    </th>
                                    <th>
                                        <input type="text" id="uom" name="uom" class="form-control" value="" readonly>
                                    </th>
                                    <th>
                                        <textarea name="remarks" id="remarks" rows="1" class="form-control"> <?php echo $description; ?></textarea>
                                    </th>
                                    <th>
                                        <select name="is_active_sub" id="is_active_sub" class="select2 form-control">
                                            <?php echo $active_status_options; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <button type="button" class="btn btn-success ordered_bom_add_update_btn" onclick="child_obom_add_update()">ADD</button>
                                    </th>
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