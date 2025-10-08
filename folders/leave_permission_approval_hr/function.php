<?php
function sunday_holiday_date($month,$year,$staff_id)
{
    $sunday= "";
    global $pdo;
    $date     = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    for($i=1;$i<=$date;$i++){
        $entry_date = $year."-".$month."-".$i;

        if(date('N',strtotime($entry_date))==7){
            $sunday = $i."-".$month."-".$year;

            $table_name    = "view_staff_attendance_report";
            $where         = [];
            $table_columns = [
                "entry_date",
            ];

            $table_details = [
                $table_name,
                $table_columns
            ];

            $where  = "entry_date = '".$entry_date."' and staff_id = '".$staff_id."' and check_in_time != '' and check_out_time != '' and  entry_date NOT IN (select comp_off_date from leave_details_sub where staff_id = '".$staff_id."')" ;

            $present = $pdo->select($table_details, $where);

            if (!($present->status)) {

                print_r($present);

            } else {
                
                if(!empty($present->data[0])) {
                    echo $present_sts    =  "<span class='text-green font-weight-bold' style = 'font-size : 18px'>".disdate($present->data[0]['entry_date'])." - Sunday - Present"."</span><br>";
                }else{
                   echo  $present_sts    = "";
                }
            }
        }
    }
}

function get_holiday_date($month,$year,$staff_id){
    global $pdo;
    $date     = cal_days_in_month(CAL_GREGORIAN,$month,$year);
    for($i=1;$i<=$date;$i++){
        $entry_date = $year."-".$month."-".$i;
        $table_name    = "attendance_holidays";
        $where         = [];
        $table_columns = [
            "holiday_date",
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where  = "holiday_date = '".$entry_date."'";

        $holiday = $pdo->select($table_details, $where);

        if (!($holiday->status)) {

            print_r($holiday);

        } else {
            
            if(!empty($holiday->data[0])) {
                $holiday_sts    = $holiday->data[0]['holiday_date'];

                $table_name    = "view_staff_attendance_report";
                $where         = [];
                $table_columns = [
                    "entry_date",
                ];

                $table_details = [
                    $table_name,
                    $table_columns
                ];

                $where  = "entry_date = '".$holiday_sts."' and staff_id = '".$staff_id."' and check_in_time != '' and check_out_time != '' and  entry_date NOT IN (select comp_off_date from leave_details_sub where staff_id = '".$staff_id."')" ;

                $present = $pdo->select($table_details, $where);

                if (!($present->status)) {

                    print_r($present);

                } else {
                    
                    if(!empty($present->data[0])) {
                        echo $present_sts    = "<span class='text-green font-weight-bold' style = 'font-size : 18px'>".disdate($present->data[0]['entry_date'])." - Holiday - Present"."</span><br>";
                    }else{
                       echo  $present_sts    = "";
                    }
                }
            }else{
               echo  $holiday_sts    = "";
            }
        }
    }
}
?>