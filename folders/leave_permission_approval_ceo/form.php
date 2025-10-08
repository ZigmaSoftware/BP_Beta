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
$day_type           = "";

$from_date             = "";
$to_date               = "";
$half_date             = "";
$half_day_type         = "";
$on_duty_half_day_type = "";
$on_duty_type          = "";
$from_time             = "";
$to_time               = "";
$permission_date       = "";
$permission_time       = "";
$leave_type            = "";
$approve            = "";
$rejected_reason    = "";
$reason             = "";
$ho_approved        = "";
$reject_class       = " d-none ";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "leave_details";

        $columns    = [
            "staff_id",
            "day_type",
            "from_date",
            "to_date",
            "from_time",
            "to_time",
            "permission_hours",
            "half_day_type",
            "leave_type",
            "leave_days",
            "reason",
            "is_approved",
            "on_duty_type",
            "on_duty_from_date",
            "on_duty_to_date",
            "on_duty_leave_days",
            "onduty_half_day_type",
            "(SELECT staff_name from staff where staff.unique_id = ".$table.".approve_by) as ho_approved",
            "hod_reject_reason",
            "ceo_name",
            "approved_date",
            "approve_time",
            "entry_date",
            "entry_time"
           // "rejected_reason"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $staff_id                   = $result_values["staff_id"];
            $day_type                   = $result_values["day_type"];
            $from_date                  = $result_values["from_date"];
            $to_date                    = $result_values["to_date"];
            $from_time                  = $result_values["from_time"];
            $to_time                    = $result_values["to_time"];
            $permission_time            = $result_values["permission_hours"];
            $half_date                  = $result_values["from_date"];
            $permission_date            = $result_values["from_date"];
            $half_day_type              = $result_values["half_day_type"];
            $leave_type                 = $result_values["leave_type"];
            $leave_days                 = $result_values["leave_days"];
            $reason                     = $result_values["reason"];
            $approve                    = $result_values["is_approved"];
            $on_duty_type               = $result_values["on_duty_type"];
            $on_duty_from_date          = $result_values["on_duty_from_date"];
            $on_duty_half_date          = $result_values["on_duty_from_date"];
            $on_duty_to_date            = $result_values["on_duty_to_date"];
            $on_duty_leave_days         = $result_values["on_duty_leave_days"];
            $on_duty_half_day_type      = $result_values["onduty_half_day_type"];
            $ho_approved                = $result_values["ho_approved"];
            //$on_duty_half_day_type      = $result_values["onduty_half_day_type"];
            $rejected_reason             = $result_values["hod_reject_reason"];
            $ceo_name                    = $result_values["ceo_name"];
            $approved_date               = $result_values["approved_date"];
            $a_time                      = strtotime($result_values['approve_time']);
            $approve_time                = date("h:i a",$a_time);
            $entry_date                  = $result_values["entry_date"];
            $time                        = strtotime($result_values['entry_time']);
            $entry_time                  = date("h:i a",$time);
            
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$staff_name_options    = staff_name();

$staff_name_options    = select_option($staff_name_options,"Select The Staff Name",$staff_id);

$staff_name  = staff_name($staff_id);
$staff       = $staff_name[0]['staff_name'];

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
    ],
    [
        "id"    => 6,
        "text"  => "Permission"
    ]
];

$day_type_options    = select_option($day_type_options,"Select Day Type",$day_type);

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
$on_duty_half_day_options    = [
    [
        "id"    => 1,
        "text"  => "Forenoon"
    ],
    [
        "id"    => 2,
        "text"  => "Afternoon"
    ]
];

$on_duty_half_day_options  = select_option($on_duty_half_day_options,"Select",$on_duty_half_day_type);


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

$leave_type_options    = select_option($leave_type_options,"Select",$leave_type);

$approve_options = [
    [
        "id"    => 1,
        "text"  => "Approved"
    ],
    [
        "id"    => 2,
        "text"  => "Rejected"
    ],
];

$approve_options    = select_option($approve_options,"Select");
$on_duty_type_options      = [
    [
        "id"    => 1,
        "text"  => "Full Day"
    ],
    [
        "id"    => 2,
        "text"  => "Half Day"
    ],
];
$on_duty_type_options    = select_option($on_duty_type_options,"Select Day Type",$on_duty_type);


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
                                    <select name="staff_id" id="staff_id" class="select2 form-control border-0" required disabled>
                                        <?php echo $staff_name_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="day_type"> Day Type </label>
                                <div class="col-md-4">
                                    <select name="day_type" id="day_type" class="select2 form-control border-0" onchange="day_type_check()" required disabled>
                                        <?php echo $day_type_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label onduty_div day_div" for="on_duty_type">Type</label>
                                <div class="col-md-4 onduty_div day_div">
                                    <select name="on_duty_type" id="on_duty_type" onchange="day_type_check_onduty()"class="select2 form-control onduty_inp day_inp border-0" disabled>
                                        <?php echo $on_duty_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label onduty_full_day_div day_div" for="on_duty_from_date"> From Date </label>
                                <div class="col-md-4 onduty_full_day_div day_div">
                                    <input type="date" name="on_duty_from_date" onchange="leave_days_calculate()" class="form-control onduty_full_day_inp day_inp border-0" value="<?php echo $on_duty_from_date; ?>" id="on_duty_from_date" readonly required>
                                </div>
                            </div>
                            <div class="form-group row onduty_full_day_div day_div">
                                <label class="col-md-2 col-form-label" for="on_duty_to_date"> To Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="on_duty_to_date" onchange="leave_days_calculate()" class="form-control onduty_full_day_inp day_inp border-0" id="on_duty_to_date" value="<?php echo $on_duty_to_date; ?>" onchange="from_date_check(this.value)" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label onduty_full_day_div day_div" for="on_duty_leave_days"> Days</label>
                                <div class="col-md-4 onduty_full_day_div day_div">
                                    <input type="number" name="on_duty_leave_days" id="on_duty_leave_days" class="form-control onduty_full_day_inp day_inp border-0" value="<?php echo $on_duty_leave_days?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row full_day_div day_div">
                                <label class="col-md-2 col-form-label" for="from_date"> From Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="from_date" class="form-control full_day_inp day_inp border-0" value="<?php echo $from_date; ?>" id="from_date" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="to_date"> To Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="to_date" class="form-control full_day_inp day_inp border-0" id="to_date" value="<?php echo $to_date; ?>" onchange="from_date_check(this.value)" required readonly>
                                </div>
                            </div>
                            <div class="form-group row permission_div day_div">
                                <label class="col-md-2 col-form-label" for="permission_date">  Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="permission_date" class="form-control permission_inp day_inp border-0" value="<?php echo $permission_date; ?>" id="permission_date" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="hours"> Time Period (in Hours) </label>
                                <div class="col-md-4">
                                    <input type="text" name="hours" class="form-control permission_inp day_inp border-0" id="hours" value="<?php echo $permission_time; ?>" onchange="from_date_check(this.value)" required readonly>
                                </div>
                            </div>
                            <div class="form-group row permission_div day_div">
                                <label class="col-md-2 col-form-label" for="from_time"> From Time </label>
                                <div class="col-md-4">
                                    <input type="time" name="from_time" class="form-control permission_inp day_inp border-0" value="<?php echo $from_time; ?>" id="from_time" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="to_time"> To Time </label>
                                <div class="col-md-4">
                                    <input type="time" name="to_time" class="form-control permission_inp day_inp border-0" id="to_time" value="<?php echo $to_time; ?>" onchange="from_date_check(this.value)" required readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <!-- <label class="col-md-2 col-form-label" for="leave_type">Type</label>
                                <div class="col-md-4">
                                    <select name="leave_type" id="leave_type" class="select2 form-control" required disabled>
                                        <?php echo $leave_type_options;?>
                                    </select>
                                </div> -->
                                <label class="col-md-2 col-form-label full_day_div day_div" for="leave_days"> Days</label>
                                <div class="col-md-4 full_day_div day_div">
                                    <input type="number" name="leave_days" id="leave_days" class="form-control full_day_inp day_inp border-0" value="<?php echo $leave_days?>" required readonly>
                                </div>
                            </div>
                            
                            <div class="form-group row half_day_div day_div">
                                <label class="col-md-2 col-form-label" for="half_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="half_date" class="form-control half_day_inp day_inp border-0" id="half_date" value="<?php echo $half_date;?>" required readonly>
                                </div>
                            </div>                            
                            <div class="fomr-group row">
                                <label for="reason" class="col-md-2 col-form-label">Reason </label>
                                <div class="col-md-4">
                                    <textarea name="reason" id="reason" rows="3" class="form-control border-0" required readonly><?php echo $reason; ?></textarea>
                                </div>
                                <?php if($ceo_name != 'staff5ffa90e8f01ed39207'){?>
                                <label class="col-md-2 col-form-label" for="ho_approved"> HO Approved </label>
                                <div class="col-md-4">
                                    <input type="text" name="ho_approved" class="form-control  border-0" id="ho_approved" value="<?php echo $ho_approved;?>"  readonly>
                                </div>
                                <?php } ?>
                            </div>
                            <?php if($ceo_name != 'staff5ffa90e8f01ed39207'){?>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="ho_approved_date_time"> HO Approved Date/Time </label>
                                <div class="col-md-4">
                                    <input type="text" name="ho_approved_date_time" class="form-control  border-0" id="ho_approved_date_time" value="<?php echo disdate($approved_date)."/".$approve_time;?>"  readonly>
                                </div>
                                
                                <label class="col-md-2 col-form-label" for="approve_reason"> Approval Reason </label>
                                <div class="col-md-4">
                                    <textarea name="approve_reason" id="approve_reason" rows="4" class="form-control border-0"  readonly><?php echo $rejected_reason; ?></textarea>
                                </div>
                            </div>    
                        <?php } ?>
                            <div class="fomr-group row">
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Status </label>
                                <div class="col-md-4">
                                    <select name="is_approved" id="is_approved" class="select2 form-control" onchange = "get_rejected_reason(this.value)" required>
                                        <?php echo $approve_options;?>
                                    </select>
                                </div>
                                <label for="rejected_reason" class="col-md-2 col-form-label  <?=$reject_class;?> reject_class"> Reason </label>
                                <div class="col-md-4">
                                    <textarea name="rejected_reason" id="rejected_reason" rows="3" class="form-control reason_inp  <?=$reject_class;?> reject_class"></textarea>
                                </div>
                            </div><br><br>
                            <div class="form-group row ">
                                <span style="color: red; font-weight: bold;font-size: 13px;">
                                    <?php echo disname($staff). ' had entered leave/permission on '.disdate($entry_date).' ('.$entry_time.')'; ?>
                                </span>
                            </div>
                            <br>
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