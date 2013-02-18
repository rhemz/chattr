<html>
<head>
	<title>A PHP error has occurred</title>

	<style>
		body {
			font-family: Arial, Helvetica, sans-serif; font-size: 12px;
		}

		.errorBox {
			border: 2px dashed #545454;
			background-color: #D1D1D1;
			width: 860px;
			margin-left: 40px;
			margin-top: 50px;
			padding: 8px;
		}
	</style>
</head>

<body>

	<div class="errorBox">

		<p>
			Whoops, something broke in <b><?=$error['file']?></b> on line <b><?=$error['line']?></b>.  
		</p>

		<?php
			switch ($error['type'])
			{
				case E_ERROR:
					$type = 'Fatal error';
					break;

				case E_PARSE:
					$type = 'Parse error';
					break;

				case E_COMPILE_ERROR:
					$type = 'Compile error';
					break;
				
				default:
					# code...
					break;
			}
		?>

		<ul>
			<li><b>Type:</b> <?=$type?></li>
			<li><b>Message:</b> <?=$error['message']?></li>
		</ul>

	</div>

</body>
</html>