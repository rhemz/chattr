<?php


/**
* Useful for logical shorthand when referencing user GET/POST/etc input.  All methods static.
*/
class Input
{
	
	/**
	* Retrieve a value from HTTP GET.  If not present, fall back to the optionally supplied default value.
	* @param string $key GET key
	* @param mixed $default The fallback default value
	* @return mixed
	*/
	public static function get($key, $default = null)
	{
		return isset($_GET[$key])
			? $_GET[$key]
			: $default;
	}


	/**
	* Retrieve a value from HTTP POST.  If not present, fall back to the optionally supplied default value.
	* @param string $key POST key
	* @param mixed $default The fallback default value
	* @return mixed
	*/
	public static function post($key, $default = null)
	{
		return isset($_POST[$key])
			? $_POST[$key]
			: $default;
	}


	/**
	* Retrieve a value from HTTP cookie header.  If not present, fall back to the optionally supplied default value.
	* @param string $key cookie key
	* @param mixed $default The fallback default value
	* @return mixed
	*/
	public static function cookie($key, $default = null)
	{
		return isset($_COOKIE[$key])
			? $_COOKIE[$key]
			: $default;
	}


	/**
	* Determine whether or not a value is present in a given HTTP request or header context.
	* @param string $key The key to test for the presence of
	* @param string $method The HTTP method (get, post, cookie)
	* @return boolean
	*/
	public static function is_present($key, $method = 'post')
	{
		switch(strtolower($method))
		{
			case 'get':
				$source =& $_GET;
				break;
			case 'cookie':
				$source =& $_COOKIE;
				break;
			default:
				$source =& $_POST;
				break;
		}
		return isset($source[$key]) && !is_null($source[$key]) && strlen($source[$key]);
	}

	/**
	* Determine whether or not a form has been submitted with this request.
	* @return boolean
	*/
	public static function form_submitted()
	{
		return isset($_POST);
	}


	/**
	* Get the client's accessing IP address.  If for whatever reason it is not available, fall back to 0.0.0.0
	* @return string
	*/
	public static function ip($default = '0.0.0.0')
	{
		return isset($_SERVER['REMOTE_ADDR'])
			? $_SERVER['REMOTE_ADDR']
			: $default;
	}


	/**
	* Attempt to get the client's true IP address by checking for a variety of headers.
	* @return string|false
	*/
	public static function true_ip()
	{
		$fields = array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR');
		foreach($fields as $field)
		{
			if(isset($_SERVER[$field]))
			{
				return $_SERVER[$field];
			}
		}
		return false;
	}


	/**
	* Retrieve the browser-supplied HTTP Referrer header, or fall back to the supplied default value.
	* @param string $default The fallback default value if no header is present
	* @return string
	*/
	public static function referer($default = '')
	{
		return isset($_SERVER['HTTP_REFERER']) && !is_null($_SERVER['HTTP_REFERER'])
			? $_SERVER['HTTP_REFERER']
			: $default;
	}


	/**
	* Determine whether the current application request is an AJAX request or not.
	* @return boolean
	*/
	public static function is_ajax()
	{
		return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] == 'xmlhttprequest');
	}


	/**
	* Determine whether the incoming request is executed by the command line
	* @return boolean
	*/
	public static function is_CLI()
	{
		return php_sapi_name() == 'cli';
	}


	/**
	* Retrieve the current HTTP request method.
	* @return string
	*/
	public static function request_method()
	{
		return $_SERVER['REQUEST_METHOD'];
	}


	/**
	* Retrieve the client-supplied User-Agent string from headers.
	* @return string
	*/
	public static function user_agent()
	{
		return $_SERVER['HTTP_USER_AGENT'];
	}

}