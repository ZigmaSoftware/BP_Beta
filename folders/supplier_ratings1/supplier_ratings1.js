$(document).ready(function () {
	// var table_id 	= "enquiry_type_datatable";
	init_datatable(table_id,form_name,action);
});

$(document).ready(function () {
    supplier_ratings_filter();
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");


// Global variables
var form_name   = 'Supplier Ratings';
var form_header = '';
var form_footer = '';
var table_name  = '';
var table_id    = 'supplier_ratings1_datatable';
var action      = "datatable";


// filter 
function supplier_ratings_filter() {
    init_datatable(table_id, form_name, action);
}



function supplier_ratings1_cu(unique_id = "") {
// alert('hiii');
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


function init_datatable(table_id = '', form_name = '', action = '') {

    var table = $("#" + table_id);
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    
    var datatable = table.DataTable({
        ordering: true,
        searching: true,
        destroy: true, // important for reload
        dom: 'Bfrtip', // add this
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Export CSV',
                title: 'Supplier Report'
            },
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                title: 'Supplier Report'
            }
        ],
        ajax: {
            url: ajax_url,
            type: "POST",
            data: function (d) {
                d.action       = action;
                d.from_period  = $("#from_period").val();
                d.to_period    = $("#to_period").val();
                d.supplier_id  = $("#supplier_name").val();   
                d.status_fill  = $("#status_fill").val();    
            }
        }
    });

}




function supplier_ratings1_toggle(unique_id = "", new_status = 0) {
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
            if (obj.status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }
            sweetalert(obj.msg, url);
        }
    });
}



