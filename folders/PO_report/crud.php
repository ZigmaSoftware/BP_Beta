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
        
        // Build once, reuse everywhere
        $po_status_case = "
            CASE 
                WHEN po.gross_amount <= 300000 THEN 
                    CASE 
                        WHEN po.status = 0 THEN 'Raised'
                        WHEN po.status = 1 THEN 'Approved'
                        WHEN po.status IN (2,3) THEN 'Rejected'
                        ELSE 'Unknown'
                    END
                WHEN po.gross_amount > 300000 AND po.gross_amount <= 1000000 THEN 
                    CASE 
                        WHEN po.lvl_2_status = 0 THEN 'Raised'
                        WHEN po.lvl_2_status = 1 THEN 'Approved'
                        WHEN po.lvl_2_status IN (2,3) THEN 'Rejected'
                        ELSE 'Unknown'
                    END
                WHEN po.gross_amount > 1000000 THEN 
                    CASE 
                        WHEN po.lvl_3_status = 0 THEN 'Raised'
                        WHEN po.lvl_3_status = 1 THEN 'Approved'
                        WHEN po.lvl_3_status IN (2,3) THEN 'Rejected'
                        ELSE 'Unknown'
                    END
                ELSE 'Unknown'
            END
        ";


        // ✅ Columns aligned to Functional Doc (Point 37 – PO Report) with fallback
        $columns = [
            "po.company_id AS unit",
            "po.project_id",
            "po.purchase_order_no AS po_no",
            "po.entry_date AS po_date",
            "po.purchase_order_type AS po_type",
        
            // Vendor
            "sup.vendor_code AS vendor_code",
            "sup.supplier_name AS vendor_name",
        
            // Values
            "po.net_amount AS basic_value",
            "IFNULL(SUM(poi.discount), 0) AS discount",
            "po.gross_amount AS total_value",
        
            // Currency / Exchange Rate + Ref SO with fallback
            "COALESCE(so.sales_order_no, so_fallback.sales_order_no) AS ref_so_no",
            "COALESCE(so.currency_id, so_fallback.currency_id) AS currency",
            "COALESCE(so.exchange_rate, so_fallback.exchange_rate) AS ex_rate",
        
            // Linked PR with fallback
            "COALESCE(pr.pr_number, pr_fallback.pr_number) AS linked_pr_no",
            "COALESCE(pr.requisition_date, pr_fallback.requisition_date) AS linked_pr_date",
        
            // Quotation
            "po.quotation_no",
        
            // Prepared / Authorized
            "po.created_user_id AS prepared_by",
            "po.created AS prepared_dt",
            "po.updated_user_id AS authorized_by",
            "po.updated AS authorized_dt",
        
            // GRN/SRN Number
            "CASE 
                WHEN po.purchase_order_type = '683568ca2fe8263239' 
                    THEN COALESCE(
                        NULLIF(GROUP_CONCAT(DISTINCT srn.srn_number SEPARATOR '<br>'), ''),
                        '-'
                    )
                ELSE COALESCE(
                        NULLIF(GROUP_CONCAT(DISTINCT grn.grn_number SEPARATOR '<br>'), ''),
                        '-'
                    )
             END AS grn_no",

            // GRN/SRN Date
            "CASE 
                WHEN po.purchase_order_type = '683568ca2fe8263239' 
                    THEN COALESCE(
                        NULLIF(GROUP_CONCAT(DISTINCT 
                            CASE 
                                WHEN srn.entry_date IS NULL 
                                     OR srn.entry_date = '' 
                                     OR srn.entry_date = '0000-00-00' 
                                     OR srn.entry_date = '0000-00-00 00:00:00' 
                                THEN '-' 
                                ELSE DATE_FORMAT(srn.entry_date, '%Y-%m-%d') 
                            END 
                        SEPARATOR '<br>'), ''),
                        '-'
                    )
                ELSE COALESCE(
                        NULLIF(GROUP_CONCAT(DISTINCT 
                            CASE 
                                WHEN grn.entry_date IS NULL 
                                     OR grn.entry_date = '' 
                                     OR grn.entry_date = '0000-00-00' 
                                     OR grn.entry_date = '0000-00-00 00:00:00' 
                                THEN '-' 
                                ELSE DATE_FORMAT(grn.entry_date, '%Y-%m-%d') 
                            END 
                        SEPARATOR '<br>'), ''),
                        '-'
                    )
             END AS grn_date",

        
            // Status with multi-level approval logic
                        // Final normalized status
            "$po_status_case AS po_status"
        ];
        
        // ✅ Join chain with fallback logic
        $table_details = [
            "purchase_order po
             LEFT JOIN purchase_order_items poi 
                ON po.screen_unique_id = poi.screen_unique_id

             -- Normal PR/SO path
             LEFT JOIN purchase_requisition_items pri 
                ON poi.pr_sub_unique_id = pri.unique_id
             LEFT JOIN purchase_requisition pr 
                ON pri.main_unique_id = pr.unique_id
             LEFT JOIN sales_order so 
                ON pr.sales_order_id = so.unique_id

             -- Fallback via OBOM child
             LEFT JOIN obom_child_table obc 
                ON poi.pr_sub_unique_id = obc.unique_id
             LEFT JOIN sales_order so_fallback 
                ON obc.so_unique_id = so_fallback.unique_id
             LEFT JOIN purchase_requisition pr_fallback 
                ON pr_fallback.sales_order_id = so_fallback.unique_id
             LEFT JOIN purchase_requisition_items pri_fallback 
                ON pri_fallback.main_unique_id = pr_fallback.unique_id

             -- Vendor
             LEFT JOIN supplier_profile sup 
                ON po.supplier_id = sup.unique_id

             -- GRN / SRN
             LEFT JOIN grn 
                ON grn.po_number = po.unique_id AND grn.is_delete = 0
             LEFT JOIN srn 
                ON srn.po_number = po.unique_id AND srn.is_delete = 0",
            $columns
        ];
        
        $where = " po.is_delete = 0 AND poi.is_delete = 0 ";

        $from_date  = $_POST['from_date'] ?? '';
        $to_date    = $_POST['to_date'] ?? '';
        $status     = $_POST['po_status'] ?? '';
        $company_id = $_POST['company_id'] ?? '';
        $project_id = $_POST['project_id'] ?? '';
        $vendor_id  = $_POST['supplier_id'] ?? '';
        
         // ✅ Apply filters
        if (!empty($from_date) && !empty($to_date)) {
            $where .= " AND DATE(po.entry_date) BETWEEN '$from_date' AND '$to_date'";
        } elseif(empty($from_date) && !empty($to_date)) {
            $where .= " AND DATE(po.entry_date) <= '$to_date'";
        } elseif(empty($to_date) && !empty($from_date)) {
            $where .= " AND DATE(po.entry_date) >= '$from_date'";
        } else {
            
        }

        if (!empty($company_id)) {
            $where .= " AND po.company_id = '". $company_id ."'";
        }

        if (!empty($project_id)) {
            $where .= " AND po.project_id = '". $project_id ."'";
        }
        
        if (!empty($vendor_id)) {
            $where .= " AND sup.unique_id = '".addslashes($vendor_id)."'";
        }

        if ($status !== '') {
            // UI mapping: Pending -> Raised
            $statusMap = [
                'Pending'  => 'Raised',
                'Approved' => 'Approved',
                'Rejected' => 'Rejected'
            ];
            if (isset($statusMap[$status])) {
                $where .= " AND ($po_status_case) = '" . $statusMap[$status] . "'";
            }
        }



        // Ordering
        $order_column = $_POST["order"][0]["column"];
        $order_dir    = $_POST["order"][0]["dir"];
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

        // Searching
        $search_str   = datatable_searching($search, $columns);
        if ($search_str) {
            $where .= " AND ".$search_str;
        }

        // ✅ Aggregates require GROUP BY
        $sql_function = "SQL_CALC_FOUND_ROWS";
        $group_by     = "po.unique_id";

        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function, $group_by);
        error_log(print_r($result, true), 3, "result.log");
        $total_records = total_records();

        if ($result->status) {
            $res_array = $result->data;

            $sno = $start + 1;
            foreach ($res_array as $row) {
                // Company lookup
                $company_data = company_name($row['unit']);
                $row['unit'] = $company_data[0]['company_name'] ?? $row['unit'];
                
                if ($row['po_type'] == '683588840086c13657'){
                    $row['po_type'] = 'Capital';
                } elseif ($row['po_type'] == '683568ca2fe8263239'){
                    $row['po_type'] = "Service";
                } elseif ($row['po_type'] == '1') {
                    $row['po_type'] = 'Regular';
                } else {
                    $row['po_type'] = '-';
                }
                
                $row['currency'] = $row['currency'] ? currency_creation_name($row['currency'])[0]['currency_name'] : '-';
                
                // Project lookup
                $project_data = project_name($row['project_id']);
                $row['project_id'] = !empty($project_data) ?
                    $project_data[0]['project_code']." / ".$project_data[0]['project_name'] : $row['project_id'];

                // User lookups
                $row['prepared_by']   = user_name($row['prepared_by'])[0]['user_name'] ?? '-';
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
    } else {
        $projects = get_project_name('', $company_id); // assume this returns array
    }
    
    $options = select_option($projects, "Select the Project");

    echo $options;
    break;

}
