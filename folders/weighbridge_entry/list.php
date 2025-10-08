<style>
   .card {
    border: 1px solid #efefef;margin-bottom:0px;
}
.form-control{margin-bottom:0px;}
</style>
<div class="row">
   <div class="col-md-3">
      <div class="card">
         <?php include 'form.php';?>
      </div>
   </div>
   <div class="col-md-9">
      <div class="card">
         <div class="rightlist p-4">
             
             <form class="was-validated" >
                    <div class="row" >
                        <div class="col-12">
                            <div class="form-group row">
                                
                                <div class="col-md-3">
                                    <label class=" col-form-label" for="from_date">From Date</label>
                                    <input type="date" id="from_date" max="<?php echo $entry_date; ?>" name="from_date" class="form-control" value="<?php echo $entry_date; ?>"  required>
                                </div>
                               
                                <div class="col-md-3">
                                     <label class="col-form-label" for="to_date">To Date</label>
                                    <input type="date" id="to_date" max="<?php echo $entry_date; ?>" name="to_date" class="form-control" value="<?php echo $entry_date; ?>"  required>
                                </div>
                                <div class="col-md-1">
                                    <button type="button" class="btn btn-primary mt-4" onclick="validateDateRange(); weighbridge_entry_go_btn();">Go</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
 <br/>
            <table id="weighbridge_entry_datatable" class="table table-striped dt-responsive nowrap w-100">
               <thead class="table-light">
                  <tr>
                     <th>#</th>
                     <th>No of Ticket</th>
                     <th>Date</th>
                     <th>Slip No</th>
                     <th>Vehicle No</th>
                     <th>Source of Waste</th>
                     <th>Gross Weight</th>
                     <th>Tare Weight</th>
                     <th>Net Weight</th>
                     <th>Action</th>
                  </tr>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>
</div>
</div>
