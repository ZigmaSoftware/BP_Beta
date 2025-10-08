<?php

$group_options      = product_group_name();
$group_options      = select_option($group_options,"Select the Group Name");

$sub_group_unique_id      = product_type_name();
$sub_group_unique_id      = select_option($sub_group_unique_id, "Select the Sub Group Name");

$company_unique_id      = company_name();
$company_unique_id      = select_option($company_unique_id, "Select the Company Name", $company_unique_ids);

$product_options        = product_name();
$product_options        = select_option($product_options, "Select Product");


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

$type_options                   = doc_type_options();
$type_options                   = select_option($type_options,"Select the Document Type",$doc_type);
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
                        <div class="form-group row ">
                            
                          
                            <div class="col-md-3">
                                  <label class=" col-form-label" for="group_unique_id">Group Name</label>
                                <select name="group_unique_id" id="group_unique_id" class="select2 form-control"  onchange="get_sub_group(this.value)" >
                                    <?php echo $group_options;?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                  <label class=" col-form-label" for="sub_group_unique_id">Sub Group Name</label>
                                <select name="sub_group_unique_id" id="sub_group_unique_id" class="select2 form-control"  onchange="get_sub_group(this.value, 1)" >
                                    <?php echo $sub_group_unique_id;?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class=" col-form-label" for="company_unique_id">Company Name</label>
                                <select name="company_unique_id" id="company_unique_id" class="select2 form-control">
                                    <?php echo $company_unique_id;?>
                                </select>
                            </div>
                            
                            <div class="col-md-3">
                                <label class=" col-form-label" for="prod_unique_id">Product</label>
                                <select name="prod_unique_id" id="prod_unique_id" class="select2 form-control">
                                    <?php echo $product_options;?>
                                </select>
                            </div>
                            <div class="col-md-1 mt-4 mb-2">
                                <button type="button" class="btn btn-primary" onclick="item_filter_1();">Go</button>
    
                            </div>
                        </div>
                    </div>
                </div>
                
                    <div class="modal fade" id="standardUploadModal" tabindex="-1" role="dialog" aria-labelledby="standardUploadModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    
                      <div class="modal-header">
                        <h5 class="modal-title" id="standardUploadModalLabel">Upload standard Bom Document</h5>
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

                <table id="standard_bom_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <!--<th>Group Name</th>-->
                            <!--<th>Sub Group Name</th>-->
                            <!--<th>Company Name</th>-->
                            <th>Product Name</th>
                            <!--<th>Description</th>-->
                            <!--<th>Active Status</th>-->
                            <th>View</th>
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