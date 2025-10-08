<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$user_type_id       = "";
$staff_id           = "";
$menus              = [];
$staff_option       = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "dashboard_settings";

        $columns    = [
            "user_type_id",
            "staff_id",
            "menus",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $user_type_id       = $result_values["user_type_id"];
            $staff_id           = $result_values["staff_id"];
            $menus              = explode(",",$result_values["menus"]);
            $is_active          = $result_values["is_active"];

            if ($staff_id) {
                $staff_option       = staff_name($staff_id)[0]['staff_name'];
                $staff_option       = "<option value='".$staff_id."' selected>".$staff_option."</option>";
            }

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$active_status_options = active_status($is_active);

$user_type_options  = user_type();

$user_type_options  = select_option($user_type_options,"Select User Type",$user_type_id);


// Get all Dashboard Menus
$dashboard_menus = dashboard_menu();

// foreach ($dashboard_menus as $menu_key => $menu_value) {
    
// }

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="menu_name"> User Type </label>
                            <div class="col-md-3">
                                <select name="user_type" id="user_type" class="select2 form-control" onchange="get_staff_details(this.value)" required>
                                    <?php echo $user_type_options; ?>
                                </select>
                            </div>
                            <label class="col-md-2 col-form-label textright" for="staff_name"> Staff Name</label>
                            <div class="col-md-3">
                                <select name="staff_name" id="staff_name" class="select2 form-control">
                                    <option value="">Select Staff Name</option>
                                    <?php echo $staff_option; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                            <div class="col-md-3">
                                <select name="is_active" id="is_active" class="select2 form-control" required>
                                    <?php echo $active_status_options;?>
                                </select>
                            </div>
                            
                                 <label class="col-md-2 col-form-label textright" for=""> Dashboard Menu(s)</label>
                           
                             <div class="col-md-5">
                                 <div class="row">
                                     <?php 
                            foreach ($dashboard_menus as $dash_key => $dash_value) {

                                $checked        = "";
                                $menu_name      = $dash_value['menu_name'];
                                $menu_name_org  = $dash_value['file_name'];

                                if ($unique_id)  {
                                    if (in_array($menu_name_org,$menus)) {
                                        $checked = " checked ";
                                    }
                                }

                        ?>

                        
                            <div class="col-md-4 checkbox checkbox-success mb-2">
                                <!-- <input id="<?php echo $menu_name_org."_order"; ?>" name="menus_order[]"class="form-control col-3" type="number" value="<?php echo $menu_name_org; ?>"> -->

                                <input id="<?php echo $menu_name_org; ?>" name="menus[]" type="checkbox" value="<?php echo $menu_name_org; ?>" <?php echo $checked; ?>>
                                <label for="<?php echo $menu_name_org; ?>">
                                    <?php echo $menu_name; ?>
                                </label>
                                
 
                            </div>                            

                        <?php } ?>
                        </div>
                        </div></div>
                        <div class="form-group row">
                           
                        </div>
                        <div class="form-group row">
               
                        </div>
                        
                        <div class="form-group row btn-action">
                            <div class="col-md-12">
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