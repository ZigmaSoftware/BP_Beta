<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "follow_up_call";
$table_sub         = "follow_up_call_sublist ";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "sql"       => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$country_name       = "";
$state_name         = "";
$prefix             = "flo";
$call_prefix        = "CID-";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $unique_id          = $_POST['unique_id'];

        $update_where       = "";

        $columns            = [
            "follow_up_call_id"     => "afjlsdf",
            "follow_up_date"        => today(),
            "executive_id"          => $_SESSION['user_id'],
            "location_id"           => $_SESSION['sess_user_location'],
            "call_type_id"          => $_POST["call_type"],
            "customer_id"           => $_POST["customer_id"],
            "mode"                  => $_POST["mode"],
            "action_val"            => $_POST["follow_up_action_type"],
            "unique_id"             => $main_unique = unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'call_type_id = "'.$_POST['call_type'].'" AND customer_id = "'.$_POST["customer_id"].'" AND follow_up_date="'.today().'" AND is_delete = 0  ';

        // When Update Check without current id
        if ($unique_id) {

            $select_where   .= '  AND follow_up_call_id = "'.$_POST['call_id'].'" AND unique_id !="'.$unique_id.'" ';

            // Call Id When Update
            $follow_up_call_id = $_POST['call_id'];

        } else {
            $bill_no_where   = [
                "acc_year"      => $_SESSION['acc_year']
            ];

            // GET Bill No
            $follow_up_call_id             = bill_no($table,$bill_no_where,$call_prefix);
            $columns['follow_up_call_id']  = $follow_up_call_id;
            // echo $follow_up_call_id;
        }

        $select_obj         = $pdo->select($table_details,$select_where);

        if ($select_obj->status) {
            $status     = $select_obj->status;
            $data       = $select_obj->data;
            $error      = "";
            $sql        = $select_obj->sql;

        } else {
            $status     = $select_obj->status;
            $data       = $select_obj->data;
            $error      = $select_obj->error;
            $sql        = $select_obj->sql;
            $msg        = "error";
        }

        // Check If already Exist
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            if ($unique_id) {
                
                // Update Begins
                unset($columns['unique_id']);
                unset($columns['follow_up_call_id']);

                // Storing unique id in main Unique is purpose to add main id in sublist
                $main_unique        = $unique_id;

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table,$columns,$update_where);
                // Update Ends


            } else {

                // Insert Begins            
                $action_obj     = $pdo->insert($table,$columns);
                // Insert Ends

            }

            // print_r($action_obj);

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                // Sublist Insert & Update 
                if($unique_id) {
                    
                    // Sublist Update
                    $sub_update_where   = [
                        "follow_up_call_unique_id"     => $unique_id,
                        "prev_follow_up_unique_id"     => 'new'
                    ];

                    if ($_POST["follow_up_action_type"]) {
                        $columns_sub = [
                            "status"                    => $_POST["cur_status"],
                            "remark"                    => $_POST["remark"],
                            "entry_date"                => $today,
                            "next_follow_up_days"       => $_POST['next_follow_up_days'],
                            "next_follow_up_date"       => $_POST['next_follow_up_date'],
                            "call_status"               => "",
                            "close_remark"              => "",
                            "close_date"                => "",
                            "is_updated"                => 0
                        ];
                    } else {
                        $columns_sub = [
                            "status"                    => $_POST["cur_status"],
                            "remark"                    => $_POST["remark"],
                            "entry_date"                => $today,
                            "next_follow_up_days"       => "",
                            "next_follow_up_date"       => "",
                            "call_status"               => $_POST['call_status'],
                            "close_remark"              => $_POST['close_remark'],
                            "close_date"                => $_POST['close_date'],
                            "is_updated"                => 3,
                            "updated_date"              => $today
                        ];
                    }

                    // Sublist Update Begins
                    $sub_action_obj     = $pdo->update($table_sub,$columns_sub,$sub_update_where);

                } else {

                    // Sublist Insert Begins
                    if ($_POST["follow_up_action_type"]) {
                        $columns_sub = [
                            "follow_up_call_id"         => $follow_up_call_id,
                            "follow_up_call_unique_id"  => $main_unique,
                            "entry_date"                => $today,
                            "status"                    => $_POST["cur_status"],
                            "remark"                    => $_POST["remark"],
                            "next_follow_up_days"       => $_POST['next_follow_up_days'],
                            "next_follow_up_date"       => $_POST['next_follow_up_date'],
                            "is_updated"                => 0,
                            "updated_date"              => "",
                            "unique_id"                 => unique_id()
                        ];
                    } else {
                        $columns_sub = [
                            "follow_up_call_id"        => $follow_up_call_id,
                            "follow_up_call_unique_id" => $main_unique,
                            "entry_date"                => $today,
                            "status"                   => $_POST["cur_status"],
                            "remark"                   => $_POST["remark"],
                            "call_status"              => $_POST['call_status'],
                            "close_remark"             => $_POST['close_remark'],
                            "close_date"               => $_POST['close_date'],
                            "is_updated"               => 3,
                            "updated_date"             => $today,
                            "unique_id"                => unique_id()
                        ];
                    }

                    $sub_action_obj     = $pdo->insert($table_sub,$columns_sub);
                    // Sublist Insert End
                }

                // Check Sub List Status
                if ($sub_action_obj->status) {
                    if ($unique_id) {
                        $msg = "update";
                    } else {
                        $msg = "create";
                    }
                } else {
                    $status     = $sub_action_obj->status;
                    $data       = $sub_action_obj->data;
                    $error      = $sub_action_obj->error;
                    $sql        = $sub_action_obj->sql;
                    $msg        = "error";
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

    case 'sub_add_update':

        // Sub List Add Update
        $unique_id              = $_POST['unique_id'];
        $follow_up_call_id      = $_POST['call_id'];
        $call_unique_id         = $_POST['call_unique_id'];
        $sub_unique_id          = $_POST['sub_unique_id'];

        // check already Exist Or not
        $table_details      = [
            $table_sub,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'unique_id = "'.$_POST['sub_unique_id'].'" AND is_updated = 1 ';

        // When Update Check without current id
        if ($unique_id) {

            $select_where   .= ' AND unique_id !="'.$_POST['sub_unique_id'].'" ';        
        } 

        $select_obj         = $pdo->select($table_details,$select_where);

        // print_r($select_obj);

        if ($select_obj->status) {
            $status     = $select_obj->status;
            $data       = $select_obj->data;
            $error      = "";
            $sql        = $select_obj->sql;

        } else {
            $status     = $select_obj->status;
            $data       = $select_obj->data;
            $error      = $select_obj->error;
            $sql        = $select_obj->sql;
            $msg        = "error";
        }

        // Check If already Exist
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {

            if ($unique_id) {

                $sub_update_where = [
                    "unique_id" => $unique_id
                ];

                // Update Begins Here
                if ($_POST["follow_up_action_type"]) {
                    $columns_sub = [
                        "status"                    => $_POST["cur_status"],
                        "remark"                    => $_POST["remark"],
                        "next_follow_up_days"       => $_POST['next_follow_up_days'],
                        "next_follow_up_date"       => $_POST['next_follow_up_date'],
                        "call_status"               => "",
                        "close_remark"              => "",
                        "close_date"                => "",
                        "is_updated"                => 0
                    ];
                } else {
                    $columns_sub = [
                        "status"                    => $_POST["cur_status"],
                        "remark"                    => $_POST["remark"],
                        "prev_follow_up_unique_id"  => $_POST["sub_unique_id"],
                        "next_follow_up_days"       => "",
                        "next_follow_up_date"       => "",
                        "call_status"               => $_POST['call_status'],
                        "close_remark"              => $_POST['close_remark'],
                        "close_date"                => $_POST['close_date'],
                        "is_updated"                => 3,
                        "updated_date"              => $today  
                    ];
                }

                $sub_action_obj     = $pdo->update($table_sub,$columns_sub,$sub_update_where);
                // Update Ends Here

            } else {

                $followup_update_status = 0;

                /**
                 * $followup_update_status value has only following anyone
                 * 0 - follow up pending
                 * 1 - updated
                 * 2 - closed
                 * 3 - closed entry
                 */
                
                // Insert Begins Here
                if ($_POST["follow_up_action_type"]) {
                    $columns_sub = [
                        "follow_up_call_id"         => $follow_up_call_id,
                        "follow_up_call_unique_id"  => $call_unique_id,
                        "entry_date"                => $today,
                        "status"                    => $_POST["cur_status"],
                        "remark"                    => $_POST["remark"],
                        "prev_follow_up_unique_id"  => $sub_unique_id,
                        "next_follow_up_days"       => $_POST['next_follow_up_days'],
                        "is_updated"                => 0,
                        "next_follow_up_date"       => $_POST['next_follow_up_date'],
                        "unique_id"                 => unique_id()
                    ];

                    $followup_update_status = 1;

                } else {
                    $columns_sub = [
                        "follow_up_call_id"        => $follow_up_call_id,
                        "follow_up_call_unique_id" => $call_unique_id,
                        "entry_date"               => $today,
                        "status"                   => $_POST["cur_status"],
                        "remark"                   => $_POST["remark"],
                        "prev_follow_up_unique_id" => $sub_unique_id,
                        "call_status"              => $_POST['call_status'],
                        "close_remark"             => $_POST['close_remark'],
                        "close_date"               => $_POST['close_date'],
                        "is_updated"               => 3,
                        "updated_date"             => $today,
                        "unique_id"                => unique_id()
                    ];

                    $followup_update_status = 2;
                }
                
                // Update Status on previous Call
                $prev_update_where = [
                    "unique_id" => $sub_unique_id
                ];

                $prev_columns      = [
                    "updated_date" => $today,
                    "is_updated"   => $followup_update_status
                ];

                $pre_action_obj = $pdo->update($table_sub,$prev_columns,$prev_update_where);

                $sub_action_obj     = $pdo->insert($table_sub,$columns_sub);
                // Insert Ends Here
            }

            // Check Sub List Status
            if ($sub_action_obj->status) {

                $msg = "update";

            } else {
                $status     = $sub_action_obj->status;
                $data       = $sub_action_obj->data;
                $error      = $sub_action_obj->error;
                $sql        = $sub_action_obj->sql;
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

    case 'datatable':
        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
        $limit 		= $length;


        // List Display Count 

        $count_array   = [
            0 => [
            "new_calls"     => 0,
            "follow_ups"    => 0,
            "updated"       => 0,
            "closed"        => 0
            ]
        ];
        
		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@c:=@c+1 s_no",
            "a.follow_up_call_id",
            "b.customer_id",
            "b.follow_up_date",
            "a.next_follow_up_date",
            "(SELECT call_type FROM call_type WHERE call_type.unique_id = b.call_type_id) AS call_type",
            "b.mode",
            "a.status",
            "a.remark",
            "b.unique_id",
            "a.unique_id AS sub_unique_id"
        ];


        
        $table_join     = $table_sub." AS a LEFT JOIN follow_up_call AS b ON a.follow_up_call_unique_id = b.unique_id ";
        
        $table_details  = [
            $table_join." JOIN (SELECT @c:= ".$start.") AS c ",
            $columns
        ];

        $where = "";
        
        $order_by       = "";
        $group_by       = " a.follow_up_call_id";
        
        // Custom Filter
        switch ($_POST['filter_action']) {
            case 'new_calls':
                $where          = ' a.entry_date BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'" AND a.is_active = 1 AND a.is_delete = 0 ';
                $where .= ' AND a.prev_follow_up_unique_id = "new"';
                break;

            case 'follow_ups':
                $where          = ' a.next_follow_up_date <= "'.$_POST['to_date'].'" AND a.is_active = 1 AND a.is_delete = 0 ';
                
                $where          .= ' AND a.is_updated = 0 ';
                break;

            case 'updated':
                $where          = ' a.next_follow_up_date BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'" AND a.is_active = 1 AND a.is_delete = 0 ';
                $where .= ' AND a.is_updated = 1 ';
                break;

            case 'closed':
                $where          = ' a.entry_date BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'" AND a.is_active = 1 AND a.is_delete = 0 ';
                $where .= ' AND a.is_updated = 3 ';
                break;
            
            default:
                $where          = ' a.next_follow_up_date <= "'.$_POST['to_date'].'" AND a.is_active = 1 AND a.is_delete = 0 ';

                $where .= ' AND a.prev_follow_up_unique_id = "new" AND a.is_updated = 0 ';
                break;
        }

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function,$group_by);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $value['follow_up_date']        = disdate($value['follow_up_date']);
                $value['next_follow_up_date']   = disdate($value['next_follow_up_date']);

                
                // Customer Details
                $customer_details               = customers ($value['customer_id']);

                $customer_name                  = disname($customer_details[0]['customer_name']);

                if ($value['next_follow_up_date'] == "00-00-0000") {
                    $value['next_follow_up_date'] = '<span class="text-danger">Closed</span>';
                }
                
                $value['customer_id']           = $customer_name ;

                if (($value['follow_up_date'] == disdate($today))) {

                    $btn_update                 = btn_update($folder_name,$value['unique_id']);
                    $btn_delete                 = btn_delete($folder_name,$value['unique_id']."','".$value['sub_unique_id']);
                    
                } else {
                    
                    $btn_update                 = btn_update($folder_name,$value['unique_id']."&sub_unique_id=".$value['sub_unique_id'].'&table_action='.$_POST['filter_action']);
                    $btn_delete                 = "";

                }

                $value['unique_id']             = $btn_update.$btn_delete;
                
                unset($value['sub_unique_id']);

                $data[]                         = array_values($value);
            }

            // Count Related Queries
            $count_sql          = "SELECT
                                        *                                        
                                    FROM
                                        (
                                        SELECT
                                            (
                                            SELECT
                                                COUNT(unique_id)
                                            FROM
                                                follow_up_call_sublist
                                            WHERE
                                                entry_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0 AND prev_follow_up_unique_id = 'new' 
                                        ) AS new_calls,
                                        (
                                        SELECT
                                            COUNT(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            next_follow_up_date <= '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 0
                                    ) AS follow_ups,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            updated_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 1
                                    ) AS updated,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            entry_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 3
                                    ) AS closed
                                    ) AS count_table";

            $count_result       = $pdo->query($count_sql);

            if ($count_result->status) {

                $count_array      = $count_result->data;
            
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "count"             => $count_array,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case 'follow_up_call_sub_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "follow_up_call";


        // DataTable Variables
        $search 	= $_POST['search']['value'];
        $length 	= $_POST['length'];
        $start 		= $_POST['start'];
        $draw 		= $_POST['draw'];
        $limit 		= $length;
        
        $data	    = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";

        $columns        = [
            "@c:=@c+1 s_no",
            "a.entry_date",
            "a.next_follow_up_date",
            "a.follow_up_call_unique_id",
            "a.status",
            "a.remark",
            "b.follow_up_date",
            "a.unique_id"
        ];

        $table_join     = $table_sub." AS a LEFT JOIN follow_up_call AS b ON a.follow_up_call_unique_id = b.unique_id ";

        $table_details  = [
            $table_join." JOIN (SELECT @c:= ".$start.") AS c ",
            $columns
        ];
        $where          = ' a.follow_up_call_unique_id = "'.$_POST['follow_up_call_unique_id'].'" AND a.is_active =  1 AND a.is_delete = 0 AND b.is_delete = 0';
        $order_by       = "";
        $group_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function,$group_by);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                if ($value['entry_date'] == $today) {
                    $btn_edit                   = btn_edit($btn_edit_delete,$value['unique_id']);
                    $btn_delete                 = btn_delete($btn_edit_delete,$value['follow_up_call_unique_id']."','".$value['unique_id']);
                } else {
                    $btn_edit                   = "";
                    $btn_delete                 = "";
                }

                if ($value['next_follow_up_date'] == "0000-00-00") {
                    $value['next_follow_up_date']   = "<b><span class='text-danger'>Closed</span></b>";
                } else {

                    $value['next_follow_up_date']   = disdate($value['next_follow_up_date']);
                }

                $value['entry_date']            = disdate($value['entry_date']);
                $value['follow_up_date']        = disdate($value['follow_up_date']);
                $value['unique_id']             = $btn_edit.$btn_delete;
                unset($value['follow_up_call_unique_id']);
                $data[]                         = array_values($value);
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;

    case "follow_up_call_sub_edit":
            // Fetch Data
            $unique_id  = $_POST['unique_id'];
            $data	    = [];
            
            // Query Variables
            $json_array     = "";
            $columns        = [
                "status",
                "remark",
                "next_follow_up_days",
                "next_follow_up_date",
                "call_status",
                "close_remark",
                "close_date"
            ];
            $table_details  = [
                $table_sub,
                $columns
            ];
            $where          = [
                "unique_id"    => $unique_id,
                "is_active"    => 1,
                "is_delete"    => 0
            ];        
        
            $result         = $pdo->select($table_details,$where);
        
            if ($result->status) {
                
                $json_array = [
                    "data" 		=> $result->data[0],
                    "status"    => $result->status,
                    "sql"       => $result->sql,
                    "error"     => $result->error,
                    "testing"	=> $result->sql
                ];
            } else {
                print_r($result);
            }
            
            echo json_encode($json_array);
        break;

    case 'follow_up_call_delete':

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
        $sub_unique_id  = $_POST['sub_unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id,
            "follow_up_date"=> $today
        ];

        $action_obj     = $pdo->update($table,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            // $msg        = "success_delete";

            // Get Previous ID
            $sub_select_columns  = [
                "prev_follow_up_unique_id"
            ];
    
            $sub_select_where   = [
                "unique_id"     => $sub_unique_id
            ];
            
            $sub_table_details  = [
                $table_sub,
                $sub_select_columns
            ];

            $sub_action_obj     = $pdo->select($sub_table_details,$sub_select_where);


            if ($sub_action_obj->status) {
                $data           = $sub_action_obj->data;
                $prev_sub_id    = $data[0]['prev_follow_up_unique_id'];
                
                // Update Previoue Follow Up was active

                if ($prev_sub_id != 'new') {

                    $sub_prev_columns            = [
                        "is_updated"   => 0
                    ];
            
                    $sub_prev_update_where   = [
                        "unique_id"     => $prev_sub_id,
                    ];
            
                    $sub_prev_update_action_obj     = $pdo->update($table_sub,$sub_prev_columns,$sub_prev_update_where);


                    // print_r ($sub_prev_update_action_obj);

                    if (!$sub_prev_update_action_obj->status) {
                        $status         = $sub_prev_update_action_obj->status;
                        $data           = $sub_prev_update_action_obj->data;
                        $error          = $sub_prev_update_action_obj->error;
                        $sql            = $sub_prev_update_action_obj->sql;
                        $msg            = "error";
                    }
                }
                
                // Sublist Delete
                $sub_columns            = [
                    "is_delete"   => 1,
                ];
        
                $sub_update_where   = [
                    "unique_id"     => $sub_unique_id,
                ];
        
                $sub_update_action_obj     = $pdo->update($table_sub,$sub_columns,$sub_update_where);


                if (!$sub_update_action_obj->status) {
                    $status         = $sub_update_action_obj->status;
                    $data           = $sub_update_action_obj->data;
                    $error          = $sub_update_action_obj->error;
                    $sql            = $sub_update_action_obj->sql;
                    $msg            = "error";
                } else {
                    $status         = $sub_update_action_obj->status;
                    $data           = $sub_update_action_obj->data;
                    $error          = $sub_update_action_obj->error;
                    $sql            = $sub_update_action_obj->sql;
                    $msg            = "success_delete";
                }

            } else {
                $status         = $sub_action_obj->status;
                $data           = $sub_action_obj->data;
                $error          = $sub_action_obj->error;
                $sql            = $sub_action_obj->sql;
                $msg            = "error";
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

    case 'new_lead':

        // Table Related to this conversion
        $table_leads        = "leads_follow_up_call";
        $table_leads        = "leads";
        $table_leads_sub    = "leads_follow_up_call_sublist";
        $table_leads_sub    = "leads_sublist";
        $leads_prefix       = "LED-";
        $prefix             = "led";

        $url                = "";

        // Get Call No and Call Unique id 
        $call_no            = $_POST['call_id'];
        $call_unique_id     = $_POST['call_unique_id'];

        $bill_no_where   = [
            "acc_year"      => $_SESSION['acc_year']
        ];

        // GET Bill No
        $lead_no                    = bill_no($table_leads,$bill_no_where,$leads_prefix);
        $columns['lead_id']         = $lead_no;


        // Select Details From Follow Up Call Usind Call Unique Id

        $select_where  = [
            "unique_id" => $call_unique_id
        ];

        $select_result  = $pdo->select($table,$select_where);

        if ($select_result->status) {

            $call_details = $select_result->data;

            $customer_details = customers ($call_details[0]['customer_id']);

            $insert_columns = [
                'follow_up_call_unique_id'  => $call_unique_id,
                'follow_up_call_no'         => $call_no,
                'lead_no'                   => $lead_no,
                'lead_date'                 => $today,
                'customer_id'               => $call_details[0]['customer_id'],
                'customer_type_id'          => $customer_details[0]['customer_type'],
                "customer_state_id"         => $customer_details[0]['state_unique_id'],
                "customer_city_id"          => $customer_details[0]['city_unique_id'],
                "customer_segement_id"      => "5fd216814a2e267766", // This is Defalut From customer segment table
                "enquiry_type_id"           => "5fd217e6bf2a018918", // This is Defalut from enquiry type table
                "action_val"                => "1",
                'unique_id'                 => $lead_unique_id = unique_id($prefix)
            ];

            // Table Insert Begins

            $insert_result  = $pdo->insert($table_leads,$insert_columns);

            if ($insert_result->status) {

                $insert_columns_sub = [
                    "lead_no"               => $lead_no,
                    "lead_unique_id"        => $lead_unique_id,
                    "prev_lead_unique_id"   => "new",
                    "entry_date"            => $today,
                    "status"                => "",
                    "remark"                => "",
                    "next_follow_up_days"   => 1,
                    "next_follow_up_date"   => date('Y-m-d', strtotime($today . ' +1 day')),
                    "unique_id"             => $lead_sub_unique_id = unique_id()
                ];

                $insert_sub_result  = $pdo->insert($table_leads_sub,$insert_columns_sub);

                if ($insert_sub_result->status) {
                    $status     = $insert_sub_result->status;
                    $data       = $insert_sub_result->data;
                    $error      = $insert_sub_result->error;
                    $sql        = $insert_sub_result->sql;
                    $msg        = "create";
                    $url        = "unique_id=".$lead_unique_id."&sub_unique_id=".$lead_sub_unique_id;
                } else {
                    $status     = $insert_sub_result->status;
                    $data       = $insert_sub_result->data;
                    $error      = $insert_sub_result->error;
                    $sql        = $insert_sub_result->sql;
                    $msg        = "error";
                }

            } else {
                $status     = $insert_result->status;
                $data       = $insert_result->data;
                $error      = $insert_result->error;
                $sql        = $insert_result->sql;
                $msg        = "error";
            }
            
        } else {
            $status     = $select_result->status;
            $data       = $select_result->data;
            $error      = $select_result->error;
            $sql        = $select_result->sql;
            $msg        = "error";
        }

        $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql,
            "url"       => $url
        ];

        echo json_encode($json_array);
        
        break;
    
    default:
        
        break;
}

?>