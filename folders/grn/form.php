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
                    <div class="col-md-3 col-sm-12 col-12">
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="supplier_invoice_no"> Supplier Invoice No </label>
                            <div class="col-md-6">
                               <select id="supplier_invoice_no" name="supplier_invoice_no" class="select2 form-control" required></select>
                            </div>
                        </div>
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="mode_type"> Mode Type </label>
                            <div class="custom-control custom-radio">
                                <input type="radio" id="cash" name="cash" class="custom-control-input" value="cash" required>
                                <label class="custom-control-label text-primary" for="cash">Cash</label>
                            </div>&nbsp;&nbsp;
                            <div class="custom-control custom-radio">
                                <input type="radio" id="credit" name="credit" class="custom-control-input" value="1" required checked>
                                <label class="custom-control-label text-primary" for="credit">Credit</label>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-md-4 col-form-label" for="payment_terms"> Payment Terms </label>
                            <div class="col-md-6">
                                <input type="text" id="payment_terms" name="payment_terms" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 col-12">
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="invoice_date"> Invoice Date </label>
                            <div class="col-md-6">
                               <input type="date" id="invoice_date" name="invoice_date" class="form-control" required>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-4 col-form-label" for="inward_type"> Inward Type </label>
                            <div class="col-md-6">
                                <select id="inward_type" name="inward_type" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-4 col-form-label" for="pa_status"> PA Status</label>
                            <div class="col-md-6">
                                <select id="pa_status" name="pa_status" class="select2 form-control" required><?php //echo $password; ?> </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 col-sm-12 col-12">
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="dc_no"> DC No </label>
                            <div class="col-md-6">
                               <input type="text" id="dc_no" name="dc_no" class="form-control" required>
                            </div>
                        </div>
                        <div class="row  form-group">
                            <label class="col-md-4 col-form-label" for="po_number"> PO Number</label>
                            <div class="col-md-6">
                                 <select id="po_number" name="po_number" class="select2 form-control" required></select>
                            </div>
                        </div>
                    </div>  
                    <div class="col-md-3 col-sm-12 col-12">
                        <div class="form-group row ">
                            <label class="col-md-4 col-form-label" for="branch"> Branch</label>
                            <div class="col-md-6">
                               <select id="branch" name="branch" class="select2 form-control" required></select>
                            </div>
                        </div>
                        <div class="row  form-group">
                             <label class="col-md-4 col-form-label" for="supplier_name"> Supplier Name</label>
                            <div class="col-md-6">
                               <select id="supplier_name" name="supplier_name" class="select2 form-control" required></select>
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
                                            <th>Order Qty</th>
                                            <th>Prev Receipt</th>
                                            <th>UOM</th>
                                            <th>Now Received Qty</th>
                                            <th>Accepted Qty</th>
                                            <th>Rejected Qty</th>
                                            <th>Rejected Reason</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td></td>
                                            <td><select id="item_name" name="item_name" class="select2 form-control" required><?php //echo $password; ?>" </select></td>
                                            <td><input type="text" id="order_qty" name="order_qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="prev_receipt" name="prev_receipt" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="unit" name="unit" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="now_received_qty" name="now_received_qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="accept_qty" name="accept_qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="rejected_qty" name="rejected_qty" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><input type="text" id="rejected_reason" name="rejected_reason" class="form-control" value="<?php //echo $password; ?>" required></td>
                                            <td><button type="button" class=" btn btn-success waves-effect  waves-light material_request_add_update_btn" >ADD</button></td>
                                        </tr>
                                        <tr>
                                            <td>1</td>
                                            <td>test</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td>Kg</td>
                                            <td>20</td>
                                            <td>5</td>
                                            <td>20</td>
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
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tfoot>
                                </table>
                                <!-- Table Ends -->
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-2 font-weight-normal" for="po_status"> PO Status/ Request Status</label>
                            <div class="col-md-2">
                                <select name="po_status" id="po_status" class="select2 form-control" ></select>
                            </div>
                        </div>
                        <div class="row form-group">
                            <label class="col-md-2 col-form-label" for="description"> Description</label>
                        </div>
                        <div class="row form-group">
                           <div class="col-md-8">
                                <textarea id="description" name="description" rows="5" class="form-control"></textarea>
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
