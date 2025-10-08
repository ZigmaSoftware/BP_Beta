<style>
    img.w-100.background_stamp.d-print-none.backgrounds {
        display: none;
    }

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
        font-size: 15px;
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
        width: 14%;
        margin-top: 41px;
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

    table.offer_table {
        width: 40%;
        margin: 0px auto;
    }

    table.offer_table tr th,
    td {
        padding: 5px;
        border: 1px solid #ccc;
    }

    td.hed {
        text-align: center;
        font-weight: 600;
        color: #000;
    }

    td.bold {
        font-weight: 600;
        color: #000;
    }

    td.right {
        text-align: end;
    }

    table.offer_table tr th {
        color: #000;
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
$designation        = "";
$location           = "";
$gender             = "";
// $staff_name             = $_SESSION["staff_name"];
$designation_name       = $_SESSION["designation_type"];
$staff_details = staff_name($_SESSION["staff_id"]);
// $department = $staff_details[0]['department'];
// $dept_details = department($department);
// $department_name = $dept_details[0]['department'];
// $location = $staff_details[0]['work_location'];
// $loc_details = work_location($location);
// $location_name = $loc_details[0]['work_location'];

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {
        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        $table      =  "employee_exit_interview_form";
        $columns    = [
            "reason",
            "likeMost",
            "improvement",
            "comeBack",
            "comments",
            "remarks",
            "employment_reason",
            "dissatisfaction_reason",
            "otherReasonText",
            "department",
            "designation",
            "location",
            "employee_name"
        ];
        $table_details   = [
            $table,
            $columns
        ];
        $result_values  = $pdo->select($table_details, $where);
        if ($result_values->status) {
            $result_values              = $result_values->data[0];
            $reason                     = $result_values["reason"];
            $likeMost                   = $result_values["likeMost"];
            $improvement                = $result_values["improvement"];
            $comeBack                   = $result_values["comeBack"];
            $comments                   = $result_values["comments"];
            $remarks                    = $result_values["remarks"];
            $employment_reason          = $result_values["employment_reason"];
            $dissatisfaction_reason     = $result_values["dissatisfaction_reason"];
            $otherReasonText            = $result_values["otherReasonText"];
            $designation                = $result_values["designation"];
            $employee_name                   = $result_values["employee_name"];

            $department                 = $result_values["department"];
            $dept                       = staff_name("",$employee_name)[0]['department'];
            $dept_name                  = department($dept)[0]['department'];

            $location                   = $result_values["location"];
            $loc_details                = staff_name("",$employee_name)[0]['work_location'];
            $location_name              = work_location($loc_details)[0]['work_location'];


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

?>
<?php

?>
<div class="row hole-page1">
    <div class="col-12">
        <div class="card-bg">
            <div class="">
                <!-- <?php if ($company_id == "comp64f5828e7ad3283886") { ?>
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
                <?php } else if ($company_id == "") { ?>
                    <div class="page-header" style="margin-bottom: 20px;">
                        <img src="http://localhost/xeon/img/logo.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                    </div>
                <?php } ?> -->
                <div class="page-header mt-2" style="margin-bottom: 0px;">
                        <img src="assets/images/blue_planet_logo.png" id="header_img" class="w-100 m-6 p-0" alt="Header Image" style="width: 180px!important;">
                    </div>
                
                <p class="bold-text" style="margin-bottom: 10px; text-align: center"><u class="p-indent" style="color: #000; font-size: 23px!important;">BLUE PLANET ENVIRONMENTAL SOLUTIONS INDIA PRIVATE 
LIMITED</u></p>
                <p class="bold-text" style="margin-bottom: 10px; text-align: center"><u class="p-indent" style="color: #000; font-size: 19px!important;">EMPLOYEE EXIT INTERVIEW FORM	</u></p>
                <p class="bold-text" style="margin-bottom: 10px; text-align: center; color: #000; font-size: 14px!important;">(Strictly confidential and should be sent to Head- HR Only)</p>
                <p class="bold-text"  style="font-size: 14px!important;">Employee Name : <strong class="p-without-indent"><?= $employee_name; ?></strong></p>
                <p class="bold-text"  style="font-size: 14px!important;">Designation   : <strong class="p-without-indent"><?= $designation; ?></strong></p>
                <p class="bold-text"  style="font-size: 14px!important;">Department    : <strong class="p-without-indent"><?= $dept_name; ?></strong></p>
                <p class="bold-text"  style="font-size: 14px!important;">Location      : <strong class="p-without-indent"><?=$location_name; ?></strong></p>
                <!-- end row -->
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <p class="p-indent">1.What is your principal reason for leaving the organization? <strong class="p-without-indent"><?= $reason; ?>  <?= $employment_reason; ?><?= $dissatisfaction_reason; ?><?= $otherReasonText; ?></strong></p>
                            <p class="p-indent">2.What did you like the most about your employment experience at Xeon?	 <strong class="p-without-indent"><?= $likeMost; ?></strong></p>
                            <p class="p-indent">3.What are the areas of improvement you would like to suggest?	 <strong class="p-without-indent"><?= $improvement; ?></strong></p>
                            <p class="p-indent">4.Would you be interested in coming back to Xeon for suitable employment in future? <strong class="p-without-indent"><?= $comeBack; ?></strong></p>
                            <p class="p-indent">5.Please give your comments? <strong class="p-without-indent"><?= $comments; ?></strong></p>
                            <p class="p-indent">6.Please feel free to give any other remarks. <strong class="p-without-indent"><?= $remarks; ?></strong></p>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <h4 class="text-secondry bold-text"> Employee Signature	</h4>
                    </div>
                    <br>
                    <div class="col-12">
                        <h4 class="text-secondry bold-text"> Date :	</h4>
                    </div>
                </div>
                <br>
<script>
    $(document).ready(function() {
        // print_header_and_footer()
        setTimeout(function() {
            // window.print();
        }, 1000);
    });
</script>