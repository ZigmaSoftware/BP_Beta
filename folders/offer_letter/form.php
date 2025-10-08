<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

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
            "company_name",
            "designation",
            "location",
            "join_date",
            "gross_salary",
            "ctc",
            "department",
            "medical_insurance_premium",
            "performance_allowance",
            "income_tax",
            "professional_tax",
            "other_deduction",
            "net_salary",
            "tds_deduction_status",
            "probation",
            "performance_bonus_status",
            "pf_esi",
            "esi_pf_opt",
            "esi_pf_amt",
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
// print_r($result_values);
        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $letter_no                  = $result_values["letter_no"];
            $letter_date                = $result_values["letter_date"]; 
            $staff_name                 = $result_values["name"];
            $staff_address              = $result_values["address"];
            $staff_company_name         = $result_values["company_name"];
            $designation                = $result_values["designation"];
            $location                   = $result_values["location"];
            $join_date                  = $result_values["join_date"];
            $gross_salary               = $result_values["gross_salary"];
            $ctc                        = $result_values["ctc"];
            $gender                     = $result_values["gender"];
            $department                 = $result_values["department"];
            $medical_insurance_premium  = $result_values["medical_insurance_premium"];
            $performance_allowance      = $result_values["performance_allowance"];
            $income_tax                 = $result_values["income_tax"];
            $professional_tax           = $result_values["professional_tax"];
            $other_deduction            = $result_values["other_deduction"];
            $net_salary                 = $result_values["net_salary"];
            $probation                  = $result_values["probation"];
            $tds_deduction_status       = $result_values["tds_deduction_status"];
            $performance_bonus_status   = $result_values["performance_bonus_status"];
            $pf_esi                     = $result_values["pf_esi"];
            $esi_pf_opt                 = $result_values["esi_pf_opt"];
            $esi_pf_amt                 = $result_values["esi_pf_amt"];
            
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

$esi_pf_options   = [
    [
        "id"    => 1,
        "text"  => "ESI Only"
    ],
    [
        "id"    => 2,
        "text"  => "PF Only"
    ],
    [
        "id"    => 3,
        "text"  => "Both ESI/PF"
    ],
    [
        "id"    => 4,
        "text"  => "Medical Insurance"
    ]
];

$esi_pf_options        = select_option($esi_pf_options,"Select",$esi_pf_opt);

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

$probation_options        = select_option($probation_options,"Select Probation Month",$probation);


$company_name_option          = company_name();
$company_name_option          = select_option($company_name_option,"Select company",$staff_company_name);


$pf_esi_options   = [
    [
        "id"    => 1,
        "text"  => "CTC Working with PF & ESIC"
    ],
    [
        "id"    => 2,
        "text"  => "Consolidated Pay No PF & NO ESI"
    ],
    [
        "id"    => 3,
        "text"  => "Sales Person CTC Working"
    ],
    
];

$pf_esi_options        = select_option($pf_esi_options,"Select pf/esi",$pf_esi);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="letter_no"> Letter No </label>
                                <div class="col-md-4">
                                    <input type="text" id="letter_no" name="letter_no" class="form-control border-0" value="<?php echo $letter_no; ?>" required readonly>
                                </div>
                                <label class="col-md-2 col-form-label" for="letter_date"> Letter Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="letter_date" name="letter_date" class="form-control" value="<?php echo $letter_date; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="staff_name"> Name </label>
                                <div class="col-md-4">
                                    <input type="text" id="staff_name" name="staff_name" class="form-control" value="<?php echo $staff_name; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="staff_address"> Address </label>
                                <div class="col-md-4">
                                    <textarea name="staff_address" class="form-control" id="staff_address" rows="4" required><?php echo $staff_address; ?></textarea>
                                </div>
                                <label class="col-md-2 col-form-label" for="company_name"> Company Name </label>
                                <div class="col-md-4">
                                <select  id="company_name" name="company_name" class="select2 form-control" value="" required>
                                <?php echo $company_name_option; ?>
                                </select>
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
                            <label class="col-md-2 col-form-label" for="location"> Location </label>
                                <div class="col-md-4">
                                    <input type="text" id="location" name="location" class="form-control" value="<?php echo $location; ?>" required>
                                </div>                                
                                <label class="col-md-2 col-form-label" for="join_date"> Join Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="join_date" name="join_date" class="form-control" value="<?php echo $join_date; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="gender"> Gender </label>
                                <div class="col-md-4">
                                    <!-- <input type="date" id="gender" name="gender" class="form-control" value="<?php echo $gender; ?>" required> -->
                                    <select name="gender" id="gender" class="select2 form-control" required>
                                        <?php echo $gender_options; ?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="gross_salary"> Gross Salary (per month)</label>
                                <div class="col-md-4">
                                    <input type="number" id="gross_salary" name="gross_salary" class="form-control" onkeyup = "get_ctc();" value="<?php echo $gross_salary; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-6" style="color: red">Employee Contribution :</label>
                            </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="medical_insurance_premium">Medical Insurance Premium </label>
                                <div class="col-md-4 ">
                                    <input type="text" id="medical_insurance_premium" name="medical_insurance_premium" class="form-control" onkeyup = "get_ctc();" value="<?php echo $medical_insurance_premium; ?>" >
                                </div>
                                <label class="col-md-2 col-form-label" for="performance_allowance"> Performance Allowance </label>
                                <div class="col-md-4">
                                    <input type="text" id="performance_allowance" name="performance_allowance" onkeyup = "get_ctc();" class="form-control" value="<?php echo $performance_allowance; ?>" >
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-6" style="color: red">Employee Deduction :</label>
                            </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="income_tax">Income Tax(TDS) </label>
                                <div class="col-md-4 ">
                                    <input type="text" id="income_tax" name="income_tax" class="form-control" onkeyup = "get_ctc();" value="<?php echo $income_tax; ?>" >
                                </div>
                                <label class="col-md-2 col-form-label" for="professional_tax"> Professional Tax </label>
                                <div class="col-md-4">
                                    <input type="text" id="professional_tax" name="professional_tax" onkeyup = "get_ctc();" class="form-control" value="<?php echo $professional_tax; ?>" >
                                </div>
                                
                            </div>
                            <div class="form-group row ">     
                            <label class="col-md-2 col-form-label" for="gender"> Probation </label>
                                <div class="col-md-4">
                                    <!-- <input type="date" id="gender" name="gender" class="form-control" value="<?php echo $gender; ?>" required> -->
                                    <select name="probation" id="probation" class="select2 form-control" required>
                                    <?php echo $probation_options; ?>
                                    </select>
                               </div>
                           </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="other_deduction"> Other Deduction</label>
                                <div class="col-md-4">
                                    <input type="number"  id="other_deduction" name="other_deduction" class="form-control" value="<?php echo $other_deduction; ?>" >
                                </div>
                            </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="ctc"> CTC (per month)</label>
                                <div class="col-md-4">
                                    <input type="number" readonly id="ctc" name="ctc" class="form-control" value="<?php echo $ctc; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="net_salary"> Net Salary (per month)</label>
                                <div class="col-md-4">
                                    <input type="number" readonly id="net_salary" name="net_salary" class="form-control" value="<?php echo $net_salary; ?>" required>
                                </div>
                            </div>
                            <div class="custom-control custom-checkbox col-md-4 ">
                                <input type="checkbox" class="custom-control-input" id="tds_deduction" onClick="get_tds_deduction(this.value)" <?php if($tds_deduction_status==1){?>checked <?php } ?> >
                                <label class="custom-control-label" style="color: red"  for="tds_deduction"> TDS Deduction required </label>
                                <input type="hidden" name="tds_deduction_status" id="tds_deduction_status" value='<?=$tds_deduction_status;?>'>
                            </div>
                            <div class="custom-control custom-checkbox col-md-4 ">
                                <input type="checkbox" class="custom-control-input" id="performance_bonus" onClick="get_performance_bonus(this.value)" <?php if($performance_bonus_status==1){?>checked <?php } ?> >
                                <label class="custom-control-label" style="color: red"  for="performance_bonus"> Performance Bonus required </label>
                                <input type="hidden" name="performance_bonus_status" id="performance_bonus_status" value='<?=$performance_bonus_status;?>'>
                            </div>
                            
                            <?php 
                             $amnt = '21000'; 

                            if($ctc<=$amnt){ ?>
                            <div class="form-group row ">         
                            <label class="col-md-2 col-form-label" for="gender"> PF/ESI </label>
                                <div class="col-md-4">
                                    
                                    <select name="pf_esi" id="pf_esi" class="select2 form-control" onChange='get_esi_pf(this.value)' required>
                                        <?php echo $pf_esi_options; ?>
                                    </select>
                                </div>
                            </div>
                                <?php }else{ ?>
                                <div class="form-group row ">
                                    <label class="col-md-2 col-form-label" for="gender"> PF/ESI </label>
                                    <div class="col-md-4" >
                                   
                                    <select name="pf_esi" id="pf_esi" class="select2 form-control" >
                                        <!-- disabled -->
                                        <?php echo $pf_esi_options; ?>
                                    </select>
                                     </div>
                                </div>
                                
                                    <?php }?>

                       

                            <div id="esi_opt" style="display: none">
                                <div class="form-group row">
                                    
                                      <label class="col-md-2 col-form-label" for="esi_pf_opt"> PF/ESI Types </label>
                                        <div class="col-md-4" >
                                        
                                            <select name="esi_pf_opt" id="esi_pf_opt" class="select2 form-control"  required>
                                                <?php echo $esi_pf_options; ?>
                                            </select>
                                        </div>
                                
                                    
                                    <label class="col-md-2 col-form-label" for="esi_pf_amt">ESI/PF Amount</label>
                                        <div class="col-md-4 ">
                                            <input type="text" id="esi_pf_amt" name="esi_pf_amt" class="form-control" value="<?php echo $esi_pf_amt; ?>" required>
                                        </div>
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