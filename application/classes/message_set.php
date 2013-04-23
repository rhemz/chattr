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


	public function get_messages($raw = false)
	{
		foreach($this->messages as &$message)
		{
			// going to have to look at the order of operations in here, some parsers generate HTML, don't want to sanitize/escape them
			if(!$raw)
			{
				$message->sanitize();
				$message->prepare();
			}
		}

		return $this->messages;
	}
}
