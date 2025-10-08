<?php
error_reporting(E_ALL);
ini_set('display_errors', 0); // hide from output
ini_set('log_errors', 1);     // log to server error log

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
                <h4 class="mb-3">Pending Purchase Requisition Report</h4>
                
                 <div class="form-group row">
                    <div class="row mb-3">
                      <div class="col-md-2">
                        <label for="from_date" class="form-label">From Date</label>
                        <input type="date" id="from_date" class="form-control" placeholder="From Date">
                      </div>
                      <div class="col-md-2">
                        <label for="to_date" class="form-label">To Date</label>
                        <input type="date" id="to_date" class="form-control" placeholder="To Date">
                      </div>
                      
                      <div class="col-md-2">
                        <label class="form-label">Company</label>
                        <select id="company_id" class="form-control select2" onclick="get_company_project(this.value)">
                          <?= select_option(company_name(), "Select the Company"); ?>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <label class="form-label">Project</label>
                        <select id="project_id" class="form-control select2">
                          <?= select_option(get_project_name(), "Select the Project Name"); ?>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <label for="pr_status" class="form-label">Status</label>
                        <select id="pr_status" class="form-select select2">
                          <option value="">All Status</option>
                          <option value="0">Pending</option>
                          <option value="1">Approved</option>
                          <option value="2">Rejected</option>
                        </select>
                      </div>
                      <div class="col-md-2">
                        <label> </label>
                        <button class="btn btn-primary" id="filterBtn" style="margin-top: 27px;">Filter</button>
                      </div>
                    </div>

                </div>

                
                <div class="table-responsive" style="overflow-x:auto; max-height:auto;">
                    <table id="pending_pr_datatable" 
                           class="table table-bordered table-striped nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Company</th>
                                <th>Project</th>
                                <th>PR No</th>
                                <th>Date</th>
                                <th>Type</th>
                                <th>For</th>
                                <th>Reference SO</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Qty</th>
                                <th>UOM</th>
                                <th>Pending Qty</th>
                                <th>Remarks</th>
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

            </div>
        </div>
    </div>
</div>
