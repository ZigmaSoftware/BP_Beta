// var fixed_target_chart  = "";
$(document).ready(function () {
	getCounts();
	festival_data();
	var staff_id = $('#staff_id').val();
	get_staff_worked_days(staff_id);
	get_staff_working_days(staff_id);
	get_staff_leave_days(staff_id);
	get_staff_not_punch_days(staff_id);
	get_staff_absent_days(staff_id);
	notification_collapse_today();
	notification_collapse_upcomming();
	notification_collapse_user();
	notification_collapse_doj();
	// AM Chart Start
	am4core.useTheme(am4themes_animated);

var chartMin = 0;
var chartMax = 100;

var data = {
  score: 15,
  gradingData: [
    {
      title: " Bad",
      advice: "Improve the revenue and workforce performance.",
      color: "#E53935",
      lowScore: 0,
      highScore: 25
    },
    {
      title: "Warning",
      advice: "Maybe you should improve workforce performance.",
      color: "#FB8C00",
      lowScore: 25,
      highScore: 50
    },
    {
      title: "OK",
      advice: "Good result.",
      color: "#ff0",
      lowScore: 50,
      highScore: 75
    },
	{
		title: "Good",
		advice: "Good industry result.",
		color: "#43A047",
		lowScore: 75,
		highScore: 100
	  }
  ]
};

/**
Grading Lookup
 */
function lookUpGrade(lookupScore, grades) {
  // Only change code below this line
  for (var i = 0; i < grades.length; i++) {
    if (
      grades[i].lowScore < lookupScore &&
      grades[i].highScore >= lookupScore
    ) {
      return grades[i];
    }
  }
  return null;
}

// create chart
var chart = am4core.create("fixed_target_am_chart", am4charts.GaugeChart);
chart.hiddenState.properties.opacity = 0;
chart.fontSize = 11;
chart.innerRadius = am4core.percent(80);
chart.resizable = true;
//chart.numberFormatter.numberFormat = "#a' % '";

/**
 * Normal axis
 */

var axis = chart.xAxes.push(new am4charts.ValueAxis());
axis.min = chartMin;
axis.max = chartMax;
axis.strictMinMax = true;
axis.renderer.radius = am4core.percent(80);
axis.renderer.inside = true;
axis.renderer.line.strokeOpacity = 0.1;
axis.renderer.ticks.template.disabled = false;
axis.renderer.ticks.template.strokeOpacity = 1;
axis.renderer.ticks.template.strokeWidth = 0.5;
axis.renderer.ticks.template.length = 5;
axis.renderer.grid.template.disabled = true;
axis.renderer.labels.template.radius = am4core.percent(20);
axis.renderer.labels.template.fontSize = "0.9em";

/**
 * Axis for ranges
 */

var axis2 = chart.xAxes.push(new am4charts.ValueAxis());
axis2.min = chartMin;
axis2.max = chartMax;
axis2.strictMinMax = true;
axis2.renderer.labels.template.disabled = true;
axis2.renderer.ticks.template.disabled = true;
axis2.renderer.grid.template.disabled = false;
axis2.renderer.grid.template.opacity = 0.5;
axis2.renderer.labels.template.bent = true;
axis2.renderer.labels.template.fill = am4core.color("#000");
axis2.renderer.labels.template.fontWeight = "bold";
axis2.renderer.labels.template.fillOpacity = 0.3;
//axis2.numberFormatter.numberFormat = "'₹'#,###"
axis2.numberFormatter.numberFormat = "#a' % '";


/**
Ranges
*/

for (let grading of data.gradingData) {
  var range = axis2.axisRanges.create();
  range.axisFill.fill = am4core.color(grading.color);
  range.axisFill.fillOpacity = 0.8;
  range.axisFill.zIndex = -1;
  range.value = grading.lowScore > chartMin ? grading.lowScore : chartMin;
  range.endValue = grading.highScore < chartMax ? grading.highScore : chartMax;
  range.grid.strokeOpacity = 0;
  range.stroke = am4core.color(grading.color).lighten(-0.1);
  range.label.inside = true;
  range.label.text = grading.title.toUpperCase();
  range.label.inside = true;
  range.label.location = 0.5;
  range.label.inside = true;
  range.label.radius = am4core.percent(10);
  range.label.paddingBottom = -5; // ~half font size
  range.label.fontSize = "0.9em";
}

var matchingGrade = lookUpGrade(data.score, data.gradingData);

/**
 * Value
 */

var label = chart.radarContainer.createChild(am4core.Label);
label.isMeasured = false;
label.fontSize = "6em";
label.x = am4core.percent(50);
label.paddingBottom = 5;
label.horizontalCenter = "middle";
label.verticalCenter = "bottom";
//label.dataItem = data;
label.text = data.score.toFixed(1);
//label.text = "{score}";
label.fill = am4core.color(matchingGrade.color);
//label.numberFormatter.numberFormat = "#a' % '";

/**
 * Advice
 */

//var label2 = chart.radarContainer.createChild(am4core.Label);
var label2 = chart.createChild(am4core.Label);
label2.isMeasured = false;
label2.fontSize = "1em";
//label2.paddingTop = 150;
label2.horizontalCenter = "middle";
label2.verticalCenter = "bottom";
//label2.text = matchingGrade.title.toUpperCase();
label2.text = "hello";
label2.fill = am4core.color(matchingGrade.color);
label2.dx = 280;
label2.dy = 320;

/**
 * Hand
 */

hand = chart.hands.push(new am4charts.ClockHand());
hand.axis = axis2;
hand.radius = am4core.percent(85);
hand.innerRadius = am4core.percent(50);
hand.startWidth = 10;
//hand.pixelHeight = 10;
hand.pin.disabled = true;
hand.value = data.score;
hand.fill = am4core.color("#444");
hand.stroke = am4core.color("#000");

hand.events.on("positionchanged", function(){
  var t = axis2.positionToValue(hand.currentPosition).toFixed(0);
  var formattedValue = chart.numberFormatter.format(t, "#a' % '");
  label.text = formattedValue;
  label.text = axis2.positionToValue(hand.currentPosition).toFixed(0);
  var value2 = axis.positionToValue(hand.currentPosition);
  var matchingGrade = lookUpGrade(axis.positionToValue(hand.currentPosition), data.gradingData);
  label2.text = matchingGrade.advice.toUpperCase();
  label2.fill = am4core.color(matchingGrade.color);
  label2.stroke = am4core.color(matchingGrade.color);  
  label.fill = am4core.color(matchingGrade.color);
})

// setInterval(function() {
    // var value = chartMin + Math.random() * (chartMax - chartMin);
    // hand.showValue(value, 1000, am4core.ease.cubicOut);
// }, 3000);
	// AM Chart End

	
	const user_calls_chart_id = document.getElementById("user-calls-chart");
	const calls_chart_id 	  = document.getElementById("calls-chart");
	const staff_wise_chart_id = document.getElementById("staff-wise-bar-chart");
	const sales_chart_id 	  = document.getElementById("sales-bar-chart");
	const fixed_target_chart_id 	  = document.getElementById("fixed-target-chart");

	// console.log(user_calls_chart_id);

	if (user_calls_chart_id) {
		var user_chart 	 	  = new Chart(user_calls_chart_id,user_calls_chart_config);
	}

	if (calls_chart_id) {
		var calls_chart 	 	  = new Chart(calls_chart_id,calls_chart_config);
	}

	if (staff_wise_chart_id) {
		var bar_chart 	 	  = new Chart(staff_wise_chart_id,bar_chart_config);
	}

	if (sales_chart_id) {
		var sales_bar_chart 	  = new Chart(sales_chart_id,sales_bar_chart_config);
	}

	if (fixed_target_chart_id) {
		fixed_target_chart  = new Chart(fixed_target_chart_id,fixed_target_chart_config);

		// console.log("In Init");
		// console.log(fixed_target_chart);

		// let test = fixed_target_chart.data.datasets[0]["value"] = 20;
		// fixed_target_chart.update();
		// console.log(test);
		// setTimeout(function() { 
		// 	// removeData(fixed_target_chart);
		// 	// addData
		// 	let test = fixed_target_chart.data.datasets;
		// }, 3000);
	}

// 	setInterval(function () {
// 		myFunction();
// 		business_forecast();
// 	}, 10000);

// 	fixed_target();
// 	business_forecast();

});


function addData(chart, label, data) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach((dataset) => {
        dataset.data.push(data);
    });
    chart.update();
}

function removeData(chart) {
    chart.data.labels.pop();
    chart.data.datasets.forEach((dataset) => {
        dataset.data.pop();
    });
    chart.update();
}

var ajax_url = sessionStorage.getItem("folder_crud_link");

// Donut Chart Values and Config
const user_calls_chart_data = {
	labels: [
	  'Worked',
	//   'Blue',
	  'Leave'
	],
	datasets: [{
	  label: 'My First Dataset',
	  data: [24, 2],
	  backgroundColor: [
		'#69d14d',
		'#ff554f'
		// 'rgb(54, 162, 235)',
	  ],
	  hoverOffset: 4
	}]
};

const user_calls_chart_config = {
	type: 'doughnut',
	data: user_calls_chart_data,
};

// Donut Chart Values and Config
const calls_chart_data = {
	labels: [
	  'Funnel',
	  'Leads',
	  'Bid\'s',
	  'Elcot',
	  'Sales Order'
	],
	datasets: [{
	  label: 'My Secound Dataset',
	  data: [30, 50, 100, 80, 160],
	  backgroundColor: [
		'#4e226f',
		'#1e8de6',
		'#bf0000',
		'#9f479e',
		'#ffc000'
	  ],
	  hoverOffset: 4
	}]
};

const calls_chart_config = {
	type: 'doughnut',
	data: calls_chart_data,
};

// Bar Chart Config and Data

const bar_labels = [
	"User 1",
	"User 2",
	"User 3",
	"User 4",
	"User 5",
	"User 6",
	"User 7"
];

const bar_data = {
  labels: bar_labels,
  datasets: [
    // {
    //   label: 'Dataset 1',
    //   data: Utils.numbers(NUMBER_CFG),
    //   borderColor: Utils.CHART_COLORS.red,
    //   backgroundColor: Utils.transparentize(Utils.CHART_COLORS.red, 0.5),
    // },
    // {
    //   label: 'Dataset 2',
    //   data: Utils.numbers(NUMBER_CFG),
    //   borderColor: Utils.CHART_COLORS.blue,
    //   backgroundColor: Utils.transparentize(Utils.CHART_COLORS.blue, 0.5),
    // },
	{
		label: 'Leads',
		data: [30,60,10,30,50,80,35],
		borderColor: "#1e8de4",
		backgroundColor: "#1e8de4",
	},
	{
		label: 'Funnel',
		data: [50,18,10,30,80,90,8],
		borderColor: "#a317bf",
		backgroundColor: "#a317bf",
	},
	{
		label: 'Elcot',
		data: [52,30,80,12,90,60,14],
		borderColor: "#dd5595",
		backgroundColor: "#dd5595",
	},
	{
		label: 'Bids',
		data: [65,75,32,74,12,82,24],
		borderColor: "#f71616",
		backgroundColor: "#f71616",
	}
  ]
};

const bar_chart_config = {
	type: 'bar',
	data: bar_data,
	options: {
	  responsive: true,
	  plugins: {
		legend: {
		  position: 'bottom',
		},
		title: {
		  display: true,
		  text: 'Staff Wise Data'
		}
	  }
	}
};

// Harizontal Bar Chart config and Data

const sales_bar_chart_data =  {
	labels: bar_labels,
	datasets: [
	  {
		axis: 'y',
		label: 'Target',
		data: [1000421.20,3652442.01,8625412.00,4523682,20,1289632.85,6325410.25,8756321.50],
		borderColor: "#a317bf",
		backgroundColor: "#a317bf",
	  },
	  {
		axis: 'y',
		label: 'Finished',
		data: [9625846.36,5324891.85,7561254.54,6548951.24,4512365.41,4512657.45,8456321.42],
		borderColor: "#f71616",
		backgroundColor: "#f71616",
	  }
	]
};

// const labels = Utils.months({count: 7});
const sales_bar_chart_data11 = {
  labels: bar_labels,
  datasets: [{
    axis: 'y',
    label: 'My First Dataset',
    data: [65, 59, 80, 81, 56, 55, 40],
    fill: false,
    backgroundColor: [
      'rgba(255, 99, 132, 0.2)',
      'rgba(255, 159, 64, 0.2)',
      'rgba(255, 205, 86, 0.2)',
      'rgba(75, 192, 192, 0.2)',
      'rgba(54, 162, 235, 0.2)',
      'rgba(153, 102, 255, 0.2)',
      'rgba(201, 203, 207, 0.2)'
    ],
    borderColor: [
      'rgb(255, 99, 132)',
      'rgb(255, 159, 64)',
      'rgb(255, 205, 86)',
      'rgb(75, 192, 192)',
      'rgb(54, 162, 235)',
      'rgb(153, 102, 255)',
      'rgb(201, 203, 207)'
    ],
    borderWidth: 1
  }]
};

const sales_bar_chart_config = {
	type: 'bar',
	data: sales_bar_chart_data,
	options: {
	//   indexAxis: 'y',
	  // Elements options apply to all of the options unless overridden in a dataset
	  // In this case, we are setting the border of each horizontal bar to be 2px wide
	  elements: {
		bar: {
		  borderWidth: 2,
		}
	  },
	  responsive: true,
	  plugins: {
		legend: {
		  position: 'right',
		},
		title: {
		  display: true,
		  text: 'Chart.js Horizontal Bar Chart'
		}
	  }
	},
};

// const sales_bar_chart_config = {
// 	type: 'bar',
// 	sales_bar_chart_data,
// 	options: {
// 	  indexAxis: 'y',
// 	}
// };

// const fixed_target_chart_data 	= [
// 	25,
// 	25,
// 	25,
// 	25
// ];

// const fixed_target_chart_value 	= 50;

let data = [
	25,
	50,
	75,
	100
];
let value = 0;
// const fixed_target_chart_config = {
// 	type : 'gauge',
// 	data : {
// 		datasets:[{
// 			data: fixed_target_chart_data,
// 			value: fixed_target_chart_value,
// 			backgroundColor: [
// 				"red",
// 				"orange",
// 				"yellow",
// 				"green"
// 			],
// 			borderWidth: 2
// 		}],
// 	},
// 	options: {
// 		responsive:true,
// 		title: {
// 			display: true,
// 			text: "Gauge Chart"
// 		}
// 	},
// 	needle: {
// 		radiusPercentage: 2,
// 		widthPercentage: 3.2,
// 		lengthPercentage: 80,
// 		color: 'rgba(0,0,0,0.8)'
// 	},
// 	valueLable: {
// 		formatter: Math.round
// 	}
// };

// con

var fixed_target_chart_config = {
	type: 'gauge',
	data: {
	  labels: ['25 %', '50 %', '75 %', '100 %'],
	  datasets: [{
		data: data,
		value: value,
		backgroundColor: [
			"red",
			"orange",
			"yellow",
			"green"
		],
		borderWidth: 1
	  }]
	},
	options: {
	  cutoutPercentage : 70, 
	  responsive: true,
	  title: {
		display: true,
		// text: 'Gauge chart with datalabels plugin displaying labels'
	  },
	  layout: {
		// padding: {
		//   bottom: 30
		// }
		// width: "100px",
		padding: {
			top: 0,
			bottom: 13
		},
		margin: {
			top: 0,
			bottom: 0
		},
	  },
	  needle: {
		// Needle circle radius as the percentage of the chart area width
		radiusPercentage: 2,
		// Needle width as the percentage of the chart area width
		widthPercentage: 3.2,
		// Needle length as the percentage of the interval between inner radius (0%) and outer radius (100%) of the arc
		lengthPercentage: 80,
		// The color of the needle
		color: 'rgba(0, 0, 0, 1)'
	  },
	  valueLabel: {
		display: true
	  },
	  plugins: {
		datalabels: {
		  display: true,
		  formatter:  function (value, context) {
			return context.chart.data.labels[context.dataIndex];
		  },
		  
		//   color: function (context) {
		//    return context.dataset.backgroundColor;
		//   },
		  color: 'rgba(0, 0, 0, 1.0)',
		//   color: 'rgba(255, 255, 255, 1.0)',
		  backgroundColor: null,
		  font: {
			size: 20,
			weight: 'bold'
		  }
		}
	  }
	}
  };


  
function dashboard_count() {
	
	let data = {
		action : "dashboard_count"
	};

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	$.ajax({
		type: "POST",
		url: ajax_url,
		data: data,
		success: function (data) {
			if (data) {
				document.getElementById('phone_no').value = data;
			}
		}
	});
}

function quarter_month_change(type = "", div = "") {
	if (type) {
		$("."+div+"-div").addClass("d-none");

		switch (type) {
			case "2":
				$("."+div+"-quarterly").removeClass("d-none");
				break;

			case "3":
				$("."+div+"-monthly").removeClass("d-none");
				
				break;
		
			default:
				break;
		}
	}
}


function myFunction() {
	$('#fixed-staff_target_div').load("folders/dashboard/fixed_target.php");
	fixed_target();
	//window.location = location.href;
 // setInterval(function(){ , 3000);
}

function fixed_target() {
	var report_type      = $('#report_type').val(); 
	var quarterly_report = $('#quarterly_report').val(); 
	var monthly_report   = $('#monthly_report').val(); 
	var is_team_head     = $('#is_team_head').val(); 
	var user_type        = $('#user_type').val(); 
	
	$.ajax({
		type: "POST",
		url: "folders/dashboard/staff_target.php",
		data: "report_type="+report_type+"&quarterly_report="+quarterly_report+"&monthly_report="+monthly_report,
		success: function(data) {

			if ((is_team_head != 1)&&(user_type != '5f97fc3257f2525529')) {

				$(".business-forcast-td").removeAttr("onclick");
				

				let json_obj 	  = JSON.parse(data);
				let target   	  = json_obj.target;
				let archieved 	  = json_obj.archieved;
				let percentage    = json_obj.percentage;
				let staff_id      = json_obj.staff_id;
				let staff_name    = json_obj.staff_name;

				$("#staff_target").html(target);
				
				$("#staff_target").attr("onclick","team_head_modal('"+staff_id+"','"+target+"','"+staff_name+"','0');");
				$("#staff_archieved").html(archieved);
				$("#staff_archieved_percentage").html(percentage);

				$("#team_head_achived").attr("onclick","team_head_modal('"+staff_id+"','"+target+"','"+staff_name+"')");

				// Chart Update
				// fixed_target_chart.data.datasets[0]["value"] = percentage;
				// fixed_target_chart.data.datasets[0]["value"] = Math.floor(Math.random() * 100);
				// fixed_target_chart.update()
				// var value = 0 + Math.random() * (100 - 0);
    			hand.showValue(percentage, 1000, am4core.ease.cubicOut);
			} else {
				// alert("working1");
				jQuery("#staff_target_div").html(data);
			}
		}
	});
}

// function business_forecast() {

// 	let report_type      = $('#business_report_type').val(); 
// 	let quarterly_report = $('#business_quarterly_report').val(); 
// 	let monthly_report   = $('#business_monthly_report').val();

// 	let data  = {
// 		type 	: report_type,
// 		quarter : quarterly_report,
// 		month 	: monthly_report,
// 		action 	: "business_forecast"
// 	};

// 	$.ajax({
// 		type 	: "POST",
// 		url     : ajax_url,
// 		data    : data,
// 		success : function (response) {

// 			// let json_obj = JSON.parse(response);
// 			// let status 	 = json_obj.status;

// 			if (response) {

// 				$("#dashboard-business-forecast").find('tbody').html(response);

// 				// let data 				= json_obj.data;

// 				// let lead_committed 		    = data[0].committed;
// 				// let funnel_upside_committed  = data[1].committed;
// 				// let funnel_commit_committed  = data[2].committed;
// 				// let purchase_committed 		= data[3].committed;
// 				// let billing_committed 		= data[4].committed;
// 				// let payment_committed 		= data[5].committed;

// 				// $("#lead_committed").html(lead_committed);
// 				// $("#funnel_upside_committed").html(funnel_upside_committed);
// 				// $("#funnel_commit_committed").html(funnel_commit_committed);
// 				// $("#purchase_order_committed").html(purchase_committed);
// 				// $("#billing_committed").html(billing_committed);
// 				// $("#payment_committed").html(payment_committed);

// 				// let lead_achieved 			    = data[0].achieved;
// 				// let funnel_upside_achieved      = data[1].achieved;
// 				// let funnel_commit_achieved      = data[2].achieved;
// 				// let purchase_achieved 		    = data[3].achieved;
// 				// let billing_achieved 		    = data[4].achieved;
// 				// let payment_achieved 		    = data[5].achieved;

// 				// $("#lead_achieved").html(lead_achieved);
// 				// $("#funnel_upside_achieved").html(funnel_upside_achieved);
// 				// $("#funnel_commit_achieved").html(funnel_commit_achieved);
// 				// $("#purchase_order_achieved").html(purchase_achieved);
// 				// $("#billing_achieved").html(billing_achieved);
// 				// $("#payment_achieved").html(payment_achieved);

// 				// let lead_percentage 			= data[0].percentage;
// 				// let funnel_upside_percentage  	= data[1].percentage;
// 				// let funnel_commit_percentage  	= data[2].percentage;
// 				// let purchase_percentage 		= data[3].percentage;
// 				// let billing_percentage 			= data[4].percentage;
// 				// let payment_percentage 			= data[5].percentage;

				
// 				// $("#lead_progress").attr("aria-valuenow",lead_percentage);
// 				// $("#funnel_upside_progress").attr("aria-valuenow",funnel_upside_percentage);
// 				// $("#funnel_commit_progress").attr("aria-valuenow",funnel_commit_percentage);
// 				// $("#purchase_order_progress").attr("aria-valuenow",purchase_percentage);
// 				// $("#billing_progress").attr("aria-valuenow",billing_percentage);
// 				// $("#payment_progress").attr("aria-valuenow",payment_percentage);

// 				// lead_percentage_per 			= lead_percentage+"% ";
// 				// funnel_upside_percentage_per  	= funnel_upside_percentage+"% ";
// 				// funnel_commit_percentage_per  	= funnel_commit_percentage+"% ";
// 				// purchase_percentage_per 		= purchase_percentage+"% ";
// 				// billing_percentage_per 			= billing_percentage+"% ";
// 				// payment_percentage_per 			= payment_percentage+"% ";

// 				// $("#lead_percentage").html(lead_percentage_per);
// 				// $("#funnel_upside_percentage").html(funnel_upside_percentage_per);
// 				// $("#funnel_commit_percentage").html(funnel_commit_percentage_per);
// 				// $("#purchase_order_percentage").html(purchase_percentage_per);
// 				// $("#billing_percentage").html(billing_percentage_per);
// 				// $("#payment_percentage").html(payment_percentage_per);

// 				// $("#lead_progress").attr("style","width:"+lead_percentage_per);
// 				// $("#funnel_upside_progress").attr("style","width:"+funnel_upside_percentage_per);
// 				// $("#funnel_commit_progress").attr("style","width:"+funnel_commit_percentage_per);
// 				// $("#purchase_order_progress").attr("style","width:"+purchase_percentage_per);
// 				// $("#billing_progress").attr("style","width:"+billing_percentage_per);
// 				// $("#payment_progress").attr("style","width:"+payment_percentage_per);

// 				// // Business forecast Progress color
// 				// $(".business-forecast-progress-bar").removeClass("bg-danger bg-warning bg-info bg-success");
				
// 				// let lead_class 				= data[0].class;
// 				// let funnel_upside_class  	= data[1].class;
// 				// let funnel_commit_class  	= data[2].class;
// 				// let purchase_class 			= data[3].class;
// 				// let billing_class 			= data[4].class;
// 				// let payment_class 			= data[5].class;

// 				// $("#lead_progress").addClass(lead_class);
// 				// $("#funnel_upside_progress").addClass(funnel_upside_class);
// 				// $("#funnel_commit_progress").addClass(funnel_commit_class);
// 				// $("#purchase_order_progress").addClass(purchase_class);
// 				// $("#billing_progress").addClass(billing_class);
// 				// $("#payment_progress").addClass(payment_class);
// 			} else {
// 				alert("Business forecast Data fetch Error");
// 			}
// 		}
// 	});
// }

function team_head_modal (team_head_staff_id = "", target_amount = '',team_head_staff_name = '',team_head = '') {
    // $("#team_head_staff_name").html(team_head_staff_name);
    // $("#team_head_staff_id").val(team_head_staff_id);
    // $("#target_amount").html(target_amount);
	// alert("model working");

    $.ajax({
		type 	: "POST",
		url  	: "folders/dashboard/modal_body.php",
		data 	: "team_head_staff_id="+team_head_staff_id+"&target_amount="+target_amount+"&team_head_staff_name="+team_head_staff_name+"&team_head="+team_head,
		success : function(response) {
			$("#fixed-target-modal").find(".modal-body").html(response);
			$("#fixed-target-modal").modal("show");
		}
	});
}

function addData(chart, label, data) {
    chart.data.labels.push(label);
    chart.data.datasets.forEach((dataset) => {
        dataset.data.push(data);
    });
    chart.update();
}

function removeData(chart) {
    chart.data.labels.pop();
    chart.data.datasets.forEach((dataset) => {
        dataset.data.pop();
    });
    chart.update();
}

// function business_forecast_modal(stage = 0) {

// 	// $("#business_forecat_modal_type").html(report_name);
// 	let report_name = "";
// 	let committed 	= "";
// 	let achieved 	= "";

// 	switch (stage) {
// 		case 1:
// 			report_name = "Lead";
// 			committed   = "lead";
// 			achieved    = "lead";
// 			break;

// 		case 2:
// 			report_name = "Funnel Upside";
// 			committed   = "funnel_upside";
// 			achieved    = "funnel_upside";
// 			break;

// 		case 3:
// 			report_name = "Funnel Commit";
// 			committed   = "funnel_commit";
// 			achieved    = "funnel_commit";
// 			break;

// 		case 4:
// 			report_name = "Purchase Order";
// 			committed   = "purchase_order";
// 			achieved    = "purchase_order";
// 			break;

// 		case 5:
// 			report_name = "Billing";
// 			committed   = "billing";
// 			achieved    = "billing";
// 			break;

// 		case 6:
// 			report_name = "Payment";
// 			committed   = "payment";
// 			achieved    = "payment";
// 			break;
		
// 		case 7:
// 			report_name = "Lead";
// 			committed   = "lead";
// 			achieved    = "lead";
// 			break;
		
	
// 		default:
// 			break;
// 	}

// 	committed = committed+"_committed";
// 	achieved  = achieved+"_achieved";

// 	committed = $("#"+committed).text();
// 	achieved  = $("#"+achieved).text();

// 	$("#business_forecat_modal_type").text(report_name);
// 	$("#business_forecast_modal_committed").text(committed);
// 	$("#business_forecast_modal_achieved").text(achieved);

// 	// Actual Program Start

// 	let report_type      = $('#business_report_type').val(); 
// 	let quarterly_report = $('#business_quarterly_report').val(); 
// 	let monthly_report   = $('#business_monthly_report').val();

// 	let data  = {
// 		stage 	: stage,
// 		type 	: report_type,
// 		quarter : quarterly_report,
// 		month 	: monthly_report,
// 		action 	: "business_forecast"
// 	};

// 	$.ajax({
// 		type 	: "POST",
// 		url     : "folders/dashboard/business_forecast_modal_tbody.php",
// 		data    : data,
// 		success : function (response) {
// 			$("#business-forecast").find("tbody").html(response);
// 			$("#business-forecast").modal("show");
// 		}
// 	});
// }

// function business_forecast_modal (forecast_id = "",forecast_target = "", forecast_achieved = "",forecast_name = "") {
	
// 	// alert("working");

// 	$("#business_forecat_modal_type").html(forecast_name);
// 	$("#business_forecast_modal_committed").text(forecast_target);
// 	$("#business_forecast_modal_achieved").text(forecast_achieved);

// 	let report_type      = $('#business_report_type').val(); 
// 	let quarterly_report = $('#business_quarterly_report').val(); 
// 	let monthly_report   = $('#business_monthly_report').val();

// 	let data  = {
// 		forecast_id 		: forecast_id,
// 		forecast_target 	: forecast_target,
// 		forecast_achieved 	: forecast_achieved,
// 		type 				: report_type,
// 		quarter 			: quarterly_report,
// 		month 				: monthly_report,
// 		action 				: "business_forecast"
// 	};

// 	// console.log(data);

// 	$.ajax({
// 		type 	: "POST",
// 		url     : "folders/dashboard/business_forecast_modal_tbody.php",
// 		data    : data,
// 		success : function (response) {
// 			$("#business-forecast").find("tbody").html(response);
// 			$("#business-forecast").modal("show");
// 		}
// 	});
// }

// function test_function() {
// 	alert("test function");
// }

function get_staff_working_days (staff_id = "") {
    
	if (staff_id) {
		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");

		var data = {
			"staff_id" 	: staff_id,
			"action"		: "working_days"
		}
		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				var obj           	= JSON.parse(data);
				var working_days    = obj.data;
				
				
				$("#working_days").html(working_days);
				
			}
		});
	}
}

function get_staff_worked_days (staff_id = "") {
    
	if (staff_id) {
		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");

		var data = {
			"staff_id" 	: staff_id,
			"action"		: "worked_days"
		}
		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				var obj           	= JSON.parse(data);
				var worked_days    = obj.data;
				
				$("#worked_days").html(worked_days);
				
			}
		});
	}
}

function get_staff_leave_days (staff_id = "") {
    
	if (staff_id) {
		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");

		var data = {
			"staff_id" 	: staff_id,
			"action"		: "leave_days"
		}
		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				var obj           = JSON.parse(data);
				var leave_days    = obj.data;
				$("#casual_leave").html(leave_days);
				
			}
		});
	}
}

function get_staff_not_punch_days (staff_id = "") {
    
	if (staff_id) {
		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");

		var data = {
			"staff_id" 	: staff_id,
			"action"		: "not_punch_days"
		}
		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				var obj           = JSON.parse(data);
				var not_punch    = obj.data;
				$("#not_punch").html(not_punch);
				
			}
		});
	}
}

function get_staff_absent_days (staff_id = "") {
    
	if (staff_id) {
		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");

		var data = {
			"staff_id" 	: staff_id,
			"action"	: "absent_days"
		}
		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				var obj           = JSON.parse(data);
				var absent_days   = obj.data;
				
				
				$("#absent_days").html(absent_days);
				
			}
		});
	}
}


function notification_collapse_today(){
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  var data = 
  {
    "action"           : "notification_today"
  };
  $.ajax({
    url : ajax_url,
    type:'POST',
    data: data,
    success:function(data)
    {
        var obj                     = JSON.parse(data);
        var data                    = (obj.data); 
      
        $('#collapse_div').html(data);
		}  
  });
}

function notification_collapse_upcomming(){
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  var data = 
  {
    "action"           : "notification_upcomming"
  };
  $.ajax({
    url : ajax_url,
    type:'POST',
    data: data,
    success:function(data)
    {
        var obj                     = JSON.parse(data);
        var data                    = (obj.data);  
      
        $('#collapse_div_comming').html(data);
		}  
  });
}

function notification_collapse_user(){
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var data = 
	{
	  "action"           : "notification_user"
	};
	$.ajax({
	  url : ajax_url,
	  type:'POST',
	  data: data,
	  success:function(data)
	  {
		  var obj                     = JSON.parse(data);
		  var data                    = (obj.data);  
		
		  $('#collapse_user_div').html(data);
		  }  
	});
  }

  function notification_collapse_doj(){
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var data = 
	{
	  "action"           : "notification_doj"
	};
	$.ajax({
	  url : ajax_url,
	  type:'POST',
	  data: data,
	  success:function(data)
	  {
		  var obj                     = JSON.parse(data);
		  var data                    = (obj.data);  
		
		  $('#collapse_doj_div').html(data);
		  }  
	});
  }

  // mythili
function getCounts() {
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var data =
	{
		"action": "get_counts"
	};
	//   alert(data);
	$.ajax({
		url: ajax_url,
		type: 'POST',
		data: data,

		success: function (data) {
			var obj = JSON.parse(data);
			var data = (obj.data);
			// alert(data);
			$('#count_list').html(data);
		}
	});
}

function get_close() {
	$('#myModal_off').hide();
}

function get_popup_data() {
	// alert('hi');
	$('#myModal_off').show();
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var from_date = document.getElementById('from_date').value;

	var to_date = document.getElementById('to_date').value;
	
	var data =
	{
		"action": "datewise_report",
		"from_date":from_date,
		"to_date":to_date,
	};
	// alert(data);
	$.ajax({
		url: ajax_url,
		type: 'POST',
		data: data,
	
			success: function (data) {
				// alert(data);
				var obj = JSON.parse(data);
				var data = (obj.data);
				// alert(data);
				$('#table3_data').html(data);
			}
		});
		
}



// mythili
function festival_data() {
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var data =
	{
		"action": "festival_today"
	};
	$.ajax({
		url: ajax_url,
		type: 'POST',
		data: data,
		success: function (data) {
			
			var obj = JSON.parse(data);
			var data = (obj.data);

			$('#festival').html(data);
		}
	});
}