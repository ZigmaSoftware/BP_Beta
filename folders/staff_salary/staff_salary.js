$(document).ready(function () {
	
	init_datatable(table_id, form_name, action);
	
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Staff Salary Report';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'staff_salary_report_datatable';
var action 			= "datatable";


function init_datatable(table_id='',form_name='',action='') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action,
	};
	 

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		
		scrollX     : true,
		scrollY     : "500px",
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
