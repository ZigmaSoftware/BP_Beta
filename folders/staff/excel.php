<?php  

require_once("../../assets/PHPExcel-1.8/Classes/PHPExcel.php");
//require("../../config/comfun.php");
if($_REQUEST['staff_status'] == 0){
    $relieve_date = '';
}else if($_REQUEST['staff_status'] == 1){
    $relieve_date = ' AND relieve_date = ""';
}else{
    $relieve_date = ' AND relieve_date != ""';
}
    $driver         = "mysql";
    $host           = "localhost";
    $username       = "zigma";
    $password       = "?WSzvxHv1LGZ";
    $databasename   = "blue_planet"; 

try {

    $conn = new PDO( $driver.":host=".$host.";dbname=".$databasename, $username, $password);

    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }   
$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);

$excel->getActiveSheet()
->setCellValue('A1','Saff List');

$excel->getActiveSheet()
->setCellValue('A3','S.no')
->setCellValue('B3','Emp No')
->setCellValue('C3','Staff Name')
->setCellValue('D3','DOJ')
->setCellValue('E3','Department')
->setCellValue('F3','Designation')
->setCellValue('G3','Gross Salary')
->setCellValue('H3','Father Name')
->setCellValue('I3','DOB')
->setCellValue('J3','Age')
->setCellValue('K3','Original DOB')
->setCellValue('L3','ESI No')
->setCellValue('M3','PF No')
->setCellValue('N3','Per. Mob No')
->setCellValue('O3','CUG')
->setCellValue('P3','Per. Mail ID')
->setCellValue('Q3','Off Mail ID')
->setCellValue('R3','Blood Group')
->setCellValue('S3','Perm Address')
->setCellValue('T3','Present Address')
->setCellValue('U3','Aadhar No')
->setCellValue('V3','PAN No')
->setCellValue('W3','Acc NO')
->setCellValue('X3','Bank Name')
->setCellValue('Y3','IFSC No')
->setCellValue('Z3','Reporting Head');


    $i = 0;
    $row_val    = 4;
          
    $sql = ("SELECT *  from staff where is_delete = 0".$relieve_date);

        $users       = $conn->query($sql);
        foreach ($users as $row) {

            $designation_details   = $conn->prepare("SELECT designation FROM designation_creation WHERE unique_id = '".$row['designation_unique_id']."'");
            $designation_details->execute();
            $designation_name      = $designation_details->fetch();

            $blood_details   = $conn->prepare("SELECT blood_name FROM blood_group WHERE unique_id = '".$row['blood_group']."'");
            $blood_details->execute();
            $blood_name      = $blood_details->fetch();

            $pre_city_details    = $conn->prepare("SELECT city_name FROM cities WHERE unique_id = '".$row['pre_city']."'");
            $pre_city_details->execute();
            $pre_city_name       = $pre_city_details->fetch();

            $pre_state_details   = $conn->prepare("SELECT state_name FROM states WHERE unique_id = '".$row['pre_state']."'");
            $pre_state_details->execute();
            $pre_state_name      = $pre_state_details->fetch();

            $perm_city_details    = $conn->prepare("SELECT city_name FROM cities WHERE unique_id = '".$row['perm_city']."'");
            $perm_city_details->execute();
            $perm_city_name       = $perm_city_details->fetch();

            $perm_state_details   = $conn->prepare("SELECT state_name FROM states WHERE unique_id = '".$row['perm_state']."'");
            $perm_state_details->execute();
            $perm_state_name      = $perm_state_details->fetch();

            $bank_details   = $conn->prepare("SELECT bank_name,account_no,ifsc_code FROM staff_account_details WHERE  staff_unique_id = '".$row['unique_id']."'");
            $bank_details->execute();
            $bank       = $bank_details->fetch();
            if (!empty($bank)) {
                $bank_name  = $bank['bank_name'];
                $account_no = $bank['account_no'];
                $ifsc_no    = $bank['ifsc_code'];
            }else{
                $bank_name  = "";
                $account_no = "";
                $ifsc_no    = "";
            }   

            $reporting_officer   = $conn->prepare("SELECT staff_name FROM staff WHERE unique_id = '".$row['reporting_officer']."'");
            $reporting_officer->execute();
            $reporting_staff     = $reporting_officer->fetch();

            $pre_city        = $pre_city_name['city_name'];
            $pre_state       = $pre_state_name['state_name'];
            $pre_building_no = $row['pre_building_no'];
            $pre_street      = $row['pre_street'];
            $pre_area        = $row['pre_area'];
            $pre_pincode     = $row['pre_pincode'];

            $present_address  = $pre_building_no." ".$pre_street." ".$pre_area." ".$pre_city." ".$pre_state."-".$pre_pincode;


            $perm_city        = $perm_city_name['city_name'];
            $perm_state       = $perm_state_name['state_name'];
            $perm_building_no = $row['perm_building_no'];
            $perm_street      = $row['perm_street'];
            $perm_area        = $row['perm_area'];
            $perm_pincode     = $row['perm_pincode'];

            $permanent_address  = $perm_building_no." ".$perm_street." ".$perm_area." ".$perm_city." ".$perm_state."-".$perm_pincode;

            $excel->getActiveSheet()
            ->setCellValue('A'.$row_val, ++$i)
            ->setCellValue('B'.$row_val, ($row["employee_id"]))
            ->setCellValue('C'.$row_val, ($row["staff_name"]))
            ->setCellValue('D'.$row_val, disdate($row["date_of_join"]))
            ->setCellValue('E'.$row_val, ($row["department"]))
            ->setCellValue('F'.$row_val, ($designation_name['designation_type']))
            ->setCellValue('G'.$row_val, ($row["salary"]))
            ->setCellValue('H'.$row_val, ($row["father_name"]))
            ->setCellValue('I'.$row_val, disdate($row["date_of_birth"]))
            ->setCellValue('J'.$row_val, ($row["age"]))
            ->setCellValue('K'.$row_val, disdate($row["doc_dob"]))
            ->setCellValue('L'.$row_val, ($row["esi_no"]))
            ->setCellValue('M'.$row_val, ($row["pf_no"]))
            ->setCellValue('N'.$row_val, ($row["personal_contact_no"]))
            ->setCellValue('O'.$row_val, ($row["office_contact_no"]))
            ->setCellValue('P'.$row_val, ($row["personal_email_id"]))
            ->setCellValue('Q'.$row_val, ($row["office_email_id"]))
            ->setCellValue('R'.$row_val, ($blood_name["blood_name"]))
            ->setCellValue('S'.$row_val, ($permanent_address))
            ->setCellValue('T'.$row_val, ($present_address))
            ->setCellValue('U'.$row_val, ($row["aadhar_no"]))
            ->setCellValue('V'.$row_val, ($row["pan_no"]))
            ->setCellValue('W'.$row_val, ($account_no))
            ->setCellValue('X'.$row_val, ($bank_name))
            ->setCellValue('Y'.$row_val, ($ifsc_no))
            ->setCellValue('Z'.$row_val, ($reporting_staff["staff_name"]));
            
            $row_val++; 

       }

//for Styling

$styleArray = array(

    'font'=>array(

        'bold'=>true
    )
);

$excel->getActiveSheet()->getStyle('A1:Z1')->applyFromArray($styleArray);
$excel->getActiveSheet()->getStyle('A3:Z3')->applyFromArray($styleArray);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Staff List.xls"');
header('Cache-Control: max-age=0');

$fileDownload = PHPExcel_IOFactory::createWriter($excel, 'Excel5');
ob_clean();
$fileDownload->save('php://output');

function disdate ($date) {

    $result     = "";

    if ($date) {
        $result =  implode("-",array_reverse(explode("-",$date)));
    }

    return $result;
}

// State Function
function state($unique_id = '',$country_id = "") {
    global $pdo;

    $table_name    = "states";
    $where         = [];
    $table_columns = [
        "unique_id",
        "state_name",
        "state_code"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($country_id) {
        // $where = " WHERE country_id = '".$country_id."' ";
        $where["country_unique_id"] = $country_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $states = $pdo->select($table_details, $where);

    // print_r($states);

    if ($states->status) {
        return $states->data;
    } else {
        print_r($states);
        return 0;
    }
}

// City Function
function city($unique_id = "",$state_id = "") {
    global $pdo;

    $table_name    = "cities";
    $where         = [];
    $table_columns = [
        "unique_id",
        "city_name"
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where     = [
        "is_active" => 1,
        "is_delete" => 0
    ];

    if ($state_id) {
        // $where = " WHERE state_id = '".$state_id."' ";
        $where["state_unique_id"] = $state_id;
    }

    if ($unique_id) {
        $table_details = $table_name;
        $where         = [];
        $where         = [
            "unique_id" => $unique_id
        ];
    }

    $cities = $pdo->select($table_details, $where);

    // print_r($cities);

    if ($cities->status) {
        return $cities->data;
    } else {
        print_r($cities);
        return 0;
    }
}