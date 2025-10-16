<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database able Names
$table_customer_profile             = "customer_profile";
$table_contact_person               = "customer_contact_person";
$table_invoice_details              = "customer_invoice_details";
$table_customer_potential_mapping   = "customer_potential_mapping";
$table_account_details              = "customer_account_details";
$table_shipping_details             = "customer_shipping_details";
$table_billing_details              = "cust_billing_details";
$documents_upload                   = "customer_document_upload";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Include this folder only functions
include 'function.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";
$prefix             = "cust";


$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {



case 'createupdate':

    // Collect form values
    // $customer_category      = $_POST["customer_category"];
    $customer_sub_category  = $_POST["customer_sub_category"];
    $customer_group         = $_POST["customer_group"];
    $currency               = $_POST["currency"];
    $customer_name          = $_POST["customer_name"];
    $customer_no            = $_POST["customer_no"];
    $country_name           = $_POST["country_name"];
    $state_name             = $_POST["state_name"];
    $city_name              = $_POST["city_name"];
    $gst_status             = $_POST["gst_status"];
    $gst_no                 = $_POST["gst_no"];
    $address                = $_POST["address"];
    $pincode                = $_POST["pincode"];
    $mobile_no              = $_POST["mobile_no"];
    $phone_no               = $_POST["phone_no"];
    $email                  = $_POST["email"];
    $pan_no                 = $_POST["pan_no"];
    $executive_name         = $_POST["executive_name"];
    $provisional_no         = $_POST["provisional_no"];
    $unique_id              = $_POST["unique_id"];

    $account_status = $_POST["account_status"] ?? 0;
    $account_type   = $_POST["account_type"] ?? 0;

   
    $doc_up_filenames = [];

    if (!empty($_FILES["test_file"]["name"][0])) {
        $target_dir = "../../uploads/customer_creation/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $existing_files = $_POST['existing_file_attach'] ?? '';
        $existing_array = array_filter(array_map('trim', explode(',', $existing_files)));

        $existing_hashes = [];
        
        foreach ($existing_array as $exist_file) {
            $exist_path = $target_dir . $exist_file;
            if (file_exists($exist_path)) {
                $existing_hashes[] = md5_file($exist_path);
            }
        }

        $current_upload_hashes = [];

        foreach ($_FILES["test_file"]["name"] as $key => $name) {
            $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            $file_tmp = $_FILES["test_file"]["tmp_name"][$key];
            $allowed_formats = ["jpg", "jpeg", "png", "pdf"];
    
            if (in_array($file_extension, $allowed_formats)) {
                $file_hash = md5_file($file_tmp);
    
                if (in_array($file_hash, $existing_hashes) || in_array($file_hash, $current_upload_hashes)) {
                    continue;
                }
    
                $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_extension;
                $target_file = $target_dir . $unique_filename;
    
                if (move_uploaded_file($file_tmp, $target_file)) {
                    $doc_up_filenames[] = $unique_filename;
                    $current_upload_hashes[] = $file_hash;
                }
            }
        }

        $combined_files = array_merge($existing_array, $doc_up_filenames);
        $doc_up_filename = implode(',', $combined_files);
    } else {
        $doc_up_filename = $_POST['existing_file_attach'] ?? '';
    }


    $table_details = [$table_customer_profile, ["COUNT(unique_id) AS count"]];
    $customer_no_select = $pdo->select($table_details);

    if ($customer_no_select->status) {
        $customer_count = $customer_no_select->data[0]['count'];
        if (!$customer_no) {
            $customer_no = "CUS" . sprintf("%06d", $customer_count + 1);
        }
    } else {
        echo json_encode([
            "status" => 0,
            "msg" => "error",
            "sql" => $customer_no_select->sql,
            "error" => $customer_no_select->error
        ]);
        exit;
    }

    $columns = [
        "customer_no"               => $customer_no,
        "customer_name"             => $customer_name,
        // "customer_category_id"      => $customer_category,
        "customer_sub_category_id"  => $customer_sub_category,
        "customer_group_id"         => $customer_group,
        "currency"                  => $currency,
        "country_unique_id"         => $country_name,
        "state_unique_id"           => $state_name,
        "city_unique_id"            => $city_name,
        "gst_status"                => $gst_status ?? '',
        "gst_no"                    => $gst_no ?? '',
        "address"                   => $address,
        "pincode"                   => $pincode,
        "mobile_no"                 => $mobile_no,
        "phone_no"                  => $phone_no,
        "pan_no"                    => $pan_no,
        // "crntly_acc_hndl_by"        => $executive_name ?? '',
        "email_id"                  => $email,
        "provisional_no"            => $provisional_no ?? '',
        // "account_status"            => $account_status,
        // "account_type"              => $account_type,
        // "file_attach"               => $doc_up_filename,
        "unique_id"                 => unique_id($prefix)
    ];

    $select_where = 'customer_name ="' . $customer_name . '" AND is_delete = 0 AND gst_no = "' . $gst_no . '"';
    
    if ($unique_id) {
        $select_where .= ' AND unique_id != "' . $unique_id . '"';
    }

    $action_obj = $pdo->select([$table_customer_profile, ["COUNT(unique_id) AS count"]], $select_where);

    if (!$action_obj->status) {
        echo json_encode([
            "status" => 0,
            "msg"    => "error",
            "sql"    => $action_obj->sql,
            "error"  => $action_obj->error
        ]);
        exit;
    }

    if ($action_obj->data[0]["count"]) {
        $msg = "already";
    } else {
        if ($unique_id) {
            unset($columns['unique_id'], $columns['customer_no']);
            $update_where = ["unique_id" => $unique_id];
            $action_obj = $pdo->update($table_customer_profile, $columns, $update_where);
            $msg = "update";
        } else {
            $action_obj = $pdo->insert($table_customer_profile, $columns);
            $msg = "create";
        }
    }

    echo json_encode([
        "status"              => $action_obj->status,
        "data"                => $action_obj->data,
        "error"               => $action_obj->error,
        "msg"                 => $msg,
        "sql"                 => $action_obj->sql,
        "customer_no"         => $customer_no,
        "customer_unique_id"  => $unique_id ?: $columns['unique_id']
    ]);

    break;

    case 'customer_master_delete':
        $customer_unique_id = $_POST['customer_unique_id'];
        $columns = [
            "is_delete" => 1,
        ];
        $update_where = [
            "unique_id" => $customer_unique_id
        ];
        $action_obj = $pdo->update($table_customer_profile, $columns, $update_where);
        error_log("Delete action for customer_unique_id: $customer_unique_id, Result: " . print_r($action_obj, true) . "\n", 3, 'delete_log.txt');
        if ($action_obj->status) {
            $status = $action_obj->status;
            $data = $action_obj->data;
            $error = "";
            $sql = $action_obj->sql;
            $msg = "success_delete";
        } else {
            $status = $action_obj->status;
            $data = $action_obj->data;
            $error = $action_obj->error;
            $sql = $action_obj->sql;
            $msg = "error";
        }
        $json_array = [
            "status" => $status,
            "data" => $data,
            "error" => $error,
            "msg" => $msg,
            "sql" => $sql
        ];
        echo json_encode($json_array);
    break;

    case 'copy_billing_data':
        $customer_unique_id = $_POST['customer_unique_id'];

        $columns = [
            "name",
            "billing_address",
            "country",
            "state",
            "city",
            "contact_name",
            "contact_no",
            "billing_gst_no",
            "gst_status",
            "ecc_no"
        ];

        $details = [
            $table_billing_details,
            $columns
        ];

        $action_obj = $pdo->select(
            $details,
            [
                "customer_profile_unique_id" => $customer_unique_id,
                "is_active" => 1,
                "is_delete" => 0
            ]
        );

        error_log("Copy Billing Data Action: " . print_r($action_obj, true) . "\n", 3, 'billing_copy_log.txt');

        if ($action_obj->status && !empty($action_obj->data)) {
            $billing_data = $action_obj->data[0]; // use first record
            $status = 1;
            $msg = "billing_data_found";
            $error = "";
        } else {
            $billing_data = [];
            $status = 0;
            $msg = "billing_data_not_found";
            $error = $action_obj->error ?? "No data";
        }

        $json_array = [
            "status" => $status,
            "data" => $billing_data,
            "msg" => $msg,
            "error" => $error,
            "sql" => $action_obj->sql ?? ""
        ];
        echo json_encode($json_array);
    break;



    case 'contact_person_add_update':

            $contact_person_name        = $_POST["contact_person_name"];
            $customer_unique_id         = $_POST["customer_unique_id"];
            $contact_person_designation = $_POST["contact_person_designation"];
            $contact_person_address1    = $_POST["contact_person_address1"];
            $contact_person_address2    = $_POST["contact_person_address2"];
            $contact_person_email       = $_POST["contact_person_email"];
            $contact_person_contact_no  = $_POST["contact_person_contact_no"];
            $unique_id                  = $_POST["unique_id"];
            $update_where               = "";
    
            $columns    = [
                "customer_profile_unique_id"    => $customer_unique_id,
                "contact_person_name"           => $contact_person_name,
                "contact_person_designation"    => $contact_person_designation,
                "contact_person_address1"       => $contact_person_address1,
                "contact_person_address2"       => $contact_person_address2,
                "contact_person_email"          => $contact_person_email,
                "contact_person_contact_no"     => $contact_person_contact_no,
                "unique_id"                     => unique_id($prefix)
            ];
    

            $table_details      = [
                $table_contact_person,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where   = 'contact_person_name ="'.$contact_person_name.'" AND is_delete = 0  AND contact_person_designation = "'.$contact_person_designation.'" AND contact_person_contact_no = "'.$contact_person_contact_no.'" ';
    
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
                $msg    = "already";
            } else if (($data[0]["count"] == 0) && ($msg != "error")) {
                // Update Begins
                if($unique_id) {
                    unset($columns['unique_id']);
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_contact_person,$columns,$update_where);
    
                } else {
                    $action_obj     = $pdo->insert($table_contact_person,$columns);
                }
    
                if ($action_obj->status) {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = "";
                    $sql        = $action_obj->sql;
    
                    if ($unique_id) {
                        $msg    = "update";
                    } else {
                        $msg    = "add";
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
        

    case 'contact_person_datatable':
        
        $btn_edit_delete    = "contact_person";
        
        $customer_unique_id = $_POST['customer_unique_id']; 

        // DataTable 
		$search 	= $_POST['search']['value'];    
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];

        error_log("POST: " . $_POST . "\n", 3, 'contact_person_log.txt');
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        
        $columns        = [
            "@a:=@a+1 s_no",
            "contact_person_name",
            "contact_person_designation",
            "contact_person_email",
            "contact_person_contact_no",            
            "unique_id"
        ];
        $table_details  = [
            $table_contact_person." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "customer_profile_unique_id"    => $customer_unique_id,
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
        

    case "contact_person_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "contact_person_name",
            "contact_person_designation",
            "contact_person_address1",
            "contact_person_address2",
            "contact_person_email",
            "contact_person_contact_no",
            "unique_id"
        ];
        $table_details  = [
            $table_contact_person,
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
        

    case 'contact_person_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_contact_person,$columns,$update_where);

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

    case 'cp_statutory':
        // Gather the raw statutory fields
        $statFields = [
            'ecc_no', 'commissionerate', 'division', 'stat_range',
            'cst_no', 'tin_no', 'service_tax_no', 'iec_code',
            'cin_no', 'tan_no'
        ];

        error_log("POST DATA: " . print_r($_POST, true) . "\n", 3, 'st_log.txt');
        
        // 1) Build your payload array
        $customerId = $_POST['customer_unique_id'] ?? '';
        $uniqueId   = $_POST['unique_id']         ?? ''; // editing?

        // If *all* statutory fields are empty, short-circuit with success
        $allEmpty = true;
        foreach ($statFields as $f) {
            if (!empty($_POST[$f] ?? '')) {
                $allEmpty = false;
                break;
            }
        }

        error_log("ALL EMPTY? " . ($allEmpty ? 'yes' : 'no') . "\n", 3, 'st_log.txt');

        // Exit early if all fields are empty AND no unique_id (nothing to save or update)
        if ($allEmpty && empty($uniqueId)) {
            echo json_encode([
                'status' => 1,
                'msg'    => 'no statutory data to save, moving on',
                'data'   => null,
                'error'  => '',
                'sql'    => ''
            ]);
            break; // 🔒 Prevents further execution of this case block
        }

        $columns = [
            'unique_id'                    => $uniqueId ?: unique_id($prefix),
            'customer_profile_unique_id'   => $customerId,
            'ecc_no'                       => $_POST['ecc_no']             ?? '',
            'commissionerate'              => $_POST['commissionerate']    ?? '',
            'division'                     => $_POST['division']           ?? '',
            'stat_range'                   => $_POST['stat_range']         ?? '',
            'cst_no'                       => $_POST['cst_no']             ?? '',
            'tin_no'                       => $_POST['tin_no']             ?? '',
            'service_tax_no'               => $_POST['service_tax_no']     ?? '',
            'iec_code'                     => $_POST['iec_code']           ?? '',
            'cin_no'                       => $_POST['cin_no']             ?? '',
            'tan_no'                       => $_POST['tan_no']             ?? '',
            // audit trail
            'acc_year'                     => $_SESSION['acc_year']        ?? '',
            'sess_user_type'               => $_SESSION['sess_user_type']  ?? '',
            'sess_user_id'                 => $_SESSION['sess_user_id']    ?? '',
            'sess_company_id'              => $_SESSION['sess_company_id'] ?? '',
            'sess_branch_id'               => $_SESSION['sess_branch_id']  ?? '',
        ];

        error_log("STATUTORY DETAILS: " . print_r($columns, true) . "\n", 3, 'st_log.txt');

        $table = 'cust_statutory_details';

        // 2) Check for existing record
        if (!empty($uniqueId)) {
            // check directly by unique_id
            $check = $pdo->select([$table, ['COUNT(unique_id) AS count']], 'unique_id = "' . $uniqueId . '"');
        } else {
            // check if any record exists for this customer
            $check = $pdo->select([$table, ['COUNT(unique_id) AS count']], 'customer_profile_unique_id = "' . $customerId . '"');
        }

        error_log("STATUTORY DETAILS CHECK: " . print_r($check->data, true) . "\n", 3, 'st_log.txt');

        // 3) Decide: UPDATE or INSERT
        if (!empty($uniqueId) && $check->data[0]['count'] > 0) {
            // we’ve got an existing row → do an UPDATE
            unset($columns['unique_id']);
            $update_where = ['unique_id' => $uniqueId];
            $action_obj   = $pdo->update($table, $columns, $update_where);
            error_log("UPDATING STATUTORY DETAILS: " . print_r($action_obj, true) . "\n", 3, 'st_log.txt');
            $msg          = 'update';
            
        } else {
            // no existing → do an INSERT
            $action_obj = $pdo->insert($table, $columns);
            error_log("INSERTING STATUTORY DETAILS: " . print_r($action_obj, true) . "\n", 3, 'st_log.txt');
            $msg        = 'create';
        }
        
        // 4) Marshal your JSON response
        if ($action_obj->status) {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = '';
            $sql    = $action_obj->sql;
        } else {
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = 'error';
        }

        error_log("STATUTORY DETAILS RESPONSE: " . print_r([
            'status' => $status,
            'data'   => $data,
            'error'  => $error,
            'msg'    => $msg,
            'sql'    => $sql
        ], true) . "\n", 3, 'st_log.txt');
        
        echo json_encode([
            'status' => $status,
            'data'   => $data,
            'error'  => $error,
            'msg'    => $msg,
            'sql'    => $sql
        ]);
    break;


    // Invoice Details Section Starts

    case 'invoice_details_add_update':

        $customer_unique_id         = $_POST["customer_unique_id"];
        $delivery_details           = $_POST["delivery_details"];
        $invoice_details            = $_POST["invoice_details"];
        $transport_courier_details  = $_POST["transport_courier_details"];
        $gst_no                     = $_POST["gst_no"];
        $tan_no                     = $_POST["tan_no"];
        $pan_no                     = $_POST["pan_no"];
        $web_address                = $_POST["web_address"];
        $email_id                   = $_POST["email_id"];
        $unique_id                  = $_POST["unique_id"];

        $update_where               = "";

        $columns            = [
            "customer_profile_unique_id"    => $customer_unique_id,
            "delivery_details"              => $delivery_details,
            "invoice_details"               => $invoice_details,
            "transport_courier_details"     => $transport_courier_details,
            "gst_no"                        => $gst_no,
            "tan_no"                        => $tan_no,
            "pan_no"                        => $pan_no,
            "web_address"                   => $web_address,
            "email_id"                      => $email_id,
            "unique_id"                     => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_invoice_details,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'gst_no ="'.$gst_no.'" AND is_delete = 0  AND pan_no = "'.$pan_no.'" AND email_id = "'.$email_id.'" ';

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

                $action_obj     = $pdo->update($table_invoice_details,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_invoice_details,$columns);
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
    

    case 'invoice_details_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "invoice_details";
        
        // Fetch Data
        $customer_unique_id = $_POST['customer_unique_id']; 

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
            "delivery_details",
            "invoice_details",
            "transport_courier_details",
            "gst_no",
            "tan_no",
            "pan_no",
            "web_address",
            "email_id",
            "unique_id"
        ];
        $table_details  = [
            $table_invoice_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "customer_profile_unique_id"    => $customer_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        $where = " is_delete = '0' ";

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
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
    
        
    case "invoice_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "delivery_details",
            "invoice_details",
            "transport_courier_details",
            "gst_no",
            "pan_no",
            "tan_no",
            "web_address",
            "email_id",
            "unique_id"
        ];
        $table_details  = [
            $table_invoice_details,
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
    

    case 'invoice_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_invoice_details,$columns,$update_where);

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

    // Start logging
    $log_file = __DIR__ . "/where_log.txt";
    error_log("=== DATATABLE CASE TRIGGERED ===\n", 3, $log_file);

    // Safe POST handling
    $search = isset($_POST['search']['value']) ? $_POST['search']['value'] : '';
    $length = isset($_POST['length']) ? $_POST['length'] : 10;
    $start  = isset($_POST['start']) ? $_POST['start'] : 0;
    $draw   = isset($_POST['draw']) ? $_POST['draw'] : 1;
    $limit  = $length;

    if ($length == '-1') {
        $limit = '';
    }

    error_log("POST DATA: " . print_r($_POST, true) . "\n", 3, $log_file);

    $data = array();
    $json_array = array();

    $columns = array(
        "@a:=@a+1 s_no",
        "customer_name",
        "customer_no",
        "(SELECT csc.customer_sub_category FROM customer_sub_category csc WHERE csc.unique_id = $table_customer_profile.customer_sub_category_id) AS customer_sub_category",
        "(SELECT cg.customer_group FROM customer_group cg WHERE cg.unique_id = $table_customer_profile.customer_group_id) AS customer_group",
        "gst_no",
        "(SELECT user_name FROM user WHERE user.unique_id = $table_customer_profile.sess_user_id) AS staff_id",
        "is_active", 
        "unique_id"
    );

    $table_details = array(
        $table_customer_profile . " , (SELECT @a:= " . intval($start) . ") AS a ",
        $columns
    );

    $where = " is_delete = 0 ";

    if (!empty($search)) {
        $where .= " AND (customer_name LIKE '" . mysql_like($search) . "' ";
        $where .= " OR customer_no LIKE '" . mysql_like($search) . "' ";
        $where .= " OR mobile_no LIKE '" . mysql_like($search) . "' ";
        $where .= " OR customer_sub_category_id IN (" . category_sub_name_like($search) . ") ";
        $where .= " OR customer_group_id IN (" . customer_group_name_like($search) . ")) ";
    }

    $order_column = isset($_POST['order'][0]['column']) ? $_POST['order'][0]['column'] : 0;
    $order_dir    = isset($_POST['order'][0]['dir']) ? $_POST['order'][0]['dir'] : 'asc';
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);

    $search_condition = datatable_searching($search, $columns);
    if (!empty($search_condition)) {
        $where .= " AND " . $search_condition;
    }

    error_log("FINAL WHERE CLAUSE: $where\n", 3, $log_file);
    error_log("ORDER BY: $order_by\n", 3, $log_file);
    error_log("TABLE DETAILS: " . print_r($table_details, true) . "\n", 3, $log_file);

    $sql_function = "SQL_CALC_FOUND_ROWS";

    error_log("== Executing PDO Select ==\n", 3, $log_file);
    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records = total_records();

    if ($result->status) {
        $res_array = $result->data;

        error_log("== DB Query Success: Found " . count($res_array) . " rows ==\n", 3, $log_file);

        foreach ($res_array as $key => $value) {
            error_log("== Processing Row $key ==\n", 3, $log_file);
            error_log(print_r($value, true) . "\n", 3, $log_file);

            $staff_name = staff_name($value['staff_id']);

            if (!isset($value['file_attach']) || trim($value['file_attach']) == '') {
                $value['file_attach'] = "<span class='font-weight-bold'>No Image Uploaded</span>";
            } else {
                $image_files = explode(',', $value['file_attach']);
                $image_buttons = "";
                foreach ($image_files as $image_file) {
                    $image_path = "../blue_planet_erp/uploads/customer_creation/" . trim($image_file);
                    $image_buttons .= "<button onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'>
                        <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                    </button>";
                }
                $value['file_attach'] = $image_buttons;
            }

           $btn_update  = btn_update($folder_name, $value['unique_id']);
            $btn_toggle  = ($value['is_active'] == "1")
                ? btn_toggle_on($folder_name, $value['unique_id'])
                : btn_toggle_off($folder_name, $value['unique_id']);
            
            $value['unique_id'] = $btn_update . $btn_toggle;
            
            // ❌ Don't show Active/Inactive as text
            unset($value['is_active']); // Remove this key before sending to UI
            
            $data[] = array_values($value); // Reindex for DataTable


        }

        $json_array = array(
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data
        );
    } else {
        error_log("== PDO Select Failed ==\n", 3, $log_file);
        error_log(print_r($result, true) . "\n", 3, $log_file);
    }

    error_log("== Final JSON Output Sent ==\n", 3, $log_file);
    echo json_encode($json_array);
    break;


    // Customer Potential Mappting Section Starts  

    case 'customer_potential_mapping_add_update':


        $customer_unique_id                     = $_POST["customer_unique_id"];
        $financial_year                         = $_POST["financial_year"];
        // $customer_mapping_financial_year        = $_POST["customer_mapping_financial_year"];
        // $value_of_year                          = $_POST["value_of_year"];
        // $purchase_pattern                       = $_POST["purchase_pattern"];

        $product_group                          = $_POST["product_group"];
        $potential_value                        = $_POST["potential_value"];
        $bis_forcast                            = $_POST["bis_forcast"];
        $unique_id                              = $_POST["unique_id"];

        $update_where                           = "";

        $columns            = [
            "customer_profile_unique_id"        => $customer_unique_id,
            "financial_year"                    => $financial_year,
            "item_group_unique_id"              => $product_group,
            "potential_value"                   => $potential_value,
            "bis_forcast"                       => $bis_forcast,
            "unique_id"                         => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_customer_potential_mapping,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = ' is_delete = 0  AND financial_year = "'.$financial_year.'" AND item_group_unique_id = "'.$product_group.'" AND customer_profile_unique_id = "'.$customer_unique_id.'" ';

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

                $action_obj     = $pdo->update($table_customer_potential_mapping,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_customer_potential_mapping,$columns);
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
    
    case 'customer_potential_mapping_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "customer_potential_mapping";
        
        // Fetch Data
        $customer_unique_id = $_POST['customer_unique_id']; 

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
            "financial_year",
            "item_group_unique_id",
            "potential_value",
            "bis_forcast",
            "unique_id"
        ];
        $table_details  = [
            $table_customer_potential_mapping." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "customer_profile_unique_id"    => $customer_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];

        error_log("WHERE: " . print_r($where, true) . "\n", 3, 'customer_potential_mapping.log');
        $order_by       = " financial_year DESC ";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        error_log("SQL QUERY: " . print_r($result, true) . "\n", 3, 'customer_potential_mapping.log');

        if ($result->status) {

            $res_array      = $result->data;

            $curr_fin_year               = "";

            if (!empty($res_array)) {

                $curr_fin_year           = $res_array[0]['financial_year'];

            }

            $customer_total_potential    = 0;
            $bis_total_potential         = 0;

            foreach ($res_array as $key => $value) {

                // Financial Year Wise grouping Data
                if ($curr_fin_year == $value['financial_year']) {

                    $customer_total_potential   += $value['potential_value'];
                    $bis_total_potential        += $value['bis_forcast'];

                } else {
                    $custom_value   = [
                        "",
                        "",
                        "<h4 class='text-primary text-right m-1'> Total : </h4>",
                        '<h5 class="text-right text-primary m-1">'.moneyFormatIndia($customer_total_potential).'</h5>',
                        '<h5 class="text-right text-primary m-1">'.moneyFormatIndia($bis_total_potential).'</h5>',
                        ""
                    ];
                    
                    // Push Custom Row In Existing Array
                    $data[]                      = $custom_value;
                    $customer_total_potential    = 0;
                    $bis_total_potential         = 0;

                    $customer_total_potential   += $value['potential_value'];
                    $bis_total_potential        += $value['bis_forcast'];

                    // Change The Current financial Year
                    $curr_fin_year               = $value['financial_year'];
                }

                $value['item_group_unique_id']    =  item_group($value['item_group_unique_id'])[0]['group_name'];
                $value['financial_year']    =  account_year($value['financial_year'])[0]['account_year'];
                $value['potential_value']   = '<h5 class="text-right text-info m-1">'.moneyFormatIndia($value['potential_value']).'</h5>';
                $value['bis_forcast']       = '<h5 class="text-right text-info m-1">'.moneyFormatIndia($value['bis_forcast']).'</h5>';

                $btn_edit                   = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete                 = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']         = $btn_edit.$btn_delete;
                $data[]                     = array_values($value);
            }

            // print_r($res_array);

            if (!empty($res_array)) {

                // Add final custom Value in Array
                $custom_value   = [
                    "",
                    "",
                    "<h4 class='text-primary text-right m-1'> Total : </h4>",
                    '<h5 class="text-right text-primary m-1">'.moneyFormatIndia($customer_total_potential).'</h5>',
                    '<h5 class="text-right text-primary m-1">'.moneyFormatIndia($bis_total_potential).'</h5>',
                    ""
                ];

                $data[]                      = $custom_value;

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
    
    case "customer_potential_mapping_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "s_no",
            "financial_year",
            "item_group_unique_id",
            "potential_value",
            "bis_forcast",
            "unique_id"
        ];
        $table_details  = [
            $table_customer_potential_mapping,
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

    case 'customer_potential_mapping_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_customer_potential_mapping,$columns,$update_where);

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

    case 'account_details_add_update':

        $customer_unique_id         = $_POST["customer_unique_id"];       
        $bank_name                  = $_POST["bank_name"];
        $bank_address               = $_POST["bank_address"];
        $ifsc_code                  = $_POST["ifsc_code"];
        $beneficiary_account_name   = $_POST["beneficiary_account_name"];
        $account_no                 = $_POST["account_no"];
        $unique_id                  = $_POST["unique_id"];

        $update_where               = "";

        $columns            = [
            "customer_profile_unique_id"   => $customer_unique_id,
            "bank_name"                    => $bank_name,
            "bank_address"                 => $bank_address,
            "ifsc_code"                    => $ifsc_code,
            "beneficiary_account_name"     => $beneficiary_account_name,
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
        $select_where       = 'bank_name ="'.$bank_name.'" AND is_delete = 0  AND ifsc_code = "'.$ifsc_code.'" AND account_no = "'.$account_no.'" ';

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

                $action_obj     = $pdo->update($table_account_details,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_account_details,$columns);
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
    
    case 'account_details_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "account_details";
        
        // Fetch Data
        $customer_unique_id = $_POST['customer_unique_id']; 

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
            "bank_name",
            "bank_address",
            "ifsc_code",
            "beneficiary_account_name",
            "account_no",
            "unique_id"
        ];
        $table_details  = [
            $table_account_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "customer_profile_unique_id"    => $customer_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        // $where = " is_delete = '0' ";

        // $order_column   = $_POST["order"][0]["column"];
        // $order_dir      = $_POST["order"][0]["dir"];

        // // Datatable Ordering 
        // $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // // Datatable Searching
        // $search         = datatable_searching($search,$columns);

        // if ($search) {
        //     if ($where) {
        //         $where .= " AND ";
        //     }

        //     $where .= $search;
        // }
        $order_by       = "";
        
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
    
    case "account_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data	    = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "bank_name",
            "bank_address",
            "ifsc_code",
            "beneficiary_account_name",
            "account_no",
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

    case 'account_details_delete':

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
    $is_active = $_POST['is_active']; // 1 or 0

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table_customer_profile, $columns, $update_where);

    if ($action_obj->status) {
        $status = true;
        $msg = $is_active ? "Activated Successfully" : "Deactivated Successfully";
    } else {
        $status = false;
        $msg = "Toggle failed!";
    }

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

/*case 'approve':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "approved_status"   => 1,
            "approved_by"       => $_SESSION["user_id"],
            "approved_date"     => $today,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_customer_profile,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_approve";

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
        */
        break;


    case 'states':

        $country_id          = $_POST['country_id'];

        $state_name_options  = state("",$country_id);

        $state_name_options  = select_option($state_name_options,"Select the State");

        echo $state_name_options;
        
        break;

    case 'cities':

        $state_id           = $_POST['state_id'];

        $city_name_options  = city("",$state_id);

        $city_name_options  = select_option($city_name_options,"Select the City");

        echo $city_name_options;
        
        break;
    
    case 'customer_sub_category_and_group':
        $json_data  = [
            "sub_category" => "<option disabled value='' selected>Select Customer Sub Category</option>",
            "group" => "<option disabled value='' selected>Select Customer Group</option>"
        ];

        $unique_id           = $_POST['unique_id'];

        if ($unique_id) {
            $customer_sub_category = customer_sub_category("", $unique_id);
            $customer_sub_category = select_option($customer_sub_category,"Select Customer Sub Category");

            $customer_group        = customer_group("", $unique_id);
            $customer_group        = select_option($customer_group,"Select Customer Sub Category");

            $json_data  = [
                "sub_category"  => $customer_sub_category,
                "group"         => $customer_group
            ];
        }

        echo json_encode($json_data);
        
        break;
    
    default:
        
    break;
    
    
    
    case 'billing_details_add_update':

        error_log("billing post data: " . print_r($_POST, true) . "\n", 3, "debug.txt");

        $customer_unique_id = $_POST["customer_unique_id"];       
        $name               = $_POST["billing_name"];
        $billing_address   = $_POST["billing_address"];
        $city               = $_POST["billing_city"];
        $country            = $_POST["billing_country"];
        $state              = $_POST["billing_state"];
        $contact_name       = $_POST["bill_contact_name"];
        $contact_no         = $_POST["bill_contact_no"];
        $billing_gst_no    = $_POST["bill_gst_no"];
        $gst_status         = $_POST["gst_status_bill"];
        $ecc_no             = $_POST["bill_ecc_no"];
        $unique_id          = $_POST["unique_id"];

        $columns = [
            "customer_profile_unique_id"    => $customer_unique_id,
            "name"                          => $name,
            "billing_address"              => $billing_address,
            "city"                          => $city,
            "country"                       => $country,
            "state"                         => $state,
            "contact_name"                  => $contact_name,
            "contact_no"                    => $contact_no,
            "billing_gst_no"               => $billing_gst_no,
            "gst_status"                    => $gst_status,
            "ecc_no"                        => $ecc_no
        ];

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Duplicate Check: Based on `name` + customer (change if needed)
        $table_details = [
            $table_billing_details,
            [ "COUNT(unique_id) AS count" ]
        ];

        $select_where = 'is_delete = 0 AND name = "'.$name.'" AND customer_profile_unique_id = "'.$customer_unique_id.'"';

        if ($unique_id) {
            $select_where .= ' AND unique_id != "'.$unique_id.'" ';
        }

        $action_obj = $pdo->select($table_details, $select_where);
        error_log("billing select query: " . $action_obj->sql . "\n", 3, "debug.txt");

        if (!$action_obj->status) {
            echo json_encode([
                "status" => false,
                "data"   => [],
                "error"  => $action_obj->error,
                "msg"    => "error",
                "sql"    => $action_obj->sql
            ]);
            break;
        }

        

        // Insert or Update
        if ($unique_id) {
            $update_where = [ "unique_id" => $unique_id ];
            $action_obj = $pdo->update($table_billing_details, $columns, $update_where);
            $msg = $action_obj->status ? "update" : "error";
        } else {
            if ($action_obj->data[0]["count"] > 0) {
                echo json_encode([
                    "status" => true,
                    "data"   => [],
                    "error"  => "",
                    "msg"    => "already",
                    "sql"    => $action_obj->sql
                ]);
                break;
            }
            $action_obj = $pdo->insert($table_billing_details, $columns);
            $msg = $action_obj->status ? "add" : "error";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);

    break;
    
    case 'billing_details_datatable':
        // Function Name button prefix
        $btn_edit_delete = "billing_details";

        // Fetch Data
        $customer_unique_id = $_POST['customer_unique_id'];

        // DataTable Inputs
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // SQL Column Selections
        $columns = [
            "@a:=@a+1 AS s_no",
            "name",
            "billing_address",
            "country",      
            "state",    
            "city", 
            "contact_name",
            "contact_no",
            "billing_gst_no",
            "gst_status",
            "ecc_no",
            "unique_id"
        ];

        $table_details = [
            "$table_billing_details, (SELECT @a:=$start) AS a",
            $columns
        ];


        $where = [
            "customer_profile_unique_id" => $customer_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                
                $country_data = country($value['country']);
                $state_data   = state($value['state']);
                $city_data    = city($value['city']);

            
                $value['country'] = (is_array($country_data) && isset($country_data[0]['name'])) ? $country_data[0]['name'] : '';
                $value['state']   = (is_array($state_data)   && isset($state_data[0]['state_name']))     ? $state_data[0]['state_name']     : '';
                $value['city']    = (is_array($city_data)    && isset($city_data[0]['city_name']))       ? $city_data[0]['city_name']       : '';


                $btn_edit           = btn_edit($btn_edit_delete, $value['unique_id']);
                $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_edit . $btn_delete;

                $data[] = array_values($value);
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

    case "billing_details_edit":
        $unique_id = $_POST['unique_id'];
    
        $columns = [
            "name",
            "billing_address",
            "city",
            "country",
            "state",
            "contact_name",
            "contact_no",
            "billing_gst_no",
            "gst_status",
            "ecc_no",
            "unique_id"
        ];
    
        $table_details = [$table_billing_details, $columns];
        $where = [
            "unique_id" => $unique_id,
            "is_active" => 1,
            "is_delete" => 0
        ];
    
        $result = $pdo->select($table_details, $where);
    
        if ($result->status && isset($result->data[0])) {
            $json_array = [
                "data" => $result->data[0],
                "status" => true,
                "sql" => $result->sql,
                "error" => ""
            ];
        } else {
            $json_array = [
                "status" => false,
                "error" => $result->error ?? "No data found",
                "msg" => "Error fetching data",
                "sql" => $result->sql ?? ""
            ];
        }
    
        echo json_encode($json_array);
    break;

    case 'billing_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_billing_details,$columns,$update_where);

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




    case 'shipping_details_add_update':

        $customer_unique_id = $_POST["customer_unique_id"];       
        $name               = $_POST["name"];
        $shipping_address   = $_POST["shipping_address"];
        $city               = $_POST["city"];
        $country            = $_POST["country"];
        $state              = $_POST["state"];
        $contact_name       = $_POST["contact_name"];
        $contact_no         = $_POST["contact_no"];
        $shipping_gst_no    = $_POST["shipping_gst_no"];
        $gst_status         = $_POST["gst_status"];
        $ecc_no             = $_POST["shipping_ecc_no"];
        $unique_id          = $_POST["unique_id"];

        $columns = [
            "customer_profile_unique_id"    => $customer_unique_id,
            "name"                          => $name,
            "shipping_address"              => $shipping_address,
            "city"                          => $city,
            "country"                       => $country,
            "state"                         => $state,
            "contact_name"                  => $contact_name,
            "contact_no"                    => $contact_no,
            "shipping_gst_no"               => $shipping_gst_no,
            "gst_status"                    => $gst_status,
            "ecc_no"                        => $ecc_no
        ];

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Duplicate Check: Based on `name` + customer (change if needed)
        $table_details = [
            $table_shipping_details,
            [ "COUNT(unique_id) AS count" ]
        ];

        $select_where = 'is_delete = 0 AND name = "'.$name.'" AND customer_profile_unique_id = "'.$customer_unique_id.'"';

        if ($unique_id) {
            $select_where .= ' AND unique_id != "'.$unique_id.'" ';
        }

        $action_obj = $pdo->select($table_details, $select_where);

        if (!$action_obj->status) {
            echo json_encode([
                "status" => false,
                "data"   => [],
                "error"  => $action_obj->error,
                "msg"    => "error",
                "sql"    => $action_obj->sql
            ]);
            break;
        }

        if ($action_obj->data[0]["count"] > 0) {
            echo json_encode([
                "status" => true,
                "data"   => [],
                "error"  => "",
                "msg"    => "already",
                "sql"    => $action_obj->sql
            ]);
            break;
        }

        // Insert or Update
        if ($unique_id) {
            $update_where = [ "unique_id" => $unique_id ];
            $action_obj = $pdo->update($table_shipping_details, $columns, $update_where);
            $msg = $action_obj->status ? "update" : "error";
        } else {
            $action_obj = $pdo->insert($table_shipping_details, $columns);
            $msg = $action_obj->status ? "add" : "error";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);

    break; 
    
    case 'shipping_details_datatable':
        // Function Name button prefix
        $btn_edit_delete = "shipping_details";

        // Fetch Data
        $customer_unique_id = $_POST['customer_unique_id'];

        // DataTable Inputs
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // SQL Column Selections
        $columns = [
            "@a:=@a+1 AS s_no",
            "name",
            "shipping_address",
            "country",      
            "state",    
            "city", 
            "contact_name",
            "contact_no",
            "shipping_gst_no",
            "gst_status",
            "ecc_no",
            "unique_id"
        ];

        $table_details = [
            "$table_shipping_details, (SELECT @a:=$start) AS a",
            $columns
        ];


        $where = [
            "customer_profile_unique_id" => $customer_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                
                $country_data = country($value['country']);
                $state_data   = state($value['state']);
                $city_data    = city($value['city']);

            
                $value['country'] = (is_array($country_data) && isset($country_data[0]['name'])) ? $country_data[0]['name'] : '';
                $value['state']   = (is_array($state_data)   && isset($state_data[0]['state_name']))     ? $state_data[0]['state_name']     : '';
                $value['city']    = (is_array($city_data)    && isset($city_data[0]['city_name']))       ? $city_data[0]['city_name']       : '';


                $btn_edit           = btn_edit($btn_edit_delete, $value['unique_id']);
                $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_edit . $btn_delete;

                $data[] = array_values($value);
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

    case 'documents_add_update':

        $customer_unique_id = $_POST["customer_unique_id"]; 
        $type               = $_POST["type"];
        $unique_id          = $_POST["unique_id"];

        $doc_up_filenames = [];     
        $allowed_exts = ['jpg', 'jpeg', 'png', 'pdf'];

        if (!empty($_FILES["test_file"]["name"])) {                              
            $target_dir = "../../uploads/customer_creation/";
            $folder_path = "customer_creation/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES["test_file"]["name"] as $key => $name) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                finfo_close($finfo);

                $is_image = strpos($mime_type, 'image/') === 0;
                $is_pdf = $mime_type === 'application/pdf';

                // Check extension and mime type
                if (!in_array($file_extension, $allowed_exts) || (!$is_image && !$is_pdf)) {
                    echo json_encode([
                        "status" => false,
                        "error"  => "Invalid file format. Only JPG, JPEG, PNG, PDF allowed.",
                        "msg"    => "invalid_file_format"
                    ]);
                    exit;
                }

                $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_extension;
                $target_file = $target_dir . $unique_filename;

                if (move_uploaded_file($_FILES["test_file"]["tmp_name"][$key], $target_file)) {
                    $doc_up_filenames[] = $unique_filename;
                }
            }

            $doc_up_filename = implode(',', $doc_up_filenames);
        } else {
            $doc_up_filename = $_POST['existing_file_attach'];
        }

        $columns = [
            "customer_profile_unique_id" => $customer_unique_id,
            "type"                       => $type,
            "file_attach"                => $doc_up_filename,
        ];

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Direct insert or update â€” no duplicate check
        if ($unique_id) {
            $update_where = [ "unique_id" => $unique_id ];
            $action_obj = $pdo->update($documents_upload, $columns, $update_where);
            $msg = $action_obj->status ? "update" : "error";
        } else {
            $action_obj = $pdo->insert($documents_upload, $columns);
            $msg = $action_obj->status ? "add" : "error";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);

    break;

    case 'add_doc':
        error_log("add_doc: Received POST: " . print_r($_POST, true) . "\n", 3, "debug.txt");
        $name = $_POST['name'] ?? '';
        error_log("add_doc: Name to insert: $name\n", 3, "debug.txt");

        $success = doc_option_insert($name);
        error_log("add_doc: Insert success: $success\n", 3, "debug.txt");

        echo json_encode(["status" => $success]);
    break;

    case 'documents_datatable':
        // Function Name button prefix
        $btn_edit_delete = "documents";

        // Fetch Data
        $customer_unique_id = $_POST['customer_unique_id']; 
        
        // DataTable Inputs
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // SQL Column Selections
        $columns = [
            "@a:=@a+1 AS s_no",
            "type",
            "file_attach",
            "unique_id"
        ];

        $table_details = [
            "$documents_upload, (SELECT @a:=$start) AS a",
            $columns
        ];

        $where = [
            "customer_profile_unique_id" => $customer_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        error_log("documents datatable query: " . $result->sql . "\n", 3, "debug.txt");

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                // Get document type name from doc_type_options(type)
                $type_data = doc_type_options($value['type']);
                $type_name = '';
                if (is_array($type_data) && isset($type_data[0]['name'])) {
                    $type_name = $type_data[0]['name'];
                }
                $value['type'] = $type_name;

                if (is_null($value['file_attach']) || $value['file_attach'] == '') {
                    $value['file_attach'] = "<td style='text-align:center'><span class='font-weight-bold'>No Image Uploaded</span></td>";
                } else {
                    $image_files = explode(',', $value['file_attach']);
                    $image_buttons = "";
                    foreach ($image_files as $image_file) {
                        $image_path = "../blue_planet_erp/uploads/customer_creation/" . trim($image_file);
                        $view_button = "<button type='button' onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'> <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                        </button>";
                        $image_buttons .= $view_button;
                    }
                    $value['file_attach'] = "<td style='text-align:center'>" . $image_buttons . "</td>";
                }

                $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_delete;

                $data[] = array_values($value);
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


    case 'documents_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($documents_upload,$columns,$update_where);

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
    

    case "shipping_details_edit":
        $unique_id = $_POST['unique_id'];
    
        $columns = [
            "name",
            "shipping_address",
            "city",
            "country",
            "state",
            "contact_name",
            "contact_no",
            "shipping_gst_no",
            "gst_status",
            "ecc_no",
            "unique_id"
        ];
    
        $table_details = [$table_shipping_details, $columns];
        $where = [
            "unique_id" => $unique_id,
            "is_active" => 1,
            "is_delete" => 0
        ];
    
        $result = $pdo->select($table_details, $where);
    
        if ($result->status && isset($result->data[0])) {
            $json_array = [
                "data" => $result->data[0],
                "status" => true,
                "sql" => $result->sql,
                "error" => ""
            ];
        } else {
            $json_array = [
                "status" => false,
                "error" => $result->error ?? "No data found",
                "msg" => "Error fetching data",
                "sql" => $result->sql ?? ""
            ];
        }
    
        echo json_encode($json_array);
    break;

    case 'shipping_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_shipping_details,$columns,$update_where);

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
    
    
}

?>
