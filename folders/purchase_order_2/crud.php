<?php 
// Get folder Name From Currnent Url 
$folder_name        = explode("/",$_SERVER['PHP_SELF']);
$folder_name        = $folder_name[count($folder_name)-2];

// Database Country Table Name
$table            = "purchase_order";
$table_sub        = "purchase_order_sublist";

// Include DB file and Common Functions
// include '../../config/dbconfig.php';
// include 'function.php';

// Variables Declaration
$action             = $_POST['action'];
$action_obj         = (object) [
    "status"    => 0,
    "data"      => "",
    "error"     => "Action Not Performed"
];
$json_array         = "";
$sql                = "";
$prefix             = "po";
$po_prefix          = "PO-";


$data               = "";
$msg                = "";
$error              = "";
$status             = "";
$test               = ""; // For Developer Testing Purpose

$purchase_order_no  ="";

switch ($action) {


    case 'createupdate':
        
        $sublist_array   = [];
        
        $sublist_columns = [
            "item_name_id"  => "",
            "unit_id"       => "",
            "unit_name"     => "",
            "unit_decimal"  => "",
            "quantity"      => "",
            "rate"          => "",
            "discount"      => "",
            "tax_id"        => "",
            "tax_name"      => "",
            "tax_value"     => "",
            "amount"        => "",
            "unique_id"     => ""
        ];

        
        
        
        $supplier_name              = $_POST["supplier_name"];
        $branch                     = $_POST["branch"];
        $entry_date                 = $_POST["entry_date"];
        $purchase_type              = $_POST["purchase_type"];
        $net_amount                 = $_POST["net_amount"];
        $freight_percentage         = $_POST["freight_percentage"];
        $freight_amount             = $_POST["freight_amount"];
        $other_charges              = $_POST["other_charges"];
        $other_tax                  = $_POST["other_tax"];
        $other_charges_percentage   = $_POST["other_charges_percentage"];
        $tcs_percentage             = $_POST["tcs_percentage"];
        $tcs_amount                 = $_POST["tcs_amount"];
        $round_off                  = $_POST["round_off"];
        $gross_amount               = $_POST["gross_amount"];
        $contact_person             = $_POST["contact_person"];
        $quote_no                   = $_POST["quote_no"];
        $quote_date                 = $_POST["quote_date"];
        $delivery                   = $_POST["delivery"];
        $ship_via                   = $_POST["ship_via"];
        $delivery_term_fright       = $_POST["delivery_term_fright"];
        $delivery_site              = $_POST["delivery_site"];
        $payment_days               = $_POST["payment_days"];
        $document_throught          = $_POST["document_throught"];
        $dealer_reference           = $_POST["dealer_reference"];
        $billing_address            = $_POST["billing_address"];
        $billing_information        = $_POST["billing_information"];
        $approve_status             = $_POST["approve_status"];
        $unique_id                  = $_POST["unique_id"];


        // Gether Current Unique ID for the purpose of Delete
        $sub_unique_ids     = "'".implode("','",array_filter($_POST["sub_unique_id"]))."'";

        $update_where       = "";
        
        $columns            = [
            "supplier_id"              => $supplier_name,
            "branch_id"                     => $branch,
            "entry_date"                 => $entry_date,
            "purchase_type"              => $purchase_type,
            // "net_amount"                 => $net_amount,
            "freight_percentage"         => $freight_percentage,
            // "freight_amount"             => $freight_amount,
            "other_charges"              => $other_charges,
            "other_tax"                  => $other_tax,
            // "other_charges_percentage"   => $other_charges_percentage,
            "tcs_percentage"             => $tcs_percentage,
            // "tcs_amount"                 => $tcs_amount,
            "round_off"                  => $round_off,
            // "gross_amount"               => $gross_amount,
            "contact_person"             => $contact_person,
            "quote_no"                   => $quote_no,
            "quote_date"                 => $quote_date,
            "delivery"                   => $delivery,
            "ship_via"                   => $ship_via,
            "delivery_term_fright"       => $delivery_term_fright,
            "delivery_site"              => $delivery_site,
            "payment_days"               => $payment_days,
            "document_throught"          => $document_throught,
            "dealer_reference"           => $dealer_reference,
            "billing_address"            => $billing_address,
            "billing_information"        => $billing_information,
            "approve_status"             => $approve_status,
            "unique_id"                  => $main_unique_id = unique_id($prefix)
        ];
        
        // check already Exist Or not
        $table_details      = [
            $table,
                [
                "COUNT(unique_id) AS count"
                ]
            ];
            $select_where       = 'purchase_order_no = "'.$purchase_order_no.'" AND is_delete = 0  ';
            
            // When Update Check without current id
            if ($unique_id) {
                $select_where   .= ' AND unique_id !="'.$unique_id.'" ';
            }
            else{

                $bill_no_where   = [
                "acc_year"      => $_SESSION['acc_year']
            ];

            // GET Bill No
            $purchase_order_no             = bill_no($table,$bill_no_where,$po_prefix);
            $columns['purchase_order_no']  = $purchase_order_no;

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
                    
                    $update_where   = [
                        "unique_id"     => $unique_id
                    ];

                    $main_unique_id = $unique_id;

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

                        $sub_select_delete = [
                            "purchase_order_main_unique_id" => $main_unique_id
                        ];

                        
                        // Update Delete Status Before Inserting new values                    
                        
                        sublist_delete($table_sub,$sub_unique_ids,$sub_select_delete);

                    }

                    // Prepare Sublist Data Depending On insert & Update
                    
                    foreach ($_POST['item_name'] as $key => $value) {
                        $sublist_columns = [
                            "item_name_id"              => $_POST['item_name'][$key],
                            "unit_name"                 => $_POST['unit_name'][$key],
                            "quantity"                  => $_POST['qty'][$key],
                            "rate"                      => $_POST['rate'][$key],
                            "discount"                  => $_POST['discount'][$key],
                            "tax_id"                    => $_POST['tax'][$key],
                            "amount"                    => $_POST['amount'][$key],
                            
                            "unique_id"                 => $_POST['sub_unique_id'][$key],
                            "purchase_order_main_unique_id"    => $main_unique_id
                        ];
            
                        $sublist_array[] = $sublist_columns;
                    }


                    //sublist add
                    sublist_insert_update($table_sub,$sublist_array);
                    
                    form_calculation($main_unique_id);
                exit;
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

    case 'item_name_ajax':
        // print_r($_POST);

        $search_term = "";

        if (isset($_POST['searchTerm']['term'])) {

            $search_term = $_POST['searchTerm']['term'];

        }

        $item_select = [
            "unique_id AS id",
            "CONCAT(item_name, ' / ', item_code) AS text",
            "(SELECT u.unit_name FROM units u WHERE u.unique_id = item_names_code.unit_unique_id) AS unit_name"
        ];

        $item_details = [
            "item_names_code",
            $item_select
        ];

        $where  = " is_delete = 0 AND is_active = 1";

        if ($search_term) {
            $where .= " AND (item_name LIKE '".$search_term."%' OR item_code LIKE '".$search_term."%')"; 
        }

        $limit  = 10;

        $item_option = $pdo->select($item_details,$where,$limit);

        if ($item_option->status) {
            echo json_encode($item_option->data);
        } else {
            print_r($item_option);
        }

        break;


        case 'tax_ajax':
        // print_r($_POST);

        $search_term = "";

        if (isset($_POST['searchTerm']['term'])) {

            $search_term = $_POST['searchTerm']['term'];

        }

        $tax_select = [
            "unique_id AS id",
            "CONCAT(tax_name) AS text",
            "tax_value"
        ];

        $tax_details = [
            "tax",
            $tax_select
        ];

        $where  = " is_delete = 0 AND is_active = 1";

        if ($search_term) {
            $where .= " AND (tax_name LIKE '".$search_term."%')"; 
        }

        $limit  = 10;

        $tax_option = $pdo->select($tax_details,$where,$limit);

        if ($tax_option->status) {
            echo json_encode($tax_option->data);
        } else {
            print_r($tax_option);
        }

        break;

        case 'billing_address':

        $billing_address     = $_POST['billing_val'];

        $billing_address_value  = branch($billing_address);


        echo $billing_address_value[0]["branch_name"].'<br>'.$billing_address_value[0]["address"].'<br>'."TEL.NO : ".$billing_address_value[0]["phone_number"]." EMAIL.ID : ".$billing_address_value[0]["email_id"].'<br>'." GSTIN.NO : ".$billing_address_value[0]["gst_number"];
        
        break;


        
    


    
    default:
        
        break;
}

?>