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
//$leave_type            = "";
$leave_days            = 0;
$reason                = "";
$hr_approve               = "1";
$rejected_reason       = "";
$ho_approved           = "";
$ceo_rejected_reason   = "";
$ceo_approved          = "";
$sublist_class         = " d-none ";
$approve_reason_class  = "";
$cancel_reason_class   = " d-none ";
$hr_cancel_reason      = "";
$hr_approved_date      = date('Y-m-d');
$hr_cancel_date        = $today;


if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "leave_details";
        $table_sub  = "leave_details_sub";


        $columns_sub = [
            "GROUP_CONCAT(DISTINCT unique_id) as sub_unique_id",
            
        ];
        $where_sub = [
            "form_unique_id" => $unique_id
        ];

        $table_details_sub   = [
            $table_sub,
            $columns_sub
        ]; 

        $order_by = " id ASC ";

        $result_values_sub  = $pdo->select($table_details_sub,$where_sub,"","",$order_by);

        if ($result_values_sub->status) {

            $result_values_sub  = $result_values_sub->data[0];

            $sub_unique_id      = $result_values_sub["sub_unique_id"];
            
        }

        $columns_sub = [
            
            "GROUP_CONCAT( half_leave_type) as half_leave_type",
        ];
        $where_sub = [
            "form_unique_id" => $unique_id
        ];

        $table_details_sub   = [
            $table_sub,
            $columns_sub
        ]; 

        $order_by = " id ASC ";

        $result_values_sub  = $pdo->select($table_details_sub,$where_sub,"","",$order_by);
        if ($result_values_sub->status) {

            $result_values_sub  = $result_values_sub->data[0];

           
            $half_leave_type_sub      = $result_values_sub["half_leave_type"];
        }

        $columns_sub = [
            
            "GROUP_CONCAT(comp_off_date) as comp_off_date",
        ];
        $where_sub = [
            "form_unique_id" => $unique_id
        ];

        $table_details_sub   = [
            $table_sub,
            $columns_sub
        ]; 

        $order_by = " id ASC ";

        $result_values_sub  = $pdo->select($table_details_sub,$where_sub,"","",$order_by);
        if ($result_values_sub->status) {

            $result_values_sub  = $result_values_sub->data[0];

           
            $comp_off_date_sub      = $result_values_sub["comp_off_date"];
        }

        $columns_sub = [
            
            "GROUP_CONCAT(comp_off_date_half) as comp_off_date_half",
        ];
        $where_sub = [
            "form_unique_id" => $unique_id
        ];

        $table_details_sub   = [
            $table_sub,
            $columns_sub
        ]; 

        $order_by = " id ASC ";

        $result_values_sub  = $pdo->select($table_details_sub,$where_sub,"","",$order_by);
        if ($result_values_sub->status) {

            $result_values_sub  = $result_values_sub->data[0];

           
            $comp_off_date_half_sub      = $result_values_sub["comp_off_date_half"];
        }


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
            "half_leave_type",
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
            "(SELECT staff_name from staff where staff.unique_id = ".$table.".ceo_approve_by) as ceo_approved",
            "ceo_reject_reason",
            "ceo_approved_date",
            "hr_approved",
            "hr_reason",
            "hr_cancel_reason",
            "hr_approved_date",
            "hr_cancel_date",
            "approve_time",
            "ceo_approve_time",
            "hr_approved_time",
            "entry_date",
            "entry_time",
            "ceo_name",
            "approved_date"
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
            $leave_type_text            = $result_values["leave_type"];
            $half_leave_type_text       = $result_values["half_leave_type"];
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
            $rejected_reason            = $result_values["hod_reject_reason"];
            $ceo_approved               = $result_values["ceo_approved"];
            $ceo_rejected_reason        = $result_values["ceo_reject_reason"];
            $hr_approve                 = $result_values["hr_approved"];
            $hr_reason                  = $result_values["hr_reason"];
            $hr_cancel_reason           = $result_values["hr_cancel_reason"];
            $hr_approved_date           = $result_values["hr_approved_date"];
            $hr_cancel_date             = $result_values["hr_cancel_date"];
            $ceo_name                   = $result_values["ceo_name"];
            $approved_date              = $result_values["approved_date"];
            $a_time                     = strtotime($result_values['approve_time']);
            $approve_time               = date("h:i a",$a_time);
            $entry_date                 = $result_values["entry_date"];
            $time                       = strtotime($result_values['entry_time']);
            $entry_time                 = date("h:i a",$time);
            $hr_time                    = strtotime($result_values['hr_approved_time']);
            $hr_approve_time            = date("h:i a",$hr_time);
            $ceo_time                   = strtotime($result_values['ceo_approve_time']);
            $ceo_approve_time           = date("h:i a",$ceo_time);
            $ceo_approved_date          = $result_values["ceo_approved_date"];
            
            $checked        = "";

            $leave_type   = explode(",",$result_values["leave_type"]);
            $leave_type_imp   = "'".implode("','",$leave_type)."'";
            if(($day_type == 1) ||($day_type == 2)){
                $sublist_class = "";
            }else{
                $sublist_class = " d-none ";
            }

            $btn_text           = "Save";
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

$half_day_options    = select_option($half_day_options,"Select",$half_day_type);
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
        "text"  => "Cancel"
    ],
];

$approve_options    = select_option($approve_options,"Select",$hr_approve);
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
                                <input type="hidden" name="unique_id" id="unique_id" value="<?=$unique_id;?>">
                                <input type="hidden" name="leave_type_imp" id="leave_type_imp" value="<?php echo $leave_type_text; ?>">
                                <input type="hidden" name="half_leave_type_imp" id="half_leave_type_imp" value="<?php echo $half_leave_type_sub; ?>">
                                <input type="hidden" name="comp_of_date_imp" id="comp_of_date_imp" value="<?php echo $comp_off_date_sub; ?>">
                                <input type="hidden" name="comp_of_date_half_imp" id="comp_of_date_half_imp" value="<?php echo $comp_off_date_half_sub; ?>">
                                 <input type="hidden" name="sub_unique_id" id="sub_unique_id" value="<?php echo $sub_unique_id; ?>">
                                <input type="hidden" name="hr_approved_date" id="hr_approved_date" value="<?php echo $hr_approved_date; ?>">
                                <input type="hidden" name="hr_cancel_date" id="hr_cancel_date" value="<?php echo $hr_cancel_date; ?>">
                                <input type="hidden" name="day_type_opt" id="day_type_opt" value="<?php echo $day_type; ?>">
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
                            <div class="form-group row on_duty_half_day_div day_div">
                                <label class="col-md-2 col-form-label" for="on_duty_half_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="on_duty_half_date" class="form-control on_duty_half_day_inp day_inp border-0" id="on_duty_half_date" value="<?php echo $on_duty_half_date;?>" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="onduty_half_day_type"> Half Day Type </label>
                                <div class="col-md-4">
                                    <!-- <input type="date" name="half_date" class="form-control on_duty_half_day_inp day_inp" id="half_date" value="<?php echo $half_date;?>" required> -->
                                    <select name="onduty_half_day_type" id="onduty_half_day_type" class="select2 form-control on_duty_half_day_inp day_inp border-0" disabled>
                                        <?php echo $on_duty_half_day_options; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                               
                                <label class="col-md-2 col-form-label full_day_div day_div" for="leave_days"> Days</label>
                                <div class="col-md-4 full_day_div day_div">
                                    <input type="number" name="leave_days" id="leave_days" class="form-control full_day_inp day_inp border-0" value="<?php echo $leave_days?>" required readonly>
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
                            <div class="form-group row half_day_div day_div">
                                <label class="col-md-2 col-form-label" for="half_date"> Date </label>
                                <div class="col-md-4">
                                    <input type="date" name="half_date" class="form-control half_day_inp day_inp border-0" id="half_date" value="<?php echo $half_date;?>" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="half_day_type"> Half Day Type </label>
                                <div class="col-md-4">
                                    
                                    <select name="half_day_type" id="half_day_type" class="select2 form-control half_day_inp day_inp  <?=$border_class;?>" disabled>
                                        <?php echo $half_day_options; ?>
                                    </select>
                                </div>
                            </div>  
                        <div class="form-group row ">                   
                        <div class="col-xl-6 col-md-12 col-6 sublist_class <?=$sublist_class;?>">
                            <form class="was-validated sublist-form" id="sublist-form">
                            <input type="hidden" name="form_unique_id" id="form_unique_id" value="">  
                                <div class="row">
                                    <div class="col-12">
                                        <!-- Table Begiins -->
                                        <table id="leave_sub_datatable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Split Leave</th>
                                                    <th>Leave Type</th>
                                                    <th>Comp Off Date</th>
                                                </tr>
                                            </thead>
                                            <tbody class="leave_datatable">
                                               
                                            </tbody>
                                        </table>
                                        <!-- Table Ends -->
                                    </div>
                                </div>
                            </form>
                        </div>               
                        <div class="col-xl-6 col-6 sublist_class <?=$sublist_class;?>">
                            <form class="was-validated sublist-form" id="sublist-form">
                            <input type="hidden" name="form_unique_id" id="form_unique_id" value="">  
                                <div class="row">
                                    <div class="col-12 sunday_holiday_tab">
                                        <!-- Table Begiins -->
                                        
                                        <!-- Table Ends -->
                                    </div>
                                </div>
                            </form>
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
                        <?php if($approve == 1){ ?>
                            <div class ="form-group row">
                                <label class="col-md-2 col-form-label" for="ceo_approved_time"> CEO Approved Date</label>
                                <div class="col-md-4">
                                    <input type="text" name="ceo_approved_time" class="form-control  border-0" id="ceo_approved_time" value="<?php echo disdate($ceo_approved_date);?>"  readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="ceo_approved_time"> CEO Approved Time </label>
                                <div class="col-md-4">
                                    <input type="text" name="ceo_approved_time" class="form-control  border-0" id="ceo_approved_time" value="<?php echo $ceo_approve_time;?>"  readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="ceo_approved"> CEO Approved </label>
                                <div class="col-md-4">
                                    <input type="text" name="ceo_approved" class="form-control  border-0" id="ceo_approved" value="<?php echo $ceo_approved;?>" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="ceo_approve_reason"> CEO Approval Reason </label>
                                <div class="col-md-4">
                                    <textarea name="ceo_approve_reason" id="ceo_approve_reason" rows="3" class="form-control border-0" required readonly><?php echo $ceo_rejected_reason; ?></textarea>
                                </div>
                            </div>   
                        <?php } ?>
                            <div class="fomr-group row">
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Status </label>
                                <div class="col-md-4">
                                    <select name="is_approved" id="is_approved" class="select2 form-control" required onchange = "get_reason_text(this.value);">
                                        <?php echo $approve_options;?>
                                    </select>
                                </div>
                                <label for="hr_reason" class="col-md-2 col-form-label approve_reason_class <?=$approve_reason_class;?>"> Reason </label>
                                <div class="col-md-4 approve_reason_class <?=$approve_reason_class;?>">
                                    <textarea name="hr_reason" id="hr_reason" rows="3" class="form-control" ><?=$hr_reason;?></textarea>
                                </div>
                                <label for="hr_cancel_reason" class="col-md-2 col-form-label cancel_reason_class <?=$cancel_reason_class;?>">Cancel Reason </label>
                                <div class="col-md-4 cancel_reason_class <?=$cancel_reason_class;?>">
                                    <textarea name="hr_cancel_reason" id="hr_cancel_reason" rows="3" class="form-control" ><?=$hr_cancel_reason;?></textarea>
                                </div>
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