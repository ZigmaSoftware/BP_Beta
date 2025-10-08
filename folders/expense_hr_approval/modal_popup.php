<div id="expense_user_approval_main-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
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
                            <table id="expense_modal_datatable" class="table dt-responsive nowrap w-100">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Entry Date</th>
                                        <th>Expense No</th>
                                        <th>Branch Name / Staff Name</th>
                                        <th>Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                
                                </tbody>                                            
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
</script>