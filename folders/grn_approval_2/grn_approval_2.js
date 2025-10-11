$(document).ready(function () {
    grnApprovalFilter2();
    const screenId = $("#screen_unique_id").val();
    if (screenId) {
        grn_sublist_datatable("grn_sublist_datatable");
    }
    $(document).on("input", "#now_received_qty", function () {
        recalculateAmount();
    });
    $(document).on("input", "#paf, #freight, #other_charges, #round_off", function () {
        recalculateTotalAmount();
    });
    $(document).on("change", "#gst_paf, #gst_freight, #gst_other", function () {
        recalculateTotalAmount();
    });

    if ($("#is_update_mode").val() === "true") {
        recalculateTotalAmount();
    }
    toggleGstField();
    toggleRemark();
});
var form_name   = 'grn';
var table_id    = 'grn_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

function grn_approval_2_cu(unique_id = "") {
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

    let approve_status = $("#grn_approval").val();
    let status_remark = $("#status_remark").val();
    let sess_user_id = $("#sess_user_id").val();
    let data = new FormData();
    data.append("action", "createupdate");
    data.append("unique_id", unique_id);    
    data.append("approve_status", approve_status);    
    data.append("status_remark", status_remark);    
    data.append("sess_user_id", sess_user_id);    
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
                window.location.href = url;
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

function recalculateAmount() {
    let qty = parseFloat($("#now_received_qty").val()) || 0;
    let rate = parseFloat($("#rate").val()) || 0;
    let discount = parseFloat($("#discount").val()) || 0;
    let tax = parseFloat($("#tax_val").val()) || 0;
    let base = qty * rate;
    let discountAmt = (base * discount) / 100;
    let afterDiscount = base - discountAmt;
    let taxAmt = (afterDiscount * tax) / 100;
    let finalAmount = afterDiscount + taxAmt;

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
    let total = basic + paf + freight + other + total_taxed + round;

    $("#tot_gst").val(total_taxed.toFixed(2));
    $("#final_total").text(total.toFixed(2));
}

function get_purchase_order_no(project_id = "") {
    // alert();
    if (project_id) {
        var data = {
            "project_id": project_id,
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

function init_datatable(table_id = '', form_name = '', action = '', filter_data = {}) {
	var from_date     = $("#from_date").val();
	var to_date       = $("#to_date").val();
	var company_name  = $("#company_name").val();
	var customer_name = $("#customer_name").val();
	let from          = $("#bids_ho_from").val();
	let to            = $("#bids_ho_to").val();
	let status        = $("#grn_approve_status").val();

	var table = $("#" + table_id);
	var data = {
		"action": action,
		"from": from,
		"to": to,
		"status": status,
		"company_name": company_name,
		"customer_name": customer_name
	};

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	// ✅ Destroy existing instance
	if ($.fn.DataTable.isDataTable(table)) {
		table.DataTable().clear().destroy();
	}

	table.DataTable({
		ordering: true,
		searching: true,
		pageLength: 10,
		displayStart: 0, // ✅ Always start from first page
		ajax: {
			url: ajax_url,
			type: "POST",
			data: data
		}
	});
}

function grnApprovalFilter2() {
	init_datatable(table_id, form_name, action);

	// ✅ Reset to page 1 after filtering
	setTimeout(() => {
		$('#' + table_id).DataTable().page(0).draw(false);
	}, 300);
}


function grn_approval_2_delete(unique_id = "") {
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

function grn_sublist_datatable(table_id = "grn_sublist_datatable") {

    let screen_unique_id = $("#screen_unique_id").val();
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    let is_update = $("#is_update_mode").val();
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
            action: "grn_sublist_datatable",
            screen_unique_id: screen_unique_id,
            is_update: is_update
        },
        dataSrc: function(json) {

                if (json.total !== undefined) {
                    $("#basic").val(json.total.toFixed(2));
                } else {
                    $("#basic").val("0.00");
                }
                
                if (json.taxed !== undefined) {
                    $("#tot_gst").val(json.taxed.toFixed(2));
                    $("#tot_gst1").val(json.taxed.toFixed(2));
                } else {
                    $("#tot_gst").val("0.00");
                    $("#tot_gst1").val("0.00");
                }

                recalculateTotalAmount();
                return json.data;
            }        
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
  $('input[name="apply_gst"]:checked').trigger('change');
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
    if (discount_value) {
        var discount = discount_value / 100;
        var discount_amount = total_amount * discount;
        total_amount -= discount_amount;
    }
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
    if (fromDate > currentDate) {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        fromDateInput.value = `${month}-${day}`;
    }
    if (toDate < fromDate) {

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
    if (!$("#screen_unique_id").val()) {
        const generated_id = "scr" + Math.random().toString(36).substring(2, 18);
        $("#screen_unique_id").val(generated_id);
    }

    let screen_unique_id = $("#screen_unique_id").val();
    $.post(ajax_url, {
        action: "clear_grn_sublist",
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

function fetch_po_items_logic(po_unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.post(ajax_url, {
        action: "get_po_items_for_grn",
        unique_id: po_unique_id
    }).done(function (res) {
        try {
            let obj = JSON.parse(res);

            if (obj.status && obj.data.length > 0) {
                $("#supplier_id").val(obj.supplier_id || "");
                $("#supplier_name").val(obj.supplier_name || "");
                obj.data.forEach((row) => {
                    add_po_item_to_grn(row, po_unique_id);
                });

                grn_sublist_datatable("grn_sublist_datatable");
                Swal.fire("PO Items Loaded", "", "success");
            } else {
                Swal.fire("No items found for this PO.");
            }
        } catch (e) {
            console.error("JSON Parse Error (items):", res);
            Swal.fire("Server error while loading items.");
        }
    });
}

function add_po_item_to_grn(row, po_unique_id) {
    let screen_unique_id = $("#screen_unique_id").val();
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "grn_sub_add_update",
            screen_unique_id: screen_unique_id,
            item_code: row.item_code,
            order_qty: row.lvl_2_quantity,
            uom: row.uom,
            now_received_qty: 0,
            po_unique_id: po_unique_id,
        },
        success: function (res) {

        }
    });
}

function toggleRemark() {
    const approval = document.getElementById("grn_approval").value;
    const remarkDiv = document.getElementById("status_remark_div");

    if (approval === "1" || approval === "2") {
        remarkDiv.style.display = "flex";
    } else {
        remarkDiv.style.display = "none";
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

function grn_approval_2_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#grnUploadModal').modal('show');
    
    sub_list_datatable("documents_datatable");
}