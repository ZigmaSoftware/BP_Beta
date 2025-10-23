<?php
$sess_user_type = $_SESSION['sess_user_type'];
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="row">                                    
                    <div class="col-12">
                        <!-- Add New Roster Button -->
                        <div class="form-group row add_btn">
                            <div class="col-md-12">
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ========================================================= -->
                <!-- ✅ Shift Roster DataTable -->
                <!-- ========================================================= -->
                <table id="shift_roster_datatable" 
                       class="table table-striped table-bordered dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Project Name</th>
                            <th>Month</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>                                            
                </table>

            </div> <!-- end card body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>
<!-- end row -->
