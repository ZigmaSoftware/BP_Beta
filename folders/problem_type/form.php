<?php include 'function.php'; ?>

<?php 
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";
$unique_id      = "";
$update_form    = 0;
$problem_type    = "";
$active         = "";
$description    = "";

if (isset($_GET['unique_id']) && !empty($_GET['unique_id'])) {
    $unique_id   = $_GET['unique_id'];
    $update_form = 1;
    $table       = "problem_type";

    $columns = ["problem_type", "is_active", "description"];
    $where   = ["unique_id" => $unique_id];
    $result  = $pdo->select([$table, $columns], $where);

    if ($result->status && !empty($result->data)) {
        $data         = $result->data[0];
        $problem_type  = $data['problem_type'] ?? '';
        $active       = $data['is_active'] ?? '';
        $description  = $data['description'] ?? '';
        $btn_text     = "Update";
        $btn_action   = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

// ✅ Status dropdown
$active_options = [
    ["unique_id" => "1", "value" => "Active"],
    ["unique_id" => "0", "value" => "Inactive"]
];
$active_options = select_option($active_options, "Select Status", $active);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" id="problem_type_form" autocomplete="off">
                    <input type="hidden" name="unique_id" id="unique_id" value="<?= htmlspecialchars($unique_id) ?>">
                    <input type="hidden" name="update_form" id="update_form" value="<?= htmlspecialchars($update_form) ?>">

                    <!-- Problem Type -->
                    <div class="form-group row col-12 mb-3">
                        <div class="col-md-3"></div>
                        <label class="col-md-2 col-form-label text-right" for="problem_type">Problem Type Name</label>
                        <div class="col-md-4">
                            <input 
                                type="text" 
                                class="form-control" 
                                id="problem_type" 
                                name="problem_type" 
                                placeholder="Enter Problem type name" 
                                value="<?= htmlspecialchars($problem_type) ?>" 
                                required
                                <?= $is_btn_disable ?>
                            >
                        </div>
                        <div class="col-md-3"></div>
                    </div>

                    <!-- Active / Inactive -->
                    <div class="form-group row col-12 mb-3">
                        <div class="col-md-3"></div>
                        <label class="col-md-2 col-form-label text-right" for="active">Status</label>
                        <div class="col-md-4">
                            <select class="form-control select2" id="active" name="active" required>
                                <?= $active_options ?>
                            </select>
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
                                <?= $is_btn_disable ?>
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
