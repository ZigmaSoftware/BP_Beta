$(document).ready(function () {
	init_datatable(table_id,form_name,action);

    // toggleCreationType(); // Apply mode logic

    const createdType = $("input[name='creation_type']:checked").val();
    const salesOrderId = $("#sales_order_id").val();
    const companyId = $("#company_name").val();

    if (createdType === "sales_order" && salesOrderId) {
        $("#sales_order_id").prop("disabled", false);
        fetchSalesOrderDetails();
    }

    if (createdType === "normal" && companyId) {
        // Delay to ensure Select2 has initialized and value is bound
        setTimeout(function () {
            get_company_code(companyId);
        }, 300); // Adjust if needed
    }
});



var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'project_creation';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'project_creation_datatable';
var action 			= "datatable";

function project_creation_cu(unique_id = "") {
    var internet_status       = is_online();
    var data                  = new FormData();
    var creation_type         = $("input[name='creation_type']:checked").val(); // new
    var company_name          = $("#company_name").val();
    var company_code          = $("#company_code").val();
    var client_name           = $("#client_name").val();
    var capacity              = $("#capacity").val();
    var project_name          = $("#project_name").val();
    var project_code          = $("#project_code").val();
    var project_date          = $("#project_date").val();
    var duration              = $("#duration").val();
    var cost_center           = $("#cost_center").val(); 
    var application_type      = $("#application_type").val();
    var country               = $("#country").val();
    var state                 = $("#state").val();
    var city                  = $("#city").val();
    var address               = $("#address").val();
    var pin_code              = $("#pin_code").val().trim();
    var pan_number            = $("#pan_number").val().trim();
    var gst_number            = $("#gst_number").val().trim();
    var gst_reg_date          = $("#gst_reg_date").val();
    var contact_person        = $("#contact_person").val();
    var contact_number        = $("#contact_number").val().trim();
    var contact_email_id      = $("#contact_email_id").val();
    var website               = $("#website").val().trim();
    var file_data             = $('#company_logo').prop('files')[0];
    var description           = $("#description").val().trim();
    var existing_company_logo = $("#existing_company_logo").val();
    var is_active             = $("#is_active").val();
    var ajax_url              = sessionStorage.getItem("folder_crud_link");
    var url                   = sessionStorage.getItem("list_link");
    var is_form               = form_validity_check("was-validated");
// alert(client_name);
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    if (pin_code.length !== 6) {
        alert("Check Pincode");
        return false;
    }

    if (contact_number !== "") {
        if (contact_number.length !== 10 || !/^\d{10}$/.test(contact_number)) {
            alert("Check Contact Number");
            return false;
        }
    }

    if (is_form) {
        data.append("company_name", company_name);
        data.append("company_code", company_code);
        if (creation_type === "normal") {
            data.append("client_name", client_name); // only when normal
        } else {
            var sales_order_id = $("#sales_order_id").val();
            data.append("sales_order_id", sales_order_id); // only when sales order
            data.append("client_name", client_name);
        }
        data.append("created_type", creation_type);
        data.append("capacity", capacity);
        data.append("project_name", project_name);
        data.append("project_code", project_code);
        data.append("project_date", project_date);
        data.append("duration", duration);
        data.append("cost_center", cost_center);
        data.append("application_type", application_type);
        data.append("country", country);
        data.append("state", state);
        data.append("city", city);
        data.append("address", address);
        data.append("pin_code", pin_code);
        data.append("pan_number", pan_number);
        data.append("gst_number", gst_number);
        data.append("gst_reg_date", gst_reg_date);
        data.append("contact_person", contact_person);
        data.append("contact_number", contact_number);
        data.append("contact_email_id", contact_email_id);
        data.append("website", website);
        data.append("company_logo", file_data);
        data.append("description", description);
        data.append("is_active", is_active);
        data.append("existing_company_logo", existing_company_logo);
        data.append("unique_id", unique_id);
        data.append("action", "createupdate");

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function () {
                $(".createupdate_btn").attr("disabled", "disabled").text("Loading...");
            },
            success: function (data) {
                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".createupdate_btn").text("Error");
                    console.log(error);
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

    } else {
        sweetalert("form_alert");
    }
}



function project_filter() {
    init_datatable(table_id, form_name, action);
}
function init_datatable(table_id='',form_name='',action='') {
	
	var table           = $("#"+table_id);
	var from_date       = $('#from_date').val();
	var to_date       = $('#to_date').val();
	var company_name    = $('#company_name').val();
	var project_name    = $('#project_name').val();
	var application_type    = $('#application_type').val();
// 	alert(application_type);

	var data 	  = {
		"action"	   : action, 
		"from_date"    : from_date,
        "to_date"      : to_date,
        "company_name" : company_name,
        "project_name" : project_name,
        "application_type" : application_type,
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

function project_creation_delete(unique_id = "") {

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

function get_company_code (company_id = "") {
// alert(company_id);
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (company_id) {
		var data = {
			"company_id" 	: company_id,
			"action"		: "get_company_code"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
// alert(data);
				if (data) { 
				$("#company_code").val(data);
				}

			}
		});
	}
}



// Get State Names Based On Country Selection
function get_states (country_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (country_id) {
		var data = {
			"country_id" 	: country_id,
			"action"		: "states"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) { 
					$("#state").html(data);
				}

			}
		});
	}
}



// Get city Names Based On State Selection
function get_cities (state_id = "") {


	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (state_id) {
		var data = {
			"state_id" 	    : state_id,
			"action"		: "cities"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
					$("#city").html(data);
				}

			}
		});
	}
}  





function number_only_pincode(event) {
    var charCode = event.which ? event.which : event.keyCode;

    // Allow only numbers (0-9)
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
        return false;
    }

    // Prevent entering more than 6 digits
    var input = document.getElementById("pin_code");
    if (input.value.length >= 6) {
        event.preventDefault();
        return false;
    }

    return true;
}



 
function validate_pan_number(event) {
    const input = event.target;
    const charCode = event.which || event.keyCode;
    const char = String.fromCharCode(charCode);

    // Block if total length is already 10
    if (input.value.length >= 10) {
        event.preventDefault();
        return false;
    }

    // PAN pattern position-based validation
    const position = input.value.length;

    if (position < 5) {
        // First 5 characters: only letters A-Z
        if (!/[a-zA-Z]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position < 9) {
        // Next 4 characters: only numbers 0-9
        if (!/[0-9]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else {
        // Last character: only a letter A-Z
        if (!/[a-zA-Z]/.test(char)) {
            event.preventDefault();
            return false;
        }
    }

    // Convert to uppercase after typing
    setTimeout(() => {
        input.value = input.value.toUpperCase();
    }, 0);

    return true;
}

function number_only_gst(event) {
    const charCode = event.which || event.keyCode;
    const char = String.fromCharCode(charCode);

    // Allow only alphanumeric A-Z, 0-9
    if (!/^[a-zA-Z0-9]$/.test(char)) {
        event.preventDefault();
        return false;
    }

    const input = event.target;

    // Limit to 15 characters
    if (input.value.length >= 15) {
        event.preventDefault();
        return false;
    }

    // Automatically convert lowercase to uppercase
    setTimeout(() => {
        input.value = input.value.toUpperCase();
    }, 0);

    return true;
}



function number_only_mobile(event) {
    var charCode = event.which ? event.which : event.keyCode;

    // Allow only numbers (0-9)
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
        return false;
    }

    // Prevent entering more than 6 digits
    var input = document.getElementById("contact_number");
    if (input.value.length >= 10) {
        event.preventDefault();
        return false;
    }

    return true;
}



function get_branch_div() {

	var company_branch_type = $("#company_branch_type").val();

	if ((company_branch_type == 1) || (company_branch_type == "")) {
		$(".branch_div").hide();
		$(".branch_div_input").removeAttr('required');
	} else {
		$(".branch_div").show();
		$(".branch_div_input").attr("required", "true");
	}
}

function new_external_window_image(image_url) {
    // Open the image in a new window
    window.open(image_url, '_blank', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}

function toggleCreationType() {
    const isSalesOrder = $("#sales_create").is(":checked");

    $("#sales_order_id").prop("disabled", !isSalesOrder);
    $("#company_name").prop("disabled", isSalesOrder);
    $("#client_name").prop("readonly", isSalesOrder);

    if (isSalesOrder) {
        // Clear Normal-mode values
        $("#company_name").val("").trigger("change");
        $("#company_code").val("");
        $("#client_name").val("");
        $("#customer_id").val("");
    } else {
        // Clear Sales Order values
        $("#sales_order_id").val("").trigger("change");
        $("#company_name").val("").trigger("change");
        $("#company_code").val("");
        $("#client_name").val("");
        $("#customer_id").val("");
    }
}


function fetchSalesOrderDetails() {
    const sales_order_id = $("#sales_order_id").val();
    const ajax_url = sessionStorage.getItem("folder_crud_link");

    if (sales_order_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "get_sales_order_data",
                sales_order_id: sales_order_id
            },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status) {
                    $("#company_name").val(data.company_id).trigger("change");
                    $("#client_name").val(data.client_name);
                    $("#customer_id").val(data.customer_id); // Add this line

                } else {
                    alert("Unable to fetch sales order data.");
                }
            }
        });
    }
}
