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
                            <label class="col-md-4 col-form-label" for="supplier_name"> Supplier Name </label>
                            <div class="col-md-8">
                               <select id="supplier_name" name="supplier_name" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="purchase_request_no"> Purchase Req. No </label>
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
                            <label class="col-md-4 col-form-label" for="purchase_type"> Purchase Type </label>
                            <div class="col-md-8">
                                <select id="purchase_type" name="purchase_type" class="select2 form-control" required><?php //echo $password; ?></select>
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
                                            <th>Part No</th>
                                            <th>UOM</th>
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
                                            <td>#</td>
                                            <td><select id="item_name" name="item_name" class="select2 form-control" required><?php //echo $password; ?>" </select></td>
                                            <td><input type="text" id="part_no" name="part_no" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="uom" name="uom" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="qty" name="qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="rate" name="rate" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="discount" name="discount" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="tax" name="tax" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="amount" name="amount" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><button type="button" class=" btn btn-success waves-effect  waves-light material_request_add_update_btn" >ADD</button></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>test</td>
                                            <td>1234</td>
                                            <td>Kg</td>
                                            <td>5</td>
                                            <td>50</td>
                                            <td></td>
                                            <td></td>
                                            <td>250</td>
                                            <td></td>
                                        </tr>
                                    </tbody>
                                    <tfoot>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>Total</td>
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
                        <div class="row form-group">
                            <label class="col-md-2 col-form-label" for="total_item"> Total No of Items :</label>
                             <label class="col-md-2 col-form-label text-green" for="no_of_item">  1 </label>
                            <input type="hidden" name="no_of_item" id="no_of_item" class="form-control" value="">
                        </div>
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <table width="100%"  border="0" >
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;width: 70%"><strong>Net Amount</strong></td>
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
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Other Charge</strong></td>
                                        <td width="20%" align="right"><input type="text" name="other_charge"  class="form-control"   id="other_charge" style="text-align:right;width: 150px;" value=""></td>
                                        <td width="20%" align="center">
                                        <select name="other_tax" id="other_tax" class="form-control" >
                                        </select>
                                        </td>
                                        <td height="30" width="20%" align="right"><input type="text" name="other_charge_per"  class="form-control"  readonly id="other_charge_per" style="text-align:right;width: 200px;" value=""></td> 
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Tcs</strong></td>
                                        <td>&nbsp;</td>
                                        <td width="20%" align="center"><input type="text" name="tcs_per"  class="form-control"   id="tcs_per" style="text-align:right; width: 100px;"  value=""></td>
                                        <td height="30" width="20%" align="right"><input type="text" name="tcs_amt"  class="form-control"  readonly id="tcs_amt" style="text-align:right;width: 200px;" value=""></td>
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Round off</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"><input type="text" name="round_of"  class="form-control"  id="round_of" style="text-align:right"  value=""></td>      
                                    </tr>
                                    <tr>
                                        <td height="30" align="right"  style="padding: 10px;"><strong>Gross Amount</strong></td>
                                        <td>&nbsp;</td>
                                        <td>&nbsp;</td>
                                        <td height="30" width="20%" align="right"><input type="text" readonlyname="net_amt"   class="form-control"   id="net_amt" style="text-align:right" value=""></td>                                   
                                    </tr>
                                </table>  
                            </div>                                
                        </div>
                        <div class="top bottom left right "><br>
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <div class="row form-group">
                                    <label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Supply and Delivery  Terms :</strong></label>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Contact Person:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="contact_person" id="contact_person" class="form-control" value="" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Quote No:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="quote_no" id="quote_no" class="form-control" value="" required>
                                    </div>
                                    <div class="col-md-1">
                                        <label class=" form-control-label">&nbsp;<strong>Quote Date:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="date" class="form-control" name="quote_date" id="quote_date" value="<?php echo $today;?>" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Delivery:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="delivery" id="delivery" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Ship Via:</strong>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="delivery" id="delivery" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Freight:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="fright" id="fright" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Delivery Site:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="del_site" id="del_site" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Payment Days:</strong></label>
                                    </div>
                                    <div class="col-md-2">  
                                        <input type="text" name="pay_terms" id="pay_terms" onKeyPress="if((event.keyCode &lt; 46)||(event.keyCode &gt; 57)) event.returnValue = false;" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong> Document Through</strong></label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                            <input type="radio" id="bank" name="bank" class="custom-control-input" value="bank" required>
                                            <label class="custom-control-label text-primary" for="bank">Bank</label>
                                        </div>&nbsp;&nbsp;
                                        <div class="custom-control custom-radio">
                                            <input type="radio" id="direct" name="direct" class="custom-control-input" value="direct" required>
                                            <label class="custom-control-label text-primary" for="direct">Direcct</label>
                                        </div>
                                    </div>
                                <div class="row form-group" id="dealer_id">
                                    <div class="col-md-2" >
                                        <label class=" form-control-label">&nbsp;<strong>Dealer Reference:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" name="dealer_reference" id="dealer_reference" class="form-control" required>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-md-2">
                                        <label class=" form-control-label">&nbsp;<strong>Billing Address:</strong></label>
                                    </div>
                                    <div class="col-md-2">
                                        <select name="po_company"  required id="po_company" class="form-control select2"  tabindex="1" >                                   
                                        </select> 
                                    </div>
                                </div>
                            </div>                                
                        </div>
                        </div>
                        <br> 
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
