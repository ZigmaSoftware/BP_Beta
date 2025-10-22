<?php 
include 'function.php'; 

$department_options = department();
$department_options = select_option($department_options, "Select Department")
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- Top Action Row -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filter_department" class="form-label">Department</label>
                        <select class="form-control select2" id="filter_department" name="filter_department">
                            <?= $department_options ?>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button type="button" class="btn btn-primary btn-rounded" onclick="task_category_filter();">
                            Go
                        </button>
                    </div>

                    <div class="col-md-7 d-flex align-items-end justify-content-end">
                        <?php echo btn_add($btn_add); ?>
                    </div>
                </div>

                <!-- DataTable -->
                <table id="category_datatable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Department</th>
                            <th>Category Name</th>
                            <th>Description</th>
                            <!--<th>Created On</th>-->
                            <!--<th>Updated On</th>-->
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div> <!-- end card body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->
