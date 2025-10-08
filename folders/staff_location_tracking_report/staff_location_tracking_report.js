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
var table_id 		= 'staff_location_tracking_report_datatable';
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
	
}
function monthlyattendanceFilter(filter_action = 0) {
	var internet_status = is_online();
	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}
	var year_month       = $("#entry_date").val();
	var executive_name  = $("#executive_name").val();
	if(year_month) {
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
// mythili
       
// JavaScript function to open a new window with a map
function openMap(latitude,longitude) {
   
    window.open("https://maps.google.com/maps?q=" + latitude + "," + longitude + "&output=embed", "_blank");
    
}
// JavaScript function to open a new window with a map
function checkoutopenMap(check_out_latitude,check_out_longitude) {
   
    window.open("https://maps.google.com/maps?q=" + check_out_latitude + "," + check_out_longitude + "&output=embed", "_blank");
    
}
