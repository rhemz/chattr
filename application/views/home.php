<?php $this->load_view('common/header', array('title' => 'Welcome!')); // pass any more data you need to here w/ additional key/value pairs?>
<?php $this->load_view('js/home', array('user' => $user)); ?>

<div class="colorbar"></div>
<div id="content">
	<div id="topPane">
		<div class="left"><span class="logo no-select">Chattr</span><span class="sublogo no-select">beta</span></div>
	</div>
	<div class="centerwrapper">
		<div class="cell">
			<div class="homecontent">
				<div class="titlecopy">
					<p>Create a new chat by clicking the button below.<br />It's <b>just that easy</b>.</p>
					<p>Then <b>send the url</b> to anyone and they will be able to join you!</p>
				</div>
				<div class="mainbuttondiv">
					<button class="mainbutton no-select">Create New Chat</button>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $this->load_view('common/footer'); ?>