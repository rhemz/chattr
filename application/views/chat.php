<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>
<?php $this->load_view('js/chat', array('user' => $user, 'room_id' => $room_id)); ?>

<div class="colorbar"></div>
<div id="content">
	<div id="topPane">
		<div class="left"><a href="/"><span class="logo no-select">Chattr</span></a><span class="sublogo no-select">beta</span></div>
		<div class="right">
			<div class="optionsarrow"><a href="#"></a></div>
		</div>
	</div>

	<div id="middle">
		<div id="userDiv">
			<div class="heading no-select"><span class="icon users"></span><span class="user-heading-text">Users</span></div>
			<ul id="userList"></ul>
		</div>
		<div id="mainChat"></div>
	</div>

	<div class="initial-options modal">
		<div class="modalbg"></div>
		<div class="innermodal">
			<div class="close"></div>
			<div class="setusername">
				<p>Set your <b>username</b> or leave blank to randomly generate one.</p>
				<input type="text" class="nameText" />
			</div>
			<!-- <div>
				<p>Current theme: <?=$user->get_theme()?></p>
			</div> -->
			<div class="enablenotifications">
				<p>Click below to <b>enable</b> notifications</p>
				<div role="button" id="notificationsButton" class="button">Enable Notifications</div>
			</div>
		</div>
	</div>

	<div class="options-menu">

		<div class="notch"></div>

		<div class="title">
			<span>Options</span>
		</div>

		<div class="options-list">
		
			<div class="change-name">
				<input type="text" class="nameText" placeholder="User name" />
			</div>

			<div class="notifications">
				<span>HTML5 Notifications</span>
				<input type="checkbox" checked name="notifications" value="Enabled">
			</div>
			
			<div class="embed-images">
				<span>Image embedding</span>
				<input type="checkbox" checked name="images" value="Enabled">
			</div>
			
			<div class="embed-video">
				<span>Video embedding</span>
				<input type="checkbox" checked name="video" value="Enabled">
			</div>

			<div class="sounds">
				<span>Enable Sound</span>
				<input type="checkbox" checked name="sounds" value="Enabled">
			</div>
			
			<div class="enable-animations">
				<span>Animations</span>
				<input type="checkbox" checked name="animation" value="Enabled">
			</div>
			
			<div class="raw-messages">
				<span>Raw HTML messages</span>
				<input type="checkbox" name="rawmessages" value="Enabled">
			</div>
			
			<div class="change-theme">
				<span>Theme</span>
				<select>
					<option value="default">Default</option>
				</select>
			</div>

		</div>

		<div role="button" id="optionsSubmit" class="button options-submit">Finish</div>

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