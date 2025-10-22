<?php

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table = "task_category";

// Include DB file and Common Functions
include '../../config/dbconfig.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";

$group_unique_id    = "";
$sub_group_unique_id= "";
$sub_group_code     = "";
$category_name      = "";
$code               = "";
$description        = "";
$is_active          = "";
$unique_id          = "";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$user_id = $_SESSION['sess_user_id'];
$date = date('Y-m-d H:i:s', time());

switch($action):
    
    case "createupdate":
        $department  = $_POST['department'] ?? '';
        $category    = $_POST['category'] ?? '';
        $description = $_POST['description'] ?? '';
        $update_form = $_POST['update_form'] ?? 0;
        $unique_id   = $_POST['unique_id'] ?? unique_id(); // your helper function
        
        error_log("post: " . print_r($_POST, true), "\n", 3, "post.log");
    
        // Common fields for both insert and update
        $column = [
            "unique_id"            => $unique_id,
            "department_unique_id" => $department,
            "task_category_name"   => $category,
            "description"          => $description
        ];
    
        // Check whether record exists
        $count_result = $pdo->select(
            [$table, ["COUNT(*) as total_count"]],
            ["department_unique_id" => $department, "task_category_name" => $category]
        );
    
        $total_count = $count_result->data[0]['total_count'] ?? 0;
        
        error_log("count: " . print_r($count_result, true) . "\n", 3, "count.log");
    
        if ($total_count > 0) {
            if($update_form != 0) {
                // 🔁 Update existing record (add updated fields here only)
                $update_columns = $column;
                $update_columns["updated"]      = date('Y-m-d H:i:s');
                $update_columns["updated_user_id"] = $_SESSION['sess_user_id'] ?? '';
        
                $update_result = $pdo->update($table, $update_columns, ["unique_id" => $unique_id]);
                error_log("update: " . print_r($update_result, true) . "\n", 3, "update.log");
        
                if ($update_result->status) {
                    $action_obj->status = 1;
                    $action_obj->data   = $update_result->data;
                    $action_obj->error  = "Category updated successfully.";
                } else {
                    $action_obj->error  = "Failed to update category.";
                }
            } else {
                $action_obj->status = 0;
                $action_obj->error  = "This category already exists for the selected department.";
            }
    
        } else {
            // 🆕 Insert new record (only created fields)
            $insert_columns = $column;
            $insert_columns["created"]      = date('Y-m-d H:i:s');
            $insert_columns["created_user_id"] = $_SESSION['sess_user_id'] ?? '';
    
            $insert_result = $pdo->insert($table, $insert_columns);
            error_log("insert: " . print_r($insert_result, true) . "\n", 3, "insert.log");
    
            if ($insert_result->status) {
                $action_obj->status = 1;
                $action_obj->data   = $insert_result->data;
                $action_obj->error  = "Category created successfully.";
            } else {
                $action_obj->error  = "Failed to create category.";
            }
        }
        
    echo json_encode($action_obj);
    
    break;
    
    case "datatable":
        $department = $_POST['department'] ?? '';
        $where = ["is_delete" => 0]; // Optional field if your table has it
    
        if ($department !== '') {
            $where["department_unique_id"] = $department;
        }
    
        $columns = [
            "@a:=@a+1 AS s_no",
            "t.department_unique_id",
            "t.task_category_name",
            "t.description",
            "t.unique_id"
        ];
    
        $table_alias = "$table t, (SELECT @a:=0) AS a";
        $result = $pdo->select([$table_alias, $columns], $where);
        
        error_log("datatable: " . print_r($result, true) . "\n", 3, "result.log");
    
        $data = [];
        if ($result->status && !empty($result->data)) {
            foreach ($result->data as $row) {
                
                $row['department_unique_id'] = department($row['department_unique_id'])[0]['department'];
                
                $action_btns  = btn_update($btn_update, $row['unique_id']);
                $action_btns .= btn_delete($btn_delete, $row['unique_id']);
                
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


    default:
        $response['msg'] = "Invalid action type";
        break;

endswitch;
        

?>