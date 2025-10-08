<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "project_creation";

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
        $client_name          = $_POST["client_name"];
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
    "client_name"       => $client_name,
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



                $value['is_active']   = is_active_show($value['is_active']);
                $btn_update           = btn_update($folder_name,$value['unique_id']);
                $btn_delete           = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']   = $btn_update.$btn_delete;
                $data[]               = array_values($value);
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
    
    
    case 'delete':
        
        $unique_id      = $_POST['unique_id'];

        $columns        = [
            "is_delete"   => 1
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table,$columns,$update_where);

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

case 'get_company_code':

        $company_id          = $_POST['company_id'];

        $company_code_options  = company_code("",$company_id);
        $company_code = $company_code_options[0]['company_code'];
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
        $company_id = $row['company_id'];
        $customer_id = $row['customer_id'];

        // Get company_name from company_creation
        $company_result = $pdo->select(
            ["company_creation", ["company_name"]],
            ["unique_id" => $company_id]
        );

        // Get supplier_name from supplier_profile
        $supplier_result = $pdo->select(
            ["supplier_profile", ["supplier_name"]],
            ["unique_id" => $customer_id]
        );

        $company_name = $company_result->status ? $company_result->data[0]['company_name'] : "";
        $supplier_name = $supplier_result->status ? $supplier_result->data[0]['supplier_name'] : "";

        echo json_encode([
            "status" => true,
            "company_id" => $company_id,
            "company_name" => $company_name,
            "client_name" => $supplier_name,
            "customer_id" => $customer_id
        ]);
    } else {
        echo json_encode(["status" => false]);
    }
    break;

    
    
    default:
        
        break;
}

?>