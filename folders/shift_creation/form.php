<link rel="stylesheet" href="jquery-ui.min.css">
<script src="jquery-3.6.0.min.js"></script>
<script src="jquery-ui.min.js"></script>

<!-- PHP function include -->
<?php include 'function.php'; ?>

<?php
// ----------------------------------------------------
// Initialize default variables
// ----------------------------------------------------
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";
$unique_id      = "";
$shift_name     = "";
$start_time     = "";
$end_time       = "";
$shift_duration = "";
$description    = "";
$is_active      = 1;

// ----------------------------------------------------
// Load data if unique_id is passed (Edit mode)
// ----------------------------------------------------
if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $where      = ["unique_id" => $unique_id];
    $table      = "shift_creation";
    $columns    = ["shift_name", "start_time", "end_time","shift_duration", "description"];
    $table_details = [$table, $columns];

    $result_values = $pdo->select($table_details, $where);
    if ($result_values->status) {
        $data = $result_values->data[0];
        $shift_name = $data["shift_name"];
        $start_time = $data["start_time"];
        $end_time   = $data["end_time"];
        $shift_duration = $data["shift_duration"]; 
        $description = $data["description"];

        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

$active_status_options = active_status($is_active);
?>

<!-- ============================================================= -->
<!-- Shift Creation Form -->
<!-- ============================================================= -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">

                            <!-- Shift Name -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="shift_name">Shift Name</label>
                                <div class="col-md-4">
                                    <input type="text" id="shift_name" name="shift_name" class="form-control"
                                           value="<?php echo $shift_name; ?>" required>
                                </div>
                            </div>

                            <!-- Start Time -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="start_time">Start Time</label>
                                <div class="col-md-4">
                                    <input type="time" id="start_time" name="start_time" class="form-control"
                                           value="<?php echo $start_time; ?>" required>
                                </div>
                            </div>

                            <!-- End Time -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="end_time">End Time</label>
                                <div class="col-md-4">
                                    <input type="time" id="end_time" name="end_time" class="form-control"
                                           value="<?php echo $end_time; ?>" required>
                                </div>
                            </div>
                            
                            <!-- Shift Duration -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="shift_duration">Shift Duration</label>
                                <div class="col-md-4">
                                    <input type="text" id="shift_duration" name="shift_duration" 
                                           class="form-control" value="<?php echo $shift_duration; ?>" readonly>
                                </div>
                            </div>


                            <!-- Description -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="description">Description</label>
                                <div class="col-md-4">
                                    <textarea id="description" name="description" class="form-control" required><?php echo $description; ?></textarea>
                                </div>
                            </div>

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
