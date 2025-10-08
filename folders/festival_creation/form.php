<link rel="stylesheet" href="jquery-ui.min.css">
    <script src="jquery-3.6.0.min.js"></script>
    <script src="jquery-ui.min.js"></script>
	

<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>
<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$user_type               = "";
$under_user_type        = "";
$exp_under_user_type     = "";
$description        = "";
$is_active          = 1;
if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {
        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        $table      =  "festival_creation";
        $columns    = [
            "datepicker",
            "description",
            "title",
        ];
        $table_details   = [
            $table,
            $columns
        ];
        $result_values  = $pdo->select($table_details, $where);
        if ($result_values->status) {
            $result_values      = $result_values->data;
            $description    = $result_values[0]["description"];
            $datepicker      = $result_values[0]["datepicker"];
            $title      = $result_values[0]["title"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}
if(isset($_GET['date'])){
    $date = $_GET['date'];
}else{
    $date = date('Y-m-d');
}
// $date = date('d-M-Y');

$active_status_options   = active_status($is_active);
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="datepicker">Select a Date</label>
                                <div class="col-md-4">
                                    <!-- <input type="date" id="entry_date" name="entry_date" class="form-control" value="<?php echo $entry_date; ?>" required> -->
                                       <input type="date" name="datepicker" id="datepicker" class="form-control" value="<?php if($datepicker){echo $datepicker; }else{echo $date;}?>" required>
                                </div></div>
                                <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="description">Description</label>
                                <div class="col-md-4">
                                <textarea id="description" name="description" class="form-control" required><?php echo $description; ?></textarea>
                                </div>
                            </div>
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label textright" for="entry_date">Title</label>
                                <div class="col-md-4">
                                    <input type="text" id="title" name="title" class="form-control" value="<?php echo $title; ?>" required>
                                </div>
                                
                            </div>
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                    <?php echo btn_cancel($btn_cancel); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>

   