<?php

//$logo_img       = "img/logo-new1.png";
//$logo_img_dark  = "img/logo-new1.png";

$logo_img       = "img/logo-new.png";
$logo_img_dark  = "img/logo-new.png";
$logo_img_sm    = "";
$logo_img_print = "";

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

// Default Admin User Type
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
function category_name_wise($unique_id = "",$department = "") {
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

    if ($department) {
        // $where = " WHERE state_id = '".$state_id."' ";
        $where["department"] = $department;
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

function department_wise($unique_id = "") {

    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department"
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

    $department = $pdo->select($table_details, $where);
    if ($department->status) {
        return $department->data;
    } else {
        print_r($department);
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

        $where          = " department LIKE '".mysql_like($search_key)."' ";

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


function company_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "company_creation";

        $columns        = [
            "unique_id",
            "company_name",
        ];

        $where          .= " company_name LIKE '".mysql_like($search_key)."' ";
        $where          .= " and  is_delete=0 and is_active=1 ";

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
function project_name_like ($search_key = "") {


    $result     = "''";

    if ($search_key) {
        global $pdo;

        $table_name = "project_creation";

        $columns        = [
            "unique_id",
            "project_name",
        ];

        $where          .= " project_name LIKE '".mysql_like($search_key)."' ";
        $where          .= " and  is_delete=0 and is_active=1 ";

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

function get_client_ip()
{
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if (getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if (getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if (getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if (getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if (getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

$admin_user_type = "5f97fc3257f2525529";
$hr_user_type    = "5ff71f5fb5ca556748";

// Date Related Function
function today($format = "")
{
    if ($format) {
        return date($format);
    }

    return date("Y-m-d");
}


function today_time($format = "")
{
    if ($format) {
        return date($format);
    }

    return date("Y-m-d H:i:s");
}

function disdate($date)
{

    $result     = "";

    if ($date) {
        $result =  implode("-", array_reverse(explode("-", $date)));
    }

    return $result;
}

$today            = today();
$today_time       = today_time();

// Bill No Generation

function bill_no($table_name, $where, $prefix = "", $y = 1, $m = 1, $d = 1, $custom_date = false, $separator = "")
{
    $billno = $prefix;

    if (!$custom_date) {
        $custom_date = date("Y-m-d");
    }

    if ($y) {
        $billno .= date('Y', strtotime($custom_date)) . $separator;
    }

    if ($m) {
        $billno .= date('m', strtotime($custom_date)) . $separator;
    }

    if ($d) {
        $billno .= date('d', strtotime($custom_date)) . $separator;
    }

    $bill_order_no  =  save_status($table_name, $where);

    $billno        .= sprintf("%04d", $bill_order_no);

    return $billno;
}

function item_bill_no($table_name, $where, $prefix = "", $y = 1, $m = 1, $d = 1, $custom_date = false, $separator = "")
{
    $billno = $prefix;

    // if (!$custom_date) {
    //     $custom_date = date("Y-m-d");
    // }

    // if ($y) {
    //     $billno .= date('Y', strtotime($custom_date)) . $separator;
    // }

    // if ($m) {
    //     $billno .= date('m', strtotime($custom_date)) . $separator;
    // }

    // if ($d) {
    //     $billno .= date('d', strtotime($custom_date)) . $separator;
    // }

    $bill_order_no  =  save_status($table_name, $where);

    $billno        .= sprintf("%04d", $bill_order_no);

    return $billno;
}


// Get Final Bill No

function save_status($table_name, $where)
{
    if ($table_name) {
        global $pdo;

        $columns    = [
            "count(acc_year) AS save_status"
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        $result         = $pdo->select($table_details, $where);

        if ($result->status) {

            $res_array      = $result->data[0]['save_status'] + 1;
        } else {
            print_r($result);
            $res_array = uniqid() . rand(10000, 99999) . "Error";
        }

        return $res_array;
    }
}


function emp_id($table_name, $emp_prefix)
{
    if ($table_name) {
        global $pdo;

        $columns    = [
            "employee_id"
        ];

        $table_details = [
            $table_name,
            $columns
        ];

        // Query to get the latest employee ID
        $where          = "is_delete = 0 and is_active = 1 order by employee_id DESC LIMIT 1";
        $result         = $pdo->select($table_details, $where);

        if ($result->status && !empty($result->data)) {
            list($alpha, $numeric) = sscanf($result->data[0]['employee_id'], "%[A-Z]%d");
            $value_no = $numeric;

            $res_array   = $value_no + 1;
            $emp_id      = $emp_prefix . $res_array;
        } else {
            // If no employee ID is found, start with the first one
            $emp_id = $emp_prefix . '1';
        }

        return $emp_id;
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
$result_obj         = $pdo->select($perm_table_details, $perm_where);

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
function unique_id($prefix = "")
{

    $unique_id = uniqid() . rand(10000, 99999);

    if ($prefix) {
        $unique_id = $prefix . $unique_id;
    }

    return $unique_id;
}

function user_permission($permission_id = "", $folder_name = "")
{

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

function menu_permission($user_type_id = "")
{

    $return_arr = [
        "main_screens"  => "",
        "sections"      => "",
        "screens"       => ""
    ];

    if ($user_type_id) {

        global $pdo;

        $table_user_permission = "user_screen_permission";

        $select_where   = [
            "user_type" => $user_type_id
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

        $screen_result     = $pdo->select($table_details, $select_where, "", "", "", "", $group_by);

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

        $section_result     = $pdo->select($table_details, $select_where, "", "", "", "", $group_by);

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

        $main_screen_result     = $pdo->select($table_details, $select_where, "", "", "", "", $group_by);

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

function array_implode($value_arr = "")
{

    $return_arr = [];

    if (is_array($value_arr)) {

        foreach ($value_arr as $arr_key => $arr_value) {

            $return_arr[] = array_values($arr_value)[0];
        }
    }

    return $return_arr;
}

function folder_permission($folder_name = "")
{
}

function acc_year()
{
    $acc_year     = '';
    $curr_year     = date("Y");

    $today      = strtotime(date("d-m-Y"));
    // $today         = "02-02-2025";
    $end_date      = strtotime("31-03-" . $curr_year);
    $start_date    = strtotime("01-04-" . $curr_year);

    if ($today >= $start_date) {
        $next_year         = $curr_year + 1;
        $acc_year          = $curr_year . "-" . $next_year;
    } else if ($today <= $end_date) {
        $previous_year     = $curr_year - 1;
        $acc_year          = $previous_year . "-" . $curr_year;
    }

    return $acc_year;
}

function btn_add($add_link = "")
{
    $final_str = '<a href="' . $add_link . '" class="">
    
	<button type="button" class="btn btn-primary">
  <i class="fe-plus"></i>Add New 
</button>
    </a>';

    return $final_str;
}

// function btn_cancel ($list_link = "") {
//     $final_str = '<a href="'.$list_link.'"><button type="button" class="btn btn-danger btn-rounded waves-effect waves-light float-right ml-2" >Cancel</button></a>';

//     return $final_str;
// }
function btn_cancel($list_link = "")
{
    $final_str = '<a href="' . $list_link . '"><button type="button" class="btn btn-soft-danger waves-effect waves-light" ><i class="fe-x"></i> Cancel</button></a>';

    return $final_str;
}
function btn_cancel_dar($list_link = "", $date)
{
    $final_str = '<a href="index.php?file=day_attendance_report/list&date=' . $date . ' "><button type="button" class="btn btn-soft-danger waves-effect waves-light" ><i class="fe-x"></i> Cancel</button></a>';

    return $final_str;
}

function btn_createupdate($folder_name = "", $unique_id = "", $btn_text, $prefix = "", $suffix = "_cu", $custom_class = "")
{
    $final_str = '<button type="button" class="btn btn-success ' . $custom_class . '" onclick="' . $folder_name . $suffix . '(\'' . $unique_id . '\')"><i class="fe-inbox"></i> &nbsp;'.  $btn_text . '</button>';

    return $final_str;
}
function btn_createupdate_dar($folder_name = "", $unique_id = "", $btn_text, $date, $prefix = "", $suffix = "_cu", $custom_class = "")
{
    $final_str = '<button type="button" class="btn btn-success  ' . $custom_class . '" onclick="' . $folder_name . $suffix . '(\'' . $unique_id . '\',\'' . $date . '\',\'day_attendance_report\')"> <i class="fe-inbox"></i> &nbsp;'.  $btn_text . '</button>';

    return $final_str;
}
function btn_update($folder_name = "", $unique_id = "", $prefix = "", $suffix = "", $date = "", $form = "")
{
    // $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&unique_id='.$unique_id.'"><button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-square-edit-outline"></i></button></a>';

    $final_str = '<a href="index.php?file=' . $prefix . $folder_name . $suffix . '/update&unique_id=' . $unique_id . '&date=' . $date . '&form=' . $form . '"><i class="fs-10"><i class="fe-edit-1"></i></a>';

    //$final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&unique_id='.$unique_id.'"><i class="mdi mdi-square-edit-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-green"></i></a>';
    return $final_str;
}
function weighbridge_entry_btn_update($folder_name = "", $unique_id = "", $prefix = "", $suffix = "", $date = "", $form = "")
{
    // $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&unique_id='.$unique_id.'"><button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-square-edit-outline"></i></button></a>';

    $final_str = '<a href="index.php?file=' . $prefix . $folder_name . $suffix . '/list&unique_id=' . $unique_id . '&date=' . $date . '&form=' . $form . '"><i class="fs-10"><i class="fe-edit-1"></i></a>';

    //$final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&unique_id='.$unique_id.'"><i class="mdi mdi-square-edit-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-green"></i></a>';
    return $final_str;
}

function btn_create($folder_name = "", $prefix = "", $suffix = "", $date = "", $form = "")
{
    // $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&unique_id='.$unique_id.'"><button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-square-edit-outline"></i></button></a>';

    $final_str = '<a href="index.php?file=' . $prefix . $folder_name . $suffix . '/create&date=' . $date . '&form=' . $form . '"><i class="mdi mdi mdi-shape-square-plus mdi-24px waves-effect waves-light mt-n2 mb-n2 text-green"></i></a>';


    return $final_str;
}

function btn_view($folder_name = "", $unique_id = "", $prefix = "", $suffix = "")
{

    $final_str = '<a href="index.php?file=' . $prefix . $folder_name . $suffix . '/view&unique_id=' . $unique_id . '"><i class="mdi mdi-eye-outline mdi-24px waves-effect waves-light mt-n2 mb-n2 text-pink mr-1"></i></a>';


    return $final_str;
}

function btn_delete($folder_name = "", $unique_id = "")
{
    // $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')"><i class="mdi mdi-delete"></i></button>';

    // $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')"><i class="mdi mdi-delete"></i></button>';

    // $final_str = '<a href="#" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')"><i class="mdi mdi-delete mdi-24px waves-effect waves-light text-danger"></a>';

    $final_str = '<a href="#" onclick="' . $folder_name . '_delete(\'' . $unique_id . '\')"><i class="text-danger fs-10"><i class="fe-trash-2"></i></a>';

    return $final_str;
}
function btn_toggle_on($folder_name = "", $unique_id = "")
{
    $icon_class = 'fa-toggle-on text-success';
    $title = 'Deactivate';

    return '<a href="javascript:void(0);" onclick="' . $folder_name . '_toggle(\'' . $unique_id . '\', 0)" title="' . $title . '" id="toggle_' . $unique_id . '">
                <i class="fa ' . $icon_class . '" style="font-size: 18px;"></i>
            </a>';
}

function btn_toggle_off($folder_name = "", $unique_id = "")
{
    $icon_class = 'fa-toggle-off text-danger';
    $title = 'Activate';

    return '<a href="javascript:void(0);" onclick="' . $folder_name . '_toggle(\'' . $unique_id . '\', 1)" title="' . $title . '" id="toggle_' . $unique_id . '">
                <i class="fa ' . $icon_class . '" style="font-size: 18px;"></i>
            </a>';
}

function btn_call_update($folder_name = "", $unique_id = "", $prefix = "", $suffix = "")
{
    $final_str = '<a href="index.php?file=' . $prefix . $folder_name . $suffix . '/update&is_phone=1&unique_id=' . $unique_id . '"><i class="mdi mdi-phone-in-talk  mdi-24px waves-effect waves-light mt-n2 mb-n2 text-warning"></i></a>';
    // $final_str = '<a href="index.php?file='.$prefix.$folder_name.$suffix.'/update&is_phone=1&unique_id='.$unique_id.'"><img src="img/start.png" width="27" height="27" alt="Start Call"></a>';

    return $final_str;
}

function btn_call_start($customer_id = "", $followup_id = "")
{
    $final_str = '<a href="javascript:void(0);" onclick="start_call(\'' . $customer_id . '\',\'' . $followup_id . '\')"><i class="mdi mdi-play-circle  mdi-24px waves-effect waves-light mt-n2 mb-n2 ml-2 text-primary"></i></a>';
    $final_str = '<a href="javascript:void(0);" onclick="start_call(\'' . $customer_id . '\',\'' . $followup_id . '\')"><img src="img/start.png" width="27" height="27" alt="Start Call" class="ml-2"></a>';

    return $final_str;
}

function btn_map($latitude = "", $longitude = "")
{

    $def_latitude = "13.0456605";
    $def_longitude = "80.2086916";

    $final_str = '<a target="_blank" href="https://www.google.com/maps/dir/?api=1&origin=' . $def_latitude . "," . $def_longitude . '&destination=' . $latitude . ',' . $longitude . '"><i class="mdi mdi-map-marker mdi-24px waves-effect waves-light mt-n2 mb-n2 text-primary"></i></a>';

    return $final_str;
}

function btn_print($folder_name = "", $unique_id = "", $file_name = "", $prefix = "", $suffix = "", $tooltip)
{
    //$final_str = '<a target="_blank" href="index.php?file='.$prefix.$folder_name.$suffix.'/'.$file_name.'&unique_id='.$unique_id.'"><button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-printer"></i></button></a>';

    if ($tooltip) {
        $final_str = '<a class="" target="_blank" data-toggle="tooltip" title = "' . htmlspecialchars($tooltip) . '" href="index.php?file=' . $prefix . $folder_name . $suffix . '/' . $file_name . '&unique_id=' . $unique_id . '"><i class="mdi mdi-printer mdi-24px waves-effect waves-light mt-n2 mb-n2 mr-1 text-success"></i></a>';
    } else {
        $final_str = '<a target="_blank"  href="index.php?file=' . $prefix . $folder_name . $suffix . '/' . $file_name . '&unique_id=' . $unique_id . '"><i class="mdi mdi-printer mdi-24px waves-effect waves-light mt-n2 mb-n2 mr-1 text-success"></i></a>';
    }

    return $final_str;
}

function btn_print1($folder_name = "", $unique_id = "", $file_name = "", $prefix = "", $suffix = "", $tooltip) 
{
    // Create a unique ID for the print link
    $print_link_id = "print_" . uniqid();

    // Generate the final string with JavaScript to delay the print dialog
    $final_str = '<a id="' . $print_link_id . '" class="" target="_blank"';

    if ($tooltip) {
        $final_str .= ' data-toggle="tooltip" title="' . htmlspecialchars($tooltip) . '"';
    }

    $final_str .= ' href="index.php?file=' . $prefix . $folder_name . $suffix . '/' . $file_name . '&unique_id=' . $unique_id . '">';
    $final_str .= '<i class="mdi mdi-printer mdi-24px waves-effect waves-light mt-n2 mb-n2 mr-1 text-success"></i></a>';

    // Add JavaScript to delay the print dialog
    $final_str .= '
    <script>
        document.getElementById("' . $print_link_id . '").addEventListener("click", function(event) {
            event.preventDefault();
            var printWindow = window.open(this.href, "_blank");
            printWindow.onload = function() {
                setTimeout(function() {
                    printWindow.print();
                }, 1000); // Delay the print dialog to ensure content loads
            };
        });
    </script>';

    return $final_str;
}


function btn_print_in_list($folder_name = "", $unique_id = "", $file_name = "", $prefix = "", $suffix = "", $content = '<button type="button" class="btn btn-info btn-rounded waves-effect waves-light float-right"><i class="mdi mdi-printer mdi-16px waves-effect waves-light text-white"></i></button>')
{

    $final_str = '<a target="_blank" href="index.php?file=' . $prefix . $folder_name . $suffix . '/' . $file_name . '&unique_id=' . $unique_id . '">' . $content . '</a>';

    return $final_str;
}

function btn_print_in_form($folder_name = "", $unique_id = "", $file_name = "", $prefix = "", $suffix = "")
{

    $final_str = '<a target="_blank" href="index.php?file=' . $prefix . $folder_name . $suffix . '/' . $file_name . '&unique_id=' . $unique_id . '"><button type="button" class="btn btn-info btn-rounded waves-effect waves-light float-right mr-2"><i class="mdi mdi-printer mdi-16px waves-effect waves-light text-white">' . $file_name . '</i></button></a>';

    return $final_str;
}

function btn_approval_print($folder_name = "", $unique_id = "", $sub_unique_id = "", $file_name = "", $prefix = "", $suffix = "")
{
    $final_str = '<a target="_blank" href="index.php?file=' . $prefix . $folder_name . $suffix . '/' . $file_name . '&unique_id=' . $unique_id . '&sub_unique_id=' . $sub_unique_id . '"><button type="button" class="btn btn-warning  btn-xs btn-rounded waves-effect waves-light mr-1"><i class="mdi mdi-lead-pencil"></i></button></a>';

    return $final_str;
}



function btn_edit($folder_name = "", $unique_id = "")
{
    $final_str = '<button type="button" class="btn btn-asgreen btn-xs btn-rounded waves-effect waves-light " onclick="' . $folder_name . '_edit(\'' . $unique_id . '\')"><i class="mdi mdi-square-edit-outline"></i></button>';

    $final_str = '<a href="#" onclick="' . $folder_name . '_edit(\'' . $unique_id . '\')"><i class="mdi mdi-square-edit-outline  mdi-24px waves-effect waves-light mt-n2 mb-n2 text-success"></i></a>';

    return $final_str;
}



// Datatables Total Records Count Function
function total_records()
{
    global $pdo;

    $total_records  = 0;
    $sql             = "SELECT FOUND_ROWS() as total";
    $result            = $pdo->query($sql);
    if ($result->status) {
        $total      = $result->data[0]["total"];
    }

    return $total;
}

// Convert Original folder Name to Display Name
function disname($name = "")
{
    if ($name) {
        $name = explode("_", $name);
        $name = array_map("ucfirst", $name);
        $name = implode(" ", $name);

        return $name;
    } else {
        return "Empty Title";
    }
}

// Continents Function
function continent($unique_id = "", $columns = "")
{
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

function select_option($options = [], $description = "", $is_selected = [], $is_disabled = [])
{

    $option_str     = "<option value='' disabled>No Options to Select</option>";

    $data_attribute = "";

    if ($options) {

        $option_str     = "<option value=''>Select</option>";

        if ($description) {
            $option_str     = "<option value=''>" . $description . "</option>";
        }
        foreach ($options as $key => $value) {

            $value      = array_values($value);
            $selected   = "";
            $disabled   = "";

            if (is_array($is_selected) and in_array($value[0], $is_selected)) {
                $selected = " selected='selected' ";
            } elseif ($is_selected == $value[0]) {

                $selected = " selected='selected' ";
            }

            if (is_array($is_disabled) and in_array($value[0], $is_disabled)) {
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
                $data_attribute = "data-extra='" . $value[2] . "'";
            }

            $option_str .= "<option value='" . $value[0] . "'" . $data_attribute . $selected . $disabled . ">" . $value[1] . "</option>";
        }
    }

    return $option_str;
}


function active_status_show($is_active = 0) {
$act_str = "In Active";
if ($is_active){

        $act_str = "Active";
    }

    return $act_str;
}


function active_status($is_active_val = 1)
{
    $option_str    = "";
    $is_active     = "";
    $is_inactive   = "";

    if ($is_active_val == 1) {
        $is_active     = " selected = 'selected' ";
    } else {
        $is_inactive   = " selected = 'selected' ";
    }

    $option_str     =  "<option value='1'" . $is_active . ">Active</option>";
    $option_str     .=  "<option value='0'" . $is_inactive . ">In Active</option>";

    return $option_str;
}



// Active and In Active Show in Data Table
function is_active_show($is_active = 0)
{
    $act_str = "<span style='color: red'>In Active</span>";

    if ($is_active) {
        $act_str = "<span style='color: green'>Active</span>";
    }

    return $act_str;
}


// Country Function
function country($unique_id = "")
{
    global $pdo;

    $table_name    = "countries";
    $where         = [];
    $table_columns = [
        "unique_id",
        "name"
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

    $countries = $pdo->select($table_details, $where);

    if ($countries->status) {
        return $countries->data;
    } else {
        print_r($countries);
        return 0;
    }
}
// qualification
// function // qualification
// ($unique_id = '',$country_id = "") {
//     global $pdo;

//     $table_name    = "qualification_details";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "graduation_type",
//         "qualification"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where     = [
//         "is_active" => 1,
//         "is_delete" => 0
//     ];

//     if ($country_id) {
//         // $where = " WHERE country_id = '".$country_id."' ";
//         $where["country_unique_id"] = $country_id;
//     }

//     if ($unique_id) {
//         $table_details = $table_name;
//         $where         = [
//             "unique_id" => $unique_id
//         ];
//     }

//     $states = $pdo->select($table_details, $where);

//     // print_r($states);

//     if ($states->status) {
//         return $states->data;
//     } else {
//         print_r($states);
//         return 0;
//     }
// }


//Ledger


function ledger($unique_id = "")
{
    global $pdo;

    $table_name    = "ledger_group";
    $where         = [];
    $table_columns = [
        "unique_id",
        "ledger_group"
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

    $ledger = $pdo->select($table_details, $where);

    if ($ledger->status) {
        return $ledger->data;
    } else {
        print_r($ledger);
        return 0;
    }
}







// State Function
function state($unique_id = '', $country_id = "")
{
    global $pdo;

    $table_name    = "states";
    $where         = [];
    $table_columns = [
        "unique_id",
        "state_name",
        "state_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($country_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["country_unique_id"] = $country_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $states = $pdo->select($table_details, $where);

    // print_r($states);

    if ($states->status) {
        return $states->data;
    } else {
        print_r($states);
        return 0;
    }
}

function get_project_name_all($unique_id = '', $company_id = "")
{
    global $pdo;

    $table_name    = "project_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "project_name",
        "project_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($company_id) {
        $where["company_name"] = $company_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $projects = $pdo->select($table_details, $where);

    if ($projects->status) {
        // Format output as "ProjectCode / ProjectName"
        $formatted = [];
        foreach ($projects->data as $row) {
            $formatted[] = [
                "unique_id" => $row["unique_id"],
                "label"     => $row["project_code"] . " / " . $row["project_name"]
            ];
        }
        return $formatted;
    } else {
        return 0;
    }
}

function get_project_name($unique_id = '', $company_id = "")
{
    global $pdo;
    session_start(); // Ensure session is active

    $table_name    = "project_creation";
    $table_columns = [
        "unique_id",
        "project_name",
        "project_code"
    ];

    $table_details = [$table_name, $table_columns];

    // Base condition: fetch all active and non-deleted projects
    $where = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    // Optional filter: company
    if (!empty($company_id)) {
        $where["company_name"] = $company_id;
    }

    // If a specific project requested, skip the rest
    if (!empty($unique_id)) {
        $where = ["unique_id" => $unique_id];
    }

    // --- Fetch all matching projects ---
    $projects = $pdo->select($table_details, $where);

    if (!$projects->status || empty($projects->data)) {
        return 0;
    }

    // --- SESSION FILTER (done in PHP) ---
    $session_work = $_SESSION['work_location'] ?? '';
    $filtered_data = [];

    if (!empty($session_work) && strtolower($session_work) !== 'all') {
        // Convert to array (handles both string and array)
        $session_projects = is_array($session_work)
            ? $session_work
            : array_map('trim', explode(',', $session_work));

        // Keep only projects whose unique_id matches the session
        foreach ($projects->data as $row) {
            if (in_array($row['unique_id'], $session_projects)) {
                $filtered_data[] = $row;
            }
        }
    } else {
        // If session empty or 'all', keep all results
        $filtered_data = $projects->data;
    }

    // --- Format final output ---
    if (!empty($filtered_data)) {
        $formatted = [];
        foreach ($filtered_data as $row) {
            $formatted[] = [
                "unique_id" => $row["unique_id"],
                "label"     => $row["project_code"] . " / " . $row["project_name"]
            ];
        }
        return $formatted;
    }

    return 0;
}







function get_pr_number($unique_id = '', $company_id = '')
{
    global $pdo;

    $table_name    = "purchase_requisition";
    $table_columns = [
        "unique_id",
        "pr_number"
    ];

    $where = [
        // "is_active" => 1,
        "is_delete" => 0
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $states = $pdo->select($table_details, $where);
    // print_r($states);
    if ($states->status) {
        return $states->data;
    } else {
        // Instead of printing, you may want to log this error or handle it differently
        // print_r($states);
        return 0;
    }
}





// unit Name Function
function unit_name($unique_id = '')
{
    global $pdo;

    $table_name    = "units";
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

// Currency Creation Function
function currency_creation_name($unique_id = '', $country_id = "")
{
    global $pdo;

    $table_name    = "currency_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "currency_name"
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
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $currency_name = $pdo->select($table_details, $where);

    // print_r($currency_name);

    if ($currency_name->status) {
        return $currency_name->data;
    } else {
        print_r($currency_name);
        return 0;
    }
}

// MSME Creation Function
function msme_creation_name($unique_id = '', $country_id = "")
{
    global $pdo;

    $table_name    = "msme_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "msme_type"
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
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $msme_name = $pdo->select($table_details, $where);

    // print_r($msme_name);

    if ($msme_name->status) {
        return $msme_name->data;
    } else {
        print_r($msme_name);
        return 0;
    }
}
// Supplier Category Creation Function
function supplier_category_name($unique_id = '', $country_id = "")
{
    global $pdo;

    $table_name    = "supplier_category_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "category_name",
        "unique_id"
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
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $category_name = $pdo->select($table_details, $where);

    // print_r($category_name);

    if ($category_name->status) {
        return $category_name->data;
    } else {
        print_r($category_name);
        return 0;
    }
}
// Product Name Function
function product_name($unique_id = '')
{
    global $pdo;

    $table_name    = "product_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "product_name",
        "group_unique_id"
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
        $where["unique_id"] = $unique_id;
    }
    
    $product = $pdo->select($table_details, $where);

    // print_r($product);

    if ($product->status) {
        return $product->data;
    } else {
        print_r($product);
        return 0;
    }
}
// Group Name Function
function group_name($unique_id = '', $group_id = "", $not_in="")
{
    global $pdo;

    $table_name    = "groups";
    $where         = [];
    $table_columns = [
        "unique_id",
        "group_name"
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
        $where         = [
            "unique_id" => $unique_id
        ];
    }
    if ($not_in) {
        $table_details;
        $where = " is_active = 1 AND is_delete = 0 AND unique_id NOT IN ('$not_in')";
    }

    $group = $pdo->select($table_details, $where);

    // print_r($group);

    if ($group->status) {
        return $group->data;
    } else {
        print_r($group);
        return 0;
    }
}


// Sub Group Name Function
function sub_group_name($unique_id = '', $group_id = "")
{
    global $pdo;

    $table_name    = "sub_group";
    $where         = [];
    $table_columns = [
        "unique_id",
        "sub_group_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];
    
    if ($group_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["group_unique_id"] = $group_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $group = $pdo->select($table_details, $where);

     //print_r($group);

    if ($group->status) {
        return $group->data;
    } else {
        print_r($group);
        return 0;
    }
}

// Category Name Function
function category_name($unique_id = '',$group_id = "", $sub_group_id = "")
{
    global $pdo;

    $table_name    = "category_master";
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
    
    if ($sub_group_id) {
        $where = [
            "sub_group_unique_id" => $sub_group_id
        ]; 
    }
    if ($group_id) {
        $where = [
            "group_unique_id" => $group_id
        ]; 
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $category = $pdo->select($table_details, $where);

    // print_r($category);

    if ($category->status) {
        return $category->data;
    } else {
        print_r($category);
        return 0;
    }
}
// item Name Function
function category_item($unique_id = '',$group_id = "", $sub_group_id = "",$category = "")
{
    global $pdo;

    $table_name    = "item_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "item_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];
    
    if ($sub_group_id) {
        $where = [
            "sub_group_unique_id" => $sub_group_id
        ]; 
    }
    if ($group_id) {
        $where = [
            "category_unique_id" => $category
        ]; 
    }
    if ($category) {
        $where = [
            "category_unique_id" => $category
        ]; 
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $category = $pdo->select($table_details, $where);

    // print_r($category);

    if ($category->status) {
        return $category->data;
    } else {
        print_r($category);
        return 0;
    }
}

// City Function
function city($unique_id = "", $state_id = "")
{
    global $pdo;

    $table_name    = "cities";
    $where         = [];
    $table_columns = [
        "unique_id",
        "city_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,
        "country_unique_id" => "coun5f7a05b7110cd84071"
    ];

    if ($state_id) {
        // $where = " WHERE state_id = '".$state_id."' ";
        $where["state_unique_id"] = $state_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [];
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $cities = $pdo->select($table_details, $where);

    // print_r($cities);

    if ($cities->status) {
        return $cities->data;
    } else {
        print_r($cities);
        return 0;
    }
}



// city name


function city_name($unique_id = "")
{
    global $pdo;

    $table_name    = "cities";
    $where         = [];
    $table_columns = [
        "unique_id",
        "city_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    // if ($city_type) {
    //     // $where = " WHERE city_type = '".$city_type."' ";
    //     $where["city_type"] = $city_type;
    // }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [];
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $cities = $pdo->select($table_details, $where);

    // print_r($cities);

    if ($cities->status) {
        return $cities->data;
    } else {
        print_r($cities);
        return 0;
    }
}

function item_name_list($unique_id = "")
{
    global $pdo;

    $table_name    = "item_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "CONCAT(item_name, ' / ', item_code) AS text",
        "item_name",
        "item_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    // if ($city_type) {
    //     // $where = " WHERE city_type = '".$city_type."' ";
    //     $where["city_type"] = $city_type;
    // }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [];
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $cities = $pdo->select($table_details, $where);

    // print_r($cities);

    if ($cities->status) {
        return $cities->data;
    } else {
        print_r($cities);
        return 0;
    }
}

function city_type($unique_id = '', $city = "")
{
    global $pdo;
// print_r($city);
    $table_name    = "vehicle_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "city_type",
        // "state_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($city) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["city_name"] = $city;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $city_type = $pdo->select($table_details, $where);

    // print_r($states);

    if ($city_type->status) {
        return $city_type->data;
    } else {
        print_r($city_type);
        return 0;
    }
}


// Screen Type Function
function screen_type($unique_id = "")
{
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
function main_screen($unique_id = "")
{
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

     $order_by = "order_no ASC";
    $main_screens = $pdo->select($table_details, $where,'','',$order_by);

    if ($main_screens->status) {
        return $main_screens->data;
    } else {
        print_r($main_screens);
        return 0;
    }
}

// vehicle number Function
function vehicle_no($unique_id = "")
{
    global $pdo;

    $table_name    = "vehicle_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "vehicle_no"
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

// source of waste Function
function source_of_waste($unique_id = "")
{
    global $pdo;

    $table_name    = "source_of_waste_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "material_name"
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

    $material_name = $pdo->select($table_details, $where);

    if ($material_name->status) {
        return $material_name->data;
    } else {
        print_r($material_name);
        return 0;
    }
}

// Main Screen Function
function section_name($unique_id = "", $main_screen_id = "")
{
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
function user_screen($unique_id = "", $screen_section_id = "")
{
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
function user_actions($unique_id = "")
{
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
function user_type($unique_id = "")
{

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



// function asset_status($unique_id = "") {

//     global $pdo;

//     $table_name    = "staff_asset_details";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "status"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where     = [
//         "is_active" => 1,
//         "is_delete" => 0
//     ];

//     if ($unique_id) {

//         $where              = [];
//         $where["unique_id"] = $unique_id;
//     }

//     $asset_status = $pdo->select($table_details, $where);
// // print_r($asset_status);die();
//     if ($asset_status->status) {
//         return $asset_status->data;
//     } else {
//         print_r($asset_status);
//         return 0;
//     }
// }
// Call type Function
function call_type($unique_id = "")
{
    global $pdo;

    $table_name    = "call_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "call_type"
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

    $call_types = $pdo->select($table_details, $where);

    if ($call_types->status) {
        return $call_types->data;
    } else {
        print_r($call_types);
        return 0;
    }
}

// Call type Function
function forecast($unique_id = "", $column_where = "")
{
    global $pdo;

    $table_name    = "forecast";
    $where         = [];
    $table_columns = [
        "unique_id",
        "forecast",
        "is_active"
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

    if ($column_where) {
        // $where[array_keys($column_where)[0]] = $column_where[array_keys($column_where)[0]];
        $where = array_merge($where, $column_where);
    }

    $forecasts = $pdo->select($table_details, $where);

    if ($forecasts->status) {
        return $forecasts->data;
    } else {
        print_r($forecasts);
        return 0;
    }
}

// Call type Function
function business_forecast($unique_id = "", $column_where = "")
{
    global $pdo;

    $table_name    = "business_forecasts";
    $where         = [];
    $table_columns = [
        "business_forecast AS ids",
        "(SELECT forecast FROM forecast AS fc WHERE fc.unique_id = " . $table_name . ".business_forecast) AS business_forecast"
        // "business_forecast"
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

    if ($column_where) {
        // $where[array_keys($column_where)[0]] = $column_where[array_keys($column_where)[0]];
        $where = array_merge($where, $column_where);
    }

    $business_forecasts = $pdo->select($table_details, $where);

    if ($business_forecasts->status) {
        return $business_forecasts->data;
    } else {
        print_r($business_forecasts);
        return 0;
    }
}

// Call type Function
function business_forecast_by_name($unique_id = "", $column_where = "")
{
    global $pdo;

    $table_name    = "view_business_forecast";
    $where         = [];
    $table_columns = [
        "business_forecast AS id",
        "business_forecast"
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
        $where                      = [];
        $where["business_forecast"] = $unique_id;
    }

    if ($column_where) {
        // $where[array_keys($column_where)[0]] = $column_where[array_keys($column_where)[0]];
        $where = array_merge($where, $column_where);
    }

    $business_forecasts = $pdo->select($table_details, $where);

    if ($business_forecasts->status) {
        return $business_forecasts->data;
    } else {
        print_r($business_forecasts);
        return 0;
    }
}

// Call Status Function
function call_status($unique_id = "", $column_where = "")
{
    global $pdo;

    $table_name    = "call_status";
    $where         = [];
    $table_columns = [
        "unique_id",
        "call_status"
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

    if ($column_where) {
        // $where[array_keys($column_where)[0]] = $column_where[array_keys($column_where)[0]];
        $where = array_merge($where, $column_where);
    }

    $call_status = $pdo->select($table_details, $where);

    if ($call_status->status) {
        return $call_status->data;
    } else {
        print_r($call_status);
        return 0;
    }
}

// Main Screen Function
function call_type_stage($unique_id = "")
{
    global $pdo;

    $table_name    = "call_type";
    $where         = [];
    $table_columns = [
        "stages",
        "call_type"
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

        if (is_array($unique_id)) {

            $where = "";
            $where = "is_active = 1 AND is_delete = 0 ";
            $where .= " AND stages IN ('" . implode("','", $unique_id) . "')";
        } else {
            $where              = [];
            $where["unique_id"] = $unique_id;
        }
    }

    $call_types = $pdo->select($table_details, $where);

    if ($call_types->status) {
        return $call_types->data;
    } else {
        print_r($call_types);
        return 0;
    }
}


// Customers Function
function customers($unique_id = "")
{
    global $pdo;

    $table_name    = "customer_profile";
    $where         = [];
    $table_columns = [
        "unique_id",
        "customer_name"
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

    $customer = $pdo->select($table_details, $where);

    // print_r($countries);

    if ($customer->status) {
        return $customer->data;
    } else {
        // print_r($customer);
        return 0;
    }
}

//Sub Customers Function
function customer_category($unique_id = "")
{
    global $pdo;

    $table_name    = "customer_category";
    $where         = [];
    $table_columns = [
        "unique_id",
        "customer_category"
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

    $customer_category_list = $pdo->select($table_details, $where);

    if ($customer_category_list->status) {
        return $customer_category_list->data;
    } else {
        print_r($customer_category_list);
        return 0;
    }
}


//Sub Customers Function
function graduation_type($unique_id = "")
{
    global $pdo;

    $table_name    = "graduation_type";
    $where         = [];
    $table_columns = [

        "unique_id",
        "graduation_type",

    ];


    $table_details = [
        $table_name,
        $table_columns
    ];


    $where = "unique_id != ''";

    if ($unique_id) {



        $where              = [];
        $where["unique_id"] = $unique_id;
    }


    $graduation_type_list = $pdo->select($table_details, $where);

    if ($graduation_type_list->status) {
        return $graduation_type_list->data;
    } else {
        print_r($graduation_type_list);
        return 0;
    }
}


// function vehicle_type($unique_id = "")
// {
//     global $pdo;

//     $table_name    = "vehicle_type";
//     $where         = [];
//     $table_columns = [

//         "unique_id",
//         "vehicle_type",

//     ];


//     $table_details = [
//         $table_name,
//         $table_columns
//     ];


//     $where = "unique_id != ''";

//     if ($unique_id) {



//         $where              = [];
//         $where["unique_id"] = $unique_id;
//     }


//     $vehicle_type_list = $pdo->select($table_details, $where);

//     if ($vehicle_type_list->status) {
//         return $vehicle_type_list->data;
//     } else {
//         print_r($vehicle_type_list);
//         return 0;
//     }
// }
function vehicle_type($unique_id = '', $travel_type = "")
{
    global $pdo;

    $table_name    = "vehicle_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "vehicle_type",
        // "state_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($travel_type) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["travel_type"] = $travel_type;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $vehicle_type = $pdo->select($table_details, $where);

    // print_r($states);

    if ($vehicle_type->status) {
        return $vehicle_type->data;
    } else {
        print_r($vehicle_type);
        return 0;
    }
}




function grade_type($unique_id = "")
{
    global $pdo;

    $table_name    = "grade_type";
    $where         = [];
    $table_columns = [

        "unique_id",
        "grade_type",

    ];


    $table_details = [
        $table_name,
        $table_columns
    ];


    $where = "unique_id != ''";

    if ($unique_id) {
        $where              = [];
        $where["unique_id"] = $unique_id;
    }


    $grade_type_list = $pdo->select($table_details, $where);

    if ($grade_type_list->status) {
        return $grade_type_list->data;
    } else {
        print_r($grade_type_list);
        return 0;
    }
}



// City Function
function qualification($graduation_type = "")
{
    global $pdo;

    $table_name    = "qualification_details";
    $where         = [];
    $table_columns = [
        "unique_id",
        "qualification"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($graduation_type) {

        $where["graduation_type"] .= $graduation_type;
    }


    $qualification = $pdo->select($table_details, $where);

    // print_r($qualification);

    if ($qualification->status) {
        return $qualification->data;
    } else {
        print_r($qualification);
        return 0;
    }
}


// get fuel type

// function fuel_type($vehicle_type = "") {
//     global $pdo;

//     $table_name    = "expense_creation";
//     $where         = [];
//     $table_columns = [

//         "unique_id",
//         "fuel_type",

//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where     = [
//         "is_active" => 1,
//         "is_delete" => 0
//     ];
//     if ($vehicle_type) {

//         $where["vehicle_type"] .= $vehicle_type;

//     }
// if($vehicle_type == 'Two Wheeler'){
//     //  $value['fuel_type']='Two Wheeler';
//     $where["fuel_type"].= 'Petrol';
// }
// else if($vehicle_type == 'Four Wheeler'){
//     $where["fuel_type"] .= 'disel';
//     // $where["fuel_type"] .= 'petrol';
// }


//     $vehicle_type = $pdo->select($table_details,$where);

//     // print_r($vehicle_type);

//     if ($vehicle_type->status) {
//         return $vehicle_type->data;
//     } else {
//         print_r($vehicle_type);
//         return 0;
//     }
// }





function designation($grade = "")
{
    global $pdo;

    $table_name    = "designation_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "designation"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($grade) {

        $where["grade_type"] .= $grade;
    }

    $designation = $pdo->select($table_details, $where);

    // print_r($designation);

    if ($designation->status) {
        return $designation->data;
    } else {
        print_r($designation);
        return 0;
    }
}


function department($unique_id = "")
{
    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department"
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

        $where["unique_id"] .= $unique_id;
    }



    $department = $pdo->select($table_details, $where);

    // print_r($department);

    if ($department->status) {
        return $department->data;
    } else {
        print_r($department);
        return 0;
    }
}

function user_department($unique_id = "")
{
    global $pdo;

    $table_name    = "department_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "department"
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

        $where["unique_id"] .= $unique_id;
    }



    $department = $pdo->select($table_details, $where);

    // print_r($department);

    if ($department->status) {
        return $department->data;
    } else {
        print_r($department);
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
    $where = "department = '$department_type' and is_delete = 0";

    $department_type_name = $pdo->select($table_details, $where);
    /////pint_r($department_type_name);
    if ($department_type_name->status) {
        return $department_type_name->data;
    } else {
        print_r($department_type_name);
        return 0;
    }
}

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

function category_creations($unique_id = "", $department_name = "", $main_category = "") {
    global $pdo;

    $table_name = "category_creation";
    $where = "is_active = 1 and is_delete = 0";

    if ($unique_id) {
        $where .= " and unique_id = '".$unique_id."' ";
    }
    
    if ($department_name) {
        $where .= " and department = '".$department_name."' ";
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

function work_location($unique_id = '')
{
    global $pdo;

    $table_name    = "work_location_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "work_location"
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


    $work_location = $pdo->select($table_details, $where);

    //  print_r($work_location);

    if ($work_location->status) {
        return $work_location->data;
    } else {
        print_r($work_location);
        return 0;
    }
}

//Customers Sub Category Function
function customer_sub_category($unique_id = "", $customer_category_id = "")
{
    global $pdo;

    $table_name    = "customer_sub_category";
    $where         = [];
    $table_columns = [
        "unique_id",
        "customer_sub_category"
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

    if ($customer_category_id) {

        $where["customer_category_id"] = $customer_category_id;
    }

    $customer_sub_category_list = $pdo->select($table_details, $where);


    if ($customer_sub_category_list->status) {
        return $customer_sub_category_list->data;
    } else {
        print_r($customer_sub_category_list);
        return 0;
    }
}

//Customers Group Function
function customer_group($unique_id = "", $customer_category_id = "")
{
    global $pdo;

    $table_name    = "customer_group";
    $where         = [];
    $table_columns = [
        "unique_id",
        "customer_group"
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

    if ($customer_category_id) {

        $where["customer_category_id"] = $customer_category_id;
    }

    $customer_group_list = $pdo->select($table_details, $where);

    if ($customer_group_list->status) {
        return $customer_group_list->data;
    } else {
        print_r($customer_group_list);
        return 0;
    }
}


//Sub Customers Function
function sub_customer_type($unique_id = "")
{
    global $pdo;

    $table_name    = "sub_customer_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "sub_customer_type"
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
        $table_details      = $table_name;
        $where["unique_id"] = $unique_id;
    }

    $sub_customer_type_list = $pdo->select($table_details, $where);

    if ($sub_customer_type_list->status) {
        return $sub_customer_type_list->data;
    } else {
        print_r($sub_customer_type_list);
        return 0;
    }
}

// Product Category
function item_category($unique_id = "")
{
    global $pdo;

    $table_name    = "item_categorys";
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

    $item_categorys = $pdo->select($table_details, $where);

    if ($item_categorys->status) {
        return $item_categorys->data;
    } else {
        print_r($item_categorys);
        return 0;
    }
}


//Group selection according to Category
function item_group($unique_id = "", $item_category_id = "")
{
    global $pdo;

    $table_name    = "item_groups";
    $where         = [];
    $table_columns = [
        "unique_id",
        "group_name",
        "item_category_unique_id"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($item_category_id) {

        $where["item_category_unique_id"] = $item_category_id;
    }
    if ($unique_id) {

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $item_groups = $pdo->select($table_details, $where);

    if ($item_groups->status) {
        return $item_groups->data;
    } else {
        print_r($item_groups);
        return 0;
    }
}

// Units
function unit($unique_id = "")
{
    global $pdo;

    $table_name    = "units";
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

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $unit = $pdo->select($table_details, $where);

    if ($unit->status) {
        return $unit->data;
    } else {
        print_r($unit);
        return 0;
    }
}

// // Work Designation
function work_designation($unique_id = "")
{
    global $pdo;

    $table_name    = "designation_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "designation",
        // "grade_type"
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
        // $where = " WHERE country_id = '".$country_id."' ";
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $work_designations = $pdo->select($table_details, $where);

    // print_r($countries);

    if ($work_designations->status) {
        return $work_designations->data;
    } else {
        print_r($work_designations);
        return 0;
    }
}


function grade($unique_id = "")
{
    global $pdo;

    $table_name    = "grade_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "grade_type",
        // "grade_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        // "is_active" => 1,
        // "is_delete" => 0,
    ];

    if ($unique_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $grade_type = $pdo->select($table_details, $where);

    // print_r($countries);

    if ($grade_type->status) {
        return $grade_type->data;
    } else {
        print_r($grade_type);
        return 0;
    }
}


// Customer Segment Function
function customer_segment($unique_id = "")
{
    global $pdo;

    $table_name    = "customer_segment";
    $where         = [];
    $table_columns = [
        "unique_id",
        "customer_segment"
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

    $customer_segment_list = $pdo->select($table_details, $where);

    if ($customer_segment_list->status) {
        return $customer_segment_list->data;
    } else {
        print_r($customer_segment_list);
        return 0;
    }
}



// Enquiry Type Function
function enquiry_type($unique_id = "")
{
    global $pdo;

    $table_name    = "enquiry_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "enquiry_type"
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

    $enquiry_type_list = $pdo->select($table_details, $where);

    if ($enquiry_type_list->status) {
        return $enquiry_type_list->data;
    } else {
        print_r($enquiry_type_list);
        return 0;
    }
}



// BID Type Function
function bid_type($unique_id = "")
{
    global $pdo;

    $table_name    = "bid_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "bid_type"
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

    $bid_type_list = $pdo->select($table_details, $where);

    if ($bid_type_list->status) {
        return $bid_type_list->data;
    } else {
        print_r($bid_type_list);
        return 0;
    }
}

// BID Type Function
function mode_of_purchase($unique_id = "")
{
    global $pdo;

    $table_name    = "mode_of_purchase";
    $where         = [];
    $table_columns = [
        "unique_id",
        "mode_of_purchase"
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

    $mode_of_purchase_list = $pdo->select($table_details, $where);

    if ($mode_of_purchase_list->status) {
        return $mode_of_purchase_list->data;
    } else {
        print_r($mode_of_purchase_list);
        return 0;
    }
}

// BID Type Function
function item_name($unique_id = "", $item_group_unique_id = "")
{


    global $pdo;

    $table_name    = "item_names_code";
    $where         = [];
    $table_columns = [
        "unique_id",
        "CONCAT(item_code,' / ',item_name) AS item_name",
        "item_group_unique_id",
        "tax_unique_id"

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

        $table_columns = [
            "unique_id",
            "item_name",
            "item_group_unique_id",
            "tax_unique_id"


        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    if ($item_group_unique_id) {
        // $where = [];
        $where["item_group_unique_id"] = $item_group_unique_id;
    }

    $item_name_list = $pdo->select($table_details, $where);



    if ($item_name_list->status) {
        return $item_name_list->data;
    } else {
        print_r($item_name_list);
        return 0;
    }
}


function user_name_value($unique_id = "")
{
    global $pdo;

    $table_name    = "user";
    $where         = [];
    $table_columns = [
        "unique_id",
        "user_name",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    //     ""
    // ];

    $where = "is_delete = 0 and is_active = 1 and staff_unique_id = '$unique_id'";

    // if ($unique_id) {
    //     $table_details      = $table_name;
    //     $where              = [];
    //     $where["unique_id"] = $unique_id;
    // }

    // if ($unique_id) {
    //     $table_details      = $table_name;
    //     $where              = [];
    //     $where["staff_name"] = $staff_name;
    // }

    $staff_name_list = $pdo->select($table_details, $where);
// print_r($staff_name_list);
    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}
// BID Type Function
function staff_name($unique_id = "",$staff_name = "")
{
    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
        "office_contact_no",
        "designation_unique_id",
        "file_name",
        "department",
        "work_location"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    //     ""
    // ];

    $where = "is_delete = 0 and is_active = 1 and relieve_status != 'Inactive'";

    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    if ($staff_name) {
        $table_details      = $table_name;
        $where              = [];
        $where["staff_name"] = $staff_name;
    }

    $staff_name_list = $pdo->select($table_details, $where);

    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}
function staff_name_bp($employee_id = "", $staff_name = "")
{
    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [
        "employee_id",
        "staff_name",
        "work_location"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = "is_delete = 0 and is_active = 1";

    if ($employee_id) {
        $table_details        = $table_name;
        $where                = [];
        $where["employee_id"] = $employee_id;
    }

    if ($staff_name) {
        $table_details       = $table_name;
        $where               = [];
        $where["staff_name"] = $staff_name;
    }

    $staff_name_list = $pdo->select($table_details, $where);

    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}

function staff_id_bp($unique_id = "", $staff_name = "")
{
    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = "is_delete = 0 and is_active = 1";

    if ($unique_id) {
        $table_details        = $table_name;
        $where                = [];
        $where["employee_id"] = $unique_id;
    }

    if ($staff_name) {
        $table_details       = $table_name;
        $where               = [];
        $where["staff_name"] = $staff_name;
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
function staff_ceo_name($unique_id = "")
{
    global $pdo;

    $table_name    = "staff_test";
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
function staff_director_name($unique_id = "")
{
    global $pdo;

    $table_name    = "staff_test";
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




function btn_approve($folder_name = "", $unique_id = "", $approved_status = "")
{
    if ($approved_status) {
        $final_str = '<i class="mdi mdi-check btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light"></i>';
    } else {
        $final_str = '<i class=" mdi mdi-24px mdi-alert-circle-outline" style = "color :#e6f22b;"></i>';
    }

    return $final_str;
}

function btn_approve_status($folder_name = "", $unique_id = "", $approved_status = "")
{
    if ($approved_status == '0') {
        $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    } else {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }

    return $final_str;
}

function btn_bid_approve_status($folder_name = "", $unique_id = "", $approved_status = "", $approval_stage = "")
{

    $final_str = "";

    if ($approved_status == '0') {
        $final_str = '<button type="button" onclick="status_show(\'pending\',\'' . $unique_id . '\',\'' . $approval_stage . '\')" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button></a>';
    } else if ($approved_status == '1') {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    } else if ($approved_status == '2') {
        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    } else if ($approved_status == '3') {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }

    return $final_str;
}

function btn_expense_approve_status($unique_id = "", $approved_status = "", $approval_type = "")
{

    $final_str = "";

    if ($approved_status == '0') {
        $final_str = '<button type="button" onclick="expense_status_show(\'pending\',\'' . $unique_id . '\',\'' . $approval_type . '\')" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation-thick"></i></button></a>';
    } else if ($approved_status == '1') {
        $final_str = '<button type="button" onclick="expense_status_show(\'approve\',\'' . $unique_id . '\',\'\')" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    } else if ($approved_status == '2') {
        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    } else if ($approved_status == '3') {
        $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    }

    return $final_str;
}

function btn_approval($folder_name = "", $unique_id = "", $approved_status = "")
{

    if (($approved_status == 'Cancel') || ($approved_status === 2)) {

        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-window-close"></i></button></a>';
    } else if (($approved_status == 'Approved') || ($approved_status === 1)) {

        $final_str = '<button type="button"  class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-check"></i></button></a>';
    } else {
        $final_str = '<button type="button"  class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" ><i class="mdi mdi-exclamation"></i></button></a>';
    }

    return $final_str;
}

// BID Type Function
function blood_group($unique_id = "")
{
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
function moneyFormatIndia($num)
{
    $explrestunits = "";
    $amount     = explode('.', $num);
    $num         = $amount[0];
    $decimal     = 0;
    if (count($amount) == 2) {
        $decimal = $amount[1];
    }
    if (strlen($num) > 3) {
        $lastthree = substr($num, strlen($num) - 3, strlen($num));
        $restunits = substr($num, 0, strlen($num) - 3); // extracts the last three digits
        $restunits = (strlen($restunits) % 2 == 1) ? "0" . $restunits : $restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for ($i = 0; $i < sizeof($expunit); $i++) {
            // creates each of the 2's group and adds a comma to the end
            if ($i == 0) {
                $explrestunits .= (int)$expunit[$i] . ","; // if is first value , convert into integer
            } else {
                $explrestunits .= $expunit[$i] . ",";
            }
        }
        $thecash = $explrestunits . $lastthree;
    } else {
        $thecash = $num;
    }

    $decimal = number_format($decimal, 2, '.', '');
    $decimal = explode(".", $decimal)[1];

    return $thecash . "." . $decimal; // writes the final format where $currency is the currency symbol.
}


// GST Name
function tax($unique_id = "")
{
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
function account_year($unique_id = "")
{
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
function competitors($unique_id = "")
{
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

function file_upload_extention_lowercase_helper()
{
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
function supplier($unique_id = "")
{
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
function branch($unique_id = "")
{
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
        $table_columns[] = "address";
        $table_columns[] = "country";
        $table_columns[] = "state";
        $table_columns[] = "city";
        $table_columns[] = "pin_code";
        $table_columns[] = "phone_number";
        $table_columns[] = "mobile_number";
        $table_columns[] = "gst_number";
        $table_columns[] = "email_id";
        $table_columns[] = "website";
        $table_columns[] = "radius";
        $table_columns[] = "latitude";
        $table_columns[] = "longitude";
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


function sublist_insert_update($table_name = "", $data = "", $prefix = "")
{
    global $pdo;
    if ($table_name) {
        foreach ($data as $data_key => $columns) {

            $unique_id = $columns['unique_id'];

            if ($unique_id) {

                unset($columns['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table_name, $columns, $update_where);

                // Update Ends
            } else {
                $columns['unique_id'] = $prefix . unique_id();
                // Insert Begins            
                $action_obj     = $pdo->insert($table_name, $columns);
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

function sublist_delete($table_name = "", $sub_unique_ids = "", $main_unique_id = [])
{

    global $pdo;

    if ($table_name) {

        if (($sub_unique_ids) && (!empty($main_unique_id))) {

            $column_name     = array_keys($main_unique_id)[0];
            $column_value    = $main_unique_id[$column_name];

            $where           = " unique_id NOT IN (" . $sub_unique_ids . ") AND " . $column_name . "  = '" . $column_value . "'";

            $columns         = [
                "is_delete" => 1
            ];

            $update_result   = $pdo->update($table_name, $columns, $where);

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
function delivery_type($unique_id = "")
{
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
function delivery_via_type($unique_id = "")
{
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


function user_name($unique_id = "")
{

    global $pdo;

    $return_result = [
        "user_name"      => "",
        "staff_name"     => ""
    ];

    if ($unique_id) {
        $table_name    = "user";
        $where         = [];
        $table_columns = [
            "unique_id",
            "user_name",
            "(SELECT a.staff_name FROM staff_test a WHERE a.unique_id = user.staff_unique_id) AS staff_name",
            "staff_unique_id"
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

        if ($user_name_id->status) {
            return $user_name_id->data;
        } else {
            print_r($user_name_id);
            return 0;
        }
    }
}
// under user Function
function team_user($user_id = "")
{
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

    $where     = "is_delete = 0 AND is_active = 1   AND is_team_head = 0 AND user_name != '" . $user_id . "'";

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

// under user Function
function under_user($user_id = "")
{
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

    $where     = "is_delete = 0 AND is_active = 1  AND user_name != '" . $user_id . "'";

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
function under_user_type($user_type = "")
{
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

    $where     = "is_delete = 0 AND is_active = 1 AND user_type != '" . $user_type . "'";

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
function user_hierarchy($user_id = "", $user_type_id = "", $team_users = false)
{

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
                $user_data['under_user'] = '"' . implode('","', explode(",", $user_data['under_user'])) . '",';
            }

            $user_data['under_user'] .= '"' . $user_id . '"';

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
                $user_data['under_user_type'] = '"' . implode('","', explode(",", $user_data['under_user_type'])) . '",';
            }

            $user_data['under_user_type'] .= '"' . $user_type_id . '"';

            $return_result['under_user_type'] = $user_data['under_user_type'];
        } else {
            print_r($user_select);
            exit;
        }
    }

    return $return_result;
}

//datatable search
function mysql_like($search_query = "", $search_term = "")
{

    $return_result = "";

    if ($search_query) {
        switch ($search_term) {
            case "first":
                $return_result = "%" . $search_query;
                break;

            case "last":
                $return_result = $search_query . "%";
                break;

            default:
                // For All result
                $return_result = "%" . $search_query . "%";
                break;
        }
    }

    return $return_result;
}
//expense type function
function expense_type($unique_id = "")
{
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



function transport_type($unique_id = "")
{
    global $pdo;

    $table_name    = "travel_master";
    $where         = [];
    $table_columns = [
        "unique_id",
        "transport_type"

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    // ];
$where="is_delete=0 and is_active=1 group by transport_type";
    if ($unique_id) {
        $table_details      = $table_name;
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $transport_type_list = $pdo->select($table_details, $where);

    if ($transport_type_list->status) {
        return $transport_type_list->data;
    } else {
        print_r($transport_type_list);
        return 0;
    }
}

function item_unit_decimal($item_id = 0)
{

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

        $select_result  = $pdo->select($table_details, $where);

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

                $decimal_select     = $pdo->select($table_unit_details, $where_unit);

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

function item_unit($item_id = 0)
{

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

        $select_result  = $pdo->select($table_details, $where);

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

                $unit_name_select     = $pdo->select($table_unit_details, $where_unit);

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

function item_tax1($item_id = 0)
{

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

        $select_result  = $pdo->select($table_details, $where);

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

                $item_tax_value_select   = $pdo->select($table_tax_details, $where_unit);

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

function item_tax($item_id = "")
{

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

        $select_result  = $pdo->select($table_details, $where);

        print_r($select_result);
    }
}

function unit_decimal_cal($number_val = 0, $decimal = 0)
{

    $return_result  = 0;

    if ($number_val) {
        $return_result = number_format($number_val, $decimal, ".", "");
    }

    return $return_result;
}

function getIndianCurrency($number)
{

    $decimal = round($number - ($no = floor($number)), 2) * 100;
    $hundred = null;
    $digits_length = strlen($no);
    $i = 0;
    $str = array();
    $words = array(
        0 => '', 1 => 'One', 2 => 'Two',
        3 => 'Three', 4 => 'Four', 5 => 'Five', 6 => 'Six',
        7 => 'Seven', 8 => 'Eight', 9 => 'Nine',
        10 => 'Ten', 11 => 'Eleven', 12 => 'Twelve',
        13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen',
        19 => 'Nineteen', 20 => 'Twenty', 30 => 'Thirty',
        40 => 'Forty', 50 => 'Fifty', 60 => 'Sixty',
        70 => 'Seventy', 80 => 'Eighty', 90 => 'Ninety'
    );
    $digits = array('', 'Hundred', 'Thousand', 'Lakh', 'Crore');
    while ($i < $digits_length) {
        $divider = ($i == 2) ? 10 : 100;
        $number = floor($no % $divider);
        $no = floor($no / $divider);
        $i += $divider == 10 ? 1 : 2;
        if ($number) {
            $plural = (($counter = count($str)) && $number > 9) ? '' : null;
            $hundred = ($counter == 1 && $str[0]) ? ' and ' : null;
            $str[] = ($number < 21) ? $words[$number] . ' ' . $digits[$counter] . $plural . ' ' . $hundred : $words[floor($number / 10) * 10] . ' ' . $words[$number % 10] . ' ' . $digits[$counter] . $plural . ' ' . $hundred;
        } else $str[] = null;
    }
    $Rupees = implode('', array_reverse($str));
    $paise = ($decimal > 0) ? "." . ($words[$decimal / 10] . " " . $words[$decimal % 10]) . ' Paise' : '';
    // return strtoupper(($Rupees ? $Rupees . 'Rupees ' : '') . $paise. " only ");
    return (($Rupees ? $Rupees . 'Rupees ' : '') . " only ");
}

//rate contract no
function rate_contract_no($unique_id = "")
{
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
function item_brand($unique_id = "")
{
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

function technical_bid_submission($unique_id = '')
{

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

        $where                  = " acc_year = '" . $_SESSION["acc_year"] . "'";

        $tender_no               = bill_no($table_name, $where, $prefix = "TEN-", 1, 1, 0);

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
        $action_obj     = $pdo->insert($table_name, $columns);

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

            $select_obj = $pdo->select($select_table_details, $select_where);

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

                    $insert_obj = $pdo->insert($table_tender_product, $value);

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

    $team_head_result = $pdo->select($team_head_details, $team_head_where);

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
        $team_members = explode(",", $team_members);
        $team_members = "'" . implode("','", $team_members) . "'";

        $team_members_where  = "is_delete = 0 AND unique_id IN (" . $team_members . ")";

        $order_by = " is_team_head DESC";

        $team_members_result = $pdo->select($team_members_details, $team_members_where, '', '', $order_by);

        if ($team_members_result->status) {
            return $team_members_result->data;
        } else {
            print_r($team_members_result);
        }
    }

    return [];
}

function week_range($week, $year)
{

    $dates          = [];
    $time           = strtotime("1 January $year", time());
    $day            = date('w', $time);
    $time           += ((7 * $week) + 1 - $day) * 24 * 3600;
    $dates["from"]  = date('Y-n-j', $time);
    $time           += 6 * 24 * 3600;
    $dates["to"]    = date('Y-n-j', $time);

    return $dates;
}

function datatable_sorting($column = 0, $direction = "ASC", $columns_array = [])
{
    $order_by   = "";
    if (!empty($columns_array)) {
        if ($column) {

            $is_found  = strripos($columns_array[$column], " AS ");

            if ($is_found) {
                $as_column = substr($columns_array[$column], $is_found + 3);
            } else {
                $as_column = false;
            }

            if ($as_column) {
                $order_by       = $as_column . " " . $direction;
            } else {
                $order_by       = $columns_array[$column] . " " . $direction;
            }
        }
    }
    return $order_by;
}


function btn_print_1($folder_name = "",$unique_id = "", $file_name = "",$dept_type='', $main_category='', $prefix = "",$suffix = "") {
    $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print_1(event,\'folders/'.$folder_name.'/'.$file_name.'\',\''.$unique_id.'\',\''.$dept_type.'\',\''.$main_category.'\');"><i class="mdi mdi-printer"></i></button></a>';

    
    
    return $final_str;
}
function btn_print_task($folder_name = "",$unique_id = "", $file_name = "",$prefix = "",$suffix = "") {
    $final_str = '<button type="button" class="btn btn-asgreen  btn-xs btn-rounded waves-effect waves-light mr-1" onclick="new_external_window_print(event,\'folders/'.$folder_name.'/'.$file_name.'\',\''.$unique_id.'\');"><i class="mdi mdi-printer"></i></button></a>';

    
    
    return $final_str;
}


function datatable_searching($search_query = '', $columns_array = [])
{
    $search_string = "";

    if ($search_query) {
        if (!empty($columns_array)) {
            // Remove AS in Subquery in $columns_array
            $temp_arr   = [];
            foreach ($columns_array as $col_key => $col_value) {

                $is_found  = strripos($col_value, " AS ");

                if ($is_found) {
                    $as_column = substr($col_value, 0, $is_found);
                } else {
                    $as_column = $col_value;
                }
                $temp_arr[] = $as_column . " LIKE '%" . $search_query . "%' ";
            }

            unset($temp_arr[count($temp_arr) - 1]); // Unique ID Endry Disable
            unset($temp_arr[0]); // S.No Search Disable
            $search_string = implode(" OR ", $temp_arr);
        }
    }
    return $search_string;
}

//Staff Employee ID
function staff_id($unique_id = "")
{
    global $pdo;
    $table_name    = "staff_test";
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
function attendance_setting($unique_id = "")
{
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
function dashboard_menu($unique_id = "")
{

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

    $dashboard_menus = $pdo->select($table_details, $where, '', '', $order_by);

    if ($dashboard_menus->status) {
        return $dashboard_menus->data;
    } else {
        print_r($dashboard_menus);
        return 0;
    }
}

function company_name($unique_id = "")
{
    global $pdo;

    $table_name    = "company_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "company_name",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,

    ];

    if ($unique_id) {

        $table_details      = $table_name;

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $company_name_options = $pdo->select($table_details, $where);

    if ($company_name_options->status) {
        return $company_name_options->data;
    } else {
        print_r($company_name_options);
        return 0;
    }
}

function purchase_requisition_category($unique_id = "")
{
    global $pdo;

    $table_name    = "purchase_requisition_category";
    $where         = [];
    $table_columns = [
        "unique_id",
        "purchase_requisition_category",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,

    ];

    if ($unique_id) {

        $table_details      = $table_name;

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $company_name_options = $pdo->select($table_details, $where);

    if ($company_name_options->status) {
        return $company_name_options->data;
    } else {
        print_r($company_name_options);
        return 0;
    }
}

function sales_order($unique_id = "")
{
    global $pdo;

    $table_name    = "sales_order";
    $where         = [];
    $table_columns = [
        "unique_id",
        "sales_order_no",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];
    
    if($unique_id){
        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "approve_status" => 1,
            "unique_id" => $unique_id
        ];
    } else {
        $where     = [
            "is_active" => 1,
            "is_delete" => 0 ,
            "approve_status" => 1,
        ];
    }

    $company_name_options = $pdo->select($table_details, $where);

    if ($company_name_options->status) {
        return $company_name_options->data;
    } else {
        print_r($company_name_options);
        // return 0;
    }
}


function company_code($unique_id = '', $country_id = "")
{
    global $pdo;

    $table_name    = "company_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "company_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($country_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["unique_id"] = $country_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $states = $pdo->select($table_details, $where);

    // print_r($states);

    if ($states->status) {
        return $states->data;
    } else {
        print_r($states);
        return 0;
    }
}

function project_name($unique_id = "")
{
    global $pdo;

    $table_name    = "project_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "project_name",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,

    ];

    if ($unique_id) {

        $table_details      = $table_name;

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $company_name_options = $pdo->select($table_details, $where);

    if ($company_name_options->status) {
        return $company_name_options->data;
    } else {
        print_r($company_name_options);
        return 0;
    }
}
function employee_no($name = "")
{
    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [

        "employee_id",
        "unique_id",
    ];

    $where     = [
        "is_active"   => 1,
        "is_delete"   => 0
    ];

    if ($name) {
        // $where = " WHERE unique_id = '".$unique_id."' ";
        $where              = [];
        $where["unique_id"] = $name;
    }

    $table_details = [
        $table_name,
        $table_columns
    ];

    $emp_no = $pdo->select($table_details, $where);

    // print_r($branch_name);

    if ($emp_no->status) {
        return $emp_no->data;
    } else {
        print_r($emp_no);
        return 0;
    }
}




function get_staff_name($unique_id = "")
{

    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [
        "staff_name",
        "employee_id",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    //     ""
    // ];
    // and relieve_status != 'Inactive'
    $where = "is_delete = 0 and is_active = 1 and unique_id='$unique_id'";

    // if ($unique_id) {
    //     $table_details      = $table_name;
    //     $where              = [];
    //     $where["unique_id"] = $unique_id;
    // }

    $staff_name_list = $pdo->select($table_details, $where);
    // print_r($staff_name_list);
    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}
function get_active_staff_name()
{
    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [
        "unique_id",
        "staff_name",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    //     ""
    // ];
    // and relieve_status != 1

    $where = "is_delete = 0 and is_active = 1 ";

    // if ($unique_id) {
    //     $table_details      = $table_name;
    //     $where              = [];
    //     $where["unique_id"] = $unique_id;
    // }

    $staff_name_list = $pdo->select($table_details, $where);

    if ($staff_name_list->status) {
        return $staff_name_list->data;
    } else {
        print_r($staff_name_list);
        return 0;
    }
}
function company_name_option1($unique_id = "")
{
    global $pdo;

    $table_name    = "company_and_branch_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "branch_name",

        // "company_branch_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,
        "company_branch_type" => 1
    ];

    if ($unique_id) {

        $table_details      = $table_name;

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $company_name_option = $pdo->select($table_details, $where);
    // print_r($company_name_option);

    if ($company_name_option->status) {
        return $company_name_option->data;
    } else {
        print_r($company_name_option);
        return 0;
    }
}
function gender($unique_id = "")
{
    global $pdo;

    $table_name    = "staff_test";
    $where         = [];
    $table_columns = [
        "unique_id",
        "gender",
        "employee_id"
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

    $gender = $pdo->select($table_details, $where);

    if ($gender->status) {
        return $gender->data;
    } else {
        print_r($gender);
        return 0;
    }
}


// mythili 
function salary_category_name($unique_id = "")
{
    global $pdo;

    $table_name    = "salary_category";
    $where         = [];
    $table_columns = [
        "unique_id",
        "salary_category",

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

    $salary_category_list = $pdo->select($table_details, $where);

    if ($salary_category_list->status) {
        return $salary_category_list->data;
    } else {
        print_r($salary_category_list);
        return 0;
    }
}



// mythili
// function vehicle_type_cost($fuel_type = "")
// {
//     global $pdo;

//     $table_name    = "vehicle_type";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "vehicle_type",

//     ];
//     $table_details = [
//         $table_name,
//         $table_columns
//     ];


//     $where = "vehicle_type != '' group by vehicle_type ";

//     if ($fuel_type) {
//         $where              = [];
//         $where["unique_id"] = $fuel_type;
//     }


//     $vehicle_type_list = $pdo->select($table_details, $where);
    

//     if ($vehicle_type_list->status) {
//         return $vehicle_type_list->data;
//     } else {
//         print_r($vehicle_type_list);
//         return 0;
//     }
// }
// function fuel_type($vehicle_type = "", $fuel_type = "")
// {
//     global $pdo;

//     $table_name    = "fuel_type_cost_creation";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "rate"
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where = "is_delete='0' AND  vehicle_type = '" . $vehicle_type . "' AND fuel_type = '" . $fuel_type . "'";

//     $fuel_type = $pdo->select($table_details, $where);

//     if ($fuel_type->status) {
//         return $fuel_type->data;
//     } else {
//         print_r($fuel_type);
//         return 0;
//     }
// }


// 
function travel_type($unique_id = "")
{
    global $pdo;

    $table_name    = "travel_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "travel_type",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];


    $where = "unique_id != ''";

    if ($unique_id) {



        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    // print_r($where);
    $travel_type_list = $pdo->select($table_details, $where);
    // PRINT_R($fuel_list);
    if ($travel_type_list->status) {
        return $travel_type_list->data;
    } else {
        print_r($travel_type_list);
        return 0;
    }
}


function vehicle_type_cost($vehicle_type = "") {
    global $pdo;

    $table_name    = "vehicle_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        // "fuel_type",
        "vehicle_type"
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];
    
    if ($vehicle_type) {

        $where["unique_id"] = $vehicle_type;
        
    }
    // print_r($where);

    $vehicle_type_list = $pdo->select($table_details, $where);

    if ($vehicle_type_list->status) {
        return $vehicle_type_list->data;
    } else {
        print_r($vehicle_type_list);
        return 0;
    }
}

// function fuel_type($vehicle_type="") {
//     global $pdo;

//     $table_name    = "fuel_type_cost_creation";
//     $where         = [];
//     $table_columns = [
//         "unique_id",
//         "fuel_type", 
//     ];

//     $table_details = [
//         $table_name,
//         $table_columns
//     ];

//     $where     = [
//         "is_active" => 1,
//         "is_delete" => 0
//     ];
    
    
//     $where= "vehicle_type='".$vehicle_type."'  and is_delete=0";
//     // print_r($where);

//     $fuel_list = $pdo->select($table_details, $where);

//     if ($fuel_list->status) {
//         return $fuel_list->data;
//     } else {
//         print_r($fuel_list);
//         return 0;
//     }
// }

function fuel_type($unique_id = "", $vehicle_type = "")
{
    global $pdo;

    $table_name    = "fuel_type_cost_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "fuel_type",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

$where = "is_delete=0 and is_active=1 AND vehicle_type ='".$vehicle_type."' group by fuel_type order by unique_id DESC;";


    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }
// $where.=" AND group by vehicle_type";
    $fuel_list = $pdo->select($table_details, $where);
    // PRINT_R($fuel_list);
    if ($fuel_list->status) {
        return $fuel_list->data;
    } else {
        print_r($fuel_list);
        return 0;
    }
}


function type($unique_id = '')
{
    global $pdo;
// print_r($city);
    $table_name    = "cities";
    $where         = [];
    $table_columns = [
        "unique_id",
        "city_type",
        // "state_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    // ];

    // if ($city) {
        $where = " is_delete=0 and city_type != 0 and is_active=1 group by city_type";
    //     $where["city_name"] = $city;
    // }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $type = $pdo->select($table_details, $where);

    // print_r($states);

    if ($type->status) {

        $formatted_types = [];

        foreach ($type->data as $type) {
            // Convert city types to "tier-x" format and push to the array
            $formatted_types[] = [
                'value' => $type['city_type'],
                'text' => 'tier-' . $type['city_type']
            ];
        }

        return $formatted_types;
    } else {
        print_r($type);
        return 0;
    }
}


function fuel_type_cost($vehicle_type="",$fuel_type = "") {
    global $pdo;

    $table_name    = "fuel_type_cost_creation";
    $where         = [];
    $table_columns = [
        // "",
        "rate as unique_id",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];
    
    // if ($rate) {

    //     $where["rate"] .= $rate;
        
    // }
    $where= "vehicle_type='".$vehicle_type."' and unique_id='".$fuel_type."' and is_delete=0";
    // print_r($where);

    $fuel_type_list = $pdo->select($table_details, $where);
    

    if ($fuel_type_list->status) {

        // foreach ($fuel_type_list as $key => $value) {
            // $btn_edit = '';
            // $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
            // $value['rate']=  $value['unique_id'];
            // $value['unique_id']     =  $value['rate'];
        

        return $fuel_type_list->data;
    // }
    } else {
        print_r($fuel_type_list); 
        return 0;
    }

}

function rate_type_cost($rate = "") {
    global $pdo;

    $table_name    = "fuel_type_cost_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "rate",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];
    
    if ($rate) {

        $where["rate"] .= $rate;
        
    }
    // print_r($where);

    $rate_list = $pdo->select($table_details, $where);

    if ($rate_list->status) {
        return $rate_list->data;
    } else {
        print_r($rate_list);
        return 0;
    }
}

function designation_name($unique_id = "")
{
    global $pdo;

    $table_name    = "view_grade_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "designation"
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

    $designation_type = $pdo->select($table_details, $where);

    if ($designation_type->status) {
        return $designation_type->data;
    } else {
        print_r($designation_type);
        return 0;
    }
}


function grade_type_name($unique_id="",$designation="")
{
    
    global $pdo;

    $table_name    = "view_grade_type";
    $where         = [];
    $table_columns = [
        "unique_id",
        "grade"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    // $where     = [
    //     "is_active" => 1,
    //     "is_delete" => 0
    // ];
    $where= "is_delete=0 and unique_id='".$designation."'";
    
    if ($unique_id) {
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $grade = $pdo->select($table_details, $where);
    // print_R($grade);

    if ($grade->status) {
        return $grade->data;
    } else {
        print_r($grade);
        return 0;
    }
}


//suswin made functions


function btn_info($folder_name = "", $unique_id = "")
{
    $final_str = '<a href="#" onclick="' . $folder_name . '_info(\'' . $unique_id . '\')"><i class="text-blue fs-10"><i class="fe-eye"></i></a>';
    return $final_str;
}

function btn_docs($folder_name = "", $unique_id = "")
{
    $final_str = '<a href="#" onclick="' . $folder_name . '_upload(\'' . $unique_id . '\')"><i class="text-blue fs-10"><i class="fe-upload"></i></a>';
    return $final_str;
}

function fetch_grn_status($grn_number = "")
{
    global $pdo;

    $table_name    = "grn";
    $where         = [];
    $table_columns = [
        "unique_id",
        "check_status",
        "approve_status"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_delete" => 0,
        "is_active" => 1
    ];

    if ($grn_number) {
        $where              = [];
        $where["grn_number"] = $grn_number;
    }

    $grn_status = $pdo->select($table_details, $where);

    if ($grn_status->status) {
        return $grn_status->data;
    } else {
        print_r($grn_status);
        return 0;
    }
}

function fetch_grn_data($unique_id) {
    global $pdo;

    $table_name = "grn";
    $table_columns = [
        "paf",
        "freight",
        "other",
        "round",
        "gst_paf",
        "gst_freight",
        "gst_other"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = [
        "is_delete" => 0
    ];

    if ($unique_id) {
        $where = [];
        $where["unique_id"] = $unique_id;
    }

    $grn_data = $pdo->select($table_details, $where);

    if ($grn_data->status) {
        return $grn_data->data;
    } else {
        print_r($grn_data);
        return 0;
    }
}


function fetch_po_data($unique_id){
    global $pdo;

    $table_name    = "purchase_order";
    $where         = [];
    $table_columns = [
        // "net_amount",
        "freight_value",
        "freight_tax",
        "other_charges",
        "other_tax",
        "packing_forwarding",
        "packing_forwarding_tax",
        "round_off"
        // "total_gst_amount",
        // "gross_amount"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_delete" => 0
    ];

    if ($unique_id) {
        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $po_data = $pdo->select($table_details, $where);

    if ($po_data->status) {
        return $po_data->data;
    } else {
        print_r($po_data);
        return 0;
    }
}


function fetch_po_unique_id($table, $screen_unique_id)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the po_unique_id)
    $table_columns = [
        "po_unique_id"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,
        $table_columns
    ];

    // Set the WHERE condition to filter by screen_unique_id
    $where = [
        "screen_unique_id" => $screen_unique_id,
        "is_active" => 1,     // Optional: depending on your use case
        "is_delete" => 0      // Optional: depending on your use case
    ];

    // Perform the query
    $po_unique_id_options = $pdo->select($table_details, $where);

    // Check if the query was successful and if data is returned
    if ($po_unique_id_options->status && !empty($po_unique_id_options->data)) {
        // Return the first po_unique_id from the fetched data
        return $po_unique_id_options->data[0]['po_unique_id'];
    } else {
        // If no data or query failed, return 0
        return 0;
    }
}

function fetch_po_sc_unique_id($po_unique_id)
{
    global $pdo;

    $table = "purchase_order";

    // Define the columns to be fetched (in this case, the po_unique_id)
    $table_columns = [
        "screen_unique_id"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,
        $table_columns
    ];

    // Set the WHERE condition to filter by screen_unique_id
    $where = [
        "unique_id" => $po_unique_id
    ];

    // Perform the query
    $po_sc_unique_id_options = $pdo->select($table_details, $where);

    // Check if the query was successful and if data is returned
    if ($po_sc_unique_id_options->status && !empty($po_sc_unique_id_options->data)) {
        // Return the first po_unique_id from the fetched data
        return $po_sc_unique_id_options->data[0]['screen_unique_id'];
    } else {
        // If no data or query failed, return 0
        return 0;
    }
}

function fetch_grn_sc_unique_id($grn_unique_id)
{
    global $pdo;

    $table = "grn";

    // Define the columns to be fetched (in this case, the po_unique_id)
    $table_columns = [
        "screen_unique_id"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,
        $table_columns
    ];

    // Set the WHERE condition to filter by screen_unique_id
    $where = [
        "unique_id" => $grn_unique_id
    ];

    // Perform the query
    $grn_sc_unique_id_options = $pdo->select($table_details, $where);

    // Check if the query was successful and if data is returned
    if ($grn_sc_unique_id_options->status && !empty($grn_sc_unique_id_options->data)) {
        // Return the first po_unique_id from the fetched data
        return $grn_sc_unique_id_options->data[0]['screen_unique_id'];
    } else {
        // If no data or query failed, return 0
        return 0;
    }
}

function fetch_unique_id($table, $screen_unique_id)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the po_unique_id)
    $table_columns = [
        "unique_id"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,
        $table_columns
    ];

    // Set the WHERE condition to filter by screen_unique_id
    $where = [
        "screen_unique_id" => $screen_unique_id,
        "is_active" => 1,     // Optional: depending on your use case
        "is_delete" => 0      // Optional: depending on your use case
    ];

    // Perform the query
    $unique_id_options = $pdo->select($table_details, $where);

    // Check if the query was successful and if data is returned
    if ($unique_id_options->status && !empty($unique_id_options->data)) {
        // Return the first po_unique_id from the fetched data
        return $unique_id_options->data[0]['unique_id'];
    } else {
        // If no data or query failed, return 0
        return 0;
    }
}

function fetch_po_unique_id1($table, $unique_id)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the po_unique_id)
    $table_columns = [
        "po_unique_id"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,
        $table_columns
    ];

    // Set the WHERE condition to filter by screen_unique_id
    $where = [
        "unique_id" => $unique_id,
        "is_active" => 1,     // Optional: depending on your use case
        "is_delete" => 0      // Optional: depending on your use case
    ];

    // Perform the query
    $po_unique_id_options = $pdo->select($table_details, $where);

    // Check if the query was successful and if data is returned
    if ($po_unique_id_options->status && !empty($po_unique_id_options->data)) {
        // Return the first po_unique_id from the fetched data
        return $po_unique_id_options->data[0]['po_unique_id'];
    } else {
        // If no data or query failed, return 0
        return 0;
    }
}

function get_po_number($unique_id = '', $project_id = '', $company_id = '', $vendor_id = "")
{
    global $pdo;

    $table_name    = "purchase_order";
    $table_columns = [
        "unique_id",
        "purchase_order_no",
        "purchase_order_type",
        "gross_amount",
        "status",
        "lvl_2_status",
        "lvl_3_status"
    ];

    $where = ["is_delete" => 0];

    if ($unique_id) {
        $where['unique_id'] = $unique_id;
    }
    if ($company_id) {
        $where['company_id'] = $company_id;
    }
    if ($project_id) {
        $where['project_id'] = $project_id;
    }
    if ($vendor_id) {
        $where['supplier_id'] = $vendor_id;
    }

    $table_details = [$table_name, $table_columns];
    $states = $pdo->select($table_details, $where);

    if ($states->status) {
        // filter only valid POs
        $filtered = array_filter($states->data, function ($po) {
            return (
                ($po['gross_amount'] < 300000 && $po['status'] == 1) ||
                ($po['gross_amount'] >= 300000 && $po['gross_amount'] < 1000000 && $po['lvl_2_status'] == 1) ||
                ($po['gross_amount'] >= 1000000 && $po['lvl_3_status'] == 1)
            );
        });

        // return only those that passed
        return array_values($filtered); // reindex keys
    } else {
        return [
            'error'   => true,
            'details' => $states
        ];
    }
}


function get_po_screen_unique_id($po_number = '')
{
    global $pdo;

    $table_name = "purchase_order";
    $table_columns = ["screen_unique_id"];
    
    $where = [
        "lvl_2_status" => 1,
        "is_delete" => 0
    ];
    
    // If PO number is provided, add it to the WHERE clause
    if (!empty($po_number)) {
        $where['po_number'] = $po_number;
    }

    $table_details = [$table_name, $table_columns];
    $result = $pdo->select($table_details, $where);

    if ($result->status && !empty($result->data)) {
        return $result->data[0]['screen_unique_id'];
    }
    
    return 0;
}

function get_po_number_grn($screen_unique_id = '')
{
    global $pdo;

    $table_name = "grn";
    $table_columns = ["po_number"]; // Changed to select po_number
    
    $where = [
        "screen_unique_id" => $screen_unique_id,
        "is_active" => 1,
        "is_delete" => 0
    ];

    $table_details = [$table_name, $table_columns];
    $result = $pdo->select($table_details, $where);

    if ($result->status && !empty($result->data)) {
        return $result->data[0]['po_number'];
    }
    
    return 0;
}


function doc_type_options($unique_id = "") {
    $table_name = "doc_types";
    $table_columns = [
        "unique_id",
        "name"
    ];
    $table_details = [
        $table_name,
        $table_columns
    ];
    global $pdo;

    $where = [];
    if ($unique_id) {
        $where["unique_id"] = $unique_id;
    }

    $result = $pdo->select($table_details, $where);

    if ($result->status) {
        return $result->data;
    } else {
        print_r($result);
        return [];
    }
}

function doc_option_insert($name) {
    global $pdo;

    $table_name = "doc_types";
    $prefix = "doc_";

    // Check if the name already exists (case-insensitive)
    $check_details = [
        $table_name,
        ["unique_id"]
    ];
    // Use a raw WHERE clause for case-insensitive match
    $check_where = "LOWER(name) = '" . strtolower($name) . "'";

    $exists = $pdo->select($check_details, $check_where);

    if ($exists->status && !empty($exists->data)) {
        // Name already exists
        return false;
    }

    $unique_id = $prefix . unique_id();
    $data = [
        "unique_id" => $unique_id,
        "name" => $name
    ];

    $result = $pdo->insert($table_name, $data);
    error_log("doc_option_insert: " . print_r($result, true) . "\n", 3, "folder/supplier/error.txt");

    if (isset($result->status) && $result->status) {
        return true;
    } else {
        if (isset($result->error)) {
            error_log(print_r($result->error, true));
        }
        return false;
    }
}

function get_work_location($unique_id = '', $company_id = "")
{
    global $pdo;

    $table_name    = "work_location_creation";
    $where         = [];
    $table_columns = [
        "unique_id",
        "work_location",
       
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($company_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["company_name"] = $company_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $work_location = $pdo->select($table_details, $where);

    // print_r($states);

    if ($work_location->status) {
        return $work_location->data;
    } else {
        print_r($work_location);
        return 0;
    }
}

function get_project_so($unique_id = '')
{
    global $pdo;

    $table_name    = "project_creation";
    $where         = [];
    $table_columns = [
        "sales_order_id"
        // "state_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,
        "unique_id" => $unique_id
    ];

    $states = $pdo->select($table_details, $where);

    // print_r($states);

    if ($states->status) {
        return $states->data;
    } else {
        print_r($states);
        return 0;
    }
}

function lwf_value($project_id = ""){
    global $pdo;
    
    $table = "lwf_entry";
    
    $table_columns = [
        "amount"
    ];
    
    $table_details = [
        $table,
        $table_columns
    ];
    
    $where = [
        "project_id" => $project_id,
        "is_active"  => 1,
        "is_delete"  => 0
    ];
    
    $result = $pdo->select($table_details, $where);
    
    if($result->status){
        return $result->data;
    }else{
        return 0;
    }
    
}

function fetch_project($unique_id = ""){
    
    global $pdo;
    
    $table = "staff_test";
    
    $table_columns = [
        "work_location"  
    ];
    
    $table_details = [
        $table,
        $table_columns
    ];
    
    $where = [
        "unique_id" => $unique_id  
    ];
    
    $result = $pdo->select($table_details, $where);
    
    if($result->status){
        return $result->data;
    } else {
        return 0;
    }
    
}

function product_group_name($id = '')
{
    global $pdo;

    $table_name    = "product_vertical";
    $where         = [];
    $table_columns = [
        "id",
        "product_vertical"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    if ($id) {
        $table_details = $table_name;
        $where         = [
            "id" => $id
        ];
    }

    $group = $pdo->select($table_details, $where);

    // print_r($group);

    if ($group->status) {
        return $group->data;
    } else {
        // print_r($group);
        return 0;
    }
}

function product_type_name($id = '', $vertical_id = "")
{
    global $pdo;

    $table_name    = "product_type";
    $where         = [];
    $table_columns = [
        "id",
        "product_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];


    if ($vertical_id) {
        $where["vertical_id"] = $vertical_id;
    }

    if ($id) {
        $table_details = $table_name;
        $where         = [
            "id" => $id
        ];
    }

    $group = $pdo->select($table_details, $where);

     //print_r($group);

    if ($group->status) {
        return $group->data;
    } else {
        // print_r($group);
        return 0;
    }
}

function btn_views($folder_name = "", $unique_id = "")
{
    $url = 'folders/' . $folder_name . '/view.php?unique_id=' . $unique_id;
    return '<a href="javascript:void(0);" 
             onclick="window.open(\'' . $url . '\', \'viewWindow\', \'width=1200,height=800,scrollbars=yes,resizable=yes\');" 
             class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i> View
            </a>';
}

function btn_prints($folder_name = "", $unique_id = "")
{
    $url = 'folders/' . $folder_name . '/print.php?unique_id=' . $unique_id;
    return '<a href="javascript:void(0);" 
             onclick="window.open(\'' . $url . '\', \'printWindow\', \'width=1200,height=800,scrollbars=yes,resizable=yes\');" 
             class="btn btn-secondary btn-sm">
                <i class="fa fa-print"></i> Print
            </a>';
}

// function btn_views($folder_name = "", $unique_id = "")
// {
//     $function_name = "view_" . $folder_name;
//     return '<a href="javascript:void(0);" onclick="' . $function_name . '(\'' . $unique_id . '\')" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> View</a>';
// }

// function btn_prints($folder_name = "", $unique_id = "")
// {
//     $function_name = "print_" . $folder_name;
//     return '<a href="javascript:void(0);" onclick="' . $function_name . '(\'' . $unique_id . '\')" class="btn btn-secondary btn-sm"><i class="fa fa-print"></i> Print</a>';
// }

function isSameStateGST($gstin1, $gstin2) {
    // Validate GSTIN length
    if (strlen($gstin1) < 2 || strlen($gstin2) < 2) {
        return false; // Invalid GSTIN format
    }

    // Extract first two digits (state codes)
    $stateCode1 = substr($gstin1, 0, 2);
    $stateCode2 = substr($gstin2, 0, 2);

    // Compare and return result
    return $stateCode1 === $stateCode2;
}

// Semi-Finished Items (from item_master)
function product_name_semi_finished($unique_id = '', $group_id = '', $sub_group_id = '', $company_id = '')
{
    global $pdo;

    // 1) Get the unique_id of the "SEMI-FINISHED" category
    $cat_table_details = [
        'category_master',
        ['unique_id']
    ];
    $cat_where = [
        'category_name' => 'FABRICATION',
        'is_active'     => 1,
        'is_delete'     => 0
    ];
    $cat_res = $pdo->select($cat_table_details, $cat_where, "", "LIMIT 1");
    if (!$cat_res->status || empty($cat_res->data)) {
        return []; // category not found / inactive
    }
    $semi_finished_cat_uid = $cat_res->data[0]['unique_id'];

    // 2) Build query for item_master
    $table_name    = "item_master";
    $table_columns = [
        "unique_id",
        "item_name"
    ];
    $table_details = [$table_name, $table_columns];

    $where = [
        "category_unique_id" => $semi_finished_cat_uid,
        "is_active"          => 1,
        "is_delete"          => 0
    ];

    if ($unique_id) {
        // when fetching a single record by unique_id, your pattern uses table name directly
        $table_details = $table_name;
        $where["unique_id"] = $unique_id;
    }

    if ($group_id) {
        $where["group_unique_id"] = $group_id;
    }

    if ($sub_group_id) {
        $where["sub_group_unique_id"] = $sub_group_id;
    }

    if ($company_id) {
        // item_master doesn’t have company_id; it has sess_company_id in your schema
        $where["sess_company_id"] = $company_id;
    }

    $items = $pdo->select($table_details, $where, "", "ORDER BY item_name ASC");

    if ($items->status) {
        return $items->data; // array of rows with unique_id, item_name
    } else {
        // print_r($items); // uncomment for debugging
        return [];
    }
}

function btn_views_dev($folder_name = "", $unique_id = "", $type = "")
{
    $url = 'index.php?file=' . $folder_name . '/view&unique_id=' . $unique_id . '&date=&form=';
    
    if (!empty($type)) {
        $url .= '&type=' . urlencode($type);
    }
    return '<a href="javascript:void(0);" 
             onclick="window.open(\'' . $url . '\', \'viewWindow\', \'width=1200,height=800,scrollbars=yes,resizable=yes\');" 
             class="btn btn-info btn-sm">
                <i class="fa fa-eye"></i> View
            </a>';
}

// Supplier Function
function po_supplier($unique_id = "", $project_id = "")
{
    global $pdo;

    $table_name    = "purchase_order";
    $table_columns = ["supplier_id"];
    $table_details = [$table_name, $table_columns];

    $where = ["is_delete" => 0];

    if ($project_id) {
        $where["project_id"] = $project_id;
    }
    
    if ($unique_id) {
        $where["unique_id"] = $unique_id;
    }

    $supplier_result = $pdo->select($table_details, $where);

    if ($supplier_result->status && count($supplier_result->data) > 0) {
        $suppliers = [];

        foreach ($supplier_result->data as $row) {
            $su_id = $row['supplier_id'];

            if ($su_id) {
                $supplier_data = supplier($su_id);

                if ($supplier_data && is_array($supplier_data)) {
                    foreach ($supplier_data as $sup) {
                        $suppliers[$sup['unique_id']] = [
                            "unique_id"     => $sup['unique_id'],
                            "supplier_name" => $sup['supplier_name']
                        ];
                    }
                }
            }
        }

        // return only values (remove associative keys)
        return array_values($suppliers);
    } else {
        return [];
    }
}

function cost_center_project($project_id)
{
    global $pdo;
    
    $table = "project_creation";
    
    $columns = ["cost_center"];
    
    $table_details = [$table, $columns];
    
    $where = [
        "is_active" => 1,
        "is_delete" => 0
    ];
    
    if($project_id){
        $where['unique_id'] = $project_id; 
    }
    
    $result = $pdo->select($table_details, $where);
    
    if($result -> status){
        return $result->data[0]['cost_center'];
    }else{
        return 0;   
    }
}

function obom_check($so_unique_id)
{
    global $pdo;

    $columns = ["so_unique_id"];

    $table_details = [
        "obom_list",
        $columns
    ];

    $where = [
        "is_active"    => 1,
        "is_delete"    => 0,
        "so_unique_id" => $so_unique_id
    ];

    $states = $pdo->select($table_details, $where);

    if ($states->status && !empty($states->data)) {
        return 1; // record exists
    } else {
        return 0; // no record found
    }
}

function fetch_pr_sub_uid($po_sub_id = ""){
    global $pdo;
    
    $table = "purchase_order_items";
    
    $columns = [
        "pr_sub_unique_id"  
    ];
    
    $where = ["screen_unique_id" => $po_sub_id];
    
    $result = $pdo->select([$table, $columns], $where);
    
    if($result->status){
        return $result->data[0]['pr_sub_unique_id'];
    } else {
        return 0;
    }
}

function fetch_pr_main_uid($po_sub_id = ""){
    global $pdo;
    
    $table = "purchase_requisition_items";
    
    $columns = [
        "main_unique_id"  
    ];
    
    $where = ["unique_id" => $po_sub_id];
    
    $result = $pdo->select([$table, $columns], $where);
    
    if($result->status){
        return $result->data[0]['main_unique_id'];
    } else {
        return 0;
    }
}

function fetch_pr_so_uid($po_main_id = ""){
    global $pdo;
    
    $table = "purchase_requisition";
    
    $columns = [
        "sales_order_id"  
    ];
    
    $where = ["unique_id" => $po_main_id];
    
    $result = $pdo->select([$table, $columns], $where);
    
    if($result->status){
        return $result->data[0]['sales_order_id'];
    } else {
        return 0;
    }
}

function btn_edit_child($folder_name = "", $unique_id = "")
{
    $final_str = '<button type="button" class="btn btn-asgreen btn-xs btn-rounded waves-effect waves-light " onclick="' . $folder_name . '_edit_child(\'' . $unique_id . '\')"><i class="mdi mdi-square-edit-outline"></i></button>';

    $final_str = '<a href="#" onclick="' . $folder_name . '_edit_child(\'' . $unique_id . '\')"><i class="mdi mdi-square-edit-outline  mdi-24px waves-effect waves-light mt-n2 mb-n2 text-success"></i></a>';

    return $final_str;
}

function btn_delete_child($folder_name = "", $unique_id = "")
{
    // $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')"><i class="mdi mdi-delete"></i></button>';

    // $final_str = '<button type="button" class="btn btn-danger  btn-xs btn-rounded waves-effect waves-light" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')"><i class="mdi mdi-delete"></i></button>';

    // $final_str = '<a href="#" onclick="'.$folder_name.'_delete(\''.$unique_id.'\')"><i class="mdi mdi-delete mdi-24px waves-effect waves-light text-danger"></a>';

    $final_str = '<a href="#" onclick="' . $folder_name . '_delete_child(\'' . $unique_id . '\')"><i class="text-danger fs-10"><i class="fe-trash-2"></i></a>';

    return $final_str;
}

function sales_order_type($unique_id = "", $type = "")
{
    global $pdo;

    $table_name    = "sales_order";
    $where         = [];
    $table_columns = [
        "unique_id",
        "sales_order_no",
        "so_type"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];
    
    if ($unique_id) {
        $where = [
            "is_active"      => 1,
            "is_delete"      => 0,
            "approve_status" => 1,
            "unique_id"      => $unique_id
        ];
    } else {
        $where = [
            "is_active"      => 1,
            "is_delete"      => 0,
            "approve_status" => 1,
        ];
    }

    // fetch all SOs
    $company_name_options = $pdo->select($table_details, $where);

    if (!$company_name_options->status) {
        // print_r($company_name_options);
        return 0;
    }

    $results = $company_name_options->data;

    // filter with obom_list if type is given
    if (!empty($type)) {
        $obom_query = $pdo->select(
            ["obom_list", ["so_unique_id"]],
            ["type" => $type, "to_list" => 1]
        );

        if ($obom_query->status && !empty($obom_query->data)) {
            $used_so_ids = array_column($obom_query->data, "so_unique_id");
            // remove matching SOs
            $results = array_filter($results, function ($row) use ($used_so_ids) {
                return !in_array($row["unique_id"], $used_so_ids);
            });
            // re-index
            $results = array_values($results);
        }
    }

    return $results;
}

// Supplier Function
function supplier_data($unique_id = "")
{
    global $pdo;

    $table_name    = "supplier_profile";
    $where         = [];
    $table_columns = [
        '*'
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

function supplier_contact_data($unique_id = "")
{
    global $pdo;

    $table_name    = "supplier_contact_person";
    $where         = [];
    $table_columns = [
        '*'
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
        $where["supplier_profile_unique_id"] = $unique_id;
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





// Customer Function
function customer_data($unique_id = "")
{
    global $pdo;

    $table_name    = "customer_profile";
    $where         = [];
    $table_columns = [
        '*'
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

function customer_contact_data($unique_id = "")
{
    global $pdo;

    $table_name    = "customer_contact_person";
    $where         = [];
    $table_columns = [
        '*'
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
        $where["customer_profile_unique_id"] = $unique_id;
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

function company_data($unique_id = "")
{
    global $pdo;

    $table_name    = "company_creation";
    $where         = [];
    $table_columns = [
       "*"

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0,

    ];

    if ($unique_id) {

        $table_details      = $table_name;

        $where              = [];
        $where["unique_id"] = $unique_id;
    }

    $company_name_options = $pdo->select($table_details, $where);

    if ($company_name_options->status) {
        return $company_name_options->data;
    } else {
        print_r($company_name_options);
        return 0;
    }
}

function get_project_by_type($type = '', $company_id = "")
{
    global $pdo;

    $table_name    = "dailylogsheet_master";
    $table_columns = ["project_name"]; // contains project unique_id from project_creation

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($company_id) {
        $where["company_name"] = $company_id;
    }

    if ($type) {
        $where["type"] = strtolower($type);
    }

    $projects = $pdo->select($table_details, $where);

    if ($projects->status && !empty($projects->data)) {
        $formatted = [];

        foreach ($projects->data as $row) {
            $project_unique_id = $row["project_name"]; // actually project_creation.unique_id
            $project_info = get_project_name($project_unique_id); // your existing function

            if ($project_info && is_array($project_info)) {
                // get_project_name() returns an array, take the first element
                $formatted[] = $project_info[0];
            }
        }

        return $formatted;
    } else {
        return 0;
    }
}

function get_application_type_by_project($project_id = '', $company_id = "")
{
    global $pdo;

    $table_name    = "integrated_dailylogsheet_master";
    $table_columns = ["application_type"];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($company_id) {
        $where["company_name"] = $company_id;
    }

    if ($project_id) {
        $where["project_name"] = $project_id; // here project_name holds project unique_id
    }

    $result = $pdo->select($table_details, $where);

    if ($result->status && !empty($result->data)) {
        // return first record's application_type
        return $result->data[0]["application_type"];
    } else {
        return null; // not found
    }
}

function has_grn_or_srn($unique_id) {
    global $pdo;

    // Define the two tables to check
    $tables = ['grn', 'srn'];

    foreach ($tables as $tbl) {
        $table_details = [
            $tbl,
            ["COUNT(*) AS cnt"]
        ];

        $where = "is_delete = 0 AND po_number = '$unique_id'";

        $result = $pdo->select($table_details, $where);

        if ($result->status && !empty($result->data) && $result->data[0]['cnt'] > 0) {
            return true; // found at least one match
        }
    }

    return false; // not found in either GRN or SRN
}
