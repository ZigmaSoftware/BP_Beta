<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$continent_id       = "";
$country_name       = "";
$country_code       = "";
$country_currency   = "";
$currency_symbol    = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "countries";

        $columns    = [
            "continent_unique_id",
            "name",
            "code",
            // "currency",
            // "currency_symbol"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $country_values  = $pdo->select($table_details,$where);

        if ($country_values->status) {

            $country_values     = $country_values->data;

            $continent_id       = $country_values[0]["continent_unique_id"];
            $country_name       = $country_values[0]["name"];
            $country_code       = $country_values[0]["code"];
            // $country_currency   = $country_values[0]["currency"];
            // $currency_symbol    = $country_values[0]["currency_symbol"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$continent_options  = continent();

$continent_options  = select_option($continent_options,"Select the Continent",$continent_id); 

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
                                <label class="col-md-2 col-form-label textright" for="continent_name"> Continent Name</label>
                                <div class="col-md-3">
                                    <select name="continent_name" id="continent_name" class="select2 form-control" required>
                                        <?php echo $continent_options;?>
                                    </select>
                                </div> </div>
                                  <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="country_name"> Country Name </label>
                                <div class="col-md-3">
                                    <input type="text" id="country_name" name="country_name" class="form-control" value="<?php echo $country_name; ?>" required>
                                </div> </div>
                           
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="country_code"> Country Code</label>
                                <div class="col-md-3">
                                    <input type="text" id="country_code" name="country_code" class="form-control" value="<?php echo $country_code; ?>" required>
                                </div>
                                 </div>
                            <!--      <div class="form-group row ">-->
                            <!--    <label class="col-md-2 col-form-label textright" for="country_currency">  Country Currency </label>-->
                            <!--    <div class="col-md-3">-->
                            <!--        <input type="text" id="country_currency" name="country_currency" class="form-control" value="<?php echo $country_currency; ?>" >-->
                            <!--    </div>-->
                            <!--</div>-->
                            <!--<div class="form-group row ">-->
                            <!--    <label class="col-md-2 col-form-label textright" for="currency_symbol"> Currency Symbol</label>-->
                            <!--    <div class="col-md-3">-->
                            <!--        <input type="text" id="currency_symbol" name="currency_symbol" class="form-control" value="<?php echo $currency_symbol; ?>" >-->
                            <!--    </div>-->
                                
                            <!--</div>-->
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