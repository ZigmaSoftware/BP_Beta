<?php include 'crud.php'; ?>

<?php 
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";

$unique_id              = "";
$project_id             = ""; 
$entry_date             = date('Y-m-d');
$waste_receive          = "";
$waste_crushing_feeding = "";
$waste_handed_back_ccp  = "";
$water_liters           = "";
$feeding_ph             = "";
$digester_1_ph          = "";
$balloon_1_position     = "";
$remarks                = "";

// If editing an existing record
if(isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $where      = ["unique_id" => $unique_id];
    $table      = "tcs_kolkata_daily_log";
    $columns    = [
        "project_id", 
        "entry_date",
        "waste_receive",
        "waste_crushing_feeding",
        "waste_handed_back_ccp",
        "water_liters",
        "feeding_ph",
        "digester_1_ph",
        "balloon_1_position",
        "remarks"
    ];

    $table_details = [$table, $columns]; 
    $result_values = $pdo->select($table_details,$where);

    if ($result_values->status) {
        $result_values          = $result_values->data;
        $project_id             = $result_values[0]["project_id"]; 
        $entry_date             = $result_values[0]["entry_date"];
        $waste_receive          = $result_values[0]["waste_receive"];
        $waste_crushing_feeding = $result_values[0]["waste_crushing_feeding"];
        $waste_handed_back_ccp = $result_values[0]["waste_handed_back_ccp"];
        $water_liters           = $result_values[0]["water_liters"];
        $feeding_ph             = $result_values[0]["feeding_ph"];
        $digester_1_ph          = $result_values[0]["digester_1_ph"];
        $balloon_1_position     = $result_values[0]["balloon_1_position"];
        $remarks                = $result_values[0]["remarks"];

        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

$project_options  = get_project_by_type('cooking');  
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


                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="waste_receive">Waste Receive (kg)</label>
                                <div class="col-md-7">
                                    <input type="number" id="waste_receive" name="waste_receive" 
                                           class="form-control" min="0" 
                                           value="<?php echo $waste_receive; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="waste_crushing_feeding">Waste Crushing / Feeding (kg)</label>
                                <div class="col-md-7">
                                    <input type="number" id="waste_crushing_feeding" name="waste_crushing_feeding" 
                                           class="form-control" min="0" 
                                           value="<?php echo $waste_crushing_feeding; ?>" required>
                                </div>
                            </div>
                             <div class="form-group row">
                                    <label class="col-md-5 col-form-label labelright" for="waste_handed_back_ccp">Waste Handed Back CCP (kg)</label>
                                    <div class="col-md-7">
                                        <input type="number" id="waste_handed_back_ccp" name="waste_handed_back_ccp" 
                                               class="form-control" min="0" 
                                               value="<?php echo $waste_handed_back_ccp; ?>">
                                    </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="remarks">Remarks</label>
                                <div class="col-md-7">
                                    <textarea name="remarks" id="remarks" class="form-control" rows="2" 
                                              placeholder="Any remarks..."><?php echo $remarks; ?></textarea>
                                </div>
                            </div>
                            
                </div>
                <div class="col-md-5">
                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="water_liters">Water (Liters)</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="water_liters" name="water_liters" 
                                          class="form-control" min="0" 
                                          value="<?php echo $water_liters; ?>" required>
                                </div>
                                </div> 
                                <div class="row">
                                <label class="col-md-5 col-form-label labelright" for="feeding_ph">Feeding pH</label>
                                <div class="col-md-7">
                                    <input type="number" step="0.01" id="feeding_ph" name="feeding_ph" 
                                           class="form-control" min="0" 
                                           value="<?php echo $feeding_ph; ?>" required>
                                </div>
                            </div>

                            <div class="form-group row">
                                <label class="col-md-5 col-form-label labelright" for="digester_1_ph">Digester-1 pH</label>
                                <div class="col-md-7">
                                   <input type="number" step="0.01" id="digester_1_ph" name="digester_1_ph" 
                                          class="form-control" min="0" 
                                          value="<?php echo $digester_1_ph; ?>" required>
                                </div>

                                <label class="col-md-5 col-form-label labelright" for="balloon_1_position">Balloon-1 Position (%)</label>
                                <div class="col-md-7">
                                    <input type="number" id="balloon_1_position" name="balloon_1_position" 
                                           class="form-control" min="0" max="100"
                                           value="<?php echo $balloon_1_position; ?>" required>
                                </div>
                            </div>
                        </div>

                            <div class="form-group row">
                                <div class="col-md-12 text-end">
                                    <?php echo btn_cancel($btn_cancel); ?>
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                </div>
                            </div>
</div>
                        </div>
                    </div>
                </form> 
            </div>
        </div>
    </div>
</div>
