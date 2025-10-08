<!-- This file Only PHP Functions -->
<?php include 'function.php';?>
<script>
   var sublist = "";
</script>

<?php 
   // Form variables
   $btn_text           = "Save";
   $btn_action         = "create"; 
   $is_btn_disable     = "";
   $form_type          = "Create";  
   $unique_id          = "";
   $screen_unique_id   = unique_id("perscr"); // It is Current Screen Unique id
   $entry_date         = $today;
   $designation = ""; 
   $sub_counter        = 1;
   $sublist_data       = "";
   $start_count_class       = " display-none ";
   
    $user_name_select = "";
   if(isset($_GET["unique_id"])) 
   {
       if (!empty($_GET["unique_id"])) 
       {
            $uni_dec = str_replace(" ", "+",$_GET['unique_id']);
            $get_uni_id           = openssl_decrypt(base64_decode($uni_dec), $enc_method, $enc_password,OPENSSL_RAW_DATA, $enc_iv);


        $unique_id  = $get_uni_id;
           $where      = [
               "unique_id" => $unique_id
           ];
   
           $table            = "periodic_creation_main";
           $table_sub        = "periodic_creation_sub";
   
           $columns    = [

            "user_id",
            "screen_unique_id",
               
           ];
   
           $table_details   = [
               $table,
               $columns
           ]; 
   
           $select_result  = $pdo->select($table_details,$where);
   
           if ($select_result->status) {
   
               $select_result       = $select_result->data;
               $user_name           = $select_result[0]["user_id"];
               $screen_unique_id    = $select_result[0]["screen_unique_id"];

               $exp_site_name       = explode(",", $site_name);
               $exp_department_name = explode(",", $department_name);
   
               $form_type           = "Update";
               $btn_text            = "Update";
               $btn_action          = "update";

               $user_name_select    = " disabled ";
   
               // $sublist_data     = "";
           } else {
               $btn_text           = "Error";
               $btn_action         = "error";
               $is_btn_disable     = "disabled='disabled'";
   
               print_r($select_result);
           }
       }
   }

// $user_name_options = user_name();
// $user_name_options =  select_option_user($user_name_options,"Select",$user_name);

// $site_options      = site_name();
// $site_options      = select_option($site_options,"All Site",$exp_site_name);

// $department_type_options = department_type();
// $department_type_options = select_option($department_type_options,"All Departments",$exp_department_name);

// $category_name_option = category_creation('',$department_name);
// $category_name_option = select_option_category($category_name_option, "All Categories",$complaint_category);

// $level_options =[
//                     "1" => [
//                         "unique_id" => "1",
//                         "value"     => "Level 1",
//                       ],
//                     "2" => [
//                         "unique_id" => "2",
//                         "value"     => "Level 2",
//                         ],
//                     "3" => [
//                       "unique_id" => "3",
//                       "value"     => "Level 3",
//                         ],
//                     "4" => [
//                         "unique_id" => "4",
//                         "value"     => "Level 4",
//                         ],
//                     "5" => [
//                         "unique_id" => "5",
//                         "value"     => "Level 5",
//                         ],
//                     "6" => [
//                         "unique_id" => "6",
//                         "value"     => "Level 6",
//                         ],
//                     "7" => [
//                         "unique_id" => "7",
//                         "value"     => "Level 7",
//                         ],
//                 ];
//   $level_options  = select_option($level_options,"Select",$level_opt);

$staff_options      = staff_id_bp();
$staff_options      = select_option($staff_options,"Select Staff",$name); 

$department_name_options        = department();
$department_name_options        = select_option($department_name_options,"Select the Department",$department_name);
?>
   <!-- start page title -->
<div class="row">
<div class="col-12">
<div class="page-title-box">
<h4 class="page-title">Periodic Creation <?=$form_type;?></h4>
<div>
<ol class="breadcrumb m-0">
<li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
<li class="breadcrumb-item active">Periodic Creation</li>
</ol>
</div>
</div>
</div>
</div>
<br>
<!-- end page title -->
<input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <form class="was-validated" id="periodic_creation_form_main" name="periodic_creation_form_main">
               <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?=$screen_unique_id;?>">
               <div class="row">
                  <div class="col-12">
                     <div class="form-group row ">
                        <input type="hidden" name="user_name" id="user_name" class="form-control" value='<?php echo  $user_name; ?>'>
                        <div class="col-md-3">
                        </div>
                        <div class="col-md-5">
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-1 col-form-label" for="user_name">User Name</label>
                        <div class="col-md-2">
                            <select name="user_name_select" <?=$user_name_select;?> id="user_name_select" class="select2 form-control" required onchange="get_dept_category(this.value)">
                                <?php echo $staff_options; ?>
                            </select>
                        </div>
      <!--                  <div class="col-md-2">-->
      <!--                     <label class="col-md-12 col-form-label" for="user_type_value" id="user_type_value">User Type :-->
						<!--   <span id="user_type"></span></label>-->
      <!--                  </div>-->
      <!--                  <div class="col-md-2">-->
      <!--                     <label class="col-md-12 col-form-label" for="ph_no" id="ph_no">Phone Number :-->
						<!--   <span id="mobile_no"></span></label>-->
      <!--                  </div>-->
						<!--<div class="form-group col-md-4">-->
      <!--                   <label class="col-md-12 col-form-label" for="designation_name"   id="designation_name">Designation:-->
      <!--                    <span id="designation"></span></label> -->
      <!--               </div>-->
                     </div>
                  </div>
               </div>
            </form>
            <form class="periodic_sub_form was-validated" id="periodic_creation_form_sub" name="periodic_creation_form_sub">
               <div class="row">
                  <div class="col-12">
                     <table id="sublist" class="table dt-responsive nowrap w-100">
                        <thead>
                           <tr class="text-center">
                              <th ><h2 class="m-0">Sublist Data </h2></th>
                           </tr>
                        </thead>
                     </table>
                  </div>
               </div>
                    <div class="form-group row" >
					<div class="form-group col-md-2" id="dept_name">
                         <label for="site_name" class="col-form-label"  for="department_name">Department Name</label>
                           <select name="department_name" id="department_name" class="select2 form-control" onchange="get_category()">
                                <?php echo  $department_name_options; ?>
                            </select>
                     </div>
                        <!--<label for="department_name" class="col-form-label">Department</label>-->
                        <!--<select class="select2 form-control" tabindex="6" multiple id="department" onchange="get_category()" required=""> <?php echo $department_type_options; ?></select>-->
                            <!--<input type="hidden" id="department_name" name="department_name" class="form-control" value="<?php echo $department_name; ?>" >-->
                        <!--</div>-->
					  <div class="form-group col-md-2">
                         <label for="site_name" class="col-form-label"   for="complaint_category">Category Name</label>
                           <select name="complaint_category" id="complaint_category" class="select2 form-control">
                                <?php echo $category_name_option; ?>
                            </select>
                     </div>
                    <!--<div class="form-group col-md-2">-->
                    <!--     <label for="site_name" class="col-form-label">Site</label>-->
                    <!--      <select class="select2 form-control" tabindex="6" multiple id="site" onchange="get_site_ids()"><?php echo $site_options; ?></select>-->
                    <!--      <input type="hidden" id="site_name" name="site_name" class="form-control" value="<?php echo $site_name; ?>" >-->
                    <!-- </div>-->
                     <div class="form-group col-md-2">
                         <label for="site_name" class="col-form-label">Site</label>
                          <select class="select2 form-control" tabindex="6" multiple id="site" onchange="get_site_ids()"><?php echo $site_options; ?></select>
                          <input type="hidden" id="site_name" name="site_name" class="form-control" value="<?php echo $site_name; ?>" >
                     </div>
                     <div class="form-group col-md-1">
                         <label for="level_no" class="col-form-label">Level</label>
                          <select class="select2 form-control" tabindex="8" id="level_no"><?=$level_options;?></select>
                     </div>
                     <!-- <div class="form-group col-md-2">
                         <label for="complaint_category" class="col-form-label"  >Category</label>
                         <select class="select2 form-control" tabindex="8" id="complaint_category"><?php echo $category_name_option; ?></select>
                     </div> -->
                     <div class="form-group col-md-2">
                         <label for="starting_count" class="col-form-label" >Starting Days</label>
                         <input type="number"  min='0' class="form-control" tabindex="5"  onkeyup="get_previous_days_count()"  value="" id="starting_count">
                         <span id="vali_days" style = 'font-size: 12px;font-weight: bold;color : red' class="start_count_class <?=$start_count_class;?>">The Starting Days should be greater than Previous Ending Days</span>
                     </div>
                     <div class="form-group col-md-2">
                         <label for="ending_count" class="col-form-label">Ending Days</label>
                         <input type="number"  min='1' class="form-control" tabindex="5"   value="" id="ending_count">
                     </div>
                     <div class="form-group col-md-1 mt-4 mb-2">
                         <label for="btn_add" class="col-form-label">&nbsp;</label>
                          <button type="button" id="btn_add" class="btn btn-success btn-block purchase_sub_add_update_btn" onclick = "periodic_add_update()">Add</button>
                     </div>
                 </div>
               <input type="hidden" id="periodic_table_count" name="periodic_table_count" value=>
               <!-- </form> 
                  <form class="was-validated sublist-form" id="sublist-form"> -->
               <div class="row">
                  <div class="col-12">
                     <table id="periodic_sub_datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                           <tr>
                              <th>#</th>
                              <th>Department</th>
                              <th>Category</th>
                              <th>Site</th>
                              <th>Level</th>
                              <th>Starting Days</th>
                              <th>Ending Days</th>
                              <th>Action</th>
                           </tr>
                        </thead>
                        <tbody>
                        </tbody>
                       
                     </table>
                  </div>
               </div>
            </form>
            <div class="form-group row ">
                <div class="col-md-12" align="right">
                  <!-- Cancel,save and update Buttons -->
                    <?php echo btn_cancel($btn_cancel); ?>
                    <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                </div>
            </div>
         </div>
      </div>
      <!-- end card-body -->
   </div>
   <!-- end card -->
</div>
<!-- end col -->
</div>  
<script>
   function print(file_name)
    {
       onmouseover= window.open('uploads/periodic_creation/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }    
</script>