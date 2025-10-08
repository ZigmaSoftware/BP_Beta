<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$call_type          = "";
$is_active          = 1;

$staff_id           = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "call_type";

        $columns    = [
            "call_type",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            // $result_values      = $result_values->data[0];

            // $call_type          = $result_values["call_type"];
            // $is_active          = $result_values["is_active"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

// $active_status_options= active_status($is_active);


$staff_name_options    = staff_name();

$staff_name_options    = select_option($staff_name_options,"Select The Staff Name",$staff_id);

$day_type_options      = [
    [
        "id"    => 1,
        "text"  => "Full Day"
    ],
    [
        "id"    => 2,
        "text"  => "Half Day"
    ],
    [
        "id"    => 3,
        "text"  => "Work From Home"
    ],
    [
        "id"    => 4,
        "text"  => "Idle"
    ],
    [
        "id"    => 5,
        "text"  => "On-Duty"
    ]
];

$day_type_options    = select_option($day_type_options,"Select Day Type");

$half_day_options    = [
    [
        "id"    => 1,
        "text"  => "Forenoon"
    ],
    [
        "id"    => 2,
        "text"  => "Afternoon"
    ]
];

$half_day_options    = select_option($half_day_options,"Select");

$leave_type_options    = [
    [
        "id"    => 1,
        "text"  => "EL"
    ],
    [
        "id"    => 2,
        "text"  => "CL"
    ],
    [
        "id"    => 3,
        "text"  => "SL"
    ],
    [
        "id"    => 4,
        "text"  => "Comp Off"
    ],
    [
        "id"    => 5,
        "text"  => "SPL Leave"
    ]
];

$leave_type_options    = select_option($leave_type_options,"Select");


?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off">
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_id"> Name </label>
                                <div class="col-md-4">
                                    <select name="staff_id" id="staff_id" class="select2 form-control" required>
                                        <?php echo $staff_name_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="day_type"> Day Type </label>
                                <div class="col-md-4">
                                    <select name="day_type" id="day_type" class="select2 form-control" onchange="day_type_check()" required>
                                        <?php echo $day_type_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row full_day_div day_div">
                                <label class="col-md-2 col-form-label" for="from_date"> Leave From Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="from_date" class="form-control full_day_inp day_inp" id="from_date" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="to_date"> Leave To Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="to_date" class="form-control full_day_inp day_inp" id="to_date" required>
                                </div>
                            </div>
                            <div class="form-group row half_day_div day_div">
                                <label class="col-md-2 col-form-label" for="half_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="half_date" class="form-control half_day_inp day_inp" id="half_date" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="leave_type">Leave Type</label>
                                <div class="col-md-4">
                                    <select name="leave_type" id="leave_type" class="select2 form-control" required>
                                        <?php echo $leave_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label full_day_div day_div" for="leave_days">Leave Days</label>
                                <div class="col-md-4 full_day_div day_div">
                                    <input type="number" name="leave_days" id="leave_days" class="form-control full_day_inp day_inp" required>
                                </div>
                            </div>
                            <div class="fomr-group row">
                                <label for="reason" class="col-md-2 col-form-label">Reason for Leave</label>
                                <div class="col-md-4">
                                    <textarea name="reason" id="reason" rows="4" class="form-control" required></textarea>
                                </div>
                            </div>
                            <div class="form-group row ">
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