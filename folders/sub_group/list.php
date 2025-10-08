<?php

$group_options      = group_name();
$group_options      = select_option($group_options,"Select the Group Name", $group_unique_id);

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
                            <label class="col-md-2 col-form-label textright" for="group_unique_id"> Group Name </label>
                            <div class="col-md-2">
                                <select name="group_unique_id" id="group_unique_id" class="select2 form-control"  required>
                                    <?php echo $group_options;?>
                                </select>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-primary" onclick="sub_group_filter();">Go</button>
    
                            </div>
                        </div>
                    </div>
                </div>
                <table id="sub_group_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Sub Group Name</th>
                            <th>Group Name</th>
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