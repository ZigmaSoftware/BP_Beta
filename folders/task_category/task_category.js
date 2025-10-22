$(document).ready(function () {
    init_datatable(table_id, form_name, action);
});

let ajax_url = sessionStorage.getItem("folder_crud_link");
let list_url = sessionStorage.getItem("list_link");

var form_name   = "task_category";
var table_id    = "category_datatable";
var action      = "datatable";

function task_category_cu() {
    let formdata = new FormData($("#task_category_form")[0]);
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
                    text: res.error,
                    icon: "success",
                    confirmButtonText: "OK",
                    timer: 1800,
                    timerProgressBar: true
                }).then(() => {
                    window.location.href = list_url;
                });
            } else {
                Swal.fire({
                    title: "Failed to Add",
                    text: "Error: " + res.error,
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

    // Destroy previous instance to avoid duplication
    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }

    // Base data for request
    let data = { action: action, ...filter_data };

    // Initialize DataTable
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
            { data: "s_no", title: "S.No" },
            { data: "department_unique_id", title: "Department" },
            { data: "task_category_name", title: "Category Name" },
            { data: "description", title: "Description" },
            // { data: "created", title: "Created On" },
            // { data: "updated", title: "Updated On" },
            { data: "action", title: "Action" }
        ],
        order: [[0, "asc"]],
        responsive: true,
        language: {
            emptyTable: "No categories found"
        }
    });
}

// ==========================
// 🔍 FILTER FUNCTION
// ==========================
function task_category_filter() {
    let dept_id = $("#filter_department").val();

    // Build FormData-like object
    let filter_data = {
        department: dept_id
    };

    // Re-initialize DataTable with filter
    init_datatable(table_id, form_name, action, filter_data);
}