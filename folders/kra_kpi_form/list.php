<?php
$sess_user_type  = $_SESSION['sess_user_type'];
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="row">                                    
                    <div class="col-12">
                        <!-- <h4 class="header-title">Delivery / Invoice Details </h4> -->
                        <div class="form-group row ">
                        <?php if ($_SESSION['sess_user_type']  == '5f97fc3257f2525529') {?>
                            <div class="col-md-12">
                                
                                <?php echo btn_add($btn_add); ?>
                            </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
                <table id="kra_kpi_form_datatable" class="table dt-responsive nowrap w-100">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Staff Name</th>
                            <th>Document1</th>
                            <th>Document2</th>
                           <?php if($_SESSION['sess_user_type']  == '5f97fc3257f2525529') {?>
                            <th>Action</th>
                            <?php }?>
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