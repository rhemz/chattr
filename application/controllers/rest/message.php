<?php


class Message_Controller extends Controller_Rest
{
	private $user;

	private $message_model;


	public function __construct()
	{
		parent::__construct();

		$this->user = new Session_User();
	}


	public function post_send($room_id)
	{
		$room_id = Input::post($this->config->get('message.key_room'));
		$text = Input::post($this->config->get('message.key_text'));

		$val = new Validation();

		$val->register($this->config->get('message.key_text'))
			->rule('required')
			->rule('min_length', 1)
			->rule('max_length', $this->config->get('message.max_length'));

		$val->register($this->config->get('message.key_room'))
			->rule('required')
			->rule('exact_length', $this->config->get('chatroom.id_length'))
			->rule('custom', 'Message_Validation::valid_room');


		if(!$val->validate())
		{
			Output::return_json(array('success' => false, 'message' => 'Invalid request'));
		}
		else
		{
			// valid chat message
			$this->message_model = new Message_Model();
			if(($id = $this->message_model->save_message($this->user->get_id(), $room_id, $text)) !== false)
			{
				Output::return_json(array('success' => true, 'id' => $id));
			}
			
			Output::return_json(array('success' => false, 'message' => 'Unable to save message'));			
		}



		
	}
}