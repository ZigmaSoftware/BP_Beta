$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Category Creation';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'category_creation_datatable';
var action 			= "datatable";

function new_external_window_print_1(event, url, unique_id,dept_name,main_category) {
    // alert(main_category);
    // var unique_id = $('#unique_id').val();
        event.preventDefault();

    var link = url + '?unique_id=' + unique_id+'&dept_name='+dept_name+'&main_category='+main_category;

    window.open(link, 'external_window', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}
function new_external_window_print(event, url, unique_id) {
    // var unique_id = $('#unique_id').val();
        event.preventDefault();

    var link = url + '?unique_id=' + unique_id;

    window.open(link, 'external_window', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}

// function new_external_window_print(event, url, category_unique_id, department_unique_id) {
//     event.preventDefault();

//     var link = url + '?category_id=' + category_unique_id + '&department_id=' + department_unique_id;

//     onmouseover = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
// }


function init_datatable(table_id='',form_name='',action='') {
	var table = $("#"+table_id);
	var department     = $("#department").val();
	var data 	  = {
		"action"	: action, 
		"department" : department,
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

function category_creation_cu(unique_id = "") {
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

function category_creation_delete(unique_id = "") {

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
        var department     = $("#department").val();


        // var is_vaild = fromToDateValidity(from_date,to_date);

        if (department)  {

            // sessionStorage.setItem("from_date",from_date);
            // sessionStorage.setItem("to_date",to_date);
            sessionStorage.setItem("department",department);
            
            var filter_data = {
                // "from_date"     : from_date,
                // "to_date"       : to_date,
                "department"    : department,
            };

            console.log(filter_data);

            init_datatable(table_id,form_name,action,filter_data);

        }

    
}

function get_category_name(department = "") {
	
    var department = document.getElementById('department').value;
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (department) {
        var data = {
            "department": department,
            "action": "get_category_name"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#main_category_name").html(data);
                }
            }
        });
    }
}

