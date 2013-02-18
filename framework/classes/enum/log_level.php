<?php

/**
* Log level enumeration.  A bitmask can be used in the application logging config to define
* the level of logging that is displayed to the user or written to a corresponding logfile.
* One could choose to print just notices and warnings by setting Log_Level::Notice | Log_Level::Warning,
* while writing Log_Level::Warning | Log_Level::Error level messages to a logfile.
*/
class Log_Level extends Enum
{
	const Error = 1;
	const Warning = 2;
	const Notice = 4;
	const All = 8;
}