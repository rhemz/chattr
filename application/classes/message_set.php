<?php

class Message_Set
{
	private $messages = array();

	public function __construct($db_result)
	{
		foreach($db_result->rows as $row)
		{
			$this->messages[] = new Message($row);
		}
	}


	public function get_messages($sanitized = true)
	{
		foreach($this->messages as &$message)
		{
			if($sanitized)
			{
				$message->sanitize();
			}
			$message->prepare();
		}

		return $this->messages;
	}
}
