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

$supplier_options = supplier();
$supplier_options = select_option($supplier_options, "Select Vendor");
?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="mb-3">Pending GRN Report</h4>
                
                
                <div class="form-group row">
                    
                    
                <div class="col-md-3">
                    <label class="col-md-12 col-form-label">From Date</label>
                    <input type="date" id="from_date" class="form-control">
                  </div>
                
                  <!-- To Date -->
                  <div class="col-md-3">
                    <label class="col-md-12 col-form-label">To Date</label>
                    <input type="date" id="to_date" class="form-control">
                  </div>  
                  
                 
                    
                
                     <div class="col-md-3">
                        <label class="col-md-12 col-form-label">Company</label>
                        <select id="company_id" class="form-control select2" onclick="get_company_project(this.value)">
                          <?= select_option(company_name(), "Select the Company"); ?>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label class="col-md-12 col-form-label">Project</label>
                        <select id="project_id" class="form-control select2">
                          <?= select_option(get_project_name(), "Select the Project Name"); ?>
                        </select>
                      </div>
                      <div class="col-md-3">
                        <label class="col-md-12 col-form-label">Supplier</label>
                        <select id="supplier_id" class="form-control select2">
                          <?= $supplier_options ?>
                        </select>
                      </div>
                      
                       <div class="col-md-3">
                        <label for="pr_status" class="col-md-12 col-form-label">Status</label>
                        <select id="pr_status" class="form-select select2">
                          <option value="">All Status</option>
                          <option value="Pending">Pending</option>
                          <option value="Closed">Closed</option>
                          <option value="Partially Received">Partially Received</option>
                        </select>

                      </div>
                    
                
                
                <div class="col-md-1 mt-4">
                    <button type="button" id="goBtn" class="btn btn-primary btn-rounded mr-2">Filter</button>
                </div>
            </div>
                    
                <div class="table-responsive" style="overflow-x:auto; max-height:auto;">
                    <table id="pending_grn_datatable" 
                           class="table table-bordered table-striped nowrap w-100">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                
                                <!-- PO Details -->
                                <th>Unit</th>
                                <th>Project Code</th>
                                <th>PO No</th>
                                <th>PO Date</th>
                                <th>PO Type</th>
                                
                                <!-- Vendor Details -->
                                <th>Vendor Code</th>
                                <th>Vendor Name</th>
                                
                                <!-- PO Item Details -->
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Order Qty</th>
                                <th>Received Qty</th>
                                <th>Pending Qty</th>
                                <th>UOM</th>
                                <th>Rate</th>
                                <th>Amount</th>
                                
                                <!-- GRN Details -->
                                <th>GRN No</th>
                                <th>GRN Date</th>
                                
                                <!-- Status -->
                                <th>Status</th> <!-- Pending / Partially Received / Closed -->
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

            </div> <!-- end card body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div><!-- end row -->
