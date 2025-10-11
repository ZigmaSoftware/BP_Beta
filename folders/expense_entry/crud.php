<?php

// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];

// Database Country Table Name
$table              = "expense_entry";
$sub_list_table     = "expense_entry_items";
$documents_upload   = "expense_entry_uploads";
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
$sub_group_unique_id = "";
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






case "expense_item_add_update":
    $main_unique_id    = $_POST["main_unique_id"];
    $sublist_unique_id = $_POST["sublist_unique_id"];
    $item_name         = $_POST["item_name"];
    $unit              = $_POST["unit"];
    $quantity          = $_POST["quantity"] ?: 0;
    $rate              = $_POST["rate"] ?: 0;
    $discount_type     = $_POST["discount_type"] ?: 0;
    $discount          = $_POST["discount"] ?: 0;
    $tax               = $_POST["tax"] ?: 0;
    $remarks           = $_POST["remarks"] ?? '';

    // Calculate line total
    $base = $quantity * $rate;
$discount_amt = 0;

if ($discount_type == 1) {
    $discount_amt = ($base * $discount) / 100;
} elseif ($discount_type == 2) {
    $discount_amt = $discount;
}

$after_discount = $base - $discount_amt;
if ($after_discount < 0) $after_discount = 0;

$tax_amt = ($after_discount * $tax) / 100;
$final_total = $after_discount + $tax_amt; // exact value, no rounding

$columns = [
    "main_unique_id" => $main_unique_id,
    "item_name"      => $item_name,
    "unit"           => $unit,
    "quantity"       => $quantity,
    "rate"           => $rate,
    "discount_type"  => $discount_type,
    "discount"       => $discount,
    "tax"            => $tax,
    "amount"         => $final_total,
    "remarks"        => $remarks
];




    if ($sublist_unique_id) {
        $columns["updated_user_id"] = $_SESSION["sess_user_id"];
        $columns["updated"] = date("Y-m-d H:i:s");
        $pdo->update("expense_entry_items", $columns, ["unique_id" => $sublist_unique_id]);
        $msg = "update";
    } else {
        $columns["unique_id"] = unique_id();
        $columns["created_user_id"] = $_SESSION["sess_user_id"];
        $columns["created"] = date("Y-m-d H:i:s");
        $pdo->insert("expense_entry_items", $columns);
        $msg = "add";
    }

    echo json_encode(["status" => true, "msg" => $msg]);
break;



case "createupdate":
    $category_id     = $_POST["category_id"];
 $payment_type_id = $_POST["payment_type_id"];

    $customer_id  = $_POST["customer_id"];
    $invoice_date = $_POST["invoice_date"];
    $remarks      = $_POST["remarks"];
    $unique_id    = !empty($_POST["unique_id"]) ? $_POST["unique_id"] : unique_id();

    // Totals
    $basic       = (float)($_POST["basic"] ?? 0);
    $total_gst   = (float)($_POST["total_gst"] ?? 0);
    $roundoff    = (float)($_POST["roundoff"] ?? 0);
    $tot_amount  = (float)($_POST["tot_amount"] ?? 0);
    
    $invoice_no = 1;

   $columns = [
    "unique_id"       => $unique_id,
    "category_id"     => $category_id,
    "payment_type_id" => $payment_type_id,
    "customer_id"     => $customer_id,
    "invoice_no"    => $invoice_no,
    "invoice_date"    => $invoice_date,
    "remarks"         => $remarks,
    "basic"           => $basic,
    "total_gst"       => $total_gst,
    "round_off"        => $roundoff,
    "tot_amount"      => $tot_amount,
    "created_user_id" => $_SESSION["sess_user_id"],
    "created"         => date("Y-m-d H:i:s")
];


    $check = $pdo->select(["expense_entry", ["COUNT(*) AS c"]], ["unique_id" => $unique_id, "is_delete" => 0]);
    error_log(print_r($check, true) . "\n", 3, "check.log");


    if ($check->status && $check->data[0]["c"]) {
        unset($columns["unique_id"], $columns["created_user_id"], $columns["created"]);
        $columns["updated_user_id"] = $_SESSION["sess_user_id"];
        $columns["updated"] = date("Y-m-d H:i:s");
        $result = $pdo->update("expense_entry", $columns, ["unique_id" => $unique_id]);
        $msg = "update";
    } else {
        $result = $pdo->insert("expense_entry", $columns);
        $msg = "create";
    }

    error_log(print_r($result, true) . "\n", 3, "result.log");

    echo json_encode(["status" => true, "msg" => $msg, "data" => ["unique_id" => $unique_id]]);
break;



   
   case 'datatable':
    $search  = $_POST['search']['value'] ?? '';
    $length  = $_POST['length'] ?? 10;
    $start   = $_POST['start'] ?? 0;
    $draw    = $_POST['draw'] ?? 1;

    $from_date     = $_POST['from_date'] ?? '';
    $to_date       = $_POST['to_date'] ?? '';
    $category_name = $_POST['category_name'] ?? '';
    $payment_type  = $_POST['payment_type'] ?? '';

    $customer_name = $_POST['customer_name'] ?? '';

    $data = [];

    $columns = [
    "@a:=@a+1 s_no",
    "invoice_no",
    "category_id",
    "payment_type_id",
    "customer_id",
    "invoice_date",
    "remarks",
    "unique_id"
];


    $table_details = [$table . " , (SELECT @a:=" . $start . ") AS a ", $columns];

    // Build WHERE
    $where = "is_delete = '0'";
    $conditions = [];

    if (!empty($from_date) && !empty($to_date)) {
        $conditions[] = "invoice_date BETWEEN '{$from_date}' AND '{$to_date}'";
    }
    if (!empty($category_name)) {
    $conditions[] = "category_id = '{$category_name}'";
}
if (!empty($payment_type)) {
    $conditions[] = "payment_type_id = '{$payment_type}'";
}


   
    if (!empty($customer_name)) {
        $conditions[] = "customer_id = '{$customer_name}'";
    }

    if ($conditions) {
        $where .= " AND " . implode(" AND ", $conditions);
    }

    // Order + Search
    $order_column = $_POST["order"][0]["column"] ?? 0;
    $order_dir    = $_POST["order"][0]["dir"] ?? "asc";
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);

    $searching    = datatable_searching($search, $columns);
    if ($searching) {
        $where .= " AND " . $searching;
    }

    $sql_function = "SQL_CALC_FOUND_ROWS";
    $result       = $pdo->select($table_details, $where, $length, $start, $order_by, $sql_function);
    
    $total_records = total_records();

    if ($result->status) {
        foreach ($result->data as $value) {
            $value['category_id'] = category_name($value['category_id'])[0]['category_name'] ?? '';
$value['payment_type_id'] = payment_type_name($value['payment_type_id'])[0]['payment_type_name'] ?? '';

            $value['customer_id']= customers($value['customer_id'])[0]['customer_name'] ?? '';

            $btn_view    = btn_views($folder_name, $value['unique_id']);
            $btn_print   = btn_prints($folder_name, $value['unique_id']);
            $btn_upload  = btn_docs($folder_name, $value['unique_id']);
            $btn_update  = btn_update($folder_name, $value['unique_id']);
            $btn_delete  = btn_delete($folder_name, $value['unique_id']);

            $value['unique_id'] = $btn_update . $btn_delete . $btn_upload;
            array_splice($value, -1, 0, [$btn_view, $btn_print]);

            $data[] = array_values($value);
        }

        echo json_encode([
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data
        ]);
    } else {
        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
            "error" => $result->error
        ]);
    }
    break;

   
   
   

        case 'delete':

            $unique_id = $_POST['unique_id'];
            $remarks   = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';
        
            $columns = [
                "is_delete"           => 1,
                "is_delete_remarks"   => $remarks
            ];
        
            $update_where = [
                "unique_id" => $unique_id
            ];
        
            $action_obj = $pdo->update($table, $columns, $update_where);
        
            $status = $action_obj->status;
            $data   = $action_obj->data;
            $error  = $action_obj->error;
            $sql    = $action_obj->sql;
            $msg    = $status ? "success_delete" : "error";
        
            $json_array = [
                "status" => $status,
                "data"   => $data,
                "error"  => $error,
                "msg"    => $msg,
                "sql"    => $sql
            ];
        
            echo json_encode($json_array);
            break;
        


     case "expense_entry_delete":
    $unique_id = $_POST['unique_id'];
    $remarks   = isset($_POST['remarks']) ? trim($_POST['remarks']) : '';

    $columns = [
        "is_delete"         => 1,
        "is_delete_remarks" => $remarks
    ];

    $update_where = ["unique_id" => $unique_id];
    $action_obj   = $pdo->update($table, $columns, $update_where);

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $action_obj->status ? "delete_success" : "delete_error",
        "error"  => $action_obj->error,
        "sql"    => $action_obj->sql
    ]);
    break;







    case 'project_name':

        $company_id          = $_POST['company_id'];

        $project_name_options  = get_project_name_all("", $company_id);

        $project_name_options  = select_option($project_name_options, "Select the Project Name");

        echo $project_name_options;

        break;
        


case "expense_items_datatable":
    $main_unique_id = $_POST["main_unique_id"];
    $btn_prefix     = "inv_item";

    $columns = [
        "@a:=@a+1 as s_no",
        "item_name",
        "unit",
        "quantity",
        "rate",
        "discount_type",
        "discount",
        "tax",
        "amount",
        "remarks",
        "unique_id"
    ];

    $table_details = [
        "expense_entry_items, (SELECT @a:=0) as a",
        $columns
    ];

    $where = [
        "main_unique_id" => $main_unique_id,
        "is_delete"      => 0
    ];

    $result = $pdo->select($table_details, $where);

    $data = [];
    if ($result->status) {
        foreach ($result->data as $row) {
            // map IDs to names
            $row["item_name"] = get_item_name($row["item_name"]);
            $row["unit"]      = unit_name($row["unit"])[0]['unit_name'];
            // Fetch tax name properly based on tax_value (not unique_id)
            $tax_value = $row["tax"]; // numeric stored in DB
            $tax_label = "";
            
            $tax_res = $pdo->select(["tax", ["tax_name"]], ["tax_value" => $tax_value, "is_delete" => 0]);
            if ($tax_res->status && !empty($tax_res->data)) {
                $tax_label = $tax_res->data[0]["tax_name"];
            } else {
                $tax_label = $tax_value . "%"; // fallback display
            }
            
            $row["tax"] = $tax_label;


            
            
        // Map discount type value to readable text
            $discount_type_label = "";
            if ($row["discount_type"] == 1) {
                $discount_type_label = "Percentage (%)";
            } elseif ($row["discount_type"] == 2) {
                $discount_type_label = "Amount (₹)";
            }

            $edit = btn_edit($btn_prefix, $row["unique_id"]);
            $del  = btn_delete($btn_prefix, $row["unique_id"]);

            $data[] = [
                "s_no"     => $row["s_no"],
                "item"     => $row["item_name"],
                "unit"     => $row["unit"],
                "qty"      => $row["quantity"],
                "rate"     => $row["rate"],
                "discount_type"  => $discount_type_label,
                "discount" => $row["discount"],
                "tax"      => $row["tax"],
                "amount"   => $row["amount"],
                "remarks"  => $row["remarks"], 
                "actions"  => $edit . $del
            ];
        }
    }

    echo json_encode([
        "draw"            => intval($_POST["draw"] ?? 1),
        "recordsTotal"    => count($data),
        "recordsFiltered" => count($data),
        "data"            => $data
    ]);
    break;





    case "delete_sublist_by_main_id":
        $main_unique_id = $_POST["main_unique_id"];
        $update = ["is_delete" => 1];
        $where = ["main_unique_id" => $main_unique_id];
        $action_obj = $pdo->update("expense_entry_items", $update, $where);
        echo json_encode([
            "status" => $action_obj->status,
            "msg" => $action_obj->status ? "deleted" : "failed",
            "error" => $action_obj->error
        ]);
        break;


    
    
    case "exp_item_edit":
    $unique_id = $_POST["unique_id"];

    $columns = [
        "unique_id",
        "item_name",
        "unit",
        "quantity",
        "rate",
        "discount_type",
        "discount",
        "tax",
        "amount",
        "remarks"
    ];

    $result = $pdo->select(["expense_entry_items", $columns], ["unique_id" => $unique_id, "is_delete" => 0]);

    if ($result->status && !empty($result->data)) {
        echo json_encode([
            "status" => true,
            "data"   => $result->data[0]
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "error"  => "Item not found"
        ]);
    }
    break;

    
    


    case "inv_item_delete":
    $unique_id = $_POST["unique_id"];

    $columns = [
        "is_delete" => 1
    ];
    $where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update("expense_entry_items", $columns, $where);

    echo json_encode([
        "status" => $action_obj->status,
        "msg"    => $action_obj->status ? "delete_success" : "delete_error",
        "error"  => $action_obj->error,
        "sql"    => $action_obj->sql
    ]);
    break;



    
    case "get_item_details":
    header('Content-Type: application/json');

    $key = $_POST['item_code'] ?? $_POST['item_id'] ?? '';
    if (empty($key)) {
        echo json_encode(["status" => false, "error" => "Missing item identifier"]);
        exit;
    }

    // Step 1: fetch unit_price and gst ID from item_master
    $q = $pdo->select(
        ["item_master", ["unit_price", "gst"]],
        ["unique_id" => $key, "is_delete" => 0]
    );

    if (!$q->status || empty($q->data)) {
        echo json_encode(["status" => false, "error" => "Item not found"]);
        exit;
    }

    $item = $q->data[0];
    $gst_id = $item['gst'];
    $unit_price = $item['unit_price'];

    // Step 2: fetch actual tax percentage or name from tax table
    $tax_val = 0;
    if (!empty($gst_id)) {
        $tq = $pdo->select(
            ["tax", ["tax_name", "tax_value"]],
            ["unique_id" => $gst_id, "is_delete" => 0]
        );

        if ($tq->status && !empty($tq->data)) {
            $tax_val = $tq->data[0]['tax_value']; // numeric % like 18
        }
    }

    // Step 3: return combined data
    echo json_encode([
        "status" => true,
        "data" => [
            "unit_price" => $unit_price,
            "gst"        => $tax_val
        ]
    ]);
    break;


    

    
    
    
    case "get_items_by_group":
        $group_id = $_POST["group_id"];

        $excluded_ids = ["683568ca2fe8263239", "683588840086c13657"]; // Service, Capital
        $table = "item_master";
        $columns = ["unique_id", "item_name", "item_code"];
        $where = "is_delete = 0";

        if ($group_id === "1") {
            // Regular: fetch all groups that are NOT Service or Capital
            $where .= " AND group_unique_id NOT IN ('" . implode("','", $excluded_ids) . "')";
        } elseif (in_array($group_id, $excluded_ids)) {
            // Specifically fetch items matching the group (Service or Capital)
            $where .= " AND group_unique_id = '$group_id'";
        } else {
            // Unknown or fallback group, return empty
            $where .= " AND 1=0";
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
        // error_log("options: " . print_r($options, true) . "\n", 3, "items_log.txt");
    break;


    case "get_items_by_sales_order":
    $so_main_unique_id = $_POST["sales_order_id"]; // main table unique_id
    $type              = (int)$_POST['type'];

    $sales_order_table = "sales_order_sublist";
    $item_table        = "item_master";
    $product_table     = "product_sublist"; // for product expansions
    $obom_table        = "obom_list";       // for PR type 3, so_type 1/2 (and optionally 4 if you use obom there)

    error_log("INPUT", ["sales_order_id"=>$so_main_unique_id, "type"=>$type]);

    // Step 1: read so_type (your main table is 'sales_order')
    error_log("STEP 1: Fetch so_type from sales_order", ["unique_id"=>$so_main_unique_id]);
    $so_main_res = $pdo->select(
        ["sales_order", ["so_type"]],
        ["unique_id" => $so_main_unique_id, "is_delete" => 0]
    );
    error_log("STEP 1 RESULT", [
        "status" => $so_main_res->status ?? null,
        "rows"   => isset($so_main_res->data) ? count($so_main_res->data) : null,
        "data"   => $so_main_res->data ?? null
    ]);

    $so_type = ($so_main_res->status && !empty($so_main_res->data))
        ? (int)$so_main_res->data[0]["so_type"]
        : null;

    error_log("STEP 1: RESOLVED so_type", $so_type);

    // Step 2: fetch all SO lines for this order
    $columns = ["item_name_id", "quantity"];
    $where   = ["so_main_unique_id" => $so_main_unique_id, "is_delete" => 0];
    error_log("STEP 2: Query SO lines", ["table"=>$sales_order_table, "columns"=>$columns, "where"=>$where]);

    $so_lines_res = $pdo->select([$sales_order_table, $columns], $where);
    error_log("STEP 2 RESULT", [
        "status" => $so_lines_res->status ?? null,
        "rows"   => isset($so_lines_res->data) ? count($so_lines_res->data) : null,
        "sample" => $so_lines_res->data[0] ?? null
    ]);

    $items = [];

    if ($so_lines_res->status && !empty($so_lines_res->data)) {
        foreach ($so_lines_res->data as $idx => $line) {
            $prod_unique_id = $line["item_name_id"]; // could be raw item OR a product
            $line_qty       = (int)$line["quantity"];

            error_log("LOOP line", ["idx"=>$idx, "prod_unique_id"=>$prod_unique_id, "qty"=>$line_qty]);

            // Decide expansion path PER LINE:
            // 1) For PR type 3 & so_type in (1,2): prefer OBOM (scoped by SO)
            $expanded = false;

            if ($type === 3 && in_array($so_type, [1,2], true)) {
                // Try OBOM expansion first
                $sublist_cols  = ["item_unique_id"];
                $sublist_where = ["prod_unique_id" => $prod_unique_id, "so_unique_id" => $so_main_unique_id, "is_delete" => 0];
                // If your obom_list keeps a 'type' column you need to filter by, add it here.
                error_log("TRY OBOM", ["table"=>$obom_table, "where"=>$sublist_where]);

                $obom_res = $pdo->select([$obom_table, $sublist_cols], $sublist_where);
                error_log("OBOM result", [
                    "status" => $obom_res->status ?? null,
                    "rows"   => isset($obom_res->data) ? count($obom_res->data) : null,
                    "sample" => $obom_res->data[0] ?? null
                ]);

                if ($obom_res->status && !empty($obom_res->data)) {
                    foreach ($obom_res->data as $sidx => $subrow) {
                        $item_unique_id = $subrow["item_unique_id"];

                        $item_res = $pdo->select([$item_table, [
                            "item_name","item_code","description","uom_unique_id","category_unique_id"
                        ]], ["unique_id" => $item_unique_id, "is_delete" => 0]);

                        if (!$item_res->status || empty($item_res->data)) continue;

                        $item = $item_res->data[0];
                        $items[] = [
                            "item_unique_id" => $item_unique_id,
                            "item_code"      => $item["item_code"],
                            "item_name"      => $item["item_name"],
                            "description"    => $item["description"],
                            "uom_id"         => $item["uom_unique_id"],
                            "quantity"       => $line_qty // * component_qty if you have it
                        ];
                    }
                    $expanded = true;
                }
            }

            // 2) If not expanded yet:
            //    - For so_type = 4 (mixed), or for type==2 & so_type in (1,2), try product_sublist.
            if (!$expanded) {
                $should_try_product_sublist = false;

                if ($so_type === 4) {
                    // mixed: try product_sublist; if none, treat as raw item
                    $should_try_product_sublist = true;
                } elseif ($type === 2 && in_array($so_type, [1,2], true)) {
                    // your original rule for PR type 2, so_type 1/2
                    $should_try_product_sublist = true;
                } elseif ($type === 3 && $so_type === 4) {
                    // optional: for PR type 3 with mixed, if OBOM didn’t expand, try product_sublist next
                    $should_try_product_sublist = true;
                }

                if ($should_try_product_sublist) {
                    $ps_cols  = ["item_unique_id"];
                    $ps_where = ["prod_unique_id" => $prod_unique_id, "is_delete" => 0];
                    error_log("TRY PRODUCT_SUBLIST", ["table"=>$product_table, "where"=>$ps_where]);

                    $ps_res = $pdo->select([$product_table, $ps_cols], $ps_where);
                    error_log("PRODUCT_SUBLIST result", [
                        "status" => $ps_res->status ?? null,
                        "rows"   => isset($ps_res->data) ? count($ps_res->data) : null,
                        "sample" => $ps_res->data[0] ?? null
                    ]);

                    if ($ps_res->status && !empty($ps_res->data)) {
                        foreach ($ps_res->data as $sidx => $subrow) {
                            $item_unique_id = $subrow["item_unique_id"];

                            $item_res = $pdo->select([$item_table, [
                                "item_name","item_code","description","uom_unique_id","category_unique_id"
                            ]], ["unique_id" => $item_unique_id, "is_delete" => 0]);

                            if (!$item_res->status || empty($item_res->data)) continue;

                            $item = $item_res->data[0];
                            $items[] = [
                                "item_unique_id" => $item_unique_id,
                                "item_code"      => $item["item_code"],
                                "item_name"      => $item["item_name"],
                                "description"    => $item["description"],
                                "uom_id"         => $item["uom_unique_id"],
                                "quantity"       => $line_qty // * component_qty if you have it
                            ];
                        }
                        $expanded = true;
                    }
                }
            }

            // 3) If still not expanded → treat as RAW ITEM from item_master
            if (!$expanded) {
                error_log("FALLBACK: direct item_master", ["unique_id"=>$prod_unique_id]);

                $item_res = $pdo->select([$item_table, [
                    "item_name","item_code","description","uom_unique_id","category_unique_id"
                ]], ["unique_id" => $prod_unique_id, "is_delete" => 0]);

                error_log("DIRECT item fetch", [
                    "status" => $item_res->status ?? null,
                    "rows"   => isset($item_res->data) ? count($item_res->data) : null,
                    "sample" => $item_res->data[0] ?? null
                ]);

                if ($item_res->status && !empty($item_res->data)) {
                    $item = $item_res->data[0];
                    $items[] = [
                        "item_unique_id" => $prod_unique_id,
                        "item_code"      => $item["item_code"],
                        "item_name"      => $item["item_name"],
                        "description"    => $item["description"],
                        "uom_id"         => $item["uom_unique_id"],
                        "quantity"       => $line_qty
                    ];
                }
            }
        }
    } else {
        error_log("NO SO LINES", ["status"=>$so_lines_res->status ?? null]);
    }

    error_log("FINAL items count", count($items));
    error_log("FINAL sample", array_slice($items, 0, 3));

    echo json_encode(["status" => count($items) > 0, "data" => $items]);
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
            "pr_unique_id" => $upload_unique_id,
            "is_active"                  => 1,
            "is_delete"                  => 0
        ];

        $order_by     = "";
        $sql_function = "SQL_CALC_FOUND_ROWS";

        // Execute Query
        $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        // error_log("documents datatable query: " . $result->sql . "\n", 3, "debug.txt");

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
                        $image_path = "../blue_planet_beta/uploads/expense_entry/" . trim($image_file);
                        $view_button = "<button type='button' onclick=\"new_external_window_image('$image_path')\" style='border: 2px solid #ccc; background:none; cursor:pointer; padding:5px; border-radius:5px; margin-right: 5px;'> <i class='fas fa-image' style='font-size: 20px; color: #555;'></i>
                        </button>";
                        $image_buttons .= $view_button;
                    }
                    $value['file_attach'] = "<td style='text-align:center'>" . $image_buttons . "</td>";
                }

                $btn_delete         = btn_delete($btn_edit_delete, $value['unique_id']);
                $value['unique_id'] = $btn_delete;

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
    
     case 'documents_add_update':

        $upload_unique_id = $_POST["upload_unique_id"] ?? null;
        $type             = $_POST["type"] ?? null;
        $unique_id        = $_POST["unique_id"] ?? null;
        
        // Log incoming POST data
        // error_log("POST: " . print_r($_POST, true) . "\n", 3, "doc_logs.txt");
        
        // Validate required fields
        if (!$upload_unique_id || !$type) {
            echo json_encode([
                "status" => false,
                "error"  => "Missing required fields: 'upload_unique_id' or 'type'.",
                "msg"    => "missing_fields"
            ]);
            exit;
        }
        
        // Check if no new file is uploaded AND no existing file is provided
        if (empty($_FILES["test_file"]["name"][0])) {
            echo json_encode([
                "status" => false,
                "error"  => "No file selected.",
                "msg"    => "no_file_selected"
            ]);
            exit;
        }


        $doc_up_filenames = [];     
        $allowed_exts = [
            // Images
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'webp', 'svg',
            
            // PDF
            'pdf',
        
            // Word documents
            'doc', 'docx',
        
            // Text files
            'txt',
        
            // Excel files
            'xls', 'xlsx',
        
            // CSV files
            'csv'
        ];

        if (!empty($_FILES["test_file"]["name"])) {                              
            $target_dir = "../../uploads/expense_entry/";
            $folder_path = "expense_entry/";

            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            foreach ($_FILES["test_file"]["name"] as $key => $name) {
                $file_extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime_type = finfo_file($finfo, $_FILES["test_file"]["tmp_name"][$key]);
                finfo_close($finfo);

               $allowed_mime_types = [
                    'image/jpeg', 'image/png', 'image/gif', 'image/bmp', 'image/webp', 'image/svg+xml',
                    'application/pdf',
                    'application/msword',
                    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                    'text/plain',
                    'application/vnd.ms-excel',
                    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'text/csv'
                ];
                
                if (!in_array($file_extension, $allowed_exts) || !in_array($mime_type, $allowed_mime_types)) {
                    echo json_encode([
                        "status" => false,
                        "error"  => "Invalid file format. Only images, PDF, Word, Excel, CSV, and text files are allowed.",
                        "msg"    => "invalid_file_format"
                    ]);
                    exit;
                }


                $unique_filename = md5(uniqid(rand(), true)) . '.' . $file_extension;
                $target_file = $target_dir . $unique_filename;

                if (move_uploaded_file($_FILES["test_file"]["tmp_name"][$key], $target_file)) {
                    $doc_up_filenames[] = $unique_filename;
                }
            }

            $doc_up_filename = implode(',', $doc_up_filenames);
        } else {
            $doc_up_filename = $_POST['existing_file_attach'];
        }

        $columns = [
            "pr_unique_id"              => $upload_unique_id,
            "type"                       => $type,
            "file_attach"                => $doc_up_filename,
        ];

        if (!$unique_id) {
            $columns["unique_id"] = unique_id($prefix);
        }

        // Direct insert or update — no duplicate check
        if ($unique_id) {
            $update_where = [ "unique_id" => $unique_id ];
            $action_obj = $pdo->update($documents_upload, $columns, $update_where);
            $msg = $action_obj->status ? "update" : "error";
        } else {
            $action_obj = $pdo->insert($documents_upload, $columns);
            $msg = $action_obj->status ? "add" : "error";
        }
        
        // error_log("action_obj: " . print_r($action_obj, true) . "\n", 3, "doc_logs.txt");
        
        $data_array = [
            "insert_id" => $action_obj->data,     // if it's lastInsertId()
            "upload"    => $upload_unique_id
        ];
        
       

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $data_array,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql,
        ]);

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
    

        break;
}

// ---- Debug helper ----
$LOG_FILE = "so_debug.log"; // make sure this path is writable

function dbg($msg, $data = null, $dest = null) {
    $dest = $dest ?? (defined('LOG_FILE') ? LOG_FILE : $GLOBALS['LOG_FILE']);
    $ts = date('Y-m-d H:i:s');
    if ($data !== null) {
        // Safely stringify arrays/objects
        if (!is_string($data)) {
            $encoded = json_encode($data, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
            if ($encoded === false) {
                $encoded = print_r($data, true);
            }
        } else {
            $encoded = $data;
        }
        $line = "[$ts] $msg :: $encoded" . PHP_EOL;
    } else {
        $line = "[$ts] $msg" . PHP_EOL;
    }
    error_log($line, 3, $dest);
}

function fetch_si_number($table)
{
    global $pdo;

    // Define the columns to be fetched (in this case, the invoice_no)
    $table_columns = [
        "invoice_no"
    ];

    // Prepare the details for the query
    $table_details = [
        $table,  // Specify the table name
        $table_columns  // Specify the columns to fetch
    ];

   

    // Perform the query (assuming your PDO object has a select() method)
    $result = $pdo->select($table_details);

    $invoice_nos = [];

    // Check if the query was successful and if data is returned
    if ($result->status && !empty($result->data)) {
        // Loop through the data and collect all the invoice_no values
        foreach ($result->data as $row) {
            $invoice_nos[] = $row['invoice_no'];
        }
        error_log($invoice_nos . "\n", 3, "logs/grn_log.txt");
        return $invoice_nos;
    }
}

function generateSI($label, &$labelData) {
    $year = $_SESSION['acc_year'];
    $number = 1;

    do {
        $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
        $grn = "SI/$year/$label/$paddedNumber";
        $number++;
    } while (in_array($grn, $labelData));

    // Optionally store the new GRN
    $labelData[] = $grn;

    return $grn;
}



function get_item_name($id) {
    global $pdo;
    $res = $pdo->select(["item_master", ["item_name"]], ["unique_id" => $id]);
    return ($res->status && !empty($res->data)) ? $res->data[0]["item_name"] : $id;
}

function get_uom_name($id) {
    global $pdo;
    $res = $pdo->select(["uom_master", ["uom_name"]], ["unique_id" => $id]);
    return ($res->status && !empty($res->data)) ? $res->data[0]["uom_name"] : $id;
}



