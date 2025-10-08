<style>

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

.p-indent {
    /* text-indent: 30px; */
    font-size : 21px !important;
    text-align: justify;
    margin-left : 25px;
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
            "ctc",
            "gender"
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
            $gender             = $result_values["gender"];

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

$ctc_in_words = getIndianCurrency($ctc);
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

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="page-header">
                    <img src="img/letter_back/1-header.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                </div>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                
                <div class="page-header-space"></div>

                <div class="row mt-3">
                    <div class="col-md-5">
                        <h5 class="head-bold text-secondry mb-1"><?= $mr_ms; ?> <?= $staff_name; ?></h5>
                        <h5 class="p-without-indent"><?=nl2br($staff_address);?></h5>
                    </div><!-- end col -->
                    <div class="col-md-5 offset-md-2">
                        <div class="">
                            <h4 class="float-right"> Ref: <span class="head-bold"> <?= $letter_no; ?> </span> </h4>
                            <h4 class="float-right mt-n1"><span class="head-bold"><?=$letter_day?><sup><?=$letter_position;?></sup> <?=$letter_month_year; ?> </span></h4>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">                    
                        <div class="mt-3">

                            <h5 class="head-bold text-secondry mb-1">Dear <?= $staff_name; ?>,</h5>
                            <p class="bold-text mt-3"><u class="p-indent">Sub : Offer of Appointment as  <?=$designation;?> </u></p>

                            <p class="p-indent">Congratulations. We are pleased to extend to you an offer to join us as <strong class="p-without-indent"> <?=$designation;?> </strong> based at <strong class="p-without-indent"> <?=$location;?> </strong> on the terms & conditions mutually discussed and agreed. </p>

                            <p class="p-indent">Your appointment will be effective from your date of joining, which will be on or before <strong class="p-without-indent"> <?=$join_day?><sup><?=$join_position;?></sup> <?=$join_month_year; ?> </strong>from offer, failing with this appointment will stay automatically withdrawn.  </p>

                            <p class="p-indent">Your services are transferable to any other place or office of the company or to any subsidiary or associate company whether now existing or still to be formed. </p>

                            <p class="p-indent">Your roles and responsibilities will be as discussed and as may be decided by the management from time to time. </p>

                            <p class="p-indent">Your CTC will be <strong class="p-without-indent"> Rs. <?=$ctc;?> /- (<?=$ctc_in_words;?>) </strong> per month. You will be eligible for Statutory Benefits like PF, ESI etc. as per government norms. (Annexure I attached)</p>

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
                        <p class="text-secondry p-indent"> I, <strong class="p-without-indent"><?= $mr_ms; ?>. <?=$staff_name;?> </strong>, hereby accept your offer of Appointment as <strong class="p-without-indent"><?=$designation;?></strong> based at <strong class="p-without-indent"><?=$location;?></strong>. </p>
                        <p class="text-secondry  p-indent"> I confirm that I will join duty on or before ____________ </p>
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
                        <div class="form-group row ">
                            <label class="col-md-3 col-form-label" for="branch">Branch</label>
                            <div class="col-md-9">
                                <select name="branch" id="branch" onchange="letter_head_change(this.value)" class="select2 form-control" required>
                                <?=$branch_options;?>                         
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="text-right ">
                        
                        <?php echo btn_cancel($btn_cancel);?>

                        <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>

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