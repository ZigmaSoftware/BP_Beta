<!-------------fonts----------------->
<link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">




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

$certificate_no     = "";
$certificate_date   = $today;
$certificate_type   = 1;
$staff_name         = "";
$designation        = "";
$department         = "";
$join_date          = "";
$relieve_date       = "";
$purpose_type       = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        // $where      = [
        //     "unique_id" => $unique_id
        // ];
        
        $where = 'unique_id = "'.$unique_id.'"';

        $table      =  "service_certificate";

        $columns    = [
            "certificate_no",
            "certificate_date",
            "certificate_type",
            "name",
            "designation",
            "department",
            "address",
            "purpose_type",
            "gross_salary",
            "join_date",
            "relieve_date"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
        // print_r($result_values);
        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $certificate_no     = $result_values["certificate_no"];
            $certificate_date   = $result_values["certificate_date"];
            $certificate_type   = $result_values["certificate_type"];
            $staff_name         = $result_values["name"];
            $designation        = $result_values["designation"];
            $department         = $result_values["department"];
            $address            = $result_values["address"];
            $purpose_type       = $result_values["purpose_type"];
            
            $gross_salary       = $result_values["gross_salary"];
            $join_date          = disdate($result_values["join_date"]);
            $relieve_date       = $result_values["relieve_date"];

            $work_location       = work_location($result_values['unique_id']);
            $work_location       =$work_location[0]['work_location'];
        
            $emp_nos                    = employee_no($result_values['name']);
            $employee_no =  $emp_nos[0]['employee_id'];

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



// $branch_options = branch_name();

// // print_r($staff_name_options);

// $staff_name_options = select_option($branch_options,"Select Staff",$staff);
$company_name_options          = company_name();
$company_name_options          = select_option($company_name_options, "Select company");


?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Logo & title -->

                <div class="page-header">
                   <img src="assets/images/blue_planet_logo.png" id="header_img" class="m-0 p-0 " width="10%" alt="Header Image">
                </div>
                <!-- <img src="img/cover.png" class="w-100 background_stamp d-print-none backgrounds" alt="background-stamp"> -->
                <div class="page-header-space"></div>
                <div class="row mt-5 mb-5">
                    <div class="col-12 text-center">
                        <u>
                            <h5 class="head-bold text-secondry mb-1 conc">
                                TO WHOM SO EVER IT MAY CONCERN
                            </h5>
                        </u>
                    </div>
                </div>



                
                <div class="row mt-3" style="margin-bottom: 17px;">
                    <div class="col-md-5">
                        <h5 class="head-bold text-secondry mb-1">Date : <?=$certificate_day?><sup><?=$certificate_position;?></sup><?=$certificate_month_year; ?> </span></h5>
                        <h5 class="head-bold text-secondry mb-1">Ref : <?= $certificate_no; ?></h5>
                    </div>
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-12">
                        <div class="mt-3">
                            <h5 class="text-secondry mb-1"><?= $mr_ms; ?> <?= $staff; ?></h5>
                            <h5 class="text-secondry mb-1"><b>E No<span class="colon-span-emp"> :</span></b><?= $employee_no; ?></h5>
                            <h5 class="text-secondry mb-1"><b>Designation <span class="colon-span"> :</span></b><?= $designation; ?></h5>
                            <h5 class="text-secondry mb-1"><b>Department <span class="colon-span"> :</span></b><?=   $department; ?></h5>
                            <h5 class="text-secondry mb-1"><b>Location <span class="colon-span"> :</span></b><?= $work_location; ?></h5></br> 

                            
                            <p class="p-indent ">This is to certify that <strong class="p-without-indent" style="color: #000;"><?=$mr_ms;?> <?=$staff; ?></strong> <?=$has_had;?> been working as <strong class="p-without-indent" style="color: #000;"><?=$designation?></strong> in the department of <strong class="p-without-indent" style="color: #000;"> <?=$department; ?> </strong>our esteemed organization from <strong class="p-without-indent" style="color: #000;"><?=$join_date;?></strong> to as on <strong class="p-without-indent" style="color: #000;"> <?=$relieve_date;?></strong>. <?=$his_her;?> conduct during the above period at our organization <?=$has_had;?> been good.  
                            <? if($purpose_type=="loan"){?>The permanent address is <strong class="p-without-indent" style="color: #000;"><?=$address?>.</strong><? }?>
                           <br> <br>
                           <? if($purpose_type=="loan"){?>
                            <?=$his_her?> gross salary is <strong class="p-without-indent" style="color: #000;"><?=$gross_salary;?></strong>/-. This certificate issued for the purpose of Loan. We are issuing this letter on the request of our employee and do not hold any liability on behalf of this letter or part of this letter on our company.<? }?>
                            </p>

                        </div>
                    </div>
                </div>

                <div class="row mt-5 mb-5">
                    <div class="col-12  mt-4 mb-4">
                    <h4 class="text-secondry bold-text"><span style="font-weight : initial;">For</span></h4>
                    </div>
                    <!-- <div class="col-12 mt-5 mb-5">
                        <img src="img/letter_back/signs/5ff729124da1d67875.png">
                    </div> -->
                    <div class="col-3">
                        <!-- <img src="img/letter_back/sign-3.png" style="width:60%;"> -->
                    </div> 
                    <div class="col-12">
                        <h4 class="text-secondry bold-text">Manager-HR and Admin</h4>
                    </div>
                </div>

                <!-- <div class="mt-4 mb-1 d-print-none">
            
                    <div class="col-md-6">
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input" id="print_header_footer" name="print_header_footer" onchange="print_header_and_footer()" >
                            <label class="custom-control-label header text-danger" for="print_header_footer" >With Header & Footer</label>
                        </div>
                    </div> -->

                    <!-- <div class="col-md-6">
                        <div class="form-group row ">
                            <label class="col-md-3 col-form-label" for="branch">Branch</label>
                            <div class="col-md-9">
                                <select name="branch" id="branch" onchange="letter_head_change(this.value)" class="select2 form-control" required>
                                <?=$branch_options;?>                         
                                </select>
                            </div>
                        </div> -->
                    <!-- </div>

                    <div class="text-right">
                        
                        <?php echo btn_cancel($btn_cancel);?>

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
                        <select name="company_name" id="company_name" onchange="letter_head_change(this.value)" class="select2 form-control" required> <?= $company_name_options; ?>
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

                <div class="page-footer-space"></div>

                <div class="page-footer"> -->
                    <!-- <img src="img/letter_back/1-footer.png" id="footer_img" class="w-100 m-0 p-0 backgrounds d-print-none" alt="Footer Image"> -->
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