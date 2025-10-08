<?php
   // This file Only PHP Functions
   include 'function.php';
//print_r($_SESSION);
//5ff71f5fb5ca556748
   
   // print_r($attendance_setting_options);
   
   // Common Variable for all form
   $btn_text               = "Save";
   $btn_action             = "create";
   $is_btn_disable         = "";
   $sess_user_type  = $_SESSION['sess_user_type'];
   $is_active              = 1;
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
   $issue_status                 = "";
   $mode_status                  = "";
   $relieve_status               = "";
   $relieve_date                 = "";
   $relieve_reason               = "";
   $salary                       = "";
   $annum_salary                 = "";
   $basic_wages                  = "";
   $annum_basic_wages            = "";
   $hra                          = "";
   $annum_hra                    = "";
   $conveyance                   = "";
   $annum_conveyance             = "";
   $medical_allowance            = "";
   $annum_medical_allowance      = "";
   $education_allowance          = "";
   $annum_education_allowance    = "";
   $other_allowance              = "";
   $annum_other_allowance        = "";
   $pf                           = "";
   $annum_pf                     = "";
   $esi                          = "";
   $branch_id                    = "";
   $annum_esi                    = "";
   $total_deduction              = "";
   $annum_total_deduction        = "";
   $net_salary                   = "";
   $annum_net_salary             = "";
   $purformance_allowance        = "";
   $annum_purformance_allowance  = "";
   $ctc                          = "";
   $annum_ctc                    = "";
   $conveyance_default_value     = "";
   $medical_default_value        = "";
   $pf_default_value             = "";
   $esi_default_value            = "";
   $educational_default_value    = "";
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
   
  if(isset($_GET["unique_id"])) {
      if (!empty($_GET["unique_id"])) {
   
          $unique_id  = $_GET["unique_id"];
          $where      = [
              "unique_id" => $unique_id
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
              "claim_status",
              "relieve_date",
              "relieve_reason",    
              "relieve_status",
              "salary",
              "annum_salary",
              "basic_wages",
              "annum_basic_wages",
              "hra",
              "annum_hra",
              "conveyance",
              "annum_conveyance",
              "medical_allowance",
              "annum_medical_allowance",
              "education_allowance",
              "annum_education_allowance",
              "other_allowance",
              "annum_other_allowance",
              "pf",
              "annum_pf",
              "esi",
              "annum_esi",
              "total_deduction",
              "annum_total_deduction",
              "net_salary",
              "annum_net_salary",
              "purformance_allowance",
              "annum_purformance_allowance",
              "ctc" ,
              "annum_ctc",     
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
              "company_name"                  
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
   
            $result_values             = $pdo->select($table_details,$where);
            $result_values_continuous  = $pdo->select($table_details_continuous,$where);

            if (($result_values->status) ||($result_values_continuous->status)) {

   
              $result_values            = $result_values->data;
              $result_values_continuous = $result_values_continuous->data;
   
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
              if(!(empty($result_values_continuous))) {
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
               
              $pre_state_options        = state("",$pre_country);
              $pre_state_options        = select_option($pre_state_options,"Select the State",$pre_state);
              $perm_state_options       = state("",$perm_country);
              $perm_state_options       = select_option($perm_state_options,"Select the State",$perm_state);
   
              $pre_city_options         = city("",$pre_state);
              $pre_city_options         = select_option($pre_city_options,"Select the City",$pre_city); 
                $perm_city_options       = city("",$perm_state);
              $perm_city_options        = select_option($perm_city_options,"Select the City",$perm_city); 
   
              $btn_text                 = "Update";
              $btn_action               = "update";
          } else {
              $btn_text                 = "Error";
              $btn_action               = "error";
              $is_btn_disable           = "disabled='disabled'";
          }
      }
  }
   
  $designation_options          = work_designation();
  $designation_options          = select_option($designation_options,"Select Designation",$designation_unique_id);
   
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
  $martial_options        = select_option($martial_options,"Select",$martial_status);
  $relieve_options        = ["Active" => [
                              "unique_id" => "Active",
                              "value"     => "Active",
                                  ],
                                  "Inactive" => [
                              "unique_id" => "Inactive",
                              "value"     => "Inactive",
                                  ],
                              ];
  $relieve_options        = select_option($relieve_options,"Select",$relieve_status);
   
  $exist_ill_options        = ["Yes" => [
                              "unique_id" => "Yes",
                              "value"     => "Yes",
                                  ],
                                  "No" => [
                              "unique_id" => "No",
                              "value"     => "No",
                                  ],
                              ];
  $exist_ill_options        = select_option($exist_ill_options,"Select",$exist_ill_status);
  $claim_options        = ["Yes" => [
                              "unique_id" => "Yes",
                              "value"     => "Yes",
                                  ],
                                  "No" => [
                              "unique_id" => "No",
                              "value"     => "No",
                                  ],
                              ];
  $claim_options        = select_option($claim_options,"Select",$claim_status);
  $issued_options        = ["Issued" => [
                              "unique_id" => "Issued",
                              "value"     => "Issued",
                                  ],
                                  "Returned" => [
                              "unique_id" => "Returned",
                              "value"     => "Returned",
                                  ],
                              ];
  $issued_options        = select_option($issued_options,"Select",$issue_status);
   
  $mode_options        = ["Two Wheeler" => [
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
  $mode_options        = select_option($mode_options,"Select",$mode_status);
  $exist_insu_options        = ["Yes" => [
                              "unique_id" => "Yes",
                              "value"     => "Yes",
                                  ],
                                  "No" => [
                              "unique_id" => "No",
                              "value"     => "No",
                                  ],
                              ];
  $exist_insu_options        = select_option($exist_insu_options,"Select",$exist_insur_status);

  $phy_chal_options        = ["Yes" => [
                              "unique_id" => "Yes",
                              "value"     => "Yes",
                                  ],
                                  "No" => [
                              "unique_id" => "No",
                              "value"     => "No",
                                  ],
                              ];
  $phy_chal_options        = select_option($phy_chal_options,"Select",$phy_chal_status);

  $bank_options     = ["Primary" => [
                        "unique_id" => "Primary",
                        "value"     => "Primary",
                          ],
                          "Secondary" => [
                        "unique_id" => "Secondary",
                        "value"     => "Secondary",
                          ],
                        ];
  $bank_options     = select_option($bank_options,"Select",$bank_status);
  $relationship_options        = ["Father" => [
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
  $relationship_options        = select_option($relationship_options,"Select",$relationship_opt);
  if($dob=='')
  {
      $date_of_birth=$today;
  }
  else
  {
      $date_of_birth=$dob;
  }
  if($doj=='')
  {
      $date_of_join=$today;
  }
  else
  {
      $date_of_join=$doj;
  }
   
   
  $country_options              = country();
   
  $pre_country_options          = select_option($country_options,"Select the Country",$pre_country);
   
  $perm_country_options         = select_option($country_options,"Select the Country",$perm_country);

  $blood_group_options          = blood_group();
  $blood_group_options          = select_option($blood_group_options,"Select Blood Group",$blood_group);

  $branch_options               = branch();
  $branch_options               = select_option($branch_options,"Select Branch",$branch_id);

  $staff_options               = staff_name();
  $staff_options               = select_option($staff_options,"Select",$reporting_officer);

  $attendance_setting_options   = attendance_setting();
  $attendance_setting_options   = select_option($attendance_setting_options,"Select Attendance Setting",$attendance_setting_id);

   $company_name_option        = company_name();
   $company_name_option        = select_option($company_name_option,"Select",$company_name);
   
   
   if($sess_user_type == '5ff71f5fb5ca556748'){
	   $req = '';
   }else{
	   $req = ' required';
   }
   

  // $active_status_options= active_status($is_active);

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
                <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-3">
                    <li class="nav-item">
                        <a href="#officialdetails_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">
                        <i class="mdi mdi-account-circle mr-1"></i>
                        <span class="d-none d-sm-inline">Staff Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#dependentdetails_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Dependent Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#account_details_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Account Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#qualification_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Qualification Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#experience_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Experience Details</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#asset_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Asset/Vehicle Details</span>
                        </a>
                    </li>
                     <?php  if(($sess_user_type == '5ff71f5fb5ca556748')||($sess_user_type == '5f97fc3257f2525529')){
                    ?>
                    <li class="nav-item">
                        <a href="#salary_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Salary Details</span>
                        </a>
                    </li>
                    <?php }?>
                    <?php  if(($sess_user_type == '5ff71f5fb5ca556748')||($sess_user_type == '5f97fc3257f2525529')){
                    ?>
                    <li class="nav-item">
                        <a href="#relieve_tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                        <i class="mdi mdi-checkbox-marked-circle-outline mr-1"></i>
                        <span class="d-none d-sm-inline">Relieve Details</span>
                        </a>
                    </li>
                    <?php }?>
                </ul>
                <div class="tab-content b-0 mb-0 pt-0">
                    <div id="bar" class="progress mb-3" style="height: 7px;">
                        <div class="bar progress-bar progress-bar-striped progress-bar-animated bg-success" style="width: 20%;"></div>
                    </div>
                    <div class="tab-pane active" id="officialdetails_tab">
                     <!-- Form Begins Here -->
                        <form class="was-validated staff_profile_form" id="staff_profile_form">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row ">
                                        <label class="col-md-6" style="color: red">Personal Details :</label>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="staff_name"> Staff Name</label>
                                        <div class="col-md-4">
                                            <input  pattern="[a-zA-Z\- \/_?:.,\s]+" type="text" id="staff_name" name="staff_name" class="form-control" value="<?php echo $staff_name; ?>" required>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="staff_id"> Staff Id</label>
                                        <div class="col-md-4">
                                            <!-- <input  type="text" id="staff_id" name="staff_id" class="form-control" value="<?php echo $employee_id; ?>"  required> -->
                                            <input type="hidden" name="staff_id" id="staff_id" class="form-control" value='<?php echo  $employee_id; ?>' required>
                                            <div class="col-md-3">
                                               <h4 class="text-info" id = "employee_id"><?php echo $employee_id; ?></h4>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="gender"> Gender</label>
                                        <div class="col-md-4">
                                            <select name="gender" id="gender" class="select2 form-control " <?=$req;?>>
                                                <option value="">Select Gender </option>
                                                <option value="1" <?php if($gender=="1") { echo "selected";} ?>>Male </option>
                                                <option value="2" <?php if($gender=="2") { echo "selected";} ?>>Female</option>
                                                <option value="3" <?php if($gender=="3") { echo "selected";} ?>>Others</option>
                                            </select>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="father_name"> Father Name</label>
                                        <div class="col-md-4">
                                            <input  pattern="[a-zA-Z\- \/_?:.,\s]+" type="text" id="father_name" name="father_name" class="form-control" value="<?php echo $father_name; ?>" <?=$req;?>>
                                        </div>
                                       </div>
                                       <div class="form-group row ">
                                          <label class="col-md-2 col-form-label" for="date_of_birth"> Date Of Birth</label>
                                          <div class="col-md-4">
                                             <input type="date" name="date_of_birth" class="form-control" value="<?php echo $date_of_birth; ?>"<?=$req;?> onChange="ageCalculate(this.value)">
                                          </div>
                                          <label class="col-md-2 col-form-label" for="doc_dob"> Document DOB</label>
                                          <div class="col-md-4">
                                             <input type="date" name="doc_dob" class="form-control" value="<?php echo $doc_dob; ?>"<?=$req;?> >
                                          </div>
                                       </div>
                                       <div class="form-group row ">
                                          <label class="col-md-2 col-form-label" for="age"> Age</label>
                                          <div class="col-md-4">
                                             <input type="text" onkeypress="return isNumber(event)" maxlength="3" id="age" name="age" class="form-control" value="<?php echo $age; ?>"  <?=$req;?>>
                                          </div>
                                        <label class="col-md-2 col-form-label" for="martial_status"> Martial Status</label>
                                        <div class="col-md-4">
                                            <select name="martial_status" id="martial_status" class="select2 form-control" <?=$req;?>><?php echo $martial_options;?></select>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="personal_contact_no"> Personal Contact No</label>
                                        <div class="col-md-4">
                                            <input type="text" onkeypress="return isNumber(event)" maxlength="10" id="personal_contact_no" name="personal_contact_no" minlength="10" class="form-control" value="<?php echo $personal_contact_no; ?>" <?=$req;?>>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="office_contact_no"> Office Contact No</label>
                                        <div class="col-md-4">
                                            <input type="text" onkeypress="return isNumber(event)" minlength="10" maxlength="10" id="office_contact_no" name="office_contact_no" class="form-control" value="<?php  echo $office_contact_no?>" <?=$req;?>>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="personal_email_id"> Personal Email Id</label>
                                        <div class="col-md-4">
                                            <input type="email" id="personal_email_id" name="personal_email_id" class="form-control" value="<?php echo $personal_email_id; ?>" <?=$req;?>>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="office_email_id"> Office Email Id</label>
                                        <div class="col-md-4">
                                            <input type="email" id="office_email_id" name="office_email_id" class="form-control" value="<?php echo $office_email_id; ?>" <?=$req;?>>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="aadhar_no"> Aadhar No</label>
                                        <div class="col-md-4">
                                            <input type="text"  id="aadhar_no" name="aadhar_no" class="form-control" value="<?php echo $aadhar_no; ?>"  minlength="14" maxlength="14" placeholder="0000 0000 0000" >
                                        </div>
                                        <label class="col-md-2 col-form-label" for="pan_no"> Pan No</label>
                                        <div class="col-md-4">
                                            <input type="text" id="pan_no" pattern="[A-Z]{5}[0-9]{4}[A-Z]{1}" maxlength="10" minlength="10"  name="pan_no" class="form-control" value="<?php echo $pan_no; ?>" >
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="claim_status"> Medical Claim</label>
                                        <div class="col-md-4">
                                            <select name="claim_status" id="claim_status" class="select2 form-control" ><?php echo $claim_options;?></select>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="blood_group"> Blood Group</label>
                                        <div class="col-md-4">
                                            <select name="blood_group" id="blood_group" class="select2 form-control" <?=$req;?>> <?php echo $blood_group_options;?></select>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="qualification"> Qualifiation</label>
                                        <div class="col-md-4">
                                            <input type="text" id="qualification" name="qualification" class="form-control" value="<?php echo $qualification; ?>" <?=$req;?>>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-6" style="color: red">Present Address :</label> 
                                        <label class="col-md-2" style="color: red">Permanent Address :</label>
                                        <div class="custom-control custom-checkbox col-md-4 ">
                                            <input type="checkbox" class="custom-control-input" id="same_address" onClick="get_permanent_address(this.value)" <?php if($same_address_status==1){?>checked <?php } ?> >
                                            <label class="custom-control-label" style="color: red"  for="same_address"> Same As Present Address </label>
                                            <input type="hidden" name="same_address_status" id="same_address_status" value='0'>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="pre_country"> Country</label>
                                        <div class="col-md-4">
                                            <select name="pre_country" id="pre_country" class="select2 form-control" onchange="get_states(this.value);" <?=$req;?>><?php echo $pre_country_options;?></select>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="perm_country"> Country</label>
                                        <div class="col-md-4">
                                            <select name="perm_country" id="perm_country" class="select2 form-control" onchange="get_perm_states(this.value);" <?=$req;?>><?php echo $perm_country_options;?></select>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="pre_state"> State </label>
                                        <div class="col-md-4">
                                            <select name="pre_state" id="pre_state" class="select2 form-control" onchange="get_cities(this.value);" <?=$req;?>> <?php echo $pre_state_options;?></select>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="perm_state"> State </label>
                                        <div class="col-md-4">
                                            <select name="perm_state" id="perm_state" class="select2 form-control" onchange="get_perm_cities(this.value);" <?=$req;?>> <?php echo $perm_state_options;?></select>
                                            <input type="hidden" name="edit_perm_state_id" id="edit_perm_state_id" value="" >
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="pre_city"> City </label>
                                        <div class="col-md-4">
                                            <select name="pre_city" id="pre_city" class="select2 form-control" <?=$req;?>><?php echo $pre_city_options;?> </select>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="perm_city_name"> City </label>
                                        <div class="col-md-4">
                                            <select name="perm_city" id="perm_city" class="select2 form-control" <?=$req;?>> <?php echo $perm_city_options;?> </select>
                                            <input type="hidden" name="edit_perm_city_id" id="edit_perm_city_id" value="" >
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="pre_building_no"> Building No</label>
                                        <div class="col-md-4">
                                            <input type="text" id="pre_building_no" name="pre_building_no" class="form-control" value="<?php echo $pre_building_no; ?>"  <?=$req;?>>
                                        </div>
                                        <label class="col-md-2 col-form-label" for="perm_building_no"> Building No</label>
                                        <div class="col-md-4">
                                            <input type="text" id="perm_building_no" name="perm_building_no" class="form-control" value="<?php echo $perm_building_no; ?>" <?=$req;?>>
                                        </div>
                                    </div>
                                    <div class="form-group row ">
                                        <label class="col-md-2 col-form-label" for="pre_street"> Street</label>
                                        <div class="col-md-4">
                                            <input type="text" id="pre_street" name="pre_street" class="form-control" value="<?php echo $pre_street; ?>" <?=$req;?>>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="perm_street"> Street</label>
                                 <div class="col-md-4">
                                    <input type="text" id="perm_street" name="perm_street" class="form-control" value="<?php echo $perm_street; ?>" <?=$req;?>>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="pre_area"> Area</label>
                                 <div class="col-md-4">
                                    <input type="text" id="pre_area" name="pre_area" class="form-control" value="<?php echo $pre_area; ?>" <?=$req;?>>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="perm_area"> Area</label>
                                 <div class="col-md-4">
                                    <input type="text" id="perm_area" name="perm_area" class="form-control" value="<?php echo $perm_area; ?>" <?=$req;?>>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="pre_pincode"> Pincode</label>
                                 <div class="col-md-4">
                                    <input type="text" id="pre_pincode" name="pre_pincode"  maxlength="6" onkeypress="return isNumber(event)"  minlength="6" class="form-control" value="<?php echo $pre_pincode; ?>" <?=$req;?>>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="perm_pincode"> Pincode</label>
                                 <div class="col-md-4">
                                    <input type="text" id="perm_pincode" maxlength="6" minlength="6" onkeypress="return isNumber(event)" name="perm_pincode" class="form-control" value="<?php echo $perm_pincode; ?>" <?=$req;?>>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-6" style="color: red">Official Details :</label>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="date_of_join"> Date of Join</label>
                                 <div class="col-md-4">
                                    <input type="date" id="date_of_join" name="date_of_join" class="form-control" value="<?php echo $date_of_join; ?>"  <?=$req;?>> 
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
                                    <input type="text" id="department" name="department" class="form-control" value="<?php echo $department; ?>"  required> 
                                 </div>
                                 <label class="col-md-2 col-form-label" for="work_location"> Work Location </label>
                                 <div class="col-md-4">
                                    <input type="text" id="work_location" name="work_location" class="form-control" value="<?php echo $work_location; ?>"  required> 
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="esi_no"> ESI No </label>
                                 <div class="col-md-4">
                                    <input type="text" id="esi_no" name="esi_no" class="form-control" value="<?php echo $esi_no; ?>"  > 
                                 </div>
                                 <label class="col-md-2 col-form-label" for="pf_no"> PF No </label>
                                 <div class="col-md-4">
                                    <input type="text" id="pf_no" name="pf_no" class="form-control" value="<?php echo $pf_no; ?>"  > 
                                 </div>
                              </div>
                              <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="biometric_id"> Bio Metric Id</label>
                                 <div class="col-md-4">
                                    <input  type="text" id="biometric_id" name="biometric_id" class="form-control" value="<?php echo $biometric_id; ?>"  >
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
                                    <input type="file" id="test_file" name="test_file" class="form-control dropify" data-default-file="uploads/staff/<?php echo $file_name ?>" >
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-6" style="color: red">For Attendance :</label>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="premises"> Premises Type</label>
                                <div class="col-md-4">
                                    <div class=" radio radio-primary form-check-inline">
                                        <input type="radio" id="premises_out" onchange="//premises_check()" value="0" name="premises_status" <?=$premises_out;?>>
                                        <label for="premises_out"> OUT Premises </label>
                                    </div>
                                    <div class="radio radio-primary form-check-inline">
                                       <input type="radio" id="premises_in" onchange="//premises_check()" value="1" name="premises_status" <?=$premises_in;?>>
                                       <label for="premises_in"> IN Premises</label>
                                    </div>
                                </div>
                                <label class="col-md-2 col-form-label" for="staff_branch "> Branch </label>
                                <div class="col-md-4">
                                <select name="branch" multiple id="branch" class="select2 form-control" onChange="get_branch_ids()"  required>
                                    <?php echo $branch_options;?>
                                    </select>
                                    <input type="hidden" id="staff_branch" name="staff_branch" class="form-control" value="<?php echo $branch_id; ?>" >
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="attendance_setting"> Attendance Setting</label>
                                <div class="col-md-4">
                                    <select name="attendance_setting" id="attendance_setting" class="select2 form-control" required>
                                    <?php echo $attendance_setting_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="reporting_officer"> Reporting Officer</label>
                                <div class="col-md-4">
                                    <select name="reporting_officer" id="reporting_officer" class="select2 form-control" required>
                                    <?php echo $staff_options;?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
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
                                    <?php echo $relationship_options;?>
                                    </select>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="rel_name"> Name</label>
                                 <div class="col-md-4">
                                    <input type="text" id="rel_name" name="rel_name" class="form-control" value=""  required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="rel_gender"> Gender</label>
                                 <div class="col-md-4">
                                     <select name="rel_gender" id="rel_gender" class="select2 form-control " required>
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
                                    <input type="text" id="rel_aadhar_no" name="rel_aadhar_no" class="form-control" maxlength="12" value=""  required>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="occupation">Occupation</label>
                                 <div class="col-md-4">
                                    <input type="text" id="occupation" name="occupation" class="form-control" value=""  required>
                                 </div>
                              </div>
                              <div class="form-group row mb-2">
                                 <label class="col-md-2 col-form-label" for="standard">Standard</label>
                                 <div class="col-md-4">
                                    <input type="text" id="standard" name="standard" class="form-control" value=""  >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="school">School</label>
                                 <div class="col-md-4">
                                    <input type="text" id="school" name="school" class="form-control" value=""  >
                                 </div>
                              </div>
                              <div class="form-group row mb-2">
                                 <label class="col-md-2 col-form-label" for="existing_illness">Existing Illness</label>
                                 <div class="col-md-4">
                                    <select name="existing_illness" id="existing_illness" class="select2 form-control" >
                                    <?php echo $exist_ill_options;?>
                                    </select>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="description">Description</label>
                                 <div class="col-md-4">
                                    <input type="text" id="description" name="description" class="form-control" value=""  >
                                 </div>
                              </div>
                              <div class="form-group row mb-2">
                                 <label class="col-md-2 col-form-label" for="existing_insurance">  Existing Insurance</label>
                                 <div class="col-md-4">
                                    <select name="existing_insurance" id="existing_insurance" class="select2 form-control" >
                                    <?php echo $exist_insu_options;?>
                                    </select>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="insurance_no">Insurance No</label>
                                 <div class="col-md-4">
                                    <input type="text" id="insurance_no" name="insurance_no" class="form-control" value=""  >
                                 </div>
                              </div>
                              <div class="form-group row mb-2">
                                <label class="col-md-2 col-form-label" for="physically_challenged"> Physcically Challenged</label>
                                 <div class="col-md-4">
                                    <select name="physically_challenged" id="physically_challenged" class="select2 form-control" >
                                    <?php echo $phy_chal_options;?>
                                    </select>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="remarks">Remarks</label>
                                 <div class="col-md-4">
                                    <input type="text" id="remarks" name="remarks" class="form-control" value=""  >
                                 </div>
                              </div>
                              <div class="form-group row mb-2 ">
                                 <div class="col text-center">
                                    <button type="button" class=" btn btn-success waves-effect  waves-light dependent_details_add_update_btn" onclick = "dependent_details_add_update()">ADD</button>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-12">
                                    <!-- Table Begiins -->
                                    <table id="dependent_details_datatable" class="table dt-responsive nowrap w-100">
                                       <thead>
                                          <tr>
                                             <th>#</th>
                                             <th>Relationship</th>
                                             <th>Name</th>
                                             <th>Gender</th>
                                             <th>DOB</th>
                                             <th>Aadhar No</th>
                                             <th>Occupation</th>
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
                                       <tbody>
                                       </tbody>
                                    </table>
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
                                <select name="salary_mode" id="salary_mode" class="select2 form-control" required><?php echo $salary_mode_options;?> </select>
                            </div>
                        </div>
                    </form> -->
                    <form class="was-validated account_details_form" id="account_details_form">
                    <div class="form-group row mb-2">
                            <label class="col-md-2 col-form-label" for="bank_status">Bank Status</label>
                            <div class="col-md-4">
                                <select name="bank_status" id="bank_status" class="select2 form-control" required onchange='get_salary_type();'><?php echo $bank_options;?> </select>
                            </div>
                            <label class="col-md-2 col-form-label" for="salary_type">Salary Type</label>
                            <div class="col-md-4">
                              <select name="salary_type" id="salary_type" class="select2 form-control" required disabled>
                                 <option value='Axis Bank'>Axis Bank</option>
                                 <option value='NEFT'>NEFT</option>
                                 <option value='Cheque'>Cheque</option>
                                 <option value='Cash'>Cash</option>
                                 <option value='Hold'>Hold</option>
                              </select>
                            </div>
                        </div>
                        <div class="row">
                           <div class="col-12">
                              <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                              <div class="form-group row ">
                                  <label class="col-md-2 col-form-label" for="accountant_name"> Accountant Name</label>
                                 <div class="col-md-4">                                                
                                    <input type="text" id="accountant_name" name="accountant_name" class="form-control" value=""  required>
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
                                    <input type="text" id="bank_contact_no" onkeypress="return isNumber(event)" maxlength="10" name="bank_contact_no" class="form-control" value=""  >
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
                                            <?php //echo $active_status_options;?>
                                        </select>
                                    </div>
                                </div> -->
                                <div class="form-group row mb-2 ">
                                    <div class="col text-center">
                                        <button type="button" class=" btn btn-success waves-effect  waves-light staff_account_details_add_update_btn" onclick="staff_account_details_add_update()">ADD</button>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                    <!-- Table Begiins -->
                                    <table id="staff_account_details_datatable" class="table dt-responsive nowrap w-100">
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
                                    </table>
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
                                    <input type="text" id="college_name" name="college_name" class="form-control" value=""  required>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="year_passing"> Year Of Passing</label>
                                 <div class="col-md-4">
                                    <input type="month" id="year_passing" name="year_passing" class="form-control" value="" required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="percentage"> Percentage</label>
                                 <div class="col-md-4">                                                
                                    <input type="text" id="percentage" onkeypress="return isNumber(event)" maxlength="10" name="percentage" class="form-control" value=""  required>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="university"> University</label>
                                 <div class="col-md-4">
                                    <input type="text" id="university" name="university" class="form-control" value="" required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="biometric_id"> Document Upload</label>
                                <div class="col-md-4">
                                    <input type="file" multiple id="test_file_qual" name="test_file_qual[]" class="form-control dropify"  >
                                    
                                       <!-- <img src="" id="doc_name"> -->
                                    
                                </div>
                            </div>
                              <div class="form-group row mb-2 ">
                                 <div class="col text-center">
                                    <button type="button" class=" btn btn-success waves-effect  waves-light qualification_add_update_btn" onclick="qualification_add_update()">ADD</button>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-12">
                                    <!-- Table Begiins -->
                                    <table id="qualification_datatable" class="table dt-responsive nowrap w-100">
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
                                    </table>
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
                                    <input type="text" id="company_name" name="company_name" class="form-control" value="" required>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="designation_name">Designation</label>
                                 <div class="col-md-4">
                                    <input type="text" id="designation_name" name="designation_name" class="form-control" value="" required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="salary_amt"> Salary</label>
                                 <div class="col-md-4">                                                
                                    <input type="text" id="salary_amt"  onkeypress="return isNumber(event)" name="salary_amt" class="form-control" value=""  onkeypress="" required>
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
                              <div class="row">
                                 <div class="col-12">
                                    <!-- Table Begiins -->
                                    <table id="experience_datatable" class="table dt-responsive nowrap w-100">
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
                                    </table>
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
                                    <input type="text" id="qty" name="qty" class="form-control"  onkeypress="return isNumber(event)" value=""  required>
                                 </div>
                                 <label class="col-md-2 col-form-label" for="status"> Status</label>
                                 <div class="col-md-4">
                                    <select name="status" id="
                                       status" class="select2 form-control" required>
                                    <?php echo $issued_options;?>
                                    </select>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-6" style="color: red">Vehicle Details :</label>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="veh_reg_no"> Vehicle Reg. No</label>
                                 <div class="col-md-4">                
                                    <input type="text" id="veh_reg_no" name="veh_reg_no" class="form-control" value=""  >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="license_mode"> License Mode</label>
                                 <div class="col-md-4">
                                    <select name="license_mode" id="
                                       license_mode" class="select2 form-control" >
                                    <?php echo $mode_options;?>
                                    </select>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="dri_license_no"> License No</label>
                                 <div class="col-md-4">                                                
                                    <input type="text" id="dri_license_no" name="dri_license_no" class="form-control" value=""  >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="license_validity"> License Validity</label>
                                 <div class="col-md-2">From
                                    <input type="date" id="valid_from" name="valid_from" class="form-control" value="" >
                                 </div>
                                 <div class="col-md-2">To
                                    <input type="date" id="valid_to" name="valid_to" class="form-control" value="" >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="vehicle_type"> Vehicle Type</label>
                                 <div class="col-md-4">                                                
                                    <input type="text" id="vehicle_type" name="vehicle_type" class="form-control" value=""  >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="vehicle_company"> Vehicle Company</label>
                                 <div class="col-md-4">
                                    <input type="text" id="vehicle_company" name="vehicle_company" class="form-control" value="" >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="vehicle_owner"> Vehicle Owner</label>
                                 <div class="col-md-4">                                                
                                    <input type="text" id="vehicle_owner" name="vehicle_owner" class="form-control" value=""  >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="registration_year"> Year of Registration</label>
                                 <div class="col-md-4">
                                    <input type="date" id="registration_year" name="registration_year" class="form-control" value="" >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="rc_no"> RC No</label>
                                 <div class="col-md-4">
                                    <input type="text" id="rc_no" name="rc_no" class="form-control" value="" >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="rc_validity"> Validity</label>
                                 <div class="col-md-2">From
                                    <input type="date" id="rc_validity_from" name="rc_validity_from" class="form-control" value="" >
                                 </div>
                                 <div class="col-md-2">To
                                    <input type="date" id="rc_validity_to" name="rc_validity_to" class="form-control" value="" >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="ins_no"> Insurance No</label>
                                 <div class="col-md-4">
                                    <input type="text" id="ins_no" name="ins_no" class="form-control" value="" >
                                 </div>
                                 <label class="col-md-2 col-form-label" for="ins_validity"> Ins Validity</label>
                                 <div class="col-md-2">From
                                    <input type="date" id="ins_validity_from" name="ins_validity_from" class="form-control" value="" >
                                 </div>
                                 <div class="col-md-2">To
                                    <input type="date" id="ins_validity_to" name="ins_validity_to" class="form-control" value="" >
                                 </div>
                              </div>
                              <div class="form-group row mb-2 ">
                                 <div class="col text-center">
                                    <button type="button" class=" btn btn-success waves-effect  waves-light asset_details_add_update_btn" onclick="asset_details_add_update()">ADD</button>
                                 </div>
                              </div>
                              <div class="row">
                                 <div class="col-12">
                                    <!-- Table Begiins -->
                                    <table id="asset_datatable" class="table dt-responsive nowrap w-100">
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
                                    </table>
                                    <!-- Table Ends -->
                                 </div>
                              </div>
                           </div>
                           <!-- end col -->
                        </div>
                        <!-- end row -->
                     </form>
                  </div>
                  <div class="tab-pane" id="salary_tab">
                     <form class="was-validated salary_form" id="salary_form">
                        <div class="row">
                           <div class="col-12">
                             <div class="form-group row ">
                                <label class="col-md-2" ></label>

                                <input type="hidden"  id="conveyance_default_value" name="conveyance_default_value" class="form-control" value="<?php  echo $conveyance_default_value?>" readonly required>
                                <input type="hidden" id="medical_default_value" name="medical_default_value" class="form-control" value="<?php  echo $medical_default_value?>" readonly required>
                                <input type="hidden" id="educational_default_value" name="educational_default_value" class="form-control" value="<?php  echo $educational_default_value?>" readonly required>
                                <input type="hidden"  id="pf_default_value" name="pf_default_value" class="form-control" value="<?php  echo $pf_default_value?>" readonly required>
                                <input type="hidden"  id="esi_default_value" name="esi_default_value" class="form-control" value="<?php  echo $esi_default_value?>" readonly required>
                                
                                 <label class="col-md-4" style="color: red">Per Month:</label> 
                                  
                                 <label class="col-md-2" style="color: red">Per Annum :</label>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="salary" style="color: red"> Gross</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)"  onKeyup="get_salary(this.value)" maxlength="6" id="salary" name="salary" class="form-control" value="<?php  echo $salary?>" required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)"  maxlength="6" id="annum_salary" name="annum_salary" class="form-control" value="<?php  echo $annum_salary?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="basic_wages"> Basic </label>
                                 
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="basic_wages" name="basic_wages" class="form-control" value="<?php  echo $basic_wages?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_basic_wages" name="annum_basic_wages" class="form-control" value="<?php  echo $annum_basic_wages?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="hra"> HRA</label>
                                <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="hra" name="hra" class="form-control" value="<?php  echo $hra?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_hra" name="annum_hra" class="form-control" value="<?php  echo $annum_hra?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="conveyance"> Conveyance</label>
                                <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="conveyance" name="conveyance" class="form-control" value="<?php  echo $conveyance?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_conveyance" name="annum_conveyance" class="form-control" value="<?php  echo $annum_conveyance?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="medical_allowance"> Medical allowanceance</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="medical_allowance" name="medical_allowance" class="form-control" value="<?php  echo $medical_allowance?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_medical_allowance" name="annum_medical_allowance" class="form-control" value="<?php  echo $annum_medical_allowance?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="education_allowance"> Education allowanceance</label>
                                <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="education_allowance" name="education_allowance" class="form-control" value="<?php  echo $education_allowance?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_education_allowance" name="annum_education_allowance" class="form-control" value="<?php  echo $annum_education_allowance?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="other_allowance"> Other allowanceance</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="other_allowance" name="other_allowance" class="form-control" value="<?php  echo $other_allowance?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_other_allowance" name="annum_other_allowance" class="form-control" value="<?php  echo $annum_other_allowance?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="pf"> PF</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="pf" name="pf" class="form-control" value="<?php  echo $pf?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_pf" name="annum_pf" class="form-control" value="<?php echo $annum_pf?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="esi"> ESI</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="esi" name="esi" class="form-control" value="<?php  echo $esi?>" readonly  required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_esi" name="annum_esi" class="form-control" value="<?php  echo $annum_esi?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="total_deduction"> Total Deduction</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="total_deduction" name="total_deduction" class="form-control" value="<?php  echo $total_deduction?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_total_deduction" name="annum_total_deduction" class="form-control" value="<?php echo $annum_total_deduction?>" readonly required>
                                 </div>
                              </div>
                               <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="net_salary" style="color: red"> Net Salary</label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="net_salary" name="net_salary" class="form-control" value="<?php  echo $net_salary?>" readonly required>
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_net_salary" name="annum_net_salary" class="form-control" value="<?php  echo $annum_net_salary?>" readonly required>
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="purformance_allowance"> Purformance allowance </label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" onkeyup="get_salary(salary.value)" maxlength="6" id="purformance_allowance" name="purformance_allowance" class="form-control" value="<?php  echo $purformance_allowance ?>"  >
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_purformance_allowance" name="annum_purformance_allowance" class="form-control" value="<?php echo $annum_purformance_allowance ?>" readonly >
                                 </div>
                              </div>
                              <div class="form-group row ">
                                 <label class="col-md-2 col-form-label" for="ctc" style="color: red"> CTC </label>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="ctc" name="ctc" class="form-control" value="<?php  echo $ctc?>"readonly required  >
                                 </div>
                                 <div class="col-md-4">
                                    <input type="text" onkeypress="return isNumber(event)" maxlength="6" id="annum_ctc" name="annum_ctc" class="form-control" value="<?php  echo $annum_ctc ?>" readonly required>
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
                  </div>
                  <ul class="list-inline mb-0 pager wizard">
                     <!-- <li class="previous list-inline-item disabled">
                        <a href="javascript: void(0);" class="btn btn-secondary">Previous</a>
                        </li>  -->
                     <?php echo btn_cancel($btn_cancel);?>
                     <li class="next list-inline-item float-right mr-0">
                        <a href="javascript: void(0);" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn"><?php echo $btn_text; ?> & Continue</a>
                     </li>
                     <li class="finish list-inline-item float-right mr-0">
                        <a href="javascript: void(0);" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn_finish">Finish</a>
                     </li>
                  </ul>
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