<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>
<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";

$document               = "";
$document2               = "";
$under_user_type        = "";
$exp_under_user_type     = "";
$is_active          = 1;
if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {
        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        $table      =  "kra_kpi_form";
        $columns    = [
            "staff_name",
            "doc_name",
            "document_name",

        ];
        $table_details   = [
            $table,
            $columns
        ];
        $result_values  = $pdo->select($table_details, $where);
        if ($result_values->status) {
            $result_values      = $result_values->data;
            $staff_name    = $result_values[0]["staff_name"];
            $doc_name      = $result_values[0]["doc_name"];
            $document_name      = $result_values[0]["document_name"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}
$staff_name_options  = staff_name();
$staff_name_options  = select_option($staff_name_options, "Select", $staff_name);
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <input type="hidden" id="unique_id" name="unique_id" value="<?php echo $unique_id; ?>">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff Name">Staff Name</label>
                                <div class="col-md-4">
                                    <select type="select" name="staff_name" id="staff_name" class="select2 form-control" required>
                                        <?php echo $staff_name_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="document1">document1</label>
                                <div class="col-md-4">
                                    <input type="file" id="test_file_exp" multiple name="test_file_exp[]" class="form-control dropify" value="<?php echo $doc_name; ?>">
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="document2">document2</label>
                                <div class="col-md-4">
                                    <input type="file" id="test_file_imp" multiple name="test_file_imp[]" class="form-control dropify" value="<?php echo $document_name; ?>">
                                </div>

                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel);?>
                            <?php if($unique_id == ''){?>
                           
                                    <button type="button" style="float: right; margin-top: 1px;" class=" btn btn-success m-t-15 btn-rounded waves-effect waves-light float-right ml-2" experience_add_update_btn" onclick="form_data()">Save</button>
                                 
                              <?php }else{?>
                                
                                    <button type="button" style="float: right; margin-top: 1px;" class="btn btn-success m-t-15 btn-rounded waves-effect waves-light float-right ml-2" experience_add_update_btn" onclick="form_data()">Update</button>
                                 
                                <?php }?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>