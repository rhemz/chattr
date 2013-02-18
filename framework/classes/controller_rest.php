<?php


/**
* Controller specifically for implementing RESTful services.
* REST controller inherits from Controller and is responsible for providing information for URI routing.
*/
class Controller_Rest extends Rz_Mvc
{
	private $method_prefix;

	public function __construct()
	{
		parent::__construct();

		$this->method_prefix = sprintf("%s_", Input::request_method());
	}

	
	/**
	* Used by the router to determine if a given controller maps to a URI
	* @param string $method Method to test for
	*/
	public function _has_method($method)
	{
		$rc = new ReflectionClass($this);

		// find function by http method
		return $rc->hasMethod(sprintf("%s%s", $this->method_prefix, $method));
	}


	/**
	* Overridden PHP magic method __call() to match incoming URI and HTTP request method to the
	* corresponding REST method
	* @param string $method Method to invoke
	* @param array|null $args The optional method parameters to pass
	*/
	public function __call($method, $args)
	{
		$method = sprintf("%s%s", $this->method_prefix, $method);

		sizeof($args)
			? call_user_func_array(array($this, $method), $args)
			: $this->{$method}();
	}
}