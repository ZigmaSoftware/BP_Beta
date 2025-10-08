<?php print_r($_SESSION['sess_user_type']); ?>
<div class="col-md-12">
    <?php echo btn_add($btn_add); ?>
</div> 
<div class="row">
    <div class="col-12">
        <div class="card">

            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >    
                <div class="row">                                    
                    <div class="col-12">
                        <?php
                            $startDate = date("Y-m-01");
                            $endDate = date("Y-m-t");
                        ?>
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row ">
                            <label class="col-md-1 col-form-label" for="bids_ho_from"> From  </label>
                            <div class="col-md-2">
                                <input type="date" name="bids_ho_from" id="bids_ho_from" class="form-control" max = "<?php echo $startDate; ?>" value="<?php echo $startDate; ?>" required>
                            </div>
                            
                            <label class="col-md-1 col-form-label" for="bids_ho_to"> To </label>
                            <div class="col-md-2">
                                <input type="date" name="bids_ho_to" id="bids_ho_to" class="form-control" max = "<?php echo $endDate; ?>" value="<?php echo $endDate; ?>" required>
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="BidsHoFilter();">Go</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <div class="modal fade" id="grnInfoModal" tabindex="-1">
                    <div class="modal-dialog modal-xl">
                        <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">GRN Sublist Info</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="grnInfoModalBody">
                            <!-- iframe will be injected here -->
                        </div>
                        </div>
                    </div>
                </div>

                <table id="grn_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Project Name</th>
                            <th>Project Code</th>
                            <th>Supplier Name</th>
                            <th>Invoice Date</th>
                            <th>PO Number</th>
                            <th>GRN Number</th>
                            <th>Supplier Invoice No</th>
                            <th>Approve Status</th>
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