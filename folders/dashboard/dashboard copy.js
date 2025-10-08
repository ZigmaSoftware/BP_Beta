$(document).ready(function () {
	var url = "";
	// dashboard_month_follow_up_filter();
	// dashboard_month_leads_filter();
});

function dashboard_details() {

	var dashboard_month = $("#dashboard_month").val();
	var ajax_url 		= 'folders/dashboard/crud.php';

	if (dashboard_month) {
		var data = {
			"month"  : dashboard_month,
			"action" : "dashboard"
		}

		$.ajax({
			type : "POST",
			url  : ajax_url,
			data : data,
			success : function (data) {
				var obj 	= JSON.parse(data);

				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    console.log(error);
				} else {

				}
			}
		});
	} else {

	}
}

// Get Dashboard Filter Options in Followup call
function dashboard_month_follow_up_filter() {


	var dashboard_month = $("#dashboard_month").val();

	var ajax_url = 'folders/dashboard/crud.php';


	if (dashboard_month) {
		var data = {
			"dashboard_month": dashboard_month,
			"action": "dashboard_filter"
		}

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {


				var obj = JSON.parse(data);
				var count = obj.data[0];
				//Update All Counts In List Panel
				$("#new_calls_count").html(count.new_calls);
				$("#new_calls_count1").val(count.new_calls);
				$("#follow_up_calls_count").html(count.follow_ups);
				$("#updated_calls_count").html(count.updated);
				$("#closed_calls_count").html(count.closed);


			}
		});


	}
}


// Get Dashboard Filter Options in leads
function dashboard_month_leads_filter() {


	var dashboard_month = $("#dashboard_month").val();

	var ajax_url = 'folders/dashboard/crud.php';


	if (dashboard_month) {
		var data = {
			"dashboard_month": dashboard_month,
			"action": "dashboard_filter_leads"
		}

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {


				var obj = JSON.parse(data);
				var count = obj.data[0];
				//Update All Counts In List Panel
				$("#new_calls_leads_count").html(count.new_calls);
				$("#follow_up_calls_leads_count").html(count.follow_ups);
				$("#updated_calls_leads_count").html(count.updated);
				$("#closed_calls_leads_count").html(count.closed);

			}
		});
	}
}

! function (e) {


	var new_calls_count = $("#new_calls_count1").val();
	var follow_up_calls_count = $("#follow_up_calls_count").val();
	var updated_calls_count = $("#updated_calls_count").val();
	var closed_calls_count = $("#closed_calls_count").val();
	var new_calls_leads_count = $("#new_calls_leads_count").val();
	var follow_up_calls_leads_count = $("#follow_up_calls_leads_count").val();
	var updated_calls_leads_count = $("#updated_calls_leads_count").val();
	var closed_calls_leads_count = $("#closed_calls_leads_count").val();


	function a() {}
	a.prototype.createDonutChart = function (a, t, e) {
		Morris.Donut({
			element: a,
			data: t,
			barSize: .2,
			resize: !0,
			colors: e,
			backgroundColor: "transparent"
		})
	}, a.prototype.init = function () {

		var t;
		a = ["#C0392B", "#F1C40F", "#27AE60", "#2980B9"];
		(t = e("#lifetime-sales").data("colors")) && (a = t.split(",")), this.createDonutChart("lifetime-sales", [{
			label: " New Calls ",
			value: 0
		}, {
			label: " Followup Calls",
			value: 0
		}, {
			label: " Update Calls ",
			value: 0
		}, {
			label: " Close Calls ",
			value: 0
		}], a)

		a = ["#C0392B", "#F1C40F", "#27AE60", "#2980B9"];
		(t = e("#lifetime-sales_1").data("colors")) && (a = t.split(",")), this.createDonutChart("lifetime-sales11", [{
			label: " New Leads ",
			value: 0
		}, {
			label: " Followup Leads",
			value: 0
		}, {
			label: " Update Leads ",
			value: 0
		}, {
			label: " Close Leads ",
			value: 0
		}], a)

		
	}, e.Dashboard4 = new a, e.Dashboard4.Constructor = a

}
(window.jQuery),
function () {
	"use strict";
	window.jQuery.Dashboard4.init()
}();