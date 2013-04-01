<?php


class Autoloader
{
	private static $instance;
	private $tree = array(FRAMEWORK_PATH => array(), APPLICATION_PATH => array(), 'CM' => array());
	private $suffixes;
	private $config;


	private function __construct()
	{
		$this->config =& Config::get_instance();
		$this->config->load('paths');

		$this->suffixes = array(
			$this->config->get('paths.controller_suffix')	=> $this->config->get('paths.controllers'),
			$this->config->get('paths.model_suffix') 		=> $this->config->get('paths.models')
		);

		$this->build_cache();
	}


	public static function &get_instance()
	{
		if(is_null(self::$instance))
		{
			self::$instance = new Autoloader();
		}
		return self::$instance;
	}


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
		foreach(new RecursiveIteratorIterator(
			$i = new RecursiveDirectoryIterator(APPLICATION_PATH)) as $item)
		{
			if(!in_array($i, $nolook) && !$item->isDir() && $item->isFile())
			{
				$this->tree[APPLICATION_PATH][] = $item->getPathname();
			}
		}
	}


	public function load_class($class)
	{
		// look for user defined models & controllers first
		$class = strtolower($class);

		if($file = $this->search_cache($class))
		{
			require_once($file);
		}
	}


	private function search_cache($class)
	{
		// look for user defined models & controllers first
		foreach($this->suffixes as $suffix => $path)
		{
			if(stripos($class, ($s = sprintf("_%s", $suffix))) !== false)
			{
				$class = str_ireplace($s, '', $class);
				foreach($this->tree[APPLICATION_PATH] as $file)
				{
					if(strpos($file, DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR) !== false
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


	public function register()
	{
		spl_autoload_register(array('Autoloader', 'load_class'));
	}


}
