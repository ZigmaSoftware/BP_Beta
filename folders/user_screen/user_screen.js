$(document).ready(function () {
	// var table_id 	= "user_screen_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'user_screen';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'user_screen_datatable';
var action 			= "datatable";

function user_screen_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {

		
		var add_comma 	= false;
		var actions		= "";
		$('input[name="user_actions"]:checked').each(function() {
			if (add_comma) {
				actions   += ",";
			} else {
				add_comma = true;
			}
			actions += this.value;
		});

		// console.log(actions);


		var data 	 = $(".was-validated").serialize();
		data 		+= "&user_actions="+actions;



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

function user_screen_toggle(unique_id = "", new_status = 0) {
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
            const obj = JSON.parse(data);
            const msg = obj.msg;
            const status = obj.status;

            if (status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }

            sweetalert(msg, url);
        }
    });
}

// Get Section Names Based On Main Screen Selection
function get_sections (main_screen_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (main_screen_id) {
		var data = {
			"main_screen_id" 	: main_screen_id,
			"action"			: "sections"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
					$("#section_name").html(data);
				}

			}
		});
	}
} 


// CheckBox Change Functions
$('#all').change(function(e) {

	if (e.currentTarget.checked) {

		$('.action_check').prop('checked', true);

  	} else {

	  	$('.action_check').prop('checked', false);
	}

});

$('.action_check').change(function(e) {

	var all_check = 1;

	$('.action_check').each(function() {

		if (this.checked) {
			all_check *= 1;
		} else {
			all_check *= 0;
		}

		if (all_check) {
			$('#all').prop('checked', true);
		} else {
			$('#all').prop('checked', false);
		}
	});
});
