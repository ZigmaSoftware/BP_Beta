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
    ],
];

switch ($action) {
    case 'createupdate':

        $update_where       = "";
        $unique_id          = $_POST['unique_id'];
        $leave_days         = $_POST['leave_days'];
        if(($_POST["day_type_opt"] == 1)||($_POST['day_type_opt'] == 2)){
            $leave_type         = implode(",",$_POST["leave_type"]);
            $half_leave_type    = implode(",",$_POST["half_leave_type"]);
        }else{
            $leave_type         = 0;
            $half_leave_type  = "";
        }

         
        if($_POST['hr_approved_date'] == ''){
            $_POST['hr_approved_date'] = date('Y-m-d');
        }
        if($_POST['hr_cancel_date'] == ''){
            $_POST['hr_cancel_date'] = date('Y-m-d');
        }

        $columns            = [
            "hr_approved"        => $_POST["is_approved"],
            "leave_type"         => $leave_type,
            "half_leave_type"    => $half_leave_type,
            "hr_reason"          => $_POST["hr_reason"],
            "hr_approved_by"     => $_SESSION['staff_id'],
            "hr_approved_date"   => $_POST['hr_approved_date'],
            "hr_approved_time"   => date('H:i:s'),
            "hr_cancel_date"     => $_POST['hr_cancel_date'],
            "hr_cancel_reason"   => $_POST['hr_cancel_reason'],
        ];

        if ($unique_id) {
            
            $update_where   = [
                "unique_id"     => $unique_id
            ];
            
            // Update Begins
            $action_obj     = $pdo->update($table,$columns,$update_where);

            for ($i = 1; $i <= $leave_days; $i++) {


                $columns    = [
                    "staff_id",
                    "day_type",
                    "from_date",
                    "to_date",
                    "from_time",
                    "to_time",
                    "permission_hours",
                    "half_day_type",
                    "leave_type",
                    "leave_days",
                    "reason",
                    "is_approved",
                    "on_duty_type",
                    "on_duty_from_date",
                    "on_duty_to_date",
                    "on_duty_leave_days",
                    "onduty_half_day_type",
                    "approve_by",
                    "hod_reject_reason",
                    "ceo_approved",
                    "ceo_reject_reason",
                    "hr_approved",
                    "hr_reason",
                    "hr_cancel_reason",
                    "hr_approved_date",
                    "hr_cancel_date",
                    "entry_date",
                    "approved_date",
                    "ceo_to_be_approved",
                    "ceo_approve_by",
                    "ceo_approved_date",
                    "hr_approved_by",
                    "leads_approval",
                    "ceo_name"

                ];

                $table_details   = [
                    $table,
                    $columns
                ];
                $where  = [
                    "unique_id"     => $unique_id
                ]; 

                $result_values  = $pdo->select($table_details,$where);
                    if ($result_values->status) {

                        $result_values      = $result_values->data[0];

                        $staff_id                   = $result_values["staff_id"];
                        $day_type                   = $result_values["day_type"];
                        $from_date                  = $result_values["from_date"];
                        $to_date                    = $result_values["to_date"];
                        $from_time                  = $result_values["from_time"];
                        $to_time                    = $result_values["to_time"];
                        $permission_time            = $result_values["permission_hours"];
                        $half_date                  = $result_values["from_date"];
                        $permission_date            = $result_values["from_date"];
                        $half_day_type              = $result_values["half_day_type"];
                        $leave_type_text            = $result_values["leave_type"];
                        $leave_days                 = $result_values["leave_days"];
                        $reason                     = $result_values["reason"];
                        $approve                    = $result_values["is_approved"];
                        $on_duty_type               = $result_values["on_duty_type"];
                        $on_duty_from_date          = $result_values["on_duty_from_date"];
                        $on_duty_half_date          = $result_values["on_duty_from_date"];
                        $on_duty_to_date            = $result_values["on_duty_to_date"];
                        $on_duty_leave_days         = $result_values["on_duty_leave_days"];
                        $on_duty_half_day_type      = $result_values["onduty_half_day_type"];
                        $ho_approved                = $result_values["approve_by"];
                        $rejected_reason            = $result_values["hod_reject_reason"];
                        $ceo_approved               = $result_values["ceo_approved"];
                        $ceo_rejected_reason        = $result_values["ceo_reject_reason"];
                        $hr_approve                 = $result_values["hr_approved"];
                        $hr_reason                  = $result_values["hr_reason"];
                        $hr_cancel_reason           = $result_values["hr_cancel_reason"];
                        $hr_approved_date           = $result_values["hr_approved_date"];
                        $hr_cancel_date             = $result_values["hr_cancel_date"];
                        $entry_date                 = $result_values["entry_date"];
                        $approved_date              = $result_values["approved_date"];
                        $ceo_to_be_approved         = $result_values["ceo_to_be_approved"];
                        $ceo_approve_by             = $result_values["ceo_approve_by"];
                        $ceo_approved_date          = $result_values["ceo_approved_date"];
                        $hr_approved_by             = $result_values["hr_approved_by"];
                        $leads_approval             = $result_values["leads_approval"];
                        $ceo_name                   = $result_values["ceo_name"];

                        if($_POST['check_box_value'][$i-1] != 0){
                            $half_leave_type  = $_POST['half_leave_type'][$i-1];

                            if(($_POST['leave_type'][$i-1] == 4) ||($_POST['leave_type'][$i-1] == 10)){
                                $comp_off_date  = $_POST['comp_off_date'][$i-1];
                            }else{
                                $comp_off_date  = "";
                            }

                            if($_POST['half_leave_type'][$i-1] == 10){
                                $comp_off_date_half  = $_POST['comp_off_date_half'][$i-1];
                            }else{
                                $comp_off_date_half  = "";
                            }
                        }else{
                            $half_leave_type  = "";
                            if(($_POST['leave_type'][$i-1] == 4) ||($_POST['leave_type'][$i-1] == 10)){
                                $comp_off_date  = $_POST['comp_off_date'][$i-1];
                            }else{
                                $comp_off_date  = "";
                            }

                            if($_POST['half_leave_type'][$i-1] == 10){
                                $comp_off_date_half  = $_POST['comp_off_date_half'][$i-1];
                            }else{
                                $comp_off_date_half  = "";
                            }
                        }


                    
                        $columns_sub            = [
                            
                            "form_unique_id"        => $unique_id,
                            "staff_id"              => $staff_id,
                            "day_type"              => $day_type,
                            "from_date"             => $_POST['entry_date_sub'][$i-1],
                            "to_date"               => $_POST['entry_date_sub'][$i-1],
                            "from_time"             => $from_time,
                            "to_time"               => $to_time,
                            "permission_hours"      => $permission_time,
                            "half_day_type"         => $half_day_type,
                            "leave_type"            => $_POST['leave_type'][$i-1],
                            "half_leave_type"       => $half_leave_type,
                            "leave_days"            => $leave_days,
                            "reason"                => $reason,
                            "is_approved"           => $approve,
                            "on_duty_type"          => $on_duty_type,
                            "on_duty_from_date"     => $on_duty_from_date,
                            "on_duty_to_date"       => $on_duty_to_date,
                            "on_duty_leave_days"    => $on_duty_leave_days,
                            "onduty_half_day_type"  => $on_duty_half_day_type,
                            "approve_by"            => $ho_approved,
                            "hod_reject_reason"     => $rejected_reason,
                            "ceo_approved"          => $ceo_approved,
                            "ceo_reject_reason"     => $ceo_rejected_reason,
                            "comp_off_date"         => $comp_off_date,
                            "comp_off_date_half"    => $comp_off_date_half,
                            "hr_approved"           => $hr_approve,
                            "hr_reason"             => $hr_reason,
                            "hr_cancel_reason"      => $hr_cancel_reason,
                            "hr_approved_date"      => $hr_approved_date,
                            "hr_cancel_date"        => $hr_cancel_date,
                            "entry_date"            => $entry_date,
                            "approved_date"         => $approved_date,
                            "ceo_to_be_approved"    => $ceo_to_be_approved,
                            "ceo_approve_by"        => $ceo_approve_by,
                            "ceo_approved_date"     => $ceo_approved_date,
                            "hr_approved_by"        => $hr_approved_by,
                            "leads_approval"        => $leads_approval,
                            "ceo_name"              => $ceo_name,
                            
                        ];

                        $where_sub  = [
                            "unique_id"     => $_POST['unique_id_sub'][$i-1]
                        ]; 

                        if($_POST['unique_id_sub'][$i-1] == ''){
                            $columns_sub['unique_id']  = unique_id($prefix);
                            $action_obj     = $pdo->insert("leave_details_sub",$columns_sub);
                        }else{
                            $action_obj     = $pdo->update("leave_details_sub",$columns_sub,$where_sub);
                        }
                       // print_r($action_obj);
                    }
                }



            // Update Ends

        } else {

            $columns['entry_date'] = $today;
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
            "entry_date",
            "staff_id",
            "day_type",
            "reason",
            "is_approved",
            "unique_id",
            "hr_approved",
            "from_date",
        ];

        $table_details  = [
            $table.", (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
       
       // $where = " c.is_delete = '0' AND (((c.is_approved = 2 OR b.is_team_head = 1) AND ceo_approved = 1) OR (is_approved = 1))";

        //$where = " is_delete = '0' AND  ((is_approved = 2) or (is_approved = 0 and ceo_approved = 1)  or (is_approved = 1 and ceo_approved = 1) ) AND cancel_status = 0";

        $where = " is_delete = '0' AND cancel_status = 0";
        

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        // $order_by       = datatable_sorting($order_column,$order_dir,$columns);
        $order_by       = " hr_approved ASC,entry_date DESC ";


        // Datatable Searching
       // $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
            if ($_POST['from_date']) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' ";
            }
            if ($_POST['to_date']) {
                $where .= " AND entry_date <= '".$_POST['to_date']."' ";
            }
        $where .= " group by entry_date,staff_id ";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                // Staff Details
                $staff_details       = staff_name($value['staff_id'])[0]['staff_name'];
                $value['staff_id']   = $staff_details;

                $value['day_type']   = $day_type_options[$value['day_type']-1]['text'];

                $value['entry_date'] = disdate($value['entry_date']);

                // if ($value['hr_approved'] != "0") {
                //     $value['unique_id'] = "";
                // } else {
                    $btn_update         = btn_update($folder_name,$value['unique_id']);
                    $btn_delete         = btn_delete($folder_name,$value['unique_id']);
                    $btn_view           = btn_print($folder_name,$value['unique_id'],'print');

                    $value['unique_id'] = $btn_view.$btn_update;
                //}

                $text = "";

                switch ($value['hr_approved']) {
                    case "0":
                        $text = '<span class="text-center text-warning">Pending</span>';
                        break;
                    
                    case "1":
                        $text = '<span class="text-center text-success">Approved</span>';
                        break;

                    case "2":
                        $text = '<span class="text-center text-danger">Cancelled</span>';
                        break;

                    default:
                        $text = '';
                        break;
                }

                $value['is_approved']   = $text;

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
    
    case 'cl_type_count':
        $staff_id            = $_POST["staff_id"];
        $leave_type          = $_POST["leave_type"];
        $entry_date          = $_POST["entry_date"];
        $sub_unique_id       = $_POST["sub_unique_id"];

        $date_exp = explode('-', $entry_date);
        $month_year = $date_exp[0]."-".$date_exp[1];
        
        // Funnel Product Update in Same Function Use
        
        // check already Exist Or not
        $table_details      = [
            "view_cl_half_count",
            [
               // "COUNT(unique_id) AS count"

                "(select (sum(leave_type)/2)  from view_cl_half_count where type_cl != 'leave_type_two') as count_half",
                "(select sum(leave_type)  from view_cl_half_count where type_cl = 'leave_type_two') as count_full",
            ]
        ];
        $select_where       = 'staff_id ="'.$staff_id.'" AND date = "'.$month_year.'"';

        // When Update Check without current id
        

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
        
        $count = $data[0]["count_half"] + $data[0]["count_full"];

        if ($count >= 1) {
            $msg        = "already";
        }else { 
        //else if (($data[0]["count"] == 0) && ($msg != "error")) {
            $msg   = "";
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

    case 'sunday_holiday_date':
        for($i = $_POST['min_month']; $i <= $_POST['current_month']; $i++){
            echo sunday_holiday_date($i,$_POST['year'],$_POST['staff_id']).get_holiday_date($i,$_POST['year'],$_POST['staff_id'])."<br>";
        }
        break;


    default:
        
        break;
}

?>