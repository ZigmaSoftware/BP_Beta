<?php 

//$logo_img       = "img/logo-light.png";
//$logo_img_dark  = "img/logo-light.png";

$logo_img       = "img/logo-new.png";
$logo_img_dark  = "img/logo-new.png"; 
$logo_img_sm    = "";
$logo_img_print = ""; 

// Default Admin User Type
$password = '3sc3RLrpd17';
$enc_method = 'aes-256-cbc';
$enc_password = substr(hash('sha256', $password, true), 0, 32);
$enc_iv = "av3DYGLkwBsErphc";


function get_client_ip() {
    $ipaddress = '';
   if (getenv('HTTP_CLIENT_IP'))
       $ipaddress = getenv('HTTP_CLIENT_IP');
   else if(getenv('HTTP_X_FORWARDED_FOR'))
       $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
   else if(getenv('HTTP_X_FORWARDED'))
       $ipaddress = getenv('HTTP_X_FORWARDED');
   else if(getenv('HTTP_FORWARDED_FOR'))
       $ipaddress = getenv('HTTP_FORWARDED_FOR');
   else if(getenv('HTTP_FORWARDED'))
      $ipaddress = getenv('HTTP_FORWARDED');
   else if(getenv('REMOTE_ADDR'))
       $ipaddress = getenv('REMOTE_ADDR');
   else
       $ipaddress = 'UNKNOWN';
   return $ipaddress;
}

$admin_user_type = "5f97fc3257f2525529";
$hr_user_type    = "5ff71f5fb5ca556748";

// Date Related Function
function today($format = "") {
    if ($format) {
        return date($format);
    }

    return date("Y-m-d");
}


function today_time($format = "") {
    if ($format) {
        return date($format);
    }

    return date("Y-m-d H:i:s");
}

function disdate ($date) {

    $result     = "";

    if ($date) {
        $result =  implode("-",array_reverse(explode("-",$date)));
    }

    return $result;
}

$today            = today();
$today_time       = today_time();

// Bill No Generation

function bill_no ($table_name,$where,$prefix = "", $y = 1,$m = 1, $d = 1,$custom_date = false,$separator = "") {
    $billno = $prefix;

    if (!$custom_date) {
        $custom_date = date("Y-m-d");
    }

    if ($y) {
        $billno .= date('y',strtotime($custom_date)).$separator;
    }

    // if ($m) {
    //     $billno .= date('m',strtotime($custom_date)).$separator;
    // }

    // if ($d) {
    //     $billno .= date('d',strtotime($custom_date)).$separator;
    // }

    $bill_order_no  =  save_status($table_name,$where);

    $billno        .= sprintf("%05d",$bill_order_no);

    return $billno;
}
// user type permission

// user type permission

// Get Final Bill No

function save_status ($table_name,$where) {
    if ($table_name) {
        global $pdo;

        $columns    = [
            "max(id) AS save_status"
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {

        $res_array      = $result->data[0]['save_status'] + 1;

        } else {
            print_r($result);
            $res_array = uniqid().rand(10000,99999)."Error";
        }

        return $res_array;
    }
}

// Permissions Extract From user_screen_action Table 

$perm_columns       = [
    'variable_name',
    'is_active',
    'unique_id'
];
$perm_table         = 'user_screen_actions';
$perm_table_details = [
    $perm_table,
    $perm_columns
];
$perm_where         = '';      
$result_obj         = $pdo->select($perm_table_details,$perm_where);

if ($result_obj->status) {
    $status     = $result_obj->status;
    $data       = $result_obj->data;
    $error      = "";
    $sql        = $result_obj->sql;

    $perm_array = [];

    foreach ($data as $data_key => $data_value) {

        if (!$data_value['is_active']) {
            $data_value['unique_id'] = 0;
        }

        $perm_array[$data_value['variable_name']] = $data_value['unique_id'];
    }

    // Extract Permission Variables From $perm_array
    extract($perm_array);

    // Permissions Extract Ends Here

} else {
    $status     = $result_obj->status;
    $data       = $result_obj->data;
    $error      = $result_obj->error;
    $sql        = $result_obj->sql;
    $msg        = "error";
    exit;
}

// Uniqui ID Geneartor
function unique_id($prefix = "") {

    $unique_id = uniqid().rand(10000,99999);

    if($prefix) {
        $unique_id = $prefix.$unique_id;
    }

    return $unique_id;
}

function user_permission ($permission_id = "", $folder_name = "") {

    if (($permission_id) && ($folder_name)) {

    }

    echo "";
    echo '<div class="container h-100">
            <div class="row h-100 justify-content-center align-items-center">
                <div class="form-group">
                <h1>You Don\'t Have a Permission to Access This Page</h1>
                </div>
            </div>  
        </div>';
    exit;
    // return false;
}

function menu_permission ($user_type_id = "") {

    $return_arr = [
        "main_screens"  => "",
        "sections"      => "",
        "screens"       => ""
    ];

    if (($user_type_id)) {

        global $pdo;

        $table_user_permission = "user_screen_permission";

        $select_where   = [
            "user_type" => $user_type_id,
            
        ];

        $screen_columns = [
            // "GROUP_CONCAT(main_screen_unique_id) AS main_screens",
            // "GROUP_CONCAT(section_unique_id) AS sections",
            "GROUP_CONCAT(DISTINCT  screen_unique_id) AS screens"
        ];

        $table_details = [
            $table_user_permission,
            $screen_columns
        ];

        $group_by          = " screen_unique_id ";

        $screen_result     = $pdo->select($table_details,$select_where,"","","","",$group_by);

        if ($screen_result->status) {
            $screen_result_data     = $screen_result->data;

            $return_arr["screens"]  = array_implode($screen_result_data);
        } else {
            print_r($screen_result);
            echo "Menu Permission Error";
            exit;
        }

        $section_columns = [
            "GROUP_CONCAT(DISTINCT  section_unique_id) AS sections"
        ];

        $table_details = [
            $table_user_permission,
            $section_columns
        ];

        $group_by          = " section_unique_id ";

        $section_result     = $pdo->select($table_details,$select_where,"","","","",$group_by);

        if ($section_result->status) {
            $section_result_data     = $section_result->data;

            $return_arr["sections"]  = array_implode($section_result_data);
        } else {
            print_r($section_result);
            echo "Section Permission Error";
            exit;
        }

        $main_screen_columns = [
            "GROUP_CONCAT(DISTINCT  main_screen_unique_id) AS main_screens"
        ];

        $table_details = [
            $table_user_permission,
            $main_screen_columns
        ];

        $group_by               = " main_screen_unique_id ";

        $main_screen_result     = $pdo->select($table_details,$select_where,"","","","",$group_by);

        if ($main_screen_result->status) {
            $main_screen_result_data     = $main_screen_result->data;
            
            $return_arr["main_screens"]  = array_implode($main_screen_result_data);;
        } else {
            print_r($main_screen_result);
            echo "Section Permission Error";
            exit;
        }
    }

    return $return_arr;
}

function array_implode ($value_arr = "") {

    $return_arr = [];

    if (is_array($value_arr)) {

        foreach ($value_arr as $arr_key => $arr_value) {

            $return_arr[] = array_values($arr_value)[0];
            
        }

    }

    return $return_arr;
}

function folder_permission ($folder_name = "") {

}

function acc_year() {
    $acc_year   = '';
    $curr_year  = date("Y");
    
    $today      = strtotime(date("d-m-Y")); 
    $end_date   = strtotime("31-03-".$curr_year);
    $start_date = strtotime("01-04-".$curr_year);
    
    if ($today>=$start_date) {
        $next_year      = $curr_year + 1;
        $acc_year       = $curr_year."-".$next_year;
    }   
    else if ($today<=$end_date) {
        $previous_year  = $curr_year - 1;
        $acc_year       = $previous_year."-".$curr_year; 
    }

    return $acc_year;
    
}

function btn_add ($add_link = "") {
    $final_str = '<a href="'.$add_link.'"><button type="button" class="btn btn-danger waves-effect waves-light flaot-btn"><i class="mdi mdi-plus-circle-multiple"></i> Add New</button></a>';

    return $final_str;
}

function btn_cancel ($list_link = "") {
    $final_str = '<a href="'.$list_link.'"><button type="button" class="btn btn-danger waves-effect waves-light" >Cancel</button></a>';

    return $final_str;
}
function btn_cancel_dar ($list_link = "",$date) {
    $final_str = '<a href="index.php?file=day_attendance_report/list&date='.$date.' "><button type="button" class="btn btn-danger btn-rounded waves-effect waves-light float-right ml-2" >Cancel</button></a>';

    return $final_str;
}

function btn_createupdate($folder_name = "", $unique_id = "",$btn_text ,$prefix = "", $suffix = "_cu", $custom_class = "") {
    $final_str = '<button type="button" class="btn btn-success waves-effect waves-light createupdate_btn  '.$custom_class.'" onclick="'.$folder_name.$suffix.'(\''.$unique_id.'\')">'.$btn_text.'</button>';

    return $final_str;
}
function btn_createupdate_dar($folder_name = "", $unique_id = "",$btn_text ,$date,$prefix = "", $suffix = "_cu", $custom_class = "") {
    $final_str = '<button type="button" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right createupdate_btn  '.$custom_class.'" onclick="'.$folder_name.$suffix.'(\''.$unique_id.'\',\''.$date.'\',\'day_attendance_report\')">'.$btn_text.'</button>';

    return $final_str;
}
function btn_update($folder_name = "",$unique_id = "", $prefix = "",$suffix = "",$date = "",$form = "") {
    $password = '3sc3RLrpd17';
    $enc_method = 'aes-256-cbc';
    $enc_password = substr(hash('sha256', $password, true), 0, 32);
    $enc_iv = "av3DYGLkwBsErphc";


    $menu_screen            = $folder_name."/update";
    $file_name_update       = base64_encode(openssl_encrypt($menu_screen, $enc_method, $enc_password, OPENSSL_RAW_DATA, $enc_iv));

    $uni_id = base64_encode(openssl_encrypt($unique_id, $enc_method, $enc_password, OPENSSL_RAW_DATA, $enc_iv));

    $final_str = '<a href="index.php?file='.$file_name_update.'&unique_id='.$uni_id.'"><i class="mdi mdi-square-edit-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-green"></i></a>';

    
    return $final_str;
}
 function btn_update_app($folder_name = "",$unique_id = "", $prefix = "",$suffix = "",$date = "",$form = "") {
    $password = '3sc3RLrpd17';
    $enc_method = 'aes-256-cbc';
    $enc_password = substr(hash('sha256', $password, true), 0, 32);
    $enc_iv = "av3DYGLkwBsErphc";


   
    
    $final_str = '<a href="complaint_creation.php?unique_id='.$unique_id.'"><i class="mdi mdi-square-edit-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-green"></i></a>';

    
    return $final_str;
}

function btn_update_freeze($folder_name = "",$unique_id = "", $prefix = "",$suffix = "",$from_date = "",$to_date = "") {
   $final_str = '<a class="btn  btn-action mr-1 specl" href="complaint_creation.php?file='.$prefix.$folder_name.$suffix.'/model&unique_id='.$unique_id.'&from_date='.$from_date.'&to_date='.$to_date.'"><i class="far fa-edit"></i></a>';

    
    return $final_str;
}
function btn_create($folder_name = "", $prefix = "",$suffix = "",$date = "",$form = "") {
    // $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&unique_id='.$unique_id.'"><button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-square-edit-outline"></i></button></a>';

    $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/create&date='.$date.'&form='.$form.'"><i class="mdi mdi mdi-shape-square-plus mdi-24px waves-effect waves-light mt-n2 mb-n2 text-green"></i></a>';

    
    return $final_str;
}

function btn_view_mobile($folder_name = "",$unique_id = "", $prefix = "",$suffix = "") {

  
    $final_str = '<a href="http://zigma.in/g_app/stage1_view.php?unique_id='.$unique_id.'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';

    
    return $final_str;

}

function btn_view($folder_name = "",$unique_id = "", $prefix = "",$suffix = "") {


     $password = '3sc3RLrpd17';
    $enc_method = 'aes-256-cbc';
    $enc_password = substr(hash('sha256', $password, true), 0, 32);
    $enc_iv = "av3DYGLkwBsErphc";


    $menu_screen            = $folder_name."/view";
    $file_name_update       = base64_encode(openssl_encrypt($menu_screen, $enc_method, $enc_password, OPENSSL_RAW_DATA, $enc_iv));

    $uni_id = base64_encode(openssl_encrypt($unique_id, $enc_method, $enc_password, OPENSSL_RAW_DATA, $enc_iv));
    $final_str = '<a href="index.php?file='.$file_name_update.'&unique_id='.$uni_id.'"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';

    
    return $final_str;

}

function btn_delete($folder_name = "",$unique_id = "") {
  
    $final_str = '<a href="#" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')">
    <i class="fa fa-trash text-danger text-danger"></i></a>';
    
    return $final_str;
}

function btn_delete_app($folder_name = "", $unique_id = "") {
    // Correcting the onclick event to properly call the JavaScript function
    $final_str = '<a href="#" onclick="complaint_category_delete('.$unique_id.')">
    <i class="fa fa-trash text-danger"></i></a>';
    
    return $final_str;
}


// function btn_delete_app($folder_name = "", $unique_id = "") {
//     $final_str = '<a href="#" onclick="delete(\''.$unique_id. '\')">
//     <i class="fa fa-trash text-danger"></i></a>';
    
//     return $final_str;
// }


function btn_delete_stage($folder_name = "",$unique_id = "",$screen_unique_id) {
    
    $final_str = '<a href="#" onclick="'.$folder_name.'_delete(\''.$unique_id.'\',\''.$screen_unique_id.'\')">
    <i class="fa fa-trash text-danger text-danger"></i></a>';
    
    return $final_str;
}

function btn_delete_demo_stage($folder_name = "",$unique_id = "",$screen_unique_id) {
    
    $final_str = '<a href="#" onclick="'.$folder_name.'_delete(\''.$unique_id.'\',\''.$screen_unique_id.'\')">
    <i class="fa fa-trash text-danger text-danger"></i></a>';
    
    return $final_str;
}

function btn_call_update($folder_name = "",$unique_id = "", $prefix = "",$suffix = "") {
    $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&is_phone=1&unique_id='.$unique_id.'"><i class="mdi mdi-phone-in-talk  mdi-24px waves-effect waves-light mt-n2 mb-n2 text-warning"></i></a>';
    // $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&is_phone=1&unique_id='.$unique_id.'"><img src="img/start.png" width="27" height="27" alt="Start Call"></a>';
    
    return $final_str;
}

function btn_call_start($customer_id = "", $followup_id = "") {
    $final_str = '<a href="javascript:void(0);" onclick="start_call(\''.$customer_id.'\',\''.$followup_id.'\')"><i class="mdi mdi-play-circle  mdi-24px waves-effect waves-light mt-n2 mb-n2 ml-2 text-primary"></i></a>';
    $final_str = '<a href="javascript:void(0);" onclick="start_call(\''.$customer_id.'\',\''.$followup_id.'\')"><img src="img/start.png" width="27" height="27" alt="Start Call" class="ml-2"></a>';
    
    return $final_str;
}

function btn_map($latitude = "",$longitude = "") {
    
    $def_latitude="13.0456605";
    $def_longitude="80.2086916";

    $final_str = '<a target="_blank" href="https://www.google.com/maps/dir/?api=1&origin='.$def_latitude. "," .$def_longitude.'&destination='.$latitude.','.$longitude.'"><i class="mdi mdi-map-marker mdi-24px waves-effect waves-light mt-n2 mb-n2 text-primary"></i></a>';
    
    return $final_str;
}

function btn_print($folder_name = "",$unique_id = "", $file_name = "",$prefix = "",$suffix = "") {
    $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print(event,\'folders/'.$folder_name.'/'.$file_name.'\',\''.$unique_id.'\');"><i class="mdi mdi-printer"></i></button></a>';

    
    
    return $final_str;
}

// function btn_print1($folder_name = "",$unique_id = "", $file_name = "",$prefix = "",$suffix = "") {
//     $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print(event,\'folders/'.$folder_name.'/'.$file_name.'\',\''.$unique_id.'\');"><i class="mdi mdi-printer"></i></button></a>';
//     return $final_str;
// }

function btn_print1($folder_name = "", $unique_id = "", $screen_unique_id = "", $file_name = "", $user_name = '',  $prefix = "", $suffix = "") {
    $final_str = '<button type="button" class="btn btn-asgreen btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print_2(event, \'folders/' . $folder_name . '/' . $file_name . '\', \'' . $unique_id . '\', \'' . $screen_unique_id . '\');"><i class="mdi mdi-printer"></i></button>';

    return $final_str;
    
}

function app_view($folder_name = "",$unique_id = "", $file_name = "",$prefix = "",$suffix = "") {
    $final_str = '<a href="http://zigma.in/g_app/view.php?unique_id='.$unique_id.'"<i class="mdi mdi-printer"></i></button></a>';

    
    
    return $final_str;
}

function btn_print_1($folder_name = "",$unique_id = "", $file_name = "",$dept_type='', $main_category='', $prefix = "",$suffix = "") {
    $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print_1(event,\'folders/'.$folder_name.'/'.$file_name.'\',\''.$unique_id.'\',\''.$dept_type.'\',\''.$main_category.'\');"><i class="mdi mdi-printer"></i></button></a>';

    
    
    return $final_str;
}

function btn_print2($folder_name = "",$unique_id = "", $file_name = "", $user_name='', $prefix = "",$suffix = "") {
    $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print(event,\'folders/'.$folder_name.'/'.$file_name.'\',\''.$unique_id.'\',\''.$user_name.'\');"><i class="mdi mdi-printer"></i></button></a>';

    
    
    return $final_str;
}


function btn_print_in_list($folder_name = "",$unique_id = "", $file_name = "",$prefix = "",$suffix = "",$content = '<button type="button" class="btn btn-info btn-rounded waves-effect waves-light float-right"><i class="mdi mdi-printer mdi-16px waves-effect waves-light text-white"></i></button>') {

    $final_str = '<a target="_blank" href="index.php?file='.$prefix.$folder_name.$suffix.'/'.$file_name.'&unique_id='.$unique_id.'">'.$content.'</a>';
    
    return $final_str;
}

function btn_print_in_form($folder_name = "",$unique_id = "", $file_name = "",$prefix = "",$suffix = "") {

    $final_str = '<a target="_blank" href="index.php?file='.$prefix.$folder_name.$suffix.'/'.$file_name.'&unique_id='.$unique_id.'"><button type="button" class="btn btn-info btn-rounded waves-effect waves-light float-right mr-2"><i class="mdi mdi-printer mdi-16px waves-effect waves-light text-white">'.$file_name.'</i></button></a>';
    
    return $final_str;
}

function btn_approval_print($folder_name = "",$unique_id = "",$sub_unique_id = "", $file_name = "",$prefix = "",$suffix = "") {
    $final_str = '<a target="_blank" href="index.php?file='.$prefix.$folder_name.$suffix.'/'.$file_name.'&unique_id='.$unique_id.'&sub_unique_id='.$sub_unique_id.'"><button type="button" class="btn btn-warning  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-lead-pencil"></i></button></a>';
    
    return $final_str;
}



function btn_edit($folder_name = "",$unique_id = "") {
    $final_str = '<button type="button" class="btn btn-asgreen btn-xs btn-rounded waves-effect waves-light " onclick="'.$folder_name.'_edit(\''.$unique_id.'\')"><i class="mdi mdi-square-edit-outline"></i></button>';

    $final_str = '<a href="#" onclick="'.$folder_name.'_edit(\''.$unique_id.'\')"><i class="mdi mdi-square-edit-outline  mdi-24px waves-effect waves-light mt-n2 mb-n2 text-success"></i></a>';

    return $final_str;
}



// Datatables Total Records Count Function
function total_records() {
    global $pdo;
    
    $total_records  = 0;
    $sql            = "SELECT FOUND_ROWS() as total";
    $result         = $pdo->query($sql);
    if($result->status){
        $total      = $result->data[0]["total"];
    }
    // print_r($result);    
    return $total;
}

// Convert Original folder Name to Display Name
function disname($name = "")
{
    if ($name) {
        $name = explode("_",$name);
        $name = array_map("ucfirst",$name);
        $name = implode(" ",$name);

        return $name;
    } else {
        return "Empty Title";
    }
}

// Continents Function
function continent($unique_id = "",$columns = "") {
    global $pdo;

    $where          = [];
    $table          = "continents";

    if (!$columns) {
        $table_columns = [
            $table,
            [
                "unique_id",
                "name"  
                ]
            ];
    } else {
        $table_columns = [
            $table,
            $columns
        ];
    }

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        // $where = " WHERE continent_id = '".$continent_id."' ";
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $continents = $pdo->select($table_columns, $where);

    // print_r($continents);

    if ($continents->status) {
        return $continents->data;
    } else {
        print_r($continents);
        return 0;
    }
}

function select_option($options = [], $description = "", $is_selected = [], $is_disabled = []) {
    
    $option_str = "<option value='' disabled>No Options to Select</option>";
    $data_attribute = "";

    if ($options) {
        $option_str = "<option value=''>Select</option>";

        if ($description) {
            $option_str = "<option value='' selected>" . $description . "</option>";
        }

        foreach ($options as $key => $value) {
            $value = array_values($value);
            $selected = "";
            $disabled = "";

            if (is_array($is_selected) && in_array($value[0], $is_selected)) {
                $selected = " selected='selected' ";
            } elseif ($is_selected == $value[0]) {
                $selected = " selected='selected' ";
            }

            if (is_array($is_disabled) && in_array($value[0], $is_disabled)) {
                $disabled = " disabled='disabled' ";
            } elseif ($is_disabled == $value[0]) {
                $disabled = " disabled='disabled' ";
            }

            if (strpos($value[1], "_")) {
                $value[1] = disname($value[1]);
            } else {
                $value[1] = ucfirst($value[1]);
            }

            if (isset($value[2])) {
                $data_attribute = " data-extra='" . $value[2] . "'";
            }

            $option_str .= "<option value='" . $value[0] . "'" . $data_attribute . $selected . $disabled . ">" . $value[1] . "</option>";
        }
    }

    return $option_str;
}


function select_option_create($options = [],$description = "", $is_selected = [],$is_disabled = []) {
    
    $option_str     = "<option value='' disabled>No Options to Select</option>";

    $data_attribute = "";

    if ($options) {
        $option_str = '';

        // $option_str     = "<option value=''>Select</option>";

        // if ($description) {
        //     $option_str     = "<option value='' selected>".$description."</option>";
        // }
        foreach ($options as $key => $value) {

            $value      = array_values($value);
            $selected   = "";
            $disabled   = "";

            if (is_array($is_selected) AND in_array($value[0],$is_selected)) {            
                $selected = " selected='selected' ";
            } elseif ($is_selected == $value[0]) {
                
                $selected = " selected='selected' ";
            }
            
            if (is_array($is_disabled) AND in_array($value[0],$is_disabled)) {            
                $disabled = " disabled='disabled' ";
            } elseif ($is_disabled == $value[0]) {
                $disabled = " disabled='disabled' ";
            }

            if (strpos($value[1],"_")) {
                $value[1] = disname($value[1]);
            } else {
                $value[1] = ucfirst($value[1]);
            }

            if (isset($value[2])) {
                $data_attribute = "data-extra='".$value[2]."'";
            } 

            $option_str .= "<option value='".$value[0]."'".$data_attribute.$selected.$disabled.">".$value[1]."</option>";
        }
    }
    
    return $option_str;
}

function active_status($is_active_val = 1) {
    $option_str    = "";
    $is_active     = "";
    $is_inactive   = "";

    if ($is_active_val == 1) {
        $is_active     = " selected = 'selected' ";
    } else {
        $is_inactive   = " selected = 'selected' ";
    }
     
    $option_str     =  "<option value='1'".$is_active.">Active</option>";
    $option_str     .=  "<option value='0'".$is_inactive.">In Active</option>";

    return $option_str;
}

// Active and In Active Show in Data Table
function is_active_show($is_active = 0) {
    $act_str = "<span style='color: red'>In Active</span>";

    if ($is_active) {
        $act_str = "<span style='color: green'>Active</span>";
    }

    return $act_str;
}

function active_status_show($is_active = 0) {
$act_str = "In Active";
if ($is_active){

        $act_str = "Active";
    }

    return $act_str;
}




// Screen Type Function
function screen_type($unique_id = "") {
    global $pdo;

    $table_name    = "user_screen_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "type_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $screen_types = $pdo->select($table_details, $where);

    if ($screen_types->status) {
        return $screen_types->data;
    } else {
        print_r($screen_types);
        return 0;
    }
}


// Main Screen Function
function main_screen($unique_id = "") {
    global $pdo;

    $table_name    = "user_screen_main";
    $where         = [];
    $table_columns = [
        "unique_id",
        "screen_main_name",
        "icon_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $order_by  = 'order_no ASC';

    $main_screens = $pdo->select($table_details, $where,'','',$order_by);

    if ($main_screens->status) {
        return $main_screens->data;
    } else {
        print_r($main_screens);
        return 0;
    }
}

// Main Screen Function
function section_name($unique_id ="",$main_screen_id = "") {
    global $pdo;

    $table_name    = "user_screen_sections";
    $where         = [];
    $table_columns = [
        "unique_id",
        "section_name",
        "icon_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    $order_by = [
        "order_no"
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }
    if ($main_screen_id) {

        $where["screen_main_unique_id"] = $main_screen_id;
    }

    $section_names = $pdo->select($table_details, $where, "", "", $order_by);

    if ($section_names->status) {
        return $section_names->data;
    } else {
        print_r($section_names);
        return 0;
    }
}

// Main Screen Function
function user_screen($unique_id = "",$screen_section_id = "") {
    global $pdo;

    $table_name    = "user_screen";
    $where         = [];
    $table_columns = [
        "unique_id",
        "screen_name",
        "folder_name",
        "actions"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    $order_by = [
        "order_no"
    ];

    if ($screen_section_id) {

        $where["screen_section_unique_id"] = $screen_section_id;
    }  
     if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $user_screens = $pdo->select($table_details, $where, "", "", $order_by);

    if ($user_screens->status) {
        return $user_screens->data;
    } else {
        print_r($user_screens);
        return 0;
    }
}

// User Screen Actions Function
function user_actions($unique_id = "") {
    global $pdo;

    $table_name    = "user_screen_actions";
    $where         = [];
    $table_columns = [
        "unique_id",
        "action_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $user_actions = $pdo->select($table_details, $where);

    if ($user_actions->status) {
        return $user_actions->data;
    } else {
        print_r($user_actions);
        return 0;
    }
}

// Main Screen Function
function user_type($unique_id = "") {

    global $pdo;

    $table_name    = "user_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "user_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $user_types = $pdo->select($table_details, $where);

    if ($user_types->status) {
        return $user_types->data;
    } else {
        print_r($user_types);
        return 0;
    }
}

function plant_type($unique_id = "") {

    global $pdo;

    $table_name    = "plant_creation";
    $where         = [];
    $table_columns = [
        // "id",
        "id",
        "plant_name",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $group_by     =  "site_id";
    // $order_by = "id ASC";
    


    if ($unique_id) {

        $where              = [];
        $where["site_id"] = $unique_id;
    }

    $department_type = $pdo->select($table_details,$where);
    //  print_r($department_type);
    if ($department_type->status) {
        return $department_type->data;
    } else {
        print_r($department_type);
        return 0;
    }
}




// BID Type Function
function staff_name ($unique_id = "") {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
        "office_contact_no",
        "designation_unique_id",
        "file_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $staff_name_list = $pdo->select($table_details, $where);

    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}

// BID Type Function
function staff_ceo_name ($unique_id = "") {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
        "office_contact_no",
        "designation_unique_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active"             => 1,
        "designation_unique_id" => "5ff5d3423713a59778",
        "is_delete"             => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $staff_ceo_name_list = $pdo->select($table_details, $where);

    if ($staff_ceo_name_list->status) {
        return $staff_ceo_name_list->data;
    } else {
        print_r($staff_ceo_name_list);
        return 0;
    }
}




// BID Type Function
function staff_director_name ($unique_id = "") {
    global $pdo;

    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
        "office_contact_no",
        "designation_unique_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active"             => 1,
        "designation_unique_id" => "5ff5d3585747568896",
        "is_delete"             => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $staff_director_name_list = $pdo->select($table_details, $where);

    if ($staff_director_name_list->status) {
        return $staff_director_name_list->data;
    } else {
        print_r($staff_director_name_list);
        return 0;
    }
}




function btn_approve($folder_name = "",$unique_id = "",$approved_status = "") {
    if($approved_status) {
        $final_str = '<i class="mdi mdi-check btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light"></i>';
    } else {
        $final_str = '<i class=" mdi mdi-24px mdi-alert-circle-outline" style = "color :#e6f22b;"></i>';
    }
    
    return $final_str;
}

function btn_approve_status($folder_name = "",$unique_id = "",$approved_status = "") {
    if($approved_status=='0') {
        $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    } else {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }
    
    return $final_str;
}

function btn_bid_approve_status($folder_name = "",$unique_id = "",$approved_status = "",$approval_stage = "") {

    $final_str = "";

    if($approved_status=='0') {
        $final_str = '<button type="button" onclick="status_show(\'pending\',\''.$unique_id.'\',\''.$approval_stage.'\')" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button></a>';
    } else if($approved_status=='1') {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }
    else if($approved_status=='2') {
        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    }
    else if($approved_status=='3') {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }
    
    return $final_str;
}

function btn_expense_approve_status($unique_id = "",$approved_status = "",$approval_type = "") {

    $final_str = "";

    if($approved_status=='0') {
        $final_str = '<button type="button" onclick="expense_status_show(\'pending\',\''.$unique_id.'\',\''.$approval_type.'\')" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button></a>';
    } else if($approved_status=='1') {
        $final_str = '<button type="button" onclick="expense_status_show(\'approve\',\''.$unique_id.'\',\'\')" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }
    else if($approved_status=='2') {
        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    }
    else if($approved_status=='3') {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }
    
    return $final_str;
}

function btn_approval ($folder_name = "",$unique_id = "",$approved_status = "") {
    
    if (($approved_status=='Cancel') || ($approved_status===2)) {

        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';

    } else if (($approved_status=='Approved') || ($approved_status===1)) {

        $final_str = '<button type="button"  class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';

    } else {
        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation"></i></button></a>';
    }
    
    return $final_str;
}

// BID Type Function
function blood_group ($unique_id = "") {
    global $pdo;

    $table_name    = "blood_group";
    $where         = [];
    $table_columns = [
        "unique_id",
        "blood_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $blood_name_list = $pdo->select($table_details, $where);

    if ($blood_name_list->status) {
        return $blood_name_list->data;
    } else {
        print_r($blood_name_list);
        return 0;
    }
}

// Money Format India
function moneyFormatIndia($num) {
    $explrestunits = "";
    $amount     = explode('.', $num);
    $num        = $amount[0];
    $decimal    = 0;
    if (count($amount)==2) {
        $decimal = $amount[1];
    }
    if(strlen($num)>3) {
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if($i==0) {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }

    $decimal = number_format($decimal, 2, '.', '');
    $decimal = explode(".",$decimal)[1];

    return $thecash.".".$decimal; // writes the final format where $currency is the currency symbol.
}


// GST Name
function tax ($unique_id = "") {
    global $pdo;

    $table_name    = "tax";
    $where         = [];
    $table_columns = [
        "unique_id",
        "tax_name",
        "tax_value"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $where              = [];
        $where["unique_id"] = $unique_id;

    }

    $tax_id = $pdo->select($table_details, $where);

    if ($tax_id->status) {
        return $tax_id->data;
    } else {
        print_r($tax_id);
        return 0;
    }
}

// GST Name
function account_year ($unique_id = "") {
    global $pdo;

    $table_name    = "account_year";
    $where         = [];
    $table_columns = [
        "unique_id",
        "account_year"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        // "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $where              = [];
        $where["unique_id"] = $unique_id;

    }

    $account_year_id = $pdo->select($table_details, $where);

    if ($account_year_id->status) {
        return $account_year_id->data;
    } else {
        print_r($account_year_id);
        return 0;
    }
}

// competitor Function
function competitors ($unique_id = "") {
    global $pdo;

    $table_name    = "competitor_profile";
    $where         = [];
    $table_columns = [
        "unique_id",
        "competitor_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $competitor_list = $pdo->select($table_details, $where);

    if ($competitor_list->status) {
        return $competitor_list->data;
    } else {
        print_r($competitor_list);
        return 0;
    }
}

function file_upload_extention_lowercase_helper () {
    // In case file Extention was uppercase letters convert to lowercase
    foreach ($_FILES as $f_key => $f_value) {

        if (is_array($f_value['name'])) {
            
            // Multi File Upload
            foreach ($f_value['name'] as $fn_key => $fn_value) {
                $_FILES[$f_key]['name'][$fn_key] = strtolower($fn_value);
            }
        } else {
            // Single file Upload
            $_FILES[$f_key]['name'] = strtolower($f_value['name']);
        }
    }
}



// Supplier Function
function supplier($unique_id = "") {
    global $pdo;

    $table_name    = "supplier_profile";
    $where         = [];
    $table_columns = [
        "unique_id",
        "supplier_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        // $where = " WHERE unique_id = '".$unique_id."' ";
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $supplier = $pdo->select($table_details, $where);

    // print_r($countries);

    if ($supplier->status) {
        return $supplier->data;
    } else {
        print_r($supplier);
        return 0;
    }
}

// Supplier Function
function branch($unique_id = "") {
    global $pdo;

    $table_name    = "company_and_branch_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "branch_name"
    ];  

    $where     = [
        "is_active"   => 1,
        "is_delete"   => 0
    ];

    if ($unique_id) {
        // $where = " WHERE unique_id = '".$unique_id."' ";
        $table_columns[]="address";
        $table_columns[]="country";
        $table_columns[]="state";
        $table_columns[]="city";
        $table_columns[]="pin_code";
        $table_columns[]="phone_number";
        $table_columns[]="mobile_number";
        $table_columns[]="gst_number";
        $table_columns[]="email_id";
        $table_columns[]="website";
        $table_columns[]="radius";
        $table_columns[]="latitude";
        $table_columns[]="longitude";
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $table_details = [
        $table_name,
        $table_columns
    ];
    
    $branch = $pdo->select($table_details, $where);

    // print_r($countries);

    if ($branch->status) {
        return $branch->data;
    } else {
        print_r($branch);
        return 0;
    }
}


function sublist_insert_update($table_name = "", $data = "",$prefix = "") {
    global $pdo;
    if ($table_name) {
        foreach ($data as $data_key => $columns) {

            $unique_id = $columns['unique_id'];

            if($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_name,$columns,$update_where);

            // Update Ends
            } else {
                $columns['unique_id'] = $prefix.unique_id();
                // Insert Begins            
                $action_obj     = $pdo->insert($table_name,$columns);
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

                print_r($action_obj);
                break;
            }
        }
    } else {
        echo "table name not given";
    }
}

function sublist_delete($table_name = "", $sub_unique_ids = "",$main_unique_id = []) {

    global $pdo;

    if ($table_name) {

        if (($sub_unique_ids) && (!empty($main_unique_id))) {

            $column_name     = array_keys($main_unique_id)[0];
            $column_value    = $main_unique_id[$column_name];
            
            $where           = " unique_id NOT IN (".$sub_unique_ids.") AND ".$column_name."  = '".$column_value."'";
            
            $columns         = [
                "is_delete" => 1
            ];
            
            $update_result   = $pdo->update($table_name,$columns,$where);

            if ($update_result->status) {
                
            } else {
                print_r($update_result);
            }
        } else {
            echo "Sub List Delete Status Update Error";
        }
        
    }
}



// Purchase Order Delivery Type
function delivery_type ($unique_id = "") {
    global $pdo;

    $table_name    = "purchase_order_delivery_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "delivery_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;

    }

    $delivery_type_id = $pdo->select($table_details, $where);

    if ($delivery_type_id->status) {
        return $delivery_type_id->data;
    } else {
        print_r($delivery_type_id);
        return 0;
    }
}



// Purchase Order Delivery Via Type
function delivery_via_type ($unique_id = "") {
    global $pdo;

    $table_name    = "delivery_via_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "delivery_via_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;

    }

    $delivery_via_type_id = $pdo->select($table_details, $where);

    if ($delivery_via_type_id->status) {
        return $delivery_via_type_id->data;
    } else {
        print_r($delivery_via_type_id);
        return 0;
    }
}


// function user_name_like ($search_key = "") {


//     $result     = "''";

//     if ($search_key) {
//         global $pdo;

//         $table_name = "user";

//         $columns        = [
//             "unique_id"
//         ];

//         $where          = " staff_name LIKE '".mysql_like($search_key)."' ";

//         $table_details  = [
//             $table_name,
//             $columns
//         ];

//         // $group_by     = " quotation_unique_id ";
//         // $group_by     = " ";

//         $select_result  = $pdo->select($table_details,$where,"","","","","");
//         // print_r($select_result);

//         if (!($select_result->status)) {
//             print_r($select_result);
//         } else {
//             $result     = $select_result->data[0];
//             $result     = $result['unique_id'];

//             if ($result == "") {
//                 $result = "''";
//             }
//         }
//     }

//     return $result;
// }

function user_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "user";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " staff_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}


function user_type_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "user_type";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " user_type LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}



function complaint_category_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "category_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " category_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}



function user_name($unique_id = ""){
    
    global $pdo;

        $table_name    = "user";
        $where         = [];
        $table_columns = [
            "unique_id",
            "staff_name",
            "user_name",
            "user_type_unique_id",
            "mobile_no",
            "email_id",
            "designation_id",
            "staff_id"
        ];
    
        $table_details = [
            $table_name,
            $table_columns
        ];
    
        $where     = [
            "is_active" => 1,
            "is_delete" => 0
        ];
    
        if ($unique_id) {

            $where              = [];
    
            $where["unique_id"] = $unique_id;
    
        }
    
        $user_name_id = $pdo->select($table_details, $where);
        // print_r($user_name_id);
    
        if ($user_name_id->status) {
            return $user_name_id->data;
        } else {
            print_r($user_name_id);
            return 0;
        }
   
}
// under user Function
function team_user ($user_id = "") {
    global $pdo;

    $table_name    = "user";
    $where         = "";
    $table_columns = [
        "unique_id",
        "user_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND is_active = 1   AND is_team_head = 0 AND user_name != '".$user_id ."'";

   /* if ($unique_id) {

        $where["unique_id"] = $unique_id;
    }*/

    $user_name_list = $pdo->select($table_details, $where);

    if ($user_name_list->status) {
        return $user_name_list->data;
    } else {
        print_r($user_name_list);
        return 0;
    }
}

function select_option_user($options = [],$description = "", $is_selected = [],$is_disabled = []) {
    
    $option_str     = "<option value='' disabled>No Options to Select</option>";

    $data_attribute = "";

    if ($options) {

        $option_str     = "<option value=''>Select</option>";

        if ($description) {
            $option_str     = "<option value='' selected>".$description."</option>";
        }
        foreach ($options as $key => $value) {

            $value      = array_values($value);
            $selected   = "";
            $disabled   = "";

            if (is_array($is_selected) AND in_array($value[0],$is_selected)) {            
                $selected = " selected='selected' ";
            } elseif ($is_selected == $value[0]) {
                
                $selected = " selected='selected' ";
            }
            
            if (is_array($is_disabled) AND in_array($value[0],$is_disabled)) {            
                $disabled = " disabled='disabled' ";
            } elseif ($is_disabled == $value[0]) {
                $disabled = " disabled='disabled' ";
            }

            if (strpos($value[1],"_")) {
                $value[1] = disname($value[1]);
            } else {
                $value[1] = ucfirst($value[1]);
            }

            if (isset($value[2])) {
                $data_attribute = "data-extra='".$value[2]."'";
            } 


            $option_str .= "<option value='".$value[0]."'".$data_attribute.$selected.$disabled.">".$value[1]."-".$value[7]."</option>";
        }
    }
    
    return $option_str;
}

// under user Function
function under_user ($user_id = "") {
    global $pdo;

    $table_name    = "user";
    $where         = "";
    $table_columns = [
        "unique_id",
        "user_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND is_active = 1  AND user_name != '".$user_id ."'";

   /* if ($unique_id) {

        $where["unique_id"] = $unique_id;
    }*/

    $user_name_list = $pdo->select($table_details, $where);

    if ($user_name_list->status) {
        return $user_name_list->data;
    } else {
        print_r($user_name_list);
        return 0;
    }
}

// under user type Function
function under_user_type ($user_type = "") {
    global $pdo;

    $table_name    = "user_type";
    $where         = "";
    $table_columns = [
        "unique_id",
        "user_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND is_active = 1 AND user_type != '".$user_type ."'";

   /* if ($unique_id) {

        $where["unique_id"] = $unique_id;
    }*/

    $user_type_list = $pdo->select($table_details, $where);

    if ($user_type_list->status) {
        return $user_type_list->data;
    } else {
        print_r($user_type_list);
        return 0;
    }
}

// Show Entries Based On User Hierarcy
function user_hierarchy ($user_id = "", $user_type_id = "",$team_users = false) {

    global $pdo;

    $table_user         = "user";
    $table_user_type    = "user_type";

    $return_result = [
        "under_user"        => "",
        "under_user_type"   => ""
    ];

    if ($user_id) {
        $user_where = [
            "unique_id" => $user_id
        ];

        $user_columns = [
            "under_user"
        ];

        if ($team_users) {
            $user_columns = [
                "team_members AS under_user"
            ];
        }

        $user_table_details = [
            $table_user,
            $user_columns
        ];

        $user_select = $pdo->select($user_table_details, $user_where);

        if ($user_select->status) {
            $user_data = $user_select->data[0];

            if ($user_data['under_user']) {
                $user_data['under_user'] = '"'.implode('","',explode(",",$user_data['under_user'])).'",';
            }

            $user_data['under_user'] .= '"'.$user_id.'"';

            $return_result['under_user'] = $user_data['under_user'];
        } else {
            print_r($user_select);
            exit;
        }
    }

    if ($user_type_id) {
        $user_where = [
            "unique_id" => $user_type_id
        ];

        $user_columns = [
            "under_user_type"
        ];

        $user_table_details = [
            $table_user_type,
            $user_columns
        ];

        $user_select = $pdo->select($user_table_details, $user_where);

        if ($user_select->status) {
            $user_data = $user_select->data[0];

            if ($user_data['under_user_type']) {
                $user_data['under_user_type'] = '"'.implode('","',explode(",",$user_data['under_user_type'])).'",';
            }

            $user_data['under_user_type'] .= '"'.$user_type_id.'"';

            $return_result['under_user_type'] = $user_data['under_user_type'];
        } else {
            print_r($user_select);
            exit;
        }
    }

    return $return_result;
}

//datatable search
function mysql_like ($search_query = "", $search_term = "") {

    $return_result = "";

    if ($search_query) {
        switch ($search_term) {
            case "first":
                $return_result = "%".$search_query;
                break;
            
            case "last":
                $return_result = $search_query."%";
                break;
            
            default:
                // For All result
                $return_result = "%".$search_query."%";
                break;
        }
    }

    return $return_result;
}
//expense type function
function expense_type ($unique_id = "") {
    global $pdo;

    $table_name    = "expense_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "expense_type"
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $expense_type_list = $pdo->select($table_details, $where);

    if ($expense_type_list->status) {
        return $expense_type_list->data;
    } else {
        print_r($expense_type_list);
        return 0;
    }
}

function item_unit_decimal ($item_id = 0) {

    $decimal  = 0;

    if ($item_id) {
        global $pdo;
        $table_name = "item_names_code";

        $columns    = [
            "unit_unique_id"
        ];

        $where      = [
            "unique_id" => $item_id
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        $select_result  = $pdo->select($table_details,$where);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $item_unit = $select_result->data[0];
            
            if ($item_unit) {

                $item_unit          = $item_unit['unit_unique_id'];

                // Get Unit Decimal
                $table_unit         = "units";

                $columns_unit       = [
                    "decimal_points"
                ];
        
                $where_unit         = [
                    "unique_id" => $item_unit
                ];
        
                $table_unit_details = [
                    $table_unit,
                    $columns_unit
                ];

                $decimal_select     = $pdo->select($table_unit_details,$where_unit);

                if (!($decimal_select->status)) {
                    print_r($decimal_select);
                } else {
                    $decimal = $decimal_select->data[0];

                    $decimal = $decimal['decimal_points'];

                    if (!($decimal)) {
                        $decimal    = 0;
                    }
                }
            }
        }
    }

    return $decimal;
}

function item_unit ($item_id = 0) {

    $unit_name  = "";

    if ($item_id) {
        global $pdo;
        $table_name = "item_names_code";

        $columns    = [
            "unit_unique_id"
        ];

        $where      = [
            "unique_id" => $item_id
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        $select_result  = $pdo->select($table_details,$where);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $item_unit = $select_result->data[0];
            
            if ($item_unit) {

                $item_unit          = $item_unit['unit_unique_id'];

                // Get Unit Decimal
                $table_unit         = "units";

                $columns_unit       = [
                    "unit_name"
                ];
        
                $where_unit         = [
                    "unique_id" => $item_unit
                ];
        
                $table_unit_details = [
                    $table_unit,
                    $columns_unit
                ];

                $unit_name_select     = $pdo->select($table_unit_details,$where_unit);

                if (!($unit_name_select->status)) {
                    print_r($unit_name_select);
                } else {
                    $unit_name = $unit_name_select->data[0];

                    $unit_name = $unit_name['unit_name'];

                    if (!($unit_name)) {
                        $unit_name    = "";
                    }
                }
            }
        }
    }
    
    return $unit_name;
}

function item_tax1 ($item_id = 0) {

    $item_tax_value  = "";

    if ($item_id) {
        global $pdo;
        $table_name = "item_names_code";

        $columns    = [
            "tax_unique_id"
        ];

        $where      = [
            "unique_id" => $item_id
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        $select_result  = $pdo->select($table_details,$where);

        print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            // print_r($select_result);

            $item_tax = $select_result->data[0];
            
            if ($item_tax) {

                $item_tax          = $item_tax['tax_unique_id'];

                // Get Unit Decimal
                $table_tax         = "tax";

                $columns_unit      = [
                    "tax_value"
                ];
        
                $where_unit         = [
                    "unique_id" => $item_tax
                ];
        
                $table_tax_details  = [
                    $table_tax,
                    $columns_unit
                ];

                $item_tax_value_select   = $pdo->select($table_tax_details,$where_unit);

                print_r($item_tax_value_select);

                if (!($item_tax_value_select->status)) {
                    print_r($item_tax_value_select);
                } else {
                    // print_r($item_tax_value_select);

                    if (!empty($item_tax_value_select->data[0])) {
                        $item_tax_value = $item_tax_value_select->data[0];

                        $item_tax_value = $item_tax_value['tax_value'];

                        if (!($item_tax_value)) {
                            $item_tax_value    = 0;
                        }
                    } else {
                        $item_tax_value    = 0;
                    }
                }
            }
        }
    }
    
    return $item_tax_value;
}

function item_tax($item_id = "") {

    $item_tax_value  = 0;

    return 0;

    if ($item_id) {

        global $pdo;

        $table_name = "item_names_code";

        echo $item_id;

        $columns    = [
            "(SELECT t.tax_value FROM tax t WHERE t.unique_id = $table_name.tax_unique_id) AS tax_unique_id"
        ];

        $where      = [
            "unique_id" => $item_id
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        $select_result  = $pdo->select($table_details,$where);

        print_r($select_result);
    }
}

function unit_decimal_cal ($number_val = 0,$decimal = 0) {

    $return_result  = 0;

    if ($number_val) {
        $return_result = number_format($number_val,$decimal,".","");
    }

    return $return_result;
}

function getIndianCurrency(float $number) {
    
    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety');
    $digits = array('', 'Hundred','Thousand','Lakh', 'Crore');
    while( $i < $digits_length ) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str [] = ($number < 21) ? $words[$number].' '. $digits[$counter]. $plural.' '.$hundred:$words[floor($number / 10) * 10].' '.$words[$number % 10]. ' '.$digits[$counter].$plural.' '.$hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    // return strtoupper(($Rupees ? $Rupees . 'Rupees ' : '') . $paise. " only ");
    return (($Rupees ? $Rupees . 'Rupees ' : '') ." only ");
}

//rate contract no
function rate_contract_no ($unique_id = "") {
    global $pdo;

    $table_name    = "rate_contracts_main";
    $where         = [];
    $table_columns = [
        "unique_id",
        "tender_code"
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $rate_contract_list = $pdo->select($table_details, $where);

    if ($rate_contract_list->status) {
        return $rate_contract_list->data;
    } else {
        print_r($rate_contract_list);
        return 0;
    }
}

// Product brand
function item_brand ($unique_id = "") {
    global $pdo;

    $table_name    = "item_brands";
    $where         = [];
    $table_columns = [
        "unique_id",
        "brand_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;

    }

    $item_brands = $pdo->select($table_details, $where);

    if ($item_brands->status) {
        return $item_brands->data;
    } else {
        print_r($item_brands);
        return 0;
    }
}

function technical_bid_submission($unique_id = '') {

    if ($unique_id) {

        global $pdo;
        global $today;

        $call_no                = $_POST['call_no'];
        $call_unique_id         = $_POST['call_unique_id'];
        $bid_no                 = $_POST['bid_no'];
        $bid_unique_id          = $_POST['bid_unique_id'];
        $bid_date               = $_POST['bid_date'];

        $table_name             = "tender_submission";
        $table_bid_product      = "bid_management_product_details";
        $table_tender_product   = "tender_submission_product";

        $where                  = " acc_year = '".$_SESSION["acc_year"]."'";

        $tender_no               = bill_no ($table_name,$where,$prefix = "TEN-", 1,1,0);

        $customer_id            = $_POST['customer_id'];
        // $tender_type            = $_POST['bids_tender_type'];

        $columns = [
            "bid_no"             => $bid_no,
            "entry_date"         => $today,
            "call_unique_id"     => $call_unique_id,
            "call_no"            => $call_no,
            "bid_no"             => $bid_no,
            "bid_date"           => $bid_date,
            "bid_unique_id"      => $bid_unique_id,
            // "bids_tender_type"   => $tender_type,
            "customer_id"        => $customer_id,
            "unique_id"          => $tender_unique_id = unique_id("tender")
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        // Insert Data in Table
        $action_obj     = $pdo->insert($table_name,$columns);

        if (!($action_obj->status)) {
            print_r($action_obj);
            exit;
        } else {
            $select_columns = [
                "bids_type_id",
                "sales_or_service",
                "item_category_id",
                "item_group_id",
                "item_id",
                "quantity",
                "rate",
                "tax_id",
                "tax_value",
                "total",
                "bid_releasing_month",
                "purchase_replicate",
                "remark"
            ];

            $select_table_details = [
                $table_bid_product,
                $select_columns
            ];

            $select_where = [
                "bid_unique_id" => $_POST['bid_unique_id'],
                "is_delete"     => 0
            ];

            $select_obj = $pdo->select($select_table_details,$select_where);

            if (!($select_obj->status)) {

                print_r($select_obj);
                exit;

            } else {

                $result_val = $select_obj->data;

                foreach ($result_val as $key => $value) {

                    $value['unique_id']        = unique_id("tender");
                    $value['bid_no']           = $bid_no;
                    $value['bid_unique_id']    = $bid_unique_id;
                    $value['tender_submission_unique_id'] = $tender_unique_id;

                    $insert_obj = $pdo->insert($table_tender_product,$value);

                    if (!($insert_obj->status)) {
                        print_r($insert_obj);
                        exit;
                    }
                }               
            }
        }
    }
    return true;
}

// function file_upload_s ($upload_path = "", $unique_id = "", $table_name = "", $columns = [],$save_original_name = false) {

//     // You Can Add or Remove Which Extension You Want in Below line
//     $valid_exptentions  = ['jpeg', 'jpg', 'png', 'gif', 'bmp' , 'pdf'];

//     // Upload Directory
//     $upload_folder      = $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR."ascent".DIRECTORY_SEPARATOR."uploads";
//     // $upload_folder      = "../uploads";

//     // Add If any Other Directory
//     if ($upload_path) {
//         $upload_folder .= DIRECTORY_SEPARATOR.$upload_path;
//     }

//     // Create Directory if Not Exist
//     if (!file_exists($upload_folder)) {
//         mkdir($upload_folder, 0777, true);
//     }

//     $upload_folder .= "/";

//     // When Update Use This Array
//     $update_columns = [];

//     foreach ($_FILES as $file_key => $file_value) {

        
//         $file_name      = $_FILES[$file_key]['name'];
        
//         if ($file_name && $_FILES[$file_key]['error'] == 0) {

//             // Prepare File Delete columns
//             if ($unique_id) {
//                 $update_columns[] = $file_key;
//             }
            
//             // get uploaded file's extension
//             $file_extention = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));            
            
//             if (in_array($file_extention,$valid_exptentions)) {                
                
//                 // Create Unique File Name
//                 $file_unique_name = md5(uniqid().rand(10000,99999)).".".$file_extention;
                
//                 $final_file   =  $upload_folder.$file_unique_name;
                
//                 // Get File Temp Name
//                 $file_name_temp   = $_FILES[$file_key]['tmp_name'];
                
//                 if (move_uploaded_file($file_name_temp,$final_file)) {
                    
//                     // Add Created File Name in Array
//                     $columns[$file_key] = $file_unique_name;
                    
//                     if ($save_original_name) {
//                         // Add File Original Name in Array
//                         $columns[$file_key."_org"] = $file_name;
//                     }
                    
//                 } else {
//                     echo 'File Upload Error';
//                     exit;
//                 }
//             }
//         }
//     }
    
//     // Delete Previous Files Before Update the New Files
//     if ($unique_id && !(empty($update_columns))) {

//         global $pdo;

//         $table_details  = [
//             $table_name,
//             $update_columns
//         ];
//         $where          = [
//             "unique_id"     => $unique_id
//         ];
        
//         $result         = $pdo->select($table_details,$where);

//         if ($result->status) {
//             if ($result->data) {
//                 $result_columns = $result->data[0];

//                 foreach ($result_columns as $res_key => $res_value) {

//                     if (!unlink($upload_folder.$res_value)) {
//                         echo 'File Delete Error in Foreach';
//                     }
                    
//                 }
//             }
            
//         } else {
//             echo 'File Delete Error';
//             print_r($result);
//         }
//     }

//     return $columns;
// }
function team_heads($head_id = "")
{
    global $pdo;

    $team_heads_sql = "SELECT unique_id,staff_unique_id AS staff_id,team_members FROM user WHERE is_delete = 0 AND is_team_head = 1";

    $team_head_columns = [
        "unique_id",
        "staff_unique_id",
        "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
        "(SELECT file_name FROM staff WHERE unique_id = user.staff_unique_id) AS user_image",
        "team_members",
        "team_id",
        "profile_image"
    ];

    $team_head_details = [
        "user", // Table Name 
        $team_head_columns
    ];

    $team_head_where   = [
        "is_delete" => 0,
        "is_team_head" => 1
    ];

    if ($head_id) {
        $team_head_where['unique_id'] = $head_id;
    }

    $team_head_result = $pdo->select($team_head_details,$team_head_where);

    if ($team_head_result->status) {
        return $team_head_result->data;
    } else {
        print_r($team_head_result);
    }
    return [];
}

function team_members($team_members)
{
    global $pdo;
    if ($team_members) {
        $team_members_columns = [
            "unique_id",
            "staff_unique_id",
            "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
            "(SELECT file_name FROM staff WHERE unique_id = user.staff_unique_id) AS user_image",
            "profile_image"

        ];
    
        $team_members_details = [
            "user", // Table Name 
            $team_members_columns
        ];

        // Exploding the String
        $team_members = explode(",",$team_members);
        $team_members = "'".implode("','",$team_members)."'";
    
        $team_members_where  = "is_delete = 0 AND unique_id IN (".$team_members.")";

        $order_by = " is_team_head DESC";
    
        $team_members_result = $pdo->select($team_members_details,$team_members_where,'','',$order_by);
    
        if ($team_members_result->status) {
            return $team_members_result->data;
        } else {
            print_r($team_members_result);
        }
    }

    return [];
}

function week_range ($week, $year)
{

    $dates          = [];
    $time           = strtotime("1 January $year", time());
    $day            = date('w', $time);
    $time           += ((7*$week)+1-$day)*24*3600;
    $dates["from"]  = date('Y-n-j', $time);
    $time           += 6*24*3600;
    $dates["to"]    = date('Y-n-j', $time);

    return $dates;
}

function datatable_sorting($column = 0, $direction = "ASC", $columns_array = []) {
    //print_r($column);
    $order_by   = "";
    if (!empty($columns_array)) {
        if ($column) {

            $is_found  = strripos($columns_array[$column]," AS ");
            
            if ($is_found) {
                $as_column = substr($columns_array[$column],$is_found+3);
            } else {
                $as_column = false;
            }

            if ($as_column) {
                $order_by       = $as_column." ".$direction;
            } else {
                $order_by       = $columns_array[$column]." ".$direction;
            }
        }
    }
    return $order_by;
}

function datatable_searching($search_query = '',$columns_array = []) {
    $search_string = "";

    if ($search_query) {
        if (!empty($columns_array)) {
            // Remove AS in Subquery in $columns_array
            $temp_arr   = [];
            foreach ($columns_array as $col_key => $col_value) {
                
                $is_found  = strripos($col_value," AS ");
            
                if ($is_found) {
                    $as_column = substr($col_value,0,$is_found);
                } else {
                    $as_column = $col_value;
                }
                $temp_arr[] = $as_column." LIKE '%".$search_query."%' ";
            }
            
            unset($temp_arr[count($temp_arr)-1]); // Unique ID Endry Disable
            unset($temp_arr[0]); // S.No Search Disable
            $search_string = implode(" OR ",$temp_arr);
        }
    }
    return $search_string;
}

//Staff Employee ID
function staff_id ($unique_id = "") {
    global $pdo;
    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "unique_id",
        "employee_id",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $staff_id_list = $pdo->select($table_details, $where);
    if ($staff_id_list->status) {
        return $staff_id_list->data;
    } else {
        print_r($staff_id_list);
        return 0;
    }
}

// Attendance Setting
function attendance_setting($unique_id = "") {
    global $pdo;

    $table_name    = "attendance_setting";
    $where         = [];
    $table_columns = [
        "unique_id",
        "attendance_shift_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;

    }

    $attendance_settings = $pdo->select($table_details, $where);

    if ($attendance_settings->status) {
        return $attendance_settings->data;
    } else {
        print_r($attendance_settings);
        return 0;
    }
}

// Dashboard Menu Function
function dashboard_menu($unique_id = "") {
    
    global $pdo;

    $table_name    = "dashboard_menu";
    $where         = [];
    $table_columns = [
        "unique_id",
        "menu_name",
        "file_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $order_by        = " order_no ";

    $dashboard_menus = $pdo->select($table_details, $where,'','',$order_by);

    if ($dashboard_menus->status) {
        return $dashboard_menus->data;
    } else {
        print_r($dashboard_menus);
        return 0;
    }
}

//new 

function priority_type($unique_id = "") {

    global $pdo;

    $table_name    = "priority_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "priority_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where = "is_delete = 0 and is_active = 1 and  priority_name != ''";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $priority_name = $pdo->select($table_details, $where);
    if ($priority_name->status) {
        return $priority_name->data;
    } else {
        print_r($priority_name);
        return 0;
    }
}


function problem_type($unique_id = "") {

    global $pdo;

    $table_name    = "problem_type_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "problem_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where = "is_delete = 0 and  problem_type != ''";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $problem_type = $pdo->select($table_details, $where);
    if ($problem_type->status) {
        return $problem_type->data;
    } else {
        print_r($problem_type);
        return 0;
    }
}


function shift_name($unique_id = "") {

    global $pdo;

    $table_name    = "shift_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "shift_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where = "is_delete = 0 and  shift_type != ''";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $shift_name = $pdo->select($table_details, $where);
    if ($shift_name->status) {
        return $shift_name->data;
    } else {
        print_r($shift_name);
        return 0;
    }
}

function remark_type($unique_id = "") {

    global $pdo;

    $table_name    = "remark_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "remark"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

   
    $where = "is_delete = 0 and  remark != ''";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $remark_type = $pdo->select($table_details, $where);
    if ($remark_type->status) {
        return $remark_type->data;
    } else {
        print_r($remark_type);
        return 0;
    }
}

function state_name($unique_id = "") {

    global $pdo;

    $table_name    = "state_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "state_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    // ];

    $where = "is_delete = 0 and is_active and state_name != ''";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $state_name = $pdo->select($table_details, $where);
    if ($state_name->status) {
        return $state_name->data;
    } else {
        print_r($state_name);
        return 0;
    }
}

function department_type($unique_id = "") {

    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $department_type = $pdo->select($table_details, $where);
   // print_r($depatment_name);
    if ($department_type->status) {
        return $department_type->data;
    } else {
        print_r($department_type);
        return 0;
    }
}

function dept_unique_id($unique_id = "") {

    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["department_type"] = $unique_id;
    }

    $department_type = $pdo->select($table_details, $where);
    //print_r($department_type);
    if ($department_type->status) {
        return $department_type->data;
    } else {
        print_r($department_type);
        return 0;
    }
}

function source_type($unique_id = "") {

    global $pdo;

    $table_name    = "source_name_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "source"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $source = $pdo->select($table_details, $where);
    if ($source->status) {
        return $source->data;
    } else {
        print_r($source);
        return 0;
    }
}
// function tag_person($unique_id = "") {
// // print_r($unique_id);
//     global $pdo;

//     $table_name    = "user";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "staff_name"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

   
//     $where = "is_delete = 0 and  staff_name != ''";

//     if ($unique_id) {

//         $where              = [];
//         $where["unique_id"] = $unique_id;
//         // $where .= "unique_id = '$unique_id'";
//     }
//     // $where .= "unique_id = '$unique_id'";
// // print_r($unique_id);
//     $user_name_select = $pdo->select($table_details, $where);
//     print_r($user_name_select);
//     if ($user_name_select->status) {
//         return $user_name_select->data;
//     } else {
//         print_r($user_name_select);
//         return 0;
//     }
// }

function tag_person($unique_id = "") {

    global $pdo;

    $table_name    = "user";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = "is_delete = 0 and  staff_name != ''";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $user_name_select = $pdo->select($table_details, $where);
    if ($user_name_select->status) {
        return $user_name_select->data;
    } else {
        print_r($user_name_select);
        return 0;
    }
}


function days_type($unique_id = "") {

    global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
        "unique_id",
        "days_cnt"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     // "is_active" => 1,
    //     // "is_delete" => 0
    // ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $days_cnt = $pdo->select($table_details, $where);
    if ($days_cnt->status) {
        return $days_cnt->data;
    } else {
        print_r($days_cnt);
        return 0;
    }
}


function category_name($unique_id = "",$department_name = "",$main_category = "") {

    global $pdo;

    $table_name    = "category_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "category_name",
        "main_category_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     =  "is_active= 1 and is_delete = 0";
    

    if ($unique_id) {

       
         $where .= " and unique_id = '".$unique_id."' ";
    }
    if ($department_name) {
         $where .= " and department_type = '".$department_name."' ";
        
    }
        if ($main_category) {
         $where .= " and  main_category_name = '".$main_category."' ";
        
    }
    $category_name = $pdo->select($table_details, $where);
    //print_r($category_name);
    if ($category_name->status) {
        return $category_name->data;
    } else {
        print_r($category_name);
        return 0;
    }
}

function category_creation($unique_id = "",$department_name = "",$main_category = "") {

    global $pdo;

    $table_name    = "category_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "category_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     =  "is_active= 1 and is_delete = 0";
    

    if ($unique_id) {

       
         $where .= " and unique_id = '".$unique_id."' ";
    }
    if ($department_name) {
         $where .= " and department_type = '".$department_name."' ";
        
    }
        if ($main_category) {
         $where .= " and  main_category_name = '".$main_category."' ";
        
    }
    $category_name = $pdo->select($table_details, $where);
//print_r($category_name);
    if ($category_name->status) {
        return $category_name->data;
    } else {
        print_r($category_name);
        return 0;
    }
}

function category_creations($unique_id = "", $department_name = "", $main_category = "") {
    global $pdo;

    $table_name = "category_creation";
    $where = "is_active = 1 and is_delete = 0";

    if ($unique_id) {
        $where .= " and unique_id = '".$unique_id."' ";
    }
    
    if ($department_name) {
        $where .= " and department_type = '".$department_name."' ";
    }

    if ($main_category) {
        $where .= " and main_category_name = '".$main_category."' ";
    }

    $table_columns = [
        "unique_id",
        "category_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $category_name = $pdo->select($table_details, $where);

    if ($category_name->status) {
        return $category_name->data;
    } else {
        print_r($category_name);
        return 0;
    }
}

function main_category_creation($unique_id = "",$department_type="") {

    global $pdo;

    $table_name    = "main_category_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "category_name",
        // "department_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    // ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }
    // if($department_type) {
    //     $where              = [];
    //     $where["department_type"] = $department_type;
    // }
    $where = "department_type = '$department_type' and is_delete = 0";

    $department_type_name = $pdo->select($table_details, $where);
    /////pint_r($department_type_name);
    if ($department_type_name->status) {
        return $department_type_name->data;
    } else {
        print_r($department_type_name);
        return 0;
    }
}

// category creation 
function main_category($unique_id = "",$department_type="") {

    global $pdo;

    $table_name    = "main_category_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "category_name",
        // "department_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    // ];

    if ($unique_id) {

       
         $where = " unique_id = '".$unique_id."' ";
    }
    // if($department_type) {
    //     $where              = [];
    //     $where["department_type"] = $department_type;
    // }
    if ($department_name) {
         $where .= " and department_type = '".$department_name."' ";
        
    }
    $department_type_name = $pdo->select($table_details, $where);
    /////pint_r($department_type_name);
    if ($department_type_name->status) {
        return $department_type_name->data;
    } else {
        print_r($department_type_name);
        return 0;
    }
}
function department_creation($unique_id = "",$department_name="") {

    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
        // $where["unique_id"] = $unique_id;
    }
    if ($department_name) {

        $where              = [];
        $where["department_name"] = $department_name;
        // $where["unique_id"] = $unique_id;
    }

    $order_by  = 'department_type ASC';

    $department_name = $pdo->select($table_details, $where,'','',$order_by);
   
    if ($department_name->status) {
        return $department_name->data;
    } else {
        print_r($department_name);
        return 0;
    }
}

function site_name($unique_id = "",$state_id="") {

    global $pdo;

    $table_name    = "site_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "site_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        // $where              = [];
        $where["unique_id"] = $unique_id;
    }
    if ($state_id) {

        // $where              = [];
        $where["state_name"] = $state_id;
    }

    $order_by  = 'site_name ASC';
    

    $site_name = $pdo->select($table_details, $where,'','',$order_by);
   // print_r($site_name);
    if ($site_name->status) {
        return $site_name->data;
    } else {
        print_r($site_name);
        return 0;
    }
}

//site_name_creation
function site_type($unique_id = "") {

    global $pdo;

    $table_name    = "site_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "site_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }
    // $order_by  = 'site_name ASC';

    $site_name = $pdo->select($table_details, $where);
   // print_r($site_name);
    if ($site_name->status) {
        return $site_name->data;
    } else {
        print_r($site_name);
        return 0;
    }
}

function site_unique_id($unique_id = "") {

    global $pdo;

    $table_name    = "site_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "site_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["site_name"] = $unique_id;
    }
    // $order_by  = 'site_name ASC';

    $site_name = $pdo->select($table_details, $where);
    //print_r($site_name);
    if ($site_name->status) {
        return $site_name->data;
    } else {
        print_r($site_name);
        return 0;
    }
}

function remark_unique_id($unique_id = "") {

    global $pdo;

    $table_name    = "remark_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "remark"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["remark"] = $unique_id;
    }
    // $order_by  = 'site_name ASC';

    $site_name = $pdo->select($table_details, $where);
    //print_r($site_name);
    if ($site_name->status) {
        return $site_name->data;
    } else {
        print_r($site_name);
        return 0;
    }
}


function site_number($unique_id = "",$state_id="") {

    global $pdo;

    $table_name    = "site_creation";
    $where         = [];
    $table_columns = [
        "site_name",
        "site_name AS site"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }
    if ($state_id) {

        $where              = [];
        $where["state_name"] = $state_id;
    }
    

    $site_name = $pdo->select($table_details, $where);
    if ($site_name->status) {
        return $site_name->data;
    } else {
        print_r($site_name);
        return 0;
    } 
}
// plant creation
function plant_name($unique_id = "",$site_id="") {

    global $pdo;

    $table_name    = "plant_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "plant_name",
        // "site_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_active = 1 AND is_delete = 0";

    if ($unique_id) {

       
        $where .= " and unique_id='".$unique_id."'";
    }
    if ($site_id) {

        
        $where .=" and site_id = '".$site_id."'";
        // $where["is_delete"] = 0;
    }

    $plant_creation = $pdo->select($table_details, $where);
//   print_r( $plant_creation);
    if ($plant_creation->status) {
        return $plant_creation->data;
    } else {
        print_r($plant_creation);
        return 0;
    }
}

function state_name_wise($unique_id = "") {

    global $pdo;

    $table_name    = "state_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "state_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND unique_id!='6276080e8169648644'";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $state_name = $pdo->select($table_details, $where);
    if ($state_name->status) {
        return $state_name->data;
    } else {
        print_r($state_name);
        return 0;
    }
}

function department_type_wise($unique_id = "") {

    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND unique_id!='6276080e8169648644'";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $department_type = $pdo->select($table_details, $where);
    if ($department_type->status) {
        return $department_type->data;
    } else {
        print_r($department_type);
        return 0;
    }
}

function state_name_number($unique_id = "") {

    global $pdo;

    $table_name    = "state_creation";
    $where         = [];
    $table_columns = [
        "state_number",
        "state_number AS state"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND unique_id!='6276080e8169648644'";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $state_name = $pdo->select($table_details, $where);
    if ($state_name->status) {
        return $state_name->data;
    } else {
        print_r($state_name);
        return 0;
    }
}

function units($unique_id = "") {
    global $pdo;

    $table_name    = "units_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "unit_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [];
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $units = $pdo->select($table_details, $where);

    // print_r($units);

    if ($units->status) {
        return $units->data;
    } else {
        print_r($units);
        return 0;
    }
}

function unit_name ($unique_id = "") {
    global $pdo;

    $table_name    = "units_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "unit_name",
        "decimal_points"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $unit_name_list = $pdo->select($table_details, $where);

    if ($unit_name_list->status) {
        return $unit_name_list->data;
    } else {
        print_r($unit_name_list);
        return 0;
    }
}




function designation_name($unique_id = "",$department_name = "") {

    global $pdo;

    $table_name    = "designation_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "designation_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    if ($department_name) {
        // $where = " WHERE state_id = '".$state_id."' ";
        $where["department_type"] = $department_name;
    }

    $designation_name = $pdo->select($table_details, $where);
   
    if ($designation_name->status) {
        return $designation_name->data;
    } else {
        print_r($designation_name);
        return 0;
    }
}


function dashboard_admin_menu($unique_id = "") {

    global $pdo;

    $table_name    = "dashboard_settings_menu_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "settings_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $dashboard_admin_menu = $pdo->select($table_details, $where);
   
    if ($dashboard_admin_menu->status) {
        return $dashboard_admin_menu->data;
    } else {
        print_r($dashboard_admin_menu);
        return 0;
    }
}


//expense type function

function health_uphc_name($unique_id = "") {

    global $pdo;

    $table_name    = "health_uphc_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "uphc_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $uphc_name = $pdo->select($table_details, $where);
    if ($uphc_name->status) {
        return $uphc_name->data;
    } else {
        print_r($uphc_name);
        return 0;
    }
}

function health_hsc_name($unique_id = "",$uphc_name="") {

    global $pdo;

    $table_name    = "health_hsc_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "hsc_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

     if ($uphc_name) {
        // $where = " WHERE state_id = '".$state_id."' ";
        $where["uphc_name"] = $uphc_name;
    }

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $hsc_name = $pdo->select($table_details, $where);
    if ($hsc_name->status) {
        return $hsc_name->data;
    } else {
        print_r($hsc_name);
        return 0;
    }
}

function uphc_name_wise($unique_id = "") {

    global $pdo;

    $table_name    = "health_uphc_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "uphc_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = "is_delete = 0 AND unique_id!='6276080e8169648644'";

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $uphc_name = $pdo->select($table_details, $where);
    if ($uphc_name->status) {
        return $uphc_name->data;
    } else {
        print_r($uphc_name);
        return 0;
    }
}


//ui
// indent order function
function education_product_action($unique_id = "",$school_category = "") {
    global $pdo;

    $table_name    = "education_product_name_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "product_name",
        "unit AS unit_id",
        "(SELECT unit_name FROM units_creation WHERE units_creation.unique_id = ".$table_name.".unit) AS unit_name",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    // if ($school_category) {
    //     // $where = " WHERE state_id = '".$state_id."' ";
    //     //$where["product_group"] = $school_category;

    //     if(($school_category=='62986dce92adc72348')||($school_category=='62986dedb301754483')||($school_category=='62986df85ca2b18713')){

    //             $product_check = '';
    //     }else {
    //             $product_check = "AND unique_id NOT IN ('62c3ea7f391cd52007')";

    //     }
    // }

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $product_action = $pdo->select($table_details, $where);

    if ($product_action->status) {
        return $product_action->data;
    } else {
        print_r($product_action);
        return 0;
    }
}


function financial_year($unique_id = "") {

    global $pdo;

    $table_name    = "financial_year";
    $where         = [];
    $table_columns = [
        "unique_id",
        "description"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $financial_year = $pdo->select($table_details, $where);
    if ($financial_year->status) {
        return $financial_year->data;
    } else {
        print_r($financial_year);
        return 0;
    }
}

//solid waste vehicle type
function solid_waste_vehicle($unique_id = "") {

    global $pdo;

    $table_name    = "solid_waste_vehicle_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "vehicle_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $vehicle_name = $pdo->select($table_details, $where);
    if ($vehicle_name->status) {
        return $vehicle_name->data;
    } else {
        print_r($vehicle_name);
        return 0;
    }
}

//installment type
function health_installment_type($unique_id = "") {

    global $pdo;

    $table_name    = "health_installment_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "installment_schedule"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $installment_schedule = $pdo->select($table_details, $where);
    if ($installment_schedule->status) {
        return $installment_schedule->data;
    } else {
        print_r($installment_schedule);
        return 0;
    }
}

function state_name_att($device_id = "") {

    global $pdo_att;

    $table_name    = "m_devices";
    $where         = [];
    $table_columns = [
        "state"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    
        $where              = [];
        $where["DeviceId"] = $device_id;
   

    $state_name = $pdo_att->select($table_details, $where);
    if ($state_name->status) {
        return $state_name->data;
    } else {
        print_r($state_name);
        return 0;
    }
}

function site_name_att($device_id = "",$state_id="") {

    global $pdo_att;

    $table_name    = "m_devices";
    $where         = [];
    $table_columns = [
        "site"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    
    if ($device_id) {

        $where              = [];
        $where["DeviceId"] = $device_id;
    }
    if ($state_id) {

        $where              = [];
        $where["state"] = $state_id;
    }

   
    

    $site_name = $pdo_att->select($table_details, $where);
    if ($site_name->status) {
        return $site_name->data;
    } else {
        print_r($site_name);
        return 0;
    }
}

function device_name_att($device_id = "") {

    global $pdo_att;

    $table_name    = "m_devices";
    $where         = [];
    $table_columns = [
        "DeviceFName"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    
        $where              = [];
        $where["DeviceId"] = $device_id;
   

    $device_name = $pdo_att->select($table_details, $where);
    if ($device_name->status) {
        return $device_name->data;
    } else {
        print_r($device_name);
        return 0;
    }
}


function category_name_wise($unique_id = "",$department_type = "") {
 //print_r($unique_id);
    global $pdo;

    $table_name    = "main_category_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "category_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    if ($department_type) {
        // $where = " WHERE state_id = '".$state_id."' ";
        $where["department_type"] = $department_type;
    }

    $category_name = $pdo->select($table_details, $where);
   //print_r($category_name);
    if ($category_name->status) {
        return $category_name->data;
    } else {
        print_r($category_name);
        return 0;
    }
}

function stage_level($unique_id = "") {

    global $pdo;

    $table_name    = "view_level_all_departments";
    $where         = [];
    $table_columns = [
        // "id",
        "level",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $group_by     =  "site_id";
    // $order_by = "id ASC";
    


    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $level_type = $pdo->select($table_details,$where);
      print_r($level_type);
    if ($level_type->status) {
        return $level_type->data;
    } else {
        print_r($level_type);
        return 0;
    }
}


function department_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "department_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " department_type LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}

function site_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "site_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " site_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}

function plant_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "plant_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = "plant_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}
function state_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "state_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " state_name  LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        // $group_by     = " quotation_unique_id ";
        // $group_by     = " ";

        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}

function main_category_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "main_category_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " category_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        
        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}

function complaint_category_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "category_creation";

        $columns        = [
            "CONCAT(\"'\",GROUP_CONCAT(DISTINCT unique_id SEPARATOR \"','\"),\"'\") as unique_id"
        ];

        $where          = " category_name LIKE '".mysql_like($search_key)."' ";

        $table_details  = [
            $table_name,
            $columns
        ];

        
        $select_result  = $pdo->select($table_details,$where,"","","","","");
        // print_r($select_result);

        if (!($select_result->status)) {
            print_r($select_result);
        } else {
            $result     = $select_result->data[0];

            $result     = $result['unique_id'];

            if ($result == "") {
                $result = "''";
            }
        }
    }

    return $result;
}


function select_option_category($options = [],$description = "", $is_selected = [],$is_disabled = []) {

    $option_str     = "<option value='' disabled>No Options to Select</option>";

    $data_attribute = "";

    if ($options) {

        $option_str     = "<option value=''>Select</option>";

        if ($description) {
            $option_str     = "<option value='' selected>".$description."</option>";
        }
        foreach ($options as $key => $value) {

            $value      = array_values($value);
            $selected   = "";
            $disabled   = "";
            //print_r($value[2]);
            if (is_array($is_selected) AND in_array($value[0],$is_selected)) {            
                $selected = " selected='selected' ";
            } elseif ($is_selected == $value[0]) {
                
                $selected = " selected='selected' ";
            }
            
            if (is_array($is_disabled) AND in_array($value[0],$is_disabled)) {            
                $disabled = " disabled='disabled' ";
            } elseif ($is_disabled == $value[0]) {
                $disabled = " disabled='disabled' ";
            }

            if (strpos($value[1],"_")) {
                $value[1] = disname($value[1]);
            } else {
                $value[1] = ucfirst($value[1]);
            }

            if (isset($value[2])) {
                $data_attribute = "data-extra='".$value[2]."'";
            } 
            
            $value[2] = category_name_wise($value[2])[0]['category_name']; 

            $option_str .= "<option value='".$value[0]."'".$data_attribute.$selected.$disabled.">".$value[1]."-".$value[2]."</option>";
        }
    }
    
    return $option_str;
}
?>