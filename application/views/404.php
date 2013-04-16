<?php $this->load_view('common/header', array('title' => $title)); ?>
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
					<p><?=$message?></p>
				</div>
				<div class="mainbuttondiv">
					<button class="mainbutton no-select">Create New Chat</button>
				</div>
			</div>
		</div>
	</div>
</div>
<?php $this->load_view('common/footer'); ?>