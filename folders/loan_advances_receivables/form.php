<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";


$employee_name      = "";
$entry_date         = $today;


$designation_name   = "";
$loan_type          = "";
$payable_amount     = "";
$loan_no            = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "loan_receivables";

        $columns    = [
            "receivable_no",
            "entry_date",
            "employee_name",
            "loan_no",
            "paid_amount",
            "payable_amount",
            
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $loan_values  = $pdo->select($table_details,$where);

        if ($loan_values->status) {

            $loan_values     = $loan_values->data;

        $payable_amount       = $loan_values[0]["payable_amount"];
        $entry_date           = $loan_values[0]["entry_date"];
        $employee_name        = $loan_values[0]["employee_name"];
        $loan_no              = $loan_values[0]["loan_no"];

        $loan_no_options      = get_loan_no_fun($employee_name);              
        $loan_no_options      = select_option($loan_no_options,"Select",$loan_no);

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}


$staff_name_options    = staff_name_loan();
$staff_name_options    = select_option_staff($staff_name_options,"Select",$employee_name);

?>


<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
                                <input type="hidden" name="receivable_no" id="receivable_no" class="form-control" value='<?php echo  $loan_no; ?>'>
                                <div class="col-md-6">
                                    
                                </div>
                                <label class="col-md-2 col-form-label" for="entry_date"> Entry Date</label>
                                <div class="col-md-4">
                                    <input type="date" id="entry_date" name="entry_date" class="form-control" value="<?php echo $entry_date; ?>" required>
                                </div>
                            </div>
                      
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="employee_name"> Employee Name</label>
                                <div class="col-md-4">
                                    <select name="employee_name" id="employee_name" onChange="get_designation(),get_loan_no()" class="select2 form-control" required>
                                        <?php echo $staff_name_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="designation_name"> Designation</label>
                                <label class="col-md-4 col-form-label"  for="designation_name">
                                    <span id="designation_name"><?=$designation_name;?></span>
                                </label>
                            </div>
                            
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="loan_advance_no"> Loan / Advance No</label>
                                <div class="col-md-4">
                                    <select name="loan_advance_no" id="loan_advance_no" class="select2 form-control" onchange="get_loan_type(),get_emi_month_amount()" required>
                                        <?php echo $loan_no_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="loan_type">Type</label>
                                <label class="col-md-4 col-form-label"  for="loan_type">
                                    <span id="loan_type"><?=$loan_type;?></span>
                                </label>
                                
                                <label class="col-md-4 col-form-label"  for="loan_type">
                                  <input type="hidden" name="loan_type_no" id="loan_type_no" class="form-control" value=''>
                                  
                                </label>
                            </div>

                            <div class="form-group row loan_div">
                                <label class="col-md-2 col-form-label loan_div" for="loan_type">EMI </label>
								<div class="col-md-4">
                                    <table  class="table dt-responsive nowrap w-100" >
                                        <tbody>
                                            <tr>
                                                <td><b>Month</b></td>
                                                <td><b>Amt</b></td>
                                                <td><b>Percentage(%)</b></td>
                                            </tr>
                                            <tr>
                                                <td id="month"><b></b></td>
                                                <td id="emi"><b></b></td>
                                                <td id="loan_percentage"><b></b></td>
                                            </tr>
                                        </tbody>
                                    </table>
								</div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label loan_div" for="loan_advance_no"> EMI Type</label>
                                <label class="col-md-4 col-form-label loan_div"  for="loan_type">
                                    <span id="emi_type_val">Weekly</span>
                                    <input type="hidden" name="emi_type" id="emi_type" class="form-control" value=''>
                                </label>
                                <label class="col-md-2 col-form-label others_div" for="paid_amount">Paid Amount</label>
                                <label class="col-md-4 col-form-label others_div"  for="paid_amount">
                                    <span id="paid_amount">0</span>
                                    
                                </label>
                                <label class="col-md-2 col-form-label" for="current_payable">Current Payable</label>
                                <div class="col-md-4">
                                    <input type="text" name="current_payable" id="current_payable" class="form-control" required value='<?=$payable_amount;?>'>
                                    <input type="hidden" name="paid_amount_val" id="paid_amount_val" class="form-control" value=''>
                                </div>
                            </div>
                            
                            <div class="form-group row ">
                                <div class="col-md-6">
                                    <table id="loan_sub_datatable" class="table dt-responsive nowrap w-100" >
                                        <thead>
                                            <tr>
                                                <th>Entry Date</th>
                                                <th>Decription</th>
                                                <th>Debit</th>
                                                <th>Credit</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            
                                        </tbody>
                                    </table>
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

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  

