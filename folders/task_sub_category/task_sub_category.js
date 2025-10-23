// ==========================
// 🧠 INITIALIZATION
// ==========================
$(document).ready(function () {
    init_datatable(table_id, form_name, action);
});

let ajax_url = sessionStorage.getItem("folder_crud_link");
let list_url = sessionStorage.getItem("list_link");

var form_name = "task_sub_category";
var table_id  = "sub_category_datatable";
var action    = "datatable";

// ==========================
// 💾 CREATE / UPDATE
// ==========================
function task_sub_category_cu() {
    let formdata = new FormData($("#task_sub_category_form")[0]);
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
                    text: res.error || "Task sub-category saved successfully!",
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
                    text: res.error || "Unable to save sub-category.",
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

    let data = Object.assign({ action: action }, filter_data);

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
            { data: "department_name", title: "Department" },
            { data: "task_category_name", title: "Category" },
            { data: "task_sub_category_name", title: "Sub-Category" },
            { data: "description", title: "Description" },
            { data: "action", title: "Action", orderable: false, searchable: false, className: "text-center" }
        ],
        order: [[0, "asc"]],
        responsive: true,
        language: {
            emptyTable: "No sub-categories found"
        }
    });
}

// ==========================
// 🔍 FILTER FUNCTION
// ==========================
function task_sub_category_filter() {
    let dept_id  = $("#filter_department").val();
    let cat_id   = $("#filter_category").val();

    let filter_data = {};
    if (dept_id) filter_data.department = dept_id;
    if (cat_id)  filter_data.category = cat_id;

    init_datatable(table_id, form_name, action, filter_data);
}

// ==========================
// 🗑️ TOGGLE ACTIVE/INACTIVE
// ==========================
function task_sub_category_toggle(unique_id, current_status) {
    if (!unique_id) {
        Swal.fire({
            title: "Invalid Action",
            text: "Unique ID missing — cannot change status.",
            icon: "warning",
            confirmButtonText: "OK"
        });
        return;
    }

    let isDeactivate = (current_status == 0); // 1 = active, toggle → deactivate
    let actionText   = isDeactivate ? "Deactivate" : "Activate";
    let confirmIcon  = isDeactivate ? "⚠️" : "✅";

    Swal.fire({
        title: `${actionText} Sub-Category?`,
        text: `Are you sure you want to ${actionText.toLowerCase()} this sub-category?`,
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
                            text: res.error || `Sub-category ${actionText.toLowerCase()}d successfully.`,
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
                            text: res.error || "Unable to update sub-category status.",
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

function task_list(department){
    let date = "";
    if(department){
        data = {
            "department": department,
            "action": "task_list"
        }
    }
    
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        success: function(res){
            $("#category").html(res);
        },
        error: function (xhr, status, error) {
            console.log("error: "+error);
        }
    })
}