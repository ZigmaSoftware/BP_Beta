<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <div class="form-group row add_btn">
                            <div class="col-md-12">
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
<div class="row mb-3">
  <div class="col-md-2">
    <label class="col-form-label">From Date</label>
    <input type="date" id="flt_from_date" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="col-form-label">To Date</label>
    <input type="date" id="flt_to_date" class="form-control">
  </div>
  <div class="col-md-3">
    <label class="col-form-label">Company</label>
    <select id="flt_company" class="form-control select2">
      <?php echo select_option(company_name(), "All Companies", ""); ?>
    </select>
  </div>
  <div class="col-md-3">
    <label class="col-form-label">Project</label>
    <select id="flt_project" class="form-control select2">
      <option value="">All Projects</option>
    </select>
  </div>
  <div class="col-md-2">
    <label class="col-form-label">Application Type</label>
    <select id="flt_app_type" class="form-control select2">
      <option value="">All Types</option>
    </select>
  </div>

  <div class="col-md-12 mt-3">
    <button id="flt_go" class="btn btn-primary">Go</button>
      <button id="flt_report" class="btn btn-info">Report</button>

  </div>
</div>

                <table id="Integrated_daily_log_entry_master_datatable" class="table
table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th> Company Name </th>
                            <th> Project Name </th>
                            <th> Type  </th>
                            <th> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                                            
                </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->