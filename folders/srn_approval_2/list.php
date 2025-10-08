<?php

    $type_options                   = doc_type_options();
    $type_options                   = select_option($type_options,"Select the Document Type",$doc_type);

?>

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
                        <div class=" row ">
                                <div class="col-md-3">
                            <label class="col-md-12 col-form-label" for="bids_ho_from"> From  </label>
                            <div class="col-md-12">
                                <input type="date" name="bids_ho_from" id="bids_ho_from" class="form-control" max = "<?php echo $startDate; ?>" value="<?php echo $startDate; ?>" required>
                            </div>
                            </div>
                              <div class="col-md-3">
                            <label class="col-md-12 col-form-label" for="bids_ho_to"> To </label>
                            <div class="col-md-12">
                                <input type="date" name="bids_ho_to" id="bids_ho_to" class="form-control" max = "<?php echo $endDate; ?>" value="<?php echo $endDate; ?>" required>
                            </div>
                            </div>
                              <div class="col-md-3">
                            <label class="col-md-12 col-form-label" for="bids_status"> Status </label>
                            <div class="col-md-12">
                                <select name="bids_status" id="bids_status" class="form-control select2">
                                    <option value="">All</option>
                                    <option value="0" selected>Pending</option>
                                    <option value="1">Approved</option>
                                    <option value="2">Rejected</option>
                                </select>
                            </div>
                            </div>
                              <div class="col-md-3">
                            <div class="col-md-12 mt-4">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="BidsHoFilter();">Go</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
                <!--<div class="modal fade" id="srnInfoModal" tabindex="-1">-->
                <!--    <div class="modal-dialog modal-xl">-->
                <!--        <div class="modal-content">-->
                <!--        <div class="modal-header">-->
                <!--            <h5 class="modal-title">srn Sublist Info</h5>-->
                <!--            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
                <!--        </div>-->
                <!--        <div class="modal-body" id="srnInfoModalBody">-->
                            <!-- iframe will be injected here -->
                <!--        </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                
                
                
                <div class="modal fade" id="srnUploadModal" tabindex="-1" role="dialog" aria-labelledby="srnUploadModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    
                      <div class="modal-header">
                        <h5 class="modal-title" id="srnUploadModalLabel">Upload srn Document</h5>
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


                <table id="srn_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Company Name</th>
                            <th>Project Name</th>
                            <!--<th>Project Code</th>-->
                            <th>Supplier Name</th>
                            <th>Invoice Date</th>
                            <th>PO Number</th>
                            <th>srn Number</th>
                            <th>Supplier Invoice No</th>
                            <!--<th>Document Uploads</th>-->
                            <th>Approve Status</th>
                            <th>View</th>
                            <th>Print</th>
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