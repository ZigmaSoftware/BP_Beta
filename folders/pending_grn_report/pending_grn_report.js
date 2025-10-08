const ajax_url = sessionStorage.getItem("folder_crud_link");

$(document).ready(function() {
    
     $("#company_id").on("change", function() {
        // alert("trigger");
        let company_id = $(this).val();
        get_company_project(company_id);
    });
    
    if (!ajax_url) {
        console.error("ajax_url (folder_crud_link) is empty in sessionStorage");
    }

    var table = $('#pending_grn_datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": ajax_url,
            "type": "POST",
            "data": function(d) {
                d.action = "datatable";
                // send filters
                d.from_date  = $('#from_date').val();
                d.to_date    = $('#to_date').val();
                d.pr_status      = $('#pr_status').find(':selected').val();
                d.company_id = $('#company_id').val();
                d.project_id = $('#project_id').val();
                d.supplier_id = $('#supplier_id').val();
            }
        },
        "columns": [
            { "data": "s_no" },
            { "data": "unit" },
            { "data": "project_id" },
            { "data": "po_no" },
            { "data": "po_date" },
            { "data": "po_type" },
            { "data": "vendor_code" },
            { "data": "vendor_name" },

            // PO Item Details
            { "data": "item_code" },
            { "data": "item_name" },
            { "data": "quantity" },
            { "data": "received_qty" },
            { "data": "pending_qty" },
            { "data": "uom" },
            { "data": "rate" },
            { "data": "amount" },

            // GRN Details
            { "data": "grn_no" },
            { "data": "grn_date" },

            // Status
            { "data": "status" }
        ],
        "scrollX": true,
        "scrollCollapse": true,
        "responsive": false,
        "order": [[3, "desc"]],   // order by PO No

        // ✅ Length menu with All option
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "pageLength": 10,

        // ✅ Enable Buttons
        "dom": '<"d-flex justify-content-between align-items-center mb-2"lfB>rtip',
        "buttons": [
          {
            extend: 'excelHtml5',
            title: 'Pending GRN Report',
            className: 'btn btn-success me-2 text-end',
            customize: function (xlsx) {
                var sheet = xlsx.xl.worksheets['sheet1.xml'];
                // Loop through the rows to find the GRN Number and GRN Date
                $(sheet).find('row c[r^="r"]').each(function() {
                    var cell = $(this);
                    var cellValue = cell.text();
                    if (cellValue.includes('GRN No:')) {
                        var grn_no = cellValue.split(' ')[1] || '';
                        var grn_date = cellValue.split(' ')[2] || '';
                        var newValue = grn_no + '\n' + grn_date;
                        cell.text(newValue);
                    }
                });
            }
          },
          {
            extend: 'print',
            title: 'Pending GRN Report',
            className: 'btn btn-primary text-end',
            exportOptions: {
              stripHtml: false,
              columns: ':visible',
              modifier: { page: 'all' }
            },
            customize: function (win) {
              $(win.document.head).append(`
                <style>
                  @media print {
                    @page { size: A1 landscape; margin: 12mm; }
                    body { print-color-adjust: exact; -webkit-print-color-adjust: exact; }
                  }
                  table { width: 100% !important; table-layout: auto !important; border-collapse: collapse; }
                  thead th, tfoot th { position: static !important; }
                  th, td {
                    padding: 6px 8px !important;
                    vertical-align: top !important;
                    white-space: nowrap !important;
                    word-break: normal !important;
                    overflow-wrap: normal !important;
                    hyphens: manual !important;
                    font-size: 12pt;
                  }
                  table.dataTable tbody tr:nth-child(even) td { background: #f8f9fa; }
                  table.dataTable tbody tr:nth-child(odd)  td { background: #fff; }
                </style>
              `);

              $(win.document.body).find('div.dataTables_info').remove();
              $(win.document.body).find('thead, tfoot').show();
            }
          }
        ]
    });

    // Reload on filter button (your view uses #goBtn)
    $('#goBtn').on('click', function() {
        // reload and keep current page (false)
        table.ajax.reload(null, false);
    });

    // Optional: trigger filter when Enter pressed in selects (if desired)
    $('#company_id, #project_id, #pr_number').on('keypress', function(e) {
        if (e.which === 13) {
            table.ajax.reload(null, false);
        }
    });

    // If you later want auto reload when selecting (select2 change),
    // use: $('#company_id, #project_id, #pr_number').on('change', () => table.ajax.reload(null,false));
});


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

