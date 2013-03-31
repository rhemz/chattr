<?php

class Message_Set
{
	private $messages;

	public function __construct($db_result)
	{
		foreach($db_result->rows as $row)
		{
			$this->messages[] = new Message($row);
		}
	}


	public function get_messages($sanitized = true)
	{
		if($sanitized)
		{
			foreach($this->messages as $message)
			{

			}
		}
	}
}
