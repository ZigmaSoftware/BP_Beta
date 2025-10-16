<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "supplier_ratings";

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

$work_location    = "";
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
    
        $supplier_id = $_POST["supplier_id"];
        $from_period = $_POST["from_period"];
        $to_period   = $_POST["to_period"];
        $q_rating    = $_POST["q_rating"];
        $d_rating    = $_POST["d_rating"];
        $r_rating    = $_POST["r_rating"];
        $c_rating    = $_POST["c_rating"];
        $t_rating    = $_POST["t_rating"];
        $remarks     = $_POST["remarks"];
        $is_active   = $_POST["is_active"];
        $unique_id   = $_POST["unique_id"];

        $update_where       = "";

       $columns = [
                "supplier_id"  => $supplier_id,
                "from_period"  => $from_period,
                "to_period"    => $to_period,
                "q_rating"     => $q_rating,
                "d_rating"     => $d_rating,
                "r_rating"     => $r_rating,
                "c_rating"     => $c_rating,
                "t_rating"     => $t_rating,
                "remarks"      => $remarks,
                "is_active"    => $is_active,
                "unique_id"    => ($unique_id ?: unique_id($prefix))
            ];


        // Update Begins
        if($unique_id) {

            unset($columns['unique_id']);

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
            "supplier_id",
            "from_period" ,
            "to_period",
            "q_rating",
            "d_rating",
            "r_rating",
            "t_rating",
            "c_rating",
            "remarks",
            "is_active",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";
    
        
        $from_period = $_POST['from_period'] ?? '';
        $to_period   = $_POST['to_period'] ?? '';
        $supplier_id  = $_POST['supplier_id'] ?? '';
        $status_fill  = $_POST['status_fill'] ?? '';
        
        $conditions = [];
        
        
        
        if (!empty($from_period) && !empty($to_period)) {
            $conditions[] = " (from_period >= '{$from_period}' AND to_period <= '{$to_period}') ";
        } elseif (!empty($from_period)) {
            $conditions[] = " from_period >= '{$from_period}' ";
        } elseif (!empty($to_period)) {
            $conditions[] = " to_period <= '{$to_period}' ";
        }

        
        if (!empty($supplier_id)) {
            $conditions[] = "supplier_id = '{$supplier_id}'";
        }
        
        if (!empty($status_fill)) {
            if ($status_fill == 1) {
                $conditions[] = "(q_rating >= 30 AND d_rating >= 15 AND r_rating >= 10 AND t_rating >= 50)";
            } elseif ($status_fill == 2) {
                $conditions[] = "(q_rating < 30 OR d_rating < 15 OR r_rating < 10 OR t_rating < 50)";
            }
        }

        if (!empty($conditions)) {
            $where .= " AND " . implode(" AND ", $conditions);
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
        // error_log(print_r($result, true), 3, "logs/datatable.log");
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;
            
            // $data = [];
            
          
            foreach ($res_array as $key => $value) {
                                // error_log(print_r($value,true),3,"logs/datas.log" );
                                // error_log(print_r($value['supplier_id'],true),3,"logs/datas.log" );

                $value['supplier_id'] = supplier($value['supplier_id'])[0]['supplier_name'];
                $from = !empty($value['from_period']) ? date("M Y", strtotime($value['from_period'])) : "";
                $to   = !empty($value['to_period'])   ? date("M Y", strtotime($value['to_period']))   : "";
                $value['period_range'] = $from . " - " . $to;
                
                unset($value['from_period'], $value['to_period']);

                $value['q_rating'] = intval($value['q_rating']) . "/50";
                $value['d_rating'] = intval($value['d_rating']) . "/20";
                $value['r_rating'] = intval($value['r_rating']) . "/10";
                $value['c_rating'] = intval($value['c_rating']) . "/20";
                $value['t_rating'] = intval($value['t_rating']) . "/100";


                
                
                $status_check = "Satisfactory";
                if (
                    $value['q_rating'] < 30 ||  // out of 50
                    $value['d_rating'] < 10 ||  // out of 20
                    $value['r_rating'] < 5  ||  // out of 10
                    $value['c_rating'] < 10 ||  // out of 20
                    $value['t_rating'] < 50     // total out of 100
                ) {
                    $status_check = "Unsatisfactory";
                }

                            
    
                $value['satisfactory_status'] = $status_check;
                
                
                
                $btn_update = btn_update($folder_name, $value['unique_id']);
                // $btn_toggle = ($value['is_active'] == 1)
                //     ? btn_toggle_on($folder_name, $value['unique_id'])
                //     : btn_toggle_off($folder_name, $value['unique_id']);
                
                $value['is_active'] = is_active_show($value['is_active']);
                $value['unique_id'] = $btn_update . $btn_toggle;
                
                // $data[] = array_values($value);
                
                $data[] = [
                    $value['s_no'],
                    $value['supplier_id'],
                    $value['period_range'],
                    $value['q_rating'] ,
                    $value['d_rating'] ,
                    $value['r_rating'] ,
                    $value['c_rating'],
                    $value['t_rating'] ,
                    $value['satisfactory_status'] ,
                    $value['remarks'] ,
                    $value['is_active'],
                    $value['unique_id']
                ];
                error_log(print_r($data, true) , 3 , "logs/data.log");

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
    $is_active = $_POST['is_active'];

    $columns = [
        "is_active" => $is_active
    ];
    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    if ($action_obj->status) {
        $status = true;
        $msg = $is_active ? "Activated Successfully" : "Deactivated Successfully";
    } else {
        $status = false;
        $msg = "Toggle failed!";
    }

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

    default:
        
        break;
}

?>