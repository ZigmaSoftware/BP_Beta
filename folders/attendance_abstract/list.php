<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated"  autocomplete="off" > 
                    <div class="row">                                    
                        <div class="col-12">
                              <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="year_month"> Month - Year   </label>
                                <div class="col-md-3">
                                    <input type="month" name="year_month" id="year_month" class="form-control" value="<?php echo date('Y-m') ?>" required>
                                </div>
                                
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="attendnce_summary();">Go</button>
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="new_external_window('folders/attendance_abstract/print.php');"><i class="mdi mdi-printer mr-1"></i> Print</a>
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2"  id="excel_export"><i class="fas fa-file-excel"></i> Excel</button>
                                </div>
                            </div>
                        </div>
                    </div>  
                    <div id = "listing_div"> 
                        <?php include 'table_listing.php';?>
                    </div>
                </form>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>