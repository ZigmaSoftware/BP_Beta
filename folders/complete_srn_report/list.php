<?php
$type_options = doc_type_options();
$type_options = select_option($type_options, "Select the Document Type", $doc_type);

$data_type_options = [
    1 => ["unique_id" => 1, "value" => "Consumable"],
    2 => ["unique_id" => 2, "value" => "Component"]
];
$data_type_options = select_option($data_type_options, "Select The Type");

$requisition_type_options = [
    1 => ["unique_id" => "1", "value" => "Regular"],
    '683568ca2fe8263239' => ["unique_id" => "683568ca2fe8263239", "value" => "Service"],
    '683588840086c13657' => ["unique_id" => "683588840086c13657", "value" => "Capital"]
];
$requisition_type_options = select_option($requisition_type_options, "Select", $requisition_type);

$requisition_for_options = [
    1 => ["unique_id" => "1", "value" => "Direct"],
    2 => ["unique_id" => "2", "value" => "SO"],
    3 => ["unique_id" => "3", "value" => "Ordered BOM"]
];
$requisition_for_options = select_option($requisition_for_options, "Select");

$company_name_options = company_name();
$company_name_options = select_option($company_name_options, "Select the Company");

$project_options = get_project_name();
$project_options = select_option($project_options, "Select the Project Name");

$pr_number_options = get_pr_number();
$pr_number_options = select_option($pr_number_options, "Select the PR Number");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Complete SRN Report</h4>
                
                
                <div class="form-group row">
                    
                    <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="srn_date_from">SRN From Date</label>
                        <div class="col-md-12">
                            <input type="date" name="srn_date_from" id="srn_date_from" class="form-control">
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="srn_date_to">SRN To Date</label>
                        <div class="col-md-12">
                            <input type="date" name="srn_date_to" id="srn_date_to" class="form-control">
                        </div>
                    </div>
                    
                    
                    <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="company_id">Company Name</label>
                        <div class="col-md-12">
                            <select name="company_id" id="company_id" class="select2 form-control" onclick="get_company_project(this.value)">
                                <?php echo $company_name_options;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="project_id">Project Code</label>
                        <div class="col-md-12">
                            <select name="project_id" id="project_id" class="select2 form-control">
                                <?php echo $project_options;?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label for="pr_status">Status</label>
                        <select id="pr_status" class="form-select select2">
                          <option value="">All Status</option>
                          <option value="0">Pending</option>
                          <option value="1">Approved</option>
                          <option value="2">Rejected</option>
                        </select>
                      </div>
                    <!--<div class="col-md-3">-->
                    <!--    <label class="col-md-12 col-form-label" for="pr_number">PR Number</label>-->
                    <!--    <div class="col-md-12">-->
                    <!--        <select name="pr_number" id="pr_number" class="select2 form-control">-->
                    <!--            <?//php echo $pr_number_options;?>-->
                    <!--        </select>-->
                    <!--    </div>-->
                    <!--</div>-->
                    
                    

                    
                    <div class="col-md-1 mt-4">
                        <button type="button" id="goBtn" class="btn btn-primary btn-rounded mr-2">Filter</button>
                    </div>
                
                    
                <div class="table-responsive" style="overflow-x:auto; max-height:auto;">
                    <table id="complete_srn_datatable" 
                           class="table table-bordered table-striped nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>

                                <!-- GRN Details -->
                                <th>SRN No</th>
                                <th>SRN Date</th>
                                <!--<th>SRN Type</th>-->

                                <!-- Unit / Project / Vendor -->
                                <th>Unit</th>
                                <th>Project</th>
                                <th>Vendor Name</th>

                                <!-- Vendor Docs -->
                                <th>Supplier Invoice</th>
                                <th>Invoice Date</th>
                                <th>Challan No</th>
                                <th>eWayBill No</th>
                                <th>Transport Details</th>

                                <!-- PO Details -->
                                <th>PO No</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>PO Qty</th>
                                <th>Accepted Qty</th>
                                <th>Rejected Qty</th>
                                <th>Pending Qty</th>
                                <th>UOM</th>
                                <th>Rate</th>
                                <th>Total Value</th>

                                <!-- Audit / Status -->
                                <th>Prepared By/Dt</th>
                                <th>Authorized By/Dt</th>
                                <th>Status</th> <!-- Pending / Partially Closed / Closed -->
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div> <!-- end card body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->
