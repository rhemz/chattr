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
		<!-- <div class="options">
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
		</div>	 -->
	</div>

	<div id="middle">
		<div id="mainChat"></div>

		<div id="userDiv">
			<div class="heading">Users</div>
			<ul id="userList"></ul>
		</div>
	</div>

	<div class="modaloptions">
		<div class="modalbg"></div>
		<div class="innermodal">
			<div class="setusername">
				<p>Set your <b>username</b> or leave blank to randomly generate one.</p>
				<input type="text" class="nameText" />
			</div>
			<!-- <div>
				<p>Current theme: <?=$user->get_theme()?></p>
			</div> -->
			<div class="enablenotifications">
				<p>Click below to enable <b>notifications</b></p>
				<div role="button" id="notificationsButton" class="button">Enable Notifications</div>
			</div>
		</div>
	</div>


	<div class="form">
		<div class="textarea">
			<textarea id="inputText"></textarea>
		</div>
		<div role="button" id="sendButton" class="button"><span class="icon send"></span></div>
	</div>
</div>

<div class="ajaxmodal"><!-- ajax loader --></div>

<?php $this->load_view('common/footer'); ?>