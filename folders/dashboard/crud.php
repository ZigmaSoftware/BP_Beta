<?php
// Get folder Name From Currnent Url 
$folder_name        = explode("/", $_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name) - 2];
error_reporting(0);
// Database Country Table Name
$table             = "user_screen_actions";
$table_attendances = "daily_attendance";
// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';


// Variables Declaration
$action             = $_REQUEST['action'];
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

        $dashboard_month    = $_REQUEST['dashboard_month'];

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
                                            DATE_FORMAT(entry_date,'%Y-%m')='" . $_REQUEST['dashboard_month'] . "'
                                                AND is_active = 1 AND is_delete = 0 AND prev_follow_up_unique_id = 'new'
                                        ) AS new_calls,
                                        (
                                        SELECT
                                            COUNT(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                        
                                          DATE_FORMAT(next_follow_up_date,'%Y-%m') <='" . $_REQUEST['dashboard_month'] . "' AND is_active = 1 AND is_delete = 0 AND is_updated = 0
                                    ) AS follow_ups,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            DATE_FORMAT(updated_date,'%Y-%m')='" . $_REQUEST['dashboard_month'] . "' AND is_active = 1 AND is_delete = 0 AND is_updated = 1
                                    ) AS updated,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            follow_up_call_sublist
                                        WHERE
                                            DATE_FORMAT(entry_date,'%Y-%m')='" . $_REQUEST['dashboard_month'] . "' AND is_active = 1 AND is_delete = 0 AND is_updated = 2
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

        $dashboard_month    = $_REQUEST['dashboard_month'];

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
                                            DATE_FORMAT(entry_date,'%Y-%m')='" . $_REQUEST['dashboard_month'] . "'
                                                AND is_active = 1 AND is_delete = 0 AND prev_lead_unique_id = 'new'
                                        ) AS new_calls,
                                        (
                                        SELECT
                                            COUNT(unique_id)
                                        FROM
                                            leads_sublist
                                        WHERE
                                        
                                          DATE_FORMAT(next_follow_up_date,'%Y-%m') <='" . $_REQUEST['dashboard_month'] . "' AND is_active = 1 AND is_delete = 0 AND is_updated = 0
                                    ) AS follow_ups,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            leads_sublist
                                        WHERE
                                            DATE_FORMAT(updated_date,'%Y-%m')='" . $_REQUEST['dashboard_month'] . "' AND is_active = 1 AND is_delete = 0 AND is_updated = 1
                                    ) AS updated,
                                    (
                                        SELECT
                                            count(unique_id)
                                        FROM
                                            leads_sublist
                                        WHERE
                                            DATE_FORMAT(entry_date,'%Y-%m')='" . $_REQUEST['dashboard_month'] . "' AND is_active = 1 AND is_delete = 0 AND is_updated = 2
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
        $dashboard_month    = $_REQUEST['dashboard_month'];

        break;

    case 'dashboard_count':

        $count_where = '';

        $pending_count_where = " ((next_follow_up_date <= '" . $_REQUEST['to_date'] . "' AND is_delete = 0 AND  is_updated = 0 AND follow_up_action != 0) OR (prev_follow_up_unique_id !='new' AND call_stage = 0";

        if ($_SESSION['sess_user_type'] != $admin_user_type) {

            $under_user             = user_hierarchy($_SESSION['user_id'], $_SESSION['sess_user_type']);

            $curr_under_users       = '"' . implode('","', explode(",", $under_user['under_user'])) . '"';
            $curr_under_users       = $under_user['under_user'];
            $curr_under_user_types  = $under_user['under_user_type'];

            // $where                  .= " AND sess_user_id IN (".$curr_under_users.")";
            $count_where            = " AND sess_user_id IN (" . $curr_under_users . ")";
        }

        if ($executive_name) {
            // $where              .= " AND sess_user_id = '".$executive_name."' ";
            $count_where        .= " AND sess_user_id = '" . $executive_name . "' ";
        }

        $count_sql = "SELECT * FROM
                        ( SELECT 
                        (SELECT COUNT(unique_id) FROM follow_ups WHERE  prev_follow_up_unique_id = 'new' AND call_date >= '" . $_REQUEST['from_date'] . "' AND call_date <= '" . $_REQUEST['to_date'] . "' AND is_delete = 0 $count_where) AS new,

                        (SELECT COUNT(unique_id) FROM follow_ups WHERE $pending_count_where $count_where) AS pending,

                        (SELECT COUNT(unique_id) FROM follow_ups WHERE  entry_date >= '" . $_REQUEST['from_date'] . "' AND entry_date <= '" . $_REQUEST['to_date'] . "' AND is_delete = 0 AND prev_follow_up_unique_id != 'new'  AND follow_up_action != 0  $count_where) AS updated,

                        (SELECT COUNT(unique_id) FROM follow_ups WHERE  entry_date >= '" . $_REQUEST['from_date'] . "' AND entry_date <= '" . $_REQUEST['to_date'] . "' AND is_delete = 0 AND follow_up_action = 0 AND call_type_id !='' $count_where ) AS closed
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

        if ($_REQUEST['type'] == 2) {
            $dates  = year_quarter_month('quarter', $_REQUEST['quarter']);
        } else if ($_REQUEST['type'] == 3) {
            $dates  = year_quarter_month('month', $_REQUEST['month']);
        } else {
            $dates  = year_quarter_month();
        }

        $from_date  = $dates['from_date'];
        $to_date    = $dates['to_date'];

        $from_date_bf  = date('Y-m', strtotime($from_date));
        $to_date_bf    = date('Y-m', strtotime($to_date));

        $where             = " AND vfp.entry_date >= '" . $from_date . "' AND vfp.entry_date <= '" . $to_date . "' ";
        $commit_where      = " AND business_forecast_target.is_delete = 0  AND business_forecast_target.month_year >= '" . $from_date_bf . "' AND business_forecast_target.month_year <= '" . $to_date_bf . "' ";

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

            $user_table_where   = " is_delete = 0 AND unique_id = '" . $_SESSION['user_id'] . "' ";

            $user_result = $pdo->select($user_table_details, $user_table_where);

            if ($user_result->status) {
                $user_data = $user_result->data[0];

                // $staff_name   = $user_data['staff_name'];
                $is_team_head = $user_data['is_team_head'];
                $team_members = $user_data['team_members'];

                if ($is_team_head) {
                    $team_members = explode(",", $team_members);

                    $team_members = "'" . implode("','", $team_members) . "','" . $_SESSION['user_id'] . "'";

                    // GET Team Members Staff_ids Business Forecast Table Using Staff ID is mapping 

                    // That is why we are here select staff ids from users table using user ids

                    $column_user       = [
                        "staff_unique_id"
                    ];

                    $table_user_details = [
                        "user",
                        $column_user
                    ];

                    $table_user_where   = " unique_id IN (" . $team_members . ")";

                    $table_user_result  = $pdo->select($table_user_details, $table_user_where);

                    if ($table_user_result->status) {
                        $team_members_staff_ids = $table_user_result->data;

                        $temp_arr_var = [];
                        foreach ($team_members_staff_ids as $ids_key => $ids_value) {
                            $temp_arr_var[] = $ids_value['staff_unique_id'];
                        }
                        $team_members_staff_ids = $temp_arr_var;
                        $team_members_staff_ids = "'" . implode("','", $team_members_staff_ids) . "'";
                    } else {
                        print_r($table_user_result);
                        $team_members_staff_ids = '';
                    }

                    $where          .= " AND vfp.user_id IN (" . $team_members . ")";
                    $commit_where   .= " AND business_forecast_target.staff_id IN (" . $team_members_staff_ids . ")";
                } else {
                    $where          .= " AND vfp.user_id ='" . $_SESSION['user_id'] . "' ";
                    $commit_where   .= " AND business_forecast_target.staff_id ='" . $_SESSION['staff_id'] . "' ";
                }
            } else {
                print_r($user_result);
            }
        }

        // User Wise Control End

        // SQL Query to Get Current business Forecast Data
        $sql_query               = "SELECT fc.forecast,'' AS progress,(SELECT IFNULL(SUM(target),0.00) AS target    FROM business_forecast_target WHERE business_forecast_target.business_forecast=fc.unique_id $commit_where) AS target,IFNULL(SUM(vfp.total),0.00) AS achieved,fc.unique_id FROM forecast AS fc LEFT JOIN view_followup_product AS vfp ON fc.unique_id = vfp.business_forecast  $where WHERE fc.is_delete = 0 AND fc.is_active = 1  GROUP BY fc.unique_id,vfp.business_forecast";

        $result = $pdo->query($sql_query);

        $total_target   = 0.00;
        $total_achieved = 0.00;

        foreach ($result->data as $data_key => $data_value) {
            $data_target = $data_value['target'];
            $data_achieved  = $data_value['achieved'];
            if ((float)$data_target) {
                $data_percentage = round(($data_achieved / $data_target) * 100);
            } else {
                $data_percentage = 0;
            }

            // progress bar background
            $progess_bar_background = progess_bar($data_percentage);
            $progess_bar_background = $progess_bar_background['class'];

            $percentage_div = '<div class="progress mb-0">
                <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated ' . $progess_bar_background . '" role="progressbar" aria-valuenow="' . $data_percentage . '" aria-valuemin="0" aria-valuemax="100" style="width: ' . $data_percentage . '%">
                </div>
            </div>
            <div>
                ' . $data_percentage . '%
            </div>';

            $total_target   += $data_value['target'];
            $total_achieved += $data_value['achieved'];

            $onclick_fun = 'onclick="business_forecast_modal(\'' . $result->data[$data_key]['unique_id'] . '\',\'' . moneyFormatIndia($data_value['target']) . '\',\'' . moneyFormatIndia($data_value['achieved']) . '\',\'' . ucfirst($result->data[$data_key]['forecast']) . '\')"';

            $result->data[$data_key]['forecast']  = '<td class="font-weight-bold" ' . $onclick_fun . '>' . $data_value['forecast'] . '</td>';
            $result->data[$data_key]['target']    = '<td class="text-right"  ' . $onclick_fun . '>' . moneyFormatIndia($data_value['target']) . '</td>';
            $result->data[$data_key]['achieved']  = '<td class="text-right"  ' . $onclick_fun . '>' . moneyFormatIndia($data_value['achieved']) . '</td>';
            $result->data[$data_key]['progress']  = '<td class="text-center">' . $percentage_div . '</td>';
            unset($result->data[$data_key]['unique_id']);
        }

        $result->data["sql"] = $sql_query;
        $table_data     = "";

        $table_data_arr = (array) $result->data;

        foreach ($table_data_arr as $td_key => $td_value) {

            $td_value_temp  = array_values((array)$td_value);

            $table_data     .= '<tr>';
            $table_data     .= implode('', $td_value_temp);
            // $table_data     .= $td_value['forecast'];
            // $table_data     .= $td_value['progress'];
            // $table_data     .= $td_value['target'];
            // $table_data     .= $td_value['achieved'];
            $table_data     .= '</tr>';
        }

        $table_data     .= "<tr class = 'font-weight-bold'>";
        $table_data     .= "<td>Total</td>";
        $table_data     .= "<td></td>";
        $table_data     .= "<td class='text-right'>" . moneyFormatIndia($total_target) . "</td>";
        $table_data     .= "<td class='text-right'>" . moneyFormatIndia($total_achieved) . "</td>";
        $table_data     .= "</tr>";
        echo $table_data;

        break;

    case 'device_check':

        $staff_id  = $_REQUEST['staff_id'];
        $device_id  = $_REQUEST['device_id'];

        $table_name    = "user";
        $table_details      = [
            $table_name,
            [
                "COUNT(unique_id) AS count"
            ]
        ];


        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "device_id" => $device_id,
            "staff_unique_id" => $staff_id,
        ];

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];

        $json_array = [

            "data"              => $office_in['count'],

        ];

        echo json_encode($json_array);
        break;

    case 'break_in_out_validation':

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "attendance_type"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        // $where     = [
        //     "is_active" => 1,
        //     "is_delete" => 0,
        //     "attendance_type" => 1,
        //     "staff_id" => $staff_id,
        //     "entry_date" => date('Y-m-d')
        // ];
        $where = " is_active=1 and is_delete=0 and staff_id='" . $staff_id . "' and entry_date='" . date('Y-m-d') . "' order by id DESC limit 1";

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        $ofc_in = $office_in['attendance_type'];

        $json_array = [

            "data"              => $ofc_in,

        ];

        echo json_encode($json_array);
        break;

    case 'device_id_control':


        $table_name    = "device_id_control";
        $where         = [];
        $table_columns = [
            "id",
            "mobile_no"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [];

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        $ofc_in = $office_in['mobile_no'];

        $json_array = [

            "data"              => $ofc_in,

        ];

        echo json_encode($json_array);
        break;

    case 'office_in':

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "entry_time"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "attendance_type" => 1,
            "staff_id" => $staff_id,
            "entry_date" => date('Y-m-d')
        ];

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        $ofc_in = date('d-m-Y') . ' ( ' . date('h:i A', strtotime($office_in['entry_time'])) . ' )';
        if ($office_in['entry_time']) {
            $ofc_in = date('d-m-Y') . ' ( ' . date('h:i A', strtotime($office_in['entry_time'])) . ' )';
        } else {
            $ofc_in = '-';
        }
        $json_array = [

            "data"              => $ofc_in,

        ];

        echo json_encode($json_array);
        break;

    case 'office_out':

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "entry_time"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "attendance_type" => 2,
            "staff_id" => $staff_id,
            "entry_date" => date('Y-m-d')
        ];

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        if ($office_in['entry_time']) {
            $ofc_in = date('d-m-Y') . ' ( ' . date('h:i A', strtotime($office_in['entry_time'])) . ' )';
        } else {
            $ofc_in = '-';
        }
        $json_array = [

            "data"              => $ofc_in,

        ];

        echo json_encode($json_array);
        break;

    case 'break_in':

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "entry_time"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "attendance_type" => 3,
            "staff_id" => $staff_id,
            "entry_date" => date('Y-m-d')
        ];

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        $ofc_in = date('d-m-Y') . ' ( ' . date('h:i A', strtotime($office_in['entry_time'])) . ' )';
        if ($office_in['entry_time']) {
            $ofc_in = date('d-m-Y') . ' ( ' . date('h:i A', strtotime($office_in['entry_time'])) . ' )';
        } else {
            $ofc_in = '-';
        }
        $json_array = [

            "data"              => $ofc_in,

        ];

        echo json_encode($json_array);
        break;

    case 'break_out':

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "entry_time"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "attendance_type" => 4,
            "staff_id" => $staff_id,
            "entry_date" => date('Y-m-d')
        ];

        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        if ($office_in['entry_time']) {
            $ofc_in = date('d-m-Y') . ' ( ' . date('h:i A', strtotime($office_in['entry_time'])) . ' )';
        } else {
            $ofc_in = '-';
        }
        $json_array = [

            "data"              => $ofc_in,

        ];

        echo json_encode($json_array);
        break;

    case 'monthly_report':

        $search     = $_REQUEST['search']['value'];
        $length     = $_REQUEST['length'];
        $start      = $_REQUEST['start'];
        $draw       = $_REQUEST['draw'];
        $limit      = $length;

        $data       = [];
        $where_arr  = [];
        $total      = 0;

        $staff_id  = $_REQUEST['staff_id'];

        if ($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "'' as entry_date",
            "'' as day_status",
            "'' as check_in_time",
            "'' as check_out_time"
        ];
        $table_name_report    = "view_staff_attendance_report";
        $table_details  = [
            $table_name_report,
            $columns
        ];

        if ($_REQUEST['year_month'] == '') {
            $_REQUEST['year_month'] =   date('Y-m');
        }

        if ($_REQUEST['staff_id'] != '') {
            $executive_name = "staff_id = '" . $_REQUEST['staff_id'] . "' AND ";
        } else {
            $executive_name = "";
        }

        $where  = $executive_name . 'entry_date like "%' . $_REQUEST['year_month'] . '%" ';

        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        $total_records  = total_records();


        if ($result->status) {
            $month_explode  = explode('-', $_REQUEST['year_month']);

            $year  = $month_explode[0];
            $month = $month_explode[1];

            $current_month   = date('Y-m');
            if ($_REQUEST['year_month'] == $current_month) {
                $total_days     = date('d');
                $day_count      = $total_days - 1;
            } else {
                $total_days     = cal_days_in_month(CAL_GREGORIAN, $month, $year);
                $day_count      = $total_days;
            }

            $total_count_days     = cal_days_in_month(CAL_GREGORIAN, $month, $year);


            $res_array      = $result->data;

            for ($d = 1; $d <= $total_days; $d++) {

                if ($d < 10) {
                    $date_month = "0" . $d;
                    $entry_date = $_REQUEST['year_month'] . "-" . $date_month;
                } else {
                    $date_month = $d;
                    $entry_date = $_REQUEST['year_month'] . "-" . $date_month;
                }

                $res_array[0]['entry_date']  = disdate($_REQUEST['year_month'] . "-" . $date_month);

                $date                        = $_REQUEST['year_month'] . "-" . $date_month;
                $staff_id                    = $_REQUEST['staff_id'];
                $staff                       = staff_name($_REQUEST['staff_id']);
                $staff_name                  = disname(($staff[0]['staff_name']));
                $res_array[0]['staff_id']    = $staff_name;


                $check_holiday          = get_holiday_date($entry_date);
                $check_sunday           = get_sunday_date($entry_date, $date_month);

                $day_status             = get_day_status($staff_id, $entry_date);
                $leave                  = get_leave_status($staff_id, $entry_date);
                $emer_leave             = get_emer_leave_status($staff_id, $entry_date);

                switch ($day_status) {
                    case 1:
                        $res_array[0]['day_status']  = "<span class='text-success font-weight-bold'>Present</span>";
                        break;
                    case 2:
                        $res_array[0]['day_status']  = "<span class='text-warning font-weight-bold'>Late</span>";
                        break;
                    case 3:
                        $res_array[0]['day_status']  = "<span class='text-warning font-weight-bold'>Permission</span>";
                        break;
                    case 4:
                        $res_array[0]['day_status']  = "<span class='text-warning font-weight-bold'>Half Day</span>";
                        break;
                    default:
                        $res_array[0]['day_status']  = "<span class='text-danger font-weight-bold'>Absent</span>";
                        break;
                }

                $check_in                    = get_check_in_time($staff_id, $entry_date);

                if ($check_in) {
                    $check_in_time_val       = date_create($check_in);
                    $check_in_time           = date_format($check_in_time_val, "H:i a");
                } else {
                    $check_in_time           = "-";
                    $res_array[0]['day_status']  = $check_sunday;
                }
                $res_array[0]['check_in_time']    = $check_in_time;

                $check_out                    = get_check_out_time($staff_id, $entry_date);
                if ($check_out) {
                    $check_out_time_val       = date_create($check_out);
                    $check_out_time           = date_format($check_out_time_val, "H:i a");
                } else {
                    $check_out_time           = "-";
                }
                $res_array[0]['check_out_time']    = $check_out_time;

                $time1 = new DateTime($check_in);
                $time2 = new DateTime($check_out);
                $timediff = $time1->diff($time2);


                if (($check_out_time == '-') || ($check_out_time == '')) {
                    if ($date != $today) {
                        $res_array[0]['day_status'] = "<span class='text-danger font-weight-bold'>Absent</span>";
                    }
                }

                if ($check_holiday) {
                    $res_array[0]['day_status'] = "<span class='font-weight-bold' style='color :blue'>Holiday</span>";
                }

                if ($leave) {
                    $res_array[0]['day_status'] = "<span class='font-weight-bold' style='color :#099be4'>Leave</span>";
                }

                if ($emer_leave) {
                    $res_array[0]['day_status'] = "<span class='font-weight-bold' style='color :#e46409'>Emergency Leave</span>";
                }

                if ($check_in_time == '-') {
                    if ($check_sunday) {
                        $res_array[0]['day_status'] = $check_sunday;
                    }
                }

                // if($check_out_time!='')
                // {
                //     $check_out_time = date('h:i A',strtotime($value["check_out_time"]));
                // }    
                // else
                // {
                //     $check_out_time = '-';
                // }     
                $ofc_in = '<tr>';
                $ofc_in .= '<td style="font-size:12px;">' . $res_array[0]["entry_date"] . '</td>';
                $ofc_in .= '<td style="font-size:12px;">' . $res_array[0]['day_status'] . '</td>';
                $ofc_in .= '<td style="font-size:12px;">' . $check_in_time . '</td>';
                $ofc_in .= '<td style="font-size:12px;text-align:center;">' . $check_out_time . '</td>';
                $ofc_in .= '</tr>';

                $data[]         = $ofc_in;
            }

            $month_explode  = explode('-', $_REQUEST['year_month']);

            $year  = $month_explode[0];
            $month = $month_explode[1];

            $current_month_days   = cal_days_in_month(CAL_GREGORIAN, $month, $year);

            $json_array = [

                "data"              => $data,

            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;

    case 'leave_date_details':

        $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "leave_details";
        $where         = [];
        $table_columns = [
            "unique_id",
            "from_date",
            "to_date",
            "leave_days",
            "half_day_type",
            "from_time",
            "to_time",
            "permission_hours",
            "day_type"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where = " staff_id='" . $staff_id . "' and MONTH(from_date) = MONTH(now()) AND YEAR(from_date) = YEAR(now())";

        $result = $pdo->select($table_details, $where);
        $data        = [];
        $res_array      = $result->data;
        foreach ($res_array as $value) {
            if ($value['day_type']) {
                if ($value['day_type'] == '1') {
                    $day_type = 'Full Day';
                }
                if ($value['day_type'] == '2') {
                    $day_type = 'Half Day';
                }
                if ($value['day_type'] == '3') {
                    $day_type = 'Work From Home';
                }
                if ($value['day_type'] == '4') {
                    $day_type = 'Idle';
                }
                if ($value['day_type'] == '5') {
                    $day_type = 'On Duty';
                }
                if ($value['day_type'] == '6') {
                    $day_type = 'Permission';
                }

                if ($value['half_day_type'] == '1') {
                    $hlf_day_type = 'Forenoon';
                }
                if ($value['half_day_type'] == '2') {
                    $hlf_day_type = 'Afternoon';
                }

                if ($value['day_type'] == '1' || $value['day_type'] == '3' || $value['day_type'] == '4') {
                    $ofc_in = "<p>" . $day_type . ' ( ' . date("d-m-Y", strtotime($value['from_date'])) . ' to ' . date("d-m-Y", strtotime($value['to_date'])) . ' - ' . $value['leave_days'] . " Days )</p>";
                }
                if ($value['day_type'] == '2' || $value['day_type'] == '5') {
                    $ofc_in = "<p>" . $day_type . ' ( ' . date("d-m-Y", strtotime($value['from_date'])) . ' - ' . $hlf_day_type . " )</p>";
                }
                if ($value['day_type'] == '6') {
                    $ofc_in = "<p>" . $day_type . ' ( ' . date("h:i A", strtotime($value['from_time'])) . ' to ' . date("h:i A", strtotime($value['to_time'])) . ' - ' . date("h:i", strtotime($value['permission_hours'])) . " Hours )</p>";
                }

                //$ofc_in = "<p>".date("d-m-Y",strtotime($value['from_date'])).' - '.$day_type."</p>";
                $data[] = $ofc_in;
            } else {
                $data[] = '-';
            }
        }
        $json_array = [

            "data"              => $data,

        ];

        echo json_encode($json_array);
        break;


    case 'holiday_details':

        // $staff_id  = $_REQUEST['staff_id'];

        $table_name    = "attendance_holidays";
        $where         = [];
        $table_columns = [
            "unique_id",
            "holiday_date",
            "remarks"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where = "  MONTH(holiday_date) = MONTH(now()) AND YEAR(holiday_date) = YEAR(now())";

        $result = $pdo->select($table_details, $where);
        $data        = [];
        $res_array      = $result->data;
        foreach ($res_array as $value) {
            if ($value['holiday_date']) {

                $ofc_in = "<p>" . date("d-m-Y", strtotime($value['holiday_date'])) . ' - ' . $value['remarks'] . "</p>";
                $data[] = $ofc_in;
            } else {
                $data[] = '-';
            }
        }
        $json_array = [

            "data"              => $data,

        ];

        echo json_encode($json_array);
        break;


    case 'working_days':

        $staff_id  = $_REQUEST['staff_id'];

        $month = date('m');
        $year  = date('Y');
        $date  = date('d');
        // Query Variables
        $json_array     = "";
        $current_month_days   = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $current_days         = $date - 1;
        $total_sundays        = total_sundays_days($current_days, $month, $year);

        $holiday_leave_cnt  = get_holiday_leave($month, $year);

        if ($holiday_leave_cnt) {
            $holiday_leave = $holiday_leave_cnt;
        } else {
            $holiday_leave = 0;
        }

        $working_days         = $current_days - $total_sundays - $holiday_leave_cnt;
        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "day_status"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [
            "staff_id" => $staff_id,
        ];
        $entry_time1          = date('H:i');
        $staff_name_list = $pdo->select($table_details, $where);
        $json_array = [

            "data"              => $working_days,

        ];

        echo json_encode($json_array);
        break;

    case 'attendance_details':
        $staff_id  = $_REQUEST['staff_id'];

        $month = date('m');
        $year  = date('Y');
        $date  = date('d');
        // Query Variables
        $json_array     = "";

        $no_of_late_cnt         = get_late_count($month, $year, $staff_id);

        if ($no_of_late_cnt) {
            $no_of_late = $no_of_late_cnt;
        } else {
            $no_of_late = 0;
        }

        $no_of_permission_cnt    = get_permission_count($month, $year, $staff_id);
        if ($no_of_permission_cnt) {
            $no_of_permission = $no_of_permission_cnt;
        } else {
            $no_of_permission = 0;
        }

        $no_of_check_out_cnt    = get_check_out_count($month, $year, $staff_id);
        if ($no_of_check_out_cnt) {
            $no_of_check_out = $no_of_check_out_cnt;
        } else {
            $no_of_check_out = 0;
        }

        $json_array = [

            "no_of_permission"              => $no_of_permission,
            "no_of_late"                    => $no_of_late,
            "no_of_check_out"               => $no_of_check_out,


        ];

        echo json_encode($json_array);
        break;

    case 'worked_days':

        $staff_id  = $_REQUEST['staff_id'];

        $month = date('m');
        $year  = date('Y');
        $date  = date('d');
        // Query Variables
        $json_array     = "";

        $current_month_days   = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        $current_days        = $date - 1;
        $total_sundays        = total_sundays($month, $year);
        $holiday_leave_cnt    = get_holiday_leave($month, $year);

        if ($holiday_leave_cnt) {
            $holiday_leave = $holiday_leave_cnt;
        } else {
            $holiday_leave = 0;
        }
        $working_days         = $current_days - $total_sundays -  $holiday_leave;

        $full_day_leave_cnt    = get_full_day_leave($month, $year, $staff_id);

        if ($full_day_leave_cnt) {
            $full_day_leave = $full_day_leave_cnt;
        } else {
            $full_day_leave = 0;
        }

        $cl_day_leave_cnt    = get_cl_day_leave($month, $year, $staff_id);

        if ($cl_day_leave_cnt) {
            $cl_day_leave = $cl_day_leave_cnt;
        } else {
            $cl_day_leave = 0;
        }

        $count_half_day       = get_half_present_count($month, $year, $staff_id);
        if ($count_half_day) {
            $half_day_present = $count_half_day;
        } else {
            $half_day_present = 0;
        }

        $half_day_leave_cnt     = get_half_day_leave($month, $year, $staff_id);

        if ($half_day_leave_cnt) {
            $half_day_leave = $half_day_leave_cnt;
        } else {
            $half_day_leave = 0;
        }

        $no_of_late_cnt         = get_late_count($month, $year, $staff_id);

        if ($no_of_late_cnt) {
            $no_of_late = $no_of_late_cnt;
        } else {
            $no_of_late = 0;
        }

        $no_of_permission_cnt    = get_permission_count($month, $year, $staff_id);
        if ($no_of_permission_cnt) {
            $no_of_permission = $no_of_permission_cnt;
        } else {
            $no_of_permission = 0;
        }

        // calculation for total working days
        if ($no_of_late_cnt) {
            if ($no_of_late_cnt > 3) {
                $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
            } else {
                $no_of_late_tot_cnt = 0;
            }
        } else {
            $no_of_late_tot_cnt = 0;
        }
        // calculation for per total working days
        if ($no_of_permission_cnt) {
            if ($no_of_late_tot_cnt > 2) {
                $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2) / 2);
            } else {
                $no_of_permission_tot_cnt = 0;
            }
        } else {
            $no_of_permission_tot_cnt = 0;
        }


        $absent_count_cnt         = get_absent_count($month, $year, $staff_id);

        if ($absent_count_cnt != 0) {
            $absent_count = $absent_count_cnt;
        } else {
            $absent_count = 0;
        }

        if ($half_day_leave_cnt) {
            $half_day_leave = $half_day_leave_cnt;
        } else {
            $half_day_leave = 0;
        }


        $no_of_leave          = $full_day_leave + $half_day_leave + $cl_day_leave_cnt;

        $no_of_absent         = $working_days - $absent_count - $no_of_leave;

        $total_worked_days    =   $absent_count - $half_day_present;

        $json_array = [

            "data"              => $total_worked_days,

        ];

        echo json_encode($json_array);
        break;

    case 'leave_days':

        $staff_id  = $_REQUEST['staff_id'];

        $date  = date('d');
        $month = date('m');
        $year  = date('Y');
        // Query Variables
        $json_array     = "";

        $current_days         = $date - 1;

        $total_sundays        = total_sundays_days($current_days, $month, $year);



        $full_day_leave_cnt    = get_full_day_leave($month, $year, $staff_id);

        if ($full_day_leave_cnt) {
            $full_day_leave = $full_day_leave_cnt;
        } else {
            $full_day_leave = 0;
        }

        $cl_day_leave_cnt    = get_cl_day_leave($month, $year, $staff_id);

        if ($cl_day_leave_cnt) {
            $cl_day_leave = $cl_day_leave_cnt;
        } else {
            $cl_day_leave = 0;
        }

        $half_day_leave_cnt     = get_half_day_leave($month, $year, $staff_id);

        if ($half_day_leave_cnt) {
            $half_day_leave = $half_day_leave_cnt;
        } else {
            $half_day_leave = 0;
        }

        $holiday_leave_cnt  = get_holiday_leave($month, $year);

        if ($holiday_leave_cnt) {
            $holiday_leave = $holiday_leave_cnt;
        } else {
            $holiday_leave = 0;
        }

        $no_of_leave          = $full_day_leave + $half_day_leave;

        //     $no_of_late_cnt         = get_late_count($month,$year,$staff_id);

        // if($no_of_late_cnt){
        //     $no_of_late = $no_of_late_cnt;
        // }else{
        $no_of_late = 0;
        // }

        // $no_of_permission_cnt    = get_permission_count($month,$year,$staff_id);
        // if($no_of_permission_cnt){
        //     $no_of_permission = $no_of_permission_cnt;
        // }else{
        $no_of_permission = 0;
        // }

        // // calculation for total working days
        // if($no_of_late_cnt){
        //     if($no_of_late_cnt > 3){
        //         $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
        //     } else{
        //         $no_of_late_tot_cnt = 0;
        //     }
        // }else {
        $no_of_late_tot_cnt = 0;
        // }
        // // calculation for per total working days
        // if($no_of_permission_cnt){
        //     if($no_of_late_tot_cnt > 2){
        //         $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2)/2);
        //     } else{
        //         $no_of_permission_tot_cnt = 0;
        //     }
        // }else {
        $no_of_permission_tot_cnt = 0;
        // }



        $absent_count_cnt         = get_absent_count_leave($month, $year, $staff_id);

        if ($absent_count_cnt != 0) {
            $absent_count = $absent_count_cnt;
        } else {
            $absent_count = 0;
        }

        if ($half_day_leave_cnt) {
            $half_day_leave = $half_day_leave_cnt;
        } else {
            $half_day_leave = 0;
        }

        $working_days         = $current_days - $total_sundays  - $holiday_leave;


        $no_of_leave          = $full_day_leave + $half_day_leave + $cl_day_leave_cnt +  $no_of_permission_tot_cnt;

        $no_of_absent         =    $no_of_leave;

        $total_worked_days    = $absent_count;
        $absent_days          = $working_days -  $total_worked_days;
        $leave_days           = $no_of_leave;

        $json_array = [

            "data"              =>  $leave_days,

        ];

        echo json_encode($json_array);
        break;

    case 'absent_days':

        $staff_id  = $_REQUEST['staff_id'];

        $date  = date('d');
        $month = date('m');
        $year  = date('Y');
        // Query Variables
        $json_array     = "";

        $current_days         = $date - 1;

        $total_sundays        = total_sundays_days($current_days, $month, $year);



        $full_day_leave_cnt    = get_full_day_leave($month, $year, $staff_id);

        if ($full_day_leave_cnt) {
            $full_day_leave = $full_day_leave_cnt;
        } else {
            $full_day_leave = 0;
        }

        $cl_day_leave_cnt    = get_cl_day_leave($month, $year, $staff_id);

        if ($cl_day_leave_cnt) {
            $cl_day_leave = $cl_day_leave_cnt;
        } else {
            $cl_day_leave = 0;
        }

        $half_day_leave_cnt     = get_half_day_leave($month, $year, $staff_id);

        if ($half_day_leave_cnt) {
            $half_day_leave = $half_day_leave_cnt;
        } else {
            $half_day_leave = 0;
        }

        $holiday_leave_cnt  = get_holiday_leave($month, $year);

        if ($holiday_leave_cnt) {
            $holiday_leave = $holiday_leave_cnt;
        } else {
            $holiday_leave = 0;
        }

        $no_of_leave          = $full_day_leave + $half_day_leave;

        //     $no_of_late_cnt         = get_late_count($month,$year,$staff_id);

        // if($no_of_late_cnt){
        //     $no_of_late = $no_of_late_cnt;
        // }else{
        $no_of_late = 0;
        // }

        // $no_of_permission_cnt    = get_permission_count($month,$year,$staff_id);
        // if($no_of_permission_cnt){
        //     $no_of_permission = $no_of_permission_cnt;
        // }else{
        $no_of_permission = 0;
        // }

        // calculation for total working days
        // if($no_of_late_cnt){
        //     if($no_of_late_cnt > 3){
        //         $no_of_late_tot_cnt = $no_of_permission_cnt + ($no_of_late_cnt - 3);
        //     } else{
        //         $no_of_late_tot_cnt = 0;
        //     }
        // }else {
        $no_of_late_tot_cnt = 0;
        // }
        // calculation for per total working days
        // if($no_of_permission_cnt){
        //     if($no_of_late_tot_cnt > 2){
        //         $no_of_permission_tot_cnt = $half_day_leave + (($no_of_late_tot_cnt - 2)/2);
        //     } else{
        //         $no_of_permission_tot_cnt = 0;
        //     }
        // }else {
        $no_of_permission_tot_cnt = 0;
        // }



        $absent_count_cnt         = get_absent_count_leave($month, $year, $staff_id);

        if ($absent_count_cnt != 0) {
            $absent_count = $absent_count_cnt;
        } else {
            $absent_count = 0;
        }

        if ($half_day_leave_cnt) {
            $half_day_leave = $half_day_leave_cnt;
        } else {
            $half_day_leave = 0;
        }

        $count_half_day       = get_half_present_count($month, $year, $staff_id);
        if ($count_half_day) {
            $half_day_present = $count_half_day;
        } else {
            $half_day_present = 0;
        }

        $working_days         = $current_days - $total_sundays  - $holiday_leave;


        $no_of_leave          = $full_day_leave + $half_day_leave + $cl_day_leave_cnt;


        $total_worked_days    =   $absent_count;
        $no_of_absent         =   (($working_days  - $total_worked_days) - $no_of_leave) + $half_day_present;

        $json_array = [

            "data"              => $no_of_absent,

        ];

        echo json_encode($json_array);
        break;

    case 'office_attendance':

        $staff_id            = $_REQUEST["staff_id"];
        $entry_date          = date('Y-m-d');
        $entry_time          = date("H:i:s");
        $latitude            = $_REQUEST["latitude"];
        $longitude           = $_REQUEST["longitude"];
        $attendance_type     = $_REQUEST["attendance_type"];
        // $day_status          = $_REQUEST["day_status"];
        $day_type            = '7';


        $table_name    = "daily_attendance";
        $where         = [];
        $table_columns = [
            "unique_id",
            "day_status"
        ];

        $table_details = [
            $table_name,
            $table_columns
        ];

        $where     = [
            "is_active" => 1,
            "is_delete" => 0,
            "attendance_type" => 1,
            "staff_id" => $staff_id,
            "entry_date" => date('Y-m-d')
        ];
        $entry_time1          = date('H:i');
        $staff_name_list = $pdo->select($table_details, $where);
        $office_in = $staff_name_list->data[0];
        if ($office_in['day_status'] == '') {
            if ($entry_time1 >= '05:00' && $entry_time1 <= '09:40') {
                $day_status =  '1';
            }
            if ($entry_time1 >= '09:41' && $entry_time1 <= '10:30') {
                $day_status =  '2';
            }
            if ($entry_time1 >= '10:31' && $entry_time1 <= '11:30') {
                $day_status =  '3';
            }
            if ($entry_time1 >= '11:31') {
                $day_status =  '4';
            }
        } else {
            $day_status =   $office_in['day_status'];
        }

        $update_where       = "";

        $columns            = [
            "staff_id"          => $staff_id,
            "entry_date"        => $entry_date,
            "entry_time"        => $entry_time,
            "latitude"          => $latitude,
            "longitude"         => $longitude,
            "attendance_type"   => $attendance_type,
            "day_status"        => $day_status,
            "day_type"          => $day_type,
            "attendance_from"   => "Android App",
            "unique_id"         => unique_id($prefix)
        ];

        // Check already Exist Or not
        $table_details      = [
            $table_attendances,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        if (($attendance_type != 2) && ($attendance_type != 3) && ($attendance_type != 4)) {

            $select_where       = 'staff_id = "' . $staff_id . '" AND entry_date = "' . $entry_date . '" AND attendance_type = "' . $attendance_type . '" AND is_delete = 0  ';

            $action_obj         = $pdo->select($table_details, $select_where);

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }

            if ($data[0]["count"]) {
                $msg        = "already";
            } else if ($data[0]["count"] == 0) {

                $action_obj     = $pdo->insert($table_attendances, $columns);


                if ($action_obj->status) {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = "";
                    $sql        = $action_obj->sql;

                    if ($unique_id) {
                        $msg        = "update";
                    } else {
                        $msg        = "create";
                    }
                } else {
                    $status     = $action_obj->status;
                    $data       = $action_obj->data;
                    $error      = $action_obj->error;
                    $sql        = $action_obj->sql;
                    $msg        = "error";
                }
            }
        } else {

            $action_obj     = $pdo->insert($table_attendances, $columns);

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "create";
                }
            } else {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = $action_obj->error;
                $sql        = $action_obj->sql;
                $msg        = "error";
            }
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

    case 'notification_today':
        $data       = [];
        $tomorrow = date("Y-m-d", time() + 86400);
        $date = date("m-d", time() + 86400);
        $today = date("m-d");



        $json_array     = "";
        $columns        = [
            "employee_id",
            "staff_name",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = staff.designation_unique_id ) AS designation_type",
            "'' as dob",
            "file_name",

        ];
        $table_details  = [
            "staff",
            $columns
        ];
        $where          = "date_of_birth like '%" . $today . "%' AND is_active = 1 and is_delete = 0";

        $result         = $pdo->select($table_details, $where);

        if ($result->status) {

            $res_array      = $result->data;


            $json_array     = "";



            $data_val =     '<div class="card">
                                    <div class="card-body" style="height:292px;overflow-y: auto;">
                                        <b><h4 style="color: #6700c5;font-weight: 600;">Today Birthday &nbsp;<i class="fa fa-1x fa-birthday-cake"></i></h4></b>
                                        <hr/>';

            $birthday_count = get_birthday_cnt($today);

            if ($birthday_count != 0) {
                foreach ($res_array as $key => $value) {
                    $dob_year = date('Y');
                    $dob = $dob_year . "-" . $today;
                    $value['dob'] = disdate($dob);


                    if ($value['file_name'] != '') {
                        $data_val .=  '<div class="row">
                                                    <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/uploads/staff/' . $value['file_name'] . '" width="100%"></div>
                                                    <div class="col-md-9">
                                                        <h4>' . $value['staff_name'] . '</h4>
                                                        <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                                        <h5 style="color: #6700c5;">' . $value['dob'] . '</h5>
                                                    </div>
                                                </div>
                                                <hr style="margin:0.6rem;">';
                    } else {
                        $data_val .=  '<div class="row">
                                                    <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/img/user.jpg" width="100%"></div>
                                                    <div class="col-md-9">
                                                        <h4>' . $value['staff_name'] . '</h4>
                                                        <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                                        <h5 style="color: #6700c5;">' . $value['dob'] . '</h5>
                                                    </div>
                                                </div>
                                               <hr style="margin:0.6rem;">';
                    }
                }
            } else {
                $data_val .= '<center><img class="cake_img" src="https://localhost/xeon/uploads/staff/cake.gif" width="100%"><h3>No Birthday Found!</h3> </center>';
            }
            $data_val .=  '</div>
                                                </div>';




            $json_array = [
                "data"              => $data_val,
            ];
        } else {
            print_r($result);
        }
        echo json_encode($json_array);

        break;

    case 'notification_upcomming':
        $data       = [];
        $tomorrow = date("Y-m-d", time() + 86400);
        $date = date("m-d", time() + 86400);
        $today = date("m-d");

        $json_array     = "";
        $columns        = [
            "employee_id",
            "staff_name",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = staff.designation_unique_id ) AS designation_type",
            "'' as dob",
            "file_name",

        ];
        $table_details  = [
            "staff",
            $columns
        ];
        $where          = "date_of_birth like '%" . $date . "%' AND is_active = 1 and is_delete = 0";

        $result         = $pdo->select($table_details, $where);

        if ($result->status) {

            $res_array      = $result->data;



            $json_array     = "";
            $data_val =     ' <div class="card">
                                    <div class="card-body" style="height:292px;overflow-y: auto;">
                                        <b><h4 style="color: #df0404;font-weight: 600;">Upcoming Birthday &nbsp;<i class="fa fa-1x fa-birthday-cake"></i></h4></b>
               
                                        <hr/>';

            $birthday_count = get_birthday_cnt($date);
            if ($birthday_count != 0) {
                foreach ($res_array as $key => $value) {
                    $dob_year = date('Y');
                    $dob = $dob_year . "-" . $date;
                    $value['dob'] = disdate($dob);

                    if ($value['file_name'] != '') {
                        $data_val .=  '<div class="row">
                                                    <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/uploads/staff/' . $value['file_name'] . '" width="100%"></div>
                                                    <div class="col-md-9">
                                                        <h4>' . $value['staff_name'] . '</h4>
                                                        <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                                        <h5 style="color: #6700c5;">' . $value['dob'] . '</h5>
                                                    </div>
                                                </div>
                                                <hr style="margin:0.6rem;">';
                    } else {
                        $data_val .=  '<div class="row">
                                                    <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/img/user.jpg" width="100%"></div>
                                                    <div class="col-md-9">
                                                        <h4>' . $value['staff_name'] . '</h4>
                                                        <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                                        <h5 style="color: #6700c5;">' . $value['dob'] . '</h5>
                                                    </div>
                                                </div>
                                               <hr style="margin:0.6rem;">';
                    }
                }
            } else {
                $data_val .= '<center><img class="cake_img" src="https://localhost/xeon/uploads/staff/cake.gif" width="100%"><h3>No Birthday Found!</h3> </center>';
            }
            $data_val .=  '</div>
                                                </div>';




            $json_array = [
                "data"              => $data_val,
            ];
        } else {
            print_r($result);
        }
        echo json_encode($json_array);

        break;


    case 'notification_user':
        $data = [];
        $tomorrow = date("Y-m-d", time() + 86400);
        $date = date("m-d", time() + 86400);
        $today = date("m-d");

        $json_array = "";
        $columns = [
            "employee_id",
            "staff_name",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = staff.designation_unique_id ) AS designation_type",
            "'' as dob",
            "file_name",
        ];
        $table_details = [
            "staff",
            $columns
        ];
        $where = "date_of_birth like '%" . $today . "%' and unique_id = '" . $_SESSION["staff_id"] . "' AND is_active = 1 and is_delete = 0";

        $result = $pdo->select($table_details, $where);

        if ($result->status) {
            $res_array = $result->data;

            $json_array = "";

            $data_val = '';

            if (!empty($res_array)) { // Check if the logged-in user has a birthday
                $data_val .= ' <div class="card">
                                <div class="card-body" style="height:292px;overflow-y: auto;">
                                    <b><h4 style="color: #df0404;font-weight: 600;">Happy Birthday &nbsp;<i class="fa fa-1x fa-birthday-cake"></i></h4></b>
                                    <hr/>';

                foreach ($res_array as $key => $value) {
                    $dob_year = date('Y');
                    $dob = $dob_year . "-" . $today;
                    $value['dob'] = disdate($dob);

                    if ($value['file_name'] != '') {
                        $data_val .= '<div class="row">
                            <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/uploads/staff/' . $value['file_name'] . '" width="100%"></div>
                            <div class="col-md-9">
                               
                                <h4>' . $value['staff_name'] . '</h4>
                                <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                <h5 style="color: #6700c5;">' . $value['dob'] . '</h5>
                                <h4> Wishing You a Happy Birthday ' . $value['staff_name'] . '</h4>
                            </div>
                        </div>
                        <hr style="margin:0.6rem;">';
                    } else {
                        $data_val .= '<div class="row">
                            <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/img/user.jpg" width="100%"></div>
                            <div class="col-md-9">
                                
                                <h4>' . $value['staff_name'] . '</h4>
                                <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                <h5 style="color: #6700c5;">' . $value['dob'] . '</h5>
                                <h4> Wishing You a Happy Birthday </h4>
                            </div>
                        </div>
                        <hr style="margin:0.6rem;">';
                    }
                }
                $data_val .= '</div></div>';
            }

            $json_array = [
                "data" => $data_val,
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;




    case 'notification_doj':
        $data       = [];
        $tomorrow = date("Y-m-d", time() + 86400);
        $date = date("m-d", time() + 86400);
        $today = date("m-d");



        $json_array     = "";
        $columns        = [
            "employee_id",
            "staff_name",
            "(SELECT designation FROM designation_creation AS designation WHERE designation.unique_id = staff.designation_unique_id ) AS designation_type",
            "date_of_join",
            "'' as doj",
            "file_name",

        ];
        $table_details  = [
            "staff",
            $columns
        ];
        $where          = "date_of_join like '%" . $today . "%' and  unique_id = '" . $_SESSION['staff_id'] . "' AND is_active = 1 and  is_delete = 0";

        $result         = $pdo->select($table_details, $where);

        if ($result->status) {

            $res_array = $result->data;

            $json_array = "";

            $data_val = '';


            if (!empty($res_array)) {
                $data_val =     '<div class="card">
                    <div class="card-body" style="height:292px;overflow-y: auto;">
                        <b><h4 style="color: #6700c5;font-weight: 600;">Anniversary &nbsp;<i class="fa fa-1x fa-birthday-cake"></i></h4></b>
                        <hr/>';

                foreach ($res_array as $key => $value) {
                    $cur_year = date('Y');
                    $date_join = date_create($value['date_of_join']);
                    $doj_year = date_format($date_join, 'Y');
                    $dob = $dob_year . "-" . $today;
                    $value['doj'] = disdate($dob);

                    $anniversary_year = $cur_year - $doj_year;

                    if ($value['file_name'] != '') {
                        $data_val .=  '<div class="row">
                            <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/uploads/staff/' . $value['file_name'] . '" width="100%"></div>
                            <div class="col-md-9">
                                <h4>' . $value['staff_name'] . '</h4>
                                <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                <h5 style="color: #6700c5;">' . $anniversary_year . ' Year of Xeon</h5>
                            </div>
                        </div>
                        <hr style="margin:0.6rem;">';
                    } else {
                        $data_val .=  '<div class="row">
                            <div class="col-md-3"><img class="round_img" src="https://localhost/xeon/img/user.jpg" width="100%"></div>
                            <div class="col-md-9">
                                <h4>' . $value['staff_name'] . '</h4>
                                <h5 style="color: #747474;">' . $value['employee_id'] . '  |  ' . $value['designation_type'] . ' </h5>
                                <h5 style="color: #6700c5;">' . $anniversary_year . ' Year of Xeon</h5>
                            </div>
                        </div>
                        <hr style="margin:0.6rem;">';
                    }
                }
            }

            $data_val .=  '</div>   </div>';

            $json_array = [
                "data"              => $data_val,
            ];
        } else {
            print_r($result);
        }
        echo json_encode($json_array);

        break;



    // mythili
    case 'get_counts':

        $staff_name  = $_REQUEST['staff_name'];
        // $device_id  = $_REQUEST['device_id'];
        $today = date("m-d");
        $table_name    = "lets_talk";
        $table_details      = [
            $table_name,
            [
                "COUNT(unique_id) AS count",
                "employee_name",
                "entry_date"
            ]
        ];

        $where = "is_delete='0' and is_active='1' AND  entry_date like '%" .$today."%'";
       
        $staff_name_list = $pdo->select($table_details, $where);
        // print_r($staff_name_list);die();
        $office_in = $staff_name_list->data[0];


        $json_array = [

            "data"              => $office_in['count'],

        ];

        echo json_encode($json_array);
        break;


    // date_wise report start
    case 'datewise_report':

        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $table = "lets_talk";

        $data       = [];
        $employee_name = [];
        $description = [];
        $entry_date = [];
        $start_date = $_POST['from_date'];
        $end_date = $_POST['to_date'];

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "employee_name",
            "description",
            "entry_date"
        ];

        $table_details  = [
            $table,
            $columns
        ];

        $where  = "is_delete = 0";
        if (isset($_POST['from_date'])) {
            if ($_POST['from_date']) {
                $where .= " AND entry_date>= '" . $_POST['from_date'] . "'";
            }
        }

        if (isset($_POST['to_date'])) {
            if ($_POST['to_date']) {
                $where .= " AND entry_date <= '" . $_POST['to_date'] . "'";
            }
        }
        $order_by       = "";
        $sql_function   = "SQL_CALC_FOUND_ROWS";
        $result         = $pdo->select($table_details, $where);
      
        $total_records  = total_records();

        if ($result->status) {
            $res_array1      = $result->data;

            $table_data   .= '<table class="display table table-striped table-bordered" id="table3" style="width:100%">
                <thead>
                    <tr>
                        <th rowspan="2"> S.NO</th>
                        <th rowspan="2"> Entry Date </th>
                        <th>Employee Name</th>
                        <th colspan="2">Description </th>
                    </tr>								
                </thead>
                <tbody id="data_get">';
            if (count($res_array1) == 0) {
                $table_data .= "<tr>";
                $table_data .= "<td colspan=9 style='text-align:center'>" . 'No Data Found' . "</td>";
                $table_data .= "</tr>";
            } else {
                foreach ($res_array1 as $key => $value) {
                    $s_no  = $s_no + 1;
                    $employee_name = $value['employee_name'];
                    $entry_date = $value['entry_date'];
                    
                    // Convert the date string to a timestamp using strtotime()
                    $timestamp = strtotime($entry_date);

                    // Format the timestamp as "Month Year" using date()
                    $value['entry_date'] = date("d-m-Y", $timestamp);
                  
                    $description = $value['description'];
                    $table_data .= "<tr>";
                    $table_data .= "<td>" . $s_no . "</td>";
                    $table_data .= "<td>" . $value['entry_date'] . "</td>";
                    $table_data .= "<td style='text-align: left;'>" . $employee_name . "</td>";
                    $table_data .= "<td>" . $description . "</td>";


                    $table_data .= "</tr>";
                }
            }
            $table_data .= '</tbody>
                
                </table>';

            $json_array = [
                'data'            => $table_data,
            ];
            echo json_encode($json_array);

            break;
        }



    case 'festival_today':
        $data       = [];
        $today = date("m-d");

        $json_array     = "";
        $columns        = [
            "description",
            "title"
        ];
        $table_details  = [
            "festival_creation",
            $columns
        ];
        $where          = "datepicker like '%" .$today. "%' and is_delete = 0";

        $result = $pdo->select($table_details, $where);
        if ($result->status) {
            $res_array      = $result->data;

            $json_array = "";

            $data_val = '';
            
             if (!empty($res_array)) {
                $data_val =     '<div class="card">
                    <div class="card-body" style="height:292px;overflow-y: auto;">
                        <b><h4 style="color: #24bfa0;font-weight: 600;">Leave / Announcement &nbsp;<i class="fa fa-1x fa-bullhorn"></i></h4></b>
                        <hr/>';
             
            foreach ($res_array  as $key => $value) {

                $description = $value['description'];
                $title = $value['title'];

                $data_val .=  '<div class="row">
                <div class="col-md-12">
                    <h2 class="title_fr" style="text-align: center;">'.$title.'</h2>
                    <br>
                    <h4 style="color: #747474;">'.$description.' </h4>
                </div>
            </div> 
            <hr style="margin:0.6rem;">';
            }
            $data_val .=  '</div>   </div>';
        }
            $json_array = [
                "data"              => $data_val,
            ];
         
       
        echo json_encode($json_array);
        break;
    }

    default:
        break;

}






