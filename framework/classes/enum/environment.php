<?php

/**
* Application environment enumeration, to be set in the application environment config file.
* Many rzMVC Exceptions expose different amounts of data depending on the current operating environment.
*/
class Environment extends Enum
{
	const None = 0; // only here to allow for loading of config files in base directory in fringe-cases
	const Development = 1;
	const Staging = 2;
	const Testing = 4;
	const Production = 8;
}