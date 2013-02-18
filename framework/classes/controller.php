<?php


/**
* All user controllers extend Controller.
* Controller inherits from Rz_MVC and is responsible for providing information for URI routing.
*/
class Controller extends Rz_MVC
{
	public function __construct()
	{
		parent::__construct();
	}


	/**
	* Used by the router to determine if a given controller maps to a URI
	* @param string $method Method to test for
	*/
	public function _has_method($method)
	{
		$rc = new ReflectionClass($this);
		return $rc->hasMethod($method);
	}
}