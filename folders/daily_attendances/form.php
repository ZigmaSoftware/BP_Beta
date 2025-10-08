<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Redirect Default Button Cancel to Dashboard
$btn_cancel          = "index.php";
$page_show           = true;
$page_msg            = "";
$unique_id    = "";

if($_SESSION['sess_user_type'] == $hr_user_type){
    $readonly        = '';
    $border_0        = ''; 
    $entry_time      =  date('H:i:s');
    $div_class       = "col-md-2";
    $date_div_class  = "col-md-2";
    $date_class      = "date";
}else{
    $border_0        = ' border-0 '; 
    $readonly        = ' readonly ';
    $entry_time      = date('H:i:s');
    $div_class       = "col-md-4";
    $date_div_class  = "";
    $date_class      = "hidden";
}
$default_class     = '';
$edit_class        = ' d-none ';
$edit_reason_class = '' ;
$day_status_class = '' ;

// if($_SESSION['sess_user_type'] == $admin_user_type) {
    // Get Last Entry Current User
    $last_activity_check = "SELECT attendance_type FROM daily_attendance WHERE is_delete = 0 AND staff_id = '".$_SESSION["staff_id"]."' AND entry_date = '".$today."' ORDER BY id DESC LIMIT 1 ";

    $last_activity_result= $pdo->query($last_activity_check);

    // print_r($last_activity_result);
// if($_SESSION['sess_user_type'] != $hr_user_type){
//     if ($last_activity_result->status) {
//         if (empty($last_activity_result->data)) {
//             if (date('H') > 14) { // 02:00 PM is a last time to check-in
//                 $page_show = false;
//                 $page_msg  = "You can Check-In Until 02:00 PM Only";
//             }
//         } else {
//             if ($last_activity_result->data[0]["attendance_type"] == 2) {
//                 $page_show = false;
//                 $page_msg  = "Today Your Check-out Entry Already placed!";
//             }
//         }
//     }
// }

if (!$page_show) {
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12 text-center">
                        <h4 class="font-weight-bold text-primary"> <?=$page_msg;?> </h4>
                        <?php echo btn_cancel($btn_cancel);?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
} else {

// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$latitude           = "";
$longitude          = "";

$day_status         = 0;
$premises_type      = 0;
$employee_name      = "";
$entry_date         = date('Y-m-d');

$attendance_type    = "";

if(($_SESSION['sess_user_type'] == $admin_user_type)||($_SESSION['sess_user_type'] == $hr_user_type)) {
    $staff_id         = '';
    $staff_id_class   = "";
    $staff_name_class = " disabled ";
} else {
    $staff_id         = $_SESSION['staff_id'];
    $staff_id_class   = " disabled ";
    $staff_name_class = " disabled ";
}

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "daily_attendance";

        $columns    = [
            "entry_date",
            "entry_time",
            "staff_id",
            "attendance_type",
            "day_status",
            "unique_id",
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values   = $result_values->data[0];

            $entry_date      = $result_values['entry_date'];
            $entry_time      = $result_values['entry_time'];
            $staff_id        = $result_values['staff_id'];
            $attendance_type = $result_values['attendance_type'];
            $day_status      = $result_values['day_status'];
            $unique_id       = $result_values['unique_id'];

            // $daily_attendances  = $result_values["daily_attendances"];
            // $description        = $result_values["description"];
            // $is_active          = $result_values["is_active"];
            $attendance_type_options    = [
                    1 => [
                            "unique_id" => 1,
                            "value"     => "Check In",
                        ],
                    2 => [
                            "unique_id" => 2,
                            "value"     => "Check Out",
                        ],
                   
            ];

                                           
            $attendance_type_options  = select_option($attendance_type_options,"Select Attendance Type",$attendance_type);

            $day_status_options            = [
                    
                    1 => [
                        "id"    => 1,
                        "text"  => "Present"
                    ],
                    2 => [
                        "id"    => 2,
                        "text"  => "Late"
                    ],
                    3 => [
                        "id"    => 3,
                        "text"  => "Permission"
                    ],
                    4 => [
                        "id"    => 4,
                        "text"  => "Half-Day Leave"
                    ],
                    5 => [
                        "id"    => 5,
                        "text"  => "Emergency Leave"
                    ],
                ];
            $day_status_options  = select_option($day_status_options,"Select Day Status",$day_status);
            $default_class     = ' d-none ';
            $edit_class        = '';
            $edit_reason_class = ' required ';
            $day_status_class = ' required ';

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            // print_r($result_values);

            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$staff_id_options   = staff_name();
// $staff_id_options   = [];
$staff_id_options   = select_option($staff_id_options,"Select",$staff_id);

if(isset($_GET['form'])){
    $date = $_GET['date'];
    $form = $_GET['form'];
}else{
    $date = '';
    $form = '';
}
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                    <input  type="hidden" id="form_name" name="form_name" class="form-control" value="<?php echo $form; ?>">
                    <input  type="hidden" id="date" name="date" class="form-control" value="<?php echo $date; ?>">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_id"> Employee Name </label>
                                <div class="col-md-4">
                                    <select name="staff_id"  id="staff_id" class="select2 form-control " <?=$staff_id_class;?> onChange = "get_staff_name(this.value);" required>
                                        <?php echo $staff_id_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="employee_name"> Employee ID </label>
                                <div class="col-md-4">
                                    <input  type="text" id="employee_name" name="employee_name" class="form-control" value="<?php echo $employee_name; ?>" readonly required>
                                    <input  type="hidden" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>" readonly >
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="attendance_type"> Attendance Type </label>
                                <div class="col-md-4">
                                    <select name="attendance_type" id="attendance_type" onchange="get_day_status(this.value)" class="select2 form-control" required>
                                        <?php echo $attendance_type_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="server_time"> Server Time </label>
                                <div class="<?=$date_div_class;?>">
                                    <input  type="<?=$date_class;?>" id="entry_date" name="entry_date" class="form-control" value="<?php echo $entry_date; ?>" onchange = 'get_attendance_type(staff_id.value);get_day_type(staff_id.value);' required>
                                </div>
                                <div class="<?=$div_class;?>">
                                    
                                    <input  type="hidden" id="sess_user" name="sess_user" class="form-control" value="<?php echo $_SESSION['sess_user_type']; ?>" readonly required>
                                    <input  type="text" id="entry_time" name="entry_time" class="form-control <?=$border_0;?>" onkeyup="get_day_status(attendance_type.value)" value="<?php echo $entry_time; ?>" <?=$readonly;?> required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="day_status" > Today Status</label>
                                <!-- <div class="col-md-4"> -->
                                <label class="col-md-4 col-form-label text-primary default_class <?=$default_class;?>" for="day_status" id="day_status_text">
                                    
                                </label>
                                <input  type="hidden" id="day_status" name="day_status" class="form-control default_class <?=$default_class;?>" value="<?php echo $day_status; ?>" readonly required>
                                <div class="col-md-4 edit_class <?=$edit_class;?>">
                                    <select name="day_status" id="day_status"  class="select2 form-control" <?=$day_status_class;?>>
                                        <?php echo $day_status_options; ?>
                                    </select>
                                </div>

                                <input  type="hidden" id="premises_type" name="premises_type" class="form-control" value="<?php echo $premises_type; ?>" readonly required>
                                <!-- Branch  Details -->
                                <input  type="hidden" id="branch_rds" name="branch_rds" class="form-control" value="">
                                <input  type="hidden" id="branch_lat" name="branch_lat" class="form-control" value="">
                                <input  type="hidden" id="branch_lng" name="branch_lng" class="form-control" value="">
                                <input  type="hidden" id="loc_check" name="loc_check" class="form-control" value="0">
                                <!-- </div> -->
                                <label class="col-md-2 col-form-label " for="type">Day Type </label>
                                <div class="col-md-4">
                                   <label class="col-md-4 col-form-label text-primary" for="day_type" id="day_type_text"></label>
                                    <input  type="hidden" id="day_type" name="day_type" class="form-control" value="<?php echo $day_type; ?>" readonly >
                                </div>
                                <!-- </div> -->
                                <label class="col-md-2 col-form-label d-none" for="description"> Location Tag</label>
                                <div class="col-md-4 d-none">
                                    <i class="fas fa-map-marker-alt fa-2x" style="color: red"></i>
                                </div>
                            </div>
                            <div class="form-group row edit_class <?=$edit_class;?>">
                                <label class="col-md-2 col-form-label" for="edit_reason" >Edit Reason</label>
                                <div class="col-md-4">
                                    <textarea id = "edit_reason" name="edit_reason" class="form-control" <?=$edit_reason_class;?> rows="4"></textarea>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                  <?php  if(isset($_GET['form'])){?>
                                    <?php echo btn_cancel_dar($btn_cancel,$date);?>
                                    <?php echo btn_createupdate_dar($folder_name_org,$unique_id,$btn_text,$date);?>
                                    
                                    <?php }else{?>
                                        <?php echo btn_cancel($btn_cancel);?>
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                    <?php }?>
                                </div>                                
                            </div>
                    </div>
                </div>
                </form>

                <input type="hidden" name="in_out_check" id="in_out_check" value=0 >

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div> 

<?php } ?>