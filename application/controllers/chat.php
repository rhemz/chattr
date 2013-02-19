<?php

class Chat_Controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
		$chatArray = [
			"progName" => "Chattr",
			"userName" => "TestUser",
			"users" => [
				"user1",
				"user2",
				"user3"
			]
		];
		// go from here!
		$this->load_view('chat', $chatArray);
	}
}