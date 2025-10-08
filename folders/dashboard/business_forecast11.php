<div class="col-xl-6 col-md-6">
        <div class="card">            
            <div class="card-body">
                <h4>Business Forecast</h4>
                <form class="was-validated">
                    <div class="form-row">
                        <div class="form-group col-md-5">
                            <label for="business_report_type" class="col-form-label">Report Type</label>
                            <select name="business_report_type" id="business_report_type" onchange="quarter_month_change(this.value,'forecast')"  class="select2 form-control">
                                <?php echo $report_type_options; ?>
                            </select>
                        </div>
                        <div class="form-group col-md-5 forecast-div forecast-quarterly d-none">
                            <label for="business_quarterly_report" class="col-form-label">Quarterly</label>
                            <select name="business_quarterly_report" id="business_quarterly_report" class="select2 form-control">
                                <?php echo $quarter_type_options; ?>
                            </select>  
                        </div>
                        <div class="form-group col-md-5 forecast-div forecast-monthly d-none">
                            <label for="business_monthly_report" class="col-form-label">Monthly</label>
                            <select name="business_monthly_report" id="business_monthly_report" class="select2 form-control">
                                <?php echo $months_options; ?>
                            </select>  
                        </div>
                        <div class="form-group col-md-2">
                            <button type="button" id="btn" class="btn btn-primary btn-rounded mt-4" onclick="business_forecast()">Go</button>
                        </div>
                    </div>
                </form>
                <div class="table-responsive table-striped">
                    <table class="table" id="dashboard-business-forecast">
                        <thead>
                        <tr class="text-right">
                            <th class="text-left">Target</th>
                            <th class="text-center">Progress</th>
                            <th>Committed</th>
                            <th>Achieved</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>           
        </div>
    </div>

    <?php //include 'business_forecast_model.php'; ?>