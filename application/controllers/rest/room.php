<?php

class Room_Controller extends Controller_Rest
{
	private $user;

	private $room_model;
	private $message_model;

	public function __construct()
	{
		parent::__construct();

		$this->user = new Session_User();

		$this->room_model = new Chatroom_Model();
		$this->message_model = new Message_Model();
	}


	public function post_create()
	{
		$helper = new Chatroom_Helper();
		$id = null;

		while(!$this->room_model->create_room($id, $this->user->get_id()))
		{
			$id = $helper->generate_id($this->config->get('chatroom.id_length'));
		}

		Output::return_json(array('success' => true, 'id' => $id));	
	}


	public function get_messages($room_id)
	{
		if(!$this->room_model->room_exists($room_id))
		{
			Output::return_json(array('success' => false, 'message' => 'Room does not exist'));
		}

		if(($messages = $this->message_model->get_messages($room_id, $this->user->get_id())) !== false)
		{
			Output::return_json(array('success' => true, 'messages' => $messages->rows));
		}

		Output::return_json(array('success' => false, 'message' => 'A database error occurred'));
	}


	public function get_leave($room_id)
	{
		Output::return_json(array('success' => $this->room_model->leave_room($room_id, $this->user->get_id())));
	}
}
