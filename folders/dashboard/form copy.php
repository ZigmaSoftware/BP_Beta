<style>
h4 {
    color: #000;
}
</style>
<!-- This file Only PHP Functions -->
<?php include 'function.php';?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body" dir="ltr">
                <input type="month" name="dashboard_month" id="dashboard_month" class="form-control" value="<?php echo date('Y-m');?>" onchange="dashboard_month_follow_up_filter(),dashboard_month_leads_filter()">
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <!-- Portlet card -->
        <div class="card">
            <div class="card-body" dir="ltr">
                <div class="card-widgets">
                    <a href="index.php?file=call/list" class="text-success"><i class="mdi mdi-account-multiple-plus mdi-36px"></i></a>
                    <!-- <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="false" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                    <a href="javascript: void(0);" data-toggle="remove"><i class="mdi mdi-close"></i></a> -->
                </div>
                <h4 class="header-title mb-0"> Calls & Follow ups</h4>

                <div id="cardCollpase1" class="collapse pt-3 show">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-3">
                                <h3 class="text-danger"  id="new_calls_count" >0</h3>
                                 <input type="hidden" name="new_calls_count1" id="new_calls_count1" val="10">
                                <p class="text-muted font-13 mb-0 text-truncate">New Calls</p>
                            </div>
                            <div class="col-3">
                                <h3 class="text-warning" id="follow_up_calls_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">Followup Calls</p>
                            </div>
                            <div class="col-3">
                                <h3 class="text-success" id="updated_calls_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">Update Calls</p>
                            </div>
                             <div class="col-3">
                                <h3 class="text-info" id="closed_calls_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">Close Calls</p>
                            </div>
                        </div> <!-- end row -->

                        <div id="lifetime-sales" data-colors="#C0392B,#F1C40F, #27AE60,#2980B9" style="height: 270px;" class="morris-chart mt-3"></div>                
                    
                    </div>
                </div> <!-- end collapse-->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->


     <div class="col-md-6">
        <!-- Portlet card -->
        <div class="card">
            <div class="card-body" dir="ltr">

                 <div class="card-widgets">
                    <a href="index.php?file=call/list" class="text-success"><i class="mdi mdi-account-multiple-plus mdi-36px"></i></a>
                    <!-- <a data-toggle="collapse" href="#cardCollpase1" role="button" aria-expanded="false" aria-controls="cardCollpase1"><i class="mdi mdi-minus"></i></a>
                    <a href="javascript: void(0);" data-toggle="remove"><i class="mdi mdi-close"></i></a> -->
                </div>
                
                <h4 class="header-title mb-0">Leads & Follow ups</h4>

                <div id="cardCollpase12" class="collapse pt-3 show">
                    <div class="text-center">
                        <div class="row">
                            <div class="col-3">
                                <h3 class="text-danger" id="new_calls_leads_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">New Leads</p>

                            </div>
                            <div class="col-3">
                                <h3 class="text-warning" id="follow_up_calls_leads_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">Followup Leads</p>
                            </div>
                            <div class="col-3">
                                <h3 class="text-success" id="updated_calls_leads_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">Update Leads</p>
                            </div>
                             <div class="col-3">
                                <h3 class="text-info" id="closed_calls_leads_count">0</h3>
                                <p class="text-muted font-13 mb-0 text-truncate">Close Leads</p>
                            </div>
                        </div> <!-- end row -->

                        <div id="lifetime-sales11" data-colors="#C0392B,#F1C40F, #27AE60,#2980B9" style="height: 270px;" class="morris-chart mt-3"></div>                
                    
                    </div>
                </div> <!-- end collapse-->
            </div> <!-- end card-body-->
        </div> <!-- end card-->
    </div> <!-- end col-->

      

    
</div>


