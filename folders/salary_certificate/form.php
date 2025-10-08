<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$certificate_no     = "";
$certificate_date   = $today;
$gross_salary             = "";
$staff_name         = "";
$designation        = "";
$department         = "";
$join_date          = "";
$relieve_date       = "";
$purpose            = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "salary_certificate";

        $columns    = [
            "certificate_no",
            "certificate_date",
            "gross_salary",
            "name",
            "designation",
            "department",
            "join_date",
            "purpose"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $certificate_no     = $result_values["certificate_no"];
            $certificate_date   = $result_values["certificate_date"];
            $gross_salary       = $result_values["gross_salary"];
            $staff_name         = $result_values["name"];
            $designation        = $result_values["designation"];
            $department         = $result_values["department"];
            $join_date          = $result_values["join_date"];
            $purpose            = $result_values["purpose"];

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

$staff_name_options = staff_name();

$staff_name_options = select_option($staff_name_options,"Select Staff",$staff_name);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="certificate_no"> Certificate No </label>
                                <div class="col-md-4">
                                    <input type="text" id="certificate_no" name="certificate_no" class="form-control border-0" value="<?php echo $certificate_no; ?>" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="certificate_date"> Certificate Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="certificate_date" name="certificate_date" class="form-control" value="<?php echo $certificate_date; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_name"> Name </label>
                                <div class="col-md-4">
                                    <!-- <input type="text" id="staff_name" name="staff_name" class="form-control" value="<?php echo $staff_name; ?>" required> -->
                                    <select name="staff_name" id="staff_name" class="select2 form-control" onchange="get_staff_details(this.value)" required>
                                        <?php echo $staff_name_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="gross_salary">  Gross Salary </label>
                                <div class="col-md-4">
                                    <input type="number" name="gross_salary" id="gross_salary" class="form-control" value="<?=$gross_salary;?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="designation"> Designation </label>
                                <div class="col-md-4">
                                    <input type="text" id="designation" name="designation" class="form-control" value="<?php echo $designation; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="department"> Department </label>
                                <div class="col-md-4">
                                    <input type="text" id="department" name="department" class="form-control" value="<?php echo $department; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="join_date"> Join Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="join_date" name="join_date" class="form-control" value="<?php echo $join_date; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="purpose "> Purpose </label>
                                <div class="col-md-4">
                                    <input type="text" id="purpose" name="purpose" class="form-control" value="<?php echo $purpose; ?>" required>
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