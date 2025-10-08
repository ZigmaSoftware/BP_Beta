<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "loan_advance";
$table_staff       = "staff_account_debitor_creditor";

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
$prefix             = "loan";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose



switch ($action) {
    case 'createupdate':

        $update_where       = "";
        $unique_id          = $_POST['unique_id'];
        $is_approved        = $_POST['is_approved'];

       
        $columns            = [
            "accounts_approval"     => $_POST["is_approved"],
            "accounts_approve_by"   => $_SESSION['staff_id'],
            "accounts_app_reason"   => $_POST['accounts_app_reason'],
            "accounts_approve_date" => date('Y-m-d'),
            "payment_type"          => $_POST['payment_type'],
            "bank_name"             => $_POST['bank_name'],
            "account_no"            => $_POST['account_no'],
            "ifsc_code"             => $_POST['ifsc_code'],
            "branch_name"           => $_POST['branch_name'],
            "upi_id"                => $_POST['upi_id'],
            "cheque_no"             => $_POST['cheque_no']
        ];


        $columns_accounts           = [
            "form_unique_id"        => $unique_id,
            "loan_type"             => $_POST['loan_type'],
            "loan_unique_id"        => $unique_id,
            "loan_no"               => $_POST['loan_no'],
            "entry_date"            => date('Y-m-d'),
            "employee_name"         => $_POST['staff_id'],
            "debit_amount"          => $_POST['amount'],
            "unique_id"             => unique_id($prefix)
        ];




        

        

        if ($unique_id) {
            
            $update_where   = [
                "unique_id"     => $unique_id
            ];
            
            // Update Begins
            $action_obj     = $pdo->update($table,$columns,$update_where);
            if($is_approved == 1){
            $action_obj_acc = $pdo->insert($table_staff,$columns_accounts);
            }
            // Update Ends

        } else {

            $columns['entry_date'] = $today;
            $columns['unique_id']  = unique_id($prefix);

            // Insert Begins
            $action_obj         = $pdo->insert($table,$columns);
            if($is_approved == 1){
            $action_obj_acc = $pdo->insert($table_staff,$columns_accounts);
            }
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
            "c.entry_date",
            "c.staff_id",
            "c.loan_type",
            "c.amount",
            "c.description",
            "c.accounts_approval",
            "c.unique_id"
        ];
        if ($_SESSION['sess_user_type'] == $admin_user_type) {

            $table_details  = [
                $table." as c, (SELECT @a:= ".$start.") AS a ",
                $columns
            ];
        }else{
            $table_details  = [
                $table." as c join staff as b on c.staff_id = b.unique_id, (SELECT @a:= ".$start.") AS a ",
                $columns
            ];
        }

        if ($_SESSION['sess_user_type'] != $admin_user_type) {
            $where = "c.is_delete = '0' AND  b.reporting_officer = '".$_SESSION['staff_id']."' AND c.accounts_app_status = 1";
        }else{
            $where = " c.is_delete = '0' AND c.accounts_app_status = 1 ";
        }
         //if (isset($_POST['from_date'])) {
            if ($_POST['from_date']) {
                $where .= " AND c.entry_date >= '".$_POST['from_date']."' ";
            }
        //}

       // if (isset($_POST['to_date'])) {
            if ($_POST['to_date']) {
                $where .= " AND c.entry_date <= '".$_POST['to_date']."' ";
            }
        //}

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

                // Staff Details

                $staff_details       = staff_name($value['staff_id'])[0]['staff_name'];
                $value['staff_id']   = $staff_details;

                $btn_update         = btn_update($folder_name,$value['unique_id']);
                $btn_delete         = btn_delete($folder_name,$value['unique_id']);
                $btn_view           = btn_print($folder_name,$value['unique_id'],'print');

                switch($value['loan_type']){
                    case 1:
                        $value['loan_type'] = "Loan";
                        break;
                    case 2:
                        $value['loan_type'] = "Advance";
                        break;
                    case 3:
                        $value['loan_type'] = "Others";
                        break;
                }


                $value['entry_date'] = disdate($value['entry_date']);

                if ($value['accounts_approval'] != "0") {
                    $value['unique_id'] = $btn_view;
                } else {
                    
                    $value['unique_id'] = $btn_view.$btn_update;
                }

                $text = "";

                switch ($value['accounts_approval']) {
                    case "0":
                        $text = '<span class="text-center text-warning">Pending</span>';
                        break;
                    case "1":
                        $text = '<span class="text-center text-success">Approved</span>';
                        break;
                    case "2":
                        $text = '<span class="text-center text-success">Rejected</span>';
                        break;    
                    default:
                        $text = 'test';
                        break;
                }

                $value['accounts_approval']   = $text;

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