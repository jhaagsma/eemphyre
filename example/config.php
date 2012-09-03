<?php
//THIS IS THE EXAMPLE CONFIGURATION FILE!

$db = array(
	'db_un'=>"framework_ex",
	'db_pwd'=>"MyYDMDT6A2xZtHaw",
	'host'=>"localhost",
	'db'=>"framework_ex"
);

//EXTRA SALT so that you have salt for your passwords that is contained in code rather than in the database
$extra_salt = '947yhf';

//BASE DIRECTORY
$base_dir = '/var/yourproject';
		
//SET DEBUG VARIABLE
$debug = false;

//This variable enables outputting error messages to IRC, assuming you have the bot cluster enabled and running
$output_errors_to_irc = false;

//SET WHETHER OR NOT YOU CAN USE mail()
$can_send_mail = false;

//SET WHETHER OR NOT YOU CAN USE recaptcha AND PUT YOUR APPROPRIATE SITE INFORMATION AND KEYS HERE
$use_recaptcha = false;
$recaptcha = array(
	'example.com' => array('public_key'=>"", 'private_key'=>"")
);

//IMAGE OR FILE MIRROR
$image_file_mirror = "http://img.example.com/";
