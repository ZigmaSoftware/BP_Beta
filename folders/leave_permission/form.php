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

$day_type           = "";

$is_approved           = 0;
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
$leave_days            = 0;
$reason                = "";
$approve               = "";
$ceo_approve           = 0;
$border_class          = "";
$day_type_class        = "";
$cancel_class          = " d-none ";

$ceo_staff_class       = " d-none ";
$ceo_name              = "";

$leads_approval_class  = " d-none ";
$leads_approval        = 0;


if($_SESSION['sess_user_type'] == $admin_user_type) {
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
            "on_duty_type",
            "on_duty_from_date",
            "on_duty_to_date",
            "on_duty_leave_days",
            "onduty_half_day_type",
            "is_approved",
            "leads_approval",
            "ceo_name"
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
            $on_duty_type               = $result_values["on_duty_type"];
            $on_duty_from_date          = $result_values["on_duty_from_date"];
            $on_duty_half_date          = $result_values["on_duty_from_date"];
            $on_duty_to_date            = $result_values["on_duty_to_date"];
            $on_duty_leave_days         = $result_values["on_duty_leave_days"];
            $on_duty_half_day_type      = $result_values["onduty_half_day_type"];
            $is_approved                = $result_values["is_approved"];
            $is_approved                = $result_values["is_approved"];
            $leads_approval             = $result_values["leads_approval"];
            $ceo_name                   = $result_values["ceo_name"];

            if(($result_values['is_approved'] != 0) || (($result_values['is_approved'] == 0)&&($result_values['leads_approval'] != 0))){ 
                $border_class = " border-0 ";
                $cancel_class = " d-none ";
                $day_type_class = " disabled ";
               
            }else{
                $border_class = "";
                $day_type_class = "";
                $cancel_class   = " d-none ";
            }

            if($result_values["leads_approval"] == 2){
                $leads_approval_class  = "";
                $ceo_staff_class       = "";
                $border_class = " border-0 ";
                $cancel_class = "";
                $day_type_class = " disabled ";
            }else if($result_values["leads_approval"] == 1){
                $leads_approval_class  = " d-none ";
                $ceo_staff_class       = " d-none ";
                $border_class = " border-0 ";
                $cancel_class = "";
                $day_type_class = " disabled ";
            }

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

$half_day_options          = select_option($half_day_options,"Select",$half_day_type);
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

$leave_type_options  = [
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
$approve_options = [
    
    [
        "id"    => 1,
        "text"  => "Cancel"
    ],
];

$approve_options    = select_option($approve_options,"Select",$approve);
$ceo_approve_options = [
    
   [
        "id"    => 1,
        "text"  => "HO Approval Required"
    ],
    [
        "id"    => 2,
        "text"  => "CEO Approval Required"
    ],
];

$ceo_approve_options    = select_option($ceo_approve_options,"Select",$leads_approval);


$ceo_staff_name_options    = staff_ceo_name();

$ceo_staff_name_options    = select_option($ceo_staff_name_options,"Select",$ceo_name);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off">
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row">
                                <input type="hidden" name="is_approved" class="form-control" id="is_approved" value="<?php echo $is_approved;?>" required>
                                <input type="hidden" name="is_lead_approved" class="form-control" id="is_lead_approved" value="<?php echo $leads_approval;?>" required>
                                <label class="col-md-2 col-form-label" for="staff_id"> Staff Name</label>
                                <div class="col-md-4">
                                    <select name="staff_id"  id="staff_id" class="select2 form-control  <?=$border_class;?>" onchange = "get_ho_staff(this.value),get_staff_designation(this.value)" <?=$staff_id_class;?>  required>
                                        <?php echo $staff_name_options; ?>

                                        <input type="hidden" name="designation" class="form-control" id="designation" value="" >
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="day_type"> Day Type </label>
                                <div class="col-md-4">
                                    <select name="day_type" id="day_type" class="select2 form-control" <?=$day_type_class;?> onchange="day_type_check()" required>
                                        <?php echo $day_type_options;?>
                                    </select>
                                </div>

                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label onduty_div day_div" for="on_duty_type">Type</label>
                                <div class="col-md-4 onduty_div day_div">
                                    <select name="on_duty_type" id="on_duty_type" onchange="day_type_check_onduty()"class="select2 form-control onduty_inp day_inp  <?=$border_class;?>" <?=$day_type_class;?>>
                                        <?php echo $on_duty_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label onduty_full_day_div day_div" for="on_duty_from_date"> From Date </label>
                                <div class="col-md-4 onduty_full_day_div day_div">
                                    <input type="date" name="on_duty_from_date" onchange="leave_days_calculate()" class="form-control onduty_full_day_inp day_inp  <?=$border_class;?>" value="<?php echo $on_duty_from_date; ?>" id="on_duty_from_date" required>
                                </div>
                            </div>
                            <div class="form-group row onduty_full_day_div day_div">
                                <label class="col-md-2 col-form-label" for="on_duty_to_date"> To Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="on_duty_to_date" onchange="leave_days_calculate()" class="form-control onduty_full_day_inp day_inp  <?=$border_class;?>" id="on_duty_to_date" value="<?php echo $on_duty_to_date; ?>" onchange="from_date_check(this.value)" required>
                                </div>
                                <label class="col-md-2 col-form-label onduty_full_day_div day_div" for="on_duty_leave_days"> Days</label>
                                <div class="col-md-4 onduty_full_day_div day_div">
                                    <input type="number" name="on_duty_leave_days" id="on_duty_leave_days" class="form-control onduty_full_day_inp day_inp  <?=$border_class;?>" value="<?php echo $on_duty_leave_days?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row on_duty_half_day_div day_div">
                                <label class="col-md-2 col-form-label" for="on_duty_half_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="on_duty_half_date" class="form-control on_duty_half_day_inp day_inp  <?=$border_class;?>" id="on_duty_half_date" value="<?php echo $on_duty_half_date;?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="onduty_half_day_type"> Half Day Type </label>
                                <div class="col-md-4">
                                    <!-- <input type="date" name="half_date" class="form-control on_duty_half_day_inp day_inp" id="half_date" value="<?php echo $half_date;?>" required> -->
                                    <select name="onduty_half_day_type" id="onduty_half_day_type" class="select2 form-control on_duty_half_day_inp day_inp  <?=$border_class;?>">
                                        <?php echo $on_duty_half_day_options; ?>
                                    </select>
                                </div>
                            </div>     
                            <div class="form-group row full_day_div day_div">
                                <label class="col-md-2 col-form-label" for="from_date"> From Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="from_date" onchange="leave_days_calculate()" class="form-control full_day_inp day_inp  <?=$border_class;?>" value="<?php echo $from_date; ?>" id="from_date" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="to_date"> To Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="to_date" onchange="leave_days_calculate()" class="form-control full_day_inp day_inp  <?=$border_class;?>" id="to_date" value="<?php echo $to_date; ?>" onchange="from_date_check(this.value)" required>
                                </div>
                            </div>
                            <div class="form-group row permission_div day_div">
                                <label class="col-md-2 col-form-label" for="permission_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="permission_date" class="form-control permission_inp day_inp  <?=$border_class;?>" id="permission_date" value="<?php echo $permission_date;?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="permission_time"> Time Period (in Hours) </label>
                                <div class="col-md-4">
                                    <input type="text" name="permission_time" class="form-control permission_inp day_inp  <?=$border_class;?>" id="permission_time" value="<?php echo $permission_time;?>" readonly>
                                </div>
                            </div>
                            <div class="form-group row permission_div day_div">
                                <label class="col-md-2 col-form-label" for="from_time"> From Time </label>
                                <div class="col-md-4">
                                    <input type="time" name="from_time" onchange="permission_time_calculate()" class="form-control permission_inp day_inp  <?=$border_class;?>" step = "900" value="<?php echo $from_time; ?>" id="from_time" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="to_time"> To Time </label>
                                <div class="col-md-4">
                                    <input type="time" name="to_time" onchange="permission_time_calculate()" class="form-control permission_inp day_inp  <?=$border_class;?>" id="to_time" step = "900" value="<?php echo $to_time; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label d-none" for="leave_type">Type</label>
                                <div class="col-md-4 d-none">
                                    <select name="leave_type" id="leave_type" class="select2 form-control  <?=$border_class;?>">
                                        <?php echo $leave_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label full_day_div day_div" for="leave_days"> Days</label>
                                <div class="col-md-4 full_day_div day_div">
                                    <input type="number" name="leave_days" id="leave_days" class="form-control full_day_inp day_inp  <?=$border_class;?>" value="<?php echo $leave_days?>" readonly>
                                </div>
                            </div>
                            
                            <div class="form-group row half_day_div day_div">
                                <label class="col-md-2 col-form-label" for="half_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="half_date" class="form-control half_day_inp day_inp  <?=$border_class;?>" id="half_date" value="<?php echo $half_date;?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="half_day_type"> Half Day Type </label>
                                <div class="col-md-4">
                                    <select name="half_day_type" id="half_day_type" class="select2 form-control half_day_inp day_inp  <?=$border_class;?>">
                                        <?php echo $half_day_options; ?>
                                    </select>
                                </div>
                            </div>                            
                            <div class="fomr-group row">

                                <label for="reason" class="col-md-2 col-form-label">Reason </label>
                                <div class="col-md-4">
                                    <textarea name="reason" id="reason" rows="4" class="form-control  <?=$border_class;?>" required><?php echo $reason; ?></textarea>
                                </div>
                                <label class="col-md-2 col-form-label " for="ho_to_be_approved">HO To Be Approved</label>
                                <div class="col-md-4">
                                   <label class="col-md-4 col-form-label text-primary" for="ho_to_be_approved" id="ho_to_be_approved"></label>
                                   <input type="hidden" name="ho_name" class="form-control" id="ho_name" >
                                </div>
                            </div><br>
                            <!-- <div class="fomr-group row">
                                 <label class="col-md-2 col-form-label <?=$leads_approval_class;?> leads_approval_class" for="leads_approved"> Approve Status </label>
                                <div class="col-md-4 <?=$leads_approval_class;?> <?=$border_class;?> leads_approval_class">
                                    <select name="leads_approved"  id="leads_approved" class="select2 form-control lead_inp <?=$border_class;?> " onchange = "get_ceo_name(this.value)"<?=$day_type_class;?> >
                                        <?php echo $ceo_approve_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label <?=$ceo_staff_class;?> ceo_staff_class" for="ceo_to_be_approved"> CEO Name </label>
                                <div class="col-md-4 <?=$ceo_staff_class;?> ceo_staff_class">
                                    <select name="ceo_to_be_approved" id="ceo_to_be_approved" class="select2 form-control ceo_inp  <?=$border_class;?>" <?=$day_type_class;?>>
                                        <?php echo $ceo_staff_name_options;?>
                                    </select>
                                </div>
                            </div><br> -->
                            <div class="fomr-group row   cancel_status <?=$cancel_class;?>">
                                <label class="col-md-2 col-form-label" for="cancel_status"> Status</label>
                                <div class="col-md-4">
                                    <select name="cancel_status" id="cancel_status" class="select2 form-control">
                                        <?php echo $approve_options; ?>
                                    </select>
                                </div>
                            </div>  <br>
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