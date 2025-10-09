// $(document).ready(function () {
// 	// var table_id 	= "units_datatable";
// 	init_datatable(table_id,form_name,action);
// });


$(document).ready(function () {
    const table_id = 'expense_category_datatable';
    const form_name = 'Expense Category';
    const action = 'datatable';
    init_datatable(table_id, form_name, action);
});


var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Expense Category';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'expense_category_datatable';
var action 			= "datatable";

function expense_category_cu(unique_id = "") {

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

				sweetalert(msg,url,"category_name");
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("form_alert");
    }
}



function init_datatable(table_id = '', form_name = '', action = '') {
    const ajax_url = sessionStorage.getItem("folder_crud_link");

    $("#" + table_id).DataTable({
        ordering: true,
        searching: true,
        destroy: true, 
        ajax: {
            url: ajax_url,
            type: "POST",
            data: { action: action },
            dataType: "json",
            error: function (xhr, error, thrown) {
                console.error("DataTable Load Error:", xhr.responseText);
                Swal.fire("Error", "Failed to load data: " + error, "error");
            }
        },
        columns: [
            { title: "#" },
            { title: "Expense Category Name" },
            { title: "Description" },
            { title: "Active Status" },
            { title: "Action" }
        ]
    });
}




function category_toggle(unique_id = "", new_status = 0) {
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