<?php

    $type_options                   = doc_type_options();
    $type_options                   = select_option($type_options,"Select the Document Type",$doc_type);


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


$company_name_options       = company_name();
$company_name_options       = select_option($company_name_options,"Select the Company");


$project_options            = get_project_name();
$project_options            = select_option($project_options,"Select the Project Name");


$pr_number_options          = get_pr_number();
$pr_number_options          = select_option($pr_number_options,"Select the Project Name");

?>

<div class="col-12 text-end mb-2">
<div class="form-group row">
    <div class="col-md-12">
        <?php echo btn_add($btn_add); ?>
    </div>
</div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                           
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
                        </div></div>
                        <div class="col-md-3">
                                                    
                    <label class="col-md-12 col-form-label" for="type_of_service">Requisition Type</label>
                        <div class="col-md-12">
                            <select name="type_of_service" id="type_of_service" class="select2 form-control">
                                <?php echo $requisition_type_options;?>
                            </select>
                        </div>
                            </div>
                    </div>
                    
                    <div class="form-group row">
 <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="requisition_for">Requisition For</label>
                        <div class="col-md-12">
                            <select name="requisition_for" id="requisition_for" class="select2 form-control">
                                <?php echo $requisition_for_options;?>
                            </select>
                        </div> </div>
                         <div class="col-md-3">
                        <label class="col-md-12 col-form-label" for="requisition_date">Req. Date</label>
                        <div class="col-md-12">
                            <input type="date" name="requisition_date" id="requisition_date" class="form-control">
                        </div>
                        </div>
                        <div class="col-md-1 mt-4">
                            <button type="button" class="btn btn-primary btn-rounded mr-2" onclick="item_filter();">Go</button>
                        </div>
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
                
                <table id="purchase_requisition_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>PR Number</th>
                            <th>Company Name</th>
                            <th>Project Name</th>
                            <!--<th>Type of Service</th>-->
                            <th>Requisition For</th>
                            <th>Requisition Type</th>
                            <th>Requisition Date</th>
                            <th>Requested By</th>
                            <th>Remarks</th>
                             <th>View</th>
                            <th>Print</th>
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