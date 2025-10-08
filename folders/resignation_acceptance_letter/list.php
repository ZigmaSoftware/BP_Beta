<?php
$company_name_option          = company_name();
$company_name_option        = select_option($company_name_option, "Select", $company_name);

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
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <?php echo btn_add($btn_add); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="resignation_acceptance_letter" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee Name</th>
                                <th>Company Name</th>
                                <th>Department</th>
                                <th>Designation</th>
                                <th>Employee Code</th>
                                <th>Join Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div><!-- end col-->
</div>
<!-- end row-->