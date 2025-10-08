<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "user";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $full_name          = $_POST["full_name"];
        $user_name          = $_POST["user_name"];
        $password           = $_POST["password"];
        $user_type          = $_POST["user_type"];
        $is_active          = $_POST["is_active"];
        $phone_no           = $_POST["phone_no"];
        // $address         = $_POST["address"];
        $confirm_password   = $_POST["confirm_password"];
        $under_user         = $_POST["under_user"];
        $work_location_array = $_POST['work_location']; 
        $work_location = implode(",", $work_location_array);
        $team_members       = $_POST["team_users"];
        // $device_id          = $_POST["device_id"];
        $unique_id          = $_POST["unique_id"];
        $role = isset($_POST['role']) ? $_POST['role'] : null;



        $update_where       = "";

        $columns            = [
            "staff_unique_id"       => $full_name,
            "user_name"             => $user_name,
            "password"              => $password,
            "phone_no"              => $phone_no,
            "is_active"             => $is_active,
            // "address"            => $address,
            "user_type_unique_id"   => $user_type,
            "under_user"            => $under_user,
            "team_members"          => $team_members,
            // "device_id"             => $device_id,
            "work_location"         => $work_location,
            "role"                  => $role,
            "unique_id"             => unique_id($prefix)
        ];

        
        if (isset($_POST["is_team_head"])) {
            $columns["is_team_head"] = 1;
            
            if (!$_POST["team_id"]) {
                $columns["team_id"]      = unique_id();
            }
        } else {
            // $columns["team_id"]      = "";
            $columns["is_team_head"] = 0;
            $columns["team_members"] = '';
        }

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = '(user_name = "'.$user_name.'" OR phone_no="'.$phone_no.'")  AND staff_unique_id = "'.$full_name.'"  AND is_delete = 0  ';

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
            "(SELECT staff_name FROM staff_test AS staff WHERE staff.unique_id = ".$table.".staff_unique_id ) AS name",
            "phone_no",
            "user_name",
            "(SELECT user_type FROM user_type AS user_type WHERE user_type.unique_id = ".$table.".user_type_unique_id ) AS user_type",
            "password",
            // "device_id",
            "work_location",
            "is_active",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
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
                
                
 if($value['work_location'] != '' ){
 $work_location_ids = explode(',', $value['work_location']);
$work_locations = [];

foreach ($work_location_ids as $id) {
    $location_data = work_location($id); // Assuming this returns an array
    if (!empty($location_data[0]['work_location'])) {
        $work_locations[] = $location_data[0]['work_location'];
    }else{
        
    }
}

$value['work_location'] = implode(', ', $work_locations);
}else{
    $value['work_location'] ='-';
}



               $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == "1")
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
                
                $value['unique_id'] = $btn_update . $btn_toggle;
                
                $value['is_active'] = ($value['is_active'] == "1")
                    ? "<span style='color:green'>Active</span>"
                    : "<span style='color:red'>Inactive</span>";
                
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

        case 'user_options':

        $under_user          = $_POST['under_user'];

        $user_name_options  = under_user($under_user);

        $user_name_options  = select_option($user_name_options,"Select");

        echo $user_name_options;
        
        break;


        case 'mobile':

        $staff_id         = $_POST['staff_id'];

        $staff_mobile_no  = staff_name_bp($staff_id);

        echo $staff_mobile_no[0]["office_contact_no"];
        
        break;


    default:
        
        break;
}

?>