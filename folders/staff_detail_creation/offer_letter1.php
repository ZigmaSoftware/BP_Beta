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

include 'function.php';

// Form Needed Variables
$btn_text               = "Save"; 

$unique_id              = ""; // It is Unique Id

$customer_id            = "";
$customer_name          = "";
$customer_city          = "";
$customer_state         = "";

$quotation_no           = "";
$quotation_print        = 1;
$quotation_date         = "";
$quotation_to           = "";
$subject                = "";
$call_no                = "";
$call_date              = "";
$call_unique_id         = "";
$follow_up_unique_id    = "";
$follow_up_date         = "";
$customer_id            = "";
$delivery_period        = "";
$guarantee_period       = "";
$warranty_period        = "";
$freight_value          = "";
$packing_forwarding     = "";
$payment_terms          = "";
$is_cancel              = "";
$is_approve             = "";

$readonly               = "";
$disabled               = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $product_groups = quotation_product_groups($unique_id);

        $table      = "quotation";
        $table_sub  = "quotation_product_details";

        $columns    = [
            "quotation_no",   
            "quotation_date", 
            "quotation_to",   
            "subject",        
            "call_no",        
            "call_date", 
            "customer_id",    
            "delivery_period",
            "guarantee_period",
            "warranty_period",
            "freight_value",  
            "packing_forwarding",
            "payment_terms",
            "is_cancel",
            "is_approve",
            "sess_user_id"
        ];

        $table_details = [
            $table,
            $columns
        ];

        $select_result  = $pdo->select($table_details,$where);

        if (!($select_result->status)) {

            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";

            print_r($select_result);            
            exit;

        } else {

            $select_result          =   $select_result->data[0];

            $quotation_no           =   $select_result["quotation_no"];   
            $quotation_date         =   $select_result["quotation_date"]; 
            $quotation_to           =   $select_result["quotation_to"];
            $subject                =   $select_result["subject"];      
            $call_no                =   $select_result["call_no"];        
            $call_date              =   $select_result["call_date"];
            $customer_id            =   $select_result["customer_id"];    
            $delivery_period        =   $select_result["delivery_period"];
            $guarantee_period       =   $select_result["guarantee_period"];
            $warranty_period        =   $select_result["warranty_period"];
            $freight_value          =   $select_result["freight_value"];  
            $packing_forwarding     =   $select_result["packing_forwarding"];
            $payment_terms          =   $select_result["payment_terms"];
            $is_cancel              =   $select_result["is_cancel"];
            $is_approve             =   $select_result["is_approve"];

            // Customer Details 
            $customer_details       = customers($customer_id);

            $customer_name          = $customer_details[0]['customer_name'];
            $customer_city          = $customer_details[0]['city_unique_id'];
            $customer_state         = $customer_details[0]['state_unique_id'];

            $customer_city          = city($customer_city);
            $customer_city          = $customer_city[0]['city_name'];

            $customer_state         = state($customer_state);
            $customer_state         = $customer_state[0]['state_name'];   

            // Needed Details

            // User Details
            $user_details           = user_name($select_result['sess_user_id']);

            $staff_unique_id        = $user_details[0]['staff_unique_id'];

            $staff_details          = staff_name ($staff_unique_id);

            $staff_name             = $staff_details[0]['staff_name'];
            $phone_no               = $staff_details[0]['office_contact_no'];
            $office_mail            = $staff_details[0]['office_email_id'];

            $work_designation       = $staff_details[0]['designation_unique_id'];

            $work_designation       = work_designation($work_designation);

            $work_designation       = $work_designation[0]['designation_type'];

        }
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

<input type="hidden" name="unique_id" id="unique_id" value="<?=$unique_id;?>">
<input type="hidden" name="quotation_no" id="quotation_no" value="<?=$quotation_no;?>">
<input type="hidden" name="quotation_print" id="quotation_print" value="<?=$quotation_print;?>">

<div class="card">
    <div class="card-body">

        <div class="page-header">
            <img src="img/letter_back/1-header.png" id="header_img" class="w-100 m-0 p-0 d-print-none backgrounds" alt="Header Image">
        </div>

        <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp">

        <?php if ($is_cancel) { ?>
        <img src="img/cancel_stamp.png" class="w-100 cancel_stamp" alt="cancel-stamp">
        <?php } ?>

        <?php if (!$is_approve) { ?>
        <img src="img/waiting_for_approval.png" class="w-100 cancel_stamp" alt="cancel-stamp">
        <?php } ?>

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
                                    <!-- <div class="mt-3">
                                        <p><b>Hello, Stanley Jones</b></p>
                                        <p class="text-muted">Thanks a lot because you keep purchasing our products. Our company
                                            promises to provide high quality products for you as well as outstanding
                                            customer service for every transaction. </p>
                                    </div> -->

                                    <h4 class="font-weight-bold text-secondry">To</h4>
                                    <h5 class="text-secondary m-1"><b><?=$quotation_to;?></b>,</h5>
                                    <h5 class="text-secondary m-1"><?=$customer_name;?></h5>
                                    <h5 class="text-secondary m-1"><?=$customer_city;?>,</h5>
                                    <h5 class="text-secondary m-1"><?=$customer_state;?></h5>
                                    <!-- <h5 class="text-secondary">CHENNAI - 600032</h5> -->

                                </div><!-- end col -->
                                <div class="col-md-4 offset-md-2">
                                    <div class="mt-3 float-right">
                                        <p class="m-b-10 text-secondary">Quotation Date  <span class="float-right"> &nbsp;&nbsp;&nbsp;&nbsp;<strong class='font-weight-bold'> <?=disdate($quotation_date);?> </strong> </span></p>
                                        <p class="m-b-10 text-secondary mt-n2">Quotation No &nbsp; <span class="float-right"><strong class='font-weight-bold'><?=$quotation_no;?></strong></span></p>
                                    </div>
                                </div><!-- end col -->
                            </div>
                            <!-- end row -->

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="font-weight-bold text-secondry">Sub : Proposal for <?php echo $subject; ?> - reg</h5>
                                    <h5 class="text-secondary mb-3">Dear Sir/Madam,</h5>
                                    <h5 class="font-weight-bold text-secondry">Greetings from Ascent e-Digit Solutions (P) Ltd.,</h5>
                                
                                    <div class="mt-3">
                                        <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;Thanks for the kind courtesy extended and the reinforced confidence that you show on us and your support. Further to your enquiry regarding the requirement for <span class="text-secondary"><?php echo $subject; ?></span>, we are pleased to submit the price </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="font-weight-bold text-secondry"><u>Our Strengths</u> :</h5>                    
                                </div>
                                <div class="col-6 our_stengths">
                                    <ul>
                                        <li>30 Year's Experience in System Integrators</li>
                                        <li>An ISO 9001:2015, 27001:2013, 20000-1:2011 Certified Company</li>
                                        <li>Highly Professtional in Providing Solutions in IT Field</li>
                                        <!-- <li>Well-equipped Testing & Repair Centre</li>
                                        <li>Specialists in Server & Higher end Networking Solutions</li>
                                        <li>Branch & Support Centers across in India</li> -->
                                    </ul>
                                </div>
                                <div class="col-6">
                                    <ul>
                                        <!-- <li>30 Year's Experience in System Integrators</li>
                                        <li>An ISO 9001:2015, 27001:2013, 20000-1:2011 Certified Company</li>
                                        <li>Highly Professtional in Providing Solutions in IT Field</li> -->
                                        <li>Well-equipped Testing & Repair Centre</li>
                                        <li>Specialists in Server & Higher end Networking Solutions</li>
                                        <li>Branch & Support Centers across in India</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="font-weight-bold text-secondry"><u>Commercial Proposal</u> :</h5>
                                    <table id="quotation_product_datatable" class="table dt-responsive w-100 border-bottom border-dark">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Product Description</th>
                                                <th>Make & Model</th>
                                                <th>Qty</th>
                                                <th>Rate</th>
                                                <th>GST</th>
                                                <th>GST Value</th>
                                                <th>Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                        <tbody class="table_footer">
                                            <tr>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th>Total</th>
                                                <th id="gst_total" class="text-right"></th>
                                                <th id="net_total" class="text-right"></th>
                                            </tr>
                                        </tbody>                                      
                                    </table>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="font-weight-bold text-secondry">Total in Words : <span class="" id="total_in_words"></span></h5>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <h5 class="font-weight-bold text-secondry"><u>Terms & Conditions</u> :</h5>
                                    <ul>
                                        <li>Payment Terms: <span class="text-secondary font-weight-bold"><?php echo $payment_terms; ?></span> </li>
                                        <li>Warranty Period : <span class="text-secondary font-weight-bold"><?php echo $warranty_period; ?></span> </li>
                                        <li>Delivery Address & Contact Numbers Required Along With Purchase Order for Delivery.</li>
                                        <li>Delivery: <span class="text-secondary font-weight-bold"><?php echo $delivery_period; ?></span> from the Date of Receiving Purchase Order.</li>
                                        <li>Material Delivery: <span class="text-secondary font-weight-bold">Free of Cost at Destination Address.</span></li>
                                        <li>Order & Payment: <span class="text-secondary font-weight-bold">Should be placed on Ascent e-Digit Solutions (P) Ltd, Chennai</span></li>
                                        <li>Order Delivery Follow up: <span class="text-secondary font-weight-bold"> <?php echo $staff_name; ?> / <?php echo $phone_no; ?></span>.</li>
                                    </ul>
                                    
                                </div>
                            </div>

                            <div class="row page_break_before">
                                <div class="col-12 mt-3">
                                    <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;We trust you find our offer in line  with your requirements and look forward to partnering you in all your IT endeavours. We request you to call for any further clarifications need regarding the above same order. </p>
                                </div>
                                <div class="col-12  mt-4 ">
                                    <h5 class="font-weight-bold text-secondry float-right"><span class="text-muted">For</span> Ascent e-Digit solutions (P) Ltd</h5>
                                </div>
                                <div class="col-12">
                                    <input type="hidden" name="user_id" id="user_id" value="<?php echo $_SESSION['user_id'];?>">
                                    <img src="" class="float-right mr-4 d-print-none backgrounds print_backgrounds d-none" id="user_sign" width="90px;" alt="Signs" style="
                                        position: absolute;
                                        right: 60px;
                                        top: -15px;
                                    ">
                                    <img src="" class="d-print-none backgrounds print_backgrounds d-none" id="branch_seal" width="120px;" alt="Seal" style="
                                        top: -75px;
                                        right: 380px;
                                        position: absolute;
                                    ">
                                </div>
                                <div class="col-12 mb-n2 mt-4">
                                    <h5 class="font-weight-bold text-secondry float-right"> <span class="text-secondary"><?php echo $staff_name; ?> / <?php echo $work_designation; ?></span> </h5>
                                    <!-- <h5 class="font-weight-bold text-secondry float-right"> <span class="text-secondary"> +91 9952555305 / <u>sugan@aedindia.com</u> </span> </h5> -->
                                </div>
                                
                                <div class="col-12">
                                    <h5 class="font-weight-bold text-secondry float-right"> <span class="text-secondary"> <?php echo $phone_no; ?> / <u><?php echo $office_mail; ?></u> </span> </h5>
                                </div>
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