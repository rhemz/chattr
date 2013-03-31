<?php


class Session_User 
{
	const Config_Group = 'user';

	const Session_Key = 'user_id';
	const User_Name_Key = 'user_name';
	const Theme_Key = 'theme';

	private $mvc;
	private $user_model;
	private $theme;

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
				$name = $this->mvc->config->get(sprintf("%s.default_username", self::Config_Group)) 
					. rand(1, $this->mvc->config->get(sprintf("%s.default_username_max", self::Config_Group)));
				$id = $this->user_model->create_user($this->session_id, $name);
			}

			$this->mvc->session->set(self::Session_Key, $id);
			$this->mvc->session->set(self::User_Name_Key, $name);
			$this->mvc->session->set(self::Theme_Key, $this->mvc->config->get('global.default_theme'));
		}
		
	}


	public function set_name($name)
	{
		$this->mvc->session->set(self::User_Name_Key, $name);

		$this->user_model = new User_Model();
		return $this->user_model->set_name($this->get_id(), $name);
	}


	public function get_name()
	{
		return $this->mvc->session->get(self::User_Name_Key, $this->mvc->config->get(sprintf("%s.default_username_max", self::Config_Group)));
	}


	public function get_id()
	{
		return $this->mvc->session->get(self::Session_Key);
	}


	public function get_theme()
	{
		return $this->mvc->session->get(self::Theme_Key);
	}


	public function set_theme($name)
	{
		return $this->mvc->session->set(self::Theme_Key, $name);
	}
}