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

        // ✅ Columns for Complete GRN Report
       $columns = [
            "@a := @a + 1 AS s_no",
        
            // GRN details
            "grn.grn_number AS grn_no",
            "CASE 
                WHEN grn.entry_date IS NULL 
                     OR grn.entry_date = '' 
                     OR grn.entry_date = '0000-00-00' 
                     OR grn.entry_date = '0000-00-00 00:00:00' 
                THEN '-'
                ELSE DATE_FORMAT(grn.entry_date, '%d-%m-%Y')
            END AS grn_date
            ",        
            // Unit / Project / Vendor
            "grn.company_id AS unit",
            "grn.project_id",
            "sup.supplier_name AS vendor_name",
        
            // Vendor docs
            "grn.supplier_invoice_no AS supplier_invoice",
            "DATE_FORMAT(grn.invoice_date, '%d-%m-%Y') AS invoice_date",
            "grn.dc_no AS challan_no",
            "CASE 
                WHEN grn.eway_bill_no IS NULL OR grn.eway_bill_no = '' THEN '-'
                WHEN grn.eway_bill_date IS NULL 
                     OR grn.eway_bill_date = '' 
                     OR grn.eway_bill_date = '0000-00-00' THEN grn.eway_bill_no
                ELSE CONCAT(grn.eway_bill_no, ' (', DATE_FORMAT(grn.eway_bill_date, '%d-%m-%Y'), ')')
            END AS eway_bill_no
            ",        
            // PO linkage
            "po.purchase_order_no AS po_no",
        
            // Item details
            "gs.item_code",
            "'' AS item_name",
            "CASE 
                WHEN poi.lvl_3_quantity IS NOT NULL AND poi.lvl_3_quantity > 0 THEN poi.lvl_3_quantity
                WHEN poi.lvl_2_quantity IS NOT NULL AND poi.lvl_2_quantity > 0 THEN poi.lvl_2_quantity
                WHEN poi.appr_quantity IS NOT NULL AND poi.appr_quantity > 0 THEN poi.appr_quantity
                ELSE poi.quantity
            END AS po_qty",
        
            // Accepted / Rejected / Pending (approval-aware)
            "MAX(CASE WHEN grn.approve_status = 1 THEN gs.now_received_qty ELSE 0 END) AS accepted_qty",
            "MAX(CASE WHEN grn.approve_status = 2 THEN gs.now_received_qty ELSE 0 END) AS rejected_qty",
            "GREATEST(poi.quantity - MAX(CASE WHEN grn.approve_status = 1 THEN gs.now_received_qty ELSE 0 END), 0) AS pending_qty",
        
            // Pricing
            "gs.uom",
            "poi.rate",
            "(MAX(CASE WHEN grn.approve_status = 1 THEN gs.now_received_qty ELSE 0 END) * poi.rate) AS total_value",
        
            // Audit
            "CONCAT(u1.user_name, '<br>', DATE_FORMAT(grn.created, '%d-%m-%Y')) AS prepared_by",
            "CONCAT(u2.user_name, '<br>', DATE_FORMAT(grn.updated, '%d-%m-%Y')) AS authorized_by",
        
            // Status (approval-aware)
            "CASE 
                WHEN grn.approve_status = 0 THEN 'Pending'
                WHEN grn.approve_status = 1 THEN 'Approved'
                WHEN grn.approve_status = 2 THEN 'Rejected'
                ELSE 'Pending'
            END AS status"
        ];

        // ✅ Join chain (GRN → GRN Sublist → PO → PO Items → Supplier)
        $table_details = [
            "grn
             INNER JOIN grn_sublist gs 
                ON grn.screen_unique_id = gs.screen_unique_id AND gs.is_delete = 0
             LEFT JOIN purchase_order po 
                ON grn.po_number = po.unique_id AND po.is_delete = 0
             LEFT JOIN purchase_order_items poi 
                ON po.screen_unique_id = poi.screen_unique_id 
                AND poi.item_code = gs.item_code 
                AND poi.is_delete = 0
             LEFT JOIN supplier_profile sup 
                ON po.supplier_id = sup.unique_id
             LEFT JOIN user u1 
                ON grn.created_user_id = u1.unique_id
             LEFT JOIN user u2 
                ON grn.approved_by = u2.unique_id",
            $columns
        ];
        
        $from_date  = $_POST['from_date'] ?? '';
        $to_date    = $_POST['to_date'] ?? '';
        $company_id = $_POST['company_id'] ?? '';
        $project_id = $_POST['project_id'] ?? '';
        $status     = $_POST['grn_status'] ?? '';

        // ✅ Exclude deleted/cancelled
        

        $where = "grn.is_delete = 0";
        
        if (!empty($from_date) && !empty($to_date)) {
            $where .= " AND DATE(grn.entry_date) BETWEEN '".$from_date."' AND '".$to_date."'";
        }
        if (!empty($company_id)) {
            $where .= " AND grn.company_id = '".$company_id."'";
        }
        if (!empty($project_id)) {
            $where .= " AND grn.project_id = '".$project_id."'";
        }
        if ($status !== '') {
            $where .= " AND grn.approve_status = '" . $status . "'";
        }





        // Ordering
        $order_column = $_POST["order"][0]["column"] ?? 0;
        $order_dir    = $_POST["order"][0]["dir"] ?? "asc";
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

        // Searching
        $search_str   = datatable_searching($search, $columns);
        if ($search_str) {
            $where .= " AND " . $search_str;
        }

        // ✅ Aggregates require GROUP BY
        $sql_function = "SQL_CALC_FOUND_ROWS";
        $group_by     = "grn.grn_number, gs.item_code";

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

                // Item lookup
                $item = item_name_list($row['item_code'])[0];
                $row['item_code'] = $item['item_code'];
                $row['item_name'] = $item['item_name'];

                // UOM
                $row['uom'] = unit($row['uom'])[0]['unit_name'];

                // Serial No
                $row['s_no'] = $sno;
                $sno++;

                $data[] = $row;
            }

            $json_array = [
                "draw"            => intval($draw),
                "recordsTotal"    => intval($total_records),
                "recordsFiltered" => intval($total_records),
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
