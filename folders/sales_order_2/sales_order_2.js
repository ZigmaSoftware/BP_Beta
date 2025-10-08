
$(document).ready(function () {
    let main_id = $("#unique_id").val();
    if (main_id) {
        so_sublist_datatable("so_sublist_datatable");
    }
    
    // ✅ Function to update heading + reload item options
    function updateTypeHeadAndOptions() {
        let selected = $("#so_type").val();
    
        if (selected === "1") {
            $("#type_head").text("Product Name");
        }else if (selected === "2") {
            $("#type_head").text("Project Name");
        }else if (selected === "3") {
            $("#type_head").text("Item Name");
        } else {
            $("#type_head").text("Product & Item Names");
        }
    
        // ✅ Fetch item options
        item_options();
    }
    
    // ✅ Trigger on change
    $("#so_type").on("change", updateTypeHeadAndOptions);
    
    updateTypeHeadAndOptions();


    // toggleSalesOrderField();
});
// $(document).ready(function () {
//     // Initialize Select2 if used
//     $('.select2').select2();

//     // Trigger on page load
//     toggleSalesOrderField();

//     // Bind change event
//     $('#requisition_for').on('change', toggleSalesOrderField);
// });
$(document).ready(function () {
    sales_order_filter();
});


var form_name   = 'sales_order';
var table_id    = 'sales_order_datatable';
var action      = 'datatable';

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

// function sales_order_2_cu(unique_id = "") {
//     let internet_status = is_online();
//     if (!internet_status) {
//         sweetalert("no_internet");
//         return false;
//     }

//     let is_form = form_validity_check("was-validated");
//     if (!is_form) {
//         sweetalert("form_alert");
//         return false;
//     }

//     // ✅ CHECK: If sublist is empty, stop
//     let rowCount = $("#so_sublist_datatable tbody tr").length;
//     if (rowCount === 0 || $("#so_sublist_datatable tbody").text().includes("No data available")) {
//         Swal.fire("Please add at least one item in the sublist before saving.");
//         return false;
//     }

//     let data = new FormData($("#purchase_requisition_form")[0]);
//     data.append("action", "createupdate");
//     data.append("unique_id", unique_id);

//     $.ajax({
//         type: "POST",
//         url: ajax_url,
//         data: data,
//         cache: false,
//         contentType: false,
//         processData: false,
//         beforeSend: function () {
//             $(".createupdate_btn").attr("disabled", "disabled").text("Processing...");
//         },
//         success: function (data) {
//             let obj = JSON.parse(data);
//             let msg = obj.msg;
//             let status = obj.status;
//             let error = obj.error;

//             if (!status) {
//                 $(".createupdate_btn").text("Error");
//                 console.log(error);
//             } else {
//                 sweetalert(msg, url);
//             }
//         },
//         error: function () {
//             alert("Network Error");
//         },
//         complete: function () {
//             $(".createupdate_btn").removeAttr("disabled").text("Save");
//         }
//     });
// }

function sales_order_2_cu(unique_id = "") {
    let internet_status = is_online();
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    let is_form = form_validity_check("was-validated");
    if (!is_form) {
        sweetalert("form_alert");
        return false;
    }

    // ✅ CHECK: If sublist is empty, stop
    let rowCount = $("#so_sublist_datatable tbody tr").length;
    if (rowCount === 0 || $("#so_sublist_datatable tbody").text().includes("No data available")) {
        Swal.fire("Please add at least one item in the sublist before saving.");
        return false;
    }

    // ✅ Temporarily enable #so_type so its value is included
    let soTypeField = $("#so_type");
    let wasDisabled = soTypeField.prop("disabled");  // store current state
    soTypeField.prop("disabled", false);

    // ✅ Now fetch form data
    let data = new FormData($("#purchase_requisition_form")[0]);
    data.append("action", "createupdate");
    data.append("unique_id", unique_id);

    // ✅ Restore original state (disable again if it was disabled before)
    if (wasDisabled) {
        soTypeField.prop("disabled", true);
    }

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
            let obj = JSON.parse(data);
            let msg = obj.msg;
            let status = obj.status;
            let error = obj.error;

            if (!status) {
                $(".createupdate_btn").text("Error");
                console.log(error);
            } else {
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

function item_options(){
    let so_type = $("#so_type").val();
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            "so_type": so_type, 
            "action": "item_options"
        },
        success: function(data){
            $("#product_unique_id").html(data);
        },
        error: function(xhr, status, error){
            console.error("AJAX Error:", status, error);
            Swal.fire("Error", "Failed to fetch item options. Please try again.", "error");
        }
    });
}


function init_datatable(table_id='',form_name='',action='', filter_data ='') {
var from_date       = $("#from_date").val();
var to_date         = $("#to_date").val();
var company_name    = $("#company_name").val();
var customer_name   = $("#customer_name").val();
var status          = $("#status_fill").val();

    
	var table = $("#"+table_id);
	var data 	  = {
		"action"	    : action, 
		"from_date"	    : from_date, 
		"to_date"	    : to_date, 
		"company_name"	: company_name, 
		"customer_name"	: customer_name, 
		"status"	    : status
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
	    responsive  : true, 
		ordering    : true,
		searching   : true,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}


// filter 
function sales_order_filter() {
    init_datatable(table_id, form_name, action);
}


// Delete Function
function sales_order_2_delete(unique_id = "") {
    Swal.fire({
        title: 'Are you sure?',
        html: `
            <textarea id="delete_remarks_input" class="swal2-textarea" 
                placeholder="Enter delete remarks..." rows="5" 
                style="width:100%; resize: vertical;"></textarea>
        `,
        showCancelButton: true,
        confirmButtonText: 'Delete',
        focusConfirm: false,
        preConfirm: () => {
            const remarks = document.getElementById('delete_remarks_input').value.trim();
            if (!remarks) {
                Swal.showValidationMessage('Remarks are required for delete');
                return false;
            }
            return remarks;
        },
        didOpen: () => {
            const textarea = document.getElementById('delete_remarks_input');
            textarea.focus();
            textarea.addEventListener('keydown', function (e) {
                if (e.ctrlKey && e.key === 'Enter') {
                    Swal.clickConfirm();
                }
            });
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const remarks = result.value;

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    "unique_id": unique_id,
                    "action": "delete",
                    "remarks": remarks
                },
                success: function (data) {
                    var obj = JSON.parse(data);
                    var msg = obj.msg;
                    var status = obj.status;

                    if (status) {
                        init_datatable(table_id, form_name, action);
                    }
                    sweetalert(msg, url);
                }
            });
        }
    });
}



function sale_order_sublist_add_update() {
    let main_unique_id = $("#unique_id").val();
    let sublist_id = $("#sublist_unique_id").val();
    
    if (!main_unique_id) {
        Swal.fire("Please save the main form before adding items.");
        return;
    }

    var product_unique_id       = $('#product_unique_id').val();
    var uom                     = $('#uom').val();
    var qty                     = $('#qty').val();
    var rate                    = $('#rate').val();
    var tax                     = $('#tax').val();
    var amount                  = $('#amount').val();
    var subtask                  = $('#subtask').val();

    if (!product_unique_id || !qty || !uom || !rate || !tax || !amount) {
        Swal.fire("Please fill all required sublist fields.");
        return;
    }

    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "so_sub_add_update",
            main_unique_id: main_unique_id,
            sublist_unique_id: sublist_id,
            product_unique_id,
            qty,
            uom,
            rate,
            tax,
            amount,
            subtask
        },
        success: function (res) {
            let obj = JSON.parse(res);

            if (obj.status) {
                Swal.fire(obj.msg === "update" ? "Item updated" : "Item added");
                
                reset_sublist_form();
                    $("#so_type").prop("disabled", true);

                so_sublist_datatable("so_sublist_datatable");
            } else {
                Swal.fire("Error", obj.error || "Operation failed", "error");
            }
        },
        error: function () {
            alert("Network error");
        }
    });
}

function reset_sublist_form() {
    $("#sublist_unique_id").val("");
    $("#qty, #rate, #amount, #subtask").val("");
    resetDropdowns(["#product_unique_id", "#uom", "#tax"]);
}




function so_sublist_datatable(table_id = "so_sublist_datatable") {
    let main_unique_id = $("#unique_id").val();
    let ajax_url = sessionStorage.getItem("folder_crud_link");
    let so_type  = $("#so_type").val();
    console.info(so_type);

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
                action: "so_sublist_datatable",
                main_unique_id: main_unique_id,
                so_type: so_type
            }
        },
        initComplete: function(settings, json) {
            // ✅ If there are sublist rows, disable input fields
            if (json.data.length > 0 && so_type === '1') {
                disable_sublist_inputs();
            }
        }
    });
}


function disable_sublist_inputs() {
    $("#product_unique_id").prop("disabled", true);
    $("#uom").prop("disabled", true);
    $("#qty").prop("disabled", true);
    $("#rate").prop("disabled", true);
    $("#tax").prop("disabled", true);
    $("#amount").prop("disabled", true);
    $("#subtask").prop("disabled", true);
    $(".sale_order_add_update_btn").prop("disabled", true);
}


function so_sub_edit(unique_id) {
    let ajax_url = sessionStorage.getItem("folder_crud_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: { action: "so_sub_edit", unique_id },
        success: function (res) {
            let d = JSON.parse(res).data;
            
            "item_name_id",
            "unit_name",
            "quantity",
            "rate",
            "tax_id",
            "amount",
            "subtask",
            "unique_id"
            $("#qty, #rate, #amount").val("");
            $("#product_unique_id").val(null).trigger("change");
            $("#uom").val(null).trigger("change");
            $("#tax").val(null).trigger("change");

            $("#sublist_unique_id").val(d.unique_id);
            $("#product_unique_id").val(d.item_name_id).trigger("change");
            $("#uom").val(d.unit_name).trigger("change");
            $("#qty").val(d.quantity);
            $("#rate").val(d.rate);
            $("#amount").val(d.amount);
            $("#subtask").val(d.subtask);
            $("#tax").val(d.tax_id).trigger("change");
            $(".sale_order_add_update_btn").text("Edit");

            // ✅ ENABLE fields after setting values
            $(".sale_order_add_update_btn").prop("disabled", false);
            $("#product_unique_id").prop("disabled", false);
            $("#uom").prop("disabled", false);
            $("#qty").prop("disabled", false);
            $("#rate").prop("disabled", false);
            $("#tax").prop("disabled", false);
            $("#amount").prop("disabled", false);
            $("#subtask").prop("disabled", false);
    
        }
    });
}


function so_sub_delete(unique_id) {
    Swal.fire({
        title: "Are you sure?",
        text: "This item will be deleted",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes, delete it!"
    }).then((result) => {
        if (result.isConfirmed) {
            let ajax_url = sessionStorage.getItem("folder_crud_link");

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "so_sub_delete",
                    unique_id
                },
                success: function (res) {
                    let obj = JSON.parse(res);
                    Swal.fire(obj.msg);

                    // ✅ Refresh sublist
                    so_sublist_datatable("so_sublist_datatable");

                    // ✅ Enable sublist input fields
                    $("#product_unique_id").prop("disabled", false).val(null).trigger("change");
                    $("#uom").prop("disabled", false).val(null).trigger("change");
                    $("#tax").prop("disabled", false).val(null).trigger("change");

                    $("#qty, #rate, #amount, #subtask").prop("disabled", false).val("");
                    $(".sale_order_add_update_btn").prop("disabled", false).text("Add");

                    // ✅ Reset hidden sublist ID
                    $("#sublist_unique_id").val("");
                }
            });
        }
    });
}


function number_check(no = 0) {

	if ((isNaN(no)) || (no == undefined) || (no == "")) {

		return 0;
	}

	return no;


}
function sub_total_amount_2(tax_value="") {
    var qty_value = parseFloat($("#qty").val()) || 0;
    var rate_value = parseFloat($("#rate").val()) || 0;


    var total_amount = qty_value * rate_value;


    // Apply tax
    if (tax_value) {
        var tax = tax_value / 100;
        var tax_amount = total_amount * tax;
        total_amount += tax_amount;
    }

    $("#amount").val(total_amount.toFixed(2));
}

function get_tax_val(code){

    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (code) {
		var data = {
			"code" 	: code,
			"action": "get_tax_val",
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			dataType: 'json',
			success : function(data) {
				if (data.status === 'success' && data.data) {
                    sub_total_amount_2(data.data);
                }
			}
		});
	}
	
}

// Reset and disable dropdowns
function resetDropdowns(selectors) {
    let so_type   = $("#so_type").val();
    console.info(so_type);
    selectors.forEach(function (selector) {
        if(so_type === '1'){
           $(selector).val(null).trigger("change").prop("disabled", true); 
        } else {
            $(selector).val(null).trigger("change");
        }
    });
}

function dateValidation() {
    const fromDateInput = document.getElementById('from_date');
    const toDateInput = document.getElementById('to_date');

    const fromDate = new Date(fromDateInput.value);
    const toDate = new Date(toDateInput.value);
    const currentDate = new Date();

    // Prevent future date for From Date
    if (fromDate > currentDate) {
        const year = currentDate.getFullYear();
        const month = String(currentDate.getMonth() + 1).padStart(2, '0');
        const day = String(currentDate.getDate()).padStart(2, '0');
        fromDateInput.value = `${year}-${month}-${day}`;
    }

    // Ensure To Date is not before From Date
    if (toDate < fromDate) {
        // Set To Date equal to From Date
        toDateInput.value = fromDateInput.value;
    }
}

function sales_order_2_upload(unique_id){
    // Set the hidden unique_id in the modal form
    document.getElementById('upload_unique_id').value = unique_id;

    // Show the modal (Bootstrap 4 or 5)
    $('#soUploadModal').modal('show');
    
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

// Trigger SweetAlert when selecting file if > 5MB
$(document).on("change", "#test_file_qual", function () {
    let files = this.files;
    if (files.length > 0) {
        for (let i = 0; i < files.length; i++) {
            if (files[i].size > 5 * 1024 * 1024) { // 5 MB
                Swal.fire({
                    icon: 'error',
                    title: 'File Too Large',
                    text: 'Each file must be less than 5 MB.',
                    confirmButtonColor: '#3bafda'
                });
                // Clear the invalid file immediately
                $(this).val("");
                break;
            }
        }
    }
});
