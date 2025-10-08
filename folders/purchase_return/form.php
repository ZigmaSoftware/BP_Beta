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
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Supplier Name</label>
                            <div class="col-md-8">
                                <select id="purchase_request_no" name="purchase_request_no" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                        
                    </div>
                    <div class="col-md-4 col-sm-12 col-12">
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="branch"> Branch </label>
                            <div class="col-md-8">
                                <select id="branch" name="branch" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                       <div class="row form-group">
                            
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
                                            <th>Rack</th>
                                            <th>Qty</th>
                                            <th>Rate</th>
                                            <th>Discount(%)</th>
                                            <th>Tax</th>
                                            <th>Amount</th>
                                            <th>Action</th>
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
                                            <td></td>

                                            
                                        </tr>
                                       
                                    </tbody>
                                    
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                       
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <table width="100%"  border="0" >
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;width: 70%"><strong>Gross Amount</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"> <input type="text" name="gros_amount" readonly class="form-control"  id="gros_amount" style="text-align:right;width: 200px;" value=""></td> 
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Freight Value (%)</strong></td>
                                        <td>&nbsp;</td>
                                        <td width="20%" align="center"><input type="text" name="freight_per"  class="form-control"   id="freight_per" style="text-align:right; width: 100px;"  value=""></td>
                                        <td height="30" width="20%" align="right"><input type="text" name="freight_amt"  class="form-control"  readonly id="freight_amt" style="text-align:right;width: 200px;" value=""></td> 
                                    </tr>
                                    
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Round off</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"><input type="text" name="round_of"  class="form-control"  id="round_of" style="text-align:right"  value=""></td>      
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Net Amount</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"><input type="text" readonlyname="net_amt"   class="form-control"   id="net_amt" style="text-align:right" value=""></td>                                   
                                    </tr>
                                </table>  
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
