<!-- This file contains only PHP functions -->
<?php include 'function.php'; ?>

<?php
// ==========================================================
// ✅ Basic Setup
// ==========================================================
$btn_text         = "Save";
$btn_action       = "create";
$is_btn_disable   = "";
$form_type        = "Create";
$unique_id        = "";
$today            = date("Y-m-d");
$user_id          = "";
$user_type        = "";
$mobile_number    = "";
$designation      = "";

// ==========================================================
// ✅ Edit Mode (Fetch Data if unique_id present)
// ==========================================================
if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id = $_GET["unique_id"];

    $table   = "periodic_creation_main";
    $columns = ["user_id", "user_type", "mobile_number", "designation"];
    $where   = ["unique_id" => $unique_id];

    $result = $pdo->select([$table, $columns], $where);

    if ($result->status && !empty($result->data)) {
        $data          = $result->data[0];
        $user_id       = $data["user_id"] ?? '';
        $user_type     = $data["user_type"] ?? '';
        $mobile_number = $data["mobile_number"] ?? '';
        $designation   = $data["designation"] ?? '';

        $btn_text   = "Update";
        $btn_action = "update";
        $form_type  = "Update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

// ==========================================================
// ✅ Dropdown Lists
// ==========================================================
$staff_options = staff_name_bp();
$staff_options = select_option($staff_options, "Select Staff", $user_id);

$department_options = department();
$department_options = select_option($department_options, "Select Department");

$category_options = task_category();
$category_options = select_option($category_options, "Select Category");

$project_options = project_name(); // project/site list function
$project_options = select_option($project_options, "Select Project");

$level_options = [
    ["unique_id" => "L1", "value" => "Level 1"],
    ["unique_id" => "L2", "value" => "Level 2"],
    ["unique_id" => "L3", "value" => "Level 3"],
    ["unique_id" => "L4", "value" => "Level 4"],
    ["unique_id" => "L5", "value" => "Level 5"]
];
$level_options = select_option($level_options, "Select Level");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                
                <!-- ==========================================================
                ✅ MAIN FORM
                ========================================================== -->
                <form class="was-validated" id="periodic_creation_form_main" name="periodic_creation_form_main">
                  <input type="hidden" name="unique_id" id="unique_id" value="<?= $unique_id; ?>">

                  <!-- 🧍 Staff Details -->
                  <div class="form-group row mb-3">
                    <label class="col-md-2 col-form-label text-right">Staff Name</label>
                    <div class="col-md-4">
                      <select class="select2 form-control" id="user_id" name="user_id" onchange="fetch_staff_info()" required <?= $is_btn_disable; ?>>
                        <?= $staff_options; ?>
                      </select>
                    </div>
                    <label class="col-md-2 col-form-label text-right">User Type</label>
                    <div class="col-md-4">
                      <input type="text" class="form-control" id="user_type" name="user_type" value="<?= htmlspecialchars($user_type) ?>" readonly>
                    </div>
                  </div>
                
                  <div class="form-group row mb-3">
                    <label class="col-md-2 col-form-label text-right">Mobile Number</label>
                    <div class="col-md-4">
                      <input type="text" class="form-control" id="mobile_number" name="mobile_number" value="<?= htmlspecialchars($mobile_number) ?>" readonly>
                    </div>
                    <label class="col-md-2 col-form-label text-right">Designation</label>
                    <div class="col-md-4">
                      <input type="text" class="form-control" id="designation" name="designation" value="<?= htmlspecialchars($designation) ?>" readonly>
                    </div>
                  </div>
                </form>
                
                <!-- ==========================================================
                ✅ SUBLIST ENTRY FORM
                ========================================================== -->
                <form class="was-validated" id="periodic_creation_form_sub" name="periodic_creation_form_sub">
                  <div class="border p-3 rounded mb-3">
                    <h5 class="text-primary mb-3">Periodic Assignment Details</h5>
                    <div class="row">
                      <div class="col-md-2 mb-2">
                        <label>Department</label>
                        <select class="form-control select2" id="department" name="department" required>
                          <?= $department_options; ?>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label>Category</label>
                        <select class="form-control select2" id="category" name="category" required>
                          <?= $category_options; ?>
                        </select>
                      </div>
                      <div class="col-md-3 mb-2">
                        <label>Project</label>
                        <select class="form-control select2" id="project_id" name="project_id" required>
                          <?= $project_options; ?>
                        </select>
                      </div>
                      <div class="col-md-2 mb-2">
                        <label>Level</label>
                        <select class="form-control select2" id="level" name="level" required>
                          <?= $level_options; ?>
                        </select>
                      </div>
                      <div class="col-md-1 mb-2">
                        <label>Start</label>
                        <input type="text" class="form-control" id="starting_days" name="starting_days" onkeydown="number_only(event)" required>
                      </div>
                      <div class="col-md-1 mb-2">
                        <label>End</label>
                        <input type="text" class="form-control" id="ending_days" name="ending_days" onkeydown="number_only(event)" required>
                      </div>
                      <div class="col-md-1 mb-3 d-flex align-items-end">
                        <button type="button" class="btn btn-success w-100 periodic_sub_add_update_btn" onclick="periodic_add_update();">
                          <i class="fa fa-plus"></i> Add
                        </button>
                      </div>
                    </div>
                  </div>
                
                  <!-- 🗂 Sublist Table -->
                  <div class="table-responsive mb-4">
                    <table class="table table-bordered table-striped w-100" id="periodic_sub_datatable">
                      <thead class="table-light">
                        <tr>
                          <th>#</th>
                          <th>Department</th>
                          <th>Category</th>
                          <th>Project</th>
                          <th>Level</th>
                          <th>Start Days</th>
                          <th>End Days</th>
                          <th>Action</th>
                        </tr>
                      </thead>
                      <tbody id="periodic_sub_tbody"></tbody>
                    </table>
                  </div>
                </form>
                
                <!-- ==========================================================
                ✅ BUTTONS
                ========================================================== -->
                <div class="form-group row text-center">
                  <div class="col-md-12">
                    <?php echo btn_cancel($btn_cancel); ?>
                    <?php echo btn_createupdate($folder_name_org, $unique_id, ucfirst($btn_text)); ?>
                  </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
