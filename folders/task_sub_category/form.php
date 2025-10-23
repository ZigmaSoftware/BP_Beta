<?php include 'function.php'; ?>

<?php 
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";
$unique_id      = "";
$update_form    = 0;
$department     = "";
$category       = "";
$sub_category   = "";
$description    = "";

if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id = $_GET['unique_id'];
    $update_form  = 1;
    
    $table = "task_sub_category";
    $columns = [
        "department_unique_id",
        "task_category_name",
        "task_sub_category_name",
        "description"
    ];
    
    $where = ["unique_id" => $unique_id];
    $result = $pdo->select([$table, $columns], $where);
    
    if ($result->status && !empty($result->data)) {
        $data         = $result->data[0];
        $department   = $data['department_unique_id'] ?? '';
        $category     = $data['task_category_name'] ?? '';
        $sub_category = $data['task_sub_category_name'] ?? '';
        $description  = $data['description'] ?? '';
        
        $btn_text     = "Update";
        $btn_action   = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

// ✅ Dropdown data
$department_options = department();
$department_options = select_option($department_options, "Select Department", $department);

$task_category_options = task_category();
$task_category_options = select_option($task_category_options, "Select Category", $category);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" id="task_sub_category_form" autocomplete="off">
                    <input type="hidden" name="unique_id" id="unique_id" value="<?= htmlspecialchars($unique_id) ?>">
                    <input type="hidden" name="update_form" id="update_form" value="<?= htmlspecialchars($update_form) ?>">

                    <!-- Department -->
                    <div class="form-group row col-12 mb-3">
                        <div class="col-md-3"></div>
                        <label class="col-md-2 col-form-label text-right" for="department">Department Name</label>
                        <div class="col-md-4">
                            <select class="form-control select2" id="department" name="department" required onchange="task_list(this.value)">
                                <?= $department_options ?>
                            </select>
                        </div>
                        <div class="col-md-3"></div>
                    </div>
                    
                    <!-- Category -->
                    <div class="form-group row col-12 mb-3">
                        <div class="col-md-3"></div>
                        <label class="col-md-2 col-form-label text-right" for="category">Category Name</label>
                        <div class="col-md-4">
                            <select class="form-control select2" id="category" name="category" required>
                                <?= $task_category_options ?>
                            </select>
                        </div>
                        <div class="col-md-3"></div>
                    </div>

                    <!-- Sub-Category -->
                    <div class="form-group row col-12 mb-3">
                        <div class="col-md-3"></div>
                        <label class="col-md-2 col-form-label text-right" for="sub_category">Sub-Category Name</label>
                        <div class="col-md-4">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="sub_category" 
                                name="sub_category" 
                                placeholder="Enter sub-category name" 
                                value="<?= htmlspecialchars($sub_category) ?>" 
                                required
                            >
                        </div>
                        <div class="col-md-3"></div>
                    </div>

                    <!-- Description -->
                    <div class="form-group row col-12 mb-3">
                        <div class="col-md-3"></div>
                        <label class="col-md-2 col-form-label text-right" for="description">Description</label>
                        <div class="col-md-4">
                            <textarea 
                                class="form-control" 
                                id="description" 
                                name="description" 
                                rows="4" 
                                placeholder="Enter description"
                            ><?= htmlspecialchars($description) ?></textarea>
                        </div>
                        <div class="col-md-3"></div>
                    </div>

                    <!-- Buttons -->
                    <div class="form-group row col-12 text-center">
                        <div class="col-md-12">
                            <?php echo btn_createupdate($folder_name_org, $unique_id, ucfirst($btn_text)); ?>
                            <?php echo btn_cancel($btn_cancel); ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
