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

// Form/Table variables
var form_name       = 'Daily Logsheet Master';
var form_header     = '';
var form_footer     = '';
var table_name      = '';
var table_id        = 'dailylogsheet_master_datatable';
var action          = "datatable";

// ========================= CREATE / UPDATE =========================
function dailylogsheet_master_cu(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
        var data = $(".was-validated").serialize();
        data    += "&unique_id=" + unique_id + "&action=createupdate";

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

                if (!status) {
                    url = '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg == "already") {
                        url = '';
                        $(".createupdate_btn").removeAttr("disabled", "disabled");
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

// ========================= DATATABLE =========================
function init_datatable(table_id = '', form_name = '', action = '') {
    var table = $("#" + table_id);
    var data  = { "action": action };
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var datatable = table.DataTable({
        ordering    : true,
        searching   : true,
        "ajax"      : {
            url     : ajax_url,
            type    : "POST",
            data    : data
        }
    });
}

// ========================= TOGGLE ACTIVE STATUS =========================
function dailylogsheet_master_toggle(unique_id = "", new_status = 0) {
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

// ========================= COMPANY CODE (Optional) =========================
function get_company_code(company_id = "") {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (company_id) {
        var data = {
            "company_id": company_id,
            "action": "get_company_code"
        };

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            success : function (data) {
                if (data) { 
                    $("#company_code").val(data);
                }
            }
        });
    }
}




// Project Name Load
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
        // If no company is selected, reset project dropdown
        $("#project_name").html('<option value="">Select the Project</option>');
    }
}



