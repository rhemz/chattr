<?php

// class suffixes for models and controllers.  e.g. with the default settings, a User controller 
// class would be called User_Controller, and reside at application/controllers/user.php
$config['controller_suffix'] 	= 'controller';
$config['model_suffix'] 		= 'model';

// change these if you want to change the default directories rzmvc looks for things in
$config['controllers'] 			= 'controllers';
$config['models']				= 'models';
$config['views']				= 'views';

$config['nolook']				= array('config', 'views'); // directories for the autoloader to skip

$config['framework_classes']	= 'classes';