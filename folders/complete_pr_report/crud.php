<?php
include '../../config/dbconfig.php';
include '../../config/new_db.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'datatable':

        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = ($length == '-1') ? "" : $length;

        $data = [];

        // Columns aligned to Functional Doc (point 36)
        $columns = [
            "pr.company_id AS unit",
            "pr.project_id",
            "pr.pr_number AS indent_no",
            "pr.requisition_date AS indent_date",
            "pr.requisition_type",
            "pr.requisition_for",
            "pr.sales_order_id AS ref_so",
        
            // ✅ Computed Doc Status
            "CASE 
                WHEN (sub.quantity - IFNULL(poi.po_qty, 0)) <= 0 THEN 'Closed'
                WHEN sub.lvl_2_status = 1 THEN 'Authorised'
                ELSE 'Drafted'
             END AS doc_status",
        
             // ✅ Computed Item Status
            "CASE 
                WHEN IFNULL(poi.po_qty, 0) <= 0 THEN 'Pending'
                WHEN IFNULL(poi.po_qty, 0) > 0 AND IFNULL(poi.po_qty, 0) < sub.quantity THEN 'Partially Fulfilled'
                WHEN IFNULL(poi.po_qty, 0) >= sub.quantity THEN 'Fulfilled'
                ELSE 'Pending'
             END AS item_status",
             
            "sub.item_code",
            "sub.item_description AS item_name",
            "sub.quantity AS s_qty",
            "sub.uom",
            "IFNULL(poi.po_number, '') AS po_no",
            "CASE 
                WHEN poi.lvl_3_status = 1 THEN 'Approved (L3)'
                WHEN poi.lvl_3_status = 2 THEN 'Rejected (L3)'
                WHEN poi.lvl_2_status = 1 AND poi.gross_amount <= 1000000 THEN 'Approved (L2)'
                WHEN poi.lvl_2_status = 2 THEN 'Rejected (L2)'
                WHEN poi.po_status = 1 AND poi.gross_amount <= 300000 THEN 'Approved (L1)'
                WHEN poi.po_status = 2 THEN 'Rejected (L1)'
                WHEN (poi.po_status = 0 OR poi.po_status IS NULL) THEN 'Pending (L1)'
                WHEN poi.po_status = 1 AND poi.appr_gross_amount BETWEEN 300001 AND 1000000 
                     AND (poi.lvl_2_status = 0 OR poi.lvl_2_status IS NULL) THEN 'Pending (L2)'
                WHEN poi.po_status = 1 AND poi.appr_gross_amount > 1000000 
                     AND poi.lvl_2_status = 1 AND poi.lvl_2_gross_amount > 1000000 
                     AND (poi.lvl_3_status = 0 OR poi.lvl_3_status IS NULL) THEN 'Pending (L3)'
                ELSE 'Pending'
            END AS po_final_status",
        
            // Approved by / date
            // L1
            "CASE 
                WHEN poi.po_status IN (1,2) 
                THEN CONCAT(IFNULL(u1.user_name, poi.poa_user_id), '/', DATE_FORMAT(poi.poa_created_dt, '%Y-%m-%d'))
                ELSE '-'
             END AS l1_action_by",
            
            // L2
            "CASE 
                WHEN poi.lvl_2_status IN (1,2) 
                THEN CONCAT(IFNULL(u2.user_name, poi.lvl_2_user_id), '/', DATE_FORMAT(poi.lvl_2_created_dt, '%Y-%m-%d'))
                ELSE '-'
             END AS l2_action_by",
            
            // L3
            "CASE 
                WHEN poi.lvl_3_status IN (1,2) 
                THEN CONCAT(IFNULL(u3.user_name, poi.lvl_3_approved_by), '/', DATE_FORMAT(poi.lvl_3_approved_date, '%Y-%m-%d'))
                ELSE '-'
             END AS l3_action_by",



            "IFNULL(poi.po_qty, 0) AS po_qty",
            "IFNULL(poi.vendor_name, '') AS vendor_name",
        
            // ✅ Unified Receipt (GRN for material, SRN for service)
            "CASE 
                WHEN poi.po_type = '683568ca2fe8263239' 
                THEN IFNULL(srn.srn_no, '') 
                ELSE IFNULL(grn.grn_no, '') 
             END AS grn_no",
        
            "CASE 
                WHEN poi.po_type = '683568ca2fe8263239' 
                     AND (srn.srn_date IS NULL OR srn.srn_date = '' OR srn.srn_date = '0000-00-00' OR srn.srn_date = '0000-00-00 00:00:00') 
                THEN '-' 
                WHEN poi.po_type = '683568ca2fe8263239' 
                THEN srn.srn_date 
            
                WHEN poi.po_type != '683568ca2fe8263239' 
                     AND (grn.grn_date IS NULL OR grn.grn_date = '' OR grn.grn_date = '0000-00-00' OR grn.grn_date = '0000-00-00 00:00:00') 
                THEN '-' 
                ELSE grn.grn_date 
             END AS grn_date",
        
            // ✅ Users (Prepared = created_user_id, Authorised = updated_user_id)
            "CASE 
                WHEN pr.created_user_id IS NULL OR pr.created_user_id = '' THEN '-' 
                ELSE pr.created_user_id 
            END AS prepared_by",
            
            "CASE 
                WHEN pr.created IS NULL OR pr.created = '' OR pr.created = '0000-00-00 00:00:00' THEN '-' 
                ELSE pr.created 
            END AS prepared_dt",
            
            "CASE 
                WHEN sub.updated_user_id IS NULL OR sub.updated_user_id = '' THEN '-' 
                ELSE sub.updated_user_id 
            END AS authorized_by",
            
            "CASE 
                WHEN sub.updated IS NULL OR sub.updated = '' OR sub.updated = '0000-00-00 00:00:00' THEN '-' 
                ELSE sub.updated 
            END AS authorized_dt",
            
            "CASE 
                WHEN sub.lvl_2_status IS NULL OR sub.lvl_2_status = '' OR sub.lvl_2_status = '0' THEN 'Pending'
                WHEN sub.lvl_2_status = '1' THEN 'Approved'
                WHEN sub.lvl_2_status = '2' THEN 'Rejected'
                ELSE 'Pending'
             END AS authorized_status",

        ];

        $table_details = [
            "purchase_requisition pr
             JOIN purchase_requisition_items sub 
               ON pr.unique_id = sub.main_unique_id
             LEFT JOIN (
                SELECT 
                    poi.pr_sub_unique_id, 
                    SUM(poi.quantity) AS po_qty,
            
                    MAX(po.purchase_order_no) AS po_number,
                    MAX(po.status) AS po_status,
                    MAX(po.appr_gross_amount) AS appr_gross_amount,
                    MAX(po.appr_net_amount) AS appr_net_amount,
            
                    MAX(po.lvl_2_status) AS lvl_2_status,
                    MAX(po.lvl_2_gross_amount) AS lvl_2_gross_amount,
                    MAX(po.lvl_2_net_amount) AS lvl_2_net_amount,
                    MAX(po.lvl_2_user_id) AS lvl_2_user_id,
                    MAX(po.lvl_2_created_dt) AS lvl_2_created_dt,
            
                    MAX(po.lvl_3_status) AS lvl_3_status,
                    MAX(po.lvl_3_gross_amount) AS lvl_3_gross_amount,
                    MAX(po.lvl_3_net_amount) AS lvl_3_net_amount,
                    MAX(po.lvl_3_approved_by) AS lvl_3_approved_by,
                    MAX(po.lvl_3_approved_date) AS lvl_3_approved_date,
            
                    MAX(po.poa_user_id) AS poa_user_id,
                    MAX(po.poa_created_dt) AS poa_created_dt,
            
                    MAX(sup.supplier_name) AS vendor_name,
                    MAX(po.unique_id) AS po_unique_id,
                    MAX(po.gross_amount) AS gross_amount,
                    MAX(po.net_amount) AS net_amount,
                    MAX(po.purchase_order_type) AS po_type
                FROM purchase_order po
                JOIN purchase_order_items poi 
                  ON po.screen_unique_id = poi.screen_unique_id
                LEFT JOIN supplier_profile sup 
                  ON po.supplier_id = sup.unique_id
                WHERE po.is_delete = 0 AND poi.is_delete = 0
                GROUP BY poi.pr_sub_unique_id
            ) poi ON poi.pr_sub_unique_id = sub.unique_id

             LEFT JOIN (
                SELECT gri.po_unique_id,
                       GROUP_CONCAT(DISTINCT grn.grn_number ORDER BY grn.entry_date ASC SEPARATOR '<br>') AS grn_no,
                       GROUP_CONCAT(DISTINCT DATE_FORMAT(grn.entry_date, '%Y-%m-%d') ORDER BY grn.entry_date ASC SEPARATOR '<br>') AS grn_date
                FROM grn_sublist gri
                JOIN grn grn 
                  ON gri.screen_unique_id = grn.screen_unique_id
                WHERE grn.is_delete = 0 AND gri.is_delete = 0
                GROUP BY gri.po_unique_id
            ) grn ON grn.po_unique_id = poi.po_unique_id
        
             LEFT JOIN (
                SELECT sri.po_unique_id,
                       GROUP_CONCAT(DISTINCT srn.srn_number ORDER BY srn.entry_date ASC SEPARATOR '<br>') AS srn_no,
                       GROUP_CONCAT(DISTINCT DATE_FORMAT(srn.entry_date, '%Y-%m-%d') ORDER BY srn.entry_date ASC SEPARATOR '<br>') AS srn_date
                FROM srn_sublist sri
                JOIN srn srn 
                  ON sri.screen_unique_id = srn.screen_unique_id
                WHERE srn.is_delete = 0 AND sri.is_delete = 0
                GROUP BY sri.po_unique_id
             ) srn ON srn.po_unique_id = poi.po_unique_id
             LEFT JOIN user u1 ON u1.unique_id = poi.poa_user_id
             LEFT JOIN user u2 ON u2.unique_id = poi.lvl_2_user_id
             LEFT JOIN user u3 ON u3.unique_id = poi.lvl_3_approved_by",
            $columns
        ];

        // Base filter: Include ALL PRs (not only pending like earlier)
        $where = " pr.is_delete = '0' 
                   AND sub.is_delete = '0' ";

        // Ordering
        $order_column = $_POST["order"][0]["column"];
        $order_dir    = $_POST["order"][0]["dir"];
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

        // Searching
        $search_str   = datatable_searching($search, $columns);
        if ($search_str) {
            $where .= " AND ".$search_str;
        }
        
        $from_date       = $_POST['from_date'] ?? '';
        $to_date         = $_POST['to_date'] ?? '';
        $status          = $_POST['pr_status'] ?? '';
        $company_id      = $_POST['company_id'] ?? $_POST['company_name'] ?? '';
        $project_id      = $_POST['project_id'] ?? $_POST['project_name'] ?? '';
        $requisition_for = $_POST['requisition_for'] ?? '';

        // Date range
        if (!empty($from_date) && !empty($to_date)) {
            $where .= " AND DATE(pr.requisition_date) BETWEEN '$from_date' AND '$to_date'";
        } elseif (!empty($from_date)) {
            $where .= " AND DATE(pr.requisition_date) >= '$from_date'";
        } elseif (!empty($to_date)) {
            $where .= " AND DATE(pr.requisition_date) <= '$to_date'";
        }
        
        // Status (Pending / Approved / Rejected)
        if ($status !== '') {
            $where .= " AND sub.lvl_2_status = '$status'";
        }


        
        // Company
        if (!empty($company_id)) {
            $where .= " AND pr.company_id = '$company_id'";
        }
        
        // Project
        if (!empty($project_id)) {
            $where .= " AND pr.project_id = '$project_id'";
        }
        
        // Requisition For
        if (!empty($requisition_for)) {
            $where .= " AND pr.requisition_for = '$requisition_for'";
        }

        
        $sql_function = "SQL_CALC_FOUND_ROWS";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            $requisition_type_options = [
                1 => ["value" => "Regular"],
                '683568ca2fe8263239' => ["value" => "Service"],
                '683588840086c13657' => ["value" => "Capital"]
            ];

            $requisition_for_options = [
                1 => ["value" => "Direct"],
                2 => ["value" => "SO"],
                3 => ["value" => "Ordered BOM"]
            ];

            $doc_status_options = [
                0 => "Draft",
                1 => "Approved",
                2 => "Closed",
                3 => "Cancelled"
            ];

            $item_status_options = [
                0 => "Pending",
                1 => "Partially Fulfilled",
                2 => "Fulfilled",
                3 => "Cancelled"
            ];

            $sno = $start + 1;
            foreach ($res_array as $row) {

                // Company lookup
                $company_data = company_name($row['unit']);
                $row['unit'] = $company_data[0]['company_name'] ?? $row['unit'];

                // Project lookup
                $project_data = project_name($row['project_id']);
                $row['project_id'] = !empty($project_data) ? 
                    $project_data[0]['project_code']." / ".$project_data[0]['project_name'] : $row['project_id'];

                // Requisition Type mapping
                if (isset($requisition_type_options[$row['requisition_type']])) {
                    $row['requisition_type'] = $requisition_type_options[$row['requisition_type']]['value'];
                }

                // Requisition For mapping
                if (isset($requisition_for_options[$row['requisition_for']])) {
                    $row['requisition_for'] = $requisition_for_options[$row['requisition_for']]['value'];
                }

                // Doc Status
                $row['doc_status'] = $doc_status_options[$row['doc_status']] ?? $row['doc_status'];

                // Item Status
                $row['item_status'] = $item_status_options[$row['item_status']] ?? $row['item_status'];

                // Sales Order lookup
                $so_data = sales_order($row['ref_so']);
                if (!empty($so_data)) {
                    $row['ref_so'] = $so_data[0]['sales_order_no'];
                }

                // Item Master lookup
                $item_data = item_name_list($row['item_code']);
                if (!empty($item_data)) {
                    $row['item_code'] = $item_data[0]['item_code'];
                    $row['item_name'] = $item_data[0]['item_name'];
                }

                // UOM
                $uom = unit_name($row['uom']);
                if (!empty($uom)) {
                    $row['uom'] = $uom[0]['unit_name'];
                }

                $row['prepared_by'] = user_name($row['prepared_by'])[0]['user_name'] ?? '-';
                $row['authorized_by'] = user_name($row['authorized_by'])[0]['user_name'] ?? '';

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
