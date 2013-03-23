<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<script type="text/javascript">

var name = "<?=$user->get_name()?>";
var chat = new Chat();
var users = new Array();


/*
	Objects
*/
function Message(msgJson) {

	this.id = msgJson.message_id;
	this.user_id = msgJson.user_id;
	this.user_name = msgJson.user_name;
	this.text = msgJson.text;
	this.timestamp = msgJson.timestamp;

	this.formatMessage = function() {
		return "(" + this.formatTime() + ") " + this.user_name + ": " + this.text;
	}

	this.formatTime = function() {
		var date = new Date([Math.round(this.timestamp)] * 1000);

		// return date.getHours() + ":" + date.getMinutes() + ":" + date.getSeconds();
		return date.toLocaleTimeString();
	}

	this.messageHTML = function() {
		// this html will be fancier once its gussied up
		return "<p>(" + this.formatTime() + ") <b>" + this.user_name + "</b>: " + this.text + "</p>";
	}

	this.messageNotification = function() {
		return this.user_name + ": " + this.text;
	}
}


function User(userObj) {

	this.id = userObj.id;
	this.name = userObj.name;

	this.checkName = function(name) {
		if(this.name !== name) {
			var msg = this.name + " changed their name to " + name;
			chat.addSystemMessage(msg);
			this.name = name;

			<?php if(ENVIRONMENT == Environment::Development): ?>
				log.info(msg);
			<?php endif; ?>
		}
	}
}


function Chat() {

	this.div = "#mainChat";

	this.addUserMessage = function(msgObject) {
		$(this.div).append(msgObject.messageHTML());
		
		if(window.webkitNotifications.checkPermission() == 0) {
			var n = window.webkitNotifications.createNotification('/public/images/notification.png', 'New Message', msgObject.messageNotification());
			n.show();
		}

		$(this.div).animate({
			scrollTop: $(this.div)[0].scrollHeight
		}, 'slow');
	}

	this.addSystemMessage = function(message) {
		$(this.div).append('<p class="systemMessage">' + message + '</p>');

		$(this.div).animate({
			scrollTop: $(this.div)[0].scrollHeight
		}, 'slow');
	}
}


$(document).ready(function() {

	log.info("Current username: " + name);

	

	$("#html5notify").click(function(e) {

		if(window.webkitNotifications) {
			if($("#html5notify").is(":checked")) {
				if(window.webkitNotifications.checkPermission() != 0) {
					log.info("requesting webkitNotifications permission");
					window.webkitNotifications.requestPermission();
				}
			} else {
				// disable them.  probably going to have to set a cookie when they're enabled and change this value
				log.info("disabling webkitNotifications");
			}
		}
	});


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
			sendMessage($("#inputText").val());
		}
	});

	$("#nameText").on("keypress", function(e) {
		if(e.which == 13 && $("#nameText").val().length > 0) {
			$.ajax({
				url: "/rest/user/name",
				cache: false,
				type: "PUT",
				timeout: <?=$this->config->get('chatroom.message_send_timeout')?>,
				data: {username: $("#nameText").val()}
			}).done(function(response) {
				<?php if(ENVIRONMENT == Environment::Development): ?>
					log.debug(response);
				<?php endif; ?>

				
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
		// $("#inputText, #sendButton").prop("disabled", true);
		
		$.ajax({
			url: "/rest/room/<?=$room_id?>/send",
			cache: false,
			type: "POST",
			timeout: <?=$this->config->get('chatroom.message_send_timeout')?>,
			data: {room_id: "<?=$room_id?>", text: message}
		}).done(function(response) {
			<?php if(ENVIRONMENT == Environment::Development): ?>
				log.debug(response);
			<?php endif; ?>

			var obj = jQuery.parseJSON(response);
			if(obj.success) {
				$("#inputText").val("");
			} else {
				log.error("Error sending message: " + response);
			}

			// $("#inputText, #sendButton").prop("disabled", false); 
		});
	}


	function updateUsers(userList) {

		$("#userList").empty();

		$.each(userList.users, function(index, value) {

			var r = $.grep(users, function(e) {
				return e.id == value.id;
			});
			if(r.length == 1) {
				r[0].checkName(value.name);
			} else if(r.length == 0) {
				users.push(new User(value));
			}

			// if the name exists in the new userlist and not the selectbox, add it to the select
			// if the name exists in the selectbox but not the userlist, remove it from the select
			// if the name exists in both, do nothing

			// for now just wipe the select box entries and re-add them.  might cause flickering?
			// http://stackoverflow.com/questions/646317/how-can-i-check-whether-a-option-already-exist-in-select-by-jquery
			// http://stackoverflow.com/questions/1964839/jquery-please-wait-loading-animation
			$("#userList").append('<li>' + value.name + '</li>');

		});
	}


	/*
		Message checker.
	*/

	function checkForMessages() {
		$.ajax({
			url: "/rest/room/<?=$room_id?>/messages",
			cache: false,
			type: "GET",
			timeout: <?=$this->config->get('chatroom.message_check_timeout')?>
		}).done(function(response) {
			<?php if(ENVIRONMENT == Environment::Development): ?>
				log.debug(response);
			<?php endif; ?>

			var obj = jQuery.parseJSON(response);
			if(obj.success) {
				$.each(obj.messages, function(index, value) {
					var msg = new Message(value);

					<?php if(ENVIRONMENT == Environment::Development): ?>
						log.info(msg.formatMessage());
					<?php endif; ?>

					chat.addUserMessage(msg);
				});
			} else {
				<?php if(ENVIRONMENT == Environment::Development): ?>
					log.error("Error retrieving messages: " + response);
				<?php endif; ?>
			}

		});
	}
	checkForMessages();
	setInterval(checkForMessages, <?=$this->config->get('chatroom.message_check_interval')?>);


	/*
		Room participants check.  Eventually this might be merged with message check to cut down on # of requests.
	*/

	function getRoomParticipants() {
		$.ajax({
			url: "/rest/room/<?=$room_id?>/users",
			cache: false,
			type: "GET",
			timeout: <?=$this->config->get('chatroom.room_check_timeout')?>
		}).done(function(response) {
			<?php if(ENVIRONMENT == Environment::Development): ?>
				log.debug(response);
			<?php endif; ?>

			var obj = jQuery.parseJSON(response);
			if(obj.success) {
				updateUsers(obj);
			} else {
				<?php if(ENVIRONMENT == Environment::Development): ?>
					log.error("Error retrieving room list: " + response);
				<?php endif; ?>
			}

		});
	}
	getRoomParticipants();
	setInterval(getRoomParticipants, <?=$this->config->get('chatroom.room_check_interval')?>)

});

</script>


<div id="mainChat">

</div>

<div id="userDiv">
	<ul id="userList">
		<li>users</li>
	</ul>
</div>


<p>
	<input type="text" size="120" id="inputText" />
	<input type="button" value="Send" id="sendButton" />
</p>


<p>
	Set name:
	<input type="text" size="20" id="nameText" />
	<br />
	This will probably be a button or something clever that brings up a modal dialogue to set the name.  For now it's just a textfield w/ a listener.
</p>


<p>
	Hit F2 to bring up the debug console to see whats going on.  Filter out the raw responses by unchecking the green box in it.
</p>

<p>
	<input type="checkbox" id="html5notify" value="" />
	<label for="html5notify">Enable Notifications</label>
</p>

<div class="ajaxmodal"><!-- ajax loader --></div>

<?php $this->load_view('common/footer'); ?>