<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$letter_no                  = "";
$letter_date                = $today;
$staff_name                 = "";
$staff_address              = "";
$phone_no                   = "";
$designation                = "";
$location                   = "";
$join_date                  = "";
$ctc                        = "";
$gender                     = "";
$department                 = "";
$emp_id = $_SESSION["user_name"];
$sess_user_type  = $_SESSION['sess_user_type'];

$table1      =  "staff";

$columns1    = [
    "employee_id",
    "staff_name",
    "company_name",
    "date_of_join",
];

$table_details1   = [
    $table1,
    $columns1
];
$where1 = "employee_id = '$emp_id'";
$result_values1  = $pdo->select($table_details1, $where1);
// print_r($result_values1);

if ($result_values1->status) {

    $result_values1             = $result_values1->data[0];

    $staff_name                 = $result_values1["staff_name"];

    $staff_company_name         = $result_values1["company_name"];

    $join_date                  = $result_values1["date_of_join"];
}

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "resignation_acceptance_letter";

        $columns        = [
            "@a:=@a+1 s_no",
            "staff_name",
            "company_name",
            "emp_no",
            "designation",
            "department",
            "join_date",
            "branch",
            "date_of_resignation",
            "entry_date",
            "unique_id",
            "accept_resig_date"
        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);
        //print_r($result_values);

        if ($result_values->status) {

            $result_values              = $result_values->data[0];

            $staff_name                 = $result_values["staff_name"];

            $staff_company_name         = $result_values["company_name"];

            $join_date                  = $result_values["join_date"];

            $branch                     = $result_values["branch"];

            $date_of_resignation        = $result_values["date_of_resignation"];

            $unique_id                  = $result_values['unique_id'];

            $entry_date                 = $result_values['entry_date'];

            $resgination_date           = $result_values['accept_resig_date'];
                if($resgination_date == ''){
                    $resg_date = date('Y-m-d');
                }else{
                    $resg_date = $resgination_date;
                }
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            print_r($result_values);
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}
if ($to_date == '') {
    $to_dates = date('Y-m-d');
} else {
    $to_dates = $to_date;
}

// $company_name_option          = company_name();
// $company_name_option          = select_option($company_name_option, "Select company", $staff_company_name);
$staff_names                  = get_active_staff_name();
$staff_names                   = select_option($staff_names, "Select Employee Name", $staff_name);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_name">Employee Name </label>
                                <div class="col-md-4">
                                    <select id="staff_name" name="staff_name" onchange="get_staff_details()" class="select2 form-control" value="" required>
                                        <?php echo $staff_names; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="staff_name">Employee Code </label>
                                <div class="col-md-4">
                                    <input type="text" id="emp_code" name="emp_code" class="form-control" value="<?php echo $result_values['emp_no']; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_name">Designation </label>
                                <div class="col-md-4">
                                    <input type="text" id="designation" name="designation" class="form-control" value="<?php echo $result_values['designation']; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="join_date"> Join Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="join_date" name="join_date" class="form-control" value="<?php echo $join_date; ?>" required>
                                    <input type="hidden" id="unique_id" name="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
                                    <input type="hidden" id="entry_date" name="entry_date" class="form-control" value="<?php echo $entry_date; ?>">
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="letter_date"> Department </label>
                                <div class="col-md-4">
                                    <input type="text" id="department" name="department" class="form-control" value="<?php echo $result_values['department']; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="company_name"> Company Name </label>
                                <div class="col-md-4">
                                    <input id="company_name" name="company_name" class="select2 form-control" value=" <?php echo $result_values["company_name"]; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="branch"> Location </label>
                                <div class="col-md-4">
                                    <input type="text" id="branch" name="branch" class="form-control" value="<?php echo $result_values['branch']; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="date_of_resignation"> Date Of Resignation </label>
                                <div class="col-md-4">
                                    <input type="date" id="date_of_resignation" name="date_of_resignation" class="select2 form-control" value="<?php echo $result_values['date_of_resignation']; ?>" required>
                                </div>
                            <br>
                            <br>
                            <br>
                            <?php
                            if($sess_user_type == '5ff71f5fb5ca556748'){?>
                            <label class="col-md-2 col-form-label" for="date_of_resignation"> Resignation Acceptance Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="accept_resig_date" name="accept_resig_date" class="select2 form-control" value="<?php echo $resg_date; ?>" required>
                                </div>
                                <?php }?>
                                </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel); ?>
                                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>