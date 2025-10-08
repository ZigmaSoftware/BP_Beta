<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$screen_type_id     = "";
$screen_name        = "";
$order_no           = "";
$icon_name          = "";
$is_active          = 1;
$description        = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "user_screen_main";

        $columns    = [
            "screen_type_unique_id",
            "screen_main_name",
            "icon_name",
            "order_no",
            "is_active",
            "description"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values     = $result_values->data;

            $screen_type_id    = $result_values[0]["screen_type_unique_id"];
            $screen_name       = $result_values[0]["screen_main_name"];
            $icon_name         = $result_values[0]["icon_name"];
            $order_no          = $result_values[0]["order_no"];
            $is_active         = $result_values[0]["is_active"];
            $description       = $result_values[0]["description"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$screen_type_options  = screen_type();

// print_r($screen_type_options);

$screen_type_options  = select_option($screen_type_options,"Select the Screen Type",$screen_type_id);

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
                                <label class="col-md-2 col-form-label" for="screen_type"> Screen Type</label>
                                <div class="col-md-4">
                                    <select name="screen_type" id="screen_type" class="select2 form-control" required>
                                        <?php echo $screen_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="screen_name"> Screen Name </label>
                                <div class="col-md-4">
                                    <input type="text" id="screen_name" name="screen_name" class="form-control" value="<?php echo $screen_name; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="order_no"> Order No</label>
                                <div class="col-md-4">
                                    <input type="number" id="order_no" name="order_no" class="form-control" value="<?php echo $order_no; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="is_active">  Active Status </label>
                                <div class="col-md-4">
                                    <!-- <input type="text" id="is_active" name="is_active" class="form-control" value="<?php echo $is_active; ?>" required> -->
                                    <select name="active_status" id="active_status" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="icon_name"> Icon</label>
                                <div class="col-md-4">
                                    <input type="text" id="icon_name" name="icon_name" class="form-control" value="<?php echo $icon_name; ?>" placeholder = "Material Design Icon" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="description"> Description</label>
                                <div class="col-md-4">
                                    <!-- <input type="text" id="description" name="description" class="form-control" value="" required> -->
                                    <textarea name="description" id="description"  rows="5" class="form-control"><?php echo $description; ?></textarea>
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
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  