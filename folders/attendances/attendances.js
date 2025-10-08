$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
	init_datatable(table_id,form_name,action);
	var staff_id = $('#staff_id').val();
	get_staff_name(staff_id);

	// Check Geolocation Status
	getLocation();
	get_attendance_type(staff_id);
	get_day_type(staff_id);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");
var ajax_url 		= sessionStorage.getItem("folder_crud_link");

var form_name 		= 'daily_attendances';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'daily_attendances_datatable';
var action 			= "datatable";

function attendances_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

	var latitude = $("#latitude").val();

	// if (!latitude) {
	// 	sweetalert("no_location");
	// 	return false;
	// } else {
	// 	getLocation();
	// 	let premises_validate = premises_check();
	// 	if (!premises_validate) {
	// 		return false;
	// 	}
	// }

	// return false;

    var staff_id        = $('#staff_id').val(); 
	var entry_time      = $('#entry_time').val(); 
    var attendance_type = $('#attendance_type').val(); 
    var latitude        = $('#latitude').val(); 
    var longitude       = $('#longitude').val(); 
    var today_status    = $('#today_status').val(); 

    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&staff_id="+staff_id+"&entry_time="+entry_time+"&longitude="+longitude+"&latitude="+latitude+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".createupdate_btn").attr("disabled","disabled");
				$(".createupdate_btn").text("Loading...");
			},
			success		: function(data) {
				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
					url 	= '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
				} else {
					if (msg=="already") {
						// Button Change Attribute
						url 		= '';

						$(".createupdate_btn").removeAttr("disabled","disabled");
						if (unique_id) {
							$(".createupdate_btn").text("Update");
						} else {
							$(".createupdate_btn").text("Save");
						}
					}
				}

				url = "index.php";
				sweetalert(msg,url);
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("form_alert");
    }
}

function init_datatable(table_id='',form_name='',action='') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

function attendances_delete(unique_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	confirm_delete('delete')
	.then((result) => {
		if (result.isConfirmed) {

			var data = {
				"unique_id" 	: unique_id,
				"action"		: "delete"
			}

			$.ajax({
				type 	: "POST",
				url 	: ajax_url,
				data 	: data,
				success : function(data) {

					var obj     = JSON.parse(data);
					var msg     = obj.msg;
					var status  = obj.status;
					var error   = obj.error;

					if (!status) {
						url 	= '';
						
					} else {
						init_datatable(table_id,form_name,action);
					}
					sweetalert(msg,url);
				}
			});
		} else {
			// alert("cancel");
		}
	});
}

function get_staff_name(staff_id = ''){ 
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	
	if (staff_id) {

		var data = {
			"staff_id" : staff_id,
			"action"   : "staff_name"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {

				if (data) {
					$("#employee_name").val(data);
					get_attendance_type(staff_id);
					get_day_type(staff_id);
					
				}
			}
		});
	}
}

function get_day_type (staff_id = "") {

	var staff_id 		= $("#staff_id").val();
	
	if (staff_id) {

		var data = {
			"staff_id" 			: staff_id,
			"action"   			: "day_type"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {

				var obj           	 = JSON.parse(data);
				var message       	 = obj.message;
				var status        	 = obj.status;
				
				if (status) {
					$("#day_type_text").html(message);
					$("#day_type").val(status);
					
				}
			}
		});
	}
}

function get_attendance_type(staff_id = '') { 
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	
	if (staff_id) {

		var data = {
			"staff_id" : staff_id,
			"action"   : "attendance_type"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {
				$("#attendance_type").html(data);
				// var obj          = JSON.parse(data);
				// var msg          = obj.msg;
				// var status       = obj.status;
				// var error        = obj.error;
				// var brand_option = obj.option;
				get_day_status();
				get_day_type (staff_id);
				if (data) {
					

					// $("#attendance_type option").remove();
					// $("#attendance_type").append(brand_option);
				}
			}
		});
	}
}

function get_day_status (attendance_type = "") {

	let staff_id 		= $("#staff_id").val();
	var attendance_type = $("#attendance_type").val();

	if (attendance_type && staff_id) {

		var data = {
			"staff_id" 			: staff_id,
			"attendance_type" 	: attendance_type,
			"action"   			: "day_status"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {

				var obj           	 = JSON.parse(data);
				var message       	 = obj.message;
				var status        	 = obj.status;
				var premises_type 	 = obj.premises_type;
				var branch_lat       = obj.branch_lat;
				var branch_lng       = obj.branch_lng;
				var branch_rds       = obj.branch_rds;

				$("#branch_rds").val(branch_rds);
				$("#branch_lat").val(branch_lat);
				$("#branch_lng").val(branch_lng);

				$("#premises_type").val(premises_type);

				if (status) {
					$("#day_status_text").html(message);
					$("#day_status").val(status);
				}

				// var error        = obj.error;
				// var brand_option = obj.option;

				// if (data) {
					

					// $("#attendance_type option").remove();
					// $("#attendance_type").append(brand_option);
				// }
			}
		});
	}
}

function premises_check() {

	let premises_type = $("#premises_type").val();

	if (premises_type != "0") {

		let branch_rds = $("#branch_rds").val();
		let branch_lat = $("#branch_lat").val();
		let branch_lng = $("#branch_lng").val();

		let latitude   = $("#latitude").val();
		let longitude  = $("#longitude").val();

		let cur_location = new google.maps.LatLng(latitude, longitude);
		let brn_location = new google.maps.LatLng(branch_lat, branch_lng);

		//let distance = google.maps.geometry.spherical.computeDistanceBetween(cur_location,brn_location,branch_rds);
		let distance = google.maps.geometry.spherical.computeDistanceBetween(brn_location,cur_location);

		// alert(distance);
		// alert(branch_rds);

		if (distance > branch_rds) {
			alert("You are Out of the Attendance Location!");
			return false;
		}
	}
	return true;
}

//date and time display function

// function timeDateDisplay(element_id = "entry_time",need_secounds = false) {
// 	var x = new Date()
// 	var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
// 	hours = x.getHours() % 12;
// 	hours = hours ? hours : 12;
// 	hours = hours.toString().length == 1 ? 0 + hours.toString() : hours;
  
// 	var minutes = x.getMinutes().toString()
// 	minutes = minutes.length == 1 ? 0 + minutes : minutes;
  
// 	var seconds = x.getSeconds().toString()
// 	seconds = seconds.length == 1 ? 0 + seconds : seconds;
  
// 	var month = (x.getMonth() + 1).toString();
// 	month = month.length == 1 ? 0 + month : month;
  
// 	var dt = x.getDate().toString();
// 	dt = dt.length == 1 ? 0 + dt : dt;
  
// 	var x1 = dt + "-" + month + "-" + x.getFullYear();
// 	// x1 = x1 + " - " + hours + ":" + minutes + ":" + seconds + " " + ampm;
// 	x1 = x1 + " - " + hours + ":" + minutes + " "+ ":" + seconds + " " + ampm;
// 	document.getElementById(element_id).value = x1;
  
// 	display_c7();
//   }
  
//   function display_c7() {
// 	var refresh = 1000; // Refresh rate in milli seconds
// 	mytime = setTimeout('timeDateDisplay()', refresh)
//   }
//   display_c7();