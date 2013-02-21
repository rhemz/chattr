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


	public function create_user($session_id)
	{
		$sql = "INSERT INTO user (`session_id`) VALUES (?)";
		if($this->query($sql, array($session_id)))
		{
			return $this->last_insert_id();
		}
		return false;
	}
	
}