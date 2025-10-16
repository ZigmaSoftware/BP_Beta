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

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { "year_month": year_month },
        success: function (data) {
            $('#listing_div').html(data);
        },
        error: function (xhr, status, error) {
            console.error("Error loading attendance summary:", error);
        }
    });
}


function new_external_window(url) {
    window.open(
        url,
        'printWindow',
        'height=950,width=1500,resizable=no,left=200,top=150,toolbar=no,location=no,status=no,menubar=no'
    );
}

$(document).ready(function () {
    $("#btn_print").on("click", function () {
        // Trigger browser print dialog
        window.print();
    });
});

async function exportAttendanceToExcel() {
  const workbook = new ExcelJS.Workbook();
  const ws = workbook.addWorksheet("Attendance Report");

  // === 1) Report Title ===
  ws.mergeCells('A1:H1');
  ws.getCell('A1').value = 'Attendance Summary Report';
  ws.getCell('A1').font = { size: 16, bold: true };
  ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };

  const selectedMonth = document.getElementById('year_month').value || '';
  ws.mergeCells('A2:H2');
  ws.getCell('A2').value = selectedMonth ? `Month: ${selectedMonth}` : '';
  ws.getCell('A2').alignment = { horizontal: 'center' };

  let startRow = 4; // leave space for title

  // === 2) Grab table headers dynamically ===
  const headerCells = Array.from(document.querySelectorAll("#atttendance_summary_report_datatable thead th"));
  const headers = headerCells.map(th => th.innerText.trim());

  ws.getRow(startRow).values = headers;
  ws.getRow(startRow).font = { bold: true };
  ws.getRow(startRow).alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };

  // === 3) Grab table rows ===
  const rows = document.querySelectorAll("#atttendance_summary_report_datatable tbody tr");
  let excelRow = startRow + 1;
  rows.forEach(tr => {
    const tds = Array.from(tr.querySelectorAll("td"));
    const rowData = tds.map(td => td.innerText.trim());
    ws.getRow(excelRow).values = rowData;
    excelRow++;
  });

  // === 4) Auto column widths ===
  ws.columns.forEach(col => {
    let maxLength = 10;
    col.eachCell({ includeEmpty: true }, cell => {
      const len = cell.value ? cell.value.toString().length : 0;
      if (len > maxLength) maxLength = len;
    });
    col.width = maxLength + 2;
  });

  // === 5) Borders ===
  for (let r = startRow; r < excelRow; r++) {
    ws.getRow(r).eachCell(cell => {
      cell.border = {
        top: { style: 'thin' },
        left: { style: 'thin' },
        bottom: { style: 'thin' },
        right: { style: 'thin' }
      };
    });
  }

  // === 6) Save file ===
  const buf = await workbook.xlsx.writeBuffer();
  const fileName = selectedMonth 
    ? `attendance_summary_${selectedMonth}.xlsx`
    : 'attendance_summary.xlsx';
  saveAs(new Blob([buf]), fileName);
}
