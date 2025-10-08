<?php

$sess_user_type      = $_SESSION['sess_user_type'];
$executive_id        = $_SESSION["staff_id"];
if(isset($_GET['date'])){
    $date = $_GET['date'];
}else{
    $date = date('Y-m-d');
}

if (($sess_user_type != $admin_user_type) && ($sess_user_type != $hr_user_type)) {
    $executive_options = [];
    // $executive_options   = $data;
    $executive_options   = staff_name_bp($_SESSION["staff_id"])[0];
    //print_r($executive_options);
    $executive_options  = [[
        "unique_id" => $executive_options['unique_id'],
        "name"      => $executive_options['staff_name']
    ]];
    $executive_options   = select_option($executive_options, "Select Staff Name", $_SESSION["staff_id"]);
    $staff_check        = " disabled ";
} else {
    $executive_options  = staff_name_bp();
    $executive_options  = select_option($executive_options, "All");
    $staff_check        = "  ";
}
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated" autocomplete="off">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="entry_date"> Date</label>
                                <div class="col-md-3">
                                    <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo $date; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label" for="executive_name"> Employee Name </label>
                                <div class="col-md-3">
                                    <select name="executive_name" id="executive_name" class="select2 form-control " <?= $staff_check; ?>><?php echo $executive_options; ?>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="monthlyattendanceFilter();">Go</button>

                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="staff_location_tracking_report_datatable" class="table table-striped w-100 nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee Id</th>
                                <th>Employee Name</th>
                                <!--<th>Employee Id</th>-->
                                <th>Attendence Date</th>
                                <th>Attendence Time</th>
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