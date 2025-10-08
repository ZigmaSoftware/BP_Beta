<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text               = "Save";
$btn_action             = "create";
$is_btn_disable         = "";

$unique_id              = "";

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated funnel_form" name = "funnel_form">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="staff_name"> Staff Name </label>
                            <div class="col-md-8">
                               <select id="staff_name" name="staff_name" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="branch"> Branch </label>
                            <div class="col-md-8">
                                <select id="branch" name="branch" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="vehicle_name"> Vehicle Name </label>
                            <div class="col-md-8">
                                <select id="vehicle_name" name="vehicle_name" class="select2 form-control" required><?php //echo $password; ?></select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="row form-group ">
                             <div class="custom-control custom-radio">
                              <input type="radio" id="vehicle" name="vehicle" class="custom-control-input" value="0" required>
                              <label class="custom-control-label text-primary" for="vehicle">Vehicle</label>
                           </div>&nbsp;&nbsp;
                           <div class="custom-control custom-radio">
                              <input type="radio" id="equipment" name="equipment" class="custom-control-input" value="1" required>
                              <label class="custom-control-label text-primary" for="equipment">Equipment</label>
                           </div>&nbsp;&nbsp;
                           <div class="custom-control custom-radio">
                              <input type="radio" id="asset" name="asset" class="custom-control-input" value="2" required>
                              <label class="custom-control-label text-primary" for="asset">Asset</label>
                           </div>&nbsp;&nbsp;
                           <div class="custom-control custom-radio">
                              <input type="radio" id="work_order" name="work_order" class="custom-control-input" value="3" required>
                              <label class="custom-control-label text-primary" for="work_order">Work Order</label>
                           </div>
                        </div>
                        
                        <div class="row form-group">
                            <label class="col-md-4 col-form-label" for="request_type"> Request Type </label>
                            
                            <div class="custom-control custom-radio">
                              <input type="radio" id="direct" name="direct" class="custom-control-input" value="0" required>
                              <label class="custom-control-label text-primary" for="direct">Direct</label>
                           </div>&nbsp;&nbsp;
                           <div class="custom-control custom-radio">
                              <input type="radio" id="bulk" name="bulk" class="custom-control-input" value="1" required>
                              <label class="custom-control-label text-primary" for="work_order">Bulk</label>
                           </div>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="row  form-group">
                            <label class="col-md-4 font-weight-normal" for="entry_date"> Entry Date</label>
                            <div class="col-md-8">
                                <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo $today; ?>">
                            </div>
                        </div>
                    </div>                                  
                    <div class="col-12">                            
                       <div class="row">
                            <div class="col-12">
                            <!-- Table Begiins -->
                                <table id="funnel_product_datatable" class="table dt-responsive nowrap w-100">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Stock In Hand</th>
                                            <th>Qty</th>
                                            <th>Unit</th>
                                            <th>Description</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td><select id="item_name" name="item_name" class="select2 form-control" required><?php //echo $password; ?>" </select></td>
                                            <td><input type="text" id="stock" name="stock" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="qty" name="qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="unit" name="unit" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="sub_descrption" name="sub_descrption" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><button type="button" class=" btn btn-success waves-effect  waves-light material_request_add_update_btn" >ADD</button></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>test</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td>Kg</td>
                                            <td>Test</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tfoot>
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-2 col-form-label" for="request_reason"> Request Reason </label>
                        </div>
                        <div class="row form-group">
                           <div class="col-md-8">
                                <textarea id="request_reason" name="request_reason" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <!-- Cancel,save and update Buttons -->
                                <?php echo btn_cancel($btn_cancel);?>
                                <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                            </div>                                
                        </div>
                    </div>
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  
