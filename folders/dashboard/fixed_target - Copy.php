<div class="col-xl-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4>Fixed Target </h4>
                <form class="was-validated">
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="report_type" class="col-form-label">Report Type</label>
                            <select name="report_type" id="report_type" onchange="quarter_month_change(this.value,'fixed')" class="select2 form-control">
                                <?php echo $report_type_options; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-5 fixed-div fixed-quarterly d-none">
                            <label for="quarterly_report" class=" col-form-label">Quarterly</label>
                            <select name="quarterly_report" id="quarterly_report" class="select2 form-control">
                                <?php echo $quarter_type_options; ?>
                            </select> 
                        </div>
                        <div class="form-group col-md-5 fixed-div fixed-monthly d-none">
                            <label for="monthly_report" class=" col-form-label">Monthly</label>
                            <select name="monthly_report" id="monthly_report" class="select2 form-control">
                                <?php echo $months_options; ?>
                            </select>  
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" id="btn" class="btn btn-primary btn-rounded mt-4" onclick="fixed_target();">Go</button>
                        </div>
                    </div>
                </form>

                <?php if(($is_team_head == 0)&&($_SESSION['sess_user_type'] != '5f97fc3257f2525529')) { ?>
                    <div class="row ">
                        <div class="col-md-12">
                            <!-- <div class="col-md-6 col-6 p-0"> -->
                                <h5 class="text-dark d-inline">Target (100 %):<span class="text-primary font-weight-bold" id="staff_target">0.00 </span> </h5>
                                
                            <!-- </div>
                            <div class="col-md-6 col-6 p-0"> -->
                                <h5 class="text-dark d-inline float-right" id="team_head_achived" onclick="">Achieved (<span id="staff_archieved_percentage">0</span>%): <span class="text-primary font-weight-bold" id="staff_archieved">0.00 </span></h5>
                                
                            <!-- </div> -->
                        </div>
                        <div class="col-md-12">
                            <!-- Chart Begins -->
                            <div class="chartjs-chart">
                                <canvas id="fixed-target-chart" height="100" data-colors="#1abc9c,#f1556c"></canvas>
                            </div>
                            <!-- Chart Ends -->
                        </div>
                    </div>
                <?php } else { ?>
                    <div id = "staff_target_div">
                        <?php include 'staff_target.php';?>
                    </div>
                <?php } ?>
            </div>           
        </div>
    </div>
        <!-- <div class="col-xl-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center"> Fixed Target </h4>
                <div class="row ">
                    <div class="col-md-12"> -->
                        <!-- Chart Begins -->
                        <!-- <div class="chartjs-chart">
                            <canvas id="fixed-target-chart"  data-colors="#1abc9c,#f1556c"></canvas>
                        </div> -->
                        <!-- Chart Ends -->
                    <!-- </div>
                </div>
            </div>           
        </div>
    </div>
 -->

<style type="text/css">
        
        .rounded-circle:hover {
  -ms-transform: scale(1.5); /* IE 9 */
  -webkit-transform: scale(1.5); /* Safari 3-8 */
  transform: scale(2.5); 
}

    </style>