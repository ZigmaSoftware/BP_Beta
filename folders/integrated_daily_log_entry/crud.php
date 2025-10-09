<?php 

// Get folder Name From Current Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Table Name
$table             = "integrated_dailylogsheet_entry";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include '../../config/dbconfig_weighbridge.php';


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
    // Save/Update into integrated_dailylogsheet_entry
    $company_name     = $_POST["company_name"]     ?? "";
    $project_name     = $_POST["project_name"]     ?? "";
    $application_type = $_POST["application_type"] ?? "";
    $is_active        = $_POST["is_active"]        ?? 1;
    $unique_id        = $_POST["unique_id"]        ?? ""; // if present → UPDATE

    // Dynamic entry form values:
    $values = $_POST['values'] ?? []; // expects values[date_field], values[wet_mix_corp], etc.

    // Map date_field -> entry_date (Y-m-d)
    $entry_date = null;
    if (!empty($values['date_field'])) {
        $ts = strtotime($values['date_field']);
        if ($ts) $entry_date = date('Y-m-d', $ts);
    }

    // Map week_field -> week_no (integer)
    $week_no = null;
    if (!empty($values['week_field'])) {
        if (preg_match('/W(\d{1,2})/', $values['week_field'], $m)) {
            $week_no = (int)$m[1];
        } elseif (ctype_digit((string)$values['week_field'])) {
            $week_no = (int)$values['week_field'];
        }
    }

    // Whitelist of entry numeric/text columns you actually store
    $entry_columns = [
        "automated_weighbridge",
        "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp",
        "wet_mix_bwg","dry_mix_bwg","wet_segregated_bwg","complete_mix_bwg",
        "total_waste_actual","total_waste_reported","organic_waste_feed",
        "recycles_generated","rejects_dry_segregation","rejects_wet_segregation",
        "total_inert_disposed","total_rdf_generation","rdf_sold","rdf_stock",
        "slurry_disposed","flare_hrs","cbg_compressor_hrs","raw_biogas_produced",
        "biogas_flared","captive_consumption_gas","digester_temp","fos_tac_ratio",
        "ph_value","cbg_production_kg","cbg_captive_vehicle","cbg_sold_vehicle",
        "cbg_sold_cascades","cbg_sold_pipeline","cbg_total_sold","cbg_stock",
        "manure_production","manure_sold","manure_stock","plant_incharge","remarks"
    ];

    // Build payload
    $columns = [
        "company_name"     => $company_name,
        "project_name"     => $project_name,
        "application_type" => $application_type,
        "is_active"        => $is_active,
        "is_delete"        => 0
    ];
    if ($entry_date !== null) $columns["entry_date"] = $entry_date;
    if ($week_no   !== null) $columns["week_no"]     = $week_no;

    foreach ($entry_columns as $col) {
        if (array_key_exists($col, $values)) {
            $columns[$col] = $values[$col];
        }
    }

    // Insert or Update
    if (!empty($unique_id)) {
        $action_obj = $pdo->update($table, $columns, ["unique_id"=>$unique_id, "is_delete"=>0]);
        $msg = "update";
    } else {
        $columns["unique_id"] = unique_id($prefix);
        $action_obj = $pdo->insert($table, $columns);
        $msg = "create";
    }

    echo json_encode([
        "status" => (bool)$action_obj->status,
        "data"   => $action_obj->data,
        "error"  => $action_obj->error,
        "msg"    => $msg,
        "sql"    => $action_obj->sql
    ]);
    break;

   case 'datatable':
    $search  = $_POST['search']['value'] ?? "";
    $length  = $_POST['length'] ?? 10;
    $start   = $_POST['start']  ?? 0;
    $draw    = $_POST['draw']   ?? 1;
    $limit   = ($length == '-1') ? "" : $length;

    // Filters from client
    $from_date       = trim($_POST['from_date'] ?? '');
    $to_date         = trim($_POST['to_date']   ?? '');
    $company_id_flt  = trim($_POST['company_id'] ?? '');
    $project_id_flt  = trim($_POST['project_id'] ?? '');
    $app_type_flt    = trim($_POST['application_type'] ?? '');

    // Header order: #, Company, Project, Type, Action
    $columns = [
        "@a:=@a+1 s_no",
        "company_name",
        "project_name",
        "application_type",
        "unique_id"
    ];

    $table_details = [$table." , (SELECT @a:= ".$start.") AS a ", $columns];
    $where         = "is_delete = '0'";

    // Apply filters
    if ($from_date !== '' && $to_date !== '') {
        $where .= " AND entry_date BETWEEN '".$from_date."' AND '".$to_date."'";
    } elseif ($from_date !== '') {
        $where .= " AND entry_date >= '".$from_date."'";
    } elseif ($to_date !== '') {
        $where .= " AND entry_date <= '".$to_date."'";
    }

    if ($company_id_flt !== '') {
        $where .= " AND company_name = '".$company_id_flt."'";
    }
    if ($project_id_flt !== '') {
        $where .= " AND project_name = '".$project_id_flt."'";
    }
    if ($app_type_flt !== '') {
        $where .= " AND application_type = '".$app_type_flt."'";
    }

    // DataTable built-in sort/search
    $order_column = $_POST["order"][0]["column"] ?? 0;
    $order_dir    = $_POST["order"][0]["dir"]    ?? "asc";
    $order_by     = datatable_sorting($order_column, $order_dir, $columns);

    $search_sql   = datatable_searching($search, $columns);
    if ($search_sql) $where .= " AND ".$search_sql;

    $result        = $pdo->select($table_details, $where, $limit, $start, $order_by, "SQL_CALC_FOUND_ROWS");
    $total_records = total_records();

    if ($result->status) {
        $rows = [];
        foreach ($result->data as $value) {
            $btn_update = btn_update($folder_name, $value['unique_id']);
            $btn_delete = btn_delete($folder_name, $value['unique_id']);

            $display_company = company_name($value['company_name'])[0]['company_name'] ?? $value['company_name'];
            $display_project = project_name($value['project_name'])[0]['project_name'] ?? $value['project_name'];
            $display_type    = disname($value['application_type']);

            $rows[] = [
                $value['s_no'],
                $display_company,
                $display_project,
                $display_type,
                $btn_update . $btn_delete
            ];
        }

        echo json_encode([
            "draw"            => intval($draw),
            "recordsTotal"    => intval($total_records),
            "recordsFiltered" => intval($total_records),
            "data"            => $rows
        ]);
    } else {
        echo json_encode([
            "draw"            => intval($draw),
            "recordsTotal"    => 0,
            "recordsFiltered" => 0,
            "data"            => [],
            "error"           => $result->error ?? "query_failed",
            "sql"             => $result->sql   ?? ""
        ]);
    }
    break;




    case 'delete':
    $unique_id = $_POST['unique_id'] ?? '';
    if (!$unique_id) {
        echo json_encode([
            "status" => false,
            "msg"    => "Missing unique_id",
        ]);
        break;
    }
    // Soft delete
    $action_obj = $pdo->update($table, ["is_delete" => 1], ["unique_id" => $unique_id]);
    echo json_encode([
        "status" => (bool)$action_obj->status,
        "msg"    => $action_obj->status ? "Deleted successfully" : "Delete failed",
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
        
    case 'application_type':
    $company_id = $_POST['company_id'];
    $project_id = $_POST['project_id'];

    $application_type = get_application_type_by_project($project_id, $company_id);

    $options = '<option value="">Select Type</option>';
    if ($application_type) {
        $types = explode(",", $application_type); // in case multiple stored
        foreach ($types as $type) {
            $type = trim($type);
            $options .= '<option value="'.$type.'">'.$type.'</option>';
        }
    }

    echo $options;
    break;
    
    case 'dailylogsheet_data':
    // Log the incoming POST data
    error_log("POST: " . print_r($_POST, true) . "\n", 3, "doc_logs.txt");

    $company_id       = $_POST['company_id'];
    $project_id       = $_POST['project_id'];
    $application_type = $_POST['application_type'];

    // Checkbox fields to fetch
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

    $where = [
        "company_name"     => $company_id,
        "project_name"     => $project_id,
        "application_type" => $application_type,
        "is_active"        => 1,
        "is_delete"        => 0
    ];

    $result = $pdo->select(["integrated_dailylogsheet_master", $checkbox_fields], $where);

    // Log the result for debugging
    error_log("Result: " . print_r($result, true) . "\n", 3, "doc_logs.txt");

    if ($result->status && !empty($result->data)) {
        echo json_encode([
            "status" => true,
            "data"   => $result->data[0]
        ]);
    } else {
        echo json_encode([
            "status" => false,
            "data"   => []
        ]);
    }
    break;

    case 'get_entry':
    $unique_id = $_POST['unique_id'] ?? '';
    if (!$unique_id) {
        echo json_encode(["status"=>false, "error"=>"missing_unique_id"]);
        break;
    }

    // Columns you may want to prefill
    $cols = [
        "company_name","project_name","application_type",
        "entry_date","week_no",
        "automated_weighbridge",
        "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp",
        "wet_mix_bwg","dry_mix_bwg","wet_segregated_bwg","complete_mix_bwg",
        "total_waste_actual","total_waste_reported","organic_waste_feed",
        "recycles_generated","rejects_dry_segregation","rejects_wet_segregation",
        "total_inert_disposed","total_rdf_generation","rdf_sold","rdf_stock",
        "slurry_disposed","flare_hrs","cbg_compressor_hrs","raw_biogas_produced",
        "biogas_flared","captive_consumption_gas","digester_temp","fos_tac_ratio",
        "ph_value","cbg_production_kg","cbg_captive_vehicle","cbg_sold_vehicle",
        "cbg_sold_cascades","cbg_sold_pipeline","cbg_total_sold","cbg_stock",
        "manure_production","manure_sold","manure_stock","plant_incharge","remarks"
    ];

    $res = $pdo->select([$table, $cols], ["unique_id"=>$unique_id, "is_delete"=>0]);
    if ($res->status && !empty($res->data)) {
        echo json_encode(["status"=>true, "data"=>$res->data[0]]);
    } else {
        echo json_encode(["status"=>false, "data"=>[]]);
    }
    break;
    
    // case 'fetch_report':
    // header('Content-Type: application/json');

    // $from = trim($_POST['from'] ?? '');
    // $to   = trim($_POST['to']   ?? '');
    // $comp = trim($_POST['company'] ?? '');
    // $proj = trim($_POST['project'] ?? '');
    // $app  = trim($_POST['app'] ?? '');

    // // Canonical order & labels (aligned to your current report)
    // $order = [
    //     'entry_date','project_name','week_no',
    //     'automated_weighbridge',
    //     'dry_mix_corp','wet_mix_corp','wet_segregated_corp','complete_mix_corp',
    //     'wet_mix_bwg','dry_mix_bwg','wet_segregated_bwg','complete_mix_bwg',
    //     'total_waste_actual','total_waste_reported','organic_waste_feed',
    //     'recycles_generated','rejects_dry_segregation','rejects_wet_segregation',
    //     'total_inert_disposed','total_rdf_generation','rdf_sold','rdf_stock',
    //     'slurry_disposed','flare_hrs','cbg_compressor_hrs','raw_biogas_produced',
    //     'biogas_flared','captive_consumption_gas','digester_temp','fos_tac_ratio',
    //     'ph_value','cbg_production_kg','cbg_captive_vehicle','cbg_sold_vehicle',
    //     'cbg_sold_cascades','cbg_sold_pipeline','cbg_total_sold','cbg_stock',
    //     'manure_production','manure_sold','manure_stock','plant_incharge','remarks'
    // ];

    // $labels = [
    //     'entry_date' => 'Date',
    //     'project_name' => 'Project',
    //     'week_no'    => 'Week No',
    //     'automated_weighbridge' => 'Automated Weighbridge',
    //     'dry_mix_corp' => 'Dry Mix (Corp)',
    //     'wet_mix_corp' => 'Wet Mix (Corp)',
    //     'wet_segregated_corp' => 'Wet Segregated (Corp)',
    //     'complete_mix_corp' => 'Complete Mix (Corp)',
    //     'wet_mix_bwg' => 'Wet Mix (BWG)',
    //     'dry_mix_bwg' => 'Dry Mix (BWG)',
    //     'wet_segregated_bwg' => 'Wet Segregated (BWG)',
    //     'complete_mix_bwg' => 'Complete Mix (BWG)',
    //     'total_waste_actual' => 'Total Waste Actual',
    //     'total_waste_reported' => 'Total Waste Reported',
    //     'organic_waste_feed' => 'Organic Waste Feed',
    //     'recycles_generated' => 'Recycles Generated',
    //     'rejects_dry_segregation' => 'Rejects Dry',
    //     'rejects_wet_segregation' => 'Rejects Wet',
    //     'total_inert_disposed' => 'Total Inert Disposed',
    //     'total_rdf_generation' => 'RDF Generation',
    //     'rdf_sold' => 'RDF Sold',
    //     'rdf_stock' => 'RDF Stock',
    //     'slurry_disposed' => 'Slurry Disposed',
    //     'flare_hrs' => 'Flare Hours',
    //     'cbg_compressor_hrs' => 'CBG Compressor Hrs',
    //     'raw_biogas_produced' => 'Raw Biogas Produced',
    //     'biogas_flared' => 'Biogas Flared',
    //     'captive_consumption_gas' => 'Captive Gas Consumption',
    //     'digester_temp' => 'Digester Temp',
    //     'fos_tac_ratio' => 'FOS:TAC',
    //     'ph_value' => 'pH',
    //     'cbg_production_kg' => 'CBG Production',
    //     'cbg_captive_vehicle' => 'CBG Captive Vehicle',
    //     'cbg_sold_vehicle' => 'CBG Sold Vehicle',
    //     'cbg_sold_cascades' => 'CBG Sold Cascades',
    //     'cbg_sold_pipeline' => 'CBG Sold Pipeline',
    //     'cbg_total_sold' => 'CBG Total Sold',
    //     'cbg_stock' => 'CBG Stock',
    //     'manure_production' => 'Manure Production',
    //     'manure_sold' => 'Manure Sold',
    //     'manure_stock' => 'Manure Stock',
    //     'plant_incharge' => 'Plant Incharge',
    //     'remarks' => 'Remarks'
    // ];

    // // Resolve MASTER config for selected Company/Project/AppType
    // $masterWhere = "is_delete = 0";
    // if ($comp !== '') $masterWhere .= " AND company_name = '".$comp."'";
    // if ($proj !== '') $masterWhere .= " AND project_name = '".$proj."'";
    // if ($app  !== '') $masterWhere .= " AND application_type = '".$app."'";
    // $masterWhere .= " ORDER BY id DESC LIMIT 1";

    // $mres = $pdo->select(['integrated_dailylogsheet_master', ['*']], $masterWhere);
    // if (!$mres->status || empty($mres->data)) {
    //     echo json_encode(['status'=>false,'message'=>'No master configuration found.']);
    //     break;
    // }
    // $m = $mres->data[0];

    // // Build enabled columns map from MASTER flags
    // $enabled = [];
    // if (intval($m['date_field'] ?? 0) === 1) $enabled['entry_date'] = true;
    // $enabled['project_name'] = true;   
    // if (intval($m['week_field'] ?? 0) === 1) $enabled['week_no']    = true;

    // foreach ($order as $k) {
    //     if (in_array($k, ['entry_date','week_no'], true)) continue;
    //     if (array_key_exists($k, $m) && intval($m[$k]) === 1) $enabled[$k] = true;
    // }

    // // Columns payload (S.No always first)
    // $columns = [['key'=>'__sno','label'=>'S.No']];
    // foreach ($order as $k) {
    //     if (!empty($enabled[$k])) $columns[] = ['key'=>$k,'label'=>$labels[$k] ?? $k];
    // }

    // if (count($columns) === 1) {
    //     echo json_encode(['status'=>false,'message'=>'No fields enabled for this project/type.']);
    //     break;
    // }

    // // Prepare select cols for ENTRY (ensure entry_date present for sort/format)
    // $entryCols = array_values(array_filter(array_map(function($c){
    //     return $c['key'] === '__sno' ? null : $c['key'];
    // }, $columns)));
    // if (!in_array('entry_date', $entryCols, true)) $entryCols[] = 'entry_date';
    // if (!in_array('project_name', $entryCols, true))  $entryCols[] = 'project_name'; 

    // // Build WHERE for ENTRY
    // $ew = "is_delete = 0";
    // if ($comp !== '') $ew .= " AND company_name = '".$comp."'";
    // if ($proj !== '') $ew .= " AND project_name = '".$proj."'";
    // if ($app  !== '') $ew .= " AND application_type = '".$app."'";
    // if ($from !== '') $ew .= " AND entry_date >= '".$from."'";
    // if ($to   !== '') $ew .= " AND entry_date <= '".$to."'";
    // $ew .= " ORDER BY entry_date ASC, id ASC";

    // $eres = $pdo->select(['integrated_dailylogsheet_entry', $entryCols], $ew);
    // $rows = ($eres->status && !empty($eres->data)) ? $eres->data : [];
    // if (!empty($rows)) {
    // foreach ($rows as &$r) {
    //     if (isset($r['project_name']) && $r['project_name'] !== '') {
    //         // Your helper already used elsewhere in datatable case
    //         $p = project_name($r['project_name']);
    //         if (is_array($p) && isset($p[0]['project_name'])) {
    //             $r['project_name'] = $p[0]['project_name'];
    //         }
    //     }
    //     }
    //     unset($r); // break reference
    // }

    // echo json_encode([
    //     'status'  => true,
    //     'columns' => $columns,
    //     'rows'    => $rows
    // ]);
    // break;
    
    case 'fetch_report':
    header('Content-Type: application/json');

    $from = trim($_POST['from'] ?? '');
    $to   = trim($_POST['to']   ?? '');
    $comp = trim($_POST['company'] ?? '');
    $proj = trim($_POST['project'] ?? '');
    $app  = trim($_POST['app'] ?? '');

    // Canonical order & labels (aligned to your current report)
    $order = [
        'entry_date','project_name','week_no',
        'automated_weighbridge',
        'dry_mix_corp','wet_mix_corp','wet_segregated_corp','complete_mix_corp',
        'wet_mix_bwg','dry_mix_bwg','wet_segregated_bwg','complete_mix_bwg',
        'total_waste_actual','total_waste_reported','organic_waste_feed',
        'recycles_generated','rejects_dry_segregation','rejects_wet_segregation',
        'total_inert_disposed','total_rdf_generation','rdf_sold','rdf_stock',
        'slurry_disposed','flare_hrs','cbg_compressor_hrs','raw_biogas_produced',
        'biogas_flared','captive_consumption_gas','digester_temp','fos_tac_ratio',
        'ph_value','cbg_production_kg','cbg_captive_vehicle','cbg_sold_vehicle',
        'cbg_sold_cascades','cbg_sold_pipeline','cbg_total_sold','cbg_stock',
        'manure_production','manure_sold','manure_stock','plant_incharge','remarks'
    ];

    $labels = [
        'entry_date' => 'Date',
        'project_name' => 'Project',
        'week_no'    => 'Week No',
        'automated_weighbridge' => 'Automated Weighbridge',
        'dry_mix_corp' => 'Dry Mix (Corp)',
        'wet_mix_corp' => 'Wet Mix (Corp)',
        'wet_segregated_corp' => 'Wet Segregated (Corp)',
        'complete_mix_corp' => 'Complete Mix (Corp)',
        'wet_mix_bwg' => 'Wet Mix (BWG)',
        'dry_mix_bwg' => 'Dry Mix (BWG)',
        'wet_segregated_bwg' => 'Wet Segregated (BWG)',
        'complete_mix_bwg' => 'Complete Mix (BWG)',
        'total_waste_actual' => 'Total Waste Actual',
        'total_waste_reported' => 'Total Waste Reported',
        'organic_waste_feed' => 'Organic Waste Feed',
        'recycles_generated' => 'Recycles Generated',
        'rejects_dry_segregation' => 'Rejects Dry',
        'rejects_wet_segregation' => 'Rejects Wet',
        'total_inert_disposed' => 'Total Inert Disposed',
        'total_rdf_generation' => 'RDF Generation',
        'rdf_sold' => 'RDF Sold',
        'rdf_stock' => 'RDF Stock',
        'slurry_disposed' => 'Slurry Disposed',
        'flare_hrs' => 'Flare Hours',
        'cbg_compressor_hrs' => 'CBG Compressor Hrs',
        'raw_biogas_produced' => 'Raw Biogas Produced',
        'biogas_flared' => 'Biogas Flared',
        'captive_consumption_gas' => 'Captive Gas Consumption',
        'digester_temp' => 'Digester Temp',
        'fos_tac_ratio' => 'FOS:TAC',
        'ph_value' => 'pH',
        'cbg_production_kg' => 'CBG Production',
        'cbg_captive_vehicle' => 'CBG Captive Vehicle',
        'cbg_sold_vehicle' => 'CBG Sold Vehicle',
        'cbg_sold_cascades' => 'CBG Sold Cascades',
        'cbg_sold_pipeline' => 'CBG Sold Pipeline',
        'cbg_total_sold' => 'CBG Total Sold',
        'cbg_stock' => 'CBG Stock',
        'manure_production' => 'Manure Production',
        'manure_sold' => 'Manure Sold',
        'manure_stock' => 'Manure Stock',
        'plant_incharge' => 'Plant Incharge',
        'remarks' => 'Remarks'
    ];

    // Show enabled-only columns only when a specific project is chosen
    $useMasterForColumns = ($proj !== '');

    // Load master for column trimming (only if specific project)
    $m = [];
    if ($useMasterForColumns) {
        $masterWhere = "is_delete = 0";
        if ($comp !== '') $masterWhere .= " AND company_name = '".$comp."'";
        if ($proj !== '') $masterWhere .= " AND project_name = '".$proj."'";
        if ($app  !== '') $masterWhere .= " AND application_type = '".$app."'";
        $masterWhere .= " ORDER BY id DESC LIMIT 1";

        $mres = $pdo->select(['integrated_dailylogsheet_master', ['*']], $masterWhere);
        $m = ($mres->status && !empty($mres->data)) ? $mres->data[0] : [];
    }

    // Build enabled map from the loaded master (for specific project case)
    $enabled = [];
    if (!empty($m)) {
        if (intval($m['date_field'] ?? 0) === 1) $enabled['entry_date'] = true;
        $enabled['project_name'] = true;
        if (intval($m['week_field'] ?? 0) === 1) $enabled['week_no']    = true;
        foreach ($order as $k) {
            if (in_array($k, ['entry_date','week_no'], true)) continue;
            if (array_key_exists($k, $m) && intval($m[$k]) === 1) $enabled[$k] = true;
        }
    }

    // === Columns payload ===
    $columns = [['key'=>'__sno','label'=>'S.No']];
    if ($useMasterForColumns && !empty($m)) {
        // Specific project: only enabled columns
        foreach ($order as $k) {
            if (!empty($enabled[$k])) {
                $columns[] = ['key'=>$k, 'label'=>($labels[$k] ?? $k)];
            }
        }
    } else {
        // Default / All Projects: show all columns
        foreach ($order as $k) {
            $columns[] = ['key'=>$k, 'label'=>($labels[$k] ?? $k)];
        }
    }

    // SELECT columns (whatever we're displaying) + keys needed for per-row NA logic
    $displayKeys = array_values(array_filter(array_map(function($c){
        return $c['key'] === '__sno' ? null : $c['key'];
    }, $columns)));

    // We need these for per-row master lookup in All Projects/default mode
    if (!in_array('company_name', $displayKeys, true))      $displayKeys[] = 'company_name';
    if (!in_array('application_type', $displayKeys, true))  $displayKeys[] = 'application_type';
    if (!in_array('entry_date', $displayKeys, true))        $displayKeys[] = 'entry_date';
    if (!in_array('project_name', $displayKeys, true))      $displayKeys[] = 'project_name';

    // WHERE for ENTRY
    $ew = "is_delete = 0";
    if ($comp !== '') $ew .= " AND company_name = '".$comp."'";
    if ($proj !== '') $ew .= " AND project_name = '".$proj."'";
    if ($app  !== '') $ew .= " AND application_type = '".$app."'";
    if ($from !== '') $ew .= " AND entry_date >= '".$from."'";
    if ($to   !== '') $ew .= " AND entry_date <= '".$to."'";
    $ew .= " ORDER BY entry_date ASC, id ASC";

    $eres = $pdo->select(['integrated_dailylogsheet_entry', $displayKeys], $ew);
    $rows = ($eres->status && !empty($eres->data)) ? $eres->data : [];

    // Cache for per-row master flags in All Projects/default
    $masterCache = []; // key: comp|proj|app  -> ['enabled'=>map]

    // Normalize + fill NA / - per rules
    if (!empty($rows)) {
        foreach ($rows as &$r) {
            // Preserve raw project id for master lookup BEFORE mapping to display name
            $rowProjectId = $r['project_name'] ?? '';

            // Map project id/code to display name (safe)
            if (isset($r['project_name']) && $r['project_name'] !== '') {
                $p = project_name($r['project_name']);
                if (is_array($p) && isset($p[0]['project_name'])) {
                    $r['project_name'] = $p[0]['project_name'];
                }
            }

            // Determine enabled map for this row
            $rowEnabled = null;

            if ($useMasterForColumns && !empty($m)) {
                // Specific project path: we already trimmed columns; NA not needed.
                $rowEnabled = $enabled;
            } else {
                // All Projects/default path: fetch master for this row to decide NA
                $mc = ($r['company_name'] ?? $comp) . '|' . $rowProjectId . '|' . ($r['application_type'] ?? $app);
                if (!isset($masterCache[$mc])) {
                    $mw = "is_delete=0";
                    if (!empty($r['company_name'] ?? $comp))     $mw .= " AND company_name = '".($r['company_name'] ?? $comp)."'";
                    if (!empty($rowProjectId))                   $mw .= " AND project_name = '".$rowProjectId."'";
                    if (!empty($r['application_type'] ?? $app))  $mw .= " AND application_type = '".($r['application_type'] ?? $app)."'";
                    $mw .= " ORDER BY id DESC LIMIT 1";

                    $mr = $pdo->select(['integrated_dailylogsheet_master', ['*']], $mw);
                    $rowMap = [];
                    if ($mr->status && !empty($mr->data[0])) {
                        $mm = $mr->data[0];
                        if (intval($mm['date_field'] ?? 0) === 1) $rowMap['entry_date'] = true;
                        $rowMap['project_name'] = true;
                        if (intval($mm['week_field'] ?? 0) === 1) $rowMap['week_no']    = true;
                        foreach ($order as $k2) {
                            if (in_array($k2, ['entry_date','week_no'], true)) continue;
                            if (array_key_exists($k2, $mm) && intval($mm[$k2]) === 1) $rowMap[$k2] = true;
                        }
                    }
                    $masterCache[$mc] = ['enabled' => $rowMap];
                }
                $rowEnabled = $masterCache[$mc]['enabled'];
            }

            // Fill values per rules across displayed columns
            foreach ($columns as $cdef) {
                $k = $cdef['key'];
                if ($k === '__sno' || $k === 'entry_date') continue; // date stays raw for JS
                // If specific project view: only enabled cols are present; fill '-' for null/empty
                if ($useMasterForColumns && !empty($m)) {
                    if (!array_key_exists($k, $r) || $r[$k] === null || $r[$k] === '') {
                        $r[$k] = '-';
                    }
                } else {
                    // All Projects/default: decide NA vs '-'
                    $isEnabled = !empty($rowEnabled[$k]);
                    if (!$isEnabled) {
                        $r[$k] = 'NA';
                    } else {
                        if (!array_key_exists($k, $r) || $r[$k] === null || $r[$k] === '') {
                            $r[$k] = '-';
                        }
                    }
                }
            }
        }
        unset($r);
    }

    // Remove helper columns from output rows if they weren't part of displayed columns
    // (keep company_name/application_type only if they are part of $columns)
    $displaySet = array_flip(array_map(function($c){ return $c['key']; }, $columns));
    foreach ($rows as &$r) {
        foreach (['company_name','application_type'] as $aux) {
            if (!isset($displaySet[$aux]) && isset($r[$aux])) unset($r[$aux]);
        }
    }
    unset($r);

    echo json_encode([
        'status'  => true,
        'columns' => $columns,
        'rows'    => $rows
    ]);
    break;


    case 'get_last_rdf_stock':
    $company_id       = $_POST['company_id'] ?? '';
    $project_id       = $_POST['project_id'] ?? '';
    $application_type = $_POST['application_type'] ?? '';

    $where = "is_delete = 0";
    if ($company_id)       $where .= " AND company_name = '".$company_id."'";
    if ($project_id)       $where .= " AND project_name = '".$project_id."'";
    if ($application_type) $where .= " AND application_type = '".$application_type."'";
    $where .= " ORDER BY entry_date DESC, id DESC LIMIT 1";

    $res = $pdo->select(['integrated_dailylogsheet_entry', ['rdf_stock']], $where);
    if ($res->status && !empty($res->data)) {
        echo json_encode(['status'=>true,'data'=>$res->data[0]]);
    } else {
        echo json_encode(['status'=>false,'data'=>['rdf_stock'=>0]]);
    }
    break;
    
    
    case 'get_last_cbg_stock':
    $company_id       = $_POST['company_id'] ?? '';
    $project_id       = $_POST['project_id'] ?? '';
    $application_type = $_POST['application_type'] ?? '';

    $where = "is_delete = 0";
    if ($company_id)       $where .= " AND company_name = '".$company_id."'";
    if ($project_id)       $where .= " AND project_name = '".$project_id."'";
    if ($application_type) $where .= " AND application_type = '".$application_type."'";
    $where .= " ORDER BY entry_date DESC, id DESC LIMIT 1";

    $res = $pdo->select(['integrated_dailylogsheet_entry', ['cbg_stock']], $where);
    if ($res->status && !empty($res->data)) {
        echo json_encode(['status'=>true,'data'=>$res->data[0]]);
    } else {
        echo json_encode(['status'=>false,'data'=>['cbg_stock'=>0]]);
    }
    break;
    
    case 'get_last_manure_stock':
    $company_id       = $_POST['company_id'] ?? '';
    $project_id       = $_POST['project_id'] ?? '';
    $application_type = $_POST['application_type'] ?? '';

    $where = "is_delete = 0";
    if ($company_id)       $where .= " AND company_name = '".$company_id."'";
    if ($project_id)       $where .= " AND project_name = '".$project_id."'";
    if ($application_type) $where .= " AND application_type = '".$application_type."'";
    $where .= " ORDER BY entry_date DESC, id DESC LIMIT 1";

    $res = $pdo->select(['integrated_dailylogsheet_entry', ['manure_stock']], $where);
    if ($res->status && !empty($res->data)) {
        echo json_encode(['status'=>true,'data'=>$res->data[0]]);
    } else {
        echo json_encode(['status'=>false,'data'=>['manure_stock'=>0]]);
    }
    break;
    
  case 'fetch_weighbridge_data':
    header('Content-Type: application/json');
    $entry_date = trim($_POST['entry_date'] ?? '');

    if ($entry_date == '') {
        echo json_encode(['status' => false, 'error' => 'Missing date']);
        break;
    }

    try {
        $sql = "
            SELECT
                SUM(DRY_WASTE) AS dry_mix_corp,
                SUM(WET_WASTE) AS wet_mix_corp,
                SUM(MIX_WASTE_NETWEIGHT) AS mix_waste_total
            FROM (
                -- Joined records (ticket_weighbridge_bp)
                SELECT
                    t1.TicketNo,
                    t1.VehicleNo,
                    t1.Date,
                    t1.Time,
                    SUM(CASE WHEN t2.MaterialCode = 'DRY WASTE' THEN t2.Weight ELSE 0 END) AS DRY_WASTE,
                    SUM(CASE WHEN t2.MaterialCode = 'WET WASTE' THEN t2.Weight ELSE 0 END) AS WET_WASTE,
                    SUM(CASE WHEN t2.MaterialCode = '3' THEN t2.Weight ELSE 0 END) AS MIX_WASTE_NETWEIGHT
                FROM newweighbridge_bp t1
                JOIN ticket_weighbridge_bp t2
                    ON t1.TicketNo = t2.TicketNo
                WHERE DATE(t1.Date) = :entry_date
                GROUP BY t1.TicketNo

                UNION ALL

                -- Direct weighbridge entries
                SELECT
                    t1.TicketNo,
                    t1.VehicleNo,
                    t1.Date,
                    t1.Time,
                    0 AS DRY_WASTE,
                    0 AS WET_WASTE,
                    t1.NetWeight AS MIX_WASTE_NETWEIGHT
                FROM newweighbridge_bp t1
                WHERE DATE(t1.Date) = :entry_date
                  AND t1.State NOT IN ('FT','FMT')
                  AND t1.MaterialName NOT IN ('DRY WASTE', 'WET WASTE')
            ) AS combined;
        ";

        // Log for debugging
        error_log("fetch_weighbridge_data SQL for date {$entry_date}\n{$sql}\n", 3, "login.log");

        $stmt = $conn->prepare($sql);
        $stmt->execute([':entry_date' => $entry_date]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

        // Values
        $dry_mix_corp        = round($row['dry_mix_corp'] ?? 0, 2);
        $wet_mix_corp        = round($row['wet_mix_corp'] ?? 0, 2);
        $mix_waste_total     = round($row['mix_waste_total'] ?? 0, 2);

        // Mapping to required fields
        $dry_mix_bwg         = $mix_waste_total; // ← your requirement
        $wet_segregated_corp = 0;
        $wet_mix_bwg         = 0;
        $wet_segregated_bwg  = 0;

        echo json_encode([
            'status' => true,
            'data'   => [
                'dry_mix_corp'        => $dry_mix_corp,
                'wet_mix_corp'        => $wet_mix_corp,
                'dry_mix_bwg'         => $dry_mix_bwg,
                'wet_segregated_corp' => $wet_segregated_corp,
                'wet_mix_bwg'         => $wet_mix_bwg,
                'wet_segregated_bwg'  => $wet_segregated_bwg
            ]
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => false, 'error' => $e->getMessage()]);
    }
    break;





    default:
        break;
}
?>
