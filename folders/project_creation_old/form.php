<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text            = "Save";
$btn_action          = "create";
$is_btn_disable      = "";

$unique_id           = "";

$branch_name         = "";
$company_name = "";
$short_name          = "";
$address             = "";
$country             = "";
$state               = "";
$city                = "";
$pin_code            = "";
$phone_number        = "";
$mobile_number       = "";
$gst_number          = "";
$pan_number          = "";
$email_id            = "";
$website             = "";
$map_radius          = 30;
$user_latitute       = "";
$user_longitute      = "";
$website             = "";
$is_active           = 1;

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "project_creation";

$columns = [
    "unique_id",
    "sales_order_id",
    "created_type",
    "company_name",
    "company_code",
    "client_name",
    "capacity",
    "project_name",
    "project_code",
    "project_date",
    "duration",
    "cost_center",
    "application_type",
    "country",
    "state",
    "city",
    "address",
    "pin_code",
    "pan_number",
    "gst_number",
    "gst_date",
    "contact_person",
    "contact_number",
    "contact_email_id",
    "website",
    "logo", 
    "description",
    "is_active"
];


        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);
        // print_r($result_values);
        // die();
        if ($result_values->status) {

            $result_values      = $result_values->data[0];
            $unique_id           = $result_values["unique_id"];
            $sales_order       = $result_values["sales_order_id"];
            $created_type      = $result_values["created_type"]; 
            $company_name        = $result_values["company_name"];
            $company_code        = $result_values["company_code"];
            $client_name         = $result_values["client_name"];
            $capacity            = $result_values["capacity"];
            $project_name        = $result_values["project_name"];
            $project_code        = $result_values["project_code"];
            $project_date        = $result_values["project_date"];
            $duration            = $result_values["duration"];
            $cost_center         = $result_values["cost_center"];
            $application_type    = $result_values["application_type"];
            $country             = $result_values["country"];
            $state               = $result_values["state"];
            $city                = $result_values["city"];
            $address             = $result_values["address"];
            $pin_code            = $result_values["pin_code"];
            $pan_number          = $result_values["pan_number"];
            $gst_number          = $result_values["gst_number"];
            $gst_reg_date        = $result_values["gst_date"];
            $contact_person      = $result_values["contact_person"];
            $contact_number      = $result_values["contact_number"];
            $contact_email_id    = $result_values["contact_email_id"];
            $website             = $result_values["website"];
            $logo                = $result_values["logo"];
            $description         = $result_values["description"];
            $is_active           = $result_values["is_active"];


            $btn_text            = "Update";
            $btn_action          = "update";
        } else {
            $btn_text            = "Error";
            $btn_action          = "error";
            $is_btn_disable      = "disabled='disabled'";
        }
    }
}

$active_status_options= active_status($is_active);

$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);

// $sales_order_options        = sales_order();
// $sales_order_options        = select_option($sales_order_options,"Select the Sales Order",$sales_order);

$application_type_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "CBG"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "COMPOST"
    ],
    3 => [
        "unique_id" => "3",
        "value"     => "CBG/COMPOST"
    ]
];
$application_type_options    = select_option($application_type_options,"Select Application",$application_type); 


$country_options        = country();
$country_options        = select_option($country_options,"Select the Country",$country);

//state options
 $state_options         = state("",$country);
 $state_options         = select_option($state_options,"Select the State",$state);

 //city options

 $city_options         = city("",$state);
 $city_options         = select_option($city_options,"Select the City",$city);
//  echo "HELLOOOO";
// echo "<!-- DEBUG: company_name = $company_name -->";
// echo "<!-- DEBUG: company_code = $company_code -->";
// echo "<!-- DEBUG: client_name = $client_name -->";
// echo "<!-- GET unique_id: " . ($_GET['unique_id'] ?? 'not set') . " -->";
?>


<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <form class="was-validated"  autocomplete="off" >
               <div class="row">
                  <div class="col-12">
<div class="form-group row">
    <label class="col-md-2 col-form-label textright textright">Creation Type<span style="color:red">*</span></label>
    <div class="col-md-3">
        <div class="form-check form-check-inline">
<input class="form-check-input" type="radio" name="creation_type" id="normal_create" value="normal"
<?php echo ($created_type == "normal") ? "checked" : ""; ?> onchange="toggleCreationType()">
 <label class="form-check-label" for="normal_create">Create Normally</label>
        </div>
        <div class="form-check form-check-inline">
<input class="form-check-input" type="radio" name="creation_type" id="sales_create" value="sales_order"
<?php echo ($created_type == "sales_order") ? "checked" : ""; ?> onchange="toggleCreationType()">
<label class="form-check-label" for="sales_create">Create from Sales Order</label>
        </div>
    </div>

    <label class="col-md-2 col-form-label textright textright" for="sales_order_id">Select Sales Order</label>
    <div class="col-md-3">
        <select name="sales_order_id" id="sales_order_id" class="form-control select2" onchange="fetchSalesOrderDetails()" disabled>
            <?php 
            $sales_order_options = sales_order();
            $sales_order_options = select_option($sales_order_options, "Select the Sales Order", $sales_order);
            echo $sales_order_options;
            ?>
        </select>
    </div>
</div>


                   
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="company_name">Company Name<span style=color:red>*</span> </label>
                        <div class="col-md-3">
                            <select name="company_name" id="company_name" class="select2 form-control" onchange="get_company_code(this.value);" required>
                           <?php echo $company_name_options;?>
                           </select>
                           
                        </div>

                        <label class="col-md-2 col-form-label textright branch_div" for="company_code">Company Code</label>
                        <div class="col-md-3 branch_div">
        
                           
                           <input type="number" id="company_code" name="company_code" class="form-control branch_div_input" value="<?php echo $company_code; ?>" readonly>
                        </div> </div>
                        
                        
                          <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="client_name">Client Name<span style=color:red>*</span> </label>
                        <div class="col-md-3">
                           <input type="text" id="client_name" name="client_name" class="form-control branch_div_input" value="<?php echo $client_name; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label textright branch_div" for="capacity">Capacity<span style=color:red>*</span></label>
                        <div class="col-md-3 branch_div">
                           <input type="number" id="capacity" name="capacity" class="form-control branch_div_input" value="<?php echo $capacity; ?>" required>
                        </div>                     </div>
                        <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="project_name">Project Name<span style=color:red>*</span> </label>
                        <div class="col-md-3">
                           <input type="text" id="project_name" name="project_name" class="form-control branch_div_input" value="<?php echo $project_name; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label textright branch_div" for="label">Project Code<span style=color:red>*</span></label>
                        <div class="col-md-3 branch_div">
                           <input type="number" id="project_code" name="project_code" class="form-control branch_div_input" value="<?php echo $project_code; ?>" required>
                        </div> </div>
                        
                        <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="project_date">Project Date </label>
                        <div class="col-md-3">
                           <input type="date" id="project_date" name="project_date" class="form-control branch_div_input" value="<?php echo $project_date; ?>" >
                        </div>

                        <label class="col-md-2 col-form-label textright branch_div" for="duration">Duration </label>
                        <div class="col-md-3 branch_div">
                           <input type="number" id="duration" name="duration" class="form-control branch_div_input" value="<?php echo $duration; ?>" >
                        </div> </div>
                        
                        
                        
                        <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="cost_center">Cost Center<span style=color:red>*</span> </label>
                        <div class="col-md-3">
                           <input type="text" id="cost_center" name="cost_center" class="form-control branch_div_input" value="<?php echo $cost_center; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label textright branch_div" for="label">Application Type<span style=color:red>*</span></label>
                        <div class="col-md-3 branch_div">
                            <select name="application_type" id="application_type" class="select2 form-control"  required >
                           <?php echo $application_type_options;?>
                           </select>
                        </div> </div>
                     
                     
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="short_name"> Country <span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <select name="country" id="country" class="select2 form-control" onchange="get_states(this.value);" required>
                           <?php echo $country_options;?>
                           </select>
                        </div>
                        <label class="col-md-2 col-form-label textright" for="is_active"> State<span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <select name="state" id="state" class="select2 form-control" onchange="get_cities(this.value);" required >
                           <?php echo $state_options;?>
                           </select>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="city"> City <span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <select name="city" id="city" class="select2 form-control" required >
                           <?php echo $city_options;?>
                           </select>
                        </div>
                        
                        <label class="col-md-2 col-form-label textright" for="address"> Address<span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $address; ?></textarea>
                        </div>                     </div>
                        
                        
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="is_active"> Pin Code<span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <input type="number" name="pin_code" id="pin_code" class="form-control" onkeypress="number_only_pincode(event)" value="<?php echo $pin_code; ?>" required>
                        </div>
                        <label class="col-md-2 col-form-label textright" for="pan_number"> PAN Number</label>
                        <div class="col-md-3">
<input type="text" name="pan_number" id="pan_number" 
       onkeypress="validate_pan_number(event)" 
       maxlength="10"
       value="<?php echo $pan_number; ?>" 
       class="form-control" 
       placeholder="Eg: ABCDE1234F" 
       >

                        </div>
                        
                     </div>
                     
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="gst_number"> GST Number </label>
                        <div class="col-md-3">
<input type="text" name="gst_number" id="gst_number" 
       onkeypress="number_only_gst(event)" 
       maxlength="15"
       value="<?php echo $gst_number; ?>" 
       class="form-control"  placeholder="Eg: 27ABCDE1234F1Z5"  > </div>
       
       
  <label class="col-md-2 col-form-label textright" for="gst_reg_date"> GST Reg.Date </label>
                        <div class="col-md-3">
<input type="date" name="gst_reg_date" id="gst_reg_date" 
       
       value="<?php echo $gst_date; ?>" 
       class="form-control"   >

                        </div>                      
                     </div>
 
     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="contact_person"> Contact Person </label>
                        <div class="col-md-3">
<input type="text" name="contact_person" id="contact_person" 
       value="<?php echo $contact_person; ?>" 
       class="form-control"   > </div>
       
       
  <label class="col-md-2 col-form-label textright" for="contact_number"> Contact  Number </label>
                        <div class="col-md-3">
<input type="number" name="contact_number" id="contact_number" onkeypress="number_only_mobile(event)" value="<?php echo $contact_number; ?>" 
       class="form-control"    >

                        </div></div>                      
                        
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="contact_email_id">Contact  Email ID </label>
                        <div class="col-md-3">
                           <input type="text" id="contact_email_id" name="contact_email_id" onkeypress="validate_email(event)"  class="form-control" value="<?php echo $contact_email_id; ?>" >
                        </div>
                        <label class="col-md-2 col-form-label textright" for="website"> Website</label>
                        <div class="col-md-3">
                           <input type="text" name="website" id="website" class="form-control" value="<?php echo $website; ?>"  >
                        </div>
                     </div>


                      <div class="form-group row ">
                       <label class="col-md-2 col-form-label textright" for="company_logo"> File Attachment </label>
                                <div class="col-md-3">
                                   <input type="file" id="company_logo" name="company_logo" class="form-control dropify"
     data-default-file="uploads/project_creation/<?php echo $logo ?>" >
       
<input type="hidden" name="existing_company_logo" id="existing_company_logo" value="<?php echo $logo ?>">
 <span style="color:red">Allowed formats: jpg, jpeg, png,pdf</span>
                                </div>
                                
<label class="col-md-2 col-form-label textright" for="description">Project Description<span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <textarea class="form-control" id="description" name="description" rows="3" ><?php echo $description; ?> </textarea>
                        </div>
                        
                        </div>
                        
                        
<div class="form-group row ">

<label class="col-md-2 col-form-label textright" for="description"> Active Status</label>
                        <div class="col-md-3">
                           <select name="is_active" id="is_active" class="select2 form-control" >
                           <?php echo $active_status_options;?>
                           </select>
                        </div></div>


                     <div class="form-group row btn-action">
                        <div class="col-md-12">
                            <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                            <?php echo btn_cancel($btn_cancel);?>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <!-- end card-body -->
      </div>
      <!-- end card -->
   </div>
   <!-- end col -->
</div>