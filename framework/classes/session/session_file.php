<?php

class Session_File extends Session_Base
{
	private $file_path;

	public function __construct()
	{
		$this->config = Config::get_instance()->get('session.*');

		ini_set('session.gc_maxlifetime', $this->config['timeout']);
		ini_set('session.gc_probability', 1);
		ini_set('session.gc_divisor', 10);


		$this->start();

		// $this->_set_session_handler();  don't need this for file-based sessions
	}

	public function _open()
	{
		return true;
	}

	public function _close()
	{
		return true;
	}

	public function _read($id)
	{
		return true;
	}

	public function _write($id, $data)
	{
		return true;
	}

	public function _destroy($id)
	{
		return true;
	}

	public function _gc($age)
	{
		return true;
	}
	
}