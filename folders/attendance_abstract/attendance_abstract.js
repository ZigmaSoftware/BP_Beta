$(document).ready(function () {
	
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'Attendance Summary Report';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'atttendance_summary_report_datatable';
var action 			= "datatable";

function init_datatable(table_id='',form_name='',action='',filter_data = "") {alert();

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

function attendnce_summary(filter_action = 0) {
	var ajax_url = "folders/attendance_abstract/table_listing.php";
	var year_month = $('#year_month').val();
	var data = {
			"year_month"  : year_month,
		};

	$.ajax({
		type    : "POST",
		url     : ajax_url,
		data    : data,
		success : function (data) {
			$('#listing_div').html(data);
			//attendnce_summary_filter();
			
		}
	});
}

function new_external_window(url) {
	
	onmouseover = window.open(url, 'onmouseover', 'height=950,width=1500,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}

$("#excel_export").click(function(){alert();
	window.location="folders/attendance_abstract/excel.php";
});