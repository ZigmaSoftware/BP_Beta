// ==========================
// 🧠 INITIALIZATION
// ==========================
let ajax_url = sessionStorage.getItem("folder_crud_link");
let list_url = sessionStorage.getItem("list_link");

var form_name = "impact_type";
var table_id  = "impact_datatable";
var action    = "datatable";

$(document).ready(function () {
    init_datatable(table_id, form_name, action);
});

// ==========================
// 💾 CREATE / UPDATE
// ==========================
function impact_type_cu() {
    let formdata = new FormData($("#impact_type_form")[0]);
    formdata.append("action", "createupdate");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: formdata,
        processData: false,
        contentType: false,
        success: function (response) {
            console.log(response);

            let res = (typeof response === "string") ? JSON.parse(response) : response;

            if (res.status == 1) {
                Swal.fire({
                    title: "Success",
                    text: res.error || "Impact Type saved successfully!",
                    icon: "success",
                    confirmButtonText: "OK",
                    timer: 1800,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = list_url;
                });
            } else {
                Swal.fire({
                    title: "Failed",
                    text: res.error || "Unable to save Impact Type.",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            }
        },
        error: function (xhr, status, error) {
            Swal.fire({
                title: "AJAX Error",
                text: "Something went wrong: " + error,
                icon: "error",
                confirmButtonText: "OK"
            });
        }
    });
}

// ==========================
// 📊 INIT DATATABLE
// ==========================
function init_datatable(table_id = "", form_name = "", action = "", filter_data = {}) {
    let table = $("#" + table_id);

    // Prevent duplicate initialization
    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }

    // Base data for request (avoid ES6 spread for old browsers)
    let data = Object.assign({ action: action }, filter_data || {});

    table.DataTable({
        ordering: true,
        searching: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: ajax_url,
            type: "POST",
            data: data
        },
        columns: [
            { data: "s_no", title: "S.No", width: "5%" },
            { data: "impact_type", title: "Impact Type Name" },
            { data: "description", title: "Description" },
            { data: "status_label", title: "Status", className: "text-center" },
            { data: "action", title: "Action", className: "text-center", orderable: false, searchable: false }
        ],
        order: [[0, "asc"]],
        responsive: true,
        language: {
            emptyTable: "No Impact Types found"
        }
    });
}

// ==========================
// 🔍 FILTER FUNCTION (optional)
// ==========================
function impact_type_filter() {
    // In this module, there’s no department filter, 
    // but keeping structure for future expansion.
    init_datatable(table_id, form_name, action);
}

// ==========================
// 🗑️ TOGGLE ACTIVE/INACTIVE
// ==========================
function impact_type_toggle(unique_id, current_status) {
    if (!unique_id) {
        Swal.fire({
            title: "Invalid Action",
            text: "Unique ID missing — cannot change status.",
            icon: "warning",
            confirmButtonText: "OK"
        });
        return;
    }

    let isDeactivate = (current_status == 0); // 1 = active → deactivate
    let actionText   = isDeactivate ? "Deactivate" : "Activate";
    let confirmIcon  = isDeactivate ? "⚠️" : "✅";

    Swal.fire({
        title: `${actionText} Impact Type?`,
        text: `Are you sure you want to ${actionText.toLowerCase()} this Impact Type?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: isDeactivate ? "#d33" : "#3085d6",
        cancelButtonColor: "#6c757d",
        confirmButtonText: `${confirmIcon} Yes, ${actionText}`,
        cancelButtonText: "Cancel"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "toggle",
                    unique_id: unique_id,
                    mode: isDeactivate ? "deactivate" : "activate"
                },
                success: function (response) {
                    let res = (typeof response === "string") ? JSON.parse(response) : response;

                    if (res.status == 1) {
                        Swal.fire({
                            title: "Success",
                            text: res.error || `Impact Type ${actionText.toLowerCase()}d successfully.`,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        });

                        setTimeout(() => {
                            $("#" + table_id).DataTable().ajax.reload(null, false);
                        }, 800);
                    } else {
                        Swal.fire({
                            title: "Failed",
                            text: res.error || "Unable to update Impact Type status.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                },
                error: function (xhr, status, error) {
                    Swal.fire({
                        title: "Network Error",
                        text: "Something went wrong: " + error,
                        icon: "error",
                        confirmButtonText: "OK"
                    });
                }
            });
        }
    });
}
