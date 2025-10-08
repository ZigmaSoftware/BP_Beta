
$(document).ready(function() {
	// Datatable Initialize
	//init_datatable(table_id,form_name,action);
	datatable_init_based_on_prev_state ();
	get_designation();
	sub_list_datatable("exp_food_daily_expense_sub_datatable", form_name, "exp_food_daily_expense_sub_datatable");
	sub_list_datatable_hotel("exp_hotel_expense_sub_datatable", form_name, "exp_hotel_expense_sub_datatable");
	sub_list_datatable_travel("exp_travel_expense_sub_datatable", form_name, "exp_travel_expense_sub_datatable");
	sub_list_datatable_petrol("exp_petrol_expense_sub_datatable", form_name, "exp_petrol_expense_sub_datatable");

	// sub_list_datatable(sub_tableid,form_name,sub_action);
	display_c7();
	
});


var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'expense_hr_approval';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'expense_hr_approval_datatable';
// var sub_tableid     = 'expense_hr_approval_sub_datatable';
// var sub_action      = 'expense_hr_approval_sub_datatable';
var action 			= "datatable";


// Create and Update Values
function expense_hr_approval_cu(unique_id = "") {
	
	var internet_status  = is_online();
	
    if (!internet_status) {
		sweetalert("no_internet");
        return false;
    }
    
  var is_form = form_validity_check("was-validated");
	
	//alert(is_form);
    if (is_form) {

    	 // return false;
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

function init_datatable(table_id='',form_name='',action='',filter_data='') {
    var table = $("#"+table_id);

	var data 	  = {
		"action"	: action, 
	};
	data 		  = {
		...data,
		...filter_data
	};	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
	
        "searching" : false,
		"columnDefs": [
        
            { className:  "text-center", "width" : "5%","targets": [ 0,-1 ] },
        ],
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

// function sub_list_datatable (table_id = "", form_name = "", action = "") {
     
//     var unique_id = $("#unique_id").val();
    
//     var table = $("#"+table_id);
// 	var data 	  = {
//         "unique_id"    : unique_id,
// 		"action"	    : action, 
// 	};
// 	var ajax_url = sessionStorage.getItem("folder_crud_link");

// 	var datatable = table.DataTable({
//         "searching": false,
//         "paging":   false,
//         "ordering": false,
//         "info":     false,
// 		"ajax"		: {
// 			url 	: ajax_url,
// 			type 	: "POST",
// 			data 	: data
// 		}
// 	});
// }

// function expense_sub_list_datatable (table_id = "", form_name = "", action = "",staff_id = "",entry_date) {
     
//     //var staff_id = $("#staff_name").val();
    
//     var table = $("#"+table_id);
// 	var data 	  = {
//         "staff_id"    : staff_id,
//         "entry_date"  : entry_date,
// 		"action"	  : action, 
// 	};
// 	var ajax_url = sessionStorage.getItem("folder_crud_link");

// 	var datatable = table.DataTable({
//         "searching": false,
//         "paging":   false,
//         "ordering": false,
//         "info":     false,
// 		"ajax"		: {
// 			url 	: ajax_url,
// 			type 	: "POST",
// 			data 	: data
// 		}
// 	});
// }
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

    // console.log(datatable);
    // console.log(datatable.data());

    // alert(datatable.data().count());

	datatable.on('xhr', function (e, settings, json) {

        
        if (table_id == "exp_food_daily_expense_sub_datatable") {

            var total = json['total_amt'];
            var count = json['count'];

            $("#expense_table_count").val(count);
            $("#total_amt").html(total);
        }


	});


	return datatable;
}

function sub_list_datatable_hotel(table_id = "", form_name = "", action = "") {

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

        
        if (table_id == "exp_hotel_expense_sub_datatable") {

            var total = json['total_amt_hotel'];
            var count = json['count'];

            $("#expense_table_count_hotel").val(count);
            $("#total_amt_hotel").html(total);
        }


	});


	return datatable;
}

function sub_list_datatable_petrol(table_id = "", form_name = "", action = "") {

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

        
        if (table_id == "exp_petrol_expense_sub_datatable") {

            var total = json['total_amt_petrol'];
            var count = json['count'];

            $("#expense_table_count_petrol").val(count);
            $("#total_amt_petrol").html(total);
        }


	});


	return datatable;
}

function sub_list_datatable_travel(table_id = "", form_name = "", action = "") {

	var screen_unique_id = $("#screen_unique_id").val();

	var unique_id = $('#unique_id').val();
// alert(unique_id);
	var table = $("#" + table_id);
	var data = {
		"screen_unique_id": screen_unique_id,
		"unique_id": unique_id,
		"action": table_id,
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
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

        
        if (table_id == "exp_travel_expense_sub_datatable") {

            var total = json['total_amt_travel'];
            var count = json['count'];

            $("#expense_table_count_travel").val(count);
            $("#total_amt_travel").html(total);
        }


	});


	return datatable;
}


function datatable_init_based_on_prev_state () {
	// Data Table Filter Function Based ON Previous Search
	    var from_date       = $("#expense_from").val();
		var to_date         = $("#expense_to").val();
		var pending_status  = $("#pending_status").val();
		var executive_name  = $("#executive_name").val();
		var filter_action   = sessionStorage.getItem("expense_action");

		if (!filter_action) {
			filter_action = 0;
		}

	// Datatable Filter Data
	var filter_data = {
		"from_date" 	: from_date,
		"to_date" 		: to_date,
		"pending_status": pending_status,
		"executive_name": executive_name,
		"filter_action" : filter_action
	};

	// var table_id 	= "follow_up_call_datatable";
	init_datatable(table_id,form_name,action,filter_data);
}

function expensehrFilter(filter_action = 0 ) {
	var internet_status  = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;	
	}

	var is_form = form_validity_check("was-validated");

	if (is_form) {
		var from_date = $("#expense_from").val();
		var to_date   = $("#expense_to").val();
		var pending_status  = $("#pending_status").val();
		var executive_name  = $("#executive_name").val();


		var is_vaild = fromToDateValidity(from_date,to_date);

		if (is_vaild) {

			// sessionStorage.setItem("expense_from_date",from_date);
			// sessionStorage.setItem("expense_to_date",to_date);
			// sessionStorage.setItem("expense_action",filter_action);
			// sessionStorage.setItem("pending_status",pending_status);
			// sessionStorage.setItem("executive_name",executive_name);
			// Delete Below Line After Testing Complete
			sessionStorage.setItem("follow_up_call_action",0);

			var filter_data = {
				"from_date" 	: from_date,
				"to_date" 		: to_date,
				"pending_status": pending_status,
				"executive_name": executive_name,
				"filter_action" : filter_action
			};

			console.log(filter_data);

			init_datatable(table_id,form_name,action,filter_data);

		}

	} else {
	sweetalert("form_alert","");
	}
}


function get_designation() {

	var staff_name = $('#branch_staff_name').val();
	var name_type = $('.branch_staff_name_type').val();
	var grade_type = $('.grade_type').val();
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if ((name_type == 0) && (staff_name)) {
		var data = {
			"name_type": name_type,
			"staff_name": staff_name,
			"action": "designation"
		};

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {
				var obj = JSON.parse(data);
				var data = obj.data;
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;


				var designation_unique_id = data.designation_unique_id;
				var designation_name = data.designation_name;
				var grade_name = data.grade_name;
				var grade = data.grade;

				document.getElementById('designation_unique_id').value = designation_unique_id;
				document.getElementById('designation_name').innerHTML = designation_name;

				document.getElementById('grade_type').value = grade;
				document.getElementById('grade_name').innerHTML = grade_name;


			}
		});
	} else {
		document.getElementById('designation_name').innerHTML = '';
		document.getElementById('designation_unique_id').value = '';
		document.getElementById('grade_name').innerHTML = '';
		document.getElementById('grade_type').value = '';

	}
}



function expense_creation_cu(unique_id = "") {

	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

    var table_count =  $("#expense_table_count").val();

	if (table_count == "0") {
		sweetalert("custom", "", "", "Mimimun one Entry Needed");
		return false;
	}

	var is_form1 = form_validity_check("was-validated","expense_creation_form_main");
	var is_form2 = form_validity_check("was-validated","expense_creation_form_bottom");

	if ((is_form1) && (is_form2)) {

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

function exp_food_daily_expense_sub_add_update(unique_id = "") { // au = add,update

	var internet_status         = is_online();

	// var branch_staff_name_type  = $("#branch_staff_name_type").val();
	// var entry_date              = $("#entry_date").val();
	var expense_type            = $("#expense_type").val();
	var amount                  = $("#amount").val();
	// var customer_name           = $("#customer_name").val();
	// var call_no                 = $("#call_no").val();
	// var limit_value             = $("#limit_value").val();
	var sub_description         = $("#sub_description").val();
	var screen_unique_id        = $("#screen_unique_id").val();
	// var customer_hidden  		= $("#customer_hidden").val();

	var file = $('#test_file').val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	if (parseFloat(limit_value) < parseFloat(amount) && (limit_value)) {
		sweetalert("custom", '', '', 'Amount is greater than Limit Value');
		return false;
	}
    
    // if ($("#expense_call_status").prop('checked')) {
        
    //     var cust_name  = ((expense_type != '') && (amount != '') && (sub_description != '') && (branch_staff_name_type != '')&&(customer_name != '')&&(call_no != ''));

    // } else {
    //     var cust_name  = ((expense_type != '') && (amount != '') && (sub_description != '') && (branch_staff_name_type != ''));
    // }
    
	if (cust_name) {

		var data = new FormData();
		var image_s = document.getElementById("test_file");
		if (image_s != '') {
			for (var i = 0; i < image_s.files.length; i++) {
				data.append("test_file", document.getElementById('test_file').files[i]);

			}
		} else {
			data.append("test_file", '');
		}

		data.append("entry_date", entry_date);
		data.append("file", file);
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "exp_food_daily_expense_sub_add_update");
		data.append("unique_id", unique_id);
		data.append("expense_type", expense_type);
		data.append("amount", amount);
		// data.append("customer_name", customer_name);
		// data.append("call_no", call_no);
		// data.append("sub_description", sub_description);

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
				$(".exp_food_daily_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_food_daily_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_food_daily_expense_sub_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						form_reset("expense_sub_form");
						$("#expense_type").val("");
						$("#amount").val("");
						// $("#sub_description").val("");
						$(".dropify-clear").trigger("click");
					}
					$(".exp_food_daily_expense_sub_btn").removeAttr("disabled", "disabled");
					if (unique_id && msg == "already") {
						$(".exp_food_daily_expense_sub_btn").text("Update");
					} else {
						$(".exp_food_daily_expense_sub_btn").text("Add");
						$(".exp_food_daily_expense_sub_btn").attr("onclick", "exp_food_daily_expense_sub('')");
					}
					// Init Datatable
					sub_list_datatable("exp_food_daily_expense_sub_datatable");
				}
				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

		if (expense_type == '') {
			document.getElementById('expense_type').focus();
		} else if (amount == '') {
			document.getElementById('amount').focus();
		} else if (sub_description == '') {
			document.getElementById('sub_description').focus();
		}
		//else if(oem_map_justification==''){document.getElementById('oem_map_justification').focus();}
	}
}

function exp_hotel_expense_sub_add_update(unique_id = "") { // au = add,update

	var internet_status         = is_online();

	var branch_staff_name_type  = $("#branch_staff_name_type").val();
	var entry_date              = $("#entry_date").val();
	var expense_type_hotel      = $("#expense_type_hotel").val();
	var amount_hotel            = $("#amount_hotel").val();
	// var customer_name           = $("#customer_name").val();
	// var call_no                 = $("#call_no").val();
	// var limit_value             = $("#limit_value").val();
	// var sub_description_hotel   = $("#sub_description_hotel").val();
	// var expense_type_hotel      = $("#expense_type_hotel").val();
	var screen_unique_id        = $("#screen_unique_id").val();
	// var customer_hidden  		= $("#customer_hidden").val();

	var test_file_hotel = $('#test_file_hotel').val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	if (parseFloat(limit_value) < parseFloat(amount_hotel) && (limit_value)) {
		sweetalert("custom", '', '', 'amount_hotel is greater than Limit Value');
		return false;
	}
	else if(limit_value==''){
		return false;
	}
    
    if ($("#expense_call_status").prop('checked')) {
        
        var cust_name  = ((expense_type_hotel != '') && (amount_hotel != '') && (sub_description_hotel != '') && (branch_staff_name_type != '')&&(customer_name != '')&&(call_no != ''));

    } else {
        var cust_name  = ((expense_type_hotel != '') && (amount_hotel != '') && (sub_description_hotel != '') && (branch_staff_name_type != ''));
    }
    
	if (cust_name) {

		var data = new FormData();
		var image_s = document.getElementById("test_file_hotel");
		if (image_s != '') {
			for (var i = 0; i < image_s.files.length; i++) {
				data.append("test_file_hotel", document.getElementById('test_file_hotel').files[i]);

			}
		} else {
			data.append("test_file_hotel", '');
		}

		data.append("entry_date", entry_date);
		data.append("test_file_hotel", test_file_hotel);
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "exp_hotel_expense_sub_add_update");
		data.append("unique_id", unique_id);
		data.append("expense_type_hotel", expense_type_hotel);
		data.append("amount_hotel", amount_hotel);
		// data.append("customer_name", customer_name);
		// data.append("call_no", call_no);
		data.append("sub_description_hotel", sub_description_hotel);

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
				$(".exp_hotel_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_hotel_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_hotel_expense_sub_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						form_reset("expense_sub_form");
						$("#expense_type_hotel").val("");
						$("#amount_hotel").val("");
						$("#sub_description_hotel").val("");
						$(".dropify-clear").trigger("click");
					}
					$(".exp_hotel_expense_sub_btn").removeAttr("disabled", "disabled");
					if (unique_id && msg == "already") {
						$(".exp_hotel_expense_sub_btn").text("Update");
					} else {
						$(".exp_hotel_expense_sub_btn").text("Add");
						$(".exp_hotel_expense_sub_btn").attr("onclick", "exp_hotel_expense_sub('')");
					}
					// Init Datatable
					sub_list_datatable_hotel("exp_hotel_expense_sub_datatable");
				}
				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

		if (expense_type_hotel == '') {
			document.getElementById('expense_type_hotel').focus();
		} else if (amount_hotel == '') {
			document.getElementById('amount_hotel').focus();
		} else if (sub_description_hotel == '') {
			document.getElementById('sub_description_hotel').focus();
		}
		//else if(oem_map_justification==''){document.getElementById('oem_map_justification').focus();}
	}
}

function exp_petrol_expense_sub_add_update(unique_id = "") { // au = add,update

	var internet_status         = is_online();

	var branch_staff_name_type  = $("#branch_staff_name_type").val();
	var entry_date              = $("#entry_date").val();
	var expense_type_petrol      = $("#expense_type_petrol").val();
	var amount_petrol            = $("#amount_petrol").val();
	var customer_name           = $("#customer_name").val();
	var call_no                 = $("#call_no").val();
	var limit_value             = $("#limit_value").val();
	var sub_description_petrol   = $("#sub_description_petrol").val();
	var screen_unique_id        = $("#screen_unique_id").val();
	var travel_type           = $("#travel_type").val();
	var vehicle_type           = $("#vehicle_type").val();
	var fuel_type           = $("#fuel_type").val();
	var rate           = $("#rate").val();
	var kg_meter           = $("#kg_meter").val();
	var customer_hidden  		= $("#customer_hidden").val();

	var test_file_petrol = $('#test_file_petrol').val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	if (parseFloat(limit_value) < parseFloat(amount_petrol) && (limit_value)) {
		sweetalert("custom", '', '', 'amount_petrol is greater than Limit Value');
		return false;
	}
    
    if ($("#expense_call_status").prop('checked')) {
        
        var cust_name  = ((expense_type_petrol != '') && (amount_petrol != '') && (sub_description_petrol != '') && (branch_staff_name_type != '')&&(customer_name != '')&&(call_no != ''));

    } else {
        var cust_name  = ((expense_type_petrol != '') && (amount_petrol != '') && (sub_description_petrol != '') && (branch_staff_name_type != ''));
    }
    
	if (cust_name) {

		var data = new FormData();
		var image_s = document.getElementById("test_file_petrol");
		if (image_s != '') {
			for (var i = 0; i < image_s.files.length; i++) {
				data.append("test_file_petrol", document.getElementById('test_file_petrol').files[i]);

			}
		} else {
			data.append("test_file_petrol", '');
		}

		data.append("entry_date", entry_date);
		data.append("test_file_petrol", test_file_petrol);
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "exp_petrol_expense_sub_add_update");
		data.append("unique_id", unique_id);
		data.append("expense_type_petrol", expense_type_petrol);
		data.append("amount_petrol", amount_petrol);
		data.append("customer_name", customer_name);
		data.append("call_no", call_no);
		data.append("travel_type", travel_type);
		data.append("vehicle_type", vehicle_type);
		data.append("fuel_type", fuel_type);
		data.append("rate", rate);
		data.append("kg_meter", kg_meter);
		data.append("sub_description_petrol", sub_description_petrol);

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
				$(".exp_petrol_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_petrol_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_petrol_expense_sub_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						form_reset("expense_sub_form");
						$("#expense_type_petrol").val("");
						$("#amount_petrol").val("");
						$("#sub_description_petrol").val("");
						$("#travel_type").val("");
						$("#vehicle_type").val("");
						$("#fuel_type").val("");
						$("#rate").val("");
						$("#kg_meter").val("");
						$(".dropify-clear").trigger("click");
					}
					$(".exp_petrol_expense_sub_btn").removeAttr("disabled", "disabled");
					if (unique_id && msg == "already") {
						$(".exp_petrol_expense_sub_btn").text("Update");
					} else {
						$(".exp_petrol_expense_sub_btn").text("Add");
						$(".exp_petrol_expense_sub_btn").attr("onclick", "exp_petrol_expense_sub('')");
					}
					// Init Datatable
					sub_list_datatable_petrol("exp_petrol_expense_sub_datatable");
				}
				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

		if (expense_type_petrol == '') {
			document.getElementById('expense_type_petrol').focus();
		} else if (amount_petrol == '') {
			document.getElementById('amount_petrol').focus();
		} else if (sub_description_petrol == '') {
			document.getElementById('sub_description_petrol').focus();
		} else if (travel_type == '') {
			document.getElementById('travel_type').focus();
		} else if (vehicle_type == '') {
			document.getElementById('vehicle_type').focus();
		} else if (fuel_type == '') {
			document.getElementById('fuel_type').focus();
		} else if (rate == '') {
			document.getElementById('rate').focus();
		} else if (kg_meter == '') {
			document.getElementById('kg_meter').focus();
		}
		//else if(oem_map_justification==''){document.getElementById('oem_map_justification').focus();}
	}
}

function exp_travel_expense_sub_add_update(unique_id = "") { // au = add,update

	var internet_status         = is_online();

	var branch_staff_name_type  = $("#branch_staff_name_type").val();
	var entry_date              = $("#entry_date").val();
	var expense_type_travel      = $("#expense_type_travel").val();
	var amount_travel            = $("#amount_travel").val();
	var customer_name           = $("#customer_name").val();
	var call_no                 = $("#call_no").val();
	var limit_value             = $("#limit_value").val();
	var sub_description_travel   = $("#sub_description_travel").val();
	var screen_unique_id        = $("#screen_unique_id").val();
	var customer_hidden  		= $("#customer_hidden").val();

	var test_file_travel = $('#test_file_travel').val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	if (parseFloat(limit_value) < parseFloat(amount_travel) && (limit_value)) {
		sweetalert("custom", '', '', 'amount_travel is greater than Limit Value');
		return false;
	}
    
    if ($("#expense_call_status").prop('checked')) {
        
        var cust_name  = ((expense_type_travel != '') && (amount_travel != '') && (sub_description_travel != '') && (branch_staff_name_type != '')&&(customer_name != '')&&(call_no != ''));

    } else {
        var cust_name  = ((expense_type_travel != '') && (amount_travel != '') && (sub_description_travel != '') && (branch_staff_name_type != ''));
    }
    
	if (cust_name) {

		var data = new FormData();
		var image_s = document.getElementById("test_file_travel");
		if (image_s != '') {
			for (var i = 0; i < image_s.files.length; i++) {
				data.append("test_file_travel", document.getElementById('test_file_travel').files[i]);

			}
		} else {
			data.append("test_file_travel", '');
		}

		data.append("entry_date", entry_date);
		data.append("test_file_travel", test_file_travel);
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "exp_travel_expense_sub_add_update");
		data.append("unique_id", unique_id);
		data.append("expense_type_travel", expense_type_travel);
		data.append("amount_travel", amount_travel);
		data.append("customer_name", customer_name);
		data.append("call_no", call_no);
		data.append("sub_description_travel", sub_description_travel);

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
				$(".exp_travel_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_travel_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_travel_expense_sub_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						form_reset("expense_sub_form");
						$("#expense_type_travel").val("");
						$("#amount_travel").val("");
						$("#sub_description_travel").val("");
						$(".dropify-clear").trigger("click");
					}
					$(".exp_travel_expense_sub_btn").removeAttr("disabled", "disabled");
					if (unique_id && msg == "already") {
						$(".exp_travel_expense_sub_btn").text("Update");
					} else {
						$(".exp_travel_expense_sub_btn").text("Add");
						$(".exp_travel_expense_sub_btn").attr("onclick", "exp_travel_expense_sub('')");
					}
					// Init Datatable
					sub_list_datatable_travel("exp_travel_expense_sub_datatable");
				}
				sweetalert(msg, url);
			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

		if (expense_type_travel == '') {
			document.getElementById('expense_type_travel').focus();
		} else if (amount_travel == '') {
			document.getElementById('amount_travel').focus();
		} else if (sub_description_travel == '') {
			document.getElementById('sub_description_travel').focus();
		}
		//else if(oem_map_justification==''){document.getElementById('oem_map_justification').focus();}
	}
}

function exp_food_daily_expense_sub_edit(unique_id = "") {
	if (unique_id) {
		var data = "unique_id=" + unique_id + "&action=exp_food_daily_expense_sub_edit";

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		// var url      = sessionStorage.getItem("list_link");
		var url = "";

		// console.log(data);
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			beforeSend: function () {
				$(".exp_food_daily_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_food_daily_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var data = obj.data;
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_food_daily_expense_sub_btn").text("Error");
				} else {
					var expense_type       = data.expense_type_unique_id;
					var amount             = data.amount;
					var customer_unique_id = data.customer_unique_id;
					var call_no            = data.call_no;
					var sub_description    = data.description;
					var file_name          = data.file_name;

					if ((customer_unique_id != '')&&(customer_unique_id != 'null')) {
						document.getElementById("expense_call_status").checked = true;
					}

					$("#expense_type").val(expense_type).trigger('change');
					$("#amount").val(amount);
					$("#call_no_hidden").val(call_no);
					$("#customer_hidden").val(customer_unique_id);

					expense_call_function();

					$("#call_no").val(call_no).trigger('change');
					$("#customer_name").val(customer_unique_id).trigger('change');
					$("#sub_description").val(sub_description);

					// Button Change 
					$(".exp_food_daily_expense_sub_btn").removeAttr("disabled", "disabled");
					$(".exp_food_daily_expense_sub_btn").text("Update");
					$(".exp_food_daily_expense_sub_btn").attr("onclick", "exp_food_daily_expense_sub('" + unique_id + "')");
				}
			},
			error: function (data) {
				alert("Network Error");
			}
		});
	}
	
}

function exp_hotel_expense_sub_edit(unique_id = "") {
	if (unique_id) {
		var data = "unique_id=" + unique_id + "&action=exp_hotel_expense_sub_edit";

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		// var url      = sessionStorage.getItem("list_link");
		var url = "";

		// console.log(data);
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			beforeSend: function () {
				$(".exp_hotel_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_hotel_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var data = obj.data;
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_hotel_expense_sub_btn").text("Error");
				} else {
					var expense_type       = data.expense_type_unique_id;
					var amount             = data.amount;
					var customer_unique_id = data.customer_unique_id;
					var call_no            = data.call_no;
					var sub_description    = data.description;
					var file_name          = data.file_name;

					if ((customer_unique_id != '')&&(customer_unique_id != 'null')) {
						document.getElementById("expense_call_status").checked = true;
					}

					$("#expense_type").val(expense_type).trigger('change');
					$("#amount").val(amount);
					$("#call_no_hidden").val(call_no);
					$("#customer_hidden").val(customer_unique_id);

					expense_call_function();

					$("#call_no").val(call_no).trigger('change');
					$("#customer_name").val(customer_unique_id).trigger('change');
					$("#sub_description").val(sub_description);

					// Button Change 
					$(".exp_hotel_expense_sub_btn").removeAttr("disabled", "disabled");
					$(".exp_hotel_expense_sub_btn").text("Update");
					$(".exp_hotel_expense_sub_btn").attr("onclick", "exp_hotel_expense_sub('" + unique_id + "')");
				}
			},
			error: function (data) {
				alert("Network Error");
			}
		});
	}
}

function exp_petrol_expense_sub_edit(unique_id = "") {
	if (unique_id) {
		var data = "unique_id=" + unique_id + "&action=exp_petrol_expense_sub_edit";

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		// var url      = sessionStorage.getItem("list_link");
		var url = "";

		// console.log(data);
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			beforeSend: function () {
				$(".exp_petrol_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_petrol_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var data = obj.data;
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_petrol_expense_sub_btn").text("Error");
				} else {
					var expense_type       = data.expense_type_unique_id;
					var amount             = data.amount;
					var customer_unique_id = data.customer_unique_id;
					var call_no            = data.call_no;
					var sub_description    = data.description;
					var file_name          = data.file_name;

					if ((customer_unique_id != '')&&(customer_unique_id != 'null')) {
						document.getElementById("expense_call_status").checked = true;
					}

					$("#expense_type").val(expense_type).trigger('change');
					$("#amount").val(amount);
					$("#call_no_hidden").val(call_no);
					$("#customer_hidden").val(customer_unique_id);

					expense_call_function();

					$("#call_no").val(call_no).trigger('change');
					$("#customer_name").val(customer_unique_id).trigger('change');
					$("#sub_description").val(sub_description);

					// Button Change 
					$(".exp_petrol_expense_sub_btn").removeAttr("disabled", "disabled");
					$(".exp_petrol_expense_sub_btn").text("Update");
					$(".exp_petrol_expense_sub_btn").attr("onclick", "exp_petrol_expense_sub('" + unique_id + "')");
				}
			},
			error: function (data) {
				alert("Network Error");
			}
		});
	}
}

function exp_travel_expense_sub_edit(unique_id = "") {
	if (unique_id) {
		var data = "unique_id=" + unique_id + "&action=exp_travel_expense_sub_edit";

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		// var url      = sessionStorage.getItem("list_link");
		var url = "";

		// console.log(data);
		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			beforeSend: function () {
				$(".exp_travel_expense_sub_btn").attr("disabled", "disabled");
				$(".exp_travel_expense_sub_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var data = obj.data;
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				if (!status) {
					$(".exp_travel_expense_sub_btn").text("Error");
				} else {
					var expense_type       = data.expense_type_unique_id;
					var amount             = data.amount;
					var customer_unique_id = data.customer_unique_id;
					var call_no            = data.call_no;
					var sub_description    = data.description;
					var file_name          = data.file_name;

					if ((customer_unique_id != '')&&(customer_unique_id != 'null')) {
						document.getElementById("expense_call_status").checked = true;
					}

					$("#expense_type").val(expense_type).trigger('change');
					$("#amount").val(amount);
					$("#call_no_hidden").val(call_no);
					$("#customer_hidden").val(customer_unique_id);

					expense_call_function();

					$("#call_no").val(call_no).trigger('change');
					$("#customer_name").val(customer_unique_id).trigger('change');
					$("#sub_description").val(sub_description);

					// Button Change 
					$(".exp_travel_expense_sub_btn").removeAttr("disabled", "disabled");
					$(".exp_travel_expense_sub_btn").text("Update");
					$(".exp_travel_expense_sub_btn").attr("onclick", "exp_travel_expense_sub('" + unique_id + "')");
				}
			},
			error: function (data) {
				alert("Network Error");
			}
		});
	}
}
//date and time display function

function timeDateDisplay() {
    var x = new Date()
    var ampm = x.getHours() >= 12 ? ' PM' : ' AM';
    hours = x.getHours() % 12;
    hours = hours ? hours : 12;
    hours = hours.toString().length == 1 ? 0 + hours.toString() : hours;

    var minutes = x.getMinutes().toString()
    minutes = minutes.length == 1 ? 0 + minutes : minutes;

    var seconds = x.getSeconds().toString()
    seconds = seconds.length == 1 ? 0 + seconds : seconds;

    var month = (x.getMonth() + 1).toString();
    month = month.length == 1 ? 0 + month : month;

    var dt = x.getDate().toString();
    dt = dt.length == 1 ? 0 + dt : dt;

    var x1 = dt + "-" + month + "-" + x.getFullYear();
    // x1 = x1 + " - " + hours + ":" + minutes + ":" + seconds + " " + ampm;
    x1 = x1 + " - " + hours + ":" + minutes + " " + ampm;
    document.getElementById('approved_date').value = x1;

    display_c7();
}

function display_c7() { 
    var refresh = 1000; // Refresh rate in milli seconds
    mytime = setTimeout('timeDateDisplay()', refresh)
}
display_c7()