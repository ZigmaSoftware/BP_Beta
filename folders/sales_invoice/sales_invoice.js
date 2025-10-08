$(document).ready(function () {
    let main_id = $("#unique_id").val();
    if (main_id) {
        invoice_items_datatable("invoice_items_datatable");
    }

    $('.select2').select2();

    // When item is chosen → load details (unit etc.)
    $("#item_code").on("change", function () {
        const item_name = $(this).val();
        if (item_name) {
            get_item_details(item_name);
        } else {
            $("#unit, #quantity, #rate, #discount, #tax, #amount, #remarks").val("");
        }
    });

    // Auto calculate amount when qty, rate, discount, tax changes
    $("#quantity, #rate, #discount, #tax").on("input", function () {
        calculate_amount();
    });

    init_datatable(table_id, form_name, action);
});

var form_name   = 'sales_invoice';
var table_id    = 'sales_invoice_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");


$(document).ready(function () {
    $('.select2').select2();
    init_datatable();
});


function calculate_amount() {
    // alert("test");
    let qty      = parseFloat($("#quantity").val()) || 0;
    let rate     = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discountType = $("#discount_type").val() || 0;  
    let tax      = parseFloat($("#tax").val()) || 0;

    let subtotal = qty * rate;
    let after_discount = subtotal;

    // Apply discount based on type
    if (discountType === "1") {        // Percentage
        after_discount -= (discount / 100) * subtotal;
    } else if (discountType === "2") { // Amount (₹)
        after_discount -= discount;
    }

    // Apply tax
    let after_tax = after_discount + (after_discount * (tax / 100));

    $("#amount").val(after_tax.toFixed(2));
}




// Save main invoice
function sales_invoice_cu(unique_id = "") {
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

    // ✅ CHECK: If sublist is empty, stop
    let rowCount = $("#invoice_items_datatable tbody tr").length;
    if (rowCount === 0 || $("#invoice_items_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }
    
    let remarks = $("#remarks_main").val().trim();

    let data = new FormData($("#sales_invoice_form")[0]);
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




function invoice_item_add_update() {
    let main_unique_id = $("#unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

    if (!main_unique_id) {
        Swal.fire("Please save the invoice header before adding items.");
        return;
    }

    let item_name     = $("#item_name").val();   
    let unit          = $("#unit").val();       
    let quantity      = $("#quantity").val();
    let rate          = $("#rate").val();
    let discount_type = $("#discount_type").val();
    let discount      = $("#discount").val();
    let tax           = $("#tax").val();
    let amount        = $("#amount").val();
    let remarks       = $("#remarks").val();

    // validation check
    if (!item_name || quantity <= 0 || rate <= 0) {
        Swal.fire("Please fill all required sublist fields (Item, Qty, Rate).");
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "invoice_item_add_update",
            main_unique_id: main_unique_id,
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
            let obj = JSON.parse(res);

            if (obj.status) {
                Swal.fire({
                    icon: "success",
                    title: (obj.msg === "update" ? "Item updated" : "Item added"),
                    timer: 1500,
                    showConfirmButton: false
                });

                invoice_items_datatable("invoice_items_datatable");
                reset_sublist_form();
            } else {
                Swal.fire({
                    icon: "error",
                    title: "Error",
                    text: (obj.error || "Operation failed")
                });
            }
        },
        error: function () {
            Swal.fire("Network error");
        }
    });
}




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

// Load items datatable
function invoice_items_datatable(table_id = "invoice_items_datatable") {
    let main_unique_id = $("#unique_id").val();

    let table = $("#" + table_id).DataTable({
        destroy: true,
        searching: false,
        paging: false,
        ordering: false,
        info: false,
        ajax: {
            type: "POST",
            url: ajax_url,
            data: {
                action: "invoice_items_datatable",
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
        ]
    });
}




// Edit sublist item
function inv_item_edit(unique_id) {
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "inv_item_edit", unique_id },
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
                    invoice_items_datatable("invoice_items_datatable");
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
                d.action        = action;
                d.from_date     = $("#from_date").val();
                d.to_date       = $("#to_date").val();
                d.company_name  = $("#company_name").val();
                d.project_name  = $("#project_name").val();
                d.customer_name = $("#customer_name").val();
                // d.status     = $("#status_fill").val(); // future filter
            }
        }
    });
}




// filter button
function sales_invoice_filter() {
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
function sales_invoice_delete(unique_id) {
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
                    action: "sales_invoice_delete",   // match crud.php
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



function sales_invoice_upload(upload_unique_id) {
    $("#upload_unique_id").val(upload_unique_id);
    documents_datatable(upload_unique_id);
    $("#siUploadModal").modal("show");
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



function fetch_item_details(item_id) {
    if (!item_id) return;

    $.ajax({
        url: "function.php",
        type: "POST",
        data: { action: "get_item_details", item_id: item_id },
        dataType: "json",
        success: function (response) {
            if (response.status === 1 && response.data) {
                $("#rate").val(response.data.unit_price);
                $("#tax").val(response.data.tax_value).trigger('change');
                calculate_amount();
            } else {
                $("#rate").val("");
                $("#tax").val("0").trigger('change');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching item details:", error);
        }
    });
}


