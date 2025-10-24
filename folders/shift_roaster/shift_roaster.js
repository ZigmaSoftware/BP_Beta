// ==========================================================
// ✅ Basic Config
// ==========================================================
var company_name    = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_address");
var company_phone   = sessionStorage.getItem("company_phone");
var company_email   = sessionStorage.getItem("company_email");
var company_logo    = sessionStorage.getItem("company_logo");

var form_name   = 'shift roster';
var table_id    = 'shift_roster_datatable';
var action      = 'datatable';



$(document).ready(function () {
    init_datatable(table_id, form_name, action);

    // Load Roster Table dynamically when selecting Project + Month
    $('#project_id, #month_year').on('change', function () {
        const project_id = $('#project_id').val();
        const month_year = $('#month_year').val();

        if (project_id && month_year) {
            const ajax_url = sessionStorage.getItem("folder_crud_link");

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: { action: "get_roster_table", project_id, month_year },
                beforeSend: function () {
                    $('#roster_table_container').html("<p class='text-center text-muted'>Loading roster...</p>");
                },
                success: function (response) {
                    $('#roster_table_container').html(response);
                },
                error: function (xhr) {
                    console.log(xhr.responseText);
                    $('#roster_table_container').html("<div class='alert alert-danger'>Error loading roster table.</div>");
                }
            });
        }
    });
});


$(document).on('click', '.add_row_btn', function() {
    const row = $(this).closest('tr');
    const emp_id = row.find('.shift_input').first().data('emp');
    const project_id = $('#project_id').val();
    const month_year = $('#month_year').val();

    let shifts = {};
    row.find('.shift_input').each(function() {
        const date = $(this).data('date');
        const shift_name = $(this).val();
        const is_weekoff = $(this).closest('td').find('.weekoff_check').is(':checked') ? 1 : 0;
        if (shift_name) {
            shifts[date] = { shift_name, is_weekoff };
        }
    });

    $.ajax({
        type: 'POST',
        url: sessionStorage.getItem('folder_crud_link'),
        data: {
            action: 'add_shift_details',
            project_id,
            month_year,
            employee_id: emp_id,
            shifts
        },
        success: function(res) {
            try {
                const result = typeof res === 'string' ? JSON.parse(res) : res;
                if (result.status == 1) {
                    Swal.fire('Success', result.msg, 'success');
                    // Change button to "Update"
                    row.find('.add_row_btn')
                        .text('Update')
                        .removeClass('btn-success')
                        .addClass('btn-warning');
                } else {
                    Swal.fire('Error', result.msg || 'Unable to save data', 'error');
                }
            } catch (err) {
                console.error(err, res);
            }
        }
    });
});


// ==========================================================
// ✅ Auto-load roster when editing (prefilled Project + Month)
// ==========================================================
$(document).ready(function () {
    const project_id = $('#project_id').val();
    const month_year = $('#month_year').val();
    const ajax_url = sessionStorage.getItem("folder_crud_link");

    // Only load if both fields have value (i.e., in edit mode)
    if (project_id && month_year) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: { action: "get_roster_table", project_id, month_year },
            beforeSend: function () {
                $('#roster_table_container').html("<p class='text-center text-muted'>Loading roster...</p>");
            },
            success: function (response) {
                $('#roster_table_container').html(response);
            },
            error: function (xhr) {
                console.log(xhr.responseText);
                $('#roster_table_container').html("<div class='alert alert-danger'>Error loading roster table.</div>");
            }
        });
    }
});

// ==========================================================
// ✅ Create / Update Shift Roster
// ==========================================================
function shift_roaster_cu(unique_id = "") {

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
                $(".createupdate_btn").attr("disabled", "disabled").text("Loading...");
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

// ==========================================================
// ✅ DataTable Initialization for Shift Roster List
// ==========================================================
function init_datatable(table_id = '', form_name = '', action = '') {
    var table = $("#" + table_id);
    var data = { "action": action };
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
            { title: "Project Name" },
            { title: "Month" },
            { title: "Actions" }
        ]
    });
}

// ==========================================================
// ✅ Toggle Active / Inactive Roster
// ==========================================================
function shift_roaster_toggle(unique_id = "", new_status = 0) {
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

// ==========================================================
// ✅ Custom Autocomplete + Shift Propagation (no external lib)
// ==========================================================
$(document).ready(function () {
    let shiftList = [];

    // 1️⃣ Load available shifts from backend (shift_creation)
    $.ajax({
        type: "POST",
        url: sessionStorage.getItem("folder_crud_link"),
        data: { action: "get_shift_list" },
        success: function (response) {
            try {
                const obj = JSON.parse(response);
                if (obj.status && Array.isArray(obj.data)) {
                    shiftList = obj.data.map(s => s.shift_name);
                }
            } catch (e) {
                console.log("Error parsing shift list:", e);
            }
        }
    });

    // 2️⃣ Build a reusable suggestion dropdown
    const suggestionBox = $('<div id="shift-suggestions"></div>').css({
        position: 'absolute',
        background: '#fff',
        border: '1px solid #ccc',
        'border-radius': '6px',
        'box-shadow': '0 2px 6px rgba(0,0,0,0.15)',
        'z-index': 10000,
        'max-height': '180px',
        overflow: 'auto',
        display: 'none'
    });
    $('body').append(suggestionBox);

    // 3️⃣ Show suggestions dynamically
    $(document).on('input focus', '.shift_input', function (e) {
        const input = $(this);
        const val = input.val().toLowerCase().trim();

        // Filter results
        const results = shiftList.filter(s => s.toLowerCase().includes(val));
        if (results.length === 0) {
            suggestionBox.hide();
            return;
        }

        // Position suggestion box near the input
        const offset = input.offset();
        suggestionBox.empty().css({
            top: offset.top + input.outerHeight(),
            left: offset.left,
            width: input.outerWidth()
        });

        // Build suggestion items
        results.forEach(r => {
            const item = $('<div></div>').text(r).css({
                padding: '6px 10px',
                cursor: 'pointer',
                'font-size': '14px'
            });
            item.hover(
                function () { $(this).css('background', '#f2f2f2'); },
                function () { $(this).css('background', '#fff'); }
            );
            item.on('mousedown', function () {
                input.val(r);
                suggestionBox.hide();
            });
            suggestionBox.append(item);
        });

        suggestionBox.show();
    });

    // Hide suggestions on click outside
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#shift-suggestions, .shift_input').length) {
            suggestionBox.hide();
        }
    });

    // 4️⃣ Checkbox behavior – apply shift across or ahead
    $(document).on("change", ".weekoff_check", function () {
        const checked = $(this).is(":checked");
        const empId = $(this).data("emp");
        const date = $(this).data("date");
        const row = $(this).closest("tr");
        const currentInput = $(this).closest("td").find(".shift_input");
        const currentShift = currentInput.val().trim();
    
        const allInputs = row.find(".shift_input");
        let applyFlag = false;
    
        if (checked) {
            // ✅ Apply shift from this day onward
            if (!currentShift) {
                alert("Please type a shift before applying.");
                $(this).prop("checked", false);
                return;
            }
    
            allInputs.each(function () {
                const cellDate = $(this).data("date");
                if (cellDate === date) applyFlag = true;
                if (applyFlag) $(this).val(currentShift);
            });
        } else {
            // 🚫 Unchecked → remove shifts from this day onward
            allInputs.each(function () {
                const cellDate = $(this).data("date");
                if (cellDate === date) applyFlag = true;
                if (applyFlag) $(this).val('');
            });
        }
    });
});

