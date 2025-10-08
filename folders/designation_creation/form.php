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

        $table      =  "designation_creation";

        $columns    = [
            "grade_type",
            "designation",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $grade_type         = $result_values[0]["grade_type"];
            $designation        = $result_values[0]["designation"];
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
$grade_type_options    = grade_type();
$grade_type_options    = select_option($grade_type_options,"Select The Grade",$grade_type);
 

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
                            <label class="col-md-2 col-form-label textright" for="user_type"> Grade </label>
                                <div class="col-md-3">
                                    <!--  onKeyup="get_under_user_type(this.value)" -->
                                    <select name="grade_type" id="grade_type" class="select2 form-control" required>
                                        <?php echo $grade_type_options;?>
                                    </select>
                                    <!-- <input type="text" id="grade_type" name="grade_type" class="form-control" value="<?php echo $grade_type; ?>" required> -->
                                </div></div><div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="designation"> Designation </label>
                                <div class="col-md-3">
                                   
                                    <input type="text" id="designation" name="designation" class="form-control" value="<?php echo $designation; ?>" required>
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