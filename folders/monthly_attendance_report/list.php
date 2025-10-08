<?php

if(isset($_GET['date'])){
    $date = $_GET['date'];
}else{
    $date = date('Y-m-d');
}
if(isset($_GET['to_date'])){
    $to_date = $_GET['to_date'];
}else{
    $to_date = date('Y-m-d');
}
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" > 
                    <div class="row">                                    
                        <div class="col-12">
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="entry_date"> Attendance On</label>
                                    <div class="col-md-3">
                                        <input type="date" name="entry_date" id="entry_date" class="form-control" value="<?php echo $date; ?>" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="date" name="to_date" id="to_date" class="form-control" value="<?php echo $to_date; ?>" required>
                                    </div>
                                <div class="col-md-3 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="daydattendanceFilter();">Go</button>
                                    <!--<button type="button" class="btn btn-primary  btn-rounded mr-3" onclick="mail_send(entry_date.value);">Send Mail</button>-->
                                    <!--<button type="button" class="btn btn-primary  btn-rounded mr-3" onclick="enter_attendance(entry_date.value);">Make Attendance</button>-->
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--<table id="count_list" class="table table-bordered w-50 nowrap">-->
                        <!--<tr>-->
                        <!--    <td><b>Total Staffs</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="total_staff"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Full Day Leave</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="full_day_leave"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Half Day Leave</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="half_day_leave"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Work From Home</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="work_from_home"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Idle</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="idle"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>On Duty</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="on_duty"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Present With Permission</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="permission"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Present With Late</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="late"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Present Staffs</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="present_staff"></td>-->
                        <!--</tr>-->
                        <!--<tr>-->
                        <!--    <td><b>Absent Staffs</b></td>-->
                        <!--    <td align="right" style="font-weight : bold;" id="non_present_staff"></td>-->
                        <!--</tr>-->
                    <!--</table>   -->
                   
                    <div id="custom-accordion-one" class=" custom-accordion">
    <div class="card mb-1">
        <div class="card-header" id="headingPresentstaff">
            <h5 class="m-0">
                <a class="custom-accordion-title text-dark collapsed d-block"
                   data-toggle="collapse" href="#collapsePresentstaff"
                   aria-expanded="false" aria-controls="collapsePresentstaff"> Present Staff <span id="present_staff_table" style="font-weight: 700; font-size:16px" class="float-right"></span>
                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>
                </a>
            </h5>
        </div>
        <div id="collapsePresentstaff" class="collapse show" aria-labelledby="headingPresentstaff" data-parent="#custom-accordion-one">
            <div class="row">
                <div class="col-12">
                    <table id="present_staff_report_datatable" class="table table-bordered w-100 nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Date</th>
                                <th>EMP No</th>
                                <!--<th>Date</th>-->
                                <th>Name</th>
                                <th>Check In</th>
                                <th>Check Out</th>
                                <th>Total Working Time</th>
                                <!--<th>Current Status</th>-->
                                <!--<th>Reason</th>-->
                                <!--<th>Action</th>-->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody> 
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingLate">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseLate"-->
<!--                   aria-expanded="false" aria-controls="collapseLate"> Present With Late <span id="late_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseLate" class="collapse show" aria-labelledby="headingLate" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="late_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
<!--                                <th>Location</th>-->
<!--                                <th>Name</th>-->
<!--                                <th>Check In</th>-->
<!--                                <th>Check Out</th>-->
<!--                                <th>Total Working Time</th>-->
<!--                                <th>Current Status</th>-->
<!--                                <th>Action</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingPermission">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapsePermission"-->
<!--                   aria-expanded="false" aria-controls="collapsePermission"> Present With Permission <span id="permission_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapsePermission" class="collapse show" aria-labelledby="headingPermission" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="permission_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
<!--                                <th>Location</th>-->
<!--                                <th>Name</th>-->
<!--                                <th>Check In</th>-->
<!--                                <th>Check Out</th>-->
<!--                                <th>Total Working Time</th>-->
<!--                                <th>Current Status</th>-->
<!--                                <th>Action</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingOnduty">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseOnduty"-->
<!--                   aria-expanded="false" aria-controls="collapseOnduty"> On Duty <span id="on_duty_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseOnduty" class="collapse show" aria-labelledby="headingOnduty" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="onduty_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
<!--                                <th>Location</th>-->
<!--                                <th>Name</th>-->
<!--                                <th>Check In</th>-->
<!--                                <th>Check Out</th>-->
<!--                                <th>Total Working Time</th>-->
<!--                                <th>Current Status</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingWorkfromHome">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseWorkfromHome"-->
<!--                   aria-expanded="false" aria-controls="collapseWorkfromHome"> Work From Home <span id="work_from_home_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseWorkfromHome" class="collapse show" aria-labelledby="headingWorkfromHome" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="work_from_home_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
<!--                                <th>Location</th>-->
<!--                                <th>Name</th>-->
<!--                                <th>Check In</th>-->
<!--                                <th>Check Out</th>-->
<!--                                <th>Total Working Time</th>-->
<!--                                <th>Current Status</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingIdle">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseIdle"-->
<!--                   aria-expanded="false" aria-controls="collapseIdle"> Idle <span id="idle_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseIdle" class="collapse show" aria-labelledby="headingIdle" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="idle_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
<!--                                <th>Location</th>-->
<!--                                <th>Name</th>-->
<!--                                <th>Check In</th>-->
<!--                                <th>Check Out</th>-->
<!--                                <th>Total Working Time</th>-->
<!--                                <th>Current Status</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingFullday">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseFullday"-->
<!--                   aria-expanded="false" aria-controls="collapseFullday"> Full Day Leave <span id="full_day_leave_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseFullday" class="collapse show" aria-labelledby="headingFullday" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--<table id="fullday_leave_report_datatable" class="table table-bordered w-100 nowrap" style="display: none;">-->
<!--    <thead>-->
<!--        <tr>-->
<!--            <th>#</th>-->
<!--            <th>EMP No</th>-->
<!--            <th>Location</th>-->
<!--            <th>Name</th>-->
<!--            <th>Current Status</th>-->
<!--        </tr>-->
<!--    </thead>-->
<!--    <tbody>-->
<!--    </tbody> -->
<!--</table>-->

<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingHalfday">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseHalfday"-->
<!--                   aria-expanded="false" aria-controls="collapseHalfday"> Half Day Leave <span class="float-right" id="half_day_leave_table" style="font-weight: 700; font-size:16px"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseHalfday" class="collapse show" aria-labelledby="headingHalfday" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="halfday_leave_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
<!--                                <th>Location</th>-->
<!--                                <th>Name</th>-->
<!--                                <th>Check In</th>-->
<!--                                <th>Check Out</th>-->
<!--                                <th>Total Working Time</th>-->
<!--                                <th>Current Status</th>-->
<!--                                <th>Action</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

<!--<div id="custom-accordion-one" class=" custom-accordion">-->
<!--    <div class="card mb-1">-->
<!--        <div class="card-header" id="headingAbsentstaff">-->
<!--            <h5 class="m-0">-->
<!--                <a class="custom-accordion-title text-dark collapsed d-block"-->
<!--                   data-toggle="collapse" href="#collapseAbsentstaff"-->
<!--                   aria-expanded="false" aria-controls="collapseAbsentstaff"> Absent Staff <span id="non_present_staff_table" style="font-weight: 700; font-size:16px" class="float-right"></span>-->
<!--                    <i class="mdi mdi-chevron-down mr-1 text-primary accordion-arrow"></i>-->
<!--                </a>-->
<!--            </h5>-->
<!--        </div>-->
<!--        <div id="collapseAbsentstaff" class="collapse show" aria-labelledby="headingAbsentstaff" data-parent="#custom-accordion-one">-->
<!--            <div class="row">-->
<!--                <div class="col-12">-->
<!--                    <table id="absent_staff_report_datatable" class="table table-bordered w-100 nowrap">-->
<!--                        <thead>-->
<!--                            <tr>-->
<!--                                <th>#</th>-->
<!--                                <th>EMP No</th>-->
                                <!--<th>Location</th>-->
<!--                                <th>Name</th>-->
                                <!--<th>Name</th>-->
                                <!--<th>Name</th>-->
<!--                                <th>Current Status</th>-->
<!--                            </tr>-->
<!--                        </thead>-->
<!--                        <tbody>-->
<!--                        </tbody> -->
<!--                    </table>-->
<!--                </div>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

                </form>
                <div class="text-right ">
                    <a href="javascript:window.print()" class="btn btn-primary btn-rounded waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                </div>   
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->