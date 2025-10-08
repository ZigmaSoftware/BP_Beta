<?php
include '../../config/dbconfig.php';

$table = "tcs_kolkata_daily_log";
$columns = [
    "entry_date",
    "project_id",
    "waste_receive",
    "waste_crushing_feeding",
    "waste_handed_back_ccp",
    "water_liters",
    "feeding_ph",
    "digester_1_ph",
    "balloon_1_position",
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
  .redbg { background-color: #ffe7e7!important; }
  .bluebg { background-color: #e7f6ff!important; }
  .yellowbg { background-color: #fbffe7!important; }
  .greybg { background-color: #ededed!important; }
  .table th { font-size: 0.75rem; }
  .table td { font-size: 0.9rem; }

  /* hide header/footer in normal view */
  .print-header, .print-footer { display: none; }

 @media print {
  @page { size: A4 landscape; margin: 10mm; }
  body { -webkit-print-color-adjust: exact; print-color-adjust: exact; zoom: 80%; }
  table { width: 100% !important; border-collapse: collapse !important; }
  th, td { border: 1px solid #000 !important; padding: 4px; }

  /* Hide filters/buttons */
  .row, .btn-group, #from_date, #to_date, #filter_project_id, label {
    display: none !important;
  }

  /* Show header/footer */
  .print-header { display: block !important; }

  /* ✅ Stamp only once at the end */
  .print-footer {
    display: block !important;
    position: static !important;   /* no fixed */
    margin-top: 20px;
    text-align: right;
    page-break-before: avoid;
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
        <label for="filter_project_id" class="form-label labelright">Project Name</label></div>
            <div class="col-md-2">  <select id="filter_project_id" class="form-control">
            <option value="">All Projects</option>
            <?php
            $project_options  = get_project_by_type('cooking');  
            if ($project_options && is_array($project_options)) {
                foreach ($project_options as $proj) {
                    echo '<option value="'.$proj['unique_id'].'">'.$proj['label'].'</option>';

                }
            }
            ?>
        </select>
    </div>

            <div class="col-md-2"><button class="btn btn-primary" type="button" onclick="filterByDate()">GO</button></div>
    </div>
  </div>
  <div class="col-4 text-end">
    <div class="btn-group">
      <button class="btn btn-secondary" onclick="goBack()" style="margin-right:10px">Back</button>
      <button class="btn btn-primary" id="printBtn" style="margin-right:10px">Print</button>
      <button class="btn btn-success" onclick="exportKolkataLogToExcel()" style="margin-right:10px">Export to Excel</button>
    </div>
  </div>
</div>

<!-- Print Header -->
<div class="print-header">
  <table width="100%">
  <tr>
    <!-- ✅ Left Logo -->
    <td style="text-align:left; width:230px;">
      <img src="https://zigma.in/blue_planet_beta/folders/cooking_daily_log_sheet/newlogo.jpg" 
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
  <table id="kolkataReport" class="table table-bordered">
    <thead>
  <tr>
    <th rowspan="2" class="greybg">S.No</th>
    <th rowspan="2" class="greybg">Date</th>
    
    <!-- Waste Processed -->
    <th colspan="5" class="redbg">Waste Processed (Kgs)</th>
    
    <!-- Digester -->
    <th colspan="2" class="bluebg">Digester pH Readings</th>
    
    <!-- Gas -->
    <th colspan="1" class="yellowbg">Gas Productions</th>
    
    <th rowspan="2" class="greybg">Representatives</th>
    <th rowspan="2" class="greybg">Remarks</th>
  </tr>
  <tr>
    <th class="redbg">Waste Receive</th>
    <th class="redbg">Waste Crushing / Feeding (Kgs)</th>
    <th class="redbg">Waste Handed Back CCP</th>
    <th class="redbg">Water (Liters)</th>
    <th class="redbg">Feeding pH</th>
    
    <th colspan="2" class="bluebg">Digester-1 pH</th>

    
    <th class="yellowbg">Balloon-1 Position (%)</th>
  </tr>
</thead>

    <tbody>
      <?php $sno=1; foreach($data_rows as $row): ?>
      <tr data-project-id="<?= $row['project_id']; ?>">
    <td><?= $sno++; ?></td>
    <td><?= date("d-m-Y", strtotime($row['entry_date'])); ?></td>
    <td><?= $row['waste_receive']; ?></td>
    <td><?= $row['waste_crushing_feeding']; ?></td>
    <td><?= $row['waste_handed_back_ccp']; ?></td>
    <td><?= $row['water_liters']; ?></td>
    <td><?= $row['feeding_ph']; ?></td>
    <td colspan="2"><?= $row['digester_1_ph']; ?></td>
    <td><?= $row['balloon_1_position']; ?></td>
    <td><?= $row['created_by']; ?></td>
    <td><?= $row['remarks']; ?></td>
</tr>

      <?php endforeach; ?>
        <tr id="totalRow" class="total-row"></tr>

    </tbody>
    <!--<tfoot>-->
    <!--  <tr id="totalRow"></tr>-->
    <!--</tfoot>-->
  </table>
</div>

<!-- Print Footer -->
<div class="print-footer">
  <img src="https://zigma.in/blue_planet_beta/folders/cooking_daily_log_sheet/stamp_1.jpg" 
       alt="Footer Stamp" style="max-height: 120px;">
</div>
</div>
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
  const projectFilter = document.getElementById("filter_project_id").value;
  const rows = document.querySelectorAll("#kolkataReport tbody tr:not(#totalRow)");

  let totals = { wr:0, wf:0, wh:0, wl:0, fph:0, d1:0, b1:0 };
  let counts = { fph:0, d1:0, b1:0 };
  let sno = 1;

  rows.forEach(r => {
    const c = r.querySelectorAll("td");
    const [dd, mm, yy] = c[1].innerText.split("-");
    const d = `${yy}-${mm}-${dd}`;
    const rowProjectId = r.getAttribute("data-project-id");

    if ((!from || d >= from) && (!to || d <= to) && (!projectFilter || rowProjectId === projectFilter)) {
      r.style.display = "";
      c[0].innerText = sno++;
      totals.wr += +c[2].innerText||0;
      totals.wf += +c[3].innerText||0;
      totals.wh += +c[4].innerText||0;
      totals.wl += +c[5].innerText||0;
      if(+c[6].innerText){ totals.fph+=+c[6].innerText; counts.fph++; }
      if(+c[7].innerText){ totals.d1+=+c[7].innerText; counts.d1++; }
      if(+c[8].innerText){ totals.b1+=+c[8].innerText; counts.b1++; }
    } else { r.style.display="none"; }
  });

  document.getElementById("totalRow").innerHTML = `
    <td colspan="2"><b>Total/Avg</b></td>
    <td><b>${totals.wr.toFixed(2)}</b></td>
    <td><b>${totals.wf.toFixed(2)}</b></td>
    <td><b>${totals.wh.toFixed(2)}</b></td>
    <td><b>${totals.wl.toFixed(2)}</b></td>
    <td><b>${counts.fph?(totals.fph/counts.fph).toFixed(2):""}</b></td>
    <td colspan="2"><b>${counts.d1?(totals.d1/counts.d1).toFixed(2):""}</b></td>
    <td><b>${counts.b1?(totals.b1/counts.b1).toFixed(2):""}</b></td>
    <td colspan="2"></td>`;
}

document.getElementById("printBtn").addEventListener("click", () => window.print());

async function exportKolkataLogToExcel() {
  const workbook = new ExcelJS.Workbook();
  const ws = workbook.addWorksheet("Daily Log Sheet Report");

  const startRow = 6; // header starts (like Mandi module)

  // === 1) Left Logo in first two columns (newlogo.jpg) ===
try {
  const respLogo = await fetch('https://zigma.in/blue_planet_beta/folders/cooking_daily_log_sheet/newlogo.jpg');
  const blobLogo = await respLogo.blob();
  const dataUrlLogo = await new Promise(r => {
    const f = new FileReader();
    f.onload = () => r(f.result);
    f.readAsDataURL(blobLogo);
  });
  const base64Logo = dataUrlLogo.split(',')[1];
  const logoId = workbook.addImage({ base64: base64Logo, extension: 'png' });

  // Place logo in first two columns (A-B), rows 1-4
  ws.addImage(logoId, {
    tl: { col: 0, row: 0 },  // column A
    br: { col: 2, row: 4 }   // spans A-B
  });
} catch(e) {
  console.warn("Left logo load failed:", e);
}

// === 2) Company name & address in columns C-L (next to logo) ===
ws.mergeCells('C1:L1');
ws.getCell('C1').value = 'XEON WASTE MANAGERS PRIVATE LIMITED';
ws.getCell('C1').alignment = { vertical:'middle', horizontal:'center' };
ws.getCell('C1').font = { size:16, bold:true };

ws.mergeCells('C2:L2');
ws.getCell('C2').value = 'Kohinoor World Towers T3, Office No.306, Opp. Empire Estate, Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.';
ws.getCell('C2').alignment = { wrapText:true, vertical:'middle', horizontal:'left', indent: 2  }; // keep wrapText if needed
ws.getCell('C2').font = { size:12 };
ws.getRow(2).height = 25; // optional: more space for address


  // === 2) Logo ===
  try {
    const resp = await fetch('https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG');
    const blob = await resp.blob();
    const dataUrl = await new Promise(r => {
      const f = new FileReader();
      f.onload = () => r(f.result);
      f.readAsDataURL(blob);
    });
    const base64Data = dataUrl.split(',')[1];
    const imageId = workbook.addImage({ base64: base64Data, extension:'png' });
    
    ws.addImage(imageId, {
      tl: { col: 11, row: 0 },  // 10 = start at column 11 (Remarks)
      br: { col: 12, row: 4 }   // exclusive, so ends at col 11 (still inside Remarks)
    });
  } catch(e) { console.warn('Logo load failed:', e); }

  // === 3) Headers (2 rows) ===
  const headersRow1 = [
      'S.No','Date','Waste Processed','Waste Processed','Waste Processed','Waste Processed','Waste Processed',
      'Digester pH Readings','Gas Productions','Representatives','Remarks'
    ];
    
    const headersRow2 = [
      '', '', 'Waste Receive','Waste Crushing / Feeding','Waste Handed Back CCP','Water (Liters)','Feeding pH',
      'Digester-1 pH','Balloon-1 Position (%)','', ''
    ];

  ws.getRow(startRow).values = headersRow1;
  ws.getRow(startRow+1).values = headersRow2;

  ws.getRow(startRow).font = { bold:true };
  ws.getRow(startRow+1).font = { bold:true };
  ws.getRow(startRow).alignment = { vertical:'middle', horizontal:'center', wrapText:true };
  ws.getRow(startRow+1).alignment = { vertical:'middle', horizontal:'center', wrapText:true };

  // Merge top headers
  ws.mergeCells(startRow,1,startRow+1,1); // S.No
  ws.mergeCells(startRow,2,startRow+1,2); // Date
  ws.mergeCells(startRow,3,startRow,7);   // Waste Processed
  ws.mergeCells(startRow,8,startRow,8);   // Digester pH Readings
  ws.mergeCells(startRow,9,startRow,9); // Gas Productions
  ws.mergeCells(startRow,10,startRow+1,10); // Representatives
  ws.mergeCells(startRow,11,startRow+1,11); // Remarks

  // === 4) Body Rows ===
  const tableRows = document.querySelectorAll("#kolkataReport tbody tr");
  let excelRow = startRow+2;
  let sno = 1;
  let totals = { wr:0,wf:0,wh:0,wl:0,fph:0,d1:0,b1:0 };
  let counts = { fph:0,d1:0,b1:0 };
  const maxCols = 11;

  tableRows.forEach(tr => {
    if(tr.style.display==='none') return;
    const tds = Array.from(tr.querySelectorAll('td'));
    if(!tds.length) return;

    let d1=0, b1=0;
    if(tds[7]){
      const phValues = tds[7].innerText.split(',').map(v=>parseFloat(v.trim()));
      d1 = phValues[0]||0;
      b1 = phValues[1]||d1;
    }
   const rowData = [
      sno++,                     // we generate our own serial no
      tds[1]?.innerText.trim(),  // Date
      +tds[2]?.innerText || 0,   // Waste Receive
      +tds[3]?.innerText || 0,   // Waste Crushing / Feeding
      +tds[4]?.innerText || 0,   // Waste Handed Back CCP
      +tds[5]?.innerText || 0,   // Water (Liters)
      +tds[6]?.innerText || 0,   // Feeding pH
      +tds[7]?.innerText || 0,   // Digester-1 pH
      +tds[8]?.innerText || 0,   // Balloon-1 Position
      tds[9]?.innerText.trim() || '',  // ✅ Representative (text)
      tds[10]?.innerText.trim() || ''  // ✅ Remarks
    ];

    totals.wr += rowData[2]; totals.wf += rowData[3]; totals.wh += rowData[4];
    totals.wl += rowData[5]; totals.fph += rowData[6]; totals.d1 += rowData[7]; totals.b1 += rowData[8];
    counts.fph++; counts.d1++; counts.b1++;

    ws.getRow(excelRow).values = rowData;
    excelRow++;
  });

  // === 5) Total/Avg Row ===
  ws.getRow(excelRow).values = [
    'Total/Avg','',
    totals.wr.toFixed(2), totals.wf.toFixed(2), totals.wh.toFixed(2), totals.wl.toFixed(2),
    counts.fph?(totals.fph/counts.fph).toFixed(2):'',
    counts.d1?(totals.d1/counts.d1).toFixed(2):'',
    counts.b1?(totals.b1/counts.b1).toFixed(2):'',
    '', '', ''
  ];
  ws.getRow(excelRow).font = { bold:true };
  ws.getRow(excelRow).alignment = { vertical:'middle', horizontal:'center' };
  excelRow++;
  
  try {
  const resp2 = await fetch('https://zigma.in/blue_planet_beta/folders/cooking_daily_log_sheet/stamp_1.jpg');
  const blob2 = await resp2.blob();
  const dataUrl2 = await new Promise(r => {
    const f = new FileReader();
    f.onload = () => r(f.result);
    f.readAsDataURL(blob2);
  });
  const base64Data2 = dataUrl2.split(',')[1];
  const stampId = workbook.addImage({ base64: base64Data2, extension: 'jpg' });

  // Place stamp under "Remarks" column (last column)
  const remarksCol = 11; 
  const stampRowStart = excelRow + 1;
  const stampRowEnd   = excelRow + 5; // adjust height
  ws.addImage(stampId, {
    tl: { col: remarksCol - 1, row: stampRowStart - 1 }, // top-left (0-based index)
    br: { col: remarksCol, row: stampRowEnd }             // bottom-right (exclusive)
  });
} catch(e) {
  console.warn("Stamp logo load failed:", e);
}

  // === Column Widths ===
  ws.columns = [
    {width:6},{width:12},{width:14},{width:18},{width:16},{width:14},{width:12},
    {width:12},{width:14},{width:12},{width:20},{width:20}
  ];

  // === Borders ===
  for(let r=startRow; r<excelRow; r++){
    for(let c=1; c<=maxCols; c++){
      ws.getCell(r,c).border = { top:{style:'thin'}, left:{style:'thin'}, bottom:{style:'thin'}, right:{style:'thin'} };
    }
  }

  // === Save ===
  const buf = await workbook.xlsx.writeBuffer();
  saveAs(new Blob([buf]), 'daily_log_sheet_report.xlsx');
}


function goBack(){ window.location.href="index.php?file=cooking_daily_log_sheet/list"; }
</script>