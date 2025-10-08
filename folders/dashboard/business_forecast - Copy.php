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
                    <table class="table ">
                        <thead>
                        <tr class="text-right">
                            <th class="text-left">Target</th>
                            <th class="text-center">Progress</th>
                            <th>Committed</th>
                            <th>Achieved</th>
                        </tr>
                        </thead>
                        <tbody class="text-right">
                            <tr>
                                <th class="business-forcast-td text-left" onclick="business_forecast_modal(1)">Lead</th>
                                <td class="text-center">
                                    <div class="progress mb-0">
                                        <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated " role="progressbar" id="lead_progress" aria-valuenow="175" aria-valuemin="0" aria-valuemax="100" style="width: 175%">
                                        </div>
                                    </div>
                                    <div id="lead_percentage">
                                        75 %
                                    </div>
                                </td>
                                <td class="business-forcast-td" id="lead_committed" onclick="business_forecast_modal(1)"></td>
                                <td class="business-forcast-td" id="lead_achieved" onclick="business_forecast_modal(1)"></td>
                            </tr>
                            <tr>
                                <th class="business-forcast-td text-left" onclick="business_forecast_modal(2,'Funnel Upside')">Funnel Upside</th>
                                <td  class="text-center">
                                    <div class="progress mb-0">
                                        <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated " role="progressbar" id="funnel_upside_progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                        </div>
                                    </div>
                                    <div id="funnel_upside_percentage">
                                        75 %
                                    </div>
                                </td>
                                <td class="business-forcast-td" id="funnel_upside_committed" onclick="business_forecast_modal(2)"></td>
                                <td class="business-forcast-td" id="funnel_upside_achieved" onclick="business_forecast_modal(2)"></td>
                            </tr>
                            <tr>
                                <th class="business-forcast-td text-left" onclick="business_forecast_modal(3,'Funnel Commit')">Funnel Commit</th>
                                <td  class="text-center">
                                    <div class="progress mb-0">
                                        <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated " role="progressbar" id="funnel_commit_progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                        </div>
                                    </div>                                    
                                    <div id="funnel_commit_percentage">
                                        75 %
                                    </div>
                                </td>
                                <td class="business-forcast-td" id="funnel_commit_committed" onclick="business_forecast_modal(3)"></td>
                                <td class="business-forcast-td" id="funnel_commit_achieved" onclick="business_forecast_modal(3)"></td>
                            </tr>
                            <tr>
                                <th class="business-forcast-td text-left" onclick="business_forecast_modal(4,'Purchase')">Purchase Order</th>
                                <td  class="text-center">
                                    <div class="progress mb-0">
                                        <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated " role="progressbar" id="purchase_order_progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                        </div>
                                    </div>
                                    <div id="purchase_order_percentage">
                                        75 %
                                    </div>
                                </td>
                                <td class="business-forcast-td" id="purchase_order_committed" onclick="business_forecast_modal(4)"></td>
                                <td class="business-forcast-td" id="purchase_order_achieved" onclick="business_forecast_modal(4)"></td>
                            </tr>
                            <tr>
                                <th class="business-forcast-td text-left" onclick="business_forecast_modal(5,'Billing')">Billing</th>
                                <td  class="text-center">
                                    <div class="progress mb-0">
                                        <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated " role="progressbar" id="billing_progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                        </div>
                                    </div>
                                    <div id="billing_percentage">
                                        75 %
                                    </div>
                                </td>
                                <td class="business-forcast-td" id="billing_committed" onclick="business_forecast_modal(5)"></td>
                                <td class="business-forcast-td" id="billing_achieved" onclick="business_forecast_modal(5)"></td>
                            </tr>
                            <tr>
                                <th class="business-forcast-td text-left" onclick="business_forecast_modal(6,'Payment')">Payment</th>
                                <td  class="text-center">
                                    <div class="progress mb-0">
                                        <div class="progress-bar business-forecast-progress-bar progress-bar-striped progress-bar-animated " role="progressbar" id="payment_progress" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%">
                                        </div>
                                    </div>
                                    <div id="payment_percentage">
                                        75 %
                                    </div>
                                </td>
                                <td class="business-forcast-td" id="payment_committed" onclick="business_forecast_modal(6)"></td>
                                <td class="business-forcast-td" id="payment_achieved" onclick="business_forecast_modal(6)"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>           
        </div>
    </div>

    <?php include 'business_forecast_model.php'; ?>