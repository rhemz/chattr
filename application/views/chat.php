<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<?php $this->load_view('js/chat', array('user' => $user, 'room_id' => $room_id)); ?>
<div class="colorbar"></div>
<div id="content">
	<div id="topPane">
		<div class="left"><span class="logo">Chattr</span></div>
		<div class="right">
			<div class="username">rickdmer</div>
			<div class="optionsarrow"><a href="#"></a></div>
		</div>
		<div class="options">
			<div class="nameinput">
				<input type="text" id="nameText" />
			</div>
			<div class="notification">
				<input type="checkbox" id="html5notify" value="" />
				<label for="html5notify">Enable Notifications</label>
			</div>	
			<div>
				Current theme: <?=$user->get_theme()?>
			</div>
		</div>	
	</div>

	<div id="middle">
		<div id="mainChat"></div>

		<div id="userDiv">
			<ul id="userList">
				<li>users</li>
			</ul>
		</div>
	</div>


	<div class="form">
		<div class="textarea">
			<textarea id="inputText"></textarea>
		</div>
		<input class="button" type="button" value="Send" id="sendButton" />
	</div>
</div>

<div class="ajaxmodal"><!-- ajax loader --></div>

<?php $this->load_view('common/footer'); ?>