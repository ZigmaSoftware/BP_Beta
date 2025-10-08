var company_name    = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone   = sessionStorage.getItem("company_name");
var company_email   = sessionStorage.getItem("company_name");
var company_logo    = sessionStorage.getItem("company_name");

var form_name       = 'periodic_creation';
var form_header     = '';
var form_footer     = '';
var table_name      = '';
var table_id        = 'periodic_creation_datatable';
var action          = "datatable";
 
$(document).ready(function () {

	datatable_init_based_on_prev_state();
	get_username();
	//get_designation();
	sub_list_datatable("periodic_sub_datatable", form_name, "periodic_sub_datatable");

});


function datatable_init_based_on_prev_state() {
	// Data Table Filter Function Based ON Previous Search
	var from_date       = sessionStorage.getItem("periodic_from_date");
	var to_date         = sessionStorage.getItem("periodic_to_date");
	var executive_name  = sessionStorage.getItem("executive_name");
	var filter_action   = sessionStorage.getItem("periodic_action");

	if (!from_date) {
		from_date = $("#periodic_from").val();
	} else {
		$("#periodic_from").val(from_date);
	}

	if (!to_date) {
		to_date = $("#periodic_to").val();
	} else {
		$("#periodic_to").val(to_date);
	}
	if (!executive_name) {
		executive_name = $("#executive_name").val();
	} else {
		$("#executive_name").val(executive_name);
	}

	if (!filter_action) {
		filter_action = 0;
	}

	// Datatable Filter Data
	var filter_data = {
		"from_date"     : from_date,
		"to_date"       : to_date,
		"executive_name": executive_name,
		"filter_action" : filter_action
	};

	// var table_id 	= "follow_up_call_datatable";
	init_datatable(table_id, form_name, action, filter_data);
}

// Create and Update Values

function init_datatable(table_id = '', form_name = '', action = '', filter_data = '') {

	var table = $("#" + table_id);

	var data = {
		"action": action,
	};
	data = {
		...data,
		...filter_data
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
	    ordering    : true,
	    searching   : true,
		"searching": false,
		"columnDefs": [

			{
				className: "text-center",
				"width": "5%",
				"targets": [0, -1]
			},
		],
		"ajax": {
			url: ajax_url,
			type: "POST",
			data: data
		}
	});
}

function new_external_window_print(event, url, unique_id) {

    var link = url + '?unique_id=' + unique_id;

    onmouseover = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    event.preventDefault();
}

function periodic_creation_delete(unique_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");

	confirm_delete('delete')
		.then((result) => {
			if (result.isConfirmed) {

				var data = {
					"unique_id" : unique_id,
					"action"    : "delete"
				};

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
							datatable_init_based_on_prev_state();
						}
						sweetalert(msg, url);
					}
				});

			} else {
				// alert("cancel");
			}
		});
}



function get_designation() {

	var department_name     =   $('#department_name').val();
    var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	
    var data = {
		"department_name" 	: department_name,
		"action"			: "designation"
	}

	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: data,
		success : function(data) 
		{
		    var obj     = JSON.parse(data);
			var data    = obj.data;
			if(data){
				$('#designation_name').html(data);
			}else{
				$('#designation_name').html('');
			}
			
		}
	});
}

function get_category() {

    var department_name = $('#department_name').val();
    
    
       
    var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	
    var data = {
		"department_name" 	: department_name,
		"action"			: "category"
	}

	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: data,
		success : function(data) 
		{
		  //  alert(data);
		    var obj     = JSON.parse(data);
			var data    = obj.data;
			if(data){
				 
				$('#complaint_category').html(data);
			}else{
				$('#complaint_category').html('');
			}
			
		}
	});
}



function get_department(){
    var user_name = $('#user_name_select').val();
    
       
    var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	
    var data = {
		"user_name" 	: user_name,
		"action"			: "department"
	}

	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: data,
		success : function(data) 
		{
		  //  alert(data);
		    var obj     = JSON.parse(data);
			var data    = obj.data;
			if(data){
				 
				$('#department_name').html(data);
			}else{
				$('#department_name').html('');
			}
			
		}
	});
}


function sub_list_datatable(table_id = "", form_name = "", action = "") {

	var screen_unique_id = $("#screen_unique_id").val();
	var unique_id = $('#unique_id').val();

	var table = $("#" + table_id);
	var data = {
		"screen_unique_id": screen_unique_id,
		"unique_id": unique_id,
		"action": table_id,
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
	    ordering    : true,
	    searching   : true,
		"searching" : false,
		"paging"    : false,
		// "ordering"  : false,
		"info"      : false,
		"ajax"      : {
			url         : ajax_url,
			type        : "POST",
			data        : data
		}
	});

    

	datatable.on('xhr', function (e, settings, json) {

        
        if (table_id == "periodic_sub_datatable") {

           
            var count = json['count'];

            $("#periodic_table_count").val(count);
         
        }


	});


	return datatable;
}

function periodic_creation_cu(unique_id = "") { 

	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

    var table_count =  $("#periodic_table_count").val();

	if (table_count == "0") {
		sweetalert("custom", "", "", "Mimimun one Entry Needed");
		return false;
	}

	var is_form = form_validity_check("was-validated","periodic_creation_form_main");
	//var is_form2 = form_validity_check("was-validated","periodic_creation_form_bottom");

	if (is_form){

		var data = $(".was-validated").serialize();
		data += "&unique_id=" + unique_id + "&action=createupdate";

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url      = sessionStorage.getItem("list_link");

		// console.log(data);
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			/* cache       : false,
			 contentType : false,
			 processData : false,
			 method      : 'POST',*/
			beforeSend: function () {
				$(".createupdate_btn").attr("disabled", "disabled");
				$(".createupdate_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;
				var exp_id = obj.exp_unique_id;

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

	} else {
		sweetalert("form_alert");
	}
}

function periodic_add_update(unique_id = "") { // au = add,update

	var internet_status         = is_online();

	var level_no  			    = $("#level_no").val();
	var starting_count          = $("#starting_count").val();
	var ending_count            = $("#ending_count").val();
	var dept_name               = $("#department_name").val();
	var complaint_cat           = $("#complaint_category").val();
	if(complaint_cat == ''){
		var complaint_category = "All";
	}else{
		var complaint_category = complaint_cat;
	}
	if(dept_name == ''){
		var department_name = "All";
	}else{
		var department_name = dept_name;
	}
	var user_name               = $("#user_name").val();
	var site_id                 = $("#site_name").val();
	if(site_id == ''){
		var site_name = "All";
	}else{
		var site_name = site_id;
	}
	var screen_unique_id        = $("#screen_unique_id").val();
	
	

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}


    
	if ((complaint_category)&&(level_no)&&(starting_count)&&(ending_count)&&(department_name)) {alert(department_name);

		if(parseInt(starting_count) < parseInt(ending_count))
		
		{
		var data = new FormData();
		
		data.append("level_no", level_no);
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "periodic_add_update");
		data.append("unique_id", unique_id);
		data.append("starting_count", starting_count);
		data.append("ending_count", ending_count);
		data.append("department_name", department_name);
		data.append("complaint_category", complaint_category);
		data.append("user_name", user_name);
		data.append("site_name", site_name);
		

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
				$(".periodic_sub_add_update_btn").attr("disabled", "disabled");
				$(".periodic_sub_add_update_btn").text("Loading...");
			},
			success: function (data) { 

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".periodic_sub_add_update_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						form_reset("periodic_sub_form");
						$("#level_no").val("");
						$("#starting_count").val("");
						$("#ending_count").val("");
						$("#complaint_category").val("");
					}
					$(".periodic_sub_add_update_btn").removeAttr("disabled", "disabled");
					if (unique_id && msg == "already") {
						$(".periodic_sub_add_update_btn").text("Update");
					} else {
						$(".periodic_sub_add_update_btn").text("Add");
						$(".periodic_sub_add_update_btn").attr("onclick", "periodic_sub_add_update('')");
					}
					// Init Datatable
					sub_list_datatable("periodic_sub_datatable");
				}
				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});

	}else{
		sweetalert("custom", '', '', 'Ending days should be greater!!!');
	}
	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

	     if (starting_count == '') {
			document.getElementById('starting_count').focus();
		}else if (ending_count == '') {
			document.getElementById('ending_count').focus();
		}
		
	}
}

function get_site_ids(){
    var branch= $('#site').val();
    
    $('#site_name').val(branch);
}

function periodic_sub_delete(unique_id = "") {

	if (unique_id) {

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url = sessionStorage.getItem("list_link");

		confirm_delete('delete')
			.then((result) => {
				if (result.isConfirmed) {

					var data = {
						"unique_id": unique_id,
						"action": "periodic_sub_delete"
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
								sub_list_datatable("periodic_sub_datatable");
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


function get_previous_days_count(){ 
	var screen_unique_id  = $('#screen_unique_id').val();
	var starting_count  = $('#starting_count').val();
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	
    var data = {
		"starting_count" 	: starting_count,
		"screen_unique_id"  : screen_unique_id,
 		"action"			: "previous_day_count"
	}

	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: data,
		success : function(data) 
		{
		    var obj     = JSON.parse(data);
			var data    = obj.data;
			var last_ending_count = data.ending_count;
			if(starting_count > last_ending_count){
				$(".start_count_class").removeClass("d-none");
			}else{
				$(".start_count_class").addClass("d-none");
			}

			
		}
	});
}

function get_username() {
get_dept_name();

	var user_name     =   $('#user_name_select').val();
	 // alert(user_name);
    var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	if(user_name){
    var data = {
		"user_name" 	: user_name,
		"action"		: "get_usertype"
	}

	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: data,
		success : function(data) 
		{
		    var obj     = JSON.parse(data);
			var data    = obj.data;
			var user_type = obj.user_type;
			var mobile_no   = obj.mobile_no;
            var designation = obj.designation;

			$('#user_type').html(user_type);
            $('#mobile_no').html(mobile_no);
            $('#user_name').val(user_name);
			$('#designation').html(designation);
		}
	});
}
}

function get_dept_name() {

	var user_name     =   $('#user_name_select').val();
 
    var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	if(user_name){
    var data = {
		"user_name" 	: user_name,
		"action"		: "get_dept_name"
	}

	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: data,
		success : function(data) 
		{
		   var obj     = JSON.parse(data);
			var data    = obj.data;
 			var site_data = obj.site_data;
			
			if(data){
				 
				$('#department_name').html(data);
			}else{
				$('#department_name').html('');
			}
			
			if(site_data){
				 
				$('#site').html(site_data);
			}else{
				$('#site').html('');
			}
            
		}
	});
}
}
function get_department_name(user_id = "") {
    if (user_id) {
        var data = {
            "user_id": user_id,
            "action": "department_name"
        };
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // alert(ajax_url);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#department_name").html(data);
                }
            }
        });
    }
}

function get_dept_category(staff_id = "") {
    if (staff_id) {
        var data = {
            "staff_id": staff_id,
            "action": "get_dept_category"
        };
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // alert(ajax_url);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    // $("#plant_name").html(data);
                    console.log(data);
                }
            }
        });
    }
}
