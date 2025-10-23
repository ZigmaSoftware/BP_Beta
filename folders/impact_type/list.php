<?php 
include 'function.php'; 
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- 🔍 Top Action Row -->
                <div class="row mb-3">
                    <div class="col-md-8"></div>
                    <div class="col-md-4 d-flex align-items-end justify-content-end">
                        <?php echo btn_add($btn_add); ?>
                    </div>
                </div>

                <!-- 📋 DataTable -->
                <table id="impact_datatable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>S.No</th>
                            <th>Impact Type Name</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->
