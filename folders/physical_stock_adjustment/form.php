<!-- This file Only PHP Functions -->
<?php include 'function.php';?>


<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";
$sub_unique_id      = "";

$call_id            = "";
$call_type_id       = "";
$state_name         = "";

$follow_up_call_id  = "";
$follow_up_date     = "";
$executive_id       = $_SESSION['user_id'];
$location_id        = $_SESSION['sess_user_location'];
$location_name      = $_SESSION['sess_user_location'];
$call_type_id       = "";
$customer_id        = "";
$mode               = "";
$cur_status         = "";
$remark             = "";
$action_type        = "";
$action_type_next   = " checked ";
$action_type_close  = "";

// Additional Needed Variables
$today_dis          = $today;
$executive_name     = "";
// $location_name      = "";
$call_type_name     = "";
$customer_name      = "";
$mode_name          = "";

// Sub List Data Variable
$next_follow_up_date = $today;
$next_follow_up_days = "";
$call_close_status   = "";
$call_close_date     = $today;
$call_close_remark   = "";


// Table Related Actions
$table_action        = 0;
$btn_class           = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id   = $_GET["unique_id"];

        $where       = " a.unique_id = '".$unique_id."' AND b.prev_follow_up_unique_id = 'new' ";
        
        // In this Form Join Table Used to get the Both Main and Sub Table Data
        $table      =  "follow_up_call AS a";
        $table      .=  " RIGHT JOIN follow_up_call_sublist AS b ON a.unique_id = b.follow_up_call_unique_id ";

        $columns    = [
            "a.follow_up_call_id",
            "a.follow_up_date",
            "a.executive_id",
            "a.location_id",
            "a.call_type_id",
            "a.customer_id",
            "a.mode",
            "a.action_val",
            "b.status",
            "b.remark",
            "b.next_follow_up_days",
            "b.next_follow_up_date",
            "b.call_status",
            "b.close_remark",
            "b.close_date"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_value  = $pdo->select($table_details,$where);

        if ($result_value->status) {

            $result_value       = $result_value->data;

            $follow_up_call_id  = $result_value[0]["follow_up_call_id"];
            $today              = $result_value[0]["follow_up_date"];
            $today_dis          = $result_value[0]["follow_up_date"];
            $executive_id       = $result_value[0]["executive_id"];
            $executive_name     = $result_value[0]["executive_id"];
            $location_id        = $result_value[0]["location_id"];
            $location_name      = $result_value[0]["location_id"];
            $call_type_id       = $result_value[0]["call_type_id"];
            $call_type_name     = $result_value[0]["call_type_id"];
            $customer_id        = $result_value[0]["customer_id"];
            $customer_name      = $result_value[0]["customer_id"];
            $mode               = $result_value[0]["mode"];
            $action_type        = $result_value[0]["action_val"];
            $cur_status         = $result_value[0]["status"];
            $remark             = $result_value[0]["remark"];

            if (isset($_GET['table_action'])) {

                $table_action = $_GET['table_action'];

                // Custom Filter
                switch ($table_action) {
                    case 'new_calls':

                        break;

                    case 'follow_ups':

                        break;

                    case 'updated':
                        $btn_class = ' d-none ';
                        break;

                    case 'closed':

                        break;
                    
                    default:

                        break;
                    }

                }

            if (!isset($_GET['sub_unique_id'])) {
                if ($action_type) {
                    $action_type_next  = " checked ";
                    $next_follow_up_date = $result_value[0]["next_follow_up_date"];
                    $next_follow_up_days = $result_value[0]["next_follow_up_days"];
                } else {
                    $action_type_close = " checked ";
                    $call_close_status   = $result_value[0]["call_status"];
                    $call_close_date     = $result_value[0]["close_date"];
                    $call_close_remark   = $result_value[0]["close_remark"];
                }
            } else {
                $sub_unique_id = $_GET['sub_unique_id'];
            }

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            print_r($result_value);
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$executive_options = [
    1 => [
        "unique_id" => $_SESSION['user_id'],
        "value" => $_SESSION['user_name']
    ]
];


$executive_options  = select_option($executive_options,"Select Executive Name",$executive_id); 

// Customer Options Start
// $customer_options = [
//     1 => [
//         "unique_id" => "alsdfjslafasdl489rq",
//         "value" => "Customer 1"
//     ],
//     2 => [
//         "unique_id" => 2,
//         "value" => "Customer 2"
//     ],
//     3 => [
//         "unique_id" => 3,
//         "value" => "Customer 3"
//     ],
//     4 => [
//         "unique_id" => 4,
//         "value" => "Customer 4"
//     ],
//     5 => [
//         "unique_id" => 5,
//         "value" => "Customer 5"
//     ],
//     6 => [
//         "unique_id" => 6,
//         "value" => "Customer 6"
//     ],
// ];

$customer_options   = customers();


$customer_options   = select_option($customer_options,"Select Staff Name",$customer_id); 

// Customer Options End

// Mode Options Start
$mode_options = [
    1 => [
        "unique_id" => "Direct",
        "value" => "Direct"
    ],
    2 => [
        "unique_id" => "Indirect",
        "value" => "Indirect"
    ]
];


$mode_options       = select_option($mode_options,"Select Mode",$mode); 
// Mode Options End

$call_type_options  = call_type();

$call_type_options  = select_option($call_type_options,"Select the Division Name",$call_type_id);

// Close Call Status Start
$close_call_options = [
    1 => [
        "unique_id" => 1,
        "value" => "Completed"
    ],
    2 => [
        "unique_id" => 2,
        "value" => "Rejected"
    ]
];


$close_call_options   = select_option($close_call_options,"Select Call Status",$call_close_status); 
// Close Call Status End

?>

<?php 

// Redirect Sub Form
if (isset($_GET['sub_unique_id'])) {

    $sub_unique_id = $_GET['sub_unique_id'];

    $call_type_options  = call_type($call_type_id);

    $call_type_name     = $call_type_options[0]['call_type'];

    $call_type_options  = "";

    $customer_options   = customers($customer_id);


    $customer_name      = $customer_options[0]['customer_name'];


    include 'sub_form.php';


} else {

?>

<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <form class="was-validated"  autocomplete="off" >
               <div class="row">
                  <div class="col-12">
                     <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                     <div class="form-group row">
                        <label class="col-md-2 col-form-label" for="call_id"> Damage No </label>
                        <div class="col-md-4">
                           <h4 class="text-primary"><?php echo $follow_up_call_id; ?></h4>
                          
                        </div>

                        <label class="col-md-2 col-form-label" for="call_id"> Date</label>
                        <div class="col-md-4">
                                <input type="date" name="follow_up_call_from" id="follow_up_call_from" class="form-control" max = "<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                            </div>

                          
                     </div>
                  
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="call_type"> Branch Name </label>
                        <div class="col-md-4">
                           <select name="call_type" id="call_type" class="select2 form-control" required>
                           <?php echo $call_type_options; ?>                                     
                           </select>
                        </div>
                        <label class="col-md-2 col-form-label" for="customer_id"> </label>
                        <div class="col-md-4">
                          <input type="radio" name="stock_out" id="stock_out" class="form-control" style="width: 19px;" checked>Stock Out
                          <input type="radio" name="stock_out" id="stock_out" class="form-control" style="width: 19px;" >Stock In                                        
                        </div>
                     </div>
                    

                      <div class="row">
                            <div class="col-12">
                            <!-- Table Begiins -->
                                <table id="order_product_datatable" class="table dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Stock </th>
                                            <th>Qty</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                          <br>

                         <div class="form-group row ">
                        
                        <label class="col-md-2 col-form-label" for="cur_status"> Description </label>
                        <div class="col-md-4">
                           <textarea name="cur_status" id="cur_status" rows="5" class="form-control" required><?php  ?></textarea>
                        </div>
                      </div>

                     
                     <div class="form-group row ">
                        <div class="col-md-12">
                           <!-- Cancel,save and update Buttons -->
                           <?php echo btn_cancel($btn_cancel);?>
                           <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <!-- end card-body -->
      </div>
      <!-- end card -->
   </div>
   <!-- end col -->
</div> 

<?php } ?>