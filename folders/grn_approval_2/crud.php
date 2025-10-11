<?php 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];
$table              = 'grn'; 
$sub_table          = 'grn_sublist'; 
$documents_upload   = 'grn_uploads';

include '../../config/dbconfig.php';
include '../../config/new_db.php';

$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];

$json_array         = "";
$sql                = "";
$supplier_invoice_no    = "";
$mode_type              = "";
$payment_terms          = "";
$invoice_date           = "";
$inward_type            = "";
$pa_status              = "";
$dc_no                  = "";
$po_number              = "";
$branch                 = "";
$supplier_name          = "";
$po_status              = "";
$description            = "";
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
        $approve_status = $_POST['approve_status'];
        $status_remark  = $_POST['status_remark'];
        $sess_user_id   = $_POST['sess_user_id'];
        $unique_id      = $_POST['unique_id'];

        $response = [
            "status" => false,
            "data"   => ["unique_id" => $unique_id],
            "error"  => null,
            "msg"    => "Unexpected error"
        ];

        // Validate record existence
        $check_query = [$table, ["COUNT(*) AS count"]];
        $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

        $check_result = $pdo->select($check_query, $check_where);

        if (!$check_result->status || $check_result->data[0]["count"] == 0) {
            $response["error"] = "Record not found or already deleted";
            $response["msg"]   = "No valid GRN found to update";
            echo json_encode($response);
            break;
        }

        if ($approve_status == 3) {
            // Cancel logic
            $columns = ["is_delete" => 1];
            $update_where  = ["unique_id" => $unique_id];
            $update_result = $pdo->update($table, $columns, $update_where);

            $response["status"] = $update_result->status;
            $response["error"]  = $update_result->error;
            $response["msg"]    = $update_result->status ? "GRN cancelled successfully" : "GRN cancellation failed";
            $response["sql"]    = $update_result->sql ?? '';

            echo json_encode($response);
            break;
        }

        // Approve or Reject logic
        $columns = [
            "approve_status"   => $approve_status,
            "status_remark"    => $status_remark,
            "approved_by"      => $sess_user_id,
            "updated_user_id"  => $user_id,
            "updated"          => $date
        ];

        $update_where  = ["unique_id" => $unique_id];
        $update_result = $pdo->update($table, $columns, $update_where);

        $response["status"] = $update_result->status;
        $response["error"]  = $update_result->error;
        $response["msg"]    = $update_result->status 
            ? ($approve_status == 1 ? "Approved" : "Rejected") . " successfully"
            : "Approval update failed";
        $response["sql"]    = $update_result->sql ?? '';

        echo json_encode($response);
    break;
    
    case 'documents_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($documents_upload,$columns,$update_where);

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
    
    
    case 'documents_datatable':
        // Function Name button prefix
        $btn_edit_delete = "documents";

        // Fetch Data
        $upload_unique_id = $_POST['upload_unique_id']; 
        
        // DataTable Inputs
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data = [];

        if ($length == '-1') {
            $limit = "";
        }

        // SQL Column Selections
        $columns = [
            "@a:=@a+1 AS s_no",
            "type",
            "file_attach",
            "unique_id"
        ];

        $table_details = [
            "$documents_upload, (SELECT @a:=$start) AS a",
            $columns
        ];

        $where = [
            "grn_unique_id" => $upload_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        error_log("documents datatable query: " . $result->sql . "\n", 3, "debug.txt");

        if ($result->status) {
            $res_array = $result->data;

            foreach ($res_array as $key => $value) {
                // Get document type name from doc_type_options(type)
                $type_data = doc_type_options($value['type']);
                $type_name = '';
                if (is_array($type_data) && isset($type_data[0]['name'])) {
                    $type_name = $type_data[0]['name'];
                }
                $value['type'] = $type_name;

                if (is_null($value['file_attach']) || $value['file_attach'] == '') {
                    $value['file_attach'] = "<td style='text-align:center'><span class='font-weight-bold'>No Image Uploaded</span></td>";
                } else {
                    $image_files = explode(',', $value['file_attach']);
                    $image_buttons = "";
                    foreach ($image_files as $image_file) {
                        $image_path = "../blue_planet_erp/uploads/grn_new/" . trim($image_file);
                        $view_button = "<button type='button' onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'> <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                        </button>";
                        $image_buttons .= $view_button;
                    }
                    $value['file_attach'] = "<td style='text-align:center'>" . $image_buttons . "</td>";
                }

                // $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                // $value['unique_id'] = $btn_delete;

                $data[] = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
    break;



    case 'datatable':
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;
		$data	    = [];
        $from = isset($_POST['from']) ? $_POST['from'] : '';
        $to   = isset($_POST['to']) ? $_POST['to'] : '';
        $status   = isset($_POST['status']) ? $_POST['status'] : '';
		if($length == '-1') {
			$limit  = "";
        }
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 AS s_no", 
            "company_id",
            "project_id",
            "supplier_name",
            "invoice_date",
            "po_number",
            "grn_number",
            "supplier_invoice_no",
            "checked_by",
            "status_remark",
            "approve_status",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_delete"     => 0
        ];
        if($status != ''){
            $where = " is_delete = '0' AND entry_date >= '$from' AND entry_date <= '$to' AND check_status = 1 AND approve_status = '$status'";
        } else {
            $where = " is_delete = '0' AND entry_date >= '$from' AND entry_date <= '$to' AND check_status = 1";
        }
        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);
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
        error_log("sql: " . print_r($result, true) . "\n", 3, "datatable_init.txt");
        error_log("result: " . $result->sql . "\n", 3, "sql_error_log.txt");
        if ($result->status) {
            $res_array      = $result->data;
            foreach ($res_array as $key => $value) {
                $company_data                   = company_name($value['company_id']);
                $value['company_id']            = $company_data[0]['company_name'];

                $company_data = project_name($value['project_id']);
                $value['project_id'] = $company_data[0]['project_code'] . " / " . $company_data[0]['project_name'];

                $project_code = $project_options[0]['project_code'];

                $purchase_order_no  = get_po_number($value['po_number']);
                $value['po_number']            = $purchase_order_no[0]['purchase_order_no'];

                $supplier_names = supplier($value['supplier_name']);
                $value['supplier_name'] = $supplier_names[0]['supplier_name'];        
                
                $checked_by = fetch_user_name($value['checked_by']);
                $formatted_checked_by = ucwords(strtolower($checked_by));
                $value['checked_by'] = $formatted_checked_by;
                
                $approve_status = $value['approve_status']; // logic usage
                
                $btn_view  = btn_views($folder_name, $value['unique_id']);
                $btn_print = btn_prints($folder_name, $value['unique_id']);
                $btn_upload = btn_docs($folder_name, $value['unique_id']);

                if ($approve_status == '1') {
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $action_display = $btn_delete;
                    $approve_status = '<span class="text-success fw-bold">&#10003; Approved</span>';
                } elseif ($approve_status == '2') {
                    $approve_status = '<span class="text-danger fw-bold">&#10007; Rejected</span>';
                } else {
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $action_display = $btn_update;
                    $approve_status = '<span class="text-warning fw-bold">Pending</span>';
                }
                
                $action_display .= $btn_upload;
                unset($value['approve_status']);
                $row = array_values($value);
                // array_splice($row, 3, 0, $project_code);
                $row[10] = $approve_status;
                $row[11] = $action_display;
                
                array_splice($row, 11, 0, [$btn_view, $btn_print]);
                $data[] = $row;
            }

            error_log("data: " . print_r($data, true) . "\n", 3, "data_log.txt");
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

    case "grn_sublist_datatable":
    $screen_unique_id = $_POST["screen_unique_id"];
    $btn_prefix = "grn_sub";
    $is_update = isset($_POST['is_update']) ? $_POST['is_update'] : false;

    $po_unique_id = fetch_po_unique_id($sub_table, $screen_unique_id);
    $po_unique_id = is_array($po_unique_id) ? $po_unique_id[0]["po_unique_id"] : $po_unique_id;

    $unique_id = fetch_unique_id($sub_table, $screen_unique_id);
    $unique_id = is_array($unique_id) ? $unique_id[0]["unique_id"] : $unique_id;

    $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
    $po_sc_unique_id = is_array($po_sc_unique_id) ? $po_sc_unique_id[0]["screen_unique_id"] : $po_sc_unique_id;

    $td_data = fetch_tax_discount($po_sc_unique_id);
    $tax = $td_data['tax'];
    $tax_info = tax($tax);
    $tax_name = $tax_info[0]['tax_name'];
    $tax = (float)$tax_info[0]['tax_value'];
    $discount = (float)$td_data['discount'];
    $discount_type = $td_data['discount_type'];

    $pdo->query("SET @a := 0;");
    $total_amount = 0;
    $taxed_val = 0;

    $columns = [
        "@a := @a + 1 AS s_no",
        "gs.item_code",
        "gs.order_qty",
        "gs.uom",
        "(gs.now_received_qty - gs.update_qty) AS prev_received",
        "IF(gs.po_unique_id = '0', 
            gs.order_qty, 
            IF(gs.update_qty IS NULL OR gs.update_qty = 0, 
                GREATEST(gs.order_qty - COALESCE(grn_sub.total_received_qty, 0), 0), 
                gs.update_qty
            )
        ) AS update_qty",
        "poi_items.rate",
        "'$tax_name' AS tax_name",
        "$discount_type AS discount_type",
        "$discount AS discount",
        // keep SQL-side amount for reference but not used for total
        "ROUND(((gs.update_qty * poi_items.rate) - ((gs.update_qty * poi_items.rate * $discount) / 100)) + (((gs.update_qty * poi_items.rate - (gs.update_qty * poi_items.rate * $discount / 100)) * $tax) / 100), 2) AS amount",
        "gs.unique_id"
    ];

    $table_details = [
        "$sub_table gs 
            LEFT JOIN ( 
                SELECT 
                    gs2.item_code, 
                    gs2.po_unique_id, 
                    SUM(gs2.update_qty) AS total_received_qty
                FROM grn_sublist as gs2
                LEFT JOIN grn as g ON g.screen_unique_id = gs2.screen_unique_id
                WHERE gs2.po_unique_id = '$po_unique_id' 
                AND gs2.screen_unique_id != '$screen_unique_id' 
                AND gs2.is_delete = 0 
                AND g.is_delete = 0
                GROUP BY gs2.item_code, gs2.po_unique_id
            ) AS grn_sub 
            ON gs.item_code = grn_sub.item_code 
            AND gs.po_unique_id = grn_sub.po_unique_id
        LEFT JOIN purchase_order poi 
            ON gs.po_unique_id = poi.unique_id
        LEFT JOIN purchase_order_items poi_items 
            ON poi_items.screen_unique_id = poi.screen_unique_id 
            AND poi_items.item_code = gs.item_code
        WHERE gs.screen_unique_id = '$screen_unique_id' 
        AND gs.is_delete = 0",
        $columns
    ];

    $result = $pdo->select($table_details);
    $data = [];

    if ($result->status) {
        foreach ($result->data as $row) {
            // --- numeric conversions ---
            $qty  = isset($row['update_qty']) ? (float)$row['update_qty'] : 0.0;
            $rate = isset($row['rate'])       ? (float)$row['rate']       : 0.0;

            // --- discount ---
            $discountAmt = 0.0;
            if ($discount_type == 1 || $discount_type === 'Percentage') {
                $discountAmt = ($qty * $rate * $discount) / 100;
            } elseif ($discount_type == 2 || $discount_type === 'Amount') {
                $discountAmt = $discount;
            }

            $afterDiscount = max(($qty * $rate) - $discountAmt, 0.0);

            // --- tax ---
            $rowTax = ($tax * $afterDiscount) / 100;

            // --- per-row display amount ---
            $row['amount'] = round($afterDiscount + $rowTax, 2);

            // --- accumulate totals ---
            $total_amount += round($afterDiscount, 2); // pre-tax total
            $taxed_val    += round($rowTax, 2);        // total tax

            // --- map item and uom ---
            $item_data = item_name_list($row["item_code"]);
            $row["item_code"] = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];

            $uom_data = unit_name($row["uom"]);
            $row["uom"] = !empty($uom_data[0]["unit_name"]) ? $uom_data[0]["unit_name"] : $row["uom"];

            // --- discount type display ---
            if ($discount_type == 1 || $discount_type === 'Percentage') {
                $row['discount_type_display'] = 'Percentage';
                $row['discount_type'] = 'Percentage';
            } elseif ($discount_type == 2 || $discount_type === 'Amount') {
                $row['discount_type_display'] = 'Amount';
                $row['discount_type'] = 'Amount';
            } else {
                $row['discount_type_display'] = 'No Type';
                $row['discount_type'] = 'No Type';
            }

            // --- edit/delete buttons ---
            $edit = btn_edit($btn_prefix, $row["unique_id"]);
            $del  = btn_delete($btn_prefix, $row["unique_id"]);
            $row["unique_id"] = $edit . $del;

            $data[] = array_merge(array_values($row), [
                'item_code'        => $row["item_code"],
                'order_qty'        => $row["order_qty"],
                'uom'              => $row["uom"],
                'now_received_qty' => $row["now_received_qty"] ?? 0,
                'update_qty'       => $row["update_qty"],
                'rate'             => $row["rate"],
                'tax'              => $tax_name,
                'discount_type'    => $row["discount_type"],
                'discount'         => $discount,
                'discount_type_display' => $row["discount_type_display"],
                'amount'           => $row['amount'],
                'unique_id'        => $row["unique_id"]
            ]);
        }

        echo json_encode([
            "draw"            => 1,
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data,
            "total"           => $total_amount, // ✅ pre-tax, after discount
            "taxed"           => $taxed_val     // ✅ total GST
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


    case 'project_name':
        $company_id          = $_POST['company_id'];
        $project_name_options  = get_project_name("",$company_id);
        $project_name_options  = select_option($project_name_options,"Select the Project Name");
        echo $project_name_options;
    break;   

    case 'get_purchase_order_no':
        $project_id = $_POST['project_id'];
        $purchase_order_options  = get_po_number("",$project_id);
        $purchase_order_options = select_option($purchase_order_options, "Select Purchase Order No");
        echo $purchase_order_options;
    break;

    case "get_po_items_for_grn":
        $po_unique_id = $_POST["unique_id"];
        $po_result = $pdo->select(["purchase_order", ["screen_unique_id", "supplier_id"]], ["unique_id" => $po_unique_id]);
        if (!$po_result->status || empty($po_result->data)) {
            echo json_encode([
                "status" => false,
                "msg"    => "PO not found",
                "data"   => [],
                "error"  => $po_result->error
            ]);
            break;
        }
        $po_screen_unique_id = $po_result->data[0]["screen_unique_id"];
        $supplier_id = $po_result->data[0]["supplier_id"];
        $supplier_name = '';
        if ($supplier_id) {
            $supp = $pdo->select(["supplier_profile", ["supplier_name"]], ["unique_id" => $supplier_id]);
            if ($supp->status && count($supp->data)) {
                $supplier_name = $supp->data[0]["supplier_name"];
            }
        }
        $po_item_table = "purchase_order_items";
        $columns = ["item_code", "lvl_2_quantity", "uom", "rate", "tax"];
        $table_details = [$po_item_table, $columns];
        $where = [
            "screen_unique_id" => $po_screen_unique_id,
            "is_delete" => 0
        ];
        $items_result = $pdo->select($table_details, $where);
        echo json_encode([
            "status"         => $items_result->status,
            "msg"            => $items_result->status ? "data_fetched" : "no_items",
            "data"           => $items_result->status ? $items_result->data : [],
            "supplier_name"  => $supplier_name,
            "supplier_id"    => $supplier_id, 
            "error"          => $items_result->error
        ]);
    break;

    case "clear_grn_sublist":
        ob_clean(); // flush any previous output
        $screen_unique_id = $_POST["screen_unique_id"];
        $columns = [ "is_delete" => 1 ];
        $where   = [ "screen_unique_id" => $screen_unique_id ];
        $action_obj = $pdo->update("grn_sublist", $columns, $where);
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "cleared" : "clear_failed",
            "error"  => $action_obj->error
        ]);
    break;

    default:
        break;
}

function batch_creation($table_name, $company_unique_id, $prefixs, $conn) {
  $stmt = $conn->prepare("SELECT * FROM $table_name WHERE company_id = :company ORDER BY id DESC LIMIT 1");
  $stmt->execute([':company' => $company_unique_id]);
  if ($pit_query = $stmt->fetch(PDO::FETCH_ASSOC)) {
      $billno = $prefixs;
      $bill_order_no = generate_order_number($table_name, $conn, $prefixs);
      $billno .= sprintf("%03d", $bill_order_no);
      return $billno;
  } else {
      $billno = $prefixs;
      $bill_order_no = generate_order_number($table_name, $conn, $prefixs);
      $billno .= sprintf("%03d", $bill_order_no);
      return $billno;
  }
}

function generate_order_number($table_name, $conn, $prefix) {
  $stmt = $conn->prepare("SELECT MAX(sales_order_no) AS max_id FROM $table_name WHERE sales_order_no LIKE :prefix and is_delete = 0");
  $stmt->execute([':prefix' => $prefix . '%']);
  $result = $stmt->fetch(PDO::FETCH_ASSOC);
  $max_id = isset($result['max_id']) ? intval(substr($result['max_id'], strlen($prefix))) : 0;
  $new_order_number = $max_id + 1;
  return $new_order_number;
}

function fetch_tax_discount($screen_unique_id)
{
    global $pdo; // ensure $pdo is available
    $table = "purchase_order_items";
    $columns = ["tax", "discount", "discount_type"]; // Added discount_type to the columns
    $table_details = [$table, $columns];
    $where = [
        "screen_unique_id" => $screen_unique_id,
        "is_delete" => 0
    ];
    $result = $pdo->select($table_details, $where);
    if ($result->status && !empty($result->data)) {
        return [
            'tax' => $result->data[0]['tax'],
            'discount' => $result->data[0]['discount'],
            'discount_type' => $result->data[0]['discount_type'] // Fetching discount_type
        ];
    } else {
        return [
            'tax' => 0,
            'discount' => 0,
            'discount_type' => 0 // Default value if not found
        ];
    }
}

function fetch_user_name($user_unique_id){
    global $pdo;

    $table = "user";

    $columns = [
        'user_name'
    ];

    $table_details = [
        $table,
        $columns
    ];

    $where = [
        "unique_id" => $user_unique_id
    ];

    $result = $pdo->select($table_details, $where);

    if ($result->status && !empty($result->data)) {
        return $result->data[0]['user_name']; // ✅ Return the user name
    } else {
        return 0; // Or return null if preferred
    }
}
?>