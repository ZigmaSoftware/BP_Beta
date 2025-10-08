$(document).ready(function () {
    init_datatable("sales_order_datatable", "Sales Order", "datatable");
});
var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'company_name Type';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'currency_name_creation_datatable';
var action 			= "datatable";


function add_sales_order_item_row() {
    let html = `
    <tr>
        <td><input name="item_code[]" type="text" class="form-control" required></td>
        <td><input name="item_description[]" type="text" class="form-control"></td>
        <td><input name="quantity[]" type="number" class="form-control" required></td>
        <td><input name="rate[]" type="number" class="form-control" required></td>
        <td>
            <select name="discount_type[]" class="form-control">
                <option value="None">None</option>
                <option value="Percentage">%</option>
                <option value="Value">Value</option>
            </select>
        </td>
        <td><input name="discount_value[]" type="number" class="form-control"></td>
        <td><input name="item_rate[]" type="number" class="form-control" readonly></td>
        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">X</button></td>
    </tr>`;
    $('#order_items_body').append(html);
}

function removeRow(button) {
    $(button).closest('tr').remove();
}



function sales_order_cu(order_id = "") {
    if (!is_online()) return sweetalert("no_internet");

    if (form_validity_check("was-validated")) {
        let data = $(".was-validated").serialize() + "&action=createupdate&order_id=" + order_id;
        let ajax_url = sessionStorage.getItem("folder_crud_link");
        let url = sessionStorage.getItem("list_link");

        $.post(ajax_url, data, function (response) {
            const res = JSON.parse(response);
            sweetalert(res.msg, res.status ? url : '');
        });
    } else {
        sweetalert("form_alert");
    }
}

function init_datatable(table_id='',form_name='',action='') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
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

function currency_creation_delete(unique_id = "") {

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