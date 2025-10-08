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
        margin-top: 12px;
        border: 1px solid black;
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

    .colon-span {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 0.5em;
        /* Adjust the margin as needed */
    }

    .colon-span-branch {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 6em;
        /* Adjust the margin as needed */
    }

    .colon-span-designation {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 3.6em;
        /* Adjust the margin as needed */
    }

    .colon-span-doj {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 2.0em;
        /* Adjust the margin as needed */
    }

    .colon-span-emp-no {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 3.3em;
        /* Adjust the margin as needed */
    }

    .colon-span-emp-name {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 1.6em;
        /* Adjust the margin as needed */
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

        $table      =  "confirmation_letter";

        $columns        = [
            "@a:=@a+1 s_no",

            // "(SELECT staff_name FROM staff as staff  WHERE staff.unique_id = ".$table.".name ) AS name",
            "name",
             "(SELECT company_name FROM staff as company_name  WHERE company_name.unique_id = ".$table.".company_name ) AS company_name",
            // "company_name",
            // "(SELECT branch_name FROM company_and_branch_creation AS company_and_branch_creation WHERE company_and_branch_creation.unique_id = " . $table . ".company_name ) AS company_name",
            "emp_no",
            "designation",
            "branch",
            "join_date",
            "gross_salary",
            "revised_salary",
            "unique_id",
            "entry_date",
            "confirmation_letter_no"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];


            $staff_names                 = get_staff_name($result_values["name"]);
            $staff_name = $staff_names[0]['staff_name'];

            $staff_company_name         = $result_values["company_name"];

            // $to_date                   = $result_values["to_date"];
            $join_date                  = $result_values["join_date"];

            $company_name   = $result_values["company_name"];

            $revised_salary = $result_values["revised_salary"];

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

// $letter_day         = date('d', strtotime($result_values['entry_date']));
// $letter_position    = date('S', strtotime($result_values['entry_date']));
// $letter_month_year  = date('F Y', strtotime($result_values['entry_date']));
$letter_day         = date('d', strtotime(date('Y-m-d')));
$letter_position    = date('S', strtotime(date('Y-m-d')));
$letter_month_year  = date('F Y', strtotime(date('Y-m-d')));

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
                <?php if ($company_name == "ASCENT E DIGIT SOLUTIONS PRIVATE LIMITED") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-1.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "Erode Service Office") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "Infinite Inland Famers pvt ltd") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-2.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "ASCENT URBAN RECYCLERS PRIVATE LIMITED") { ?>
                    <!-- <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div> -->
                <?php } ?>
                <?php if ($company_name == "") { ?>
                    <div class="page-header mt-2" style="margin-bottom: 0px;">
                        <img src="assets/images/blue_planet_logo.png" id="header_img" class="w-100 m-6 p-0" alt="Header Image" style="width: 180px!important;">
                    </div>
                <?php } ?>

                <!-- <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp"> -->

                <div class="page-header-space"></div>
                <div class="row">
                    <div class="col-6 ">


                        <h4 class="float-right2" style="text-align: initial;"> <span class="head-bold">Ref: <?= $result_values['confirmation_letter_no']; ?> </span> </h4>

                    </div>
                    <div class="col-6 ">
                        <h4 class="float-right2 mt-n1"><span class="head-bold"><?= $letter_day ?><sup><?= $letter_position; ?></sup> <?= $letter_month_year; ?> </span></h4>

                    </div>

                </div>

                <div class="row mt-3" style="margin-bottom: 17px;">
                    <div class="col-md-5">

                        <h5 class="text-secondry mb-1"><b>Employee Name<span class="colon-span-emp-name"> :</span></b><?= $mr_ms; ?><?= disname($staff_name); ?></h5>
                        <h5 class="text-secondry mb-1"><b>Employee No<span class="colon-span-emp-no"> :</span></b><?= $result_values["emp_no"]; ?></h5>
                        <h5 class="text-secondry mb-1"><b>Designation <span class="colon-span-designation"> :</span></b><?= $result_values["designation"]; ?></h5>
                        <h5 class="text-secondry mb-1"><b>Branch <span class="colon-span-branch"> :</span></b><?= $result_values["branch"]; ?></h5>
                        <h5 class="text-secondry mb-1"><b>Date Of Joining <span class="colon-span-doj"> :</span></b><?= date("d-m-Y", strtotime($result_values["join_date"]));  ?></h5></br>
                    </div>
                    <!-- end col -->
                    <div class="col-md-5 offset-md-2">
                        <!-- <div class="">
                        <h4 class="float-right2 mt-n1"><span class="head-bold"><?= $letter_day ?><sup><?= $letter_position; ?></sup> <?= $letter_month_year; ?> </span></h4>
                            
                        </div> -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">

                            <h5 class="head-bold text-secondry mb-1">Dear <?= $mr_ms; ?> <?= disname($staff_name); ?>,</h5>
                            <p class="bold-text mt-3" style="margin-bottom: 46px; text-align: center"><u class="p-indent" style="color: #000;">Sub: Confirmation Order – Reg. </u></p>

                            <p class="p-indent">Further to the performance review we had with you, the Management of <strong class="p-without-indent"> <?= $company_name; ?> </strong> , is pleased to inform you that you are confirmed in your current role as <strong class="p-without-indent"> <?= $result_values['designation']; ?> - <?= $result_values['branch']; ?> </strong> with your
                                <?php if ($revised_salary != 0 && $revised_salary != "") { ?>
                                    Gross Salary has been revised from <strong class="p-without-indent">Rs.<?= $result_values['gross_salary']; ?>/- to Rs<?= $result_values['revised_salary']; ?>/- </strong>
                                    <?php } else { ?>
                                        existing Salary
                                    <?php } ?> with effect from <?= date("d-m-Y", strtotime($result_values["join_date"]));  ?>.</p>

                            <p class="p-indent">All other terms and conditions of services as mentioned in your Appointment letter will be continue to exit and you will be governed by the service rule of Xeon / other rules and regulations / instructions form the Management of Xeon from time to time.</p>

                            <p class="p-indent">As a token of acceptance of this confirmation letter, you may please sign and date the duplicate copy of this letter.</p>



                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12  mt-4 mb-4" style="margin-bottom: 47px !important;">

                        <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> <?= $company_name; ?></h4>
                    </div>
                     <!-- <div class="col-12">
                        <img src="img/letter_back/signs/5ff729124da1d67875.png">
                    </div>
                     <div class="col-12">
                        <h4 class="text-secondry bold-text">Authorized Signatory/ Manager – HR & Admin</h4>
                    </div> -->


                </div>

                <!-- <div class="row">
                    <div class="col-3">
                        <img src="img/letter_back/sign-3.png" style="width:60%;">
                    </div> -->
                    <div class="col-12  mt-4 mb-4">

                        <h4 class="text-secondry bold-text"> </h4>
                        <h4 class="text-secondry bold-text">(Human Resource)</h4>
                        <br>
                        <hr>
                        <!-- <p class="p-indent">I hereby confirm acceptance of the above terms and conditions of <?= $company_name; ?></p> -->
                    </div>

                    <!-- <div class="col-12">
                        <img src="img/letter_back/signs/5ff729124da1d67875.png">
                    </div> -->
                    <!-- <div class="col-12">
                        <h4 class="text-secondry bold-text"> Signature</h4>
                    </div>


                </div>
                <div class="row">
                    <div class="col-6">
                        <h4 class="text-secondry bold-text"> Name </h4>
                    </div>
                    <div class="col-6">
                        <h4 class="text-secondry bold-text" style="text-align: end;"> Date </h4>
                    </div>
                </div> -->

                <!-- <div class="mt-4 mb-1 d-print-none">

                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="print_header_footer" name="print_header_footer" onchange="print_header_and_footer()">
                            <label class="custom-control-label header text-danger" for="print_header_footer">With Header & Footer</label>
                        </div>
                    </div>
                    <div class="text-right ">

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
            </div> -->
            <!-- <div class="col-md-6">
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
        </div>
                <div class="page-footer-space"></div> -->

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
    // function print_header_and_footer() {

    // 	alert("hii");

    // 	var user_id = $("#user_id").val();
    // 	var branch_seal = $("#branch").val();

    // 	if (user_id) {
    // 		var sign_png 	= "img/letter_back/signs/"+user_id+".png";
    // 		$("#user_sign").attr("src",sign_png);
    // 	}

    // 	if (!branch_seal) {
    // 		branch_seal  = 1;
    // 	}

    // 	var branch_seal = "img/letter_back/"+branch_seal+"-seal.png";

    // 	$("#branch_seal").attr("src",branch_seal);
    //     var check = $("#print_header_footer").prop("checked");
    // 	if(check = true) {
    //         alert("demo");
    //         alert($("#print_header_footer").prop("checked"));
    //         // alert("demo");
    //         // window.print();
    // 		// $(".with_header_footer").addClass("d-print-none");
    // 		// $(".with_header_footer_top").css("margin-top","100px");

    //         $(".backgrounds").addClass("d-print-none");
    // 		$(".print_backgrounds").addClass("d-none");
    //         window.print();
    // 		// $(".backgrounds").removeClass("d-print-none");
    // 		// $(".print_backgrounds").removeClass("d-none");
    // 	} else if (check = false) {
    //         alert("hello");
    //         alert($("#print_header_footer").prop("checked"));
    //         window.print();

    // 		// $(".backgrounds").addClass("d-print-none");
    // 		// $(".print_backgrounds").addClass("d-none");
    //         $(".backgrounds").removeClass("d-print-none");
    // 		$(".print_backgrounds").removeClass("d-none");

    // 		// $(".with_header_footer").removeClass("d-print-none");
    // 		// $(".with_header_footer_top").removeAttr("style");
    // 	}



    // }
</script>