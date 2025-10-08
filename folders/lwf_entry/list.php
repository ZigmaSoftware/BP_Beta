<?php print_r($_SESSION['sess_user_type']); ?>
<div class="col-md-12">
    <?php echo btn_add($btn_add); ?>
</div> 

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <!-- Modal for details -->
                <!--<div class="modal fade" id="lwfInfoModal" tabindex="-1">-->
                <!--    <div class="modal-dialog modal-xl">-->
                <!--        <div class="modal-content">-->
                <!--            <div class="modal-header">-->
                <!--                <h5 class="modal-title">LWF Entry Details</h5>-->
                <!--                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>-->
                <!--            </div>-->
                <!--            <div class="modal-body" id="lwfInfoModalBody">-->
                                <!-- dynamic content goes here -->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->

                <table id="lwf_table" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project Name</th>
                            <th>State</th>
                            <th>Amount</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>                                            
                </table>
            </div> <!-- end card body-->
        </div> <!-- end card -->
    </div> <!-- end col -->
</div> <!-- end row -->
