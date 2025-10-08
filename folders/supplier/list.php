<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row add_btn">
                            <div class="col-md-12">
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 " align="right">
                    <a href="#" id="excel_export"><i class="fas fa-file-excel" style="font-size:30px; color: green;"></i><span style="color: green;">&nbsp;Excel Export</span></a>
                </div>
                <table id="supplier_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light"> 
                        <tr>
                            <th>#</th>
                            <th>Supplier Name</th>
                            <th>Contact No</th>
                            <th>Email Id</th>
                            <th>Address</th>
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