<?php

class Room_Controller extends Controller_Rest
{
	private $user;

	private $room_model;

	public function __construct()
	{
		parent::__construct();

		$this->user = new Session_User();
		$this->room_model = new Chatroom_Model();
	}


	public function post_create()
	{
		$helper = new Chatroom_Helper();
		$id = null;

		while(!$this->room_model->create_room($id, $this->user->get_id()))
		{
			$id = $helper->generate_id($this->config->get('chatroom.id_length'));
		}

		Output::return_json(array('id' => $id));	
	}
}