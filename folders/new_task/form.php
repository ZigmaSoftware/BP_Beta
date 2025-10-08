<style>
   .form-label {
      margin: 10px 0px !important;
   }
   ul.list-inline.wizard.mb-0 {
      margin-top: 33px;
   }
   input[type="file"] {
      width: 100%;
   }
   .imgg {
      width: 139px;
      height: 84px;
   }
   .info_head {
      background-color: #00a726;
      color: #fff;
      padding: 6px 15px;
      border-radius: 4px;
      font-size: 14px;
      margin-top: 0px;
   }
   .mb-2 {
      margin-bottom: 20px !important;
   }
   .card-body {
      padding: 0.6rem 0.6rem;
   }
   .col-form-label {
      padding-bottom: 0.1rem;
   }
   .mrg-btm {
      margin-bottom: 4px !important;
   }
   span.select2.select2-container {
      width: 100% !important;
   }
</style>
<!-- This file Only PHP Functions -->
<?php
//print_r($_SESSION);
// Form variables
$screen_unique_id = unique_id('CMP');
$btn_text   = "Save";
$btn_action = "create";
$form_type  = "Create";
$unique_id  = "";
$user_type  = "";
$assign_by  = "";
$is_active  = 1;
$user_id  = $_SESSION['user_id'];
$user_name_ses = $_SESSION['user_name'];
$mobile_no_ses = $_SESSION['mobile_no'];
$session_site = $_SESSION['sess_site_name'];
if (isset($_GET["unique_id"])) {
   if (!empty($_GET["unique_id"])) {
      $unique_id  = $_GET["unique_id"];
      $where = [
         "unique_id" => $unique_id
      ];
      $table = "complaint_creation";
      $columns = [
         "state_name",
         "site_name",
         "plant_name",
         "shift_name",
         "problem_type",
         "priority_type",
         "department_name",
         "main_category",
         "complaint_category",
         "source_name",
         "complaint_description",
         "screen_unique_id",
         "complaint_no",
         "entry_date",
         "assign_by",
         "user_id"
      ];
      $table_details = [
         $table,
         $columns
      ];
      $result_values = $pdo->select($table_details, $where);
    //   print_r($unique_id);
    //   print_r($result_values);
    //   die();
      if ($result_values->status) {
         $result_values = $result_values->data[0];
         $state_name             = $result_values["state_name"];
         $site_name              = $result_values["site_name"];
         // $site_name              = $result_values["site_id"];
         // print_r($site_name);
         $plant_name             = $result_values["plant_name"];
         $shift_name             = $result_values["shift_name"];
         $problem_name           = $result_values["problem_type"];
         $priority_name          = $result_values["priority_type"];
         $department_name        = $result_values["department_name"];
         $main_category          = $result_values["main_category"];
         $complaint_category     = $result_values["complaint_category"];
         $source_name            = $result_values["source_name"];
         $complaint_description  = $result_values["complaint_description"];
         $screen_unique_id       = $result_values["screen_unique_id"];
         $complaint_no           = $result_values["complaint_no"];
         $entry_date             = $result_values["entry_date"];
         $assign_by              = $result_values["assign_by"];
         $user_id                = $result_values["user_id"];

        $project_name_options  = get_project_name();
        $project_name_options  = select_option($project_name_options, "Select the Project Name",$plant_name);
        

         $form_type  = "Update";
         $btn_text   = "Update";
         $btn_action = "update";
      } else {
         $btn_text       = "Error";
         $btn_action     = "error";
         $is_btn_disable = "disabled='disabled'";
      }
   }
}
if ($assign_by) {
   $assign_bys = $result_values["assign_by"];
} else {
   $assign_bys = $_SESSION['user_id'];
}
// $department_type_options = department_type();
// $department_type_options = select_option_create($department_type_options, '', $department_name);
// $main_name_option = main_category('',$department_name);
// $main_name_option = select_option_create($main_name_option,'',  $main_category);
// $source_type_options = source_type();
// $source_type_options = select_option_create($source_type_options, "Select ", $source_name);
// $category_name_option = category_name('', $department_name,'', $main_category);
// $category_name_option = select_option_create($category_name_option,  $complaint_category);
// $state_type_options = state_name();
// $state_type_options = select_option_create($state_type_options, '', $state_name);
// $shift_type_options = shift_name();
// $shift_type_options = select_option_create($shift_type_options,'',  $shift_name);
$priority_type_options = priority_type();
$priority_type_options = select_option($priority_type_options, '', $priority_name);
$problem_type_options = problem_type();
$problem_type_options = select_option($problem_type_options,'', $problem_name);
// // $site_type_options = site_name('',$state_name);
// $site_type_options = site_name();
// $site_type_options = select_option_create($site_type_options,'',$site_name);
// // $plant_type_options = plant_name('',$state_name,$site_name);
// $plant_type_options = plant_name();
// // $plant_type_options = select_option($plant_type_options,"Select", $plant_name);
// $plant_type_options = select_option_create($plant_type_options,'', $plant_name);
$document_options        = [
    "1" => [
        "unique_id" => "1",
        "value"     => "Image",
    ],
    "2" => [
        "unique_id" => "2",
        "value"     => "Document",
    ],
    "3" => [
        "unique_id" => "3",
        "value"     => "Audio",
    ],
];
$document_options        = select_option($document_options, "Select", $stage);
$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$site_name);


$department_name_options        = department();
$department_name_options        = select_option($department_name_options,"Select the Department",$department_name);

        $main_category_options  = main_category_creation('',$department_name);
        $main_category_options  = select_option($main_category_options, "Select the Main Category",$main_category);
        
$complaint_category_option = category_creations("", $department_type,$main_category);
$complaint_category_option = select_option($complaint_category_option,"" ,$complaint_category);
?>
<form class="was-validated" autocomplete="off">
   <div class="row">
      <input type='hidden' id='screen_unique_id' name="screen_unique_id" value='<?php echo $screen_unique_id; ?>'>
      <input type='hidden' name='user_id' id='user_id' value='<?= $user_id; ?>'>
      <input type='hidden' name='complaint_no' id='complaint_no'
         value='<?= $complaint_no; ?>'>
      <div class="col-md-4">
         <div class="card">
            <div class="card-body">
               <h3 class="info_head"><i class="mdi mdi-map-marker"></i> Location Info</h3>
               <div class="row">
                  <div class="row col-12">
                     <label for="site" class="col-12 col-xl-12 col-form-label">Company Name </label>
                     <div class="col-12 col-xl-12 mrg-btm">
                    <select name="site_name" id="site_name"  class="form-control select2"  onchange="get_project_name(this.value);" required>
                        <?= $company_name_options ?>
                    </select>
                     </div>
                     <label for="plant_name" class="col-12 col-xl-12 col-form-label">Project Name</label>
                     <div class="col-12 col-xl-12 mrg-btm">
                    <select name="plant_name" id="plant_name" class="form-control select2" required>
                        <?= $project_name_options ?>
                    </select>
                     </div>
                     <label for="department_name" class="col-12 col-xl-12 col-form-label">Department</label>
                     <div class="col-12 col-xl-12 mrg-btm">
                     <select name="department_name" id="department_name"  class="form-control select2"  onchange="get_main_category(this.value);" required>
                        <?= $department_name_options ?>
                    </select>
                        <input type="hidden" name="assign_by" id="assign_by" class="form-control" value="<?= $assign_by; ?>">
                     </div>
                     <label for="main_category" class="col-12 col-xl-12 col-form-label ">Main Category</label>
                     <div class="col-12 col-xl-12 mb-4">
                        <select name="main_category" id="main_category"  class="select2 form-control" onchange="trigger_complaint_category();" required> <?php echo $main_category_options; ?></select>
                     </div>
                     <br />
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="card">
            <div class="card-body">
               <h3 class="info_head"><i class="mdi  mdi-pencil-box-outline"></i>Task Info</h3>
               <div class="row col-12">
                  <label for="complaint_category" class="col-12 col-xl-12 col-form-label">Task Category </label>
                  <div class="col-12 col-xl-12 mrg-btm">
                     <select name="complaint_category" id="complaint_category" class="select2 form-control" required=""> <?php echo $complaint_category_option; ?>
                     </select>
                  </div>
                  <label for="problem_type" class="col-12 col-xl-12 col-form-label">Problem Type </label>
                  <div class="col-12 col-xl-12 mrg-btm">
                     <select name="problem_type" id="problem_type" class="select2 form-control" required=""> <?php echo $problem_type_options; ?>
                     </select>
                  </div>
                  <label for="priority" class="col-12 col-xl-12 col-form-label">Impact Type</label>
                  <div class="col-12 col-xl-12 mrg-btm">
                     <select name="priority" id="priority" class="select2 form-control" required="">
                        <?php echo $priority_type_options; ?>
                     </select>
                  </div>
                  <label for="source_name" class="col-12 col-xl-12 col-form-label" style="display:none;">User Name</label>
                  <div class="col-12 col-xl-12 mrg-btm" style="display:none;">
                     <input type="hidden" name="source_name" id="source_name" class="form-control" value="<?= $_SESSION['staff_name']; ?>">
                     <input type="hidden" name="assign_by" id="assign_by" class="form-control" value="<?= $assign_bys; ?>">
                     <select name="source_name" id="source_name" class="select2 form-control"> <?php echo $source_type_options; ?></select>
                  </div>
                  <label for="complaint_description" class="col-12 col-xl-12 col-form-label">Task Description</label>
                  <div class="col-12 col-xl-12 mrg-btm">
                     <textarea class="form-control" rows="3" id="complaint_description" required name="complaint_description"><?= $complaint_description; ?></textarea>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <div class="col-md-4">
         <div class="card">
            <div class="card-body" style="height:350px;">
               <h3 class="info_head"> <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>Attachment</h3>
               <div class="row">
                  <div class="form-group col-md-4">
                     <label for="category_name" class="col-form-label">Document
                        Type</label>
                     <select name="document_name" id="document_name" class="select2 form-control"> <?php echo $document_options; ?></select>
                  </div>
                  <div class="form-group col-md-5">
                     <label for="category_name" class="col-form-label">Upload</label>
                     <input type="file" data-plugins="dropify" data-height="300" name="test_file" class="upload_image" id="test_file" onChange='showPreview(event);' />
                  </div>
                  <!--   <img src="uploads/noimage.png" id="output_image" name="output_image" class="imgg"> -->
                  <div class="form-group col-md-3 align-self-center">
                     <label for="btn_add" class="col-form-label">&nbsp;</label>
                     <button type="button" class="btn btn-success btn-block" id='sublist_save_btn' onclick="document_upload_add_update()">Add</button>
                  </div>
               </div>
               <br>
               <div class="row">
                  <div class="col-md-12">
                     <div class="table-responsive mt-2">
                        <!-- Table Begiins -->
                        <div id="document_upload_sub_datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
                           <div class="row">
                              <div class="col-sm-12 col-md-6"></div>
                              <div class="col-sm-12 col-md-6"></div>
                           </div>
                           <div class="row">
                              <div class="col-sm-12">
                                 <table id="document_upload_sub_datatable" class="table table-bordered table-md dataTable no-footer" style="width: 100%;">
                                    <thead>
                                       <tr>
                                          <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 43.8px;">#</th>
                                          <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 247.8px;">Document Type</th>
                                          <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 117.8px;">Upload</th>
                                          <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 111.8px;">Action</th>
                                       </tr>
                                    </thead>
                                    <tbody id='document_upload_sub_datatable'>
                                       <tr class="odd">
                                          <td valign="top" colspan="4" class="dataTables_empty">No data available in
                                             table</td>
                                       </tr>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                           <div class="row">
                              <div class="col-sm-12 col-md-5"></div>
                              <div class="col-sm-12 col-md-7"></div>
                           </div>
                        </div>
                        <!-- Table Ends -->
                     </div>
                  </div>
               </div><br>
            </div>
         </div>
      </div>
      <div class="col-md-12">
         <div class="card">
            <div class="card-body">
               <div class="form-group row ">
                  <div class="col-md-12" align="right">
                     <!-- Cancel,save and update Buttons -->
                     <?php echo btn_cancel($btn_cancel); ?>
                     <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</form>
<script>
   /*
    * Hacky fix for a bug in select2 with jQuery 3.6.0's new nested-focus "protection"
    * see: https://github.com/select2/select2/issues/5993
    * see: https://github.com/jquery/jquery/issues/4382
    *
    * TODO: Recheck with the select2 GH issue and remove once this is fixed on their side
    */
   $(document).on('select2:open', () => {
      document.querySelector('.select2-search__field').focus();
   });
</script>