$(document).ready(function () {
	// var table_id 	= "leave_permission_datatable";
	//init_datatable(table_id,form_name,action);
	datatable_init_based_on_prev_state();
	get_type_div();
	get_loan_percentage();
	
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
var table_id 		= 'loan_advance_datatable';
var action 			= "datatable";

function datatable_init_based_on_prev_state () {
		// Data Table Filter Function Based ON Previous Search
		var from_date  	    = sessionStorage.getItem("from_date");
		var to_date         = sessionStorage.getItem("to_date");
		var filter_action   = sessionStorage.getItem("action");
	
		if (!from_date) {
			from_date = $("#from_date").val();
		} else {
			$("#from_date").val(from_date);
		}
	
		if (!to_date) {
			to_date = $("#to_date").val();
		} else {
			$("#to_date").val(to_date);
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


function loan_advances_approval_account_cu (unique_id = "") {

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




function loanadvanceaccountFilter(filter_action = 0) {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var is_form = form_validity_check("was-validated");

	if (is_form) {
		var from_date       = $("#from_date").val();
		var to_date         = $("#to_date").val();
		
		var is_vaild = fromToDateValidity(from_date, to_date);

		if (is_vaild) {

			sessionStorage.setItem("from_date", from_date);
			sessionStorage.setItem("to_date", to_date);
			sessionStorage.setItem("action", filter_action);
			 
			// Delete Below Line After Testing Complete
			//sessionStorage.setItem("follow_up_call_action", 0);

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


function get_payment_type (payment_type = '') {

    if (payment_type == 1) {
	
        // $(".bank_name_class").attr("required", "required");
		$(".bank_name_class").removeClass("d-none");
		// $(".account_no_class").attr("required", "required");
		$(".account_no_class").removeClass("d-none");
		// $(".ifsc_code_class").attr("required", "required");
		$(".ifsc_code_class").removeClass("d-none");
		// $(".branch_name_class").attr("required", "required");
		$(".branch_name_class").removeClass("d-none");
		// $(".upi_id_class").prop("required", "false");
		$(".upi_id_class").addClass("d-none");
		// $(".cheque_no_class").attr("required", "false");
	    $(".cheque_no_class").addClass("d-none");

	} else  if (payment_type == 2) {
	
        // $(".bank_name_class").attr("required", "false");
		$(".bank_name_class").addClass("d-none");
		// $(".account_no_class").attr("required", "false");
		$(".account_no_class").addClass("d-none");
		// $(".branch_name_class").attr("required", "false");
		$(".branch_name_class").addClass("d-none");
		// $(".upi_id_class").attr("required", "false");
		$(".upi_id_class").addClass("d-none");
		// $(".ifsc_code_class").attr("required", "false");
		$(".ifsc_code_class").addClass("d-none");
		// $(".cheque_no_class").attr("required", "false");
		$(".cheque_no_class").addClass("d-none");

	}
	else  if (payment_type == 3) {
	
        // $(".bank_name_class").attr("required", "required");
		$(".bank_name_class").removeClass("d-none");
		// $(".account_no_class").attr("required", "required");
		$(".account_no_class").removeClass("d-none");
		// $(".branch_name_class").attr("required", "required");
		$(".branch_name_class").removeClass("d-none");
		// $(".upi_id_class").attr("required", "required");
		$(".upi_id_class").removeClass("d-none");
		// $(".ifsc_code_class").attr("required", "false");
		$(".ifsc_code_class").addClass("d-none");
		// $(".cheque_no_class").attr("required", "false");
		$(".cheque_no_class").addClass("d-none");

	} else  if (payment_type == 4) {
	    // $(".bank_name_class").attr("required", "required");
		$(".bank_name_class").removeClass("d-none");
		// $(".branch_name_class").attr("required", "required");
		$(".branch_name_class").removeClass("d-none");
        // $(".cheque_no_class").attr("required", "required");
		$(".cheque_no_class").removeClass("d-none");
		// $(".account_no_class").attr("required", "false");
		$(".account_no_class").addClass("d-none");
		// $(".upi_id_class").attr("required", "false");
		$(".upi_id_class").addClass("d-none");
		// $(".ifsc_code_class").attr("required", "false");
		$(".ifsc_code_class").addClass("d-none");
	} 
}


function get_ceo_name (is_approved = '') {

    if (is_approved != 1) {
		$(".ceo_inp").removeAttr("required", "required");

		$(".ceo_staff_class").addClass("d-none");
	} else {
        $(".ceo_inp").attr("required", "required");

		$(".ceo_staff_class").removeClass("d-none");
	}
}

function get_type_div() {
	// Default 
	$(".loan_advance_div").addClass("d-none");
	$(".loan_advance_prop").prop("required",false);

	let loan_type = $("#loan_type").val();


	if (loan_type) {
		if (loan_type == 1) {
			$(".loan_div").removeClass("d-none");
			$(".loan_prop").prop("required",true);
			
		}else if (loan_type == 2) {
			$(".loan_div").addClass("d-none");
			$(".loan_prop").prop("required",false);
			$(".others_div").addClass("d-none");
			$(".others_prop").prop("required",false);
			
		} else {
			$(".others_div").removeClass("d-none");
			$(".others_prop").prop("required",true);
			
		}
	}
}


function get_loan_percentage(){

	var loan_type  = $('#loan_type').val(); 
	var amount     = $('#amount').val(); 

	if((amount >= 30000)&&(loan_type == 1)){
	
	$(".percentage_div").removeClass("d-none");
    $(".percentage_prop").prop("required",true);


	} else {


	$(".percentage_div").addClass("d-none");
	$(".percentage_prop").prop("required",false);
	}
	
}