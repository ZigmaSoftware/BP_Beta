var late_permission_leave_tableid     = "late_permission_leave_datatable";
var leave_type_tableid                = "leave_type_datatable";
var holidays_tableid                  = "holidays_datatable";


var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'attendance_setting';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'attendance_setting_datatable';
var action 			= "datatable";

$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
	init_datatable(table_id,form_name,action);
    sub_list_datatable(late_permission_leave_tableid,form_name,"late_permission_leave_datatable");
    sub_list_datatable(leave_type_tableid,form_name,"leave_type_datatable");
	sub_list_datatable(holidays_tableid,form_name,"holidays_datatable");
    
});

function attendance_setting_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {

        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");

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

function init_datatable(table_id='',form_name='',action='') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
		ordering    : true,
		searching   : true,
		"ajax"		: {
			url 	: ajax_url,
			type 	: "POST",
			data 	: data
		}
	});
}

function attendance_setting_toggle(unique_id = "", new_status = 0) {
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

function get_permission_leave_option (late_permission = ''){
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	
	if (late_permission) {

		var data = {
			"late_permission" : late_permission,
			"action"          : "leave_permission"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {
				$("#leave_permission").html(data);
				
			}
		});
	}
}


function sub_list_datatable (table_id = "", form_name = "", action = "") {
     
    var attendance_setting_unique_id = $("#attendance_setting_unique_id").val();
    
    var table = $("#"+table_id);
	var data 	  = {
        "attendance_setting_unique_id"    : attendance_setting_unique_id,
		"action"	                      : table_id, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	var datatable = table.DataTable({
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

function late_permission_leave_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var attendance_setting_unique_id     = $("#attendance_setting_unique_id").val();
    var late_permission                  = $("#late_permission").val();
    var late_count                       = $("#late_count").val();
    var leave_permission                 = $("#leave_permission").val();
    var permission_count                 = $("#permission_count").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    

    if ((late_permission!='')&&(leave_permission!='')&&(late_count!='')&&(permission_count!='')) {

        var data     = $(".was-validated").serialize();
        data        += "&attendance_setting_unique_id="+attendance_setting_unique_id;
        data        += "&unique_id="+unique_id+"&action=late_permission_leave_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".late_permission_leave_add_update_btn").attr("disabled","disabled");
                $(".late_permission_leave_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".late_permission_leave_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                        //form_reset("was-validated");
                        $("#late_permission").val('').trigger('change');
                        $("#late_count").val('');
                        $("#leave_permission").val('').trigger('change');
                        $("#permission_count").val('');
                    }
                    $(".late_permission_leave_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".late_permission_leave_add_update_btn").text("Update");
                    } else {
                        $(".late_permission_leave_add_update_btn").text("Add");
                        $(".late_permission_leave_add_update_btn").attr("onclick","late_permission_leave_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("late_permission_leave_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
        sweetalert("custom",'','','Fill all Sub Details');
        if(late_permission==''){document.getElementById('late_permission').focus();}
        else if(late_count==''){document.getElementById('late_count').focus();}
        else if(leave_permission==''){document.getElementById('leave_permission').focus();}
        else if(permission_count==''){document.getElementById('permission_count').focus();}
    }
}

// function late_permission_leave_edit(unique_id = "") {
//     if (unique_id) {
//         var data        = "unique_id="+unique_id+"&action=late_permission_leave_edit";

//         var ajax_url = sessionStorage.getItem("folder_crud_link");
//         // var url      = sessionStorage.getItem("list_link");
//         var url      = "";

//         // console.log(data);
//         $.ajax({
//             type    : "POST",
//             url     : ajax_url,
//             data    : data,
//             beforeSend  : function() {
//                 $(".late_permission_leave_add_update_btn").attr("disabled","disabled");
//                 $(".late_permission_leave_add_update_btn").text("Loading...");
//             },
//             success     : function(data) {

//                 var obj     = JSON.parse(data);
//                 var data    = obj.data;
//                 var msg     = obj.msg;
//                 var status  = obj.status;
//                 var error   = obj.error;

//                 if (!status) {
//                     $(".late_permission_leave_add_update_btn").text("Error");
//                     console.log(error);
//                 } else {
//                     console.log(obj);
//                     var late_permission_type    = data.late_permission_type;
//                     var late_count              = data.late_count;
//                     var permission_leave_type   = data.permission_leave_type;
//                     var permission_count        = data.permission_count;
                   
                   
//                     $("#late_permission").val(late_permission_type).trigger('change');
//                     $("#late_count").val(late_count);
//                     $("#leave_permission").val(permission_leave_type).trigger('change');
//                     $("#permission_count").val(permission_count);
                    
//                     // Button Change 
//                     $(".late_permission_leave_add_update_btn").removeAttr("disabled","disabled");
//                     $(".late_permission_leave_add_update_btn").text("Update");
//                     $(".late_permission_leave_add_update_btn").attr("onclick","late_permission_leave_add_update('"+unique_id+"')");
//                 }
//             },
//             error       : function(data) {
//                 alert("Network Error");
//             }
//         });
//     }
// }

function late_permission_leave_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id"     : unique_id,
                    "action"        : "late_permission_leave_delete"
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
                            sub_list_datatable("late_permission_leave_datatable");
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

function leave_type_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var attendance_setting_unique_id     = $("#attendance_setting_unique_id").val();
    var leave_type                  = $("#leave_type").val();
    var leave_days                       = $("#leave_days").val();
    
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    

    if ((leave_type!='')&&(leave_days!='')) {

        var data     = $(".was-validated").serialize();
        data        += "&attendance_setting_unique_id="+attendance_setting_unique_id;
        data        += "&unique_id="+unique_id+"&action=leave_type_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".leave_type_add_update_btn").attr("disabled","disabled");
                $(".leave_type_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".leave_type_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                        //form_reset("was-validated");
                        $("#leave_type").val('');
                        $("#leave_days").val('');
                        
                    }
                    $(".leave_type_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".leave_type_add_update_btn").text("Update");
                    } else {
                        $(".leave_type_add_update_btn").text("Add");
                        $(".leave_type_add_update_btn").attr("onclick","leave_type_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("leave_type_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
        sweetalert("custom",'','','Fill all Sub Details');
        if(leave_type==''){document.getElementById('leave_type').focus();}
        else if(leave_days==''){document.getElementById('leave_days').focus();}
        
    }
}

function leave_type_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id"     : unique_id,
                    "action"        : "leave_type_delete"
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
                            sub_list_datatable("leave_type_datatable");
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

function holidays_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var attendance_setting_unique_id     = $("#attendance_setting_unique_id").val();
    var holiday_date                  = $("#holiday_date").val();
    var remarks                       = $("#remarks").val();
    
    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    

    if ((holiday_date!='')&&(remarks!='')) {

        var data     = $(".was-validated").serialize();
        data        += "&attendance_setting_unique_id="+attendance_setting_unique_id;
        data        += "&unique_id="+unique_id+"&action=holidays_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".holidays_add_update_btn").attr("disabled","disabled");
                $(".holidays_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".holidays_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                        //form_reset("was-validated");
                        $("#holiday_date").val('');
                        $("#remarks").val('');
                        
                    }
                    $(".holidays_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".holidays_add_update_btn").text("Update");
                    } else {
                        $(".holidays_add_update_btn").text("Add");
                        $(".holidays_add_update_btn").attr("onclick","holidays_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("holidays_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
        sweetalert("custom",'','','Fill all Sub Details');
        if(holiday_date==''){document.getElementById('holiday_date').focus();}
        else if(remarks==''){document.getElementById('remarks').focus();}
        
    }
}

function holiday_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id"     : unique_id,
                    "action"        : "holiday_delete"
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
                            sub_list_datatable("holidays_datatable");
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


function get_late_permission_time() {

    var late_hrs          = $('#late_hrs').val();
    var permission_hrs    = $('#permission_hrs').val();
    var working_time_from = $('#working_time_from').val();
    

    if(permission_hrs > late_hrs) { 
        var time1 = working_time_from;
        var time2 = late_hrs;
        var time3 = permission_hrs;
        
        
        var hour=0;
        var minute=0;
        
        
        var splitTime1= time1.split(':');
        var splitTime2= time2.split(':');
        var splitTime3= time3.split(':');
        
        hour_late       = parseInt(splitTime1[0])+parseInt(splitTime2[0]);
        minute_late     = parseInt(splitTime1[1])+parseInt(splitTime2[1]);
        hour_val_late   = hour_late + (minute_late/60);
        min_val_late    = minute_late%60;

        if (hour_val_late   < 10) {hour_val_late   = "0"+hour_val_late;}
        if (min_val_late < 10) {min_val_late = "0"+min_val_late;}
        var late_time  = Math.round(hour_val_late) +":"+ min_val_late; 

        var splitTime4= late_time.split(':');

        hour_perm       = parseInt(splitTime3[0])+parseInt(splitTime4[0]);
        minute_perm     = parseInt(splitTime3[1])+parseInt(splitTime4[1]);
        hour_val_perm   = hour_perm + (minute_perm/60);
        min_val_perm    = minute_perm % 60;

        var permission_time  = Math.round(hour_val_perm) +":"+min_val_perm; 

        $('#late_time').val(late_time);
        $('#permission_time').val(permission_time);

    }else{
        
    }
}