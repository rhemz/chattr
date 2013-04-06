<?php

class Message_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function save_message($user_id, $room_id, $text)
	{
		$timestamp = microtime(true);
		$sql = "INSERT INTO message (`user_id`, `room_id`, `text`, `timestamp`) VALUES (?, ?, ?, ?)";
		if($this->query($sql, array($user_id, $room_id, $text, $timestamp)))
		{
			return $this->last_insert_id();
		}
		return false;
	}


	public function get_messages($room_id, $user_id)
	{
		$sql = "SELECT
					message.id as message_id,
					message.user_id as user_id,
					user.name as user_name,
					message.text as text,
					message.timestamp as timestamp
				FROM
					message
				INNER JOIN
					user ON message.user_id = user.id
				INNER JOIN
					message_retrieve ON message.user_id = message_retrieve.user_id
				WHERE
					message.room_id = ?
				AND
					message.timestamp >= (SELECT last_checked FROM message_retrieve WHERE user_id = ? AND room_id = ?)
				GROUP BY 
					message.id";

		if($this->query($sql, array($room_id, $user_id, $room_id)))
		{
			$result = $this->result();

			$timestamp = microtime(true);

			$sql = "INSERT INTO 
						message_retrieve (`user_id`, `room_id`, `last_checked`) VALUES (?, ?, ?)
					ON DUPLICATE KEY 
						UPDATE `last_checked` = ?";

			if($this->query($sql, array($user_id, $room_id, $timestamp, $timestamp)))
			{
				return new Message_Set($result);
				// return $result;
			}
		}
		return false;
	}
}