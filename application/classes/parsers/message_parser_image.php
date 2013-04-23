<?php

class Message_Parser_Image extends Message_Parser_Base
{

	public function contains_key()
	{
		if(($path = parse_url($this->text, PHP_URL_PATH)) !== false)
		{
			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			return ($ext == 'png' || $ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'bmp');
		}
		return false;
	}


	public function parse()
	{
		// make sure there is an http:// on all URLs
		$this->text = preg_replace("/([^\w\/])(www\.[a-z0-9\-]+\.[a-z0-9\-]+)/i", "$1http://$2", $this->text);

		//list($width, $height, $type, $attr) = getimagesize($this->text);		
		//$this->text = sprintf('<img src="%s" height="%s" width="%s" class="embedded" />', $this->text, $height, $width);
		
		$this->text = sprintf('<a href="%s" target="_blank"><img src="%s" class="embedded" /></a>', $this->text, $this->text);

		return $this->text;
	}
}