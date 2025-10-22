<!-- This file contains only PHP functions -->
<?php include 'function.php'; ?>

<?php 
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";


$department_options = department();
$department_options = select_option($department_options, "Select Department")

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" id="task_category_form" autocomplete="off">
                    <div class="row">

                        <!-- Department -->
                        <div class="form-group row col-12 mb-3">
                            <div class="col-md-3"></div>
                            <label class="col-md-2 col-form-label" for="department">Department Name</label>
                            <div class="col-md-4">
                                <select class="form-control select2" id="department" name="department" required>
                                    <?= $department_options ?>
                                </select>
                            </div>
                            <div class="col-md-3"></div>
                        </div>

                        <!-- Category -->
                        <div class="form-group row col-12 mb-3">
                            <div class="col-md-3"></div>
                            <label class="col-md-2 col-form-label" for="category">Category Name</label>
                            <div class="col-md-4">
                                <input type="text" class="form-control" id="category" name="category" placeholder="Enter category name" required>
                            </div>
                            <div class="col-md-3"></div>
                        </div>

                        <!-- Description -->
                        <div class="form-group row col-12 mb-3">
                            <div class="col-md-3"></div>
                            <label class="col-md-2 col-form-label" for="description">Description</label>
                            <div class="col-md-4">
                                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Enter description"></textarea>
                            </div>
                            <div class="col-md-3"></div>
                        </div>

                        <!-- Buttons -->
                        <div class="form-group row col-12 text-center">
                            <div class="col-md-12">
                                <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                <?php echo btn_cancel($btn_cancel); ?>
                            </div>
                        </div>

                    </div> <!-- end row -->
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div>
