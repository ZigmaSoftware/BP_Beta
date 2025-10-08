
// $(document).ready(function () {
//     let main_id = $("#unique_id").val();
//     if (main_id) {
//         so_sublist_datatable("so_sublist_datatable");
//     }
//     // toggleSalesOrderField();
// });
// $(document).ready(function () {
//     // Initialize Select2 if used
//     $('.select2').select2();

//     // Trigger on page load
//     toggleSalesOrderField();

//     // Bind change event
//     $('#requisition_for').on('change', toggleSalesOrderField);
// });
$(document).ready(function () {
    sales_order_filter();
});


var form_name   = 'sales_order';
var table_id    = 'sales_order_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

function sales_order_2_cu(unique_id = "") {
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
    let rowCount = $("#so_sublist_datatable tbody tr").length;
    if (rowCount === 0 || $("#so_sublist_datatable tbody").text().includes("No data available")) {
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


function init_datatable(table_id='',form_name='',action='', filter_data ='') {
var from_date       = $("#from_date").val();
var to_date         = $("#to_date").val();
var company_name    = $("#company_name").val();
var customer_name   = $("#customer_name").val();
var status          = $("#status_fill").val();

    
	var table = $("#"+table_id);
	var data 	  = {
		"action"	    : action, 
		"from_date"	    : from_date, 
		"to_date"	    : to_date, 
		"company_name"	: company_name, 
		"customer_name"	: customer_name, 
		"status"	    : status
	};
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


// filter 
function sales_order_filter() {
    init_datatable(table_id, form_name, action);
}


// Delete Function
function sales_order_2_delete(unique_id = "") {
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



function sale_order_sublist_add_update() {
    let main_unique_id = $("#unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();
    
    if (!main_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }

    var product_unique_id       = $('#product_unique_id').val();
    var uom                     = $('#uom').val();
    var qty                     = $('#qty').val();
    var rate                    = $('#rate').val();
    var discount                = $('#discount').val();
    var tax                     = $('#tax').val();
    var amount                  = $('#amount').val();

    if (!product_unique_id || !qty || !uom || !rate || !discount || !tax || !amount) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }

    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "so_sub_add_update",
            main_unique_id: main_unique_id,
            sublist_unique_id: sublist_id,
            product_unique_id,
            qty,
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
                so_sublist_datatable("so_sublist_datatable");
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
    $("#qty, #rate, #discount, #amount").val("");
    resetDropdowns(["#product_unique_id", "#uom", "#tax"]);
}




function so_sublist_datatable(table_id = "so_sublist_datatable") {
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
                action: "so_sublist_datatable",
                main_unique_id: main_unique_id
            }
        }
    });
}


function so_sub_edit(unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "so_sub_edit", unique_id },
        success: function (res) {
            let d = JSON.parse(res).data;
            
            "item_name_id",
            "unit_name",
            "quantity",
            "rate",
            "discount",
            "tax_id",
            "amount",
            "unique_id"
            $("#qty, #rate, #discount, #amount").val("");
            $("#product_unique_id").val(null).trigger("change");
            $("#uom").val(null).trigger("change");
            $("#tax").val(null).trigger("change");
            
            $("#sublist_unique_id").val(d.unique_id);
            $("#product_unique_id").val(d.item_name_id).trigger("change");
            $("#uom").val(d.unit_name).trigger("change");
            $("#qty").val(d.quantity);
            $("#rate").val(d.rate);
            $("#discount").val(d.discount);
            $("#amount").val(d.amount);
            $("#tax").val(d.tax_id).trigger("change");
            $(".sale_order_add_update_btn").text("Edit");
    
        }
    });
}


function so_sub_delete(unique_id) {
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
                    action: "so_sub_delete",
                    unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    Swal.fire(obj.msg);
                    so_sublist_datatable("so_sublist_datatable");
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
        fromDateInput.value = `${year}-${month}-${day}`;
    }

    // Ensure To Date is not before From Date
    if (toDate < fromDate) {
        // Set To Date equal to From Date
        toDateInput.value = fromDateInput.value;
    }
}
