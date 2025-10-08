
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" >
                <div class="row">                                    
                    <div class="col-12">
                            <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="call_id"> Call ID </label>
                                <div class="col-md-4">

                                        <h4 class="text-primary"><?php echo $follow_up_call_id; ?></h4>
                                        <input type="hidden" name="call_id" id="call_id" class="form-control" value="<?php echo $follow_up_call_id; ?>">
                                        <input type="hidden" name="call_unique_id" id="call_unique_id" class="form-control" value="<?php echo $unique_id; ?>">
                                        <input type="hidden" name="sub_unique_id" id="sub_unique_id" class="form-control" value="<?php echo $sub_unique_id; ?>">
                                    
                                </div>
                                <label class="col-md-2 col-form-label" for="follow_up_date"> Date </label>
                                <div class="col-md-4">

                                    <h4 class="text-info"><?php echo $today_dis; ?></h4>
                                    <input type="hidden" name="follow_up_date" class="form-control" id="follow_up_date" value="<?php echo $today; ?>">

                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-md-2 col-form-label" for="execute_name"> Executive Name </label>
                                <div class="col-md-4">
                                        <h4 class="text-primary"><?php echo $executive_name; ?></h4>
                                        <input type="hidden" name="executive_name" id="executive_name" value="<?php echo $executive_id; ?>">

                                </div>
                                <label class="col-md-2 col-form-label" for="location"> Location </label>
                                <div class="col-md-4">

                                        <h4 class="text-primary"><?php echo $location_name; ?></h4>
                                        <input type="hidden" name="location" id="location" class="form-control" value="<?php echo $location_id; ?>">

                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="call_type"> Call Type </label>
                                <div class="col-md-4">

                                        <h4 class="text-primary"><?php echo $call_type_name; ?></h4>
                                        <input type="hidden" name="call_type" id="call_type" class="form-control" value="<?php echo $call_type_id; ?>">

                                </div>
                                <label class="col-md-2 col-form-label" for="customer_id"> Customer ID / Name </label>
                                <div class="col-md-4">


                                        <h4 class="text-primary"><?php echo $customer_name; ?></h4>
                                        <input type="hidden" name="customer_id" id="customer_id" class="form-control" value="<?php echo $customer_id; ?>">

                                </div>
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="mode"> Mode </label>
                                <div class="col-md-4">

                                        <h4 class="text-primary"><?php echo $mode; ?></h4>
                                        <input type="hidden" name="mode" id="mode" class="form-control" value="<?php echo $mode; ?>">

                                    
                                </div>
                                <?php if ($unique_id) { ?>
                                    <ul class="ks-cboxtags">
                                        <li>
                                            <input type="checkbox" id="new_lead">
                                            <label for="new_lead">New Lead</label>
                                        </li>
                                    </ul>
                                    <!-- <label class="col-md-4 col-form-label text-info" for="add_new_lead">Add new Lead <i class="mdi mdi-account-plus-outline mdi-24px m-0 p-0"></i></label> -->
                                <?php } ?>
                                
                            </div>
                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="cur_status"> Status </label>
                                <div class="col-md-4">
                                                                    
                                        <textarea name="cur_status" id="cur_status" rows="5" class="form-control" required></textarea>
                                    
                                </div>
                                <label class="col-md-2 col-form-label" for="remark"> Remark </label>
                                <div class="col-md-4">

                                        <textarea name="remark" id="remark" rows="5" class="form-control" required></textarea>

                                </div>
                            </div>

                            <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="follow_up_action_type"> Action </label>
                                <div class="col-md-4">
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="next_follow_up_action" name="follow_up_action_type" class="custom-control-input" onclick="status_check();" value="1" <?php echo $action_type_next; ?> required>
                                        <label class="custom-control-label text-primary" for="next_follow_up_action">Next Follow Ups</label>
                                    </div>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" id="close_action" name="follow_up_action_type" class="custom-control-input" onclick="status_check();" value="0" <?php echo $action_type_close; ?> required>
                                        <label class="custom-control-label text-danger" for="close_action">Close</label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row next_follow_up_status d-none">
                                <label class="col-md-2 col-form-label" for="next_follow_up_days"> Next Follow Up </label>
                                <div class="col-md-2">
                                    <input type="number" name="next_follow_up_days" id="next_follow_up_days" onkeyup="date_count(this.value);" min = 1 class="form-control next_follow_up_status_inp" value="<?php echo $next_follow_up_days; ?>">
                                </div>
                                <div class="col-md-2 mt-3 mt-sm-0">
                                    <input type="date" name="next_follow_up_date" id="next_follow_up_date" class="form-control next_follow_up_status_inp" min = "<?php echo $today; ?>" onchange ="days_count(this.value);" value="<?php echo $next_follow_up_date; ?>">
                                </div>
                            </div>
                            <div class="form-group row close_status d-none">
                                <label class="col-md-2 col-form-label" for="call_status"> Call Status </label>
                                <div class="col-md-4">
                                    <select name="call_status" id="call_status" class="select2 form-control close_status_inp">
                                        <?=$close_call_options;?>
                                    </select>
                                </div>
                                <label class="col-md-2 col-form-label" for="country_name"> Close Date</label>
                                <div class="col-md-4">
                                    <!-- <h4 class="text-dark">18-11-2020</h4> -->
                                    <input type="date" name="close_date" id="close_date" class="form-control close_status_inp" value = "<?php echo $call_close_date; ?>">
                                </div>
                            </div>
                            <div class="form-group row close_status d-none">
                                <label class="col-md-2 col-form-label" for="close_remark"> Remarks </label>
                                <div class="col-md-4">
                                    <textarea name="close_remark" id="close_remark" rows="3" class="form-control close_status_inp" required><?=$call_close_remark;?></textarea>
                                </div>
                            </div>

                                
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <!-- Cancel,save and update Buttons -->
                                    <?php echo btn_cancel($btn_cancel);?>
                                    <?php echo btn_createupdate($folder_name_org,'',$btn_text,'','_sub_au',$btn_class);?>
                                </div>                                
                            </div>
                    </div>
                </div>

                <?php if ($unique_id) { ?>
                <div class="row">
                    <div class="col-12">
                        <!-- Table Begiins -->
                        <table id="follow_up_call_sub_datatable" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Entry Date</th>
                                    <th>Next Follow Up</th>
                                    <th>Remarks</th>
                                    <th>Status</th>
                                    <th>Call Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>                                            
                        </table>
                        <!-- Table Ends -->
                    </div>
                </div>
                <?php } ?>
                </form> 

            </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div><!-- end col -->
</div>  