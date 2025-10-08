
$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
	init_datatable(table_id,form_name,action);
});

var company_name = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone = sessionStorage.getItem("company_name");
var company_email = sessionStorage.getItem("company_name");
var company_logo = sessionStorage.getItem("company_name");

var form_name = 'Kra Kpa Form Creation ';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'kra_kpi_form_datatable';
var action = "datatable";

// function kra_kpi_form_cu(unique_id = "") {

//     var internet_status  = is_online();

//     if (!internet_status) {
//         sweetalert("no_internet");
//         return false;
//     }

//     var is_form = form_validity_check("was-validated");

//     if (is_form) {

//         var data 	 = $(".was-validated").serialize();
//         data 		+= "&unique_id="+unique_id+"&action=createupdate";

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url      = sessionStorage.getItem("list_link");

//         // console.log(data);
//         $.ajax({
// 			type 	: "POST",
// 			url 	: ajax_url,
// 			data 	: data,
// 			beforeSend 	: function() {
// 				$(".createupdate_btn").attr("disabled","disabled");
// 				$(".createupdate_btn").text("Loading...");
// 			},
// 			success		: function(data) {

// 				var obj     = JSON.parse(data);
// 				var msg     = obj.msg;
// 				var status  = obj.status;
// 				var error   = obj.error;

// 				if (!status) {
// 					url 	= '';
//                     $(".createupdate_btn").text("Error");
//                     console.log(error);
// 				} else {
// 					if (msg=="already") {
// 						// Button Change Attribute
// 						url 		= '';

// 						$(".createupdate_btn").removeAttr("disabled","disabled");
// 						if (unique_id) {
// 							$(".createupdate_btn").text("Update");
// 						} else {
// 							$(".createupdate_btn").text("Save");
// 						}
// 					}
// 				}

// 				sweetalert(msg,url);
// 			},
// 			error 		: function(data) {
// 				alert("Network Error");
// 			}
// 		});


//     } else {
//         sweetalert("form_alert");
//     }
// }
function init_datatable(table_id='',form_name='',action='') {
// alert('hi')
;	var table = $("#"+table_id);
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

function kra_kpi_form_delete(unique_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url = sessionStorage.getItem("list_link");

    confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {

                var data = {
                    "unique_id": unique_id,
                    "action": "delete"
                }

                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    success: function (data) {

                        var obj = JSON.parse(data);
                        var msg = obj.msg;
                        var status = obj.status;
                        var error = obj.error;

                        if (!status) {
                            url = '';

                        } else {
                            init_datatable(table_id, form_name, action);
                        }
                        sweetalert(msg, url);
                    }
                });

            } else {
                // alert("cancel");
            }
        });
}

function form_data() {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var staff_name = $("#staff_name").val();
    var test_doc = $("#test_file_exp").val();
    var test_docs = $("#test_file_imp").val();
    var unique_id = $('#unique_id').val();
    var is_form = form_validity_check("was-validated");

    if (is_form) {
        var data = $(".was-validated").serialize();
        var data = new FormData();
        var image_s = document.getElementById("test_file_exp");
        var image = document.getElementById("test_file_imp");
        if (image_s != '') {
            for (var i = 0; i < image_s.files.length; i++) {
                data.append("test_file[]", document.getElementById('test_file_exp').files[i]);
            }
        } else {

            data.append("test_doc", '');

        }

        if (image != '') {
            for (var i = 0; i < image.files.length; i++) {
                data.append("test_file1[]", document.getElementById('test_file_imp').files[i]);
            }
        } else {

            data.append("test_docs", '');

        }
        data.append("staff_name", staff_name);
        data.append("action", "createupdate");
        data.append("unique_id", unique_id);

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function () {
                $(".createupdate_btn").attr("disabled", "disabled");
                $(".createupdate_btn").text("Loading...");
            },
            success: function (data) {
                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    url = '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg == "already") {
                        // Button Change Attribute
                        url = '';

                        $(".createupdate_btn").removeAttr("disabled", "disabled");
                        if (unique_id) {
                            $(".createupdate_btn").text("Update");
                        } else {
                            $(".createupdate_btn").text("Save");
                        }
                    }
                }

                sweetalert(msg, url);
            },
            error: function (data) {
                alert("Network Error");
            }
        });
        // }
    } else {
        sweetalert("form_alert");
    }
}

function print(file_name)
{
	window.location='uploads/kra_kpi_form/' + file_name,'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no';
}

// function print(file_name) {
//     onmouseover = window.open('uploads/kra_kpi_form/' + file_name, 'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
// }
function print_view(file_name) {

    window.location = 'uploads/kra_kpi_form/' + file_name, 'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no';
}
