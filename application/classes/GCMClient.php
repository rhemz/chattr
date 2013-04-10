<?php

class GCM_Client
{
	const GCM_Send_URI = 'https://android.googleapis.com/gcm/send';

	private $api_key;
	private $registration_ids = array();
	private $message;
	private $headers;

	private $curl;
	private $response;


	public function __construct($api_key)
	{
		$this->api_key = $api_key;

		$this->headers = array(
			'Authorization: key=' . $this->api_key,
			'Content-Type: application/json'
		);

		$this->curl = curl_init();
		curl_setopt($this->curl, CURLOPT_URL, self::GCM_Send_URI);
		curl_setopt($this->curl, CURLOPT_POST, true);
		curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);
	}


	public function add_recipient($recipients)
	{
		if(!is_array($recipients))
		{
			$recipients = array($recipients);
		}

		array_push($this->registration_ids, $recipients);
	}


	public function set_message($message)
	{
		$this->message = $message;
	}


	public function send()
	{
		$fields = array(
			'registration_ids'	=> $this->registration_ids,
			'data'				=> array('message'	=> $this->message)
		);

		curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($fields));

		$this->response = curl_exec($this->curl);
		return $this->response;
	}


	private function __destruct()
	{
		curl_close($this->curl);
	}

}