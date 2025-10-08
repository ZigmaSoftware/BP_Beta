<?php
error_reporting(0);

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';
    $table  = "salary_generation_sub";

    // $department     = explode('-',$_GET['department']);

    // if($department[1])
    // {
    //     $department  = $department[0].' '.$department[1].' '.$department[2];
    // }
    // else
    // {
    //     $department  = $department[0];
    // }
    
    $start      = 0;
    $gross_salary         = 0;
    $tds                  = 0;
    $pf                   = 0;
    $esi                  = 0;
    $fixed_advance        = 0;
    $insurance            = 0;
    $other_deduction      = 0;
    $total_deduction      = 0;
    $net_salary           = 0;
    $reimbrusment         = 0;
    $take_home            = 0;
    $tfi_total            = 0;
        $columns    = [
            "@a:=@a+1 s_no",
            "(SELECT work_location as location FROM staff WHERE staff.unique_id = ".$table.".staff_id ) AS location",
            "(SELECT employee_id as employee_id FROM staff WHERE staff.unique_id = ".$table.".staff_id ) AS employee_id",
            "(SELECT date_of_join as date_of_join FROM staff WHERE staff.unique_id = ".$table.".staff_id ) AS date_of_join",
            "staff_name",
            "designation_type",
            "department",
            "total_days",
            "lop",
            "salary_days",
            "gross_salary",
            "(SELECT basic_wages as basic FROM staff WHERE staff.unique_id = ".$table.".staff_id) AS basic",
            "(SELECT hra as hra_pay FROM staff WHERE staff.unique_id = ".$table.".staff_id) AS hra_pay",
            "(SELECT conveyance as conveyance_pay FROM staff WHERE staff.unique_id = ".$table.".staff_id) AS conveyance_pay",
            "(SELECT medical_allowance as medical_pay FROM staff WHERE staff.unique_id = ".$table.".staff_id) AS medical_pay",
            "(SELECT education_allowance as education_pay FROM staff WHERE staff.unique_id = ".$table.".staff_id) AS education_pay",
            "tds",
            "pf",
            "advance",
            "esi",
            "other_deduction",
            "total_deduction",
            "net_salary",
            "reimbrusment",
            "take_home",
            "salary_date"
        ];

       $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];

        $year  = date('Y');
        if($department)
        {
            $dep = " department = '".$department."' AND salary_unique_id = '".$_GET['unique_id']."'";
        }
        else
        {
            $dep = " salary_unique_id = '".$_GET['unique_id']."'";
        }

        $where = " is_active = '1'  and is_delete = 0 AND ".$dep;

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,"",$start);
        $total_records  = total_records();

        $data = $result->data;
        if ($result->status) {

            $res_array      = $result->data;

            $table_data_animal_details     = "";
          
			
            // $TFI              = 0;
            $tfi              = 0;
            $basic            = 0;
            $hra_pay          = 0;
            $conveyance_pay   = 0;
            $medical_pay      = 0;
            $education_pay    = 0;
            foreach ($res_array as $key => $value) {

                $basic            =   $value['basic'];
                $hra_pay          =   $value['hra_pay'];
                $conveyance_pay   =   $value['conveyance_pay'];
                $medical_pay      =   $value['medical_pay'];
                $education_pay    =   $value['education_pay'];

                $tfi_total        =   $basic + $hra_pay;

                // $TFI         =   $value['gross_salary'] - $tfi_total;

                $table_data_animal_details .="<tr>";
                $table_data_animal_details .="<td>".$value['s_no']."</td>";
                $table_data_animal_details .="<td>".$value['location']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['employee_id']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".date('d/m/Y',strtotime($value['date_of_join']))."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['staff_name']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['designation_type']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['department']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['total_days']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['lop']."</td>";
                $table_data_animal_details .="<td style='width:40px;'>".$value['salary_days']."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['basic'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['hra_pay'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['conveyance_pay'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['medical_pay'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['education_pay'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($TFI)."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['tds'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['pf'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['esi'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['advance'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['other_deduction'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['total_deduction'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['net_salary'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['reimbrusment'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['take_home'])."</td>";
                $table_data_animal_details .="</tr>";

                $gross_salary         += $value['gross_salary'];
                $tfi                  += $TFI;
                // $basic_wages          += $value["basic"];
                // $hra                  += $value['hra_pay'];
                // $conveyance           += $value['conveyance_pay'];
                // $medical_allowance    += $value['medical_pay'];
                // $education_allowance  += $value['education_pay'];
                $advance              += $value['advance'];
                $tds                  += $value['tds'];
                $pf                   += $value['pf'];
                $esi                  += $value['esi'];
                $other_deduction      += $value['other_deduction'];
                $total_deduction      += $value['total_deduction'];
                $net_salary           += $value['net_salary'];
                $reimbrusment         += $value['reimbrusment'];
                $take_home            += $value['take_home'];

            }
                
                $table_data_animal_details .="<tr>";

                // $table_data_animal_details .="<td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
                $table_data_animal_details .="<td colspan='10' style='font-weight : bold'>Total</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($basic_wages))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($hra))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($conveyance))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($medical_allowance))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($education_allowance))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($tfi))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
                $table_data_animal_details .="<td  style='font-weight : bold' align = 'right'>".moneyFormatIndia($tds)."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($pf))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($esi))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($advance))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($other_deduction))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($total_deduction))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($net_salary))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($reimbrusment))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($take_home))."</td>";
                $table_data_animal_details .="</tr>";

}


?>
<body>

<table width="100%" border="0" class="botom_pad" align="center" >
    <tr>
        <td width="20%"></td>
        <td width="30%">Date :&nbsp;<b><?php echo date('d-m-Y h:i:s a'); ?><b></td>
        <td width="20%" align="center">Month : <b><?php echo date("M-Y",strtotime($data[0]['salary_date'])); ?></b></td>
        <td width="20%"></td>
    </tr>  
</table>

 <table id="staff_salary_report_datatable" class="table table-responsive dt-responsive w-100 nowrap">
    <thead>
        <tr>
            <th >S.No</th>
            <th>Location</th>
            <th>Emp Code</th>
            <th>DOJ</th>
            <th>Name</th>
            <th>Designation</th>
            <th>Department</th>
            <th>Total Days</th>
            <th>LOP</th>
            <th>Salary Days</th>
            <th>Gross</th>
            <th>Basic</th>
            <th>HRA</th>
            <th>Conveyance</th>
            <th>Medical</th>
            <th>Education</th>
            <th>Term Perf Incentive</th>
            <th>Gross</th>
            <th>ESI GROSS</th>
            <th>TDS</th>
            <th>PF</th>
            <th>ESI</th>
            <th>Advance</th>
            <th>Other Ded</th>
            <th>Total Ded</th>
            <th>Net Salary</th>
            <th>Reimbrusment</th>
            <th>Takehome</th>
        </tr>
    </thead>
    <tbody>
		<?php echo $table_data_animal_details; ?>
    </tbody>
</table>


<!-- <script type="text/javascript">
$(document).ready(function () {    
    var table_id    = "staff_salary_report_datatable";
    var data        = '';
    var table       = $("#"+table_id);
    var datatable = table.DataTable({
    scrollX     : "900px",
    scrollY     : "400px",
    processing  : true,
    serverSide  : true,
    ordering    : true,
    responsive  : false,
    paging      : false,
    info        : true,
    searching   : true,
    "ajax"      : {
        type    : "POST",
        data    : data
    },
    dom: 'Blfrtip',
    buttons: [
        'copy',
        'csv',
    ],
    lengthChange: true
    });
});  
</script> -->