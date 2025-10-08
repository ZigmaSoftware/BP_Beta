<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = "purchase_requisition";
$sub_list_table     = "purchase_requisition_items";

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
    case "requisition_sub_add_update":

    $main_unique_id         = $_POST["main_unique_id"];
    $sublist_unique_id      = $_POST["sublist_unique_id"];

    $item_code              = $_POST["item_code"];
    $item_description       = $_POST["item_description"];
    $quantity               = $_POST["quantity"];
    $uom                    = $_POST["uom"];
    // $preferred_vendor_id    = $_POST["preferred_vendor_id"];
    // $budgetary_rate         = $_POST["budgetary_rate"];
    $item_remarks           = $_POST["item_remarks"];
    $required_delivery_date = $_POST["required_delivery_date"];

    $columns = [
        "main_unique_id"         => $main_unique_id,
        "item_code"              => $item_code,
        "item_description"       => $item_description,
        "quantity"               => $quantity,
        "uom"                    => $uom,
        // "preferred_vendor_id"    => $preferred_vendor_id ?: null,
        // "budgetary_rate"         => $budgetary_rate,
        "item_remarks"           => $item_remarks,
        "required_delivery_date" => $required_delivery_date
    ];

    if (!empty($sublist_unique_id)) {
        // Update existing sublist row
        $columns["updated"] = $date;
        $columns["updated_user_id"] = $user_id;

        $where = ["unique_id" => $sublist_unique_id];
        $action_obj = $pdo->update($sub_list_table, $columns, $where);
        $msg = "update";
    } else {
        // Insert new sublist row
        $columns["unique_id"] = unique_id();
        $columns["created"]   = $date;
        $columns["created_user_id"] = $user_id;

        $action_obj = $pdo->insert($sub_list_table, $columns);
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


    
    
        
   case "createupdate":

    $company_id         = $_POST["company_id"];
    $project_id         = $_POST["project_id"];
    $service_type       = $_POST["service_type"];
    $requisition_for    = $_POST["requisition_for"];
    $requisition_type   = $_POST["requisition_type"];
    $requisition_date   = $_POST["requisition_date"];
    $requested_by       = $_SESSION['user_name'];
    $sales_order_id     = ($requisition_for === 'SO') ? $_POST["sales_order_id"] : null;
    $remarks            = $_POST["remarks"];
    $unique_id          = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();

    $columns = [
        "unique_id"          => $unique_id,
        "company_id"         => $company_id,
        "project_id"         => $project_id,
        "service_type"       => $service_type,
        "requisition_for"    => $requisition_for,
        "requisition_type"   => $requisition_type,
        "requisition_date"   => $requisition_date,
        "requested_by"       => $requested_by,
        "sales_order_id"     => $sales_order_id,
        "remarks"            => $remarks,
        "created_user_id"    => $user_id,
        "created"            => $date
    ];

    // Check if it exists
    $check_query = [$table, ["COUNT(unique_id) AS count"]];
    $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

    $action_obj = $pdo->select($check_query, $check_where);

    if ($action_obj->status && $action_obj->data[0]["count"]) {
        // Update mode — do NOT change pr_number
        unset($columns["unique_id"], $columns["created_user_id"], $columns["created"]);
        $columns["updated_user_id"] = $user_id;
        $columns["updated"]         = $date;

        $update_where = ["unique_id" => $unique_id];
        $action_obj   = $pdo->update($table, $columns, $update_where);
        $msg          = "update";
    } else {
        // INSERT mode — generate PR number
        $company_code_arr = company_code("", $company_id);
        $company_code = $company_code_arr[0]["company_code"];

        $acc_year = $_SESSION["acc_year"];
        $prefix = "PR/{$company_code}/{$acc_year}/";

        // Get last PR number
        $pr_no_result = $pdo->select(
            [$table, ["pr_number"]],
            "pr_number LIKE '{$prefix}%' ORDER BY pr_number DESC LIMIT 1"
        );

        $next_number = "001";
        if ($pr_no_result->status && !empty($pr_no_result->data)) {
            $last_pr = $pr_no_result->data[0]["pr_number"];
            $last_split = explode("/", $last_pr);
            $last_num = isset($last_split[3]) ? (int)$last_split[3] : 0;
            $next_number = str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
        }

        $columns["pr_number"] = "{$prefix}{$next_number}";

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

		$data	    = [];
		

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no", 
            "pr_number",
            "company_id",
            "project_id",
            // "service_type",
            "requisition_for",
            "requisition_type",
            "requisition_date",
            "requested_by",
            "remarks",
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


if (!empty($_POST['pr_number'])) {
    $pr_number = trim($_POST['pr_number']);
    $where .= " AND unique_id = '$pr_number'";
}

// echo $_POST['pr_number'];

if (!empty($_POST['company_name'])) {
    $company_name = trim($_POST['company_name']);
    $where .= " AND company_id = '$company_name'"; // Assuming company_name is actually a company_id in DB
}

if (!empty($_POST['project_name'])) {
    $project_name = trim($_POST['project_name']);
    $where .= " AND project_id = '$project_name'"; // Assuming project_name is actually a project_id
}

if (!empty($_POST['type_of_service'])) {
    $type_of_service = trim($_POST['type_of_service']);
    $where .= " AND service_type = '$type_of_service'";
}

if (!empty($_POST['requisition_for'])) {
    $requisition_for = trim($_POST['requisition_for']);
    $where .= " AND requisition_for = '$requisition_for'";
}

if (!empty($_POST['requisition_date'])) {
    $requisition_date = trim($_POST['requisition_date']);
    $where .= " AND requisition_date = '$requisition_date'";
}

        
        // if (!empty($_POST['group_unique_id']) && !empty($_POST['sub_group_unique_id']) && !empty($_POST['category_unique_id'])) {
        //     $where .= " AND group_unique_id = '{$_POST['group_unique_id']}' AND sub_group_unique_id = '{$_POST['sub_group_unique_id']}' AND category_unique_id = '{$_POST['category_unique_id']}'";
        // } else {
        //     $conditions = [];
        
        //     if (!empty($_POST['data_type'])) {
        //         $conditions[] = "data_type = '{$_POST['data_type']}'";
        //     }
        //     if (!empty($_POST['group_unique_id'])) {
        //         $conditions[] = "group_unique_id = '{$_POST['group_unique_id']}'";
        //     }
        //     if (!empty($_POST['sub_group_unique_id'])) {
        //         $conditions[] = "sub_group_unique_id = '{$_POST['sub_group_unique_id']}'";
        //     }
        //     if (!empty($_POST['category_unique_id'])) {
        //         $conditions[] = "category_unique_id = '{$_POST['category_unique_id']}'";
        //     }
        
        //     if (!empty($conditions)) {
        //         $where .= " AND " . implode(" AND ", $conditions);
        //     }
        // }
        
        $requisition_type_options = [
            1 => [
                "unique_id" => "1",
                "value"     => "Regular"
            ],
            2 => [
                "unique_id" => "2",
                "value"     => "Service"
            ],
            3 => [
                "unique_id" => "3",
                "value"     => "Capital"
            ]
        ];
        
        $requisition_for_options = [
            1 => [
                "unique_id" => "1",
                "value"     => "Direct"
            ],
            2 => [
                "unique_id" => "2",
                "value"     => "SO"
            ],
            3 => [
                "unique_id" => "3",
                "value"     => "Planning WO"
            ]
        ];
        
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
                
                // $sub_group_data                 = sub_group_name($value['sub_group_unique_id']);
                
                
                $company_data                   = company_name($value['company_id']);
                $value['company_id']            = $company_data[0]['company_name'];
                
                
                $company_data                   = project_name($value['project_id']);
                $value['project_id']            = $company_data[0]['project_name'];
                
                // $prc_data                       = purchase_requisition_category($value['service_type']);
                // $value['service_type']          = $prc_data[0]['purchase_requisition_category'];
                
                
                $value['requisition_for']       = $requisition_for_options[$value['requisition_for']]['value'];
                $value['requisition_type']      = $requisition_type_options[$value['requisition_type']]['value'];
                
                
                // $value['sub_group_unique_id']   = !empty($sub_group_data[0]['sub_group_name']) ? $sub_group_data[0]['sub_group_name'] : '-';
                
                // if($value['description'] == ' ' || empty($value['description']) || $value['description'] == ''){
                //     $description = '-';
                // } else {
                //     $description = $value['description'];
                // }
                
                // $value['description']           = $description;
                // $value['is_active']             = is_active_show($value['is_active']);
                $btn_update                     = btn_update($folder_name,$value['unique_id']);
                $btn_delete                     = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']             = $btn_update.$btn_delete;
                $data[]             = array_values($value);
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
    

    
    
    

    
   

        case 'project_name':

        $company_id          = $_POST['company_id'];

        $project_name_options  = get_project_name("",$company_id);

        $project_name_options  = select_option($project_name_options,"Select the Project Name");

        echo $project_name_options;
        
        break;
case "purchase_sublist_datatable":
    $main_unique_id = $_POST["main_unique_id"];
    $btn_prefix     = "pr_sub";

    $columns = [
        "@a:=@a+1 as s_no",
        "item_code",
        "item_description",
        "quantity",
        "uom",
        // "preferred_vendor_id",
        // "budgetary_rate",
        "item_remarks",
        "required_delivery_date",
        "unique_id"
    ];

    $table_details = [
        "purchase_requisition_items, (SELECT @a:=0) as a",
        $columns
    ];

    $where = [
        "main_unique_id" => $main_unique_id,
        "is_delete" => 0
    ];

    $result = $pdo->select($table_details, $where);
    // print_r($result);
    // die();
    $data = [];

    if ($result->status) {
        foreach ($result->data as $row) {
$item_data = item_name_list($row["item_code"]);
$row["item_code"] = isset($item_data[0]["item_name"]) && isset($item_data[0]["item_code"])
    ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
    : "-";
           $uom_data = unit_name($row["uom"]);
        $row["uom"] = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";
    
            // $row["item_code"] = $item_data[0]["text"]; // This returns just the string: "item_name / item_code"
// echo "<pre>";
// print_r($row["item_code"]);
// echo "</pre>";
// die();

            // $vendor_data = supplier($row["preferred_vendor_id"]);
            // $row["preferred_vendor_id"] = $vendor_data ?: "-";

            $edit = btn_edit($btn_prefix, $row["unique_id"]);
            $del  = btn_delete($btn_prefix, $row["unique_id"]);

            $row["unique_id"] = $edit . $del;
            $data[] = array_values($row);
        }
//         echo "<pre>";
// print_r($row);
// echo "</pre>";
// die();

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

case "pr_sub_edit":
    $unique_id = $_POST["unique_id"];

    $columns = [
        "unique_id",
        "main_unique_id",
        "item_code",
        "item_description",
        "quantity",
        "uom",
        // "preferred_vendor_id",
        // "budgetary_rate",
        "item_remarks",
        "required_delivery_date"
    ];

    $table_details = [
        "purchase_requisition_items",
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
case "pr_sub_delete":
    $unique_id = $_POST["unique_id"];

    $columns = [
        "is_delete" => 1
    ];
    $where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update("purchase_requisition_items", $columns, $where);

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $action_obj->status ? "delete_success" : "delete_error",
        "error"  => $action_obj->error,
        "sql"    => $action_obj->sql
    ]);
    break;
    
case "get_item_details_by_code":
    $item_code = $_POST["item_code"];

    $table = "item_master";
    $columns = ["description", "uom_unique_id"];
    $where = ["unique_id" => $item_code, "is_delete" => 0];

    $result = $pdo->select([$table, $columns], $where);

    if ($result->status && !empty($result->data)) {
        $description = $result->data[0]['description'];
        $uom_id = $result->data[0]['uom_unique_id'];

        // Convert uom_unique_id to readable UOM name
        $uom_data = unit_name($uom_id);
        $uom_name = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";

echo json_encode([
    "status" => true,
    "data" => [
        "description" => $description,
        "uom" => $uom_name,      // For display
        "uom_id" => $uom_id      // For saving
    ]
]);

    } else {
        echo json_encode([
            "status" => false,
            "error" => "Item not found"
        ]);
    }
    break;
    case "get_items_by_group":
    $group_id = $_POST["group_id"];

    $excluded_ids = ["683568ca2fe8263239", "683588840086c13657"]; // Service, Capital
    $table = "item_master";
    $columns = ["unique_id", "item_name", "item_code"];
    $where = "is_delete = 0";

    if ($group_id === "1") {
        // Regular: Exclude Service & Capital
        $where .= " AND group_unique_id NOT IN ('" . implode("','", $excluded_ids) . "')";
    } elseif ($group_id === "all") {
        // Capital: show all items (no additional filter)
        // keep $where = "is_delete = 0"
    } else {
        // Specific group match (like Service)
        $where .= " AND group_unique_id = '$group_id'";
    }

    $result = $pdo->select([$table, $columns], $where);
    $options = "<option value=''>Select the Item/Code</option>";

    if ($result->status && !empty($result->data)) {
        foreach ($result->data as $row) {
            $text = $row['item_name'] . " / " . $row['item_code'];
            $options .= "<option value='{$row['unique_id']}'>$text</option>";
        }
    } else {
        $options .= "<option value=''>No items found</option>";
    }

    echo $options;
    break;

case "get_items_by_sales_order":
    $sales_order_id = $_POST["sales_order_id"];
    $sales_order_table = "sales_order_sublist";
    $product_table = "product_master";
    $product_sublist_table = "product_sublist";
    $item_table = "item_master";

    $columns = ["item_name_id", "quantity"];
    $where = ["so_main_unique_id" => $sales_order_id, "is_delete" => 0];

    $result = $pdo->select([$sales_order_table, $columns], $where);
    $options = "<option value=''>Select the Item/Code</option>";

    if ($result->status && !empty($result->data)) {
        foreach ($result->data as $row) {
            $item_name_id = $row["item_name_id"];
            $qty          = $row["quantity"];

            // Step 1: Get screen_unique_id from product_master
            $product_res = $pdo->select([$product_table, ["screen_unique_id"]], ["unique_id" => $item_name_id]);
            if (!$product_res->status || empty($product_res->data)) continue;

            $screen_id = $product_res->data[0]["screen_unique_id"];

            // Step 2: Get item_unique_id from product_sublist
            $sublist_res = $pdo->select([$product_sublist_table, ["item_unique_id"]], [
                "screen_unique_id" => $screen_id,
                "is_delete" => 0
            ]);

            if (!$sublist_res->status || empty($sublist_res->data)) continue;

            foreach ($sublist_res->data as $subrow) {
                $item_unique_id = $subrow["item_unique_id"];

                // Step 3: Get item details from item_master
                $item_res = $pdo->select([$item_table, ["item_name", "item_code"]], [
                    "unique_id" => $item_unique_id,
                    "is_delete" => 0
                ]);

                if ($item_res->status && !empty($item_res->data)) {
                    $item = $item_res->data[0];
                    $text = "{$item['item_name']} / {$item['item_code']} (Qty: $qty)";
                    $options .= "<option value='$item_unique_id'>$text</option>";
                }
            }
        }
    } else {
        $options .= "<option value=''>No items found</option>";
    }

    echo $options;
    break;




    
    default:
        
        break;
}


?>