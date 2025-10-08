    <link href="../assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-default-stylesheet" />
<style>
body{background-color:#fff;}
.contn_info h5 {
    color: #888;
    font-size: 13px;
    margin-bottom: 6px;
}
.contn_info h4 {
    margin-top: 4px;
    margin-bottom: 14px;
    font-size: 13.5px;
}
h3.box_border {
    border: 1px solid #eee;
    padding: 10px;
    background-color: #ebebeb;
    font-size: 15px;
    text-transform: uppercase;
    font-weight: bold;
}
h6.com_des {
    line-height: 20px;
    font-size: 13px;
}
.contn_info.bor_btm {
    border-bottom: 1px solid #eee;
}
.table>:not(caption)>*>* {
    padding: 4px 4px !important;
}
tr {
    vertical-align: middle;
}
table#status_sub_datatable tr th {
    background-color: #e4e4e4;
}
hr{margin-bottom:auto;}
.state_recom3 {
    border: 1px solid #ddd;
    padding: 10px;
}
.state_recom{ margin-top:10px;border: 1px solid #ddd;
    padding: 10px;}
</style>
    <?php
	include '../../config/dbconfig.php';
	include 'function.php';

	if (isset($_GET["unique_id"])) {
		if (!empty($_GET["unique_id"])) {

			$unique_id = $_GET["unique_id"];
			$where = [
				"unique_id" => $unique_id
			];

			$table = "complaint_creation";

			$columns = [
				"state_name",
				"site_name",
				"plant_name",
				"shift_name",               
                "problem_type",          
                "priority_type",
				"address",
				"landmark",
				"department_name",
				"main_category",
				"complaint_category",
				"source_name",
				"assign_by",
				"complaint_description",
				"screen_unique_id",
				"unique_id",
				"complaint_no",
				"entry_date",
				"(SELECT designation_id from user where user.unique_id = complaint_creation.assign_by) as designation_name",
				"stage_1_status",
				"stage_1_update_date",
                "(select level from view_level_all_departments where unique_id= complaint_creation.unique_id) as stage"
			];

			$table_details = [
				$table,
				$columns
			];

			$result_values = $pdo->select($table_details, $where);
// 			print_r($result_values);

			if ($result_values->status) {

				$result_values = $result_values->data[0];

				$shift_name             = shift_name($result_values["shift_type"])[0]['shift_type'];
				$problem_type             = problem_type($result_values["problem_type"])[0]['problem_type'];
				$priority_type            = priority_type($result_values["priority_type"])[0]['priority_name'];
				$state_name              = state_name($result_values["state_name"])[0]['state_name'];
				$plant_name          = plant_name($result_values["plant_name"])[0]['plant_name'];
				$site_name              = site_name ($result_values["site_name"])[0]['site_name'];
				$location_address       = $result_values["address"];
				$landmark               = $result_values["landmark"];
				$department_name        = department_type($result_values["department_name"])[0]['department_type'];
				$main_category     = main_category($result_values["main_category"])[0]['category_name'];
				$complaint_category     = category_creation($result_values["complaint_category"])[0]['category_name'];
				$source_name            = source_type($result_values["source"])[0]['source'];
				$complaint_description  = $result_values["complaint_description"];
				$screen_unique_id       = $result_values["screen_unique_id"];
				$unique_id              = $result_values["unique_id"];
				$complaint_no           = $result_values["complaint_no"];
				$designation_id           = $result_values["designation_name"];
				$designation_name           = designation_name($result_values["designation_name"])[0]['designation_name'];
                $stage       = $result_values["stage"];
				$assign_by              = disname(user_name($result_values['assign_by'])[0]['staff_name']);
				$assign_by_mobile              = disname(user_name($result_values['assign_by'])[0]['mobile_no']);
				$ass_email_id           = disname(user_name($result_values['assign_by'])[0]['email_id']);
				$entry_date             = disdate($result_values["entry_date"]);
				$user_id                = $_SESSION['user_id'];
				$staff_name             = user_name($_SESSION['user_id'])[0]['staff_name'];
				$comp_status            = $result_values['stage_1_status'];
			
				switch ($comp_status) {
					case 1:
						$complaint_status = "In Progress";
						$completed_date   = "Not Mentioned";
						break;
					case 2:
						$complaint_status = "Completed";
						$date = date_create($result_values['stage_1_update_date']);
						$completed_date   = date_format($date,'d-m-Y');
						break;
					case 3:
						$complaint_status = "Cancel";
						$completed_date   = "Not Mentioned";
						break;
					default:
						$complaint_status = "Pending";
						$completed_date   = "Not Mentioned";
						break;
				}

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
	?>

    <div class="container-fluid pe-4 ps-4 pt-1" style="background-color:#fff;">
    	<div class="compl_print pt-2">
    		 <div class="row">
                <div style="display: flex; align-items: center; justify-content: center;">
                    <img src="../../assets/images/logo1.png" width="80px" style="margin-right: 10px;" />
            <div>
                <h2 style="margin:0px 0px 10px 0px;font-size:20px;"><b>Zigma Global Environ Solutions Private Limited</b></h2>
                <h5 style="margin: 0;">No. 178, Indu nagar, Palayapalayam, Perundurai Road, Erode 638 011.</h5>
                <h5>0424 2225157  &nbsp;&nbsp; | &nbsp;&nbsp;connect@zigma.in  &nbsp;&nbsp;| &nbsp;&nbsp;www.zigma.in</h5>
            </div>
        </div>
    </div>
    
    
    		<div class="state_boxbor mt-2">
    			<div class="row">
    				<div class="col-md-12" style="">
    					<div class="state_recom3">
    					    <div class="row">
    						<div class="col-md-12">
    							<h3 class="box_border mt-0">Location Information</h3>
    						
    							<div class="row">
    						    <div class="col-md-3 contn_info">
    								<div class="col-md-12">
    									<h5>Task No</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?=$complaint_no; ?></h4>
    								</div>
    							</div>
    							 <div class="col-md-3 contn_info">
    								<div class="col-md-12">
    									<h5>Site</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?=$site_name; ?></h4>
    								</div>
    							</div>
    							 <div class="col-md-3 contn_info ">
    								<div class="col-md-12">
    									<h5>Plant</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?=$plant_name; ?></h4>
    								</div>
    							</div>
    							<div class="col-md-3 contn_info ">
    								<div class="col-md-12">
    									<h5>Shift</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?=$shift_name; ?></h4>
    								</div>
    							</div>
    							
    							<div class="col-md-3 contn_info">
    								
    								<div class="col-md-12 text-center">
    									<h3 style="color: #169737;padding: 6px;border: 1px solid #169737;"><b>LEVEL <?=$stage; ?></b></h3>
    								</div>
    							</div>
    							   
    							     
    							</div>
    						</div>
   
    					</div>
    						
    						
    						
    					<div class="col-md-12">
    						  <h3 class="box_border">Task Information</h3>
    						   
    						   	<div class="row">
    						   	  
    						   	    <div class="col-md-3 contn_info ">
    						    				<div class="col-md-12">
    									<h5>Department</h5>
    								</div>
									<div class="col-md-12">
    									<h4><?= $department_name; ?></h4>
    								</div>
    							</div>
    							<div class="col-md-3  contn_info ">
    								<div class="col-md-12">
    									<h5>Main Category</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?= $main_category; ?></h4>
    								</div>
    							</div>
    							<div class="col-md-3 contn_info">
    								<div class="col-md-12">
    									<h5>Task Category</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?= $complaint_category; ?></h4>
    								</div>
    							</div>
    							 <div class="col-md-3 contn_info ">
    								<div class="col-md-12">
    									<h5>Problem Type </h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?= $problem_type; ?></h4>
    								</div>
    							</div>
    						   	  		<hr/>	
    						   	    
    							<div class="col-md-3 contn_info">
    								<div class="col-md-12">
    									<h5>Impact Type</h5>
    								</div>
    								<div class="col-md-12">
    									<h4><?=$priority_type; ?></h4>
    								</div>
    							</div>
    							 <div class="col-md-3 contn_info ">
    								<div class="col-md-12">
    									<h5>Name</h5>
    								</div>
    								<div class="col-md-12 ">
    									<h4><?=$assign_by; ?></h4>
    								</div>
    							</div>
    							<div class="col-md-3 contn_info">
    								<div class="col-md-12">
    									<h5>Task Status</h5>
    									<h3 style="text-align:center;color: #169737;padding: 6px;border: 1px solid #169737;text-transform: uppercase;font-weight: bold;"><?= $complaint_status; ?></h3>
    									</div>	
    							</div>	

    						    </div>
    						</div>
    						
    					
    						 
    						     <div class="col-md-12">
    						         
    						         <h3 class="box_border">Task Description</h3>
    						         	<h6 class="com_des"><?=$complaint_description;?></h6>
    						     </div>
    						     
    						 
    						
				
    					</div>
    				</div>
    				
    				
    			</div>
    		</div>

    		<div class="row">
    			<div class="col-md-12" style="">
    				<div class="state_recom">
    				
    						<h3 class="box_border">User Attachments</h3>
    				
    						<div class="row">
							<?php
							$columns = [
								"@a:=@a+1 s_no",
								"doc_name",
								"file_name",
								"unique_id"
							];
							$table_details = [
								"complaint_creation_doc_upload",
								$columns
							];

							$where = "screen_unique_id ='". $screen_unique_id."' AND is_active = 1 AND is_delete = 0 ";
								
							
							$result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
        				//	print_r($result);
							if ($result->status) {

								$res_array = $result->data;
					
								foreach ($res_array as $key => $value) {
									$cfile_name = explode('.',$value['file_name']);

									// switch ($value['doc_name']) {
					                //     case 1:
					                //         $value['doc_name'] = "Image";
					                //         break;
					                //     case 2:
					                //         $value['doc_name'] = "Document";
					                //         break;
					                //     case 3:
					                //         $value['doc_name'] = "Audio";
					                //         break;
					                // }
									if($value['doc_name'] == "1"){
										$url = 'https://zigma.in/g_admin/uploads/complaint_category/image/'.$value['file_name'];?>
									
											<div style="width: 33.33%;padding: 10px;">
										<div class="form-check mb-1 form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="customckeck1" >
										<a href="javascript:print_view('image/<?=$value['file_name'];?>')"><img src="<?=$url;?>"  width="200px" ></a> </div></div>
									<?php }
									else if($value['doc_name'] == "2"){
										if(($cfile_name[1]=='xls')||($cfile_name[1]=='xlsx')) {
											$url = 'https://zigma.in/g_admin/assets/images/excel_icon.png'; ?>

											<div style="width: 33.33%;padding: 10px;">
												<div class="form-check mb-1 form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="customckeck1" >

											<a href="javascript:print_view('document/<?=$value['file_name'];?>')"><img src="<?=$url;?>"  height="43px" width="53px" >Task Details</a></div></div>
									<?php  }else{ 
										$url = 'https://zigma.in/g_admin/assets/images/pdf.png'; ?>
										<div style="width: 33.33%;padding: 10px;">
											<div class="form-check mb-1 form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="customckeck1" >
										<a href="javascript:print_view('document/<?=$value['file_name'];?>')"><img src="<?=$url;?>" width="100%" ></a> 
									</div></div>
										<?php }


									}

									else if($value['doc_name'] == "3"){
										$url = 'https://zigma.in/g_admin/uploads/complaint_category/audio/'.$value['file_name']; ?>
										<div style="width: 33.33%;padding: 10px;">
											<div class="form-check mb-1 form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="customckeck1" >
										<audio controls style="width: 100%;"> <source src="<?=$url;?>" type="audio/ogg"></audio></div> </div>
									<?php }
									else if($value['doc_name'] == "4"){
										$url = $value['file_name']?>
									
											<div style="width: 33.33%;padding: 10px;">
										<div class="form-check mb-1 form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="customckeck1" >
										<a href="#"><img src="<?=$url;?>"  width="100%" ></a> </div></div>
									<?php }
									else{ ?>
										<div style="width: 33.33%;padding: 10px;">
											<div class="form-check mb-1 form-check-primary">
                                                    <input class="form-check-input" type="checkbox" value="" id="customckeck1" >
                                                
										<img src='https://zigma.in/g_admin/folders/assets/images/images.jpg'>
										</div>
									</div>
									<?php }
							?>
    								
							<?php }
							} ?>


    					</div></div>
    			
    			</div>

    	
    		</div>
    	</div>
   

    <div class="row">
    			<div class="col-md-12" >
    				<div class="state_recom">
    			
    							<h3 class="box_border">Status Updates</h3>
    						 <div class="row">
                                                <div class="col-sm-12">
                                                    <table id="status_sub_datatable" class="table " style="font-size:12px;">
                                                        <thead>
                                                            <tr>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="">#</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="">Date / Time</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="">Staff Name</th>
                                                                
																<th class="sorting_disabled" rowspan="1" colspan="1" style="">Closed Images</th>

                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="">Description</th>
                                                                <th class="sorting_disabled" rowspan="1" colspan="1" style="">Status</th>     
                                                                
                                                                </tr>
                                                            </thead>
                                                            <tbody>

                                            	
											<?php
									$columns = [
										"@a:=@a+1 s_no",
										"date_time",
										"approve_by",
										"status_description",
										"status_option",
										"unique_id",
										"screen_unique_id",
										"file_name",
										"doc_name"
									];
									$table_details = [
										"stage_1",
										$columns
									];

									$where = "screen_unique_id ='" . $screen_unique_id . "' AND is_active = 1 AND is_delete = 0 ";

									$order_by = "";

									$sql_function = "SQL_CALC_FOUND_ROWS";

									$result = $pdo->select($table_details, $where, $limit, $start, $order_by, $sql_function);
									// print_r($result);
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
												<td><?= $value['file_name']; ?></td>
												<td><?= $value['status_description']; ?></td>
												<td><?= $value['status_option']; ?></td>
											</tr>


									<?php    }
									}

									?>

                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>

    						
    						
							
    				
    				</div>
    			</div>
    		</div>
    	</div>
    	 </div>
    </div>
    <script>
        function print_view(file_name)
    {
       onmouseover= window.open('../../../g_admin/uploads/complaint_category/'+file_name,'_blank','onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }  
     function print_view_1(file_name)
    {
       onmouseover= window.open('../../uploads/stage_1/'+file_name,'_blank','onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }  
    </script>