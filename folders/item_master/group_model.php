<!-- Full width modal content -->
<div id="group-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
   <div class="modal-dialog modal-full-width">
      <div class="modal-content">
         <div class="modal-header">
            <h3 class="text-primary" > Create Category </h3>
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
         </div>
         <div class="modal-body">
            <form class="was-validated category_form" name="category_form" id="category_form">
               <div class="row">
                  <div class="col-12">
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="category_name"> Category Name </label>
                        <div class="col-md-4">
                           <input type="text" id="cc_category_name" name="cc_category_name" class="form-control" value="" >
                        </div>
                        <label class="col-md-2 col-form-label" for="is_active"> Active Status</label>
                        <div class="col-md-4">
                           <select name="cc_is_active" id="cc_is_active" class="select2 form-control" required> <?php echo $active_status_options;?> </select>
                        </div>
                     </div>
                     <div class="form-group row ">
                        <label class="col-md-2 col-form-label" for="description"> Description</label>
                        <div class="col-md-4">
                           <textarea name="cc_description" id="cc_description" rows="5" class="form-control" > </textarea>
                        </div>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-asgreen btn-rounded waves-effect waves-light float-right categorycreate_btn " onclick="category_create('category_create')">Save</button>
            <button type="button" class="btn btn-danger btn-rounded" id="leads_modal_close" data-dismiss="modal">Close</button>
         </div>
      </div> <!-- /.modal-content -->
   </div> <!-- /.modal-dialog -->
</div> <!-- /.modal -->