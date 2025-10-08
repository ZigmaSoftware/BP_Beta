<?php
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table                   = "expense_creation_main";
$table_sub               = "expense_creation_sub";
$table_sub_hotel         = "expense_creation_sub_hotel";
$table_sub_travel        = "expense_creation_sub_travel";
$table_sub_petrol        = "expense_creation_sub_petrol";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';

//$fileUploadPath = $fileUploadConfig->get("upload_folder");

// Create Folder in root->uploads->(this_folder_name) Before using this file upload
// $fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name . DIRECTORY_SEPARATOR);

// File Upload Library Call
//$fileUpload         =  new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$country_name       = "";
$state_name         = "";
$prefix             = "exp";
$expense_prefix     = "EXP-";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose
$approved_date_1= date("D M d, Y G:i a");
switch ($action) {
    case 'createupdate':

        $unique_id           = $_POST["unique_id"];
        $hr_approval         = $_POST["hr_approval"];
        $approved_date         = $_POST["approved_date"];
        $approved_by          = $_POST["approved_by"];
        $approved_description    = $_POST["approved_description"];

        $update_where       = "";

        $columns            = [
            "hr_approval"            => $hr_approval,
            "user_approval_id"          => $_SESSION['staff_id'], 
            "approved_description"        => $approved_description,
            "approved_date"            => $approved_date_1,
            
        ];


        // check already Exist Or not
        $table_details      = [
            // $table,
            // [
            //     "COUNT(unique_id) AS count"
            // ]
        ];
        $select_where       = 'hr_approval = "' . $hr_approval . '"  AND is_delete = 0  ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="' . $unique_id . '" ';
            // $exp_no = $_POST['exp_no'];
        } 
        // else {
        //     $bill_no_where   = [
        //         "acc_year"      => $_SESSION['acc_year']
        //     ];

            // GET Bill No
            // $exp_no             = bill_no($table, $bill_no_where, $expense_prefix);
            // $columns['exp_no']  = $exp_no;
            // // echo $follow_up_call_id;
        // }
        $action_obj         = $pdo->select($table_details, $select_where);

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
            if ($unique_id) {
                $sublist_columns  = [
                    "exp_main_unique_id"      => $unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                $sublist_columns_hotel  = [
                    "exp_main_unique_id"      => $unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist_hotel = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                $sublist_columns_travel  = [
                    "exp_main_unique_id"      => $unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist_travel = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                $sublist_columns_petrol  = [
                    "exp_main_unique_id"      => $unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist_petrol = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $main_unique_id = $unique_id;

                $action_obj     = $pdo->update($table, $columns, $update_where);
                $action_obj_sub   = $pdo->update($table_sub, $sublist_columns, $where_sublist);
                $action_obj_sub_hotel   = $pdo->update($table_sub_hotel, $sublist_columns_hotel, $where_sublist_hotel);
                $action_obj_sub_travel   = $pdo->update($table_sub_travel, $sublist_columns_travel, $where_sublist_travel);
                $action_obj_sub_petrol   = $pdo->update($table_sub_petrol, $sublist_columns_petrol, $where_sublist_petrol);

                // Update Ends
            } else {

                $sublist_columns  = [
                    "exp_main_unique_id"      => $main_unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                $sublist_columns_hotel  = [
                    "exp_main_unique_id"      => $main_unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist_hotel = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                $sublist_columns_travel  = [
                    "exp_main_unique_id"      => $main_unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist_travel = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];

                $sublist_columns_petrol  = [
                    "exp_main_unique_id"      => $main_unique_id,
                    "entry_date"              => $entry_date,
                    "exp_no"                  => $exp_no,
                ];

                $where_sublist_petrol = [
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];
                // Insert Begins            
                $action_obj       = $pdo->insert($table, $columns);
                $action_obj_sub   = $pdo->update($table_sub, $sublist_columns, $where_sublist);
                $action_obj_sub_hotel   = $pdo->update($table_sub_hotel, $sublist_columns_hotel, $where_sublist_hotel);
                $action_obj_sub_travel   = $pdo->update($table_sub_travel, $sublist_columns_travel, $where_sublist_travel);
                $action_obj_sub_petrol   = $pdo->update($table_sub_petrol, $sublist_columns_petrol, $where_sublist_petrol);
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

        $exp_unique_id = "";

        if ($unique_id) {
            $exp_unique_id = $unique_id;
        } else {
            $exp_unique_id = $columns['unique_id'];
        }

        $json_array   = [
            "status"           => $status,
            "data"             => $data,
            "error"            => $error,
            "msg"              => $msg,
            "sql"              => $sql,
            "exp_unique_id"    => $exp_unique_id
        ];

        echo json_encode($json_array);

        break;

    case 'datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start         = $_POST['start'];
        $draw         = $_POST['draw'];
        $limit         = $length;

        $data        = [];


        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "exp_no",
            "branch_unique_id",
            "(SELECT sum(amount) FROM expense_creation_sub WHERE ". $table . ".screen_unique_id = expense_creation_sub.screen_unique_id ) AS main_id",
           
            "hr_approval",
            "unique_id",
            "(SELECT sum(amount) FROM expense_creation_sub_hotel WHERE " . $table . ".screen_unique_id = expense_creation_sub_hotel.screen_unique_id ) AS amount_hotel",
            "(SELECT sum(amount) FROM expense_creation_sub_petrol WHERE " . $table . ".screen_unique_id = expense_creation_sub_petrol.screen_unique_id ) AS rate",
            "(SELECT sum(amount) FROM expense_creation_sub_travel WHERE " . $table . ".screen_unique_id = expense_creation_sub_travel.screen_unique_id ) AS amount_travel",
        ];

        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];


        // if ($_POST['executive_name']!='') {
        //     $executive_name = "branch_unique_id = '".$_POST['executive_name']."' AND" ;
        // } else {
        //     $executive_name = '';
        // }

        $where  = 'is_active = 1 AND is_delete = 0 ';

        if ($_SESSION['sess_user_type'] == $admin_user_type) {
        } else {
            $where  .= ' AND branch_unique_id = "' . $_SESSION['staff_id'] . '" ';
        }

        if (isset($_POST['from_date'])) {
            if ($_POST['from_date']) {
                $where .= " AND entry_date >= '" . $_POST['from_date'] . "' ";
            }
        }

        if (isset($_POST['to_date'])) {
            if ($_POST['to_date']) {
                $where .= " AND entry_date <= '" . $_POST['to_date'] . "' ";
            }
        }

        if ($_POST['executive_name'] != '') {
            $where     .= " AND branch_unique_id = '" . $_POST['executive_name'] . "' ";
        }
        if ($_POST['pending_status'] != '') {
            $where     .= " AND hr_approval = '" . $_POST['pending_status'] ."' ";
        }

        $order_by       = "";

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['entry_date']    = disdate($value['entry_date']);

                $staff                  = staff_name($value['branch_unique_id']);
                $staff_name             = disname($staff[0]['staff_name']);
                if($value["hr_approval"] == 1) {
                    $value["hr_approval"]='Approved';
                }
                else if($value["hr_approval"] == 2) {
                    $value["hr_approval"]='Pending';
                }
                else if($value["hr_approval"] == 3) {
                    $value["hr_approval"]='Cancel';
                }

                if($value['hr_approval'] == '' ){
                    $btn_update             = btn_update($folder_name,$value['unique_id']);
                } else {
                    $btn_update             = "";
                }
                $btn_approve                  = btn_approval($folder_name,$value['unique_id'],$value['hr_approval']);
                $value['hr_approval']  = $btn_approve;
                $value['branch_unique_id'] = $staff_name;
                // $value["main_id"]       = moneyFormatIndia($value['main_id']);
                $value["main_id"] = ($value['amount_hotel']+ ($value['rate'])+($value['amount'])+($value['main_id'])+($value['amount_travel']));
                $value['unique_id']     = $btn_update;
                $data[]                 = array_values($value);
            }

            $json_array = [
                "draw"                => intval($draw),
                "recordsTotal"         => intval($total_records),
                "recordsFiltered"     => intval($total_records),
                "data"                 => $data,
                "testing"            => $result->sql
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;


    case 'delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table, $columns, $update_where);

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



    case 'staff_name':

        $name_type          = $_POST['name_type'];


        if ($name_type == '0') {
            // if ($_SESSION['sess_user_type'] != "5f97fc3257f2525529") {

            $table_user = "user";

            $select_where = [
                "unique_id"                 => $_SESSION['user_id'],
                "is_active"                 => 1,
                "is_delete"                 => 0
            ];
            $columns   = [
                "unique_id",
                "under_user"
            ];
            $table_details = [
                $table_user,
                $columns
            ];


            $select_result = $pdo->select($table_details, $select_where, '', '', '', '', '');
            if ($select_result->status) {
                $status     = $select_result->status;
                $data       = $select_result->data;
                $error      = "";
                $sql        = $select_result->sql;
            }



            $exp_result  = explode(',', $data[0]["under_user"]);
            $imp_result  = implode("','", $exp_result);

            $columns   = [
                "(SELECT unique_id from staff WHERE staff.unique_id =" . $table_user . ".staff_unique_id ) AS staff_unique_id",
                "(SELECT staff_name from staff WHERE staff.unique_id =" . $table_user . ".staff_unique_id ) AS staff_name"
            ];

            $table_details = [
                $table_user,
                $columns
            ];
            // if ($_SESSION['sess_user_type'] != "5f97fc3257f2525529") {
            $staff_select_where  = " is_active = 1 AND is_delete = 0 ";
            /* } else {
             $staff_select_where  = " is_active = 1 AND is_delete = 0 ";
        }*/
            $result = $pdo->select($table_details, $staff_select_where);
            if ($result->status) {
                $status     = $result->status;
                $data       = $result->data;
                $error      = "";
                $sql        = $result->sql;

                $executive_options   = $data;
                $executive_options   = select_option($executive_options, "Select Staff Name");

                //$label_name  = "Staff Name";

            }
        } else {
            $executive_options = branch();
            $executive_options = select_option($executive_options, "Select Branch Name", '');

            //$label_name  = "Branch Name";
        }
        echo $executive_options;

        break;

    case "designation":

        $staff_name  = $_POST['staff_name'];
        $data       = [];

        // Query Variables
        $json_array     = "";
        $columns        = [
            "designation_unique_id AS designation_unique_id",
            "grade AS grade",
            "(SELECT designation FROM designation_creation as designation JOIN staff ON designation.unique_id = staff.designation_unique_id WHERE staff.unique_id = '" . $staff_name . "') AS designation_name",
            "(SELECT grade_type FROM grade_type as grade_type JOIN staff ON grade_type.unique_id = staff.grade WHERE staff.unique_id = '" . $staff_name . "') AS grade_name",
        ];
        $table_details  = [
            "staff",
            $columns
        ];
        $where          = [
            "unique_id"    => $staff_name,
            "is_active"    => 1,
            "is_delete"    => 0
        ];

        $result         = $pdo->select($table_details, $where);
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

    case "get_city_type":

        $city_hotel  = $_POST['city_hotel'];
        $data       = [];

        // Query Variables
        $json_array     = "";
        $columns        = [
            "city_type as city_type",
            //"unique_id"
        ];
       
        $table_details  = [
            "cities",
            $columns
        ];
        $where          = [
            "unique_id"    => $city_hotel,
            "is_active"    => 1,
            "is_delete"    => 0
        ];

        $result         = $pdo->select($table_details, $where);
        //print_r($result);

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


        // get hotel city

        case "get_city_type_hotel":

            $city_hotel_type  = $_POST['city_hotel_type'];
            $data       = [];
    
            // Query Variables
            $json_array     = "";
            $columns        = [
                "city_type as city_hotel_type",
                //"unique_id"
            ];
           
            $table_details  = [
                "cities",
                $columns
            ];
            $where          = [
                "unique_id"    => $city_hotel_type,
                "is_active"    => 1,
                "is_delete"    => 0
            ];
    
            $result         = $pdo->select($table_details, $where);
            //print_r($result);
    
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

    case "expense_max_limit":

        $expense_type  = $_POST['expense_type'];
        $grade_type  = $_POST['grade_type'];
        $city_type  = $_POST['city_type'];
        $data       = [];

        // Query Variables
        $json_array     = "";
        $columns        = [
            "unique_id",
            "limit_value",
            // "limit_status"

        ];
        $table_details  = [
            "expense_type_sub",
            $columns
        ];
        $where  ="is_delete=0 and is_active=1 and expense_type_unique_id='".$expense_type."' and type='".$city_type."' and grade='".$grade_type."'";
       
        $result         = $pdo->select($table_details, $where);
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



        // get hotel amount
        case "expense_max_limit_hotel":

            $expense_type_hotel  = $_POST['expense_type_hotel'];
            $grade_type  = $_POST['grade_type'];
            $city  = $_POST['city'];
            $data       = [];
    
            // Query Variables
            $json_array     = "";
            $columns        = [
                "unique_id",
                "limit_value",
                // "limit_status"
    
            ];
            $table_details  = [
                "expense_type_sub",
                $columns
            ];
            $where  ="is_delete=0 and is_active=1 and expense_type_unique_id='".$expense_type_hotel."' and type='".$city."' and grade='".$grade_type."'";
            $result         = $pdo->select($table_details, $where);
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

    case 'exp_food_daily_expense_sub_add_update':

        $file_names    = "";

        $entry_date              = $_POST["entry_date"];
        $expense_type            = $_POST["expense_type"];
        $amount                  = $_POST["amount"];
        $sub_description         = $_POST["sub_description"];
        $customer_name           = $_POST["customer_name"];
        $call_no                 = $_POST["call_no"];
        $screen_unique_id        = $_POST["screen_unique_id"];
        $test_file               = $_POST['file'];
        $unique_id               = $_POST["unique_id"];

        $update_where               = "";


        // $image_array=$_FILES['test_file'];
        //  $picturename1=$image_array['name'];
        //  $main_name=$image_array['tmp_name'];
        //  $upload_file1='../uploads/expense_creation/'.$picturename1;
        //              copy($main_name,$upload_file1);

        $tmp_name = $_FILES["test_file"]["tmp_name"];
        // $image2= $_FILES['test_file']['name'];
        $rand_val = unique_id($prefix);
        $ext = pathinfo($test_file);
        $img1 = $rand_val . '.' . $ext['extension'];

        $target = "../../uploads/expense_creation/" . $img1;

        move_uploaded_file($tmp_name, $target);

        //         if($test_file){
        //         if (is_array($_FILES["test_file"]['name'])) {
        //         if ($_FILES["test_file"]['name'][0] != "") {
        //             // Multi file Upload 
        //             $confirm_upload     = $fileUpload->uploadFiles("test_file");
        //                 if (is_array($confirm_upload)) {
        //                     $_FILES["test_file"]['file_name'] = [];
        //                         foreach ($confirm_upload as $c_key => $c_value) {
        //                             if ($c_value->status == 1) {
        //                                 $c_file_name = $c_value->name ? $c_value->name.".".$c_value->ext : "";
        //                                 array_push($_FILES["test_file"]['file_name'],$c_file_name);
        //                             } else {// if Any Error Occured in File Upload Stop the loop
        //                                 $status     = $confirm_upload->status;
        //                                 $data       = "file not uploaded";
        //                                 $error      = $confirm_upload->error;
        //                                 $sql        = "file upload error";
        //                                 $msg        = "file_error";
        //                                 break;
        //                             }
        //                         }  
        //                 } else if (!empty($_FILES["test_file"]['name'])) {// Single File Upload
        //                     $confirm_upload     = $fileUpload->uploadFile("test_file");

        //                     if($confirm_upload->status == 1) {
        //                         $c_file_name = $confirm_upload->name ? $confirm_upload->name.".".$confirm_upload->ext : "";
        //                         $_FILES["test_file"]['file_name']  = $c_file_name;
        //                     } else {// if Any Error Occured in File Upload Stop the loop
        //                         $status     = $confirm_upload->status;
        //                         $data       = "file not uploaded";
        //                         $error      = $confirm_upload->error;
        //                         $sql        = "file upload error";
        //                         $msg        = "file_error";
        //                     }                    
        //                 }
        //         }
        //     }

        //     if (is_array($_FILES["test_file"]['name'])) {
        //         if ($_FILES["test_file"]['name'][0] != "") {
        //             $file_names     = implode(",",$_FILES["test_file"]['file_name']);
        //             $file_org_names = implode(",",$_FILES["test_file"]['name']);
        //         }                            
        //     } else if (!empty($_FILES["test_file"]['name'])) {
        //         $file_names     = $_FILES["test_file"]['file_name'];
        //         $file_org_names = $_FILES["test_file"]['name'];
        //     }
        // }   

        if ($img1) {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type,
                "amount"                     => $amount,
                "description"                => $sub_description,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "file_name"                  => $img1,
                "file_original_name"         => $ext['basename'],
                "unique_id"                  => unique_id($prefix)
            ];
        } else {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type,
                "amount"                     => $amount,
                "description"                => $sub_description,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "unique_id"                  => unique_id($prefix)
            ];
        }

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table_sub, $columns, $update_where);

            // Update Ends
        } else {

            // Insert Begins            
            $action_obj     = $pdo->insert($table_sub, $columns);

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


        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;

    case 'exp_hotel_expense_sub_add_update':

        $file_names    = "";

        $entry_date              = $_POST["entry_date"];
        $expense_type_hotel      = $_POST["expense_type_hotel"];
        $amount_hotel            = $_POST["amount_hotel"];
        $sub_description_hotel   = $_POST["sub_description_hotel"];
        $customer_name           = $_POST["customer_name"];
        $call_no                 = $_POST["call_no"];
        $screen_unique_id        = $_POST["screen_unique_id"];
        $test_file_hotel         = $_POST['test_file_hotel'];
        $unique_id               = $_POST["unique_id"];

        $update_where               = "";

        $tmp_name = $_FILES["test_file_hotel"]["tmp_name"];
        $rand_val = unique_id($prefix);
        $ext = pathinfo($test_file_hotel);
        $img1 = $rand_val . '.' . $ext['extension'];

        $target = "../../uploads/expense_creation/" . $img1;

        move_uploaded_file($tmp_name, $target);

        if ($img1) {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type_hotel,
                "amount"                     => $amount_hotel,
                "description"                => $sub_description_hotel,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "expense_type"               => $expense_type_hotel,
                "screen_unique_id"           => $screen_unique_id,
                "file_name"                  => $img1,
                "file_original_name"         => $ext['basename'],
                "unique_id"                  => unique_id($prefix)
            ];
        } else {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type_hotel,
                "amount"                     => $amount_hotel,
                "description"                => $sub_description_hotel,
                "customer_unique_id"         => $customer_name,
                "expense_type"              => $expense_type_hotel,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "unique_id"                  => unique_id($prefix)
            ];
        }

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table_sub_hotel, $columns, $update_where);

            // Update Ends
        } else {

            // Insert Begins            
            $action_obj     = $pdo->insert($table_sub_hotel, $columns);

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


        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;

    case 'exp_travel_expense_sub_add_update':

        $file_names    = "";

        $entry_date              = $_POST["entry_date"];
        $expense_type_travel      = $_POST["expense_type_travel"];
        $amount_travel            = $_POST["amount_travel"];
        $sub_description_travel   = $_POST["sub_description_travel"];
        $customer_name           = $_POST["customer_name"];
        $call_no                 = $_POST["call_no"];
        $screen_unique_id        = $_POST["screen_unique_id"];
        $test_file_travel         = $_POST['test_file_travel'];
        $unique_id               = $_POST["unique_id"];

        $update_where               = "";

        $tmp_name = $_FILES["test_file_travel"]["tmp_name"];
        $rand_val = unique_id($prefix);
        $ext = pathinfo($test_file_travel);
        $img1 = $rand_val . '.' . $ext['extension'];

        $target = "../../uploads/expense_creation/" . $img1;

        move_uploaded_file($tmp_name, $target);

        if ($img1) {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type_travel,
                "amount"                     => $amount_travel,
                "description"                => $sub_description_travel,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "file_name"                  => $img1,
                "file_original_name"         => $ext['basename'],
                "unique_id"                  => unique_id($prefix)
            ];
        } else {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type_travel,
                "amount"                     => $amount_travel,
                "description"                => $sub_description_travel,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "unique_id"                  => unique_id($prefix)
            ];
        }

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table_sub_travel, $columns, $update_where);

            // Update Ends
        } else {

            // Insert Begins            
            $action_obj     = $pdo->insert($table_sub_travel, $columns);

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


        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;

    case 'exp_petrol_expense_sub_add_update':

        $file_names    = "";

        $entry_date              = $_POST["entry_date"];
        $expense_type_petrol      = $_POST["expense_type_petrol"];
        $amount_petrol            = $_POST["amount_petrol"];
        $sub_description_petrol   = $_POST["sub_description_petrol"];
        $customer_name           = $_POST["customer_name"];
        $call_no                 = $_POST["call_no"];
        $screen_unique_id        = $_POST["screen_unique_id"];
        $test_file_petrol         = $_POST['test_file_petrol'];
        $travel_type               = $_POST["travel_type"];
        $vehicle_type               = $_POST["vehicle_type"];
        $fuel_type               = $_POST["fuel_type"];
        $rate               = $_POST["rate"];
        $kg_meter               = $_POST["kg_meter"];
        $unique_id               = $_POST["unique_id"];

        $update_where               = "";

        $tmp_name = $_FILES["test_file_petrol"]["tmp_name"];
        $rand_val = unique_id($prefix);
        $ext = pathinfo($test_file_petrol);
        $img1 = $rand_val . '.' . $ext['extension'];

        $target = "../../uploads/expense_creation/" . $img1;

        move_uploaded_file($tmp_name, $target);

        if ($img1) {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type_petrol,
                "amount"                     => $amount_petrol,
                "description"                => $sub_description_petrol,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "file_name"                  => $img1,
                "file_original_name"         => $ext['basename'],
                "travel_type"                => $travel_type,
                "vehicle_type"               => $vehicle_type,
                "fuel_type"                  => $fuel_type,
                "rate"                       => $rate,
                "kg_meter"                   => $kg_meter,
                "unique_id"                  => unique_id($prefix)
            ];
        } else {
            $columns            = [
                "entry_date"                 => $entry_date,
                "expense_type_unique_id"     => $expense_type_petrol,
                "amount"                     => $amount_petrol,
                "description"                => $sub_description_petrol,
                "customer_unique_id"         => $customer_name,
                "call_no"                    => $call_no,
                "screen_unique_id"           => $screen_unique_id,
                "travel_type"                => $travel_type,
                "vehicle_type"               => $vehicle_type,
                "fuel_type"                  => $fuel_type,
                "rate"                       => $rate,
                "kg_meter"                   => $kg_meter,
                "unique_id"                  => unique_id($prefix)
            ];
        }

        // Update Begins
        if ($unique_id) {

            unset($columns['unique_id']);

            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table_sub_petrol, $columns, $update_where);

            // Update Ends
        } else {

            // Insert Begins            
            $action_obj     = $pdo->insert($table_sub_petrol, $columns);

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


        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];

        echo json_encode($json_array);

        break;


    case 'exp_food_daily_expense_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "exp_food_daily_expense_sub";

        // Fetch Data
        // $screen_unique_id = $_POST['screen_unique_id'];
         $unique_id        = $_POST['unique_id'];

        // DataTable 
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $total      = 0;


        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(SELECT expense_type FROM expense_type WHERE expense_type.unique_id = " . $table_sub . ".expense_type_unique_id ) AS expense_type_unique_id",
            "amount",
            "description",
            "file_name",
            "unique_id",
            // "call_no as call_unique_id",
            // "(SELECT customer_name FROM customer_profile WHERE customer_profile.unique_id = " . $table_sub . ".customer_unique_id ) AS customer_unique_id",
            // "(SELECT call_no FROM follow_ups WHERE follow_ups.unique_id = " . $table_sub . ".call_no ) AS call_no",


        ];
        $table_details  = [
            $table_sub . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        // if($unique_id) {
        //     $where          = [
        //         "exp_main_unique_id"    => $unique_id,
        //         "is_active"    => 1,
        //         "is_delete"    => 0
        //     ];
        // } else {
        // $where          = [
        //     "screen_unique_id"    => $unique_id,
        //     "is_active"          => 1,
        //     "is_delete"          => 0
        // ];
        // }
        $where = "exp_main_unique_id = '$unique_id' and is_delete = 0";
        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                // if (($value['customer_unique_id'] != '') && ($value['customer_unique_id'] != 'null')) {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'] . '<br><strong>(' . $value['customer_unique_id'] . ',' . $value['call_no'] . ')</strong>';
                // } else {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'];
                // }

                // $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['file_name']     =  image_view("expense_creation", $value['unique_id'], $value['file_name']);
                $total                +=  $value['amount'];

                $value['unique_id']     = $btn_edit . $btn_delete;
                $data[]                 = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                "count"             => count($res_array),
                "total_amt"         => moneyFormatIndia($total)
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);

        break;


    case 'exp_hotel_expense_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "exp_hotel_expense_sub";

        // Fetch Data
        $screen_unique_id = $_POST['screen_unique_id'];
        $unique_id        = $_POST['unique_id'];

        // DataTable 
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $total      = 0;


        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(SELECT expense_type FROM expense_type WHERE expense_type.unique_id = " . $table_sub_hotel . ".expense_type_unique_id ) AS expense_type_unique_id",
            "amount",
            "description",
            "file_name",
            "unique_id",
            // "call_no as call_unique_id",
            // "(SELECT customer_name FROM customer_profile WHERE customer_profile.unique_id = " . $table_sub_hotel . ".customer_unique_id ) AS customer_unique_id",
            // "(SELECT call_no FROM follow_ups WHERE follow_ups.unique_id = " . $table_sub_hotel . ".call_no ) AS call_no",


        ];
        $table_details  = [
            $table_sub_hotel . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where          = [
            "exp_main_unique_id"    => $unique_id,
            "is_active"          => 1,
            "is_delete"          => 0
        ];

        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                // if (($value['customer_unique_id'] != '') && ($value['customer_unique_id'] != 'null')) {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'] . '<br><strong>(' . $value['customer_unique_id'] . ',' . $value['call_no'] . ')</strong>';
                // } else {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'];
                // }

                // $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['file_name']     =  image_view("expense_creation", $value['unique_id'], $value['file_name']);
                $total                +=  $value['amount'];

                $value['unique_id']     = $btn_edit . $btn_delete;
                $data[]                 = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                "count"             => count($res_array),
                "total_amt"         => moneyFormatIndia($total)
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);

        break;

    case 'exp_travel_expense_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "exp_travel_expense_sub";

        // Fetch Data
        $screen_unique_id = $_POST['screen_unique_id'];
        $unique_id        = $_POST['unique_id'];

        // DataTable 
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $total      = 0;


        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(SELECT expense_type FROM expense_type WHERE expense_type.unique_id = " . $table_sub_travel . ".expense_type_unique_id ) AS expense_type_unique_id",
            "amount",
            "description",
            "file_name",
            "unique_id",
            // "call_no as call_unique_id",
            // "(SELECT customer_name FROM customer_profile WHERE customer_profile.unique_id = " . $table_sub_travel . ".customer_unique_id ) AS customer_unique_id",
            // "(SELECT call_no FROM follow_ups WHERE follow_ups.unique_id = " . $table_sub_travel . ".call_no ) AS call_no",


        ];
        $table_details  = [
            $table_sub_travel . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where          = [
            "exp_main_unique_id"    => $unique_id,
            "is_active"          => 1,
            "is_delete"          => 0
        ];

        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                // if (($value['customer_unique_id'] != '') && ($value['customer_unique_id'] != 'null')) {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'] . '<br><strong>(' . $value['customer_unique_id'] . ',' . $value['call_no'] . ')</strong>';
                // } else {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'];
                // }

                // $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['file_name']     =  image_view("expense_creation", $value['unique_id'], $value['file_name']);
                $total                +=  $value['amount'];

                $value['unique_id']     = $btn_edit . $btn_delete;
                $data[]                 = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                "count"             => count($res_array),
                "total_amt"         => moneyFormatIndia($total)
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);

        break;

    case 'exp_petrol_expense_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "exp_petrol_expense_sub";

        // Fetch Data
        $screen_unique_id = $_POST['screen_unique_id'];
        $unique_id        = $_POST['unique_id'];

        // DataTable 
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $total      = 0;


        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "(SELECT expense_type FROM expense_type WHERE expense_type.unique_id = " . $table_sub_petrol . ".expense_type_unique_id ) AS expense_type_unique_id",
            "amount",
            "description",
            "file_name",
            "unique_id",
            // "call_no as call_unique_id",
            // "(SELECT customer_name FROM customer_profile WHERE customer_profile.unique_id = " . $table_sub_petrol . ".customer_unique_id ) AS customer_unique_id",
            // "(SELECT call_no FROM follow_ups WHERE follow_ups.unique_id = " . $table_sub_petrol . ".call_no ) AS call_no",


        ];
        $table_details  = [
            $table_sub_petrol . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

        $where          = [
            "exp_main_unique_id"    => $unique_id,
            "is_active"          => 1,
            "is_delete"          => 0
        ];

        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                // if (($value['customer_unique_id'] != '') && ($value['customer_unique_id'] != 'null')) {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'] . '<br><strong>(' . $value['customer_unique_id'] . ',' . $value['call_no'] . ')</strong>';
                // } else {
                //     $value['expense_type_unique_id']  = $value['expense_type_unique_id'];
                // }

                // $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['file_name']     =  image_view("expense_creation", $value['unique_id'], $value['file_name']);
                $total                +=  $value['amount'];

                $value['unique_id']     = $btn_edit . $btn_delete;
                $data[]                 = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                "count"             => count($res_array),
                "total_amt"         => moneyFormatIndia($total)
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);

        break;

    // case "exp_food_daily_expense_sub_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];

    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "expense_type_unique_id",
    //         "amount",
    //         "customer_unique_id",
    //         "call_no",
    //         "description",
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_sub,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];

    //     $result         = $pdo->select($table_details, $where);

    //     if ($result->status) {

    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }

    //     echo json_encode($json_array);
    //     break;

    // case "exp_hotel_expense_sub_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];

    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "expense_type_unique_id",
    //         "amount",
    //         "customer_unique_id",
    //         "call_no",
    //         "description",
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_sub_hotel,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];

    //     $result         = $pdo->select($table_details, $where);

    //     if ($result->status) {

    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }

    //     echo json_encode($json_array);
    //     break;

    // case "exp_travel_expense_sub_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];

    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "expense_type_unique_id",
    //         "amount",
    //         "customer_unique_id",
    //         "call_no",
    //         "description",
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_sub_travel,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];

    //     $result         = $pdo->select($table_details, $where);

    //     if ($result->status) {

    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }

    //     echo json_encode($json_array);
    //     break;


    // case "exp_petrol_expense_sub_edit":
    //     // Fetch Data
    //     $unique_id  = $_POST['unique_id'];
    //     $data       = [];

    //     // Query Variables
    //     $json_array     = "";
    //     $columns        = [
    //         "expense_type_unique_id",
    //         "amount",
    //         "customer_unique_id",
    //         "call_no",
    //         "description",
    //         "travel_type",
    //         "vehicle_type",
    //         "fuel_type",
    //         "rate",
    //         "kg_meter",
    //         "unique_id"
    //     ];
    //     $table_details  = [
    //         $table_sub_petrol,
    //         $columns
    //     ];
    //     $where          = [
    //         "unique_id"    => $unique_id,
    //         "is_active"    => 1,
    //         "is_delete"    => 0
    //     ];

    //     $result         = $pdo->select($table_details, $where);

    //     if ($result->status) {

    //         $json_array = [
    //             "data"      => $result->data[0],
    //             "status"    => $result->status,
    //             "sql"       => $result->sql,
    //             "error"     => $result->error,
    //             "testing"   => $result->sql
    //         ];
    //     } else {
    //         print_r($result);
    //     }

    //     echo json_encode($json_array);
    //     break;


    case 'exp_food_daily_expense_sub_delete':

        $unique_id  = $_POST['unique_id'];
        $call_no    = $_POST['call_no'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_sub, $columns, $update_where);

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

    case 'exp_hotel_expense_sub_delete':

        $unique_id  = $_POST['unique_id'];
        $call_no    = $_POST['call_no'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_sub_hotel, $columns, $update_where);

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

    case 'exp_travel_expense_sub_delete':

        $unique_id  = $_POST['unique_id'];
        $call_no    = $_POST['call_no'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_sub_travel, $columns, $update_where);

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

    case 'exp_petrol_expense_sub_delete':

        $unique_id  = $_POST['unique_id'];
        $call_no    = $_POST['call_no'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_sub_petrol, $columns, $update_where);

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


    case "call_no":


        $customer_id       = $_POST["customer_id"];
        $branch_staff_name = $_POST["branch_staff_name"];
        $call_no           = $_POST["call_no"];
        $entry_date        = $_POST["entry_date"];
        $data              = [];

        $select_where   = "b.customer_unique_id = '" . $customer_id . "' AND b.staff_unique_id = '" . $branch_staff_name . "' AND c.entry_date <= '" . $entry_date . "' AND b.is_active = 1 AND b.is_delete =0 ";


        // $select_where = [
        //     "customer_unique_id"        => $customer_id,
        //     "staff_unique_id"           => $branch_staff_name,
        //     "staff_unique_id"           => $branch_staff_name,
        //     "is_active"                 => 1,
        //     "is_delete"                 => 0
        // ];

        $columns       = [
            "GROUP_CONCAT(b.call_no SEPARATOR ',') AS call_no"
        ];

        $group_by   = " b.customer_unique_id  ";

        $table_details = [
            "follow_ups as b join  follow_up_call_travel_expense as c on b.unique_id = c.followup_call_unique_id	",
            $columns
        ];

        $select_result = $pdo->select($table_details, $select_where, '', '', '', '', $group_by);

        //print_r($select_result);
        if ($select_result->status) {
            $status     = $select_result->status;
            $data       = $select_result->data;
            $error      = "";
            $sql        = $select_result->sql;
        }


        $exp_result  = explode(',', $data[0]["call_no"]);

        $imp_result  = implode("','", $exp_result);


        $columns   = [
            "unique_id",
            "call_no"
        ];

        $table_details = [
            "follow_ups",
            $columns
        ];
        $comp_select_where  = "call_no IN ('" . $imp_result . "') AND is_active = 1 AND is_delete = 0  AND follow_up_close_status != '1' ";

        $group_by = " call_no ";

        $result = $pdo->select($table_details, $comp_select_where, '', '', '', '', $group_by);

        if ($result->status) {
            $status     = $result->status;
            $data       = $result->data;
            $error      = "";
            $sql        = $result->sql;

            $call_no_options   = $data;
            echo $call_no_options   = select_option($call_no_options, "Select Call No ", $call_no);

            // print_r($bidders_options);
        } else {
            $status     = $result->status;
            $data       = $result->data;
            $error      = "error";
            $sql        = $result->sql;
        }

        break;

    case "customer_name":


        $customer_id     = $_POST["customer_id"];
        $staff_name     = $_POST["staff_name"];
        $data          = [];


        $select_where = [
            "staff_unique_id"  => $staff_name,
            "is_active"                 => 1,
            "is_delete"                 => 0
        ];

        $columns       = [
            "GROUP_CONCAT(customer_unique_id SEPARATOR ',') AS customer_unique_id"
        ];

        $group_by   = " staff_unique_id  AND customer_unique_id";

        $table_details = [
            "follow_ups",
            $columns
        ];

        $select_result = $pdo->select($table_details, $select_where, '', '', '', '', $group_by);


        if ($select_result->status) {
            $status     = $select_result->status;
            $data       = $select_result->data;
            $error      = "";
            $sql        = $select_result->sql;
        }


        $exp_result  = explode(',', $data[0]["customer_unique_id"]);

        $imp_result  = implode("','", $exp_result);


        $columns   = [
            "unique_id",
            "customer_name"
        ];

        $table_details = [
            "customer_profile",
            $columns
        ];
        $comp_select_where  = "unique_id IN ('" . $imp_result . "') AND is_active = 1 AND is_delete = 0   ";
        $result = $pdo->select($table_details, $comp_select_where);

        if ($result->status) {
            $status     = $result->status;
            $data       = $result->data;
            $error      = "";
            $sql        = $result->sql;

            $customer_name_options   = $data;
            echo $customer_name_options   = select_option($customer_name_options, "Select Customer Name ", $customer_id);

            // print_r($bidders_options);
        } else {
            $status     = $result->status;
            $data       = $result->data;
            $error      = "error";
            $sql        = $result->sql;
        }

        break;

    case 'get_fuel_type':

        $vehicle_type           = $_POST['vehicle_type'];

        $vehicle_type_options  = fuel_type("", $vehicle_type);

        $vehicle_type_options  = select_option($vehicle_type_options, "Select the Fuel Type");

        echo $vehicle_type_options;

        break;

    case 'get_vehicle_type':

        $travel_type           = $_POST['travel_type'];

        $travel_type_options = vehicle_type("", $travel_type);

        $travel_type_options  = select_option($travel_type_options, "Select the vehicle Type");

        echo $travel_type_options;

        break;

    case "get_fuel_type_cost":

        $vehicle_type           = $_POST['vehicle_type'];
        $fuel_type               = $_POST['fuel_type'];
        $data       = [];

        // Query Variables
        $json_array     = "";
        $columns        = [
            "rate as rate_val",

        ];

        $table_details  = [
            "fuel_type_cost_creation",
            $columns
        ];

        $where = "is_delete=0 and is_active=1 and vehicle_type='" . $vehicle_type . "' and unique_id='" . $fuel_type . "' order by unique_id DESC;";
        $result         = $pdo->select($table_details, $where);
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

    default:

        break;
}



function file_upload_status($upload_status = "")
{

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
