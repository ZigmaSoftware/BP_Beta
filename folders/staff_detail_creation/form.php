<?php



$js_css_file_version = "0.0.7";
// $js_css_file_version = "1.0";

$js_css_file_comment = "?v=" . $js_css_file_version;

?>

<head>
    <meta charset="utf-8" />
    <title>XEON</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Ascent E-Digit Solutions" name="description" />
    <meta content="infobytes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <meta http-equiv="cache-control" content="no-cache" />
    <meta http-equiv="Pragma" content="no-cache" />
    <meta http-equiv="Expires" content="-1" />

    <link rel="shortcut icon" type="image/x-icon" href="img/icon/favicon.ico">

    <!-- App css -->
    <link href="../../assets/css/bootstrap.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="../../assets/css/app.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <link href="../../assets/css/bootstrap-dark.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" />
    <link href="../../assets/css/app-dark.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" id="app-dark-stylesheet" />

    <!-- icons -->
    <link href="../../assets/css/icons.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
    <link href="../../assets/css/common.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

    <?php if (session_id() and ($user_id)) { ?>
        <!-- Datatables -->
        <!-- <link href="../../assets/libs/datatables/datatables.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        <link href="../../assets/libs/datatables-responsive/css/responsive.bootstrap4.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" /> -->
        <link href="../../assets/libs/datatables.net-bs4/css/dataTables.bootstrap4.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        <link href="../../assets/libs/datatables.net-responsive-bs4/css/responsive.bootstrap4.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        <link href="../../assets/libs/datatables.net-buttons-bs4/css/buttons.bootstrap4.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- Select2 -->
        <link href="../../assets/libs/select2/css/select2.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />
        <link href="../../assets/libs/select2-bootstrap4/select2-bootstrap4.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- Dropify -->
        <link href="../../assets/libs/dropify/dist/css/dropify.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- Auto complete -->
        <link href="../../assets/libs/autocomplete/css/autocomplete.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- jQuery Multiselect -->
        <link href="../../assets/libs/jquery_multiselect/jquery.multiselect.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

        <!-- jQuery-->
        <script src="../../assets/libs/jquery/jquery-3.5.1.min.js<?php echo $js_css_file_comment; ?>"></script>


    <?php } ?>

    <!-- Flatpicker -->
    <link href="../../assets/libs/flatpickr/flatpickr.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />


    <!-- Sweetalert2 -->
    <link href="../../assets/libs/sweetalert2/sweetalert2.min.css<?php echo $js_css_file_comment; ?>" rel="stylesheet" type="text/css" />

</head>

<?php
// This file Only PHP Functions
include 'function.php';

// Include DB file and Common Functions
include '../../config/dbconfig.php';
// include '../../inc/header.php';
// include '../inc/footer.php';
// include '../../inc/top-menu.php';



// print_r($attendance_setting_options);

// Common Variable for all form
$btn_text               = "Save";
$btn_action             = "create";
$is_btn_disable         = "";

$is_active          = 1;
$unique_id              = "";

$premises_out           = " Checked ";
$premises_in            = "";
$attendance_setting_id  = "";

/**
 * staff Profile Section 
 *      - vaiable declaration 
 *      - update informations
 *      - unique id details
 */
$staff_name                   = "";
$employee_id                  = "";
$date_of_join                 = "";
$designation_unique_id        = "";
$biometric_id                 = "";
$contact_no                   = "";
$age                          = "";
$dob                          = "";
$doj                          = "";
$gender                       = "";
$martial_status               = "";
$date_of_birth                = "";
$personal_contact_no          = "";
$office_contact_no            = "";
$personal_email_id            = "";
$blood_group                  = "";
$pre_country                  = "";
$pre_state                    = "";
$pre_city                     = "";
$pre_building_no              = "";
$pre_street                   = "";
$pre_area                     = "";
$pre_pincode                  = "";
$perm_country                 = "";
$perm_state                   = "";
$perm_city                    = "";
$perm_building_no             = "";
$perm_street                  = "";
$perm_area                    = "";
$perm_pincode                 = "";
$relationship_opt             = "";
$exist_ill_status             = "";
$exist_insur_status           = "";
$phy_chal_status              = "";
$bank_status                  = "Primary";
$claim_status                 = "";
$aadhar_no                    = "";
$pan_no                       = "";
$office_email_id              = "";
$file_name                    = "";
$same_address_status          = "";
$department                   = "";
$work_location                = "";
$reporting_officer            = "";
$father_name                  = "";
$doc_dob                      = "";
$esi_no                       = "";
$pf_no                        = "";
$qualification                = "";
$company_name                 = "";

$designation_options        = "<option value='' disabled='disabled' selected>Select Designation</option>";

//   if(isset($_GET["unique_id"])) {
//       if (!empty($_GET["unique_id"])) {

//           $unique_id  = $_GET["unique_id"];
//           $where      = [
//               "unique_id" => $unique_id
//           ];

//           $table            =  "staff_detail_creation";
$emp_id = base64_decode($_GET["unique_id"]);

//  $id = str_replace("Form.php?","demo",".$emp_id.");
//   if(isset($_GET["unique_id"])) {
//       if (!empty($_GET["unique_id"])) {
if (isset($emp_id)) {
    if (!empty($emp_id)) {

        global $pdo;
        //   $unique_id  = $_GET["unique_id"];

        $where      = [
            "unique_id" => $emp_id
        ];

        $table            =  "staff";
        $table_continuous = "staff_continuous";

        $columns    = [
            "staff_name",
            "employee_id",
            "date_of_join",
            "designation_unique_id",
            "biometric_id",
            "age",
            "gender",
            "martial_status",
            "date_of_birth",
            "personal_contact_no",
            "office_contact_no",
            "personal_email_id",
            "blood_group",
            "pre_country",
            "pre_state",
            "pre_city",
            "pre_building_no",
            "pre_street",
            "pre_area",
            "pre_pincode",
            "perm_country",
            "perm_state",
            "perm_city",
            "perm_building_no",
            "perm_street",
            "perm_area",
            "perm_pincode",
            "aadhar_no",
            "pan_no",
            "office_email_id",
            "file_name",
            "same_address_status",
            "work_location",
            "premises_type",
            "branch_id",
            "attendance_setting_id",
            "department",
            "reporting_officer",
            "father_name",
            "doc_dob",
            "esi_no",
            "pf_no",
            "qualification",
            "company_name",
            "unique_id"
        ];

        $columns_continuous     = [
            "conveyance_default_value",
            "medical_default_value",
            "pf_default_value",
            "esi_default_value",
            "educational_default_value",
            "unique_id"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $table_details_continuous   = [
            $table_continuous,
            $columns_continuous
        ];

        $result_values             = $pdo->select($table_details, $where);
        $result_values_continuous  = $pdo->select($table_details_continuous, $where);

        if (($result_values->status) || ($result_values_continuous->status)) {


            $result_values            = $result_values->data;
            $result_values_continuous = $result_values_continuous->data;
            $unique_id  = $result_values[0]["unique_id"];
            $staff_name                     = $result_values[0]["staff_name"];
            $employee_id                    = $result_values[0]["employee_id"];
            $doj                            = $result_values[0]["date_of_join"];
            $designation_unique_id          = $result_values[0]["designation_unique_id"];
            $biometric_id                   = $result_values[0]["biometric_id"];
            $age                            = $result_values[0]["age"];
            $qualification                  = $result_values[0]["qualification"];
            $gender                         = $result_values[0]["gender"];
            $martial_status                 = $result_values[0]["martial_status"];
            $dob                            = $result_values[0]["date_of_birth"];
            $personal_contact_no            = $result_values[0]["personal_contact_no"];
            $office_contact_no              = $result_values[0]["office_contact_no"];
            $personal_email_id              = $result_values[0]["personal_email_id"];
            $blood_group                    = $result_values[0]["blood_group"];
            $pre_country                    = $result_values[0]["pre_country"];
            $perm_country                   = $result_values[0]["perm_country"];
            $pre_state                      = $result_values[0]["pre_state"];
            $perm_state                     = $result_values[0]["perm_state"];
            $pre_city                       = $result_values[0]["pre_city"];
            $perm_city                      = $result_values[0]["perm_city"];
            $pre_building_no                = $result_values[0]["pre_building_no"];
            $perm_building_no               = $result_values[0]["perm_building_no"];
            $pre_area                       = $result_values[0]["pre_area"];
            $perm_area                      = $result_values[0]["perm_area"];
            $pre_street                     = $result_values[0]["pre_street"];
            $perm_street                    = $result_values[0]["perm_street"];
            $pre_pincode                    = $result_values[0]["pre_pincode"];
            $perm_pincode                   = $result_values[0]["perm_pincode"];
            $aadhar_no                      = $result_values[0]["aadhar_no"];
            $pan_no                         = $result_values[0]["pan_no"];
            $claim_status                   = $result_values[0]["claim_status"];
            $relieve_date                   = $result_values[0]["relieve_date"];
            $premises_type                  = $result_values[0]["premises_type"];
            $branch_id                      = $result_values[0]["branch_id"];
            $attendance_setting_id          = $result_values[0]["attendance_setting_id"];
            $relieve_status                 = $result_values[0]["relieve_status"];
            $relieve_reason                 = $result_values[0]["relieve_reason"];
            $salary                         = $result_values[0]["salary"];
            $annum_salary                   = $result_values[0]["annum_salary"];
            $basic_wages                    = $result_values[0]["basic_wages"];
            $annum_basic_wages              = $result_values[0]["annum_basic_wages"];
            $hra                            = $result_values[0]["hra"];
            $annum_hra                      = $result_values[0]["annum_hra"];
            $conveyance                     = $result_values[0]["conveyance"];
            $annum_conveyance               = $result_values[0]["annum_conveyance"];
            $medical_allowance              = $result_values[0]["medical_allowance"];
            //   $emer_contact_person            = $result_values[0]["emer_contact_person"];
            //   $emer_contact_no                = $result_values[0]["emer_contact_no"];
            $annum_medical_allowance        = $result_values[0]["annum_medical_allowance"];
            $education_allowance            = $result_values[0]["education_allowance"];
            $annum_education_allowance      = $result_values[0]["annum_education_allowance"];
            $other_allowance                = $result_values[0]["other_allowance"];
            $annum_other_allowance          = $result_values[0]["annum_other_allowance"];
            $pf                             = $result_values[0]["pf"];
            $annum_pf                       = $result_values[0]["annum_pf"];
            $esi                            = $result_values[0]["esi"];
            $annum_esi                      = $result_values[0]["annum_esi"];
            $total_deduction                = $result_values[0]["total_deduction"];
            $annum_total_deduction          = $result_values[0]["annum_total_deduction"];
            $net_salary                     = $result_values[0]["net_salary"];
            $annum_net_salary               = $result_values[0]["annum_net_salary"];
            $purformance_allowance          = $result_values[0]["purformance_allowance"];
            $annum_purformance_allowance    = $result_values[0]["annum_purformance_allowance"];
            $ctc                            = $result_values[0]["ctc"];
            $annum_ctc                      = $result_values[0]["annum_ctc"];
            $office_email_id                = $result_values[0]["office_email_id"];
            $file_name                      = $result_values[0]["file_name"];
            $same_address_status            = $result_values[0]["same_address_status"];
            $work_location                  = $result_values[0]["work_location"];
            $department                     = $result_values[0]["department"];
            $reporting_officer              = $result_values[0]["reporting_officer"];
            $father_name                    = $result_values[0]["father_name"];
            $doc_dob                        = $result_values[0]["doc_dob"];
            $esi_no                         = $result_values[0]["esi_no"];
            $pf_no                          = $result_values[0]["pf_no"];

            $company_name                        = $result_values[0]["company_name"];
            if (!(empty($result_values_continuous))) {
                $conveyance_default_value       = $result_values_continuous[0]["conveyance_default_value"];
                $medical_default_value          = $result_values_continuous[0]["medical_default_value"];
                $pf_default_value               = $result_values_continuous[0]["pf_default_value"];
                $esi_default_value              = $result_values_continuous[0]["esi_default_value"];
                $educational_default_value      = $result_values_continuous[0]["educational_default_value"];
            }

            if ($premises_type) {
                $premises_in = " checked ";
                $premises_out = "";
            }

            $exp_branch_id           = explode(",", $branch_id);

            $pre_state_options        = state("", $pre_country);
            $pre_state_options        = select_option($pre_state_options, "Select the State", $pre_state);
            $perm_state_options       = state("", $perm_country);
            $perm_state_options       = select_option($perm_state_options, "Select the State", $perm_state);

            $pre_city_options         = city("", $pre_state);
            $pre_city_options         = select_option($pre_city_options, "Select the City", $pre_city);
            $perm_city_options       = city("", $perm_state);
            $perm_city_options        = select_option($perm_city_options, "Select the City", $perm_city);

            $btn_text                 = "Update";
            $btn_action               = "update";
        } else {
            $btn_text                 = "Error";
            $btn_action               = "error";
            $is_btn_disable           = "disabled='disabled'";
        }
    }
}

$designation_options          = designation();
$designation_options          = select_option($designation_options, "Select Designation", $designation_unique_id);

$martial_options        = [
    "married" => [
        "unique_id" => "married",
        "value"     => "married",
    ],
    "unmarried" => [
        "unique_id" => "unmarried",
        "value"     => "unmarried",
    ],
];
$martial_options        = select_option($martial_options, "Select", $martial_status);
//   $relieve_options        = ["Active" => [
//                               "unique_id" => "Active",
//                               "value"     => "Active",
//                                   ],
//                                   "Inactive" => [
//                               "unique_id" => "Inactive",
//                               "value"     => "Inactive",
//                                   ],
//                               ];
//   $relieve_options        = select_option($relieve_options,$relieve_status);

// $relieve_options= active_status($relieve_status);
$relieve_options = active_status($is_active);
// echo $relieve_options;


$exist_ill_options        = [
    "Yes" => [
        "unique_id" => "Yes",
        "value"     => "Yes",
    ],
    "No" => [
        "unique_id" => "No",
        "value"     => "No",
    ],
];
$exist_ill_options        = select_option($exist_ill_options, "Select", $exist_ill_status);
$claim_options        = [
    "Yes" => [
        "unique_id" => "Yes",
        "value"     => "Yes",
    ],
    "No" => [
        "unique_id" => "No",
        "value"     => "No",
    ],
];
$claim_options        = select_option($claim_options, "Select", $claim_status);
$issued_options        = [
    "Issued" => [
        "unique_id" => "Issued",
        "value"     => "Issued",
    ],
    "Returned" => [
        "unique_id" => "Returned",
        "value"     => "Returned",
    ],
];
$issued_options        = select_option($issued_options, "Select", $issue_status);

$mode_options        = [
    "Two Wheeler" => [
        "unique_id" => "Two Wheeler",
        "value"     => "Two Wheeler",
    ],
    "Four Wheeler" => [
        "unique_id" => "Four Wheeler",
        "value"     => "Four Wheeler",
    ],
    "Badge License" => [
        "unique_id" => "Badge License",
        "value"     => "Badge License",
    ],
    "Both Two & Four Wheeler" => [
        "unique_id" => "Both Two & Four Wheeler",
        "value"     => "Both Two & Four Wheeler",
    ],
];
$mode_options        = select_option($mode_options, "Select", $mode_status);
$exist_insu_options        = [
    "Yes" => [
        "unique_id" => "Yes",
        "value"     => "Yes",
    ],
    "No" => [
        "unique_id" => "No",
        "value"     => "No",
    ],
];
$exist_insu_options        = select_option($exist_insu_options, "Select", $exist_insur_status);

$phy_chal_options        = [
    "Yes" => [
        "unique_id" => "Yes",
        "value"     => "Yes",
    ],
    "No" => [
        "unique_id" => "No",
        "value"     => "No",
    ],
];
$phy_chal_options        = select_option($phy_chal_options, "Select", $phy_chal_status);

$bank_options     = [
    "Primary" => [
        "unique_id" => "Primary",
        "value"     => "Primary",
    ],
    "Secondary" => [
        "unique_id" => "Secondary",
        "value"     => "Secondary",
    ],
];
$bank_options     = select_option($bank_options, "Select", $bank_status);
$relationship_options        = [
    "Father" => [
        "unique_id" => "Father",
        "value"     => "Father",
    ],
    "Mother" => [
        "unique_id" => "Mother",
        "value"     => "Mother",
    ],
    "Husband" => [
        "unique_id" => "Husband",
        "value"     => "Husband",
    ],
    "Wife" => [
        "unique_id" => "Wife",
        "value"     => "Wife",
    ],
    "Son" => [
        "unique_id" => "Son",
        "value"     => "Son",
    ],
    "Daughter" => [
        "unique_id" => "Daughter",
        "value"     => "Daughter",
    ],
];
$relationship_options        = select_option($relationship_options, "Select", $relationship_opt);

$company_name_option              = company_name_option();
$company_name_option        = select_option($company_name_option, "Select", $company_name);

if ($dob == '') {
    $date_of_birth = $today;
} else {
    $date_of_birth = $dob;
}
if ($doj == '') {
    $date_of_join = $today;
} else {
    $date_of_join = $doj;
}


$country_options              = country();

$pre_country_options          = select_option($country_options, "Select the Country", $pre_country);

$perm_country_options         = select_option($country_options, "Select the Country", $perm_country);

$blood_group_options          = blood_group();
$blood_group_options          = select_option($blood_group_options, "Select Blood Group", $blood_group);

$branch_options               = branch();
$branch_options               = select_option($branch_options, "Select Branch", $branch_id);

$staff_options               = staff_name();
$staff_options               = select_option($staff_options, "Select", $reporting_officer);

$attendance_setting_options   = attendance_setting();
$attendance_setting_options   = select_option($attendance_setting_options, "Select Attendance Setting", $attendance_setting_id);
// $active_status_options= active_status($is_active);
$staff_id = $_SESSION['sess_user_type'];
?>
<!-- Unique ID hidden input -->
<input type="hidden" id="unique_id" value="<?php echo $unique_id; ?>">
<input type="hidden" id="staff_unique_id" value="<?php echo $unique_id; ?>">



<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- <form> -->
                <div id="staffcreatewizard">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-3" id="loadmore">
                        <li class="nav-item" id="staff_tab" >
                            <a href="#officialdetails_tab" onchange="my_tab_check()" data-toggle="tab"  class="nav-link rounded-0 pt-2 pb-2 active">
                                <i class="mdi mdi-account-circle mr-1"></i>
                                <span class="d-none d-sm-inline">Staff Details</span>
                            </a>
                        </li>
                        <li class="nav-item" id="dep_tab" >
                            <a href="#dependentdetails_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 " >
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Dependent Details</span>
                            </a>
                        </li>
                        <li class="nav-item acc_tab" >
                            <a href="#account_details_tab"  data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 ">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Account Details</span>
                            </a>
                        </li>
                        <li class="nav-item" >
                            <a href="#qualification_tab" id="qua_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 ">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Qualification Details</span>
                            </a>
                        </li>
                        <li class="nav-item " >
                            <a href="#experience_tab" id="exp_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 ">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Experience Details</span>
                            </a>
                        </li>
                        <li class="nav-item " >
                            <a href="#asset_tab" id="ass_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 ">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Asset/Vehicle Details</span>
                            </a>
                        </li>
                        <!-- <li class="nav-item">
                        <a href="#salary_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Salary Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#relieve_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Relieve Details</span>
                        </a>
                    </li> -->
                    </ul>
                    <div class="tab-content b-0 mb-0 pt-0">
                        <div id="bar" class="progress mb-3" style="height: 7px;">
                            <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 20%;"></div>
                        </div>
                        <div class="tab-pane active" id="officialdetails_tab" >

                            <!-- Form Begins Here -->
                            <form class="was-validated valdity staff_profile_form" id="staff_profile_form" name="staff_profile_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-6" style="color: red">Personal Details :</label>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="staff_name"> Staff Name</label>
                                            <div class="col-md-4">
                                                <input pattern="[a-zA-Z\- \/_?:.,\s]+" type="text" id="staff_name" name="staff_name" class="form-control" value="<?php echo $staff_name; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="staff_id"> Staff Id</label>
                                            <div class="col-md-4">
                                                <!-- <input  type="text" id="staff_id" name="staff_id" class="form-control" value="<?php echo $employee_id; ?>"  required> -->
                                                <input type="hidden" name="staff_id" id="staff_id" class="form-control" value='<?php echo  $employee_id; ?>' required>
                                                <div class="col-md-3">
                                                    <h4 class="text-info" id="employee_id"><?php echo $employee_id; ?></h4>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="gender"> Gender</label>
                                            <div class="col-md-4">
                                                <select name="gender" id="gender" class="select2 form-control"   required>
                                                    <option value="">Select Gender </option>
                                                    <option value="1" <?php if ($gender == "1") {
                                                                            echo "selected";
                                                                        } ?>>Male </option>
                                                    <option value="2" <?php if ($gender == "2") {
                                                                            echo "selected";
                                                                        } ?>>Female</option>
                                                    <option value="3" <?php if ($gender == "3") {
                                                                            echo "selected";
                                                                        } ?>>Others</option>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="father_name"> Father Name</label>
                                            <div class="col-md-4">
                                                <input pattern="[a-zA-Z\- \/_?:.,\s]+" type="text" id="father_name" name="father_name" class="form-control" value="<?php echo $father_name; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="date_of_birth"> Date Of Birth</label>
                                            <div class="col-md-4">
                                                <input type="date" name="date_of_birth" class="form-control" value="<?php echo $date_of_birth; ?>" required onChange="ageCalculate(this.value)">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="doc_dob"> Document DOB</label>
                                            <div class="col-md-4">
                                                <input type="date" name="doc_dob" class="form-control" value="<?php echo $doc_dob; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="age"> Age</label>
                                            <div class="col-md-4">
                                                <input type="text" onkeypress="return isNumber(event)" maxlength="3" id="age" name="age" class="form-control" value="<?php echo $age; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="martial_status"> Martial Status</label>
                                            <div class="col-md-4">
                                                <select name="martial_status" id="martial_status" class="select2 form-control" required><?php echo $martial_options; ?></select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="personal_contact_no"> Personal Contact No</label>
                                            <div class="col-md-4">
                                                <input type="text" onkeypress="return isNumber(event)" maxlength="10" id="personal_contact_no" name="personal_contact_no" minlength="10" class="form-control" value="<?php echo $personal_contact_no; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="office_contact_no"> Office Contact No</label>
                                            <div class="col-md-4">
                                                <input type="text" onkeypress="return isNumber(event)" minlength="10" maxlength="10" id="office_contact_no" name="office_contact_no" class="form-control" value="<?php echo $office_contact_no ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="personal_email_id"> Personal Email Id</label>
                                            <div class="col-md-4">
                                                <input type="email" id="personal_email_id" name="personal_email_id" class="form-control" value="<?php echo $personal_email_id; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="office_email_id"> Office Email Id</label>
                                            <div class="col-md-4">
                                                <input type="email" id="office_email_id" name="office_email_id" class="form-control" value="<?php echo $office_email_id; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="aadhar_no"> Aadhar No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="aadhar_no" name="aadhar_no" class="form-control" value="<?php echo $aadhar_no; ?>" minlength="14" maxlength="14" placeholder="0000 0000 0000">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="pan_no"> Pan No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pan_no" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" maxlength="10" minlength="10" name="pan_no" class="form-control" value="<?php echo $pan_no; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="claim_status"> Medical Claim</label>
                                            <div class="col-md-4">
                                                <select name="claim_status" id="claim_status" class="select2 form-control"><?php echo $claim_options; ?></select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="blood_group"> Blood Group</label>
                                            <div class="col-md-4">
                                                <select name="blood_group" id="blood_group" class="select2 form-control" required> <?php echo $blood_group_options; ?></select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="qualification"> Qualifiation</label>
                                            <div class="col-md-4">
                                                <input type="text" id="qualification" name="qualification" class="form-control" value="<?php echo $qualification; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-6" style="color: red">Present Address :</label>
                                            <label class="col-md-2" style="color: red">Permanent Address :</label>
                                            <div class="custom-control custom-checkbox col-md-4 ">
                                                <input type="checkbox" class="custom-control-input" id="same_address" onClick="get_permanent_address(this.value)" <?php if ($same_address_status == 1) { ?>checked <?php } ?>>
                                                <label class="custom-control-label" style="color: red" for="same_address"> Same As Present Address </label>
                                                <input type="hidden" name="same_address_status" id="same_address_status" value='0'>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_country"> Country</label>
                                            <div class="col-md-4">
                                                <select name="pre_country" id="pre_country" class="select2 form-control" onchange="get_states(this.value);" required><?php echo $pre_country_options; ?></select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_country"> Country</label>
                                            <div class="col-md-4">
                                                <select name="perm_country" id="perm_country" class="select2 form-control" onchange="get_perm_states(this.value);" required><?php echo $perm_country_options; ?></select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_state"> State </label>
                                            <div class="col-md-4">
                                                <select name="pre_state" id="pre_state" class="select2 form-control" onchange="get_cities(this.value);" required> <?php echo $pre_state_options; ?></select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_state"> State </label>
                                            <div class="col-md-4">
                                                <select name="perm_state" id="perm_state" class="select2 form-control" onchange="get_perm_cities(this.value);" required> <?php echo $perm_state_options; ?></select>
                                                <input type="hidden" name="edit_perm_state_id" id="edit_perm_state_id" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_city"> City </label>
                                            <div class="col-md-4">
                                                <select name="pre_city" id="pre_city" class="select2 form-control" required><?php echo $pre_city_options; ?> </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_city_name"> City </label>
                                            <div class="col-md-4">
                                                <select name="perm_city" id="perm_city" class="select2 form-control" required> <?php echo $perm_city_options; ?> </select>
                                                <input type="hidden" name="edit_perm_city_id" id="edit_perm_city_id" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_building_no"> Building No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pre_building_no" name="pre_building_no" class="form-control" value="<?php echo $pre_building_no; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_building_no"> Building No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="perm_building_no" name="perm_building_no" class="form-control" value="<?php echo $perm_building_no; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_street"> Street</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pre_street" name="pre_street" class="form-control" value="<?php echo $pre_street; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_street"> Street</label>
                                            <div class="col-md-4">
                                                <input type="text" id="perm_street" name="perm_street" class="form-control" value="<?php echo $perm_street; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_area"> Area</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pre_area" name="pre_area" class="form-control" value="<?php echo $pre_area; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_area"> Area</label>
                                            <div class="col-md-4">
                                                <input type="text" id="perm_area" name="perm_area" class="form-control" value="<?php echo $perm_area; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pre_pincode"> Pincode</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pre_pincode" name="pre_pincode" maxlength="6" onkeypress="return isNumber(event)" minlength="6" class="form-control" value="<?php echo $pre_pincode; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="perm_pincode"> Pincode</label>
                                            <div class="col-md-4">
                                                <input type="text" id="perm_pincode" maxlength="6" minlength="6" onkeypress="return isNumber(event)" name="perm_pincode" class="form-control" value="<?php echo $perm_pincode; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-6" style="color: red">Official Details :</label>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="date_of_join"> Date of Join</label>
                                            <div class="col-md-4">
                                                <input type="date" id="date_of_join" name="date_of_join" class="form-control" value="<?php echo $date_of_join; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="designation"> Designation</label>
                                            <div class="col-md-4">
                                                <select name="designation" id="designation" class="select2 form-control" required><?php echo $designation_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="department"> Department </label>
                                            <div class="col-md-4">
                                                <input type="text" id="department" name="department" class="form-control" value="<?php echo $department; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="work_location"> Work Location </label>
                                            <div class="col-md-4">
                                                <input type="text" id="work_location" name="work_location" class="form-control" value="<?php echo $work_location; ?>" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="esi_no"> ESI No </label>
                                            <div class="col-md-4">
                                                <input type="text" id="esi_no" name="esi_no" class="form-control" value="<?php echo $esi_no; ?>">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="pf_no"> PF No </label>
                                            <div class="col-md-4">
                                                <input type="text" id="pf_no" name="pf_no" class="form-control" value="<?php echo $pf_no; ?>">
                                            </div>

                                        </div>

                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="biometric_id"> Bio Metric Id</label>
                                            <div class="col-md-4">
                                                <input type="text" id="biometric_id" name="biometric_id" class="form-control" value="<?php echo $biometric_id; ?>">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="pf_no"> Company Name </label>
                                            <div class="col-md-4">
                                                <select name="company_name" id="company_name" class="select2 form-control" required><?php echo $company_name_option; ?></select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-6" style="color: red">Profile Image :</label>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="biometric_id"> Staff Image</label>
                                            <div class="col-md-4">
                                                <input type="file" id="test_file" name="test_file" class="form-control dropify" data-default-file="uploads/staff/<?php echo $file_name ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-6" style="color: red">For Attendance :</label>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="premises"> Premises Type</label>
                                            <div class="col-md-4">
                                                <div class=" radio radio-primary form-check-inline">
                                                    <input type="radio" id="premises_out" onchange="//premises_check()" value="0" name="premises_status" <?= $premises_out; ?>>
                                                    <label for="premises_out"> OUT Premises </label>
                                                </div>
                                                <div class="radio radio-primary form-check-inline">
                                                    <input type="radio" id="premises_in" onchange="//premises_check()" value="1" name="premises_status" <?= $premises_in; ?>>
                                                    <label for="premises_in"> IN Premises</label>
                                                </div>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="staff_branch "> Branch </label>
                                            <div class="col-md-4">
                                                <select name="branch" id="branch" class="select2 form-control" onChange="get_branch_ids()" required>
                                                    <!-- multiple -->
                                                    <?php echo $branch_options; ?>
                                                </select>
                                                <input type="hidden" id="staff_branch" name="staff_branch" class="form-control" value="<?php echo $branch_id; ?>">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="attendance_setting"> Attendance Setting</label>
                                            <div class="col-md-4">
                                                <select name="attendance_setting" id="attendance_setting" class="select2 form-control" required>
                                                    <?php echo $attendance_setting_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="reporting_officer"> Reporting Officer</label>
                                            <div class="col-md-4">
                                                <select name="reporting_officer" id="reporting_officer" class="select2 form-control" required>
                                                    <?php echo $staff_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </form>
                            <ul class="list-inline mb-0 pager wizard">
                            <!-- <li class="previous list-inline-item disabled">
                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                        </li>  -->
                            <?php echo btn_cancel($btn_cancel); ?>
                            <li class="next list-inline-item float-right mr-0">

                                <!-- <a href="javascript: void(0);" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn" onclick="staff_detail_creation_cu()"><?php echo $btn_text; ?> & Continue</a> -->
                                <button type="button" name="btn" id="btn" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn" onclick="staff_detail_creation_cu(unique_id.value);"><?php echo $btn_text; ?> & Continue</button>
                            </li>
                            <!-- <li class="finish list-inline-item float-right mr-0">
                        <a href="javascript: void(0);" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn_finish">Finish</a>
                     </li> -->
                        </ul>


                        </div>
                        <div class="tab-pane" id="dependentdetails_tab">
                            <form class="was-validated dependent_details_form" id="dependent_details_form">
                                <div class="row">
                                    <div class="col-12">
                                        <h4 class="header-title" style="color: red"></h4>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="relationship"> Relationship</label>
                                            <div class="col-md-4">
                                                <select name="relationship" id="relationship" class="select2 form-control" required>
                                                    <?php echo $relationship_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="rel_name"> Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="rel_name" name="rel_name" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="rel_gender"> Gender</label>
                                            <div class="col-md-4">
                                                <select name="rel_gender" id="rel_gender" class="select2 form-control" required>
                                                    <option value="">Select Gender </option>
                                                    <option value="1">Male </option>
                                                    <option value="2">Female</option>
                                                    <option value="3">Others</option>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="rel_date_of_birth"> Date Of Birth</label>
                                            <div class="col-md-4">
                                                <input type="date" name="rel_date_of_birth" id="rel_date_of_birth" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="rel_aadhar_no">Aadhar No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="rel_aadhar_no" name="rel_aadhar_no" class="form-control" maxlength="12" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="occupation">Occupation</label>
                                            <div class="col-md-4">
                                                <input type="text" id="occupation" name="occupation" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="emer_contact_person">Emergency Contact Person</label>
                                            <div class="col-md-4">
                                                <input type="text" id="emer_contact_person" name="emer_contact_person" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="emg_contact_no">Emergency Contact Number</label>
                                            <div class="col-md-4">
                                                <input type="text" onkeypress="return isNumber(event)" maxlength="10" id="emer_contact_no" name="emer_contact_no" minlength="10" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="standard">Standard</label>
                                            <div class="col-md-4">
                                                <input type="text" id="standard" name="standard" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="school">School</label>
                                            <div class="col-md-4">
                                                <input type="text" id="school" name="school" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="existing_illness">Existing Illness</label>
                                            <div class="col-md-4">
                                                <select name="existing_illness" id="existing_illness" class="select2 form-control">
                                                    <?php echo $exist_ill_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="description">Description</label>
                                            <div class="col-md-4">
                                                <input type="text" id="description" name="description" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="existing_insurance"> Existing Insurance</label>
                                            <div class="col-md-4">
                                                <select name="existing_insurance" id="existing_insurance" class="select2 form-control">
                                                    <?php echo $exist_insu_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="insurance_no">Insurance No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="insurance_no" name="insurance_no" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="physically_challenged"> Physcically Challenged</label>
                                            <div class="col-md-4">
                                                <select name="physically_challenged" id="physically_challenged" class="select2 form-control">
                                                    <?php echo $phy_chal_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="remarks">Remarks</label>
                                            <div class="col-md-4">
                                                <input type="text" id="remarks" name="remarks" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light dependent_details_add_update_btn" onclick="dependent_details_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row" id="my_dependentent_table">
                                            <div class="col-12">
                                                <!-- Table Begiins -->
                                                <!-- <table id="dependent_details_datatable" class="table dt-responsive nowrap w-100">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Relationship</th>
                                             <th>Name</th>
                                             <th>Gender</th>
                                             <th>DOB</th>
                                             <th>Aadhar No</th>
                                             <th>Occupation</th>
                                             <th>Emergency Contact Person</th>
                                             <th>Emergency Contact No.</th>
                                             <th>Std. </th>
                                             <th>School</th>
                                             <th>Existing Illness</th>
                                             <th>Description</th>
                                             <th>Existing Ins.</th>
                                             <th>Insurance No</th>
                                             <th> Phy. Challenged</th>
                                             <th>Remarks</th>
                                             <th>Action</th>
                                          </tr>
                                       </thead>
                                       <tbody id="tbd">
                                       </tbody>
                                    </table> -->
                                                <!-- Table Ends -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->
                            </form>
                        </div>
                        <div class="tab-pane" id="account_details_tab">
                            <!-- <form class="was-validated account_bank_form" id="account_bank_form">
                        <div class="form-group row mb-2">
                            <label class="col-md-2 col-form-label" for="salary_mode">Salary Mode</label>
                            <div class="col-md-4">
                                <select name="salary_mode" id="salary_mode" class="select2 form-control" required><?php echo $salary_mode_options; ?> </select>
                            </div>
                        </div>
                    </form> -->
                            <form class="was-validated account_details_form" id="account_details_form">
                                <div class="form-group row mb-2">
                                    <label class="col-md-2 col-form-label" for="bank_status">Bank Status</label>
                                    <div class="col-md-4">
                                        <select name="bank_status" id="bank_status" class="select2 form-control" required onchange='get_salary_type();'><?php echo $bank_options; ?> </select>
                                    </div>
                                    <label class="col-md-2 col-form-label" for="salary_type">Salary Type</label>
                                    <div class="col-md-4">
                                        <select name="salary_type" id="salary_type" class="select2 form-control" required>
                                            <option value='AxisBank'>Axis Bank</option>
                                            <option value='NEFT'>NEFT</option>
                                            <option value='Cheque'>Cheque</option>
                                            <option value='Cash'>Cash</option>
                                            <option value='Hold'>Hold</option>
                                        </select>
                                    </div>
                                </div>
                                <!-- disabled -->
                                <div class="row">
                                    <div class="col-12">
                                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="accountant_name"> Accountant Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="accountant_name" name="accountant_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="account_no">Account Number</label>
                                            <div class="col-md-4">
                                                <input type="text" id="account_no" name="account_no" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="bank_name"> Bank name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="bank_name" name="bank_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="ifsc_code"> IFSC Code</label>
                                            <div class="col-md-4">
                                                <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="bank_contact_no"> Contact No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="bank_contact_no" onkeypress="return isNumber(event)" maxlength="10" name="bank_contact_no" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="bank_address"> Bank Address</label>
                                            <div class="col-md-4">
                                                <textarea id="bank_address" name="bank_address" class="form-control"></textarea>
                                            </div>
                                            <!--  <label class="col-md-2 col-form-label" for="bank_gst_no"> GST No</label>
                                 <div class="col-md-4">
                                    <input type="text" id="bank_gst_no" name="bank_gst_no" class="form-control" maxlength="15" value="" required> -->
                                            <!--  </div> -->
                                        </div>
                                        <!-- <div class="form-group row mb-2">
                                    <label class="col-md-2 col-form-label" for="is_active"> Active Status</label>
                                    <div class="col-md-4">
                                        <select name="is_active" id="is_active" class="select2 form-control" required>
                                            <?php //echo $active_status_options;
                                            ?>
                                        </select>
                                    </div>
                                </div> -->
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light staff_account_details_add_update_btn" onclick="staff_account_details_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row" id="my_account_table">
                                            <div class="col-12">
                                                <!-- Table Begiins -->
                                                <!-- <table id="staff_account_details_datatable" class="table dt-responsive nowrap w-100">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Bank Status</th>
                                             <th>Salary Type</th>
                                             <th>Acc. Name</th>
                                             <th>Acc. No</th>
                                             <th>Bank Name</th>
                                             <th>IFSC Code</th>
                                             <th>Ph. No</th>
                                             <th>Bank Address</th>
                                             <th>Action</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                    </table> -->
                                                <!-- Table Ends -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                </div>
                                <!-- end row -->
                            </form>
                        </div>
                        <div class="tab-pane" id="qualification_tab">
                            <form class="was-validated qualification_form" id="qualification_form">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="education_type"> Education Type</label>
                                            <div class="col-md-4">
                                                <input type="text" id="education_type" name="education_type" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="degree">Degree</label>
                                            <div class="col-md-4">
                                                <input type="text" id="degree" name="degree" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="college_name"> College Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="college_name" name="college_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="year_passing"> Year Of Passing</label>
                                            <div class="col-md-4">
                                                <input type="month" id="year_passing" name="year_passing" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="percentage"> Percentage</label>
                                            <div class="col-md-4">
                                                <input type="text" id="percentage" onkeypress="return isNumber(event)" maxlength="10" name="percentage" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="university"> University</label>
                                            <div class="col-md-4">
                                                <input type="text" id="university" name="university" class="form-control" value="" required>
                                            </div>
                                        </div>

                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="biometric_id"> Document Upload</label>
                                            <div class="col-md-4">
                                                <input type="file" multiple="multiple" id="test_file_qual" name="test_file_qual[]" class="form-control dropify">

                                                <!-- <img src="" id="doc_name"> -->

                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light qualification_add_update_btn" onclick="qualification_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row" id="my_qualification_table">
                                            <div class="col-12">
                                                <!-- Table Begiins -->
                                                <!-- <table id="qualification_datatable" class="table dt-responsive nowrap w-100">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Education Type</th>
                                             <th>Degree</th>
                                             <th>Document Image</th>
                                             <th>College Name</th>
                                             <th>Year of Passing</th>
                                             <th>Percentage</th>
                                             <th>University</th>
                                             <th>Action</th>
                                          </tr>
                                       </thead> 
                                       <tbody>
                                       </tbody>
                                    </table> -->
                                                <!-- Table Ends -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                </div>
                                <!-- end row -->
                            </form>
                        </div>
                        <div class="tab-pane" id="experience_tab">
                            <form class="was-validated experience_form" id="experience_form">
                                <div class="row">
                                    <div class="col-12">
                                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="company_name"> Company</label>
                                            <div class="col-md-4">
                                                <input type="text" id="company_names" name="company_names" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="designation_name">Designation</label>
                                            <div class="col-md-4">
                                                <input type="text" id="designation_name" name="designation_name" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="salary_amt"> Salary</label>
                                            <div class="col-md-4">
                                                <input type="text" id="salary_amt" onkeypress="return isNumber(event)" name="salary_amt" class="form-control" value="" onkeypress="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="join_month"> Joining Month</label>
                                            <div class="col-md-4">
                                                <input type="month" id="join_month" name="join_month" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="relieve_month"> Relieving Month</label>
                                            <div class="col-md-4">
                                                <input type="month" id="relieve_month" name="relieve_month" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="exp"> Experience</label>
                                            <div class="col-md-4">
                                                <input type="text" id="exp" name="exp" class="form-control" value="" onkeypress="return isNumber(event)" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="biometric_id"> Document Upload</label>
                                            <div class="col-md-4">
                                                <input type="file" id="test_file_exp" multiple name="test_file_exp[]" class="form-control dropify">

                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light experience_add_update_btn" onclick="experience_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row" id="my_experience_table">
                                            <div class="col-12">
                                                <!-- Table Begiins -->
                                                <!-- <table id="experience_datatable" class="table dt-responsive nowrap w-100">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Company</th>
                                             <th>Designation</th>
                                             <th>Document Image</th>
                                             <th>Salary</th>
                                             <th>Joining Month</th>
                                             <th>Relieving Month</th>
                                             <th>Experience</th>
                                             <th>Action</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                    </table> -->
                                                <!-- Table Ends -->
                                            </div>
                                        </div>
                                    </div>
                                    <!-- end col -->
                                </div>
                                <!-- end row -->
                            </form>
                        </div>
                        <div class="tab-pane" id="asset_tab">
                            <form class="was-validated asset_form" id="asset_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="asset_name"> Asset Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="asset_name" name="asset_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="item_no">Serial No/Item No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="item_no" name="item_no" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="qty"> Qty</label>
                                            <div class="col-md-4">
                                                <input type="text" id="qty" name="qty" class="form-control" onkeypress="return isNumber(event)" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="status"> Status</label>
                                            <div class="col-md-4">
                                                <select name="status" id="
                                       status" class="select2 form-control" required>
                                                    <?php echo $issued_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-6" style="color: red">Vehicle Details :</label>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="veh_reg_no"> Vehicle Reg. No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="veh_reg_no" name="veh_reg_no" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="license_mode"> License Mode</label>
                                            <div class="col-md-4">
                                                <select name="license_mode" id="
                                       license_mode" class="select2 form-control">
                                                    <?php echo $mode_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="dri_license_no"> License No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="dri_license_no" name="dri_license_no" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="license_validity"> License Validity</label>
                                            <div class="col-md-2">From
                                                <input type="date" id="valid_from" name="valid_from" class="form-control" value="">
                                            </div>
                                            <div class="col-md-2">To
                                                <input type="date" id="valid_to" name="valid_to" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="vehicle_type"> Vehicle Type</label>
                                            <div class="col-md-4">
                                                <input type="text" id="vehicle_type" name="vehicle_type" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="vehicle_company"> Vehicle Company</label>
                                            <div class="col-md-4">
                                                <input type="text" id="vehicle_company" name="vehicle_company" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="vehicle_owner"> Vehicle Owner</label>
                                            <div class="col-md-4">
                                                <input type="text" id="vehicle_owner" name="vehicle_owner" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="registration_year"> Year of Registration</label>
                                            <div class="col-md-4">
                                                <input type="date" id="registration_year" name="registration_year" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="rc_no"> RC No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="rc_no" name="rc_no" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="rc_validity"> Validity</label>
                                            <div class="col-md-2">From
                                                <input type="date" id="rc_validity_from" name="rc_validity_from" class="form-control" value="">
                                            </div>
                                            <div class="col-md-2">To
                                                <input type="date" id="rc_validity_to" name="rc_validity_to" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="ins_no"> Insurance No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="ins_no" name="ins_no" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="ins_validity"> Ins Validity</label>
                                            <div class="col-md-2">From
                                                <input type="date" id="ins_validity_from" name="ins_validity_from" class="form-control" value="">
                                            </div>
                                            <div class="col-md-2">To
                                                <input type="date" id="ins_validity_to" name="ins_validity_to" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light asset_details_add_update_btn" onclick="asset_details_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row" id="my_asset_table">
                                            <div class="col-12">
                                                <!-- Table Begiins -->
                                                <!-- <table id="asset_datatable" class="table dt-responsive nowrap w-100">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Asset Name</th>
                                             <th>Item No</th>
                                             <th>Qty</th>
                                             <th>Status</th>
                                             <th>Veh. Reg No</th>
                                             <th>Lins. No</th>
                                             <th>Lins.Mode</th>
                                             <th>Veh. Type</th>
                                             <th>Veh. Comp</th>
                                             <th>Veh. Owner</th>
                                             <th>Reg. Year</th>
                                             <th>Rc No</th>
                                             <th>Ins. No</th>
                                             <th>Action</th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                       </tbody>
                                    </table> -->
                                                <!-- Table Ends -->
                                            </div>
                                        </div>
                                        <li class="finish list-inline-item float-right mr-0">
                                 <button type="button" name="btn" id="createupdate_btn_finished" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn_finish" onclick="submit()">Finish</button>
                        <!-- <a href="javascript: void(0);" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn_finish">Finish</a> -->
                     </li>
                                    </div>
                                    <!-- end col -->
                                </div>
                                <!-- end row -->
                            </form>
                        </div>
                        <!-- <div class="tab-pane" id="salary_tab">
                     <form class="was-validated salary_form" id="salary_form">
                        <div class="row">
                           <div class="col-12">
                             <div class="form-group row ">
                                <label class="col-md-2" ></label>

                                <input type="hidden"  id="conveyance_default_value" name="conveyance_default_value" class="form-control" value="<?php echo $conveyance_default_value ?>" readonly required>
                                <input type="hidden" id="medical_default_value" name="medical_default_value" class="form-control" value="<?php echo $medical_default_value ?>" readonly required>
                                <input type="hidden" id="educational_default_value" name="educational_default_value" class="form-control" value="<?php echo $educational_default_value ?>" readonly required>
                                <input type="hidden"  id="pf_default_value" name="pf_default_value" class="form-control" value="<?php echo $pf_default_value ?>" readonly required>
                                <input type="hidden"  id="esi_default_value" name="esi_default_value" class="form-control" value="<?php echo $esi_default_value ?>" readonly required>
                                
                                 <label class="col-md-4" style="color: red">Per Month:</label> 
                                  
                                 <label class="col-md-2" style="color: red">Per Annum :</label>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="salary" style="color: red"> Gross</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)"  onKeyup="get_salary(this.value)" maxlength="6" id="salary" name="salary" class="form-control" value="<?php echo $salary ?>" required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)"  maxlength="6" id="annum_salary" name="annum_salary" class="form-control" value="<?php echo $annum_salary ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="basic_wages"> Basic </label>
                                 
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="basic_wages" name="basic_wages" class="form-control" value="<?php echo $basic_wages ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_basic_wages" name="annum_basic_wages" class="form-control" value="<?php echo $annum_basic_wages ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="hra"> HRA</label>
                                <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="hra" name="hra" class="form-control" value="<?php echo $hra ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_hra" name="annum_hra" class="form-control" value="<?php echo $annum_hra ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="conveyance"> Conveyance</label>
                                <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="conveyance" name="conveyance" class="form-control" value="<?php echo $conveyance ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_conveyance" name="annum_conveyance" class="form-control" value="<?php echo $annum_conveyance ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="medical_allowance"> Medical allowanceance</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="medical_allowance" name="medical_allowance" class="form-control" value="<?php echo $medical_allowance ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_medical_allowance" name="annum_medical_allowance" class="form-control" value="<?php echo $annum_medical_allowance ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="education_allowance"> Education allowanceance</label>
                                <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="education_allowance" name="education_allowance" class="form-control" value="<?php echo $education_allowance ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_education_allowance" name="annum_education_allowance" class="form-control" value="<?php echo $annum_education_allowance ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="other_allowance"> Other allowanceance</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="other_allowance" name="other_allowance" class="form-control" value="<?php echo $other_allowance ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_other_allowance" name="annum_other_allowance" class="form-control" value="<?php echo $annum_other_allowance ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="pf"> PF</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="pf" name="pf" class="form-control" value="<?php echo $pf ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_pf" name="annum_pf" class="form-control" value="<?php echo $annum_pf ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="esi"> ESI</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="esi" name="esi" class="form-control" value="<?php echo $esi ?>" readonly  required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_esi" name="annum_esi" class="form-control" value="<?php echo $annum_esi ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="total_deduction"> Total Deduction</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="total_deduction" name="total_deduction" class="form-control" value="<?php echo $total_deduction ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_total_deduction" name="annum_total_deduction" class="form-control" value="<?php echo $annum_total_deduction ?>" readonly required>
                                 </div>
                              </div>
                               <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="net_salary" style="color: red"> Net Salary</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="net_salary" name="net_salary" class="form-control" value="<?php echo $net_salary ?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_net_salary" name="annum_net_salary" class="form-control" value="<?php echo $annum_net_salary ?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="purformance_allowance"> Purformance allowance </label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" onkeyup="get_salary(salary.value)" maxlength="6" id="purformance_allowance" name="purformance_allowance" class="form-control" value="<?php echo $purformance_allowance ?>"  >
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_purformance_allowance" name="annum_purformance_allowance" class="form-control" value="<?php echo $annum_purformance_allowance ?>" readonly >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="ctc" style="color: red"> CTC </label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="ctc" name="ctc" class="form-control" value="<?php echo $ctc ?>"readonly required  >
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_ctc" name="annum_ctc" class="form-control" value="<?php echo $annum_ctc ?>" readonly required>
                                 </div>
                              </div>
                            </div>
                        </div>
                     </form>
                  </div>
                  <div class="tab-pane" id="relieve_tab">
                     <form class="was-validated relieve_form" id="relieve_form">
                        <div class="row">
                           <div class="col-12">
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="relieve_status"> Status</label>
                                 <div class="col-md-4">
                                    <select name="relieve_status" id="relieve_status" class="select2 form-control" ><?php echo $relieve_options; ?>
                                    </select> 
                                 </div>
                                 <label class="col-md-2 col-form-label" for="relieve_date"> Relieve Date</label>
                                 <div class="col-md-4">
                                    <input  type="date" id="relieve_date" name="relieve_date" class="form-control" value="<?php echo $relieve_date; ?>"  >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="relieve_reason"> Relieve Reason</label>
                                 <div class="col-md-4">
                                    <textarea id="relieve_reason" name="relieve_reason" class="form-control"><?php echo $relieve_reason; ?></textarea>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </form>
                  </div> -->
                        <!-- <ul class="list-inline mb-0 pager wizard"> -->
                            <!-- <li class="previous list-inline-item disabled">
                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                        </li>  -->
                            <?php 
                            // echo btn_cancel($btn_cancel); 
                            ?>
                            <!-- <li class="next list-inline-item float-right mr-0"> -->
                                <!-- <a href="javascript: void(0);" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn" onclick="staff_detail_creation_cu()"><?php echo $btn_text; ?> & Continue</a> -->
                                <!-- <button type="button" name="btn" id="btn" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn" onclick="staff_detail_creation_cu(unique_id.value); btn_hide()"><?php echo $btn_text; ?> & Continue</button>
                            </li> -->
                            
                        <!-- </ul> -->
                    </div>
                    <!-- tab-content -->
                </div>
                <!-- end #staffcreatewizard-->
                <!-- </form> -->
            </div>
            <!-- end card-body -->
        </div>
        <!-- end card-->
    </div>
    <!-- end col -->
</div>
<!-- Vendor js -->
<?php //echo 'dfadfa'.$js_css_file_comment; 
?>

<script src="../../assets/js/vendor.min.js<?php echo $js_css_file_comment; ?>"></script>

<!-- App js -->
<script src="../../assets/js/app.min.js<?php echo $js_css_file_comment; ?>"></script>

<!-- Common to all -->
<!-- Flatpicker -->
<script src="../../assets/libs/flatpickr/flatpickr.min.js<?php echo $js_css_file_comment; ?>"></script>

<?php if (session_id() and ($user_id)) { ?>
    <!-- Plugins js-->
    <script src="../../assets/libs/twitter-bootstrap-wizard/jquery.bootstrap.wizard.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- Init js-->
    <script src="../../assets/js/pages/form-wizard.init.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- Datatables js-->
    <!-- <script src="../../assets/libs/datatables/datatables.min.js<?php echo $js_css_file_comment; ?>"></script> -->
    <script src="../../assets/libs/datatables.net/js/jquery.dataTables.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-bs4/js/dataTables.bootstrap4.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-buttons-bs4/js/buttons.bootstrap4.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/jszip/jszip.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.html5.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.print.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.flash.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net/js/dataTables.fixedColumns.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js<?php echo $js_css_file_comment; ?>"></script>
    <script src="../../assets/libs/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js<?php echo $js_css_file_comment; ?>"></script>
    <!-- <script src="../../assets/libs/datatables.net-buttons/js/buttons.html5.min.js<?php echo $js_css_file_comment; ?>"></script> -->

    <!-- Select2 js-->
    <script src="../../assets/libs/select2/js/select2.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- Datatables Responsive js-->
    <!-- <script src="../../assets/libs/datatables-responsive/js/responsive.bootstrap4.min.js<?php echo $js_css_file_comment; ?>"></script> -->

    <!-- Sweetalert2 -->
    <script src="../../assets/libs/sweetalert2/sweetalert2.all.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- Drop Zone -->
    <!-- <script src="../../assets/libs/dropzone/dist/min/dropzone.min.js<?php echo $js_css_file_comment; ?>"></script> -->

    <!-- Dropify -->
    <script src="../../assets/libs/dropify/dist/js/dropify.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- Auto complete -->
    <script src="../../assets/libs/autocomplete/js/autocomplete.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- jQuery Multiselect -->
    <script src="../../assets/libs/jquery_multiselect/jquery.multiselect.js<?php echo $js_css_file_comment; ?>"></script>

    <?php if ($folder_name_org == 'dashboard') { ?>

        <!-- Morris Chart JS -->
        <script src="../../assets/libs/morris/morris.min.js<?php echo $js_css_file_comment; ?>"></script>

        <!-- Rapheal Chart JS -->
        <script src="../../assets/libs/raphael/raphael.min.js<?php echo $js_css_file_comment; ?>"></script>

        <!-- Chart JS -->
        <script src="../../assets/libs/chart.js/Chart.bundle.min.js<?php echo $js_css_file_comment; ?>"></script>
        <script src="../../assets/libs/chart.js/chartjs-gauge.js<?php echo $js_css_file_comment; ?>"></script>

        <!-- AM Chart JS -->
        <script src="../../assets/libs/amcharts4/core.js<?php echo $js_css_file_comment; ?>"></script>
        <script src="../../assets/libs/amcharts4/charts.js<?php echo $js_css_file_comment; ?>"></script>
        <script src="../../assets/libs/amcharts4/themes/animated.js<?php echo $js_css_file_comment; ?>"></script>

    <?php } ?>
    <!-- This file only js Functions -->
    <script type="text/javascript" src="<?php echo 'folders/' . $folder_name_org . "/" . $folder_name_org; ?>.js<?php echo $js_css_file_comment; ?>"></script>

<?php } else { ?>

    <!-- Particlesjs -->
    <script src="../../assets/libs/particlesjs/js/lib/particles.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- Sweetalert2 -->
    <script src="../../assets/libs/sweetalert2/sweetalert2.all.min.js<?php echo $js_css_file_comment; ?>"></script>

    <!-- This file only js Functions -->
    <script type="text/javascript" src="<?php echo 'folders/' . $folder_name_org . "/" . $folder_name_org; ?>.js<?php echo $js_css_file_comment; ?>"></script>

<?php } ?>

<script type="text/javascript" src='https://maps.google.com/maps/api/js?key=AIzaSyCEDMbFnE7uOxsVb5nzzZGTMImFZ_Fu7Ko&libraries=geometry'></script>

<!-- Location Picker -->
<script src="../../assets/libs/jquery_locationpicker/locationpicker.jquery.min.js<?php echo $js_css_file_comment; ?>"></script>

<!-- Custom js-->
<!-- <script src="../../assets/js/common.js<?php echo $js_css_file_comment; ?>"></script> -->
<script src="../../assets/js/common.js"></script>

<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> -->


<!-- end col -->
</div>
<script>
     confirm_delete = function(msg) {
        return sweetalert(msg); // <--- return the swal call which returns a promise
    };

    // $("#my_tab").click(function() {
    //     alert("hii");
    //     var is_form = form_validity_check("was-validated");
    //     if (!is_form) {
    //         sweetalert("form_alert");
    //     }else{
    //         $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
    //         // staff_detail_creation_cu();
    //     }
    // });
    // //   Sweet Alert Delete Confirmation Function
   
    // var contact_person_tableid = "contact_person_datatable";
    // var delivery_details_tableid = "dependent_details_datatable";
    // var qualification_datatable_tableid = "qualification_datatable";
    // var experience_datatable_tableid = "experience_datatable";
    // var account_details_tableid = "staff_account_details_datatable";
    // var asset_tableid = "asset_datatable";

    // var form_name = 'staff';
    // var form_header = '';
    // var form_footer = '';
    // var table_name = '';
    // var table_id = 'staff_datatable';
    // var action = "datatable";
    $("#createupdate_btn_finished").click(function() {
        // alert("hii");
         var url = '';
         var msg        = "create";
        sweetalert(msg, url);
        window.reload();
        
    });
    
    
    function my_tab_check(){
        alert("hii");
        // var is_form = form_validity_check("valdity");
        // if (!is_form) {
        //     sweetalert("form_alert");
        // }else{
        //     $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
        //     // staff_detail_creation_cu();
        // }
    }
        // alert("hii");
        // var is_form = form_validity_check("was-validated");
        // if (!is_form) {
        //     sweetalert("form_alert");
        // }else{
        //     $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
        //     staff_detail_creation_cu();
        // }
    // }
    // $("#dep_tab").click(function() {
    //     alert("hii");
    //     $('#staff_profile_form').change(){
    //     var is_form = form_validity_check("was-validated");
    //     if (!is_form) {
    //         sweetalert("form_alert");
    //     }else{
    //         staff_detail_creation_cu();
    //     }
    // }
    // });

   
    function staff_detail_creation_cu(unique_id = "") {
        // alert("hii");
        var internet_status = is_online();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }


        var is_form = form_validity_check("valdity");
        
         if (is_form) {
       
        var data = $(".valdity").serialize();
        data += "&unique_id=" + unique_id + "&action=createupdate";

        var ajax_url = 'crud.php'
        //   var url      = sessionStorage.getItem("list_link");

        // console.log(data);
        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function() {
                $(".createupdate_btn").attr("disabled", "disabled");
                // $(".createupdate_btn").text("Loading...");
            },
            success: function(data) {

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;
                // alert(msg);
                if (!status) {
					url 	= '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
				} else {
					if (msg=="already") {
						// Button Change Attribute
						url 		= '';

						$(".createupdate_btn").removeAttr("disabled",true);
						// if (unique_id) {
						// 	$(".createupdate_btn").text("Update");
						// } else {
						// 	$(".createupdate_btn").text("Save");
						// }
                        // sweetalert(msg,url);
					}else if (msg =="create") {
						// Button Change Attribute
						url 		= '';

						// $(".createupdate_btn").removeAttr("disabled","disabled");
						// if (unique_id) {
						// 	$(".createupdate_btn").text("Update");
						// } else {
						// 	$(".createupdate_btn").text("Save");
						// }
                        // window.reload();
                        
                // $('#dependentdetails_tab').attr("disabled", false);
                // $('#account_details_tab').attr("disabled", false);
                // $('#qualification_tab').attr("disabled", false);
                // $('#experience_tab').attr("disabled", false);
                // $('#asset_tab').attr("disabled", false);
        //         var dep_tab = document.getElementById('dep_tab');
        // var acc_tab = document.getElementById('acc_tab');
        // var qua_tab = document.getElementById('qua_tab');
        // var exp_tab = document.getElementById('exp_tab');
        // var ass_tab = document.getElementById('ass_tab');

        // dep_tab.setAttribute("class", "enabled");
        // acc_tab.setAttribute("class", "enabled");
        // qua_tab.setAttribute("class", "enabled");
        // exp_tab.setAttribute("class", "enabled");
        // ass_tab.setAttribute("class", "enabled");
				
					}
                //     $(".createupdate_btn").attr("disabled", false);
                // // document.getElementById("staff_profile_form").reset();
                // $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
                // sweetalert(msg,url);
                sweetalert(msg,url);
                $(".createupdate_btn").attr("disabled", false);
                // document.getElementById("staff_profile_form").reset();
                // $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');

                $("ul").tabs("option", "active", 2);


				}
                
                
                // $('#staff_profile_form').reset();
                
			},
			// error 		: function(data) {
			// 	alert("Network Error");
			// }
		});
    // }

    } else {
        sweetalert("form_alert");
    }
}
                
                // if (!success) {
                //     sweetalert("form_alert");
                //                         // console.log(success);
                //                         $('#staffcreatewizard').find("a[href*='officialdetails_tab']").trigger('click');
                //                         // $('#staffcreatewizard').find("a[href*='contactperson_tab']").removeClass('active');
                //                         // $('#staffcreatewizard').find("a[href*='profile_tab']").addClass('active');
                //                     } else {
                //                         // console.log(success);
                //                         $("#staff_unique_id").val(cus_id);
                //                         $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
                //                         // $('#staffcreatewizard').find("a[href*='profile_tab']").removeClass('active');
                //                         // $('#staffcreatewizard').find("a[href*='contactperson_tab']").addClass('active');
                //                     },
            
        
        // }
    // }
           
            // });                       
            // }
        // }
        // });
    // }
    //             
               
    //             // $("#staff_unique_id").val(cus_id);
    //             $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
                
    //             // location.reload();
    //         },
       
    //         error: function(data) {
    //             alert("Network Error");
    //         }
    //     });

    // }else{
    //     var url = '';
    //     var msg = 'form_alert';
    //         sweetalert(msg,url);
    //         $('#staffcreatewizard').find("a[href*='officialdetails_tab']").trigger('click');
    //     }
    // }
    // return event.preventDefault(), event.stopPropagation(), !1;
        //  } else {
        //      sweetalert("form_alert");
        //  }
    // }
    
    
        $('#dep_tab').click(function(){
        // alert("hii");
        var is_form = form_validity_check("valdity");
        if(!is_form){
            
              sweetalert("form_alert");

    }else{
        $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
    }
});
    $(document).ready(function() {
        
        // alert("hii");
        // var dep_tab = document.getElementById('dep_tab');
        // var acc_tab = document.getElementById('acc_tab');
        // var qua_tab = document.getElementById('qua_tab');
        // var exp_tab = document.getElementById('exp_tab');
        // var ass_tab = document.getElementById('ass_tab');

        // $('#loadMore li dep_tab').setAttribute("class", "disabled");
        // acc_tab.setAttribute("class", "disabled");
        // qua_tab.setAttribute("class", "disabled");
        // exp_tab.setAttribute("class", "disabled");
        // ass_tab.setAttribute("class", "disabled");
        

        //    anchor.setAttribute("style", "color: black;");
        // var a = $("#dep_tab");
        // alert(a);
        // $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").disable();
        // $("#nav-item li #dep_tab").attr("disabled","disabled");
        // $('#dep_tab').attr('disabled', true);
        // $("#dep_tab").prop("disabled",false);
        // $('.dep_tab').inactive();
                // $('.acc_tab').addClass("disabled");
                // $('.qua_tab').addClass("disabled");
                // $('.exp_tab').addClass("disabled");
                // $('.ass_tab').addClass("disabled");
            get_employee_id();
        // Datatable Initialize
        datatable_init_based_on_prev_state();
        // var unique_id = $("#unique_id").val();
        // alert(unique_id);
        // if (unique_id == '') {
            
        // }

        $("#excel_export").click(function() {
            var staff_status = $('#staff_status').val();
            var company_name = $('#company_name').val();
            window.location = "folders/staff/excel.php?staff_status=" + staff_status + "&company_name=" + company_name;
        });
        // Form wizard Functions
        $('#staffcreatewizard').bootstrapWizard({
            onTabShow: function(tab, navigation, index) {
                var staff_unique_id = $("#staff_unique_id").val();
                var unique_id = $("#unique_id").val();
                if (index != 0) {
                    if (!staff_unique_id) {
                        sweetalert("custom", '', '', 'Create Staff Details');
                        $('#staffcreatewizard').find("a[href*='officialdetails_tab']").trigger('click');
                        return event.preventDefault(), event.stopPropagation(), !1;
                    }
                }
                // console.log(index);
                var $total = navigation.find('li').length;
                var $current = index + 1;
                var $percent = ($current / $total) * 100;
                $('#staffcreatewizard').find('.bar').css({
                    width: $percent + '%'
                });
                // If it's the last tab then hide the last button and show the finish instead
                if ($current >= $total) {
                    $('#staffcreatewizard').find('.pager .next').hide();
                    $('#staffcreatewizard').find('.pager .finish').show();
                    $('#staffcreatewizard').find('.pager .finish').removeClass('disabled');
                    // unique_id    = $(".finish").data("unique-id");
                } else {
                    $('#staffcreatewizard').find('.pager .next').show();
                    $('#staffcreatewizard').find('.pager .finish').hide();
                }
                if ((index != 0) && (index != 5)) {
                    $(".createupdate_btn").text("Next");
                } else if (index == 5) {
                    $(".createupdate_btn").text("Next");
                    var form_class = "salary_form";
                    var is_form = form_validity_check(form_class);
                    if (!is_form) {
                        sweetalert("form_alert");
                        console.log(is_form);
                        var sucs = "false";
                        if (sucs == "false") {
                            console.log(sucs);
                            $('#staffcreatewizard').find("a[href*='salary_tab']").trigger('click');
                        }
                    } else {
                        var data = $("." + form_class).serialize();
                        data += "&unique_id=" + unique_id + "&action=salarycreateupdate" + "&staff_unique_id=" + staff_unique_id;
                        var ajax_url = 'crud.php'
                        var url = sessionStorage.getItem("list_link");
                        // console.log(data);
                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: data,
                            success: function(data) {
                                var obj = JSON.parse(data);
                                var msg = obj.msg;
                                var status = obj.status;
                                var error = obj.error;
                                var cus_id = obj.staff_unique_id;
                                url = '';
                                var success = false;
                                if (!status) {
                                    $(".createupdate_btn").text("Error");
                                    console.log(error);
                                } else {
                                    success = true;
                                    if (msg == "already") {
                                        // Button Change Attribute
                                        url = '';
                                        success = false;
                                    }
                                    sweetalert(msg, url);
                                }
                            },
                            error: function(data) {
                                alert("Network Error");
                                // return false;
                            }
                        });
                        $('#staffcreatewizard').find("a[href*='relieve_tab']").trigger('click');
                    }
                }
                switch (index) {
                    case 0:
                        break;
                    case 1:
                        sub_list_datatable(delivery_details_tableid);
                        break;
                    case 2:
                        sub_list_datatable(account_details_tableid);
                        break;
                    case 3:
                        sub_list_datatable(qualification_datatable_tableid);
                        break;
                    case 4:
                        sub_list_datatable(experience_datatable_tableid);
                        break;
                    case 5:
                        sub_list_datatable(asset_tableid);
                        break;
                    default:
                        break;
                }
            },
            onclick: function(t, r, index) {
                // alert("hii");
                if (index == 1) {
                    var form_class = "staff_profile_form";
                    var is_form = form_validity_check(form_class);
                    var unique_id = $("#unique_id").val();
                    if (!is_form) {
                        sweetalert("form_alert");
                        return event.preventDefault(), event.stopPropagation(), !1;
                    } else {
                        var data = $("." + form_class).serialize();
                        data += "&unique_id=" + unique_id + "&action=createupdate";
                        var ajax_url = 'crud.php'
                        var url = sessionStorage.getItem("list_link");
                        // console.log(data);
                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: data,
                            beforeSend: function() {
                                $(".createupdate_btn").addClass("disabled");
                                $(".createupdate_btn").text("Loading...");
                            },
                            success: function(data) {
                                var obj = JSON.parse(data);
                                var msg = obj.msg;
                                var status = obj.status;
                                var error = obj.error;
                                var cus_id = obj.staff_unique_id;
                                url = '';
                                var success = false;
                                if (!status) {
                                    $(".createupdate_btn").text("Error");
                                    console.log(error);
                                } else {
                                    success = true;
                                    if (msg == "already") {
                                        // Button Change Attribute
                                        url = '';
                                        success = false;
                                    }
                                    file_upload(cus_id);
                                    $(".createupdate_btn").removeClass("disabled", "disabled");
                                    if (unique_id) {
                                        $(".createupdate_btn").text("Update & Continue");
                                    } else {
                                        $(".createupdate_btn").text("Save & Continue");
                                    }
                                    sweetalert(msg, url);
                                    if (!success) {
                                        console.log(success);
                                        $('#staffcreatewizard').find("a[href*='officialdetails_tab']").trigger('click');
                                        // $('#staffcreatewizard').find("a[href*='contactperson_tab']").removeClass('active');
                                        // $('#staffcreatewizard').find("a[href*='profile_tab']").addClass('active');
                                    } else {
                                        console.log(success);
                                        $("#staff_unique_id").val(cus_id);
                                        $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
                                        // $('#staffcreatewizard').find("a[href*='profile_tab']").removeClass('active');
                                        // $('#staffcreatewizard').find("a[href*='contactperson_tab']").addClass('active');
                                    }
                                }
                            },
                            error: function(data) {
                                alert("Network Error");
                                // return false;
                            }
                        });
                        return event.preventDefault(), event.stopPropagation(), !1;
                    }
                }
            },
            onTabClick: function(tab, navigation, index) {
                alert();
                // return false;
                // return event.preventDefault(), event.stopPropagation(), !1;
            }
        });
        $('#staffcreatewizard .finish').click(function() {
            //alert('Finished!, Starting over!');
            var staff_unique_id = $("#staff_unique_id").val();
            var unique_id = $("#unique_id").val();
            var form_class = "relieve_form";
            var data = $("." + form_class).serialize();
            data += "&unique_id=" + unique_id + "&action=relievecreateupdate";
            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");
            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(data) {
                    var obj = JSON.parse(data);
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;
                    var cus_id = obj.staff_unique_id;
                    url = '';
                    var success = false;
                    if (!status) {
                        $(".createupdate_btn").text("Error");
                        console.log(error);
                    } else {
                        success = true;
                        if (msg == "already") {
                            // Button Change Attribute
                            url = '';
                            success = false;
                        }
                        sweetalert(msg, url);
                    }
                },
                error: function(data) {
                    alert("Network Error");
                    // return false;
                }
            });
            var url = sessionStorage.getItem("list_link");
            sweetalert("create", url);
        });
        //premises_check();
    });

    function premises_check() {

        var status = $("input[name='premises_status']:checked").val();
        if (status != 0) {
            $("#staff_branch").attr("required", "required");

            $(".premises_in_div").removeClass("d-none");

        } else {

            $("#staff_branch").removeAttr("required", "required");
            $("#staff_branch").val("");

            $(".premises_in_div").addClass("d-none");

        }
    }

    function datatable_init_based_on_prev_state() {
        // Data Table Filter Function Based ON Previous Search
        var staff_status = sessionStorage.getItem("staff_status");
        var filter_action = sessionStorage.getItem("expense_action");


        if (!staff_status) {
            staff_status = $("#staff_status").val();
        } else {
            $("#staff_status").val(staff_status);
        }

        if (!filter_action) {
            filter_action = 0;
        }

        // Datatable Filter Data
        var filter_data = {
            "status": staff_status,
            "filter_action": filter_action
        };

        // var table_id     = "follow_up_call_datatable";
        init_datatable(table_id, form_name, action, filter_data);
    }


    function init_datatable(table_id = '', form_name = '', action = '', filter_data = '') {
        var table = $("#" + table_id);
        var data = {
            "action": action,
        };
        data = {
            ...data,
            ...filter_data
        };
        var ajax_url = 'crud.php'

        var datatable = table.DataTable({
            ordering: true,
            searching: true,
            "searching": true,
            "ajax": {
                url: ajax_url,
                type: "POST",
                data: data
            },

        });
    }


    // Get State Names Based On Country Selection
    function get_states(country_id = "") {

        var ajax_url = 'crud.php'

        if (country_id) {
            var data = {
                "country_id": country_id,
                "action": "states"
            }

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(data) {

                    if (data) {
                        $("#pre_state").html(data);
                    }
                }
            });
        }
    }

    // Get city Names Based On State Selection
    function get_cities(state_id = "") {


        var ajax_url = 'crud.php'

        if (state_id) {
            var data = {
                "state_id": state_id,
                "action": "cities"
            }

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(data) {

                    if (data) {
                        $("#pre_city").html(data);
                    }
                }
            });
        }
    }

    // Get permanent address State Names Based On Country Selection
    function get_perm_states(country_id = "") {
        var ajax_url = 'crud.php'

        if (country_id) {
            var data = {
                "country_id": country_id,
                "action": "perm_states"
            }

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(data) {

                    if (data) {
                        $("#perm_state").html(data);

                        var edit_state_id = $("#edit_perm_state_id").val();

                        if (edit_state_id) {
                            $("#perm_state").val(edit_state_id).trigger('change');

                            $("#edit_perm_state_id").val('');
                        }
                    }
                }
            });
        }
    }


    // Get permanent address State Names Based On Country Selection
    function get_employee_id() {
        var ajax_url = 'crud.php'

        var data = {
            "action": "employee_id"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(data) {
// alert(data);
                if (data) {
                    $("#employee_id").html(data);

                    $("#staff_id").val(data);
                }
            }
        });
    }

    // Get city Names Based On State Selection
    function get_perm_cities(state_id = "") {

        var ajax_url = 'crud.php'

        if (state_id) {
            var data = {
                "state_id": state_id,
                "action": "perm_cities"
            }

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(data) {

                    if (data) {
                        $("#perm_city").html(data);

                        var edit_city_id = $("#edit_perm_city_id").val();

                        if (edit_city_id) {

                            $("#perm_city").val(edit_city_id).trigger('change');

                            $("#edit_perm_city_id").val('');
                        }
                    }
                }
            });
        }
    }


    function sub_list_datatable(table_id = "", form_name = "", action = "") {
        //  alert("test");
        var staff_unique_id = $("#staff_unique_id").val();

        //  var table = $("#" + table_id);
        var table = table_id;

        //  var data = {
        //      "staff_unique_id": staff_unique_id,
        //      "action": table_id,
        //  };
        //  alert(data);
        var data1 = $("#staff_unique_id").val();
        var data2 = table_id;
        var ajax_url = 'crud.php'

        var datatable = table.DataTable({
            ordering: true,
            searching: true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
            "ajax": {
                url: ajax_url,
                // url: 'crud.php?staff_unique_id='+staff_unique_id+'&action='+table_id;
                type: "POST",
                data: {
                    data1,
                    data2
                },
            }
        });
        return datatable;
    }

    // Invoice Details ADD & UPDATE
    function asset_details_add_update(unique_id = "") { // au = add,update

        var internet_status = is_online();

        var staff_unique_id = $("#staff_unique_id").val();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }

        var is_form = form_validity_check("asset_form");

        console.log(is_form);

        if (is_form) {

            var data = $(".asset_form").serialize();
            data += "&staff_unique_id=" + staff_unique_id;
            data += "&unique_id=" + unique_id + "&action=asset_details_add_update";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".asset_details_add_update_btn").attr("disabled", "disabled");
                    // $(".asset_details_add_update_btn").text("Loading...");
                },
                success: function(data) {
                    var obj = JSON.parse(data);
                    $('#my_asset_table').html(obj.data);
                    $(".asset_details_add_update_btn").attr("disabled", false);
                    $("#asset_form")[0].reset();
                }
            });
        }

        //             var obj = JSON.parse(data);
        //             var msg = obj.msg;
        //             var status = obj.status;
        //             var error = obj.error;

        //             if (!status) {
        //                 $(".asset_details_add_update_btn").text("Error");
        //                 console.log(error);
        //             } else {
        //                 if (msg !== "already") {
        //                     form_reset("asset_form");
        //                 }
        //                 $(".asset_details_add_update_btn").removeAttr("disabled", "disabled");
        //                 if (unique_id && msg == "already") {
        //                     $(".asset_details_add_update_btn").text("Update");
        //                 } else {
        //                     $(".asset_details_add_update_btn").text("Add");
        //                     $(".asset_details_add_update_btn").attr("onclick", "asset_details_add_update('')");
        //                 }
        //                 // Init Datatable
        //                 sub_list_datatable("asset_datatable");
        //             }
        //             sweetalert(msg, url);
        //         },
        //         error: function(data) {
        //             alert("Network Error");
        //         }
        //     });
        // } else {
        //     sweetalert("form_alert");
        // }
    }

    function asset_details_edit(unique_id = "") {
        if (unique_id) {
            var data = "unique_id=" + unique_id + "&action=asset_details_edit";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".asset_details_add_update_btn").attr("disabled", "disabled");
                    // $(".asset_details_add_update_btn").text("Loading...");
                },
                success: function(data) {

                    var obj = JSON.parse(data);
                    var data = obj.data;
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;

                    if (!status) {
                        $(".asset_details_add_update_btn").text("Error");
                        console.log(error);
                    } else {
                        console.log(obj);
                        var asset_name = data.asset_name;
                        var item_no = data.item_no;
                        var qty = data.qty;
                        var status = data.status;
                        var veh_reg_no = data.veh_reg_no;
                        var license_mode = data.license_mode;
                        var dri_license_no = data.dri_license_no;
                        var valid_from = data.valid_from;
                        var valid_to = data.valid_to;
                        var vehicle_type = data.vehicle_type;
                        var vehicle_company = data.vehicle_company;
                        var vehicle_owner = data.vehicle_owner;
                        var registration_year = data.registration_year;
                        var rc_no = data.rc_no;
                        var rc_validity_from = data.rc_validity_from;
                        var rc_validity_to = data.rc_validity_to;
                        var ins_no = data.ins_no;
                        var ins_validity_from = data.ins_validity_from;
                        var ins_validity_to = data.ins_validity_to;

                        $("#asset_name").val(asset_name);
                        $("#item_no").val(item_no);
                        $("#qty").val(qty);
                        $("#status").val(status).trigger("change");
                        $("#veh_reg_no").val(veh_reg_no);
                        $("#license_mode").val(license_mode).trigger("change");
                        $("#dri_license_no").val(dri_license_no);
                        $("#valid_from").val(valid_from);
                        $("#valid_to").val(valid_to);
                        $("#vehicle_type").val(vehicle_type);
                        $("#vehicle_company").val(vehicle_company);
                        $("#registration_year").val(registration_year);
                        $("#vehicle_owner").val(vehicle_owner);
                        $("#rc_no").val(rc_no);
                        $("#rc_validity_from").val(rc_validity_from);
                        $("#rc_validity_to").val(rc_validity_to);
                        $("#ins_no").val(ins_no);
                        $("#ins_validity_from").val(ins_validity_from);
                        $("#ins_validity_to").val(ins_validity_to);

                        // Button Change 
                        $(".asset_details_add_update_btn").removeAttr("disabled", "disabled");
                        $(".asset_details_add_update_btn").text("Update");
                        $(".asset_details_add_update_btn").attr("onclick", "asset_details_add_update('" + unique_id + "')");
                    }
                },
                error: function(data) {
                    alert("Network Error");
                }
            });
        }
    }

    function asset_details_delete(unique_id = "", staff_unique_id = "") {
        if (unique_id) {

            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");

            confirm_delete('delete')
                .then((result) => {
                    if (result.isConfirmed) {

                        // alert(unique_id);
                        var action = "asset_details_delete";


                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: {
                                unique_id: unique_id,
                                staff_unique_id: staff_unique_id,
                                action: action,
                            },
                            success: function(data) {

                                var obj = JSON.parse(data);
                                var msg = obj.msg;

                                $('#my_asset_table').html(obj.data);
                                sweetalert(msg);
                            }
                        });

                    } else {
                        // alert("cancel");
                    }
                });
        }
    }


    // Invoice Details ADD & UPDATE
    function dependent_details_add_update(unique_id = "") { // au = add,update

        var internet_status = is_online();

        var staff_unique_id = $("#staff_unique_id").val();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }

        var is_form = form_validity_check("dependent_details_form");

        //  console.log(is_form);

        if (is_form) {

            var data = $(".dependent_details_form").serialize();
            data += "&staff_unique_id=" + staff_unique_id;
            data += "&unique_id=" + unique_id + "&action=dependent_details_add_update";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".dependent_details_add_update_btn").attr("disabled", "disabled");
                    // $(".dependent_details_add_update_btn").text("Loading...");
                },
                success: function(data) {
                    // alert(data);
                    var obj = JSON.parse(data);
                    $('#my_dependentent_table').html(obj.data);
                    $(".dependent_details_add_update_btn").attr("disabled", false);
                    $("#dependent_details_form")[0].reset();
                }
            });
        }
        //  var msg = obj.msg;
        //  var status = obj.status;
        //  var error = obj.error;

        //  if (!status) {
        //      $(".dependent_details_add_update_btn").text("Error");
        //      console.log(error);
        //  } else {
        //      if (msg !== "already") {
        //          form_reset("dependent_details_form");
        //      }
        //      $(".dependent_details_add_update_btn").removeAttr("disabled", "disabled");
        //      if (unique_id && msg == "already") {
        //          $(".dependent_details_add_update_btn").text("Update");
        //      } else {
        //          $(".dependent_details_add_update_btn").text("Add");
        //          $(".dependent_details_add_update_btn").attr("onclick", "dependent_details_add_update('')");
        //      }
        // Init Datatable
        //   sub_list_datatable("dependent_details_datatable");
        //    if (data && data.length > 0) {
        //       alert(data);
        //      data = $.parseJSON(data);

        //      var tr = '';
        //      $.each(data.data, function(i, item) {
        //          var k = i + 1;
        //          alert(data);


        //         tr += '<tr><td width="5%">' + k + '</td><td width="15%">' + item.relationship + '</td><td>' + item.name + '</td><td width="25%">' + item.gender + '</td><td width="25%">' + item.date_of_birth + '</td><td width="20%">' + item.aadhar_no + '</td><td width="20%">' + item.emer_contact_no + '</td><td width="25%">' + item.emer_contact_person + '</td><td width="25%">' + item.existing_illness + '</td><td width="20%">'+item.existing_insurance+'</td><td width="20%">'+item.illness_description+'</td><td width="20%">'+item.insurance_no+'</td><td width="20%">'+item.occupation+'</td><td width="20%">'+item.physically_challenged+'</td><td width="20%">'+item.remarks+'</td><td width="20%">'+item.school+'</td><td width="20%">'+item.standard+'</td></tr>';



        //      });
        //  }




        //              }
        //              sweetalert(msg, url);
        //          },
        //          error: function(data) {
        //              alert("Network Error");
        //          }
        //      });
        //  } else {
        //      sweetalert("form_alert");
        //  }
    }

    function dependent_details_edit(unique_id = "") {
        if (unique_id) {
            var data = "unique_id=" + unique_id + "&action=dependent_details_edit";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".dependent_details_add_update_btn").attr("disabled", "disabled");
                    // $(".dependent_details_add_update_btn").text("Loading...");
                },
                success: function(data) {

                    var obj = JSON.parse(data);
                    var data = obj.data;
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;

                    if (!status) {
                        $(".dependent_details_add_update_btn").text("Error");
                        console.log(error);
                    } else {
                        console.log(obj);
                        var relationship = data.relationship;
                        var name = data.name;
                        var gender = data.gender;
                        var aadhar_no = data.aadhar_no;
                        var occupation = data.occupation;
                        var emer_contact_person = data.emer_contact_person;
                        var emer_contact_no = data.emer_contact_no;
                        var standard = data.standard;
                        var school = data.school;
                        var existing_illness = data.existing_illness;
                        var existing_insurance = data.existing_insurance;
                        var illness_description = data.illness_description;
                        var insurance_no = data.insurance_no;
                        var physically_challenged = data.physically_challenged;
                        var remarks = data.remarks;
                        var date_of_birth = data.date_of_birth;


                        $("#relationship").val(relationship).trigger("change");
                        $("#rel_name").val(name);
                        $("#rel_gender").val(gender).trigger("change");
                        $("#rel_date_of_birth").val(date_of_birth);
                        $("#rel_aadhar_no").val(aadhar_no);
                        $("#occupation").val(occupation);
                        $("#emer_contact_person").val(emer_contact_person);
                        $("#emer_contact_no").val(emer_contact_no);
                        $("#standard").val(standard);
                        $("#school").val(school);
                        $("#existing_illness").val(existing_illness).trigger("change");
                        $("#description").val(illness_description);
                        $("#existing_insurance").val(existing_insurance).trigger("change");
                        $("#insurance_no").val(insurance_no);
                        $("#physically_challenged").val(physically_challenged).trigger("change");
                        $("#remarks").val(remarks);

                        // Button Change 
                        $(".dependent_details_add_update_btn").removeAttr("disabled", "disabled");
                        $(".dependent_details_add_update_btn").text("Update");
                        $(".dependent_details_add_update_btn").attr("onclick", "dependent_details_add_update('" + unique_id + "')");
                    }
                },
                error: function(data) {
                    alert("Network Error");
                }
            });
        }
    }

    function dependent_details_delete(unique_id = "", staff_unique_id = "") {
        if (unique_id) {

            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");

            confirm_delete('delete')
                .then((result) => {
                    if (result.isConfirmed) {
                        // 
                        // var unique_id = unique_id;
                        var action = "dependent_details_delete";


                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: {
                                unique_id: unique_id,
                                staff_unique_id: staff_unique_id,
                                action: action,
                            },
                            success: function(data) {

                                var obj = JSON.parse(data);
                                var msg = obj.msg;
                                // var status = obj.status;
                                // var error = obj.error;

                                // if (!status) {
                                //     url = '';
                                // } else {
                                //     sub_list_datatable("dependent_details_datatable");
                                // }
                                $('#my_dependentent_table').html(obj.data);
                                sweetalert(msg);
                            }
                        });

                    } else {
                        // alert("cancel");
                    }
                });
        }
    }

    // Customer Potential Mapping ADD & UPDATE
    function qualification_add_update(unique_id = "") { // au = add,update

        // alert("hii");


        var internet_status = is_online();

        var staff_unique_id = $("#staff_unique_id").val();
        // var unique_id         = $("#unique_id").val();
        var test_doc = $("#test_file_qual").val();
        //var doc_name          = $("#doc_name_qual").val();
        var education_type = $("#education_type").val();
        var degree = $("#degree").val();
        var college_name = $("#college_name").val();
        var year_passing = $("#year_passing").val();
        var percentage = $("#percentage").val();
        var university = $("#university").val();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }

        var is_form = form_validity_check("qualification_form");

        // console.log(is_form);

        //  if(is_form){
        // if(empty(doc_name)){
        var data = new FormData();
        var image_s = document.getElementById("test_file_qual");
        //if (image_s != '') {
        for (var i = 0; i < image_s.files.length; i++) {
            data.append("test_file[]", document.getElementById('test_file_qual').files[i]);
        }
        // } 

        // }else if(!empty(doc_name)){
        //     //     // var doc_names = doc_name;
        //         data.append("doc_names",doc_name);
        //     }



        //data.append("doc_names",doc_name);
        data.append("education_type", education_type);
        data.append("degree", degree);
        data.append("college_name", college_name);
        data.append("year_passing", year_passing);
        data.append("percentage", percentage);
        data.append("university", university);
        data.append("action", "qualification_add_update");
        data.append("staff_unique_id", staff_unique_id);
        data.append("unique_id", unique_id);

        // var data = $(".qualification_form").serialize();
        // data += "&staff_unique_id=" + staff_unique_id;
        // data += "&unique_id=" + unique_id + "&action=qualification_add_update";

        var ajax_url = 'crud.php'
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function() {

                $(".qualification_add_update_btn").attr("disabled", "disabled");
                // $(".qualification_add_update_btn").text("Loading...");
            },
            success: function(data) {

                // document.getElementById('test_doc').value = '';


                //  alert(data);
                var obj = JSON.parse(data);
                $('#my_qualification_table').html(obj.data);
                $(".qualification_add_update_btn").attr("disabled", false);
                $("#qualification_form")[0].reset();
                // window.location();
            }

        });

    }


    function experience_add_update(unique_id = "") { // au = add,update

        var internet_status = is_online();

        var staff_unique_id = $("#staff_unique_id").val();
        var test_docs = $("#test_docs").val();
        // var test_doc          = $("#test_doc").val();
        //var doc_names          = $("#doc_names").val();
        var company_names = $("#company_names").val();
        var salary_amt = $("#salary_amt").val();

        var designation_name = $("#designation_name").val();
        var join_month = $("#join_month").val();
        var relieve_month = $("#relieve_month").val();
        var exp = $("#exp").val();
        // var university        = $("#university").val();

        // if (!internet_status) {
        //     sweetalert("no_internet");
        //     return false;
        // }

        var is_form = form_validity_check("experience_form");

        // console.log(is_form);

        // if (is_form) {
        //     if(test_doc){
        var data = new FormData();
        var image_s = document.getElementById("test_file_exp");
        // alert(image_s);
        if (image_s != '') {
            for (var i = 0; i < image_s.files.length; i++) {
                data.append("test_file[]", document.getElementById('test_file_exp').files[i]);
            }
        } else {
            data.append("test_docs", '');
        }
        // }
        // data.append("test_doc",test_doc);

        //data.append("test_docs",test_docs);
        //data.append("doc_names",doc_names);
        data.append("company_names", company_names);
        data.append("salary_amt", salary_amt);

        data.append("designation_name", designation_name);
        data.append("join_month", join_month);
        data.append("relieve_month", relieve_month);
        data.append("exp", exp);
        data.append("action", "experience_add_update");
        data.append("staff_unique_id", staff_unique_id);
        data.append("unique_id", unique_id);
        // var data = $(".experience_form").serialize();
        // data += "&staff_unique_id=" + staff_unique_id;
        // data += "&unique_id=" + unique_id + "&action=experience_add_update";

        var ajax_url = 'crud.php'
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function() {
                $(".experience_add_update_btn").attr("disabled", "disabled");
                // $(".experience_add_update_btn").text("Loading...");
            },
            success: function(data) {

                var obj = JSON.parse(data);
                $('#my_experience_table').html(obj.data);
                $(".experience_add_update_btn").attr("disabled", false);
                $("#experience_form")[0].reset();
            }
        });
        // }

        //         var obj = JSON.parse(data);
        //         var msg = obj.msg;
        //         var status = obj.status;
        //         var error = obj.error;

        //         if (!status) {
        //             $(".experience_add_update_btn").text("Error");
        //             console.log(error);
        //         } else {
        //             if (msg !== "already") {
        //                 form_reset("experience_form");
        //             }
        //             $(".experience_add_update_btn").removeAttr("disabled", "disabled");
        //             if (unique_id && msg == "already") {
        //                 $(".experience_add_update_btn").text("Update");
        //             } else {
        //                 $(".experience_add_update_btn").text("Add");
        //                 $(".experience_add_update_btn").attr("onclick", "experience_add_update('')");
        //             }
        //             // Init Datatable
        //             sub_list_datatable("experience_datatable");
        //         }
        //         sweetalert(msg, url);
        //     },
        //     error: function(data) {
        //         alert("Network Error");
        //     }
        // });
        // } else {
        //     sweetalert("form_alert");
        // }
    }

    function staff_qualification_details_edit(unique_id = "") {

        document.getElementById('test_doc').value = '';

        if (unique_id) {
            var data = "unique_id=" + unique_id + "&action=staff_qualification_details_edit";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".qualification_add_update_btn").attr("disabled", "disabled");
                    // $(".qualification_add_update_btn").text("Loading...");
                },
                success: function(data) {

                    var obj = JSON.parse(data);
                    var data = obj.data;
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;
                    // alert(data.doc_name);
                    if (!status) {
                        $(".qualification_add_update_btn").text("Error");
                        console.log(error);
                    } else {
                        console.log(obj);
                        var education_type = data.education_type;
                        var degree = data.degree;
                        var college_name = data.college_name;
                        var doc_name = data.doc_name;
                        var year_passing = data.year_passing;
                        var percentage = data.percentage;
                        var university = data.university;

                        $("#education_type").val(education_type);
                        $("#degree").val(degree);
                        $("#college_name").val(college_name);

                        $("#year_passing").val(year_passing);
                        $("#percentage").val(percentage);
                        $("#university").val(university);
                        // document.getElementById('doc_name').value = 'uploads/q_doc'/doc_name;
                        $("#doc_name").val(doc_name);

                        // Button Change 
                        $(".qualification_add_update_btn").removeAttr("disabled", "disabled");
                        $(".qualification_add_update_btn").text("Update");
                        $(".qualification_add_update_btn").attr("onclick", "qualification_add_update('" + unique_id + "')");
                    }
                },
                error: function(data) {
                    alert("Network Error");
                }
            });
        }
    }

    function staff_qualification_delete(unique_id = "", staff_unique_id = "") {
        // alert('hi');
        if (unique_id) {
            // alert(unique_id);
// alert(unique_id);
            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");

            confirm_delete('delete').then((result) => {
                    if (result.isConfirmed) {
                        

                        // var unique_id =  unique_id;
                        // var action =  "staff_qualification_details_delete";
                        var action = "staff_qualification_details_delete";


                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: {
                                unique_id: unique_id,
                                staff_unique_id: staff_unique_id,
                                action: action,
                            },
                            success: function(data) {
                                
                                var obj = JSON.parse(data);
                                var msg = obj.msg;

                                $('#my_qualification_table').html(obj.data);
                                sweetalert(msg);
                                // var obj = JSON.parse(data);
                                // var msg = obj.msg;
                                // // var status = obj.status;
                                // // var error = obj.error;

                                // // if (!status) {
                                // //     url = '';
                                // // } else {
                                // //     sub_list_datatable("dependent_details_datatable");
                                // // }
                                // $('#my_qualification_table').html(obj.data);
                                // sweetalert(msg);

                                // window.location.reload();
                            }
                        });

                    } else {
                        // alert("cancel");
                    }
                });
        }
    }

    function staff_experience_details_edit(unique_id = "") {
        
        document.getElementById('test_docs').value = '';
        if (unique_id) {
            var data = "unique_id=" + unique_id + "&action=staff_experience_details_edit";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".experience_add_update_btn").attr("disabled", "disabled");
                    $(".experience_add_update_btn").text("Loading...");
                },
                success: function(data) {

                    var obj = JSON.parse(data);
                    var data = obj.data;
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;

                    if (!status) {
                        $(".experience_add_update_btn").text("Error");
                        console.log(error);
                    } else {
                        console.log(obj);
                        var company_name = data.company_name;
                        var salary_amt = data.salary_amt;
                        var designation_name = data.designation_name;

                        var join_month = data.join_month;
                        var relieve_month = data.relieve_month;
                        var exp = data.exp;
                        var doc_name = data.doc_name;

                        $("#company_names").val(company_name);
                        $("#salary_amt").val(salary_amt);
                        $("#designation_name").val(designation_name);
                        $("#doc_names").val(doc_name);
                        $("#join_month").val(join_month);
                        $("#relieve_month").val(relieve_month);
                        $("#exp").val(exp);

                        // Button Change 
                        $(".experience_add_update_btn").removeAttr("disabled", "disabled");
                        $(".experience_add_update_btn").text("Update");
                        $(".experience_add_update_btn").attr("onclick", "experience_add_update('" + unique_id + "')");
                    }
                },
                error: function(data) {
                    alert("Network Error");
                }
            });
        }
    }

    function staff_experience_details_delete(unique_id = "", staff_unique_id = "") {
        // alert('hi');

        if (unique_id) {

            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");

            confirm_delete('delete')
                .then((result) => {
                    if (result.isConfirmed) {
                        // alert(unique_id);
                        var action = "staff_experience_details_delete";


                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: {
                                unique_id: unique_id,
                                staff_unique_id: staff_unique_id,
                                action: action,
                            },

                            success: function(data) {

                                var obj = JSON.parse(data);
                                var msg = obj.msg;

                                $('#my_experience_table').html(obj.data);
                                sweetalert(msg);
                            }
                        });

                    } else {
                        // alert("cancel");
                    }
                });
        }
    }

    // Account Details ADD & UPDATE
    function staff_account_details_add_update(unique_id = "") { // au = add,update
        // alert("hii");

        var internet_status = is_online();

        var staff_unique_id = $("#staff_unique_id").val();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }

        var is_form = form_validity_check("account_details_form");

        // console.log(is_form);

        // if (is_form) {

        var data = $(".account_details_form").serialize();
        data += "&staff_unique_id=" + staff_unique_id;
        data += "&unique_id=" + unique_id + "&action=staff_account_details_add_update";

        var ajax_url = 'crud.php'
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function() {
                $(".staff_account_details_add_update_btn").attr("disabled", true);
                // $(".staff_account_details_add_update_btn").text("Loading...");

            },
            success: function(data) {
                var obj = JSON.parse(data);

                $('#my_account_table').html(obj.data);
                $(".staff_account_details_add_update_btn").attr("disabled", false);
                $("#account_details_form")[0].reset();
            }

        });
        // }

        //             var obj = JSON.parse(data);
        //             var msg = obj.msg;
        //             var status = obj.status;
        //             var error = obj.error;

        //             if (!status) {
        //                 $(".staff_account_details_add_update_btn").text("Error");
        //                 console.log(error);
        //             } else {
        //                 if (msg !== "already") {
        //                     form_reset("account_details_form");
        //                 }
        //                 $(".staff_account_details_add_update_btn").removeAttr("disabled", "disabled");
        //                 if (unique_id && msg == "already") {
        //                     $(".staff_account_details_add_update_btn").text("Update");
        //                 } else {
        //                     $(".staff_account_details_add_update_btn").text("Add");
        //                     $(".staff_account_details_add_update_btn").attr("onclick", "staff_account_details_add_update('')");
        //                 }
        //                 // Init Datatable
        //                 sub_list_datatable("staff_account_details_datatable");
        //             }
        //             sweetalert(msg, url);
        //         },
        //         error: function(data) {
        //             alert("Network Error");
        //         }
        //     });


        // } else {
        //     sweetalert("form_alert");
        // }
    }

    function staff_account_details_edit(unique_id = "") {

        if (unique_id) {
            var data = "unique_id=" + unique_id + "&action=staff_account_details_edit";

            var ajax_url = 'crud.php'
            // var url      = sessionStorage.getItem("list_link");
            var url = "";

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                beforeSend: function() {
                    $(".staff_account_details_add_update_btn").attr("disabled", "disabled");
                    $(".staff_account_details_add_update_btn").text("Loading...");
                },
                success: function(data) {

                    var obj = JSON.parse(data);
                    var data = obj.data;
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;

                    if (!status) {
                        $(".staff_account_details_add_update_btn").text("Error");
                        console.log(error);
                    } else {
                        console.log(obj);
                        var bank_name = data.bank_name;
                        var bank_address = data.address;
                        var ifsc_code = data.ifsc_code;
                        var accountant_name = data.accountant_name;
                        var account_no = data.account_no;
                        var contact_no = data.contact_no;
                        // var gst_no                      = data.gst_no;

                        $("#bank_name").val(bank_name);
                        $("#bank_address").val(bank_address);
                        $("#ifsc_code").val(ifsc_code);
                        $("#accountant_name").val(accountant_name);
                        $("#account_no").val(account_no);
                        // $("#bank_gst_no").val(gst_no);
                        $("#bank_contact_no").val(contact_no);
                        // Button Change 
                        $(".staff_account_details_add_update_btn").removeAttr("disabled", "disabled");
                        $(".staff_account_details_add_update_btn").text("Update");
                        $(".staff_account_details_add_update_btn").attr("onclick", "staff_account_details_add_update('" + unique_id + "')");
                    }
                },
                error: function(data) {
                    alert("Network Error");
                }
            });
        }
    }

    function staff_account_details_delete(unique_id = "", staff_unique_id = "") {

        if (unique_id) {

            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");

            confirm_delete('delete')
                .then((result) => {
                    if (result.isConfirmed) {
                        // var unique_id = unique_id;
                        // 
                        var action = "staff_account_details_delete";
                        // var data = {
                        //     "unique_id": unique_id,
                        //     "action": "staff_account_details_delete"
                        // }

                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: {
                                unique_id: unique_id,
                                staff_unique_id: staff_unique_id,
                                action: action
                            },
                            success: function(data) {

                                var obj = JSON.parse(data);
                                var msg = obj.msg;
                                // var status = obj.status;
                                // var error = obj.error;

                                // var obj = JSON.parse(data);

                                $('#my_account_table').html(obj.data);

                                sweetalert(msg);
                            }
                        });

                    } else {
                        // alert("cancel");
                    }
                });
        }
    }

    function staff_delete(unique_id = "") {

        var ajax_url = 'crud.php'
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "delete"
                    }

                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: data,
                        success: function(data) {

                            var obj = JSON.parse(data);
                            var msg = obj.msg;
                            var status = obj.status;
                            var error = obj.error;

                            if (!status) {
                                url = '';

                            } else {
                                init_datatable(table_id, form_name, action);
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    // alert("cancel");
                }
            });
    }


    function isNumber(evt) {
        evt = (evt) ? evt : window.event;
        var charCode = (evt.which) ? evt.which : evt.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    }

    function get_salary(salary) {
        var annum_basic = "";
        var annum_hra = "";
        var annum_conveyance = "";
        var annum_medical_allowance = "";
        var annum_educational_allowance = "";
        var sum_allowance = "";
        var other_allowance = "";
        var annum_other_allowance = "";
        var annum_pf = "";
        var annum_esi = "";
        var total_deduction = "";
        var annum_total_deduction = "";
        var net_salary = "";
        var annum_net_salary = "";
        var annum_purformance_allowance = "";
        var ctc_cal = "";
        var ctc = "";
        var annum_ctc = "";

        if (salary) {
            get_salary1(salary);
        }
    }

    function get_salary1(salary) {

        var perf_allowance = $('#purformance_allowance').val();
        if (perf_allowance == '') {
            var purformance_allowance = 0;
        } else {
            var purformance_allowance = parseFloat(perf_allowance);
        }

        var conveyance_default_value = 5000;
        var medical_default_value = 8000;
        var educational_default_value = 900;
        var pf_default_value = 15000;
        var esi_default_value = 21000;

        if ((salary != '')) {
            var per_annum = salary * 12;
            var basic = ((salary * 40) / 100);
            var hra = ((basic * 50) / 100);
            //conveyance calculation
            if (salary >= conveyance_default_value) {
                var conveyance = 1600;
            } else {
                var conveyance = 0;
            }
            //medical allowance
            if (salary >= medical_default_value) {
                var medical_allowance = 1250;
            } else {
                var medical_allowance = 0;
            }
            //Education allowance
            if (salary >= educational_default_value) {
                var educational_allowance = 200;
            } else {
                var educational_allowance = 0;
            }
            //pf
            if (basic <= pf_default_value) {
                var pf = ((basic * 12) / 100);
            } else {
                var pf = 0;
            }
            //esi
            if (salary <= esi_default_value) {
                var esi = ((salary * 0.75) / 100);
            } else {
                var esi = 0;
            }


            var annum_basic = basic * 12;
            var annum_hra = hra * 12;
            var annum_conveyance = conveyance * 12;
            var annum_medical_allowance = medical_allowance * 12;
            var annum_educational_allowance = educational_allowance * 12;
            var sum_allowance = basic + hra + conveyance + medical_allowance + educational_allowance;
            var other_allowance = salary - sum_allowance;
            var annum_other_allowance = other_allowance * 12;
            var annum_pf = pf * 12;
            var annum_esi = esi * 12;
            var total_deduction = pf + esi;
            var annum_total_deduction = annum_pf + annum_esi;
            var net_salary = salary - total_deduction;
            var annum_net_salary = net_salary * 12;
            var annum_purformance_allowance = purformance_allowance * 12;
            var ctc_cal = total_deduction + net_salary;
            var ctc = purformance_allowance + ctc_cal;
            var annum_ctc = ctc * 12;

            $("#annum_salary").val(per_annum);
            $("#basic_wages").val(Math.round(basic));
            $("#annum_basic_wages").val(annum_basic);
            $("#hra").val(Math.round(hra));
            $("#annum_hra").val(annum_hra);
            $("#conveyance").val(Math.round(conveyance));
            $("#annum_conveyance").val(annum_conveyance);
            $("#medical_allowance").val(Math.round(medical_allowance));
            $("#annum_medical_allowance").val(annum_medical_allowance);
            $("#education_allowance").val(Math.round(educational_allowance));
            $("#annum_education_allowance").val(annum_educational_allowance);
            $("#other_allowance").val(Math.round(other_allowance));
            $("#annum_other_allowance").val(annum_other_allowance);
            $("#pf").val(Math.round(pf));
            $("#annum_pf").val(annum_pf);
            $("#esi").val(Math.round(esi));
            $("#annum_esi").val(annum_esi);
            $("#total_deduction").val(Math.round(total_deduction));
            $("#annum_total_deduction").val(annum_total_deduction);
            $("#net_salary").val(Math.round(net_salary));
            $("#annum_net_salary").val(annum_net_salary);
            $("#annum_purformance_allowance").val(annum_purformance_allowance);
            $("#ctc").val(Math.round(ctc));
            $("#annum_ctc").val(annum_ctc);
            $("#conveyance_default_value").val(conveyance_default_value);
            $("#medical_default_value").val(medical_default_value);
            $("#pf_default_value").val(pf_default_value);
            $("#esi_default_value").val(esi_default_value);
            $("#educational_default_value").val(educational_default_value);
        } else {
            $("#annum_salary").val('');
            $("#basic_wages").val('');
            $("#annum_basic_wages").val('');
            $("#hra").val('');
            $("#annum_hra").val('');
            $("#conveyance").val('');
            $("#annum_conveyance").val('');
            $("#medical_allowance").val('');
            $("#annum_medical_allowance").val('');
            $("#education_allowance").val('');
            $("#annum_education_allowance").val('');
            $("#other_allowance").val('');
            $("#annum_other_allowance").val('');
            $("#pf").val('');
            $("#annum_pf").val('');
            $("#esi").val('');
            $("#annum_esi").val('');
            $("#total_deduction").val('');
            $("#annum_total_deduction").val('');
            $("#net_salary").val('');
            $("#annum_net_salary").val('');
            $("#annum_purformance_allowance").val('');
            $("#ctc").val('');
            $("#annum_ctc").val('');
            $("#conveyance_default_value").val(conveyance_default_value);
            $("#medical_default_value").val(medical_default_value);
            $("#pf_default_value").val(pf_default_value);
            $("#esi_default_value").val(esi_default_value);
            $("#educational_default_value").val(educational_default_value);
        }
    }

    function get_permanent_address(same_address = '') {

        if (document.getElementById('same_address').checked) {
            $("#same_address_status").val('1');
            var country = $("#pre_country").val();
            var per_state = $("#pre_state").val();
            var city = $("#pre_city").val();
            var building_no = $("#pre_building_no").val();
            var street = $("#pre_street").val();
            var area = $("#pre_area").val();
            var pincode = $("#pre_pincode").val();

            //  alert(per_state);
            $('#perm_country').val(country).trigger('change');
            $('#edit_perm_state_id').val(per_state);
            $('#edit_perm_city_id').val(city);
            // $('#perm_city').val(city).trigger('change');
            $('#perm_building_no').val(building_no);
            $('#perm_street').val(street);
            $('#perm_area').val(area);
            $('#perm_pincode').val(pincode);



        } else {
            $("#same_address_status").val('0');
        }

    }



    function ageCalculate(birthDate) {



        var d = new Date(birthDate);



        var mdate = birthDate.toString();
        var yearThen = parseInt(mdate.substring(0, 4), 10);
        var monthThen = parseInt(mdate.substring(5, 7), 10);
        var dayThen = parseInt(mdate.substring(8, 10), 10);

        var today = new Date();
        var birthday = new Date(yearThen, monthThen - 1, dayThen);
        var differenceInMilisecond = today.valueOf() - birthday.valueOf();

        var year_age = Math.floor(differenceInMilisecond / 31536000000);
        var day_age = Math.floor((differenceInMilisecond % 31536000000) / 86400000);

        document.getElementById("age").value = year_age;
    }


    function file_upload(unique_id = "") {
        var internet_status = is_online();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }

        var is_form = form_validity_check("was-validated");

        if (is_form) {

            var file_data = $('#test_file').prop('files')[0];
            var data = new FormData();
            console.log(data);

            data.append("action", "image_upload");
            data.append("unique_id", unique_id);
            data.append("test_file", file_data);

            console.log(typeof data);

            var ajax_url = 'crud.php'
            var url = sessionStorage.getItem("list_link");

            // console.log(data);
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                beforeSend: function() {
                    // $(".createupdate_btn").attr("disabled","disabled");
                    $(".createupdate_btn").text("Loading...");
                },
                success: function(data) {

                    var obj = JSON.parse(data);
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;

                    if (!status) {
                        url = '';
                        $(".createupdate_btn").text("Error");
                        console.log(error);
                    } else {
                        if (msg == "already") {
                            // Button Change Attribute
                            url = '';

                            $(".createupdate_btn").removeAttr("disabled", "disabled");
                            if (unique_id) {
                                $(".createupdate_btn").text("Update");
                            } else {
                                $(".createupdate_btn").text("Save");
                            }
                        }
                    }
                    // sweetalert(msg,url);
                },
                error: function(data) {
                    alert("Network Error");
                }
            });


        } else {
            sweetalert("form_alert");
        }
    }

    function staffFilter(filter_action = 0) {
        var internet_status = is_online();

        if (!internet_status) {
            sweetalert("no_internet");
            return false;
        }
        var status = $('#staff_status').val();
        var company_name = $('#company_name').val();
        if (status) {

            sessionStorage.setItem("status", status);
            sessionStorage.setItem("staff_action", filter_action);

            // Delete Below Line After Testing Complete
            sessionStorage.setItem("follow_up_call_action", 0);

            var filter_data = {
                "status": status,
                "company_name": company_name,
                "filter_action": filter_action
            };

            console.log(filter_data);

            init_datatable(table_id, form_name, action, filter_data);

        } else {
            sweetalert("form_alert", "");
        }
    }

    function get_salary_type() {
        var bank_status = $('#bank_status').val();
        if (bank_status == 'Secondary') {

            $("#salary_type").val("NEFT").change();
            $("#salary_type").prop("disabled", false);
        } else {
            $("#salary_type").val("Axis Bank").change();
            $("#salary_type").prop("disabled", true);
        }
    }

    function get_branch_ids() {
        var branch = $('#branch').val();
        $('#staff_branch').val(branch);
    }

    function get_file_name() {
        var file_name = $("#test_doc").val();

        $("#doc_name").val(file_name);
    }

    function print(file_name) {
        onmouseover = window.open('../../aed_erp/' + file_name, 'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }

    function submit(){
        location.reload();
    }

    // function btn_hide() {
    //     if ($("#dependentdetails_tab").hasClass("active")) {
    //         document.getElementById('btn').style.display = "none";
    //         document.getElementById('btn_next').style.display = "block";

    //     }
    // }

   
</script>