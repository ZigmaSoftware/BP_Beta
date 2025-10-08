<?php
include 'function.php';
$sess_user_type      = $_SESSION['sess_user_type'];
// $executive_id        = $_SESSION["staff_id"];
$department_id        = $_SESSION["designation_id"];


    
        
        //  $executive_options   = staff_name_lead("",$executive_id);
         //print_r($executive_options);
         // $executive_options  =[[
         //    "unique_id" => $executive_options['unique_id'], 
         //    "name"      => $executive_options['staff_name']
            
        // $executive_options   = select_option($executive_options,"Select Staff Name"); 
        //  $staff_check        = "";
         $department_options   = designation_type_lead("",$department_id);
         $department_options   = select_option($department_options,"Select Department Name"); 
         $staff_check        = "";
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" > 
                    <div class="row">                                    
                        <div class="col-12">
                              <div class="form-group row ">
                                <!-- <label class="col-md-2 col-form-label" for="year_month"> Month - Year   </label>
                                <div class="col-md-2">
                                    <input type="month" name="year_month" id="year_month" class="form-control" value="<?php echo date('Y-m') ?>" required>
                                </div>
                                
                                
                                 <label class="col-md-2 col-form-label" for="executive_name"> Executive Name </label>
                                <div class="col-md-2">
                                    <select name="executive_name" id="executive_name" class="select2 form-control " <?=$staff_check;?> required ><?php echo $executive_options;?>
                                    </select>
                                </div> -->
                                <label class="col-md-2 col-form-label" for="executive_name"> Department </label>
                                <div class="col-md-2">
                                    <select name="department_name" id="department_name" class="select2 form-control " <?=$staff_check;?> required ><?php echo $department_options;?>
                                    </select>
                                </div>
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="staffaccountFilter();">Go</button>
                                    
                                </div>
                            </div>
                        </div>
                    </div>   
                    <table id="staff_accounts_report_datatable" class="table table-striped w-100 nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Name</th>
                                <th>Designation</th>
                                
                                <th>Phone No</th>
                                <th>Department </th>
                                <th>Dr</th>
                                <th>Cr</th>
                                <th>Balance</th>
                                <!-- <th>Location Tag</th> -->
                            </tr>
                        </thead>
                        <tbody>
                        </tbody> 
                        <!-- <tfoot>
                            <tr>
                                <td colspan="8" class="text-cener"><b> Attendance Details</b></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>Total days in Current Month</b></td>
                                <td id="current_month" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>Total Working day in Current Month</b></td>
                                <td id="working_days" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>No of Sunday </b></td>
                                <td id="no_of_sunday" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>No of Holiday </b></td>
                                <td id="no_of_holiday" class="text-right"></td>
                            </tr>

                            <tr>
                                <td colspan="8" class="text-right"><b>No of Leave</b></td>
                                <td id="no_of_leave" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>No of Emergency Leave</b></td>
                                <td id="no_of_emer_leave" class="text-right"></td>
                            </tr>
                             <tr>
                                <td colspan="8" class="text-right"><b>No of Absent </b></td>
                                <td id="no_of_absent" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>No of Comp Off </b></td>
                                <td id="no_of_comp_off" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>No of Late</b></td>
                                <td id="no_of_late" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>No of Permission</b></td>
                                <td id="no_of_permission" class="text-right"></td>
                            </tr>
                            <tr>
                                <td colspan="8" class="text-right"><b>Total Worked Days</b> </td>
                                <td id="total_worked_days" class="text-right"></td>
                            </tr>
                        </tfoot> -->
                    </table>   
                </form>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->