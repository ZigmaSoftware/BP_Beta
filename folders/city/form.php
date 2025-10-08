<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$country_id         = "";
$state_id           = "";
$city_name          = "";
$pincode            = "";
$city_type          ="";

$state_options      = "<option value='' disabled='disabled' selected>Select the State</option>";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "cities";

        $columns    = [
            "country_unique_id",
            "state_unique_id",
            "city_name",
            "pincode",
            "city_type"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $city_values            = $pdo->select($table_details,$where);

        if ($city_values->status) {

            $city_values        = $city_values->data;

            $country_id         = $city_values[0]["country_unique_id"];
            $state_id           = $city_values[0]["state_unique_id"];
            $city_name          = $city_values[0]["city_name"];
            $pincode            = $city_values[0]["pincode"];
            $city_type          = $city_values[0]["city_type"];

            $state_options      = state();
            $state_options      = select_option($state_options,"Select the State",$state_id); 

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


$city_type_options        = [
    "1" => [
          "unique_id" => "1",
          "value"     => "Tier 1",
          ],
      "2" => [
          "unique_id" => "2",
          "value"     => "Tier 2",
          ],
  ];
$city_type_options        = select_option($city_type_options,"Select City Type",$city_type);

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
                                    <select name="country_name" id="country_name" class="select2 form-control" onchange="get_states(this.value);" required>
                                        <?php echo $country_options;?>
                                    </select>
                                </div>  </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="state_name"> State </label>
                                <div class="col-md-3">
                                    <select name="state_name" id="state_name" class="select2 form-control" required>
                                        <?php echo $state_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="city_name"> City Name </label>
                                <div class="col-md-3">
                                    <input type="text" id="city_name" name="city_name" class="form-control" value="<?php echo $city_name; ?>" required>
                                </div>
                                </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="pincode"> Pincode </label>
                                <div class="col-md-3">
                                    <input type="text" id="pincode" name="pincode" class="form-control" value="<?php echo $pincode; ?>" >
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="city_type"> City Type </label>
                                    <div class="col-md-3">
                                        <select name="city_type" id="city_type" class="select2 form-control">
                                            
                                        <?php echo $city_type_options;?> 
                                    </select>
                                </div>
                                <div class="col-md-12 btn-action">
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