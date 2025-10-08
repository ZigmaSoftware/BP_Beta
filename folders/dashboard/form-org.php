<style>
h1,h2,h3,h4,h5,h6 {
    color: #000;
}

/* Dash Boad Only Css */
/* .bg-blue {
    background-image: linear-gradient(to right, #1709ef , #070432) !important;
}


.bg-warning {
    background-image: linear-gradient(to right, #3fa30c , #070432) !important;
}

.bg-danger {
    background-image: linear-gradient(to right, #f10909 , #070432) !important;
}

.bg-success {
    background-image: linear-gradient(to right, #ce0fe5 , #070432) !important;
}

.bg-pink {
    background-image: linear-gradient(to right, #eb870a , #070432) !important;
}

.bg-primary {
    background-image: linear-gradient(to right, #09cfd8 , #070432) !important;
}



.text-success {
    color: #01c650!important;
}

.text-info {
    color: #0188a7!important;
} */

/* Custom */

.bg-light-gray {
    background-color: #c9ccce !important;
}

.dash-bg-blue-white {
    background-image: linear-gradient(to right, #1f5b8e, #65aae4) !important;
}

.dash-bg-blue-pink {
    background-image: linear-gradient(to right, #31368a, #c800f3) !important;
}

.dash-bg-red-dred {
    background-image: linear-gradient(to right, #bf0001, #f81617) !important;
}

.dash-bg-pink-dpink {
    background-image: linear-gradient(#d642ea, #f15897) !important;
}

.dash-bg-yellow-orange {
    background-image: linear-gradient(to right, #fec91b, #ff8d1a) !important;
}

.dash-bg-yellow-green {
    background-image: linear-gradient(to right, #cfdd54, #4abe3d) !important;
}

.dash-bg-red-blue {
    background-image: linear-gradient(#d94c81, #485df8) !important;
}

.dash-bg-green-dgreen {
    background-image: linear-gradient(#2be082, #129e91) !important;
}

.dash-bg-orange-rose {
    background-image: linear-gradient(#fa5929, #ef1375) !important;
}

.dash-bg-lgreen-green {
    background-image: linear-gradient(#b7d851, #3dbe3c) !important;
}

.dash-bg-vilot {
    background-image: linear-gradient(#902fa2, #582477) !important;
}

.text-vilot {
    color: #582477 !important;
}

.text-green {
    color: #25b22e !important;
}

.text-vilot, .text-green {
    font-weight: bold !important;
}



<?php

$report_type = [
    [
        "id"    => 1,
        "value" => "Yearly"
    ],
    [
        "id"    => 2,
        "value" => "Quarterly"
    ],
    [
        "id"    => 3,
        "value" => "Monthly"
    ],
];

$quarter_type = [	
    [	
        "id"    => 1,	
        "value" => "Q1-(2021-2022)"	
    ],	
    [	
        "id"    => 2,	
        "value" => "Q2-(2021-2022)"	
    ],	
    [	
        "id"    => 3,	
        "value" => "Q3-(2021-2022)"	
    ],	
    [	
        "id"    => 4,	
        "value" => "Q4-(2021-2022)"	
    ]	
];

$months = [
    [
        "id"    => 4,        
        "value" => "April",
    ],
    [
        "id"    => 5,        
        "value" => "May",
    ],
    [
        "id"    => 6,        
        "value" => "June",
    ],
    [
        "id"    => 7,        
        "value" => "July",
    ],
    [
        "id"    => 8,        
        "value" => "August",
    ],
    [
        "id"    => 9,        
        "value" => "September",
    ],
    [
        "id"    => 10,        
        "value" => "October",
    ],
    [
        "id"    => 11,        
        "value" => "November",
    ],
    [
        "id"    => 12,        
        "value" => "December",
    ],
    [
        "id"    => 1,        
        "value" => "January",
    ],
    [
        "id"    => 2,        
        "value" => "February",
    ],
    [
        "id"    => 3,        
        "value" => "March",
    ],
];


$report_type_options = select_option($report_type,"Select Report Type",1);

$account_year        = account_year();

$account_year_options= select_option($account_year,"Select Account Year");

$quarter_type_options= select_option($quarter_type,"Select Quarter");

$months_options      = select_option($months,"Select Month");

?>

</style>

<script>
    $('[data-toggle="collapse"]').on('click', function() {
        $(this).toggleClass('collapsed');
    });
</script>


<?php

$is_team_head   = "";
$team_members   = "";

if ($admin_user_type != $_SESSION['sess_user_type']) {
    
    include 'staff_dashboard.php';
    
} else {
    
?>

<div class="row">
    <!-- <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                
            </div>
        </div>
    </div> -->

    <!-- Busniess Forecast Include -->
    <?php include 'fixed_target.php'; ?>    

    <!-- Model Include -->
    <?php include 'modal.php'; ?>

    <!-- Busniess Forecast Include -->
    <?php include 'business_forecast.php'; ?>
    

    <div class="col-xl-6 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="chartjs-chart">
                    <canvas id="staff-wise-bar-chart" height="350" width="680" data-colors="#4a81d4,#e3eaef"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6">
        <div class="card">
            <div class="card-title mt-2 mb-0">
                <h2 class="text-primary ml-3 d-inline">Call Type</h2>
                <p class="text-muted font-13 text-truncate d-inline">(Call Type Wise Report)</p>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-blue-white">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5">
                                    <h6 class="text-white">Leads</h6>
                                </div>
                                <div class="col-7">
                                    <div class="text-right mt-n1">
                                        <h3 class="text-white d-inline">58</h3>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-white">Value</h6>
                                    <h3 class="text-white text-center">5,23,524</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-blue-pink">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5">
                                    <h6 class="text-white">Funnel</h6>
                                </div>
                                <div class="col-7">
                                    <div class="text-right mt-n1">
                                        <h3 class="text-white d-inline">38</h3>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-white">Value</h6>
                                    <h3 class="text-white text-center">1,12,524</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-red-dred">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5">
                                    <h6 class="text-white">Bid's</h6>
                                </div>
                                <div class="col-7">
                                    <div class="text-right mt-n1">
                                        <h3 class="text-white d-inline">9</h3>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-white">Value</h6>
                                    <h3 class="text-white text-center">28,10,964</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-pink-dpink">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5">
                                    <h6 class="text-white">Elcot</h6>
                                </div>
                                <div class="col-7">
                                    <div class="text-right mt-n1">
                                        <h3 class="text-white d-inline">158</h3>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-white">Value</h6>
                                    <h3 class="text-white text-center">1,28,10,964</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-yellow-orange">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5">
                                    <h6 class="text-white">Sales Order</h6>
                                </div>
                                <div class="col-7">
                                    <div class="text-right mt-n1">
                                        <h3 class="text-white d-inline">139</h3>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-white">Value</h6>
                                    <h3 class="text-white text-center">28,10,964</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-yellow-green">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-6">
                                    <h6 class="text-white">Payment</h6>
                                </div>
                                <div class="col-6">
                                    <div class="text-right mt-n1">
                                        <h3 class="text-white d-inline">87</h3>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h6 class="text-white">Value</h6>
                                    <h3 class="text-white text-center">1,28,10,964</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                </div>
            </div>
        </div>
    </div>


    <div class="col-xl-4 col-md-4">
        <div class="card">
            <div class="card-body">
                <h4 class="text-center"> Call Type </h4>
                <div class="row ">
                    <div class="col-md-12">
                        <!-- Chart Begins -->
                        <div class="chartjs-chart">
                            <canvas id="calls-chart" height="350" data-colors="#1abc9c,#f1556c"></canvas>
                        </div>
                        <!-- Chart Ends -->
                    </div>
                </div>
            </div>           
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card">
            <div class="card-title mt-2 ml-3 mb-0">
                <h2 class="text-primary">Pending Approval's</h2>
                <p class="text-muted font-13 text-truncate">(Inter & Intra State Count)</p>
            </div>
            <div class="card-body">
                <div class="row mt-n2">
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-6">
                        <div class="widget-rounder-circle card-box dash-bg-red-blue">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-12 text-center mt-n2 mb-2">
                                    <h3 class="text-white">Quotation's</h3>
                                </div>
                                
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h2 class="text-white">58</h2>
                                        <h6 class="text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-6">
                        <div class="widget-rounder-circle card-box dash-bg-green-dgreen">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-12 text-center mt-n2 mb-2">
                                    <h3 class="text-white">Bid's</h3>
                                </div>
                                
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h2 class="text-white">33</h2>
                                        <h6 class="text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-6">
                        <div class="widget-rounder-circle card-box dash-bg-orange-rose">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-12 text-center mt-n2 mb-2">
                                    <h3 class="text-white">Order's</h3>
                                </div>
                                
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h2 class="text-white">85</h2>
                                        <h6 class="text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-6">
                        <div class="widget-rounder-circle card-box dash-bg-lgreen-green">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-12 text-center mt-n2 mb-2">
                                    <h3 class="text-white">Claim's</h3>
                                </div>
                                
                            </div>
                            <div class="row mt-2">
                                <div class="col-12">
                                    <div class="text-center">
                                        <h2 class="text-white">32</h2>
                                        <h6 class="text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="chartjs-chart">
                    <canvas id="sales-bar-chart" height="350" data-colors="#4a81d4,#e3eaef"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<?php } ?>