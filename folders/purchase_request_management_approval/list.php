
    
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >    
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row ">
                            <label class="col-md-1 col-form-label" for="follow_up_call_from"> From  </label>
                            <div class="col-md-4">
                                <input type="date" name="follow_up_call_from" id="follow_up_call_from" class="form-control" max = "<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                            </div>
                            
                            <label class="col-md-1 col-form-label" for="follow_up_call_to"> To </label>
                            <div class="col-md-4">
                                <input type="date" name="follow_up_call_to" id="follow_up_call_to" class="form-control" max = "<?php echo $today; ?>" value="<?php echo $today; ?>" required>
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="followUpCallFilter();">Go</button>
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <table id="follow_up_call_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Call ID</th>
                            <th>Customer Name </th>
                            <th>Call Date</th>
                            <th>Follow Date</th>
                            <th>Call Type</th>
                            <th>Mode</th>
                            <th>Status</th>
                            <th>Remark</th>
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