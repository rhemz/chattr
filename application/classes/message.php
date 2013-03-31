<?php


class Message
{
	public $id;
	public $text;
	public $user_id;
	public $user_name;

	public function __construct($row)
	{
		$this->id = $row['id'];
		$this->text = $row['text'];
		$this->user_id = $row['user_id'];
		$this->user_name = $row['user_name'];
	}


	public function sanitize()
	{
		
	}


	public function __toString()
	{
		return $this->text;
	}
}