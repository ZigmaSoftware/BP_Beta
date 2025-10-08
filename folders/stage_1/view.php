<style>

    .form-label {
        margin: 10px 0px !important;
    }

    ul.list-inline.wizard.mb-0 {
        margin-top: 33px;
    }

    input[type="file"] {
        width: 100%;
    }

    .imgg {
        width: 139px;
        height: 84px;
    }

    .value_field {
        color: black;
        font-weight: bold;
    }

    .text_rt {
        text-align: right;
    }

    .text_gry h4 {
        color: #999;font-size: 15px;
    }
    .text_compl h4 {
    font-size: 15px;    margin-bottom: 4px;
    color: #999;
}
        .text_gry {margin-bottom: 10px;}
</style>
<!-- This file Only PHP Functions -->

<?php


// Form variables
$screen_unique_id = unique_id('CMP');
$btn_text = "Save";
$btn_action = "create";
$form_type = "Create";
$unique_id = "";
$user_type = "";
$status_options = "";
$remark_type_option = "";
$is_active = 1;

if (isset($_GET["unique_id"])) {
    if (!empty($_GET["unique_id"])) {

        $uni_dec = str_replace(" ", "+", $_GET['unique_id']);
        $get_uni_id           = openssl_decrypt(base64_decode($uni_dec), $enc_method, $enc_password, OPENSSL_RAW_DATA, $enc_iv);


        $unique_id  = $get_uni_id;
        $where = [
            "unique_id" => $unique_id
        ];

        $table = "complaint_creation";

        $columns = [
            "state_name",
            "site_name",
            "plant_name",
            "department_name",
            "complaint_category",
            "main_category",
            "source_name",
            "assign_by",
            "complaint_description",
            "screen_unique_id",
            "unique_id",
            "complaint_no",
            "entry_date",
            "(SELECT designation_id from user where user.unique_id = complaint_creation.assign_by) as designation_name",
            "stage_1_status",

        ];

        $table_details = [
            $table,
            $columns
        ];

        $result_values = $pdo->select($table_details, $where);

        if ($result_values->status) {

            $result_values = $result_values->data[0];

            $person_name            = $result_values["person_name"];
            $mobile_no              = $result_values["mobile_no"];
            $email_id               = $result_values["email_id"];
            $age                    = $result_values["age"];
            $state_name              = state_name_wise($result_values["state_name"])[0]['state_name'];
            $site_name              = site_name($result_values["site_name"])[0]['site_name'];
            $plant_name          = plant_name($result_values["plant_name"])[0]['plant'];
            $location_address       = $result_values["address"];
            $landmark               = $result_values["landmark"];
            $department_name        = department_type($result_values["department_name"])[0]['department_type'];
            $complaint_category     = category_creation($result_values["complaint_category"])[0]['category_name'];
            $main_category     = main_category 
                ($result_values["main_category"])[0]['category_name'];
            $source_name            = source_type($result_values["source_name"])[0]['source'];
            $complaint_description  = $result_values["complaint_description"];
            $screen_unique_id       = $result_values["screen_unique_id"];
            $unique_id              = $result_values["unique_id"];
            $complaint_no           = $result_values["complaint_no"];
            $designation_id           = $result_values["designation_name"];
            $designation_name           = designation_name($result_values["designation_name"])[0]['designation_name'];
            $assign_by              = disname(user_name($result_values['assign_by'])[0]['staff_name']);
            $entry_date             = $result_values["entry_date"];
            $user_id                = $_SESSION['user_id'];
            $staff_name             = user_name($_SESSION['user_id'])[0]['staff_name'];
            $stage_1_status         = $result_values['stage_1_status'];



            $form_type  = "Update";
            $btn_text   = "Update";
            $btn_action = "update";
        } else {
            $btn_text       = "Error";
            $btn_action     = "error";
            $is_btn_disable = "disabled='disabled'";
        }
    }
}
echo $stage_1_status;  

if($stage_1_status == "2"){

$status_options        = [
    "1" => [
        "unique_id" => "4",
        "value"     => "Reopen",
    ],

];
$status_options        = select_option($status_options, "Select", $stage_opt);
} 
else if ($_SESSION["user_id"] == '5ff562ed542d625323' || $_SESSION["user_type_unique_id"] == '6607e79d0c9c927739' || $_SESSION["sess_user_id"] == $result_values['assign_by']){
    $status_options        = [
    "1" => [
        "unique_id" => "1",
        "value"     => "Progressing",
    ],
    "2" => [
        "unique_id" => "2",
        "value"     => "Completed",
    ],
    "3" => [
        "unique_id" => "3",
        "value"     => "Cancel",
    ],

];
 $status_options        = select_option($status_options, "Select", $stage_opt);   
}
else{

$status_options        = [
    "1" => [
        "unique_id" => "1",
        "value"     => "Progressing",
    ],
    
    "2" => [
        "unique_id" => "3",
        "value"     => "Cancel",
    ],

];
$status_options        = select_option($status_options, "Select", $stage_opt);
}


$doc_options        = [
    "1" => [
        "unique_id" => "1",
        "value"     => "Image",
    ],
    "2" => [
        "unique_id" => "2",
        "value"     => "Document",
    ],
    "3" => [
        "unique_id" => "3",
        "value"     => "Audio",
    ],

];

$user_name_options = user_name();
$user_name_options =  select_option_user($user_name_options,"Select",$user_name);
$doc_options        = select_option($doc_options, "Select", $doc_opt);
$remark_type_option = remark_type();
$remark_type_option = select_option($remark_type_option, "Select", $remark_type);
?>
<!-- start page title -->
<div class="row">
    <div class="col-12">
        <div class="page-title-box">
            <h4 class="page-title"> Level 1 <?= $form_type; ?>
            </h4>
            <div>
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Tasks</a></li>
                    <li class="breadcrumb-item active">Level 1</li>
                </ol>
            </div>
        </div>
    </div>
</div>
<br>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <form class="was-validated" autocomplete="off">
                  <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            
                            <div class="col-md-3">
                             <div class="row">
                                <div class="col-md-12 text_compl">
                                    <h4>Task No  </h4>
                                </div>
                                <div class="col-md-12">
                                    <h5 style="color :#006fc4; font-weight: bold;font-size: 18px;margin: 6px 0px;"><?= $complaint_no; ?></h5>
                                </div>
                             </div>
                             <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4>Task Date  </h4>
                            </div>
                            <div class="col-md-12">
                                <h5 style="color :#006fc4; font-weight: bold;font-size: 16px;margin: 6px 0px;"><?= today_time($entry_date); ?></h5>

                            </div>
                        </div>
                
                            </div>
                            
                             <div class="col-md-3">
                        <!--    <div class="row">-->
                        <!--    <div class="col-md-12 text_compl">-->
                        <!--        <h4>State  </h4>-->
                        <!--    </div>-->
                        <!--    <div class="col-md-12">-->
                        <!--        <h5><?= $state_name; ?></h5>-->
                        <!--    </div>-->
                        <!--</div>-->
                        <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4>Department  </h4>
                            </div>
                            <div class="col-md-12">
                                <h5><?= disname($department_name); ?></h5>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4> Main Category /
                                Sub Category</h4>
                            </div>
                            <div class="col-md-12">
                                <h5><?= $main_category; ?> /
                                <?= $complaint_category; ?></h5>
                            </div>
                        </div>
                                 
                                 
                              </div>
                              
                            <div class="col-md-3">
                                <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4>Site  </h4>
                            </div>
                            <div class="col-md-12">
                                <h5><?= $site_name; ?></h5>
                            </div>
                        </div>
                        <div class="row">
                                <div class="col-md-12 text_compl">
                                    <h4>In charge  </h4>
                                </div>
                                <div class="col-md-12">
                                    <h5><?= $assign_by; ?></h5>
                                </div>
                            </div>
               
                            </div>
                             <div class="col-md-3">
                                 
                                                <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4>Designation  </h4>
                            </div>
                            <div class="col-md-12">
                                <h5><?= disname($designation_name); ?></h5>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4>Description  </h4>
                            </div>
                            <div class="col-md-12">
                                <h5><?= disname($complaint_description); ?></h5>
                            </div>
                        </div>
                                 
                                 </div>
                   
                        </div>
                    </div>
                    
                 
                    
                </div>
                
                <div class="col-md-12 mt-2 ms-2" style="text-align:center;">
			
                        <button class="btn btn-primary mb-2" style="font-size:14px;font-weight:bold;" onclick="new_external_window_print1(event,'folders/stage_1/print.php',unique_id.value);">
                                Task  Preview
                        </button>
			        </div>
            <hr/>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Date/Time </h4>
                                </div>
                                <div class="col-md-7">
                                    <h5><?php echo date("d-m-Y / H:i:s"); ?>
                                    </h5>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Status </h4>
                                </div>
                                <div class="col-md-7">

                                    <select class="select2 form-control" tabindex="8" name="status_option" id="status_option"><?= $status_options; ?></select>
                                </div>
                            </div>
                            
                             <div class="row">
                                   <input type="hidden" name="unique_id" id="unique_id" value="<?=$unique_id;?>">
                                <input type="hidden" name="screen_unique_id" id="screen_unique_id" value="<?=$screen_unique_id;?>">
                                <input type="hidden" name="complaint_no" id="complaint_no" value="<?=$complaint_no;?>">
                                <input type="hidden" name="entry_date" id="entry_date" value="<?=$entry_date;?>">
                                <div class="col-md-3 text_gry">
                                    <h4>Remarks Type </h4>
                                </div>
                                <div class="col-md-7"> <select class="select2 form-control form-select" tabindex="8" name="remark_type" id="remark_type">
                                    <?= $remark_type_option; ?></select>
                                </div>
                             </div>
                             
                             <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Description </h4>
                                </div>
                                <div class="col-md-7"> <textarea class="form-control mb-2" id="status_description" name="status_description"></textarea> </div>

                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Tag Name </h4>
                                </div>
                                <div class="col-md-7">

                                    <select name="user_name_select[]" <?=$user_name_select;?> id="user_name_select" class="select2 form-control" multiple >
                                    <?php echo $user_name_options; ?>
                                
                            </select>
                            <input type="hidden" id="user_name_select" name="user_name_select[]" class="form-control" value="<?php echo $user_name_select; ?>">
                            
                                </div>
                            </div>
                        </div>
                            <div class="col-md-6">
                             <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Document Type</h4>
                                </div>
                                <div class="col-md-7">

                                    <select class="select2 form-control form-select" tabindex="8" name="doc_option" id="doc_option"><?= $doc_options; ?></select>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Image Upload</h4>
                                </div>
                                <div class="col-md-7">  <input type="file" id="test_file_exp" multiple name="test_file_exp[]" class="form-control dropify" value="<?php echo $docs;?>"></div>

                            </div>
                            

                            <div class="row">
                                <div class="col-md-3 text_gry">
                                    <h4>Staff Name </h4>
                                </div>
                                <div class="col-md-7">
                                    <h5><?php echo disname($staff_name); ?></h5>
                                </div>
                            </div>
                             
                         </div>
                         
                         
                            <div class="row">
                                <div class="col-md-5 text_gry"> </div>
                                <div class="col-md-2">
                                    <button type="button" id="btn_add" class="btn btn-success btn-block status_sub_add_update_btn mb-2" onclick="status_sub_add_update()">Add</button>
                                </div>
                                <div class="col-md-5 text_gry"> </div>
                            </div>
                         
                    </div>
                    
                    
                                         <div class="row">
                                <div class="col-12">

                                    <input type='hidden' id='screen_unique_id' name="screen_unique_id" value='<?php echo $screen_unique_id; ?>'>
                                    <input type='hidden' id='unique_id' name="unique_id" value='<?php echo $unique_id; ?>'>

                                    <div class="row">
                                        <div class="col-md-12">

                                            <!-- Table Begiins -->
                                            <div id="status_sub_datatable_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">

                                                <div class="row">
                                                    <div class="col-sm-12">
                                                        <table id="status_sub_datatable" class="table table-bordered table-md dataTable no-footer mt-2 mb-2">
                                                            <thead>
                                                                <tr>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 5%;">#</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 10%;">Date / Time</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 10%;">Staff Name</th>
                                                                     <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 10%;">Remark Type</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 30%;">Description</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 10%;">Tag Name</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 10%;">File Name</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 5%;">File</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 30%">Status</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 30%">Call Status</th>
                                                                    <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 10%;">Action</th>
                                                                    <!--<h1><?php echo "Hello, World!"; ?></h1>-->
                                                                </tr>
                                                            </thead>
                                                            <tbody id='document_upload_sub_datatable'>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-sm-12 col-md-5"></div>
                                                    <div class="col-sm-12 col-md-7"></div>
                                                </div>
                                            </div>
                                            <!-- Table Ends -->

                                        </div>
                                    </div>
                                    <div class="form-group row mt-2">
                                        <div class="col-md-12" align="right">
                                            <!-- Cancel,save and update Buttons -->
                                            <?php echo btn_createupdate($folder_name_org, $unique_id, $btn_text); ?>
                                            <?php echo btn_cancel($btn_cancel); ?>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>

                    
     </form>
            <!-- end card-body -->
        </div>
        <!-- end card -->
    </div>
    <!-- end col -->
</div>
<!-- end page title -->

<script>
$(document).ready(function() {
    // Function to show/hide remarks field based on status
    function toggleRemarksBasedOnStatus() {
        var selectedStatus = $('#status_option').val(); // Get selected value
        
        // Assuming the value for the 'Complete' option is 'complete'
        if(selectedStatus === '2') {
            // Hide the remarks field row
            $('#remark_type').closest('.row').hide();
            $('#user_name_select').closest('.row').hide();
        } else {
            // Show the remarks field row
            $('#remark_type').closest('.row').show();
            $('#user_name_select').closest('.row').show();
        }
    }

    // Run once on document ready to ensure correct initial state
    toggleRemarksBasedOnStatus();

    // Bind the change event to the status dropdown
    $('#status_option').change(function() {
        toggleRemarksBasedOnStatus(); // Run every time selection changes
    });
});
</script>
