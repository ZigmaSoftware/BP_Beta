<div class="row">
    <div class="col-12">
        <div class="card">
        <div class="col-md-12">
            <?php echo btn_add($btn_add); ?>
        </div> 
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >    
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row ">
                            <label class="col-md-1 col-form-label" for="bids_ho_from"> From  </label>
                            <div class="col-md-2">
                                <input type="date" name="bids_ho_from" id="bids_ho_from" class="form-control" max = "<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                            </div>
                            
                            <label class="col-md-1 col-form-label" for="bids_ho_to"> To </label>
                            <div class="col-md-2">
                                <input type="date" name="bids_ho_to" id="bids_ho_to" class="form-control" max = "<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="BidsHoFilter();">Go</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <table id="management_approval_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Bid No</th>
                            <th>Entry Date</th>
                            <th>Customer Name</th>
                            <th>Segment</th>
                            <th>Location</th>
                            <th>Bid Value</th>
                            <th>Bid Completed Date</th>
                            <th>HO Approval</th>
                            <th>Management Approval</th>
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