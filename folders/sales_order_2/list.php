<?php

// Company Name
$company_name_options        = company_name();
$company_name_options        = select_option($company_name_options,"Select");

//Supplier Name
$supplier_name_options     = supplier();
$supplier_name_options     = select_option($supplier_name_options,"Select");


//Customer Name 
$customer_name_options = customers();
$customer_name_options = select_option($customer_name_options,"Select");


// Approve Option
$approve_status_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Not Completed"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Completed"
    ]
];
$approve_status_options    = select_option($approve_status_options,"Select");



$so_type_options = [
        1 => [
            "unique_id" => "1",
            "value" => "product"
        ],
        2 => [
            "unique_id" => "2",
            "value" => "project"
        ],
        3 => [
            "unique_id" => "3",
            "value" => "spare"
        ],
        4 => [
            "unique_id" => "4",
            "value" => "service"
        ],
    ];
$so_type_options    = select_option($so_type_options, "Select", $so_type);

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
                            
                           
                            <div class="col-md-2">
                                 <label class="col-form-label" for="company_name"> Company Name </label>
                                <select name="company_name" id="company_name" class="select2 form-control" required <?= $disable; ?>>
                                    <?= $company_name_options; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-2">
                                <label class="col-form-label" for="customer_name"> Customer Name </label>
                               <select name="customer_name" id="customer_name" class="select2 form-control" required <?= $disable; ?>>
                                <?= $customer_name_options; ?> 
                            </select>
                            </div>
                           
                            <div class="col-md-2">
                                 <label class="col-form-label" for="status_fill"> Active Status</label>
                                <select name="status_fill" id="status_fill" class="select2 form-control" required>
                                    <?php echo $approve_status_options; ?>
                                </select>
                            </div>
                           
                            <div class="col-md-2">
                                 <label class="col-form-label" for="from_date"> From Date </label>
                                <input type="date" class="form-control" id='from_date' name='from_date' value='<?php echo $from_date; ?>' max="<?php echo $current_month; ?>" onchange="dateValidation()">
                            </div>
                            
                           
                            <div class="col-md-2">
                                 <label class="col-form-label" for="to_date"> To Date </label>
                                <input type="date" class="form-control" id='to_date' name='to_date' value='<?php echo $to_date; ?>' max="<?php echo $current_month; ?>" onchange="dateValidation()">  
                            </div>
                            
                            <div class="col-md-1 mt-4 mb-2">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="sales_order_filter();">Go</button>
    
                            </div>
                        </div>
                    </div>
                </div>
                
                                <div class="modal fade" id="soUploadModal" tabindex="-1" role="dialog" aria-labelledby="soUploadModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg" role="document">
                    <div class="modal-content">
                    
                      <div class="modal-header">
                        <h5 class="modal-title" id="soUploadModalLabel">Upload SO Document</h5>
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

                
                <table id="sales_order_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Entry Date</th>
                            <th>Sales Order No</th>
                            <th>Company Name</th>
                            <th>Customer Name</th>
                            <!--<th>Product Name</th>-->
                            <th>SO Type</th>
                            <th>Active Status</th>
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