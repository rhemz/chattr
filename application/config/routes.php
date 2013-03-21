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
	'/rest/room/%var/messages'		=> '/rest/room/messages/$1', // GET new messages for room
	'/rest/room/%var/users'			=> '/rest/room/users/$1', // GET the current users in the room
	'/rest/room/%var/send'			=> '/rest/message/send/$1', // POST (send) a message to the room
	'/rest/room/%var/leave'			=> '/rest/room/leave/$1' // GET leave the room
);
