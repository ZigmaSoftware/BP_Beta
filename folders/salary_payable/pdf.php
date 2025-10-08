<style>
    body
    {
        font-family:\"Roboto\", sans-serif;
    }
</style>



<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<!-- <link rel="stylesheet" type="text/css" href="jquery-ui-1.8.17.custom.css"> -->
<!-- <link rel="stylesheet" type="text/css" href="main.css"> -->
<script type="text/javascript" src="jquery-1.7.1.min.js"></script>
<script type="text/javascript" src="jquery-ui-1.8.17.custom.min.js"></script>
<script type="text/javascript" src="jspdf.debug.js"></script>

<?php
include '../../config/dbconfig.php';
$get_date = $_REQUEST['entry_date'];
$exp_date = explode('-',$get_date);
$year     = $exp_date[0];
$mon      = date('F', strtotime($_REQUEST['entry_date']));
$month    = $exp_date[1];
//$subject  = "Payslip for the Month of ".$month;
$subject  = "Payslip for the Month of ".$mon."-".$year;
$count_day= cal_days_in_month(CAL_GREGORIAN,$month,$year);
$body     = "";
//$headers  = "software@ascentedigit.com";
$headers = "";
// To send HTML mail, the Content-type header must be set
$headers .= 'MIME-Version: 1.0' . "\r\n";
$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

global $pdo;
// message

    $employee_id   = $_REQUEST['employee_id'];
    $unique_id     = $_REQUEST['unique_id'];
    $table_name    = "staff";
    $where         = [];
    $table_columns = [
        "office_email_id AS to_mail",
        "staff_name",
        "employee_id",
        "date_of_join",
        "work_location",
        "department",
        "(select designation from designation_creation where designation_creation.unique_id = staff.designation_unique_id) as designation",
        "pf_no",
        "esi_no",
        "(select account_no from staff_account_details where staff_account_details.staff_unique_id = staff.unique_id and bank_status = 'Primary') as acc_no",
        "(select bank_name from staff_account_details where staff_account_details.staff_unique_id = staff.unique_id and bank_status = 'Primary') as bank_name",
        "(select total_days from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as total_days",
        "(select lop from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as lop",
        "(select salary_days from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as salary_days",
        "(select reimbrusment from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as reimbrusment",
        "(select tds from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as tds",
        "(select esi from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as esi",
        "(select pf from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as pf",
        "(select salary from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as salary",
       // "(select fixed_advance from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as fixed_advance",
        "'' as fixed_advance",
        "(select other_deduction from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as other_deduction",
        "(select gross_salary from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as gross_salary",
        "(select total_deduction from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as total_deduction",
        "(select take_home from salary_generation_sub where salary_generation_sub.salary_unique_id = '".$unique_id." ' and employee_id = '".$employee_id."') as take_home",

    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'employee_id = "'.$employee_id.'" and `is_delete` = 0';
    

    $staff_count = $pdo->select($table_details, $where);

    if (!($staff_count->status)) {

        print_r($staff_count);

    } else {

        $staff_count  = $staff_count->data[0];

        $to_email     = $staff_count['to_mail'];
        $basic        = ($staff_count['salary'] * 40)/100;
        $hra          = ($basic * 50) / 100;
        $conveyance_default_value = 5000;
        $medical_default_value    = 8000;
        $educational_default_value= 900;
        $pf_default_value         = 15000;
        $esi_default_value        = 21000;
       
        if ($staff_count['salary'] >= $conveyance_default_value) {
            $conveyance = 1600;
        } else {
            $conveyance = 0;
        }

        //medical allowance
        if ($staff_count['salary'] >= $medical_default_value) {
            $medical_allowance = 1250;
        } else {
            $medical_allowance = 0;
        }
        //Education allowance
        if ($staff_count['salary'] >= $educational_default_value) {
            $educational_allowance = 200;
        } else {
            $educational_allowance = 0;
        }
        //pf
        if ($basic <= $pf_default_value) {
            $pf = (($basic * 12) / 100);
        } else {
            $pf = 0;
        }
        //esi
        if ($staff_count['salary'] <= $esi_default_value) {
            $esi = (($staff_count['salary'] * 0.75) / 100);
        } else {
            $esi = 0;
        }

        $sum_allowance = $basic + $hra + $conveyance + $medical_allowance + $educational_allowance;
        $other_allowance = $staff_count['salary'] - $sum_allowance;

        $body .= "<div style =  'padding: 30px 0px;' id ='container' class='container' >";
        $body .= "<div style = 'padding: 0px 10px 0px;' class='row header'>";
        $body .= "<div class='col-md-12'>";
        $body .= "<table width='100%' style = 'text-align: center;'>";
        $body .= "<tr>";
        $body .= "<td width='9%'><img style = 'width: 100%;' src='https://103.130.89.95/aed_erp/img/logo-new1.png'></td>";
        $body .= "<td><h4 style = 'font-size: 22px; color: #000;margin: 0px;'>Ascent e-Digit Solutions (P) Ltd.,</h4>
                    <p style = ' color: #7a7777; font-weight: 400;font-size: 17px;margin-bottom: 0px;
    margin-top: 5px;'><i style = 'padding-right: 7px;' aria-hidden='true'></i> B.O: No. 64, Kalaimagal School Road, Erode 638001.</p>
                    <p style = ' color: #7a7777; font-weight: 400;font-size: 17px;margin-bottom: 4px;margin-top: 0px;'><i style = 'padding-right: 7px;' aria-hidden='true'></i> H.O: No. 15 Masthan Ali Garden Street, Off. S V Lingam Road, Vadapalani, Chennai - 600 026.</p></td>";
        $body .= "</tr>";
        $body .= "</table>";
        $body .= "</div>";
        $body .= "</div>";

        $body .= "<table width='100%' style='background: #648f29;padding:2px 7px;margin: 26px 0px 15px; -webkit-print-color-adjust: exact;'>";
        $body .= "<tr>";
        $body .= "<td style='font-size:17px;color:#ffffff;margin:0px;font-family:\"Roboto\", sans-serif;'>Employee pay summary</td>";
        $body .= "<td style='margin:0px;text-align:end;text-transform:uppercase;color: #fff;font-size: 17px;font-family:\"Roboto\", sans-serif;'>PAY SLIP FOR THE MONTH OF ".$mon."-".$year."</td>";
        $body .= "</tr>";
        $body .= "</table>";
     


                $body .= "<table width='100%'>";
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Employee No</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['employee_id']."</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Employee Name</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['staff_name']."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Date of Joining</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".disdate($staff_count['date_of_join'])."</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Location</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['work_location']."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Department</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['department']."</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Designation</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['designation']."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>PF UAN No</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['pf_no']."</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>ESI IP NO</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['esi_no']."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Mode of Transfer</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>Bank</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Account No</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['acc_no']."</td>";
                $body .= "</tr>";
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Bank Name</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['bank_name']."</td>";
                $body .= "</tr>";
                $body .= "</table>";
                

                $body .= "<table width='100%'>"; 
                $body .= "<tr>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Monthly days</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['total_days']."</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Loss Of Pay</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['lop']."</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #555 !important;'>Salary Days</td>";
                $body .= "<td>:</td>";
                $body .= "<td style='font-size: 17px;padding: 5px;font-weight: 500;color: #000!important;'>".$staff_count['salary_days']."</td>";
                $body .= "</tr>";
                $body .= "</table>";







        $body .= "<div class='row'>";   
        $body .= "<div class='col-md-12'>";
        $body .= "<table style = 'margin: 30px 0px;border-collapse: collapse;' class='earn' width='100%' >";
        $body .= "<tr style = 'background: #648f29;-webkit-print-color-adjust: exact; ' class='table-bg'>";
        $body .= "<th style = 'color: #fff !important; font-size: 17px;padding: 7px 5px; font-weight: 600;text-transform: uppercase;text-align: inherit;' width='20%'>EARNINGS</th>";
        $body .= "<th style = 'color: #fff !important; font-size: 17px;padding: 7px 5px; font-weight: 500;text-transform: uppercase;text-align: end; font-weight: 600 !important;font-size: 17px;' class='amt' width='20%'>AMOUNT</th>";
        $body .= "<th style = 'color: #fff !important; font-size: 17px;padding: 7px 5px; font-weight: 500;text-transform: uppercase;' width='20%'></th>";
        $body .= "<th style = 'color: #fff !important; font-size: 17px;padding: 7px 5px; font-weight: 600;text-transform: uppercase;text-align: inherit;' width='20%'>DEDUCTIONS</th>";
        $body .= "<th style = 'color: #fff !important; font-size: 17px;padding: 7px 5px; font-weight: 500;text-transform: uppercase;text-align: end; font-weight: 600 !important;' class='amt' width='20%'>AMOUNT</th>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Basic</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$basic."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>PF</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$staff_count['pf']."</td>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>HRA</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$hra."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>ESI</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$staff_count['esi']."</td>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;' >Conveyance</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$conveyance."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Prof Tax</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border-collapse: collapse;font-size: 17px;' class='amt'>0.00</td>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Medical Allowance</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$medical_allowance."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>TDS</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$staff_count['tds']."</td>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Education Allowance</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$educational_allowance."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Salary Advance</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>0.00</td>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Other Allowance</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$other_allowance."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Fixed Advance</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$staff_count['fixed_advance']."</td>";
        $body .= "</tr>";
        $body .= "<tr>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Reimbursement</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$staff_count['reimbrusment']."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='color: #464646;font-size: 17px;padding: 5px;font-weight: 500;border: 1px solid #f3f3f3;border-collapse: collapse;'>Other Deductions</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;border: 1px solid #f3f3f3;font-size: 17px;border-collapse: collapse;' class='amt'>".$staff_count['other_deduction']."</td>";
        $body .= "</tr>";
        $body .= "<tr style = ' font-size: 18px !important;color: #000 !important;border-top: 2px solid #4f7619 !important; border-bottom: 2px solid #4f7619 !important;-webkit-print-color-adjust: exact;border-bottom-style: solid; border-top-style: solid;padding: 7px 5px !important;'  class='total'>";
        $body .= "<td style='font-size: 18px !important;padding: 3px 5px;font-weight: 500;'>Gross</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;' class='amt'>".$staff_count['gross_salary']."</td>";
        $body .= "<td style='border: 1px solid #f3f3f3;'></td>";
        $body .= "<td style='font-size: 18px !important;padding: 3px 5px;font-weight: 500;'>Deductions</td>";
        $body .= "<td style = 'text-align: end;color: #000 !important;font-weight: 600 !important;' class='amt'>".$staff_count['total_deduction']."</td>";
        $body .= "</tr>";
        $body .= "</table>";
        $body .= "</div>";
        $body .= "</div>";

        $body .= "<div class='row'>";
        $body .= "<div class='col-md-12 '>";
        $body .= "<div style = 'text-align: center;background: #cfeba8;padding: 10px 0px;-webkit-print-color-adjust: exact;margin-top: 20px;' width = '75%' class='net-salary'>";
        $body .= "<h4 style = 'margin: 0px;color: #000;font-weight: 500;font-family: \"Roboto\", sans-serif;font-size: 21px;'>Total Net Payable ₹".$staff_count['take_home']." <span style = 'font-size: 15px;text-transform: uppercase;'> (".  getIndianCurrency($staff_count['take_home'])." Only)</span></h4>";
        $body .= "</div>";
        $body .= "</div>";
        $body .= "</div>";

        $body .= "</div>";

        echo $body;
    //     if(mail($to_email, $subject, $body, $headers)){
    //         echo "Email sent successfully";
    //     }else{
    //         echo "Sorry, failed while sending mail!";
    //     }
    }
?>

<script>
$(document).ready(function() {
	// var cust = "<?php echo $_REQUEST['employee_id']; ?>";
    // var pdf = new jsPDF();
    // // var source = $('#container');
    // //  var pdf = new jsPDF('p', 'pt', 'a4');
    // var options = {background:"white"};
    // // var pdf = new jsPDF('p', 'pt', 'a4');
    // pdf.addHTML($("#container"), options, function() {
    // // pdf.addHTML(source);
    // pdf.save(cust+'.pdf');
            
window.print();
 
    //     });
    });
</script>