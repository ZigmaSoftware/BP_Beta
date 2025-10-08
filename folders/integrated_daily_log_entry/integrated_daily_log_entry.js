// Form/Table variables
var form_name = 'Integrated Daily Logsheet';
var table_id  = 'Integrated_daily_log_entry_master_datatable';
var action    = "datatable";
var dt; // keep reference


$(document).ready(function () {
  init_datatable(table_id, form_name, action);
  let company_name = $("#company_name").val();
  get_project_name(company_name);
});

$(document).ready(function () {
  init_datatable(table_id, form_name, action);

  // load projects when company changes
  $('#flt_company').on('change', function () {
    const company_id = $(this).val() || '';
    load_projects_for_filter(company_id, '');
    // reset app types
    $('#flt_app_type').html('<option value="">All Types</option>');
  });

  // load app types when project changes
  $('#flt_project').on('change', function () {
    const company_id = $('#flt_company').val() || '';
    const project_id = $(this).val() || '';
    load_app_types_for_filter(project_id, company_id, '');
  });

  // Go/Reset
  $('#flt_go').on('click', function () {
    $('#' + table_id).DataTable().ajax.reload();
  });
  
  $('#flt_report').on('click', function () {
  const from_date  = $('#flt_from_date').val() || '';
  const to_date    = $('#flt_to_date').val()   || '';
  const company_id = $('#flt_company').val()   || '';
  const project_id = $('#flt_project').val()   || '';
  const app_type   = $('#flt_app_type').val()  || '';

  let qs = {};
  if (from_date)  qs.from_date = from_date;
  if (to_date)    qs.to_date = to_date;
  if (company_id) qs.company_id = company_id;
  if (project_id) qs.project_id = project_id;
  if (app_type)   qs.application_type = app_type;

  let url = 'index.php?file=integrated_daily_log_entry/report';

  // only append filters if any are set
  if (Object.keys(qs).length > 0) {
    url += '&' + $.param(qs);
  }

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

        // Finally load flags + render inputs using the selected value
        const company_id_val = $("#company_name").val();
        const app_val = $("#application_type").val();
        if (project_id && app_val && company_id_val) {
          get_dailylogsheet_data(project_id, app_val, company_id_val);
        }
      }
    });
  } else {
    $("#application_type").html('<option value="">Select Type</option>');
  }
}

/* ======================= GET FLAGS + RENDER DYNAMIC INPUTS ======================= */
function get_dailylogsheet_data(project_id = "", application_type = "", company_id = "") {
  if (!project_id || !application_type || !company_id) {
    $("input[type=checkbox].field-checkbox").prop("checked", false);
    $('#dailylog_dynamic_fields').empty();
    return;
  }

  var ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url : ajax_url,
    dataType: "json",
    data: { company_id, project_id, application_type, action: "dailylogsheet_data" },
    success: function (resp) {
      if (resp.status) {
        // sync checkboxes (if displayed)
        Object.keys(FIELD_CONFIG).forEach(k => {
          const checked = +resp.data[k] === 1;
          $(`#${k}.field-checkbox`).prop('checked', checked);
        });
        renderFromFlags(resp.data);
        load_entry_values_if_any();   // <— add this
      } else {
        $("input[type=checkbox].field-checkbox").prop("checked", false);
        $('#dailylog_dynamic_fields').html('<div class="col-12 text-muted">No fields configured for this selection.</div>');
      }
    },
    error: function(xhr, status, error) {
      console.error("AJAX Error:", status, error);
      $('#dailylog_dynamic_fields').html('<div class="col-12 text-danger">Failed to load configuration.</div>');
    }
  });
}

/* ======================= UI CONFIG FOR DYNAMIC FIELDS ======================= */
const FIELD_CONFIG = {
  date_field:             { label: 'Date',                       type: 'date' },
  week_field:             { label: 'Week',                       type: 'week' },

  // NOT a checkbox — treat as normal field
  automated_weighbridge:  { label: 'Automated Weighbridge',      type: 'text' },

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
  const required = 'required'; // tweak per-field if needed
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

function renderFromFlags(flags) {
  const $root = $('#dailylog_dynamic_fields').empty();
  Object.keys(FIELD_CONFIG).forEach(key => {
    if (+flags[key] === 1) {
      $root.append(buildField(key, FIELD_CONFIG[key]));
    }
  });
}
/* ======================= AUTO TOTAL CALCULATION ======================= */
function setupAutoTotalCalculation() {
  const actualFields = [
    "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp",
    "wet_mix_bwg","dry_mix_bwg","wet_segregated_bwg","complete_mix_bwg"
  ];
  const reportedFields = [
    "dry_mix_corp","wet_mix_corp","wet_segregated_corp","complete_mix_corp"
  ];

  function safeNumber(v) {
    return parseFloat(v) || 0;
  }

  function calcTotals() {
    let totalActual = 0, totalReported = 0;

    actualFields.forEach(f => {
      totalActual += safeNumber($(`[name="values[${f}]"]`).val());
    });

    reportedFields.forEach(f => {
      totalReported += safeNumber($(`[name="values[${f}]"]`).val());
    });

    // Only update if those fields exist
    const $act = $(`[name="values[total_waste_actual]"]`);
    const $rep = $(`[name="values[total_waste_reported]"]`);
    if ($act.length) $act.val(totalActual.toFixed(2));
    if ($rep.length) $rep.val(totalReported.toFixed(2));
  }

  // Watch for input changes
  const watchFields = [...new Set([...actualFields, ...reportedFields])];
  $(document).on("input", watchFields.map(f => `[name="values[${f}]"]`).join(","), calcTotals);

  // Initial compute (in case of edit mode)
  calcTotals();
}

/* --- Extend renderFromFlags to trigger auto total setup --- */
const _renderFromFlags = renderFromFlags;
renderFromFlags = function(flags) {
  _renderFromFlags(flags);
  setupAutoTotalCalculation();
};

/* ======================= AUTO RDF STOCK CALCULATION ======================= */
let LAST_RDF_STOCK = 0; // store previous day's RDF stock

// Fetch last RDF stock from backend for this project/app/company
function fetchLastRdfStock(company_id, project_id, application_type, callback) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url: ajax_url,
    dataType: "json",
    data: {
      action: "get_last_rdf_stock",
      company_id,
      project_id,
      application_type
    },
    success: function (resp) {
      if (resp.status && resp.data && resp.data.rdf_stock !== undefined) {
        LAST_RDF_STOCK = parseFloat(resp.data.rdf_stock) || 0;
      } else {
        LAST_RDF_STOCK = 0;
      }
      if (typeof callback === "function") callback(LAST_RDF_STOCK);
    },
    error: function () {
      LAST_RDF_STOCK = 0;
      if (typeof callback === "function") callback(LAST_RDF_STOCK);
    }
  });
}



// Setup real-time calculation
function setupAutoRdfStockCalculation() {
  function safeNumber(v) {
    return parseFloat(v) || 0;
  }

  function calcRdfStock() {
    const gen = safeNumber($(`[name="values[total_rdf_generation]"]`).val());
    const sold = safeNumber($(`[name="values[rdf_sold]"]`).val());
    const newStock = LAST_RDF_STOCK + gen - sold;

    const $stock = $(`[name="values[rdf_stock]"]`);
    if ($stock.length) $stock.val(newStock.toFixed(2));
  }

  // Watch fields
  $(document).on("input", `[name="values[total_rdf_generation]"],[name="values[rdf_sold]"]`, calcRdfStock);

  // Initial calculation (if in edit mode)
  calcRdfStock();
}


/* ======================= AUTO CBG CALCULATION ======================= */
let LAST_CBG_STOCK = 0; // previous day's CBG stock

// Fetch last CBG stock from backend
function fetchLastCbgStock(company_id, project_id, application_type, callback) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url: ajax_url,
    dataType: "json",
    data: {
      action: "get_last_cbg_stock",
      company_id,
      project_id,
      application_type
    },
    success: function (resp) {
      if (resp.status && resp.data && resp.data.cbg_stock !== undefined) {
        LAST_CBG_STOCK = parseFloat(resp.data.cbg_stock) || 0;
      } else {
        LAST_CBG_STOCK = 0;
      }
      if (typeof callback === "function") callback(LAST_CBG_STOCK);
    },
    error: function () {
      LAST_CBG_STOCK = 0;
      if (typeof callback === "function") callback(LAST_CBG_STOCK);
    }
  });
}

function setupAutoCbgCalculation() {
  function safeNumber(v) { return parseFloat(v) || 0; }

  function calcCbg() {
    const P = safeNumber($(`[name="values[cbg_production_kg]"]`).val());
    const Q = safeNumber($(`[name="values[cbg_captive_vehicle]"]`).val());
    const X = safeNumber($(`[name="values[cbg_sold_vehicle]"]`).val());
    const Y = safeNumber($(`[name="values[cbg_sold_cascades]"]`).val());
    const Z = safeNumber($(`[name="values[cbg_sold_pipeline]"]`).val());

    // Total Sold = X + Y + Z
    const totalSold = X + Y + Z;
    const $totalSold = $(`[name="values[cbg_total_sold]"]`);
    if ($totalSold.length) $totalSold.val(totalSold.toFixed(2));

    // Stock = LastDayStock + P - Q - X - Y - Z
    const newStock = LAST_CBG_STOCK + P - Q - X - Y - Z;
    const $stock = $(`[name="values[cbg_stock]"]`);
    if ($stock.length) $stock.val(newStock.toFixed(2));
  }

  // Watch for any field that affects totals
  const fields = [
    "cbg_production_kg","cbg_captive_vehicle",
    "cbg_sold_vehicle","cbg_sold_cascades","cbg_sold_pipeline"
  ];
  $(document).on("input", fields.map(f => `[name="values[${f}]"]`).join(","), calcCbg);

  calcCbg(); // initial run
}



/* ======================= AUTO MANURE CALCULATION ======================= */
let LAST_MANURE_STOCK = 0;

// Fetch last manure stock from backend
function fetchLastManureStock(company_id, project_id, application_type, callback) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");
  $.ajax({
    type: "POST",
    url: ajax_url,
    dataType: "json",
    data: {
      action: "get_last_manure_stock",
      company_id,
      project_id,
      application_type
    },
    success: function (resp) {
      if (resp.status && resp.data && resp.data.manure_stock !== undefined) {
        LAST_MANURE_STOCK = parseFloat(resp.data.manure_stock) || 0;
      } else {
        LAST_MANURE_STOCK = 0;
      }
      if (typeof callback === "function") callback(LAST_MANURE_STOCK);
    },
    error: function () {
      LAST_MANURE_STOCK = 0;
      if (typeof callback === "function") callback(LAST_MANURE_STOCK);
    }
  });
}

// Real-time manure stock update
function setupAutoManureCalculation() {
  function safeNumber(v) { return parseFloat(v) || 0; }

  function calcManureStock() {
    const R = safeNumber($(`[name="values[manure_production]"]`).val());
    const S = safeNumber($(`[name="values[manure_sold]"]`).val());
    const newStock = LAST_MANURE_STOCK + R - S;

    const $stock = $(`[name="values[manure_stock]"]`);
    if ($stock.length) $stock.val(newStock.toFixed(2));
  }

  // Watch both fields
  $(document).on("input", `[name="values[manure_production]"],[name="values[manure_sold]"]`, calcManureStock);

  // Initial compute (edit mode)
  calcManureStock();
}


/* --- Extend renderFromFlags again to include manure setup --- */
const ____renderFromFlags = renderFromFlags;
renderFromFlags = function(flags) {
  ____renderFromFlags(flags);
  setupAutoTotalCalculation();

  const company_id = $("#company_name").val();
  const project_id = $("#project_name").val();
  const app_type   = $("#application_type").val();

  if (project_id && company_id && app_type) {
    fetchLastRdfStock(company_id, project_id, app_type, function() {
      setupAutoRdfStockCalculation();
      fetchLastCbgStock(company_id, project_id, app_type, function() {
        setupAutoCbgCalculation();
        fetchLastManureStock(company_id, project_id, app_type, function() {
          setupAutoManureCalculation();
        });
      });
    });
  } else {
    setupAutoRdfStockCalculation();
    setupAutoCbgCalculation();
    setupAutoManureCalculation();
  }
};

/* --- Extend renderFromFlags again to include CBG setup --- */
const ___renderFromFlags = renderFromFlags;
renderFromFlags = function(flags) {
  ___renderFromFlags(flags);
  setupAutoTotalCalculation();

  const company_id = $("#company_name").val();
  const project_id = $("#project_name").val();
  const app_type   = $("#application_type").val();

  if (project_id && company_id && app_type) {
    fetchLastRdfStock(company_id, project_id, app_type, function() {
      setupAutoRdfStockCalculation();
      fetchLastCbgStock(company_id, project_id, app_type, function() {
        setupAutoCbgCalculation();
      });
    });
  } else {
    setupAutoRdfStockCalculation();
    setupAutoCbgCalculation();
  }
};


/* --- Extend renderFromFlags again to include RDF stock setup --- */
const __renderFromFlags = renderFromFlags;
renderFromFlags = function(flags) {
  __renderFromFlags(flags);
  setupAutoTotalCalculation();

  const company_id = $("#company_name").val();
  const project_id = $("#project_name").val();
  const app_type   = $("#application_type").val();

  if (project_id && company_id && app_type) {
    fetchLastRdfStock(company_id, project_id, app_type, function() {
      setupAutoRdfStockCalculation();
    });
  } else {
    setupAutoRdfStockCalculation(); // fallback if IDs missing
  }
};


/* ============ “Select All” and manual checkbox sync (only if flags are visible) ============ */
$(document).on('change', '#select_all', function() {
  const checked = $(this).is(':checked');
  $('.field-checkbox').prop('checked', checked);
  renderFromCheckboxes();
});

function renderFromCheckboxes() {
  const flags = {};
  Object.keys(FIELD_CONFIG).forEach(k => {
    flags[k] = $(`#${k}.field-checkbox`).is(':checked') ? 1 : 0;
  });
  renderFromFlags(flags);
}

$(document).on('change', '.field-checkbox', function() {
  renderFromCheckboxes();
});

$(function(){
  renderFromCheckboxes(); // initial
});

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
  $.ajax({
    type: "POST",
    url : ajax_url,
    dataType: "json",
    data: { action: "get_entry", unique_id: uid },
    success: function(resp) {
      if (!resp.status || !resp.data) return;
      const row = resp.data;

      // date_field → entry_date
      if (row.entry_date && row.entry_date !== '0000-00-00') {
        $(`[name="values[date_field]"]`).val(row.entry_date);
      }

      // week_field → week_no
      if (row.week_no) {
        const wk = toWeekInputValue(row.entry_date, row.week_no);
        if (wk) $(`[name="values[week_field]"]`).val(wk);
      }

      // All other numeric/text fields (only if that input exists on page)
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
    }
  });
}

function load_projects_for_filter(company_id = "", selected = "") {
  if (!company_id) {
    $('#flt_project').html('<option value="">All Projects</option>').trigger('change.select2');
    return;
  }
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  $.post(ajax_url, { action: 'project_name', company_id: company_id, project: selected }, function (html) {
    // html is <option> list with “Select the Project Name” — change that to “All Projects”
    html = html.replace('Select the Project Name', 'All Projects');
    $('#flt_project').html(html).val(selected);
    $('#flt_project').trigger('change.select2');
  });
}

function load_app_types_for_filter(project_id = "", company_id = "", selected = "") {
  if (!project_id) {
    $('#flt_app_type').html('<option value="">All Types</option>').trigger('change.select2');
    return;
  }
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  $.post(ajax_url, { action: 'application_type', company_id: company_id, project_id: project_id }, function (html) {
    // Ensure there is an "All Types" option at top
    if (!/All Types/.test(html)) html = '<option value="">All Types</option>' + html;
    $('#flt_app_type').html(html).val(selected).trigger('change.select2');
  });
}