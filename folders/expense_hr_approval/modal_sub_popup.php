<div id="expense_hr_approval-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-full-width">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="text-primary" > Expense Details</h3>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <div class="modal-body">
           <form class="was-validated">
            <div class="row">                                    
                <div class="col-12">
                    <div class="form-group row ">
                        <label class="col-md-1 col-form-label" for="staff_name">Staff Name </label>
                        
                            <label class="col-md-4 col-form-label"><span name = "staff_name_modal" id="staff_name_modal" ></span></label>
                        
                        <label class="col-md-1 col-form-label" for="entry_date"> Entry Date </label>
                        <label class="col-md-2 col-form-label"><span name = "entry_date_modal" id="entry_date_modal" ></span></label>
                        <label class="col-md-2 col-form-label" for="total_amount">Total Amount</label>
                        <div class="col-md-2">
                            <label class="col-md-2 col-form-label"><span name = "total_amount_modal" id="total_amount_modal" style="font-weight: bold;font-size: 20px;"></span></label>
                        </div>
                    </div>
                </div>
            </div>
            <h5 class="text-primary" > Call Details</h5>
                <div class="row">                                    
                    <div class="col-12">
                            <table id="call_expense_sub_modal_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Call-ID</th>
                                        <th>Customer Name</th>
                                        <th>Travel Expense No</th>
                                        <th>Vehicle Type</th>
                                        <th>Image View</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                 
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td><label class="col-md-2 col-form-label"><span name = "call_amount_modal" id="call_amount_modal" ></span></label></td>
                                    </tr>
                                </tfoot>                                     
                            </table>

                    </div>
                </div>
                <h5 class="text-primary" > Other Details</h5>
                <div class="row">                                    
                    <div class="col-12">
                            <table id="expense_sub_modal_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Expense Type</th>
                                        <th>Image View</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td><b>Total</b></td>
                                        <td><label class="col-md-2 col-form-label"><span name = "expense_amount_modal" id="expense_amount_modal" ></span></label></td>
                                    </tr>
                                </tfoot>       
                            </table>
                    </div>
                </div>


                    <div class="row col-md-12 ">
                        <div class="col text-center">
                        	

                            <button type="button" class="btn btn-danger  btn-rounded" data-dismiss="modal">Close</button>
                        </div>
                </div>
            </form>
            </div>
            <div class="modal-footer">
           
            </div>
        </div>
      <!-- /.modal-content -->
    </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<script>
	
    
function print(file_name)
 {
    onmouseover= window.open('uploads/expense_creation/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
 }
 function print_call(file_name)
 {
    onmouseover= window.open('uploads/call/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
 }
</script>