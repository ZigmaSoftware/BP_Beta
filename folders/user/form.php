<!-- This file Only PHP Functions -->
<?php include 'function.php'; ?>

<?php 
// ----------------------------------------------------------
// Variable Initialization
// ----------------------------------------------------------
$btn_text       = "Save";
$btn_action     = "create";
$is_btn_disable = "";
$unique_id      = "";

$name           = "";
$user_name      = "";
$password       = "";
$user_type      = "";
$is_active      = 1;
$phone_no       = "";
$address        = "";
$under_users    = "";
$exp_under_user = "";
$is_team_head   = "";
$team_id        = "";
$exp_team_users = "";
$device_id      = "";
$role           = "";
$exp_role       = [];
$work_location  = "";

// ----------------------------------------------------------
// Load Data If Editing Existing User
// ----------------------------------------------------------
if (isset($_GET["unique_id"]) && !empty($_GET["unique_id"])) {
    $unique_id  = $_GET["unique_id"];
    $where      = ["unique_id" => $unique_id];
    $table      = "user";
    $columns    = [
        "staff_unique_id", "user_type_unique_id", "phone_no", "user_name",
        "address", "is_active", "password", "under_user", "is_team_head",
        "team_id", "team_members", "device_id", "work_location", "role"
    ];

    $table_details = [$table, $columns];
    $user_values   = $pdo->select($table_details, $where);

    if ($user_values->status) {
        $u = $user_values->data[0];
        $user_type     = $u["user_type_unique_id"];
        $name          = $u["staff_unique_id"];
        $phone_no      = $u["phone_no"];
        $user_name     = $u["user_name"];
        $address       = $u["address"];
        $password      = $u["password"];
        $under_users   = $u["under_user"];
        $work_location = $u["work_location"];
        $is_active     = $u["is_active"];
        $is_team_head  = $u["is_team_head"] ? " checked " : "";
        $team_id       = $u["team_id"];
        $team_users    = $u["team_members"];
        $device_id     = $u["device_id"];
        $role          = $u["role"];
        $exp_role      = explode(",", $role);
        $exp_under_user = explode(",", $under_users);
        $exp_team_users = explode(",", $team_users);
        $exp_work_location = explode(",", $work_location);

        $btn_text   = "Update";
        $btn_action = "update";
    } else {
        $btn_text   = "Error";
        $btn_action = "error";
        $is_btn_disable = "disabled='disabled'";
    }
}

// ----------------------------------------------------------
// Dropdown Options
// ----------------------------------------------------------
$user_type_options  = select_option(user_type(), "Select User Type", $user_type);
$staff_options      = select_option(staff_name_bp(), "Select Staff", $name);
$under_user_options = select_option(under_user($user_name), "Select Users", $exp_under_user);
$team_user_options  = select_option(team_user($user_name), "Select Team Members", $exp_team_users);
$exp_work_location  = array_filter(array_map('trim', explode(",", $work_location)));
$work_location_options = select_option(get_project_name_all(), "All", $exp_work_location);
$active_status_options = active_status($is_active);
?>

<style>
  .info-icon {
    display: inline-block;
    background-color: #007bff;
    color: white;
    font-size: 12px;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    text-align: center;
    line-height: 18px;
    cursor: pointer;
    margin-left: 6px;
  }
  .checklist {
    position: absolute;
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    padding: 10px 30px;
    margin-top: 5px;
    font-size: 12px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    width: 250px;
    z-index: 1000;
    text-align: left;
  }
  .hidden { display: none; }
</style>

<div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <form class="was-validated" autocomplete="off">

          <!-- ===================================== -->
          <!-- Role Selection -->
          <!-- ===================================== -->
          <div class="form-group row">
            <label class="col-md-2 col-form-label textright">
              Role <span style="color:red">*</span>
            </label>
            <div class="col-md-3">
              <select id="role" name="role" class="form-control" onchange="toggleRoleFields(this.value)" required>
                <option value="">Select Role</option>
                <option value="In Role" <?php if ($role == 'In Role') echo 'selected'; ?>>In Role</option>
                <option value="Off Role" <?php if ($role == 'Off Role') echo 'selected'; ?>>Off Role</option>
              </select>
            </div>
          </div>

          <!-- ===================================== -->
          <!-- Staff Section (Shown only for In Role) -->
          <!-- ===================================== -->
          <div class="form-group row" id="staff_section">
            <label class="col-md-2 col-form-label textright" for="full_name">Staff Name <span style="color:red">*</span></label>
            <div class="col-md-3">
              <select name="full_name" id="full_name" class="select2 form-control" onchange="get_mobile_no(this.value)">
                <?php echo $staff_options; ?>
              </select>
            </div>

          </div>
        
         <!-- ===================================== -->
         <!-- User Name (always visible) -->
         <!-- ===================================== -->
         <div class="form-group row">
           <label class="col-md-2 col-form-label textright" for="user_name">User Name <span style="color:red">*</span></label>
           <div class="col-md-3">
             <input type="text" id="user_name" name="user_name" onkeyup="get_under_users(this.value)"
                    class="form-control" value="<?php echo $user_name; ?>" required>
           </div>
         </div>
         
          <!-- ===================================== -->
          <!-- Password Fields -->
          <!-- ===================================== -->
          <div class="form-group row">
            <label class="col-md-2 col-form-label textright" for="password">
              Password <span style="color:red">*</span>
              <span class="info-icon" onmouseover="showChecklist()" onmouseout="hideChecklist()">?</span>
              <ul class="checklist hidden" id="checklist">
                <li id="length">At least 8 characters</li>
                <li id="lower">Contains lowercase letter</li>
                <li id="upper">Contains uppercase letter</li>
                <li id="number">Contains number</li>
                <li id="special">Contains special character</li>
              </ul>
            </label>
            <div class="col-md-3">
              <div class="input-group input-group-merge">
                <input type="password" id="password" name="password" class="form-control"
                       value="<?php echo $password; ?>" onkeyup="validatePassword()" required>
                <div class="input-group-append" data-password="false">
                  <div class="input-group-text form-control" onclick="togglePasswords()">
                    <span class="password-eye" style="cursor:pointer;">️</span>
                  </div>
                </div>
              </div>
            </div>

            <label class="col-md-2 col-form-label textright" for="confirm_password">
              Confirm Password <span style="color:red">*</span>
            </label>
            <div class="col-md-3">
              <input type="password" id="confirm_password" name="confirm_password"
                     class="form-control" value="<?php echo $password; ?>" required>
            </div>
          </div>

          <!-- ===================================== -->
          <!-- User Type / Active Status -->
          <!-- ===================================== -->
          <div class="form-group row">
            <label class="col-md-2 col-form-label textright" for="user_type">User Type<span style="color:red">*</span></label>
            <div class="col-md-3">
              <select name="user_type" id="user_type" class="select2 form-control" required>
                <?php echo $user_type_options; ?>
              </select>
            </div>
            <label class="col-md-2 col-form-label textright" for="is_active">Active Status</label>
            <div class="col-md-3">
              <select name="is_active" id="is_active" class="select2 form-control" required>
                <?php echo $active_status_options; ?>
              </select>
            </div>
          </div>

          <!-- ===================================== -->
          <!-- Phone + Under Users -->
          <!-- ===================================== -->
          <div class="form-group row" id="phone_section">
            <label class="col-md-2 col-form-label textright" for="phone_no">Mobile No.</label>
            <div class="col-md-3">
              <input type="text" id="phone_no" name="phone_no" class="form-control"
                     onkeypress="number_only(event);" minlength="9" maxlength="12"
                     value="<?php echo $phone_no; ?>" readonly required>
            </div>
            <label class="col-md-2 col-form-label textright" for="under_user">Under Users</label>
            <div class="col-md-3">
              <select name="under_user_name" id="under_user_name" class="select2 form-control" multiple onChange="get_under_user_ids()">
                <?php echo $under_user_options; ?>
              </select>
              <input type="hidden" id="under_user" name="under_user" value="<?php echo $under_users; ?>">
            </div>
          </div>

          <!-- ===================================== -->
          <!-- Work Location -->
          <!-- ===================================== -->
          <div class="form-group row">
            <label class="col-md-2 col-form-label textright" for="work_location">Project</label>
            <div class="col-md-3">
              <select name="work_location[]" id="work_location" class="select2 form-control" multiple>
                <?php echo $work_location_options; ?>
              </select>
            </div>
          </div>

          <!-- ===================================== -->
          <!-- Team Head Section -->
          <!-- ===================================== -->
          <div class="form-group row">
            <label class="col-md-2 col-form-label textright" for="is_team_head">Check if user was Team Head</label>
            <div class="col-md-3">
              <div class="checkbox checkbox-info mt-2">
                <input id="is_team_head" type="checkbox" onchange="team_users_div(this.checked)" name="is_team_head"
                       value="1" <?php echo $is_team_head; ?>>
                <label for="is_team_head"></label>
              </div>
            </div>

            <label class="col-md-2 col-form-label textright" for="team_users">Team Members</label>
            <div class="col-md-3 team_users_class">
              <select name="team_users_name" id="team_users_name" class="select2 form-control" multiple onChange="get_team_users_ids()">
                <?php echo $team_user_options; ?>
              </select>
              <input type="hidden" id="team_users" name="team_users" value="<?php echo $team_users; ?>">
              <input type="hidden" id="team_id" name="team_id" value="<?php echo $team_id; ?>">
            </div>
          </div>

          <!-- ===================================== -->
          <!-- Buttons -->
          <!-- ===================================== -->
          <div class="form-group row btn-action">
            <div class="col-md-12">
              <?php echo btn_cancel($btn_cancel); ?>
              <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
            </div>
          </div>

        </form>
      </div>
    </div>
  </div>
</div>

