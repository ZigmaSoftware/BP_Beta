<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
// $table                  = "tender_submission";
// $table_product   = "tender_submission_product";

$table                  = "price_bid";
$table_competitor       = "price_bid_competitor";
$table_product          = "price_bid_product";
$table_competitor_rates = "price_bid_competitor_rates";

// Include DB file and Common Functions
include '../../config/dbconfig.php';
include 'function.php';

// Variables Declaration
$action             = $_POST['action'];

$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "sql"       => "",
    "error"     => "Action Not Performed"
];

$json_array         = "";
$sql                = "";

$country_name       = "";
$state_name         = "";
$prefix             = "tendr";
$prefix_product     = "tendrpro";
$bill_prefix        = "TEN-";

$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

switch ($action) {

    case 'createupdate':

        $price_bid_opening_date = $_POST["price_bid_opening_date"];
        // $tech_bid_opening_date  = $_POST["tech_bid_opening_date"];
        // $no_of_bidders          = $_POST["no_of_bidders"];
        // $bidders_names          = implode(",",$_POST["bidder_name"]);
        // $bid_no                 = $_POST["bid_no"];
        // $bid_date               = $_POST["bid_date"];

        $unique_id              = $_POST["unique_id"];

        $main_unique_id         = ""; 

        $update_where           = "";

        $columns                = [
            "price_bid_date"        => $price_bid_opening_date,
            // "price_bid_opening_date"        => $tech_bid_opening_date,
            // "no_of_bidders"                 => $no_of_bidders,
            "tender_status"         => $_POST["tender_status"],
            "is_updated"            => 1,
            "unique_id"             => $main_unique_id = unique_id($prefix)
        ];

        if (!$_POST['tender_status']) {
            $columns['l1_bidder_id']        = $_POST['won_bidder_name'];
            $columns['price_difference']    = $_POST['price_difference'];
        }

        // check already Exist Or not
        $table_details      = [
            $table,
            [
                "COUNT(unique_id) AS count"
            ]
        ];
        $select_where       = 'price_bid_date = "'.$price_bid_opening_date.'" AND is_delete = 0  ';

        // When Update Check without current id
        if ($unique_id) {
            // $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
            $select_where   .= ' AND bid_unique_id !="'.$unique_id.'" ';
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

                // $main_unique_id = $unique_id;

                // Get Price Bid ID based on bid Unique ID
                $tender_sql     = "SELECT  unique_id FROM price_bid WHERE bid_unique_id = '".$unique_id."' ";

                $select_result  = $pdo->query($tender_sql);

                // print_r($select_result);
                if ($select_result->status && !empty($select_result->data)) {
                    
                    $main_unique_id = $select_result->data[0]['unique_id'];
                    // return false;
                } else {
                    return false;
                }

                unset($columns['unique_id']);

                $update_where   = [
                    // "unique_id"     => $unique_id
                    "bid_unique_id"     => $unique_id
                ];

                $action_obj     = $pdo->update($table,$columns,$update_where);

                $msg            = "update";

            // Update Ends
            } else {
                
                // Insert Begins            
                $action_obj     = $pdo->insert($table,$columns);
                // Insert Ends

                $msg        = "create";

            }

            if ($action_obj->status) {
                $status     = $action_obj->status;
                $data       = $action_obj->data;
                $error      = "";
                $sql        = $action_obj->sql;

                // Product Details Add and Update
                price_bid_products_insert_update($main_unique_id);

                price_bid_competitor_update($main_unique_id);

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
    
    case 'bidder_name_ajax':

        $search_term = "";

        if (isset($_POST['searchTerm']['term'])) {

            $search_term = $_POST['searchTerm']['term'];

        }

        $item_select = [
            "unique_id AS id",
            "CONCAT(competitor_name) AS text"
        ];

        $item_details = [
            "competitor_profile",
            $item_select
        ];

        $where  = " is_delete = 0 AND is_active = 1";

        if ($search_term) {
            $where .= " AND (competitor_name LIKE '".$search_term."%' )"; 
        }

        $limit  = 10;

        $item_option = $pdo->select($item_details,$where,$limit);

        if ($item_option->status) {
            echo json_encode($item_option->data);
        } else {
            print_r($item_option);
        }

        break;
    
    case 'datatable':

        // DataTable Variables
        $search 	= $_POST['search']['value'];
        $length 	= $_POST['length'];
        $start 		= $_POST['start'];
        $draw 		= $_POST['draw'];
        $limit 		= $length;

        $data	    = [];
        
        if($length == '-1') {
            $limit  = "";
        }

        // Query Variables
        $json_array     = "";
        $columns        = [
            "@a:=@a+1 s_no",
            "bid_no",
            "bid_date",
            "customer_id",
            // "bids_tender_type",
            "call_no",
            "unique_id"
        ];
        $table_details  = [
            $table." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "is_active"     => 1,
            "is_delete"     => 0
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

            // $tender_type_options  = [
            //     1 => [
            //       "id"          => 1,
            //       "value"       => "Open" 
            //     ],
            //     2 => [
            //       "id"          => 2,
            //       "value"       => "Limited" 
            //     ],
            //     3 => [
            //       "id"          => 3,
            //       "value"       => "GeM"
            //     ]
            // ];

            foreach ($res_array as $key => $value) {
                
                // Customer Details 
                $customer_details           = customers($value['customer_id']);

                $value['customer_id']       = $customer_details[0]['customer_name'];

                $value['bid_date']          = disdate($value['bid_date']);
                
                // $bids_tender_type           = $value['bids_tender_type'];
                // $bids_tender_type           = $tender_type_options[$bids_tender_type]['value'];
                // $value['bids_tender_type']  = $bids_tender_type;

                $btn_print                 = btn_print($folder_name,$value['unique_id'],"print");
                $btn_update                 = btn_update($folder_name,$value['unique_id']);
                $btn_delete                 = btn_delete($folder_name,$value['unique_id']);
                $value['unique_id']         = $btn_print.$btn_update.$btn_delete;
                $data[]                     = array_values($value);
            }
            
            $json_array = [
                "draw"				=> intval($draw),
                "recordsTotal" 		=> intval($total_records),
                "recordsFiltered" 	=> intval($total_records),
                "data" 				=> $data,
                "testing"			=> $result->sql
            ];
        } else {
            print_r($result);
        }
        
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

        $action_obj     = $pdo->update($table,$columns,$update_where);

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

    case 'bid_product':

        $unique_id  = $_POST['unique_id'];

        if ($unique_id) {

            $select_where   = [
                // "price_bid_id" => $unique_id,
                "bid_unique_id" => $unique_id,
                "is_delete"    => 0,
                "is_active"    => 1
            ];

            $table_columns  = [
                "item_id AS id",
                "(SELECT inc.item_name FROM item_names_code inc WHERE inc.unique_id = $table_product.item_id) AS name",
                "'' AS value",
                "'' AS bidder"
            ];

            $table_details  = [
                $table_product,
                $table_columns
            ];

            $select_result  = $pdo->select($table_details,$select_where);

            if (!($select_result->status)) {
                $status     = $select_result->status;
                $data       = $select_result->data;
                $error      = $select_result->error;
                $sql        = $select_result->sql;
                $msg        = "error";
            } else {
                $result     = $select_result->data[0];
                $status     = $select_result->status;
                $data       = $select_result->data;
                $error      = $select_result->error;
                $sql        = $select_result->sql;
                $msg        = "success";
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
    
    default:
        
        break;
}

?>