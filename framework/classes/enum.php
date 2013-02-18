<?php

abstract class Enum
{
	private final function __construct()
	{

	}


	public static function toString($val)
	{
		$r = new ReflectionClass(get_called_class());
		$constants = array_flip($r->getConstants());

		return $constants[$val];

	}
}