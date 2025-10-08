<?php

// $site_name_options         = site_name();
// $site_name_options         = select_option($site_name_options, "Select ", $state_name);

// $department_type_options    = department_type_wise();
// $department_type_options    = select_option($department_type_options);

// $category_name_option       = category_name();
// $category_name_option       = select_option($category_name_option, "Select ");

// $priority_type_option       = priority_type();
// $priority_type_option       = select_option($priority_type_option, "Select ");

$status_option              = [
   "1" => [
      "unique_id" => "0",
      "value"     => "Pending",
   ],
   "2" => [
      "unique_id" => "1",
      "value"     => "Progressing",
   ],
//   "3" => [
//       "unique_id" => "2",
//       "value"     => "Completed",
//   ],
//   "4" => [
//       "unique_id" => "3",
//       "value"     => "Cancel",
//   ],
];
$status_opt = '';
$status_option        = select_option($status_option, "Select", $status_opt);

?>

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
            <div class="collapse collapse-horizontal" id="collapseWidthExample">
               <div class="card card-body mb-0" style="width: 100%;">
                  <div class="row">
                     <div class="col-2">
                        <div class="mb-3">
                           <label for="example-date" class="form-label">From Date</label>
                           <input class="form-control" id="from_date" name="from_date" type="date" value="">
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label for="example-date" class="form-label">To Date</label>
                           <input class="form-control" id="to_date" name="to_date" type="date" value="">
                        </div>
                     </div>
                    <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Site</label>
                           <select name="site_name" id="site_name" class="select2 form-control" required=""> <?php echo $site_name_options; ?> </select>
                        </div>
                     </div>
                    
                 <div class="col-2">
                 <div class="mb-3">
                 <label class="form-label" for="department_type">Department</label>
                 <select class="form-control select2" style="background-color: white;" name="department_type" id="department_type" onchange="category_entry_filter1(this.value)" required>
                 <?php echo $department_type_options; ?>
                    <option value="">Select Employee</option>
                </select>
                
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
                           <label class="form-label">Impact Type</label>
                           <select name="priority" id="priority" class="select2 form-control" required=""> <?php echo $priority_type_option; ?> </select>
                        </div>
                     </div>
                     <div class="col-2">
                        <div class="mb-3">
                           <label class="form-label">Status</label>
                           <select name="status_name" id="status_name" class="select2 form-control" required=""> <?php echo $status_option; ?> </select>
                        </div>
                     </div>
                     <div class="col-2 align-self-center">
                        <button type="button" class="btn btn-primary rounded-pill waves-effect waves-light" onclick="complaint_category_filter1()">Go</button>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>



<!-- start page title -->
<!--<div class="row">-->
<!--    <div class="col-12">-->
<!--        <div class="page-title-box">-->
<!--            <h4 class="page-title">Task Followu</h4>-->
<!--            <div>-->
<!--                <ol class="breadcrumb m-0">-->
<!--                    <li class="breadcrumb-item"><a href="javascript: void(0);">Complaints</a></li>-->
<!--                    <li class="breadcrumb-item active">Level 1</li>-->
<!--                </ol>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
<!--<div class="row list-pad">-->


    <!----content start---------->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form>
                        <div id="basicwizard">

                            <ul class="nav nav-pills bg-light nav-justified form-wizard-header mb-4">
                                <!--<li class="nav-item">-->
                                <!--    <a href="#basictab1" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2 active">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 1 - </span>(<span id="level_1_cnt">  </span>)-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li class="nav-item">-->
                                <!--    <a href="#basictab2" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 2 -</span>(<span id="level_2_cnt">  </span>) -->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li class="nav-item">-->
                                <!--    <a href="#basictab3" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 3 - </span>(<span id="level_3_cnt">  </span>)-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li class="nav-item">-->
                                <!--    <a href="#level4" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 4 - </span>(<span id="level_4_cnt">  </span>)-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li class="nav-item">-->
                                <!--    <a href="#level5" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 5 - </span>(<span id="level_5_cnt">  </span>)-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li class="nav-item">-->
                                <!--    <a href="#level6" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 6 - </span>(<span id="level_6_cnt">  </span>)-->
                                <!--    </a>-->
                                <!--</li>-->
                                <!--<li class="nav-item">-->
                                <!--    <a href="#level7" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">-->
                                <!--        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>-->
                                <!--        <span class="d-none d-sm-inline">Level 7 - </span>(<span id="level_7_cnt">  </span>)-->
                                <!--    </a>-->
                                <!--</li>-->
                                
                                <li class="nav-item">
                                    <a href="#all_level" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                        <span class="d-none d-sm-inline">All - </span>(<span id="all_cnt">  </span>)
                                    </a>
                                </li>
                                 <li class="nav-item">
                                    <a href="#own_call" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                        <span class="d-none d-sm-inline">Own Calls - </span>(<span id="call_cnt"> </span>)
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#tag_person" data-bs-toggle="tab" data-toggle="tab" class="nav-link rounded-0 pt-2 pb-2">
                                        <i class="mdi mdi-checkbox-marked-circle-outline me-1"></i>
                                        <span class="d-none d-sm-inline">Tag Person - </span>(<span id="tag_person_cnt">  </span>)
                                    </a>
                                </li>
                                
                            </ul>

                            <div class="tab-content b-0 mb-0 pt-0">
                                <div class="tab-pane active" id="basictab1">
                                    <table id="stage_1_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane" id="basictab2">
                                    <table id="stage_2_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane" id="basictab3">
                                    <table id="stage_3_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="level4">
                                    <table id="stage_4_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="level5">
                                    <table id="stage_5_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="level6">
                                    <table id="stage_6_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                <div class="tab-pane" id="level7">
                                    <table id="stage_7_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="tab-pane" id="all_level">
                                    <table id="all_level_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="tab-pane" id="own_call">
                                    <table id="own_call_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
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
                                
                                 <div class="tab-pane" id="tag_person">
                                    <table id="tag_person_datatable" class="table table-striped dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>S.No</th>
                                                <th>Reg No/Reg Date </th>
                                                <th>Site / Plant</th>
                                                <th>Department / Category</th>
                                                <th>Description</th>
                                                <th>Impact Type</th>
                                                <th>Ageing Days / Assign By</th>
                                                <th>Status / Level</th>
                                                <!--<th>At Level</th>-->
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>
                                </div>
                                
                            </div> <!-- tab-content -->
                        </div> <!-- end #basicwizard-->
                    </form>


                </div>
            </div>
        </div>
    </div>
    <!----content start---------->