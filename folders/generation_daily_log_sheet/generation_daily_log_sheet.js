$(document).ready(function () {
    // ✅ Initialize DataTable on page load
    init_datatable(table_id, form_name, action);

    // ✅ Go Button Click (apply filters)
    $("#btnFilter").on("click", function () {
        $("#" + table_id).DataTable().ajax.reload();
    });
});
$(document).on("click", "#btnReport", function () {
    window.open("index.php?file=generation_daily_log_sheet/report", "_blank");
});

// Custom identifiers
var form_name  = 'Mandi Gobindgad Daily Log Sheet';
var table_name = 'mandi_gobindgad_log';
var table_id   = 'mandi_gobindgad_log_datatable'; // ✅ updated to match HTML table
var action     = "datatable";

// ✅ Create / Update
function generation_daily_log_sheet_cu(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
        // ✅ Flowmeter validation
        let start = parseFloat($("#flowmeter_start").val()) || 0;
        let stop  = parseFloat($("#flowmeter_stop").val()) || 0;

        if (stop < start) {
            Swal.fire({
                icon: 'error',
                title: 'Validation Failed',
                text: '⚠️ Flowmeter Stop Reading must be greater than or equal to Start Reading.'
            });
            return false; // ⛔ stop here, don’t submit
        }

        // ✅ If valid, continue AJAX
        var data = $(".was-validated").serialize();
        data += "&unique_id=" + unique_id + "&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".createupdate_btn").attr("disabled", "disabled");
                $(".createupdate_btn").text("Loading...");
            },
            success: function (data) {
                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    if (msg === "duplicate_entry") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Duplicate Entry',
                            text: '⚠️ A record already exists for this date.',
                        });
                        $(".createupdate_btn").removeAttr("disabled");
                        $(".createupdate_btn").text("Save");
                        return;
                    }
                    url = '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                } else {
                    $(".createupdate_btn").removeAttr("disabled");
                    $(".createupdate_btn").text(unique_id ? "Update" : "Save");
                }
                sweetalert(msg, url);
            },
            error: function () {
                alert("Network Error");
            }
        });
    } else {
        sweetalert("form_alert");
    }
}

// ✅ Init DataTable with custom filter params
function init_datatable(table_id = '', form_name = '', action = '') {
    var table = $("#" + table_id);
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    table.DataTable({
        ordering: true,
        searching: true,
        destroy: true,
        ajax: {
            url: ajax_url,
            type: "POST",
            data: function (d) {
                d.action     = action;
                d.project_id = $("#filter_project_id").val();   // ✅ project filter
                d.from_date  = $("#filter_from_date").val();    // ✅ from date
                d.to_date    = $("#filter_to_date").val();      // ✅ to date
            }
        }
    });
}
// ✅ Delete
function generation_daily_log_sheet_delete(unique_id = "") {
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

// ✅ Date Validation (SweetAlert check)
$(document).on("change", "#filter_from_date, #filter_to_date", function () {
    let fromDate = $("#filter_from_date").val();
    let toDate   = $("#filter_to_date").val();

    if (fromDate && toDate && toDate < fromDate) {
        Swal.fire({
            icon: "warning",
            title: "Invalid Date Range",
            text: "⚠️ 'To Date' cannot be earlier than 'From Date'.",
        });

        // Reset to date to from date if invalid
        $("#filter_to_date").val(fromDate);
    }
});

