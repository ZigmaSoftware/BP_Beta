$(document).ready(function () {
	// var table_id 	= "units_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'category';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'item_master_datatable';
var action 			= "datatable";

function item_master_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var form = document.querySelector(".was-validated");
    
    if (!form.checkValidity()) {
        form.classList.add("was-validated"); // Optional: bootstrap-style visual feedback
        sweetalert("form_alert");
        return false;
    }
    // Temporarily disable checkbox before serialize to avoid duplicate
		$("#qc_approval").prop("disabled", true);
		$("#qc_approval_final").prop("disabled", true);

		var data = $(".was-validated").serialize();

		// Re-enable after serialize
		$("#qc_approval").prop("disabled", false);
		$("#qc_approval_final").prop("disabled", false);

		// Now append manually
		var qcApproval = $("#qc_approval").is(":checked") ? 1 : 0;
		var qcApprovalFinal = $("#qc_approval_final").is(":checked") ? 1 : 0;
// 		alert(qcApprovalFinal);
		data += "&qc_approval=" + qcApproval;
		data += "&qc_final=" + qcApprovalFinal;


		data += "&unique_id=" + unique_id + "&action=createupdate";

// 		alert(data);

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

        console.info(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".createupdate_btn").attr("disabled","disabled");
				$(".createupdate_btn").text("Loading...");
			},
			success		: function(data) {

				// var obj     = JSON.parse(data);
				var msg     = data.msg;
				var status  = data.status;
				var error   = data.error;

				if (!status) {
					url 	= '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
				} else {
					if (msg=="group_alert") {
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

}

function item_filter() {
	var internet_status = is_online();
	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var group_unique_id     = $('#group_unique_id').val();
	var sub_group_unique_id = $('#sub_group_unique_id').val();
	var category_unique_id  = $('#category_unique_id').val();

	var filter_data = {
		group_unique_id: group_unique_id,
		sub_group_unique_id: sub_group_unique_id,
		category_unique_id: category_unique_id
	};

	// Re-initialize and reset page
	init_datatable("item_master_datatable", form_name, action, filter_data);

	// After slight delay, reset to page 1
	setTimeout(() => {
		$('#item_master_datatable').DataTable().page(0).draw(false);
	}, 300);
}

function init_datatable(table_id = '', form_name = '', action = '', filter_data = {}) {
	var table = $("#" + table_id);

	// Destroy existing table to reset state (including page)
	if ($.fn.DataTable.isDataTable(table)) {
		table.DataTable().clear().destroy();
	}

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	table.DataTable({
		ordering: true,
		searching: true,
		pageLength: 10,
		displayStart: 0, // ✅ Force start from page 1
		ajax: {
			url: ajax_url,
			type: "POST",
			data: function (d) {
				// Append action and filter data dynamically
				d.action = action;
				Object.assign(d, filter_data);
			}
		}
	});
}



function item_master_toggle(unique_id = "", new_status = 0) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "toggle",
            unique_id: unique_id,
            is_active: new_status
        },
        success: function (data) {
            const obj = JSON.parse(data);
            sweetalert(obj.msg, url);

            if (obj.status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }
        }
    });
}

document.getElementById('category_name').addEventListener('input', function () {
    const groupName = this.value.trim();

    // Remove all non-alphanumeric characters
    const cleanName = groupName.replace(/[^a-zA-Z0-9]/g, '');

    // Get the first 3 characters and convert to uppercase
    const code = cleanName.substring(0, 3).toUpperCase();

    document.getElementById('code').value = code;
});

function get_code(code){

    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (code) {
		var data = {
			"code" 	: code,
			"action": "get_group_code",
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			dataType: 'json',
			success : function(data) {
				if (data.status === 'success' && data.data) {
                    $("#sub_group_code").val(data.data);
                }
			}
		});
	}
	
}


function get_sub_group(group_id, category = ""){
    
    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (group_id) {
		var data = {
			"group_id" 	: group_id,
			"category" 	: category,
			"action"		: "sub_group_name"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
                if(category === ""){
    				if (data) {
    					$("#sub_group_unique_id").html(data);
    				}
                } else {
                    if (data) {
    					$("#category_unique_id").html(data);
    				}
                }

			}
		});
	}
}


// Show Category Modal
function create_category () {
    // alert("Dsd");
    $("#group-modal").modal("show");
}

