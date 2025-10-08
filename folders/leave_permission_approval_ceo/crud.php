<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "leave_details";

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

switch ($action) {
    case 'createupdate':

        $update_where       = "";
        $unique_id          = $_POST['unique_id'];

        $columns            = [
            "ceo_approved"         => $_POST["is_approved"],
            "ceo_approve_by"       => $_SESSION['staff_id'],
            "ceo_approve_time"     => date("H:i:s"),
            "ceo_approved_date"    => date('Y-m-d'),
            "ceo_reject_reason"    => $_POST['rejected_reason'],
        ];

        if ($unique_id) {
            
            $update_where   = [
                "unique_id"     => $unique_id
            ];
            
            // Update Begins
            $action_obj     = $pdo->update($table,$columns,$update_where);
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
            "unique_id",
            "ceo_approved",
            "from_date",
            "leads_approval",
            "hr_approved",
        ];

        $table_details  = [
            $table.", (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
       
        $where = " is_delete = '0' AND ((is_approved = 1 or (leads_approval = 2 and is_approved = 0)) or ceo_name = 'staff5ffa90e8f01ed39207') and cancel_status = 0";

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
        
        //if (isset($_POST['from_date'])) {
            if ($_POST['from_date']) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' ";
            }
        //}

       // if (isset($_POST['to_date'])) {
            if ($_POST['to_date']) {
                $where .= " AND entry_date <= '".$_POST['to_date']."' ";
            }
        //}

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
                $btn_update         = btn_update($folder_name,$value['unique_id']);
                $btn_view           = btn_print($folder_name,$value['unique_id'],'print');
                $btn_delete         = btn_delete($folder_name,$value['unique_id']);
                if ((($value['is_approved'] != "0")||($value['leads_approval'] == 2))&&($value['ceo_approved'] != "0")||($value['hr_approved'] == "1"))) {
                    $value['unique_id'] = $btn_view;
                } else {
                    
                    $value['unique_id'] = $btn_view.$btn_update;
                }

                $text = "";

                switch ($value['ceo_approved']) {
                    case "0":
                        $text = '<span class="text-center text-warning">Pending</span>';
                        break;
                    
                    case "1":
                        $text = '<span class="text-center text-success">Approved</span>';
                        break;

                    case "2":
                        $text = '<span class="text-center text-danger">Rejected</span>';
                        break;

                    default:
                        $text = 'test';
                        break;
                }

                $value['is_approved']   = $text;

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
    
    default:
        
        break;
}

?>