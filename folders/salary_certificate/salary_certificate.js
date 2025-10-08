$(document).ready(function () {
	// var table_id 	= "salary_certificate_datatable";
	// relieve_date_show();

	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");
var ajax_url        = sessionStorage.getItem("folder_crud_link");

var form_name 		= 'User Type';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'salary_certificate_datatable';
var action 			= "datatable";

function salary_certificate_cu(unique_id = "") {

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

function salary_certificate_delete(unique_id = "") {

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

function relieve_date_show() {

	let cer_type = $("#certificate_type").val();


	if (cer_type == 2) {
		$(".relieve_date_div").removeClass("d-none");
		$(".relieve_date_inp").attr("required","required");
	// $("#relieve_date").val("");

	} else {		
		$(".relieve_date_div").addClass("d-none");
		$(".relieve_date_inp").removeAttr("required");
	}
}

function get_staff_details(staff_id = "") {
	if (staff_id) {
		$.ajax({
			type : "POST",
			url  : ajax_url,
			data : {
				staff_id : staff_id,
				action   : "staff_details"
			},
			success : function (res) {
				console.log(res);

				var details = JSON.parse(res);

				$("#designation").val(details['designation']);
				$("#join_date").val(details['date_of_join']);
				$("#department").val(details['department']);
				$("#relieve_date").val(details['relieve_date']);
				$("#gross_salary").val(details['salary']);
			}
		});
	}	
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
		// $(".with_header_footer").addClass("d-print-none");
		// $(".with_header_footer_top").css("margin-top","100px");
		$(".backgrounds").removeClass("d-print-none");
		$(".print_backgrounds").removeClass("d-none");
	} else {
		$(".backgrounds").addClass("d-print-none");
		$(".print_backgrounds").addClass("d-none");
		// $(".with_header_footer").removeClass("d-print-none");
		// $(".with_header_footer_top").removeAttr("style");
	}
}

// function letter_head_change(letter_head_branch = '',img_id = "letter_head_background") {

// 	if (letter_head_branch) {
// 		var header_img = "img/letter_back/"+letter_head_branch+"-header.png";
// 		var footer_img = "img/letter_back/"+letter_head_branch+"-footer.png";

// 		$("#header_img").attr("src",header_img);
// 		$("#footer_img").attr("src",footer_img);
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