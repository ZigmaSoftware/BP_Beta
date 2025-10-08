<link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />


<?php

include '../../config/dbconfig.php';
// Retrieve unique ID and screen unique ID from the URL
$unique_id = $_REQUEST['unique_id'];


$status = "Closed"; 
$remark = ""; 
$date_time = date("Y-m-d H:i:s");

$conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if a record with the given screen_unique_id exists
$sql_check = "SELECT * FROM stage_1 WHERE unique_id = '$unique_id'";
$result_check = $conn->query($sql_check);



if ($result_check->num_rows > 0) {
    // If a record exists, update it
    if(isset($_POST['submit'])){
        // Retrieve form data
        $status = $_POST['status'];
        $remark = $_POST['remark'];
        
       
        
        $sql = "UPDATE stage_1 SET call_updated_status = '$status', remark_status = '$remark', print_status = 1, date_time = '$date_time' WHERE unique_id = '$unique_id'";
        
        
        // if ($conn->query($sql) === TRUE) {
        //     closeWindow();
        //     // echo "Record updated successfully";
        // } else {
        //     echo "Error: " . $sql . "<br>" . $conn->error;
        // }
        
        if ($conn->query($sql) === TRUE) {
    echo "<script>window.close();</script>";
    // alert("hi");
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

        
        
        
    }
} 
// else {
//     // If no record exists, insert a new one
//     if(isset($_POST['submit'])){
//         // Retrieve form data
//         $status = $_POST['status'];
//         $remark = $_POST['remark'];
        
//         $sql = "INSERT INTO stage_1 (screen_unique_id, call_updated_status, remark_status, date_time) VALUES ('$unique_id', '$status', '$remark', '$date_time')";
        
//         if ($conn->query($sql) === TRUE) {
//             // echo "New record inserted successfully";
//         } else {
//             echo "Error: " . $sql . "<br>" . $conn->error;
//         }
//     }
// }

$conn->close();
?>
<style>
    .text_compl h4 {
    font-size: 15px;
    margin-bottom: 4px;
    color: #999;
}
</style>



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


if (isset($_GET["screen_unique_id"])) {
    if (!empty($_GET["screen_unique_id"])) {


        $screen_unique_id  = $_GET["screen_unique_id"];
        $where = [
            "screen_unique_id" => $screen_unique_id
        ];

        $table = "complaint_creation";

        $columns = [
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
        // print_r($result_values);
        // print_r($_SESSION);
        if ($result_values->status) {

            $result_values = $result_values->data[0];

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
// echo $stage_1_status;  

if($stage_1_status == "2"){

$status_options        = [
    "1" => [
        "unique_id" => "4",
        "value"     => "Reopen",
    ],

];
$status_options        = select_option($status_options, "Select", $stage_opt);
} else if ($_SESSION["user_id"] == '5ff562ed542d625323' || $_SESSION["user_id"] == '6607e79d0c9c927739' || $_SESSION["sess_user_id"] == $result_values['assign_by']){
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
    // "2" => [
    //     "unique_id" => "2",
    //     "value"     => "Completed",
    // ],
    "2" => [
        "unique_id" => "2",
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

<div class="container">
    <div class="row">
        <div class="card mt-5">
  <div class="card-body">
                    <form method="post" action="">
                        
                        
                        <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            
                            <div class="col-md-3">
                             <div class="row">
                                <div class="col-md-12 text_compl">
                                    <h4>Complaint No  </h4>
                                </div>
                                <div class="col-md-12">
                                    <h5 style="color :#006fc4; font-weight: bold;font-size: 18px;margin: 6px 0px;"><?= $complaint_no; ?></h5>
                                </div>
                             </div>
                             <div class="row">
                            <div class="col-md-12 text_compl">
                                <h4>Complaint Date  </h4>
                            </div>
                            <div class="col-md-12">
                                <h5 style="color :#006fc4; font-weight: bold;font-size: 16px;margin: 6px 0px;"><?= today_time($entry_date); ?></h5>

                            </div>
                        </div>
                
                            </div>
                            
                             <div class="col-md-3">
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
                <hr/>    
                    <div class="row">
                                                <div class="col-sm-12">
                                                    <table id="status_sub_datatable" class="table" style="font-size:13px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 5%;">#</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20%;">Date / Time</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20%;">Staff Name</th>
                                                                
																<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20%;">Remark Type</th>
																 <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 30%;">Description</th>
																
																<th class="sorting_disabled" rowspan="1" colspan="1" style="width: 30%;">Tag Person</th>
                            
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="width: 20%">Status</th>     
                                                                
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                            	
											<?php
									include 'function.php';		
								
									$columns = [
										
										"@a:=@a+1 s_no",
                                        "date_time",
                                        "approve_by",
                                        "remark_type",
                                        "status_description",
                                        "user_name_select",
                                        // "doc_name",
                                        // "file_name",
                                        "status_option",
                                        " '' as call_status",
                                        "unique_id",
                                        "screen_unique_id",
                                        // "print_status"
									];
									$table_details = [
										"stage_1",
										$columns
									];

									$where = "screen_unique_id ='" . $screen_unique_id . "' AND unique_id ='" . $_REQUEST['unique_id'] . "' AND is_active = 1 AND is_delete = 0 ";



									$order_by = "";


									//$sql_function = "SQL_CALC_FOUND_ROWS";

									$result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
									 //print_r($result);
									$total_records = total_records();

									if ($result->status) {
										$res_array = $result->data;
										$sno = 1;

										foreach ($res_array as $key => $value) {
											switch ($value['status_option']) {
												case 1:
													$value['status_option'] = "In Progress";
													break;
												case 2:
													$value['status_option'] = "Completed";
													break;
												case 3:
													$value['status_option'] = "Cancel";
													break;
												case 4:
													$value['status_option'] = "Reopened";
													break;
											}

											

											$date = date_create($value['date_time']);
											$value['date_time'] = date_format($date, 'd-m-Y H:i:s');

											$value['approve_by']     = user_name($value['approve_by'])[0]['staff_name'];
											
											$value['remark_type']     = remark_type($value['remark_type'])[0]['remark'];
													
											$value['user_name_select']     = tag_person($value['user_name_select'])[0]['staff_name'];
													
											$value['file_name'] = image_view("stage_1", $value['unique_id'], $value['file_name'],$value['doc_name']); 
											switch ($value['doc_name']) {
						                        case 1:
						                            $value['doc_name'] = "Image";
						                            break;
						                        case 2:
						                            $value['doc_name'] = "Document";
						                            break;
						                        case 3:
						                            $value['doc_name'] = "Audio";
						                            break;
						                        case 4:
						                            $value['doc_name'] = "Chatbot";
						                            break;
						                    }
											?>


											<tr>
												<td><?= $sno++; ?></td>
												<td><?= $value['date_time']; ?></td>
												<td><?= $value['approve_by']; ?></td>
												<td><?= $value['remark_type']; ?></td>
												<td><?= $value['status_description']; ?></td>
												<td><?= $value['user_name_select']; ?></td>
												<td><?= $value['status_option']; ?></td>
											</tr>


									<?php    }
									}



									?>


                                                                
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                        
                                
                <h3 class=""><b>CALL STATUS</b></h3>
                <input type="hidden" class="form-control" name="date_time" required>
                
          
           
                <form method="post" action="" >
              
                        <div class="row ">
                            <div class="col-md-4">
                                <div class="form-group">
                        <label>Status</label>
                        <select class="select2 form-select" tabindex="8" name="status" id="status">
                            <option value="Closed" <?= ($status == 'Closed') ? 'selected' : ''; ?>>Closed</option>
                        </select>
                    </div> </div>
                          
                                 <div class="col-md-8">
                                <div class="form-group">
                                    <label>Remark</label>
                                    <textarea class="form-control" name="remark" maxlength="150" rows="2" required></textarea>
                                </div> </div>
                             
                                <div class="text-center mt-2 mb-2">
                                    <button type="submit" name="submit" id="submitBtn" class="btn btn-primary">Update</button>
                                </div>
                           
                        </div>
                   
                </form>
    
      </div> 
    
</div>    
    </div>
</div>