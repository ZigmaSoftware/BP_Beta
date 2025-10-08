<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$company_name       = "";
$project_name       = "";
$application_type   = "";
$is_active          = 1;

// Checkbox fields
$checkbox_fields = [
    "date_field","week_field","automated_weighbridge","dry_mix_corp","wet_mix_corp",
    "wet_segregated_corp","complete_mix_corp","wet_mix_bwg","dry_mix_bwg",
    "wet_segregated_bwg","complete_mix_bwg","total_waste_actual","total_waste_reported",
    "organic_waste_feed","recycles_generated","rejects_dry_segregation",
    "rejects_wet_segregation","total_inert_disposed","total_rdf_generation",
    "rdf_sold","rdf_stock","slurry_disposed","flare_hrs","cbg_compressor_hrs",
    "raw_biogas_produced","biogas_flared","captive_consumption_gas","digester_temp",
    "fos_tac_ratio","ph_value","cbg_production_kg","cbg_captive_vehicle",
    "cbg_sold_vehicle","cbg_sold_cascades","cbg_sold_pipeline","cbg_total_sold",
    "cbg_stock","manure_production","manure_sold","manure_stock","plant_incharge",
    "remarks"
];

// Initialize checkbox values
$checkbox_values = array_fill_keys($checkbox_fields, 0);

// Prefill (EDIT) — use ENTRY table, not MASTER
if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id = $_GET["unique_id"];

    // pull straight from integrated_dailylogsheet_entry
    $table   = "integrated_dailylogsheet_entry";
    $columns = ["company_name","project_name","application_type","is_active"];
    $where   = ["unique_id" => $unique_id, "is_delete" => 0];

    $res = $pdo->select([$table, $columns], $where);

    if ($res->status && !empty($res->data)) {
        $row = $res->data[0];

        $company_name     = $row["company_name"];
        $project_name     = $row["project_name"];
        $application_type = $row["application_type"];
        $is_active        = $row["is_active"];

        // (Keep flags loading for dynamic fields via JS from MASTER;
        // no need to load $checkbox_values here for prefill.)

        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text       = "Error";
        $btn_action     = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

// Dropdown options
$active_status_options = active_status($is_active);
$company_name_options  = select_option(company_name(),"Select the Company",$company_name);
$project_options       = select_option(get_project_name(),"Select the Project Name",$project_name);

// Build Application Type options (prefill-safe)
$application_type_options = '<option value="">Select Type</option>';

$types_csv = get_application_type_by_project($project_name, $company_name); // returns CSV or single value
if ($types_csv) {
    $types = explode(",", $types_csv);
    foreach ($types as $type) {
        $type = trim($type);
        $sel  = ($type === $application_type) ? 'selected' : '';
        $application_type_options .= '<option value="'.$type.'" '.$sel.'>'.$type.'</option>';
    }
}

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id ?>">
                <input type="hidden" id="project" name="project" value="<?= $project_name ?>">
                <form class="was-validated" autocomplete="off">

                    <div class="row">                                    
                        <div class="col-12">

                            <!-- Company Name -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="company_name">Company Name<span style=color:red>*</span></label>
                                <div class="col-md-3">
                                    <select name="company_name" id="company_name" class="select2 form-control" required onchange="get_project_name(this.value)">
                                        <?php echo $company_name_options;?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Project Name -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="project_name">Project Name<span style=color:red>*</span></label>
                                <div class="col-md-3">
                                    <select id="project_name" name="project_name" class="select2 form-control" required
                                    onchange="get_application_type_by_project(this.value, $('#company_name').val());
                                              get_dailylogsheet_data(this.value, $('#application_type').val(), $('#company_name').val())">
                                    <?php echo $project_options;?>
                                </select>


                                </div>
                            </div>

                            <!-- Application Type -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="application_type">Application Type <span style="color:red">*</span></label>
                                <div class="col-md-3">
                                    <input type="hidden" id="application_type_prefill" value="<?= htmlspecialchars($application_type) ?>">

                                   <select id="application_type" name="application_type" class="form-control select2" required
                                    onchange="get_dailylogsheet_data($('#project_name').val(), this.value, $('#company_name').val())">
                                    <?php echo $application_type_options; ?>
                                </select>

                                </div>
                            </div>

                            <!-- Data entry fields generated from the selected flags -->
                                    <div class="form-group row">
                                      <div class="col-md-10">
                                        <div id="dailylog_dynamic_fields" class="row"></div>
                                      </div>
                                    </div>

                            <!-- Buttons -->
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <?php echo btn_cancel($btn_cancel);?>
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                </div>
                            </div>

                        </div>
                    </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  


