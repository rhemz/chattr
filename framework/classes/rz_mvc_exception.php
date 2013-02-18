<?php

/**
* Base Rz_MVC exception class that all MVC exceptions inherit from.  Just provides basic toString() functionality
*/
class Rz_MVC_Exception extends Exception
{
	/**
	* Call PHP standard class Exception
	*/
	public function __construct($message, $code = null)
	{
		parent::__construct($message, $code);
	}

	/**
	* Provide trace to current calling class.
	*/
	public function __toString()
	{
		return sprintf('%s: [%s]', __CLASS__, $this->message);
	}
	
}