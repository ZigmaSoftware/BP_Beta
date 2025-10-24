// ==========================================================
// 🌐 GLOBAL VARIABLES
// ==========================================================
var company_name    = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_address");
var company_phone   = sessionStorage.getItem("company_phone");
var company_email   = sessionStorage.getItem("company_email");
var company_logo    = sessionStorage.getItem("company_logo");

var form_name   = "periodic_creation";
var table_id    = "periodic_creation_datatable";
var action      = "datatable";

// ==========================================================
// 🚀 DOCUMENT READY
// ==========================================================
$(document).ready(function () {
    init_main_datatable();
    init_sublist_datatable("periodic_sub_datatable");
    get_staff_info();

    // Refresh sublist whenever staff changes
    $("#user_id").on("change", function () {
        fetch_staff_info();
        init_sublist_datatable("periodic_sub_datatable");
    });
});

// ==========================================================
// 📊 MAIN DATATABLE INITIALIZATION
// ==========================================================
function init_main_datatable(filter_data = {}) {
    let table = $("#" + table_id);
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    if ($.fn.DataTable.isDataTable(table)) {
        table.DataTable().destroy();
    }

    table.DataTable({
        ordering: true,
        searching: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: ajax_url,
            type: "POST",
            data: Object.assign({ action: action }, filter_data)
        },
        columns: [
            { data: "s_no", title: "S.No" },
            { data: "user_id", title: "User Name" },
            { data: "user_type", title: "User Type" },
            { data: "mobile_number", title: "Mobile Number" },
            { data: "unique_id", title: "Action", className: "text-center", orderable: false }
        ],
        order: [[0, "asc"]],
        language: { emptyTable: "No records found" },
        responsive: true
    });
}

// ==========================================================
// 🧾 SUBLIST DATATABLE (Dynamic)
// ==========================================================
function init_sublist_datatable(table_id = "periodic_sub_datatable") {
    let ajax_url = sessionStorage.getItem("folder_crud_link");
    let user_id = $("#user_id").val(); // 🟢 use user_id now

    if (!user_id) {
        // clear existing sublist
        $("#" + table_id + " tbody").html("");
        return;
    }

    if ($.fn.DataTable.isDataTable("#" + table_id)) {
        $("#" + table_id).DataTable().destroy();
    }

    $("#" + table_id).DataTable({
        ordering: true,
        searching: false,
        paging: false,
        info: false,
        ajax: {
            url: ajax_url,
            type: "POST",
            data: { action: table_id, user_id: user_id } // 🟢 pass user_id
        },
        columns: [
            { data: "s_no", title: "#" },
            { data: "department", title: "Department" },
            { data: "category", title: "Category" },
            { data: "project_id", title: "Project" },
            { data: "level", title: "Level" },
            { data: "starting_days", title: "Start Days" },
            { data: "ending_days", title: "End Days" },
            { data: "action", title: "Action", className: "text-center" }
        ],
        language: { emptyTable: "No sublist entries found" }
    });
}

// ==========================================================
// 💾 CREATE / UPDATE MAIN FORM
// ==========================================================
function periodic_creation_cu(unique_id = "") {
    if (!is_online()) return sweetalert("no_internet");

    let is_form_valid = form_validity_check("was-validated", "periodic_creation_form_main");
    if (!is_form_valid) return sweetalert("form_alert");

    let formData = $("#periodic_creation_form_main").serialize() +
        "&unique_id=" + unique_id +
        "&action=createupdate";

    let ajax_url = sessionStorage.getItem("folder_crud_link");
    let redirect_url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: formData,
        beforeSend: function () {
            $(".createupdate_btn").attr("disabled", true).text("Saving...");
        },
        success: function (res) {
            let data = JSON.parse(res);
            let msg = data.msg, status = data.status;

            $(".createupdate_btn").removeAttr("disabled").text(unique_id ? "Update" : "Save");
            if (status) sweetalert(msg, redirect_url);
            else sweetalert("error", "", "", "Failed to save record.");
        },
        error: function () {
            sweetalert("custom", "", "", "Network Error");
        }
    });
}

// ==========================================================
// ➕ ADD / UPDATE SUBLIST ROW
// ==========================================================
function periodic_add_update(unique_id = "") {
    if (!is_online()) return sweetalert("no_internet");

    let department = $("#department").val();
    let category = $("#category").val();
    let project_id = $("#project_id").val();
    let level = $("#level").val();
    let starting_days = $("#starting_days").val();
    let ending_days = $("#ending_days").val();
    let user_id = $("#user_id").val(); // 🟢 linkage key

    if (!user_id) return sweetalert("custom", "", "", "Please select a staff name first.");
    if (!department || !category || !project_id || !level || !starting_days || !ending_days)
        return sweetalert("custom", "", "", "Please fill all sublist fields.");

    if (parseInt(starting_days) >= parseInt(ending_days))
        return sweetalert("custom", "", "", "Ending days must be greater than starting days.");

    let data = new FormData();
    data.append("action", "periodic_add_update");
    data.append("unique_id", unique_id);
    data.append("department", department);
    data.append("category", category);
    data.append("project_id", project_id);
    data.append("level", level);
    data.append("starting_days", starting_days);
    data.append("ending_days", ending_days);
    data.append("user_id", user_id); // 🟢 used instead of screen_unique_id

    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $(".periodic_sub_add_update_btn").attr("disabled", true).text("Adding...");
        },
        success: function (res) {
            console.info(res);
            // let obj = JSON.parse(res);
            if (res.msg == "add") {
                init_sublist_datatable();
                form_reset("periodic_creation_form_sub");
            }
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: res.msg || 'Operation completed successfully!',
              showConfirmButton: true,
              confirmButtonColor: '#3085d6'
            });
            $(".periodic_sub_add_update_btn").removeAttr("disabled").text("Add");
            
        },
        error: function (xhr, status, error) {
            console.log("Raw Response:", xhr.responseText);
            console.log("Status:", status, "Error:", error);
        }

    });
}

// ==========================================================
// ❌ DELETE SUBLIST ROW
// ==========================================================
function periodic_sub_delete(unique_id = "") {
    if (!unique_id) return;

    let ajax_url = sessionStorage.getItem("folder_crud_link");
    confirm_delete('delete').then((result) => {
        if (result.isConfirmed) {
            $.post(ajax_url, { action: "periodic_sub_delete", unique_id: unique_id }, function (res) {
                let obj = JSON.parse(res);
                if (obj.status) init_sublist_datatable();
                sweetalert(obj.msg, "");
            });
        }
    });
}

// ==========================================================
// 🗑 DELETE MAIN RECORD
// ==========================================================
function periodic_creation_delete(unique_id = "") {
    if (!unique_id) return;

    let ajax_url = sessionStorage.getItem("folder_crud_link");
    let redirect_url = sessionStorage.getItem("list_link");

    confirm_delete('delete').then((result) => {
        if (result.isConfirmed) {
            $.post(ajax_url, { action: "delete", unique_id: unique_id }, function (res) {
                let obj = JSON.parse(res);
                if (obj.status) init_main_datatable();
                sweetalert(obj.msg, redirect_url);
            });
        }
    });
}

// ==========================================================
// 🧠 FETCH STAFF INFO (on select)
// ==========================================================
function fetch_staff_info() {
    let user_id = $("#user_id").val();
    if (!user_id) return;

    let ajax_url = sessionStorage.getItem("folder_crud_link");
    $.post(ajax_url, { action: "get_usertype", user_name: user_id }, function (res) {
        let obj = JSON.parse(res);
        $("#user_type").val(obj.user_type);
        $("#mobile_number").val(obj.mobile_no);
        $("#designation").val(obj.designation);
    });
}

// ==========================================================
// 🔁 REFRESH SITE / DEPT DROPDOWNS BASED ON USER
// ==========================================================
function get_dept_category(user_id = "") {
    if (!user_id) return;

    let ajax_url = sessionStorage.getItem("folder_crud_link");
    $.post(ajax_url, { action: "get_dept_category", user_name: user_id }, function (res) {
        let obj = JSON.parse(res);
        $("#department").val(obj.department_name);
        $("#category").html(obj.category_options);
    });
}
