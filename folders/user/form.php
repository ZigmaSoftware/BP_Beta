<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$name               = "";
$user_name          = "";
$password           = "";
$user_type          = "";
$is_active          = 1;
$phone_no           = "";
$address            = "";
$under_users        = "";
$exp_under_user     = "";

$is_team_head       = "";
$team_id            = "";
$exp_team_users     = "";
$device_id          = "";

$role = "";
$exp_role = [];


// var_dump($_SESSION);
if(isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $unique_id  = $_GET["unique_id"];
        $where      = [
            "unique_id" => $unique_id
        ];

        $table      =  "user";

        $columns    = [
            "staff_unique_id",
            "user_type_unique_id",
            "phone_no",
            "user_name",
            "address",
            "is_active",
            "password",
            "under_user",
            "is_team_head",
            "team_id",
            "team_members",
            "device_id",
            "work_location",
            "role"   
        ];

        $table_details   = [
            $table,
            $columns
        ]; 

        $user_values  = $pdo->select($table_details,$where);

        if ($user_values->status) {

            $user_values     = $user_values->data;

            $user_type              = $user_values[0]["user_type_unique_id"];
            $name                   = $user_values[0]["staff_unique_id"];
            $phone_no               = $user_values[0]["phone_no"];
            $user_name              = $user_values[0]["user_name"];
            $address                = $user_values[0]["address"];
            $password               = $user_values[0]["password"];
            $under_users            = $user_values[0]["under_user"];
            $work_location          = $user_values[0]["work_location"];
            $is_active              = $user_values[0]["is_active"];
            $is_team_head           = $user_values[0]["is_team_head"];
            $team_id                = $user_values[0]["team_id"];
            $team_users             = $user_values[0]["team_members"];
            $device_id              = $user_values[0]["device_id"];
            $role                   = $user_values[0]["role"];
            $exp_role               = explode(",", $role);

            if ($is_team_head) {
                $is_team_head       = " checked ";
            }

            $exp_under_user     = explode(",", $under_users);
            $exp_team_users     = explode(",", $team_users);
            $exp_work_location     = explode(",", $work_location);
            
            $btn_text           = "Update";
            $btn_action         = "update";
        } else {
            $btn_text           = "Error";
            $btn_action         = "error";
            $is_btn_disable     = "disabled='disabled'";
        }
    }
}

$user_type_options  = user_type();
$user_type_options  = select_option($user_type_options,"Select User Type",$user_type); 

$staff_options      = staff_name_bp();
$staff_options      = select_option($staff_options,"Select Staff",$name); 

$under_user_options = under_user($user_name);
$under_user_options = select_option($under_user_options,"Select Users ",$exp_under_user);

$team_user_options  = team_user($user_name);
$team_users_options = select_option($team_user_options,"Select Team Members",$exp_team_users);
$work_location_options    = project_name();
$work_location_options    = select_option($work_location_options,"All",$exp_work_location);


$active_status_options= active_status($is_active);

?>
  <style>
    /*body {*/
    /*  font-family: Arial, sans-serif;*/
    /*  background: #f7f9fb;*/
    /*  padding: 40px;*/
    /*}*/

   

    /*.checklist {*/
    /*  margin: 10px 0;*/
    /*  list-style: none;*/
    /*  padding-left: 0;*/
    /*}*/

    /*.checklist li {*/
    /*  margin-bottom: 5px;*/
    /*  color: red;*/
    /*}*/

    /*.checklist li.valid {*/
    /*  color: green;*/
    /*}*/

    /*.strength-bar {*/
    /*  height: 10px;*/
    /*  width: 300px;*/
    /*  background: #e0e0e0;*/
    /*  border-radius: 5px;*/
    /*  overflow: hidden;*/
    /*  margin-top: 10px;*/
    /*}*/

    /*.strength-fill {*/
    /*  height: 10px;*/
    /*  width: 0%;*/
    /*  background: red;*/
    /*  transition: width 0.3s ease;*/
    /*}*/

    /*button {*/
    /*  margin-top: 15px;*/
    /*  padding: 10px 20px;*/
    /*  font-size: 16px;*/
    /*}*/

    /*button:disabled {*/
    /*  background: #ccc;*/
    /*  cursor: not-allowed;*/
    /*}*/

    /*.hidden {*/
    /*  display: none;*/
    /*}*/
  </style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                        
                            <div class="form-group row">
                            <label class="col-md-2 col-form-label textright"> Role <span style="color:red">*</span></label>
                            <div class="col-md-3 col-12">
                                <div class="radio radio-info mt-2">
                                    <input type="radio" id="in_role" name="role" value="In Role"
                                        <?php if(isset($role) && $role == "In Role") echo "checked"; ?>>
                                    <label for="in_role">In Role</label>
                                </div>
                                <div class="radio radio-info mt-2">
                                    <input type="radio" id="off_role" name="role" value="Off Role"
                                        <?php if(isset($role) && $role == "Off Role") echo "checked"; ?>>
                                    <label for="off_role">Off Role</label>
                                </div>
                            </div>
                        </div>


                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="full_name"> Staff Name <span style="color:red">*</span></label>
                                <div class="col-md-3">
                               <select name="full_name" id="full_name"  class="select2 form-control" onchange="get_mobile_no(this.value)" required>
                                        <?php echo $staff_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="user_name"> User Name <span style="color:red">*</span></label>
                                <div class="col-md-3">
                                    <input type="text" id="user_name" name="user_name" onkeyup="get_under_users(this.value)" class="form-control" value="<?php echo $user_name; ?>" required>
                                </div>
                            </div>
                            <div class="form-group row ">
                         <label class="col-md-2 col-form-label textright" for="password">
  Password <span style="color:red">*</span>
  <!-- Info icon with hover -->
  <span class="info-icon" onmouseover="showChecklist()" onmouseout="hideChecklist()" onkeydown="hideChecklist()">?</span>
  <!-- Checklist shown on hover -->
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

<!-- Styles -->
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
    background-color: #fefefe;
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

  .hidden {
    display: none;
  }
</style>

<!-- JavaScript -->
<script>
  function showChecklist() {
    document.getElementById("checklist").classList.remove("hidden");
  }

  function hideChecklist() {
    document.getElementById("checklist").classList.add("hidden");
  }
</script>

                                
                                <!--<div class="col-md-4">-->
                                <!--    <input type="password" id="password" name="password" class="form-control" value="<?php echo $password; ?>"  onkeyup="validatePassword()" required>-->
                                <!--</div>-->
                                
                            
                                <label class="col-md-2 col-form-label textright" for="confirm_password"> Confirm Password <span style="color:red">*</span></label>
                                 <div class="col-md-3">
                                <div class="input-group input-group-merge">
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control"  value="<?php echo $password; ?>" required>
                                    <div class="input-group-append" data-password="false">
                                        <div class="input-group-text form-control">
                                            <span class="password-eye"></span>
                                        </div>
                                    </div></div>
                                </div>
                                <!--<div class="col-md-4">-->
                                <!--    <input type="password" id="confirm_password" name="confirm_password" class="form-control"  value="<?php echo $password; ?>" required>-->
                                <!--</div>-->
                                
                            </div>
                            <div class="form-group row ">
                                <div class="col-md-12">
                                <ul class="checklist hidden" id="checklist">
                                    <li id="length">✅ At least 8 characters</li>
                                    <li id="lower">✅ Contains lowercase letter</li>
                                    <li id="upper">✅ Contains uppercase letter</li>
                                    <li id="number">✅ Contains number</li>
                                    <li id="special">✅ Contains special character</li>
                                </ul>
                            
                                <div class="strength-bar hidden" id="strength-bar">
                                    <div id="strength-fill" class="strength-fill"></div>
                                </div>
                               </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="user_type"> User Type<span style="color:red">*</span></label>
                                <div class="col-md-3">
                                    <select name="user_type" id="user_type" class="select2 form-control" required>
                                        <?php echo $user_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="is_active"> Active Status</label>
                                <div class="col-md-3">
                                <select name="is_active" id="is_active" class="select2 form-control" required>
                                        <?php echo $active_status_options;?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label textright" for="phone_no"> Mobile No.</label>
                                <div class="col-md-3">
                                    <input type="text" id="phone_no" name="phone_no" class="form-control" onkeypress="number_only(event);" minlength="9" maxlength="12" value="<?php echo $phone_no; ?>" readonly required>
                                </div>
                                <!-- <label class="col-md-2 col-form-label" for="address"> Address</label>
                                <div class="col-md-4">
                                    <textarea name="address" id="address" rows="5" class="form-control" required> <?php echo $address; ?></textarea>
                                </div> -->
                                <label class="col-md-2 col-form-label textright" for="under_user"> Under Users </label>
                                <div class="col-md-3">
                                     <select name="under_user_name" id="under_user_name" class="select2 form-control" onChange="get_under_user_ids()"  multiple >
                                        <?php echo $under_user_options;?>
                                    </select>
                                     <input type="hidden" id="under_user" name="under_user" class="form-control" value="<?php echo $under_users; ?>" >
                                </div>
                            </div>
                            <div class="form-group row ">
                           
                            <!--<label class="col-md-2 col-form-label textright" for="under_user"> Device ID </label>-->
                            <!--<div class="col-md-3">-->
                            <!--<input type="text" id="device_id" name="device_id" class="form-control"  value="<?php echo $device_id; ?>"  required>-->
                            <!--</div>-->
                            
                            
                            <label class="col-md-2 col-form-label textright" for="work_location"> Project </label>
                                <div class="col-md-3">
                                     <select name="work_location[]" id="work_location" class="select2 form-control" multiple>

                                        <?php echo $work_location_options;?>
                                    </select>
                                     <!--<input type="hidden" id="under_user" name="under_user" class="form-control" value="<?php echo $under_users; ?>" >-->
                                </div>
                        </div>

                            <div class="form-group row ">
                                 <!-- <label class="col-md-2 col-form-label" for="phone_no"> Image Upload</label>
                                <div class="col-md-4">
                                   
                                </div> -->
                                <label class="col-md-2 col-form-label textright" for="is_team_head"> Check if user was Team Head </label>
                                <div class="col-md-3 col-12">
                                    <div class="checkbox checkbox-info mt-2">
                                        <input id="is_team_head" type="checkbox" onchange="team_users_div(this.checked)" name="is_team_head" value="1" <?php echo $is_team_head; ?>>
                                        <label for="is_team_head">                            
                                        </label>
                                    </div>
                                </div>
                                <label class="col-md-2 col-form-label textright" for="team_users"> Team Members</label>
                                <div class="col-md-3 team_users_class">
                                     <select name="team_users_name" id="team_users_name" class="select2 form-control " onChange="get_team_users_ids()"  multiple >
                                        <?php echo $team_users_options;?>
                                    </select>
                                     <input type="hidden" id="team_users" name="team_users" class="form-control" value="<?php echo $team_users; ?>" >
                                     <input type="hidden" id="team_id" name="team_id" class="form-control" value="<?php echo $team_id; ?>" >
                                </div>

                            </div>
                            <div class="form-group row btn-action">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel);?>
                                    <?php echo btn_createupdate($folder_name_org,$unique_id,$btn_text);?>
                                </div>
                                
                            </div>
                    </div>
                </div>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  