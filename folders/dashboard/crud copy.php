<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "user_screen_actions";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';


// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "sql"       => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$action_name        = "";
$is_active          = "";
$unique_id          = "";
$prefix             = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {
    

    case 'dashboard_filter':

        $dashboard_month    = $_POST['dashboard_month'];

        // Count Related Queries
            $count_sql          = "SELECT
                                        *                                        
                                    FROM
                                        (
                                        SELECT
                                            (
                                            SELECT
                                                COUNT(unique_id)
                                            FROM
                                                follow_up_call_sublist
                                            WHERE
                                            DATE_FORMAT(entry_date,'%Y-%m')='".$_POST['dashboard_month']."'
                                                AND is_active = 1 AND is_delete = 0 AND prev_follow_up_unique_id = 'new'
                                        ) AS new_calls,
                                        (
                                        SELECT
                                            COUNT(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                        
                                          DATE_FORMAT(next_follow_up_date,'%Y-%m') <='".$_POST['dashboard_month']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 0
                                    ) AS follow_ups,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            DATE_FORMAT(updated_date,'%Y-%m')='".$_POST['dashboard_month']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 1
                                    ) AS updated,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            DATE_FORMAT(entry_date,'%Y-%m')='".$_POST['dashboard_month']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 2
                                    ) AS closed
                                    ) AS count_table";

            $count_result       = $pdo->query($count_sql);

             if ($count_result->status) {

                $status     = $count_result->status;
                $data       = $count_result->data;
                $error      = $count_result->error;
                $sql        = $count_result->sql;
                $msg        = "success";

            } else {
                $status     = $count_result->status;
                $data       = $count_result->data;
                $error      = $count_result->error;
                $sql        = $count_result->sql;
                $msg        = "error";
            }

            $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];


        echo json_encode($json_array);

        break;

    case 'dashboard_filter_leads':

            $dashboard_month    = $_POST['dashboard_month'];

            // Count Related Queries
            $count_sql          = "SELECT
                                        *                                        
                                    FROM
                                        (
                                        SELECT
                                            (
                                            SELECT
                                                COUNT(unique_id)
                                            FROM
                                                leads_sublist
                                            WHERE
                                            DATE_FORMAT(entry_date,'%Y-%m')='".$_POST['dashboard_month']."'
                                                AND is_active = 1 AND is_delete = 0 AND prev_lead_unique_id = 'new'
                                        ) AS new_calls,
                                        (
                                        SELECT
                                            COUNT(unique_id)
                                        FROM
                                            leads_sublist
                                        WHERE
                                        
                                          DATE_FORMAT(next_follow_up_date,'%Y-%m') <='".$_POST['dashboard_month']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 0
                                    ) AS follow_ups,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            leads_sublist
                                        WHERE
                                            DATE_FORMAT(updated_date,'%Y-%m')='".$_POST['dashboard_month']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 1
                                    ) AS updated,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            leads_sublist
                                        WHERE
                                            DATE_FORMAT(entry_date,'%Y-%m')='".$_POST['dashboard_month']."' AND is_active = 1 AND is_delete = 0 AND is_updated = 2
                                    ) AS closed
                                    ) AS count_table";

            $count_result       = $pdo->query($count_sql);

             if ($count_result->status) {

                $status     = $count_result->status;
                $data       = $count_result->data;
                $error      = $count_result->error;
                $sql        = $count_result->sql;
                $msg        = "success";

            } else {
                $status     = $count_result->status;
                $data       = $count_result->data;
                $error      = $count_result->error;
                $sql        = $count_result->sql;
                $msg        = "error";
            }

            $json_array   = [
            "status"    => $status,
            "data"      => $data,
            "error"     => $error,
            "msg"       => $msg,
            "sql"       => $sql
        ];


        echo json_encode($json_array);

        break;
        
    case 'dashboard':
        $dashboard_month    = $_POST['dashboard_month'];
        
        break;
    
    case 'dashboard_count':

        $count_where = '';

        $pending_count_where = " ((next_follow_up_date <= '".$_POST['to_date']."' AND is_delete = 0 AND  is_updated = 0 AND follow_up_action != 0) OR (prev_follow_up_unique_id !='new' AND call_stage = 0";

        if ($_SESSION['sess_user_type'] != $admin_user_type) {

            $under_user             = user_hierarchy($_SESSION['user_id'],$_SESSION['sess_user_type']);

            $curr_under_users       = '"'.implode('","',explode(",",$under_user['under_user'])).'"';
            $curr_under_users       = $under_user['under_user'];
            $curr_under_user_types  = $under_user['under_user_type'];

            // $where                  .= " AND sess_user_id IN (".$curr_under_users.")";
            $count_where            = " AND sess_user_id IN (".$curr_under_users.")";

        }

        if ($executive_name) {
            // $where              .= " AND sess_user_id = '".$executive_name."' ";
            $count_where        .= " AND sess_user_id = '".$executive_name."' ";
        }

        $count_sql = "SELECT * FROM
                        ( SELECT 
                        (SELECT COUNT(unique_id) FROM follow_ups WHERE  prev_follow_up_unique_id = 'new' AND call_date >= '".$_POST['from_date']."' AND call_date <= '".$_POST['to_date']."' AND is_delete = 0 $count_where) AS new,

                        (SELECT COUNT(unique_id) FROM follow_ups WHERE $pending_count_where $count_where) AS pending,

                        (SELECT COUNT(unique_id) FROM follow_ups WHERE  entry_date >= '".$_POST['from_date']."' AND entry_date <= '".$_POST['to_date']."' AND is_delete = 0 AND prev_follow_up_unique_id != 'new'  AND follow_up_action != 0  $count_where) AS updated,

                        (SELECT COUNT(unique_id) FROM follow_ups WHERE  entry_date >= '".$_POST['from_date']."' AND entry_date <= '".$_POST['to_date']."' AND is_delete = 0 AND follow_up_action = 0 AND call_type_id !='' $count_where ) AS closed
                        ) AS count_table";

        $count_result   = $pdo->query($count_sql);

        if ($count_result->status) {

            $count_array      = $count_result->data;

        } else {
        // List Display Count 

            $count_array  = [
                0 => [
                "new"     => 0,
                "pending" => 0,
                "updated" => 0,
                "closed"  => 0
                ]
            ];
        }

        $json_array = [
        "count"             => $count_array,
        // "pending_status"    => $started_pending_status,
        // "url"			    => $update_url,
        // "testing"			=> $result,
        ];

        echo json_encode($json_array);
        break;

    case 'business_forecast':

        $result_arr     = [];

        // print_r($_SESSION);

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

        $where             = " AND fu.entry_date >= '".$from_date."' AND fu.entry_date <= '".$to_date."' ";
        $commit_where      = " AND month_year >= '".$from_date_bf."' AND month_year <= '".$to_date_bf."' ";

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

                    $where          .= " AND fu.sess_user_id IN (".$team_members.")";
                    $commit_where   .= " AND staff_id IN (".$team_members_staff_ids.")";

                } else {
                    $where          .= " AND fu.sess_user_id ='".$_SESSION['user_id']."' ";
                    $commit_where   .= " AND staff_id ='".$_SESSION['staff_id']."' ";
                }
                
            } else {
                print_r($user_result);
            }           
        } 

        $sql_query  = "SELECT (SELECT IFNULL(SUM(lead),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed, IFNULL(SUM(lpd.total),0.00) AS achieved FROM lead_product_details AS lpd LEFT JOIN follow_ups AS fu ON lpd.follow_up_unique_id = fu.unique_id WHERE fu.is_delete = 0 $where
        UNION ALL
        SELECT (SELECT IFNULL(SUM(funnel_upside),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,IFNULL(SUM(fpd.total),0.00) AS achieved FROM funnel_product_details AS fpd LEFT JOIN follow_ups AS fu ON fpd.follow_up_unique_id = fu.unique_id WHERE fu.is_delete = 0 AND fu.funnel_type = 1 $where
        UNION ALL
        SELECT (SELECT IFNULL(SUM(funnel_commit),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,IFNULL(SUM(fpd.total),0.00) AS achieved FROM funnel_product_details AS fpd LEFT JOIN follow_ups AS fu ON fpd.follow_up_unique_id = fu.unique_id WHERE fu.is_delete = 0 AND fu.funnel_type = 2 $where
        UNION ALL
        SELECT (SELECT IFNULL(SUM(purchase_order),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,0 AS achieved
        UNION ALL
        SELECT (SELECT IFNULL(SUM(billing),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,0 AS achieved
        UNION ALL
        SELECT (SELECT IFNULL(SUM(payment),0.00) AS committed FROM business_forecast_target WHERE is_delete = 0 $commit_where) AS committed,0 AS achieved";

        $result = $pdo->query($sql_query);

        // if ($result->status) {
        //     $result['msg'] = "success";
        // } else {
        //     $result['msg'] = "error";
        // }

        foreach ($result->data as $data_key => $data_value) {
            $data_committed = $data_value['committed'];
            $data_achieved  = $data_value['achieved'];
            if ((float)$data_committed) {                
                $data_percentage = round(($data_achieved / $data_committed) * 100);
            } else {
                $data_percentage = 0;
            }

            // progress bar background
            $progess_bar_background = progess_bar($data_percentage);
            $progess_bar_background = $progess_bar_background['class'];

            $result->data[$data_key]['committed']   = moneyFormatIndia($data_value['committed']);
            $result->data[$data_key]['achieved']    = moneyFormatIndia($data_value['achieved']);
            $result->data[$data_key]['percentage']  = $data_percentage;
            $result->data[$data_key]['class']       = $progess_bar_background;
        }

        $result->data["sql"] = $sql_query;
        echo json_encode($result);
        break;
}



?>