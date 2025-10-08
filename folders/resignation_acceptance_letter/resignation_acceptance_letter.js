$(document).ready(function () {

	init_datatable(table_id, form_name, action);
	
});

var company_name = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone = sessionStorage.getItem("company_name");
var company_email = sessionStorage.getItem("company_name");
var company_logo = sessionStorage.getItem("company_name");

var form_name = 'User Type';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'resignation_acceptance_letter';
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

function resignation_acceptance_letter_cu(unique_id = "") {

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

						document.getElementById('company_name').value = data.values.company_name;
						document.getElementById('emp_code').value = data.values.employee_id;
						document.getElementById('designation').value = data.values.designation_type;
						document.getElementById('join_date').value = data.values.date_of_join;
						document.getElementById('department').value = data.values.department;
						document.getElementById('branch').value = data.values.work_location;

					});
				}
			}
		});
	}
}

function resignation_acceptance_letter_delete(unique_id = "") {

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
				// alert("cancel");
			}
		});
}

function get_tds_deduction(tds_deduction = '') {

	if (document.getElementById('tds_deduction').checked) {
		$("#tds_deduction_status").val('1');
	} else {
		$("#tds_deduction_status").val('0');
	}

}

function get_performance_bonus(performance_bonus = '') {

	if (document.getElementById('performance_bonus').checked) {
		$("#performance_bonus_status").val('1');
	} else {
		$("#performance_bonus_status").val('0');
	}

}

// function letter_head_change(letter_head_branch = '', img_id = "letter_head_background") {

// 	if (letter_head_branch) {
// 		var header_img = "img/letter_back/" + letter_head_branch + "-header.png";
// 		var header_img1 = "img/letter_back/" + letter_head_branch + "-header.png";
// 		var footer_img = "img/letter_back/" + letter_head_branch + "-footer.png";

// 		$("#header_img_1").attr("src", header_img1);
// 		$("#header_img").attr("src", header_img);
// 		$("#footer_img").attr("src", footer_img);

// 	}
// }
function letter_head_change(letter_head_branch = '',branch = '',img_id = "letter_head_background") {
	// alert(letter_head_branch)
	
	var letter_head_branch = $('#company_name').val();
	var branch = $('#branch').val();
	
	if(letter_head_branch == "comp64f5828e7ad3283886"){
		var id = 1;
	}
	else if(letter_head_branch == "comp64fff278765fc93189"){
		var id = 2;
	}
	else if(letter_head_branch == "comp64fff33f28b4478803"){
		var id = 3;
	}
	
	// alert(id);
	// alert(branch);
if (id==1 && branch==1) {

var header_img = "img/letter_back/headernew-1.jpg";

}else if(id==1 && branch==2) {

var header_img = "img/letter_back/"+branch+"-header.png";
}else if (id==1 && branch==3) {
var header_img = "img/letter_back/"+branch+"-header.png";
}if (id==2 && branch==1) {

var header_img = "img/letter_back/headernew-3.jpg";
}else if(id==2 && branch==2) {

var header_img = "img/letter_back/headernew-5.jpg";
}else if (id==2 && branch==3) {
var header_img = "img/letter_back/headernew-5.jpg";
}else if(id==2 && branch== ''){
var header_img = "img/letter_back/headernew-5.jpg";
}else if(id==1 && branch== ''){
var header_img = "img/letter_back/2-header.png";
}else if(id==3 && branch== ''){
var header_img = "img/letter_back/headernew-2.jpg";
}
// var header_img1 = "img/letter_back/"+id+"-header.png";
// var footer_img = "img/letter_back/"+id+"-footer.png";

// var header_img = "img/letter_back/header/"+letter_head_branch+"-header.png";
// var header_img1 = "img/letter_back/header/"+letter_head_branch+"-header.png";
// var footer_img = "img/letter_back/header/"+letter_head_branch+"-footer.png";

// $("#header_img_1").attr("src",header_img1);
$("#header_img").attr("src",header_img);
$("#header_img1").attr("src",header_img);
$("#header_img2").attr("src",header_img);
$("#header_img3").attr("src",header_img);
$("#header_img4").attr("src",header_img);
$("#header_img5").attr("src",header_img);
// $("#footer_img").attr("src",footer_img);

// }
}

function get_ctc() {
	var gross_salary = $("#gross_salary").val();
	var medical_insurance_premium = $("#medical_insurance_premium").val();
	var performance_allowance = $("#performance_allowance").val();
	var income_tax = $("#income_tax").val();
	var professional_tax = $("#professional_tax").val();
	var other_deduction = $("#other_deduction").val();

	if (gross_salary) {
		var gs = gross_salary;
	} else {
		var gs = 0;
	}

	if (medical_insurance_premium) {
		var mip = medical_insurance_premium;
	} else {
		var mip = 0;
	}

	if (performance_allowance) {
		var pa = performance_allowance;
	} else {
		var pa = 0;
	}

	if (income_tax) {
		var it = income_tax;
	} else {
		var it = 0;
	}

	if (professional_tax) {
		var pt = professional_tax;
	} else {
		var pt = 0;
	}

	if (other_deduction) {
		var od = other_deduction;
	} else {
		var od = 0;
	}

	var ctc = parseFloat(gs) + parseFloat(mip) + parseFloat(pa);
	var net_salary = parseFloat(gs) - (parseFloat(it) + parseFloat(pt) + parseFloat(od));
	$('#ctc').val(ctc);
	$('#net_salary').val(net_salary);
}

function print_header_and_footer () {

	var user_id = $("#user_id").val();
	var branch_seal = $("#branch").val();

	if (user_id) {
		var sign_png 	= "img/letter_back/signs/"+user_id+".png";
		$("#user_sign").attr("src",sign_png);
	}

	if (!branch_seal) {
		branch_seal  = 1;
	}

	var branch_seal = "img/letter_back/"+branch_seal+"-seal.png";

	$("#branch_seal").attr("src",branch_seal);

	if ($("#print_header_footer").prop("checked")) {

		$(".backgrounds").removeClass("d-print-none");
		$(".print_backgrounds").removeClass("d-none");
	} else {

		$(".backgrounds").addClass("d-print-none");
		$(".print_backgrounds").addClass("d-none");
	}
}