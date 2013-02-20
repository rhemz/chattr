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


}