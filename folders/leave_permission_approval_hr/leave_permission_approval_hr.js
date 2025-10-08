$(document).ready(function () {
	// var table_id 	= "leave_permission_datatable";
	//init_datatable(table_id,form_name,action);
	//sub_list_datatable("leave_sub_datatable");
	datatable_init_based_on_prev_state();
	day_type_check();
	let day_type = $("#day_type").val();
	
	if(day_type == 5) {
		day_type_check_onduty();
	}
	//leavepermissionhrFilter();
	var leave_days 	= $("#leave_days").val();
	if((day_type == 1) ||(day_type == 2)){
		$('.sublist_class').removeClass('d-none');
		leave_type_sublist(leave_days);
		$(".leave_type_class").prop("required",true);
	}
	
	var is_approved = $('#is_approved').val();
		get_reason_text(is_approved);
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'User Type';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'leave_permission_datatable';
var action 			= "datatable";

function datatable_init_based_on_prev_state () {
		// Data Table Filter Function Based ON Previous Search
		var from_date  	    = sessionStorage.getItem("leave_hr_from");
		var to_date         = sessionStorage.getItem("leave_hr_to");
		var filter_action   = sessionStorage.getItem("leave_hr_action");
	
		if (!from_date) {
			from_date = $("#leave_hr_from").val();
		} else {
			$("#leave_hr_from").val(from_date);
		}
	
		if (!to_date) {
			to_date = $("#leave_hr_to").val();
		} else {
			$("#leave_hr_to").val(to_date);
		}
		
	
		if (!filter_action) {
			filter_action = 0;
		}
	
		// Datatable Filter Data
		var filter_data = {
			"from_date" 	: from_date,
			"to_date" 		: to_date,
			"filter_status" : 0,
			"filter_action" : filter_action
		};
	
		// var table_id 	= "follow_up_call_datatable";
		init_datatable(table_id,form_name,action,filter_data);
}

function day_type_check() {
	// Default 
	$(".day_div").addClass("d-none");
	$(".day_inp").prop("required",false);

	let day_type = $("#day_type").val();

	if (day_type) {
		if ((day_type != 2) && (day_type != 6) &&(day_type != 5)) {
			$(".full_day_div").removeClass("d-none");
			$(".full_day_inp").prop("required",true);
		} else if (day_type == 6) {
			$(".permission_div").removeClass("d-none");
			$(".permission_inp").prop("required",true);
		} else if (day_type == 5) {
			$(".onduty_div").removeClass("d-none");
			$(".onduty_inp").prop("required",true);
		} else {
			$(".half_day_div").removeClass("d-none");
			$(".half_day_inp").prop("required",true);
		}
	}
}

function day_type_check_onduty() {
	// Default 
	$(".day_div").addClass("d-none");
	$(".day_inp").prop("required",false);

	let day_type = $("#on_duty_type").val();

	if (day_type) {
		if (day_type != 2) {
			$(".onduty_div").removeClass("d-none");
			$(".onduty_full_day_div").removeClass("d-none");
			$(".onduty_full_day_inp").prop("required",true);
			$(".onduty_inp").prop("required",true);
		} else {
			$(".onduty_div").removeClass("d-none");
			$(".onduty_inp").prop("required",true);
			$(".on_duty_half_day_div").removeClass("d-none");
			$(".on_duty_half_day_inp").prop("required",true);
		}
	}
}

function get_reason_text(approved_status = '') {
	
	if (approved_status == 1) {
		$(".approve_reason_class").removeClass("d-none");
		$(".cancel_reason_class").addClass("d-none");
		$("#hr_reason").prop("required",true);
		$("#hr_cancel_reason").prop("required",false);
	} else if (approved_status == 2){
		$(".approve_reason_class").addClass("d-none");
		$(".cancel_reason_class").removeClass("d-none");
		$("#hr_reason").prop("required",false);
		$("#hr_cancel_reason").prop("required",true);
	}else{
		$(".approve_reason_class").removeClass("d-none");
		$(".cancel_reason_class").addClass("d-none");
		$("#hr_reason").prop("required",true);
		$("#hr_cancel_reason").prop("required",false);
	}
	
}

function leave_permission_approval_hr_cu (unique_id = "") {

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

function init_datatable(table_id='',form_name='',action='' , filter_data = '') {

	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action, 
	};
	data = {
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

function leave_permission_delete(unique_id = "") {

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

function days_by_dates () {

	// calculation of no. of days between two date
	// To set two dates to two variables
	let date1 = new Date("06/30/2019");
	let date2 = new Date("07/3/2019");

	// To calculate the time difference of two dates
	let Difference_In_Time = date2.getTime() - date1.getTime();

	// To calculate the no. of days between two dates
	let Difference_In_Days = Difference_In_Time / (1000 * 3600 * 24);

	// Add Plus One For Inclusive of From Date or To Date
	return Difference_In_Days+1;
}

function dates_by_days () {
	
}

function leavepermissionhrFilter(filter_action = 0) {
	var internet_status = is_online();

	if (!internet_status) {
		sweetalert("no_internet");
		return false;
	}

	var is_form = form_validity_check("was-validated");

	if (is_form) {
		var from_date       = $("#leave_hr_from").val();
		var to_date         = $("#leave_hr_to").val();
		
		var is_vaild = fromToDateValidity(from_date, to_date);

		if (is_vaild) {

			sessionStorage.setItem("leave_hr_from_date", from_date);
			sessionStorage.setItem("leave_hr_to_date", to_date);
			sessionStorage.setItem("leave_hr_action", filter_action);
			 
			// Delete Below Line After Testing Complete
			sessionStorage.setItem("follow_up_call_action", 0);

			var filter_data = {
				"from_date": from_date,
				"to_date"  : to_date,
				"filter_action": filter_action
			};

			console.log(filter_data);

			init_datatable(table_id, form_name, action, filter_data);

		}

	} else {
		sweetalert("form_alert", "");
	}
}

$( window ).on( "load", function() {

	var unique_id 	= $("#unique_id").val();

	if (unique_id) {
		
			var leave_days 	= $("#leave_days").val();

			leave_type_sublist(leave_days);
	}
});

function leave_type_sublist (leave_days) {

    var ajax_url 		= sessionStorage.getItem("folder_crud_link");
	
	var list_ui 		= "";
	var list_ui_total   = "";
	var i  				= 1;
	let sub_id_cnt      = "";


	var mon            = new Date();
	var current_month  = (mon.getMonth() + 1);
	var month          = mon.setMonth(mon.getMonth() - 2); 
	var three_months   = mon.toLocaleDateString();
	var days           = new Date(mon.getFullYear(), mon.getMonth()+1, 0).getDate();
	var year           = mon.getFullYear();  
	
	
	var j = 0;
	var from_date        = new Date($('#from_date').val());
 	var leave_count      = $("#leave_type_imp").val();
 	var half_leave_count = $("#half_leave_type_imp").val();
 	var comp_off         = $("#comp_of_date_imp").val();
 	var comp_off_half    = $("#comp_of_date_half_imp").val();
 	var sub_unique_id    = $("#sub_unique_id").val();

 	var split_leave_cnt 	 = leave_count.split(','); 
 	var split_half_leave_cnt = half_leave_count.split(','); 
 	var split_comp_off 	 	 = comp_off.split(','); 
 	var split_half_comp_off  = comp_off_half.split(','); 
 	var sub_unique_id_cnt    = sub_unique_id.split(','); 

 	var min_mnth = three_months.split('/');

 	if(min_mnth[0] < 10){
 		var min_month = "0"+min_mnth[0];
 	}else{
 		var min_month = min_mnth[0];
 	}

 	var min_date = year+"-"+min_month+"-01"; 
 	var max_date = year+"-"+current_month+"-"+days; 

	if(leave_days != 0){	
		for (i = 1; i <= leave_days; i++) {
			
			var date       = formatDate(from_date);
			var date_fomat = formatDate1(from_date);
			
			let val1_selected  = "";
			let val2_selected  = "";
			let val3_selected  = "";
			let val4_selected  = "";
			let val5_selected  = "";
			let val6_selected  = "";
			let val7_selected  = "";
			let val8_selected  = "";
			let val9_selected  = "";
			let val10_selected = "";
			let val11_selected = "";
			let val12_selected = "";

			let sub_val7_selected  = "";
			let sub_val8_selected  = "";
			let sub_val9_selected  = "";
			let sub_val10_selected = "";
			let sub_val11_selected = "";
			let sub_val12_selected = "";
			
			//let checked   = "";

			if(sub_unique_id_cnt != ''){
				sub_id = '<input type = "hidden" name = "unique_id_sub[]" id = "unique_id_sub'+i+'" value = "'+sub_unique_id_cnt[j]+'">';
			}else{
				sub_id = '<input type = "hidden" name = "unique_id_sub[]" id = "unique_id_sub'+i+'" value = "'+sub_id_cnt+'">';
			}
			
			if((split_leave_cnt[j] <= 6)||(split_leave_cnt[j] == '')){
				var check_val = '<input id="checkbox_text'+i+'" name="checkbox_text[]" onchange = "get_check_box_leave_count('+i+')" type="checkbox" value="" >';
				var check_select_class        = " d-none ";
				var comp_off_date_half_class  = " d-none ";
				var check_select_class        = " d-none ";
				var check_select_class_option = "";
				var check_value               = 0;
			}else if(split_leave_cnt[j] > 6){
				var check_val = '<input id="checkbox_text'+i+'" name="checkbox_text[]" onchange = "get_check_box_leave_count('+i+')" type="checkbox" value="" checked>';
				var check_select_class        = "";
				var comp_off_date_half_class  = "";
				var check_select_class_option = " d-none ";
				var check_value               = 1;
			}else{
				var check_val = '<input id="checkbox_text'+i+'" name="checkbox_text[]" onchange = "get_check_box_leave_count('+i+')" type="checkbox" value="" >';
				var check_select_class        = " d-none ";
				var comp_off_date_half_class  = " d-none ";
				var check_select_class_option = "";
				var check_value               = 0;
			}

			if((split_leave_cnt[j] == 4)||(split_leave_cnt[j] == 10)){
				var comp_off_readonly  = "";
				//get_comp_off_date(leave_type+i, i,entry_date+i,sub_unique_id[j])
			}else{
				var comp_off_readonly  = " readonly ";
			}

			if(split_half_leave_cnt[j] == 10){
				var comp_off_half_readonly = "";
			}else{
				var comp_off_half_readonly = " readonly ";
			}

			switch(split_leave_cnt[j]){
				case '1':
					val1_selected  = " selected ";  
					break;
				case '2':
					val2_selected  = " selected ";  
					break;
				case '3':
					val3_selected  = " selected ";  
					break;
				case '4':
					val4_selected  = " selected ";  
					break;
				case '5':
					val5_selected  = " selected ";  
					break;
				case '6':
					val6_selected  = " selected ";  
					break;
				case '7':
					val7_selected  = " selected "; 
					break;
				case '8':
					val8_selected  = " selected ";  
					break;
				case '9':
					val9_selected  = " selected "; 
					break;
				case '10':
					val10_selected = " selected "; 
					break;
				case '11':
					val11_selected = " selected ";  
					break;
				case '12':
					val12_selected = " selected "; 
					break;
			};

			switch(split_half_leave_cnt[j]){
				
				case '7':
					sub_val7_selected  = " selected "; 
					break;
				case '8':
					sub_val8_selected  = " selected ";  
					break;
				case '9':
					sub_val9_selected  = " selected "; 
					break;
				case '10':
					sub_val10_selected = " selected "; 
					break;
				case '11':
					sub_val11_selected = " selected ";  
					break;
				case '12':
					sub_val12_selected = " selected "; 
					break;
			};

			list_ui 	 = '<tr>';
			list_ui 	+= '<td>'+i+'</td>';

			list_ui 	+= '<td>'+date_fomat+'<input type = "hidden" name = "entry_date_sub[]" id = "entry_date_sub'+i+'" value = "'+date+'">'+sub_id+'</td>';
			list_ui 	+= '<td><div align = "center" checkbox checkbox-success mb-2">'+check_val+'</div><input type = "hidden" name = "check_box_value[]" id = "check_box_value'+i+'" value = "'+check_value+'"></td>';

			list_ui 	+= '<td><select name="leave_type[]" id="leave_type'+i+'" onload = "get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)"  onclick ="get_check_box_leave_count('+i+')" onchange = "get_cl_count(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value),get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)" onfocus ="get_check_box_leave_count('+i+')" class=" form-control leave_type_class"  >';

			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="">Select</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="1" '+val1_selected+'>EL</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="2" '+val2_selected+'>CL</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="3" '+val3_selected+'>SL</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="4" '+val4_selected+'>Comp Off</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="5" '+val5_selected+'>SPL Leave</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="6" '+val6_selected+'>LOP</option>';

			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="">Select</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="7" '+val7_selected+'>EL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="8" '+val8_selected+'>CL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="9" '+val9_selected+'>SL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="10" '+val10_selected+'>Comp Off Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="11" '+val11_selected+'>SPL Leave Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="12" '+val12_selected+'>LOP Half</option>';

			list_ui 	+= '</select><br><select name="half_leave_type[]" id="half_leave_type'+i+'" onload = "get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)" onchange = "get_cl_count(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value),get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)" onclick ="get_check_box_leave_count('+i+')" onfocus ="get_check_box_leave_count('+i+')" class=" form-control check_select'+check_select_class+' half_leave_type_class'+i+'"  >';


			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="">Select</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="7" '+sub_val7_selected+'>EL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="8" '+sub_val8_selected+'>CL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="9" '+sub_val9_selected+'>SL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="10" '+sub_val10_selected+'>Comp Off Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="11" '+sub_val11_selected+'>SPL Leave Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="12" '+sub_val12_selected+'>LOP Half</option>';

			list_ui 	+= '</select></td>';
			list_ui 	+= '<td><input type = "date" class = "form-control" min = "'+min_date+'" max = "'+max_date+'"   name = "comp_off_date[]" id = "comp_off_date'+i+'"  value = "'+split_comp_off[j]+'"'+comp_off_readonly+'><br>';
			list_ui 	+= '<input type = "date" class = "form-control'+comp_off_date_half_class+' comp_off_half'+i+'" name = "comp_off_date_half[]" min = "'+min_date+'" max = "'+max_date+'" id = "comp_off_date_half'+i+'" value = "'+split_half_comp_off[j]+'"'+comp_off_half_readonly+'></td>';

			list_ui     +='</tr>';

			list_ui_total     += list_ui;
			from_date.setDate(from_date.getDate() + 1);
			j++; 
			
		}
	}else{
		var date = formatDate(from_date);
			var date_fomat = formatDate1(from_date);
			
			let val1_selected  = "";
			let val2_selected  = "";
			let val3_selected  = "";
			let val4_selected  = "";
			let val5_selected  = "";
			let val6_selected  = "";
			let val7_selected  = "";
			let val8_selected  = "";
			let val9_selected  = "";
			let val10_selected = "";
			let val11_selected = "";
			let val12_selected = "";

			let sub_val7_selected  = "";
			let sub_val8_selected  = "";
			let sub_val9_selected  = "";
			let sub_val10_selected = "";
			let sub_val11_selected = "";
			let sub_val12_selected = "";
			
			//let checked   = "";

			if(sub_unique_id_cnt != ''){
				sub_id = '<input type = "hidden" name = "unique_id_sub[]" id = "unique_id_sub'+i+'" value = "'+sub_unique_id_cnt[j]+'">';
			}else{
				sub_id = '<input type = "hidden" name = "unique_id_sub[]" id = "unique_id_sub'+i+'" value = "'+sub_id_cnt+'">';
			}

			if((split_leave_cnt[j] <= 6)||(split_leave_cnt[j] == '')){
				var check_val = '<input id="checkbox_text'+i+'" name="checkbox_text[]" onchange = "get_check_box_leave_count('+i+')" type="checkbox" value="" >';
				var check_select_class = " d-none ";
				var check_select_class_option = "";
				var check_value  = 0;
			}else if(split_leave_cnt[j] > 6){
				var check_val = '<input id="checkbox_text'+i+'" name="checkbox_text[]" onchange = "get_check_box_leave_count('+i+')" type="checkbox" value="" checked>';
				var check_select_class = "";
				var check_select_class_option = " d-none ";
				var check_value  = 1;
			}else{
				var check_val = '<input id="checkbox_text'+i+'" name="checkbox_text[]" onchange = "get_check_box_leave_count('+i+')" type="checkbox" value="" >';
				var check_select_class = " d-none ";
				var check_select_class_option = "";
				var check_value  = 0;
			}
			
			if((split_leave_cnt[j] == 4)||(split_leave_cnt[j] == 10)){
				var comp_off_readonly  = "";
			}else{
				var comp_off_readonly  = " readonly ";
			}

			if(split_half_leave_cnt[j] == 10){
				var comp_off_half_readonly = "";
			}else{
				var comp_off_half_readonly = " readonly ";
			}

			switch(split_leave_cnt[j]){
				case '1':
					val1_selected  = " selected ";  
					break;
				case '2':
					val2_selected  = " selected ";  
					break;
				case '3':
					val3_selected  = " selected ";  
					break;
				case '4':
					val4_selected  = " selected ";  
					break;
				case '5':
					val5_selected  = " selected ";  
					break;
				case '6':
					val6_selected  = " selected ";  
					break;
				case '7':
					val7_selected  = " selected "; 
						break;
				case '8':
					val8_selected  = " selected ";  
						break;
				case '9':
					val9_selected  = " selected "; 
						break;
				case '10':
					val10_selected = " selected "; 
						break;
				case '11':
					val11_selected = " selected ";  
						break;
				case '12':
					val12_selected = " selected "; 
						break;
			};

			switch(split_half_leave_cnt[j]){
				
				case '7':
					sub_val7_selected  = " selected "; 
						break;
				case '8':
					sub_val8_selected  = " selected ";  
						break;
				case '9':
					sub_val9_selected  = " selected "; 
						break;
				case '10':
					sub_val10_selected = " selected "; 
						break;
				case '11':
					sub_val11_selected = " selected ";  
						break;
				case '12':
					sub_val12_selected = " selected "; 
						break;
			};

			list_ui 	 = '<tr>';
			list_ui 	+= '<td>'+i+'</td>';

			list_ui 	+= '<td>'+date_fomat+'<input type = "hidden" name = "entry_date_sub[]" id = "entry_date_sub'+i+'" value = "'+date+'">'+sub_id+'</td>';
			list_ui 	+= '<td><div align = "center" checkbox checkbox-success mb-2">'+check_val+'</div><input type = "hidden" name = "check_box_value[]" id = "check_box_value'+i+'" value = "'+check_value+'"></td>';

			list_ui 	+= '<td><select name="leave_type[]" id="leave_type'+i+'" onload = "get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)" onclick ="get_check_box_leave_count('+i+')" onchange = "get_cl_count(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value),get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)"   onfocus ="get_check_box_leave_count('+i+')" class=" form-control leave_type_class"  >';

			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="">Select</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="1" '+val1_selected+'>EL</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="2" '+val2_selected+'>CL</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="3" '+val3_selected+'>SL</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="4" '+val4_selected+'>Comp Off</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="5" '+val5_selected+'>SPL Leave</option>';
			list_ui 	+= '<option class = "check_select_option'+ check_select_class_option +'" value="6" '+val6_selected+'>LOP</option>';

			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="">Select</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="7" '+val7_selected+'>EL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="8" '+val8_selected+'>CL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="9" '+val9_selected+'>SL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="10" '+val10_selected+'>Comp Off Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="11" '+val11_selected+'>SPL Leave Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="12" '+val12_selected+'>LOP Half</option>';

			list_ui 	+= '</select><br><select name="half_leave_type[]" id="half_leave_type'+i+'" onload = "get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+'),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)" onchange = "get_cl_count(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value),get_comp_off_date(this.value,'+i+',entry_date_sub'+i+'.value,unique_id_sub'+i+'.value)" onclick ="get_check_box_leave_count('+i+'),get_holiday_date(this.value,'+i+','+min_month+','+current_month+','+year+')" onfocus ="get_check_box_leave_count('+i+')" class=" form-control check_select'+check_select_class+' half_leave_type_class'+i+'"  >';

			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="">Select</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="7" '+sub_val7_selected+'>EL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="8" '+sub_val8_selected+'>CL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="9" '+sub_val9_selected+'>SL Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="10" '+sub_val10_selected+'>Comp Off Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="11" '+sub_val11_selected+'>SPL Leave Half</option>';
			list_ui 	+= '<option class = "check_select_val'+ check_select_class +'" value="12" '+sub_val12_selected+'>LOP Half</option>';

			list_ui 	+= '</select></td>';

			list_ui 	+= '<td><input type = "date" class = "form-control" min = "'+min_date+'" max = "'+max_date+'"   name = "comp_off_date[]" id = "comp_off_date'+i+'" value = "'+split_comp_off[j]+'"><br>';
			list_ui 	+= '<input type = "date" class = "form-control'+comp_off_date_half_class+' comp_off_half'+i+'" name = "comp_off_date_half[]" min = "'+min_date+'" max = "'+max_date+'" id = "comp_off_date_half'+i+'" value = "'+split_half_comp_off[j]+'"></td>';


			list_ui     +='</tr>';

			list_ui_total     += list_ui;
	}

	$(".leave_datatable").html(list_ui_total);
	
	return false;
}

function formatDate(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [year, month, day].join('-');
}

function formatDate1(date) {
    var d = new Date(date),
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = '0' + month;
    if (day.length < 2) day = '0' + day;

    return [day, month, year].join('-');
}

function get_cl_count(leave_type = '' , i = '',entry_date = '',sub_unique_id = ''){
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var staff_id = $('#staff_id').val();
    if ((leave_type == 2) || (leave_type == 8)) {
        var data = {
        	"staff_id"       : staff_id,
        	"leave_type"     : leave_type,
        	"entry_date"     : entry_date,
        	"sub_unique_id"  : sub_unique_id,
            "action"         : "cl_type_count"
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
                if (msg == "already") {
                	url 		= '';
                	sweetalert("custom",'','','CL for this staff already entered');
                	$(".createupdate_btn").attr("disabled","disabled");
                }else{
                	$(".createupdate_btn").removeAttr("disabled","disabled");
                }
            }
        });
    }else{
    	$(".createupdate_btn").removeAttr("disabled","disabled");
    }
}

function get_check_box_leave_count(i){
	if ($("#checkbox_text"+i).prop("checked")) {
		$('#check_box_value'+i).val('1');
	}else{
		$('#check_box_value'+i).val('0');
	}

	var check_box_val = $("#check_box_value"+i).val();
	if (check_box_val == 1) {
		$('.check_select_val').removeClass('d-none');
		$('#half_leave_type'+i).removeClass('d-none');
		$('#comp_off_date_half'+i).removeClass('d-none');
		$('.check_select_option').addClass('d-none');
		$(".half_leave_type_class"+i).prop("required",true);
	}else{
		$('.check_select_option').removeClass('d-none');
		$('.check_select_val').addClass('d-none');
		$('#half_leave_type'+i).addClass('d-none');
		$('#comp_off_date_half'+i).addClass('d-none');
		$(".half_leave_type_class"+i).prop("required",false);
	}
}

function get_comp_off_date(leave_type = '' , i = '',entry_date = '',sub_unique_id = '') { 
	var x = $('#leave_type'+i).val();
	if((x == 4) || (x == 10)){
		$('#comp_off_date'+i).prop("readonly",false);
	}else{
		$('#comp_off_date'+i).prop("readonly",true);
		}
	var y = $('#half_leave_type'+i).val();

	if(y == 10){
		$('#comp_off_date_half'+i).prop("readonly",false);
	}else{
		$('#comp_off_date_half'+i).prop("readonly",true);
	}
}

function get_holiday_date(leave_type = '' , i = '', min_month = '', current_month = '', year = '') {
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var x = $('#leave_type'+i).val();
	var y = $('#half_leave_type'+i).val();
	var staff_id = $('#staff_id').val();

	if((x == 4) || (x == 10) ||(y == 10)) {

		var data = {
			"min_month"     : min_month,
			"current_month" : current_month,
			"year"          : year,
			"staff_id"      : staff_id,
			"action"        : "sunday_holiday_date"
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {
				$(".sunday_holiday_tab").html(data);
			}
		});
	}else {
		$(".sunday_holiday_tab").html('');
	}
}