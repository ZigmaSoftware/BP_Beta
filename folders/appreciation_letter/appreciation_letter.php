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
        top: 0;
        width: 100%;
        /* background: yellow; */
    }

    .page {
        page-break-after: always;
    }

    .page_break_before {
        page-break-inside: avoid;
    }

    #content {
        display: table;
    }

    #pageFooter {
        display: table-footer-group;
        right: 0;
        bottom: 0;
    }

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

        .cancel_stamp {
            position: fixed;
        }

        .background_stamp {
            position: fixed;
        }

        .approve_waiting_stamp {
            position: fixed;
        }

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
        margin-left: 3.0em;
        /* Adjust the margin as needed */
    }

    .colon-span-emp {
        display: inline-block;
        width: 1em;
        /* You can adjust this width as needed */
        text-align: center;
        margin-left: 4.6em;
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
$gender             = "";

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "appreciation_letter";

        $columns        = [
            "@a:=@a+1 s_no",
            "staff_name",
            "date_of_appreciation",
            "unique_id",
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];


            $staff_names                = get_staff_name($result_values["staff_name"]);
            $staff_name                 = $staff_names[0]['staff_name'];

            $date_of_appreciation        = $result_values["date_of_appreciation"];


            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
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

$gender = gender($result_values["staff_name"]);
$get_gender = $gender[0]['gender'];

if ($get_gender == 1) {
    $mr_ms           = "Mr";
    $his_her         = "His";
} else if ($get_gender == 2) {
    $mr_ms           = "Ms";
    $his_her         = "Her";
}

// Letter Date Seperation

$today_date = date('d.m.Y');

$letter_day         = date('d', strtotime($date_of_appreciation));
$letter_position    = date('S', strtotime($date_of_appreciation));
$letter_month_year  = date('F Y', strtotime($date_of_appreciation));

// $accept_letter_day         = date('d', strtotime($accept_resig_date));
// $accept_letter_position    = date('S', strtotime($accept_resig_date));
// $accept_letter_month_year  = date('F Y', strtotime($accept_resig_date));

// $join_day         = date('d', strtotime($join_date));
// $join_position    = date('S', strtotime($join_date));
// $join_month_year  = date('F Y', strtotime($join_date));

// $branch_options     = [
//     [
//         'value' => 1,
//         'text'  => 'Chennai'
//     ],
//     [
//         'value' => 2,
//         'text'  => 'Erode'
//     ],
//     [
//         'value' => 3,
//         'text'  => 'Bangalore'
//     ],
//     [
//         'value' => 4,
//         'text'  => 'Hyderabad'
//     ],
//     [
//         'value' => 5,
//         'text'  => 'Vijayawada'
//     ],
// ];

// $branch_options       = select_option($branch_options, "Select Branch");
// $company_name_option          = company_name_option();
// $company_name_option          = select_option($company_name_option, "Select company", $staff_company_name);


?>

<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <!-- <?php if ($company_name == "ASCENT E DIGIT SOLUTIONS PRIVATE LIMITED") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-1.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_name == "Erode Service Office") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_name == "Infinite Inland Famers pvt ltd") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-2.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_name == "ASCENT URBAN RECYCLERS PRIVATE LIMITED") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-5.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?>
                <?php if ($company_name == "") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="img/letter_back/header/headernew-1.jpg" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?> -->
                <div class="page-header mt-2" style="margin-bottom: 0px;">
                        <img src="assets/images/blue_planet_logo.png" id="header_img" class="w-100 m-6 p-0" alt="Header Image" style="width: 180px!important;">
                    </div>
                <!--<img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">-->
                <div class="page-header-space"></div>
                <div class="row mt-1" style="margin-bottom: 17px;">
                    
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">

                            <h5 class="head-bold text-secondry mb-1">Dear <?= $mr_ms; ?> <?= $staff_name; ?>,</h5>
                            <p class="bold-text mt-3" style="margin-bottom: 46px; text-align: center"><u class="p-indent" style="color: #000;">APPRECIATION LETTER</u></p>
                            <p class="p-indent">We are writing to express our sincere appreciation for your hard work and dedication to Xeon Waste Managers. </p></br>
                            <p class="p-indent">Your contributions have been invaluable to our team, and we are grateful for all that you do.</p></br>
                            <p class="p-indent">Specifically, we want to recognize your outstanding work on <?= $letter_day ?><sup><?= $letter_position; ?></sup> <?= $letter_month_year; ?>.</p>
                            <p class="p-indent">Your leadership and attention to detail have been instrumental in achieving company goals so we want to publicly acknowledge your efforts.</p>
                            <p class="p-indent">We encourage you to keep up the excellent work, and we will continue to support your growth and development within the company.</p>
                            <p class="p-indent">Once again, thank you for all that you do. You are a valued member of our team, and we appreciate your hard work and dedication.</p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12  mt-2 mb-2" style="margin-bottom: 10px !important;">
                        <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> Xeon Waste Managers Private Ltd.</h4>
                    </div>
                     <br>
                    <div class="mt-5 col-12">
                        <h4 class="text-secondry bold-text">Authorized Signatory</h4>
                    </div>
                </div>
            </div> <!-- end card-body-->
        </div>
    </div> <!-- end col -->
</div>

<script>
    $(document).ready(function() {
        setTimeout(function() {
            // window.print();
        }, 1000);
    });
</script>