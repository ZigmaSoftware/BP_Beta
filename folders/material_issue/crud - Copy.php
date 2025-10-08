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

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    case 'createupdate':

        $unique_id          = $_POST['unique_id'];
        $sub_unique_id      = $_POST['sub_unique_id'];
        $prev_sub_unique_id = $_POST['prev_sub_unique_id'];


        $update_where       = "";

        $columns            = [
            "follow_up_call_id"     => "afjlsdf",
            "follow_up_date"        => today(),
            "executive_id"          => $_SESSION['user_id'],
            "location_id"           => "Erode",
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
            // $select_where   .= ' AND unique_id !="'.$unique_id.'" AND follow_up_call_id = "'.$_POST['call_id'].'" ';

            // Call Id When Update
            $follow_up_call_id = $_POST['call_id'];

        } else {
            $bill_no_where   = [
                "acc_year"      => $_SESSION['acc_year']
            ];

            // GET Bill No
            $follow_up_call_id             = bill_no($table,$bill_no_where);
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
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);

                $sub_update_where   = [
                    "unique_id"     => $_POST['sub_unique_id']
                ];

                $sub_colunms     = [
                    "is_updated"    => 1,
                    "updated_date"  => today()
                ];

                $main_unique        = $unique_id;

                // Update Previous Follow Up Update Status in $table_sub
                $action_obj     = $pdo->update($table_sub,$sub_colunms,$sub_update_where);

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

                if ($sub_unique_id) {
                    // Sub List Update Where
                    $sub_update_where   = [
                        "unique_id"     => $sub_unique_id
                    ];

                    if ($_POST["follow_up_action_type"]) {
                        $columns_sub = [
                            "status"                    => $_POST["cur_status"],
                            "remark"                    => $_POST["remark"],
                            "next_follow_up_days"       => $_POST['next_follow_up_days'],
                            "next_follow_up_date"       => $_POST['follow-up-date'],
                            "call_status"               => "",
                            "close_remark"              => "",
                            "close_date"                => ""
                        ];
                    } else {
                        $columns_sub = [
                            "status"                    => $_POST["cur_status"],
                            "remark"                    => $_POST["remark"],
                            "next_follow_up_days"       => "",
                            "next_follow_up_date"       => "",
                            "call_status"               => $_POST['call_status'],
                            "close_remark"              => $_POST['close_remark'],
                            "close_date"                => $_POST['close_date']
                        ];
                    }

                    // Sublist Update Begins
                    $sub_action_obj     = $pdo->update($table_sub,$columns_sub,$sub_update_where);

                } else {

                    if ($_POST["follow_up_action_type"]) {
                        $columns_sub = [
                            "follow_up_call_id"         => $follow_up_call_id,
                            "follow_up_call_unique_id"  => $main_unique,
                            "status"                    => $_POST["cur_status"],
                            "remark"                    => $_POST["remark"],
                            "next_follow_up_days"       => $_POST['next_follow_up_days'],
                            "next_follow_up_date"       => $_POST['follow-up-date'],
                            "unique_id"                 => unique_id()
                        ];
                    } else {
                        $columns_sub = [
                            "follow_up_call_id"        => $follow_up_call_id,
                            "follow_up_call_unique_id" => $main_unique,
                            "status"                   => $_POST["cur_status"],
                            "remark"                   => $_POST["remark"],
                            "call_status"              => $_POST['call_status'],
                            "close_remark"             => $_POST['close_remark'],
                            "close_date"               => $_POST['close_date'],
                            "unique_id"                => unique_id()
                        ];
                    }

                    $sub_action_obj     = $pdo->insert($table_sub,$columns_sub);

                }

                if (!$sub_action_obj->status) {
                    $status     = $sub_action_obj->status;
                    $data       = $sub_action_obj->data;
                    $error      = $sub_action_obj->error;
                    $sql        = $sub_action_obj->sql;
                    $msg        = "error";
                } else {

                    if ($unique_id) {
                        $msg        = "update";
                    } else {
                        $msg        = "create";
                    }

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

    case 'datatable':
        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
        $limit 		= $length;


        // List Display Count 

        $count_array   = [
            "new_calls"     => 0,
            "follow_ups"    => 0,
            "updated"       => 0,
            "pending"       => 0
        ];
        
        
        // $today      = "2020-12-04";

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@c:=@c+1 s_no",
            "b.follow_up_date",
            "a.next_follow_up_date",
            "(SELECT call_type FROM call_type WHERE call_type.unique_id = b.call_type_id) AS call_type",
            "a.follow_up_call_id",
            "b.customer_id",
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
        $where          = ' (b.follow_up_date BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'" OR a.next_follow_up_date BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'") AND a.is_active =  1 AND a.is_delete = 0 AND b.is_delete = 0';
        $order_by       = "";
        $group_by       = " a.follow_up_call_id";
        
        // Custom Filter
        switch ($_POST['filter_action']) {
            case 'new_calls':
                # code...
                break;

            case 'follow_ups':
                # code...
                break;

            case 'updated':
                # code...
                break;

            case 'pending':
                # code...
                break;
            
            default:
                # code...
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
                $value['customer_id']           = disname($value['customer_id']);
                $btn_update                     = btn_update($folder_name,$value['unique_id']."&sub_unique_id=".$value['sub_unique_id']);
                if (!($value['follow_up_date'] == disdate($today))) {
                    $btn_delete = "";
                } else {
                    $btn_delete                     = btn_delete($folder_name,$value['unique_id']."','".$value['sub_unique_id']);
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
                                                COUNT(follow_up_call_id)
                                            FROM
                                                follow_up_call
                                            WHERE
                                                follow_up_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0
                                        ) AS new_calls,
                                        (
                                        SELECT
                                            COUNT(follow_up_call_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            next_follow_up_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0
                                    ) AS follow_ups,
                                    (
                                        SELECT
                                            '0'
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            next_follow_up_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0
                                    ) AS updated,
                                    (
                                        SELECT
                                            '0'
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            next_follow_up_date BETWEEN '".$_POST['from_date']."' AND '".$_POST['to_date']."' AND is_active = 1 AND is_delete = 0
                                    ) AS pending
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
            "b.follow_up_date",
            "a.next_follow_up_date",
            "a.status",
            "a.remark",
            "a.next_follow_up_date as nfd",
            "a.unique_id"
        ];

        $table_join = $table_sub." AS a LEFT JOIN follow_up_call AS b ON a.follow_up_call_unique_id = b.unique_id ";

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
                $value['follow_up_date']        = disdate($value['follow_up_date']);
                $value['next_follow_up_date']   = disdate($value['next_follow_up_date']);
                $btn_edit                       = btn_edit($btn_edit_delete,$value['unique_id']);
                // $btn_edit                       = "";
                $btn_delete                     = btn_delete($btn_edit_delete,$value['unique_id']);
                $btn_delete                     = "";
                $value['unique_id']             = $btn_edit.$btn_delete;
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

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
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
    
    default:
        
        break;
}

?>