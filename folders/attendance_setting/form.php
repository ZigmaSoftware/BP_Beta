

<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$attendance_shift_name     = "";
$working_time_from         = "";
$working_time_to           = "";
$permission_hrs            = "";
$late_hrs                  = "";
$attendance_shift_hr       = "";

if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "attendance_setting";

        $columns    = [
            "attendance_shift_name",
            "attendance_shift_hr",
            "working_time_from",
            "working_time_to",
            "late_hrs",
            "permission_hrs"
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $result_values  = $pdo->select($table_details,$where);

        if ($result_values->status) {

            $result_values      = $result_values->data;

            $attendance_shift_name      = $result_values[0]["attendance_shift_name"];
            $attendance_shift_hr        = $result_values[0]["attendance_shift_hr"];
            $working_time_from          = $result_values[0]["working_time_from"];
            $working_time_to            = $result_values[0]["working_time_to"];
            $late_hrs                   = $result_values[0]["late_hrs"];
            $permission_hrs             = $result_values[0]["permission_hrs"];

            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$type_options    = [
                        1 => [
                    "unique_id" => 1,
                    "value"     => "Late",
                       ],
                       2 => [
                    "unique_id" => 2,
                    "value"     => "Permission",
                       ],
                    ];
$type_options  = select_option($type_options,"Select");

$attendance_type_options    = [
                                    1 => [
                                "unique_id" => 1,
                                "value"     => "Permission",
                                   ],
                                   2 => [
                                "unique_id" => 2,
                                "value"     => "Half Day Leave",
                                   ],
                                   3 => [
                                "unique_id" => 3,
                                "value"     => "Full Day Leave",
                                   ],
                              ];
$attendance_type_options  = select_option($attendance_type_options,"Select");

$shift_hr_options         = [
                                    1 => [
                                "unique_id" => 1,
                                "value"     => "8",
                                   ],
                                   2 => [
                                "unique_id" => 2,
                                "value"     => "12",
                                   ],
                                   3 => [
                                "unique_id" => 3,
                                "value"     => "24",
                                   ],
                              ];
$shift_hr_options         = select_option($shift_hr_options,"Select",$attendance_shift_hr);


?>

<input type="hidden" id="unique_id" value="<?php echo $unique_id; ?>">
<input type="hidden" id="attendance_setting_unique_id" value="<?php echo $unique_id; ?>">

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <form class="was-validated"  autocomplete="off" >
                            <div class="form-group row ">
                                <label class="col-md-3 col-form-label textright" for="attendance_shift_name"> Attendance Shift Name </label>
                                <div class="col-md-3">
                                    <input type="text" id="attendance_shift_name" name="attendance_shift_name" class="form-control" value="<?php echo $attendance_shift_name; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="attendance_shift_hr"> Shift Hrs</label>
                                <div class="col-md-3">
                                    <select class="select2 form-control"  name="attendance_shift_hr" id="attendance_shift_hr" required ><?php echo $shift_hr_options;?> </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-3 col-form-label textright" for="working_time_from"> Working Time</label>
                                <div class="col-md-1">From
                                    <input type="time" id="working_time_from" name="working_time_from" class="form-control" value="<?php echo $working_time_from; ?>" required>
                                </div>
                                <div class="col-md-2">To
                                    <input type="time" id="working_time_to" name="working_time_to" class="form-control" value="<?php echo $working_time_to; ?>" required>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="late_hrs"> Late Hrs<br/>(Shift Starting Time)</label>
                                <div class="col-md-3">
                                    <input  type="text" pattern = "[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}" id="late_hrs" name="late_hrs" class="form-control" onkeyup="get_late_permission_time();" value="<?php echo $late_hrs; ?>" required>
                                    <input type="hidden" id="late_time" name="late_time" class="form-control" value="" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-3 col-form-label textright" for="permission_hrs"> Permission Hrs <br/>(Shift Starting Time)</label>
                                <div class="col-md-3 timeclass">
                                    <input type="text" pattern = "[0-2]{1}[0-9]{1}:[0-5]{1}[0-9]{1}" id="permission_hrs" name="permission_hrs" onkeyup="get_late_permission_time();" class="form-control" value="<?php echo $permission_hrs; ?>" required>
                                    <input type="hidden" id="permission_time" name="permission_time" class="form-control" value="" required>
                                </div>
                            </div>
                        </form>
<div class="accordion mb-3" id="customAccordion">

  <!-- Late / Permission Conditions -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingEight">
      <button class="accordion-button collapsed" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapseEleven"
              aria-expanded="false" aria-controls="collapseEleven">
        Late / Permission Conditions
      </button>
    </h2>
    <div id="collapseEleven" class="accordion-collapse collapse"
         aria-labelledby="headingEight" data-bs-parent="#customAccordion">
      <div class="accordion-body">
        <form class="was-validated" autocomplete="off">
          <div class="row ">
            <label class="col-md-2 col-form-label textright" for="ld_per_day">Late / Permission</label>
            <div class="col-md-3">
              <select class="select2 form-control" name="late_permission"
                      onchange="get_permission_leave_option(this.value)" id="late_permission">
                <?php echo $type_options;?>
              </select>
            </div>
            <label class="col-md-2 col-form-label textright" for="late_count">Count</label>
            <div class="col-md-3">
              <input type="text" class="form-control" onkeypress="number_only(event)"
                     name="late_count" id="late_count" value="">
            </div>
          </div>
          <div class="row">
            <label class="col-md-2 col-form-label textright" for="equality_status">Permission / Leave</label>
            <div class="col-md-3">
              <select class="select2 form-control" name="leave_permission" id="leave_permission">
                <?php echo $attendance_type_options;?>
              </select>
            </div>
            <label class="col-md-2 col-form-label textright" for="permission_count">Count</label>
            <div class="col-md-3">
              <input type="text" class="form-control" onkeypress="number_only(event)"
                     name="permission_count" id="permission_count" value="">
            </div>
          </div>
          <div class="row ">
            <div class="col text-center">
              <button type="button" class="btn btn-success late_permission_leave_add_update_btn"
                      onclick="late_permission_leave_add_update()">ADD</button>
            </div>
          </div>
          <div class="row">
            <div class="col-12">
              <table id="late_permission_leave_datatable"
                     class="table table-striped table-bordered w-100">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Late / Permission</th>
                    <th>Count</th>
                    <th>Permission / Leave</th>
                    <th>Count</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Leave Type -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingNine">
      <button class="accordion-button collapsed" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapseAb"
              aria-expanded="false" aria-controls="collapseAb">
        Leave Type
      </button>
    </h2>
    <div id="collapseAb" class="accordion-collapse collapse"
         aria-labelledby="headingNine" data-bs-parent="#customAccordion">
      <div class="accordion-body">
        <form class="was-validated" autocomplete="off">
          <div class="row ">
            <label class="col-md-2 col-form-label textright" for="leave_type">Leave Type</label>
            <div class="col-md-3">
              <input type="text" class="form-control" name="leave_type" id="leave_type" value="">
            </div>
            <label class="col-md-2 col-form-label textright" for="leave_days">Days</label>
            <div class="col-md-3">
              <input type="text" class="form-control" onkeypress="number_only(event)"
                     name="leave_days" id="leave_days" value="">
            </div>
            <div class="col-md-2 text-center">
              <button type="button" class="btn btn-success leave_type_add_update_btn"
                      onclick="leave_type_add_update()">ADD</button>
            </div>
          </div>
         
          <div class="row">
            <div class="col-12">
              <table id="leave_type_datatable"
                     class="table table-striped table-bordered w-100">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Leave Type</th>
                    <th>Leave Days</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Holidays -->
  <div class="accordion-item">
    <h2 class="accordion-header" id="headingCD">
      <button class="accordion-button collapsed" type="button"
              data-bs-toggle="collapse" data-bs-target="#collapseCD"
              aria-expanded="false" aria-controls="collapseCD">
        Holidays
      </button>
    </h2>
    <div id="collapseCD" class="accordion-collapse collapse"
         aria-labelledby="headingCD" data-bs-parent="#customAccordion">
      <div class="accordion-body">
        <form class="was-validated" autocomplete="off">
          <div class="row">
            <label class="col-md-2 col-form-label textright" for="holiday_date">Date</label>
            <div class="col-md-3">
              <input type="date" class="form-control"
                     name="holiday_date"
                     min="<?php echo date('Y-m-01'); ?>"
                     max="<?php echo date('Y-m-31'); ?>"
                     id="holiday_date"
                     value="<?php echo $today; ?>">
            </div>
            <label class="col-md-2 col-form-label textright" for="remarks">Remarks</label>
            <div class="col-md-3">
              <textarea name="remarks" id="remarks" class="form-control"></textarea>
            </div>
            <div class="col-md-2 text-end">
              <button type="button" class="btn btn-success holidays_add_update_btn"
                      onclick="holidays_add_update()">ADD</button>
            </div>
          </div>
       
          <div class="row">
            <div class="col-12">
              <table id="holidays_datatable"
                     class="table table-striped table-bordered w-100">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>Leave Type</th>
                    <th>Days</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

</div>


                       <div class="form-group row btn-action">
                            <div class="col-md-12">
                                <!-- Cancel,save and update Buttons -->
                               
                                <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                 <?php echo btn_cancel($btn_cancel);?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>