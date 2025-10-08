$(document).ready(function () {
// 	var table_id 	= "new_task_datatable";
// 	init_datatable(table_id,form_name,action);
new_task_filter1();
//     get_project_name();
// 	get_plant_name();    //get plant name initially
// 	get_main_category();
	get_new_task();
// 	get_main_category();
    // trigger_complaint_category();
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Complaint Category';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'new_task_datatable';
var action 			= "datatable";


function new_external_window_print(event, url, unique_id) {

    var link = url + '?unique_id=' + unique_id;

    onmouseover = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    event.preventDefault();
}
function init_datatable(table_id='',form_name='',action='',filter_data='') { 
	var table = $("#"+table_id);
    var from_date           = $("#from_date").val();
    var to_date             = $("#to_date").val();
    var department_type     = $("#department_type").val();
    var complaint_name      = $("#complaint_name").val();
    var priority            = $("#priority").val();
    var site_name           = $('#site_name').val();
    var status_name         = $('#status_name').val();
	var data 	  = {
		"action"	            : action, 
        "from_date"             : from_date,
        "to_date"               : to_date,
        "department_type"       : department_type,
        "complaint_name"        : complaint_name,
        "priority"              : priority,
        "site_name"             : site_name,
        "status_name"           : status_name,
	};
// 	data          = {
//         ...data,
        
//     };
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


function new_task_cu(unique_id = "") {
	
    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) { 

        var person_name = $("#person_name").val();
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

 function get_site(state_name = "") {
   
        var state_name = $('#state_name').val();
        // var data 	 = $(".was-validated").serialize();
        var data = "&state_id=" + state_name + "&action=site_name_option";

        var ajax_url = sessionStorage.getItem("folder_crud_link");

        // if (state_id) {


        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {


                if (data) {
                    $("#site_name").html(data);
                }
            }
        });


    }
    

    function get_new_task(department_name = "",main_category="") {

        var department_name = $('#department_name').val();
        var main_category = $('#main_category').val();
        // var data 	 = $(".was-validated").serialize();
        var data = "&department_type=" + department_name + "&main_category=" + main_category + "&action=category_name_option";

        var ajax_url = sessionStorage.getItem("folder_crud_link");

        // if (state_id) {


        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {


                if (data) {
                    $("#new_task").html(data);
                }
            }
        });


        // }
    }
    
        

    function showPreview(event) {

        if (event.target.files.length > 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById("output_image");
            preview.src = src;
            //  document.getElementById('profile_image').value = preview.src;
        }
    }
    sub_list_datatable("document_upload_sub_datatable");
    function sub_list_datatable(table_id = "", form_name = "", action = "") {

        var unique_id = $("#unique_id").val();
        var screen_unique_id = $("#screen_unique_id").val();

        var table = $("#" + table_id);
        var data = {
            "unique_id": unique_id,
            "screen_unique_id": screen_unique_id,
            "action": table_id,
        };
        //  var ajax_url = "https://ascentrecyclers.com/pgp_admin/folders/new_complaints/crud.php";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var datatable = new DataTable(table, {
            destroy: true,
            "searching": false,
            "paging": false,
            "ordering": false,
            "info": false,
            "ajax": {
                url: ajax_url,
                type: "POST",
                data: data
            }

        });


    }
function document_upload_add_update(){
    var internet_status         = is_online();

	
	var document_name           = $("#document_name").val();
	var screen_unique_id        = $("#screen_unique_id").val();
	

    var file = $('#test_file').val();
	var output_image = $('#output_image').val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}
  
    
	if (document_name) {

		var data = new FormData();
		var image_s = document.getElementById("test_file");
		if (image_s != '') {
			for (var i = 0; i < image_s.files.length; i++) {
				data.append("test_file", document.getElementById('test_file').files[i]);

			}
		} else {
			data.append("test_file", '');
		}

		
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "document_upload_add_update");
		data.append("document_name", document_name);

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = '';

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			cache   : false,
			contentType: false,
			processData: false,
			method  : 'POST',
			beforeSend: function () {
				$(".sublist_save_btn").attr("disabled", "disabled");
				$(".sublist_save_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".sublist_save_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						form_reset("basictab4");
                        $("#document_name").val(null).trigger('change');
                        $("#test_file").val("");
						//showPreview(event);
                        
						//$(".upload_image").trigger("click");
					}
					$(".sublist_save_btn").removeAttr("disabled", "disabled");
					if (msg == "already") {
						$(".sublist_save_btn").text("Update");
					} else {
						$(".sublist_save_btn").text("Add");
						$(".sublist_save_btn").attr("onclick", "document_upload_add_update('')");
					}
					// Init Datatable
					sub_list_datatable("document_upload_sub_datatable");
				}
				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

		if (document_name == '') {
			document.getElementById('document_name').focus();
		}
		//else if(oem_map_justification==''){document.getElementById('oem_map_justification').focus();}
	}
}

function print(file_name)
    {
       onmouseover= window.open('uploads/new_task/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }  

function print_view(file_name)
    {
       onmouseover= window.open('uploads/new_task/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }  


    function document_upload_sub_delete(unique_id = "") {

        if (unique_id) {
    
            var ajax_url = sessionStorage.getItem("folder_crud_link");
            var url = sessionStorage.getItem("list_link");
    
            confirm_delete('delete')
                .then((result) => {
                    if (result.isConfirmed) {
    
                        var data = {
                            "unique_id": unique_id,
                            "action": "document_upload_sub_delete"
                        }
    
                        $.ajax({
                            type: "POST",
                            url: ajax_url,
                            data: data,
                            success: function (data) {
    
                                var obj     = JSON.parse(data);
                                var msg     = obj.msg;
                                var status  = obj.status;
                                var error   = obj.error;
    
                                if (!status) {
                                    url = '';
                                } else {
                                    sub_list_datatable("document_upload_sub_datatable");
                                }
                                sweetalert(msg, url);
                            }
                        });
    
                    } else {
                        // alert("cancel");
                    }
                });
        }
    }

function new_task_delete(unique_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    
    confirm_delete('delete')
    .then((result) => {
        if (result.isConfirmed) {

            var data = {
                "unique_id"     : unique_id,
                "action"        : "delete"
            }

            $.ajax({
                type    : "POST",
                url     : ajax_url,
                data    : data,
                success : function(data) {

                    var obj     = JSON.parse(data);
                    var msg     = obj.msg;
                    var status  = obj.status;
                    var error   = obj.error;

                    if (!status) {
                        url     = '';
                        
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

function new_task_filter(filter_action = 0 ) { 
    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    
        var from_date           = $("#from_date").val();
        var to_date             = $("#to_date").val();
        var department_type     = $("#department_type").val();
        var complaint_name      = $("#complaint_name").val();
        var priority            = $("#priority").val();
        var state_name           = $('#state_name').val();
        var site_name           = $('#site_name').val();
        var status_name         = $('#status_name').val();
        

       var is_vaild = fromToDateValidity(from_date, to_date);

        if (is_vaild) {

            sessionStorage.setItem("from_date",from_date);
            sessionStorage.setItem("to_date",to_date);
            sessionStorage.setItem("department_type",department_type);
            sessionStorage.setItem("complaint_name",complaint_name);
            sessionStorage.setItem("priority",priority);
            sessionStorage.setItem("state_name",state_name);
            sessionStorage.setItem("site_name",site_name);
            sessionStorage.setItem("status_name",status_name);
             
            var filter_data = {
                "from_date"             : from_date,
                "to_date"               : to_date,
                "department_type"       : department_type,
                "complaint_name"        : complaint_name,
                "priority"              : priority,
                "state_name"            : state_name,
                "site_name"             : site_name,
                "status_name"           : status_name,
               
            };

            console.log(filter_data);

            init_datatable(table_id,form_name,action,filter_data);

        }
    
}

function new_task_filter1() {
    var table_id = "new_task_datatable";
    
    init_datatable(table_id,form_name,action);
}


function category_entry_filter(department_name) {
     
    var department_name = $('#department_type').val();
        // var data      = $(".was-validated").serialize();
        var data = "&department_type=" + department_name + "&action=category_name_option_filter";

        var ajax_url = sessionStorage.getItem("folder_crud_link");

        // if (state_id) {


        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {


                if (data) {
                    $("#complaint_name").html(data);
                  
                }
            }
        });

}

 function focusOnComplaintName() {
    //  alert('Hi');
    //  $('.select2-search__field').focus();
    //     document.getElementById('complaint_name').focus();
    }

// function focusOnComplaintName() {
//     alert("hii");
//     document.getElementById('myinp').focus();
//     $("#myinp").focus(); // Set focus on #complaint_name
// }

function get_assign_staff(department_name){
    var department_name = $('#department_name').val();
    var site_name = $('#site_name').val();
    var data = "&department_type=" + department_name + "&site_name=" + site_name + "&action=assign_staff_name";
    var ajax_url = sessionStorage.getItem("folder_crud_link");

   $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        success: function (data) {
            var obj     = JSON.parse(data);
            var unique_id     = obj.data;

            if (data) {
                $("#assign_by").val(unique_id);
            }
        }
    });
}

function get_project_name(company_id = "") {
    if (company_id) {
        var data = {
            "company_id": company_id,
            "action": "project_name"
        };
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // alert(ajax_url);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#plant_name").html(data);
                }
            }
        });
    }
}

function get_main_category(department_id = "") {
    if (department_id) {
        var data = {
            "department_id": department_id,
            "action": "main_category"
        };
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // alert(ajax_url);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#main_category").html(data);
                }
            }
        });
    }
}

function trigger_complaint_category() {
    var department_id = document.getElementById("department_name").value;
    // alert(department_id);
    var main_category_id = document.getElementById("main_category").value;
    // alert(main_category_id);
    get_complaint_category(department_id, main_category_id);
}

function get_complaint_category(department_id = "", main_category_id = "") {
    var company_id = document.getElementById("site_name").value; // Ensure company_id is declared
    if (company_id) {
        var data = {
            "department_id": department_id,
            "main_category_id": main_category_id,
            "action": "complaint_category"
        };
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#complaint_category").html(data);
                }
            }
        });
    }
}

