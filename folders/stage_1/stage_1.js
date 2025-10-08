$(document).ready(function () { 
	var table_id = "stage_1_datatable";
	complaint_category_filter1();
// 	init_datatable(table_id, form_name, action);
	//sub_list_datatable("document_upload_sub_datatable");
	sub_list_datatable("status_sub_datatable", form_name, "status_sub_datatable");

	get_level_count();
	get_tag_level_count();
	get_all_count();
	get_call_count();
});

var company_name = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone = sessionStorage.getItem("company_name");
var company_email = sessionStorage.getItem("company_name");
var company_logo = sessionStorage.getItem("company_name");

var form_name = 'Complaint Category';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'stage_1_datatable';
var action = "datatable";

function init_datatable(table_id = '', form_name = '', action = '') {
	var table = $("#" + table_id); 
	var from_date           = $("#from_date").val();
	var to_date             = $("#to_date").val();
	var department_type 	= $("#department_type").val();
	var complaint_name 	    = $("#complaint_name").val();
	var priority 	        = $("#priority").val();
    var site_name           = $('#site_name').val();
	var status_name         = $('#status_name').val();

	var data = {
		"action"			: action,
		"from_date"         : from_date,
		"to_date"           : to_date,
		"department_type"	: department_type,
		"complaint_name"	: complaint_name,
		"priority"          : priority,
		"site_name"         : site_name,
		"status_name"	 	: status_name,

	};
// 	data = { 
// 		...data,

// 	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({

		ordering: true,
		searching: true,
		"searching": true,

		"ajax": {
			url: ajax_url,
			type: "POST",
			data: data
		},
		dom: 'Blfrtip',
		buttons: [
			'copy', 'csv', 'excel', 'pdf', 'print'
		]
	});
}



function sub_list_datatable(table_id = "", form_name = "", action = "") {

	var unique_id = $("#unique_id").val();
	var screen_unique_id = $("#screen_unique_id").val();
// 	var entry_date = 

	var table = $("#" + table_id);
	var data = {
		"unique_id": unique_id,
		"screen_unique_id": screen_unique_id,
		"action": table_id,
	};
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

function print(file_name) {
	onmouseover = window.open('uploads/complaint_category/' + file_name, 'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}


function new_external_window_print(event, url, unique_id) {
	var unique_id = $('#unique_id').val();
	
	var link = url + '?unique_id=' + unique_id

	onmouseover = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
	event.preventDefault();
}


function is_online() {
    // Implement your internet check logic here
    return true;  // Return true if online, false otherwise
}

function form_validity_check(form_id) {
    // Implement your form validity check logic here
    return true;  // Return true if form is valid, false otherwise
}

function fromToDateValidity(from_date, to_date) {
    // Implement your date validity check logic here
    return true;  // Return true if dates are valid, false otherwise
}


function status_sub_add_update(unique_id = "") { // au = add,update
// alert();
// alert('hi');
	var internet_status = is_online();

	var status_option = $("#status_option").val();
	var doc_option = $("#doc_option").val();
	var entry_date = $("#entry_date").val();
    var remark_type = $("#remark_type").val();  
	var user_name_select = $("#user_name_select").val();
// 	alert(user_name_select);
	var status_description = $("#status_description").val();
	var screen_unique_id = $("#screen_unique_id").val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	if ((status_option) && (status_description)) {

		var data = new FormData();
		if (doc_option) {
            var image_s = document.getElementById("test_file_exp");
    
            if (image_s != '') {
                    for (var i = 0; i < image_s.files.length; i++) {
                      data.append("test_file[]", document.getElementById('test_file_exp').files[i]);
                    }
                // }
                } else {
                   
                    data.append("test_docs", '');
                
                }
   
        }
		data.append("status_option", status_option);
		data.append("remark_type", remark_type);
		
	

var user_name_select = $("#user_name_select").val();

if (user_name_select != null) {
            data.append("user_name_select", user_name_select.join(','));
        } else {
            data.append("user_name_select", '');
        }
            // var user_name_select = $("#user_name_select").val();
            //         if (user_name_select != null) {
            //             for (var i = 0; i < user_name_select.length; i++) {
            //                 data.append("user_name_select[]", user_name_select[i]);
            //             }
            //         }

// 		data.append("user_name_select", user_name_select);
        // data.append("user_name_select",user_name_select);
		data.append("doc_option", doc_option);
		data.append("status_description", status_description);
		data.append("screen_unique_id", screen_unique_id);
		data.append("entry_date", entry_date);
		data.append("action", "status_sub_add_update");
		data.append("unique_id", unique_id);

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url = '';

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			beforeSend: function () {
				$(".status_sub_add_update_btn").attr("disabled", "disabled");
				$(".status_sub_add_update_btn").text("Loading...");
			},
			success: function (data) {

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;
				// var user_name_select_from_server = obj.user_name_select;

				if (!status) {
					$(".status_sub_add_update_btn").text("Error");
					console.log(error);
				} else {
					if (msg !== "already") {
						//form_reset("periodic_sub_form");
						$("#status_option").val('').trigger('change');
						$("#remark_type").val('').trigger('change');
						$("#user_name_select").val('').trigger('change');
						$("#status_description").val("");
						sweetalert(msg, url);

					}
					$(".status_sub_add_update_btn").removeAttr("disabled", "disabled");
					if (unique_id && msg == "already") {
						$(".status_sub_add_update_btn").text("Update");
						sweetalert("custom", '', '', 'The Complaint has already completed');
					} else {
						$(".status_sub_add_update_btn").text("Add");
						$(".status_sub_add_update_btn").attr("onclick", "status_sub_add_update('')");
						if (msg == 'already') {

							sweetalert("custom", '', '', 'The Complaint has already completed');
						} else {
							sweetalert(msg, url);
						}

					}
					// Init Datatable
					sub_list_datatable("status_sub_datatable");

				}

			},
			error: function (data) {
				alert("Network Error");
			}
		});


	} else {

		sweetalert("custom", '', '', 'Create Sub Details');

		if (status_option == '') {
			document.getElementById('status_option').focus();
		} else if (status_description == '') {
			document.getElementById('status_description').focus();
		}
	}
}



function status_sub_delete(unique_id = "", screen_unique_id = "") {

	if (unique_id) {

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url = sessionStorage.getItem("list_link");

		confirm_delete('delete')
			.then((result) => {
				if (result.isConfirmed) {

					var data = {
						"unique_id": unique_id,
						"screen_unique_id": screen_unique_id,
						"action": "status_sub_delete"
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
								sub_list_datatable("status_sub_datatable");
							}
							$("#status_option").val(null).trigger('change');
							$("#status_description").val("");
							sweetalert(msg, url);
						}
					});

				} else {
					// alert("cancel");
				}
			});
	}
}



function stage_1_cu(unique_id = "") {


	var msg = "update";
	var url = sessionStorage.getItem("list_link");
	sweetalert(msg, url);


}


function complaint_category_filter(filter_action = 0) {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var is_form = form_validity_check("was-validated");
	// var from_date           = $("#from_date").val();
	// var to_date             = $("#to_date").val();
	var department_type = $("#department_type").val();
	var complaint_name 	= $("#complaint_name").val();
	var priority 	    = $("#priority").val();
	var zone_name 		= $('#zone_name').val();
	var ward_name 		= $('#ward_name').val();
	var stage     		= $('#stage').val(); 
	var status_name     = $('#status_name').val();
	//alert(zone_name);
	// alert(stage);

	//var is_vaild = fromToDateValidity(from_date, to_date);

	// sessionStorage.setItem("from_date",from_date);
	// sessionStorage.setItem("to_date",to_date);
	sessionStorage.setItem("department_type", department_type);
	sessionStorage.setItem("complaint_name", complaint_name);
	sessionStorage.setItem("priority", priority);
	sessionStorage.setItem("zone_name", zone_name);
	sessionStorage.setItem("ward_name", ward_name);
	sessionStorage.setItem("stage",stage);
	sessionStorage.setItem("status_name",status_name);


	var filter_data = {
		// "from_date"             : from_date,
		// "to_date"               : to_date,
		"department_type": department_type,
		"complaint_name": complaint_name,
		"priority"      : priority,
		"zone_name"		: zone_name,
		"ward_name"		: ward_name,
		"stage"	   		: stage,
		"status_name"	: status_name,

	};

	console.log(filter_data);

	init_datatable(table_id, form_name, action, filter_data);
}

function complaint_category_filter1() { 
    
    var table_id_2 = "stage_2_datatable";
	var table_id_3 = "stage_3_datatable";
	var table_id_4 = "stage_4_datatable";
	var table_id_5 = "stage_5_datatable";
	var table_id_6 = "stage_6_datatable";
	var table_id_7 = "stage_7_datatable";
    var table_id_all = "all_level_datatable";
    var table_id_call = "own_call_datatable";
    var table_id_tag = "tag_person_datatable";
    
    init_datatable(table_id,form_name,action);
    init_datatable(table_id_2, form_name, table_id_2);
    init_datatable(table_id_3, form_name, table_id_3);
    init_datatable(table_id_4, form_name, table_id_4);
    init_datatable(table_id_5, form_name, table_id_5);
    init_datatable(table_id_6, form_name, table_id_6);
    init_datatable(table_id_7, form_name, table_id_7);
    init_datatable(table_id_all, form_name, table_id_all);
    init_datatable(table_id_call, form_name, table_id_call);
    init_datatable(table_id_tag, form_name, table_id_tag);
    
}

function category_entry_filter1(department_name) {
    // alert("Oooo");
	var department_name = $('#department_type').val();
// 	var data = "&department_type=" + department_name + "&action=category_name_option_filter";
    var data = {
            department_type: department_name,
            action: 'category_name_option_filter'
        };

	var ajax_url = sessionStorage.getItem("folder_crud_link");

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


function category_entry_filter(department_name) {
	var department_name = $('#department_type').val();
	// var data      = $(".was-validated").serialize();
	var data = "&department_type=" + department_name + "&action=category_name_option";

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	// if (zone_id) {


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

function get_ward_name() {
	var zone_id = $('#zone_name').val();
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url = sessionStorage.getItem("list_link");


	var data = {
		"zone_id": zone_id,
		"action": "ward_name"
	}

	$.ajax({
		type: "POST",
		url: ajax_url,
		data: data,
		success: function (data) {
			var obj = JSON.parse(data);
			var data = obj.data;
			$('#ward_name').html(data);
		}
	});
}

function new_complaint() {
	window.location.href = 'index.php?file=complaint_category/create';
}

function print(file_name)
    {
       onmouseover= window.open('uploads/stage_1/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }  
    
function new_external_window_print1(event, url, unique_id) 
{
    // alert();
    // alert(url);
    // alert(unique_id);
    var unique_id = $("#unique_id").val();
    
event.preventDefault();

	var link = url + '?unique_id=' + unique_id;

	onmousever = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
	
}

function new_external_window_print_2(event, url, unique_id,screen_unique_id) {
   // var unique_id = $("#unique_id").val();
    // alert(unique_id);
    // var screen_unique_id = $("#screen_unique_id").val();
    // alert(screen_unique_id);
    event.preventDefault();

    var link = url + '?unique_id=' + unique_id+'&screen_unique_id='+screen_unique_id;

    onmouseover = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}

// function new_external_window_print_2(event, url, unique_id, screen_unique_id) 
// {
    
//     var unique_id = $("#unique_id").val();
//     // alert(unique_id);
//     var screen_unique_id = $("#screen_unique_id").val();
//     // alert(screen_unique_id);
    
// event.preventDefault();

// 	var link = url + '?unique_id=' + unique_id + '?screen_unique_id=' + screen_unique_id;

// 	onmousever = window.open(link, 'onmouseover', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
	
// }

function get_status_value(val) { // au = add,update

	var internet_status = is_online();

	var status_option = $("#status_option").val();
	var complaint_no = $("#complaint_no").val();
	
	var unique_id = $("#unique_id").val();
	var screen_unique_id = $("#screen_unique_id").val();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	if (status_option == "4") {

		var data = new FormData();
		
		data.append("status_option", status_option);
		data.append("complaint_no", complaint_no);
		data.append("status_description", status_description);
		data.append("screen_unique_id", screen_unique_id);
		data.append("action", "reopen_status_update");
		data.append("unique_id", unique_id);

		var ajax_url = sessionStorage.getItem("folder_crud_link");
		var url = '';

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			cache: false,
			contentType: false,
			processData: false,
			method: 'POST',
			// beforeSend: function () {
			// 	$(".status_sub_add_update_btn").attr("disabled", "disabled");
			// 	$(".status_sub_add_update_btn").text("Loading...");
			// },
			success: function (data) {alert(data);

				var obj = JSON.parse(data);
				var msg = obj.msg;
				var status = obj.status;
				var error = obj.error;

				// if (!status) {
				// 	$(".status_sub_add_update_btn").text("Error");
				// 	console.log(error);
				// } else {
				// 	if (msg !== "already") {
				// 		//form_reset("periodic_sub_form");
				// 		$("#status_option").val(null).trigger('change');
				// 		$("#status_description").val("");
				// 		sweetalert(msg, url);

				// 	}
				// 	$(".status_sub_add_update_btn").removeAttr("disabled", "disabled");
				// 	if (unique_id && msg == "already") {
				// 		$(".status_sub_add_update_btn").text("Update");
				// 		sweetalert("custom", '', '', 'The Complaint has already completed');
				// 	} else {
				// 		$(".status_sub_add_update_btn").text("Add");
				// 		$(".status_sub_add_update_btn").attr("onclick", "status_sub_add_update('')");
				// 		if (msg == 'already') {

				// 			sweetalert("custom", '', '', 'The Complaint has already completed');
				// 		} else {
				// 			sweetalert(msg, url);
				// 		}

				// 	}
				// 	// Init Datatable
				// 	sub_list_datatable("status_sub_datatable");

				// }

			},
			error: function (data) {
				alert("Network Error");
			}
		});


	 } 
		// else {

	// 	sweetalert("custom", '', '', 'Create Sub Details');

	// 	if (status_option == '') {
	// 		document.getElementById('status_option').focus();
	// 	} else if (status_description == '') {
	// 		document.getElementById('status_description').focus();
	// 	}
	// }
}


$(document).ready(function() {
    // Hide the Remarks Type row initially if the selected status is 'Completed'
    toggleRemarksType();

    // Event listener for when the status_option changes
    $('#status_option').change(function() {
        toggleRemarksType();
    });

    function toggleRemarksType() {
        // Check if the selected option is 'Completed'
        if ($('#status_option').val() === '2') {
            // Hide the Remarks Type row
            $('#remark_type').closest('.row').hide();
            $('#remark_type').val();
        } else {
            // Show the Remarks Type row
            $('#remark_type').closest('.row').show();
        }
    }
});

function print_view(file_name)
    {
       onmouseover= window.open('uploads/stage_1/'+file_name,'onmouseover','height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
    }  

    // function print_view(file_path) {
    //     // Open the file for viewing or printing
    //     console.log("Opening file: " + file_path); // Log the file path for debugging
    //     window.open(file_path, '_blank');
    // }
    
function get_tag_level_count(){

    var data = 
        {
            "action"           : "tag_wise_counts",
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
//             // alert(data);
            var obj   = JSON.parse(data);
           
            $('#tag_person_cnt').text(obj.tag_person_cnt);// Set the total count

        }
  });
}  

function get_all_count(){

    var data = 
        {
            "action"           : "all_counts",
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
//             // alert(data);
            var obj   = JSON.parse(data);
           
           $('#all_cnt').text(obj.all_cnt);// Set the total count

        }
  });
} 

function get_call_count(){

    var data = 
        {
            "action"           : "own_call_counts",
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
            // alert(data);
            var obj   = JSON.parse(data);
            $('#call_cnt').text(obj.call_cnt);
        }
    });
} 

function get_level_count(){
    // alert("hiii");
    // var month = $("#month_filter").val();
    var data = 
        {
            "action"           : "level_wise_counts",
        };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
        url: ajax_url,
        type:'POST',
        data: data,
        success:function(data)
        {
            var obj   = JSON.parse(data);
            //$('#all_cnt').text(obj.all_cnt);
            $('#level_1_cnt').text(obj.level_1_cnt);
            $('#level_2_cnt').text(obj.level_2_cnt);
            $('#level_3_cnt').text(obj.level_3_cnt);
            $('#level_4_cnt').text(obj.level_4_cnt);
            $('#level_5_cnt').text(obj.level_5_cnt);
            $('#level_6_cnt').text(obj.level_6_cnt); 
            $('#level_7_cnt').text(obj.level_7_cnt);
            // $('#tag_person_cnt').text(obj.tag_person_cnt);
        }
  });
}    