$(document).ready(function () { 
	// var table_id 	= "loan_advances_datatable";
	init_datatable(table_id,form_name,action);
	get_designation();
	//$(".others_div").addClass("d-none");
	//get_loan_no();
	get_loan_type();
	get_emi_month_amount();
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'loan_advances_receivables';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'loan_advances_receivables_datatable';
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

function loanadvancereceivableFilter(filter_action = 0) {
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
				"from_date" 	: from_date,
				"to_date"		: to_date,
				"filter_action"	: filter_action
			};

			console.log(filter_data);

			init_datatable(table_id, form_name, action, filter_data);

		}

	} else {
		sweetalert("form_alert", "");
	}
}

function loan_advances_receivables_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
	}
	
	var password 		= $("#password").val();
	var con_password 	= $("#confirm_password").val();

	if (password !== con_password) {
		sweetalert("custom","","","Password Dosen't Match");
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


function loan_advances_receivables_delete(unique_id = "") {

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

function get_emi_amount(){
	var emi     = $('#emi').val(); 
	var amount  = $('#amount').val(); 

	var amt  = parseFloat(amount) / parseFloat(emi);
	if(emi!='' && amount!='' && emi!='0' && amount!='0'){
		document.getElementById("emi_amount").value=amt;
	} else {
		document.getElementById("emi_amount").value=0;
	}
}

function get_designation() {

	var staff_name  = $('#employee_name').val();
	var ajax_url    = sessionStorage.getItem("folder_crud_link");

	if(staff_name) {
		var data = {
			"staff_name"  : staff_name,
			"action"      : "designation"
		};

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {
				var obj     = JSON.parse(data);
				var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;


				var designation_unique_id   = data.designation_unique_id;
				var designation_name        = data.designation_name;

				document.getElementById('designation_name').innerHTML = designation_name;
			}
		});
	} else {
		document.getElementById('designation_name').innerHTML = '';
	}
}

function get_loan_no() {
	var staff_name  = $('#employee_name').val();
	var ajax_url    = sessionStorage.getItem("folder_crud_link");

    if(staff_name){
        //document.getElementById('purchase_label').innerHTML = "Purchase No";
        
        var data = {
        	"staff_name"    : staff_name,
            "action"        : "loan_advance_no"
        }

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            success : function(data) {

                if (data) { 
                    $("#loan_advance_no").html(data);
                }
            }
        });
    }
}

function get_loan_type(){
	var loan_advance_no  = $('#loan_advance_no').val();
	var ajax_url    = sessionStorage.getItem("folder_crud_link");

    if(loan_advance_no){
        
        var data = {
        	"loan_advance_no" : loan_advance_no,
            "action"          : "loan_advance_type"
        }

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            success : function(data) {

                if (data) { 
                	var obj     = JSON.parse(data);
					var data    = obj.data;
					var msg     = obj.msg;
					var status  = obj.status;
					var error   = obj.error;

                	if(data.loan_type == 1){
						document.getElementById('loan_type').innerHTML = "Loan";	
						document.getElementById('loan_type_no').value = "1";

							if(data.emi_type == 1){
								document.getElementById('emi_type_val').innerHTML   = "Monthly";
								document.getElementById('emi_type').value   = data.emi_type;
								document.getElementById('paid_amount_val').value   = data.amount;
								
							}else{
								
								document.getElementById('emi_type_val').innerHTML   = "Weekly";
								document.getElementById('emi_type').value   = data.emi_type;
								document.getElementById('paid_amount_val').value   = data.amount;
								

						    }	
						$(".others_div").addClass("d-none");
						$(".loan_div").removeClass("d-none");
					} else {
						document.getElementById('loan_type').innerHTML = "Advance";	
						document.getElementById('loan_type_no').value = "2";
						document.getElementById('paid_amount').innerHTML   = data.amount;
						document.getElementById('paid_amount_val').value   = data.amount;
						$(".loan_div").addClass("d-none");
						$(".others_div").removeClass("d-none");
					}
                }
            }
        });
    }
}


function get_emi_month_amount(){
	var loan_advance_no  = $('#loan_advance_no').val();
	var ajax_url    = sessionStorage.getItem("folder_crud_link");

    if(loan_advance_no){
        
        var data = {
        	"loan_advance_no" : loan_advance_no,
            "action"          : "emi_amt_mnth"
        }

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            success : function(data) {

                if (data) { 
                	var obj     = JSON.parse(data);
					var data    = obj.data;
					var msg     = obj.msg;
					var status  = obj.status;
					var error   = obj.error;

					document.getElementById('month').innerHTML         		= data.emi;
					document.getElementById('emi').innerHTML           		= data.emi_amount;
					document.getElementById('loan_percentage').innerHTML    = data.loan_percentage;
					
                }
            }
        });
    }
	sub_list_datatable("loan_sub_datatable", form_name, "loan_sub_datatable");

}


function sub_list_datatable(table_id = "", form_name = "", action = "") {

	var loan_advance_no = $('#loan_advance_no').val();

	var table = $("#" + table_id);
	var data = {
		"loan_advance_no": loan_advance_no,
		"action": table_id,
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		"searching" : false,
		"paging"    : false,
		// "ordering"  : false,
		"info"      : false,
		"ajax"      : {
			url         : ajax_url,
			type        : "POST",
			data        : data
		}
	});

    // console.log(datatable);
    // console.log(datatable.data());

    // alert(datatable.data().count());

	// datatable.on('xhr', function (e, settings, json) {

        
 //        if (table_id == "expense_sub_datatable") {

 //            var total = json['total_amt'];
 //            var count = json['count'];

 //            $("#expense_table_count").val(count);
 //            $("#total_amt").html(total);
 //        }


	// });


	// return datatable;
}




