$(document).ready(function () {
	init_datatable(table_id,form_name,action);
// 	so_sub_list_datatable("sale_order_sub_datatable");
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
var table_id 		= 'product_creation_datatable';
var action 			= "datatable";

function sales_order_final_cu(unique_id = "", update_condition = "") {
    alert("update_condition"+update_condition);
    alert("unique_id"+unique_id);
  var internet_status = is_online();

  if (!internet_status) {
    sweetalert("no_internet");
    return false;
  }

  var is_form = form_validity_check("was-validated");
  var company_name          = $("#company_name").val();
  var entry_date            = $("#entry_date").val();
  var customer_name         = $("#customer_name").val();
  var currency              = $("#currency").val();
  var exchange_rate         = $("#exchange_rate").val();
  var contact_person_name   = $("#contact_person_name").val();
  var customer_po_no        = $("#customer_po_no").val();
  var customer_po_date      = $("#customer_po_date").val();
  var status                = $("#status").val();
  // if (is_form) {
  var url       = sessionStorage.getItem("list_link");
  var ajax_url  = sessionStorage.getItem("folder_crud_link");
  var url1      = sessionStorage.getItem("create_link");
  var data      = $("#sales_order_main_data").find("input, select, date").serialize();
    alert(data);
  if (unique_id || update_condition) {
    if (company_name && entry_date && customer_name && currency && exchange_rate && contact_person_name && customer_po_no && customer_po_date && status) {
      data +=
        "&unique_id=" +
        unique_id +
        "&update_condition=" +
        update_condition +
        "&action=createupdate";

      // console.log(data);
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        beforeSend: function () {
          $(".createupdate_btn").attr("disabled", "disabled");
          $(".createupdate_btn").text("Loading...");
        },
        success: function (data) {
          var obj = JSON.parse(data);
          var msg = obj.msg;
          var status = obj.status;
          var error = obj.error;

          if (!status) {
            url = "";
            $(".createupdate_btn").text("Error");
            console.log(error);
          } else {
            if (msg == "already") {
              // Button Change Attribute
              url = "";

              $(".createupdate_btn").removeAttr("disabled", "disabled");
              if (unique_id) {
                $(".createupdate_btn").text("Update");
              } else {
                $(".createupdate_btn").text("Save");
              }
            }
          }
          if (unique_id) {
            sweetalert(msg, url);
          }
        },
        error: function (data) {
          alert("Network Error");
        }
      });
    } else {
      sweetalert("form_alert");
    }
  } else {
     if (company_name && entry_date && customer_name && currency && exchange_rate && contact_person_name && customer_po_no && customer_po_date && status) {
      sweetalert("form_alert");
      return; // Stop execution if any field is empty
    } else {
      data += "&action=sub_list_cnt";
      // console.log(data);
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        beforeSend: function () {
          $(".createupdate_btn").attr("disabled", "disabled");
          $(".createupdate_btn").text("Loading...");
        },
        success: function (data) {
          var obj = JSON.parse(data);
          var msg = obj.msg;
          var status = obj.status;
          var error = obj.error;

          if (msg == "sub_list") {
            sweetalert(msg, url);
          } else if (msg == "completed") {
            sweetalert("create", url);
          } else {
            $(".createupdate_btn").attr("disabled", "disabled");
            $(".createupdate_btn").text("Loading...");
          }
        },
        error: function (data) {
          alert("Network Error");
        }
      });
    }
  }
}
function item_filter(){
    
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var data_type               = $('#data_type').val();
	var group_unique_id         = $('#group_unique_id').val();
	var sub_group_unique_id     = $('#sub_group_unique_id').val();
	var category_unique_id      = $('#category_unique_id').val();

	var filter_data = {
		"data_type"             : data_type,
		"group_unique_id"       : group_unique_id,
		"sub_group_unique_id"   : sub_group_unique_id,
		"category_unique_id"    : category_unique_id
	};


	init_datatable(table_id, form_name, action, filter_data);


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


function sales_order_final_delete(unique_id = "") {

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






function get_sub_group(group_id, type = ""){
    
    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (group_id) {
		var data = {
			"group_id" 	: group_id,
			"type" 	: type,
			"action"		: "sub_group_name"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
                if(type === ""){
    				if (data) {
    					$("#sub_group_unique_id").html(data);
    				}
                } else if(type === 1) {
                    if (data) {
    					$("#sub_group_unique_id_sub_list").html(data);
    				}
                } else if(type === 2) {
                    if (data) {
    					$("#category_unique_id_sub").html(data);
    				}
                } else if(type === 3) {
                    if (data) {
    					$("#item_unique_id_sub").html(data);
    				}
                }

			}
		});
	}
}


function get_group_code(code){

    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (code!=="") {
		var data = {
			"code" 	: code,
			"action": "get_group_code"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			dataType: 'json',
			success : function(data) {
            
				if (data.status === 'success' && data.data) {
                    $("#uom").val(data.data);
                }

			}
		});
	} else {
	    $("#uom").val('');
	}
	
}
// Sub list
function sale_order_creation_add_update(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");
    var prod_unique_id = $('#unique_id').val();
    
    var product_unique_id       = $('#product_unique_id').val();
    var uom                     = $('#uom').val();
    var qty                     = $('#qty').val();
    var rate                    = $('#rate').val();
    var discount                = $('#discount').val();
    var tax                     = $('#tax').val();
    var amount                  = $('#amount').val();

    if (product_unique_id && uom && qty && rate && discount && tax && amount) {
        
        sales_order_final_cu("",prod_unique_id);

        var data = $("#sale_order_sublist_data").find("input, select, textarea").serialize();
        data += "&unique_id=" + unique_id + "&action=so_add_update&prod_unique_id=" + prod_unique_id;

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = "";

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".sale_order_creation_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {

                       
                    }

                    $(".sale_order_creation_add_update_btn").text(unique_id && msg === "already" ? "Update" : "Add");

                    so_sub_list_datatable("sale_order_sub_datatable");

                    $("#sub_group_unique_id").attr("disabled", "disabled");
                    $("#product_name").attr("disabled", "disabled");
                    $("#description").attr("disabled", "disabled");
                    $("#is_active").attr("disabled", "disabled");
                }
                sweetalert(msg, url);
            },
            error: function () {
                alert("Network Error");
            }
        });
    } else {
        sweetalert("form_alert");
    }
}



function so_sub_list_datatable (table_id = "", form_name = "", action = "") {
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
function prod_edit(unique_id = "") {
    if (unique_id) {
        var data        = "unique_id="+unique_id+"&action=prod_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".branch_details_add_update_btn").attr("disabled","disabled");
                $(".branch_details_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".branch_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var group_unique_id             = data.group_unique_id;
                    var sub_group_unique_id         = data.sub_group_unique_id;
                    var category_unique_id          = data.category_unique_id;
                    var item_unique_id              = data.item_unique_id;
                    var qty                         = data.qty;
                    var remarks                     = data.remarks;
                    var is_active                   = data.is_active;
                    
                    $("#group_unique_id_sub").val(data.group_unique_id).trigger("change");

                    // After group change triggers and populates sub_group, set timeout or handle inside callback to wait
                    setTimeout(function() {
                        $("#sub_group_unique_id_sub_list").val(data.sub_group_unique_id).trigger("change");
                    
                        setTimeout(function() {
                            $("#category_unique_id_sub").val(data.category_unique_id).trigger("change");
                    
                            setTimeout(function() {
                                $("#item_unique_id_sub").val(data.item_unique_id).trigger("change");
                            }, 300);
                        }, 300);
                    }, 300);
                    $("#qty").val(data.qty);
                    $("#uom").val(data.uom);
                    $("#remarks").val(data.remarks);
                    $("#is_active_sub").val(data.is_active).trigger("change");

                    // Button Change 
                    $(".sale_order_creation_add_update_btn").removeAttr("disabled","disabled");
                    $(".sale_order_creation_add_update_btn").text("Update");
                    so_sub_list_datatable("sale_order_sub_datatable");
                }
            },
            error       : function(data) {
                alert("Network Error");
            }
        });
    }
}
function prod_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id"     : unique_id,
                    "action"        : "prod_delete"
                }
    
                $.ajax({
                    type    : "POST",
                    url     : ajax_url,
                    data    : data,
                    success : function(data) {
    
                        var obj     = JSON.parse(data);
                        var msg     = obj.msg;
                        var status  = obj.status;
                        var error   = obj.error;
    
                        if (!status) {
                            url     = '';                            
                        } else {
                            so_sub_list_datatable("sale_order_sub_datatable");
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


$(document).ready(function () {
    // Enable Sub Group on Group change
    $("#group_unique_id_sub").on("change", function () {
        let val = $(this).val();
        resetDropdowns(["#sub_group_unique_id_sub_list", "#category_unique_id_sub", "#item_unique_id_sub"]);
        if (val) {
            $("#sub_group_unique_id_sub_list").prop("disabled", false);
        }
    });

    // Enable Category on Sub Group change
    $("#sub_group_unique_id_sub_list").on("change", function () {
        let val = $(this).val();
        resetDropdowns(["#category_unique_id_sub", "#item_unique_id_sub"]);
        if (val) {
            $("#category_unique_id_sub").prop("disabled", false);
        }
    });

    // Enable Item on Category change
    $("#category_unique_id_sub").on("change", function () {
        let val = $(this).val();
        resetDropdowns(["#item_unique_id_sub"]);
        if (val) {
            $("#item_unique_id_sub").prop("disabled", false);
        }
    });
});

// ✅ Reset and disable dropdowns
function resetDropdowns(selectors) {
    selectors.forEach(function (selector) {
        $(selector).val(null).trigger("change").prop("disabled", true);
    });
}
