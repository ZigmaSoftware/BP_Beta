<?php include 'crud.php'; ?>

<?php 
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";

$unique_id   = "";
$project_id  = ""; 
$date        = date('Y-m-d');

// initialize variables
$waste_receive = $waste_reject = $waste_crushing = $feeding_kgs = $water_liters = "";
$feeding_ph = $valve_1_ph = $nb = $wd = "";
$start_reading = $end_reading = $total_reading = "";
$daily_gas_generation = $start_purification_balloon = $stop_purification_balloon = $gas_used_for_cbg = "";
$cbg_start_time = $cbg_stop_time = $cbg_running_hrs = "";
$comp_start_time = $comp_stop_time = $comp_total_run_hrs = "";
$total_cbg_generation = "";
$start_cascade_pressure = $stop_cascade_pressure = $balance_cascade_pressure = "";
$no_of_vehicle_filled = $balance_gas_cascade = "";
$remark = "";

// If editing an existing record
if(isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $where      = ["unique_id" => $unique_id];
    $table      = "cbg_daily_log";
    $columns    = [
        "project_id","date","waste_receive","waste_reject","waste_crushing","feeding_kgs",
        "water_liters","feeding_ph","valve_1_ph","nb","wd","start_reading","end_reading","total_reading",
        "daily_gas_generation","start_purification_balloon","stop_purification_balloon","gas_used_for_cbg",
        "cbg_start_time","cbg_stop_time","cbg_running_hrs","comp_start_time","comp_stop_time","comp_total_run_hrs",
        "total_cbg_generation","start_cascade_pressure","stop_cascade_pressure","balance_cascade_pressure",
        "no_of_vehicle_filled","balance_gas_cascade","remark"
    ];

    $table_details = [$table, $columns]; 
    $result_values = $pdo->select($table_details,$where);

    if ($result_values->status) {
        $result_values = $result_values->data[0];
        foreach($columns as $col) {
            $$col = $result_values[$col];
        }
        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

$project_options  = get_project_by_type('cbg');  
$project_options  = select_option($project_options,"Select Project",$project_id);
?>
<style>
    input[type=number]::-webkit-outer-spin-button,
    input[type=number]::-webkit-inner-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="date">Date</label>
                                <div class="col-md-7">
                                    <input type="date" id="date" name="date" class="form-control"
                                           value="<?php echo $date; ?>" required>
                                    <input type="hidden" name="unique_id" id="unique_id" value="<?php echo $unique_id; ?>">
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="project_id">Project Name <span style="color:red">*</span></label>
                                <div class="col-md-7">
                                    <select name="project_id" id="project_id" class="select2 form-control" required>
                                        <?php echo $project_options; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Waste & Feeding -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="waste_receive">Waste Receive (Kg)</label>
                                <div class="col-md-7"><input type="number" id="waste_receive" name="waste_receive" class="form-control" value="<?php echo $waste_receive; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="waste_reject">Waste Reject (Kg)</label>
                                <div class="col-md-7"><input type="number" id="waste_reject" name="waste_reject" class="form-control" value="<?php echo $waste_reject; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="waste_crushing">Waste Crushing (Kg)</label>
                                <div class="col-md-7"><input type="number" id="waste_crushing" name="waste_crushing" class="form-control" value="<?php echo $waste_crushing; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="feeding_kgs">Feeding (Kg)</label>
                                <div class="col-md-7"><input type="number" id="feeding_kgs" name="feeding_kgs" class="form-control" value="<?php echo $feeding_kgs; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="water_liters">Water (Liters)</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="water_liters" name="water_liters" class="form-control" value="<?php echo $water_liters; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="feeding_ph">Feeding pH</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="feeding_ph" name="feeding_ph" class="form-control" value="<?php echo $feeding_ph; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="valve_1_ph">Valve-1 pH</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="valve_1_ph" name="valve_1_ph" class="form-control" value="<?php echo $valve_1_ph; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="nb">NB</label>
                                <div class="col-md-7"><input type="number" id="nb" name="nb" class="form-control" value="<?php echo $nb; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="wd">WD</label>
                                <div class="col-md-7"><input type="number" id="wd" name="wd" class="form-control" value="<?php echo $wd; ?>"></div>
                            </div>

                            <!-- Readings -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="start_reading">Flow Start Reading</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="start_reading" name="start_reading" class="form-control" value="<?php echo $start_reading; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="end_reading">Flow End Reading</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="end_reading" name="end_reading" class="form-control" value="<?php echo $end_reading; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="total_reading">Flow Total Reading</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="total_reading" name="total_reading" class="form-control" value="<?php echo $total_reading; ?>" readonly></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="daily_gas_generation">Daily Gas Generation (%)</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="daily_gas_generation" name="daily_gas_generation" class="form-control" value="<?php echo $daily_gas_generation; ?>"></div>
                            </div>

                            <!-- Balloons -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="start_purification_balloon">Start Purification Balloon (%)</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="start_purification_balloon" name="start_purification_balloon" class="form-control" value="<?php echo $start_purification_balloon; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="stop_purification_balloon">Stop Purification Balloon (%)</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="stop_purification_balloon" name="stop_purification_balloon" class="form-control" value="<?php echo $stop_purification_balloon; ?>"></div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="col-md-6">
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="gas_used_for_cbg">% Gas Used for CBG</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="gas_used_for_cbg" name="gas_used_for_cbg" class="form-control" value="<?php echo $gas_used_for_cbg; ?>"></div>
                            </div>

                            <!-- CBG Times -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="cbg_start_time">CBG Start Time</label>
                                <div class="col-md-7"><input type="time" id="cbg_start_time" name="cbg_start_time" class="form-control" value="<?php echo $cbg_start_time; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="cbg_stop_time">CBG Stop Time</label>
                                <div class="col-md-7"><input type="time" id="cbg_stop_time" name="cbg_stop_time" class="form-control" value="<?php echo $cbg_stop_time; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="cbg_running_hrs">CBG Running Hrs</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="cbg_running_hrs" name="cbg_running_hrs" class="form-control" value="<?php echo $cbg_running_hrs; ?>" readonly></div>
                            </div>

                            <!-- Compressor Times -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="comp_start_time">Comp Start Time</label>
                                <div class="col-md-7"><input type="time" id="comp_start_time" name="comp_start_time" class="form-control" value="<?php echo $comp_start_time; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="comp_stop_time">Comp Stop Time</label>
                                <div class="col-md-7"><input type="time" id="comp_stop_time" name="comp_stop_time" class="form-control" value="<?php echo $comp_stop_time; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="comp_total_run_hrs">Comp Total Run Hrs</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="comp_total_run_hrs" name="comp_total_run_hrs" class="form-control" value="<?php echo $comp_total_run_hrs; ?>" readonly></div>
                            </div>

                            <!-- CBG Generation & Cascade -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="total_cbg_generation">Total CBG Generation (Kg)</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="total_cbg_generation" name="total_cbg_generation" class="form-control" value="<?php echo $total_cbg_generation; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="start_cascade_pressure">Start Cascade Pressure</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="start_cascade_pressure" name="start_cascade_pressure" class="form-control" value="<?php echo $start_cascade_pressure; ?>"></div>
                                
                                <label class="col-md-5 col-form-label labelright" for="stop_cascade_pressure">Stop Cascade Pressure</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="stop_cascade_pressure" name="stop_cascade_pressure" class="form-control" value="<?php echo $stop_cascade_pressure; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="balance_cascade_pressure">Balance Cascade Pressure</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="balance_cascade_pressure" name="balance_cascade_pressure" class="form-control" value="<?php echo $balance_cascade_pressure; ?>" readonly></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="no_of_vehicle_filled">No. of Vehicle Filled</label>
                                <div class="col-md-7"><input type="number" id="no_of_vehicle_filled" name="no_of_vehicle_filled" class="form-control" value="<?php echo $no_of_vehicle_filled; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="balance_gas_cascade">Balance Gas in Cascade (Kg)</label>
                                <div class="col-md-7"><input type="number" step="0.01" id="balance_gas_cascade" name="balance_gas_cascade" class="form-control" value="<?php echo $balance_gas_cascade; ?>"></div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="remark">Remark</label>
                                <div class="col-md-7"><textarea name="remark" id="remark" class="form-control" rows="2"><?php echo $remark; ?></textarea></div>
                            </div>

                            <div class="form-group row">
                                <div class="col-md-12 text-end">
                                    <?php echo btn_cancel($btn_cancel); ?>
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>
