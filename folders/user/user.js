$(document).ready(function () { 
	// var table_id 	= "user_datatable";
	init_datatable(table_id,form_name,action);
	team_users_div($("#is_team_head").prop("checked"));
});

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'user';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'user_datatable';
var action 			= "datatable";

function user_cu(unique_id = "") {

	var work_location= $('#work_location').val();
// 	alert(work_location);
    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
	}
	
	var password 		= $("#password").val();
	var con_password 	= $("#confirm_password").val();

	if (password !== con_password) {
		sweetalert("custom","","","Password Dosen't Match");
		return false;
	}
	
	 // Check password strength
    const strength = validatePassword();
    if (strength < 5) {
        alert("Please create a stronger password before submitting.");
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
function togglePasswords() {
  const fields = [document.getElementById("password"), document.getElementById("confirm_password")];
  const eyes = document.querySelectorAll(".password-eye");

  fields.forEach((field, index) => {
    if (field.type === "password") {
      field.type = "text";
      eyes[index]?.classList.remove("fa-eye");
      eyes[index]?.classList.add("fa-eye-slash");
    } else {
      field.type = "password";
      eyes[index]?.classList.remove("fa-eye-slash");
      eyes[index]?.classList.add("fa-eye");
    }
  });
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

function user_toggle(unique_id = "", new_status = 0) {
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

function get_under_users (under_user = "") {

$("#under_user_name").html('');
	var ajax_url = sessionStorage.getItem("folder_crud_link");

	if (under_user) {
		var data = {
			"under_user" 	: under_user,
			"action"	: "user_options"
		}

		$.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			success : function(data) {

				if (data) { 
					$("#under_user_name").html(data);
				}

			}
		});
	}
}


  
function isNumber(evt) {
    evt = (evt) ? evt : window.event;
    var charCode = (evt.which) ? evt.which : evt.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
}

function get_under_user_ids()
{
	var under_user= $('#under_user_name').val();
	$('#under_user').val(under_user);
}

function get_team_users_ids()
{
	var under_user= $('#team_users_name').val();
	$('#team_users').val(under_user);
}

// $(document).ready(function () {
// 	$("#confirm_password").change(function() { 
//    var password = $("#password").val();
//    var confirmPassword = $("#confirm_password").val();

// 	    if (password !== confirmPassword)
// 	    {
// 	       alert("Confirm Password Doesn't match with Password");
// 	       $("#confirm_password").focus();
// 	    }
//     });
// });



// Get Group Names Based On Category Selection
function get_mobile_no(staff_id = "") {


	var ajax_url = sessionStorage.getItem("folder_crud_link");


	if (staff_id) {
		var data = {
			"staff_id": staff_id,
			"action": "mobile"
		}

		$.ajax({
			type: "POST",
			url: ajax_url,
			data: data,
			success: function (data) {

				if (data) {
					// $("#phone_no").html(data);
				document.getElementById('phone_no').value = data;

				}

				
			}
		});
	}
}

function team_users_div(this_val = '') {
	if (this_val) {
		$(".team_users_class").removeClass("d-none");
	} else {
		$(".team_users_class").addClass("d-none");
		$("#team_users_name").val(null);
	}
}

// Password Validation 
// function validatePassword() {
//       const password = document.getElementById("password").value;
//       const submitBtn = document.getElementsByClassName("createupdate_btn");
//       const checklist = document.getElementById("checklist");
//       const strengthBar = document.getElementById("strength-bar");

//       const length = document.getElementById("length");
//       const lower = document.getElementById("lower");
//       const upper = document.getElementById("upper");
//       const number = document.getElementById("number");
//       const special = document.getElementById("special");
//       const strengthFill = document.getElementById("strength-fill");

//       // Show checklist and strength bar if typing
//       if (password.length > 0) {
//         checklist.classList.remove("hidden");
//         strengthBar.classList.remove("hidden");
//       } else {
//         checklist.classList.add("hidden");
//         strengthBar.classList.add("hidden");
//         submitBtn.disabled = true;
//         strengthFill.style.width = "0%";
//         return;
//       }

//       let strength = 0;

//       if (password.length >= 8) {
//         length.classList.add("valid");
//         strength += 1;
//       } else {
//         length.classList.remove("valid");
//       }

//       if (/[a-z]/.test(password)) {
//         lower.classList.add("valid");
//         strength += 1;
//       } else {
//         lower.classList.remove("valid");
//       }

//       if (/[A-Z]/.test(password)) {
//         upper.classList.add("valid");
//         strength += 1;
//       } else {
//         upper.classList.remove("valid");
//       }

//       if (/[0-9]/.test(password)) {
//         number.classList.add("valid");
//         strength += 1;
//       } else {
//         number.classList.remove("valid");
//       }

//       if (/[^A-Za-z0-9]/.test(password)) {
//         special.classList.add("valid");
//         strength += 1;
//       } else {
//         special.classList.remove("valid");
//       }

//       const colors = ["red", "orange", "gold", "dodgerblue", "green"];
//       strengthFill.style.width = (strength * 20) + "%";
//       strengthFill.style.background = colors[strength - 1] || "transparent";

//       submitBtn.disabled = strength < 5;
//     }
function validatePassword() {
    const password = document.getElementById("password").value;
    const submitBtns = document.getElementsByClassName("createupdate_btn");
    const checklist = document.getElementById("checklist");
    const strengthBar = document.getElementById("strength-bar");

    const length = document.getElementById("length");
    const lower = document.getElementById("lower");
    const upper = document.getElementById("upper");
    const number = document.getElementById("number");
    const special = document.getElementById("special");
    const strengthFill = document.getElementById("strength-fill");

    let strength = 0;

    if (password.length > 0) {
        // checklist.classList.remove("hidden");
        strengthBar.classList.remove("hidden");
    } else {
        checklist.classList.add("hidden");
        strengthBar.classList.add("hidden");
        Array.from(submitBtns).forEach(btn => btn.disabled = true);
        strengthFill.style.width = "0%";
        return 0;
    }

    if (password.length >= 8) { length.classList.add("valid"); strength++; } else { length.classList.remove("valid"); }
    if (/[a-z]/.test(password)) { lower.classList.add("valid"); strength++; } else { lower.classList.remove("valid"); }
    if (/[A-Z]/.test(password)) { upper.classList.add("valid"); strength++; } else { upper.classList.remove("valid"); }
    if (/[0-9]/.test(password)) { number.classList.add("valid"); strength++; } else { number.classList.remove("valid"); }
    if (/[^A-Za-z0-9]/.test(password)) { special.classList.add("valid"); strength++; } else { special.classList.remove("valid"); }

    const colors = ["red", "orange", "gold", "dodgerblue", "green"];
    strengthFill.style.width = (strength * 20) + "%";
    strengthFill.style.background = colors[strength - 1] || "transparent";

    Array.from(submitBtns).forEach(btn => btn.disabled = strength < 5);

    return strength;
}

function togglePasswords() {
  const fields = [document.getElementById("password"), document.getElementById("confirm_password")];
  fields.forEach(field => {
    field.type = field.type === "password" ? "text" : "password";
  });
}

