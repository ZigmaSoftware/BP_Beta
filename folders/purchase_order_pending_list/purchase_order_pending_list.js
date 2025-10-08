$(document).ready(function () {
	// var table_id 	= "city_datatable";
    datatable_init_based_on_prev_state ();
	// init_datatable(table_id,form_name,action,filter_data);
});


function datatable_init_based_on_prev_state () {
        // Data Table Filter Function Based ON Previous Search
        var from_date     = sessionStorage.getItem("bids_ho_from_date");
        var to_date       = sessionStorage.getItem("bids_ho_to_date");
        var filter_action = sessionStorage.getItem("bids_ho_action");
    
        if (!from_date) {
            from_date = $("#bids_ho_from").val();
        } else {
            $("#bids_ho_from").val(from_date);
        }
    
        if (!to_date) {
            to_date = $("#bids_ho_to").val();
        } else {
            $("#bids_ho_to").val(to_date);
        }
    
        if (!filter_action) {
            filter_action = 0;
        }
    
        // Datatable Filter Data
        var filter_data = {
            "from_date"     : from_date,
            "to_date"       : to_date,
            "filter_action" : filter_action
        };
    
        // var table_id     = "bids_management_datatable";
        init_datatable(table_id,form_name,action,filter_data);
}
var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var competition_oem_datatable_tableid    = "competition_oem_datatable"; 
var sub_action	                         = "competition_oem_datatable";
var competition_bidder_datatable_tableid = "competition_bidder_datatable"; 
var sub_bidder_action	                 = "competition_bidder_datatable";


var form_name 		= 'management_approval';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
// var table_id 		= 'management_approval_datatable';
var action 			= "datatable";
$(document).ready(function () {
	// var table_id 	= "city_datatable";
	sub_list_datatable(competition_oem_datatable_tableid,form_name,sub_action);
	sub_list_datatable(competition_bidder_datatable_tableid,form_name,sub_bidder_action);
});
function bids_ho_approval_cu(unique_id = "") {

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

function init_datatable(table_id='',form_name='',action='',filter_data = "") {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
      data          = {
        ...data,
        ...filter_data
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


function BidsHoFilter(filter_action = 0 ) {
    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    var is_form = form_validity_check("was-validated");

    if (is_form) {
        var from_date = $("#bids_ho_from").val();
        var to_date   = $("#bids_ho_to").val();
        

        var is_vaild = fromToDateValidity(from_date,to_date);

        if (is_vaild) {

            sessionStorage.setItem("bids_ho_from_date",from_date);
            sessionStorage.setItem("bids_ho_to_date",to_date);
            sessionStorage.setItem("bids_ho_action",filter_action);

            // Delete Below Line After Testing Complete
            sessionStorage.setItem("bids_ho_action",0);

            var filter_data = {
                "from_date"     : from_date,
                "to_date"       : to_date,
                "filter_action" : filter_action
            };

            console.log(filter_data);

            init_datatable(table_id,form_name,action,filter_data);

        }

    } else {
        sweetalert("form_alert","");
    }
}

function bids_ho_approval_delete(unique_id = "") {

	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
	
	confirm_delete('delete')
	.then((result) => {
		if (result.isConfirmed) {

			var data = {
				"unique_id" 	: unique_id,
				"action"		: "delete"
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
						 init_datatable(table_id,form_name,action,filter_data);
					}
					sweetalert(msg,url);
				}
			});

		} else {
			// alert("cancel");
		}
	});
}


function get_points_noted(management_support='')
{
      if(document.getElementById('management_support').value="Yes")
    {
        $(".points_noted").attr("required","required");
        $(".point_noted_lable").removeClass("d-none");
    }
    else
    {
         $(".points_noted").removeAttr("required","required");
         $(".point_noted_lable").addClass("d-none");
    }
}

function competition_oem_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var bids_management_unique_id = $("#bids_management_unique_id").val();
    var oem_name        = $("#oem_name").val();
    var product_details = $("#product_details").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    

    if ((oem_name!='')&&(product_details!='')) {

        var data     = $(".was-validated").serialize();
        data        += "&bids_management_unique_id="+bids_management_unique_id;
        data        += "&unique_id="+unique_id+"&action=competition_oem_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".competition_oem_add_update_btn").attr("disabled","disabled");
                $(".competition_oem_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".competition_oem_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                        //form_reset("was-validated");
                    }
                    $(".competition_oem_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".competition_oem_add_update_btn").text("Update");
                    } else {
                        $(".competition_oem_add_update_btn").text("Add");
                        $(".competition_oem_add_update_btn").attr("onclick","competition_oem_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("competition_oem_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
        //$("#oem_name").attr("required","required");
        //$("#product_details").attr("required","required");
         sweetalert("custom",'','','Create Competition OEM Details');
         if(oem_name==''){document.getElementById('oem_name').focus();}
        else if(product_details==''){document.getElementById('product_details').focus();}
         // return event.preventDefault(), event.stopPropagation(), !1;
    }
}

function competition_oem_edit(unique_id = "") {
    if (unique_id) {
        var data 		= "unique_id="+unique_id+"&action=competition_oem_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".competition_oem_add_update_btn").attr("disabled","disabled");
				$(".competition_oem_add_update_btn").text("Loading...");
			},
			success		: function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".competition_oem_add_update_btn").text("Error");
                    console.log(error);
				} else {
                    console.log(obj);
                    var oem_name            = data.oem_name;
                    var product_details     = data.product_details;
                   

                    $("#oem_name").val(oem_name);
                    $("#product_details").val(product_details);
                    
                    // Button Change 
                    $(".competition_oem_add_update_btn").removeAttr("disabled","disabled");
                    $(".competition_oem_add_update_btn").text("Update");
                    $(".competition_oem_add_update_btn").attr("onclick","competition_oem_add_update('"+unique_id+"')");
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
    }
}

function competition_oem_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "competition_oem_delete"
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
                            sub_list_datatable("competition_oem_datatable");
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
function competition_bidder_add_update (unique_id = "") { // au = add,update

    var internet_status  = is_online();

    var bids_management_unique_id = $("#bids_management_unique_id").val();
    var bid_name        = $("#bid_name").val();
    var map_oem_name = $("#map_oem_name").val();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    if ((bid_name!='')&&(map_oem_name!='')) {

        var data     = $(".was-validated").serialize();
        data        += "&bids_management_unique_id="+bids_management_unique_id;

        data        += "&unique_id="+unique_id+"&action=competition_bidder_add_update";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
            type    : "POST",
            url     : ajax_url,
            data    : data,
            beforeSend  : function() {
                $(".competition_bidder_add_update_btn").attr("disabled","disabled");
                $(".competition_bidder_add_update_btn").text("Loading...");
            },
            success     : function(data) {

                var obj     = JSON.parse(data);
                var msg     = obj.msg;
                var status  = obj.status;
                var error   = obj.error;

                if (!status) {
                    $(".competition_bidder_add_update_btn").text("Error");
                    console.log(error);
                } else {
                    if (msg !=="already") {
                       // form_reset("was-validated");
                    }
                    $(".competition_bidder_add_update_btn").removeAttr("disabled","disabled");
                    if (unique_id && msg =="already"){
                        $(".competition_bidder_add_update_btn").text("Update");
                    } else {
                        $(".competition_bidder_add_update_btn").text("Add");
                        $(".competition_bidder_add_update_btn").attr("onclick","competition_bidder_add_update('')");
                    }
                    // Init Datatable
                    sub_list_datatable("competition_bidder_datatable");
                }
                sweetalert(msg,url);
            },
            error       : function(data) {
                alert("Network Error");
            }
        });


    } else {
       sweetalert("custom",'','','Create Competition Bidder Details');
         if(bid_name==''){document.getElementById('bid_name').focus();}
        else if(map_oem_name==''){document.getElementById('map_oem_name').focus();}
    }
}


function competition_bidder_edit(unique_id = "") {
    if (unique_id) {
        var data 		= "unique_id="+unique_id+"&action=competition_bidder_edit";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        // var url      = sessionStorage.getItem("list_link");
        var url      = "";

        // console.log(data);
        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			beforeSend 	: function() {
				$(".competition_bidder_add_update_btn").attr("disabled","disabled");
				$(".competition_bidder_add_update_btn").text("Loading...");
			},
			success		: function(data) {

                var obj     = JSON.parse(data);
                var data    = obj.data;
				var msg     = obj.msg;
				var status  = obj.status;
				var error   = obj.error;

				if (!status) {
                    $(".competition_bidder_add_update_btn").text("Error");
                    console.log(error);
				} else {
                    console.log(obj);
                    var bid_name            = data.bid_name;
                    var map_oem_name        = data.map_oem_name;
                   

                    $("#bid_name").val(bid_name);
                    $("#map_oem_name").val(map_oem_name);
                    
                    // Button Change 
                    $(".competition_bidder_add_update_btn").removeAttr("disabled","disabled");
                    $(".competition_bidder_add_update_btn").text("Update");
                    $(".competition_bidder_add_update_btn").attr("onclick","competition_bidder_add_update('"+unique_id+"')");
				}
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});
    }
}

function competition_bidder_delete (unique_id = "") {
    if (unique_id) {

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = sessionStorage.getItem("list_link");
        
        confirm_delete('delete')
        .then((result) => {
            if (result.isConfirmed) {
    
                var data = {
                    "unique_id" 	: unique_id,
                    "action"		: "competition_bidder_delete"
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
                            sub_list_datatable("competition_bidder_datatable");
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

function sub_list_datatable (table_id = "", form_name = "", action = "") {
     
    var bids_management_unique_id = $("#bids_management_unique_id").val();
    
    var table = $("#"+table_id);
	var data 	  = {
        "bids_management_unique_id"    : bids_management_unique_id,
		"action"	                   : table_id, 
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

function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}
