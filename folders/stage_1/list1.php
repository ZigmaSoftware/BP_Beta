<?php

$zone_name_options         = zone_name(); 
$zone_name_options         = select_option($zone_name_options, "Select ", $zone_name);

$department_type_options         = department_type_wise();
$department_type_options         = select_option($department_type_options, "Select ");

$category_name_option            = category_creation();
$category_name_option            = select_option($category_name_option, "Select ");

$ward_name_options            = ward_name();
$ward_name_options            = select_option($ward_name_options, "Select ");

$status_option                   = [
   "1" => [
      "unique_id" => "0",
      "value"     => "Pending",
   ],
   "2" => [
      "unique_id" => "1",
      "value"     => "Progressing",
   ],
   // "3" => [
   //    "unique_id" => "2",
   //    "value"     => "Completed",
   // ],
   // "4" => [
   //    "unique_id" => "3",  
   //    "value"     => "Cancel",
   // ],
];
$status_opt = 0;
$status_option        = select_option($status_option, "Select", $status_opt);

$stage_options        = ["1" => [
                               "unique_id" => "1",
                               "value"     => "Level 1",
                                   ],
                               "2" => [
                               "unique_id" => "2",
                               "value"     => "Level 2",
                                   ],
                              "3" => [
                               "unique_id" => "3",
                               "value"     => "Level 3",  
                                   ],
                               "4" => [
                                 "unique_id" => "4",
                                 "value"     => "Level 4",
                                        ],
                                 "5" => [
                                 "unique_id" => "5",
                                 "value"     => "Level 5",
                                              ],
                                 "6" => [
                                 "unique_id" => "6",
                                 "value"     => "Level 6",
                                                    ],
                                  "7" => [
                                 "unique_id" => "7",
                                 "value"     => "Level 7",
                                           ],
                           
                               ];
   $stage_options        = select_option($stage_options,"Select",$stage_opt);
?>  
<!-- start page title -->
<div class="row">
   <div class="col-12 mb-2">
      <div class="page-title-box"> 
         <h4 class="page-title">Level 1 List</h4>
         <div>
            <ol class="breadcrumb m-0">
               <li class="breadcrumb-item"><a href="javascript: void(0);">Complaints</a></li>
               <li class="breadcrumb-item active">Level 1</li>
            </ol>
         </div>
      </div>
   </div>
</div>
<div class="row list-pad">
   <div class="row">
      <div class="col-12">
         <div class="row">
            <div class="col-md-5">
               <p>

                  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample" aria-expanded="false" aria-controls="collapseWidthExample">
                     Filter With Date Wise
                  </button>
               </p>
            </div>
            <!-- <div class="col-7 new-add-btn">
               <?php echo btn_add($btn_add); ?>
            </div> -->
            <div>
            </div>
            <div class="collapse collapse-horizontal" id="collapseWidthExample">
               <div class="card card-body mb-0" style="width: 100%;">
                  <div class="row">
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Level</label>
                           <!-- <select name="department_type" id="department_type" onChange="category_entry_filter(department_type.value)" class="select2 form-control" required><?php echo $department_type_options; ?> </select> -->
                           <select class="select2 form-control"  id="stage" name="stage"><?=$stage_options;?></select>
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Department</label>
                           <select name="department_type" id="department_type" onChange="category_entry_filter(department_type.value)" class="select2 form-control" required><?php echo $department_type_options; ?> </select>
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Category</label>
                           <select name="complaint_name" id="complaint_name" class="select2 form-control" required=""> <?php echo $category_name_option; ?> </select>
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Status</label>
                           <select name="status_name" id="status_name" class="select2 form-control" required=""> <?php echo $status_option; ?> </select>
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Zone</label>
                           <select name="zone_name" id="zone_name" class="select2 form-control" required="" onchange="get_ward_name();"> <?php echo $zone_name_options; ?> </select>
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Ward</label>
                           <select name="ward_name" id="ward_name" class="select2 form-control" required=""> <?php echo $ward_name_options; ?> </select>
                        </div>
                     </div> 
                     <div class="col-2 align-self-center">
                        <button type="button" class="btn btn-primary rounded-pill waves-effect waves-light" onclick="complaint_category_filter()">Go</button>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

</div>




<div class="row list-pad">
   <!----content start---------->
   <div class="row">
      <div class="col-12">

         <div class="card">
            <div class="card-body">
            <?php if($_SESSION['user_type_unique_id'] == "642ba85ccdd4d47048"){ ?>
               <div class="row">
                  <div class="col-md-12 mb-2 mt-1" style="text-align:right;position: absolute;padding-right: 40px;"><button class="btn btn-primary" onclick="new_complaint()">New Complaint</button></div>


               </div>
         <?php    } ?>
               <table id="stage_1_datatable" class="table table-striped dt-responsive nowrap w-100">
                  <thead>
                     <tr>
                        <th>S.No</th>
                        <th>Reg No/Reg Date </th>
                        <th>Zone / Ward</th> 
                        <th>Department / Category</th>
                        <th>Description</th>
                        <th>Source</th>
                        <th>Name / Mobile No</th>
                        <th>Ageing Days / Assign By</th>
                        <th>Status / Level</th>
                        <th>At Level</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody>
                  </tbody>
               </table>
            </div>
         </div>
      </div>
   </div>
   <!----content start---------->