<?php

class User_Controller extends Controller_Rest
{
	public function __construct()
	{
		parent::__construct();
	}


	public function post_name()
	{
		$user = new Session_User();

		Output::return_json(array('success' => $user->set_name(Input::post('username'))));
	}
}