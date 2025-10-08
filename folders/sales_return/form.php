<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text               = "Save";
$btn_action             = "create";
$is_btn_disable         = "";

$unique_id              = "";

?>
<style>
.top{ border-top: 1px solid #000000;}
.bottom{ border-bottom: 1px solid #000000;}
.left{ border-left: 1px solid #000000;}
.right{ border-right: 1px solid #000000;}
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated funnel_form" name = "funnel_form">
                <div class="row">
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="supplier_name"> Sales Return  No </label>
                            <div class="col-md-8">
                              
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Customer Name</label>
                            <div class="col-md-8">
                                <select id="purchase_request_no" name="purchase_request_no" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Vehicle Type</label>
                            <div class="col-md-8">
                               <input type="radio" name="" >Company
                               <input type="radio" name="" >Private
                            </div>
                        </div>

                        
                    </div>
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="branch">  </label>
                            <div class="col-md-8">
                                
                            </div>
                        </div>
                       <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Branch Name</label>
                            <div class="col-md-8">
                                <select id="" name="" class="select2 form-control" required></select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Vehicle Name</label>
                            <div class="col-md-8">
                                <select id="purchase_request_no" name="purchase_request_no" class="select2 form-control" required><?php //echo $password; ?> </select>
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

                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> DC NO</label>
                            <div class="col-md-8">
                                <select id="purchase_request_no" name="purchase_request_no" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>

                         <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Freight Fare</label>
                            <div class="col-md-8">
                               <input type="radio" name="" >Company
                               <input type="radio" name="" >Party
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
                                            <th>Check</th>
                                            <th>#</th>
                                            <th>Product Name</th>
                                            <th>Dispatched Qty</th>
                                            <th>Return Qty</th>
                                            <th>Reason</th>
                                            <th>Description</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>

                                            
                                        </tr>
                                       
                                    </tbody>
                                    
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                       
                        <div class="form-group row">
                            <label class="col-md-1 col-form-label" for="purchase_request_no"> Description</label>
                            <div class="col-md-3">
                               <textarea class="form-control"></textarea>
                            </div>
                        </div>
                        
                        <br> 
                        
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
