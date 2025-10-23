<?php
// ===========================================================
// 🧠 TASK SUB-CATEGORY CRUD HANDLER
// ===========================================================

// Get folder name from current URL
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// Table name
$table = "task_sub_category";

// Include DB config
include '../../config/dbconfig.php';

// Initialize response object
$action_obj = (object) [
    "status" => 0,
    "data"   => "",
    "error"  => "Action Not Performed"
];

// Common variables
$action = $_POST['action'] ?? '';
$user_id = $_SESSION['sess_user_id'] ?? '';
$now     = date('Y-m-d H:i:s');

switch ($action):

    // ===========================================================
    // 💾 CREATE / UPDATE
    // ===========================================================
    case "createupdate":
        $department   = $_POST['department'] ?? '';
        $category     = $_POST['category'] ?? '';
        $sub_category = trim($_POST['sub_category'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $update_form  = $_POST['update_form'] ?? 0;
        $unique_id    = !empty($_POST['unique_id']) ? $_POST['unique_id'] : unique_id();
    
        // --- prepare column mapping
        $columns = [
            "unique_id"               => $unique_id,
            "department_unique_id"    => $department,
            "task_category_name" => $category,
            "task_sub_category_name"  => $sub_category,
            "description"             => $description
        ];
    
        // --- check duplicates
        $duplicate_check = $pdo->select(
            [$table, ["COUNT(*) as total"]],
            [
                "department_unique_id"    => $department,
                "task_category_name" => $category,
                "task_sub_category_name"  => $sub_category,
                "is_delete"               => 0
            ]
        );
        $exists = $duplicate_check->data[0]['total'] ?? 0;
    
        if ($update_form != 0) {
            // --- UPDATE
            if ($exists > 0) {
                $action_obj->status = 0;
                $action_obj->error  = "This sub-category already exists under the selected department and category.";
            } else {
                $columns["updated"]         = $now;
                $columns["updated_user_id"] = $user_id;
    
                $update_result = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
    
                if ($update_result->status) {
                    $action_obj->status = 1;
                    $action_obj->error  = "Sub-category updated successfully.";
                } else {
                    $action_obj->error  = "Failed to update sub-category.";
                }
            }
        } else {
            // --- INSERT
            if ($exists > 0) {
                $action_obj->status = 0;
                $action_obj->error  = "This sub-category already exists under the selected department and category.";
            } else {
                $columns["created"]         = $now;
                $columns["created_user_id"] = $user_id;

                $insert_result = $pdo->insert($table, $columns);
                error_log("insert: " . print_r($insert_result, true) . "\n", 3, "insert.log");
    
                if ($insert_result->status) {
                    $action_obj->status = 1;
                    $action_obj->error  = "Sub-category created successfully.";
                } else {
                    $action_obj->error  = "Failed to create sub-category.";
                }
            }
        }
    
        echo json_encode($action_obj);
    break;

    // ===========================================================
    // 📊 DATATABLE LIST
    // ===========================================================
    case "datatable":
        $department = $_POST['department'] ?? '';
        $category   = $_POST['category'] ?? '';
    
        $where = ["is_delete" => 0];
        if ($department !== '') $where["department_unique_id"] = $department;
        if ($category !== '')   $where["task_category_name"] = $category;
    
        $columns = [
            "@a:=@a+1 AS s_no",
            "t.unique_id",
            "t.department_unique_id",
            "t.task_category_name",
            "t.task_sub_category_name",
            "t.description",
            "t.is_active"
        ];
    
        $table_alias = "$table t, (SELECT @a:=0) AS a";
        $result = $pdo->select([$table_alias, $columns], $where);
        
        error_log(print_r($result, true) . "\n" ,3 ,"result_datatable.log");
    
        $data = [];
        if ($result->status && !empty($result->data)) {
            foreach ($result->data as $row) {
                // Get department name
                $dept = department($row['department_unique_id']);
                $row['department_name'] = $dept ? $dept[0]['department'] : '-';
    
                // Get category name
                $cat = task_category($row['task_category_name']);
                $row['task_category_name'] = $cat ? $cat[0]['task_category_name'] : '-';
    
                // Action buttons
                $actions = btn_update($folder_name, $row['unique_id']);
                $actions .= ($row['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $row['unique_id'])
                    : btn_toggle_off($folder_name, $row['unique_id']);
                $row['action'] = $actions;
    
                $data[] = $row;
            }
        }
    
        echo json_encode([
            "data" => $data,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data)
        ]);
    break;

    // ===========================================================
    // 🔁 TOGGLE ACTIVE / INACTIVE
    // ===========================================================
    case "toggle":
        $unique_id = $_POST['unique_id'] ?? '';
        $mode      = $_POST['mode'] ?? '';
    
        if (!empty($unique_id) && in_array($mode, ["activate", "deactivate"])) {
            $new_status = ($mode === "activate") ? 1 : 0;
    
            $update_result = $pdo->update(
                $table,
                [
                    "is_active"       => $new_status,
                    "updated"         => $now,
                    "updated_user_id" => $user_id
                ],
                ["unique_id" => $unique_id]
            );
    
            if ($update_result->status) {
                $action_obj->status = 1;
                $action_obj->error  = "Sub-category " . ($mode === "activate" ? "activated" : "deactivated") . " successfully.";
            } else {
                $action_obj->error = "Failed to update sub-category status.";
            }
        } else {
            $action_obj->error = "Invalid request — missing unique ID or mode.";
        }
    
        echo json_encode($action_obj);
    break;
    
    case "task_list":
        
        $department = $_POST['department'];
        
        $task_options = task_category("", $department);
        
        $task_options = select_option($task_options, "Select Task Category");
        
        echo $task_options;
        
    break;

// ===========================================================
// ❌ DEFAULT
// ===========================================================
default:
    $action_obj->error = "Invalid action type.";
    echo json_encode($action_obj);
    break;

endswitch;
?>
