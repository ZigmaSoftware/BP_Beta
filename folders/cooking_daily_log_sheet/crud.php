<?php 

$folder_name = explode("/", $_SERVER['PHP_SELF']);
$folder_name = $folder_name[count($folder_name)-2];

// Use your TCS Kolkata table
$table = "tcs_kolkata_daily_log";

include '../../config/dbconfig.php';

$action = $_POST['action'];
$action_obj = (object)[
    "status" => 0,
    "data"   => "",
    "error"  => "Action Not Performed"
];

$json_array = "";
$sql = "";

$unique_id = "";
$prefix = "";
$data = "";
$msg = "";
$error = "";
$status = "";
$test = "";

// ---- Main Switch Block ---- //
switch ($action) {
    case 'createupdate':
        
        $project_id            = $_POST["project_id"];
        $entry_date            = $_POST["entry_date"];
        $waste_receive         = $_POST["waste_receive"];
        $waste_crushing_feeding= $_POST["waste_crushing_feeding"];
        $waste_handed_back_ccp = $_POST["waste_handed_back_ccp"]; // new field
        $water_liters          = $_POST["water_liters"];
        $feeding_ph            = $_POST["feeding_ph"];
        $digester_1_ph         = $_POST["digester_1_ph"];
        $balloon_1_position    = $_POST["balloon_1_position"];
        $remarks               = $_POST["remarks"];
        $unique_id             = $_POST["unique_id"];
        
        // ---- Duplicate entry restriction ---- //
        $duplicate_check_sql = "SELECT COUNT(*) AS count 
                                FROM $table 
                                WHERE project_id = :project_id 
                                  AND entry_date = :entry_date 
                                  AND is_delete = 0";
        
        if (!empty($_POST["unique_id"])) {
            $duplicate_check_sql .= " AND unique_id != :unique_id";
        }
        
        $stmt = $pdo->conn->prepare($duplicate_check_sql);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->bindParam(':entry_date', $entry_date);
        if (!empty($_POST["unique_id"])) {
            $stmt->bindParam(':unique_id', $_POST["unique_id"]);
        }
        $stmt->execute();
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            echo json_encode([
                "status" => 0,
                "msg"    => "duplicate",
                "error"  => "Duplicate entry for this Project and Date."
            ]);
            exit;
        }


        $columns = [
            "project_id"            => $project_id,
            "entry_date"            => $entry_date,
            "waste_receive"         => $waste_receive,
            "waste_crushing_feeding"=> $waste_crushing_feeding,
            "waste_handed_back_ccp" => $waste_handed_back_ccp,
            "water_liters"          => $water_liters,
            "feeding_ph"            => $feeding_ph,
            "digester_1_ph"         => $digester_1_ph,
            "balloon_1_position"    => $balloon_1_position,
            "remarks"               => $remarks,
            "created_by"            => isset($_SESSION['staff_id']) ? $_SESSION['staff_id'] : 'unknown',
            "unique_id"             => unique_id($prefix)
        ];

        if ($unique_id) {
            unset($columns['unique_id']);
            $update_where = ["unique_id" => $unique_id];
            $action_obj = $pdo->update($table, $columns, $update_where);
        } else {
            // Insert new entry (no duplicate date check needed)
            $action_obj = $pdo->insert($table, $columns);
        }

        if ($action_obj->status) {
            $status = 1;
            $msg = $unique_id ? "update" : "create";
        } else {
            $status = 0;
            $msg = "error";
            $error = $action_obj->error;
        }

        echo json_encode([
            "status" => $status,
            "data"   => $action_obj->data,
            "error"  => $error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
        break;

    case 'datatable':
    $search = $_POST['search']['value'];
    $length = $_POST['length'];
    $start  = $_POST['start'];
    $draw   = $_POST['draw'];
    $limit  = $length;

    $data = [];

    if ($length == '-1') {
        $limit = "";
    }

    $columns = [
        "@a:=@a+1 s_no",
        "entry_date",
        "project_id",
        "waste_receive",
        "waste_crushing_feeding",
        "waste_handed_back_ccp",   // new field
        "water_liters",
        "feeding_ph",
        "digester_1_ph",
        "balloon_1_position",
        "remarks",
        "unique_id"
    ];

    $table_details = [
        $table . " , (SELECT @a:= " . $start . ") AS a ",
        $columns
    ];

    $where = " is_delete = '0' ";

    // 🔹 Apply custom filters
    $from_date  = isset($_POST['from_date']) ? trim($_POST['from_date']) : '';
    $to_date    = isset($_POST['to_date']) ? trim($_POST['to_date']) : '';
    $project_id = isset($_POST['project_id']) ? trim($_POST['project_id']) : '';

    if ($from_date && $to_date) {
        $where .= " AND entry_date BETWEEN '" . $from_date . "' AND '" . $to_date . "' ";
    } elseif ($from_date) {
        $where .= " AND entry_date >= '" . $from_date . "' ";
    } elseif ($to_date) {
        $where .= " AND entry_date <= '" . $to_date . "' ";
    }

    if ($project_id) {
        $where .= " AND project_id = '" . $project_id . "' ";
    }

    // 🔹 DataTable built-in search
    $order_column = $_POST["order"][0]["column"];
    $order_dir    = $_POST["order"][0]["dir"];
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);
    $search       = datatable_searching($search, $columns);

    if ($search) {
        if ($where) $where .= " AND ";
        $where .= $search;
    }

    $sql_function = "SQL_CALC_FOUND_ROWS";

    $result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
    $total_records = total_records();

    if ($result->status) {
        $res_array = $result->data;

        foreach ($res_array as $key => $value) {
            $value['entry_date'] = disdate($value['entry_date']);
            $value['project_id'] = get_project_name($value['project_id'])[0]['label'];

            $btn_update = btn_update($folder_name, $value['unique_id']);
            $btn_delete = btn_delete($folder_name, $value['unique_id']);

            $value['unique_id'] = $btn_update . $btn_delete;
            $data[] = array_values($value);
        }

        echo json_encode([
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $data,
            "testing"         => $result->sql
        ]);
    } else {
        print_r($result);
    }
    break;

    case 'delete':
        $unique_id = $_POST['unique_id'];
        $columns = ["is_delete" => 1];
        $update_where = ["unique_id" => $unique_id];

        $action_obj = $pdo->update($table, $columns, $update_where);

        if ($action_obj->status) {
            $msg = "success_delete";
        } else {
            $msg = "error";
        }

        echo json_encode([
            "status" => $action_obj->status,
            "data"   => $action_obj->data,
            "error"  => $action_obj->error,
            "msg"    => $msg,
            "sql"    => $action_obj->sql
        ]);
        break;

    case 'user_type_options':
        $user_type = $_POST['user_type'];
        $user_type_options = under_user_type($user_type);
        $user_type_options = select_option($user_type_options, "Select");
        echo $user_type_options;
        break;
        
     case 'check_duplicate_entry':
    $project_id = $_POST['project_id'];
    $entry_date = $_POST['entry_date'];

    // Build your select using pdo->select()
    $columns = ["COUNT(*) AS count"];
    $where = [
        "project_id" => $project_id,
        "entry_date" => $entry_date,
        "is_delete"  => 0
    ];

    $result = $pdo->select([$table, $columns], $where);

    if ($result->status && !empty($result->data)) {
        $count = (int) $result->data[0]['count'];
        echo json_encode([
            "status" => 1,
            "exists" => $count > 0,
            "count"  => $count
        ]);
    } else {
        echo json_encode([
            "status" => 0,
            "exists" => false,
            "error"  => $result->error ?? "Query failed"
        ]);
    }
    break;
   

    default:
        break;
}
?>
