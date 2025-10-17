<?php
// $group_options      = group_name();
// $group_options      = select_option($group_options,"Select the Group Name");

// $sub_group_unique_id      = sub_group_name();
// $sub_group_unique_id      = select_option($sub_group_unique_id, "Select the Sub Group Name");

// $category_unique_id      = category_name();
// $category_unique_id      = select_option($category_unique_id, "Select the Category Name", $category_unique_ids);


$data_type_options  = [
    1 => [
        "unique_id" => 1,
        "value"     => "Consumable",
    ],
    2 => [
        "unique_id" => 2,
        "value"     => "Component",
    ]
];
$data_type_options  = select_option($data_type_options, "Select The Type");



$requisition_type_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Regular"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Service"
    ],
    3 => [
        "unique_id" => "3",
        "value"     => "Capital"
    ]
];


$requisition_type_options    = select_option($requisition_type_options,"Select",$requisition_type);

$requisition_for_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Direct"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "SO"
    ],
    3 => [
        "unique_id" => "3",
        "value"     => "Planning WO"
    ]
];



$requisition_for_options    = select_option($requisition_for_options,"Select",$requisition_for);

$approval_status_options = [
    ["unique_id" => "pending_l1", "value" => "Pending (L1)"],
    ["unique_id" => "pending_l2", "value" => "Pending (L2)"],
    ["unique_id" => "pending_l3", "value" => "Pending (L3)"],
    ["unique_id" => "approved_l1","value" => "Approved (L1)"],
    ["unique_id" => "approved_l2","value" => "Approved (L2)"],
    ["unique_id" => "approved_l3","value" => "Approved (L3)"],
    ["unique_id" => "rejected_l1","value" => "Rejected (L1)"],
    ["unique_id" => "rejected_l2","value" => "Rejected (L2)"],
    ["unique_id" => "rejected_l3","value" => "Rejected (L3)"]
];
$approval_status_options = select_option($approval_status_options,"All",$approval_status);

$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);


$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);

$type_options                   = doc_type_options();
$type_options                   = select_option($type_options,"Select the Document Type",$doc_type);



if ($_GET['from_date'] == '') {
    $from_date = date("Y-m-01");
} else {
    $from_date = $_GET['from_date'];
}
if ($_GET['to_date'] == '') {
    $to_date = date("Y-m-d");
} else {
    $to_date = $_GET['to_date'];
}

$current_month = date('Y-m-d');

?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row add_btn">
                            <div class="col-md-12">
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                        
                        <!--<div class="form-group row ">-->
                            
                        <!--    <label class="col-md-1 col-form-label" for="sub_group_unique_id">Sub Group Name</label>-->
                        <!--    <div class="col-md-2">-->
                        <!--        <select name="sub_group_unique_id" id="sub_group_unique_id" class="select2 form-control"  onchange="get_sub_group(this.value, 1)" >-->
                        <!--            
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--    <label class="col-md-1 col-form-label" for="category_unique_id">Category Name</label>-->
                        <!--    <div class="col-md-2">-->
                        <!--        <select name="category_unique_id" id="category_unique_id" class="select2 form-control" >-->
                        <!--            
                        <!--        </select>-->
                        <!--    </div>-->
                        <!--    <div class="col-md-1 d-flex justify-content-center">-->
                        <!--        <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="item_filter();">Go</button>-->
    
                        <!--    </div>-->
                        <!--</div>-->
                        
                       <div class="form-group row">
                        <!--<label class="col-md-1 col-form-label" for="pr_number">PR Number</label>-->
                          
                        <!--<div class="col-md-2">-->
                        <!--    <input type="text" name="pr_number" id="pr_number" class="form-control" placeholder="PR Number">-->
                        <!--</div>-->
                          <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="company_name">Company</label>
                        <div class="col-md-12">
                            <select name="company_name" id="company_name" class="select2 form-control">
                                <?php echo $company_name_options;?>
                            </select>
                        </div>
                        </div>
                         <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="project_name">Project</label>
                        <div class="col-md-12">
                            <select name="project_name" id="project_name" class="select2 form-control">
                                <?php echo $project_options;?>
                            </select>
                        </div>
                        </div>
                          <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="from_date"> From Date </label>
                        <div class="col-md-12">
                            <input type="date" class="form-control" id='from_date' name='from_date' value='<?php echo $from_date; ?>' max="<?php echo $current_month; ?>" onchange="dateValidation()">
                        </div>
                         </div>
                        <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="to_date"> To Date </label>
                        <div class="col-md-12">
                            <input type="date" class="form-control" id='to_date' name='to_date' value='<?php echo $to_date; ?>' max="<?php echo $current_month; ?>" onchange="dateValidation()">  
                        </div>
                           </div>
                         <div class="col-md-3">
                        <label class="col-md-12 col-form-label">Approval Status</label>
                        <div class="col-md-12">
                            <select name="lvl_1_status" id="lvl_1_status" class="select2 form-control">
                                <?php echo $approval_status_options;?>
                            </select>
                        </div>
                        </div>
                        
                        <div class="col-md-2 mt-4">
                            <button type="button" class="btn btn-primary btn-rounded mr-2" onclick="po_filter();">Go</button>
                        </div>
                    </div>


                        
                        
                    </div>
                </div>
                
                <div class="modal fade" id="poUploadModal" tabindex="-1" role="dialog" aria-labelledby="poUploadModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    
                      <div class="modal-header">
                        <h5 class="modal-title" id="poUploadModalLabel">Upload po Document</h5>
                      </div>
                
                      <div class="modal-body">
                        <form class="was-validated documents_form" id="documents_form">
                            <input type="hidden" name="upload_unique_id" id="upload_unique_id" value="">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row mb-2">
                                            <label class="col-md-2 col-form-label" for="type">Type</label>
                                            <div class="col-md-4">
                                                <select id="type" name="type" class="form-control" onchange="showAddNewTypeInput(this)">
                                                    <?php echo $type_options; ?>
                                                </select>
                                            </div>
                                            <label class="col-md-2 col-form-label" for="biometric_id">Files (PAN,GST etc)  </label>
                                            <div class="col-md-4">
                                                <input type="file" multiple id="test_file_qual" name="test_file_qual[]" class="form-control dropify" data-default-file="uploads/supplier_creation/<?php echo $file_attach ?>"  >
                                            </div>
                                        </div>
                                        <div class="form-group row mb-2 ">
                                            <div class="col text-center">
                                                <button type="button" class=" btn btn-success waves-effect  waves-light documents_add_update_btn" onclick="documents_add_update()">ADD</button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <table id="documents_datatable" class="table dt-responsive nowrap w-100">
                                                    <thead>
                                                        <tr>
                                                            <th>#</th>
                                                            <th>Type</th>
                                                            <th>Document</th>
                                                            <th>Action</th>
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
                
                <table id="purchase_order_datatable" class="table table-striped  dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PO Number</th>
                            <th>Company Name</th>
                            <th>Project Name</th>
                            <th>Supplier Name</th>
                            <th>Entry Date</th>
                            <th>Net Amount</th>
                            <th>Gross Amount</th>
                            <th>Approval Status</th>
                            <!--<th>Level 2 Status</th>-->
                            <!--<th>Requisition Date</th>-->
                            <!--<th>Requested By</th>-->
                            <!--<th>Remarks</th>-->
                            <!--<th>Active Status</th>-->
                            <th>View</th>
                            <th>Print</th>
                            <th>Forclose</th>
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