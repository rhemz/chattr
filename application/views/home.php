<?php $this->load_view('common/header', array('title' => 'Welcome!')); // pass any more data you need to here w/ additional key/value pairs?>

	<p>
	A button or something should go here to create a chatroom.  It should make an empty ajax HTTP POST to /rest/room/create
	</p>


	<p>
		Your user_id: <?=$user->get_id()?>
	</p>




<?php $this->load_view('common/footer'); ?>