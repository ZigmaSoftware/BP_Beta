<?php
require_once("../../assets/PHPExcel-1.8/Classes/PHPExcel.php");
include '../../config/dbconfig.php';

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();

// Title
$sheet->setCellValue('A1', 'Supplier List');

// Headers
$headers = [
    'S.no', 'Vendor No', 'Supplier Name', 'Vendor Group', 'Manufacturer', 'Agent / Dealer', 'Service / Jobwork',
    'Credit Limit', 'Currency', 'Country', 'State', 'City', 'Address', 'Corporate Address', 'Pincode',
    'Mobile No', 'Email ID', 'Website', 'Fax No', 'PAN No', 'GST No', 'GST Reg Date',
    'GST Status', 'MSME Type', 'MSME Value', 'ARN No'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '3', $header);
    $col++;
}

// Fetch data
$suppliers = $pdo->select(['supplier_profile', '*'], ['is_delete' => 0])->data;
if (!$suppliers) die("No supplier data found.");

// Collect IDs for mapping
$country_ids   = array_unique(array_filter(array_column($suppliers, 'country')));
$state_ids     = array_unique(array_filter(array_column($suppliers, 'state')));
$city_ids      = array_unique(array_filter(array_column($suppliers, 'city')));
$cat_ids       = array_unique(array_filter(array_column($suppliers, 'group_unique_id')));
$currency_ids  = array_unique(array_filter(array_column($suppliers, 'currency_type')));

// Helper for fetching mapping
function fetch_map($table, $id_col, $name_col, $ids) {
    global $pdo;
    if (empty($ids)) return [];
    $id_str = implode("','", array_map('addslashes', $ids));
    $result = $pdo->select([$table, [$id_col, $name_col]], "$id_col IN ('$id_str')")->data;
    return array_column($result, $name_col, $id_col);
}

// Map data
$states    = fetch_map("states", "unique_id", "state_name", $state_ids);
$cities    = fetch_map("cities", "unique_id", "city_name", $city_ids);
$countries = fetch_map("countries", "unique_id", "name", $country_ids);
$groups    = fetch_map("customer_group", "unique_id", "customer_group", $cat_ids);
$currency  = fetch_map("currency_creation", "unique_id", "currency_name", $currency_ids);

// Start filling data
$row = 4;
$sno = 1;



foreach ($suppliers as $s) {
    
    $s_category = '';
    if(!empty($s['group_unique_id'])) {
        $s_category = supplier_category_name($s['group_unique_id']);
    }
    $group = $s_category[0]['category_name'];
    
    $msme_type = '';
    if (!empty($s['msme_type'])) {
        $msme_type = msme_creation_name($s['msme_type']);
    }
    $msme = $msme_type[0]['msme_type'];
    
    $sheet->setCellValue("A$row", $sno++);
    $sheet->setCellValue("B$row", $s["vendor_code"]);
    $sheet->setCellValue("C$row", $s["supplier_name"]);
    $sheet->setCellValue("D$row", $group);
    $sheet->setCellValue("E$row", $s["manufacturer_flag"] == 1 ? 'Yes' : 'No');
    $sheet->setCellValue("F$row", $s["agent_dealer_flag"] == 1 ? 'Yes' : 'No');
    $sheet->setCellValue("G$row", $s["service_jobwork_flag"] == 1 ? 'Yes' : 'No');
    $sheet->setCellValue("H$row", $s["credit_limit"]);
    $sheet->setCellValue("I$row", $currency[$s["currency_type"]] ?? '');
    $sheet->setCellValue("J$row", $countries[$s["country"]] ?? '');
    $sheet->setCellValue("K$row", $states[$s["state"]] ?? '');
    $sheet->setCellValue("L$row", $cities[$s["city"]] ?? '');
    $sheet->setCellValue("M$row", $s["address"]);
    $sheet->setCellValue("N$row", $s["corporate_address"]);
    $sheet->setCellValue("O$row", $s["pincode"]);
    $sheet->setCellValue("P$row", $s["contact_no"]);
    $sheet->setCellValue("Q$row", $s["email_id"]);
    $sheet->setCellValue("R$row", $s["website"]);
    $sheet->setCellValue("S$row", $s["fax_no"]);
    $sheet->setCellValue("T$row", $s["pan_no"]);
    $sheet->setCellValue("U$row", $s["gst_no"]);
    $sheet->setCellValue("V$row", $s["gst_reg_date"]);
    $sheet->setCellValue("W$row", $s["gst_status"] == 1 ? 'Active' : ($s["gst_status"] == 2 ? 'Inactive' : ''));
    $sheet->setCellValue("X$row", $msme);
    $sheet->setCellValue("Y$row", $s["msme_value"]);
    $sheet->setCellValue("Z$row", $s["arn_no"]);

    $row++;
}

// Bold headers
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);
$sheet->getStyle("A3:Z3")->getFont()->setBold(true);

// Output
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Supplier_List.xls"');
header('Cache-Control: max-age=0');
ob_clean();
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');
exit;
?>
