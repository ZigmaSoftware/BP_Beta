$(document).ready(function () {
	
	monthlyattendanceFilter();
	
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Executive Wise Monthly Attendance Report';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'executive_wise_monthly_report_datatable';
var action 			= "datatable";


function init_datatable(table_id='',form_name='',action='',filter_data = "") {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action,

	};
	 data          = {
        ...data,
        ...filter_data
    };

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		lengthMenu  : [
	        [-1],
	        ["All"]
    	],
		scrollX     : true,
		scrollY     : "400px",
		processing  : true,
        serverSide  : true,
       
        responsive  : false,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		},
		dom: 'Blfrtip',
		
		buttons: [
            'copy',
            'csv',
            'excel',
			'print'
        ],
		lengthChange: true
        
	});

	datatable.on('xhr', function (e, settings, json) {
       if (table_id == "executive_wise_monthly_report_datatable") { 
       		var current_month      = json['current_month'];
       		var working_days       = json['working_days'];
       		var no_of_holiday      = json['no_of_holiday'];
       		var no_of_leave        = json['no_of_leave'];
       		var no_of_late         = json['no_of_late'];
       		var no_of_permission   = json['no_of_permission'];
       		var no_of_absent       = json['no_of_absent'];
       		var no_of_comp_off     = json['no_of_comp_off'];
       		var total_worked_days  = json['total_worked_days'];
       		var no_of_sunday  	   = json['no_of_sunday'];
       		var no_of_emer_leave   = json['no_of_emer_leave'];
       		
       		$("#current_month").html(current_month);
       		$("#working_days").html(working_days);
       		$("#no_of_holiday").html(no_of_holiday);
       		$("#no_of_leave").html(no_of_leave);
       		$("#no_of_late").html(no_of_late);
       		$("#no_of_permission").html(no_of_permission);
       		$("#no_of_absent").html(no_of_absent);
       		$("#no_of_comp_off").html(no_of_comp_off);
       		$("#total_worked_days").html(total_worked_days);
       		$("#no_of_sunday").html(no_of_sunday);
       		$("#no_of_emer_leave").html(no_of_emer_leave);
			 
			
        }
        
        
	});
    
    return datatable;
}


function monthlyattendanceFilter(filter_action = 0) {

	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}


	var year_month       = $("#year_month").val();
	var executive_name  = $("#executive_name").val();

	if(executive_name) {
		sessionStorage.setItem("year_month", year_month);
		sessionStorage.setItem("expense_action", filter_action);
		sessionStorage.setItem("executive_name", executive_name);
        
		
		var filter_data = {
			"year_month": year_month,
			"executive_name": executive_name,
			"filter_action": filter_action
		};

		console.log(filter_data);

		init_datatable(table_id, form_name, action, filter_data);
	}
}