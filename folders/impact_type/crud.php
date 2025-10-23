<?php
// ===========================================================
// 🧠 IMPACT TYPE CRUD HANDLER
// ===========================================================

// Get folder name dynamically
$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name) - 2];

// Table name
$table = "impact_type";

// Include config
include '../../config/dbconfig.php';

// Common variables
$action = $_POST['action'] ?? '';
$user_id = $_SESSION['sess_user_id'] ?? '';
$now = date('Y-m-d H:i:s');

$action_obj = (object)[
    "status" => 0,
    "data"   => "",
    "error"  => "Action not performed"
];

// ===========================================================
// 💾 CREATE / UPDATE
// ===========================================================
switch ($action):
    
    case "createupdate":
        $impact_type  = trim($_POST['impact_type'] ?? '');
        $description  = trim($_POST['description'] ?? '');
        $active       = $_POST['active'] ?? 1;
        $update_form  = $_POST['update_form'] ?? 0;
        $unique_id    = !empty($_POST['unique_id']) ? $_POST['unique_id'] : unique_id();
    
        if (empty($impact_type)) {
            $action_obj->error = "Impact Type name cannot be empty.";
            echo json_encode($action_obj);
            break;
        }
    
        // Build columns
        $columns = [
            "unique_id"   => $unique_id,
            "impact_type" => $impact_type,
            "description" => $description,
            "is_active"   => $active
        ];
    
        // Check duplicate (same name)
        $dup_check = $pdo->select([$table, ["COUNT(*) as cnt"]], ["impact_type" => $impact_type, "is_delete" => 0]);
        $exists = $dup_check->data[0]['cnt'] ?? 0;
    
        if ($update_form != 0) {
            // Update mode
            if ($exists > 0) {
                $action_obj->status = 0;
                $action_obj->error  = "Impact Type already exists.";
            } else {
                $columns["updated"] = $now;
                $columns["updated_user_id"] = $user_id;
    
                $update_result = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
                if ($update_result->status) {
                    $action_obj->status = 1;
                    $action_obj->error  = "Impact Type updated successfully.";
                } else {
                    $action_obj->error  = "Failed to update Impact Type.";
                }
            }
        } else {
            // Insert mode
            if ($exists > 0) {
                $action_obj->status = 0;
                $action_obj->error  = "Impact Type already exists.";
            } else {
                $columns["created"] = $now;
                $columns["created_user_id"] = $user_id;
                $columns["is_delete"] = 0;
    
                $insert_result = $pdo->insert($table, $columns);
                error_log(print_r($insert_result, true) . "\n", 3, "insert.log");
                if ($insert_result->status) {
                    $action_obj->status = 1;
                    $action_obj->error  = "Impact Type created successfully.";
                } else {
                    $action_obj->error  = "Failed to create Impact Type.";
                }
            }
        }
    
        echo json_encode($action_obj);
        break;
    
    // ===========================================================
    // 📊 DATATABLE
    // ===========================================================
    case "datatable":
        $where = ["is_delete" => 0];
        $columns = [
            "@a:=@a+1 AS s_no",
            "t.unique_id",
            "t.impact_type",
            "t.description",
            "t.is_active as status_label",
            "t.is_active"
        ];
        $table_alias = "$table t, (SELECT @a:=0) AS a";
        $result = $pdo->select([$table_alias, $columns], $where);
    
        $data = [];
        if ($result->status && !empty($result->data)) {
            foreach ($result->data as $row) {
                $row['status_label'] = ($row['status_label'] == 1)
                    ? "<span class='text-success'>Active</span>"
                    : "<span class='text-danger'>Inactive</span>";
    
                $action_btns = btn_update($folder_name, $row['unique_id']);
                $action_btns .= ($row['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $row['unique_id'])
                    : btn_toggle_off($folder_name, $row['unique_id']);
    
                $row['action'] = $action_btns;
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
        $mode = $_POST['mode'] ?? '';
    
        if (!empty($unique_id) && in_array($mode, ["activate", "deactivate"])) {
            $new_status = ($mode === "activate") ? 1 : 0;
            $update_result = $pdo->update(
                $table,
                [
                    "is_active" => $new_status,
                    "updated" => $now,
                    "updated_user_id" => $user_id
                ],
                ["unique_id" => $unique_id]
            );
    
            if ($update_result->status) {
                $action_obj->status = 1;
                $action_obj->error  = "Impact Type " . ($mode === "activate" ? "activated" : "deactivated") . " successfully.";
            } else {
                $action_obj->error = "Failed to update Impact Type status.";
            }
        } else {
            $action_obj->error = "Invalid request — missing unique ID or mode.";
        }
    
        echo json_encode($action_obj);
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
