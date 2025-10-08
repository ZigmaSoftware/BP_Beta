<?php
include '../../config/dbconfig.php';
include '../../config/new_db.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'datatable':

        $search     = $_POST['search']['value'] ?? '';
        $length     = $_POST['length'] ?? 10;
        $start      = $_POST['start'] ?? 0;
        $draw       = $_POST['draw'] ?? 1;
        $limit      = ($length == '-1') ? "" : $length;

        $data = [];

        // ✅ Columns for Pending GRN Report
        $columns = [
            "@a := @a + 1 AS s_no",
        
            // PO details
            "po.company_id AS unit",
            "po.project_id",
            "po.purchase_order_no AS po_no",
            "po.entry_date AS po_date",
            "po.purchase_order_type AS po_type",
        
            // Vendor
            "sup.vendor_code AS vendor_code",
            "sup.supplier_name AS vendor_name",
        
            // PO Item
            "poi.item_code",
            "'' AS item_name",
            "poi.quantity",
            "poi.uom",
            "poi.rate",
        
            // ✅ Received & Pending Qty (approval-aware)
            "FORMAT(COALESCE(grn_sub.max_received_qty, 0), 2) AS received_qty",
            "FORMAT(GREATEST(poi.quantity - COALESCE(grn_sub.max_received_qty, 0), 0), 2) AS pending_qty",
        
            // Amount
            "(poi.quantity * poi.rate) AS amount",
        
            // GRN details
            "COALESCE(GROUP_CONCAT(DISTINCT grn.grn_number SEPARATOR '<br>'), '-') AS grn_no",
            "COALESCE(
                GROUP_CONCAT(
                    DISTINCT 
                    CASE 
                        WHEN grn.entry_date IS NULL 
                             OR grn.entry_date = '' 
                             OR grn.entry_date = '0000-00-00' 
                             OR grn.entry_date = '0000-00-00 00:00:00' 
                        THEN '-'
                        ELSE DATE_FORMAT(grn.entry_date, '%Y-%m-%d') 
                    END 
                    SEPARATOR '<br>'
                ), 
                '-'
            ) AS grn_date",
            // ✅ Status check (approval-aware)
            "CASE 
                WHEN grn_sub.has_approved = 0 THEN 'Pending'
                WHEN COALESCE(grn_sub.max_received_qty,0) = 0 THEN 'Pending'
                WHEN poi.quantity <= grn_sub.max_received_qty THEN 'Closed'
                WHEN poi.quantity > grn_sub.max_received_qty THEN 'Partially Received'
                ELSE 'Pending'
            END AS status"
        
        ];
        
        // ✅ Join chain
        $table_details = [
            "purchase_order po
             LEFT JOIN purchase_order_items poi 
                ON po.screen_unique_id = poi.screen_unique_id
             LEFT JOIN supplier_profile sup 
                ON po.supplier_id = sup.unique_id
             LEFT JOIN grn 
                ON grn.po_number = po.unique_id AND grn.is_delete = 0
             LEFT JOIN (
                SELECT 
                    gs.item_code,
                    MAX(CASE WHEN g.approve_status = 1 THEN gs.now_received_qty ELSE 0 END) AS max_received_qty,
                    MAX(CASE WHEN g.approve_status = 1 THEN 1 ELSE 0 END) AS has_approved
                FROM grn_sublist gs
                INNER JOIN grn g ON gs.screen_unique_id = g.screen_unique_id
                WHERE gs.is_delete = 0 AND g.is_delete = 0
                GROUP BY gs.item_code
            ) grn_sub ON poi.item_code = grn_sub.item_code",
            $columns
        ];
        
        $from_date  = $_POST['from_date'] ?? '';
        $to_date    = $_POST['to_date'] ?? '';
        $company_id = $_POST['company_id'] ?? '';
        $project_id = $_POST['project_id'] ?? '';
        $vendor_id  = $_POST['supplier_id'] ?? '';
        $status     = $_POST['pr_status'] ?? ''; 



        $where = " po.is_delete = 0 
           AND poi.is_delete = 0 
           AND po.purchase_order_type != '683568ca2fe8263239'  -- exclude Service POs
           AND NOT (
               grn_sub.has_approved = 1 
               AND poi.quantity <= COALESCE(grn_sub.max_received_qty, 0)
           )";



        // ✅ Date filter
        if (!empty($from_date) && !empty($to_date)) {
            $where .= " AND po.entry_date BETWEEN '".addslashes($from_date)."' AND '".addslashes($to_date)."'";
        }
        
        // ✅ Company filter
        if (!empty($company_id)) {
            $where .= " AND po.company_id = '".addslashes($company_id)."'";
        }
        
        // ✅ Project filter
        if (!empty($project_id)) {
            $where .= " AND po.project_id = '".addslashes($project_id)."'";
        }
        
        if (!empty($vendor_id)) {
            $where .= " AND sup.unique_id = '".addslashes($vendor_id)."'";
        }
        
        if (!empty($status)) {
            if ($status === "Pending") {
                $where .= " AND (
                    grn_sub.has_approved = 0
                    OR COALESCE(grn_sub.max_received_qty,0) = 0
                )";
            } elseif ($status === "Closed") {
                $where .= " AND poi.quantity <= COALESCE(grn_sub.max_received_qty, 0)";
            } elseif ($status === "Partially Received") {
                $where .= " AND poi.quantity > COALESCE(grn_sub.max_received_qty, 0)
                            AND COALESCE(grn_sub.max_received_qty,0) > 0";
            }
        }


        

        // Ordering
        $order_column = $_POST["order"][0]["column"] ?? 0;
        $order_dir    = $_POST["order"][0]["dir"] ?? "asc";
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

        // Searching
        $search_str   = datatable_searching($search, $columns);
        if ($search_str) {
            $where .= " AND ".$search_str;
        }

        // ✅ Aggregates require GROUP BY
        $sql_function = "SQL_CALC_FOUND_ROWS";
        $group_by     = "poi.unique_id";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function, $group_by);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            $sno = $start + 1;
            foreach ($res_array as $row) {
                // Company lookup
                $company_data = company_name($row['unit']);
                $row['unit'] = $company_data[0]['company_name'] ?? $row['unit'];

                // Project lookup
                $project_data = project_name($row['project_id']);
                $row['project_id'] = !empty($project_data) ?
                    $project_data[0]['project_code']." / ".$project_data[0]['project_name'] : $row['project_id'];
                    
                    
                $item = item_name_list($row['item_code'])[0];
                $row['item_code'] = $item['item_code'];
                $row['item_name'] = $item['item_name'];
                
                $row['uom'] = unit($row['uom'])[0]['unit_name'];

                // PO Type mapping
                if ($row['po_type'] == '683588840086c13657'){
                    $row['po_type'] = 'Capital';
                } elseif ($row['po_type'] == '683568ca2fe8263239'){
                    $row['po_type'] = "Service";
                } elseif ($row['po_type'] == '1') {
                    $row['po_type'] = 'Regular';
                } else {
                    $row['po_type'] = '-';
                }

                // Serial No
                $row['s_no'] = $sno;
                $sno++;

                $data[] = $row;
            }

            $json_array = [
                "draw"            => intval($draw),
                "recordsTotal"    => intval($total_records),
                "recordsFiltered" => intval($total_records), // keep your existing logic; if you have FOUND_ROWS() helper you can replace this
                "data"            => $data,
                "sql"             => $result->sql
            ];
        } else {
            $json_array = ["error" => $result];
        }

        echo json_encode($json_array);
        break;
        
        
        case "company_project":
            $company_id = $_POST['company_id'] ?? '';
        
            $options = '<option value="">Select the Project</option>';
            if (!empty($company_id)) {
                $projects = get_project_name('', $company_id); // assume this returns array
                $options = select_option($projects, "Select the Project");
            }
        
            echo $options;
            break;
        
        
}
