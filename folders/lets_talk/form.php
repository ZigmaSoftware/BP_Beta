<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>
<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id          = "";
$description           ="";
$user_type               = "";
$under_user_type        = "";
$exp_under_user_type     = "";
$is_active          = 1;
if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {
        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        $table      =  "lets_talk";
        $columns    = [
            // "entry_time",
            "entry_date",
            "employee_name",
            "description",
            // "status",
            "is_active"
        ];
        $table_details   = [
            $table,
            $columns
        ];
        $result_values  = $pdo->select($table_details, $where);
        if ($result_values->status) {
            $result_values      = $result_values->data;
            // $entry_time    = $result_values[0]["entry_time"];
            $entry_date      = $result_values[0]["entry_date"];
            // if($sess_user_type != '5f97fc3257f2525529'){

            $employee_names      = $result_values[0]["employee_name"];
            // }
         
            $description    = $result_values[0]["description"];
            // $status         = $result_values[0]["status"];
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

$date = date('d-M-Y');
$status_options        = [
    "Approve" => [
        "unique_id" => "approve",
        "value"     => "approve",
    ],
    // "pending" => [
    //     "unique_id" => "pending",
    //     "value"     => "pending",
    // ],
    "cancel" => [
        "unique_id" => "cancel",
        "value"     => "cancel",
    ],
];
$status_options        = select_option($status_options, "Select", $status);
if($employee_names != ''){
    $employee_name = $employee_names;
}else{
    $employee_name = $_SESSION["staff_name"];
}


// if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
$staff_name             = $_SESSION["staff_name"];
// }
$sess_user_type  = $_SESSION['sess_user_type'];

$active_status_options   = active_status($is_active);
if(isset($_GET['date'])){
    $date = $_GET['date'];
}else{
    $date = date('Y-m-d');
}
?>
<!-- value='<?php if ($sess_user_type != '5f97fc3257f2525529') { echo $employee_name; } elseif($sess_user_type == '5f97fc3257f2525529') {echo $staff_name; }?>' -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                                           <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label for="staff_name" class="col-md-2 col-form-label">Staff Name</label>
                                <div class="col-md-4">
                                <input type="text" id="staff_name" name="staff_name" class="form-control" value='<?php  echo $employee_name ;  ?>'>    
                      
                                </div>
                                <label class="col-md-2 col-form-label" for="entry_date">Date</label>
                                <div class="col-md-4">
                                    <input type="date" id="entry_date" name="entry_date" class="form-control" value="<?php if($entry_date) { echo $entry_date;} else {echo $date; } ?>" required>
                                </div>

                            </div>
                            <div class="form-group row ">
                            <label class="col-md-2 col-form-label" for="description"> Description</label>
                                <div class="col-md-4">
                                    <textarea name="description" id="description"  rows="5"  class="form-control"><?php echo $description; ?></textarea>
                                </div>
                                <!-- <?php
                                if($sess_user_type == '5f97fc3257f2525529'){?>

                                <label class="col-md-2 col-form-label" for="status">Status</label>
                                <div class="col-md-4">
                                    <select name="status" id="status" class="select2 form-control" required>
                                        <?php echo $status_options; ?>

                                    </select>
                                </div>
                                
                                <?php }?> -->
                                
                            </div>
                            <!-- <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="is_active"> Active Status</label>
                                <div class="col-md-4">
                                    <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options; ?>
                                    </select>
                                </div>

                            </div> -->
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel); ?>
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>