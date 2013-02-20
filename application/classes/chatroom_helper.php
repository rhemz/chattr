<?php


class Chatroom_Helper
{

	public function generate_id($length)
	{
		$mvc =& get_mvc();
		$chars = $mvc->config->get('chatroom.id_chars');

		$id = '';
		while($length--)
		{
			$id .= $chars[mt_rand(0, (strlen($chars)-1))];
		}

		return $id;
	}
}