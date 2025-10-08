$(document).ready(function () {

	init_datatable(table_id, form_name, action);
	
});

var company_name = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone = sessionStorage.getItem("company_name");
var company_email = sessionStorage.getItem("company_name");
var company_logo = sessionStorage.getItem("company_name");

var form_name = 'termination Letter';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'termination_letter';
var action = "datatable";

function staffFilter(filter_action = 0) {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var company_name = $('#company_name').val();

	sessionStorage.setItem("staff_action", filter_action);

	// Delete Below Line After Testing Complete
	sessionStorage.setItem("follow_up_call_action", 0);

	var filter_data = {

		"company_name": company_name,
		"filter_action": filter_action
	};

	console.log(filter_data);

	init_datatable(table_id, form_name, action, filter_data);

}

function termination_letter_cu(unique_id = "") {

	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var is_form = form_validity_check("was-validated");

	if (is_form) {

		var data = $(".was-validated").serialize();
		data += "&unique_id=" + unique_id + "&action=createupdate";

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url = sessionStorage.getItem("list_link");

		// console.log(data);
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			beforeSend: function () {
				$(".createupdate_btn").attr("disabled", "disabled");
				$(".createupdate_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					url = '';
					$(".createupdate_btn").text("Error");
					console.log(error);
				} else {
					if (msg == "already") {
						// Button Change Attribute
						url = '';

						$(".createupdate_btn").removeAttr("disabled", "disabled");
						if (unique_id) {
							$(".createupdate_btn").text("Update");
						} else {
							$(".createupdate_btn").text("Save");
						}
					}
				}

				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {
		sweetalert("form_alert");
	}
}


function init_datatable(table_id = '', form_name = '', action = '', filter_data = '') {
	var table = $("#" + table_id);
	var data = {
		"action": action,
	};
	data = {
		...data,
		...filter_data
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering: true,
		searching: true,
		"searching": true,
		"ajax": {
			url: ajax_url,
			type: "POST",
			data: data
		},

	});
}

function get_staff_details() {
	var staff_name = $('#staff_name').val();
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (staff_name) {
		var data = {
			"staff_name": staff_name,
			"action": "get_staffdetails"
		}

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {
				if (data && data.length > 0) {
					data = $.parseJSON(data);
					$.each(data.values, function (i, item) {
						var k = i + 1;

						document.getElementById('designation').value = data.values.designation_type;
						document.getElementById('department').value = data.values.department;
						document.getElementById('work_location').value = data.values.work_location;


					});
				}
			}
		});
	}
}

function termination_letter_delete(unique_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url = sessionStorage.getItem("list_link");

	confirm_delete('delete')
		.then((result) => {
			if (result.isConfirmed) {

				var data = {
					"unique_id": unique_id,
					"action": "delete"
				}

				$.ajax({
					type: "POST",
					url: ajax_url,
					data: data,
					success: function (data) {

						var obj = JSON.parse(data);
						var msg = obj.msg;
						var status = obj.status;
						var error = obj.error;

						if (!status) {
							url = '';

						} else {
							init_datatable(table_id, form_name, action);
						}
						sweetalert(msg, url);
					}
				});

			} else {
				
			}
		});
}
