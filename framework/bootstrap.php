<?php

// load base mvc exception, configuration class, and autoloader
require_once(FRAMEWORK_PATH . 'classes' . DIRECTORY_SEPARATOR . 'rz_mvc_exception' . PHP_EXT);
require_once(FRAMEWORK_PATH . 'classes' . DIRECTORY_SEPARATOR . 'config' . PHP_EXT);
require_once(FRAMEWORK_PATH . 'autoload' . PHP_EXT);

$loader =& Autoloader::get_instance();
$loader->register();

// load core framework and application configs
$config =& Config::get_instance();
$config->load(array('routes', 'environment', 'logging', 'session'));

// register custom shutdown and error handler hooks
if($config->get('global.framework_handle_fatal_errors'))
{
	register_shutdown_function('Rz_MVC::hook_shutdown');
	ini_set('display_errors', 0);
}

// check for existance of application environment config file
if(!$config->user_config_exists('environment'))
{
	Logger::log('No application environment setting present, falling back to framework default', Log_Level::Warning);
}

// set operating environment
define('ENVIRONMENT', $config->get('environment.environment'));

// prepare URI and startup Router
$uri = rtrim(preg_replace('/\?(.*)/', '', $_SERVER['REQUEST_URI']), '/');

$router = new Router($uri);

$router->check_route()
	? $router->execute_route()
	: $router->show_404();