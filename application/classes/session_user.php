<?php


class Session_User 
{
	const Session_Key = 'user_id';

	private $mvc;
	private $user_model;

	private $session_id;

	public function __construct()
	{
		$this->mvc =& get_mvc();
		$this->session_id = session_id();

		if(is_null($this->mvc->session->get(self::Session_Key)))
		{
			$this->user_model = new User_Model();

			if(!is_numeric($id = $this->user_model->get_id($this->session_id)))
			{
				$id = $this->user_model->create_user($this->session_id);
			}

			$this->mvc->session->set(self::Session_Key, $id);
		}
		
	}


	public function get_id()
	{
		return $this->mvc->session->get(self::Session_Key);
	}
}