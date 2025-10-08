<?php
include '../../config/dbconfig.php';

$table = "mandi_gobindgad_log";
$columns = [
    "entry_date",
    "project_id",  
    "waste_received",
    "waste_reject",
    "feed_to_digester",
    "black_water_liters",
    "water_liters",
    "feeding_ph",
    "outlet_ph",
    "flowmeter_start",
    "flowmeter_stop",
    "genset_start_hrs",
    "genset_stop_hrs",
    "start_kwh",
    "stop_kwh",
    "(SELECT user_name FROM user WHERE user.staff_unique_id = ".$table.".created_by LIMIT 1) AS created_by",
    "remarks"
];

$table_details = [$table, $columns];
$where = "is_delete = 0 ORDER BY entry_date ASC";

$result = $pdo->select($table_details, $where);
$data_rows = $result->status ? $result->data : [];
?>

<style>
  th, td { text-align: center; vertical-align: middle; }
  .redbg { background-color: #ffe7e7; }
  .bluebg { background-color: #e7f6ff; }
  .yellowbg { background-color: #fbffe7; }
  .greybg { background-color: #ededed; }
    .purplebg { background-color: #f7effe; }

  .table th { font-size: 0.75rem; }
  .table td { font-size: 0.9rem; }

  /* ✅ hide header/footer in normal view */
  .print-header, .print-footer { display: none; }

  @media print {
    @page {
      size: A4 landscape;
      margin: 10mm;
    }

    body {
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
      zoom: 66%;
    }

    table {
      width: 100% !important;
      border-collapse: collapse !important;
    }

    th, td {
      border: 1px solid #000 !important;
      padding: 4px;
      text-align: center;
      vertical-align: middle;
    }

    /* ✅ Hide filters & buttons */
    .row, 
    .btn-group, 
    #from_date, 
    #to_date, 
    #filter_project_id, 
    label {
      display: none !important;
    }

    /* ✅ Show header/footer only in print */
    .print-header {
      display: block !important;
      text-align: center;
      margin-bottom: 10px;
    }

    .print-footer {
      display: block !important;
      position: fixed;
      bottom: 0;
      right: 0;
      text-align: right;
      padding-right: 20px;
    }
  }
</style>
   <div class="card">  <div class="card-body">
<div class="row">
  <div class="col-8">
    <div class="form-group row">
      <label class="col-md-1 col-form-label">From</label>
      <div class="col-md-2"><input type="date" id="from_date" class="form-control"></div>
      <label class="col-md-1 col-form-label">To</label>
      <div class="col-md-2"><input type="date" id="to_date" class="form-control"></div>
      <div class="col-md-2">
        <label for="filter_project_id" class="form-label labelright">Project Name</label>
      </div>
      <div class="col-md-2">  
        <select id="filter_project_id" class="form-control">
          <option value="">All Projects</option>
          <?php
          $project_options  = get_project_by_type('generation');  
          if ($project_options && is_array($project_options)) {
              foreach ($project_options as $proj) {
                  echo '<option value="'.$proj['unique_id'].'">'.$proj['label'].'</option>';
              }
          }
          ?>
        </select>
      </div>
      <div class="col-md-2">
        <button class="btn btn-primary" type="button" onclick="filterByDate()">GO</button>
      </div>
    </div>
  </div>
  <div class="col-4 text-end">
    <div class="btn-group">
      <button class="btn btn-secondary" onclick="goBack()" style="margin-right:10px">Back</button>
      <button class="btn btn-primary" id="printBtn" style="margin-right:10px">Print</button>
      <button class="btn btn-success" onclick="exportMandiLogToExcel()" style="margin-right:10px">Export to Excel</button>
    </div>
  </div>
</div>

<!-- ✅ Print Header (only visible in print) -->
<div class="print-header">
  <table width="100%">
  <tr>
    <!-- ✅ Left Logo -->
    <td style="text-align:left; width:230px;">
      <img src="https://zigma.in/blue_planet_beta/folders/generation_daily_log_sheet/newlogo.jpg" 
           alt="Left Logo" style="max-height: 100px; padding:10px">
    </td>

    <!-- ✅ Center Title & Address -->
    <td style="text-align:center;">
      <p style="font-size:34px;color:#444;margin-bottom:0px">
        <b>XEON WASTE MANAGERS PRIVATE LIMITED</b>
      </p>
      <p style="font-size:16px;margin-top:2px">
        Kohinoor World Towers T3, Office No.306, Opp. Empire Estate, Old Mumbai-Pune Hwy, 
        Pimpri Colony, Pune Maharashtra-411018.
      </p>
    </td>

    <!-- ✅ Right Logo -->
    <td style="text-align:right; width:230px;">
      <img src="https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG" 
           alt="Right Logo" style="max-height: 100px; padding:10px">
    </td>
  </tr>
</table>
</div>

<div id="reportTableWrapper">
  <table id="mandiReport" class="table table-bordered">
    <thead>
      <tr>
        <th rowspan="2" class="greybg">S.No</th>
        <th rowspan="2" class="greybg">Date</th>
        
        <!-- Waste Processing -->
        <th colspan="5" class="redbg">Waste Processing</th>

        <!-- pH Values -->
        <th colspan="2" class="bluebg">pH Values</th>

        <!-- Biogas -->
        <th colspan="3" class="yellowbg">Biogas Production (m³)</th>

        <!-- Gen Set -->
        <th colspan="6" class="purplebg">Gen Set Reading Details</th>

        <!-- Manpower -->
        <th colspan="2" class="greybg">Manpower Details</th>
      </tr>
      <tr>
        <th class="redbg">Waste Received</th>
        <th class="redbg">Waste Reject</th>
        <th class="redbg">Feed to Digester</th>
        <th class="redbg">Black Water (Liters)</th>
        <th class="redbg">Water (Liters)</th>
        <th class="bluebg">Feeding pH</th>
        <th class="bluebg">Outlet pH</th>
        <th class="yellowbg">Flowmeter Start</th>
        <th class="yellowbg">Flowmeter Stop</th>
        <th class="yellowbg"> Total Reading</th>
        <th class="purplebg">Start Hrs</th>
        <th class="purplebg">Stop Hrs</th>
        <th class="purplebg">Total Hrs</th>
        <th class="purplebg">Start KWH</th>
        <th class="purplebg">Stop KWH</th>
        <th class="purplebg">Total KWH</th>
        <th class="greybg">Operator Name</th>
        <th class="greybg">Remarks</th>
      </tr>
    </thead>
    <tbody>
      <?php $sno=1; foreach($data_rows as $row): ?>
      <?php
        $total_reading = $row['flowmeter_start'] - $row['flowmeter_stop'];
        $total_hrs     = $row['genset_stop_hrs'] - $row['genset_start_hrs'];
        $total_kwh     = $row['stop_kwh'] - $row['start_kwh'];
      ?>
      <tr data-project-id="<?= $row['project_id']; ?>">
        <td><?= $sno++; ?></td>
        <td><?= date("d-m-Y", strtotime($row['entry_date'])); ?></td>
        <td><?= $row['waste_received']; ?></td>
        <td><?= $row['waste_reject']; ?></td>
        <td><?= $row['feed_to_digester']; ?></td>
        <td><?= $row['black_water_liters']; ?></td>
        <td><?= $row['water_liters']; ?></td>
        <td><?= $row['feeding_ph']; ?></td>
        <td><?= $row['outlet_ph']; ?></td>
        <td><?= $row['flowmeter_start']; ?></td>
        <td><?= $row['flowmeter_stop']; ?></td>
        <td><?= abs($total_reading); ?></td>
        <td><?= $row['genset_start_hrs']; ?></td>
        <td><?= $row['genset_stop_hrs']; ?></td>
        <td><?= abs($total_hrs); ?></td>
        <td><?= $row['start_kwh']; ?></td>
        <td><?= $row['stop_kwh']; ?></td>
        <td><?= abs($total_kwh); ?></td>
        <td><?= $row['created_by']; ?></td>
        <td><?= $row['remarks']; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
    <tfoot>
      <tr id="totalRow"></tr>
    </tfoot>
  </table>
</div>
</div></div>
<!-- ✅ Print Footer (only visible in print) -->
<div class="print-footer">
  <img src="https://zigma.in/blue_planet_beta/folders/generation_daily_log_sheet/stamp_1.jpg" 
       alt="Footer Stamp" style="max-height: 120px;">
</div>

<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const now = new Date();

  // First day of current month (local)
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
  // Last day of current month (local)
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

  // Helper to format as YYYY-MM-DD in local time
  function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
  }

  document.getElementById("from_date").value = formatDate(firstDay);
  document.getElementById("to_date").value = formatDate(lastDay);

  filterByDate();
});


function filterByDate() {
  const from = document.getElementById("from_date").value;
  const to   = document.getElementById("to_date").value;
  const projectFilter = document.getElementById("filter_project_id").value; // ✅ new line
  const rows = document.querySelectorAll("#mandiReport tbody tr");

  let totals = { wr:0, rej:0, fd:0, bw:0, wl:0, fph:0, oph:0, fr:0, hrs:0, kwh:0 };
  let counts = { fph:0, oph:0 };
  let sno = 1;

  rows.forEach(r => {
    const c = r.querySelectorAll("td");
    const [dd,mm,yy] = c[1].innerText.split("-");
    const d = new Date(`${yy}-${mm}-${dd}`);
    const fromDate = from ? new Date(from) : null;
    const toDate   = to   ? new Date(to)   : null;

    const rowProjectId = r.getAttribute("data-project-id"); // ✅ new line
    
    if ((!fromDate || d >= fromDate) && 
        (!toDate || d <= toDate) &&
        (!projectFilter || rowProjectId === projectFilter)) { // ✅ added project check
      r.style.display = "";
      c[0].innerText = sno++;
      totals.wr  += +c[2].innerText||0;
      totals.rej += +c[3].innerText||0;
      totals.fd  += +c[4].innerText||0;
      totals.bw  += +c[5].innerText||0;
      totals.wl  += +c[6].innerText||0;

      if(+c[7].innerText){ totals.fph+=+c[7].innerText; counts.fph++; }
      if(+c[8].innerText){ totals.oph+=+c[8].innerText; counts.oph++; }

      totals.fr  += +c[11].innerText||0;
      totals.hrs += +c[14].innerText||0;
      totals.kwh += +c[17].innerText||0;
    } else {
      r.style.display="none";
    }
  });

  document.getElementById("totalRow").innerHTML = `
    <td colspan="2"><b>Total/Avg</b></td>
    <td><b>${totals.wr.toFixed(2)}</b></td>
    <td><b>${totals.rej.toFixed(2)}</b></td>
    <td><b>${totals.fd.toFixed(2)}</b></td>
    <td><b>${totals.bw.toFixed(2)}</b></td>
    <td><b>${totals.wl.toFixed(2)}</b></td>
    <td><b>${counts.fph?(totals.fph/counts.fph).toFixed(2):""}</b></td>
    <td><b>${counts.oph?(totals.oph/counts.oph).toFixed(2):""}</b></td>
    <td colspan="2"></td>
    <td><b>${totals.fr.toFixed(2)}</b></td>
    <td colspan="2"></td>
    <td><b>${totals.hrs.toFixed(2)}</b></td>
    <td colspan="2"></td>
    <td><b>${totals.kwh.toFixed(2)}</b></td>
    <td colspan="2"></td>`;
}

document.getElementById("printBtn").addEventListener("click", function () {
  window.print();
});


async function exportMandiLogToExcel() {
  const workbook = new ExcelJS.Workbook();
  const ws = workbook.addWorksheet("Mandi Log");

  const startRow = 6; // header starts

  // === 1) Title & Address ===
  ws.mergeCells('A1:T1');
  ws.getCell('A1').value = 'XEON WASTE MANAGERS PRIVATE LIMITED';
  ws.getCell('A1').alignment = { vertical: 'middle', horizontal: 'center' };
  ws.getCell('A1').font = { size: 16, bold: true };

  ws.mergeCells('A2:T2');
  ws.getCell('A2').value =
    'Kohinoor World Towers T3, Office No.306, Opp. Empire Estate, Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.';
  ws.getCell('A2').alignment = { wrapText: true, vertical: 'middle', horizontal: 'center' };
  ws.getCell('A2').font = { size: 12 };

       // === 2) Logo ===
    try {
      const resp = await fetch('https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG');
      const blob = await resp.blob();
      const dataUrl = await new Promise(r => {
        const f = new FileReader();
        f.onload = () => r(f.result);
        f.readAsDataURL(blob);
      });
    
      // Strip the prefix "data:image/png;base64,"
      const base64Data = dataUrl.split(',')[1];
    
      const imageId = workbook.addImage({ base64: base64Data, extension: 'png' });
    
      ws.addImage(imageId, {
        tl: { col: 18, row: 0 }, // start at col 19
        br: { col: 20, row: 5 }  // end at col 20
      });
    } catch (e) {
      console.warn('Logo load failed:', e);
    }
    
    
// === 2a) Left Logo (newlogo.jpg) ===
try {
  const respLeft = await fetch('https://zigma.in/blue_planet_beta/folders/generation_daily_log_sheet/newlogo.jpg');
  const blobLeft = await respLeft.blob();
  const dataUrlLeft = await new Promise(r => {
    const f = new FileReader();
    f.onload = () => r(f.result);
    f.readAsDataURL(blobLeft);
  });
  const base64DataLeft = dataUrlLeft.split(',')[1];
  const leftLogoId = workbook.addImage({ base64: base64DataLeft, extension: 'jpg' });

  ws.addImage(leftLogoId, {
    tl: { col: 0, row: 0 },    // start at column A (0-based)
    br: { col: 2, row: 5 }     // spans first 2 columns, adjust width if needed
  });
} catch(e){ console.warn("Left logo load failed:", e); }


  // === 3) Headers (two rows like HTML) ===
  const headersRow1 = [
    'S.No', 'Date',
    'Waste Processing', 'Waste Processing', 'Waste Processing', 'Waste Processing', 'Waste Processing',
    'pH Values', 'pH Values',
    'Biogas Production (m³)', 'Biogas Production (m³)', 'Biogas Production (m³)',
    'Gen Set Reading Details', 'Gen Set Reading Details', 'Gen Set Reading Details', 'Gen Set Reading Details', 'Gen Set Reading Details', 'Gen Set Reading Details',
    'Manpower Details', 'Manpower Details'
  ];

  const headersRow2 = [
    '', // S.No
    '', // Date
    'Waste Received', 'Waste Reject', 'Feed to Digester', 'Black Water (Liters)', 'Water (Liters)',
    'Feeding pH', 'Outlet pH',
    'Flowmeter Start', 'Flowmeter Stop', 'Total Reading',
    'Start Hrs', 'Stop Hrs', 'Total Hrs',
    'Start KWH', 'Stop KWH', 'Total KWH',
    'Operator Name', 'Remarks'
  ];

  ws.getRow(startRow).values = headersRow1;
  ws.getRow(startRow + 1).values = headersRow2;

  ws.getRow(startRow).font = { bold: true };
  ws.getRow(startRow + 1).font = { bold: true };
  ws.getRow(startRow).alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };
  ws.getRow(startRow + 1).alignment = { vertical: 'middle', horizontal: 'center', wrapText: true };

  // Merge top headers
  ws.mergeCells(startRow, 1, startRow + 1, 1); // S.No
  ws.mergeCells(startRow, 2, startRow + 1, 2); // Date
  ws.mergeCells(startRow, 3, startRow, 7);     // Waste Processing
  ws.mergeCells(startRow, 8, startRow, 9);     // pH Values
  ws.mergeCells(startRow, 10, startRow, 12);   // Biogas Production
  ws.mergeCells(startRow, 13, startRow, 18);   // Gen Set Reading
  ws.mergeCells(startRow, 19, startRow, 20);   // Manpower

  // Column widths
  ws.columns = [
    { width: 6 }, { width: 12 },
    { width: 14 }, { width: 14 }, { width: 16 }, { width: 18 }, { width: 18 },
    { width: 12 }, { width: 12 },
    { width: 16 }, { width: 16 }, { width: 16 },
    { width: 12 }, { width: 12 }, { width: 12 },
    { width: 12 }, { width: 12 }, { width: 12 },
    { width: 20 }, { width: 25 }
  ];

  // === 4) Body Rows ===
  const table = document.getElementById('mandiReport');
  const bodyRows = table.querySelectorAll('tbody tr');
  let excelRow = startRow + 2;
  let sno = 1;
  const maxCols = 20;

  bodyRows.forEach((tr) => {
    if (tr.style.display === 'none') return;
    const tds = tr.querySelectorAll('td');
    if (!tds.length) return;

    let rowData = Array.from(tds).map(td => td.innerText.trim());
    rowData[0] = String(sno); // overwrite S.No

    if (rowData.length > maxCols) rowData = rowData.slice(0, maxCols);
    while (rowData.length < maxCols) rowData.push('');

    ws.getRow(excelRow).values = rowData;
    excelRow++;
    sno++;
  });

 // === Add Total/Avg Row ===
const totalRow = document.querySelector("#totalRow");
if (totalRow) {
  const tds = totalRow.querySelectorAll("td");
  if (tds.length) {
    let totalData = [];
    tds.forEach((td, index) => {
      const colspan = parseInt(td.getAttribute("colspan") || 1, 10);
      let text = td.innerText.trim();

      if (index === 0) {
        // First cell: put text only in first column, blank for remaining colspan
        totalData.push(text);
        for (let i = 1; i < colspan; i++) totalData.push('');
      } else {
        // Other cells: just push the value, repeated if colspan>1
        for (let i = 0; i < colspan; i++) totalData.push(text);
      }
    });

    // Ensure exact column count
    while (totalData.length < maxCols) totalData.push('');
    if (totalData.length > maxCols) totalData = totalData.slice(0, maxCols);

    // Add row to worksheet
    ws.getRow(excelRow).values = totalData;
    ws.getRow(excelRow).font = { bold: true };
    ws.getRow(excelRow).alignment = { horizontal: "center", vertical: "middle" };
    ws.getRow(excelRow).eachCell(cell => {
      cell.fill = {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'E0E0E0' }
      };
    });
    excelRow++;
  }
}
// === 5) Stamp Logo (after totals row) ===
try {
  const resp2 = await fetch('https://zigma.in/blue_planet_beta/folders/generation_daily_log_sheet/stamp_1.jpg');
  const blob2 = await resp2.blob();
  const dataUrl2 = await new Promise(r => {
    const f = new FileReader();
    f.onload = () => r(f.result);
    f.readAsDataURL(blob2);
  });

  const base64Data2 = dataUrl2.split(',')[1];
  const stampId = workbook.addImage({ base64: base64Data2, extension: 'jpeg' });

  ws.addImage(stampId, {
  tl: { col: 18, row: excelRow + 2 }, // column S (19th)
  br: { col: 20, row: excelRow + 8 }  // column T (20th)
});

} catch (e) {
  console.warn("Stamp logo load failed:", e);
}



  // === 5) Borders ===
  for (let r = startRow; r < excelRow; r++) {
    for (let c = 1; c <= maxCols; c++) {
      ws.getCell(r, c).border = {
        top: { style: 'thin' }, left: { style: 'thin' },
        bottom: { style: 'thin' }, right: { style: 'thin' }
      };
    }
  }

  // === 6) Save ===
  const buf = await workbook.xlsx.writeBuffer();
  saveAs(new Blob([buf]), 'daily_log_sheet_report.xlsx');
}
function goBack(){ 
  window.location.href="index.php?file=generation_daily_log_sheet/list"; 
}
</script>
