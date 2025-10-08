<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "offer_letter";

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

$offer_letter   = "";
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

        $unique_id          = $_POST['unique_id'];

        $columns            = [
            "letter_date"               => $_POST["letter_date"],
            "name"                      => $_POST["staff_name"],
            "address"                   => $_POST["staff_address"],
            "company_name"              => $_POST["company_name"],
            "designation"               => $_POST["designation"],
            "location"                  => $_POST["location"],
            "join_date"                 => $_POST["join_date"],
            "ctc"                       => $_POST["ctc"],
            "gross_salary"              => $_POST["gross_salary"],
            "gender"                    => $_POST["gender"],
            "department"                => $_POST["department"],
            "medical_insurance_premium" => $_POST["medical_insurance_premium"],
            "performance_allowance"     => $_POST["performance_allowance"],
            "income_tax"                => $_POST["income_tax"],
            "professional_tax"          => $_POST["professional_tax"],
            "other_deduction"           => $_POST["other_deduction"],
            "net_salary"                => $_POST["net_salary"],
            "tds_deduction_status"      => $_POST["tds_deduction_status"],
            "probation"                 => $_POST["probation"],
            "performance_bonus_status"  => $_POST["performance_bonus_status"],
            "pf_esi"                    => $_POST["pf_esi"],
            "esi_pf_opt"                => $_POST["esi_pf_opt"],
            "esi_pf_amt"                => $_POST["esi_pf_amt"],

        ];

        if($unique_id) {
            
            $update_where   = [
                "unique_id"     => $unique_id
            ];
            
            // Update Begins
            $action_obj     = $pdo->update($table,$columns,$update_where);
            // Update Ends

        } else {
            $where                  = " acc_year = '".$_SESSION["acc_year"]."'";
// AND pf_esi = '".$_POST["pf_esi"]."'
            $letter_no              = bill_no ($table,$where,"XWM/HRD/HO/", 1,0,0,false,"/");

            $columns["letter_no"]   = $letter_no;

            // Unique Id
            $columns["unique_id"]   = unique_id($prefix);

            // Insert Begins
            $action_obj             = $pdo->insert($table,$columns);
            // PRINT_R($action_obj);
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
        $company_name=$_POST['company_name'];
        // print_r($company_name);

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "letter_date",
            "letter_no",
            "name",
            "(SELECT company_name FROM company_name_creation AS company_name WHERE company_name.unique_id =".$table.".company_name )AS company_name",
            "designation",
            "join_date",
            "ctc",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        // $where          = [
        //     "is_delete"     => 0
        // ];
        $where = " is_delete = '0' ";
        
 
        
        if($_POST['company_name']){
            $where .= " AND company_name = '$company_name' ";
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

        // $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $value['letter_date'] = disdate($value['letter_date']);
                $value['join_date']   = disdate($value['join_date']);
                $value['letter_no']   = disname($value['letter_no']);
                $value['name']        = disname($value['name']);
                $value['designation'] = disname($value['designation']);
                $value['ctc']         = moneyFormatIndia($value['ctc']);
                $btn_view             = btn_print1($folder_name,$value['unique_id'],'offer_letter',"","","");
                $btn_update           = btn_update($folder_name,$value['unique_id']);
                $btn_delete           = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']   = $btn_view.$btn_update.$btn_delete;
                $data[]               = array_values($value);
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
    
    default:
        
        break;
}

?>