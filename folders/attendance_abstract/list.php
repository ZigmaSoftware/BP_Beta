<head>
<style>
    @media print {
  body * {
    visibility: hidden; /* hide everything */
  }
  #listing_div, #listing_div * {
    visibility: visible; /* show only table */
  }
  #listing_div {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
  /* Optional: hide the action buttons row */
  .btn, .col-md-7 {
    display: none !important;
  }
}
</style>
</head>
<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form class="needs-validation" novalidate autocomplete="off">
          <div class="row">
            <div class="col-12">
              <div class="row mb-3 align-items-center">
                <label for="year_month" class="col-md-2 col-form-label">Month - Year</label>
                <div class="col-md-3">
                  <input 
                    type="month" 
                    name="year_month" 
                    id="year_month" 
                    class="form-control" 
                    value="<?php echo date('Y-m') ?>" 
                    required>
                  <div class="invalid-feedback">Please select a month.</div>
                </div>

                <div class="col-md-7 d-flex justify-content-start gap-2">
                  <button type="button" class="btn btn-primary rounded-pill" onclick="attendnce_summary();">
                    Go
                  </button>

                  <button type="button" class="btn btn-primary rounded-pill" id="btn_print">
                  <i class="mdi mdi-printer me-1"></i> Print
                </button>


                 <button type="button" class="btn btn-success rounded-pill" onclick="exportAttendanceToExcel()">
                <i class="fas fa-file-excel me-1"></i> Excel
                </button>

                 </div>
              </div>
            </div>
          </div>

          <div id="listing_div">
            <?php include 'table_listing.php'; ?>
          </div>
        </form>
      </div> <!-- end card-body -->
    </div> <!-- end card -->
  </div> <!-- end col -->
</div>
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
