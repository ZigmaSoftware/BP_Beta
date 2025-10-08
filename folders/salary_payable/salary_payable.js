$(document).ready(function () {
	// var table_id 	= "offer_letter_datatable";
	init_datatable_main(table_id_main,form_name,action_main);
	// init_datatable(table_id,form_name,action);
	
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
var table_id_main 	= 'salary_letter_datatable';
var table_id    	= 'salary_sub_datatable';
var action 			= "datatable";
var action_main		= "datatable_main";

function salary_generation_cu(unique_id = "") {

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

function init_datatable_main(table_id_main='',form_name='',action_main='') {

	var table = $("#"+table_id_main);
	var data 	  = {
		"action"	: action_main, 
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

function init_datatable(table_id='',form_name='',action='') {

	var now = $('#letter_date').val();
	var str = now.split('-');
  	var date = new Date(str[0], str[1], 0).getDate();
	var table = $("#"+table_id);
	var data 	  = {
		"action"	: action,
		"date"		: date, 
		"now"		: now, 
	};
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var datatable = table.DataTable({
	scrollX     : "900px",
	scrollY     : "400px",
	processing  : true,
    serverSide  : true,
	ordering    : true,
	responsive  : false,
	paging      : false,
	info        : true,
	searching   : true,
	"ajax"		: {
		url 	: ajax_url,
		type 	: "POST",
		data 	: data,
		date 	: date,
		now 	: now
	},
	dom: 'Blfrtip',
	buttons: [
        'copy',
        'csv',
    ],
	lengthChange: true
});
}

function salary_generation_delete(unique_id = "") {

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
						init_datatable_main(table_id_main,form_name,action_main);
					}
					sweetalert(msg,url);
				}
			});

		} else {
			// alert("cancel");
		}
	});
}

function print_header_and_footer () {

	var user_id = $("#user_id").val();
	var branch_seal = $("#branch").val();

	if (user_id) {
		var sign_png 	= "img/letter_back/signs/"+user_id+".png";
		$("#user_sign").attr("src",sign_png);
	}

	if (!branch_seal) {
		branch_seal  = 1;
	}

	var branch_seal = "img/letter_back/"+branch_seal+"-seal.png";

	$("#branch_seal").attr("src",branch_seal);

	if ($("#print_header_footer").prop("checked")) {
		// $(".with_header_footer").addClass("d-print-none");
		// $(".with_header_footer_top").css("margin-top","100px");
		$(".backgrounds").removeClass("d-print-none");
		$(".print_backgrounds").removeClass("d-none");
	} else {
		$(".backgrounds").addClass("d-print-none");
		$(".print_backgrounds").addClass("d-none");
		// $(".with_header_footer").removeClass("d-print-none");
		// $(".with_header_footer_top").removeAttr("style");
	}
}

function letter_head_change(letter_head_branch = '',img_id = "letter_head_background") {

	if (letter_head_branch) {
		var header_img = "img/letter_back/"+letter_head_branch+"-header.png";
		var footer_img = "img/letter_back/"+letter_head_branch+"-footer.png";

		$("#header_img").attr("src",header_img);
		$("#footer_img").attr("src",footer_img);
	}
}

function daysInThisMonth(date) 
{
	init_datatable(table_id,form_name,action);
}

function deduction_calculation(staff_id,gross_salary)
{
	var tds 				= $('#tds_'+staff_id).val();
	var pf  				= $('#pf_'+staff_id).val();
	var esi 				= $('#esi_'+staff_id).val();
	var advance 			= $('#advance_'+staff_id).val();
	var loan 				= $('#loan_'+staff_id).val();
	var insurance  			= $('#insurance_'+staff_id).val();
	var other_deduction 	= $('#other_deduction_'+staff_id).val();

	if(tds)					{ tds             = tds; 			 } 			   else { tds='0'; }
	if(pf)					{ pf 			  = pf; 			 } 			   else { pf='0'; }
	if(esi)					{ esi 			  = esi; 			 } 			   else { esi='0'; }
	if(advance)				{ advance   	  = advance; 	 	 }   		   else { advance='0'; }
	if(loan)				{ loan   	  	  = loan; 	 		 }   		   else { loan='0'; }
	if(insurance)			{ insurance 	  = insurance; 		 } 	   		   else { insurance='0'; }
	if(other_deduction)		{ other_deduction = other_deduction; }			   else { other_deduction='0'; }
	if(gross_salary!='' && gross_salary!='0' && gross_salary!='0.00')		
	{
		gross_salary = gross_salary; 
	}			   
	else 
	{ 
		gross_salary='0'; 
	}


	var total_deduction 	= parseFloat(tds) + parseFloat(pf) + parseFloat(esi) + parseFloat(advance) + parseFloat(loan) + parseFloat(insurance) + parseFloat(other_deduction);
	$('#total_deduction_'+staff_id).val(total_deduction.toFixed());
	var net_salary 			= parseFloat(gross_salary) - parseFloat(total_deduction);
	$('#net_salary_'+staff_id).val(net_salary.toFixed());
	$('#net_salary1_'+staff_id).val(net_salary);
	$('#take_home1_'+staff_id).val(net_salary);

	reimbrusment_calculation(staff_id);
}

function reimbrusment_calculation(staff_id)
{
	var net_salary 			= $('#net_salary_'+staff_id).val();
	var reimbrusment  		= $('#reimbrusment_'+staff_id).val();
	

	if(net_salary)		{ net_salary    = net_salary; 		} else { net_salary='0'; 	}
	if(reimbrusment)	{ reimbrusment  = reimbrusment; 	} else { reimbrusment='0'; 	}

	var total_salary 			= parseFloat(net_salary) + parseFloat(reimbrusment);
	$('#take_home_'+staff_id).val(total_salary.toFixed());
	$('#take_home1_'+staff_id).val(total_salary);
}

function new_external_window(url) {
	
	onmouseover = window.open(url, 'onmouseover', 'height=900,width=1350,resizable=no,left=200,top=50,toolbar=no,location=no,directories=no,status=no,menubar=no');
}

function get_staff_check(){
	if ($("#staff_check").prop('checked')) { 
		$('.all_staff_class').prop('checked', true);
		$('.check_val').val(1);
	} else {
		$('.all_staff_class').prop('checked', false);
		$('.check_val').val(0);
	}
}

function get_ind_staff_check(staff_id = '', id = '') {
	if ($("#staff_id"+id).prop('checked')) { 
		$('#staff_id'+id).prop('checked', true);
		$('#staff_val'+id).val(1);
	} else {
		$('#staff_id'+id).prop('checked', false);
		$('#staff_val'+id).val(0);
	}
}

function pay_mail_send(employee_id = '',unique_id = '',entry_date = '') { 

	var staff_val = $('#staff_val'+employee_id).val();
	var ajax_url = "folders/salary_generation/mail.php"
	
	//if (staff_val == 1) {

		var data = {
			"employee_id" : employee_id,
			"entry_date"  : entry_date,
			"unique_id"   : unique_id,
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) {
				
				if (data) {
					
					sweetalert("custom_mail",'','','Email sent successfully');
				}
			}
		});
	// }else{
	// 	sweetalert("custom",'','','Select the Employee');
	// }
}

function pay_mail_send_all(s_no = '',unique_id, entry_date){
	
	var ajax_url = "folders/salary_generation/mail.php"
	
	for(i = 1; i <= s_no; i++) {

		var staff_val = $('.check_class'+i).val();
		var employee_id = $('.emp_class'+i).val();
		if (staff_val == 1) {
			var data = {
				"employee_id" : employee_id,
				"entry_date"  : entry_date, 
				"unique_id"   : unique_id,
			};

			$.ajax({
				type    : "POST",
				url     : ajax_url,
				data    : data,
				success : function (data) {
					
					if (data) {
						//alert(data);
						sweetalert("custom_mail",'','','Email sent successfully');
					}
				}
			});
		}
	}
}


function salary_pay(id,department)
{
	var ajax_url = sessionStorage.getItem("folder_crud_link");
	var url      = sessionStorage.getItem("list_link");
if(department=='Axis Bank')
{
	var description	=	$('#pay_description').val();
	var date	    =	$('#pay_date').val();
}
else
{
	var description	=	$('#pay_description'+id).val();
	var date	    =	$('#pay_date'+id).val();
}
	var salary_date = 	$('#salary_date').val();

	var data = {
			"description"      : description,
			"date"  		   : date,
			"unique_id"  	   : id,
			"department"  	   : department,
			"salary_date"  	   : salary_date,
			"action"		   : 'salary_pay',
		};

		$.ajax({
			type    : "POST",
			url     : ajax_url,
			data    : data,
			success : function (data) 
			{
				var obj     = JSON.parse(data);
				var msg     = obj.msg;
				if(msg=='status_update')
				{
					$('.salary_pay'+id).prop('disabled', true);
					$('.salary_pay'+id).val('Paid');
				}
			}
		});
}