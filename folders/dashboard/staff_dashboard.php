<style>
button {
  -webkit-appearance: none;
     -moz-appearance: none;
          appearance: none;
  font-family: inherit;
  font-size: inherit;
  border: none;
  background: none;
  cursor: pointer;
}

.wrapper {
  width: min(100% - 2rem, 90ch);
  margin-inline: auto;
  padding-top: 1rem;
  display: flex;
  flex-direction: column;
}

.accordion {
  width: 100%;
}
.accordion__panel {
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, 0.125);
  overflow: hidden;
  transition: height 0.5s ease-in-out;
}
.accordion__panel:first-of-type {
  border-top-left-radius: 0.25rem;
  border-top-right-radius: 0.25rem;
}
.accordion__panel:not(:first-of-type) {
  border-top: 0;
}
.accordion__panel:last-of-type {
  border-bottom-right-radius: 0.25rem;
  border-bottom-left-radius: 0.25rem;
}
.is-active .accordion__heading {
  box-shadow: inset 0 -1px 0 rgba(0, 0, 0, 0.13);
}
.accordion__btn {
  display: flex;
  align-items: center;
  padding: 10px;
  width: 100%;
  font-size: 15px;
  background: #dfdcd7;
}
.is-active .accordion__btn {
    background-color: #2196f3;
    color: #fff;
}
.accordion__btn::after {
  content: "";
  flex-shrink: 0;
  width: 1.25rem;
  aspect-ratio: 1;
  margin-left: auto;
  background-color: #000;
  -webkit-mask-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
          mask-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23000'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
  transition: transform 0.2s ease-in-out;
}
.is-active .accordion__btn::after {
  background-color: #fff;
  transform: rotate(180deg);
}
.accordion__content {
  display: none;
  margin-top: -1px;
  transition: height 0.35s ease-in-out;
  overflow: hidden;
}
.is-active .accordion__content {
  display: block;
}
.accordion__inner {
  padding: 1rem 1.25rem;
}

.src {
  font-size: 0.85rem;
  margin-top: 2rem;
}
.accordion__inner {
    background: #f7f7f7;
}
.text-muted {
    color: #000000!important;
}
ul.list-notes {
    margin-bottom: 0px;
}
ul.list-notes li {
    font-size: 14px;
    margin: 0px;
    color: #000;
    margin-bottom: 6px;
}
.count-name {
    text-align: end;
    margin: 0px;
    color: #0563ad;
    font-weight: 600;
    font-size: 17px;
}
</style>
<?php
//Check Out count
function get_check_out_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
    $date = date('Y-m-d');
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "entry_date",
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];
    $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 1 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date <= '".$date."'";

    $check_out_count = $pdo->select($table_details, $where);
    $result_array = $check_out_count->data;
    $chk_out_cnt = '0';
    $main_cnt    = '0';
    foreach($result_array as $val)
    {
        $main_cnt = $main_cnt;
        $main_cnt++;
        $table_columns_sub = [
            "entry_date",
            
        ];
    
        $table_details_sub = [
            $table_name,
            $table_columns_sub
        ];
        $where_sub  = "is_active = 1 AND is_delete = 0 AND attendance_type = 2 AND entry_date='".$val['entry_date']."' and staff_id = '".$staff_id."'";
        $check_out_count_sub = $pdo->select($table_details_sub, $where_sub);
        $result_array_sub = $check_out_count_sub->data;

        foreach($result_array_sub as $val_sub)
        {
            $chk_out_cnt = $chk_out_cnt;
            $chk_out_cnt++;
        }
    }
    $count  =   $main_cnt - $chk_out_cnt;

        return $count;
}

//Late count
function get_late_count($month,$year,$staff_id) {
    global $pdo;

    $current_month = $year."-".$month;
     $date = date('Y-m-d');
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as late_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

   $where  = "is_active = 1 AND is_delete = 0 AND attendance_type = 1 AND day_status = 2 AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date < '".$date."'";
    

    $late_count = $pdo->select($table_details, $where);

    if (!($late_count->status)) {

        print_r($late_count);

    } else {

        $late_count  = $late_count->data[0];

        $late_cnt    = $late_count['late_count'];
        
    }
        return $late_cnt;
}

//Permission count
function get_permission_count($month,$year,$staff_id) {
    global $pdo;
     $date = date('Y-m-d');
    $current_month = $year."-".$month;
    $table_name    = "daily_attendance";
    $where         = [];
    $table_columns = [
        "COUNT(day_status) as permission_count",
        
    ];

    $table_details = [
        $table_name,
        $table_columns
    ];

    

   $where  = "is_active = 1 AND is_delete = 0 AND day_status = 3 AND attendance_type = 1  AND entry_date like '%".$current_month."%' and staff_id = '".$staff_id."' and entry_date < '".$date."'";
    

    $permission_count = $pdo->select($table_details, $where);

    if (!($permission_count->status)) {

        print_r($permission_count);

    } else {

        $permission_count  = $permission_count->data[0];

        $permission_cnt    = $permission_count['permission_count'];
        
    }
        return $permission_cnt;
}
$is_team_head = 0;
$team_members = 0;
$staff_name   = "";

$dash_show    = 0;

$user_columns = [
    "is_team_head",
    "(SELECT staff_name FROM staff WHERE unique_id = user.staff_unique_id) AS staff_name",
    "team_members",
    "staff_unique_id"
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
    $staff_id     = $user_data['staff_unique_id'];
    $is_team_head = $user_data['is_team_head'];
    $team_members = $user_data['team_members'];


    
} else {
    print_r($user_result);
}


$staff_id  = $staff_id;
$month = date('m');
$year  = date('Y');


$no_of_check_out_cnt    = get_check_out_count($month,$year,$staff_id);
if($no_of_check_out_cnt)
{
    $no_of_check_out = $no_of_check_out_cnt;
}
else
{
    $no_of_check_out = 0;
}

$no_of_permission_cnt    = get_permission_count($month,$year,$staff_id);
if($no_of_permission_cnt)
{
    $no_of_permission = $no_of_permission_cnt;
}
else
{
    $no_of_permission = 0;
}
$no_of_late_cnt         = get_late_count($month,$year,$staff_id);
    
if($no_of_late_cnt)
{
    $no_of_late = $no_of_late_cnt;
}
else
{
    $no_of_late = 0;
}
?>
<style>
    .cls-left {
    text-align: left;
}
.cls-right {
    text-align: right;
}
.bdr_btm {
    border-bottom: dotted 1px black;
}
.count-name {
    text-align: end;
    margin: 0px;
}
h4.name {
    font-size: 14px;
    margin: 0px;
}
hr.tag-line {
    margin: 11px 0px;
}
</style>
    <div class="col-xl-4 col-md-4">
        <div class="card">
            <div class="card-body">
                <div class="row border-bottom border-secondary">
                    <!-- <div class="col-12"> -->
                    <!-- </div> -->
                    <div class="col-md-6">
                        <h3 class="header-title text-center"><?php echo $staff_name; ?></h3>
                        <input type = 'hidden' name = 'staff_id' id = 'staff_id' value = "<?=$staff_id;?>">
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
                        <h2 class="text-green text-bold" id = 'working_days'>00</h2>
                        <p class="text-muted font-15 mt-n1 text-truncate">Working Days</p>
                    </div>
                    <div class="col-4 border-right border-secondary">
                        <h2 class="text-green text-bold" id = 'worked_days'>00</h2>
                        <p class="text-muted font-15 mt-n1 text-truncate">Worked Days</p>
                    </div>
                    <div class="col-4">
                        <h2 class="text-danger text-bold" id = 'absent_days'>00</h2>
                        <p class="text-muted font-15 mt-n1 text-truncate">Absent Days</p>
                    </div>
                    <!--<div class="col-3">-->
                    <!--    <h2 class="text-danger text-bold" id = 'leave_days'>00</h2>-->
                    <!--    <p class="text-muted font-15 mt-n1 text-truncate">CL Days</p>-->
                    <!--</div>-->
                </div>
                <div class="row mt-1 text-center mb-3">
                    <div class="col-12">
                        <!-- <i class="mdi mdi-transit-transfer mdi-48px text-primary"></i> 
                        <h4 class="text-primary text-bold">                            
                            Attendance
                        </h4> -->
                        <a href="index.php?file=daily_attendances/create">
                        <!-- <button type="button" class="btn btn-primary btn-rounded progress-bar-striped progress-bar-animated">
                            <i class="mdi mdi-transit-transfer mdi-24px text-white"></i>  
                        </button> -->
                        <button type="button" class="btn btn-primary btn-rounded">
                            Punch Attendance
                            <!-- <img src="img/attendance.png" alt="Attendance" weight="100" height="100"> -->
                        </button>
                        </a>
                        <a href="index.php?file=leave_permission/create">
                        <!-- <img src="img/request.png" alt="Leave / Permission Request" weight="100" height="100"> -->

                        <button type="button" class="btn btn-warning btn-rounded">
                            Leave / Permission Request
                        </button>
                        </a>
                        <!-- <p class="text-muted font-15 mt-n1 text-truncate">Working Days</p> -->
                    </div>
                </div>
                <hr>
                
                  <div class="wrapper">
	<div class="accordion">
		<div class="accordion__panel is-active">
			<div class="accordion__heading"><button class="accordion__btn">Attendance</button></div>
			<div class="accordion__content" style="box-sizing: border-box; display: block;">
				<div class="accordion__inner">
				    <div class="row">
				        <div class="col-md-9">
				           <h4 class="name">Casual Leave Count</h4> 
				        </div>
				        <div class="col-md-3">
				           <h5 class="count-name" id = 'casual_leave'>0</h5> 
				        </div>
				    </div>
				    <hr class="tag-line">
				     <div class="row">
				        <div class="col-md-9">
				           <h4 class="name">Not Check Out Punch Count</h4> 
				        </div>
				        <div class="col-md-3">
				           <h5 class="count-name" id = 'not_punch'><?php echo $no_of_check_out; ?></h5> 
				        </div>
				    </div>
				     <hr class="tag-line">
				     <div class="row">
				        <div class="col-md-9">
				           <h4 class="name">Late Count</h4> 
				        </div>
				        <div class="col-md-3">
				           <h5 class="count-name" id = 'late_count'><?php echo $no_of_late; ?></h5> 
				        </div>
				    </div>
				     <hr class="tag-line">
				     <div class="row">
				        <div class="col-md-9">
				           <h4 class="name">Permission Count</h4> 
				        </div>
				        <div class="col-md-3">
				           <h5 class="count-name" id = 'permission_count'><?php echo $no_of_permission; ?></h5> 
				        </div>
				    </div>

               	</div>
			</div>
		</div>
		<div class="accordion__panel">
			<div class="accordion__heading"><button class="accordion__btn">Notes</button></div>
			<div class="accordion__content" style="display: none; box-sizing: border-box;">
				<div class="accordion__inner">
                 <div class="row ">
                    <ul class="list-notes">
                        <li>3 Permissions Calcualted as a 1 Day Leave.</li>
                        <li>2 Late Calcualted as a 1 Permission.</li>
                    </ul>
                </div>				</div>
			</div>
		</div>
	
	</div>
</div>
                
                
            </div>
        </div>
    </div>
    
<script id="rendered-js">
const accordion = document.querySelector(".accordion");
const accordionPanels = document.querySelectorAll(".accordion__panel");
const accordionTriggers = document.querySelectorAll(".accordion__btn");
let speedAnimation = 300;

accordionTriggers.forEach(trigger => {
  trigger.addEventListener("click", e => {
    let panel = trigger.parentNode.parentNode.querySelector(
    ".accordion__content");

    if (e.target.parentNode.parentNode.classList.contains("is-active")) {
      slideUp(panel, speedAnimation);

      panel.addEventListener(
      "transitionrun",
      () => {
        trigger.parentNode.parentNode.classList.remove("is-active");
      },
      { once: true });

    } else {
      accordionPanels.forEach(function (item) {
        if (item.classList.contains("is-active")) {
          slideUp(item.querySelector(".accordion__content"), speedAnimation);

          item.querySelector(".accordion__content").addEventListener(
          "transitionrun",
          () => {
            item.classList.remove("is-active");
          },
          { once: true });

        }
      });
      trigger.parentNode.parentNode.classList.add("is-active");
      slideDown(panel, speedAnimation);
    }
  });
});

let slideUp = (target, duration = 500) => {
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.boxSizing = "border-box";
  target.style.height = target.offsetHeight + "px";
  target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  window.setTimeout(() => {
    target.style.display = "none";
    target.style.removeProperty("height");
    target.style.removeProperty("padding-top");
    target.style.removeProperty("padding-bottom");
    target.style.removeProperty("margin-top");
    target.style.removeProperty("margin-bottom");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
    //alert("!");
  }, duration);
};

let slideDown = (target, duration = 500) => {
  target.style.removeProperty("display");
  let display = window.getComputedStyle(target).display;

  if (display === "none") display = "block";

  target.style.display = display;
  let height = target.offsetHeight;
  target.style.overflow = "hidden";
  target.style.height = 0;
  target.style.paddingTop = 0;
  target.style.paddingBottom = 0;
  target.style.marginTop = 0;
  target.style.marginBottom = 0;
  target.offsetHeight;
  target.style.boxSizing = "border-box";
  target.style.transitionProperty = "height, margin, padding";
  target.style.transitionDuration = duration + "ms";
  target.style.height = height + "px";
  target.style.removeProperty("padding-top");
  target.style.removeProperty("padding-bottom");
  target.style.removeProperty("margin-top");
  target.style.removeProperty("margin-bottom");
  window.setTimeout(() => {
    target.style.removeProperty("height");
    target.style.removeProperty("overflow");
    target.style.removeProperty("transition-duration");
    target.style.removeProperty("transition-property");
  }, duration);
};

let slideToggle = (target, duration = 500) => {
  if (window.getComputedStyle(target).display === "none") {
    return slideDown(target, duration);
  } else {
    return slideUp(target, duration);
  }
};
//# sourceURL=pen.js
</script>
