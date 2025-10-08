<?php

$is_team_head = 0;
$team_members = 0;
$staff_name   = "";

$dash_show    = 0;

$user_columns = [
    "is_team_head",
    "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
    "team_members"
];

$user_table_details = [
    "user",
    $user_columns
];

$user_table_where   = [
    "is_delete"     => 0,
    "unique_id"     => $_SESSION['user_id']
];


$user_result = $pdo->select($user_table_details,$user_table_where);

if ($user_result->status) {
    $user_data = $user_result->data[0];

    $staff_name   = $user_data['staff_name'];
    $is_team_head = $user_data['is_team_head'];
    $team_members = $user_data['team_members'];


    
} else {
    print_r($user_result);
}

?>
<div class="row">
    <div class="col-xl-4 col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row border-bottom border-secondary">
                    <!-- <div class="col-12"> -->
                    <!-- </div> -->
                    <div class="col-md-6">
                        <h3 class="header-title text-center"><?php echo $staff_name; ?></h3>
                        
                        <?php 
                            if ($is_team_head) {
                        ?>
                        <p class="text-center">(Team Head)</p>
                        <?php
                            }
                        ?>
                        <img src="<?php echo $_SESSION["user_image"]; ?>" alt="image" class="img-fluid rounded mb-2  mx-auto d-block">
                    </div>
                    <div class="col-md-6">
                        <!-- Chart Begins -->
                        <div class="chartjs-chart">
                            <canvas id="user-calls-chart" height="350" data-colors="#1abc9c,#f1556c"></canvas>
                        </div>
                        <!-- Chart Ends -->
                    </div>
                </div>
                <div class="row mt-1 text-center mb-2">
                    <div class="col-4 border-right border-secondary">
                        <h2 class="text-green text-bold">26</h2>
                        <p class="text-muted font-15 mt-n1 text-truncate">Working Days</p>
                    </div>
                    <div class="col-4 border-right border-secondary">
                        <h2 class="text-green text-bold">24</h2>
                        <p class="text-muted font-15 mt-n1 text-truncate">Worked Days</p>
                    </div>
                    <div class="col-4">
                        <h2 class="text-green text-bold">02</h2>
                        <p class="text-muted font-15 mt-n1 text-truncate">Leave Days</p>
                    </div>
                </div>
                <div class="row mt-1 text-center mb-3">
                    <div class="col-12">
                        <!-- <i class="mdi mdi-transit-transfer mdi-48px text-primary"></i> 
                        <h4 class="text-primary text-bold">                            
                            Attendance
                        </h4> -->
                        <a href="index.php?file=daily_attendances/create">
                        <button type="button" class="btn btn-primary btn-rounded progress-bar-striped progress-bar-animated">
                            <i class="mdi mdi-transit-transfer mdi-24px text-white"></i>  
                        </button>
                        <button type="button" class="btn btn-primary btn-rounded ml-n3 progress-bar-striped progress-bar-animated">
                            <!-- <span class="text-white mt-n2"> Attendance </span> -->
                            Attendance
                        </button>
                        </a>
                        <!-- <p class="text-muted font-15 mt-n1 text-truncate">Working Days</p> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if ($dash_show) { ?>
    <div class="col-xl-2 col-md-2">
        <div class="card text-center">
            <div class="card-title dash-bg-vilot">
                <h3 class="header-title text-white mt-2 mb-2">Payment Commit</h3>
            </div>
            <div class="card-body ">
                <div>
                    <h2 class="text-vilot mt-2">
                        2,55,000
                    </h2>
                    <p class="text-muted font-13 mb-0 text-truncate">Pending Payment</p>
                </div>
                <div>
                    <h2 class="text-vilot">
                        1,36,000
                    </h2>
                    <p class="text-muted font-13 mb-0 text-truncate">Commited Payment</p>
                </div>
                <div>
                    <h2 class="text-vilot">
                        52,000
                    </h2>
                    <p class="text-muted font-13 mb-0 text-truncate">Collected Payment</p>
                </div>
                <div>
                    <h2 class="text-green">
                        15,000
                    </h2>
                    <p class="text-muted font-13 mb-5 text-truncate">Yet So be Collected</p>
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
                                <div class="col-5 p-0">
                                    <h6 class="text-white">Leads</h6>
                                </div>
                                <div class="col-7 p-0">
                                    <div class="text-right mt-n1">
                                        <h2 class="text-white d-inline">58</h2>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <h6 class="text-white">Value</h6>
                                    <h2 class="text-white text-center">5,23,524</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-blue-pink">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5 p-0">
                                    <h6 class="text-white">Funnel</h6>
                                </div>
                                <div class="col-7 p-0">
                                    <div class="text-right mt-n1">
                                        <h2 class="text-white d-inline">38</h2>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <h6 class="text-white">Value</h6>
                                    <h2 class="text-white text-center">1,12,524</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-red-dred">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5 p-0">
                                    <h6 class="text-white">Bid's</h6>
                                </div>
                                <div class="col-7 p-0">
                                    <div class="text-right mt-n1">
                                        <h2 class="text-white d-inline">9</h2>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <h6 class="text-white">Value</h6>
                                    <h2 class="text-white text-center">28,10,964</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-pink-dpink">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5 p-0">
                                    <h6 class="text-white">Elcot</h6>
                                </div>
                                <div class="col-7 p-0">
                                    <div class="text-right mt-n1">
                                        <h2 class="text-white d-inline">158</h2>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <h6 class="text-white">Value</h6>
                                    <h2 class="text-white text-center">1,28,10,964</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-yellow-orange">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5 p-0">
                                    <h6 class="text-white">Sales Order</h6>
                                </div>
                                <div class="col-7 p-0">
                                    <div class="text-right mt-n1">
                                        <h2 class="text-white d-inline">139</h2>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <h6 class="text-white">Value</h6>
                                    <h2 class="text-white text-center">28,10,964</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                    <!-- Single Card Box Start -->
                    <div class="col-12 col-md-4 col-xl-4">
                        <div class="widget-rounder-circle card-box dash-bg-yellow-green">
                            <div class="row text-white border-bottom border-white">
                                <div class="col-5 p-0">
                                    <h6 class="text-white">Payment</h6>
                                </div>
                                <div class="col-7 p-0">
                                    <div class="text-right mt-n1">
                                        <h2 class="text-white d-inline">87</h2>
                                        <h6 class="d-inline text-white"> &nbsp;Nos</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 p-0">
                                    <h6 class="text-white">Value</h6>
                                    <h2 class="text-white text-center">1,28,10,964</h2>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Single Card Box End -->
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
    <!-- Busniess Forecast Include -->
    <?php include 'fixed_target.php'; ?>    

    <!-- Model Include -->
    <?php include 'modal.php'; ?>

    <!-- Busniess Forecast Include -->
    <?php include 'business_forecast.php'; ?>
    <?php if ($dash_show) { ?>
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
            <div class="card-title text-center dash-bg-vilot">
                <h3 class="header-title text-white mt-2 mb-2">Expense Claims</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12 col-md-6 col-xl-6">
                        <div class="widget-rounded-circle card-box p-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="mdi mdi-file-document mdi-48px text-primary"></i>
                                </div>
                                <div class="col">
                                    <p class="text-muted">Expense Claims</p>
                                    <h3 class="text-vilot">2,822.00</h3>
                                </div>
                            </div> <!-- end row-->
                        </div> <!-- end widget-rounded-circle-->
                    </div>

                    <div class="col-12 col-md-6 col-xl-6">
                        <div class="widget-rounded-circle card-box p-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="mdi mdi-file-check mdi-48px text-green"></i>
                                </div>
                                <div class="col">
                                    <p class="text-muted">HOD Approved</p>
                                    <h3 class="text-vilot">222.00</h3>
                                </div>
                            </div> <!-- end row-->
                        </div> <!-- end widget-rounded-circle-->
                    </div>

                    <div class="col-12 col-md-6 col-xl-6">
                        <div class="widget-rounded-circle card-box p-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="mdi mdi-checkbox-multiple-marked-outline mdi-48px text-info"></i>
                                </div>
                                <div class="col">
                                    <p class="text-muted">Accounts Approved</p>
                                    <h3 class="text-vilot">1,922.00</h3>
                                </div>
                            </div> <!-- end row-->
                        </div> <!-- end widget-rounded-circle-->
                    </div>

                    <div class="col-12 col-md-6 col-xl-6">
                        <div class="widget-rounded-circle card-box p-0">
                            <div class="row align-items-center">
                                <div class="col-auto">
                                    <i class="mdi mdi-login-variant mdi-48px text-pink"></i>
                                </div>
                                <div class="col">
                                    <p class="text-muted"> Last Received</p>
                                    <h3 class="text-vilot">2,822.00</h3>
                                </div>
                            </div> <!-- end row-->
                        </div> <!-- end widget-rounded-circle-->
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
    <?php } ?>
</div>

