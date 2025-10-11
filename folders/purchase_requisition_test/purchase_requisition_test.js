
$(document).ready(function () {
    let main_id = $("#unique_id").val();
    if (main_id) {
        purchase_sublist_datatable("purchase_sublist_datatable");
    }

    $('.select2').select2();

    toggleSalesOrderField();
    $('#requisition_for').on('change', toggleSalesOrderField);

    function loadItemsByReqType(req_type) {
        if (req_type) {
            let ajax_url = sessionStorage.getItem("folder_crud_link");

            $.post(ajax_url, { action: "get_items_by_group", group_id: req_type }, function (data) {
                $('#item_code').html(data).trigger("change.select2");
            }).fail(function (xhr, status, error) {
                alert("AJAX failed: " + status + ", " + error);
            });
        } else {
            $('#item_code').html("<option value=''>Select the Item/Code</option>").trigger("change.select2");
        }
    }

    // ✅ On page load → check existing value and call AJAX if needed
    const initial_req_type = $('#requisition_type').val();
    loadItemsByReqType(initial_req_type);

    // ✅ On change → reload items
    $('#requisition_type').on('change', function () {
        const req_type = $(this).val();
        loadItemsByReqType(req_type);
    });

    $("#item_code").on("change", function () {
        const item_code = $(this).val();
        if (item_code) {
            get_item_details(item_code);
        } else {
            $("#item_description, #uom, #uom_id").val("");
        }
    });

    init_datatable(table_id, form_name, action);
});

// Requisition Type / For Change Logic
$("#requisition_for, #requisition_type").on("change", function () {
    const requisitionFor = $("#requisition_for").val();
    const requisitionType = $("#requisition_type").val();
    const isSO = requisitionFor === "2";

    // Allow SO only for these requisition types
    const allowedSORequisitionTypes = [
        "1",                                 // Regular
        "683568ca2fe8263239",               // Service
        "683588840086c13657"                // Capital
    ];

    const isSOAllowed = allowedSORequisitionTypes.includes(requisitionType);

    
    // Enable/disable Sales Order dropdown
    if (isSO) {
        $("#sales_order_id").prop("disabled", false);
    } else {
        $("#sales_order_id").prop("disabled", true).val("").trigger("change");
    }
});


// Insert SO items when a Sales Order is selected (for all allowed types)
$("#sales_order_id").on("change", function () {
    const requisitionType = $("#requisition_type").val();
    const requisitionFor = $("#requisition_for").val();
    const salesOrderId = $(this).val();
    const main_unique_id = $("#unique_id").val();

    if (!salesOrderId) return;

    const allowedSORequisitionTypes = [
        "1", // Regular
        "683568ca2fe8263239", // Service
        "683588840086c13657" // Capital
    ];

    if (requisitionFor === "2" && allowedSORequisitionTypes.includes(requisitionType) || requisitionFor === "3" && allowedSORequisitionTypes.includes(requisitionType)) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "delete_sublist_by_main_id",
                main_unique_id: main_unique_id,
            },
            success: function () {
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: {
                        action: "get_items_by_sales_order",
                        sales_order_id: salesOrderId,
                        type : requisitionFor
                    },
                    success: function (res) {
                        const obj = JSON.parse(res);
                        if (!obj.status || obj.data.length === 0) {
                            Swal.fire("No items found in the selected Sales Order.");
                            return;
                        }

                        obj.data.forEach(item => {
                            $.ajax({
                                type: "POST",
                                url: ajax_url,
                                data: {
                                    action: "requisition_sub_add_update",
                                    main_unique_id: main_unique_id,
                                    sublist_unique_id: "",
                                    item_code: item.item_unique_id,
                                    item_description: item.description,
                                    quantity: item.quantity,
                                    uom: item.uom_id,
                                    item_remarks: "",
                                    required_delivery_date: "",
                                    from_sales_order: 1
                                },
                                success: function () {
                                    purchase_sublist_datatable("purchase_sublist_datatable");
                                }
                            });
                        });

                        Swal.fire("Sales Order items inserted.");
                    }
                });
            }
        });
    }
});


var form_name   = 'purchase_requisition';
var table_id    = 'purchase_requisition_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

function purchase_requisition_test_cu(unique_id = "") {
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
    let rowCount = $("#purchase_sublist_datatable tbody tr").length;
    if (rowCount === 0 || $("#purchase_sublist_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }

    let data = new FormData($("#purchase_requisition_form")[0]);
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
            let msg = obj.msg;
            let status = obj.status;
            let error = obj.error;

            if (!status) {
                $(".createupdate_btn").text("Error");
                console.log(error);
            } else {
                sweetalert(msg, url);
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



function item_filter() {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var pr_number        = $('#pr_number').val();
	var company_name     = $('#company_name').val();
	var project_name     = $('#project_name').val();
	var type_of_service  = $('#type_of_service').val();
	var requisition_for  = $('#requisition_for').val();
	var requisition_date = $('#requisition_date').val();

	var filter_data = {
		"pr_number": pr_number,
		"company_name": company_name,
		"project_name": project_name,
		"type_of_service": type_of_service,
		"requisition_for": requisition_for,
		"requisition_date": requisition_date
	};

	init_datatable(table_id, form_name, action, filter_data);

	// ✅ Reset to first page after applying filter
	setTimeout(() => {
		$('#' + table_id).DataTable().page(0).draw(false);
	}, 300);
}



function init_datatable(table_id = '', form_name = '', action = '', filter_data = {}) {
	var table = $("#" + table_id);
	var data = {
		"action": action,
		...filter_data
	};

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	// ✅ Destroy if already initialized
	if ($.fn.DataTable.isDataTable(table)) {
		table.DataTable().clear().destroy();
	}

	table.DataTable({
		ordering: true,
		searching: true,
		pageLength: 10,
		displayStart: 0, // ✅ Force start from first page
		ajax: {
			url: ajax_url,
			type: "POST",
			data: data
		}
	});
}

// Delete Function
function purchase_requisition_test_delete(unique_id = "") {
    Swal.fire({
        title: 'Are you sure?',
        html: `
            <textarea id="delete_remarks_input" class="swal2-textarea" 
                placeholder="Enter delete remarks..." rows="5" 
                style="width:100%; resize: vertical;"></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        focusConfirm: false,
        preConfirm: () => {
            const remarks = document.getElementById('delete_remarks_input').value.trim();
            if (!remarks) {
                Swal.showValidationMessage('Remarks are required for delete');
                return false;
            }
            return remarks;
        },
        didOpen: () => {
            const textarea = document.getElementById('delete_remarks_input');
            textarea.focus();

            // Optional: Submit on Ctrl+Enter
            textarea.addEventListener('keydown', function (e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    Swal.clickConfirm();
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const remarks = result.value;

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    "unique_id": unique_id,
                    "action": "delete",
                    "remarks": remarks
                },
                success: function (data) {
                    var obj = JSON.parse(data);
                    var msg = obj.msg;
                    var status = obj.status;

                    if (status) {
                        init_datatable(table_id, form_name, action);
                    }
                    sweetalert(msg, url);
                }
            });
        }
    });
}

// Project Name Load
function get_project_name(company_id = "") {
    if (company_id) {
        var data = {
            "company_id": company_id,
            "action": "project_name"
        };
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#project_id").html(data);
                }
            }
        });
    }
}

function get_linked_so(requisition_for = "") {
    let project_id = $("#project_id").val(); // ✅ get requisition_for value

    if (project_id) {
        var data = {
            "project_id": project_id,
            "requisition_for": requisition_for, // ✅ send it to ajax
            "action": "linked_so"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#sales_order_id").html(data);
                }
            }
        });
    }
}

// ✅ Trigger when requisition_for changes
$(document).on("change", "#requisiton_for", function () {
    let project_id = $("#project_id").val(); // or however you get current project_id
    get_linked_so(project_id);
});



// Conditional: Enable/Disable Linked Sales Order
function toggleSalesOrderField() {
    var requisitionFor = $('#requisition_for').val();
    if (requisitionFor === '2' || requisitionFor === '3') {
        $('#sales_order_id').prop('disabled', false);
    } else {
        $('#sales_order_id').prop('disabled', true).val('').trigger('change');
    }
}

function requisition_sublist_add_update() {
    let main_unique_id = $("#unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();
    
    if (!main_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }

    let item_code = $("#item_code").val();
    let item_description = $("#item_description").val();
    let quantity = $("#quantity").val();
    let uom = $("#uom_id").val();
    // let preferred_vendor_id = $("#preferred_vendor_id").val();
    // let budgetary_rate = $("#budgetary_rate").val();
    let item_remarks = $("#item_remarks").val();
    let required_delivery_date = $("#required_delivery_date").val();

    if (!item_code || !quantity || !uom || !required_delivery_date) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }

    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "requisition_sub_add_update",
            main_unique_id: main_unique_id,
            sublist_unique_id: sublist_id,
            item_code,
            item_description,
            quantity,
            uom,
            // preferred_vendor_id,
            // budgetary_rate,
            item_remarks,
            required_delivery_date
        },
        success: function (res) {
            let obj = JSON.parse(res);

            if (obj.status) {
                Swal.fire(obj.msg === "update" ? "Item updated" : "Item added");
            
                reset_sublist_form();
                afterEditSuccess();
                purchase_sublist_datatable("purchase_sublist_datatable");
            
                // ✅ Reset button label and color to "Add" (green)
                $("#sublist_btn_text").text("Add");
                $(".requisition_sublist_add_btn")
                    .removeClass("btn-primary btn-warning btn-danger")
                    .addClass("btn-success");
            } else {
                Swal.fire("Error", obj.error || "Operation failed", "error");
            }
        },
        error: function () {
            alert("Network error");
        }
    });
}

function reset_sublist_form() {
    $("#sublist_unique_id").val("");
    $("#item_code, #item_description, #quantity, #uom, #item_remarks").val("");
    $("#required_delivery_date").val("");

    // ✅ Reset button text and color properly
    $("#sublist_btn_text").text("Add");
    $(".requisition_sublist_add_btn")
        .removeClass("btn-primary btn-warning btn-danger")
        .addClass("btn-success");
}




function purchase_sublist_datatable(table_id = "purchase_sublist_datatable") {
    let main_unique_id = $("#unique_id").val();
    let type = $("#requisition_for").val();
    let so_id = $("#sales_order_id").val();
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    // Initialize DataTable
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
                action: "purchase_sublist_datatable",
                main_unique_id: main_unique_id,
                type: type,
                so_id : so_id
            }
        },
        columns: [
            { data: "s_no" },
            { data: "item_code" },
            { data: "item_desc" },
            { data: "quantity" },
            { data: "uom" },
            { data: "remarks" },
            { data: "req_date" },
            { data: "actions" }
        ]
    });

    // Handle child row toggle when clicking FAB items
    $("#" + table_id + " tbody")
    .off("click")
    .on("click", "td:nth-child(2) .fab-toggle", function (e) {
        e.stopPropagation();

        let tr = $(this).closest("tr");
        let row = table.row(tr);
        let sublist = row.data().sublist;
        let rowData = row.data();
        console.info(rowData);

        // 🚨 If no sublist, do nothing (extra safety)
        if (!sublist || sublist.length === 0) {
            return;
        }

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
        } else {
            let html = `
                <table class="table table-sm table-bordered mt-2">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>UOM</th>
                            <th>Remarks</th>
                        </tr>
                    </thead>
                    <tbody>`;
            
            sublist.forEach(function (sub) {
                let quantityInputHtml = row.data().quantity;
                console.info(quantityInputHtml);
                
                // Use jQuery to parse the HTML and get the input value
                let parentQty = parseFloat(row.data().quantity) || 0;    
                
                let childQty = parseFloat(sub.qty) || 0;
                let totalQty = parentQty * childQty;
                
                html += `
                    <tr>
                        <td>${sub.sno}</td>
                        <td>${sub.item}</td>
                        <td>${totalQty}</td>
                        <td>${sub.uom}</td>
                        <td>${sub.remarks}</td>
                    </tr>`;
            });
            
            html += `</tbody></table>`;


            row.child(html).show();
            tr.addClass("shown");
        }
    });

}





function pr_sub_edit(unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "pr_sub_edit", unique_id },
        success: function (res) {
            let d = JSON.parse(res).data;
            $("#sublist_unique_id").val(d.unique_id);

            const requisitionType = $("#requisition_type").val();
            let group_id = "all";

            // Decide group based on requisition type
            if (requisitionType === "683568ca2fe8263239") {
                group_id = "683568ca2fe8263239"; // Service
            } else if (requisitionType === "683588840086c13657") {
                group_id = "683588840086c13657"; // Capital
            } else {
                group_id = "1"; // Regular (default for all others)
            }

            // Ensure item_code is a select
            if (!$("#item_code").is("select")) {
                $("#item_code").replaceWith(
                    `<select id="item_code" name="item_code" class="form-control select2"></select>`
                );
                $('.select2').select2();
            }

            // Load filtered items and include the current editing item if missing
            $.post(ajax_url, { action: "get_items_by_group", group_id: group_id }, function (options_html) {
                $('#item_code').html(options_html);

                // Check if current item_code exists in the options
                if ($(`#item_code option[value='${d.item_code}']`).length === 0) {
                    const text = `${d.item_code_text || d.item_code}`;
                    $("#item_code").append(`<option value="${d.item_code}" data-temporary="1">${text}</option>`);
                }

                $("#item_code").val(d.item_code).trigger("change");
            });

            $("#item_description").val(d.item_description);
            $("#quantity").val(d.quantity);
            $("#uom").val(d.uom);
            $("#item_remarks").val(d.item_remarks);
            $("#required_delivery_date").val(d.required_delivery_date);
           // Change button to "Edit" mode with proper color
            $("#sublist_btn_text").text("Edit");
            $(".requisition_sublist_add_btn")
                .removeClass("btn-success btn-warning btn-primary") // Clean slate
                .addClass("btn-primary"); // Navy blue for edit
            

            
        }
    });
}

function afterEditSuccess() {
    const $itemSelect = $('#item_code');

    // Remove any option with attribute data-temporary="1"
    $itemSelect.find("option[data-temporary='1']").remove();

    // Optional: Reset selection if needed
    $itemSelect.val('').trigger('change.select2');
}




function pr_sub_delete(unique_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This item will be deleted",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            let ajax_url = sessionStorage.getItem("folder_crud_link");

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "pr_sub_delete",
                    unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    Swal.fire(obj.msg);
                    purchase_sublist_datatable("purchase_sublist_datatable");
                }
            });
        }
    });
}

function get_item_details(item_code = "") {
    const ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_item_details_by_code",
            item_code: item_code
        },
success: function (res) {
    const obj = JSON.parse(res);
    if (obj.status) {
        const data = obj.data;
        $("#item_description").val(data.description || "");
        $("#uom").val(data.uom || "");  // Display UOM name
        $("#uom_id").val(data.uom_id || "");  // Hidden input to hold uom_unique_id
    } else {
        Swal.fire("Item details not found.");
    }
},

        error: function () {
            Swal.fire("Failed to fetch item details.");
        }
    });
}
function purchase_requisition_test_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#grnUploadModal').modal('show');
    
    sub_list_datatable("documents_datatable");
}
function sub_list_datatable (table_id = "", form_name = "", action = "", upload_id = "") {
    // alert("test");
    var upload_unique_id = $("#upload_unique_id").val();
    
    var table = $("#"+table_id);
	var data 	  = {
        "upload_unique_id"    : upload_unique_id,
		"action"	            : table_id, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
        "searching": false,
        "paging":   false,
        "ordering": false,
        "info":     false,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

function documents_add_update(unique_id = "") {
    var internet_status = is_online();
    var data = new FormData();

    var type = $("#type").val();
    var upload_unique_id = $("#upload_unique_id").val();
    var image_s = document.getElementById("test_file_qual");

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("documents_form");

    if (is_form) {
        data.append("type", type);

        let invalidFile = false;
        let allowedTypes = [
            "application/pdf",
            "image/jpeg",
            "image/png",
            "image/gif",
            "image/bmp",
            "image/webp",
            "image/svg+xml",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
            "text/plain",
            "application/vnd.ms-excel",
            "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
            "text/csv"
        ];

        // Check file types before appending
        if (image_s && image_s.files.length > 0) {
            for (var i = 0; i < image_s.files.length; i++) {
                let file = image_s.files[i];
                if (!allowedTypes.includes(file.type)) {
                    invalidFile = true;
                    break;
                }
                data.append("test_file[]", file);
            }
        } else {
            // If no new file, send existing file info if present
            var existing_file = $("#existing_file_attach").val();
            if (existing_file) {
                data.append("existing_file_attach", existing_file);
            }
        }

        data.append("upload_unique_id", upload_unique_id);
        data.append("unique_id", unique_id);
        data.append("action", "documents_add_update");

        if (invalidFile) {
            Swal.fire({
                icon: 'error',
                title: 'Invalid File Format',
                text: 'Only images and PDF files are allowed.',
                confirmButtonColor: '#3bafda'
            });
            return;
        }

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = "";

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function () {
                $(".documents_add_update_btn").attr("disabled", "disabled").text("Loading...");
            },
            success: function (data) {
                var obj;
                try {
                    obj = JSON.parse(data);
                } catch (e) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Server Error',
                        text: 'Invalid server response.',
                        confirmButtonColor: '#3bafda'
                    });
                    $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                    form_reset("documents_form");
                    return;
                }

                var msg = obj.msg;
                var status = obj.status;

                // Handle specific messages
                if (msg === "invalid_file_format") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Format',
                        text: 'Only images and PDF files are allowed.',
                        confirmButtonColor: '#3bafda'
                    });
                    $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                    form_reset("documents_form");
                    return;
                }

                if (msg === "missing_fields") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Missing Required Fields',
                        text: 'Please ensure both Document Type and Upload Reference are filled.',
                        confirmButtonColor: '#3bafda'
                    });
                    $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                    return;
                }

                if (msg === "no_file_selected") {
                    Swal.fire({
                        icon: 'warning',
                        title: 'No File Selected',
                        text: 'Please choose a file to upload or select an existing one.',
                        confirmButtonColor: '#3bafda'
                    });
                    $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                    return;
                }

                if (!status) {
                    $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                    form_reset("documents_form");
                    sweetalert(msg || "Error", url);
                } else {
                    if (msg !== "already" && msg !== "invalid_file_format") {
                        form_reset("documents_form");

                        var newValue = Number($("#document_value").val()) + 1;
                        $("#document_value").val(newValue);
                    }

                    $(".documents_add_update_btn").removeAttr("disabled");

                    if (unique_id && msg === "update") {
                        $(".documents_add_update_btn").text("Update");
                    } else {
                        $(".documents_add_update_btn").text("Add");
                        $(".documents_add_update_btn").attr("onclick", "documents_add_update('')");
                    }

                    
                    sweetalert(msg, url);
                }
                $("#upload_unique_id").val(upload_unique_id); 
                sub_list_datatable("documents_datatable");
            },
            error: function () {
                $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                sweetalert("Network Error");
                form_reset("documents_form");
            }
        });

    } else {
        sweetalert("form_alert");
    }
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

function documents_delete (unique_id = "") {
    // alert(unique_id);
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "documents_delete"
                }
    
                $.ajax({
                    type 	: "POST",
                    url 	: ajax_url,
                    data 	: data,
                    success : function(data) {
    
                        var obj     = JSON.parse(data);
                        var msg     = obj.msg;
                        var status  = obj.status;
                        var error   = obj.error;
    
                        if (!status) {
                            url 	= '';                            
                        } else {
                            sub_list_datatable("documents_datatable");
                        }
                        sweetalert(msg,url);
                    }
                });
    
            } else {
                // alert("cancel");
            }
        });
    }
}

// Prevent manual typing in date inputs
document.querySelectorAll("input[type='date']").forEach(function(el) {
    el.addEventListener("keydown", function(e) {
        e.preventDefault(); // stops manual typing
    });
});


// =============================================================
// ITEM-WISE STATUS MODAL HANDLER
// =============================================================
function showStatusModal(pr_id) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");

  Swal.fire({
    title: "Fetching status...",
    allowOutsideClick: false,
    backdrop: true,
    didOpen: () => Swal.showLoading()
  });

  $.ajax({
    url: ajax_url,
    type: "POST",
    data: { action: "fetch_item_status", main_unique_id: pr_id },
    dataType: "json",
    success: function (resp) {
      Swal.close();
      const tbody = $("#itemStatusTable tbody").empty();

      if (!resp.status || !resp.data || resp.data.length === 0) {
        tbody.append(
          "<tr><td colspan='9' class='text-center text-muted'>No item data found</td></tr>"
        );
        $("#statusModal").modal("show");
        return;
      }

      resp.data.forEach((row, i) => {
        const l1 = renderStatusBadge(row.status);
        const l2 = row.status == 2 ? "" : renderStatusBadge(row.lvl_2_status); // hide L2 if L1 rejected
        const reason = row.reason || row.lvl_2_reason || "";

        tbody.append(`
          <tr>
            <td>${i + 1}</td>
            <td>${row.item_code}</td>
            <td>${row.item_description}</td>
            <td>${row.quantity}</td>
            <td>${row.uom}</td>
            <td>${row.required_delivery_date}</td>
            <td>${l1}</td>
            <td>${l2}</td>
            <td>${reason}</td>
          </tr>
        `);
      });

      $("#statusModal").modal("show");
    },
    error: function () {
      Swal.fire("Error", "Unable to fetch item statuses.", "error");
    }
  });
}


// =============================================================
// STATUS BADGE RENDERER
// =============================================================
function renderStatusBadge(statusVal) {
  switch (parseInt(statusVal)) {
    case 1:
      return `<span class="badge bg-success">Approved</span>`;
    case 2:
      return `<span class="badge bg-danger">Rejected</span>`;
    default:
      return `<span class="badge bg-warning text-dark">Pending</span>`;
  }
}

$('#statusModal').on('hidden.bs.modal', function () {
  setTimeout(() => {
    if ($.fn.DataTable.isDataTable('#purchase_requisition_datatable')) {
      $('#purchase_requisition_datatable').DataTable().columns.adjust().responsive.recalc();
    }
  }, 250);
});

window.addEventListener('show.bs.modal', () => {
  document.body.style.paddingRight = '0px';
});
