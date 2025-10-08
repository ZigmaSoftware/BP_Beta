<?php

// Get folder Name From Currnent Url 
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// // Database Country Table Name
$table          = "complaint_creation";
$table_sub      = "complaint_creation_doc_upload";
$table_stage_1  = "stage_1";

// // Include DB file and Common Functions  
include '../../config/dbconfig.php';
include 'function.php';

// // Variables Declaration
$action = $_POST['action'];

//$user_type          = "";
$is_active = "";
$unique_id = "";
$prefix = "";

$data = "";
$msg = "";
$error = "";
$status = "";
$test = ""; // For Developer Testing Purpose
$session_user_id = $_SESSION['id'];
$session_user = $_SESSION['sess_type_user'];
$sess_user_id = $_SESSION['user_id'];

// File Upload Library Call
$fileUpload         = new Alirdn\SecureUPload\SecureUPload( $fileUploadConfig );


$fileUploadPath = $fileUploadConfig->get("upload_folder");

// Create Folder in root->uploads->(this_folder_name) Before using this file upload

if($_POST["doc_option"] == 1){  
    $file_path = "stage_1/image";
}else if($_POST["doc_option"] == 2){
    $file_path = "stage_1/document";
}else if($_POST["doc_option"] == 3){
    $file_path = "stage_1/audio";
   
}
$fileUploadConfig->set("upload_folder",$fileUploadPath. $file_path . DIRECTORY_SEPARATOR);

// if($_POST["doc_option"] == 1){  
//     $fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name."image/" . DIRECTORY_SEPARATOR);
// }else if($_POST["doc_option"] == 2){
//     $fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name."document/" . DIRECTORY_SEPARATOR);
// }else if($_POST["doc_option"] == 3){
//     $fileUploadConfig->set("upload_folder",$fileUploadPath. $folder_name."audio/" . DIRECTORY_SEPARATOR);
// }
switch ($action) {

    case 'datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_1, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];


        
            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            

            $where     .= "level = 1 and stage_1_status != 2";


        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        if ($order_column == array_search('complaint_no', $columns)) {
            $order_by .= " DESC";
        }
        // if ($_POST['search']['value']) {
        //     $where .= " AND ((complaint_no LIKE '" . mysql_like($_POST['search']['value']) . "') ";

        //     $where .= " OR (department_name in (" . department_name_like($_POST['search']['value']) . ")) ";

        //     $where .= " OR (site_name in (" . site_name_like($_POST['search']['value']) . ")) ";

        //     $where .= " OR (plant_name in (" . plant_name_like($_POST['search']['value']) . "))) ";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        

        $sql_function   = "SQL_CALC_FOUND_ROWS";

// print_r(user_name_like($searchValue));
        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    //   print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
                    $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 1){

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];
                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 1</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");




                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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
        
        
        case 'demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_1",
            $columns
        ];
        
        


        
            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);

        $where     .= "level = 1 and stage_1_status != 2";


        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }

        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //  print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
            $i = 1;
            foreach ($res_array as $key => $value) {
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 1){
        
                    $value['s_no'] = $i++;
                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];






                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 1</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");




                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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

    case 'stage_2_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
       

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_2, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level_name = periodic_username_like($sess_user_id);
            
    
            $where     .= "level = 2 and stage_1_status != 2";
            
        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
    if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
            $level_test = explode(",",$get_level);
         
        foreach($level_test as $level){
            if($level == 2){

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 2</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
        }
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
case 'stage_2_demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_2",
            $columns
        ];


        
        $get_dept_name = get_dept_priodic($sess_user_id);
  
         $get_site_name = get_site_priodic($sess_user_id);
         
         $get_level     = periodic_username_like($sess_user_id);
         
        $where     .= "level = 2 and stage_1_status != 2";
// session_start();
// print_r($_SESSION['user_type_unique_id']);die();
// if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
//             if($get_dept_name != 'All'){
//                 $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
//             }
//         }
//         if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
//             if($get_site_name != 'All'){
//                 $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
//             }
//         }
        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }


if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                            // print_r($_SESSION['user_type_unique_id']);die();
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }

        // if ($_SESSION['sess_department_name'] != 'All') {
        //     if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //         $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
        //     }else{
        //         $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
        //     }
        // }

        // if ($_SESSION['sess_site_name'] != 'All') {
        //     if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //         $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
        //     }
        //     else{
        //         $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
        //     }
        // }
        
        

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }


        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        
        // print_r($result);die();
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 2){
                    $value['s_no'] = $i++;
                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 2</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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
        
    case 'stage_3_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_3, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];


        

        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
        
          $where     .= "level = 3 and stage_1_status != 2";

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }
    
        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
            $level_test = explode(",",$get_level);
         
        foreach($level_test as $level){
                if($level == 3){

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 3</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
        }    
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
        
            case 'stage_3_demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_3",
            $columns
        ];


        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
             $get_level     = periodic_username_like($sess_user_id);
             
             $where     .= "level = 3 and stage_1_status != 2";



        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        
        
         
        

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 3){
        
                    $value['s_no'] = $i++;
                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 3</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
                
            }
            
    }
    
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


    case 'stage_4_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_4, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];

            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
           
        $where     .= "level = 4 and stage_1_status != 2";


        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
       $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

     $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 4){
         
                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 4</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
        }
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
        
            case 'stage_4_demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_4",
            $columns
        ];


        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
             $get_level     = periodic_username_like($sess_user_id);
             
                     $where     .= "level = 4 and stage_1_status != 2";


        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }


        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 4){
                    $value['s_no'] = $i++;
                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 4</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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


    case 'stage_5_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",

        ];
        $table_details  = [
            "view_level_5, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];


        

        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
           
        $where     .= "level = 5 and stage_1_status != 2";

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }
    
        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }



        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
        $level_test = explode(",",$get_level);
         
            foreach($level_test as $level){
                if($level == 5){

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 5</b>";


                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");


                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
        }
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
        
            case 'stage_5_demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",

        ];
        $table_details  = [
            "view_level_5",
            $columns
        ];


        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
             $get_level     = periodic_username_like($sess_user_id);
             
                     $where     .= "level = 5 and stage_1_status != 2";
                         

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }

        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 5){
                    $value['s_no'] = $i++;

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 5</b>";


                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");


                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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


    case 'stage_6_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_6, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];


        

        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
            
            $where     .= "level = 6 and stage_1_status != 2";
            
        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
       if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }
    
        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
        $level_test = explode(",",$get_level);
         
        foreach($level_test as $level){
            if($level == 6){

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;

                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];

                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 6</b>";
                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");




                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
        }
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
        
            case 'stage_6_demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",


        ];
        $table_details  = [
            "view_level_6",
            $columns
        ];


        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
          $get_level     = periodic_username_like($sess_user_id);
          
                  $where     .= "level = 6 and stage_1_status != 2";


        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }


        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 6){
                    $value['s_no'] = $i++;

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;

                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 6</b>";
                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");




                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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

    case 'stage_7_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",

        ];
        $table_details  = [
            "view_level_7, (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
            

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //         $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        //     }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }
        
            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
           
            $where     .= "level = 7 and stage_1_status != 2";

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }
    
        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }


        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
        $level_test = explode(",",$get_level);
         
            foreach($level_test as $level){
                if($level == 7){


                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 7</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
        }
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
        
            case 'stage_7_demo_datatable':
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables 
        $json_array     = "";
        $columns        = [
            "'' as s_no",
            "entry_date",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            // "stage_1_description",
            "unique_id",
            "plant_name",
            "complaint_category",
            "complaint_no",
            "level",

        ];
        $table_details  = [
            "view_level_7",
            $columns
        ];


        $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
              $get_level     = periodic_username_like($sess_user_id);
              
              $where     .= "level = 7 and stage_1_status != 2";



        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }


        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
            }
        }

        // if (($_POST['from_date'])&&($_POST['to_date'])) {
        //     $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        // }


        // if ($_POST['department_type']) {
        //     $where .= " AND department_name = '" . $_POST['department_type'] . "' ";
        // } 

        // if ($_POST['complaint_name']) {
        //     $where .= " AND complaint_category = '" . $_POST['complaint_name'] . "' ";
        // }

        // if ($_POST['state_name']) {
        //     $where .= " AND state_name = '" . $_POST['state_name'] . "' ";
        // }

        // if ($_POST['site_name']) {
        //     $where .= " AND site_name = '" . $_POST['site_name'] . "' ";
        // }



        // if ($_POST['status_name'] != '') {
        //     $where .= " AND stage_1_status = '" . $_POST['status_name'] . "' ";
        // }

        // if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //     $where .= " and department_name = '" . $_SESSION['sess_department_name'] . "'";
        // }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                $level_test = explode(",",$get_level);
         
foreach($level_test as $level){
    if($level == 7){
                    $value['s_no'] = $i++;

                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);

                $current_date =  $diff->format("%a");

                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                        break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);

                $value['entry_date']            = today_time($value['entry_date']);
                $btn_update                      = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];



                $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                $value['stage_1_description']             = "<b>Level 7</b>";

                $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");



                $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                $data[]                         = array_values($value);
            }
    }
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


        case 'all_level_datatable':
            // DataTable Variables
            $search     = $_POST['search']['value'];
            $length     = $_POST['length'];
            $start      = $_POST['start'];
            $draw       = $_POST['draw'];
            $limit      = $length;
    
            $data       = [];
    
            if ($length == '-1') {
                $limit  = "";
            }
    
            // Query Variables 
            $json_array     = "";
            $columns        = [
                "@a:=@a+1 s_no",
                "entry_date",
                "site_name",
                "department_name",
                "complaint_description",
                "priority_type",
                "assign_by",
                "stage_1_status",
                "unique_id",
                "plant_name",
                "complaint_category",
                "complaint_no",
                
                "stage_1_description",
                
    
            ];
            $table_details  = [
                "view_pending_days_cnt, (SELECT @a:= " . $start . ") AS a ",
                $columns
            ];
    
            
    
             $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
//             $level_test = explode(",",$get_level);
         
// foreach($level_test as $level){
//     if($level == all){
            $where .= "complaint_no != '' AND stage_1_status != 2 AND stage_1_status NOT IN (3)";

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
        
    //     if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
    //     if ($_POST['department_name']) {
    //         $where .= " AND department_name = '".$_POST['department_name']."' ";
    //     }
    // }

    //     if ($_POST['complaint_name']) {
    //         $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
    //     }
        
    // if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_site_name'] == "All"){
    
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
    //     }else{
    //         $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
    //     }
    // }else{
        
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
        
    // }

    //     if ($_POST['status_name'] != '') {
    //         $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
    //     }
            
            
        
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_dept_name != 'All'){
    //             $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
    //         }
    //     }
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_site_name != 'All'){
    //             $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
    //         }
    //     }
    
    
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
            }else{
                $where .=" and department_name != ''";
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }else{
             $where .= " and site_name != ''";   
            }
        }else{
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
            }
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
                
        // if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
        //     if($get_dept_name != 'All'){
        //         $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
        //     }
        // }
        // if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
        //     if($get_site_name != 'All'){
        //         $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
        //     }
        // }
        
        // if ($_SESSION['sess_department_name'] != 'All') {
        //     if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //         $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
        //     }
        // }

        // if ($_SESSION['sess_site_name'] != 'All') {
        //     if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        //         $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
        //     }
        // }
            //$order_by       = "entry_date DESC";
            $order_column   = $_POST["order"][0]["column"];
            $order_dir      = $_POST["order"][0]["dir"];
    
            // Datatable Ordering 
            $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        //     if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
        
        
        
            $where .= " ORDER BY complaint_no DESC";
            $sql_function   = "SQL_CALC_FOUND_ROWS";
    
            $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
         //print_r($result);
            $total_records  = total_records();
    
            if ($result->status) {
    
                $res_array      = $result->data;
    
                foreach ($res_array as $key => $value) {
    
    
                    $ent_date = date('Y-m-d');
                    $date1 = date_create($value['entry_date']);
                    $date2 = date_create($ent_date);
                    $diff = date_diff($date1, $date2);
    
                    $current_date =  $diff->format("%a");
    
                    switch ($value['stage_1_status']) {
                        case 1:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                            break;
                        case 2:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                            break;
                        case 3:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                            break;
                        default:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                            break;
                    }
    
                    $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                    $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                    $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                    $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                    $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                    $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                    $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);
    
                    $value['entry_date']            = today_time($value['entry_date']);
                    $btn_update                      = btn_update($folder_name, $value['unique_id']);
                    $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                    $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                    $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                    $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                    $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                    $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                    $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];
                    $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                    $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                    $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                    // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                    $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                    $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                    $value['stage_1_description']             = "<b>Level 7</b>";
    
                    $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");
    
    
    
                    $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                    $value['unique_id']             = $btn_view;
                    $data[]                         = array_values($value);
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
//     }
// }
    
            echo json_encode($json_array);
            break;
            
            
            
case 'all_level_demo_datatable':
            // DataTable Variables
            $search     = $_POST['search']['value'];
            $length     = $_POST['length'];
            $start      = $_POST['start'];
            $draw       = $_POST['draw'];
            $limit      = $length;
    
            $data       = [];
    
            if ($length == '-1') {
                $limit  = "";
            }
    
            // Query Variables 
            $json_array     = "";
            $columns        = [
                "'' as s_no",
                "entry_date",
                "site_name",
                "department_name",
                "complaint_description",
                "priority_type",
                "assign_by",
                "stage_1_status",
                "stage_1_description",
                "unique_id",
                "plant_name",
                "complaint_category",
                "complaint_no",
                "level",
    
            ];
            $table_details  = [
                "view_level_all_departments",
                $columns
            ];
    
    
            $where     .= "complaint_no != '' and stage_1_status != 2";
    
            if ($session_user == 1) {
                $where .= " and assign_by = '$sess_user_id'";
            }
    
    
            if ($_SESSION['sess_department_name'] != 'All') {
                if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                    $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
                }
            }
    
            if ($_SESSION['sess_site_name'] != 'All') {
                if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                    $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
                }
            }
    
            //$order_by       = "entry_date DESC";
            $order_column   = $_POST["order"][0]["column"];
            $order_dir      = $_POST["order"][0]["dir"];
    
            // Datatable Ordering 
            $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        //     if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
    
            $sql_function   = "SQL_CALC_FOUND_ROWS";
    
            $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
            $total_records  = total_records();
    
            if ($result->status) {
    
                $res_array      = $result->data;
$i = 1;
            foreach ($res_array as $key => $value) {
                    $value['s_no'] = $i++;
    
                    $ent_date = date('Y-m-d');
                    $date1 = date_create($value['entry_date']);
                    $date2 = date_create($ent_date);
                    $diff = date_diff($date1, $date2);
    
                    $current_date =  $diff->format("%a");
    
                    switch ($value['stage_1_status']) {
                        case 1:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                            break;
                        case 2:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                            break;
                        case 3:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                            break;
                        default:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                            break;
                    }
    
                    $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                    $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                    $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                    $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                    $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                    $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                    $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);
    
                    $value['entry_date']            = today_time($value['entry_date']);
                    $btn_update                      = btn_update($folder_name, $value['unique_id']);
                    $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                    $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                    $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                    $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                    $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                    $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                    $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];
    
    
    
                    $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                    $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                    $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                    // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                    $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                    $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                    $value['stage_1_description']             = "<b>Level 7</b>";
    
                    $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");
    
    
    
                    $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                    $value['unique_id']             = $btn_view;
                    $data[]                         = array_values($value);
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
            
    
            
    case 'own_call_datatable':
            // DataTable Variables
            $search     = $_POST['search']['value'];
            $length     = $_POST['length'];
            $start      = $_POST['start'];
            $draw       = $_POST['draw'];
            $limit      = $length;
            $data       = [];
    
            if ($length == '-1') {
                $limit  = "";
            }
    
            // Query Variables 
            $json_array     = "";
            $columns        = [
                "@a:=@a+1 s_no",
                "entry_date",
                "site_name",
                "department_name",
                "complaint_description",
                "priority_type",
                "assign_by",
                "stage_1_status",
                "stage_1_description",
                "unique_id",
                "plant_name",
                "complaint_category",
                "complaint_no",
                // "level",
            ];
            
            $table_details  = [
                "view_pending_days_cnt, (SELECT @a:= " . $start . ") AS a ",
                $columns
            ];
            
            $where     .= "complaint_no != '' and stage_1_status NOT IN ('2','3')";
            $where .= " and assign_by = '$sess_user_id'";
            
            $get_dept_name = get_dept_priodic($sess_user_id);
            $get_site_name = get_site_priodic($sess_user_id);
            
            
            // if ($_SESSION["user_id"]=='5ff562ed542d625323'){
            //     $where .= "";
            // } else {
                
            // }


        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];
    
        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        // if ($_POST['search']['value']) {
        //     $where .= " AND (complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //     $where .= " OR site_name LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR department_name LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR plant_name LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR complaint_category LIKE '".mysql_like($_POST['search']['value'])."' ";
        //     $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."')";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
    
        $sql_function   = "SQL_CALC_FOUND_ROWS";
    
        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    //   print_r($result);
        $total_records  = total_records();
    
        if ($result->status) {
            $res_array      = $result->data;
    
        foreach ($res_array as $key => $value) {
                $ent_date = date('Y-m-d');
                $date1 = date_create($value['entry_date']);
                $date2 = date_create($ent_date);
                $diff = date_diff($date1, $date2);
                $current_date =  $diff->format("%a");
    
                switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                    break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                    break;
                    case 3:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                    break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                    break;
                }
    
                    $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                    $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                    $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                    $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                    $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                    $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                    $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);
    
                    $value['entry_date']            = today_time($value['entry_date']);
                    $btn_update                      = btn_update($folder_name, $value['unique_id']);
                    $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                    $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                    $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                    $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                    $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                    $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                    $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];
                    $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                    $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                    $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                    $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                    $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                    $value['stage_1_description']             = "<b>Level 7</b>";
                    $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");

                    $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                    $value['unique_id']             = $btn_view;
                    $data[]                         = array_values($value);
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
            
        case 'tag_person_datatable':
            // DataTable Variables
            $search     = $_POST['search']['value'];
            $length     = $_POST['length'];
            $start      = $_POST['start'];
            $draw       = $_POST['draw'];
            $limit      = $length;
    
            $data       = [];
    
            if ($length == '-1') {
                $limit  = "";
            }
    
            // Query Variables 
            $json_array     = "";
            $columns        = [
                "''as s_no",
                "v.entry_date",
                "v.site_name",
                "v.department_name",
                "v.complaint_description",
                "v.priority_type",
                "v.assign_by",
                "v.stage_1_status",
                // "v.stage_1_description",
                "v.unique_id",
                "v.plant_name",
                "v.complaint_category",
                "v.complaint_no",
                "s.stage",
    
            ];
            $table_details  = [
                "view_pending_days_cnt as v JOIN stage_1 as s on v.screen_unique_id = s.screen_unique_id",
                $columns
            ];
    
    
           
    
            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);
            
//             $level_test = explode(",",$get_level);
         
// foreach($level_test as $level){
//     if($level == tag){
             $where     .="v.complaint_no != '' and v.stage_1_status != 2 ";

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        }
        
    //     if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
    //     if ($_POST['department_name']) {
    //         $where .= " AND department_name = '".$_POST['department_name']."' ";
    //     }
    // }

    //     if ($_POST['complaint_name']) {
    //         $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
    //     }
        
    // if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_site_name'] == "All"){
    
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
    //     }else{
    //         $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
    //     }
    // }else{
        
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
        
    // }

    //     if ($_POST['status_name'] != '') {
    //         $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
    //     }
            
            
        
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_dept_name != 'All'){
    //             $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
    //         }
    //     }
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_site_name != 'All'){
    //             $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
    //         }
    //     }
    
    // if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
        }
    // }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
        }
        
        if($_POST['priority'] != '') {
            $where .= " AND priority_type = '".$_POST['priority']."' ";
        }
        
    // if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_site_name'] == "All"){
    
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
    //     }else{
    //         $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
    //     }
    // }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
            }
        
    // }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }
                
            
        
        // if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
        //     if($get_dept_name != 'All'){
        //         $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
        //     }
        // }
        // if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
        //     if($get_site_name != 'All'){
        //         $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
        //     }
        // }
    
    
    
    
    //print_r($_SESSION);
        if($_SESSION['user_id'] !='5ff562ed542d625323'){
            $where .= "and FIND_IN_SET('".$sess_user_id."',s.user_name_select)";
        }else{
            $where .= " and s.user_name_select != ''";
        }
        $where .= " and s.is_delete = '0'";
        
            //$order_by       = "entry_date DESC";
            $order_column   = $_POST["order"][0]["column"];
            $order_dir      = $_POST["order"][0]["dir"];
    
            // Datatable Ordering 
            $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        //     if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
            
            $where     .="GROUP BY v.screen_unique_id";
    
            $sql_function   = "SQL_CALC_FOUND_ROWS";
    
            $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
         //print_r($result);
            $total_records  = total_records();
    
            if ($result->status) {
    
                $res_array      = $result->data;
    $i = 1;
            foreach ($res_array as $key => $value) {
                    $value['s_no'] = $i++; 
    // foreach ($res_array as $key => $value) {
                
                    // $level_test = explode(",",$get_level);
    
                    $ent_date = date('Y-m-d');
                    $date1 = date_create($value['entry_date']);
                    $date2 = date_create($ent_date);
                    $diff = date_diff($date1, $date2);
    
                    $current_date =  $diff->format("%a");
    
                    switch ($value['stage_1_status']) {
                        case 1:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                            break;
                        case 2:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                            break;
                        case 3:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                            break;
                        default:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                            break;
                    }
    
                    $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                    $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                    $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                    $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                    $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                    $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                    $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);
    
                    $value['entry_date']            = today_time($value['entry_date']);
                    $btn_update                      = btn_update($folder_name, $value['unique_id']);
                    $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                    $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                    $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                    $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                    $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                    $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                    $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];
    
    
    
                    $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                    $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                    $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                    // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                    $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                    $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                    $value['stage_1_description']             = "<b>Level 7</b>";
    
                    $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");
    
    
    
                    $btn_view                       = btn_view($folder_name, $value['unique_id'] . $action_btn);
                    $value['unique_id']             = $btn_view;
                    $data[]                         = array_values($value);
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
//     }
// }
    
            echo json_encode($json_array);
            break;
            
            
            
        case 'tag_person_demo_datatable':
            // DataTable Variables
            $search     = $_POST['search']['value'];
            $length     = $_POST['length'];
            $start      = $_POST['start'];
            $draw       = $_POST['draw'];
            $limit      = $length;
    
            $data       = [];
    
            if ($length == '-1') {
                $limit  = "";
            }
    
            // Query Variables 
            $json_array     = "";
            $columns        = [
                "'' as s_no",
                "v.entry_date",
                "v.site_name",
                "v.department_name",
                "v.complaint_description",
                "priority_type",
                "v.assign_by",
                "v.stage_1_status",
                "v.stage_1_description",
                "v.unique_id",
                "v.plant_name",
                "v.complaint_category",
                "v.complaint_no",
                "v.level",
    
            ];
            $table_details  = [
                "view_level_all_departments as v JOIN stage_1 as s on v.screen_unique_id = s.screen_unique_id",
                $columns
            ];
    
    
            $where     .="v.complaint_no != '' and v.stage_1_status != 2 ";
    
            // $get_dept_name = get_dept_priodic($sess_user_id);
  
            // $get_site_name = get_site_priodic($sess_user_id);

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
        }
        
    //     if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
    //     if ($_POST['department_name']) {
    //         $where .= " AND department_name = '".$_POST['department_name']."' ";
    //     }
    // }

    //     if ($_POST['complaint_name']) {
    //         $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
    //     }
        
    // if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_site_name'] == "All"){
    
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
    //     }else{
    //         $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
    //     }
    // }else{
        
    //         if ($_POST['site_name']) {
    //             $where .= " AND site_name = '".$_POST['site_name']."' ";
    //         }
        
    // }

    //     if ($_POST['status_name'] != '') {
    //         $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
    //     }
            
            
        
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_dept_name != 'All'){
    //             $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
    //         }
    //     }
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_site_name != 'All'){
    //             $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
    //         }
    //     }
    
    //print_r($_SESSION);
        if($_SESSION['user_id'] !='5ff562ed542d625323'){
            $where .= "and FIND_IN_SET('".$sess_user_id."',s.user_name_select)";
        }else{
            $where .= " and s.user_name_select != ''";
        }
            //$order_by       = "entry_date DESC";
            $order_column   = $_POST["order"][0]["column"];
            $order_dir      = $_POST["order"][0]["dir"];
    
            // Datatable Ordering 
            $order_by       = datatable_sorting($order_column, $order_dir, $columns);
        //     if ($_POST['search']['value']) {
        //         $where .= " AND ((complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";
        //         $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_category in (".complaint_category_like($_POST['search']['value']).")) ";
        //         $where .= " OR (complaint_description LIKE '".mysql_like($_POST['search']['value'])."'))";
        // }
        
        if (!empty($_POST['search']['value'])) {
            $searchValue = mysql_like($_POST['search']['value']);
        
            $where .= " AND ((complaint_no LIKE '$searchValue') ";
            $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
            $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
            $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
            $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
            $where .= " OR (complaint_description LIKE '$searchValue') ";
            $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
            $where .= " )"; 
        }
        
            
            $where     .="GROUP BY v.screen_unique_id";
    
            $sql_function   = "SQL_CALC_FOUND_ROWS";
    
            $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
         //print_r($result);
            $total_records  = total_records();
    
            if ($result->status) {
    
                $res_array      = $result->data;
    $i=1;
                foreach ($res_array as $key => $value) {
      $value['s_no'] = $i++; 
    
                    $ent_date = date('Y-m-d');
                    $date1 = date_create($value['entry_date']);
                    $date2 = date_create($ent_date);
                    $diff = date_diff($date1, $date2);
    
                    $current_date =  $diff->format("%a");
    
                    switch ($value['stage_1_status']) {
                        case 1:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>Progressing</span>";
                            break;
                        case 2:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Completed</span>";
                            break;
                        case 3:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:Red'>Cancel</span>";
                            break;
                        default:
                            $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                            break;
                    }
    
                    $stage_1_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 1);
                    $stage_2_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 2);
                    $stage_3_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 3);
                    $stage_4_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 4);
                    $stage_5_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 5);
                    $stage_6_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 6);
                    $stage_7_ending_date  = get_ending_date($value['assign_by'], $value['department_name'], 7);
    
                    $value['entry_date']            = today_time($value['entry_date']);
                    $btn_update                      = btn_update($folder_name, $value['unique_id']);
                    $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                    $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                    $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                    $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                    $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                    $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                    $value['complaint_category']    = category_creation($value['complaint_category'])[0]['category_name'];
    
    
    
                    $value['site_name']             =   "<b>" . $value['site_name'] . " </b><br><span style=font-size:12px;>" . $value['plant_name'] . '</span>';
                    $value['department_name']       =   "<b>" . $value['department_name'] . " </b><br><span style=font-size:12px;>" . $value['complaint_category'] . '</span>';
                    $value['entry_date']            =   "<b>" . $value['complaint_no'] . " </b><br><span style=font-size:12px;>" . $value['entry_date'] . '</span>';
                    // $value['assign_by']     = "<b>".$value['assign_by']." </span> </b><br><span style='font-size:12px;'>".$current_date." Days</span>";
                    $value['assign_by']     = "<b>" . $current_date . " Days</b><br><span style='font-size:12px;'>" . $value['assign_by'] . " </span>";
                    $value['stage_1_status']             = "<b>" . $stage_status . "</b><br><span style='font-size:12px;'>" . $value['stage'] . " </span>";
                    $value['stage_1_description']             = "<b>Level 7</b>";
    
                    $value['complaint_description'] = wordwrap($value['complaint_description'], 50, "<br>");
    
    
    
                    $btn_view                       = '<a href="../../../g_app/stage1_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
                $value['unique_id']             = $btn_view;
                    $data[]                         = array_values($value);
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


    case 'document_upload_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete = "document_upload_sub";

        // Fetch Data
        $unique_id = $_POST['unique_id'];
        $screen_unique_id = $_POST['screen_unique_id'];

        // DataTable 
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start = $_POST['start'];
        $draw = $_POST['draw'];
        $limit = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // Query Variables
        $json_array = "";
        $columns = [
            "@a:=@a+1 s_no",
            "doc_name",
            "file_name",
            "unique_id"
        ];
        $table_details = [
            $table_sub . " , (SELECT @a:= '" . $start . "') AS a ",
            $columns
        ];

        $where = [
            //"purchase_unique_id"            => $unique_id,
            "screen_unique_id" => $screen_unique_id,
            "is_active" => 1,
            "is_delete" => 0
        ];


        $order_by = "";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_delete = btn_delete($btn_edit_delete, $value['unique_id']);


                $value['file_name'] = image_view("complaint_category", $value['unique_id'], $value['file_name']);


                $data[] = array_values($value);
            }

            $json_array = [
                "draw" => intval($draw),
                "recordsTotal" => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data" => $data,
                "testing" => $result->sql,

            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;
        
        case 'app_document_upload_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete = "document_upload_sub";

        // Fetch Data
        $unique_id = $_POST['unique_id'];
        $screen_unique_id = $_POST['screen_unique_id'];

        // DataTable 
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start = $_POST['start'];
        $draw = $_POST['draw'];
        $limit = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // Query Variables
        $json_array = "";
        $columns = [
            "@a:=@a+1 s_no",
            "doc_name",
            "file_name",
            "unique_id"
        ];
        $table_details = [
            $table_sub . " , (SELECT @a:= '" . $start . "') AS a ",
            $columns
        ];

        $where = [
            //"purchase_unique_id"            => $unique_id,
            "screen_unique_id" => $screen_unique_id,
            "is_active" => 1,
            "is_delete" => 0
        ];


        $order_by = "";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        // print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_delete = btn_delete($btn_edit_delete, $value['unique_id']);


                $value['file_name'] = image_view("complaint_category", $value['unique_id'], $value['file_name']);


                $data[] = array_values($value);
            }

            $json_array = [
                "draw" => intval($draw),
                "recordsTotal" => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data" => $data,
                "testing" => $result->sql,

            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;
    case 'all_counts':
           
            // Query Variables 
            $json_array     = "";
            $columns        = [
                 "COUNT(DISTINCT screen_unique_id) as all_cnt"
                
            ];
            $table_details  = [
                "view_pending_days_cnt ",
                $columns
            ];
    
    
            $where     .= "complaint_no != '' and stage_1_status != 2 AND stage_1_status NOT IN (3) ";
    
            

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
            }
  
    
        if ($_SESSION['sess_department_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(department_name,'" . $_SESSION['sess_department_name'] . "')";
            }
        }

        if ($_SESSION['sess_site_name'] != 'All') {
            if ($_SESSION['user_id'] != '5ff562ed542d625323') {
                $where .= " and FIND_IN_SET(site_name,'" . $_SESSION['sess_site_name'] . "')";
            }
        }

           $result         = $pdo->select($table_details, $where);
         //print_r($result);
            $total_records  = total_records();
    
            if ($result->status) {
    
                $res_array      = $result->data;
    
                foreach ($res_array as $key => $value) {
    
    
                   $all_cnt    = $value['all_cnt'];
                }
    
                $json_array = [
                "all_cnt" => $all_cnt,
                
                
            ];
            }
    
            // Encode JSON array
            echo json_encode($json_array);
            break;
          
    
    
    case 'own_call_counts':
        
        $json_array = "";
        
        $columns = [
            "COUNT(DISTINCT screen_unique_id) as call_cnt"
        ];
            
        $table_details = [
            "view_pending_days_cnt", 
            $columns
        ];
        
        $where = "complaint_no != '' and stage_1_status NOT IN ('2','3')";
        $where .= " and assign_by = '$sess_user_id'";
        
        $result = $pdo->select($table_details, $where);
        
        $total_records = total_records();
        
        if ($result->status) {
            $res_array = $result->data;
            
            foreach ($res_array as $key => $value) {
                $call_cnt = $value['call_cnt'];
            }
            
            $json_array = [
                "call_cnt" => $call_cnt,
            ];
        }
        
        echo json_encode($json_array);
    break;
    
    
    case 'status_sub_add_update':
        
        $status_option          = $_POST["status_option"];
        $remark_type            = $_POST["remark_type"];
        $status_description     = $_POST["status_description"];
        $doc_option             = $_POST["doc_option"];
        $date_time              = date('Y-m-d H:i:s');
        $approve_by             = $_SESSION['user_id'];
        $user_name_select       = $_POST["user_name_select"];
        $screen_unique_id       = $_POST["screen_unique_id"];
        $unique_id              = $_POST["unique_id"];
        $update_where           = "";
        
        if ($status_option == 2) {
            $remark_type = '';
        }
        
        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                // Multi file Upload 
                $allowedFormats = ['jpg', 'jpeg', 'png', 'pdf', 'xls', 'xlsx', 'txt', 'docx', 'doc',"mp3", "mp4", "wma", "wav"];
                $confirm_upload     = $fileUpload->uploadFiles("test_file");
                // if (in_array($confirm_upload, $allowedFormats)) {
                if (is_array($confirm_upload)) {
                    $_FILES["test_file"]['file_name'] = [];
                        
                    foreach ($confirm_upload as $c_key => $c_value) {
                        if ($c_value->status == 1) {
                           $c_file_name = $c_value->name ? $c_value->name.".".$c_value->ext : "";
                            array_push($_FILES["test_file"]['file_name'],$c_file_name);
                        }else {   
                        // if Any Error Occured in File Upload Stop the loop
                        $status     = $confirm_upload->status;
                        $data       = "file not uploaded";
                        $error      = $confirm_upload->error;
                        $sql        = "file upload error";
                        $msg        = "file_error";
                        break;
                        }
                    }  
                        // }
                }
            }else if (!empty($_FILES["test_file"]['name'])) {// Single File Upload
                $confirm_upload     = $fileUpload->uploadFile("test_file");
                        
                if($confirm_upload->status == 1) {
                    $c_file_name = $confirm_upload->name ? $confirm_upload->name.".".$confirm_upload->ext : "";
                            $_FILES["test_file"]['file_name']  = $c_file_name;
                } else {// if Any Error Occured in File Upload Stop the loop
                   $status     = $confirm_upload->status;
                   $data       = "file not uploaded";
                   $error      = $confirm_upload->error;
                   $sql        = "file upload error";
                   $msg        = "file_error";
                }                    
            }
        }
    // }

        if (is_array($_FILES["test_file"]['name'])) {
            if ($_FILES["test_file"]['name'][0] != "") {
                $file_names     = implode(",",$_FILES["test_file"]['file_name']);
                $file_org_names = implode(",",$_FILES["test_file"]['name']);
            }                            
        } else if (!empty($_FILES["test_file"]['name'])) {
            $file_names     = $_FILES["test_file"]['file_name'];
            $file_org_names = $_FILES["test_file"]['name'];
        }

        $columns            = [
            "status_option"         => $status_option,
            "remark_type"           => $remark_type,
            "status_description"    => $status_description,
            "date_time"             => $date_time,
            "doc_name"              => $doc_option,
            "file_name"             => $file_names,
            "user_name_select"      => $user_name_select,
            // "file_org_name"         => $file_org_names,
            "approve_by"            => $approve_by,
            "stage"                 => "Level 1",

            "screen_unique_id"  => $screen_unique_id,
            "unique_id"         => unique_id($prefix)
        ];
// print_r($columns);die();

        $table_details = [
            $table,
            [
                "stage_1_status"
            ]
        ];

        $select_where = ' is_delete = 0 AND  screen_unique_id ="' . $screen_unique_id . '" ';
        
        $action_obj = $pdo->select($table_details, $select_where);

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
        if ($data[0]["stage_1_status"] == 2) {
            if ($_POST["status_option"] == 4) {
                $action_obj     = $pdo->insert($table_stage_1, $columns);
// print_r($action_obj); die();
                $mainlist_columns  = [
                    "stage_1_status"        => $_POST["status_option"],
                    "stage_1_description"   => $_POST["status_description"],
                    "stage_1_update_date"   => date('Y-m-d H:i:s'),
                    "stage_1_approve_by"    => $_SESSION['user_id'],
                    "stage"                 => "Stage 1",
                    "reopen_date"           => $_POST['entry_date'],
                    "entry_date"            => date('Y-m-d'),
                    "reopen_by"             => $_SESSION['user_id'],

                ];

                $where_mainlist = [
                    "screen_unique_id"  => $_POST["screen_unique_id"],
                    "is_active"         => 1,
                    "is_delete"         => 0,

                ];

                $action_obj_main   = $pdo->update($table, $mainlist_columns, $where_mainlist);

                if ($action_obj->status) {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = "";
                    $sql        = $action_obj->sql;

                    if ($unique_id) {
                        $msg        = "update";
                    } else {
                        $msg        = "add";
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
            } else {
                $msg        = "already";
            }
        } else if ($data[0]["stage_1_status"] != 2) {

            // Insert Begins            

            $action_obj     = $pdo->insert($table_stage_1, $columns);

            $mainlist_columns  = [
                "stage_1_status"        => $_POST["status_option"],
                "stage_1_description"   => $_POST["status_description"],
                "stage_1_update_date"   => date('Y-m-d H:i:s'),
                "stage_1_approve_by"    => $_SESSION['user_id'],
                "stage"                 => "Stage 1",

            ];

            $where_mainlist = [
                "screen_unique_id"  => $_POST["screen_unique_id"],
                "is_active"         => 1,
                "is_delete"         => 0,

            ];

            $action_obj_main   = $pdo->update($table, $mainlist_columns, $where_mainlist);
            // Insert Ends

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
            // }


            $json_array   = [
                "status"    => $status,
                "data"      => $data,
                "error"     => $error,
                "msg"       => $msg,
                "sql"       => $sql
            ];
        } else {

            // Insert Begins            

            $action_obj     = $pdo->insert($table_stage_1, $columns);

            $mainlist_columns  = [
                "stage_1_status"        => $_POST["status_option"],
                "stage_1_description"   => $_POST["status_description"],
                "stage_1_update_date"   => date('Y-m-d H:i:s'),
                "stage_1_approve_by"    => $_SESSION['user_id'],
                "stage"                 => "Stage 1",

            ];

            $where_mainlist = [
                "screen_unique_id"  => $_POST["screen_unique_id"],
                "is_active"         => 1,
                "is_delete"         => 0,

            ];

            $action_obj_main   = $pdo->update($table, $mainlist_columns, $where_mainlist);
            // Insert Ends





            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
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
    case 'mobile_sub_add_update':

        $status_option              = $_REQUEST["status_option"];
        $remark_type                = $_POST["remark_type"];
        $status_description         = $_REQUEST["status_description"];
        $date_time                  = date('Y-m-d H:i:s');
        $approve_by                 = $_REQUEST['user_id'];
        $doc_option                 = $_REQUEST['doc_option'];
        $user_name_select           = $_POST["user_name_select"];

        $screen_unique_id           = $_REQUEST["screen_unique_id"];
        $unique_id                  = $_REQUEST["unique_id"];


        $update_where               = "";
        if ($status_option == 2) {
            $remark_type = '';
        }


        if ($_REQUEST["doc_option"] == 1) {
            $allowedExts = array("jpeg", "png", "jpg");
            $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
            // print_r($extension);
            if ((($_FILES["test_file"]["type"] == "image/jpeg") || ($_FILES["test_file"]["type"] == "image/png") || ($_FILES["test_file"]["type"] == "image/jpg"))) {



                $file_exp = explode(".", $_FILES["test_file"]['name']);
                $tem_name =  random_strings(25) . "." . $file_exp[1];

                move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/stage_1/image/" . $tem_name);
            }
            if (!empty($_FILES["test_file"]['name'])) {
                $file_names     = $tem_name;
                $file_org_names = $_FILES["test_file"]['name'];
            }
        }

        if ($_REQUEST["doc_option"] == 2) {
            // print_r($document_name);

            $allowedExts = array("xls", "xlsx", "pdf");
            $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
            // print_r($extension);
            if ((($_FILES["test_file"]["type"] == "application/pdf") || ($_FILES["test_file"]["type"] == "application/vnd.ms-excel") || ($_FILES["test_file"]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') || ($_FILES["test_file"]["type"] == "application/xls"))) {



                $file_exp = explode(".", $_FILES["test_file"]['name']);
                $tem_name =  random_strings(25) . "." . $file_exp[1];

                move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/stage_1/document/" . $tem_name);
            }

            if (!empty($_FILES["test_file"]['name'])) {
                $file_names     = $tem_name;
                //print_r($file_names);
                $file_org_names = $_FILES["test_file"]['name'];
            }
        }


        $columns            = [
            "status_option"         => $status_option,
            "remark_type"           => $remark_type,
            "status_description"    => $status_description,
            "date_time"             => $date_time,
            "doc_name"              => $doc_option,
            "file_name"             => $file_names,
            "user_name_select"      => $user_name_select,
            "approve_by"            => $approve_by,
            "stage"                 => "Level 1",
            "screen_unique_id"  => $screen_unique_id,
            "unique_id"         => unique_id($prefix)
        ];


        $table_details = [
            $table,
            [
                "stage_1_status"
            ]
        ];

        $select_where = ' is_delete = 0 AND  screen_unique_id ="' . $screen_unique_id . '" ';

        $action_obj = $pdo->select($table_details, $select_where);

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
        if ($data[0]["stage_1_status"] == 2) {
            $msg        = "already";
        } else if ($data[0]["stage_1_status"] != 2) {

            // Insert Begins            

            $action_obj     = $pdo->insert($table_stage_1, $columns);

            $mainlist_columns  = [
                "stage_1_status"        => $_REQUEST["status_option"],
                "stage_1_description"   => $_REQUEST["status_description"],
                "stage_1_update_date"   => date('Y-m-d H:i:s'),
                "stage_1_approve_by"    => $_REQUEST['user_id'],
                "stage"                 => "Level 1",

            ];

            $where_mainlist = [
                "screen_unique_id"  => $_REQUEST["screen_unique_id"],
                "is_active"         => 1,
                "is_delete"         => 0,

            ];

            $action_obj_main   = $pdo->update($table, $mainlist_columns, $where_mainlist);
            // Insert Ends

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "add";
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
    case 'sub_details':
        $table_date = "";
        $unique_id  = $_POST['unique_id'];
        $screen_unique_id  = $_POST['screen_unique_id'];

        $columns = [
            "@a:=@a+1 s_no",
            "date_format(date_time,'%Y-%m-%d') as date_time",
            "approve_by",
            "status_description",
            "status_option",

            "unique_id",
            "screen_unique_id",
            "file_name",
            "doc_name"
        ];
        $table_details = [
            $table_stage_1 . " , (SELECT @a:= '" . $start . "') AS a ",
            $columns
        ];

        $where = "screen_unique_id = '" . $screen_unique_id . "' and is_delete = 0";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where);
        // print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;
            $s_no = '1';

            foreach ($res_array as $key => $value) {

                switch ($value['status_option']) {
                    case 1:
                        $value['status_option'] = "<span style='color: #FF5733;font-size : 10px ;'><b>In Progress</b></span>";
                        break;
                    case 2:
                        $value['status_option'] = "<span style='color: #228B22;font-size : 10px ;'><b>Completed</b></span>";
                        break;
                    case 3:
                        $value['status_option'] = "Cancel";
                        break;
                    case 4:
                        $value['status_option'] = "<span style='color: blue;font-size : 10px ;'><b>Reopened</b></span>";
                        break;
                }

                $status_description = $value['status_description'];
                //$date = date_create($value['date_time']);
                $value['date_time'] = disdate($value['date_time']);
                // $value['unique_id']     = $btn_delete;

                $approve_details = user_name($value['approve_by']);
                $approve_by = $approve_details[0]['staff_name'];
                $desig_id  = $approve_details[0]['designation_id'];

                $desig_details = designation_name($desig_id);
                $designation_name = $desig_details[0]['designation_name'];
                // / $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = '<a href="#" onclick="status_sub_delete(\'' . $value['unique_id'] . '\')"> <i class="fa fa-trash fa-2x text-danger"></i></a>';
                $value['file_name'] = image_view("stage_1", $value['unique_id'], $value['file_name'], $value['doc_name']);
                //print_r($value['file_name']);
                $table_data .= '<div class="row">
                                <div class="col-2 bor_rt">
                                   <center>' . $value['file_name'] . '</center>
                                   
                                </div>
                                 <div class="col-8 nopadd">
                                    <div class="row">
                                        <div class="col-6 font_smll">
                                             <h6 class="mt-2 mb-1"  style="font-size : 12px">' . $approve_by . ' <small>' . $designation_name . '</small></h6>
                                             <p>' . $status_description . '</p>
                                        </div>
                                        <div class="col-6 font_smll text-right">
                                            <p class="mt-2">' . $value['date_time'] . '</p>
                                            <p class="mt-1">' . $value['status_option'] . '</p>
                                        </div>
                                    </div>
                                   
                                </div>
                                <div class="col-2 bor_lft font_smll text-center">
                                   <p class="mt-4">' . $value['unique_id'] . '</p>
                                   
                                </div>
                            </div>';
            }
        }

        //print_r($table_data);

        $json_array   = [
            "data"      => $table_data,
        ];

        echo json_encode($json_array);
        break;

    // case 'status_sub_datatable':
    //     // Function Name button prefix
    //     $btn_edit_delete = "status_sub";

    //     // Fetch Data
    //     $unique_id = $_POST['unique_id'];
    //     $screen_unique_id = $_POST['screen_unique_id'];

    //     // DataTable 
    //     $search = $_POST['search']['value'];
    //     $length = $_POST['length'];
    //     $start = $_POST['start'];
    //     $draw = $_POST['draw'];
    //     $limit = $length;

    //     $data = [];

    //     if ($length == '-1') {
    //         $limit = "";
    //     }

    //     // Query Variables
    //     $json_array = "";
    //     $columns = [
    //         "@a:=@a+1 s_no",
    //         "date_time",
    //         "approve_by",
    //         "remark_type",
    //         "status_description",
    //         "user_name_select",
    //         "doc_name",
    //         "file_name",
    //         "status_option",
    //         "unique_id",
    //         "screen_unique_id"
    //     ];
    //     $table_details = [
    //         $table_stage_1 . " , (SELECT @a:= '" . $start . "') AS a ",
    //         $columns
    //     ];

    //     $where = [
    //         //"purchase_unique_id"            => $unique_id,
    //         "screen_unique_id" => $screen_unique_id,
    //         "is_active" => 1,
    //         "is_delete" => 0
    //     ];


    //     $order_by = "";


    //     $sql_function = "SQL_CALC_FOUND_ROWS";

    //     $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    //     //  print_r($result);
    //     $total_records = total_records();

    //     if ($result->status) {

    //         $res_array = $result->data;

    //         foreach ($res_array as $key => $value) {
    //             switch ($value['status_option']) {
    //                 case 1:
    //                     $value['status_option'] = "Progressing";
    //                     break;
    //                 case 2:
    //                     $value['status_option'] = "Completed";
    //                     break;
    //                 case 3:
    //                     $value['status_option'] = "Cancel";
    //                     break;
    //                 case 4:
    //                     $value['status_option'] = "Reopened";
    //                     break;
    //             }
    //             $date = date_create($value['date_time']);
    //             $entry_date = date_format($date, 'Y-m-d');
    //             $value['date_time'] = date_format($date, 'd-m-Y H:i:s');
    //             if ($entry_date == date('Y-m-d')) {
    //                 $btn_delete         = btn_delete_stage($btn_edit_delete, $value['unique_id'], $screen_unique_id);
    //             } else {
    //                 $btn_delete = "";
    //             }

    //             // $btn_delete         = btn_delete_stage($btn_edit_delete, $value['unique_id'], $screen_unique_id);
    //             $value['unique_id']     = $btn_delete;
    //             $value['approve_by']     = user_name($value['approve_by'])[0]['staff_name'];
    //             if ($value['remark_type']) {
    //                 $value['remark_type']  = remark_type($value['remark_type'])[0]['remark'];
    //             } else {
    //                 $value['remark_type'] = '';
    //             }
    //             // $value['user_name_select']     = tag_person($value['user_name_select'])[0]['staff_name'];
                
    //             $user_ids = explode(',', $value['user_name_select']);
    //             $user_names = [];

    //             foreach ($user_ids as $user_id) {
    //                 $user_names[] = tag_person($user_id)[0]['staff_name'];
    //             }
    //             $value['user_name_select'] = implode('<br>', $user_names);
                
    //             $value['file_name'] = image_view_1("stage_1", $value['unique_id'], $value['file_name'], $value['doc_name']);
    //             switch ($value['doc_name']) {
    //                 case 1:
    //                     $value['doc_name'] = "Image";
    //                     break;
    //                 case 2:
    //                     $value['doc_name'] = "Document";
    //                     break;
    //                 case 3:
    //                     $value['doc_name'] = "Audio";
    //                     break;
    //             }



    //             $data[] = array_values($value);
    //         }

    //         $json_array = [
    //             "draw" => intval($draw),
    //             "recordsTotal" => intval($total_records),
    //             "recordsFiltered" => intval($total_records),
    //             "data" => $data,
    //             "testing" => $result->sql,

    //         ];
    //     } else {
    //         print_r($result);
    //     }

    //     echo json_encode($json_array);

    //     break;
case 'status_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete = "status_sub";

        // Fetch Data
        $unique_id = $_POST['unique_id'];
        $screen_unique_id = $_POST['screen_unique_id'];

        // DataTable 
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start = $_POST['start'];
        $draw = $_POST['draw'];
        $limit = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // Query Variables
        $json_array = "";
        $columns = [
            "@a:=@a+1 s_no",
            "date_time",
            "approve_by",
            "remark_type",
            "status_description",
            "user_name_select",
            "doc_name",
            "file_name",
            "status_option",
            " '' as call_status",
            "unique_id",
            "screen_unique_id",
            "print_status"
        ];
        $table_details = [
            $table_stage_1 . " , (SELECT @a:= '" . $start . "') AS a ",
            $columns
        ];

        $where = [
            //"purchase_unique_id"            => $unique_id,
            "screen_unique_id" => $screen_unique_id,
            "is_active" => 1,
            "is_delete" => 0
        ];


        $order_by = "";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //   print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                switch ($value['status_option']) {
                    case 1:
                        $value['status_option'] = "Progressing";
                        break;
                    case 2:
                        $value['status_option'] = "Completed";
                        break;
                    case 3:
                        $value['status_option'] = "Cancel";
                        break;
                    case 4:
                        $value['status_option'] = "Reopened";
                        break;
                }
                $date = date_create($value['date_time']);
                $update_date = date_format($date, 'd-m-Y'); 
                $current_date = date('d-m-Y');
                if ($_SESSION['user_id'] == $value['approve_by'] && $current_date == $update_date) {
                    $btn_delete         = btn_delete_stage($btn_edit_delete, $value['unique_id'], $screen_unique_id);
                } else {
                    $btn_delete = "";
                }
        
                // $btn_view           = btn_print1($folder_name ,$value['unique_id'],$screen_unique_id .$action_btn,"popup.php");
                //  $value['call_status'] = $btn_view;
                
                   if (($value['approve_by'] == $_SESSION["user_id"])&&($value['user_name_select'] != '')&& ($value['print_status'] == 0 )) {
                        $btn_view = btn_print1($folder_name, $value['unique_id'],$value['screen_unique_id'] . $action_btn, "popup.php");
                        $value['call_status'] = $btn_view;
                    }elseif(($value['approve_by'] == $_SESSION["user_id"])&&($value['user_name_select'] != '')&& ($value['print_status'] == 1 )){
                       $value['call_status'] = 'Tagged Closed';
                    } 
                    
                    
                    
            
            
                // $value['call_status'] = '<a href="index.php?file=stage_1/popup?unique_id=' . $value['unique_id'] . '&screen_unique_id=' . $value['screen_unique_id'] . '" class="btn btn-primary btn-sm">Edit</a>';

                // $btn_update         = btn_update_remark($folder_name,$value['unique_id'],$value['screen_unique_id']);
                
               
                $value['unique_id']     = $btn_delete;
                $value['approve_by']     = user_name($value['approve_by'])[0]['staff_name'];
                if ($value['remark_type']) {
                    $value['remark_type']  = remark_type($value['remark_type'])[0]['remark'];
                } else {
                    $value['remark_type'] = '';
                }
                // $value['user_name_select']     = tag_person($value['user_name_select'])[0]['staff_name'];
                
                $user_ids = explode(',', $value['user_name_select']);
                $user_names = [];

                foreach ($user_ids as $user_id) {
                    $user_names[] = tag_person($user_id)[0]['staff_name'];
                }
                $value['user_name_select'] = implode('<br>', $user_names);
                // $user_name_select = implode('<br>', $user_names);
                // print_r($user_name_select);
                $value['file_name'] = image_view_1("stage_1", $value['unique_id'], $value['file_name'], $value['doc_name']);
                switch ($value['doc_name']) {
                    case 1:
                        $value['doc_name'] = "Image";
                        break;
                    case 2:
                        $value['doc_name'] = "Document";
                        break;
                    case 3:
                        $value['doc_name'] = "Audio";
                        break;
                }



                $data[] = array_values($value);
            }

            $json_array = [
                "draw" => intval($draw),
                "recordsTotal" => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data" => $data,
                "testing" => $result->sql,

            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);

        break;
        
case 'app_status_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete = "status_sub";

        // Fetch Data
        $unique_id = $_POST['unique_id'];
        $screen_unique_id = $_POST['screen_unique_id'];

        // DataTable 
        $search = $_POST['search']['value'];
        $length = $_POST['length'];
        $start = $_POST['start'];
        $draw = $_POST['draw'];
        $limit = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // Query Variables
        $json_array = "";
        $columns = [
            "@a:=@a+1 s_no",
            "date_time",
            "approve_by",
            "remark_type",
            "status_description",
            "user_name_select",
            "doc_name",
            "file_name",
            "status_option",
            "unique_id",
            "screen_unique_id"
        ];
        $table_details = [
            $table_stage_1 . " , (SELECT @a:= '" . $start . "') AS a ",
            $columns
        ];

        $where = [
            //"purchase_unique_id"            => $unique_id,
            "screen_unique_id" => $screen_unique_id,
            "is_active" => 1,
            "is_delete" => 0
        ];


        $order_by = "";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //  print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                switch ($value['status_option']) {
                    case 1:
                        $value['status_option'] = "Progressing";
                        break;
                    case 2:
                        $value['status_option'] = "Completed";
                        break;
                    case 3:
                        $value['status_option'] = "Cancel";
                        break;
                    case 4:
                        $value['status_option'] = "Reopened";
                        break;
                }
                $date = date_create($value['date_time']);
                $value['date_time'] = date_format($date, 'd-m-Y H:i:s');
                if ($_SESSION['user_id'] == $value['approve_by']) {
                    $btn_delete         = btn_delete_demo_stage($btn_edit_delete, $value['unique_id'], $screen_unique_id);
                } else {
                    $btn_delete = "";
                }

                // $btn_delete         = btn_delete_demo_stage($btn_edit_delete, $value['unique_id'], $screen_unique_id);
                $value['unique_id']     = $btn_delete;
                $value['approve_by']     = user_name($_SESSION['user_id'])[0]['staff_name'];
                if ($value['remark_type']) {
                    $value['remark_type']  = remark_type($value['remark_type'])[0]['remark'];
                } else {
                    $value['remark_type'] = '';
                }
                $value['user_name_select']     = tag_person($value['user_name_select'])[0]['staff_name'];
                $value['file_name'] = image_view_1("stage_1", $value['unique_id'], $value['file_name'], $value['doc_name']);
                switch ($value['doc_name']) {
                    case 1:
                        $value['doc_name'] = "Image";
                        break;
                    case 2:
                        $value['doc_name'] = "Document";
                        break;
                    case 3:
                        $value['doc_name'] = "Audio";
                        break;
                }



                $data[] = array_values($value);
            }

            $json_array = [
                "draw" => intval($draw),
                "recordsTotal" => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data" => $data,
                "testing" => $result->sql,

            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);

        break;

    case 'status_sub_delete':

        $unique_id  = $_POST['unique_id'];
        $screen_unique_id  = $_POST['screen_unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_stage_1, $columns, $update_where);


        $json_array = "";
        $columns = [
            "date_time",
            "approve_by",
            "status_description",
            "status_option",
            "unique_id",
            "screen_unique_id",
            "stage"
        ];
        $table_details = [
            $table_stage_1 . " , (SELECT @a:= '" . $start . "') AS a ",
            $columns
        ];


        $where = "screen_unique_id ='" . $screen_unique_id . "' and is_delete = 0 and is_active = 1 order by id DESC LIMIT 1";


        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);

        //print_r($result);
        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {

                $status_option = $value['status_option'];
                $status_description = $value['status_description'];
                $date_time = $value['date_time'];
                $approve_by = $value['approve_by'];
                $stage = $value['stage'];
            }
        }


        $mainlist_columns  = [
            "stage_1_status"        => $status_option,
            "stage_1_description"   => $status_description,
            "stage_1_update_date"   => $date_time,
            "stage_1_approve_by"    => $approve_by,
            "stage"                 => $stage,

        ];

        $where_mainlist = [
            "screen_unique_id"  => $_POST["screen_unique_id"],
            "is_active"         => 1,
            "is_delete"         => 0
        ];

        $action_obj_main   = $pdo->update($table, $mainlist_columns, $where_mainlist);


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


    case 'site_name':

        $state_id      = $_POST['state_id'];

        $site_name_options         = site_name('', $state_id);
        $site_name_options         = select_option($site_name_options, "Select ");

        $data   =  $site_name_options;

        $json_array   = [
            "data"      => $data,
        ];

        echo json_encode($json_array);
        break;

        // =================================================================================================
        case 'category_name_option':

        $department_type = $_POST['department_type'];
        $main_category   = $_POST['main_category'];
        $sub_category_name_type_options = category_creation("", $department_type,$main_category);
        
        $sub_name_option = select_option_create($sub_category_name_type_options,"Select the Category");

        echo $sub_name_option;

        break;
        
        
        // To filter ategory name by department
    // case 'category_name_option_filter':
        
    //     $department_type = $_POST['department_type'];
    //     // print_r($department_type);
    //     $main_category = $_POST['main_category'];
    //     // print_r($main_category);
    //     $sub_category_name_type_options = category_creation("", $department_type = "",$main_category = "");

    //     $sub_name_option = select_option($sub_category_name_type_options,"Select the Category");
        
    //     echo $sub_name_option;
        
    // break;    
    
     case 'category_name_option_filter':
        
        $department_type = $_POST['department_type'];
        $sub_category_name_type_options = category_creations("", $department_type, "");

        $sub_name_option = select_option($sub_category_name_type_options, "Select the Category");
        
        echo $sub_name_option;
        
        break;

    case 'reopen_status_update':

        $complaint_no               = $_POST["complaint_no"];
        $reopen_by                  = $_SESSION['user_id'];
        $reopen_date                = date('Y-m-d');

        $screen_unique_id           = $_POST["screen_unique_id"];
        $unique_id                  = $_POST["unique_id"];


        $update_where               = "";





        $columns            = [
            "reopen_by"             => $reopen_by,
            "reopen_date"           => $reopen_date,
            "reopen_status"         => "0"


            // "screen_unique_id"  => $screen_unique_id,
            // "unique_id"         => unique_id($prefix)
        ];


        $table_details = [
            $table,
            $columns,
        ];

        $select_where = ' is_delete = 0 AND  complaint_no ="' . $complaint_no . '" ';

        $action_obj = $pdo->select($table_details, $select_where);

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
        if ($data[0]["reopen_status"] == 2) {
            $msg        = "already";
        } else if ($data[0]["reopen_status"] != 2) {

            // Insert Begins            

            $action_obj     = $pdo->update($table, $columns, $select_where);
            //print_r($action_obj);

            // $mainlist_columns  = [
            //     "stage_1_status"        => $_POST["status_option"],
            //     "stage_1_description"   => $_POST["status_description"],
            //     "stage_1_update_date"   => date('Y-m-d H:i:s'),
            //     "stage_1_approve_by"    => $_SESSION['user_id'],
            //     "stage"                 => "Stage 1",

            // ];

            // $where_mainlist = [
            //     "screen_unique_id"  => $_POST["screen_unique_id"],
            //     "is_active"         => 1,
            //     "is_delete"         => 0,

            // ];

            // $action_obj_main   = $pdo->update($table, $mainlist_columns, $where_mainlist);
            // Insert Ends





            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                // if ($unique_id) {
                //     $msg        = "update";
                // } else {
                //     $msg        = "add";
                // }
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

        // =======================================================================================================

   

case 'tag_wise_counts':

       
        $date = date('Y-m-d');
        $json_array = [];
        $columns = [           
       
            "COUNT(DISTINCT v.screen_unique_id) as tag_person_cnt"
            
        ];
         $table_details  = [
                "view_pending_days_cnt as v JOIN stage_1 as s on v.screen_unique_id = s.screen_unique_id",
                $columns
            ];
    
    
            $where     .="v.complaint_no != '' and v.stage_1_status != 2 ";
    
            //  $get_dept_name = get_dept_priodic($sess_user_id);
  
            // $get_site_name = get_site_priodic($sess_user_id);

        if ($session_user == 1) {
            $where .= " and v.assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND v.entry_date >= '".$_POST['from_date']."' and v.entry_date <= '".$_POST['to_date']."'";
            }
        
    //     if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND v.department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (v.department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
    //     if ($_POST['department_name']) {
    //         $where .= " AND v.department_name = '".$_POST['department_name']."' ";
    //     }
    // }

    //     if ($_POST['complaint_name']) {
    //         $where .= " AND v.complaint_category = '".$_POST['complaint_name']."' ";
    //     }
        
    // if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_site_name'] == "All"){
    
    //         if ($_POST['site_name']) {
    //             $where .= " AND v.site_name = '".$_POST['site_name']."' ";
    //         }
    //     }else{
    //         $where .= " AND  FIND_IN_SET (v.site_name,'".$_SESSION['sess_site_name']."')";
    //     }
    // }else{
        
    //         if ($_POST['site_name']) {
    //             $where .= " AND v.site_name = '".$_POST['site_name']."' ";
    //         }
        
    // }

    //     if ($_POST['status_name'] != '') {
    //         $where .= " AND v.stage_1_status = '".$_POST['status_name']."' ";
    //     }
            
            
        
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_dept_name != 'All'){
    //             $where .= " and FIND_IN_SET(v.department_name,'" . $get_dept_name . "')";
    //         }
    //     }
    //     if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
    //         if($get_site_name != 'All'){
    //             $where .= " and FIND_IN_SET(v.site_name,'" . $get_site_name . "')";
    //         }
    //     }
    if($_SESSION['user_id'] !='5ff562ed542d625323'){
            $where .= "and FIND_IN_SET('".$sess_user_id."',s.user_name_select)";
        }else{
            $where .= " and s.user_name_select != ''";
        }
        $where .= " and s.is_delete = '0' group by s.screen_unique_id";
        $result = $pdo->select($table_details,$where);
      //print_r($result);
        if ($result->status) {
            $res_array = $result->data;
    
            // Initialize total count
            $total_count = 0;
    
            // Loop through the results to calculate individual category counts and total count
            foreach ($res_array as $value) {
                
              
                
                 
                $tag_person_cnt += $value['tag_person_cnt'];
                
               
            }
    $total_count = $tag_person_cnt;
            // Construct JSON array
            $json_array = [
                
                "tag_person_cnt" => $tag_person_cnt
            ];
    
            // Encode JSON array
            echo json_encode($json_array);
        } 
       
    break;


case 'level_wise_counts':
    

        
        
            $get_dept_name = get_dept_priodic($sess_user_id);
  
            $get_site_name = get_site_priodic($sess_user_id);
            
            $get_level     = periodic_username_like($sess_user_id);

        if ($session_user == 1) {
            $where .= " and assign_by = '$sess_user_id'";
            $where_tag = " and a.assign_by = '$sess_user_id'";
        }
        
        if (($_POST['from_date'])&&($_POST['to_date'])) {
                $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
                $where_tag .= " AND a.entry_date >= '".$_POST['from_date']."' and a.entry_date <= '".$_POST['to_date']."'";
            }
        
        if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_department_name'] == "All"){
            if ($_POST['department_type']) {
                $where .= " AND department_name = '".$_POST['department_type']."' ";
                $where_tag .=  " AND a.department_name = '".$_POST['department_type']."' ";
                
            }
        }else{
            $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
            $where_tag .= " AND FIND_IN_SET (a.department_name,'".$_SESSION['sess_department_name']."')";
        }
        }else{
        
        if ($_POST['department_name']) {
            $where .= " AND department_name = '".$_POST['department_name']."' ";
            $where_tag .= " AND a.department_name = '".$_POST['department_name']."' ";
        }
    }

        if ($_POST['complaint_name']) {
            $where .= " AND complaint_category = '".$_POST['complaint_name']."' ";
            $where_tag .= " AND a.complaint_category = '".$_POST['complaint_name']."' ";
        }
        
    if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
        if($_SESSION['sess_site_name'] == "All"){
    
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
                $where_tag .= " AND a.site_name = '".$_POST['site_name']."' ";
            }
        }else{
            $where .= " AND  FIND_IN_SET (site_name,'".$_SESSION['sess_site_name']."')";
            $where_tag .= " AND  FIND_IN_SET (a.site_name,'".$_SESSION['sess_site_name']."')";
        }
    }else{
        
            if ($_POST['site_name']) {
                $where .= " AND site_name = '".$_POST['site_name']."' ";
                $where_tag .= " AND a.site_name = '".$_POST['site_name']."' ";
            }
        
    }

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
            $where_tag .= " AND a.stage_1_status = '".$_POST['status_name']."' ";
        }
            
            
        
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_dept_name != 'All'){
                $where .= " and FIND_IN_SET(department_name,'" . $get_dept_name . "')";
                $where_tag .= " and FIND_IN_SET(a.department_name,'" . $get_dept_name . "')";
            }
        }
        if($_SESSION['user_type_unique_id'] !='5f97fc3257f2525529'){
            if($get_site_name != 'All'){
                $where .= " and FIND_IN_SET(site_name,'" . $get_site_name . "')";
                $where_tag .= " and FIND_IN_SET(a.site_name,'" . $get_site_name . "')";
            }
        }

        

        $date = date('Y-m-d');
        $json_array = [];
        $columns = [           
            "(select count(*) from view_level_1 where stage_1_status != 2 and level = 1 $where) as level_1_cnt",
            "(select count(*) from view_level_2 where stage_1_status != 2 and level = 2 $where) as level_2_cnt",
            "(select count(*) from view_level_3 where stage_1_status != 2 and level = 3 $where) as level_3_cnt",
            "(select count(*) from view_level_4 where stage_1_status != 2 and level = 4 $where) as level_4_cnt",
            "(select count(*) from view_level_5 where stage_1_status != 2 and level = 5 $where) as level_5_cnt",
            "(select count(*) from view_level_6 where stage_1_status != 2 and level = 6 $where) as level_6_cnt",
            "(select count(*) from view_level_7 where stage_1_status != 2 and level = 7 $where) as level_7_cnt",
            
            
        ];
        $table_details = [
            "view_pending_days_cnt",
            $columns
        ];
        $result = $pdo->select($table_details);
       // print_r($result);
        if ($result->status) {
            $res_array = $result->data;
    
            // Initialize total count
            $total_count = 0;
    
            // Loop through the results to calculate individual category counts and total count
            foreach ($res_array as $value) {
                $level_test = explode(",",$get_level);
         //print_r($level_test);
        foreach($level_test as $level){
                 if($level == 1){
                $level_1_cnt = $value['level_1_cnt'];
                 }else if($level == 2){
                $level_2_cnt = $value['level_2_cnt'];
                 }else if($level == 3){
                $level_3_cnt = $value['level_3_cnt'];
                 }else if($level == 4){
                $level_4_cnt = $value['level_4_cnt'];
                 }else if($level == 5){
                $level_5_cnt = $value['level_5_cnt'];
                 }else if($level == 6){
                $level_6_cnt = $value['level_6_cnt'];
                 }else if($level == 7){
                $level_7_cnt = $value['level_7_cnt'];
                 }
                 
               
                
        }
               
            }
    $total_count = $all_cnt + $level_1_cnt + $level_2_cnt + $level_3_cnt + $level_4_cnt + $level_5_cnt + $level_6_cnt + $level_7_cnt;
            // Construct JSON array
            $json_array = [
                "level_1_cnt" => $level_1_cnt,
                "level_2_cnt" => $level_2_cnt,
                "level_3_cnt" => $level_3_cnt,
                "level_4_cnt" => $level_4_cnt,
                "level_5_cnt" => $level_5_cnt,
                "level_6_cnt" => $level_6_cnt,
                "level_7_cnt" => $level_7_cnt,
                
            ];
        
            // Encode JSON array
            echo json_encode($json_array);
        } 
       
    break;

 default:

        break;
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


    $where  = "department_name = '" . $department . "' and level = " . $stage . "  and is_delete = 0 and form_unique_id != ''";



    $cnt_status = $pdo->select($table_details, $where);
    //print_r($cnt_status);
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


function random_strings($length_of_string)
{

    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';

    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(
        str_shuffle($str_result),
        0,
        $length_of_string
    );
}

function get_all_cnt(){
    global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as all_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['all_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_level_1_cnt(){
    global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_1_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 1 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_1_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_level_2_cnt(){
      global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_2_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 2 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_2_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_level_3_cnt(){
      global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_3_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 3 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_3_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}


function get_level_4_cnt(){
      global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_4_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 4 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_4_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_level_5_cnt(){
      global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_5_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 5 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_5_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_level_6_cnt(){
      global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_6_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 6 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_6_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function get_level_7_cnt(){
      global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
       
        "count(entry_date) as level_7_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   $where  = "level = 7 and stage_1_status !=2";
    

    $cnt_status = $pdo->select($table_details, $where);
   
    if (!($cnt_status->status)) {

        print_r($cnt_status);

    } else {
        
        if(!empty($cnt_status->data[0])) {
            $cnt_sts    = $cnt_status->data[0]['level_7_cnt'];

        }else{
            $cnt_sts    = "";
        }
        
    }
        return $cnt_sts;
}

function periodic_username_like($user_id = "") {

    $result     = "''";

    if ($user_id) {
        global $pdo;

        $table_name = "periodic_creation_sub";

        $columns        = [
           "CONCAT(GROUP_CONCAT(DISTINCT level)) as level"
           //"level",
        ];

        $where          = " user_id ='".$user_id."' and is_delete = 0";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
       //print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['level'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}
