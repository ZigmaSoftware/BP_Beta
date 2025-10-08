<style>
h1,h2,h3,h4,h5,h6 {
    color: #000;
}

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

// Get all Dashboard Menus
$dashboard_menus = dashboard_menu();

?>

</style>

<script>
    $('[data-toggle="collapse"]').on('click', function() {
        $(this).toggleClass('collapsed');
    });
</script>


<?php

// $is_team_head   = "";
// $team_members   = "";

// if ($admin_user_type != $_SESSION['sess_user_type']) {
    
//     include 'staff_dashboard.php';
    
// } else {
    
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
    
    <?php include 'staff_wise_bar_chart.php'; ?>

    <?php include 'call_type.php'; ?>
    
    <?php include 'call_type_donut.php'; ?>

    <?php include 'pending_approvals.php'; ?>

    <?php //include 'pending_approvals.php'; ?>

    <?php include 'staff_horizondal.php'; ?>
    
</div>

<?php //} ?>