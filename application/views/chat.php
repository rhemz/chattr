<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Welcome to: <?=$progName?></title>
	<link rel="stylesheet" type="text/css" href="../main.css"/>    
	<script src="/public/scripts/jquery.min.js" type="text/javascript"></script>
	<script src="/public/scripts/chat.js" type="text/javascript" ></script>
	<script type="text/javascript" src="settings.js"></script>
</head>
<body>
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
</body>
</html>