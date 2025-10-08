<?php

include 'function.php';
include '../../config/dbconfig.php';


if ($_POST['type'] == 2) {
    $dates  = year_quarter_month('quarter',$_POST['quarter']);
} else if ($_POST['type'] == 3) {
    $dates  = year_quarter_month('month',$_POST['month']);
} else {
    $dates  = year_quarter_month();
}

$from_date  = $dates['from_date'];
$to_date    = $dates['to_date'];

$from_date_bf  = date('Y-m',strtotime($from_date));
$to_date_bf    = date('Y-m',strtotime($to_date));

$stage = $_POST['stage'];


    if($_SESSION['sess_user_type'] == $admin_user_type) {
        $team_heads         = team_heads();
    } else {
        $team_heads         = team_heads($_SESSION['user_id']);
    }

    // Heads Loop Start
    foreach ($team_heads as $head_key => $head_value) {

        $team_head_staff_id     = $head_value["staff_unique_id"];
        $team_head_staff_name   = disname($head_value["staff_name"]);
        $team_head_user_id      = $head_value["unique_id"];
        $team_id                = $head_value["team_id"];
        $team_members           = $head_value["team_members"];

        $team_members_arr       = team_members($team_members.",".$team_head_user_id);
        
        $team_members           = explode(",",$team_members);
        $team_members           = "'".implode("','",$team_members)."','".$team_head_user_id."'";

        // This was used to member Loop

        // GET Team Members Staff_ids

        $column_user       = [
            "staff_unique_id"
        ];

        $table_user_details = [
            "user",
            $column_user
        ];

        $table_user_where   = " unique_id IN (".$team_members.")";
        
        $table_user_result  = $pdo->select($table_user_details,$table_user_where);

        if ($table_user_result->status) {
            $team_members_staff_ids = $table_user_result->data;
            // print_r($team_members_staff_ids);

            $temp_arr_var = [];
            foreach ($team_members_staff_ids as $ids_key => $ids_value) {
                $temp_arr_var[] = $ids_value['staff_unique_id'];
            }
            $team_members_staff_ids = $temp_arr_var;
            $team_members_staff_ids = "'".implode("','",$team_members_staff_ids)."'";
        } else {
            print_r($table_user_result);
            $team_members_staff_ids = '';
        }



        $where             = " AND fu.entry_date >= '".$from_date."' AND fu.entry_date <= '".$to_date."' ";
        $commit_where      = " AND month_year >= '".$from_date_bf."' AND month_year <= '".$to_date_bf."' ";

        $where             .= " AND fu.sess_user_id IN (".$team_members.")";
        $commit_where      .= " AND staff_id IN (".$team_members_staff_ids.")";

        // Get Commited and Achieved By Userwise
        $sql               = business_forecast_tbody_sql($stage,$where,$commit_where);

        $head_result       = $pdo->query($sql);

        if ($head_result->status) {
            $head_result   = $head_result->data[0];

            $head_committed = $head_result['committed'];
            $head_achieved  = $head_result['achieved'];

            // echo $head_percen    = (float)$head_achieved / (float)$head_committed;


        } else {
            print_r($head_result);
            $head_committed = 0.00;
            $head_achieved  = 0.00;

            $head_balance  = 0.00;
        }

        if ((float)$head_achieved && (float)$head_committed) {
            $progress_percentage = round(($head_achieved / $head_committed) * 100);
            // $progress_percentage = $head_committed."t";
        } else {
            $progress_percentage = 0;
        }

        // progress bar background
        $progress_class = progess_bar($progress_percentage);
        $progress_class = $progress_class['class'];

        // $progress_percentage = 0.00;


        $head_committed = moneyFormatIndia($head_committed);
        $head_achieved  = moneyFormatIndia($head_achieved);
?>
    <tr class="collapsed font-weight-bold bg-light-gray" data-toggle="collapse" data-target=".team<?php echo $team_id; ?>">
        <td class="text-left"><?php echo $team_head_staff_name; ?></td>
        <td class="text-center">
            <div class="progress mb-0">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress_percentage; ?>%">
                </div>
            </div>
            <div>
                <?php echo $progress_percentage; ?> %
            </div>
        </td>
        <td><?php echo $head_committed; ?></td>
        <td><?php echo $head_achieved; ?></td>
    </tr>

<?php
    // Members Loop Started Here

    foreach ($team_members_arr as $member_key => $member_value) {

        $team_member_staff_id     = $member_value["staff_unique_id"];
        $team_member_staff_name   = $member_value["staff_name"];
        $team_member_user_id      = $member_value["unique_id"];

        $where             = " AND fu.entry_date >= '".$from_date."' AND fu.entry_date <= '".$to_date."' ";
        $commit_where      = " AND month_year >= '".$from_date_bf."' AND month_year <= '".$to_date_bf."' ";

        $where             .= " AND fu.sess_user_id = '".$team_member_user_id."'";
        $commit_where      .= " AND staff_id = '".$team_member_staff_id."' ";

        // Get Commited and Achieved By Userwise
        $sql               = business_forecast_tbody_sql($stage,$where,$commit_where);

        $member_result       = $pdo->query($sql);

        if ($member_result->status) {
            $member_result   = $member_result->data[0];

            $member_committed = $member_result['committed'];
            $member_achieved = $member_result['achieved'];

            $member_balance  = $member_committed - $member_achieved;


        } else {
            print_r($member_result);
            $member_committed = 0.00;
            $member_achieved = 0.00;

            $member_balance  = 0.00;
        }

        if ((float)$member_achieved && (float)$member_committed) {
            $progress_percentage = round(($member_achieved / $member_committed) * 100);
            // $progress_percentage = $member_committed."t";
        } else {
            $progress_percentage = 0;
        }

        // progress bar background
        $progress_class = progess_bar($progress_percentage);
        $progress_class = $progress_class['class'];

        $member_committed = moneyFormatIndia($member_committed);
        $member_achieved  = moneyFormatIndia($member_achieved);
?>

    <tr class="collapse team<?php echo $team_id; ?>">
        <td class="text-left"><?php echo $team_member_staff_name; ?></td>
        <td class="text-center">
             <div class="progress mb-0">
                <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress_percentage; ?>%">
                </div>
            </div>
            <div>
                <?php echo $progress_percentage; ?> %
            </div>
        </td>
        <td><?php echo $member_committed; ?></td>
        <td><?php echo $member_achieved; ?></td>
    </tr>
<?php
    // Members Loop Ends 
    }
?>

<?php
    // Heads Loop Ends
    }
?>

<!-- <tr class="collapsed font-weight-bold" data-toggle="collapse" data-target=".test">
    <td>Test</td>
    <td>500.00</td>
    <td>250.00</td>
    <td>250.00</td>
</tr>
<tr class="collapse test">
    <td>Berlin</td>
    <td>650.00</td>
    <td>250.00</td>
    <td>400.00</td>
</tr> -->



