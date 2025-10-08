<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database able Names
$table_staff_official               = "staff_test";
$table_staff_official_continuous    = "staff_continuous_test";
$table_dependent_details            = "staff_dependent_details_test";
$table_staff_asset                  = "staff_asset_details_test";
$table_staff_qualification_details  = "staff_qualification_details_test";
$table_staff_experience_details     = "staff_experience_details_test";
$table_account_details              = "staff_account_details_test";
$staff_employment_status            = "staff_employment_status";
$staff_stat_table                   = "staff_stat_details";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Include this folder only functions
include 'function.php';

// File Upload Library Call
$fileUpload         = new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );

$fileUploadPath = $fileUploadConfig->get("upload_folder");

// Create Folder in root->uploads->(this_folder_name) Before using this file upload
$fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name . DIRECTORY_SEPARATOR);
// $fileUploadPath = $fileUploadConfig->get("upload_folder");

// Create Folder in root->uploads->(this_folder_name) Before using this file upload
// $fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name . DIRECTORY_SEPARATOR);

// File Upload Library Call
// $fileUpload         = new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";
$prefix             = "staff";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $staff_name           = $_POST["staff_name"];
        $employee_id          = $_POST["staff_id"];
        $date_of_join         = $_POST["date_of_join"];
        $employment_type      = $_POST["employment_type"];
        $skill_level          = $_POST["skill_level"];
        $designation          = $_POST["designation"];
        $biometric_id         = $_POST["biometric_id"];
        $salary_category      = $_POST["salary_category"];
        $age                  = $_POST["age"];
        $gender               = $_POST["gender"];
        $martial_status       = $_POST["martial_status"];
        $date_of_birth        = $_POST["date_of_birth"];
        $personal_contact_no  = $_POST["personal_contact_no"];
        $office_contact_no    = $_POST["office_contact_no"];
        $personal_email_id    = $_POST["personal_email_id"];
        $office_email_id      = $_POST["office_email_id"];
        $blood_group          = $_POST["blood_group"];
        $pre_country          = $_POST["pre_country"];
        $perm_country         = $_POST["perm_country"];
        $pre_state            = $_POST["pre_state"];
        $perm_state           = $_POST["perm_state"];
        $pre_city             = $_POST["pre_city"];
        $perm_city            = $_POST["perm_city"];
        $pre_building_no      = $_POST["pre_building_no"];
        $perm_building_no     = $_POST["perm_building_no"];
        $pre_area             = $_POST["pre_area"];
        $perm_area            = $_POST["perm_area"];
        $pre_street           = $_POST["pre_street"];
        $perm_street          = $_POST["perm_street"];
        $pre_pincode          = $_POST["pre_pincode"];
        $perm_pincode         = $_POST["perm_pincode"];
        $unique_id            = $_POST["unique_id"];
        $aadhar_no            = $_POST["aadhar_no"];
        $pan_no               = $_POST["pan_no"];
        $claim_status         = $_POST["claim_status"];
        $same_address_status  = $_POST["same_address_status"];
        $work_location        = $_POST["work_location"];
        $department           = $_POST["department"];
        $premises_status      = $_POST["premises_status"];
        $staff_branch         = $_POST["staff_branch"];
        $attendance_setting   = $_POST["attendance_setting"];
        $reporting_officer    = $_POST["reporting_officer"];
        $father_name          = $_POST["father_name"];
        $mother_name          = $_POST["mother_name"];
        $doc_dob              = $_POST["doc_dob"];
        $qualification        = $_POST["qualification"];
        $graduation_type      = $_POST["graduation_type"];
        $company_name         = $_POST['company_name'];
        $staff_company_name         = $_POST['staff_company_name'];
        $grade                = $_POST["grade"];
        $esi_no                  = $_POST["esi_no"];
        $pf_no                   =$_POST["pf_no"];
        // $relieve_status       = $_POST["relieve_status"];
        $update_where         = "";

        $columns            = [
            "staff_name"            => $staff_name,
            "employee_id"           => $employee_id,
            "date_of_join"          => $date_of_join,
            "employment_type"       => $employment_type,
            "skill_level"           => $skill_level,
            "designation_unique_id" => $designation,
            "biometric_id"          => $biometric_id,
            "salary_category"       => $salary_category,
            "age"                   => $age,
            "gender"                => $gender,
            "martial_status"        => $martial_status,
            "date_of_birth"         => $date_of_birth,
            "personal_contact_no"   => $personal_contact_no,
            "office_contact_no"     => $office_contact_no,
            "personal_email_id"     => $personal_email_id,
            "office_email_id"       => $office_email_id,
            "blood_group"           => $blood_group,
            "pre_country"           => $pre_country, 
            "pre_state"             => $pre_state, 
            "pre_city"              => $pre_city,      
            "pre_building_no"       => $pre_building_no,     
            "pre_street"            => $pre_street,        
            "pre_area"              => $pre_area,     
            "pre_pincode"           => $pre_pincode,  
            "perm_country"          => $perm_country, 
            "perm_state"            => $perm_state,    
            "perm_city"             => $perm_city,  
            "perm_building_no"      => $perm_building_no,  
            "perm_street"           => $perm_street,   
            "perm_area"             => $perm_area,       
            "perm_pincode"          => $perm_pincode, 
            "aadhar_no"             => $aadhar_no,
            "pan_no"                => $pan_no,  
            "claim_status"          => $claim_status,
            "same_address_status"   => $same_address_status,
            "department"            => $department,
            "work_location"         => $work_location,
            "premises_type"         => $premises_status,
            "branch_id"             => ltrim($staff_branch,","),   
            "attendance_setting_id" => $attendance_setting,
            "reporting_officer"     => $reporting_officer,
            "father_name"           => $father_name,
            "mother_name"           => $mother_name,
            "doc_dob"               => $doc_dob,
            "qualification"         => $qualification,
            "graduation_type"       => $graduation_type,
            "company_name"          => $company_name,  
            "grade"                 => $grade,
            "esi_no"                 => $esi_no,
            "pf_no"                 => $pf_no,
            "unique_id"             => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_staff_official,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
       
        $select_where       = ' is_delete = 0 AND is_active = 1  AND employee_id = "'.$employee_id.'" ';
        // if( $table_details['graduation_type']==1){
        //     $table_details['graduation_type']='UG';
        // }
        // else if( $table_details['graduation_type']==2){
        //     $table_details['graduation_type']='PG';
        // }
        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_staff_official,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_staff_official,$columns);
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "create";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $staff_unique_id = "";

        if ($unique_id) {
            $staff_unique_id = $unique_id;
        } else {
            $staff_unique_id = $columns['unique_id'];
        }

        $json_array   = [
            "status"                => $status,
            "data"                  => $data,
            "error"                 => $error,
            "msg"                   => $msg,
            "sql"                   => $sql,
            "staff_unique_id"    => $staff_unique_id
        ];

        echo json_encode($json_array);

        break;

    case 'statcreateupdate':

        // Start logging
        error_log("[$current_datetime] | User: $current_user | Starting statcreateupdate operation\n", 3, "error_log.txt");
        
        try {
            $staff_unique_id = $_POST["staff_unique_id"];
            $unique_id = $_POST["unique_id"];
            
            error_log("[$current_datetime] | User: $current_user | Processing for staff_unique_id: $staff_unique_id\n", 3, "error_log.txt");
        
            $columns = [
                "pf_applicable"         => $_POST["pf_applicable"],
                "employee_pf_ceiling"   => $_POST["employee_pf_ceiling"],
                "pf_joining_date"       => $_POST["pf_joining_date"],
                "uan_number"            => $_POST["uan_number"],
                "vpf"                   => $_POST["vpf"],
                "pf_pension"            => $_POST["pf_pension"],
                "employer_pf_ceiling"   => $_POST["employer_pf_ceiling"],
                "pf_number"             => $_POST["pf_number"],
                "pf_wage"              => $_POST["pf_wage"],
                "esic_applicable"       => $_POST["esic_applicable"],
                "pt_applicable"         => $_POST["pt_applicable"],
                "it_applicable"         => $_POST["it_applicable"],
                "nps_applicable"        => $_POST["nps_applicable"],
                "lwf_applicable"        => $_POST["lwf_applicable"],
                "gratuity_applicable"   => $_POST["gratuity_applicable"],
                "tax_regime"            => $_POST["tax_regime"],
                "tax_updated_at"        => $_POST["tax_updated_at"],
                "tax_updated_by"        => $_POST["tax_updated_by"],
                "tax_no_pan"            => $_POST["tax_no_pan"],
                "decimal_rates"         => $_POST["decimal_rates"],
                "unique_id"             => $staff_unique_id
            ];
        
            error_log("[$current_datetime] | User: $current_user | Data prepared for update/insert\n", 3, "error_log.txt");
            error_log("[$current_datetime] | User: $current_user | Data: " . json_encode($columns) . "\n", 3, "error_log.txt");
        
            // Check if record exists
            $table_details = [
                $staff_stat_table,
                ["*"]
            ];
        
            $select_where = ' is_delete = 0 AND is_active = 1 AND unique_id = "' . $staff_unique_id . '" ';
            
            error_log("[$current_datetime] | User: $current_user | Checking existing record with query: $select_where\n", 3, "error_log.txt");
            
            $action_obj = $pdo->select($table_details, $select_where);
        
            if ($action_obj->status) {
                if ($action_obj->data) {
                    // Record exists - Update
                    error_log("[$current_datetime] | User: $current_user | Record exists - Performing update\n", 3, "error_log.txt");
                    
                    $update_where = [
                        "unique_id" => $staff_unique_id
                    ];
                    $action_obj = $pdo->update($staff_stat_table, $columns, $update_where);
                    $msg = "update";
                    
                    error_log("[$current_datetime] | User: $current_user | Update completed with status: {$action_obj->status}\n", 3, "error_log.txt");
                } else {
                    // Record doesn't exist - Insert
                    error_log("[$current_datetime] | User: $current_user | Record not found - Performing insert\n", 3, "error_log.txt");
                    
                    $action_obj = $pdo->insert($staff_stat_table, $columns);
                    $msg = "create";
                    
                    error_log("[$current_datetime] | User: $current_user | Insert completed with status: {$action_obj->status}\n", 3, "error_log.txt");
                }
        
                if ($action_obj->status) {
                    $status = $action_obj->status;
                    $data = $action_obj->data;
                    $error = "";
                    $sql = $action_obj->sql;
                    
                    error_log("[$current_datetime] | User: $current_user | Operation successful: $msg\n", 3, "error_log.txt");
                } else {
                    $status = $action_obj->status;
                    $data = $action_obj->data;
                    $error = $action_obj->error;
                    $sql = $action_obj->sql;
                    $msg = "error";
                    
                    error_log("[$current_datetime] | User: $current_user | Operation failed. Error: {$action_obj->error}\n", 3, "error_log.txt");
                }
            } else {
                $status = $action_obj->status;
                $data = $action_obj->data;
                $error = $action_obj->error;
                $sql = $action_obj->sql;
                $msg = "error";
                
                error_log("[$current_datetime] | User: $current_user | Select query failed. Error: {$action_obj->error}\n", 3, "error_log.txt");
            }
        
        } catch (Exception $e) {
            $status = false;
            $data = null;
            $error = $e->getMessage();
            $sql = "";
            $msg = "error";
            
            error_log("[$current_datetime] | User: $current_user | Exception occurred: {$e->getMessage()}\n", 3, "error_log.txt");
            error_log("[$current_datetime] | User: $current_user | Stack trace: {$e->getTraceAsString()}\n", 3, "error_log.txt");
        }
        
        $json_array = [
            "status"          => $status,
            "data"            => $data,
            "error"           => $error,
            "msg"             => $msg,
            "sql"             => $sql,
            "staff_unique_id" => $staff_unique_id
        ];
        
        error_log("[$current_datetime] | User: $current_user | Response prepared: " . json_encode($json_array) . "\n", 3, "error_log.txt");
        error_log("[$current_datetime] | User: $current_user | Operation completed\n", 3, "error_log.txt");
        
        echo json_encode($json_array);
    break;
    
    case 'calculate_salary':
        $staff_id = $_POST['staff_unique_id'];
        // $project_id = $_POST['project_id'];
        
        error_log("POST: " . print_r($_POST, true) . "\n", 3, "post_data.txt");
        $monthly = [
            'basic', 'hra', 'statutory_bonus', 'special_allowance', 'other_allowance'
        ];
    
        // Set defaults
        $data = [];
        foreach ($monthly as $field) {
            $data[$field] = isset($_POST[$field]) && is_numeric($_POST[$field]) ? floatval($_POST[$field]) : 0;
        }
    
        // Fetch statutory settings
        $table_details = [
            $staff_stat_table,
            ['*']
        ];
        $select_where = 'unique_id = "' . $staff_id . '" AND is_delete = 0 AND is_active = 1';
        $stat_result = $pdo->select($table_details, $select_where);
    
        if (!$stat_result->status || empty($stat_result->data)) {
            echo json_encode(['error' => 'Statutory settings not found']);
            exit;
        }
    
        $stat = $stat_result->data[0];
    
        // Default rates
        $pf_rate = 0.12;
        $esi_rate_employee = 0.0075;
        $esi_rate_employer = 0.0325;
        $nps_rate = 0.10;
        // $lwf_amount = 200; // Can vary by state in future
        
        $project_id = fetch_project($staff_id)[0]['work_location'];
        
        $lwf_amount = lwf_value($project_id)[0]['amount'];
        // error_log("lwf: " . print_r($lwf_value, true) . "\n", 3, "lwf_log.txt");
    
        $pf_wage_limit = 15000;
        $esi_wage_limit = 21000;
    
        // Gross salary
        $gross = array_sum($data);
    
        // PF calculation
        $pf_employee = 0;
        $pf_employer = 0;
        if ($stat['pf_applicable'] == 'Yes') {
            $ceiling_applicable = $stat['employee_pf_ceiling'] == 'Yes';
            $pf_base = ($ceiling_applicable && $data['basic'] > $pf_wage_limit) ? 0 : $data['basic'];
            $pf_employee = round($pf_base * $pf_rate);
            $pf_employer = round($pf_base * $pf_rate);
        }
    
        // ESIC calculation
        $esi_employee = 0;
        $esi_employer = 0;
        if ($stat['esic_applicable'] == 'Yes' && $gross <= $esi_wage_limit) {
            $esi_employee = round($gross * $esi_rate_employee);
            $esi_employer = round($gross * $esi_rate_employer);
        }
    
        // LWF
        $lwf_employee = $stat['lwf_applicable'] == 'Yes' ? $lwf_amount : 0;
        $lwf_employer = $lwf_employee;
    
        // PT
        $pt = $stat['pt_applicable'] == 'Yes' ? 200 : 0; // TODO: Add slab logic
    
        // NPS
        $nps = $stat['nps_applicable'] == 'Yes' ? round($gross * $nps_rate) : 0;
    
        // Total deduction
        $total_deduction = $pf_employee + $esi_employee + $lwf_employee + $pt + $nps;
        $net_salary = $gross - $total_deduction;
    
        // CTC
        $ctc = $gross + $pf_employer + $esi_employer + $lwf_employer;
    
        // Response
        echo json_encode([
            'pf_employee' => $pf_employee,
            'pf_employer' => $pf_employer,
            'esi_employee' => $esi_employee,
            'esi_employer' => $esi_employer,
            'lwf_employee' => $lwf_employee,
            'lwf_employer' => $lwf_employer,
            'pt' => $pt,
            'nps' => $nps,
            'total_deduction' => $total_deduction,
            'net_salary' => $net_salary,
            'ctc' => $ctc
        ]);
    break;

    
    case "get_statuatory_details":
        $current_datetime = '2025-07-11 09:39:41';
        $current_user = 'SIWNUS';
        
        error_log("[$current_datetime] | User: $current_user | Starting get_statuatory_details operation\n", 3, "error_log.txt");
    
        try {
            $staff_unique_id = $_POST["staff_unique_id"];
            error_log("[$current_datetime] | User: $current_user | Fetching details for staff_unique_id: $staff_unique_id\n", 3, "error_log.txt");
    
            // Set up query details
            $table_details = [
                $staff_stat_table,
                ["*"]
            ];
            
            $select_where = ' is_delete = 0 AND is_active = 1 AND unique_id = "' . $staff_unique_id . '" ';
            
            error_log("[$current_datetime] | User: $current_user | Executing select query with condition: $select_where\n", 3, "error_log.txt");
            
            $action_obj = $pdo->select($table_details, $select_where);
    
            if ($action_obj->status) {
                $status = $action_obj->status;
                $data = $action_obj->data;
                $error = "";
                $sql = $action_obj->sql;
                $msg = "success";
                
                error_log("[$current_datetime] | User: $current_user | Data fetched successfully\n", 3, "error_log.txt");
            } else {
                $status = $action_obj->status;
                $data = $action_obj->data;
                $error = $action_obj->error;
                $sql = $action_obj->sql;
                $msg = "error";
                
                error_log("[$current_datetime] | User: $current_user | Failed to fetch data. Error: {$action_obj->error}\n", 3, "error_log.txt");
            }
    
        } catch (Exception $e) {
            $status = false;
            $data = null;
            $error = $e->getMessage();
            $sql = "";
            $msg = "error";
            
            error_log("[$current_datetime] | User: $current_user | Exception occurred: {$e->getMessage()}\n", 3, "error_log.txt");
            error_log("[$current_datetime] | User: $current_user | Stack trace: {$e->getTraceAsString()}\n", 3, "error_log.txt");
        }
    
        $json_array = [
            "status"          => $status,
            "data"            => $data,
            "error"           => $error,
            "msg"             => $msg,
            "sql"             => $sql,
            "staff_unique_id" => $staff_unique_id
        ];
    
        error_log("[$current_datetime] | User: $current_user | Response prepared: " . json_encode($json_array) . "\n", 3, "error_log.txt");
        error_log("[$current_datetime] | User: $current_user | Operation completed\n", 3, "error_log.txt");
    
        echo json_encode($json_array);
    break;
    
    case 'salarycreateupdate':

        $basic                      = $_POST["basic"];
        $annum_basic                = $_POST["annum_basic"];
        $hra                        = $_POST["hra"];
        $annum_hra                  = $_POST["annum_hra"];
        $statutory_bonus            = $_POST["statutory_bonus"];
        $annum_statutory_bonus      = $_POST["annum_statutory_bonus"];
        $special_allowance          = $_POST["special_allowance"];
        $annum_special_allowance    = $_POST["annum_special_allowance"];
        $other_allowance            = $_POST["other_allowance"];
        $annum_other_allowance      = $_POST["annum_other_allowance"];
        
        $pf_employer                = $_POST["pf_employer"];
        $annum_pf_employer          = $_POST["annum_pf_employer"];
        $esi_employer               = $_POST["esi_employer"];
        $annum_esi_employer         = $_POST["annum_esi_employer"];
        $lwf_employer               = $_POST["lwf_employer"];
        $annum_lwf_employer         = $_POST["annum_lwf_employer"];
        
        $pf_employee                = $_POST["pf_employee"];
        $annum_pf_employee          = $_POST["annum_pf_employee"];
        $esi_employee               = $_POST["esi_employee"];
        $annum_esi_employee         = $_POST["annum_esi_employee"];
        $lwf_employee               = $_POST["lwf_employee"];
        $annum_lwf_employee         = $_POST["annum_lwf_employee"];
        
        $pt                         = $_POST["pt"];
        $annum_pt                   = $_POST["annum_pt"];
        $nps                        = $_POST["nps"];
        $annum_nps                  = $_POST["annum_nps"];
        
        $total_deduction            = $_POST["total_deduction"];
        $annum_total_deduction      = $_POST["annum_total_deduction"];
        $net_salary                 = $_POST["net_salary"];
        $annum_net_salary           = $_POST["annum_net_salary"];
        $ctc                        = $_POST["ctc"];
        $annum_ctc                  = $_POST["annum_ctc"];
        
        $purformance_allowance       = $_POST["purformance_allowance"];
        $annum_purformance_allowance = $_POST["annum_purformance_allowance"];
        
        $pf_default_value           = $_POST["pf_default_value"];
        $esi_default_value          = $_POST["esi_default_value"];
        $conveyance_default_value   = $_POST["conveyance_default_value"];
        $medical_default_value      = $_POST["medical_default_value"];
        $educational_default_value  = $_POST["educational_default_value"];
        
        $unique_id                  = $_POST["unique_id"];
        $staff_unique_id            = $_POST["staff_unique_id"];

       
        $update_where         = "";

         $update_where_insert   = [
                    "unique_id"     => $staff_unique_id
                ];

        $columns = [
            "salary"                         => $net_salary, // Mapping to net_salary as 'salary' not posted
            "annum_salary"                   => $annum_net_salary, // Same mapping for annual
            "basic_wages"                    => $basic,
            "annum_basic_wages"              => $annum_basic,
            "hra"                            => $hra,
            "annum_hra"                      => $annum_hra,
            "stat_bonus"                     => $statutory_bonus,
            "annum_stat_bonus"               => $statutory_bonus * 12,
            "special_allowance"              => $special_allowance,
            "annum_special_allowance"        => $special_allowance * 12,
            "conveyance"                     => $conveyance_default_value,
            "annum_conveyance"               => $conveyance_default_value * 12,
            "medical_allowance"              => $medical_default_value,
            "annum_medical_allowance"        => $medical_default_value * 12,
            "education_allowance"            => $educational_default_value,
            "annum_education_allowance"      => $educational_default_value * 12,
            "other_allowance"                => $other_allowance,
            "annum_other_allowance"          => $annum_other_allowance,
            "pf"                             => $pf_employee,  // assuming pf = employee pf
            "annum_pf"                       => $annum_pf_employee,
            "esi"                            => $esi_employee,
            "annum_esi"                      => $annum_esi_employee,
            "total_deduction"                => $total_deduction,
            "annum_total_deduction"          => $annum_total_deduction,
            "net_salary"                     => $net_salary,
            "annum_net_salary"               => $annum_net_salary,
            "purformance_allowance"          => $purformance_allowance,
            "annum_purformance_allowance"    => $annum_purformance_allowance,
            "ctc"                            => $ctc,
            "annum_ctc"                      => $annum_ctc
        ];

        
        error_log("Columns for salary: " . print_r($columns, true) . "\n", 3, "error_log.txt");

        $columns_continuous            = [
            "conveyance_default_value"       => $conveyance_default_value,
            "medical_default_value"          => $medical_default_value,
            "pf_default_value"               => $pf_default_value,
            "esi_default_value"              => $esi_default_value,
            "educational_default_value"      => $educational_default_value,
            "unique_id"                      => $staff_unique_id
        ];
        // check already Exist Or not
        $table_details      = [
            $table_staff_official,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
       
       if($unique_id) {

                unset($columns['unique_id']);
                unset($columns_continuous['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj            = $pdo->update($table_staff_official,$columns,$update_where);
                $action_obj_continuous = $pdo->update($table_staff_official_continuous,$columns_continuous,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
               // $action_obj     = $pdo->insert($table_staff_official,$columns);
                $action_obj_continuous = $pdo->insert($table_staff_official_continuous,$columns_continuous);
                $action_obj            = $pdo->update($table_staff_official,$columns,$update_where_insert);
                          // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "create";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        //}

        $staff_unique_id = "";

        if ($unique_id) {
            $staff_unique_id = $unique_id;
        } else {
            $staff_unique_id = $columns['unique_id'];
        }

        $json_array   = [
            "status"                => $status,
            "data"                  => $data,
            "error"                 => $error,
            "msg"                   => $msg,
            "sql"                   => $sql,
            "staff_unique_id"    => $staff_unique_id
        ];

        echo json_encode($json_array);

        break;
        
    case 'get_salary_details':
        try {
            $staff_unique_id = $_POST['staff_unique_id'] ?? '';
    
            if (!$staff_unique_id) {
                error_log("[$current_datetime] | get_salary_details | Missing staff_unique_id\n", 3, "error_log.txt");
                echo json_encode([
                    'status' => false,
                    'error' => 'Missing staff_unique_id',
                    'data' => null
                ]);
                break;
            }
    
            error_log("[$current_datetime] | get_salary_details | staff_unique_id: $staff_unique_id\n", 3, "error_log.txt");
    
            $table_details = [
                $table_staff_official,
                ['basic_wages', 'hra', 'stat_bonus', 'special_allowance', 'other_allowance']
            ];
    
            $where = 'is_delete = 0 AND is_active = 1 AND unique_id = "' . $staff_unique_id . '"';
    
            error_log("[$current_datetime] | get_salary_details | Running SELECT from $staff_salary_table\n", 3, "error_log.txt");
            error_log("[$current_datetime] | get_salary_details | WHERE: $where\n", 3, "error_log.txt");
    
            $result = $pdo->select($table_details, $where);
    
            error_log("[$current_datetime] | get_salary_details | PDO Result: " . json_encode($result) . "\n", 3, "error_log.txt");
    
            if ($result->status && !empty($result->data)) {
                error_log("[$current_datetime] | get_salary_details | Data Fetched: " . json_encode($result->data[0]) . "\n", 3, "error_log.txt");
    
                echo json_encode([
                    'status' => true,
                    'data' => $result->data[0],
                    'error' => '',
                    'sql' => $result->sql ?? ''
                ]);
            } else {
                $errorMsg = $result->error ?? 'No data found';
                error_log("[$current_datetime] | get_salary_details | Error: $errorMsg\n", 3, "error_log.txt");
    
                echo json_encode([
                    'status' => false,
                    'data' => null,
                    'error' => $errorMsg,
                    'sql' => $result->sql ?? ''
                ]);
            }
    
        } catch (Exception $e) {
            error_log("[$current_datetime] | get_salary_details | Exception: " . $e->getMessage() . "\n", 3, "error_log.txt");
            error_log("[$current_datetime] | get_salary_details | Stack Trace: " . $e->getTraceAsString() . "\n", 3, "error_log.txt");
    
            echo json_encode([
                'status' => false,
                'data' => null,
                'error' => $e->getMessage(),
                'sql' => ''
            ]);
        }
    break;


    case 'accountcreateupdate':

        
        $salary_mode                 = $_POST["salary_mode"];
        $unique_id                   = $_POST["unique_id"];
        $staff_unique_id             = $_POST["staff_unique_id"];
       
        $update_where         = "";

         $update_where_insert   = [
                    "unique_id"     => $staff_unique_id
                ];

        $columns            = [
            "salary_mode"              => $salary_mode,
            
        ];

       
        // check already Exist Or not
        $table_details      = [
            $table_staff_official,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
       
       if($unique_id) {

                unset($columns['unique_id']);
               

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj            = $pdo->update($table_staff_official,$columns,$update_where);
               

            // Update Ends
            } else {

                // Insert Begins            
                // $action_obj     = $pdo->insert($table_staff_official,$columns);
                $action_obj_continuous = $pdo->insert($table_staff_official_continuous,$columns_continuous);
                
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "create";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        //}

        $staff_unique_id = "";

        if ($unique_id) {
            $staff_unique_id = $unique_id;
        } else {
            $staff_unique_id = $columns['unique_id'];
        }

        $json_array   = [
            "status"                => $status,
            "data"                  => $data,
            "error"                 => $error,
            "msg"                   => $msg,
            "sql"                   => $sql,
            "staff_unique_id"    => $staff_unique_id
        ];

        echo json_encode($json_array);

        break;
case 'relievecreateupdate':

         $relieve_reason       = $_POST["relieve_reason"];
        $relieve_date         = $_POST["relieve_date"];
        $relieve_status       = $_POST["relieve_status"];
        $unique_id            = $_POST["unique_id"];
       
        $update_where         = "";

        $columns            = [
            "relieve_date"          => $relieve_date,   
            "relieve_reason"         => $relieve_reason,       
            "relieve_status"        => $relieve_status, 
            "unique_id"           => $unique_id
        ];

        // check already Exist Or not
        $table_details      = [
            $table_staff_official,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
       // $select_where       = 'unique_id ="'.$designation.'" AND is_delete = 0  AND staff_name = "'.$staff_name.'"   AND employee_id = "'.$employee_id.'" ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= '  unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
        //     $msg        = "already";
        // } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_staff_official,$columns,$update_where);
                // print_r($action_obj);
            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_staff_official,$columns);
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "create";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $staff_unique_id = "";

        if ($unique_id) {
            $staff_unique_id = $unique_id;
        } else {
            $staff_unique_id = $columns['unique_id'];
        }

        $json_array   = [
            "status"                => $status,
            "data"                  => $data,
            "error"                 => $error,
            "msg"                   => $msg,
            "sql"                   => $sql,
            "staff_unique_id"    => $staff_unique_id
        ];

        echo json_encode($json_array);

        break;

    // Contact person Section Starts  

    case 'asset_details_add_update':

            $asset_name         = $_POST["asset_name"];
            $staff_unique_id    = $_POST["staff_unique_id"];
            $item_no            = $_POST["item_no"];
            $qty                = $_POST["qty"];
            $asset_status             = $_POST["asset_status"];
            $veh_reg_no         = $_POST["veh_reg_no"];
            $license_mode       = $_POST["license_mode"];
            $dri_license_no     = $_POST["dri_license_no"];
            $valid_from         = $_POST["valid_from"];
            $valid_to           = $_POST["valid_to"];
            $vehicle_type       = $_POST["vehicle_type"];
            $vehicle_company    = $_POST["vehicle_company"];
            $vehicle_owner      = $_POST["vehicle_owner"];
            $registration_year  = $_POST["registration_year"];
            $rc_no              = $_POST["rc_no"];
            $rc_validity_from   = $_POST["rc_validity_from"];
            $rc_validity_to     = $_POST["rc_validity_to"];
            $ins_no             = $_POST["ins_no"];
            $ins_validity_from  = $_POST["ins_validity_from"];
            $ins_validity_to    = $_POST["ins_validity_to"];
            $unique_id          = $_POST["unique_id"];
    
            $update_where               = "";
    
            $columns            = [
                "staff_unique_id"  => $staff_unique_id,
                "asset_name"       => $asset_name,
                "item_no"          => $item_no,
                "qty"              => $qty,
                "asset_status"           => $asset_status,
                "veh_reg_no"       => $veh_reg_no,
                "license_mode"     => $license_mode,
                "dri_license_no"   => $dri_license_no,
                "valid_from"       => $valid_from,
                "valid_to"         => $valid_to,
                "vehicle_type"     => $vehicle_type,
                "vehicle_company"  => $vehicle_company,
                "vehicle_owner"    => $vehicle_owner,
                "registration_year"=> $registration_year,
                "rc_no"            => $rc_no,
                "rc_validity_from" => $rc_validity_from,
                "rc_validity_to"   => $rc_validity_to,
                "ins_no"           => $ins_no,
                "ins_validity_from"=> $ins_validity_from,
                "ins_validity_to"  => $ins_validity_to,
                "unique_id"        => unique_id($prefix)
            ];
    
            // check already Exist Or not
            $table_details      = [
                $table_staff_asset,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'asset_name ="'.$asset_name.'" AND is_delete = 0  AND item_no = "'.$item_no.'" AND asset_status = "'.$asset_status.'" ';
    
            // When Update Check without current id
            if ($unique_id) {
                $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
            }
    
            $action_obj         = $pdo->select($table_details,$select_where);
    
            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;
    
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
                
            }
            if ($data[0]["count"]) {
                $msg        = "already";
            } else if (($data[0]["count"] == 0) && ($msg != "error")) {
                // Update Begins
                if($unique_id) {
    
                    unset($columns['unique_id']);
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_staff_asset,$columns,$update_where);
    
                // Update Ends
                } else {
    
                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_staff_asset,$columns);
                    // Insert Ends
    
                }
    
                if ($action_obj->status) {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = "";
                    $sql        = $action_obj->sql;
    
                    if ($unique_id) {
                        $msg        = "update";
                    } else {
                        $msg        = "add";
                    }
                } else {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = $action_obj->error;
                    $sql        = $action_obj->sql;
                    $msg        = "error";
                }
            }
    
            $json_array   = [
                "status"    => $status,
                "data"      => $data,
                "error"     => $error,
                "msg"       => $msg,
                "sql"       => $sql
            ];
    
            echo json_encode($json_array);
    
        break;

    case 'asset_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "asset_details";
        
        // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        // DataTable 
		$search 	= $_POST['search']['value'];    
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
                "asset_name",
                "item_no",
                "qty",
                "asset_status",
                "veh_reg_no", 
                "dri_license_no",
                "license_mode",
                "vehicle_type",
                "vehicle_company",
                "vehicle_owner",
                "registration_year",
                "rc_no",
                "ins_no",
                "unique_id"
        ];
        $table_details  = [
            $table_staff_asset." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                // if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                // }
                if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                    $value['unique_id']     = $btn_edit.$btn_delete;
                }else if($_SESSION['sess_user_type']  != '5f97fc3257f2525529'){
                    $value['unique_id']     = $btn_edit;
                }
                // $value['unique_id']     = $btn_edit.$btn_delete;
                $data[]                 = array_values($value);
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case "asset_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
                "asset_name",
                "item_no",
                "qty",
                "asset_status",
                "veh_reg_no",
                "license_mode",
                "dri_license_no",
                "valid_from",
                "valid_to",
                "vehicle_type",
                "vehicle_company",
                "vehicle_owner",
                "registration_year",
                "rc_no",
                "rc_validity_from",
                "rc_validity_to",
                "ins_no",
                "ins_validity_from",
                "ins_validity_to",
                "unique_id"
        ];
        $table_details  = [
            $table_staff_asset,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data" 		=> $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"	=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'asset_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_staff_asset,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        
        break;

    case 'employment_status_add_update':
    
        $effective_from     = $_POST['effective_from'];
        $effective_to       = $_POST['effective_to'];
        $conf_due_date      = $_POST['conf_due_date'];
        $conf_date          = $_POST['conf_date'];
        $employment_status  = $_POST['employment_status'];
        $staff_unique_id    = $_POST['staff_unique_id']; // assuming this is passed
        $unique_id          = $_POST['unique_id'];
        $update_where       = "";
        $msg                = "";
    
        $columns = [
            "staff_unique_id"      => $staff_unique_id,
            "effective_from"       => $effective_from,
            "effective_to"         => $effective_to,
            "conf_due_date"        => $conf_due_date,
            "conf_date"            => $conf_date,
            "employment_status"    => $employment_status,
            "unique_id"            => unique_id($prefix)
        ];
    
        // Check if record with same dates and status already exists for the staff
        $table_details = [
            $staff_employment_status, // <-- make sure this is defined properly
            [
                "COUNT(unique_id) AS count"
            ]
        ];
    
        $select_where = 'staff_unique_id = "'.$staff_unique_id.'" AND effective_from = "'.$effective_from.'" AND employment_status = "'.$employment_status.'" AND is_delete = 0';
    
        if ($unique_id) {
            $select_where .= ' AND unique_id != "'.$unique_id.'" ';
        }
    
        $action_obj = $pdo->select($table_details, $select_where);
    
        if ($action_obj->status) {
            $data   = $action_obj->data;
            $error  = "";
            $sql    = $action_obj->sql;
        } else {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = "error";
        }
    
        if ($data[0]["count"]) {
            $msg = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update
            if ($unique_id) {
                unset($columns['unique_id']);
    
                $update_where = [
                    "unique_id" => $unique_id
                ];
    
                $action_obj = $pdo->update($staff_employment_status, $columns, $update_where);
            }
            // Insert
            else {
                $action_obj = $pdo->insert($staff_employment_status, $columns);
            }
    
            if ($action_obj->status) {
                $status = $action_obj->status;
                $data   = $action_obj->data;
                $error  = "";
                $sql    = $action_obj->sql;
                $msg    = ($unique_id) ? "update" : "add";
            } else {
                $status = $action_obj->status;
                $data   = $action_obj->data;
                $error  = $action_obj->error;
                $sql    = $action_obj->sql;
                $msg    = "error";
            }
        }
    
        echo json_encode([
            "status" => $status,
            "data"   => $data,
            "error"  => $error,
            "msg"    => $msg,
            "sql"    => $sql
        ]);
    
    break;
    
    case 'employment_status_datatable':
    
        $btn_edit_delete    = "employment_status";
    
        $staff_unique_id    = $_POST['staff_unique_id'];
    
        $search             = $_POST['search']['value'];    
        $length             = $_POST['length'];
        $start              = $_POST['start'];
        $draw               = $_POST['draw'];
        $limit              = $length;
    
        $data               = [];
    
        if($length == '-1') {
            $limit = "";
        }
    
        // Define columns to fetch
        $columns = [
            "@a:=@a+1 AS s_no",
            "effective_from",
            "effective_to",
            "conf_due_date",
            "conf_date",
            "employment_status",
            "unique_id"
        ];
    
        $table_details = [
            $staff_employment_status . " , (SELECT @a:=" . $start . ") AS a",
            $columns
        ];
    
        $where = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"          => 1,
            "is_delete"          => 0
        ];
    
        $order_by = "";
    
        $sql_function = "SQL_CALC_FOUND_ROWS";
    
        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();
    
        if ($result->status) {
            $res_array = $result->data;
    
            foreach ($res_array as $key => $value) {
                $btn_edit = btn_edit($btn_edit_delete, $value['unique_id']);
                $btn_delete = btn_delete($btn_edit_delete, $value['unique_id']);
    
                if ($_SESSION['sess_user_type'] == '5f97fc3257f2525529') {
                    $value['unique_id'] = $btn_edit . $btn_delete;
                } else {
                    $value['unique_id'] = $btn_edit;
                }
    
                $data[] = array_values($value);
            }
    
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data
                // "testing"           => $result->sql
            ];
        } else {
            print_r($result);
            exit;
        }
    
        echo json_encode($json_array);
    break;
    
    case "employment_status_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
    
        // Query Variables
        $json_array     = "";
        $columns        = [
            "effective_from",
            "effective_to",
            "conf_due_date",
            "conf_date",
            "employment_status",
            "unique_id"
        ];
        $table_details  = [
            $staff_employment_status,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        
    
        $result         = $pdo->select($table_details, $where);
    
        if ($result->status) {
            $json_array = [
                "data" 		=> $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"	=> $result->sql
            ];
        } else {
            print_r($result);
        }
    
        echo json_encode($json_array);
    break;
    
    case "employment_status_delete":
    
        $unique_id  = $_POST['unique_id'];
    
        $columns = [
            "is_delete" => 1,
        ];
    
        $update_where = [
            "unique_id" => $unique_id
        ];
    
        $action_obj = $pdo->update($staff_employment_status, $columns, $update_where);
    
        if ($action_obj->status) {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = "";
            $sql    = $action_obj->sql;
            $msg    = "success_delete";
        } else {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = "error";
        }
    
        $json_array = [
            "status" => $status,
            "data"   => $data,
            "error"  => $error,
            "msg"    => $msg,
            "sql"    => $sql
        ];
    
        echo json_encode($json_array);
    break;




    // Invoice Details Section Starts  

    case 'dependent_details_add_update':

        $relationship          = $_POST["relationship"];
        $rel_name              = $_POST["rel_name"];
        $rel_gender            = $_POST["rel_gender"];
        $rel_date_of_birth     = $_POST["rel_date_of_birth"];
        $rel_aadhar_no         = $_POST["rel_aadhar_no"];
        $occupation            = $_POST["occupation"];
        $standard              = $_POST["standard"];
        $school                = $_POST["school"];
        $existing_illness      = $_POST["existing_illness"];
        $description           = $_POST["description"];
        $existing_insurance    = $_POST["existing_insurance"];
        $insurance_no          = $_POST["insurance_no"];
        $physically_challenged = $_POST["physically_challenged"];
        $remarks               = $_POST["remarks"];
        $staff_unique_id       = $_POST["staff_unique_id"]; 
        $unique_id             = $_POST["unique_id"];
        $update_where          = "";

        $columns            = [
            "staff_unique_id"        => $staff_unique_id, 
            "relationship"           => $relationship,
            "name"                   => $rel_name,
            "gender"                 => $rel_gender,
            "date_of_birth"          => $rel_date_of_birth,
            "aadhar_no"              => $rel_aadhar_no,
            "occupation"             => $occupation,
            "standard"               => $standard,
            "school"                 => $school,
            "existing_illness"       => $existing_illness,
            "illness_description"    => $description,
            "existing_insurance"     => $existing_insurance,
            "insurance_no"           => $insurance_no,
            "physically_challenged"  => $physically_challenged,
            "remarks"                => $remarks,
            "unique_id"              => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_dependent_details,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'relationship ="'.$relationship.'" AND is_delete = 0  AND name = "'.$rel_name.'"  ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_dependent_details,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_dependent_details,$columns);
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

    break;

    case 'dependent_details_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "dependent_details";
        
        // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        // DataTable 
		$search 	= $_POST['search']['value'];    
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "relationship",
            "name",
            "gender",
            "date_of_birth",
            "aadhar_no",
            "occupation",
            "standard",
            "school",
            "existing_illness",
            "illness_description",
            "existing_insurance",
            "insurance_no",
            "physically_challenged",
            "remarks",
            "unique_id"
        ];
        $table_details  = [
            $table_dependent_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if ($value['gender'] == "1") {
                    $value['gender']   = "Male";
                } else if($value['gender'] == "2") {

                    $value['gender']   = "Female";
                }
                else if($value['gender'] == "3") {

                    $value['gender']   = "Others";
                }
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);

                
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                // print_r($_SESSION['sess_user_type']);
                if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                $value['unique_id']     = $btn_edit.$btn_delete;
            }else if($_SESSION['sess_user_type']  != '5f97fc3257f2525529'){
                $value['unique_id']     = $btn_edit;
            }
                $data[]                 = array_values($value);

                 
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
        
    case "dependent_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "relationship",
            "name",
            "gender",
            "date_of_birth",
            "aadhar_no",
            "occupation",
            "standard",
            "school",
            "existing_illness",
            "illness_description",
            "existing_insurance",
            "insurance_no",
            "physically_challenged",
            "remarks",
            "unique_id"
        ];
        $table_details  = [
            $table_dependent_details,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data" 		=> $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"	=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'dependent_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_dependent_details,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        
    break;
    
   
    case 'datatable':

        // DataTable Variables
		$search 	= $_POST['search']['value'];    
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        
        $columns        = [
            "@a:=@a+1 s_no",
            "employee_id",
            "staff_name",
            "date_of_birth",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = ".$table_staff_official.".designation_unique_id ) AS designation_type",
            "(SELECT department FROM department_creation AS department WHERE department.unique_id =".$table_staff_official.".department) AS department",
            "(SELECT company_name FROM company_creation AS company_name WHERE company_name.unique_id = ".$table_staff_official.".company_name) AS company_name",
            "(SELECT CONCAT(project_code, '/', project_name) FROM project_creation AS pc WHERE pc.unique_id =".$table_staff_official.".work_location) AS work_location",
            "is_active",
            "unique_id"
        ];
        
        $table_details  = [
            $table_staff_official." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        
        $order_by       = " employee_id DESC ";

        $sess_user_type  = $_SESSION['sess_user_type'];

        if($_POST['status'] == 0){
            $where = " is_delete = '0'";
        }else if($_POST['status'] == 1){
            $where = " is_delete = '0' and relieve_date = '' ";
        }else{
            $where = " is_delete = '0' and relieve_date != '' ";
        }
   
        if($_POST['company_name']){
            $company_name = $_POST['company_name'];
            $where .= " AND company_name = '$company_name'";
        }
		
        error_log("company: " .print_r($where, true) . "\n", 3, "logs/where.txt" );
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND (";
            }

            $where .= $search.")";
        }

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        error_log("result: " .print_r($result, true) . "\n", 3, "logs/result.txt" );
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $value['date_of_birth'] = disdate($value['date_of_birth']);
                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
            
                unset($value['is_active']); // hide status text in UI
            
                $value['unique_id'] = $btn_update . $btn_toggle;
                $data[] = array_values($value);
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;
    
   case 'qualification_add_update':

        $staff_unique_id         = $_POST["staff_unique_id"];        
        $education_type          = $_POST["education_type"];
        $degree                  = $_POST["degree"];
        $college_name            = $_POST["college_name"];
        $year_passing            = $_POST["year_passing"];
        $percentage              = $_POST["percentage"];
        $university              = $_POST["university"];
        $unique_id               = $_POST["unique_id"];
        $update_where                       = "";
        

        if (is_array($_FILES["test_file"]['name'])) {
           
            if ($_FILES["test_file"]['name'][0] != "") {
 
                // Multi file Upload 
                $confirm_upload     = $fileUpload->uploadFiles("test_file");

                    if (is_array($confirm_upload)) {
                        // print_r($_FILES["test_file"]['name']);
                        $_FILES["test_file"]['file_name'] = [];
                            foreach ($confirm_upload as $c_key => $c_value) {
                                if ($c_value->status == 1) {
                                    $c_file_name = $c_value->name ? $c_value->name.".".$c_value->ext : "";
                                    array_push($_FILES["test_file"]['file_name'],$c_file_name);
                                } else {// if Any Error Occured in File Upload Stop the loop
                                    $status     = $confirm_upload->status;
                                    $data       = "file not uploaded";
                                    $error      = $confirm_upload->error;
                                    $sql        = "file upload error";
                                    $msg        = "file_error";
                                    break;
                                }
                            }  

                    } else if (!empty($_FILES["test_file"]['name'])) {// Single File Upload
                        $confirm_upload     = $fileUpload->uploadFile("test_file");
                        
                        if($confirm_upload->status == 1) {
                            $c_file_name = $confirm_upload->name ? $confirm_upload->name.".".$confirm_upload->ext : "";
                            $_FILES["test_file"]['file_name']  = $c_file_name;
                        } else {// if Any Error Occured in File Upload Stop the loop
                            $status     = $confirm_upload->status;
                            $data       = "file not uploaded";
                            $error      = $confirm_upload->error;
                            $sql        = "file upload error";
                            $msg        = "file_error";
                        }                    
                    }
            }
        }

        // print_r($_FILES["test_file"]['name']);

        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                $file_names     = implode(",",$_FILES["test_file"]['file_name']);
                $file_org_names = implode(",",$_FILES["test_file"]['name']);
            }                            
        } else if (!empty($_FILES["test_file"]['name'])) {
            $file_names     = $_FILES["test_file"]['file_name'];
            $file_org_names = $_FILES["test_file"]['name'];
        }
        
            // print_r($image);
        $columns            = [
            "staff_unique_id"    => $staff_unique_id,
            "education_type"     => $education_type,
            "degree"             => $degree,
            "doc_name"           => $file_names,
            "college_name"       => $college_name,
            "year_passing"       => $year_passing,
            "percentage"         => $percentage,
            "university"         => $university,
            "unique_id"          => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_staff_qualification_details,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'education_type ="'.$education_type.'" AND is_delete = 0  AND degree = "'.$degree.'" AND college_name = "'.$college_name.'" ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);
        // print_r($action_obj);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_staff_qualification_details,$columns,$update_where);
                // print_r($action_obj);
            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_staff_qualification_details,$columns);
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

    break;
    
    
   case 'experience_add_update':

        $staff_unique_id         = $_POST["staff_unique_id"];        
        $staff_company_name            = $_POST["staff_company_name"];
        // print_R($staff_company_name );die();
        $salary_amt              = $_POST["salary_amt"];
        $designation_name        = $_POST["designation_name"];
        $join_month              = $_POST["join_month"];
        $relieve_month           = $_POST["relieve_month"];
        $exp                     = $_POST["exp"];
        $unique_id               = $_POST["unique_id"];
        // $doc_names = $_POST["doc_names"];
        $update_where                       = "";
        
        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                // Multi file Upload 
                $confirm_upload     = $fileUpload->uploadFiles("test_file");
                    if (is_array($confirm_upload)) {
                        $_FILES["test_file"]['file_name'] = [];
                            foreach ($confirm_upload as $c_key => $c_value) {
                                if ($c_value->status == 1) {
                                    $c_file_name = $c_value->name ? $c_value->name.".".$c_value->ext : "";
                                    array_push($_FILES["test_file"]['file_name'],$c_file_name);
                                } else {// if Any Error Occured in File Upload Stop the loop
                                    $status     = $confirm_upload->status;
                                    $data       = "file not uploaded";
                                    $error      = $confirm_upload->error;
                                    $sql        = "file upload error";
                                    $msg        = "file_error";
                                    break;
                                }
                            }  
                    } else if (!empty($_FILES["test_file"]['name'])) {// Single File Upload
                        $confirm_upload     = $fileUpload->uploadFile("test_file");
                        
                        if($confirm_upload->status == 1) {
                            $c_file_name = $confirm_upload->name ? $confirm_upload->name.".".$confirm_upload->ext : "";
                            $_FILES["test_file"]['file_name']  = $c_file_name;
                        } else {// if Any Error Occured in File Upload Stop the loop
                            $status     = $confirm_upload->status;
                            $data       = "file not uploaded";
                            $error      = $confirm_upload->error;
                            $sql        = "file upload error";
                            $msg        = "file_error";
                        }                    
                    }
            }
        }

        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                $file_names     = implode(",",$_FILES["test_file"]['file_name']);
                $file_org_names = implode(",",$_FILES["test_file"]['name']);
            }                            
        } else if (!empty($_FILES["test_file"]['name'])) {
            $file_names     = $_FILES["test_file"]['file_name'];
            $file_org_names = $_FILES["test_file"]['name'];
        }
        $columns            = [
            "staff_unique_id"    => $staff_unique_id,
            "staff_company_name" => $staff_company_name,
            "salary_amt"         => $salary_amt,
            "doc_name"           => $file_names,
            "designation_name"   => $designation_name,
            "join_month"         => $join_month,
            "relieve_month"      => $relieve_month,
            "exp"                => $exp,
            "unique_id"          => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_staff_experience_details,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'staff_company_name ="'.$staff_company_name.'" AND is_delete = 0  AND designation_name = "'.$designation_name.'"  ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_staff_experience_details,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_staff_experience_details,$columns);
                // Insert Ends

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

    break;
    
    case 'experience_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "staff_experience_details";
        
        // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        // DataTable 
        $search     = $_POST['search']['value'];    
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "staff_company_name",
            "designation_name",
            "doc_name",
            "salary_amt",
            "join_month",
            "relieve_month",
            "exp",
            "unique_id"
        ];
        $table_details  = [
            $table_staff_experience_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['doc_name']      = image_view1("staff", $value['unique_id'],$value['doc_name']);
                switch ($value['doc_name']) {
                    case 1:
                        $value['doc_name'] = "Image";
                        break;
                    case 2:
                        $value['doc_name'] = "Document";
                        break;
                   
                }
                $btn_edit               = "";
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                // if($_SESSION['sess_user_type']  = '5f97fc3257f2525529'){
                    if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                        $value['unique_id']     = $btn_edit.$btn_delete;
                    }else if($_SESSION['sess_user_type']  != '5f97fc3257f2525529'){
                        $value['unique_id']     = $btn_edit;
                    }
               
                // $value['unique_id']     = $btn_edit.$btn_delete;
                $data[]                 = array_values($value);
            }
           
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;
case 'qualification_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "staff_qualification_details";
        
        // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        // DataTable 
        $search     = $_POST['search']['value'];    
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "education_type",
            "degree",
            "doc_name",
            "college_name",
            "year_passing",
            "percentage",
            "university",
            "unique_id"
        ];
        $table_details  = [
            $table_staff_qualification_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $value['doc_name']      = image_view1("staff", $value['unique_id'], $value['doc_name']);
              
                $btn_edit               = "";
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                // if($_SESSION['sess_user_type']  = '5f97fc3257f2525529'){
                    if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                        $value['unique_id']     = $btn_edit.$btn_delete;
                    }else if($_SESSION['sess_user_type']  != '5f97fc3257f2525529'){
                        $value['unique_id']     = $btn_edit;
                    }
                // $value['unique_id']     = $btn_edit.$btn_delete;
                // }
                $data[]                 = array_values($value);
            }
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;       
    case "staff_qualification_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "education_type",
            "degree",
            "college_name",
            "year_passing",
            "percentage",
            "university",
            "unique_id"
        ];
        $table_details  = [
            $table_staff_qualification_details,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data" 		=> $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"	=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;

    case 'staff_qualification_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_staff_qualification_details,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        
    break;

    case "staff_experience_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "company_name",
            "designation_name",
            "salary_amt",
            "join_month",
            "relieve_month",
            "exp",
            "unique_id"
        ];
        $table_details  = [
            $table_staff_experience_details,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"   => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;

    case 'staff_experience_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_staff_experience_details,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        
    break;

    case 'staff_account_details_add_update':

        $staff_unique_id            = $_POST["staff_unique_id"];
        $bank_status                = $_POST["bank_status"];       
        $bank_name                  = $_POST["bank_name"];
        $bank_address               = $_POST["bank_address"];
        $ifsc_code                  = $_POST["ifsc_code"];
        $accountant_name            = $_POST["accountant_name"];
        $account_no                 = $_POST["account_no"];
        $bank_contact_no            = $_POST["bank_contact_no"];
        $salary_type                = $_POST["salary_type"];
        $payment_method             = $_POST["payment_method"];
        $unique_id                  = $_POST["unique_id"];

        $update_where               = "";

        $columns            = [
            "staff_unique_id"              => $staff_unique_id,
            "bank_status"                  => $bank_status,
            "bank_name"                    => $bank_name,
            "address"                      => $bank_address,
            "ifsc_code"                    => $ifsc_code,
            "accountant_name"              => $accountant_name,
            "contact_no"                   => $bank_contact_no,
            "salary_type"                  => $salary_type,
            "payment_method"               => $payment_method,
            "account_no"                   => $account_no,
            "unique_id"                    => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_account_details,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
       //$select_where       = 'bank_name ="'.$bank_name.'" AND is_delete = 0  AND ifsc_code = "'.$ifsc_code.'" AND account_no = "'.$account_no.'" ';
        if($bank_status == 'Primary'){
            $select_where       = 'bank_status = "Primary"  AND is_delete = 0  AND staff_unique_id="'.$staff_unique_id.'"';
        }
       else if($bank_status == 'Secondary'){
            $select_where       = 'bank_status = "Secondary"  AND is_delete = 0  AND staff_unique_id="'.$staff_unique_id.'"';
        }
        else{
            $select_where       = 'bank_name ="'.$bank_name.'" AND is_delete = 0  AND ifsc_code = "'.$ifsc_code.'" AND account_no = "'.$account_no.'"';
        }

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);
// print_r($action_obj);
        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
            
        }
        if ($data[0]["count"]) 
        {
            $msg        = "already";
        } 
        else if (($data[0]["count"] == 0) && ($msg != "error")) 
        {
            // Update Begins
            if($unique_id) 
            {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_account_details,$columns,$update_where);

            // Update Ends
            } 
            else 
            {
                // Insert Begins            
                $action_obj     = $pdo->insert($table_account_details,$columns);
                // Insert Ends
            }

            if ($action_obj->status) 
            {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) 
                {
                    $msg        = "update";
                } 
                else 
                {
                    $msg        = "add";
                }
            } 
            else 
            {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

    break;

    
    case 'staff_account_details_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "staff_account_details";
        
        // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        // DataTable 
		$search 	= $_POST['search']['value'];    
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "bank_status",
            "salary_type",
            "accountant_name",
            "account_no",
            "payment_method",
            "bank_name",
            "ifsc_code",
            "contact_no",
            "address",
            // "'' as active_status",
            "unique_id"
        ];
        $table_details  = [
            $table_account_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
            //    $value['bank_status']=  $value['bank_status'];
            if($value['bank_status']=='Primary')
               {
                $value['salary_type']='Axis Bank';
               }
              else
               {
                $value['salary_type']= 'NEFT';
               }
                // $value['active_status'] = '<input type ="checkbox"  id="check_qty'.$value['unique_id'].'" value="1" onchange="edit_status(this.value,\''.$value['unique_id'].'\')"><input type ="hidden"  id="sub_unique_id'.$value['unique_id'].'"  name="sub_unique_id[]" value="'.$value['unique_id'].'">';
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                // if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                if($_SESSION['sess_user_type']  == '5f97fc3257f2525529'){
                        $value['unique_id']     = $btn_edit.$btn_delete;
                }else if($_SESSION['sess_user_type']  != '5f97fc3257f2525529'){
                        $value['unique_id']     = $btn_edit;
                    }
              
                // }
                // $value['unique_id']     = $btn_edit.$btn_delete;
                $data[]                 = array_values($value);
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
    break;
    case "staff_account_details_edit":
        // Fetch Data
       $unique_id  = $_POST['unique_id'];
       $data	    = [];
       
       // Query Variables
       $json_array     = "";
       $columns        = [
           // "bank_name",
           // "address",
           // "ifsc_code",
           // "accountant_name",
           // "account_no",
           // "contact_no",
           //"gst_no",
           "bank_status",
           "salary_type",
           "accountant_name",
           "account_no",
           "bank_name",
           "ifsc_code",
           "contact_no",
           "address",
           "unique_id"
       ];
       $table_details  = [
           $table_account_details,
           $columns
       ];
       $where          = [
           "unique_id"    => $unique_id,
           "is_active"    => 1,
           "is_delete"    => 0
       ];        

       $result         = $pdo->select($table_details,$where);

       if ($result->status) {
           
           $json_array = [
               "data" 		=> $result->data[0],
               "status"    => $result->status,
               "sql"       => $result->sql,
               "error"     => $result->error,
               "testing"	=> $result->sql
           ];
       } else {
           print_r($result);
       }
       
       echo json_encode($json_array);
   break;

    case 'staff_account_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_account_details,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);
        
    break;

  case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active'];

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table_staff_official, $columns, $update_where); // use $table_staff_official

    $status = $action_obj->status;
    $msg    = $status
        ? ($is_active == 1 ? "Activated Successfully" : "Deactivated Successfully")
        : "Toggle failed!";

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

        
        case "staff_account_details_count":
            $staff_unique_id = $_POST['unique_id'];  // Use the variable name
            $data = [];
        
            // Query Variables
            $json_array = "";
        
            $table_details = [
                $table_account_details,
                [
                    "COUNT(staff_unique_id) AS count"  // Use the field name
                ]
            ];
            $where = [
                "staff_unique_id" => $staff_unique_id,  // Use the field name
                "is_active" => 1,
                "is_delete" => 0
            ];
        
            $result = $pdo->select($table_details, $where);
        
            if ($result->status) {
                $json_array = [
                    "data" => $result->data[0],
                    "status" => $result->status,
                    "sql" => $result->sql,
                    "error" => $result->error,
                    "testing" => $result->sql
                ];
            } else {
                print_r($result);
            }
        
            echo json_encode($json_array);
            break;
        

    case 'states':

        $country_id          = $_POST['country_id'];

        $pre_state_options  = state("",$country_id);

        $pre_state_options  = select_option($pre_state_options,"Select the State");

        echo $pre_state_options;
        
        break;

        case 'get_qualification':

            $graduation_type          = $_POST['graduation_type'];
    
            $graduation_type_options  = qualification($graduation_type);
    
            $graduation_type_options  = select_option($graduation_type_options,"Select the Graduation");
    
            echo $graduation_type_options;
            
            break;
        
            case 'get_designation':

                $grade_type          = $_POST['grade_type'];
        
                $grade_type_options  = designation($grade_type);
        
                $grade_type_options  = select_option($grade_type_options,"Select Designation");
        
                echo $grade_type_options;
                
                break;
                
        case 'work_location':

        $company_id          = $_POST['company_id'];

        $pre_state_options  = get_project_name("",$company_id);
        
        error_log("project_result: " .print_r($pre_state_options, true) . "\n", 3, "logs/log.txt" );

        $pre_state_options  = select_option($pre_state_options,"Select Project Name");
        
        error_log("project_options: " .print_r($pre_state_options, true) . "\n", 3, "logs/log.txt" );

        echo $pre_state_options;
        
        break;

    case 'cities':

        $state_id           = $_POST['state_id'];

        $pre_city_options  = city("",$state_id);

        $pre_city_options  = select_option($pre_city_options,"Select the City");

        echo $pre_city_options;
        
        break;

    case 'perm_states':

        $country_id          = $_POST['country_id'];

        $perm_state_options  = state("",$country_id);

        $perm_state_options  = select_option($perm_state_options,"Select the State");

        echo $perm_state_options;
        
        break;

    case 'perm_cities':

        $state_id           = $_POST['state_id'];

        $perm_city_options  = city("",$state_id);

        $perm_city_options  = select_option($perm_city_options,"Select the City");

        echo $perm_city_options;
        
        break;

case 'employee_id':

    $unique_id_company = $_POST['company_name'];

    // Get the dynamic company label
    $prefix = company_label($unique_id_company);

    if ($prefix != "") {
        $emp_prefix = $prefix; // Append 0 as required
    } else {
        $emp_prefix = "BPGN0"; // fallback
    }

    $emp_id = emp_id("staff", $emp_prefix);

    echo $emp_id;

    break;


    case 'image_upload':

        $update_where       = "";

        $unique_id          = $_POST['unique_id'];

        $columns            = [
            //"user_input"            => $_POST['input_name'],
            "file_original_name"    => is_array($_FILES['test_file']['name']) ? implode(",",$_FILES['test_file']['name']) : $_FILES['test_file']['name'] ,
            "unique_id"             => unique_id($prefix)
        ];

        // // check already Exist Or not
        // $table_details      = [
        //     $table_staff_official,
        //     [
        //         "COUNT(unique_id) AS count"
        //     ]
        // ];        

        // $select_where       = 'user_input = "'.$_POST['input_name'].'" AND is_delete = 0  ';

        // // When Update Check without current id
        // if ($unique_id) {
        //     $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        // } else {
        // }

        // $action_obj         = $pdo->select($table_details,$select_where);

        // if ($action_obj->status) {
        //     $status     = $action_obj->status;
        //     $data       = $action_obj->data;
        //     $error      = "";
        //     $sql        = $action_obj->sql;

        // } else {
        //     $status     = $action_obj->status;
        //     $data       = $action_obj->data;
        //     $error      = $action_obj->error;
        //     $sql        = $action_obj->sql;
        //     $msg        = "error";
        // }

        // if ($data[0]["count"]) {
        //     $msg        = "already";
        // } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            
            
            $file_name          = "";
            $file_path          = "";
   
            foreach ($_FILES as $file_key => $file_value) {

               if (!empty($file_value['name'])) {

                    // Single File Upload
                    $confirm_upload     = $fileUpload->uploadFile($file_key);
                    
                    if($confirm_upload->status == 1) {

                        $c_file_name = $confirm_upload->name ? $confirm_upload->name.".".$confirm_upload->ext : "";
                        $c_file_path = $confirm_upload->path;

                        $_FILES[$file_key]['file_name']  = $c_file_name;
                        $_FILES[$file_key]['path']       = $c_file_path;

                    } else {

                        // if Any Error Occured in File Upload Stop the loop

                        $status     = $confirm_upload->status;
                        $data       = "file not uploaded";
                        $error      = $confirm_upload->error;
                        $sql        = "file upload error";
                        $msg        = "file_error";

                    }                    
                }
                
            }
            

            if ($msg != "file_error") {
                
                foreach ($_FILES as $fi_key => $fi_value) {
                    
                    if (!empty($fi_value['name'])) {

                        $file_names     = $fi_value['file_name'];
                        $file_org_names = $fi_value['name'];

                    }

                    if ($file_names) {

                        // Set Here Columns(from database) and Equal input(from Form) Names
                        // Add cases depending on how many fields You have 
                        switch ($fi_key) {
                            case 'test_file':
                                $columns['file_name']           = $file_names;
                                $columns['file_original_name']  = $file_org_names;
                                break;
                                
                                default:
                                # code...
                                break;
                        }
                            
                    }
                }

                // Update Begins

                if($unique_id) {

                    unset($columns['unique_id']);

                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];

                    $action_obj     = $pdo->update($table_staff_official,$columns,$update_where);

                // Update Ends
                } else {

                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_staff_official,$columns);
                    // Insert Ends

                }

                if ($action_obj->status) {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = "";
                    $sql        = $action_obj->sql;

                    if ($unique_id) {
                        $msg        = "update";
                    } else {
                        $msg        = "create";
                    }
                } else {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = $action_obj->error;
                    $sql        = $action_obj->sql;
                    $msg        = "error";
                }
            } 
       // }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;

        case "send_mail" :

            $email_id = $_POST['email_id'];
            $link     = $_POST['link'];
                $date = date('Y-m-d');
                $date1 = '2023-08-20';
                $datehms = date('h:i:s');
            $to_email = $email_id;
            $subject = "Please fill the Form";
            $body = $link.'?date='.$date1.'&time='.$datehms;
            $headers = $email_id;
    
            if (mail($to_email, $subject, $body, $headers)) {
             echo "sent";
            } else {
            echo "failed";
            }
    
            break;

        default:
        
    break;
}

function file_upload_status ($upload_status = "") {

    $error_array    = [
        "status"    => 0,
        "error"     => "",
        "msg"       => "file_error",
        "id"        => ""
    ];

    if (!empty($upload_status)) {

        if (is_array($upload_status)) {
            foreach ($upload_status as $upload_status_key => $upload_status_value) {

                if ($upload_status_value->status == 1) {
                    // return $upload_status_value;
                    // print_r($upload_status_value);
                } else {
                    echo 'File didn\'t uploaded. Error code: ' . $upload_status->error;
                    break;
                }
            }
        } else {
            
            if ($upload_status->status == 1) {
                return $upload_status;
            } else {

                // print_r($upload_status);

                $error_array['error'] = $upload_status->error;
                // echo 'File didn\'t uploaded. Error code: ' . $upload_status->error;
                return $error_array;
            }
            
        }

    }
}
function image_view1($folder_name = "",$unique_id = "",$doc_name = "") {
     //print_r($doc_name);
  $file_names = explode(',', $doc_name);
  
    $image_view = '';

    if($doc_name){
            foreach ($file_names as $file_key => $doc_name) { 
      
                if($file_key!=0){
                    if($file_key%4!=0){
                        $image_view .= "&nbsp";
                    } else {
                        $image_view .= "<br><br>"; 
                    }
                }
       
                $cfile_name = explode('.',$doc_name);
                // print_r($cfile_name);
                if($doc_name){  
                    if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
                    $image_view .= '<a href="javascript:print(\'uploads/staff/'.$doc_name.'\')"><img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" ></a>';
                    // $image_view .= '<img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" >';
                    }else if($cfile_name[1]=='pdf'){
                        $image_view .= '<a href="javascript:print(\'uploads/staff/'.$doc_name.'\')"><img src="uploads/staff/pdf.png"  height="50px" width="50px" ></a>';
                    }
                    else if(($cfile_name[1]=='pdf')||($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')){
                        $image_view .= '<a href="javascript:print(\'uploads/staff/'.$doc_name.'\')"><img src="uploads/staff/excel.png"  height="50px" width="50px" ></a>';
                    }
                }
               
            }
    }
    // print_r($image_view);    
       return $image_view;
}
