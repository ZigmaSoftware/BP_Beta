$(document).ready(function () {
    //   init_datatable(table_id, form_name, action);
    po_filter()
});
$(document).ready(function () {
    $('#pr_plus_btn').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open');
    });
});

function loadSupplierDetails() {
    const supplier_id = $("#supplier_id").val();
    if (!supplier_id) return;

    $.ajax({
        type: "POST",
        url: sessionStorage.getItem("folder_crud_link"),
        data: {
            action: "get_supplier_details_json",
            supplier_id: supplier_id
        },
        dataType: "json",
        success: function (res) {
            if (res.status) {
                const d = res.data;
                $("#gst_no").val(d.gst_no);
                $("#pan_no").val(d.pan_no);
                $("#msme_type_display").val(d.msme_type);
                $("#msme_no").val(d.msme_value);
                $("#contact_person").val(d.contact_person_name);
                $("#vendor_contact_no").val(d.contact_person_contact_no);
            } else {
                alert("No supplier data found.");
            }
        },
        error: function () {
            alert("Error fetching supplier details.");
        }
    });
}

$(document).ready(function () {
    // On load
    loadSupplierDetails();

    // On change
    $("#supplier_id").on("change", function () {
        loadSupplierDetails();
    });
});


function show_pr_sublist() {
    const company_id = $("#company_id").val();
    const project_id = $("#project_id").val();
    const po_type    = $("#po_for").val(); // ✅ NEW

    const modalEl = document.getElementById("pr_plus_btn");

    // ✅ Validate all 3 fields
    if (!company_id || !project_id || !po_type) {
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        $("body").removeClass("modal-open").css("overflow", "auto");
        $(".modal-backdrop").remove();

        Swal.fire("Please choose Company, Project, and PO Type before proceeding.");
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_pr_sublist",
            company_id: company_id,
            project_id: project_id,
            po_type: po_type // ✅ NEW PARAM
        },
        success: function (res) {
            $("#pr_sublist_content").html(res);

            const modal = new bootstrap.Modal(modalEl, {
                backdrop: 'static',
                keyboard: false
            });

            $("body").removeClass("modal-open").css("overflow", "auto");
            $(".modal-backdrop").remove();

            modal.show();
        },
        error: function () {
            Swal.fire("Failed to load sublist");
        }
    });
}



$(document).ready(function () {
    let screen_id = $("#screen_unique_id").val();
    if (screen_id) {
        purchase_order_sublist_datatable("purchase_order_sub_datatable");
    }
    $('.select2').select2();

    $("#item_code").on("change", function () {
        const item_code = $(this).val();
        if (item_code) {
            get_item_details(item_code);
        } else {
            $("#uom").val("");
        }
    });
        
    // 🔹 Encapsulate logic in a reusable function
    function compareStates() {
        // Decide source: company_id or project_id
        const project_id = $("#from_comp").is(":checked")
            ? $("#company_id").val()
            : $("#project_id").val();
            
        // alert(project_id);
    
        const supplier_id = $("#supplier_id").val();
    
        if (project_id && supplier_id) {
            $.ajax({
                url: ajax_url,
                type: "POST",
                data: {
                    action: "compare_states",
                    project_id: project_id,
                    supplier_id: supplier_id
                },
                success: function (response) {
                    console.log("Compare States Response:", response);
                    if (response.status === "success") {
                        if (response.same_state) {
                            $("#tax_title").text("CGST + SGST");
                        } else if (response.igst_applicable) {
                            $("#tax_title").text("IGST");
                        }
                    } else {
                        console.warn("Compare states failed:", response);
                    }
                },
                error: function (xhr, status, error) {
                    console.error("Compare States Error:", error);
                }
            });
        } else {
            // reset if incomplete
            $("#tax_title").text("");
        }
    }
    
    // 🔹 Run on load
    $(document).ready(function () {
        compareStates();
    
        // 🔹 Run on change (detect checkbox & selects)
        $("#from_comp, #company_id, #project_id, #supplier_id").on("change", function () {
            compareStates();
        });
    });

    
    $(document).on("click", ".btn-soft-danger", function (e) {
        let unique_id = $("#unique_id").val();
        let screen_unique_id = $("#screen_unique_id").val();
        var url = sessionStorage.getItem("list_link");
        
        if (!unique_id) {  
            // unique_id empty → prevent normal redirect
            e.preventDefault();
    
            if (screen_unique_id) {
                $.ajax({
                    url: ajax_url,
                    type: "POST",
                    dataType: "json",
                    data: {
                        action: "cancel",
                        screen_unique_id: screen_unique_id
                    },
                    success: function (res) {
                        if (res.status == 1) {
                            // ✅ success → redirect
                            window.location.href = url;
                        } else {
                            // ❌ failed → show error
                            alert("Cancel failed: " + (res.error || res.msg));
                        }
                    },
                    error: function (xhr, status, err) {
                        alert("Server error: " + err);
                    }
                });
            } else {
                e.preventDefault();
                alert("No screen_unique_id found.");
            }
        }
        // else → unique_id exists → let the <a> redirect normally
    });


});

function calculate_amount() {
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discount_type = $("#discount_type").val(); // 0 = %, 1 = ₹
    let tax_percent = parseFloat($("#tax option:selected").data('extra')) || 0;

    let total = qty * rate;

    // Apply discount
if (discount_type === "1") {
    total -= (discount / 100) * total;
} else if (discount_type === "2") {
    total -= discount;
}

    // Apply tax
    total += (tax_percent / 100) * total;

    $("#amount").val(total.toFixed(2));
}

function total_amount_calculation() {
  let total_basic = 0;
  let item_gst_total = 0;

  // --- Loop through items in table ---
  $("#purchase_order_sub_datatable tbody tr").each(function () {
    let qty = parseFloat($(this).find("td:eq(3)").text()) || 0;
    let rate = parseFloat($(this).find("td:eq(4)").text()) || 0;
    let discount = parseFloat($(this).find("td:eq(6)").text()) || 0;
    let discount_type_text = $(this).find("td:eq(5)").text().trim(); // %, ₹
    let tax_text = $(this).find("td:eq(7)").text();
    let tax_percent = parseFloat(tax_text.match(/\d+/)) || 0;

    // Base total for item
    let item_total = qty * rate;

    // Discount
    let discount_amt = 0;
    if (discount_type_text === "₹") {
      discount_amt = discount;
    } else if (discount_type_text === "%") {
      discount_amt = (discount / 100) * item_total;
    }

    // Apply discount
    item_total -= discount_amt;

    // Add to total basic (pre-tax)
    total_basic += item_total;

    // Calculate GST
    let gst_amt = (tax_percent / 100) * item_total;
    item_gst_total += gst_amt;
  });

  // --- Additional charges ---
  let freight_value = parseFloat($("#freight_value").val()) || 0;
  let packing_value = parseFloat($("#packing_forwarding").val()) || 0;
  let other_value = parseFloat($("#other_charges").val()) || 0;

  let freight_tax = parseFloat($("#freight_tax option:selected").data("extra")) || 0;
  let packing_tax = parseFloat($("#packing_forwarding_tax option:selected").data("extra")) || 0;
  let other_tax = parseFloat($("#other_tax option:selected").data("extra")) || 0;

  // GST on charges
  let freight_gst = (freight_value * freight_tax) / 100;
  let packing_gst = (packing_value * packing_tax) / 100;
  let other_gst = (other_value * other_tax) / 100;

  // Amounts including GST
  let freight_amount = freight_value + freight_gst;
  let packing_amount = packing_value + packing_gst;
  let other_amount = other_value + other_gst;

  // --- Combine totals ---
  let total_gst = item_gst_total + freight_gst + packing_gst + other_gst;

  // ✅ Gross = (total_basic + all charge values) + total_gst
  //   (no double counting)
  let gross = total_basic + freight_value + packing_value + other_value + total_gst;

  // --- TCS & Round-off ---
  let tcs_percentage = parseFloat($("#tcs_percentage").val()) || 0;
  let tcs_amount = (gross * tcs_percentage) / 100;
  let round_off = parseFloat($("#round_off").val()) || 0;

  gross += tcs_amount + round_off;

  // --- Populate UI ---
  $("#freight_amount").val(freight_amount.toFixed(2));
  $("#packing_forwarding_amount").val(packing_amount.toFixed(2));
  $("#other_charges_percentage").val(other_amount.toFixed(2));
  $("#tcs_amount").val(tcs_amount.toFixed(2));
  $("#round_off").val(round_off.toFixed(2));

  // ✅ Output fields
  $("#net_amount").val(total_basic.toFixed(2));       // Total after discount, before any tax
  $("#total_gst_amount").val(total_gst.toFixed(2));   // Total GST from items + charges
  $("#gross_amount").val(gross.toFixed(2));           // Grand total after GST, TCS, Round off
  $("#total_sub_amount").val((total_basic + item_gst_total).toFixed(2)); // Items with GST only
}




function total_sub_quantity() {
    let total_qty = 0;
    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let qty = parseFloat($(this).find("td:eq(3)").text()) || 0;
        total_qty += qty;
    });
    $("#total_quantity").val(total_qty);
}

$("#quantity, #rate, #discount").on("keyup change", function () {
    calculate_amount();
    total_amount_calculation();
    total_sub_quantity();
});

var form_name = 'purchase_order';
var table_id = 'purchase_order_datatable';
var action = 'datatable';
var ajax_url = sessionStorage.getItem("folder_crud_link");
var url = sessionStorage.getItem("list_link");

function purchase_order_test_cu(unique_id = "") {
    if (!is_online()) {
        sweetalert("no_internet");
        return false;
    }

    // Validate both forms
    const mainForm = document.getElementById("purchase_order_form");
    const secondForm = document.getElementById("form_second");

    if (!mainForm.checkValidity() || !secondForm.checkValidity()) {
        mainForm.classList.add("was-validated");
        secondForm.classList.add("was-validated");
        sweetalert("form_alert");
        return false;
    }

    // Check sublist
    let rowCount = $("#purchase_order_sub_datatable tbody tr").length;
    if (rowCount === 0 || $("#purchase_order_sub_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }

    // FormData
    let data = new FormData(mainForm);
    $("#form_second").serializeArray().forEach(({ name, value }) => {
        data.append(name, value);
    });
    console.info(data);

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
        success: function (res) {
            let obj = JSON.parse(res);
            if (!obj.status) {
                $(".createupdate_btn").text("Error");
                console.log("Error:", obj.error);
            } else {
                sweetalert(obj.msg, url);
            }
        },
        error: function () {
            Swal.fire("Network Error", "Failed to submit form", "error");
        },
        complete: function () {
            $(".createupdate_btn").removeAttr("disabled").text("Save");
        }
    });
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

function po_filter(){
    
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var company_name        = $('#company_name').val();
	var project_name        = $('#project_name').val();
	var from_date           = $("#from_date").val();
    var to_date             = $("#to_date").val();
    var lvl_1_status              = $("#lvl_1_status").val();

console.log("L1 Status:", lvl_1_status);

    
	var filter_data = {
		"company_name"      : company_name,
		"project_name"      : project_name,
		"from_date"         : from_date,
		"to_date"           : to_date,
		"status"            : lvl_1_status,
	};

	init_datatable(table_id, form_name, action, filter_data);


}

// Sublist Add/Update using only screen_unique_id
function po_sublist_add_update() {
    let screen_unique_id = $("#screen_unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

    let item_code = $("#item_code").val();
    let quantity = $("#quantity").val();
    let uom = $("#uom").val();
    let rate = $("#rate").val();
    let discount = $("#discount").val();
    let discount_type = $("#discount_type").val(); 
    let tax = $("#tax").val();
    let amount = $("#amount").val();
    let item_remarks = $("#item_remarks").val();
    let delivery_date = $("#delivery_date").val();
    let entry_date = $("#entry_date").val(); // ✅ ADD THIS

    if (!item_code || !quantity || !uom || !rate || !tax) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "po_sub_add_update",
            screen_unique_id,
            sublist_unique_id: sublist_id,
            item_code,
            quantity,
            uom,
            rate,
            discount,
            discount_type,
            tax,
            amount,
            delivery_date,
            entry_date, // ✅ PASS THIS TO SERVER
            item_remarks
        },
        success: function (res) {
            let obj = JSON.parse(res);
            if (obj.status) {
                Swal.fire(obj.msg === "update" ? "Item updated" : "Item added");
                reset_sublist_form();
                purchase_order_sublist_datatable("purchase_order_sub_datatable");
                setTimeout(() => {
                    calculate_sublist_totals();
                    total_amount_calculation();
                }, 300);
            } else {
                // ✅ Handle server-side delivery date error message
                Swal.fire("Error", obj.msg || "Operation failed", "error");
            }
        },
        error: function () {
            alert("Network error");
        }
    });
}



function po_sublist_add_update_pop_up(item_code,uom,quantity,pr_unique_id,delivery_date,remarks) {
    let screen_unique_id = $("#screen_unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

    if (!screen_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }
    // alert(delivery_date);

    if (!item_code || !quantity || !uom) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "po_sub_add_update_modal",
            screen_unique_id,
            sublist_unique_id: sublist_id,
            item_code,
            quantity,
            uom,
            pr_unique_id,
            delivery_date,
            remarks
            
        },
        success: function (res) {
            let obj = JSON.parse(res);
            if (obj.status) {
                Swal.fire(obj.msg === "update" ? "Item updated" : "Item added");
                reset_sublist_form();
                purchase_order_sublist_datatable("purchase_order_sub_datatable");
                setTimeout(calculate_sublist_totals, 300);
                show_pr_sublist();
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
    $("#item_code").val("").trigger("change");
    $("#quantity").val("");
    $("#rate").val("");
    $("#discount").val("");
    $("#discount_type").val("3");  // reset to 'Select Discount Type'
    handleDiscountTypeChange();   // set discount readonly
    $("#tax").val("").trigger("change");
    $("#amount").val("");
    $("#item_remarks").val("");
    $("#delivery_date").val("");
    $("#uom").val("").trigger("change");
    $(".po_sublist_add_btn").text("Add");
}



// Fetch sublist via screen_unique_id
function purchase_order_sublist_datatable(table_id = "purchase_order_sub_datatable") {
    let screen_unique_id = $("#screen_unique_id").val();

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
                action: "purchase_order_sublist_datatable",
                screen_unique_id
            }
        },
        drawCallback: function () {
            calculate_sublist_totals(); // Call totals after rendering
        }
    });
}


function po_sub_edit(unique_id) {
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "po_sub_edit", unique_id },
        success: function (res) {
            let d = JSON.parse(res).data;
            $("#sublist_unique_id").val(d.unique_id);
            $("#item_code").val(d.item_code).trigger("change");
            $("#quantity").val(d.quantity);
            $("#uom").val(d.uom).trigger("change");
            $("#rate").val(d.rate);
            $("#discount").val(d.discount);
            $("#discount_type").val(d.discount_type).trigger("change");
            handleDiscountTypeChange();
            $("#tax").val(d.tax).trigger("change");
            $("#amount").val(d.amount);
            $("#item_remarks").val(d.item_remarks);
            $("#delivery_date").val(d.delivery_date);
        }
    });
}

function po_sub_delete(unique_id) {
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
                    action: "po_sub_delete",
                    unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    Swal.fire(obj.msg);
                    purchase_order_sublist_datatable("purchase_order_sub_datatable");
                    // purchase_order_sublist_datatable("purchase_order_sub_datatable");
                    setTimeout(calculate_sublist_totals, 300);
                    setTimeout(total_amount_calculation, 300);
                }
            });
        }
    });
}

function get_item_details(item_code = "") {
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_item_details_by_code",
            item_code
        },
        success: function (res) {
            const obj = JSON.parse(res);
            if (obj.status) {
                const data = obj.data;
                // Only update UOM if not editing existing sublist item
                if (!$("#sublist_unique_id").val()) {
                    $("#uom").val(data.uom_id || "").trigger('change');
                }
            } else {
                Swal.fire("Item details not found.");
            }
        },
        error: function () {
            Swal.fire("Failed to fetch item details.");
        }
    });
}

// function purchase_order_test_delete(unique_id) {
//     Swal.fire({  
//         title: "Are you sure?",
//         text: "This purchase order will be permanently deleted.",
//         icon: "warning",
//         showCancelButton: true,
//         confirmButtonText: "Yes, delete it!"
//     }).then((result) => {
//         if (result.isConfirmed) {
//             $.ajax({
//                 type: "POST",
//                 url: ajax_url,
//                 data: {
//                     action: "delete",
//                     unique_id: unique_id
//                 },
//                 success: function (res) {
//                     let obj = JSON.parse(res);
//                     if (obj.status) {
//                         Swal.fire("Deleted!", "Purchase Order deleted successfully.", "success");
//                         init_datatable(table_id, form_name, action); // refresh datatable
//                     } else {
//                         Swal.fire("Error", obj.error || "Unable to delete", "error");
//                     }
//                 },
//                 error: function () {
//                     Swal.fire("Network Error", "Failed to delete purchase order", "error");
//                 }
//             });
//         }
//     });
// }

function purchase_order_test_delete(unique_id = "") {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url      = sessionStorage.getItem("list_link");

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
                    var obj    = JSON.parse(data);
                    var msg    = obj.msg;
                    var status = obj.status;

                    if (status) {
                            $("#" + table_id).DataTable().ajax.reload(null, false); 
                    }
                    sweetalert(msg, url);
                }
            });
        }
    });
}

function get_tax_val(code){

    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (code) {
		var data = {
			"code" 	: code,
			"action": "get_tax_val",
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			dataType: 'json',
			success : function(data) {
				if (data.status === 'success' && data.data) {
                    sub_total_amount(data.data);
                }
			}
		});
	}
	
}

function sub_total_amount(tax_value = "") {
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discount_type = $("#discount_type").val(); // 0 = %, 1 = ₹
    let total = qty * rate;

    // Apply Discount
if (discount_type === "1") {
    total -= (discount / 100) * total;
} else if (discount_type === "2") {
    total -= discount;
}

    // Apply Tax
    if (tax_value) {
        let tax = tax_value / 100;
        total += total * tax;
    }

    $("#amount").val(total.toFixed(2));
}


function calculate_sublist_totals() {
    let total_qty = 0;
    let total_amt = 0;

    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let qty = parseFloat($(this).find("td:eq(3)").text()) || 0;
        let amt = parseFloat($(this).find("td:eq(8)").text()) || 0;

        total_qty += qty;
        total_amt += amt;
    });

    $("#total_quantity").val(total_qty.toFixed(2));
    $("#total_sub_amount").val(total_amt.toFixed(2));
    $("#net_amount").val(total_amt.toFixed(2));
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


function dateValidation() {
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');

    const fromDate = new Date(fromDateInput.value);
    const toDate = new Date(toDateInput.value);
    const currentDate = new Date();

    // Prevent future date for From Date
    if (fromDate > currentDate) {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        fromDateInput.value = `${year}-${month}-${day}`;
    }

    // Ensure To Date is not before From Date
    if (toDate < fromDate) {
        // Set To Date equal to From Date
        toDateInput.value = fromDateInput.value;
    }
}

function handleDiscountTypeChange() {
    const type = $("#discount_type").val();
    if (type === "1" || type === "2") {
        $("#discount").removeAttr("readonly");
    } else {
        $("#discount").val("").attr("readonly", true);
    }
    sub_total_amount(); // re-trigger recalculation
}

// Fetch and update billing address on company change
function fetch_company_address(company_id = "") {
    if (company_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "get_company_address",
                company_id: company_id
            },
            success: function (res) {
                const obj = JSON.parse(res);
                if (obj.status) {
                    $("#billing_address").val(obj.address);
                }
            }
        });
    }
}

// Fetch and update shipping address on project change
function fetch_project_address(project_id = "") {
    if (project_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "get_project_address",
                project_id: project_id
            },
            success: function (res) {
                const obj = JSON.parse(res);
                if (obj.status) {
                    $("#shipping_address").val(obj.address);
                }
            }
        });
    }
}

// Hook into select change events
$(document).ready(function () {
$("#company_id").on("change", function () {
    const companyId = $(this).val();

    // Clear dependent project and shipping address
    $("#project_id").html('<option value="">Select the Project Name</option>');
    $("#shipping_address").val("");

    // Fetch updated addresses and project list
    fetch_company_address(companyId);
    get_project_name(companyId); // This will populate new project options
});

    $("#project_id").on("change", function () {
        fetch_project_address($(this).val());
    });
});

// function view_purchase_order_test(unique_id = "") {
//     if (unique_id) {
//         var data = {
//             "unique_id": unique_id,
//             "action": "view_po"
//         };

//         $.ajax({
//             type: "POST",
//             url: ajax_url,
//             data: data,
//             dataType: "json",
//             success: function (response) {
//                 if (response.status == 1 || response.status === true) {
//                     window.open("index.php?file=purchase_order_test/view&unique_id=" + unique_id, "_blank");
//                 } else {
//                     alert(response.message || "Unable to open the view page.");
//                 }
//             },
//             error: function () {
//                 alert("Something went wrong. Please try again.");
//             }
//         });
//     }
// }

//Upload Functions

function purchase_order_test_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#poUploadModal').modal('show');
    
    sub_list_datatable("documents_datatable");
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

// Trigger SweetAlert when selecting file if > 5MB
$(document).on("change", "#test_file_qual", function () {
    let files = this.files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5 * 1024 * 1024) { // 5 MB
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Each file must be less than 5 MB.',
                    confirmButtonColor: '#3bafda'
                });
                // Clear the invalid file immediately
                $(this).val("");
                break;
            }
        }
    }
});

  function setMinDeliveryDate() {
    var entryDate = document.getElementById("entry_date").value;
    if (entryDate) {
      document.getElementById("delivery_date").setAttribute("min", entryDate);
    //   document.getElementById("quotation_date").setAttribute("min", entryDate);
    }
  }

  // Disable manual typing for delivery_date field
  function disableKeyboardInput() {
    var deliveryDateInput = document.getElementById("delivery_date");
    deliveryDateInput.addEventListener("keydown", function (e) {
      e.preventDefault(); // Prevent all keyboard input
    });
    // var quotationDateInput = document.getElementById("quotation_date");
    // quotationDateInput.addEventListener("keydown", function (e) {
    //   e.preventDefault(); // Prevent all keyboard input
    // });
  }

  // Set min on page load
  window.addEventListener("DOMContentLoaded", function () {
    setMinDeliveryDate();
    disableKeyboardInput();
  });

  // Update min when entry date changes
  document.getElementById("entry_date").addEventListener("change", function () {
    setMinDeliveryDate();
  });