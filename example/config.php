<?php
/*---------------------------------------------------
These files are part of the empiresPHPframework;
The original framework core (specifically the mysql.php
the router.php and the errorlog) was started by Timo Ewalds,
and rewritten to use APC and extended by Julian Haagsma,
for use in Earth Empires (located at http://www.earthempires.com );
it was spun out for use on other projects.

The general.php contains content from Earth Empires
written by Dave McVittie and Joe Obbish.


The example website files were written by Julian Haagsma.

All files are licensed under the MIT License.

First release, September 3, 2012
---------------------------------------------------*/

//THIS IS THE EXAMPLE CONFIGURATION FILE!

$db = array(
	'db_un'=>"",
	'db_pwd'=>"",
	'host'=>"",
	'db'=>""
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
