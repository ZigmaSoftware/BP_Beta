<style>

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

</style>

<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables


$unique_id          = "";
$table_main         = "expense_creation_main";
$table_sub          = "expense_creation_sub";
$table_call         = "follow_ups";
$table_call_expense = "follow_up_call_travel_expense";


if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "expense_user_approval_main";

        $columns    = [
            "expense_bill_no",
            "(SELECT staff_name FROM staff WHERE staff.unique_id=".$table.".staff_unique_id) AS staff",
            "entry_date",
            "(SELECT designation_unique_id FROM staff WHERE staff.unique_id=".$table.".staff_unique_id) AS designation",
            "total_amount",
            "notes",
            "ho_approval",
            "(SELECT staff_name FROM staff WHERE staff.unique_id=".$table.".approved_by) AS ho_approved_by",
            "(SELECT designation_unique_id FROM staff WHERE staff.unique_id=".$table.".approved_by) AS ho_designation",
            "approved_date",
            "(SELECT staff_name FROM staff WHERE staff.unique_id=".$table.".ceo_approved_by) AS ceo_approved_by",
            "(SELECT designation_unique_id FROM staff WHERE staff.unique_id=".$table.".ceo_approved_by) AS ceo_designation",
            "ceo_approved_date",
            "(SELECT staff_name FROM staff WHERE staff.unique_id=".$table.".hr_approved_by) AS hr_approved_by",
            "(SELECT designation_unique_id FROM staff WHERE staff.unique_id=".$table.".hr_approved_by) AS hr_designation",
            "hr_approved_date",
            "ho_notes",
            "ceo_notes",
            "hr_notes",

            
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values          = $result_values->data;

            $expense_bill_no     = $result_values[0]["expense_bill_no"];
            $staff_name          = $result_values[0]["staff"];
            $total_amount        = $result_values[0]["total_amount"];
            $today               = $result_values[0]["entry_date"];
            $notes               = $result_values[0]["notes"];
            $ho_approved_by      = $result_values[0]["ho_approved_by"];
            $approved_date       = $result_values[0]["approved_date"];
            $ho_notes            = $result_values[0]["ho_notes"];
            $ho_approval         = $result_values[0]["ho_approval"];
            $ceo_approved_by     = $result_values[0]["ceo_approved_by"];
            $ceo_approved_date   = $result_values[0]["ceo_approved_date"];
            $ceo_notes           = $result_values[0]["ceo_notes"];
            $hr_approved_by      = $result_values[0]["hr_approved_by"];
            $hr_approved_date    = $result_values[0]["hr_approved_date"];
            $hr_notes            = $result_values[0]["hr_notes"];
            $ho_designation_id   = $result_values[0]["ho_designation"];
            $ceo_designation_id  = $result_values[0]["ceo_designation"];
            $hr_designation_id   = $result_values[0]["hr_designation"];


            $entry_date           = disdate($today);
            $designation_id       = $result_values[0]["designation"];

            $designation_details = work_designation($designation_id);
            $designation         = disname($designation_details[0]["designation_type"]);

            $ho_designation_details = work_designation($ho_designation_id);
            $ho_designation         = disname($ho_designation_details[0]["designation_type"]);
            $ceo_designation_details = work_designation($ceo_designation_id);
            $ceo_designation         = disname($ceo_designation_details[0]["designation_type"]);
            $hr_designation_details = work_designation($hr_designation_id);
            $hr_designation         = disname($hr_designation_details[0]["designation_type"]);

           
            $btn_text               = "Update";
            $btn_action             = "update";
        } else {
            $btn_text               = "Error";
            $btn_action             = "error";
            $is_btn_disable         = "disabled='disabled'";
        }
    }
}

//other expense
if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $start =0;

        $unique_id  = $_GET["unique_id"];
       
        $where_list = "a.unique_id = b.exp_main_unique_id and a.user_approval_id = '".$unique_id."' and a.is_active = '1' and a.is_delete = '0' and a.approve_status = '1'";

        $columns_list    = [
            "@a:=@a+1 s_no",
            "a.entry_date",
            "a.exp_no",
            "(SELECT expense_type FROM expense_type where expense_type.unique_id = b.expense_type_unique_id) as expense_type",
            "b.description",
            "b.amount"
            
        ];

        $table_details_list  = [
            $table_main." as a join ".$table_sub." as b , (SELECT @a:= ".$start.") AS a ",
            $columns_list
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

        $result         = $pdo->select($table_details_list,$where_list,"",$start,$order_by,$sql_function);

  
        $total_records  = total_records();
        if ($result->status) {

            $res_array      = $result->data;

            $table_data     = "";
            $total_value     = 0;

            foreach ($res_array as $key => $value) {

                $table_data .="<tr>";

                $table_data .="<td>".$value['s_no']."</td>";
                $table_data .="<td>".disdate($value['entry_date'])."</td>";
                $table_data .="<td>".$value['exp_no']."</td>";
                $table_data .="<td>".$value['expense_type']."</td>";
                $table_data .="<td>".$value['description']."</td>";
                $table_data .="<td class='text-right'>".moneyFormatIndia($value['amount'])."</td>";

                $table_data .="</tr>";

                $total_value += $value['amount'];

            }

            $total_value = number_format($total_value,2,".","");
        }
    }
}

//call expense
if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $start =0;

        $unique_id  = $_GET["unique_id"];
       
        $where_call_list = "a.unique_id = b.followup_call_unique_id and a.user_approval_id = '".$unique_id."' and a.is_active = '1' and a.is_delete = '0' and a.approve_status = '1' group by b.travel_exp_no";

        $columns_call_list    = [
            "@a:=@a+1 s_no",
            "b.entry_date",
            "b.travel_exp_no",
            "b.vehicle_type",
            "SUM(total_cost + ticket_cost + amount) AS amount",
            "b.other_vehicle_type",
            "b.total_kms"
            
        ];

        $table_details_call_list  = [
            $table_call." as a join ".$table_call_expense." as b , (SELECT @a:= ".$start.") AS a ",
            $columns_call_list
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

        $result_call         = $pdo->select($table_details_call_list,$where_call_list,"",$start,$order_by,$sql_function);

        $total_records  = total_records();
        if ($result_call->status) {

            $res_array_call      = $result_call->data;

            $table_data_call     = "";
            $total_value_call     = 0;

            


            foreach ($res_array_call as $key => $value_call) {

                if($value_call['vehicle_type'] == '1'){
                   $tot_kms       = $value_call["total_kms"]."Km";
                   $vehicle_name  = 'Two Wheeler';
                }else if($value_call['vehicle_type'] == '2') {
                    $tot_kms      = $value_call["total_kms"]."Km";
                    $vehicle_name = 'Four Wheeler';
                }else if($value_call['vehicle_type'] == '3') {
                    $vehicle_name = 'Bus';
                    $tot_kms      = "Ticket";
                }else if($value_call['vehicle_type'] == '4') {
                    $vehicle_name = 'Air';
                    $tot_kms      = "Ticket";
                }else if($value_call['vehicle_type'] == '5') {
                    $vehicle_name = 'Train';
                    $tot_kms      = "Ticket";
                }else if($value_call['vehicle_type'] == '6') {
                    $tot_kms      = $value_call["other_vehicle_type"];
                    $vehicle_name = 'Others'; 
                }else {
                    $tot_kms      = '';
                    $vehicle_name = ''; 
                }

                $table_data_call .="<tr>";

                $table_data_call .="<td>".$value_call['s_no']."</td>";
                $table_data_call .="<td>".disdate($value_call['entry_date'])."</td>";
                $table_data_call .="<td>".$value_call['travel_exp_no']."</td>";
                $table_data_call .="<td>".$vehicle_name."(<strong>".$tot_kms."</strong>)</td>";
                //$table_data_call .="<td>".$value_call['description']."</td>";
                $table_data_call .="<td class='text-right'>".moneyFormatIndia($value_call['amount'])."</td>";

                $table_data_call .="</tr>";

                $total_value_call += $value_call['amount'];

            }

            $total_value_call = number_format($total_value_call,2,".","");
        }
    }
}
$overall_amount = $total_value_call + $total_value;




?>
<input type="hidden" name="unique_id" id="unique_id" value="<?php echo $_GET['unique_id'];?>">
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <!-- Logo & title -->
                <div class="clearfix" class="col-md-12 ">
                    <div class="col-md-8 float-left row ">
                        <div class="auth-logo">
                            <div class="logo logo-dark mt-3">
                                <span class="">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="90">
                                </span>
                            </div>
        
                            <div class="logo logo-light">
                                <span class="logo-lg">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-new1.png" alt="" height="22">
                                </span>
                            </div>
                           
                        </div>&nbsp;&nbsp;
                        <div class="mt-2 float-right pl-3">

                            <h2 class=""> <?php echo $_SESSION['sess_company_name'] ; ?></h2>
                            <h5 class=""> <?php echo $_SESSION['sess_company_address'] ; ?></h5>
                            <h5 class=""> <?php echo $_SESSION['sess_company_district'] ; ?></h5>
                            <h5 class=""> <?php echo $_SESSION['sess_company_state'] ; ?></h5>

                            
                    </div><!-- end col -->
                    </div>
                    <div class="float-right">
                        <h1 class="">Expense Billing</h1>
                        <h5 class=""><strong>Expense Date : </strong> <span class="float-right">  <?php echo $entry_date; ?></span></h5>
                        <h5 class=""> <strong>Expense Bill No : </strong> <span class="float-right"><?php echo $expense_bill_no; ?> </span></h5>
                        <h5 class=""><strong>Staff Name : </strong> <span class="float-right"> <?php echo $staff_name; ?></span></h5>
                        <h5 class=""><strong>Designation : </strong> <span class="float-right"> <?php echo $designation;?></span></h5>
                        <h5 class=""> <strong>Total Amount : </strong> <span class="float-right"><?php echo moneyFormatIndia($overall_amount); ?></h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <div class="float-left">
                            <h3 class="">Call Expense</h3>
                        </div>
                    </div>
                </div>
               
 
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table  class="table mt-4 table-centered">
                                <thead>
                                <tr><th style="width: 2%">#</th>
                                    <th style="width: 5%">Entry Date</th>
                                    <th style="width: 10%">Travel Expense No</th>
                                    <th style="width: 25%">Vehicle Type</th>
                                    <th style="width: 10%" class="text-right">Amount</th>
                                </tr></thead>
                                <tbody>
                                    <?php echo $table_data_call; ?>
                                </tbody>
                                <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td ><b>Total</b></td>
                                    <td style="width: 10%" class="text-right"><?php echo moneyFormatIndia($total_value_call); ?></td>
                                </tfoot>
                            </table>
                        </div> <!-- end table-responsive -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->

                <!-- end row -->
                  <div class="row">
                    <div class="col-12">
                        <div class="float-left">
                            <h3 class="">Other Expense</h3>
                        </div>
                    </div>
                </div>
               

                
                <!-- end row -->
                
                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table  class="table mt-4 table-centered">
                                <thead>
                                <tr><th style="width: 2%">#</th>
                                    <th style="width: 5%">Entry Date</th>
                                    <th style="width: 10%">Expense No </th>
                                    <th style="width: 10%">Expense Type</th>
                                    <th style="width: 20%">Description</th>
                                    <th style="width: 10%" class="text-right">Amount</th>
                                </tr></thead>
                                <tbody>
                                    <?php echo $table_data; ?>
                                </tbody>
                                <tfoot>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td ><b>Total</b></td>
                                    <td style="width: 10%" class="text-right"><?php echo moneyFormatIndia($total_value); ?></td>
                                </tfoot>
                            </table>
                        </div> <!-- end table-responsive -->
                    </div> <!-- end col -->
                </div>
                <!-- end row -->
                <div class="row">
                    <div class="col-sm-6">
                        <div class="clearfix pt-5">
                            <h5 class="text-muted">Notes:</h5>

                            <h5  class="text-muted">
                                <?php echo $notes;?>
                            </h5>
                        </div>
                    </div> <!-- end col -->
                    
                </div>
                <!-- end row -->
            <?php if($ho_approval){?>
                <div class="row">
                    <div class="col-12">
                        <div class="float-left">
                            <h4 class="">Approval Details</h4>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="table-responsive">
                            <table  class="table mt-4 table-centered">
                                <thead>
                                    <tr>
                                        <th style="width: 2%">#</th>
                                        <th style="width: 20%">Approved By</th>
                                        <th style="width: 10%">Type</th>
                                        <th style="width: 15%">Approved Date</th>
                                         <th style="width: 15%">Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td><b><?=$ho_approved_by?></b><br>(<?=$ho_designation;?>)</td>
                                        <td>HOD</td>
                                        <td><?=$approved_date; ?></td>
                                        <td><?=$ho_notes; ?></td>
                                    </tr>
                                    <tr>
                                        <td>2</td>
                                        <td><b><?=$ceo_approved_by?></b><br>(<?=$ceo_designation;?>)</td>
                                        <td>CEO</td>
                                        <td><?=$ceo_approved_date; ?></td>
                                        <td><?=$ceo_notes; ?></td>
                                    </tr>
                                    <tr>
                                        <td>3</td>
                                        <td><b><?=$hr_approved_by?></b><br>(<?=$hr_designation;?>)</td>
                                        <td>HR</td>
                                        <td><?=$hr_approved_date; ?></td>
                                        <td><?=$hr_notes; ?></td>
                                    </tr>
                                </tbody>
                                
                            </table>
                        </div> <!-- end table-responsive -->
                    </div> <!-- end col -->
                </div>
            <?php } ?>
                <div class="mt-4 mb-1">
                    <div class="text-right d-print-none">
                        <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                        <!-- <a href="#" class="btn btn-info waves-effect waves-light">Submit</a> -->
                    </div>
                </div>
            </div> <!-- end card-body-->        
        </div>
    </div> <!-- end col -->
</div>