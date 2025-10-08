
<?php
// $company_name_option     = ["1" => [
//     "unique_id" => "1",
//     "value"     => "ASCENT URBAN RECYCLERS PRIVATE LIMITED",
//       ],
//       "2" => [
//     "unique_id" => "2",
//     "value"     => "ASCENT E DIGIT SOLUTIONS PRIVATE LIMITED",
//       ],
//       "3" => [
//        "unique_id" => "3",
//        "value"     => " Infinite Inland Famers pvt ltd",
//          ],
//     ];
$company_name_option          = company_name();
    $company_name_option        = select_option($company_name_option,"Select",$company_name);

?>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
            <form class="was-validated">
                <div class="row">                                    
                    <div class="col-12">
                    <div class="form-group row ">
                                <label class="col-md-2 col-form-label" for="status">Company Name</label>
                                <div class="col-md-3">
                                    <select name="company_name" id="company_name" class="select2 form-control ">
                                    <?php echo $company_name_option; ?>   
                                        
                                    </select>
                                </div>
                            
                            <div class="col-md-1  d-flex justify-content-center">
                                <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="staffFilter();">Go</button>
                                
                            </div>
                            </div>
                            
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row ">
                            <div class="col-md-12">
                                <?php echo btn_add($btn_add); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <table id="offer_letter_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Letter Date </th>
                            <th>Letter No</th>
                            <th>Name</th>
                            <th>Company Name</th>
                            <th>Designation</th>
                            <th>Join Date</th>
                            <th>CTC</th>
                            <th>Action</th>
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