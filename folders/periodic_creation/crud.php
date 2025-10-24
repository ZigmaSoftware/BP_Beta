<?php
// ==========================================================
// 📦 BASIC SETUP
// ==========================================================
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

$table      = "periodic_creation_main";
$table_sub  = "periodic_creation_sub";
$prefix     = "per";

include '../../config/dbconfig.php';
include 'function.php';

$action     = $_POST['action'] ?? '';
$action_obj = (object)[ "status" => 0, "data" => "", "error" => "Action not performed" ];

$status = 0; 
$msg = ''; 
$data = ''; 
$error = ''; 
$sql = '';

// ==========================================================
// 🔹 CASE HANDLER
// ==========================================================
switch ($action) {

    // ======================================================
    // 🧩 CREATE / UPDATE MAIN
    // ======================================================
    case 'createupdate':
        $unique_id      = $_POST["unique_id"] ?? '';
        $user_id        = $_POST["user_id"] ?? '';
        $user_type      = $_POST["user_type"] ?? '';
        $mobile_number  = $_POST["mobile_number"] ?? '';
        $designation    = $_POST["designation"] ?? '';

        // Prepare main table columns
        $main_unique_id = $unique_id ?: unique_id($prefix);
        $columns = [
            "unique_id"      => $main_unique_id,
            "user_id"        => $user_id,
            "user_type"      => $user_type,
            "mobile_number"  => $mobile_number,
            "designation"    => $designation
        ];

        // Check duplicates (prevent duplicate user entries)
        $check = $pdo->select([$table, ["COUNT(unique_id) AS count"]], "user_id='$user_id' AND is_delete=0");
        if ($check->status && $check->data[0]['count'] > 0 && !$unique_id) {
            $msg = "already";
        } else {
            if ($unique_id) {
                unset($columns["unique_id"]);
                $action_obj = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
                $msg = "update";
            } else {
                $action_obj = $pdo->insert($table, $columns);
                $msg = "create";
            }
        }

        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql,
            "periodic_unique_id" => $main_unique_id
        ]);
        break;


    // ======================================================
    // 📊 DATATABLE MAIN
    // ======================================================
    case 'datatable':
        $start  = $_POST['start'] ?? 0;
        $length = $_POST['length'] ?? 10;
        $draw   = $_POST['draw'] ?? 1;

        $columns = [
            "@a:=@a+1 s_no",
            "user_id",
            "user_type",
            "mobile_number",
            "unique_id"
        ];

        $table_details = [$table . ", (SELECT @a:=" . intval($start) . ") AS a", $columns];
        $where = "is_active=1 AND is_delete=0";
        $result = $pdo->select($table_details, $where, $length, $start, "id DESC", "SQL_CALC_FOUND_ROWS");
        $total_records = total_records();

        $data = [];
        if ($result->status) {
            foreach ($result->data as $row) {
                $staff = user_name($row['user_id'])[0];
                $row['user_id'] = disname($staff['staff_name']);
                $row['mobile_number'] = $staff['phone_no'];
                $row['user_type'] = user_type($staff['user_type_unique_id'])[0]['user_type'];

                $btn_update = btn_update($folder_name, $row['unique_id']);
                $btn_delete = btn_delete($folder_name, $row['unique_id']);
                $row['unique_id'] = $btn_update . $btn_delete;
                $data[] = $row;
            }
        }

        echo json_encode([
            "draw" => intval($draw),
            "recordsTotal" => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data" => $data
        ]);
        break;


    // ======================================================
    // 🧾 SUBLIST ADD/UPDATE
    // ======================================================
    case 'periodic_add_update':
        $unique_id      = $_POST["unique_id"] ?? '';
        $user_id        = $_POST["user_id"] ?? '';
        $department     = $_POST["department"] ?? '';
        $category       = $_POST["category"] ?? '';
        $project_id     = $_POST["project_id"] ?? '';
        $level          = $_POST["level"] ?? '';
        $starting_days  = $_POST["starting_days"] ?? 0;
        $ending_days    = $_POST["ending_days"] ?? 0;

        $columns = [
            "unique_id"     => unique_id($prefix),
            "user_id"       => $user_id,
            "department"    => $department,
            "category"      => $category,
            "project_id"    => $project_id,
            "level"         => $level,
            "starting_days" => $starting_days,
            "ending_days"   => $ending_days
        ];

        if ($unique_id) {
            unset($columns["unique_id"]);
            $action_obj = $pdo->update($table_sub, $columns, ["unique_id" => $unique_id]);
            $msg = "update";
        } else {
            $action_obj = $pdo->insert($table_sub, $columns);
            $msg = "add";
        }
        
        $response = [
            "status" => $action_obj->status,
            "msg"    => $msg,
            "error"  => $action_obj->error,

        ];
        
        echo json_encode($response);
        break;


    // ======================================================
    // 🧹 SUBLIST DELETE
    // ======================================================
    case 'periodic_sub_delete':
        $unique_id = $_POST['unique_id'] ?? '';
        $action_obj = $pdo->update($table_sub, ["is_delete" => 1], ["unique_id" => $unique_id]);
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "success_delete" : "error",
            "sql"    => $action_obj->sql
        ]);
        break;


    // ======================================================
    // 📋 GET SUBLIST DATA (FOR DATATABLE)
    // ======================================================
    case 'periodic_sub_datatable':
        $user_id = $_POST['user_id'] ?? '';
        error_log(print_r($_POST, true), 3, "subpost.log");
        $columns = [
            "@a:=@a+1 s_no",
            "department",
            "category",
            "project_id",
            "level",
            "starting_days",
            "ending_days",
            "unique_id"
        ];
        $table_details = [$table_sub . ", (SELECT @a:=0) AS a", $columns];
        $where = "user_id='$user_id' AND is_delete=0 AND is_active=1";

        $result = $pdo->select($table_details, $where);
        error_log(print_r($result, true), 3, "subresult.log");
        $data = [];

        if ($result->status) {
            foreach ($result->data as $row) {
                $row['department'] = department($row['department'])[0]['department'] ?? '-';
                $row['category']   = task_category($row['category'])[0]['category_name'] ?? '-';
                $row['project_id'] = project_name($row['project_id'])[0]['site_name'] ?? '-';
                $row['level']      = strtoupper($row['level']);
                $row['action']     = btn_delete("periodic_sub", $row['unique_id']);
                $data[] = $row;
            }
        }

        echo json_encode([
            "data" => $data,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data)
        ]);
        break;


    // ======================================================
    // 🧠 FETCH USER INFO
    // ======================================================
    case 'get_usertype':
        $usr_id = $_POST['user_name'] ?? '';

        $user = user_name("", $usr_id);
        $user_type = user_type($user[0]['user_type_unique_id'])[0]['user_type'] ?? '';
        $mobile_no = $user[0]['phone_no'] ?? '';

        $staff_designation = staff_name_bp($usr_id)[0]['designation_unique_id'];
        $designation = designation("", $staff_designation)[0]['designation'] ?? '';

        echo json_encode([
            "user_type" => $user_type,
            "mobile_no" => $mobile_no,
            "designation" => $designation
        ]);
        break;


    // ======================================================
    // 🗑 DELETE MAIN
    // ======================================================
    case 'delete':
        $unique_id = $_POST['unique_id'] ?? '';
        $action_obj = $pdo->update($table, ["is_delete" => 1], ["unique_id" => $unique_id]);
        echo json_encode([
            "status" => $action_obj->status,
            "msg"    => $action_obj->status ? "success_delete" : "error",
            "sql"    => $action_obj->sql
        ]);
        break;


    default:
        echo json_encode(["error" => "No valid action specified."]);
        break;
}
?>
