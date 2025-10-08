// Form/Table variables
var form_name       = 'Integrated Daily Logsheet';
var table_id        = 'Integrated_daily_log_master_datatable';
var action          = "datatable";

$(document).ready(function () {
    init_datatable(table_id, form_name, action);
    let company_name = $("#company_name").val();
    get_project_name(company_name);
});


// Session variables (if used for headers/logos)
var company_name    = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_address");
var company_phone   = sessionStorage.getItem("company_phone");
var company_email   = sessionStorage.getItem("company_email");
var company_logo    = sessionStorage.getItem("company_logo");

// ========================= CREATE / UPDATE =========================
function integrated_daily_log_cu(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
        var data = $(".was-validated").serialize();

        // Collect checkbox fields
        $("input[name='fields[]']").each(function() {
            if(!$(this).is(':checked')) {
                data += "&fields[" + $(this).attr('name') + "]=0";
            }
        });

        data += "&unique_id=" + unique_id + "&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend: function () {
                $(".createupdate_btn").attr("disabled", "disabled");
                $(".createupdate_btn").text("Loading...");
            },
            success: function (data) {
                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                $(".createupdate_btn").removeAttr("disabled");
                $(".createupdate_btn").text(unique_id ? "Update" : "Save");

                // 🚫 Duplicate record check
                if (msg == "duplicate") {
                    Swal.fire({
                        icon: "warning",
                        title: "Duplicate Entry",
                        text: "This combination of Company Name, Project Name, and Application Type already exists!",
                        confirmButtonColor: "#3085d6",
                    });
                    return;
                }

                // ❌ General error check
                if (!status) {
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                    return;
                }

                // ✅ Success alert
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


function init_datatable(table_id = '', form_name = '', action = '') {
    var table = $("#" + table_id);
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var datatable = table.DataTable({
        processing  : true,
        serverSide  : true,
        ordering    : true,
        searching   : false, // disable default search
        ajax: {
            url : ajax_url,
            type: "POST",
            data: function(d) {
                d.action        = action;
                d.from_date     = $("#from_date").val();
                d.to_date       = $("#to_date").val();
                d.company_id    = $("#filter_company").val();
                d.project_id    = $("#filter_project").val();
                d.application_type = $("#filter_application_type").val();
            }
        }
    });

    // Reload table when Go button clicked
    $("#btn_filter").on("click", function() {
        datatable.ajax.reload();
    });

    return datatable;
}


// ========================= TOGGLE ACTIVE STATUS =========================
function integrated_daily_log_toggle(unique_id = "", new_status = 0) {
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

// ========================= PROJECT NAME =========================
function get_project_name(company_id = "") {
    let project = $("#project").val();
    if (company_id) {
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var data = {
            "company_id": company_id,
            "project": project,
            "action": "project_name"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#project_name").html(data);
                }
            }
        });
    } else {
        $("#project_name").html('<option value="">Select the Project</option>');
    }
}


document.getElementById('select_all').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('.field-checkbox');
    checkboxes.forEach(chk => chk.checked = this.checked);
});

// Auto-check Select All if all individual boxes are checked manually
var allCheckboxes = document.querySelectorAll('.field-checkbox');
allCheckboxes.forEach(function(cb){
    cb.addEventListener('change', function(){
        document.getElementById('select_all').checked = Array.from(allCheckboxes).every(chk => chk.checked);
    });
});

