<?php

class Message_Validation
{

	public static function valid_room($value, &$message)
	{
		$model = new Chatroom_Model();
		
		return $model->room_exists($value);
	}

}