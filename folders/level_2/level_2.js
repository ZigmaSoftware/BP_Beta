$(document).ready(function () {
      init_datatable(table_id, form_name, action);
    po_filter();
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
//   const prNo = $("#pr_number").val();
//   if (!prNo) return;

    var company_id = $("#company_id").val();
    var project_id = $("#project_id").val();

     if (company_id && project_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                company_id: company_id,
                project_id: project_id,
                action: "get_pr_sublist",
            },
            success: function (res) {
                $("#pr_sublist_content").html(res);
                const modal = new bootstrap.Modal(document.getElementById("pr_plus_btn"));
                modal.show();
            },
            error: function () {
                alert("Failed to load sublist");
            }
        });
    }else{
        const modalEl = document.getElementById("pr_plus_btn");
    const modalInstance = bootstrap.Modal.getInstance(modalEl);

    if (modalInstance) {
        modalInstance.hide();
    }
        Swal.fire("Please Choose company and project name");
        return;
    }
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
});
function calculate_amount() {
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discount_type = $("#discount_type").val(); // 1 = %, 2 = ₹
    let tax_percent = parseFloat($("#tax option:selected").data("extra")) || 0;

    let total = qty * rate;

    if (discount_type === "1") {
        total -= (discount / 100) * total;
    } else if (discount_type === "2") {
        total -= discount;
    }

    total += (tax_percent / 100) * total;

    $("#amount").val(total.toFixed(2));
}
function total_amount_calculation() {
    let total_basic = 0;
    let total_gst = 0;

    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let qty = parseFloat($(this).find("td:eq(5)").text()) || 0;
        let rate = parseFloat($(this).find("td:eq(6)").text()) || 0;
        if (qty <= 0) {
        console.log("Skipping row due to zero quantity");
        return;  // Skip this iteration
    }
        let discount = parseFloat($(this).find("td:eq(8)").text()) || 0;
        let discount_type = $(this).find("td:eq(7)").text().trim(); // ₹ or %
        let tax_text = $(this).find("td:eq(9)").text();
        let tax_percent = parseFloat(tax_text.match(/\d+/)) || 0;

        let item_total = qty * rate;
        let discount_amt = 0;

        if (discount_type === "₹") {
            discount_amt = discount;
        } else if (discount_type === "%") {
            discount_amt = (discount / 100) * item_total;
        }

        item_total -= discount_amt;

        let gst_amt = (tax_percent / 100) * item_total;
        total_gst += gst_amt;

        total_basic += item_total;
         console.log(`Row debug → Qty: ${qty}, Rate: ${rate}, Discount: ${discount} (${discount_type}), Tax: ${tax_percent}%`);
    });

    let freight = parseFloat($("#freight_value").val()) || 0;
    let freight_tax = parseFloat($("#freight_tax option:selected").data("extra")) || 0;

    let pack = parseFloat($("#packing_forwarding").val()) || 0;
    let pack_tax = parseFloat($("#packing_forwarding_tax option:selected").data("extra")) || 0;

    let other = parseFloat($("#other_charges").val()) || 0;
    let other_tax = parseFloat($("#other_tax option:selected").data("extra")) || 0;

    let freight_gst = (freight * freight_tax) / 100;
    let pack_gst = (pack * pack_tax) / 100;
    let other_gst = (other * other_tax) / 100;

    let freight_amt = freight + freight_gst;
    let pack_amt = pack + pack_gst;
    let other_amt = other + other_gst;

    let tcs_per = parseFloat($("#tcs_percentage").val()) || 0;
    let round_off = parseFloat($("#round_off").val()) || 0;

    let net = total_basic + total_gst;
    let gross = net + freight_amt + pack_amt + other_amt;

    let tcs_amt = (gross * tcs_per) / 100;
    gross += tcs_amt + round_off;

    // Set values in form
    $("#freight_amount").val(freight_amt.toFixed(2));
    $("#packing_forwarding_amount").val(pack_amt.toFixed(2));
    $("#other_charges_percentage").val(other_amt.toFixed(2));
    $("#tcs_amount").val(tcs_amt.toFixed(2));
    $("#net_amount").val(net.toFixed(2));
    $("#gross_amount").val(gross.toFixed(2));
    $("#total_sub_amount").val(net.toFixed(2));

    let total_all_gst = total_gst + freight_gst + pack_gst + other_gst;
    $("#total_gst_amount").val(total_all_gst.toFixed(2));
}

function handleDiscountTypeChange() {
    const type = $("#discount_type").val();
    if (type === "1" || type === "2") {
        $("#discount").removeAttr("readonly");
    } else {
        $("#discount").val("").attr("readonly", true);
    }
    sub_total_amount(); // Recalculate on change
}
function sub_total_amount(tax_value = "") {
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discount_type = $("#discount_type").val(); // 1 = %, 2 = ₹

    // Block if qty is zero or negative
    if (qty <= 0 || rate <= 0) {
        $("#amount").val("0.00");
        return;
    }

    let total = qty * rate;

    // Apply Discount
    if (discount_type === "1") {
        total -= (discount / 100) * total;
    } else if (discount_type === "2") {
        total -= discount;
    }

    // Prevent discount from making total negative
    total = Math.max(total, 0);

    // Apply tax if total is > 0
    if (tax_value && total > 0) {
        total += (parseFloat(tax_value) / 100) * total;
    }

    $("#amount").val(total.toFixed(2));
}

function total_sub_quantity() {
    let total_po_qty = 0;
    let total_qty = 0;
    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let po_qty = parseFloat($(this).find("td:eq(3)").text()) || 0;
        let qty = parseFloat($(this).find("td:eq(4)").text()) || 0;
        total_po_qty += po_qty;
        total_qty += qty;
    });
    $("#total_po_quantity").val(total_po_qty);
    $("#total_quantity").val(total_qty);
}

$("#quantity, #rate, #discount").on("keyup change", function () {
    calculate_amount();
    total_amount_calculation();
    total_sub_quantity();
});

var form_name = 'level_2';
var table_id = 'level_2_datatable';
var action = 'datatable';
var ajax_url = sessionStorage.getItem("folder_crud_link");
var url = sessionStorage.getItem("list_link");

function level_2_cu(unique_id = "") {
    if (!is_online()) {
        sweetalert("no_internet");
        return false;
    }

    if (!form_validity_check("was-validated")) {
        sweetalert("form_alert");
        return false;
    }

    const rowCount = $("#purchase_order_sub_datatable tbody tr").length;
    if (rowCount === 0 || $("#purchase_order_sub_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }

    const apprStatus = $("#appr_status").val();
    const cancelReason = $("#cancelReason").val();

    if (!apprStatus) {
        sweetalert("cancel_reason", "", "", "Please select an Approval Status.");
        return false;
    }

    if ((apprStatus === "2" || apprStatus === "3") && !cancelReason.trim()) {
        sweetalert("cancel_reason", "", "", "Please provide a reason for Rejection or Cancellation.");
        return false;
    }

    // Prepare FormData
    let data = new FormData($("#purchase_order_form")[0]);

    // Append form_second inputs
    $("#form_second").serializeArray().forEach(({ name, value }) => {
        data.set(name, value);  // `set` ensures overwrite if exists
    });

    // Append custom fields
    data.set("action", "createupdate");
    data.set("unique_id", unique_id);
    data.set("appr_status", apprStatus);
    data.set("cancelReason", cancelReason);

    // Submit via AJAX
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
            let obj;
            try {
                obj = JSON.parse(res);
            } catch (e) {
                console.error("Invalid JSON response:", res);
                Swal.fire("Unexpected Error", "Server response is invalid.", "error");
                return;
            }

            if (!obj.status) {
                $(".createupdate_btn").text("Error");
                console.log("Error:", obj.error || obj);
            } else if (obj.status == 1){
                // Fix: Use obj.msg, not res.msg
                sendMail(unique_id);
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

/**
 * Send an email based on the given unique_id
 * @param {string} unique_id
 */
function sendMail(unique_id) {
    alert("mail function triggered");
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "sendmail",
            unique_id: unique_id
        },
        success: function (response) {
            try {
                const res = JSON.parse(response);
                console.log(res);
                if (res.message == true) {
                    alert("Email sent successfully!");
                } else {
                    alert("Failed to send email. Reason: " + (res.message || "Unknown error"));
                }
            } catch (e) {
                console.error("Invalid JSON response:", response);
                alert("Unexpected response from server.");
            }
        },
        error: function () {
            alert("AJAX request failed.");
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
    var appr_status         = $("#appr_status").val();

	var filter_data = {
		"company_name"      : company_name,
		"project_name"      : project_name,
		"from_date"         : from_date,
		"to_date"           : to_date,
		"appr_status"       : appr_status
	};


	init_datatable(table_id, form_name, action, filter_data);


}

// Sublist Add/Update using only screen_unique_id
function po_sublist_add_update() {
    let screen_unique_id = $("#screen_unique_id").val();
    let sublist_id = $("#sublist_unique_id").val() || "";
    if (!screen_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }

    let item_code = $("#item_code").val();
    let quantity = $("#quantity").val();
    let uom = $("#uom").val();
    let rate = $("#rate").val();
    let discount = $("#discount").val();
    let tax = $("#tax").val();
    let amount = $("#amount").val();
    
    // alert(
    //   "Item Code: " + item_code + "\n" +
    //   "Quantity: " + quantity + "\n" +
    //   "UOM: " + uom + "\n" +
    //   "Rate: " + rate + "\n" +
    //   "Discount: " + discount + "\n" +
    //   "Tax: " + tax + "\n" +
    //   "Amount: " + amount
    // );


    if (!item_code || !quantity || (quantity == 0 ) || !uom || !rate || !tax) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "po_sub_add_update",
            screen_unique_id,
            sublist_id,
            item_code,
            quantity,
            uom,
            rate,
            discount,
            tax,
            amount
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
                Swal.fire("Error", obj.error || "Operation failed", "error");
            }
            // purchase_order_sublist_datatable("purchase_order_sub_datatable");
// setTimeout(calculate_sublist_totals, 300); // slight delay to ensure data is rendered

        },
        error: function () {
            alert("Network error");
        }
    });
}


function po_sublist_add_update_pop_up(item_code,uom,quantity,pr_unique_id) {
    let screen_unique_id = $("#screen_unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

    if (!screen_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }


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
            pr_unique_id
            
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
    $("#item_code, #po_quantity, #quantity, #uom, #rate, #discount, #amount").val("");
    $("#tax").val("").trigger("change");
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
            $("#po_quantity").val(Math.round(d.quantity));
            let apprQty = parseFloat(d.appr_quantity);
            let finalQty = (!apprQty) ? Math.round(d.quantity) : Math.round(apprQty);
            $("#appr_quantity").val(finalQty);
            $("#quantity").val(Math.round(d.lvl_2_quantity));
            $("#uom").val(d.uom);
            $("#rate").val(d.rate);
            $("#discount").val(Math.round(d.discount));
            $("#discount_type").val(d.discount_type).trigger("change");
            handleDiscountTypeChange();
            $("#tax").val(d.tax).trigger("change");
            $("#amount").val(d.amount);
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
                $("#uom").val(data.uom || "");
            } else {
                Swal.fire("Item details not found.");
            }
        },
        error: function () {
            Swal.fire("Failed to fetch item details.");
        }
    });
}


function level_2_delete(unique_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	confirm_delete('delete')
	.then((result) => {
		if (result.isConfirmed) {

			var data = {
				"unique_id" 	: unique_id,
				"action"		: "delete"
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
						init_datatable(table_id,form_name,action);
					}
					sweetalert(msg,url);
				}
			});

		} else {
			// alert("cancel");
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

function calculate_amount() {
    let qty = parseFloat($("#quantity").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let discount_type = $("#discount_type").val(); // 1 = %, 2 = ₹
    let tax_percent = parseFloat($("#tax option:selected").data("extra")) || 0;

    if (qty <= 0 || rate <= 0) {
        $("#amount").val("0.00");
        return;
    }

    let total = qty * rate;

    if (discount_type === "1") {
        total -= (discount / 100) * total;
    } else if (discount_type === "2") {
        total -= discount;
    }

    total = Math.max(total, 0);

    if (total > 0) {
        total += (tax_percent / 100) * total;
    }

    $("#amount").val(total.toFixed(2));
}


function calculate_sublist_totals() {
    let total_po_qty        = 0;
    let total_appr_quantity = 0;
    let total_qty           = 0;
    let total_amt           = 0;

    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let po_qty = parseFloat($(this).find("td:eq(3)").text()) || 0;
        let appr_qty = parseFloat($(this).find("td:eq(4)").text()) || 0;
        let qty = parseFloat($(this).find("td:eq(5)").text()) || 0;
        let amt = parseFloat($(this).find("td:eq(10)").text()) || 0;

        total_po_qty += po_qty;
        total_appr_quantity += appr_qty;
        total_qty += qty;
        total_amt += amt;
    });

    $("#total_po_quantity").val(total_po_qty.toFixed(2));
    $("#total_appr_quantity").val(total_appr_quantity.toFixed(2));
    $("#total_quantity").val(total_qty.toFixed(2));
    $("#total_sub_amount").val(total_amt.toFixed(2));
    $("#net_amount").val(total_amt.toFixed(2));
    total_amount_calculation();
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


function po_status_approval() {
    var selectedValue = $("#appr_status").val();
    let internet_status = is_online();
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    if (selectedValue === "2" || selectedValue === "3") {
        // Show cancel/reject reason
        document.getElementById("cancelReason").style.display = 'block';
        document.getElementById("cancelReasonLabel").style.display = 'block';
    } else {
        // Hide and clear if Approve
        document.getElementById("cancelReason").style.display = 'none';
        document.getElementById("cancelReasonLabel").style.display = 'none';
        document.getElementById("cancelReason").value = "";
    }
}

//Upload Functions

function level_2_upload(unique_id){
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