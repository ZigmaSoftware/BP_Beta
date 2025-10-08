<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>
<script>
   var sublist = "";
</script>
<?php
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";
$screen_unique_id   = unique_id("expscr"); // It is Current Screen Unique id

$entry_date         = $today;
$branch_name        = $_SESSION['staff_id'];
$exp_no             = "";
$table              = "";
$description        = "";
$table_sub          = "";
$staff_branch_type  = 0;
$button_disable     = "";
$text_readonly      = "";
$branch_check       = " checked ";
$staff_check        = "";
$designation_unique_id = "";
$sub_counter        = 1;

$sublist_data       = "";

if (isset($_GET["unique_id"])) {
   if (!empty($_GET["unique_id"])) {

      $unique_id  = $_GET["unique_id"];
      $where      = [
         "unique_id" => $unique_id
      ];

      $table            = "expense_creation_main";
      $table_sub        = "expense_creation_sub";

      $columns    = [
         "exp_no",
         "branch_unique_id",
         "description",
         "entry_date",
         "staff_branch_type",
         "designation_unique_id",
         "grade_type",
         "screen_unique_id"

      ];

      $table_details   = [
         $table,
         $columns
      ];

      $select_result  = $pdo->select($table_details, $where);

      if ($select_result->status) {


         $select_result              = $select_result->data;
         $entry_date                 = $select_result[0]["entry_date"];
         $branch_name                = $select_result[0]["branch_unique_id"];
         $exp_no                     = $select_result[0]["exp_no"];
         $description                = $select_result[0]["description"];
         $screen_unique_id           = $select_result[0]["screen_unique_id"];
         $staff_branch_type          = $select_result[0]["staff_branch_type"];
         $designation_unique_id      = $select_result[0]["designation_unique_id"];
         $grade_type                 = $select_result[0]["grade_type"];

         $designation_details   = work_designation($designation_unique_id);
         $designation_name      = $designation_details[0]["designation_type"];
         



         $btn_text           = "Update";
         $btn_action         = "update";

         // $sublist_data       = "";
      } else {
         $btn_text           = "Error";
         $btn_action         = "error";
         $is_btn_disable     = "disabled='disabled'";

         print_r($select_result);
      }
   }
}

//if ($_SESSION['sess_user_type'] != "5f97fc3257f2525529") {

if ($staff_branch_type == 0) {

   $sess_user_type      = $_SESSION['sess_user_type'];
   $executive_id        = $_SESSION["staff_id"];


   if ($sess_user_type != $admin_user_type) {
      $executive_options = [];
      // $executive_options   = $data;
      $executive_options   = staff_name($_SESSION["staff_id"])[0];
      //print_r($executive_options);
      $executive_options  = [[
         "unique_id" => $executive_options['unique_id'],
         "name"      => $executive_options['staff_name']
      ]];
      $executive_options   = select_option($executive_options, "Select Staff Name", $_SESSION["staff_id"]);
   } else {
      $executive_options  = staff_name();
      $executive_options  = select_option($executive_options, "Select Executive Name", $branch_name);
   }

   $label_name         = "Staff Name";
   $staff_check        = " checked ";
   $button_disable     = " disabled ";
   $text_readonly      = " disabled ";
} else {
   $executive_options = branch($branch_name);
   $executive_options = select_option($executive_options, "Select Branch Name", $branch_name);

   $label_name                 = "Branch Name";
   $branch_check               = " checked ";
   $designation_unique_id      = "";
   $designation_name           = "";
   $designation_class          = "d-none";
   $button_disable             = " disabled ";
   $text_readonly              = " disabled ";
}

$expense_type_options = expense_type();
$expense_type_options = select_option($expense_type_options, "Select Expense Type");


$expense_type_hotel_options = expense_type();
$expense_type_hotel_options = select_option($expense_type_hotel_options, "Select Expense Type");

$expense_type_travel_options = expense_type();
$expense_type_travel_options = select_option($expense_type_travel_options, "Select Expense Type");

$expense_type_petrol_options = expense_type();
$expense_type_petrol_options = select_option($expense_type_petrol_options, "Select Expense Type");

$vehicle_type_options      = vehicle_type();
$vehicle_type_options      = select_option($vehicle_type_options, "Select the vehicle", $vehicle_type);

$fuel_type_options         = fuel_type();
$fuel_type_options         = select_option($fuel_type_options, "Select the Fuel", $fuel_type);

$travel_type_options       = travel_type();
$travel_type_options       = select_option($travel_type_options, "Select vehicle Type", $travel_type);

$city_hotel_options         = city();
$city_hotel_options         = select_option($city_hotel_options,"Select the City",$city_hotel); 




?>
<input type="hidden" name="unique_id" id="unique_id" class="form-control" value="<?php echo $unique_id; ?>">
<div class="row">
   <div class="col-12">
      <div class="card">
         <div class="card-body">
            <form class="was-validated" id="expense_creation_form_main" name="expense_creation_form_main">
               <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?= $screen_unique_id; ?>">
               <div class="row">
                  <div class="col-12">
                     <div class="form-group row ">
                        <input type="hidden" name="exp_no" id="exp_no" class="form-control" value='<?php echo  $exp_no; ?>'>
                        <div class="col-md-2">
                           <h4 class="text-info"><?php echo $exp_no; ?></h4>
                        </div>
                        <label class="col-md-1 col-form-label" for="branch_staff_name"> <span id="staff_branch"><?= $label_name; ?></span></label>
                        <div class="col-md-3">
                           <select name="branch_staff_name" id="branch_staff_name" class="select2 form-control" onChange="get_designation()">
                              <?php echo  $executive_options; ?>
                           </select>
                        </div>
                        <label class="col-md-2 col-form-label" for="entry_date"> Entry Date </label>
                        <div class="col-md-3">
                           <input type="date" id="entry_date" name="entry_date" class="form-control" onChange="expense_call_function()" max="<?php echo $today; ?>" value="<?php echo $entry_date; ?>" required>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <div class="col-md-3 col-sm-12 col-12">
                           <div class="form-group row d-none">
                              <div class="custom-control branch_staff_name_type custom-radio">
                                 <input type="radio" id="branch" name="branch_staff_name_type" <?= $branch_check; ?> class="custom-control-input" onclick="get_staff_name(this.value);" value="1" checked required>
                                 <label class="custom-control-label text-primary" for="branch">Branch</label>&nbsp;&nbsp;
                              </div>
                              <div class="custom-control branch_staff_name_type custom-radio">
                                 <input type="radio" id="staff" name="branch_staff_name_type" class="custom-control-input" onclick="get_staff_name(this.value);" value="0" <?= $staff_check; ?> required>
                                 <label class="custom-control-label text-primary" for="staff">Staff</label>
                              </div>
                           </div>
                        </div>
                        <label class="col-md-1 col-form-label <?php echo $designation_class; ?>" for="designation"> Designation </label>
                        <div class="col-md-1">
                           <label class="col-md-12 col-form-label " for="designation_name"> <span id="designation_name"><?= $designation_name; ?></span> </label>
                           <input type="hidden" id="designation_unique_id" name="designation_unique_id" class="form-control" value="<?php echo $designation_unique_id; ?>">
                        </div>
                        <label class="col-md-1 col-form-label" for="grade_name">Grade Type</label>
                        <div class="col-md-1">
                           <label class="col-md-12 col-form-label " for="grade_name"> <span id="grade_name"><?= $grade_name; ?></span> </label>
                           <input type="hidden" id="grade_type" name="grade_type" class="form-control" value="<?php echo $grade_type; ?>">
                        </div>
                     </div>
                  </div>
               </div>
            </form>
            <ul class="nav nav-tabs" id="expenseTabs" role="tablist">
               <li class="nav-item">
                  <a class="nav-link active" id="foodDailyExpense-tab" data-toggle="tab" href="#foodDailyExpense" role="tab" aria-controls="foodDailyExpense" aria-selected="true">Food / Daily Expense</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" id="hotelAccommodation-tab" data-toggle="tab" href="#hotelAccommodation" role="tab" aria-controls="hotelAccommodation" aria-selected="false">Hotel / Accommodation Expense</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" id="travel-tab" data-toggle="tab" href="#travel" role="tab" aria-controls="travel" aria-selected="false">Travel Expense</a>
               </li>
               <li class="nav-item">
                  <a class="nav-link" id="petrol-tab" data-toggle="tab" href="#petrol" role="tab" aria-controls="petrol" aria-selected="false">Petrol Expense</a>
               </li>
            </ul>
            <div class="tab-content mt-3" id="expenseTabsContent">
               <div class="tab-pane fade show active" id="foodDailyExpense" role="tabpanel" aria-labelledby="foodDailyExpense-tab">
                  <form class="exp_food_daily_expense_sub_form was-validated" id="exp_food_daily_expense_sub" name="exp_food_daily_expense_sub">
                     <div class="form-group row ">
                        
                        <label class="col-md-2 col-form-label" for="city_hotel"> City </label>
                        <div class="col-md-4">
                        <select name="city_hotel" id="city_hotel" class="select2 form-control" onchange="get_city_type(this.value)" required><?= $city_hotel_options; ?> </select>
                        <input type='hidden' class='form-control city_type' name='city_type' id='city_type'>
                        </div>
                        <label class="col-md-2 col-form-label" for="expense_type"> Expense Type</label>
                        <div class="col-md-4">
                           <select name="expense_type" id="expense_type" class="select2 form-control" onChange='get_max_expense_limit(expense_type.value,city_type.value,grade_type.value)' required><?= $expense_type_options; ?> </select>
                        </div>
                     </div>
                     <div class="form-group row ">
                        
                        <label class="col-md-2 col-form-label" for="amount"> Amount</label>
                        <div class="col-md-4">
                           <input type="text" onkeypress="number_only(event)" name="amount" onchange='check_max_limit_value(limit_value.value,amount.value)' id="amount" class="form-control" value="" required>
                           <label style='color :red; font-weight : bold;'><span id='max_limit'></span></label>
                           <input type='hidden' class='form-control limit_value' name='limit_value' id='limit_value'>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="exclusive_support"> Description</label>
                        <div class="col-md-4">
                           <textarea name="sub_description" id="sub_description" class="form-control" required></textarea>
                        </div>
                        <label class="col-md-2 col-form-label" for="oem_map_justification"> Document Upload </label>
                        <div class="col-md-4">
                           <input type="file" data-default-file="" id="test_file" name="test_file" class="form-control dropify ">
                        </div>
                     </div>
                     <div class="form-group row mb-2 ">
                        <div class="col text-center">
                           <button type="button" class="btn btn-success waves-effect  waves-light exp_food_daily_expense_sub_add_update_btn" onclick="exp_food_daily_expense_sub_add_update()">ADD</button>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="tab-pane fade" id="hotelAccommodation" role="tabpanel" aria-labelledby="hotelAccommodation-tab">
                  <form class="exp_hotel_expense_sub_form was-validated" id="exp_hotel_expense_sub" name="exp_hotel_expense_sub">
                     <div class="form-group row ">
                        
                     <label class="col-md-2 col-form-label" for="city_hotel"> City </label>
                        <div class="col-md-4">
                        <select name="city_hotel_type" id="city_hotel_type" class="select2 form-control" onchange="get_city_type_hotel(this.value)" required><?= $city_hotel_options; ?> </select>
                        <input type='hidden' class='form-control city' name='city' id='city'>
                        </div>
                        <label class="col-md-2 col-form-label" for="expense_type_hotel"> Expense Type</label>
                        <div class="col-md-4">
                           <select name="expense_type_hotel" id="expense_type_hotel" class="select2 form-control" onChange='get_max_expense_limit_hotel(expense_type_hotel.value,city.value,grade_type.value)' required><?= $expense_type_hotel_options; ?> </select>
                        </div>
                        
                     </div>
                     <div class="form-group row ">
                        
                     <label class="col-md-2 col-form-label" for="amount_hotel"> Amount</label>
                        <div class="col-md-4">
                           <input type="text" onkeypress="number_only(event)" name="amount_hotel" onchange='check_max_limit_value_hotel(limit_value.value,amount_hotel.value)' id="amount_hotel" class="form-control" value="" required>
                           <label style='color :red; font-weight : bold;'><span id='max_limit_hotel'></span></label>
                           <input type='hidden' class='form-control limit_value_hotel' name='limit_value_hotel' id='limit_value_hotel'>
                        </div>
                   </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="sub_description_hotel"> Description</label>
                        <div class="col-md-4">
                           <textarea name="sub_description_hotel" id="sub_description_hotel" class="form-control" required></textarea>
                        </div>
                        <label class="col-md-2 col-form-label" for="test_file_hotel"> Document Upload </label>
                        <div class="col-md-4">
                           <input type="file" data-default-file="" id="test_file_hotel" name="test_file_hotel" class="form-control dropify ">
                        </div>
                     </div>
                     <div class="form-group row mb-2 ">
                        <div class="col text-center">
                           <button type="button" class="btn btn-success waves-effect  waves-light exp_food_daily_expense_sub_add_update_btn" onclick="exp_hotel_expense_sub_add_update()">ADD</button>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="tab-pane fade" id="travel" role="tabpanel" aria-labelledby="travel-tab">
                  <form class="exp_travel_expense_sub_form was-validated" id="exp_travel_expense_sub" name="exp_travel_expense_sub">
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="expense_type_travel"> Expense Type</label>
                        <div class="col-md-4">
                           <select name="expense_type_travel" id="expense_type_travel" class="select2 form-control" onChange='get_max_expense_limit(expense_type.value,designation_unique_id.value)' required><?= $expense_type_travel_options; ?> </select>
                        </div>
                        <label class="col-md-2 col-form-label" for="amount_travel"> Amount</label>
                        <div class="col-md-4">
                           <input type="text" onkeypress="number_only(event)" name="amount_travel"  id="amount_travel" class="form-control" value="" required>
                           <!-- onKeyup='check_max_limit_value(limit_value.value,amount_travel.value)' -->
                           <!-- <label style='color :red; font-weight : bold;'><span id='max_limit'></span></label>
                           <input type='hidden' class='form-control limit_value' name='limit_value' id='limit_value'> -->
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="exclusive_support"> Description</label>
                        <div class="col-md-4">
                           <textarea name="sub_description_travel" id="sub_description_travel" class="form-control" required></textarea>
                        </div>
                        <label class="col-md-2 col-form-label" for="oem_map_justification"> Document Upload </label>
                        <div class="col-md-4">
                           <input type="file" data-default-file="" id="test_file_travel" name="test_file_travel" class="form-control dropify ">
                        </div>
                     </div>
                     <div class="form-group row mb-2 ">
                        <div class="col text-center">
                           <button type="button" class="btn btn-success waves-effect  waves-light exp_travel_expense_sub_add_update_btn" onclick="exp_travel_expense_sub_add_update()">ADD</button>
                        </div>
                     </div>
                  </form>
               </div>
               <div class="tab-pane fade" id="petrol" role="tabpanel" aria-labelledby="petrol-tab">
                  <form class="exp_petrol_expense_sub_form was-validated" id="exp_petrol_expense_sub" name="exp_petrol_expense_sub">
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="expense_type_petrol"> Expense Type</label>
                        <div class="col-md-4">
                           <select name="expense_type_petrol" id="expense_type_petrol" class="select2 form-control" onChange='get_max_expense_limit(expense_type_petrol.value,designation_unique_id.value)' required><?= $expense_type_petrol_options; ?> </select>
                        </div>

                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="travel_type">Travel Type</label>
                        <div class="col-md-4">
                           <select name="travel_type" id="travel_type" class="select2 form-control" onchange="get_vehicle_type(this.value);"><?= $travel_type_options; ?> </select>
                        </div>
                        <label class="col-md-2 col-form-label" for="vehicle_type">Vehicle</label>
                        <div class="col-md-4">
                           <select name="vehicle_type" id="vehicle_type" class="select2 form-control" onchange="get_fuel_type(this.value);"><?= $vehicle_type_options; ?>
                           </select>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="fuel_type">Fuel Type</label>
                        <div class="col-md-4">
                           <select name="fuel_type" id="fuel_type" class="select2 form-control" onchange="get_rate_value(vehicle_type.value,this.value);"><?= $fuel_type_options; ?></select>
                        </div>
                        <label class="col-md-2 col-form-label" for="rate">Rate Per Km</label>
                        <div class="col-md-4">
                           <input type='text' name='rate' id='rate' class='form-control' onkeyup="calculateTotal(rate.value);">
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="kg_meter">Kilo Meter</label>
                        <div class="col-md-4">
                           <input type='text' class='form-control' name='kg_meter' id='kg_meter' onkeyup="calculateTotal(kg_meter.value);" value="<?php echo $kg_meter; ?>">
                        </div>
                        <label class="col-md-2 col-form-label" for="amount_petrol"> Amount</label>
                        <div class="col-md-4">
                           <input type="text" onkeypress="number_only(event)" name="amount_petrol" onKeyup='check_max_limit_value(limit_value.value,amount_petrol.value)' id="amount_petrol" class="form-control" value="" required>
                           <label style='color :red; font-weight : bold;'><span id='max_limit_petrol'></span></label>
                           <input type='hidden' class='form-control limit_value_petrol' name='limit_value_petrol' id='limit_value_petrol'>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="sub_description_petrol"> Description</label>
                        <div class="col-md-4">
                           <textarea name="sub_description_petrol" id="sub_description_petrol" class="form-control" required></textarea>
                        </div>
                        <label class="col-md-2 col-form-label" for="test_file_petrol"> Document Upload </label>
                        <div class="col-md-4">
                           <input type="file" data-default-file="" id="test_file_petrol" name="test_file_petrol" class="form-control dropify ">
                        </div>
                     </div>
                     <div class="form-group row mb-2 ">
                        <div class="col text-center">
                           <button type="button" class="btn btn-success waves-effect  waves-light exp_food_daily_expense_sub_add_update_btn" onclick="exp_petrol_expense_sub_add_update()">ADD</button>
                        </div>
                     </div>
                  </form>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-12">
                  <table id="exp_food_daily_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Expense Type</th>
                           <th>Amount</th>
                           <th>Description</th>
                           <th>Document Upload</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                     </tbody>
                     <tfoot>
                        <th></th>
                        <th>Total</th>
                        <th id='total_amt'></th>
                        <th></th>
                        <th></th>
                        <th></th>
                     </tfoot>
                  </table>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-12">
                  <table id="exp_hotel_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Expense Type</th>
                           <th>Amount</th>
                           <th>Description</th>
                           <th>Document Upload</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                     </tbody>
                     <tfoot>
                        <th></th>
                        <th>Total</th>
                        <th id='total_amt_hotel'></th>
                        <th></th>
                        <th></th>
                        <th></th>
                     </tfoot>
                  </table>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-12">
                  <table id="exp_travel_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Expense Type</th>
                           <th>Amount</th>
                           <th>Description</th>
                           <th>Document Upload</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                     </tbody>
                     <tfoot>
                        <th></th>
                        <th>Total</th>
                        <th id='total_amt_travel'></th>
                        <th></th>
                        <th></th>
                        <th></th>
                     </tfoot>
                  </table>
               </div>
            </div>
            <br>
            <div class="row">
               <div class="col-12">
                  <table id="exp_petrol_expense_sub_datatable" class="table dt-responsive nowrap w-100">
                     <thead>
                        <tr>
                           <th>#</th>
                           <th>Expense Type</th>
                           <th>Amount</th>
                           <th>Description</th>
                           <th>Document Upload</th>
                           <th>Action</th>
                        </tr>
                     </thead>
                     <tbody>
                     </tbody>
                     <tfoot>
                        <th></th>
                        <th>Total</th>
                        <th id='total_amt_petrol'></th>
                        <th></th>
                        <th></th>
                        <th></th>
                     </tfoot>
                  </table>
               </div>
            </div>
            <form class="was-validated" id="expense_creation_form_bottom" name="expense_creation_form_bottom">
               <div class="row">
                  <div class="col-12">
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="description"> Description </label>
                     </div>
                     <div class="form-group row ">
                        <div class="col-md-4">
                           <textarea name="description" id="description" class=" form-control" rows="5" required> <?php echo  $description; ?> </textarea>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
            <div class="col-12">
               <div class="form-group row ">
                  <div class="col-md-12">
                     <!-- Cancel,save and update Buttons -->
                     <?php echo btn_cancel($btn_cancel); ?>
                     <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                  </div>
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
   function print(file_name) {
      onmouseover = window.open('uploads/expense_creation/' + file_name, 'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
   }

   $(document).ready(function() {
      $('#expenseTabs a').on('click', function(e) {
         e.preventDefault();
         $(this).tab('show');
      });

      // Switch to the respective tab based on the active one
      $('#expenseTabs a').on('shown.bs.tab', function(e) {
         var activeTab = $(e.target).attr('href'); // Get the href attribute of the active tab
         localStorage.setItem('activeTab', activeTab); // Store the active tab in localStorage
      });

      // Activate the last active tab on page load
      var lastActiveTab = localStorage.getItem('activeTab');
      if (lastActiveTab) {
         $('#expenseTabsContent').find('.nav-link[href="' + lastActiveTab + '"]').tab('show');
      }
   });
</script>