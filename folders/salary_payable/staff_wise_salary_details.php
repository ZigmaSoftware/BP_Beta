<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>

<style type="text/css">

.success {
  /*background-color: #fce4e4;*/
  border: 2px solid green;
  outline: none;
}
.error {
  /*background-color: #fce4e4;*/
  border: 2px solid #cc0033;
  outline: none;
}

.column {
  float: left;
  width: 22%;
  /*padding: 10px;*/
  margin-left: 115px;
 /* height: 300px;*/ /* Should be removed. Only for demonstration */
}

/* Clear floats after the columns */
.row:after {
  content: "";
  display: table;
  clear: both;
}
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

.button-three {
    position: relative;
    background-color: #f39c12;
    border: none;
    padding: 10px;
    width: 80px;
    text-align: center;
    -webkit-transition-duration: 0.4s; /* Safari */
    transition-duration: 0.4s;
    text-decoration: none;
    overflow: hidden;
}

.button-three:hover{
   background:#fff;
   box-shadow:0px 2px 10px 5px #97B1BF;
   color:#000;
}

.button-three:after {
    content: "";
    background: #f1c40f;
    display: block;
    position: absolute;
    padding-top: 300%;
    padding-left: 350%;
    margin-left: -20px !important;
    margin-top: -120%;
    opacity: 0;
    transition: all 0.8s
}

.button-three:active:after {
    padding: 0;
    margin: 0;
    opacity: 1;
    transition: 0s
}

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

[type="date"] {
  background:#fff url(https://cdn1.iconfinder.com/data/icons/cc_mono_icon_set/blacks/16x16/calendar_2.png)  97% 50% no-repeat ;
}
[type="date"]::-webkit-inner-spin-button {
  display: none;
}
[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0;
}

</style>

<?php
error_reporting(0);

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';
    $table  = "salary_generation_sub";
    $table_main =   "salary_generation";
    $table_main_acc =   "staff_account_details";

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
            "staff_id",
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
            "salary_date",
            "salary_pay",
            "pay_date",
            "pay_description",
            "unique_id"
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

                if($value['salary_pay']=='0')
                {
                    $salary_pay =   "<input type='button' value='Pay' class='button-three salary_pay".$value['unique_id']."' onClick=salary_pay('".$value['unique_id']."','".trim($department)."')>";
                }
                else if($value['salary_pay']=='1')
                {
                    $salary_pay =   "<input type='button' value='Paid' disabled class='button-three salary_pay".$value['unique_id']."' onClick=salary_pay('".$value['unique_id']."')>";
                }

                $table_acc      = [
                $table_main_acc,
                    [
                        "bank_name,account_no"
                    ]
            ];
            $select_where_acc  = ' staff_unique_id = "'.$value['staff_id'].'"';
            $action_obj_acc    = $pdo->select($table_acc,$select_where_acc);
            $data_val_acc      = $action_obj_acc->data;

                $table_data_animal_details .="<tr>";
                $table_data_animal_details .="<td>".$value['s_no']."</td>";
                $table_data_animal_details .="<td>".$value['staff_name']."</td>";
                $table_data_animal_details .="<td>".$data_val_acc[0]['bank_name']." - ".$data_val_acc[0]['account_no']."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['gross_salary'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['tds'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['pf'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['esi'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['loan'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['advance'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['insurance'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['other_deduction'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['total_deduction'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['net_salary'])."</td>";
                // $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['reimbrusment'])."</td>";
                $table_data_animal_details .="<td align = 'right'>".moneyFormatIndia($value['take_home'])."</td>";
                 if($_GET['department']!='Axis-Bank-') { $table_data_animal_details .="<td align = 'left' style='text-align: left'><input type='date' id='pay_date".$value['unique_id']."' name='pay_date".$value['unique_id']."' class='form-control' value=".$value['pay_date']."></td>";
                $table_data_animal_details .="<td align = 'left' style='text-align: left'><textarea id='pay_description".$value['unique_id']."' name='pay_description".$value['unique_id']."'>".$value['pay_description']."</textarea></td>"; 
                $table_data_animal_details .="<td align = 'left' style='text-align: left'>".$salary_pay."</td>";}
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

                $table_data_animal_details .="<td></td><td></td>";
                $table_data_animal_details .="<td style='font-weight : bold'>Total</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
                // $table_data_animal_details .="<td  style='font-weight : bold' align = 'right'>".moneyFormatIndia($tds)."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($pf))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($esi))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($loan))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($advance))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($insurance))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($other_deduction))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($total_deduction))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($net_salary))."</td>";
                // $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($reimbrusment))."</td>";
                $table_data_animal_details .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($take_home))."</td>";
                $table_data_animal_details .="</tr>";

}

$table_date      = [
    $table_main,
        [
            "hr_approve_by,hr_approve_date,ceo_approve_by,ceo_reason,ceo_approve_date,dir_approve_by,dir_reason,dir_approve_date"
        ]
];
$select_where_date  = ' unique_id = "'.$_GET['unique_id'].'"';
$action_obj_date    = $pdo->select($table_date,$select_where_date);
$data_val_date      = $action_obj_date->data;


$hr_name  = staff_name($data_val_date[0]['hr_approve_by']);
$ceo_name = staff_name($data_val_date[0]['ceo_approve_by']);
$dir_name = staff_name($data_val_date[0]['dir_approve_by']);

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
        <input type="hidden" name="salary_date" id="salary_date" value="<?php echo date('Y-m',strtotime($data[0]['salary_date'])); ?>">
        <td width="10%"></td>
    </tr>  
</table>
<div class="row" style="margin-top: -10px;">
        <div class="column" style="margin-left: 150px;margin-right: 30px;">
            <h3 style="border-bottom: 1px solid black;">HR Generation</h3>
            <p style="margin-left:10px;margin-top: -10px;">Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo date('d-m-Y h:i A',strtotime($data_val_date[0]['hr_approve_date'])); ?></b></p>
            <p style="margin-left:10px;margin-top: -10px;">Approved By : <b><?php echo $hr_name[0]['staff_name']; ?></b></p>
            <p style="margin-left:10px;margin-top: -10px;">Description &nbsp; : <b><?php echo $data_val_date[0]['hr_reason']; ?></b></p>

            <?php if($_GET['department']=='Axis-Bank-') { ?><p style="margin-left:10px;margin-top: 20px;font-weight: bold;border-top: 1px solid black;">Pay Date : <br><input type='date' id='pay_date' name='pay_date' class='form-control' value="<?php echo $value['pay_date'] ?>"></p><?php } ?>
        </div>
        <div class="column">
            <h3 style="border-bottom: 1px solid black;">CEO Approval</h3>
            <p style="margin-left:10px;margin-top: -10px;">Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo date('d-m-Y h:i A',strtotime($data_val_date[0]['ceo_approve_date'])); ?></b></p>
            <p style="margin-left:10px;margin-top: -10px;">Approved By : <b><?php echo $ceo_name[0]['staff_name']; ?></b></p>
            <p style="margin-left:10px;margin-top: -10px;">Description &nbsp; : <b><?php echo $data_val_date[0]['ceo_reason']; ?></b></p>

            <?php if($_GET['department']=='Axis-Bank-') { ?><p style="margin-left:10px;margin-top: 20px;font-weight: bold;border-top: 1px solid black;">Pay Description : <br><textarea id='pay_description' name='pay_description'  rows="4" cols="35"><?php echo $value['pay_description']; ?></textarea></p><?php } ?>
        </div>
        <div class="column" style="margin-left: 90px;">
            <h3 style="border-bottom: 1px solid black;">Directors Approval</h3>
            <p style="margin-left:10px;margin-top: -10px;">Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <b><?php echo date('d-m-Y h:i A',strtotime($data_val_date[0]['dir_approve_date'])); ?></b></p>
            <p style="margin-left:10px;margin-top: -10px;">Approved By : <b><?php echo $dir_name[0]['staff_name']; ?></b></p>
            <p style="margin-left:10px;margin-top: -10px;">Description &nbsp; : <b><?php echo $data_val_date[0]['dir_reason']; ?></b></p>

            <?php if($_GET['department']=='Axis-Bank-') { ?><p style="margin-left:10px;margin-top: 20px;font-weight: bold;border-top: 1px solid black;">Action : <br><input type='button' value='Pay' class='button-three salary_pay<?php echo $_GET['unique_id'] ?>' onClick="salary_pay('<?php echo $_GET['unique_id'] ?>','<?php echo trim($department); ?>');"></p><?php } ?>
        </div>
</div>
<table width="100%" border="1" class="table table-striped" align="center" style="border-collapse:collapse;border:1px solid #ccc;">
    <thead>
        <tr>
            <th width="1%">S.No</th>
            <th width="24%" style="text-align: left">Staff Name</th>
            <th width="20%" style="text-align: left">Bank - Account NO</th>
            <th width="5%" style="text-align: right">Gross</th>
            <!-- <th width="5%">TDS</th>
            <th width="5%">PF</th>
            <th width="5%">ESI</th>
            <th width="5%">Loan</th>
            <th width="5%">Advance</th>
            <th width="5%">Insurance</th>
            <th width="5%">Others</th>
            <th width="5%">Total Deduction</th>
            <th width="5%">Net Salary</th>
            <th width="5%">Reimbrusment</th> -->
            <th width="5%" style="text-align: right">Take Home</th>
            <?php if($_GET['department']!='Axis-Bank-') { ?><th width="5%" style="text-align: left">Pay Date</th>
            <th width="5%" style="text-align: left">Pay Description</th> 
            <th width="5%" style="text-align: left">Action</th><?php } ?>
        </tr>
    </thead>
    <tbody>
		<?php echo $table_data_animal_details; ?>
    </tbody>
</table>


<script type="text/javascript">
function salary_pay(id,department)
{
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    var salary_date =   $('#salary_date').val();
if(department=='Axis Bank')
{
    var description =   $('#pay_description').val();
    var date        =   $('#pay_date').val();
    if((date)&&(description))
    {
        var data = {
                "description"      : description,
                "date"             : date,
                "unique_id"        : id,
                "department"       : department,
                "salary_date"      : salary_date,
                "action"           : 'salary_pay',
            };

            $.ajax({
                type    : "POST",
                url     : "crud.php",
                data    : data,
                success : function (data) 
                {
                    var obj     = JSON.parse(data);
                    var msg     = obj.msg;
                    if(msg=='status_update')
                    {
                        $('.salary_pay'+id).prop('disabled', true);
                        $('.salary_pay'+id).val('Paid');
                    }
                }
            });
    }
    else
    {
        if(date==='') {$("#pay_date").addClass('error'); $("#pay_date").focus(); return false;}
        else {$("#pay_date").addClass('success');}
        if(description==='') {$("#pay_description").addClass('error'); $("#pay_description").focus(); return false;}
        else {$("#pay_description").addClass('success');}
    }
}
else
{
    var description =   $('#pay_description'+id).val();
    var date        =   $('#pay_date'+id).val();
    
    if((date)&&(description))
    {
        var data = {
                "description"      : description,
                "date"             : date,
                "unique_id"        : id,
                "department"       : department,
                "salary_date"      : salary_date,
                "action"           : 'salary_pay',
            };

            $.ajax({
                type    : "POST",
                url     : "crud.php",
                data    : data,
                success : function (data) 
                {
                    var obj     = JSON.parse(data);
                    var msg     = obj.msg;
                    if(msg=='status_update')
                    {
                        $('.salary_pay'+id).prop('disabled', true);
                        $('.salary_pay'+id).val('Paid');
                    }
                }
            });
    }
    else
    {
        if(date==='') {$("#pay_date"+id).addClass('error'); $("#pay_date"+id).focus(); return false;}
        else {$("#pay_date"+id).addClass('success');}
        if(description==='') {$("#pay_description"+id).addClass('error'); $("#pay_description"+id).focus(); return false;}
        else {$("#pay_description"+id).addClass('success');}
    }
}

}
</script>