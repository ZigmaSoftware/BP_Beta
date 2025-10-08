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

        $table      =  "company_creation";

        $columns    = [
            "company_name",
            "company_code",
            "country",
            "state",
            "city",
            "pin_code",
            "tel_number",
            "pan_number",
            "gst_number",
            "gst_date",
            "contact_person",
            "contact_number",
            "contact_email_id",
            "website",
            "latitude",
            "longitude",
            "logo",
            "file_attach",
             "address",
            "is_active"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data[0];

            $company_name        = $result_values["company_name"];
            $company_code        = $result_values["company_code"];
            $country             = $result_values["country"];
            $state               = $result_values["state"];
            $city                = $result_values["city"];
            $pin_code            = $result_values["pin_code"];
            $tel_number          = $result_values["tel_number"];
            $pan_number          = $result_values["pan_number"];
            $gst_number          = $result_values["gst_number"];
            $gst_date            = $result_values["gst_date"];
            $contact_person      = $result_values["contact_person"];
            $contact_number      = $result_values["contact_number"];
            $contact_email_id    = $result_values["contact_email_id"];
            $website             = $result_values["website"];
            $latitute            = $result_values["latitude"];
            $longitute           = $result_values["longitude"];
            $logo                = $result_values["logo"];
            $file_attach         = $result_values["file_attach"];
            $address             = $result_values["address"];
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


//country options
$country_options        = country();
   
$country_options        = select_option($country_options,"Select the Country",$country);

//state options
 $state_options         = state("",$country);
 
 $state_options         = select_option($state_options,"Select the State",$state);

 //city options

 $city_options         = city("",$state);
 $city_options         = select_option($city_options,"Select the City",$city);
?>

<div class="modal fade" id="documentModal" tabindex="-1" aria-labelledby="documentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Uploaded Documents</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body d-flex flex-wrap gap-3 justify-content-start" id="documentModalBody">
        <!-- Dynamic content here -->
      </div>
    </div>
  </div>
</div>

<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <form class="was-validated"  autocomplete="off"  type="multipart/form-data">
               <div class="row">
                  <div class="col-12">
                     <div class="form-group row ">
                         
                        <label class="col-md-2 col-form-label textright" for="company_name">Company Name<span style=color:red>*</span> </label>
                        <div class="col-md-3">
                           <input type="text" id="company_name" name="company_name" class="form-control branch_div_input" value="<?php echo $company_name; ?>" required>
                        </div>

                        <label class="col-md-2 col-form-label textright branch_div" for="label">Company Code<span style=color:red>*</span></label>
                        <div class="col-md-3 branch_div">
                           <input type="text" id="company_code" name="company_code" class="form-control branch_div_input" value="<?php echo $company_code; ?>" required>
                        </div>
                     
                     
                     </div>
                     
                     
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
                        <label class="col-md-2 col-form-label textright" for="is_active"> Pin Code<span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <input type="number" name="pin_code" id="pin_code" class="form-control" onkeypress="number_only_pincode(event)" value="<?php echo $pin_code; ?>" required>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="tel_number">Telephone Number</label>
                        <div class="col-md-3">
                           <input type="number" name="tel_number" id="tel_number" class="form-control"  value="<?php echo $tel_number; ?>" >
                        </div>
                        <label class="col-md-2 col-form-label textright" for="pan_number"> PAN Number<span style=color:red>*</span></label>
                        <div class="col-md-3">
<input type="text" name="pan_number" id="pan_number" 
       onkeypress="validate_pan_number(event)" 
       maxlength="10"
       value="<?php echo $pan_number; ?>" 
       class="form-control" 
       placeholder="Eg: ABCDE1234F" 
       required>

                        </div>
                        
                     </div>
                     
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="gst_number"> GST Number <span style=color:red>*</span></label>
                        <div class="col-md-3">
<input type="text" name="gst_number" id="gst_number" 
       onkeypress="number_only_gst(event)" 
       maxlength="15"
       value="<?php echo $gst_number; ?>" 
       class="form-control"  placeholder="Eg: 27ABCDE1234F1Z5"  required> </div>
       
       
  <label class="col-md-2 col-form-label textright" for="gst_reg_date"> GST Reg.Date <span style=color:red>*</span></label>
                        <div class="col-md-3">
<input type="date" name="gst_reg_date" id="gst_reg_date" 
       
       value="<?php echo $gst_date; ?>" 
       class="form-control"   required>

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
              
                     
                     
                     
                        <!----------------------------------->
                     

<!--<div class="form-group row">-->
<!--                        <label class="col-md-2 col-form-label" for="map_radius"> Radius </label>-->
<!--                        <div class="col-md-4">-->
                           <input type="hidden" min="1" id="map_radius" name="map_radius" class="form-control" value="<?=$map_radius;?>" required>
                     <!--   </div>                        -->
                     <!--</div>-->
                     <div class="form-group row">
                        <label class="col-md-2 col-form-label textright" for="user_latitude"> Latitude</label>
                        <div class="col-md-3">
                           <input type="text" name="user_latitude" id="user_latitude" class="form-control" value="<?=$user_latitute;?>" readonly required>
                        </div>
                        <label class="col-md-2 col-form-label textright" for="user_longitude"> Longitude</label>
                        <div class="col-md-3">
                           <input type="text" name="user_longitude" id="user_longitude" class="form-control" value="<?=$user_longitute;?>" readonly required>
                        </div>
                     </div>

                      <div class="form-group row ">
                       <label class="col-md-2 col-form-label textright" for="company_logo"> Company Logo  </label>
                                <div class="col-md-3">
                                   <input type="file" id="company_logo" name="company_logo" class="form-control dropify"
     data-default-file="uploads/company_creation/<?php echo $logo ?>" required>
       
<input type="hidden" name="existing_company_logo" id="existing_company_logo" value="<?php echo $logo ?>">
 <span style="color:red">Allowed formats: jpg, jpeg, png</span>
                                </div>
                                
                                
<label class="col-md-2 col-form-label textright" for="test_file_qual">Files (PAN, GST etc)</label>
<div class="col-md-4">
    <input type="file" multiple id="test_file_qual" name="test_file[]" class="form-control" accept=".jpg,.jpeg,.png,.pdf">
    <input type="hidden" name="existing_file_attach" id="existing_file_attach" value="<?php echo $file_attach ?>">
    <div id="file_preview_list" class="mt-2"></div>
</div>




<!-----------------------------file edit------>
<!--<div class="form-group row">-->
    <!-- Company Logo -->
    <!--<label class="col-md-2 col-form-label" for="company_logo">-->
    <!--    Company Logo <span style="color:red">*</span>-->
    <!--</label>-->
    <!--<div class="col-md-4">-->
    <!--    <input type="file" id="company_logo" name="company_logo" class="form-control dropify"-->
    <!--        data-default-file="uploads/company_creation/<?php echo $logo ?>">-->
    <!--    <span style="color:red">Allowed formats: jpg, jpeg, png</span>-->
    <!--</div>-->


<!--    <label class="col-md-2 col-form-label" for="biometric_id">-->
<!--        File Attachment <span style="color:red">*</span>-->
<!--    </label>-->
<!--    <div class="col-md-4">-->
<!--        <div class="row">-->
          
          <?php
// <!--            $file_array = explode(',', $file_attach);-->
// <!--            foreach ($file_array as $index => $file):-->
// <!--                if (!empty(trim($file))): 
?>
<!--                    <div class="col-md-12 mb-2">-->
<!--                        <input type="file" name="test_file_qual[]" class="form-control dropify"-->
<!--                            data-default-file="uploads/company_creation/
<?php 
// echo trim($file) 
?>
" />-->
<!--                    </div>-->
               <?php 
// endif;
// <!--            endforeach;-->
            
?>
            <!-- Extra empty input to allow new upload -->
<!--            <div class="col-md-12 mb-2">-->
<!--                <input type="file" name="test_file_qual[]" class="form-control dropify" />-->
<!--            </div>-->
<!--        </div>-->
<!--        <span style="color:red">Allowed formats: jpg, jpeg, png</span>-->
<!--    </div>-->
<!--</div>-->
<!-----------------------------file edit------>















                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label textright" for="address"> Address<span style=color:red>*</span></label>
                        <div class="col-md-3">
                           <textarea class="form-control" id="address" name="address" rows="3" required><?php echo $address; ?></textarea>
                        </div>
                        
           <!----------------------------------->              
                        
                        <label class="col-md-2 col-form-label textright" for="description"> Active Status</label>
                        <div class="col-md-3">
                           <select name="is_active" id="is_active" class="select2 form-control" >
                           <?php echo $active_status_options;?>
                           </select>
                        </div>
                     </div>
                     
                 
                     <div class="form-group row">
                        <div class="col-md-12"> 
                        
                           <div id="branch_map" style="width: 80%; height: 300px; margin: 0 auto;"></div>

                        </div>               
                     </div>
                   
                   <!---------------------------->  
      
                     <div class="form-group row btn-action">
                        <div class="col-md-12">
                           <!-- Cancel,save and update Buttons -->
                          
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