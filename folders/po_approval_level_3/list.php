<?php

// ini_set('display_errors', 1);
// error_reporting(E_ALL);

$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select the Company",$company_name);


$project_options  = get_project_name();
$project_options  = select_option($project_options,"Select the Project Name",$project_id);

// $po_approval_options = [
//     1 => [
//         "unique_id" => "1",
//         "value"     => "Approve"
//     ],
//     2 => [
//         "unique_id" => "2",
//         "value"     => "Cancel"
//     ]
// ];
// $po_approval_options              = select_option($po_approval_options, "Pending");


$approval_status_options = [
    ["unique_id" => "pending_l3", "value" => "Pending (L3)"],
    ["unique_id" => "approved_l3","value" => "Approved (L3)"],
    ["unique_id" => "rejected_l3","value" => "Rejected (L3)"]
];
$approval_status_options = select_option($approval_status_options,"All",$approval_status);



if ($_GET['from_date'] == '') {
    $from_date = date("Y-m-d");
} else {
    $from_date = $_GET['from_date'];
}
if ($_GET['to_date'] == '') {
    $to_date = date("Y-m-d");
} else {
    $to_date = $_GET['to_date'];
}

$current_month = date('Y-m-d');


$start_date  = date('Y-m-01');
$end_date  = date('Y-m-d');

?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="row ">
                            
                     
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
                        <div class="col-md-3">
                       <div class="form-group row">
                            <label class="col-md-12 col-form-label" for="from_date"> From Date </label>
                            <div class="col-md-12">
                                <input type="date" class="form-control" id='from_date' name='from_date' value='<?php echo $start_date; ?>' max="<?php echo $current_month; ?>" onchange="dateValidation()">
                            </div>
                            </div>  </div>
                                 <div class="col-md-3">
                                      <div class="form-group row">
                            <label class="col-md-12 col-form-label" for="to_date"> To Date </label>
                            <div class="col-md-12">
                                <input type="date" class="form-control" id='to_date' name='to_date' value='<?php echo $end_date; ?>' max="<?php echo $current_month; ?>" onchange="dateValidation()">  
                            </div>
                            </div>  </div>
                        <!--<label class="col-md-1 col-form-label" for="pr_number">PR Number</label>-->
                          
                        <!--<div class="col-md-2">-->
                        <!--    <input type="text" name="pr_number" id="pr_number" class="form-control" placeholder="PR Number">-->
                        <!--</div>-->
                              <div class="col-md-3">
                                   <div class="form-group row">
                        <label class="col-md-12 col-form-label" for="company_name">Company</label>
                        <div class="col-md-12">
                            <select name="company_name" id="company_name" class="select2 form-control">
                                <?php echo $company_name_options;?>
                            </select>
                        </div>  </div>
                        </div>
                              <div class="col-md-3">
                                   <div class="form-group row">
                        <label class="col-md-12 col-form-label" for="project_name">Project</label>
                        <div class="col-md-12">
                            <select name="project_name" id="project_name" class="select2 form-control">
                                <?php echo $project_options;?>
                            </select>
                        </div>  </div>
                        </div>
                              <div class="col-md-3">
                         <label class="col-md-12 col-form-label">Approval Status</label>
                        <div class="col-md-12">
                            <select class="select2 form-control" id="appr_status" >
                               <?= $approval_status_options; ?>
                            </select>
                        </div>
                        </div>
                    <!--</div>-->
                    
                    <!--<div class="form-group row">-->
                    <!--    <label class="col-md-1 col-form-label" for="type_of_service">Type of Service</label>-->
                    <!--    <div class="col-md-2">-->
                    <!--        <select name="type_of_service" id="type_of_service" class="select2 form-control">-->
                    <!--            <?php echo $requisition_type_options;?>-->
                    <!--        </select>-->
                    <!--    </div>-->
                    <!--    <label class="col-md-1 col-form-label" for="requisition_for">Requisition For</label>-->
                    <!--    <div class="col-md-2">-->
                    <!--        <select name="requisition_for" id="requisition_for" class="select2 form-control">-->
                    <!--            <?php echo $requisition_for_options;?>-->
                    <!--        </select>-->
                    <!--    </div>-->
                    <!--    <label class="col-md-1 col-form-label" for="requisition_date">Req. Date</label>-->
                    <!--    <div class="col-md-2">-->
                    <!--        <input type="date" name="requisition_date" id="requisition_date" class="form-control">-->
                    <!--    </div>-->
                     <div class="col-md-3">
                        <div class="col-md-12 mt-4">
                            <button type="button" class="btn btn-primary btn-rounded mr-2" onclick="po_filter();">Go</button>
                        </div>  </div>
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

                
                <table id="po_approval_level_3_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Entry Date</th>
                            <th>PO Number</th>
                            <th>Company Name</th>
                            <th>Project Name</th>
                             <th>Supplier Name</th>
                            <th>Net Amount</th>
                            <th>Gross Amount</th>
                            <th>Appr Net Amount</th>
                            <th>Appr Gross Amount</th>
                            <th>lvl 2 Net Amount</th>
                            <th>lvl 2 Gross Amount</th>
                            <th>lvl 3 Net Amount</th>
                            <th>lvl 3 Gross Amount</th>
                            <th>Approval Status</th>
                            <th>View</th>
                            <th>Print</th>
                            <!--<th>Requisition Date</th>-->
                            <!--<th>Requested By</th>-->
                            <!--<th>Remarks</th>-->
                            <!--<th>Active Status</th>-->
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