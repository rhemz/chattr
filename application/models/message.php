<?php

class Message_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function save_message($user_id, $room_id, $text, $timestamp)
	{
		$sql = "INSERT INTO message (`user_id`, `room`, `text`, `timestamp`) VALUES (?, ?, ?, ?)";
		if($this->query($sql, array($user_id, $room_id, $text, $timestamp)))
		{
			return $this->last_insert_id();
		}
		return false;
	}
}