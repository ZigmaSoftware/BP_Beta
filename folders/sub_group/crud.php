<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "sub_group";
$table_group       = "groups";

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

$group_unique_id         = "";
$sub_group_name         = "";
$code               = "";
$description        = "";
$is_active          = "";
$unique_id          = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$user_id = $_SESSION['sess_user_id'];
$date = date('Y-m-d H:i:s', time());

switch ($action) {
    case 'createupdate':

        $group_unique_id         = $_POST["group_unique_id"];
        $sub_group_name         = $_POST["sub_group_name"];
        $group_code               = $_POST["group_code"];
        $code               = $_POST["code"];
        $final_code         = $group_code .'-'.$code;
		$description        = $_POST["description"];
        $is_active          = $_POST["is_active"];
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "group_unique_id"     => $group_unique_id,
            "sub_group_name"     => $sub_group_name,
            "code"=> $final_code,
            "description"   => $description,
            "is_active"     => $is_active,
            "created_user_id"     => $user_id,
            "created"     => $date,
            "unique_id"     => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'group_unique_id = "'.$group_unique_id.'" AND (sub_group_name = "'.$sub_group_name.'" OR code = "'.$final_code.'") AND is_delete = 0  ';

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
        } else if ($data[0]["count"] == 0) {
        // Update Begins
        if($unique_id) {

            unset($columns['unique_id']);
            unset($columns['created_user_id']);
            unset($columns['created']);
            
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;

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

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "sub_group_name",
            "group_unique_id",
            "description",
            "is_active",
            "unique_id",
            "code"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";
        
        if ($_POST['group_unique_id']) {
            $where .= " AND group_unique_id = '$_POST[group_unique_id]' ";
        }

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

            foreach ($res_array as $key => $value) {
                $part  = explode('-',$value['code']);
                $group_data = group_name($value['group_unique_id']);
                $value['group_unique_id'] =  '<div>' .$group_data[0]['group_name'] . '</div><div style="font-weight: bold;">' . $part[0] .'</div>'; 
                $value['sub_group_name'] =  '<div>' .$value['sub_group_name'] . '</div><div style="font-weight: bold;">' . $value['code'] .'</div>'; 
                if($value['description'] == ' ' || empty($value['description']) || $value['description'] == ''){
                    $description = '-';
                } else {
                    $description = $value['description'];
                }
                $value['description'] = $description;
                 $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_toggle = ($value['is_active'] == 1)
                        ? btn_toggle_on($folder_name, $value['unique_id'])
                        : btn_toggle_off($folder_name, $value['unique_id']);
                
                    $value['is_active'] = ($value['is_active'] == 1)
                        ? "<span style='color: green'>Active</span>"
                        : "<span style='color: red'>In Active</span>";
                
                    $value['unique_id'] = $btn_update . $btn_toggle;
                
                    $data[] = array_values($value);
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
    
    
   case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active']; // 1 or 0

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    $status = $action_obj->status;
    $msg    = $status
            ? ($is_active == 1 ? "Activated Successfully" : "Deactivated Successfully")
            : "Toggle failed!";

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

    
    case 'get_group_code':
        $unique_id = $_POST['code'];
        
        $json_array     = "";
        
        $columns        = [
            "code"
        ];
        $table_details  = [
            $table_group,
            $columns
        ];
        
        $where  = " is_delete = '0' AND unique_id = '$unique_id'";
        
        $result = $pdo->select($table_details,$where);
        if ($result->status) {
            if (!empty($result->data)) {
                $json_array = [
                    'status' => 'success',
                    'data' => $result->data[0]['code']
                    ];
                echo json_encode($json_array);
            } else {
                $json_array = [
                    'status' => 'empty',
                    'message' => 'No matching data found.'
                ];
                echo json_encode($json_array);
            }
        } else {
            $json_array = [
                'status' => 'error',
                'message' => 'Query execution failed.'
            ];
            echo json_encode($json_array);
        }
        
        break;
    
    default:
        
        break;
}

?>