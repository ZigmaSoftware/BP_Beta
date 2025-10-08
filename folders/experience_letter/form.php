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
$medical_insurance_premium  = "";
$performance_allowance      = "";
$gross_salary               = "";
$tds_deduction_status       = 0;
$performance_bonus_status   = 0;
$emp_id = $_SESSION["user_name"];
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

        $table      =  "experience_letter";

        $columns    = [
            "letter_no",
            "name",
            "company_name",
            "company_name_unique_id",
            "designation",
            "join_date",
            "to_date",

        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $letter_no                          = $result_values["letter_no"];
            $letter_date                        = $result_values["letter_date"];
            $staff_name                         = $result_values["name"];
            $staff_company_name                 = $result_values["company_name"];
            $staff_company_name_unique_id       = $result_values["company_name_unique_id"];
            $designation                        = $result_values["designation"];        
            $to_date                            = $result_values["to_date"];
            $join_date                          = $result_values["join_date"];



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
$gender_options   = [
    [
        "id"    => 1,
        "text"  => "Male"
    ],
    [
        "id"    => 2,
        "text"  => "Female"
    ],
    [
        "id"    => 3,
        "text"  => "Others"
    ]
];

$gender_options        = select_option($gender_options, "Select Gender", $gender);

$probation_options   = [
    [
        "id"    => 1,
        "text"  => "1 Month"
    ],
    [
        "id"    => 2,
        "text"  => "2 Month"
    ],
    [
        "id"    => 3,
        "text"  => "3 Month"
    ]
];

$probation_options        = select_option($probation_options, "Select Probation Month", $probation);


$company_name_option          = company_name();
$company_name_option          = select_option($company_name_option, "Select company", $staff_company_name);
$staff_names          = get_active_staff_name();
$staff_names          = select_option($staff_names, "Select Employee Name", $staff_name);



?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_name"> Name </label>
                                <div class="col-md-4">
                                    <select id="staff_name" name="staff_name" class="select2 form-control" onchange="get_staff_details(this.value)" required>
                                       <?php echo $staff_names; ?>
                                    </select>
                                </div>                        
                                <label class="col-md-2 col-form-label" for="company_name"> Company Name </label>
                                <div class="col-md-4">
                                    <input type="text" id="company_name" name="company_name" class="form-control" value="<?php echo $staff_company_name;?> " required>
                                    <input type="hidden" id="company_name_unique_id" name="company_name_unique_id"class="form-control" value="<?php echo $staff_company_name_unique_id;?> " required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="join_date"> Join Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="join_date" name="join_date" class="form-control" value="<?php echo $join_date; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="letter_date"> To Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="to_date" name="to_date" class="form-control" value="<?php echo $to_dates; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="designation"> Designation </label>
                                    <div class="col-md-4">
                                        <input type="text" id="designation" name="designation" class="form-control" value="<?php echo $designation; ?>" required>
                                    </div>
                            </div>
                            <!--  -->
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