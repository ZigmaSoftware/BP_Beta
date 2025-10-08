$(document).ready(function () {
	// var table_id 	= "user_type_datatable";
	init_datatable(table_id,form_name,action);

	//get_branch_div();

	navigator.geolocation.getCurrentPosition(showPosition, showError);

    function showError(error) {
    	console.error("Geolocation error:", error);
    	alert("Geolocation failed: " + error.message);
    }
	function showPosition(position) {
		var lat = $("#user_latitude").val() || position.coords.latitude;
		var lng = $("#user_longitude").val() || position.coords.longitude;

		var rad   = $("#map_radius").val();

		buildMap(lat, lng, rad);
		
	}

	// get 
	
});

function buildMap(lat = '', lng = '', rad) {
	$('#branch_map').locationpicker({
		location: {
            latitude: lat,
            longitude: lng
        },
        radius: rad || 30,
		zoom: 20,
        inputBinding: {
            latitudeInput: $('#user_latitude'),
            longitudeInput: $('#user_longitude'),
            radiusInput: $('#map_radius')
            // locationNameInput: $('#us2-address')
        }
	});
}

var company_name 	= sessionStorage.getItem("company_name");
var company_address	= sessionStorage.getItem("company_name");
var company_phone 	= sessionStorage.getItem("company_name");
var company_email 	= sessionStorage.getItem("company_name");
var company_logo 	= sessionStorage.getItem("company_name");

var form_name 		= 'company_creation';
var form_header		= '';
var form_footer 	= '';
var table_name 		= '';
var table_id 		= 'company_creation_datatable';
var action 			= "datatable";



let uploadedFiles = [];

document.getElementById("test_file_qual").addEventListener("change", function () {
    const files = this.files;
    const formData = new FormData();

    for (let i = 0; i < files.length; i++) {
        const file = files[i];

        // ✅ Check file size: 2MB = 2 * 1024 * 1024 = 2097152 bytes
        if (file.size > 2097152) {
            Swal.fire({
                icon: 'warning',
                title: 'File too large',
                text: `The file "${file.name}" exceeds 2MB. Please choose a smaller file.`,
            });
            return; // Stop upload
        }

        formData.append("test_file[]", file);
    }

    formData.append("action", "upload_files_only");

    $.ajax({
        type: "POST",
        url: sessionStorage.getItem("folder_crud_link"),
        data: formData,
        contentType: false,
        processData: false,
        success: function (res) {
            const obj = JSON.parse(res);
            if (obj.status) {
                const newFiles = obj.uploaded_files;
                uploadedFiles = [...uploadedFiles, ...newFiles];
                $("#existing_file_attach").val(uploadedFiles.join(","));
                renderFileList();
            } else {
                Swal.fire("Upload failed", "Something went wrong during upload.", "error");
            }
        },
        error: function () {
            Swal.fire("Network error", "Unable to connect to server.", "error");
        }
    });
});


function renderFileList() {
    const container = document.getElementById("file_preview_list");
    container.innerHTML = "";

    uploadedFiles.forEach((file, index) => {
        const ext = file.split('.').pop().toLowerCase();
        const isImage = ["jpg", "jpeg", "png"].includes(ext);
        const filePath = `uploads/company_creation/${file}`;

        const fileElement = document.createElement("div");
        fileElement.className = "mb-1";
        fileElement.innerHTML = `
            ${isImage ? `<img src="${filePath}" width="50" height="50" style="object-fit:cover;margin-right:10px;">` : `<i class="fa fa-file-pdf-o" style="font-size:24px;color:red;margin-right:10px;"></i>`}
            ${file}
            <button type="button" class="btn btn-sm btn-danger ml-2" onclick="deleteFile('${file}', ${index})">X</button>
        `;
        container.appendChild(fileElement);
    });
}

function deleteFile(filename, index) {
    $.ajax({
        type: "POST",
        url: sessionStorage.getItem("folder_crud_link"),
        data: {
            action: "delete_uploaded_file",
            filename: filename
        },
        success: function (res) {
            const obj = JSON.parse(res);
            if (obj.status) {
                // ✅ Reload updated file list from DB-safe field
                let existing = $("#existing_file_attach").val().split(',')
                                .filter(f => f.trim() !== filename.trim());
                uploadedFiles = existing;
                $("#existing_file_attach").val(existing.join(","));
                renderFileList();
            } else {
                alert("Failed to delete file: " + (obj.error || "Unknown error"));
            }
        },
        error: function () {
            alert("Network error");
        }
    });
}


function company_creation_cu(unique_id = "") {

// alert(unique_id);
    var internet_status  = is_online();
    var data = new FormData();
    
    var company_name = $("#company_name").val();
    // alert(company_name);
    var company_code = $("#company_code").val();
    var country      = $("#country").val();
    var state        = $("#state").val();
    var city         = $("#city").val();
var pin_code = $("#pin_code").val().trim();

if (pin_code.length !== 6) {
    alert("Check Pincode");
    return false;
}

    var tel_number   = $("#tel_number").val();
    var pan_number   = $("#pan_number").val().trim();
if (pan_number.length !== 10) {
    alert("Check Pan Number");
    return false;
}

    var gst_number   = $("#gst_number").val().trim();
    
    if (gst_number.length !== 15) {
    alert("Check GST Detials");
    return false;
  }
    var gst_reg_date = $("#gst_reg_date").val();
    var contact_person = $("#contact_person").val();
var contact_number = $("#contact_number").val().trim();

if (contact_number !== "") {
    if (contact_number.length !== 10 || !/^\d{10}$/.test(contact_number)) {
        alert("Check Contact Number");
        return false;
    }
}

    var contact_email_id = $("#contact_email_id").val();
 
    var website = $("#website").val().trim();


    var user_latitude = $("#user_latitude").val();
    var user_longitude = $("#user_longitude").val();
    var file_data = $('#company_logo').prop('files')[0];
    var image_s = document.getElementById("test_file_qual");
    var address = $("#address").val();
    var is_active = $("#is_active").val();
    var existing_company_logo   = $("#existing_company_logo").val();
    var existing_file_attach   = $("#existing_file_attach").val();
    var is_form = form_validity_check("was-validated");
    var ajax_url = sessionStorage.getItem("folder_crud_link");
    var url      = sessionStorage.getItem("list_link");

    if (!internet_status) {
        sweetalert("no_internet");
        return false;
    }

    if (is_form) { 
    data.append("company_name", company_name);
    data.append("company_code", company_code);
    data.append("country", country);
    data.append("state", state);
    data.append("city", city);
    data.append("pin_code", pin_code);
    data.append("tel_number", tel_number);
    data.append("pan_number", pan_number);
    data.append("gst_number", gst_number);
    data.append("gst_reg_date", gst_reg_date);
    data.append("contact_person", contact_person);
    data.append("contact_number", contact_number);
    data.append("contact_email_id", contact_email_id);
    data.append("website", website);
    data.append("user_latitude", user_latitude);
    data.append("user_longitude", user_longitude);
    
    data.append("company_logo", file_data);
    for (var i = 0; i < image_s.files.length; i++) {
    data.append("test_file[]", image_s.files[i]);
    }
    
    data.append("address", address);
    data.append("is_active", is_active);
    data.append("existing_company_logo", existing_company_logo);
    data.append("existing_file_attach", existing_file_attach);
    data.append("unique_id", unique_id);
    data.append("action", "createupdate");
    // alert(data);
        // console.log(data);
//         for (let pair of data.entries()) {
//     console.log(pair[0] + ':', pair[1]);
// }

        $.ajax({
			type 	: "POST",
			url 	: ajax_url,
			data 	: data,
			cache: false,
        contentType: false,
        processData: false,
        method: 'POST',
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

function company_creation_toggle(unique_id = "", new_status = 0) {
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
            if (obj.status) {
                $("#" + table_id).DataTable().ajax.reload(null, false);
            }
            sweetalert(obj.msg, url);
        }
    });
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
					$("#state").html(data);
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
					$("#city").html(data);
				}

			}
		});
	}
}  





function number_only_pincode(event) {
    var charCode = event.which ? event.which : event.keyCode;

    // Allow only numbers (0-9)
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
        return false;
    }

    // Prevent entering more than 6 digits
    var input = document.getElementById("pin_code");
    if (input.value.length >= 6) {
        event.preventDefault();
        return false;
    }

    return true;
}



 
function validate_pan_number(event) {
    const input = event.target;
    const charCode = event.which || event.keyCode;
    const char = String.fromCharCode(charCode);

    // Block if total length is already 10
    if (input.value.length >= 10) {
        event.preventDefault();
        return false;
    }

    // PAN pattern position-based validation
    const position = input.value.length;

    if (position < 5) {
        // First 5 characters: only letters A-Z
        if (!/[a-zA-Z]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position < 9) {
        // Next 4 characters: only numbers 0-9
        if (!/[0-9]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else {
        // Last character: only a letter A-Z
        if (!/[a-zA-Z]/.test(char)) {
            event.preventDefault();
            return false;
        }
    }

    // Convert to uppercase after typing
    setTimeout(() => {
        input.value = input.value.toUpperCase();
    }, 0);

    return true;
}

function number_only_gst(event) {
    const charCode = event.which || event.keyCode;
    const char = String.fromCharCode(charCode).toUpperCase();
    const input = event.target;
    const currentValue = input.value.toUpperCase();

    // Block if not alphanumeric
    if (!/^[A-Z0-9]$/.test(char)) {
        event.preventDefault();
        return false;
    }

    // Block if more than 15 characters
    if (currentValue.length >= 15) {
        event.preventDefault();
        return false;
    }

    // Validate character at specific position
    const position = currentValue.length;

    if (position === 0 || position === 1) {
        // First two characters must be digits (state code)
        if (!/[0-9]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position >= 2 && position <= 6) {
        // Next 5: letters only (PAN)
        if (!/[A-Z]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position >= 7 && position <= 10) {
        // Next 4: digits only (PAN)
        if (!/[0-9]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position === 11) {
        // One letter at end of PAN
        if (!/[A-Z]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position === 12) {
        // Entity number (digit or letter)
        if (!/[A-Z0-9]/.test(char)) {
            event.preventDefault();
            return false;
        }
    } else if (position === 13) {
        // Must be 'Z'
        if (char !== 'Z') {
            event.preventDefault();
            return false;
        }
    } else if (position === 14) {
        // Checksum: any alphanumeric
        if (!/[A-Z0-9]/.test(char)) {
            event.preventDefault();
            return false;
        }
    }

    // Insert character as uppercase
    setTimeout(() => {
        input.value = input.value.toUpperCase();
    }, 0);

    return true;
}
document.getElementById("gst_number").addEventListener("blur", function () {
    const gstInput = this.value.toUpperCase();
    const gstField = this;

    // ✅ Strict GSTIN format validation
    const gstPattern = /^[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[A-Z0-9]{1}$/;

    if (gstInput && !gstPattern.test(gstInput)) {
        Swal.fire({
            icon: 'error',
            title: 'Invalid GST Number',
            text: 'GSTIN must be in the format: 27ABCDE1234F1Z5',
            confirmButtonText: 'OK'
        }).then(() => {
            gstField.value = "";   // Clear invalid input
            gstField.focus();      // Focus back for correction
        });
    } else {
        gstField.value = gstInput; // Store uppercase version
    }
});



function number_only_mobile(event) {
    var charCode = event.which ? event.which : event.keyCode;

    // Allow only numbers (0-9)
    if (charCode < 48 || charCode > 57) {
        event.preventDefault();
        return false;
    }

    // Prevent entering more than 6 digits
    var input = document.getElementById("contact_number");
    if (input.value.length >= 10) {
        event.preventDefault();
        return false;
    }

    return true;
}


function openDocumentWindow(allFilesStr) {
    const files = allFilesStr.split(',');
    const basePath = "../blue_planet_beta/uploads/company_creation/";

    let htmlContent = `
        <html>
        <head>
            <title>Uploaded Documents</title>
            <style>
                body { font-family: Arial; padding: 20px; }
                .file-box { margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; width: 300px; }
                .file-box img, .file-box iframe { width: 100%; max-height: 300px; object-fit: contain; }
                .btn { display: inline-block; margin-top: 10px; padding: 5px 10px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; }
                .btn-danger { background: #dc3545; }
            </style>
        </head>
        <body>
            <h2>Uploaded Files</h2>
    `;

    files.forEach((file, index) => {
        const ext = file.split('.').pop().toLowerCase();
        const isImage = ['jpg', 'jpeg', 'png'].includes(ext);
        const filePath = basePath + file.trim();

        htmlContent += `
            <div class="file-box" id="file-box-${index}">
                ${isImage 
                    ? `<img src="${filePath}" alt="Document">`
                    : `<iframe src="${filePath}"></iframe>`}
                <br>
                <a class="btn" href="${filePath}" download>Download</a>
                <a class="btn btn-danger" href="javascript:void(0)" onclick="deleteFile('${file.trim()}', 'file-box-${index}')">Delete</a>
            </div>
        `;
    });

    // Inline delete function with DOM removal
    htmlContent += `
        <script>
            function deleteFile(filename, boxId) {
                if (confirm("Are you sure to delete " + filename + "?")) {
                    fetch("${sessionStorage.getItem("folder_crud_link")}", {
                        method: "POST",
                        headers: { "Content-Type": "application/x-www-form-urlencoded" },
                        body: "action=delete_uploaded_file&filename=" + encodeURIComponent(filename)
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status) {
                            alert("Deleted Successfully");
                            const box = document.getElementById(boxId);
                            if (box) box.remove(); // ✅ Remove file box without reload
                        } else {
                            alert("Delete failed: " + data.error);
                        }
                    })
                    .catch(() => alert("Network Error"));
                }
            }
        </script>
    `;

    htmlContent += `</body></html>`;

    const win = window.open('', '_blank', 'height=700,width=1100');
    win.document.write(htmlContent);
    win.document.close();
}


function deleteModalFile(filename) {
    Swal.fire({
        title: 'Are you sure?',
        text: `Delete ${filename}?`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(sessionStorage.getItem("folder_crud_link"), {
                action: "delete_uploaded_file",
                filename: filename
            }, function (res) {
                const obj = JSON.parse(res);
                if (obj.status) {
                    Swal.fire('Deleted!', 'File has been deleted.', 'success');
                    $("#documentModal").modal("hide");
                } else {
                    Swal.fire('Error', obj.error || 'Unable to delete.', 'error');
                }
            });
        }
    });
}

function get_branch_div() {

	var company_branch_type = $("#company_branch_type").val();

	if ((company_branch_type == 1) || (company_branch_type == "")) {
		$(".branch_div").hide();
		$(".branch_div_input").removeAttr('required');
	} else {
		$(".branch_div").show();
		$(".branch_div_input").attr("required", "true");
	}
}

function new_external_window_image(image_url) {
    // Open the image in a new window
    window.open(image_url, '_blank', 'height=550,width=950,resizable=no,left=200,top=150,toolbar=no,location=no,directories=no,status=no,menubar=no');
}