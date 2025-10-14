


// ---------- INIT ----------
$(document).ready(function () {
    let main_id = $("#unique_id").val();
    if (main_id) {
        expense_items_datatable("expense_items_datatable");
    }

    $('.select2').select2();

    $("#item_code").on("change", function () {
        const item_name = $(this).val();
        if (item_name) get_item_details(item_name);
    });

    $("#quantity, #rate, #discount, #tax, #discount_type").on("input change", function () {
        calculate_amount();
    });

    init_datatable(table_id, form_name, action);
});

var form_name = 'expense_entry';
var table_id = 'expense_entry_datatable';
var action = 'datatable';
var ajax_url = sessionStorage.getItem("folder_crud_link");
var url = sessionStorage.getItem("list_link");






$(document).ready(function () {
    $('.select2').select2();
    init_datatable();
});



function calculate_amount() {
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discountType = $("#discount_type").val() || "0";
    let taxPercent = parseFloat($("#tax").val()) || 0;

    // Step 1: Base
    let base = qty * rate;

    // Step 2: Discount
    let discountAmt = 0;
    if (discountType === "1") {         
        discountAmt = (base * discount) / 100;
    } else if (discountType === "2") {  
        discountAmt = discount;
    }

    // Step 3: After discount
    let afterDiscount = base - discountAmt;
    if (afterDiscount < 0) afterDiscount = 0;

    // Step 4: Tax
    let taxAmt = (afterDiscount * taxPercent) / 100;

    // Step 5: Total before round-off (per item)
    let totalAmt = afterDiscount + taxAmt;

    // Step 6: Display live per-item result
    $("#amount").val(totalAmt.toFixed(2));

    // Step 7: Update invoice totals
    recalc_invoice_totals();
}


// Save main invoice
function expense_entry_cu(unique_id = "") {
    let internet_status = is_online();
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    let is_form = form_validity_check("was-validated");
    if (!is_form) {
        sweetalert("form_alert");
        return false;
    }

    // âœ… CHECK: If sublist is empty, stop
    let rowCount = $("#expense_items_datatable tbody tr").length;
    if (rowCount === 0 || $("#expense_items_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }
    
    let remarks = $("#remarks_main").val().trim();

    let data = new FormData($("#expense_entry_form")[0]);
    data.append("remarks", remarks);
    data.append("action", "createupdate");
    data.append("unique_id", unique_id);

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        beforeSend: function () {
            $(".createupdate_btn").attr("disabled", "disabled").text("Processing...");
        },
        success: function (data) {
            let obj = JSON.parse(data);
            if (!obj.status) {
                $(".createupdate_btn").text("Error");
                console.log(obj.error);
            } else {
                sweetalert(obj.msg, url);
            }
        },
        error: function () {
            alert("Network Error");
        },
        complete: function () {
            $(".createupdate_btn").removeAttr("disabled").text("Save");
        }
    });
}





function expense_item_add_update() {
    let main_unique_id = $("#unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

    if (!main_unique_id) {
        Swal.fire("Please save the invoice header first.");
        return;
    }

    let item_name = $("#item_name").val();
    let unit = $("#unit").val();
    let quantity = $("#quantity").val();
    let rate = $("#rate").val();
    let discount_type = $("#discount_type").val();
    let discount = $("#discount").val();
    let tax = $("#tax").val();
    let amount = $("#amount").val();
    let remarks = $("#remarks").val();

    if (!item_name || quantity <= 0 || rate <= 0) {
        Swal.fire("Please fill all required sublist fields (Item, Qty, Rate).");
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "expense_item_add_update",
            main_unique_id,
            sublist_unique_id: sublist_id,
            item_name,
            unit,
            quantity,
            rate,
            discount_type,
            discount,
            tax,
            amount,
            remarks
        },
        success: function (res) {
            const obj = JSON.parse(res);
            if (obj.status) {
                Swal.fire({
                    icon: "success",
                    title: obj.msg === "update" ? "Item updated" : "Item added",
                    timer: 1200,
                    showConfirmButton: false
                });
                expense_items_datatable("expense_items_datatable");
                recalc_invoice_totals();   // ðŸ”¹ refresh totals
                reset_sublist_form();
            }
        }
    });
}


function recalc_invoice_totals() {
    let basic = 0;
    let total_gst = 0;

    $("#invoice_items_datatable tbody tr").each(function () {
        const qty  = parseFloat($(this).find("td:eq(3)").text()) || 0;
        const rate = parseFloat($(this).find("td:eq(4)").text()) || 0;

        const discTypeText = ($(this).find("td:eq(5)").text() || "").trim().toLowerCase();
        const discount = parseFloat($(this).find("td:eq(6)").text()) || 0;

        // FIX: sanitize GST column
        const gstText = ($(this).find("td:eq(7)").text() || "").replace(/[^\d.-]/g, "");
        const gstPercent = parseFloat(gstText) || 0;

        // Step 1: Base
        const base = qty * rate;

        // Step 2: Discount
        let discountAmt = 0;
        if (discTypeText === "1" || discTypeText === "%" || discTypeText.includes("percent")) {
            discountAmt = (base * discount) / 100;
        } else if (discTypeText === "2" || discTypeText === "₹" || discTypeText.includes("amount")) {
            discountAmt = discount;
        }

        // Step 3: After discount
        const afterDiscount = Math.max(0, base - discountAmt);

        // Step 4: Tax
        const gstAmt = (afterDiscount * gstPercent) / 100;

        // Debugging
        console.log({
            qty, rate, discount, discTypeText, gstPercent,
            base, afterDiscount, gstAmt
        });

        // Step 5: Accumulate
        basic += afterDiscount;
        total_gst += gstAmt;
    });

    const roundoff = parseFloat($("#roundoff").val()) || 0;
    const tot_amount = basic + total_gst + roundoff;

    $("#basic").val(basic.toFixed(2));
    $("#total_gst").val(total_gst.toFixed(2));
    $("#tot_amount").val(tot_amount.toFixed(2));
}

// Update totals when roundoff changes
$("#roundoff").on("input", function () {
    // const val = parseFloat($(this).val()) || 0;
    // $(this).val(val.toFixed(2));
    recalc_invoice_totals();
});






// Reset sublist fields
function reset_sublist_form() {
    $("#sublist_unique_id").val("");
    $("#item_name, #unit, #quantity, #rate, #discount, #tax, #amount, #remarks").val("").trigger("change");
    $("#discount_type").val("0").trigger("change");
    $("#sublist_btn_text").text("Add");
    $(".invoice_sublist_add_btn")
        .removeClass("btn-primary")
        .addClass("btn-success");
}



function expense_items_datatable(table_id = "expense_items_datatable") {
    let main_unique_id = $("#unique_id").val();

    $("#" + table_id).DataTable({
        destroy: true,
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        ajax: {
            type: "POST",
            url: ajax_url,
            data: {
                action: "expense_items_datatable",
                main_unique_id: main_unique_id
            }
        },
        columns: [
            { data: "s_no" },
            { data: "item" },
            { data: "unit" },
            { data: "qty" },
            { data: "rate" },
            { data: "discount_type" },
            { data: "discount" },
            { data: "tax" },
            { data: "amount" },
            { data: "remarks" },
            { data: "actions" }
        ],
        drawCallback: function () {
            recalc_invoice_totals(); // ðŸ”¹ auto update totals when data changes
        }
    });
}



// Edit sublist item
function exp_item_edit(unique_id) {
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "exp_item_edit", unique_id },
        success: function (res) {
    let d = JSON.parse(res).data;
    $("#sublist_unique_id").val(d.unique_id);
    $("#item_name").val(d.item_name).trigger("change");
    $("#unit").val(d.unit).trigger("change");
    $("#quantity").val(d.quantity);
    $("#rate").val(d.rate);
    $("#discount_type").val(d.discount_type).trigger("change");
    $("#discount").val(d.discount);
    $("#tax").val(d.tax).trigger("change");
    $("#amount").val(d.amount);
    $("#remarks").val(d.remarks);

    $("#sublist_btn_text").text("Update");
    $(".invoice_sublist_add_btn").removeClass("btn-success").addClass("btn-primary");
}

    });
}

// Delete sublist item
function inv_item_delete(unique_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This item will be deleted",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "inv_item_delete",
                    unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    Swal.fire(obj.msg);
                    expense_items_datatable("expense_items_datatable");
                }
            });
        }
    });
}

// Get item details by name/id
function get_item_details(item_name = "") {
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_item_details",
            item_name: item_name
        },
        success: function (res) {
            const obj = JSON.parse(res);
            if (obj.status) {
                const data = obj.data;
                $("#unit").val(data.unit || "");
                $("#rate").val(data.rate || 0);
                $("#discount").val(0);
                $("#tax").val(0);
                calculate_amount();
            } else {
                Swal.fire("Item details not found.");
            }
        },
        error: function () {
            Swal.fire("Failed to fetch item details.");
        }
    });
}


function init_datatable(table_id = '', form_name = '', action = '') {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    // Destroy old instance if exists
    if ($.fn.DataTable.isDataTable("#" + table_id)) {
        $("#" + table_id).DataTable().destroy();
    }

    var datatable = $("#" + table_id).DataTable({
        responsive: true,
        ordering: true,
        searching: true,
        processing: true,
        serverSide: true,
        destroy: true,
        dom: 'Bfrtip', // enable export buttons
        buttons: [
            {
                extend: 'csvHtml5',
                text: 'Export CSV',
                title: 'Sales Invoice Report'
            },
            {
                extend: 'excelHtml5',
                text: 'Export Excel',
                title: 'Invoice Report'
            }
        ],
        ajax: {
            url: ajax_url,
            type: "POST",
            data: function (d) {
  d.action         = action;
  d.from_date      = $("#from_date").val();
  d.to_date        = $("#to_date").val();
  d.category_name  = $("#category_name").val();
  d.payment_type   = $("#payment_type").val();
  d.customer_name  = $("#customer_name").val();
}

        }
    });
}




// filter button
function expense_entry_filter() {
    $("#" + table_id).DataTable().ajax.reload();
}




// Load projects when company changes
function get_project_name(company_id = "") {
    if (company_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: { company_id: company_id, action: "project_name" },
            success: function (data) {
                if (data) {
                    $("#project_id").html(data).trigger("change");
                }
            }
        });
    }
}

// Optional: If you want to use project_id change for further linking later
function get_linked_so(project_id = "") {
    if (project_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: { project_id: project_id, action: "linked_so" },
            success: function (data) {
                if (data) {
                    $("#sales_order_id").html(data);
                }
            }
        });
    }
}



// Delete main invoice
function expense_entry_delete(unique_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This invoice will be marked as deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "expense_entry_delete",   // match crud.php
                    unique_id: unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    if (obj.status) {
                        Swal.fire("Deleted!", "Invoice deleted successfully.", "success");
                        $("#" + table_id).DataTable().ajax.reload();
                    } else {
                        Swal.fire("Error", obj.error || "Delete failed.", "error");
                    }
                },
                error: function () {
                    Swal.fire("Error", "Network error occurred.", "error");
                }
            });
        }
    });
}



// Initialize Dropify
$('.dropify').dropify();

// Add or Update Document
function documents_add_update() {
    let form = document.getElementById("documents_form");
    let formData = new FormData(form);
    formData.append("action", "documents_add_update");

    $.ajax({
        url: sessionStorage.getItem("folder_crud_link"), // Adjust if needed
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function () {
            Swal.fire({
                title: "Uploading...",
                text: "Please wait while your files are uploaded.",
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        },
        success: function (res) {
            try {
                let obj = JSON.parse(res);
                if (obj.status) {
                    Swal.fire("Success", "Files uploaded successfully!", "success");
                    $("#documents_datatable").DataTable().ajax.reload();
                    $("#test_file").val("");
                    $('.dropify-clear').click(); // Reset dropify
                } else {
                    Swal.fire("Error", obj.error || "Upload failed", "error");
                }
            } catch (e) {
                console.error(res);
                Swal.fire("Error", "Unexpected server response", "error");
            }
        },
        error: function (xhr, status, error) {
            Swal.fire("Error", "Network error occurred", "error");
            console.error(error);
        }
    });
}

// Load uploaded documents into DataTable
function documents_datatable(upload_unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    if ($.fn.DataTable.isDataTable("#documents_datatable")) {
        $("#documents_datatable").DataTable().destroy();
    }

    $("#documents_datatable").DataTable({
        responsive: true,
        ordering: false,
        searching: false,
        paging: false,
        ajax: {
            url: ajax_url,
            type: "POST",
            data: {
                action: "documents_datatable",
                upload_unique_id: upload_unique_id
            }
        }
    });
}

// Delete Document
function documents_delete(unique_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This file will be permanently deleted.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: sessionStorage.getItem("folder_crud_link"),
                data: {
                    action: "documents_delete",
                    unique_id: unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    if (obj.status) {
                        Swal.fire("Deleted!", "File deleted successfully.", "success");
                        $("#documents_datatable").DataTable().ajax.reload();
                    } else {
                        Swal.fire("Error", obj.error || "Delete failed.", "error");
                    }
                }
            });
        }
    });
}



function expense_entry_upload(upload_unique_id) {
    $("#upload_unique_id").val(upload_unique_id);
    documents_datatable(upload_unique_id);
    $("#exUploadModal").modal("show");
}

function new_external_window_image(image_url) {
    if (!image_url) {
        alert("Image URL not provided.");
        return;
    }

    const windowFeatures = [
        "height=550",
        "width=950",
        "resizable=no",
        "left=200",
        "top=150",
        "toolbar=no",
        "location=no",
        "directories=no",
        "status=no",
        "menubar=no",
        "scrollbars=no"
    ].join(",");

    window.open(image_url, '_blank', windowFeatures);
}


function fetch_item_details(val) {
    if (!val) return;

    const ajax_url = sessionStorage.getItem("folder_crud_link") || "function.php";
    const item_text = $("#item_name option:selected").text();

    $.ajax({
        url: ajax_url,
        type: "POST",
        dataType: "json",
        data: {
            action: "get_item_details",
            // send several identifiers so backend can resolve robustly
            item_code: val,
            item_id: val,
            item_name_text: item_text
        },
        success: function (res) {
            // For debugging:
            // console.log("get_item_details =>", res);

            if (res && res.status && res.data) {
                if (res.data.uom_id) {
                    $("#unit").val(res.data.uom_id).trigger("change");
                }
                if (res.data.unit_price !== undefined) {
                    $("#rate").val(res.data.unit_price);
                }
                if (res.data.gst !== undefined) {
                    $("#tax").val(res.data.gst).trigger("change");
                }
                if (res.data.description) {
                    // Optional: show in remarks field for user context
                    $("#remarks").val(res.data.description);
                }
                calculate_amount();
            } else {
                // Clear on miss to avoid stale values
                $("#rate").val("");
                $("#tax").val("0").trigger("change");
            }
        },
        error: function (xhr) {
            console.error("get_item_details error:", xhr.responseText || xhr.statusText);
        }
    });
}


