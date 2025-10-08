<?php
//include 'ticket_mgt_common_function.php';

// $site_name_options         = site_name();
// $site_name_options         = select_option($site_name_options, "Select ", $state_name);

// $department_type_options         = department_type_wise();
// $department_type_options         = select_option($department_type_options, "Select ");

// $category_name_option            = category_name();
// $category_name_option            = select_option($category_name_option, "Select ");

// $priority_type_option            = priority_type();
// $priority_type_option            = select_option($priority_type_option, "Select ");


$status_option                   = [
   "1" => [
      "unique_id" => "0",
      "value"     => "Pending",
   ],
   "2" => [
      "unique_id" => "1",
      "value"     => "In Progress",
   ],
   "3" => [
      "unique_id" => "2",
      "value"     => "Completed",
   ],
   "4" => [
      "unique_id" => "3",
      "value"     => "Cancel",
   ],
];
$status_opt = '';
$status_option        = select_option($status_option, "Select", $status_opt);

?>
<style>
    .center {
  margin: 0 auto;
  width: 200px;
}

.search-wrap {
  position: relative;
}

.search-button {
  width: 50px;
  height: 50px;
  position: absolute;
  right: 0;
}

.search-form {
  height: 51px;
  width: 0;
  position: absolute;
  right: 50px;
  top: 0;
  -webkit-transition: width 400ms ease;
  transition: width 400ms ease;
}

.search-form .search-field {
  height: 100%;
  width: 100%;
}

.active .search-form {
  width: 250px;
  right: 50px;
}
</style>
<div class="row">
   <div class="col-12">
      <div class="card">
        <div class="row">                                    
            <div class="col-12">
                <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                <div class="form-group row ">
                    <div class="col-md-12">
                        <?php echo btn_add($btn_add); ?>
                    </div>
                </div>
            </div>
        </div>
         <div class="card-body">
            <form class="was-validated" autocomplete="off">
               <div class="row">
                  <div class="col-12">
                     <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                     <div class="form-group row ">
                        <label class="col-md-1 col-form-label" for="from_date"> From </label>
                        <div class="col-md-2">
                           <input type="date" name="from_date" id="from_date" class="form-control" max="<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                        </div>

                        <label class="col-md-1 col-form-label" for="to_date"> To </label>
                        <div class="col-md-2">
                           <input type="date" name="to_date" id="to_date" class="form-control" max="<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                        </div>

                        <label class="col-md-1 col-form-label" for="department_type"> Department </label>
                        <div class="col-md-2">
                           <select name="department_type" id="department_type" onChange="focusOnComplaintName(),category_entry_filter(department_type.value)" class="select2 search-form" required><?php echo $department_type_options; ?> </select>
                        </div>

                        <label class="col-md-1 col-form-label" for="complaint_name"> Category </label>
                        <div class="col-md-2">
                           <select name="complaint_name" id="complaint_name" class="select2 form-control" required=""> <?php echo $category_name_option; ?> </select>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-1 col-form-label" for="priority"> Impact Type </label>
                        <div class="col-md-2">
                           <select name="priority" id="priority" class="select2 form-control" required=""> <?php echo $priority_type_option; ?> </select>
                        </div>

                        <label class="col-md-1 col-form-label" for="status_name"> Status </label>
                        <div class="col-md-2">
                           <select name="status_name" id="status_name" class="form-control site_co select2" required=""> <?php echo $status_option; ?> </select>
                        </div>

                        <label class="col-md-1 col-form-label" for="site_name"> Site Name </label>
                        <div class="col-md-2">
                           <select name="site_name" id="site_name" class="select2 form-control" required=""> <?php echo $site_name_options; ?> </select>
                        </div>


                        <div class="col-2 align-self-center">
                           <button type="button" class="btn btn-primary rounded-pill waves-effect waves-light" onclick="new_task_filter1()">Go</button>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
            <table id="new_task_datatable" class="table dt-responsive nowrap w-100">
               <thead>
                  <tr>
                     <th>S.No</th>
                     <th>Reg No / Reg Date</th>
                     <th>Site / Plant</th>
                     <th>Department / Category</th>
                     <th>Description</th>
                     <th>Impact Type</th>
                     <th>Ageing Days / Assign By</th>
                     <th>Status / Level</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
               </tbody>
            </table>

         </div> <!-- end card body-->
      </div> <!-- end card -->
   </div><!-- end col-->
</div>
<!-- end row-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
// $("#department_type").click(function () {
//     $(this).find('.input-field-for-test').focus();
// });
    function focusOnComplaintName() {
    //  alert('Hi');
     
    //  $('#myinp').focus();
        document.getElementById('myinp').focus();
    }
</script>
<!----content start---------->