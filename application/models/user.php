<?php

class User_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function get_id($session_id)
	{
		$sql = "SELECT id FROM user WHERE session_id = ?";
		if($this->query($sql, array($session_id)))
		{
			$result = $this->result();
			return $result->num_rows() > 0 ? $result->rows[0]->id : null;
		}
		return false;
	}


	public function create_user($session_id, $username)
	{
		$sql = "INSERT INTO user (`session_id`, `name`) VALUES (?, ?)";
		if($this->query($sql, array($session_id, $username)))
		{
			return $this->last_insert_id();
		}
		return false;
	}


	public function set_name($user_id, $name)
	{
		$sql = "UPDATE user SET `name` = ? WHERE id = ?";
		return $this->query($sql, array($name, $user_id));
	}
	
}