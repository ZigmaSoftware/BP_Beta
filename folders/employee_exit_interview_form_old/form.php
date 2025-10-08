<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id              = "";
$is_active              = 1;
$reason                 = "";
$likeMost               = "";
$improvement            = "";
$comeBack               = "";
$comments               ="";
$remarks                ="";
$staff_name             =$_SESSION["staff_name"];
$designation_name       =$_SESSION["designation_type"];
//$department_name        =$_SESSION["department_name"];
//$location               =$_SESSION['sess_user_location'];

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "employee_exit_interview_form";

        $columns    = [
            "reason",
            "likeMost",
            "improvement",
            "comeBack",
            "comments",
            "remarks",
            "is_active"
            

        ];

        $table_details   = [
            $table,
            $columns
        ];

        $result_values  = $pdo->select($table_details, $where);
        // print_r($result_values);
        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $reason                     = $result_values["reason"];
            $likeMost                   = $result_values["likeMost"];
            $improvement                 = $result_values["improvement"];
            $comeBack                   = $result_values["comeBack"];
            $comments                   = $result_values["comments"];
            $remarks                   = $result_values["remarks"];
            $is_active          = $result_values[0]["is_active"];

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

$reason_options        = [
    "Health Reasons" => [
        "unique_id" => "Health Reasons",
        "value"     => "Health Reasons",
    ],
    "Personal Reasons" => [
        "unique_id" => "Personal Reasons",
        "value"     => "Personal Reasons",
    ],
    "Further Education" => [
        "unique_id" => "Further Education",
        "value"     => "Further Education",
    ],
    "To relocate with spouse" => [
        "unique_id" => "To relocate with spouse",
        "value"     => "To relocate with spouse",
    ],
    "Retirement" => [
        "unique_id" => "Retirement",
        "value"     => "Retirement",
    ],
    "To accept other employment" => [
        "unique_id" => "To accept other employment",
        "value"     => "To accept other employment",
    ],
    "Dissatisfaction" => [
        "unique_id" => "Dissatisfaction",
        "value"     => "Dissatisfaction",
    ],
    "Involuntary Termination" => [
        "unique_id" => "Involuntary Termination",
        "value"     => "Involuntary Termination",
    ],
    "Other Reasons" => [
        "unique_id" => "Other Reasons",
        "value"     => "Other Reasons",
    ]
];
$reason_options        = select_option($reason_options, "Select", $reason);

$comeBack_options        = [
    "Yes" => [
        "unique_id" => "Yes",
        "value"     => "Yes",
    ],
    "No" => [
        "unique_id" => "No",
        "value"     => "No",
    ]
];
$comeBack_options        = select_option($comeBack_options, "Select", $comeBack);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <input type="text" id="staff_name" name="staff_name" value='<?php echo  $staff_name; ?>'>
                            <input type="text" id="designation_name" name="designation_name" value='<?php echo  $designation_name; ?>'>
                            <!-- <input type="text" id="department_name" name="department_name" value='<?php echo  $department_name; ?>'> -->
                            <!-- <input type="text" id="location" name="location" value='<?php echo  $location; ?>'> -->
                            <div class="form-group row">
                                <label for="reason">1. What is your principal reason for leaving the organization?</label>
                                <select name="reason" id="reason" class="select2 form-control" onchange="getSelectedValue(this);"><?php echo $reason_options; ?></select>
                            </div>
                            <div id="employmentOptions" style="display: none;">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label>Reasons for Accepting Other Employment:</label>
                                        <div>
                                            <div class=" radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Promotion / Career Advancement" name="employment_reason">
                                                <label for=""> Promotion / Career Advancement </label>
                                            </div>
                                            <div class="radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Distance To / From Work" name="employment_reason">
                                                <label for=""> Distance To / From Work</label>
                                            </div>
                                            <div class=" radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Better benefits" name="employment_reason">
                                                <label for=""> Better benefits </label>
                                            </div>
                                            <div class="radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Better - Pay" name="employment_reason">
                                                <label for=""> Better - Pay</label>
                                            </div>
                                            <div class=" radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Work Schedule" name="employment_reason">
                                                <label for=""> Work Schedule </label>
                                            </div>
                                            <div class="radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Career Change" name="employment_reason">
                                                <label for=""> Career Change</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="dissatisfactionOptions" style="display: none;">
                                <div class="form-group row">
                                    <div class="col-md-12">
                                        <label>Dissatisfaction Reasons:</label>
                                        <div>

                                            <div class=" radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Job Dissatisfaction" name="dissatisfaction_reason">
                                                <label for="">Job Dissatisfaction </label>
                                            </div>
                                            <div class="radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Dissatisfaction with Supervisor" name="dissatisfaction_reason">
                                                <label for=""> Dissatisfaction with Supervisor</label>
                                            </div>
                                            <div class=" radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Dissatisfaction with work associates" name="dissatisfaction_reason">
                                                <label for=""> Dissatisfaction with work associates </label>
                                            </div>
                                            <div class="radio radio-primary form-check-inline">
                                                <input type="radio" id="" value="Dissatisfaction of working conditions" name="dissatisfaction_reason">
                                                <label for=""> Dissatisfaction of working conditions</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" id="otherReasonTextArea" style="display: none;">
                                <label for="otherReasonText">Please specify the other reason:</label>
                                <textarea id="otherReasonText" name="otherReasonText" class="form-control"><?php echo $otherReasonText; ?></textarea>
                            </div>
                            <div class="form-group row">
                                <label for="likeMost">2. What did you like the most about your employment experience at Xeon?</label>
                                <textarea id="likeMost" name="likeMost" class="form-control"><?php echo $likeMost; ?></textarea>
                            </div>
                            <div class="form-group row">
                                <label for="improvement">3. What are the areas of improvement you would like to suggest?</label>
                                <textarea id="improvement" name="improvement" class="form-control"><?php echo $improvement; ?></textarea>
                            </div>
                            <div class="form-group row">
                                <label for="comeBack">4. Would you be interested in coming back to Xeon for suitable employment in the future? Yes / No:</label> 
                                <select name="comeBack" id="comeBack" class="select2 form-control"><?php echo $comeBack_options; ?></select>

                            </div>
                            <div class="form-group row">
                                <label for="comments">5. Please give your comments:</label>
                                <textarea id="comments" name="comments" class="form-control"><?php echo $comments; ?></textarea>
                            </div>
                            <div class="form-group row">
                                <label for="remarks">6. Please feel free to give any other remarks:</label>
                                <textarea id="remarks" name="remarks" class="form-control"><?php echo $remarks; ?></textarea>
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

<script>
    function getSelectedValue(selectElement) {
        var selectedValue = selectElement.value;

        var radioButtons = document.querySelectorAll('input[type="radio"]');
        radioButtons.forEach(function(radio) {
            radio.checked = false;
        });

        // Clear the textarea
        document.getElementById("otherReasonText").value = "";

        // Get references to the radio buttons and textarea
        var employmentOptions = document.getElementById("employmentOptions");
        var dissatisfactionOptions = document.getElementById("dissatisfactionOptions");
        var otherReasonTextArea = document.getElementById("otherReasonTextArea");

        if (selectedValue === "To accept other employment") {
            employmentOptions.style.display = "block";
            dissatisfactionOptions.style.display = "none";
            otherReasonTextArea.style.display = "none";
        } else if (selectedValue === "Dissatisfaction") {
            employmentOptions.style.display = "none";
            dissatisfactionOptions.style.display = "block";
            otherReasonTextArea.style.display = "none";
        } else if (selectedValue === "Other Reasons") {
            employmentOptions.style.display = "none";
            dissatisfactionOptions.style.display = "none";
            otherReasonTextArea.style.display = "block";
        }
    }
</script>