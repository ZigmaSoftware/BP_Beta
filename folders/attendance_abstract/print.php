<style>
    table#atttendance_summary_report_datatable {
    width: 100%;
    padding: 0px 48px;
    text-align: left;
    border-collapse: collapse;
}
table#atttendance_summary_report_datatable tr td {
    padding: 10px 10px;
    border-collapse: collapse;
    border: 1px solid #ccc;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
}
table#atttendance_summary_report_datatable tr th {
    padding: 10px 10px;
    border-collapse: collapse;
    border: 1px solid #ccc;
    font-family: 'Poppins', sans-serif;
    font-size: 13px;
}
table#atttendance_summary_report_datatable tr td span.font-weight-bold {
    color: #000 !important;
}
strong.hedline {
    text-transform: uppercase;
    font-family: 'Poppins', sans-serif;
}

</style>




<?php
   
    $totday = date('Y-m-d');
    if(isset($_POST['year_month'])){
        include '../../config/dbconfig.php';
        include 'function.php';
        error_reporting(0);
        $year_month     = $_POST['year_month'];
        $exp_month_year = explode('-',$_POST['year_month']);
        $month          = $exp_month_year[1];
        $year           = $exp_month_year[0];
        $total_days     = cal_days_in_month(CAL_GREGORIAN,$month,$year);
        $day_count      = $total_days ;
    }else{
        include '../../config/dbconfig.php';
        include 'function.php';
        $month      = date('m');
        $year       = date('Y');
        $year_month = $year."-".$month;
        $total_days = date('d');
        $day_count  = $total_days - 1;
        $totday = date('Y-m-d');
    }

   
    
        
    $start =0;
    $s_no = 0;
    $where_list = "is_delete = 0 and is_active = 1 and (relieve_date = '' or DATE_FORMAT(date_of_join, '%Y-%m') <= '".$year_month."' )";

    $columns_list    = [
        "@a:=@a+1 s_no",
        "employee_id",
        "staff_name",
        "work_location",
        "(SELECT designation_type FROM work_designation AS designation WHERE designation.unique_id = staff.designation_unique_id ) AS designation_type",
        "department",
        "'' as date_month",  
        "unique_id"                                            
    ];

    $table_details_list  = [
        "staff",
        $columns_list
    ];

    $order_by       = " employee_id ";


    $sql_function   = "SQL_CALC_FOUND_ROWS";

    $result         = $pdo->select($table_details_list,$where_list,"",$start,$order_by,$sql_function);

    $total_records  = total_records();
    if ($result->status) {

        $res_array       = $result->data;

        $table_data      = "";
        
        foreach ($res_array as $key => $value) {
            $total_sundays          = total_sundays($month,$year,$year_month);
            
            $full_day_leave_cnt       = get_full_day_leave($month,$year,$value['unique_id']);

                if($full_day_leave_cnt){
                    $full_day_leave = $full_day_leave_cnt;
                }else{
                    $full_day_leave = 0;
                }

            
            $full_day_cl_leave_cnt       = get_full_day_cl_leave($month,$year,$value['unique_id']);

                if($full_day_cl_leave_cnt){
                    $full_day_cl_leave = $full_day_cl_leave_cnt;
                }else{
                    $full_day_cl_leave = 0;
                }
            
            $cl_day_leave_cnt      = get_cl_day_leave($month,$year,$value['unique_id']);

                if($cl_day_leave_cnt){
                    $cl_day_leave = $cl_day_leave_cnt;
                }else{
                    $cl_day_leave = 0;
                }
           
            $absent_count_cnt         = get_absent_count($month,$year,$value['unique_id'],$day_count);

                if($absent_count_cnt){
                    $absent_count = $absent_count_cnt;
                }else{
                    $absent_count = 0;
                }

            $half_day_leave_cnt       = get_half_day_leave($month,$year,$value['unique_id']);

                if($half_day_leave_cnt){
                    $half_day_leave = $half_day_leave_cnt;
                }else{
                    $half_day_leave = 0;
                } 

            $no_of_late_cnt           = get_late_count($month,$year,$value['unique_id']);

                if($no_of_late_cnt){
                    $no_of_late = $no_of_late_cnt;
                }else{
                    $no_of_late = 0;
                }

            $no_of_permission_cnt     = get_permission_count($month,$year,$value['unique_id']);
                
                if($no_of_permission_cnt){
                    $no_of_permission = $no_of_permission_cnt;
                }else{
                    $no_of_permission = 0;
                }

            // calculation for total working days
                if($no_of_late_cnt){
                    if($no_of_late_cnt > 3){
                        $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
                    } else{
                        $no_of_late_tot_cnt = 0;
                    }
                }else {
                    $no_of_late_tot_cnt = 0;
                }
                
            // calculation for per total working days
                if($no_of_permission_cnt){
                    if($no_of_late_tot_cnt > 2){
                        $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2)/2);
                    } else{
                        $no_of_permission_tot_cnt = 0;
                    }
                }else {
                    $no_of_permission_tot_cnt = 0;   
                }

            $table_data .="<tr>";
            $table_data .="<td class=''>".($s_no=$s_no+1)."</td>";
            $table_data .="<td>".$value['employee_id']."</td>";
            $table_data .="<td>".$value['staff_name']."</td>";
            $table_data .="<td>".$value['work_location']."</td>";
            $table_data .="<td>".$value['designation_type']."</td>";
            $table_data .="<td>".$value['department']."</td>";
            for($date = 1; $date <= $total_days; $date++){
                if($date < 10){$date = "0".$date;}  
                $entry_date = $year."-".$month."-".$date;
                $day_status             = get_attendance_type($value['unique_id'],$entry_date);
                $check_holiday          = get_holiday_date($entry_date);
                $check_sunday           = get_sunday_date($entry_date,$date);
                $leave                  = get_leave_status($value['unique_id'],$entry_date);
                
                $total_holidays         = total_holidays($month,$year);

                

                
                switch($day_status){
                    case 1 :
                        $value['date_month']  = "<span class='text-green font-weight-bold'>P</span>";
                        break;
                    case 2 :
                        $value['date_month']  = "<span class='text-warning font-weight-bold'>LP</span>";
                        break;
                    case 3 :
                        $value['date_month']  = "<span class='text-warning font-weight-bold'>PP</span>";
                        break;
                    case 4:
                        $value['date_month']  = "<span class='text-warning font-weight-bold'>1/2 L</span>";
                        break;
                    default :
                        $value['date_month']  = "<span class='text-danger font-weight-bold'>A</span>";
                        break;
                }

                switch($leave){
                    case 1 :
                        $value['date_month']  = "<span class='font-weight-bold' style='color :#099be4'>EL</span>";
                        break;
                    case 2 :
                        $value['date_month']  = "<span class='font-weight-bold' style='color :#099be4'>CL</span>";
                        break;
                    case 3 :
                        $value['date_month']  = "<span class='font-weight-bold' style='color :#099be4'>SL</span>";
                        break;
                    case 4:
                        $value['date_month']  = "<span class='font-weight-bold' style='color :#099be4'>COL</span>";
                        break;
                    case 5:
                        $value['date_month']  = "<span class='font-weight-bold' style='color :#099be4'>SPL</span>";
                        break;
                    case 6:
                        $value['date_month']  = "<span class='font-weight-bold' style='color :#099be4'>LOP</span>";
                        break;
                }

                if($check_sunday){
                    $value['date_month'] = $check_sunday;
                }

                if($check_holiday){
                    $value['date_month'] = "<span class='font-weight-bold' style='color :blue'>H</span>";
                }

                
                $check_in               = get_check_in_time($value['unique_id'],$entry_date);

                if($check_in){
                   // $check_in_time_val       = date_create($check_in);
                    $check_in_time           = $check_in;
                }else{
                    $check_in_time           = "";
                    
                }

                $check_out                    = get_check_out_time($value['unique_id'],$entry_date);
                if($check_out){
                    //$check_out_time_val       = date_create($check_out);
                    $check_out_time           = $check_out;
                }else{
                    $check_out_time           = "";
                }

                if(($check_out_time == '-')||($check_out_time == '')){
                    if($check_sunday){
                        $value['date_month'] = $check_sunday;
                    }
    
                    else if($check_holiday){
                        $value['date_month'] = "<span class='font-weight-bold' style='color :blue'>H</span>";
                    }
                    else if($entry_date != $today){
                       $value['date_month'] = "<span class='text-danger font-weight-bold'>A</span>";
                    }
                }

                if($check_in_time == ''){
                    if($check_sunday){
                        $value['date_month'] = $check_sunday;
                    }
                }

                if($entry_date <= $totday){
                    $table_data .="<td>".$value['date_month']."</td>";
                }else{
                    $table_data .="<td>-</td>";   
                }
            }
            $no_of_leave         = $cl_day_leave + $full_day_cl_leave;
            $ab_cnt              = ($total_days - $absent_count  - $no_of_leave - $total_sundays); //-1 for current_day
            $no_of_absent        = (($full_day_leave + $half_day_leave + $ab_cnt) - $total_sundays);
            // $no_of_absent        = $total_sundays;
            $salary_days         = $total_days - ($no_of_absent + $no_of_leave  + $no_of_permission_tot_cnt + $total_sundays);

            $table_data .="<td>".$total_days."</td>";
            $table_data .="<td>".$full_day_cl_leave + $cl_day_leave."</td>";
            $table_data .="<td>".$no_of_late."</td>";
            $table_data .="<td>".$no_of_permission."</td>";
            $table_data .="<td>".$no_of_absent."</td>";
            $table_data .="<td>".$salary_days."</td>";
            $table_data .="</tr>";
        }
                                 
    }
?>
<table id="example" class="table table-bordered dt-responsive nowrap" cellspacing="0" width="100%" align="center">
    <tr>
        <td height="117" align="center" class="style7"><span style="font-size:20px;">
            <strong class="hedline">Attendance Abstract</strong></span><br></td>
        <td height="117" valign="top" align="right" class="style7"><b></b> &nbsp;</td>
    </tr>
</table>

<table id="atttendance_summary_report_datatable" class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>Emp ID</th>
            <th>Executive Name</th>
            <th>Location</th>
            <th>Designation </th>
            <th>Department</th>
            <?php 
                for($date = 1; $date <= $total_days; $date++){
                    if($date < 10){$date = "0".$date;} ?>
                <th><?=$date;?></th>
            <?php } ?>
            <th>T.Days</th>
            <th>LV</th>
            <th>LP</th>
            <th>PP</th>
            <th>AB</th>
            <th>Sal. Days</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <?php echo $table_data; ?>
        </tr>
    </tbody> 
</table>   