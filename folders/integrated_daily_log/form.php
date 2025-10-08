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

if(isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $where      = ["unique_id" => $unique_id];
    $table      =  "integrated_dailylogsheet_master";
    $columns    = array_merge(["company_name","project_name","application_type","is_active"], $checkbox_fields);
    $table_details   = [$table, $columns]; 

    $result_values  = $pdo->select($table_details,$where);

    if ($result_values->status) {
        $result_values      = $result_values->data;

        $company_name       = $result_values[0]["company_name"];
        $project_name       = $result_values[0]["project_name"];
        $application_type   = $result_values[0]["application_type"];
        $is_active          = $result_values[0]["is_active"];

        // Load checkbox values from DB
        foreach ($checkbox_fields as $field) {
            if(isset($result_values[0][$field])) {
                $checkbox_values[$field] = $result_values[0][$field];
            }
        }

        $btn_text       = "Update";
        $btn_action     = "update";
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
                                    <select name="project_name" id="project_name" class="select2 form-control" required>
                                        <?php echo $project_options;?>
                                    </select>
                                </div>
                            </div>

                            <!-- Application Type -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="application_type">Application Type <span style="color:red">*</span></label>
                                <div class="col-md-3">
                                    <select id="application_type" name="application_type" class="form-control select2" required>
                                        <option value="">Select Type</option>
                                        <option value="MRF" <?= (strtolower(trim($application_type)) == "mrf") ? "selected" : "" ?>>MRF</option>
                                        <option value="MSW" <?= (strtolower(trim($application_type)) == "msw") ? "selected" : "" ?>>MSW</option>
                                        <option value="CBG" <?= (strtolower(trim($application_type)) == "cbg") ? "selected" : "" ?>>CBG</option>
                                        <option value="Composting" <?= (strtolower(trim($application_type)) == "composting") ? "selected" : "" ?>>Composting</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright" for="is_active">Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>

                            <!-- Checkbox Fields (List style with Select All) -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label textright">Select Fields to Enable</label>
                                <div class="col-md-10">

                                    <!-- Select All -->
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="select_all">
                                        <label class="form-check-label font-weight-bold" for="select_all">Select All</label>
                                    </div>

                                    <!-- List of checkboxes -->
                                    <div class="list-group">
                                        <?php
                                        foreach ($checkbox_fields as $field) {
                                            $checked = ($checkbox_values[$field] == 1) ? "checked" : "";
                                            echo '<div class="form-check list-group-item">';
                                            echo '<input class="form-check-input field-checkbox" type="checkbox" id="'.$field.'" name="fields['.$field.']" value="1" '.$checked.'>';
                                            echo '<label class="form-check-label" for="'.$field.'">'.ucwords(str_replace("_"," ",$field)).'</label>';
                                            echo '</div>';
                                        }
                                        ?>
                                    </div>

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


