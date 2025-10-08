<?php 
$today = date('Y-m-d');
$from_date  = date('Y-m-01');            
$to_date    = date('Y-m-t'); 
?>


<div class="col-md-12 add_btn">
    <?php echo btn_add($btn_add); ?>
</div>

<div class="row mb-3">

  <div class="col-md-3">
        <label for="filter_from_date">From Date</label>
        <input type="date" id="filter_from_date" class="form-control"  value="<?php echo $from_date; ?>">

    </div>
    <div class="col-md-3">
        <label for="filter_to_date">To Date</label>
        <input type="date" id="filter_to_date" class="form-control" value="<?php echo $to_date; ?>">
    </div>
    
    <div class="col-md-3">
        <label for="filter_project_id" class="form-label">Project Name</label>
        <select id="filter_project_id" class="form-control">
            <option value="">All Projects</option>
            <?php
            $project_options  = get_project_by_type('generation');  
            if ($project_options && is_array($project_options)) {
                foreach ($project_options as $proj) {
                    echo '<option value="'.$proj['unique_id'].'">'.$proj['label'].'</option>';
                }
            }
            ?>
        </select>
    </div>


    <div class="col-md-2 mt-3">
        <button type="button" id="btnFilter" class="btn btn-primary">Go</button>
        <button type="button" id="btnReport" class="btn btn-success">Report</button>
    </div>
    
    
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                
                <table id="mandi_gobindgad_log_datatable" class="table dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Entry Date</th>
                            <th>Project Name</th>
                            <th>Waste Received (kg)</th>
                            <th>Waste Reject (kg)</th>
                            <th>Feed to Digester (kg)</th>
                            <th>Black Water (L)</th>
                            <th>Water (L)</th>
                            <th>Feeding pH</th>
                            <th>Outlet pH</th>
                            <th>Flowmeter Start</th>
                            <th>Flowmeter Stop</th>
                            <th>Gen set Start Hrs</th>
                            <th>Gen set Stop Hrs</th>
                            <th>Start KWH</th>
                            <th>Stop KWH</th>
                            <th>Remarks</th>
                            <th>Action</th>
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
