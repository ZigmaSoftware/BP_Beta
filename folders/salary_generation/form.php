<style type="text/css">
    
</style>

<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$letter_no          = "";
$letter_date        = $today;
$staff_name         = "";
$staff_address      = "";
$phone_no           = "";
$designation        = "";
$location           = "";
$join_date          = "";
$ctc                = "";
$gender             = "";

$screen_unique_id             = unique_id("dgscr");

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "offer_letter";

        $columns    = [
            "letter_no",
            "letter_date",
            "name",
            "gender",
            "address",
            "designation",
            "location",
            "join_date",
            "ctc"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $letter_no          = $result_values["letter_no"];
            $letter_date        = $result_values["letter_date"];
            $staff_name         = $result_values["name"];
            $staff_address      = $result_values["address"];
            $designation        = $result_values["designation"];
            $location           = $result_values["location"];
            $join_date          = $result_values["join_date"];
            $ctc                = $result_values["ctc"];
            $gender             = $result_values["gender"];

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

$gender_options        = select_option($gender_options,"Select Gender",$gender);
// mythili
$salary_category_id_option        = salary_category_name();
$salary_category_id_options        = select_option($salary_category_id_option,"Select");


?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?=$screen_unique_id;?>">
                                <label class="col-md-2 col-form-label" for="salary_no"> Salary No </label>
                                <div class="col-md-4">
                                    <input type="text" id="salary_no" name="salary_no" class="form-control border-0" value="<?php echo $letter_no; ?>" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="letter_date"> Month </label>
                                <div class="col-md-4">
                                    <input type="month" id="letter_date" name="letter_date" class="form-control" value="<?php echo date('Y-m',strtotime($letter_date)); ?>" onChange='daysInThisMonth(letter_date.value);' required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="letter_date">Salary</label>
                                <div class="col-md-4">
                                    <select id="salary_category" name="salary_category" class="select2 form-control" onChange='daysInThisMonth(salary_category.value);'required> 
                                        <?php echo $salary_category_id_options; ?>
                                   </select>
                                </div>
                            </div> 
                            <table id="salary_sub_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Location </th>
                                        <th>Emp Code</th>
                                        <th>DOJ</th>
                                        <th>Name</th>
                                        <!-- <th>Designation</th>
                                        <th>Department</th> -->
                                        <th>Gross</th>
                                        <th>Total Days</th>
                                        <th>LOP</th>
                                        <th>Salary Days</th>
                                        <th>Gross Salary</th>
                                        <!-- <th>Basic</th>
                                        <th>HRA</th>
                                        <th>Conveyance</th>
                                        <th>Medical</th>
                                        <th>Education</th>
                                        <th>Term Perf Incentive</th>
                                        <th>Gross</th> -->
                                        <th>TDS</th>
                                        <th>PF</th>
                                        <th>ESI</th>
                                        <th>Loan</th>
                                        <th>Advance</th>
                                        <th>Insurance</th>
                                        <th>Other Ded</th>
                                        <th>Total Deduction</th>
                                        <th>Net Salary</th>
                                        <th>Reimbrusment</th>
                                        <th>Takehome Salary</th>
                                        <th>Salary Type</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>                                            
                            </table>
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