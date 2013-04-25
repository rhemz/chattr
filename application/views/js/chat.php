<script type="text/javascript">

var name = "<?=$user->get_name()?>";
var soundsEnabled = true;
var chat = new Chat();
var users = new Array();
var window_focus = true;
var notification;
var notifying = false;
var feelingDangerous = false;
var messageSound = ['/public/sounds/standard_msg_receive.mp3', '/public/sounds/standard_msg_receive.ogg'];
var joinSound = ['/public/sounds/join_chat.mp3', '/public/sounds/join_chat.ogg'];
var leaveSound = ['/public/sounds/leave_chat.mp3', '/public/sounds/leave_chat.ogg'];

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
		var messageDom = $(msgObject.messageHTML());
		messageDom.css('visibility','visible').hide();
		$(this.div).append(messageDom);
		//$(this.div).children().last().fadeIn(250);

		if ($(this.div).children().last().find('img').length > 0) {
			var instance = this;
			$(this.div).children().last().find('img').imagesLoaded(function (e) {
				$(instance.div).children().last().slideDown(250);
				scrollToMessagesBottom();
				instance.playMessageSound();
			});
		} else if ($(this.div).children().last().find('iframe').length > 0) {
			$(this.div).children().last().find('iframe').wrap('<div class="videoWrapper" />');
			// create div and move iframe to that
			$(this.div).children().last().slideDown(250);
			scrollToMessagesBottom();
			this.playMessageSound();
		} else {
			$(this.div).children().last().slideDown(500, 'custom');
			scrollToMessagesBottom();
			this.playMessageSound();
		}

		openNotification(msgObject);
	}

	this.addSystemMessage = function(message) {
		var messageDom = $('<p class="systemMessage">' + message + '</p>');
		messageDom.css('visibility','visible').hide();
		$(this.div).append(messageDom);
		$(this.div).children().last().slideDown('slow', 'custom');

		//openNotification(message);
		
		scrollToMessagesBottom();
	}

	this.playMessageSound = function () {
		if (soundsEnabled) {
			$.playSound(messageSound);
		}
	}

	this.playJoinSound = function () {
		if (soundsEnabled) {
			$.playSound(joinSound);
		}
	}

	this.playLeaveSound = function () {
		if (soundsEnabled) {
			$.playSound(leaveSound);
		}
	}
}

function scrollToMessagesBottom() {
	console.log($('#mainChat').scrollTop() + $('#mainChat').innerHeight() - $('#mainChat')[0].scrollHeight );
	if ( Math.abs( $('#mainChat').scrollTop() + $('#mainChat').innerHeight() - $('#mainChat')[0].scrollHeight ) < 50) {
		$('#mainChat').scrollTo($('#mainChat').children().last());
		//$('#mainChat').animate( { scrollTop: $('#mainChat')[0].scrollHeight }, { queue: false, duration: 600 });
	}	
}

function openNotification(msgObject) {
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
}


$(document).ready(function() {

	$.fn.imagesLoaded = function(callback, fireOne) {
		var	args = arguments,
		elems = this.filter('img'),
		elemsLen = elems.length - 1;

		elems.bind('load', function(e) {
			if (fireOne) {
				!elemsLen-- && callback.call(elems, e);
			} else {
				callback.call(this, e);
			}
		}).each(function() {
			// cached images don't fire load sometimes, so we reset src.
			if (this.complete || this.complete === undefined){
				this.src = this.src;
			}
		});
	}

	$.extend({
		playSound: function(){
			$('.playSound').remove();
			return $('<audio autoplay class="playSound"><source src="'+arguments[0][0]+'" type="audio/mpeg"><source src="'+arguments[0][1]+'" type="audio/ogg"><embed height="50" width="100" src="'+arguments[0]+'"><embed src="'+arguments[0]+'"" hidden="true" autostart="true" loop="false" class="playSound"></audio>').appendTo('body');
		}
	});

	$.easing.custom = function (x, t, b, c, d) {
		if ((t/=d) < (1/2.75)) {
			return c*(7.5625*t*t) + b;
		} else if (t < (2/2.75)) {
			return c*(7.5625*(t-=(1.5/2.75))*t + .75) + b;
		} else if (t < (2.5/2.75)) {
			return c*(7.5625*(t-=(2.25/2.75))*t + .9375) + b;
		} else {
			return c*(7.5625*(t-=(2.625/2.75))*t + .984375) + b;
		}
	}

	var currentUsers;

	log.info("Current username: " + name);

	$(window).focus(function() {
		window_focus = true;
	}).blur(function() {
		window_focus = false;
	});

	var mobileView = false;
	var mobileSizeCheck = function() {
		if ($(window).width() <= 500) {
			$('body').addClass('mobile');
			$('#mainChat').width('100%');
			$('#mainChat').height($('#middle').height() - $('#userDiv').height());
			mobileView = true;
			return true;
		} else {
			$('body').removeClass('mobile');
			$('#mainChat').outerWidth($(window).width() - 240);
			$('#mainChat').height('100%');
			mobileView = false;
			return false;
		}
	};

	var userDiv = $('#userDiv');
	var userListOpen = false;
	var usersListRefresh = function() {
		if (mobileView) {
			if (userListOpen) {
				userDiv.addClass('expanded');
				$('#userList').css({
					'display': 'block',
					'max-height': $('#middle').height() - $('#userDiv .heading').outerHeight()
				});
			} else {
				userDiv.removeClass('expanded');
				$('#userList').css('display', 'none');
			}
		} else {
			$('#userList').css('display', 'block');
		}
	};

	var openUserList = function (open) {
		userListOpen = open;
		usersListRefresh();
	}

	// scroll to bottom of chat on window resize
	$(window).resize(function() {
		$('#mainChat').scrollTop($('#mainChat')[0].scrollHeight);
		mobileSizeCheck();
		openUserList(false);
	});

	mobileSizeCheck();

	// set checked state for notifications option
	if ( t = ( $.cookie('<?=$this->config->get('chatroom.notification_cookie')?>') == 'true' ) ) {
		setNotificationStateEnabled(t);
	}
	// when clicking users header, expand/collapse users list
	userDiv.on('click', '.heading', function() {		
		if (userDiv.hasClass('expanded')) {
			openUserList(false);
		} else {
			openUserList(true);	
		}
		mobileSizeCheck();
	});

	// Apply active styling to textarea div when input is selected
	$('#inputText').on('focus blur', function(e) {
		if (e.type === 'focus') {
			$('.textarea').addClass('active');
		} else {
			$('.textarea').removeClass('active');
		}
	});
	
	// set checked state for html messages option
	if(t = ($.cookie('<?=$this->config->get('chatroom.raw_messages_cookie')?>') == 'true')) {
		feelingDangerous = t;
		$("#feelingDangerous").attr('checked', t ? 'checked' : null);
	}

	var success = function() {
		if (feelingDangerous === true) {
			$.cookie('<?=$this->config->get('chatroom.raw_messages_cookie')?>', false, { expires: 365, path: '/' });
			feelingDangerous = false;
			chat.addSystemMessage('Konami code entered. God mode disabled.');	
		} else {
			$.cookie('<?=$this->config->get('chatroom.raw_messages_cookie')?>', true, { expires: 365, path: '/' });
			feelingDangerous = true;
			chat.addSystemMessage('Konami code entered. God mode enabled.');	
		}
		
	}	
	var konami = new Konami(success);

	$('#topPane .right, #middle, div.form').css('visibility','visible').hide().fadeIn(250);

	/**
	 * If the user has an anoymous name, prompt them to change it.
	 */
	if ( name.indexOf('Anonymous') >= 0 ) {
		openMenu();
	}

	// Hide modal window on click
	$('.initial-options .modalbg, .initial-options .innermodal .close').on('click', function() {
		closeMenu();
	});

	if (window.webkitNotifications) {
		// notifications toggle option
		$("#notificationsButton").click(function(e) {
			if(!notifying) {
				notifying = true;
				if(window.webkitNotifications.checkPermission() !== 0) {
					window.webkitNotifications.requestPermission(function() {
						if (window.webkitNotifications.checkPermission() === 0) {
							setNotificationStateEnabled(true);
						} else {
							$('.enablenotifications p').html('You have permenantly denied notifications from this site.');
						}
					});
				} else {
					setNotificationStateEnabled(true);
				}
			} else {
				setNotificationStateEnabled(false);
			}
		});
	} else {
		$("#notificationsButton").addClass('disabled');
		$("#notificationsButton").html('Notifications not supported');
	}

	
	// html messages toggle option
	$("#feelingDangerous").click(function(e) {
		if($("#feelingDangerous").is(":checked")) {
				$.cookie('<?=$this->config->get('chatroom.raw_messages_cookie')?>', true, { expires: 365, path: '/' });
				log.info("enabling raw messages");
				feelingDangerous = true;
			} else {
				$.cookie('<?=$this->config->get('chatroom.raw_messages_cookie')?>', false, { expires: 365, path: '/' });
				log.info("disabling raw messages");
				feelingDangerous = false;
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
		$('#inputText').focus();
	});

	$('#content').on('keypress', '.nameText', function(e) {
		var nameString = $.trim($('.nameText').val());
		if(e.which == 13) {
			$(this).blur();
			closeMenu();
			if (nameString.length >= <?=$this->config->get('user.username_min_length')?>) {
				$.ajax({
					url: "/rest/user/name",
					cache: false,
					type: "PUT",
					timeout: <?=$this->config->get('chatroom.message_send_timeout')?>,
					data: {username: nameString}
				}).done(function(response) {
					<?php if(ENVIRONMENT == Environment::Development): ?>
						log.debug(response);
					<?php endif; ?>

					
					var obj = jQuery.parseJSON(response);
					if(obj.success) {
						setUserName(nameString);
					} else {
						log.error("Error changing username: " + response);
						$(".nameText").val("").addClass('invalid');
					}

					$("#inputText, #sendButton").prop("disabled", false); 
				});
			} else {
				console.log('User name not long enough');
				$(".nameText").val("").addClass('invalid');
				chat.addSystemMessage('The minimum username length is ' + <?=$this->config->get('user.username_min_length')?> + ' characters.');
			}
		}
	});

	$('.optionsarrow a').on('click', function() {
		openMenu();
	});

	function editName(e) {
		e.preventDefault();
		var usernameDiv = $('.user<?=$user->get_id()?>');
		usernameDiv.parent().addClass('editing');
		usernameDiv.html('<input type="text" class="nameText">');
		var input = usernameDiv.children('input');
		input.focus();
		input.on('blur', function() {
			usernameDiv.parent().removeClass('editing');
			usernameDiv.html(name);
			input.off('blur');
		});
	}

	$('body').on('dblclick', '.user<?=$user->get_id()?>', editName);
	$('body').on('click', '.currentuser .edit', editName);
	
	$('#userDiv .heading').on('click', function() {
		// show/hide users
	});

	$('#userDiv').on('click', '.message-icon', function(e) {
		var targetUserId = e.target.id;
		startPrivateMessage(targetUserId);
	});

	$('#inputText').focus();

	function setNotificationStateEnabled(enabled) {
		if (enabled) {
			$("#notificationsButton").addClass('enabled');
			$(".enablenotifications p").html('Click below to <b>disable</b> notifications');
			$("#notificationsButton").html('Disable Notifications');
			$.cookie('<?=$this->config->get('chatroom.notification_cookie')?>', true, { expires: 365, path: '/' });
			notifying = true;
		} else {
			$("#notificationsButton").removeClass('enabled');
			$(".enablenotifications p").html('Click below to <b>enable</b> notifications');
			$("#notificationsButton").html('Enable Notifications');
			$.cookie('<?=$this->config->get('chatroom.notification_cookie')?>', false, { expires: 365, path: '/' });
			notifying = false;
		}
	}

	function setUserName(nameString) {
		name = nameString;
		$('.username').html(name);
		$(".nameText").val("").removeClass('invalid');
	}

	function openMenu() {
		$('.initial-options').fadeIn(250, function() {
			$('.initial-options .innermodal').fadeIn(250);
			$('.initial-options .nameText').focus();
		});
	}

	function closeMenu() {
		$('.initial-options .innermodal').fadeOut(250, function() {
			$('.initial-options').fadeOut(250);
		});
	}

	function sendMessage(message) {

		message = $.trim(message);

		$("#inputText").attr("value", "");

		// some client side validation
		if(message.length > <?=$this->config->get('message.max_length')?>) {
			chat.addSystemMessage('The maximum message length you can send is <?=$this->config->get('message.max_length')?> characters');
		} else {
			//scrollToMessagesBottom();
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

	function startPrivateMessage(userId) {
		console.log(userId);
	}


	function updateUsers(userList) {

		var updateUserList = function() {
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
				var userDom = $('<li><span class="name user' + value.id + '">' + value.name + '</span><span  id="user' + value.id + '" class="message-icon no-select"></span></li>');
				if (value.id == <?=$user->get_id()?>) {
					userDom.addClass('currentuser');
					userDom.append($('<span class="icon edit"></span>'));
				}
				$("#userList").append(userDom);
				usersListRefresh();
				

			});
		}

		// if current users has not been defined
		if (currentUsers === undefined) {
			currentUsers = userList;
			updateUserList();
		// if the known users does not match the sent users
		} else if (JSON.stringify(currentUsers.users) !== JSON.stringify(userList.users)) {
			userDiff(currentUsers.users, userList.users);
			updateUserList();
			currentUsers = userList;
		}
	}

	function userDiff(oldUsers, newUsers) {
		if (oldUsers.length > newUsers.length) {
			// look through each old user and see if its id stil exists
			for (var i = 0; i < oldUsers.length; i++) {
				if (newUsers.filter(function (user) { return user.id == oldUsers[i].id}).length === 0) {
					var msg = "<span class=\"name\">" + oldUsers[i].name + "</span> has left the chat.</span>";					
					chat.addSystemMessage(msg);
					chat.playLeaveSound();
				}
			}
		} else if (oldUsers.length < newUsers.length) {
			for (var i = 0; i < newUsers.length; i++) {
				if (oldUsers.filter(function (user) { return user.id == newUsers[i].id}).length === 0) {
					var msg = "<span class=\"name\">" + newUsers[i].name + "</span> has joined the chat.</span>";					
					chat.addSystemMessage(msg);
					chat.playJoinSound();
				}
			}
		}
	}


	/*
		Message checker.
	*/
	function checkForMessages() {
		$.ajax({
			url: "/rest/room/<?=$room_id?>/messages" + (feelingDangerous ? "?raw" : ""),
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