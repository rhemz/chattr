<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>
<?php $this->load_view('js/chat', array('user' => $user, 'room_id' => $room_id)); ?>

<div class="colorbar"></div>
<div id="content">
	<div id="topPane">
		<div class="left"><a href="/"><span class="logo no-select">Chattr</span></a><span class="sublogo no-select">beta</span></div>
		<div class="right">
			<div class="username no-select oneline"><?=$user->get_name()?></div>
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
			<div class="feelingDangerous">
				<input type="checkbox" id="feelingDangerous" value="" />
				<label for="feelingDangerous">Allow Raw Messages</label>
			</div>
			<div>
				Current theme: <?=$user->get_theme()?>
			</div>
		</div>	
	</div>

	<div id="middle">
		<div id="mainChat"></div>

		<div id="userDiv">
			<ul id="userList"></ul>
		</div>
	</div>


	<div class="form">
		<div class="textarea">
			<textarea id="inputText"></textarea>
		</div>
		<div role="button" id="sendButton" class="button">Send</div>
	</div>
</div>

<div class="ajaxmodal"><!-- ajax loader --></div>

<?php $this->load_view('common/footer'); ?>