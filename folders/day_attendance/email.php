<?php
include '../../config/dbconfig.php';
$subject  = "Remainder for Check OUT!!!";
$body     = " \n This is Remainder Mail for your Check Out in Ascent CRM.\n\n\n\nRegards,\nHR, Ascent";
$headers  = "kiranerode2003@gmail.com";
// $headers = "";

global $pdo;

    $entry_date    = $_POST['entry_date'];
    $table_name    = "view_staff_check_in_out";
    $where         = [];
    $table_columns = [
        "(select office_email_id from staff where staff.unique_id = view_staff_check_in_out.staff_id)  AS to_mail",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    $where  = 'entry_date = "'.$entry_date.'" and `check_out_time` IS NULL';
    

    $staff_count = $pdo->select($table_details, $where);

    if (!($staff_count->status)) {

        print_r($staff_count);

    } else {

        $staff_count  = $staff_count->data;
        foreach ($staff_count as $key => $value) {

            $to_email     = $value['to_mail'];
            //print_r($to_email);
        
            if(mail($to_email, $subject, $body, $headers)){
                echo "Email sent successfully";
            }else{
                echo "Sorry, failed while sending mail!";
            }
        }
    }
?>