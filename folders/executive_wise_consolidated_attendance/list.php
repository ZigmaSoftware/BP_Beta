
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" > 
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-1 col-form-label" for="year_month"> Month - Year   </label>
                                    <div class="col-md-2">
                                        <input type="month" name="year_month" id="year_month" class="form-control" value="<?php echo date('Y-m') ?>" required>
                                    </div>
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="consolidatedattendanceFilter();">Go</button>
                                </div>
                            </div>
                        </div>
                    </div>
                       
                    <table id="executive_wise_consolidate_report_datatable" class="table table-striped w-100 nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Emp. No</th>
                                <th>Location</th>
                                <th>DOJ</th>
                                <th>Emp. Name</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Total Days</th>
                                <th>Sundays</th>
                                <th>Holidays</th>
                                <th>Wrk Days</th>
                                <th>WFH</th>
                                <th>SPL Leave</th>
                                <th>CL</th>
                                <th>Late</th>
                                <th>Permission</th>
                                <th>Leave <br>Permission</th>
                                <th>Emergency<br>Leave</th>
                                <th>Absent</th>
                                <th>Present</th>
                                <th>LOP</th>
                                <th>Salary Days</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody> 
                    </table>   
                </form>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->