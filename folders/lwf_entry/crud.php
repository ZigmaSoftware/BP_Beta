<?php
include '../../config/dbconfig.php';
include '../../config/new_db.php';

$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

$table = 'lwf_entry';
$action = $_POST['action'] ?? '';
// $action_obj = (object) [
//     "status" => 0,
//     "data" => null,
//     "error" => "Unhandled action"
// ];

switch ($action) {
    case 'get_state':
        $project_id = $_POST['project_id'] ?? '';
        if (!empty($project_id)) {
            $where = [
                "unique_id" => $project_id,
                "is_active" => 1,
                "is_delete" => 0
            ];
            $columns = ["state"];
            $res = $pdo->select(["project_creation", $columns], $where);
            if ($res->status && isset($res->data[0]['state'])) {
                $state_id = $res->data[0]['state'];
                $state = state($state_id)[0]['state_name'];
                $action_obj->status = 1;
                $action_obj->data = ["state" => $state];
                $action_obj->error = "";
            } else {
                $action_obj->error = "State not found for project";
            }
        } else {
            $action_obj->error = "Missing project_id";
        }
        echo json_encode($action_obj);
        break;

    case 'createupdate':
        $project_id = $_POST['project_id'] ?? '';
        $state = $_POST['state'] ?? '';
        $amount = $_POST['amount'] ?? '';
        $unique_id = $_POST['unique_id'] ?? unique_id();
        $date = date('Y-m-d H:i:s');
        $user_id = $_SESSION['sess_user_id'] ?? 0;
        
        if($_POST['unique_id']){
            $action = 'update';
        }

        $columns = [
            "project_id" => $project_id,
            "state" => $state,
            "amount" => $amount,
            // "updated_user_id" => $user_id,
            "updated" => $date
        ];

        if ($action === 'create') {
            // $columns["created_user_id"] = $user_id;
            $columns["created"] = $date;
            $columns["unique_id"] = $unique_id;
            $action_obj = $pdo->insert($table, $columns);
        } else {
            $action_obj = $pdo->update($table, $columns, ["unique_id" => $unique_id]);
        }
        
        error_log("action: " . print_r($action_obj, true) . "\n", 3, "action_log.txt");

        echo json_encode([
            "status" => $action_obj->status,
            "data" => ["unique_id" => $unique_id],
            "error" => $action_obj->error,
            "msg" => $action
        ]);
        break;

    case 'delete':
        $unique_id = $_POST['unique_id'] ?? '';
        if (!empty($unique_id)) {
            $action_obj = $pdo->update($table, ["is_delete" => 1], ["unique_id" => $unique_id]);
        } else {
            $action_obj->error = "Missing unique_id";
        }
        echo json_encode([
            "status" => $action_obj->status,
            "msg" => $action_obj->status ? "deleted" : "delete_failed",
            "error" => $action_obj->error
        ]);
        break;

    case 'datatable':
    $where = "is_delete = 0";
    $columns = ["project_id", "state", "amount", "unique_id"];
    $res = $pdo->select([$table, $columns], $where);

    if ($res->status && !empty($res->data)) {
        $data = [];
        $s_no = 1;
        $is_admin = isset($_SESSION['sess_user_type']) && $_SESSION['sess_user_type'] == $admin_user_type;

        foreach ($res->data as $row) {
            // Get project name
            $proj_res = $pdo->select(["project_creation", ["project_name"]], ["unique_id" => $row["project_id"]]);
            $project_name = $proj_res->status && isset($proj_res->data[0]['project_name'])
                ? $proj_res->data[0]['project_name']
                : "Unknown";

            // Buttons
            $btn_update = btn_update($folder_name, $row['unique_id']);
            $btn_delete = $is_admin ? btn_delete($folder_name, $row['unique_id']) : '';
            $btns = $btn_update . $btn_delete;

            $data[] = [
                's_no'         => $s_no++,
                'project_name' => $project_name,
                'state'        => $row['state'],
                'amount'       => number_format($row['amount'], 2),
                'btns'         => $btns
            ];
        }
        
        error_log("btns: " . print_r($data, true) . "\n", 3, "data.txt");

        echo json_encode([
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
            "error" => $res->error
        ]);
    }
    break;


    default:
        echo json_encode($action_obj);
        break;
}
