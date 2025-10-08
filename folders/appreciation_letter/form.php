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

}

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "appreciation_letter";

        $columns        = [
            "@a:=@a+1 s_no",
            "staff_name",
            "date_of_appreciation",
            "entry_date",
            "unique_id",
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

            $date_of_termination        = $result_values["date_of_appreciation"];

            $unique_id                  = $result_values['unique_id'];

            $entry_date                 = $result_values['entry_date'];

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
                                    <select id="staff_name" name="staff_name" class="select2 form-control" value="" required>
                                        <?php echo $staff_names; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="date_of_appreciation"> Date Of Appreciation </label>
                                <div class="col-md-4">
                                    <input type="date" id="date_of_appreciation" name="date_of_appreciation" class="select2 form-control" value="<?php echo $result_values['date_of_appreciation']; ?>" required>
                                </div>
                            </div>
                           
                            <div class="form-group row ">
                                
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