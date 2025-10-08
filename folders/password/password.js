
var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'User Type';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';

function password_cu(unique_id = "") {

    var internet_status  = is_online();

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

	var new_password 		= $("#new_password").val();
	var con_password 	    = $("#confirm_password").val(); 
	var old_password 	    = $("#old_password").val(); 
	var image_name 	        = $("#image_name").val();

	if (new_password != con_password) {
		sweetalert("custom","","","Confirm Password Dosen't Match");
		return false;
	}
	//alert(old_password); alert(image_name);

    //var is_form = form_validity_check("was-validated");

    if ((old_password)||(image_name)) {

        var data 	 = $(".was-validated").serialize();
        data 		+= "&unique_id="+unique_id+"&action=createupdate";

        var ajax_url = sessionStorage.getItem("folder_crud_link");
        var url      = "";

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
				} else{
						sweetalert("custom","","","Old Password Was Not Correct");
						$(".createupdate_btn").removeAttr("disabled","disabled");
						$(".createupdate_btn").text("Save");
						
				}
				if(msg == "update") {
				sweetalert(msg,"","","Successfully Saved");
				window.location.href = "logout.php";
				}	
				
			},
			error 		: function(data) {
				alert("Network Error");
			}
		});


    } else {
        sweetalert("password_alert");
    }
}


// Password Validation 
function validatePassword() {
    const password = document.getElementById("new_password").value;
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
        checklist.classList.remove("hidden");
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