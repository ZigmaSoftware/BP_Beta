<?php

// This file Only PHP Functions
// include 'function.php';

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

// Common Variable for all form
$btn_text                       = "Save";
$btn_action                     = "create";
$is_btn_disable                 = "";

$unique_id                      = "";

$customer_category_id           = "";
// $customer_category_options      = "";
$customer_sub_category_id       = "";
$customer_sub_category_options  = "";
$customer_group_id              = "";
$customer_group_options         = "";
$sub_customer_type              = "";
$customer_name                  = "";
$customer_no                    = "";
$country_unique_id              = "coun5f7a05b7110cd84071"; // Default India
$state_unique_id                = "";
$city_unique_id                 = "";
$contact_no                     = "";

$gst_value                      = "";
$gst_status_yes                 = "";
$gst_status_no                  = " checked ";
$gst_no                         = "";

$provisional_value              = "";
$provisional_status_yes         = "";
$provisional_status_no          = " checked ";
$provisional_no                 = "";

$address                        = "";
$pincode                        = "";
$mobile_no                      = "";
$phone_no                       = "";
$pan_no                         = "";
$email_id                       = "";
$provisional_no                 = "";

$account_status_yes             = "";
$account_status_no              = "";

$account_type_yes               = "";
$account_type_no                = "";

$customer_category_options      = "<option value='' disabled='disabled' selected>Select Customer Category</option>";
$customer_sub_category_options  = "<option value='' disabled='disabled' selected>Select Customer Sub Category</option>";
$customer_group_options         = "<option value='' disabled='disabled' selected>Select Customer Group</option>";

$country_options                = "<option value='' disabled='disabled' selected>Select the Country</option>";
$state_options                  = "<option value='' disabled='disabled' selected>Select the State</option>";
$city_options                   = "<option value='' disabled='disabled' selected>Select the City</option>";

$shipping_state_unique_id = isset($shipping_state_unique_id) ? $shipping_state_unique_id : '';
$shipping_city_unique_id = isset($shipping_city_unique_id) ? $shipping_city_unique_id : '';


if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "customer_profile";

        $columns    = [
            "customer_name",
            "customer_no",
            // "customer_category_id",
            "customer_sub_category_id",
            "customer_group_id",
            "currency",
            "country_unique_id",
            "state_unique_id",
            "city_unique_id",
            "gst_status",
            "gst_no",
            "address",
            "pincode",
            "mobile_no",
            "phone_no",
            "pan_no",
            "email_id",     
            "provisional_status",     
            "provisional_no",
            // "account_status",
            // "account_type",
            // "crntly_acc_hndl_by",
            // "file_attach"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
        
        error_log("customer_profile: " . print_r($result_profile) . "\n", 3, "form_log.txt");

        if ($result_values->status) {

            $result_values                  = $result_values->data;

            $customer_name                  = $result_values[0]["customer_name"];
            $customer_no                    = $result_values[0]["customer_no"];
            // $customer_category_id           = $result_values[0]["customer_category_id"];
            $customer_sub_category_id       = $result_values[0]["customer_sub_category_id"];
            $customer_group_id              = $result_values[0]["customer_group_id"];
            $currency_type                  = $result_values[0]["currency"];
            $country_unique_id              = $result_values[0]["country_unique_id"];
            $state_unique_id                = $result_values[0]["state_unique_id"];
            $city_unique_id                 = $result_values[0]["city_unique_id"];
            $gst_status                     = $result_values[0]["gst_status"];
            $gst_no                         = $result_values[0]["gst_no"];
            $address                        = $result_values[0]["address"];
            $pincode                        = $result_values[0]["pincode"];
            $mobile_no                      = $result_values[0]["mobile_no"];
            $phone_no                       = $result_values[0]["phone_no"];
            $pan_no                         = $result_values[0]["pan_no"];
            $email_id                       = $result_values[0]["email_id"];
            $provisional_status             = $result_values[0]["provisional_status"];
            $provisional_no                 = $result_values[0]["provisional_no"];
            $account_status                 = $result_values[0]["account_status"];
            $account_type                   = $result_values[0]["account_type"];
            $crntly_acc_hndl_by             = $result_values[0]["crntly_acc_hndl_by"];
            $file_attach                    = $result_values[0]["file_attach"];

            error_log("Customer sub category ID: " . $customer_sub_category_id . "\n", 3, "debug.txt");

            // echo "TEst GSt ";

            if ($gst_status == 1) {
                $gst_status_yes                 = " checked ";
                $gst_status_no                  = "";
            }

            if ($account_status == 1) {
                $account_status_yes                 = " checked ";
            } else {
                $account_status_no                  = " checked ";
            }

            if ($account_type == 1) {
                $account_type_yes                 = " checked ";
            } else {
                $account_type_no                  = " checked ";
            }

            if ($provisional_status == 1) {

                $provisional_status_yes         = " checked ";
                $provisional_status_no          = "";
            }

            $state_options                  = state("",$country_unique_id);
            $state_options                  = select_option($state_options,"Select the State",$state_unique_id);

            $city_options                   = city("",$state_unique_id);
            $city_options                   = select_option($city_options,"Select the City",$city_unique_id);
            
            $shipping_city_options          = city("", $shipping_state_unique_id);
            $shipping_city_options          = select_option($shipping_city_options, "Select the City", $shipping_city_unique_id);


            $customer_sub_category_options  = customer_sub_category();


            $customer_sub_category_options  = select_option($customer_sub_category_options,"Select Customer Division",$customer_sub_category_id);

            $customer_group_options         = customer_group();
            $customer_group_options         = select_option($customer_group_options,"Select Customer Group",$customer_group_id);

            error_log("\ncustomer_group_id: $customer_group_id", 3, __DIR__ . "/debug.txt");
            error_log("\ncustomer_group_options: " . print_r($customer_group_options, true), 3, __DIR__ . "/debug.txt");

            $btn_text               = "Update";
            $btn_action             = "update";
        } else {
            // print_r($result_values);
            $btn_text               = "Error";
            $btn_action             = "error";
            $is_btn_disable         = "disabled='disabled'";
        }
        
        $unique_id  = $_GET["unique_id"];
        $where      = [
            "customer_profile_unique_id" => $unique_id
        ];
        
        $table      = "cust_statutory_details";
        
        $columns    = [
            "customer_profile_unique_id",
            "ecc_no",
            "commissionerate",
            "division",
            "stat_range",
            "cst_no",
            "tin_no",
            "service_tax_no",
            "iec_code",
            "cin_no",
            "tan_no",
            "acc_year",
            "sess_user_type",
            "sess_user_id",
            "sess_company_id",
            "sess_branch_id",
            "session_id"
        ];
        
        $table_details   = [
            $table,
            $columns
        ];
        
        $result_values  = $pdo->select($table_details, $where);
        
        if ($result_values->status) {
            $result_values = $result_values->data;
        
            $customer_profile_unique_id = $result_values[0]["customer_profile_unique_id"];
            $ecc_no                     = $result_values[0]["ecc_no"];
            $commissionerate            = $result_values[0]["commissionerate"];
            $division                   = $result_values[0]["division"];
            $stat_range                 = $result_values[0]["stat_range"];
            $cst_no                     = $result_values[0]["cst_no"];
            $tin_no                     = $result_values[0]["tin_no"];
            $service_tax_no             = $result_values[0]["service_tax_no"];
            $iec_code                   = $result_values[0]["iec_code"];
            $cin_no                     = $result_values[0]["cin_no"];
            $tan_no                     = $result_values[0]["tan_no"];
            $acc_year                   = $result_values[0]["acc_year"];
            $sess_user_type             = $result_values[0]["sess_user_type"];
            $sess_user_id               = $result_values[0]["sess_user_id"];
            $sess_company_id            = $result_values[0]["sess_company_id"];
            $sess_branch_id             = $result_values[0]["sess_branch_id"];
            $session_id                 = $result_values[0]["session_id"];
        
            // Use the values as needed in your form
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            // print_r($result_values);
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }

        
    }
}


$country_options                = country();
$country_options                = select_option($country_options,"Select the Country",$country_unique_id);

$shipping_country_options       = country();
$shipping_country_options       = select_option($shipping_country_options, "Select the Country");

$account_year_options           = account_year();
$account_year_options           = select_option($account_year_options,"Select the Account Year");

$item_group_options             = item_group();
$item_group_options             = select_option($item_group_options,"Select the Account Year");

// $customer_category_options      = customer_category();
// $customer_category_options      = select_option($customer_category_options,"Select Customer Category",$customer_category_id);

$customer_sub_category_options  = customer_sub_category();
$customer_sub_category_options  = select_option($customer_sub_category_options,"Select Customer Division",$customer_sub_category_id);

$customer_group_options         = customer_group();
$customer_group_options         = select_option($customer_group_options,"Select Customer Group",$customer_group_id);
            
// print_r($customer_group_options);

$executive_options              = staff_name();
$executive_options              = select_option($executive_options,"Select Executive Name",$crntly_acc_hndl_by);

$currency_options               = currency_creation_name();
$currency_options               = select_option($currency_options,"Select the Currency Type",$currency_type);

$type_options                   = doc_type_options();
$type_options                   = select_option($type_options,"Select the Document Type",$doc_type);

?>

<!-- Unique ID hidden input -->
<input type="hidden" name="unique_id" id="unique_id" value="<?php echo $unique_id; ?>">
<input type="hidden" name="customer_unique_id" id="customer_unique_id" value="<?php echo $unique_id; ?>">

<input type="hidden" id="user_id" value="<?php echo $_SESSION['user_id']; ?>">
<input type="hidden" id="user_type_id" value="<?php echo $_SESSION['sess_user_type']; ?>">
<input type="hidden" id="customer_name" value="0">
<input type="hidden" id="contact_value" value="0">
<input type="hidden" id="cpm_value" value="0">
<input type="hidden" id="bd_value" value="0">
<input type="hidden" id="billing_value" value="0">
<input type="hidden" id="shipping_value" value="0">
<input type="hidden" id="tab_count" value="0">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div id="customercreatewizard">
                    <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-3">
                        <li class="nav-item">
                            <a href="#profile_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                                <i class="mdi mdi-account-circle mr-1"></i>
                                <span class="d-none d-sm-inline">Profile</span>
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
                        <!--<li class="nav-item">-->
                        <!--    <a href="#customer_potential_mapping_tab" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                        <!--        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>-->
                        <!--        <span class="d-none d-sm-inline">Customer Potential Mapping</span>-->
                        <!--    </a>-->
                        <!--</li>-->
                        <li class="nav-item">
                            <a href="#account_details_tab"data-bs-toggle="tab"  data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                                <span class="d-none d-sm-inline">Bank Details</span>
                            </a>
                        </li>
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
                    
                            <div class="tab-pane active" id="profile_tab">
                            <!-- Form Begins Here -->
                                <form class="was-validated customer_profile_form" id="customer_profile_form">
                                    <div class="row">                                
                                        <div class="col-12">
                                            
                                            <div class="form-group row ">

                                                <label class="col-md-2 col-form-label textright" for="customer_no"> Customer No </label>
                                                <div class="col-md-3">
                                                    <input type="text" readonly id="customer_no" name="customer_no" class="form-control border-0 text-primary font-weight-bold" value="<?php echo $customer_no; ?>" >
                                                </div>
                                                
                                                <!-- <label class="col-md-2 col-form-label textright " for="customer_category"> Customer Category</label>
                                                <div class="col-md-3">
                                                    <select name="customer_category" id="customer_category" class="select2 form-control" onchange = "customer_sub_category_and_group(this.value)" required>
                                                    </select>
                                                </div> -->
                                                
                                                <label class="col-md-2 col-form-label textright" for="customer_name"> Customer Name</label>
                                                <div class="col-md-3">
                                                    <input pattern="[a-zA-Z\- \/_?:.,\s\(\)]+" type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo $customer_name; ?>"  required>
                                                </div>
                                                
                                                
                                            </div>
                                            <div class="form-group row ">
                                                <!-- <label class="col-md-2 col-form-label textright" for="customer_name"> Customer Name</label>
                                                <div class="col-md-3">
                                                    <input pattern="[a-zA-Z\- \/_?:.,\s\(\)]+" type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo $customer_name; ?>"  required>
                                                </div> -->
                                                <label class="col-md-2 col-form-label textright" for="customer_group"> Customer Group</label>
                                                <div class="col-md-3">
                                                    <select name="customer_group" id="customer_group" class="select2 form-control" required>
                                                        <?=$customer_group_options;?>
                                                    </select>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="customer_sub_category"> Customer Division</label>
                                                <div class="col-md-3">
                                                    <select name="customer_sub_category" id="customer_sub_category" class="select2 form-control">
                                                        <?=$customer_sub_category_options;?>
                                                    </select>
                                                </div>
                                                                                                
                                            </div>
                                            <div class="form-group row ">
                                                <!-- <label class="col-md-2 col-form-label textright" for="customer_group"> Customer Group</label>
                                                <div class="col-md-3">
                                                    <select name="customer_group" id="customer_group" class="select2 form-control" required>
                                                        <?php // echo $customer_group_options; ?>
                                                    </select>
                                                </div> -->
                                                <label class="col-md-2 col-form-label textright" for="currency"> Currency</label>
                                                <div class="col-md-3">
                                                    <select name="currency" id="currency" class="select2 form-control" required>
                                                        <?=$currency_options;?>
                                                    </select>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="country_name"> Country</label>
                                                <div class="col-md-3">
                                                    <select name="country_name" id="country_name" class="select2 form-control" onchange="get_states(this.value);" required>
                                                        <?php echo $country_options;?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row ">

                                                <label class="col-md-2 col-form-label textright" for="state_name"> State </label>
                                                <div class="col-md-3">
                                                    <select name="state_name" id="state_name" class="select2 form-control" onchange="get_cities(this.value);" required>
                                                        <?php echo $state_options;?>
                                                    </select>
                                                </div>

                                                <label class="col-md-2 col-form-label textright" for="city_name"> City </label>
                                                <div class="col-md-3">
                                                    <select name="city_name" id="city_name" class="select2 form-control" required>
                                                        <?php echo $city_options;?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="form-group row ">

                                                <label class="col-md-2 col-form-label textright" for="address"> Address </label>
                                                <div class="col-md-3">
                                                    <textarea name="address" id="address" class="form-control" rows="5"><?=$address;?></textarea>
                                                </div>

                                                <label class="col-md-2 col-form-label textright" for="pincode"> Pincode </label>
                                                <div class="col-md-3">
                                                    <input type="text" name="pincode" id="pincode" class="form-control" onkeypress="number_only();" minlength="6" maxlength="6" value="<?=$pincode;?>">
                                                </div>

                                            </div>

                                            <div class="form-group row ">

                                                <label class="col-md-2 col-form-label textright" for="mobile_no"> Mobile No </label>
                                                <div class="col-md-3">
                                                    <input type="text" name="mobile_no" id="mobile_no" class="form-control" onkeypress="number_only(event);" minlength="10" maxlength="10" size="10"  value="<?=$mobile_no;?>">
                                                </div>

                                                <label class="col-md-2 col-form-label textright" for="phone_no"> Phone No </label>
                                                <div class="col-md-3">
                                                    <input type="text" name="phone_no" id="phone_no" class="form-control" onkeypress="number_only(event);" minlength="10" maxlength="10" size="10"  value="<?=$phone_no;?>">
                                                </div>

                                            </div>

                                            <div class="form-group row ">

                                                <label class="col-md-2 col-form-label textright" for="email"> Email </label>
                                                <div class="col-md-3">
                                                    <input type="email" name="email" id="email" class="form-control" value="<?=$email_id;?>">
                                                </div>

                                                <label class="col-md-2 col-form-label textright" for="pan_no"> PAN </label>
                                                <div class="col-md-3">
                                                    <input type="text" name="pan_no" id="pan_no" class="form-control" value="<?=$pan_no;?>">
                                                </div>

                                            </div>
                                            
                                                                                        <div class="form-group row ">
                                                
                                                <label class="col-md-2 col-form-label textright" for="gst_status"> GST Registered </label>
                                                <div class="col-md-3">
                                                    <div class="radio radio-primary form-check-inline">
                                                        <input type="radio" id="gst_status_yes" onchange="gst_check()" value="1" name="gst_status" <?=$gst_status_yes;?>>
                                                        <label for="gst_status_yes"> Yes</label>
                                                    </div>
                                                    <div class=" radio radio-primary form-check-inline">
                                                        <input type="radio" id="gst_status_no" onchange="gst_check()" value="0" name="gst_status" <?=$gst_status_no;?>>
                                                        <label for="gst_status_no"> No </label>
                                                    </div>
                                                </div>   
                                                

                                                <label class="col-md-2 col-form-label textright gst_no_div" for="country_name"> GST NO</label>
                                                <div class="col-md-3 gst_no_div">
                                                    <input type="text" id="gst_no" name="gst_no" class="form-control" pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$" value="<?php echo $gst_no; ?>" required>
                                                </div>

                                            </div>

                                            <div class="form-group row ">
                                                
                                                <label class="col-md-2 col-form-label textright" for="provisional_status"> GST Provisional </label>
                                                <div class="col-md-3">
                                                    <div class="radio radio-primary form-check-inline">
                                                        <input type="radio" id="provisional_status_yes" onchange="provisional_check()" value="1" name="provisional_status" <?=$provisional_status_yes;?>>
                                                        <label for="provisional_status_yes"> Yes</label>
                                                    </div>
                                                    <div class=" radio radio-primary form-check-inline">
                                                        <input type="radio" id="provisional_status_no" onchange="provisional_check()" value="0" name="provisional_status" <?=$provisional_status_no;?>>
                                                        <label for="provisional_status_no"> No </label>
                                                    </div>
                                                </div>                                               

                                                <label class="col-md-2 col-form-label textright provisional_no_div" for="country_name"> Provisional No</label>
                                                <div class="col-md-3 provisional_no_div">
                                                    <input type="text" id="provisional_no" name="provisional_no" class="form-control" value="<?php echo $provisional_no; ?>" required>
                                                </div>

                                            </div>

                                            
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="tab-pane" id="contactperson_tab">
                                <form class="was-validated contact_person_form" name="contact_person_form" id="contact_person_form" novalidate>
                                    <div class="row">                                
                                        <div class="col-12">
                                            <!-- <h4 class="header-title">Contact Person </h4> -->
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="contact_person_name"> Contact Person Name</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="contact_person_name" name="contact_person_name" class="form-control" value="" required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="contact_person_designation"> Designation</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="contact_person_designation" name="contact_person_designation" class="form-control" value="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row d-none">
                                                <label class="col-md-2 col-form-label textright" for="contact_person_address1" > Address 1</label>
                                                <div class="col-md-3">
                                                    
                                                    <textarea name="contact_person_address1" class="form-control" id="contact_person_address1" rows="5"  ></textarea>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="contact_person_address2"> Address 2</label>
                                                <div class="col-md-3">
                                                    
                                                    <textarea name="contact_person_address2" class="form-control" id="contact_person_address2" rows="5"  ></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="contact_person_email"> Email </label>
                                                <div class="col-md-3">
                                                    <input type="email" id="contact_person_email" name="contact_person_email" class="form-control" value="" >
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="contact_person_contact_no">Mobile No</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="contact_person_contact_no" name="contact_person_contact_no" class="form-control" value="" onkeypress="number_only(event);" minlength="10" maxlength="10" size="10" required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2 ">
                                                <div class="col text-center">
                                                <button type="button" class="btn btn-success waves-effect  waves-light contact_person_add_update_btn" onclick="contact_person_add_update()">ADD</button>
                                                </div>
                                            </div>

                                            
                                            <div class="row">
                                                <div class="col-12">
                                                    <!-- Table Begiins -->
                                                    <table id="contact_person_datatable" class="table table-striped dt-responsive nowrap w-100">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Person Name</th>
                                                                <th>Designation</th>
                                                                <!-- <th>Address 1</th> -->
                                                                <th>Email</th>
                                                                <th>Mobile No</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>                                            
                                                    </table>
                                                    <!-- Table Ends -->
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

                            <!-- <div class="tab-pane" id="invoice_details_tab"> -->
                                <!-- <form class="was-validated invoice_details_form" id="invoice_details_form"> -->
                                    <!-- <div class="row">                                    
                                        <div class="col-12">
                                            <h4 class="header-title">Delivery / Invoice Details </h4>
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="delivery_details"> Delivery Details</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="delivery_details" name="delivery_details" class="form-control" value="" required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="invoice_details"> Invoice Details</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="invoice_details" name="invoice_details" class="form-control" value=""  required>
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="transport_courier_details"> Transport /Courier Details</label>
                                                <div class="col-md-3">
                                                    
                                                    <textarea name="transport_courier_details" class="form-control" id="transport_courier_details" rows="5"  required></textarea>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="gst_no"> GST No</label>
                                                <div class="col-md-3">                                                
                                                    <input type="text" id="gst_no" name="gst_no" class="form-control" value=""  required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="tan_no">TAN No</label>
                                                <div class="col-md-4">
                                                    <input type="text" id="tan_no" name="tan_no" class="form-control" value=""  required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="pan_no">PAN No</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="pan_no" name="pan_no" class="form-control" value=""  required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="web_address">Web Address</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="web_address" name="web_address" class="form-control" value=""  required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="email_id">Email ID</label>
                                                <div class="col-md-3">
                                                    <input type="email" id="email_id" name="email_id" class="form-control" value=""  required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2 ">
                                                <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light invoice_details_add_update_btn" onclick = "invoice_details_add_update()">ADD</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <table id="invoice_details_datatable" class="table table-striped dt-responsive nowrap w-100">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Delivery Details</th>
                                                                <th>Invoice Details</th>
                                                                <th>Trans./Courier Details</th>
                                                                <th>GST NO</th>
                                                                <th>TAN NO</th>
                                                                <th>PAN NO</th>
                                                                <th>Web Address</th>
                                                                <th>Email ID</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>                                            
                                                    </table>
                                                </div>
                                            </div> -->
                                        <!-- </div>
                                    </div> -->
                                <!-- </form> --> 
                            <!-- </div> -->

                            <div class="tab-pane" id="customer_potential_mapping_tab" style="display: none">
                                <form class="was-validated customer_potential_mapping_form" id="customer_potential_mapping_form">
                                    <div class="row">
                                        <div class="col-12">

                                            <div class="form-group row ">
                                                
                                                <label class="col-md-2 col-form-label textright" for="financial_year"> Financial Year</label>
                                                <div class="col-md-3">
                                                    <select name="financial_year" id="financial_year" class="select2 form-control" onchange="get_states(this.value);" required>
                                                        <?php echo $account_year_options;?>
                                                    </select>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="product_group"> Product Group</label>
                                                <div class="col-md-3">
                                                    <select name="product_group" id="product_group" class="select2 form-control" onchange="get_states(this.value);" required>
                                                        <?php echo $item_group_options;?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="potential_value"> Potential Value</label>
                                                <div class="col-md-3">                                             
                                                    <input type="text" id="potential_value" name="potential_value" onkeypress="number_only(event)" class="form-control" value="" required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="bis_forcast">Ascent Bis Forcast</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="bis_forcast" name="bis_forcast" onkeypress="number_only(event)" class="form-control" value=""  required>
                                                </div>
                                                
                                            </div>

                                            <div class="form-group row mb-2 ">
                                                <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light customer_potential_mapping_add_update_btn" onclick="customer_potential_mapping_add_update()">ADD</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <!-- Table Begiins -->
                                                    <table id="customer_potential_mapping_datatable" class="table table-striped dt-responsive nowrap w-100">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Financial Year</th>
                                                                <th>Protuct Group</th>
                                                                <th class="text-right">Potential Value</th>
                                                                <th class="text-right">Ascent Bis Value</th>
                                                                <th>Action</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>                                            
                                                    </table>
                                                    <!-- Table Ends -->
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div> <!-- end row -->
                                </form>
                            </div>

                            <div class="tab-pane" id="account_details_tab" >
                                <form class="was-validated account_details_form" id="account_details_form">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="bank_name"> Bank name</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="bank_name" name="bank_name" class="form-control" value="" required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="bank_address"> Bank Address</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="bank_address" name="bank_address" class="form-control" value="" required>
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="ifsc_code"> IFSC Code</label>
                                                <div class="col-md-3">
                                                    
                                                    <input type="text" id="ifsc_code" name="ifsc_code" class="form-control" value="" required>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="beneficiary_account_name"> Beneficiary Account Name</label>
                                                <div class="col-md-3">                                                
                                                    <input type="text" id="beneficiary_account_name" name="beneficiary_account_name" class="form-control" value=""  required>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="account_no">Account Number</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="account_no" name="account_no" class="form-control" value="" required>
                                                </div>
                                                
                                            </div>
                                            <div class="form-group row mb-2 ">
                                                <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light account_details_add_update_btn" onclick="account_details_add_update()">ADD</button>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <!-- Table Begiins -->
                                                    <table id="account_details_datatable" class="table table-striped dt-responsive nowrap w-100">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Bank Name</th>
                                                                <th>Bank Address</th>
                                                                <th>IFSC Code</th>
                                                                <th>Acc. Name</th>
                                                                <th>Acc. No</th>
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
                                                <label class="col-md-2 col-form-label textright" for="name">Name</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="name" name="name" class="form-control" value="">
                                                    <div class="col-md-2">
                                                        <div class="form-check mt-1 mb-1">
                                                            <input type="checkbox" id="copy_info" name="copy_info" class="form-check-input">
                                                            <label class="form-check-label" for="copy_info">Same as billing address</label>
                                                        </div>
                                                    </div>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="shipping_address"> Address </label>
                                                <div class="col-md-3">
                                                    <textarea name="shipping_address" id="shipping_address" class="form-control" rows="5"></textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row ">
                                                <label class="col-md-2 col-form-label textright" for="country"> Country</label>
                                                <div class="col-md-3">
                                                    <select name="country" id="shipping_country" class="select2 form-control" onchange="get_shipping_states(this.value);">
                                                        <?php echo $shipping_country_options; ?>
                                                    </select>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="state"> State </label>
                                                <div class="col-md-3">
                                                    <select name="state" id="shipping_state" class="select2 form-control" onchange="get_shipping_cities(this.value);">
                                                        <?php echo $shipping_state_options; ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="city"> City </label>
                                                <div class="col-md-3">
                                                    <select name="city" id="shipping_city" class="select2 form-control">
                                                        <?php echo $shipping_city_options; ?>
                                                    </select>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="contact_name">Contact Name</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="contact_name" name="contact_name" class="form-control" value="">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="contact_no">Contact No</label>
                                                    <div class="col-md-3">
                                                        <input type="text" name="contact_no" id="contact_no" class="form-control" maxlength="10" pattern="\d{10}" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);">
                                                    </div>
                                                <label class="col-md-2 col-form-label textright" for="gst_no">GST No</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="shipping_gst_no" name="shipping_gst_no" class="form-control"
                                                        pattern="^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}$"
                                                        title="Enter a valid 15-digit GST number (e.g., 27ABCDE1234F1Z5)" maxlength="15">
                                                </div>
                                            </div>
                                            <div class="form-group row mb-2">
                                                <label class="col-md-2 col-form-label textright" for="gst_status">GST Status</label>
                                                <div class="col-md-3">
                                                    <select id="gst_status" name="gst_status" class="form-control">
                                                        <option value="1">Active</option>
                                                        <option value="2">Inactive</option>
                                                    </select>
                                                </div>
                                                <label class="col-md-2 col-form-label textright" for="shipping_ecc_no">ECC No</label>
                                                <div class="col-md-3">
                                                    <input type="text" id="shipping_ecc_no" name="shipping_ecc_no" class="form-control"
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
                                                    <!-- Table Begiins -->
                                                    <table id="shipping_details_datatable" class="table table-striped dt-responsive nowrap w-100">
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

                        <div class="tab-pane" id="documents_tab">
                            <form class="was-validated documents_form" id="documents_form">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="type">Type</label>
                                            <div class="col-md-4">
                                                <select id="type" name="type" class="form-control" onchange="showAddNewTypeInput(this)" required> 
                                                    <?php echo $type_options; ?>
                                                    <option value="add_new">Add New...</option>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="biometric_id">Files (PAN,GST etc)  </label>
                                            <div class="col-md-4">
                                                <input type="file" multiple id="test_file_qual" name="test_file_qual[]" required class="form-control dropify" data-default-file="uploads/supplier_creation/<?php echo $file_attach ?>"  >
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
                                    <button type="button" id="createupdate_btn" class="btn btn-success rounded-pill createupdate_btn" onclick="item_master_sc(this);">
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

                        </div> <!-- tab-content -->
                    </div> <!-- end #customercreatewizard-->
                <!-- </form> -->

            </div> <!-- end card-body -->
        </div> <!-- end card-->
    </div> <!-- end col -->

</div>


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

