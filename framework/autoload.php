<?php


/**
* The Autoloader.  Responsible for locating and including application & framework classes.  User-defined
* Models and Controllers are located by naming convention & directories in the paths configuration.
*/
class Autoload
{
	const CM_Branch = 'CM';
	private static $instance;
	private $tree = array(FRAMEWORK_PATH => array(), APPLICATION_PATH => array(), self::CM_Branch => array());
	private $suffixes;
	private $config;


	/**
	* Instantiate autoloader
	*/
	private function __construct()
	{
		$this->config =& Config::get_instance();
		$this->config->load('paths');

		$this->suffixes = array(
			$this->config->get('paths.controller_suffix')	=> $this->config->get('paths.controllers') . DIRECTORY_SEPARATOR,
			$this->config->get('paths.model_suffix') 		=> $this->config->get('paths.models') . DIRECTORY_SEPARATOR
		);

		$this->build_cache();
	}


	/**
	* Get the autoloader singleton
	*/
	public static function &get_instance()
	{
		if(is_null(self::$instance))
		{
			self::$instance = new Autoload();
		}
		return self::$instance;
	}


	/**
	* Build arrays of the filesystem paths for the user application & framework directories.  This cache is searched whenever undefined 
	* classes are referenced, rather than repeatedly scanning them.  Called by the constructor.
	*/
	private function build_cache()
	{
		// build framework cache
		foreach(new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator(FRAMEWORK_PATH . $this->config->get('paths.framework_classes'))) as $item)
		{
			if(!$item->isDir() && $item->isFile())
			{
				$this->tree[FRAMEWORK_PATH][] = $item->getPathname();
			}
		}


		// build application cache
		$nolook = $this->config->get('paths.nolook');
		$v = array_values($this->suffixes);
		foreach(new RecursiveIteratorIterator(
			$i = new RecursiveDirectoryIterator(APPLICATION_PATH)) as $item)
		{
			if(!in_array($i, $nolook) && !$item->isDir() && $item->isFile())
			{
				$branch = !count(array_filter(array_map('strpos', array_fill(0, count($v), $item->getPathname()), $v), 'is_int')) == count($v)
					? APPLICATION_PATH
					: self::CM_Branch;
				$this->tree[$branch][] = $item->getPathname();
			}
		}
	}


	/**
	* Registered with spl_autoload, called whenever a new, currently undefined class is referenced.
	* @param string $class The class name
	*/
	public function load_class($class)
	{
		// look for user defined models & controllers first
		$class = strtolower($class);
		if($file = $this->search_cache($class))
		{
			require_once($file);
		}
	}


	/**
	* Actually look for the class in the directory caches.  First searches for user-defined Models and Controllers, then
	* moves on to the greater application directory & framework directory.
	* @param string $class The class name
	* @return string|false $file The absolute filesystem path
	*/
	private function search_cache($class)
	{
		// look for user defined models & controllers first
		foreach($this->suffixes as $suffix => $path)
		{
			if(stripos($class, ($s = sprintf('_%s', $suffix))) !== false)
			{
				$class = str_ireplace($s, '', $class);
				foreach($this->tree[self::CM_Branch] as $file)
				{
					if(strpos($file, DIRECTORY_SEPARATOR . $path) !== false
						&& $class == basename($file, PHP_EXT))
					{
						return $file;
					}
				}
			}
		}

		// look in application & framework directories
		foreach(array($this->tree[APPLICATION_PATH], $this->tree[FRAMEWORK_PATH]) as $tree)
		{
			if($result = $this->check_tree($class, $tree))
			{
				return $result;
			}
		}

		return false;
	}


	/**
	* Parse the filename from the absolute path and test it against the classname.
	* @param string $class The classname
	* @param array $tree The filesystem tree
	* @return string|false
	*/
	private function check_tree($class, $tree)
	{
		foreach($tree as $file)
		{
			if($class == basename($file, PHP_EXT))
			{
				return $file;
			}
		}

		return false;
	}


	/**
	* Register the autoloader class with PHP.  Called by the bootstrap.
	*/
	public function register()
	{
		spl_autoload_register(array('Autoload', 'load_class'));
	}


}
