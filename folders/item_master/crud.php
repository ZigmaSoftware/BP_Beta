<?php 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = 'item_master';
$category_table    = 'category_master';
$table_sub_group   = "sub_group";
$table_group       = "groups";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include '../../config/new_db.php';

if (!isset($_SESSION['sess_user_id'])) {
    echo json_encode([
        "status" => 0,
        "msg" => "Session expired",
        "error" => "User session not found"
    ]);
    exit;
}

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$qc_approval        = "";
$qc_final           = "";
$group_unique_id    = "";
$group_unique_id    = "";
$sub_group_unique_id= "";
$category_unique_id = "";
$item_name          = "";
$uom                = "";
$item_code          = "";
$reorder_level      = "";
$reorder_qty        = "";
$hsn_code           = "";
$purchase_lead_time = "";
$tolerance          = "";
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
// case 'createupdate':

//     error_log("✅ Entered createupdate case\n", 3, "debug_log.txt");
//     error_log("✅ POST: " . print_r($_POST, true) . "\n", 3, "debug_log.txt");

//     $group_unique_id        = $_POST["group_unique_id"];
//     $sub_group_unique_id    = $_POST["sub_group_unique_id"];
//     $category_unique_id     = $_POST["category_unique_id"];
//     $item_name              = $_POST["item_name"];
//     $uom                    = $_POST["uom"];
//     $item_code              = $_POST["item_code"];
//     $reorder_level          = $_POST["reorder_level"];
//     $reorder_qty            = $_POST["reorder_qty"];
//     $hsn_code               = $_POST["hsn_code"];
//     $purchase_lead_time     = $_POST["purchase_lead_time"];
//     $tolerance              = $_POST["tolerance"];
//     $unit_price             = $_POST["unit_price"];
//     $gst                    = $_POST["gst"];
//     $description            = $_POST["description"];
//     $qc_approval            = $_POST["qc_approval"];
//     $qc_final               = $_POST["qc_final"];
//     $is_active              = $_POST["is_active"];
//     $unique_id              = $_POST["unique_id"];

//     $update_where = "";

//     $columns = [
//         "qc_approval"           => $qc_approval,
//         "qc_final"              => $qc_final,
//         "group_unique_id"       => $group_unique_id,
//         "sub_group_unique_id"   => $sub_group_unique_id,
//         "category_unique_id"    => $category_unique_id,
//         "item_name"             => $item_name,
//         "item_code"             => $item_code,
//         "uom_unique_id"         => $uom,
//         "reorder_level"         => $reorder_level,
//         "reorder_qty"           => $reorder_qty,
//         "hsn_code"              => $hsn_code,
//         "purchase_lead_time"    => $purchase_lead_time,
//         "tolerance"             => $tolerance,
//         "unit_price"            => $unit_price,   
//         "gst"                   => $gst,         
//         "description"           => $description,
//         "is_active"             => $is_active,
//         "created_user_id"       => $user_id,
//         "created"               => $date,
//         "unique_id"             => unique_id($prefix)
//     ];

//     // Check if already exists
//     $table_details = [$table, ["COUNT(unique_id) AS count"]];
//     $select_where = 'group_unique_id = "' . $group_unique_id . '" AND sub_group_unique_id = "' . $sub_group_unique_id . '" AND category_unique_id = "' . $category_unique_id . '" AND item_name = "' . $item_name . '" AND is_delete = 0';

//     if ($unique_id) {
//         $select_where .= ' AND unique_id != "' . $unique_id . '"';
//     }

//     $action_obj = $pdo->select($table_details, $select_where);
//     error_log("✅ action_obj1 (duplicate check): " . print_r($action_obj, true) . "\n", 3, "debug_log.txt");

//     if ($action_obj->status && $action_obj->data[0]["count"] == 0) {

//         error_log("✅ Entered INSERT/UPDATE block\n", 3, "debug_log.txt");

//         if ($unique_id) {
//             // UPDATE
//             unset($columns['unique_id'], $columns['created_user_id'], $columns['created']);
//             $columns['updated_user_id'] = $user_id;
//             $columns['updated'] = $date;

//             $update_where = ["unique_id" => $unique_id];

//             $action_obj = $pdo->update($table, $columns, $update_where);
//             error_log("✅ action_obj2 (update): " . print_r($action_obj, true) . "\n", 3, "debug_log.txt");

//         } else {
//             // INSERT Begins
//             $columns_code = ["code"];
//             error_log("📌 columns_code: " . print_r($columns_code, true) . "\n", 3, "debug_log.txt");

//             $table_details_code = [$category_table, $columns_code];
//             error_log("📌 table_details_code: " . print_r($table_details_code, true) . "\n", 3, "debug_log.txt");

//             $where_code = "is_delete = '0' AND unique_id = '$category_unique_id'";
//             error_log("📌 where_code: $where_code\n", 3, "debug_log.txt");

//             $prefixs = "";

//             try {
//                 $result = $pdo->select($table_details_code, $where_code);
//                 error_log("📌 pdo->select result: " . print_r($result, true) . "\n", 3, "debug_log.txt");

//                 if ($result->status && !empty($result->data)) {
//                     $prefixs = $result->data[0]['code'] . '-';
//                     error_log("✅ Prefix generated: $prefixs\n", 3, "debug_log.txt");
//                 } else {
//                     error_log("⚠️ No prefix found from category, using empty\n", 3, "debug_log.txt");
//                 }

//             } catch (Exception $e) {
//                 error_log("❌ Exception during prefix fetch: " . $e->getMessage() . "\n", 3, "debug_log.txt");
//             }

//             // Safe fallback to avoid undefined $prefixs
//             $prefixs = isset($prefixs) ? $prefixs : "";

//             // Generate bill/item code
//             $bill_no = batch_creation($table, $category_unique_id, $update_where, $prefixs, $conns);
//             $columns['item_code'] = $bill_no;

//             $action_obj = $pdo->insert($table, $columns);
//             error_log("✅ action_obj3 (insert): " . print_r($action_obj, true) . "\n", 3, "debug_log.txt");
//             error_log("✅ Insert SQL: " . $action_obj->sql . "\n", 3, "debug_log.txt");
//             error_log("✅ Insert Error: " . print_r($action_obj->error, true) . "\n", 3, "debug_log.txt");
//         }


//         // Finalize response
//         if ($action_obj->status) {
//             $status = $action_obj->status;
//             $data = $action_obj->data;
//             $error = "";
//             $sql = $action_obj->sql;
//             $msg = $unique_id ? "update" : "create";
//         } else {
//             $status = $action_obj->status;
//             $data = $action_obj->data;
//             $error = $action_obj->error;
//             $sql = $action_obj->sql;
//             $msg = "error";
//         }

//     } else {
//         $msg = "already";
//         $status = false;
//         $data = [];
//         $error = "";
//         $sql = "";
//     }

//     $json_array = [
//         "status" => $status,
//         "data"   => $data,
//         "error"  => $error,
//         "msg"    => $msg,
//         "sql"    => $sql
//     ];

//     $json_output = json_encode($json_array);
//     if ($json_output === false) {
//         error_log("❌ JSON Encode Error: " . json_last_error_msg() . "\n", 3, "debug_log.txt");
//     }

//     error_log("✅ Final JSON: " . print_r($json_array, true) . "\n", 3, "debug_log.txt");

//     header('Content-Type: application/json');
//     echo $json_output;

// break;

case 'createupdate':

    error_log("✅ Entered createupdate case\n", 3, "debug_log.txt");
    error_log("✅ POST: " . print_r($_POST, true) . "\n", 3, "debug_log.txt");

    $group_unique_id        = $_POST["group_unique_id"];
    $sub_group_unique_id    = $_POST["sub_group_unique_id"];
    $category_unique_id     = $_POST["category_unique_id"];
    $item_name              = $_POST["item_name"];
    $uom                    = $_POST["uom"];
    $item_code              = $_POST["item_code"]; // will be overwritten on insert
    $reorder_level          = $_POST["reorder_level"];
    $reorder_qty            = $_POST["reorder_qty"];
    $hsn_code               = $_POST["hsn_code"];
    $purchase_lead_time     = $_POST["purchase_lead_time"];
    $tolerance              = $_POST["tolerance"];
    $unit_price             = $_POST["unit_price"];
    $gst                    = $_POST["gst"];
    $description            = $_POST["description"];
    $qc_approval            = $_POST["qc_approval"];
    $qc_final               = $_POST["qc_final"];
    $is_active              = $_POST["is_active"];
    $unique_id              = $_POST["unique_id"];

    $update_where = "";

    $columns = [
        "qc_approval"           => $qc_approval,
        "qc_final"              => $qc_final,
        "group_unique_id"       => $group_unique_id,
        "sub_group_unique_id"   => $sub_group_unique_id,
        "category_unique_id"    => $category_unique_id,
        "item_name"             => $item_name,
        "item_code"             => $item_code, // will be replaced if insert
        "uom_unique_id"         => $uom,
        "reorder_level"         => $reorder_level,
        "reorder_qty"           => $reorder_qty,
        "hsn_code"              => $hsn_code,
        "purchase_lead_time"    => $purchase_lead_time,
        "tolerance"             => $tolerance,
        "unit_price"            => $unit_price,   
        "gst"                   => $gst,         
        "description"           => $description,
        "is_active"             => $is_active,
        "created_user_id"       => $user_id,
        "created"               => $date,
        "unique_id"             => unique_id($prefix)
    ];

    // Duplicate check
    $table_details = [$table, ["COUNT(unique_id) AS count"]];
    $select_where = 'group_unique_id = "' . $group_unique_id . '" AND sub_group_unique_id = "' . $sub_group_unique_id . '" AND category_unique_id = "' . $category_unique_id . '" AND item_name = "' . $item_name . '" AND is_delete = 0';

    if ($unique_id) {
        $select_where .= ' AND unique_id != "' . $unique_id . '"';
    }

    $action_obj = $pdo->select($table_details, $select_where);
    error_log("✅ action_obj1 (duplicate check): " . print_r($action_obj, true) . "\n", 3, "debug_log.txt");

    if ($action_obj->status && $action_obj->data[0]["count"] == 0) {

        error_log("✅ Entered INSERT/UPDATE block\n", 3, "debug_log.txt");

        if ($unique_id) {
            // === UPDATE ===
            unset($columns['unique_id'], $columns['created_user_id'], $columns['created']);
            $columns['updated_user_id'] = $user_id;
            $columns['updated'] = $date;

            $update_where = ["unique_id" => $unique_id];

            $action_obj = $pdo->update($table, $columns, $update_where);
            error_log("✅ action_obj2 (update): " . print_r($action_obj, true) . "\n", 3, "debug_log.txt");

        } else {
            // === INSERT ===
            try {
                $bill_no = generate_item_code(
                    $pdo,
                    $table,
                    $table_sub_group,
                    $category_table,
                    $group_unique_id,
                    $sub_group_unique_id,
                    $category_unique_id
                );
                $columns['item_code'] = $bill_no;

                $action_obj = $pdo->insert($table, $columns);
                error_log("✅ action_obj3 (insert): " . print_r($action_obj, true) . "\n", 3, "debug_log.txt");
                error_log("✅ Insert SQL: " . $action_obj->sql . "\n", 3, "debug_log.txt");
                error_log("✅ Insert Error: " . print_r($action_obj->error, true) . "\n", 3, "debug_log.txt");
            } catch (Exception $e) {
                error_log("❌ Exception during item_code generation/insert: " . $e->getMessage() . "\n", 3, "debug_log.txt");
            }
        }

        // Finalize response
        if ($action_obj->status) {
            $status = $action_obj->status;
            $data = $action_obj->data;
            $error = "";
            $sql = $action_obj->sql;
            $msg = $unique_id ? "update" : "create";
        } else {
            $status = $action_obj->status;
            $data = $action_obj->data;
            $error = $action_obj->error;
            $sql = $action_obj->sql;
            $msg = "error";
        }

    } else {
        $msg = "already";
        $status = false;
        $data = [];
        $error = "";
        $sql = "";
    }

    $json_array = [
        "status" => $status,
        "data"   => $data,
        "error"  => $error,
        "msg"    => $msg,
        "sql"    => $sql
    ];

    $json_output = json_encode($json_array);
    if ($json_output === false) {
        error_log("❌ JSON Encode Error: " . json_last_error_msg() . "\n", 3, "debug_log.txt");
    }

    error_log("✅ Final JSON: " . print_r($json_array, true) . "\n", 3, "debug_log.txt");

    header('Content-Type: application/json');
    echo $json_output;

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
            // "qc_approval",            
            "item_name",            
            "category_unique_id",   
            "sub_group_unique_id",  
            "group_unique_id",
            "description",
            "is_active",
            "unique_id",
            // "uom_unique_id",        
            // "reorder_level",        
            // "reorder_qty",          
            // "hsn_code",
            "item_code" 
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";
        
        if (!empty($_POST['group_unique_id']) && !empty($_POST['sub_group_unique_id']) && !empty($_POST['category_unique_id'])) {
            $where .= " AND group_unique_id = '{$_POST['group_unique_id']}' AND sub_group_unique_id = '{$_POST['sub_group_unique_id']}' AND category_unique_id = '{$_POST['category_unique_id']}'";
        } else {
            $conditions = [];
        
            // if (!empty($_POST['data_type'])) {
            //     $conditions[] = "data_type = '{$_POST['data_type']}'";
            // }
            if (!empty($_POST['group_unique_id'])) {
                $conditions[] = "group_unique_id = '{$_POST['group_unique_id']}'";
            }
            if (!empty($_POST['sub_group_unique_id'])) {
                $conditions[] = "sub_group_unique_id = '{$_POST['sub_group_unique_id']}'";
            }
            if (!empty($_POST['category_unique_id'])) {
                $conditions[] = "category_unique_id = '{$_POST['category_unique_id']}'";
            }
        
            if (!empty($conditions)) {
                $where .= " AND " . implode(" AND ", $conditions);
            }
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
        // $data_type_options  = [
        //     1 => [
        //         "unique_id" => 1,
        //         "value"     => "Consumable",
        //     ],
        //     2 => [
        //         "unique_id" => 2,
        //         "value"     => "Component",
        //     ]
        // ];
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $part                           = explode('-',$value['item_code']);
                // $data_type                      = $data_type_options[$value['data_type']]['value'];
                // $value['data_type']             = $data_type;
                $group_data                     = group_name($value['group_unique_id']);
                $sub_group_data                 = sub_group_name($value['sub_group_unique_id']);
                $category_data                  = category_name($value['category_unique_id']);
                $value['group_unique_id']       =  '<div>' .$group_data[0]['group_name'] . '</div><div style="font-weight: bold;">' . $part[0] .'</div>';
                $value['sub_group_unique_id']   =  '<div>' .$sub_group_data[0]['sub_group_name'] . '</div><div style="font-weight: bold;">' . $part[1] .'</div>';
                $value['category_unique_id']    =  '<div>' .$category_data[0]['category_name'] . '</div><div style="font-weight: bold;">' . $part[2] .'</div>';
                $value['item_name']             =  '<div>' .$value['item_name'] . '</div><div style="font-weight: bold;">' . $value['item_code'] .'</div>';
                
                if($value['description'] == ' ' || empty($value['description']) || $value['description'] == ''){
                    $description = '-';
                } else {
                    $description = $value['description'];
                }
                
                $value['description']           = $description;
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
    $is_active = $_POST['is_active'];

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
            $table_sub_group,
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
        
    case 'sub_group_name':

        $group_id = $_POST['group_id'];
        $category = $_POST['category'];
        $sub_group_name_options = "";
        $msg = "";
        if($category == 1){
            $sub_group_name_options  = category_name("","",$group_id);
            $msg = "Select the Category Name";
        } else {
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select the Sub Group Name";
        }
        $sub_group_name_options  = select_option($sub_group_name_options,$msg);
        echo $sub_group_name_options;
        
        break;
    
    default:
        
        break;
}


// function batch_creation($table_name, $where, $category_unique_id, $prefixs, $conn) {
  
//   // Fetch the last batch ID for the given item_name
//   $stmt = $conn->prepare("SELECT * FROM $table_name WHERE category_unique_id = :category ORDER BY id DESC LIMIT 1");
//   $stmt->execute([':category' => $category_unique_id]);
  
//   // Fetch the results
//   if ($pit_query = $stmt->fetch(PDO::FETCH_ASSOC)) {
//       // Create new batch ID with the prefix
//       $billno = $prefixs;

//       // Generate a sequential ID
//       $bill_order_no = generate_order_number($table_name, $conn, $prefixs);

//       // Append the generated number to the prefix
//       $billno .= sprintf("%05d", $bill_order_no);
//       return $billno;
//   } else {
//       // Create new batch ID with the prefix
//       $billno = $prefixs;

//       // Generate a sequential ID
//       $bill_order_no = generate_order_number($table_name, $conn, $prefixs);

//       // Append the generated number to the prefix
//       $billno .= sprintf("%05d", $bill_order_no);

//       return $billno;
//   }
// }

function generate_item_code($pdo, $table, $table_sub_group, $category_table, $group_id, $sub_group_id, $category_id) {
    // 1. Get sub_group code
    $result = $pdo->select([$table_sub_group, ["code"]], "unique_id = '$sub_group_id' AND is_delete = 0");
    $sub_group_code = ($result->status && !empty($result->data)) ? $result->data[0]['code'] : '';

    // 2. Get category code
    $result = $pdo->select([$category_table, ["code"]], "unique_id = '$category_id' AND is_delete = 0");
    $category_code = ($result->status && !empty($result->data)) ? $result->data[0]['code'] : '';

    // 3. Build prefix
    $prefix = $sub_group_code . "-" . $category_code . "-";

    // 4. Find max existing item_code for same group + sub_group + category
    $columns = ["MAX(item_code) as max_code"];
    $where   = "group_unique_id = '$group_id' 
                AND sub_group_unique_id = '$sub_group_id' 
                AND category_unique_id = '$category_id' 
                AND is_delete = 0 
                AND item_code LIKE '$prefix%'";
    $result  = $pdo->select([$table, $columns], $where);

    $max_num = 0;
    if ($result->status && !empty($result->data) && $result->data[0]['max_code']) {
        $last_code = $result->data[0]['max_code'];
        $numeric_part = intval(substr($last_code, strlen($prefix)));
        $max_num = $numeric_part;
    }

    // 5. Increment and return final code
    $new_num = $max_num + 1;
    return $prefix . sprintf("%05d", $new_num);
}


function generate_order_number($table_name, $conn, $prefix) {
  // Query the database to find the highest existing number for the given prefix and increment it by one
  $stmt = $conn->prepare("SELECT MAX(item_code) AS max_id FROM $table_name WHERE item_code LIKE :prefix and is_delete = 0");
  $stmt->execute([':prefix' => $prefix . '%']);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Extract the numeric part of the batch_id and increment it
  $max_id = isset($result['max_id']) ? intval(substr($result['max_id'], strlen($prefix))) : 0;
  $new_order_number = $max_id + 1;

  return $new_order_number;
}
?>