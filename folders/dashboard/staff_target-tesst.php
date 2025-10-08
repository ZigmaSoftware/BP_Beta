<?php 
    include 'function.php';
    include '../../config/dbconfig.php';

    if ($_POST['report_type'] == 2) {
        $dates  = year_quarter_month('quarter',$_POST['quarter']);
    } else if ($_POST['report_type'] == 3) {
        $dates  = year_quarter_month('month',$_POST['month']);
    } else {
        $dates  = year_quarter_month();
    }

    $from_date  = $dates['from_date'];
    $to_date    = $dates['to_date'];

    $target_year  = date('Y',strtotime($from_date)).'-'.date('Y-m',strtotime($to_date));