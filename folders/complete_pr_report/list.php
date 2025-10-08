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
                <h4 class="mb-3">Complete Purchase Requisition Report</h4>
                
                
                 <div class="form-group row">
                    <div class="row mb-3">
                      <div class="col-md-2">
                        <label for="from_date">From Date</label>
                        <input type="date" id="from_date" class="form-control" placeholder="From Date">
                      </div>
                      <div class="col-md-2">
                        <label for="to_date">To Date</label>
                        <input type="date" id="to_date" class="form-control" placeholder="To Date">
                      </div>
                      <div class="col-md-2">
                        <label for="company_id">Company</label>
                        <select id="company_id" class="form-select select2" onclick="get_company_project(this.value)">
                          <option value="">All Companies</option>
                          <!-- dynamically populate with PHP if you want -->
                          <?= $company_name_options; ?>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <label for"project_id">Project</label>
                        <select id="project_id" class="form-select select2">
                          <option value="">All Projects</option>
                          <!-- dynamically populate with PHP if you want -->
                          <?= $project_options; ?>
                        </select>
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
                      <div class="col-md-2">
                        <label> </label>
                        <button class="btn btn-primary" id="filterBtn" style="margin-top: 17px;">Filter</button>
                      </div>
                    </div>

                </div>
                
                    
                
                
                    
                <div class="table-responsive" style="overflow-x:auto; max-height:auto;">
                    <table id="complete_pr_datatable" 
                           class="table table-bordered table-striped nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Unit</th>
                                <th>Project Code</th>
                                <th>PR No</th>      <!-- Indent No -->
                                <th>PR Date</th>    <!-- Indent Date -->
                                <th>Type</th>
                                <th>Requisition For</th>
                                <th>Ref SO No</th>
                                <th>Doc Status</th>
                                <th>Item Status</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>SQty</th>
                                <th>UOM</th>
                                <th>PO No</th>
                                <th>PO Status</th>
                                <th>L1 Action by/Date</th>
                                <th>L2 Action by/Date</th>
                                <th>L3 Action by/Date</th>
                                <th>PO Qty</th>
                                <th>Vendor Name</th>
                                <th>GRN/SRN Number</th>
                                <th>GRN/SRN Date</th>
                                <th>Prepared By</th>
                                <th>Prepared Date</th>
                                <th>Authorized By</th>
                                <th>Authorized Date</th>
                                <th>Authorized Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div> <!-- end card body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->
