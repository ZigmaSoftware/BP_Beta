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

var form_name 		= 'product_creation';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'product_creation_datatable';
var action 			= "datatable";

function product_creation_cu(unique_id = "", update_condition = "") {
  var internet_status = is_online();

  if (!internet_status) {
    sweetalert("no_internet");
    return false;
  }

  var is_form = form_validity_check("was-validated");
  var sub_group_unique_id = $("#sub_group_unique_id").val();
  var update = $("#update").val();
  var product_name = $("#product_name").val();
  var is_active = $("#is_active").val();
  // if (is_form) {
  var url = sessionStorage.getItem("list_link");
  var ajax_url = sessionStorage.getItem("folder_crud_link");
  var url1 = sessionStorage.getItem("create_link");
  var data = $("#product_details_main_form")
    .find("input, select, textarea")
    .serialize();
    console.info(data)

  if (unique_id || update_condition) {
    if (sub_group_unique_id && product_name && is_active) {
      data +=
        "&unique_id=" +
        unique_id +
        "&update_condition=" +
        update_condition +
        "&action=createupdate";

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
            } else if (msg == "create"){
                // alert(msg);
                sweetalert(msg, url);
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
    if (!sub_group_unique_id || !product_name || !is_active) {
      sweetalert("form_alert");
      return; // Stop execution if any field is empty
    } else {
    data +=
        "&unique_id=" +
        unique_id +
        "&update_condition=" +
        update_condition +
        "&action=createupdate";

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
            } else if (msg == "create"){
                // alert(msg);
                sweetalert(msg, url);
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
    }
  }
}

function product_creation_drop_down(unique_id = "") {

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

function item_filter(){
    
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

// 	var data_type               = $('#data_type').val();
	var group_unique_id         = $('#group_unique_id').val();
	var sub_group_unique_id     = $('#sub_group_unique_id').val();
	var company_unique_id       = $('#company_unique_id').val();
	
	alert(company_unique_id);
	
	

	var filter_data = {
// 		"data_type"             : data_type,
		"group_unique_id"       : group_unique_id,
		"sub_group_unique_id"   : sub_group_unique_id,
		"company_unique_id"     : company_unique_id
	};
	
	console.info(company_unique_id);


	init_datatable(table_id, form_name, action, filter_data);


}

// function init_datatable(table_id='',form_name='',action='', filter_data ='') {

// 	var table = $("#"+table_id);
// 	var data 	  = {
// 		"action"	: action, 
// 		...filter_data
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


function product_creation_toggle(unique_id = "", new_status = 0) {
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



function get_group(group_id, type = ""){
    
    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (group_id) {
		var data = {
			"group_id" 	: group_id,
			"type" 	: type,
			"action"		: "group_name"
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
function product_creation_add_update(unique_id = "") {
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

    // if (is_form) {
    if (group_unique_id_sub && sub_group_unique_id_sub_list && category_unique_id_sub && item_unique_id_sub && qty && is_active_sub) {
        if (!isProductCUCalled) {
            product_creation_cu("", prod_unique_id);
            isProductCUCalled = true; // Mark as called
        }

        var data = $("#product_details_form").find("input, select, textarea").serialize();
        data += "&unique_id=" + unique_id + "&action=product_add_update&prod_unique_id=" + prod_unique_id;

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
                    $(".product_creation_add_update_btn").text("Error");
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

                    $(".product_creation_add_update_btn").text(unique_id && msg === "already" ? "Edit" : "Add");

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
                    $(".product_creation_add_update_btn").removeAttr("disabled","disabled");
                    $(".product_creation_add_update_btn").text("Edit");
                     $(".product_creation_add_update_btn").attr("onclick","product_creation_add_update('"+unique_id+"')");
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
  // init select2 (make sure your selects have class .select2)
  $('.select2').select2();

  // Enable Sub Group on Group change
  $("#group_unique_id_sub").on("change", function () {
    let val = $(this).val();
    resetDropdowns(["#sub_group_unique_id_sub_list", "#category_unique_id_sub", "#item_unique_id_sub"]);
    if (val) $("#sub_group_unique_id_sub_list").prop("disabled", false);
  });

  // Enable Category on Sub Group change
  $("#sub_group_unique_id_sub_list").on("change", function () {
    let val = $(this).val();
    resetDropdowns(["#category_unique_id_sub", "#item_unique_id_sub"]);
    if (val) $("#category_unique_id_sub").prop("disabled", false);
  });

  // Enable Item on Category change
  $("#category_unique_id_sub").on("change", function () {
    let val = $(this).val();
    resetDropdowns(["#item_unique_id_sub"]);
    if (val) $("#item_unique_id_sub").prop("disabled", false);
  });
});

// ✅ Reset and disable dropdowns
function resetDropdowns(selectors) {
  selectors.forEach(function (selector) {
    $(selector).val(null).trigger("change").prop("disabled", true);
  });
}

(function () {
  const ADD_VALUE = "__add_new_product_type__";

  // 1) Ensure "+ Add new..." option exists AT THE END
  function ensureAddNewOption() {
    const $sel = $("#sub_group_unique_id");
    const $existing = $sel.find(`option[value="${ADD_VALUE}"]`);
    if ($existing.length === 0) {
      $sel.append('<option value="__add_new_product_type__">+ Add new product type…</option>');
    } else {
      // move it to the end if it exists
      $existing.detach().appendTo($sel);
    }
    // Refresh Select2 rendering
    $sel.trigger("change.select2");
  }

  // Call once on page load
  $(document).ready(function () {
    ensureAddNewOption();
  });

  // 2) Intercept selecting "+ Add new..."
  $(document).on("change", "#sub_group_unique_id", function () {
    const val = $(this).val();
    if (val === ADD_VALUE) {
      // revert selection so select2 doesn't keep the add option
      $(this).val("").trigger("change.select2");

      // Respect disabled state from DOM
      if ($(this).is(":disabled")) {
        Swal.fire("Not allowed", "You cannot add a new product type while this form is locked.", "info");
        return;
      }

      openAddProductTypeModal();
    }
  });

  // 3) SweetAlert modal to capture values
  async function openAddProductTypeModal() {
    const groupId = $("#group_unique_id_display").val() || $("#group_unique_id").val();
    if (!groupId) {
      Swal.fire("Select group", "Please choose a Group Name first.", "warning");
      return;
    }

    const { value: formValues } = await Swal.fire({
      title: "Add Product Type",
      html: `
        <div class="text-left">
          <label>Product Type <span style="color:#d00">*</span></label>
          <input id="swal_ptype" class="swal2-input" placeholder="e.g., EnergyBin-X">
          <label>Product Code <span style="color:#d00">*</span></label>
          <input id="swal_pcode" class="swal2-input" placeholder="e.g., EX">
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: "Save",
      cancelButtonText: "Cancel",

      // 🔒 enforce uppercase-only for product code while typing
      didOpen: () => {
        const pcode = document.getElementById("swal_pcode");
        if (pcode) {
          pcode.setAttribute("maxlength", "6");
          pcode.setAttribute("autocomplete", "off");
          pcode.setAttribute("spellcheck", "false");
          pcode.style.textTransform = "uppercase";

          const enforceUpper = () => {
            const cur = pcode.value;
            const next = cur.toUpperCase().replace(/[^A-Z0-9]/g, "");
            if (cur !== next) {
              pcode.value = next;
              // put caret at end (simple + reliable)
              pcode.selectionStart = pcode.selectionEnd = pcode.value.length;
            }
          };
          ["input", "paste", "change"].forEach(ev => pcode.addEventListener(ev, enforceUpper));
        }
      },

      preConfirm: () => {
        const ptype = ($("#swal_ptype").val() || "").trim();
        const pcode = ($("#swal_pcode").val() || "").trim().toUpperCase();
        if (!ptype || !pcode) {
          Swal.showValidationMessage("Both fields are required.");
          return false;
        }
        if (!/^[A-Z0-9]{1,6}$/.test(pcode)) {
          Swal.showValidationMessage("Product code must be 1–6 chars (A–Z, 0–9).");
          return false;
        }
        return { ptype, pcode };
      }
    });

    if (!formValues) return;

    // 4) Save via AJAX
    saveNewProductType({
      vertical_id: groupId,
      product_type: formValues.ptype,
      product_code: formValues.pcode
    });
  }

  // 5) AJAX to backend
  function saveNewProductType(payload) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
      type: "POST",
      url: ajax_url,
      data: {
        action: "product_type_add",
        vertical_id: payload.vertical_id,
        product_type: payload.product_type,
        product_code: payload.product_code
      },
      beforeSend: function () {
        Swal.fire({
          title: "Saving...",
          allowOutsideClick: false,
          didOpen: () => Swal.showLoading()
        });
      },
     success: function (data) {
          Swal.close();
          let obj;
          try { obj = JSON.parse(data); } catch (e) {
            return Swal.fire("Error", "Invalid server response.", "error");
          }
        
          if (!obj.status) {
            Swal.fire("Error", obj.error || "Failed to add product type.", "error");
            return;
          }
        
          const newId = obj.data?.id;
          const newText = obj.data?.text || payload.product_type;
        
          const $sel = $("#sub_group_unique_id");
        
          // make sure "+ Add new…" exists and is LAST
          ensureAddNewOption();
        
          // remove any stale duplicate
          $sel.find(`option[value="${newId}"]`).remove();
        
          // ⬇️ Insert the new option RIGHT BEFORE the "+ Add new…" option
          const $add = $sel.find(`option[value="${ADD_VALUE}"]`);
          const $opt = $("<option>", { value: newId, text: newText });
          if ($add.length) {
            $opt.insertBefore($add);
          } else {
            // fallback (shouldn't happen if ensureAddNewOption ran)
            $sel.append($opt);
          }
        
          // select the new option
          $sel.val(newId).trigger("change.select2");
        
          Swal.fire("Added", "New product type has been added.", "success");
        }

    });
  }

  // If your AJAX repopulates the list, call this to ensure the add-new stays LAST
  window.afterProdTypesLoaded = function () {
    ensureAddNewOption();
  };
})();

(function () {
  const ADD_VALUE = "__add_new_product_vertical__";

  function ensureAddNewOptionGroup() {
    const $sel = $("#group_unique_id_display");
    const $existing = $sel.find(`option[value="${ADD_VALUE}"]`);
    if ($existing.length === 0) {
      $sel.append('<option value="__add_new_product_vertical__">+ Add new group…</option>');
    } else {
      $existing.detach().appendTo($sel);
    }
    $sel.trigger("change.select2");
  }

  $(document).ready(function () {
    ensureAddNewOptionGroup();
  });

  $(document).on("change", "#group_unique_id_display", function () {
    const val = $(this).val();
    if (val === ADD_VALUE) {
      $(this).val("").trigger("change.select2");
      if ($(this).is(":disabled")) {
        Swal.fire("Not allowed", "Form is locked.", "info");
        return;
      }
      openAddGroupModal();
    }
  });

  async function openAddGroupModal() {
    const { value: formValues } = await Swal.fire({
      title: "Add Group",
      html: `
        <div class="text-left">
          <label>Group Name <span style="color:#d00">*</span></label>
          <input id="swal_gname" class="swal2-input" placeholder="e.g., ELECTRONICS">
          <label>Group Code <span style="color:#d00">*</span></label>
          <input id="swal_gcode" class="swal2-input" placeholder="e.g., EL">
        </div>
      `,
      focusConfirm: false,
      showCancelButton: true,
      confirmButtonText: "Save",
      cancelButtonText: "Cancel",
      didOpen: () => {
        const gcode = document.getElementById("swal_gcode");
        if (gcode) {
          gcode.setAttribute("maxlength", "6");
          gcode.style.textTransform = "uppercase";
          gcode.addEventListener("input", () => {
            gcode.value = gcode.value.toUpperCase().replace(/[^A-Z0-9]/g, "");
          });
        }
      },
      preConfirm: () => {
        const gname = ($("#swal_gname").val() || "").trim();
        const gcode = ($("#swal_gcode").val() || "").trim().toUpperCase();
        if (!gname || !gcode) {
          Swal.showValidationMessage("Both fields required.");
          return false;
        }
        if (!/^[A-Z0-9]{1,6}$/.test(gcode)) {
          Swal.showValidationMessage("Code must be 1–6 chars (A–Z, 0–9).");
          return false;
        }
        return { gname, gcode };
      }
    });

    if (!formValues) return;

    saveNewGroup(formValues);
  }

  function saveNewGroup(payload) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    $.ajax({
      type: "POST",
      url: ajax_url,
      data: {
        action: "product_vertical_add",
        product_vertical: payload.gname,
        product_code: payload.gcode
      },
      beforeSend: function () {
        Swal.fire({ title: "Saving...", allowOutsideClick: false, didOpen: () => Swal.showLoading() });
      },
      success: function (data) {
        Swal.close();
        let obj;
        try { obj = JSON.parse(data); } catch (e) {
          return Swal.fire("Error", "Invalid response.", "error");
        }
        if (!obj.status) {
          Swal.fire("Error", obj.error || "Insert failed.", "error");
          return;
        }
        const newId = obj.data?.id;
        const newText = obj.data?.text || payload.gname;

        const $sel = $("#group_unique_id_display");
        ensureAddNewOptionGroup();
        $sel.find(`option[value="${newId}"]`).remove();

        const $add = $sel.find(`option[value="${ADD_VALUE}"]`);
        const $opt = $("<option>", { value: newId, text: newText });
        if ($add.length) {
          $opt.insertBefore($add);
        } else {
          $sel.append($opt);
        }

        $sel.val(newId).trigger("change.select2");
        $("#group_unique_id").val(newId); // hidden input update

        Swal.fire("Added", "New group has been added.", "success");
      }
    });
  }
})();
