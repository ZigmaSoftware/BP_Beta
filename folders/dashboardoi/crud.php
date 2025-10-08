<?php
// Get folder Name From Currnent Url 
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// date_default_timezone_set("Asia/Kolkata"); 

// Database Country Table Name
$table          = "complaint_creation";
$table_sub      = "complaint_creation_doc_upload";
$table_stage_1  = "stage_1";
$table_cmd      = "commends";

// Include DB file and Common Functions
include '../../config/dbconfig2.php';
include 'function.php';

// Variables Declaration
$action = $_POST['action'];

//$user_type          = "";
$is_active = "";
$unique_id = "";
$prefix = "";

$data = "";
$msg = "";
$error = "";
$status = "";
$test = ""; 


        
// For Developer Testing Purpose
if($_SESSION['sess_department_name'] != 'All'){
    if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        $rep_dept = str_replace(',', "','", $_SESSION['sess_department_name']);
        $where .= " and department_name in ('" . $rep_dept . "')";
    }
}

if($_SESSION['sess_type_user'] == 1){
            $where .= " and assign_by = '".$_SESSION['user_id']."'";
        }
        
if($_SESSION['sess_site_name'] != 'All'){
    if($_SESSION['user_id'] != '5ff562ed542d625323'){
        $rep_site = str_replace(',', "','", $_SESSION['sess_site_name']);
        $where .= " and site_name in ('".$rep_site."')";
    }
}
$where .=" and priority_type = '664ad1a16448664824'";
switch ($action) {
    
    case 'insert_commends':
    
        // $entry_date     = $_POST["entry_date"];
        $complaint_no   = $_POST["complaint_no"];
        $commends       = $_POST["commends"];
        $current_date1  = date('Y-m-d');
        // print_r($current_date1);
        // die();
        // echo "hi";
        // print_r("hello");
       
        $update_where       = "";

        $columns            = [
            "entry_date"    => $current_date1,
            "complaint_no"  => $complaint_no,
            "commends"      => $commends,
            "unique_id"     => unique_id($prefix)
        ];
        

            // check already Exist Or not
        $table_details      = [
            $table_cmd,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = ' is_delete = 0  AND complaint_no ="'.$complaint_no.'" ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

 $action_obj     = $pdo->insert($table_cmd,$columns);
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
        
        
        
       case 'update_commends':  
        
        $unique_id      = $_POST['unique_id_up'];
        $commends       = $_POST["commends"];

        $columns        = [
            "commends"   => $commends
        ];

        $update_where   = [
            "s_no"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_cmd,$columns,$update_where);

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
        
        
        
        case 'delete_commends':
            
        $unique_id      = $_POST['unique_id'];

        $columns        = [
            "is_delete"   => 1
        ];

        $update_where   = [
            "s_no"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_cmd,$columns,$update_where);

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
        
        
	case 'task_details':
    
        $json_array     = "";
        $today  =  date('Y-m-d');
        // $columns        = [           
            
        //     "(select COUNT(id) as opening_complaints from complaint_creation where entry_date < '".$today."' and ((stage_1_status = 2 and DATE_FORMAT(stage_1_update_date, '%Y-%m-%d')  = '".$today."') or  (stage_1_status = 0 or stage_1_status = 1))  and is_delete = 0) as opening_complaints",
        //     "(select COUNT(id) as new_complaints from complaint_creation where entry_date = '".$today."' and  (stage_1_status = 0 or stage_1_status = 1) and is_delete = 0) as new_complaints",
        //     "(select COUNT(id) as completed_complaints from complaint_creation where  DATE_FORMAT(stage_1_update_date, '%Y-%m-%d')  = '".$today."' and stage_1_status = 2 and is_delete = 0) as completed_complaints",
        // ];
        
        $columns        = [           
            
            "(select COUNT(id) as opening_complaints from complaint_creation where entry_date < '".$today."'".$where."  and ((stage_1_status = 0 or stage_1_status = 1))  and is_delete = 0) as opening_complaints",
            "(select COUNT(id) as new_complaints from complaint_creation where entry_date = '".$today."'".$where." and is_delete = 0) as new_complaints",
            "(select COUNT(id) as completed_complaints from complaint_creation where  DATE_FORMAT(stage_1_update_date, '%Y-%m-%d')  = '".$today."'".$where." and stage_1_status = 2 and is_delete = 0 and (entry_date != '". $today ."' OR stage_1_update_date NOT LIKE '%". $today ."%' )) as completed_day_complaints",
            "(select COUNT(id) as completed_complaints from complaint_creation where  DATE_FORMAT(stage_1_update_date, '%Y-%m-%d')  = '".$today."'".$where." and stage_1_status = 2 and is_delete = 0) as completed_complaints",
            "(select COUNT(id) as progress_complaints from complaint_creation where entry_date <= '".$today."'".$where." and ((stage_1_status = 1))  and is_delete = 0) as progress_complaints",
            "(select COUNT(id) as pending from complaint_creation where entry_date <= '".$today."'".$where." and ((stage_1_status = 0)) and is_delete = 0) as pending",
            // "(select COUNT(id) as complete_day_complaints from complaint_creation where entry_date = '".$today."'".$where."  and ( (stage_1_status = 2))  and is_delete = 0) as complete_day_complaints",
        ];
       
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        // print_r($result);
        $res_array      = $result->data;
       
        foreach($res_array as $value){
        
	        $opening_complaints 	= $value['opening_complaints'];
	        $new_complaints 		= $value['new_complaints'];
	        $completed_day_complaints = $value['completed_day_complaints'];
	        $completed_complaints 	= $value['completed_complaints'];
	        $progress_complaints 	= $value['progress_complaints'];
	        $pending_complaint      = $value['pending'];

            $opening_complaint     = abs($opening_complaints+$completed_day_complaints);
            
	       // $complete_day_complaints 	= $value['complete_day_complaints'];
	       // $pending_complaints 	= abs(($opening_complaints + $new_complaints)-$complete_day_complaints);

        }
                
        $json_array = [
                "opening_complaints"       	=> $opening_complaint,
                "new_complaints"       		=> $new_complaints,
                "completed_complaints"    	=> $completed_complaints,
                "pending_complaints"       	=> $progress_complaints,
                "pending"                   => $pending_complaint,
            ];
        
        echo json_encode($json_array);
         
    break;  
        
        case 'tagged_details':
        
        $json_array     = "";
        $today  =  date('Y-m-d');
        // $columns        = [           
            
        //     "(select COUNT(id) as opening_complaints from complaint_creation where entry_date < '".$today."' and ((stage_1_status = 2 and DATE_FORMAT(stage_1_update_date, '%Y-%m-%d')  = '".$today."') or  (stage_1_status = 0 or stage_1_status = 1))  and is_delete = 0) as opening_complaints",
        //     "(select COUNT(id) as new_complaints from complaint_creation where entry_date = '".$today."' and  (stage_1_status = 0 or stage_1_status = 1) and is_delete = 0) as new_complaints",
        //     "(select COUNT(id) as completed_complaints from complaint_creation where  DATE_FORMAT(stage_1_update_date, '%Y-%m-%d')  = '".$today."' and stage_1_status = 2 and is_delete = 0) as completed_complaints",
        // ];
        // if($_SESSION['user_id'] != '5ff562ed542d625323'){
        // $columns        = [           
            
        //     "(select COUNT(DISTINCT s.screen_unique_id) AS tagged_complaints from stage_1 s join complaint_creation c on c.screen_unique_id = s.screen_unique_id where c.stage_1_status != 2 and  c.is_delete = 0 and s.is_delete = 0 and FIND_IN_SET('". $_SESSION['user_id']."',s.user_name_select)) as tagged_calls",
            
        // ];
        // }else{
        //     $columns        = [           
             
        //     "(select COUNT(DISTINCT s.screen_unique_id) AS tagged_complaints from stage_1 s join complaint_creation c on c.screen_unique_id = s.screen_unique_id where c.stage_1_status != 2 and c.is_delete = 0 and s.is_delete = 0 and s.user_name_select !='') as tagged_calls",
            
        // ];
        // }
        
        
		if($_SESSION['user_id'] != '5ff562ed542d625323'){
        $columns        = [           
            
           "( select COUNT(DISTINCT s.screen_unique_id) AS tagged_complaints from stage_1 s join complaint_creation c on c.screen_unique_id = s.screen_unique_id where c.stage_1_status != 2 and c.stage_1_status != 3 and  c.is_delete = 0 and c.priority_type = '664ad1a16448664824' and s.is_delete = 0 and  s.print_status != 1 and FIND_IN_SET('". $_SESSION['user_id']."',s.user_name_select)) as tagged_calls",
            
        ];
        }else{
            $columns        = [           
             
            "(select COUNT(DISTINCT s.screen_unique_id) AS tagged_complaints from stage_1 s join complaint_creation c on c.screen_unique_id = s.screen_unique_id where c.stage_1_status != 2 and c.stage_1_status != 3 and c.is_delete = 0 and c.priority_type = '664ad1a16448664824' and s.is_delete = 0 and s.print_status != 1 and s.user_name_select !='') as tagged_calls",
            
        ];
        }
        
        
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "is_delete = 0";
        $result         = $pdo->select($table_details,$where);
       // print_r($result);
        $res_array      = $result->data;
        
        foreach($res_array as $value){
        
	        $tagged_calls 	= $value['tagged_calls'];

        }
                
        $json_array = [
                "tagged_calls"       	=> $tagged_calls,
                
                
            ];
        
         echo json_encode($json_array);
         
        break; 

    case 'over_complaint_details':
    
        $json_array     = "";
        $columns        = [           
            "(select COUNT(id) as total_comp from complaint_creation where  is_delete = 0) as total_comp",
            "(select COUNT(id) as pending_comp from complaint_creation where stage_1_status = 0  and is_delete = 0) as pending_comp",
            "(select COUNT(id) as progressing_comp from complaint_creation where stage_1_status = 1  and is_delete = 0) as progressing_comp",
            "(select COUNT(id) as completed_comp from complaint_creation where stage_1_status = 2 and is_delete = 0) as completed_comp",
            "(select COUNT(id) as cancel_comp from complaint_creation where stage_1_status = 3 and is_delete = 0) as cancel_comp",
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
            $total_comp         = $value['total_comp'];
            $pending_comp       = $value['pending_comp'];
            $progressing_comp   = $value['progressing_comp'];
            $completed_comp     = $value['completed_comp'];
            $cancel_comp        = $value['cancel_comp'];
            }
                
        $json_array = [
                "total_comp"        => $total_comp,
                "pending_comp"      => $pending_comp,
                "progressing_comp"  => $progressing_comp,
                "completed_comp"    => $completed_comp,
                "cancel_comp"       => $cancel_comp,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;  

    case 'sourcewise_complaints':
    
        $json_array     = "";
        $columns        = [           
            "(select COUNT(id) as app from complaint_creation where entry_from like '%APP%' and is_delete = 0) as app",
            "(select COUNT(id) as web from complaint_creation where entry_from like '%web%'  and is_delete = 0) as web",
            "(select COUNT(id) as admin_portal from complaint_creation where entry_from like '%Admin Portal%'  and is_delete = 0) as admin_portal",
            "(select COUNT(id) as chatbot from complaint_creation where entry_from like '%Chat Bot%' and is_delete = 0) as chatbot",
            
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
            $app         = $value['app'];
            $web       = $value['web'];
            $admin_portal   = $value['admin_portal'];
            $chatbot     = $value['chatbot'];
            }
                
        $json_array = [
                "app"        => $app,
                "web"      => $web,
                "admin_portal"  => $admin_portal,
                "chatbot"    => $chatbot,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;  

    case 'overall_complaint_status':
        
        $json_array     = "";
        $columns        = [           
            "(select COUNT(id) as total_comp from complaint_creation where  is_delete = 0 ".$where.")  as total_comp",
             "(select COUNT(id) as progressing_comp from complaint_creation where stage_1_status = 1 ".$where." and is_delete = 0) as progressing_comp",
            "(select COUNT(id) as completed_comp from complaint_creation where stage_1_status = 2 ".$where." and is_delete = 0) as completed_comp",
            "(select COUNT(id) as cancel_comp from complaint_creation where stage_1_status = 3 ".$where." and is_delete = 0) as cancel_comp",
             "(select COUNT(id) as pending_comp from complaint_creation where stage_1_status = 0 ".$where." and is_delete = 0) as pending_comp",
    
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
    //   print_r($result);
        foreach($res_array as $value){
        
            $total_comp         = $value['total_comp'];
            $pending_comp       = $value['pending_comp'];
            $progressing_comp   = $value['progressing_comp'];
            $completed_comp     = $value['completed_comp'];
            $cancel_comp        = $value['cancel_comp'];
            }
                
        $json_array = [
                "total_comp"        => $total_comp,
                "progressing_comp"  => $progressing_comp,
                "completed_comp"    => $completed_comp,
                "cancel_comp"       => $cancel_comp,
                "pending_comp"      => $pending_comp,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;  

    // case "department_details":
    //     $table_data = ' <table class="table table-hover table-centered mb-0 spel-table">
    //                         <thead>
    //                             <tr>
    //                                 <th></th>
    //                                 <th>Department</th>
    //                                 <th>Task</th>
    //                                 <th>Percentage</th>
    //                                 <th>Overdue</th>
    //                             </tr>
    //                         </thead>
    //                         <tbody>';

    //                          $table_data .=     '<tr>
    //                                 <td><i class="mdi mdi-circle-double text-info me-1"></i></td>
    //                                 <td>
    //                                     <h5>Account Department</h5>Kumar
    //                                 </td>
    //                                 <td>0/25</td>
    //                                 <td>
    //                                     <div class="progress" style="height: 6px;">
    //                                         <div class="progress-bar bg-info" role="progressbar" style="width: 55%;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
    //                                     </div>
    //                                 </td>
    //                                 <td>18</td>
    //                             </tr>';
                                


    //                        $table_data .=   '</tbody>
    //                     </table>';

    //      $json_array = [
    //         'data'            => $table_data,
    //     ];
        
        
        
    //     echo json_encode($json_array);
    //     break;

    case "site_details":
        $month = $_POST['month'];
        $columns        = [
                "site_name",
                "count(complaint_no) as total_complaints",
                
               ];
            
               $table_details  = [
                'complaint_creation',
                $columns
            ];
    
            $where        = "is_delete = 0 and 	entry_date like'%". $month."%' ".$where." GROUP BY site_name ";
    
            $result         = $pdo->select($table_details,$where);
            // print_r($result);
    
            if ($result->status) {
    
                $res_array      = $result->data;
                

        $table_data .= ' <table class="table table-hover table-centered mb-0 spel-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Site</th>
                                    <th>Task</th>
                                    <th>Percentage</th>
                                    <th>Overdue</th>
                                </tr>
                            </thead>
                            <tbody>';

                if($res_array){
                    
                    $i =0;
                        
                        foreach ($res_array as $key => $value) {

                            $department_name = $value['department_name'];
                            $assign = $value['assign_by'];        
                            $site_name = site_name($value['site_name'])[0]['site_name'];
                            // $assign_by = disname(user_name($value['assign_by'])[0]['user_name']);
                            $pending_details = get_pending_details($value[site_name]);
                      
                            // $over_due = get_overdue_cnt($department_name,$assign);

                            if($pending_details == ''){
                                $pending_details = 0;
                            }
                            if($over_due == ''){
                                $over_due = 0;
                            }
                            $progress_bar_per = (($pending_details/$value['total_complaints'])*100);


                             $table_data .=     '<tr>
                                    <td><i class="mdi mdi-circle-double text-info me-1"></i></td>
                                    <td>
                                        <h5>'.$site_name.'</h5>
                                    </td>
                                    <td>'.$pending_details.'/'.$value['total_complaints'].'</td>

                                    <td>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-info" role="progressbar" style="width: '.$progress_bar_per.'%;" aria-valuenow="55" aria-valuemin="0" aria-valuemax="100"></div>
                                        </div>
                                    </td>
                                    <td>'.$over_due.'</td>
                                </tr>';

                            }
                        }
                    }
                        $table_data .=   '</tbody>
                        </table>';

         $json_array = [
            'data'            => $table_data,

        ];
        
        
        
        echo json_encode($json_array);
        break;
    
    case 'age_wise_complaints':
        $date = date('Y-m-d');
        $json_array = [];
        $columns = [           
            // "(select count(entry_date) from view_level_all_departments where stage_1_status != 2 .$where) as less_1",
            // "less_5",
            // "less_10",
            // "less_15",
            // "less_30",
            // "greater_30"
            // "(select COUNT(complaint_category) as tot_comp from view_level_all_departments where  days_cnt <= 1 $where)  as less_1",
            // // "(SELECT COUNT(complaint_category) FROM complaint_creation WHERE view_all_departments.unique_id =" . $table . ".unique_id AND days_cnt <= 1 $where) AS less_1",

            // "(select COUNT(complaint_category) as pending from view_level_all_departments where stage_1_status != 2 and  days_cnt > 1 and days_cnt <= 5 $where)  as less_5",
            // "(select COUNT(complaint_category) as progressing from view_level_all_departments where stage_1_status != 2 and  days_cnt > 5 and days_cnt <= 10 $where)  as less_10",
            // "(select COUNT(complaint_category) as registered from view_level_all_departments where stage_1_status != 2 and  days_cnt > 10 and days_cnt <= 15 $where)  as less_15",
            // "(select COUNT(complaint_category) as cancelled from view_level_all_departments where stage_1_status != 2 and  days_cnt > 15 and days_cnt <= 30 $where)  as less_30",
            // "(select COUNT(complaint_category) as cancelled from view_level_all_departments where stage_1_status != 2 and  days_cnt > 30  $where) as greater_30",
            
            "Count(CASE
               WHEN stage_1_status NOT IN (2,3)
                    AND days_cnt <= 1 $where THEN complaint_category
             END) AS less_1,
       Count(CASE
               WHEN stage_1_status NOT IN (2,3)
                    AND days_cnt > 1
                    AND days_cnt <= 5 $where THEN complaint_category
             END) AS less_5,
       Count(CASE
               WHEN stage_1_status NOT IN (2,3)
                    AND days_cnt > 5
                    AND days_cnt <= 10 $where THEN complaint_category
             END) AS less_10,
       Count(CASE
               WHEN stage_1_status NOT IN (2,3)
                    AND days_cnt > 10
                    AND days_cnt <= 15 $where THEN complaint_category
             END) AS less_15,
       Count(CASE
               WHEN stage_1_status NOT IN (2,3)
                    AND days_cnt > 15
                    AND days_cnt <= 30 $where THEN complaint_category
             END) AS less_30,
       Count(CASE
               WHEN stage_1_status NOT IN (2,3)
                    AND days_cnt > 30 $where THEN complaint_category
             END) AS greater_30", 
        ];
        $table_details = [
            "view_pending_days_cnt",
            $columns
        ];
        
        //$where  = "stage_1_status != 2 ";
        
        $result = $pdo->select($table_details);
       //print_r($result);
       //die();
        if ($result->status) {
            $res_array = $result->data;
    
   
            // Initialize total count
            $total_count = 0;
    
            // Loop through the results to calculate individual category counts and total count
            foreach ($res_array as $value) {
                
                 $less_1 = $value['less_1'];
                // $less_5 = get_cnt_less_5();
                // $less_10 = get_cnt_less_10();
                // $less_15 = get_cnt_less_15();
                // $less_30 = get_cnt_less_30();
                // $greater_30 = get_cnt_greater_30();
                // $less_1 = '';
                $less_5 = $value['less_5'];
                $less_10 = $value['less_10'];
                $less_15 = $value['less_15'];
                $less_30 = $value['less_30'];
                $greater_30 = $value['greater_30'];
              
              
            }
    $total_count = $less_1 + $less_5 + $less_10 + $less_15 + $less_30 + $greater_30;
            // Construct JSON array
            $json_array = [
                "less_1" => $less_1,
                "less_5" => $less_5,
                "less_10" => $less_10,
                "less_15" => $less_15,
                "less_30" => $less_30,
                "greater_30" => $greater_30,
                "total_count" => $total_count
            ];
    
            // Encode JSON array
            echo json_encode($json_array);
        } 
       
    break;
    
    case "action_taken":

        $date = $_POST['month'];
        $columns        = [        
            "department_name",
            "priority_type",
            "'' as cnt",
        ];
    
        $table_details  = [
            'view_pending_days_cnt',
            $columns
        ];

        $where        = "entry_date != '' ".$where." GROUP BY department_name ";
        
        $result         = $pdo->select($table_details, $where);
        //  print_r($result);
    
        if ($result->status) {
    
            $res_array      = $result->data;
    
            $table_data .= ' <table class="table table-hover table-centered mb-0">
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Count</th>
                    </tr>
                </thead>
                <tbody>';
    
                if ($res_array) {
    
    
                    foreach ($res_array as $key => $value) {

                        $dept_name = department_type($value['department_name'])[0]['department_type'];
                        $count = get_pending_count($value['department_name'], $priority_type);
                        
                        if($count !=0){
    
                            $table_data .= '<tr onclick="new_external_window_print2(event,\'folders/dashboardoi/print2.php\',\'' . $value['department_name'] . '\',\'' . $value['priority_type'] . '\');">
                            <td>
                            <span class="light">' . $dept_name . '</span>
                            </td>
                            <td class="bold" style="color: #808000;" 
                                onclick="new_external_window_print2(event,\'folders/dashboardoi/print2.php\',\'' . $value['department_name'] . '\',\'' . $value['priority_type'] . '\';">' . $count . '</td>
                        </tr>';
                        }
                    }
                }
            }
            $table_data .=   '</tbody>
                            </table>';
    
        $json_array = [
            'data'            => $table_data,
        ];
    
        echo json_encode($json_array);
    break;


    case 'state_wise_map':
        $state       = [];
        $today_cnt       = [];
        
        $cummulative_cnt   = [];

        $entry_date = $_POST['month'];
        
        //print_r($newdate);
    
        $json_array     = "";

        $columns        = [
            "entry_date",
            "state_1",
            "state_2",
            "state_3",
            "state_4",
        ];
        $table_details  = [
            "view_all_state_complaints",
            $columns
        ];
        $where          = "  month = '".$entry_date."' group by entry_date order by entry_date ASC";

        $result         = $pdo->select($table_details,$where);
      //print_r($result);
        $res_array       = $result->data;
        foreach($res_array as $value)
        {
            if($value['state_1']==''){ $value['state_1'] = '0'; }
            if($value['state_2']==''){ $value['state_2'] = '0'; }
            if($value['state_3']==''){ $value['state_3'] = '0'; }
            if($value['state_4']==''){ $value['state_4'] = '0'; }
            $date[]      =   date('d-M',strtotime($value['entry_date']));
            $today_cnt[]     =   $value['total_cnt'];

            $state_1[]     =   $value['state_1'];
            $state_2[]     =   $value['state_2'];
            $state_3[]     =   $value['state_3'];
            $state_4[]     =   $value['state_4'];
        }
        
        
        
        $json_array = [
                "date"           => $date,
                "state_1"          => $state_1,
                "state_2"          => $state_2,
                "state_3"          => $state_3,
                "state_4"          => $state_4,
            ];
        
         echo json_encode($json_array);
        break;    
    

}




function get_pending_details($site_name){

    global $pdo;

    $table_name    = "complaint_creation";
    $where         = [];
    $table_columns = [
        // "(select count(complaint_no) from complaint_creation where stage_1_status = '1' GROUP BY department_name) as comp_complaint",
        "ifnull(count(complaint_no),0) as pending_complaint",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "stage_1_status != '2' and is_delete = 0 and site_name = '".$site_name."' GROUP BY department_name";
    

    $cnt_status = $pdo->select($table_details, $where);
    //echo $cnt_status;
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['pending_complaint'];
//print_r("HH".$cnt_sts);
        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_ending_date($assign_by, $department, $stage)
{
    global $pdo;

    $table_name    = "periodic_creation_sub";
    $where         = [];
    $table_columns = [
        "ending_count",
    ];

    $table_details = [
        "periodic_creation_sub",
        $table_columns
    ];

    $where  = "department_name = '" . $department . "' and stage = " . $stage . " and is_delete = 0 and form_unique_id != ''";


    $cnt_status = $pdo->select($table_details, $where);
    //echo $cnt_status;
    if (!($cnt_status->status)) {

        print_r($cnt_status);
    } else {

        if (!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['ending_count'];
        } else {
            $cnt_sts    = "";
        }
    }
    return $cnt_sts;
}

function get_overdue_cnt($department_name,$assign){

  global $pdo;



    $table_name    = "complaint_creation";
    $where         = [];
    $table_columns = [
        "count(complaint_no) as pending_complaint",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
   // $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);

    if($stage_1_ending_date == ''){
        $stage_1_ending_date = 1; 
    }
   $where  = "stage_1_status != '2' and is_delete = 0 and department_name = '".$department_name."' and assign_by = '".$assign."' and  DATEDIFF(CURDATE(), entry_date) > ".$stage_1_ending_date." GROUP BY department_name";
    

    $cnt_status = $pdo->select($table_details, $where);
    //echo $cnt_status;
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['pending_complaint'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
    
}

function get_department($user_id){
    global $pdo;

    $table_name    = "periodic_creation_sub";
    $where         = [];
    $table_columns = [
        //"GROUP_CONCAT(department_name) AS department_name",
        "GROUP_CONCAT((select department_type from department_creation where periodic_creation_sub.department_name=department_creation.unique_id)) AS department_name"
    ];

    $table_details = [
       "periodic_creation_sub",
        $table_columns
    ];

   $where  = "user_id = '".$user_id."' and  is_delete = 0 and form_unique_id != ''";
    

    $cnt_status = $pdo->select($table_details, $where);
  // print_r($cnt_status);
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['department_name'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_pending_count($department_name){
 
    
    if($_SESSION['sess_type_user'] == 1){
                $wherelsit .= " and assign_by = '".$_SESSION['user_id']."'";
            }
            
    if($_SESSION['sess_site_name'] != 'All'){
        if($_SESSION['user_id'] != '5ff562ed542d625323'){
            $rep_site = str_replace(',', "','", $_SESSION['sess_site_name']);
            $wherelsit .= " and site_name in ('".$rep_site."')";
        }
    }
    $wherelsit .=" and priority_type = '664ad1a16448664824'";
    global $pdo;

    $table_name    = "view_pending_days_cnt";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as pending_count"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "department_name = '".$department_name."' and stage_1_status = 0  $wherelsit ";
    

    $cnt_status = $pdo->select($table_details, $where);
    // print_r($cnt_status);
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['pending_count'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_cnt_less_5(){
    global $pdo;

    $table_name    = "view_pending_days_cnt";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as less_1_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = "days_cnt > 1 and days_cnt <= 5 and stage_1_status !=2";
    
    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['less_1_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_cnt_less_10(){
      global $pdo;

    $table_name    = "view_pending_days_cnt";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as less_1_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "days_cnt > 5 and days_cnt <= 10 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['less_1_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_cnt_less_15(){
      global $pdo;

    $table_name    = "view_pending_days_cnt";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as less_1_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "days_cnt > 10 and days_cnt <= 15 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['less_1_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_cnt_less_30(){
      global $pdo;

    $table_name    = "view_pending_days_cnt";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as less_1_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "days_cnt > 15 and days_cnt <= 30 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['less_1_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_cnt_greater_30(){
      global $pdo;

    $table_name    = "view_pending_days_cnt";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as greater_30_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "days_cnt > 30 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
        
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['greater_30_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}
?>
