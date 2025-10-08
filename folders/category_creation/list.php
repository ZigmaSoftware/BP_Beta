<?php
$department_options = department_wise();
$department_options = select_option($department_options, "Select ");

$category_name_options = category_name_wise();
$category_name_options = select_option($category_name_options, "Select ");

?>
  <!-- start page title -->
  <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Category Creation List</h4>
                <div>
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                        <li class="breadcrumb-item active">Category Creation</li>
                    </ol>
                </div>
            </div>
        </div>
        </div>
        <div class="row list-pad">
        <div class="row">
            <div class="col-12">
            <div class="row">
                  <div class="col-md-5" >
               <p>

                  <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample"
                     aria-expanded="false" aria-controls="collapseWidthExample">
                  Filter
                  </button>
               </p>
               </div>
               <div class="col-7 new-add-btn">
               <?php echo btn_add($btn_add); ?>
            </div>
               <div >
               </div>
                  <div class="collapse collapse-horizontal" id="collapseWidthExample">
                     <div class="card card-body mb-0" style="width: 100%;">
                        <div class="row">
                           <div class="col-4">
                              <div class="mb-3">
                              <label  class="form-label">Department</label>
                                  <select name="department" id="department" class="select2 form-control" required ><?php echo $department_options; ?> </select>
                              </div>
                           </div>

                           <div class="col-2 align-self-center">
                           <button type="button" class="btn btn-primary rounded-pill waves-effect waves-light" onclick="department_entry_filter()">Go</button>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

         </div>

         <!----content start---------->
         <div class="row">
            <div class="col-12">
               <div class="card">
                  <div class="card-body">

                     <table id="category_creation_datatable" class="table table-striped dt-responsive nowrap w-100">
                        <thead>
                           <tr>

                           <th>S.No</th>
                            <th>Department Name</th>
                            <th>Main Category Name</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <th>Unique Id</th>
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