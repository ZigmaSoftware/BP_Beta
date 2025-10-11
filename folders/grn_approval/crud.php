<?php 

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table              = 'grn'; 
$sub_table          = 'grn_sublist'; 
$documents_upload   = 'grn_uploads';


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
    
    // case "createupdate":
    //     $approve_status = $_POST['approve_status'];
    //     $status_remark = $_POST['status_remark'];
    //     $sess_user_id = $_POST['sess_user_id'];
    //     $unique_id = $_POST["unique_id"];

    //     if ($approve_status == 3){
    //         // Cancel action
    //         $columns = [
    //             "is_delete"      => 1
    //         ];

    //         // Check if record exists
    //         $check_query = [$table, ["COUNT(unique_id) AS count"]];
    //         $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

    //         $action_obj = $pdo->update($check_query, $columns, $check_where);

    //         if ($action_obj->status && $action_obj->data[0]["count"] > 0) {
    //             // Proceed with update
    //             $update_where = ["unique_id" => $unique_id];
    //             $action_obj   = $pdo->update($table, $columns, $update_where);
    //             $msg          = "cancelled";
    //         } else {
    //             // Error: record not found
    //             echo json_encode([
    //                 "status" => false,
    //                 "error"  => "Invalid GRN record",
    //                 "msg"    => "Record not found for cancellation"
    //             ]);
    //             break;
    //         }
    //         echo json_encode([
    //             "status" => $action_obj->status,
    //             "data"   => ["unique_id" => $unique_id],
    //             "error"  => $action_obj->error,
    //             "msg"    => $msg,
    //             "sql"    => isset($action_obj->sql) ? $action_obj->sql : ''
    //         ]);
    //         break;
    //     }

    //     $columns = [
    //         "check_status"    => $approve_status,
    //         "check_remarks"   => $status_remark,
    //         "checked_by"      => $sess_user_id,
    //         "updated_user_id" => $user_id,
    //         "updated"         => $date,
    //         "approve_status"  => 0,
    //         "approved_by"     => null,
    //         "status_remark"   => null
    //     ];

    //     // Check if record exists
    //     $check_query = [$table, ["COUNT(unique_id) AS count"]];
    //     $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

    //     $action_obj = $pdo->select($check_query, $check_where);

    //     if ($action_obj->status && $action_obj->data[0]["count"] > 0) {
    //         // Proceed with update
    //         $update_where = ["unique_id" => $unique_id];
    //         $action_obj   = $pdo->update($table, $columns, $update_where);
    //         $msg          = "approved";
    //     } else {
    //         // Error: record not found
    //         echo json_encode([
    //             "status" => false,
    //             "error"  => "Invalid GRN record",
    //             "msg"    => "Record not found for approval"
    //         ]);
    //         break;
    //     }

    //     echo json_encode([
    //         "status" => $action_obj->status,
    //         "data"   => ["unique_id" => $unique_id],
    //         "error"  => $action_obj->error,
    //         "msg"    => $msg,
    //         "sql"    => isset($action_obj->sql) ? $action_obj->sql : ''
    //     ]);
    // break;

    case "createupdate":
        $approve_status = $_POST['approve_status'];
        $status_remark  = $_POST['status_remark'];
        $sess_user_id   = $_POST['sess_user_id'];
        $unique_id      = $_POST['unique_id'];

        // Set base response
        $response = [
            "status" => false,
            "data"   => ["unique_id" => $unique_id],
            "error"  => null,
            "msg"    => "Unknown error"
        ];

        // First, check if the record exists and is not deleted
        $check_query = [$table, ["COUNT(*) AS count"]];
        $check_where = 'unique_id = "' . $unique_id . '" AND is_delete = 0';

        $check_result = $pdo->select($check_query, $check_where);

        if (!$check_result->status || $check_result->data[0]['count'] == 0) {
            $response["error"] = "Invalid GRN record";
            $response["msg"]   = "Record not found";
            echo json_encode($response);
            break;
        }

        // === CANCEL ===
        if ($approve_status == 3) {
            $columns = ["is_delete" => 1];
            $update_where = ["unique_id" => $unique_id];

            $update_result = $pdo->update($table, $columns, $update_where);

            $response["status"] = $update_result->status;
            $response["error"]  = $update_result->error;
            $response["msg"]    = $update_result->status ? "GRN cancelled successfully" : "Failed to cancel GRN";
            $response["sql"]    = $update_result->sql ?? '';

            echo json_encode($response);
            break;
        }

        // === APPROVE / REJECT ===
        $columns = [
            "check_status"    => $approve_status,
            "check_remarks"   => $status_remark,
            "checked_by"      => $sess_user_id,
            "updated_user_id" => $user_id,
            "updated"         => $date,
            "approve_status"  => 0,
            "approved_by"     => null,
            "status_remark"   => null
        ];

        $update_where = ["unique_id" => $unique_id];
        $update_result = $pdo->update($table, $columns, $update_where);

        $response["status"] = $update_result->status;
        $response["error"]  = $update_result->error;
        $response["msg"]    = $update_result->status ? "GRN updated successfully" : "Failed to update GRN";
        $response["sql"]    = $update_result->sql ?? '';

        echo json_encode($response);
    break;



    // case "update_qty":
    //     $screen_unique_id = $_POST['screen_unique_id'];
    //     $is_update = $_POST['is_update'];
        
    //     // Check if the screen_unique_id is provided
    //     if (!empty($screen_unique_id)) {

    //         $po_unique_id_data = fetch_po_unique_id($sub_table, $screen_unique_id);
    //         $po_unique_id = is_array($po_unique_id_data) ? $po_unique_id_data[0]["po_unique_id"] ?? null : $po_unique_id_data;

    //         if (empty($po_unique_id)) {
    //             echo json_encode([
    //                 "status" => false,
    //                 "msg" => "No PO Unique ID found for this screen ID"
    //             ]);
    //         }

    //         if ($is_update) {
    //             // Step 1: Prepare JOIN query to get previous total_received_qty (excluding this screen)
    //             $columns = [
    //                 "gs.unique_id",
    //                 "gs.update_qty",
    //                 "gs.item_code",
    //                 "gs.po_unique_id",
    //                 "IFNULL(grn_sub.total_received_qty, 0) AS prev_received_qty"
    //             ];
                
    //             $select_query = [
    //                 "$sub_table gs 
    //                 LEFT JOIN ( 
    //                     SELECT 
    //                         item_code, 
    //                         po_unique_id, 
    //                         SUM(now_received_qty) AS total_received_qty
    //                     FROM $sub_table 
    //                     WHERE po_unique_id = '$po_unique_id' AND screen_unique_id != '$screen_unique_id' AND is_delete = 0
    //                     GROUP BY item_code, po_unique_id
    //                 ) AS grn_sub 
    //                 ON gs.item_code = grn_sub.item_code 
    //                 AND gs.po_unique_id = grn_sub.po_unique_id",
    //                 $columns
    //             ];
                
    //             $select_where = "gs.screen_unique_id = '$screen_unique_id' AND gs.is_delete = 0";
    //             $action_obj = $pdo->select($select_query, $select_where);

    //             if ($action_obj->status && count($action_obj->data) > 0) {
    //                 foreach ($action_obj->data as $row) {
    //                     $new_now_received_qty = $row['prev_received_qty'] + $row['update_qty'];

    //                     $update_columns = [
    //                         "now_received_qty" => $new_now_received_qty,
    //                         "updated_user_id" => $user_id,
    //                         "updated" => $date
    //                     ];

    //                     $update_where = ["unique_id" => $row['unique_id']];
    //                     $pdo->update($sub_table, $update_columns, $update_where);
    //                 }
    //                 $msg = "Quantities updated successfully!";
    //                 $status = true;
    //             } else {
    //                 $msg = "No matching records found to update.";
    //                 $status = false;
    //             }
    //         } else {
    //             // Conditionally set the select query columns based on whether it's an update or not
    //         $select_query = [
    //                 $sub_table,
    //                 ["unique_id", "now_received_qty", "update_qty"]  // Fetch all relevant columns when in create mode
    //             ];
    //         $select_where = "screen_unique_id = '$screen_unique_id' AND is_delete = 0";
            
    //         // Fetch the records
    //         $action_obj = $pdo->select($select_query, $select_where);
            
    //         if ($action_obj->status && count($action_obj->data) > 0) {
    //             // Loop through the results
    //             foreach ($action_obj->data as $row) {
    //                 $current_now_received_qty = $row['now_received_qty'];
    //                 $current_update_qty = $row['update_qty'];

    //                 // Add the existing now_received_qty with the update_qty
    //                 $new_now_received_qty = $current_now_received_qty + $current_update_qty;

    //                 // Prepare the update query to update the now_received_qty
    //                 $update_columns = [
    //                     "now_received_qty" => $new_now_received_qty,
    //                     "updated_user_id" => $user_id,
    //                     "updated" => $date
    //                 ];

    //                 // Update the record with the new now_received_qty value
    //                 $update_where = ["unique_id" => $row['unique_id']];
    //                 $pdo->update($sub_table, $update_columns, $update_where);
    //             }
    //             $msg = "Quantities updated successfully!";
    //             $status = true;
    //         } else {
    //             $msg = "No matching records found to update.";
    //             $status = false;
    //         }
    //         }
    //         // SQL to fetch existing records with the matching screen_unique_id
            
    //     } else {
    //         $msg = "Screen Unique ID is required.";
    //         $status = false;
    //     }

    //     // Return the response
    //     echo json_encode([
    //         "status" => $status,
    //         "msg" => $msg
    //     ]);
    // break;
    
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

        // DataTable Variables
		$search 	= $_POST['search']['value'];
		$length 	= $_POST['length'];
	    $start 		= $_POST['start'];
		$draw 		= $_POST['draw'];
		$limit 		= $length;

		$data	    = [];
		
        // Date Filters
        $from = isset($_POST['from']) ? $_POST['from'] : '';
        $to   = isset($_POST['to']) ? $_POST['to'] : '';
        $status   = isset($_POST['status']) ? $_POST['status'] : '';
        

		if($length == '-1') {
			$limit  = "";
        }

        // Query Variables
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
            "check_remarks",
            "check_status",
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
            $where = " is_delete = '0' AND entry_date >= '$from' AND entry_date <= '$to' AND check_status = '$status'";
        } else {
            $where = " is_delete = '0' AND entry_date >= '$from' AND entry_date <= '$to'";
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

        error_log("sql: " . print_r($result, true) . "\n", 3, "datatable_init.txt");

        error_log("result: " . $result->sql . "\n", 3, "sql_error_log.txt");
        
        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {

                $grn_no = $value['grn_number'];

                $grn_check_status_arr = fetch_grn_status($grn_no);
                $grn_check_status = isset($grn_check_status_arr[0]['check_status']) ? $grn_check_status_arr[0]['check_status'] : '';
                $grn_approve_status = isset($grn_check_status_arr[0]['approve_status']) ? $grn_check_status_arr[0]['approve_status'] : '';
                
                $company_data                   = company_name($value['company_id']);
                $value['company_id']            = $company_data[0]['company_name'];
                

               $company_data = project_name($value['project_id']);
                $value['project_id'] = $company_data[0]['project_code'] . " / " . $company_data[0]['project_name'];


                // $project_code = $project_options[0]['project_code'];

                $purchase_order_no  = get_po_number($value['po_number']);
                $value['po_number']            = $purchase_order_no[0]['purchase_order_no'];

                $supplier_names = supplier($value['supplier_name']);
                $value['supplier_name'] = $supplier_names[0]['supplier_name'];                
                $check_status = $value['check_status']; // logic usage

                // Use both check_status and approve_status from fetch_grn_status
                $btn_update = '';
                $btn_delete = '';
                $action_display = '';
                $status = '';
                $approve_status = '';
                
                $btn_view  = btn_views($folder_name, $value['unique_id']);
                $btn_print = btn_prints($folder_name, $value['unique_id']);
                $btn_upload = btn_docs($folder_name, $value['unique_id']);

                if ($grn_check_status == '0' && $grn_approve_status != '1') {
                    // Not checked and not approved: show update and delete
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $action_display = $btn_update . $btn_delete;
                    $status = '<span class="text-warning fw-bold">Pending</span>';
                } elseif ($grn_check_status != '0' && $grn_approve_status == '2') {
                    // Checked (approved/rejected) and approval is completed: show update and delete
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $action_display = $btn_update . $btn_delete;
                    $status = '<span class="text-warning fw-bold">Pending</span>';
                } elseif ($grn_check_status == '1' && $grn_approve_status == '1') {
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $action_display = $btn_delete;
                    $status = '<span class="text-success fw-bold">&#10003; Approved</span>';
                } elseif ($grn_check_status == '1' && $grn_approve_status != '1') {
                    $action_display = '';
                    $status = '<span class="text-success fw-bold">&#10003; Checked</span>';
                } elseif ($grn_check_status == '2') {
                    $action_display = '';
                    $status = '<span class="text-danger fw-bold">&#10007; Rejected</span>';
                } else {
                    // Default fallback: show update and delete
                    $btn_update = btn_update($folder_name, $value['unique_id']);
                    $btn_delete = $is_admin ? btn_delete($folder_name, $value['unique_id']) : '';
                    $action_display = $btn_update . $btn_delete;
                    $status = '<span class="text-warning fw-bold">Pending</span>';
                }
                
                $action_display .= $btn_upload;

                unset($value['check_status']); 

                $row = array_values($value);
                // array_splice($row, 3, 0, $project_code);
                $row[9] = $status;
                $row[10] = $action_display;
                
                array_splice($row, 10, 0, [$btn_view, $btn_print]);
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
        
        
    // case "grn_sub_add_update":

    //     $now_received_qty = 0;
    //     $screen_unique_id = $_POST["screen_unique_id"];
    //     $sublist_unique_id      = $_POST["sublist_unique_id"];
        
    //     $item_code      = $_POST["item_code"];
    //     $order_qty              = $_POST["order_qty"];
    //     $uom                    = $_POST["uom"];
    //     $now_received_qty       = $_POST["tot_qty"];
    //     $update_qty             = $_POST["update_qty"];
       
    //     $columns = [
    //         "grn_main_unique_id" => $unique_id, // Use actual form's unique_id if needed
    //         "screen_unique_id" => $screen_unique_id,
    //         "item_code"     => $item_code,
    //         "order_qty"             => $order_qty,
    //         "uom"                   => $uom,
    //         "now_received_qty"      => $now_received_qty,
    //         "update_qty"            => $update_qty ? $update_qty : 0,
    //     ];

    //         // If po_unique_id is set, add it to the columns
    //     if (isset($_POST["po_unique_id"])) {
    //         $po_unique_id = $_POST["po_unique_id"];
    //         $columns["po_unique_id"] = $po_unique_id;
    //     }
        
    //     if (!empty($sublist_unique_id)) {
    //         // Update existing sublist row
    //         $columns["updated"] = $date;
    //         $columns["updated_user_id"] = $user_id;
            
    //         $where = ["unique_id" => $sublist_unique_id];
            
    //         $action_obj = $pdo->update($sub_table, $columns, $where);
    //         $msg = "update";
    //     } else {
    //         // Insert new sublist row
    //         $columns["unique_id"] = unique_id();
    //         $columns["created"]   = $date;
    //         $columns["created_user_id"] = $user_id;
        
    //         $action_obj = $pdo->insert($sub_table, $columns);
    //         $msg = "add";
    //     }
        
    //     echo json_encode([
    //         "status" => $action_obj->status,
    //         "msg"    => $msg,
    //         "data"   => $action_obj->data,
    //         "error"  => $action_obj->error
    //     ]);

    // break;

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
        error_log("td_data: " . print_r($td_data, true) . "\n", 3, "td_data_log.txt");

        $tax = $td_data['tax'];
        $tax_name = tax($tax)[0]['tax_name'];
        $tax = tax($tax)[0]['tax_value'];
        $discount = $td_data['discount'];
        $discount_type = $td_data['discount_type'];

        $total_amount = 0;
        $taxed_val = 0;

        $pdo->query("SET @a := 0;");

        $columns = [
            "@a := @a + 1 AS s_no",
            "gs.item_code",
            "gs.order_qty",
            "gs.uom",
            "(gs.now_received_qty - gs.update_qty)",
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
            "ROUND(((gs.update_qty * poi_items.rate) - ((gs.update_qty * poi_items.rate * $discount) / 100)) + (((gs.update_qty * poi_items.rate - (gs.update_qty * poi_items.rate * $discount / 100)) * $tax) / 100), 2) AS amount",
            "gs.unique_id"
        ];

        error_log("columns: " . print_r($columns, true) . "\n", 3, "columns_log.txt");


        $table_details = [
        "$sub_table gs 
            LEFT JOIN ( 
                SELECT 
                    gs2.item_code, 
                    gs2.po_unique_id, 
                    (gs2.now_received_qty - gs2.update_qty) AS total_received_qty
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
        error_log("result: " . print_r($result, true) . "\n", 3, "row_log.txt");


        $data = [];
if ($result->status) {
    foreach ($result->data as $row) {
        // --- numbers ---
        $qty   = isset($row['update_qty']) ? (float)$row['update_qty'] : 0.0;
        $rate  = isset($row['rate'])       ? (float)$row['rate']       : 0.0;

        // $discount_type and $discount come from your PO-level settings above
        $discType = $discount_type;              // 1 = Percentage, 2 = Amount (string/number both ok)
        $discVal  = (float)$discount;            // value (percent or amount)
        $base     = $qty * $rate;

        // --- discount ---
        $discountAmt = 0.0;
        if ($discType == 1 || $discType === '1' || $discType === 'Percentage') {
            $discountAmt = ($base * $discVal) / 100;
        } elseif ($discType == 2 || $discType === '2' || $discType === 'Amount') {
            $discountAmt = $discVal;
        }

        $afterDiscount = max($base - $discountAmt, 0.0);

        // --- tax (use numeric $tax you set from tax(...)[0]['tax_value']) ---
        $rowTax = ($tax * $afterDiscount) / 100;

        // --- per-row display amount: afterDiscount + rowTax ---
        $row['amount'] = round($afterDiscount + $rowTax, 2);

        // --- totals to return (match GRN semantics) ---
        $total_amount += round($afterDiscount, 2); // pre-tax total
        $taxed_val    += round($rowTax, 2);        // tax total

        // ----- existing mapping/rendering below -----

        if (!$is_update) {
            $item_code = $row['item_code'];
            $row['now_received_qty'] = isset($latest_qty_map[$item_code]) ? $latest_qty_map[$item_code] : 0;
        }

        $item_data = item_name_list($row["item_code"]);
        $row["item_code"] = $item_data[0]["item_name"] . " / " . $item_data[0]["item_code"];

        $uom_data = unit_name($row["uom"]);
        $row["uom"] = !empty($uom_data[0]["unit_name"]) ? $uom_data[0]["unit_name"] : $row["uom"];

        // Map discount type for display
        if ($discType == 1 || $discType === '1' || $discType === 'Percentage') {
            $row['discount_type_display'] = 'Percentage';
            $row['discount_type'] = 'Percentage';
        } elseif ($discType == 2 || $discType === '2' || $discType === 'Amount') {
            $row['discount_type_display'] = 'Amount';
            $row['discount_type'] = 'Amount';
        } else {
            $row['discount_type_display'] = 'No Type';
            $row['discount_type'] = 'No Type';
        }

        $edit = btn_edit($btn_prefix, $row["unique_id"]);
        $del  = btn_delete($btn_prefix, $row["unique_id"]);
        $row["unique_id"] = $edit . $del;

        $data[] = array_merge(array_values($row), [
            'item_code'        => $row["item_code"],
            'order_qty'        => $row["order_qty"],
            'uom'              => $row["uom"],
            'now_received_qty' => $row["now_received_qty"],
            'update_qty'       => $row["update_qty"],
            'rate'             => $row["rate"],
            'remarks'          => $row["remarks"] ?? '',
            'tax'              => $row["tax_name"],
            'discount_type'    => $row["discount_type"],
            'discount'         => $row["discount"],
            'discount_type_display' => $row["discount_type_display"],
            'amount'           => $row['amount'],
            'unique_id'        => $row["unique_id"],
        ]);
    }

    echo json_encode([
        "draw"            => 1,
        "recordsTotal"    => count($data),
        "recordsFiltered" => count($data),
        "data"            => $data,
        "total"           => $total_amount, // ✅ pre-tax, after discount (matches GRN)
        "taxed"           => $taxed_val,    // ✅ total tax only (matches GRN)
    ]);
    // ...
}

    break;


    // case "grn_sub_edit":
    //     $unique_id = $_POST["unique_id"];
    //     $is_update = isset($_POST['is_update']) ? $_POST['is_update'] : false;

    //     $po_unique_id = fetch_po_unique_id1($sub_table, $unique_id);

    //     $po_sc_unique_id = fetch_po_sc_unique_id($po_unique_id);
    //     error_log("sc_un_id: " . print_r($po_sc_unique_id, true) . "\n", 3, "po_sc_log.txt");
    //     // $po_sc_unique_id = is_array($po_sc_unique_id) ? $po_sc_unique_id[0]["screen_unique_id"] : $po_sc_unique_id;

    //     $td_data = fetch_tax_discount($po_sc_unique_id);

    //     $tax = $td_data['tax'];
    //     $tax_name = tax($tax)[0]['tax_name'];
    //     $tax = tax($tax)[0]['tax_value'];
    //     $discount = $td_data['discount'];

    //     error_log("tax: " . $tax . "\n" . "discount: " . $discount . "\n", 3, "td_log.txt");


    //     $columns = [
    //         "gs.grn_main_unique_id",
    //         "gs.item_code",
    //         "gs.order_qty",
    //         "gs.uom",
    //         "IF('$po_unique_id' = 0, 0, COALESCE(grn_sub.total_received_qty, 0)) AS now_received_qty",
    //         "gs.update_qty",
    //         "poi_items.rate",
    //         "'$tax_name' AS tax_name",
    //         $discount,
    //         "ROUND(((gs.update_qty * poi_items.rate) - ((gs.update_qty * poi_items.rate * $discount) / 100)) + (((gs.update_qty * poi_items.rate - (gs.update_qty * poi_items.rate * $discount / 100)) * $tax) / 100), 2) AS amount",
    //         // "gs.unique_id"
    //     ];

    //     if ($is_update) {
    //         $table_details = [
    //             "$sub_table gs 
    //                 LEFT JOIN ( 
    //                     SELECT 
    //                         item_code, 
    //                         po_unique_id, 
    //                         SUM(update_qty) AS total_received_qty
    //                     FROM grn_sublist 
    //                     WHERE po_unique_id = '$po_unique_id'
    //                         AND unique_id != '$unique_id'
    //                         AND now_received_qty IS NOT NULL
    //                         AND is_delete = 0
    //                     GROUP BY item_code, po_unique_id
    //                 ) AS grn_sub 
    //                 ON gs.item_code = grn_sub.item_code 
    //                 AND gs.po_unique_id = grn_sub.po_unique_id
    //             LEFT JOIN purchase_order poi 
    //                 ON gs.po_unique_id = poi.unique_id
    //             LEFT JOIN purchase_order_items poi_items 
    //                 ON poi_items.screen_unique_id = poi.screen_unique_id 
    //                 AND poi_items.item_code = gs.item_code
    //             WHERE gs.unique_id = '$unique_id' 
    //             AND gs.is_delete = 0",
    //             $columns
    //         ];
    //     } else {
    //         $table_details = [
    //             "$sub_table gs 
    //                 LEFT JOIN ( 
    //                     SELECT 
    //                         item_code, 
    //                         po_unique_id, 
    //                         SUM(update_qty) AS total_received_qty
    //                     FROM grn_sublist 
    //                     WHERE po_unique_id = '$po_unique_id'
    //                         AND now_received_qty IS NOT NULL
    //                         AND is_delete = 0
    //                     GROUP BY item_code, po_unique_id
    //                 ) AS grn_sub 
    //                 ON gs.item_code = grn_sub.item_code 
    //                 AND gs.po_unique_id = grn_sub.po_unique_id
    //             LEFT JOIN purchase_order poi 
    //                 ON gs.po_unique_id = poi.unique_id
    //             LEFT JOIN purchase_order_items poi_items 
    //                 ON poi_items.screen_unique_id = poi.screen_unique_id 
    //                 AND poi_items.item_code = gs.item_code
    //             WHERE gs.unique_id = '$unique_id' 
    //             AND gs.is_delete = 0",
    //             $columns
    //         ];
    //     }

    //     $result = $pdo->select($table_details);
    //     error_log("result: " . print_r($result, true) . "\n", 3, "row_log.txt");

    //     if ($result->status) {
    //         $row = $result->data[0];
    //         error_log("row: " . print_r($row, true) . "\n", 3, "rows_log.txt");

    //         echo json_encode([
    //             "status" => true,
    //             "data"   => $row,
    //             "tax"    => $tax,
    //             "discount" => $discount,
    //             "msg"    => "edit_data",
    //             "error"  => null
    //         ]);
    //     } else {
    //         echo json_encode([
    //             "status" => false,
    //             "data"   => [],
    //             "msg"    => "error",
    //             "error"  => $result->error
    //         ]);
    //     }
    // break;


    
    // case "grn_sub_delete":
    //     $unique_id = $_POST["unique_id"];
    
    //     $columns = [
    //         "is_delete" => 1
    //     ];
    //     $where = [
    //         "unique_id" => $unique_id
    //     ];
    
    //     $action_obj = $pdo->update("$sub_table", $columns, $where);
    
    //     echo json_encode([
    //         "status" => $action_obj->status,
    //         "msg"    => $action_obj->status ? "delete_success" : "delete_error",
    //         "error"  => $action_obj->error,
    //         "sql"    => $action_obj->sql
    //     ]);
    // break;
    
    case 'project_name':

        $company_id          = $_POST['company_id'];

        $project_name_options  = get_project_name("",$company_id);

        $project_name_options  = select_option($project_name_options,"Select the Project Name");

        echo $project_name_options;
        
    break;    
    
    case 'get_purchase_order_no':

        $project_id = $_POST['project_id'];

        // Assuming get_purchase_orders_by_project returns an array of [id => value]
        $purchase_order_options  = get_po_number("",$project_id);

        $purchase_order_options = select_option($purchase_order_options, "Select Purchase Order No");

        echo $purchase_order_options;
    break;
    
    case "get_po_items_for_grn":

        $po_unique_id = $_POST["unique_id"];

        // Step 1: Fetch screen_unique_id and supplier from purchase_order
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

        // Step 2: Get supplier name
        $supplier_name = '';
        if ($supplier_id) {
            $supp = $pdo->select(["supplier_profile", ["supplier_name"]], ["unique_id" => $supplier_id]);
            if ($supp->status && count($supp->data)) {
                $supplier_name = $supp->data[0]["supplier_name"];
            }
        }

        // Step 3: Fetch matching items from purchase_order_items
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

// function generateGRN($label, &$labelData) {
//     $year = $_SESSION['acc_year'];
//     $number = 1;

//     do {
//         $paddedNumber = str_pad($number, 3, '0', STR_PAD_LEFT);
//         $grn = "GRN/$label/$year/$paddedNumber";
//         $number++;
//     } while (in_array($grn, $labelData));

//     // Optionally store the new GRN
//     $labelData[] = $grn;

//     return $grn;
// }

// function fetch_grn_number($table)
// {
//     global $pdo;

//     // Define the columns to be fetched (in this case, the grn_number)
//     $table_columns = [
//         "grn_number"
//     ];

//     // Prepare the details for the query
//     $table_details = [
//         $table,  // Specify the table name
//         $table_columns  // Specify the columns to fetch
//     ];

//     // Set the WHERE condition to filter by unique_id, is_active, and is_delete
//     $where = [
//         "is_active" => 1,     // Optional: depending on your use case
//         "is_delete" => 0      // Optional: depending on your use case
//     ];

//     // Perform the query (assuming your PDO object has a select() method)
//     $result = $pdo->select($table_details, $where);

//     $grn_numbers = [];

//     // Check if the query was successful and if data is returned
//     if ($result->status && !empty($result->data)) {
//         // Loop through the data and collect all the grn_number values
//         foreach ($result->data as $row) {
//             $grn_numbers[] = $row['grn_number'];
//         }
//         error_log($grn_numbers . "\n", 3, "grn_log.txt");
//         return $grn_numbers;
//     }
// }

function fetch_tax_discount($screen_unique_id)
{
    global $pdo; // ensure $pdo is available

    $table = "purchase_order_items";
    $columns = ["tax", "discount", "discount_type"];

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
            'discount_type' => $result->data[0]['discount_type']
        ];
    } else {
        return [
            'tax' => 0,
            'discount' => 0,
            'discount_type' => 0
        ];
    }
}

?>