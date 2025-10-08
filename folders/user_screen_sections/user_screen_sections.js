$(document).ready(function () {
	// var table_id 	= "user_screen_sections_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'user_screen_sections';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'user_screen_sections_datatable';
var action 			= "datatable";

function user_screen_sections_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    // Collect form data directly (no validation check)
    var data = $("form").serialize();
    data    += "&unique_id=" + unique_id + "&action=createupdate";

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        beforeSend: function() {
            $(".createupdate_btn").attr("disabled","disabled");
            $(".createupdate_btn").text("Loading...");
        },
        success: function(data) {
            var obj     = JSON.parse(data);
            var msg     = obj.msg;
            var status  = obj.status;
            var error   = obj.error;

            if (!status) {
                url = '';
                $(".createupdate_btn").text("Error");
                console.log(error);
            } else {
                if (msg == "already") {
                    // Button Change Attribute
                    url = '';
                    $(".createupdate_btn").removeAttr("disabled","disabled");
                    if (unique_id) {
                        $(".createupdate_btn").text("Update");
                    } else {
                        $(".createupdate_btn").text("Save");
                    }
                }
            }
            sweetalert(msg, url);
        },
        error: function(data) {
            alert("Network Error");
        }
    });
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

function user_screen_sections_toggle(unique_id = "", new_status = 0) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            unique_id: unique_id,
            action: "toggle",
            is_active: new_status
        },
        success: function (data) {
            var obj = JSON.parse(data);
            var msg = obj.msg;
            var status = obj.status;

            if (status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }
            sweetalert(msg, url);
        }
    });
}

  
