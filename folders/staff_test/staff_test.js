var contact_person_tableid = "contact_person_datatable";
var delivery_details_tableid = "dependent_details_datatable";
var qualification_datatable_tableid = "qualification_datatable";
var experience_datatable_tableid = "experience_datatable";
var account_details_tableid = "staff_account_details_datatable";
var asset_tableid = "asset_datatable";
var emp_status_tableId = "employment_status_datatable";

var form_name = 'staff';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'staff_datatable';
var action = "datatable";

const ajax_url = sessionStorage.getItem("folder_crud_link");

// document.querySelectorAll("button[type='button']").forEach(btn => {
//     if (btn.innerText.trim() === "Cancel") {
//         btn.addEventListener("click", function (e) {
//             e.preventDefault();

//             const confirmCancel = confirm("Are you sure you want to cancel?");
//             if (!confirmCancel) return;

//             var staffIdElem = document.getElementById("staff_unique_id");
//             var uniqueIdElem = document.getElementById("unique_id");
            
//             var staffId = staffIdElem ? staffIdElem.value : "";
//             var uniqueId = uniqueIdElem ? uniqueIdElem.value : "";

//             if (!staffId || uniqueId) {
//                 console.log("No staff ID found. Nothing to cancel.");
//                 window.location.href = url;
//                 return;
//             }

//             $.ajax({
//                 type: "POST",
//                 url: ajax_url,
//                 data: {
//                     action: "staff_master_delete",
//                     staff_unique_id: staffId
//                 },
//                 beforeSend: function () {
//                     console.log("Deleting staff record...");
//                 },
//                 success: function (response) {
//                     try {
//                         const res = JSON.parse(response);
//                         if (res.status) {
//                             alert("Staff record successfully deleted.");
//                             const listUrl = sessionStorage.getItem("list_link") || "folders/staff/list.php";
//                             window.location.href = listUrl;
//                         } else {
//                             alert("Delete failed: " + (res.msg || "Unknown error"));
//                         }
//                     } catch (err) {
//                         console.error("Invalid JSON response:", response);
//                         alert("Server error. Please try again.");
//                     }
//                 },
//                 error: function () {
//                     alert("AJAX error. Check your connection or try again.");
//                 }
//             });
//         });
//     }
// });


$(document).ready(function () {
    let lastActiveTabIndex = null;

    $('.form-wizard-header .nav-link').on('shown.bs.tab', function (e) {

        const $newTab = $(e.target); // the tab that was just activated
        const tabIndex = $newTab.parent().index();
        const uniqueId = $('#unique_id').val();
        
        $('#tab_count').val(tabIndex);     // <-- Update hidden input
        toggleCreateUpdateBtn();           // <-- Trigger visibility toggle


        if (!uniqueId) {
            // Tab already switched visually, but prevent further logic
            // alert("Please save the previous tabs first.");
            return;
        }

        if (tabIndex === lastActiveTabIndex) {
            return; // Skip if same tab is clicked again
        }
        
        // if (tabIndex === 8){
        //     const btn = $("#createupdate_btn");
        //     console.log(btn.style.display);
        //     btn.style.display = "none";
        // } else {
        //     btn.style.display = "block";
        // }

        handleTabLoadByIndex(tabIndex);
        lastActiveTabIndex = tabIndex;
    });
});

function toggleCreateUpdateBtn() {
    const tabIndex = parseInt($('#tab_count').val(), 10);
    const $btn = $('.createupdate_btn');

    if (tabIndex === 8) {
        $btn.css('display', 'none');
    } else {
        $btn.css('display', 'block');
    }
}



function staff_master_sc() {
    const activeTab = $('#staffcreatewizard .nav-link.active').attr('href');
    
    const tabHandlers = {
        '#officialdetails_tab': handleStaffProfile,
        '#employment_status_tab': handleEmploymentStatus,
        '#dependentdetails_tab': handleDependentDetails,
        '#account_details_tab': handleAccountDetails,
        '#qualification_tab': handleQualification,
        '#experience_tab': handleExperience,
        '#statuatory_tab': handlestatuatoryDetails,
        '#salary_tab': handleSalaryDetails,
        '#relieve_tab': handleRelieveDetails
    };

    if (tabHandlers[activeTab]) {
        tabHandlers[activeTab]();
        console.log(`Handling submission for tab: ${activeTab}`);
    } else {
        console.error('No handler for active tab:', activeTab);
    }
}


function handleStaffProfile() {
    const form = document.getElementById('staff_profile_form');
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    formData.append('unique_id', document.getElementById('unique_id').value);
    formData.append('action', 'createupdate');

    submitForm(ajax_url, formData, function(response) {
        if (response.unique_id) {
            document.getElementById('unique_id').value = response.unique_id;
            document.getElementById('staff_unique_id').value = response.staff_unique_id;
        }
        $("#staff_unique_id").val(response.staff_unique_id);
        
        let count = parseInt($("#profile_count").val() || "0", 10);
        $("#profile_count").val(count + 1);
        
        let unique_id = document.getElementById('unique_id').value;
        if(unique_id) {
            handleTabLoadByIndex(1);
        }
        moveToNextTab(1);
    });
}

function handleEmploymentStatus() {
    const from = document.getElementById('employment_status_form');
    moveToNextTab(2);
}


function handleDependentDetails() {
    const from = document.getElementById('dependent_details_form');
    moveToNextTab(3);
}


function handleAccountDetails() {
    const form = document.getElementById('account_details_form');
    moveToNextTab(4);
}


function handleQualification() {
    const form = document.getElementById('qualification_form');
    moveToNextTab(5);
}


function handleExperience() {
    const form = document.getElementById('experience_form');
    moveToNextTab(6);
}

function handlestatuatoryDetails() {
    const form_id = "statuatory_form";
    const formElement = document.querySelector(`.${form_id}`);

    if (!formElement) {
        // alert("Form not found.");
        return;
    }

    
    const is_form = form_validity_check(form_id);
    if (!is_form) {
        sweetalert("form_alert");
        return;
    }

    const formData = new FormData(formElement);

    
    formData.append('unique_id', document.getElementById('unique_id').value);
    formData.append('staff_unique_id', document.getElementById('staff_unique_id').value);
    formData.append('action', 'statcreateupdate');

    
    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    
    submitForm(ajax_url, formData, function(responseData) {
        
        if (document.getElementById('unique_id').value) {
            handleTabLoadByIndex(7);
        }
        
        
        moveToNextTab(7);
    });
}



function handleSalaryDetails() {
    const form_id = "salary_form";
    const formElement = document.querySelector(`.${form_id}`);

    if (!formElement) {
        // alert("Salary form not found.");
        return;
    }

    const is_form = form_validity_check(form_id);
    if (!is_form) {
        sweetalert("form_alert");
        return;
    }

    const formData = new FormData(formElement);
    
    for (let [key, value] of formData.entries()) {
        console.info(`${key}: ${value}`);
    }

    formData.append('unique_id', document.getElementById('unique_id').value);
    formData.append('staff_unique_id', document.getElementById('staff_unique_id').value);
    formData.append('action', 'salarycreateupdate');

    for (let [key, value] of formData.entries()) {
        console.log(`${key}: ${value}`);
    }

    submitForm(ajax_url, formData, function(responseData) {
        if (document.getElementById('unique_id').value) {
            handleTabLoadByIndex(8);
        }

        moveToNextTab(8);
    });
}



function handleRelieveDetails() {
    const form_id = "relieve_form";
    const formElement = document.querySelector(`.${form_id}`);

    if (!formElement) {
        // alert("Form not found.");
        return;
    }

    const is_form = form_validity_check(form_id);
    if (!is_form) {
        sweetalert("form_alert");
        return;
    }

    const formData = new FormData(formElement);

    formData.append('unique_id', document.getElementById('unique_id').value);
    formData.append('staff_unique_id', document.getElementById('staff_unique_id').value);
    formData.append('action', 'relievecreateupdate');

    // Debug log
    for (let [key, value] of formData.entries()) {
        console.info(`${key}: ${value}`);
    }

    submitForm(ajax_url, formData, function (responseData) {
        if (document.getElementById('unique_id').value) {
            handleTabLoadByIndex(8); // refresh current tab content
        }
        moveToNextTab(9);
    });
}



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
                    if (data.msg == 'create' || data.msg == 'update') {
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
    const tabs = $('#staffcreatewizard .nav-link');
    const currentIndex = tabs.index($('#staffcreatewizard .nav-link.active'));
    
    console.log('Current:', currentIndex, 'Tabs total:', tabs.length);


    if (currentIndex < tabs.length - 1) {
        tabs.eq(currentIndex + 1).tab('show');
        document.querySelector('.bar').style.width = `${(stepNumber / 9) * 100}%`;
    }
    
    handleTabLoadByIndex(stepNumber);
}

function showError(message) {
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: message,
        confirmButtonColor: '#3bafda'
    });
}

function final_submit() {
    // Validation temporarily disabled
    
    const tab_count = parseInt($("#tab_count").val() || 0);
    
    if(tab_count === 8){
        // alert("releive");
        handleRelieveDetails();
    } else if(tab_count === 0) {
        handleStaffProfile();
    }

    const requiredFields = [
        '#profile_count',
        // '#emp_status_count',
        // '#dependent_count',
        // '#account_count',
        // '#qualification_count',
        // '#exp_count'
    ];

    let allValuesValid = true;
    const invalidFields = [];

    requiredFields.forEach(id => {
        const $field = $(id);
        const value = $field.val()?.trim();

        if (!value || value === '0') {
            allValuesValid = false;
            invalidFields.push(id);
            $field.addClass('is-invalid');
        } else {
            $field.removeClass('is-invalid');
        }
    });

    if (!allValuesValid) {
        // alert(allValuesValid);
        console.log(invalidFields);
        Swal.fire({
            icon: 'error',
            title: 'Validation Error',
            text: 'Please complete all mandatory fields before final submission.',
            confirmButtonText: 'OK'
        });
        return;
    }
    

    // Immediate redirect without validation
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Staff profile created successfully.',
        confirmButtonColor: '#3bafda'
    }).then(() => {
        const redirectUrl = sessionStorage.getItem('list_link') || window.location.href;
        window.location.href = redirectUrl;
    });
}



function handleTabLoadByIndex(index) {
    
    const currentUser = 'SIWNUS';
    const currentDateTime = '2025-07-11 07:01:49';

    console.log(`Tab ${index} loaded by ${currentUser} at ${currentDateTime}`);

    switch (index) {
        case 1: 
            sub_list_datatable(emp_status_tableId);
            console.log('Loading employment status details');
            break;

        case 2: 
            sub_list_datatable(delivery_details_tableid);
            console.log('Loading dependent details');
            break;

        case 3: 
            sub_list_datatable(account_details_tableid);
            console.log('Loading account details');
            break;

        case 4: 
            sub_list_datatable(qualification_datatable_tableid);
            console.log('Loading qualification details');
            break;

        case 5: 
            sub_list_datatable(experience_datatable_tableid);
            console.log('Loading experience details');
            break;

        case 6: 
            
            console.log('Loading statuatory form');
            loadstatuatoryForm();
            break;

        case 7: 
            
            console.log('Loading salary form');
            loadSalaryForm();
            break;

        case 8: 
            
            console.log('Loading relieve form');
            loadRelieveForm();
            break;

        default:
            console.log(`No specific loading action for tab index ${index}`);
            break;
    }

    
    logTabAccess(index);
}


function logTabAccess(tabIndex) {
    const currentUser = 'SIWNUS';
    const currentDateTime = '2025-07-11 07:01:49';
    
    const tabNames = {
        1: 'Employment Status',
        2: 'Dependent Details',
        3: 'Account Details',
        4: 'Qualification',
        5: 'Experience',
        6: 'statuatory',
        7: 'Salary',
        8: 'Relieve'
    };

    console.log({
        event: 'Tab Access',
        tab: tabNames[tabIndex] || 'Unknown Tab',
        index: tabIndex,
        user: currentUser,
        timestamp: currentDateTime,
        staff_id: document.getElementById('staff_unique_id')?.value || 'Not Set'
    });
}


function loadstatuatoryForm() {
    const staff_unique_id = document.getElementById('staff_unique_id')?.value;
    
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    
    if (!staff_unique_id) {
        console.warn('Staff ID not found');
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_statuatory_details",
            staff_unique_id: staff_unique_id
        },
        success: function(response) {
            console.info(response);
            try {
                const data = JSON.parse(response);
                if (data.status && data.data) {
                    populatestatuatoryForm(data.data);
                }
            } catch (error) {
                console.error('Error loading statuatory data:', error);
            }
        },
        error: function() {
            console.error('Failed to load statuatory data');
        }
    });
}

function loadSalaryForm() {
    const staff_unique_id = document.getElementById('staff_unique_id')?.value;
        var ajax_url = sessionStorage.getItem("folder_crud_link");

    
    if (!staff_unique_id) {
        console.warn('Staff ID not found');
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_salary_details",
            staff_unique_id: staff_unique_id
        },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.status && data.data) {
                    populateSalaryForm(data.data);
                }
            } catch (error) {
                console.error('Error loading salary data:', error);
            }
        },
        error: function() {
            console.error('Failed to load salary data');
        }
    });
}

function loadRelieveForm() {
    const staff_unique_id = document.getElementById('staff_unique_id')?.value;
        var ajax_url = sessionStorage.getItem("folder_crud_link");

    
    if (!staff_unique_id) {
        console.warn('Staff ID not found');
        return;
    }

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: {
            action: "get_relieve_details",
            staff_unique_id: staff_unique_id
        },
        success: function(response) {
            try {
                const data = JSON.parse(response);
                if (data.status && data.data) {
                    populateRelieveForm(data.data);
                }
            } catch (error) {
                console.error('Error loading relieve data:', error);
            }
        },
        error: function() {
            console.error('Failed to load relieve data');
        }
    });
}


function populatestatuatoryForm(data) {
    
    const formData = data[0];
    console.log('Populating form with:', formData);

    
    const fields = [
        'pf_applicable',
        'employee_pf_ceiling',
        'pf_joining_date',
        'uan_number',
        'vpf',
        'pf_pension',
        'employer_pf_ceiling',
        'pf_number',
        'pf_wage',
        'esic_applicable',
        'pt_applicable',
        'it_applicable',
        'nps_applicable',
        'lwf_applicable',
        'gratuity_applicable',
        'tax_regime',
        'tax_no_pan',
        'decimal_rates'
    ];

    fields.forEach(fieldId => {
        
        if (['pf_applicable', 'employee_pf_ceiling', 'pf_pension', 
             'employer_pf_ceiling', 'esic_applicable', 'pt_applicable', 
             'it_applicable', 'nps_applicable', 'lwf_applicable', 
             'gratuity_applicable', 'tax_no_pan', 'decimal_rates'].includes(fieldId)) {
            
            const value = formData[fieldId];
            if (value !== null) {
                const radio = document.querySelector(`input[name="${fieldId}"][value="${value}"]`);
                if (radio) {
                    radio.checked = true;
                    console.log(`Set radio ${fieldId} to ${value}`);
                }
            }
        } 
        
        else if (fieldId === 'tax_regime') {
            const select = document.getElementById(fieldId);
            if (select && formData[fieldId]) {
                select.value = formData[fieldId];
                console.log(`Set select ${fieldId} to ${formData[fieldId]}`);
            }
        }
        
        else {
            const field = document.getElementById(fieldId);
            if (field && formData[fieldId]) {
                field.value = formData[fieldId];
                console.log(`Set field ${fieldId} to ${formData[fieldId]}`);
            }
        }
    });

    
    if (formData.tax_updated_at) {
        const taxUpdatedAt = document.getElementById('tax_updated_at');
        if (taxUpdatedAt) {
            taxUpdatedAt.value = formData.tax_updated_at;
        }
    }

    if (formData.tax_updated_by) {
        const taxUpdatedBy = document.getElementById('tax_updated_by');
        if (taxUpdatedBy) {
            taxUpdatedBy.value = formData.tax_updated_by;
        }
    }

    console.log('Form population completed');
}

function populateSalaryForm(data) {
    const fieldMap = {
        basic_wages: "basic",
        hra: "hra",
        stat_bonus: "statutory_bonus",
        special_allowance: "special_allowance",
        other_allowance: "other_allowance"
    };

    Object.keys(data).forEach(key => {
        const fieldId = fieldMap[key] || key;
        const field = document.getElementById(fieldId);
        if (field) {
            field.value = data[key];
        }
    });
    
    triggerSalaryRecalc();
}


function populateRelieveForm(data) {
    
    Object.keys(data).forEach(key => {
        const field = document.getElementById(key);
        if (field) {
            field.value = data[key];
        }
    });
}

$(document).ready(function () {

    $("#excel_export").on("click", function() {
        window.location.href = 'folders/staff_test/excel.php';
    });

    $("#work_location").on("change", function () {
        $("#project_name").val($(this).val());
    });
    
    datatable_init_based_on_prev_state();
    
    var unique_id = $("#unique_id").val();
    
    if (unique_id){
        $("#profile_count").val(1);
    }
    
    $('#pf_number').on('input', function () {
        const val = $(this).val();
        if (val.length > 12) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid PF Number',
                text: 'PF Number cannot exceed 12 characters.',
                confirmButtonText: 'OK'
            });
            $(this).val(val.slice(0, 12)); // Trim it to 12 characters
        }
    });
    $('#uan_number').on('input', function () {
        const val = $(this).val();
        if (val.length > 12) {
            Swal.fire({
                icon: 'warning',
                title: 'Invalid UAN Number',
                text: 'UAN Number cannot exceed 12 characters.',
                confirmButtonText: 'OK'
            });
            $(this).val(val.slice(0, 12)); // Trim it to 12 characters
        }
    });

    
    $('#staffcreatewizard').bootstrapWizard({
        onTabShow: function (tab, navigation, index) {
            wizardTotalTabs = navigation.find('li').length;
            wizardCurrentTab = index + 1;
            
            const $percent = (wizardCurrentTab / wizardTotalTabs) * 100;
            $('#staffcreatewizard').find('.bar').css({ width: $percent + '%' });
            
            const $nextBtn = $('#createupdate_btn');
            const $finishBtn = $('.createupdate_btn_finish').parent();
            
            if (wizardCurrentTab >= wizardTotalTabs) {
                $nextBtn.parent().hide();
                $finishBtn.show();
            } else {
                $nextBtn.parent().show();
                $finishBtn.hide();
            }
            
            // Set next button label based on index
            if ([0, 6, 7].includes(index)) {
                $nextBtn.text($("#unique_id").val() ? "Update & Continue" : "Save & Continue");
            } else {
                $nextBtn.text("Next & Continue");
            }
            
            handleTabLoadByIndex(index);
            }

    });

    
    $('#createupdate_btn').on('click', function(e) {
        e.preventDefault();
    
        const currentTab = $('#staffcreatewizard .nav-link.active').parent().index();
        console.log(`Current tab index: ${currentTab}`);
    
        const unique_id = $("#unique_id").val();
        const staff_unique_id = $("#staff_unique_id").val();
        const ajax_url = sessionStorage.getItem("folder_crud_link");
    
        
        switch(currentTab) {
            case 0: 
                var form_class = "staff_profile_form";
                var is_form = form_validity_check(form_class);
                if (!is_form) {
                    sweetalert("form_alert");
                    return false;
                }
                
                var data = $("." + form_class).serialize();
                data += "&unique_id=" + unique_id + "&action=createupdate";
                console.info(data);
                
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    beforeSend: function() {
                        $("#createupdate_btn").addClass("disabled").text("Loading...");
                    },
                    success: function(response) {
                        var obj = JSON.parse(response);
                        if (obj.status) {
                            if (obj.staff_unique_id) {
                                $("#staff_unique_id").val(obj.staff_unique_id);
                                file_upload(obj.staff_unique_id);
                            }
                            
                            sweetalert(obj.msg || "Success", "");
                        } else {
                            sweetalert("error", obj.error || "Operation failed");
                        }
                    },
                    error: function() {
                        alert("Network Error");
                    },
                    complete: function() {
                        $("#createupdate_btn").removeClass("disabled")
                            .text(unique_id ? "Update & Continue" : "Save & Continue");
                    }
                });
                break;
    
            case 6: 
                var form_id = "statuatory_form";
                // alert("#"+form_id);
                var is_form = form_validity_check(form_id);
                var data = $("." + form_id).serialize();
                console.info(data);
                // alert(data);
                data += "&unique_id=" + unique_id + 
                       "&action=statcreateupdate" + 
                       "&staff_unique_id=" + staff_unique_id;
                       
                console.info(data);

                
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    beforeSend: function() {
                        $("#createupdate_btn").addClass("disabled").text("Loading...");
                    },
                    success: function(response) {
                        var obj = JSON.parse(response);
                        if (obj.status) {
                            
                            sweetalert(obj.msg || "Success", "");
                        } else {
                            sweetalert("error", obj.error || "Operation failed");
                        }
                    },
                    error: function() {
                        alert("Network Error");
                    },
                    complete: function() {
                        $("#createupdate_btn").removeClass("disabled")
                            .text(unique_id ? "Update & Continue" : "Save & Continue");
                    }
                });
                break;
    
            case 7: 
                var form_class = "salary_form";
                var is_form = form_validity_check(form_class);
                if (!is_form) {
                    sweetalert("form_alert");
                    return false;
                }
                
                var project_id = $("#project_name").val();
                
                var data = $("." + form_class).serialize();
                data += "&unique_id=" + unique_id + 
                        "&project_id=" + project_id +
                       "&action=salarycreateupdate" + 
                       "&staff_unique_id=" + staff_unique_id;
                       
                console.log(data);
                
                $.ajax({
                    type: "POST",
                    url: ajax_url,
                    data: data,
                    beforeSend: function() {
                        $("#createupdate_btn").addClass("disabled").text("Loading...");
                    },
                    success: function(response) {
                        var obj = JSON.parse(response);
                        if (obj.status) {
                            let count = parseInt($("#salary_count").val() || "0", 10);
                            $("#salary_count").val(count + 1);
        
                            sweetalert(obj.msg || "Success", "");
                        } else {
                            sweetalert("error", obj.error || "Operation failed");
                        }
                    },
                    error: function() {
                        alert("Network Error");
                    },
                    complete: function() {
                        $("#createupdate_btn").removeClass("disabled")
                            .text(unique_id ? "Update & Continue" : "Save & Continue");
                    }
                });
                break;
    
            case 8: 
                $('.createupdate_btn_finish').trigger('click');
                break;
    
            default:
                
                
                break;
        }
    });

    
    $('.form-wizard-header .nav-link').on('click', function (e) {
        const tabIndex = $(this).parent().index();
        handleTabLoadByIndex(tabIndex);
    });

    
    $('.createupdate_btn_finish').on('click', function(e) {
        e.preventDefault();

        var staff_unique_id = $("#staff_unique_id").val();
        var unique_id = $("#unique_id").val();
        var form_class = "relieve_form";
        var data = $("." + form_class).serialize();
        data += "&unique_id=" + unique_id + "&action=relievecreateupdate";
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function(data) {
                var obj = JSON.parse(data);
                if (!obj.status) {
                    $(".createupdate_btn").text("Error");
                    console.log(obj.error);
                } else {
                    sweetalert(obj.msg || "Success", url);
                }
            },
            error: function() {
                alert("Network Error");
            }
        });
    });

    
    $('#company_name').on('change', function() {
        const selectedCompany = $(this).val();
        const unique_id = $("#unique_id").val();

        if (!unique_id) {
            console.log(`Getting employee ID for company: ${selectedCompany}`);
            get_employee_id(selectedCompany);
        }
    });

    
    $(document).on('input', '.per_month', function() {
        const monthId = $(this).attr('id');
        const value = parseFloat($(this).val());

        if (!isNaN(value)) {
            const annumValue = value * 12;
            $(`#annum_${monthId}`).val(annumValue.toFixed(2));
        } else {
            $(`#annum_${monthId}`).val('');
        }
    });

    
    $("#excel_export").click(function() {
        const staff_status = $('#staff_status').val();
        window.location = `folders/staff/excel.php?staff_status=${staff_status}`;
    });
});

function premises_check() {

    var status = $("input[name='premises_status']:checked").val();
    if (status != 0) {
        $("#staff_branch").attr("required", "required");

        $(".premises_in_div").removeClass("d-none");

    } else {

        $("#staff_branch").removeAttr("required", "required");
        $("#staff_branch").val("");

        $(".premises_in_div").addClass("d-none");

    }
}

function datatable_init_based_on_prev_state() {
    
    var staff_status = sessionStorage.getItem("staff_status");
    var filter_action = sessionStorage.getItem("expense_action");

    if (!staff_status) {
        staff_status = $("#staff_status").val();
    } else {
        $("#staff_status").val(staff_status);
    }

    if (!filter_action) {
        filter_action = 0;
    }

    
    var filter_data = {
        "status": staff_status,
        "filter_action": filter_action
    };

    
    init_datatable(table_id, form_name, action, filter_data);
}

function init_datatable(table_id = '', form_name = '', action = '', filter_data = '') {
    var table = $("#" + table_id);
    var data = {
        "action": action,
    };
    data = {
        ...data,
        ...filter_data
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var datatable = table.DataTable({
        ordering: true,
        searching: true,
        "searching": true,
        "ajax": {
            url: ajax_url,
            type: "POST",
            data: data
        },

    });
}

function get_qualification(graduation_type = "") {
    
    var graduation_type = document.getElementById('graduation_type').value;
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (graduation_type) {
        var data = {
            "graduation_type": graduation_type,
            "action": "get_qualification"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#qualification").html(data);
                }
            }
        });
    }
}

function get_designation(grade_type = "") {
    
    var grade_type = document.getElementById('grade').value;
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (grade_type) {
        var data = {
            "grade_type": grade_type,
            "action": "get_designation"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#designation").html(data);
                }
            }
        });
    }
}


function get_states(country_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (country_id) {
        var data = {
            "country_id": country_id,
            "action": "states"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#pre_state").html(data);
                }
            }
        });
    }
}

function get_work_location(company_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (company_id) {
        var data = {
            "company_id": company_id,
            "action": "work_location"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) { 

                if (data) {
                    $("#work_location").html(data);
                }
            }
        });
    }
}



function get_cities(state_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (state_id) {
        var data = {
            "state_id": state_id,
            "action": "cities"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#pre_city").html(data);
                }
            }
        });
    }
}


function get_perm_states(country_id = "") {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (country_id) {
        var data = {
            "country_id": country_id,
            "action": "perm_states"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#perm_state").html(data);

                    var edit_state_id = $("#edit_perm_state_id").val();

                    if (edit_state_id) {
                        $("#perm_state").val(edit_state_id).trigger('change');

                        $("#edit_perm_state_id").val('');
                    }
                }
            }
        });
    }
}


function get_employee_id(company_name) {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var data = {
        "action": "employee_id",
        "company_name": company_name
    };

    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        success: function (data) {
            if (data) {
                $("#employee_id").html(data);
                $("#staff_id").val(data);
            }
        }
    });
}


function get_perm_cities(state_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");

    if (state_id) {
        var data = {
            "state_id": state_id,
            "action": "perm_cities"
        }

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {

                if (data) {
                    $("#perm_city").html(data);

                    var edit_city_id = $("#edit_perm_city_id").val();

                    if (edit_city_id) {

                        $("#perm_city").val(edit_city_id).trigger('change');

                        $("#edit_perm_city_id").val('');
                    }
                }
            }
        });
    }
}

function sub_list_datatable(table_id = "", form_name = "", action = "") {

    var staff_unique_id = $("#staff_unique_id").val();
    // alert(staff_unique_id);

    var table = $("#" + table_id);
    var data = {
        "staff_unique_id": staff_unique_id,
        "action": table_id,
    };
    
    // alert(table);
    

    var datatable = table.DataTable({
        ordering: true,
        searching: true,
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
    
    console.info(datatable);
}


function asset_details_add_update(unique_id = "") { 

    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("asset_form");

    console.log(is_form);

    if (is_form) {

        var data = $(".asset_form").serialize();
        data += "&staff_unique_id=" + staff_unique_id;
        data += "&unique_id=" + unique_id + "&action=asset_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".asset_details_add_update_btn").attr("disabled", "disabled");
                $(".asset_details_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".asset_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {
                        form_reset("asset_form");
                    }
                    $(".asset_details_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".asset_details_add_update_btn").text("Update");
                    } else {
                        $(".asset_details_add_update_btn").text("Add");
                        $(".asset_details_add_update_btn").attr("onclick", "asset_details_add_update('')");
                    }
                    
                    sub_list_datatable("asset_datatable");
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

function asset_details_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=asset_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".asset_details_add_update_btn").attr("disabled", "disabled");
                $(".asset_details_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var data = obj.data;
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".asset_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var asset_name = data.asset_name;
                    var item_no = data.item_no;
                    var qty = data.qty;

                    var asset_status = data.asset_status;
                    var veh_reg_no = data.veh_reg_no;
                    var license_mode = data.license_mode;
                    var dri_license_no = data.dri_license_no;
                    var valid_from = data.valid_from;
                    var valid_to = data.valid_to;
                    var vehicle_type = data.vehicle_type;
                    var vehicle_company = data.vehicle_company;
                    var vehicle_owner = data.vehicle_owner;
                    var registration_year = data.registration_year;
                    var rc_no = data.rc_no;
                    var rc_validity_from = data.rc_validity_from;
                    var rc_validity_to = data.rc_validity_to;
                    var ins_no = data.ins_no;
                    var ins_validity_from = data.ins_validity_from;
                    var ins_validity_to = data.ins_validity_to;

                    

                    $("#asset_name").val(asset_name);
                    $("#item_no").val(item_no);
                    $("#qty").val(qty); 
                    $("#asset_status").val(asset_status).trigger("change");
                    
                    
                    $("#veh_reg_no").val(veh_reg_no);
                    $("#license_mode").val(license_mode).trigger("change");
                    $("#dri_license_no").val(dri_license_no);
                    $("#valid_from").val(valid_from);
                    $("#valid_to").val(valid_to);
                    $("#vehicle_type").val(vehicle_type);
                    $("#vehicle_company").val(vehicle_company);
                    $("#registration_year").val(registration_year);
                    $("#vehicle_owner").val(vehicle_owner);
                    $("#rc_no").val(rc_no);
                    $("#rc_validity_from").val(rc_validity_from);
                    $("#rc_validity_to").val(rc_validity_to);
                    $("#ins_no").val(ins_no);
                    $("#ins_validity_from").val(ins_validity_from);
                    $("#ins_validity_to").val(ins_validity_to);

                    
                    $(".asset_details_add_update_btn").removeAttr("disabled", "disabled");
                    $(".asset_details_add_update_btn").text("Update");
                    $(".asset_details_add_update_btn").attr("onclick", "asset_details_add_update('" + unique_id + "')");
                }
            },
            error: function (data) {
                alert("Network Error");
            }
        });
    }
}

function asset_details_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "asset_details_delete"
                    }

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
                                url = '';
                            } else {
                                sub_list_datatable("asset_datatable");
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    
                }
            });
    }
}


function dependent_details_add_update(unique_id = "") { 

    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("dependent_details_form");

    console.log(is_form);

    if (is_form) {

        var data = $(".dependent_details_form").serialize();
        data += "&staff_unique_id=" + staff_unique_id;
        data += "&unique_id=" + unique_id + "&action=dependent_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".dependent_details_add_update_btn").attr("disabled", "disabled");
                $(".dependent_details_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".dependent_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {
                        form_reset("dependent_details_form");
                    }
                    $(".dependent_details_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".dependent_details_add_update_btn").text("Update");
                    } else {
                        $(".dependent_details_add_update_btn").text("Add");
                        $(".dependent_details_add_update_btn").attr("onclick", "dependent_details_add_update('')");
                    }
                    
                    let count = parseInt($("#dependent_count").val() || "0", 10);
                    $("#dependent_count").val(count + 1);
        
                    
                    sub_list_datatable("dependent_details_datatable");
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

function dependent_details_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=dependent_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".dependent_details_add_update_btn").attr("disabled", "disabled");
                $(".dependent_details_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var data = obj.data;
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".dependent_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var relationship = data.relationship;
                    var name = data.name;
                    var gender = data.gender;
                    var aadhar_no = data.aadhar_no;
                    var occupation = data.occupation;
                    var standard = data.standard;
                    var school = data.school;
                    var existing_illness = data.existing_illness;
                    var existing_insurance = data.existing_insurance;
                    var illness_description = data.illness_description;
                    var insurance_no = data.insurance_no;
                    var physically_challenged = data.physically_challenged;
                    var remarks = data.remarks;
                    var date_of_birth = data.date_of_birth;

                    $("#relationship").val(relationship).trigger("change");
                    $("#rel_name").val(name);
                    $("#rel_gender").val(gender).trigger("change");
                    $("#rel_date_of_birth").val(date_of_birth);
                    $("#rel_aadhar_no").val(aadhar_no);
                    $("#occupation").val(occupation);
                    $("#standard").val(standard);
                    $("#school").val(school);
                    $("#existing_illness").val(existing_illness).trigger("change");
                    $("#description").val(illness_description);
                    $("#existing_insurance").val(existing_insurance).trigger("change");
                    $("#insurance_no").val(insurance_no);
                    $("#physically_challenged").val(physically_challenged).trigger("change");
                    $("#remarks").val(remarks);

                    
                    $(".dependent_details_add_update_btn").removeAttr("disabled", "disabled");
                    $(".dependent_details_add_update_btn").text("Update");
                    $(".dependent_details_add_update_btn").attr("onclick", "dependent_details_add_update('" + unique_id + "')");
                }
            },
            error: function (data) {
                alert("Network Error");
            }
        });
    }
}

function dependent_details_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "dependent_details_delete"
                    }

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
                                url = '';
                            } else {
                                sub_list_datatable("dependent_details_datatable");
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    
                }
            });
    }
}

function employment_status_add_update(unique_id = "") { 

    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("employment_status_form");

    console.log(is_form);

    if (is_form) {

        var data = $(".employment_status_form").serialize();
        data += "&staff_unique_id=" + staff_unique_id;
        data += "&unique_id=" + unique_id + "&action=employment_status_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".employment_status_add_update_btn").attr("disabled", "disabled");
                $(".employment_status_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".employment_status_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {
                        form_reset("employment_status_form");
                    }
                    $(".employment_status_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".employment_status_add_update_btn").text("Update");
                    } else {
                        $(".employment_status_add_update_btn").text("Add");
                        $(".employment_status_add_update_btn").attr("onclick", "employment_status_add_update('')");
                    }
                    
                    let count = parseInt($("#emp_status_count").val() || "0", 10);
                    $("#emp_status_count").val(count + 1);
                    
                    sub_list_datatable("employment_status_datatable");
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

function employment_status_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=employment_status_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".employment_status_add_update_btn").attr("disabled", "disabled");
                $(".employment_status_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var data = obj.data;
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".employment_status_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var effective_from = data.effective_from;
                    var effective_to = data.effective_to;
                    var conf_due_date = data.conf_due_date;
                    var conf_date = data.conf_date;
                    var employment_status = data.employment_status;
                    
                    $("#effective_from").val(effective_from);
                    $("#effective_to").val(effective_to);
                    $("#conf_due_date").val(conf_due_date);
                    $("#conf_date").val(conf_date);
                    $("#employment_status").val(employment_status).trigger("change");

                    
                    $(".employment_status_add_update_btn").removeAttr("disabled", "disabled");
                    $(".employment_status_add_update_btn").text("Update");
                    $(".employment_status_add_update_btn").attr("onclick", "employment_status_add_update('" + unique_id + "')");
                }
            },
            error: function (data) {
                alert("Network Error");
            }
        });
    }
}

function employment_status_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "employment_status_delete"
                    }

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
                                url = '';
                            } else {
                                sub_list_datatable("employment_status_datatable");
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    
                }
            });
    }
}


function qualification_add_update(unique_id = "") { 

    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();
    var education_type = $("#education_type").val();
    var degree = $("#degree").val();
    var college_name = $("#college_name").val();
    var year_passing = $("#year_passing").val();
    var percentage = $("#percentage").val();
    var university = $("#university").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("qualification_form");
    
    var data = new FormData();
    var image_s = document.getElementById("test_file_qual");

    
    for (var i = 0; i < image_s.files.length; i++) {
        data.append("test_file[]", document.getElementById('test_file_qual').files[i]);
    }
    

    data.append("education_type", education_type);
    data.append("degree", degree);
    data.append("college_name", college_name);
    data.append("year_passing", year_passing);
    data.append("percentage", percentage);
    data.append("university", university);
    data.append("action", "qualification_add_update");
    data.append("staff_unique_id", staff_unique_id);
    data.append("unique_id", unique_id);

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url = "";
    if (is_form){
    
    $.ajax({
        type: "POST",
        url: ajax_url,
        data: data,
        cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
        beforeSend: function () {

            $(".qualification_add_update_btn").attr("disabled", "disabled");
            $(".qualification_add_update_btn").text("Loading...");
        },
        success: function (data) {

            
            var obj = JSON.parse(data);
            var msg = obj.msg;
            var status = obj.status;
            var error = obj.error;

            if (!status) {
                $(".qualification_add_update_btn").text("Error");
                console.log(error);
            } else {
                if (msg !== "already") {

                    form_reset("qualification_form");
                }
                $(".qualification_add_update_btn").removeAttr("disabled", "disabled");
                if (unique_id && msg == "already") {
                    $(".qualification_add_update_btn").text("Update");

                } else {
                    $(".qualification_add_update_btn").text("Add");

                    $(".qualification_add_update_btn").attr("onclick", "qualification_add_update('')");
                }

                let count = parseInt($("#qualification_count").val() || "0", 10);
                $("#qualification_count").val(count + 1);
                
                sub_list_datatable("qualification_datatable");
            }

            sweetalert(msg, url);
        },
        error: function (data) {
            alert("Network Error");
        }
    });
}else{
    sweetalert("form_alert");
}

}

function experience_add_update(unique_id = "") { 
    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();
    var test_docs = $("#test_docs").val();
    
    var staff_company_name = $("#staff_company_name").val();
    
    var salary_amt = $("#salary_amt").val();
    var designation_name = $("#designation_name").val();
    var join_month = $("#join_month").val();
    var relieve_month = $("#relieve_month").val();
    var exp = $("#exp").val();

    var is_form = form_validity_check("experience_form");

    var data = new FormData();
    var image_s = document.getElementById("test_file_exp");
    if (image_s != '') {
        for (var i = 0; i < image_s.files.length; i++) {
            data.append("test_file[]", document.getElementById('test_file_exp').files[i]);
        }
    } else {
        data.append("test_docs", '');
    }
    data.append("staff_company_name", staff_company_name);
    data.append("salary_amt", salary_amt);
    data.append("designation_name", designation_name);
    data.append("join_month", join_month);
    data.append("relieve_month", relieve_month);
    data.append("exp", exp);
    data.append("action", "experience_add_update");
    data.append("staff_unique_id", staff_unique_id);
    data.append("unique_id", unique_id);
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
            $(".experience_add_update_btn").attr("disabled", "disabled");
            $(".experience_add_update_btn").text("Loading...");
        },
        success: function (data) {

            var obj = JSON.parse(data);
            var msg = obj.msg;
            var status = obj.status;
            var error = obj.error;

            if (!status) {
                $(".experience_add_update_btn").text("Error");
                console.log(error);
            } else {
                if (msg !== "already") {
                    form_reset("experience_form");
                }
                $(".experience_add_update_btn").removeAttr("disabled", "disabled");
                if (unique_id && msg == "already") {
                    $(".experience_add_update_btn").text("Update");
                } else {
                    $(".experience_add_update_btn").text("Add");
                    $(".experience_add_update_btn").attr("onclick", "experience_add_update('')");
                }
                
                let count = parseInt($("#exp_count").val() || "0", 10);
                $("#exp_count").val(count + 1);
                
                sub_list_datatable("experience_datatable");
                document.getElementById('test_file_qual').addEventListener('change', handleFileInput);

            }
            sweetalert(msg, url);
        },
        error: function (data) {
            alert("Network Error");
        }
    });

}

function staff_qualification_details_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=staff_qualification_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".qualification_add_update_btn").attr("disabled", "disabled");
                $(".qualification_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var data = obj.data;
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".qualification_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var education_type = data.education_type;
                    var degree = data.degree;
                    var college_name = data.college_name;
                    var year_passing = data.year_passing;
                    var percentage = data.percentage;
                    var university = data.university;

                    $("#education_type").val(education_type);
                    $("#degree").val(degree);
                    $("#college_name").val(college_name);
                    $("#year_passing").val(year_passing);
                    $("#percentage").val(percentage);
                    $("#university").val(university);

                    
                    $(".qualification_add_update_btn").removeAttr("disabled", "disabled");
                    $(".qualification_add_update_btn").text("Update");
                    $(".qualification_add_update_btn").attr("onclick", "qualification_add_update('" + unique_id + "')");
                }
            },
            error: function (data) {
                alert("Network Error");
            }
        });
    }
}

function staff_qualification_details_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "staff_qualification_details_delete"
                    }

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
                                url = '';
                            } else {
                                sub_list_datatable("qualification_datatable");
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    
                }
            });
    }
}

function staff_experience_details_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=staff_experience_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".experience_add_update_btn").attr("disabled", "disabled");
                $(".experience_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var data = obj.data;
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".experience_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var staff_company_name = data.staff_company_name;
                    var salary_amt = data.salary_amt;
                    var designation_name = data.designation_name;
                    var join_month = data.join_month;
                    var relieve_month = data.relieve_month;
                    var exp = data.exp;

                    $("#staff_company_name").val(staff_company_name);
                    $("#salary_amt").val(salary_amt);
                    $("#designation_name").val(designation_name);
                    $("#join_month").val(join_month);
                    $("#relieve_month").val(relieve_month);
                    $("#exp").val(exp);

                    
                    $(".experience_add_update_btn").removeAttr("disabled", "disabled");
                    $(".experience_add_update_btn").text("Update");
                    $(".experience_add_update_btn").attr("onclick", "experience_add_update('" + unique_id + "')");
                }
            },
            error: function (data) {
                alert("Network Error");
            }
        });
    }
}

function staff_experience_details_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "staff_experience_details_delete"
                    }

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
                                url = '';
                            } else {
                                sub_list_datatable("experience_datatable");
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    
                }
            });
    }
}


function staff_account_details_add_update(unique_id = "") { 

    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("account_details_form");

    console.log(is_form);

    if (is_form) {

        var data = $(".account_details_form").serialize();
        
        console.info(data);
        data += "&staff_unique_id=" + staff_unique_id;
        data += "&unique_id=" + unique_id + "&action=staff_account_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            datatype: "json",
            beforeSend: function () {
                $(".staff_account_details_add_update_btn").attr("disabled", "disabled");
                $(".staff_account_details_add_update_btn").text("Loading...");
            },
            success: function (data) {
                
                // alert(data);

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".staff_account_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {

                        
                        
                        form_reset("account_details_form");
                    }
                    $(".staff_account_details_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".staff_account_details_add_update_btn").text("Update");
                    } else {
                        $(".staff_account_details_add_update_btn").text("Add");
                        $(".staff_account_details_add_update_btn").attr("onclick", "staff_account_details_add_update('')");
                    }
                    
                    let count = parseInt($("#account_count").val() || "0", 10);
                    $("#account_count").val(count + 1);
                    
                    sub_list_datatable("staff_account_details_datatable");
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

function staff_account_details_edit(unique_id = "") {

    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=staff_account_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        
        var url = "";

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            beforeSend: function () {
                $(".staff_account_details_add_update_btn").attr("disabled", "disabled");
                $(".staff_account_details_add_update_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var data = obj.data;
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".staff_account_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    console.log(obj);
                    var bank_status = data.bank_status;
                    var bank_name = data.bank_name;
                    var bank_address = data.address;
                    var ifsc_code = data.ifsc_code;
                    var accountant_name = data.accountant_name;
                    var account_no = data.account_no;
                    var contact_no = data.contact_no;
                    

                    $("#bank_status").val(bank_status).trigger("change");
                    $("#bank_name").val(bank_name);
                    $("#bank_address").val(bank_address);
                    $("#ifsc_code").val(ifsc_code);
                    $("#accountant_name").val(accountant_name);
                    $("#account_no").val(account_no);
                    
                    $("#bank_contact_no").val(contact_no);
                    
                    $(".staff_account_details_add_update_btn").removeAttr("disabled", "disabled");
                    $(".staff_account_details_add_update_btn").text("Update");
                    $(".staff_account_details_add_update_btn").attr("onclick", "staff_account_details_add_update('" + unique_id + "')");
                }
            },
            error: function (data) {
                alert("Network Error");
            }
        });
    }
}

function staff_account_details_delete(unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        confirm_delete('delete')
            .then((result) => {
                if (result.isConfirmed) {

                    var data = {
                        "unique_id": unique_id,
                        "action": "staff_account_details_delete"
                    }

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
                                url = '';
                            } else {
                                sub_list_datatable("staff_account_details_datatable");
                            }
                            sweetalert(msg, url);
                        }
                    });

                } else {
                    
                }
            });
    }
}

function staff_test_toggle(unique_id = "", new_status = 0) {
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

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function get_salary(salary) {
    var annum_basic = "";
    var annum_hra = "";
    var annum_conveyance = "";
    var annum_medical_allowance = "";
    var annum_educational_allowance = "";
    var sum_allowance = "";
    var other_allowance = "";
    var annum_other_allowance = "";
    var annum_pf = "";
    var annum_esi = "";
    var total_deduction = "";
    var annum_total_deduction = "";
    var net_salary = "";
    var annum_net_salary = "";
    var annum_purformance_allowance = "";
    var ctc_cal = "";
    var ctc = "";
    var annum_ctc = "";

    if (salary) {
        get_salary1(salary);
    }
}

function get_salary1(salary) {

    var perf_allowance = $('#purformance_allowance').val();
    if (perf_allowance == '') {
        var purformance_allowance = 0;
    } else {
        var purformance_allowance = parseFloat(perf_allowance);
    }

    var conveyance_default_value = 5000;
    var medical_default_value = 8000;
    var educational_default_value = 900;
    var pf_default_value = 15000;
    var esi_default_value = 21000;

    if ((salary != '')) {
        var per_annum = salary * 12;
        var basic = ((salary * 40) / 100);
        var hra = ((basic * 50) / 100);
        
        if (salary >= conveyance_default_value) {
            var conveyance = 1600;
        } else {
            var conveyance = 0;
        }
        
        if (salary >= medical_default_value) {
            var medical_allowance = 1250;
        } else {
            var medical_allowance = 0;
        }
        
        if (salary >= educational_default_value) {
            var educational_allowance = 200;
        } else {
            var educational_allowance = 0;
        }
        
        if (basic <= pf_default_value) {
            var pf = ((basic * 12) / 100);
        } else {
            var pf = 0;
        }
        
        if (salary <= esi_default_value) {
            var esi = ((salary * 0.75) / 100);
        } else {
            var esi = 0;
        }

        var annum_basic = basic * 12;
        var annum_hra = hra * 12;
        var annum_conveyance = conveyance * 12;
        var annum_medical_allowance = medical_allowance * 12;
        var annum_educational_allowance = educational_allowance * 12;
        var sum_allowance = basic + hra + conveyance + medical_allowance + educational_allowance;
        var other_allowance = salary - sum_allowance;
        var annum_other_allowance = other_allowance * 12;
        var annum_pf = pf * 12;
        var annum_esi = esi * 12;
        var total_deduction = pf + esi;
        var annum_total_deduction = annum_pf + annum_esi;
        var net_salary = salary - total_deduction;
        var annum_net_salary = net_salary * 12;
        var annum_purformance_allowance = purformance_allowance * 12;
        var ctc_cal = total_deduction + net_salary;
        var ctc = purformance_allowance + ctc_cal;
        var annum_ctc = ctc * 12;

        $("#annum_salary").val(per_annum);
        $("#basic_wages").val(Math.round(basic));
        $("#annum_basic_wages").val(annum_basic);
        $("#hra").val(Math.round(hra));
        $("#annum_hra").val(annum_hra);
        $("#conveyance").val(Math.round(conveyance));
        $("#annum_conveyance").val(annum_conveyance);
        $("#medical_allowance").val(Math.round(medical_allowance));
        $("#annum_medical_allowance").val(annum_medical_allowance);
        $("#education_allowance").val(Math.round(educational_allowance));
        $("#annum_education_allowance").val(annum_educational_allowance);
        $("#other_allowance").val(Math.round(other_allowance));
        $("#annum_other_allowance").val(annum_other_allowance);
        $("#pf").val(Math.round(pf));
        $("#annum_pf").val(annum_pf);
        $("#esi").val(Math.round(esi));
        $("#annum_esi").val(annum_esi);
        $("#total_deduction").val(Math.round(total_deduction));
        $("#annum_total_deduction").val(annum_total_deduction);
        $("#net_salary").val(Math.round(net_salary));
        $("#annum_net_salary").val(annum_net_salary);
        $("#annum_purformance_allowance").val(annum_purformance_allowance);
        $("#ctc").val(Math.round(ctc));
        $("#annum_ctc").val(annum_ctc);
        $("#conveyance_default_value").val(conveyance_default_value);
        $("#medical_default_value").val(medical_default_value);
        $("#pf_default_value").val(pf_default_value);
        $("#esi_default_value").val(esi_default_value);
        $("#educational_default_value").val(educational_default_value);
    } else {
        $("#annum_salary").val('');
        $("#basic_wages").val('');
        $("#annum_basic_wages").val('');
        $("#hra").val('');
        $("#annum_hra").val('');
        $("#conveyance").val('');
        $("#annum_conveyance").val('');
        $("#medical_allowance").val('');
        $("#annum_medical_allowance").val('');
        $("#education_allowance").val('');
        $("#annum_education_allowance").val('');
        $("#other_allowance").val('');
        $("#annum_other_allowance").val('');
        $("#pf").val('');
        $("#annum_pf").val('');
        $("#esi").val('');
        $("#annum_esi").val('');
        $("#total_deduction").val('');
        $("#annum_total_deduction").val('');
        $("#net_salary").val('');
        $("#annum_net_salary").val('');
        $("#annum_purformance_allowance").val('');
        $("#ctc").val('');
        $("#annum_ctc").val('');
        $("#conveyance_default_value").val(conveyance_default_value);
        $("#medical_default_value").val(medical_default_value);
        $("#pf_default_value").val(pf_default_value);
        $("#esi_default_value").val(esi_default_value);
        $("#educational_default_value").val(educational_default_value);
    }
}


$(document).on("input change", ".per_month", function () {
  triggerSalaryRecalc();
});

function triggerSalaryRecalc() {
    const editableFields = [
    "basic",
    "hra",
    "statutory_bonus",
    "special_allowance",
    "other_allowance",
  ];
  
  var project_id = $("#project_name").val();

  let payload = {
    action: "calculate_salary",
    staff_unique_id: $("#staff_unique_id").val(),
    project_id: $("#project_name").val()
  };
  let filled = false;

  
  editableFields.forEach((id) => {
    let val = parseFloat($(`#${id}`).val());
    if (!isNaN(val)) {
      payload[id] = val;
      if (val > 0) filled = true;
    } else {
      payload[id] = filled ? 0 : "";
    }
  });

  
  if (!filled) return;

  
  payload["staff_unique_id"] = $("#staff_unique_id").val();
  
  console.info(payload);
  
  var ajax_url = sessionStorage.getItem("folder_crud_link");


  
  $.ajax({
    url: ajax_url,
    method: "POST",
    data: payload,
    dataType: "json",
    success: function (res) {
      
      const keys = new Set([...editableFields, ...Object.keys(res)]);
      keys.forEach((key) => {
            const val = res[key] ?? $(`#${key}`).val(); 
            $(`#${key}`).val(val);
            $(`#annum_${key}`).val(val * 12);
            console.info("Updated:", key, "=>", val);
        });
    },
    error: function (xhr, status, error) {
      console.error("Salary calc failed:", error);
    },
  });
}


function get_permanent_address(same_address = '') {

    if (document.getElementById('same_address').checked) {
        $("#same_address_status").val('1');
        var country = $("#pre_country").val();
        var per_state = $("#pre_state").val();
        var city = $("#pre_city").val();
        var building_no = $("#pre_building_no").val();
        var street = $("#pre_street").val();
        var area = $("#pre_area").val();
        var pincode = $("#pre_pincode").val();

        
        $('#perm_country').val(country).trigger('change');
        $('#edit_perm_state_id').val(per_state);
        $('#edit_perm_city_id').val(city);
        
        $('#perm_building_no').val(building_no);
        $('#perm_street').val(street);
        $('#perm_area').val(area);
        $('#perm_pincode').val(pincode);

    } else {
        $("#same_address_status").val('0');
        
          // Clear permanent address fields
    $('#perm_country').val('').trigger('change');
    $('#perm_state').val('').trigger('change'); // important for Select2
    $('#perm_city').val('').trigger('change');  // important for Select2
    $('#perm_building_no').val('');
    $('#perm_street').val('');
    $('#perm_area').val('');
    $('#perm_pincode').val('');
    }

}

function ageCalculate(birthDate) {

    var d = new Date(birthDate);

    var mdate = birthDate.toString();
    var yearThen = parseInt(mdate.substring(0, 4), 10);
    var monthThen = parseInt(mdate.substring(5, 7), 10);
    var dayThen = parseInt(mdate.substring(8, 10), 10);

    var today = new Date();
    var birthday = new Date(yearThen, monthThen - 1, dayThen);
    var differenceInMilisecond = today.valueOf() - birthday.valueOf();

    var year_age = Math.floor(differenceInMilisecond / 31536000000);
    var day_age = Math.floor((differenceInMilisecond % 31536000000) / 86400000);

    document.getElementById("age").value = year_age;
}

function file_upload(unique_id = "") {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var file_data = $('#test_file').prop('files')[0];
        var data = new FormData();
        console.log(data);

        data.append("action", "image_upload");
        data.append("unique_id", unique_id);
        data.append("test_file", file_data);

        console.log(typeof data);

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");

        
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function () {
                
                $(".createupdate_btn").text("Loading...");
            },
            success: function (data) {

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    url = '';
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg == "already") {
                        
                        url = '';

                        $(".createupdate_btn").removeAttr("disabled", "disabled");
                        if (unique_id) {
                            $(".createupdate_btn").text("Update");
                        } else {
                            $(".createupdate_btn").text("Save");
                        }
                    }
                }
                
            },
            error: function (data) {
                alert("Network Error");
            }
        });

    } else {
        sweetalert("form_alert");
    }
}

function staffFilter(filter_action = 0) {
    var internet_status = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var company_name = $('#company_name').val();

    var filter_data = {
        "company_name": company_name,
    };

    console.log(filter_data);

    init_datatable(table_id, form_name, action, filter_data);
}

function get_salary_type() {
    var bank_status = $('#bank_status').val();
    if (bank_status == 'Secondary') {
        $("#salary_type").val("NEFT").change();
        $("#salary_type").prop("disabled", false);
    }
    else {
        $("#salary_type").val("Axis Bank").change();
        $("#salary_type").prop("disabled", true);
    }
}

function get_branch_ids() {
    var branch = $('#branch').val();
    $('#staff_branch').val(branch);
}

function print(file_name) {
    onmouseover = window.open('../../xeon/' + file_name, 'onmouseover', 'height=600,width=900,scrollbars=yes,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}


