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
        $salary_date        = $_POST["letter_date"];
        $salary_category    = $_POST["salary_category"];


        $columns            = [
            "salary_date"   => $_POST["letter_date"],
            
        ];

        $columns_app        = [
            "unique_id",
            "salary_category"   => $_POST["salary_category"]

        ];
        $table_details_app  = [
            $table,
            $columns_app
        ];
        $where_app          = " is_delete = '0'  AND relieve_date='0000-00-00' AND DATE_FORMAT(date_of_join, '%Y-%m') <= '".$_POST['letter_date']."' AND  salary_category= '".$_POST['salary_category']."'";        

        $result_app         = $pdo->select($table_details_app,$where_app);
        $res_array          = $result_app->data;
        
        $where                              = " acc_year = '".$_SESSION["acc_year"]."'";
        $salary_no                          = bill_no ($table_salary,$where,"SG-", 1,0,0,false,"-");

        $table_details_salary      = [
            $table_salary,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where_salary       = 'salary_date = "'.$salary_date.'" AND is_delete="0"';
        $action_obj_salary         = $pdo->select($table_details_salary,$select_where_salary);

        $data_salary               = $action_obj_salary->data;

        if($data_salary[0]['count']=='0')
        {
            foreach ($res_array as $key => $value) 
            {
                $staff_id           = $value['unique_id'];
                $work_location      = "work_location_".$staff_id;
                $employee_id        = "employee_id_".$staff_id;
                $date_of_join       = "doj_".$staff_id;
                $staff_name         = "staff_name_".$staff_id;
                $designation_type   = "designation_type_".$staff_id;
                $department         = "department_".$staff_id;
                $salary             = "salary1_".$staff_id;
                $total_days         = "total_days_".$staff_id;
                $lop                = "lop_".$staff_id;
                $salary_days        = "salary_days_".$staff_id;
                $gross_salary       = "gross_salary1_".$staff_id;
                $tds                = "tds_".$staff_id;
                $pf                 = "pf_".$staff_id;
                $esi                = "esi_".$staff_id;
                $advance            = "advance_".$staff_id;
                $loan               = "loan_".$staff_id;
                $insurance          = "insurance_".$staff_id;
                $other_deduction    = "other_deduction_".$staff_id;
                $total_deduction    = "total_deduction_".$staff_id;
                $net_salary         = "net_salary1_".$staff_id;
                $reimbrusment       = "reimbrusment_".$staff_id;
                $take_home          = "take_home1_".$staff_id;
                $salary_type        = "salary_type_".$staff_id;
                $loan_id            = "loan_unique_id".$staff_id;
                $advance_id         = "advance_unique_id".$staff_id;
                $loan_type          = "loan_emi".$staff_id;
                $advance_type       = "advance_emi".$staff_id;
                $total_tds              += $_POST[$tds];
                $total_pf               += $_POST[$pf];
                $total_esi              += $_POST[$esi];
                $total_advance          += $_POST[$advance];
                $total_loan             += $_POST[$loan];
                $total_insurance        += $_POST[$insurance];
                $total_other_deduction  += $_POST[$other_deduction];
                $total_net_salary       += $_POST[$net_salary];
                $total_reimbrushment    += $_POST[$reimbrusment];
                $total_takehome         += $_POST[$take_home];


                $columns_sub        = [
                    "work_location"   => $_POST[$work_location],
                    "employee_id"     => $_POST[$employee_id],
                    "date_of_join"    => $_POST[$date_of_join],
                    "staff_id"        => $staff_id,
                    "staff_name"      => $_POST[$staff_name],
                    "designation_type"=> $_POST[$designation_type],
                    "department"      => $_POST[$department],
                    "salary"          => $_POST[$salary],
                    "total_days"      => $_POST[$total_days],
                    "lop"             => $_POST[$lop],
                    "salary_days"     => $_POST[$salary_days],
                    "gross_salary"    => $_POST[$gross_salary],
                    "tds"             => $_POST[$tds],
                    "pf"              => $_POST[$pf],
                    "esi"             => $_POST[$esi],
                    "advance"         => $_POST[$advance],
                    "loan"            => $_POST[$loan],
                    "insurance"       => $_POST[$insurance],
                    "other_deduction" => $_POST[$other_deduction],
                    "total_deduction" => $_POST[$total_deduction],
                    "net_salary"      => $_POST[$net_salary],
                    "reimbrusment"    => $_POST[$reimbrusment],
                    "take_home"       => $_POST[$take_home],
                    "salary_type"     => $_POST[$salary_type],
                    "screen_unique_id"=> $_POST['screen_unique_id']
                ];

                $where_sub                  = " acc_year = '".$_SESSION["acc_year"]."'";
                $columns_sub["unique_id"]   = unique_id($prefix);
                $action_obj_sub             = $pdo->insert($table_salary_sub,$columns_sub);

                $now     = date('Y-m-d');
                if($_POST[$loan_id])
                {
                    $columns_staff["loan_unique_id"]          = $_POST[$loan_id];
                    $columns_staff["loan_type"]               = $_POST[$loan_type];
                    $columns_staff["entry_date"]              = $now;
                    $columns_staff["employee_name"]           = $staff_id;
                    $columns_staff["credit_amount"]           = $_POST[$loan];
                    $columns_staff["form_unique_id"]          = $_POST['screen_unique_id'];
                    $columns_staff["unique_id"]               = unique_id($prefix);
                    $action_obj_staff                         = $pdo->insert($staff_account,$columns_staff);
                }
                if($_POST[$advance_id])
                {
                    $columns_staff["loan_unique_id"]          = $_POST[$advance_id];
                    $columns_staff["loan_type"]               = $_POST[$advance_type];
                    $columns_staff["entry_date"]              = $now;
                    $columns_staff["employee_name"]           = $staff_id;
                    $columns_staff["credit_amount"]           = $_POST[$advance];
                    $columns_staff["form_unique_id"]          = $_POST['screen_unique_id'];
                    $columns_staff["unique_id"]               = unique_id($prefix);
                    $action_obj_staff                         = $pdo->insert($staff_account,$columns_staff);
                }
            }

            

            $columns["salary_no"]               = $salary_no;
            $columns["total_tds"]               = $total_tds;
            $columns["total_pf"]                = $total_pf;
            $columns["total_esi"]               = $total_esi;
            $columns["total_advance"]           = $total_advance;
            $columns["total_loan"]              = $total_loan;
            $columns["total_insurance"]         = $total_insurance;
            $columns["total_other_deduction"]   = $total_other_deduction;
            $columns["total_net_salary"]        = $total_net_salary;
            $columns["total_reimbrushment"]     = $total_reimbrushment;
            $columns["total_take_home"]         = $total_takehome;
            $columns["hr_approve_by"]           = $_SESSION["staff_id"];
            $columns["hr_approval"]             = '1';
            $columns["hr_approve_date"]         = date('Y-m-d H:i:s');
            $columns["screen_unique_id"]        = $_POST['screen_unique_id'];
            $columns["unique_id"]               = $main_unique_id = unique_id($prefix);
            $action_obj                         = $pdo->insert($table_salary,$columns);

            $sublist_columns  = [
                "salary_unique_id"      => $main_unique_id,
                "salary_no"             => $salary_no,
                "salary_date"           => $_POST["letter_date"]
            ];      

            $where_sublist =[
                "screen_unique_id"  => $_POST['screen_unique_id'],
                "is_active"         => 1,
                "is_delete"         => 0
            ];
            $action_obj_salsub   = $pdo->update($table_salary_sub,$sublist_columns,$where_sublist);

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
        else
        {
            $msg            = "already";
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

        case 'datatable_slry_accuracy':
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
            "(SELECT work_location as location FROM staff WHERE staff.unique_id = ".$table_salary_sub.".staff_id ) AS location",
            "(SELECT employee_id as employee_id FROM staff WHERE staff.unique_id = ".$table_salary_sub.".staff_id ) AS employee_id",
            "(SELECT date_of_join as date_of_join FROM staff WHERE staff.unique_id = ".$table_salary_sub.".staff_id ) AS date_of_join",
            "staff_name",
            "designation_type",
            "department",
            "total_days",
            "lop",
            "salary_days",
            "gross_salary",
            "((gross_salary * 40) / 100) as basic_wages",
            "'' as hra",
            "'' as coneyance",
            "'' as medical",
            "'' as education",
            "'' as tfi",
            "gross_salary as gross",
            "gross_salary as esi_gross",
            "tds",
            "pf",
            "esi",
            "advance",
            "other_deduction",
            "total_deduction",
            "net_salary",
            "reimbrusment",
            "take_home",
            ];
            $table_details  = [
                $table_salary_sub." , (SELECT @a:= ".$start.") AS a ",
                $columns
            ];
            $where          = [
                "is_delete"         => 0,
                "salary_unique_id"  => $_POST['unique_id'],
            ];
            $where = " is_delete = '0' and salary_unique_id = '".$_POST['unique_id']."' ";
    
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
                    if($value['basic_wages']!='' && $value['basic_wages']!='0' && $value['basic_wages']>'1')
                    {
                        $hra_wages            = ($value['basic_wages'] * 50) / 100;
                        $conveyance_wages     = "1600";
                        $medical_wages        = "1250";
                        $educational_wages    = "200";
                        $value['hra']         = ($value['basic_wages'] * 50) / 100;
                        $value['coneyance']   = "1600";
                        $value['medical']     = "1250";
                        $value['education']   = "200";
                    }
                    else
                    {
                        $hra_wages            = '0';
                        $conveyance_wages     = "0";
                        $medical_wages        = "0";
                        $educational_wages    = "0";
                        $value['hra']         = '0';
                        $value['coneyance']   = "0";
                        $value['medical']     = "0";
                        $value['education']   = "0";
                    }
                    $TFI_total                  =   $value['basic_wages'] + $hra_wages + $conveyance_wages + $medical_wages + $educational_wages;
                    $value['tfi']               =   $value['gross_salary'] - $TFI_total;
                    $value['basic_wages']       = moneyFormatIndia($value['basic_wages']);
                    $value['hra']               = moneyFormatIndia($value['hra']);
                    $value['coneyance']         = moneyFormatIndia($value['coneyance']);
                    $value['medical']           = moneyFormatIndia($value['medical']);
                    $value['education']         = moneyFormatIndia($value['education']);
                    $value['tfi']               = moneyFormatIndia($value['tfi']);
                    $value['gross_salary']      = moneyFormatIndia($value['gross_salary']);
                    $value['esi_gross']         = moneyFormatIndia($value['esi_gross']);
                    $value['gross']             = moneyFormatIndia($value['gross']);
                    $value['pf']                = moneyFormatIndia($value['pf']);
                    $value['esi']               = moneyFormatIndia($value['esi']);
                    $value['tds']               = moneyFormatIndia($value['tds']);
                    $value['advance']           = moneyFormatIndia($value['advance']);
                    $value['other_deduction']   = moneyFormatIndia($value['other_deduction']);
                    $value['total_deduction']   = moneyFormatIndia($value['total_deduction']);
                    $value['net_salary']        = moneyFormatIndia($value['net_salary']);
                    $value['reimbrusment']      = moneyFormatIndia($value['reimbrusment']);
                    $value['take_home']         = moneyFormatIndia($value['take_home']);
                    $value['date_of_join']      = disdate($value['date_of_join']);
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
                
                $btn_view                   = btn_print($folder_name,$value['unique_id'],'salary_generation',"","",'Department Wise Data Print');
                // $btn_print                  = "<i class='mdi mdi-printer mdi-24px waves-effect waves-light mt-n2 mb-n2 mr-1 text-success' onclick=new_external_window('folders/salary_generation/salary_accuracy.php?department=".$department."&unique_id=".$value['unique_id']."'); title='Salary Accuracy'></i>";
                $btn_mail                   = btn_print($folder_name,$value['unique_id'],'mail_print',"","","Payslip");
                $btn_print                  = btn_print($folder_name,$value['unique_id'],'salary_accuracy',"","",'Individual Data Print');
                $btn_delete                 = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']         = $btn_print.$btn_view.$btn_mail.$btn_delete;
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
                "(SELECT unique_id as advance_id FROM loan_advance WHERE loan_advance.staff_id = ".$table.".unique_id and (loan_type = 2) ) AS advance_id",
                "salary_category",
            ];
            $table_details  = [
                $table." , (SELECT @a:= ".$start.") AS a ",
                $columns
            ];
            $where          = [
                "is_delete"     => 0,
                "relieve_date"  => '0000-00-00'
            ];
            $where = " is_delete = '0' AND relieve_date='0000-00-00'  AND DATE_FORMAT(date_of_join, '%Y-%m') <= '".$_REQUEST['now']."' AND salary_category='".$_REQUEST['salary']."'";
    
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
    
    default:
        
        break;
}

?>