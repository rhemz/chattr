<?php


$config['id_length']				= 16;
$config['id_chars'] 				= 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';


$config['time_leave']				= 30; // if no message checks comes from client for 30s, assume they left/closed room
$config['time_dead']				= 60*10; // 10 minutes of no message checks, room is dead