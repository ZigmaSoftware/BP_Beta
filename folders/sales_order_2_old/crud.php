<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = 'sales_order'; 
$sub_table          = 'sales_order_sublist'; 

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include '../../config/new_db.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$random_sc          = "";
$random_no          = "";
$sub_group_unique_id= "";
$product_name       = "";
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
    
    case "createupdate":
        $customer_name          = $_POST["customer_name"];
        $currency               = $_POST["currency"];
        $exchange_rate          = $_POST["exchange_rate"];
        $contact_person_name    = $_POST["contact_person_name"];
        $customer_po_no         = $_POST["customer_po_no"];
        $customer_po_date       = $_POST["customer_po_date"];
        $status                 = $_POST["status"];
        $company_name           = $_POST["company_name"];
        $entry_date             = $_POST["entry_date"];
        $unique_id              = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();
    
       
        $columns = [
            "unique_id"             => $unique_id,
            "customer_id"           => $customer_name,
            "currency_id"           => $currency,
            "exchange_rate"         => $exchange_rate,
            "contact_person_name"   => $contact_person_name,
            "customer_po_no"        => $customer_po_no,
            "customer_po_date"      => $customer_po_date,
            "company_id"            => $company_name,
            "entry_date"            => $entry_date,
            "status"                => $status,
            "created_user_id"       => $user_id,
            "created"               => $date
        ];
    
        // Check if it exists
        $check_query = [$table, ["COUNT(unique_id) AS count"]];
        $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';
    
        $action_obj = $pdo->select($check_query, $check_where);
    
        if ($action_obj->status && $action_obj->data[0]["count"]) {
            // Update mode — do NOT change pr_number
            unset($columns["unique_id"], $columns["created_user_id"], $columns["created"], $columns["customer_id"], $columns["company_id"], $columns["entry_date"]);
            $columns["updated_user_id"] = $user_id;
            $columns["updated"]         = $date;
    
            $update_where = ["unique_id" => $unique_id];
            $action_obj   = $pdo->update($table, $columns, $update_where);
            $msg          = "update";
        } else {
            // INSERT mode — generate PR number
            $company_code_arr = "";
            $company_code = "";
            $prefix = "";
            $acc_year = "";
            $company_code_arr = company_code("", "$company_name");
            $company_code = $company_code_arr[0]["company_code"];
    
            $acc_year = $_SESSION["acc_year"];
            $prefix = "SO/{$company_code}/{$acc_year}/";
    
            $bill_no = batch_creation($table, $company_name, $prefix , $conns);
            // echo $bill_no;
            $columns["sales_order_no"] = $bill_no;
    
            // Now insert
            $action_obj = $pdo->insert($table, $columns);
            $msg        = "create";
        }
    
        echo json_encode([
            "status" => $action_obj->status,
            "data"   => ["unique_id" => $unique_id],
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
    break;





    case 'datatable':

        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;
		
		$from_date 		= $_POST['from_date'];
		$to_date 		= $_POST['to_date'];
		$company_name 	= $_POST['company_name'];
		$customer_name 	= $_POST['customer_name'];
		$status 		= $_POST['status'];

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no", 
            "entry_date",
            "sales_order_no",
            "company_id",
            "customer_id",
            "status",
            "unique_id",
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        $where = " is_delete = '0' ";
        
       
            $conditions = [];
        
            if (!empty($from_date) && !empty($to_date)) {
                $conditions[] = "entry_date >= '{$from_date}' AND entry_date <= '{$to_date}'";
            }
            if (!empty($company_name)) {
                $conditions[] = "company_id = '{$company_name}'";
            }
            if (!empty($customer_name)) {
                $conditions[] = "customer_id = '{$customer_name}'";
            }
            if (!empty($status)) {
                $conditions[] = "status = '{$status}'";
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
        
        $approve_status_options = [
            1 => [
                "unique_id" => "1",
                "value"     => "Not Completed"
            ],
            2 => [
                "unique_id" => "2",
                "value"     => "Completed"
            ]
        ];
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();
        
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                
                $company_data                   = company_name($value['company_id']);
                $value['company_id']            = $company_data[0]['company_name'];
                
                $customer_data                  = supplier($value['customer_id']);
                $value['customer_id']           = $customer_data[0]['supplier_name'];
                $value['status']                = $approve_status_options[$value['status']]['value'];
                
                
                $btn_update                     = btn_update($folder_name,$value['unique_id']);
                $btn_delete                     = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']             = $btn_update.$btn_delete;
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
    
    
    case 'sub_group_name':

        $group_id = $_POST['group_id'];
        $type = $_POST['type'];
        $sub_group_name_options = "";
        $msg = "";
        if($type == 1){
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select";
        } else if($type == 2){
            $sub_group_name_options  = category_name("",$group_id);
            $msg = "Select";
        } else if($type == 3){
            $sub_group_name_options  = category_item("",$group_id);
            $msg = "Select";
        } else {
            $sub_group_name_options  = sub_group_name("",$group_id);
            $msg = "Select";
        }
        $sub_group_name_options  = select_option($sub_group_name_options,$msg);
        echo $sub_group_name_options;
        
        break;
        
        
    case "so_sub_add_update":

        $main_unique_id         = $_POST["main_unique_id"];
        $sublist_unique_id      = $_POST["sublist_unique_id"];
        
        $product_unique_id      = $_POST["product_unique_id"];
        $uom                    = $_POST["uom"];
        $qty                    = $_POST["qty"];
        $rate                   = $_POST["rate"];
        $discount               = $_POST["discount"];
        $tax                    = $_POST["tax"];
        $amount                 = $_POST["amount"];
        
        $columns = [
            "so_main_unique_id"     => $main_unique_id,
            "item_name_id"          => $product_unique_id,
            "unit_name"             => $uom,
            "quantity"              => $qty,
            "rate"                  => $rate,
            "discount"              => $discount,
            "tax_id"                => $tax,
            "amount"                => $amount,
        ];
        
        if (!empty($sublist_unique_id)) {
            // Update existing sublist row
            $columns["updated"] = $date;
            $columns["updated_user_id"] = $user_id;
            
            $where = ["unique_id" => $sublist_unique_id];
            
            $action_obj = $pdo->update($sub_table, $columns, $where);
            $msg = "update";
        } else {
            // Insert new sublist row
            $columns["unique_id"] = unique_id();
            $columns["created"]   = $date;
            $columns["created_user_id"] = $user_id;
        
            $action_obj = $pdo->insert($sub_table, $columns);
            $msg = "add";
        }
        
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);

    break;
    
    case "so_sublist_datatable":
        $main_unique_id = $_POST["main_unique_id"];
        $btn_prefix     = "so_sub";
    
        $columns = [
            "@a:=@a+1 as s_no",
            "item_name_id",
            "unit_name",
            "quantity",
            "rate",
            "discount",
            "tax_id",
            "amount",
            "unique_id"
        ];
    
        $table_details = [
            "$sub_table, (SELECT @a:=0) as a",
            $columns
        ];
    
        $where = [
            "so_main_unique_id" => $main_unique_id,
            "is_delete" => 0
        ];
    
        $result = $pdo->select($table_details, $where);
        // print_r($result);
        // die();
        $data = [];
    
        if ($result->status) {
            foreach ($result->data as $row) {
                
                $product_data           = product_name($row['item_name_id']);
                $row['item_name_id']    = $product_data[0]['product_name']; 
                
                $unit_data              = unit_name($row['unit_name']);
                $row['unit_name']       = $unit_data[0]['unit_name']; 
                
                $tax_data               = tax($row['tax_id']);
                $row['tax_id']          = $tax_data[0]['tax_name']; 
                
                $edit                   = btn_edit($btn_prefix, $row["unique_id"]);
                $del                    = btn_delete($btn_prefix, $row["unique_id"]);
    
                $row["unique_id"]       = $edit . $del;
                $data[]                 = array_values($row);
            }
    
            echo json_encode([
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            ]);
        } else {
            echo json_encode([
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => [],
                "error" => $result->error
            ]);
        }
    break;

    case "so_sub_edit":
        $unique_id = $_POST["unique_id"];
    
        $columns = [
            "so_main_unique_id",
            "item_name_id",
            "unit_name",
            "quantity",
            "rate",
            "discount",
            "tax_id",
            "amount",
            "unique_id"
            
        ];
    
        $table_details = [
            $sub_table,
            $columns
        ];
    
        $where = [
            "unique_id" => $unique_id,
            "is_delete" => 0
        ];
    
        $result = $pdo->select($table_details, $where);
    
        echo json_encode([
            "status" => $result->status,
            "data"   => $result->status ? $result->data[0] : [],
            "msg"    => $result->status ? "edit_data" : "error",
            "error"  => $result->error
        ]);
    break;
    
    case "so_sub_delete":
        $unique_id = $_POST["unique_id"];
    
        $columns = [
            "is_delete" => 1
        ];
        $where = [
            "unique_id" => $unique_id
        ];
    
        $action_obj = $pdo->update("$sub_table", $columns, $where);
    
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "delete_success" : "delete_error",
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
    break;
    
    case 'get_tax_val':
        $unique_id = $_POST['code'];
        
        $json_array     = "";
        
        $tax_data       = tax($unique_id);
        
        if ($unique_id) {
            $json_array = [
                'status' => 'success',
                'data' => $tax_data[0]['tax_value']
                ];
            echo json_encode($json_array);
        } else {
            $json_array = [
                'status' => 'empty',
                'message' => 'No matching data found.'
            ];
            echo json_encode($json_array);
        }
       
        
        break;

    
    default:
        
        break;
}

function batch_creation($table_name, $company_unique_id, $prefixs, $conn) {
  
  // Fetch the last batch ID for the given item_name
  $stmt = $conn->prepare("SELECT * FROM $table_name WHERE company_id = :company ORDER BY id DESC LIMIT 1");
  $stmt->execute([':company' => $company_unique_id]);
  
  // Fetch the results
  if ($pit_query = $stmt->fetch(PDO::FETCH_ASSOC)) {
      // Create new batch ID with the prefix
      $billno = $prefixs;

      // Generate a sequential ID
      $bill_order_no = generate_order_number($table_name, $conn, $prefixs);

      // Append the generated number to the prefix
      $billno .= sprintf("%03d", $bill_order_no);
      return $billno;
  } else {
      // Create new batch ID with the prefix
      $billno = $prefixs;

      // Generate a sequential ID
      $bill_order_no = generate_order_number($table_name, $conn, $prefixs);

      // Append the generated number to the prefix
      $billno .= sprintf("%03d", $bill_order_no);

      return $billno;
  }
}

function generate_order_number($table_name, $conn, $prefix) {
  // Query the database to find the highest existing number for the given prefix and increment it by one
  $stmt = $conn->prepare("SELECT MAX(sales_order_no) AS max_id FROM $table_name WHERE sales_order_no LIKE :prefix and is_delete = 0");
  $stmt->execute([':prefix' => $prefix . '%']);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);

  // Extract the numeric part of the batch_id and increment it
  $max_id = isset($result['max_id']) ? intval(substr($result['max_id'], strlen($prefix))) : 0;
  $new_order_number = $max_id + 1;

  return $new_order_number;
}

?>