<?php
// Options
$category_options = select_option(expense_category(), "Select Category");
$company_name_options = select_option(company_name(), "Select");
$payment_type_options = select_option(payment_type(), "Select Payment Type");
$supplier_name_options = select_option(supplier(), "Select");
$customer_name_options = select_option(customers(), "Select");
$project_options = select_option(get_project_name(), "Select the Project Name");

// Default date range
if (empty($_GET['from_date'])) {
    $from_date = date("Y-m-01");
} else {
    $from_date = $_GET['from_date'];
}
if (empty($_GET['to_date'])) {
    $to_date = date("Y-m-d");
} else {
    $to_date = $_GET['to_date'];
}
$current_month = date('Y-m-d');

$type_options = select_option(doc_type_options(), "Select the Document Type", $doc_type);
?>

<div class="col-12 text-end mb-2">
  <div class="form-group row">
    <!--<div class="col-md-12">-->
    <!--  <?//php echo btn_add($btn_add); ?>-->
    <!--</div>-->
  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="row">                                    
          <div class="col-12">
            <div class="form-group row">
              <div class="col-md-2">
                <label class="col-form-label" for="from_date">From Date</label>
                <input type="date" class="form-control" id="from_date" name="from_date"
                       value="<?= $from_date ?>" max="<?= $current_month ?>">
              </div>
              <div class="col-md-2">
                <label class="col-form-label" for="to_date">To Date</label>
                <input type="date" class="form-control" id="to_date" name="to_date"
                       value="<?= $to_date ?>" max="<?= $current_month ?>">
              </div>
              <div class="col-md-2">
                <label class="col-form-label" for="company_name">Company Name</label>
                <select name="company_name" id="company_name" class="select2 form-control">
                  <?= $company_name_options ?>
                </select>
              </div>
              <div class="col-md-2">
                <label class="col-form-label" for="project_name">Project Name</label>
                <select name="project_name" id="project_name" class="select2 form-control">
                  <?= $project_options ?>
                </select>
              </div>
             <div class="col-md-2">
                  <label class="col-form-label" for="category_name">Category</label>
                  <select name="category_name" id="category_name" class="select2 form-control">
                    <?= $category_options ?>
                  </select>
                </div>
                
                <div class="col-md-2">
                  <label class="col-form-label" for="payment_type">Payment Type</label>
                  <select name="payment_type" id="payment_type" class="select2 form-control">
                    <?= $payment_type_options ?>
                  </select>
                </div>
              <div class="col-md-2">
                <label class="col-form-label" for="customer_name">Supplier</label>
                <select name="customer_name" id="customer_name" class="select2 form-control">
                  <?= $supplier_name_options ?>
                </select>
              </div>
              <div class="col-md-1 mt-4 mb-2">
                <button type="button" class="btn btn-primary btn-rounded"
                        onclick="expense_entry_filter();">Go</button>
              </div>
            </div>
          </div>
        </div>
        
        <!-- Document Upload Modal -->
<div class="modal fade" id="siUploadModal" tabindex="-1" role="dialog" aria-labelledby="siUploadModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title" id="siUploadModalLabel">Upload GRN Document</h5>
      </div>

      <div class="modal-body">
        <form class="was-validated documents_form" id="documents_form" enctype="multipart/form-data">
          <input type="hidden" name="upload_unique_id" id="upload_unique_id" value="">
          <input type="hidden" name="unique_id" id="unique_id" value="">
          
          <div class="row">
            <div class="col-12">
              <div class="form-group row mb-2">
                <label class="col-md-2 col-form-label" for="type">Type</label>
                <div class="col-md-4">
                  <select id="type" name="type" class="form-control">
                    <?php echo $type_options; ?>
                  </select>
                </div>

                <label class="col-md-2 col-form-label" for="test_file">Files (PAN, GST, etc.)</label>
                <div class="col-md-4">
                  <input type="file" multiple id="test_file" name="test_file[]" class="form-control dropify" data-allowed-file-extensions="pdf jpg jpeg png doc docx xls xlsx csv txt">
                </div>
              </div>

              <div class="form-group row mb-2 text-center">
                <div class="col">
                  <button type="button" class="btn btn-success waves-effect waves-light" onclick="documents_add_update()">Upload</button>
                </div>
              </div>

              <div class="row">
                <div class="col-12">
                  <table id="documents_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead>
                      <tr>
                        <th>#</th>
                        <th>Type</th>
                        <th>Document</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody></tbody>
                  </table>
                </div>
              </div>

            </div>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>

    </div>
  </div>
</div>

        

        <table id="expense_entry_datatable"
               class="table table-striped dt-responsive nowrap w-100">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Expense No</th>
              <th>Company Name</th>
              <th>Project Name</th>
              <th>Category</th>
              <th>Payment Type</th>
              <th>Customer</th>
              <th>Expense Date</th>
              <!--<th>Remarks</th>-->
              <th>View</th>
              <th>Print</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody></tbody>                                            
        </table>
      </div>
    </div>
  </div>
</div>
