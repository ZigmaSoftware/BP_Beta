$(document).ready(function () {
	// var table_id 	= "leave_permission_datatable";
	//init_datatable(table_id,form_name,action);
	datatable_init_based_on_prev_state();
	day_type_check();
	let day_type = $("#day_type").val();
	if(day_type == 5) {day_type_check_onduty();}
	var staff_id = $('#staff_id').val();
	get_ho_staff(staff_id);
	var designation = $('#designation').val();

	//get_ceo_approval(designation);
	
	var approve_status = $('#is_approved').val();
	get_cancel_status(approve_status);
	//leavepermissionFilter();
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'User Type';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'leave_permission_datatable';
var action 			= "datatable";

function day_type_check() { 
	// Default 
	$(".day_div").addClass("d-none");
	$(".day_inp").prop("required",false);

	let day_type = $("#day_type").val();

	if (day_type) {
		if ((day_type != 2) && (day_type != 6) &&(day_type != 5)) {
			$(".full_day_div").removeClass("d-none");
			$(".full_day_inp").prop("required",true);
		} else if (day_type == 6) {
			$(".permission_div").removeClass("d-none");
			$(".permission_inp").prop("required",true);
		} else if (day_type == 5) {
			$(".onduty_div").removeClass("d-none");
			$(".onduty_inp").prop("required",true);
		} else {
			$(".half_day_div").removeClass("d-none");
			$(".half_day_inp").prop("required",true);
		}
	}
}

function day_type_check_onduty() {
	// Default 
	$(".day_div").addClass("d-none");
	$(".day_inp").prop("required",false);

	let day_type = $("#on_duty_type").val();

	if (day_type) {
		if (day_type != 2) {
			$(".onduty_div").removeClass("d-none");
			$(".onduty_full_day_div").removeClass("d-none");
			$(".onduty_full_day_inp").prop("required",true);
			$(".onduty_inp").prop("required",true);
		} else {
			$(".onduty_div").removeClass("d-none");
			$(".onduty_inp").prop("required",true);
			$(".on_duty_half_day_div").removeClass("d-none");
			$(".on_duty_half_day_inp").prop("required",true);
		}
	}
}
function datatable_init_based_on_prev_state () {
		// Data Table Filter Function Based ON Previous Search
		var from_date  	    = sessionStorage.getItem("leave_from");
		var to_date         = sessionStorage.getItem("leave_to");
		var filter_action   = sessionStorage.getItem("leave_action");
	
		if (!from_date) {
			from_date = $("#leave_from").val();
		} else {
			$("#leave_from").val(from_date);
		}
	
		if (!to_date) {
			to_date = $("#leave_to").val();
		} else {
			$("#leave_to").val(to_date);
		}
		
	
		if (!filter_action) {
			filter_action = 0;
		}
	
		// Datatable Filter Data
		var filter_data = {
			"from_date" 	: from_date,
			"to_date" 		: to_date,
			"filter_status" : 0,
			"filter_action" : filter_action
		};
	
		// var table_id 	= "follow_up_call_datatable";
		init_datatable(table_id,form_name,action,filter_data);
}
function leave_permission_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

        // console.log(data);
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
					if (msg=="leave_already") {
						// Button Change Attribute
						url 		= '';

						$(".createupdate_btn").removeAttr("disabled","disabled");
						if (unique_id) {
							$(".createupdate_btn").text("Update");
						} else {
							$(".createupdate_btn").text("Save");
						}
					}
					var ho_name = $('#ho_name').val();
					var staff_id = $('#staff_id').val();
					mail_send(ho_name,staff_id);
				}

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

function init_datatable(table_id='',form_name='',action='' , filter_data = '') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
	data = {
		...data,
		...filter_data
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

function leave_permission_delete(unique_id = "") {

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

function days_by_dates () {

	// calculation of no. of days between two date
	// To set two dates to two variables
	let date1 = new Date("06/30/2019");
	let date2 = new Date("07/3/2019");

	// To calculate the time difference of two dates
	let Difference_In_Time = date2.getTime() - date1.getTime();

	// To calculate the no. of days between two dates
	let Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

	// Add Plus One For Inclusive of From Date or To Date
	return Difference_In_Days+1;
}

function dates_by_days () {
	
}

function leave_days_calculate () {

	
	var day_type    = $("#day_type").val();

	if(day_type != 5){

		var from_date    = $("#from_date").val();
		var to_date      = $("#to_date").val();


		if (from_date && to_date) {

			var from_date    = new Date(from_date);
			var to_date      = new Date(to_date);

			if (to_date < from_date) {

				$("#to_date").val("");

				sweetalert("custom","","","To Date Must to Lower then From Date")
				return false;
			}
		
			// To calculate the time difference of two dates 
			var Difference_In_Time = to_date.getTime() - from_date.getTime();
		
			// To calculate the no. of days between two dates 
			var Difference_In_Days = Math.ceil(Difference_In_Time / (1000 * 3600 * 24));
		
			$("#leave_days").val(Difference_In_Days + 1);
		}
	}else{
		var from_date    = $("#on_duty_from_date").val();
		var to_date      = $("#on_duty_to_date").val();


		if (from_date && to_date) {

			var from_date    = new Date(from_date);
			var to_date      = new Date(to_date);

			if (to_date < from_date) {

				$("#to_date").val("");

				sweetalert("custom","","","To Date Must to Lower then From Date")
				return false;
			}
		
			// To calculate the time difference of two dates 
			var Difference_In_Time = to_date.getTime() - from_date.getTime();
		
			// To calculate the no. of days between two dates 
			var Difference_In_Days = Math.ceil(Difference_In_Time / (1000 * 3600 * 24));
		
			$("#on_duty_leave_days").val(Difference_In_Days + 1);
		}

	}

}

function permission_time_calculate() {
	var from_time    = $("#from_time").val();
	var to_time      = $("#to_time").val();

	from_time 	     = time_to_minute(from_time);
	to_time 	     = time_to_minute(to_time);

	if (from_time && to_time) {

		if ((from_time + 120) < to_time) { // Permission 2 Hours
			$("#to_time").val("");
			$("#permission_time").val("");
			sweetalert("custom","","","Permission have only 2 Hours")
			return false;
		} else if (from_time > to_time) {
			$("#to_time").val("");
			$("#permission_time").val("");
			sweetalert("custom","","","To Time Must to Lower then From Time")
			return false;
		}
		
		let diff_time = to_time - from_time;
		diff_time 	  = minute_to_time(diff_time);
		
		$("#permission_time").val(diff_time);
	} else {
		$("#permission_time").val("");
	}
}

function get_ho_staff(staff_id = ''){
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	
	if (staff_id) {

		var data = {
			"staff_id" : staff_id,
			"action"   : "ho_staff_name"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {

				var obj           	 = JSON.parse(data);
				var unique_id    = obj.unique_id;
				var ho_staff_name    = obj.ho_staff_name;
				var status        	 = obj.status;
				
				if (ho_staff_name) {
					$("#ho_to_be_approved").html(ho_staff_name);
					$("#ho_name").val(unique_id);
				}else{
					$("#ho_to_be_approved").html('');
					$("#ho_name").val('');
				}
				get_staff_designation(unique_id);
			}			

		});
	}
}

function get_cancel_status (is_approved = '') {

    if (is_approved != 0) {
	
        $("#cancel_status").attr("required", "required");

		$(".cancel_status").removeClass("d-none");
	}
}

function leavepermissionFilter(filter_action = 0) {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var is_form = form_validity_check("was-validated");

	if (is_form) {
		var from_date       = $("#leave_from").val();
		var to_date         = $("#leave_to").val();
		
		var is_vaild = fromToDateValidity(from_date, to_date);

		if (is_vaild) {

			sessionStorage.setItem("leave_from_date", from_date);
			sessionStorage.setItem("leave_to_date", to_date);
			sessionStorage.setItem("leave_action", filter_action);
			 
			// Delete Below Line After Testing Complete
			sessionStorage.setItem("follow_up_call_action", 0);

			var filter_data = {
				"from_date": from_date,
				"to_date": to_date,
				"filter_action": filter_action
			};

			console.log(filter_data);

			init_datatable(table_id, form_name, action, filter_data);

		}

	} else {
		sweetalert("form_alert", "");
	}
}

function get_ceo_name (is_approved = '') {

    if (is_approved != 2) {
		$(".ceo_inp").removeAttr("required", "required");

		$(".ceo_staff_class").addClass("d-none");
	} else {
        $(".ceo_inp").attr("required", "required");

		$(".ceo_staff_class").removeClass("d-none");
	}
}

function get_staff_designation(staff_id = ''){
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	
	if (staff_id) {

		var data = {
			"staff_id" : staff_id,
			"action"   : "staff_designation"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {

				var obj           	 = JSON.parse(data);
				var designation      = obj.designation;
				var status        	 = obj.status;
				
				if (designation) {
					$("#designation").val(designation);
				}else{
					$("#designation").val('');
				}

				//get_ceo_approval(designation);
			}
		});
	}
}


// function get_ceo_approval (designation = '') {

//     if (designation != '5ff5d3423713a59778') {
// 		$(".lead_inp").removeAttr("required", "required");

// 		$(".leads_approval_class").addClass("d-none");
// 	} else {
//         $(".lead_inp").attr("required", "required");

// 		$(".leads_approval_class").removeClass("d-none");
// 	}
// }

function mail_send(ho_name = '',staff_name = '') {
	//var ajax_url = "folders/leave_permission/email.php"
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	if (ho_name) {

		var data = {
			"ho_name"  : ho_name,
			"staff_id" : staff_name,
			"action"   : "mail",
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) { 
				
				if (data) {
					
					sweetalert("custom",'','','Email sent successfully');
				}
			}
		});
	}
}