<?php  

require_once("../../assets/PHPExcel-1.8/Classes/PHPExcel.php");
//require("../../config/comfun.php");

try {

    $conn = new PDO( "mysql:host=localhost;dbname=ascent_v2", "root", "9KF8sVGZakZBf8IH");
    $conn->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    }
    catch(PDOException $e)
    {
        echo $sql . "<br>" . $e->getMessage();
    }   
$excel = new PHPExcel();

$excel->setActiveSheetIndex(0);

$excel->getActiveSheet()
->setCellValue('A1','Attendance Abstract');

$excel->getActiveSheet()
->setCellValue('A3','S.no')
->setCellValue('B3','Emp ID')
->setCellValue('C3','Executive Name')
->setCellValue('D3','Location')
->setCellValue('E3','Designation')
// ->setCellValue('F3','Customer Group')
->setCellValue('G3','Department')
->setCellValue('H3','City')
->setCellValue('I3','Address')
->setCellValue('J3','Pincode')
->setCellValue('K3','Mobile No')
->setCellValue('L3','Phone No')
->setCellValue('M3','Email ID')
->setCellValue('N3','PAN No')
->setCellValue('O3','GST No')
->setCellValue('P3','Provisional No')
->setCellValue('Q3','Account Status')
->setCellValue('R3','Account Type');


    $i = 0;
    $row_val    = 4;

    
    $sql = ("SELECT *  from customer_profile where is_delete = 0");
    
        $users       = $conn->query($sql);
        foreach ($users as $row) {

            
            $city_details    = $conn->prepare("SELECT city_name FROM cities WHERE unique_id = '".$row['city_unique_id']."'");
            $city_details->execute();
            $city_name       = $city_details->fetch();

            $state_details   = $conn->prepare("SELECT state_name FROM states WHERE unique_id = '".$row['state_unique_id']."'");
            $state_details->execute();
            $state_name      = $state_details->fetch();

            $category_details   = $conn->prepare("SELECT customer_category FROM customer_category WHERE unique_id = '".$row['customer_category_id']."'");
            $category_details->execute();
            $category_name      = $category_details->fetch();

            $category_sub_details   = $conn->prepare("SELECT customer_sub_category FROM customer_sub_category WHERE unique_id = '".$row['customer_sub_category_id']."'");
            $category_sub_details->execute();
            $category_sub_name      = $category_sub_details->fetch();

            // $category_group_details   = $conn->prepare("SELECT customer_group FROM customer_group WHERE unique_id = '".$row['customer_group_id']."'");
            // $category_group_details->execute();
            // $category_group_name      = $category_group_details->fetch();

            // if($category_group_name["customer_group"] != ''){
            //     $customer_group = $category_group_name["customer_group"];   
            // }else{
            //     $customer_group = "";
            // }

            if($row['account_status'] == 1){
                $account_status = "Mapped";
            }else{
                $account_status = "Yet to Map";
            }
            if($row['account_type'] == 1){
                $account_type = "New";
            }else{
                $account_type = "Existing";
            }

            $excel->getActiveSheet()
            ->setCellValue('A'.$row_val, ++$i)
            ->setCellValue('B'.$row_val, ($row["customer_no"]))
            ->setCellValue('C'.$row_val, ($row["customer_name"]))
            ->setCellValue('D'.$row_val, ($category_name["customer_category"]))
            ->setCellValue('E'.$row_val, ($category_sub_name["customer_sub_category"]))
            // ->setCellValue('F'.$row_val, ($customer_group))
            ->setCellValue('G'.$row_val, ($state_name["state_name"]))
            ->setCellValue('H'.$row_val, ($city_name["city_name"]))
            ->setCellValue('I'.$row_val, ($row["address"]))
            ->setCellValue('J'.$row_val, ($row["pincode"]))
            ->setCellValue('K'.$row_val, ($row["mobile_no"]))
            ->setCellValue('L'.$row_val, ($row["phone_no"]))
            ->setCellValue('M'.$row_val, ($row["email_id"]))
            ->setCellValue('N'.$row_val, ($row["pan_no"]))
            ->setCellValue('O'.$row_val, ($row["gst_no"]))
            ->setCellValue('P'.$row_val, ($row["provisional_no"]))
            ->setCellValue('Q'.$row_val, ($account_status))
            ->setCellValue('R'.$row_val, ($account_type));
            
            $row_val++; 

       }

//for Styling

$styleArray = array(

    'font'=>array(

        'bold'=>true
    )
);

$excel->getActiveSheet()->getStyle('A1:R1')->applyFromArray($styleArray);
$excel->getActiveSheet()->getStyle('A3:R3')->applyFromArray($styleArray);

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment;filename="Customer List.xls"');
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