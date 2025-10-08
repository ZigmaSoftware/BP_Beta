<?php
include '../../config/dbconfig.php';
?>
<!DOCTYPE html>
<html>
<head>
  <title>Integrated Daily Log Sheet Report</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { font-family: Arial, sans-serif; font-size: 13px; }
    .report-header { text-align: center; margin-bottom: 15px; }
    .report-header h3 { margin: 5px 0; }
    th, td { text-align: center; vertical-align: middle; }
    thead th { background: #f2f2f2; font-size: 12px; }
    tbody td { font-size: 12px; }
    
    @media print {
      @page {
        size: A2 landscape; /* ðŸ‘ˆ You can change to landscape if needed */
        margin: 10mm;
      }
    
      body * {
        visibility: hidden;
      }
    
      #report_table, #report_table *, .report-header, .report-header * {
        visibility: visible;
      }
    
      #report_table {
        position: absolute;
        left: 0;
        top: 60px; /* leaves space for title */
        width: 100%;
      }
    
      .report-header {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        text-align: center;
      }
    }

  </style>
</head>
<body class="p-3">

<div class="report-header">
  <h3>Integrated Daily Log Sheet Report</h3>
</div>

<!-- Filters -->
<div class="row g-3 mb-3">
  <div class="col-md-2">
    <label class="form-label">From Date</label>
    <input type="date" id="flt_from_date" class="form-control">
  </div>
  <div class="col-md-2">
    <label class="form-label">To Date</label>
    <input type="date" id="flt_to_date" class="form-control">
  </div>
  <div class="col-md-3">
    <label class="form-label">Company</label>
    <select id="flt_company" class="form-control select2">
      <?php echo select_option(company_name(), "All Companies", ""); ?>
    </select>
  </div>
  <div class="col-md-3">
    <label class="form-label">Project</label>
    <select id="flt_project" class="form-control select2">
      <option value="">All Projects</option>
    </select>
  </div>
  <div class="col-md-2">
    <label class="form-label">Application Type</label>
    <select id="flt_app_type" class="form-control select2">
      <option value="">All Types</option>
    </select>
  </div>

  <div class="col-12 d-flex align-items-end gap-2">
  <button id="flt_go" class="btn btn-primary">Go</button>
  <button id="btn_print" class="btn btn-success">Print</button>
  <button id="btn_excel" class="btn btn-warning text-white">Excel</button>

</div>

</div>

<div class="table-responsive">
  <table class="table table-bordered table-sm align-middle" id="report_table">
    <!-- dynamic -->
  </table>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/exceljs/dist/exceljs.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/FileSaver.js/2.0.5/FileSaver.min.js"></script>
<script>
/** UTIL **/
function dmy(dateStr) {
  if (!dateStr || dateStr === '0000-00-00') return '';
  const d = new Date(dateStr);
  if (isNaN(d)) return dateStr;
  const dd = String(d.getDate()).padStart(2,'0');
  const mm = String(d.getMonth()+1).padStart(2,'0');
  const yyyy = d.getFullYear();
  return `${dd}-${mm}-${yyyy}`;
}

// === HEADER GROUP DEFINITIONS ===
const headerGroups = {
  'Date': [
    'entry_date', 'project_name', 'week_no', 'automated_weighbridge'
  ],
  'Waste Incoming': [
    'dry_mix_corp', 'wet_mix_corp', 'wet_segregated_corp', 'complete_mix_corp',
    'wet_mix_bwg', 'dry_mix_bwg', 'wet_segregated_bwg', 'complete_mix_bwg',
    'total_waste_actual', 'total_waste_reported'
  ],
  'Processing Data': [
    'organic_waste_feed', 'recycles_generated'
  ],
  'Disposal Data': [
    'rejects_dry_segregation', 'rejects_wet_segregation', 'total_inert_disposed',
    'total_rdf_generation', 'rdf_sold', 'rdf_stock', 'slurry_disposed'
  ],
  'Performance Monitoring': [
    'flare_hrs', 'cbg_compressor_hrs', 'raw_biogas_produced', 'biogas_flared',
    'captive_consumption_gas', 'digester_temp', 'fos_tac_ratio', 'ph_value'
  ],
  'CBG': [
    'cbg_production_kg', 'cbg_captive_vehicle', 'cbg_sold_vehicle', 'cbg_sold_cascades',
    'cbg_sold_pipeline', 'cbg_total_sold', 'cbg_stock'
  ],
  'Manure': [
    'manure_production', 'manure_sold', 'manure_stock'
  ]
};

function buildReportTable(resp) {
  const $tbl = $('#report_table');
  $tbl.empty();

  const cols = resp.columns || [];
  const rows = resp.rows || [];

  if (!cols.length) {
    $tbl.html('<thead><tr><th>No columns</th></tr></thead><tbody><tr><td>No configuration found.</td></tr></tbody>');
    return;
  }

  // THEAD
  // === BUILD GROUPED HEADER ===
let theadRow1 = '<tr>';
let theadRow2 = '<tr>';

// For easier lookup
const colKeys = cols.map(c => c.key);

// Track which columns belong to which group
let added = new Set();

colKeys.forEach((key, idx) => {
  // Skip S.No special case
  if (key === '__sno') {
    theadRow1 += `<th rowspan="2">S.No</th>`;
    added.add(key);
    return;
  }

  // Find which group it belongs to
  let groupName = null;
  for (const g in headerGroups) {
    if (headerGroups[g].includes(key)) {
      groupName = g;
      break;
    }
  }

  // If part of a group not yet added → create group cell
  if (groupName && !added.has(groupName)) {
    const groupCols = headerGroups[groupName].filter(k => colKeys.includes(k));
    if (groupCols.length > 0) {
      theadRow1 += `<th colspan="${groupCols.length}" class="text-center">${groupName}</th>`;
      groupCols.forEach(k => {
        const c = cols.find(col => col.key === k);
        if (c) theadRow2 += `<th>${c.label}</th>`;
        added.add(k);
      });
      added.add(groupName);
    }
  }

  // If not part of any group (like remarks, plant_incharge etc.)
  else if (!groupName && !added.has(key)) {
    const c = cols.find(col => col.key === key);
    if (c) {
      theadRow1 += `<th rowspan="2">${c.label}</th>`;
      added.add(key);
    }
  }
});

theadRow1 += '</tr>';
theadRow2 += '</tr>';
const thead = `<thead>${theadRow1}${theadRow2}</thead>`;


  // TBODY
  let tbody = '<tbody>';
  if (rows.length === 0) {
    tbody += `<tr><td colspan="${cols.length}" class="text-center">No records</td></tr>`;
  } else {
    rows.forEach((r, i) => {
      tbody += '<tr>';
      cols.forEach(c => {
        if (c.key === '__sno') {
          tbody += `<td>${i + 1}</td>`;
        } else if (c.key === 'entry_date') {
          tbody += `<td>${dmy(r.entry_date)}</td>`;
        } else {
          const val = (r[c.key] ?? '');
          tbody += `<td>${val}</td>`;
        }
      });
      tbody += '</tr>';
    });
  }
  tbody += '</tbody>';

  $tbl.append(thead + tbody);
}

/** EXISTING HELPERS YOU ALREADY HAD **/
function load_project_for_filter(company_id = "", selected = "") {
  if (!company_id) {
    $('#flt_project').html('<option value="">All Projects</option>').trigger('change.select2');
    $('#flt_app_type').html('<option value="">All Types</option>').trigger('change.select2');
    return;
  }
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  $.post(ajax_url, { action: 'project_name', company_id: company_id, project: selected }, function (html) {
    html = html.replace('Select the Project Name', 'All Projects');
    $('#flt_project').html(html).val(selected).trigger('change.select2');
  });
}

function load_app_types_for_filter(project_id = "", company_id = "", selected = "") {
  if (!project_id) {
    $('#flt_app_type').html('<option value="">All Types</option>').trigger('change.select2');
    return;
  }
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  $.post(ajax_url, { action: 'application_type', company_id: company_id, project_id: project_id }, function (html) {
    if (!/All Types/.test(html)) html = '<option value="">All Types</option>' + html;
    $('#flt_app_type').html(html).val(selected).trigger('change.select2');
  });
}

/** EVENTS **/
$(document).on('change', '#flt_company', function() {
  load_project_for_filter($(this).val(), "");
});

$(document).on('change', '#flt_project', function() {
  const comp = $('#flt_company').val();
  load_app_types_for_filter($(this).val(), comp, "");
});

//Print
$('#btn_print').on('click', function() {
  if ($('#report_table').find('tbody tr').length === 0) {
    alert('No records to print.');
    return;
  }
  window.print();
});


$('#flt_go').on('click', function() {
  const ajax_url = sessionStorage.getItem('folder_crud_link');
  if (!ajax_url) {
    alert('Routing not set: sessionStorage.folder_crud_link is missing.');
    return;
  }

  const payload = {
    action: 'fetch_report',
    from:   $('#flt_from_date').val(),
    to:     $('#flt_to_date').val(),
    company: $('#flt_company').val(),
    project: $('#flt_project').val(),
    app:     $('#flt_app_type').val()
  };

  $.ajax({
    type: 'POST',
    url: ajax_url,
    dataType: 'json',
    data: payload,
    success: function(resp) {
      if (!resp || resp.status !== true) {
        const msg = resp && resp.message ? resp.message : 'No data found';
        $('#report_table').empty().html('<thead><tr><th>Info</th></tr></thead><tbody><tr><td class="text-center">'+msg+'</td></tr></tbody>');
        return;
      }
      buildReportTable(resp);
    },
    error: function() {
      $('#report_table').empty().html('<thead><tr><th>Error</th></tr></thead><tbody><tr><td class="text-center">Failed to fetch report</td></tr></tbody>');
    }
  });
});

// === EXCEL EXPORT ===
async function exportToExcel() {
  const ajax_url = sessionStorage.getItem('folder_crud_link');
  if (!ajax_url) {
    alert('Routing not set: sessionStorage.folder_crud_link is missing.');
    return;
  }

  const payload = {
    action: 'fetch_report',
    from:   $('#flt_from_date').val(),
    to:     $('#flt_to_date').val(),
    company: $('#flt_company').val(),
    project: $('#flt_project').val(),
    app:     $('#flt_app_type').val()
  };

  // Fetch data fresh based on current filters
  const response = await fetch(ajax_url, {
    method: 'POST',
    body: new URLSearchParams(payload),
  });
  const resp = await response.json();

  if (!resp || resp.status !== true || !resp.rows?.length) {
    alert('No data to export.');
    return;
  }

  // Build Excel workbook
  const wb = new ExcelJS.Workbook();
  const ws = wb.addWorksheet('Integrated Daily Log Sheet');

  // === Report Title ===
  ws.mergeCells('A1', 'J1');
  ws.getCell('A1').value = 'BLUE PLANET INTEGRATED WASTE SOLUTIONS LIMITED';
  ws.getCell('A1').font = { bold: true, size: 14 };
  ws.getCell('A1').alignment = { horizontal: 'center', vertical: 'middle' };
  ws.mergeCells('A2', 'J2');
  ws.getCell('A2').value = 'Powerol Building, Gate Number 2, 2nd Floor, Akruli Road, Kandivali East';
  ws.getCell('A2').font = { italic: true, size: 12, color: { argb: 'FF006666' } };
  ws.getCell('A2').alignment = { horizontal: 'center', vertical: 'middle' };
  ws.addRow([]); // empty row spacer

  // === Build Grouped Header Rows ===
  const cols = resp.columns;
  const colKeys = cols.map(c => c.key);
  let headerRow1 = [];
  let headerRow2 = [];
  const added = new Set();

  colKeys.forEach(key => {
    if (key === '__sno') {
      headerRow1.push('S.No');
      headerRow2.push('');
      added.add(key);
      return;
    }

    // Identify which group the column belongs to
    let groupName = null;
    for (const g in headerGroups) {
      if (headerGroups[g].includes(key)) {
        groupName = g;
        break;
      }
    }

    // Add grouped columns
    if (groupName && !added.has(groupName)) {
      const groupCols = headerGroups[groupName].filter(k => colKeys.includes(k));
      if (groupCols.length > 0) {
        headerRow1.push(groupName);
        for (let i = 1; i < groupCols.length; i++) headerRow1.push('');
        groupCols.forEach(k => {
          const c = cols.find(col => col.key === k);
          if (c) headerRow2.push(c.label);
          added.add(k);
        });
        added.add(groupName);
      }
    }
    // Columns not under any group
    else if (!groupName && !added.has(key)) {
      const c = cols.find(col => col.key === key);
      headerRow1.push(c.label);
      headerRow2.push('');
      added.add(key);
    }
  });

  const r1 = ws.addRow(headerRow1);
  const r2 = ws.addRow(headerRow2);

  // Merge grouped headers (adjusted for company header rows)
    let colIndex = 1;
    const headerStartRow = 4; // group titles row (like Date, Waste Incoming)
    
    // We’ll push non-grouped headers (like Plant Incharge, Remarks) to the second row instead
    for (let i = 0; i < headerRow1.length; i++) {
      const h = headerRow1[i];
      if (h && h !== '') {
        let span = 1;
        for (let j = i + 1; j < headerRow1.length && headerRow1[j] === ''; j++) span++;
        if (span > 1) {
          // Grouped columns (Date, Waste Incoming, etc.)
          ws.mergeCells(headerStartRow, colIndex, headerStartRow, colIndex + span - 1);
        } else {
          // Non-grouped (like Plant Incharge, Remarks) → occupy both header rows
          ws.mergeCells(headerStartRow, colIndex, headerStartRow + 1, colIndex);
        }
      }
      colIndex++;
    }
    


  // Style header rows
  [r1, r2].forEach(r => {
    r.font = { bold: true, size: 11 };
    r.alignment = { horizontal: 'center', vertical: 'middle', wrapText: true };
    r.eachCell(cell => {
      cell.fill = { type: 'pattern', pattern: 'solid', fgColor: { argb: 'FFECECEC' } };
      cell.border = {
        top: { style: 'thin' },
        left: { style: 'thin' },
        bottom: { style: 'thin' },
        right: { style: 'thin' }
      };
    });
  });

  // === Add Data Rows ===
  resp.rows.forEach((r, i) => {
    const rowData = [];
    cols.forEach(c => {
      if (c.key === '__sno') rowData.push(i + 1);
      else if (c.key === 'entry_date') rowData.push(dmy(r.entry_date));
      else rowData.push(r[c.key] ?? '');
    });
    ws.addRow(rowData);
  });

  // Auto-fit columns
  ws.columns.forEach(col => {
    let maxLen = 0;
    col.eachCell({ includeEmpty: true }, cell => {
      const val = cell.value ? cell.value.toString() : '';
      maxLen = Math.max(maxLen, val.length);
    });
    col.width = Math.min(Math.max(maxLen + 2, 12), 40);
  });
  
    // === Add Logo above Plant Incharge / Remarks (no row spacing) ===
  try {
    const imageUrl = "https://zigma.in/blue_planet/assets/images/blueplanetbiofuel.PNG";

    // Fetch and convert image
    const imgResponse = await fetch(imageUrl);
    const imgBuffer = await imgResponse.arrayBuffer();
    const imageId = wb.addImage({
      buffer: imgBuffer,
      extension: 'png'
    });

    // Find columns for Plant Incharge and Remarks
    let plantCol = 0, remarksCol = 0;
    cols.forEach((c, idx) => {
      const name = (c.label || '').toLowerCase();
      if (name.includes('plant')) plantCol = idx + 1;
      if (name.includes('remark')) remarksCol = idx + 1;
    });

    // Decide where to place the logo
    const targetCol = plantCol || remarksCol || cols.length; // fallback to last col
    const targetRow = 1; // near top (row numbering is 1-based)

    // 🖼️ Add logo exactly above these headers without creating extra rows
    ws.addImage(imageId, {
      tl: { col: targetCol - 1, row: targetRow - 1.2 }, // a bit above header
      ext: { width: 170, height: 45 }                   // adjust as needed
    });
  } catch (err) {
    console.error("Logo insert failed:", err);
  }

  

  // === Download Excel ===
  const blob = await wb.xlsx.writeBuffer();
  saveAs(
      new Blob([blob], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' }),
      'Integrated_daily_log_sheet.xlsx'
    );

}

// Bind Excel button
$('#btn_excel').on('click', exportToExcel);

// === Auto-load ALL records on first paint ===
$(function () {
  // ensure filters are blank so backend returns everything
  $('#flt_from_date').val('');
  $('#flt_to_date').val('');
  $('#flt_company').val('');
  $('#flt_project').html('<option value="">All Projects</option>').val('');
  $('#flt_app_type').html('<option value="">All Types</option>').val('');

  // fire the fetch
  $('#flt_go').trigger('click');
});

</script>
</body>
</html>
