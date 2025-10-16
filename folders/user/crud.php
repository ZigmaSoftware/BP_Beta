<?php
// ==============================================
// USER CRUD (Modified to handle In Role / Off Role)
// ==============================================

// Get folder Name From Current Url 
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// Database Table
$table = "user";

// Include DB Config
include '../../config/dbconfig.php';

// -----------------------------
// Variable Initialization
// -----------------------------
$action = $_POST['action'] ?? '';
$action_obj = (object)[
    "status" => 0,
    "data"   => "",
    "error"  => "Action Not Performed"
];

$json_array = $sql = "";
$status = $msg = $error = "";
$unique_id = $prefix = "";

// ===================================================
// CREATE / UPDATE USER
// ===================================================
switch ($action) {
    case 'createupdate':

        // Form Fields
        $full_name        = $_POST["full_name"] ?? "";
        $user_name        = $_POST["user_name"] ?? "";
        $password         = $_POST["password"] ?? "";
        $user_type        = $_POST["user_type"] ?? "";
        $is_active        = $_POST["is_active"] ?? 1;
        $phone_no         = $_POST["phone_no"] ?? "";
        $confirm_password = $_POST["confirm_password"] ?? "";
        $under_user       = $_POST["under_user"] ?? "";
        $team_members     = $_POST["team_users"] ?? "";
        $unique_id        = $_POST["unique_id"] ?? "";
        $role             = $_POST["role"] ?? "In Role";

        error_log(print_r($_POST, true), 3, "POST.log");

        // Handle work_location field
        if (isset($_POST['work_location']) && is_array($_POST['work_location'])) {
            $work_location_array = array_filter(array_map('trim', $_POST['work_location']));
            $work_location = !empty($work_location_array) ? implode(",", $work_location_array) : "all";
        } else {
            $work_location = "all";
        }

        // Base columns
        $columns = [
            "user_name"             => $user_name,
            "password"              => $password,
            "phone_no"              => $phone_no,
            "is_active"             => $is_active,
            "user_type_unique_id"   => $user_type,
            "under_user"            => $under_user,
            "team_members"          => $team_members,
            "work_location"         => $work_location,
            "role"                  => $role,
        ];

        // Add staff_unique_id only for In Role users
        if ($role === "In Role") {
            $columns["staff_unique_id"] = $full_name;
        } else {
            $columns["staff_unique_id"] = ""; // Off Role users don’t have staff linkage
        }

        // Add unique_id if inserting
        $columns["unique_id"] = unique_id($prefix);

        // Team Head logic
        if (isset($_POST["is_team_head"])) {
            $columns["is_team_head"] = 1;
            if (empty($_POST["team_id"])) {
                $columns["team_id"] = unique_id();
            } else {
                $columns["team_id"] = $_POST["team_id"];
            }
        } else {
            $columns["is_team_head"] = 0;
            $columns["team_members"] = '';
        }
        
        // -----------------------------
        // Duplicate Check (clean version)
        // -----------------------------
        $table_details = [$table, ["COUNT(unique_id) AS count"]];
        $where_parts = [];
        
        $where_parts[] = '(user_name = "' . addslashes($user_name) . '" OR phone_no = "' . addslashes($phone_no) . '")';
        if ($role === "In Role") {
            $where_parts[] = 'staff_unique_id = "' . addslashes($full_name) . '"';
        }
        $where_parts[] = 'is_delete = 0';
        if (!empty($unique_id)) {
            $where_parts[] = 'unique_id NOT IN ("' . addslashes($unique_id) . '")';
        }
        
        $select_where = implode(' AND ', $where_parts);
        $dup_check = $pdo->select($table_details, $select_where);
        
        if (!$dup_check->status) {
            echo json_encode([
                "status" => 0,
                "msg"    => "error",
                "error"  => $dup_check->error,
                "sql"    => $dup_check->sql
            ]);
            exit;
        }
        
        $dup_count = (int)($dup_check->data[0]['count'] ?? 0);
        
        if ($dup_count > 0) {
            echo json_encode([
                "status" => 1,
                "msg"    => "already",
                "data"   => $dup_check->data,
                "sql"    => $dup_check->sql
            ]);
            exit;
        }
        
        // -----------------------------
        // Insert or Update
        // -----------------------------
        if (!empty($unique_id)) {
            unset($columns['unique_id']);
            $update_where = ["unique_id" => $unique_id];
            $action_obj = $pdo->update($table, $columns, $update_where);
            $msg = "update";
        } else {
            $action_obj = $pdo->insert($table, $columns);
            $msg = "create";
        }
        
        echo json_encode([
            "status" => $action_obj->status ? 1 : 0,
            "msg"    => $msg,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "sql"    => $action_obj->sql
        ]);

        break;

    // ===================================================
    // DATATABLE
    // ===================================================
    case 'datatable':
        $search  = $_POST['search']['value'] ?? '';
        $length  = $_POST['length'] ?? 10;
        $start   = $_POST['start'] ?? 0;
        $draw    = $_POST['draw'] ?? 1;
        $limit   = ($length == '-1') ? "" : $length;
        $data    = [];
    
        // 🔹 Dynamic CASE logic for in-role / off-role users
        $columns = [
            "@a:=@a+1 s_no",
            "CASE 
                WHEN {$table}.role = 'inrole' 
                    THEN (SELECT staff_name 
                          FROM staff_test AS staff 
                          WHERE staff.employee_id = {$table}.staff_unique_id 
                          LIMIT 1)
                ELSE {$table}.user_name
             END AS name",
            "{$table}.phone_no",
            "{$table}.user_name",
            "(SELECT ut.user_type 
              FROM user_type AS ut 
              WHERE ut.unique_id = {$table}.user_type_unique_id 
              LIMIT 1) AS user_type",
            "{$table}.password",
            "{$table}.work_location",
            "{$table}.is_active",
            "{$table}.unique_id"
        ];
    
        $table_details = [$table . " , (SELECT @a:=" . $start . ") AS a ", $columns];
        $where = " {$table}.is_delete = '0' ";
    
        $order_column = $_POST["order"][0]["column"] ?? 0;
        $order_dir    = $_POST["order"][0]["dir"] ?? 'asc';
    
        $order_by = datatable_sorting($order_column, $order_dir, $columns);
        $search_sql = datatable_searching($search, $columns);
    
        if ($search_sql) {
            $where .= " AND " . $search_sql;
        }
    
        $result = $pdo->select($table_details, $where, $limit, $start, $order_by, "SQL_CALC_FOUND_ROWS");
        $total_records = total_records();
    
        if ($result->status) {
            foreach ($result->data as $value) {
                // 🔹 Format work location nicely
                if ($value['work_location'] && $value['work_location'] != 'all') {
                    $ids = explode(',', $value['work_location']);
                    $locs = [];
                    foreach ($ids as $id) {
                        $loc = get_project_name_all($id);
                        if (!empty($loc[0]['label'])) {
                            $locs[] = $loc[0]['label'];
                        }
                    }
                    $value['work_location'] = implode(', ', $locs);
                } elseif ($value['work_location'] == 'all') {
                    $value['work_location'] = 'ALL Projects';
                } else {
                    $value['work_location'] = '-';
                }
    
                // 🔹 Action buttons
                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == "1")
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);
                $value['unique_id'] = $btn_update . $btn_toggle;
    
                $value['is_active'] = ($value['is_active'] == "1")
                    ? "<span style='color:green'>Active</span>"
                    : "<span style='color:red'>Inactive</span>";
    
                $data[] = array_values($value);
            }
    
            echo json_encode([
                "draw" => intval($draw),
                "recordsTotal" => intval($total_records),
                "recordsFiltered" => intval($total_records),
                "data" => $data,
                "testing" => $result->sql
            ]);
        } else {
            print_r($result);
        }
        break;

    // ===================================================
    // TOGGLE ACTIVE STATUS
    // ===================================================
    case 'toggle':
        $unique_id = $_POST['unique_id'] ?? "";
        $is_active = $_POST['is_active'] ?? 0;

        $columns = ["is_active" => $is_active];
        $update_where = ["unique_id" => $unique_id];

        $action_obj = $pdo->update($table, $columns, $update_where);

        echo json_encode([
            "status" => $action_obj->status,
            "msg" => $is_active ? "Activated Successfully" : "Deactivated Successfully",
            "sql" => $action_obj->sql,
            "error" => $action_obj->error
        ]);
        break;

    // ===================================================
    // UNDER USER OPTIONS
    // ===================================================
    case 'user_options':
        $under_user = $_POST['under_user'] ?? "";
        $user_name_options = select_option(under_user($under_user), "Select");
        echo $user_name_options;
        break;

    // ===================================================
    // GET STAFF MOBILE
    // ===================================================
    case 'mobile':
        $staff_id = $_POST['staff_id'] ?? "";
    
        // Fetch staff details
        $staff_details = staff_name_bp($staff_id);
    
        $response = [
            "mobile" => $staff_details[0]["office_contact_no"] ?? "",
            "work_location" => $staff_details[0]["work_location"] ?? ""
        ];
    
        echo json_encode($response);
        break;


    default:
        echo json_encode(["status" => 0, "msg" => "Invalid Action"]);
        break;
}
?>
