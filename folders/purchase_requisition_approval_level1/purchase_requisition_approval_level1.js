$(document).ready(function () {
    let main_id = $("#unique_id").val();
    if (main_id) {
        purchase_sublist_datatable("purchase_sublist_datatable");
    }

    $('.select2').select2();

    toggleSalesOrderField();
    $('#requisition_for').on('change', toggleSalesOrderField);

    $("#item_code").on("change", function () {
        const item_code = $(this).val();
        if (item_code) {
            get_item_details(item_code);
        } else {
            $("#item_description").val("");
            $("#uom").val("");
            $("#uom_id").val("");
        }
    });

    init_datatable(table_id, form_name, action);
});


$("#requisition_for, #requisition_type").on("change", function () {
    const requisitionFor = $("#requisition_for").val();
    const requisitionType = $("#requisition_type").val();

    const isSO = requisitionFor === "2";
    const isService = requisitionType === "683568ca2fe8263239";

    // if (isSO && !isService) {
    //     $("#requisition_for").val("1").trigger("change");
    //     Swal.fire("SO is only allowed when Requisition Type is Service.");
    //     return;
    // }

    if (isSO && isService) {
        $("#sales_order_id").prop("disabled", false);
        $("#sales_order_id").trigger("change");
    } else {
        $("#sales_order_id").prop("disabled", true).val("").trigger("change");

        if (requisitionType !== "") {
            let groupToSend = requisitionType;
        
            if (requisitionType === "683588840086c13657") {
                groupToSend = "all";
            }
        
            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "get_items_by_group",
                    group_id: groupToSend
                },
                success: function (res) {
                    $("#item_code").html(res).trigger("change");
                }
            });
        }
        else {
            $("#item_code").html("<option value=''>Select the Item/Code</option>").trigger("change");
        }
    }
});


$("#sales_order_id").on("change", function () {
    const requisitionType = $("#requisition_type").val();
    const requisitionFor = $("#requisition_for").val();
    const salesOrderId = $(this).val();

    if (requisitionFor === "2" && requisitionType === "683568ca2fe8263239") {
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "get_items_by_sales_order",
                sales_order_id: salesOrderId
            },
            success: function (res) {
                $("#item_code").html(res).trigger("change");
            }
        });
    }
});


var form_name   = 'purchase_requisition';
var table_id    = 'purchase_requisition_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");


function item_filter() {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var pr_number        = $('#pr_number').val();
	var company_name     = $('#company_name').val();
	var project_name     = $('#project_name').val();
	var type_of_service  = $('#type_of_service').val();
	var requisition_for  = $('#requisition_for').val();
	var requisition_date = $('#requisition_date').val();
	var status           = $('#item_status').val(); // corrected

	var filter_data = {
		"pr_number": pr_number,
		"company_name": company_name,
		"project_name": project_name,
		"type_of_service": type_of_service,
		"requisition_for": requisition_for,
		"requisition_date": requisition_date,
		"status": status
	};

	init_datatable(table_id, form_name, action, filter_data);

	// ✅ Reset to first page after applying filter
	setTimeout(() => {
		$('#' + table_id).DataTable().page(0).draw(false);
	}, 300);
}



function init_datatable(table_id = '', form_name = '', action = '', filter_data = {}) {
	var table = $("#" + table_id);
	var data = {
		"action": action,
		...filter_data
	};

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	// ✅ Destroy previous instance if exists
	if ($.fn.DataTable.isDataTable(table)) {
		table.DataTable().clear().destroy();
	}

	table.DataTable({
		ordering: true,
		searching: true,
		pageLength: 10,
		displayStart: 0, // ✅ Force start from first page
		ajax: {
			url: ajax_url,
			type: "POST",
			data: data
		}
	});
}


function toggleSalesOrderField() {
    var requisitionFor = $('#requisition_for').val();
    if (requisitionFor === '2') {
        $('#sales_order_id').prop('disabled', false);
    } else {
        $('#sales_order_id').prop('disabled', true).val('').trigger('change');
    }
}


function purchase_sublist_datatable(table_id = "purchase_sublist_datatable") {
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
                action: "purchase_sublist_datatable",
                main_unique_id: main_unique_id
            }
        }
    });
}

function open_modal(id) {
  var tablepop = $("#requisition_approval_modal");
  $("#approval_modal_form").modal("show");

  // ✅ reset dropdown state first
  $("#bulk_status_select").prop("disabled", false).val("");
  $("#bulk_status_select option:first").text("Select Status");

  $("#approval_main_id").val(id);

  var data = { action: "approval_modal", id: id };
  var ajax_url = sessionStorage.getItem("folder_crud_link");

  var datatable = tablepop.DataTable({
    destroy: true,
    ajax: {
      url: ajax_url,
      type: "POST",
      data: data,
      dataSrc: function (json) {
        if (json.main_data) {
          $("#pr_number_approval").text(json.main_data.pr_number || "-");
          $("#date_approval").text(json.main_data.date || "-");
          $("#requisition_date_approval").text(json.main_data.requisition_date || "-");

          const requisitionForMap = { "1": "Direct", "2": "SO", "3": "Ordered BOM" };
          $("#requisition_for_approval").text(requisitionForMap[json.main_data.requisition_for] || "-");

          const requisitiontypeMap = {
            "1": "Regular",
            "683568ca2fe8263239": "Service",
            "683588840086c13657": "Capital"
          };
          $("#requisition_type_approval").text(requisitiontypeMap[json.main_data.requisition_type] || "-");

          $("#company_id_approval").text(json.main_data.company_id || "-");
          $("#project_id_approval").text(json.main_data.project_id || "-");

          // ✅ Handle bulk_status value from PHP
          if (json.main_data.bulk_status === "1") {
            $("#bulk_status_select")
              .html("<option value='1' selected>Approved</option>")
              .prop("disabled", true);
          } else if (json.main_data.bulk_status === "2") {
            $("#bulk_status_select")
              .html("<option value='2' selected>Rejected</option>")
              .prop("disabled", true);
          } else {
            // Not finalized yet
            $("#bulk_status_select").html(`
              <option value="">Select Status</option>
              <option value="1">Approve All</option>
              <option value="2">Reject All</option>
            `);
          }
        }
        return json.data;
      },
    },
    columns: [
      { data: "s_no", title: "#" },
      {
        data: "item_code",
        title: "Item Code",
        render: function (data, type, row) {
          let displayText = (data && data !== "null") ? data : (row.item_description || "-");

          if (row.sublist && Array.isArray(row.sublist) && row.sublist.length > 0) {
            return `<span class="item-code-toggle" 
                        style="cursor:pointer; color:#e96f26;" 
                        data-sno="${row.s_no}">
                        ${displayText}
                    </span>`;
          } else {
            return `<span>${displayText}</span>`;
          }
        }
      },
      { data: "item_description", title: "Description", defaultContent: "-" },
      { data: "pr_qty", title: "PR Qty", defaultContent: "-" },
      { data: "quantity", title: "Qty", defaultContent: "-" },
      { data: "uom", title: "UOM", defaultContent: "-" },
      { data: "item_remarks", title: "Item Remarks", defaultContent: "-" },
      { data: "required_delivery_date", title: "Delivery Date", defaultContent: "-" },
      { data: "status", title: "Status", defaultContent: "-" },
      { data: "reason", title: "Cancelled Reason", defaultContent: "-" }
    ]
  });

  // 🔁 Toggle child sublist
  tablepop.off("click", "span.item-code-toggle").on("click", "span.item-code-toggle", function () {
    var tr = $(this).closest("tr");
    var row = datatable.row(tr);

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass("shown");
    } else {
      let rowData = row.data();
      let sublist = Array.isArray(rowData.sublist) ? rowData.sublist : [];
      let parentSno = $(this).data("sno");

      if (sublist.length > 0) {
        let childHtml = `
          <table class='table table-bordered table-sm child-table' data-parent-id="${rowData.unique_id}">
            <thead>
              <tr>
                <th>S.No</th>
                <th>Item</th>
                <th>Qty</th>
                <th>UOM</th>
                <th>Remarks</th>
              </tr>
            </thead>
            <tbody>
        `;

        let quantityInputHtml = rowData.quantity;
        let parentQty = parseFloat($(quantityInputHtml).val()) || 0;

        sublist.forEach(function (sub, index) {
          let childQty = parseFloat(sub.qty) || 0;
          let totalQty = parentQty * childQty;

          childHtml += `
            <tr>
              <td>${parentSno}.${index + 1}</td>
              <td>${sub.item}</td>
              <td class="child-qty" data-child-qty="${childQty}">${totalQty}</td>
              <td>${sub.uom}</td>
              <td>${sub.remarks || "-"}</td>
            </tr>
          `;
        });

        childHtml += "</tbody></table>";
        row.child(childHtml).show();
        tr.addClass("shown");

        const qtyInput = $(`#quantity_${rowData.unique_id}`);
        qtyInput.off("input.updateChildQty").on("input.updateChildQty", function () {
          let newParentQty = parseFloat($(this).val()) || 0;
          row.child().find("td.child-qty").each(function () {
            let baseChildQty = parseFloat($(this).data("child-qty")) || 0;
            $(this).text((newParentQty * baseChildQty).toFixed(2));
          });
        });
      }
    }
  });
}


// 🔄 Bulk status dropdown action
$(document).on("change", "#bulk_status_select", function () {
  const selectedValue = $(this).val();
  const main_unique_id = $("#approval_main_id").val();
  const ajax_url = sessionStorage.getItem("folder_crud_link");

  if (!selectedValue) return;
  if (!main_unique_id) {
    sweetalert("No requisition selected.", "error");
    return;
  }

  const actionText = selectedValue === "1" ? "approve all items" : "reject all items";

  Swal.fire({
    title: "Confirm Bulk Action",
    text: `Are you sure you want to ${actionText}?`,
    icon: "warning",
    showCancelButton: true,
    confirmButtonColor: "#3085d6",
    cancelButtonColor: "#d33",
    confirmButtonText: "Yes, proceed!"
  }).then((result) => {
    if (result.isConfirmed) {
      $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
          action: "bulk_update_status",
          main_unique_id: main_unique_id,
          selectedValue: selectedValue
        },
        success: function (response) {
          try {
            var res = JSON.parse(response);
            if (res.status) {
              $('#requisition_approval_modal').DataTable().ajax.reload(null, false);
              $('#purchase_requisition_datatable').DataTable().ajax.reload(null, false);

              const statusText = selectedValue === "1"
                ? "<span style='color: green; font-weight: bold;'>Approved</span>"
                : "<span style='color: red; font-weight: bold;'>Rejected</span>";

              $('#requisition_approval_modal')
                .find('select.status-select')
                .each(function () {
                  $(this).replaceWith(statusText);
                });

              sweetalert(
                "All items successfully " +
                  (selectedValue === "1" ? "approved!" : "rejected!"),
                "success"
              );

              // ✅ Reflect in dropdown header
              const newLabel = selectedValue === "1" ? "Approved" : "Rejected";
              $("#bulk_status_select")
                .html(`<option value="${selectedValue}" selected>${newLabel}</option>`)
                .prop("disabled", true);

            } else {
              sweetalert("Error: " + res.error, "error");
            }
          } catch (e) {
            sweetalert("Invalid server response!", "error");
          }
        },
        error: function () {
          sweetalert("Network error occurred!", "error");
        }
      });
    } else {
      $("#bulk_status_select").val("");
    }
  });
});




function handle_status(selectedValue, unique_id, cancelReason = "") {
    let internet_status = is_online();
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    if (selectedValue === "2" && cancelReason === "") {
        document.getElementById(`cancelReasonDiv_${unique_id}`).style.display = 'block';
        return;
    } else {
        document.getElementById(`cancelReasonDiv_${unique_id}`).style.display = 'none';
    }


    let new_quantity = document.getElementById(`quantity_${unique_id}`).value;

   
    if ((selectedValue === "2" && cancelReason !== "") || (selectedValue === "1")) {
        var data = {
            "selectedValue" : selectedValue,
            "unique_id"     : unique_id,
            "cancelReason"  : cancelReason,
            "quantity"      : new_quantity,
            "action"        : "handle_status"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            
            success: function (response) {
                try {
                    var res = JSON.parse(response);
                    if (res.status) {
                        
                        $('#requisition_approval_modal').DataTable().ajax.reload(null, false);
                        
                        $('#purchase_requisition_datatable').DataTable().ajax.reload(null, false);
                        
                        sweetalert("Status & Quantity updated successfully!", "success");
                    } else {
                        sweetalert("Error: " + res.error, "error");
                    }
                } catch (e) {
                    sweetalert("Invalid server response!", "error");
                }
            },
            error: function () {
                sweetalert("AJAX error occurred!", "error");
            }

        });
    }
}
function purchase_requisition_approval_level1_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#grnUploadModal').modal('show');
    
    sub_list_datatable("documents_datatable");
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

        // Check file types before appending
        if (image_s && image_s.files.length > 0) {
            for (var i = 0; i < image_s.files.length; i++) {
                let file = image_s.files[i];
                if (!allowedTypes.includes(file.type)) {
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
                text: 'Only images and PDF files are allowed.',
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


$(document).ready(function () {
  $(".close").click(function () {
    $("#approval_modal_form").modal("hide");
  });
});
