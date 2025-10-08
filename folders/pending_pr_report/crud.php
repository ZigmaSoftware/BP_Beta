<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // hide from output
ini_set('log_errors', 1);     // log to server error log


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

        $columns = [
            "pr.company_id AS unit",
            "pr.project_id",
            "pr.pr_number",
            "pr.requisition_date",
            "pr.requisition_type",
            "pr.requisition_for",
            "pr.sales_order_id AS reference",
            "sub.item_code",
            "sub.item_description AS item_name",
            "sub.quantity AS qty",
            "IFNULL(poi.po_qty, 0) AS po_qty",
            "(sub.quantity - IFNULL(poi.po_qty, 0)) AS pending_qty",
            "sub.uom",
            "pr.remarks",
            "pr.created_user_id AS prepared_by",
            "pr.created AS prepared_dt",
            "sub.created_user_id AS authorized_by",
            "sub.created AS authorized_date",
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
                SELECT pr_sub_unique_id, SUM(quantity) AS po_qty
                FROM purchase_order_items
                WHERE is_delete = 0
                GROUP BY pr_sub_unique_id
             ) poi ON poi.pr_sub_unique_id = sub.unique_id",
            $columns
        ];

        $where = " pr.is_delete = '0' 
                   AND sub.is_delete = '0' 
                   AND (sub.quantity - IFNULL(poi.po_qty, 0)) > 0";

        

        $order_column = $_POST["order"][0]["column"] ?? 0;
        $order_dir    = $_POST["order"][0]["dir"] ?? "asc";
        $order_by     = datatable_sorting($order_column, $order_dir, $columns);

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
                1 => ["unique_id" => "1", "value" => "Regular"],
                '683568ca2fe8263239' => ["unique_id" => "683568ca2fe8263239", "value" => "Service"],
                '683588840086c13657' => ["unique_id" => "683588840086c13657", "value" => "Capital"]
            ];

            $requisition_for_options = [
                1 => ["unique_id" => "1", "value" => "Direct"],
                2 => ["unique_id" => "2", "value" => "SO"],
                3 => ["unique_id" => "3", "value" => "Ordered BOM"]
            ];

            $sno = $start + 1;
            foreach ($res_array as $row) {
                $company_data = company_name($row['unit']);
                $row['unit'] = $company_data[0]['company_name'];

                $project_data = project_name($row['project_id']);
                $row['project_id'] = $project_data[0]['project_code']." / ".$project_data[0]['project_name'];

                if (isset($requisition_type_options[$row['requisition_type']])) {
                    $row['requisition_type'] = $requisition_type_options[$row['requisition_type']]['value'];
                }
                if (isset($requisition_for_options[$row['requisition_for']])) {
                    $row['requisition_for'] = $requisition_for_options[$row['requisition_for']]['value'];
                }

                $item_data = item_name_list($row['item_code']);
                if (!empty($item_data)) {
                    $row['item_code'] = $item_data[0]['item_code'];
                    $row['item_name'] = $item_data[0]['item_name'];
                }

                $so_data = sales_order($row['reference']); 
                if (!empty($so_data)) {
                    $row['reference'] = $so_data[0]['sales_order_no']; 
                }

                $uom = unit_name($row['uom']);
                if (!empty($uom)) {
                    $row['uom'] = $uom[0]['unit_name'];
                }

                $row['prepared_by'] = user_name($row['prepared_by'])[0]['user_name'] ?? '-';
                $row['authorized_by'] = user_name($row['authorized_by'])[0]['user_name'] ?? '';

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
