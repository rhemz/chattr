<?php

class Home_Controller extends Controller
{
	private $user;

	public function __construct()
	{
		parent::__construct();

		$this->user = new Session_User();
	}


	public function index()
	{
		$this->load_view('home', array('user' => $this->user));
	}


	public function chatroom($room_id)
	{
		// just load the room view and make the user & room ID accessible to it.
		// all other data to be loaded w/ ajax calls from the REST controllers

		$rooms = new Chatroom_Model();

		if($rooms->room_exists($room_id))
		{
			$this->load_view('chat', array('user' => $this->user, 'room_id' => $room_id));
		}
		else
		{
			// load the 404 view
			$this->load_view('404', array(
				'title' => 'Room not found', 
				'message' => "Sorry, the chatroom you're looking for does not exist"));
		}

		
	}
}