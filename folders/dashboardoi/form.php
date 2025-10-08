<script>
    setInterval(function() {
 window.location.reload();
 }, 300000); 
</script>



<div>

</div>
<div class="row">
    <div class="col-12 filter_head">
        <div class="row"> 
            <div class="col-md-2 pe-0">
                
                    <button class="btn btn-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapseWidthExample"
                        aria-expanded="false" aria-controls="collapseWidthExample">
                        Impact Type Filter
                    </button>
               
            </div>
             <div class="col-md-7 ps-0">
            <div class="collapse collapse-horizontal" id="collapseWidthExample" style="">
                <div class="" style="">
                    <div class="row">
                        <div class="col-4 ps-0" style="margin-left: -60px;">
                            <div class="">
                                <select name="priority_name" id="priority_name" class="form-control chosen-select select2" required="">
                                    <?php echo $priority_name_options; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-3 align-self-center" style="">
                             <div class="">
                            <button type="button" class="btn btn-primary waves-effect waves-light" onclick="submitForm()" id="submitBtn">GO</button>
                            </div>
                        </div>
                    </div>
                </div>
                  </div>
            </div>
              <div class="col-md-3 text-end"> 
              <button type="button" class="btn btn-secondary waves-effect waves-light">Operation Impact Dashboard</button>
              </div>
            
            
            
        </div>
    </div>
</div>
<!--<div class="fixed-dashboard-text">
    <p>Operation Impact Dashboard</p>
</div>--->
<form id="priorityForm" method="POST" action="">
    <input type="hidden" name="priority_name_post" id="priority_name_post">
</form>
<?php
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     print_r($_POST['priority_name_post']);
// } else {
//     // Print the default value if the form hasn't been submitted
//     echo $priority_type;
// }
?>
<div class="row">
    <div class="col-xl-3 col-md-6">
        <div class="card">
            <div class="card-body">
                <div class="tabs">
                    <button class="tablink dept-tab" onclick="openTab('dept')" id="defaultOpen">Dept</button>
                    <button class="tablink site-tab" onclick="openTab('site')">Site</button>
                    <button class="tablink own-calls-tab" onclick="openTab('owncalls')">Own Calls</button>
                    <button class="tablink tagged-calls-tab" onclick="openTab('taggedcalls')">Support Calls</button>
                </div>
                
                <div id="owncalls" class="tabcontent" style="display: none;">
                    <h4 class="header-title mb-3">Own Calls</h4>
                    <div id="chartowncalls"></div>
                </div>

                <div id="dept" class="tabcontent">
                    <h4 class="header-title mb-3">Dept wise tasks</h4>
                    <div id="chartbsar"></div>
                </div>

                <div id="site" class="tabcontent" style="display: none;">
                    <h4 class="header-title mb-3">Site wise tasks</h4>
                    <div id="chartsbar"></div>
                </div>
                
                <div id="taggedcalls" class="tabcontent" style="display: none;">
                    <h4 class="header-title mb-3">Tagged tasks</h4>
                    <div id="charttaggedcalls"></div>
                </div>
                
            </div>
        </div>

        <div class="card" style="margin-top: 8px;">
            <div class="card mb-0">
                <div class="card-body">

                    <h4 class="header-title mb-3">Overall task Status</h4>
                    <div class="table-responsive">
                        <table id="overall_complaint_status" class="table table-hover table-centered mb-0 overall-ststus">
                            
                            <tr>
                                <td style="color: #45bbe0;">Registered </td>
                                <th id="total_comp" style="color: #45bbe0;"></th>
                            </tr>
                           
                            <tr>
                                <td style="color: #ff9800;">Progressing</td>
                                <th id="progressing_comp" style="color: #ff9800;"></th>
                            </tr>
                            <tr>
                                <td style="color: #f06292;">Pending</td>
                                <th id="pending_comp" style="color: #f06292;"></th>
                            </tr>
                            <tr>
                               <td style="color: #4CAF50;">Completed</td>
                                <th id="completed_comp" style="color: #4CAF50;"></th>
                            </tr>
                            <tr>
                                <td style="color: #bc170b;">Canceled</td>
                                <th id="cancel_comp" style="color: #bc170b;"></th>
                            </tr>
							 
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
   
            <!--<div class="card">-->
            <!--    <div class="card-body" style="padding-bottom: 68px;">-->
            <!--        <h4 class="header-title mb-3">This Month Closed Status</h4>-->
            <!--        <div id="chartdiv2"></div>-->
            <!--    </div>-->
            <!--</div>-->
      
       
    </div>

    <div class="col-md-9">
        <div class="card">
            <div class="card-body">
                <h4 class="header-title ">Total Tasks</h4>
                    <div class="row show-grid">
        
                    <div class="col-sm-2">
                        <div class="colr-box cr-4">
                            <div class="icon-bg i3">
                                <i class=" fas fa-check-double"></i>
                            </div>
                                <h6 onclick="new_external_window_print(event,'folders/dashboardoi/print.php','opening','<?php echo $priority_type?>');">Opening</h6>
                                <h3 id="opening_complaints" style="color: #bd83fc;"></h3>
                            </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="colr-box cr-2">
                            <div class="icon-bg i1">
                                <i class="  fas fa-exclamation-circle"></i>
                            </div>
                                <h6 onclick="new_external_window_print(event,'folders/dashboardoi/newprint.php','new','<?php echo $priority_type?>');">New</h6>
                                <h3 id="new_complaints" style="color: #dd980e;"></h3>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="colr-box cr-7">
                            <div class="icon-bg tag1">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                                <h5 onclick="new_external_window_print(event,'folders/dashboardoi/print.php','pending','<?php echo $priority_type?>');">Pending</h5>
                                <h3 style="color: #06a0f3;" id="pending_calls"></h3>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="colr-box cr-3">
                            <div class="icon-bg i2">
                                <i class="fas fa-clock"></i>
                            </div>
                                <h5 onclick="new_external_window_print(event,'folders/dashboardoi/print.php','progress','<?php echo $priority_type?>');">Progressing</h5>
                                <h3 style="color: #f95a80;" id="pending_complaints"></h3>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="colr-box">
                            <div class="icon-bg">
                                <i class=" fas fa-clipboard-check"></i>
                            </div>
                                <h5 onclick="new_external_window_print(event,'folders/dashboardoi/complete_print.php','completed','<?php echo $priority_type?>');">Completed</h5>
                                <h3 style="color: #3ad959;" id="completed_complaints"></h3>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="colr-box cr-6">
                            <div class="icon-bg tag">
                                <i class=" fas fa-tag"></i>
                            </div>
                                <h6 onclick="new_external_window_print(event,'folders/dashboardoi/tagprint.php','tagged_calls','<?php echo $priority_type?>');">Tagged</h6>
                                <h3 style="color: #06a0f3;" id="tagged_calls"></h3>
                        </div>
                    </div>
                    </div>
            </div>
        </div>
		<div class="row">
		<div class="col-md-12">
		<!--<div class="collapse-horizontal collapse show" id="collapseWidthExample" style="">-->
  <!--                   <div class="card card-body mb-0" style="width: 100%;padding: 16px;">-->
  <!--                      <div class="row">-->
  <!--                         <div class="col-5">-->
  <!--                            <div class="">-->
  <!--                               <label for="example-date" class="form-label">Month</label>-->
  <!--                               <input class="form-control" id="month_filter" type="month" name="date" value="<?= date('Y-m') ?>">-->
  <!--                            </div>-->
  <!--                         </div>-->
                           <!-- <div class="col-5">
                              <div class="mb-3">
                                 <label for="example-date" class="form-label">To Date</label>
                                 <input class="form-control" id="example-date" type="month" name="date">
                              </div>
                           </div> -->
                  <!--         <div class="col-2 align-self-center">-->
                  <!--            <button type="button" class="btn btn-primary rounded-pill waves-effect waves-light" onclick="get_month_details()">Go</button>-->
                  <!--         </div>-->
                  <!--      </div>-->
                  <!--   </div>-->
                  <!--</div>-->
		</div>
		</div>
        <div class="row">
            <div class="col-md-8">
			
                <div class="card" style="overflow: overlay;height: 454px;">
                    <div class="card-body">


                        <h4 class="header-title mb-3" >Analytics</h4>
 <!--<div id="apex-line-1" class="apex-charts" data-colors="#348cd4,#f06292"></div>-->
 

 <div id="apex-line-1" class="apex-charts" data-colors="#f06292,#2ea507"></div>
                      <!---  <div class="table-responsive">
                      <!--      <div id="site_details_div"></div>-->
                      <!--  </div> --->
                    </div>




                </div>
                <div class="card mt-3">
                <div class="card-body">
                    <h4 class="header-title mb-0">Remark Wise Tasks</h4>
                        <div class="row">
  <div id="chartdiv"></div>
                        </div>
                </div>
            </div>
            <div class="col-xl-12">
            <!-- Portlet card -->
            <!--<div class="card">-->
            <!--    <div class="card-body">-->
            <!--        <div class="card-widgets">-->
            <!--            <a href="javascript: void(0);" data-toggle="reload"><i class="mdi mdi-refresh"></i></a>-->
            <!--            <a data-bs-toggle="collapse" href="#cardCollpase3" role="button" aria-expanded="false" aria-controls="cardCollpase3"><i class="mdi mdi-minus"></i></a>-->
            <!--            <a href="javascript: void(0);" data-toggle="remove"><i class="mdi mdi-close"></i></a>-->
            <!--        </div>-->
            <!--        <h4 class="header-title mb-0">Month Wise Report</h4>-->

            <!--        <div id="cardCollpase3" class="collapse pt-3 show" dir="ltr">-->
                        <!-- <div id="apex-line-2" class="apex-charts" data-colors="#f06292"></div> -->
            <!--            <div id="chart">-->
            <!--            </div>-->
            <!--        </div> <!-- collapsed end -->
            <!--    </div> <!-- end card-body -->
            <!--</div> <!-- end card-->
        </div> <!-- end col-->
            
            
            
            </div>
            
            <div class="col-md-4">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">


                                <h4 class="header-title mb-3" style="margin-bottom: 12px !important;">Action Not Taken</h4>
                                <div class="table-responsive">
                                    <div id="action_taken"></div>
                                </div> <!-- end table responsive-->

                            </div>
                        </div>
                    </div>
                </div>
                <div class="card" style="margin-bottom: 6px;">
                    <div class="card-body">

                        <h4 class="header-title mb-3" style="margin-bottom: 12px !important;">Age Wise Tasks</h4>

                        <div class="table-responsive">
                                    <!--<div id="top_most_complaints"></div>-->
                        <table id="top_most_complaints" class="table table-hover table-centered mb-0 overall-ststus">
                            <tr>
                                <td style="color: #808000;" onclick="new_external_window_print1(event,'folders/dashboardoi/print1.php','1_days','<?php echo $priority_type?>');">&nbsp;<=1 days</td>
                                <th id="less_1" style="color:  #808000;"></th>
                            </tr>
                            <tr>
                                <td style="color: #800080;" onclick="new_external_window_print1(event,'folders/dashboardoi/print1.php','<=5_days','<?php echo $priority_type?>');">&nbsp;<=5 days</td>
                                <th id="less_5" style="color: #800080;"></th>
                            </tr>
                            <tr>
                               <td style="color: #ff0000;" onclick="new_external_window_print1(event,'folders/dashboardoi/print1.php','<=10_days','<?php echo $priority_type?>');">&nbsp;<=10 days</td>
                                <th id="less_10" style="color: #ff0000;"></th>
                            </tr>
                            <tr>
                                <td style="color: #8a2be2;" onclick ="new_external_window_print1(event,'folders/dashboardoi/print1.php','<=15_days','<?php echo $priority_type?>');">&nbsp;<=15 days</td>
                                <th id="less_15" style="color: #8a2be2;"></th>
                            </tr>
							 <tr>
                                <td style="color: #bc170b;" onclick = "new_external_window_print1(event,'folders/dashboardoi/print1.php','<=30_days','<?php echo $priority_type?>');">&nbsp;<=30 days</td>
                                <th id="less_30" style="color: #bc170b;"></th>
                            </tr>
                            <tr>
                                <td style="color: blue;" onclick = "new_external_window_print1(event,'folders/dashboardoi/print1.php','>30_days','<?php echo $priority_type?>');">&nbsp;>30 days</td>
                                <th id="greater_30" style="color: #bc170b;"></th>
                            </tr>
                            <tr>
                                <td class="top style4 right" style="color: #343a40;">Total&nbsp;</td>
                                <th id="total_count"; font-width:"bold"style="color: #343a40;"><?php echo $total_count; ?></th>
                            </tr>
                        </table> 
                                </div> <!-- end table responsive-->
                    </div>
                </div>
            
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<?php 
//     // Create connection
// $conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// if($_SESSION['sess_department_name'] != 'All'){
//      if ($_SESSION['user_id'] != '5ff562ed542d625323') { 
//             $rep_dept = str_replace(',', "','", $_SESSION['sess_department_name']);
//         $where .= " and department_name in ('" . $rep_dept . "')";
//         }
// }
// if($_SESSION['sess_type_user'] == 1){
//             $where .= " and assign_by = '".$_SESSION['user_id']."'";
//         }
// if($_SESSION['sess_site_name'] != 'All'){
//     if($_SESSION['user_id'] != '5ff562ed542d625323'){
//       $rep_site = str_replace(',', "','", $_SESSION['sess_site_name']);
//       $where .= " and site_name in ('".$rep_site."')";
//     }
// }
// $where .=" and priority_type = '664ad1a16448664824'";
// // Fetch categories and data from the database
// $sql = "SELECT department_name, COUNT(*) AS complaint_count FROM complaint_creation where is_delete = 0 and  stage_1_status NOT IN ('2','3')". $where." GROUP BY department_name order by complaint_count DESC";
// $result = $conn->query($sql);

// // Initialize arrays for categories and data
// $categories = [];
// $data = [];

// // Populate the arrays
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
//         $dept_name = department_type($row['department_name'])[0]['department_type'];
//         $categories[] = $dept_name;
//         $data[] = (int)$row['complaint_count']; // Convert data to integer if needed
//     }
// } 
// // else {
// //     echo "0 results";
// // }

// // Convert the PHP arrays into JavaScript arrays
// $js_categories = json_encode($categories);
// $js_data = json_encode($data);

// // Close connection
// $conn->close();
?>

    <script>
     
        var options = {
          series:  [{
          data: <?php echo $js_data; ?>
        }],
        
          chart: {
          type: 'bar',
          height: 450,
         events: {
    dataPointSelection: function(event, chartContext, config) {
        var category = config.w.globals.labels[config.dataPointIndex];
        var count = config.w.globals.series[config.seriesIndex][config.dataPointIndex];
        var priority_name = <?php echo json_encode($priority_type); ?>;
        // Make AJAX request to fetch call details based on the department
        $.ajax({
            method: 'POST',
            data: { department: category },
            success: function(response) {
                // Display the call details in a modal or any other element
                // Example: Show call details in a modal
                $('#callDetailsModalBody').html(response);
                $('#callDetailsModal').modal('show');
                // Open the new page in a new window with department name as parameter in URL
                var url = 'folders/dashboardoi/chart_print.php';
                onmouseover = window.open(url + '?department=' + encodeURIComponent(category) + '&priority_name=' + encodeURIComponent(priority_name), 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
                event.preventDefault();
            }
        });
    }
}
        },
        plotOptions: {
          bar: {
            borderRadius: 2,
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: true
        },
        xaxis: {
          categories: <?php echo $js_categories; ?>,
        },
        fill: {
        colors: ['#00a726']
      }
        };

        var chart = new ApexCharts(document.querySelector("#chartbsar"), options);
        chart.render();
      
    </script>

<!--OWN CALLS-->
<?php
    // $conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");

    // // Check connection
    // if ($conn->connect_error) {
    //     die("Connection failed: " . $conn->connect_error);
    // }
    // // $where = " assign_by = '$sess_user_id'";
    
    // $wheres = '';

    //     if ($_SESSION['user_id'] != '5ff562ed542d625323') {
    //             $wheres .= "WHERE cc.assign_by IN ('" . $_SESSION['user_id'] . "')  ";}
        
    //     if ($_SESSION['user_id'] != '5ff562ed542d625323') {
    //             $wheres_like .= " and c.assign_by IN ('" . $_SESSION['user_id'] . "')  ";}

    //   $sql_own_calls = "SELECT rc.remark, COUNT(*) AS remark_count 
    //                     FROM (
    //                     SELECT s.screen_unique_id, s.remark_type 
    //                     FROM `stage_1` AS s 
    //                     JOIN (
    //                         SELECT screen_unique_id, MAX(date_time) AS max_timestamp 
    //                         FROM `stage_1` 
    //                         GROUP BY screen_unique_id
    //                     ) AS max_timestamps ON s.screen_unique_id = max_timestamps.screen_unique_id 
    //                     AND s.date_time = max_timestamps.max_timestamp 
    //                     WHERE s.remark_type != '' AND s.is_delete = 0
    //                 ) AS last_updated_remark 
    //                 JOIN remark_creation AS rc ON last_updated_remark.remark_type = rc.unique_id 
    //                 JOIN complaint_creation AS cc ON last_updated_remark.screen_unique_id = cc.screen_unique_id 
    //                  $wheres and cc.priority_type = '664ad1a16448664824' and cc.stage_1_status != 3 
    //                 GROUP BY rc.remark UNION
    //                 SELECT 'Pending' as remark_type, COUNT(*) AS remark_count FROM complaint_creation as c WHERE c.stage_1_status = 0 and c.is_delete = 0 $wheres_like and c.priority_type = '664ad1a16448664824'";
                    
    //                 // echo "SELECT rc.remark, COUNT(*) AS remark_count 
    //                 //     FROM (
    //                 //     SELECT s.screen_unique_id, s.remark_type 
    //                 //     FROM `stage_1` AS s 
    //                 //     JOIN (
    //                 //         SELECT screen_unique_id, MAX(date_time) AS max_timestamp 
    //                 //         FROM `stage_1` 
    //                 //         GROUP BY screen_unique_id
    //                 //     ) AS max_timestamps ON s.screen_unique_id = max_timestamps.screen_unique_id 
    //                 //     AND s.date_time = max_timestamps.max_timestamp 
    //                 //     WHERE s.remark_type != '' AND s.is_delete = 0
    //                 // ) AS last_updated_remark 
    //                 // JOIN remark_creation AS rc ON last_updated_remark.remark_type = rc.unique_id 
    //                 // JOIN complaint_creation AS cc ON last_updated_remark.screen_unique_id = cc.screen_unique_id 
    //                 //  $wheres and cc.priority_type = '664ad1a16448664824' 
    //                 // GROUP BY rc.remark UNION
    //                 // SELECT 'Pending' as remark_type, COUNT(*) AS remark_count FROM complaint_creation as c WHERE c.stage_1_status = 0 and c.is_delete = 0 $wheres_like and c.priority_type = '664ad1a16448664824'";

    // $result_own_calls = $conn->query($sql_own_calls);
    
    // // Initialize arrays for categories and data
    // $categories_own_calls = []; 
    // $data_own_calls = [];

    // if ($result_own_calls->num_rows > 0) {
    //     //echo "ho";
    //     while ($row = $result_own_calls->fetch_assoc()) {
    //         $categories_own_calls[] = $row['remark'];
    //         $data_own_calls[] = (int)$row['remark_count']; 
    //     }
    // }

    // // Convert the PHP arrays into JavaScript arrays
    // $js_categories_own_calls = json_encode($categories_own_calls);
    // $js_data_own_calls = json_encode($data_own_calls);

    // // Close connection
    // $conn->close();
?>

<script>
    var options = {
        series: [{
            data: <?php echo $js_data_own_calls; ?>
        }],
        chart: {
            type: 'bar',
            height: 450,
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var category = config.w.globals.labels[config.dataPointIndex];
                    var count = config.w.globals.series[config.seriesIndex][config.dataPointIndex];
                    var priority_name = <?php echo json_encode($priority_type); ?>;
                   $.ajax({
                        method: 'POST',
                        data: {owncalls: category },
                        success: function(response) {
                            // Display the call details in a modal or any other element
                            $('#callDetailsModalBody').html(response);
                            $('#callDetailsModal').modal('show');
                            // var url;
                            if (category === "Pending") {
                                url = 'folders/dashboardoi/print_pending.php';
                            } else {
                                url = 'folders/dashboardoi/own_call_print.php';
                            }
                            
                          onmouseover = window.open(url + '?remark=' + encodeURIComponent(category) + '&priority_name=' + encodeURIComponent(priority_name), 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
                            event.preventDefault();
        
                        }
                    });
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 2,
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories:<?php echo $js_categories_own_calls; ?>,
        },
        
        fill: {
            colors: ['#00a726']
        }
    };

    var chart = new ApexCharts(document.querySelector("#chartowncalls"), options);
    // alert(chart);
    chart.render();
</script>

<!--TAGGED CALLS-->
<?php
    $conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");
    
    // Check connection
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }
    // $sql_tagged_calls = "SELECT (SELECT remark FROM remark_creation WHERE unique_id = s.remark_type) AS remark, COUNT(DISTINCT s.sess_user_id) AS user_count FROM stage_1 AS s WHERE s.is_delete = 0 GROUP_BY remark";
    // $sql_tagged_calls = "SELECT s.remark_type, COUNT(DISTINCT CONCAT(s.remark_type, s.sess_user_id)) AS user_count FROM stage_1 AS s WHERE s.remark_type != '' AND s.is_delete = 0 GROUP BY s.remark_type";

//  $sql_tagged_calls = "SELECT s.remark_type, COUNT(DISTINCT s.sess_user_id) AS  user_count 
// FROM stage_1 AS s WHERE s.remark_type != '' AND s.is_delete = 0 GROUP BY s.remark_type";

$user_id = $_SESSION['user_id']; 










 $sql_tagged_calls ="SELECT
    r.remark,
    COUNT(s1.user_name_select) AS user_count,
    s1.remark_type
FROM
    stage_1 s1
JOIN
    remark_creation r ON s1.remark_type = r.unique_id
JOIN
    complaint_creation cc ON cc.screen_unique_id = s1.screen_unique_id
WHERE
    s1.sess_user_id = '$user_id'
    AND s1.is_delete = 0
    AND s1.user_name_select != ''
    AND s1.print_status = 0
    AND cc.stage_1_status != 2
    AND cc.priority_type = '664ad1a16448664824'
GROUP BY
    r.remark";


   
    


     $result_tagged_calls = $conn->query($sql_tagged_calls);
    
    // Initialize arrays for categories and data
    $categories_tagged_calls = [];
    $data_tagged_calls = [];
    
    if ($result_tagged_calls->num_rows > 0) {
   
      while ($row = $result_tagged_calls->fetch_assoc()) {
        //   $remarks_data = remark_type($row['remark_type']);
          
        //   if ($remarks_data && isset($remarks_data[0]['remark'])) {
        //     $categories_own_calls[] = $remarks_data[0]['remark'];
        // } 
        $categories_tagged_calls[] = $row['remark'];
        
$remark_type=$row['remark_type'];
// echo $remark_type;
        $data_tagged_calls[] = (int)$row['user_count'];
      }
    }

    $js_categories_tagged_calls = json_encode($categories_tagged_calls);
    $js_data_tagged_calls = json_encode($data_tagged_calls);

    // Close connection
    $conn->close();
?>



<script>
    var options = {
        series: [{
            data: <?php echo $js_data_tagged_calls; ?>
        }],
        chart: {
            type: 'bar',
            height: 450,
            events: {
                dataPointSelection: function(event, chartContext, config) {
                    var category = config.w.globals.labels[config.dataPointIndex];
                    var count = config.w.globals.series[config.seriesIndex][config.dataPointIndex];
                    var priority_name = <?php echo json_encode($priority_type); ?>;
                   
                    $.ajax({
                        method: 'POST',
                        data: {taggedcalls: category },
                        success: function(response) {
                            // alert(data);
                            // Display the call details in a modal or any other element
                            $('#callDetailsModalBody').html(response);
                            $('#callDetailsModal').modal('show');
                           
                            var url = 'folders/dashboardoi/tag_chart_calls.php';
                            onmouseover = window.open(url + '?remark=' + encodeURIComponent(category) + '&priority_name=' + encodeURIComponent(priority_name), 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
                            event.preventDefault();
                        }
                    });
                }
            }
        },
        plotOptions: {
            bar: {
                borderRadius: 2,
                horizontal: true,
            }
        },
        dataLabels: {
            enabled: true
        },
        xaxis: {
            categories:<?php echo $js_categories_tagged_calls; ?>,
        },
        
        fill: {
            colors: ['#00a726']
        }
    };

    var chart = new ApexCharts(document.querySelector("#charttaggedcalls"), options);
    // alert(chart);
    chart.render();
</script>

   

    

<?php 
// Create connection
// $conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// if($_SESSION['sess_department_name'] != 'All'){
//      if ($_SESSION['user_id'] != '5ff562ed542d625323') {
//             $rep_dept = str_replace(',', "','", $_SESSION['sess_department_name']);
//         $where .= " and department_name in ('" . $rep_dept . "')";
//         }
// }
// if($_SESSION['sess_type_user'] == 1){
//             $where .= " and assign_by = '".$_SESSION['user_id']."'";
//         }
// if($_SESSION['sess_site_name'] != 'All'){
//     if($_SESSION['user_id'] != '5ff562ed542d625323'){
//       $rep_site = str_replace(',', "','", $_SESSION['sess_site_name']);
//       $where .= " and site_name in ('".$rep_site."')";
//     }
// }

// $where .=" and priority_type = '664ad1a16448664824'";
// // Fetch categories and data from the database
//  $sql_query = "SELECT site_name, COUNT(*) AS complaint_count FROM complaint_creation where is_delete = 0 and stage_1_status = '1'". $where." GROUP BY site_name order by complaint_count DESC";
// //  echo "SELECT site_name, COUNT(*) AS complaint_count FROM complaint_creation where is_delete = 0 and stage_1_status != 2". $where." GROUP BY site_name order by complaint_count DESC";
// $result = $conn->query($sql_query);

// // Initialize arrays for categories and data
// $categories1 = [];
// $data1 = [];


// // // Populate the arrays

// // Populate the arrays
// if ($result->num_rows > 0) {
//     while ($row = $result->fetch_assoc()) {
        
//         $site_name = site_type($row['site_name'])[0]['site_name'];
//         $categories1[] = $site_name;
//         $data1[] = (int)$row['complaint_count']; 
//     }
// } 
// // else {
// //     echo "0 results";
// // }

// // Convert the PHP arrays into JavaScript arrays
// $js_categories1 = json_encode($categories1);
// $js_data1 = json_encode($data1);

// // Close connection
// $conn->close();
?>

    <script>
     
        var options = {
          series: [{
          data: <?php echo $js_data1; ?>
        }],
        
          chart: {
          type: 'bar',
          height: 450,
          events: {
    dataPointSelection: function(event, chartContext, config) {
        var category = config.w.globals.labels[config.dataPointIndex];
        var count = config.w.globals.series[config.seriesIndex][config.dataPointIndex];
        var priority_name = <?php echo json_encode($priority_type); ?>;
        // Make AJAX request to fetch call details based on the site
        $.ajax({
            url: 'folders/dashboardoi/chart_print.php',
            method: 'POST',
            data: { site: category },
            success: function(response) {
                // Display the call details in a modal or any other element
                // Example: Show call details in a modal
                $('#callDetailsModalBody').html(response);
                $('#callDetailsModal').modal('show');
                // Open the new page in a new window with site name as parameter in URL
                var url = 'folders/dashboardoi/chart_print_site.php';
                onmouseover = window.open(url + '?site=' + encodeURIComponent(category) + '&priority_name=' + encodeURIComponent(priority_name), 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
                event.preventDefault();
            }
        });
    }
}
        },
        plotOptions: {
          bar: {
            borderRadius: 2,
            horizontal: true,
          }
        },
        dataLabels: {
          enabled: true
        },
        xaxis: {
          categories: <?php echo $js_categories1; ?>,
        },
        fill: {
        colors: ['#00a726']
      }
        };

        var chart = new ApexCharts(document.querySelector("#chartsbar"), options);
        chart.render();
      
    </script>

<style>
        #chartdiv {
            width: 100%;
            height: 400px;
        }

        #chartdiv2 {
            width: 100%;
            height: 280px;
        }
        
        .dept-tab {
    background-color: #2196F3;
    color: white;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 5px 0 0 5px;
}

.site-tab {
    background-color: #4CAF50;
    color: white;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 0 0 0 0;
}

.own-calls-tab {
    background-color: #E75480;
    color: white;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 0 0 0 0;
}

.tagged-calls-tab {
    background-color: #8A2BE2;
    color: white;
    border: none;
    padding: 6px 12px;
    cursor: pointer;
    border-radius: 0 5px 5px 0;
}


/* Style the tab content */
.tabcontent {
    display: none;
    padding: 20px;
    /*border-top: 1px solid #ccc;*/
}

.tabcontent h4 {
    margin-top: 0;
}



.tablink:hover {
    background-color: #6c757d;
}

.tablink.active {
    background-color: #ccc;
}


    </style>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/waypoints/2.0.3/waypoints.min.js"></script>
    <script src="https://bfintal.github.io/Counter-Up/jquery.counterup.min.js"></script>
    <script>
        jQuery(document).ready(function($) {
            $('.counter').counterUp({
                delay: 10,
                time: 2000
            });
        });
    </script>

    <!-- Resources -->
    <script src="https://cdn.amcharts.com/lib/5/index.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/percent.js"></script>
    <script src="https://cdn.amcharts.com/lib/5/themes/Animated.js"></script>

    <!-- Chart code -->
   
    
    <script>
        document.getElementById("defaultOpen").click();

        function openTab(tabName) {
            var i, tabcontent, tablinks;
            tabcontent = document.getElementsByClassName("tabcontent");
            for (i = 0; i < tabcontent.length; i++) {
                tabcontent[i].style.display = "none";
            }
            document.getElementById(tabName).style.display = "block";
        }

    </script>

    
<?php

$conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");

// // Check connection
// if ($conn->connect_error) {
//     die("Connection failed: " . $conn->connect_error);
// }
// if($_SESSION['sess_department_name'] != 'All'){
//      if ($_SESSION['user_id'] != '5ff562ed542d625323') { 
//             $rep_dept = str_replace(',', "','", $_SESSION['sess_department_name']);
//         $where .= " and department_name in ('" . $rep_dept . "')";
//         }
// }
// if($_SESSION['sess_type_user'] == 1){
//             $where .= " and assign_by = '".$_SESSION['user_id']."'";
//         }
// if($_SESSION['sess_site_name'] != 'All'){
//     if($_SESSION['user_id'] != '5ff562ed542d625323'){
//       $rep_site = str_replace(',', "','", $_SESSION['sess_site_name']);
//       $where .= " and site_name in ('".$rep_site."')";
//     }
// }

// if($_SESSION['sess_department_name'] != 'All'){
//      if ($_SESSION['user_id'] != '5ff562ed542d625323') { 
//             $rep_depts = str_replace(',', "','", $_SESSION['sess_department_name']);
//         $wheres .= " and cc.department_name in ('" . $rep_depts . "')";
//         }
// }
// if($_SESSION['sess_type_user'] == 1){
//             $wheres .= " and cc.assign_by = '".$_SESSION['user_id']."'";
//         }
// if($_SESSION['sess_site_name'] != 'All'){
//     if($_SESSION['user_id'] != '5ff562ed542d625323'){
//       $rep_sites = str_replace(',', "','", $_SESSION['sess_site_name']);
//       $wheres .= " and cc.site_name in ('".$rep_sites."')";
//     }
// }
$wheres .= " and cc.priority_type = '664ad1a16448664824'";
 $query_analytics = "SELECT 
                        date, 
                        SUM(completed_call_count) AS completed_call_count, 
                        SUM(new_call_count) AS new_call_count, 
                        SUM(response_call_count) AS response_call_count 
                    FROM (
                        SELECT 
                            DATE(entry_date) AS date, 
                            0 AS completed_call_count, 
                            COUNT(id) AS new_call_count, 
                            0 AS response_call_count 
                        FROM 
                            complaint_creation 
                        WHERE 
                            entry_date >= DATE_SUB(CURDATE(), INTERVAL 15 DAY) 
                            AND is_delete != 1 ".$where." 
                        GROUP BY 
                            DATE(entry_date)
                        
                        UNION ALL
                        SELECT 
                            DATE(stage_1_update_date) AS date, 
                            COUNT(CASE WHEN stage_1_status = 2 THEN id ELSE NULL END) AS completed_call_count, 
                            0 AS new_call_count, 
                            0 AS response_call_count 
                        FROM 
                            complaint_creation 
                        WHERE 
                            stage_1_update_date >= DATE_SUB(CURDATE(), INTERVAL 15 DAY) 
                            AND is_delete != 1 ".$where." 
                        GROUP BY 
                            DATE(stage_1_update_date)
                        
                        UNION ALL
                        
                        SELECT Date(s1.date_time)                  AS date,
              0                                AS completed_call_count,
              0                                AS new_call_count,
              Count(DISTINCT s1.screen_unique_id) AS response_call_count
        FROM   stage_1 s1
        LEFT JOIN
        complaint_creation cc ON s1.screen_unique_id = cc.screen_unique_id
        WHERE  s1.date_time >= Date_sub(Curdate(), INTERVAL 15 day)
              AND s1.is_delete != 1 ".$where." 
                        GROUP BY 
                            DATE(s1.date_time)
                    ) AS combined_data 
                    GROUP BY 
                        date";

$result_analytics = $conn->query($query_analytics);

// Fetch data into an associative array
$data = [];
if ($result_analytics->num_rows > 0) {
    while ($row = $result_analytics->fetch_assoc()) {
        $data[] = [
            'date' => $row['date'],
            'new_call_count' => (int)$row['new_call_count'],
            'completed_call_count' => (int)$row['completed_call_count'],
            'response_call_count' => (int)$row['response_call_count'] // Adding response_call_count
        ];
    }
}

// Convert PHP array to JavaScript object
$data_json = json_encode($data);

$conn->close();

?>



<script>
    var countData = <?php echo $data_json; ?>;
    
var colors = ['#6658dd', '#f7b84b', '#2ea507']; // Default colors for New, Completed, and Response respectively
var dataColors = $("#apex-line-1").data('colors');
if (dataColors) {
    colors = dataColors.split(",");
}

// Change the color for the Response series
// colors[2] = '#57df1c'; // Change to any color you prefer for the Response series
colors[2] = '#6658dd'; 

var options = {
    chart: {
        height: 380,
        type: 'line',
        zoom: {
            enabled: false
        },
        toolbar: {
            show: false
        }
    },
    colors: colors, // Use the modified colors array
    dataLabels: {
        enabled: true,
    },
    stroke: {
        width: [1],
        curve: 'smooth'
    },
    series: [{
        name: "New",
        data: countData.map(item => item.new_call_count)
    },
    {
        name: "Completed",
        data: countData.map(item => item.completed_call_count)
    },
    {
        name: "Response",
        data: countData.map(item => item.response_call_count)
    }],
    title: {
        text: 'Analytics',
        align: 'left',
        style: {
            fontSize: "14px",
            color: '#666'
        }
    },
    grid: {
        row: {
            colors: ['transparent'],
            opacity: 0.2
        },
        borderColor: '#f1f3fa'
    },
    markers: {
        style: 'inverted',
        size: 8
    },
    xaxis: {
        categories: countData.map(item => item.date),
        title: {
            // text: 'Month'
        }
    },
    yaxis: {
        title: {
            // text: 'Temperature'
        },
        min: 0,
        // max: 40
    },
    legend: {
        position: 'top',
        horizontalAlign: 'right',
        floating: true,
        offsetY: -25,
        offsetX: -5
    },
    responsive: [{
        breakpoint: 600,
        options: {
            chart: {
                toolbar: {
                    show: false
                }
            },
            legend: {
                show: false
            },
        }
    }]
};

var chart = new ApexCharts(
    document.querySelector("#apex-line-1"),
    options
);

chart.render();

</script>



<script src="assets/js/amcharts.js"></script>
<script src="assets/js/pie.js"></script>

<?php

$conn = new mysqli("localhost", "zigma", "?WSzvxHv1LGZ", "zigma_complaints");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize the $wheres variable
$wheres = '';

if ($_SESSION['sess_department_name'] != 'All') {
    if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        $rep_depts = str_replace(',', "','", $_SESSION['sess_department_name']);
        $wheres .= " cc.department_name IN ('" . $rep_depts . "') AND ";
    }
}

if ($_SESSION['sess_type_user'] == 1) {
    $wheres .= " cc.assign_by = '" . $_SESSION['user_id'] . "' AND ";
}

if ($_SESSION['sess_site_name'] != 'All') {
    if ($_SESSION['user_id'] != '5ff562ed542d625323') {
        $rep_sites = str_replace(',', "','", $_SESSION['sess_site_name']);
        $wheres .= " cc.site_name IN ('" . $rep_sites . "') AND ";
    }
}
$wheres .= " cc.is_delete = 0 and cc.stage_1_status != 2 and cc.priority_type = '664ad1a16448664824'";
// Remove the trailing "AND" if present
$wheres = rtrim($wheres, " AND ");

 $query_analytics = "SELECT rc.remark, COUNT(*) AS remark_count 
                    FROM (
                        SELECT s.screen_unique_id, s.remark_type 
                        FROM `stage_1` AS s 
                        JOIN (
                            SELECT screen_unique_id, MAX(date_time) AS max_timestamp 
                            FROM `stage_1` 
                            GROUP BY screen_unique_id
                        ) AS max_timestamps ON s.screen_unique_id = max_timestamps.screen_unique_id  WHERE s.remark_type != ''
                        AND s.date_time = max_timestamps.max_timestamp 
                    ) AS last_updated_remark 
                    JOIN remark_creation AS rc ON last_updated_remark.remark_type = rc.unique_id 
                    JOIN complaint_creation AS cc ON last_updated_remark.screen_unique_id = cc.screen_unique_id and cc.stage_1_status != 3 WHERE
                     $wheres  
                    GROUP BY rc.remark";
// echo "SELECT rc.remark, COUNT(*) AS remark_count 
//                     FROM (
//                         SELECT s.screen_unique_id, s.remark_type 
//                         FROM `stage_1` AS s 
//                         JOIN (
//                             SELECT screen_unique_id, MAX(date_time) AS max_timestamp 
//                             FROM `stage_1` 
//                             GROUP BY screen_unique_id
//                         ) AS max_timestamps ON s.screen_unique_id = max_timestamps.screen_unique_id  WHERE s.remark_type != ''
//                         AND s.date_time = max_timestamps.max_timestamp 
//                     ) AS last_updated_remark 
//                     JOIN remark_creation AS rc ON last_updated_remark.remark_type = rc.unique_id 
//                     JOIN complaint_creation AS cc ON last_updated_remark.screen_unique_id = cc.screen_unique_id where
//                      $wheres  
//                     GROUP BY rc.remark";
$result_analytics = $conn->query($query_analytics);

// Fetch data into an associative array
$data = [];
if ($result_analytics->num_rows > 0) {
    while ($row = $result_analytics->fetch_assoc()) {
        $data[] = [
            'remark' => $row['remark'],
            'remark_count' => (int)$row['remark_count']
        ];
    }
}

// Convert PHP array to JavaScript object
$data_json = json_encode($data);

$conn->close();

?>

<script>
    var chart = AmCharts.makeChart("chartdiv", {
    "type": "pie",
    "dataProvider": <?php echo $data_json; ?>,
    "valueField": "remark_count",
    "titleField": "remark",
    "colorField": "color", // Assuming you have a color field in your data, if not remove this line
    "theme": "light", // Set the theme (light or dark)

    // Add a legend
    "legend": {
        "align": "center",
        "position": "bottom",
        "autoMargins": true,
        "markerType": "circle"
    },

    // Add a balloon (tooltip) to display additional information
    "balloon": {
        "fixedPosition": true
    },

    // Add animation to the chart
    "startDuration": 1,

    // Add labels to the slices
    "labelRadius": 1,
    "labelText": "[[title]]: [[percents]]%",

    // Customize colors for each slice
    "colors": ["#ff9f00", "#007bff", "#28a745", "#dc3545", "#6610f2", "#6c757d", "#fd7e14"],

    // Set the radius of the pie chart
    "radius": "35%", // Adjust the percentage value to increase or decrease the size

    // Event listener for slice click
    "listeners": [{
        "event": "clickSlice",
        "method": function(event) {
            // Get the clicked category and count
            var category = event.dataItem.dataContext.remark;
            var count = event.dataItem.dataContext.remark_count;
            var priority_name = <?php echo json_encode($priority_type); ?>;
            
            // Perform your action here, such as displaying more information about the clicked slice
            // For example, you can show an alert with the category and count
          var url = 'folders/dashboardoi/pie_chart_print.php';
        onmouseover = window.open(url + '?remark=' + encodeURIComponent(category) + '&priority_name=' + encodeURIComponent(priority_name), 'onmouseover', 'height=550,width=2000,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
        event.preventDefault();
        }
    }]
});

// Add animation to update data
function test() {
    chart.animateAgain();
}


</script>
    