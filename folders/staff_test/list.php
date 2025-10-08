<?php

$company_name_option          = company_name();
$company_name_option        = select_option($company_name_option, "Select", $company_name);

?>
<div class="col-md-12" >
    <div class="col-md-2"><?php echo btn_add($btn_add); ?></div>
</div>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <form class="was-validated">
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group row ">
                                <!--<label class="col-md-1 col-form-label" for="status"> Status</label>-->
                                <!--<div class="col-md-2">-->
                                <!--    <select name="staff_status" id="staff_status" class="select2 form-control " required>-->
                                <!--        <option value="0">All</option>-->
                                <!--        <option value="1">Active Staff</option>-->
                                <!--        <option value="2">Relieved Staff</option>-->
                                <!--    </select>-->
                                <!--</div>-->
                                <label class="col-md-2 col-form-label" for="status">Company Name</label>
                                <div class="col-md-3">
                                    <select name="company_name" id="company_name" class="select2 form-control ">
                                        <?php echo $company_name_option; ?>

                                    </select>
                                </div>
                                <div class="col-md-2 d-flex justify-content-center">
                                    <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="staffFilter();">Go</button>

                                </div>
                                
                            </div>
                        </div>
                    </div>

                    <!-- <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exampleModal" data-whatever="@getbootstrap">Send Email</button> -->

                    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalLabel" style="color: #000;">Email</h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <form>
                                        <div class="form-group">
                                            <label for="recipient-name" class="col-form-label">Email:</label>
                                            <input type="email" id="email_id" name="email_id" class="form-control" required>
                                        </div>
                                        <!-- <div class="form-group">
            <label for="message-text" class="col-form-label">Message:</label>
            <textarea class="form-control" id="message-text"></textarea>
          </div> -->
                                    </form>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="button" onclick="send_mail()" class="btn btn-primary">Send Email</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    
                    <div class="col-md-12 " align="right">
                        <a href="#" id="excel_export"><i class="fas fa-file-excel" style="font-size:30px; color: green;"></i><span style="color: green;">&nbsp;Excel Export</span></a>
                    </div>
                    <table id="staff_datatable" class="table dt-responsive nowrap w-100">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Staff Id</th>
                                <th>Staff Name</th>
                                <th>DOB</th>
                                <th>Designation</th>
                                <th>Department</th>
                                <th>Company Name</th>
                                <th>Project Name</th>
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
<script>
    function send_mail() {
        alert("hii");
        var email_id = $('#email_id').val();

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        //     var currentdate = new Date(); 
        // var datetime = "Last Sync: " + currentdate.getDate() + "/"
        //                 + (currentdate.getMonth()+1)  + "/" 
        //                 + currentdate.getFullYear() + " @ "  
        //                 + currentdate.getHours() + ":"  
        //                 + currentdate.getMinutes() + ":" 
        //                 + currentdate.getSeconds();
        // const date = new Date();

        // let currentDay= String(date.getDate()).padStart(2, '0');

        // let currentMonth = String(date.getMonth()+1).padStart(2,"0");

        // let currentYear = date.getFullYear();

        // // we will display the date as DD-MM-YYYY 

        // let currentDate = `${currentDay}-${currentMonth}-${currentYear}`;
        // alert(currentDate);
        // alert(datetime);

        if (email_id) {
            var data = {
                // "ho_name"  : ho_name,
                // "staff_id" : staff_name,
                "email_id": email_id,

                "link": "https://103.130.89.95/aed_erp/folders/staff_detail_creation/check.php",
                "action": "send_mail",
            };
            // alert(data);

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(data) {

                    if (data) {
                        const modal = document.querySelector('.modal')
                        sweetalert("custom", '', '', 'Email sent successfully');
                        // window.close();
                        // $('#exampleModal').hide();
                        modal.style.display = 'none';
                        location.reload();
                        // document.getElementById('exampleModal').style.display = "none";
                    }
                }
            });
        } else {
            alert("Enter Email Address");
        }

    }
</script>