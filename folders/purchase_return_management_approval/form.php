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
                            <label class="col-md-4 col-form-label" for="supplier_name"> Purchase Return  No </label>
                            <div class="col-md-8">
                              
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Purchase Order No</label>
                            <div class="col-md-8">
                                
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="branch"> Branch </label>
                            <div class="col-md-8">
                               
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Supplier Invoice No</label>
                            <div class="col-md-8">
                               
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
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Supplier Name</label>
                            <div class="col-md-8">
                               
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
                                            <th>Rejected Qty</th>
                                            <th>Rate</th>
                                            <th>Discount(%)</th>
                                            <th>Tax</th>
                                            <th>Amount</th>
                                           
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
                             <div class="row  form-group">
                            <label class="col-md-5 font-weight-normal" for="entry_date"> Approve Status</label>
                            <div class="col-md-6">
                                <select class="form-control">
                                    <option value="1">Approved</option>
                                    <option value="2">Management Approval</option>
                                </select>
                            </div>
                            
                        </div>
                    </div>

                    <div class="form-group row">
                             <div class="row  form-group">
                            <label class="col-md-5 font-weight-normal" for="entry_date"> Swap</label>
                            <div class="col-md-7">
                                    <input type="radio"   name="" >Accepted <br>
                                    <input type="radio"   name="" >Swap
                            </div>
                            
                        </div>
                    </div>

                     <div class="form-group row">
                             <div class="row  form-group">
                            <label class="col-md-5 font-weight-normal" for="entry_date"> Request Reason</label>
                            <div class="col-md-6">
                                <textarea class="form-control"></textarea>
                            </div>
                            
                        </div>
                    </div>

                     <div class="form-group row">
                             <div class="row  form-group">
                            <label class="col-md-5 font-weight-normal" for="entry_date"> Description</label>
                            <div class="col-md-6">
                                <textarea class="form-control"></textarea>
                            </div>
                            
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
