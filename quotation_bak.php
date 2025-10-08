<style>

.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #323a46;
}

</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body ml-4 mr-4">
                <!-- Logo & title -->

                <div class="row">
                    <div class="col-12">
                        <img src="img/headerimg.PNG" class="w-100" alt="logo">
                    </div>
                </div>
                <div class="clearfix">
                    <div class="float-left">
                        <div class="auth-logo">
                            <div class="logo logo-dark">
                                <span class="logo-lg">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-sm.png" alt="logo" height="90" width="90">
                                </span>
                            </div>
        
                            <!-- <div class="logo logo-light">
                                <span class="logo-lg">
                                    <img src="<?=$_SESSION['sess_img_path'];?>logo-sm.png" alt="logo" height="90" width="90">
                                </span>
                            </div> -->
                            
                        </div>
                    </div>
                    <div class="float-left">
                        <div class="mt-2 pl-3">
                            <h4 class=""> <?php echo $_SESSION['sess_company_name'] ; ?></h4>
                            <h6 class=""> <?php echo $_SESSION['sess_company_address'] ; ?></h6>
                            <h6 class=""> <?php echo $_SESSION['sess_company_district'] ; ?></h6>
                            <h6 class=""> <?php echo $_SESSION['sess_company_state'] ; ?></h6>                            
                        </div><!-- end col -->
                    </div>
                    <div class="float-right">
                        <h3 class="">Quotation</h3>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <!-- <div class="mt-3">
                            <p><b>Hello, Stanley Jones</b></p>
                            <p class="text-muted">Thanks a lot because you keep purchasing our products. Our company
                                promises to provide high quality products for you as well as outstanding
                                customer service for every transaction. </p>
                        </div> -->

                        <h4 class="text-secondary">To</h4>
                        <h5 class="text-danger">The Managing Director,</h5>
                        <h5 class="text-danger">TamilNadu Small Industries Development Corporation</h5>
                        <h5 class="text-danger">Tansidco,</h5>
                        <h5 class="text-danger">Thiru.Vi.ka Industrial Estate,</h5>
                        <h5 class="text-danger">CHENNAI - 600032</h5>

                    </div><!-- end col -->
                    <div class="col-md-4 offset-md-2">
                        <div class="mt-3 float-right">
                            <p class="m-b-10"><strong>Quotation Date  </strong> <span class="float-right text-danger"> &nbsp;&nbsp;&nbsp;&nbsp; 05-02-2021</span></p>
                            <!-- <p class="m-b-10"><strong>Quotation Status : </strong> <span class="float-right"><span class="badge badge-danger">Unpaid</span></span></p> -->
                            <p class="m-b-10"><strong>Quotation No &nbsp;    </strong> <span class="float-right text-danger">QUO-202102010001 </span></p>
                        </div>
                    </div><!-- end col -->
                </div>
                <!-- end row -->

                <div class="row">
                    <div class="col-12">
                        <h5 class="font-weight-bold text-secondry">Sub : Proposal for <span class="text-danger">(Professional Display)</span> - reg</h5>
                        <h5 class="text-secondary mb-3">Dear Sir/Madam,</h5>
                        <h5 class="font-weight-bold text-secondry">Greetings from Ascent e-Digit Solutions (P) Ltd.,</h5>
                    
                        <div class="mt-3">
                            <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;Thanks for the kind courtesy extended and the reinforced confidence that you show on us and your support. Further to your enquiry regarding the requirement for <br /><span class="text-danger">(Professional Display)</span>, we are pleased to submit the price </p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                    <h5 class="font-weight-bold text-secondry"><u>Our Strengths</u> :</h5>
                    <ul>
                        <li>30 Year's Experience in System Integrators</li>
                        <li>An ISO 9001:2015, 27001:2013, 20000-1:2011 Certified Company</li>
                        <li>Highly Professtional in Providing Solutions in IT Field</li>
                        <li>Well-equipped Testing & Repair Centre</li>
                        <li>Specialists in Server & Higher end Networking Solutions</li>
                        <li>Branch & Support Centers across in India</li>
                    </ul>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5 class="font-weight-bold text-secondry"><u>Commercial Proposal</u> :</h5>
                        <table id="quotation_product_datatable" class="table dt-responsive nowrap w-100">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <!-- <th>Sales Or Service</th>
                                    <th>Product Category</th>
                                    <th>Product Group</th> -->
                                    <th>Product Name</th>
                                    <th>Product Description</th>
                                    <th>Make & Modal</th>
                                    <th>Qty</th>
                                    <th>Rate Per Unit</th>
                                    <th>GST</th>
                                    <th>GST Value</th>
                                    <th>Total Value (INR)</th>
                                    <!-- <th>Bid releasing Month</th>
                                    <th>Will the Purchase replicate in other District or State</th>
                                    <th>Remarks</th> -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>                                        
                        </table>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <h5 class="font-weight-bold text-secondry"><u>Terms & Conditions</u> :</h5>
                        <ul>
                            <li>Payment Terms: <span class="text-danger">100 % against delivery</span> </li>
                            <li>Delivery Address & Contact Numbers Required Along With Purchase Order for Delivery.</li>
                            <li>Delivery: <span class="text-danger">10 Days</span> from the Date of Receiving Purchase Order.</li>
                            <li>Material Delivery: Free of Cost at Destination Address.</li>
                            <li>Order & Payment: should be placed on Ascent e-Digit Solutions (P) Ltd, Chennai</li>
                            <li>Order Delivery Follow up: <span class="text-danger"> D.SUGAN / 9952555305</span>.</li>
                        </ul>
                        <div class="mt-3">
                            <p class="text-muted">&nbsp;&nbsp;&nbsp;&nbsp;We trust you find our offer in line  ith your requirements and look forward to partnering you in all your IT endeavours. We request you to call for any further clarifications need regarding the above same order. </p>
                        </div>
                    </div>

                </div>

                <div class="row">
                    <div class="col-12  mt-4 mb-4">
                        <h5 class="font-weight-bold text-secondry float-right">For Ascent e-Digit solutions (P) Ltd</h5>
                    </div>
                    <div class="col-12 mb-n2">
                        <h5 class="font-weight-bold text-secondry float-right"> <span class="text-danger"> D.SUGAN / Business Development Manager</span> </h5>
                        <!-- <h5 class="font-weight-bold text-secondry float-right"> <span class="text-danger"> +91 9952555305 / <u>sugan@aedindia.com</u> </span> </h5> -->
                    </div>
                    <div class="col-12">
                        <!-- <h5 class="font-weight-bold text-secondry float-right"> <span class="text-danger"> D.SUGAN / Business Development Manager</span> </h5> -->
                        <h5 class="font-weight-bold text-secondry float-right"> <span class="text-danger"> +91 9952555305 / <u>sugan@aedindia.com</u> </span> </h5>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                    <h4 class="header text-center">Vijaywada . Bangalore . Erode . Coimbatore . Madurai . Trichy . Trinelveli . Salem . Secunderabad </h4>
                    </div>
                </div>

                <div class="mt-4 mb-1">
                    <div class="text-right d-print-none">
                        <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light"><i class="mdi mdi-printer mr-1"></i> Print</a>
                        <a href="#" class="btn btn-info waves-effect waves-light">Submit</a>
                    </div>
                </div>
            </div> <!-- end card-body-->        
        </div>
    </div> <!-- end col -->
</div>