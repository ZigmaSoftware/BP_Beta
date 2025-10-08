$(document).ready(function () {
	// var table_id 	= "units_datatable";
	init_datatable(table_id,form_name,action);
	prod_sub_list_datatable("product_sub_datatable");
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'standard_bom';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'standard_bom_datatable';
var action 			= "datatable";

// function standard_bom_cu(unique_id = "", update_condition = "") {
//   var internet_status = is_online();

//   if (!internet_status) {
//     sweetalert("no_internet");
//     return false;
//   }

//   var is_form = form_validity_check("was-validated");
//   var sub_group_unique_id = $("#sub_group_unique_id").val();
//   var update = $("#update").val();
//   var product_name = $("#product_name").val();
//   var is_active = $("#is_active").val();
//   // if (is_form) {
//   var url = sessionStorage.getItem("list_link");
//   var ajax_url = sessionStorage.getItem("folder_crud_link");
//   var url1 = sessionStorage.getItem("create_link");
//   var data = $("#product_details_main_form")
//     .find("input, select, textarea")
//     .serialize();
//     console.info(data)

//   if (unique_id || update_condition) {
//     if (sub_group_unique_id && product_name && is_active) {
//       data +=
//         "&unique_id=" +
//         unique_id +
//         "&update_condition=" +
//         update_condition +
//         "&action=createupdate";

//       $.ajax({
//         type: "POST",
//         url: ajax_url,
//         data: data,
//         beforeSend: function () {
//           $(".createupdate_btn").attr("disabled", "disabled");
//           $(".createupdate_btn").text("Loading...");
//         },
//         success: function (data) {
//           var obj = JSON.parse(data);
//           var msg = obj.msg;
//           var status = obj.status;
//           var error = obj.error;

//           if (!status) {
//             url = "";
//             $(".createupdate_btn").text("Error");
//             console.log(error);
//           } else {
//             if (msg == "already") {
//               // Button Change Attribute
//               url = "";

//               $(".createupdate_btn").removeAttr("disabled", "disabled");
//               if (unique_id) {
//                 $(".createupdate_btn").text("Update");
//               } else {
//                 $(".createupdate_btn").text("Save");
//               }
//             } else if (msg == "create"){
//                 // alert(msg);
//                 sweetalert(msg, url);
//             }
//           }
//           if (unique_id) {
//             sweetalert(msg, url);
//           }
//         },
//         error: function (data) {
//           alert("Network Error");
//         }
//       });
//     } else {
//       sweetalert("form_alert");
//     }
//   } else {
//     if (!sub_group_unique_id || !product_name || !is_active) {
//       sweetalert("form_alert");
//       return; // Stop execution if any field is empty
//     } else {
//     data +=
//         "&unique_id=" +
//         unique_id +
//         "&update_condition=" +
//         update_condition +
//         "&action=createupdate";

//       $.ajax({
//         type: "POST",
//         url: ajax_url,
//         data: data,
//         beforeSend: function () {
//           $(".createupdate_btn").attr("disabled", "disabled");
//           $(".createupdate_btn").text("Loading...");
//         },
//         success: function (data) {
//           var obj = JSON.parse(data);
//           var msg = obj.msg;
//           var status = obj.status;
//           var error = obj.error;

//           if (!status) {
//             url = "";
//             $(".createupdate_btn").text("Error");
//             console.log(error);
//           } else {
//             if (msg == "already") {
//               // Button Change Attribute
//               url = "";

//               $(".createupdate_btn").removeAttr("disabled", "disabled");
//               if (unique_id) {
//                 $(".createupdate_btn").text("Update");
//               } else {
//                 $(".createupdate_btn").text("Save");
//               }
//             } else if (msg == "create"){
//                 // alert(msg);
//                 sweetalert(msg, url);
//             }
//           }
//           if (unique_id) {
//             sweetalert(msg, url);
//           }
//         },
//         error: function (data) {
//           alert("Network Error");
//         }
//       });
//     }
//   }
// }

function standard_bom_cu(unique_id = "", update_condition = ""){
    
    var url      = sessionStorage.getItem("list_link");
    
    if(!update_condition){
        window.location.href = url;
    }
}

function standard_bom_drop_down(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    // var is_form = form_validity_check("was-validated");
    var group_unique_id = $('#group_unique_id').val();

    if (group_unique_id !== "") {

         var data 	 = $("#product_details_main_form").find("input, select, textarea").serialize();
        data 		+= "&unique_id="+unique_id+"&action=createupdate_drop_down";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        var url1      = sessionStorage.getItem("create_link");

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
				sweetalert(msg,url1);
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        
        sweetalert("form_alert_group");
    }
}

function item_filter_1(){
    
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

// 	var data_type               = $('#data_type').val();
	var group_unique_id         = $('#group_unique_id').val();
	var sub_group_unique_id     = $('#sub_group_unique_id').val();
	var company_unique_id       = $('#company_unique_id').val();
	var prod_unique_id          = $('#prod_unique_id').val();

	var filter_data = {
// 		"data_type"             : data_type,
		"group_unique_id"       : group_unique_id,
		"sub_group_unique_id"   : sub_group_unique_id,
		"company_unique_id"     : company_unique_id,
		"prod_unique_id"        : prod_unique_id
	};
	
	console.info(filter_data);


	init_datatable(table_id, form_name, action, filter_data);


}

// function init_datatable(table_id='',form_name='',action='', filter_data ='') {

// 	var table = $("#"+table_id);
// 	var data 	  = {
// 		"action"	: action
// 	}
	
// 	console.info(data);
	
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


function init_datatable(table_id = '', form_name = '', action = '', filter_data = {}) {

    var table = $("#" + table_id);
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var datatable = table.DataTable({
        ordering: true,
        searching: true,
        processing: true,
        serverSide: true,
        ajax: {
            url: ajax_url,
            type: "POST",
            data: function (d) {
                // Merge DataTables internal params with your custom filter data
                return {
                    ...d,
                    action: action,
                    ...(filter_data || {})
                };
            },
            error: function (xhr, error, thrown) {
                console.error("Invalid JSON response:");
                console.error(xhr.responseText); // Shows actual error or PHP notice
            }
        }
    });
}



// function init_datatable(table_id = '', form_name = '', action = '', filter_data = {}) {

//     var table = $("#" + table_id);
//     var ajax_url = sessionStorage.getItem("folder_crud_link");

//     var datatable = table.DataTable({
//         ordering: true,
//         searching: true,
//         processing: true,
//         serverSide: true,
//         ajax: {
//             url: ajax_url,
//             type: "POST",
//             data: function (d) {
//                 // Merge DataTables internal params with your custom filter data
//                 return {
//                     ...d,
//                     action: action,
//                     ...(filter_data || {})
//                 };
//             },
//             error: function (xhr, error, thrown) {
//                 console.error("Invalid JSON response:");
//                 console.error(xhr.responseText); // Shows actual error or PHP notice
//             }
//         }
//     });
// }


function standard_bom_toggle(unique_id = "", new_status = 0) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "toggle",
            unique_id: unique_id,
            is_active: new_status
        },
        success: function (data) {
            const obj = JSON.parse(data);
            sweetalert(obj.msg, url);

            if (obj.status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }
        }
    });
}




function get_prod_types(selectElement) {
    // alert("Hey");
    var vertical_id = selectElement.value; // get selected value
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (vertical_id) {
        var data = {
            "vertical_id": vertical_id,
            "action": "product_type"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(data) {
                // alert('success block hit');
                // alert(data);
                $("#sub_group_unique_id").html(data);
            },
            error: function(xhr, status, error) {
                // alert("AJAX Error: " + status + "\n" + error + "\n" + xhr.responseText);
                console.log("Error response:", xhr.responseText);
            }
        });

        // Update the hidden input too
        $("#group_unique_id").val(vertical_id);
    }
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
let isProductCUCalled = false;
function standard_bom_add_update(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");
    var prod_unique_id = $('#unique_id').val();
    
    var group_unique_id_sub                 = $('#group_unique_id_sub').val();
    var sub_group_unique_id_sub_list        = $('#sub_group_unique_id_sub_list').val();
    var category_unique_id_sub              = $('#category_unique_id_sub').val();
    var item_unique_id_sub                  = $('#item_unique_id_sub').val();
    var qty                                 = $('#qty').val();
    var is_active_sub                       = $('#is_active_sub').val();
    var type                                = $("#material_type").val();
    
    if(type === 'product'){
        var product_name                        = $('#product_name').val();
    } else {
        var product_name                        = $('#semi_finished_item').val();
    }
    

    // if (is_form) {
    if (group_unique_id_sub && sub_group_unique_id_sub_list && category_unique_id_sub && item_unique_id_sub && qty && is_active_sub && product_name) {
        if (!isProductCUCalled) {
            standard_bom_cu("", product_name);
            isProductCUCalled = true; // Mark as called
        }

        var data = $("#product_details_form").find("input, select, textarea").serialize();
       data += "&unique_id=" + unique_id
      + "&product_name=" + product_name
      + "&material_type=" + type
      + "&action=product_add_update&prod_unique_id=" + prod_unique_id;


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
                    $(".standard_bom_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {
                        form_reset("product_sub_datatable");

                        // ✅ Clear form fields
                        $("#product_details_form").find("input[type='text'], textarea").val('');
                        $("#group_unique_id_sub").val(null).trigger('change'); // Keep this enabled
                        resetDropdowns(["#sub_group_unique_id_sub_list", "#category_unique_id_sub", "#item_unique_id_sub"]); // Disable and reset all others

                        // Disable them again for step-by-step process
                        $("#sub_group_unique_id_sub_list").prop("disabled", true);
                        $("#category_unique_id_sub").prop("disabled", true);
                        $("#item_unique_id_sub").prop("disabled", true);
                    }

                    $(".standard_bom_add_update_btn").text(unique_id && msg === "already" ? "Edit" : "Add");

                    prod_sub_list_datatable("product_sub_datatable");

                    $("#sub_group_unique_id").attr("disabled", "disabled");
                    $("#product_name").attr("disabled", "disabled");
                    $("#description").prop('readonly', true);
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



function prod_sub_list_datatable (table_id = "", form_name = "", action = "") {
     //alert("test");
    var prod_unique_id  = $('#unique_id').val();
    var type                                = $("#material_type").val();
    
    if(type === 'product'){
        var product_name                        = $('#product_name').val();
    } else {
        var product_name                        = $('#semi_finished_item').val();
    }

    
    var table = $("#"+table_id);
    var data      = {
        "product_name"          : product_name,
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
                    $(".standard_bom_add_update_btn").removeAttr("disabled","disabled");
                    $(".standard_bom_add_update_btn").text("Edit");
                     $(".standard_bom_add_update_btn").attr("onclick","standard_bom_add_update('"+unique_id+"')");
                    prod_sub_list_datatable("product_sub_datatable");
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
                            prod_sub_list_datatable("product_sub_datatable");
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
    
    $("#group_unique_id, #sub_group_unique_id, #company_unique_id").on("change", function () {
        get_prod();
    });
});

// ✅ Reset and disable dropdowns
function resetDropdowns(selectors) {
    selectors.forEach(function (selector) {
        $(selector).val(null).trigger("change").prop("disabled", true);
    });
}

// Function to fetch products
function get_prod() {
    // Get current values
    let group_id = $("#group_unique_id").val();
    let sub_group_id = $("#sub_group_unique_id").val();
    let company_id = $("#company_unique_id").val();
    
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");

    // Send AJAX request
    $.ajax({
        url: ajax_url, // Must be defined in your PHP output
        type: "POST",
        data: {
            action: "prod_names",
            group_unique_id: group_id,
            sub_group_unique_id: sub_group_id,
            company_unique_id: company_id
        },
        beforeSend: function () {
            // Optional: disable product dropdown or show loader
            $("#prod_unique_id").prop("disabled", true).html('<option>Loading...</option>');
        },
        success: function (response) {
            // Populate product dropdown
            $("#prod_unique_id").prop("disabled", false).html(response);
        },
        error: function (xhr, status, error) {
            console.error("AJAX Error:", error);
            $("#prod_unique_id").prop("disabled", false).html('<option>Error loading products</option>');
        }
    });
}
// global flag toggled by Material Type
window.hideSemiCategory = false;

// Custom matcher that can exclude a specific option by text
function categoryMatcher(params, data) {
  // Always keep optgroups etc.
  if ($.type(data.children) !== 'undefined') {
    var filteredChildren = [];
    for (var i = 0; i < data.children.length; i++) {
      var match = categoryMatcher(params, data.children[i]);
      if (match != null) filteredChildren.push(match);
    }
    if (filteredChildren.length) {
      var modifiedData = $.extend({}, data, true);
      modifiedData.children = filteredChildren;
      return modifiedData;
    }
    return null;
  }

  if (!data.text) return null;

  // EXCLUDE when the flag is on
  var isSemi = data.text.trim().toUpperCase() === 'FABRICATION';
  if (window.hideSemiCategory && isSemi) return null;

  // Normal select2 matching behavior
  if (!params.term || params.term.trim() === '') return data;
  var term = params.term.toUpperCase();
  return data.text.toUpperCase().indexOf(term) > -1 ? data : null;
}

// Re-init helper to apply the matcher immediately
function reinitCategorySelect2() {
    // alert("3333")
  var $cat = $('#category_unique_id_sub');
  var currentVal = $cat.val(); // remember selection

  // If current selection is the one we’re hiding, clear it
  var currentText = $cat.find('option:selected').text().trim().toUpperCase();
  if (window.hideSemiCategory && currentText === 'FABRICATION') {
    currentVal = null;
  }

  // Destroy and re-init with matcher so result list is rebuilt
  if ($cat.data('select2')) $cat.select2('destroy');
  $cat.select2({ matcher: categoryMatcher, width: 'resolve' });

  if (currentVal) {
    $cat.val(currentVal).trigger('change');
  } else {
    $cat.val(null).trigger('change');
  }
}

function toggleMaterialFields() {
  var mt = $('#material_type').val();
  var isViewMode = $('#update').val() == "1"; // 1 means update/view mode

  if (mt === 'semi_finished') {
    window.hideSemiCategory = true;   // hide the SEMI-FINISHED option
    $('#semi_finished_row').show();
    $('#semi_finished_item').prop('disabled', isViewMode ? true : false);

    $('#product_name_row').hide();
    $('#product_name').prop('disabled', true).val(null).trigger('change');
  } 
  else if (mt === 'product') {
    window.hideSemiCategory = false;  // show it again
    $('#product_name_row').show();
    $('#product_name').prop('disabled', isViewMode ? true : false);

    $('#semi_finished_row').hide();
    $('#semi_finished_item').prop('disabled', true).val(null).trigger('change');
  } 
  else {
    window.hideSemiCategory = false;
    $('#product_name_row, #semi_finished_row').hide();
    $('#product_name, #semi_finished_item').prop('disabled', true).val(null).trigger('change');
  }

  // Apply the filter immediately to the Category select
  reinitCategorySelect2();
}

// On first load, ensure Select2 uses the matcher
$(document).ready(function () {
  reinitCategorySelect2(); // initialize Category with matcher
  toggleMaterialFields();  // respect any preselected material type
});

function standard_bom_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#standardUploadModal').modal('show');
    
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

