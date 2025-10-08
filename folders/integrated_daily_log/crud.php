<?php 

// Get folder Name From Current Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Table Name
$table             = "integrated_dailylogsheet_master";

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

// Form Variables
$company_name       = "";
$project_name       = "";
$application_type   = "";
$is_active          = "";
$unique_id          = "";
$checkbox_fields    = [];
$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$prefix             = "";

// List of checkbox fields
$checkbox_fields = [
    "date_field","week_field","automated_weighbridge","dry_mix_corp","wet_mix_corp",
    "wet_segregated_corp","complete_mix_corp","wet_mix_bwg","dry_mix_bwg",
    "wet_segregated_bwg","complete_mix_bwg","total_waste_actual","total_waste_reported",
    "organic_waste_feed","recycles_generated","rejects_dry_segregation",
    "rejects_wet_segregation","total_inert_disposed","total_rdf_generation",
    "rdf_sold","rdf_stock","slurry_disposed","flare_hrs","cbg_compressor_hrs",
    "raw_biogas_produced","biogas_flared","captive_consumption_gas","digester_temp",
    "fos_tac_ratio","ph_value","cbg_production_kg","cbg_captive_vehicle",
    "cbg_sold_vehicle","cbg_sold_cascades","cbg_sold_pipeline","cbg_total_sold",
    "cbg_stock","manure_production","manure_sold","manure_stock","plant_incharge",
    "remarks"
];

switch ($action) {
    case 'createupdate':

    $company_name      = $_POST["company_name"];
    $project_name      = $_POST["project_name"];
    $application_type  = $_POST["application_type"];
    $is_active         = $_POST["is_active"];
    $unique_id         = $_POST["unique_id"];

    // Prepare checkbox data
    $checkbox_data = [];
    foreach ($checkbox_fields as $field) {
        $checkbox_data[$field] = isset($_POST['fields'][$field]) ? 1 : 0;
    }

    // Columns to insert/update
    $columns = array_merge([
        "company_name"     => $company_name,
        "project_name"     => $project_name,
        "application_type" => $application_type,
        "is_active"        => $is_active,
        "unique_id"        => unique_id($prefix)
    ], $checkbox_data);

    // === DUPLICATE CHECK FOR BOTH CREATE AND UPDATE ===
    $duplicate_where = [
        "company_name"     => $company_name,
        "project_name"     => $project_name,
        "application_type" => $application_type,
        "is_delete"        => 0
    ];

    $duplicate_check = $pdo->select([$table, ["unique_id"]], $duplicate_where);

    if ($duplicate_check->status && count($duplicate_check->data) > 0) {
        $duplicate_id = $duplicate_check->data[0]['unique_id'];

        // Prevent duplicate if it's a new entry OR updating a different record
        if (!$unique_id || $duplicate_id != $unique_id) {
            $json_array = [
                "status" => 0,
                "data"   => "",
                "error"  => "Duplicate entry found",
                "msg"    => "duplicate"
            ];
            echo json_encode($json_array);
            exit;
        }
    }

    // === UPDATE BEGINS ===
    if ($unique_id) {
        unset($columns['unique_id']);
        $update_where = ["unique_id" => $unique_id];
        $action_obj   = $pdo->update($table, $columns, $update_where);
    } else {
        // === INSERT BEGINS ===
        $action_obj = $pdo->insert($table, $columns);
    }

    if ($action_obj->status) {
        $status = $action_obj->status;
        $data   = $action_obj->data;
        $error  = "";
        $sql    = $action_obj->sql;
        $msg    = $unique_id ? "update" : "create";
    } else {
        $status = $action_obj->status;
        $data   = $action_obj->data;
        $error  = $action_obj->error;
        $sql    = $action_obj->sql;
        $msg    = "error";
    }

    $json_array = [
        "status" => $status,
        "data"   => $data,
        "error"  => $error,
        "msg"    => $msg,
        "sql"    => $sql
    ];
    echo json_encode($json_array);
    break;


    case 'datatable':
        $search  = $_POST['search']['value'];
        $length  = $_POST['length'];
        $start   = $_POST['start'];
        $draw    = $_POST['draw'];
        $limit   = $length;
        $data    = [];
        
        if($length == '-1') $limit = "";

        $columns = array_merge([
            "@a:=@a+1 s_no",
            "company_name",
            "project_name",
            "application_type",
            "is_active",
            "unique_id"
        ], $checkbox_fields);

        $table_details = [$table." , (SELECT @a:= ".$start.") AS a ", $columns];
        $where = "is_delete = '0'";
        
        // ✅ Filters
        if (!empty($_POST['company_id'])) {
            $company_id = $_POST['company_id'];
            $where .= " AND company_name = '$company_id'";
        }
        
        if (!empty($_POST['project_id'])) {
            $project_id = $_POST['project_id'];
            $where .= " AND project_name = '$project_id'";
        }
        
        if (!empty($_POST['application_type'])) {
            $application_type = $_POST['application_type'];
            $where .= " AND application_type = '$application_type'";
        }
        
        if (!empty($_POST['from_date']) && !empty($_POST['to_date'])) {
            $from_date = $_POST['from_date'];
            $to_date   = $_POST['to_date'];
            $where .= " AND entry_date BETWEEN '$from_date' AND '$to_date'";
        }


        $order_column = $_POST["order"][0]["column"];
        $order_dir    = $_POST["order"][0]["dir"];
        $order_by     = datatable_sorting($order_column,$order_dir,$columns);
        $search       = datatable_searching($search,$columns);

        if ($search) $where .= " AND ".$search;

        $sql_function  = "SQL_CALC_FOUND_ROWS";
        $result        = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records = total_records();

        if ($result->status) {
            $res_array  = $result->data;
            foreach ($res_array as $key => $value) {
                $btn_update = btn_update($folder_name, $value['unique_id']);
                $btn_toggle = ($value['is_active'] == 1)
                    ? btn_toggle_on($folder_name, $value['unique_id'])
                    : btn_toggle_off($folder_name, $value['unique_id']);

                $value['company_name']      = company_name($value['company_name'])[0]['company_name'];
                $value['project_name']      = project_name($value['project_name'])[0]['project_name'];
                $value['application_type']  = disname($value['application_type']);
                $value['is_active']         = is_active_show($value['is_active']);
                $value['unique_id']         = $btn_update . $btn_toggle;

                $data[] = array_values($value);
            }

            $json_array = [
                "draw"              => intval($draw),
                "recordsTotal"      => intval($total_records),
                "recordsFiltered"   => intval($total_records),
                "data"              => $data,
                "testing"           => $result->sql
            ];
        } else {
            print_r($result);
        }

        echo json_encode($json_array);
        break;

    case 'toggle':
        $unique_id = $_POST['unique_id'];
        $is_active = $_POST['is_active'];
        $columns   = ["is_active" => $is_active];
        $update_where = ["unique_id" => $unique_id];
        $action_obj = $pdo->update($table, $columns, $update_where);

        if ($action_obj->status) {
            $status = true;
            $msg = $is_active ? "Activated Successfully" : "Deactivated Successfully";
        } else {
            $status = false;
            $msg = "Toggle failed!";
        }

        echo json_encode([
            "status" => $status,
            "msg"    => $msg,
            "sql"    => $action_obj->sql,
            "error"  => $action_obj->error
        ]);
        break;

    case 'project_name':
        $company_id = $_POST['company_id'];
        $project    = $_POST['project'];
        $project_name_options  = get_project_name("", $company_id);
        $project_name_options  = select_option($project_name_options, "Select the Project Name", $project);
        echo $project_name_options;
        break;

    default:
        break;
}
?>
