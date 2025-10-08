<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$tax_value          = "";
$tax_name           = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "tax";

        $columns    = [
            "tax_value",
            "tax_name",
            "country"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $state_values  = $pdo->select($table_details,$where);

        if ($state_values->status) {

            $state_values       = $state_values->data;

            $tax_value          = $state_values[0]["tax_value"];
            $tax_name           = $state_values[0]["tax_name"];
            $tax_nation          = $state_values[0]["country"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$tax_nation_options = country($tax_nation);
$tax_nation_options = select_option($tax_nation_options, "Select Country", $tax_nation);
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
                                <label class="col-md-2 col-form-label textright" for="tax_nation"> Tax Nation </label>
                                <div class="col-md-3">
                                    <select id="tax_nation" name="tax_nation" class="form-control select2" required>
                                        <?php echo $tax_nation_options; ?>
                                    </select>
                                </div> 
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="tax_name"> Tax Name </label>
                                <div class="col-md-3">
                                    <input type="text" id="tax_name" name="tax_name" class="form-control" value="<?php echo $tax_name; ?>" required>
                                </div> 
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="tax_value"> Tax Value (%) </label>
                                <div class="col-md-3">
                                    <input type="text" id="tax_value" name="tax_value" class="form-control" value="<?php echo $tax_value; ?>" required>                                    
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