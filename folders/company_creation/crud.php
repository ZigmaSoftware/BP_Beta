<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "company_creation";

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
    $unique_id              = $_POST["unique_id"];
    $company_name           = $_POST["company_name"];
    $company_code           = $_POST["company_code"];
    $country                = $_POST["country"];
    $state                  = $_POST["state"];
    $city                   = $_POST["city"];
    $pin_code               = $_POST["pin_code"];
    $tel_number             = $_POST["tel_number"];
    $pan_number             = $_POST["pan_number"];
    $gst_number             = $_POST["gst_number"];
    $gst_reg_date           = $_POST["gst_reg_date"];
    $contact_person         = $_POST["contact_person"];
    $contact_number         = $_POST["contact_number"];
    $contact_email_id       = $_POST["contact_email_id"];
    $website                = $_POST["website"];
    $latitude               = $_POST['user_latitude'];
    $longitude              = $_POST['user_longitude'];
    $address                = $_POST["address"];
    $is_active              = $_POST["is_active"];

    // ✅ Added this line to fix NULL in file_attach
    $doc_up_filename = isset($_POST['existing_file_attach']) ? $_POST['existing_file_attach'] : "";

    $upload_dir = "../../uploads/company_creation/";
    $allowed_formats = ["jpg", "jpeg", "png"];
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
            // ✅ Updated this line to use shorter random filename
            $unique_filename = mt_rand(1000, 9999) . '.' . $file_ext;

            $target_path = $upload_dir . $unique_filename;

            if (move_uploaded_file($tmp_name, $target_path)) {
                $company_logo = $unique_filename;
            } else {
                echo "Failed to upload logo file.";
            }
        } else {
            echo "Invalid file format: only jpg, jpeg, png allowed.";
        }
    } else {
        $company_logo = $_POST['existing_company_logo'];
    }

     $upload_fun = fetch_docs($unique_id)[0]['file_attach']; // existing from DB

    if (!empty($upload_fun) && !empty($doc_up_filename)) {
        // both existing and new files present
        $doc_up_filename = $upload_fun . ',' . $doc_up_filename;
    } elseif (!empty($upload_fun)) {
        // only existing files present
        $doc_up_filename = $upload_fun;
    }
    // else: only new files present (keep $doc_up_filename as is)

    

    $columns = [
        "unique_id"            => unique_id($prefix),
        "company_name"         => $company_name,
        "company_code"         => $company_code,
        "country"              => $country,
        "state"                => $state,
        "city"                 => $city,
        "pin_code"             => $pin_code,
        "tel_number"           => $tel_number,
        "pan_number"           => $pan_number,
        "gst_number"           => $gst_number,
        "gst_date"             => $gst_reg_date,
        "contact_person"       => $contact_person,
        "contact_number"       => $contact_number,
        "contact_email_id"     => $contact_email_id,
        "website"              => $website,
        "latitude"             => $latitude,
        "longitude"            => $longitude,
        "address"              => $address,
        "logo"                 => $company_logo,
        "file_attach"          => $doc_up_filename, // ✅ Now defined
        "is_active"            => $is_active
    ];

        
  // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        $select_where       = 'company_name = "'.$company_name.'" AND company_code = "'.$company_code.'" AND is_delete = 0  ';


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

case 'upload_files_only':
    $upload_dir = "../../uploads/company_creation/";
    $allowed_formats = ["jpg", "jpeg", "png", "pdf"];
    $uploaded_files = [];

    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    if (!empty($_FILES["test_file"]["name"])) {
        foreach ($_FILES["test_file"]["name"] as $key => $name) {
            $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
            if (in_array($file_extension, $allowed_formats)) {
                // Use shorter file name
                $rand = mt_rand(1000, 9999);
                $unique_filename = $rand . '_' . time() . '.' . $file_extension;
                $target_path = $upload_dir . $unique_filename;

                if (move_uploaded_file($_FILES["test_file"]["tmp_name"][$key], $target_path)) {
                    $uploaded_files[] = $unique_filename;
                }
            }
        }
    }

    echo json_encode([
        "status" => true,
        "uploaded_files" => $uploaded_files
    ]);
    break;

case 'delete_uploaded_file':
    $filename = isset($_POST['filename']) ? trim($_POST['filename']) : '';
    $upload_dir = "../../uploads/company_creation/";

    $status = false;
    $msg = "error";
    $error = "";
    $sql = "";

    if (!empty($filename)) {
        $file_path = $upload_dir . $filename;

        if (file_exists($file_path)) {
            // Step 1: Delete the physical file
            unlink($file_path);

            // Step 2: Find the matching company record
            $table_details = [
                "company_creation",
                ["unique_id", "file_attach"]
            ];
            $where = "is_delete = 0 AND file_attach LIKE '%$filename%'";

            $result = $pdo->select($table_details, $where);

            if ($result->status && count($result->data) > 0) {
                foreach ($result->data as $row) {
                    $existing_files = explode(",", $row['file_attach']);
                    $filtered_files = array_filter($existing_files, function($f) use ($filename) {
                        return trim($f) !== $filename;
                    });

                    $updated_files = implode(",", $filtered_files);

                    $update_data = ["file_attach" => $updated_files];
                    $update_where = ["unique_id" => $row["unique_id"]];

                    $update_result = $pdo->update("company_creation", $update_data, $update_where);

                    if ($update_result->status) {
                        $status = true;
                        $msg = "deleted";
                    } else {
                        $error = $update_result->error;
                    }
                }
            } else {
                $status = true; // File deleted but no DB match
                $msg = "deleted";
            }
        } else {
            $error = "File not found";
        }
    } else {
        $error = "No filename provided";
    }

    $json_array = [
        "status" => $status,
        "msg"    => $msg,
        "error"  => $error,
        "sql"    => $sql
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
            "company_name",
            "company_code",
            "state",
            "city",
            "pin_code",
            "latitude",
            "longitude",
            "logo",
            "file_attach",
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

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
    if (!empty($search)) {
         $where .= " AND company_name LIKE '%" . $search . "%'";
   
    }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if ($value['company_branch_type'] == "1") {
                    $value['company_branch_type']   = "Ho";
                } else {

                    $value['company_branch_type']   = "Branch";
                }

                if ($value['country']) {
                    $value['country']   = country($value['country'])[0]['name'];
                }
                if ($value['state']) {
                    $value['state']   = state($value['state'])[0]['state_name'];
                }
                if ($value['city']) {
                    $value['city']   = city($value['city'])[0]['city_name'];
                }
                if ($value['branch_name']){
                $value['branch_name'] = disname($value['branch_name']);
                    }
                    else{
                        $value['branch_name'] ="";
                    }
                    
                    
                    
                if (is_null($value['logo']) || $value['logo'] == '') {
                    $value_sub['logo'] = "<td style='text-align:center'><span class='font-weight-bold'>No Image Uploaded</span></td>";
                } else {
                    $image_files = explode(',', $value['logo']);
                    $image_buttons = "";
                    foreach ($image_files as $image_file) {
                    $image_path = "../blue_planet_beta/uploads/company_creation/" . trim($image_file);
$view_button = "<button onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'>
 <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                        </button>";
                        $image_buttons .= $view_button;
                    }
$value['logo']= "<td style='text-align:center'>" . $image_buttons . "</td>";
                }
                                
                                
                                
if (is_null($value['file_attach']) || $value['file_attach'] == '') {
    $value_sub['logo'] = "<td style='text-align:center'><span class='font-weight-bold'>No Image Uploaded</span></td>";
} else {
    $image_files = explode(',', $value['file_attach']);
    $image_buttons = "";
    foreach ($image_files as $image_file) {
        $image_path = "../blue_planet_beta/uploads/company_creation/" . trim($image_file);
        $view_button = "<button onclick=\"openDocumentWindow('$value[file_attach]')\" class='btn btn-sm btn-primary'>View</button>";

        break; // ✅ Only one view button for all files
    }
    $value['file_attach'] = "<td style='text-align:center'>" . $view_button . "</td>";
}

                    
                    
                    

                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = $value['is_active'] == 1
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
                
                $value['is_active'] = is_active_show($value['is_active']); // optional green/red text
                
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
    
    
   case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active'];

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
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
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

    
    
    default:
        
        break;
}

function fetch_docs($unique_id = "") {
    global $pdo;
    
    $table = "company_creation";
    
    $table_columns = [
        "file_attach"    
    ];
    
    $table_details = [
        $table,
        $table_columns
    ];
    
    $where = [
        "unique_id" => $unique_id  
    ];
    
    $result = $pdo->select($table_details, $where);
    
    if($result->status){
        return $result->data;
    } else {
        return 0;
    }
}

?>