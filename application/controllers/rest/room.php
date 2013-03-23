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


	/**
	* Create a new chatroom with a unique ID
	* @return mixed JSON 
	*/
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


	/**
	* Get the incoming messages that have been sent to the chatroom since the last time the user polled.
	* @param string $room_id The unique chatroom ID
	* @return mixed JSON 
	*/
	public function get_messages($room_id)
	{
		if(!$this->room_model->room_exists($room_id))
		{
			Output::return_json(array('success' => false, 'message' => 'Room does not exist'));
		}

		if(($messages = $this->message_model->get_messages($room_id, $this->user->get_id())) !== false)
		{
			if(is_null(Input::get('hackalicous')))
			{
				foreach($messages->rows as &$row)
				{
					$row['text'] = htmlentities($row['text']);
				}
			}
			Output::return_json(array('success' => true, 'messages' => $messages->rows));
		}

		Output::return_json(array('success' => false, 'message' => 'A database error occurred trying to fetch chatroom messages'));
	}


	/**
	* Process a leave-room request for the current user
	* @param string $room_id The unique chatroom ID
	* @return mixed JSON 
	*/
	public function get_leave($room_id)
	{
		Output::return_json(array('success' => $this->room_model->leave_room($room_id, $this->user->get_id())));
	}


	/**
	* Get the current active users in a given chatroom
	* @param string $room_id The unique chatroom ID
	* @return mixed JSON 
	*/
	public function get_users($room_id)
	{
		$users = $this->room_model->get_users($room_id, $this->config->get('chatroom.time_leave'));

		if($users = $this->room_model->get_users($room_id, $this->config->get('chatroom.time_leave')))
		{
			Output::return_json(array('success' => true, 'users' => $users->rows));
		}
		Output::return_json(array('success' => false, 'message' => 'A database error occurred trying to fetch chatroom users'));
	}
}
