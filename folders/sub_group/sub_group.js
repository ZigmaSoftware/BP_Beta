$(document).ready(function () {
	// var table_id 	= "units_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'group';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'sub_group_datatable';
var action 			= "datatable";

function sub_group_cu(unique_id = "") {

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

				sweetalert(msg,url,'sub_group_name');
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("form_alert");
    }
}

function sub_group_filter(){
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var group_unique_id = $('#group_unique_id').val();

	var filter_data = {

		"group_unique_id": group_unique_id
	};


	init_datatable(table_id, form_name, action, filter_data);


}

function init_datatable(table_id='',form_name='',action='', filter_data = '') {

	var table = $("#"+table_id);
	
	var data = {
		"action"	: action, 
		...filter_data
	}
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

function sub_group_toggle(unique_id = "", new_status = 0) {
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


document.getElementById('sub_group_name').addEventListener('input', function () {
    const groupName = this.value.trim();

    // Remove all non-alphanumeric characters
    const cleanName = groupName.replace(/[^a-zA-Z0-9]/g, '');

    // Get the first 3 characters and convert to uppercase
    const code = cleanName.substring(0, 3).toUpperCase();

    document.getElementById('code').value = code;
});

function get_group_code(code){

    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (code) {
		var data = {
			"code" 	: code,
			"action": "get_group_code"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			dataType: 'json',
			success : function(data) {
            
				if (data.status === 'success' && data.data) {
                    $("#group_code").val(data.data);
                }

			}
		});
	}
	
}


