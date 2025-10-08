<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "project_creation";
$documents_upload = "project_uploads";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

$fileUpload         = new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );

$fileUploadPath = $fileUploadConfig->get("upload_folder");

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

$branch_name        = "";
$description        = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

 
        $unique_id            = $_POST["unique_id"];
        $company_name         = $_POST["company_name"];
        $company_code         = $_POST["company_code"];
        $client_id = $_POST["client_name"]; // this is actually supplier_id
        $capacity             = $_POST["capacity"];
        $project_name         = $_POST["project_name"];
        $project_code         = $_POST["project_code"];
        $project_date         = $_POST["project_date"];
        $duration             = $_POST["duration"];
        $cost_center          = $_POST["cost_center"];
        $application_type     = $_POST["application_type"];
        $country              = $_POST["country"];
        $state                = $_POST["state"];
        $city                 = $_POST["city"];
        $address              = $_POST["address"];
        $pin_code             = $_POST["pin_code"];
        $pan_number           = $_POST["pan_number"];
        $gst_number           = $_POST["gst_number"];
        $gst_reg_date         = $_POST["gst_reg_date"];
        $contact_person       = $_POST["contact_person"];
        $contact_number       = $_POST["contact_number"];
        $contact_email_id     = $_POST["contact_email_id"];
        $website              = $_POST["website"];
        $company_logo         = $_FILES["company_logo"];
        $description          = $_POST["description"];
        $is_active            = $_POST["is_active"];
        $created_type= $_POST["created_type"];
        $existing_company_logo= $_POST["existing_company_logo"];
         $sales_order_id = isset($_POST['sales_order_id']) ? $_POST['sales_order_id'] : NULL;
        $update_where       = "";
        
        
$upload_dir = "../../uploads/project_creation/";
$allowed_formats = ["jpg", "jpeg", "png","pdf"];
$uploaded_logo_name = "";

if (!empty($_FILES["company_logo"]["name"])) {
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = $_FILES["company_logo"]["name"];
    $tmp_name = $_FILES["company_logo"]["tmp_name"];
    $file_size = $_FILES["company_logo"]["size"];
    $file_error = $_FILES["company_logo"]["error"];
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $allowed_formats)) {
        $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_ext;
        $target_path = $upload_dir . $unique_filename;

        if (move_uploaded_file($tmp_name, $target_path)) {
            $company_logo = $unique_filename;
        } else {
            echo "Failed to upload logo file.";
        }
    } else {
        echo "Invalid file format: only jpg, jpeg, png allowed.";
    }
}else {
    $company_logo = $_POST['existing_company_logo'];
  
}


$columns = [
    "unique_id"         => unique_id($prefix),  
    "company_name"      => $company_name,
    "company_code"      => $company_code,
    "client_name" => $client_id,
    "capacity"          => $capacity,
    "project_name"      => $project_name,
    "project_code"      => $project_code,
    "project_date"      => $project_date,
    "duration"          => $duration,
    "cost_center"       => $cost_center,
    "application_type"  => $application_type,
    "country"           => $country,
    "state"             => $state,
    "city"              => $city,
    "address"           => $address,
    "pin_code"          => $pin_code,
    "pan_number"        => $pan_number,
    "gst_number"        => $gst_number,
    "gst_date"          => $gst_reg_date,
    "contact_person"    => $contact_person,
    "contact_number"    => $contact_number,
    "contact_email_id"  => $contact_email_id,
    "website"           => $website,
    "logo"              => $company_logo,  
    "description"       => $description,
    "sales_order_id"    => $sales_order_id,
    "is_active"         => $is_active,
    "created_type" => $created_type,

];

        
  // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        $select_where       = 'project_code = "'.$project_code.'"  AND is_delete = 0  ';

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
        } else if ($data[0]["count"] == 0) {
        // Update Begins
        if($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table,$columns,$update_where);

        // Update Ends
        } else {
        // Insert Begins
           
            $action_obj     = $pdo->insert($table,$columns);
// print_r($action_obj);
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
        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;
        
        
        
// Document upload
case 'documents_add_update':

    $upload_unique_id = $_POST["upload_unique_id"] ?? null; // <-- unique_id from project_uploads
    $type             = $_POST["type"] ?? null;
    $unique_id        = $_POST["unique_id"] ?? null;        // row unique_id for the documents table (if updating)

    // --- helpers ---
    $bytes_to_human = function($bytes) {
        $units = ['B','KB','MB','GB','TB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    };

    // Log incoming POST data
    error_log("POST: " . print_r($_POST, true) . "\n", 3, "doc_logs.log");

    // Basic validation
    if (!$upload_unique_id || !$type) {
        echo json_encode([
            "status" => false,
            "error"  => "Missing required fields: 'upload_unique_id' or 'type'.",
            "msg"    => "missing_fields"
        ]);
        exit;
    }
    if (empty($_FILES["test_file"]["name"][0])) {
        echo json_encode([
            "status" => false,
            "error"  => "No file selected.",
            "msg"    => "no_file_selected"
        ]);
        exit;
    }

    // --- Fetch existing combined size from project_creation by project_unique_id ---
    $current_size = 0;
    $pc_res = $pdo->select(["project_creation", ["files_combined_size"]], ["unique_id" => $upload_unique_id]);
    error_log("files_combined_size: " . print_r($pc_res, true) . "\n", 3, "doc_logs.log");
    if ($pc_res->status && isset($pc_res->data[0]['files_combined_size'])) {
        $current_size = (int)$pc_res->data[0]['files_combined_size'];
    }

    // Allowed types
    $allowed_exts = [
        'jpg','jpeg','png','gif','bmp','webp','svg', // Images
        'pdf',                                      // PDF
        'doc','docx',                               // Word
        'txt',                                      // Text
        'xls','xlsx',                               // Excel
        'csv'                                       // CSV
    ];
    $allowed_mime_types = [
        'image/jpeg','image/png','image/gif','image/bmp','image/webp','image/svg+xml',
        'application/pdf',
        'application/msword',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'text/plain',
        'application/vnd.ms-excel',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'text/csv'
    ];

    // --- limit (set what you want; 10MB for testing, 500MB for prod) ---
    // $MAX_SIZE = 10 * 1024 * 1024;           // TEST limit
    $MAX_SIZE = 500 * 1024 * 1024;        // PROD limit
    $MAX_SIZE_HR = $bytes_to_human($MAX_SIZE);

    // Size of current batch
    $new_files_size = 0;
    foreach ($_FILES["test_file"]["size"] as $sz) {
        $new_files_size += (int)$sz;
    }

    // Check limit BEFORE moving (existing + new)
    $prospective_total = $current_size + $new_files_size;
    if ($prospective_total > $MAX_SIZE) {
        echo json_encode([
            "status"        => false,
            "msg"           => "file_size_exceeded",
            "error"         => "Total file size exceeds the allowed limit.",
            "current_size"  => $bytes_to_human($current_size),
            "attempt_size"  => $bytes_to_human($new_files_size),
            "would_total"   => $bytes_to_human($prospective_total),
            "max_size"      => $MAX_SIZE_HR
        ]);
        exit;
    }

    // --- Move files ---
    $target_dir  = "../../uploads/project_creation/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $doc_up_filenames = [];
    $all_moved = true;

    foreach ($_FILES["test_file"]["name"] as $key => $name) {
        $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        $tmp_path       = $_FILES["test_file"]["tmp_name"][$key];

        // MIME via finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $tmp_path);
        finfo_close($finfo);

        // Validate type
        if (!in_array($file_extension, $allowed_exts) || !in_array($mime_type, $allowed_mime_types)) {
            echo json_encode([
                "status" => false,
                "error"  => "Invalid file format. Only images, PDF, Word, Excel, CSV, and text files are allowed.",
                "msg"    => "invalid_file_format"
            ]);
            exit;
        }

        // Unique filename + move
        $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_extension;
        $target_file     = $target_dir . $unique_filename;

        if (move_uploaded_file($tmp_path, $target_file)) {
            $doc_up_filenames[] = $unique_filename;
        } else {
            $all_moved = false;
            break;
        }
    }

    // If any move failed → rollback moved files and abort
    if (!$all_moved) {
        foreach ($doc_up_filenames as $f) {
            @unlink($target_dir . $f);
        }
        echo json_encode([
            "status" => false,
            "msg"    => "error",
            "error"  => "File upload failed. Please try again."
        ]);
        exit;
    }

    // All moved successfully → persist the NEW TOTAL (previous + this batch) in project_creation
    $new_total = $current_size + $new_files_size;
    $pdo->update("project_creation", ["files_combined_size" => $new_total], ["unique_id" => $upload_unique_id]);

    // Prepare row for your documents table
    // NOTE: keep your table variable as-is; assuming $documents_upload = 'project_uploads' or your docs table.
    $doc_up_filename = implode(',', $doc_up_filenames);
    $columns = [
        "project_unique_id" => $upload_unique_id, // <-- store the true project id, not the upload header id
        "type"              => $type,
        "file_attach"       => $doc_up_filename,
        // Optional audit fields:
        // "batch_size_bytes"    => $new_files_size,
        // "combined_size_bytes" => $new_total,
    ];
    if (!$unique_id) {
        $columns["unique_id"] = unique_id($prefix);
    }

    // Insert/update document record
    if ($unique_id) {
        $action_obj = $pdo->update($documents_upload, $columns, ["unique_id" => $unique_id]);
        $msg = $action_obj->status ? "update" : "error";
    } else {
        $action_obj = $pdo->insert($documents_upload, $columns);
        $msg = $action_obj->status ? "add" : "error";
    }

    echo json_encode([
        "status" => $action_obj->status,
        "data"   => [
            "insert_id"      => $action_obj->data,
            "upload"         => $upload_unique_id,
            "project_id"     => $project_unique_id,
            "batch_size"     => $bytes_to_human($new_files_size),
            "new_total"      => $bytes_to_human($new_total),
            "max_size"       => $MAX_SIZE_HR
        ],
        "error"  => $action_obj->error,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
    ]);

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
    
    
    case 'documents_datatable':
        // Function Name button prefix
        $btn_edit_delete = "documents";

        // Fetch Data
        $upload_unique_id = $_POST['upload_unique_id']; 
        
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
            "project_unique_id" => $upload_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        error_log("documents datatable query: " . print_r($result, true) . "\n", 3, "debug.txt");

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
                        $image_path = "../blue_planet_beta/uploads/project_creation/" . trim($image_file);
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
        "company_name",
        "project_name",
        "project_code",
        "client_name",
        "application_type",
        "capacity",
        "state",
        "city",
        "contact_person",
        "contact_number",
        "logo", 
        "is_active",
        "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";
        
        
         if(($_POST['from_date']!='')  && ($_POST['to_date']!=''))
    {
        $where  .= " and project_date>='".$_POST['from_date']."' and project_date<='".$_POST['to_date']."'";
    }
    if (!empty($_POST['company_name'])) {
        $where .= " and company_name = '" . $_POST['company_name'] . "'";
    }
    if (!empty($_POST['project_name'])) {
        $where .= " and unique_id = '" . $_POST['project_name'] . "'";
    }
    if (!empty($_POST['application_type'])) {
        $where .= " and application_type = '" . $_POST['application_type'] . "'";
    }
    
    

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

    if (!empty($search)) {
         $where .= " AND ( company_name LIKE '%".company_name_like($_POST['search']['value'])."%' ";
         $where .= " or project_name LIKE '%" . $search . "%'";
         $where .= " or client_name LIKE '%" . $search . "%')";
   
    }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {


                     $value['company_name']  = company_name($value['company_name'])[0]['company_name'];
                     $value['country']       = country($value['country'])[0]['name'];
                     $value['state']         = state($value['state'])[0]['state_name'];
                     $value['city']          = city($value['city'])[0]['city_name'];
                     if (!empty($value['client_name'])) {
                        $customers = customers($value['client_name']);
                        $value['client_name'] = !empty($customers[0]['customer_name']) ? $customers[0]['customer_name'] : "-";
                    } else {
                        $value['client_name'] = "-";
                    }

                     error_log(print_r($value['client_name'], true), 3, 'client.log');
                    switch ($value['application_type']) {
                    case 1:
                        $value['application_type'] = "CBG";
                        break;
                
                    case 2:
                        $value['application_type'] = "COMPOST";
                        break;
                    case 3:
                        $value['application_type'] = "CBG/COMPOST";
                        break;
                    case 4:
                        $value['application_type'] = "COOKING";
                        break;
                    case 5:
                        $value['application_type'] = "GENERATION";
                        break;
                
                    default:
                        $value['application_type'] = "UNKNOWN";
                        break;
                    }

if (is_null($value['logo']) || $value['logo'] == '') {
    $value['logo'] = "<td style='text-align:center'><span class='font-weight-bold'>No File Uploaded</span></td>";
} else {
    $image_files = explode(',', $value['logo']);
    $output = "";

    foreach ($image_files as $file) {
        $file = trim($file);
        $file_path = "../blue_planet_beta/uploads/project_creation/" . $file;
        $file_ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));

        if (in_array($file_ext, ['jpg', 'jpeg', 'png'])) {
            // Show image thumbnail
            $output .= "<a href='$file_path' target='_blank'>
                            <img src='$file_path' style='height: 50px; width: 50px; object-fit: cover; border: 1px solid #ccc; border-radius: 5px; margin-right: 5px;' />
                        </a>";
        } elseif ($file_ext === 'pdf') {
            // Show PDF text with icon
            $output .= "<a href='$file_path' target='_blank' style='text-decoration:none; margin-right: 10px;'>
                            <i class='fas fa-file-pdf' style='font-size: 20px; color: red;'></i> <span style='vertical-align: middle;'>PDF</span>
                        </a>";
        } else {
            // Unknown format
            $output .= "<span style='color:gray;'>Unsupported File</span>";
        }
    }

    $value['logo'] = "<td style='text-align:center'>" . $output . "</td>";
}



               $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == "1")
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
                    $btn_upload = btn_docs($folder_name, $value['unique_id']);
                
                $value['unique_id'] = $btn_update . $btn_toggle .$btn_upload ;
                
                // Styled is_active display
                $value['is_active'] = ($value['is_active'] == "1")
                    ? "<span style='color:green'>Active</span>"
                    : "<span style='color:red'>Inactive</span>";
                
                $data[] = array_values($value); // no unset, column count remains correct

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
    
    
    case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active']; // 1 or 0

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    if ($action_obj->status) {
        $status = true;
        $msg = $is_active ? "Activated Successfully" : "Deactivated Successfully";
    } else {
        $status = false;
        $msg = "Toggle failed!";
    }

    echo json_encode([
        "status" => $status,
        "msg" => $msg,
        "sql" => $action_obj->sql,
        "error" => $action_obj->error
    ]);
    break;


case 'get_company_code':

        $company_id          = $_POST['company_id'];

        $company_code_options  = company_code($company_id);
        error_log("cc: " . print_r($company_code_options, true) . '\n', 3, "cc.log");
        $company_code = $company_code_options[0]['company_code'];
        error_log("cco: " . print_r($company_code, true) . '\n', 3, "cc.log");

        echo $company_code;
        
        break;

        case 'states':

        $country_id          = $_POST['country_id'];

        $pre_state_options  = state("",$country_id);

        $pre_state_options  = select_option($pre_state_options,"Select the State");

        echo $pre_state_options;
        
        break;

    case 'cities':

        $state_id           = $_POST['state_id'];

        $city_options       =    city("",$state_id);

        $city_options  = select_option($city_options,"Select the City");

        echo $city_options;
        
        break;

case 'get_sales_order_data':
    $sales_order_id = $_POST['sales_order_id'];
    error_log("sales order id: " . $sales_order_id . "\n" ,3, "sales_log.txt");

    // Get company_id and customer_id from sales_order table
    $table_details = [
        "sales_order",
        ["company_id", "customer_id"]
    ];
    $where = [
        "unique_id" => $sales_order_id,
        "is_active" => 1,
        "is_delete" => 0
    ];

    $result = $pdo->select($table_details, $where);

    if ($result->status && count($result->data) > 0) {
        $row = $result->data[0];
        
        error_log("row: " . print_r($row, true) . "\n", 3, "row.log");
        $company_id = $row['company_id'];
        $customer_id = $row['customer_id'];

        // Get company_name from company_creation
        $company_result = $pdo->select(
            ["company_creation", ["company_name"]],
            ["unique_id" => $company_id]
        );

        // Get supplier_name from supplier_profile
        $supplier_result = $pdo->select(
            ["customer_profile", [
                "customer_name",
                "country_unique_id",
                "state_unique_id",
                "city_unique_id",
                "address",
                "gst_no",
                "pan_no",
                "pincode"
            ]],
            ["unique_id" => $customer_id]
        );

        $supplier_data = $supplier_result->status ? $supplier_result->data[0] : [];
        
        error_log("supplier_data: " . print_r($supplier_result, true) . "\n", 3, 'sup_log.log');


        $company_name = $company_result->status ? $company_result->data[0]['company_name'] : "";
        $supplier_name = $supplier_result->status ? $supplier_result->data[0]['supplier_name'] : "";

    echo json_encode([
        "status" => true,
        "company_id" => $company_id,
        "company_name" => $company_name,
        "client_name" => $supplier_data['customer_name'] ?: '',
        "customer_id" => $customer_id,
        "supplier_details" => [
            "country" => $supplier_data['country_unique_id'] ?: '',
            "state" => $supplier_data['state_unique_id'] ?: '',
            "city" => $supplier_data['city_unique_id'] ?: '',
            "address" => $supplier_data['address'] ?: '',
            "gst_no" => $supplier_data['gst_no'] ?: '',
            "pan_no" => $supplier_data['pan_no'] ?: '',
            "pincode" => $supplier_data['pincode'] ?: '',
        ]
    ]);

    } else {
        echo json_encode(["status" => false]);
    }
    break;

    
    
    default:
        
        break;
}

?>