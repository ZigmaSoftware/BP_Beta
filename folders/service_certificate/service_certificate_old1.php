<!-------------fonts----------------->
<link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">


<style>
.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

.p-indent {
    font-size: 19px !important;
    margin-bottom: 15px;
    text-align: justify;
}
.bold-text {
    font-weight: bold;
    font-size: 18px;
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

h4.reff {
    font-size: 18px;
    font-family: 'Roboto', sans-serif;
    color: #000;
}
.head-bold-ref {
    font-size: 22px;
    font-weight: bold;
    color: #000;
    font-family: 'Roboto', sans-serif;
}
h4.reff span {
    line-height: 33px;
    font-family: 'Roboto', sans-serif;
}
h5.head-bold.text-secondry.conc {
    color: #000;
    font-size: 17px;
    font-family: 'Roboto', sans-serif;
    text-decoration: underline;
}
p.p-indent.para {
    font-family: 'Roboto', sans-serif;
    color: #444;
    line-height: 48px;
}
p.p-indent.para strong {
    font-size: 24px !important;
    color: #000;
}
.offset-md-2 {
    margin-top: 100px;
}
h4.float-right2.ref {
    margin-top: 25px;
}
h5.head-bold.text-secondry.conc {
    margin-top: 36px;
}
.p-indent {
    padding:0px 45px
    line-height: 33px;
}
.head-bold {
    font-size: 17px;
}
h5.head-bold.text-secondry.mb-1.conc {
    font-size: 20px;
}
</style>


<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$certificate_no     = "";
$certificate_date   = $today;
$certificate_type   = 1;
$staff_name         = "";
$designation        = "";
$department         = "";
$join_date          = "";
$relieve_date       = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "service_certificate";

        $columns    = [
            "certificate_no",
            "certificate_date",
            "certificate_type",
            "name",
            "designation",
            "department",
            "join_date",
            "relieve_date"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $certificate_no     = $result_values["certificate_no"];
            $certificate_date   = $result_values["certificate_date"];
            $certificate_type   = $result_values["certificate_type"];
            $staff_name         = $result_values["name"];
            $designation        = $result_values["designation"];
            $department         = $result_values["department"];
            $join_date          = disdate($result_values["join_date"]);
            $relieve_date       = $result_values["relieve_date"];

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


if ($certificate_type == 1) {
    $relieve_date   = "Till Now";
    $has_had        = "has";
} else {
    $relieve_date   = disdate($relieve_date);
    $has_had        = "had"; 
}

// Certificate Date Seperation
$certificate_day         = date('d', strtotime($certificate_date));
$certificate_position    = date('S', strtotime($certificate_date));
$certificate_month_year  = date('F Y', strtotime($certificate_date));

$staff_details                 = staff_name($staff_name);

$mr_ms                   = "";
$his_her                 = "";
$gender                  = "";

if (!empty($staff_name)) {
    // print_r($staff_name);
    $gender         = $staff_details[0]["gender"];
    $staff          = $staff_details[0]["staff_name"];
    // $married_status      = $staff_name[0]["martial_status"];

    if ($gender == 1) {
        $mr_ms                   = "Mr";
        $his_her                 = "His";
    } else {
        $mr_ms                   = "Ms";
        $his_her                 = "Her";
    }
}

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
                <!-- Logo & title -->

                <div class="page-header">
                    <img src="img/letter_back/1-header.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
                </div>

                <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">
                
                <div class="page-header-space"></div>
                
                <div class="row mb-5">
                    <div class="col-md-4">
                    </div><!-- end col -->
                    <div class="col-md-6 offset-md-2">
                        <div class="mt-3 float-right">
                           
                            <h4 class="float-right2 mt-n1"><span class="head-bold"><?=$certificate_day?><sup><?=$certificate_position;?></sup> <?=$certificate_month_year; ?> </span></h4>
                            <h4 class="float-right2 ref">  <span class="head-bold ">Ref: <?= $certificate_no; ?> </span> </h4>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row mt-5 mb-5">
                    <div class="col-12 text-center">
                        <u>
                            <h5 class="head-bold text-secondry mb-1 conc">
                                TO WHOM SO EVER IT MAY CONCERN
                            </h5>
                        </u>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">    
                        <div class="mt-3">
                            
                            <p class="p-indent ">This is to certify that <strong class="p-without-indent" style="color: #000;"><?=$mr_ms;?>  <?=$staff; ?></strong> <?=$has_had;?> been worked as <strong class="p-without-indent" style="color: #000;"><?=$designation?></strong> in the department of <strong class="p-without-indent" style="color: #000;"><?=$department; ?></strong> in our esteemed organization from <strong class="p-without-indent" style="color: #000;"><?=$join_date;?></strong> to <strong class="p-without-indent" style="color: #000;"><?=$relieve_date;?></strong>. <?=$his_her;?> conduct during the above period at our organization <?=$has_had;?> been good. </p>

                        </div>
                    </div>
                </div>

                <div class="row mt-5 mb-5">
                    <div class="col-12  mt-4 mb-4">
                    <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span> Ascent e-Digit solutions (P) Ltd</h4>
                    </div>
                    <!-- <div class="col-12 mt-5 mb-5">
                        <img src="img/letter_back/signs/5ff729124da1d67875.png">
                    </div> -->
                    <div class="col-12">
                        <h4 class="text-secondry bold-text">Manager-HR and Admin</h4>
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


                    <div class="text-right">
                        
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