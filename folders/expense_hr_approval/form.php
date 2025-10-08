<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>
<script>
    var sublist = "";
</script>
<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";
$screen_unique_id   = unique_id("expscr"); // It is Current Screen Unique id


$entry_date         = $today;
$branch_name        = "";
$exp_no             = "";
$table              = "";
$approved_description    = "";
$table_sub          = "";

$sublist_data       = "";
$today=date('Y-m-d');
$hr_approval       = "Approved";

// echo $_SESSION["staff_name"];
// echo $_SESSION["staff_id"];
 
$staff = user_name_value($_SESSION["staff_id"]);


 $approved_by = $staff[0]['user_name'];

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];
        // "user_approval_id"      => $_SESSION['staff_id'], 
        $table            = "expense_creation_main";
        $table_sub        = "expense_creation_sub";

        $columns    = [
            "exp_no",
            "branch_unique_id",
            "description",
            "entry_date",
            "staff_branch_type",
            "designation_unique_id",
            "grade_type",
            "hr_approval",
            "approved_date",
            "user_approval_id",
            "approved_description",
            "screen_unique_id"


        ];

        $table_details   = [
            $table,
            $columns
        ];

        $select_result  = $pdo->select($table_details, $where);

        if ($select_result->status) {

            //Get Sublist Data


            $select_result              = $select_result->data;
            $entry_date                 = $select_result[0]["entry_date"];
            $branch_name                = $select_result[0]["branch_unique_id"];
            $exp_no                     = $select_result[0]["exp_no"];
            $description                = $select_result[0]["description"];
            $screen_unique_id           = $select_result[0]["screen_unique_id"];
            $staff_branch_type          = $select_result[0]["staff_branch_type"];
            $designation_unique_id      = $select_result[0]["designation_unique_id"];
            $grade_type                 = $select_result[0]["grade_type"];
            $hr_approval                = $select_result[0]["hr_approval"];
            $approved_date              = $select_result[0]["approved_date"];
            $approved_description       = $select_result[0]["approved_description"];
            

            $designation_details   = work_designation($designation_unique_id);
            $designation_name      = $designation_details[0]["designation_type"];



            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";

            print_r($select_result);
        }
    }
}
if ($staff_branch_type == 0) {

    $sess_user_type      = $_SESSION['sess_user_type'];
    $executive_id        = $_SESSION["staff_id"];


    if ($sess_user_type != $admin_user_type) {
        $executive_options = [];
        // $executive_options   = $data;
        $executive_options   = staff_name($_SESSION["staff_id"])[0];
        //print_r($executive_options);
        $executive_options  = [[
            "unique_id" => $executive_options['unique_id'],
            "name"      => $executive_options['staff_name']
        ]];
        $executive_options   = select_option($executive_options, "Select Staff Name", $_SESSION["staff_id"]);
    } else {
        $executive_options  = staff_name();
        $executive_options  = select_option($executive_options, "Select Executive Name", $branch_name);
    }

    $label_name         = "Staff Name";
    $staff_check        = " checked ";
    $button_disable     = " disabled ";
    $text_readonly      = " disabled ";
} else {
    $executive_options = branch($branch_name);
    $executive_options = select_option($executive_options, "Select Branch Name", $branch_name);

    $label_name                 = "Branch Name";
    $branch_check               = " checked ";
    $designation_unique_id      = "";
    $designation_name           = "";
    $designation_class          = "d-none";
    $button_disable             = " disabled ";
    $text_readonly              = " disabled ";
}

$hr_approval_option      = [
    "Approved" => [
        "unique_id" => "1",
        "value"     => "Approved",
    ],
    "Pending" => [
        "unique_id" => "2",
        "value"     => "Pending",
    ],
    "Cancel" => [
        "unique_id" => "3",
        "value"     => "Cancel",
    ],
];
$hr_approval_option     = select_option($hr_approval_option, "Select", $hr_approval);



?>

<input type="hidden" name="sub_counter" id="sub_counter" value="<?php echo $sub_counter; ?>">

<input type="hidden" name="sublist_data" id="sublist_data" value='<?php echo $sublist_data; ?>'>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                    <input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">

                    <!-- <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id; ?>"> -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <input type="hidden" name="exp_no" id="exp_no" class="form-control" value='<?php echo  $expense_bill_no; ?>'>

                                <div class="col-md-3">
                                    <label class="col-form-label"><?php echo $expense_bill_no; ?></label>
                                </div>
                                <div class="col-md-5"></div>

                                <!-- <label class="col-md-1 col-form-label" for="entry_date"> Entry Date </label>
                                <div class="col-md-3">
                                    <label class="col-form-label"><?php echo disdate($today); ?></label>
                                    <input type="hidden" id="entry_date" name="entry_date" class="form-control" required>
                                </div> -->
                            </div>

                        </div>
                    </div>
                    <!-- </form> 
                <form class="was-validated sublist-form" id="sublist-form"> -->
                    <!-- <div class="row">                                    
                    <div class="col-12">
                            <table id="expense_hr_approval_sub_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Entry Date</th>
                                        <th>Branch Name / Staff Name</th> -->
                    <!-- <th>Call Amount</th> -->
                    <!-- <th>Expense Amount</th>
                                        <th>View</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>                                            
                            </table>
                    </div>
                </div> -->
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">

                                <input type="hidden" name="exp_no" id="exp_no" class="form-control" value='<?php echo  $exp_no; ?>'>
                                <div class="col-md-2">
                                    <h4 class="text-info"><?php echo $exp_no; ?></h4>
                                </div>
                                <label class="col-md-1 col-form-label" for="branch_staff_name"> <span id="staff_branch"><?= $label_name; ?></span></label>
                                <div class="col-md-3">
                                    <select name="branch_staff_name" id="branch_staff_name" class="select2 form-control" onChange="get_designation()">
                                        <?php echo  $executive_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="entry_date"> Entry Date </label>
                                <div class="col-md-3">
                                <label class="col-form-label"> <?php echo $entry_date; ?> </label>
                                    <input type="hidden" id="entry_date" name="entry_date" class="form-control"  max="<?php echo $today; ?>" value="<?php echo $entry_date; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-3 col-sm-12 col-12">
                                    <div class="form-group row d-none">
                                        <div class="custom-control branch_staff_name_type custom-radio">
                                            <input type="radio" id="branch" name="branch_staff_name_type" <?= $branch_check; ?> class="custom-control-input" onclick="get_staff_name(this.value);" value="1" checked required>
                                            <label class="custom-control-label text-primary" for="branch">Branch</label>&nbsp;&nbsp;
                                        </div>
                                        <div class="custom-control branch_staff_name_type custom-radio">
                                            <input type="radio" id="staff" name="branch_staff_name_type" class="custom-control-input" onclick="get_staff_name(this.value);" value="0" <?= $staff_check; ?> required>
                                            <label class="custom-control-label text-primary" for="staff">Staff</label>
                                        </div>
                                    </div>
                                </div>
                                <label class="col-md-1 col-form-label <?php echo $designation_class; ?>" for="designation"> Designation </label>
                                <div class="col-md-1">
                                    <label class="col-md-12 col-form-label " for="designation_name"> <span id="designation_name"><?= $designation_name; ?></span> </label>
                                    <input type="hidden" id="designation_unique_id" name="designation_unique_id" class="form-control" value="<?php echo $designation_unique_id; ?>">
                                </div>
                                <label class="col-md-1 col-form-label" for="grade_name">Grade Type</label>
                                <div class="col-md-1">
                                    <label class="col-md-12 col-form-label " for="grade_name"> <span id="grade_name"><?= $grade_name; ?></span> </label>
                                    <input type="hidden" id="grade_type" name="grade_type" class="form-control" value="<?php echo $grade_type; ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    </br>
                    <!--  </form>
                <form class="was-validated"> -->
                    
                    <input type="hidden" id="expense_table_count" name="expense_table_count" value=>



                    <div class="row">
                        <div class="col-12">
                            <table id="exp_food_daily_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Expense Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Document Upload</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th>Total</th>
                                    <th id='total_amt'></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" id="expense_table_count_hotel" name="expense_table_count_hotel" value=>
                    <div class="row">
                        <div class="col-12">
                            <table id="exp_hotel_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Expense Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Document Upload</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th>Total</th>
                                    <th id='total_amt_hotel'></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" id="expense_table_count_petrol" name="expense_table_count_petrol" value=>

                    <div class="row">
                        <div class="col-12">
                            <table id="exp_travel_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Expense Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Document Upload</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th>Total</th>
                                    <th id='total_amt_travel'></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <br>
                    <input type="hidden" id="expense_table_count_travel" name="expense_table_count_travel" value=>

                    <div class="row">
                        <div class="col-12">
                            <table id="exp_petrol_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Expense Type</th>
                                        <th>Amount</th>
                                        <th>Description</th>
                                        <th>Document Upload</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                                <tfoot>
                                    <th></th>
                                    <th>Total</th>
                                    <th id='total_amt_petrol'></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <form class="was-validated sublist-form" id="sublist-form">
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group row ">
                                    <label class="col-md-2 col-form-label" for="notes"> Approval Status </label>
                                    <div class="col-md-2">
                                        <select name="hr_approval" id="hr_approval" class="select2 form-control" required><?php echo $hr_approval_option; ?>
                                        </select>
                                    </div>
                                    <label class="col-md-1 col-form-label" for="entry_date"> Approved Date </label>
                                    <div class="col-md-3">
                                       <!-- <?php echo date("D M d, Y G:i a"); ?> -->
                                        <label class="col-form-label"><?php echo date("D M d, Y G:i a"); ?> </label>
                                        <input type="hidden" id="approved_date" name="approved_date" class="form-control"  required>
                                    </div>
                                    <label class="col-md-1 col-form-label" for="approved_by"> Approved By</label>
                                    <div class="col-md-3">
                                    <label class="col-form-label"><?php echo $approved_by; ?></label>
                                        <input type="hidden" id="approved_by" name="approved_by" class="form-control" value="<?php echo $approved_by; ?>" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group row ">
                                    <label class="col-md-2 col-form-label" for="notes">Approved Description</label>
                                </div>
                                <div class="form-group row ">
                                    <div class="col-md-4">
                                        <textarea name="approved_description" id="approved_description" class=" form-control" rows="5"><?php echo $approved_description;?> </textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                    <div class="col-12">
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <!-- Cancel,save and update Buttons -->
                                <?php echo btn_cancel($btn_cancel); ?>
                                <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div> <!-- end card-body -->
    </div> <!-- end card -->
</div><!-- end col -->
<!-- </div> -->

<?php
include 'modal_sub_popup.php';
?>