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
		$this->text = sprintf('<img src="%s" class="embedded" />', $this->text);

		return $this->text;
	}
}