$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
// 	init_datatable(table_id,form_name,action);
    weighbridge_entry_go_btn();
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
var table_id 		= 'weighbridge_entry_datatable';
var action 			= "datatable";

function isFormValid(formId) {
    const form = document.getElementById(formId);
    if (form && form.checkValidity()) {
        return true;
    } else {
        form.reportValidity(); // Show native validation messages
        return false;
    }
}

function weighbridge_entry_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (isFormValid("myForm")) {

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

function init_datatable(table_id='',form_name='',action='', filter_data = "") {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
    data          = {
        ...data,
        ...filter_data
    };
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
		destroy: true, // allow reinitialization
		dom: 'Bfrtip', // Add Buttons control
    	buttons: [
            {
                extend: 'csvHtml5',
                text: 'Export CSV',
                title: 'Data_Export' // Optional: filename prefix
            }
        ],
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

function weighbridge_entry_delete(unique_id = "") {

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
					window.location.reload();
				}
			});

		} else {
			// alert("cancel");
		}
	});
}

function get_under_user_type_ids()
{ 
	var under_user_type= $('#under_user_type_name').val();
	$('#under_user_type').val(under_user_type);
}

function get_under_user_type (user_type = "") {

$("#under_user_type_name").html('');
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (staff_id) {
		var data = {
			"user_type" : user_type,
			"action"	: "user_type_options"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) { 
					$("#under_user_type_name").html(data);
				}

			}
		});
	}
}


function validateMonth() {
    const fromDateInput = document.getElementById('entry_date');
    const selectedDate = new Date(fromDateInput.value);
    const currentMonth = new Date();
    
    // Check if the selected date is in the future
    if (selectedDate > currentMonth) {
        fromDateInput.value = currentMonth.toISOString().slice(0, 7); // Set to current month
    }
}

function validateDateRange() {
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');

    const fromValue = fromDateInput.value;
    const toValue = toDateInput.value;

    // Reset styles first
    fromDateInput.style.borderColor = '';
    toDateInput.style.borderColor = '';

    // Check if both dates are selected
    if (!fromValue || !toValue) {
        alert("Please select both From Date and To Date.");
        if (!fromValue) fromDateInput.style.borderColor = "red";
        if (!toValue) toDateInput.style.borderColor = "red";
        return false;
    }

    // Convert input values to date objects (normalized to midnight)
    const fromDate = new Date(fromValue + "T00:00:00");
    const toDate = new Date(toValue + "T00:00:00");
    const today = new Date();
    today.setHours(0, 0, 0, 0);

    // Check if From Date is in the future
    if (fromDate > today) {
        alert("From Date cannot be in the future.");
        fromDateInput.style.borderColor = "red";
        return false;
    }

    // Check if To Date is in the future
    if (toDate > today) {
        alert("To Date cannot be in the future.");
        toDateInput.style.borderColor = "red";
        return false;
    }

    // Check if From Date is after To Date
    if (fromDate > toDate) {
        alert("From Date cannot be after To Date.");
        fromDateInput.style.borderColor = "red";
        toDateInput.style.borderColor = "red";
        return false;
    }

    return true;
}



let debounceTimer;

function validateWeightsOnInput() {
    clearTimeout(debounceTimer);

    debounceTimer = setTimeout(() => {
        const gross = parseInt(document.getElementById('gross_webtn_cancelight').value);
        const tare = parseInt(document.getElementById('tare_weight').value);

        const grossInput = document.getElementById('gross_webtn_cancelight');
        const tareInput = document.getElementById('tare_weight');
        const netField = document.getElementById('net_weight');

        if (!isNaN(gross) && !isNaN(tare)) {
            if (gross <= tare) {
                alert("Gross Weight must be greater than Tare Weight.");
                grossInput.style.borderColor = "red";
                tareInput.style.borderColor = "red";
                netField.value = "";
            } else {
                grossInput.style.borderColor = "";
                tareInput.style.borderColor = "";
                netField.value = gross - tare; 
            }
        } else {
            grossInput.style.borderColor = "";
            tareInput.style.borderColor = "";
            netField.value = "";
        }
    }, 500); // 500ms typing delay
}


function weighbridge_entry_go_btn() {
	var internet_status = is_online();
	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}
	var from_date       = $("#from_date").val();
	var to_date  = $("#to_date").val();
// 	if(year_month) {
		sessionStorage.setItem("from_date", from_date);
		sessionStorage.setItem("to_date", to_date);
        
		
		var filter_data = {
			"from_date": from_date,
			"to_date": to_date,
		};
		console.log(filter_data);
		init_datatable(table_id, form_name, action, filter_data);
// 	}
}