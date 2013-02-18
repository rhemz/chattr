<?php

/**
* Application environment enumeration, to be set in the application environment config file.
* Many rzMVC Exceptions expose different amounts of data depending on the current operating environment.
*/
class Environment extends Enum
{
	const Development = 1;
	const Staging = 10;
	const Production = 20;
}