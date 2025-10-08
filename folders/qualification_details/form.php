<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$user_type               = "";
$under_user_type        = "";
$exp_under_user_type     = "";

$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "qualification_details";

        $columns    = [
            "graduation_type",
            "qualification",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $graduation_type    = $result_values[0]["graduation_type"];
            $qualification      = $result_values[0]["qualification"];
            $is_active          = $result_values[0]["is_active"];

            $exp_under_user_type  = explode(",", $under_user_type);
          
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}
$graduation_type_options    = graduation_type();
$graduation_type_options    = select_option($graduation_type_options,"Select The Staff Name",$graduation_type);
 

$active_status_options   = active_status($is_active);

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
                            <label class="col-md-2 col-form-label textright" for="user_type"> Graduation Type</label>
                                <div class="col-md-3">
                                    <!--  onKeyup="get_under_user_type(this.value)" -->
                                    <select name="graduation_type" id="graduation_type" class="select2 form-control" required>
                                        <?php echo $graduation_type_options;?>
                                    </select>
                                    <!-- <input type="text" id="graduation_type" name="graduation_type" class="form-control" value="<?php echo $graduation_type; ?>" required> -->
                                </div>   </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="qualification">Qualification</label>
                                <div class="col-md-3">
                                   
                                    <input type="text" id="qualification" name="qualification" class="form-control" value="<?php echo $qualification; ?>" required>
                                </div>
                                </div>

                                <div class="form-group row ">
                                    <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
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