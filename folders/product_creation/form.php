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
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "product_master";

        $columns    = [
            "company_id",
            "group_unique_id",
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

            $company_id             = $result_values[0]["company_id"];
            $group_unique_ids       = $result_values[0]["group_unique_id"];
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

$sub_group_unique_id_sub_list    = sub_group_name();
$sub_group_unique_id_sub_list     = select_option($sub_group_unique_id_sub_list, "Select");


$category_unique_id     = category_name();
$category_unique_id     = select_option($category_unique_id, "Select");

if($_GET['unique_id']){
    $update = 1;
} else {
    $update = 0;
}


?>

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
                            <div class="form-group row ">
                                
                                <label class="col-md-2 col-form-label textright" for="company_id">Company Name</label>
                                <div class="col-md-3">
                                    <select name="company_id_display" id="company_id_display" class="select2 form-control" required <?= $disabled; ?>>
                                        <?php echo $company_options; ?>
                                    </select>
                                </div>
                                
                                <label class="col-md-2 col-form-label textright" for="group_unique_id">Group Name</label>
                                <div class="col-md-3">
                                    <select id="group_unique_id_display" class="select2 form-control" onchange="get_prod_types(this)" required <?= $disabled; ?>>
                                        <?php echo $group_options; ?>
                                    </select>
                                    <input type="hidden" name="group_unique_id" id="group_unique_id" value="<?php echo $group_unique_id; ?>">

                                </div>
                                
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="sub_group_unique_id">Sub Group Name</label>
                                <div class="col-md-3">
                                    <select name="sub_group_unique_id" id="sub_group_unique_id" class="select2 form-control" required <?= $disabled; ?>>
                                        <?php echo $prod_type_list; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="product_name">Product Name</label>
                                <div class="col-md-3">
                                    <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo $product_names; ?>" <?= $readonly; ?> required>
                                </div>
                            </div>


                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="description">Description</label>
                                <div class="col-md-3">
                                    <textarea name="description" id="description" rows="5" class="form-control"> <?php echo $description; ?></textarea>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<div class="col-12">-->
                        <!-- Table Begiins -->
                    <!--    <table id="product_sub_datatable" class="table table-striped dt-responsive nowrap w-100">-->
                    <!--        <thead class="table-light">-->
                    <!--            <tr>-->
                    <!--                <th>#</th>-->
                    <!--                <th>Group Name</th>-->
                    <!--                <th>Sub Group Name</th>-->
                    <!--                <th>Category Name</th>-->
                    <!--                <th>Item Name</th>-->
                    <!--                <th>Qty</th>-->
                    <!--                <th>Unit</th>-->
                    <!--                <th>Remarks</th>-->
                    <!--                <th>Status</th>-->
                    <!--                <th><button type="button" class="addRow btn btn-success d-none">Add</button>Action</th>-->
                    <!--            </tr>-->
                    <!--             <tr id="product_details_form">-->
                    <!--                <th>-->
                    <!--                    #-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <select name="group_unique_id_sub" id="group_unique_id_sub" class="select2 form-control" onchange="get_sub_group(this.value, 1)">-->
                    <!--                        <?php echo $group_options_sub; ?>-->
                    <!--                    </select>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <select name="sub_group_unique_id_sub_list" id="sub_group_unique_id_sub_list" class="select2 form-control" disabled onchange="get_sub_group(this.value, 2)">-->
                    <!--                        <?php echo $sub_group_unique_id_sub_list; ?>-->
                    <!--                    </select>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <select name="category_unique_id_sub" id="category_unique_id_sub" class="select2 form-control" disabled onchange="get_sub_group(this.value, 3)">-->
                    <!--                        <?php echo $category_unique_id; ?>-->
                    <!--                    </select>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <select name="item_unique_id_sub" id="item_unique_id_sub" class="select2 form-control" disabled onchange="get_group_code(this.value)">-->
                    <!--                        <?php echo $item_unique_id; ?>-->
                    <!--                    </select>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <input type="text" id="qty" name="qty" class="form-control" value="" oninput="this.value = this.value.replace(/[^0-9]/g, '')">-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <input type="text" id="uom" name="uom" class="form-control" value="" readonly>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <textarea name="remarks" id="remarks" rows="1" class="form-control"> <?php echo $description; ?></textarea>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <select name="is_active_sub" id="is_active_sub" class="select2 form-control">-->
                    <!--                        <?php echo $active_status_options; ?>-->
                    <!--                    </select>-->
                    <!--                </th>-->
                    <!--                <th>-->
                    <!--                    <button type="button" class="btn btn-success product_creation_add_update_btn" onclick="product_creation_add_update()">ADD</button>-->
                    <!--                </th>-->
                    <!--            </tr>-->
                    <!--        </thead>-->
                               
                    <!--        <tbody>-->

                    <!--        </tbody>-->
                    <!--    </table>-->
                        <!-- Table Ends -->
                    <!--</div>-->
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

        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->