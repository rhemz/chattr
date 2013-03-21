<?php


$config['id_length']				= 16;
$config['id_chars'] 				= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';


$config['time_leave']				= 10; // if no message checks comes from client for 10s, assume they left/closed room
$config['time_dead']				= 60*30; // 30 minutes of no message checks, room is dead


$config['message_check_interval']	= 2000; // 1s.  to be twerked.
$config['message_check_timeout']	= 2600;

$config['message_send_interval']	= 100; // allow no more than 10 messages to be sent per second
$config['message_send_timeout']		= 5000;

$config['room_check_interval']		= 5000;
$config['room_check_timeout']		= 2000;