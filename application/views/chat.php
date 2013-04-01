<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<?php $this->load_view('js/chat', array('user' => $user, 'room_id' => $room_id)); ?>


<div id="mainChat"></div>

<div id="userDiv">
	<ul id="userList">
		<li>users</li>
	</ul>
</div>


<p>
	<textarea type="text" id="inputText"> </textarea>
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

<p>
	Current theme: <?=$user->get_theme()?>
</p>

<div class="ajaxmodal"><!-- ajax loader --></div>

<?php $this->load_view('common/footer'); ?>