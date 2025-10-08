<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$main_screen_id     = "";
$screen_section_id  = "";
$screen_name        = "";
$screen_folder_name = "";
$order_no           = "";
$icon_name          = "";
$is_active          = 1;
$description        = "";

$user_action_options    = "";
$user_action_selected   = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "user_screen";

        $columns    = [
            "main_screen_unique_id",
            "screen_section_unique_id",
            "screen_name",
            "folder_name",
            "icon_name",
            "order_no",
            "is_active",
            "description",
            "actions"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values     = $result_values->data;

            $main_screen_id    = $result_values[0]["main_screen_unique_id"];
            $screen_section_id = $result_values[0]["screen_section_unique_id"];
            $screen_name       = $result_values[0]["screen_name"];
            $screen_folder_name= $result_values[0]["folder_name"];
            $icon_name         = $result_values[0]["icon_name"];
            $order_no          = $result_values[0]["order_no"];
            $is_active         = $result_values[0]["is_active"];
            $description       = $result_values[0]["description"];
            $user_action_selected   = $result_values[0]["actions"];


            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$main_screen_options  = main_screen();

$main_screen_options  = select_option($main_screen_options,"Select the Main Screen",$main_screen_id);

$section_name_options = "<option value='' disabled='disabled' selected>Select the Screen Section</option>";

if ($main_screen_id) {

    $section_name_options  = section_name('',$main_screen_id);

    $section_name_options  = select_option($section_name_options,"Select the Screen Section",$screen_section_id);
}

$active_status_options = active_status($is_active);

$user_action_options    = user_actions();

$user_action_options    = user_action_list($user_action_options, $user_action_selected);

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
                                <label class="col-md-2 col-form-label textright" for="main_screen"> Main Screen</label>
                                <div class="col-md-3">
                                    <select name="main_screen" id="main_screen" class="select2 form-control" onchange="get_sections(this.value)" required>
                                        <?php echo $main_screen_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="section_name"> Screen Section </label>
                                <div class="col-md-3">
                                    <select name="section_name" id="section_name" class="select2 form-control" required>
                                        <?php echo $section_name_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="screen_name"> Screen Name</label>
                                <div class="col-md-3">
                                    <input type="text" id="screen_name" name="screen_name" class="form-control" value="<?php echo $screen_name; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="screen_folder_name"> Folder Name</label>
                                <div class="col-md-3">
                                    <input type="text" id="screen_folder_name" name="screen_folder_name" class="form-control" value="<?php echo $screen_folder_name; ?>" required>
                                </div>                    
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="order_no"> Order No</label>
                                <div class="col-md-3">
                                    <input type="number" id="order_no" name="order_no" class="form-control" value="<?php echo $order_no; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="is_active">  Active Status </label>
                                <div class="col-md-3">
                                    <select name="active_status" id="active_status" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>                            
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="icon_name"> Icon</label>
                                <div class="col-md-3">
                                    <input type="text" id="icon_name" name="icon_name" class="form-control" value="<?php echo $icon_name; ?>" placeholder = "Material Design Icon" required>
                                </div>
                                
                                
                                <label class="col-md-2 col-form-label textright" for=""> Actions</label>
                            <div class="col-md-5">
                           
                            <div class="form-group row mt-2 ms-1">
                                <ul class="ks-cboxtags">
                                    <?php echo $user_action_options; ?>
                                </ul>
                            </div>
                             </div>
                                
                                                          
                            </div>
                              <div class="form-group row ">
                                   <label class="col-md-2 col-form-label textright" for="description"> Description</label>
                                <div class="col-md-3">
                                    <!-- <input type="text" id="description" name="description" class="form-control" value="" required> -->
                                    <textarea name="description" id="description"  rows="5" class="form-control expandable-textarea"><?php echo $description; ?></textarea>
                                </div>     
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


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const textareas = document.querySelectorAll('.expandable-textarea');

        textareas.forEach(function (textarea) {
            textarea.addEventListener('focus', function () {
                if (!this.classList.contains('expanded')) {
                    this.classList.add('expanded');
                }
            });
        });
    });
</script>
