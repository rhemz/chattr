<?php

// load base mvc exception, configuration class, environments enum, and autoloader
require_once(FRAMEWORK_PATH . 'classes' . DIRECTORY_SEPARATOR . 'rz_mvc_exception' . PHP_EXT);
require_once(FRAMEWORK_PATH . 'classes' . DIRECTORY_SEPARATOR . 'config' . PHP_EXT);
require_once(FRAMEWORK_PATH . 'classes' . DIRECTORY_SEPARATOR . 'enum' . PHP_EXT);
require_once(FRAMEWORK_PATH . 'classes' . DIRECTORY_SEPARATOR . 'enum' . DIRECTORY_SEPARATOR . 'environment' . PHP_EXT);
require_once(FRAMEWORK_PATH . 'autoload' . PHP_EXT);

// determine operating environment.  ideally determined in the user's .htaccess but can fall back to config files
$e = isset($_SERVER['ENVIRONMENT']) && !is_null($e = @constant('Environment::' . $_SERVER['ENVIRONMENT']))
	? $e
	: Config::get_instance()->load('environment')->get('environment.environment');
define('ENVIRONMENT', $e);

// register class autoloader
Autoload::get_instance()->register();

// load core framework and application configs
$config =& Config::get_instance();
$config->load(array('global', 'routes', 'logging', 'session'));

// register custom shutdown and error handler hooks
if($config->get('global.framework_handle_fatal_errors'))
{
	register_shutdown_function('Rz_MVC::hook_shutdown');
	ini_set('display_errors', 0);
}

// prepare URI and startup Router
$uri = rtrim(preg_replace('/\?(.*)/', '', $_SERVER['REQUEST_URI']), '/');

$router = new Router($uri);

// find the route or die trying!
$router->check_route()
	? $router->execute_route()
	: $router->show_404();