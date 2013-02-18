<?php

/*
	Ideas:
	http://www.php.net/manual/en/function.session-set-save-handler.php
	https://github.com/fuel/core/blob/1.5/develop/classes/session/driver.php
	http://www.php.net/session_regenerate_id
	http://stackoverflow.com/questions/11596082/php-session-class-similar-to-codeigniter-session-class
*/

abstract class Session_Base
{
	protected $config = array();
	protected $timestamp = null;


	/**
	* Following abstract functions are implemented by session type drivers
	*/
	abstract public function _open();

	abstract public function _close();

	abstract public function _read($id);

	abstract public function _write($id, $data);

	abstract public function _destroy($id);

	abstract public function _gc($age);


	public function set($key, $data)
	{
		$this->_check_enabled();
		$_SESSION[$key] = $data;
		return true;
	}


	public function get($key, $default = null)
	{
		$this->_check_enabled();
		return isset($_SESSION[$key])
			? $_SESSION[$key]
			: $default;
	}


	public function delete($key)
	{
		$this->_check_enabled();
		unset($_SESSION[$key]);
	}


	protected function start()
	{
		if($this->config['use_session'])
		{
			// sanity check, just in case user tries to manually start up extra controllers
			$active = function_exists('session_status')
				? (session_status() == PHP_SESSION_ACTIVE)
				: (strlen(session_id()) ? true : false);
			
			if(!$active)
			{
				session_name(Config::get_instance()->get('session.name'));

				@session_start();
			}
		}
		
	}


	protected function _set_session_handler()
	{
		session_set_save_handler(
			array(&$this, '_open'), 
			array(&$this, '_close'),
			array(&$this, '_read'),
			array(&$this, '_write'),
			array(&$this, '_destroy'),
			array(&$this, '_gc')
		);
	}


	protected function _generate_key()
	{
		$key = Config::get_instance()->get('session.hash');

		return sha1(microtime(true) . $key);
	}


	private function _check_enabled()
	{
		if(!$this->config['use_session'])
		{
			Logger::log('You cannot use the session object with sessions disabled.', Log_Level::Error);
		}
	}

}