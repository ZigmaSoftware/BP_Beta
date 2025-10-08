$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Main Category Creation';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'main_category_creation_datatable';
var action 			= "datatable";

function init_datatable(table_id='',form_name='',action='') {
	var table = $("#"+table_id);
	var department_type     = $("#department_type").val();
	var data 	  = {
		"action"	: action, 
		"department_type" : department_type,
	};
	data          = {
        ...data,
        
    };
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
	    
	    ordering    : true,
		searching   : true,
        "searching" : true,
	
	"ajax"		: {
		url 	: ajax_url,
		type 	: "POST",
		data 	: data
	},
		dom: 'Blfrtip',
		buttons: [
			'copy', 'csv', 'excel', 'pdf', 'print'
		]
	});
}

function main_category_cu(unique_id = "") {
    alert();
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

function main_category_delete(unique_id = "") {

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

function department_entry_filter(filter_action = 0 ) {
    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    
        // var from_date      = $("#from_date").val();
        // var to_date        = $("#to_date").val();
        var department_type     = $("#department_type").val();


        // var is_vaild = fromToDateValidity(from_date,to_date);

        if (department_type)  {

            // sessionStorage.setItem("from_date",from_date);
            // sessionStorage.setItem("to_date",to_date);
            sessionStorage.setItem("department_type",department_type);
            
            var filter_data = {
                // "from_date"     : from_date,
                // "to_date"       : to_date,
                "department_type"    : department_type,
            };

            console.log(filter_data);

            init_datatable(table_id,form_name,action,filter_data);

        }

    
}

