<?php


$config['id_length']				= 16;
$config['id_chars'] 				= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';


$config['time_leave']				= 30; // if no message checks comes from client for 30s, assume they left/closed room
$config['time_dead']				= 60*10; // 10 minutes of no message checks, room is dead


$config['client_check_interval']	= 3000; // 1s.  to be twerked.
$config['client_check_timeout']		= 3000;

$config['client_send_interval']		= 100; // allow no more than 10 messages to be sent per second
$config['client_send_timeout']		= 5000;