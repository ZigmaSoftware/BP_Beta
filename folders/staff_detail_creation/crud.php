<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database able Names
$table_staff_official               = "staff";
$table_staff_official_continuous    = "staff_continuous";
$table_dependent_details            = "staff_dependent_details";
$table_staff_asset                  = "staff_asset_details";
$table_staff_qualification_details  = "staff_qualification_details";
$table_staff_experience_details     = "staff_experience_details";
$table_account_details              = "staff_account_details";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Include this folder only functions
include 'function.php';

// File Upload Library Call
$fileUpload         = new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );

$fileUploadPath = $fileUploadConfig->get("upload_folder");

//print_r($fileUploadPath);
// Create Folder in root->uploads->(this_folder_name) Before using this file upload
$fileUploadConfig->set("upload_folder",$fileUploadPath. "staff" . DIRECTORY_SEPARATOR);
// print_r($fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name . DIRECTORY_SEPARATOR));
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
        $designation          = $_POST["designation"];
        $biometric_id         = $_POST["biometric_id"];
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
        $doc_dob              = $_POST["doc_dob"];
        $qualification        = $_POST["qualification"];
        $company_name         = $_POST['company_name'];
        // $doc_name             = $_POST["doc_name"];
        $relieve_status       = $_POST["relieve_status"];
        $update_where         = "";

        $columns            = [
            "staff_name"            => $staff_name,
            "employee_id"           => $employee_id,
            "date_of_join"          => $date_of_join,
            "designation_unique_id" => $designation,
            "biometric_id"          => $biometric_id,
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
            "doc_dob"               => $doc_dob,
            "qualification"         => $qualification,
            "company_name"          => $company_name,
            "relieve_status"              => $relieve_status,
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

    case 'salarycreateupdate':

        $salary                      = $_POST["salary"];
        $annum_salary                = $_POST["annum_salary"];
        $basic_wages                 = $_POST["basic_wages"];
        $annum_basic_wages           = $_POST["annum_basic_wages"];
        $hra                         = $_POST["hra"];
        $annum_hra                   = $_POST["annum_hra"];
        $conveyance                  = $_POST["conveyance"];
        $annum_conveyance            = $_POST["annum_conveyance"];
        $medical_allowance           = $_POST["medical_allowance"];
        $annum_medical_allowance     = $_POST["annum_medical_allowance"];
        $education_allowance         = $_POST["education_allowance"];
        $annum_education_allowance   = $_POST["annum_education_allowance"];
        $other_allowance             = $_POST["other_allowance"];
        $annum_other_allowance       = $_POST["annum_other_allowance"];
        $pf                          = $_POST["pf"];
        $annum_pf                    = $_POST["annum_pf"];
        $esi                         = $_POST["esi"];
        $annum_esi                   = $_POST["annum_esi"];
        $total_deduction             = $_POST["total_deduction"];
        $annum_total_deduction       = $_POST["annum_total_deduction"];
        $net_salary                  = $_POST["net_salary"];
        $annum_net_salary            = $_POST["annum_net_salary"];
        $purformance_allowance       = $_POST["purformance_allowance"];
        $annum_purformance_allowance = $_POST["annum_purformance_allowance"];
        $ctc                         = $_POST["ctc"];
        $annum_ctc                   = $_POST["annum_ctc"];
        $conveyance_default_value    = $_POST["conveyance_default_value"];
        $medical_default_value       = $_POST["medical_default_value"];
        $pf_default_value            = $_POST["pf_default_value"];
        $esi_default_value           = $_POST["esi_default_value"];
        $educational_default_value   = $_POST["educational_default_value"];
        $unique_id                   = $_POST["unique_id"];
        $staff_unique_id             = $_POST["staff_unique_id"];
       
        $update_where         = "";

         $update_where_insert   = [
                    "unique_id"     => $staff_unique_id
                ];

        $columns            = [
            "salary"                         => $salary,
            "annum_salary"                   => $annum_salary,
            "basic_wages"                    => $basic_wages,
            "annum_basic_wages"              => $annum_basic_wages,
            "hra"                            => $hra,
            "annum_hra"                      => $annum_hra,
            "conveyance"                     => $conveyance,
            "annum_conveyance"               => $annum_conveyance,
            "medical_allowance"              => $medical_allowance,
            "annum_medical_allowance"        => $annum_medical_allowance,
            "education_allowance"            => $education_allowance,
            "annum_education_allowance"      => $annum_education_allowance,
            "other_allowance"                => $other_allowance,
            "annum_other_allowance"          => $annum_other_allowance,
            "pf"                             => $pf,
            "annum_pf"                       => $annum_pf,
            "esi"                            => $esi,
            "annum_esi"                      => $annum_esi,
            "total_deduction"                => $total_deduction,
            "annum_total_deduction"          => $annum_total_deduction,
            "net_salary"                     => $net_salary,
            "annum_net_salary"               => $annum_net_salary,
            "purformance_allowance"          => $purformance_allowance,
            "annum_purformance_allowance"    => $annum_purformance_allowance,
            "ctc"                            => $ctc,
            "annum_ctc"                      => $annum_ctc,
            //"unique_id"           => $unique_id
        ];

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
            "is_active"             =>0,
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
            $status             = $_POST["status"];
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
                "status"           => $status,
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
            $select_where       = 'asset_name ="'.$asset_name.'" AND is_delete = 0  AND item_no = "'.$item_no.'" AND status = "'.$status.'" ';
    
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
            }
            // Function Name button prefix
        $btn_edit_delete    = "asset_details";
    // Fetch Data
    $staff_unique_id = $_POST['staff_unique_id']; 

    

    $data	    = [];
    

    if($length == '-1') {
        $limit  = "";
    }

    // Query Variables
    $json_array     = "";
    $columns1        = [
        "@a:=@a+1 s_no",
            "asset_name",
            "item_no",
            "qty",
            "status",
            "veh_reg_no", 
            "dri_license_no",
            "license_mode",
            "vehicle_type",
            "vehicle_company",
            "vehicle_owner",
            "registration_year",
            "rc_no",
            "ins_no",
            "staff_unique_id",
            "unique_id"
    ];
    $i = 1;
    $table_details1  = [
        $table_staff_asset,
        $columns1
    ];
    $where1          = [
        "staff_unique_id"    => $staff_unique_id,
        "is_active"                     => 1,
        "is_delete"                     => 0
    ];

    $order_by = "";
    
    $sql_function   = "SQL_CALC_FOUND_ROWS";

    $result3         = $pdo->select($table_details1,$where1,$limit,$start,$order_by,$sql_function);
    // $total_records  = total_records();
    $res_array = $result3->data;
    $data1 =  '<table class="table dt-responsive nowrap w-100">';
    $data1 .= '<tr>
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
 </tr>';
    foreach ($res_array as $key => $item) {
        

        

$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['asset_name'] . '</td>';
$data1 .= '<td>' . $item['item_no'] . '</td>';
$data1 .= '<td>' . $item['qty'] . '</td>';
$data1 .= '<td>' . $item['status'] . '</td>';
$data1 .= '<td>' . $item['veh_reg_no'] . '</td>';
$data1 .= '<td>' . $item['dri_license_no'] . '</td>';
$data1 .= '<td>' . $item['license_mode'] . '</td>';
$data1 .= '<td>' . $item['vehicle_type'] . '</td>';
$data1 .= '<td>' . $item['vehicle_company'] . '</td>';
$data1 .= '<td>' . $item['vehicle_owner'] . '</td>';
$data1 .= '<td>' . $item['registration_year'] . '</td>';
$data1 .= '<td>' . $item['rc_no'] . '</td>';
$data1 .= '<td>' . $item['ins_no'] . '</td>';
$data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item['unique_id'],$item['staff_unique_id']).'</td>';

// $data1 .= '<td>' . $item['name'] . '</td>';
// $data1 .= '<td>' . $item['name'] . '</td>';
$data1 .= '</tr>';




    }
    $data1 .= '</table>';

//     if ($action_obj->status) {
//         $status     = $action_obj->status;
//         $data       = $data1;
//         $error      = "";
//         $sql        = $result->sql;

//         if ($unique_id) {
//             $msg        = "update";
//         } else {
//             $msg        = "add";
//         }
//     } else {
//         $status     = $action_obj->status;
//         $data       = $data1;
//         $error      = $action_obj->error;
//         $sql        = $action_obj->sql;
//         $msg        = "error";
//     }
// }
// $json_array   = [
//     // "status"    => $status,
//     "data"      => $data1,
//     "error"     => $error,
//     "msg"       => $msg,
//     "sql"       => $sql
// ];
// }
$json_array = [
    'data'            => $data1,
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
                "status",
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
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_edit.$btn_delete;
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
                "status",
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

        // Function Name button prefix
        $btn_edit_delete    = "asset_details";
    // Fetch Data
    $staff_unique_id = $_POST['staff_unique_id']; 

    

    $data	    = [];
    

    if($length == '-1') {
        $limit  = "";
    }

    // Query Variables
    $json_array     = "";
    $columns1        = [
        "@a:=@a+1 s_no",
            "asset_name",
            "item_no",
            "qty",
            "status",
            "veh_reg_no", 
            "dri_license_no",
            "license_mode",
            "vehicle_type",
            "vehicle_company",
            "vehicle_owner",
            "registration_year",
            "rc_no",
            "ins_no",
            "staff_unique_id",
            "unique_id"
    ];
    $i = 1;
    $table_details1  = [
        $table_staff_asset,
        $columns1
    ];
    $where1          = [
        "staff_unique_id"    => $staff_unique_id,
        "is_active"                     => 1,
        "is_delete"                     => 0
    ];

    $order_by = "";
    
    $sql_function   = "SQL_CALC_FOUND_ROWS";

    $result3         = $pdo->select($table_details1,$where1,$limit,$start,$order_by,$sql_function);
    // $total_records  = total_records();
    $res_array = $result3->data;
    $data1 =  '<table class="table dt-responsive nowrap w-100">';
    $data1 .= '<tr>
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
 </tr>';
    foreach ($res_array as $key => $item) {
        

        

$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['asset_name'] . '</td>';
$data1 .= '<td>' . $item['item_no'] . '</td>';
$data1 .= '<td>' . $item['qty'] . '</td>';
$data1 .= '<td>' . $item['status'] . '</td>';
$data1 .= '<td>' . $item['veh_reg_no'] . '</td>';
$data1 .= '<td>' . $item['dri_license_no'] . '</td>';
$data1 .= '<td>' . $item['license_mode'] . '</td>';
$data1 .= '<td>' . $item['vehicle_type'] . '</td>';
$data1 .= '<td>' . $item['vehicle_company'] . '</td>';
$data1 .= '<td>' . $item['vehicle_owner'] . '</td>';
$data1 .= '<td>' . $item['registration_year'] . '</td>';
$data1 .= '<td>' . $item['rc_no'] . '</td>';
$data1 .= '<td>' . $item['ins_no'] . '</td>';
$data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item['unique_id'],$item['staff_unique_id']).'</td>';

// $data1 .= '<td>' . $item['name'] . '</td>';
// $data1 .= '<td>' . $item['name'] . '</td>';
$data1 .= '</tr>';




    }
    $data1 .= '</table>';


$json_array = [
    'data'            => $data1,
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
        $emer_contact_person    = $_POST["emer_contact_person"];
        $emer_contact_no        = $_POST["emer_contact_no"];
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
            "emer_contact_person"    => $emer_contact_person,
            "emer_contact_no"        => $emer_contact_no,
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

        }
        // Function Name button prefix
        $btn_edit_delete    = "dependent_details";
        
        // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        

        // Query Variables
        $json_array     = "";
        $columns1        = [
            "@a:=@a+1 s_no",
            "relationship",
            "name",
            "gender",
            "date_of_birth",
            "aadhar_no",
            "occupation",
            "emer_contact_person",
            "emer_contact_no",
            "standard",
            "school",
            "existing_illness",
            "illness_description",
            "existing_insurance",
            "insurance_no",
            "physically_challenged",
            "remarks",
            "staff_unique_id",
            "unique_id"
        ];
        $table_details1  = [
            $table_dependent_details,
            $columns1
        ];
        $where1          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        // $order_by = "";
        
        // $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result8         = $pdo->select($table_details1,$where1);
        $i = 1;

        // if ($result->status) {

            $res_array1      = $result8->data;
            $data1 =  '<table class="table dt-responsive nowrap w-100">';
            $data1 .= '<tr>
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
            </tr>';
            foreach ($res_array1 as $key => $item_value) {

                if($item_value['gender'] == 1){
                    $gender = "male";
                }elseif($item_value['gender'] == 2){
                    $gender = "female"; 
                }else{
                    $gender = "others";
                }
                

    $data1 .= '<tr>';
    $data1 .= '<td>' . $i++ . '</td>';
    $data1 .= '<td>' . $item_value['relationship'] . '</td>';
    $data1 .= '<td>' . $item_value['name'] . '</td>';
    $data1 .= '<td>' . $gender . '</td>';
    $data1 .= '<td>' . $item_value['date_of_birth'] . '</td>';
    $data1 .= '<td>' . $item_value['aadhar_no'] . '</td>';
    $data1 .= '<td>' . $item_value['occupation'] . '</td>';
    $data1 .= '<td>' . $item_value['emer_contact_person'] . '</td>';
    $data1 .= '<td>' . $item_value['emer_contact_no'] . '</td>';
    $data1 .= '<td>' . $item_value['standard'] . '</td>';
    $data1 .= '<td>' . $item_value['school'] . '</td>';
    $data1 .= '<td>' . $item_value['existing_illness'] . '</td>';
    $data1 .= '<td>' . $item_value['illness_description'] . '</td>';
    $data1 .= '<td>' . $item_value['existing_insurance'] . '</td>';
    $data1 .= '<td>' . $item_value['insurance_no'] . '</td>';
    $data1 .= '<td>' . $item_value['physically_challenged'] . '</td>';
    $data1 .= '<td>' . $item_value['remarks'] . '</td>';
    $data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item_value['unique_id'],$item_value['staff_unique_id']).'</td>';
    
    $data1 .= '</tr>';
            }
            $data1 .= '</table>';

    
        $json_array = [
            'data'            => $data1,
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
            "emer_contact_person",
            "emer_contact_no",
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
                $value['unique_id']     = $btn_edit.$btn_delete;
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
            "emer_contact_no",
            "emer_contact_person",
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

       // Function Name button prefix
       $btn_edit_delete    = "dependent_details";
        
       // Fetch Data
       $staff_unique_id = $_POST['staff_unique_id']; 

       

       // Query Variables
       $json_array     = "";
       $columns1        = [
           "@a:=@a+1 s_no",
           "relationship",
           "name",
           "gender",
           "date_of_birth",
           "aadhar_no",
           "occupation",
           "emer_contact_person",
           "emer_contact_no",
           "standard",
           "school",
           "existing_illness",
           "illness_description",
           "existing_insurance",
           "insurance_no",
           "physically_challenged",
           "remarks",
           "staff_unique_id",
           "unique_id"
       ];
       $table_details1  = [
           $table_dependent_details,
           $columns1
       ];
       $where1          = [
           "staff_unique_id"    => $staff_unique_id,
           "is_active"                     => 1,
           "is_delete"                     => 0
       ];

       // $order_by = "";
       
       // $sql_function   = "SQL_CALC_FOUND_ROWS";

       $result8         = $pdo->select($table_details1,$where1);
       $i = 1;

       // if ($result->status) {

           $res_array1      = $result8->data;
           $data1 =  '<table class="table dt-responsive nowrap w-100">';
           $data1 .= '<tr>
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
           </tr>';
           foreach ($res_array1 as $key => $item_value) {

               if($item_value['gender'] == 1){
                   $gender = "male";
               }elseif($item_value['gender'] == 2){
                   $gender = "female"; 
               }else{
                   $gender = "others";
               }
               

   $data1 .= '<tr>';
   $data1 .= '<td>' . $i++ . '</td>';
   $data1 .= '<td>' . $item_value['relationship'] . '</td>';
   $data1 .= '<td>' . $item_value['name'] . '</td>';
   $data1 .= '<td>' . $gender . '</td>';
   $data1 .= '<td>' . $item_value['date_of_birth'] . '</td>';
   $data1 .= '<td>' . $item_value['aadhar_no'] . '</td>';
   $data1 .= '<td>' . $item_value['occupation'] . '</td>';
   $data1 .= '<td>' . $item_value['emer_contact_person'] . '</td>';
   $data1 .= '<td>' . $item_value['emer_contact_no'] . '</td>';
   $data1 .= '<td>' . $item_value['standard'] . '</td>';
   $data1 .= '<td>' . $item_value['school'] . '</td>';
   $data1 .= '<td>' . $item_value['existing_illness'] . '</td>';
   $data1 .= '<td>' . $item_value['illness_description'] . '</td>';
   $data1 .= '<td>' . $item_value['existing_insurance'] . '</td>';
   $data1 .= '<td>' . $item_value['insurance_no'] . '</td>';
   $data1 .= '<td>' . $item_value['physically_challenged'] . '</td>';
   $data1 .= '<td>' . $item_value['remarks'] . '</td>';
   $data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item_value['unique_id'],$item_value['staff_unique_id']).'</td>';
   
   $data1 .= '</tr>';

   


           }
           $data1 .= '</table>';

   
       $json_array = [
           'data'            => $data1,
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
           
           "department",
           "work_location",
           "is_active",
           "unique_id"
        ];
        $table_details  = [
            $table_staff_official." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        
        // $where          = " is_active = 1 AND is_delete = 0 ";

        // if ($_POST['search']['value']) {
        //    $where .= " AND employee_id LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR staff_name LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR biometric_id LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR designation_unique_id IN (".designation_name_like($_POST['search']['value']).") ";
            
        // }
         $order_by       = " employee_id DESC ";
         $company_name=$_POST['company_name'];
        //  $relieve_status=$_POST['relieve_status'];

 
        if($_POST['status'] == 0){
            $where = " is_delete = '0' ";
        }else if($_POST['status'] == 1){
            $where = " is_delete = '0' and relieve_date = '' ";
        }else{
            $where = " is_delete = '0' and relieve_date != '' ";
        }
        if($_POST['company_name']){
            $where .= " AND company_name = '$company_name' ";
        }
        // if($_POST['relieve_status']){
        //     $where .= " AND relieve_status = '$relieve_status' ";
        // }


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
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['date_of_birth']    = disdate($value['date_of_birth']);
                $value['is_active']   = is_active_show($value['is_active']);
            
                $btn_update             = btn_update($folder_name,$value['unique_id']);
                $btn_delete             = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']     = $btn_update.$btn_delete;
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
    
    // staff Potential Mappting Section Starts  

    case 'qualification_add_update':


        $staff_unique_id         = $_POST["staff_unique_id"];        
        $education_type          = $_POST["education_type"];
        $degree                  = $_POST["degree"];
        // $doc_name                = $_POST["test_doc"];
        $college_name            = $_POST["college_name"];
        $year_passing            = $_POST["year_passing"];
        $percentage              = $_POST["percentage"];
        $university              = $_POST["university"];
        $unique_id               = $_POST["unique_id"];
       // $doc_name = $_POST["doc_names"];
        
        // print_r($_POST["test_doc"]);
        $update_where                       = "";
    
        for($i=0;$i<count($_FILES['test_file']['name']);$i++){
            // $allowTypes = array('jpg','png','jpeg','gif'); 
            $file_name[]=basename($_FILES['test_file']['name'][$i]);
            $uploadfile=$_FILES['test_file']['name'][$i];
        
            $tem_name[$i] =  random_strings(100).".".$file_name[$i];
        
            // $imageName = $this->random_strings() . 

            // $targetDir = "../../uploads/q_doc/. $file_name[$i]";
            $targetDir = "../../uploads/q_doc/. $tem_name[$i]";
        
        
            // $newname1 = "CALIBRATION_" . rand(10000,99999) . "." .  $targetDir; 
         
            // if(in_array($targetDir,$allowTypes)){ 
             move_uploaded_file($uploadfile,$targetDir);
            //  }
        }
        // $images=implode(',',$_FILES['test_file']['name']);
        $images=implode(',',$_FILES['test_file']['name']);


        $columns            = [
            "staff_unique_id"    => $staff_unique_id,
            "education_type"     => $education_type,
            "degree"             => $degree,
            "doc_name"           => $images,
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
        }else if (($data[0]["count"] == 0) && ($msg != "error")) {
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
        }
            // Function Name button prefix
             $btn_edit_delete    = "staff_qualification";

            // Fetch Data
           $staff_unique_id = $_POST['staff_unique_id']; 



// Query Variables
        $json_array     = "";
        $columns1        = [
            "@a:=@a+1 s_no",
            "education_type",
            "degree",
            "doc_name",
            "college_name",
            "year_passing",
            "percentage",
            "university",
            "staff_unique_id",
            "unique_id"
        ];

        // $i = 1;
        $table_details1  = [
            $table_staff_qualification_details,
            $columns1
        ];
        $where1          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result1         = $pdo->select($table_details1,$where1);
        // print_r($result);
        // $total_records  = total_records();

        // if ($result1->status) {

            $res_array2      = $result1->data;
            $i = 1;
            $data2 =  '<table class="table dt-responsive nowrap w-100">';
            $data2 .= '<tr>
            <th>#</th>
            <th>Education Type</th>
            <th>Degree</th>
            <th>Document Image</th>
            <th>College Name</th>
            <th>Year of Passing</th>
            <th>Percentage</th>
            <th>University</th>
            <th>Action</th>
            </tr>';
            foreach ($res_array2 as $key => $item_val) {
                $data2 .= '<tr>';
                $data2 .= '<td>' . $i++ . '</td>';
                $data2 .= '<td>' . $item_val['education_type'] . '</td>';
                $data2 .= '<td>' . $item_val['degree'] . '</td>';
                $data2 .= '<td>' .image_view1("staff", $item_val['unique_id'], $item_val['doc_name']).'</td>';
                $data2 .= '<td>' . $item_val['college_name'] . '</td>';
                $data2 .= '<td>' . $item_val['year_passing'] . '</td>';
                $data2 .= '<td>' . $item_val['percentage'] . '</td>';
                $data2 .= '<td>' . $item_val['university'] . '</td>';
                $data2 .= '<td>'.btn_delete_new($btn_edit_delete,$item_val['unique_id'],$item_val['staff_unique_id']).'</td>';
                // $data2 .= '<td>' . $item['standard'] . '</td>';
            
                // $data2 .= '<td>' . $item['name'] . '</td>';
                // $data2 .= '<td>' . $item['name'] . '</td>';
                $data2 .= '</tr>';
            }
            $data2 .= '</table>';
          // }
   
        // }
        $json_array = [
            'data'            => $data2,
        ];
        
        echo json_encode($json_array);
        //     if ($action_obj->status) {
        //         $status     = $action_obj->status;
        //         $data       = $action_obj->data;
        //         $error      = "";
        //         $sql        = $action_obj->sql;

        //         if ($unique_id) {
        //             $msg        = "update";
        //         } else {
        //             $msg        = "add";
        //         }
        //     } else {
        //         $status     = $action_obj->status;
        //         $data       = $action_obj->data;
        //         $error      = $action_obj->error;
        //         $sql        = $action_obj->sql;
        //         $msg        = "error";
        //     }
        // }

        // $json_array   = [
        //     "status"    => $status,
        //     "data"      => $data,
        //     "error"     => $error,
        //     "msg"       => $msg,
        //     "sql"       => $sql
        // ];

        // echo json_encode($json_array);

    break;
    
    case 'experience_add_update':


        $staff_unique_id         = $_POST["staff_unique_id"];        
        $company_names            = $_POST["company_names"];
        $salary_amt              = $_POST["salary_amt"];
        // $doc_name                = $_POST["test_doc"];
        $designation_name        = $_POST["designation_name"];
        $join_month              = $_POST["join_month"];
        $relieve_month           = $_POST["relieve_month"];
        $exp                     = $_POST["exp"];
        $unique_id               = $_POST["unique_id"];
        $doc_names = $_POST["doc_names"];
        $update_where                       = "";
        
        // if (is_array($_FILES["test_file"]['name'])) {
        //     if ($_FILES["test_file"]['name'][0] != "") {
        //         // Multi file Upload 
        //         $confirm_upload     = $fileUpload->uploadFiles("test_file");
        //             if (is_array($confirm_upload)) {
        //                 $_FILES["test_file"]['file_name'] = [];
        //                     foreach ($confirm_upload as $c_key => $c_value) {
        //                         if ($c_value->status == 1) {
        //                             $c_file_name = $c_value->name ? $c_value->name.".".$c_value->ext : "";
        //                             array_push($_FILES["test_file"]['file_name'],$c_file_name);
        //                         } else {// if Any Error Occured in File Upload Stop the loop
        //                             $status     = $confirm_upload->status;
        //                             $data       = "file not uploaded";
        //                             $error      = $confirm_upload->error;
        //                             $sql        = "file upload error";
        //                             $msg        = "file_error";
        //                             break;
        //                         }
        //                     }  
        //             } else if (!empty($_FILES["test_file"]['name'])) {// Single File Upload
        //                 $confirm_upload     = $fileUpload->uploadFile("test_file");
                        
        //                 if($confirm_upload->status == 1) {
        //                     $c_file_name = $confirm_upload->name ? $confirm_upload->name.".".$confirm_upload->ext : "";
        //                     $_FILES["test_file"]['file_name']  = $c_file_name;
        //                 } else {// if Any Error Occured in File Upload Stop the loop
        //                     $status     = $confirm_upload->status;
        //                     $data       = "file not uploaded";
        //                     $error      = $confirm_upload->error;
        //                     $sql        = "file upload error";
        //                     $msg        = "file_error";
        //                 }                    
        //             }
        //     }
        // }

        // if (is_array($_FILES["test_file"]['name'])) {
        //     if ($_FILES["test_file"]['name'][0] != "") {
        //         $file_names     = implode(",",$_FILES["test_file"]['file_name']);
        //         $file_org_names = implode(",",$_FILES["test_file"]['name']);
        //     }                            
        // } else if (!empty($_FILES["test_file"]['name'])) {
        //     $file_names     = $_FILES["test_file"]['file_name'];
        //     $file_org_names = $_FILES["test_file"]['name'];
        // }
        for($i=0;$i<count($_FILES['test_file']['name']);$i++){
            // $allowTypes = array('jpg','png','jpeg','gif'); 
            $file_name[]=basename($_FILES['test_file']['name'][$i]);
            $uploadfile=$_FILES['test_file']['name'][$i];
        
            // $tem_name[$i] =  random_strings(25).".".$file_name[$i];
        
            $targetDir = "../../uploads/q_doc/. $file_name[$i]";
            // $targetDir = "../../uploads/q_doc/. $tem_name[$i]";
        
        
            // $newname1 = "CALIBRATION_" . rand(10000,99999) . "." .  $targetDir; 
         
            // if(in_array($targetDir,$allowTypes)){ 
             move_uploaded_file($uploadfile,$targetDir);
            //  }
        }
        $images=implode(',',$_FILES['test_file']['name']);
        
        $columns            = [
            "staff_unique_id"    => $staff_unique_id,
            "company_name"       => $company_names,
            "salary_amt"         => $salary_amt,
            "doc_name"           => $images,
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
        $select_where       = 'company_name ="'.$company_name.'" AND is_delete = 0  AND designation_name = "'.$designation_name.'"  ';

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
             // Function Name button prefix
        $btn_edit_delete    = "staff_experience_details";
                // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns1        = [
            "@a:=@a+1 s_no",
            "company_name",
            "designation_name",
            "doc_name",
            "salary_amt",
            "join_month",
            "relieve_month",
            "exp",
            "staff_unique_id",
            "unique_id"
        ];
        $i = 1;
        $table_details1  = [
            $table_staff_experience_details,
            $columns1
        ];
        $where1          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result4         = $pdo->select($table_details1,$where1,$limit,$start,$order_by,$sql_function);
        
            $res_array      = $result4->data;

            
    $data1 =  '<table class="table dt-responsive nowrap w-100">';
    $data1 .= '<tr>
    <th>#</th>
    <th>Company</th>
    <th>Designation</th>
    <th>Document Image</th>
    <th>Salary</th>
    <th>Joining Month</th>
    <th>Relieving Month</th>
    <th>Experience</th>
    <th>Action</th>
 </tr>';
    foreach ($res_array as $key => $item) {
        
        
        

$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['company_name'] . '</td>';
$data1 .= '<td>' . $item['designation_name'] . '</td>';
// $data1 .= '<td>' . $item['doc_name'] . '</td>';
$data1 .= '<td>' . image_view1("staff", $item['unique_id'], $item['doc_name']) .'</td>';
$data1 .= '<td>' . $item['salary_amt'] . '</td>';
$data1 .= '<td>' . $item['join_month'] . '</td>';
$data1 .= '<td>' . $item['relieve_month'] . '</td>';
$data1 .= '<td>' . $item['exp'] . '</td>';
$data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item['unique_id'],$item['staff_unique_id']).'</td>';


// $data1 .= '<td>' . $item['name'] . '</td>';
// $data1 .= '<td>' . $item['name'] . '</td>';
$data1 .= '</tr>';

    }
    $data1 .= '</table>';
        
//     if ($action_obj->status) {
//         $status     = $action_obj->status;
//         $data       = $data1;
//         $error      = "";
//         $sql        = $result->sql;

//         if ($unique_id) {
//             $msg        = "update";
//         } else {
//             $msg        = "add";
//         }
//     } else {
//         $status     = $action_obj->status;
//         $data       = $data1;
//         $error      = $action_obj->error;
//         $sql        = $action_obj->sql;
//         $msg        = "error";
//     }
// }
// $json_array   = [
//     // "status"    => $status,
//     "data"      => $data1,
//     "error"     => $error,
//     "msg"       => $msg,
//     "sql"       => $sql
// ];
}
// }
$json_array = [
    'data'            => $data1,
];


echo json_encode($json_array);


    break;
    
    case 'experience_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "staff_experience_details";
        
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
            "company_name",
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
                $value['unique_id']     = $btn_edit.$btn_delete;
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
                // $value['doc_name'] = $value['doc_name'];
                // switch ($value['doc_name']) {
                //     case 1:
                //         $value['doc_name'] = "Image";
                //         break;
                //     case 2:
                //         $value['doc_name'] = "Document";
                //         break;
                   
                // }
                $btn_edit               = "";
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_edit.$btn_delete;
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
            "doc_name",
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
        // print_r($action_obj);

        // Function Name button prefix
        $btn_edit_delete    = "staff_qualification";

// Fetch Data
      $staff_unique_id = $_POST['staff_unique_id']; 



// Query Variables
$json_array     = "";
$columns1        = [
    "@a:=@a+1 s_no",
    "education_type",
    "degree",
    "doc_name",
    "college_name",
    "year_passing",
    "percentage",
    "university",
    "staff_unique_id",
    "unique_id"
];
$i = 1;
$table_details1  = [
    $table_staff_qualification_details,
    $columns1
];
$where1          = [
    "staff_unique_id"    => $staff_unique_id,
    "is_active"                     => 1,
    "is_delete"                     => 0
];

$order_by = "";

$sql_function   = "SQL_CALC_FOUND_ROWS";

$result1         = $pdo->select($table_details1,$where1);
// print_r($result);
// $total_records  = total_records();

// if ($result1->status) {

    $res_array2      = $result1->data;
            $data2 =  '<table class="table dt-responsive nowrap w-100">';
            $data2 .= '<tr>
            <th>#</th>
            <th>Education Type</th>
            <th>Degree</th>
            <th>Document Image</th>
            <th>College Name</th>
            <th>Year of Passing</th>
            <th>Percentage</th>
            <th>University</th>
            <th>Action</th>
            </tr>';
    foreach ($res_array2 as $key => $item_val) {
    $data2 .= '<tr>';
    $data2 .= '<td>' . $i++ . '</td>';
    $data2 .= '<td>' . $item_val['education_type'] . '</td>';
    $data2 .= '<td>' . $item_val['degree'] . '</td>';
    $data2 .= '<td>' . image_view1("staff", $item_val['unique_id'], $item_val['doc_name']) . '</td>';
    $data2 .= '<td>' . $item_val['college_name'] . '</td>';
    $data2 .= '<td>' . $item_val['year_passing'] . '</td>';
    $data2 .= '<td>' . $item_val['percentage'] . '</td>';
    $data2 .= '<td>' . $item_val['university'] . '</td>';
    $data2 .= '<td>'.btn_delete_new($btn_edit_delete,$item_val['unique_id'],$item_val['staff_unique_id']).'</td>';
    // $data2 .= '<td>' . $item['standard'] . '</td>';
   
    // $data2 .= '<td>' . $item['name'] . '</td>';
    // $data2 .= '<td>' . $item['name'] . '</td>';
    $data2 .= '</tr>';
            }
            $data2 .= '</table>';
        // }
        $json_array = [
            'data'            => $data2,
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
            "doc_name",
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

        // Function Name button prefix
        $btn_edit_delete    = "staff_experience_details";
                // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns1        = [
            "@a:=@a+1 s_no",
            "company_name",
            "designation_name",
            "doc_name",
            "salary_amt",
            "join_month",
            "relieve_month",
            "exp",
            "staff_unique_id",
            "unique_id"
        ];
        $i = 1;
        $table_details1  = [
            $table_staff_experience_details,
            $columns1
        ];
        $where1          = [
            "staff_unique_id"    => $staff_unique_id,
            // "unique_id"          => $unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result4         = $pdo->select($table_details1,$where1,$limit,$start,$order_by,$sql_function);
        
        $res_array      = $result4->data;
        
$i = 1;
            
    $data1 =  '<table class="table dt-responsive nowrap w-100">';
    $data1 .= '<tr>
    <th>#</th>
    <th>Company</th>
    <th>Designation</th>
    <th>Document Image</th>
    <th>Salary</th>
    <th>Joining Month</th>
    <th>Relieving Month</th>
    <th>Experience</th>
    <th>Action</th>
 </tr>';
foreach ($res_array as $key => $item) {
$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['company_name'] . '</td>';
$data1 .= '<td>' . $item['designation_name'] . '</td>';
// $data1 .= '<td>' . $item['doc_name'] . '</td>';
$data1 .= '<td>' . image_view1("staff", $item['unique_id'], $item['doc_name']) . '</td>';
$data1 .= '<td>' . $item['salary_amt'] . '</td>';
$data1 .= '<td>' . $item['join_month'] . '</td>';
$data1 .= '<td>' . $item['relieve_month'] . '</td>';
$data1 .= '<td>' . $item['exp'] . '</td>';
$data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item['unique_id'],$item['staff_unique_id']).'</td>';



// $data1 .= '<td>' . $item['name'] . '</td>';
// $data1 .= '<td>' . $item['name'] . '</td>';
$data1 .= '</tr>';

    }
    $data1 .= '</table>';
        

$json_array = [
    'data'            => $data1,
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
        $salary_types                = $_POST["salary_type"];
        $unique_id                  = $_POST["unique_id"];
            if($salary_types == ''){
                $salary_type = "Axis Bank";
            }else{
                $salary_type = $salary_types;
            }



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
            $select_where       = 'bank_status = "Primary" AND is_delete = 0 AND unique_id ="'.$unique_id.'"';
        }else{
            $select_where       = 'bank_name ="'.$bank_name.'" AND is_delete = 0  AND ifsc_code = "'.$ifsc_code.'" AND account_no = "'.$account_no.'"';
        }

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
        }
        // print_r($action_obj);
        // Function Name button prefix
        $btn_edit_delete    = "staff_account_details";
              // Fetch Data
        $staff_unique_id = $_POST['staff_unique_id']; 

        

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns1        = [
            "@a:=@a+1 s_no",
            "bank_status",
            "salary_type",
            "accountant_name",
            "account_no",
            "bank_name",
            "ifsc_code",
            "contact_no",
            "address",
            // "'' as active_status",
            "staff_unique_id",
            "unique_id"
        ];
        $table_details1  = [
            $table_account_details,
            $columns1
        ];
        $where1          = [
            "staff_unique_id"    => $staff_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        $order_by = "";
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result5         = $pdo->select($table_details1,$where1);
        // $total_records  = total_records();
        $res_array      = $result5->data;

        $i = 1;

            
    $data1 =  '<table class="table dt-responsive nowrap w-100">';
    $data1 .= ' <tr>
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
 </tr>';
    foreach ($res_array as $key => $item) {
        
        
        

$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['bank_status'] . '</td>';
$data1 .= '<td>' . $item['salary_type'] . '</td>';
$data1 .= '<td>' . $item['accountant_name'] . '</td>';
$data1 .= '<td>' . $item['account_no'] . '</td>';
$data1 .= '<td>' . $item['bank_name'] . '</td>';
$data1 .= '<td>' . $item['ifsc_code'] . '</td>';
$data1 .= '<td>' . $item['contact_no'] . '</td>';
$data1 .= '<td>' . $item['address'] . '</td>';
$data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item['unique_id'],$item['staff_unique_id']).'</td>';

// $data1 .= '<td>' . $item['name'] . '</td>';
// $data1 .= '<td>' . $item['name'] . '</td>';
$data1 .= '</tr>';




    }
    $data1 .= '</table>';
        
$json_array = [
    'data'            => $data1,
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
                // $value['active_status'] = '<input type ="checkbox"  id="check_qty'.$value['unique_id'].'" value="1" onchange="edit_status(this.value,\''.$value['unique_id'].'\')"><input type ="hidden"  id="sub_unique_id'.$value['unique_id'].'"  name="sub_unique_id[]" value="'.$value['unique_id'].'">';
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_edit.$btn_delete;
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
             "bank_name",
            "address",
            "ifsc_code",
            "accountant_name",
            "account_no",
            "contact_no",
            //"gst_no",
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
// Function Name button prefix
$btn_edit_delete    = "staff_account_details";
// Fetch Data
$staff_unique_id = $_POST['staff_unique_id']; 



$data	    = [];


if($length == '-1') {
$limit  = "";
}

// Query Variables
$json_array     = "";
$columns1        = [
"@a:=@a+1 s_no",
"bank_status",
"salary_type",
"accountant_name",
"account_no",
"bank_name",
"ifsc_code",
"contact_no",
"address",
// "'' as active_status",
"staff_unique_id",
"unique_id"
];
$table_details1  = [
$table_account_details,
$columns1
];
$where1          = [
"staff_unique_id"    => $staff_unique_id,
"is_active"                     => 1,
"is_delete"                     => 0
];

$order_by = "";

$sql_function   = "SQL_CALC_FOUND_ROWS";

$result5         = $pdo->select($table_details1,$where1);
// $total_records  = total_records();
$res_array      = $result5->data;

$i = 1;


$data1 =  '<table class="table dt-responsive nowrap w-100">';
$data1 .= ' <tr>
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
</tr>';
foreach ($res_array as $key => $item) {




$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['bank_status'] . '</td>';
$data1 .= '<td>' . $item['salary_type'] . '</td>';
$data1 .= '<td>' . $item['accountant_name'] . '</td>';
$data1 .= '<td>' . $item['account_no'] . '</td>';
$data1 .= '<td>' . $item['bank_name'] . '</td>';
$data1 .= '<td>' . $item['ifsc_code'] . '</td>';
$data1 .= '<td>' . $item['contact_no'] . '</td>';
$data1 .= '<td>' . $item['address'] . '</td>';
$data1 .= '<td>'.btn_delete_new($btn_edit_delete,$item['unique_id'],$item['staff_unique_id']).'</td>';


$data1 .= '</tr>';




}
$data1 .= '</table>';



$json_array = [
'data'            => $data1,
];


echo json_encode($json_array);
        // if ($action_obj->status) {
        //     $status     = $action_obj->status;
        //     $data       = $action_obj->data;
        //     $error      = "";
        //     $sql        = $action_obj->sql;
        //     $msg        = "success_delete";

        // } else {
        //     $status     = $action_obj->status;
        //     $data       = $action_obj->data;
        //     $error      = $action_obj->error;
        //     $sql        = $action_obj->sql;
        //     $msg        = "error";
        // }

        // $json_array   = [
        //     "status"    => $status,
        //     "data"      => $data,
        //     "error"     => $error,
        //     "msg"       => $msg,
        //     "sql"       => $sql
        // ];

        // echo json_encode($json_array);
        
    break;



    case 'staff_account_details_delete1':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_account_details,$columns,$update_where);
     // Function Name button prefix
     $btn_edit_delete    = "staff_account_details";
     // Fetch Data
$staff_unique_id = $_POST['staff_unique_id']; 



$data	    = [];


if($length == '-1') {
   $limit  = "";
}

// Query Variables
$json_array     = "";
$columns1        = [
   "@a:=@a+1 s_no",
   "bank_status",
   "salary_type",
   "accountant_name",
   "account_no",
   "bank_name",
   "ifsc_code",
   "contact_no",
   "address",
   // "'' as active_status",
   "unique_id"
];
$table_details1  = [
   $table_account_details,
   $columns1
];
$where1          = [
   "staff_unique_id"    => $staff_unique_id,
   "is_active"                     => 1,
   "is_delete"                     => 0
];

$order_by = "";

$sql_function   = "SQL_CALC_FOUND_ROWS";

$result5         = $pdo->select($table_details1,$where1);
// $total_records  = total_records();
$res_array      = $result5->data;

$i = 1;

   
$data1 =  '<table class="table dt-responsive nowrap w-100">';
$data1 .= ' <tr>
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
</tr>';
foreach ($res_array as $key => $item) {




$data1 .= '<tr>';
$data1 .= '<td>' . $i++ . '</td>';
$data1 .= '<td>' . $item['bank_status'] . '</td>';
$data1 .= '<td>' . $item['salary_type'] . '</td>';
$data1 .= '<td>' . $item['accountant_name'] . '</td>';
$data1 .= '<td>' . $item['account_no'] . '</td>';
$data1 .= '<td>' . $item['bank_name'] . '</td>';
$data1 .= '<td>' . $item['ifsc_code'] . '</td>';
$data1 .= '<td>' . $item['contact_no'] . '</td>';
$data1 .= '<td>' . $item['address'] . '</td>';
$data1 .= '<td>'.btn_delete($btn_edit_delete,$item['unique_id']).'</td>';
$data1 .= '</tr>';




}
$data1 .= '</table>';

// $msg        = "success_delete";


$json_array = [
'data'            => $data1,
// 'msg'             => $msg
];


echo json_encode($json_array);
// if ($action_obj->status) {
//     $status     = $action_obj->status;
//     $data       = $action_obj->data;
//     $error      = "";
//     $sql        = $action_obj->sql;
//     $msg        = "success_delete";

// } else {
//     $status     = $action_obj->status;
//     $data       = $action_obj->data;
//     $error      = $action_obj->error;
//     $sql        = $action_obj->sql;
//     $msg        = "error";
// }

// $json_array   = [
//     "status"    => $status,
//     "data"      => $data,
//     "error"     => $error,
//     "msg"       => $msg,
//     "sql"       => $sql
// ];

// echo json_encode($json_array);

break;

    case 'delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_staff_official,$columns,$update_where);

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


    case 'states':

        $country_id          = $_POST['country_id'];

        $pre_state_options  = state("",$country_id);

        $pre_state_options  = select_option($pre_state_options,"Select the State");

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

        $emp_prefix = "AED";

        $emp_id  = emp_id("staff",$emp_prefix);

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
function random_strings($length_of_string)
{
 
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
 
    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result),
                       0, $length_of_string);
}
// function image_view1($folder_name = "",$unique_id = "",$doc_name = "") {
//      //print_r($doc_name);
//   $file_names = explode(',', $doc_name);
  
//     $image_view = '';

//     if($doc_name){
//             foreach ($file_names as $file_key => $doc_name) { 
      
//                 if($file_key!=0){
//                     if($file_key%4!=0){
//                         $image_view .= "&nbsp";
//                     } else {
//                         $image_view .= "<br><br>"; 
//                     }
//                 }
       
//                 $cfile_name = explode('.',$doc_name);
//                 // print_r($cfile_name);
//                 // if($doc_name){  
//                 //     if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
//                 //     $image_view .= '<a href="javascript:print(\'uploads/staff/'.$doc_name.'\')"><img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" ></a>';
//                 //     // $image_view .= '<img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" >';
//                 //     }else if($cfile_name[1]=='pdf'){
//                 //         $image_view .= '<a href="javascript:print(\'uploads/staff/'.$doc_name.'\')"><img src="uploads/staff/pdf.png"  height="50px" width="50px" ></a>';
//                 //     }
//                 //     else if(($cfile_name[1]=='pdf')||($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')){
//                 //         $image_view .= '<a href="javascript:print(\'uploads/staff/'.$doc_name.'\')"><img src="uploads/staff/excel.png"  height="50px" width="50px" ></a>';
//                 //     }
//                 // }
//                 if($doc_name){  
//                     if(($cfile_name[1]=='jpg')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
//                     $image_view .= '<a href="javascript:print(\'../../uploads/q_doc/'.$doc_name.'\')"><img src="../../uploads/q_doc/'.$doc_name.'"  height="50px" width="50px" ></a>';
//                     // $image_view .= '<img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" >';
//                     }else if($cfile_name[1]=='pdf'){
//                         $image_view .= '<a href="javascript:print(\'../../uploads/q_doc/'.$doc_name.'\')"><img src="uploads/staff/pdf.png"  height="50px" width="50px" ></a>';
//                     }
//                     else if(($cfile_name[1]=='pdf')||($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')){
//                         $image_view .= '<a href="javascript:print(\'../.../uploads/q_doc/'.$doc_name.'\')"><img src="uploads/staff/excel.png"  height="50px" width="50px" ></a>';
//                     }
//                 }
               
//             }
//     }
//     // print_r($image_view);    
//        return $image_view;
// }
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
                   if(($cfile_name[1]=='jpg')||($cfile_name[1]=='JPG')||($cfile_name[1]=='png')||($cfile_name[1]=='jpeg')) {
                   $image_view .= '<a href="javascript:print(\'uploads/q_doc/'.$doc_name.'\')"><img src="../../uploads/q_doc/'.$doc_name.'"  height="50px" width="50px" ></a>';
                   // $image_view .= '<img src="uploads/'.$folder_name.'/'.$doc_name.'"  height="50px" width="50px" >';
                   }else if($cfile_name[1]=='pdf'){
                       $image_view .= '<a href="javascript:print(\'uploads/q_doc/'.$doc_name.'\')"><img src="../../uploads/q_doc/pdf.png"  height="50px" width="50px" ></a>';
                   }
                   else if(($cfile_name[1]=='pdf')||($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')){
                       $image_view .= '<a href="javascript:print(\'uploads/q_doc/'.$doc_name.'\')"><img src="../../uploads/q_doc/excel.png"  height="50px" width="50px" ></a>';
                   }
               }
              
           }
   }
   // print_r($image_view);    
      return $image_view;
}
?>