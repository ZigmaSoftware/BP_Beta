<?php 
include 'function.php'; 

// Department dropdown
$department_options = department();
$department_options = select_option($department_options, "Select Department");

// Category dropdown (used for filtering)
$category_options = task_category();
$category_options = select_option($category_options, "Select Category");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- 🔍 Filter Row -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filter_department" class="form-label">Department</label>
                        <select class="form-control select2" id="filter_department" name="filter_department">
                            <?= $department_options ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="filter_category" class="form-label">Category</label>
                        <select class="form-control select2" id="filter_category" name="filter_category">
                            <?= $category_options ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-rounded" onclick="task_sub_category_filter();">
                            Go
                        </button>
                    </div>

                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <?php echo btn_add($btn_add); ?>
                    </div>
                </div>

                <!-- 📋 DataTable -->
                <table id="sub_category_datatable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Department</th>
                            <th>Category</th>
                            <th>Sub-Category</th>
                            <th>Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->
