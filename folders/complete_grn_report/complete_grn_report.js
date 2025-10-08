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

    var table = $('#complete_grn_datatable').DataTable({
        "processing": true,
        "serverSide": true,
                "ajax": {
            "url": ajax_url,
            "type": "POST",
            "data": function(d) {
                d.action     = "datatable";
                d.from_date  = $('#from_date').val();
                d.to_date    = $('#to_date').val();
                d.company_id = $('#company_id').val();
                d.project_id = $('#project_id').val();
                d.grn_status     = $('#grn_status').val();
            }
        },
        "columns": [
            { "data": "s_no" },
            { "data": "grn_no" },
            { "data": "grn_date" },
            { "data": "unit" },
            { "data": "project_id" },
            { "data": "vendor_name" },
            { "data": "supplier_invoice" },
            { "data": "invoice_date" },
            { "data": "challan_no" },
            { "data": "eway_bill_no" },
            { "data": "po_no" },
            { "data": "item_code" },
            { "data": "item_name" },
            { "data": "po_qty" },
            { "data": "accepted_qty" },
            { "data": "rejected_qty" },
            { "data": "pending_qty" },
            { "data": "uom" },
            { "data": "rate" },
            { "data": "total_value" },
            { "data": "prepared_by" },
            { "data": "authorized_by" },
            { "data": "status" }
        ],
        "scrollX": true,
        "scrollCollapse": true,
        "responsive": false,
        "order": [[1, "desc"]],
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "pageLength": 10,
        "dom": '<"d-flex justify-content-between align-items-center mb-2"lfB>rtip',
        "buttons": [
          {
            extend: 'excelHtml5',
            title: 'Complete GRN Report',
            className: 'btn btn-success me-2 text-end'
          },
          {
            extend: 'print',
            title: 'Complete GRN Report',
            className: 'btn btn-primary text-end',
            exportOptions: { stripHtml: false, columns: ':visible', modifier: { page: 'all' } },
            customize: function (win) {
              $(win.document.head).append(`<style>
                  @media print { @page { size: A1 landscape; margin: 12mm; } body { print-color-adjust: exact; -webkit-print-color-adjust: exact; } }
                  table { width: 100% !important; table-layout: auto !important; border-collapse: collapse; }
                  thead th, tfoot th { position: static !important; }
                  th, td { padding: 6px 8px !important; vertical-align: top !important; white-space: nowrap !important; word-break: normal !important; overflow-wrap: normal !important; hyphens: manual !important; font-size: 12pt; }
                  table.dataTable tbody tr:nth-child(even) td { background: #f8f9fa; }
                  table.dataTable tbody tr:nth-child(odd) td { background: #fff; }
                </style>`);
              $(win.document.body).find('div.dataTables_info').remove();
              $(win.document.body).find('thead, tfoot').show();
            }
          }
        ]
    });

    // Reload table on Go button click
    $('#goBtn').on('click', function() {
        table.ajax.reload();
    });
});
