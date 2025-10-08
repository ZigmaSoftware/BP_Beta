var contact_person_tableid                  = "contact_person_datatable";
var delivery_details_tableid                = "invoice_details_datatable";
var customer_potential_mapping_tableid      = "customer_potential_mapping_datatable";
var account_details_tableid                 = "account_details_datatable";
var shipping_details_tableid                = "shipping_details_datatable";
var billing_details_tableid                 = "billing_details_datatable";
var documents_tableid                       = "documents_datatable";


var form_name 		                        = 'Customer';
var form_header		                        = '';
var form_footer 	                        = '';
var table_name 		                        = '';
var table_id 		                        = 'customer_datatable';
var action 			                        = "datatable";

var ajax_url    = sessionStorage.getItem("folder_crud_link");
var url         = sessionStorage.getItem("list_link");

document.querySelectorAll("button[type='button']").forEach(btn => {
    if (btn.innerText.trim() === "Cancel") {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            const confirmCancel = confirm("Are you sure you want to cancel?");
            if (!confirmCancel) return;

            const customerId = document.getElementById("customer_unique_id")?.value;
            const uniqueId = document.getElementById("unique_id")?.value;

            if (!customerId || uniqueId) {
                console.log("No customer ID found. Nothing to cancel.");
                window.location.href = url;
                return;
            }

            $.ajax({
                type: "POST",
                url: ajax_url,
                data: {
                    action: "customer_master_delete",
                    customer_unique_id: customerId
                },
                beforeSend: function () {
                    console.log("Deleting customer...");
                },
                success: function (response) {
                    try {
                        const res = JSON.parse(response);
                        if (res.status) {
                            alert("Customer successfully deleted.");
                            const listUrl = sessionStorage.getItem("list_link") || "folders/customer/list.php";
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



function gst_check() {

    var status = $("input[name='gst_status']:checked").val();

    if (status != 0) {
		$("#gst_no").attr("required","required");
		
		$(".gst_no_div").removeClass("d-none");
		
	} else {

		$("#gst_no").removeAttr("required","required");
		$("#gst_no").val("");
		
		$(".gst_no_div").addClass("d-none");
		
	} 	
}

$('.form-wizard-header .nav-link').on('click', function (e) {
    const $this = $(this);
    const tabIndex = $this.parent().index();
    // alert(tabIndex);
    const unique_id = $('#unique_id').val();

    if (!unique_id) {
        e.preventDefault();
        // alert("Please save the previous tabs first.");
        return;
    }
    // alert(`Loading tab index: ${tabIndex}`);
    handleTabLoadByIndex(tabIndex);
});

// Master function to handle form submissions
function item_master_sc() {
    // Get the active tab
    const activeTab = $('#customercreatewizard .nav-link.active').attr('href');
    
    // Map tabs to their submission functions
    const tabHandlers = {
        '#profile_tab': handleCustomerProfile,
        '#contactperson_tab': handleContactPerson,
        '#statutory_details_tab': handleStatutoryDetails,
        // '#customer_potential_mapping_tab': handleCustomerPotential,
        '#account_details_tab': handleAccountDetails,
        '#billing_details_tab': handleBillingDetails,
        '#shipping_details_tab': handleShippingDetails
    };

    // Execute the appropriate handler
    if (tabHandlers[activeTab]) {
        tabHandlers[activeTab]();
        console.log(`Handling submission for tab: ${activeTab}`);
    } else {
        console.error('No handler for active tab:', activeTab);
    }
}

// Handle Customer Profile Form
function handleCustomerProfile() {
    return new Promise((resolve, reject) => {
        const form = document.getElementById('customer_profile_form');

        // Basic HTML5 form validation
        if (!form.checkValidity()) {
            form.reportValidity();
            reject("Form is invalid");
            return;
        }

        const formData = new FormData(form);
        formData.append('btn_action', '<?php echo $btn_action; ?>');
        formData.append('unique_id', document.getElementById('unique_id').value);
        formData.append('action', 'createupdate');

        for (let [key, value] of formData.entries()) {
            console.log(key + ": " + value);
        }

        // submitForm must trigger callback with response object
        submitForm(ajax_url, formData, function(response) {
            if (response && response.unique_id) {
                document.getElementById('unique_id').value = response.unique_id;
                document.getElementById('customer_unique_id').value = response.customer_unique_id;
            }

            $("#customer_unique_id").val(response.customer_unique_id);
            var newValue = Number($("#customer_name").val()) + 1;
            $("#customer_name").val(newValue);

            let unique_id = document.getElementById('unique_id').value;
            if (unique_id) {
                handleTabLoadByIndex(1); // Load contact person tab
            }

            moveToNextTab(1);
            resolve(response); // Promise resolved after success
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
        const formData = new FormData();
        formData.append('customer_unique_id', document.getElementById('customer_unique_id').value);
        formData.append('unique_id', document.getElementById('unique_id').value);
        formData.append('action', 'cp_statutory');  // Statutory Details

        const fields = [
            'ecc_no', 'commissionerate', 'division', 'range', 
            'cst_no', 'tin_no', 'service_tax_no', 'iec_code',
            'cin_no', 'tan_no'
        ];

        fields.forEach(field => {
            const element = document.getElementById(field);
            if (element) {
                formData.append(field, element.value);
                console.info(`Appending ${field}: ${element.value}`);
            } else {
                console.warn(`Field ${field} not found`);
            }
        });

        submitForm(ajax_url, formData, function(response) {
            try {
                const res = JSON.parse(response);
                if (res.status === 1) {
                    const unique_id = document.getElementById('unique_id').value;
                    if (unique_id) {
                        handleTabLoadByIndex(3); // Load next tab only if updating
                    }
                    moveToNextTab(3);
                    resolve(res); // success
                } else {
                    console.error('Error in statutory save:', res);
                    reject(res); // failure response
                }
            } catch (err) {
                console.error('Invalid JSON from server:', response);
                reject({ error: 'Invalid server response', raw: response });
            }
        });
    });
}

// // Handle Customer Potential Mapping
// function handleCustomerPotential() {
//     const form = document.getElementById('customer_potential_mapping_form');
//     let unique_id = document.getElementById('unique_id').value;
//     if (unique_id) {
        
//         handleTabLoadByIndex(3); // Load account details tab data
//     }
//     moveToNextTab(4);
// }

// Handle Account Details
function handleAccountDetails() {
    const form = document.getElementById('account_details_form');
    // const form = document.getElementById('billing_details_form');
    let unique_id = document.getElementById('unique_id').value;
    if (unique_id) {
        handleTabLoadByIndex(2); // Load billing details tab data
    }
    moveToNextTab(4);
}

// Handle Billing Details
function handleBillingDetails() {
    let unique_id = document.getElementById('unique_id').value;
    if (unique_id) {
        handleTabLoadByIndex(4); // Load shipping details tab data
    }
    moveToNextTab(5);
}

// Handle Shipping Details
function handleShippingDetails() {
    const form = document.getElementById('shipping_details_form');
    if (unique_id) {
        handleTabLoadByIndex(5); // Load shipping details tab data
    }
    moveToNextTab(7);
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

                    // document.getElementById('unique_id').value = data.unique_id;
                    // document.getElementById('customer_unique_id').value = data.customer_unique_id;
                    if (data.msg == 'create' || data.msg == 'update' || data.msg == 'no statutory data to save, moving on') {
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

function moveToNextTab(stepNumber) {
    const tabs = $('#customercreatewizard .nav-link');
    const currentIndex = tabs.index($('#customercreatewizard .nav-link.active'));

    if (currentIndex < tabs.length - 1) {
        // Move to the next tab
        if(currentIndex === 1 || currentIndex === 3 || currentIndex === 4){
            tabs.eq(currentIndex).tab('show');
        } else {
            tabs.eq(currentIndex + 1).tab('show');
        }

        // Update the progress bar
        document.querySelector('.bar').style.width = `${(stepNumber / 7) * 100}%`;

        // Increment #tab_count value
        const tabCountInput = document.getElementById("tab_count");
        if (tabCountInput) {
            const currentVal = parseInt(tabCountInput.value) || 0;
            tabCountInput.value = currentVal + 1;
        }
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
    $("#excel_export").on("click", function() {
        window.location.href = 'folders/customer/excel.php';
    });
    $("#copy_info").on("change", function () {
        const isChecked = this.checked;
        const customerId = document.getElementById("customer_unique_id")?.value;
        const ajax_url = sessionStorage.getItem("folder_crud_link");

        if (!isChecked) return;

        if (!customerId || !ajax_url) {
            alert("Customer ID or URL missing.");
            return;
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: {
                action: "copy_billing_data",
                customer_unique_id: customerId
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
function gst_check1() {
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
    let unique_id = document.getElementById('unique_id').value;
    const tab_count = parseInt($("#tab_count").val(), 10);

    // Wait for profile handler to complete if tab_count is 0
    if (tab_count === 0) {
        await handleCustomerProfile(); // This must return a Promise
    }
    
    if (tab_count === 2) {
        await handleStatutoryDetails();
    }

    let allValuesValid = false;
    let invalidValues = [];

    if (!unique_id) {
        const requiredIds = [
            '#customer_name',
            // '#contact_value',
            // '#cpm_value',
            // '#bd_value',
        ];
        
        allValuesValid = true;

        requiredIds.forEach(id => {
            const value = Number($(id).val());
            if (isNaN(value) || value <= 0) {
                allValuesValid = false;
                invalidValues.push(id);
            }
        });
    } else {
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
            text: 'Customer profile completed successfully',
            confirmButtonColor: '#3bafda'
        }).then(() => {
            sessionStorage.setItem('redirectAfterSave', url);
            window.location.href = url;
        });
    }
}

function list_page() {
  const expected = sessionStorage.getItem('redirectAfterSave');
  const current  = window.location.href;
  
  if (current == 'http://localhost/blue_planet_beta/blue_planet_beta/index.php?file=customer/list') {
    
    // it’s the list page we just redirected to → fire your datatable
    // alert("Hello");
    init_datatable(table_id, form_name, action);
    // clear the flag so it only runs once
    sessionStorage.removeItem('redirectAfterSave');
  }
}


function provisional_check() {

    var status = $("input[name='provisional_status']:checked").val();

    // 

    if (status != 0) {
		$("#provisional_no").attr("required","required");
		
		$(".provisional_no_div").removeClass("d-none");
		
	} else {

		$("#provisional_no").removeAttr("required","required");
		$("#provisional_no").val("");
		
		$(".provisional_no_div").addClass("d-none");
		
	} 	
}

function customer_sub_category_and_group (unique_id = "") {
    
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
    
        var data = {
            "unique_id" 	: unique_id,
            "action"		: "customer_sub_category_and_group"
        }

        $.ajax({
            type 	: "POST",
            url 	: ajax_url,
            data 	: data,
            success : function(data) {

                var obj             = JSON.parse(data);

                var sub_category    = obj.sub_category;
                var group           = obj.group;

                $("#customer_sub_category").html(sub_category);
                $("#customer_group").html(group);
            }
        });
    }
}

$(document).ready(function () {
        $('#customercreatewizard .nav-link').on('shown.bs.tab', function (e) {
            const targetTab = $(e.target).attr('href');
            const tabCountInput = document.getElementById("tab_count");
            const updateBtn = document.getElementById("createupdate_btn");

            if (!tabCountInput || !updateBtn) return;

            if (targetTab === '#documents_tab') {
                tabCountInput.value = 7;
                updateBtn.style.display = "none";
            } else {
                tabCountInput.value = 0;
                updateBtn.style.display = "inline-block"; // or "" or "flex" depending on layout
            }
        });
    });

$(document).ready(function() {
    
    // alert("hello");
    

    list_page();

    var country_id = $("#country_name").val();
    var unique_id  = $("#unique_id").val();
   
    var sess_user_id       = $("#user_id").val();
    var sess_user_type_id  = $("#user_type_id").val();
    var image_s = document.getElementById("test_file_qual");

    if (country_id) {
        // Only check when Update
        if (!unique_id) {
            get_states(country_id);
        }
    }    
    
    // alert("pre gst");
    
    gst_check();
    // alert("gst checked");
    provisional_check();
    // alert("provisional checked");
    $("#excel_export").click(function(){
        window.location="folders/customer/excel.php?user_id="+sess_user_id+"&user_type_id="+sess_user_type_id;
    //

    });
    // Datatable Initialize
    
    init_datatable(table_id,form_name,action);
    // alert(table_id);

    // Form wizard Functions
    $('#customercreatewizard').bootstrapWizard({
        onTabShow: function(tab, navigation, index) {
            // alert(index);
            var customer_unique_id = $("#customer_unique_id").val();

            if (index != 0) {
                if (!customer_unique_id) {
                    
                    sweetalert("custom",'','','Create Customer First');
                    $('#customercreatewizard').find("a[href*='profile_tab']").trigger('click');
                    return event.preventDefault(), event.stopPropagation(), !1;
                }
            }

            // console.log(index);
            var $total   = navigation.find('li').length;
            var $current = index+1;
            var $percent = ($current/$total) * 100;
            
   
    
            $('#customercreatewizard').find('.bar').css({width:$percent+'%'});

            // If it's the last tab then hide the last button and show the finish instead
            if($current >= $total) {
                $('#customercreatewizard').find('.pager .next').hide();
                $('#customercreatewizard').find('.pager .finish').show();
                $('#customercreatewizard').find('.pager .finish').removeClass('disabled');
                // unique_id 	= $(".finish").data("unique-id");
            } else {
                $('#customercreatewizard').find('.pager .next').show();
                $('#customercreatewizard').find('.pager .finish').hide();
            }
            
            if (index != 0) {
                $(".createupdate_btn").text("Next");
            }

            handleTabLoadByIndex(index);

        },
        onNext: function (t, r, index) {

            
            if (index == 1) {
                var form_class = "customer_profile_form";
                var is_form = form_validity_check(form_class);
                var unique_id = $("#unique_id").val();
            
                if (!is_form) {
                    sweetalert("form_alert");
                    return event.preventDefault(), event.stopPropagation(), !1;
                } else {
                    var formData = new FormData(document.querySelector("." + form_class));
            
                    // Append manually because serialize() skips file input
                    formData.append("unique_id", unique_id);
                    formData.append("action", "createupdate");
            
                    var files = $("#test_file")[0].files;
                    for (var i = 0; i < files.length; i++) {
                        formData.append("test_file[]", files[i]);
                    }
            
                    var ajax_url = sessionStorage.getItem("folder_crud_link");
            
                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function () {
                            $(".createupdate_btn").addClass("disabled").text("Loading...");
                        },
                        success: function (data) {
                            var obj = JSON.parse(data);
                            var msg = obj.msg;
                            var status = obj.status;
                            var error = obj.error;
                            var customer_no = obj.customer_no;
                            var cus_id = obj.customer_unique_id;
            
                            if (!status) {
                                $(".createupdate_btn").text("Error");
                                console.log(error);
                            } else {
                                $("#customer_no").val(customer_no);
                                $(".createupdate_btn").removeClass("disabled").text(unique_id ? "Update & Continue" : "Save & Continue");
            
                                sweetalert(msg, ""); // No redirect
                                if (msg !== "already") {
                                    $("#customer_unique_id").val(cus_id);
                                    $('#customercreatewizard').find("a[href*='contactperson_tab']").trigger('click');
                                } else {
                                    $('#customercreatewizard').find("a[href*='profile_tab']").trigger('click');
                                }
                            }
                        },
                        error: function () {
                            alert("Network Error");
                        }
                    });
            
                    return event.preventDefault(), event.stopPropagation(), !1;
                }
            }

        },
        onTabClick: function(tab, navigation, index) {
            // return false;
            // return event.preventDefault(), event.stopPropagation(), !1;
        }
    });
    $('#customercreatewizard .finish').click(function() {
        var url      = sessionStorage.getItem("list_link");
        sweetalert("create",url);
        // alert('Finished!, Starting over!');
        // $('#customercreatewizard').find("a[href*='tab1']").trigger('click');
    });

});

function handleTabLoadByIndex(index) {
    // alert(index);
    switch (index) {
        case 1:
            sub_list_datatable(contact_person_tableid);
            break;
        case 2:
            sub_list_datatable(account_details_tableid);
            break;
        case 4:
            sub_list_datatable(shipping_details_tableid);
            break;
        case 3:
            sub_list_datatable(billing_details_tableid);
            break;
        case 5:
            sub_list_datatable(documents_tableid);
            break;
        default:
            break;
    }
}

function init_datatable(table_id='',form_name='',action='') {
    
    // alert(table_id);
    
    var table = $("#"+table_id);
    
    // alert(table);
    
	var data 	  = {
		"action"	: action, 
	};
	
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
	
        "searching" : true,
	"columnDefs": [
        
            { className:  "text-center", "width" : "5%","targets": [ 0,-1 ] },
        ],
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
    
    console.info(datatable);
    // alert(data);
    
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



// Get city Names Based On State Selection
function get_cities (state_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (state_id) {
		var data = {
			"state_id" 	    : state_id,
			"action"		: "cities"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) {
					$("#city_name").html(data);
				}

			}
		});
	}
}  

// Contact Person CU
function contact_person_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var customer_unique_id = $("#customer_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("","contact_person_form");

    console.log(is_form);

    if (is_form) {

        var data 	 = $(".contact_person_form").serialize();
        data 		+= "&customer_unique_id="+customer_unique_id;
        data 		+= "&unique_id="+unique_id+"&action=contact_person_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".contact_person_add_update_btn").attr("disabled","disabled");
				$(".contact_person_add_update_btn").text("Loading...");
			},
			success		: function(data) {

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
                    sub_list_datatable("contact_person_datatable");

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

function sub_list_datatable (table_id = "", form_name = "", action = "") {
    // alert("test");
    var customer_unique_id = $("#customer_unique_id").val();
    
    var table = $("#"+table_id);
	var data 	  = {
        "customer_unique_id"    : customer_unique_id,
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

function contact_person_edit(unique_id = "") {
    if (unique_id) {
        var data 		= "unique_id="+unique_id+"&action=contact_person_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".contact_person_add_update_btn").attr("disabled","disabled");
				$(".contact_person_add_update_btn").text("Loading...");
			},
			success		: function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

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

                    $("#contact_person_name").val(contact_person_name);
                    $("#contact_person_designation").val(contact_person_designation);
                    // $("#contact_person_address1").val(contact_person_address1);
                    // $("#contact_person_address2").val(contact_person_address2);
                    $("#contact_person_email").val(contact_person_email);
                    $("#contact_person_contact_no").val(contact_person_contact_no);

                    // Button Change 
                    $(".contact_person_add_update_btn").removeAttr("disabled","disabled");
                    $(".contact_person_add_update_btn").text("Update");
                    $(".contact_person_add_update_btn").attr("onclick","contact_person_add_update('"+unique_id+"')");
				}
			},
			error 		: function(data) {
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
                    "unique_id" 	: unique_id,
                    "action"		: "contact_person_delete"
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
                            sub_list_datatable("contact_person_datatable");
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


// Invoice Details ADD & UPDATE
function invoice_details_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var customer_unique_id = $("#customer_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("invoice_details_form");

    console.log(is_form);

    if (is_form) {

        var data 	 = $(".invoice_details_form").serialize();
        data 		+= "&customer_unique_id="+customer_unique_id;
        data 		+= "&unique_id="+unique_id+"&action=invoice_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".invoice_details_add_update_btn").attr("disabled","disabled");
				$(".invoice_details_add_update_btn").text("Loading...");
			},
			success		: function(data) {

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".invoice_details_add_update_btn").text("Error");
                    console.log(error);
				} else {
					if (msg !=="already") {
						form_reset("invoice_details_form");
                    }
                    $(".invoice_details_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".invoice_details_add_update_btn").text("Update");
                    } else {
                        $(".invoice_details_add_update_btn").text("Add");
                        $(".invoice_details_add_update_btn").attr("onclick","invoice_details_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("invoice_details_datatable");
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

function invoice_details_edit(unique_id = "") {
    if (unique_id) {
        var data 		= "unique_id="+unique_id+"&action=invoice_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".invoice_details_add_update_btn").attr("disabled","disabled");
				$(".invoice_details_add_update_btn").text("Loading...");
			},
			success		: function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".invoice_details_add_update_btn").text("Error");
                    console.log(error);
				} else {
                    console.log(obj);
                    var delivery_details            = data.delivery_details;
                    var invoice_details             = data.invoice_details;
                    var transport_courier_details   = data.transport_courier_details;
                    var gst_no                      = data.gst_no;
                    var tan_no                      = data.tan_no;
                    var pan_no                      = data.pan_no;
                    var web_address                 = data.web_address;
                    var email_id                    = data.email_id;

                    $("#delivery_details").val(delivery_details);
                    $("#invoice_details").val(invoice_details);
                    $("#transport_courier_details").val(transport_courier_details);
                    $("#gst_no").val(gst_no);
                    $("#tan_no").val(tan_no);
                    $("#pan_no").val(pan_no);
                    $("#web_address").val(web_address);
                    $("#email_id").val(email_id);

                    // Button Change 
                    $(".invoice_details_add_update_btn").removeAttr("disabled","disabled");
                    $(".invoice_details_add_update_btn").text("Update");
                    $(".invoice_details_add_update_btn").attr("onclick","invoice_details_add_update('"+unique_id+"')");
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
    }
}

function invoice_details_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "invoice_details_delete"
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
                            sub_list_datatable("invoice_details_datatable");
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

// Customer Potential Mapping ADD & UPDATE
function customer_potential_mapping_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var customer_unique_id = $("#customer_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("customer_potential_mapping_form");

    // console.log(is_form);

    if (is_form) {

        var data 	 = $(".customer_potential_mapping_form").serialize();
        data 		+= "&customer_unique_id="+customer_unique_id;
        data 		+= "&unique_id="+unique_id+"&action=customer_potential_mapping_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".customer_potential_mapping_add_update_btn").attr("disabled","disabled");
				$(".customer_potential_mapping_add_update_btn").text("Loading...");
			},
			success		: function(data) {

				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".customer_potential_mapping_add_update_btn").text("Error");
                    console.log(error);
				} else {
					if (msg !=="already") {
						form_reset("customer_potential_mapping_form");
                        var newValue = Number($("#cpm_value").val()) + 1;

                        // Update the field with new value
                        $("#cpm_value").val(newValue);
                    }
                    $(".customer_potential_mapping_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".customer_potential_mapping_add_update_btn").text("Update");
                    } else {
                        $(".customer_potential_mapping_add_update_btn").text("Add");
                        $(".customer_potential_mapping_add_update_btn").attr("onclick","customer_potential_mapping_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("customer_potential_mapping_datatable");
 
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

function customer_potential_mapping_edit(unique_id = "") {
    if (unique_id) {
        var data 		= "unique_id="+unique_id+"&action=customer_potential_mapping_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".customer_potential_mapping_add_update_btn").attr("disabled","disabled");
				$(".customer_potential_mapping_add_update_btn").text("Loading...");
			},
			success		: function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".customer_potential_mapping_add_update_btn").text("Error");
                    console.log(error);
				} else {
                    console.log(obj);
                    var s_no              = data.s_no;
                    var financial_year    = data.financial_year;
                    var product_group     = data.item_group_unique_id;
                    var potential_value   = data.potential_value;
                    var bis_forcast       = data.bis_forcast;

                    $("#s_no").val(s_no);
                    $("#financial_year").val(financial_year).trigger("change");
                    $("#product_group").val(product_group).trigger("change");
                    $("#potential_value").val(potential_value);
                    $("#bis_forcast").val(bis_forcast);

                    // Button Change 
                    $(".customer_potential_mapping_add_update_btn").removeAttr("disabled","disabled");
                    $(".customer_potential_mapping_add_update_btn").text("Update");
                    $(".customer_potential_mapping_add_update_btn").attr("onclick","customer_potential_mapping_add_update('"+unique_id+"')");
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
    }
}

function customer_potential_mapping_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "customer_potential_mapping_delete"
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
                            sub_list_datatable("customer_potential_mapping_datatable");
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
function account_details_add_update (unique_id = "") { // au = add,update   
// alert("4444");

    var internet_status  = is_online();

    var customer_unique_id = $("#customer_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("account_details_form");

    // console.log(is_form);

    if (is_form) {

        var data 	 = $(".account_details_form").serialize();
        data 		+= "&customer_unique_id="+customer_unique_id;
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
                        $(".account_details_add_update_btn").attr("onclick","account_details_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("account_details_datatable");

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
                    var bank_address                = data.bank_address;
                    var ifsc_code                   = data.ifsc_code;
                    var beneficiary_account_name    = data.beneficiary_account_name;
                    var account_no                  = data.account_no;

                    $("#bank_name").val(bank_name);
                    $("#bank_address").val(bank_address);
                    $("#ifsc_code").val(ifsc_code);
                    $("#beneficiary_account_name").val(beneficiary_account_name);
                    $("#account_no").val(account_no);

                    // Button Change 
                    $(".account_details_add_update_btn").removeAttr("disabled","disabled");
                    $(".account_details_add_update_btn").text("Update");
                    $(".account_details_add_update_btn").attr("onclick","account_details_add_update('"+unique_id+"')");
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
                            sub_list_datatable("account_details_datatable");
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

function customer_toggle(unique_id = "", new_status = 0) {
    const ajax_url = sessionStorage.getItem("folder_crud_link");
    const url = sessionStorage.getItem("list_link");

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            unique_id: unique_id,
            action: "toggle",
            is_active: new_status
        },
        success: function (data) {
            const obj = JSON.parse(data);
            const msg = obj.msg;
            const status = obj.status;

            if (status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }

            sweetalert(msg, url);
        }
    });
}

//approval
function customer_approval(unique_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");
    
    confirm_approve('approve')
    .then((result) => {
        if (result.isConfirmed) {

            var data = {
                "unique_id"     : unique_id,
                "action"        : "approve"
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


// billing Details ADD & UPDATE
function billing_details_add_update (unique_id = "") {

    var internet_status  = is_online();

    var customer_unique_id = $("#customer_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("billing_details_form");


    if (is_form) {

        var data 	 = $(".billing_details_form").serialize();
        data 		+= "&customer_unique_id="+customer_unique_id;
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
                    sub_list_datatable("billing_details_datatable");
   
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
                
                 console.log($("#bill_contact_name").length);


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
                            sub_list_datatable("billing_details_datatable");
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

// Shipping Details ADD & UPDATE
function shipping_details_add_update (unique_id = "") {

    var internet_status  = is_online();

    var customer_unique_id = $("#customer_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("shipping_details_form");


    if (is_form) {

        var data 	 = $(".shipping_details_form").serialize();
        data 		+= "&customer_unique_id="+customer_unique_id;
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
                    sub_list_datatable("shipping_details_datatable");

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
                $("#shipping_ecc_no").val(data.ecc_no || "");

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
                            sub_list_datatable("shipping_details_datatable");
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


function documents_add_update(unique_id = "") {
    var internet_status = is_online();
    var data = new FormData();

    var type = $("#type").val();
    var customer_unique_id = $("#customer_unique_id").val();
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
        } else {
            // If no new file, send existing file info if present
            var existing_file = $("#existing_file_attach").val();
            if (existing_file) {
                data.append("existing_file_attach", existing_file);
            }
        }

        data.append("customer_unique_id", customer_unique_id);
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

                // If backend returns invalid_file_format, show alert
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
                    // Init Datatable
                    sub_list_datatable("documents_datatable");
                    sweetalert(msg, url);
                }
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

$(document).ready(function () {
    sub_list_datatable("account_details_datatable");
    sub_list_datatable("billing_details_datatable");
    sub_list_datatable("shipping_details_datatable");
    sub_list_datatable("documents_datatable");
});
