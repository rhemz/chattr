<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<div id="page-wrap"> 
	<div id="header">
		<div id="current-user"><span>Logged in as:</span> <?=$userName?></div>
	</div>
	<div id="section">
		<h2><?=$userName?></h2>
		<div id="chat"></div>
		<div id="userlist">
			<ul>
			<?php foreach($users as $user): ?>
				<li><?=$user->name?></li>
			<?php endforeach; ?>
			</ul>
		</div>
		<form id="chat-form">
			<textarea maxlength='144'></textarea>
		</form>
	</div>
</div>


make ajax GET requests to /rest/room/room_id/messages
<br />
make ajax POST request to /rest/message/send, sending keys 'text' 'room_id'
<br />
make ajax GET request to /rest/room/room_id

<?php $this->load_view('common/footer'); ?>