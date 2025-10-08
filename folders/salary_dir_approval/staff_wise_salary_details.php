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
include 'function.php';
    $table  = "salary_generation_sub";

    $department     = explode('-',$_GET['department']);

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
            "staff_name",
            "salary_type",
            "gross_salary",
            "tds",
            "pf",
            "esi",
            "loan",
            "advance",
            "insurance",
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

        $where = " is_active = '1'  and is_delete = 0 AND salary_type = '".$department."' AND salary_unique_id = '".$_GET['unique_id']."'";

        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,"",$start);
        $total_records  = total_records();

        $data = $result->data;

        // print_r($result->sql);

        if ($result->status) {

            $res_array      = $result->data;

            $table_data_animal_details     = "";
          
			$stk_qty = 0;
            foreach ($res_array as $key => $value) {
                $table_data_animal_details .="<tr>";
                $table_data_animal_details .="<td>".$value['s_no']."</td>";
                $table_data_animal_details .="<td>".$value['staff_name']."</td>";
                // $table_data_animal_details .="<td>".$value['salary_type']."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['tds'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['pf'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['esi'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['loan'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['advance'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['insurance'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['other_deduction'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['total_deduction'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['net_salary'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['reimbrusment'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['take_home'])."</td>";
                // $table_data_animal_details .="<td align = 'right'><input type='date' class='form-control' value=".date('Y-m-d')."></td>";
                $table_data_animal_details .="</tr>";
                $gross_salary         += $value['gross_salary'];
                $tds                  += $value['tds'];
                $pf                   += $value['pf'];
                $esi                  += $value['esi'];
                $loan                 += $value['loan'];
                $advance              += $value['advance'];
                $insurance            += $value['insurance'];
                $other_deduction      += $value['other_deduction'];
                $total_deduction      += $value['total_deduction'];
                $net_salary           += $value['net_salary'];
                $reimbrusment         += $value['reimbrusment'];
                $take_home            += $value['take_home'];

            }
                
                $table_data_animal_details .="<tr>";

                $table_data_animal_details .="<td></td>";
                $table_data_animal_details .="<td style='font-weight : bold'>Total</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
                $table_data_animal_details .="<td  style='font-weight : bold' align = 'right'>".moneyFormatIndia($tds)."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($pf))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($esi))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($loan))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($advance))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($insurance))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($other_deduction))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($total_deduction))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($net_salary))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($reimbrusment))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($take_home))."</td>";
                $table_data_animal_details .="</tr>";

}


?>
<body>


<table id="example" class="table table-bordered dt-responsive nowrap" cellspacing="0" width="100%" align="center">
    <tr>
        <td height="117" align="center" class="style7"><span style="font-size:20px;">
            <strong>STAFF SALARY DETAILS</strong></span><br></td>
        <td height="117" valign="top" align="right" class="style7"><b></b> &nbsp;</td>
    </tr>
</table>

<table width="100%" border="0" class="botom_pad" align="center" >
    <tr>
        <td width="10%"></td>
        <td width="30%">Date :&nbsp;<b><?php echo date('d-m-Y'); ?><b></td>
        <td width="20%">Salary Type :&nbsp;<b><?php echo $department; ?><b></td>
        <td width="20%" align="center">Month : <b><?php echo date("M-Y",strtotime($data[0]['salary_date'])); ?></b></td>
        <td width="10%"></td>
    </tr>  
</table>

<table width="100%" border="1" class="table table-striped" align="center" style="border-collapse:collapse;border:1px solid #ccc;">
    <thead>
        <tr>
            <th width="1%">S.No</th>
            <th width="15%">Staff Name</th>
            <!-- <th width="7%">Salary Type</th> -->
            <th width="7%">Gross</th>
            <th width="7%">TDS</th>
            <th width="7%">PF</th>
            <th width="7%">ESI</th>
            <th width="7%">Loan</th>
            <th width="7%">Advance</th>
            <th width="7%">Insurance</th>
            <th width="7%">Others</th>
            <th width="7%">Total Deduction</th>
            <th width="7%">Net Salary</th>
            <th width="7%">Reimbrusment</th>
            <th width="7%">Take Home</th>
            <!-- <th width="7%">Pay Date</th> -->
        </tr>
    </thead>
    <tbody>
		<?php echo $table_data_animal_details; ?>
    </tbody>
</table>
