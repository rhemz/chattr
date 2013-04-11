<?php

class User_Controller extends Controller_Rest
{
	public function __construct()
	{
		parent::__construct();
	}


	public function put_name()
	{
		$user = new Session_User();

		if(strlen($u = trim(Input::put('username'))) >= $this->config->get('user.username_min_length'))
		{
			Output::return_json(array('success' => $user->set_name($u)));
		}
		else
		{
			Output::return_json(array('success' => false, 'message' => 'Invalid username'));
		}
		
	}
}