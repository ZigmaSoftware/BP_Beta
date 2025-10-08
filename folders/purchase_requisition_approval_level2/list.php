<?php

$pr_number_options          = get_pr_number();
$pr_number_options          = select_option($pr_number_options,"Select the Project Name");

$company_name_options       = company_name();
$company_name_options       = select_option($company_name_options,"Select the Company");

$project_options            = get_project_name();
$project_options            = select_option($project_options,"Select the Project Name");

        $requisition_type_options = [
            1 => [
                "unique_id" => "1",
                "value"     => "Regular"
            ],
            '683568ca2fe8263239' => [
                "unique_id" => "683568ca2fe8263239",
                "value"     => "Service"
            ],
            '683588840086c13657' => [
                "unique_id" => "683588840086c13657",
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
        "value"     => "Ordered BOM"
    ]
];

$requisition_for_options    = select_option($requisition_for_options,"Select");


$status_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Approved"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Cancelled"
    ]
];

$status_options    = select_option($status_options,"Select");

$status_options_lvl_2 = [
    0 => ["unique_id" => "0", "value" => "Pending"],
    1 => ["unique_id" => "1", "value" => "Approved"],
    2 => ["unique_id" => "2", "value" => "Rejected"]
];

$status_options_lvl_2    = select_option($status_options_lvl_2,"Select");

// print_r($_SESSION);
?>



<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                        <div class="col-md-3">
                            
                            <label class="col-md-12 col-form-label" for="pr_number">PR Number</label>
                            <div class="col-md-12">
                                <select name="pr_number" id="pr_number" class="select2 form-control">
                                    <?php echo $pr_number_options;?>
                                </select>
                            </div>
                            </div>
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
                            <label class="col-md-12 col-form-label" for="type_of_service">Requisition Type</label>
                            <div class="col-md-12">
                                <select name="type_of_service" id="type_of_service" class="select2 form-control">
                                    <?php echo $requisition_type_options;?>
                                </select>
                            </div>
                        </div>
                                <div class="col-md-3">
                                <label class="col-md-12 col-form-label" for="requisition_for">Requisition For</label>
                                <div class="col-md-12">
                                    <select name="requisition_for" id="requisition_for" class="select2 form-control">
                                        <?php echo $requisition_for_options;?>
                                    </select>
                                </div>
                                </div>
                                      <div class="col-md-3">
                                <label class="col-md-12 col-form-label" for="requisition_date">Req. Date</label>
                                <div class="col-md-12">
                                    <input type="date" name="requisition_date" id="requisition_date" class="form-control">
                                </div>
                                </div>
                                <!--<label class="col-md-1 col-form-label" for="item_status">Status</label>-->
                                <!--<div class="col-md-2">-->
                                <!--    <select name="item_status" id="item_status" class="select2 form-control">-->
                                <!--        <?php echo $status_options;?>-->
                                <!--    </select>-->
                                <!--</div>-->
                                      <div class="col-md-3">
                                <label class="col-md-12 col-form-label" for="lvl_2_status">Level 2 Status</label>
                                <div class="col-md-12">
                                    <select name="lvl_2_status" id="lvl_2_status" class="select2 form-control">
                                        <?php echo $status_options_lvl_2;?>
                                    </select>
                                </div>
                                </div>
                                      <div class="col-md-3">
                                <div class="col-md-12 mt-4">
                                    <button type="button" class="btn btn-primary btn-rounded mr-2" onclick="item_filter();">Go</button>
                                </div>
                                </div>
                </div>
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

                <table id="purchase_requisition_lvl_2_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PR Number</th>
                            <th>Company Name</th>
                            <th>Project Name</th>
                            <th>Requisition For</th>
                            <th>Requisition Type</th>
                            <th>Requisition Date</th>
                            <th>Requested By</th>
                            <th>Remarks</th>
                            <!--<th>Level 1 Status</th>-->
                            <th>Level 2 Status</th>
                            <th>View</th>
                            <th>Print</th>
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



<!-- Modal -->
<div class="modal fade" id="approval_modal_form_lvl_2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel1" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header w-100 text-center">
                <h5 class="modal-title" id="designation_name"><b>Purchase Requisition Approval Model</b></h5>
                <select id="bulk_status_select_lvl_2" class="form-select form-select-sm" style="width:auto; display:inline-block;">
                  <option value="">Select Status</option>
                  <option value="1">Approve All</option>
                  <option value="2">Reject All</option>
                </select>
                    <button type="button" class="btn-close btn-close-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="container-fluid">
                <div class="col-md-12 sugg_pop">
                    <div class="row">
                        <div class="col-md-3 text-end">Request No</div>
                        <div class="col-md-3"><b><p id="pr_number_approval">-</p></b></div>
                        <div class="col-md-3 text-end">Date</div>
                        <div class="col-md-3"><b><p id="date_approval">-</p></b></div>
                        <div class="col-md-3 text-end">Requisition Date</div>
                        <div class="col-md-3"><b><p id="requisition_date_approval">-</p></b></div>
                        <div class="col-md-3 text-end">Requisition For</div>
                        <div class="col-md-3"><b><p id="requisition_for_approval">-</p></b></div>
                        <div class="col-md-3 text-end">Requisition Type</div>
                        <div class="col-md-3"><b><p id="requisition_type_approval">-</p></b></div>
                        <div class="col-md-3 text-end">Company Name</div>
                        <div class="col-md-3"><b><p id="company_id_approval">-</p></b></div>
                        <div class="col-md-3 text-end">Project Name</div>
                        <div class="col-md-3"><b><p id="project_id_approval">-</p></b></div>
                    </div>
                </div>
            </div>
            <input type="hidden" id="approval_main_id">
            <div class="modal-body">
            <table id="requisition_approval_modal" class="table table-striped dt-responsive w-100">
<thead>
    <tr>
        <th>#</th>
        <th>Item Code</th>
        <th>Description</th>
        <th>PR Qty</th> <!-- Original PR quantity -->
        <th>L1 Qty</th> <!-- Approved quantity in Level 1 -->
        <th>L2 Qty</th> <!-- Editable quantity in Level 2 -->
        <th>UOM</th>
        <!--<th>Rate</th>-->
        <th>Item Remarks</th>
        <th>Delivery Date</th>
        <th>Status</th>
        <th>Rejected Reason</th>
    </tr>
</thead>


               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
