$(document).ready(function () {
    init_datatable(table_id, form_name, action);
});

// ==========================================================
// ✅ Basic Config
// ==========================================================
var company_name   = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_address");
var company_phone  = sessionStorage.getItem("company_phone");
var company_email  = sessionStorage.getItem("company_email");
var company_logo   = sessionStorage.getItem("company_logo");

var form_name   = 'shift creation';
var form_header = '';
var form_footer = '';
var table_name  = '';
var table_id    = 'shift_creation_datatable';
var action      = 'datatable';

// ==========================================================
// ✅ Create / Update
// ==========================================================
function shift_creation_cu(unique_id = "") {

    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
        
        calculateShiftDuration();
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
                    url = '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg == "already") {
                        url = '';
                        $(".createupdate_btn").removeAttr("disabled");
                        if (unique_id) {
                            $(".createupdate_btn").text("Update");
                        } else {
                            $(".createupdate_btn").text("Save");
                        }
                    }
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

// ==========================================================
// ✅ DataTable Init
// ==========================================================
function init_datatable(table_id = '', form_name = '', action = '') {

    var table = $("#" + table_id);
    var data = {
        "action": action,
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var datatable = table.DataTable({
        ordering: true,
        searching: true,
        "ajax": {
            url: ajax_url,
            type: "POST",
            data: data
        },
        columns: [
            { title: "S.No" },
            { title: "Shift Name" },
            { title: "Start Time" },
            { title: "End Time" },
            { title: "Shift Duration" },  
            { title: "Description" },
            { title: "Actions" }
        ]
    });
}

// ==========================================================
// ✅ Toggle Active / Inactive
// ==========================================================
function shift_creation_toggle(unique_id = "", new_status = 0) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            unique_id: unique_id,
            action: "toggle",
            is_active: new_status
        },
        success: function (data) {
            const obj = JSON.parse(data);
            if (obj.status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }
            sweetalert(obj.msg, url);
        }
    });
}


function calculateShiftDuration() {
    const start = document.getElementById('start_time').value;
    const end = document.getElementById('end_time').value;
    if (start && end) {
        const [sH, sM] = start.split(':').map(Number);
        const [eH, eM] = end.split(':').map(Number);
        let startMins = sH * 60 + sM;
        let endMins = eH * 60 + eM;

        if (endMins < startMins) endMins += 24 * 60; // handle overnight shifts

        const diff = endMins - startMins;
        const hours = Math.floor(diff / 60);
        const mins = diff % 60;
        document.getElementById('shift_duration').value =
            `${hours.toString().padStart(2, '0')}:${mins.toString().padStart(2, '0')}`;
    }
}

document.getElementById('start_time').addEventListener('change', calculateShiftDuration);
document.getElementById('end_time').addEventListener('change', calculateShiftDuration);