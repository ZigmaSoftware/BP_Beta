<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$menu_name          = "";
$order_no           = "";
$dash_file_name     = "";
$is_active          = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "dashboard_menu";

        $columns    = [
            "menu_name",
            "file_name",
            "order_no",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values  = $result_values->data[0];

            $menu_name      = $result_values["menu_name"];
            $order_no       = $result_values["order_no"];
            $dash_file_name = $result_values["file_name"];
            $is_active      = $result_values["is_active"];

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
                            <label class="col-md-2 col-form-label textright" for="menu_name"> Name </label>
                            <div class="col-md-3">
                                <input type="text" id="menu_name" name="menu_name" class="form-control" value="<?php echo $menu_name; ?>" required>
                            </div>
                            </div>
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="order_no"> Order No </label>
                            <div class="col-md-3">
                                <input type="number" id="order_no" name="order_no" class="form-control" value="<?php echo $order_no; ?>" required>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="dash_file_name"> File Name </label>
                            <div class="col-md-3">
                                <input type="text" id="dash_file_name" name="dash_file_name" class="form-control" value="<?php echo $dash_file_name; ?>" required>
                            </div></div>
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