<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

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

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "expense_type_sub";

        $columns    = [
            "expense_type_unique_id",
            "grade",
            "type",
            "limit_value",
            // "under_user_type",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);
        // print_r($result_values);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $expense_type_unique_id        = $result_values[0]["expense_type_unique_id"];
            $grade                          = $result_values[0]["grade"];
            $type                           = $result_values[0]["type"];
            
            $limit_value                     = $result_values[0]["limit_value"];
            $is_active                      = $result_values[0]["is_active"];

            // $exp_under_user_type  = explode(",", $under_user_type);

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$expense_type_unique_id_options = expense_type();
$expense_type_unique_id_options = select_option($expense_type_unique_id_options, "Select Expense Type", $expense_type_unique_id);

$grade_options = grade();
$grade_options = select_option($grade_options, "Select Grade Type", $grade);

$type_options = type();
$type_options = select_option($type_options, "Select Type", $type);


$active_status_options   = active_status($is_active);

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
                                <label class="col-md-2 col-form-label" for="expense_type_unique_id"> Expense Type </label>
                                <div class="col-md-4">
                                    <select name="expense_type_unique_id" id="expense_type_unique_id" class="select2 form-control" required>
                                        <?php echo $expense_type_unique_id_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="grade"> Grade Type </label>
                                <div class="col-md-4">
                                    <select name="grade" id="grade" class="select2 form-control" required>
                                        <?php echo $grade_options; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="type">Type </label>
                                <div class="col-md-4">
                                    <select name="type" id="type" class="select2 form-control" >
                                        <?php echo $type_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="limit_value">Limit Value</label>
                                <div class="col-md-4">
                                    <input type="text" id="limit_value" name="limit_value" class="form-control" value="<?php echo $limit_value; ?>">
                                </div>
                            </div>
                            <div class="form-group row ">

                                <label class="col-md-2 col-form-label" for="is_active"> Active Status</label>
                                <div class="col-md-4">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="under_user_type"> Under Users </label>
                                <div class="col-md-4">
                                     <select name="under_user_type_name" id="under_user_type_name" class="select2 form-control" onChange="get_under_user_type_ids()"  multiple>
                                        <?php echo $under_user_type_options; ?>
                                    </select>
                                     <input type="hidden" id="under_user_type" name="under_user_type" class="form-control" value="<?php echo $under_user_type; ?>">
                                </div>
                            </div> -->
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel); ?>
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </form>

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>