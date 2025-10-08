$(document).ready(function () {
	contacts();
});


setInterval(function(){
	contacts();

	let receiver_id = $("#receiver_id").val();
	let receiver_name = $("#receiver_name").val();

	if (receiver_id) {
		chats(receiver_id,receiver_name);
	}

}, 1000);

var ajax_url = sessionStorage.getItem("folder_crud_link");
var url      = sessionStorage.getItem("list_link");

function contacts() {

	let data = {
		action : "contacts"
	};
	$.ajax({
		type 	: 'POST',
		url		: ajax_url,
		data 	: data,
		success : function (result) {
			$("#contacts_list").html(result);
		}
	});
}

function chats(receiver_id = "",receiver_name = "") {

	if (receiver_id) {

		// Set Receiver id in Hidden Field
		$("#receiver_id").val(receiver_id);
		$("#receiver_name").val(receiver_name);
		$("#dis_receiver_name").text(receiver_name);

		let data = {
			receiver_id : receiver_id,
			action 		: "chats"
		};
		$.ajax({
			type 	: 'POST',
			url		: ajax_url,
			data 	: data,
			success : function (result) {

				let json 	= JSON.parse(result);
				let chats 	= json.chats;
				let active 	= json.active;

				$("#chat_list").html(chats);

				$("#receiver_status").text(active);

				if (active != "Active") {
					$("#receiver_status_icon").removeClass("text-success");
					$("#receiver_status_icon").addClass("text-danger");
				} else {
					$("#receiver_status_icon").removeClass("text-danger");
					$("#receiver_status_icon").addClass("text-success");
				}

				$(".chat_message").last().focus();
			}
		});
	} else {
		alert("Chat Not Loading - Contact Developers");
	}
}

function in_message() {

	let receiver_id = $('#receiver_id').val();
	let receiver_name = $("#receiver_name").val();
	let message 	= $('#message').val();

	// alert(receiver_id);
	// alert(message);

	if (receiver_id) {

		if (message) {

			let data = {
				receiver_id : receiver_id,
				message 	: message,
				action 		: "message"
			};

			$.ajax({
				type 	: 'POST',
				url		: ajax_url,
				data 	: data,
				success : function (result) {

					chats(receiver_id,receiver_name);
					$('#message').val("");
					
				}
			});
		}
	} else {
		alert("Chat Not Loading - Contact Developers");
	}
}

