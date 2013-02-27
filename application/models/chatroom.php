<?php


class Chatroom_Model extends Model
{
	public function __construct()
	{
		parent::__construct();
	}


	public function room_exists($room_id)
	{
		$sql = "SELECT id FROM room WHERE id = ?";
		if($this->query($sql, array($room_id)))
		{
			$result = $this->result();
			return ($result->num_rows() > 0);
		}
		return true; // if query fails, say room exists so as to not try to create duplicate
	}


	public function create_room($id, $user_id, $name = null)
	{
		if(is_null($id) || $this->room_exists($id))
		{
			return false;
		}

		$sql = "INSERT INTO room (`id`, `creator`, `name`) VALUES (?, ?, ?)";
		return $this->query($sql, array($id, $user_id, $name));
	}


	public function get_users($room_id, $cutoff)
	{
		$cutoff = microtime(true) - $cutoff;

		$sql = "SELECT 
					user.id,
					user.name
				FROM
					user
				INNER JOIN
					message_retrieve ON user.id = message_retrieve.user_id
				WHERE
					message_retrieve.room_id = ?
				AND
					message_retrieve.last_checked > ?";

		if($this->query($sql, array($room_id, $cutoff)))
		{
			$result = $this->result();
			return ($result->num_rows() > 0) ? $result : null;
		}
		return false;
	}


	public function leave_room($room_id, $user_id)
	{
		return $this->query("DELETE FROM message_retrieve WHERE user_id = ? AND room_id = ?", array($user_id, $room_id));
	}


}