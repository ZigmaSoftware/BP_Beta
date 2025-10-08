<?php
require_once("../../assets/PHPExcel-1.8/Classes/PHPExcel.php");
include '../../config/dbconfig.php';

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();

// Title
$sheet->setCellValue('A1', 'Customer List');

// Header Row
$headers = [
    'S.no', 'Customer No', 'Customer Name', 'Customer Division', 'Customer Group', 
    'Currency', 'Country', 'State', 'City', 'Address', 'Pincode', 
    'Mobile No', 'Phone No', 'Email ID', 'PAN No', 'GST No'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '3', $header);
    $col++;
}

// Fetch main data
$customers = $pdo->select(['customer_profile', '*'], ['is_delete' => 0])->data;
if (!$customers) {
    die("No customer data found.");
}

// Collect unique IDs for related tables
$country_ids   = array_unique(array_filter(array_column($customers, 'country_unique_id')));
$state_ids     = array_unique(array_filter(array_column($customers, 'state_unique_id')));
$city_ids      = array_unique(array_filter(array_column($customers, 'city_unique_id')));
$subcat_ids    = array_unique(array_filter(array_column($customers, 'customer_sub_category_id')));
$cat_ids       = array_unique(array_filter(array_column($customers, 'customer_category_id')));
$currency_ids  = array_unique(array_filter(array_column($customers, 'currency_id')));
$user_ids      = array_unique(array_filter(array_column($customers, 'sess_user_id')));

// Helper for batch fetching maps
function fetch_map($table, $id_col, $name_col, $ids) {
    global $pdo;
    if (empty($ids)) return [];
    $id_str = implode("','", array_map('addslashes', $ids));
    $result = $pdo->select([$table, [$id_col, $name_col]], "$id_col IN ('$id_str')")->data;
    return array_column($result, $name_col, $id_col);
}

// Lookup maps
$states        = fetch_map("states", "unique_id", "state_name", $state_ids);
$cities        = fetch_map("cities", "unique_id", "city_name", $city_ids);
$subcategories = fetch_map("customer_sub_category", "unique_id", "customer_sub_category", $subcat_ids);

// Users → Staff
$users           = $pdo->select(["user", ["unique_id", "staff_unique_id"]], "unique_id IN ('" . implode("','", $user_ids) . "')")->data;
$user_staff_map  = array_column($users, "staff_unique_id", "unique_id");
$staff_ids       = array_values($user_staff_map);
$staff           = fetch_map("staff", "unique_id", "staff_name", $staff_ids);

$row = 4;
$sno = 1;

foreach ($customers as $cust) {
    $staff_id = $user_staff_map[$cust["sess_user_id"]] ?? '';

    // Lookup values via helper functions
    $currency     = currency_creation_name($cust['currency_id'], $cust['country_unique_id']);
    $country_data = country($cust['country_unique_id']);
    $country_name = $country_data[0]['name'] ?? '';

    $cust_group_data = customer_group($cust['customer_group'], $cust['customer_category_id']);
    $cust_group_name = $cust_group_data[0]['customer_group'] ?? '';

    $sheet->setCellValue("A$row", $sno++);
    $sheet->setCellValue("B$row", $cust["customer_no"]);
    $sheet->setCellValue("C$row", $cust["customer_name"]);
    $sheet->setCellValue("D$row", $subcategories[$cust["customer_sub_category_id"]] ?? '');
    $sheet->setCellValue("E$row", $cust_group_name);
    $sheet->setCellValue("F$row", $currency[0]['currency_name'] ?? '');
    $sheet->setCellValue("G$row", $country_name);
    $sheet->setCellValue("H$row", $states[$cust["state_unique_id"]] ?? '');
    $sheet->setCellValue("I$row", $cities[$cust["city_unique_id"]] ?? '');
    $sheet->setCellValue("J$row", $cust["address"]);
    $sheet->setCellValue("K$row", $cust["pincode"]);
    $sheet->setCellValue("L$row", $cust["mobile_no"]);
    $sheet->setCellValue("M$row", $cust["phone_no"]);
    $sheet->setCellValue("N$row", $cust["email_id"]);
    $sheet->setCellValue("O$row", $cust["pan_no"]);
    $sheet->setCellValue("P$row", $cust["gst_no"]);
    $row++;
}

// Styling
$sheet->getStyle("A1:Q1")->getFont()->setBold(true);
$sheet->getStyle("A3:Q3")->getFont()->setBold(true);

// Export
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Customer_List.xls"');
header('Cache-Control: max-age=0');
ob_clean();
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');
exit;
?>
