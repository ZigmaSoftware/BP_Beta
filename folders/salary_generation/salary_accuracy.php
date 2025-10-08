<style type="text/css">

.style2{font-weight:normal;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;}
.style3 {
font-weight:bold; text-align:right;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;
}
.style4 {
font-weight:bold; font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 12px;
}
.style5 {
font-family: Verdana, Arial, Helvetica, sans-serif;
font-weight: bold;
font-size: 14px;
}
.style7 {font-weight:bold;font-family:Bookman Old Style;font-size: 12px;}

.style6 {
font-family: Verdana, Arial, Helvetica, sans-serif;
font-weight: bold;
font-size: 16px;
}
.style1{font-weight:normal;font-family: Verdana, Arial, Helvetica, sans-serif;font-size: 10px;}
.style10{ border-top: solid 1px; border-top-color:#BFBFBF;}

</style>

<style>
  body{
font-family:calibri;
}
.top_header
{
border-bottom:2px solid #000;
}
.border_collapse
{
border-collapse:collapse;
}
.cmpny_name
{
font-size:22px;
font-weight:600;
text-transform:uppercase;
}
.text_center
{
text-align:center;
}
.addr
{
text-transform:uppercase;
}
.compny_addre p
{
line-height: 5px;
    font-size: 14px;
}
.to_addre
{
line-height:8px;
}
body{
font-family:calibri;
}
h2.heading
{
text-transform:uppercase;
text-decoration:underline;
font-size:22px;
font-weight:600;
letter-spacing:1px;
}
.text_right
{
text-align:right;
}
.font_upper
{
text-transform:uppercase;
}
.sml_head
{
font-size:15px;
}
.desti_head
{
text-decoration:underline;
font-weight:600;
font-size:17px;
}
.botom_pad tr td
{
padding-bottom:8px;
}
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}
.table-striped td
{
padding:5px;
}
.table-striped th
{
padding:5px;
}
.border_tab td
{
border:1px solid #ccc;
border-collapse:collapse !important;
}
.incharge
{
font-size:22px;
font-weight:bold;
    margin: 5px;
}
.border_tab td
{
padding:5px;
}
.reason
{
    font-size: 16px;
    text-transform: lowercase;
}
.mate_deta p
{
line-height:8px;
}
</style>

<style>
body{
font-family:calibri;
}
h2.heading
{
text-transform:uppercase;
text-decoration:underline;
font-size:22px;
font-weight:600;
letter-spacing:1px;
}
.text_right
{
text-align:right;
}
.font_upper
{
text-transform:uppercase;
}
.sml_head
{
font-size:15px;
}
.desti_head
{
text-decoration:underline;
font-weight:600;
font-size:17px;
}
.botom_pad tr td
{
padding-bottom:8px;
}
.table-striped>tbody>tr:nth-of-type(odd) {
    background-color: #f9f9f9;
}
.table-striped td
{
padding:5px;
}
.table-striped th
{
padding:5px;
}
.border_tab td
{
border:1px solid #ccc;
border-collapse:collapse !important;
}
.incharge
{
font-size:22px;
font-weight:bold;
    margin: 5px;
}
.border_tab td
{
padding:5px;
}
</style>

<?php
error_reporting(0);

// Include DB file and Common Functions
include '../../config/dbconfig.php';
    $table  = "salary_generation_sub";

    $department     = explode('-',$_GET['department']);
    $dep            = "";
    if($department[1])
    {
        $department  = $department[0].' '.$department[1].' '.$department[2];
    }
    else
    {
        $department  = $department[0];
    }
    
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
            "((gross_salary * 40) / 100) as basic_wages",
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

        // print_r($result->sql);

//         if ($result->status) {

//             $res_array      = $result->data;

//             $table_data_animal_details     = "";
          
// 			$stk_qty = 0;
//             foreach ($res_array as $key => $value) {
//                 $hra_wages            = ($value['basic_wages'] * 50) / 100;
//                 $conveyance_wages     = "1600";
//                 $medical_wages        = "1250";
//                 $educational_wages    = "200";
//                 $TFI_total   =   $value['basic_wages'] + $hra_wages + $conveyance_wages + $medical_wages + $educational_wages;
//                 $TFI         =  $value['gross_salary'] - $TFI_total;

//                 $table_data_animal_details .="<tr>";
//                 $table_data_animal_details .="<td>".$value['s_no']."</td>";
//                 $table_data_animal_details .="<td>".$value['location']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['employee_id']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".date('d/m/Y',strtotime($value['date_of_join']))."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['staff_name']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['designation_type']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['department']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['total_days']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['lop']."</td>";
//                 $table_data_animal_details .="<td style='width:40px;'>".$value['salary_days']."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['basic_wages'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($hra_wages)."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($conveyance_wages)."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($medical_wages)."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($educational_wages)."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($TFI)."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['tds'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['pf'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['esi'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['advance'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['other_deduction'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['total_deduction'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['net_salary'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['reimbrusment'])."</td>";
//                 $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['take_home'])."</td>";
//                 $table_data_animal_details .="</tr>";
//                 $gross_salary         += $value['gross_salary'];
//                 $tfi                  += $TFI;
//                 $basic_wages          += $value['basic_wages'];
//                 $hra                  += $hra_wages;
//                 $conveyance           += $conveyance_wages;
//                 $medical_allowance    += $medical_wages;
//                 $education_allowance  += $educational_wages;
//                 $advance              += $value['advance'];
//                 $tds                  += $value['tds'];
//                 $pf                   += $value['pf'];
//                 $esi                  += $value['esi'];
//                 $other_deduction      += $value['other_deduction'];
//                 $total_deduction      += $value['total_deduction'];
//                 $net_salary           += $value['net_salary'];
//                 $reimbrusment         += $value['reimbrusment'];
//                 $take_home            += $value['take_home'];

//             }
                
//                 $table_data_animal_details .="<tr>";

//                 // $table_data_animal_details .="<td></td><td></td><td></td><td></td><td></td><td></td><td></td>";
//                 $table_data_animal_details .="<td colspan='10' style='font-weight : bold'>Total</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($basic_wages))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($hra))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($conveyance))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($medical_allowance))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($education_allowance))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($tfi))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
//                 $table_data_animal_details .="<td  style='font-weight : bold' align = 'right'>".moneyFormatIndia($tds)."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($pf))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($esi))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($advance))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($other_deduction))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($total_deduction))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($net_salary))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($reimbrusment))."</td>";
//                 $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($take_home))."</td>";
//                 $table_data_animal_details .="</tr>";

// }


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
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <table id="staff_salary_report_datatable" class="table dt-responsive nowrap w-100">
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
                        <?php //echo $table_data_animal_details; ?>
                    </tbody>
                </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>


<script type="text/javascript">
$(document).ready(function () {    
    var unique_id   = "<?php echo $_GET['unique_id']; ?>";
    var table_id    = "staff_salary_report_datatable";
    var data 	  = {
        "unique_id" : unique_id,
		"action"	: "datatable_slry_accuracy",
	};    
    var table       = $("#"+table_id);
    var ajax_url    = sessionStorage.getItem("folder_crud_link");
    var datatable   = table.DataTable({
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
        url 	: ajax_url,
		type 	: "POST",
		data 	: data
    },
    dom: 'Blfrtip',
    buttons: [
        'copy',
        'csv',
    ],
    lengthChange: true
    });
});  
</script>