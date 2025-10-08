<?php
include '../../config/dbconfig.php';
include '../../config/new_db.php';

$table              = "purchase_order";
$sub_list_table     = "purchase_order_items";
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

$action             = $_POST["action"];
$user_id            = $_SESSION['sess_user_id'];
$date               = date("Y-m-d H:i:s");

$acc_year           = $_SESSION['acc_year'];
$session_id         = session_id();
$sess_user_type     = $_SESSION['sess_user_type'];
$sess_user_id       = $_SESSION['sess_user_id'];
$sess_company_id    = $_SESSION['sess_company_id'];
$sess_branch_id     = $_SESSION['sess_branch_id'];

switch ($action) {

    case "createupdate":
        $screen_unique_id = $_POST["screen_unique_id"];
        $unique_id        = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();
        $company_id       = $_POST["company_id"];
        // Company Name from company_creation
        $company_query = $pdo->select(["company_creation", ["company_name"]], ["unique_id" =>$company_id, "is_delete" => 0]);
        $company_name = ($company_query->status && !empty($company_query->data)) ? $company_query->data[0]["company_name"] : "NA";
        $company_name_clean = preg_replace('/[^A-Za-z0-9]/', '', $company_name);

        // Check if record exists
        $exists = $pdo->select([$table, ["COUNT(*) AS count"]], "unique_id = '$unique_id' AND is_delete = 0");

        // Generate PO Number only on create
        if (!$exists->status || !$exists->data[0]["count"]) {
            $po_prefix = "PO/$company_name_clean/$acc_year/";
            $serial_query = "SELECT COUNT(*) AS po_count FROM $table WHERE sess_company_id = '$company_id' AND is_delete = 0";
            $serial_result = $pdo->query($serial_query);
            $next_serial = str_pad(($serial_result->data[0]["po_count"] ?? 0) + 1, 3, "0", STR_PAD_LEFT);
            $purchase_order_no = $po_prefix . $next_serial;
        }

        $columns = [
            "unique_id"                 => $unique_id,
            "screen_unique_id"         => $screen_unique_id,
            "company_id"               => $_POST["company_id"],
            "project_id"               => $_POST["project_id"],
            "supplier_id"              => $_POST["supplier_id"],
            "branch_id"                => $_POST["branch_id"],
            "purchase_request_no"      => $_POST["purchase_request_no"],
            "entry_date"               => $_POST["entry_date"],
            "purchase_type"            => $_POST["purchase_type"],
            "net_amount"               => $_POST["net_amount"],
            "freight_percentage"       => $_POST["freight_percentage"],
            "freight_amount"           => $_POST["freight_amount"],
            "other_charges"            => $_POST["other_charges"],
            "other_tax"                => $_POST["other_tax"],
            "other_charges_percentage" => $_POST["other_charges_percentage"],
            "tcs_percentage"           => $_POST["tcs_percentage"],
            "tcs_amount"               => $_POST["tcs_amount"],
            "round_off"                => $_POST["round_off"],
            "gross_amount"             => $_POST["gross_amount"],
            "contact_person"           => $_POST["contact_person"],
            "quote_no"                 => $_POST["quote_no"],
            "quote_date"               => $_POST["quote_date"],
            "delivery"                 => $_POST["delivery"],
            "ship_via"                 => $_POST["ship_via"],
            "delivery_term_fright"     => $_POST["delivery_term_fright"],
            "delivery_site"            => $_POST["delivery_site"],
            "payment_days"             => $_POST["payment_days"],
            "dealer_reference"         => $_POST["dealer_reference"],
            "document_throught"        => $_POST["document_throught"],
            "billing_address"          => $_POST["billing_address"],
            "billing_information"      => $_POST["billing_information"],
            "approve_status"           => $_POST["approve_status"],
            "acc_year"                 => $acc_year,
            "session_id"               => $session_id,
            "sess_user_type"           => $sess_user_type,
            "sess_user_id"             => $sess_user_id,
            "sess_company_id"          => $sess_company_id,
            "sess_branch_id"           => $sess_branch_id,
            "created_user_id"          => $user_id,
            "created"                  => $date
        ];

        if (!$exists->status || !$exists->data[0]["count"]) {
            $columns["purchase_order_no"] = $purchase_order_no;
            $action_obj = $pdo->insert($table, $columns);
            $msg = "create";
        } else {
            unset($columns["unique_id"], $columns["created_user_id"], $columns["created"], $columns["purchase_order_no"]);
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;
            $action_obj = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
            $msg = "update";
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
            "purchase_order_no",
            "company_id",
            "project_id",
            "entry_date",
            "net_amount",
            "gross_amount",
            // "requisition_date",
            // "requested_by",
            // "remarks",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        // $where          = [
        //     "is_delete"     => 0
        // ];

        $where = " is_delete = '0' ";
            $conditions = [];
            
            if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
                $conditions[] = "DATE(created) >= '{$_POST['from_date']}' AND DATE(created) <= '{$_POST['to_date']}'";
            }
            if (!empty($_POST['company_name'])) {
                $conditions[] = "company_id = '{$_POST['company_name']}'";
            }
            if (!empty($_POST['project_name'])) {
                $conditions[] = "project_id = '{$_POST['project_name']}'";
            }
           
        
            if (!empty($conditions)) {
                $where .= " AND " . implode(" AND ", $conditions);
            }
        
        // $requisition_type_options = [
        //     1 => [
        //         "unique_id" => "1",
        //         "value"     => "Regular"
        //     ],
        //     2 => [
        //         "unique_id" => "2",
        //         "value"     => "Service"
        //     ],
        //     3 => [
        //         "unique_id" => "3",
        //         "value"     => "Capital"
        //     ]
        // ];
        
        // $requisition_for_options = [
        //     1 => [
        //         "unique_id" => "1",
        //         "value"     => "Direct"
        //     ],
        //     2 => [
        //         "unique_id" => "2",
        //         "value"     => "SO"
        //     ],
        //     3 => [
        //         "unique_id" => "3",
        //         "value"     => "Planning WO"
        //     ]
        // ];
        
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
                
                
                // $value['requisition_for']       = $requisition_for_options[$value['requisition_for']]['value'];
                // $value['requisition_type']      = $requisition_type_options[$value['requisition_type']]['value'];
                
                
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

    // Sublist Add/Update (by screen_unique_id)
    case "po_sub_add_update":
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id = $_POST["sublist_unique_id"];

        $columns = [
            "screen_unique_id"   => $screen_unique_id,
            "item_code"          => $_POST["item_code"],
            "quantity"           => $_POST["quantity"],
            "uom"                => $_POST["uom"],
            "rate"               => $_POST["rate"],
            "discount"           => $_POST["discount"],
            "tax"                => $_POST["tax"],
            "amount"             => $_POST["amount"],
            "acc_year"           => $acc_year,
            "session_id"         => $session_id,
            "sess_user_type"     => $sess_user_type,
            "sess_user_id"       => $sess_user_id,
            "sess_company_id"    => $sess_company_id,
            "sess_branch_id"     => $sess_branch_id
        ];

        if (!empty($sublist_unique_id)) {
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;
            $action_obj = $pdo->update($sub_list_table, $columns, ["unique_id" => $sublist_unique_id]);
            $msg = "update";
        } else {
            $columns["unique_id"] = unique_id();
            $columns["created_user_id"] = $user_id;
            $columns["created"] = $date;
            $action_obj = $pdo->insert($sub_list_table, $columns);
            $msg = "add";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
        break;
        
     case "po_sub_add_update_modal":
        $screen_unique_id = $_POST["screen_unique_id"];
        $sublist_unique_id = $_POST["sublist_unique_id"];
        $pr_unique_id = $_POST["pr_unique_id"];

        $columns = [
            "screen_unique_id"   => $screen_unique_id,
            "item_code"          => $_POST["item_code"],
            "quantity"           => $_POST["quantity"],
            "uom"                => $_POST["uom"],
        ];

        if (!empty($sublist_unique_id)) {
            $columns["updated_user_id"] = $user_id;
            $columns["updated"] = $date;
            $action_obj = $pdo->update($sub_list_table, $columns, ["unique_id" => $sublist_unique_id]);
            $coulum_pr["po_add_item"] = 1;
            $action_pr = $pdo->update('purchase_requisition_items',$coulum_pr,["unique_id" => $pr_unique_id]);
            $msg = "update";
        } else {
            $columns["unique_id"] = unique_id();
            $columns["created_user_id"] = $user_id;
            $columns["created"] = $date;
            
            
            $action_obj = $pdo->insert($sub_list_table, $columns);
            $coulum_pr["po_add_item"] = 1;
            $action_pr = $pdo->update('purchase_requisition_items',$coulum_pr,["unique_id" => $pr_unique_id]);
            
            $msg = "add";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
        break;

    // Sublist List via screen_unique_id
    case "purchase_order_sublist_datatable":
        $screen_unique_id = $_POST["screen_unique_id"];
        $btn_prefix = "po_sub";

        $columns = [
            "@a:=@a+1 as s_no",
            "item_code",
            // "quantity",
            "uom",
            "quantity",
            "rate",
            "discount",
            "tax",
            "amount",
            "unique_id"
        ];

        $table_details = ["purchase_order_items, (SELECT @a:=0) AS a", $columns];
        $where = ["screen_unique_id" => $screen_unique_id, "is_delete" => 0];

        $result = $pdo->select($table_details, $where);
        $data = [];

        if ($result->status) {
            foreach ($result->data as $row) {
                $item_data = item_name_list($row["item_code"]);
                $row["item_code"] = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];

                $edit = btn_edit($btn_prefix, $row["unique_id"]);
                $del  = btn_delete($btn_prefix, $row["unique_id"]);

                $row["unique_id"] = $edit . $del;
                $data[] = array_values($row);
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

    // Sublist Edit
    case "po_sub_edit":
        $unique_id = $_POST["unique_id"];
        $columns = [
            "unique_id",
            "screen_unique_id",
            "item_code",
            "quantity",
            "uom",
            "rate",
            "discount",
            "tax",
            "amount"
        ];
        $where = ["unique_id" => $unique_id, "is_delete" => 0];
        $result = $pdo->select([$sub_list_table, $columns], $where);

        echo json_encode([
            "status" => $result->status,
            "data"   => $result->status ? $result->data[0] : [],
            "msg"    => $result->status ? "edit_data" : "error",
            "error"  => $result->error
        ]);
        break;

    // Sublist Delete
    case "po_sub_delete":
        $unique_id = $_POST["unique_id"];
        $columns = ["is_delete" => 1];
        $where = ["unique_id" => $unique_id];
        $action_obj = $pdo->update($sub_list_table, $columns, $where);

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "delete_success" : "delete_error",
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);
        break;

    // Item Details for Select2
    case "get_item_details_by_code":
        $item_code = $_POST["item_code"];
        $columns = ["description", "uom_unique_id"];
        $where = ["unique_id" => $item_code, "is_delete" => 0];
        $result = $pdo->select(["item_master", $columns], $where);

        if ($result->status && !empty($result->data)) {
            $description = $result->data[0]['description'];
            $uom_id = $result->data[0]['uom_unique_id'];
            $uom_data = unit_name($uom_id);
            $uom_name = !empty($uom_data[0]['unit_name']) ? $uom_data[0]['unit_name'] : "";

            echo json_encode([
                "status" => true,
                "data" => [
                    "description" => $description,
                    "uom" => $uom_name
                ]
            ]);
        } else {
            echo json_encode([
                "status" => false,
                "error" => "Item not found"
            ]);
        }
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

    case "get_pr_sublist":
        $company_id = $_POST["company_id"];
        $project_id = $_POST["project_id"];
        
        $table     = "purchase_requisition_items as sub join purchase_requisition as main on main.unique_id = sub.main_unique_id ";
        $columns   = [
            "main.pr_number AS pr_number",
            "sub.item_code",
            "sub.item_description",
            "sub.quantity",
            "sub.uom",
            "sub.required_delivery_date",
            "sub.unique_id",
            "sub.item_code as item_id",
        ];
        
        $where     = "sub.main_unique_id != '' and  main.company_id ='".$company_id."' and  main.project_id ='".$project_id."' and main.is_delete = 0 and sub.is_delete = 0 and sub.po_add_item = 0 ";

        $result = $pdo->select([$table, $columns], $where);
       // print_r($result);
        if ($result->status) {

            echo "<table class='table table-bordered'>";
            echo "<thead><tr><th>#</th><th>PR Number</th><th>Item</th><th>Item Description</th><th>Qty</th><th>UOM</th><th>Delivery Date</th><th>Action</th></tr></thead><tbody>";
            $i = 1;
            foreach ($result->data as $row) {
                $item_data = item_name_list($row["item_code"]);
                $row["item_code"] = isset($item_data[0]["item_name"]) && isset($item_data[0]["item_code"])
                    ? $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"]
                    : "-";
                    $unit_details = unit_name($row['uom']);
                    $unit = $unit_details[0]['unit_name'];
                    $delivery_date = disdate($row['required_delivery_date']);
                echo "<tr>";
                echo "<td>{$i}</td>";
                echo "<td>{$row['pr_number']}</td>";
                echo "<td>{$row['item_code']}</td>";
                echo "<td>{$row['item_description']}</td>";
                echo "<td>{$row['quantity']}</td>";
                echo "<td>{$unit}</td>";
                echo "<td>{$delivery_date}</td>";
                echo "<td> <button id= 'sub_add' class='btn btn-success po_sublist_add_modal_btn' 
                onclick=\"po_sublist_add_update_pop_up('{$row['item_id']}','{$row['uom']}', '{$row['quantity']}','{$row['unique_id']}')\">Add</button>&nbsp;<button class='btn btn-danger'> Cancel</button></td>";
                echo "</tr>";
                $i++;
            }
            echo "</tbody></table>";
        } else {
            echo "<div class='text-danger'>No sublist found for this PR number.</div>";
        }
        break;

    case 'project_name':
        $company_id          = $_POST['company_id'];
        $project_name_options  = get_project_name("", $company_id);
        $project_name_options  = select_option($project_name_options, "Select the Project Name");
        echo $project_name_options;
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
        echo json_encode(["status" => false, "error" => "Invalid action"]);
        break;
}
