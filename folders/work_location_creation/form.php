<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$work_location      = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        //  $where ='unique_id = "'.$unique_id.'"';

        $table      =  "work_location_creation";

        $columns    = [
            "work_location",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $work_location  = $result_values[0]["work_location"];
            $is_active          = $result_values[0]["is_active"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$active_status_options= active_status($is_active);


$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);

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
                                <label class="col-md-2 col-form-label textright" for="work_location"> Work Location </label>
                                <div class="col-md-3">
                                    <input type="text" id="work_location" name="work_location" class="form-control" value="<?php echo $work_location; ?>" required>
                                </div>
                                 </div>
                                 
                                 <div class="form-group row ">
                                    <label class="col-md-2 col-form-label textright" for="company_name">Company Name<span style=color:red>*</span> </label>
                                    <div class="col-md-3">
                                        <select name="company_name" id="company_name" class="select2 form-control" onchange="get_company_code(this.value);" required>
                                       <?php echo $company_name_options;?>
                                       </select>
                                       
                                    </div>

                                 
                                 
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
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