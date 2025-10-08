$(document).ready(function () {
    // ✅ Initialize DataTable on page load
    init_datatable(table_id, form_name, action);

    // ✅ Go Button Click (apply filters)
    $("#btnFilter").on("click", function () {
        $("#" + table_id).DataTable().ajax.reload();
    });
});

$(document).on("click", "#btnReport", function () {
    window.open("index.php?file=cbg_daily_log/report", "_blank");
});

// Custom identifiers
var form_name  = 'CBG Daily Log Sheet';
var table_name = 'cbg_daily_log';
var table_id   = 'cbg_daily_log_datatable';
var action     = "datatable";

// ✅ Create / Update
function cbg_daily_log_cu(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
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
function cbg_daily_log_delete(unique_id = "") {
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


$(document).ready(function() {

    // 🔹 Total Flow = Start Reading - End Reading
    $("#start_reading, #end_reading").on("input", function() {
        let start = parseFloat($("#start_reading").val()) || 0;
        let stop  = parseFloat($("#end_reading").val()) || 0;
        let total = start - stop;   // ✅ Start - Stop
        $("#total_reading").val(total.toFixed(2));
    });


    // 🔹 CBG Running Hrs = Stop Time - Start Time
    $("#cbg_start_time, #cbg_stop_time").on("change", function() {
        let start = $("#cbg_start_time").val();
        let stop  = $("#cbg_stop_time").val();

        if (start && stop) {
            let diff = (new Date("1970-01-01T" + stop + "Z") - new Date("1970-01-01T" + start + "Z")) / 1000 / 3600;
            if (diff < 0) diff += 24; // handle midnight crossover
            $("#cbg_running_hrs").val(diff.toFixed(2));
        }
    });

    // 🔹 Comp Total Run Hrs = Stop Time - Start Time
    $("#comp_start_time, #comp_stop_time").on("change", function() {
        let start = $("#comp_start_time").val();
        let stop  = $("#comp_stop_time").val();

        if (start && stop) {
            let diff = (new Date("1970-01-01T" + stop + "Z") - new Date("1970-01-01T" + start + "Z")) / 1000 / 3600;
            if (diff < 0) diff += 24; // handle midnight crossover
            $("#comp_total_run_hrs").val(diff.toFixed(2));
        }
    });

    // 🔹 Balance Cascade Pressure = Start - Stop
    $("#start_cascade_pressure, #stop_cascade_pressure").on("input", function() {
        let start = parseFloat($("#start_cascade_pressure").val()) || 0;
        let stop  = parseFloat($("#stop_cascade_pressure").val()) || 0;
        let balance = start - stop;
        $("#balance_cascade_pressure").val(balance.toFixed(2));
    });

});


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