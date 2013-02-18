<?php

class Home_Controller extends Controller
{
	public function __construct()
	{
		parent::__construct();
	}


	public function index()
	{
		// go from here!
		$this->load_view('home', array('something' => "I'm some data from the controller!"));
	}
}