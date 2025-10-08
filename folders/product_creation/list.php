<?php

$group_options      = product_group_name();
$group_options      = select_option($group_options,"Select the Group Name");

$sub_group_unique_id      = product_type_name();
$sub_group_unique_id      = select_option($sub_group_unique_id, "Select the Sub Group Name");

$company_unique_id      = company_name();
$company_unique_id      = select_option($company_unique_id, "Select the Company Name", $company_unique_ids);


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
                                <select name="company_unique_id" id="company_unique_id" class="select2 form-control" >
                                    <?php echo $company_unique_id;?>
                                </select>
                            </div>
                            <div class="col-md-1 mt-4 mb-2">
                                <button type="button" class="btn btn-primary" onclick="item_filter();">Go</button>
    
                            </div>
                        </div>
                    </div>
                </div>
                <table id="product_creation_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Group Name</th>
                            <th>Sub Group Name</th>
                            <th>Company Name</th>
                            <th>Product Name</th>
                            <th>Description</th>
                            <th>Active Status</th>
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