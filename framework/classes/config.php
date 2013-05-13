<?php


/**
* Class responsible for loading configuration files and making values contained therein accessible
* via queries.
*/
class Config
{
	
	const Config_Dir = 'config';
	const Config_Delimiter = '.';
	const Config_Wildcard = '*';

	private $config = array();
	private $framework_config;
	private $application_config;

	private static $instance;



	/**
	* Create instance of Config.  Never called directly, only by static method get_instance()
	*/
	private final function __construct()
	{
		$this->framework_config = FRAMEWORK_PATH . self::Config_Dir . DIRECTORY_SEPARATOR . '%s' . PHP_EXT;
		$this->application_config = APPLICATION_PATH . self::Config_Dir . DIRECTORY_SEPARATOR . '%s' . PHP_EXT;
	}


	/**
	* Handle singletone instance of config.  new Config() is never called.
	*/
	public static function &get_instance()
	{
		if(is_null(self::$instance))
		{
			self::$instance = new Config();
		}
		return self::$instance;
	}


	/**
	* Load any number of configuration files.  If an application configuration file is not present, the corresponding
	* framework config will be loaded.  If that does not exist, an error is thrown.
	* @param string|Array $files Config file(s)
	* @param Environment $env Optionally override the default environment-config selection process
	* @throws Config_Not_Found_Exception
	* @throws Config_Malformed_Exception
	*/
	public function load($files, $env = null)
	{
		/*
			1. Determine paths to file
				-if $env = Environment::None, set path to to /config/file
				-if /config/$env||ENVIRONMENT/file exists, set path to that
				-otherwise set path to /config/file 
			2. If application config exists
				-attempt to load corresponding framework config
				-any values present in framework config should be overridden with application values
				-any values present in application config not existing in framework config should persist
			3. If application config does not exist, load framework config
			4. If framework config does not exist, throw error
		*/

		if(!is_array($files))
		{
			$files = array("{$files}");
		}

		foreach($files as $file)
		{
			if(isset($this->config[$file])) continue;

			$ac = (!is_null($env) && $env == Environment::None) ? sprintf($this->application_config, $file) : (defined('ENVIRONMENT')
				&& file_exists($p = sprintf($this->application_config, strtolower(Environment::to_string((!is_null($env) ? $env : ENVIRONMENT))) . DIRECTORY_SEPARATOR . $file))
				? $p
				: sprintf($this->application_config, $file));
				
			$fc = sprintf($this->framework_config, $file);
			
			try
			{
				if(file_exists($ac))
				{
					$ac = $this->get_contents($ac);

					if(file_exists($fc))
					{
						$fc = $this->get_contents($fc);
						$this->config[$file] = array_merge($fc, $ac);
					}
					else
					{
						$this->config[$file] = $ac;
					}
				}
				else
				{
					$this->config[$file] = $this->get_contents($fc);
				}
			}
			catch(Config_Not_Found_Exception $cnfe)
			{
				Logger::log($cnfe->getMessage(), Log_Level::Error);
			}
			catch(Config_Malformed_Exception $cme)
			{
				Logger::log($cme->getMessage(), Log_Level::Error);
			}
		}

		return self::$instance;
	}


	/**
	* Get a configuration value by means of a config query.
	* Format is as follows:
	* 		file.key - returns a specific value existing in the file (e.g. database.hostname)
	*		file.* - returns the full configuration array defined in the given file (e.g. routes.*)
	* @param string $key Configuration query path
	* @return mixed The configuration query result
	*/
	public function get($key)
	{
		$parts = explode(self::Config_Delimiter, $key);
		if(sizeof($parts) != 2)
		{
			// was throwing an exception, but it would be super obnoxious to wrap try around every config get.
			Logger::log(sprintf('The supplied selector (%s) is invalid.  Expected format: "section%skey"', $key, self::Config_Delimiter));
			return null;
		}

		// if smart config loading is enabled in application config, config files don't have to be loaded before being queried
		if(isset($this->config['global']['smart_config_loading']) && $this->config['global']['smart_config_loading'] && !isset($this->config[$parts[0]]))
		{
			$this->load($parts[0]);
		}

		/*
		if(!isset($this->config[$parts[0]]))
		{
			Logger::log(sprintf("The '%s' configuration file has not been loaded or cannot be found.  Unable to find '%s'", 
				$parts[0], $key), Log_Level::Warning);
		}
		*/

		if(isset($this->config[$parts[0]]) && $parts[1] == self::Config_Wildcard)
		{
			return $this->config[$parts[0]];
		}
		else if(isset($this->config[$parts[0]][$parts[1]]))
		{
			return $this->config[$parts[0]][$parts[1]];
		}
		return null;

	}


	/**
	* Return the contents of a given configuration file path.  Used internally by the Config class
	* @param string $path Path to the config file
	* @throws Config_Not_Found_Exception Thrown if the specified configuration file does not exist
	* @throws Config_Malformed_Exception Thrown if the configuration file contains an invalid config array
	* @return array
	*/
	private function get_contents($path)
	{
		if(!file_exists($path))
		{
			throw new Config_Not_Found_Exception($path);
		}

		require_once($path);

		if(!isset($config) || !is_array($config))
		{
			throw new Config_Malformed_Exception($path);
		}

		return $config;
	}


	/**
	* Test whether a user configuration file exists
	* @param string $file The configuration filename
	* @return bool
	*/
	public function user_config_exists($file)
	{
		return file_exists(sprintf($this->application_config, $file));
	}

}






/*
	Exceptions
*/

class Config_Not_Found_Exception extends Rz_MVC_Exception
{
	public function __construct($config_file)
	{
		$msg = sprintf('The following configuration file was not found: %s', $config_file);
		parent::__construct($msg);
	}
}


class Framework_Config_Not_Found_Exception extends Rz_MVC_Exception
{
	public function __construct($config_file)
	{
		$msg = sprintf('The following framework configuration file was not found: %s', $config_file);
		parent::__construct($msg);
	}
}


class Config_Malformed_Exception extends Rz_MVC_Exception
{
	public function __construct($config_file)
	{
		$msg = sprintf('The following framework configuration file is malformed: %s', $config_file);
		parent::__construct($msg);
	}
}


class Config_Selector_Malformed_Exception extends Rz_MVC_Exception
{
	public function __construct($key)
	{
		$msg = sprintf('The supplied config selector (%s) is invalid.  Expected format: "section%skey"', $key, Config::Config_Delimiter);
		parent::__construct($msg);
	}
}