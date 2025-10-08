$(document).ready(function() {

    init_datatable(table_id, form_name, action);

	var tender_status = $("#tender_status").val();
	
	price_bid_tender_change_status(tender_status);


	
	// $('form').find('sup').hide();
	
	// $('document').ready(function() {
	// 	$('td:nth-child(7),th:nth-child(7)').hide();
	// });

});

$( window ).on( "load", function() {

	var unique_id 	= $("#unique_id").val();

	if (unique_id) {

		var is_updated 	= $("#price_bid_is_updated").val();

		if (is_updated) {

			var no_of_bidders 	= $("#no_of_bidders").val();

			price_bid_competitor_list(no_of_bidders);

			price_bid_price_bid_ui();
			
		}
	}
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address = sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'leads';
var form_header 	= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'price_bid_datatable';
var table_sub_id 	= 'leads_sub_datatable';
var action 			= "datatable";

var product_data_table = "";


function init_datatable(table_id='',form_name='',action='',filter_data = "") {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
		"searching" : true,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}                              

function price_bid_competitor_list (no_of_competitors = 0) {


	var print_status 	= $("#print_status").val();

	// console.log(print_status);

	// alert(print_status);

	// if (no_of_competitors > 25) {

	// 	sweetalert("custom","","","Maximum 25 bidders Allowed");

	// 	return false;

	// }

    var ajax_url 		= sessionStorage.getItem("folder_crud_link");
	
	var list_ui 		= "";
	var list_ui_total   = "";
	var i  				= 1;
	
	// Default User Set
	list_ui 			= `<div class="form-group row">`;

	// Bidder Lable UI
	list_ui 			+= `<label class="col-md-2 col-form-label" for="price_bid_bidder_name${i}">Name of Bidder ${i}</label>`;

	// Bidder Div UI
	list_ui 			+= '<div class="col-md-4">';

	// Bidder Select 
	list_ui 			+= `<select name="price_bid_bidder_name[]" id="price_bid_bidder_name${i}" class="select2 form-control bidders_name"  required>`;

	list_ui 			+= `<option value="default" selected>Ascent e-Digit Solutions</option>`;

	list_ui 			+= '</select>';

	list_ui     		+='</div>';

	list_ui     		+='</div>';

	// list_ui_total   	= list_ui;

	++i;

	var bidder_names 	= $("#price_bid_bidder_names").val();

	if (bidder_names) {

		bidder_names 		= JSON.parse(bidder_names);

		$("#price_bid_bidder_names").val("");

		// console.log(bidder_names);

	} else {
		bidder_names = [];
	}

	var j = 0;

	for (i; i <= bidder_names.length + 1; i++) {

		var accepted 	= " selected ";
		var ui_accepted = " Accepted ";
		var rejected 	= "";
		// var ui_rejected = "";
		var reason   	= "";
		var d_none 		= "";

		var competitor_status = bidder_names[j]['competitor_status'];

		if (competitor_status == "0") {
			accepted 	= "";
			rejected 	= " selected ";
			ui_accepted = " Rejected ";
			// ui_rejected = " Rejected ";
		}

		if (print_status == "1") {
			d_none 		= " d-none ";
		}

		// console.log(bidder_names);

		var list_ui  = `<div class="form-group row ${d_none}">
				<label class="col-md-2 col-form-label" for="price_bid_competitor_status${j}"> ${bidder_names[j]['competitor_name']} </label>
				<div class="col-md-4">
					<input type="hidden" name="price_bid_competitor_name[]" value="${bidder_names[j]['competitor_id']}">
					<select name="price_bid_competitor_status[]" id="price_bid_competitor_status${j}" class="select2 form-control price_bid_competitor_status" required>
						<option value="">Select Status</option>
						<option value="1" data-id = "${bidder_names[j]['competitor_id']}" data-competitor = "${bidder_names[j]['competitor_name']}" ${accepted}>Accepted</option>
						<option value="0" data-competitor = "${bidder_names[j]['competitor_name']}" ${rejected}>Rejected</option>
					</select>
				</div>
				<label class="col-md-2 col-form-label" for="competitor_reason${j}"> Reason </label>
				<div class="col-md-4">
					<textarea name="competitor_reason[]" id="competitor_reason${j}" plaseholder="Reason.." class="form-control" rows="1">${bidder_names[j]['reason']}</textarea>
				</div>
			</div>`;

		if (print_status == "1") {

			list_ui  += `<div class="form-group row">
							<label class="col-md-2 col-form-label  font-weight-light" for="price_bid_competitor_status${j}"> ${bidder_names[j]['competitor_name']} </label>
							<label class="col-md-4 col-form-label" for="price_bid_competitor_status${j}">${ui_accepted}</label>
							<label class="col-md-2 col-form-label  font-weight-light" for="competitor_reason${j}"> Reason </label>
							<label class="col-md-4 col-form-label" for="competitor_reason${j}"> ${bidder_names[j]['reason']} </label>
						</div>`;
		}

		list_ui_total     += list_ui;

		j++;
	}

	// Add Go Button 

	if (print_status == "0") {

	list_ui_total 		+= `
		<div class="form-group row ">
			<div class="col-md-12 text-center">
				<button type="button" class="btn btn-asgreen btn-rounded waves-effect waves-light" onclick="price_bid_price_bid_ui()">Go</button>                     
			</div>
		</div>`;
	}

	// let test = <h1>Test</h1>;

	// for (i; i <= no_of_competitors; i++) {

	// 	// Bidder Lable UI
	// 	list_ui 	= `<div class="form-group row">`;

	// 	list_ui 	+= `<label class="col-md-2 col-form-label" for="bidder_name${i}">Name of Bidder ${i}</label>`;

	// 	// Bidder Div UI
	// 	list_ui 	+= '<div class="col-md-4">';

	// 	// Bidder Select 
	// 	list_ui 	+= `<select name="bidder_name[]" id="bidder_name${i}" class="ajax-select1 form-control bidders_name" onchange = "price_bid_check_bidders(this);price_bid_price_bid_ui()" required>`;

	// 	// when Update Get Id's From hidden field
	// 	if (bidder_names.length) {

	// 		// alert(bidder_names[j]);
	// 		// console.log(bidder_names[j]);
	// 		list_ui 	+= `<option value="${bidder_names[j]['unique_id']}" selected>${bidder_names[j]['competitor_name']}</option>`;
	// 		j++;
	// 	}

	// 	list_ui 	+= '</select>';

	// 	list_ui     +='</div>';

	// 	list_ui     +='</div>';

	// 	list_ui_total     += list_ui;
	// }

	$(".price_bid_competitor_list").html(list_ui_total);

	if ( $.fn.DataTable.isDataTable( '#price_bid_product' ) ) {
		product_data_table.destroy();
	}

	$("#price_bid_product").html("");

	$(".select2").select2({
		theme		: 'bootstrap4'
	});
	
	return false;
}

function price_bid_check_bidders(e) {

	var current_id 		= e.id;
	var current_value 	= e.value;

	$(".bidders_name").each(function () {

		var this_id 	= this.id;
		var this_value 	= this.value;

		if ((this_id != current_id) && (current_value == this_value) && (this.value != "")) {

			sweetalert("custom","","","Bidder Already Exist");

			// Enpty the current bidder if already exist
			$("#"+current_id).val(null).trigger('change');
			
		}
	});
}

function price_bid_product_datatable_init(table_id = 1) {

	if (table_id) {

		product_data_table = $("#price_bid_product").DataTable({
			"destroy": true,
			"scrollX": !0,
			"scrollY": "350px",
			"scrollCollapse": !0,
			"searching": false,
			"paging": !1,
			"serverSide": false,
			"responsive": false,
			"columnDefs": [
				{ "width": "20%", "targets": "_all" },
				{ className: "text-left", "targets": [ 0, -1 ] }
			],
		});
	}	
}

function price_bid_price_bid_ui () {

    var ajax_url 	  = sessionStorage.getItem("folder_crud_link");

	var unique_id 	  = $("#unique_id").val();

	var bid_ui 		  = "";
	var bid_ui_total  = "";

	var data 		  = {
		"unique_id"	: unique_id,
		"action" 	: "bid_product"
	};

	var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("price_bid_form");

    if (is_form) {
		
		// Get Product Details 
		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			// beforeSend 	: function() {
			// 	$(".createupdate_btn").attr("disabled","disabled");
			// 	$(".createupdate_btn").text("Loading...");
			// },
			success		: function(data) {

				var obj     	= JSON.parse(data);
				var msg     	= obj.msg;
				var status  	= obj.status;
				var error   	= obj.error;
				var products	= obj.data;

				var bidders 	= [];

				$(".price_bid_competitor_status").each(function () {

					var this_value 	= this.value;
					// var this_name 	= this.options[this.selectedIndex].text;
					var this_name 	= this.options[this.selectedIndex].dataset.competitor;
					var this_id  	= this.options[this.selectedIndex].dataset.id;

					if (this_value  != "0") {
						bidders.push({
							"id"   : this_id,
							"name" : this_name
						});
					}
					
				});

				if (!status) {

					url 	= '';
                    // $(".createupdate_btn").text("Error");
					sweetalert(msg);
				} else {
					price_bid_product_ui(products,bidders);
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});

		// bid Submission UI Begins
	}
}

function price_bid_product_ui (product_details = [], bidder_details = []) {

	if ( $.fn.DataTable.isDataTable( '#price_bid_product' ) ) {
		product_data_table.destroy();
	}

	// Product Details Has Array {"id":unique_id,"value":value,"bidder":""}
	// Bidder Details Has Array {"id":unique_id,"value":value}

	var bid_price_bid_product_ui 			= "";
	var bid_price_bid_product_ui_total 	= "";

	var bidders_arr 			= [];

	// Table Head Create

	var total_value 			= 0.00;

	var table_footer 			= "";
	var table_bid_position		= "";

	bid_price_bid_product_ui				= `<thead>`;
	bid_price_bid_product_ui				+= `<tr>`;
	bid_price_bid_product_ui				+= `<th>Product Details</th>`;

	// Prepare table footer On Here
	// table_footer 				= `<tfoot>`;
	table_bid_position 			= `<tr>`;
	table_bid_position 			+=`<td><span class='font-weight-bold'>Bidder Position</span></td>`;


	table_footer 				= `<tr>`;
	table_footer 				+=`<td><span class='font-weight-bold'>Total</span></td>`;

	var bidd_total 				= 1;
	var bidder_option 			= `<option value=''>Select Bidder Position</option>`;

	bidder_details.forEach(function(bidder_value,bidder_key) {

		console.log(bidder_details);
		
		bid_price_bid_product_ui	+= `<th>${bidder_value['name']}</th>`;

		bidders_arr.push(bidder_value['id']);
		
		// table_bid_position 	+= `<td id="bidd${bidd_total}_position"></td>`;

		table_footer 	+= `<td id="bidd${bidd_total}_total">`;
		
		table_footer 	+= `</td>`;
		
		table_footer 	+= `<input type="hidden" name="bidders_total_${bidder_value['id']}" value="0" id="bidd${bidd_total}_total_inp" readonly>`;

		bidder_option 	+= `<option value='l${bidd_total}'>L${bidd_total}</option>`;

		bidd_total++;
	});



	// console.log(bidder_option);

	// table_bid_position 	+= `</tr>`;
	table_footer 	+= `</tr>`;
	// table_footer 	+= `</tfoot>`;

	bid_price_bid_product_ui	+= `</tr>`;
	bid_price_bid_product_ui	+= `</thead>`;

	// Table Body Create

	bid_price_bid_product_ui	+= `<tbody>`;

	// When Update

	var is_updated 	= $("#price_bid_is_updated").val();

	if (is_updated) {

		var up_product_details = $("#price_bid_product_details").val();

		if (up_product_details) {

			up_product_details 	= JSON.parse(up_product_details);

			// console.log(up_product_details);
		}

		$("#price_bid_product_details").val("");
	}

	var prod 	= 1;

	product_details.forEach(function(product_value,product_key) {

		bid_price_bid_product_ui	+= `<tr>`;
		bid_price_bid_product_ui	+= `<td>${product_value['name']}</td>`;

		var bidd 	= 1;


		var bid_position 	= 0;

		bidders_arr.forEach(function(bid_item_value,bid_item_key) {	
			
			// console.log(bidders_arr);

			bid_price_bid_product_ui	+= `<td>`;

			product_rate     = '';

			if ((is_updated) && (up_product_details)) {

				for (let ii = 0; ii < up_product_details.length; ii++) {

					if (up_product_details[ii]['competitor_id'] == bid_item_value && up_product_details[ii]['item_id'] == product_value['id']) {
						
						product_rate     = up_product_details[ii]['rate'];

						bid_price_bid_product_ui	+= `<label class="col-form-label">${up_product_details[ii]['competitor_product_name']}</label><br />`;
						
						break;
					}	
				}
			}

			var print_readonly = "";

			var print_status = $("#print_status").val();

			// console.log(print_status);
			// alert(print_status);

			if (print_status == "0") {

				bid_price_bid_product_ui	+= `<input type="text" class="form-control price_bid_products pro${prod}bid${bidd} bidd${bidd}" name = "product_rate[]" data-bidder="${bid_item_value}" onchange="price_bid_bidder_product_total_value('bidd${bidd}')" data-product = "${product_value['id']}" data-identy = "item_${bid_item_value}_${product_value['id']}" style="width: 220px;" value = "${product_rate}" placeholder = "Product Rate..." required>`;

			} else {
				print_readonly 	= " disabled ";				


				// This is Hidden Input Type So Don't Delete But Above one is text Type
				 bid_price_bid_product_ui	+= `<input type="hidden" class="form-control price_bid_products pro${prod}bid${bidd} bidd${bidd}" name = "product_rate[]" data-bidder="${bid_item_value}" onchange="price_bid_bidder_product_total_value('bidd${bidd}')" data-product = "${product_value['id']}" data-identy = "item_${bid_item_value}_${product_value['id']}" style="width: 220px;" value = "${product_rate}" placeholder = "Product Rate..." required>`;

				bid_price_bid_product_ui	+= `<label class="col-form-label">${product_rate}</label>`;
			}

			bid_price_bid_product_ui	+= `</td>`;

			if (prod == product_details.length) {

				table_bid_position 	+= `<td>`;
				table_bid_position 	+= `<select class="select2 form-control" id="competitor_position${bidd}" name="competitor_position_${bidders_arr[bid_position]}" style="width: 220px;" required ${print_readonly}>`;
				table_bid_position 	+= bidder_option;
				table_bid_position 	+= `</td>`;

				bid_position++;
			}

			bidd++;
		});

		bid_price_bid_product_ui	+= `</tr>`;

		prod++;

	});

	table_bid_position 	+= `</tr>`;
	
	bid_price_bid_product_ui	+= table_footer;
	bid_price_bid_product_ui	+= table_bid_position;
	bid_price_bid_product_ui	+= `</tbody>`;
	// bid_price_bid_product_ui	+= table_footer;

	$("#price_bid_product").html(bid_price_bid_product_ui);

	price_bid_product_datatable_init();

	var bid_total = 1;

	$(".select2").select2({
		theme		: 'bootstrap4'
	});

	var bidder_position 	= $("#price_bid_bidder_position").val();

	if (bidder_position) {

		bidder_position 		= JSON.parse(bidder_position);

		$("#price_bid_bidder_position").val("");

	}

	bidders_arr.forEach(function(bid_item_value,bid_item_key) {

		price_bid_bidder_product_total_value("bidd"+bid_total);
		
		if (bidder_position) {
			
			if (bidder_position[bid_item_value]) {

				$("#competitor_position"+bid_total).val(bidder_position[bid_item_value]).trigger("change");
				
			}			
		
		}		
		bid_total++;
	});
}

function price_bid_bidder_product_total_value (class_name = "") {

	if (class_name) {

		var total = 0.00;

		$("."+class_name).each(function () {
			total += parseFloat($(this).val());
		});

		var text_va 	= `<label class="col-form-label">${indianMoneyFormat(total)}</label>`;

		$("#"+class_name+"_total").html(text_va);
		$("#"+class_name+"_total_inp").val(total);

	}
}

function price_bid_get_competitor_select(no_of_bidders) {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var label_str = '';

    for (var i = 1; i <= no_of_bidders; i++) {
        label_str += `<select name="bidder_name[]" id="bidder_name${i}" class="ajax-select form-control"  required>
                    </select>`;
        label_str += "<BR>";

    }

    document.getElementById('bidder_name').innerHTML = label_str;


    $(".ajax-select").select2({
        debug: true,
        theme: 'bootstrap4',
        placeholder: 'Search...',
        ajax: {
            url: ajax_url,
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function(params) {
                return {
                    searchTerm: params,
                    type: "",
                    action: "bidder_name_ajax"
                };
            },
            processResults: function(response) {
                return {
                    results: response
                };
            },
            cache: true
        }
    });
}

function price_bid_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("price_bid_form");

    if (is_form) {

		var product_arr 	= [];

		$(".price_bid_products").each(function() {

			var competitor = $(this).data("bidder");
			var product    = $(this).data("product");
			var value      = $(this).val();

			product_arr.push({
				"bidder" : competitor,
				"product" : product,
				"value"   : value
			});
			
		});

		product_arr  = JSON.stringify(product_arr);

        var data 	 = $(".price_bid_form").serialize();

        data 		+= "&product_details="+product_arr;

        data 		+= "&unique_id="+unique_id+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

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

function price_bid_tender_change_status(this_value = "") {
	if (this_value == "0") {
		// alert(this_value);
		$(".tender_loss_ui").removeClass("d-none");
		$(".tender_loss_inp").attr("required","required");
	} else {
		$(".tender_loss_ui").addClass("d-none");
		$(".tender_loss_inp").removeAttr("required");
	}
}