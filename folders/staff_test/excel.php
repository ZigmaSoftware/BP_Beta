<?php
require_once("../../assets/PHPExcel-1.8/Classes/PHPExcel.php");
include '../../config/dbconfig.php';

$excel = new PHPExcel();
$excel->setActiveSheetIndex(0);
$sheet = $excel->getActiveSheet();

$sheet->setCellValue('A1', 'Staff Master Export');

$headers = [
    'S.no', 'Staff Name', 'Father Name', 'Mother Name', 'Employee ID', 'Premises Type', 'Attendance Setting',
    'Document DOB', 'Date of Birth', 'Personal Contact No', 'Age', 'Gender', 'Marital Status',
    'Office Contact No', 'Personal Email ID', 'Office Email ID', 'Blood Group', 'Aadhar Number',
    'PAN Number', 'GST Number', 'Claim Status', 'Present Country', 'Present State', 'Present City',
    'Present Building No', 'Present Street', 'Present Area', 'Present Pincode',
    'Permanent Country', 'Permanent State', 'Permanent City', 'Permanent Building No', 'Permanent Street',
    'Permanent Area', 'Permanent Pincode', 'Date of Join', 'Employment Type', 'Skill Level', 'Grade',
    'Designation', 'Work Location', 'Department', 'Biometric ID', 'Salary Category', 'Reporting Officer',
    'ESI Number', 'PF Number', 'Company Name'
];

$col = 'A';
foreach ($headers as $header) {
    $sheet->setCellValue($col . '2', $header);
    $col++;
}

// Fetch staff data
$staff = $pdo->select(['staff_test', '*'], ['is_delete' => 0])->data;
if (!$staff) die("No staff data found.");

// Collect mapping IDs
$country_ids        = array_unique(array_filter(array_column($staff, 'pre_country')));
$state_ids          = array_unique(array_filter(array_column($staff, 'pre_state')));
$city_ids           = array_unique(array_filter(array_column($staff, 'pre_city')));
$currency_ids       = array_unique(array_filter(array_column($staff, 'currency_type')));
$dept_ids           = array_unique(array_filter(array_column($staff, 'department')));
$blood_group        = array_unique(array_filter(array_column($staff, 'blood_group')));
$grade_ids          = array_unique(array_filter(array_column($staff, 'grade')));
$designation_ids    = array_unique(array_filter(array_column($staff, 'designation_unique_id')));
$work_location_ids  = array_unique(array_filter(array_column($staff, 'work_location')));
$department_ids     = array_unique(array_filter(array_column($staff, 'department')));
$sal_category_ids     = array_unique(array_filter(array_column($staff, 'salary_category')));
$company_ids     = array_unique(array_filter(array_column($staff, 'company_name')));

// Helper to fetch name maps
function fetch_map($table, $id_col, $name_col, $ids) {
    global $pdo;
    if (empty($ids)) return [];
    $id_str = implode("','", array_map('addslashes', $ids));
    $result = $pdo->select([$table, [$id_col, $name_col]], "$id_col IN ('$id_str')")->data;
    return array_column($result, $name_col, $id_col);
}

// Build mapping arrays
$countries = fetch_map("countries", "unique_id", "name", $country_ids);
$states    = fetch_map("states", "unique_id", "state_name", $state_ids);
$cities    = fetch_map("cities", "unique_id", "city_name", $city_ids);
$currency  = fetch_map("currency_creation", "unique_id", "currency_name", $currency_ids);
$departments = fetch_map("department_creation", "unique_id", "department", $dept_ids);
$blood = fetch_map("blood_group", "unique_id", "blood_name", $blood_group);
$grade = fetch_map("grade_type", "unique_id", "grade_type", $grade_ids);
$designation = fetch_map("designation_creation", "unique_id", "designation", $designation_ids);
$work_loc = fetch_map("work_location_creation", "unique_id", "work_location", $work_location_ids);
$dep = fetch_map("department_creation", "unique_id", "department", $department_ids);
$sal_cat = fetch_map("salary_category", "unique_id", "salary_category", $sal_category_ids);
$com_name = fetch_map("company_creation", "unique_id", "company_name", $company_ids);

// Start writing data
$row = 4;
$sno = 1;


foreach ($staff as $s) {
    
    $att = '';
    if(!empty($s['attendance_setting_id'])) {
        $att = attendance_setting($s['attendance_setting_id']);
    }
    $att_name = $att[0]['attendance_shift_name'];
    
    $grade_type = '';
    if(!empty($s['grade'])) {
        $grade = grade_type($s['grade']);
    }
    $grade_type = $grade[0]['grade_type'];
    
    $gender = '';
    if($s['gender'] == '1' || $s['gender'] == 1){
        $gender = 'male';
    } else if($s['gender'] == '2' || $s['gender'] == 2){
        $gender = 'female';
    } else {
        $gender = 'other';
    }
    
    $col = 'A';
    $sheet->setCellValue($col++ . $row, $sno++);
    $sheet->setCellValue($col++ . $row, $s['staff_name']);
    $sheet->setCellValue($col++ . $row, $s['father_name']);
    $sheet->setCellValue($col++ . $row, $s['mother_name']);
    $sheet->setCellValue($col++ . $row, $s['employee_id']);
    $sheet->setCellValue($col++ . $row, $s['premises_type']);
    $sheet->setCellValue($col++ . $row, $att_name);
    $sheet->setCellValue($col++ . $row, $s['doc_dob']);
    $sheet->setCellValue($col++ . $row, $s['date_of_birth']);
    $sheet->setCellValue($col++ . $row, $s['personal_contact_no']);
    $sheet->setCellValue($col++ . $row, $s['age']);
    $sheet->setCellValue($col++ . $row, $gender);
    $sheet->setCellValue($col++ . $row, $s['martial_status']);
    $sheet->setCellValue($col++ . $row, $s['office_contact_no']);
    $sheet->setCellValue($col++ . $row, $s['personal_email_id']);
    $sheet->setCellValue($col++ . $row, $s['office_email_id']);
    $sheet->setCellValue($col++ . $row, $blood[$s['blood_group']] ?? '');
    $sheet->setCellValue($col++ . $row, $s['aadhar_no']);
    $sheet->setCellValue($col++ . $row, $s['pan_no']);
    $sheet->setCellValue($col++ . $row, $s['gst_no']);
    $sheet->setCellValue($col++ . $row, $s['claim_status']);
    $sheet->setCellValue($col++ . $row, $countries[$s['pre_country']] ?? '');
    $sheet->setCellValue($col++ . $row, $states[$s['pre_state']] ?? '');
    $sheet->setCellValue($col++ . $row, $cities[$s['pre_city']] ?? '');
    $sheet->setCellValue($col++ . $row, $s['pre_building_no']);
    $sheet->setCellValue($col++ . $row, $s['pre_street']);
    $sheet->setCellValue($col++ . $row, $s['pre_area']);
    $sheet->setCellValue($col++ . $row, $s['pre_pincode']);
    $sheet->setCellValue($col++ . $row, $countries[$s['perm_country']] ?? '');
    $sheet->setCellValue($col++ . $row, $states[$s['perm_state']] ?? '');
    $sheet->setCellValue($col++ . $row, $cities[$s['perm_city']] ?? '');
    $sheet->setCellValue($col++ . $row, $s['perm_building_no']);
    $sheet->setCellValue($col++ . $row, $s['perm_street']);
    $sheet->setCellValue($col++ . $row, $s['perm_area']);
    $sheet->setCellValue($col++ . $row, $s['perm_pincode']);
    $sheet->setCellValue($col++ . $row, $s['date_of_join']);
    $sheet->setCellValue($col++ . $row, $s['employment_type']);
    $sheet->setCellValue($col++ . $row, $s['skill_level']);
    $sheet->setCellValue($col++ . $row, $grade_type);
    $sheet->setCellValue($col++ . $row, $designation[$s['designation_unique_id']] ?? '');
    $sheet->setCellValue($col++ . $row, $work_loc[$s['work_location']] ?? '');
    $sheet->setCellValue($col++ . $row, $dep[$s['department']] ?? '');
    $sheet->setCellValue($col++ . $row, $s['biometric_id']);
    $sheet->setCellValue($col++ . $row, $sal_cat[$s['salary_category']] ?? '');
    $sheet->setCellValue($col++ . $row, $s['reporting_officer']);
    $sheet->setCellValue($col++ . $row, $s['esi_no']);
    $sheet->setCellValue($col++ . $row, $s['pf_no']);
    $sheet->setCellValue($col++ . $row, $com_name[$s['company_name']] ?? '');

    $row++;
}

// Format
$sheet->getStyle("A1:Z1")->getFont()->setBold(true);
$sheet->getStyle("A2:AY2")->getFont()->setBold(true);

// Output
header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Staff_List.xls"');
header('Cache-Control: max-age=0');
ob_clean();
$writer = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
$writer->save('php://output');
exit;
?>