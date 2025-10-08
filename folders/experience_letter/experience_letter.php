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
        background: yellow;
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
 
if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "experience_letter";

        $columns    = [
            "letter_no",
            "designation",
            "name",
            "to_date",
            "join_date",
            "company_name"
            

        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values              = $result_values->data[0];
            $letter_no                  = $result_values["letter_no"];
            
            $staff_name1                 = get_staff_name($result_values["name"]);
            $staff_name                  =disname($staff_name1[0]["staff_name"]);

            $company_name         = $result_values["company_name"];
            $designation         = $result_values["designation"];
            $staff_names          = get_staff_name($result_values["name"]);
            $emp_code             = $staff_names[0]["employee_id"];

            $emp_code1              = $_SESSION['user_name'];
            
            $to_dates                   = $result_values["to_date"];
            $join_dates                  = $result_values["join_date"];
            

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
$date = strtotime($join_dates);
$join_date = date('F j , Y  ', $date);
$todate = strtotime($to_dates);
$to_date = date('F j , Y  ', $todate);
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

$letter_day         = date('d', strtotime($to_date));
$letter_position    = date('S', strtotime($to_date));
$letter_month_year  = date('F Y', strtotime($to_date));




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
$company_name_option          = company_name();
$company_name_option          = select_option($company_name_option, "Select company", $staff_company_name);


?>

<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <?php if ($company_name == "comp64f5828e7ad3283886") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-1.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "Erode Service Office") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "comp64fff33f28b4478803") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-2.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "comp64fff278765fc93189") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } else if (($company_name == "")||($company_name == 'Xeon')) { ?>
                    <div class="page-header mt-2" style="margin-bottom: 0px;">
                        <img src="assets/images/blue_planet_logo.png" id="header_img" class="w-100 m-6 p-0" alt="Header Image" style="width: 180px!important;">
                    </div>
                <?php } ?>

                <!-- <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp"> -->

                <div class="page-header-space"></div>

                <div class="row mt-3" style="margin-bottom: 17px;">
                    <div class="col-md-5">
                        <!-- <h5 class="head-bold text-secondry mb-1"><?= $mr_ms; ?> <?= $staff_name; ?></h5> -->
                        <!-- <h5 class="p-without-indent"><?= nl2br($staff_address); ?></h5> -->
                    </div><!-- end col -->
                    <!-- <div class="col-md-5 offset-md-2">
                        <div class="">
                            <h4 class="float-right2 mt-n1"><span class="head-bold"><?= $letter_day ?><sup><?= $letter_position; ?></sup> <?= $letter_month_year; ?> </span></h4>
                            <h4 class="float-right2">  <span class="head-bold">Ref: <?= $letter_no; ?> </span> </h4>
                        </div>
                    </div> -->
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">

                            <!-- <h5 class="head-bold text-secondry mb-1">Dear <?= $mr_ms; ?> <?= $staff_name; ?>,</h5> -->
                            <p class="bold-text mt-3" style="margin-bottom: 46px; text-align: center"><u class="p-indent" style="color: #000;">EXPERIENCE LETTER </u></p>

                            <p class="p-indent">This is to certify that <strong class="p-without-indent"> <?= $staff_name; ?> </strong> , Employee code <strong class="p-without-indent"> <?= $emp_code; ?> </strong> was an employee of <strong class="p-without-indent"> <?= $company_name; ?> from <?= $join_date; ?> till <?= $to_date; ?>. </strong> As per our records, his last designation at the time of offboarding from the company was <strong class="p-without-indent"> <?= $designation; ?> </strong> . His conduct during the above the period at our organization had been good.</p>
                            <p class="p-indent">We wish <strong class="p-without-indent"><?= $staff_name; ?></strong> all the best in his future endeavours. </p>



                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12  mt-2 mb-2" style="margin-bottom: 10px !important;">
                        <h5 class="text-secondry mb-2">Yours Sincerely, </h5>
                        <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> <?= $company_name ?></h4>
                    </div>
                    <!-- <div class="col-12">
                        <img src="img/letter_back/signs/5ff729124da1d67875.png">
                    </div> -->
                    <!-- <div class="col-3">
                        <img src="img/letter_back/sign-3.png" style="width:60%;">
                    </div> -->
                    <div class="col-12">
                        <h4 class="text-secondry bold-text">Authorized Signatory/ Manager – HR & Admin</h4>
                    </div>


                </div>

                <!-- <div class="mt-4 mb-1 d-print-none">

                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="print_header_footer" name="print_header_footer" onchange="print_header_and_footer()">
                            <label class="custom-control-label header text-danger" for="print_header_footer">With Header & Footer</label>
                        </div>
                    </div>
                    <div class="text-right">

                        <?php echo btn_cancel($btn_cancel); ?>

                        <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>

                    </div>
                </div> -->
                <!-- <div class="mt-4 mb-1 d-print-none">
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
                    </div> -->
                    <!-- <div class="text-right ">
                        <?php echo btn_cancel($btn_cancel); ?>
                        <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                    </div>
                </div> -->
                <!-- <div class="page-footer-space"></div> -->

                <!-- <div class="page-footer">
                    <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
                </div> -->
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div>

<script>
    $(document).ready(function() {
        // print_header_and_footer()
        setTimeout(function() {
            // window.print();
        }, 1000);
    });
</script>