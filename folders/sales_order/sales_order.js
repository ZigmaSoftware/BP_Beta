

$(window).on("load", function () {
    setTimeout(function () {
        $('.addRow').first().trigger("click");
    }, 1); // Slight delay ensures DOM is ready
});


var company_name = sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone = sessionStorage.getItem("company_name");
var company_email = sessionStorage.getItem("company_name");
var company_logo = sessionStorage.getItem("company_name");

var form_name = 'sales_order';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'sales_order_datatable';
var action = "datatable";

var counter = $("#sub_counter").val();
$(document).ready(function () {
init_datatable(table_id,form_name,action);
});

function init_datatable(table_id='',form_name='',action='') {
    var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");
// alert(sessionStorage.getItem("folder_crud_link"));
	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
	
//         "searching" : true,
// 	"columnDefs": [
        
//             { className:  "text-center", "width" : "5%","targets": [ 0,-1 ] },
//         ],
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}


// var sub_datatable = $("#sales_order_sub_datatable").DataTable({
// 	"searching": false,
// 	"paging": false,
// 	// "ordering": false,
// 	"info": false,

// 	"columnDefs": [{
// 			"width": "5%",
// 			"targets": [0, -1]
// 		},
// 		{
// 			"width": "25%",
// 			"targets": [1]
// 		},
// 		{
// 			"width": "9%",
// 			"targets": [2, 3, 4, 5, 6]
// 		},
// 		{
// 			"width": "10%",
// 			"targets": [7]
// 		},


// 	]

// });


// // Sub Table UI Design
// // Global counter should be declared somewhere outside, e.g.:
// var counter = sub_datatable.rows().count() || 0; // initialize counter from existing rows

// // Add Row Button Click Handler
// $(document).on('click', '.addRow', function () {
//     var sub_table_data = $(".sublist-form").serialize();

//     var is_form = form_validity_check("sublist-form");

//     if ((is_form) || (sub_table_data == "")) {

//         var url = sessionStorage.getItem("list_link");
//         var ajax_url = sessionStorage.getItem("folder_crud_link");

//         // Hide all addRow buttons first
//         $('#sales_order_sub_datatable tbody tr').find('.addRow').addClass('d-none');

//         // Show all delete buttons
//         $(".delete_btn").removeClass("d-none");

//         var sublist_data = $("#sublist_data").val();

//         if (sublist_data) {
//             sublist_data = JSON.parse(sublist_data);
//             var sublist_length = sublist_data.length;

//             sublist_data.forEach(insert_arr);

//             function insert_arr(values) {
//                 var sub_unique_id = values['unique_id'];
//                 var item_name_id = values['item_name'];
//                 var unit_value = values['unit_name'];
//                 var qty_value = values['quantity'];
//                 var rate_value = values['rate'];
//                 var discount_value = values['discount'];
//                 var tax_value = values['tax'];
//                 var amount_value = values['amount'];

//                 sub_datatable.row.add([
//                     '',
//                     `<input type='hidden' class='form-control' name='sub_unique_id[]' data-count='${counter}' id='sub_unique_id${counter}' value='${sub_unique_id}' required>` +
//                     `<select class='ajax-select form-control' name='item_name[]' data-count='${counter}' tabindex='${counter}' onchange='get_unit_name(this);' id='item_name${counter}' required><option value='${item_name_id}' selected>${item_name_id}</option></select>`,
//                     `<input type='text' class='form-control unit_name' data-count='${counter}' tabindex='${counter}' readonly name='unit_name[]' id='unit_name${counter}' value='${unit_value}' required>`,
//                     `<input type='text' class='form-control qty' data-count='${counter}' tabindex='${counter}' onkeyup='sub_total_amount(this);' onkeypress='number_only(event),total_sub_quantity();' name='qty[]' id='qty${counter}' value='${qty_value}' required>`,
//                     `<input type='text' class='form-control rate' data-count='${counter}' tabindex='${counter}' onkeyup='sub_total_amount(this);' onkeypress='number_only(event);' name='rate[]' id='rate${counter}' value='${rate_value}' required>`,
//                     `<input type='text' class='form-control discount' data-count='${counter}' tabindex='${counter}' onkeyup='sub_total_amount(this);' onkeypress='number_only(event);' name='discount[]' id='discount${counter}' value='${discount_value}' required>`,
//                     `<select class='ajax-tax-select form-control' name='tax[]' data-count='${counter}' tabindex='${counter}' onchange='sub_total_amount(this);' id='tax${counter}' required><option value='${tax_value}' selected>${tax_value}</option></select>` +
//                     `<input type='hidden' class='form-control tax_value' data-count='${counter}' tabindex='${counter}' name='tax_value[]' id='tax_value${counter}' required>`,
//                     `<input type='text' class='form-control amount' data-count='${counter}' tabindex='${counter}' readonly name='amount[]' id='amount${counter}' value='${amount_value}' required>`,
//                     `<button type='button' class='btn btn-success addRow' tabindex='${counter}'>Add</button>` +
//                     `<button type='button' class='btn btn-danger delete_btn' tabindex='${counter}'>Delete</button>`
//                 ]).draw();

//                 counter++;
//             }

//             $("#sublist_data").val("");
//         } else {
//             // Add empty new row if no sublist_data
//             sub_datatable.row.add([
//                 '',
//                 `<input type='hidden' class='form-control' name='sub_unique_id[]' data-count='${counter}' id='sub_unique_id${counter}' required>` +
//                 `<select class='ajax-select form-control' name='item_name[]' data-count='${counter}' tabindex='${counter}' onchange='get_unit_name(this);' id='item_name${counter}' required></select>`,
//                 `<input type='text' class='form-control unit_name' data-count='${counter}' tabindex='${counter}' readonly name='unit_name[]' id='unit_name${counter}' required>`,
//                 `<input type='text' class='form-control qty' data-count='${counter}' tabindex='${counter}' onkeyup='sub_total_amount(this),total_sub_quantity();' onkeypress='number_only(event);' name='qty[]' id='qty${counter}' required>`,
//                 `<input type='text' class='form-control rate' data-count='${counter}' tabindex='${counter}' onkeyup='sub_total_amount(this);' onkeypress='number_only(event);' name='rate[]' id='rate${counter}' required>`,
//                 `<input type='text' class='form-control discount' data-count='${counter}' tabindex='${counter}' onkeyup='sub_total_amount(this);' onkeypress='number_only(event);' name='discount[]' id='discount${counter}' required>`,
//                 `<select class='ajax-tax-select form-control' name='tax[]' data-count='${counter}' tabindex='${counter}' onchange='sub_total_amount(this);' id='tax${counter}' required></select>` +
//                 `<input type='hidden' class='form-control tax_value' data-count='${counter}' tabindex='${counter}' name='tax_value[]' id='tax_value${counter}' required>`,
//                 `<input type='text' class='form-control amount' data-count='${counter}' tabindex='${counter}' readonly name='amount[]' id='amount${counter}' required>`,
//                 `<button type='button' class='btn btn-success addRow' tabindex='${counter}'>Add</button>` +
//                 `<button type='button' class='btn btn-danger delete_btn' tabindex='${counter}'>Delete</button>`
//             ]).draw();

//             counter++;
//         }

//         // Initialize select2 ajax selects
//         $(".ajax-select").select2({
//             debug: true,
//             theme: 'bootstrap4',
//             placeholder: 'Search...',
//             ajax: {
//                 url: ajax_url,
//                 type: "post",
//                 dataType: 'json',
//                 delay: 250,
//                 data: function (params) {
//                     return {
//                         searchTerm: params,
//                         type: "",
//                         action: "item_name_ajax"
//                     };
//                 },
//                 processResults: function (response) {
//                     return { results: response };
//                 },
//                 cache: true
//             }
//         });

//         $(".ajax-tax-select").select2({
//             debug: true,
//             theme: 'bootstrap4',
//             placeholder: 'Search...',
//             ajax: {
//                 url: ajax_url,
//                 type: "post",
//                 dataType: 'json',
//                 delay: 250,
//                 data: function (params) {
//                     return {
//                         searchTerm: params,
//                         type: "",
//                         action: "tax_ajax"
//                     };
//                 },
//                 processResults: function (response) {
//                     return { results: response };
//                 },
//                 cache: true
//             }
//         });

//         // Auto order number update on reordering/search
//         sub_datatable.on('order.dt search.dt', function () {
//             sub_datatable.column(0, { search: 'applied', order: 'applied' })
//                 .nodes()
//                 .each(function (cell, i) {
//                     cell.innerHTML = i + 1;
//                 });
//         }).draw();

//         // Show/hide add and delete buttons after row added
//         var table_count = sub_datatable.rows().count();

//         // Hide all addRow buttons first
//         $('#sales_order_sub_datatable tbody tr').find('.addRow').addClass('d-none');

//         // Show addRow only on last row
//         $('#sales_order_sub_datatable tbody tr:last').find('.addRow')
//             .removeClass('d-none')
//             .addClass('btn btn-success');

//         // Show/hide delete button based on row count
//         if (table_count === 1) {
//             $('#sales_order_sub_datatable tbody tr:last').find('.delete_btn').addClass('d-none');
//         } else {
//             $('.delete_btn').removeClass('d-none');
//         }

//     } else {
//         sweetalert("form_alert");
//     }
// });


// // Delete Row Button Handler
// $('#sales_order_sub_datatable tbody').on('click', '.delete_btn', function () {
//     const table = sub_datatable;
//     const table_count = table.rows().count();

//     // Prevent deletion if only 1 row remains
//     if (table_count === 1) {
//         sweetalert("custom", "", "", "Minimum one Entry Needed");
//         return false;
//     }

//     // Remove the selected row and redraw table
//     table
//         .row($(this).parents('tr'))
//         .remove()
//         .draw();

//     const new_count = table.rows().count();

//     // Hide all addRow buttons first
//     $('#sales_order_sub_datatable tbody tr').find('.addRow').addClass('d-none');

//     // Show addRow button only on the last row
//     $('#sales_order_sub_datatable tbody tr:last').find('.addRow')
//         .removeClass('d-none')
//         .addClass('btn btn-success');

//     // If only one row left, hide its delete button; else show all delete buttons
//     if (new_count === 1) {
//         $('#sales_order_sub_datatable tbody tr:last').find('.delete_btn').addClass('d-none');
//     } else {
//         $('.delete_btn').removeClass('d-none');
//     }
// });

// function number_check(no = 0) {

// 	if ((isNaN(no)) || (no == undefined) || (no == "")) {

// 		return 0;
// 	}

// 	return no;


// }

// function sub_total_amount(e) {

// 	// console.log(e);

// 	var count = $(e).data('count');

// 	var qty_value = number_check($("#qty" + count).val());
// 	var rate_value = number_check($("#rate" + count).val());
// 	var discount_value = number_check($("#discount" + count).val());
// 	var tax_test = $("#tax" + count).val();
// 	if (tax_test == null) {

// 		tax_value = 0;
// 	} else {

// 		var tax_value = number_check($("#tax" + count).select2('data')[0]['tax_value']);
// 		$("#tax_value" + count).val(tax_value);
// 	}


// 	var total_amount = (qty_value * rate_value);
// 	var discount = (discount_value / 100);
// 	var tax = (tax_value / 100);

// 	if (discount) {

// 		discount_amount = total_amount * discount;
// 		total_amount = total_amount - discount_amount;
// 	}

// 	if (tax_value) {

// 		tax_amount = total_amount * tax;
// 		total_amount = total_amount + tax_amount;
// 	}

// 	$("#amount" + count).val(indianMoneyFormat(total_amount));

// 	total_amount_calculation();

// }

// function total_amount_calculation() {

	

// 	var total_amount_value = 0;

// 	$(".qty").each(function () {

// 		var count = $(this).data('count');


// 		var qty_value 		= number_check($("#qty" + count).val());
// 		var rate_value 		= number_check($("#rate" + count).val());
// 		var discount_value 	= number_check($("#discount" + count).val());
// 		var tax_value  		= number_check($("#tax_value" + count).val());

// 		var total_amount 	= (qty_value * rate_value);
// 		var discount 		= (discount_value / 100);
// 		var tax 			= (tax_value / 100);

// 		if (discount) {

// 			discount_amount = total_amount * discount;
// 			total_amount 	= total_amount - discount_amount;
// 		}

// 		if (tax_value) {

// 			tax_amount 		= total_amount * tax;
// 			total_amount 	= total_amount + tax_amount;
// 		}

// 		total_amount_value += total_amount;
// 	});

// 	//final total calculation

// 	var freight_percentage 	= number_check($("#freight_percentage").val());
// 	var other_charges 		= number_check($("#other_charges").val());
// 	var other_tax 			= $("#other_tax option:selected").data('extra');
// 	var tcs_percentage 		= number_check($("#tcs_percentage").val());
// 	var round_off 			= number_check($("#round_off").val());

// 	console.log(round_off);

// 	var freight 			= (freight_percentage / 100);
// 	var freight_amount 		= 0.00;
// 	var other_charges_percentage = 0.00;
// 	var net_amount			= total_amount_value;
// 	var gross_amount		= total_amount_value;


// 	//freight charges calculation based on user input
// 	if(freight){

// 		freight_amount 	   = total_amount_value * freight;
// 		gross_amount	  += freight_amount;
// 	}

// 	//other charges calculation based on user input
// 	if(other_charges){

// 		other_charges_percentage = other_charges;

// 	}


// 	//other tax changes calculation based on user input
// 	 other_tax 		= (other_tax / 100);



// 	if(other_tax){

// 		other_tax_amount 				= other_charges_percentage * other_tax;
// 		other_charges_percentage	    = Number(other_charges_percentage)+Number(other_tax_amount);
// 		gross_amount	  			   += other_charges_percentage;
		
// 	}


// 	//tcs charges calculation based on user input

// 	var tcs 			= (tcs_percentage / 100);
// 	var tcs_amount 		= 0.00;

// 	if(tcs){

// 		tcs_amount 	   = gross_amount * tcs;
// 		gross_amount  += tcs_amount;
// 	}

// 	//round off calculation based on user input
// 			// round_off_b = Math.round(gross_amount);
// 			// round_off = round_off_b - gross_amount;
// 			// console.log("round off = "+round_off);
// 			// gross_amount = round_off_b; 
// 	if(round_off){
// 		gross_amount = Number(gross_amount)+Number(round_off);
// 	}			

// 	$("#freight_amount").val(indianMoneyFormat(freight_amount));
// 	$("#tcs_amount").val(indianMoneyFormat(tcs_amount));
// 	$("#other_charges_percentage").val(indianMoneyFormat(other_charges_percentage));
// 	// $("#round_off").val(indianMoneyFormat(round_off));


// 	$("#total_sub_amount").val(indianMoneyFormat(total_amount_value));
// 	$("#net_amount").val(indianMoneyFormat(total_amount_value));
// 	$("#gross_amount").val(indianMoneyFormat(gross_amount));


// }

// function total_sub_quantity() {
// 	// alert();
// 	var total_qty = 0;

// 	$(".qty").each(function () {
// 		if (!isNaN($(this).val())) {

// 			total_qty += Number($(this).val());
// 		}
// 	});

// 	$("#total_quantity").val(total_qty);
// }


// $('#purchase_order_sub_datatable tbody').on('click', '.delete_btn', function () {
// 	var table_count = sub_datatable.rows().count();
// 	if (table_count == 1) {
// 		alert("Mimimun one Entry Needed");
// 		return false;
// 	}
// 	sub_datatable
// 		.row($(this).parents('tr'))
// 		.remove()
// 		.draw();
// });


// // Get unit name  
// function get_unit_name(e) {

// 	var count = $(e).data('count');
// 	var unit_name   = $("#item_name"+count).select2('data')[0]['unit_name'];
// 	$("#unit_name" + count).val(unit_name);


	
// }


// // Get Billing Address  
// function get_billing_address(billing_val = "") {


// 	var ajax_url = sessionStorage.getItem("folder_crud_link");

// 	if (billing_val) {
// 		var data = {
// 			"billing_val": billing_val,
// 			"action": "billing_address"
// 		}

// 		$.ajax({
// 			type: "POST",
// 			url: ajax_url,
// 			data: data,
// 			success: function (data) {

// 				if (data) {
// 					$("#billing_information").html(data);
// 					$("#billing_info").html(data);
// 				}

// 			}
// 		});
// 	}
// }



// Create and Update Values
function sales_order_cu(unique_id = "") {
	
	var internet_status  = is_online();
	
    if (!internet_status) {
		sweetalert("no_internet");
        return false;
    }
	
    var is_form = form_validity_check("was-validated");
	
    if (is_form) {
		
        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
		
        console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".createupdate_btn").attr("disabled","disabled");
				$(".createupdate_btn").text("Loading...");
			},
			success		: function(data) {

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
					url 	= '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
				} else {
					if (msg=="already") {
						// Button Change Attribute
						url 		= '';

						$(".createupdate_btn").removeAttr("disabled","disabled");
						if (unique_id) {
							$(".createupdate_btn").text("Update");
						} else {
							$(".createupdate_btn").text("Save");
						}
					}
				}
				sweetalert(msg,url);
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});

    } else {
        sweetalert("form_alert");
    }
}

// Sub list
function product_creation_add_update(unique_id = "") { // au = add,update
    
    var internet_status  = is_online();
    
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    // console.log(is_form);
    var prod_unique_id               = $('#unique_id').val();
    if (is_form) {
        product_creation_cu(prod_unique_id);
        var data = $("#product_details_form").find("input, select, textarea").serialize();
        data += "&unique_id=" + unique_id + "&action=product_add_update&prod_unique_id=" + prod_unique_id;

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
// 			beforeSend 	: function() {
// 				$(".product_creation_add_update_btn").attr("disabled","disabled");
// 				$(".product_creation_add_update_btn").text("Loading...");
// 			},
			success		: function(data) {

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".product_creation_add_update_btn").text("Error");
                    console.log(error);
				} else {
					if (msg !=="already") {
						form_reset("product_sub_datatable");
                    }
                    
                    if (unique_id && msg =="already"){
                        $(".product_creation_add_update_btn").text("Update");
                    } else {
                        $(".product_creation_add_update_btn").text("Add");
                    }
                    // Init Datatable
                    prod_sub_list_datatable("product_sub_datatable");
                    $("#sub_group_unique_id").attr("disabled","disabled");
                    $("#product_name").attr("disabled","disabled");
                    $("#description").attr("disabled","disabled");
                    $("#is_active").attr("disabled","disabled");
				}
				sweetalert(msg,url);
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("form_alert");
    }
}

function prod_sub_list_datatable (table_id = "", form_name = "", action = "") {
     //alert("test");
    var prod_unique_id  = $('#unique_id').val();
    
    var table = $("#"+table_id);
    var data      = {
        "prod_unique_id"    : prod_unique_id,
        "action"                : table_id, 
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
        "searching": false,
        "paging":   false,
        "ordering": false,
        "info":     false,
        "ajax"      : {
            url     : ajax_url,
            type    : "POST",
            data    : data
        }
    });
}
