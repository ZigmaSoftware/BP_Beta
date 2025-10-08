$(document).ready(function () {
	sub_list_datatable("fullday_leave_report_datatable", form_name, "fullday_leave_report_datatable");
	sub_list_datatable("halfday_leave_report_datatable", form_name, "halfday_leave_report_datatable");
	sub_list_datatable("work_from_home_report_datatable", form_name, "work_from_home_report_datatable");
	sub_list_datatable("idle_report_datatable", form_name, "idle_report_datatable");
	sub_list_datatable("onduty_report_datatable", form_name, "onduty_report_datatable");
	sub_list_datatable("permission_report_datatable", form_name, "permission_report_datatable");
	sub_list_datatable("late_report_datatable", form_name, "late_report_datatable");
	sub_list_datatable("present_staff_report_datatable", form_name, "present_staff_report_datatable");
	sub_list_datatable("absent_staff_report_datatable", form_name, "absent_staff_report_datatable");
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		      = 'Staff Salary Report';
var form_header		      = '';
var form_footer 	      = '';
var table_name 		      = '';
var sub_table_id 		  = 'count_list';
var full_table_id 		  = 'fullday_leave_report_datatable';
var half_table_id 		  = 'halfday_leave_report_datatable';
var work_table_id 		  = 'work_from_home_report_datatable';
var idle_table_id 		  = 'idle_report_datatable';
var onduty_table_id       = 'onduty_report_datatable';
var permission_table_id   = 'permission_report_datatable';
var late_table_id         = 'late_report_datatable';
var present_table_id 	  = 'present_staff_report_datatable';
var absent_table_id 	  = 'absent_staff_report_datatable';
var action 			      = "datatable";


function sub_list_datatable(table_id = "", form_name = "", action = "") {
   	var table       = $("#"+table_id);
   	var sub_table   = $("#"+sub_table_id);
    var entry_date  = $("#entry_date").val();
    var to_date  = $("#to_date").val();    
	var data 	    = {
		"entry_date": entry_date,
		"to_date": to_date,
		"action"	: action,
	};
	
    var ajax_url = sessionStorage.getItem("folder_crud_link");
	if((table_id == 'permission_report_datatable') ||(table_id == 'late_report_datatable')){
    	var datatable = table.DataTable({
	        "searching": false,
	        "paging"   : false,
	        "ordering" : false,
	        "info"     : false,
	        "ajax"     : {
	            url: ajax_url,
	            type: "POST",
	            data: data
	        },
	        dom: 'Blfrtip',
			
			buttons: [
	            'copy',
	            'csv',
	            'excel',
	          
	        ],
	        "columnDefs" : [
	        {"className" : "text-center", "targets": [-1,-2]}
	        ], 

	    });
    }else if(table_id == 'present_staff_report_datatable'){
    	var datatable = table.DataTable({
	        "searching": false,
	        "paging"   : false,
	        "ordering" : false,
	        "info"     : false,
	        "ajax"     : {
	            url: ajax_url,
	            type: "POST",
	            data: data
	        },
	        dom: 'Blfrtip',
			
			buttons: [
	            'copy',
	            'csv',
	            'excel',
	          
	        ],
	        "columnDefs" : [
	        {"className" : "text-center", "targets": [-2]}
	        ], 

	    });
    }else{
	    var datatable = table.DataTable({
	        "searching": false,
	        "paging"   : false,
	        "ordering" : false,
	        "info"     : false,
	        "ajax"     : {
	            url: ajax_url,
	            type: "POST",
	            data: data
	        },
	        dom: 'Blfrtip',
			
			buttons: [
	            'copy',
	            'csv',
	            'excel',
	          
	        ],
	        "columnDefs" : [
	        {"className" : "text-center", "targets": [-1]}
	        ], 

	    });
	}		
    datatable.on('xhr', function (e, settings, json) {
       if (sub_table_id == "count_list") { 
       		var total_staff          = json['total_staff'];
       		var full_day_leave       = json['full_day_leave'];
       		var half_day_leave       = json['half_day_leave']; 
       		var work_from_home       = json['work_from_home'];
       		var idle                 = json['idle']; 
       		var on_duty              = json['on_duty'];
       		var permission           = json['permission'];
       		var late                 = json['late'];
       		var present_staff        = json['present_staff'];
       		var non_present_staff    = json['non_present_staff'];

       		 $("#total_staff").html(total_staff);
			 $("#full_day_leave").html(full_day_leave);
			 $("#half_day_leave").html(half_day_leave);
			 $("#work_from_home").html(work_from_home);
			 $("#idle").html(idle);
			 $("#on_duty").html(on_duty);
			 $("#permission").html(permission);
			 $("#late").html(late);
			 $("#present_staff").html(present_staff);
			 $("#non_present_staff").html(non_present_staff);
			
        }
    });
    
    return datatable;
}

function init_datatable(table_id='',form_name='',action='') {

	var table = $("#"+table_id);
	
	var entry_date  = $("#entry_date").val();
	var to_date  = $("#to_date").val();
// 	alert(to_date);
	var data 	    = {
		"entry_date": entry_date,
		"to_date"   : to_date,
		"action"	: action,
	};

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		
		scrollX     : true,
		scrollY     : "500px",
		processing  : true,
        serverSide  : true,
       
        responsive  : false,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		},
		// dom: 'Blfrtip',
		
		// buttons: [
  //           'copy',
  //           'csv',
  //           'excel',
  //           'print'
  //       ],
        "columnDefs" : [
        {"className" : "text-left", "targets": [-1,-2,-3,-4]}
        ], 
		lengthChange : true
    });
	
	datatable.on('xhr', function (e, settings, json) {
        if (table_id == "count_list") { 
       		var total_staff          = json['total_staff'];
       		var full_day_leave       = json['full_day_leave'];
       		var half_day_leave       = json['half_day_leave']; 
       		var work_from_home       = json['work_from_home'];
       		var idle                 = json['idle']; 
       		var on_duty              = json['on_duty'];
       		var permission           = json['permission'];
       		var present_staff        = json['present_staff'];
       		var non_present_staff    = json['non_present_staff'];

       		$("#total_staff").html(total_staff);
			$("#full_day_leave").html(full_day_leave);
			$("#half_day_leave").html(half_day_leave);
			$("#work_from_home").html(work_from_home);
			$("#idle").html(idle);
			$("#on_duty").html(on_duty);
			$("#permission").html(permission);
			$("#present_staff").html(present_staff);
			$("#non_present_staff").html(non_present_staff);
		}
    });
    return datatable;
}

function daydattendanceFilter(filter_action = 0) {

	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}
	var entry_date       = $("#entry_date").val();
	var to_date  = $("#to_date").val();
// 		alert(to_date);
	
		sessionStorage.setItem("entry_date", entry_date);
		sessionStorage.setItem("expense_action", filter_action);
	
	var filter_data = {
		"entry_date": entry_date,
		"to_date"   : to_date,
		"filter_action": filter_action
	};

	console.log(filter_data);
	sub_list_datatable("fullday_leave_report_datatable", form_name, "fullday_leave_report_datatable", filter_data);
	sub_list_datatable("halfday_leave_report_datatable", form_name, "halfday_leave_report_datatable", filter_data);
	sub_list_datatable("work_from_home_report_datatable", form_name, "work_from_home_report_datatable", filter_data);
	sub_list_datatable("idle_report_datatable", form_name, "idle_report_datatable", filter_data);
	sub_list_datatable("onduty_report_datatable", form_name, "onduty_report_datatable", filter_data);
	sub_list_datatable("permission_report_datatable", form_name, "permission_report_datatable", filter_data);
	sub_list_datatable("late_report_datatable", form_name, "late_report_datatable", filter_data);
	sub_list_datatable("present_staff_report_datatable", form_name, "present_staff_report_datatable", filter_data);
	sub_list_datatable("absent_staff_report_datatable", form_name, "absent_staff_report_datatable", filter_data);
}

function mail_send(entry_date = '') { 
	var ajax_url = "folders/day_attendance_report/email.php"
	
	if (entry_date) {

		var data = {
			"entry_date" : entry_date,
			
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {
				
				if (data) {
					
					console.log(data);
					sweetalert("custom_mail",'','','Email sent successfully');
				}
			}
		});
	}
}


function enter_attendance(entry_date = '') { 
	var ajax_url = "folders/day_attendance_report/attendance_entry.php"
	
	if (entry_date) {

		var data = {
			"entry_date" : entry_date,
			
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {
				
				if (data) {
					
					sweetalert("custom_attendance",'','','Added successfully');
				}
			}
		});
	}
}

