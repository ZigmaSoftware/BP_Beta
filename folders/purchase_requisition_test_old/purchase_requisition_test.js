
$(document).ready(function () {
    let main_id = $("#unique_id").val();
    if (main_id) {
        purchase_sublist_datatable("purchase_sublist_datatable");
    }

    $('.select2').select2();

    // Trigger on load and bind change
    toggleSalesOrderField();
    $('#requisition_for').on('change', toggleSalesOrderField);

    // Item code change triggers item detail fetch
    $("#item_code").on("change", function () {
        const item_code = $(this).val();
        if (item_code) {
            get_item_details(item_code);
        } else {
            $("#item_description").val("");
            $("#uom").val("");
            $("#uom_id").val("");
        }
    });

    // Table init (listing)
    init_datatable(table_id, form_name, action);
});

$("#requisition_for, #requisition_type").on("change", function () {
    const requisitionFor = $("#requisition_for").val();
    const requisitionType = $("#requisition_type").val();

    const isSO = requisitionFor === "2";
    const isService = requisitionType === "683568ca2fe8263239";

    // ✅ Enforce: SO is only allowed with Service
    if (isSO && !isService) {
        $("#requisition_for").val("1").trigger("change"); // Force to Direct
        Swal.fire("SO is only allowed when Requisition Type is Service.");
        return;
    }

    if (isSO && isService) {
        $("#sales_order_id").prop("disabled", false);
        $("#sales_order_id").trigger("change");
    } else {
        $("#sales_order_id").prop("disabled", true).val("").trigger("change");

        // Load items by group for Direct or invalid pairings
if (requisitionType !== "") {
    let groupToSend = requisitionType;

    // Capital → load all items
    if (requisitionType === "683588840086c13657") {
        groupToSend = "all";
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_items_by_group",
            group_id: groupToSend
        },
        success: function (res) {
            $("#item_code").html(res).trigger("change");
        }
    });
}
 else {
            $("#item_code").html("<option value=''>Select the Item/Code</option>").trigger("change");
        }
    }
});


// SO change logic – only when Service type is selected
$("#sales_order_id").on("change", function () {
    const requisitionType = $("#requisition_type").val();
    const requisitionFor = $("#requisition_for").val();
    const salesOrderId = $(this).val();

    if (requisitionFor === "2" && requisitionType === "683568ca2fe8263239") {
        // Only allow if both conditions match
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "get_items_by_sales_order",
                sales_order_id: salesOrderId
            },
            success: function (res) {
                $("#item_code").html(res).trigger("change");
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

    var pr_number = $('#pr_number').val();
    var company_name = $('#company_name').val();
    var project_name = $('#project_name').val();
    var type_of_service = $('#type_of_service').val();
    var requisition_for = $('#requisition_for').val();
    var requisition_date = $('#requisition_date').val();

    var filter_data = {
        "pr_number": pr_number,
        "company_name": company_name,
        "project_name": project_name,
        "type_of_service": type_of_service,
        "requisition_for": requisition_for,
        "requisition_date": requisition_date
    };
    
    // console.log(filter_data);
    
    init_datatable(table_id, form_name, action, filter_data);
}






function init_datatable(table_id='',form_name='',action='', filter_data ='') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
		...filter_data
	}
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

// Delete Function
function purchase_requisition_test_delete(unique_id = "") {
    confirm_delete('delete').then((result) => {
        if (result.isConfirmed) {
            var data = {
                "unique_id": unique_id,
                "action": "delete"
            };

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function (data) {
                    var obj = JSON.parse(data);
                    var msg = obj.msg;
                    var status = obj.status;
                    var error = obj.error;

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

// Conditional: Enable/Disable Linked Sales Order
function toggleSalesOrderField() {
    var requisitionFor = $('#requisition_for').val();
    if (requisitionFor === '2') {
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
                purchase_sublist_datatable("purchase_sublist_datatable");
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
    // $("#preferred_vendor_id").val(null).trigger("change");

    // Optional: reset button text to "Add"
    $(".requisition_sublist_add_btn").text("Add");
}



function purchase_sublist_datatable(table_id = "purchase_sublist_datatable") {
    let main_unique_id = $("#unique_id").val();
    let ajax_url = sessionStorage.getItem("folder_crud_link");

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
                action: "purchase_sublist_datatable",
                main_unique_id: main_unique_id
            }
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

            // ✅ Alert item_code value before setting it
            // alert("Item Code Value: " + d.item_code);

            $("#sublist_unique_id").val(d.unique_id);
            $("#item_code").val(d.item_code).trigger("change");  // For Select2 to reflect change
            $("#item_description").val(d.item_description);
            $("#quantity").val(d.quantity);
            $("#uom").val(d.uom);
            // $("#preferred_vendor_id").val(d.preferred_vendor_id).trigger("change");
            // $("#budgetary_rate").val(d.budgetary_rate);
            $("#item_remarks").val(d.item_remarks);
            $("#required_delivery_date").val(d.required_delivery_date);
        }
    });
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
