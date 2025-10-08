<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$ledger_id       = "";
$ledger_sub_group       = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "ledger_sub_group";

        $columns    = [
            "ledger_unique_id",
            "ledger_sub_group"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $ledger_values  = $pdo->select($table_details,$where);

        if ($ledger_values->status) {

            $ledger_values     = $ledger_values->data;

            $ledger_id       = $ledger_values[0]["ledger_unique_id"];
            $ledger_sub_group       = $ledger_values[0]["ledger_sub_group"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$ledger_group_options  = ledger();

$ledger_group_options  = select_option($ledger_group_options,"Select the Ledger Group",$ledger_id); 

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
                                <label class="col-md-2 col-form-label textright" for="ledger_group"> Ledger Group </label>
                                <div class="col-md-3">
                                    <select name="ledger_group" id="ledger_group" class="select2 form-control" required>
                                        <?php echo $ledger_group_options;?>
                                    </select>
                                </div> </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="ledger_sub_group"> Ledger Sub Group </label>
                                <div class="col-md-3">
                                    <input type="text" id="ledger_sub_group" name="ledger_sub_group" class="form-control" value="<?php echo $ledger_sub_group; ?>" required>
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