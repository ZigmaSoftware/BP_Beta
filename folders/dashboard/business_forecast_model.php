<!-- Full width modal content -->
<div id="business-forecast" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="businessforecastModal" aria-hidden="true">
    <div class="modal-dialog modal-full-width">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="fixedforecastModal">Business Forecast</h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
            </div>
            <div class="modal-body">
            <div class="row">
                <div class="col-12 text-center">
                    <h3 id="business_forecat_modal_type"></h3>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-6">
                    <h3 class="d-inline">Committed : </h3>
                    <h3 class="d-inline" id="business_forecast_modal_committed">0.00</h3>
                </div>
                <div class="col-md-6 col-6 text-right">
                    <h3 class="d-inline">Achieved : </h3>
                    <h3 class="d-inline" id="business_forecast_modal_achieved">0.00</h3>
                </div>
            </div>             
            <table id="forcast_modal_table" class="table table-bordered table-striped text-right">
                <thead>
                    <tr>
                        <th class="text-left">Staff Name</th>
                        <th class="text-center">Progress</th>
                        <th>Committed</th>
                        <th>Achieved</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Save changes</button> -->
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->