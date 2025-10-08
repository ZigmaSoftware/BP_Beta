
<?php
include '../../config/dbconfig.php';

$table = "date_wise_feed_rejection";
$columns = [
    "entry_date",
    "total_net_weight",
    "feeding_quantity",
    "rejection_quantity",
    "water_liters",
    "feeding_ph",
    "digester_1_ph",
    "digester_2_ph",
    "digester_3_ph",
    "digester_4_ph",
    "balloon_1_position",
    "balloon_2_position",
    "CBGProduction",
    "CBGFlare",
    "CT_VehicleConsumption",
    "CBGSold",
    "CBGStock",
    "ManureProduction",
    "ManureDisposed",
    "ManureSold",
    "ManureStock",
    "(SELECT staff_name FROM staff WHERE staff.unique_id = ".$table.".created_by ) AS created_by",
    "remarks"
];

$table_details = [$table, $columns];
$where = "is_delete = 0 ORDER BY entry_date ASC";

$result = $pdo->select($table_details, $where);
$data_rows = $result->status ? $result->data : [];
?>




  <style>
    .btn-group {
      margin-bottom: 15px;
    }
    th, td {
      text-align: center;
      vertical-align: middle;
    }
    .redbg{    background-color: #ffe7e7;}
    .bluebg{    background-color: #e7f6ff;}
    .greenbg{    background-color: #e7fff0;}
    .yellowbg{    background-color: #fbffe7;}
    .greybg{    background-color: #ededed;}
    table#digesterReport tr th {
    vertical-align: middle;
}
    table#digesterReport tr td {
    vertical-align: middle;
}
table{    background-color: #fff;}
 .table th{
    padding: 0.25rem !important;
    padding-bottom: 0.40rem !important;
    font-size: 0.7rem;    color: #333;}
     .table td{
    font-size: 0.9rem;    color: #333;}
    .table thead th {
    border-bottom: 1px solid #c4c4c4;
    border-top: 1px solid #c4c4c4;
    border-right: 1px solid #c4c4c4;    border-left: 1px solid #c4c4c4
}
.table-bordered td, .table-bordered th {
    border: 1px solid #c4c4c4;
}
  </style>
  <script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>

</head>
<body class="">

<div class="row">
   <div class="col-8" >
      <div class="form-group row">
         <label class="col-md-1 col-form-label">From</label>
        <div class="col-md-2">
          <input type="date" id="from_date" class="form-control">
        </div>
        
        <label class="col-md-1 col-form-label">To</label>
        <div class="col-md-2">
          <input type="date" id="to_date" class="form-control">
        </div>
        
        <button class="btn btn-primary" type="button" onclick="filterByDate()">GO</button>
      </div>
   </div>
   <div class="col-4" style="text-align:right;">
      <div class="btn-group">
         <button class="btn btn-secondary" onclick="goBack()" style="margin-right:10px;">Back</button>
         <button class="btn btn-primary" id="printBtn" style="margin-right:10px">️️ Print</button>
         <button class="btn btn-success" onclick="exportTableToExcel()"> Export to Excel</button>
      </div>
   </div>
</div>

  <div id="reportTableWrapper">
      <div class="">
    <table id="digesterReport" class="table table-bordered" width="100%">
      <thead>
        <tr>
          <th rowspan="2" class="greybg">S.No</th>
          <th rowspan="2" class="greybg">Date</th>
          <th colspan="5" class="redbg">Waste Processed (Kgs)</th>
          <th colspan="4" class="bluebg">Digester pH Readings</th>
          <th colspan="2" class="yellowbg">Gas Productions</th>
          <th colspan="5" class="greenbg">CBG</th>
          <th colspan="4" class="bluebg">Manure</th>
          <th rowspan="2" class="greybg">Representatives</th>
          <th rowspan="2" class="greybg">Remarks</th>
        </tr>
        <tr>
          <th class="redbg">Waste Receive</th>
          <th class="redbg">Waste Crushing / <br/>Feeding in (Kgs)</th>
          <th class="redbg">Waste Handed Back CCP</th>
          <th class="redbg">Water (Liters)</th>
          <th class="redbg">Feeding pH</th>
          <th class="bluebg">Digester-1 pH</th>
          <th class="bluebg">Digester-2 pH</th>
          <th class="bluebg">Digester-3 pH</th>
          <th class="bluebg">Digester-4 pH</th>
          <th class="yellowbg">Balloon-1 Position (%)</th>
          <th class="yellowbg">Balloon-2 Position (%)</th>
          <th class="greenbg">CBG Production</th>
          <th class="greenbg">CBG Flare</th>
          <th class="greenbg">C&T Vehicle <br/>Consumption</th>
          <th class="greenbg">CBG Sold</th>
          <th class="greenbg">CBG Stock</th>
          <th class="bluebg">Manure Production</th>
          <th class="bluebg">Manure Disposed</th>
          <th class="bluebg">Manure Sold</th>
          <th class="bluebg">Manure Stock</th>
        </tr>
      </thead>
     <tbody>
<?php
$sno = 1;
$total_feeding_quantity = 0;

foreach ($data_rows as $row):
  $total_feeding_quantity += floatval($row['feeding_quantity']);
?>
<tr>
  <td><?= $sno++; ?></td>
  <td width="100px"><?= date("d-m-Y", strtotime($row['entry_date'])); ?></td>
  <td><?= $row['total_net_weight']; ?></td>
  <td><?= $row['feeding_quantity']; ?></td>
  <td><?= $row['rejection_quantity']; ?></td>
  <td><?= $row['water_liters']; ?></td>
  <td><?= $row['feeding_ph']; ?></td>
  <td><?= $row['digester_1_ph']; ?></td>
  <td><?= $row['digester_2_ph']; ?></td>
  <td><?= $row['digester_3_ph']; ?></td>
  <td><?= $row['digester_4_ph']; ?></td>
  <td><?= $row['balloon_1_position']; ?></td>
  <td><?= $row['balloon_2_position']; ?></td>
  <td><?= $row['CBGProduction']; ?></td>
  <td><?= $row['CBGFlare']; ?></td>
  <td><?= $row['CT_VehicleConsumption']; ?></td>
  <td><?= $row['CBGSold']; ?></td>
  <td><?= $row['CBGStock']; ?></td>
  <td><?= $row['ManureProduction']; ?></td>
  <td><?= $row['ManureDisposed']; ?></td>
  <td><?= $row['ManureSold']; ?></td>
  <td><?= $row['ManureStock']; ?></td>
  <td><?= $row['created_by']; ?></td>
  <td><?= $row['remarks']; ?></td>
</tr>
<?php endforeach; ?>
</tbody>

<tfoot>
  <tr id="totalRow">
    <td colspan="2" style="text-align:center"><strong>Total</strong></td>
    <td id="totalWasteReceived"><strong>0.00</strong></td>
    <td id="totalFeedingQty"><strong>0.00</strong></td>
    <td id="totalRejection"><strong>0.00</strong></td>
    <td id="totalWater"><strong>0.00</strong></td>
   <td id="totalFeedingPh"><strong>0.00</strong></td>
    <td id="totalDigester1Ph"><strong>0.00</strong></td>
    <td id="totalDigester2Ph"><strong>0.00</strong></td>
    <td id="totalDigester3Ph"><strong>0.00</strong></td>
    <td id="totalDigester4Ph"><strong>0.00</strong></td>


    <td id="totalBalloon1"><strong>0.00</strong></td>
    <td id="totalBalloon2"><strong>0.00</strong></td>
    <td id="totalCBGProduction"><strong>0.00</strong></td>
    <td id="totalCBGFlare"><strong>0.00</strong></td>
    <td id="totalCTConsumption"><strong>0.00</strong></td>
    <td id="totalCBGSold"><strong>0.00</strong></td>
    <td id="totalCBGStock"><strong>0.00</strong></td>
    <td id="totalManureProd"><strong>0.00</strong></td>
    <td id="totalManureDisposed"><strong>0.00</strong></td>
    <td id="totalManureSold"><strong>0.00</strong></td>
    <td id="totalManureStock"><strong>0.00</strong></td>
    <td colspan="2"></td> <!-- created_by + remarks -->
  </tr>
</tfoot>



    </table>
</div>
  </div>

  <script>
      document.addEventListener("DOMContentLoaded", function () {
        const today = new Date();
        const yyyy_mm_dd = d => d.toISOString().split('T')[0];
        
        document.getElementById("from_date").value = yyyy_mm_dd(today);
        document.getElementById("to_date").value = yyyy_mm_dd(today);
        
        filterByDate(); // Auto filter on load

    });

  // Wait for DOM before attaching print event
// Wait for DOM before attaching print event
$(document).ready(function () {
  $('#printBtn').on('click', function () {
    $('#reportTableWrapper').printThis({
      importCSS: true,
    importStyle: true,
    pageTitle: "Daily Log Sheet Report",
    loadCSS: "", // if you have an external CSS, you can specify it here
    header: `
      <style>
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
        }
      </style>
        <table style="width:100%;">
          <tr>
            <td style="text-align:center;">
              <p style="font-size:34px;color:#555;margin-bottom:0px"><b>BLUE PLANET BIOFUELS PVT. LTD.</b></p
              <p style="font-size:16px;color:#555;">Kohinoor World Towers T3, Office No.306, Opp. Empire Estate, Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.</p>
            </td>
            <td style="text-align:right; width: 230px;">
              <img src="https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG" alt="Logo" style="max-height: 100px;padding:10px">
            </td>
          </tr>
        </table>
      `,
      footer: `
        <div style="text-align:right; padding-top:0px;padding-right:20px">
          <img src="https://zigma.in/blue_planet/folders/daily_log_sheet/stamp.jpg" alt="Footer Logo" style="max-height: 120px;">
          
        </div>
      `
    });
  });
});




    // Excel export function
async function exportTableToExcel() {
  const workbook = new ExcelJS.Workbook();
  const ws = workbook.addWorksheet("Report");
  const startRow = 6; // header rows at 6-7, data starts at 8

  // ===== Title & Address (centered). Leave X for logo =====
  ws.mergeCells('A1:W1');
  ws.getCell('A1').value = 'BLUE PLANET BIOFUELS PVT. LTD.';
  ws.getCell('A1').alignment = { vertical: 'middle', horizontal: 'center' };
  ws.getCell('A1').font = { size: 16, bold: true };

  ws.mergeCells('A2:W2');
  ws.getCell('A2').value =
    'Kohinoor World Towers T3, Office No.306, Opp. Empire Estate, Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.';
  ws.getCell('A2').alignment = { wrapText: true, vertical: 'middle', horizontal: 'center' };
  ws.getCell('A2').font = { size: 12 };

  // ===== Logo in X1:X5 =====
  try {
    const resp = await fetch('https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG');
    const blob = await resp.blob();
    const dataUrl = await new Promise(r => { const f=new FileReader(); f.onload=()=>r(f.result); f.readAsDataURL(blob); });
    const imageId = workbook.addImage({ base64: dataUrl, extension: 'png' });
    ws.addImage(imageId, 'X1:X5');
  } catch(e) { console.warn('Logo load failed:', e); }

  // ===== Two-row header (A..X = 24 cols) =====
  const row1 = [
    'S.No', 'Date',
    'Waste Processed (Kgs)', '', '', '', '',                 // C..G
    'Digester pH Readings', '', '', '',                      // H..K
    'Gas Productions', '',                                   // L..M
    'CBG', '', '', '', '',                                   // N..R
    'Manure', '', '', '',                                    // S..V
    'Representatives', 'Remarks'                             // W, X
  ];
  const row2 = [
    '', '',                                                  // A, B
    'Waste Receive', 'Waste Crushing / Feeding in (Kgs)', 'Waste Handed Back CCP', 'Water (Liters)', 'Feeding pH', // C..G
    'Digester-1 pH', 'Digester-2 pH', 'Digester-3 pH', 'Digester-4 pH',                                             // H..K
    'Balloon-1 Position (%)', 'Balloon-2 Position (%)',                                                              // L..M
    'CBG Production', 'CBG Flare', 'C&T Vehicle Consumption', 'CBG Sold', 'CBG Stock',                               // N..R
    'Manure Production', 'Manure Disposed', 'Manure Sold', 'Manure Stock',                                           // S..V
    '', '' // W, X
  ];

  ws.getRow(startRow).values = row1;
  ws.getRow(startRow + 1).values = row2;

  // Merges to mirror HTML
  ws.mergeCells(startRow,1, startRow+1,1);    // A  S.No
  ws.mergeCells(startRow,2, startRow+1,2);    // B  Date
  ws.mergeCells(startRow,3, startRow,7);      // C..G Waste Processed
  ws.mergeCells(startRow,8, startRow,11);     // H..K Digester pH
  ws.mergeCells(startRow,12, startRow,13);    // L..M Gas Productions
  ws.mergeCells(startRow,14, startRow,18);    // N..R CBG
  ws.mergeCells(startRow,19, startRow,22);    // S..V Manure
  ws.mergeCells(startRow,23, startRow+1,23);  // W  Representatives
  ws.mergeCells(startRow,24, startRow+1,24);  // X  Remarks

  for (let r=startRow; r<=startRow+1; r++){
    ws.getRow(r).font = { bold: true };
    ws.getRow(r).alignment = { vertical:'middle', horizontal:'center', wrapText:true };
  }

  // Column widths (A..X = 24)
  ws.columns = [
    {width:6},  {width:12}, // A,B
    {width:14}, {width:22}, {width:18}, {width:14}, {width:12}, // C..G
    {width:12}, {width:12}, {width:12}, {width:12},             // H..K
    {width:16}, {width:16},                                     // L..M
    {width:16}, {width:14}, {width:18}, {width:14}, {width:14}, // N..R
    {width:16}, {width:16}, {width:16}, {width:16},             // S..V
    {width:18}, {width:50}                                      // W,X
  ];

  // ===== Body (only visible rows) + totals/avg =====
  const table = document.getElementById('digesterReport');
  const bodyRows = table.querySelectorAll('tbody tr');
  let excelRow = startRow + 2;

  // Totals/avg trackers mapped to Excel columns (1..24). We'll use 3..22 for numeric.
  const totals = Array(25).fill(0);   // 1-based
  const counts = Array(25).fill(0);   // for avg cols

  const add = (col, txt, isAvg=false) => {
    const v = parseFloat(String(txt).replace(/,/g,'').trim());
    if (!isNaN(v)) {
      totals[col] += v;
      if (isAvg) counts[col]++;
      return v;
    }
    return null;
  };

  bodyRows.forEach(tr => {
    if (tr.style.display === 'none') return; // respect filter
    const td = tr.querySelectorAll('td'); if (!td.length) return;

    // Build row matching columns A..X
    const r = [
      td[0].innerText.trim(),                 // 1  A  S.No
      td[1].innerText.trim(),                 // 2  B  Date
      add(3,  td[2].innerText),               // 3  C  Waste Receive           (TOTAL)
      add(4,  td[3].innerText),               // 4  D  Feeding Qty             (TOTAL)
      add(5,  td[4].innerText),               // 5  E  Waste Handed Back CCP   (TOTAL)
      add(6,  td[5].innerText),               // 6  F  Water (Liters)          (TOTAL)
      add(7,  td[6].innerText, true),         // 7  G  Feeding pH              (AVG)
      add(8,  td[7].innerText, true),         // 8  H  D1                      (AVG)
      add(9,  td[8].innerText, true),         // 9  I  D2                      (AVG)
      add(10, td[9].innerText, true),         // 10 J  D3                      (AVG)
      add(11, td[10].innerText, true),        // 11 K  D4                      (AVG)
      add(12, td[11].innerText, true),        // 12 L  Balloon-1               (AVG)
      add(13, td[12].innerText, true),        // 13 M  Balloon-2               (AVG)
      add(14, td[13].innerText),              // 14 N  CBG Production          (TOTAL)
      add(15, td[14].innerText),              // 15 O  CBG Flare               (TOTAL)
      add(16, td[15].innerText),              // 16 P  C&T Vehicle Cons.       (TOTAL)
      add(17, td[16].innerText),              // 17 Q  CBG Sold                (TOTAL)
      add(18, td[17].innerText),              // 18 R  CBG Stock               (TOTAL)
      add(19, td[18].innerText),              // 19 S  Manure Production       (TOTAL)
      add(20, td[19].innerText),              // 20 T  Manure Disposed         (TOTAL)
      add(21, td[20].innerText),              // 21 U  Manure Sold             (TOTAL)
      add(22, td[21].innerText),              // 22 V  Manure Stock            (TOTAL)
      td[22].innerText.trim(),                // 23 W  Representatives
      td[23].innerText.trim()                 // 24 X  Remarks
    ];

    ws.getRow(excelRow).values = r;

    // Number formats & alignment for numeric cols C..V
    for (let c=3; c<=22; c++){
      const cell = ws.getCell(excelRow, c);
      if (typeof r[c-1] === 'number') cell.numFmt = '0.00';
      cell.alignment = { vertical:'middle', horizontal:'center' };
    }
    // Remarks wrap
    ws.getCell(excelRow, 24).alignment = { wrapText:true, vertical:'top', horizontal:'left' };

    excelRow++;
  });

  // ===== Footer: "Total / Avg" aligned to columns =====
  const avg = (col) => counts[col] > 0 ? Number((totals[col]/counts[col]).toFixed(2)) : null;
  const n2z = (v) => typeof v === 'number' ? Number(v.toFixed(2)) : Number((0).toFixed(2));

  ws.getRow(excelRow).values = [
    '', 'Total / Avg:',
    n2z(totals[3]),  // C  Waste Receive (TOTAL)
    n2z(totals[4]),  // D  Feeding Qty (TOTAL)
    n2z(totals[5]),  // E  Waste Handed Back CCP (TOTAL)
    n2z(totals[6]),  // F  Water (TOTAL)
    avg(7),          // G  Feeding pH (AVG)
    avg(8),          // H  D1 (AVG)
    avg(9),          // I  D2 (AVG)
    avg(10),         // J  D3 (AVG)
    avg(11),         // K  D4 (AVG)
    avg(12),         // L  Balloon-1 (AVG)
    avg(13),         // M  Balloon-2 (AVG)
    n2z(totals[14]), // N  CBG Production (TOTAL)
    n2z(totals[15]), // O  CBG Flare (TOTAL)
    n2z(totals[16]), // P  C&T Vehicle Consumption (TOTAL)
    n2z(totals[17]), // Q  CBG Sold (TOTAL)
    n2z(totals[18]), // R  CBG Stock (TOTAL)
    n2z(totals[19]), // S  Manure Production (TOTAL)
    n2z(totals[20]), // T  Manure Disposed (TOTAL)
    n2z(totals[21]), // U  Manure Sold (TOTAL)
    n2z(totals[22]), // V  Manure Stock (TOTAL)
    '', ''           // W, X
  ];

  ws.getCell(excelRow, 2).font = { bold: true };
  for (let c=3; c<=22; c++){
    const cell = ws.getCell(excelRow, c);
    cell.font = { bold: true };
    if (typeof cell.value === 'number') cell.numFmt = '0.00';
    cell.alignment = { vertical:'middle', horizontal:'center' };
  }

  // ===== Borders for whole table area (A..X) =====
  for (let r=startRow; r<=excelRow; r++){
    for (let c=1; c<=24; c++){
      ws.getCell(r, c).border = {
        top:{style:'thin'}, left:{style:'thin'},
        bottom:{style:'thin'}, right:{style:'thin'}
      };
    }
  }

  // Save
  const buf = await workbook.xlsx.writeBuffer();
  saveAs(new Blob([buf]), 'daily_log_sheet_report.xlsx');
}

    
function filterByDate() {
  const fromDate = document.getElementById("from_date").value;
  const toDate = document.getElementById("to_date").value;
  const rows = document.querySelectorAll("#digesterReport tbody tr");

  // Totals
  let totalWasteReceived = 0;
  let totalFeedingQty = 0;
  let totalRejection = 0;
  let totalWater = 0;

  let totalFeedingPh = 0, totalDig1Ph = 0, totalDig2Ph = 0, totalDig3Ph = 0, totalDig4Ph = 0;
  let totalBalloon1 = 0, totalBalloon2 = 0;

  let totalCBGProduction = 0, totalCBGFlare = 0, totalCTConsumption = 0;
  let totalCBGSold = 0, totalCBGStock = 0;
  let totalManureProd = 0, totalManureDisposed = 0, totalManureSold = 0, totalManureStock = 0;

  // Counters for averaging
  let countFeedingPh = 0, countDig1Ph = 0, countDig2Ph = 0, countDig3Ph = 0, countDig4Ph = 0;
  let countBalloon1 = 0, countBalloon2 = 0;

  rows.forEach(row => {
    const cells = row.querySelectorAll("td");
    const dateStr = cells[1].textContent.trim();
    const [dd, mm, yyyy] = dateStr.split("-");
    const formattedDate = `${yyyy}-${mm}-${dd}`;

    const showRow = (!fromDate || formattedDate >= fromDate) && (!toDate || formattedDate <= toDate);
    row.style.display = showRow ? "" : "none";

    if (showRow) {
      totalWasteReceived += parseFloat(cells[2].textContent.trim()) || 0;
      totalFeedingQty   += parseFloat(cells[3].textContent.trim()) || 0;
      totalRejection    += parseFloat(cells[4].textContent.trim()) || 0;
      totalWater        += parseFloat(cells[5].textContent.trim()) || 0;

      let v6 = parseFloat(cells[6].textContent.trim());
      if (!isNaN(v6)) { totalFeedingPh += v6; countFeedingPh++; }

      let v7 = parseFloat(cells[7].textContent.trim());
      if (!isNaN(v7)) { totalDig1Ph += v7; countDig1Ph++; }

      let v8 = parseFloat(cells[8].textContent.trim());
      if (!isNaN(v8)) { totalDig2Ph += v8; countDig2Ph++; }

      let v9 = parseFloat(cells[9].textContent.trim());
      if (!isNaN(v9)) { totalDig3Ph += v9; countDig3Ph++; }

      let v10 = parseFloat(cells[10].textContent.trim());
      if (!isNaN(v10)) { totalDig4Ph += v10; countDig4Ph++; }

      let v11 = parseFloat(cells[11].textContent.trim());
      if (!isNaN(v11)) { totalBalloon1 += v11; countBalloon1++; }

      let v12 = parseFloat(cells[12].textContent.trim());
      if (!isNaN(v12)) { totalBalloon2 += v12; countBalloon2++; }

      totalCBGProduction += parseFloat(cells[13].textContent.trim()) || 0;
      totalCBGFlare      += parseFloat(cells[14].textContent.trim()) || 0;
      totalCTConsumption += parseFloat(cells[15].textContent.trim()) || 0;
      totalCBGSold       += parseFloat(cells[16].textContent.trim()) || 0;
      totalCBGStock      += parseFloat(cells[17].textContent.trim()) || 0;
      totalManureProd    += parseFloat(cells[18].textContent.trim()) || 0;
      totalManureDisposed += parseFloat(cells[19].textContent.trim()) || 0;
      totalManureSold    += parseFloat(cells[20].textContent.trim()) || 0;
      totalManureStock   += parseFloat(cells[21].textContent.trim()) || 0;
    }
  });

  const avg = (total, count) => count > 0 ? (total / count).toFixed(2) : '0.00';

  // Update UI
  document.getElementById("totalWasteReceived").innerHTML  = `<strong>${totalWasteReceived.toFixed(2)}</strong>`;
  document.getElementById("totalFeedingQty").innerHTML      = `<strong>${totalFeedingQty.toFixed(2)}</strong>`;
  document.getElementById("totalRejection").innerHTML       = `<strong>${totalRejection.toFixed(2)}</strong>`;
  document.getElementById("totalWater").innerHTML           = `<strong>${totalWater.toFixed(2)}</strong>`;

  // Show average values
  document.getElementById("totalFeedingPh").innerHTML       = `<strong>${avg(totalFeedingPh, countFeedingPh)}</strong>`;
  document.getElementById("totalDigester1Ph").innerHTML     = `<strong>${avg(totalDig1Ph, countDig1Ph)}</strong>`;
  document.getElementById("totalDigester2Ph").innerHTML     = `<strong>${avg(totalDig2Ph, countDig2Ph)}</strong>`;
  document.getElementById("totalDigester3Ph").innerHTML     = `<strong>${avg(totalDig3Ph, countDig3Ph)}</strong>`;
  document.getElementById("totalDigester4Ph").innerHTML     = `<strong>${avg(totalDig4Ph, countDig4Ph)}</strong>`;

  document.getElementById("totalBalloon1").innerHTML        = `<strong>${avg(totalBalloon1, countBalloon1)}</strong>`;
  document.getElementById("totalBalloon2").innerHTML        = `<strong>${avg(totalBalloon2, countBalloon2)}</strong>`;

  document.getElementById("totalCBGProduction").innerHTML   = `<strong>${totalCBGProduction.toFixed(2)}</strong>`;
  document.getElementById("totalCBGFlare").innerHTML        = `<strong>${totalCBGFlare.toFixed(2)}</strong>`;
  document.getElementById("totalCTConsumption").innerHTML   = `<strong>${totalCTConsumption.toFixed(2)}</strong>`;
  document.getElementById("totalCBGSold").innerHTML         = `<strong>${totalCBGSold.toFixed(2)}</strong>`;
  document.getElementById("totalCBGStock").innerHTML        = `<strong>${totalCBGStock.toFixed(2)}</strong>`;
  document.getElementById("totalManureProd").innerHTML      = `<strong>${totalManureProd.toFixed(2)}</strong>`;
  document.getElementById("totalManureDisposed").innerHTML  = `<strong>${totalManureDisposed.toFixed(2)}</strong>`;
  document.getElementById("totalManureSold").innerHTML      = `<strong>${totalManureSold.toFixed(2)}</strong>`;
  document.getElementById("totalManureStock").innerHTML     = `<strong>${totalManureStock.toFixed(2)}</strong>`;
}


function goBack() {
  window.location.href = "https://zigma.in/blue_planet/index.php?file=daily_log_sheet/list";
}


  </script>

</body>
</html>
