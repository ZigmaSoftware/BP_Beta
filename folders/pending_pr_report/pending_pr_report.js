const ajax_url = sessionStorage.getItem("folder_crud_link");

$(document).ready(function() {
    $("#company_id").on("change", function() {
        // alert("trigger");
        let company_id = $(this).val();
        get_company_project(company_id);
    });
    
    var table = $('#pending_pr_datatable').DataTable({
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": ajax_url,
            "type": "POST",
            "data": function(d) {
                d.action         = "datatable";
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
            { "data": "pr_number" },
            { "data": "requisition_date" },
            { "data": "requisition_type" },
            { "data": "requisition_for" },
            { "data": "reference" },
            { "data": "item_code" },
            { "data": "item_name" },
            { "data": "qty" },
            { "data": "uom" },
            { "data": "pending_qty" },
            { "data": "remarks" },
            { "data": "prepared_by" },
            { "data": "prepared_dt" },
            { "data": "authorized_by" },
            { "data": "authorized_date" },
            { "data": "authorized_status" }
        ],
        "scrollX": true,
        "responsive": false,
        "order": [[2, "desc"]],
        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
        "pageLength": 10,
        "dom": '<"d-flex justify-content-between align-items-center mb-2"lfB>rtip',
        "buttons": [
            { extend: 'excelHtml5', title: 'Pending PR Report', className: 'btn btn-success me-2' },
            { extend: 'print', title: 'Pending PR Report', className: 'btn btn-primary',
              exportOptions: { modifier: { page: 'all' } },
              customize: function (win) {
                  $(win.document.head).append(`
                      <style>
                          @page { size: A2 landscape; }
                          body { font-size: 12pt; }
                          table { width: 100% !important; border-collapse: collapse; }
                          th, td { padding: 6px 8px !important; white-space: nowrap !important; }
                      </style>
                  `);
              }
            }
        ]
    });

    // ✅ Reload table when Go button clicked
    $('#filterBtn').on('click', function() {
        table.ajax.reload();
    });
    
    
    
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
