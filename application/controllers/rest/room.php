<?php

class Room_Controller extends Controller_Rest
{
	private $room_model;

	public function __construct()
	{
		parent::__construct();

		$this->room_model = new Chatroom_Model();
	}


	public function get_create()
	{
		$helper = new Chatroom_Helper();
		$id = null;

		while(!$this->room_model->create_room($id, 1)) // get userid from session
		{
			$id = $helper->generate_id($this->config->get('chatroom.id_length'));
		}

		Logger::log(sprintf("created room: %s", $id));
		
	}
}