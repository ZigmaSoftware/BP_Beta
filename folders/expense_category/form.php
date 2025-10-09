<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$category_name          = "";
$code     = "";
$description        = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "expense_category";

        $columns    = [
            "category_name",
            "description",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $category_name         = $result_values[0]["category_name"];
            $description        = $result_values[0]["description"];
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


$active_status_options = active_status($is_active);

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
                                <label class="col-md-2 col-form-label textright" for="category_name">Expense Category Name</label>
                                <div class="col-md-3">
                                    <input type="text" id="category_name" name="category_name" class="form-control" value="<?php echo $category_name; ?>" required>
                                </div></div>
                              
                                
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div> </div>
                                 <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="description">Description</label>
                                <div class="col-md-3">
                                    <textarea name="description" id="description" rows="5" class="form-control" > <?php echo $description; ?></textarea>
                                </div>
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