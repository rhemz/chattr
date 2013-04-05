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
		$this->text = htmlentities($this->text);
	}


	public function prepare()
	{
		// make sure there is an http:// on all URLs
		$this->text = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", ' ' . $this->text);
		// make all URLs links
		$this->text = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</A>", $this->text);
		// make all emails hot links
		$this->text = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<a href=\"mailto:$1\">$1</A>", $this->text);


		// translate smileys to images, etc...
	}


	public function __toString()
	{
		return $this->text;
	}
}