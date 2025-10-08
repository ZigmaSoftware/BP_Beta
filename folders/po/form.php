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
$group_unique_id = "682c6648c9e8d96641";

$screen_unique_id  = unique_id("scr");

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "product_master";

        $columns    = [
            "group_unique_id",
            "sub_group_unique_id",
            "product_name",
            "description",
            "is_active",
            "screen_unique_id"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values          = $result_values->data;

            $group_unique_id       = $result_values[0]["group_unique_id"];
            $sub_group_unique_id   = $result_values[0]["sub_group_unique_id"];
            $screen_unique_id       = $result_values[0]["screen_unique_id"];
            $product_names          = $result_values[0]["product_name"];
            $description            = $result_values[0]["description"];

            $is_active              = $result_values[0]["is_active"];

            $readonly = "";
            $disabled = "";


            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
        // $sub_group_unique_id    = sub_group_name("", $group_unique_id);
    }
} 

$active_status_options = active_status($is_active);

$group_options      = group_name();
$group_options      = select_option($group_options, "Select", $group_unique_id);

$sub_group_options      = sub_group_name("", $group_unique_id);;
$sub_group_options      = select_option($sub_group_options, "Select",$sub_group_unique_id);

$group_options_sub      = group_name("","",$group_unique_id);
$group_options_sub      = select_option($group_options_sub, "Select");

$sub_group_unique_id_sub_list      = sub_group_name();
$sub_group_unique_id_sub_list      = select_option($sub_group_unique_id_sub_list, "Select");

$category_sublist_options  = category_name();
$category_sublist_options  = select_option($category_sublist_options,"Select");
        
$item_unique_id  = category_item();
$item_unique_id  = select_option($item_unique_id,"Select"); 

$unit_name  = unit("",$item_id);
$unit_id  = $unit_name[0]['unit_name']."@@".$unit_name[0]['unique_id'];
        
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <form class="was-validated"  autocomplete="off" >
                        <div id="product_details_main_form">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <div class="col-md-4">
                                    <input type="hidden" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>" >
                                    <input type="hidden" id="screen_unique_id" name="screen_unique_id" class="form-control" value="<?php echo $screen_unique_id; ?>" required >

                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="group_unique_id">Group Name</label>
                                <div class="col-md-4">
                                    <select name="group_unique_id" id="group_unique_id" class="select2 form-control" onchange="get_sub_group_name(),get_sublist_group_name()"  <?= $disabled; ?> required>
                                        <?php echo $group_options; ?>
                                    </select>
                                </div>
                               

                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="sub_group_unique_id">Sub Group Name</label>
                                <div class="col-md-4">
                                    <select name="sub_group_unique_id" id="sub_group_unique_id" class="select2 form-control"  <?= $disabled; ?> required>
                                        <?php echo $sub_group_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="product_name">Product Name</label>
                                <div class="col-md-4">
                                    <input type="text" id="product_name" name="product_name" class="form-control" value="<?php echo $product_names; ?>" <?= $readonly; ?> required>
                                </div>
                            </div>


                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="description">Description</label>
                                <div class="col-md-4">
                                    <textarea name="description" id="description" rows="5" class="form-control" required><?php echo $description; ?></textarea>
                                </div>
                                <label class="col-md-2 col-form-label" for="is_active"> Active Status</label>
                                <div class="col-md-4">
                                    <select name="is_active" id="is_active" class="select2 form-control" required >
                                        <?php echo $active_status_options; ?>
                                    </select>
                                </div>
                            </div>
                            </div>
                       </form>
                    </div>
                    <div class="col-12">
                        <!-- Table Begiins -->
                        <table id="po_sub_datatable" class="table dt-responsive nowrap w-100">
                            <thead>
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
                                    <th><button type="button" class="addRow btn btn-success d-none">Add</button>Action</th>
                                </tr>
                                <tr id="product_details_form">
                                    <th>
                                        #
                                    </th>
                                    <th>
                                        <select name="group_unique_id_sub" id="group_unique_id_sub" class="select2 form-control" onchange="get_sublist_sub_group(this.value)">
                                            <?php echo $group_options_sub; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="sub_group_unique_id_sub" id="sub_group_unique_id_sub" class="select2 form-control"  onchange="get_sublist_category(this.value)">
                                            <?php echo $sub_group_unique_id_sub_list; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="category_unique_id_sub" id="category_unique_id_sub" class="select2 form-control"  onchange="get_sublist_item(this.value)">
                                            <?php echo $category_unique_id; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <select name="item_unique_id_sub" id="item_unique_id_sub" class="select2 form-control"  onchange="get_unit_name(this.value)">
                                            <?php echo $item_unique_id; ?>
                                        </select>
                                    </th>
                                    <th>
                                        <input type="number" min="0" id="qty" name="qty" class="form-control" value="">
                                    </th>
                                    <th>
                                        <input type="text" id="unit" name="unit" class="form-control" value="" readonly>
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
                                        <button type="button" class=" btn btn-success waves-effect  waves-light product_sub_add_update_btn" onclick="product_sub_add_update()"><a href="javascript: void(0);">ADD</a></button>
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

        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->