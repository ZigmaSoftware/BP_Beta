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
                <div class="row mb-3">
    <div class="col-md-2">
        <label>From Date</label>
        <input type="date" id="from_date" class="form-control">
    </div>
    <div class="col-md-2">
        <label>To Date</label>
        <input type="date" id="to_date" class="form-control">
    </div>
    <div class="col-md-2">
        <label>Company</label>
        <select id="filter_company" class="form-control select2">
            <option value="">All</option>
            <?php echo select_option(company_name(),"All Companies",""); ?>
        </select>
    </div>
    <div class="col-md-2">
        <label>Project</label>
        <select id="filter_project" class="form-control select2">
            <option value="">All</option>
            <?php echo select_option(get_project_name(),"All Projects",""); ?>
        </select>
    </div>
    <div class="col-md-2">
        <label>Application Type</label>
        <select id="filter_application_type" class="form-control select2">
            <option value="">All</option>
            <option value="MRF">MRF</option>
            <option value="MSW">MSW</option>
            <option value="CBG">CBG</option>
            <option value="Composting">Composting</option>
        </select>
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button id="btn_filter" class="btn btn-primary">Go</button>
    </div>
</div>

                <table id="Integrated_daily_log_master_datatable" class="table
table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th> Company Name </th>
                            <th> Project Name </th>
                            <th> Type  </th>
                            <th> Active Status</th>
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