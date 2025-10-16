<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

// $data_type          = "";
$group_unique_id    = "";
$sub_group_unique_id= "";
$category_unique_id = "";
$item_name          = "";
$uom                = "";
$item_code          = "";
$reorder_level      = "";
$reorder_qty        = "";
$hsn_code           = "";


$description        = "";
$is_active              = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "item_master";

        $columns    = [
            // "data_type",
            "group_unique_id",
            "sub_group_unique_id",  
            "category_unique_id",   
            "item_name",            
            "item_code",            
            "uom_unique_id",        
            "reorder_level",        
            "reorder_qty",          
            "hsn_code",
            "purchase_lead_time",
            "tolerance",
            "unit_price",          
            "gst",             
            "qc_approval",
            "qc_final",             
            "description",          
            "is_active",
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
        
        if ($result_values->status) {

            $result_values      = $result_values->data;

            $qc_approval            = $result_values[0]["qc_approval"];
            $qc_final               = $result_values[0]["qc_final"];
            $group_unique_id        = $result_values[0]["group_unique_id"];
            $sub_group_unique_ids   = $result_values[0]["sub_group_unique_id"];
            $category_unique_ids    = $result_values[0]["category_unique_id"];
            $item_name              = $result_values[0]["item_name"];
            $item_code              = $result_values[0]["item_code"];
            $uom                    = $result_values[0]["uom_unique_id"];
            $reorder_level          = $result_values[0]["reorder_level"];
            $reorder_qty            = $result_values[0]["reorder_qty"];
            $hsn_code               = $result_values[0]["hsn_code"];
            $purchase_lead_time     = $result_values[0]["purchase_lead_time"];
            $tolerance              = $result_values[0]["tolerance"];
            $unit_price             = $result_values[0]["unit_price"];
            $gst                    = $result_values[0]["gst"];
            $description            = $result_values[0]["description"];
            $is_active              = $result_values[0]["is_active"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
        
        $sub_group_unique_id      = sub_group_name("",$group_unique_id);
        $category_unique_id      = category_name("","",$sub_group_unique_ids);

        print_r($item_code);
    }
} else {
    $sub_group_unique_id      = sub_group_name();
    $category_unique_id      = category_name();
}


$active_status_options = active_status($is_active);

$group_options      = group_name();
$group_options      = select_option($group_options,"Select the Group Name", $group_unique_id);

$uom_unique_id      = unit_name();
$uom_unique_id      = select_option($uom_unique_id,"Select the Unit Type", $uom);


$sub_group_unique_id      = select_option($sub_group_unique_id, "Select the Sub Group Name", $sub_group_unique_ids);

$category_unique_id      = select_option($category_unique_id, "Select the Category Name", $category_unique_ids);


// $data_type_options  = [
//     1 => [
//         "unique_id" => 1,
//         "value"     => "Consumable",
//     ],
//     2 => [
//         "unique_id" => 2,
//         "value"     => "Component",
//     ]
// ];
// $data_type_options  = select_option($data_type_options, "Select The Type", $data_type);

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
                                <label class="col-md-2 col-form-label textright" for="group_unique_id">Group Name</label>
                                <div class="col-md-3">
                                    <select name="group_unique_id" id="group_unique_id" class="select2 form-control" onchange="get_sub_group(this.value)" required>
                                        <?php echo $group_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="sub_group_unique_id">Sub Group Name</label>
                                <div class="col-md-3">
                                    <select name="sub_group_unique_id" id="sub_group_unique_id" class="select2 form-control" onchange="get_sub_group(this.value, 1)" required>
                                        <?php echo $sub_group_unique_id; ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="category_unique_id">Category Name</label>
                                <div class="col-md-3">
                                    <select name="category_unique_id" id="category_unique_id" class="select2 form-control" onchange="get_code(this.value)" required>
                                        <?php echo $category_unique_id; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="item_name">Item Name</label>
                                <div class="col-md-3">
                                    <input type="text" id="item_name" name="item_name" class="form-control" value="<?php echo $item_name; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="uom">UOM</label>
                                <div class="col-md-3">
                                    <select name="uom" id="uom" class="select2 form-control" onchange="get_code(this.value)" required>
                                        <?php echo $uom_unique_id; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="reorder_level">Reorder Level</label>
                                <div class="col-md-3">
                                    <input type="text" id="reorder_level" name="reorder_level" class="form-control" value="<?php echo $reorder_level; ?>" >
                                </div>
                            </div>
                            <!--<div class="col-md-1">-->
                                <!--    <button type="button" class = "btn btn-primary btn-block category_btn " onclick = "create_category()">Create <i class="mdi mdi-plus-circle-multiple"></i></button>-->
                            <!--</div>-->
                            <!--<label class="col-md-2 col-form-label" for="item_code">Item Code</label>-->
                            <!--<div class="col-md-4">-->
                                   <input type="hidden" id="item_code" name="item_code" class="form-control" value="<?php echo $item_code; ?>" >
                            <!--</div>-->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="reorder_qty">Reorder Qty</label>
                                <div class="col-md-3">
                                    <input type="text" id="reorder_qty" name="reorder_qty" class="form-control" value="<?php echo $reorder_qty; ?>" >
                                </div>
                                <label class="col-md-2 col-form-label textright" for="hsn_code">HSN Code</label>
                                <div class="col-md-3">
                                    <input type="text" id="hsn_code" name="hsn_code" class="form-control" value="<?php echo $hsn_code; ?>" >
                                </div>
                            </div>

                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="purchase_lead_time">Purchase Lead Time</label>
                                <div class="col-md-3">
                                    <input type="text" id="purchase_lead_time" name="purchase_lead_time" class="form-control" onkeypress='number_only(event);' value="<?php echo $purchase_lead_time; ?>" >
                                </div>
                                <label class="col-md-2 col-form-label textright" for="tolerance">Tolerance (%)</label>
                                <div class="col-md-3">
                                    <input type="text" id="tolerance" name="tolerance" class="form-control" onkeypress='number_only(event);' value="<?php echo $tolerance; ?>" >
                                </div>
                            </div>
                            
                            
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="unit_price">Unit Price</label>
                                <div class="col-md-3">
                                    <input type="text" id="unit_price" name="unit_price" class="form-control" onkeypress="number_only(event);" value="<?php echo $unit_price; ?>">
                                </div>
                                <label class="col-md-2 col-form-label textright" for="gst">Tax (GST %)</label>
                                <div class="col-md-3">
                                    <input type="text" id="gst" name="gst" class="form-control" onkeypress="number_only(event);" value="<?php echo $gst; ?>">
                                </div>
                            </div>
                            
                            

                            <div class="form-group row">
                                <!--<label class="col-md-2 col-form-label textright" for="qc_approval">QC Approval</label>-->
                                <!--<div class="col-md-3">-->
                                <!--    <div class="form-check mt-2 d-flex gap-3 align-items-center">-->
                                <!--        <div>-->
                                <!--            <input type="checkbox" id="qc_approval" name="qc_approval" class="form-check-input"-->
                                <!--                <?php if (!empty($qc_approval)) echo 'checked'; ?>>-->
                                <!--            <label class="form-check-label" for="qc_approval">Internal</label>-->
                                <!--        </div>-->
                                <!--        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
                                <!--        <div>-->
                                <!--            <input type="checkbox" id="qc_approval_final" name="qc_approval_final" class="form-check-input"-->
                                <!--                <?php if (!empty($qc_final)) echo 'checked'; ?>>-->
                                <!--            <label class="form-check-label" for="qc_approval_final">Final</label>-->
                                <!--        </div>-->

                                <!--    </div>-->
                                <!--</div>-->
                                <label class="col-md-2 col-form-label textright" for="is_active">Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="description">Description</label>
                                <div class="col-md-3">
                                    <textarea name="description" id="description" rows="5" class="form-control" > <?php echo $description; ?></textarea>
                                </div>
                            </div>

                            <!--<div class="form-group row ">-->
                            <!--    <label class="col-md-2 col-form-label textright" for="description">Description</label>-->
                            <!--    <div class="col-md-3">-->
                            <!--        <textarea name="description" id="description" rows="5" class="form-control" > <?php echo $description; ?></textarea>-->
                            <!--    </div>-->
                            <!--</div>-->
                            
                                <div class="col-md-12 btn-action">
                                    <!-- Cancel,save and update Buttons -->
                                 
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                       <?php echo btn_cancel($btn_cancel);?>
                                </div>
                                
                            </div>
                    </div>
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  

<?php include "group_model.php"; ?>
