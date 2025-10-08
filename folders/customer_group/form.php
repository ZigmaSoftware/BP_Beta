<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text                   = "Save";
$btn_action                 = "create";
$is_btn_disable             = "";
$unique_id                  = "";
$customer_category_id       = "";
$customer_category_options  = "";
$customer_group             = "";
$description                = "";
$is_active                  = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "customer_group";

        $columns    = [
            "customer_category_id",
            "customer_group",
            "description",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values          = $result_values->data;

            $customer_category_id   = $result_values[0]["customer_category_id"];
            $customer_group         = $result_values[0]["customer_group"];
            $description            = $result_values[0]["description"];
            $is_active              = $result_values[0]["is_active"];

            $btn_text               = "Update";
            $btn_action             = "update";
        } else {
            print_r($result_values);
            $btn_text               = "Error";
            $btn_action             = "error";
            $is_btn_disable         = "disabled='disabled'";
        }
    }
}

$customer_category_options  = customer_category();
$customer_category_options  = select_option($customer_category_options,"Select Customer Categroy",$customer_category_id);
$active_status_options      = active_status($is_active);

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
                                <label class="col-md-2 col-form-label textright" for="customer_category"> Customer Category </label>
                                <div class="col-md-3">
                                    <select name="customer_category" id="customer_category" class="select2 form-control" required>
                                        <?php echo $customer_category_options;?>
                                    </select>
                                </div>  </div> 
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="customer_group"> Customer Group </label>
                                <div class="col-md-3">
                                    <input type="text" id="customer_group" name="customer_group" class="form-control" value="<?php echo $customer_group; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="description"> Description </label>
                                <div class="col-md-3">
                                    <textarea type="text" id="description" name="description" row = "5" class="form-control"><?php echo $description; ?></textarea>
                                </div>   </div> 
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