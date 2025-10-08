const ajax_url = sessionStorage.getItem("folder_crud_link");

function get_company_project(company_id = "") {
    const ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        url: ajax_url,
        type: "POST",
        data: {
            action: "company_project",
            company_id: company_id
        },
        success: function(response) {
            // response will be <option>…</option>
            $("#project_id").html(response);

            // if using select2, refresh it
            if ($("#project_id").hasClass("select2")) {
                $("#project_id").trigger("change.select2");
            }
        },
        error: function(xhr, status, error) {
            console.error("get_company_project error:", error);
        }
    });
}


$(document).ready(function() {
        $("#company_id").on("change", function() {
        let company_id = $(this).val();
        get_company_project(company_id);
    });

    var table = $('#complete_pr_datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": ajax_url,
            "type": "POST",
            "data": function(d) {
                d.action      = "datatable";
                d.from_date      = $('#from_date').val();
                d.to_date        = $('#to_date').val();
                d.pr_status      = $('#pr_status').find(':selected').val();
                d.company_id     = $('#company_id').val() || $('#company_name').val(); // if using two company selectors
                d.project_id     = $('#project_id').val() || $('#project_name').val();
            }
        },
        "columns": [
            { "data": "s_no" },
            { "data": "unit" },
            { "data": "project_id" },
            { "data": "indent_no" },
            { "data": "indent_date" },
            { "data": "requisition_type" },
            { "data": "requisition_for" },
            { "data": "ref_so" },
            { "data": "doc_status" },
            { "data": "item_status" },
            { "data": "item_code" },
            { "data": "item_name" },
            { "data": "s_qty" },
            { "data": "uom" },
            { "data": "po_no" },
            { "data": "po_final_status" },
            { "data": "l1_action_by" },
            { "data": "l2_action_by" },
            { "data": "l3_action_by" },
            { "data": "po_qty" },
            { "data": "vendor_name" },
            { "data": "grn_no" },
            { "data": "grn_date" },
            { "data": "prepared_by" },
            { "data": "prepared_dt" },
            { "data": "authorized_by" },
            { "data": "authorized_dt" },
            { "data": "authorized_status" }
        ],
        "scrollX": true,
        "scrollCollapse": true,
        "responsive": false,
        "order": [[3, "desc"]],   // order by Indent No

        // ✅ Length menu with All option
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "pageLength": 10,

        // ✅ Enable Buttons
        "dom": '<"d-flex justify-content-between align-items-center mb-2"lfB>rtip',
        "buttons": [
          {
            extend: 'excelHtml5',
            title: 'Complete Purchase Requisition Report',
            className: 'btn btn-success me-2 text-end',
            exportOptions: {
              columns: ':visible',
              // Convert <br> to real newlines for Excel; strip any other tags
              format: {
                body: function (data) {
                  if (typeof data !== 'string') return data;
                  return data
                    .replace(/<br\s*\/?>/gi, '\r\n')
                    .replace(/<[^>]+>/g, '');
                }
              }
            }
          },
          {
            extend: 'print',
            title: 'Complete Purchase Requisition Report',
            className: 'btn btn-primary text-end',
            exportOptions: {
              columns: ':visible',
              stripHtml: false,                 // ✅ keep <br> so print matches browser
              modifier: { page: 'all' }
            },
            customize: function (win) {
              $(win.document.head).append(`
                <style>
                  @media print {
                    @page { size: A1 landscape; margin: 12mm; }
                    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                  }
                  body { font-size: 12pt; }
                  table { width: 100% !important; table-layout: auto !important; border-collapse: collapse; }
                  thead th, tfoot th { position: static !important; }
                  th, td {
                    padding: 6px 8px !important;
                    vertical-align: top !important;
                    white-space: nowrap !important;    /* only your <br> can break lines */
                    word-break: normal !important;      /* no mid-word splits */
                    overflow-wrap: normal !important;
                    hyphens: manual !important;
                    font-size: 12pt;
                  }
                  table.dataTable tbody tr:nth-child(even) td { background: #f8f9fa; }
                  table.dataTable tbody tr:nth-child(odd)  td { background: #fff; }
                </style>
              `);
        
              // widen Vendor & GRN columns a bit on paper (adjust indices if needed)
              $(win.document.head).append(
                '<style>td:nth-child(18){min-width:220px!important} td:nth-child(19),td:nth-child(20){min-width:180px!important}</style>'
              );
        
              // tidy print DOM
              $(win.document.body).find('div.dataTables_info').remove();
              $(win.document.body).find('thead, tfoot').show();
            }
          }
        ]
    });

    // ✅ Reload on filter button
    $('#filterBtn').on('click', function() {
        table.ajax.reload();
    });
});
