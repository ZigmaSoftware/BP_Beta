<?php
include '../../config/dbconfig.php';

$table = "cbg_daily_log";
$columns = [
    "date",
    "project_id",
    "waste_receive",
    "waste_reject",
    "waste_crushing",
    "feeding_kgs",
    "water_liters",
    "feeding_ph",
    "valve_1_ph",
    "nb",
    "wd",
    "start_reading",
    "end_reading",
    "total_reading",
    "daily_gas_generation",
    "start_purification_balloon",
    "stop_purification_balloon",
    "gas_used_for_cbg",
    "cbg_start_time",
    "cbg_stop_time",
    "cbg_running_hrs",
    "comp_start_time",
    "comp_stop_time",
    "comp_total_run_hrs",
    "total_cbg_generation",
    "start_cascade_pressure",
    "stop_cascade_pressure",
    "balance_cascade_pressure",
    "no_of_vehicle_filled",
    "balance_gas_cascade",
    "remark",
    "(SELECT user_name FROM user WHERE user.staff_unique_id = ".$table.".created_by ) AS created_by"
];

$table_details = [$table, $columns];
$where = "is_delete = 0 ORDER BY date ASC";

$result = $pdo->select($table_details, $where);
$data_rows = $result->status ? $result->data : [];
?>

<style>
  th, td { text-align: center; vertical-align: middle; }
  .redbg { background-color: #ffe7e7!important; }
  .bluebg { background-color: #e7f6ff!important; }
  .yellowbg { background-color: #fbffe7!important; }
  .greenbg { background-color: #e8ffe7!important; }
  .greybg { background-color: #ededed!important; }
  .table th { font-size: 0.75rem; }
  .table td { font-size: 0.9rem; }
  .print-header, .print-footer { display:none; }
@media print {
  @page { 
    size: A2 landscape;   /* ✅ A2 + Landscape */
    margin: 10mm; 
  }
  body { 
    -webkit-print-color-adjust: exact; 
    print-color-adjust: exact; 
    zoom: 90%;   /* ✅ bumped from 80% to 120% */
  }
  th, td { 
    border:1px solid #000!important; 
    padding:4px; 
  }

  /* Hide filters/buttons */
  .row, .btn-group, #from_date, #to_date, #filter_project_id, label { display:none!important; }

  /* Header only at top of page 1 */
  .print-header { display:block; position:static; margin-bottom:10px; }
  .print-header img { max-height:100px; }

  /* Footer stamp only once */
  .print-footer { 
    display:block; 
    position:static; 
    margin-top:20px; 
    text-align:right; 
    page-break-before: avoid; 
    page-break-after: avoid; 
  }
  .navbar-custom.d-none.d-md-block

 {

    display: none !important;

}
}




</style>

<div class="card"><div class="card-body">

<!-- Toolbar -->
<div class="row">
  <div class="col-8">
    <div class="form-group row">
      <label class="col-md-1 col-form-label">From</label>
      <div class="col-md-2"><input type="date" id="from_date" class="form-control"></div>
      <label class="col-md-1 col-form-label">To</label>
      <div class="col-md-2"><input type="date" id="to_date" class="form-control"></div>
      <div class="col-md-2"><label for="filter_project_id" class="form-label labelright">Project</label></div>
      <div class="col-md-2">
        <select id="filter_project_id" class="form-control">
          <option value="">All</option>
          <?php
          $project_options  = get_project_by_type('cbg');  
          if ($project_options && is_array($project_options)) {
              foreach ($project_options as $proj) {
                  echo '<option value="'.$proj['unique_id'].'">'.$proj['label'].'</option>';
              }
          }
          ?>
        </select>
      </div>
      <div class="col-md-2"><button class="btn btn-primary" onclick="filterByDate()">GO</button></div>
    </div>
  </div>
  <div class="col-4 text-end">
    <div class="btn-group">
      <button class="btn btn-secondary" onclick="goBack()" style="margin-right:10px">Back</button>
      <button class="btn btn-primary" id="printBtn" style="margin-right:10px">Print</button>
      <button class="btn btn-success" onclick="exportCBGLogToExcel()">Export to Excel</button>
    </div>
  </div>
</div>

<!-- Print Header -->
<div class="print-header">
  <table width="100%">
  <tr>
    <!-- ✅ Left Logo -->
    <td style="text-align:left; width:230px;">
      <img src="https://zigma.in/blue_planet_beta/folders/cbg_daily_log/newlogo.jpg" 
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

<!-- Report Table -->
<div id="reportTableWrapper">
    <div class="table-responsive">
  <table id="cbgReport" class="table table-bordered">
<thead>
  <tr>
    <th rowspan="2" class="greybg">S.No</th>
    <th rowspan="2" class="greybg">Date</th>
    <th colspan="5" class="redbg">Waste Processed (Kgs)</th>
    <th colspan="2" class="bluebg">Digester pH Values</th>
    <th colspan="2" class="yellowbg">Microbial Culture</th>
    <th colspan="3" class="greenbg">Flow Meter Reading</th>
    <th colspan="4" class="yellowbg">Gas Used for CBG</th>
    <th colspan="3" class="bluebg">CBG Running Status</th>
    <th colspan="4" class="greybg">Compressor Running Status</th>
    <th colspan="5" class="redbg">Cascade Filling Details</th>
    <th rowspan="2" class="greybg">Remark</th>
  </tr>
  <tr>
    <th class="redbg">Waste Receive</th>
    <th class="redbg">Waste Reject</th>
    <th class="redbg">Waste Crushing</th>
    <th class="redbg">Feeding (Kgs)</th>
    <th class="redbg">Water (L)</th>

    <th class="bluebg">Feeding pH</th>
    <th class="bluebg">Valve-1 pH</th>

    <th class="yellowbg">NB</th>
    <th class="yellowbg">WD</th>

    <th class="greenbg">Start</th>
    <th class="greenbg">End</th>
    <th class="greenbg">Total</th>

    <th class="yellowbg">Daily Gas (%)</th>
    <th class="yellowbg">Start Balloon (%)</th>
    <th class="yellowbg">Stop Balloon (%)</th>
    <th class="yellowbg">% Used for CBG</th>

    <th class="bluebg">CBG Start</th>
    <th class="bluebg">CBG Stop</th>
    <th class="bluebg">CBG Hrs</th>

    <th class="greybg">Comp Start</th>
    <th class="greybg">Comp Stop</th>
    <th class="greybg" class="bluebg">Comp Hrs</th>
    <th class="greybg">Total CBG (Kg)</th>


    <th class="redbg">Start Cascade</th>
    <th class="redbg">Stop Cascade</th>
    <th class="redbg">Balance (Bar)</th>
    <th class="redbg">Vehicles</th>
    <th class="redbg">Balance Gas (Kg)</th>
  </tr>
</thead>
    <tbody>
      <?php $sno=1; foreach($data_rows as $row): ?>
      <tr data-project-id="<?= $row['project_id']; ?>">
        <td><?= $sno++; ?></td>
        <td><?= date("d-m-Y", strtotime($row['date'])); ?></td>
        <td><?= $row['waste_receive']; ?></td>
        <td><?= $row['waste_reject']; ?></td>
        <td><?= $row['waste_crushing']; ?></td>
        <td><?= $row['feeding_kgs']; ?></td>
        <td><?= $row['water_liters']; ?></td>
        <td><?= $row['feeding_ph']; ?></td>
        <td><?= $row['valve_1_ph']; ?></td>
        <td><?= $row['nb']; ?></td>
        <td><?= $row['wd']; ?></td>
        <td><?= $row['start_reading']; ?></td>
        <td><?= $row['end_reading']; ?></td>
        <td><?= $row['total_reading']; ?></td>
        <td><?= $row['daily_gas_generation']; ?></td>
        <td><?= $row['start_purification_balloon']; ?></td>
        <td><?= $row['stop_purification_balloon']; ?></td>
        <td><?= $row['gas_used_for_cbg']; ?></td>
        <td><?= $row['cbg_start_time']; ?></td>
        <td><?= $row['cbg_stop_time']; ?></td>
        <td><?= $row['cbg_running_hrs']; ?></td>
        <td><?= $row['comp_start_time']; ?></td>
        <td><?= $row['comp_stop_time']; ?></td>
        <td><?= $row['comp_total_run_hrs']; ?></td>
        <td><?= $row['total_cbg_generation']; ?></td>
        <td><?= $row['start_cascade_pressure']; ?></td>
        <td><?= $row['stop_cascade_pressure']; ?></td>
        <td><?= $row['balance_cascade_pressure']; ?></td>
        <td><?= $row['no_of_vehicle_filled']; ?></td>
        <td><?= $row['balance_gas_cascade']; ?></td>
        <td><?= $row['remark']; ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
<tfoot>
  <tr style="font-weight:bold; background:#eafaea">
    <td colspan="2" class="text-end">Total/Avg</td>
    <td id="avg_waste_receive"></td>
    <td id="avg_waste_reject"></td>
    <td id="avg_waste_crushing"></td>
    <td id="avg_feeding"></td>
    <td></td> <!-- water liters skip -->
    <td colspan="7"></td>
    <td id="avg_daily_gas"></td>
    <td colspan="9"></td> <!-- ✅ was 8, make it 9 -->
    <td id="avg_total_cbg"></td> <!-- ✅ now aligns with Total CBG column -->
    <td colspan="5"></td> <!-- ✅ was 6, reduce to balance -->
  </tr>
</tfoot>

  </table>
  </div>
</div>
<!-- Print Footer -->
<div class="print-footer">
  <img src="https://zigma.in/blue_planet_beta/folders/cbg_daily_log/stamp_1.jpg" 
       alt="Footer Stamp" style="max-height: 120px;">
</div>
</div></div>
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
  const now = new Date();

  // First day of current month
  const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
  // Last day of current month
  const lastDay = new Date(now.getFullYear(), now.getMonth() + 1, 0);

  function formatDate(date) {
    const y = date.getFullYear();
    const m = String(date.getMonth() + 1).padStart(2, '0');
    const d = String(date.getDate()).padStart(2, '0');
    return `${y}-${m}-${d}`;
  }

  // ✅ Auto-set date fields
  document.getElementById("from_date").value = formatDate(firstDay);
  document.getElementById("to_date").value   = formatDate(lastDay);

  // ✅ Load with current month’s data immediately
  filterByDate();
  updateFooterAverages();

});


function filterByDate(){
  const from = document.getElementById("from_date").value;
  const to   = document.getElementById("to_date").value;
  const projectFilter = document.getElementById("filter_project_id").value;

  const rows = document.querySelectorAll("#cbgReport tbody tr");
  let sno = 1;

  rows.forEach(r => {
    const cols = r.querySelectorAll("td");
    const [dd,mm,yy] = cols[1].innerText.split("-");
    const d = `${yy}-${mm}-${dd}`;
    const projId = r.getAttribute("data-project-id");

    if ((!from || d >= from) && (!to || d <= to) && (!projectFilter || projId === projectFilter)){
      r.style.display="";
      cols[0].innerText = sno++;
    } else {
      r.style.display="none";
    }
  });

  // ✅ recalc averages after filtering
  updateFooterAverages();
}


function updateFooterAverages() {
  const rows = document.querySelectorAll("#cbgReport tbody tr");
  let sum_receive=0, sum_reject=0, sum_crushing=0, sum_feeding=0, sum_daily_gas=0, sum_total_cbg=0;
  let count=0;

  rows.forEach(r=>{
    if(r.style.display==="none") return;
    const cols = r.querySelectorAll("td");
    sum_receive   += parseFloat(cols[2].innerText)   || 0;
    sum_reject    += parseFloat(cols[3].innerText)   || 0;
    sum_crushing  += parseFloat(cols[4].innerText)   || 0;
    sum_feeding   += parseFloat(cols[5].innerText)   || 0;
    sum_daily_gas += parseFloat(cols[14].innerText)  || 0;
    sum_total_cbg += parseFloat(cols[24].innerText)  || 0;
    count++;
  });

  // ✅ Only averages
  document.getElementById("avg_waste_receive").innerText = (count? (sum_receive/count).toFixed(2):0);
  document.getElementById("avg_waste_reject").innerText  = (count? (sum_reject/count).toFixed(2):0);
  document.getElementById("avg_waste_crushing").innerText= (count? (sum_crushing/count).toFixed(2):0);
  document.getElementById("avg_feeding").innerText       = (count? (sum_feeding/count).toFixed(2):0);
  document.getElementById("avg_daily_gas").innerText     = (count? (sum_daily_gas/count).toFixed(2):0);
  document.getElementById("avg_total_cbg").innerText     = (count? (sum_total_cbg/count).toFixed(2):0);
}


async function exportCBGLogToExcel(){
  const workbook = new ExcelJS.Workbook();
  const ws = workbook.addWorksheet("CBG Daily Log");

  const startRow = 6; // Table headers start here

  // === 1) Left Logo (Zigma Logo) ===
  try {
    const respLogo = await fetch('https://zigma.in/blue_planet_beta/folders/cbg_daily_log/newlogo.jpg');
    const blobLogo = await respLogo.blob();
    const dataUrlLogo = await new Promise(r => {
      const f = new FileReader();
      f.onload = () => r(f.result);
      f.readAsDataURL(blobLogo);
    });
    const base64Logo = dataUrlLogo.split(',')[1];
    const logoId = workbook.addImage({ base64: base64Logo, extension: 'jpg' });
    ws.addImage(logoId, { tl: { col: 0, row: 0 }, br: { col: 2, row: 4 } });
  } catch(e) { console.warn("Zigma logo failed:", e); }

  // === 2) Company name & address in C1:L2 ===
  ws.mergeCells('C1:L1');
  ws.getCell('C1').value = 'XEON WASTE MANAGERS PRIVATE LIMITED';
  ws.getCell('C1').alignment = { vertical:'middle', horizontal:'center' };
  ws.getCell('C1').font = { size:16, bold:true };

  ws.mergeCells('C2:L2');
  ws.getCell('C2').value = 'Kohinoor World Towers T3, Office No.306, Opp. Empire Estate, Old Mumbai-Pune Hwy, Pimpri Colony, Pune Maharashtra-411018.';
  ws.getCell('C2').alignment = { wrapText:true, vertical:'middle', horizontal:'center' };
  ws.getCell('C2').font = { size:12 };
  ws.getRow(2).height = 25;

  // === 3) Blue Planet Logo ===
  try {
    const respBP = await fetch('https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG');
    const blobBP = await respBP.blob();
    const dataUrlBP = await new Promise(r => {
      const f = new FileReader();
      f.onload = () => r(f.result);
      f.readAsDataURL(blobBP);
    });
    const base64BP = dataUrlBP.split(',')[1];
    const bpLogoId = workbook.addImage({ base64: base64BP, extension:'png' });
    ws.addImage(bpLogoId, { tl: { col: 30, row: startRow-3 }, br: { col: 31, row: startRow-1 } });
  } catch(e){ console.warn("Blue Planet logo failed:", e); }

  // === 4) Headers ===
  const headers1 = [
    "S.No","Date",
    "Waste Processed (Kgs)","","","","",
    "Digester pH Values","",
    "Microbial Culture","",
    "Flow Meter Reading","","",
    "Gas Used for CBG","","","",
    "CBG Running Status","","",
    "Compressor Running Status","","","",
    "Cascade Filling Details","","","","",
    "Remark"
  ];
  const headers2 = [
    "","","Waste Receive","Waste Reject","Waste Crushing","Feeding (Kgs)","Water (L)",
    "Feeding pH","Valve-1 pH",
    "NB","WD",
    "Start","End","Total",
    "Daily Gas (%)","Start Balloon (%)","Stop Balloon (%)","% Used for CBG",
    "CBG Start","CBG Stop","CBG Hrs",
    "Comp Start","Comp Stop","Comp Hrs","Total CBG (Kg)",
    "Start Cascade","Stop Cascade","Balance (Bar)","Vehicles","Balance Gas (Kg)",
    ""
  ];

  ws.getRow(startRow).values = headers1;
  ws.getRow(startRow+1).values = headers2;
  ws.getRow(startRow).font = { bold:true };
  ws.getRow(startRow+1).font = { bold:true };
  ws.getRow(startRow).alignment = { vertical:'middle', horizontal:'center', wrapText:true };
  ws.getRow(startRow+1).alignment = { vertical:'middle', horizontal:'center', wrapText:true };

  ws.mergeCells(startRow,1,startRow+1,1);   // S.No
  ws.mergeCells(startRow,2,startRow+1,2);   // Date
  ws.mergeCells(startRow,3,startRow,7);     // Waste Processed
  ws.mergeCells(startRow,8,startRow,9);     // Digester pH
  ws.mergeCells(startRow,10,startRow,11);   // Microbial
  ws.mergeCells(startRow,12,startRow,14);   // Flow
  ws.mergeCells(startRow,15,startRow,18);   // Gas
  ws.mergeCells(startRow,19,startRow,21);   // CBG
  ws.mergeCells(startRow,22,startRow,25);   // Compressor
  ws.mergeCells(startRow,26,startRow,30);   // Cascade
  ws.mergeCells(startRow,31,startRow+1,31); // Remark

  // === 5) Data Rows ===
  const trs = document.querySelectorAll("#cbgReport tbody tr");
  let excelRow = startRow+2;
  let sno = 1;
  let sum_receive=0, sum_reject=0, sum_crushing=0, sum_feeding=0, sum_daily_gas=0, sum_total_cbg=0, count=0;

  trs.forEach(tr => {
    if(tr.style.display==="none") return;
    const tds = Array.from(tr.querySelectorAll("td")).map(td => td.innerText.trim());
    if(!tds.length) return;
    tds[0] = sno++; // serial no
    ws.getRow(excelRow).values = tds;

    sum_receive   += parseFloat(tds[2])  || 0;
    sum_reject    += parseFloat(tds[3])  || 0;
    sum_crushing  += parseFloat(tds[4])  || 0;
    sum_feeding   += parseFloat(tds[5])  || 0;
    sum_daily_gas += parseFloat(tds[14]) || 0;
    sum_total_cbg += parseFloat(tds[24]) || 0;
    count++;
    excelRow++;
  });

   // === 5b) Average Row ===
if(count > 0){
  const avgRow = ws.getRow(excelRow);
  avgRow.values = [
    "", "Total/Avg :",              // col 1–2
    (sum_receive/count).toFixed(2), // col 3 Waste Receive
    (sum_reject/count).toFixed(2),  // col 4 Waste Reject
    (sum_crushing/count).toFixed(2),// col 5 Waste Crushing
    (sum_feeding/count).toFixed(2), // col 6 Feeding (Kgs)
    "",                             // col 7 Water (skip)

    "", "",                         // col 8–9 pH
    "", "",                         // col 10–11 NB/WD
    "", "", "",                     // col 12–14 Flow Meter

    (sum_daily_gas/count).toFixed(2), // ✅ col 15 Daily Gas (%)

    "", "", "",                       // col 16–18 Balloon
    "", "", "",                       // col 19–21 CBG Running
    "", "", "",                       // col 22–24 Compressor

    (sum_total_cbg/count).toFixed(2), // ✅ col 25 Total CBG (Kg)

    "", "", "", "",                   // col 26–29 Cascade
    "",                               // col 30 Balance Gas
    ""                                // col 31 Remark
  ];

  avgRow.font = { bold:true };
  avgRow.alignment = { horizontal:"right" };

  for(let c=1; c<=31; c++){
    avgRow.getCell(c).border = { 
      top:{style:'thin'}, left:{style:'thin'}, 
      bottom:{style:'thin'}, right:{style:'thin'} 
    };
  }
  excelRow++;
}


  // === 6) Stamp below data ===
  try {
    const respStamp = await fetch('https://zigma.in/blue_planet_beta/folders/cooking_daily_log_sheet/stamp_1.jpg');
    const blobStamp = await respStamp.blob();
    const dataUrlStamp = await new Promise(r => {
      const f = new FileReader();
      f.onload = () => r(f.result);
      f.readAsDataURL(blobStamp);
    });
    const base64Stamp = dataUrlStamp.split(',')[1];
    const stampId = workbook.addImage({ base64: base64Stamp, extension: 'jpg' });
    ws.addImage(stampId, { tl: { col: 30, row: excelRow+1 }, br: { col: 31, row: excelRow+6 } });
  } catch(e){ console.warn("Stamp failed:", e); }

  // === 7) Column Widths ===
  ws.columns = [
    {width:6},{width:12},
    {width:14},{width:14},{width:14},{width:16},{width:14},
    {width:12},{width:12},
    {width:12},{width:12},
    {width:12},{width:12},{width:12},
    {width:14},{width:14},{width:14},{width:14},
    {width:14},{width:14},{width:12},
    {width:14},{width:14},{width:12},{width:14},
    {width:14},{width:14},{width:14},{width:12},{width:16},
    {width:20}
  ];

  // === 8) Borders ===
  for(let r=startRow; r<excelRow; r++){
    for(let c=1; c<=31; c++){
      ws.getCell(r,c).border = { 
        top:{style:'thin'}, left:{style:'thin'}, 
        bottom:{style:'thin'}, right:{style:'thin'} 
      };
    }
  }

  // === 9) Save ===
  const buf = await workbook.xlsx.writeBuffer();
  saveAs(new Blob([buf]), 'cbg_daily_log.xlsx');
}
function goBack(){ window.location.href="index.php?file=cbg_daily_log/list"; }
document.getElementById("printBtn").addEventListener("click",()=>window.print());
</script>

