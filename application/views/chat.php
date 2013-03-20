<?php $this->load_view('common/header', array('title' => 'Chatting.  Room name will eventually go here')); ?>

<script type="text/javascript">


$(document).ready(function() {

	$("#inputText").live("keypress", function(e) {
		if(e.which == 13) {
			$.ajax({
				url: "/rest/room/<?=$room_id?>/send",
				cache: false,
				type: "POST",
				data: {room_id: "<?=$room_id?>", text: $("#inputText").val()}
			}).done(function(response) {
				alert(response);
				var obj = jQuery.parseJSON(response);
				if(obj.success) {
 
				} else {
					alert("Something went horribly, horribly wrong!");
				}
			});
		}
	});

});

</script>



<input type="text" size="80" id="inputText" />

<br />


make ajax GET requests to /rest/room/room_id/messages
<br />
make ajax POST request to /rest/message/send, sending keys 'text' 'room_id'
<br />
make ajax GET request to /rest/room/room_id

<?php $this->load_view('common/footer'); ?>