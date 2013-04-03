<script type="text/javascript">

var name = "<?=$user->get_name()?>";
var chat = new Chat();
var users = new Array();
var window_focus = true;
var notification;
var notifying = false;


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
		return "<p class=\"message\"><span class=\"time\">" + this.formatTime() + "</span><span class=\"user\">" + this.user_name + "</span><span class=\"text\">" + this.text + "</span></p>";
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
			var msg = "<span class=\"name\">" + this.name + "</span> changed their name to <span class=\"name\">" + name + "</span>";
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
		
		if(window.webkitNotifications && window.webkitNotifications.checkPermission() == 0 && !window_focus && notifying) {
			if(notification != null) {
				notification.cancel();
			}
			notification = window.webkitNotifications.createNotification(
				'<?=$this->config->get('chatroom.notification_path')?>', 
				'<?=$this->config->get('chatroom.notification_title')?>', 
				msgObject.messageNotification());

			notification.ondisplay = function(e) {
				setTimeout(function() { e.currentTarget.cancel(); }, <?=$this->config->get('chatroom.notification_timeout')?>);
			}
			notification.onclick = function() {
				window.focus();
				this.cancel();
			}
			notification.show();
		}

		$(this.div).animate({
			scrollTop: $(this.div)[0].scrollHeight,
			queue: false
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

	$(window).focus(function() {
		window_focus = true;
	}).blur(function() {
		window_focus = false;
	});

	if(t = ($.cookie('<?=$this->config->get('chatroom.notification_cookie')?>') == 'true')) {
		notifying = t;
		$("#html5notify").attr('checked', t ? 'checked' : null);
	}


	$("#html5notify").click(function(e) {

		if(window.webkitNotifications) {
			if($("#html5notify").is(":checked")) {
				if(window.webkitNotifications.checkPermission() != 0) {
					log.info("requesting webkitNotifications permission");
					window.webkitNotifications.requestPermission();
				}

				$.cookie('<?=$this->config->get('chatroom.notification_cookie')?>', true, { expires: 365, path: '/' });
				notifying = true;

			} else {
				log.info("disabling webkitNotifications");
				$.cookie('<?=$this->config->get('chatroom.notification_cookie')?>', false, { expires: 365, path: '/' });
				notifying = false;
			}
		}
	});


	/*
		Event listeners
	*/
	$("#inputText").on("keypress", function(e) {
		if(e.which === 13 && $("#inputText").val().length > 0) {
			e.preventDefault();
			sendMessage($("#inputText").val());
		}
	});

	$("#sendButton").on("click", function() {
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
					$('.username').html(name);
					$('.options').slideUp(100);
					log.info("Changed username to " + name);
					$("#nameText").val("");
				} else {
					log.error("Error changing username: " + response);
				}

				$("#inputText, #sendButton").prop("disabled", false); 
			});
		}
	});

	$('.optionsarrow a').on("click", function() {
		$('.options').slideToggle(100);
	});

	$('.username').on('dblclick', function(e) {
		e.preventDefault();
		$('.options').show();
		$('#nameText').focus();
	});


	function sendMessage(message) {

		$("#inputText").attr("value", "");

		// some client side validation
		if(message.length > <?=$this->config->get('message.max_length')?>) {
			chat.addSystemMessage('The maximum message length you can send is <?=$this->config->get('message.max_length')?> characters');
		}
		
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
				//$("#inputText").val("");
			} else {
				$("#inputText").val(message);
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

					// blink title for last message
					if(!window_focus && index == (obj.messages.length - 1)) {
						flashTitle(msg.messageNotification());
					}
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
	setInterval(getRoomParticipants, <?=$this->config->get('chatroom.room_check_interval')?>);



	// blink title
	(function () {

	var original = document.title;
	var timeout;

	window.flashTitle = function (newMsg, counter) {
	    function step() {
	        document.title = (document.title == original) ? newMsg : original;

	        if (--counter > 0) {
	            timeout = setTimeout(step, <?=$this->config->get('chatroom.title_blink_delay')?>);
	        };
	    };

	    counter = parseInt(counter);

	    if (isNaN(counter)) {
	        counter = <?=$this->config->get('chatroom.title_blink_count')?>;
	    };

	    clearTimeout(timeout);

	    step();
	};

	window.cancelFlashTitle = function () {
	    clearTimeout(timeout);
	    document.title = original;
	};

	}());

});

</script>