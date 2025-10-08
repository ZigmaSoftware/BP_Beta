 <?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table            = "periodic_creation_main";
$table_sub        = "periodic_creation_sub";
 
// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';


// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$country_name       = "";
$state_name         = "";
$prefix             = "per";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':
       
        $unique_id              = $_POST["unique_id"];
        
        $user_id                = $_POST['user_name'];
        $screen_unique_id       = $_POST["screen_unique_id"];

        $update_where       = "";
        
        $columns            = [
            
            "user_id"         => $user_id,
            "screen_unique_id"      => $screen_unique_id,
            "unique_id"             => $main_unique_id = unique_id($prefix)
        ];
        
        // check already Exist Or not
        $table_details      = [
            $table,
                [
                "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'user_id = "'.$user_id.'"   AND is_delete = 0  ';
            
            // When Update Check without current id
            if ($unique_id) {
                $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
               

            } 
            $action_obj         = $pdo->select($table_details,$select_where);
           // print_r($action_obj);
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
                     $sublist_columns  = [
                    "form_unique_id"      => $unique_id,
                    "user_id"      => $user_id,
                    
                ];

                $where_sublist =[
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];
                    
                    unset($columns['unique_id']);
                    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];

                    $main_unique_id = $unique_id;

                    $action_obj         = $pdo->update($table,$columns,$update_where);
                    $action_obj_sub     = $pdo->update($table_sub,$sublist_columns,$where_sublist);
                    // Update Ends
                } else {   

                 $sublist_columns  = [
                     "form_unique_id"      => $main_unique_id,
                    "user_id"      => $user_id,
                    
                ];

                $where_sublist =[
                    "screen_unique_id"  => $screen_unique_id,
                    "is_active"         => 1,
                    "is_delete"         => 0
                ];                 
                    // Insert Begins            
                    $action_obj       = $pdo->insert($table,$columns);
                    $action_obj_sub   = $pdo->update($table_sub,$sublist_columns,$where_sublist);
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

        $periodic_unique_id = "";

        if ($unique_id) {
            $periodic_unique_id = $unique_id;
        } else {
            $periodic_unique_id = $columns['unique_id'];
        }

        $json_array   = [
            "status"           => $status,
            "data"             => $data,
            "error"            => $error,
            "msg"              => $msg,
            "sql"              => $sql,
            "periodic_unique_id"    => $periodic_unique_id
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
            "user_id",
            "'' as user_type",
            "'' as mobile_no",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        // if($_POST['department_name']!=''){

        //     $department_name = "AND department_name = '".$_POST['department_name']."'";
        // } else {
        //     $department_name = "";
        // }

        $where      = " is_active = 1 ";
        $where     .= " AND is_delete = 0  ".$department_name." ";
        
       
        $order_by       = "";
        
        //$order_by       = "entry_date DESC";
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column, $order_dir, $columns);

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();
       
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                // $value['open_date'] =   disdate($value['open_date']);
                
                $btn_view           = btn_print2($folder_name ,$value['unique_id'] . $action_btn,"view.php");
                // $btn_view                       = btn_print($folder_name, $value['unique_id'] . $action_btn,"view.php");
                $btn_update         = btn_update($folder_name,$value['unique_id']);
                $btn_delete         = btn_delete($folder_name,$value['unique_id']);

                $user_details    = user_name($value['user_id']);
                $value['user_id']    = disname($user_details[0]['staff_name']);

                $value['mobile_no']  = $user_details[0]['mobile_no'];
                $user_type =  user_type($user_details[0]['user_type_unique_id']);


                $value['user_type'] = $user_type[0]['user_type'];
                // $value['department_name'] = department_type($value['department_name'])[0]['department_type'];
                // $value['category_name'] = category_name($value['category_name'])[0]['category_name'];
                $value['unique_id']     = $btn_view . $btn_update . $btn_delete;
               
                $value['unique_id']             = $btn_view . $btn_update . $btn_delete;
                $data[]                 = array_values($value);
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

    case 'periodic_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "periodic_sub";
        
        // Fetch Data
        $screen_unique_id = $_POST['screen_unique_id']; 
        $unique_id        = $_POST['unique_id']; 

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
            "@a:=@a+1 s_no",
            "'' as department_name",
            "category_name",
            "'' as site_id",
            "level", 
            "starting_count",
            "ending_count",
            "unique_id",
            "site_id as site",
            "department_name as department",
        ];
        $table_details  = [
            $table_sub." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "screen_unique_id"    => $screen_unique_id,
            "is_active"           => 1,
            "is_delete"           => 0
        ];
        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

               
               switch($value['level']){
                    case 1:
                        $value['level'] = "Level 1";
                        break;
                    case 2:
                        $value['level'] = "Level 2";
                        break;
                    case 3:
                        $value['level'] = "Level 3";
                        break;
                    case 4:
                        $value['level'] = "Level 4";
                        break;
                    case 5:
                        $value['level'] = "Level 5";
                        break;
                    case 6:
                        $value['level'] = "Level 6";
                            break;
                    case 7:
                        $value['level'] = "Level 7";
                         break;
               }
                
                if($value['category_name'] != 'All'){
                    $value['category_name']      = category_name($value['category_name'])[0]['category_name'];
                }else{
                    $value['category_name'] = "All Categories";
                }

               if($value['site'] != 'All'){
                   $exp_site = explode(',',$value['site']);
                   foreach($exp_site as $site){
                        $site_name = site_name($site);
                        $site_id = $site_name[0]['site_name'];
                        $value['site_id'] .= "site - ".$site_id."<br>";
                    }
                }else{
                    $value['site_id'] = "All sites";
                }
                
                if($value['department'] != 'All'){
                   $exp_department = explode(',',$value['department']);
                   foreach($exp_department as $department){
                        $department_name = department_type($department);
                        
                        $department_id = $department_name[0]['department_type'];
                       
                        $value['department_name'] .= $department_id."<br>";
                    }
                }else{
                    $value['department_name'] = "All sites";
                }
                
              

                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_delete;
                $data[]                 = array_values($value);
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


    case 'periodic_add_update':

            $level                  = $_POST["level_no"];
            $ending_count           = $_POST["ending_count"];
            $starting_count         = $_POST["starting_count"];
            $screen_unique_id       = $_POST["screen_unique_id"];
            $unique_id              = $_POST["unique_id"];
            $department        = $_POST["department_name"];
            $complaint_cate     = $_POST["complaint_category"];
            $user_name              = $_POST["user_name"];
            $site              = $_POST["site_name"];

            if($site == ''){
                $site_name = "All";
            }else{
                $site_name = $site;
            }
            if($department == ''){
                $department_name = "All";
            }else{
                $department_name = $department;
            }
            if($complaint_cate == ''){
                $complaint_category = "All";
            }else{
                $complaint_category = $complaint_cate;
            }
    
            $update_where               = "";
            
                $columns            = [
                    "level"             => $level,
                    "department_name"       => $department_name,
                    "category_name"         => $complaint_category,
                    "starting_count"    => $starting_count,
                    "ending_count"      => $ending_count,
                    "user_id"      => $user_name,
                    "site_id"      => $site_name,
                    "screen_unique_id"  => $screen_unique_id,
                    "unique_id"         => unique_id($prefix)
                ];
            
            
                // check already Exist Or not
                $table_details      = [
                $table_sub,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'is_delete = 0 AND level ="'.$level.'" and  department_name = "'.$department_name.'" and category_name = "'.$complaint_category.'" and screen_unique_id = "'.$screen_unique_id.'" and user_id = "'.$user_name.'"';
    
            // When Update Check without current id
            if ($unique_id) {
                $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
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
            } else if (($data[0]["count"] == 0) && ($msg != "error")) {
                // Update Begins
                if($unique_id) {
    
                    unset($columns['unique_id']);
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_sub,$columns,$update_where);
    
                // Update Ends
                } else {
    
                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_sub,$columns);
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
   

    case 'periodic_sub_delete':

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

    case 'designation':

        $department_name      = $_POST['department_name'];

        $designation_name_options         = designation_name('', $department_name);
        $designation_name_options         = select_option($designation_name_options, "Select ");

        $data   =  $designation_name_options;

        $json_array   = [
            "data"      => $data,
        ];

        echo json_encode($json_array);
        break;

    case 'category':

        $department_name      = $_POST['department_name'];

        $category_name_options         = category_name('', $department_name);
        $category_name_options         = select_option_category($category_name_options, "All Categories");

        $data   =  $category_name_options;

        $json_array   = [
            "data"      => $data,
        ];

        echo json_encode($json_array);
        break;

    
        case 'get_usertype':

           $json_array     = "";
           $usr_name =  $_POST['user_name'];
            // user_name($value['assign_by'])[0]['user_name']);
           $user_name = user_name($usr_name)[0]['user_name'];

           // $today  =  date('Y-m-d');
           $columns        = [           
            
            "user_type_unique_id",
            "mobile_no",
            "designation_id"
        ];
        $table_details  = [
            "user",
            $columns
        ];
        $where        = "user_name = '".$user_name."' and is_delete = 0";
        $result         = $pdo->select($table_details,$where);
        // print_r($result);
        $res_array      = $result->data;
       // print_r($res_array);
        foreach($res_array as $value){
        
            $user_type     = user_type($value['user_type_unique_id'])[0]['user_type'];
            $mobile_no      = $value['mobile_no'];
            $designation    =designation_name($value['designation_id'])[0]['designation_name'];

        }
                
        $json_array = [
                "user_type"            => $user_type,
                "mobile_no"            => $mobile_no,
                "designation"          =>$designation,
                
                
            ];
        
         echo json_encode($json_array);
         
        break;

        case 'previous_day_count':


        $screen_unique_id  = $_POST['screen_unique_id'];
        $starting_count    = $_POST['starting_count'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "starting_count",
            "ending_count",          
        ];
        $table_details  = [
            "periodic_creation_sub",
            $columns
        ];
           

        $where = "screen_unique_id = '".$screen_unique_id."' and is_delete = 0 and is_active = 1 order by id DESC LIMIT 1";

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
        
        
        
        case 'get_dept_name':

           $json_array     = "";
           $usr_name =  $_POST['user_name'];
            // user_name($value['assign_by'])[0]['user_name']);
        //   $user_name = user_name($usr_name)[0]['user_name'];

           // $today  =  date('Y-m-d');
           
           $data .= '<select name="department_name" id="department_name" class="select2 form-control" onchange="get_category()"   required>
                     <option value="">Select Department</option>';
                     
                    
                                $host = 'localhost'; // or IP address
                                $dbname = 'zigma_complaints';
                                $username = 'zigma';
                                $password = '?WSzvxHv1LGZ';


                                    $dbh = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    
                                $query = "select department_name from user where unique_id = '$usr_name' and is_delete ='0'";
                                // echo $query;
                                $sql_query = $dbh->prepare($query);
                                $sql_query->execute();
                                $result = $sql_query->fetch(PDO::FETCH_ASSOC);
                                
                                
                                $department_id  = $result['department_name'];
                               
                                $department_exp = explode(",",$department_id);
                                
                                $count = count($department_exp);
                                
                               if($count == '1' && $department_id != 'All'){
                                    
                                    $user_department_name = department_type($department_id)[0]['department_type'];
                                    $data .= "<option value='$department_id'>$user_department_name</option>";
                                    
                                }
                                
                                
                                if($department_id != 'All' && $count != '1'){
                                    
                                
                                $department_name = explode(",",$department_id);
                                
                                foreach($department_name as $key => $value){
                                $user_department_name = department_type($value)[0]['department_type'];
                                
                                   $data .= "<option value='$value'>$user_department_name</option>";
                                
                               
                                }
                                }
                                elseif($department_id == 'All')
                                {
                                    $query = "select unique_id,department_type from department_creation where is_delete='0'";
                                // echo $query;
                                $sql_query = $dbh->prepare($query);
                                $sql_query->execute();
                                $result = $sql_query->fetchAll();
                                
                                
                                
                                foreach($result as $value){
                                $department_id    = $value['unique_id'];
                                $department_name  = $value['department_type'];
                                   $data .= "<option value='$department_id'>$department_name</option>";
                               
                                }
                                }
                                
                                
                                
                                
                               $data .=  "</select>";
           
           
           $site_data .= '<select class="select2 form-control" tabindex="6" multiple id="site" onchange="get_site_ids()"><option value="All">All Sites</option>';
                          
                     
                    
                                
    
                               $query = "select site_name from user where unique_id = '$usr_name' and is_delete ='0'";
                               // echo $query;
                                $sql_query = $dbh->prepare($query);
                                $sql_query->execute();
                                $result = $sql_query->fetch(PDO::FETCH_ASSOC);
                                
                                
                                $site_id  = $result['site_name'];
                                
                                $count_q = explode(",",$site_id);
                                $count = count($count_q);
                                
                                if($count == '1'){
                                    
                                    $user_site_name = site_name($site_id)[0]['site_name'];
                                     
                                    $site_data .= "<option value='$site_id'>$user_site_name</option>";
                                    
                                }
                                
                                
                                if($site_id != 'All' && $count != '1'){
                                    
                                
                                $site_name = explode(",",$site_id);
                                
                                foreach($site_name as $key => $value){
                                $user_site_name = site_name($value)[0]['site_name'];
                                
                                   $site_data .= "<option value='$value'>$user_site_name</option>";
                                
                               
                                }
                                }
                                elseif($site_id == 'All')
                                {
                                    $query = "select unique_id,site_name from site_creation where is_delete='0'";
                                // echo $query;
                                $sql_query = $dbh->prepare($query);
                                $sql_query->execute();
                                $result = $sql_query->fetchAll();
                                
                                
                                
                                foreach($result as $value){
                                $site_id    = $value['unique_id'];
                                $site_name  = $value['site_name'];
                                   $site_data .= "<option value='$site_id'>$site_name</option>";
                               
                                }
                                }
                                
                                
                                
                                
                                
                               $data .=  "</select>";
                
        $json_array = [
                "data"                 => $data,
                "site_data"            => $site_data,
                
                
                
            ];
        
         echo json_encode($json_array);
         
        break;
case 'get_dept_category':
    $staff_id = $_POST['user_name'];

    // Log the incoming staff ID
    error_log("➡️ STAFF ID RECEIVED: " . $staff_id);

    $department_label = "Unknown";
    $category_options = '<option value="">Select [Main Category - Category]</option>';

    // 1. Fetch department from staff table
    $columns = ['department'];
    $table_details = ['staff', $columns];
    $where = ['unique_id' => $staff_id, 'is_delete' => 0];

    $result = $pdo->select($table_details, $where);
    error_log("📦 Staff Fetch Result: " . print_r($result, true));

    if ($result->status && !empty($result->data)) {
        $department_id = $result->data[0]['department'];
        error_log("✅ Department ID: " . $department_id);

        // Get readable department name
        $department_label = department_type($department_id)[0]['department_type'];
        error_log("📘 Department Label: " . $department_label);

        // 2. Fetch categories joined with main categories
        $query = "
            SELECT 
                cc.unique_id AS category_id,
                cc.category_name,
                mcc.main_category_name
            FROM category_creation cc
            JOIN main_category_creation mcc ON cc.main_category_name = mcc.unique_id
            WHERE cc.department = :department_id AND cc.is_delete = 0
        ";

        $stmt = $pdo_conn->prepare($query);
        $stmt->execute([':department_id' => $department_id]);
        $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

        error_log("🧾 Categories Fetched: " . print_r($categories, true));

        foreach ($categories as $row) {
            $id = $row['category_id'];
            $label = "[" . $row['main_category_name'] . " - " . $row['category_name'] . "]";
            $category_options .= "<option value='$id'>$label</option>";
        }
    } else {
        error_log("❌ No department found or staff record missing.");
    }

    // Final output
    $response = [
        "department_name"   => $department_label,
        "category_options"  => $category_options
    ];

    error_log("✅ Final JSON Response: " . json_encode($response));
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;

    break;

        
        
        
       
   default:
   break;
}
?>