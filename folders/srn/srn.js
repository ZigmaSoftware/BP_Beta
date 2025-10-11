$(document).ready(function () {
    BidsHoFilter();
    const screenId = $("#screen_unique_id").val();
    if (screenId) {
        srn_sublist_datatable("srn_sublist_datatable");
    }
    $(document).on("input", "#now_received_qty", function () {
        recalculateAmount();
    });
    $(document).on("change", "#discount_type", function () {
        recalculateAmount();
    });
    $(document).on("input", "#paf, #freight, #other_charges, #round_off", function () {
        recalculateTotalAmount();
    });

    // âœ… Trigger on GST dropdown changes (important)
    $(document).on("change", "#gst_paf, #gst_freight, #gst_other", function () {
        recalculateTotalAmount();
    });

    if ($("#is_update_mode").val() === "true") {
        recalculateTotalAmount();
    }
    toggleGstField();
    
    if ($("#is_update_mode").val() === "true") {
        const poVal = $("#purchase_order_no").val();
        if (poVal) {
            get_po_date(poVal); // ðŸ‘ˆ manually trigger
        }
    }
    
});


var form_name   = 'srn';
var table_id    = 'srn_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

function logFormData(formData) {
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
}



function srn_cu(unique_id = "") {
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
    
    
    var ajax_url    = sessionStorage.getItem("folder_crud_link");
    var url         = sessionStorage.getItem("list_link");
    
    let count_sl    = $("#count_sl").val();

    let paf                 = $("#paf").val();
    let freight             = $("#freight").val();
    let other               = $("#other_charges").val();
    let round               = $("#round_off").val();
    let gst_paf             = $("#gst_paf").val();
    let gst_freight         = $("#gst_freight").val();
    let gst_other           = $("#gst_other").val();
    let eway_bill_no        = $("#eway_bill_no").val();
    let eway_bill_date      = $("#eway_bill_date").val();

    let $is_update = $("#is_update_mode").val();

    let data = new FormData($("#srn_form")[0]);
    data.append("action", "createupdate");
    data.append("unique_id", unique_id);
    data.append("paf", paf);
    data.append("freight", freight);
    data.append("other", other);
    data.append("round", round);
    data.append("gst_paf", gst_paf);
    data.append("gst_freight", gst_freight);
    data.append("gst_other", gst_other);
    data.append("eway_bill_no", eway_bill_no);
    data.append("eway_bill_date", eway_bill_date);
    
    // Then in your code:
    logFormData(data); // Instead of console.info(data)
    alert(data);
    

    let screen_unique_id = data.get("screen_unique_id");  // Getting the value of "screen_unique_id" field from FormData

    if (count_sl > 0){
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
                    update_Qty(screen_unique_id, $is_update);
                    // srn_sublist_add_update();
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
    } else {
        swal.fire({
            text: "No Sublist Found",
            icon: "error",
            confirmButton: "Ok"
        });
    }
}

function update_Qty(screen_unique_id, is_update){
    // alert("screen_unique_id: " + screen_unique_id);
    if(screen_unique_id) {
            // Prepare the data for sending to the server
            let data = {
                "action": "update_qty",  // Specify action to be handled by the backend
                "screen_unique_id": screen_unique_id,
                "is_update": is_update  // Send the screen_unique_id to the backend
            };

            // Send an AJAX request to the backend
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: data,
                success: function(response){
                    try {
                        let obj = JSON.parse(response);
                        if (obj.status) {
                            // alert("Quantity updated successfully");
                        } else {
                            alert("Failed to update quantity: " + obj.msg);
                        }
                    } catch (e) {
                        alert("Server error: " + response);
                    }
                },
                error: function() {
                    alert("Error in updating quantity");
                }
            });
        }
    }
// Project Name Load
function get_project_name(company_id = "") {
    // alert("ds");
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


function recalculateAmount() {
    let qty = parseFloat($("#now_received_qty").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let tax = parseFloat($("#tax_val").val()) || 0;

    let discount_type = $("#discount_type").val();

    let discountAmt = 0;

        // Step 1: Calculate base amount
    let base = qty * rate;

    if (discount_type === "1") {
            discountAmt = (base * discount) / 100;
            console.log("Discount Type: Percentage, Amount: " + discountAmt);
            console.log("Base Amount: " + base);
            console.log("Discount Percentage: " + discount);
    } else if (discount_type === "2") {
            discountAmt = discount; // Direct amount
    } else {
            discountAmt = 0; // No discount
    }

    // Step 2: Apply discount
    let afterDiscount = base - discountAmt;

    // Step 3: Apply tax
    let taxAmt = (afterDiscount * tax) / 100;

    // Final amount
    let finalAmount = afterDiscount + taxAmt;

    if (finalAmount < 0) {
        finalAmount = 0; // Ensure final amount is not negative
    }

    $("#amount").val(finalAmount.toFixed(2));
}

function recalculateTotalAmount() {
    let basic = parseFloat($("#basic").val()) || 0;
    let paf = parseFloat($("#paf").val()) || 0;
    let freight = parseFloat($("#freight").val()) || 0;
    let other = parseFloat($("#other_charges").val()) || 0;
    let round = parseFloat($("#round_off").val()) || 0;
    let tot_gst = parseFloat($("#tot_gst1").val()) || 0;

    let gst_paf = parseFloat($("#gst_paf option:selected").data("extra")) || 0;
    let gst_freight = parseFloat($("#gst_freight option:selected").data("extra")) || 0;
    let gst_other = parseFloat($("#gst_other option:selected").data("extra")) || 0;

    let taxed_paf = (paf * gst_paf) / 100;
    let taxed_freight = (freight * gst_freight) / 100;
    let taxed_other = (other * gst_other) / 100;

    let taxed_charges = taxed_paf + taxed_freight + taxed_other;


    if (round > 10) round = 10;
    if (round < -10) round = -10;

    $("#round_off").on("input", function () {
        let val = parseFloat($(this).val());
        if (val > 10 || val < -10) {
            Swal.fire("Round Off should be between -10.00 and +10.00");
        }

        if(val > 10){
            $("#round_off").val("10.00");
        } else if(val < -10) {
            $("#round_off").val("-10.00");
        }
    });

    let total_taxed = taxed_charges + tot_gst;

    // Total calculation
    let total = basic + paf + freight + other + taxed_charges + round;

    $("#tot_gst").val(total_taxed.toFixed(2));
    $("#final_total").text(total.toFixed(2));
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



function get_purchase_order_no(vendor_id = "") {
    // alert();
    var project_id = $("#project_id").val();
    if (vendor_id) {
        var data = {
            "vendor_id": vendor_id,
            "project_id" : project_id,
            "action": "get_purchase_order_no"
        };
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#purchase_order_no").html(data);
                }
            }
        });
    }
}

function init_datatable(table_id='',form_name='',action='') {
    var company_name    = $("#company_name").val();
    var customer_name   = $("#customer_name").val();
    var status          = $("#status_fill").val();

    let from = $("#bids_ho_from").val();
    let to = $("#bids_ho_to").val();

    var table = $("#"+table_id);
    var data 	  = {
        "action"	    : action, 
        "from"	    : from, 
        "to"	    : to, 
        "company_name"	: company_name, 
        "customer_name"	: customer_name, 
        "status"	    : status
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    table.DataTable({
        ordering    : true,
        searching   : true,
        "ajax"		: {
            url 	: ajax_url,
            type 	: "POST",
            data 	: data,
            dataSrc : function(json) {
                // If your response is { data: [...] }, return json.data
                // Otherwise, adjust as needed
                // alert(json);
                console.info(json);
                return json.data || json;
            }
        },
        createdRow: function(row) {
            // Apply the 'text-end' class to the "Supplier Invoice No" column (assuming it's at index 6)
            $(row).find('td').eq(6).addClass('text-center');
        }
    });
}


// filter 
function BidsHoFilter() {

    init_datatable(table_id, form_name, action);
}


// Info Function
function srn_info(unique_id = "") {
    if (!unique_id) {
        Swal.fire("No record selected", "", "info");
        return;
    }

    // alert(ajax_url);
    var data = {
        "unique_id": unique_id,
        "action": "info"
    };

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        success: function (data) {
            var obj = JSON.parse(data);
            var msg = obj.msg || "No info available";
            var status = obj.status;
            var error = obj.error;
            var link = obj.iframe_src || "";
            var rows = obj.data || [];

            if ((status === true || status === 1) && rows.length > 0) {
                // Build HTML for the <tbody>
                let tbodyHtml = "";
                rows.forEach((row, index) => {
                    tbodyHtml += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${row.item_code}</td>
                            <td>${row.order_qty}</td>
                            <td>${row.uom}</td>
                            <td>${row.now_received_qty}</td>
                            <td>${row.update_qty}</td>
                            <td>${row.rate}</td>
                            <td>${row.tax_name}</td>
                            <td>${row.discount_type}</td>
                            <td>${row.discount}</td>
                            <td>${row.amount}</td>
                        </tr>
                    `;
                });

                // Replace the table body
                $("#srn_sublist_datatable tbody").html(tbodyHtml);

                // Step 2: Inject Iframe
                const iframeHtml = `
                    <iframe 
                        src="${link}" 
                        width="100%" 
                        height="400" 
                        frameborder="0" 
                        style="border:0; display:block;">
                    </iframe>
                `;
                $("#srnInfoModalBody").append(iframeHtml);

                // Open modal
                $('#srnInfoModal').modal('show');
            } else {
                Swal.fire("Error", error || "No data found", "error");
            }
        }
    });
}

// Delete Function
function srn_delete(unique_id = "") {
    if (!unique_id) {
        Swal.fire("No record selected", "", "info");
        return;
    }

    Swal.fire({
        title: "Are you sure?",
        text: "This record will be deleted",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "delete",
                    unique_id: unique_id
                },
                success: function (data) {
                    var obj = JSON.parse(data);
                    if (obj.status) {
                        Swal.fire("Deleted!", obj.msg || "Record deleted.", "success");
                        // Optionally refresh datatable or redirect
                        BidsHoFilter();
                    } else {
                        Swal.fire("Error", obj.error || "Could not delete record", "error");
                    }
                }
            });
        }
    });
}


function srn_sublist_add_update() {
    let screen_unique_id = $("#screen_unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

    if (!screen_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }

    var item_code = $("#item_code").val();
    var order_qty = parseFloat($("#order_qty").val()) || 0;
    var uom = $("#uom").val();
    var now_received_qty = parseFloat($("#now_received_qty").val()) || 0;
    var already_received_qty = parseFloat($("#already_received_qty").val()) || 0;
    
    // alert(item_code);
    // alert(order_qty);
    // alert(uom);
    // alert(now_received_qty);
    // alert(already_received_qty);
    
    var remarks = $("#remarks").val();

    let tot_qty = now_received_qty + already_received_qty;

    // If it's an update page, skip the validation on received quantity
    let isUpdatePage = $("#is_update_mode").val() == "true"; // Hidden field for page type
    // alert(isUpdatePage);

    // Check for all required fields
    let isInvalid = false;
    
    if (!item_code || !order_qty || !uom) {
        isInvalid = true;
    }
    
    if (!isUpdatePage && !now_received_qty) {
        isInvalid = true;
    }
    
    if (isInvalid) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }


    // alert(
    //     "Values:\n" +
    //     "Screen Unique ID: " + screen_unique_id + "\n" +
    //     "Sublist ID: " + sublist_id + "\n" +
    //     "Item Code: " + item_code + "\n" +
    //     "Order Qty: " + order_qty + "\n" +
    //     "UOM: " + uom + "\n" +
    //     "Now Received Qty: " + now_received_qty + "\n" +
    //     "Already Received Qty: " + already_received_qty + "\n" +
    //     "Total Qty: " + tot_qty + "\n" +
    //     "Is Update Page: " + isUpdatePage
    // );
    // For update page, we need to skip the check for received quantity exceeding order quantity
    if (isUpdatePage) {
        // Only send `now_received_qty` for the update page, without adding already received quantity
        let ajax_url = sessionStorage.getItem("folder_crud_link");

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "srn_sub_add_update",
                screen_unique_id: screen_unique_id,
                sublist_unique_id: sublist_id,
                item_code,
                order_qty,
                uom,
                remarks: remarks,
                tot_qty,  // Send only the updated received quantity
                update_qty: now_received_qty // Send updated qty to update the value
            },
            success: function (res) {
                let obj = JSON.parse(res);
                if (obj.status) {
                    Swal.fire("Item updated");

                    // âœ… Refresh the table
                    srn_sublist_datatable("srn_sublist_datatable");

                    // âœ… Reset the sublist form inputs
                    $("#sublist_unique_id").val("");
                    $("#item_code").val(null).trigger("change");
                    $("#uom").val(null).trigger("change");
                    $("#order_qty").val("");
                    $("#already_received_qty").val("");
                    $("#now_received_qty").val("");
                    $("#rate").val("");
                    $("#remarks").val("");
                    $("#amount").val("");
                    $("#tax").val("");
                    $("#discount_type").val("0").trigger("change"); // Set to default (0)
                    $("#discount").val("");
                } else {
                    Swal.fire("Error", obj.error || "Operation failed", "error");
                }
            },
            error: function () {
                alert("Network error");
            }
        });
    } else {
        // On Create Page, check if total received quantity exceeds the order quantity

        if (tot_qty > order_qty) {
            Swal.fire("Received quantity is higher than the ordered quantity.");
            return;
        }

        let ajax_url = sessionStorage.getItem("folder_crud_link");

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "srn_sub_add_update",
                screen_unique_id: screen_unique_id,
                sublist_unique_id: sublist_id,
                item_code,
                order_qty,
                uom,
                remarks: remarks,
                now_received_qty,  // Send the current received qty
                update_qty: now_received_qty // Send now_received_qty to update_qty column
            },
            success: function (res) {
                let obj = JSON.parse(res);

                if (obj.status) {
                    Swal.fire(obj.msg === "update" ? "Item updated" : "Item added");

                    // âœ… Refresh the table
                    srn_sublist_datatable("srn_sublist_datatable");

                    // âœ… Reset the sublist form inputs
                    $("#sublist_unique_id").val("");
                    $("#item_code").val(null).trigger("change");
                    $("#uom").val(null).trigger("change");
                    $("#order_qty").val("");
                    $("#already_received_qty").val("");
                    $("#now_received_qty").val("");
                    $("#rate").val("");
                    $("#remarks").val("");
                    $("#amount").val("");
                    $("#tax").val("");
                    $("#discount_type").val("0").trigger("change"); // Set to default (0)
                    $("#discount").val("");
                } else {
                    Swal.fire("Error", obj.error || "Operation failed", "error");
                }
            },
            error: function () {
                alert("Network error");
            }
        });
    }
}


function srn_sublist_datatable(table_id = "srn_sublist_datatable") {
    let screen_unique_id = $("#screen_unique_id").val();
    let unique_id   = $("#unique_id").val();
    let ajax_url = sessionStorage.getItem("folder_crud_link");
    let is_update = $("#is_update_mode").val();

    // Get GST select values, send "" if empty or 0
    let paf_tax = $("#gst_paf").val();
    paf_tax = (!paf_tax || paf_tax === "0") ? "" : paf_tax;

    let freight_tax = $("#gst_freight").val();
    freight_tax = (!freight_tax || freight_tax === "0") ? "" : freight_tax;

    let other_tax = $("#gst_other").val();
    other_tax = (!other_tax || other_tax === "0") ? "" : other_tax;

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
                action: "srn_sublist_datatable",
                screen_unique_id: screen_unique_id,
                unique_id: unique_id,
                is_update: is_update,
                paf_tax: paf_tax,
                freight_tax: freight_tax,
                other_tax: other_tax
            },
            dataSrc: function(json) {
                // Populate HTML fields from returned data
                if (json.total !== undefined) {
                    $("#basic").val(json.total);
                }

                if (json.taxed !== undefined) {
                    $("#tot_gst").val(json.taxed.toFixed(2));
                    $("#tot_gst1").val(json.taxed.toFixed(2));
                }

                // Populate PAF
                if (json.packing_forwarding !== undefined) {
                    $("#paf").val(json.packing_forwarding);
                }
                if (json.packing_forwarding_tax !== undefined && json.packing_forwarding_tax !== "" && json.packing_forwarding_tax !== 0) {
                    $("#gst_paf").val(json.packing_forwarding_tax).trigger("change");
                }

                // Populate Freight
                if (json.freight_value !== undefined) {
                    $("#freight").val(json.freight_value);
                }
                if (json.freight_tax !== undefined && json.freight_tax !== "" && json.freight_tax !== 0) {
                    $("#gst_freight").val(json.freight_tax).trigger("change");
                }

                // Populate Other Charges
                if (json.other_charges !== undefined) {
                    $("#other_charges").val(json.other_charges);
                }
                if (json.other_tax !== undefined && json.other_tax !== "" && json.other_tax !== 0) {
                    $("#gst_other").val(json.other_tax).trigger("change");
                }

                // Populate Round Off
                if (json.round_off !== undefined) {
                    $("#round_off").val(json.round_off);
                }
                
                if (json.count_sl !== undefined) {
                    $("#count_sl").val(json.count_sl);
                }

                recalculateTotalAmount();
                return json.data;
            }
        }
    });
}



function srn_sub_edit(unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");
    let is_update = $("#is_update_mode").val();

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "srn_sub_edit", unique_id, is_update: is_update },
        success: function (res) {
            let response = JSON.parse(res);
            let d = response.data;

            console.info(d);

            $("#sublist_unique_id").val(d.unique_id);
            $("#item_code").val(d.item_code).trigger("change");
            $("#uom").val(d.uom).trigger("change");
            
            $("#order_qty").val(d.order_qty);
            $("#already_received_qty").val(d.now_received_qty);
            $("#now_received_qty").val(d.update_qty);
            $("#rate").val(d.rate);
            $("#remarks").val(d.remarks);
            $("#amount").val(d.amount);

            // âœ… Set tax and discount values
            $("#tax").val(d.tax);
            $("#tax_val").val(response.tax);
            $("#discount_type").val(d.discount_type).trigger("change");
            $("#discount").val(d.discount);

            $(".srn_add_update_btn").text("Edit");

            // Optionally recalculate amount after setting tax/discount
            recalculateAmount();
        }
    });
}

function toggleGstField() {
  $('input[name="apply_gst"]').change(function () {
    if ($(this).val() === "yes") {
      $('#gst_label_wrapper, #gst_select_wrapper').show();
      $('#gst').prop('disabled', false);
    } else {
      $('#gst_label_wrapper, #gst_select_wrapper').hide();
      $('#gst').prop('disabled', true).val('');
    }
  });

  // Trigger on load
  $('input[name="apply_gst"]:checked').trigger('change');
}




function srn_sub_delete(unique_id) {
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
                    action: "srn_sub_delete",
                    unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    Swal.fire(obj.msg);
                    srn_sublist_datatable("srn_sublist_datatable");
                    $("#sublist_unique_id").val("");
                    $("#item_code").val(null).trigger("change");
                    $("#uom").val(null).trigger("change");
                    $("#order_qty").val("");
                    $("#already_received_qty").val("");
                    $("#now_received_qty").val("");
                    $("#rate").val("");
                    $("#amount").val("");
                    $("#tax").val("");
                    $("#discount_type").val("0").trigger("change");
                    $("#discount").val("");
                    $("#remarks").val(""); // âœ… Clear remarks also
                    let current = parseFloat($("#count_sl").val()) || 0; 
                    $("#count_sl").val(current - 1)
                }
            });
        }
    });
}

function number_check(no = 0) {

	if ((isNaN(no)) || (no == undefined) || (no == "")) {

		return 0;
	}

	return no;


}
function sub_total_amount_2(tax_value="") {
    var qty_value = parseFloat($("#qty").val()) || 0;
    var rate_value = parseFloat($("#rate").val()) || 0;
    var discount_value = parseFloat($("#discount").val()) || 0;

    var total_amount = qty_value * rate_value;

    // Apply discount
    if (discount_value) {
        var discount = discount_value / 100;
        var discount_amount = total_amount * discount;
        total_amount -= discount_amount;
    }

    // Apply tax
    if (tax_value) {
        var tax = tax_value / 100;
        var tax_amount = total_amount * tax;
        total_amount += tax_amount;
    }

    $("#amount").val(total_amount.toFixed(2));
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
                    sub_total_amount_2(data.data);
                }
			}
		});
	}
	
}

// Reset and disable dropdowns
function resetDropdowns(selectors) {
    selectors.forEach(function (selector) {
        $(selector).val(null).trigger("change").prop("disabled", true);
    });
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
        fromDateInput.value = `${month}-${day}`;
    }

    // Ensure To Date is not before From Date
    if (toDate < fromDate) {
        // Set To Date equal to From Date
        toDateInput.value = fromDateInput.value;
    }
}

$('#purchase_order_no').on('change', function () {
    let po_screen_id = $(this).val(); // assuming this is screen_unique_id from purchase_order table
    if (po_screen_id) {
        fetch_po_items(po_screen_id);
    }
});

function fetch_po_items(po_unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    // If screen_unique_id not generated, generate it
    if (!$("#screen_unique_id").val()) {
        const generated_id = "scr" + Math.random().toString(36).substring(2, 18);
        $("#screen_unique_id").val(generated_id);
    }

    let screen_unique_id = $("#screen_unique_id").val();

    // Step 1: Clear previous sublist for this screen_unique_id
    $.post(ajax_url, {
        action: "clear_srn_sublist",
        screen_unique_id: screen_unique_id
    }).done(function (clearRes) {
        try {
            let clearObj = JSON.parse(clearRes);
            if (!clearObj.status) {
                Swal.fire("Clear Failed", clearObj.error || "", "error");
                return;
            }

            fetch_po_items_logic(po_unique_id); // proceed if cleared
        } catch (e) {
            console.error("JSON Parse Error (clear):", clearRes);
            Swal.fire("Server error while clearing sublist.");
        }
    });
}


// Fetch PO Items for SRN
async function fetch_po_items_logic(po_unique_id) {
  const ajax_url = sessionStorage.getItem("folder_crud_link");

  try {
    // 🌀 Start loading overlay
    Swal.fire({
      title: "Loading PO Items...",
      html: `
        <div id="progress-text">Initializing...</div>
        <progress id="progress-bar" value="0" max="100" style="width:100%;"></progress>
        <br><b>Please wait — this may take a minute.</b>
      `,
      allowOutsideClick: false,
      allowEscapeKey: false,
      allowEnterKey: false,
      didOpen: () => {
        Swal.showLoading();
      },
    });

    // --- Fetch PO items from server ---
    const res = await $.post(ajax_url, {
      action: "get_po_items_for_srn",
      unique_id: po_unique_id,
    });

    const obj = JSON.parse(res);

    if (!obj.status || !obj.data?.length) {
      Swal.close();
      Swal.fire("No items found for this PO.");
      return;
    }

    // ✅ Set supplier details
    // $("#supplier_id").val(obj.supplier_id || "");
    // $("#supplier_name").val(obj.supplier_name || "");

    // --- Sequentially upload each PO item ---
    for (let i = 0; i < obj.data.length; i++) {
      const row = obj.data[i];
      await add_po_item_to_srn(row, po_unique_id);

      // Update progress bar
      const pct = Math.round(((i + 1) / obj.data.length) * 100);
      document.getElementById("progress-bar").value = pct;
      document.getElementById("progress-text").innerHTML = `Processing item ${i + 1} of ${obj.data.length}... (${pct}%)`;

      // Wait 1 second to prevent overload
      await new Promise((resolve) => setTimeout(resolve, 1000));
    }

    // --- Refresh SRN sublist table ---
    await srn_sublist_datatable("srn_sublist_datatable");

    // ✅ Replace loading with success
    Swal.fire({
      icon: "success",
      title: "PO Items Loaded",
      text: "All items have been successfully added to the SRN.",
      timer: 2000,
      showConfirmButton: false,
    });
  } catch (e) {
    console.error("Error loading SRN items:", e);
    Swal.close();
    Swal.fire("Server error while loading items.");
  }
}

// Add PO item to SRN sublist (insert or update)
function add_po_item_to_srn(row, po_unique_id) {
  const screen_unique_id = $("#screen_unique_id").val();
  const ajax_url = sessionStorage.getItem("folder_crud_link");

  return $.ajax({
    type: "POST",
    url: ajax_url,
    data: {
      action: "srn_sub_add_update",
      screen_unique_id: screen_unique_id,
      item_code: row.item_code,
      order_qty: row.lvl_2_quantity,
      uom: row.uom,
      now_received_qty: 0,
      po_unique_id: po_unique_id,
    },
    error: function (xhr, status, err) {
      console.error("Error adding SRN item:", row.item_code, err);
    },
  });
}



function get_project_address(project_id) {
    if (project_id) {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                "project_id": project_id,
                "action": "get_purchase_address"
            },
            dataType: "json", // Tell jQuery to expect JSON response
            success: function (data) {
                if (data && data.status && data.address) {
                    $("#received").val(data.address); // Insert address into HTML
                } else {
                    $("#received").val("No address found.");
                }
            },
            error: function () {
                $("#received").val("Error fetching address.");
            }
        });
    }
}

function get_po_date(po_uid){
    if(po_uid){
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                "po_uid": po_uid,
                "action": "get_po_date"
            },
            dataType: "json", // Tell jQuery to expect JSON response
            success: function (data) {
                if (data && data.status && data.date) {
                    $("#po_date").val(data.date); // Insert address into HTML
                } else {
                    $("#po_date").val("No date found.");
                }
            },
            error: function () {
                $("#po_date").val("Error fetching address.");
            }
        });
    }
    
}

function get_supplier_names(project_id) {
    if (project_id) {
        var data = {
            "project_id": project_id,
            "action": "get_supplier"
        };
        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(response) {
                console.log(response); // handle response
                $("#supplier_name").html(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error:", error);
            }
        });
    }
}

function get_cost_center(project_id) {
    if (project_id) {
        var data = {
            "project_id": project_id,
            "action": "cost_center"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(response) {
                console.log("Cost Center:", response);
                $("#cost_center").val(response);
            },
            error: function(xhr, status, error) {
                console.error("AJAX Error: " + error);
            }
        });
    }
}

$(document).ready(function() {
    // Disable manual typing in eWay Bill Date
    $("#eway_bill_date").on("keydown paste", function(e) {
        e.preventDefault();
    });

    // Function to update eWay date restrictions
    function updateEwayDateRestriction() {
        let poDate = $("#po_date").val();

        if (poDate) {
            // Restrict minimum selectable date
            $("#eway_bill_date").attr("min", poDate);

            // If already selected date is less than PO date, clear it
            let ewayDate = $("#eway_bill_date").val();
            if (ewayDate && ewayDate < poDate) {
                $("#eway_bill_date").val("");
            }
        } else {
            // If no PO date, remove restrictions
            $("#eway_bill_date").removeAttr("min");
        }
    }

    // Trigger when PO Date changes manually
    $("#po_date").on("change", function() {
        updateEwayDateRestriction();
    });

    // Trigger when Purchase Order No changes (after PO date is fetched)
    $("#purchase_order_no").on("change", function() {
        // Delay to ensure PO Date is updated after AJAX fetch
        setTimeout(updateEwayDateRestriction, 300);
    });

    // Apply on page load if PO Date already exists
    updateEwayDateRestriction();
});


$("#test_file_qual").on("change", function () {
    let files = this.files;
    let maxSize = 5 * 1024 * 1024; // 5 MB

    for (let i = 0; i < files.length; i++) {
        if (files[i].size > maxSize) {
            Swal.fire({
                icon: 'error',
                title: 'File Too Large',
                text: 'Each file must be under 5 MB.',
                confirmButtonColor: '#3bafda'
            });

            // Reset the input (so user must re-select)
            $(this).val("");
            break;
        }
    }
});

function srn_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#srnUploadModal').modal('show');
    
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
        
        let allowedExtensions = [
            "pdf", "jpg", "jpeg", "png", "gif", "bmp", "webp", "svg",
            "doc", "docx", "txt", "xls", "xlsx", "csv"
        ];

        // Check file types before appending
        if (image_s && image_s.files.length > 0) {
            for (var i = 0; i < image_s.files.length; i++) {
                let file = image_s.files[i];
                let fileExt = file.name.split('.').pop().toLowerCase();
                let typeAllowed = allowedTypes.includes(file.type);
                let extAllowed = allowedExtensions.includes(fileExt);
                console.info(file.type);
                console.info(fileExt);
                if (!typeAllowed && !extAllowed) {
                    // alert(file.type);
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
                text: 'Invalid file format. Only images, PDF, Word, Excel, CSV, and text files are allowed.',
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