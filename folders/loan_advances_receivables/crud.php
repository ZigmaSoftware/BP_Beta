<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "loan_receivables";
$table_cr_dr       = "staff_account_debitor_creditor";


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
$prefix             = "recv";
$expense_prefix     = "LAR-";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $receivable_no          = $_POST["receivable_no"];
        $entry_date             = $_POST["entry_date"];
        $loan_no                = $_POST["loan_advance_no"];
        $paid_amount            = $_POST["paid_amount_val"];
        $employee_name          = $_POST["employee_name"];
        $current_payable        = $_POST["current_payable"];
        $unique_id              = $_POST["unique_id"];
        $loan_type_no           = $_POST["loan_type_no"];

        $update_where       = "";

        $columns            = [
            "entry_date"          => $entry_date,
            "employee_name"       => $employee_name,
            "loan_no"             => $loan_no,
            "paid_amount"         => $paid_amount,
            "payable_amount"      => $current_payable,
            "unique_id"           => $main_unique_id = unique_id($prefix)
        ];

        $columns_cr_dr     = [
            "form_unique_id"  => $main_unique_id,
            "loan_unique_id"  => $main_unique_id,
            "entry_date"      => $entry_date,
            "loan_type"       => $loan_type_no,
            "loan_no"         => $receivable_no,
            "employee_name"   => $employee_name,
            "credit_amount"   => $current_payable,
            "unique_id"       => unique_id($prefix)
        ];


        
        
        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'entry_date = "'.$entry_date.'"  AND loan_no = "'.$loan_no.'" AND employee_name = "'.$employee_name.'"  AND is_delete = 0  ';

        // When Update Check without current id
       

        if ($unique_id) {
                $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
                $receivable_no = $_POST['receivable_no'];

            } else {
                $bill_no_where   = [
                    "acc_year"      => $_SESSION['acc_year']
                ];

                // GET Bill No
                $receivable_no                   = bill_no($table,$bill_no_where,$expense_prefix);
                $columns['receivable_no']        = $receivable_no;
                $columns_cr_dr['loan_no']  = $receivable_no;
                // echo $follow_up_call_id;
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
                unset($columns_cr_dr['form_unique_id']);
                unset($columns_cr_dr['loan_unique_id']);
                unset($columns_cr_dr['unique_id']);

                $update_where_cr_dr   = [
                    "form_unique_id"       => $unique_id
                ];

                $action_obj           = $pdo->update($table,$columns,$update_where);
                $action_obj_cr_dr     = $pdo->update($table_cr_dr,$columns_cr_dr,$update_where_cr_dr);
            // Update Ends
            } else {

                // Insert Begins            
                $action_obj           = $pdo->insert($table,$columns);
                $action_obj_cr_dr     = $pdo->insert($table_cr_dr,$columns_cr_dr);
                
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
            "entry_date",
            "(SELECT staff_name FROM staff WHERE staff.unique_id = ".$table.".employee_name ) AS employee_name",
            "(SELECT loan_no FROM loan_advance WHERE loan_advance.unique_id = ".$table.".loan_no ) AS loan_no",
            "paid_amount",
            "payable_amount",
            "unique_id"
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

                $value['entry_date']  = disdate($value['entry_date']);

                

                $btn_update   = btn_update($folder_name,$value['unique_id']);
                $btn_delete   = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id'] = $btn_update.$btn_delete;

                 
                $data[]       = array_values($value);
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

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
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
    
        
    case "loan_advance_no" :

        $staff_name  = $_POST['staff_name'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "unique_id",
            "loan_no",

        ];
        $table_details  = [
            "loan_advance",
            $columns
        ];
        $where          = "is_active = 1 AND is_delete = 0 AND staff_id = '".$staff_name."' AND loan_type != 3 AND accounts_approval = 1";

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
                $status     = $result->status;
                $data       = $result->data;
                $error      = "";
                $sql        = $result->sql;

                $loan_no   = $data;
                echo $loan_no   = select_option($loan_no,"Select");


            }  else {
                $status     = $result->status;
                $data       = $result->data;
                $error      = "error";
                $sql        = $result->sql;
            }  
        break;

    case "designation" :

        $staff_name  = $_POST['staff_name'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "designation_unique_id AS designation_unique_id",
            "(SELECT designation FROM designation_creation as designation JOIN staff ON designation.unique_id = staff.designation_unique_id WHERE staff.unique_id = '".$staff_name."') AS designation_name",
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

        $result         = $pdo->select($table_details,$where);

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

    case "loan_advance_type" :

        $loan_advance_no  = $_POST['loan_advance_no'];
        $data             = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "loan_type",
            "emi_type",
            "amount"
        ];
        $table_details  = [
            "loan_advance",
            $columns
        ];
        $where          = [
            "unique_id"    => $loan_advance_no,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"   => $result->sql,

            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case "emi_amt_mnth" :
        $loan_advance_no  = $_POST['loan_advance_no'];
        $data             = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "emi",
            "emi_amount",
            "loan_percentage",

        ];
        $table_details  = [
            "loan_advance",
            $columns
        ];
        $where          = [
            "unique_id"    => $loan_advance_no,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

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

    case "loan_sub_datatable" :
        $loan_advance_no  = $_POST['loan_advance_no'];
        $data             = [];
        
        // DataTable 
        $search     = $_POST['search']['value'];    
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        $total      = 0;
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            
            "entry_date",
            "loan_no",
            "debit_amount",
            "credit_amount",
        ];

        $table_details  = [
            $table_cr_dr." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

            $where          = [
                "loan_unique_id"     => $loan_advance_no,
                "is_active"          => 1,
                "is_delete"          => 0
            ];
       
        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
               
                $value['entry_date']    = disdate($value['entry_date']);
                $data[]                 = array_values($value);
            }
            
            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql,
                //"count"             => count($res_array),
                //"total_amt"         => moneyFormatIndia($total)
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        
        break;

    default:
        
        break;
}

?>