$(document).ready(function () {
	
	consolidatedattendanceFilter();
	//init_datatable(table_id, form_name, action, filter_data);
	
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Executive Wise Consolidate Attendance Report';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'executive_wise_consolidate_report_datatable';
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
		
		scrollX     : true,
		scrollY     : "400px",
		processing : true,
        serverSide : true,
       
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
}


function consolidatedattendanceFilter(filter_action = 0) {

	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}


	var year_month       = $("#year_month").val();
	
	
		sessionStorage.setItem("year_month", year_month);
		sessionStorage.setItem("expense_action", filter_action);
		 
		
		var filter_data = {
			"year_month": year_month,
			"filter_action": filter_action
		};

		console.log(filter_data);

		init_datatable(table_id, form_name, action, filter_data);
	
}