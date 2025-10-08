var contact_person_tableid = "contact_person_datatable";
var delivery_details_tableid = "dependent_details_datatable";
var qualification_datatable_tableid = "qualification_datatable";
var experience_datatable_tableid = "experience_datatable";
var account_details_tableid = "staff_account_details_datatable";
var asset_tableid = "asset_datatable";

var form_name = 'staff';
var form_header = '';
var form_footer = '';
var table_name = '';
var table_id = 'staff_datatable';
var action = "datatable";

$(document).ready(function () {
    // Datatable Initialize
    datatable_init_based_on_prev_state();
    var unique_id = $("#unique_id").val();
    if (unique_id == '') {
        get_employee_id();
    }

    $("#excel_export").click(function () {
        var staff_status = $('#staff_status').val();
        window.location = "folders/staff/excel.php?staff_status=" + staff_status;
    });
    // Form wizard Functions
    $('#staffcreatewizard').bootstrapWizard({
        onTabShow: function (tab, navigation, index) {
            var staff_unique_id = $("#staff_unique_id").val();
            var unique_id = $("#unique_id").val();
            if (index != 0) {
                if (!staff_unique_id) {
                    sweetalert("custom", '', '', 'Create Staff Details');
                    $('#staffcreatewizard').find("a[href*='officialdetails_tab']").trigger('click');
                    return event.preventDefault(), event.stopPropagation(), !1;
                }
            }
            // console.log(index);
            var $total = navigation.find('li').length;
            var $current = index + 1;
            var $percent = ($current / $total) * 100;
            $('#staffcreatewizard').find('.bar').css({
                width: $percent + '%'
            });
            // If it's the last tab then hide the last button and show the finish instead
            if ($current >= $total) {
                $('#staffcreatewizard').find('.pager .next').hide();
                $('#staffcreatewizard').find('.pager .finish').show();
                $('#staffcreatewizard').find('.pager .finish').removeClass('disabled');
                // unique_id    = $(".finish").data("unique-id");
            } else {
                $('#staffcreatewizard').find('.pager .next').show();
                $('#staffcreatewizard').find('.pager .finish').hide();
            }
            if (index == 3) {
                var unique_id = $("#unique_id").val();
                //alert(unique_id);

                if (unique_id) {
                    var ajax_url = sessionStorage.getItem("folder_crud_link");
                    var url = sessionStorage.getItem("list_link");

                    var data = {
                        "unique_id": unique_id,
                        "action": "staff_account_details_count"
                    }

                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: data,
                        success: function (data) {
                            console.log(data);
                            var obj = JSON.parse(data);
                            var count = obj.data.count;

                            if (count === 0) {
                                // Display a sweet alert
                                // Display a sweet alert
                                sweetalert("custom", '', '', 'Fill the account details');
                                // Prevent the user from accessing the "qualification" tab by returning false
                                $('#staffcreatewizard').find("a[href*='account_details_tab']").trigger('click');
                                return false;
                            } else {
                                // Allow the user to proceed to the "qualification" tab
                                // Remove the disabled class if it was previously added
                                //alert('Count is not 0');
                                $('#staffcreatewizard').find("a[href*='qualification_tab']").trigger('click');
                            }
                        }
                    });
                }
            }

            if ((index != 0) && (index != 7)) {
                $(".createupdate_btn").text("Next");
            } else if (index == 7) {
                $(".createupdate_btn").text("Next");
                var form_class = "salary_form";
                var is_form = form_validity_check(form_class);
                if (!is_form) {
                    sweetalert("form_alert");
                    console.log(is_form);
                    var sucs = "false";
                    if (sucs == "false") {
                        console.log(sucs);
                        $('#staffcreatewizard').find("a[href*='salary_tab']").trigger('click');
                    }
                } else {
                    var data = $("." + form_class).serialize();
                    data += "&unique_id=" + unique_id + "&action=salarycreateupdate" + "&staff_unique_id=" + staff_unique_id;
                    var ajax_url = sessionStorage.getItem("folder_crud_link");
                    var url = sessionStorage.getItem("list_link");
                    // console.log(data);
                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: data,
                        success: function (data) {
                            var obj = JSON.parse(data);
                            var msg = obj.msg;
                            var status = obj.status;
                            var error = obj.error;
                            var cus_id = obj.staff_unique_id;
                            url = '';
                            var success = false;
                            if (!status) {
                                $(".createupdate_btn").text("Error");
                                console.log(error);
                            } else {
                                success = true;
                                if (msg == "already") {
                                    // Button Change Attribute
                                    url = '';
                                    success = false;
                                }
                                sweetalert(msg, url);
                            }
                        },
                        error: function (data) {
                            alert("Network Error");
                            // return false;
                        }
                    });
                    $('#staffcreatewizard').find("a[href*='relieve_tab']").trigger('click');
                }
            }
            switch (index) {
                case 0:
                    break;
                case 1:
                    sub_list_datatable(delivery_details_tableid);
                    break;
                case 2:
                    sub_list_datatable(account_details_tableid);
                    break;
                case 3:
                    sub_list_datatable(qualification_datatable_tableid);
                    break;
                case 4:
                    sub_list_datatable(experience_datatable_tableid);
                    break;
                case 5:
                    sub_list_datatable(asset_tableid);
                    break;
                default:
                    break;
            }
        },
        onNext: function (t, r, index) {
            if (index == 1) {
                var form_class = "staff_profile_form";
                var is_form = form_validity_check(form_class);
                var unique_id = $("#unique_id").val();
                if (!is_form) {
                    sweetalert("form_alert");
                    return event.preventDefault(), event.stopPropagation(), !1;
                } else {
                    var data = $("." + form_class).serialize();
                    data += "&unique_id=" + unique_id + "&action=createupdate";
                    var ajax_url = sessionStorage.getItem("folder_crud_link");
                    var url = sessionStorage.getItem("list_link");
                    // console.log(data);
                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: data,
                        beforeSend: function () {
                            $(".createupdate_btn").addClass("disabled");
                            $(".createupdate_btn").text("Loading...");
                        },
                        success: function (data) {
                            var obj = JSON.parse(data);
                            var msg = obj.msg;
                            var status = obj.status;
                            var error = obj.error;
                            var cus_id = obj.staff_unique_id;
                            url = '';
                            var success = false;
                            if (!status) {
                                $(".createupdate_btn").text("Error");
                                console.log(error);
                            } else {
                                success = true;
                                if (msg == "already") {
                                    // Button Change Attribute
                                    url = '';
                                    success = false;
                                }
                                file_upload(cus_id);
                                $(".createupdate_btn").removeClass("disabled", "disabled");
                                if (unique_id) {
                                    $(".createupdate_btn").text("Update & Continue");
                                } else {
                                    $(".createupdate_btn").text("Save & Continue");
                                }
                                sweetalert(msg, url);
                                if (!success) {
                                    console.log(success);
                                    $('#staffcreatewizard').find("a[href*='officialdetails_tab']").trigger('click');
                                    // $('#staffcreatewizard').find("a[href*='contactperson_tab']").removeClass('active');
                                    // $('#staffcreatewizard').find("a[href*='profile_tab']").addClass('active');
                                } else {
                                    console.log(success);
                                    $("#staff_unique_id").val(cus_id);
                                    window.location.href = "http://zigma.in/blue_planet/index.php?file=staff/list";
                                    // $('#staffcreatewizard').find("a[href*='dependentdetails_tab']").trigger('click');
                                    // $('#staffcreatewizard').find("a[href*='profile_tab']").removeClass('active');
                                    // $('#staffcreatewizard').find("a[href*='contactperson_tab']").addClass('active');
                                }
                            }
                        },
                        error: function (data) {
                            alert("Network Error");
                            // return false;
                        }
                    });
                    return event.preventDefault(), event.stopPropagation(), !1;
                }
            }

            if (index == 3) {
                var unique_id = $("#unique_id").val();
                //alert(unique_id);

                if (unique_id) {
                    var ajax_url = sessionStorage.getItem("folder_crud_link");
                    var url = sessionStorage.getItem("list_link");

                    var data = {
                        "unique_id": unique_id,
                        "action": "staff_account_details_count"
                    }

                    $.ajax({
                        type: "POST",
                        url: ajax_url,
                        data: data,
                        success: function (data) {
                            console.log(data);
                            var obj = JSON.parse(data);
                            var count = obj.data.count;

                            if (count === 0) {
                                // Display a sweet alert
                                // Display a sweet alert
                                sweetalert("custom", '', '', 'Fill the account details');
                                // Prevent the user from accessing the "qualification" tab by returning false
                                $('#staffcreatewizard').find("a[href*='account_details_tab']").trigger('click');
                                return false;
                            } else {
                                // Allow the user to proceed to the "qualification" tab
                                // Remove the disabled class if it was previously added
                                //alert('Count is not 0');
                                $('#staffcreatewizard').find("a[href*='qualification_tab']").trigger('click');
                            }
                        }
                    });
                }
            }

        },
        onTabClick: function (tab, navigation, index) {
            // return false;
            // return event.preventDefault(), event.stopPropagation(), !1;
        }
    });
    $('#staffcreatewizard .finish').click(function () {
        //alert('Finished!, Starting over!');
        var staff_unique_id = $("#staff_unique_id").val();
        var unique_id = $("#unique_id").val();
        var form_class = "relieve_form";
        var data = $("." + form_class).serialize();
        data += "&unique_id=" + unique_id + "&action=relievecreateupdate";
        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url = sessionStorage.getItem("list_link");
        // console.log(data);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;
                var cus_id = obj.staff_unique_id;
                url = '';
                var success = false;
                if (!status) {
                    $(".createupdate_btn").text("Error");
                    console.log(error);
                } else {
                    success = true;
                    if (msg == "already") {
                        // Button Change Attribute
                        url = '';
                        success = false;
                    }
                    sweetalert(msg, url);
                }
            },
            error: function (data) {
                alert("Network Error");
                // return false;
            }
        });
        var url = sessionStorage.getItem("list_link");
        sweetalert("create", url);
    });
    //premises_check();
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
    // Data Table Filter Function Based ON Previous Search
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

    // Datatable Filter Data
    var filter_data = {
        "status": staff_status,
        "filter_action": filter_action
    };

    // var table_id     = "follow_up_call_datatable";
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
    // alert("hii");
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
    // alert("hii");
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

// Get State Names Based On Country Selection
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

// Get get_qualification Based On qualification Selection

// Get city Names Based On State Selection
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

// Get permanent address State Names Based On Country Selection
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

// Get permanent address State Names Based On Country Selection
function get_employee_id() {
    var ajax_url = sessionStorage.getItem("folder_crud_link");

    var data = {
        "action": "employee_id"
    }

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

// Get city Names Based On State Selection
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

    var table = $("#" + table_id);
    var data = {
        "staff_unique_id": staff_unique_id,
        "action": table_id,
    };
    var ajax_url = sessionStorage.getItem("folder_crud_link");

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
}

// Invoice Details ADD & UPDATE
function asset_details_add_update(unique_id = "") { // au = add,update

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
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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
                    // Init Datatable
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
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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

                    // alert(status);

                    $("#asset_name").val(asset_name);
                    $("#item_no").val(item_no);
                    $("#qty").val(qty); 
                    $("#asset_status").val(asset_status).trigger("change");
                    // $("#status").val(status);
                    // document.getElementById('#status').INNE
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

                    // Button Change 
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
                    // alert("cancel");
                }
            });
    }
}

// Invoice Details ADD & UPDATE
function dependent_details_add_update(unique_id = "") { // au = add,update

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
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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
                    // Init Datatable
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
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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

                    // Button Change 
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
                    // alert("cancel");
                }
            });
    }
}

// Customer Potential Mapping ADD & UPDATE
function qualification_add_update(unique_id = "") { // au = add,update

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

    //if (image_s != '') {
    for (var i = 0; i < image_s.files.length; i++) {
        data.append("test_file[]", document.getElementById('test_file_qual').files[i]);
    }
    // }

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
    // console.log(data);
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

            // document.getElementById('test_doc').value = '';
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

                // Init Datatable
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

function experience_add_update(unique_id = "") { // au = add,update
    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();
    var test_docs = $("#test_docs").val();
    // alert(staff_unique_id);
    var staff_company_name = $("#staff_company_name").val();
    // alert(staff_company_name);
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

    // console.log(data);
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
                // Init Datatable
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
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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

                    // Button Change 
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
                    // alert("cancel");
                }
            });
    }
}

function staff_experience_details_edit(unique_id = "") {
    if (unique_id) {
        var data = "unique_id=" + unique_id + "&action=staff_experience_details_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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

                    // Button Change 
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
                    // alert("cancel");
                }
            });
    }
}

// Account Details ADD & UPDATE
function staff_account_details_add_update(unique_id = "") { // au = add,update

    var internet_status = is_online();

    var staff_unique_id = $("#staff_unique_id").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("account_details_form");

    // console.log(is_form);

    if (is_form) {

        var data = $(".account_details_form").serialize();
        data += "&staff_unique_id=" + staff_unique_id;
        data += "&unique_id=" + unique_id + "&action=staff_account_details_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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

                var obj = JSON.parse(data);
                var msg = obj.msg;
                var status = obj.status;
                var error = obj.error;

                if (!status) {
                    $(".staff_account_details_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !== "already") {

                        // form_reset(".account_details_form").empty('data');
                        // $(".account_details_form").reset("add");
                        form_reset("account_details_form");
                    }
                    $(".staff_account_details_add_update_btn").removeAttr("disabled", "disabled");
                    if (unique_id && msg == "already") {
                        $(".staff_account_details_add_update_btn").text("Update");
                    } else {
                        $(".staff_account_details_add_update_btn").text("Add");
                        $(".staff_account_details_add_update_btn").attr("onclick", "staff_account_details_add_update('')");
                    }
                    // Init Datatable
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
        // var url      = sessionStorage.getItem("list_link");
        var url = "";

        // console.log(data);
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
                    // var gst_no                      = data.gst_no;

                    $("#bank_status").val(bank_status).trigger("change");
                    $("#bank_name").val(bank_name);
                    $("#bank_address").val(bank_address);
                    $("#ifsc_code").val(ifsc_code);
                    $("#accountant_name").val(accountant_name);
                    $("#account_no").val(account_no);
                    // $("#bank_gst_no").val(gst_no);
                    $("#bank_contact_no").val(contact_no);
                    // Button Change 
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
                    // alert("cancel");
                }
            });
    }
}

function staff_delete(unique_id = "") {

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url = sessionStorage.getItem("list_link");

    confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {

                var data = {
                    "unique_id": unique_id,
                    "action": "delete"
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
                            init_datatable(table_id, form_name, action);
                        }
                        sweetalert(msg, url);
                    }
                });

            } else {
                // alert("cancel");
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
        //conveyance calculation
        if (salary >= conveyance_default_value) {
            var conveyance = 1600;
        } else {
            var conveyance = 0;
        }
        //medical allowance
        if (salary >= medical_default_value) {
            var medical_allowance = 1250;
        } else {
            var medical_allowance = 0;
        }
        //Education allowance
        if (salary >= educational_default_value) {
            var educational_allowance = 200;
        } else {
            var educational_allowance = 0;
        }
        //pf
        if (basic <= pf_default_value) {
            var pf = ((basic * 12) / 100);
        } else {
            var pf = 0;
        }
        //esi
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

        //  alert(per_state);
        $('#perm_country').val(country).trigger('change');
        $('#edit_perm_state_id').val(per_state);
        $('#edit_perm_city_id').val(city);
        // $('#perm_city').val(city).trigger('change');
        $('#perm_building_no').val(building_no);
        $('#perm_street').val(street);
        $('#perm_area').val(area);
        $('#perm_pincode').val(pincode);

    } else {
        $("#same_address_status").val('0');
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

        // console.log(data);
        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            beforeSend: function () {
                // $(".createupdate_btn").attr("disabled","disabled");
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
                        // Button Change Attribute
                        url = '';

                        $(".createupdate_btn").removeAttr("disabled", "disabled");
                        if (unique_id) {
                            $(".createupdate_btn").text("Update");
                        } else {
                            $(".createupdate_btn").text("Save");
                        }
                    }
                }
                // sweetalert(msg,url);
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
    var status = $('#staff_status').val();
    var company_name = $('#company_name').val();
    if (status) {

        // sessionStorage.setItem("status", status);
        //sessionStorage.setItem("staff_action", filter_action);

        // Delete Below Line After Testing Complete
        //  sessionStorage.setItem("follow_up_call_action", 0);

        var filter_data = {
            "status": status,
            "company_name": company_name,
            "filter_action": filter_action
        };

        console.log(filter_data);

        init_datatable(table_id, form_name, action, filter_data);

    } else {
        sweetalert("form_alert", "");
    }
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

function send_mail() {
    // alert("hii");
    var email_id = $('#email_id').val();

    var ajax_url = sessionStorage.getItem("folder_crud_link");
    //     var currentdate = new Date(); 
    // var datetime = "Last Sync: " + currentdate.getDate() + "/"
    //                 + (currentdate.getMonth()+1)  + "/" 
    //                 + currentdate.getFullYear() + " @ "  
    //                 + currentdate.getHours() + ":"  
    //                 + currentdate.getMinutes() + ":" 
    //                 + currentdate.getSeconds();
    // const date = new Date();

    // let currentDay= String(date.getDate()).padStart(2, '0');

    // let currentMonth = String(date.getMonth()+1).padStart(2,"0");

    // let currentYear = date.getFullYear();

    // // we will display the date as DD-MM-YYYY 

    // let currentDate = `${currentDay}-${currentMonth}-${currentYear}`;
    // alert(currentDate);
    // alert(datetime);

    if (email_id) {
        var data = {
            // "ho_name"  : ho_name,
            // "staff_id" : staff_name,
            "email_id": email_id,

            "link": "https://103.130.89.95/aed_erp/folders/staff_detail_creation/check.php",
            "action": "send_mail",
        };
        // alert(data);

        $.ajax({
            type: "POST",
            url: ajax_url,
            data: data,
            success: function (data) {
                // var obj = JSON.parse(data);
                // var status = obj.status;

                if (data == 'sent') {
                    const modal = document.querySelector('.modal');
                    sweetalert("custom", '', '', 'Email sent successfully');
                    // window.close();
                    // $('#exampleModal').hide();
                    modal.style.display = 'none';
                    location.reload();
                    // document.getElementById('exampleModal').style.display = "none";
                } else {
                    sweetalert("custom", '', '', 'Email Not sent!!!')
                }
            }
        });
    } else {
        alert("Enter Email Address");
    }

}

// function get_countSublist_account() {
//     var unique_id = $("#unique_id").val();  // Keep the variable name as it is for the alert
//     alert(unique_id);

//     if (unique_id) {
//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         var url = sessionStorage.getItem("list_link");

//         var data = {
//             "unique_id": unique_id,  // Keep the field name as it is for the data payload
//             "action": "staff_account_details_count"
//         }

//         $.ajax({
//             type: "POST",
//             url: ajax_url,
//             data: data,
//             success: function (data) {
//                 console.log(data);  // Add this line to check the response data
//                 var obj = JSON.parse(data);
//                 var count = obj.data.count;  // Update the field name

//                 if (count === 0) {
//                     alert('0');
//                     // Prevent the user from accessing the "qualification" tab
//                     // You can use some logic to disable or hide the tab.
//                 } else {
//                     alert('1');
//                     // Allow the user to proceed to the "qualification" tab
//                     //window.location.href = url; // Navigate to the next tab
//                 }
//             }
//         });
//     }
// }
