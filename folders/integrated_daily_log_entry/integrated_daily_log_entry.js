// Form/Table variables
var form_name = 'Integrated Daily Logsheet';
var table_id  = 'Integrated_daily_log_entry_master_datatable';
var action    = "datatable";
var dt; // keep reference

// ======================= BLOCKING LOADER (NEW) =======================
function showBlockingLoader(minMs = 8000) {
  const start = Date.now();
  Swal.fire({
    title: 'Loading…',
    html: 'Preparing daily log inputs',
    allowOutsideClick: false,
    allowEscapeKey: false,
    allowEnterKey: false,
    didOpen: () => { Swal.showLoading(); },
    backdrop: true
  });
  return {
    closeAfterMin: () => {
      const elapsed = Date.now() - start;
      const wait = Math.max(0, minMs - elapsed);
      return new Promise(res => setTimeout(res, wait)).then(() => Swal.close());
    }
  };
}

// ======================= RAW FETCHERS (NEW) =======================
// Only fetch; DO NOT render. Returns Promise<{status:boolean, data:object}>
function fetchDailylogsheetFlags(project_id, application_type, company_id) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  return $.ajax({
    type: "POST",
    url : ajax_url,
    dataType: "json",
    data: { company_id, project_id, application_type, action: "dailylogsheet_data" }
  });
}

// Returns Promise<row|null>
function fetchEntryIfAny() {
  const uid = $("#unique_id").val();
  if (!uid) return Promise.resolve(null);
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  return $.ajax({
    type: "POST",
    url : ajax_url,
    dataType: "json",
    data: { action: "get_entry", unique_id: uid }
  }).then(resp => (resp && resp.status && resp.data) ? resp.data : null);
}

// ======================= APPLY ENTRY ROW (NEW) =======================
function apply_entry_row(row) {
  if (!row) return;
  withProgrammaticChange(() => {
    if (row.entry_date && row.entry_date !== '0000-00-00') {
      $(`[name="values[date_field]"]`).val(row.entry_date);
    }
    if (row.week_no) {
      const wk = toWeekInputValue(row.entry_date, row.week_no);
      if (wk) $(`[name="values[week_field]"]`).val(wk);
    }
    const keys = [
      "automated_weighbridge",
      "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp",
      "wet_mix_bwg","dry_mix_bwg","wet_segregated_bwg","complete_mix_bwg",
      "total_waste_actual","total_waste_reported","organic_waste_feed",
      "recycles_generated","rejects_dry_segregation","rejects_wet_segregation",
      "total_inert_disposed","total_rdf_generation","rdf_sold","rdf_stock",
      "slurry_disposed","flare_hrs","cbg_compressor_hrs","raw_biogas_produced",
      "biogas_flared","captive_consumption_gas","digester_temp","fos_tac_ratio",
      "ph_value","cbg_production_kg","cbg_captive_vehicle","cbg_sold_vehicle",
      "cbg_sold_cascades","cbg_sold_pipeline","cbg_total_sold","cbg_stock",
      "manure_production","manure_sold","manure_stock","plant_incharge","remarks"
    ];
    keys.forEach(k => {
      const $el = $(`[name="values[${k}]"]`);
      if ($el.length) $el.val(row[k] ?? "");
    });
  });
}

// ======================= BOOT PAINT (4s gate) (NEW) =======================
function bootDailylogsheetPaint(project_id, application_type, company_id) {
  // Start spinner and block UI
  const gate = showBlockingLoader(4000);

  // Fetch both in parallel
  return Promise.all([
    fetchDailylogsheetFlags(project_id, application_type, company_id),
    fetchEntryIfAny()
  ]).then(([flagsResp, entryRow]) => {
    // Render only after BOTH are ready
    if (flagsResp && flagsResp.status) {
      // 1) build inputs
      renderFromFlags(flagsResp.data);

      // 2) prefill values (simultaneous to user)
      apply_entry_row(entryRow);

      // 3) kick idempotent stock fetchers & derived calcs
      const company_id_val = $("#company_name").val();
      const project_id_val = $("#project_name").val() || project_id;
      const app_val        = $("#application_type").val() || application_type;
      if (project_id_val && app_val && company_id_val) {
        fetchLastRdfStock(company_id_val, project_id_val, app_val, ()=>{});
        fetchLastCbgStock(company_id_val, project_id_val, app_val, ()=>{});
        fetchLastManureStock(company_id_val, project_id_val, app_val, ()=>{});
      }
    } else {
      $('#dailylog_dynamic_fields').html('<div class="col-12 text-muted">No fields configured for this selection.</div>');
    }
  }).catch(err => {
    console.error('Boot load failed:', err);
    $('#dailylog_dynamic_fields').html('<div class="col-12 text-danger">Failed to load configuration.</div>');
  }).finally(() => gate.closeAfterMin()); // ensure spinner stays up for >=4s
}



// 🔧 replace BOTH $(document).ready(...) blocks with this single one
$(document).ready(function () {
  init_datatable(table_id, form_name, action);

  // initial company -> project load (edit or first paint)
  const company_name_val = $("#company_name").val();
  get_project_name(company_name_val);

  // FILTERS (list screen)
  $('#flt_company').on('change', function () {
    const company_id = $(this).val() || '';
    load_projects_for_filter(company_id, '');
    $('#flt_app_type').html('<option value="">All Types</option>');
  });

  $('#flt_project').on('change', function () {
    const company_id = $('#flt_company').val() || '';
    const project_id = $(this).val() || '';
    load_app_types_for_filter(project_id, company_id, '');
  });

  $('#flt_go').on('click', function () {
    $('#' + table_id).DataTable().ajax.reload();
  });

  $('#flt_report').on('click', function () {
    const from_date  = $('#flt_from_date').val() || '';
    const to_date    = $('#flt_to_date').val()   || '';
    const company_id = $('#flt_company').val()   || '';
    const project_id = $('#flt_project').val()   || '';
    const app_type   = $('#flt_app_type').val()  || '';

    const qs = {};
    if (from_date)  qs.from_date = from_date;
    if (to_date)    qs.to_date = to_date;
    if (company_id) qs.company_id = company_id;
    if (project_id) qs.project_id = project_id;
    if (app_type)   qs.application_type = app_type;

    let url = 'index.php?file=integrated_daily_log_entry/report';
    if (Object.keys(qs).length > 0) url += '&' + $.param(qs);
    window.open(url, '_blank');
  });

  $('#flt_reset').on('click', function () {
    $('#flt_from_date').val('');
    $('#flt_to_date').val('');
    $('#flt_company').val('').trigger('change.select2');
    $('#flt_project').html('<option value="">All Projects</option>').trigger('change.select2');
    $('#flt_app_type').html('<option value="">All Types</option>').trigger('change.select2');
    $('#' + table_id).DataTable().ajax.reload();
  });

  // 🆕 FORM SCREEN: if the user changes application type explicitly, re-gate and repaint
  $(document).on('change', '#application_type', function () {
    const pid = $('#project_name').val();
    const cid = $('#company_name').val();
    const app = $(this).val();
    if (pid && cid && app) {
      bootDailylogsheetPaint(pid, app, cid);
    } else {
      $('#dailylog_dynamic_fields').empty();
    }
  });
});

/* ======================= Session for headers/logos (if any) ======================= */
var company_name    = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_address");
var company_phone   = sessionStorage.getItem("company_phone");
var company_email   = sessionStorage.getItem("company_email");
var company_logo    = sessionStorage.getItem("company_logo");

/* ======================= CREATE / UPDATE ======================= */
function integrated_daily_log_entry_cu(unique_id = "") {
  var internet_status = is_online();
  if (!internet_status) { sweetalert("no_internet"); return false; }

  var is_form = form_validity_check("was-validated");
  if (!is_form) { sweetalert("form_alert"); return; }

  var data = $(".was-validated").serialize();

  // If dynamic inputs are present, save ENTRY; else save MASTER flags
  var hasEntryFields = $('[name^="values["]').length > 0;
  if (hasEntryFields) {
    data += "&unique_id=" + unique_id + "&action=createupdate";
  } else {
    // Ensure unchecked flags go as 0 (if flags are on this page)
    $(".field-checkbox").each(function(){
      var key = $(this).attr('id'); // id == field key
      if (!$(this).is(':checked')) {
        data += "&fields[" + key + "]=0";
      }
    });
    data += "&unique_id=" + unique_id + "&action=createupdate";
  }

  var ajax_url = sessionStorage.getItem("folder_crud_link");
  var url      = sessionStorage.getItem("list_link");

  $.ajax({
    type: "POST",
    url: ajax_url,
    data: data,
    beforeSend: function () {
      $(".createupdate_btn").attr("disabled","disabled").text("Loading...");
    },
    success: function (res) {
      var obj    = JSON.parse(res);
      var msg    = obj.msg;
      var status = obj.status;

      if (!status) {
        $(".createupdate_btn").text("Error");
        console.log(obj.error || obj.sql || obj);
        return sweetalert("error", "");
      }

      $(".createupdate_btn").removeAttr("disabled").text(unique_id ? "Update" : "Save");
      sweetalert(msg, url);
    },
    error: function () {
      alert("Network Error");
    }
  });
}

/* DataTable with filter params sent to server */
function init_datatable(table_id = '', form_name = '', action = '') {
  var table = $("#" + table_id);
  var ajax_url = sessionStorage.getItem("folder_crud_link");

  dt = table.DataTable({
    ordering  : true,
    searching : true,
    ajax      : {
      url  : ajax_url,
      type : "POST",
      data : function (d) {
        d.action          = "datatable";
        d.from_date       = $('#flt_from_date').val() || '';
        d.to_date         = $('#flt_to_date').val()   || '';
        d.company_id      = $('#flt_company').val()   || '';
        d.project_id      = $('#flt_project').val()   || '';
        d.application_type= $('#flt_app_type').val()  || '';
      }
    }
  });
}

function integrated_daily_log_entry_delete(unique_id = "") {
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url = sessionStorage.getItem("list_link");

    confirm_delete('delete').then((result) => {
        if (result.isConfirmed) {
            var data = {
                "unique_id": unique_id,
                "action": "delete"
            };

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function (data) {
                    var obj = JSON.parse(data);
                    var msg = obj.msg;
                    var status = obj.status;

                    if (status) {
                        init_datatable(table_id, form_name, action);
                    }
                    sweetalert(msg, url);
                }
            });
        }
    });
}
/* ======================= PROJECT NAME DROPDOWN ======================= */
function get_project_name(company_id = "") {
  let project = $("#project").val(); // hidden current project (on edit)
  if (company_id) {
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
      type: "POST",
      url : ajax_url,
      data: { company_id, project, action: "project_name" },
      success: function (data) {
        $("#project_name").html(data || '<option value="">Select the Project</option>');

        // Ensure selected project in edit mode
        if (project) {
          $("#project_name").val(project).trigger('change.select2');
        }

        // Now load Application Types and preselect
        const pid = $("#project_name").val();
        if (pid) {
          get_application_type_by_project(pid, $("#company_name").val());
        }
      }
    });
  } else {
    $("#project_name").html('<option value="">Select the Project</option>');
  }
}


/* ======================= APPLICATION TYPE DROPDOWN ======================= */
function get_application_type_by_project(project_id = "", company_id = "") {
  if (project_id) {
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
      type: "POST",
      url : ajax_url,
      data: { company_id, project_id, action: "application_type" },
      success: function (data) {
        // Replace options from server
        $("#application_type").html(data || '<option value="">Select Type</option>');

        // Prefill selection (edit mode) if available
        const pre = $("#application_type_prefill").val();
        if (pre) {
          $("#application_type").val(pre).trigger('change.select2');
        }

        // Finally: block for 4s, fetch flags + entry, then render together
        const company_id_val = $("#company_name").val();
        const app_val = $("#application_type").val();
        if (project_id && app_val && company_id_val) {
          bootDailylogsheetPaint(project_id, app_val, company_id_val);
        }

      }
    });
  } else {
    $("#application_type").html('<option value="">Select Type</option>');
  }
}

/* ======================= GET FLAGS + RENDER DYNAMIC INPUTS ======================= */
// <script>
// ====================== GLOBAL STABILIZERS ======================
let __dlg_req = { xhr: null, seq: 0, lastKey: "", debounce: null };
let __get_entry_req = { xhr: null };
let __progChange = 0; // >0 => ignore change handlers bound below

function withProgrammaticChange(fn) {
  __progChange++;
  try { fn(); } finally { __progChange--; }
}

// Debounced single dispatcher for dailylogsheet_data
function requestDailylogsheetData(project_id, application_type, company_id) {
  if (__dlg_req.debounce) clearTimeout(__dlg_req.debounce);
  __dlg_req.debounce = setTimeout(() => {
    get_dailylogsheet_data(project_id, application_type, company_id);
  }, 150); // tune 150–250ms if needed
}

// ======================= GET FLAGS + RENDER DYNAMIC INPUTS =======================
function get_dailylogsheet_data(project_id = "", application_type = "", company_id = "") {
  if (!project_id || !application_type || !company_id) {
    $("input[type=checkbox].field-checkbox").prop("checked", false);
    $('#dailylog_dynamic_fields').empty();
    return;
  }

  const ajax_url = sessionStorage.getItem("folder_crud_link");

  // Single-flight: same key + in-flight => do nothing
  const key = `${company_id}|${project_id}|${application_type}`;
  if (__dlg_req.lastKey === key && __dlg_req.xhr && __dlg_req.xhr.readyState !== 4) {
    return;
  }
  __dlg_req.lastKey = key;

  // Abort any prior request
  if (__dlg_req.xhr && __dlg_req.xhr.readyState !== 4) {
    __dlg_req.xhr.abort();
  }

  // Latest-wins: sequence token
  const mySeq = ++__dlg_req.seq;

  __dlg_req.xhr = $.ajax({
    type: "POST",
    url : ajax_url,
    dataType: "json",
    data: { company_id, project_id, application_type, action: "dailylogsheet_data" }
  })
  .done(function (resp) {
    // Ignore stale responses
    if (mySeq !== __dlg_req.seq) return;

    if (resp.status) {
      // sync checkboxes (if displayed)
      Object.keys(FIELD_CONFIG).forEach(k => {
        const checked = +resp.data[k] === 1;
        $(`#${k}.field-checkbox`).prop('checked', checked);
      });

      renderFromFlags(resp.data);

      // prevent change handlers from re-firing cascades
      withProgrammaticChange(() => {
        load_entry_values_if_any();
      });

      // fetch rolling stocks once IDs exist (idempotent)
      const company_id_val = $("#company_name").val();
      const project_id_val = $("#project_name").val();
      const app_val        = $("#application_type").val();
      if (project_id_val && app_val && company_id_val) {
        fetchLastRdfStock(company_id_val, project_id_val, app_val, ()=>{});
        fetchLastCbgStock(company_id_val, project_id_val, app_val, ()=>{});
        fetchLastManureStock(company_id_val, project_id_val, app_val, ()=>{});
      }
    } else {
      $("input[type=checkbox].field-checkbox").prop("checked", false);
      $('#dailylog_dynamic_fields').html('<div class="col-12 text-muted">No fields configured for this selection.</div>');
    }
  })
  .fail(function(xhr, status, error) {
    if (status !== 'abort') {
      console.error("AJAX Error:", status, error);
      $('#dailylog_dynamic_fields').html('<div class="col-12 text-danger">Failed to load configuration.</div>');
    }
  })
  .always(function(){
    if (mySeq === __dlg_req.seq) __dlg_req.xhr = null;
  });
}

// ======================= UI CONFIG FOR DYNAMIC FIELDS =======================
const FIELD_CONFIG = {
  date_field:             { label: 'Date',                       type: 'date' },
  week_field:             { label: 'Week',                       type: 'week' },

  // NOT a checkbox — treat as normal field
  automated_weighbridge:  { label: 'Automated Weighment Date', type: 'date' },
  // Corporation streams (tonnes)
  dry_mix_corp:           { label: 'Dry Mix (Corp)',             type: 'number', step: '0.01', suffix: 'T' },
  wet_mix_corp:           { label: 'Wet Mix (Corp)',             type: 'number', step: '0.01', suffix: 'T' },
  wet_segregated_corp:    { label: 'Wet Segregated (Corp)',      type: 'number', step: '0.01', suffix: 'T' },
  complete_mix_corp:      { label: 'Complete Mix (Corp)',        type: 'number', step: '0.01', suffix: 'T' },

  // BWG streams (tonnes)
  wet_mix_bwg:            { label: 'Wet Mix (BWG)',              type: 'number', step: '0.01', suffix: 'T' },
  dry_mix_bwg:            { label: 'Dry Mix (BWG)',              type: 'number', step: '0.01', suffix: 'T' },
  wet_segregated_bwg:     { label: 'Wet Segregated (BWG)',       type: 'number', step: '0.01', suffix: 'T' },
  complete_mix_bwg:       { label: 'Complete Mix (BWG)',         type: 'number', step: '0.01', suffix: 'T' },

  total_waste_actual:     { label: 'Total Waste (Actual)',       type: 'number', step: '0.01', suffix: 'T' },
  total_waste_reported:   { label: 'Total Waste (Reported)',     type: 'number', step: '0.01', suffix: 'T' },
  organic_waste_feed:     { label: 'Organic Waste Feed',         type: 'number', step: '0.01', suffix: 'T' },
  recycles_generated:     { label: 'Recyclables Generated',      type: 'number', step: '0.01', suffix: 'T' },
  rejects_dry_segregation:{ label: 'Rejects (Dry Segregation)',  type: 'number', step: '0.01', suffix: 'T' },
  rejects_wet_segregation:{ label: 'Rejects (Wet Segregation)',  type: 'number', step: '0.01', suffix: 'T' },
  total_inert_disposed:   { label: 'Total Inert Disposed',       type: 'number', step: '0.01', suffix: 'T' },

  total_rdf_generation:   { label: 'RDF Generation',             type: 'number', step: '0.01', suffix: 'T' },
  rdf_sold:               { label: 'RDF Sold',                   type: 'number', step: '0.01', suffix: 'T' },
  rdf_stock:              { label: 'RDF Stock',                  type: 'number', step: '0.01', suffix: 'T' },

  slurry_disposed:        { label: 'Slurry Disposed',            type: 'number', step: '0.01', suffix: 'T' },

  flare_hrs:              { label: 'Flare Hours',                type: 'number', step: '0.1',  suffix: 'hrs' },
  cbg_compressor_hrs:     { label: 'CBG Compressor Hours',       type: 'number', step: '0.1',  suffix: 'hrs' },

  raw_biogas_produced:    { label: 'Raw Biogas Produced',        type: 'number', step: '0.01' },
  biogas_flared:          { label: 'Biogas Flared',              type: 'number', step: '0.01' },
  captive_consumption_gas:{ label: 'Captive Gas Consumption',    type: 'number', step: '0.01' },

  digester_temp:          { label: 'Digester Temperature',       type: 'number', step: '0.1',  suffix: '°C' },
  fos_tac_ratio:          { label: 'FOS:TAC Ratio',              type: 'number', step: '0.01' },
  ph_value:               { label: 'pH Value',                   type: 'number', step: '0.01', min: '0', max: '14' },

  cbg_production_kg:      { label: 'CBG Production',             type: 'number', step: '0.01', suffix: 'kg' },
  cbg_captive_vehicle:    { label: 'CBG Captive (Vehicle)',      type: 'number', step: '0.01', suffix: 'kg' },
  cbg_sold_vehicle:       { label: 'CBG Sold (Vehicle)',         type: 'number', step: '0.01', suffix: 'kg' },
  cbg_sold_cascades:      { label: 'CBG Sold (Cascades)',        type: 'number', step: '0.01', suffix: 'kg' },
  cbg_sold_pipeline:      { label: 'CBG Sold (Pipeline)',        type: 'number', step: '0.01', suffix: 'kg' },
  cbg_total_sold:         { label: 'CBG Total Sold',             type: 'number', step: '0.01', suffix: 'kg' },
  cbg_stock:              { label: 'CBG Stock',                  type: 'number', step: '0.01', suffix: 'kg' },

  manure_production:      { label: 'Manure Production',          type: 'number', step: '0.01', suffix: 'T' },
  manure_sold:            { label: 'Manure Sold',                type: 'number', step: '0.01', suffix: 'T' },
  manure_stock:           { label: 'Manure Stock',               type: 'number', step: '0.01', suffix: 'T' },

  plant_incharge:         { label: 'Plant In-charge',            type: 'text' },
  remarks:                { label: 'Remarks',                    type: 'textarea' }
};

function titleCaseFromKey(key) {
  return key.replace(/_/g, ' ').replace(/\b\w/g, s => s.toUpperCase());
}

function buildField(name, cfg) {
  const id = 'input_' + name;
  const label = cfg.label || titleCaseFromKey(name);
  const required = 'required';
  const wrapCol = `<div class="col-md-6 mb-3">`;
  let control = '';

  if (cfg.type === 'textarea') {
    control = `<textarea class="form-control" id="${id}" name="values[${name}]" rows="2" ${required}></textarea>`;
  } else {
    const step = cfg.step ? ` step="${cfg.step}"` : '';
    const min  = cfg.min  ? ` min="${cfg.min}"`   : '';
    const max  = cfg.max  ? ` max="${cfg.max}"`   : '';
    const type = cfg.type || 'text';
    let readonly = ['total_waste_actual','total_waste_reported','rdf_stock','cbg_total_sold','cbg_stock','manure_stock'].includes(name) ? 'readonly style="background-color:#f5f5f5;"' : '';
    const input = `<input type="${type}" class="form-control" id="${id}" name="values[${name}]" ${required}${step}${min}${max} ${readonly}>`;

    if (cfg.suffix) {
      control = `
        <div class="input-group">
          ${input}
          <div class="input-group-append">
            <span class="input-group-text">${cfg.suffix}</span>
          </div>
        </div>`;
    } else {
      control = input;
    }
  }

  return `
    ${wrapCol}
      <label class="col-form-label" for="${id}">${label}</label>
      ${control}
    </div>`;
}

// ======================= SINGLE renderFromFlags + one-time wiring =======================
const __wired = { totals:false, rdf:false, cbg:false, manure:false, weighbridge:false };

function renderFromFlags(flags) {
  const $root = $('#dailylog_dynamic_fields').empty();
  Object.keys(FIELD_CONFIG).forEach(key => {
    if (+flags[key] === 1) $root.append(buildField(key, FIELD_CONFIG[key]));
  });

  if (!__wired.totals)      { setupAutoTotalCalculation();      __wired.totals = true; }
  if (!__wired.rdf)         { setupAutoRdfStockCalculation();   __wired.rdf = true; }
  if (!__wired.cbg)         { setupAutoCbgCalculation();        __wired.cbg = true; }
  if (!__wired.manure)      { setupAutoManureCalculation();     __wired.manure = true; }
  if (!__wired.weighbridge) { setupAutoWeighbridgeFetch();      __wired.weighbridge = true; }
}

// ======================= AUTO TOTAL CALCULATION =======================
function setupAutoTotalCalculation() {
  // Clear previous to avoid duplicates
  $(document).off("input.totals");

  const actualFields = [
    "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp",
    "wet_mix_bwg","dry_mix_bwg","wet_segregated_bwg","complete_mix_bwg"
  ];
  const reportedFields = [
    "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp"
  ];

  const watchFields = [...new Set([...actualFields, ...reportedFields])];
  const selector = watchFields.map(f => `[name="values[${f}]"]`).join(",");

  function safeNumber(v) { return parseFloat(v) || 0; }

  function calcTotals() {
    let totalActual = 0, totalReported = 0;

    actualFields.forEach(f => { totalActual   += safeNumber($(`[name="values[${f}]"]`).val()); });
    reportedFields.forEach(f => { totalReported += safeNumber($(`[name="values[${f}]"]`).val()); });

    const $act = $(`[name="values[total_waste_actual]"]`);
    const $rep = $(`[name="values[total_waste_reported]"]`);
    if ($act.length) $act.val(totalActual.toFixed(2));
    if ($rep.length) $rep.val(totalReported.toFixed(2));
  }

  $(document).on("input.totals", selector, function() {
    if (__progChange > 0) return;
    calcTotals();
  });

  calcTotals(); // initial (edit mode)
}

// ======================= AUTO RDF STOCK CALCULATION =======================
let LAST_RDF_STOCK = 0;

function fetchLastRdfStock(company_id, project_id, application_type, callback) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url: ajax_url,
    dataType: "json",
    data: { action: "get_last_rdf_stock", company_id, project_id, application_type }
  })
  .done(function (resp) {
    LAST_RDF_STOCK = (resp && resp.status && resp.data && resp.data.rdf_stock !== undefined)
      ? (parseFloat(resp.data.rdf_stock) || 0) : 0;
  })
  .always(function(){ if (typeof callback === "function") callback(LAST_RDF_STOCK); });
}

function setupAutoRdfStockCalculation() {
  $(document).off("input.rdf");
  function safeNumber(v) { return parseFloat(v) || 0; }
  function calcRdfStock() {
    const gen = safeNumber($(`[name="values[total_rdf_generation]"]`).val());
    const sold = safeNumber($(`[name="values[rdf_sold]"]`).val());
    const newStock = LAST_RDF_STOCK + gen - sold;
    const $stock = $(`[name="values[rdf_stock]"]`);
    if ($stock.length) $stock.val(newStock.toFixed(2));
  }
  $(document).on("input.rdf", `[name="values[total_rdf_generation]"],[name="values[rdf_sold]"]`, function(){
    if (__progChange > 0) return;
    calcRdfStock();
  });
  calcRdfStock();
}

// ======================= AUTO CBG CALCULATION =======================
let LAST_CBG_STOCK = 0;

function fetchLastCbgStock(company_id, project_id, application_type, callback) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url: ajax_url,
    dataType: "json",
    data: { action: "get_last_cbg_stock", company_id, project_id, application_type }
  })
  .done(function (resp) {
    LAST_CBG_STOCK = (resp && resp.status && resp.data && resp.data.cbg_stock !== undefined)
      ? (parseFloat(resp.data.cbg_stock) || 0) : 0;
  })
  .always(function(){ if (typeof callback === "function") callback(LAST_CBG_STOCK); });
}

function setupAutoCbgCalculation() {
  $(document).off("input.cbg");
  function safeNumber(v) { return parseFloat(v) || 0; }
  function calcCbg() {
    const P = safeNumber($(`[name="values[cbg_production_kg]"]`).val());
    const Q = safeNumber($(`[name="values[cbg_captive_vehicle]"]`).val());
    const X = safeNumber($(`[name="values[cbg_sold_vehicle]"]`).val());
    const Y = safeNumber($(`[name="values[cbg_sold_cascades]"]`).val());
    const Z = safeNumber($(`[name="values[cbg_sold_pipeline]"]`).val());
    const totalSold = X + Y + Z;
    const $totalSold = $(`[name="values[cbg_total_sold]"]`);
    if ($totalSold.length) $totalSold.val(totalSold.toFixed(2));
    const newStock = LAST_CBG_STOCK + P - Q - X - Y - Z;
    const $stock = $(`[name="values[cbg_stock]"]`);
    if ($stock.length) $stock.val(newStock.toFixed(2));
  }
  $(document).on("input.cbg", [
    "cbg_production_kg","cbg_captive_vehicle",
    "cbg_sold_vehicle","cbg_sold_cascades","cbg_sold_pipeline"
  ].map(f => `[name="values[${f}]"]`).join(","), function(){
    if (__progChange > 0) return;
    calcCbg();
  });
  calcCbg();
}

// ======================= AUTO MANURE CALCULATION =======================
let LAST_MANURE_STOCK = 0;

function fetchLastManureStock(company_id, project_id, application_type, callback) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url: ajax_url,
    dataType: "json",
    data: { action: "get_last_manure_stock", company_id, project_id, application_type }
  })
  .done(function (resp) {
    LAST_MANURE_STOCK = (resp && resp.status && resp.data && resp.data.manure_stock !== undefined)
      ? (parseFloat(resp.data.manure_stock) || 0) : 0;
  })
  .always(function(){ if (typeof callback === "function") callback(LAST_MANURE_STOCK); });
}

function setupAutoManureCalculation() {
  $(document).off("input.manure");
  function safeNumber(v) { return parseFloat(v) || 0; }
  function calcManureStock() {
    const R = safeNumber($(`[name="values[manure_production]"]`).val());
    const S = safeNumber($(`[name="values[manure_sold]"]`).val());
    const newStock = LAST_MANURE_STOCK + R - S;
    const $stock = $(`[name="values[manure_stock]"]`);
    if ($stock.length) $stock.val(newStock.toFixed(2));
  }
  $(document).on("input.manure", `[name="values[manure_production]"],[name="values[manure_sold]"]`, function(){
    if (__progChange > 0) return;
    calcManureStock();
  });
  calcManureStock();
}

// ======================= LOAD EXISTING ENTRY (EDIT MODE) =======================
function toWeekInputValue(entryDate, weekNo) {
  if (!weekNo) return "";
  let year = "";
  if (entryDate) {
    const d = new Date(entryDate);
    if (!isNaN(d)) year = d.getFullYear();
  }
  if (!year) year = new Date().getFullYear();
  return `${year}-W${String(weekNo).padStart(2, '0')}`;
}

function load_entry_values_if_any() {
  const uid = $("#unique_id").val();
  if (!uid) return;

  const ajax_url = sessionStorage.getItem("folder_crud_link");

  // Abort inflight get_entry
  if (__get_entry_req.xhr && __get_entry_req.xhr.readyState !== 4) {
    __get_entry_req.xhr.abort();
  }

  __get_entry_req.xhr = $.ajax({
    type: "POST",
    url : ajax_url,
    dataType: "json",
    data: { action: "get_entry", unique_id: uid }
  })
  .done(function(resp) {
    if (!resp.status || !resp.data) return;
    const row = resp.data;

    withProgrammaticChange(() => {
      if (row.entry_date && row.entry_date !== '0000-00-00') {
        $(`[name="values[date_field]"]`).val(row.entry_date);
      }
      if (row.week_no) {
        const wk = toWeekInputValue(row.entry_date, row.week_no);
        if (wk) $(`[name="values[week_field]"]`).val(wk);
      }

      const keys = [
        "automated_weighbridge",
        "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp",
        "wet_mix_bwg","dry_mix_bwg","wet_segregated_bwg","complete_mix_bwg",
        "total_waste_actual","total_waste_reported","organic_waste_feed",
        "recycles_generated","rejects_dry_segregation","rejects_wet_segregation",
        "total_inert_disposed","total_rdf_generation","rdf_sold","rdf_stock",
        "slurry_disposed","flare_hrs","cbg_compressor_hrs","raw_biogas_produced",
        "biogas_flared","captive_consumption_gas","digester_temp","fos_tac_ratio",
        "ph_value","cbg_production_kg","cbg_captive_vehicle","cbg_sold_vehicle",
        "cbg_sold_cascades","cbg_sold_pipeline","cbg_total_sold","cbg_stock",
        "manure_production","manure_sold","manure_stock","plant_incharge","remarks"
      ];
      keys.forEach(k => {
        const $el = $(`[name="values[${k}]"]`);
        if ($el.length) $el.val(row[k] ?? "");
      });
    });
  })
  .always(function(){
    __get_entry_req.xhr = null;
  });
}

// ======================= AUTO FETCH FROM WEIGHBRIDGE =======================
function setupAutoWeighbridgeFetch() {
  // clear old handlers
  $(document)
    .off('change.weighbridge', '[name="values[date_field]"]')
    .off('change.weighbridge', '[name="values[automated_weighbridge]"]')
    .off('input.mixsum',       '[name="values[dry_mix_corp]"], [name="values[wet_mix_corp]"], [name="values[wet_segregated_corp]"], [name="values[dry_mix_bwg]"], [name="values[wet_mix_bwg]"], [name="values[wet_segregated_bwg]"]');

  // 🔁 Now listen to Automated Weighment Date
  $(document).on('change.weighbridge', '[name="values[automated_weighbridge]"]', function() {
    if (__progChange > 0) return;
    const weighDate = $(this).val(); // YYYY-MM-DD
    if (!weighDate) return;

    const ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
      type: "POST",
      url: ajax_url,
      dataType: "json",
      // Backend currently expects `entry_date`. Send the automated date as that:
      data: { action: "fetch_weighbridge_data", automated_weighbridge: weighDate } 
      // If you prefer a new key (e.g., weigh_date), adjust backend accordingly.
    })
    .done(function (resp) {
      if (resp.status && resp.data) {
        const d = resp.data;
        withProgrammaticChange(() => {
          // Corp
          $('[name="values[dry_mix_corp]"]').val(d.dry_mix_corp);
          $('[name="values[wet_mix_corp]"]').val(d.wet_mix_corp);
          $('[name="values[wet_segregated_corp]"]').val(d.wet_segregated_corp);
          $('[name="values[complete_mix_corp]"]').val(d.complete_mix_corp);
          // BWG
          $('[name="values[dry_mix_bwg]"]').val(d.dry_mix_bwg);
          $('[name="values[wet_mix_bwg]"]').val(d.wet_mix_bwg);
          $('[name="values[wet_segregated_bwg]"]').val(d.wet_segregated_bwg);
          $('[name="values[complete_mix_bwg]"]').val(d.complete_mix_bwg);
        });
      }
    });
  });

  $(document).on(
    'input.mixsum',
    '[name="values[dry_mix_corp]"], [name="values[wet_mix_corp]"], [name="values[wet_segregated_corp]"], [name="values[dry_mix_bwg]"], [name="values[wet_mix_bwg]"], [name="values[wet_segregated_bwg]"]',
    function () {
      if (__progChange > 0) return;
      const getVal = (n) => parseFloat($(`[name="values[${n}]"]`).val()) || 0;

      const completeCorp = getVal("dry_mix_corp") + getVal("wet_mix_corp") + getVal("wet_segregated_corp");
      $('[name="values[complete_mix_corp]"]').val(completeCorp.toFixed(2));

      const completeBWG  = getVal("dry_mix_bwg") + getVal("wet_mix_bwg") + getVal("wet_segregated_bwg");
      $('[name="values[complete_mix_bwg]"]').val(completeBWG.toFixed(2));
    }
  );

  // readonly styling (idempotent)
  $('[name="values[complete_mix_corp]"], [name="values[complete_mix_bwg]"]')
    .attr("readonly", true)
    .css({ "background-color": "#f5f5f5", "cursor": "not-allowed" });
}

// ======================= FILTER HELPERS =======================
function load_projects_for_filter(company_id = "", selected = "") {
  if (!company_id) {
    withProgrammaticChange(() => {
      $('#flt_project').html('<option value="">All Projects</option>').trigger('change.select2');
    });
    return;
  }
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.post(ajax_url, { action: 'project_name', company_id: company_id, project: selected }, function (html) {
    html = html.replace('Select the Project Name', 'All Projects');
    withProgrammaticChange(() => {
      $('#flt_project').html(html).val(selected).trigger('change.select2');
    });
  });
}

function load_app_types_for_filter(project_id = "", company_id = "", selected = "") {
  if (!project_id) {
    withProgrammaticChange(() => {
      $('#flt_app_type').html('<option value="">All Types</option>').trigger('change.select2');
    });
    return;
  }
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.post(ajax_url, { action: 'application_type', company_id: company_id, project_id: project_id }, function (html) {
    if (!/All Types/.test(html)) html = '<option value="">All Types</option>' + html;
    withProgrammaticChange(() => {
      $('#flt_app_type').html(html).val(selected).trigger('change.select2');
    });
  });
}

// ======================= OPTIONAL: FUNCTION CALL COUNTERS =======================
(function () {
  window.__fnCallCounts = window.__fnCallCounts || {};
  function __bump(name){ window.__fnCallCounts[name] = (window.__fnCallCounts[name] || 0) + 1; }
  function __wrap(name){
    const fn = window[name];
    if (typeof fn !== 'function') return;
    window[name] = function(){ __bump(name); return fn.apply(this, arguments); };
  }
  [
    'requestDailylogsheetData',
    'get_dailylogsheet_data',
    'renderFromFlags',
    'setupAutoTotalCalculation',
    'fetchLastRdfStock','setupAutoRdfStockCalculation',
    'fetchLastCbgStock','setupAutoCbgCalculation',
    'fetchLastManureStock','setupAutoManureCalculation',
    'setupAutoWeighbridgeFetch',
    'toWeekInputValue','load_entry_values_if_any',
    'load_projects_for_filter','load_app_types_for_filter'
  ].forEach(__wrap);

  window.logFunctionCallCounts = function(){
    const entries = Object.entries(window.__fnCallCounts || {});
    if (!entries.length) { console.log('Function counters: no calls recorded.'); return; }
    const sorted = entries.map(([k,v]) => ({ function:k, calls:v })).sort((a,b)=>b.calls-a.calls);
    console.table(sorted);
    return sorted;
  };
  window.addEventListener('pagehide', window.logFunctionCallCounts);
})();
// </script>