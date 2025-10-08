<style>
table.salary-details tr td {
    padding: 7px;
    color: #000;
    font-size: 15px;
}
table.salary-details, th, td {
    border: 1px solid #979797;
    border-collapse: collapse;
}
.hole-page1 {
    background: #fff;
    padding: 20px;
}
h4.float-right2 {
    text-align: end;
    line-height: 15px;
}
p.bold-text.mt-3 {
    text-align: center;
}
strong.p-without-indent {
    color: #000000;
    font-weight: bold;
}
tr.align-center td {
    text-align: center;
}
.bg td {
    background: #f7f7f7;
    font-weight: 700;
    
}
tr.blod.bg-align td {
    font-weight: 600;
    text-align: center;
}
td.as {
    text-align: end;
}
.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

.p-indent {
    font-size: 17px !important;
    margin-bottom: 15px;
    text-align: justify;
}

.p-without-indent {
    /* text-indent: 50px; */
    font-size: 15px !important;
    line-height: 20px;

}

.head-bold {
    font-size : 16px;
    font-weight: bold;
}

.bold-text {
    font-weight: bold;
}

.page-header,
.page-header-space {
    height: 100px;
}

.page-header,
.page-header-space {
    height: 100px;
}

.page-footer,
.page-footer-space {
    height: 70px;
}

.page-footer {
    /* position: fixed; */
    bottom: 0;
    width: 100%;
    /* border-top: 1px solid black; */
    /* for demo */
    background: yellow;
    /* for demo */
}

.page-header {
    /* position: fixed; */
    top: 0;
    width: 100%;
    /* border-bottom: 1px solid black; */
    /* for demo */
    background: yellow;
    /* for demo */
}

.page {
    page-break-after: always;
}

.page_break_before {
    page-break-inside: avoid;
}

/* @page {
  @bottom-left {
    content: counter(page) ' of ' counter(pages);
  }
} */


#content {
    display: table;
}

#pageFooter {
    display: table-footer-group;
    right: 0; 
    bottom: 0;
}

/* #pageFooter:after {
    counter-increment: page;
    content: counter(page);
} */

#pageFooter:after {
    counter-increment: page;
    content:counter(page) " | " counter(pages);
    white-space: nowrap; 
    z-index: 1020;
    -moz-border-radius: 5px; 
    -moz-box-shadow: 0px 0px 4px #222;  
    background-image: -moz-linear-gradient(top, #eeeeee, #cccccc);  
}


@media print {
    .page-header {
        position: fixed;
    }
    .page-footer {
        position: fixed;
    }
    thead {
        display: table-header-group;
    }
    tfoot {
        display: table-footer-group;
    }
    button {
        display: none;
    }
    /* body {
        margin: 25px;
    } */

    .cancel_stamp {
        position: fixed;
    }
    
    .background_stamp {
        position: fixed;
    }

    .approve_waiting_stamp {
        position: fixed;
    }

    /* #table_footer {
        position: absolute; */
    /* bottom: 0; */
    /* } */

    tfoot .table_footer {
        display: none;
    }

    tfoot .table_footer:last-of-type {
        display: block;
    }
    .print_backgrounds {
        display: block;
    }
}

</style>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$letter_no          = "";
$letter_date        = $today;
$staff_name         = "";
$staff_address      = "";
$phone_no           = "";
$designation        = "";
$location           = "";
$join_date          = "";
$ctc                = "";
$gender             = "";
$pf_esi             = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "offer_letter";

        $columns    = [
            "letter_no",
            "letter_date",
            "name",
            "address",
            "designation",
            "location",
            "join_date",
            "gross_salary",
            "ctc",
            "gender",
            "department",
            "medical_insurance_premium",
            "performance_allowance",
            "income_tax",
            "professional_tax",
            "other_deduction",
            "net_salary",
            "tds_deduction_status",
            "performance_bonus_status",
            "pf_esi",
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];
            $letter_no                  = $result_values["letter_no"];
            $letter_date                = $result_values["letter_date"];
            $staff_name                 = $result_values["name"];
            $staff_address              = $result_values["address"];
            $designation                = $result_values["designation"];
            $location                   = $result_values["location"];
            $join_date                  = $result_values["join_date"];
            $ctc                        = $result_values["gross_salary"];
            $salary                     = $result_values["gross_salary"];
            $gender                     = $result_values["gender"];
            $department                 = $result_values["department"];
            $mip                        = $result_values["medical_insurance_premium"];
            $perf_allowance             = $result_values["performance_allowance"];
            $income_tax                 = $result_values["income_tax"];
            $professional_tax           = $result_values["professional_tax"];
            $other_deduction            = $result_values["other_deduction"];
            $net_salary                 = $result_values["net_salary"];
            $tds_deduction_status       = $result_values["tds_deduction_status"];
            $performance_bonus_status   = $result_values["performance_bonus_status"];
            $pf_esi                     = $result_values["pf_esi"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            print_r($result_values);
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$mr_ms                   = "";
$his_her                 = "";
// $gender                  = "";

if ($gender == 1) {
    $mr_ms           = "Mr";
    $his_her         = "His";
} else if ($gender == 2)  {
    $mr_ms           = "Ms";
    $his_her         = "Her";
}


// Letter Date Seperation

$letter_day         = date('d', strtotime($letter_date));
$letter_position    = date('S', strtotime($letter_date));
$letter_month_year  = date('F Y', strtotime($letter_date));

$join_day         = date('d', strtotime($join_date));
$join_position    = date('S', strtotime($join_date));
$join_month_year  = date('F Y', strtotime($join_date));

// $ctc_in_words = getIndianCurrency($ctc);
$ctc          = moneyFormatIndia($ctc);

$branch_options     = [
    [
        'value' => 1,
        'text'  => 'Chennai'
    ],
    [
        'value' => 2,
        'text'  => 'Erode'
    ],
    [
        'value' => 3,
        'text'  => 'Bangalore'
    ],
    [
        'value' => 4,
        'text'  => 'Hyderabad'
    ],
    [
        'value' => 5,
        'text'  => 'Vijayawada'
    ],
];

$branch_options       = select_option($branch_options,"Select Branch");
$company_name_option          = company_name_option();
$company_name_option          = select_option($company_name_option,"Select company",$staff_company_name);


?>

<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <div class="page-header" style="margin-bottom: 20px;">
                    <img src="img/letter_back/1-header.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                </div>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                
                <div class="page-header-space"></div>

                <div class="row mt-3" style="margin-bottom: 17px;">
                    <div class="col-md-5">
                        <h5 class="head-bold text-secondry mb-1"><?= $mr_ms; ?> <?= $staff_name; ?></h5>
                        <h5 class="p-without-indent"><?=nl2br($staff_address);?></h5>
                    </div><!-- end col -->
                    <div class="col-md-5 offset-md-2">
                        <div class="">
                            <h4 class="float-right2 mt-n1"><span class="head-bold"><?=$letter_day?><sup><?=$letter_position;?></sup> <?=$letter_month_year; ?> </span></h4>
                            <h4 class="float-right2">  <span class="head-bold">Ref: <?= $letter_no; ?> </span> </h4>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">                    
                        <div class="mt-3">

                            <h5 class="head-bold text-secondry mb-1">Dear <?= $mr_ms; ?> <?= $staff_name; ?>,</h5>
                            <p class="bold-text mt-3" style="margin-bottom: 46px; text-align: center"><u class="p-indent" style="color: #000;">Sub : Offer of Appointment as  <?=$designation;?> - <?=$location;?> </u></p>

                            <p class="p-indent">Congratulations. We are pleased to extend to you an offer to join us as  <strong class="p-without-indent"> <?=$designation;?> </strong> based at <strong class="p-without-indent"> <?=$location;?> </strong> on the terms & conditions mutually discussed and agreed. </p>

                            <p class="p-indent">Your appointment will be effective from your date of joining, which will be on or before <strong class="p-without-indent"> <?=$join_day?><sup><?=$join_position;?></sup> <?=$join_month_year; ?> </strong>from offer, failing with this appointment will stay automatically withdrawn.  </p>

                            <p class="p-indent">Your services are transferable to any other place or office of the company or to any subsidiary or associate company whether now existing or still to be formed. </p>

                            <p class="p-indent">Your roles and responsibilities will be as discussed and as may be decided by the management from time to time.  </p>

                            <p class="p-indent">Your Gross will be <strong class="p-without-indent"> Rs. <?=$ctc;?> /- </strong> per month. You will be eligible for Statutory Benefits like PF, ESI etc. as per government norms.(Annexure I attached)</p>

                            <p class="p-indent">Your Probation Period will be 3 Months and Notice Period will be 3 Months. </p>

                            <p class="p-indent">You will abide by the rules and regulations of the Company in letter and Spirit. </p>

                            <p class="p-indent">Hearty welcome to the Ascent family and look forward to a mutually beneficial and long-term relationship.  </p>

                            <p class="p-indent">We shall appreciate your confirmation of acceptance of the above offer. A detailed Appointment Letter will be issued to you at the time of joining. </p>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12  mt-4 mb-4" style="margin-bottom: 47px !important;">
                        <h5 class="text-secondry mb-2">Yours Sincerely,</h5>
                        <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> Ascent e-Digit solutions (P) Ltd</h4>
                    </div>
                    <!-- <div class="col-12">
                        <img src="img/letter_back/signs/5ff729124da1d67875.png">
                    </div> -->
                    <div class="col-12">
                        <h4 class="text-secondry bold-text">Manager-HR and Admin</h4>
                    </div>

                    <div class="col-12" style="margin-bottom: 400px;">
                        <h5 class="text-secondry" style="margin: 26px 0px 20px!important;"> <u class="head-bold">Acceptance</u> : </h5>
                        <p class="text-secondry p-indent"> I, <strong class="p-without-indent"><?= $mr_ms; ?>. <?=$staff_name;?> </strong>, hereby accept your offer of Appointment as <strong class="p-without-indent"><?=$designation;?></strong> based at <strong class="p-without-indent"><?=$location;?></strong>. </p>
                        <p class="text-secondry  p-indent"> I confirm that I will join duty on or before ____________ </p>
                    </div>
                </div>

               

                <div class="page-footer-space"></div>

                <div class="page-footer" >
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div>
            </div> <!-- end card-body-->        
        </div>
    </div> <!-- end col -->
</div>
<?php
    $annum_basic                    = "";
    $annum_hra                      = "";
    $annum_conveyance               = "";
    $annum_medical_allowance        = "";
    $annum_educational_allowance    = "";
    $sum_allowance                  = "";
    $other_allowance                = "";
    $annum_other_allowance          = "";
    $annum_pf                       = "";
    $annum_esi                      = "";
    $total_deduction                = "";
    $annum_total_deduction          = "";
    $net_salary                     = "";
    $annum_net_salary               = "";
    $annum_performance_allowance    = "";
    $ctc_cal                        = "";
    $ctc_sal                        = "";
    $annum_ctc                      = "";  
    $annum_net                      = "";  
    $annum_it                       = "";  
    $annum_pt                       = "";  
    $annum_od                       = "";  
    
    $total_emp_contribution         ="";
    $total_anum_emp_contribution    ="";
    $annum_bonus                    ="";
    $performance_based_annum_incentives ="";
    $performance_based_incentives ="";
    
    
    if ($perf_allowance == '') {
        $performance_allowance = 0;
        $annum_performance_allowance = 0;
    } else {
        $performance_allowance = $perf_allowance;
        $annum_performance_allowance = $perf_allowance * 12;
    }

    if ($mip == '' && $pf_esi==1) {
        $medical_insurance_premium = 0;
        $annum_medical_insurance_premium = 0;
    } else if($pf_esi==2) {
        $medical_insurance_premium = $mip;
        $annum_medical_insurance_premium = $mip * 12;
    }

    $conveyance_default_value       = 5000;
    $medical_default_value          = 8000;
    $educational_default_value      = 900;
    $pf_default_value               = 15000;
    $esi_default_value              = 21000;
    $medical_insurance              = 400;

    $performance_based_incentive   = 20000;
    $medical_insurance_premium      = 400;

        $per_annum = floatval($salary) * 12;
        $per_annum = moneyFormatIndia($per_annum);

        $basic = ((floatval($salary) * 40) / 100);
        $hra = ((floatval($basic) * 50) / 100);
        
        //conveyance calculation
        if ($salary >= $conveyance_default_value) {
            $conveyance = 1600;
        } else {
            $conveyance = 0;
        }
        //medical allowance
        if ($salary >= $medical_default_value) {
            $medical_allowance = 1250;
        } else {
            $medical_allowance = 0;
        }
        //Education allowance
        if ($salary >= $educational_default_value) {
            $educational_allowance = 200;
        } else {
            $educational_allowance = 0;
        }

       

    //pf
    // mythili
    if ($pf_esi==1 && $basic <= $pf_default_value) {
        $pf = round(($basic * 12) / 100);
    } 
    else if($pf_esi==2) {
        $pf = 0;
    }
    else if($pf_esi==3) {
        $pf = 0;
    }

    //esi
    if ($pf_esi==1 && $salary <= $esi_default_value) {
        $esi = round(($salary * 0.75) / 100);
    } else if($pf_esi==2) {
        $esi = 0;
    }

    //emp_esi
    if ($pf_esi==1 && $salary <= $esi_default_value) {
        $emp_esi = round(($salary * 3.25) / 100);
    } else if($pf_esi==2){
        $emp_esi = 0;
    }

// $bonus
if($pf_esi!=3 ){
    $bonus =$ctc/2/12*1000;
}
else if($pf_esi==3)
{
    $bonus =0;
}
    // print_r($bonus);
    // print_r($bonus);


// medical insurance
if ($pf_esi==1 ) {
    $medical_insurance_val = 0;
} else if($pf_esi==2) {
    $medical_insurance_val =$medical_insurance;
}
// $performance_based_incentives  
if($pf_esi==3){
$performance_based_incentives   = $performance_based_incentive;
}
else{
    $performance_based_incentives =0;
}

// performance_based_incentive 
if($pf_esi==3){
    $medical_insurance_premiums   = $medical_insurance_premium;
    }
    else{
        $medical_insurance_premiums =0;
    }

      // mythili
      $annum_bonus = $bonus*12;
      $medical_insurance_annum_val=$medical_insurance_val*12;
      $performance_based_annum_incentives=$performance_based_incentives*12;

      $medical_insurance_anum_premium =$medical_insurance_premiums*12;
      $total_emp_contribution= $pf + $emp_esi + $bonus + $medical_insurance_val+$performance_based_incentives+$medical_insurance_premiums;
      $annum_emp_esi = $emp_esi * 12;
      $total_annum_emp_contribution= $total_emp_contribution*12;
      $annum_basic = $basic * 12;
      $annum_hra = $hra * 12;
      $annum_conveyance = $conveyance * 12;
      $annum_medical_allowance = $medical_allowance * 12;
      $annum_educational_allowance = $educational_allowance * 12;
      $sum_allowance = $basic + $hra + $conveyance + $medical_allowance + $educational_allowance;
      $other_allowance = floatval($salary) - $sum_allowance;
      $annum_other_allowance = $other_allowance * 12;
      $annum_pf = $pf * 12;
      $annum_esi = $esi * 12;
     
      $total_deduction = $pf + $esi + $income_tax + $professional_tax + $other_deduction;
      $annum_total_deduction = $total_deduction * 12;
      $net_salary = floatval($salary) - $total_deduction;
      $annum_net_salary = $net_salary * 12;
      $annum_performance_allowance = $performance_allowance * 12;
      $ctc_cal = $total_deduction + $net_salary;
      $ctc_sal = $performance_allowance + $ctc_cal;
      $annum_ctc = $ctc_sal * 12;
      $annum_net = $net_salary * 12;
      $annum_it = $income_tax * 12;
      $annum_pt = $professional_tax * 12;
      $annum_od = $other_deduction * 12;

      $total_contribution = $pf + $emp_esi + $mip + $perf_allowance;

      $annum_total_contribution = $total_contribution * 12;

?>
<div class="row  hole-page1">
    <div class="col-md-12">
        <div class="page-header" style="margin-bottom: 100px;">
            <img src="img/letter_back/1-header.png" id="header_img_1" class="m-0 p-0 d-print-none backgrounds" width="10%" alt="Header Image">
        </div>
        <div class="row">
            <div class="col-md-2">
                
            </div>
            <div class="col-md-8">
                <table class="salary-details" width="100%">
                    <tr class="align-center bold bg">
                        <td colspan="3">Annexure I</td>
                    </tr>
                    <tr>
                        <td width="47%" >Candidate Name</td>
                        <td colspan="2" class="bold-text"><?= $mr_ms; ?> <?= $staff_name; ?></td>
                    </tr>
                    <tr>
                        <td >Designation</td>
                        <td colspan="2" class="bold-text"><?=$designation;?></td>
                    </tr>
                    <tr>
                        <td >Department</td>
                        <td colspan="2" class="bold-text"><?=$department;?></td>
                    </tr>
                    <tr>
                        <td >Work Location</td>
                        <td colspan="2" class="bold-text"><?=$location;?></td>
                    </tr>
                    <tr class="bg">
                        <td >Gross </td>
                        <td width="27%" class="as">Rs.<?=$ctc;?></td>
                        <td width="26%" class="as">Rs.<?=$per_annum;?></td>
                    </tr>
                    <?php  if($pf_esi==3) {?>
                    <tr class="bg">
                        <td >Performance Based Incentive </td>
                        <td width="27%" class="as">Rs.<?=$performance_based_incentives;?></td>
                        <td width="26%" class="as">Rs.<?=$performance_based_annum_incentives;?></td>
                    </tr>
                    <?php } ?>
                    <tr class="bg">
                        <td >Net Salary</td>
                        <td width="27%" class="as">Rs.<?=$net_salary;?></td>
                        <td width="26%" class="as">Rs.<?=$annum_net_salary;?></td>
                    </tr>
                    <tr class="bg">
                    <td > TOTAL CTC </td>           
                        <td width="27%" class="as">Rs.<?=moneyFormatIndia($salary + $total_emp_contribution);?></td>
                        <td width="26%" class="as">Rs.<?=moneyFormatIndia(($salary + $total_emp_contribution) * 12);?></td>
                    </tr>
                    <tr class="blod bg-align">
                        <td>Particulars </td>
                        <td>Per Month</td>
                        <td>Per Annum</td>
                    </tr>
                    <tr>
                        <td>Basic</td>
                        <td class="as">Rs.<?=moneyFormatIndia($basic);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_basic);?></td>
                    </tr>
                    <tr>
                        <td>HRA </td>
                        <td class="as">Rs.<?=moneyFormatIndia($hra);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_hra);?></td>
                    </tr>
                    <tr>
                        <td>Conveyance</td>
                        <td class="as">Rs.<?=moneyFormatIndia($conveyance);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_conveyance);?></td>
                    </tr>
                    <tr>
                        <td>Special Allowance</td>
                        <td class="as">Rs.<?=moneyFormatIndia($medical_allowance);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_medical_allowance);?></td>
                    </tr>

                    <tr>
                        <td>Education Allowance</td>
                        <td class="as">Rs.<?=moneyFormatIndia($educational_allowance);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_educational_allowance);?></td>
                    </tr>
                    <tr>
                        <td>Other Allowance</td>
                        <td class="as">Rs.<?=moneyFormatIndia($other_allowance);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_other_allowance);?></td>
                    </tr>
                    <tr class="bg">
                        <td >Gross </td>
                        <td class="as">Rs.<?=$ctc;?></td>
                        <td class="as">Rs.<?=$per_annum;?></td>
                    </tr>
                    <?php if($pf_esi!=2){?>
                    <tr>
                        <td>PF</td>
                        <td class="as">Rs.<?=moneyFormatIndia($pf);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_pf);?></td>
                    </tr>
                    <?php } ?>
                  <?php if($pf_esi==3) {?>
                    <tr>
                        <td>Performance Based Incentives</td>
                        <td class="as">Rs.<?=moneyFormatIndia($performance_based_incentives);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($performance_based_annum_incentives);?></td>
                    </tr>
                    <?php } ?>
                    <?php if($pf_esi==3) {?>
                    <tr>
                        <td>Medical Insurance Premium</td>
                        <td class="as">Rs.<?=moneyFormatIndia($medical_insurance_premiums);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($medical_insurance_anum_premium);?></td>
                    </tr>
                    <?php } ?>
                    <?php if($pf_esi==1) {?>

                    <tr>
                        <td>ESIC</td>
                        <td class="as">Rs.<?=moneyFormatIndia($emp_esi);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_emp_esi);?></td>
                    </tr>
                    <?php } ?>
                    <?php if($pf_esi!=3) {?>

                    <tr>
                        <td>Bonus</td>
                        <td class="as">Rs.<?=moneyFormatIndia($bonus);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_bonus);?></td>
                    </tr>
                    <?php } ?>
                    <?php if($pf_esi==2) {?>
                    <tr>
                        <td>Medical Insurance</td>
                        <td class="as">Rs.<?=moneyFormatIndia($medical_insurance_val);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($medical_insurance_annum_val);?></td>
                    </tr>
                    
                    <?php } ?>
                    <tr class="bg">
                        <td >Employer Contribution</td>
                        <td class="as">Rs.<?=moneyFormatIndia($total_emp_contribution);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($total_annum_emp_contribution);?></td>
                    </tr>
                    <tr class="bg">
                        <td > TOTAL CTC </td>
                        <td class="as">Rs.<?=moneyFormatIndia($salary + $total_emp_contribution);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia(($salary + $total_emp_contribution) * 12);?></td>
                    </tr>
                    <?php if($pf_esi!=2){?>

                    <tr>
                        <td>PF</td>
                        <td class="as">Rs.<?=moneyFormatIndia($pf);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_pf);?></td>
                    </tr>
                    <?php } ?>
                    <?php if($pf_esi==1) {?>

                    <tr>
                        <td>ESI</td>
                        <td class="as">Rs.<?=moneyFormatIndia($esi);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_esi);?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td>Other Deduction PT</td>
                        <td class="as">Rs.<?=moneyFormatIndia($other_deduction);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_od);?></td>
                    </tr>
                    
                    
                    
                    <tr class="bg">
                        <td>Total Deduction- Employee</td>
                        <td class="as">Rs.<?=moneyFormatIndia($total_deduction);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_total_deduction);?></td>
                    </tr>
                    <tr class="bg">
                        <td>Net Salary</td>
                        <td class="as">Rs.<?=moneyFormatIndia($net_salary);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia($annum_net_salary);?></td>
                    </tr>
                    <tr class="bg">
                        <td >TOTAL CTC </td>
                        <td class="as">Rs.<?=moneyFormatIndia($salary + $total_emp_contribution);?></td>
                        <td class="as">Rs.<?=moneyFormatIndia(($salary + $total_emp_contribution) * 12);?></td>
                    </tr>
                   
                    <?php if($tds_deduction_status == 0){?>
                    <tr class="bold bg">
                        <td colspan="3">* TDS will be deducted based on the yearly computation</td>
                    </tr>
                <?php } ?>
                    <?php if($performance_bonus_status == 0 && $pf_esi==3){?>
                    <tr class="bold bg">
                        <td colspan="3">* Performance Allowance will pay yearly once, baased on your performance</td>
                    </tr>
                <?php } ?>
                </table>
            </div>
            <div class="col-md-2">
                
            </div>
        </div>
        <div class="mt-4 mb-1 d-print-none">
            <div class="col-md-6">
                <div class="custom-control custom-checkbox mb-2">
                    <input type="checkbox" class="custom-control-input" id="print_header_footer" name="print_header_footer" onchange="print_header_and_footer()" >
                    <label class="custom-control-label header text-danger" for="print_header_footer" >With Header & Footer</label>
                </div>
            </div>
            <div class="col-md-6">
            <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="company">Company Name</label>
                    <div class="col-md-9">
                        <select name="company_name" id="company_name"  onchange="letter_head_change(this.value)" class="select2 form-control" required> <?=$company_name_option;?>                         
                        </select>
                    </div>
                </div>
                <div class="form-group row ">
                    <label class="col-md-3 col-form-label" for="branch">Branch</label>
                    <div class="col-md-9">
                        <select name="branch" id="branch" onchange="letter_head_change(this.value)" class="select2 form-control" required> <?=$branch_options;?>                         
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right ">
                <?php echo btn_cancel($btn_cancel);?>
                <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
            </div>
        </div>
        <div class="page-footer">
            <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
        </div>
    </div>
</div>
