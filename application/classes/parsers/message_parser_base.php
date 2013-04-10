<?php


abstract class Message_Parser_Base
{

	protected $text;

	public function __construct($text)
	{
		$this->text = $text;
	}


	abstract public function contains_key();

	abstract public function parse();


}