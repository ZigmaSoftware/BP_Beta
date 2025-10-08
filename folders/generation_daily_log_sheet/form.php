<?php include 'crud.php'; ?>

<?php 
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";

// Default values
$unique_id           = "";
$project_id             = ""; 
$entry_date          = date('Y-m-d');
$waste_received      = "";
$waste_reject        = "";
$feed_to_digester    = "";
$black_water_liters  = "";
$water_liters        = "";
$feeding_ph          = "";
$outlet_ph           = "";
$flowmeter_start     = "";
$flowmeter_stop      = "";
$genset_start_hrs    = "";
$genset_stop_hrs     = "";
$start_kwh           = "";
$stop_kwh            = "";
$remarks             = "";

// If editing an existing record
if(isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $where      = ["unique_id" => $unique_id];
    $table      = "mandi_gobindgad_log";
    $columns    = [
        "project_id", 
        "entry_date",
        "waste_received",
        "waste_reject",
        "feed_to_digester",
        "black_water_liters",
        "water_liters",
        "feeding_ph",
        "outlet_ph",
        "flowmeter_start",
        "flowmeter_stop",
        "genset_start_hrs",
        "genset_stop_hrs",
        "start_kwh",
        "stop_kwh",
        "remarks"
    ];

    $table_details = [$table, $columns]; 
    $result_values = $pdo->select($table_details,$where);

    if ($result_values->status) {
        $result_values          = $result_values->data;
        $project_id             = $result_values[0]["project_id"]; 
        $entry_date             = $result_values[0]["entry_date"];
        $waste_received         = $result_values[0]["waste_received"];
        $waste_reject           = $result_values[0]["waste_reject"];
        $feed_to_digester       = $result_values[0]["feed_to_digester"];
        $black_water_liters     = $result_values[0]["black_water_liters"];
        $water_liters           = $result_values[0]["water_liters"];
        $feeding_ph             = $result_values[0]["feeding_ph"];
        $outlet_ph              = $result_values[0]["outlet_ph"];
        $flowmeter_start        = $result_values[0]["flowmeter_start"];
        $flowmeter_stop         = $result_values[0]["flowmeter_stop"];
        $genset_start_hrs       = $result_values[0]["genset_start_hrs"];
        $genset_stop_hrs        = $result_values[0]["genset_stop_hrs"];
        $start_kwh              = $result_values[0]["start_kwh"];
        $stop_kwh               = $result_values[0]["stop_kwh"];
        $remarks                = $result_values[0]["remarks"];

        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

$project_options  = get_project_by_type('generation');  
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
                        <div class="col-12">
                            <div class="row"> 
<div class="col-md-5">
    <!-- Entry Date -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="entry_date">Entry Date</label>
                                <div class="col-md-7">
                                    <input type="date" id="entry_date" name="entry_date" 
                                           class="form-control" 
                                           value="<?php echo $entry_date; ?>" required>
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

                            <!-- Waste Received / Waste Reject -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="waste_received">Waste Received (kg)</label>
                                <div class="col-md-7">
                                    <input type="number" id="waste_received" name="waste_received" 
                                           class="form-control" min="0" 
                                           value="<?php echo $waste_received; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="waste_reject">Waste Reject (kg)</label>
                                <div class="col-md-7">
                                    <input type="number" id="waste_reject" name="waste_reject" 
                                           class="form-control" min="0" 
                                           value="<?php echo $waste_reject; ?>" required>
                                </div>
                            </div>

                            <!-- Feed to Digester / Black Water -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="feed_to_digester">Feed to Digester (kg)</label>
                                <div class="col-md-7">
                                    <input type="number" id="feed_to_digester" name="feed_to_digester" 
                                           class="form-control" min="0" 
                                           value="<?php echo $feed_to_digester; ?>" required>
                                </div>
 
                                <label class="col-md-5 col-form-label labelright" for="black_water_liters">Black Water (Liters)</label>
                                <div class="col-md-7">
                                    <input type="number" step="0.01" id="black_water_liters" name="black_water_liters" 
                                           class="form-control" min="0" 
                                           value="<?php echo $black_water_liters; ?>" required>
                                </div>
                            </div>

                            <!-- Water / Feeding PH -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="water_liters">Water (Liters)</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="water_liters" name="water_liters" 
                                          class="form-control" min="0" 
                                          value="<?php echo $water_liters; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="feeding_ph">Feeding pH</label>
                                <div class="col-md-7">
                                    <input type="number" step="0.01" id="feeding_ph" name="feeding_ph" 
                                           class="form-control" min="0" 
                                           value="<?php echo $feeding_ph; ?>" required>
                                </div>
                            </div>

</div>
<div class="col-md-5">
    <!-- Outlet PH / Flowmeter Start -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="outlet_ph">Outlet pH</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="outlet_ph" name="outlet_ph" 
                                          class="form-control" min="0" 
                                          value="<?php echo $outlet_ph; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="flowmeter_start">Flowmeter Start Reading</label>
                                <div class="col-md-7">
                                    <input type="number" step="0.01" id="flowmeter_start" name="flowmeter_start" 
                                           class="form-control" min="0" 
                                           value="<?php echo $flowmeter_start; ?>" required>
                                </div>
                            </div>

                            <!-- Flowmeter Stop / Gen set Start Hrs -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="flowmeter_stop">Flowmeter Stop Reading</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="flowmeter_stop" name="flowmeter_stop" 
                                          class="form-control" min="0" 
                                          value="<?php echo $flowmeter_stop; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="genset_start_hrs">Gen set Start Hrs</label>
                                <div class="col-md-7">
                                    <input type="number" step="0.01" id="genset_start_hrs" name="genset_start_hrs" 
                                           class="form-control" min="0" 
                                           value="<?php echo $genset_start_hrs; ?>" required>
                                </div>
                            </div>

                            <!-- Gen set Stop Hrs / Start KWH -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="genset_stop_hrs">Gen set Stop Hrs</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="genset_stop_hrs" name="genset_stop_hrs" 
                                          class="form-control" min="0" 
                                          value="<?php echo $genset_stop_hrs; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="start_kwh">Start KWH</label>
                                <div class="col-md-7">
                                    <input type="number" step="0.01" id="start_kwh" name="start_kwh" 
                                           class="form-control" min="0" 
                                           value="<?php echo $start_kwh; ?>" required>
                                </div>
                            </div>

                            <!-- Stop KWH / Remarks -->
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="stop_kwh">Stop KWH</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="stop_kwh" name="stop_kwh" 
                                          class="form-control" min="0" 
                                          value="<?php echo $stop_kwh; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="remarks">Remarks</label>
                                <div class="col-md-7">
                                    <textarea name="remarks" id="remarks" class="form-control" rows="2" 
                                              placeholder="Any remarks..."><?php echo $remarks; ?></textarea>
                                </div>
                            </div>
</div>
</div>
                            
                            

                            <!-- Buttons -->
                            <div class="form-group row">
                                <div class="col-md-12 text-center">
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
