<style>

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

.label_value {
    font-family: calibri;
    font-size: 14px;
    font-weight: 400;
}

</style>


<?php 
    // Form variables

    $unique_id            = "";
    $table_main           = "leave_details";
    $full_day_div         = " d-none ";
    $half_day_div         = " d-none ";
    $onduty_full_day_div  = " d-none ";
    $on_duty_half_day_div = " d-none ";
    $permission_div       = " d-none ";
    $ceo_name             = "";
    $leave                = "";
    $leave_type           = "";
    $exp_leave_type[]     = "";
    
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
            "half_leave_type",
            "leave_days",
            "reason",
            "leads_approval",
            "(SELECT staff_name from staff where staff.unique_id = ".$table.".ceo_name) as ceo_name",
            "is_approved",
            "(SELECT staff_name from staff where staff.unique_id = ".$table.".approve_by) as approve_by",
            "approved_date",
            "on_duty_type",
            "on_duty_from_date",
            "on_duty_to_date",
            "on_duty_leave_days",
            "onduty_half_day_type",
            "hod_reject_reason",
            "ceo_to_be_approved",
            "ceo_approved as ceo_approve_status",
            "(SELECT staff_name from staff where staff.unique_id = ".$table.".ceo_approve_by) as ceo_approved_staff",
            "ceo_reject_reason",
            "hr_approved",
            "(SELECT staff_name from staff where staff.unique_id = ".$table.".hr_approved_by) as hr_approved_by",
            "hr_reason",
            "approved_date",
            "ceo_approved_date",
            "hr_approved_date",
            "hr_cancel_date",
            "hr_cancel_reason",
            "approve_time",
            "ceo_approve_time",
            "hr_approved_time",
            "entry_date",
            "entry_time"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $staff_id                   = $result_values["staff_id"];
            $day_type_opt               = $result_values["day_type"];
            $from_date                  = $result_values["from_date"];
            $to_date                    = $result_values["to_date"];
            $from_time                  = $result_values["from_time"];
            $to_time                    = $result_values["to_time"];
            $permission_time            = $result_values["permission_hours"];
            $half_date                  = $result_values["from_date"];
            $permission_date            = $result_values["from_date"];
            $half_day_type              = $result_values["half_day_type"];
            $leave_type                 = $result_values["leave_type"];
            $half_leave_type            = $result_values["half_leave_type"];
            $leave_days                 = $result_values["leave_days"];
            $reason                     = $result_values["reason"];
            $is_approved                = $result_values["is_approved"];
            $on_duty_type               = $result_values["on_duty_type"];
            $on_duty_from_date          = $result_values["on_duty_from_date"];
            $on_duty_half_date          = $result_values["on_duty_from_date"];
            $on_duty_to_date            = $result_values["on_duty_to_date"];
            $on_duty_leave_days         = $result_values["on_duty_leave_days"];
            $on_duty_half_day_type      = $result_values["onduty_half_day_type"];
            $rejected_reason            = $result_values["hod_reject_reason"];
            $ceo_to_be_approved         = $result_values["ceo_to_be_approved"];
            $ceo_approved_staff         = $result_values["ceo_approved_staff"];
            $ceo_approve_status         = $result_values["ceo_approve_status"];
            $ceo_rejected_reason        = $result_values["ceo_reject_reason"];
            $hr_approve                 = $result_values["hr_approved"];
            $hr_reason                  = $result_values["hr_reason"];
            $approved_date              = $result_values["approved_date"];
            $approve_by                 = $result_values["approve_by"];
            $ceo_approved_date          = $result_values["ceo_approved_date"];
            $hr_approved_date           = $result_values["hr_approved_date"];
            $hr_approved_by             = $result_values["hr_approved_by"];
            $hr_cancel_date             = $result_values["hr_cancel_date"];
            $hr_cancel_reason           = $result_values["hr_cancel_reason"];
            $ceo_name                   = $result_values["ceo_name"];
            $leads_approval             = $result_values["leads_approval"];
            $a_time                     = strtotime($result_values['approve_time']);
            $approve_time               = date("h:i a",$a_time);
            $entry_date                 = $result_values["entry_date"];
            $time                       = strtotime($result_values['entry_time']);
            $entry_time                 = date("h:i a",$time);
            $hr_time                    = strtotime($result_values['hr_approved_time']);
            $hr_approve_time            = date("h:i a",$hr_time);
            $ceo_time                   = strtotime($result_values['ceo_approve_time']);
            $ceo_approve_time           = date("h:i a",$ceo_time);
           
                if($day_type_opt == 1){
                    $day_type             = "Full Day";
                    $full_day_div         = "";
                    $half_day_div         = " d-none ";
                    $onduty_div           = " d-none ";
                    $onduty_full_day_div  = " d-none ";
                    $on_duty_half_day_div = " d-none ";
                    $permission_div       = " d-none ";
                }else if($day_type_opt == 2){
                    $day_type             = "Half Day";
                    $full_day_div         = " d-none ";
                    $half_day_div         = "";
                    $onduty_full_day_div  = " d-none ";
                    $onduty_div           = " d-none ";
                    $permission_div       = " d-none ";
                    $on_duty_half_day_div = " d-none ";

                    if($half_day_type == 1){
                        $half_day_options = "Forenoon";
                    }else{
                        $half_day_options = "Afternoon";
                    }
                }else if($day_type_opt == 3){
                    $day_type             = "Work From Home";
                    $full_day_div         = "";
                    $half_day_div         = " d-none ";
                    $onduty_full_day_div  = " d-none ";
                    $on_duty_half_day_div = " d-none ";
                    $onduty_div           = " d-none ";
                    $permission_div       = " d-none ";
                }else if($day_type_opt == 4){
                    $day_type             = "Idle";
                    $full_day_div         = "";
                    $half_day_div         = " d-none ";
                    $onduty_full_day_div  = " d-none ";
                    $on_duty_half_day_div = " d-none ";
                    $permission_div       = " d-none ";
                    $onduty_div           = " d-none ";
                }else if($day_type_opt == 5){
                    $day_type             = "On-Duty";
                    $full_day_div         = " d-none ";
                    $half_day_div         = " d-none ";
                    $onduty_div           = "";
                    if ($on_duty_type == 1) {
                        $on_duty_half_day_div = " d-none ";
                        $onduty_full_day_div  = "";
                        $on_duty_type_options = "Full Day";
                    }else{
                        $on_duty_half_day_div = "";
                        $on_duty_type_options = "Half Day";
                        $onduty_full_day_div  = " d-none ";
                        if($on_duty_half_day_type == 1){
                            $on_duty_half_day_options = "Forenoon";
                        }else{
                            $on_duty_half_day_options = "Afternoon";
                        }
                    }
                    $permission_div       = " d-none ";
                }else if($day_type_opt == 6){
                    $day_type             = "Permission";
                    $full_day_div         = " d-none ";
                    $half_day_div         = " d-none ";
                    $onduty_div           = " d-none ";
                    $onduty_full_day_div  = " d-none ";
                    $on_duty_half_day_div = " d-none ";
                    $permission_div       = "";
                }

            if($is_approved == 1){
                $approve_options  = "CEO Approval Required";
                $ceo_staff_class  = "";
                $ceo_name_details = staff_name($ceo_to_be_approved);
                $ceo_name         = $ceo_name_details[0]['staff_name'];
                $ho_approve_class= "";
                $ceo_to_be_approved_class = "";
            }else if($is_approved == 2){
                $approve_options = "Approved";
                $ceo_to_be_approved_class = " d-none ";
                $ho_approve_class= "";
                $ceo_staff_class = " d-none ";
            }else if ($is_approved == 3) {
                $approve_options = "Rejected";
                $ceo_to_be_approved_class = " d-none ";
                $ho_approve_class= "";
                $ceo_staff_class = " d-none ";
            }else{
                $approve_options = "Pending";
                $ceo_staff_class = " d-none ";
                $reject_class    = " d-none ";
                $ceo_to_be_approved_class = " d-none ";
                $ho_approve_class= " d-none ";
            }
            if($ceo_approve_status == 1){
                $ceo_approved_class = "";
                $ceo_approved       = "Approved";
            }else{
                $ceo_approved       = "Rejected";
                $ceo_approved_class = " d-none ";
            }
            if($hr_approve == 1){
                $hr_approved_class  = "";
                $hr_approved_status = "Approved";
                $hr_cancel_class    = " d-none ";
            }else if($hr_approve == 2){
                $hr_approved_class  = "";
                $hr_cancel_class  = "";
                $hr_approved_status = "Rejected";
            }else{
                $hr_approved_status = "Pending";
                $hr_approved_class  = " d-none ";
                $hr_cancel_class    = " d-none ";
            }


            // if($leads_approval == 1){
            //     $lead_status      = "HO Approval Required";
            //     $ceo_staff_class  = "";
            //     $ho_approve_class = "";
            //     $lead_approve_class = "";
            //     $ceo_to_be_approved_class = " d-none ";
            //     $ceo_name_class  = " d-none "; 
            // }else if($leads_approval == 2){
            //     $lead_status = "CEO Approval Required";
            //     $ceo_to_be_approved_class = " d-none ";
            //     $ho_approve_class= " d-none ";
            //     $ceo_staff_class = " d-none ";
            //     $lead_approve_class = "";
            //     $ceo_name_class  = " d-none ";
            // }else{
            //     $lead_status = "Pending";
            //     $ceo_staff_class = " d-none ";
            //     $reject_class    = " d-none ";
            //     $ceo_to_be_approved_class = " d-none ";
            //     $ho_approve_class= " d-none ";
            // }

            $table_data_leave_sub = "";

            $start_date1 = ($from_date);
            $end_date   = ($to_date);

            
            $exp_leave_type = explode(',',$leave_type);
            $exp_half_leave_type = explode(',',$half_leave_type);


           $dates = array();

            if($hr_approve != 0){
                for ($i = 0; $i < $leave_days; $i++) {
                    $dates[]    = $start_date1;
                    $start_date = date ("Y-m-d", strtotime("+".$i." day", strtotime($start_date1)));
                
                    switch($exp_leave_type[$i]){
                        case '1':
                            $leave = "EL";
                            break;
                        case '2':
                            $leave = "CL";
                            break;
                        case '3':
                            $leave = "SL";
                            break;
                        case '4':
                            $leave = "Comp Off";
                            break;
                        case '5':
                            $leave = "SPL Leave";
                            break;
                        case '6':
                            $leave = "LOP";
                            break;
                        case '7':
                            $leave = "EL Half";
                            break;
                        case '8':
                            $leave = "CL Half";
                            break;
                        case '9':
                            $leave = "SL Half";
                            break;
                        case '10':
                            $leave = "Comp Off Half";
                            break;
                        case '11':
                            $leave = "SPL Leave Half";
                            break;
                        case '12':
                            $leave = "LOP Half";
                            break;
                        default :
                            $leave = "";
                            break;
                    }
                    switch($exp_half_leave_type[$i]){
                        case '7':
                            $half_leave = "EL Half";
                            break;
                        case '8':
                            $half_leave = "CL Half";
                            break;
                        case '9':
                            $half_leave = "SL Half";
                            break;
                        case '10':
                            $half_leave = "Comp Off Half";
                            break;
                        case '11':
                            $half_leave = "SPL Leave Half";
                            break;
                        case '12':
                            $half_leave = "LOP Half";
                            break;
                        default :
                            $half_leave = "";
                            break;
                    }
                    $sno = $i + 1;

                    $table_data_leave_sub  .= "<tr>";

                    $table_data_leave_sub  .= "<td>".$sno."</td>";
                    $table_data_leave_sub  .= "<td>".disdate($start_date)."</td>";
                    $table_data_leave_sub  .= "<td>".$leave." - ".$half_leave."</td>";
                    $table_data_leave_sub  .= "</tr>";
                }
            }
        }
    }
}

$staff_name_options    = staff_name($staff_id);
$staff_name            = $staff_name_options[0]['staff_name'];       

    
?>
<input type="hidden" name="unique_id" id="unique_id" value="<?php echo $_GET['unique_id'];?>">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Logo & title -->
                <div class="clearfix" class="col-md-12 ">
                    <div class="col-md-8 float-left row ">
                        <div class="auth-logo">
                            <div class="logo logo-dark mt-3">
                                <span class="">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="90">
                                </span>
                            </div>
        
                            <div class="logo logo-light">
                                <span class="logo-lg">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="22">
                                </span>
                            </div>
                           
                        </div>&nbsp;&nbsp;
                        <div class="mt-2 float-right pl-3">

                            <h2 class=""> <?php echo $_SESSION['sess_company_name'] ; ?></h2>
                            <h5 class=""> <?php echo $_SESSION['sess_company_address'] ; ?></h5>
                            <h5 class=""> <?php echo $_SESSION['sess_company_district'] ; ?></h5>
                            <h5 class=""> <?php echo $_SESSION['sess_company_state'] ; ?></h5>

                            
                    </div><!-- end col -->
                    </div>
                    <div class="float-right">
                        <h1 class="">Leave Approval</h1>
                        <!-- <h5 class=""><strong>Staff Name : </strong> <span class="float-right"> <?php echo $staff_name; ?></span></h5>
                        <h5 class=""><strong>Designation : </strong> <span class="float-right"> <?php echo $designation;?></span></h5>
                        <h5 class=""> <strong>Total Amount : </strong> <span class="float-right"><?php echo moneyFormatIndia($overall_amount); ?></h5> -->
                    </div>
                </div>
                <br>
                <div class="row">                                    
                        <div class="col-12">
                            <div class="row ">
                                <label class="col-md-2 col-form-label" for="staff_id"> Name </label>
                                <label class="col-md-4 label_value" for="staff_id"> <b><?php echo $staff_name; ?></b> </label>
                                <label class="col-md-2 col-form-label" for="day_type"> Day Type </label>
                                <label class="col-md-4 label_value" for="day_type"> <?php echo $day_type; ?> </label>
                            </div>
                            <div class="row ">
                                <label class="col-md-2 col-form-label <?=$onduty_div;?>" for="on_duty_type">Type</label>
                                <label class="col-md-4 label_value <?=$onduty_div;?>" for="on_duty_type_options"> <?php echo $on_duty_type_options; ?> </label>
                                <label class="col-md-2 col-form-label <?=$onduty_full_day_div;?>" for="on_duty_from_date"> From Date </label>
                                <label class="col-md-4 label_value <?=$onduty_div;?>" for="on_duty_from_date"><b><?php echo disdate($on_duty_from_date); ?></b> </label>
                            </div>
                            <div class="row <?=$onduty_full_day_div;?>">
                                <label class="col-md-2 col-form-label" for="on_duty_to_date"> To Date </label>
                                <label class="col-md-4 label_value" for="on_duty_to_date"><b><?php echo disdate($on_duty_to_date); ?></b></label>
                                <label class="col-md-2 col-form-label <?=$onduty_full_day_div;?>" for="on_duty_leave_days"> Days</label>
                                <label class="col-md-4 label_value <?=$onduty_full_day_div;?>" for="on_duty_leave_days"><b> <?=$on_duty_leave_days;?></b></label>
                            </div>
                            <div class="row <?=$on_duty_half_day_div;?>">
                                <label class="col-md-2 col-form-label" for="on_duty_half_date"> Date </label>
                                <label class="col-md-4 label_value <?=$on_duty_half_date;?>" for="on_duty_leave_days"> <b><?php echo disdate($on_duty_half_date);?></b></label>
                                <label class="col-md-2 col-form-label" for="onduty_half_day_type"> Half Day Type </label>
                                <label class="col-md-4 label_value <?=$on_duty_half_date;?>" for="on_duty_leave_days"><?php echo $on_duty_half_day_options;?></label>
                            </div>
                            <div class="row <?=$full_day_div;?>">
                                <label class="col-md-2 col-form-label <?=$full_day_div;?>" for="from_date"> From Date </label>
                                <label class="col-md-4 label_value <?=$full_day_div;?>" for="on_duty_leave_days"><b><?php echo disdate($from_date);?></b></label>
                                <label class="col-md-2 col-form-label <?=$full_day_div;?>" for="to_date"> To Date </label>
                                <label class="col-md-4 label_value <?=$full_day_div;?>" for="on_duty_leave_days"><b><?php echo disdate($to_date);?></b></label>
                            </div>
                            <div class="row <?=$permission_div;?>">
                                <label class="col-md-2 col-form-label" for="permission_date">  Date </label>
                                <label class="col-md-4 label_value <?=$permission_div;?>" for="on_duty_leave_days"><b><?php echo disdate($permission_date);?></b></label>
                                <label class="col-md-2 col-form-label" for="hours"> Time Period (in Hours) </label>
                                <label class="col-md-4 label_value <?=$permission_div;?>" for="on_duty_leave_days"><?php echo $permission_time;?></label>
                            </div>
                            <div class="row  <?=$permission_div;?>">
                                <label class="col-md-2 col-form-label" for="from_time"> From Time </label>
                                <label class="col-md-4 label_value <?=$permission_div;?>" for="on_duty_leave_days"><b><?php echo $from_time;?></b></label>
                                <label class="col-md-2 col-form-label" for="to_time"> To Time </label>
                                <label class="col-md-4 label_value <?=$permission_div;?>" for="on_duty_leave_days"><b><?php echo $to_time;?></b></label>
                            </div>
                            <div class="row <?=$full_day_div;?>">
                                <label class="col-md-2 col-form-label <?=$full_day_div;?>" for="leave_days"> Days</label>
                                <label class="col-md-4 label_value <?=$full_day_div;?>" for="on_duty_leave_days"><b><?php echo $leave_days;?></b></label>
                            </div>
                            <div class="form-group row<?=$half_day_div;?>">
                                <label class="col-md-2 col-form-label" for="half_date"> Date</label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($half_date);?></label>
                                <label class="col-md-2 col-form-label" for="half_day_type"> Half Day Type </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $half_day_options;?></label>
                            </div>                            
                            <div class="row">
                                <label for="reason" class="col-md-2 col-form-label"> Request Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $reason;?></label>
                                <label for="reason" class="col-md-2 col-form-label"> Entry Date / Time </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($entry_date)." / ".$entry_time;?></label>
                            </div>
                            <div class="row <?=$lead_approve_class;?>">
                                <label class="col-md-2 col-form-label <?=$lead_approve_class;?>" for="is_approved">Approved Status</label>
                                <label class="col-md-4 label_value <?=$lead_approve_class;?>" for="half_date"><?php echo $approve_options;?></label>
                                <label for="reason" class="col-md-2 col-form-label <?=$ceo_name_class;?>">CEO Name </label>
                                <label class="col-md-4 label_value <?=$ceo_name_class;?>" for="half_date "><?php echo $ceo_name;?></label>
                            </div>
                            <!-- HO approval details -->
                            <div class="row <?=$ho_approve_class;?>"><span class="col-md-4 text-danger"><strong>HO Approval Details :</strong></span></div>
                            <div class="row <?=$ho_approve_class;?>">
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Status </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $approve_options;?></label>
                                <label for="reason" class="col-md-2 col-form-label">Approved By </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $approve_by;?></label>
                            </div>
                            <div class="row <?=$ho_approve_class;?>">
                                <label class="col-md-2 col-form-label <?=$ceo_to_be_approved_class;?>" for="is_approved">CEO To be Approved</label>
                                <label class="col-md-4 label_value <?=$ceo_to_be_approved_class;?>" for="half_date"><?php echo $ceo_name;?></label>
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Date / Time</label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($approved_date)." / ".$approve_time;?></label>
                                
                            </div>
                            <div class="row <?=$ho_approve_class;?>">
                                
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $rejected_reason;?></label>
                            </div>
                            <!-- CEO approval details -->
                            <div class="row <?=$ceo_approved_class;?>"><span class="col-md-4 text-danger"><strong>CEO Approval Details :</strong></span></div>
                            <div class="row <?=$ceo_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved"> Approved Status</label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo $ceo_approved;?></label>
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved">  Approved By </label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo $ceo_approved_staff;?></label>
                            </div>
                            <div class="row <?=$ceo_approved_class;?>">
                                
                                <label for="rejected_reason" class="col-md-2 col-form-label">  Approved Date / Time </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($ceo_approved_date)." / ".$ceo_approve_time;?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $ceo_rejected_reason;?></label>
                            </div>

                            <!-- HR approval details -->
                            <div class="row <?=$hr_approved_class;?>"><span class="col-md-4 text-danger"><strong>HR Approval Details :</strong></span></div>
                            <div class="row <?=$hr_approved_class;?>">
                                    <div class="col-6">
                                        <!-- Table Begiins -->
                                        <table id="leave_sub_datatable" class="table table-bordered table-striped">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Date</th>
                                                    <th>Leave Type</th>
                                                </tr>
                                            </thead>
                                            <tbody class="leave_datatable">
                                               <?php echo $table_data_leave_sub; ?>
                                            </tbody>
                                        </table>
                                        <!-- Table Ends -->
                                    </div>
                                </div>
                            <div class="row <?=$hr_approved_class;?>">
                                <label class="col-md-2 col-form-label" for="ceo_approved"> Approved By </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hr_approved_by;?></label>
                                <label class="col-md-2 col-form-label" for="ceo_approve_reason">Approved Date / Time</label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($hr_approved_date)." / ".$hr_approve_time;?></label>
                            </div>
                            <div class="row <?=$hr_approved_class;?>">
                                <label class="col-md-2 col-form-label" for="ceo_approved"> Approved Status </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hr_approved_status;?></label>
                                <label class="col-md-2 col-form-label" for="ceo_approve_reason">Approved Reason</label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hr_reason;?></label>
                            </div>  
                            <div class="row <?=$hr_cancel_class;?>">
                                <label class="col-md-2 col-form-label" for="ceo_approved"> Cancel Date </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($hr_cancel_date);?></label>
                                <label class="col-md-2 col-form-label" for="ceo_approve_reason">Cancel Reason</label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hr_cancel_reason;?></label>
                            </div>    
                        </div>
                    </div>

                
                <div class="mt-4 mb-1">
                    <div class="text-right d-print-none">
                    <a href="javascript:window.close();" class="btn btn-danger btn-rounded waves-effect waves-light">Close</a>
                        <a href="javascript:window.print()" class="btn btn-primary  btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                        
                    </div>
                </div>
            </div> <!-- end card-body-->        
        </div>
    </div> <!-- end col -->
</div>