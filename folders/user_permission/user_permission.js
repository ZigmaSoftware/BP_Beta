$(document).ready(function () {
	// var table_id 	= "user_permission_datatable";
	init_datatable(table_id,form_name,action);
	init_sub_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'user_permission';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'user_permission_datatable';
var action 			= "datatable";

function perm_ui_val() {
	var main_screen = $("#main_screen").val();
	var update_user_type = $("#update_user_type").val();
	// alert(main_screen);
	if (main_screen) {
		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");
		
		var data 	 = {
			"action" 		: "permission_ui",
			"main_screen" 	: main_screen,
			"user_type" 	: update_user_type
		}

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success		: function(data) {
				$("#perm_ui").html(data);
				init_sub_datatable(table_id,form_name,action);
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
	}
}

function check_all(class_name = "",this_obj = "") {

	if (this_obj.type == "button") {

		is_check = $(this_obj).val();

		if (is_check == "unchecked") {
			$('.'+class_name).each(function () {
				$(this).prop('checked', true); // checks it
			});
			$(this_obj).attr("data-check","checked");
			$(this_obj).val("checked");
		} else {

			$('.'+class_name).each(function () {
				$(this).prop('checked', false); // checks it
			});
			$(this_obj).attr("data-check","unchecked");
			$(this_obj).val("unchecked");
		}
	} else {
		if (this_obj.checked) {
			$('.'+class_name).each(function () {
				$(this).prop('checked', true); // checks it
			});
		} else {
			$('.'+class_name).each(function () {
				$(this).prop('checked', false); // Un Checks it
			});
		}
	}
}

function check_me (class_name = "") {
	var is_value = 1;

	if (class_name) {
		$('.allcheck-'+class_name).each(function () {
			if (!this.checked) {
				is_value *= 0;
			} 
		});

		if (is_value) {
			$("#all"+class_name).prop('checked',true);
		} else {
			$("#all"+class_name).prop('checked',false);
		}
	}
}

function user_permission_cu(unique_id = "") {
    if (!is_online()) { sweetalert("no_internet"); return; }
    if (!form_validity_check("was-validated")) { sweetalert("form_alert"); return; }

    // collect checked permissions
    const data_obj = [];
    $('.all-checkbox:checked').each(function () {
        data_obj.push({
            main: $(this).data("main"),
            section: $(this).data("section"),
            screen: $(this).data("screen"),
            action: $(this).data("action")
        });
    });

    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const list_url = sessionStorage.getItem("list_link"); // <-- keep this

    let data = $(".was-validated").serialize();
    data += "&json_data=" + encodeURIComponent(JSON.stringify(data_obj));
    data += "&unique_id=" + encodeURIComponent(unique_id) + "&action=createupdate";

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        dataType: "json", // expect JSON
        beforeSend: function () {
            $(".createupdate_btn").attr("disabled", "disabled").text("Loading...");
        },
        success: function (obj) {
            const { msg, status, error } = obj || {};
            if (!status) {
                console.log(error);
                $(".createupdate_btn").text("Error").prop("disabled", false);
                sweetalert(msg || "error");
                $("#main_screen").focus();
                return;
            }

            // restore button text
            $(".createupdate_btn").prop("disabled", false)
                .text(unique_id ? "Update" : "Save");

            sweetalert(msg || "create");

            // ✅ redirect only on success
            if (list_url && typeof list_url === "string") {
                // use replace() if you don't want the form page in history
                window.location.href = list_url;
                // or: window.location.replace(list_url);
            } else {
                console.warn("list_link missing in sessionStorage; cannot redirect.");
            }
        },
        error: function () {
            $(".createupdate_btn").prop("disabled", false).text(unique_id ? "Update" : "Save");
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
		searching   : false,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

function init_sub_datatable(table_id='',form_name='',action='') {

	var table = $("."+table_id);
	var data 	  = {
		"action"	: action, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable   = table.DataTable({
		"columnDefs": [
			{ 
				className: "td-text-center", "targets":  "_all"
				// className: "td-text-left", "targets":  1
		 	}
		],
		"searching": false,
        "paging":   false,
        "ordering": false,
        "info":     false,
		"serverSide": false,
    	"deferLoading": 0
	});
}

function user_permission_toggle(unique_id = "", new_status = 0) {
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


// All CheckBox Change Functions Start
$('#all').change(function(e) {

	if (e.currentTarget.checked) {

		$('.check_all').prop('checked', true);

  	} else {

	  	$('.check_all').prop('checked', false);
	}

});

$('.check_all').change(function(e) {

	var all_check = 1;

	$('.check_all').each(function() {

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
// All CheckBox Change Functions End

// Main Screen Change Functions
$('.main_all').change(function(e) {

	var unique_id = $("."+$(this).val()+"section");

	if (e.currentTarget.checked) {

		$(unique_id).prop('checked', true);

  	} else {
		
	  	$(unique_id).prop('checked', false);
	}

});

// Screen Section Change Functions
$('.main_all').change(function(e) {

	var unique_id = $("."+$(this).val()+"section");

	if (e.currentTarget.checked) {

		$(unique_id).prop('checked', true);

  	} else {
		
	  	$(unique_id).prop('checked', false);
	}

});

// Screen Section Change Functions
$('.section_all').change(function(e) {

	var unique_id = $("."+$(this).val()+"screen");

	if (e.currentTarget.checked) {

		$(unique_id).prop('checked', true);

  	} else {
		
	  	$(unique_id).prop('checked', false);
	}

});