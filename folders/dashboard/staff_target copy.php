<?php 
    include 'function.php';
    include '../../config/dbconfig.php';

    if($_POST['report_type']){
        $report_type  = $_POST['report_type'];
    }else{
        $report_type  = '';
    }

    if($report_type == 2){
        $quaterly_monthly  = $_POST["quarterly_report"];
    }else if($report_type == 3){
        $quaterly_monthly  = $_POST["monthly_report"];
    }else{
        $quaterly_monthly  = '';
    }

//print_r($_SESSION);

?>
    <?php if($_SESSION['sess_user_type'] == '5f97fc3257f2525529'){ ?>
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
                    $team_heads         = team_heads();
                    $target_amount      = 0;
                    $team_count         = 1;
                
                    foreach ($team_heads as $team_head_key => $team_head_value) {
                        $team_head_staff_id     = $team_head_value["staff_unique_id"];
                        $team_head_staff_name   = $team_head_value["staff_name"];
                        $team_head_user_id      = $team_head_value["unique_id"];
                        $team_id                = $team_head_value["team_id"];

                        $tar_amt          = target_amount($team_id,$report_type,$quaterly_monthly);
                        $target_amount    = $tar_amt[0]['target_amount'];


                        
                        if($team_head_value["user_image"] != ''){
                            $profile_image          = "uploads/staff/".$team_head_value["user_image"];
                        }else{
                            $profile_image          = "img/user.jpg";
                        }
                        
                        $team_members           = $team_head_user_id.",".$team_head_value["team_members"];

                        $team_mem               = $team_members;

                        $team_members           = team_members($team_members);

                        // Get Commit Amount Start

                        if ($_POST['report_type'] == 2) {
                            $dates  = year_quarter_month('quarter',$_POST['quarter']);
                        } else if ($_POST['report_type'] == 3) {
                            $dates  = year_quarter_month('month',$_POST['month']);
                        } else {
                            $dates  = year_quarter_month();
                        }

                        $from_date  = $dates['from_date'];
                        $to_date    = $dates['to_date'];
                        
                        $from_date_bf  = date('Y-m',strtotime($from_date));
                        $to_date_bf    = date('Y-m',strtotime($to_date));

                        $where             = " AND vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";
                        $commit_where      = " AND business_forecast_target.month_year >= '".$from_date_bf."' AND business_forecast_target.month_year <= '".$to_date_bf."' ";

                        // User Wise Control End

                        $team_mem       = explode(",",$team_mem);

                        $team_mem       = "'".implode("','",$team_mem)."'";

                        $sql_query      = "SELECT '0.00' AS forecast,'0.00' AS progress, '0.00' AS target,'0.00' AS achieved ";

                        $sql_query      = "SELECT 0.00 AS achieved ,IFNULL(SUM(target),0.00) AS target FROM business_forecast_target WHERE is_delete = 0 $commit_where";

                                
                        $result = $pdo->query($sql_query);

                        $total_target   = 0.00;
                        $total_achieved = 0.00;

                        if (($result->status) && (!empty($result->data))) {
                            $data_temp = $result->data[0];
                        } else {
                            $data_temp = [
                                "target" => 0.00,
                                "achieved" => 0.00
                            ];
                        }

                        // foreach ($result->data as $data_key => $data_value) {

                            $data_target    = $data_temp['target'];
                            $data_achieved  = $data_temp['achieved'];

                            if ((float)$data_target) {                
                                $data_percentage = round(($data_achieved / $data_target) * 100);
                            } else {
                                $data_percentage = 0;
                            }

                        // }
                        // Get Commit Amount End


                        // Temp
                        // $percentage        = rand(1,100);

                        // $archieved         = $member_target_amount * ($percentage / 100);
                        if ($data_target && $member_target_amount) {
                            
                            $percentage   = ($data_target / $member_target_amount) * 100;
                        } else {
                            $percentage   = 0;
                        }
                        $percentage           = $percentage * 1;

                ?>
                        <tr class="collapsed font-weight-bold" data-toggle="collapse" data-target=".nest_col_<?=$team_head_staff_id;?>">
                            <th><img src="<?=$profile_image;?>"  class="rounded-circle"  width="30" height="30">&nbsp;<?=$team_head_staff_name;?></th>
                            <td><div class="progress mb-0"><div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage; ?>%"></div></div><?php echo $percentage; ?>%</td>
                            <td  onclick="team_head_modal('<?=$team_head_staff_id;?>','<?=$target_amount;?>','<?=$team_head_staff_name; ?>','1');" ><?=number_format($target_amount,2);?></td>
                            <td><?php echo number_format($data_target,2); ?></td>
                        </tr>
                        <?php
                        // Team Members Loop Start

                            $team_member_count         = 1;
                            $member_target_amount      = 0;
                        
                        foreach ($team_members as $team_member_key => $team_member_value) {

                            $team_member_staff_id     = $team_member_value["staff_unique_id"];
                            $team_member_staff_name   = $team_member_value["staff_name"];
                            $team_member_user_id      = $team_member_value["unique_id"];

                            $member_tar_amt          = member_target_amount($team_member_staff_id,$report_type,$quaterly_monthly);
                            $member_target_amount    = $member_tar_amt[0]['target_amount'];

                            // if($team_member_value["profile_image"] != ''){
                            //     $member_profile_image          = "folders/password/upload/".$team_member_value["profile_image"];
                            // }else{
                            //     $member_profile_image          = "img/user.jpg";
                            // }

                            if($team_member_value["user_image"] != ''){
                                $member_profile_image   = "uploads/staff/".$team_member_value["user_image"];
                            }else{
                                $member_profile_image   = "img/user.jpg";
                            }

                                                    // Get Commit Amount Start

                        if ($_POST['report_type'] == 2) {
                            $dates  = year_quarter_month('quarter',$_POST['quarter']);
                        } else if ($_POST['report_type'] == 3) {
                            $dates  = year_quarter_month('month',$_POST['month']);
                        } else {
                            $dates  = year_quarter_month();
                        }

                        $from_date  = $dates['from_date'];
                        $to_date    = $dates['to_date'];
                        
                        $from_date_bf  = date('Y-m',strtotime($from_date));
                        $to_date_bf    = date('Y-m',strtotime($to_date));

                        $where             = " AND vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";
                        $commit_where      = " AND business_forecast_target.month_year >= '".$from_date_bf."' AND business_forecast_target.month_year <= '".$to_date_bf."' ";

                        // User Wise control Start
                        if ($admin_user_type != $_SESSION['sess_user_type']) {

                            $is_team_head = 0;
                            $team_members = 0;
                            $staff_name   = "";

                            $user_columns = [
                                "is_team_head",
                                "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
                                "team_members"
                            ];

                            $user_table_details = [
                                "user",
                                $user_columns
                            ];

                            $user_table_where   = " is_delete = 0 AND unique_id = '".$_SESSION['user_id']."' ";


                            $user_result = $pdo->select($user_table_details,$user_table_where);

                            if ($user_result->status) {
                                $user_data = $user_result->data[0];

                                // $staff_name   = $user_data['staff_name'];
                                $is_team_head = $user_data['is_team_head'];
                                $team_members = $user_data['team_members'];


                                if ($is_team_head) {
                                    $team_members = explode(",",$team_members);

                                    $team_members = "'".implode("','",$team_members)."','".$_SESSION['user_id']."'";

                                    // GET Team Members Staff_ids Business Forecast Table Using Staff ID is mapping 

                                    // That is why we are here select staff ids from users table using user ids

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

                                    $where          .= " AND vfp.user_id IN (".$team_members.")";
                                    $commit_where   .= " AND business_forecast_target.staff_id IN (".$team_members_staff_ids.")";

                                } else {
                                    $where          .= " AND vfp.user_id ='".$_SESSION['user_id']."' ";
                                    $commit_where   .= " AND business_forecast_target.staff_id ='".$_SESSION['staff_id']."' ";
                                }
                                
                            } else {
                                print_r($user_result);
                            }           
                        } 

                        // User Wise Control End

                        // SQL Query to Get Current business Forecast Data
                        $sql_query      = "SELECT fc.forecast,'' AS progress,(SELECT IFNULL(SUM(target),0.00) AS target    FROM business_forecast_target WHERE business_forecast_target.business_forecast=fc.unique_id $commit_where) AS target,IFNULL(SUM(vfp.total),0.00) AS achieved,fc.unique_id FROM forecast AS fc LEFT JOIN view_followup_product AS vfp ON fc.unique_id = vfp.business_forecast  $where WHERE fc.is_delete = 0 AND fc.is_active = 1  GROUP BY fc.unique_id,vfp.business_forecast";

                        $sql_query      = "SELECT '0.00' AS forecast,'0.00' AS progress, '0.00' AS target,'0.00' AS achieved ";

                        $result = $pdo->query($sql_query);

                        $total_target   = 0.00;
                        $total_achieved = 0.00;

                        foreach ($result->data as $data_key => $data_value) {

                            $data_target    = $data_value['target'];
                            $data_achieved  = $data_value['achieved'];

                            if ((float)$data_target) {                
                                $data_percentage = round(($data_achieved / $data_target) * 100);
                            } else {
                                $data_percentage = 0;
                            }

                            
                            
                            

                        }
                        // Get Commit Amount End


                        // Temp
                        // $percentage        = rand(1,100);

                        // $archieved         = $member_target_amount * ($percentage / 100);
                        if ($total_achieved && $member_target_amount) {
                            
                            $percentage   = ($total_achieved / $member_target_amount) * 100;
                        } else {
                            $percentage   = 0;
                        }
                        $percentage           = $percentage * 1;
                        ?>
                            <tr class="collapse nest_col_<?=$team_head_staff_id;?>">
                                <td><img src="<?=$member_profile_image;?>"  class="rounded-circle"  width="25" height="25" ><?=$team_member_staff_name; ?></td>
                                <td><div class="progress mb-0"><div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage; ?>%"></div></div><?php echo $percentage; ?>%</td>
                                <td onclick="team_head_modal('<?=$team_member_staff_id;?>','<?=$member_target_amount;?>','<?=$team_member_staff_name; ?>','0');"><?=number_format($member_target_amount,2);?></td>
                                <td><?php echo $total_achieved; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
            </tbody>
        </table>
    </div>
    <?php } else {

        if($_SESSION['is_team_head'] == 1) { 
            
    
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
                        $team_heads         = team_head_dispaly($_SESSION['user_id']);
                        $target_amount      = 0;
                        $team_count         = 1;

                        foreach ($team_heads as $team_head_key => $team_head_value) {
                            $team_head_staff_id     = $team_head_value["staff_unique_id"];
                            $team_head_staff_name   = $team_head_value["staff_name"];
                            $team_head_user_id      = $team_head_value["unique_id"];
                            $team_id                = $team_head_value["team_id"];

                            $tar_amt          = target_amount($team_id,$report_type,$quaterly_monthly);
                            $target_amount    = $tar_amt[0]['target_amount'];


                            
                            if($team_head_value["profile_image"] != ''){
                                $profile_image          = "folders/password/upload/".$team_head_value["profile_image"];
                            }else{
                                $profile_image          = "img/user.jpg";
                            }
                            
                            $team_members           = $team_head_user_id.",".$team_head_value["team_members"];

                                // Get Commit Amount Start

                                if ($_POST['report_type'] == 2) {
                                    $dates  = year_quarter_month('quarter',$_POST['quarter']);
                                } else if ($_POST['report_type'] == 3) {
                                    $dates  = year_quarter_month('month',$_POST['month']);
                                } else {
                                    $dates  = year_quarter_month();
                                }

                                $from_date  = $dates['from_date'];
                                $to_date    = $dates['to_date'];
                                
                                $from_date_bf  = date('Y-m',strtotime($from_date));
                                $to_date_bf    = date('Y-m',strtotime($to_date));

                                $where             = " AND vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";
                                $commit_where      = " AND business_forecast_target.month_year >= '".$from_date_bf."' AND business_forecast_target.month_year <= '".$to_date_bf."' ";

                                $sql_query      = "SELECT '0.00' AS forecast,'0.00' AS progress, '0.00' AS target,'0.00' AS achieved ";

                                $result = $pdo->query($sql_query);

                                $total_target   = 0.00;
                                $total_achieved = 0.00;

                                foreach ($result->data as $data_key => $data_value) {

                                    $data_target    = $data_value['target'];
                                    $data_achieved  = $data_value['achieved'];

                                    if ((float)$data_target) {                
                                        $data_percentage = round(($data_achieved / $data_target) * 100);
                                    } else {
                                        $data_percentage = 0;
                                    }

                                    
                                    
                                    

                                }
                                // Get Commit Amount End


                                // Temp
                                // $percentage        = rand(1,100);

                                // $archieved         = $member_target_amount * ($percentage / 100);
                                if ($total_achieved && $member_target_amount) {
                                    
                                    $percentage   = ($total_achieved / $member_target_amount) * 100;
                                } else {
                                    $percentage   = 0;
                                }
                                $percentage           = $percentage * 1;

                            $team_members           = team_members($team_members);
                    ?>
                            <tr class="collapsed font-weight-bold" data-toggle="collapse" data-target=".nest_col_<?=$team_head_staff_id;?>">
                            <th><img src="<?=$profile_image;?>"  class="rounded-circle"  width="30" height="30">&nbsp;<?=$team_head_staff_name;?></th>
                            <td><div class="progress mb-0"><div class="progress-bar progress-bar-striped progress-bar-animated " role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage; ?>%"></div></div><?php echo $percentage; ?>%</td>
                            <td  onclick="team_head_modal('<?=$team_head_staff_id;?>','<?=$target_amount;?>','<?=$team_head_staff_name; ?>','1');" ><?=number_format($target_amount,2);?></td>
                            <td><?php echo $total_achieved; ?></td>
                        </tr>
                        <?php
                        // Team Members Loop Start

                            $team_member_count         = 1;
                            $member_target_amount      = 0;
                        
                        foreach ($team_members as $team_member_key => $team_member_value) {

                            $team_member_staff_id     = $team_member_value["staff_unique_id"];
                            $team_member_staff_name   = $team_member_value["staff_name"];
                            $team_member_user_id      = $team_member_value["unique_id"];

                            $member_tar_amt          = member_target_amount($team_member_staff_id,$report_type,$quaterly_monthly);
                            $member_target_amount    = $member_tar_amt[0]['target_amount'];

                            if($team_member_value["profile_image"] != ''){
                                $member_profile_image          = "folders/password/upload/".$team_member_value["profile_image"];
                            }else{
                                $member_profile_image          = "img/user.jpg";
                            }


                            // Get Commit Amount Start

                            if ($_POST['report_type'] == 2) {
                                $dates  = year_quarter_month('quarter',$_POST['quarter']);
                            } else if ($_POST['report_type'] == 3) {
                                $dates  = year_quarter_month('month',$_POST['month']);
                            } else {
                                $dates  = year_quarter_month();
                            }

                            $from_date  = $dates['from_date'];
                            $to_date    = $dates['to_date'];
                            
                            $from_date_bf  = date('Y-m',strtotime($from_date));
                            $to_date_bf    = date('Y-m',strtotime($to_date));

                            $where             = " AND vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";
                            $commit_where      = " AND business_forecast_target.month_year >= '".$from_date_bf."' AND business_forecast_target.month_year <= '".$to_date_bf."' ";

                            $sql_query      = "SELECT '0.00' AS forecast,'0.00' AS progress, '0.00' AS target,'0.00' AS achieved ";

                            $result = $pdo->query($sql_query);

                            $total_target   = 0.00;
                            $total_achieved = 0.00;

                            foreach ($result->data as $data_key => $data_value) {

                                $data_target    = $data_value['target'];
                                $data_achieved  = $data_value['achieved'];

                                if ((float)$data_target) {                
                                    $data_percentage = round(($data_achieved / $data_target) * 100);
                                } else {
                                    $data_percentage = 0;
                                }

                                
                                
                                

                            }
                            // Get Commit Amount End


                            // Temp
                            // $percentage        = rand(1,100);

                            // $archieved         = $member_target_amount * ($percentage / 100);
                            if ($total_achieved && $member_target_amount) {
                                
                                $percentage   = ($total_achieved / $member_target_amount) * 100;
                            } else {
                                $percentage   = 0;
                            }
                            $percentage           = $percentage * 1;

                        ?>
                            <tr class="collapse nest_col_<?=$team_head_staff_id;?>">
                                <td><img src="<?=$member_profile_image;?>"  class="rounded-circle"  width="25" height="25" ><?=$team_member_staff_name; ?></td>
                                <td><div class="progress mb-0"><div class="progress-bar progress-bar-striped bg-info" role="progressbar" aria-valuenow="<?php echo $percentage; ?>" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $percentage; ?>%"></div></div><?php echo $percentage; ?>%</td>
                                <td onclick="team_head_modal('<?=$team_member_staff_id;?>','<?=$member_target_amount;?>','<?=$team_member_staff_name; ?>','0');"><?=number_format($member_target_amount,2);?></td>
                                <td><?php echo $total_achieved; ?></td>
                            </tr>
                        <?php } ?>
                </tbody>
            </table>
        </div>
        <?php } ?>
    <?php } else { 


// Team Members Loop Start
    $team_members              = team_members_display($_SESSION['user_id']);
    $team_member_count         = 1;
    $member_target_amount      = 0;
// print_r($team_members);
foreach ($team_members as $team_member_key => $team_member_value) {

    $team_member_staff_id     = $team_member_value["staff_unique_id"];
    $team_member_staff_name   = $team_member_value["staff_name"];
    $team_member_user_id      = $team_member_value["unique_id"];

    $member_tar_amt          = member_target_amount($team_member_staff_id,$report_type,$quaterly_monthly);
    $member_target_amount    = $member_tar_amt[0]['target_amount'];

    if($team_member_value["profile_image"] != ''){
        $member_profile_image   = "folders/password/upload/".$team_member_value["profile_image"];
    }else{
        $member_profile_image   = "img/user.jpg";
    }

    if($team_member_value["user_image"] != ''){
        $member_profile_image   = "uploads/staff/".$team_member_value["user_image"];
    }else{
        $member_profile_image   = "img/user.jpg";
    }

    // Get Commit Amount Start

    if ($_POST['report_type'] == 2) {
        $dates  = year_quarter_month('quarter',$_POST['quarter']);
    } else if ($_POST['report_type'] == 3) {
        $dates  = year_quarter_month('month',$_POST['month']);
    } else {
        $dates  = year_quarter_month();
    }

    $from_date  = $dates['from_date'];
    $to_date    = $dates['to_date'];
    
    $from_date_bf  = date('Y-m',strtotime($from_date));
    $to_date_bf    = date('Y-m',strtotime($to_date));

    $where             = " AND vfp.entry_date >= '".$from_date."' AND vfp.entry_date <= '".$to_date."' ";
    $commit_where      = " AND business_forecast_target.month_year >= '".$from_date_bf."' AND business_forecast_target.month_year <= '".$to_date_bf."' ";


    $sql_query      = "SELECT '0.00' AS forecast,'0.00' AS progress, '0.00' AS target,'0.00' AS achieved ";
            
    $result = $pdo->query($sql_query);

    $total_target   = 0.00;
    $total_achieved = 0.00;

    foreach ($result->data as $data_key => $data_value) {

        $data_target    = $data_value['target'];
        $data_achieved  = $data_value['achieved'];

        if ((float)$data_target) {                
            $data_percentage = round(($data_achieved / $data_target) * 100);
        } else {
            $data_percentage = 0;
        }

        
        
        

    }
    // Get Commit Amount End


    // Temp
    // $percentage        = rand(1,100);

    // $archieved         = $member_target_amount * ($percentage / 100);
    if ($total_achieved && $member_target_amount) {
        
        $percentage   = ($total_achieved / $member_target_amount) * 100;
    } else {
        $percentage   = 0;
    }
    $percentage           = $percentage * 1;

    $tar_arr = [
        "target"       => moneyFormatIndia($member_target_amount),
        "staff_id"     => $team_member_staff_id,
        "staff_name"   => $team_member_staff_name,
        "archieved"     => moneyFormatIndia($total_achieved),
        "percentage"   => $percentage
    ];

    echo json_encode($tar_arr);
   }  
   
   ?>
    <?php } ?>
<?php } ?>