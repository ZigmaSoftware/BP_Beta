<?php
include 'function.php';
include '../../config/dbconfig.php';

if ($_POST['report_type'])
{
    $report_type = $_POST['report_type'];
}
else
{
    $report_type = '';
}

if ($report_type == 2)
{
    $quaterly_monthly = $_POST["quarterly_report"];
}
else if ($report_type == 3)
{
    $quaterly_monthly = $_POST["monthly_report"];
}
else
{
    $quaterly_monthly = '';
}

if ($report_type == 2)
{
    $dates = year_quarter_month('quarter', $_POST['quarter']);
}
else if ($report_type == 3)
{
    $dates = year_quarter_month('month', $_POST['month']);
}
else
{
    $dates = year_quarter_month();
}

$from_date = $dates['from_date'];
$to_date = $dates['to_date'];

$from_date_bf = date('Y-m', strtotime($from_date));
$to_date_bf = date('Y-m', strtotime($to_date));

//print_r($_SESSION);

?>
<?php if ($_SESSION['sess_user_type'] == '5f97fc3257f2525529')
{ ?>
<input type="hidden" name="is_team_head" id="is_team_head" value="<?php echo $_SESSION['is_team_head']; ?>">
<input type="hidden" name="user_type" id="user_type" value="<?php echo $_SESSION['sess_user_type']; ?>">
<div id= "staff_target" class="table-responsive table-striped">
   <table id="records_table" class="table table-bordered ">
      <thead>
         <tr>
            <th>Staff Name</th>
            <th>Progress</th>
            <th>Target</th>
            <th>Achieved</th>
         </tr>
      </thead>
      <tbody>
         <?php
    $team_heads = team_heads();
    $target_amount = 0;
    $team_count = 1;

    foreach ($team_heads as $team_head_key => $team_head_value)
    {
        $team_head_staff_id = $team_head_value["staff_unique_id"];
        $team_head_staff_name = $team_head_value["staff_name"];
        $team_head_user_id = $team_head_value["unique_id"];
        $team_id = $team_head_value["team_id"];

        $tar_amt = target_amount($team_id, $report_type, $quaterly_monthly);
        $target_amount = $tar_amt[0]['target_amount'];

        if ($team_head_value["user_image"] != '')
        {
            $profile_image = "uploads/staff/" . $team_head_value["user_image"];
        }
        else
        {
            $profile_image = "img/user.jpg";
        }

        $team_members = $team_head_user_id . "," . $team_head_value["team_members"];

        $team_temp = $team_members;

        $team_members = team_members($team_members);

        // Team Head Commit Amount
        $team_temp = explode(",", $team_temp);

        $team_temp = "'" . implode("','", $team_temp) . "'";

        $where             = " vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";

        $where             .= " AND vfp.user_id IN (".$team_temp.")";

        $target_sql = "SELECT IFNULL(SUM(total),0.00)  AS target FROM view_followup_product AS vfp WHERE $where";

        $target_result = $pdo->query($target_sql);

        if ($target_result->status && (!(empty($target_result->data[0]))))
        {
            $commit_amount = $target_result->data[0]['target'];
        }
        else
        {
            $commit_amount = 0.00;
        }

        if ((float)$target_amount && (float)$commit_amount) {
            $progress_percentage = ($commit_amount / $target_amount) * 100;
        } else {            
            $progress_percentage = 0;
        }

        // progress bar background
        $progress_class = progess_bar($progress_percentage);
        $progress_class = $progress_class['class'];
?>
         <tr class="collapsed font-weight-bold" data-toggle="collapse" data-target=".nest_col_<?=$team_head_staff_id; ?>">
            <th><img src="<?=$profile_image; ?>"  class="rounded-circle"  width="30" height="30">&nbsp;<?=$team_head_staff_name; ?></th>
            <td>
                <div class="progress mb-0">
                    <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress_percentage; ?>%">
                    </div>
                </div>
                <div class="d-block">
                    <?php echo $progress_percentage; ?> %
                </div>
            </td>
            <td  onclick="team_head_modal('<?=$team_head_staff_id; ?>','<?=$target_amount; ?>','<?=$team_head_staff_name; ?>','1');" ><?=number_format($target_amount, 2); ?></td>
            <td><?=$commit_amount; ?></td>
         </tr>
         <?php
        // Team Members Loop Start
        $team_member_count = 1;
        $member_target_amount = 0;

        foreach ($team_members as $team_member_key => $team_member_value)
        {

            $team_member_staff_id = $team_member_value["staff_unique_id"];
            $team_member_staff_name = $team_member_value["staff_name"];
            $team_member_user_id = $team_member_value["unique_id"];

            $member_tar_amt = member_target_amount($team_member_staff_id, $report_type, $quaterly_monthly);
            $member_target_amount = $member_tar_amt[0]['target_amount'];

            // if($team_member_value["profile_image"] != ''){
            //     $member_profile_image          = "folders/password/upload/".$team_member_value["profile_image"];
            // }else{
            //     $member_profile_image          = "img/user.jpg";
            // }
            if ($team_member_value["user_image"] != '')
            {
                $member_profile_image = "uploads/staff/" . $team_member_value["user_image"];
            }
            else
            {
                $member_profile_image = "img/user.jpg";
            }

            $where             = " vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";

            $where             .= " AND vfp.user_id ='".$team_member_user_id."' ";

            $target_sql = "SELECT IFNULL(SUM(total),0.00)  AS target FROM view_followup_product AS vfp WHERE $where";

            $target_result = $pdo->query($target_sql);

            if ($target_result->status && (!(empty($target_result->data[0]))))
            {
                $commit_amount = $target_result->data[0]['target'];
            }
            else
            {
                $commit_amount = 0.00;
            }

            if ((float)$target_amount && (float)$commit_amount) {
                $progress_percentage = ($commit_amount / $target_amount) * 100;
            } else {            
                $progress_percentage = 0;
            }
    
            // progress bar background
            $progress_class = progess_bar($progress_percentage);
            $progress_class = $progress_class['class'];
?>
         <tr class="collapse nest_col_<?=$team_head_staff_id; ?>">
            <td>
                <img src="<?=$member_profile_image; ?>"  class="rounded-circle"  width="25" height="25" >                
                <?=$team_member_staff_name; ?>
            </td>
            <td>
                <div class="progress mb-0">
                    <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress_percentage; ?>%">
                    </div>
                </div>
                <div class="d-block">
                    <?php echo $progress_percentage; ?> %
                </div>
            </td>
            <td onclick="team_head_modal('<?=$team_member_staff_id; ?>','<?=$member_target_amount; ?>','<?=$team_member_staff_name; ?>','0');"><?=number_format($member_target_amount, 2); ?></td>
            <td><?=$commit_amount; ?></td>
         </tr>
         <?php
        } ?>
         <?php
    } ?>
      </tbody>
   </table>
</div>
<?php
}
else
{
    if ($_SESSION['is_team_head'] == 1)
    {

?>
<div id= "staff_target" class="table-responsive table-striped">
   <input type="hidden" name="is_team_head" id="is_team_head" value="<?php echo $_SESSION['is_team_head']; ?>">
   <input type="hidden" name="user_type" id="user_type" value="<?php echo $_SESSION['sess_user_type']; ?>">
   <table id="records_table" class="table table-bordered ">
      <thead>
         <tr>
            <th>Staff Name</th>
            <th>Progress</th>
            <th>Target</th>
            <th>Archieved</th>
         </tr>
      </thead>
      <tbody>
         <?php
        // Team Head Start
        $team_heads = team_head_dispaly($_SESSION['user_id']);
        $target_amount = 0;
        $team_count = 1;

        foreach ($team_heads as $team_head_key => $team_head_value)
        {
            $team_head_staff_id = $team_head_value["staff_unique_id"];
            $team_head_staff_name = $team_head_value["staff_name"];
            $team_head_user_id = $team_head_value["unique_id"];
            $team_id = $team_head_value["team_id"];

            $tar_amt = target_amount($team_id, $report_type, $quaterly_monthly);
            $target_amount = $tar_amt[0]['target_amount'];

            if ($team_head_value["profile_image"] != '')
            {
                $profile_image = "folders/password/upload/" . $team_head_value["profile_image"];
            }
            else
            {
                $profile_image = "img/user.jpg";
            }

            $team_members = $team_head_user_id . "," . $team_head_value["team_members"];

            $team_temp    = $team_members;

            $team_members = team_members($team_members);

            // Team Head Commit Amount
            $team_temp = explode(",", $team_temp);

            $team_temp = "'" . implode("','", $team_temp) . "'";

            $where             = " vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";

            $where             .= " AND vfp.user_id IN (".$team_temp.")";
    
            $target_sql = "SELECT IFNULL(SUM(total),0.00)  AS target FROM view_followup_product AS vfp WHERE $where";

            $target_result = $pdo->query($target_sql);

            if ($target_result->status && (!(empty($target_result->data[0]))))
            {
                $commit_amount = $target_result->data[0]['target'];
            }
            else
            {
                $commit_amount = 0.00;
            }
            if ((float)$target_amount && (float)$commit_amount) {
                $progress_percentage = ($commit_amount / $target_amount) * 100;
            } else {            
                $progress_percentage = 0;
            }
    
            // progress bar background
            $progress_class = progess_bar($progress_percentage);
            $progress_class = $progress_class['class'];
?>
         <tr class="collapsed font-weight-bold" data-toggle="collapse" data-target=".nest_col_<?=$team_head_staff_id; ?>">
            <th><img src="<?=$profile_image; ?>"  class="rounded-circle"  width="30" height="30">&nbsp;<?=$team_head_staff_name; ?></th>
            <td>
                <div class="progress mb-0">
                    <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress_percentage; ?>%">
                    </div>
                </div>
                <div class="d-block">
                    <?php echo $progress_percentage; ?> %
                </div>
            </td>
            <td  onclick="team_head_modal('<?=$team_head_staff_id; ?>','<?=$target_amount; ?>','<?=$team_head_staff_name; ?>','1');" ><?=number_format($target_amount, 2); ?></td>
            <td><?=$commit_amount; ?></td>
         </tr>
         <?php
            // Team Members Loop Start
            $team_member_count = 1;
            $member_target_amount = 0;

            foreach ($team_members as $team_member_key => $team_member_value)
            {

                $team_member_staff_id = $team_member_value["staff_unique_id"];
                $team_member_staff_name = $team_member_value["staff_name"];
                $team_member_user_id = $team_member_value["unique_id"];

                $member_tar_amt = member_target_amount($team_member_staff_id, $report_type, $quaterly_monthly);
                $member_target_amount = $member_tar_amt[0]['target_amount'];

                if ($team_member_value["profile_image"] != '')
                {
                    $member_profile_image = "folders/password/upload/" . $team_member_value["profile_image"];
                }
                else
                {
                    $member_profile_image = "img/user.jpg";
                }
                
                $where             = " vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";

                $where             .= " AND vfp.user_id ='".$team_member_user_id."' "; 
    
                $target_sql = "SELECT IFNULL(SUM(total),0.00)  AS commited FROM view_followup_product AS vfp WHERE $where";

                $target_result = $pdo->query($target_sql);

                if ($target_result->status && (!(empty($target_result->data[0]))))
                {
                    $commit_amount = $target_result->data[0]['commited'];
                }
                else
                {
                    $commit_amount = 0.00;
                }

                if ((float)$target_amount && (float)$commit_amount) {
                    $progress_percentage = ($commit_amount / $target_amount) * 100;
                } else {            
                    $progress_percentage = 0;
                }
        
                // progress bar background
                $progress_class = progess_bar($progress_percentage);
                $progress_class = $progress_class['class'];
        ?>
         <tr class="collapse nest_col_<?=$team_head_staff_id; ?>">
            <td><img src="<?=$member_profile_image; ?>"  class="rounded-circle"  width="25" height="25" ><?=$team_member_staff_name; ?></td>
            <td>
                <div class="progress mb-0">
                    <div class="progress-bar progress-bar-striped progress-bar-animated <?php echo $progress_class; ?>" role="progressbar" aria-valuenow="<?php echo $progress_percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $progress_percentage; ?>%">
                    </div>
                </div>
                <div class="d-block">
                    <?php echo $progress_percentage; ?> %
                </div>
            </td>
            <td onclick="team_head_modal('<?=$team_member_staff_id; ?>','<?=$member_target_amount; ?>','<?=$team_member_staff_name; ?>','0');"><?=number_format($member_target_amount, 2); ?></td>
            <td><?=$commit_amount; ?></td>
         </tr>
         <?php
            } ?>
      </tbody>
   </table>
</div>
<?php
    } ?>
<?php
    }
    else
    {
        // Team Members Loop Start
        $team_members = team_members_display($_SESSION['user_id']);
        $team_member_count = 1;
        $member_target_amount = 0;
        $team_member_value = $team_members[0];

        $team_member_staff_id = $team_member_value["staff_unique_id"];
        $team_member_staff_name = $team_member_value["staff_name"];
        $team_member_user_id = $team_member_value["unique_id"];

        $member_tar_amt = member_target_amount($team_member_staff_id, $report_type, $quaterly_monthly);

        $member_target_amount = $member_tar_amt[0]['target_amount'];

        if ($team_member_value["user_image"] != '')
        {
            $member_profile_image = "uploads/staff/" . $team_member_value["user_image"];
        }
        else
        {
            $member_profile_image = "img/user.jpg";
        }

        $where             = " vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";

        $where             .= " AND vfp.user_id ='".$team_member_user_id."' AND business_forecast = 'cty60c0233a7305c82174'  "; // commit only

        $target_sql = "SELECT IFNULL(SUM(total),0.00)  AS target FROM view_followup_product AS vfp WHERE $where";

        $target_result = $pdo->query($target_sql);

        if ($target_result->status && (!(empty($target_result->data[0]))))
        {
            $commit_amount = $target_result->data[0]['target'];
        }
        else
        {
            $commit_amount = 0.00;
        }

        $percentage = 0.00;

        if ((float)$commit_amount && (float)$member_target_amount)
        {
            $percentage = ($commit_amount / $member_target_amount) * 100;
        }

        $archieved = $member_target_amount * ($percentage / 100);

        $tar_arr = [
            "target" => moneyFormatIndia($member_target_amount) , 
            "staff_id" => $team_member_staff_id, 
            "staff_name" => $team_member_staff_name, 
            "archieved" => moneyFormatIndia($commit_amount) , 
            "percentage" => $percentage
        ];

        echo json_encode($tar_arr);
    }
} ?>
