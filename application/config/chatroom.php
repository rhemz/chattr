<?php


$config['id_length']				= 16;
$config['id_chars'] 				= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';


$config['time_leave']				= 7; // if no message checks comes from client for 7s, assume they left/closed room
$config['time_dead']				= 60*10; // 10 minutes of no message checks, room is dead


$config['message_check_interval']	= 800; // 800ms.  to be twerked.
$config['message_check_timeout']	= 2000;

$config['message_send_interval']	= 100; // allow no more than 10 messages to be sent per second
$config['message_send_timeout']		= 5000;

$config['room_check_interval']		= 3500;
$config['room_check_timeout']		= 2000;

$config['notification_title']		= 'New Message';
$config['notification_path']		= '/public/images/notification.png';
$config['notification_timeout']		= 8000;
$config['notification_cookie']		= 'chattr_notify';

$config['title_blink']				= true;
$config['title_blink_count']		= 10;
$config['title_blink_delay']		= 1000;