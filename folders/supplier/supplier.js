var account_details_tableid                 = "account_details_datatable";
var branch_details_tableid                  = "branch_details_datatable";
var contact_person_tableid                  = "contact_person_datatable";
var shipping_details_tableid                = "shipping_details_datatable";
var billing_details_tableid                 = "billing_details_datatable";
var documents_tableid                       = "documents_datatable";

var form_name 		                        = 'supplier';
var form_header		                        = '';
var form_footer 	                        = '';
var table_name 		                        = '';
var table_id 		                        = 'supplier_datatable';
var action 			                        = "datatable";

let ajax_url = sessionStorage.getItem("folder_crud_link");
let url      = sessionStorage.getItem("list_link");

document.querySelectorAll("button[type='button']").forEach(btn => {
    if (btn.innerText.trim() === "Cancel") {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            const confirmCancel = confirm("Are you sure you want to cancel?");
            if (!confirmCancel) return;

            const supplierId = document.getElementById("supplier_unique_id")?.value;
            const uniqueId = document.getElementById("unique_id")?.value;

            if (!supplierId || uniqueId) {
                console.log("No supplier ID found. Nothing to cancel.");
                window.location.href = url;
                return;
            }

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "supplier_master_delete",
                    supplier_unique_id: supplierId
                },
                beforeSend: function () {
                    console.log("Deleting supplier...");
                },
                success: function (response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.status) {
                            alert("Supplier successfully deleted.");
                            const listUrl = sessionStorage.getItem("list_link") || "folders/supplier/list.php";
                            window.location.href = listUrl;
                        } else {
                            alert("Delete failed: " + (res.msg || "Unknown error"));
                        }
                    } catch (err) {
                        console.error("Invalid JSON response:", response);
                        alert("Server error. Please try again.");
                    }
                },
                error: function () {
                    alert("AJAX error. Check your connection or try again.");
                }
            });
        });
    }
});

$(document).ready(function () {
    $("#excel_export").on("click", function() {
        window.location.href = 'folders/supplier/excel.php';
    });
    // Block direct tab clicks if no unique_id
    $('.form-wizard-header .nav-link').on('click', function (e) {
        const unique_id = $('#unique_id').val();

        if (!unique_id) {
            e.preventDefault();
            // alert("Please save the previous tabs first.");
            return;
        }
        // Let it proceed to trigger shown.bs.tab
    });

    // When any tab is shown (user clicks or programmatic)
    $('.form-wizard-header .nav-link').on('shown.bs.tab', function (e) {
        const tabIndex = $(e.target).parent().index(); // Get new tab index
        const unique_id = $('#unique_id').val();

        if (!unique_id) return; // Safety check, shouldn't hit here due to above

        // alert("Switched to Tab Index: " + tabIndex);

        // Call your data loader
        handleTabLoadByIndex(tabIndex);
    });
});


// Master function to handle form submissions
function supplier_master_sc() {
    // Get the active tab
    const activeTab = $('#suppliercreatewizard .nav-link.active').attr('href');

    // Map tabs to their submission functions
    const tabHandlers = {
        '#personaldetails_tab': handlesupplierProfile,
        '#contactperson_tab': handleContactPerson,
        '#statutory_details_tab': handleStatutoryDetails,
        '#account_details_tab': handleAccountDetails,
        '#branch_details_tab': handleBranchDetails,
        '#billing_details_tab': handleBillingDetails,
        '#shipping_details_tab': handleShippingDetails,
        '#documents_tab': handleDocuments
    };

    // Check if activeTab exists and execute the appropriate handler
    if (activeTab && tabHandlers[activeTab]) {
        tabHandlers[activeTab]();
        console.log(`Handling submission for tab: ${activeTab}`);
    } else if (!activeTab) {
        console.error('No active tab found.');
    } else {
        console.error('No handler for active tab:', activeTab);
    }
}

// Handle supplier Profile Form
function handlesupplierProfile() {
    return new Promise((resolve, reject) => {
        const form = document.getElementById('supplier_profile_form');
        if (!form.checkValidity()) {
            form.reportValidity();
            reject({ error: 'Form validation failed' });
            return;
        }

        const formData = new FormData(form);
        formData.append('btn_action', '<?php echo $btn_action; ?>');
        formData.append('unique_id', document.getElementById('unique_id').value);
        formData.append('action', 'createupdate');  // Supplier Profile Create/Update

        for (let [key, value] of formData.entries()) {
            console.log(`${key}: ${value}`);
        }

        submitForm(ajax_url, formData, function(response) {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;

                if (res.supplier_unique_id) {
                    document.getElementById('unique_id').value = res.unique_id;
                    document.getElementById('supplier_unique_id').value = res.supplier_unique_id;
                    $("#supplier_unique_id").val(res.supplier_unique_id);

                    // Increment supplier name (assuming it's numeric)
                    const currentName = $("#supplier_name").val();
                    const newValue = Number(currentName) + 1;
                    $("#supplier_name").val(newValue);

                    handleTabLoadByIndex(1);
                    moveToNextTab(1);

                    resolve(res);
                } else {
                    console.error("Unexpected response format:", res);
                    reject({ error: 'Missing unique_id in response', raw: res });
                }
            } catch (err) {
                console.error("Failed to parse response:", response);
                reject({ error: 'Invalid JSON response', raw: response });
            }
        });
    });
}


function handleContactPerson() {
    
    const form = document.getElementById('contact_person_form');
    moveToNextTab(2);
}


// Handle Statutory Details
function handleStatutoryDetails() {
    return new Promise((resolve, reject) => {
        const fields = [
            'ecc_no', 'commissionerate', 'division', 'range', 
            'cst_no', 'tin_no', 'service_tax_no', 'iec_code',
            'cin_no', 'tan_no'
        ];

        // // Check if all fields are empty
        // const allEmpty = fields.every(field => {
        //     const element = document.getElementById(field);
        //     return !element || !element.value.trim();
        // });

        // if (allEmpty) {
        //     moveToNextTab(3);
        //     resolve({ status: 'skipped', message: 'All statutory fields are empty' });
        //     return;
        // }

        const formData = new FormData();
        formData.append('supplier_unique_id', document.getElementById('supplier_unique_id').value);
        formData.append('unique_id', document.getElementById('unique_id').value);
        formData.append('action', 'sp_statutory');

        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element) formData.append(field, element.value);
            console.info(`Appending ${field}: ${element ? element.value : 'not found'}`);
        });

        submitForm(ajax_url, formData, function(response) {
            try {
                const res = typeof response === 'string' ? JSON.parse(response) : response;

                let unique_id = document.getElementById('unique_id').value;
                if (unique_id) {
                    handleTabLoadByIndex(2); // Load supplier potential mapping tab
                }

                moveToNextTab(3);
                resolve(res);
            } catch (err) {
                console.error("Failed to process response:", response);
                reject({ error: 'Failed to parse response', raw: response });
            }
        });
    });
}

// Handle Account Details
function handleAccountDetails() {
    const form = document.getElementById('account_details_form');
    // const form = document.getElementById('billing_details_form');
    let unique_id = document.getElementById('unique_id').value;
    if (unique_id) {
        handleTabLoadByIndex(5); // Load billing details tab data
    }
    moveToNextTab(5);
}

// Handle supplier Potential Mapping
function handleBranchDetails() {
    const form = document.getElementById('branch_details_form');
    let unique_id = document.getElementById('unique_id').value;
    if (unique_id) {
        
        handleTabLoadByIndex(3); // Load account details tab data
    }
    moveToNextTab(4);
}

// Handle Billing Details
function handleBillingDetails() {
    let unique_id = document.getElementById('unique_id').value;
    if (unique_id) {
        handleTabLoadByIndex(4); // Load shipping details tab data
    }
    moveToNextTab(6);
}

// Handle Shipping Details
function handleShippingDetails() {
    const form = document.getElementById('shipping_details_form');
    moveToNextTab(7);
}
function handleDocuments() {
    const form = document.getElementById('documents_form');
    moveToNextTab(8);
}

// Generic AJAX submission function
function submitForm(url, formData, successCallback) {
    const submitBtn = document.querySelector('.createupdate_btn');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    $.ajax({
        url: url,
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.status) {
                    // alert(data.msg);

                    // document.getElementById('unique_id').value = data.unique_id;
                    // document.getElementById('supplier_unique_id').value = data.supplier_unique_id;
                    if (data.msg == 'create' || data.msg == 'update' || data.msg == 'add') {
                        successCallback(data);
                    } else {
                        showError(data.msg || 'Operation failed');
                    }
                }
                else {
                    showError(data.msg || 'Operation failed');
                }
            } catch (e) {
                
                showError('Invalid server response');
            }
        },
        error: function(xhr) {
            showError(`Server error: ${xhr.status} ${xhr.statusText}`);
        },
        complete: function() {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });
}

// Move to next tab and update progress
// function moveToNextTab(stepNumber) {
//     const tabs = $('#suppliercreatewizard .nav-link');
//     const currentIndex = tabs.index($('#suppliercreatewizard .nav-link.active'));
    
//     if (currentIndex < tabs.length - 1) {
//         tabs.eq(currentIndex + 1).tab('show');
//         document.querySelector('.bar').style.width = `${(stepNumber / 7) * 100}%`;
//     }
// }

function moveToNextTab(stepNumber) {
    const tabs = $('#suppliercreatewizard a[data-bs-toggle="tab"]');
    const currentIndex = tabs.index($('#suppliercreatewizard .nav-link.active'));

    if (currentIndex < tabs.length - 1) {
        const nextIndex = currentIndex + 1;

        // Show the next tab
        if(currentIndex === 1 || currentIndex === 3 || currentIndex === 4){
            tabs.eq(currentIndex).tab('show');
        } else {
            tabs.eq(currentIndex + 1).tab('show');
        }

        // Update progress bar (assuming 7 total steps)
        const progressBar = document.querySelector('.bar');
        if (progressBar) {
            progressBar.style.width = `${((nextIndex) / (tabs.length - 1)) * 100}%`;
        }

        // Update tab count to reflect new index
        $('#tab_count').val(nextIndex);
    }
}


// Show error message
function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#3bafda'
    });
}

$(document).ready(function () {
    $("#copy_info").on("change", function () {
        const isChecked = this.checked;
        const supplierId = document.getElementById("supplier_unique_id")?.value;
        const ajax_url = sessionStorage.getItem("folder_crud_link");

        if (!isChecked) return;

        if (!supplierId || !ajax_url) {
            alert("supplier ID or URL missing.");
            return;
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "copy_billing_data",
                supplier_unique_id: supplierId
            },
            beforeSend: function () {
                console.log("Fetching billing info...");
            },
            success: function (response) {
                try {
                    const res = JSON.parse(response);
                    if (res.status && res.data) {
                        const d = res.data;

                        console.info("Billing data fetched:", d);
                        

                        $("#name").val(d.name || "");
                        $("#shipping_address").val(d.billing_address || "");
                        $("#shipping_country").val(d.country || "").trigger("change");

                        // Wait until state & city dropdowns are populated
                        setTimeout(() => {
                            $("#shipping_state").val(d.state || "").trigger("change");

                            setTimeout(() => {
                                $("#shipping_city").val(d.city || "").trigger("change");
                            }, 300);
                        }, 300);

                        $("#contact_name").val(d.contact_name || "");
                        $("#contact_no").val(d.contact_no || "");
                        $("#shipping_gst_no").val(d.billing_gst_no || "");
                        $("#gst_status").val(d.gst_status || "1");
                        
                        $("#shipping_ecc_no").val(d.ecc_no || "");

                        console.log("Billing data copied to shipping.");
                    } else {
                        alert("No billing data found.");
                    }
                } catch (err) {
                    console.error("Invalid JSON response:", response);
                    alert("Server error while parsing billing data.");
                }
            },
            error: function () {
                alert("AJAX error while copying billing data.");
            }
        });
    });
});



// Helper function to show/hide GST fields
function gst_check() {
    const gstYes = document.getElementById('gst_status_yes').checked;
    document.querySelector('.gst_no_div').style.display = gstYes ? 'block' : 'none';
    document.getElementById('gst_no').required = gstYes;
}

function provisional_check() {
    const provYes = document.getElementById('provisional_status_yes').checked;
    document.querySelector('.provisional_no_div').style.display = provYes ? 'block' : 'none';
    document.getElementById('provisional_no').required = provYes;
}


async function final_submit() {
    // Check required values
    let unique_id = document.getElementById('unique_id').value;
    
    const tab_count = parseInt($("#tab_count").val(), 10);

    // Wait for profile handler to complete if tab_count is 0
    if (tab_count === 0) {
        await handlesupplierProfile(); // This must return a Promise
    }
    
    if (tab_count === 2) {
        await handleStatutoryDetails();
    }
    
    let allValuesValid = false;
    if (!unique_id) {
        const requiredIds = [
            '#supplier_name',
            // '#contact_value', 
            // '#branch_value', 
            // '#bd_value', 
            // '#billing_value', 
            // '#shipping_value',
            // '#doc_value',
        ];
        
        allValuesValid = true;
        const invalidValues = [];
        
        requiredIds.forEach(id => {
            const value = Number($(id).val());
            if (isNaN(value) || value <= 0) {
                allValuesValid = false;
                invalidValues.push(id);
            }
        });
    } else {
        // If unique_id exists, we assume the form is already filled
        allValuesValid = true;
        invalidValues = [];
    }
    
    if (!allValuesValid) {
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please fill all required fields',
            confirmButtonText: 'OK'
        });
        return;
    } else {
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: 'supplier profile completed successfully',
            confirmButtonColor: '#3bafda'
        }).then(() => {
            // stash your target URL so list_page can see it
            sessionStorage.setItem('redirectAfterSave', url);
            window.location.href = url;
        });
    }
    
    
}

function list_page() {
  const expected = sessionStorage.getItem('redirectAfterSave');
  const current  = window.location.href;
  
  if (current == 'http://localhost/blue_planet_beta/blue_planet_beta/index.php?file=supplier/list') {
    
    // it’s the list page we just redirected to → fire your datatable
    init_datatable(table_id, form_name, action);
    // clear the flag so it only runs once
    sessionStorage.removeItem('redirectAfterSave');
  }
}
    

$(document).ready(function() {

    
    // Datatable Initialize
    init_datatable(table_id,form_name,action);
    $('#suppliercreatewizard a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const $tabs = $('#suppliercreatewizard a[data-bs-toggle="tab"]');
        const $tabCount = $('#tab_count');
        const updateBtn = document.getElementById("createupdate_btn");
    
        const clickedTab = $(e.target).attr('href'); // e.g., "#contactperson_tab"
        const tabIndex = $tabs.index($(e.target));   // Get the index of the current tab
    
        $tabCount.val(tabIndex); // Dynamically update tab count
    
        if (clickedTab === '#documents_tab') {
            updateBtn.style.display = 'none';
        } else {
            updateBtn.style.display = 'inline-block';
        }
    });


    // Form wizard Functions
    $('#suppliercreatewizard').bootstrapWizard({
        onTabShow: function(tab, navigation, index) {
            var supplier_unique_id = $("#supplier_unique_id").val();

            if (index != 0) {
                if (!supplier_unique_id) {
                    
                    sweetalert("custom",'','','Create Supplier First');
                    $('#suppliercreatewizard').find("a[href*='personaldetails_tab']").trigger('click');
                    return event.preventDefault(), event.stopPropagation(), !1;
                }
            }

            // console.log(index);
            var $total   = navigation.find('li').length;
            var $current = index+1;
            var $percent = ($current/$total) * 100;
            $('#suppliercreatewizard').find('.bar').css({width:$percent+'%'});

            // If it's the last tab then hide the last button and show the finish instead
            if($current >= $total) {
                $('#suppliercreatewizard').find('.pager .next').hide();
                $('#suppliercreatewizard').find('.pager .finish').show();
                $('#suppliercreatewizard').find('.pager .finish').removeClass('disabled');
                // unique_id    = $(".finish").data("unique-id");
            } else {
                $('#suppliercreatewizard').find('.pager .next').show();
                $('#suppliercreatewizard').find('.pager .finish').hide();
            }
            
            if (index != 0) {
                $(".createupdate_btn").text("Next");
            }

            handleTabLoadByIndex(index);

        },
        onNext: function (t, r, index) {

            

            if (index == 1) {
                var form_class  = "supplier_profile_form";

                var is_form     = form_validity_check(form_class);
                var unique_id   = $("#unique_id").val();

                if (!is_form) {
                    sweetalert("form_alert");
                    return event.preventDefault(), event.stopPropagation(), !1;
                } else {
                    var gst_no = $('#gst_no').val();

                    var res = gst_no.substring(0, 2);
                    
                    if(res){
                        var data     = $("."+form_class).serialize();
                        data        += "&unique_id="+unique_id+"&action=createupdate";
                    
                        var ajax_url = sessionStorage.getItem("folder_crud_link");
                        var url      = sessionStorage.getItem("list_link");
                    
                        // console.log(data);
                        $.ajax({
                            type    : "POST",
                            url     : ajax_url,
                            data    : data,
                            beforeSend  : function() {
                                $(".createupdate_btn").addClass("disabled");
                                $(".createupdate_btn").text("Loading...");
                            },
                            success     : function(data) {
                    
                                var obj           = JSON.parse(data);
                                var msg           = obj.msg;
                                var status        = obj.status;
                                var error         = obj.error;
                                var supplier_unique_id        = obj.supplier_unique_id;
                                url               = '';
                                var success       = false;
                                    
                                if (!status) {
                                    
                                    $(".createupdate_btn").text("Error");
                                    console.log(error);
                                } else {
                                    success = true;
                                    if (msg=="already") {
                                        // Button Change Attribute
                                        url         = '';
                                        success = false;
                                    }

                                

                                    $(".createupdate_btn").removeClass("disabled","disabled");
                                    if (unique_id) {
                                        $(".createupdate_btn").text("Update & Continue");
                                    } else {
                                        $(".createupdate_btn").text("Save & Continue");
                                    }

                                    sweetalert(msg,url);
                    
                                    if (!success) {
                                        console.log(success);
                                        $('#suppliercreatewizard').find("a[href*='personaldetails_tab']").trigger('click');
                                        // $('#suppliercreatewizard').find("a[href*='contactperson_tab']").removeClass('active');
                                        // $('#suppliercreatewizard').find("a[href*='profile_tab']").addClass('active');
                                    } else {
                                        console.log(success);
                                        $("#supplier_unique_id").val(supplier_unique_id);
                                        $('#suppliercreatewizard').find("a[href*='contactperson_tab']").trigger('click');
                                        // $('#suppliercreatewizard').find("a[href*='profile_tab']").removeClass('active');
                                        // $('#suppliercreatewizard').find("a[href*='contactperson_tab']").addClass('active');
                                    }
                                    // return success;

                                }
                                
                            },
                            error       : function(data) {
                                alert("Network Error");
                                // return false;
                            }
                        });
                        return event.preventDefault(), event.stopPropagation(), !1;
                    }
                }
            }
        },
        onTabClick: function(tab, navigation, index) {
            // return false;
            // return event.preventDefault(), event.stopPropagation(), !1;
        }
    });
    $('#suppliercreatewizard .finish').click(function() {
        var url      = sessionStorage.getItem("list_link");
        sweetalert("create",url);
        // alert('Finished!, Starting over!');
        // $('#suppliercreatewizard').find("a[href*='tab1']").trigger('click');
    });

});

function init_datatable(table_id='',form_name='',action='') {
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

function handleTabLoadByIndex(index) {
    switch (index) {
        case 1:
            supplier_sub_list_datatable(contact_person_tableid);
            break;
        case 3:
            supplier_sub_list_datatable(account_details_tableid);
            break;
        case 5:
            supplier_sub_list_datatable(shipping_details_tableid);
            break;
        case 4:
            supplier_sub_list_datatable(billing_details_tableid);
            break;
        case 6:
            supplier_sub_list_datatable(documents_tableid);
            break;
        default:
            break;
    }
}
// Contact Person CU
function contact_person_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var supplier_unique_id = $("#supplier_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("","contact_person_form");

    console.log(is_form);

    if (is_form) {

        var data     = $(".contact_person_form").serialize();
        data        += "&supplier_unique_id="+supplier_unique_id;
        data        += "&unique_id="+unique_id+"&action=contact_person_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".contact_person_add_update_btn").attr("disabled","disabled");
                $(".contact_person_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".contact_person_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                        form_reset("contact_person_form");
                        var newValue = Number($("#contact_value").val()) + 1;

                        // Update the field with new value
                        $("#contact_value").val(newValue);
                    }
                    $(".contact_person_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".contact_person_add_update_btn").text("Update");
                    } else {
                        $(".contact_person_add_update_btn").text("Add");
                        $(".contact_person_add_update_btn").attr("onclick","contact_person_add_update('')");
                    }
                    // Init Datatable
                    supplier_sub_list_datatable("contact_person_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
        sweetalert("form_alert");
    }
}

function contact_person_edit(unique_id = "") {
    // alert(unique_id);
    if (unique_id) {
        var data        = "unique_id="+unique_id+"&action=contact_person_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".contact_person_add_update_btn").attr("disabled","disabled");
                $(".contact_person_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;
                
                // alert(data);

                if (!status) {
                    $(".contact_person_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var contact_person_name             = data.contact_person_name;
                    var contact_person_designation      = data.contact_person_designation;
                    // var contact_person_address1     = data.contact_person_address1;
                    // var contact_person_address2     = data.contact_person_address2;
                    var contact_person_email            = data.contact_person_email;
                    var contact_person_contact_no       = data.contact_person_contact_no;
                    var landline                        = data.landline;
                    var department                      = data.department;
                    
                    // alert(contact_person_name);
                    // alert(contact_person_designation);
                    // alert(contact_person_email);
                    // alert(contact_person_contact_no);
                    // alert(landline);
                    // alert(department);

                    $("#contact_person_name").val(contact_person_name);
                    $("#contact_person_designation").val(contact_person_designation);
                    // $("#contact_person_address1").val(contact_person_address1);
                    // $("#contact_person_address2").val(contact_person_address2);
                    $("#contact_person_email").val(contact_person_email);
                    $("#contact_person_contact_no").val(contact_person_contact_no);
                    $("#landline").val(landline);
                    $("#department").val(department);

                    // Button Change 
                    $(".contact_person_add_update_btn").removeAttr("disabled","disabled");
                    $(".contact_person_add_update_btn").text("Update");
                    $(".contact_person_add_update_btn").attr("onclick","contact_person_add_update('"+unique_id+"')");
                }
            },
            error       : function(data) {
                alert("Network Error");
            }
        });
    }
}

function contact_person_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id"     : unique_id,
                    "action"        : "contact_person_delete"
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
                            supplier_sub_list_datatable("contact_person_datatable");
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



// Account Details ADD & UPDATE
function supp_account_details_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var supplier_unique_id = $("#supplier_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("account_details_form");

    // console.log(is_form);

    if (is_form) {

        var data 	 = $(".account_details_form").serialize();
        data 		+= "&supplier_unique_id="+supplier_unique_id;
        data 		+= "&unique_id="+unique_id+"&action=account_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".account_details_add_update_btn").attr("disabled","disabled");
				$(".account_details_add_update_btn").text("Loading...");
			},
			success		: function(data) {

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".account_details_add_update_btn").text("Error");
                    console.log(error);
				} else {
					if (msg !=="already") {
						form_reset("account_details_form");
                        var newValue = Number($("#bd_value").val()) + 1;

                        // Update the field with new value
                        $("#bd_value").val(newValue);
                    }
                    $(".account_details_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".account_details_add_update_btn").text("Update");
                    } else {
                        $(".account_details_add_update_btn").text("Add");
                        $(".account_details_add_update_btn").attr("onclick","supp_account_details_add_update('')");
                    }
                    // Init Datatable
                    supplier_sub_list_datatable("account_details_datatable");
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

function account_details_edit(unique_id = "") {
    if (unique_id) {
        var data 		= "unique_id="+unique_id+"&action=account_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".account_details_add_update_btn").attr("disabled","disabled");
				$(".account_details_add_update_btn").text("Loading...");
			},
			success		: function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".account_details_add_update_btn").text("Error");
                    console.log(error);
				} else {
                    console.log(obj);
                    var bank_name                   = data.bank_name;
                    var bank_address                = data.address;
                    var ifsc_code                   = data.ifsc_code;
                    var dealer_name                 = data.dealer_name;
                    var account_no                  = data.account_no;
                    var contact_no                  = data.contact_no;
                    var swift_code                  = data.swift_code;
                    
                    $("#bank_name").val(bank_name);
                    $("#bank_address").val(bank_address);
                    $("#ifsc_code").val(ifsc_code);
                    $("#dealer_name").val(dealer_name);
                    $("#account_no").val(account_no);
                     $("#bank_contact_no").val(contact_no);
                     $("#swift_code").val(swift_code);

                    // Button Change 
                    $(".account_details_add_update_btn").removeAttr("disabled","disabled");
                    $(".account_details_add_update_btn").text("Update");
                    $(".account_details_add_update_btn").attr("onclick","supp_account_details_add_update('"+unique_id+"')");
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
    }
}

function account_details_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "account_details_delete"
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
                            supplier_sub_list_datatable("account_details_datatable");
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


// Branch Details ADD & UPDATE
function supp_branch_details_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var supplier_unique_id = $("#supplier_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("branch_details_form");

    // console.log(is_form);

    if (is_form) {

        var data     = $(".branch_details_form").serialize();
        data        += "&supplier_unique_id="+supplier_unique_id;
        data        += "&unique_id="+unique_id+"&action=branch_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
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
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".branch_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                        form_reset("branch_details_form");
                        var newValue = Number($("#branch_value").val()) + 1;

                        // Update the field with new value
                        $("#branch_value").val(newValue);
                    }
                    $(".branch_details_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".branch_details_add_update_btn").text("Update");
                    } else {
                        $(".branch_details_add_update_btn").text("Add");
                        $(".branch_details_add_update_btn").attr("onclick","supp_branch_details_add_update('')");
                    }
                    // Init Datatable
                    supplier_sub_list_datatable("branch_details_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
        sweetalert("form_alert");
    }
}

function branch_details_edit(unique_id = "") {
    if (unique_id) {
        var data        = "unique_id="+unique_id+"&action=branch_details_edit";

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
                    var branch_name                   = data.branch_name;
                    var branch_address                = data.branch_address;
                    var branch_state_name             = data.branch_state_name;
                    var branch_city_name              = data.branch_city_name;
                    var branch_gst_no                 = data.branch_gst_no;
                    var branch_contact_no             = data.branch_contact_no;
                    var branch_pincode                = data.branch_pincode;
                    
                    $("#branch_name").val(branch_name);
                    $("#branch_address").val(branch_address);
                    $("#branch_state_name").val(branch_state_name).trigger("change");
                    $("#edit_branch_city").val(branch_city_name);
                    $("#branch_gst_no").val(branch_gst_no);
                    $("#branch_contact_no").val(branch_contact_no);
                    $("#branch_pincode").val(branch_pincode);

                    // Button Change 
                    $(".branch_details_add_update_btn").removeAttr("disabled","disabled");
                    $(".branch_details_add_update_btn").text("Update");
                    $(".branch_details_add_update_btn").attr("onclick","supp_branch_details_add_update('"+unique_id+"')");
                }
            },
            error       : function(data) {
                alert("Network Error");
            }
        });
    }
}

function branch_details_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id"     : unique_id,
                    "action"        : "branch_details_delete"
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
                            supplier_sub_list_datatable("branch_details_datatable");
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

function supplier_sub_list_datatable (table_id = "", form_name = "", action = "") {
     //alert("test");
    var supplier_unique_id = $("#supplier_unique_id").val();
    
    var table = $("#"+table_id);
    var data      = {
        "supplier_unique_id"    : supplier_unique_id,
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

function supplier_toggle(unique_id = "", is_active = 0) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "toggle",
            unique_id: unique_id,
            is_active: is_active
        },
        success: function (data) {
            const obj = JSON.parse(data);
            if (obj.status) {
                $("#" + "supplier_datatable").DataTable().ajax.reload(null, false);
                sweetalert(obj.msg);
            } else {
                sweetalert("Toggle failed!");
            }
        }
    });
}



  
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}


function autocapitalization(pan_no = "") { 
    
    var val = $('#pan_no').val(); 
    $('#pan_no').val(val.toUpperCase());
}

function autocapitalization_gst(pan_no = "") { 
    
    var val = $('#gst_no').val(); 
    $('#gst_no').val(val.toUpperCase());
}

// Get city Names Based On State Selection
function get_cities (state_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (state_id) {
        var data = {
            "state_id"      : state_id,
            "action"        : "cities"
        }

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            success : function(data) {

                if (data) {
                    $("#city_name").html(data);
                }

            }
        });
    }
}  



// Get city Names Based On State Selection
function get_branch_cities (state_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (state_id) {
        var data = {
            "state_id"      : state_id,
            "action"        : "branch_cities"
        }

        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            success : function(data) {

                if (data) {
                    $("#branch_city_name").html(data);
                }
                var edit_branch_city = $("#edit_branch_city").val();
				if (edit_branch_city) {

					$("#branch_city_name").val(edit_branch_city).trigger('change');
					$("#edit_branch_city").val('');
				}

            }
        });
    }
}  

// Get State Names Based On Country Selection
function get_states (country_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (country_id) {
		var data = {
			"country_id" 	: country_id,
			"action"		: "states"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
					$("#state_name").html(data);
				}

			}
		});
	}
}  

function get_state_code(code){

    var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (code) {
		var data = {
			"code" 	: code,
			"action": "get_state_code"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			dataType: 'json',
			success : function(data) {
                $("#state_code").val(data);
			}
		});
	}
	
}




// Billing Details ADD & UPDATE
function billing_details_add_update (unique_id = "") {
    // alert(unique_id);

    var internet_status  = is_online();

    var supplier_unique_id = $("#supplier_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("billing_details_form");


    if (is_form) {

        var data 	 = $(".billing_details_form").serialize();
        data 		+= "&supplier_unique_id="+supplier_unique_id;
        data 		+= "&unique_id="+unique_id+"&action=billing_details_add_update";
        
       
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = "";

        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".billing_details_add_update_btn").attr("disabled","disabled");
				$(".billing_details_add_update_btn").text("Loading...");
			},
			success		: function(data) {
                console.info(data);               // still prints object to console

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".billing_details_add_update_btn").text("Error");
                    console.log(error);
				} else {
					if (msg !=="already") {
						form_reset("billing_details_form");
                        var newValue = Number($("#billing_value").val()) + 1;

                        // Update the field with new value
                        $("#billing_value").val(newValue);
                    }
                    $(".billing_details_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".billing_details_add_update_btn").text("Update");
                    } else {
                        $(".billing_details_add_update_btn").text("Add");
                        $(".billing_details_add_update_btn").attr("onclick","billing_details_add_update('')");
                    }
                    // Init Datatable
                    supplier_sub_list_datatable("billing_details_datatable");
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



function billing_details_edit(unique_id = "") {  //correct
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=billing_details_edit";
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".billing_details_add_update_btn").attr("disabled", "disabled").text("Loading...");
            },
            success: function (response) {
                var obj = JSON.parse(response);
                if (!obj.status) {
                    $(".billing_details_add_update_btn").text("Error");
                    console.log(obj.error);
                    return;
                }

                var data = obj.data;

                $("#billing_name").val(data.name || "");
                $("#billing_address").val(data.billing_address || "");
                $("#bill_contact_name").val(data.contact_name || "");
                $("#bill_contact_no").val(data.contact_no || "");
                $("#bill_gst_no").val(data.billing_gst_no || "");
                $("#gst_status_bill").val(data.gst_status).trigger('change');
                $("#bill_ecc_no").val(data.ecc_no || "");
                
                $("#billing_country").val(data.country).trigger('change');
                
                get_billing_states(data.country);
                
                setTimeout(function () {
                    $("#billing_state").val(data.state).trigger('change');
                
                    get_billing_cities(data.state);
                
                    setTimeout(function () {
                        $("#billing_city").val(data.city).trigger('change');
                    }, 500);
                }, 500);
                

                $(".billing_details_add_update_btn")
                    .removeAttr("disabled")
                    .text("Update")
                    .attr("onclick", "billing_details_add_update('" + unique_id + "')");
            },
            error: function () {
                alert("Network Error");
            }
        });
    }
}



function billing_details_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "billing_details_delete"
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
                            supplier_sub_list_datatable("billing_details_datatable");
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

// Shipping Details ADD & UPDATE
function shipping_details_add_update (unique_id = "") {
    // alert(unique_id);

    var internet_status  = is_online();

    var supplier_unique_id = $("#supplier_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("shipping_details_form");


    if (is_form) {

        var data 	 = $(".shipping_details_form").serialize();
        data 		+= "&supplier_unique_id="+supplier_unique_id;
        data 		+= "&unique_id="+unique_id+"&action=shipping_details_add_update";
        
       
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = "";

        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".shipping_details_add_update_btn").attr("disabled","disabled");
				$(".shipping_details_add_update_btn").text("Loading...");
			},
			success		: function(data) {

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".shipping_details_add_update_btn").text("Error");
                    console.log(error);
				} else {
					if (msg !=="already") {
						form_reset("shipping_details_form");
                        var newValue = Number($("#shipping_value").val()) + 1;

                        // Update the field with new value
                        $("#shipping_value").val(newValue);
                    }
                    $(".shipping_details_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".shipping_details_add_update_btn").text("Update");
                    } else {
                        $(".shipping_details_add_update_btn").text("Add");
                        $(".shipping_details_add_update_btn").attr("onclick","shipping_details_add_update('')");
                    }
                    // Init Datatable
                    supplier_sub_list_datatable("shipping_details_datatable");
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



function shipping_details_edit(unique_id = "") {  //correct
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=shipping_details_edit";
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".shipping_details_add_update_btn").attr("disabled", "disabled").text("Loading...");
            },
            success: function (response) {
                var obj = JSON.parse(response);
                if (!obj.status) {
                    $(".shipping_details_add_update_btn").text("Error");
                    console.log(obj.error);
                    return;
                }

                var data = obj.data;

                $("#name").val(data.name || "");
                $("#shipping_address").val(data.shipping_address || "");
                $("#contact_name").val(data.contact_name || "");
                $("#contact_no").val(data.contact_no || "");
                $("#shipping_gst_no").val(data.shipping_gst_no || "");
                $("#gst_status").val(data.gst_status).trigger('change');
                $("#ecc_no").val(data.ecc_no || "");

                $("#shipping_country").val(data.country).trigger('change');

                get_shipping_states(data.country);

                setTimeout(function () {
                    $("#shipping_state").val(data.state).trigger('change');

                    get_shipping_cities(data.state);

                    setTimeout(function () {
                        $("#shipping_city").val(data.city).trigger('change');
                    }, 500);
                }, 500);
                

                $(".shipping_details_add_update_btn")
                    .removeAttr("disabled")
                    .text("Update")
                    .attr("onclick", "shipping_details_add_update('" + unique_id + "')");
            },
            error: function () {
                alert("Network Error");
            }
        });
    }
}



function shipping_details_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "shipping_details_delete"
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
                            supplier_sub_list_datatable("shipping_details_datatable");
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


function documents_add_update(unique_id = "") {
    var internet_status = is_online();
    var data = new FormData();

    var type = $("#type").val();
    var supplier_unique_id = $("#supplier_unique_id").val();
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
            "image/svg+xml"
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
        }

        data.append("supplier_unique_id", supplier_unique_id);
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

        for (let pair of data.entries()) {
            console.log(pair[0] + ':', pair[1]);
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
                $(".documents_add_update_btn").attr("disabled", "disabled");
                $(".documents_add_update_btn").text("Loading...");
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
                    return;
                }

                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                // If backend returns invalid_file_format, show alert
                if (msg === "invalid_file_format") {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid File Format',
                        text: 'Only images and PDF files are allowed.',
                        confirmButtonColor: '#3bafda'
                    });
                    $(".documents_add_update_btn").removeAttr("disabled").text("Add");
                    return;
                }

                if (!status) {
                    $(".documents_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already" && msg !== "invalid_file_format") {
                        form_reset("documents_form");
                        var newValue = Number($("#document_value").val()) + 1;
                        $("#document_value").val(newValue);
                    }
                    $(".documents_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".documents_add_update_btn").text("Update");
                    } else {
                        $(".documents_add_update_btn").text("Add");
                        $(".documents_add_update_btn").attr("onclick", "documents_add_update('')");
                    }
                    supplier_sub_list_datatable("documents_datatable");
                }
                sweetalert(msg, url);
            },
            error: function (data) {
                alert("Network Error");
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
                            supplier_sub_list_datatable("documents_datatable");
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



function get_billing_states (country_id = "") {
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (country_id) {
		var data = {
			"country_id" 	: country_id,
			"action"		: "states"
		};

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				if (data) {
					$("#billing_state").html(data);
				}
			}
		});
	}
}




function get_billing_cities(state_id = "", selected_city_id = "") {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (state_id) {
        var data = {
            "state_id": state_id,
            "action": "cities"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (response) {
                if (response) {
                    $("#billing_city").html(response);
                    if (selected_city_id) {
                        $("#billing_city").val(selected_city_id).trigger('change');
                    }
                }
            }
        });
    }
}

function get_shipping_states (country_id = "") {
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (country_id) {
		var data = {
			"country_id" 	: country_id,
			"action"		: "states"
		};

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {
				if (data) {
					$("#shipping_state").html(data);
				}
			}
		});
	}
}




function get_shipping_cities(state_id = "", selected_city_id = "") {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (state_id) {
        var data = {
            "state_id": state_id,
            "action": "cities"
        };

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (response) {
                if (response) {
                    $("#shipping_city").html(response);
                    if (selected_city_id) {
                        $("#shipping_city").val(selected_city_id).trigger('change');
                    }
                }
            }
        });
    }
}



function new_external_window_image(image_url) {
    // Open the image in a new window
    window.open(image_url, '_blank', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}
// Run this after the page loads
$(document).ready(function() {
    $("#test_file_qual").on("change", function() {
        var files = this.files;
        var maxSize = 5 * 1024 * 1024; // 5 MB in bytes
        var valid = true;

        for (var i = 0; i < files.length; i++) {
            if (files[i].size > maxSize) {
                valid = false;
                break;
            }
        }

        if (!valid) {
            // Clear the file input
            $(this).val('');
            
            // SweetAlert
            Swal.fire({
                icon: 'error',
                title: 'File too large',
                text: 'Each file must be under 5 MB!'
            });
        }
    });
});


