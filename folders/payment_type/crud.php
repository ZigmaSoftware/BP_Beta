<?php 

// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table             = "payment_type";

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

$payment_name         = "";
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

switch ($action) {
    case 'createupdate':

        $payment_name         = $_POST["payment_name"];
		$description        = $_POST["description"];
        $is_active          = $_POST["is_active"];
        $unique_id          = $_POST["unique_id"];

        $update_where       = "";

        $columns            = [
            "payment_name"     => $payment_name,
            "description"   => $description,
            "is_active"     => $is_active,
            "created_user_id"     => $user_id,
            "created"     => $date,
            "unique_id"     => unique_id($prefix)
        ];

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = '(payment_name = "'.$payment_name.'" ) AND is_delete = 0  ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj         = $pdo->select($table_details,$select_where);

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
        // Update Begins
        if($unique_id) {

            unset($columns['unique_id']);
            unset($columns['created_user_id']);
            unset($columns['created']);
                $columns = [
                    "payment_name"        => $payment_name,
                    "description"       => $description,
                    "is_active"         => $is_active,
                    "updated_user_id"   => $user_id,
                    "updated"           => $date
                ];
            $update_where   = [
                "unique_id"     => $unique_id
            ];

            $action_obj     = $pdo->update($table,$columns,$update_where);

        // Update Ends
        } else {
        // Insert Begins
           
            $action_obj     = $pdo->insert($table,$columns);
        // Insert Ends
        }

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



case 'datatable':
    $search  = $_POST['search']['value'] ?? '';
    $length  = $_POST['length'] ?? 10;
    $start   = $_POST['start'] ?? 0;
    $draw    = $_POST['draw'] ?? 1;

    $columns = [
        "@a:=@a+1 s_no",
        "payment_name",
        "description",
        "is_active",
        "unique_id"
    ];

    $table_details = [$table . ", (SELECT @a:=" . intval($start) . ") AS a", $columns];
    $where = "is_delete = 0";

    $order_column = $_POST["order"][0]["column"] ?? 0;
    $order_dir    = $_POST["order"][0]["dir"] ?? 'asc';
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);
    $search_cond  = datatable_searching($search, $columns);

    if ($search_cond) $where .= " AND " . $search_cond;

    $result = $pdo->select($table_details, $where, $length, $start, $order_by, "SQL_CALC_FOUND_ROWS");
    $total_records = total_records();

    $data = [];

    if ($result->status) {
        foreach ($result->data as $row) {
            $desc = trim($row['description']) ?: '-';
            $btn_update = btn_update($folder_name, $row['unique_id']);
            $btn_toggle = ($row['is_active'] == 1)
                ? btn_toggle_on($folder_name, $row['unique_id'])
                : btn_toggle_off($folder_name, $row['unique_id']);

            $data[] = [
                $row['s_no'],
                $row['payment_name'],
                $desc,
                is_active_show($row['is_active']),
                $btn_update . $btn_toggle
            ];
        }
    }

    echo json_encode([
        "draw" => intval($draw),
        "recordsTotal" => intval($total_records),
        "recordsFiltered" => intval($total_records),
        "data" => $data
    ]);
    exit; // ✅ Prevent further output

    
    
 case 'toggle':
    $unique_id = $_POST['unique_id'];
    $is_active = $_POST['is_active'];

    $columns = [
        "is_active" => $is_active
    ];

    $update_where = [
        "unique_id" => $unique_id
    ];

    $action_obj = $pdo->update($table, $columns, $update_where);

    $status = $action_obj->status;
    $msg    = $status ? ($is_active ? "Activated Successfully" : "Deactivated Successfully") : "Toggle failed!";

    echo json_encode([
        "status" => $status,
        "msg"    => $msg,
        "sql"    => $action_obj->sql,
        "error"  => $action_obj->error
    ]);
    break;

    
    default:
        
        break;
}

?>