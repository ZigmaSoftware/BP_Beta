<style>
    table.salary-details tr td {
        padding: 7px;
        color: #000;
        font-size: 15px;
    }

    table.salary-details,
    th,
    td {
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

    .h1,
    .h2,
    .h3,
    .h4,
    .h5,
    .h6,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
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
        font-size: 16px;
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
        /* background: yellow; */
        /* for demo */
    }

    .page-header {
        /* position: fixed; */
        top: 0;
        width: 100%;
        /* border-bottom: 1px solid black; */
        /* for demo */
        /* background: yellow; */
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
        content: counter(page) " | " counter(pages);
        white-space: nowrap;
        z-index: 1020;
        -moz-border-radius: 5px;
        -moz-box-shadow: 0px 0px 4px #222;
        background-image: -moz-linear-gradient(top, #eeeeee, #cccccc);
    }

    hr {
        width: 86%;
        margin-left: 1px;
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
    
    .page-break {
        page-break-before: always;
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

if (isset($_GET["unique_id"])) {
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
            "(SELECT branch_name FROM company_and_branch_creation AS company_and_branch_creation WHERE company_and_branch_creation.unique_id = " . $table . ".company_name ) AS company_name",
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
            "company_name as company_id"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

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
            $company_name                     = $result_values["company_name"];

              $company_id = $result_values['company_id'];
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
} else if ($gender == 2) {
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

$branch_options       = select_option($branch_options, "Select Branch");
// $company_name_option          = company_name_option();
// $company_name_option          = select_option($company_name_option, "Select company", $staff_company_name);

$company_name_option          = company_name_option1();
$company_name_option          = select_option($company_name_option, "Select company", $staff_company_name);



?>
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

$total_emp_contribution         = "";
$total_anum_emp_contribution    = "";
$annum_bonus                    = "";
$performance_based_annum_incentives = "";
$performance_based_incentives = "";


if ($perf_allowance == '') {
    $performance_allowance = 0;
    $annum_performance_allowance = 0;
} else {
    $performance_allowance = $perf_allowance;
    $annum_performance_allowance = $perf_allowance * 12;
}

if ($mip == '' && $pf_esi == 1) {
    $medical_insurance_premium = 0;
    $annum_medical_insurance_premium = 0;
} else if ($pf_esi == 2) {
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
if ($pf_esi == 1 && $basic <= $pf_default_value) {
    $pf = round(($basic * 12) / 100);
} else if ($pf_esi == 2) {
    $pf = 0;
} else if ($pf_esi == 3) {
    $pf = 0;
}

//esi
if ($pf_esi == 1 && $salary <= $esi_default_value) {
    $esi = round(($salary * 0.75) / 100);
} else if ($pf_esi == 2) {
    $esi = 0;
}

//emp_esi
if ($pf_esi == 1 && $salary <= $esi_default_value) {
    $emp_esi = round(($salary * 3.25) / 100);
} else if ($pf_esi == 2) {
    $emp_esi = 0;
}

// $bonus
if ($pf_esi != 3) {
    // $bonus =$ctc/2/12*1000;
    $bonus = $salary / 12;
} else if ($pf_esi == 3) {
    $bonus = 0;
}
// print_r($bonus);
// print_r($bonus);


// medical insurance
if ($pf_esi == 1) {
    $medical_insurance_val = 0;
} else if ($pf_esi == 2) {
    $medical_insurance_val = $medical_insurance;
}
// $performance_based_incentives  
if ($pf_esi == 3) {
    $performance_based_incentives   = $performance_based_incentive;
} else {
    $performance_based_incentives = 0;
}

// performance_based_incentive 
if ($pf_esi == 3) {
    $medical_insurance_premiums   = $medical_insurance_premium;
} else {
    $medical_insurance_premiums = 0;
}

// mythili
$annum_bonus = $bonus * 12;
$medical_insurance_annum_val = $medical_insurance_val * 12;
$performance_based_annum_incentives = $performance_based_incentives * 12;

$medical_insurance_anum_premium = $medical_insurance_premiums * 12;
$total_emp_contribution = $pf + $emp_esi + $bonus + $medical_insurance_val + $performance_based_incentives + $medical_insurance_premiums;
$annum_emp_esi = $emp_esi * 12;
$total_annum_emp_contribution = $total_emp_contribution * 12;
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
<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-1.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "Erode Service Office") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff33f28b4478803") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-2.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff278765fc93189") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php }else if ($company_id == ""){ ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/2-header.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }?>
                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">

                <div class="page-header-space"></div>

                <p class="bold-text mt-3" style="margin-bottom: 46px; text-align: center"><u class="p-indent" style="color: #000;">OFFER OF APPOINTMENT LETTER</u></p>
                <div class="row mt-3" style="margin-bottom: 17px;">
                    <div class="col-md-5">
                        <h5 class="head-bold text-secondry mb-1">Dear <?= $mr_ms; ?> <?= $staff_name; ?></h5>
                        <h5 class="p-without-indent"><?= nl2br($staff_address); ?></h5>
                    </div><!-- end col -->
                    <div class="col-md-5 offset-md-2">
                        <div class="">
                            <h4 class="float-right2 mt-n1"><span class="head-bold">Offer Release Date : <?= $letter_day ?><sup><?= $letter_position; ?></sup> <?= $letter_month_year; ?> </span></h4>

                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">

                            <h5 class="head-bold text-secondry mb-1">Dear <?= $mr_ms; ?> <?= $staff_name; ?>,</h5>
                            <p class="p-indent">Congratulations! With reference to your application and subsequent discussions you with us, we are pleased to inform you that you have been selected for employment with as <strong class="p-without-indent"> <?= $company_name; ?> <?= $designation; ?>. </strong> </p>
                            <p class="p-indent">We take this opportunity to thank and appreciate your decision to join with us. Your current working location will be at<strong class="p-without-indent"> <?= $company_name; ?>, No 119 Greenways Towers 1st floor, St Mary’s Road, Abiramapuram, Chennai - 600018. And you are requested to report on June 12, 2023 at 9:30 AM at the respective location.</strong></p>
                            <p class="p-indent">This is a system generated (offer and appointment letter) document. You are Agreeing to the terms and conditions of our employment and is to signing of physical contract form on a mutual agreement basis between you and the company. It has legal binding as per the law if mutual trust is breached.<strong class="p-without-indent">You are requested to accept the offer within 02 days and mail the confirmation of acceptance, failing which the offer will stand null and void.</strong> </p>
                            <p class="p-indent">Welcome Onboard! We look forward to a mutually fruitful association. </p>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12  mt-1 mb-1" style="margin-bottom: 10px !important;">
                        <h5 class="text-secondry mb-2">Yours Sincerely,</h5>
                        <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> <?= $company_name ?></h4>
                    </div>
                    <div class="col-3">
                        <img src="img/letter_back/sign-3.png" style="width:60%;">
                    </div>
                    <div class="col-12">
                        <h4 class="text-secondry bold-text">Authorized Signatory/ Manager – HR & Admin</h4>
                    </div>
                    <div class="col-12" style="margin-bottom: 400px;">
                        <h5 class="text-secondry" style="margin: 26px 0px 20px!important;"> <u class="head-bold">Acceptance</u> : </h5>

                    </div>
                </div>
                <div class="page-footer-space"></div>
                <div class="page-footer">
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div>
<div class="page-break"></div>
<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-1.jpg" id="header_img1" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "Erode Service Office") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img1" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff33f28b4478803") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-2.jpg" id="header_img1" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff278765fc93189") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img1" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }else if ($company_id == ""){ ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/2-header.png" id="header_img1" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }?>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                <div class="page-header-space"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="">
                            <p class="bold-text mt-3" style="margin-bottom: 46px; text-align: center"><u class="p-indent" style="color: #000;">GENERAL TERMS AND CONDITIONS OF EMPLOYMENT</u></p>
                            <h3><strong class="p-without-indent">Commencement of Employment:</strong></h3>
                            <p class="p-indent">Your presentwork place will be <?= $company_name; ?> Chennai. However, during the course of the employment, you shall be liable to be posted / transferred at any associate / affiliate / sister concern to serve any of the establishments of our organization.</p>
                            <p class="p-indent">Your employment with the Company will commence from the date of your joining the Company subject to fulfilment of the other conditions as mentioned in this employment contract.</p>
                            <p class="p-indent">The office working hours commences from 9.30 A.M to 7.00 P.M Monday to Saturday. You will be required to work in excess of your normal working hours when the need arises in the course of your work.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">Remuneration& Benefits:</strong></h3>
                            <h3><strong class="p-without-indent">PAY:</strong></h3>
                            <p class="p-indent">You will be entitled to a <strong class="p-without-indent">CTC Salary of Rs.<?= $per_annum; ?>/- per annum (Only). And the salary breakup will be enclosed herewith as Annexure I.</strong></p>
                            <h3><strong class="p-without-indent">Provident Fund:</strong></h3>
                            <p class="p-indent">You will be participating in the Organization’s Provided Fund Scheme. Our organization will contribute monthly an equivalent of 12% of your basic salary to the fund. This is inclusive of statutory remittance by the organization towards employee pension scheme maintained with the EPFO, wherever is applicable.</p>
                            <h3><strong class="p-without-indent">ESIC& Insurance: </strong></h3>
                            <p class="p-indent"><strong class="p-without-indent">Insurance Coverage: </strong>Those who not covered under ESIC, the Company will be provided Group Medical Life Insurance PolicyCoverage. <strong class="p-without-indent">Additionally, if you are come within the purview of the Employee State Insurance Act 1948, you will be eligible for coverage as per the said Act. Your contribution and along with company contribution will be remitted, as per the said Act.</strong></p>
                            <h3><strong class="p-without-indent">Leave: </strong></h3>
                            <p class="p-indent">You are entitled for 12 days as Casual Leave during your probation as per the company policy, in addition you will be entitled to a maximum of 12 working day sick leave. Earned Leave after completion of one year of services Earned Leave will be added as the statutory act. </p>
                        </div>
                    </div>
                </div>
                <?php if ($pf != 0) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">Provident Fund:</strong></h3>
                            <p class="p-indent">You will be participating in the Organization’s Provided Fund Scheme. Our organization will contribute monthly an equivalent of 12% of your basic salary to the fund. This is inclusive of statutory remittance by the organization towards employee pension scheme maintained with the EPFO, wherever is applicable.</p>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <?php if ($esi != 0) { ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">ESIC& Insurance: </strong></h3>
                            <h3><strong class="p-without-indent">Insurance Coverage: </strong></h3>
                            <p class="p-indent">Those who not covered under ESIC, the Company will be provided Group Medical Life Insurance PolicyCoverage.<strong class="p-without-indent">. Additionally, if you are come within the purview of the Employee State Insurance Act 1948, you will be eligible for coverage as per the said Act. Your contribution and along with company contribution will be remitted, as per the said Act.</strong> </p>
                        </div>
                    </div>
                </div>
                <?php } ?>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">SERVICE RULES: </strong></h3>
                            <h3><strong class="p-without-indent">Probation: </strong></h3>
                            <p class="p-indent">Period You shall be on probation for a period of 3 months from the date of your appointment and unless notified in writing, Performance review will be held at the end of your probations and based on the review, the probation period (initial or extended or Confirmed) the Management finds your performance to be unsatisfactory or that you lack the aptitude for the job or that you are not suitable for the job, or the like, your probationary employment would be liable to be terminated, at any time, and without any liability.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">Employment General Terms & Condition’s: </strong></h3>
                            <p class="p-indent">You will be bound by all rules, regulations, policies and orders promulgated by the company from time to time in relation to conduct, discipline, punctuality, leave, medical and retirement and other matters which form part of these terms of employment. However, some of the terms of immediate relevance are specifically mentioned herein for your benefit.</p>
                            <p class="p-indent">During the tenure of the assignment with the company, you will not engage yourself in any other assignments or gainful employment without consent of the management.</p>
                            <p class="p-indent">You are required to maintain the highest order of secrecy with regards to the work or confidential information of the Company and / or its subsidiaries or Associated Companies and in case of any breach of trust, your appointment may be terminated by the Company without any notice.</p>
                            <p class="p-indent">If at any time in our opinion, which is final in this matter you are found non- performer or guilty of fraud, dishonest, disobedience, disorderly behaviour, negligence, indiscipline, absence from duty without permission or any other conduct considered by us deterrent to our interest or of violation of one or more terms of this letter, your services may be terminated without notice and on account of reason of any of the acts or omission the company shall be entitled to recover the damages from you.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">Retirement:</strong></h3>
                            <p class="p-indent"> You will retire from service on attaining superannuation at the age of 60 years.</p>
                        </div>
                    </div>
                </div>
                <div class="page-footer">
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div>
<div class="page-break"></div>
<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-1.jpg" id="header_img2" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "Erode Service Office") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img2" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff33f28b4478803") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-2.jpg" id="header_img2" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff278765fc93189") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img2" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }else if ($company_id == ""){ ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/2-header.png" id="header_img2" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }?>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                <div class="page-header-space"></div>

                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">Notice for Separation / Termination of Employment: </strong> </h3>
                            <h3><strong class="p-without-indent">INCIDENCE OF ABSENCE: </strong> </h3>
                            <p class="p-indent">Your continuous absence for 15 days or more without any communication to the management by itself will be proof of your voluntary abandonment of services and accordingly, your name will be removed from the muster roll.</p>
                            <p class="p-indent">If you wish to relieve from the service of the company you will have to give a notice period of 90 days for both Confirmed & Probationer.</p>
                            <p class="p-indent">If not either side will have to pay three month / 90 days as the case may be of the salary before leaving the company. </p>
                            <p class="p-indent">Full and Final settlement, experience certificate and relieving letter will only be forwarded to the employee on accepting the resignation by the management.</p>
                            <p class="p-indent">However, management reserves the right to exempt your notice of resignation and relieve you with immediate effect in which case no notice or pay is entitled by you. </p>
                            <p class="p-indent">Your employment by this company will be conditional based on the correct information provided to us in the course of your application. Any false information would be regarded as a breach of the terms, which may lead to termination of employment.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12  mt-4 mb-4" style="margin-bottom: 47px !important;">
                        <h5 class="text-secondry mb-2">Yours Sincerely,</h5>
                        <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> <?= $company_name ?></h4>
                    </div>
                    <div class="col-12">
                        <h4 class="text-secondry bold-text">Authorized Signatory/ Manager – HR & Admin</h4>
                    </div>
                    <div class="col-3">
                        <img src="img/letter_back/sign-3.png" style="width:60%;">
                    </div>
                    <div class="col-12">
                        <h5 class="text-secondry" style="margin: 26px 0px 20px!important;"> Acceptance : </h5>
                        <p class="text-secondry p-indent"> I, <strong class="p-without-indent"><u class="head-bold"><?= $mr_ms; ?>. <?= $staff_name; ?> </strong></u>, hereby accept your offer of Appointment as <strong class="p-without-indent"><u class="head-bold"><?= $designation; ?></strong></u> based at <u class="head-bold"><strong class="p-without-indent"><?= $location; ?></strong></u>. </p>
                        <p class="text-secondry  p-indent"> I confirm that I will join duty on or before ____________ </p>
                    </div>
                </div>

                <div class="page-footer-space"></div>
                <div class="page-footer">
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div> 
<div class="page-break"></div>
<div class="row hole-page1">
    <div class="col-md-12">
        <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
            <div class="page-header" style="margin-bottom: 20px;">
                <img src="img/letter_back/headernew-1.jpg" id="header_img3" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
            </div>
        <?php } ?>
        <?php if ($company_id == "Erode Service Office") { ?>
            <div class="page-header" style="margin-bottom: 20px;">
                <img src="img/letter_back/headernew-5.jpg" id="header_img3" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
            </div>
        <?php } ?>
        <?php if ($company_id == "comp64fff33f28b4478803") { ?>
            <div class="page-header" style="margin-bottom: 20px;">
                <img src="img/letter_back/headernew-2.jpg" id="header_img3" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
            </div>
        <?php } ?>
        <?php if ($company_id == "comp64fff278765fc93189") { ?>
            <div class="page-header" style="margin-bottom: 20px;">
                <img src="img/letter_back/headernew-5.jpg" id="header_img3" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
            </div>
            <?php }else if ($company_id == ""){ ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/2-header.png" id="header_img3" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }?>
        <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
        <div class="page-header-space"></div>
        <div class="row">

            <div class="col-md-8">
                <table class="salary-details" width="100%">
                    <tr class="align-center bold bg">
                        <td colspan="3">Annexure I</td>
                    </tr>
                    <tr>
                        <td width="47%">Candidate Name</td>
                        <td colspan="2" class="bold-text"><?= $mr_ms; ?> <?= $staff_name; ?></td>
                    </tr>
                    <tr>
                        <td>Designation</td>
                        <td colspan="2" class="bold-text"><?= $designation; ?></td>
                    </tr>
                    <tr>
                        <td>Department</td>
                        <td colspan="2" class="bold-text"><?= $department; ?></td>
                    </tr>
                    <tr>
                        <td>Work Location</td>
                        <td colspan="2" class="bold-text"><?= $location; ?></td>
                    </tr>
                    <tr class="bg">
                        <td>Gross </td>
                        <td width="27%" class="as">Rs.<?= $ctc; ?></td>
                        <td width="26%" class="as">Rs.<?= $per_annum; ?></td>
                    </tr>
                    <?php if ($pf_esi == 3) { ?>
                        <tr class="bg">
                            <td>Performance Based Incentive </td>
                            <td width="27%" class="as">Rs.<?= $performance_based_incentives; ?></td>
                            <td width="26%" class="as">Rs.<?= $performance_based_annum_incentives; ?></td>
                        </tr>
                    <?php } ?>
                    <tr class="bg">
                        <td>Net Salary</td>
                        <td width="27%" class="as">Rs.<?= $net_salary; ?></td>
                        <td width="26%" class="as">Rs.<?= $annum_net_salary; ?></td>
                    </tr>
                    <tr class="bg">
                        <td> TOTAL CTC </td>
                        <td width="27%" class="as">Rs.<?= moneyFormatIndia($salary + $total_emp_contribution); ?></td>
                        <td width="26%" class="as">Rs.<?= moneyFormatIndia(($salary + $total_emp_contribution) * 12); ?></td>
                    </tr>
                    <tr class="blod bg-align">
                        <td>Particulars </td>
                        <td>Per Month</td>
                        <td>Per Annum</td>
                    </tr>
                    <tr>
                        <td>Basic</td>
                        <td class="as">Rs.<?= moneyFormatIndia($basic); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_basic); ?></td>
                    </tr>
                    <tr>
                        <td>HRA </td>
                        <td class="as">Rs.<?= moneyFormatIndia($hra); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_hra); ?></td>
                    </tr>
                    <tr>
                        <td>Conveyance</td>
                        <td class="as">Rs.<?= moneyFormatIndia($conveyance); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_conveyance); ?></td>
                    </tr>
                    <tr>
                        <td>Special Allowance</td>
                        <td class="as">Rs.<?= moneyFormatIndia($medical_allowance); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_medical_allowance); ?></td>
                    </tr>

                    <tr>
                        <td>Education Allowance</td>
                        <td class="as">Rs.<?= moneyFormatIndia($educational_allowance); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_educational_allowance); ?></td>
                    </tr>
                    <tr>
                        <td>Other Allowance</td>
                        <td class="as">Rs.<?= moneyFormatIndia($other_allowance); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_other_allowance); ?></td>
                    </tr>
                    <tr class="bg">
                        <td>Gross </td>
                        <td class="as">Rs.<?= $ctc; ?></td>
                        <td class="as">Rs.<?= $per_annum; ?></td>
                    </tr>
                    <?php if ($pf_esi != 2  && $pf!=0) { ?>
                        <tr>
                            <td>PF</td>
                            <td class="as">Rs.<?= moneyFormatIndia($pf); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($annum_pf); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($pf_esi == 3) { ?>
                        <tr>
                            <td>Performance Based Incentives</td>
                            <td class="as">Rs.<?= moneyFormatIndia($performance_based_incentives); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($performance_based_annum_incentives); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($pf_esi == 3) { ?>
                        <tr>
                            <td>Medical Insurance Premium</td>
                            <td class="as">Rs.<?= moneyFormatIndia($medical_insurance_premiums); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($medical_insurance_anum_premium); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($pf_esi == 1) { ?>

                        <tr>
                            <td>ESIC</td>
                            <td class="as">Rs.<?= moneyFormatIndia($emp_esi); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($annum_emp_esi); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($pf_esi != 3) { ?>

                        <tr>
                            <td>Bonus</td>
                            <td class="as">Rs.<?= moneyFormatIndia($bonus); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($annum_bonus); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($pf_esi == 2) { ?>
                        <tr>
                            <td>Medical Insurance</td>
                            <td class="as">Rs.<?= moneyFormatIndia($medical_insurance_val); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($medical_insurance_annum_val); ?></td>
                        </tr>

                    <?php } ?>
                    <tr class="bg">
                        <td>Employer Contribution</td>
                        <td class="as">Rs.<?= moneyFormatIndia($total_emp_contribution); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($total_annum_emp_contribution); ?></td>
                    </tr>
                    <tr class="bg">
                        <td> TOTAL CTC </td>
                        <td class="as">Rs.<?= moneyFormatIndia($salary + $total_emp_contribution); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia(($salary + $total_emp_contribution) * 12); ?></td>
                    </tr>
                    <?php if ($pf_esi != 2  && $pf!=0) { ?>

                        <tr>
                            <td>PF</td>
                            <td class="as">Rs.<?= moneyFormatIndia($pf); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($annum_pf); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($pf_esi == 1) { ?>

                        <tr>
                            <td>ESI</td>
                            <td class="as">Rs.<?= moneyFormatIndia($esi); ?></td>
                            <td class="as">Rs.<?= moneyFormatIndia($annum_esi); ?></td>
                        </tr>
                    <?php } ?>
                    <?php if ($professional_tax != 0) { ?>
                    <tr>
                        <td>Other Deduction PT</td>
                        <td class="as">Rs.<?= moneyFormatIndia($professional_tax); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_pt); ?></td>
                    </tr>
                    <?php } ?>


                    <tr class="bg">
                        <td>Total Deduction- Employee</td>
                        <td class="as">Rs.<?= moneyFormatIndia($total_deduction); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_total_deduction); ?></td>
                    </tr>
                    <tr class="bg">
                        <td>Net Salary</td>
                        <td class="as">Rs.<?= moneyFormatIndia($net_salary); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia($annum_net_salary); ?></td>
                    </tr>
                    <tr class="bg">
                        <td>TOTAL CTC </td>
                        <td class="as">Rs.<?= moneyFormatIndia($salary + $total_emp_contribution); ?></td>
                        <td class="as">Rs.<?= moneyFormatIndia(($salary + $total_emp_contribution) * 12); ?></td>
                    </tr>

                    <?php if ($tds_deduction_status == 0) { ?>
                        <tr class="bold bg">
                            <td colspan="3">* TDS will be deducted based on the yearly computation</td>
                        </tr>
                    <?php } ?>
                    <?php if ($performance_bonus_status == 0 && $pf_esi == 3) { ?>
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
                    <input type="checkbox" class="custom-control-input" id="print_header_footer" name="print_header_footer" onchange="print_header_and_footer()">
                    <label class="custom-control-label header text-danger" for="print_header_footer">With Header & Footer</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group row">
                    <label class="col-md-3 col-form-label" for="company">Company Name</label>
                    <div class="col-md-9">
                        <select name="company_name" id="company_name" onchange="letter_head_change(this.value)" class="select2 form-control" required> <?= $company_name_option; ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row ">
                    <label class="col-md-3 col-form-label" for="branch">Branch</label>
                    <div class="col-md-9">
                        <select name="branch" id="branch" onchange="letter_head_change(company_name.value,this.value)" class="select2 form-control" required> <?= $branch_options; ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="text-right ">
                <?php echo btn_cancel($btn_cancel); ?>
                <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
            </div>
        </div>
        <!-- <div class="page-footer-space"></div> -->
        <div class="page-footer">
            <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
        </div>
    </div>
</div>
<div class="page-break"></div>
<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-1.jpg" id="header_img4" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "Erode Service Office") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img4" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff33f28b4478803") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-2.jpg" id="header_img4" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff278765fc93189") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img4" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }else if ($company_id == ""){ ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/2-header.png" id="header_img4" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }?>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                <div class="page-header-space"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h3><strong class="p-without-indent">On-Boarding Process: </strong> </h3>

                            <p class="p-indent">The requisite forms are available as a part of the employee joining kit. The filled forms are required to be handed over to the Manpower representative along with below mentioned documents at the time of joining. Submission of all joining documents is mandatory.</p>
                            <hr>
                            <p class="p-indent">Duly accepted and signed Appointment Letter / Offer Letter</p>

                            <hr>
                            <p class="p-indent">Duly filled joining kit
                            </p>
                            <hr>
                            <p class="p-indent">Resume
                            </p>
                            <hr>
                            <p class="p-indent">ID Proof
                            </p>
                            <hr>
                            <p class="p-indent">Address Proof
                            </p>
                            <hr>
                            <p class="p-indent">Educational Certificates - Highest Qualification
                            </p>
                            <hr>
                            <p class="p-indent">Employment documents - Appointment letter/ last increment letter with salary annexure
                            </p>
                            <hr>
                            <p class="p-indent">Address Proof
                            </p>
                            <hr>
                            <p class="p-indent">Salary Slip - last 3 months

                            </p>
                            <hr>
                            <p class="p-indent">Form 16

                            </p>
                            <hr>
                            <p class="p-indent">Bank Statement - last 3 months

                            </p>
                            <hr>
                            <p class="p-indent">PAN Card Copy

                            </p>
                            <hr>
                            <p class="p-indent">Aadhar Card
                            </p>
                            <hr>

                            <p class="p-indent">UAN

                            </p>
                            <hr>
                            <p class="p-indent">4 Passport size photographs

                            </p>
                            <hr>
                        </div>
                    </div>
                </div>

                <div class="page-footer">
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div>
<div class="page-break"></div>
<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
            
                <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-1.jpg" id="header_img5" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "Erode Service Office") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img5" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff33f28b4478803") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-2.jpg" id="header_img5" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_id == "comp64fff278765fc93189") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img5" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }else if ($company_id == ""){ ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/2-header.png" id="header_img5" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                    <?php }?>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                <div class="page-header-space"></div>
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            </p>
                            <hr>
                            <p class="p-indent">ESIC Temporary / Smart Card in original (In case already have)

                            </p>
                            <hr>
                            <p class="p-indent">Cancelled Cheque Leaf in case of an existing account

                            </p>
                            <hr>
                            <p class="p-indent">Duly Filled Investment Declaration Form

                            </p>
                            <hr>
                            <p class="p-indent">Resignation Letter from the previous organization and acceptance copy

                            </p>
                            <hr>
                            <p class="p-indent">Candidate information Form

                            </p>
                            <hr>
                            <p class="p-indent">Non Disclosure Agreement

                            </p>
                            <hr>
                            <p class="p-indent">PF Declaration - Form 11

                            </p>
                            <hr>
                            <p class="p-indent">PF Nomination - Form 2

                            </p>

                            <hr>
                            <p class="p-indent">ESIC Declaration Form
                            </p>
                            <hr>
                           
                            <p class="p-indent">Declaration of Previous Employer Income - Form 12 B
                            </p>
                            <hr>
                        </div>
                    </div>
                </div>

                <div class="page-footer">
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div>

<script>
    $(document).ready(function() {
        // print_header_and_footer()
        setTimeout(function() {
            window.print();
        }, 1000);
    });
</script>