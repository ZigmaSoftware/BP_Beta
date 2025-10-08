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
                    <div class="col-md-6 col-sm-12 col-12">
                        <div class="row ">
                            <label class="col-md-4 col-form-label" for="staff_name"> Staff Name </label>
                            <div class="col-md-8">
                                <label class="col-md-8 text-green" for="staff_name">Test  </label>
                                <input type="hidden" name="staff_id" id="staff_id" class="form-control" value="">
                            </div>
                        </div>
                        <div class=" row">
                            <label class="col-md-4 col-form-label" for="branch"> Branch </label>
                            <div class="col-md-8">
                                <label class="col-md-8 text-green" for="branch">Test  </label>
                                <input type="hidden" name="branch_id" id="branch_id" class="form-control" value="">
                            </div>
                        </div>
                        <div class=" row">
                            <label class="col-md-4 col-form-label" for="vehicle_name"> Vehicle Name </label>
                            <div class="col-md-8">
                                <label class="col-md-8 text-green" for="vehicle_name">Test  </label>
                                <input type="hidden" name="vehicle_id" id="vehicle_id" class="form-control" value="">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-12">
                        <div class="row ">
                            <label class="col-md-4 col-form-label" for="request_no"> Item Request No </label>
                            <div class="col-md-8">
                               <label class="col-md-8 text-green" for="request_no">MR-000001  </label>
                                <input type="hidden" name="request_no_id" id="request_no_id" class="form-control" value="">
                            </div>
                        </div>
                        <div class="row ">
                            <label class="col-md-4 font-weight-normal" for="entry_date"> Entry Date</label>
                            <div class="col-md-4">
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
                                            <th>HO Qty</th>
                                            <th>App Qty</th>
                                            <th>Rate</th>
                                            <th>Value</th>
                                            <th>App Status</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>1</td>
                                            <td>test</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td><input type="text" id="ho_app_qty" name="ho_app_qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="app_qty" name="app_qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="rate" name="rate" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="value" name="value" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><select id="app_status" name="app_status" class="select2 form-control" required><?php //echo $password; ?></select> </td>
                                            <td><input type="text" id="sub_descrption" name="sub_descrption" class="form-control" value="<?php //echo $password; ?>" required></td>
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
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tfoot>
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                        <div class="row">
                            <label class="col-md-2 col-form-label" for="request_reason"> Request Reason </label>
                             <label class="col-md-8 text-green" for="request_reason">Test</label>
                              
                        </div>
                        <div class="row">
                           <label class="col-md-2 col-form-label" for="request_reason"> Request Type </label>
                             <label class="col-md-8 text-green" for="request_type">Test</label>
                               
                        </div>
                        <div class="row form-group">
                            <label class="col-md-2 col-form-label" for="ceo_instruction"> CEO Instruction </label>
                            <div class="col-md-6">
                                <textarea id="ceo_instruction" name="ceo_instruction" rows="5" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-2 font-weight-normal" for="approve_status"> Approve Status</label>
                            <div class="col-md-2">
                                <select name="approve_status" id="approve_status" class="form-control" ></select>
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
