<?php
// This file Only PHP Functions
include 'function.php';
// Common Variable for all form
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";
$unique_id              = "";

$supplier_name          = "";
$vendor_code            = "";
$group_unique_id        = "";
$reference              = "";
$manufacturer_flag      = "";
$agent_dealer_flag      = "";
$service_jobwork_flag   = "";
$ledger_balance         = "";
$credit_limit           = "";
$credit_days            = "";
$contact_no             = "";
$currency_type          = "";
$country                = "";
$state                  = "";
$district               = "";
$city                   = "";
$pincode                = "";
$address                = "";
$corporate_address      = "";
$email_id               = "";
$fax_no                 = "";
$gst_no                 = "";
$pan_no                 = "";
$gst_reg_date           = "";
$gst_status             = "";
$arn_no                 = "";
$msme_type              = "";
$msme_value             = "";
$msme_no                = "";
$website                = "";

$country_options                = "<option value='' disabled='disabled' selected>Select the Country</option>";
$state_options                  = "<option value='' disabled='disabled' selected>Select the State</option>";
$city_options                   = "<option value='' disabled='disabled' selected>Select the City</option>";


if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id = $_GET["unique_id"];

    // ========== SUPPLIER PROFILE DETAILS ==========
    $where_profile = [
        "unique_id" => $unique_id,
        "is_active" => 1,
        "is_delete" => 0
    ];
    $table_profile = "supplier_profile";
    $columns_profile = [
        "supplier_name", "vendor_code", "group_unique_id", "reference",
        "manufacturer_flag", "agent_dealer_flag", "service_jobwork_flag",
        "contact_no", "currency_type", "country", "state", "city", 
        "pincode", "address", "corporate_address", "email_id", "fax_no",
        "gst_no", "pan_no", "gst_reg_date", "gst_status", "arn_no", "msme_type",
        "msme_value", "website"
    ];
    $result_profile = $pdo->select([$table_profile, $columns_profile], $where_profile);
    
    // error_log("supplier_profile: " . print_r($result_profile) . "\n", 3, "form_log.txt");

    if ($result_profile->status) {
        $profile_data = $result_profile->data[0];

        // Assign values
        extract($profile_data); // Use variables like $supplier_name, $vendor_code, etc.

        // Additional processing
        $state_code_details = state($state);
        $state_code = $state_code_details[0]['state_code'] ?? '';
        $city_options = select_option(city("", $state), "Select the City", $city);
        
        error_log("city: " . print_r($city_options, true) . "\n", 3, "city_log.txt");

        $btn_text = "Update";
        $btn_action = "update";
    } else {
        $btn_text = "Error";
        $btn_action = "error";
        $is_btn_disable = "disabled='disabled'";
    }

    $country_options = country();
    $state_options = state("", $country);

    // ========== STATUTORY DETAILS ==========
    $where_statutory = [
        "supplier_profile_unique_id" => $unique_id
    ];
    $table_statutory = "sp_statuatory_details";
    $columns_statutory = [
        "supplier_profile_unique_id", "ecc_no", "commissionerate", "division", "stat_range",
        "cst_no", "tin_no", "service_tax_no", "iec_code", "cin_no", "tan_no", "acc_year",
        "sess_user_type", "sess_user_id", "sess_company_id", "sess_branch_id", "session_id"
    ];
    $result_statutory = $pdo->select([$table_statutory, $columns_statutory], $where_statutory);

    error_log("STATUTORY result: " . print_r($result_statutory, true) . "\n", 3, "error_log_stat.txt");

    if ($result_statutory->status) {
        $stat_data = $result_statutory->data[0];

        // Assign values
        extract($stat_data); // Use variables like $ecc_no, $commissionerate, etc.

        // These values can now be used in the form rendering
    } else {
        // Statutory data missing – might be optional?
        error_log("No statutory details found for: $unique_id\n", 3, "error_log_stat.txt");
    }

} else {
    // New Entry
    $country_options = country();
    $state_options = state();
}



$country_options                = select_option($country_options,"Select the Country",$country);

$shipping_country_options       = country();
$shipping_country_options       = select_option($shipping_country_options, "Select the Country");

$shipping_city_options          = city("", $shipping_state_unique_id);
$shipping_city_options          = select_option($shipping_city_options, "Select the District", $shipping_city_unique_id);

$state_options                  = select_option($state_options, "Select the State", $state);

$branch_state_options           = state("", "coun5f7a05b7110cd84071");
$branch_state_options           = select_option($branch_state_options, "Select the State");

$acc_type_options  = [
    1 => [
        "unique_id" => 1,
        "value"     => "Cr",
    ],
    2 => [
        "unique_id" => 2,
        "value"     => "Dr",
    ],
];
$acc_type_options  = select_option($acc_type_options, "Select", $acc_type);

$supplier_category_options  = [
    1 => [
        "unique_id" => 1,
        "value"     => "International",
    ],
    2 => [
        "unique_id" => 2,
        "value"     => "Domestic",
    ],
];
$supplier_category_options  = select_option($supplier_category_options, "Select", $supplier_category);

$currency_type_options  = [
    1 => [
        "unique_id" => 1,
        "value"     => "National Distributor",
    ],
    2 => [
        "unique_id" => 2,
        "value"     => "Sub Distributor",
    ],
    3 => [
        "unique_id" => 3,
        "value"     => "OEM",
    ],
    4 => [
        "unique_id" => 4,
        "value"     => "Others",
    ],
];
$currency_type_options  = select_option($currency_type_options, "Select", $currency_type);

$supplier_category_options      = customer_group();
$supplier_category_options      = select_option($supplier_category_options,"Select the Group Name",$group_unique_id);

$currency_options               = currency_creation_name();
$currency_options               = select_option($currency_options,"Select the Currency Type",$currency_type);

$msme_options                   = msme_creation_name();
$msme_options                   = select_option($msme_options,"Select the MSME Type",$msme_type);

$type_options                   = doc_type_options();
$type_options                   = select_option($type_options,"Select the Document Type",$doc_type);

?>
<!-- Unique ID hidden input -->
<input type="hidden" id="unique_id" value="<?php echo  $_GET["unique_id"]; ?>">
<input type="hidden" id="supplier_unique_id" value="<?php echo  $_GET["unique_id"]; ?>">
<input type="hidden" id="user_id" value="<?php echo $_SESSION['user_id']; ?>">
<input type="hidden" id="user_type_id" value="<?php echo $_SESSION['sess_user_type']; ?>">
<input type="hidden" id="supplier_name" value="0">
<input type="hidden" id="contact_value" value="0">
<input type="hidden" id="branch_value" value="0">
<input type="hidden" id="bd_value" value="0">
<input type="hidden" id="billing_value" value="0">
<input type="hidden" id="shipping_value" value="0">
<input type="hidden" id="doc_value" value="0">
<input type="hidden" id="tab_count" value="0">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="suppliercreatewizard">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-3">
                        <li class="nav-item">
                            <a href="#personaldetails_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                <i class="mdi mdi-face-profile mr-1"></i>
                                <span class="d-none d-sm-inline">Vendor Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#contactperson_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-face-profile mr-1"></i>
                                <span class="d-none d-sm-inline">Contact Person</span>
                            </a>
                        </li>
                         <li class="nav-item">
                            <a href="#statutory_details_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-face-profile mr-1"></i>
                                <span class="d-none d-sm-inline">Statutory Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#account_details_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Account Details</span>
                            </a>
                        </li>
                        <!--<li class="nav-item">-->
                        <!--    <a href="#branch_details_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                        <!--        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>-->
                        <!--        <span class="d-none d-sm-inline">Branch Details</span>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <li class="nav-item">
                            <a href="#billing_details_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Billing Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#shipping_details_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Shipping Details</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#documents_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Upload Documents</span>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content b-0 mb-0 pt-0">
                        <div id="bar" class="progress mb-3" style="height: 7px;">
                            <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 20%;"></div>
                        </div>

                        <div class="tab-pane active" id="personaldetails_tab">
                            <form class="was-validated supplier_profile_form" id="supplier_profile_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="supplier_name"> Vendor Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="supplier_name" name="supplier_name" class="form-control" value="<?php echo $supplier_name; ?>" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="group_unique_id">Group</label>
                                            <div class="col-md-4">
                                                <select name="group_unique_id" id="group_unique_id" class="select2 form-control"  required>
                                                    <?php echo $supplier_category_options;?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row align-items-center">
                                            <label class="col-md-2 col-form-label" for="currency_unique_id">Currency</label>
                                            <div class="col-md-4">
                                                <select name="currency_unique_id" id="currency_unique_id" class="select2 form-control" >
                                                    <?php echo $currency_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="reference">Reference</label>
                                            <div class="col-md-4">
                                                <input type="text" id="reference" name="reference" class="form-control" value="<?php echo $reference; ?>" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label text-nowrap" for="manufacturer_flag">Manufacturer</label>
                                            <div class="col-md-2">
                                                <input class="form-check-input mt-2" type="checkbox" name="manufacturer_flag" id="manufacturer_flag" value="1" <?php if ($manufacturer_flag) echo 'checked'; ?>>
                                            </div>
                                            <label class="col-md-2 col-form-label text-nowrap" for="agent_dealer_flag">Agent / Dealer</label>
                                            <div class="col-md-2">
                                                <input class="form-check-input mt-2" type="checkbox" name="agent_dealer_flag" id="agent_dealer_flag" value="1" <?php if ($agent_dealer_flag) echo 'checked'; ?>>
                                            </div>
                                            <label class="col-md-2 col-form-label text-nowrap" for="service_jobwork_flag">Service / Jobwork</label>
                                            <div class="col-md-2">
                                                <input class="form-check-input mt-2" type="checkbox" name="service_jobwork_flag" id="service_jobwork_flag" value="1" <?php if ($service_jobwork_flag) echo 'checked'; ?>>
                                            </div>

                                            <!--<label class="col-md-2 col-form-label" for="credit_limit">Credit Limit</label>-->
                                            <!--<div class="col-md-4">-->
                                            <!--    <input type="text" id="credit_limit" name="credit_limit" class="form-control" value="<?php echo $credit_limit; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">-->
                                            <!--</div>-->
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <!-- <label class="col-md-2 col-form-label" for="ledger_balance">Ledger Balance</label>
                                            <div class="col-md-4">
                                                <input type="text" id="ledger_balance" name="ledger_balance" class="form-control" value="<?php echo $ledger_balance; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                                            </div> -->
                                            <!-- <label class="col-md-2 col-form-label" for="credit_limit">Credit Limit</label>
                                            <div class="col-md-4">
                                                <input type="text" id="credit_limit" name="credit_limit" class="form-control" value="<?php echo $credit_limit; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                                            </div> -->
                                        </div>
                                        
                                        <!-- <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="credit_days">Credit Days</label>
                                            <div class="col-md-4">
                                                <input type="text" id="credit_days" name="credit_days" class="form-control" value="<?php echo $credit_days; ?>" oninput="this.value = this.value.replace(/[^0-9.]/g, '')">
                                            </div>
                                        </div> -->
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="country_name"> Country </label>
                                            <div class="col-md-4">
                                                    <select name="country_name" id="country_name" class="select2 form-control" onchange="get_states(this.value);" required>
                                                        <?php echo $country_options;?>
                                                    </select>
                                                </div>
                                            <label class="col-md-2 col-form-label" for="state_name"> State </label>
                                            <div class="col-md-4">
                                                <select name="state_name" id="state_name" class="select2 form-control" onchange="get_cities(this.value);" required>
                                                    <?php echo $state_options;?>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <br>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="district_name"> City </label>
                                            <div class="col-md-4">
                                                <select name="city_name" id="city_name" class="select2 form-control" required>
                                                    <?php echo $city_options;?>
                                                </select>
                                            </div>
                                                
                                            <label class="col-md-2 col-form-label" for="pincode">Pincode</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pincode" name="pincode" class="form-control" value="<?php echo $pincode; ?>" required pattern="^[1-9][0-9]{5}$" maxlength="6" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="address"> Address</label>
                                            <div class="col-md-4">
                                                <textarea name="address" id="address" rows="5" class="form-control" required> <?php echo $address; ?></textarea>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="corporate_address">Corporate Address</label>
                                            <div class="col-md-4">
                                                <textarea name="corporate_address" id="corporate_address" rows="5" class="form-control" required> <?php echo $corporate_address; ?></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="phone_no">PhoneNo</label>
                                            <div class="col-md-4">
                                                <input type="text" maxlength='10' minlength='10' id="phone_no" name="phone_no" pattern="^[0-9]{10}$" class="form-control" value="<?php echo $contact_no; ?>" oninput="this.value=this.value.replace(/[^0-9]/g,'')" >
                                            </div>
                                            <label class="col-md-2 col-form-label" for="fax_no"> Fax No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="fax_no" pattern="^\+?[0-9\s\-]{10,15}$"   name="fax_no" class="form-control" value="<?php echo $fax_no; ?>" >
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="pan_no">PAN No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="pan_no" name="pan_no" class="form-control" maxlength="10" minlength="10" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" oninput="this.value = this.value.toUpperCase()" value="<?php echo $pan_no; ?>" required title="Enter valid PAN number (e.g., ABCDE1234F)">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="gst_no"> GST No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="gst_no" name="gst_no" class="form-control" maxlength="15" minlength="15" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$" oninput="this.value = this.value.toUpperCase()" value="<?php echo $gst_no; ?>"  title="Enter a valid 15-character GSTIN (e.g., 22ABCDE1234F1Z5)">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="gst_reg_date">GST Reg Date</label>
                                            <div class="col-md-4">
                                                <input type="date" id="gst_reg_date" name="gst_reg_date" class="form-control" value="<?php echo $gst_reg_date; ?>" >
                                            </div>
                                            <label class="col-md-2 col-form-label" for="gst_status">GST Status</label>
                                            <div class="col-md-4">
                                                <select id="gst_status" name="gst_status" class="form-control" required>
                                                    <option value="1">Active</option>
                                                    <option value="2">Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="email_id"> Email Id</label>
                                            <div class="col-md-4">
                                                <input type="email" id="email_id" name="email_id" class="form-control" value="<?php echo $email_id; ?>" >
                                            </div>
                                            <label class="col-md-2 col-form-label" for="website">Website</label>
                                            <div class="col-md-4">
                                                <input type="text" id="website" name="website" class="form-control" value="<?php echo $website; ?>">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row">
                                            <label class="col-md-2 col-form-label" for="msme_type">MSME TYPE</label>
                                                <div class="col-md-4">
                                                <select name="msme_type" id="msme_type" class="select2 form-control" onchange="get_msme_value(this.value)">
                                                    <?php echo $msme_options; ?>
                                                </select>
                                                <input type="text" id="msme_input_box" name="msme_value" class="form-control mt-2" placeholder="Enter value" value="<?php echo isset($msme_value) ? htmlspecialchars($msme_value) : ''; ?>">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="arn_no">ARN No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="arn_no" name="arn_no" class="form-control" value="<?php echo $arn_no; ?>" >
                                            </div>
                                        </div>
                                        

                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="contactperson_tab">
                            <form class="was-validated contact_person_form" name="contact_person_form" id="contact_person_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="contact_person_name"> Contact Person Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="contact_person_name" name="contact_person_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="contact_person_designation"> Designation</label>
                                            <div class="col-md-4">
                                                <input type="text" id="contact_person_designation" name="contact_person_designation" class="form-control" value="" >
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="contact_person_email"> Email </label>
                                            <div class="col-md-4">
                                                <input type="email" id="contact_person_email" name="contact_person_email" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="contact_person_contact_no">Mobile No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="contact_person_contact_no" name="contact_person_contact_no" class="form-control" value="" onkeypress="number_only(event);" minlength="10" maxlength="10" size="10" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="landline"> Landline </label>
                                            <div class="col-md-4">
                                                <input type="text" id="landline" name="landline" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="department">Department</label>
                                            <div class="col-md-4">
                                                <input type="text" id="department" name="department" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class="btn btn-success waves-effect  waves-light contact_person_add_update_btn" onclick="contact_person_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="contact_person_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Person Name</th>
                                                            <th>Designation</th>
                                                            <th>Email</th>
                                                            <th>Mobile No</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                            <div class="tab-pane fade" id="statutory_details_tab">
                                <div class="row">
                                    <div class="form-group col-md-3">
                                        <label for="ecc_no">ECC No</label>
                                        <input type="text" class="form-control" id="ecc_no" name="ecc_no" value="<?= $ecc_no; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="commissionerate">Commissionerate</label>
                                        <input type="text" class="form-control" id="commissionerate" name="commissionerate" value="<?= $commissionerate; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="division">Division</label>
                                        <input type="text" class="form-control" id="division" name="division" value="<?= $division; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="range">Range</label>
                                        <input type="text" class="form-control" id="range" name="range" value="<?= $stat_range; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="cst_no">CST No</label>
                                        <input type="text" class="form-control" id="cst_no" name="cst_no" value="<?= $cst_no; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tin_no">TIN No</label>
                                        <input type="text" class="form-control" id="tin_no" name="tin_no" value="<?= $tin_no; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="service_tax_no">Service Tax No</label>
                                        <input type="text" class="form-control" id="service_tax_no" name="service_tax_no" value="<?= $service_tax_no; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="iec_code">IEC Code</label>
                                        <input type="text" class="form-control" id="iec_code" name="iec_code" value="<?= $iec_code; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="cin_no">CIN No</label>
                                        <input type="text" class="form-control" id="cin_no" name="cin_no" value="<?= $cin_no; ?>">
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="tan_no">TAN No</label>
                                        <input type="text" class="form-control" id="tan_no" name="tan_no" value="<?= $tan_no; ?>">
                                    </div>
                                </div>
                            </div>

                        <div class="tab-pane" id="account_details_tab">
                            <form class="was-validated account_details_form" id="account_details_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="bank_name"> Bank name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="bank_name" name="bank_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="account_no">Account Number</label>
                                            <div class="col-md-4">
                                                <input type="text" id="account_no" name="account_no" pattern="\d{9,18}" title="Bank account number must be between 9 and 18 digits" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="dealer_name"> Account Holder Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="dealer_name" name="dealer_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="ifsc_code"> IFSC Code</label>
                                            <div class="col-md-4">
                                                <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="bank_contact_no"> Contact No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="bank_contact_no" onkeypress="return isNumber(event)" maxlength="10" minlength="10" name="bank_contact_no" class="form-control" value="" >
                                            </div>
                                            <label class="col-md-2 col-form-label" for="bank_address"> Bank Address</label>
                                            <div class="col-md-4">
                                                <input type="text" id="bank_address" name="bank_address" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="swift_code"> Swift Code</label>
                                            <div class="col-md-4">
                                                <input type="text" id="swift_code" name="swift_code" class="form-control" value="" >
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light account_details_add_update_btn" onclick="supp_account_details_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="account_details_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Bank Name</th>
                                                            <th>Bank Address</th>
                                                            <th>IFSC Code</th>
                                                            <th>Acc. Name</th>
                                                            <th>Acc. No</th>
                                                            <th>Ph. No</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="tab-pane" id="branch_details_tab" style="display: none">
                            <form class="was-validated branch_details_form" id="branch_details_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="branch_name"> Branch name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="branch_name" name="branch_name" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="branch_gst_no"> GST No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="branch_gst_no" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$" maxlength="15" minlength="15" name="branch_gst_no" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="branch_state_name"> State </label>
                                            <div class="col-md-4">
                                                <select name="branch_state_name" id="branch_state_name" class="select2 form-control" onchange="get_branch_cities(this.value);" required>
                                                    <?php echo $branch_state_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="branch_city_name"> City </label>
                                            <div class="col-md-4">
                                                <select name="branch_city_name" id="branch_city_name" class="select2 form-control" required>
                                                </select>
                                                <input type="hidden" name="edit_branch_city" id="edit_branch_city" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="branch_contact_no"> Contact No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="branch_contact_no" onkeypress="return isNumber(event)" maxlength="10" minlength="10" name="branch_contact_no" class="form-control" value="" required>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="branch_address"> Branch Address</label>
                                            <div class="col-md-4">
                                                <input type="text" id="branch_address" name="branch_address" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="branch_pincode">Pincode</label>
                                            <div class="col-md-4">
                                                <input type="text" onkeypress="return isNumber(event)" minlength="6" maxlength="6" id="branch_pincode" name="branch_pincode" class="form-control" value="" required>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light branch_details_add_update_btn" onclick="supp_branch_details_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="branch_details_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Branch Name</th>
                                                            <th>Branch Address</th>
                                                            <th>GST No</th>
                                                            <th>State</th>
                                                            <th>City</th>
                                                            <th>Ph. No</th>
                                                            <th>Pincode</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="tab-pane" id="billing_details_tab">
                            <form class="was-validated billing_details_form" id="billing_details_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label textright" for="billing_name">Name</label>
                                            <div class="col-md-3">
                                                <input type="text" id="billing_name" name="billing_name" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label textright" for="billing_address"> Address </label>
                                            <div class="col-md-3">
                                                <textarea name="billing_address" id="billing_address" class="form-control" rows="5"></textarea>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label textright" for="billing_country"> Country</label>
                                            <div class="col-md-3">
                                                <select name="billing_country" id="billing_country" class="select2 form-control" onchange="get_billing_states(this.value);">
                                                    <?php echo $shipping_country_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label textright" for="billing_state"> State </label>
                                            <div class="col-md-3">
                                                <select name="billing_state" id="billing_state" class="select2 form-control" onchange="get_billing_cities(this.value);">
                                                    <?php echo $shipping_state_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label textright" for="billing_city"> City </label>
                                            <div class="col-md-3">
                                                <select name="billing_city" id="billing_city" class="select2 form-control">
                                                    <?php echo $shipping_city_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label textright" for="bill_contact_name">Contact Name</label>
                                            <div class="col-md-3">
                                                <input type="text" id="bill_contact_name" name="bill_contact_name" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label textright" for="bill_contact_no">Contact No</label>
                                                <div class="col-md-3">
                                                    <input type="text" name="bill_contact_no" id="bill_contact_no" class="form-control" maxlength="10" pattern="\d{10}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                                </div>
                                            <label class="col-md-2 col-form-label textright" for="bill_gst_no">GST No</label>
                                            <div class="col-md-3">
                                                <input type="text" id="bill_gst_no" name="bill_gst_no" class="form-control"
                                                    pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
                                                    title="Enter a valid 15-digit GST number (e.g., 27ABCDE1234F1Z5)" maxlength="15">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label textright" for="gst_status_bill">GST Status</label>
                                            <div class="col-md-3">
                                                <select id="gst_status_bill" name="gst_status_bill" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="2">Inactive</option>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label textright" for="bill_ecc_no">ECC No</label>
                                            <div class="col-md-3">
                                                <input type="text" id="bill_ecc_no" name="bill_ecc_no" class="form-control"
                                                    pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{3}$"
                                                    title="Enter a valid ECC Number (e.g., ABCDE1234F001)"
                                                    maxlength="15" style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                            <button type="button" class=" btn btn-success waves-effect  waves-light billing_details_add_update_btn" onclick="billing_details_add_update()">ADD</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <!-- Table Begiins -->
                                                <table id="billing_details_datatable" class="table table-striped dt-responsive nowrap w-100">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Address</th>
                                                            <th>Country</th>
                                                            <th>State</th>
                                                            <th>City</th>
                                                            <th>Contact Name</th>
                                                            <th>Contact No</th>
                                                            <th>GST No</th>
                                                            <th>GST Status</th>
                                                            <th>ECC No</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>                                            
                                                </table>
                                                <!-- Table Ends -->
                                            </div>
                                        </div>
                                    </div> <!-- end col -->
                                </div> <!-- end row -->
                            </form>
                        </div>
                        
                        <div class="tab-pane" id="shipping_details_tab">
                            <form class="was-validated shipping_details_form" id="shipping_details_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="name">Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="name" name="name" class="form-control" value="">
                                            </div>
                                            <label class="col-md-2 col-form-label" for="shipping_address"> Address </label>
                                            <div class="col-md-4">
                                                <textarea name="shipping_address" id="shipping_address" class="form-control" rows="5"></textarea>
                                            </div>
                                        </div>
                                        <div class="form-group row ">
                                            <label class="col-md-2 col-form-label" for="country"> Country</label>
                                            <div class="col-md-4">
                                                <select name="country" id="shipping_country" class="select2 form-control" onchange="get_shipping_states(this.value);">
                                                    <?php echo $shipping_country_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="state"> State </label>
                                            <div class="col-md-4">
                                                <select name="state" id="shipping_state" class="select2 form-control" onchange="get_shipping_cities(this.value);">
                                                    <?php echo $shipping_state_options; ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="city"> City </label>
                                            <div class="col-md-4">
                                                <select name="city" id="shipping_city" class="select2 form-control">
                                                    <?php echo $shipping_city_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="contact_name">Contact Name</label>
                                            <div class="col-md-4">
                                                <input type="text" id="contact_name" name="contact_name" class="form-control" value="">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="contact_no">Contact No</label>
                                                <div class="col-md-4">
                                                    <input type="text" name="contact_no" id="contact_no" class="form-control" maxlength="10" pattern="\d{10}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                                </div>
                                            <label class="col-md-2 col-form-label" for="gst_no">GST No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="shipping_gst_no" name="shipping_gst_no" class="form-control"
                                                    pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
                                                    title="Enter a valid 15-digit GST number (e.g., 27ABCDE1234F1Z5)" maxlength="15">
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="gst_status">GST Status</label>
                                            <div class="col-md-4">
                                                <select id="gst_status" name="gst_status" class="form-control">
                                                    <option value="1">Active</option>
                                                    <option value="2">Inactive</option>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="ecc_no">ECC No</label>
                                            <div class="col-md-4">
                                                <input type="text" id="ecc_no" name="ecc_no" class="form-control"
                                                    pattern="^[A-Z]{5}[0-9]{4}[A-Z]{1}[0-9]{3}$"
                                                    title="Enter a valid ECC Number (e.g., ABCDE1234F001)"
                                                    maxlength="15" style="text-transform:uppercase;">
                                            </div>
                                        </div>
                                        
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                            <button type="button" class=" btn btn-success waves-effect  waves-light shipping_details_add_update_btn" onclick="shipping_details_add_update()">ADD</button>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <table id="shipping_details_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Name</th>
                                                            <th>Address</th>
                                                            <th>Country</th>
                                                            <th>State</th>
                                                            <th>City</th>
                                                            <th>Contact Name</th>
                                                            <th>Contact No</th>
                                                            <th>GST No</th>
                                                            <th>GST Status</th>
                                                            <th>ECC No</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>                                            
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        
                        <div class="tab-pane" id="documents_tab">
                            <form class="was-validated documents_form" id="documents_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="type">Type</label>
                                            <div class="col-md-4">
                                                <select id="type" name="type" class="form-control" onchange="showAddNewTypeInput(this)">
                                                    <?php echo $type_options; ?>
                                                    <option value="add_new">Add New...</option>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="biometric_id">Files (PAN,GST etc)  </label>
                                            <div class="col-md-4">
                                                <input type="file" multiple id="test_file_qual" name="test_file_qual[]" class="form-control dropify" data-default-file="uploads/supplier_creation/<?php echo $file_attach ?>"  >
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light documents_add_update_btn" onclick="documents_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="documents_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Type</th>
                                                            <th>Document</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Modal for Add New Document Type -->
                        <div class="modal fade" id="addDocTypeModal" tabindex="-1" aria-labelledby="addDocTypeModalLabel" aria-hidden="true">
                          <div class="modal-dialog">
                            <div class="modal-content">
                              <form id="add_doc_type_form">
                                <div class="modal-header">
                                  <h5 class="modal-title" id="addDocTypeModalLabel">Add New Document Type</h5>
                                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                  <div class="mb-3">
                                    <label for="doc_type_name" class="form-label">Name</label>
                                    <input type="text" class="form-control" id="doc_type_name" name="doc_type_name">
                                  </div>
                                </div>
                                <div class="modal-footer">
                                  <button type="submit" class="btn btn-success">Submit</button>
                                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                </div>
                              </form>
                            </div>
                          </div>
                        </div>
                        
                        
<ul class="list-inline mb-0 pager wizard">
                                <!-- <li class="previous list-inline-item disabled">
                                    <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                                </li>  -->
                              
                                <li class="next list-inline-item float-end me-0">
                                    <button type="button" id="createupdate_btn" class="btn btn-success rounded-pill createupdate_btn" onclick="supplier_master_sc(this);">
                                        <?php echo $btn_text; ?> & Continue
                                    </button>
                                </li>
                                <li class="finish list-inline-item float-end me-0">
                                    <button type="button" class="btn btn-success rounded-pill createupdate_btn_finish" onclick="final_submit();">
                                        Finish
                                    </button>
                                </li>
                                  <?php echo btn_cancel($btn_cancel);?>
                            </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
function get_msme_value(value) {
    // alert(value);
    const box = document.getElementById("msme_input_box");
    box.style.display = (value && value !== "682c28296b4d311412") ? "block" : "none";
}

document.addEventListener("DOMContentLoaded", function () {
    const msmeSelect = document.getElementById("msme_type");
    get_msme_value(msmeSelect.value); // Ensure it matches initial value on load
});
</script>

<script>
// Wait for the DOM to be fully parsed
document.addEventListener('DOMContentLoaded', function() {
  // 1) Intercept the “Add New Type” select change
  window.showAddNewTypeInput = function(select) {
    if (select.value === "add_new") {
      // Reset the text field
      document.getElementById('doc_type_name').value = '';
      // Show the modal
      const modalEl = document.getElementById('addDocTypeModal');
      const modal   = new bootstrap.Modal(modalEl);
      modal.show();
      // Clear the select after a tiny delay so the user can re‑choose
      setTimeout(() => select.value = "", 200);
    }
  };

  // 2) Hook the Add/New form submit
  const form = document.getElementById('add_doc_type_form');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      const name = document.getElementById('doc_type_name').value.trim();
      if (!name) {
        alert("Please enter a document type name.");
        return;
      }
      add_doc_type(name);  // your AJAX or logic to save the new type
      // close the modal after adding
      bootstrap.Modal.getInstance(document.getElementById('addDocTypeModal')).hide();
    });
  }
});
</script>

