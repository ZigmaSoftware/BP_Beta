<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <?php
                    $startDate = date("Y-m-01");
                    $endDate = date("Y-m-t");
                ?>
                <form class="was-validated" autocomplete="off">    
                    <div class="row">
                        <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="bids_ho_from">From</label>
                        <div class="col-md-12">
                            <input type="date" name="bids_ho_from" id="bids_ho_from" class="form-control"
                                max="<?= $endDate ?>" value="<?= $startDate ?>" required>
                        </div>   </div>
                
                         <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="bids_ho_to">To</label>
                        <div class="col-md-12">
                            <input type="date" name="bids_ho_to" id="bids_ho_to" class="form-control"
                                max="<?= $endDate ?>" value="<?= $endDate ?>" required>
                        </div>   </div>
                    <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="grn_approve_status">Status</label>
                        <div class="col-md-12">
                            <select name="grn_approve_status" id="grn_approve_status" class="form-control">
                                <option value="">All</option>
                                <option value="1">Approved</option>
                                <option value="2">Rejected</option>
                                <option value="0" selected>Pending</option>
                            </select>
                        </div>        </div>
                        <div class="col-md-3">
                        <div class="col-md-12 mt-4">
                            <button type="button" class="btn btn-primary btn-rounded" onclick="grnApprovalFilter2();">Go</button>
                        </div>
                    </div>   </div>
                </form>
                
                <div class="modal fade" id="grnUploadModal" tabindex="-1" role="dialog" aria-labelledby="grnUploadModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    
                      <div class="modal-header">
                        <h5 class="modal-title" id="grnUploadModalLabel">Upload GRN Document</h5>
                      </div>
                
                      <div class="modal-body">
                        <form class="was-validated documents_form" id="documents_form">
                            <input type="hidden" name="upload_unique_id" id="upload_unique_id" value="">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="documents_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Type</th>
                                                            <th>Document</th>
                                                            <!--<th>Action</th>-->
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                      </div>
                
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>
                
                    </div>
                  </div>
                </div>

                <div class="table-responsive">
                    <table id="grn_datatable" class="table table-striped table-bordered dt-responsive nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Company Name</th>
                                <th>Project Name</th>
                                <!--<th>Project Code</th>-->
                                <th>Supplier Name</th>
                                <th>Invoice Date</th>
                                <th>PO Number</th>
                                <th>GRN Number</th>
                                <th>Supplier Invoice No</th>
                                <th>Checked By</th>
                                <th>Remarks</th>
                                <th>Approve Status</th>
                                <th>View</th>
                                <th>Print</th>
                                <th id="action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>                                            
                </table>
                </div>
            </div> 
        </div> 
    </div> 
</div> 
