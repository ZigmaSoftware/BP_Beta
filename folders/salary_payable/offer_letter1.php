<style>

.our_stengths {
    font-size: 0.80rem !important;
}

* {
    font-size: 0.95rem;
}

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
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

<div class="card">
    <div class="card-body">

        <div class="page-header">
            <img src="img/letter_back/1-header.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
        </div>

        <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">

        <!-- Content Starts Here -->
        <table>
            <thead>
                <tr>
                    <td>
                        <!--place holder for the fixed-position header-->
                        <div class="page-header-space"></div>
                    </td>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td>
                        <div class="page">
                            <div class="row with_header_footer_top">
                                <div class="col-md-6">

                                    <h5 class="head-bold text-secondry mb-1">Mr/Miss <?= $staff_name; ?></h5>
                                    <h5 class="p-without-indent"><?=nl2br($staff_address);?></h5>

                                </div><!-- end col -->
                                <div class="col-md-4 offset-md-2">
                                    <div class="float-right">
                                        <h4 class="m-b-10">Ref<span class="head-bold float-right"> <?= $letter_no; ?> </span></h4>
                                        <!-- <h4 class="float-right"><span class="head-bold"><?=$letter_day?><sup><?=$letter_position;?></sup> <?=$letter_month_year; ?> </span></h4> -->
                                        <!-- <p class="m-b-10 text-secondary">Quotation Date  <span class="float-right"> &nbsp;&nbsp;&nbsp;&nbsp;<strong class='font-weight-bold'> <?=disdate($quotation_date);?> </strong> </span></p> -->
                                        <p class="m-b-10 text-secondary mt-n2">Ref &nbsp; <span class="float-right"><strong class='font-weight-bold'><?= $letter_no; ?></strong></span></p>
                                        <!-- <h4 class="float-right"> Ref: <span class="head-bold"> <?= $letter_no; ?> </span> </h4>
                                        <h4 class="float-right mt-n1"><span class="head-bold"><?=$letter_day?><sup><?=$letter_position;?></sup> <?=$letter_month_year; ?> </span></h4> -->
                       
                                    </div>
                                </div><!-- end col -->
                            </div>
                        </div>                        
                    </td>
                </tr>
            </tbody>

            <tfoot>
                <tr>
                    <td>
                        <!--place holder for the fixed-position footer-->
                        <div class="page-footer-space"></div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <!-- Content Ends Here -->

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

        <div class="page-footer">
            <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image">
        </div>
        <!-- <div id="content">
            <div id="pageFooter">Page </div>
        </div> -->
    </div>
</div>