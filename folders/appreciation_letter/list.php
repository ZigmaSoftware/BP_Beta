<!-- <?php
$company_name_option          = company_name();
$company_name_option        = select_option($company_name_option, "Select", $company_name);

?> -->

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <div class="col-md-12">
                                    <?php echo btn_add($btn_add); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <table id="appreciation_letter" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Employee Name</th>
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