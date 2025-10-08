// $(document).ready(function () {
// 	// var table_id 	= "units_datatable";
// 	init_datatable(table_id,form_name,action);
// 	prod_sub_list_datatable("product_sub_datatable");
// });
// $(document).ready(function () {
//   $(".select2").select2();

//   $('#add_item_row').click(function () {
//     const newRow = `<tr>
//       <td><input type="text" name="item_code[]" class="form-control" required></td>
//       <td><input type="text" name="item_description[]" class="form-control"></td>
//       <td><input type="number" name="quantity[]" step="0.01" class="form-control" required></td>
//       <td><input type="text" name="uom[]" class="form-control" required></td>
//       <td>
//         <select name="preferred_vendor_id[]" class="form-control select2">
//           <option value="">Select</option>
//         </select>
//       </td>
//       <td><input type="number" name="budgetary_rate[]" step="0.01" class="form-control"></td>
//       <td><input type="text" name="item_remarks[]" class="form-control"></td>
//       <td><input type="date" name="required_delivery_date[]" class="form-control" required></td>
//       <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
//     </tr>`;

//     $('#requisition_items_table tbody').append(newRow);
//     $('.select2').select2();
//   });

//   $(document).on('click', '.remove-item', function () {
//     $(this).closest('tr').remove();
//   });

// //   $(document).on('submit', '#purchase_requisition_form', function (e) {
// //     e.preventDefault();

//     const formData = new FormData(this);
//     const ajax_url = sessionStorage.getItem("folder_crud_link");
//     const list_url = sessionStorage.getItem("list_link");

//     $.ajax({
//       url: ajax_url,
//       type: "POST",
//       data: formData,
//       contentType: false,
//       processData: false,
//       beforeSend: function () {
//         $(".createupdate_btn").attr("disabled", "disabled").text("Processing...");
//       },
//       success: function (data) {
//         const res = JSON.parse(data);
//         sweetalert(res.msg, list_url);
//       },
//       error: function () {
//         alert("Network Error");
//       },
//       complete: function () {
//         $(".createupdate_btn").removeAttr("disabled").text("Save");
//       }
//     });
//   });
// });

// var company_name 	= sessionStorage.getItem("company_name");
// var company_address	= sessionStorage.getItem("company_name");
// var company_phone 	= sessionStorage.getItem("company_name");
// var company_email 	= sessionStorage.getItem("company_name");
// var company_logo 	= sessionStorage.getItem("company_name");

// var form_name 		= 'product_creation';
// var form_header		= '';
// var form_footer 	= '';
// var table_name 		= '';
// var table_id 		= 'product_creation_datatable';
// var action 			= "datatable";

// function purchase_requisition_cu(unique_id = "",  update_condition= "") {

//     var internet_status  = is_online();

//     if (!internet_status) {
//         sweetalert("no_internet");
//         return false;
//     }

//     var is_form = form_validity_check("was-validated");
//     var sub_group_unique_id     = $('#sub_group_unique_id').val();
//     var product_name            = $('#product_name').val();
//     var is_active               = $('#is_active').val();
//     // if (is_form) {
//     if (sub_group_unique_id && product_name && is_active) {

//         var data 	 = $("#product_details_main_form").find("input, select, textarea").serialize();
//         data 		+= "&unique_id="+unique_id+"&update_condition="+update_condition+"&action=createupdate";
        
//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url      = sessionStorage.getItem("list_link");
//         var url1      = sessionStorage.getItem("create_link");

//         // console.log(data);
//         $.ajax({
// 			type 	: "POST",
// 			url 	: ajax_url,
// 			data 	: data,
// 			beforeSend 	: function() {
// 				$(".createupdate_btn").attr("disabled","disabled");
// 				$(".createupdate_btn").text("Loading...");
// 			},
// 			success		: function(data) {

// 				var obj     = JSON.parse(data);
// 				var msg     = obj.msg;
// 				var status  = obj.status;
// 				var error   = obj.error;

// 				if (!status) {
// 					url 	= '';
//                     $(".createupdate_btn").text("Error");
//                     console.log(error);
// 				} else {
// 					if (msg=="already") {
// 						// Button Change Attribute
// 						url 		= '';

// 						$(".createupdate_btn").removeAttr("disabled","disabled");
// 						if (unique_id) {
// 							$(".createupdate_btn").text("Update");
// 						} else {
// 							$(".createupdate_btn").text("Save");
// 						}
// 					}
// 				}
// 				// sweetalert(msg,url);
// 			},
// 			error 		: function(data) {
// 				alert("Network Error");
// 			}
// 		});


//     } else {
//         sweetalert("form_alert");
//     }
// }
// function product_creation_drop_down(unique_id = "") {

//     var internet_status  = is_online();

//     if (!internet_status) {
//         sweetalert("no_internet");
//         return false;
//     }

//     // var is_form = form_validity_check("was-validated");
//     var group_unique_id = $('#group_unique_id').val();

//     if (group_unique_id !== "") {

//         var data 	 = $(".was-validated").serialize();
//         data 		+= "&unique_id="+unique_id+"&action=createupdate_drop_down";

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url      = sessionStorage.getItem("list_link");
//         var url1      = sessionStorage.getItem("create_link");

//         // console.log(data);
//         $.ajax({
// 			type 	: "POST",
// 			url 	: ajax_url,
// 			data 	: data,
// 			beforeSend 	: function() {
// 				$(".createupdate_btn").attr("disabled","disabled");
// 				$(".createupdate_btn").text("Loading...");
// 			},
// 			success		: function(data) {

// 				var obj     = JSON.parse(data);
// 				var msg     = obj.msg;
// 				var status  = obj.status;
// 				var error   = obj.error;

// 				if (!status) {
// 					url 	= '';
//                     $(".createupdate_btn").text("Error");
//                     console.log(error);
// 				} else {
// 					if (msg=="already") {
// 						// Button Change Attribute
// 						url 		= '';

// 						$(".createupdate_btn").removeAttr("disabled","disabled");
// 						if (unique_id) {
// 							$(".createupdate_btn").text("Update");
// 						} else {
// 							$(".createupdate_btn").text("Save");
// 						}
// 					}
// 				}
// 				sweetalert(msg,url1);
// 			},
// 			error 		: function(data) {
// 				alert("Network Error");
// 			}
// 		});


//     } else {
//         sweetalert("form_alert_group");
//     }
// }

// function item_filter(){
    
// 	var internet_status = is_online();

// 	if (!internet_status) {
// 		sweetalert("no_internet");
// 		return false;
// 	}

// 	var data_type               = $('#data_type').val();
// 	var group_unique_id         = $('#group_unique_id').val();
// 	var sub_group_unique_id     = $('#sub_group_unique_id').val();
// 	var category_unique_id      = $('#category_unique_id').val();

// 	var filter_data = {
// 		"data_type"             : data_type,
// 		"group_unique_id"       : group_unique_id,
// 		"sub_group_unique_id"   : sub_group_unique_id,
// 		"category_unique_id"    : category_unique_id
// 	};


// 	init_datatable(table_id, form_name, action, filter_data);


// }

// function init_datatable(table_id='',form_name='',action='', filter_data ='') {

// 	var table = $("#"+table_id);
// 	var data 	  = {
// 		"action"	: action, 
// 		...filter_data
// 	}
// 	var ajax_url = sessionStorage.getItem("folder_crud_link");

// 	var datatable = table.DataTable({
// 		ordering    : true,
// 		searching   : true,
// 		"ajax"		: {
// 			url 	: ajax_url,
// 			type 	: "POST",
// 			data 	: data
// 		}
// 	});
// }


// function product_creation_delete(unique_id = "") {

// 	var ajax_url = sessionStorage.getItem("folder_crud_link");
// 	var url      = sessionStorage.getItem("list_link");
	
// 	confirm_delete('delete')
// 	.then((result) => {
// 		if (result.isConfirmed) {

// 			var data = {
// 				"unique_id" 	: unique_id,
// 				"action"		: "delete"
// 			}

// 			$.ajax({
// 				type 	: "POST",
// 				url 	: ajax_url,
// 				data 	: data,
// 				success : function(data) {

// 					var obj     = JSON.parse(data);
// 					var msg     = obj.msg;
// 					var status  = obj.status;
// 					var error   = obj.error;

// 					if (!status) {
// 						url 	= '';
						
// 					} else {
// 						init_datatable(table_id,form_name,action);
// 					}
// 					sweetalert(msg,url);
// 				}
// 			});

// 		} else {
// 			// alert("cancel");
// 		}
// 	});
// }






// function get_sub_group(group_id, type = ""){
    
//     var ajax_url = sessionStorage.getItem("folder_crud_link");

// 	if (group_id) {
// 		var data = {
// 			"group_id" 	: group_id,
// 			"type" 	: type,
// 			"action"		: "sub_group_name"
// 		}

// 		$.ajax({
// 			type 	: "POST",
// 			url 	: ajax_url,
// 			data 	: data,
// 			success : function(data) {
//                 if(type === ""){
//     				if (data) {
//     					$("#sub_group_unique_id").html(data);
//     				}
//                 } else if(type === 1) {
//                     if (data) {
//     					$("#sub_group_unique_id_sub_list").html(data);
//     				}
//                 } else if(type === 2) {
//                     if (data) {
//     					$("#category_unique_id_sub").html(data);
//     				}
//                 } else if(type === 3) {
//                     if (data) {
//     					$("#item_unique_id_sub").html(data);
//     				}
//                 }

// 			}
// 		});
// 	}
// }


// function get_group_code(code){

//     var ajax_url = sessionStorage.getItem("folder_crud_link");

// 	if (code!=="") {
// 		var data = {
// 			"code" 	: code,
// 			"action": "get_group_code"
// 		}

// 		$.ajax({
// 			type 	: "POST",
// 			url 	: ajax_url,
// 			data 	: data,
// 			dataType: 'json',
// 			success : function(data) {
            
// 				if (data.status === 'success' && data.data) {
//                     $("#uom").val(data.data);
//                 }

// 			}
// 		});
// 	} else {
// 	    $("#uom").val('');
// 	}
	
// }
// // Sub list
// function product_creation_add_update(unique_id = "") {
//     var internet_status = is_online();

//     if (!internet_status) {
//         sweetalert("no_internet");
//         return false;
//     }

//     var is_form = form_validity_check("was-validated");
//     var prod_unique_id = $('#unique_id').val();

//     if (is_form) {
//         product_creation_cu("",prod_unique_id);

//         var data = $("#product_details_form").find("input, select, textarea").serialize();
//         data += "&unique_id=" + unique_id + "&action=product_add_update&prod_unique_id=" + prod_unique_id;

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url = "";

//         $.ajax({
//             type: "POST",
//             url: ajax_url,
//             data: data,
//             success: function (data) {
//                 var obj = JSON.parse(data);
//                 var msg = obj.msg;
//                 var status = obj.status;
//                 var error = obj.error;

//                 if (!status) {
//                     $(".product_creation_add_update_btn").text("Error");
//                     console.log(error);
//                 } else {
//                     if (msg !== "already") {
//                         form_reset("product_sub_datatable");

//                         // ✅ Clear form fields
//                         $("#product_details_form").find("input[type='text'], textarea").val('');
//                         $("#group_unique_id_sub").val(null).trigger('change'); // Keep this enabled
//                         resetDropdowns(["#sub_group_unique_id_sub_list", "#category_unique_id_sub", "#item_unique_id_sub"]); // Disable and reset all others

//                         // Disable them again for step-by-step process
//                         $("#sub_group_unique_id_sub_list").prop("disabled", true);
//                         $("#category_unique_id_sub").prop("disabled", true);
//                         $("#item_unique_id_sub").prop("disabled", true);
//                     }

//                     $(".product_creation_add_update_btn").text(unique_id && msg === "already" ? "Edit" : "Add");

//                     prod_sub_list_datatable("product_sub_datatable");

//                     $("#sub_group_unique_id").attr("disabled", "disabled");
//                     $("#product_name").attr("disabled", "disabled");
//                     $("#description").attr("disabled", "disabled");
//                     $("#is_active").attr("disabled", "disabled");
//                 }
//                 sweetalert(msg, url);
//             },
//             error: function () {
//                 alert("Network Error");
//             }
//         });
//     } else {
//         sweetalert("form_alert");
//     }
// }



// function prod_sub_list_datatable (table_id = "", form_name = "", action = "") {
//      //alert("test");
//     var prod_unique_id  = $('#unique_id').val();
    
//     var table = $("#"+table_id);
//     var data      = {
//         "prod_unique_id"    : prod_unique_id,
//         "action"                : table_id, 
//     };
//     var ajax_url = sessionStorage.getItem("folder_crud_link");

//     var datatable = table.DataTable({
// 		ordering    : true,
// 		searching   : true,
//         "searching": false,
//         "paging":   false,
//         "ordering": false,
//         "info":     false,
//         "ajax"      : {
//             url     : ajax_url,
//             type    : "POST",
//             data    : data
//         }
//     });
// }
// function prod_edit(unique_id = "") {
//     if (unique_id) {
//         var data        = "unique_id="+unique_id+"&action=prod_edit";

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         // var url      = sessionStorage.getItem("list_link");
//         var url      = "";

//         // console.log(data);
//         $.ajax({
//             type    : "POST",
//             url     : ajax_url,
//             data    : data,
//             beforeSend  : function() {
//                 $(".branch_details_add_update_btn").attr("disabled","disabled");
//                 $(".branch_details_add_update_btn").text("Loading...");
//             },
//             success     : function(data) {

//                 var obj     = JSON.parse(data);
//                 var data    = obj.data;
//                 var msg     = obj.msg;
//                 var status  = obj.status;
//                 var error   = obj.error;

//                 if (!status) {
//                     $(".branch_details_add_update_btn").text("Error");
//                     console.log(error);
//                 } else {
//                     console.log(obj);
//                     var group_unique_id             = data.group_unique_id;
//                     var sub_group_unique_id         = data.sub_group_unique_id;
//                     var category_unique_id          = data.category_unique_id;
//                     var item_unique_id              = data.item_unique_id;
//                     var qty                         = data.qty;
//                     var remarks                     = data.remarks;
//                     var is_active                   = data.is_active;
                    
//                     $("#group_unique_id_sub").val(data.group_unique_id).trigger("change");

//                     // After group change triggers and populates sub_group, set timeout or handle inside callback to wait
//                     setTimeout(function() {
//                         $("#sub_group_unique_id_sub_list").val(data.sub_group_unique_id).trigger("change");
                    
//                         setTimeout(function() {
//                             $("#category_unique_id_sub").val(data.category_unique_id).trigger("change");
                    
//                             setTimeout(function() {
//                                 $("#item_unique_id_sub").val(data.item_unique_id).trigger("change");
//                             }, 300);
//                         }, 300);
//                     }, 300);
//                     $("#qty").val(data.qty);
//                     $("#uom").val(data.uom);
//                     $("#remarks").val(data.remarks);
//                     $("#is_active_sub").val(data.is_active).trigger("change");

//                     // Button Change 
//                     $(".product_creation_add_update_btn").removeAttr("disabled","disabled");
//                     $(".product_creation_add_update_btn").text("Edit");
//                      $(".product_creation_add_update_btn").attr("onclick","product_creation_add_update('"+unique_id+"')");
//                     prod_sub_list_datatable("product_sub_datatable");
//                 }
//             },
//             error       : function(data) {
//                 alert("Network Error");
//             }
//         });
//     }
// }
// function prod_delete(unique_id = "") {
//     if (unique_id) {

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url      = sessionStorage.getItem("list_link");
        
//         confirm_delete('delete')
//         .then((result) => {
//             if (result.isConfirmed) {
    
//                 var data = {
//                     "unique_id"     : unique_id,
//                     "action"        : "prod_delete"
//                 }
    
//                 $.ajax({
//                     type    : "POST",
//                     url     : ajax_url,
//                     data    : data,
//                     success : function(data) {
    
//                         var obj     = JSON.parse(data);
//                         var msg     = obj.msg;
//                         var status  = obj.status;
//                         var error   = obj.error;
    
//                         if (!status) {
//                             url     = '';                            
//                         } else {
//                             prod_sub_list_datatable("product_sub_datatable");
//                         }
//                         sweetalert(msg,url);
//                     }
//                 });
    
//             } else {
//                 // alert("cancel");
//             }
//         });
//     }
// }


// $(document).ready(function () {
//     // Enable Sub Group on Group change
//     $("#group_unique_id_sub").on("change", function () {
//         let val = $(this).val();
//         resetDropdowns(["#sub_group_unique_id_sub_list", "#category_unique_id_sub", "#item_unique_id_sub"]);
//         if (val) {
//             $("#sub_group_unique_id_sub_list").prop("disabled", false);
//         }
//     });

//     // Enable Category on Sub Group change
//     $("#sub_group_unique_id_sub_list").on("change", function () {
//         let val = $(this).val();
//         resetDropdowns(["#category_unique_id_sub", "#item_unique_id_sub"]);
//         if (val) {
//             $("#category_unique_id_sub").prop("disabled", false);
//         }
//     });

//     // Enable Item on Category change
//     $("#category_unique_id_sub").on("change", function () {
//         let val = $(this).val();
//         resetDropdowns(["#item_unique_id_sub"]);
//         if (val) {
//             $("#item_unique_id_sub").prop("disabled", false);
//         }
//     });
// });

// // ✅ Reset and disable dropdowns
// function resetDropdowns(selectors) {
//     selectors.forEach(function (selector) {
//         $(selector).val(null).trigger("change").prop("disabled", true);
//     });
// }




var form_name   = 'purchase_requisition';
var table_id    = 'purchase_requisition_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

function purchase_requisition_cu(unique_id = "") {
    var internet_status = is_online();
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");
    if (!is_form) {
        sweetalert("form_alert");
        return false;
    }

    var data = new FormData($("#purchase_requisition_form")[0]);
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
            var obj = JSON.parse(data);
            var msg = obj.msg;
            var status = obj.status;
            var error = obj.error;

            if (!status) {
                $(".createupdate_btn").text("Error");
                console.log(error);
            } else {
                if (msg === "already") {
                    $(".createupdate_btn").removeAttr("disabled");
                    $(".createupdate_btn").text(unique_id ? "Update" : "Save");
                }
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

// Delete Function
function purchase_requisition_delete(unique_id = "") {
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

// Project Name Load
// function get_project_name(company_id = "") {
//     if (company_id) {
//         var data = {
//             "company_id": company_id,
//             "action": "project_name"
//         };
//         $.ajax({
//             type: "POST",
//             url: ajax_url,
//             data: data,
//             success: function (data) {
//                 if (data) {
//                     $("#project_id").html(data);
//                 }
//             }
//         });
//     }
// }

function get_project_name(company_id = "") {
    if (company_id) {
        var data = {
            "company_id": company_id,
            "action": "project_name"
        };
        $("#project_id").html('<option>Loading...</option>').prop("disabled", true);

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#project_id").html(data).prop("disabled", false);
                    $("#project_id").select2(); // Reinitialize Select2
                    // Rebind change handler to make sure get_linked_so works after loading new options
                    $('#project_id').off('change').on('change', function () {
                        get_linked_so(this.value);
                    });
                }
            }
        });
    }
}


// Project Name Load
function get_linked_so(project_id = "") {
    alert("load");
    if (project_id) {
        var data = {
            "project_id": project_id,
            "action": "linked_so"
        };
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                if (data) {
                    $("#sales_order_id").html(data);
                }
            }
        });
    }
}

// Conditional: Enable/Disable Linked Sales Order
function toggleSalesOrderField() {
    var requisitionFor = $('#requisition_for').val();
    if (requisitionFor === 'SO') {
        $('#sales_order_id').prop('disabled', false);
    } else {
        $('#sales_order_id').prop('disabled', true).val('').trigger('change');
    }
}

$(document).ready(function () {
    $(".select2").select2();
    
    $('#project_id').on('change', function () {
        alert("hello");
    });

    toggleSalesOrderField();

    $('#requisition_for').on('change', toggleSalesOrderField);

    // Dynamic Add Item Row
    let rowIndex = $('#requisition_items_table tbody tr').length;

    $('#add_item_row').click(function () {
        const newRow = `
        <tr>
            <td><input type="text" id="item_code_${rowIndex}" name="item_code[]" class="form-control" required></td>
            <td><input type="text" id="item_description_${rowIndex}" name="item_description[]" class="form-control"></td>
            <td><input type="number" id="quantity_${rowIndex}" name="quantity[]" step="0.01" class="form-control" required></td>
            <td><input type="text" id="uom_${rowIndex}" name="uom[]" class="form-control" required></td>
            <td>
              <select id="vendor_${rowIndex}" name="preferred_vendor_id[]" class="form-control select2">
                <option value="">Select</option>
              </select>
            </td>
            <td><input type="number" id="budgetary_rate_${rowIndex}" name="budgetary_rate[]" step="0.01" class="form-control"></td>
            <td><input type="text" id="item_remarks_${rowIndex}" name="item_remarks[]" class="form-control"></td>
            <td><input type="date" id="required_delivery_date_${rowIndex}" name="required_delivery_date[]" class="form-control" required></td>
            <td><button type="button" class="btn btn-danger remove-item">Remove</button></td>
        </tr>
        `;
        $('#requisition_items_table tbody').append(newRow);
        $(`#vendor_${rowIndex}`).select2();
        rowIndex++;
    });

    $(document).on('click', '.remove-item', function () {
        $(this).closest('tr').remove();
    });
});
