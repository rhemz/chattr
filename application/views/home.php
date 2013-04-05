<?php $this->load_view('common/header', array('title' => 'Welcome!')); // pass any more data you need to here w/ additional key/value pairs?>

<script type="text/javascript">

$(document).ready(function() {

	$(".mainbutton").click(function(e) {
		$('.homecontent').fadeOut(250);
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
				$('.homecontent').fadeIn(250);
				alert("Something went horribly, horribly wrong!");
			}
		});
	});

});

</script>

<div class="colorbar"></div>
<div id="content">
	<div id="topPane">
		<div class="left"><span class="logo unselectable">Chattr</span><span class="sublogo unselectable">beta</span></div>
	</div>
	<div class="centerwrapper">
		<div class="cell">
			<div class="homecontent">
				<div class="titlecopy">
					<p>Create a new chat by clicking the button below.<br />It's <b>just that easy</b>.</p>
					<p>Then <b>send the url</b> to anyone and they will be able to join you!</p>
				</div>
				<div class="mainbuttondiv">
					<button class="mainbutton">Create New Chat</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load_view('common/footer'); ?>