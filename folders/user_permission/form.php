<style>

</style>

<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

// $permission_check   = user_permission_ui();

// $permission_check   = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        // $where      = [
        //     "unique_id" => $unique_id
        // ];

        // $table      =  "user_permission";

        // $columns    = [
        //     "country_unique_id",
        //     "state_unique_id",
        //     "city_name",
        //     "pincode"
        // ];

        // $table_details   = [
        //     $table,
        //     $columns
        // ]; 

        // $city_values            = $pdo->select($table_details,$where);

        if ($unique_id) {

            // $city_values        = $city_values->data;

            // $country_id         = $city_values[0]["country_unique_id"];
            // $state_id           = $city_values[0]["state_unique_id"];
            // $city_name          = $city_values[0]["city_name"];
            // $pincode            = $city_values[0]["pincode"];

            // $state_options      = state();
            // $state_options      = select_option($state_options,"Select the State",$state_id); 

            $result_val = get_permissions($unique_id);

            $btn_text           = "Update";
            $btn_action         = "update";
            $is_btn_disable     = " disabled ";

        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = " disabled ";
        }
    }
}

$user_type_options  = user_type();
$user_type_options  = select_option($user_type_options,"Select User Type",$unique_id);

$main_screen_options= main_screen();
$main_screen_options= select_option($main_screen_options,"Select Main Screen","");

?>
<input type="hidden" name="update_user_type" id="update_user_type" value="<?php echo $unique_id; ?>">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row "> 
                            <label class="col-md-2 col-form-label textright" for="user_type"> User Type</label>
                            <div class="col-md-3">
                                <select <?php echo $is_btn_disable; ?> name="user_type" onchange="perm_ui_val()" id="user_type" class="select2 form-control" required>
                                    <?php echo $user_type_options;?>
                                </select>
                            </div></div>
                             <div class="form-group row "> 
                            <label class="col-md-2 col-form-label textright" for="main_screen"> Main Screen</label>
                            <div class="col-md-3">
                                <select name="main_screen" onchange="perm_ui_val()" id="main_screen" class="select2 form-control" required>
                                    <?php echo $main_screen_options;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-12" id="perm_ui">
                        <!-- <div class="card-box"> -->
                        <input type="hidden" id="perm_ui" value="">
                        <!-- </div> -->
                    </div>
                   
                    <div class="form-group row btn-action">
                                <div class="col-md-12 mt-2">
                                    <!-- Cancel,save and update Buttons -->
                                    
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                    <?php echo btn_cancel($btn_cancel);?>
                                </div>                                
                            </div>
                   
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  