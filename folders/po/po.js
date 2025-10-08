$(document).ready(function () {
	// var table_id 	= "units_datatable";
	init_datatable(table_id,form_name,action);
	sub_list_datatable("po_sub_datatable");
    
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'product_creation';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'po_datatable';
var action 			= "datatable";



function po_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }
    var screen_unique_id = $("#screen_unique_id").val();
    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&screen_unique_id="+screen_unique_id+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

        // console.log(data);
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
					if (msg=="group_alert") {
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

			  sweetalert(msg,url,"group_name");
			},
			error 		: function(data) {
				alert("Network Error");
			}
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

function group_delete(unique_id = "") {

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


function get_sub_group_name(){
    var group_id = $("#group_unique_id").val();
    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (group_id) {
		var data = {
			"group_id" 	: group_id,
			"action"	: "sub_group"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
					$("#sub_group_unique_id").html(data);
				}

			}
		});
    }
}

function get_sublist_group_name(){
    var group_id = $("#group_unique_id").val();
    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (group_id) {
		var data = {
			"group_id" 	: group_id,
			"action"	: "sublist_group"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
					$("#group_unique_id_sub").html(data);
				}

			}
		});
    }
}

function get_sublist_sub_group(callback) {
    var group_unique_id_sub = $("#group_unique_id_sub").val();
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    if (group_unique_id_sub) {
        var data = {
            "group_id": group_unique_id_sub,
            "action": "sub_group_sublist"
        }
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(data) {
                $("#sub_group_unique_id_sub").html(data);
                if (callback) callback(); // ✅ call callback after options loaded
            }
        });
    } else if (callback) {
        callback(); // if empty, still call callback to continue
    }
}

function get_sublist_category(callback) {
    var group = $("#group_unique_id_sub").val();
    var sub_group = $("#sub_group_unique_id_sub").val();
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    if (group && sub_group) {
        var data = {
            "group_id": group,
            "sub_group_id": sub_group,
            "action": "category"
        }
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(data) {
                $("#category_unique_id_sub").html(data);
                if (callback) callback();
            }
        });
    } else if (callback) {
        callback();
    }
}

function get_sublist_item(callback) {
    var group = $("#group_unique_id_sub").val();
    var sub_group = $("#sub_group_unique_id_sub").val();
    var category = $("#category_unique_id_sub").val();
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    if (group && sub_group && category) {
        var data = {
            "group_id": group,
            "sub_group_id": sub_group,
            "category": category,
            "action": "item_name"
        }
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(data) {
                $("#item_unique_id_sub").html(data);
                if (callback) callback();
            }
        });
    } else if (callback) {
        callback();
    }
}


function get_unit_name(){
    var item_id = $("#item_unique_id_sub").val();
    
    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (item_id) {
		var data = {
			"item_id" 	: item_id,
			"action"	: "unit_name"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
				    var split_data = data.split("@@");
					$("#unit").val(split_data[0]);
					$("#uom").val(split_data[1]);
				}

			}
		});
    }
}


function product_sub_add_update(unique_id = "") { // au = add,update

    var internet_status = is_online();
    var screen_unique_id = $("#screen_unique_id").val();
    var group_unique_id_sub = $("#group_unique_id_sub").val();
    var sub_group_unique_id_sub = $("#sub_group_unique_id_sub").val();
    var category_unique_id_sub = $("#category_unique_id_sub").val();
    var item_unique_id_sub = $("#item_unique_id_sub").val();
    var qty = $("#qty").val();
    var uom = $("#uom").val();
    var remarks = $("#remarks").val();
    var is_active_sub = $("#is_active_sub").val();
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }
    if ((screen_unique_id != '') && (group_unique_id_sub != '') && (sub_group_unique_id_sub != '')&& (category_unique_id_sub != '')&& (item_unique_id_sub != '')&& (qty != '')&& (uom != '')&& (remarks != '')) {
        var data = $(".was-validated").serialize();
        data += "&screen_unique_id=" + screen_unique_id+"&group_unique_id_sub=" + group_unique_id_sub+"&sub_group_unique_id_sub=" + sub_group_unique_id_sub+"&category_unique_id_sub=" + category_unique_id_sub+"&item_unique_id_sub=" + item_unique_id_sub+"&qty=" + qty+"&uom=" + uom+"&remarks=" + remarks;
        data += "&unique_id=" + unique_id + "&action=product_sub_add_update";
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url = "";
        // console.log(data);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function() {
                $(".product_sub_add_update_btn").attr("disabled", "disabled");
                $(".product_sub_add_update_btn").text("Loading...");
            },
            success: function(data) {
                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;
                if (!status) {
                    $(".product_sub_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {
                        // form_reset("was-validated");
                         $('#group_unique_id_sub').val(null).trigger('change');
                         $('#sub_group_unique_id_sub').val(null).trigger('change');
                         $('#category_unique_id_sub').val(null).trigger('change');
                         $('#item_unique_id_sub').val(null).trigger('change');
                        $("#qty").val("");
                        $("#uom").val("");
                        $("#unit").val("");
                        $("#remarks").val("");
                       
                    }
                    $(".product_sub_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".product_sub_add_update_btn").text("Update");
                    } else {
                        $(".product_sub_add_update_btn").text("Add");
                        $(".product_sub_add_update_btn").attr("onclick", "product_sub_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("po_sub_datatable");
                }
                sweetalert(msg, url);
            },
            error: function(data) {
                alert("Network Error");
            }
        });
    } else {
        sweetalert("custom", '', '', 'Create Sublist');
        if (group_unique_id_sub == '') {
            document.getElementById('group_unique_id_sub').focus();
        } else if (sub_group_unique_id_sub == '') {
            document.getElementById('sub_group_unique_id_sub').focus();
        } else if (category_unique_id_sub == '') {
            document.getElementById('category_unique_id_sub').focus();
        } else if (item_unique_id_sub == '') {
            document.getElementById('item_unique_id_sub').focus();
        } else if (qty == '') {
            document.getElementById('qty').focus();
        } else if (uom == '') {
            document.getElementById('uom').focus();
        } else if (remarks == '') {
            document.getElementById('remarks').focus();
        }
    }
}

function prod_sub_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=prod_sub_edit";
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function() {
                $(".product_sub_add_update_btn").attr("disabled", "disabled").text("Loading...");
            },
            success: function(data) {
                var obj = JSON.parse(data);
                var item = obj.data;
                if (obj.status) {

                    // 1️⃣ Set the group
                    $("#group_unique_id_sub").val(item.group_unique_id).trigger('change');

                    // 2️⃣ Load sub groups THEN set it
                    get_sublist_sub_group(function() {
                        $("#sub_group_unique_id_sub").val(item.sub_group_unique_id).trigger('change');

                        // 3️⃣ Load categories THEN set it
                        get_sublist_category(function() {
                            $("#category_unique_id_sub").val(item.category_unique_id).trigger('change');

                            // 4️⃣ Load items THEN set it
                            get_sublist_item(function() {
                                $("#item_unique_id_sub").val(item.item_unique_id).trigger('change');

                                // 5️⃣ Set the rest
                                $("#qty").val(item.qty);
                                $("#uom").val(item.uom);
                                $("#unit").val(item.bid_name);
                                $("#remarks").val(item.remarks);

                                // 6️⃣ Update button
                                $(".product_sub_add_update_btn")
                                    .removeAttr("disabled")
                                    .text("Update")
                                    .attr("onclick", "product_sub_add_update('" + unique_id + "')");
                            });
                        });
                    });

                } else {
                    $(".product_sub_add_update_btn").text("Error");
                    console.log(obj.error);
                }
            },
            error: function() {
                alert("Network Error");
            }
        });
    }
}


function prod_sub_delete(unique_id = "") {
    if (unique_id) {
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");
        confirm_delete('delete').then((result) => {
            if (result.isConfirmed) {
                var data = {
                    "unique_id": unique_id,
                    "action": "prod_sub_delete"
                }
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    success: function(data) {
                        var obj = JSON.parse(data);
                        var msg = obj.msg;
                        var status = obj.status;
                        var error = obj.error;
                        if (!status) {
                            url = '';
                        } else {
                            sub_list_datatable("po_sub_datatable");
                        }
                        sweetalert(msg, url);
                    }
                });
            } else {
                // alert("cancel");
            }
        });
    }
}

function sub_list_datatable(table_id = "", form_name = "", action = "") {
    var screen_unique_id = $("#screen_unique_id").val();
    var table = $("#"+table_id);
    var data = {
        "screen_unique_id": screen_unique_id,
        "action": table_id,
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var datatable = table.DataTable({
        "searching": false,
        "paging": false,
        "ordering": false,
        "info": false,
        "ajax": {
            url: ajax_url,
            type: "POST",
            data: data
        }
    });
}