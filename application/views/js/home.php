<script type="text/javascript">

$(document).ready(function() {

	$(".mainbutton").click(function(e) {
		
		var roomId;
		
		var fadeOutHome = function () {
			var dfd = $.Deferred();
			$('.homecontent').fadeOut(400, dfd.resolve);
			return dfd.promise();
		}
		
		$.when(
			fadeOutHome(), $.ajax(
				{
					url: "/rest/room/create",
					cache: false,
					type: "POST"
				}
			).done(
				function(response) {
					var obj = jQuery.parseJSON(response);
					if(obj.success) {
						// display some kind of fancy feedback that indicates success and take the user to the room page
						//window.location.href = "/room/" + obj.id;
						roomId = obj.id;
					} else {
						$('.homecontent').fadeIn(250);
						alert("Something went horribly, horribly wrong!");
					}
				}
			)
		).then( function() {
			window.location.href = "/room/" + roomId;
		});
	});

});

</script>