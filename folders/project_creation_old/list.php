<?php 

include '../../config/dbconfig.php';

$work_location =$_SESSION['work_location'];
if($_SESSION['work_location'] !='All'){
// user
$company_name_options       = company_name();
$company_name_options       = select_option($company_name_options, "Select The Company");
}
else{
// admin
$company_name_options       = company_name();
$company_name_options       = select_option($company_name_options, "Select The Company");
}

if($_SESSION['work_location'] !='All'){
// user
$project_name_options       = project_name();
$project_name_options       = select_option($project_name_options, "Select The Project");
}
else{
// admin
$project_name_options       = project_name();
$project_name_options       = select_option($project_name_options, "Select The Project");
}


$application_type_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "CBG"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "COMPOST"
    ],
    3 => [
        "unique_id" => "3",
        "value"     => "CBG/COMPOST"
    ]
];
$application_type_options    = select_option($application_type_options,"Select Application",$application_type); 




if ($_GET['from_date'] == ''){
    $from_date = date("Y-m-01");
} else {
    $from_date = $_GET['from_date'];
}
if ($_GET['to_date'] == ''){
    $to_date = date("Y-m-d");
} else {
    $to_date = $_GET['to_date'];
}
?>





<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
  
                            <div class="col-md-12 ">
                                <div class="add_btn">
                                <?php echo btn_add($btn_add); ?>
                           </div>
                                </div> 
                                
                                
                                   <div class="col-md-2">
                                        <div class="form-group">
                                            <label>From Date</label>
                                            <input type="date" class="form-control" id='from_date' name='from_date' value='<?php echo $from_date; ?>'>
                                  </div>  </div> 
                                  
                                  <div class="col-md-2">
                                        <div class="form-group">
                                            <label>To Date</label>
                                            <input type="date" class="form-control" id='to_date' name='to_date' value='<?php echo $to_date; ?>'>
                                   </div>  </div> 
                                        <div class="col-md-2">
                                        <div class="form-group">
                                        <label>Company Name</label>
                                            <select class="select2 form-control" id="company_name" value="<?php echo $company_name ?>" ><?php echo $company_name_options; ?></select>
                                            </div> </div>
                                            <div class="col-md-2">
                                        <div class="form-group">
                                        <label>Project Name</label>
                                            <select class="select2 form-control" id="project_name" value="<?php echo $project_name ?>" ><?php echo $project_name_options; ?></select>
                                       </div> </div>
                                       <div class="col-md-2">
                                        <div class="form-group">
                                        <label>Application Type</label>
                                            <select class="select2 form-control" id="application_type" value="<?php echo $application_type ?>" ><?php echo $application_type_options; ?></select>
                                            </div> </div>
                                       <div class="col-md-2">
                                        <div class="form-group">
                                        <button type="button" class="btn btn-primary btn-rounded mt-3" onclick="project_filter();">Go</button>
                                    </div> 
                                    </div> 
               
                <table id="project_creation_datatable" class="table table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        
                        <tr>
                            <th>#</th>
                            <th>Company Name </th>
                            <th>Project Name </th>
                            <th>Client Name </th>
                            <th>Application Type</th>
                            <th>capacity </th>
                            <th>State </th>
                            <th>City </th>
                            <th>Contact Person </th>
                            <th>Contact Number </th>
                            <th>Files</th>
                            <th>Active Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                                            
                </table>

            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>    
                </div> </div>
                   
                </div>
<!-- end row-->