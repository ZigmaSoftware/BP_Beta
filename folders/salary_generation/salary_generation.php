<style>

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

</style>
<?php 
$table_main                     = "salary_generation_sub";
error_reporting(0);
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                 <div class="clearfix" class="col-md-12 ">
                    <div class="col-md-8 float-left row ">
                        <div class="auth-logo">
                            <div class="logo logo-dark mt-3">
                                <span class="">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="90">
                                </span>
                            </div>
        
                            <div class="logo logo-light">
                                <span class="logo-lg">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="22">
                                </span>
                            </div>
                           
                        </div>&nbsp;&nbsp;
                        <div class="mt-2 float-right pl-3">

                            <h2 class=""> <?php echo $_SESSION['sess_company_name'] ; ?></h2>
                            <h5 class=""> <?php echo $_SESSION['sess_company_address'] ; ?></h5>
                            <h5 class=""> <?php echo $_SESSION['sess_company_district'] ; ?></h5>
                            <h5 class=""> <?php echo $_SESSION['sess_company_state'] ; ?></h5>
                        </div>
                            
                    </div><!-- end col -->
                    <?php
                    $unique_id  = $_GET["unique_id"];
                        $table_date      = [
                            $table_main,
                                [
                                    "salary_date,created"
                                ]
                        ];
                        $select_where_date           = ' salary_unique_id = "'.$unique_id.'"';
                        $action_obj_date             = $pdo->select($table_date,$select_where_date);
                        $data_val_date               = $action_obj_date->data;

                    ?>
                    
                    <div class="col-md-4 float-right row" >
                        <div class="auth-logo">
                            <div class="logo logo-dark mt-3">
                                <h5 class="">Salary Month : <?php echo date('M-Y',strtotime($data_val_date[0]['salary_date'])) ; ?></h5>
                                <h5 class="">Entry Date &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: <?php echo date('d-M-Y',strtotime($data_val_date[0]['created'])) ; ?></h5>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                                    if(isset($_GET["unique_id"])) {
                                        if (!empty($_GET["unique_id"])) {

                                            $start =0;

                                            $unique_id  = $_GET["unique_id"];
                                            $s_no = 0;
                                            $where_list = "salary_unique_id = '".$unique_id."' and is_active = '1' and is_delete = '0' group by department";

                                            $columns_list    = [
                                                "@a:=@a+1 s_no",
                                                "count(staff_name) as man_power",
                                                "department",
                                                "sum(take_home) as take_home",
                                                "sum(gross_salary) as gross_salary",
                                                "sum(tds) as tds",
                                                "sum(pf) as pf",
                                                "sum(esi) as esi",
                                                "sum(loan) as loan",
                                                "sum(advance) as advance",
                                                "sum(insurance) as insurance",
                                                "sum(other_deduction) as other_deduction",
                                                "sum(total_deduction) as total_deduction",
                                                "sum(net_salary) as net_salary",
                                                "sum(reimbrusment) as reimbrusment"                                                
                                            ];

                                            $table_details_list  = [
                                                $table_main,
                                                $columns_list
                                            ];

                                            $order_by       = "";


                                            $sql_function   = "SQL_CALC_FOUND_ROWS";

                                            $result         = $pdo->select($table_details_list,$where_list,"",$start,$order_by,$sql_function);
                                    
                                            $total_records  = total_records();
                                            // echo $result->sql;
                                            if ($result->status) {

                                                $res_array       = $result->data;

                                                $table_data      = "";
                                                $total_value     = 0;


                                                foreach ($res_array as $key => $value) {

                                                    $department      = explode(' ',$value['department']);
                                                    if($department[1])
                                                    {
                                                        $department  = $department[0].'-'.$department[1].'-'.$department[2];
                                                    }
                                                    else
                                                    {
                                                        $department  = $department[0];
                                                    }
                                                    
                                                    $table_details_axis      = [
                                                        $table_main,
                                                            [
                                                                "sum(take_home) AS axis_bank,COUNT(staff_name) AS NEFT,COUNT(staff_name) AS Cheque,COUNT(staff_name) AS Cash,COUNT(staff_name) AS Hold"
                                                            ]
                                                    ];
                                                    $select_where_axis           = ' salary_unique_id = "'.$unique_id.'" and department="'.$value['department'].'" and salary_type="Axis Bank"';
                                                    $action_obj_axis             = $pdo->select($table_details_axis,$select_where_axis);
                                                    $data_val_axis               = $action_obj_axis->data;

                                                    $table_details_neft      = [
                                                        $table_main,
                                                            [
                                                                "sum(take_home) AS NEFT"
                                                            ]
                                                    ];
                                                    $select_where_neft           = ' salary_unique_id = "'.$unique_id.'" and department="'.$value['department'].'" and salary_type="NEFT"';
                                                    $action_obj_neft             = $pdo->select($table_details_neft,$select_where_neft);
                                                    $data_val_neft               = $action_obj_neft->data;

                                                    $table_details_cheque      = [
                                                        $table_main,
                                                            [
                                                                "sum(take_home) AS Cheque"
                                                            ]
                                                    ];
                                                    $select_where_cheque           = ' salary_unique_id = "'.$unique_id.'" and department="'.$value['department'].'" and salary_type="Cheque"';
                                                    $action_obj_cheque             = $pdo->select($table_details_cheque,$select_where_cheque);
                                                    $data_val_cheque               = $action_obj_cheque->data;   

                                                    $table_details_cash      = [
                                                        $table_main,
                                                            [
                                                                "sum(take_home) AS Cash"
                                                            ]
                                                    ];
                                                    $select_where_cash           = ' salary_unique_id = "'.$unique_id.'" and department="'.$value['department'].'" and salary_type="Cash"';
                                                    $action_obj_cash             = $pdo->select($table_details_cash,$select_where_cash);
                                                    $data_val_cash               = $action_obj_cash->data;   

                                                    $table_details_hold      = [
                                                        $table_main,
                                                            [
                                                                "sum(take_home) AS Hold"
                                                            ]
                                                    ];
                                                    $select_where_hold           = ' salary_unique_id = "'.$unique_id.'" and department="'.$value['department'].'" and salary_type="Hold"';
                                                    $action_obj_hold             = $pdo->select($table_details_hold,$select_where_hold);
                                                    $data_val_hold               = $action_obj_hold->data;          

                                                    $table_data .="<tr>";
                                                    $table_data .="<td class=''>".($s_no=$s_no+1)."</td>";
                                                    $table_data .="<td class='' style='color:#1abc9c;font-weight:bold;font-size:14px;' onclick=new_external_window('folders/salary_generation/staff_wise_salary_details.php?department=".$department."&unique_id=".$unique_id."');>".($value['department'])."</td>";
                                                    $table_data .="<td class='text-right'>".($value['man_power'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['gross_salary'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['tds'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['pf'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['esi'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['loan'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['advance'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['insurance'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['other_deduction'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['total_deduction'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['net_salary'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['reimbrusment'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['take_home'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($data_val_axis[0]['axis_bank'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($data_val_neft[0]['NEFT'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($data_val_cheque[0]['Cheque'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($data_val_hold[0]['Hold'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($data_val_cash[0]['Cash'])."</td>";
                                                    $table_data .="<td class='text-right'>".moneyFormatIndia($value['take_home'])."</td>";
                                                    $table_data .="</tr>";

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
                                                    $axis_bank            += $data_val_axis[0]['axis_bank'];
                                                    $NEFT                 += $data_val_neft[0]['NEFT'];
                                                    $Cheque               += $data_val_cheque[0]['Cheque'];
                                                    $Hold                 += $data_val_hold[0]['Hold'];
                                                    $Cash                 += $data_val_cash[0]['Cash'];
                                                    $take_home_total      += $value['take_home'];

                                                }
                                                // $man_pawer   = $value['s_no'];
                                                $table_data .="<tr>";

                                                // $table_data .="<td></td><td></td>";
                                                $table_data .="<td colspan='3' style='font-weight : bold'>Total</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".(moneyFormatIndia($gross_salary))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia($tds)."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($pf))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($esi))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($loan))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($advance))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($insurance))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($other_deduction))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($total_deduction))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($net_salary))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($reimbrusment))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($take_home))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($axis_bank))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($NEFT))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($Cheque))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($Hold))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($Cash))."</td>";
                                                $table_data .="<td style='font-weight : bold' align = 'right'>".moneyFormatIndia(($take_home_total))."</td>";
                                                $table_data .="</tr>";
                                            }
                                        }
                                    }
                                ?>
                <form class="was-validated"  autocomplete="off" > 
                    <table id="staff_salary_report_datatable" class="table table-responsive dt-responsive w-100 nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Particulars</th>
                                <th class='text-right'>Man Power</th>
                                <th class='text-right'>Gross</th>
                                <th class='text-right'>TDS</th>
                                <th class='text-right'>PF</th>
                                <th class='text-right'>ESI</th>
                                <th class='text-right'>Loan</th>
                                <th class='text-right'>Advance</th>
                                <th class='text-right'>Insurance</th>
                                <th class='text-right'>Other Deduction</th>
                                <th class='text-right'>Total Deduction</th>
                                <th class='text-right'>Net Salary</th>
                                <th class='text-right'>Reimbrusment</th>
                                <th class='text-right'>Takehome</th>
                                <th class='text-right'>Axis Bank</th>
                                <th class='text-right'>Neft</th>
                                <th class='text-right'>Cheque</th>
                                <th class='text-right'>Hold</th>
                                <th class='text-right'>Cash</th>
                                <th class='text-right'>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php echo $table_data; ?>
                        </tbody> 
                    </table>   
                </form>
                <div class="mt-4 mb-1 d-print-none">
                    <div class="text-right ">
                        <?php echo btn_cancel($btn_cancel);?>
                        <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                    </div>
                </div>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->

<script type="text/javascript">
    
</script>



