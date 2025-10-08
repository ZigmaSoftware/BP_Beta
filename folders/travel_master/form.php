<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";
$transport_type = "";
$grade       = "";
$designation       = "";
$designation_options     = "<option value='' disabled='disabled' selected>Select Designation</option>";

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "travel_master";

        $columns    = [
            "designation",
            "grade",
            "transport_type"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $state_values  = $pdo->select($table_details, $where);

        if ($state_values->status) {

            $state_values     = $state_values->data;

            $designation       = $state_values[0]["designation"];
            $grade             = $state_values[0]["grade"];
            $transport_type    = $state_values[0]["transport_type"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}
// $designation_options  = designation_name();

// $designation_options  = select_option($designation_options, "Select the Designation", $designation);

$grade_type_options    = grade_type();
$grade_type_options    = select_option($grade_type_options,"Select The Grade",$grade);


$designation_options        = designation("", $grade_type);
$designation_options        = select_option($designation_options, "Select Designation", $designation);


?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="grade"> Grade <span style="color:red">*</span> </label>
                                 <div class="col-md-3">
                                    <select name="grade" id="grade" class="select2 form-control" onchange="get_designation(this.value);" required><?php echo $grade_type_options; ?>
                                    </select>
                                 </div> </div>
                                  <div class="form-group row ">
                                 <label class="col-md-2 col-form-label textright" for="designation"> Designation <span style="color:red">*</span> </label>
                                 <div class="col-md-3">
                                    <select name="designation" id="designation" class="select2 form-control" required><?php echo $designation_options; ?>
                                    </select>
                                 </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="transport_type">Transport Type</label>
                                <div class="col-md-3">
                                    <input type="text" id="transport_type" name="transport_type" class="form-control" value="<?php echo $transport_type; ?>" required>
                                </div>
                            </div>
                        </div>
                        <!-- <div class="form-group row "> -->
                        <div class="col-md-12 btn-action">
                            <!-- Cancel,save and update Buttons -->
                           
                             <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                             <?php echo btn_cancel($btn_cancel); ?>
                        </div>

                        <!-- </div> -->
                    </div>
            </div>
            </form>
        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->
</div>