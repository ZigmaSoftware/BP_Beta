<?php

// Get folder Name From Currnent Url 
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];
 
// // Database Country Table Name
$table = "complaint_creation";
$table_sub = "complaint_creation_doc_upload";


// // Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';


// // Variables Declaration
$action = $_POST['action'];

//$user_type          = "";
$is_active = "";
$unique_id = "";
$prefix = "CMP-";

$data = "";
$msg = "";
$error = "";
$status = "";
$test = ""; // For Developer Testing Purpose
$session_user_id = $_SESSION['id'];
$session_user = $_SESSION['sess_type_user']; 
$sess_user_id = $_SESSION['user_id']; 

$fileUploadPath = $fileUploadConfig->get("upload_folder");
// Create Folder in root->uploads->(this_folder_name) Before using this file upload
$fileUploadConfig->set("upload_folder", $fileUploadPath . $folder_name . DIRECTORY_SEPARATOR);
// File Upload Library Call
$fileUpload = new Alirdn\SecureUPload\SecureUPload($fileUploadConfig);
// // print_r($action);
switch ($action) {


    case 'createupdate':

    
        //$state_name             = $_POST["state_name"];
        $site_name              = $_POST["site_name"];
        $plant_name             = $_POST["plant_name"];
        $shift_name             = "65d459102ca9681263";
        $problem_type           = $_POST["problem_type"];
        $priority_type          = $_POST["priority"];
        $location_address       = $_POST["location_address"];
        $landmark               = $_POST["landmark"];
        $department_name        = $_POST["department_name"];
        $main_category          = $_POST["main_category"];
        $complaint_category     = $_POST["complaint_category"];
        $source_name            = $_POST["source_name"];
        $complaint_description  = $_POST["complaint_description"];
        $complaint_no           = $_POST["complaint_no"];
        $screen_unique_id       = $_POST["screen_unique_id"];
        $entry_form             = $_POST["entry_form"];
        $assign_by              = $_POST["assign_by"];
        $unique_id              = $_POST["unique_id"];
        $user_id                = $_POST["user_id"];
        $insert_data = $_POST['insert_data'];
        $time =  time('H:i:s');
    //    print_r($user_id);die();
        if($entry_form == ''){
            $entry_froms = "Admin Portal
            ";
        }else{ 
            $entry_froms = $entry_form;
        }

        if($source_name == ''){
            $source_name = '6433b4f88f4ba22636';
        }

        // if($entry_froms == 'Chat Bot'){
        //     $mobile_no = substr($mobile_no, 2);
        // }
if($insert_data == ''){
    $insert_type = 'web';
}else{
    $insert_type = $insert_data;
}
        
            $acc_year = acc_year();
        

        $update_where = "";
       
        $columns = [
            
           // "state_name"                => $state_name,
            "site_name"                 => $site_name,
            "plant_name"                => $plant_name,
            "shift_name"                => $shift_name,
            "problem_type"              => $problem_type,
            "priority_type"              => $priority_type,
            "address"                   => $location_address,
            "landmark"                  => $landmark,
            "department_name"           => $department_name,
            "main_category"             => $main_category,
            "complaint_category"        => $complaint_category,
            "source_name"               => $source_name,
            "complaint_description"     => $complaint_description,
            "screen_unique_id"          => $screen_unique_id,
            "complaint_no"              => $complaint_no,
            "entry_from"                => $entry_froms,
            "assign_by"                => $assign_by,
            "insert_type"                 =>$insert_type,
            "user_id"                   =>$user_id,
            
            "unique_id"                 => unique_id($prefix)
            
        ];
       
        $date = date('Y-m-d H:i:s');

        // check already Exist Or not
        $table_details = [
            $table, 
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        
        $select_where = ' is_delete = 0  AND  site_name ="'. $site_name. '" AND  plant_name ="'. $plant_name. '" AND  shift_name ="'. $shift_name. '" AND  problem_type ="'. $problem_type. '"AND  priority_type ="'. $priority_type. '" AND  department_name ="'. $department_name. '" AND  main_category = "'.$main_category.'" AND complaint_category ="'. $complaint_category. '" and created = "'.$date.'"';
       
        // When Update Check without current id
        if ($unique_id) {
            $select_where .= ' AND unique_id !="' . $unique_id . '" ';
        }
       
        $action_obj = $pdo->select($table_details, $select_where);
        //print_r($_SESSION);
        if ($action_obj->status) {
            $status = $action_obj->status;
            $data = $action_obj->data;
            $error = "";
            $sql = $action_obj->sql;

        } else {
            $status = $action_obj->status;
            $data = $action_obj->data;
            $error = $action_obj->error;
            $sql = $action_obj->sql;
            $msg = "error";
        }
        // print_r($action_obj);
        if ($data[0]["count"]) {
            $msg = "already";
        }else if ($data[0]["count"] == 0) {
            // Update Begins
            if ($unique_id) {
                $columns['complaint_no']                     = $complaint_no;
                unset($columns['unique_id']);

                $update_where = [
                    "unique_id" => $unique_id
                ];

                $action_obj = $pdo->update($table, $columns, $update_where);

                // Update Ends
            } else {

                 $bill_no                 = bill_no ($table,$update_where,$prefix);
                 $columns['complaint_no']   = $bill_no;
                 $columns['entry_date']   = date('Y-m-d');
                 $columns['entry_time']   = date('H:i:s');
                // Insert Begins            
                $action_obj = $pdo->insert($table, $columns);
                // Insert Ends

            }
            // print_r($action_obj);die();
            if ($action_obj->status) {
                $status = $action_obj->status;
                $data = $action_obj->data; 
                $error = "";
                $sql = $action_obj->sql;

                if ($unique_id) {
                    $msg = "update";
                } else {
                    $msg = "create";
                }
            } else {
                $status = $action_obj->status;
                $data = $action_obj->data;
                $error = $action_obj->error;
                $sql = $action_obj->sql;
                $msg = "error";
            }
        }

        $json_array = [
            "status" => $status,
            "data" => $data,
            "error" => $error,
            "msg" => $msg,
            "sql" => $sql,
            "complaint_no" => $columns['complaint_no']
        ];

        echo json_encode($json_array);
        break;
        
        case 'sub_datatable':
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
            "complaint_no",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            "unique_id",
            "plant_name",
            "complaint_category",
            "entry_date",
            "stage"
        ];
        $table_details  = [
            $table,
            $columns
        ];
    // print_r($table_details);
       $where     = "is_delete = 0 ";
       
        if($session_user == 1){
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
            $where .= " AND stage_1_status = ".$_POST['status_name']." ";
        }else{
            $where .= " AND stage_1_status != '2' ";
 
        }

       
        $result         = $pdo->select($table_details, $where);
        
        //  print_r($result);
        $total_records  = total_records();
        if ($result->status) {

            $res_array      = $result->data;
$i=1;
            foreach ($res_array as $key => $value) {
                    $value['s_no'] = $i++;               
                    
                $ent_date = date('Y-m-d');
                $date1=date_create($value['entry_date']);
                $date2=date_create($ent_date);
                $diff=date_diff($date1,$date2);
                $current_date =  $diff->format("%a");

                 switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>In Progress</span>";
                        break;
                    case 2:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Select</span>";
                        break;
                    default:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Pending</span>";
                        break;
                }

                $value['entry_date']            = disdate($value['entry_date']);
                // $btn_update                     = btn_update($folder_name, $value['unique_id']);
                
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                // $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['assign_by'] = disname(user_name($value['assign_by'])[0]['staff_name']);
                
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_name($value['complaint_category'])[0]['category_name'];
                $value['stage']                 ="<b>".$value['stage']."<b>";
                $value['site_name']             =   "<b>".$value['site_name']." </b><br><span style=font-size:12px;>".$value['plant_name'].'</span>';
                $value['department_name']       =   "<b>".$value['department_name']." </b><br><span style=font-size:12px;>".wordwrap($value['complaint_category'],25,"<br>").'</span>';
                $value['complaint_no']            =   "<b>".$value['complaint_no']." </b><br><span style=font-size:12px;>".$value['entry_date'].'</span>';
                $value['assign_by']             = "<b>".$current_date." Days</b><br><span style='font-size:12px;'>".$value['assign_by']." </span>";
                $value['complaint_description'] = wordwrap($value['complaint_description'],50,"<br>");

                $value['stage_1_status']             = "<b>".$stage_status."</b><br><span style='font-size:12px;'>".$value['stage']." </span>"; 
                
                $btn_view = '<a href="../../../g_app/view.php?unique_id='.$value['unique_id'].'"<i class="mdi mdi-printer"></i></button></a>';
                
                $btn_update                    = btn_update_freeze($folder_name, $value['unique_id'],"","",$_POST['from_date'],$_POST['to_date']);
                
                // $btn_delete = btn_delete_app($folder_name, $value['unique_id']);
               $btn_delete = '<a href="#" onclick="complaint_category_delete(\''.$value['unique_id'].'\')">
    <i class="fa fa-trash text-danger"></i></a>';

                
                // $btn_delete                     = btn_delete_app($folder_name, $value['unique_id']);
                
                $value['unique_id']             = $btn_view . $btn_update . $btn_delete;
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
            "complaint_no",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            "unique_id",
            "plant_name",
            "complaint_category",
            "entry_date",
            "stage",
            "stage_1_status as status"
        ];
        $table_details  = [
            $table . " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
            //  print_r($_SESSION);

       $where     .= " is_delete = 0 ";
       
    //     if($session_user == 1){
    //         $where .= " and assign_by = '$sess_user_id'";
    //     }
       
       
       
    //         if (($_POST['from_date'])&&($_POST['to_date'])) {
    //             $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
    //         }
       
        
    //     if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
    //         if ($_POST['department_name']) {
    //             $where .= " AND department_name = '".$_POST['department_name']."' ";
    //         }
        
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
    
    // if($_POST['priority'] != '') {
    //         $where .= " AND priority_type = '".$_POST['priority']."' ";
    //     }

    //     if ($_POST['status_name'] != '') {
    //         $where .= " AND stage_1_status = ".$_POST['status_name']." ";
    //     }

    //     //$order_by       = "entry_date DESC";
    //     $order_column   = $_POST["order"][0]["column"];
    //     $order_dir      = $_POST["order"][0]["dir"];

    //     // Datatable Ordering 
    //     $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        
    //     if (!empty($_POST['search']['value'])) {
    //         $searchValue = mysql_like($_POST['search']['value']);
        
    //         $where .= " AND ((complaint_no LIKE '$searchValue') ";
    //         $where .= " OR (department_name IN (" . department_name_like($searchValue) . ")) ";
    //         $where .= " OR (site_name IN (" . site_name_like($searchValue) . ")) ";
    //         $where .= " OR (plant_name IN (" . plant_name_like($searchValue) . ")) ";
    //         $where .= " OR (complaint_category IN (" . complaint_category_name_like($searchValue) . ")) "; 
    //         $where .= " OR (complaint_description LIKE '$searchValue') ";
    //         $where .= " OR (assign_by IN (" . user_name_like($searchValue) . ")) "; 
    //         $where .= " )"; 
    //     }





        // if ($_POST['search']['value']) {
        //     $where .= " AND ((  complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";

        //     $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
            
        //     $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
            
        //     $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value'])."))) ";
        // }
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        
        // print_r($result);
        
        $total_records  = total_records();
        //print_r($_SESSION);
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $ent_date = date('Y-m-d');
                $date1=date_create($value['entry_date']);
                $date2=date_create($ent_date);
                $diff=date_diff($date1,$date2);
                $current_date =  $diff->format("%a");

                 switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>In Progress</span>";
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

                $value['entry_date']            = disdate($value['entry_date']);
                $btn_update                     = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = get_project_name($value['plant_name'])[0]['project_name'];
                $value['site_name']             = company_name($value['site_name'])[0]['company_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department($value['department_name'])[0]['department'];
                $value['complaint_category']    = category_creations($value['complaint_category'])[0]['category_name'];
                $value['stage']                 ="<b>".$value['stage']."<b>";
                $value['site_name']             =   "<b>".$value['site_name']." </b><br><span style=font-size:12px;>".$value['plant_name'].'</span>';
                $value['department_name']       =   "<b>".$value['department_name']." </b><br><span style=font-size:12px;>".wordwrap($value['complaint_category'],25,"<br>").'</span>';
                $value['complaint_no']            =   "<b>".$value['complaint_no']." </b><br><span style=font-size:12px;>".$value['entry_date'].'</span>';
                $value['assign_by']             = "<b>".$current_date." Days</b><br><span style='font-size:12px;'>".$value['assign_by']." </span>";
                $value['complaint_description'] = wordwrap($value['complaint_description'],50,"<br>");

                $value['stage_1_status']             = "<b>".$stage_status."</b><br><span style='font-size:12px;'>".$value['stage']." </span>";

                $btn_view = btn_print_task($folder_name, $value['unique_id'] . $action_btn, "view.php");
               // print_r($_SESSION);
                if($_SESSION['user_type_unique_id'] != '6607e79d0c9c927739'){
                if ($value['status'] == 0) {
                    $value['unique_id'] =$btn_view.$btn_update.$btn_delete;
                } 
                else {
                    $value['unique_id'] = $btn_view;
                }
            }else{
                $value['unique_id'] =$btn_view.$btn_update.$btn_delete;
            }


                // $value['unique_id']             = $btn_view . $btn_update . $btn_delete;
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
        
        case 'tracker_datatable':
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
            "complaint_no",
            "site_name",
            "department_name",
            "complaint_description",
            "priority_type",
            "assign_by",
            "stage_1_status",
            "unique_id",
            "plant_name",
            "complaint_category",
            "entry_date",
            "stage"
        ];
        $table_details  = [
            $table,
            $columns
        ];
    
       $where     .= " is_delete = 0 and stage_1_status = '2'";
       
        if($session_user == 1){
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

       
        
    //     if($_SESSION['sess_user_type'] != '5f97fc3257f2525529'){
    //     if($_SESSION['sess_department_name'] == "All"){
    //         if ($_POST['department_type']) {
    //             $where .= " AND department_name = '".$_POST['department_type']."' ";
    //         }
    //     }else{
    //         $where .= " AND FIND_IN_SET (department_name,'".$_SESSION['sess_department_name']."')";
    //     }
    //     }else{
        
    //         if ($_POST['department_name']) {
    //             $where .= " AND department_name = '".$_POST['department_name']."' ";
    //         }
        
    // }
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

        if ($_POST['status_name'] != '') {
            $where .= " AND stage_1_status = '".$_POST['status_name']."' ";
        }

        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        if ($_POST['search']['value']) {
            $where .= " AND ((  complaint_no LIKE '".mysql_like($_POST['search']['value'])."') ";

            $where .= " OR (department_name in (".department_name_like($_POST['search']['value']).")) ";
            
            $where .= " OR (site_name in (".site_name_like($_POST['search']['value']).")) ";
            
            $where .= " OR (plant_name in (".plant_name_like($_POST['search']['value'])."))) ";
        }
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();
        // print_r($result);
        if ($result->status) {

            $res_array      = $result->data;
$i=1;
            foreach ($res_array as $key => $value) {
                    $value['s_no'] = $i++;
                $ent_date = date('Y-m-d');
                $date1=date_create($value['entry_date']);
                $date2=date_create($ent_date);
                $diff=date_diff($date1,$date2);
                $current_date =  $diff->format("%a");

                 switch ($value['stage_1_status']) {
                    case 1:
                        $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>In Progress</span>";
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

                $value['entry_date']            = disdate($value['entry_date']);
                $btn_update                     = btn_update($folder_name, $value['unique_id']);
                $btn_delete                     = btn_delete($folder_name, $value['unique_id']);
                $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
                $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
                $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
                $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
                $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
                $value['complaint_category']    = category_name($value['complaint_category'])[0]['category_name'];
                $value['stage']                 ="<b>".$value['stage']."<b>";
                $value['site_name']             =   "<b>".$value['site_name']." </b><br><span style=font-size:12px;>".$value['plant_name'].'</span>';
                $value['department_name']       =   "<b>".$value['department_name']." </b><br><span style=font-size:12px;>".wordwrap($value['complaint_category'],25,"<br>").'</span>';
                $value['complaint_no']            =   "<b>".$value['complaint_no']." </b><br><span style=font-size:12px;>".$value['entry_date'].'</span>';
                $value['assign_by']             = "<b>".$current_date." Days</b><br><span style='font-size:12px;'>".$value['assign_by']." </span>";
                $value['complaint_description'] = wordwrap($value['complaint_description'],50,"<br>");

                $value['stage_1_status']             = "<b>".$stage_status."</b><br><span style='font-size:12px;'>".$value['stage']." </span>";

                $btn_view                       = '<a href="../../../g_app/completed_view.php?unique_id='.$value['unique_id'].'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';
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
        
        case 'level_wise_counts':
            
    // if (($_POST['from_date'])&&($_POST['to_date'])) {
    //             $where .= " AND entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."'";
    //         }
            
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


if($_SESSION['sess_type_user'] == 1){
            $where .= " and assign_by = '".$_SESSION['user_id']."'";
        }
        

        $date = date('Y-m-d');
        $json_array = [];
        $columns = [           
            "(select count(*) from complaint_creation where stage_1_status = 2 and is_delete = 0 and entry_date >= '".$_POST['from_date']."' and entry_date <= '".$_POST['to_date']."' $where) as tracker_all_cnt",
            
            
        ];
        $table_details = [
            "complaint_creation",
            $columns
        ];
        $result = $pdo->select($table_details);
        //print_r($result);
        if ($result->status) {
            $res_array = $result->data;
    
            // Initialize total count
            $total_count = 0;
    
            // Loop through the results to calculate individual category counts and total count
            foreach ($res_array as $value) {
                
                // $all_cnt    = get_all_cnt();
                // $level_1_cnt = get_level_1_cnt();
                // $level_2_cnt = get_level_2_cnt();
                // $level_3_cnt = get_level_3_cnt();
                // $level_4_cnt = get_level_4_cnt();
                // $level_5_cnt = get_level_5_cnt();
                // $level_6_cnt = get_level_6_cnt();
                // $level_7_cnt = get_level_7_cnt();
                
                 $tracker_all_cnt    = $value['tracker_all_cnt'];
               
               
            }
    // $total_count = $all_cnt + $level_1_cnt + $level_2_cnt + $level_3_cnt + $level_4_cnt + $level_5_cnt + $level_6_cnt + $level_7_cnt;
            // Construct JSON array  
            $json_array = [
                "tracker_all_cnt" => $tracker_all_cnt,
                // "level_1_cnt" => $level_1_cnt,
                // "level_2_cnt" => $level_2_cnt,
                // "level_3_cnt" => $level_3_cnt,
                // "level_4_cnt" => $level_4_cnt,
                // "level_5_cnt" => $level_5_cnt,
                // "level_6_cnt" => $level_6_cnt,
                // "level_7_cnt" => $level_7_cnt,

            ];
    
            // Encode JSON array
            echo json_encode($json_array);
        } 
       
    break;


        
        case 'example_datatable':
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
$from_date = $_POST['from_date'];
       $to_date = $_POST['to_date'];
            $status_name = $_POST['status_name'];
            // print_r($from_date);
            // print_r($to_date);
            // print_r($status_name);die();
        // Query Variables
        $json_array     = "";
        $columns        = [
            '"" as s_no',
            'complaint_no',
            'site_name',
            'department_name',
            'complaint_description',
            'priority_type',
            'assign_by',
            'stage_1_status',
            'unique_id',
            'plant_name',
            'complaint_category',
            'entry_date',
            'stage',
            'insert_type'
        ];
        $table_details  = [
            $table. " , (SELECT @a:= " . $start . ") AS a ",
            $columns
        ];
    
       $where     = "is_delete = 0";
       
       if ($_POST['from_date'] && $_POST['to_date']) {
                $where .= " AND entry_date >= '$from_date' and entry_date <= '$to_date'";
            }
            
            if($_POST['status_name'] != ""){
            $where .= " and stage_1_status = '$status_name'";
        }else{
            $where .= " and stage_1_status = 0";
        }
        if($session_user == 1){
            $where .= "and assign_by = '$sess_user_id'";
        }
       
       
       
       
            
       
        
        


        //$order_by       = "entry_date DESC";
        // $order_column   = $_POST["order"][0]["column"];
        // $order_dir      = $_POST["order"][0]["dir"];

        // // Datatable Ordering 
        // $order_by       = datatable_sorting($order_column,$order_dir,$columns);

    
        // $sql_function   = "SQL_CALC_FOUND_ROWS";
// , $limit, $start, $order_by, $sql_function
        $result         = $pdo->select($table_details,$where);
        $total_records  = total_records();
        // print_r($result);
        if ($result->status) {

            $res_array      = $result->data;
          $i = 1;
            foreach ($res_array as $key => $value) {
                    $value['s_no'] = $i++;
        //         $ent_date = date('Y-m-d');
        //         $date1=date_create($value['entry_date']);
        //         $date2=date_create($ent_date);
        //         $diff=date_diff($date1,$date2);
        //         $current_date =  $diff->format("%a");

        //          switch ($value['stage_1_status']) {
        //             case 1:
        //                 $stage_status = "<span style='font-size:12px;font-weight : bold;color:Orange'>In Progress</span>";
        //                 break;
        //             case 2:
        //                 $stage_status = "<span style='font-size:12px;font-weight : bold;color:#1fcb6b'>Pending</span>";
        //                 break;
        //             default:
        //                 $stage_status = "<span style='font-size:12px;font-weight : bold;color:blue'>Select</span>";
        //                 break;
        //         }

        //         $value['entry_date']            = disdate($value['entry_date']);
                
        //         $value['plant_name']             = plant_name($value['plant_name'])[0]['plant_name'];
        //         $value['site_name']             = site_name($value['site_name'])[0]['site_name'];
        //         $value['priority_type']           = priority_type($value['priority_type'])[0]['priority_name'];
        //         $value['assign_by']             = disname(user_name($value['assign_by'])[0]['staff_name']);
        //         $value['department_name']       = department_type($value['department_name'])[0]['department_type'];
        //         $value['complaint_category']    = category_name($value['complaint_category'])[0]['category_name'];
        //         $value['stage']     ="<b>Level&nbsp".$value['stage']."<b>";
        //         $value['site_name']             =   "<b>".$value['site_name']." </b><br><span style=font-size:12px;>".$value['plant_name'].'</span>';
        //         $value['department_name']       =   "<b>".$value['department_name']." </b><br><span style=font-size:12px;>".wordwrap($value['complaint_category'],25,"<br>").'</span>';
        //         $value['complaint_no']            =   "<b>".$value['complaint_no']." </b><br><span style=font-size:12px;>".$value['entry_date'].'</span>';
        //         $value['assign_by']             = "<b>".$current_date." Days</b><br><span style='font-size:12px;'>".$value['assign_by']." </span>";
        //         $value['complaint_description'] = wordwrap($value['complaint_description'],50,"<br>");

        //         $value['stage_1_status']             = "<b>".$stage_status."</b><br><span style='font-size:12px;'>".$value['stage']." </span>";
        //       // if($value['insert_type'] == 'app'){
               
        //     //   $is_frozen = isset($_GET['frozen']) && $_GET['frozen'] === 'true';
        //         $btn_view                       = app_view($folder_name, $value['unique_id'] . $action_btn,"view.php");
        // // $btn_update                    = btn_update_freeze($folder_name, $value['unique_id'],"","",$_POST['from_date'],$_POST['to_date']);
        
        //           $btn_delete                     = btn_delete_app($folder_name, $value['unique_id']);
                   
        //         $value['unique_id']             = $btn_view . $btn_update . $btn_delete;
               
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
        
        
    case 'site_name_option':

        $state_id = $_POST['state_id'];

        $state_id_type_options = site_name("", $state_id);

        $site_type_options = select_option($state_id_type_options, "Select the site");

        echo $site_type_options;

        break;
        
    case 'plant_name_option':

        $site_id = $_POST['site_id'];
        // $state_name = $_POST['state_name'];

        $site_id_type_options = plant_name("",$site_id);

        $plant_name_option = select_option_create($site_id_type_options);

        echo $plant_name_option;

        break;
        
        case 'category_name_option':

        $department_type = $_POST['department_type'];
        $main_category   = $_POST['main_category'];
        
        $sub_category_name_type_options = category_creation("", $department_type,$main_category);
        
        $sub_name_option = select_option_create($sub_category_name_type_options,"Select the Category");

        echo $sub_name_option;

        break;
        
        case 'category_name_option_filter':

        $department_type = $_POST['department_type'];
        $main_category   = $_POST['main_category'];
        $sub_category_name_type_options = category_creation("", $department_type,$main_category);
        
        $sub_name_option = select_option($sub_category_name_type_options,"Select the Category");

        echo $sub_name_option;

        break;

        
    
            break;

            case 'get_plant_name':

                $site_id = $_POST['site_id'];
        
                $site_name_type_options = plant_type($site_id);
        
                $plant_name_option = select_option($site_name_type_options, "Select the Plant");
                // print_r($category_name_option);
                echo $plant_name_option;
        
                break;
                // case 'get_plant_name_1':

                //     $location1 = $_POST['location_id'];
            
                //     $location1_type_options = category_creation("", $location1);
            
                //     $location1_type_option = select_option($location1_type_options, "Select the Category");
                //     // print_r($category_name_option);
                //     echo $location1_type_option;
            
                //     break;



            // break;
    case 'document_upload_add_update':
        //     $unique_id = "";
        
            

         // $fileUploadPath = $fileUploadConfig->get("upload_folder");

         //    if($_POST["document_name"] == 1){
         //        // Create Folder in root->uploads->(this_folder_name) Before using this file upload
         //        $fileUploadConfig->set("upload_folder", $fileUploadPath . $folder_name."/image" . DIRECTORY_SEPARATOR);
         //    }
         //    if($_POST["document_name"] == 2){
         //        // Create Folder in root->uploads->(this_folder_name) Before using this file upload
         //        $fileUploadConfig->set("upload_folder", $fileUploadPath . $folder_name."/document" . DIRECTORY_SEPARATOR);
         //    }
            
            
            
         //        //     // File Upload Library Call
         //   $fileUpload = new Alirdn\SecureUPload\SecureUPload($fileUploadConfig);

          
            
            $document_name          = $_POST["document_name"];  
            
            $screen_unique_id       = $_POST["screen_unique_id"]; 

           // $unique_id              = $_POST["unique_id"];
            
            $update_where            = "";
    
           // print_r($document_name);
            if($_POST["document_name"] == 3){
                    $allowedExts = array("mp3", "mp4", "wma" , "wav");
                    $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
 
                    if ((($_FILES["test_file"]["type"] == "audio/mp3")|| ($_FILES["test_file"]["type"] == "audio/wav"))){


 
                            $file_exp = explode(".",$_FILES["test_file"]['name']);
                            $tem_name =  random_strings(25).".".$file_exp[1];
                 
                            move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/complaint_category/audio/" . $tem_name);
                    }
                    if (!empty($_FILES["test_file"]['name'])) {
                    $file_names     = $tem_name;
                    $file_org_names = $_FILES["test_file"]['name'];
                }
            } 

            if($_REQUEST["document_name"] == 1){
                    $allowedExts = array("jpeg","png","jpg");
                    $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
 
                    if ((($_FILES["test_file"]["type"] == "image/jpeg")|| ($_FILES["test_file"]["type"] == "image/png")|| ($_FILES["test_file"]["type"] == "image/jpg"))){


 
                            $file_exp = explode(".",$_FILES["test_file"]['name']);
                            $tem_name =  random_strings(25).".".$file_exp[1];
                 
                            move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/complaint_category/image/" . $tem_name);
                    }
                    if (!empty($_FILES["test_file"]['name'])) {
                    $file_names     = $tem_name;
                    $file_org_names = $_FILES["test_file"]['name'];
                }
            } 

            if($_REQUEST["document_name"] == 2){
                // print_r($document_name);
               
                    $allowedExts = array("xls","xlsx","pdf");
                    $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
                      //  print_r($extension);
                    if ((($_FILES["test_file"]["type"] == "application/pdf")|| ($_FILES["test_file"]["type"] == "application/vnd.ms-excel")|| ($_FILES["test_file"]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') || ($_FILES["test_file"]["type"] == "application/xls"))){


                        
                            $file_exp = explode(".",$_FILES["test_file"]['name']);
                            $tem_name =  random_strings(25).".".$file_exp[1];
                 
                            move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/complaint_category/document/" . $tem_name);
                    }
                    
                    if (!empty($_FILES["test_file"]['name'])) {
                    $file_names     = $tem_name;
                    //print_r($file_names);
                    $file_org_names = $_FILES["test_file"]['name'];
                }
            } 

                
           
        if($file_names != ''){
            $columns            = [
                "screen_unique_id"     => $screen_unique_id,
                "doc_name"             => $document_name,
                "file_name"            => $file_names,
                "unique_id"            => unique_id($prefix)
            ];
        
                   $action_obj     = $pdo->insert($table_sub,$columns);
                        
                    $msg                = "create";

                    $file_names = "";
                    
                    
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
           }
    
             
    
            $json_array   = [
                "status"    => $status,
                "data"      => $data,
                "error"     => $error,
                "msg"       => $msg,
                "sql"       => $sql,
                
            ];
    
            echo json_encode($json_array);
           
    
        
        break;
         
    case 'document_upload_add_update1':
                    
            $document_name          = $_POST["document_name"];  
            
            $screen_unique_id       = $_POST["screen_unique_id"]; 

           // $unique_id              = $_POST["unique_id"];
            
            $update_where            = "";
    
           // print_r($document_name);
            if($_POST["document_name"] == 3){
                    $allowedExts = array("mp3", "mp4", "wma" , "wav");
                    $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
 
                    if ((($_FILES["test_file"]["type"] == "audio/mp3")|| ($_FILES["test_file"]["type"] == "audio/wav"))){

                            $file_exp = explode(".",$_FILES["test_file"]['name']);
                            $tem_name =  random_strings(25).".".$file_exp[1];
                 
                            move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/complaint_category/audio/" . $tem_name);
                    }
                    if (!empty($_FILES["test_file"]['name'])) {
                    $file_names     = $tem_name;
                    $file_org_names = $_FILES["test_file"]['name'];
                }
            } 

            if($_REQUEST["document_name"] == 1){
                    $allowedExts = array("jpeg","png","jpg");
                    $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
 
                    if ((($_FILES["test_file"]["type"] == "image/jpeg")|| ($_FILES["test_file"]["type"] == "image/png")|| ($_FILES["test_file"]["type"] == "image/jpg"))){

                            $file_exp = explode(".",$_FILES["test_file"]['name']);
                            $tem_name =  random_strings(25).".".$file_exp[1];
                 
                            move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/complaint_category/image/" . $tem_name);
                    }
                    if (!empty($_FILES["test_file"]['name'])) {
                    $file_names     = $tem_name;
                    $file_org_names = $_FILES["test_file"]['name'];
                }
            } 

            if($_REQUEST["document_name"] == 2){
                // print_r($document_name);
               
                    $allowedExts = array("xls","xlsx","pdf");
                    $extension = pathinfo($_FILES["test_file"]['name'], PATHINFO_EXTENSION);
                      //  print_r($extension);
                    if ((($_FILES["test_file"]["type"] == "application/pdf")|| ($_FILES["test_file"]["type"] == "application/vnd.ms-excel")|| ($_FILES["test_file"]["type"] == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') || ($_FILES["test_file"]["type"] == "application/xls"))){

                            $file_exp = explode(".",$_FILES["test_file"]['name']);
                            $tem_name =  random_strings(25).".".$file_exp[1];
                 
                            move_uploaded_file($_FILES["test_file"]["tmp_name"], "../../uploads/complaint_category/document/" . $tem_name);
                    }
                    
                    if (!empty($_FILES["test_file"]['name'])) {
                    $file_names     = $tem_name;
                    //print_r($file_names);
                    $file_org_names = $_FILES["test_file"]['name'];
                }
            } 
        if($file_names != ''){
            $columns            = [
                "screen_unique_id"     => $screen_unique_id,
                "doc_name"             => $document_name,
                "file_name"            => $file_names,
                "unique_id"            => unique_id($prefix)
            ];
        
                   $action_obj     = $pdo->insert($table_sub,$columns);
                        
                    $msg                = "create";

                    $file_names = "";
                    
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
           }

            $json_array   = [
                "status"    => $status,
                "data"      => $data,
                "error"     => $error,
                "msg"       => $msg,
                "sql"       => $sql,
                
            ];
    
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

        // $where = [
        //     //"purchase_unique_id"            => $unique_id,
        //     "screen_unique_id" => $screen_unique_id,
        //     "is_active" => 1,
        //     "is_delete" => 0
        // ];

$where = "screen_unique_id='$screen_unique_id' and is_active=1 and is_delete=0";
        $order_by = "";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //  print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                
               $btn_delete = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_delete;

                $value['file_name'] = image_view("complaint_category", $value['unique_id'], $value['file_name'],$value['doc_name']);
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
                    case 4:
                            $value['doc_name'] = "Chatbot";
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

case 'document_upload_sub_datatable1':
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

$where = "screen_unique_id='$screen_unique_id' and is_active=1 and is_delete=0";
        $order_by = "";


        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        //  print_r($result);
        $total_records = total_records();

        if ($result->status) {

            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                
               $btn_delete = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_delete;

                $value['file_name'] = image_view("complaint_category", $value['unique_id'], $value['file_name'],$value['doc_name']);
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
                    case 4:
                            $value['doc_name'] = "Chatbot";
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


        case 'document_upload_sub_datatable_mobile':
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
                $value['unique_id'] = '<a href="#" onclick="document_upload_sub_delete(\''.$value['unique_id'].'\')"> <i class="fa fa-trash fa-2x text-danger"></i></a>';

                
                switch ($value['doc_name']) {
                    case 1:
                        $value['doc_name'] = "Image";
                        $value['file_name'] = '<img src="../../uploads/complaint_category/image/'.$value['file_name'].' height="50px" width="50px" >'  ;
                        break;
                    case 2:
                        $value['doc_name'] = "Document";
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


    case 'document_upload_sub_delete':

            $unique_id  = $_POST['unique_id'];
           
            $columns            = [
                "is_delete"   => 1,
            ];
    
            $update_where   = [
                "unique_id"     => $unique_id
            ];
    
            $action_obj     = $pdo->update($table_sub,$columns,$update_where);
    
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

    case "assign_staff_name" :

        $department_type    = $_POST['department_type'];
        $site_name          = $_POST['site_name'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";

         

        $columns        = [
            "user_id"

        ];
        $table_details  = [
            "periodic_creation_sub",
            $columns
        ];
        // if($site == "All"){
        //     $where          = "is_active = 1 and is_delete = 0 and department_name ='".$department_type."' and stage = 1";
        // }else{
         

        $where          = "is_active = 1 and is_delete = 0 and department_name ='".$department_type."' and (site_id like '%,".$site_name.",%' or site_id like '%,".$site_name."%' or site_id like '%".$site_name.",%' or site_id = 'All') and level = 1"; 
    //}
        
        $result         = $pdo->select($table_details,$where);
      //  print_r($result);
        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0]['user_id'],
                
            ];
        } else {
            print_r($result);
        }
        
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

    case 'resolved_complaints':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as resolved_complaints",
       
            
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "stage_1_status = 2 and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
        $resolved_complaints = $value['resolved_complaints'];

        }
                
        $json_array = [
                "resolved_complaints"        => $resolved_complaints,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;  
             case 'state_name_options':

            $plant_name = $_POST['plant_name'];
    
            // $state_id_type_options = plant_wise_state("", $plant_name);
    
            // // $site_type_options = select_option($state_id_type_options, "Select the site");
    
            // echo $state_id_type_options;
            global $pdo;

    $table_name    = "plant_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "(select state_name from state_creation as b where b.unique_id = ".$table_name.".state_id) as state_id",
        "(select site_name from site_creation as c where c.unique_id = ".$table_name.".site_id) as site_id",
        "state_id as state_name",
        "site_id as site_name"
        // "site_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = "is_delete = 0 and is_active = 1";

    
     if ($plant_name) {

        $where              .= " AND unique_id = '".$plant_name."'";
        
        
    }
    

    $plant_creation = $pdo->select($table_details, $where);

// print_r($plant_creation);
    
    
    if ($plant_creation->status) {
        
          $data = $plant_creation->data;

        // print_r($state);


    $state = $data[0]['state_id'];
    $site = $data[0]['site_id'];
    $state_id = $data[0]['state_name'];
    $site_id = $data[0]['site_name'];

    $jsonResponse = json_encode(["state" => $state,"site" => $site,"state_id" => $state_id,"site_id" => $site_id]);
    // $jsonResponse = json_encode(["state" => 0,"site" => 0]);

            echo $jsonResponse;

    } 
    else {
        print_r($plant_creation);
        return 0;
    }
    
            break;
    case 'pending_complaints':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as pending_complaints",
       
            
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "(stage_1_status = 1 or stage_1_status = 0) and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
        $pending_complaints = $value['pending_complaints'];

        }
                
        $json_array = [
                "pending_complaints"        => $pending_complaints,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;

    case 'total_complaints':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as total_complaints",
       
            
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
        
        $total_complaints = $value['total_complaints'];

        }
                
        $json_array = [
                "total_complaints"        => $total_complaints,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;
        
        case 'project_name':
        $company_id          = $_POST['company_id'];
        $project_name_options  = get_project_name("", $company_id);
        $project_name_options  = select_option($project_name_options, "Select the Project Name");
        echo $project_name_options;
        break;
        
        case 'main_category':
        $department_id          = $_POST['department_id'];
        $main_category_options  = main_category_creation("", $department_id);
        $main_category_options  = select_option($main_category_options, "Select the Main Category");
        echo $main_category_options;
        break;
        
        case 'complaint_category':

        $department_type = $_POST['department_id'];
        $main_category = $_POST['main_category_id'];

        $complaint_category_options = category_creations("", $department_type,$main_category);

        $complaint_category_option = select_option($complaint_category_options, "Select the Category");
        // print_r($category_name_option);
        echo $complaint_category_option;

        break;
        


    case 'resolved_complaints':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as resolved_complaints",
       
            
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "stage_1_status = 2 and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
        $resolved_complaints = $value['resolved_complaints'];

        }
                
        $json_array = [
                "resolved_complaints"        => $resolved_complaints,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;  

    case 'pending_complaints':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as pending_complaints",
       
            
        ];
        $table_details  = [
            $table,
            $columns
        ];
        $where        = "(stage_1_status = 1 or stage_1_status = 0) and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
        $pending_complaints = $value['pending_complaints'];

        }
                
        $json_array = [
                "pending_complaints"        => $pending_complaints,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;

    case 'total_complaints':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as total_complaints",
       
            
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
        
        $total_complaints = $value['total_complaints'];

        }
                
        $json_array = [
                "total_complaints"        => $total_complaints,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;
    
    case 'category_complaint':


        $columns        = [
            "department_name",
            "total_cnt",
            "completed_cnt",
            "pending_cnt",
            "icon"
        ];

        $table_details  = [
            'view_category_wise_overall_cnt',
            $columns
        ];

        $where = "";
    
        $result         = $pdo->select($table_details, $where);
       // print_r($result);

        if ($result->status) {

            $res_array      = $result->data;
            $s_no = 1;
            $table_data  = '<div class="row">';
            foreach ($res_array as $key => $value) {
                if($s_no < 10){$sno = "0".$s_no;}else{
                    $sno = $s_no;
                }
                $value['department_name'] = department_type($value['department_name'])[0]['department_type'];
              
                $table_data .= '<div class="col-lg-4 col-md-6 mb-65">
                        <div class="services-item col-lg-10 col-md-10">
                            <div class="services-icon">
                                <img src="https://zigma.in/g_admin/assets/images/'.$value['icon'].'" alt="Services">
                            </div>
                            <div class="services-text">
                            <h4 class="title"><a href="#"></a>'.$value['department_name'].'</h4>
                            <table class="table-det">
                                <tr>
                                    <td>Total</td>
                                    <td>:</td>
                                    <td class="rs-count cunt2">'.$value['total_cnt'].'</td>
                                </tr>
                                <tr>
                                    <td>Pending</td>
                                    <td>:</td>
                                    <td class="rs-count cunt2">'.$value['pending_cnt'].'</td>
                                </tr>
                                <tr>
                                    <td>Closed</td>
                                    <td>:</td>
                                    <td class="rs-count cunt2">'.$value['completed_cnt'].'</td>
                                </tr>
                            </table> 
                        <div class="serial-number">'.$sno.'
                        </div> 

                          
                        </div>
                    </div>
                    </div>
                        ';
                        $s_no ++;
                      
            }
        }
        $table_data .= '</div>';
        $json_array = [
            'data'            => $table_data,

        ];
        echo json_encode($json_array);

        break;  


    case 'feedback_status':
    
        $json_array     = "";

        $columns        = [           
            
            "COUNT(id) as complaint_cnt",
       
            
        ];
        $table_details  = [
            "feedback_status",
            $columns
        ];
        $where          = "complaint_no = '".$_POST['complaint_no']."' and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
        //print_r($result);
        foreach($res_array as $value){
        
        $complaint_cnt = $value['complaint_cnt'];

        }
                
        $json_array = [
                "complaint_cnt"        => $complaint_cnt,
                
                
            ];
        
         echo json_encode($json_array);
         
        break; 

    case 'feedback_status_add':
        $feedback_rating    =  $_POST['feedback_rating'];
        $feedback_comment   =  $_POST['feedback_comment'];
        $json_array     = "";

        $columns        = [           
            
            "unique_id",
            "screen_unique_id",
            "complaint_no",
            "department_name",
            "complaint_category",
       
            
        ];
        $table_details  = [
            "complaint_creation",
            $columns
        ];
        $where          = "complaint_no = '".$_POST['complaint_no']."' and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
        //print_r($res_array);
        foreach($res_array as $value){
        
            $form_unique_id     = $value['unique_id'];
            $screen_unique_id   = $value['screen_unique_id'];
            $complaint_no       = $value['complaint_no'];
            $department_name    = $value['department_name'];
            $category_name      = $value['complaint_category'];
            $user_unique_id     =  "Web Entry";
            $entry_date         =  date('Y-m-d');
            
            

        }
                
       
         $update_where       = "";
        // print_r($pincode);
        $columns            = [
            
            "feedback_rating"                         => $feedback_rating,
            "feedback_comment"                        => $feedback_comment,
            "form_unique_id"                          => $form_unique_id,
            "screen_unique_id"                        => $screen_unique_id,
            "department_name"                         => $department_name,
            "entry_date"                              => $entry_date,
            "feedback_entry_by"                       => $user_unique_id,
            "category_name"                           => $category_name,
            "complaint_no"                            => $complaint_no,
            "unique_id"                          =>  unique_id($prefix)
                   ];
       // check already Exist Or not
        $table_details      = [
            "feedback_status",
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = "is_delete = 0 and form_unique_id = '".$form_unique_id."'";
        // When Update Check without current id
        // if ($unique_id) {
        //     $select_where   .= 'and ORDER BY id AND unique_id !="'.$unique_id.'"';
        // }
        $action_obj     = $pdo->select($table_details,$select_where);
        // print_r($action_obj);die();
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
        // print_r($data['count']);

        if ($data[0]["count"] !=0) {

            $msg        = "already";
            
        } 
        else if ($data[0]["count"] == 0) {
            
                // Insert Begins            
               $action_obj     = $pdo->insert_web("feedback_status",$columns);
                // Insert Ends
            
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
            }else {
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

    case 'feedback_status_entered':
       $json_array     = "";

        $columns        = [           
            
            "feedback_rating",
            "feedback_comment"
       
            
        ];
        $table_details  = [
            "feedback_status",
            $columns
        ];
        $where          = "complaint_no = '".$_POST['complaint_no']."' and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        $res_array      = $result->data;
        //print_r($result);
        foreach($res_array as $value){
        
        $feedback_rating = $value['feedback_rating'];
        $feedback_comment = $value['feedback_comment'];

        }
                
        $json_array = [
                "feedback_rating"           => $feedback_rating,
                "feedback_comment"          => $feedback_comment,
                
                
            ];
        
         echo json_encode($json_array);
        break; 
    default:

        break;
}

function random_strings($length_of_string)
{
 
    // String of all alphanumeric character
    $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
 
    // Shuffle the $str_result and returns substring
    // of specified length
    return substr(str_shuffle($str_result),
                       0, $length_of_string);
}


?>