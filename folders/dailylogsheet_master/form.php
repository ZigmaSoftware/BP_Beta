<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$company_name       = "";
$project_name       = "";
$type               = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = ["unique_id" => $unique_id];

        $table      =  "dailylogsheet_master";

        $columns    = [
            "company_name",
            "project_name",
            "type",
            "is_active"
        ];

        $table_details   = [$table, $columns]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {
            $result_values      = $result_values->data;

            $company_name   = $result_values[0]["company_name"];
            $project_name   = $result_values[0]["project_name"];
            $type           = $result_values[0]["type"];
            $is_active      = $result_values[0]["is_active"];

            $btn_text       = "Update";
            $btn_action     = "update";
        } else {
            $btn_text       = "Error";
            $btn_action     = "error";
            $is_btn_disable = "disabled='disabled'";
        }
    }
}

// Dropdown options
$active_status_options    = active_status($is_active);

$company_name_options     = company_name();
$company_name_options     = select_option($company_name_options,"Select the Company",$company_name);

// $project_name_options     = project_name(); // <-- you should have function project_name() in function.php
// $project_name_options     = select_option($project_name_options,"Select the Project",$project_name);

$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_name);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <input type="hidden" id="unique_id" name="unique_id" value="<?= $unique_id ?>">
                <input type="hidden" id="project" name="project" value="<?= $project_name ?>">
                <form class="was-validated"  autocomplete="off" >
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
                                    <?php echo $project_name_options;?>
                                </select>
                            </div>
                        </div>


                        <!-- Type -->
                        <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="type">Type <span style="color:red">*</span></label>
                            <div class="col-md-3">
                                <select id="type" name="type" class="form-control select2" required>
                                    <option value="">Select Type</option>
                                    <option value="CBG" <?= (strtolower(trim($type)) == "cbg") ? "selected" : "" ?>>CBG</option>
                                    <option value="Cooking" <?= (strtolower(trim($type)) == "cooking") ? "selected" : "" ?>>Cooking</option>
                                    <option value="Generation" <?= (strtolower(trim($type)) == "generation") ? "selected" : "" ?>>Generation</option>
                                    <option value="Manure" <?= (strtolower(trim($type)) == "manure") ? "selected" : "" ?>>Manure</option>
                                    <option value="Manure+CBG" <?= (strtolower(trim($type)) == "manure+cbg") ? "selected" : "" ?>>Manure+CBG</option>
                                </select>
                            </div>
                        </div>


                                 
                        <!-- Active Status -->
                        <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="is_active">Active Status</label>
                            <div class="col-md-3">
                                <select name="is_active" id="is_active" class="select2 form-control" required>
                                    <?php echo $active_status_options;?>
                                </select>
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
