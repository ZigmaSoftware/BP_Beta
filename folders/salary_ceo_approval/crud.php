<?php 
error_reporting(0);
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "staff";
$table_leave       = "leave_details_sub";
$table_salary      = "salary_generation";
$table_salary_sub  = "salary_generation_sub";
$staff_account       = "staff_account_debitor_creditor";

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

$offer_letter       = "";
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
        $description        = $_POST["description"];
        $app_status         = $_POST["app_status"];
        $sublist_columns  = [
            "ceo_approval"          => $app_status,
            "ceo_approve_by"        => $_SESSION['staff_id'],
            "ceo_reason"            => $description,
            "ceo_approve_date"      => date('Y-m-d H:i:s')
        ];      

        $where_sublist =[
            "unique_id"         => $_POST['unique_id'],
            "is_active"         => 1,
            "is_delete"         => 0
        ];
        $action_obj   = $pdo->update($table_salary,$sublist_columns,$where_sublist);

        if ($action_obj->status) 
        {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;

            if ($unique_id) {
                $msg        = "update";
            } else {
                $msg        = "create";
            }
        } 
        else 
        {
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


    case 'datatable_main':
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
            "salary_date",
            "salary_no",
            "total_net_salary",
            "total_take_home",
            "ceo_approval",
            "unique_id"
        ];
        $table_details  = [
            $table_salary." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";

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

           
            foreach ($res_array as $key => $value) 
            {
                $table_details_count      = [
                    $table_salary_sub,
                        [
                            "COUNT(*) AS count"
                        ]
                ];
                $select_where_count           = ' salary_unique_id = "'.$value['unique_id'].'"';
                $action_obj_count             = $pdo->select($table_details_count,$select_where_count);
                $data_val_count               = $action_obj_count->data;            
                // print_r($action_obj_count->sql);
                $value['salary_date']       = date('M-Y',strtotime($value['salary_date']));
                $value['total_net_salary']  = $data_val_count[0]['count'];
                $value['total_take_home']   = moneyFormatIndia($value['total_take_home']);
                $btn_view                   = btn_print($folder_name,$value['unique_id'],'salary_ceo_approval');
                //$btn_mail                   = btn_print($folder_name,$value['unique_id'],'salary_generation');
                // $btn_mail                   = btn_print($folder_name,$value['unique_id'],'mail_print');
                // $btn_delete                 = btn_delete($folder_name,$value['unique_id']);
                if($value['ceo_approval']=='1')
                {
                    $btn_approve    = "CEO Approval Required";
                }
                elseif($value['ceo_approval']=='3')
                {
                    $btn_approve    = "CEO Approved";
                }
                elseif($value['ceo_approval']=='2')
                {
                    $btn_approve    = "Cancel";
                }
                $value['ceo_approval']      = $btn_approve;
                $value['unique_id']         = $btn_view;
                $data[]                     = array_values($value);
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
                "work_location",
                "employee_id",
                "date_of_join",
                "staff_name",
                "salary",
                "'' as total_days",
                "'' as lop",
                "'' as salary_days",
                "'' as gross_salary",
                "'' as tds",
                "pf",
                "esi",
                "(SELECT emi_amount as loan FROM loan_advance WHERE loan_advance.staff_id = ".$table.".unique_id and (loan_type = 1) ) AS loan",
                "(SELECT  amount as advance FROM loan_advance WHERE loan_advance.staff_id = ".$table.".unique_id and (loan_type = 2) ) AS advance",
                "'' as insurance",
                "'' as other_deduction",
                "'' as total_deduction",
                "net_salary",
                "'' as reimbrusment",
                "ctc as take_home",
                "(SELECT salary_type FROM staff_account_details AS staff_account WHERE staff_account.staff_unique_id = ".$table.".unique_id ) AS salary_type",
                "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = ".$table.".designation_unique_id ) AS designation_type",
                "department",
                "unique_id",
                "(SELECT unique_id as loan_id    FROM loan_advance WHERE loan_advance.staff_id = ".$table.".unique_id and (loan_type = 1) ) AS loan_id",
                "(SELECT unique_id as advance_id FROM loan_advance WHERE loan_advance.staff_id = ".$table.".unique_id and (loan_type = 2) ) AS advance_id"
            ];
            $table_details  = [
                $table." , (SELECT @a:= ".$start.") AS a ",
                $columns
            ];
            $where          = [
                "is_delete"     => 0,
                "relieve_date"  => '0000-00-00'
            ];
            $where = " is_delete = '0' AND relieve_date='0000-00-00'  AND DATE_FORMAT(date_of_join, '%Y-%m') <= '".$_REQUEST['now']."'";
    
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
    // print_r($result);
            if ($result->status) {
    
                $res_array      = $result->data;
    
                foreach ($res_array as $key => $value) {
                    $table_details      = [
                    $table_leave,
                        [
                            "COUNT(*) AS lop"
                        ]
                    ];
                    $select_where           = ' staff_id = "'.$value['unique_id'].'" AND leave_type="6" AND is_delete = 0 AND cancel_status = 0 AND DATE_FORMAT(from_date, "%Y-%m") = "'.$_REQUEST['now'].'" group by staff_id ';
                    $action_obj             = $pdo->select($table_details,$select_where);
                    $data_val               = $action_obj->data;
                    $doj = $value['date_of_join'];
    
                    $value['date_of_join']  = disdate($value['date_of_join']);
                    if($value['salary'])
                    {    
                        $salary                 = $value['salary'];
                    }
                    else
                    {
                        $salary                 = "0";
                    }
                    $value['salary']        = moneyFormatIndia($value['salary']);
                    foreach ($data_val as $key => $value2) 
                    {
                        $value['lop']       = $value2['lop'];
                    }
    
                    if($value['lop']!='')   { $value['lop'] = $value['lop'];  } else  {  $value['lop'] = "0"; }
    
                    if($value['pf']!='')    { $value['pf']  = $value['pf'];   } else  {  $value['pf']  = "0"; }
    
                    if($value['esi']!='')   { $value['esi'] = $value['esi'];  } else  {  $value['esi'] = "0"; }
    
                    if($value['loan']!='')   { $value['loan'] = $value['loan'];  } else  {  $value['loan'] = "0"; }
    
                    if($value['advance']!='')   { $value['advance'] = $value['advance'];  } else  {  $value['advance'] = "0"; }

                    if($value['loan_id']!='')   { $loan_emi = '1';  } else  {  $loan_emi = ""; }

                    if($value['advance_id']!='')   { $advance_emi = '2';  } else  {  $advance_emi = ""; }
    
                    $salary_type = $value['salary_type'];
    
                    if($value['salary_type']=='Axis Bank')   
                    {
                        $select = "<option value='Axis Bank' selected>Axis Bank</option><option value='NEFT'>NEFT</option><option value='Cheque'>Cheque</option><option value='Cash'>Cash</option><option value='Hold'>Hold</option>";  
                    } 
                    else if($value['salary_type']=='NEFT')  
                    {  
                        $select = "<option value='Axis Bank'>Axis Bank</option><option value='NEFT' selected>NEFT</option><option value='Cheque'>Cheque</option><option value='Cash'>Cash</option><option value='Hold'>Hold</option>"; 
                    } 
                    else if($value['salary_type']=='Cheque')  
                    {  
                        $select = "<option value='Axis Bank'>Axis Bank</option><option value='NEFT'>NEFT</option><option value='Cheque' selected>Cheque</option><option value='Cash'>Cash</option><option value='Hold'>Hold</option>"; 
                    } 
                    else if($value['salary_type']=='Cash')  
                    {  
                        $select = "<option value='Axis Bank'>Axis Bank</option><option value='NEFT'>NEFT</option><option value='Cheque'>Cheque</option><option value='Cash' selected>Cash</option><option value='Hold'>Hold</option>"; 
                    } 
                    else if($value['salary_type']=='Hold')  
                    {  
                        $select = "<option value='Axis Bank'>Axis Bank</option><option value='NEFT'>NEFT</option><option value='Cheque'>Cheque</option><option value='Cash'>Cash</option><option value='Hold' selected>Hold</option>";  
                    }
                    else if($value['salary_type']=="")  
                    {  
                        $select = "<option value='Axis Bank'>Axis Bank</option><option value='NEFT'>NEFT</option><option value='Cheque'>Cheque</option><option value='Cash'>Cash</option><option value='Hold' selected>Hold</option>";  
                    }
    
                    $value['total_days']        = $_REQUEST['date'];
                    $value['salary_days']       = $value['total_days'] - $value['lop'];
                    $total_days                 = $value['total_days'];
                    $value['gross_salary']      = moneyFormatIndia(($salary/$total_days)*$value['salary_days']);
                    $gross_salary               = (($salary/$total_days)*$value['salary_days']);
    
                    $pf_esi                     = $value['pf'] + $value['esi'] + $value['advance'] + $value['loan'];
                    $value['total_deduction']   = $pf_esi;
                    $final                      = $gross_salary - $pf_esi;
                    $final_salary               = moneyFormatIndia($gross_salary - $pf_esi);
    
                    $value['work_location']     = "<input type='text' style='width:100px;border:0px;' readonly class='form-control' id='work_location_".$value['unique_id']."' name='work_location_".$value['unique_id']."' value='".$value['work_location']."'>";
    
                    $value['employee_id']       = "<input type='text' style='width:80px;border:0px;' readonly class='form-control' id='employee_id_".$value['unique_id']."' name='employee_id_".$value['unique_id']."' value='".$value['employee_id']."'>";
    
                    $value['date_of_join']      = "<input type='text' style='width:100px;border:0px;' readonly class='form-control' id='date_of_join_".$value['unique_id']."' name='date_of_join_".$value['unique_id']."' value='".$value['date_of_join']."'><input type='hidden' id='doj_".$value['unique_id']."' name='doj_".$value['unique_id']."' value='".$doj."'>";
    
                    $value['staff_name']        = "<input type='hidden' style='width:180px;border:0px;' readonly class='form-control' id='staff_name_".$value['unique_id']."' name='staff_name_".$value['unique_id']."' value='".$value['staff_name']."'><label class='form-control' style='border:0px;'>".$value['staff_name']."</label><label class='form-control' style='border:0px;font-size:11px;margin-top: -21px;'>( ".$value['designation_type']." - ".$value['department']." )</label><input type='hidden' style='width:240px;border:0px;' readonly class='form-control' id='designation_type_".$value['unique_id']."' name='designation_type_".$value['unique_id']."' value='".$value['designation_type']."'><input type='hidden' style='width:180px;border:0px;' readonly class='form-control' id='department_".$value['unique_id']."' name='department_".$value['unique_id']."' value='".$value['department']."'>";
    
                    $value['designation_type']  = "<input type='hidden' style='width:240px;border:0px;' readonly class='form-control' id='designation_type_".$value['unique_id']."' name='designation_type_".$value['unique_id']."' value='".$value['designation_type']."'>";
    
                    $value['department']        = "<input type='hidden' style='width:180px;border:0px;' readonly class='form-control' id='department_".$value['unique_id']."' name='department_".$value['unique_id']."' value='".$value['department']."'>";
    
                    $value['salary']            = "<input type='text' style='width:90px;border:0px;' readonly class='form-control' id='salary_".$value['unique_id']."' name='salary_".$value['unique_id']."' value='".$value['salary']."'><input type='hidden' id='salary1_".$value['unique_id']."' name='salary1_".$value['unique_id']."' value='".$salary."'>";
    
                    $value['total_days']        = "<input type='text' style='width:60px;border:0px;' readonly class='form-control' id='total_days_".$value['unique_id']."' name='total_days_".$value['unique_id']."' value='".$value['total_days']."'>";
    
                    $value['lop']               = "<input type='text' style='width:40px;border:0px;' readonly class='form-control' id='lop_".$value['unique_id']."' name='lop_".$value['unique_id']."' value='".$value['lop']."'>";
    
                    $value['salary_days']       = "<input type='text' style='width:60px;border:0px;' readonly class='form-control' id='salary_days_".$value['unique_id']."' name='salary_days_".$value['unique_id']."' value='".$value['salary_days']."'>";
    
                    $value['gross_salary']      = "<input type='text' style='width:100px;border:0px;' readonly class='form-control' id='gross_salary_".$value['unique_id']."' name='gross_salary_".$value['unique_id']."' value='".$value['gross_salary']."'><input type='hidden' id='gross_salary1_".$value['unique_id']."' name='gross_salary1_".$value['unique_id']."' value='".$gross_salary."'>";
    
                    $value['tds']               = "<input type='text' style='width:130px;' class='form-control' id='tds_".$value['unique_id']."' name='tds_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='0'>";
    
                    $value['pf']                = "<input type='text' style='width:130px;' class='form-control' id='pf_".$value['unique_id']."' name='pf_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='".$value['pf']."'>";
    
                    $value['esi']               = "<input type='text' style='width:130px;' class='form-control' id='esi_".$value['unique_id']."' name='esi_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='".$value['esi']."'>";
    
                    $value['loan']              = "<input type='text' style='width:130px;' class='form-control' id='loan_".$value['unique_id']."' name='loan_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='".$value['loan']."'><input type='hidden' id='loan_unique_id".$value['unique_id']."' name='loan_unique_id".$value['unique_id']."' value='".$value['loan_id']."'><input type='hidden' id='loan_emi".$value['unique_id']."' name='loan_emi".$value['unique_id']."' value='".$loan_emi."'>";
    
                    $value['advance']           = "<input type='text' style='width:130px;' class='form-control' id='advance_".$value['unique_id']."' name='advance_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='".$value['advance']."'><input type='hidden' id='advance_unique_id".$value['unique_id']."' name='advance_unique_id".$value['unique_id']."' value='".$value['advance_id']."'><input type='hidden' id='advance_emi".$value['unique_id']."' name='advance_emi".$value['unique_id']."' value='".$advance_emi."'>";
    
                    $value['insurance']         = "<input type='text' style='width:130px;' class='form-control' id='insurance_".$value['unique_id']."' name='insurance_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='0'>";
    
                    $value['other_deduction']   = "<input type='text' style='width:130px;' class='form-control' id='other_deduction_".$value['unique_id']."' name='other_deduction_".$value['unique_id']."' onkeyup=deduction_calculation('".$value['unique_id']."','".$gross_salary."') value='0'>";
    
                    $value['total_deduction']   = "<input type='text' style='width:130px;' class='form-control' id='total_deduction_".$value['unique_id']."' name='total_deduction_".$value['unique_id']."' readonly value='".$value['total_deduction']."'>";
    
                    $value['net_salary']        = "<input type='text' style='width:130px;' class='form-control' id='net_salary_".$value['unique_id']."' name='net_salary_".$value['unique_id']."' readonly value='".$final_salary."'><input type='hidden' id='net_salary1_".$value['unique_id']."' name='net_salary1_".$value['unique_id']."' value='".$final."'>";
    
                    $value['reimbrusment']      = "<input type='text' style='width:130px;' class='form-control' id='reimbrusment_".$value['unique_id']."' name='reimbrusment_".$value['unique_id']."' onkeyup=reimbrusment_calculation('".$value['unique_id']."') value='0'>";
    
                    $value['take_home']         = "<input type='text' style='width:130px;' class='form-control' id='take_home_".$value['unique_id']."' name='take_home_".$value['unique_id']."' readonly value='".$final_salary."'><input type='hidden' id='take_home1_".$value['unique_id']."' name='take_home1_".$value['unique_id']."' value='".$final."'>";
    
                    $value['salary_type']       = "<select class='form-control select2' style='width:150px;' id='salary_type_".$value['unique_id']."' name='salary_type_".$value['unique_id']."' required><option>Select</option>".$select."</select>";
    
                    $data[]                     = array_values($value);
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

        $columns_sub        = [
            "is_delete"   => 1
        ];

        $update_where_sub   = [
            "salary_unique_id"     => $unique_id
        ];

        $action_obj_sub     = $pdo->update($table_salary_sub,$columns_sub,$update_where_sub);

        $columns        = [
            "is_delete"   => 1
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_salary,$columns,$update_where);

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

        case 'salary_pay':
        
        $type      = $_POST['type'];
        $date      = $_POST['date'];

        $columns        = [
            "salary_pay"   => 1
        ];

        $department     = explode('-',$_POST['type']);

        if($department[1])
        {
            $department  = $department[0].' '.$department[1].' '.$department[2];
        }
        else
        {
            $department  = $department[0];
        }

        $update_where   = [
            "salary_type"     => $department,
            "salary_date"     => $date,
            "is_delete"       => 0
        ];

        $action_obj     = $pdo->update($table_salary_sub,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "status_update";

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