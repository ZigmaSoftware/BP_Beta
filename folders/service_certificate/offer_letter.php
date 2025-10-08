<style>

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

.p-indent {
    text-indent: 50px;
    font-size : 21px !important;
    text-align: justify;
}

.p-without-indent {
    /* text-indent: 50px; */
    font-size : 21px !important;
    text-align: justify;
}

.head-bold {
    font-size : 25px;
    font-weight: bold;
}

.bold-text {
    font-weight: bold;
}

.page-header,
.page-header-space {
	height: 100px;
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
            "ctc"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $letter_no          = $result_values["letter_no"];
            $letter_date        = $result_values["letter_date"];
            $staff_name         = $result_values["name"];
            $staff_address      = $result_values["address"];
            $designation        = $result_values["designation"];
            $location           = $result_values["location"];
            $join_date          = $result_values["join_date"];
            $ctc                = $result_values["ctc"];

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

// Letter Date Seperation

$letter_day         = date('d', strtotime($letter_date));
$letter_position    = date('S', strtotime($letter_date));
$letter_month_year  = date('F Y', strtotime($letter_date));

$join_day         = date('d', strtotime($join_date));
$join_position    = date('S', strtotime($join_date));
$join_month_year  = date('F Y', strtotime($join_date));

$ctc_in_words = getIndianCurrency($ctc);
$ctc          = moneyFormatIndia($ctc);
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body ml-4 mr-4">
                <!-- Logo & title -->

                <div class="row">
                    <div class="col-12">
                        <div class="page-header-space"></div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-md-5">
                        <h5 class="head-bold text-secondry mb-1">Mr/Miss <?= $staff_name; ?></h5>
                        <h5 class="p-without-indent"><?=nl2br($staff_address);?></h5>
                    </div><!-- end col -->
                    <div class="col-md-5 offset-md-2">
                        <div class=" float-right">
                            <h4 class=""> Ref: <span class="head-bold"> <?= $letter_no; ?> </span> </h4>
                            <h4 class="float-right"><span class="head-bold"><?=$letter_day?><sup><?=$letter_position;?></sup> <?=$letter_month_year; ?> </span></h4>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">                    
                        <div class="mt-3">

                            <h5 class="head-bold text-secondry mb-1">Dear <?= $staff_name; ?>,</h5>
                            <p class="bold-text mt-3"><u class="p-indent">Sub : Offer of Appointment as <?=$designation;?></u></p>

                            <p class="p-indent">Congratulations. We are pleased to extend to you an offer to join us as <?=$designation;?> based at <?=$location;?> on the terms & conditions mutually discussed and agreed. </p>

                            <p class="p-indent">Your appointment will be effective from your date of joining, which will be on or before <?=$join_day?><sup><?=$join_position;?></sup> <?=$join_month_year; ?> from offer, failing with this appointment will stay automatically withdrawn.  </p>

                            <p class="p-indent">Your services are transferable to any other place or office of the company or to any subsidiary or associate company whether now existing or still to be formed. </p>

                            <p class="p-indent">Your roles and responsibilities will be as discussed and as may be decided by the management from time to time. </p>

                            <p class="p-indent">Your CTC will be Rs. <?=$ctc;?> /- (<?=$ctc_in_words;?>) per month. You will be eligible for Statutory Benefits like PF, ESI etc. as per government norms. (Annexure I attached)</p>

                            <p class="p-indent">Your Probation Period will be 3 Months and Notice Period will be 2 Months. </p>

                            <p class="p-indent">You will abide by the rules and regulations of the Company in letter and Spirit.</p>

                            <p class="p-indent">Hearty welcome to the Ascent family and look forward to a mutually beneficial and long-term relationship. </p>

                            <p class="p-indent">We shall appreciate your confirmation of acceptance of the above offer. A detailed Appointment Letter will be issued to you at the time of joining. </p>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12  mt-4 mb-4">
                        <h5 class="text-secondry mb-2 head-bold">Yours Sincerely,</h5>
                        <h4 class="text-secondry bold-text">For Ascent e-Digit solutions (P) Ltd</h4>
                    </div>
                    <!-- <div class="col-12 mb-n2">
                        <h5 class="font-weight-bold text-secondry"> <span class="text-danger"> D.SUGAN / Business Development Manager</span> </h5>
                        <!-- <h5 class="font-weight-bold text-secondry float-right"> <span class="text-danger"> +91 9952555305 / <u>sugan@aedindia.com</u> </span> </h5> --
                    </div> -->
                    <div class="col-12">
                        <!-- <h5 class="font-weight-bold text-secondry float-right"> <span class="text-danger"> D.SUGAN / Business Development Manager</span> </h5> -->
                        <!-- <h5 class="font-weight-bold text-secondry "> Manager-HR and Admin </h5> -->
                        <h4 class="text-secondry bold-text">Manager-HR and Admin</h4>

                    </div>

                    <div class="col-12">
                        <h5 class="text-secondry"> <u class="head-bold">Acceptance</u> : </h5>
                        <p class="text-secondry p-indent"> I, Mr. <?=$staff_name;?>, hereby accept your offer of Appointment as <?=$designation;?> based at <?=$location;?>. </p>
                        <p class="text-secondry  p-indent"> I confirm that I will join duty on or before ____________ </p>
                    </div>
                </div>
<!-- 
                <div class="row">
                    <div class="col-12">
                    <h4 class="header text-center">Vijaywada . Bangalore . Erode . Coimbatore . Madurai . Trichy . Trinelveli . Salem . Secunderabad </h4>
                    </div>
                </div> -->

                <div class="mt-4 mb-1">
                    <div class="text-right d-print-none">
                        <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                        <a href="#" class="btn btn-info waves-effect waves-light">Submit</a>
                    </div>
                </div>
            </div> <!-- end card-body-->        
        </div>
    </div> <!-- end col -->
</div>