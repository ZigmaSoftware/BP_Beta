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
$certificate_type   = 1;
$staff_name         = "";
$designation        = "";
$department         = "";
$join_date          = "";
$relieve_date       = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "service_certificate";

        $columns    = [
            "certificate_no",
            "certificate_date",
            "certificate_type",
            "name",
            "designation",
            "department",
            "join_date",
            "purpose",
            "purpose_type",
            "address",
            "gross_salary",
            "net_salary",
            "relieve_date"
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
            $certificate_type   = $result_values["certificate_type"];
            $staff_name         = $result_values["name"];
            $designation        = $result_values["designation"];
            $department         = $result_values["department"];
            $join_date          = $result_values["join_date"];
            $purpose          = $result_values["purpose"];
            $purpose_type          = $result_values["purpose_type"];
            $address          = $result_values["address"];
            $gross_salary          = $result_values["gross_salary"];
            $net_salary          = $result_values["net_salary"];
            
            $relieve_date       = $result_values["relieve_date"];

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

$certificate_type_options = [
    [
        "id"    => 1,
        "text"  => "Live"
    ],
    [
        "id"    => 2,
        "text"  => "Relieve(d)"
    ],
];

$certificate_type_options = select_option($certificate_type_options,"Select Certificate Type",$certificate_type);

$staff_name_options = staff_name();

// print_r($staff_name_options);

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
                                <label class="col-md-2 col-form-label" for="certificate_type"> Certificate Type </label>
                                <div class="col-md-4">
                                    <select name="certificate_type" id="certificate_type" class="select2 form-control" onchange="relieve_date_show()" required>
                                        <?php echo $certificate_type_options; ?>
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
                                <label class="col-md-2 col-form-label" for="join_date"> Join Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="join_date" name="join_date" class="form-control" value="<?php echo $join_date; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="gross_salary"> Gross Salary </label>
                                <div class="col-md-4">
                                    <input type="text" id="gross_salary" name="gross_salary" class="form-control" value="<?php echo $gross_salary; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label relieve_date_div" for="relieve_date "> Relieve(d) Date </label>
                                <div class="col-md-4">
                                    <input type="date" id="relieve_date" name="relieve_date" class="form-control relieve_date_div relieve_date_inp" value="<?php echo $relieve_date; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">                                
                                <label class="col-md-2 col-form-label" for="purpose_type"> Purpose Type</label>
                              <div class="col-md-4">
                                <select name="purpose_type" id="purpose_type" class="select2 form-control" onchange="showDiv(this.value)">
                               <?php if($purpose_type!=""){?>
                                <option value="<?php echo $purpose_type;?>"> <?php echo $purpose_type;?></option>
                                <?php }else{ ?>
                                <option value="">select</option>
                                <?php }?>
                                <option value="loan"  name="loan" id="loan" required >Loan</option>
                                <option value="others" name="others" id="others" required>Others</option>              
                               </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="net_salary"> Net Salary </label>
                                <div class="col-md-4">
                                    <input type="text" id="net_salary" name="net_salary" class="form-control" value="<?php echo $net_salary; ?>" required>
                                </div>
                            </div>
    
                            <div class="form-group row ">   
                            
                               <label class="col-md-2 col-form-label"  id="hidden_div1" style="display:none;"  for="address">Address</label>
                             
                                <div class="col-md-4" id="hidden_div" style="display:none;">
                                    <!-- <label class="col-md-2 col-form-label" for="address">Address</label> -->
                                    <textarea name="address" id="address" class="form-control"><?php echo $address;?></textarea>
                                </div>
                    
                             <label class="col-md-2 col-form-label" id="hidden_add1" style="display:none;" for="purpose"> Purpose </label>
                                <div class="col-md-4" id="hidden_add" style="display:none;">
                                <!-- <label class="col-md-2 col-form-label" for="address">Address</label> -->
                                    <input type="text" name="purpose" id="purpose" value="<?php echo $purpose;?>" class="form-control">               
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