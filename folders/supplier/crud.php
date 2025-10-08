<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database able Names
$table_supplier_profile         = "supplier_profile";
$table_account_details          = "supplier_account_details";
$table_branch_details           = "supplier_branch_details";
$table_contact_person           = "supplier_contact_person";
$table_billing_details          = "sp_billing_details";
$table_shipping_details         = "supplier_shipping_details";
$documents_upload               = "supplier_document_upload";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include '../../config/new_db.php';

// Include this folder only functions
include 'function.php';



$fileUpload         = new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );
$fileUploadPath     = $fileUploadConfig->get("upload_folder");

// Create Folder in root->uploads->(this_folder_name) Before using this file upload
$fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name . DIRECTORY_SEPARATOR);


// Variables Declaration
$action             = $_POST['action'];

$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];

$json_array         = "";
$sql                = "";
$prefix             = "supplier";
$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose


$user_id = $_SESSION['sess_user_id'];
$date = date('Y-m-d H:i:s', time());


switch ($action) {
    
case 'createupdate':

    $supplier_name          = $_POST["supplier_name"];
    $group_unique_id        = $_POST["group_unique_id"];
    $reference              = $_POST["reference"];
    $manufacturer_flag      = isset($_POST['manufacturer_flag']) ? 1 : 0;
    $agent_dealer_flag      = isset($_POST['agent_dealer_flag']) ? 1 : 0;
    $service_jobwork_flag   = isset($_POST['service_jobwork_flag']) ? 1 : 0;
    $ledger_balance         = $_POST["ledger_balance"];
    $credit_limit           = $_POST["credit_limit"];
    $credit_days            = $_POST["credit_days"];
    $contact_no             = $_POST["phone_no"];
    $currency_type          = $_POST["currency_unique_id"];
    $country                = $_POST["country_name"];
    $state                  = $_POST["state_name"];
    // $district               = $_POST["city_name"];
    $city                   = $_POST["city_name"];
    $pincode                = $_POST["pincode"];
    $address                = $_POST["address"];
    $corporate_address      = $_POST["corporate_address"];
    $email_id               = $_POST["email_id"];
    $fax_no                 = $_POST["fax_no"];
    $gst_no                 = $_POST["gst_no"];
    $pan_no                 = $_POST["pan_no"];
    $gst_reg_date           = $_POST["gst_reg_date"];
    $gst_status             = $_POST["gst_status"];
    $arn_no                 = $_POST["arn_no"];
    $msme_type              = $_POST["msme_type"];
    $msme_value             = $_POST["msme_value"];
    $msme_no                = $_POST["msme_no"];
    $website                = $_POST["website"];
    $unique_id              = isset($_POST["unique_id"]) ? $_POST["unique_id"] : "";
    $update_where           = "";
    
    error_log("POST: " . print_r($_POST, true) . "\n", 3, "post.txt");

    $columns = [
        "supplier_name"         => $supplier_name,
        "group_unique_id"       => $group_unique_id,
        "reference"             => $reference,
        "manufacturer_flag"     => $manufacturer_flag,
        "agent_dealer_flag"     => $agent_dealer_flag,
        "service_jobwork_flag"  => $service_jobwork_flag,
        // "ledger_balance"        => $ledger_balance,
        // "credit_limit"          => $credit_limit,
        // "credit_days"           => $credit_days,
        "contact_no"            => $contact_no,
        "currency_type"         => $currency_type,
        "country"               => $country,
        "state"                 => $state,
        // "district"              => $district,
        "city"                  => $city,
        "pincode"               => $pincode,
        "address"               => $address,
        "corporate_address"     => $corporate_address,
        "email_id"              => $email_id,
        "fax_no"                => $fax_no,
        "gst_no"                => $gst_no,
        "pan_no"                => $pan_no,
        "gst_reg_date"          => $gst_reg_date,
        "gst_status"            => $gst_status,
        "arn_no"                => $arn_no,
        "msme_type"             => $msme_type,
        "msme_value"            => $msme_value,
        // "msme_no"               => $msme_no,
        "website"               => $website,
        "created_user_id"       => $user_id,
        "created"               => $date,
        "unique_id"             => unique_id($prefix)
    ];
    
    error_log("columns: " . print_r($columns, true) . "\n", 3, "logged_col.txt");
    
    // Check for duplicate supplier (excluding current record if updating)
    $table_details = [
        $table_supplier_profile,
        ["COUNT(unique_id) AS count"]
    ];
    
    // $select_where = 'is_delete = 0 AND supplier_name = "' . $supplier_name . '" AND contact_no = "' . $contact_no . '"';
    // better duplicate check
    $select_where = 'is_delete = 0';
    
    if (!empty($supplier_name)) {
        $select_where .= ' AND supplier_name = "' . $supplier_name . '"';
    }
    
    if (!empty($contact_no)) {
        $select_where .= ' OR contact_no = "' . $contact_no . '"';
    }
    
    if ($unique_id) {
        // exclude current record when updating
        $select_where .= ' AND unique_id != "' . $unique_id . '"';
    }


    $action_obj = $pdo->select($table_details, $select_where);

    $status = false;
    $data   = [];
    $error  = "";
    $msg    = "";
    $sql    = "";

    if ($action_obj->status) {
        $data = $action_obj->data;
        // error_log("data: " . $data . "\n", 3, "data.txt");
        
        if ($unique_id) {
                // Update logic
                unset($columns['unique_id'], $columns['created_user_id'], $columns['created']);
                $columns['updated_user_id'] = $user_id;
                $columns['updated'] = $date;

                $update_where = ["unique_id" => $unique_id];
                $action_obj = $pdo->update($table_supplier_profile, $columns, $update_where);
                $msg = $action_obj->status ? "update" : "error";
        } else {
            if ($data[0]["count"] > 0) {
                $msg = "already";
            
            } else {
                // Insert logic
                $bill_no = batch_creation($table_supplier_profile, [], "VEND", $conns);
                $columns['vendor_code'] = $bill_no;
                $action_obj = $pdo->insert($table_supplier_profile, $columns);
                $msg = $action_obj->status ? "create" : "error";
            }

            
        }
        $status = $action_obj->status;
        $data = $action_obj->data;
        $error = $action_obj->error;
        $sql = $action_obj->sql;
    } else {
        $status = false;
        $data = [];
        $error = $action_obj->error;
        $sql = $action_obj->sql;
        $msg = "error";
    }

    $supplier_unique_id = $unique_id ?: $columns['unique_id'];

    $json_array = [
        "status"             => $status,
        "data"               => $data,
        "error"              => $error,
        "msg"                => $msg,
        "sql"                => $sql,
        "supplier_unique_id" => $supplier_unique_id
    ];

    echo json_encode($json_array);
break;

    case 'supplier_master_delete':
        $supplier_unique_id = $_POST['supplier_unique_id'];
        $columns = [
            "is_delete" => 1,
        ];
        $update_where = [
            "unique_id" => $supplier_unique_id
        ];
        $action_obj = $pdo->update($table_supplier_profile, $columns, $update_where);
        error_log("Delete action for supplier_unique_id: $supplier_unique_id, Result: " . print_r($action_obj, true) . "\n", 3, 'delete_log.txt');
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
            "supplier_name",
            "contact_no",
            "email_id",
            "address",
            "is_active",
            "unique_id"
        ];
        
        $table_details  = [
            $table_supplier_profile." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        
        $where  = "is_active = 1 AND is_delete = 0";

        if ($_POST['search']['value']) {
            $where .= " AND (supplier_name LIKE '".mysql_like($_POST['search']['value'])."' ";
            $where .= " OR address LIKE '".mysql_like($_POST['search']['value'])."' ) ";
        }
        
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
            $btn_update = btn_update($folder_name, $value['unique_id']);
            $btn_toggle = ($value['is_active'] == 1)
                ? btn_toggle_on($folder_name, $value['unique_id'], 0) // deactivate if currently active
                : btn_toggle_off($folder_name, $value['unique_id'], 1); // activate if currently inactive
        
            $value['unique_id'] = $btn_update . $btn_toggle;
        

            $keys = array_keys($value);
            $second_last_key = $keys[count($keys) - 2]; // is_active key
            unset($value[$second_last_key]);
        
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


    case 'sp_statutory':
    // Gather the raw statutory fields
    $statFields = [
        'ecc_no', 'commissionerate', 'division', 'stat_range',
        'cst_no', 'tin_no', 'service_tax_no', 'iec_code',
        'cin_no', 'tan_no'
    ];
    
    
    error_log("POST DATA: " . print_r($_POST, true) . "\n", 3, 'st_log.txt');
    error_log("ALL EMPTY? " . ($allEmpty ? 'yes' : 'no') . "\n", 3, 'st_log.txt');

    $customerId = $_POST['supplier_unique_id'] ?? '';
    $frontendUniqueId = $_POST['unique_id'] ?? '';

    // Check if all statutory fields are empty
    $allEmpty = true;
    foreach ($statFields as $f) {
        if (!empty($_POST[$f] ?? '')) {
            $allEmpty = false;
            break;
        }
    }


    // Skip processing if all fields are empty and no existing record
    if ($allEmpty && empty($frontendUniqueId)) {
        echo json_encode([
            'status' => 1,
            'msg'    => 'no statutory data to save, moving on',
            'data'   => null,
            'error'  => '',
            'sql'    => ''
        ]);
        break;
    }

    // Prepare payload
    $columns = [
        'unique_id'                  => '', // placeholder, will set below
        'supplier_profile_unique_id'=> $customerId,
        'ecc_no'                    => $_POST['ecc_no']          ?? '',
        'commissionerate'           => $_POST['commissionerate'] ?? '',
        'division'                  => $_POST['division']        ?? '',
        'stat_range'                => $_POST['range']      ?? '', // corrected from 'range'
        'cst_no'                    => $_POST['cst_no']          ?? '',
        'tin_no'                    => $_POST['tin_no']          ?? '',
        'service_tax_no'            => $_POST['service_tax_no']  ?? '',
        'iec_code'                  => $_POST['iec_code']        ?? '',
        'cin_no'                    => $_POST['cin_no']          ?? '',
        'tan_no'                    => $_POST['tan_no']          ?? '',
        // audit trail
        'acc_year'                  => $_SESSION['acc_year']     ?? '',
        'sess_user_type'            => $_SESSION['sess_user_type']  ?? '',
        'sess_user_id'              => $_SESSION['sess_user_id']    ?? '',
        'sess_company_id'           => $_SESSION['sess_company_id'] ?? '',
        'sess_branch_id'            => $_SESSION['sess_branch_id']  ?? '',
    ];

    error_log("STATUTORY DETAILS (PRE-CHECK): " . print_r($columns, true) . "\n", 3, 'st_log.txt');

    $table = 'sp_statuatory_details';

    // Check if a record already exists for this supplier
    $select_cols = ['unique_id'];
    $select_where = ['supplier_profile_unique_id' => $customerId];
    $check = $pdo->select([$table, $select_cols], $select_where);

    if ($check->status && !empty($check->data)) {
        // Update the existing record
        $existing_unique_id = $check->data[0]['unique_id'];
        unset($columns['unique_id']); // unique_id should not be updated
        $update_where = ['unique_id' => $existing_unique_id];
        $action_obj = $pdo->update($table, $columns, $update_where);
        $msg = 'update';
    } else {
        // Insert new record
        $columns['unique_id'] = !empty($frontendUniqueId) ? $frontendUniqueId : unique_id($prefix);
        $action_obj = $pdo->insert($table, $columns);
        $msg = 'add';
    }

    // Final response formatting
    $response = [
        'status' => $action_obj->status,
        'data'   => $action_obj->data,
        'error'  => $action_obj->error ?? '',
        'msg'    => $action_obj->status ? $msg : 'error',
        'sql'    => $action_obj->sql ?? ''
    ];

    error_log("STATUTORY DETAILS RESPONSE: " . print_r($response, true) . "\n", 3, 'st_log.txt');

    echo json_encode($response);
    break;

    
    case 'account_details_add_update':

        $supplier_unique_id         = $_POST["supplier_unique_id"];       
        $bank_name                  = $_POST["bank_name"];
        $bank_address               = $_POST["bank_address"];
        $ifsc_code                  = $_POST["ifsc_code"];
        $dealer_name                = $_POST["dealer_name"];
        $account_no                 = $_POST["account_no"];
        $bank_contact_no            = $_POST["bank_contact_no"];
        $swift_code                 = $_POST["swift_code"];
        $unique_id                  = $_POST["unique_id"];

        $update_where               = "";

        $columns            = [
            "supplier_profile_unique_id"   => $supplier_unique_id,
            "bank_name"                    => $bank_name,
            "address"                      => $bank_address,
            "ifsc_code"                    => $ifsc_code,
            "dealer_name"                  => $dealer_name,
            "contact_no"                   => $bank_contact_no,
            "account_no"                   => $account_no,
            "swift_code"                   => $swift_code,
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
        $supplier_unique_id = $_POST['supplier_unique_id']; 

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
            "address",
            "ifsc_code",
            "dealer_name",
            "account_no",
            "contact_no",
            "unique_id"
        ];
        $table_details  = [
            $table_account_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "supplier_profile_unique_id"    => $supplier_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
   
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
            "address",
            "ifsc_code",
            "dealer_name",
            "account_no",
            "contact_no",
            "swift_code",
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
    

    case 'branch_details_add_update':

        $supplier_unique_id         = $_POST["supplier_unique_id"];       
        $branch_name                = $_POST["branch_name"];
        $branch_address             = $_POST["branch_address"];
        $branch_state_name          = $_POST["branch_state_name"];
        $branch_city_name           = $_POST["branch_city_name"];
        $branch_gst_no              = $_POST["branch_gst_no"];
        $branch_contact_no          = $_POST["branch_contact_no"];
        $branch_pincode             = $_POST["branch_pincode"];
        $unique_id                  = $_POST["unique_id"];

        $update_where               = "";

        $columns            = [
            "supplier_profile_unique_id"   => $supplier_unique_id,
            "branch_name"                  => $branch_name,
            "branch_address"               => $branch_address,
            "branch_state_name"            => $branch_state_name,
            "branch_city_name"             => $branch_city_name,
            "branch_contact_no"            => $branch_contact_no,
            "branch_gst_no"                => $branch_gst_no,
            "branch_pincode"               => $branch_pincode,
            "unique_id"                    => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table_branch_details,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'branch_name ="'.$branch_name.'" AND is_delete = 0  AND branch_gst_no = "'.$branch_gst_no.'" AND branch_contact_no = "'.$branch_contact_no.'" ';

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

                $action_obj     = $pdo->update($table_branch_details,$columns,$update_where);

            // Update Ends
            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table_branch_details,$columns);
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
    
    
    case 'branch_details_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "branch_details";
        
        // Fetch Data
        $supplier_unique_id = $_POST['supplier_unique_id']; 

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
            "branch_name",
            "branch_address",
            "branch_gst_no",
            "(SELECT state_name FROM states WHERE states.unique_id =".$table_branch_details.". branch_state_name) AS branch_state_name",
            "(SELECT city_name FROM cities WHERE cities.unique_id =".$table_branch_details.". branch_city_name) AS branch_city_name",
            "branch_contact_no",
            "branch_pincode",
            "unique_id"
        ];
        $table_details  = [
            $table_branch_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "supplier_profile_unique_id"    => $supplier_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
      
        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
               $btn_edit                = btn_edit($btn_edit_delete,$value['unique_id']);
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
    
    
    case "branch_details_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "branch_name",
            "branch_address",
            "branch_state_name",
            "branch_city_name",
            "branch_gst_no",
            "branch_contact_no", 
            "branch_pincode", 
            "unique_id"
        ];
        $table_details  = [
            $table_branch_details,
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

    case 'branch_details_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_branch_details,$columns,$update_where);

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
    $is_active = $_POST['is_active']; // Expecting 1 or 0

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table_supplier_profile, $columns, $update_where);

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $is_active ? "Activated Successfully" : "Deactivated Successfully",
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;


     // Contact person Section Starts  

    case 'contact_person_add_update':

            $contact_person_name        = $_POST["contact_person_name"];
            $supplier_unique_id         = $_POST["supplier_unique_id"];
            $contact_person_designation = $_POST["contact_person_designation"];
            $contact_person_email       = $_POST["contact_person_email"];
            $contact_person_contact_no  = $_POST["contact_person_contact_no"];
            $landline                   = $_POST["landline"];
            $department                 = $_POST["department"];
            $unique_id                  = $_POST["unique_id"];
            $update_where               = "";
    
            $columns            = [
                "supplier_profile_unique_id"=> $supplier_unique_id,
                "contact_person_name"       => $contact_person_name,
                "contact_person_designation"=> $contact_person_designation,
                "contact_person_email"      => $contact_person_email,
                "contact_person_contact_no" => $contact_person_contact_no,
                "landline"                  => $landline,
                "department"                => $department,
                "created_user_id"           => $user_id,
                "created"                   => $date,
                "unique_id"                 => unique_id($prefix)
            ];
    
            // check already Exist Or not
            $table_details      = [
                $table_contact_person,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'contact_person_name ="'.$contact_person_name.'" AND is_delete = 0  AND contact_person_designation = "'.$contact_person_designation.'" AND contact_person_contact_no = "'.$contact_person_contact_no.'" ';
    
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
                    unset($columns['created_user_id']);
                    unset($columns['created']);
                    $columns = [
                        "updated_user_id"            => $user_id,
                        "updated"                    => $date,
                        "contact_person_name"        => $contact_person_name,
                        "contact_person_designation" => $contact_person_designation,
                        "contact_person_email"       => $contact_person_email,
                        "contact_person_contact_no"  => $contact_person_contact_no,
                        "landline"                   => $landline,
                        "department"                 => $department,
                         ];
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_contact_person,$columns,$update_where);
                    // print_r($action_obj);
    
                // Update Ends
                } else {
    
                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_contact_person,$columns);
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

    case 'contact_person_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "contact_person";
        
        // Fetch Data
        $supplier_unique_id = $_POST['supplier_unique_id']; 

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
            "supplier_profile_unique_id"    => $supplier_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        
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

    case "contact_person_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "contact_person_name",
            "contact_person_designation",
            "contact_person_email",
            "contact_person_contact_no",
            "landline",
            "department",
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
        // print_r($result);

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
        
        
    // case 'billing_details_add_update':

    //     error_log("BILLING DETAILS ADD/UPDATE STARTED\n", 3, 'billing_details_log.txt');
    //     error_log("POST DATA: " . print_r($_POST, true) . "\n", 3, 'billing_details_log.txt');
    //     $supplier_unique_id = $_POST["supplier_unique_id"]; 
    //     // echo 'hi'.$customer_unique_id;
    //     $name               = $_POST["billing_name"];
    //     $billing_address   = $_POST["billing_address"];
    //     $city               = $_POST["billing_city"];
    //     $country            = $_POST["billing_country"];
    //     $state              = $_POST["billing_state"];
    //     $contact_name       = $_POST["bill_contact_name"];
    //     $contact_no         = $_POST["bill_contact_no"];
    //     $billing_gst_no    = $_POST["bill_gst_no"];
    //     $gst_status         = $_POST["gst_status_bill"];
    //     $ecc_no             = $_POST["bill_ecc_no"];
    //     $unique_id          = $_POST["unique_id"];

    //     $columns = [
            
    //         "sp_profile_unique_id"    => $supplier_unique_id,
    //         "name"                          => $name,
    //         "billing_address"              => $billing_address,
    //         "city"                          => $city,
    //         "country"                       => $country,
    //         "state"                         => $state,
    //         "contact_name"                  => $contact_name,
    //         "contact_no"                    => $contact_no,
    //         "billing_gst_no"               => $billing_gst_no,
    //         "gst_status"                    => $gst_status,
    //         "ecc_no"                        => $ecc_no
    //     ];
    //     error_log("Columns: " . print_r($columns, true) . "\n", 3, 'billing_details_log.txt');

    //     if (!$unique_id) {
    //         $columns["unique_id"] = unique_id($prefix);
    //     }

    //     // Duplicate Check: Based on `name` + customer (change if needed)
    //     $table_details = [
    //         $table_billing_details,
    //         [ "COUNT(unique_id) AS count" ]
    //     ];

    //     error_log("Table Details: " . print_r($table_details, true) . "\n", 3, 'billing_details_log.txt');

    //     $select_where = 'is_delete = 0 AND name = "'.$name.'" AND sp_profile_unique_id = "'.$supplier_unique_id.'"';



    //     if ($unique_id) {
    //         $select_where .= ' AND unique_id != "'.$unique_id.'" ';
    //     }

    //     error_log("Select Where: " . $select_where . "\n", 3, 'billing_details_log.txt');

    //     $action_obj = $pdo->select($table_details, $select_where);
    //     error_log("Action Object: " . print_r($action_obj, true) . "\n", 3, 'billing_details_log.txt');
    //     // print_r($action_obj);

    //     if (!$action_obj->status) {
    //         echo json_encode([
    //             "status" => false,
    //             "data"   => [],
    //             "error"  => $action_obj->error,
    //             "msg"    => "error",
    //             "sql"    => $action_obj->sql
    //         ]);
    //         break;
    //     }
    //     error_log("Action Data: " . print_r($action_obj->data, true) . "\n", 3, 'billing_details_log.txt');
    //     if ($action_obj->data[0]["count"] > 0) {
    //         echo json_encode([
    //             "status" => true,
    //             "data"   => [],
    //             "error"  => "",
    //             "msg"    => "already",
    //             "sql"    => $action_obj->sql
    //         ]);
    //         break;
    //     }
    //     error_log("No duplicates found, proceeding with insert/update.\n", 3, 'billing_details_log.txt');
    //     // Insert or Update
    //     if ($unique_id) {
    //         $update_where = [ "unique_id" => $unique_id ];
    //         $action_obj = $pdo->update($table_billing_details, $columns, $update_where);
    //         $msg = $action_obj->status ? "update" : "error";
    //     } else {
    //         $action_obj = $pdo->insert($table_billing_details, $columns);
    //         $msg = $action_obj->status ? "add" : "error";
    //     }
    //     error_log("Action Object after insert/update: " . print_r($action_obj, true) . "\n", 3, 'billing_details_log.txt');
    //     if (!$action_obj->status) {
    //         echo json_encode([
    //             "status" => $action_obj->status,
    //             "data"   => $action_obj->data,
    //             "error"  => $action_obj->error,
    //             "msg"    => $msg,
    //             "sql"    => $action_obj->sql
    //         ]);
    //         error_log("BILLING DETAILS ADD/UPDATE ENDED WITH ERROR\n", 3, 'billing_details_log.txt');
    //         break;
    //     }
    //     echo json_encode([
    //         "status" => $action_obj->status,
    //         "data"   => $action_obj->data,
    //         "error"  => $action_obj->error,
    //         "msg"    => $msg,
    //         "sql"    => $action_obj->sql
    //     ]);
    //     error_log("BILLING DETAILS ADD/UPDATE ENDED\n", 3, 'billing_details_log.txt');
    // break;

    case 'billing_details_add_update':

        error_log("billing post data: " . print_r($_POST, true) . "\n", 3, "debug.txt");

        $supplier_unique_id = $_POST["supplier_unique_id"];       
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
            "sp_profile_unique_id"    => $supplier_unique_id,
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

        error_log("billing columns: " . print_r($columns, true) . "\n", 3, "debug.txt");

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Duplicate Check: Based on `name` + customer (change if needed)
        $table_details = [
            $table_billing_details,
            [ "COUNT(unique_id) AS count" ]
        ];

        $select_where = 'is_delete = 0 AND name = "'.$name.'" AND sp_profile_unique_id = "'.$supplier_unique_id.'"';

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
        } else {  if ($action_obj->data[0]["count"] > 0) {
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

        error_log("billing action object: " . print_r($action_obj, true) . "\n", 3, "debug.txt");

        error_log("billing_details_add_update response: " . print_r([
            "status" => $action_obj->status,
            "data"   => $columns,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ], true) . "\n", 3, "debug.txt");

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $columns,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);

    break;


    
    
    case 'billing_details_datatable':
        
        error_log("billing_details_datatable POST data: " . print_r($_POST, true) . "\n", 3, "debug.txt");
        
        // Function Name button prefix
        $btn_edit_delete = "billing_details";

        // Fetch Data
        $supplier_unique_id = $_POST['supplier_unique_id']; 
        
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
            "sp_profile_unique_id" => $supplier_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //   print_r($result);
        $total_records = total_records();

        error_log("billing_details_datatable result: " . print_r($result, true) . "\n", 3, "debug.txt");
    

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


        $customer_unique_id = $_POST["supplier_unique_id"]; 
        // echo 'hi'.$customer_unique_id;
        $name               = $_POST["name"];
        $shipping_address   = $_POST["shipping_address"];
        $city               = $_POST["city"];
        $country            = $_POST["country"];
        $state              = $_POST["state"];
        $contact_name       = $_POST["contact_name"];
        $contact_no         = $_POST["contact_no"];
        $shipping_gst_no    = $_POST["shipping_gst_no"];
        $gst_status         = $_POST["gst_status"];
        $ecc_no             = $_POST["ecc_no"];
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
        // print_r($action_obj);

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
        $supplier_unique_id = $_POST['supplier_unique_id']; 
        
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
            "customer_profile_unique_id" => $supplier_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //   print_r($result);
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
    
    
    
    //Document
    
    case 'documents_add_update':

        $supplier_unique_id = $_POST["supplier_unique_id"]; 
        $type               = $_POST["type"];
        $unique_id          = $_POST["unique_id"];

        $doc_up_filenames = [];     

        if (!empty($_FILES["test_file"]["name"])) {                              
            $target_dir = "../../uploads/supplier_creation/";
            $folder_path = "supplier_creation/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES["test_file"]["name"] as $key => $name) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_extension;
                $target_file = $target_dir . $unique_filename;

                // Accept all image types and pdf
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                finfo_close($finfo);

                // Accept if mime starts with image/ or is application/pdf
                if (strpos($mime_type, 'image/') === 0 || $mime_type === 'application/pdf') {
                if (move_uploaded_file($_FILES["test_file"]["tmp_name"][$key], $target_file)) {
                    $doc_up_filenames[] = $unique_filename;
                } else {
                    echo "Failed to move uploaded file: $name\n";
                }
                } else {
                echo "File type not allowed: $name\n";
                }
            }

            $doc_up_filename = implode(',', $doc_up_filenames);
        } else {
            $doc_up_filename = $_POST['existing_file_attach'];
        }

        $columns = [
            "sp_profile_unique_id" => $supplier_unique_id,
            "type"                  => $type,
            "file_attach"           => $doc_up_filename,
        ];

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Direct insert or update — no duplicate check
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


    
    
case 'documents_datatable':
    // Function Name button prefix
    $btn_edit_delete = "documents";

    // Fetch Data
    $supplier_unique_id = $_POST['supplier_unique_id']; 
    
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
        "sp_profile_unique_id" => $supplier_unique_id,
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
                    $image_path = "../blue_planet_beta/uploads/supplier_creation/" . trim($image_file);
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

    case 'add_doc':
        error_log("add_doc: Received POST: " . print_r($_POST, true) . "\n", 3, "debug.txt");
        $name = $_POST['name'] ?? '';
        error_log("add_doc: Name to insert: $name\n", 3, "debug.txt");

        $success = doc_option_insert($name);
        error_log("add_doc: Insert success: $success\n", 3, "debug.txt");

        echo json_encode(["status" => $success]);
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
        // print_r($action_obj);

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
    
    

    case 'cities':

        $state_id           = $_POST['state_id'];

        $city_name_options  = city("",$state_id);

        $city_name_options  = select_option($city_name_options,"Select the City");

        echo $city_name_options;
        
        break;

    case 'branch_cities':

        $state_id           = $_POST['state_id'];

        $city_name_options  = city("",$state_id);

        $city_name_options  = select_option($city_name_options,"Select the City");

        echo $city_name_options;
        
        break;

    case 'state_code':

        $state_id           = $_POST['state_id'];

        $state_code_details = state($state_id);

        $state_code          = $state_code_details[0]['state_code'];

        echo $state_code;
        
        break;
    case 'states':

        $country_id          = $_POST['country_id'];

        $state_name_options  = state("",$country_id);

        $state_name_options  = select_option($state_name_options,"Select the State");

        echo $state_name_options;
        
        break;
    case 'get_state_code':

        $code               = $_POST['code'];

        $state_name_options  = state($code);

        $state_code          = $state_name_options[0]['state_code'];

        echo $state_code;
        
        break;

    default:
        
    break;
    
}
    function batch_creation($table_name, $where, $prefix, $conn) {
    // Generate the next sequential number
    $order_no = generate_order_number($table_name, $conn, $prefix);

    // Create the new batch ID using prefix and padded number
    $batch_id = $prefix . sprintf("%05d", $order_no);

    return $batch_id;
}

function generate_order_number($table_name, $conn, $prefix) {
    // Prepare and execute query to get max vendor_code with the prefix
    $stmt = $conn->prepare("SELECT MAX(vendor_code) AS max_id FROM $table_name WHERE vendor_code LIKE :prefix AND is_delete = 0");
    $stmt->execute([':prefix' => $prefix . '%']);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Extract numeric part after the prefix and increment
    if ($result && $result['max_id']) {
        $numeric_part = substr($result['max_id'], strlen($prefix));
        $new_number = intval($numeric_part) + 1;
    } else {
        $new_number = 1;
    }

    return $new_number;
}

?>