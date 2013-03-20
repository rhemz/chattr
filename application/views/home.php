<?php $this->load_view('common/header', array('title' => 'Welcome!')); // pass any more data you need to here w/ additional key/value pairs?>

<script type="text/javascript">

$(document).ready(function() {

	$("#createRoom").click(function(e) {
		$.ajax({
			url: "/rest/room/create",
			cache: false,
			type: "POST"	
		}).done(function(response) {
			var obj = jQuery.parseJSON(response);
			if(obj.success) {
				// display some kind of fancy feedback that indicates success and take the user to the room page
				window.location.href = "/room/" + obj.id; 
			} else {
				alert("Something went horribly, horribly wrong!");
			}
		});
	});

});

</script>

<input type="button" id="createRoom" value="Create a Chatroom" />


<p>
	Your user_id: <?=$user->get_id()?>
</p>




<?php $this->load_view('common/footer'); ?>