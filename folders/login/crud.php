<?php 

// $folder_name        = explode("/",$_SERVER['PHP_SELF']);

// $folder_name        = $folder_name[count($folder_name)-2];

// $table              = "user";

// // Include DB file and Common Functions
// include '../../config/dbconfig.php';

// // Variables Declaration
// $action             = $_REQUEST['action'];
// $action_obj         = (object) [
//     "status"    => 0,
//     "data"      => "",
//     "error"     => "Action Not Performed"
// ];
// $json_array         = "";
// $sql                = "";

// $data               = "";
// $msg                = "";
// $error              = "";
// $status             = "";
// $test               = ""; // For Developer Testing Purpose

// // $acc_year       = explode(" to ",$_REQUEST['acc_year']);
// // $from_year      = (explode("-",$acc_year[0]))[0];
// // $to_year        = (explode("-",$acc_year[1]))[0];

// // $acc_year       = explode(" to ",$_REQUEST['acc_year']);
// $from_year      = "2020";
// $to_year        = "2021";

// $sess_acc_year  = $from_year."-".$to_year;

// $sess_acc_year  = acc_year();

// switch ($action) {
    
    
//     case 'login':

//         $user_name  = $_REQUEST['user_name'];
//         $password   = $_REQUEST['password'];

//         $columns    = [
//             "COUNT(unique_id) AS count",
//             "user_type_unique_id",
//             "staff_unique_id",
//             "profile_image AS user_image1",
//             "(SELECT file_name FROM staff WHERE unique_id = $table.staff_unique_id) AS user_image",
//             "is_team_head",
//             "(SELECT work_location from staff where employee_id = $table.staff_unique_id) AS work_location",
//             "unique_id"
//         ];

//         $table_details = [
//             $table,
//             $columns
//         ];

//         $where = [
//             "user_name" => $user_name,
//             "password"  => $password,
//             "is_active" => 1,
//             "is_delete" => 0 
//         ];

//         // $where  = " (user_name LIKE BINARY '".$user_name."' OR phone_no LIKE BINARY  '".$user_name."') AND password LIKE BINARY  '".$password."' AND is_active = 1 AND is_delete = 0 ";

//         $where  = " (user_name = '".$user_name."' OR phone_no =  '".$user_name."') AND password =  '".$password."' AND is_active = 1 AND is_delete = 0 ";

//         $action_obj    = $pdo->select($table_details,$where);

//         if ($action_obj->status) {
//             $count_data     = $action_obj->data[0]["count"];
//             if ($count_data == 1) {
//                 // session_start();
//                 $user                              = $action_obj->data[0];
//                 $msg                               = "success_login";

//                 $user_id                           = $user['unique_id'];
//                 $_SESSION["acc_year"]              = $sess_acc_year;
//                 $_SESSION["user_id"]               = $user_id;
//                 $_SESSION["staff_id"]              = $user['staff_unique_id'];
//                 $staff_name                        = staff_name($user['staff_unique_id']);
//                 $_SESSION["staff_name"]            = $staff_name[0]['staff_name'];
//                 $_SESSION["file_name"]             = $staff_name[0]['file_name'];
//                 $designation                       = $staff_name[0]['designation_unique_id'];
//                 $designation_type                  = work_designation($designation);
//                 $_SESSION["designation_type"]      = $designation_type[0]['designation'];
//                 $_SESSION["work_location"]         = $user['work_location'];
//                 $_SESSION["user_name"]             = $user_name;
//                 $_SESSION['sess_user_type']        = $user['user_type_unique_id'];
//                 $_SESSION['sess_user_id']          = $user_id;
//                 $_SESSION['sess_user_location']    = 'Erode';
//                 $_SESSION['sess_company_name']     = 'Ascent E-Digit Solutions';
//                 $_SESSION['sess_company_address']  = '64, Kalaimagal School Road,';
//                 $_SESSION['sess_company_district'] = 'Erode 638001,';
//                 $_SESSION['sess_company_state']    = 'Tamil Nadu, INDIA.';
//                 $_SESSION['sess_company_phone_no'] = '0424-2269797';
//                 $_SESSION['sess_company_id']       = "comp5fa3b1c2a3bab70290";
//                 $_SESSION['sess_img_path']         = "img/";
//                 //$_SESSION['company_logo']          = "logo-new1.png";
// 		$_SESSION['company_logo']          = "logo-new.png";
//                 $_SESSION['sess_branch_id']        = "bran5fa3b1dced5d363322";
//                 $_SESSION['is_team_head']          = $user['is_team_head'];
//                 $_SESSION['ip_address']            = get_client_ip();
//                 $msg                               = "success_login";

//                 $permissions                       = menu_permission($user['user_type_unique_id']);

//                 // print_r($permissions);

//                 $main_screens                      = $permissions["main_screens"];
//                 $sections                          = $permissions["sections"];
//                 $screens                           = $permissions["screens"];

//                 $_SESSION['main_screens']          = $main_screens;
//                 $_SESSION['sections']              = $sections;
//                 $_SESSION['screens']               = $screens;
//                 $user_image                        = "img/user.jpg";

//             //    if ((file_exists(DIRECTORY_SEPARATOR."uploads".DIRECTORY_SEPARATOR."staff".DIRECTORY_SEPARATOR.$user['user_image'])) && ($user['user_image'] != "")) {
//             //         $user_image = "uploads/staff/".$user['user_image'];
//             //     }
//                 if ($user['user_image']) {
//                     $user_image = "uploads/staff/".$user['user_image'];
//                 }

//                 // $_SESSION['file_status'] = file_exists(DIRECTORY_SEPARATOR."folders".DIRECTORY_SEPARATOR."staff".DIRECTORY_SEPARATOR.$user['user_image']);
                
//                 $_SESSION["user_image"] = $user_image;
//                 // $_SESSION["user_image"] = ;
//                 // $_SESSION["user_image1"] = $user['user_image'];


//                 $json_array = [
//                     "status"    => 1,
//                     "data"      => 1,
//                     "error"     => 0,
//                     "msg"       => "success_login",
//                     // "test"      => $_SESSION["file_status"],
//                     "sql"       => $_SESSION["user_id"],
//                     "session"   => $_SESSION
//                 ];

//                 $column_update = [
//                     "device_id" => $_REQUEST['device_id'],
//                 ];

//                 $update_where   = [
//                     "staff_unique_id"     => $user['staff_unique_id']
//                 ];
                
//                 // Update Begins
//                 $action_obj_update     = $pdo->update($table,$column_update,$update_where);


//             } else {
//                 // Incorrect username and password handling 
//                 $json_array = [
//                     "status"    => 0,
//                     "data"      => 1,
//                     "error"     => 0,
//                     "msg"       => "incorrect",
//                     "sql"       => ""
//                 ];
//             }
//         } else {
//             $msg    = "error";
//         }


//         // // For Tempervary Login 
//         // if (($user_name == "admin") && ($password == "password")) {
//         //         // session_start();
//         //         $user_id                           = "user5fa3b19be003d60568";
//         //         $_SESSION["acc_year"]              = $sess_acc_year;
//         //         $_SESSION["user_id"]               = $user_id;
//         //         $_SESSION["user_name"]             = $user_name;
//         //         $_SESSION['sess_user_type']        = "5f97fc3257f2525529";
//         //         $_SESSION['sess_user_id']          = $user_id;
//         //         $_SESSION['sess_user_location']    = 'Erode';
//         //         $_SESSION['sess_company_name']     = 'Ascent E-Digit Solutions';
//         //         $_SESSION['sess_company_address']  = '64, Kalaimagal School Road,';
//         //         $_SESSION['sess_company_district']  = 'Erode 638001,';
//         //         $_SESSION['sess_company_state']  = 'Tamil Nadu, INDIA.';
//         //         $_SESSION['sess_company_phone_no'] = '0424-2269797';
//         //         $_SESSION['sess_company_id']       = "comp5fa3b1c2a3bab70290";
//         //         $_SESSION['sess_img_path']         = "img/";
//         //         $_SESSION['sess_branch_id']        = "bran5fa3b1dced5d363322";
//         //         $msg                               = "success_login";

//         //         $json_array = [
//         //             "status"    => 1,
//         //             "data"      => 1,
//         //             "error"     => 0,
//         //             "msg"       => "success_login",
//         //             "sql"       => $_SESSION["user_id"]
//         //         ];
//         // } else {
//         //     $json_array = [
//         //         "status"    => 0,
//         //         "data"      => 1,
//         //         "error"     => 0,
//         //         "msg"       => "incorrect",
//         //         "sql"       => ""
//         //     ];
//         // }
        
//         echo json_encode($json_array);
        
//         break;
    
//     default:
        
//         break;
// }

?>

<?php 

$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name)-2];
$table = "user";

include '../../config/dbconfig.php';

$action = $_REQUEST['action'];
$action_obj = (object) [
    "status" => 0,
    "data" => "",
    "error" => "Action Not Performed"
];
$json_array = "";
$sql = "";

$data = "";
$msg = "";
$error = "";
$status = "";
$test = "";

$from_year = "2020";
$to_year = "2021";
$sess_acc_year = acc_year();

switch ($action) {
    case 'login':

        $user_name = $_REQUEST['user_name'];
        $password = $_REQUEST['password'];
        
        error_log("data: " . print_r($_REQUEST, true) . "\n", 3, "request.log");

        $columns = [
            "COUNT(unique_id) AS count",
            "user_type_unique_id",
            "staff_unique_id",
            "profile_image AS user_image1",
            "(SELECT file_name FROM staff_test WHERE unique_id = $table.staff_unique_id) AS user_image",
            "is_team_head",
            "(SELECT work_location from staff_test where employee_id = $table.staff_unique_id) AS work_location",
            "unique_id",
            "password_updated",
            "password_status"
        ];

        $table_details = [
            $table,
            $columns
        ];

        $where = "(user_name = '".$user_name."' OR phone_no =  '".$user_name."') AND password =  '".$password."' AND is_active = 1 AND is_delete = 0 ";

        $action_obj = $pdo->select($table_details, $where);
        error_log("status: " . print_r($action_obj, true) . "\n", 3, 'request.log');

        if ($action_obj->status) {
            $count_data = $action_obj->data[0]["count"];
            if ($count_data == 1) {
                $user = $action_obj->data[0];
                $password_updated = $user['password_updated'];
                $password_status = $user['password_status'];
                $user_id = $user['unique_id'];

                // Check password expiration
                $force_password_change = false;
                if ($password_updated) {
                    $last_updated = new DateTime($password_updated);
                    $now = new DateTime();
                    $diff = $last_updated->diff($now);
                    $months_diff = ($diff->y * 12) + $diff->m;
                    if ($months_diff >= 6) {
                        $force_password_change = true;
                    }
                } else {
                    $force_password_change = true;
                }

                if ($force_password_change) {
                    // Set password_status = 1 in DB
                    $pdo->update("user", ["password_status" => 1], ["unique_id" => $user_id]);

                    echo json_encode([
                        "status" => 1,
                        "msg" => "force_password_change",
                        "force_password_change" => true,
                        "session" => ["user_id" => $user_id]
                    ]);
                    return;
                }

                if ($password_status == 1) {
                    echo json_encode([
                        "status" => 1,
                        "msg" => "force_password_change",
                        "force_password_change" => true,
                        "session" => ["user_id" => $user_id]
                    ]);
                    return;
                }

                // Normal login proceeds
                session_start();
                $_SESSION['force_password_change'] = false;
                $_SESSION["acc_year"] = $sess_acc_year;
                $_SESSION["user_id"] = $user_id;
                $_SESSION["staff_id"] = $user['staff_unique_id'];
                $staff_name = staff_name_bp($_SESSION["staff_id"]);
                $_SESSION["staff_name"] = $staff_name[0]['staff_name'];
                $_SESSION["file_name"] = $staff_name[0]['file_name'];
                $designation = $staff_name[0]['designation_unique_id'];
                $designation_type = work_designation($designation);
                $_SESSION["designation_type"] = $designation_type[0]['designation'];
                $_SESSION["work_location"] = $user['work_location'];
                $_SESSION["user_name"] = $user_name;
                $_SESSION['sess_user_type'] = $user['user_type_unique_id'];
                $_SESSION['sess_user_id'] = $user_id;
                $_SESSION['sess_user_location'] = 'Erode';
                $_SESSION['sess_company_name'] = 'Ascent E-Digit Solutions';
                $_SESSION['sess_company_address'] = '64, Kalaimagal School Road,';
                $_SESSION['sess_company_district'] = 'Erode 638001,';
                $_SESSION['sess_company_state'] = 'Tamil Nadu, INDIA.';
                $_SESSION['sess_company_phone_no'] = '0424-2269797';
                $_SESSION['sess_company_id'] = "comp5fa3b1c2a3bab70290";
                $_SESSION['sess_img_path'] = "img/";
                $_SESSION['company_logo'] = "logo-new.png";
                $_SESSION['sess_branch_id'] = "bran5fa3b1dced5d363322";
                $_SESSION['is_team_head'] = $user['is_team_head'];
                $_SESSION['ip_address'] = get_client_ip();

                $permissions = menu_permission($user['user_type_unique_id']);
                $_SESSION['main_screens'] = $permissions["main_screens"];
                $_SESSION['sections'] = $permissions["sections"];
                $_SESSION['screens'] = $permissions["screens"];

                $user_image = "img/user.jpg";
                if ($user['user_image']) {
                    $user_image = "uploads/staff/" . $user['user_image'];
                }
                $_SESSION["user_image"] = $user_image;

                $json_array = [
                    "status" => 1,
                    "data" => 1,
                    "error" => 0,
                    "msg" => "success_login",
                    "force_password_change" => false,
                    "session" => $_SESSION
                ];

                $column_update = [
                    "device_id" => $_REQUEST['device_id']
                ];
                $update_where = [
                    "staff_unique_id" => $user['staff_unique_id']
                ];
                $action_obj_update = $pdo->update($table, $column_update, $update_where);

            } else {
                $json_array = [
                    "status" => 0,
                    "data" => 1,
                    "error" => 0,
                    "msg" => "incorrect",
                    "sql" => ""
                ];
            }
        } else {
            $msg = "error";
        }

        echo json_encode($json_array);
        break;

    case 'update_password':
        $user_id = $_POST['user_id'];
        $new_password = $_POST['new_password'];

        $update = $pdo->update("user", [
            "password" => $new_password,
            "password_updated" => date("Y-m-d H:i:s"),
            "password_status" => 0
        ], [
            "unique_id" => $user_id
        ]);

        echo json_encode([
            "status" => 1,
            "msg" => "Password updated"
        ]);
        break;

    default:
        break;
}

?>

