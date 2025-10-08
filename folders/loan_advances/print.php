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

$loan_advance       = "";
$emi_type           = "";
$others_type        = "";
$staff_id           = "";
$amount             = "";
$emi                = "";
$emi_amount         = "";
$description        = "";
$approval           = "";
$loan_percentage    = "";
$percentage_req     = "";
$others_type_label  = "";
$emi_type_label     = "";
$hod_app_status     = "";
$hod_approve_by     = "";
$hod_approve_date     = "";
$ceo_to_be_approved     = "";
$ceo_app_status     = "";
$ceo_approval     = "";
$ceo_approve_by     = "";
$ceo_reason     = "";
$ceo_approve_date     = "";
$director_app_status     = "";
$director_approval     = "";
$director_approve_by     = "";
$director_reason     = "";
$director_approve_date     = "";
$hr_app_status     = "";
$hr_approval     = "";
$hr_approve_by     = "";
$hr_reason     = "";
$hr_approve_date     = "";
$accounts_app_status     = "";
$accounts_approval     = "";
$accounts_approve_by     = "";
$accounts_app_reason     = "";
$accounts_approve_date     = "";
$hr_approval_status     = "";
$ceo_name      = "";
$approve_by       = "";
$entry_date         = $today;

$ho_approve_class       = "d-none";
$ceo_approved_class       = "d-none";
$director_approved_class       = "d-none";
$hr_approved_class       = "d-none";
$accounts_approved_class       = "d-none";

    // Form variables

    $unique_id                      = "";
    $table_main                     = "loan_advance";
    
    
    if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "loan_advance";

        $columns    = [
            "loan_no",
            "loan_type",
            "entry_date",
            "others_type",
            "staff_id",
            "amount",
            "emi",
            "emi_type",
            "emi_amount",
            "description",
            "loan_percentage",
            "hod_approval",
            "hod_reason",
            "hod_app_status",
            "hod_approve_by",
            "hod_approve_date",
            "ceo_to_be_approved",
            "ceo_app_status",
            "ceo_approval",
            "ceo_approve_by",
            "ceo_reason",
            "ceo_approve_date",
            "director_app_status",
            "director_approval",
            "director_approve_by",
            "director_reason",
            "director_approve_date",
            "hr_app_status",
            "hr_approval",
            "hr_approve_by",
            "hr_reason",
            "hr_approve_date",
            "accounts_app_status",
            "accounts_approval",
            "accounts_approve_by",
            "accounts_app_reason",
            "accounts_approve_date",
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

        $loan_no            = $result_values["loan_no"];
        $loan_advance       = $result_values["loan_type"];
        $entry_date         = $result_values["entry_date"];
        $others_type        = $result_values["others_type"];
        $staff_id           = $result_values["staff_id"];
        $amount             = $result_values["amount"];
        $emi                = $result_values["emi"];
        $emi_type            = $result_values["emi_type"];
        $emi_amount          = $result_values["emi_amount"];
        $description         = $result_values["description"];
        $loan_percentage     = $result_values["loan_percentage"];
        $hod_approval        = $result_values["hod_approval"];
        $hod_app_status      = $result_values["hod_app_status"];
        $hod_reason          = $result_values["hod_reason"];
        $hod_approval        = $result_values["hod_approval"];
        $hod_approve_by      = $result_values["hod_approve_by"];
        $hod_approve_date    = $result_values["hod_approve_date"];
        $ceo_to_be_approved  = $result_values["ceo_to_be_approved"];
        $ceo_app_status      = $result_values["ceo_app_status"];
        $ceo_approval        = $result_values["ceo_approval"];
        $ceo_approve_by      = $result_values["ceo_approve_by"];
        $ceo_reason          = $result_values["ceo_reason"];
        $ceo_approve_date    = $result_values["ceo_approve_date"];
        $director_app_status = $result_values["director_app_status"];
        $director_approval   = $result_values["director_approval"];
        $director_approve_by = $result_values["director_approve_by"];
        $director_reason     = $result_values["director_reason"];
        $director_approve_date   = $result_values["director_approve_date"];
        $hr_app_status           = $result_values["hr_app_status"];
        $hr_approval             = $result_values["hr_approval"];
        $hr_approve_by           = $result_values["hr_approve_by"];
        $hr_reason               = $result_values["hr_reason"];
        $hr_approve_date         = $result_values["hr_approve_date"];
        $accounts_app_status     = $result_values["accounts_app_status"];
        $accounts_approval       = $result_values["accounts_approval"];
        $accounts_approve_by     = $result_values["accounts_approve_by"];
        $accounts_app_reason     = $result_values["accounts_app_reason"];
        $accounts_approve_date   = $result_values["accounts_approve_date"];

            if(($hr_app_status == 1)){
                if($hr_approval == 0){
                    $hr_approval_status = 'Pending';
                }else if($hr_approval == 1){
                    $hr_approval_status = 'CEO Approval Required';
                }else if($hr_approval == 2){
                    $hr_approval_status = 'Rejected';
                }
                $ceo_staff_class  = "";
                $ceo_name_details = staff_name($ceo_to_be_approved);
                $ceo_name         = $ceo_name_details[0]['staff_name'];
                $ho_approve_class= "";
                $ceo_to_be_approved_class = "";
            }

            if(($ceo_app_status == 1)){
                if($ceo_approval == 0){
                    $ceo_approval_status = 'Pending';
                }else if($ceo_approval == 1){
                    $ceo_approval_status = 'HR Approval Required';
                }else if($ceo_approval == 2){
                    $ceo_approval_status = 'Director Approval Required';
                }else if($ceo_approval == 3){
                    $ceo_approval_status = 'Rejected';
                }
                
                $ceo_approved_class= "";
            }


            if(($director_app_status == 1)){
                if($director_approval == 0){
                    $director_approval_status = 'Pending';
                }else if($director_approval == 1){
                    $director_approval_status = 'HR Approval Required';
                }else if($director_approval == 2){
                    $director_approval_status = 'Rejected';
                }
                
                $director_approved_class= "";
            }

            if(($hr_app_status == 1)){
                if($hr_approval == 0){
                    $hr_approval_status = 'Pending';
                }else if($hr_approval == 1){
                    $hr_approval_status = 'Accounts Approval Required';
                }else if($hr_approval == 2){
                    $hr_approval_status = 'Rejected';
                }
                
                $hr_approved_class= "";
            }

            if(($accounts_app_status == 1)){
                if($accounts_approval == 0){
                    $accounts_approval_status = 'Pending';
                }else if($accounts_approval == 1){
                    $accounts_approval_status = 'Approved';
                }else if($accounts_approval == 2){
                    $accounts_approval_status = 'Rejected';
                }
                
                $accounts_approved_class= "";
            }

        if($loan_advance == 1){
            $loan_advance_val = 'Loan';
        }else if($loan_advance == 2){
            $loan_advance_val = 'Advance';
        }else if($loan_advance == 3){
            $loan_advance_val = 'Others';
        
        } 


        if($others_type == 1){
            $others_type_label = 'Addition';
        }else if($others_type == 2){
            $others_type_label = 'Deduction';
        }

        if($emi_type == 1){
            $emi_type_label = 'Monthly';
        }else if($emi_type == 2){
            $emi_type_label = 'Weekly';
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
                        <h1 class="">Loan Advances</h1>
                        <!-- <h5 class=""><strong>Staff Name : </strong> <span class="float-right"> <?php echo $staff_name; ?></span></h5>
                        <h5 class=""><strong>Designation : </strong> <span class="float-right"> <?php echo $designation;?></span></h5>
                        <h5 class=""> <strong>Total Amount : </strong> <span class="float-right"><?php echo moneyFormatIndia($overall_amount); ?></h5> -->
                    </div>
                </div>
                <br>
                <div class="row">                                    
                        <div class="col-12">
                            <div class="row ">
                                <label class="col-md-2 col-form-label" for="staff_id">  Loan / Advance </label>
                                <label class="col-md-4 label_value" for="staff_id"> <?php echo $loan_advance_val; ?> </label>
                                <label class="col-md-2 col-form-label" for="day_type"> Entry Date </label>
                                <label class="col-md-4 label_value" for="day_type"> <?php echo disdate($entry_date); ?> </label>
                            </div>
                            <div class="row ">
                                <input type="hidden" name="loan_type" id="loan_type" class="form-control" value="<?php echo $loan_advance; ?>">
                                <label class="col-md-2 col-form-label others_div loan_advance_div" for="on_duty_type">Type</label>
                                <label class="col-md-4 label_value others_div loan_advance_div" for="on_duty_type_options"> <?= ($others_type_label); ?> </label>
                            </div>
                            <div class="row">
                                <label class="col-md-2 col-form-label" for="on_duty_to_date"> Staff Name </label>
                                <label class="col-md-4 label_value" for="on_duty_to_date"><?php echo staff_name($staff_id)[0]['staff_name']; ?></label>
                                <label class="col-md-2 col-form-label " for="on_duty_leave_days"> Amount</label>
                                <label class="col-md-4 label_value " for="on_duty_leave_days"> <?=($amount);?></label>
                            </div>
                            <div class="row loan_div loan_advance_div">
                                <label class="col-md-2 col-form-label" for="on_duty_half_date"> EMI </label>
                                <label class="col-md-4 label_value loan_prop loan_advance_prop" for="on_duty_leave_days"> <?php echo ($emi);?></label>
                                <label class="col-md-2 col-form-label" for="onduty_half_day_type"> EMI Type /Amount </label>
                                <label class="col-md-4 label_value loan_prop loan_advance_prop" for="on_duty_leave_days"><?php echo $emi_type_label;?></label>
                                <label class="col-md-2 col-form-label" for="onduty_half_day_type"> EMI Amount </label>
                                <label class="col-md-4 label_value loan_prop loan_advance_prop" for="on_duty_leave_days"><?php echo $emi_amount;?></label>
                            </div>
                            <div class="row percentage_div">
                                <label class="col-md-2 col-form-label" for="from_date"> Percentage(%) </label>
                                <label class="col-md-4 label_value percentage_prop" for="on_duty_leave_days"><?php echo ($loan_percentage);?></label>
                            </div>
                            <div class="row">
                                <label class="col-md-2 col-form-label" for="from_date"> Description </label>
                                <label class="col-md-4 label_value percentage_prop" for="on_duty_leave_days"><?php echo ($description);?></label>
                            </div>
                           
                            <!-- HO approval details -->
                            <div class="row <?=$ho_approve_class;?>"><span class="col-md-4 text-danger"><strong>HO Approval Details :</strong></span></div>
                            <div class="row <?=$ho_approve_class;?>">
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Status </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hr_approval_status;?></label>
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Date</label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($hod_approve_date);?></label>
                            </div>
                            <div class="row <?=$ho_approve_class;?>">
                                <label class="col-md-2 col-form-label <?=$ceo_to_be_approved_class;?>" for="is_approved">CEO To be Approved</label>
                                <label class="col-md-4 label_value <?=$ceo_to_be_approved_class;?>" for="half_date"><?php echo $ceo_name;?></label>
                                <label for="reason" class="col-md-2 col-form-label">Approved By </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo staff_name($hod_approve_by)[0]['staff_name'];?></label>
                            </div>
                            <div class="row <?=$ho_approve_class;?>">
                                
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hod_reason;?></label>
                            </div>
                            <!-- CEO approval details -->
                            <div class="row <?=$ceo_approved_class;?>"><span class="col-md-4 text-danger"><strong>CEO Approval Details :</strong></span></div>
                            <div class="row <?=$ceo_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved"> Approved Status</label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo $ceo_approval_status;?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label">  Approved Date </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($ceo_approve_date);?></label>
                            </div>
                            <div class="row <?=$ceo_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved">  Approved By </label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo staff_name($ceo_approve_by)[0]['staff_name'];?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $ceo_reason;?></label>
                            </div>

                            <!-- Director approval details -->
                            <div class="row <?=$director_approved_class;?>"><span class="col-md-4 text-danger"><strong>Director Approval Details :</strong></span></div>
                            <div class="row <?=$director_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved"> Approved Status</label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo $director_approval_status;?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label">  Approved Date </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($director_approve_date);?></label>
                            </div>
                            <div class="row <?=$director_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved">  Approved By </label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo staff_name($director_approve_by)[0]['staff_name'];?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $director_reason;?></label>
                            </div>

                            <!-- HR approval details -->
                            <div class="row <?=$hr_approved_class;?>"><span class="col-md-4 text-danger"><strong>HR Approval Details :</strong></span></div>
                            <div class="row <?=$hr_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved"> Approved Status</label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo $hr_approval_status;?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label">  Approved Date </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($hr_approve_date);?></label>
                            </div>
                            <div class="row <?=$hr_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved">  Approved By </label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo staff_name($hr_approve_by)[0]['staff_name'];?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $hr_reason;?></label>
                            </div>


                            <!-- Accounts approval details -->
                            <div class="row <?=$accounts_approved_class;?>"><span class="col-md-4 text-danger"><strong>Accounts Approval Details :</strong></span></div>
                            <div class="row <?=$accounts_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved"> Approved Status</label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo $accounts_approval_status;?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label">  Approved Date </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo disdate($accounts_approve_date);?></label>
                            </div>
                            <div class="row <?=$accounts_approved_class;?>">
                                <label class="col-md-2 col-form-label " for="ceo_to_be_approved">  Approved By </label>
                                <label class="col-md-4 label_value  " for="half_date"><?php echo staff_name($accounts_approve_by)[0]['staff_name'];?></label>
                                <label for="rejected_reason" class="col-md-2 col-form-label"> Reason </label>
                                <label class="col-md-4 label_value" for="half_date"><?php echo $accounts_app_reason;?></label>
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