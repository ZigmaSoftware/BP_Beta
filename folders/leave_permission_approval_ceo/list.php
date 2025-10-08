<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                <div class="row">                                    
                    <div class="col-12">
                          <div class="form-group row ">
                            <label class="col-md-1 col-form-label" for="leave_ceo_from"> From  </label>
                            <div class="col-md-2">
                                <input type="date" name="leave_ceo_from" id="leave_ceo_from" class="form-control" max = "<?php echo $today; ?>" value="">
                            </div>
                            
                            <label class="col-md-1 col-form-label" for="leave_ceo_to"> To </label>
                            <div class="col-md-2">
                                <input type="date" name="leave_ceo_to" id="leave_ceo_to" class="form-control" max = "<?php echo $today; ?>" value="">
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="leavepermissionceoFilter();">Go</button>
                                
                            </div>
                        </div>
                    </div>
                </div>
                <table id="leave_permission_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Entry Date </th>
                            <th>Staff Name</th>
                            <th>Type</th>
                            <th>Reason</th>
                            <th>Approve Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                                            
                </table>
            </form>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->