<style>

</style>

<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<?php 
// Form variables
$btn_text           = "Save";
$btn_action         = "create";
$is_btn_disable     = "";

$unique_id          = "";

$permission_check   = user_permission_ui();
// $permission_check   = "";

$user_type_options  = user_type();
$user_type_options  = select_option($user_type_options,"Select User Type","");

$main_screen_options= main_screen();
$main_screen_options= select_option($main_screen_options,"Select Main Screen","");

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="user_type"> User Type</label>
                                <div class="col-md-4">
                                    <select name="user_type" id="user_type" class="select2 form-control" required>
                                        <?php echo $user_type_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="main_screen"> Main Screen</label>
                                <div class="col-md-4">
                                    <select name="main_screen" id="main_screen" class="select2 form-control" required>
                                        <?php echo $main_screen_options;?>
                                    </select>
                                </div>
                            </div>
                    </div>
                    <div class="col-12">
                        <div class="card-box">
                        <?php echo $permission_check; ?>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="card-box">
                            <!-- <h4 class="header-title mb-4">Tabs Justified</h4> -->

                            <ul class="nav nav-pills navtab-bg nav-justified">
                                <li class="nav-item">
                                    <a href="#home1" data-toggle="tab" aria-expanded="true" class="nav-link active">
                                        Admin
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#profile1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        General Master
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#messages1" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Store Master
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#messages6" data-toggle="tab" aria-expanded="false" class="nav-link">
                                        Account Master
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane show active" id="home1">
                                    <!-- <div class="col-12"> -->
                                        <div class="card-box">            
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="nav flex-column nav-pills nav-pills-tab" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                                        <a class="nav-link show active mb-1" id="v-pills-home-tab" data-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                                            Main Screen</a>
                                                        <a class="nav-link mb-1" id="v-pills-profile-tab" data-toggle="pill" href="#v-pills-profile" role="tab" aria-controls="v-pills-profile" aria-selected="false">
                                                            Screen Sections</a>
                                                        <a class="nav-link mb-1" id="v-pills-messages-tab" data-toggle="pill" href="#v-pills-messages" role="tab" aria-controls="v-pills-messages" aria-selected="false">
                                                            User Screens</a>
                                                        <a class="nav-link mb-1" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                                            User Types</a>

                                                        <a class="nav-link mb-1" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                                        User Creations</a>

                                                        <a class="nav-link mb-1" id="v-pills-settings-tab" data-toggle="pill" href="#v-pills-settings" role="tab" aria-controls="v-pills-settings" aria-selected="false">
                                                        User Type Permission</a>
                                                    </div>
                                                </div> <!-- end col-->
                                                <div class="col-sm-9">
                                                    <div class="tab-content pt-0">
                                                        <div class="tab-pane fade active show" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                                                <ul class="screen-permission"><li>
                                                                    <input type="checkbox" id="5f8807ff00bf726601" name="user_actions" value="5f8807ff00bf726601" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8807ff00bf726601">Add</label></li><li>
                                                                    <input type="checkbox" id="5f88082b25ec031952" name="user_actions" value="5f88082b25ec031952" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88082b25ec031952">Update</label></li><li>
                                                                    <input type="checkbox" id="5f88083fd50a344823" name="user_actions" value="5f88083fd50a344823" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88083fd50a344823">List</label></li><li>
                                                                    <input type="checkbox" id="5f8808504271036738" name="user_actions" value="5f8808504271036738" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8808504271036738">Delete</label></li><li>
                                                                    <input type="checkbox" id="5f88085e1fcea66282" name="user_actions" value="5f88085e1fcea66282" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88085e1fcea66282">View</label></li>
                                                                </ul>
                                                        </div>
                                                        <div class="tab-pane fade" id="v-pills-profile" role="tabpanel" aria-labelledby="v-pills-profile-tab">
                                                        <ul class="screen-permission"><li>
                                                                    <input type="checkbox" id="5f8807ff00bf726601" name="user_actions" value="5f8807ff00bf726601" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8807ff00bf726601">Add</label></li><li>
                                                                    <input type="checkbox" id="5f88082b25ec031952" name="user_actions" value="5f88082b25ec031952" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88082b25ec031952">Update</label></li><li>
                                                                    <input type="checkbox" id="5f88083fd50a344823" name="user_actions" value="5f88083fd50a344823" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88083fd50a344823">List</label></li><li>
                                                                    <input type="checkbox" id="5f8808504271036738" name="user_actions" value="5f8808504271036738" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8808504271036738">Delete</label></li><li>
                                                                    <input type="checkbox" id="5f88085e1fcea66282" name="user_actions" value="5f88085e1fcea66282" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88085e1fcea66282">View</label></li>
                                                                </ul>
                                                        </div>
                                                        <div class="tab-pane fade" id="v-pills-messages" role="tabpanel" aria-labelledby="v-pills-messages-tab">
                                                        <ul class="screen-permission"><li>
                                                                    <input type="checkbox" id="5f8807ff00bf726601" name="user_actions" value="5f8807ff00bf726601" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8807ff00bf726601">Add</label></li><li>
                                                                    <input type="checkbox" id="5f88082b25ec031952" name="user_actions" value="5f88082b25ec031952" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88082b25ec031952">Update</label></li><li>
                                                                    <input type="checkbox" id="5f88083fd50a344823" name="user_actions" value="5f88083fd50a344823" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88083fd50a344823">List</label></li><li>
                                                                    <input type="checkbox" id="5f8808504271036738" name="user_actions" value="5f8808504271036738" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8808504271036738">Delete</label></li><li>
                                                                    <input type="checkbox" id="5f88085e1fcea66282" name="user_actions" value="5f88085e1fcea66282" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88085e1fcea66282">View</label></li>
                                                                </ul>
                                                        </div>
                                                        <div class="tab-pane fade" id="v-pills-settings" role="tabpanel" aria-labelledby="v-pills-settings-tab">
                                                        <ul class="screen-permission"><li>
                                                                    <input type="checkbox" id="5f8807ff00bf726601" name="user_actions" value="5f8807ff00bf726601" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8807ff00bf726601">Add</label></li><li>
                                                                    <input type="checkbox" id="5f88082b25ec031952" name="user_actions" value="5f88082b25ec031952" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88082b25ec031952">Update</label></li><li>
                                                                    <input type="checkbox" id="5f88083fd50a344823" name="user_actions" value="5f88083fd50a344823" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88083fd50a344823">List</label></li><li>
                                                                    <input type="checkbox" id="5f8808504271036738" name="user_actions" value="5f8808504271036738" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f8808504271036738">Delete</label></li><li>
                                                                    <input type="checkbox" id="5f88085e1fcea66282" name="user_actions" value="5f88085e1fcea66282" class="check_all 5f6203173b9d035003section 5f87eab4b0bdf30603screen">
                                                                    <label for="5f88085e1fcea66282">View</label></li>
                                                                </ul>
                                                        </div>
                                                    </div>
                                                </div> <!-- end col-->
                                            </div> <!-- end row-->                                       
                                        </div> <!-- end card-box-->
                                    <!-- </div> -->
                                </div>
                                <div class="tab-pane" id="profile1">
                                    <p>Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                    <p class="mb-0">Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                </div>
                                <div class="tab-pane" id="messages1">
                                    <p>Vakal text here dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor. Aenean massa. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Donec quam felis, ultricies nec, pellentesque eu, pretium quis, sem. Nulla consequat massa quis enim.</p>
                                    <p class="mb-0">Donec pede justo, fringilla vel, aliquet nec, vulputate eget, arcu. In enim justo, rhoncus ut, imperdiet a, venenatis vitae, justo. Nullam dictum felis eu pede mollis pretium. Integer tincidunt.Cras dapibus. Vivamus elementum semper nisi. Aenean vulputate eleifend tellus. Aenean leo ligula, porttitor eu, consequat vitae, eleifend ac, enim.</p>
                                </div>
                            </div>
                        </div> <!-- end card-box-->
                    </div>                                  
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <!-- <div class="form-group row ">
                            <label class="col-md-2 col-form-label" for="screen_name"> User Type</label>
                                <div class="col-md-4">
                                    <input type="text" id="screen_name" name="screen_name" class="form-control" value="<?php echo ""; ?>" required>
                                </div>
                            </div> -->

                            <!-- All UI Appends Here -->
                            <div class="form-group row ">
                                <ul class="ks-cboxtags permission">
                                    
                                </ul>
                            </div>

                            <div class="row">
                                <div class="col-12">
                                    <!-- Table Begiins -->
                                    <table id="user_permission_datatable" class="table dt-responsive nowrap w-100">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Screen Name</th>
                                                <th>All</th>
                                                <th>Add</th>
                                                <th>Update</th>
                                                <th>List</th>
                                                <th>View</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>Main Screen</td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>1</td>
                                                <td>Main Screen</td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="toggle-button toggle-button--tuuli">
                                                        <input id="test" type="checkbox">
                                                        <label for="test"></label>
                                                        <div class="toggle-button__icon"></div>
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>                                            
                                    </table>
                                    <!-- Table Ends -->
                                </div>
                            </div>
                            
                            <div class="form-group row ">
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