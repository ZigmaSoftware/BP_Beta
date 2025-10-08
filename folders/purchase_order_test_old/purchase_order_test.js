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
    let tax_percent = parseFloat($("#tax option:selected").data('extra')) || 0;

    let amount = qty * rate;
    amount -= (discount / 100) * amount;
    amount += (tax_percent / 100) * amount;

    $("#amount").val(amount.toFixed(2));
}
function total_amount_calculation() {
    let total_amount_value = 0;

    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let amt = parseFloat($(this).find("td:eq(7)").text()) || 0;
        total_amount_value += amt;
    });

    let freight_percentage = parseFloat($("#freight_percentage").val()) || 0;
    let other_charges = parseFloat($("#other_charges").val()) || 0;
    let other_tax = parseFloat($("#other_tax option:selected").data('extra')) || 0;
    let tcs_percentage = parseFloat($("#tcs_percentage").val()) || 0;
    let round_off = parseFloat($("#round_off").val()) || 0;

    let freight_amount = (freight_percentage / 100) * total_amount_value;
    let other_tax_amount = (other_charges * other_tax) / 100;
    let other_charges_percentage = other_charges + other_tax_amount;
    let gross_amount = total_amount_value + freight_amount + other_charges_percentage;
    let tcs_amount = (gross_amount * tcs_percentage) / 100;
    gross_amount += tcs_amount + round_off;

    $("#freight_amount").val(freight_amount.toFixed(2));
    $("#other_charges_percentage").val(other_charges_percentage.toFixed(2));
    $("#tcs_amount").val(tcs_amount.toFixed(2));
    $("#net_amount").val(total_amount_value.toFixed(2));
    $("#gross_amount").val(gross_amount.toFixed(2));
    $("#total_sub_amount").val(total_amount_value.toFixed(2));
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

    if (!form_validity_check("was-validated")) {
        sweetalert("form_alert");
        return false;
    }

    let rowCount = $("#purchase_order_sub_datatable tbody tr").length;
    if (rowCount === 0 || $("#purchase_order_sub_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }

    // Get FormData from purchase_order_form
    let data = new FormData($("#purchase_order_form")[0]);

    // Append data from form_second
    $("#form_second").serializeArray().forEach(({ name, value }) => {
        data.append(name, value);
    });

    // Add required action values
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

	var filter_data = {
		"company_name"      : company_name,
		"project_name"      : project_name,
		"from_date"         : from_date,
		"to_date"           : to_date
	};


	init_datatable(table_id, form_name, action, filter_data);


}

// Sublist Add/Update using only screen_unique_id
function po_sublist_add_update() {
    let screen_unique_id = $("#screen_unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();

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
            tax,
            amount
        },
        success: function (res) {
            let obj = JSON.parse(res);
            if (obj.status) {
                Swal.fire(obj.msg === "update" ? "Item updated" : "Item added");
                reset_sublist_form();
                purchase_order_sublist_datatable("purchase_order_sub_datatable");
                setTimeout(calculate_sublist_totals, 300);
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
    $("#item_code, #quantity, #uom, #rate, #discount, #amount").val("");
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
            $("#quantity").val(d.quantity);
            $("#uom").val(d.uom);
            $("#rate").val(d.rate);
            $("#discount").val(d.discount);
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

function sub_total_amount(tax_value="") {
    var qty_value = parseFloat($("#quantity").val()) || 0;
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

function calculate_sublist_totals() {
    let total_qty = 0;
    let total_amt = 0;

    $("#purchase_order_sub_datatable tbody tr").each(function () {
        let qty = parseFloat($(this).find("td:eq(3)").text()) || 0;
        let amt = parseFloat($(this).find("td:eq(7)").text()) || 0;

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