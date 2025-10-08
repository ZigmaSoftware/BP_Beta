<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$group_unique_id        = "";
$sub_group_unique_id    = "";
$category_name          = "";
$code                   = "";
$description            = "";
$is_active              = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "category_master";

        $columns    = [
            "group_unique_id",
            "sub_group_unique_id",
            "category_name",
            "code",
            "description",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $group_unique_id         = $result_values[0]["group_unique_id"];
            $sub_group_unique_ids     = $result_values[0]["sub_group_unique_id"];
            $category_name           = $result_values[0]["category_name"];
            $code                    = $result_values[0]["code"];
            $part                    = explode('-',$code);
            $description             = $result_values[0]["description"];
            $is_active               = $result_values[0]["is_active"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
        
        $sub_group_unique_id      = sub_group_name("",$group_unique_id);
    }
} else {
    $sub_group_unique_id      = sub_group_name();
}


$active_status_options = active_status($is_active);

$group_options      = group_name();
$group_options      = select_option($group_options,"Select the Group Name", $group_unique_id);


$sub_group_unique_id      = select_option($sub_group_unique_id, "Select the Sub Group Name", $sub_group_unique_ids);

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
                                    <select name="group_unique_id" id="group_unique_id" class="select2 form-control"  onchange="get_sub_group(this.value)" required>
                                        <?php echo $group_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="sub_group_unique_id">Sub Group Name</label>
                                <div class="col-md-3">
                                    <select name="sub_group_unique_id" id="sub_group_unique_id" class="select2 form-control"  onchange="get_code(this.value)" required>
                                        <?php echo $sub_group_unique_id;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                               
                                <label class="col-md-2 col-form-label textright" for="sub_group_code">Sub Group Code</label>
                                <div class="col-md-3">
                                    <input type="text" id="sub_group_code" name="sub_group_code" class="form-control" maxlength="3" value="<?php echo $part[0].'-'.$part[1]; ?>" readonly>
                                </div>
                                 <label class="col-md-2 col-form-label textright" for="category_name">Category Name</label>
                                <div class="col-md-3">
                                    <input type="text" id="category_name" name="category_name" class="form-control" value="<?php echo $category_name; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                               
                                <label class="col-md-2 col-form-label textright" for="code">Category Code</label>
                                <div class="col-md-3">
                                    <input type="text" id="code" name="code" class="form-control" maxlength="3" oninput="allowAlphaNumeric(this)"  title="Please enter exactly 3 letters" value="<?php echo $part[2]; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                
                                <label class="col-md-2 col-form-label textright" for="description">Description</label>
                                <div class="col-md-3">
                                    <textarea name="description" id="description" rows="5" class="form-control" > <?php echo $description; ?></textarea>
                                </div>
                            </div>
                            
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