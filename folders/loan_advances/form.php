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
$staff_id      = "";
$amount             = "";
$emi                = "";
$emi_amount         = "";
$description        = "";
$approval           = "";
$staff_id      = "";
$loan_percentage    = "";
$percentage_req     = "";
$entry_date         = $today;


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
            "approval",
            "loan_percentage",
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
        $approval           = $loan_values["approval"];
        $loan_percentage    = $loan_values["loan_percentage"];

       

            
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$loan_advance_options    = [
    [
        "id"    => 1,
        "text"  => "Loan"
    ],
    [
        "id"    => 2,
        "text"  => "Advance"
    ],
    [
        "id"    => 3,
        "text"  => "Others"
    ]
];

$loan_advance_options    = select_option($loan_advance_options,"Select",$loan_advance);

$emi_type_options    = [
    [
        "id"    => 1,
        "text"  => "Monthly"
    ],
    [
        "id"    => 2,
        "text"  => "Weekly"
    ]
];

$emi_type_options    = select_option($emi_type_options,"Select",$emi_type);

$others_type_options    = [
    [
        "id"    => 1,
        "text"  => "Addition"
    ],
    [
        "id"    => 2,
        "text"  => "Deduction"
    ]
];

$others_type_options    = select_option($others_type_options,"Select",$others_type);

$staff_desig = designation($_SESSION["staff_id"]);

if($_SESSION['sess_user_type'] == $admin_user_type) {

    $approval_options    = [
    [
        "id"    => 1,
        "text"  => "HOD Approval"
    ],
    [
        "id"    => 2,
        "text"  => "CEO Approval"
    ]
]; 

} else if (($staff_desig == '5fd75209d365c75743')||($staff_desig == '5ff5d3081798a64663')||($staff_desig == '602151686b94779061')||($staff_desig == '607e79eb1f6fa44884')||($staff_desig == '607e79fd5d36e17729')||($staff_desig == '608009ec0743f83707')||($staff_desig == '609cd88a43e4930744')||($staff_desig == '60cb49f0f11da12695')||($staff_desig == '60cb4a02096b916097')){
$approval_options    = [
    [
        "id"    => 1,
        "text"  => "HOD Approval"
    ]
];
   
 } else {

    $approval_options    = [
    
    [
        "id"    => 2,
        "text"  => "CEO Approval"
    ]
]; 

   
}

$approval_options    = select_option($approval_options,"Select",$approval);

$staff_name_options    = staff_name();
$staff_name_options    = select_option($staff_name_options,"Select The Staff Name",$staff_id);

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
                                <label class="col-md-2 col-form-label" for="loan_type"> Loan / Advance</label>
                                <div class="col-md-4">
                                    <select name="loan_type" id="loan_type"  class="select2 form-control" onchange="get_type_div(),get_loan_percentage();" required>
                                        <?php echo $loan_advance_options;?>
                                    </select>
                                </div>
                                <input type="hidden" name="loan_no" id="loan_no" class="form-control" value='<?php echo  $loan_no; ?>'>
                                <input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">


                                <label class="col-md-2 col-form-label" for="entry_date"> Entry Date</label>
                                <div class="col-md-4">
                                    <input type="date" id="entry_date" name="entry_date" class="form-control" value="<?php echo $entry_date; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row others_div loan_advance_div">
                                <label class="col-md-2 col-form-label" for="others_type"> Type </label>
                                <div class="col-md-4">
                                   <select name="others_type" class="select2 form-control others_prop loan_advance_prop" onchange="" required>
                                        <?php echo $others_type_options;?>
                                    </select>
                                </div>
                                
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_id"> Staff Name</label>
                                <div class="col-md-4">
                                    <select name="staff_id" id="staff_id" class="select2 form-control"  onchange = "get_ho_staff(this.value)" <?=$staff_id_class;?> required>
                                        <?php echo $staff_name_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="amount"> Amount </label>
                                <div class="col-md-4">
                                    <input type="text" min="1" id="amount" onkeyup="get_emi_amount(),get_loan_percentage();" onkeypress="number_only(event)" name="amount" class="form-control" value="<?php echo $amount; ?>"  required>
                                </div>
                            </div>
                            <div class="form-group row loan_div loan_advance_div">
                                <label class="col-md-2 col-form-label" for="emi"> EMI </label>
                                <div class="col-md-4">
                                    <input type="text" id="emi"  name="emi" class="form-control loan_prop loan_advance_prop" onkeyup="get_emi_amount()" onkeypress="number_only(event)" value="<?php echo $emi; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="emi_type"> EMI Type /Amount</label>
                                <div class="col-md-2">
                               <select name="emi_type" id="emi_type"  class="select2 form-control loan_advance_prop loan_prop" onchange="" required>
                                        <?php echo $emi_type_options;?>
                                    </select>
                                </div>
                                <div class="col-md-2">EMI Amount
                                    <input type="number" id="emi_amount"  name="emi_amount" class="form-control loan_advance_prop loan_prop"  value="<?php echo $emi_amount; ?>" readonly required>
                                </div>
                            </div>
                            <div class="form-group row percentage_div"  >
                                <label class="col-md-2 col-form-label" for="description">Percentage(%) </label>
                                <div class="col-md-4">
                                	<input type="text" name="loan_percentage" id="loan_percentage" class="form-control percentage_prop" onkeypress="number_only(event)"  value="<?php echo $loan_percentage; ?>" required>
                                </div>

                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="description">Description </label>
                                <div class="col-md-4">
                                    <textarea name="description" id="description"  class="form-control" onchange="" required><?php echo $description;?></textarea>
                                </div>
                                <label class="col-md-2 col-form-label" for="approval"> Approval </label>
                                <div class="col-md-4">
                                    <select name="approval" id="approval"  class="select2 form-control" onchange="" required>
                                        <?php echo $approval_options;?>
                                    </select>
                                </div>
                            </div>

                             <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="ho_to_be_approved">Approved to </label>
                                    <label class="col-md-4 col-form-label text-primary" for="ho_to_be_approved" id="ho_to_be_approved"></label>
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