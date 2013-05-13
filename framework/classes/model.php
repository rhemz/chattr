<?php

/**
* The class all user defined Models inherit from.  Responsible for instantiating the appropriate
* database driver from the application configuration and passing method calls onto said driver.
*/
class Model
{
	const Driver_Prefix = 'Database';

	protected $db_object;
	private $db_reflection;

	/**
	* Create an instance of the Model class.  Never called directly.  Load the database configuration and create
	* a new/use existing connection to said database.
	*/
	public function __construct()
	{
		$config =& Config::get_instance();
		$config->load('database');
		$type = $config->get('database.type');

		if(is_null($type))
		{
			Logger::log('No database configuration is present', Log_Level::Error);
		}

		$type = sprintf('%s_%s', self::Driver_Prefix, $type);
		// now a singleton to avoid multiple connections
		$this->db_object =& $type::get_instance($type, $config->get('database.*')); //new $type($config->get('database.*'));

		$this->db_reflection = new ReflectionClass($this->db_object);	
	}


	/**
	* Overridden PHP magic method __call(), whenever $this->methodname() is called by a child class, this attempts
	* to pass said call onto the database driver file.
	*/
	public function __call($method, $args)
	{
		if($this->db_reflection->hasMethod($method))
		{
			return call_user_func_array(array($this->db_object, $method), $args);
		}
		else
		{
			Logger::log(sprintf('Model does not contain method: %s()', $method), Log_Level::Error);
		}
	}
}