<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "leave_details";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
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

$leave              = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "lve";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$day_type_options   = [
    
    [
        "id"    => 1,
        "text"  => "Full Day"
    ],
    [
        "id"    => 2,
        "text"  => "Half Day"
    ],
    [
        "id"    => 3,
        "text"  => "Work From Home"
    ],
    [
        "id"    => 4,
        "text"  => "Idle"
    ],
    [
        "id"    => 5,
        "text"  => "On-Duty"
    ],
    [
        "id"    => 6,
        "text"  => "Permission"
    ]
];

$leave_type_options    = [
    [
        "id"    => 1,
        "text"  => "EL"
    ],
    [
        "id"    => 2,
        "text"  => "CL"
    ],
    [
        "id"    => 3,
        "text"  => "SL"
    ],
    [
        "id"    => 4,
        "text"  => "Comp Off"
    ],
    [
        "id"    => 5,
        "text"  => "SPL Leave"
    ]
];
switch ($action) {
    case 'createupdate':

        $update_where       = "";
        $unique_id          = $_POST['unique_id'];
        if(($_POST['is_approved'] == 0)&&($_POST['is_lead_approved'] == 0)){
            if ($_POST["day_type"] == 2) {
                $_POST["from_date"] = $_POST["half_date"];
            } else if ($_POST["day_type"] == 6) {
                $_POST["from_date"] = $_POST["permission_date"];
            }

            if($_POST["day_type"] == 5){
                if($_POST["on_duty_type"] == 2){
                    $_POST["on_duty_from_date"] = $_POST["on_duty_half_date"];
                    $_POST["half_day_type"]     = $_POST["onduty_half_day_type"];

                    $_POST["from_date"] = $_POST["on_duty_half_date"];
                    
                }else {
                    $_POST["from_date"]    = $_POST["on_duty_from_date"];
                    $_POST["to_date"]      = $_POST["on_duty_to_date"]; 
                    $_POST["leave_days"]   = $_POST["on_duty_leave_days"];
                }
            }
        }
        if($_SESSION['sess_user_type'] == $admin_user_type) {
            $staff_id         = $_POST['staff_id'];
        } else {
            $staff_id         = $_SESSION['staff_id'];
        }

        if(($_POST['is_approved'] == 0)&&($_POST['is_lead_approved'] == 0)){
            $columns            = [
                "staff_id"              => $staff_id,
                "day_type"              => $_POST["day_type"],
                "from_date"             => $_POST["from_date"],
                "to_date"               => $_POST["to_date"],
                "from_time"             => $_POST["from_time"],
                "to_time"               => $_POST["to_time"],
                "half_day_type"         => $_POST["half_day_type"],
                "permission_hours"      => $_POST["permission_time"],
                "leave_type"            => $_POST["leave_type"],
                "leave_days"            => $_POST["leave_days"],
                "reason"                => $_POST["reason"],
                "on_duty_type"          => $_POST["on_duty_type"],
                "on_duty_from_date"     => $_POST["on_duty_from_date"],
                "on_duty_to_date"       => $_POST["on_duty_to_date"],
                "on_duty_leave_days"    => $_POST["on_duty_leave_days"],
                "onduty_half_day_type"  => $_POST["onduty_half_day_type"],
                // "leads_approval"        => $_POST["leads_approved"],
                "ceo_name"              => $_POST["ho_name"],
                "req_from"              => 'Web',
            ];
        }else{
            $columns            = [
                "cancel_status"       => $_POST['cancel_status'],
            ];
        }

        // check already Exist Or not
        $table_details      = [
            "leave_details_sub",
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        //$select_where       = 'is_delete = 0 AND staff_id = "'.$staff_id.'" AND (from_date >= "'.$_POST["from_date"].'" OR to_date >= "'.$_POST["from_date"].'") AND cancel_status = 0 AND hr_approved != 2 ';

        $select_where       = 'is_delete = 0 AND staff_id = "'.$staff_id.'" AND (from_date = "'.$_POST["from_date"].'") AND cancel_status = 0 AND hr_approved != 2 ';

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

            if ($unique_id) {
                
                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $update_sub   = [
                    "form_unique_id" => $unique_id
                ];
                
                // Update Begins
                $action_obj         = $pdo->update($table,$columns,$update_where);
                $action_obj_sub     = $pdo->update("leave_details_sub",$columns,$update_sub);
                // Update Ends

            } else {

                $columns['entry_date'] = $today;
                $columns['entry_time'] = date('H:i:s');
                $columns['unique_id']  = unique_id($prefix);

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
            "entry_date",
            "staff_id",
            "day_type",
            "reason",
            "is_approved",
            "hod_reject_reason",
            "ceo_approved",
            "ceo_reject_reason",
            "hr_approved",
            "hr_reason",
            //"leave_type",
            "unique_id",
            "cancel_status",
            "leads_approval"
            
        ];

        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
       
        if ($_SESSION['sess_user_type'] != $admin_user_type) {
            $where = "is_delete = '0' AND staff_id = '".$_SESSION['staff_id']."'";
        }else{
            $where = " is_delete = '0' ";
        }

        if (isset($_POST['from_date'])) {
            if ($_POST['from_date']) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' ";
            }
        }

        if (isset($_POST['to_date'])) {
            if ($_POST['to_date']) {
                $where .= " AND entry_date <= '".$_POST['to_date']."' ";
            }
        }

        $where .= " group by entry_date,staff_id";

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
            $leave_type = '';

            foreach ($res_array as $key => $value) {

                    // $leave_exp = explode(',', $value['leave_type']);
                    // foreach($leave_exp as $leave_key_call => $leave_type){
                    //    // print_r($leave_key_call);
                    //     if($leave_key_call == 1){
                    //        $leave_type .= "EL"; 
                    //     }elseif($leave_key_call == 2){
                    //         $leave_type .= "CL";    
                    //     }elseif($leave_key_call == 3){
                    //         $leave_type .= "SL";    
                    //     }elseif($leave_key_call == 4){
                    //         $leave_type .= "Comp Off";    
                    //     }elseif($leave_key_call == 5){
                    //         $leave_type .= "SPL Leave";    
                    //     }
                    // }


                // switch($value['leave_type']){
                //     case 1:
                //         $leave_type = "EL";
                //         break;
                //     case 2:
                //         $leave_type = "CL";
                //         break;
                //     case 3:
                //         $leave_type = "SL";
                //         break;
                //     case 4:
                //         $leave_type = "Comp Off";
                //         break;
                //     case 5:
                //         $leave_type = "SPL Leave";
                //         break;
                //     default :
                //         $leave_type = "";
                //         break;
                // }
                // Staff Details
                $staff_details       = staff_name($value['staff_id'])[0]['staff_name'];
                $value['staff_id']   = $staff_details;

                $btn_update         = btn_update($folder_name,$value['unique_id']);
                $btn_delete         = btn_delete($folder_name,$value['unique_id']);
                $btn_view           = btn_print($folder_name,$value['unique_id'],'print');

                if(((($value['is_approved'] != 0)||($value['ceo_approved'] != 0))&&($value['hr_approved'] == 0)&&($value['cancel_status'] != 1))||(($value['leads_approval'] != 0)||($value['ceo_approved'] != 0))&&(($value['hr_approved'] == 0)&&($value['cancel_status'] != 1))){
                    $value['unique_id'] = $btn_view.$btn_update;
                }else if($value['cancel_status'] == 1){
                     $text = '<span class="text-center text-danger">Cancelled</span>';
                     $value['unique_id'] = $text;
                }else if(($value['is_approved'] == 0)&&($value['leads_approval'] == 0)){
                    $value['unique_id'] = $btn_view.$btn_update.$btn_delete;
                }
                else{
                    $value['unique_id'] = $btn_view;
                }
                $ho_approved = $value['is_approved'];
                $value['day_type']     = $day_type_options[$value['day_type']-1]['text'];
                //$value['leave_type']   = $leave_type;

                $value['is_approved']      = btn_ho_approve_status_leave($folder_name,$value['unique_id'],$value['is_approved'],$value['leads_approval']);
                $value['ceo_approved']     = btn_ceo_approve_status_leave($folder_name,$value['unique_id'],$value['ceo_approved'],$ho_approved);
                $value['hr_approved']      = btn_hr_approve_status_leave($folder_name,$value['unique_id'],$value['hr_approved'],$value['ceo_approved'],$ho_approved);

                $value['entry_date'] = disdate($value['entry_date']);

                

                

                $data[]             = array_values($value);
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
    case 'ho_staff_name' :

        echo staff_ho($_POST['staff_id']);
        break;

    case 'staff_designation' :

        echo staff_designation($_POST['staff_id']);
        break;

    case "mail":
        $staff_name = staff_name($_POST['staff_id']);
       
        $subject  = "Notification for Approval!!!";
        $body     = " \n This is Remainder Mail for Approval in Ascent CRM. Mr/Ms.".$staff_name[0]['staff_name']." has requested for leave.";
        //$headers  = "software@ascentedigit.com";
        $headers = "";
        $ho_name    = $_POST['ho_name'];
    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "office_email_id AS to_mail",
       
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'unique_id = "'.$ho_name.'"';
    

    $staff_count = $pdo->select($table_details, $where);

    if (!($staff_count->status)) {

        print_r($staff_count);

    } else {

        $staff_count  = $staff_count->data[0];

       // $to_email     = $staff_count['to_mail'];
        $to_email     = 'praveenas2410@gmail.com';
        if(mail($to_email, $subject, $body, $headers)){
            echo "Email sent successfully";
        }else{
            echo "Sorry, failed while sending mail!";
        }
    }
    //$to_email     = 'hrd@ascentedigit.com';
    $to_email     = 'praveenas2410@gmail.com';
   
        if(mail($to_email, $subject, $body, $headers)){
            echo "Email sent successfully";
        }else{
            echo "Sorry, failed while sending mail!";
        }
        break;

        case 'leave_apply':


        
            if ($_POST["day_type"] == 2) 
            {
                $_POST["from_date"] = $_POST["half_date"];
            } 
            else if ($_POST["day_type"] == 6) 
            {
                $_POST["from_date"] = $_POST["permission_date"];
            }

            if($_POST["day_type"] == 5)
            {
                if($_POST["on_duty_type"] == 2)
                {
                    $_POST["on_duty_from_date"] = $_POST["on_duty_half_date"];
                    $_POST["half_day_type"]     = $_POST["onduty_half_day_type"];
                    $_POST["from_date"] = $_POST["on_duty_half_date"];
                }
                else 
                {
                    $_POST["from_date"]    = $_POST["on_duty_from_date"];
                    $_POST["to_date"]      = $_POST["on_duty_to_date"]; 
                    $_POST["leave_days"]   = $_POST["on_duty_leave_days"];
                }
            }
        
        
            $columns            = [
                "staff_id"              => $_POST["staff_id"],
                "day_type"              => $_POST["day_type"],
                "from_date"             => $_POST["from_date"],
                "to_date"               => $_POST["to_date"],
                "from_time"             => $_POST["from_time"],
                "to_time"               => $_POST["to_time"],
                "half_day_type"         => $_POST["half_day_type"],
                "permission_hours"      => $_POST["permission_hours"],
                "leave_type"            => $_POST["leave_type"],
                "leave_days"            => $_POST["leave_days"],
                "reason"                => $_POST["reason"],
                "on_duty_type"          => $_POST["on_duty_type"],
                "on_duty_from_date"     => $_POST["on_duty_from_date"],
                "on_duty_to_date"       => $_POST["on_duty_to_date"],
                "on_duty_leave_days"    => $_POST["on_duty_leave_days"],
                "onduty_half_day_type"  => $_POST["onduty_half_day_type"],
                "req_from"              => 'Android App',
                "ceo_name"              => $_POST["ho_name"],
            ];
       
        // check already Exist Or not
        $table_details      = [
            "leave_details",
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        //$select_where       = 'is_delete = 0 AND staff_id = "'.$staff_id.'" AND (from_date >= "'.$_POST["from_date"].'" OR to_date >= "'.$_POST["from_date"].'") AND cancel_status = 0 AND hr_approved != 2 ';

        $select_where       = 'is_delete = 0 AND staff_id = "'.$staff_id.'" AND from_date = "'.$_POST["from_date"].'" AND cancel_status = 0 AND hr_approved = 0 ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);
//print_r($action_obj);
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

            if ($unique_id) {
                
                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $update_sub   = [
                    "form_unique_id" => $unique_id
                ];
                
                // Update Begins
                $action_obj         = $pdo->update($table,$columns,$update_where);
                $action_obj_sub     = $pdo->update("leave_details_sub",$columns,$update_sub);
                // Update Ends

            } else {
               

                $columns['entry_date'] = $today;
                $columns['unique_id']  = unique_id($prefix);
 // Insert Begins
                $action_obj     = $pdo->insert($table,$columns);
                // Insert Ends
                // $staff_name = staff_name($_POST['staff_id']);
       
                // $subject  = "Notification for Approval!!!";
                // $body     = " \n This is Remainder Mail for Approval in Ascent CRM. Mr/Ms.".$staff_name[0]['staff_name']." has requested for leave.";
                // // $headers  = "software@ascentedigit.com";
                // $headers = "";
                // $ho_name    = $_POST['ho_name'];
                // $table_name    = "staff";
                // $where         = [];
                // $table_columns = [
                //     "office_email_id AS to_mail",
                   
                // ];

                // $table_details = [
                //     $table_name,
                //     $table_columns
                // ];

                // $where  = 'unique_id = "'.$ho_name.'"';
                

                // $staff_count = $pdo->select($table_details, $where);

                // if (!($staff_count->status)) {

                //     // print_r($staff_count);

                // } else {

                //     $staff_count  = $staff_count->data[0];
                //     // $to_email     = $staff_count['to_mail'];
                //     $to_email     = 'praveenas2410@gmail.com';
                //     //mail('kkarthikeyan716@gmail.com', 'test Sub', 'Hai');
                //     if(mail($to_email, $subject, $body)){
                //         //echo "Email sent successfully";
                //     }else{
                //        // echo "Sorry, failed while sending mail!";
                //     }
                // }
                // $to_email     = 'hrd@ascentedigit.com';
               // $to_email     = 'praveenas2410@gmail.com';
               // // mail('kkarthikeyan716@gmail.com', 'test Sub', 'hello');
               //      if(mail($to_email, $subject, $body)){
               //         // echo "Email sent successfully";
               //      }else{
               //         // echo "Sorry, failed while sending mail!";
               //      }

                
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


    default:
        
        break;
}
function staff_ho($staff_id = "") {
    global $pdo;

    $staff_name = '';
    
    if ($staff_id) {
        $staff_query  = "SELECT reporting_officer as report_id FROM staff WHERE  is_delete = 0 and unique_id = '".$staff_id."' and reporting_officer != ''";

        $staff_result = $pdo->query($staff_query);
        
        if ($staff_result->status) {
            if (!empty($staff_result->data)) {
                $staff_result     = $staff_result->data[0];

                $staff_details    = staff_name($staff_result['report_id']);
  
                $staff_name       = $staff_details[0]['staff_name'];
                $unique_id        = $staff_details[0]['unique_id'];

                
            }
        } else {
            print_r($staff_result);
        }
    }

    return json_encode([
        "ho_staff_name"        => $staff_name,
        "unique_id"        => $unique_id,
    ]);
}

function staff_designation($staff_id = "") {
    global $pdo;

    $staff_name = '';
    
    if ($staff_id) {
        $designation_query  = "SELECT designation_unique_id as designation FROM staff WHERE  is_delete = 0 and unique_id = '".$staff_id."'";

        $designation_result = $pdo->query($designation_query);
        
        if ($designation_result->status) {
            if (!empty($designation_result->data)) {
                $designation_result     = $designation_result->data[0];

                $designation_details    = designation($designation_result['designation']);
  
                $designation             = $designation_details[0]['unique_id'];

                
            }
        } else {
            print_r($designation_result);
        }
    }

    return json_encode([
        "designation"        => $designation,
    ]);
}
?>