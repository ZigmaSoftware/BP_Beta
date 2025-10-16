<?php



//Supplier Name
$supplier_name_options     = supplier();
$supplier_name_options     = select_option($supplier_name_options,"Select");

// Approve Option
$satisfied_status_options = [
    1 => [
        "unique_id" => "1",
        "value"     => "Satisfactory"
    ],
    2 => [
        "unique_id" => "2",
        "value"     => "Un Satisfactory"
    ]
];
$satisfied_status_options    = select_option($satisfied_status_options,"Select");


?>



<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row add_btn">
                            <div class="col-md-12">
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                        <div class="form-group row ">
                            
                            
                            <div class="col-md-2">
                                <label class="col-form-label" for="from_period">From Period</label>
                                <input type="month" name="from_period" id="from_period" class="form-control">
                            </div>
                            
                            <div class="col-md-2">
                                <label class="col-form-label" for="to_period">To Period</label>
                                <input type="month" name="to_period" id="to_period" class="form-control">
                            </div>

                            
                           
                            <div class="col-md-2">
                                <label class="col-form-label" for="supplier_name"> Supplier Name </label>
                               <select name="supplier_name" id="supplier_name" class="select2 form-control" required <?= $disable; ?>>
                                <?= $supplier_name_options; ?> 
                            </select>
                            </div>
                           
                            <div class="col-md-2">
                                 <label class="col-form-label" for="status_fill">  Satisfactory Status</label>
                                <select name="status_fill" id="status_fill" class="select2 form-control" required>
                                    <?php echo $satisfied_status_options; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-1 mt-4 mb-2">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="supplier_ratings_filter();">Go</button>
    
                            </div>
                        </div>
                    </div>
                </div>

                <table id="supplier_ratings1_datatable" class="table
table-striped dt-responsive nowrap w-100">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th> Supplier Name </th>
                            <th> Period </th>
                            <th> Quality Rating </th>
                            <th> Delivery Rating </th>
                            <th> Response Rating </th>
                            <th> Compliances Rating </th>
                            <th> Total Rating </th>
                            <th> Satisfactory Status </th>
                            <th> Remarks </th>
                            <th> Active Status</th>
                            <th> Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                                            
                </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->