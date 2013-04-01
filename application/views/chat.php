<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<?php $this->load_view('js/chat', array('user' => $user, 'room_id' => $room_id)); ?>

<div id="topPane">
	<div class="name">
		<input type="text" size="20" id="nameText" />
	</div>
	<div class="notification">
		<input type="checkbox" id="html5notify" value="" />
		<label for="html5notify">Enable Notifications</label>
	</div>	
	<div>
		Current theme: <?=$user->get_theme()?>
	</div>
	
</div>

<div id="mainChat"></div>

<div id="userDiv">
	<ul id="userList">
		<li>users</li>
	</ul>
</div>


<p class="form">
	<textarea id="inputText"></textarea>
	<input type="button" value="Send" id="sendButton" />
</p>

<div class="ajaxmodal"><!-- ajax loader --></div>

<?php $this->load_view('common/footer'); ?>