<?php

$config['default_controller'] = 'home';




/*
	Wildcard:
		%var

	Argument access:
		$n   i.e. $1, $2, $3, etc

	Ex: 
		"/xx/%var/%var/%var"	=> '/test/index/$1/$2/$3',
*/

$config['mappings'] = array(
	'/room/%var'					=> '/home/chatroom/$1',

	// RESTful routes
	'/rest/room/%var/messages'		=> '/rest/room/messages/$1',
	'/rest/room/%var/send'			=> '/rest/message/send'
);
