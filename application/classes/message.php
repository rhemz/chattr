<?php


class Message
{
	public $message_id;
	public $text;
	public $user_id;
	public $user_name;
	public $timestamp;

	public function __construct($row)
	{
		$this->message_id = $row->message_id;
		$this->text = $row->text;
		$this->user_id = $row->user_id;
		$this->user_name = $row->user_name;
		$this->timestamp = $row->timestamp;
	}


	public function sanitize()
	{
		$this->text = nl2br(htmlentities($this->text));
	}


	public function prepare()
	{
		
		$youtube = new Message_Parser_Youtube($this->text);
		$image = new Message_Parser_Image($this->text);

		// as more parsers are added, this logic will become more and more complex

		// if message is a youtube link, only generate the embed code
		if($youtube->contains_key())
		{
			$this->text = $youtube->parse();
		}
		// if a message is a single image URL, display the image
		else if($image->contains_key())
		{
			$this->text = $image->parse();
		}
		else
		{
			// load other parsers, continue logic
			$url = new Message_Parser_Url($this->text);
			$this->text = $url->parse();
		}
	}


	public function __toString()
	{
		return $this->text;
	}
}