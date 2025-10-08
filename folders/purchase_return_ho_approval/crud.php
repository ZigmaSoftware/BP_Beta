<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table                                =  "bids_management";
$table_competition_bidders_details    =  "bids_management_competition_bidders_details";
$table_competition_oem_details        =  "bids_management_competition_oem_details";
$table_fee_security                   =  "bids_management_fee_security";
$table_payment_terms                  =  "bids_management_payment_terms";
$table_project_description            =  "bids_management_project_description";

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
$prefix             = "bidsmgnt";


$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$lead_id                 = "";
$entry_date              = "";
$lead_date               = "";
$funnel_id               = "";
$funnel_date             = "";
$bu_vertical             = "";
$me_name                 = "";
$hod                     = "";
$customer_name           = "";
$region                  = "";
$location                = "";
$purchase_dept           = "";
$project_name            = "";
$project_reference_no    = "";
$rfp_published           = "";
$start_date              = "";
$budget_alloted          = "";
$scheme                  = "";
$go                      = "";
$project_objective       = "";
$contract_period         = "";
$incharge_name           = "";
$incharge_contact        = "";
$incharge_email_id       = "";
$approval_officer        = "";
$officer_contact         = "";
$officer_email_id        = "";
$emd_value               = "";
$due_date                = "";
$msme_exempted           = "";
$emd_type                = "";
$dd                      = "";
$emd_validity            = "";
$tender_fee              = "";
$payment_mode            = "";
$remarks                 = "";
$pre_bid_close_date      = "";
$pre_bid_meet_date       = "";
$tech_close_date         = "";
$commercial_close_date   = "";
$po_expected_date        = "";     
$delivery_period         = "";
$install_period          = "";
$testing                 = "";
$uat                     = "";
$project_type            = "";              
$transaction_fee         = "";
$signoff                 = "";
$training                = "";
$delivery_percent        = "";
$install_percent         = "";
$security_deposit        = "";
$sd_mode                 = "";
$sd_validity             = "";
$pbg_validity            = "";
$oem_details             = "";
$exclusive_support       = "";
$dr_status               = "";
$justification           = "";
$oem_name                = "";
$product_details         = "";
$oem_name1               = "";
$product_details1        = "";
$oem_name2               = "";
$product_details2        = "";
$bid_name                = "";
$map_oem_name            = "";
$bid_name1               = "";
$map_oem_name1           = "";
$bid_name2               = "";
$map_oem_name2           = "";
$ld_delivery             = "";
$ld_install              = "";
$ld_sla                  = "";
$ld_slow                 = "";
$penalty_gap             = "";
$consignee               = "";
$single_multi_location   = "";
$site_in_state           = "";
$site_in_india           = "";
$margin_operation        = "";
$management_support      = "";
$points_noted            = "";
$performance_percent     = "";
$ho_approval             = "";

switch ($action) {
    case 'createupdate':

        $lead_id                 = $_POST["lead_id"];
       // $entry_date              = $_POST["entry_date"];
        $lead_date               = $_POST["lead_date"];
        $funnel_id               = $_POST["funnel_id"];
        $funnel_date             = $_POST["funnel_date"];
        $bu_vertical             = $_POST["bu_vertical"];
        $me_name                 = $_POST["me_name"];
        $hod                     = $_POST["hod"];
        // $customer_name           = $_POST["customer_name"];
        // $region                  = $_POST["region"];
        // $location                = $_POST["location"];
        $purchase_dept           = $_POST["purchase_dept"];
        $project_name            = $_POST["project_name"];
        $project_reference_no    = $_POST["project_reference_no"];
        $rfp_published           = $_POST["rfp_published"];
        $start_date              = $_POST["start_date"];
        $budget_alloted          = $_POST["budget_alloted"];
        $scheme                  = $_POST["scheme"];
        $go                      = $_POST["go"];
        $project_objective       = $_POST["project_objective"];
        $contract_period         = $_POST["contract_period"];
        $incharge_name           = $_POST["incharge_name"];
        $incharge_contact        = $_POST["incharge_contact"];
        $incharge_email_id       = $_POST["incharge_email_id"];
        $approval_officer        = $_POST["approval_officer"];
        $officer_contact         = $_POST["officer_contact"];
        $officer_email_id        = $_POST["officer_email_id"];
        $emd_value               = $_POST["emd_value"];
        $due_date                = $_POST["due_date"];
        $msme_exempted           = $_POST["msme_exempted"];
        $emd_type                = $_POST["emd_type"];
        $dd                      = $_POST["dd"];
        $emd_validity            = $_POST["emd_validity"];
        $tender_fee              = $_POST["tender_fee"];
        $payment_mode            = $_POST["payment_mode"];
        $remarks                 = $_POST["remarks"];
        $pre_bid_close_date      = $_POST["pre_bid_close_date"];
        $pre_bid_meet_date       = $_POST["pre_bid_meet_date"];
        $tech_close_date         = $_POST["tech_close_date"];
        $commercial_close_date   = $_POST["commercial_close_date"];
        $po_expected_date        = $_POST["po_expected_date"];
        $delivery_period         = $_POST["delivery_period"];
        $install_period          = $_POST["install_period"];
        $testing                 = $_POST["testing"];
        $uat                     = $_POST["uat"];
        $project_type            = $_POST["project_type"];             
        $transaction_fee         = $_POST["transaction_fee"];
        $signoff                 = $_POST["signoff"];
        $training                = $_POST["training"];
        $delivery_percent        = $_POST["delivery_percent"];
        $install_percent         = $_POST["install_percent"];
        $security_deposit        = $_POST["security_deposit"];
        $sd_mode                 = $_POST["sd_mode"];
        $sd_validity             = $_POST["sd_validity"];
        $pbg_validity            = $_POST["pbg_validity"];
        $oem_details             = $_POST["oem_details"];
        $exclusive_support       = $_POST["exclusive_support"];
        $dr_status               = $_POST["dr_status"];
        $justification           = $_POST["justification"];
        $ld_delivery             = $_POST["ld_delivery"];
        $ld_install              = $_POST["ld_install"];
        $ld_sla                  = $_POST["ld_sla"];
        $ld_slow                 = $_POST["ld_slow"];
        $penalty_gap             = $_POST["penalty_gap"];
        $consignee               = $_POST["consignee"];
        $single_multi_location   = $_POST["single_multi_location"];
        $site_in_state           = $_POST["site_in_state"];
        $site_in_india           = $_POST["site_in_india"];
        $margin_operation        = $_POST["margin_operation"];
        $management_support      = $_POST["management_support"];
        $points_noted            = $_POST["points_noted"];
        $unique_id               = $_POST["unique_id"];
        $performance_percent     = $_POST["performance_percent"]; 
        $ho_approval             = $_POST["ho_approval"];  
        if (isset($_POST['unique_id'])) {
            $unique_id                = $_POST["unique_id"];
            $main_unique_id           = $_POST['unique_id'];
        } else {
            $main_unique_id           = unique_id($prefix);
            $unique_id                = $main_unique_id;
        }


        $update_where       = "";

        $columns            = [

        "lead_unique_id"         => $lead_id,
        // "entry_date"             => $entry_date,
        "lead_date"              => $lead_date,
        "funnel_unique_id"       => $funnel_id,
        "funnel_date"            => $funnel_date,
        "bu_vertical"            => $bu_vertical,
        "me_name"                => $me_name,
        "hod"                    => $hod,
        // "customer_id"          => $customer_name,
        // "region"                 => $region,
        // "location"               => $location,
        "purchase_dept"          => $purchase_dept,
        "pre_bid_close_date"     => $pre_bid_close_date,
        "pre_bid_meet_date"      => $pre_bid_meet_date,
        "tech_close_date"        => $tech_close_date,
        "commercial_close_date"  => $commercial_close_date,
        "po_expected_date"       => $po_expected_date,
        "delivery_period"        => $delivery_period,
        "install_period"         => $install_period,
        "testing"                => $testing,
        "uat"                    => $uat,
        "signoff"                => $signoff,
        "training"               => $training,
        "oem_details"            => $oem_details,
        "exclusive_support"      => $exclusive_support,
        "dr_status"              => $dr_status,
        "justification"          => $justification,
        "ld_delivery"            => $ld_delivery,
        "ld_install"             => $ld_install,
        "ld_sla"                 => $ld_sla,
        "ld_slow"                => $ld_slow,
        "penalty_gap"            => $penalty_gap,
        "consignee"              => $consignee,
        "single_multi_location"  => $single_multi_location,
        "site_in_state"          => $site_in_state,
        "site_in_india"          => $site_in_india,
        "margin_operation"       => $margin_operation,
        "management_support"     => $management_support,
        "points_noted"           => $points_noted,
        "ho_approval"            => $ho_approval,
        "unique_id"              => $main_unique_id
        ];

         $columns_fee_security       = [
        "emd_value"              => $emd_value,
        "due_date"               => $due_date,
        "msme_exempted"          => $msme_exempted,
        "emd_type"               => $emd_type,
        "dd"                     => $dd,
        "emd_validity"           => $emd_validity,
        "tender_fee"             => $tender_fee,
        "payment_mode"           => $payment_mode,
        "remarks"                => $remarks,
        "transaction_fee"        => $transaction_fee,
        "unique_id"              => $main_unique_id
        ];

        $columns_payment_terms       = [
        "delivery_percent"       => $delivery_percent,
        "install_percent"        => $install_percent,
        "security_deposit"       => $security_deposit,
        "sd_mode"                => $sd_mode,
        "sd_validity"            => $sd_validity,
        "pbg_validity"           => $pbg_validity,
        "performance_percent"    => $performance_percent,
        "unique_id"              => $main_unique_id
        ];

        $columns_project_description  = [
        "project_name"           => $project_name,
        "project_type"           => $project_type,  
        "project_reference_no"   => $project_reference_no,
        "rfp_published"          => $rfp_published,
        "start_date"             => $start_date,
        "budget_alloted"         => $budget_alloted,
        "scheme"                 => $scheme,
        "go"                     => $go,
        "project_objective"      => $project_objective,
        "contract_period"        => $contract_period,
        "incharge_name"          => $incharge_name,
        "incharge_contact"       => $incharge_contact,
        "incharge_email_id"      => $incharge_email_id,
        "approval_officer"       => $approval_officer,
        "officer_contact"        => $officer_contact,
        "officer_email_id"       => $officer_email_id,
        "unique_id"              => $main_unique_id
        ];
        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'lead_unique_id = "'.$lead_id.'" AND funnel_unique_id ="'.$funnel_id.'"  AND is_delete = 0  ';

        // When Update Check without current id
        if ($unique_id) {
            $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
        }

        $action_obj_main     = $pdo->select($table_details,$select_where);

        $table_details_fee_security            = [
            $table_fee_security,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
         $table_details_payment_terms          = [
            $table_payment_terms,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
         $table_details_project_description     = [
            $table_project_description,
            [
                "COUNT(unique_id) AS count"
            ]
        ];

        $select_where_continue       = ' is_delete = 0  ';
        $select_sub_where_continue  = "";

        // When Update Check without current id
         if ($unique_id) {
            $select_where_continue   .= ' AND unique_id !="'.$unique_id.'" ';
            $select_sub_where_continue   .= ' unique_id ="'.$unique_id.'" ';
        }

        $action_obj_main                = $pdo->select($table_details,$select_where);
        $action_obj_fee_security        = $pdo->select($table_details_fee_security,$select_sub_where_continue);
        $action_obj_payment_terms       = $pdo->select($table_details_payment_terms,$select_sub_where_continue);
        $action_obj_project_description = $pdo->select($table_details_project_description,$select_sub_where_continue);


        // print_r($action_obj_project_description);

        if ($action_obj_main->status) {

        // if (($action_obj_main->status)&&($action_obj_fee_security->status)&&($action_obj_payment_terms->status)&&($action_obj_project_description->status)) {
            $status     = $action_obj_main->status;
            $data       = $action_obj_main->data;
            $error      = "";
            $sql        = $action_obj_main->sql;

        } else {

            $action_obj = $action_obj_main;

            // if(!$action_obj_main->status)          {$action_obj = $action_obj_main;}
            // else if(!$action_obj_fee_security->status)        {$action_obj = $action_obj_fee_security;}
            // else if(!$action_obj_payment_terms->status)       {$action_obj = $action_obj_payment_terms;}
            // else if(!$action_obj_project_description->status) {$action_obj = $action_obj_project_description;}
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
        }
        if ($data[0]["count"]) {
            $msg        = "already";
        } else if (($data[0]["count"] == 0) && ($msg != "error")) {
            // Update Begins
            if($unique_id) {

                unset($columns['unique_id']);
                // unset($columns_fee_security['unique_id']);
                // unset($columns_payment_terms['unique_id']);
                // unset($columns_project_description['unique_id']);

                $update_where   = [
                    "unique_id"     => $unique_id
                ];

                $action_obj_main                        = $pdo->update($table,$columns,$update_where);


                if (($action_obj_project_description->data[0]["count"] == 0) && ($action_obj_project_description->data[0]["count"] != "")) {

                    $action_obj_project_description     = $pdo->insert($table_project_description,$columns_project_description);

                    
                } else if ($action_obj_project_description->data[0]["count"] == 1) {
                    
                    $action_obj_project_description     = $pdo->update($table_project_description,$columns_project_description,$update_where);

                } 
                if(($action_obj_payment_terms->data[0]["count"] == 0) && ($action_obj_payment_terms->data[0]["count"] != "")) {

                    $action_obj_payment_terms     = $pdo->insert($table_payment_terms,$columns_payment_terms);

                    
                } else if ($action_obj_payment_terms->data[0]["count"] == 1) {
                    
                    $action_obj_payment_terms     = $pdo->update($table_payment_terms,$columns_payment_terms,$update_where);

                }
                if(($action_obj_fee_security->data[0]["count"] == 0) && ($action_obj_fee_security->data[0]["count"] != "")) {

                    $action_obj_fee_security     = $pdo->insert($table_fee_security,$columns_fee_security);

                    
                } else if ($action_obj_fee_security->data[0]["count"] == 1) {
                    
                    $action_obj_fee_security     = $pdo->update($table_fee_security,$columns_fee_security,$update_where);

                }

                // $action_obj_fee_security        = $pdo->update($table_fee_security,$columns_fee_security,$update_where);
                // $action_obj_payment_terms       = $pdo->update($table_payment_terms,$columns_payment_terms,$update_where);
                // $action_obj_project_description = $pdo->update($table_project_description,$columns_project_description,$update_where);
                            // Update Ends
            } else {

                // Insert Begins            
                $action_obj_main                = $pdo->insert($table,$columns);
                $action_obj_fee_security        = $pdo->insert($table_fee_security,$columns_fee_security);
                $action_obj_payment_terms       = $pdo->insert($table_payment_terms,$columns_payment_terms);
                $action_obj_project_description = $pdo->insert($table_project_description,$columns_project_description);
 
                // Insert Ends

                //print_r($action_obj_payment_terms);

            }

            if (($action_obj_main->status) && ($action_obj_fee_security->status) && ($action_obj_payment_terms->status) && ($action_obj_project_description->status)) {
                $status     = $action_obj_main->status;
                $data       = $action_obj_main->data;
                $error      = "";
                $sql        = $action_obj_main->sql;


                if ($unique_id) {
                    $msg        = "update";
                } else {
                    $msg        = "create";
                }
            } else {
            if(!$action_obj_main->status)          {$action_obj = $action_obj_main;}
            else if(!$action_obj_fee_security->status)        {$action_obj = $action_obj_fee_security;}
            else if(!$action_obj_payment_terms->status)       {$action_obj = $action_obj_payment_terms;}
            else if(!$action_obj_project_description->status) {$action_obj = $action_obj_project_description;}
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
        // DataTable Variables
        $search     = $_POST['search']['value'];
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
             "@a:=@a+1 s_no",
            "bid_no",
            "entry_date",
            "customer_id",
            "customer_segment_id",
            "location",
            "bid_value",
            "bids_completed_date",
            "ho_approval",
            "management_approval",
           // "bid_management_id",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where  = 'entry_date BETWEEN "'.$_POST['from_date'].'" AND "'.$_POST['to_date'].'" AND is_active = 1 AND is_delete = 0  ';
        
        $where = " is_delete = '0' ";

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $customer_details       = customers ($value['customer_id']);

                $value['customer_id']   = disname($customer_details[0]['customer_name']);
                $customer_state_id      = $customer_details[0]['state_unique_id'];
                $customer_state_name    = state($customer_state_id)[0]['state_name'];
                $customer_city_id       = $customer_details[0]['city_unique_id'];
                $value['location']     = city($customer_city_id)[0]['city_name'];

                $value['customer_segment_id']       = customer_segment ($value['customer_segment_id'])[0]['customer_segment'];


                if($value['ho_approval'] == "Approved"){
                    $btn_update  = "";  
                    $btn_delete  = "";
                } else{
                $btn_update                      = btn_update($folder_name,$value['unique_id']);
                $btn_delete                      = btn_delete($folder_name,$value['unique_id']);
                }
                $value['entry_date']             = disdate($value['entry_date']);
                $value['bids_completed_date']    = disdate($value['bids_completed_date']);
                
                $btn_ho_approve                  = btn_bid_approval($folder_name,$value['unique_id'],$value['ho_approval']);
                $btn_management_approve          = btn_bid_approval($folder_name,$value['unique_id'],$value['management_approval']);
                $value['ho_approval']            = $btn_ho_approve;
                $value['management_approval']    = $btn_management_approve;
                $value['unique_id']     = $btn_update.$btn_delete;
                $data[]                 = array_values($value);
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

        case 'competition_oem_add_update':

            $oem_name                        = $_POST["oem_name"];
            $product_details                 = $_POST["product_details"];
            $bids_management_unique_id       = $_POST["bids_management_unique_id"];
            $unique_id                       = $_POST["unique_id"];
    
            $update_where               = "";
    
            $columns            = [
                "oem_name"                        => $oem_name,
                "product_details"                 => $product_details,
                "bids_management_unique_id"       => $bids_management_unique_id,
                "unique_id"                       => unique_id($prefix)
            ];
    
            // check already Exist Or not
            $table_details      = [
                $table_competition_oem_details,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'oem_name ="'.$oem_name.'" AND is_delete = 0  AND product_details = "'.$product_details.'" ';
    
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
            } else if (($data[0]["count"] == 0) && ($msg != "error")) {
                // Update Begins
                if($unique_id) {
    
                    unset($columns['unique_id']);
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_competition_oem_details,$columns,$update_where);
    
                // Update Ends
                } else {
    
                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_competition_oem_details,$columns);
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
                        $msg        = "add";
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

        case 'competition_oem_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "competition_oem";
        
        // Fetch Data
        $bids_management_unique_id = $_POST['bids_management_unique_id']; 

        // DataTable 
        $search     = $_POST['search']['value'];    
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "oem_name",
            "product_details",
            "unique_id"
        ];
        $table_details  = [
            $table_competition_oem_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "bids_management_unique_id"    => $bids_management_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        $where = " is_delete = '0' ";

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_edit.$btn_delete;
                $data[]                 = array_values($value);
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

        case "competition_oem_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "oem_name",
            "product_details",
            
            "unique_id"
        ];
        $table_details  = [
            $table_competition_oem_details,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"   => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
 case 'competition_oem_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_competition_oem_details,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
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

     case 'competition_bidder_add_update':

            $bid_name                        = $_POST["bid_name"];
            $map_oem_name                    = $_POST["map_oem_name"];
            $bids_management_unique_id       = $_POST["bids_management_unique_id"];
            $unique_id                       = $_POST["unique_id"];
    
            $update_where                    = "";
    
            $columns                  = [
                "bid_name"                     => $bid_name,
                "map_oem_name"                 => $map_oem_name,
                "bids_management_unique_id"    => $bids_management_unique_id,
                "unique_id"                    => unique_id($prefix)
            ];
    
            // check already Exist Or not
            $table_details      = [
                $table_competition_bidders_details,
                [
                    "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'bid_name ="'.$bid_name.'" AND is_delete = 0  AND map_oem_name = "'.$map_oem_name.'" ';
    
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
            } else if (($data[0]["count"] == 0) && ($msg != "error")) {
                // Update Begins
                if($unique_id) {
    
                    unset($columns['unique_id']);
    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];
    
                    $action_obj     = $pdo->update($table_competition_bidders_details,$columns,$update_where);
    
                // Update Ends
                } else {
    
                    // Insert Begins            
                    $action_obj     = $pdo->insert($table_competition_bidders_details,$columns);
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
                        $msg        = "add";
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

        case 'competition_bidder_datatable':
        // Function Name button prefix
        $btn_edit_delete    = "competition_bidder";
        
        // Fetch Data
        $bids_management_unique_id = $_POST['bids_management_unique_id']; 

        // DataTable 
        $search     = $_POST['search']['value'];    
        $length     = $_POST['length'];
        $start      = $_POST['start'];
        $draw       = $_POST['draw'];
        $limit      = $length;

        $data       = [];
        

        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "bid_name",
            "map_oem_name",
            "unique_id"
        ];
        $table_details  = [
            $table_competition_bidders_details." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "bids_management_unique_id"    => $bids_management_unique_id,
            "is_active"                     => 1,
            "is_delete"                     => 0
        ];
        $where = " is_delete = '0' ";

        $order_column   = $_POST["order"][0]["column"];
        $order_dir      = $_POST["order"][0]["dir"];

        // Datatable Ordering 
        $order_by       = datatable_sorting($order_column,$order_dir,$columns);

        // Datatable Searching
        $search         = datatable_searching($search,$columns);

        if ($search) {
            if ($where) {
                $where .= " AND ";
            }

            $where .= $search;
        }
        
        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,$limit,$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            foreach ($res_array as $key => $value) {
                $btn_edit               = btn_edit($btn_edit_delete,$value['unique_id']);
                $btn_delete             = btn_delete($btn_edit_delete,$value['unique_id']);
                $value['unique_id']     = $btn_edit.$btn_delete;
                $data[]                 = array_values($value);
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

        case "competition_bidder_edit":
        // Fetch Data
        $unique_id  = $_POST['unique_id'];
        $data       = [];
        
        // Query Variables
        $json_array     = "";
        $columns        = [
            "bid_name",
            "map_oem_name",
            
            "unique_id"
        ];
        $table_details  = [
            $table_competition_bidders_details,
            $columns
        ];
        $where          = [
            "unique_id"    => $unique_id,
            "is_active"    => 1,
            "is_delete"    => 0
        ];        

        $result         = $pdo->select($table_details,$where);

        if ($result->status) {
            
            $json_array = [
                "data"      => $result->data[0],
                "status"    => $result->status,
                "sql"       => $result->sql,
                "error"     => $result->error,
                "testing"   => $result->sql
            ];
        } else {
            print_r($result);
        }
        
        echo json_encode($json_array);
        break;
 case 'competition_bidder_delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj     = $pdo->update($table_competition_bidders_details,$columns,$update_where);

        if ($action_obj->status) {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = "";
            $sql        = $action_obj->sql;
            $msg        = "success_delete";

        } else {
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
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
    
    
    case 'delete':

        $unique_id  = $_POST['unique_id'];

        $columns            = [
            "is_delete"   => 1,
        ];
        

        $update_where   = [
            "unique_id"     => $unique_id
        ];

        $action_obj_main                = $pdo->update($table,$columns,$update_where);
        $action_obj_fee_security        = $pdo->update($table_fee_security,$columns,$update_where);
        $action_obj_payment_terms       = $pdo->update($table_payment_terms,$columns,$update_where);
        $action_obj_project_description = $pdo->update($table_project_description,$columns,$update_where);

        if (($action_obj_main->status)&&($action_obj_fee_security->status)&&($action_obj_payment_terms->status)&&($action_obj_project_description->status)) {
            $status     = $action_obj_main->status;
            $data       = $action_obj_main->data;
            $error      = "";
            $sql        = $action_obj_main->sql;

        } else {
            if(!$action_obj_main->status)          {$action_obj = $action_obj_main;}
            else if(!$action_obj_fee_security->status)        {$action_obj = $action_obj_fee_security;}
            else if(!$action_obj_payment_terms->status)       {$action_obj = $action_obj_payment_terms;}
            else if(!$action_obj_project_description->status) {$action_obj = $action_obj_project_description;}
            $status     = $action_obj->status;
            $data       = $action_obj->data;
            $error      = $action_obj->error;
            $sql        = $action_obj->sql;
            $msg        = "error";
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


    
    default:
        
        break;
}

?>