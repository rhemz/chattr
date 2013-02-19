<?php

/**
* The Router class is responsible for mapping an incoming URI to a corresponding action belonging
* to a Controller.  The first step is to see if the incoming URI matches any specific user-programmed
* 'vanity' patterns (e.g. mapping '/user/123' to '/account/user/view/123').  If no matches are found,
* the Router will begin traversing the application/controllers/ directory, trying to match the earliest
* possible incoming URI segment with a corresponding Controller.  If a matching Controller is found, the
* Router tests for the presence of the next incoming URI segment in the form of a member function belonging 
* to said controller.  If it exists, it is invoked, passing any additional URI segments as optional parameters.
*
* Vanity routes are defined in application/config/routes.php file and must follow this format:
*	'/something/%var'      => '/somethingelse/$1'
*	'/foo/%var/bar/%var'   => '/bar/$1/foo/$2'
*	/woot/%var/%var/%var   => '/foo/$3/$2/$1'
* where the $1 - $n are replaced by the corresponding wildcards in the order they appear.  The $n values can
* appear in any order.
*/
class Router
{
	private $incoming;
	private $path;
	private $index = 0;
	private $config;
	private $wildcard;

	private $controller_base;
	private $controller_name;
	private $controller_obj;
	private $action;


	/**
	* Create a new instance of Router from an incoming URI.
	* @param string $uri The incoming URI (usually requested by a browser)
	*/
	public function __construct($uri)
	{
		$this->incoming = $uri;

		$this->config =& Config::get_instance();
		$this->config->load(array('paths', 'routes'));
		
		$this->wildcard = $this->config->get('routes.wildcard');
		$this->controller_base = APPLICATION_PATH . $this->config->get('paths.controllers') . DIRECTORY_SEPARATOR;
	}


	/**
	* Check to see if there is any matching vanity route or Controller->action mapping that corresponds to the 
	* incoming URI this instance of Router was initialized with.
	* @return boolean
	*/
	public function check_route()
	{
		$this->eval_custom_routes($this->config->get('routes.mappings'));

		// map URI to controller
		$this->path = explode('/', trim(substr($this->incoming, 1), '/'));

		foreach($this->path as $segment)
		{
			if(!strlen($segment))
			{
				$segment = $this->config->get('routes.default_controller');
			}
			else if(is_link($this->controller_base . $segment) || is_dir($this->controller_base . $segment))
			{
				$this->controller_base .= $segment . DIRECTORY_SEPARATOR;
				$this->index++;
			}

			if(is_file($this->controller_base . $segment . PHP_EXT))
			{
				$this->controller_name = sprintf("%s_%s", $segment, $this->config->get('paths.controller_suffix'));
				$this->controller = new $this->controller_name();

				$this->action = ++$this->index == sizeof($this->path)
					? $this->config->get('routes.default_function') 
					: $this->path[$this->index];

				return (isset($this->controller) && is_object($this->controller) && $this->controller->_has_method($this->action));
			}
		}

		return false;
	}


	/**
	* Called by the rz_mvc bootstrap if a matching route is found.  Calls the appropriate Controller action & passes
	* along any parameters existing in the incoming URI
	*/
	public function execute_route()
	{
		sizeof($args = array_slice($this->path, ++$this->index))
			? call_user_func_array(array($this->controller, $this->action), $args)
			: $this->controller->{$this->action}();
	}


	/**
	* Test for any matches between the incoming URI and the application-defined vanity routes.
	* @param Array $routes The application routes configuration
	*/
	private function eval_custom_routes($routes)
	{
		$i_parts = explode('/', trim(substr($this->incoming, 1), '/'));
		
		foreach($routes as $inc => $dest)
		{
			$r_parts = explode('/', trim(substr($inc, 1), '/'));

			$matched = true;
			for($i=0; $i<sizeof($r_parts); $i++)
			{
				if((@($r_parts[$i] != $i_parts[$i])) && $r_parts[$i] != $this->wildcard)
				{
					$matched = false;
					break;
				}
			}

			if($matched)
			{
				$marker = 0;
				for($i=0; $i<sizeof($r_parts); $i++)
				{
					if($r_parts[$i] == $this->wildcard)
					{
						$dest = @str_replace(sprintf('$%s', ++$marker), $i_parts[$i], $dest);
					}
				}
				$this->incoming = $dest;
				return;
			}
		}
	}


	/**
	* Send the appropriate HTTP 404 Not Found headers and show the default 404 Page.
	* Static function, so it can be called from user Controllers if desired.
	*/
	public static function show_404()
	{
		header(HTTP_Status_Code::Not_Found);

		// make generic controller and show 404 page
		$controller = new Controller();
		$controller->load_view(Config::get_instance()->get('routes.404_view'));
	}

}