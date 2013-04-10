<?php

class Message_Parser_Url extends Message_Parser_Base
{

	public function contains_key()
	{
		return false;
	}


	public function parse()
	{
		// make sure there is an http:// on all URLs
		$this->text = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", ' ' . $this->text);
		
		// make all URLs links
		$this->text = preg_replace("/([\w]+:\/\/[\w-?&;#~=\.\/\@]+[\w\/])/i","<a target=\"_blank\" href=\"$1\">$1</A>", $this->text);
		
		// make all emails hot links
		$this->text = preg_replace("/([\w-?&;#~=\.\/]+\@(\[?)[a-zA-Z0-9\-\.]+\.([a-zA-Z]{2,3}|[0-9]{1,3})(\]?))/i","<a href=\"mailto:$1\">$1</A>", $this->text);

		return $this->text;
	}
}