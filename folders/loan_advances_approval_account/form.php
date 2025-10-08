<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$loan_advance       = "";
$emi_type           = "";
$others_type        = "";
$staff_id           = "";
$amount             = "";
$emi                = "";
$emi_amount         = "";
$description        = "";
$approval           = "";
$loan_percentage    = "";
$percentage_req     = "";
$others_type_label  = "";
$emi_type_label     = "";
$payment_type       = "";
$bank_name          = "";
$account_no         = "";
$ifsc_code          = "";
$branch_name        = "";
$upi_id             = "";
$cheque_no          = "";
$entry_date         = $today;

$reject_class          = " d-none ";
$bank_name_class       = " d-none ";
$account_no_class      = " d-none ";
$branch_name_class     = " d-none ";
$ifsc_code_class       = " d-none ";
$upi_id_class          = " d-none ";
$cheque_no_class       = " d-none ";

$ceo_staff_class       = " d-none ";

if($_SESSION['sess_user_type'] == $admin_user_type) {
    $staff_id         = '';
    $staff_id_class   = "";
} else {
    $staff_id         = $_SESSION['staff_id'];
    $staff_id_class   = " disabled ";
}

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "loan_advance";

        $columns    = [
            "loan_no",
            "loan_type",
            "entry_date",
            "others_type",
            "staff_id",
            "amount",
            "emi",
            "emi_type",
            "emi_amount",
            "description",
            "loan_percentage",
            "accounts_approval",
            "accounts_app_reason",
            "payment_type",
            "bank_name",
            "account_no",
            "ifsc_code",
            "branch_name",
            "upi_id",
            "cheque_no"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $loan_values  = $pdo->select($table_details,$where);


        if ($loan_values->status) {

            $loan_values     = $loan_values->data[0];

        $loan_no            = $loan_values["loan_no"];
        $loan_advance       = $loan_values["loan_type"];
        $entry_date         = $loan_values["entry_date"];
        $others_type        = $loan_values["others_type"];
        $staff_id           = $loan_values["staff_id"];
        $amount             = $loan_values["amount"];
        $emi                = $loan_values["emi"];
        $emi_type           = $loan_values["emi_type"];
        $emi_amount         = $loan_values["emi_amount"];
        $description        = $loan_values["description"];
        $loan_percentage    = $loan_values["loan_percentage"];
        $accounts_approval  = $loan_values["accounts_approval"];
        $accounts_app_reason = $loan_values["accounts_app_reason"];
        $payment_type       = $loan_values["payment_type"];
        $bank_name          = $loan_values["bank_name"];
        $account_no         = $loan_values["account_no"];
        $ifsc_code          = $loan_values["ifsc_code"];
        $branch_name        = $loan_values["branch_name"];
        $upi_id             = $loan_values["upi_id"];
        $cheque_no          = $loan_values["cheque_no"];



        if($loan_advance == 1){
            $loan_advance_val = 'Loan';
        }else if($loan_advance == 2){
            $loan_advance_val = 'Advance';
        }else if($loan_advance == 3){
            $loan_advance_val = 'Others';
        
        } 


        if($others_type == 1){
            $others_type_label = 'Addition';
        }else if($others_type == 2){
            $others_type_label = 'Deduction';
        }

        if($emi_type == 1){
            $emi_type_label = 'Monthly';
        }else if($emi_type == 2){
            $emi_type_label = 'Weekly';
        }   
       

            
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}



$staff_name_options    = staff_name();
$staff_name_options    = select_option($staff_name_options,"Select The Staff Name",$staff_id);

$approve_options = [
    [
        "id"    => 1,
        "text"  => "Approved"
    ],
    [
        "id"    => 2,
        "text"  => "Rejected"
    ]
];

$approve_options    = select_option($approve_options,"Select",$accounts_approval);



$payment_type_options = [
    [
        "id"    => 1,
        "text"  => "Bank"
    ],
    [
        "id"    => 2,
        "text"  => "Cash On Hand"
    ],
    [
        "id"    => 3,
        "text"  => "Net Banking"
    ],
    [
        "id"    => 4,
        "text"  => "Cheque"
    ]
];

$payment_type_options    = select_option($payment_type_options,"Select",$payment_type);


$ceo_staff_name_options    = staff_ceo_name();

$ceo_staff_name_options    = select_option($ceo_staff_name_options,"Select",);




        $start =0;

        $unique_id      = $_GET["unique_id"];
        $table_approve  = 'view_loan_advance_approval_details';
        

        $columns    = [
            "@a:=@a+1 s_no",
            "approve_date",
            "approval_stage",
            "approved_by",
            "'' AS reason",
        ];

       $table_details  = [
            $table_approve." , (SELECT @a:= ".$start.") AS a ",
            $columns
        ];
        $where          = [
            "unique_id"  => $unique_id
            
        ];
        $order_by       = "";


        $sql_function   = "SQL_CALC_FOUND_ROWS";

        $result         = $pdo->select($table_details,$where,"",$start,$order_by,$sql_function);
        $total_records  = total_records();

        if ($result->status) {

            $res_array      = $result->data;

            $table_data_approval_details  = "";
          

            foreach ($res_array as $key => $value) {

                $staff_name   = staff_name($value['approved_by'])[0]['staff_name'];

                if($value['approval_stage'] == 1){

                    $approval_stage  = 'HOD Approval';

                } else if($value['approval_stage'] == 2){
                   
                    $approval_stage  = 'CEO Approval';

                } 
                else if($value['approval_stage'] == 3){
                    $approval_stage  = 'Director Approval';

                }
                else if($value['approval_stage'] == 4){
                    $approval_stage  = 'Hr Approval';

                }    

                $table_data_approval_details.="<tr>";

                $table_data_approval_details.="<td>".$value['s_no']."</td>";
                $table_data_approval_details.="<td>".disdate($value['approve_date'])."</td>";
                $table_data_approval_details.="<td>".($approval_stage)."</td>";
                $table_data_approval_details.="<td>".($staff_name)."</td>";
                $table_data_approval_details.="<td>".($value['reason'])."</td>";
                $table_data_approval_details.="</tr>";


            }

}


?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off">
                    <div class="row">                                    
                        <div class="col-12">
                           <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="loan_type"> Loan / Advance</label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary"><?= $loan_advance_val; ?></label>
                                </div>

                                <input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
                                <input type="hidden" name="loan_type" id="loan_type" class="form-control" value="<?php echo $loan_advance; ?>">
                                <input type="hidden" name="amount" id="amount" class="form-control" value="<?php echo $amount; ?>">
                                <input type="hidden" name="loan_no" id="loan_no" class="form-control" value="<?php echo $loan_no; ?>">
                                <input type="hidden" name="staff_id" id="staff_id" class="form-control" value="<?php echo $staff_id; ?>">


                                <label class="col-md-2 col-form-label" for="entry_date"> Entry Date</label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary"><?= disdate($entry_date); ?></label>
                                </div>
                            </div>
                            <div class="form-group row others_div loan_advance_div">
                                <label class="col-md-2 col-form-label" for="others_type"> Type </label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary others_prop loan_advance_prop"><?= ($others_type_label); ?></label>
                                </div>
                                
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_id"> Staff Name</label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary"><?= staff_name($staff_id)[0]['staff_name']; ?></label>
                                    
                                </div>
                                <label class="col-md-2 col-form-label" for="amount"> Amount </label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary"><?= ($amount); ?></label>

                                </div>
                            </div>
                            <div class="form-group row loan_div loan_advance_div">
                                <label class="col-md-2 col-form-label" for="emi"> EMI </label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary loan_prop loan_advance_prop"><?= ($emi); ?></label>
                             </div>
                                
                                <label class="col-md-2 col-form-label" for="emi_type"> EMI Type /Amount</label>
                                <div class="col-md-2">

                                    <label class="col-form-label text-primary loan_prop loan_advance_prop"><?= ($emi_type_label); ?></label>

                                </div>
                                <div class="col-md-2">EMI Amount
                                    &nbsp;<label class="col-form-label text-primary loan_prop loan_advance_prop"><?= ($emi_amount); ?></label>

                                </div>
                            </div>
                            <div class="form-group row percentage_div"  >
                                <label class="col-md-2 col-form-label" for="description">Percentage(%) </label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary percentage_prop"><?= ($loan_percentage); ?></label>

                                </div>

                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="description">Description </label>
                                <div class="col-md-4">
                                    <label class="col-form-label text-primary"><?= ($description); ?></label>
                                </div>

                            </div>

                            <br>
                              <label class="col-md-2 col-form-label" for="is_approved"> Approval Details </label>
                              <table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                             <tr>
                                <th>#</th>
                                <th>Approve Date</th>
                                <th>Approval Stage</th>
                                <th>Approved By</th>
                                <th>Reason</th>
                             <tbody>
                                <?php echo $table_data_approval_details; ?>
                             </tbody>
                             </tr>
                            </table>


                            <br>

                            <br>
                              <label class="col-md-2 col-form-label" for="is_approved"> Accounts Voucher </label>
                              <table id="example" class="table table-striped table-bordered dt-responsive nowrap" cellspacing="0" width="100%">
                                <tr>
                                    <th></th>
                                    <th>Dr</th>
                                    <th>Cr</th>
                                </tr>
                             <tr>
                                <th><?= staff_name($staff_id)[0]['staff_name'].' '.'A/C'; ?></th>
                                <th><?= moneyFormatIndia($amount); ?></th>
                                <th></th>
                               </tr> 
                               <tr>
                                <th>Cash A/C</th>
                                <th></th>
                                <th><?= moneyFormatIndia($amount); ?></th>
                               </tr> 
                             <tbody>
                             </tbody>
                             
                            </table>

                            
                            <br>

                             <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="payment_type"> Payment Type </label>
                                <div class="col-md-4">
                                    <select name="payment_type" id="payment_type" class="select2 form-control" onchange="get_payment_type(this.value);"  required>
                                        <?php echo $payment_type_options;?>
                                    </select>
                                </div>
                                <label for="bank_name" class="col-md-2 col-form-label <?=$bank_name_class;?> bank_name_class" > Bank Name </label>
                                <div class="col-md-4">
                                    <input type="text" name="bank_name" id="bank_name" class="form-control <?=$bank_name_class;?> bank_name_class" value="<?=$bank_name;?>">
                                </div>
                            </div>
                             <div class="form-group row">
                                <label for="account_no" class="col-md-2 col-form-label <?=$account_no_class;?> account_no_class"> Account No </label>
                                <div class="col-md-4">
                                    <input type="text" name="account_no" id="account_no" class="form-control <?=$account_no_class;?> account_no_class" value="<?=$account_no;?>">
                                </div>
                                 <label for="ifsc_code" class="col-md-2 col-form-label <?=$ifsc_code_class;?> ifsc_code_class"> IFSC Code </label>
                                <div class="col-md-4">
                                    <input type="text" name="ifsc_code" id="ifsc_code" class="form-control <?=$ifsc_code_class;?> ifsc_code_class" value="<?=$ifsc_code;?>">
                                </div>
                             </div>

                              <div class="form-group row">
                                <label for="branch_name" class="col-md-2 col-form-label <?=$branch_name_class;?> branch_name_class"> Branch Name </label>
                                <div class="col-md-4">
                                    <input type="text" name="branch_name" id="branch_name" class="form-control <?=$branch_name_class;?> branch_name_class" value="<?=$branch_name;?>">
                                </div>
                                <label for="upi_id" class="col-md-2 col-form-label <?=$upi_id_class;?> upi_id_class"> UPI ID </label>
                                <div class="col-md-4">
                                    <input type="text" name="upi_id" id="upi_id" class="form-control <?=$upi_id_class;?> upi_id_class" value="<?=$upi_id;?>">
                                </div>
                             </div>


                             <div class="form-group row">
                                <label for="cheque_no" class="col-md-2 col-form-label <?=$cheque_no_class;?> cheque_no_class"> Cheque No </label>
                                <div class="col-md-4">
                                    <input type="text" name="cheque_no" id="cheque_no" class="form-control <?=$cheque_no_class;?> cheque_no_class" value="<?=$cheque_no;?>">
                                </div>
                                
                             </div>

                           
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Status </label>
                                <div class="col-md-4">
                                    <select name="is_approved" id="is_approved" class="select2 form-control"  required>
                                        <?php echo $approve_options;?>
                                    </select>
                                </div>
                                <label for="accounts_app_reason" class="col-md-2 col-form-label"> Reason </label>
                                <div class="col-md-4">
                                    <textarea name="accounts_app_reason" id="accounts_app_reason" rows="4" class="form-control " required><?=$accounts_app_reason;?></textarea>
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