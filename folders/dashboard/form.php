<style>
    .icon-button {
        position: relative;
        display: inline-block;
    }

    .icon-button .badge {
        position: absolute;
        top: 0;
        right: 0;
        background-color: red;
        /* Customize the background color */
        color: white;
        /* Customize the text color */
        border-radius: 50%;
        padding: 3px 6px;
        /* Adjust the padding to your liking */
        font-size: 12px;
        /* Adjust the font size as needed */
    }


    .icon {
        text-align: right;
    }

    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        color: #000;
    }

    img.round_img {
        border-radius: 50px;
        width: 80px;
        height: 80px;
        border: 1px solid #3f00ff;
    }

    img.round_img1 {
        border-radius: 50px;
        width: 80px;
        height: 80px;
        border: 1px solid #df0404;
    }

    img.cake_img {
        width: 60px;
        height: 60px;
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

    .text-vilot,
    .text-green {
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


    $report_type_options = select_option($report_type, "Select Report Type", 1);

    $account_year        = account_year();

    $account_year_options = select_option($account_year, "Select Account Year");

    $quarter_type_options = select_option($quarter_type, "Select Quarter");

    $months_options      = select_option($months, "Select Month");

    // Get all Dashboard Menus
    // $dashboard_menus     = dashboard_menu();
    $dashboard_menus        = "";

    // Get Dashboard Menus Based on Current User

    $dash_select         = "SELECT menus FROM dashboard_settings WHERE is_delete = 0 AND is_active = 1 AND user_type_id = '" . $_SESSION['sess_user_type'] . "' AND staff_id = '" . $_SESSION['staff_id'] . "' ";

    $dash_select         = $pdo->query($dash_select);

    if ($dash_select->status) {
        if (!empty($dash_select->data)) {
            $dashboard_menus     = $dash_select->data[0]['menus'];
        } else {
            $dash_select         = "SELECT menus FROM dashboard_settings WHERE is_delete = 0  AND is_active = 1  AND user_type_id = '" . $_SESSION['sess_user_type'] . "' AND staff_id = '' ";

            $dash_select         = $pdo->query($dash_select);

            if ($dash_select->status) {
                if (!empty($dash_select->data)) {
                    $dashboard_menus     = $dash_select->data[0]['menus'];
                }
            }
        }
    } else {
        print_r($dash_select);
    }

    $dashboard_menus = explode(",", $dashboard_menus);

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
if (isset($_GET['date'])) {
    $date = $_GET['date'];
} else {
    $date = date('Y-m-d');
}

// if ($admin_user_type != $_SESSION['sess_user_type']) {

//     include 'staff_dashboard.php';

// } else {

?>

<?php if ($_SESSION['sess_user_type'] == '5f97fc3257f2525529') { ?>

<span class="" onclick="getCounts()"></span>

<button class="icon-button" onclick="get_popup_data()">
    <i class="fa fa-2x fa-thin fa-comment"></i>
    <span class="badge" id="count_list">
</button>
<!-- id="count_list" -->
<!----content start---------->

<div class="modal" id="myModal_off" style="display:none" ;>
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title w-100 text-center text-info"><b>Lets talk List<span id="count"></span> </b></h4>
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true" onclick="get_close()">×</button>
            </div>
            <div class="container"></div>
            <div class="modal-body">
                <div class="form-group row ">
                    <label class="col-md-1 col-form-label" for="from_date"> From </label>
                    <div class="col-md-3">
                        <input type="date" name="from_date" id="from_date" class="form-control" value="<?php echo $date; ?>">
                    </div>

                    <label class="col-md-1 col-form-label" for="to_date"> To </label>
                    <div class="col-md-3">
                        <input type="date" name="to_date" id="to_date" class="form-control" value="<?php echo $date; ?>">
                    </div>
                    <div class="col-md-3 d-flex justify-content-center">
                        <button type="button" class="btn btn-primary  btn-rounded mr-2" onclick="get_popup_data();">Go</button>
                    </div>
                </div>
                <div id="table3_data"></div>
            </div>
            <div class="modal-footer">
                <a href="#" data-dismiss="modal" class="btn btn-secondary" onclick="get_close()">Close</a>
            </div>
        </div>
    </div>
</div>

<?php } ?>
<div class="row">

    <?php if (empty($dashboard_menus)) : ?>
        <h4 class="text-center text-primary">Welcome to Xeon Waste Management</h4>
        <?php else :

        if (empty($dashboard_menus[0])) {

        ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body text-center">
                            <h4 class="text-primary">Welcome to Xeon Waste Management</h4>
                        </div>
                    </div>
                </div>
            </div>

        <?php
        } else {

        ?>
            <marquee class="text-success h2 d-print-none" direction="left" scrollamount="7">
                For Attendance, Click Attendance Button in Staff profile
            </marquee>


    <?php
            foreach ($dashboard_menus as $dash_key => $dash_value) {

                $menu_name_org  = $dash_value;

                // include Files
                include $menu_name_org . '.php';
            }
        }
    endif;
    ?>
    <?php if ($_SESSION['sess_user_type'] == '5ff71f5fb5ca556748') { ?>

        <div class="col-xl-4 col-md-4">
            <div id="collapse_div"></div>
            <div id="collapse_div_comming"></div>
        </div>
    <?php } else { ?>
        <div class="col-xl-4 col-md-4">
            <div id="collapse_user_div"></div>
            <div id="collapse_doj_div"></div>
            <div id="festival" onclick="festival_data()"></div>
        </div>
    <?php } ?>
</div>

