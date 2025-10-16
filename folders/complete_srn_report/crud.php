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

        // ✅ Columns for Complete srn Report
       $columns = [
            "@a := @a + 1 AS s_no",
        
            // srn details
            "srn.srn_number AS srn_no",
            "CASE 
                WHEN srn.entry_date IS NULL 
                     OR srn.entry_date = '' 
                     OR srn.entry_date = '0000-00-00' 
                     OR srn.entry_date = '0000-00-00 00:00:00' 
                THEN '-'
                ELSE DATE_FORMAT(srn.entry_date, '%d-%m-%Y')
            END AS srn_date
            ",        
            // Unit / Project / Vendor
            "srn.company_id AS unit",
            "srn.project_id",
            "sup.supplier_name AS vendor_name",
        
            // Vendor docs
            "srn.supplier_invoice_no AS supplier_invoice",
            "DATE_FORMAT(srn.invoice_date, '%d-%m-%Y') AS invoice_date",
            "srn.dc_no AS challan_no",
            "CASE 
                WHEN srn.eway_bill_no IS NULL OR srn.eway_bill_no = '' THEN '-'
                WHEN srn.eway_bill_date IS NULL 
                     OR srn.eway_bill_date = '' 
                     OR srn.eway_bill_date = '0000-00-00' THEN srn.eway_bill_no
                ELSE CONCAT(srn.eway_bill_no, ' (', DATE_FORMAT(srn.eway_bill_date, '%d-%m-%Y'), ')')
            END AS eway_bill_no
            ",      
            "CASE
                WHEN srn.transporter_name IS NULL OR srn.transporter_name = '' THEN '-'
                WHEN srn.vehicle_no IS NULL
                    OR srn.vehicle_no - ''
                    THEN srn.transporter_name ELSE CONCAT(srn.transporter_name, ' (', srn.vehicle_no, ')')
            END AS transport_name",
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
            "MAX(CASE WHEN srn.approve_status = 1 THEN gs.now_received_qty ELSE 0 END) AS accepted_qty",
            "MAX(CASE WHEN srn.approve_status = 2 THEN gs.now_received_qty ELSE 0 END) AS rejected_qty",
            "GREATEST(poi.quantity - MAX(CASE WHEN srn.approve_status = 1 THEN gs.now_received_qty ELSE 0 END), 0) AS pending_qty",
        
            // Pricing
            "gs.uom",
            "poi.rate",
            "(MAX(CASE WHEN srn.approve_status = 1 THEN gs.now_received_qty ELSE 0 END) * poi.rate) AS total_value",
        
            // Audit
            "CONCAT(u1.user_name, '<br>', DATE_FORMAT(srn.created, '%d-%m-%Y')) AS prepared_by",
            "CONCAT(u2.user_name) AS checked_by",
            "CONCAT(u3.user_name, '<br>', DATE_FORMAT(srn.updated, '%d-%m-%Y')) AS authorized_by",
        
            // Status (approval-aware)
            "CASE 
                WHEN srn.approve_status = 0 THEN 'Pending'
                WHEN srn.approve_status = 1 THEN 'Approved'
                WHEN srn.approve_status = 2 THEN 'Rejected'
                ELSE 'Pending'
            END AS status"
        ];

        // ✅ Join chain (srn → srn Sublist → PO → PO Items → Supplier)
        $table_details = [
            "srn
             INNER JOIN srn_sublist gs 
                ON srn.screen_unique_id = gs.screen_unique_id AND gs.is_delete = 0
             LEFT JOIN purchase_order po 
                ON srn.po_number = po.unique_id AND po.is_delete = 0
             LEFT JOIN purchase_order_items poi 
                ON po.screen_unique_id = poi.screen_unique_id 
                AND poi.item_code = gs.item_code 
                AND poi.is_delete = 0
             LEFT JOIN supplier_profile sup 
                ON po.supplier_id = sup.unique_id
             LEFT JOIN user u1 
                ON srn.created_user_id = u1.unique_id
             LEFT JOIN user u2 
                ON srn.checked_by = u2.unique_id
            LEFT JOIN user u3
                ON srn.approved_by = u3.unique_id",
            $columns
        ];

        // ✅ Exclude deleted/cancelled
        $where = "srn.is_delete = 0";
        
         // ✅ Apply filters
        if(!empty($_POST['company_id'])) $where .= " AND srn.company_id = '". $_POST['company_id'] ."'";
        if(!empty($_POST['project_id'])) $where .= " AND srn.project_id = '". $_POST['project_id'] ."'";
        // if(!empty($_POST['pr_number']))  $where .= " AND srn.srn_number = '". $_POST['pr_number'] ."'";
        
        if(!empty($_POST['srn_date_from'])){
            $where .= " AND srn.entry_date >= '". $_POST['srn_date_from'] ."'";
        }
        if(!empty($_POST['srn_date_to'])){
            $where .= " AND srn.entry_date <= '". $_POST['srn_date_to'] ."'";
        }
        if(isset($_POST['status']) && $_POST['status'] !== '') {
        if ($_POST['status'] == "0") {
            $where .= " AND srn.approve_status = 0";
        } elseif ($_POST['status'] == "1") {
            $where .= " AND srn.approve_status = 1";
        } elseif ($_POST['status'] == "2") {
            $where .= " AND srn.approve_status = 2";
        }
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
        $group_by     = "srn.srn_number, gs.item_code";

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
