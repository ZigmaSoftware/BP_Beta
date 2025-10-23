
<?php include 'function.php'; ?>

<?php
// ----------------------------------------------------
// Initialize default variables
// ----------------------------------------------------
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";
$unique_id      = "";
$project_id     = "";
$month_year     = date('Y-m'); // default current month
$is_active      = 1;

// ----------------------------------------------------
// Load data if unique_id is passed (Edit mode)
// ----------------------------------------------------
if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $table      = "shift_roster_main";
    $columns    = ["project_id", "month_year"];
    $where      = ["unique_id" => $unique_id];

    $result_values = $pdo->select([$table, $columns], $where);

    if ($result_values->status) {
        $data = $result_values->data[0];
        $project_id  = $data["project_id"];
        $month_year  = $data["month_year"];

        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

$active_status_options = active_status($is_active);

$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);
?>
<style>
#roster_table_container {
    overflow-x: auto;
    position: relative;
}

/* ===========================================================
   ✅ Sticky first and last columns (Employee + Action)
   =========================================================== */
.table thead th:first-child,
.table tbody td:first-child {
    position: sticky;
    left: 0;
    background: #fff;
    z-index: 999; /* lower than header, higher than cells */
    min-width: 200px;
    box-shadow: 2px 0 4px rgba(0, 0, 0, 0.08);
}

.table thead th:last-child,
.table tbody td:last-child {
    position: sticky;
    right: 0;
    background: #fff;
    z-index: 999;
    min-width: 120px;
    box-shadow: -2px 0 4px rgba(0, 0, 0, 0.08);
}

/* ===========================================================
   ✅ Sticky header row (always on top)
   =========================================================== */
.table thead th {
    position: sticky;
    top: 0;
    z-index: 9; /* 👈 above side columns */
    background-color: #f8f9fa;
    text-align: center;
    vertical-align: middle;
    white-space: nowrap;
}

/* Header corners should remain crisp and above all */
.table thead th:first-child,
.table thead th:last-child {
    z-index: 10;
}

/* ===========================================================
   ✅ Shift input boxes (editable, bigger, clean)
   =========================================================== */
.table .shift_input {
    position: relative;
    z-index: 20; /* stay above everything for typing */
    background-color: #fff;
    height: 38px !important;
    min-width: 130px !important;
    border: 1.8px solid #28a745;
    border-radius: 6px;
    font-size: 14px;
    text-align: center;
    color: #212529;
}

.table .shift_input:focus {
    border-color: #007bff;
    box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    outline: none;
}

/* ===========================================================
   ✅ Checkbox alignment
   =========================================================== */
.table .form-check {
    display: flex;
    justify-content: center;
    align-items: center;
}

/* ===========================================================
   ✅ General Table Fixes
   =========================================================== */
.table-responsive {
    padding-bottom: 20px;
}

.table th,
.table td {
    vertical-align: middle;
}

</style>
<!-- ============================================================= -->
<!-- Shift Roster Form -->
<!-- ============================================================= -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">

                            <!-- Project / Site -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="project_id">Project Name</label>
                                <div class="col-md-4">
                                     <select name="project_id" id="project_id" class="form-control select2" required>
                                <?= $project_options ?>
                               </select>
                                </div>
                            </div>

                            <!-- Month & Year -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="month_year">Month</label>
                                <div class="col-md-4">
                                    <input type="month" id="month_year" name="month_year"
                                           class="form-control" value="<?php echo $month_year; ?>" required>
                                </div>
                            </div>
                            
                            <div id="roster_table_container" class="mt-4"></div>


                            <!-- Buttons -->
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                    <?php echo btn_cancel($btn_cancel); ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>
