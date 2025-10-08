<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "experience_letter";

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
            "to_date"               => $_POST["to_date"],
            "name"                      => $_POST["staff_name"],
           
            "company_name"              => $_POST["company_name"],

            "company_name_unique_id"    =>$_POST["company_name_unique_id"],
            
            "join_date"                 => $_POST["join_date"],

            "designation"               => $_POST["designation"],
            

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

            $letter_no              = bill_no ($table,$where,"XWM/HRD/HO/", 1,0,0,false,"/");

            $columns["letter_no"]   = $letter_no;

            // Unique Id
            $columns["unique_id"]   = unique_id($prefix);

            // Insert Begins
            $action_obj             = $pdo->insert($table,$columns);
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
            "letter_no",
            "(SELECT staff_name FROM staff as staff  WHERE staff.unique_id = ".$table.".name ) AS name",
            // "name",
            "company_name",
            // "(SELECT company_name FROM company_name_creation AS company_name_creation WHERE company_name_creation.unique_id = ".$table.".company_name ) AS company_name",
            "join_date",
            "to_date",
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
            $where .= " AND company_name_unique_id = '$company_name' ";
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
               
                $value['letter_no']   = disname($value['letter_no']);
                $value['name']        = disname($value['name']);
                $value['company_name'] = disname($value['company_name']);  
                
                $value['join_date']   = disdate($value['join_date']);  
                $value['to_date'] = disdate($value['to_date']);            
                $btn_view             = btn_print1($folder_name,$value['unique_id'],'experience_letter',"","","");
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

        case "staff_details":
            $staff_id   = $_POST["staff_id"];
    
            $details    = [
                "date_of_join"          => "",
                "designation_unique_id" => "",
                "designation"           => "",
                "department"            => "",
                "salary"                => "",
                "relieve_date"          => "",
            ];
    
            if ($staff_id) {
                $staff_where = [
                    "unique_id" => $staff_id
                ];
    
                $staff_columns = [
                    "date_of_join",
                    "designation_unique_id",
                    "IFNULL((SELECT designation FROM designation_creation WHERE designation_creation.unique_id = staff.designation_unique_id),'') AS designation",
                    "(SELECT department FROM department_creation WHERE department_creation.unique_id = staff.department) AS department",
                    "(SELECT company_name FROM company_name_creation WHERE company_name_creation.unique_id = staff.company_name) AS company_name_value",
                    "company_name"
                    // "department",
                    // "salary",
                    // "relieve_date"
                ];
    
                $staff_table_details = [
                    "staff",
                    $staff_columns
                ];
    
                $staff_details = $pdo->select($staff_table_details,$staff_where);
    
                if ($staff_details->status) {
                    if (!empty($staff_details->data)) {
                        $details = $staff_details->data[0];
                    }
                } else {
                    print_r($staff_details);
                }
            }
    
            echo json_encode($details);
    
            break;
    
    default:
        
        break;
}

?>