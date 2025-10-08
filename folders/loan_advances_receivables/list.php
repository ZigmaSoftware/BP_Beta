<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                <div class="row">                                    
                    <div class="col-12">
                          <div class="form-group row ">
                            <label class="col-md-1 col-form-label" for="from_date"> From  </label>
                            <div class="col-md-2">
                                <input type="date" name="from_date" id="from_date" class="form-control" max = "<?php echo $today; ?>" value="">
                            </div>
                            
                            <label class="col-md-1 col-form-label" for="to_date"> To </label>
                            <div class="col-md-2">
                                <input type="date" name="to_date" id="to_date" class="form-control" max = "<?php echo $today; ?>" value="">
                            </div>
                            <div class="col-md-2 d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="loanadvancereceivableFilter();">Go</button>
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="loan_advances_receivables_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Entry Date</th>
                            <th>Employee Name</th>
                            <th>Loan No</th>
                            <th>Paid Amount</th>
                            <th>Current Payable</th>
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