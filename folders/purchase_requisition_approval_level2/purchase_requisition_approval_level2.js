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


var form_name   = 'purchase_requisition_lvl_2';
var table_id    = 'purchase_requisition_lvl_2_datatable';
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
	var lvl_2_status     = $('#lvl_2_status').val();

	var filter_data = {
		"pr_number": pr_number,
		"company_name": company_name,
		"project_name": project_name,
		"type_of_service": type_of_service,
		"requisition_for": requisition_for,
		"requisition_date": requisition_date,
		"status": status,
		"lvl_2_status": lvl_2_status
	};

	init_datatable(table_id, form_name, action, filter_data);

	// âœ… Reset to first page after filter
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

	// âœ… Destroy if already initialized
	if ($.fn.DataTable.isDataTable(table)) {
		table.DataTable().clear().destroy();
	}

	table.DataTable({
		ordering: true,
		searching: true,
		pageLength: 10,
		displayStart: 0, // âœ… Start from page 1
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


function open_lvl_2_modal(id) {
  var tablepop = $("#requisition_approval_modal");
  $("#approval_modal_form_lvl_2").modal("show");

  $("#approval_main_id").val(id);
  $("#bulk_status_select_lvl_2").prop("disabled", false).val("");
  $("#bulk_status_select_lvl_2 option:first").text("Select Status");

  const data = { action: "approval_modal", id: id };
  const ajax_url = sessionStorage.getItem("folder_crud_link");

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
            "683588840086c13657": "Capital",
          };
          $("#requisition_type_approval").text(
            requisitiontypeMap[json.main_data.requisition_type] || "-"
          );

          $("#company_id_approval").text(json.main_data.company_id || "-");
          $("#project_id_approval").text(json.main_data.project_id || "-");

          // ✅ Handle bulk_status_lvl_2 to set header dropdown
          if (json.main_data.bulk_status_lvl_2 === "1") {
            $("#bulk_status_select_lvl_2")
              .html("<option value='1' selected>Approved</option>")
              .prop("disabled", true);
          } else if (json.main_data.bulk_status_lvl_2 === "2") {
            $("#bulk_status_select_lvl_2")
              .html("<option value='2' selected>Rejected</option>")
              .prop("disabled", true);
          } else {
            $("#bulk_status_select_lvl_2").html(`
              <option value="">Select Status</option>
              <option value="1">Approve All</option>
              <option value="2">Reject All</option>
            `);
          }
        }

        return Array.isArray(json.data) ? json.data : [];
      },
    },
    columns: [
      { data: "s_no", title: "#" },
      {
        data: "item_code",
        title: "Item Code",
        render: function (data, type, row) {
          let displayText = data || row.item_description || "-";
          if (row.sublist && Array.isArray(row.sublist) && row.sublist.length > 0) {
            return `<span class="item-code-toggle"
                        style="cursor:pointer; color:#e96f26;"
                        data-sno="${row.s_no}">
                        ${displayText}
                    </span>`;
          }
          return `<span>${displayText}</span>`;
        },
      },
      { data: "item_description", title: "Description" },
      { data: "pr_qty", title: "PR Qty" },
      { data: "l1_qty", title: "L1 Qty" },
      { data: "lvl_2_quantity", title: "L2 Qty" },
      { data: "uom", title: "UOM" },
      { data: "item_remarks", title: "Item Remarks" },
      { data: "required_delivery_date", title: "Delivery Date" },
      { data: "lvl_2_status", title: "Status" },
      { data: "lvl_2_reason", title: "Rejected Reason" },
    ],
  });

  // 🔁 Sublist toggle behavior
  tablepop.off("click", "span.item-code-toggle").on("click", "span.item-code-toggle", function () {
    const tr = $(this).closest("tr");
    const row = datatable.row(tr);

    if (row.child.isShown()) {
      row.child.hide();
      tr.removeClass("shown");
    } else {
      const rowData = row.data();
      const sublist = rowData.sublist || [];
      const parentSno = $(this).data("sno");

      if (sublist.length > 0) {
        let childHtml = `
          <table class='table table-bordered table-sm sublist-table'>
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

        const parentQty = parseFloat(rowData.l1_qty) || 0;
        sublist.forEach((sub, index) => {
          const childQty = parseFloat(sub.qty) || 0;
          const totalQty = parentQty * childQty;
          childHtml += `
            <tr>
              <td>${parentSno}.${index + 1}</td>
              <td>${sub.item}</td>
              <td class="child-qty">${totalQty}</td>
              <td>${sub.uom}</td>
              <td>${sub.remarks || "-"}</td>
            </tr>
          `;
        });

        childHtml += "</tbody></table>";
        row.child(childHtml).show();
        tr.addClass("shown");

        // Update child qty dynamically
        $(document)
          .off("input.updateChildQty")
          .on("input.updateChildQty", "input[id^='quantity_']", function () {
            const newParentQty = parseFloat($(this).val()) || 0;
            const childTable = tr.next("tr").find(".sublist-table tbody");
            childTable.find("tr").each(function (idx) {
              const baseQty = parseFloat(sublist[idx].qty) || 0;
              $(this).find(".child-qty").text((newParentQty * baseQty).toFixed(2));
            });
          });
      }
    }
  });
}


// ✅ Bulk status handler for Level 2
// ✅ Bulk status handler for Level 2
$(document).on("change", "#bulk_status_select_lvl_2", function () {
  const selectedValue = $(this).val();
  const main_unique_id = $("#approval_main_id").val();
  const ajax_url = sessionStorage.getItem("folder_crud_link");

  if (!selectedValue) return;
  if (!main_unique_id) {
    sweetalert("No requisition selected.", "error");
    return;
  }

  const actionText =
    selectedValue === "1" ? "approve all items" : "reject all items";

  // 🔴 Reject Case (with reason input)
  if (selectedValue === "2") {
    // ✅ Hide the correct modal (Level 2)
    $("#approval_modal_form_lvl_2").modal("hide");
    $("#custom-reject-overlay-lvl2").remove();

    const overlay = $(`
      <div id="custom-reject-overlay-lvl2" style="
        position: fixed;
        top: 0; left: 0;
        width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.45);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 20000;
        backdrop-filter: blur(2px);">
        <div style="
          background: #fff;
          width: 420px;
          border-radius: 12px;
          box-shadow: 0 8px 25px rgba(0,0,0,0.25);
          padding: 25px 30px;
          text-align: center;
          animation: fadeIn 0.25s ease;
          font-family: 'Segoe UI', sans-serif;">
          <h4 style="margin-bottom: 10px; color: #d33;">Reject All Items (Level 2)</h4>
          <p style="font-size: 14px; color: #555;">Please provide the reason for rejection:</p>
          <textarea id="reject-reason-lvl2" placeholder="Enter rejection reason..."
            style="width: 100%; height: 100px; margin-top: 10px; padding: 8px;
                   border: 1px solid #ccc; border-radius: 6px; resize: none;
                   font-size: 13px; outline: none;"></textarea>
          <div style="margin-top: 22px;">
            <button id="confirm-reject-lvl2" style="background: #d33; color: white; border: none; padding: 8px 20px;
              border-radius: 5px; margin-right: 10px; cursor: pointer; font-size: 13px;">Reject All</button>
            <button id="cancel-reject-lvl2" style="background: #ccc; color: #333; border: none; padding: 8px 20px;
              border-radius: 5px; cursor: pointer; font-size: 13px;">Cancel</button>
          </div>
        </div>
      </div>
    `);

    $("body").append(overlay);
    $("#reject-reason-lvl2").focus(); // ✅ immediately focus textbox

    // Cancel button
    $("#cancel-reject-lvl2").on("click", function () {
      $("#custom-reject-overlay-lvl2").fadeOut(200, function () {
        $(this).remove();
        $("#approval_modal_form_lvl_2").modal("show"); // ✅ Restore modal
      });
      $("#bulk_status_select_lvl_2").val("");
    });

    // Confirm button
    $("#confirm-reject-lvl2").on("click", function () {
      const reason = $("#reject-reason-lvl2").val().trim();
      if (!reason) {
        $("#reject-reason-lvl2").css("border", "1px solid #d33").focus();
        return;
      }

      $("#custom-reject-overlay-lvl2").fadeOut(200, function () {
        $(this).remove();
        $("#approval_modal_form_lvl_2").modal("show");
      });

      $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
          action: "bulk_update_status_lvl_2",
          main_unique_id: main_unique_id,
          selectedValue: selectedValue,
          reason: reason,
        },
        success: handleBulkResponse,
        error: function () {
          sweetalert("Network error occurred!", "error");
        },
      });
    });

    return; // stop execution
  }

  // 🟢 Approve Case (simple confirm)
  $("#approval_modal_form_lvl_2").modal("hide");

  const overlay = $(`
    <div id="custom-approve-overlay-lvl2" style="
      position: fixed; top: 0; left: 0; width: 100vw; height: 100vh;
      background: rgba(0,0,0,0.45); display: flex; justify-content: center; align-items: center;
      z-index: 20000; backdrop-filter: blur(2px);">
      <div style="background: #fff; width: 380px; border-radius: 12px;
        box-shadow: 0 8px 25px rgba(0,0,0,0.25); padding: 25px 30px; text-align: center;
        animation: fadeIn 0.25s ease; font-family: 'Segoe UI', sans-serif;">
        <h4 style="margin-bottom: 15px; color: #3085d6;">Confirm Level 2 Bulk Action</h4>
        <p style="font-size: 14px; color: #555;">Are you sure you want to ${actionText}?</p>
        <div style="margin-top: 22px;">
          <button id="confirm-approve-lvl2" style="background: #3085d6; color: white; border: none; padding: 8px 20px;
            border-radius: 5px; margin-right: 10px; cursor: pointer; font-size: 13px;">Yes, Proceed</button>
          <button id="cancel-approve-lvl2" style="background: #ccc; color: #333; border: none; padding: 8px 20px;
            border-radius: 5px; cursor: pointer; font-size: 13px;">Cancel</button>
        </div>
      </div>
    </div>
  `);

  $("body").append(overlay);

  $("#cancel-approve-lvl2").on("click", function () {
    $("#custom-approve-overlay-lvl2").fadeOut(200, function () {
      $(this).remove();
      $("#approval_modal_form_lvl_2").modal("show");
    });
    $("#bulk_status_select_lvl_2").val("");
  });

  $("#confirm-approve-lvl2").on("click", function () {
    $("#custom-approve-overlay-lvl2").fadeOut(200, function () {
      $(this).remove();
      $("#approval_modal_form_lvl_2").modal("show");
    });

    $.ajax({
      type: "POST",
      url: ajax_url,
      data: {
        action: "bulk_update_status_lvl_2",
        main_unique_id: main_unique_id,
        selectedValue: selectedValue,
      },
      success: handleBulkResponse,
      error: function () {
        sweetalert("Network error occurred!", "error");
      },
    });
  });

  // 🧠 Common success handler
  function handleBulkResponse(response) {
    try {
      const res = JSON.parse(response);
      if (res.status) {
        $("#requisition_approval_modal").DataTable().ajax.reload(null, false);
        $("#purchase_requisition_lvl_2_datatable").DataTable().ajax.reload(null, false);

        const statusText =
          selectedValue === "1"
            ? "<span style='color: green; font-weight: bold;'>Approved (Level 2)</span>"
            : "<span style='color: red; font-weight: bold;'>Rejected (Level 2)</span>";

        $("#requisition_approval_modal")
          .find("select.status-select-lvl2")
          .each(function () {
            $(this).replaceWith(statusText);
          });

        sweetalert(
          "All items successfully " +
            (selectedValue === "1"
              ? "approved (Level 2)!"
              : "rejected (Level 2)!"),
          "success"
        );

        const newLabel = selectedValue === "1" ? "Approved" : "Rejected";
        $("#bulk_status_select_lvl_2")
          .html(`<option value="${selectedValue}" selected>${newLabel}</option>`)
          .prop("disabled", true);
      } else {
        sweetalert("Error: " + res.error, "error");
      }
    } catch (e) {
      sweetalert("Invalid server response!", "error");
    }
  }
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
            "action"        : "handle_lvl_2_status"
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
                        
                        $('#purchase_requisition_lvl_2_datatable').DataTable().ajax.reload(null, false);
                        
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
function purchase_requisition_approval_level2_upload(unique_id){
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
