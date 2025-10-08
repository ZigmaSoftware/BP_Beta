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
$entry_date         = $today;

$reject_class          = " d-none ";

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
            "ceo_approval",
            "ceo_reason",
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
        $ceo_approval       = $loan_values["ceo_approval"];
        $ceo_reason         = $loan_values["ceo_reason"];



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
        "text"  => "HR Approval Required"
    ],
    [
        "id"    => 2,
        "text"  => "Director Approval Required"
    ],
    [
        "id"    => 3,
        "text"  => "Rejected"
    ]
];

$approve_options    = select_option($approve_options,"Select",$ceo_approval);


$ceo_staff_name_options    = staff_ceo_name();

$ceo_staff_name_options    = select_option($ceo_staff_name_options,"Select",);


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

                                <label class="col-md-2 col-form-label" for="is_approved"> Approve Status </label>
                                <div class="col-md-4">
                                    <select name="is_approved" id="is_approved" class="select2 form-control"  required>
                                        <?php echo $approve_options;?>
                                    </select>
                                </div>
                                
                            </div>

                           
                            <div class="form-group row">
                                <label for="ceo_reason" class="col-md-2 col-form-label"> Reason </label>
                                <div class="col-md-4">
                                    <textarea name="ceo_reason" id="ceo_reason" rows="4" class="form-control " required><?=$ceo_reason;?></textarea>
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