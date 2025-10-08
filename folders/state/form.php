<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$country_id       = "";
$state_name       = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "states";

        $columns    = [
            "country_unique_id",
            "state_name"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $state_values  = $pdo->select($table_details,$where);

        if ($state_values->status) {

            $state_values     = $state_values->data;

            $country_id       = $state_values[0]["country_unique_id"];
            $state_name       = $state_values[0]["state_name"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$country_options  = country();

$country_options  = select_option($country_options,"Select the Country",$country_id); 

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="country_name"> Country </label>
                                <div class="col-md-3">
                                    <select name="country_name" id="country_name" class="select2 form-control" required>
                                        <?php echo $country_options;?>
                                    </select>
                                </div>  </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="state_name"> State Name </label>
                                <div class="col-md-3">
                                    <input type="text" id="state_name" name="state_name" class="form-control" value="<?php echo $state_name; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                  
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                      <?php echo btn_cancel($btn_cancel);?>
                                </div>
                                
                            </div>
                    </div>
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  