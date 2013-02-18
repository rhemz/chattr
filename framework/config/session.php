<?php

$config['use_session']	= true;

$config['type']			= 'file'; // currently supported are 'file', 'database'
$config['name']			= 'rzmvc';
$config['timeout']		= 60*60*24; // 1 day
$config['hash']			= "9C]G#tx6M^X'=x>l[6v(zj:%FrcA6*e}"; // change this in your application settings!

// for database sessions
$config['table_name']	= 'mvcsession';
$config['gc_percent']	= 1; // 1% chance of running garbage collect 
$config['encrypt']		= false;