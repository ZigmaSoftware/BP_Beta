$(document).ready(function () {

	status_check();

	datatable_init_based_on_prev_state ();
	sub_list_datatable(table_sub_id,form_name,action);
});


function datatable_init_based_on_prev_state () {
		// Data Table Filter Function Based ON Previous Search
		var from_date  	  = sessionStorage.getItem("follow_up_call_from_date");
		var to_date       = sessionStorage.getItem("follow_up_call_to_date");
		var filter_action = sessionStorage.getItem("follow_up_call_action");
	
		if (!from_date) {
			from_date = $("#follow_up_call_from").val();
		} else {
			$("#follow_up_call_from").val(from_date);
		}
	
		if (!to_date) {
			to_date = $("#follow_up_call_to").val();
		} else {
			$("#follow_up_call_to").val(to_date);
		}
	
		if (!filter_action) {
			filter_action = 0;
		}
	
		// Datatable Filter Data
		var filter_data = {
			"from_date" 	: from_date,
			"to_date" 		: to_date,
			"filter_action" : filter_action
		};
	
		// var table_id 	= "follow_up_call_datatable";
		init_datatable(table_id,form_name,action,filter_data);
}

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Follow Up Call';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'follow_up_call_datatable';
var table_sub_id    = 'follow_up_call_sub_datatable';
var action 			= "datatable";

function status_check() {

	var status = $("input[name='follow_up_action_type']:checked").val();
	if (status != 0) {
		$(".next_follow_up_status_inp").attr("required","required");
		$(".close_status_inp").removeAttr("required","required");
		
		$(".next_follow_up_status").removeClass("d-none");
		$(".close_status").addClass("d-none");
		
	} else {
		$(".close_status_inp").attr("required","required");
		$(".next_follow_up_status_inp").removeAttr("required","required");
		
		$(".next_follow_up_status").addClass("d-none");
		$(".close_status").removeClass("d-none");
		
	} 	

}

function date_count(days = "") {
	var date = new Date();
	console.log(date);
	if (days) {
		days = parseInt(days);

		// add a day
		date.setDate(date.getDate() + days);
		console.log(date);
	}

	var year 		= date.getFullYear();
	var month 		= date.getMonth() < 10 ? "0"+(date.getMonth() + 1) : (date.getMonth() + 1);
	var day 		= date.getDate() < 10 ? "0"+date.getDate() : date.getDate();
	var final_date  = year + "-" + month + "-" + day;
	
	// Update Next Follow Up Date
	$("#next_follow_up_date").val(final_date);
}

function days_count(sel_date = "") {
	// alert(sel_date);
	// console.log(typeof sel_date);
	var today 		= new Date();
	var sel_date  	= new Date(sel_date);

	// To calculate the time difference of two dates 
	var Difference_In_Time = sel_date.getTime() - today.getTime(); 
	
	// To calculate the no. of days between two dates 
	var Difference_In_Days = Math.ceil(Difference_In_Time / (1000 * 3600 * 24));

	// alert(Difference_In_Days);

	$("#next_follow_up_days").val(Difference_In_Days);
}

function follow_up_call_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var data 	 = $(".was-validated").serialize();
        // data 		+= "&sub_unique_id="+sub_unique_id;
        // data 		+= "&prev_sub_unique_id="+sub_unique_update_id;
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

				var leads_checked = $("#new_lead").prop("checked");

				if (leads_checked && msg != "error") {
					new_leads();
					// url = "";
				} else {
					sweetalert(msg,url);
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("form_alert");
    }
}



function follow_up_call_sub_au (unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&action=sub_add_update";

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

				var leads_checked = $("#new_lead").prop("checked");

				if (leads_checked && msg != "error") {
					new_leads();
					// url = "";
				} else {
					sweetalert(msg,url);
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("form_alert");
    }
}

function new_leads () {

	var ajax_url 		= sessionStorage.getItem("folder_crud_link");
	var url      		= sessionStorage.getItem("list_link");

	var call_id 		= $("#call_id").val();
	var call_unique_id 	= $("#call_unique_id").val();


	var sub_data 		= {
		"call_id" 			: call_id,
		"call_unique_id" 	: call_unique_id,
		"action" 			: "new_lead"
	};

	// Create New Lead Ajax Start
	$.ajax({
		type 	: "POST",
		url 	: ajax_url,
		data 	: sub_data,
		beforeSend 	: function() {
			$(".createupdate_btn").attr("disabled","disabled");
			$(".createupdate_btn").text("Loading...");
		},
		success		: function(data) {

			var obj     = JSON.parse(data);
			var msg     = obj.msg;
			var status  = obj.status;
			var error   = obj.error;
			var url   	= obj.url;

			// var url 	= "index.php?file=leads/list&"+url;
			 var url 	= "index.php?file=leads/list";

			sweetalert("update",url);

			// Here Redirect To Leads Udpate
		},
		error 		: function(data) {
			alert("Network Error");
		}
	});
	// Create New Lead ajax End
}

function init_datatable(table_id='',form_name='',action='',filter_data = "") {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action
	};
	// alert(filter_data);
	data 		  = {
		...data,
		...filter_data
	};

	// console.log(data);

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

	datatable.on( 'xhr', function ( e, settings, json ) {

		var count 	= json.count[0];

		// Update All Counts In List Panel
		$("#new_calls_count").html(count.new_calls);
		$("#follow_up_calls_count").html(count.follow_ups);
		$("#updated_calls_count").html(count.updated);
		$("#closed_calls_count").html(count.closed);

	} );
}

function follow_up_call_delete(unique_id = "",sub_unique_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	confirm_delete('delete')
	.then((result) => {
		if (result.isConfirmed) {

			var data = {
				"unique_id" 	: unique_id,
				"sub_unique_id" : sub_unique_id,
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

						var sub_unique_id = $("#sub_unique_id").val();

						if (sub_unique_id) {
							var url      = sessionStorage.getItem("list_link");							
						} else {
							datatable_init_based_on_prev_state ();
						}
					}
					sweetalert(msg,url);
				}
			});

		} else {
			// alert("cancel");
		}
	});
}

function sub_list_datatable (table_id = "", form_name = "", action = "") {
	var unique_id 		= $("#call_unique_id").val();
	var sub_unique_id 	= $("#sub_unique_id").val();
	
	if (unique_id) {

		var table = $("#"+table_id);
		var data 	  = {
			"follow_up_call_unique_id"    : unique_id,
			"follow_up_call_sub_unique_id": sub_unique_id,
			"action"	            	  : table_id
		};
		var ajax_url = sessionStorage.getItem("folder_crud_link");

		var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
			"searching": false,
			"paging":   false,
			"ordering": false,
			"info":     false,
			"ajax"		: {
				url 	: ajax_url,
				type 	: "POST",
				data 	: data
			}
		});
	}
}

// function follow_up_call_delete (unique_id = "") {
//     if (unique_id) {

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url      = sessionStorage.getItem("list_link");
        
//         confirm_delete('delete')
//         .then((result) => {
//             if (result.isConfirmed) {
    
//                 var data = {
//                     "unique_id" 	: unique_id,
//                     "action"		: "follow_up_call_delete"
//                 }
    
//                 $.ajax({
//                     type 	: "POST",
//                     url 	: ajax_url,
//                     data 	: data,
//                     success : function(data) {
    
//                         var obj     = JSON.parse(data);
//                         var msg     = obj.msg;
//                         var status  = obj.status;
//                         var error   = obj.error;
    
//                         if (!status) {
//                             url 	= '';                            
//                         } else {
//                             sub_list_datatable("follow_up_call_sub_datatable");
//                         }
//                         sweetalert(msg,url);
//                     }
//                 });
    
//             } else {
//                 // alert("cancel");
//             }
//         });
//     }
// }

function follow_up_call_edit(unique_id = "",sub_unique_id = "") {

    if (unique_id) {

		$(".createupdate_btn").removeClass("d-none");

        var call_id     = $("#call_unique_id").val();
        var data 		= "unique_id="+unique_id+"&action=follow_up_call_sub_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

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
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".createupdate_btn").text("Error");
                    console.log(error);
				} else {
                    // console.log(obj);
                    var call_status         	= data.call_status;
                    var close_date     			= data.close_date;
                    var close_remark     		= data.close_remark;
                    var next_follow_up_date     = data.next_follow_up_date;
                    var next_follow_up_days     = data.next_follow_up_days;
                    var remark   				= data.remark;
                    var status   				= data.status;

                    $("#cur_status").val(status);
                    $("#remark").val(remark);
                    $("#next_follow_up_days").val(next_follow_up_days);
                    $("#next_follow_up_date").val(next_follow_up_date);
                    $("#close_date").val(close_date);
                    $("#close_remark").val(close_remark);
					$("#call_status").val(call_status).trigger('change');
					
					if (call_status == 0) {
						$("#next_follow_up_action").prop("checked", true);
					} else {
						$("#close_action").prop("checked", true);
					}

					status_check();

                    // Button Change 
                    $(".createupdate_btn").removeAttr("disabled","disabled");
                    $(".createupdate_btn").text("Update");
                    $(".createupdate_btn").attr("onclick","follow_up_call_sub_au('"+unique_id+"')");
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
    }
}

// Follow Up Filter Function

function followUpCallFilter(filter_action = 0 ) {
	var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
		var from_date = $("#follow_up_call_from").val();
		var to_date   = $("#follow_up_call_to").val();

		var is_vaild = fromToDateValidity(from_date,to_date);

		if (is_vaild) {

			sessionStorage.setItem("follow_up_call_from_date",from_date);
			sessionStorage.setItem("follow_up_call_to_date",to_date);
			sessionStorage.setItem("follow_up_call_action",filter_action);

			// Delete Below Line After Testing Complete
			sessionStorage.setItem("follow_up_call_action",0);

			var filter_data = {
				"from_date" 	: from_date,
				"to_date" 		: to_date,
				"filter_action" : filter_action
			};

			console.log(filter_data);

			init_datatable(table_id,form_name,action,filter_data);

		}

	} else {
        sweetalert("form_alert","");
	}
}