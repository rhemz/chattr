<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<script type="text/javascript">

var name = "<?=$user->get_name()?>";

function Message(msgJson) {

	this.id = msgJson.message_id;
	this.user_id = msgJson.user_id;
	this.user_name = msgJson.user_name;
	this.text = msgJson.text;
	this.timestamp = msgJson.timestamp;

	this.formatMessage = function() {
		return "(" + this.timestamp + ") " + this.user_name + ": " + this.text;
	}
}


$(document).ready(function() {

	log.info("Current username: " + name);
	/*
		Event listeners
	*/
	$("#inputText").on("keypress", function(e) {
		if(e.which == 13 && $("#inputText").val().length > 0) {
			sendMessage($("#inputText").val());
		}
	});

	$("#sendButton").on("mouseup", function() {
		if($("#inputText").val().length > 0) {
			// sendMessage(sendMessage($("#inputText").val());
		}
	});

	$("#nameText").on("keypress", function(e) {
		if(e.which == 13 && $("#nameText").val().length > 0) {
			$.ajax({
				url: "/rest/user/name",
				cache: false,
				type: "POST",
				timeout: <?=$this->config->get('chatroom.client_send_timeout')?>,
				data: {username: $("#nameText").val()}
			}).done(function(response) {
				log.debug(response);
				var obj = jQuery.parseJSON(response);
				if(obj.success) {
					name = $("#nameText").val();
					log.info("Changed username to " + name);
					$("#nameText").val("");
				} else {
					log.error("Error changing username: " + response);
				}

				$("#inputText, #sendButton").prop("disabled", false); 
			});
		}
	});


	/*
		Function to send client messages.  Right now it disables the inputs until it receives some
		kind of response, however once things are running smoothly, should figure out and allow 
		for simultaneous connections.  Given that they happen asynchronously, might need to start
		attaching timestamps from the client side so the server knows how to properly order them 
		when they're fetched back.
	*/
	function sendMessage(message) {
		$("#inputText, #sendButton").prop("disabled", true);
		
		$.ajax({
			url: "/rest/room/<?=$room_id?>/send",
			cache: false,
			type: "POST",
			timeout: <?=$this->config->get('chatroom.client_send_timeout')?>,
			data: {room_id: "<?=$room_id?>", text: message}
		}).done(function(response) {
			log.debug(response);
			var obj = jQuery.parseJSON(response);
			if(obj.success) {
				$("#inputText").val("");
			} else {
				log.error("Error sending message: " + response);
			}

			$("#inputText, #sendButton").prop("disabled", false); 
		});
	}


	/*
		Message checker.
	*/
	setInterval(function() {

		$.ajax({
			url: "/rest/room/<?=$room_id?>/messages",
			cache: false,
			type: "GET",
			timeout: <?=$this->config->get('chatroom.client_check_timeout')?>
		}).done(function(response) {
			log.debug(response);
			var obj = jQuery.parseJSON(response);
			if(obj.success) {
				$.each(obj.messages, function(index, value) {
					var msg = new Message(value);
					log.info(msg.formatMessage());
				});
			} else {
				log.error("Error retrieving messages: " + response);
			}

		});
	}, <?=$this->config->get('chatroom.client_check_interval')?>);

});

</script>


<p>
	<input type="text" size="80" id="inputText" />
	<input type="button" value="Send" id="sendButton" />
</p>


<p>
	Set name:
	<input type="text" size="20" id="nameText" />
	<br />
	This will probably be a button or something clever that brings up a modal dialogue to set the name.  For now it's just a textfield w/ a listener.
</p>


make ajax GET requests to /rest/room/room_id/messages
<br />
make ajax POST request to /rest/message/send, sending keys 'text' 'room_id'

<?php $this->load_view('common/footer'); ?>