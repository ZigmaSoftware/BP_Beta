// ===============================
// Document Ready Initialization
// ===============================
$(document).ready(function () { 
	const table_id = 'user_datatable';
	const form_name = 'user';
	const action = 'datatable';

	init_datatable(table_id, form_name, action);
	team_users_div($("#is_team_head").prop("checked"));

	// Trigger initial role toggle state
	toggleRoleFields($('#role').val());

	// React to role changes dynamically
	$('#role').on('change', function () {
		toggleRoleFields(this.value);
	});
});

// ===============================
// Session Variables
// ===============================
var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_address");
var company_phone 	= sessionStorage.getItem("company_phone");
var company_email 	= sessionStorage.getItem("company_email");
var company_logo 	= sessionStorage.getItem("company_logo");

var form_name 		= 'user';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'user_datatable';
var action 			= "datatable";

// ===============================
// Create / Update User
// ===============================
function user_cu(unique_id = "") {
	const work_location = $('#work_location').val();
	const internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	const password = $("#password").val();
	const con_password = $("#confirm_password").val();

	if (password !== con_password) {
		sweetalert("custom","","","Password Doesn't Match");
		return false;
	}

	// Check password strength
	const strength = validatePassword(true);
	if (strength < 5) {
		alert("Please create a stronger password before submitting.");
		return false;
	}

	const is_form = form_validity_check("was-validated");
	if (!is_form) {
		sweetalert("form_alert");
		return false;
	}

	let data = $(".was-validated").serialize();
	data += "&unique_id=" + unique_id + "&action=createupdate";

	const ajax_url = sessionStorage.getItem("folder_crud_link");
	const url = sessionStorage.getItem("list_link");

	$.ajax({
		type: "POST",
		url: ajax_url,
		data: data,
		beforeSend: function () {
			$(".createupdate_btn").attr("disabled", "disabled").text("Loading...");
		},
		success: function (data) {
			const obj = JSON.parse(data);
			const msg = obj.msg;
			const status = obj.status;
			const error = obj.error;

			if (!status) {
				console.error(error);
				$(".createupdate_btn").text("Error");
			} else {
				if (msg == "already") {
					$(".createupdate_btn").removeAttr("disabled");
					$(".createupdate_btn").text(unique_id ? "Update" : "Save");
				}
			}
			sweetalert(msg, url);
		},
		error: function () {
			alert("Network Error");
		}
	});
}

// ===============================
// DataTable Initialization
// ===============================
function init_datatable(table_id = '', form_name = '', action = '') {
	const ajax_url = sessionStorage.getItem("folder_crud_link");

	$("#" + table_id).DataTable({
		ordering: true,
		searching: true,
		ajax: {
			url: ajax_url,
			type: "POST",
			data: { action: action },
		},
	});
}

// ===============================
// Active Status Toggle
// ===============================
function user_toggle(unique_id = "", new_status = 0) {
	const ajax_url = sessionStorage.getItem("folder_crud_link");
	const url = sessionStorage.getItem("list_link");

	$.ajax({
		type: "POST",
		url: ajax_url,
		data: { unique_id: unique_id, action: "toggle", is_active: new_status },
		success: function (data) {
			const obj = JSON.parse(data);
			const msg = obj.msg;
			const status = obj.status;

			if (status) {
				$("#" + table_id).DataTable().ajax.reload(null, false);
			}
			sweetalert(msg, url);
		}
	});
}

// ===============================
// Dependent Dropdowns
// ===============================
function get_under_users(under_user = "") {
	$("#under_user_name").html('');
	const ajax_url = sessionStorage.getItem("folder_crud_link");

	if (under_user) {
		const data = { under_user: under_user, action: "user_options" };
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {
				if (data) $("#under_user_name").html(data);
			}
		});
	}
}

function get_under_user_ids() {
	const under_user = $('#under_user_name').val();
	$('#under_user').val(under_user);
}

function get_team_users_ids() {
	const team_users = $('#team_users_name').val();
	$('#team_users').val(team_users);
}

// ===============================
// Get Mobile No by Staff
// ===============================
function get_mobile_no(staff_id = "") {
	const ajax_url = sessionStorage.getItem("folder_crud_link");
	if (!staff_id) return;

	$.ajax({
		type: "POST",
		url: ajax_url,
		data: { staff_id: staff_id, action: "mobile" },
		success: function (response) {
			try {
				const data = JSON.parse(response);

				// Set mobile number
				if (data.mobile) {
					$("#phone_no").val(data.mobile);
				}

				// Set work location if available
				if (data.work_location) {
					// Clear any existing selections
					$("#work_location").val(null).trigger("change");

					// Pre-select location(s)
					const workLocations = data.work_location.split(",");
					$("#work_location").val(workLocations).trigger("change");
				}
			} catch (e) {
				console.error("Invalid JSON from server:", response);
			}
		},
		error: function (err) {
			console.error("Error fetching staff details", err);
		}
	});
}

// ===============================
// Team Users Section Toggle
// ===============================
function team_users_div(this_val = '') {
	if (this_val) {
		$(".team_users_class").removeClass("d-none");
	} else {
		$(".team_users_class").addClass("d-none");
		$("#team_users_name").val(null);
	}
}

// ===============================
// Password Validation
// ===============================
function validatePassword(checkOnly = false) {
	const password = document.getElementById("password").value;
	const submitBtns = document.getElementsByClassName("createupdate_btn");
	const checklist = document.getElementById("checklist");
	const strengthBar = document.getElementById("strength-bar");
	const strengthFill = document.getElementById("strength-fill");

	let strength = 0;

	// independent regex checks
	if (password.length >= 8) strength++;
	if (/[a-z]/.test(password)) strength++;
	if (/[A-Z]/.test(password)) strength++;
	if (/[0-9]/.test(password)) strength++;
	if (/[^A-Za-z0-9]/.test(password)) strength++;

	// when just checking on submit, skip visual updates
	if (checkOnly) return strength;

	// show visual feedback
	if (password.length > 0) {
		strengthBar.classList.remove("hidden");
	} else {
		strengthBar.classList.add("hidden");
		Array.from(submitBtns).forEach(btn => btn.disabled = true);
		strengthFill.style.width = "0%";
		return 0;
	}

	const colors = ["red", "orange", "gold", "dodgerblue", "green"];
	strengthFill.style.width = (strength * 20) + "%";
	strengthFill.style.background = colors[strength - 1] || "transparent";

	Array.from(submitBtns).forEach(btn => btn.disabled = strength < 5);
	return strength;
}

// ===============================
// Toggle Password Visibility
// ===============================
function togglePasswords() {
	const fields = [document.getElementById("password"), document.getElementById("confirm_password")];
	fields.forEach(field => {
		field.type = field.type === "password" ? "text" : "password";
	});
}

// ===============================
// Role Switching Logic
// ===============================
function toggleRoleFields(role) {
	const staffSection = document.getElementById('staff_section');
	const phoneField = document.getElementById('phone_no');
	const staffSelect = document.getElementById('full_name');

	if (!staffSection || !phoneField) return;

	if (role === 'Off Role') {
		// Hide staff section, make phone editable
		$(staffSection).hide();
		$(staffSelect).removeAttr('required');
		$(phoneField).removeAttr('readonly');
		$(phoneField).val(''); // allow manual entry
	} else {
		// Restore staff mode
		$(staffSection).show();
		$(staffSelect).attr('required', 'required');
		$(phoneField).attr('readonly', 'readonly');
	}
}
