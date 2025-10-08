<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "loan_advance";

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
$prefix             = "lon";
$expense_prefix     = "LAN-";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $loan_type          = $_POST["loan_type"];
        $entry_date         = $_POST["entry_date"];
        $others_type        = $_POST["others_type"];
        $amount             = $_POST["amount"];
        $emi                = $_POST["emi"];
        $emi_type           = $_POST["emi_type"];
        $emi_amount         = $_POST["emi_amount"];
        $description        = $_POST["description"];
        $approval           = $_POST["approval"];
        $unique_id          = $_POST["unique_id"];
        $main_unique_id     = $_POST["unique_id"];
        $loan_percentage    = $_POST["loan_percentage"];

        if($_SESSION['sess_user_type'] == $admin_user_type) {
            $staff_id         = $_POST['staff_id'];
        } else {
            $staff_id         = $_SESSION['staff_id'];
        }


        $update_where       = "";
        
        if($approval == 1){

        $columns            = [
            "loan_type"       => $loan_type,
            "entry_date"      => $entry_date,
            "others_type"     => $others_type,
            "staff_id"        => $staff_id,
            "amount"          => $amount,
            "emi"             => $emi,
            "emi_type"        => $emi_type,
            "emi_amount"      => $emi_amount,
            "description"     => $description,
            "approval"        => $approval,
            "loan_percentage" => $loan_percentage,
            "hod_app_status"  => 1,
            "unique_id"       => $main_unique_id = unique_id($prefix)
        ];

        } else if($approval == 2){
        
        $columns            = [
            "loan_type"       => $loan_type,
            "entry_date"      => $entry_date,
            "others_type"     => $others_type,
            "staff_id"        => $staff_id,
            "amount"          => $amount,
            "emi"             => $emi,
            "emi_type"        => $emi_type,
            "emi_amount"      => $emi_amount,
            "description"     => $description,
            "approval"        => $approval,
            "loan_percentage" => $loan_percentage,
            "ceo_app_status"  => 1,
            "unique_id"       => $main_unique_id = unique_id($prefix)
        ];
        }    
     

        
        
        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'entry_date = "'.$entry_date.'"  AND loan_type = "'.$loan_type.'" AND staff_id = "'.$staff_id.'"  AND is_delete = 0  ';

        // When Update Check without current id
       

            if ($unique_id) {
                $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
                $loan_no = $_POST['loan_no'];

            } else {
                $bill_no_where   = [
                    "acc_year"      => $_SESSION['acc_year']
                ];

                // GET Bill No
                $loan_no             = bill_no($table,$bill_no_where,$expense_prefix);
                $columns['loan_no']  = $loan_no;
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
                

                $update_where       = [
                    "unique_id"     => $unique_id
                ];

                $update_where_cr_dr   = [
                    "form_unique_id"       => $unique_id
                ];

                $action_obj           = $pdo->update($table,$columns,$update_where);
            // Update Ends
            } else {

                // Insert Begins            
                $action_obj           = $pdo->insert($table,$columns);
                
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
            "(SELECT staff_name FROM staff WHERE staff.unique_id = ".$table.".staff_id ) AS staff_id",
            "loan_type",
            "amount",
            "description",
            "unique_id",
            "hod_approval"
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

                $btn_update   = btn_update($folder_name,$value['unique_id']);
                $btn_delete   = btn_delete($folder_name,$value['unique_id']);
                $btn_view     = btn_print($folder_name,$value['unique_id'],'print');

                if($value['hod_approval'] == 0){
                    $value['unique_id'] = $btn_view.$btn_update.$btn_delete;
                }
                else{
                    $value['unique_id'] = $btn_view;
                }

                 
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
    
        case 'user_options':

        $under_user          = $_POST['under_user'];

        $user_name_options  = under_user($under_user);

        $user_name_options  = select_option($user_name_options,"Select");

        echo $user_name_options;
        
        break;


        case 'mobile':

        $staff_id         = $_POST['staff_id'];

        $staff_mobile_no  = staff_name($staff_id);

        echo $staff_mobile_no[0]["office_contact_no"];
        
        break;

        case 'ho_staff_name' :

        echo staff_ho($_POST['staff_id']);
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

                
            }
        } else {
            print_r($staff_result);
        }
    }

    return json_encode([
        "ho_staff_name"        => $staff_name,
        
        
    ]);
}

?>