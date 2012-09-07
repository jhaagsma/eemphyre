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

All files are licensed under the GPLv2.

First release, September 3, 2012
---------------------------------------------------*/
$router->clear_default();

$router->dir_set('./public_pages');
$router->default_auth('auth_public');
$router->default_skin('public');
//homepage
$router->add('GET', '/',			'homepage.php',	'homepage');

$router->add('GET', '/login',		'login.php',		'login', array('error'=>'u_int'));
$router->add('POST', '/login',		'login.php',		'loginfn', array('username'=>'string','password'=>'string'));
$router->add('GET', '/logout',		'login.php',		'logout', array('error'=>'u_int'));

$router->add('GET',	'/register',	'register.php',	'register',
	array(
		'error'=>'int',
		'success'=>'int',
		'username'=>'string',
		'displayname' => 'string',
		'email'=>'string',
		'emailtwo'=>'string'
	));

$router->add('POST','/register',	'register.php',	'registerfn', 
	array(
		'rules' => 'u_int',
		'username' => 'string',
		'displayname' => 'string',
		'email'=>'string',
		'emailtwo'=>'string',
		'password'=>'string',
		'passwordtwo'=>'string',
		'recaptcha_challenge_field'=>'string',
		'recaptcha_response_field'=>'string'
	));




$router->area_set("router_test");
$router->add('GET',		'/',					'router_test.php', 	'start');
$router->add('GET',		'/basic',				'router_test.php', 	'basic');
$router->add('POST',	'/basic',				'router_test.php', 	'basicfn',
	array(
		'test_u_int'	=>'u_int',
		'test_int'		=>'int',
		'test_bool'		=>'bool',
		'test_float'	=>'float',
		'test_double'	=>'double',
		'test_string'	=>'string'
	));

$router->add('GET',		'/defaults',			'router_test.php', 	'defaults');
$router->add('POST',	'/defaults',			'router_test.php', 	'defaultsfn',
	array(
		'test_u_int'	=>array('u_int',1337),
		'test_int'		=>array('int',-1337),
		'test_bool'		=>array('u_int',false),
		'test_float'	=>array('float',3.1415926),
		'test_double'	=>array('double',3.14159261415926141592614159261415926),
		'test_string'	=>array('string','kitchen sink is 3.141')
	));

$router->add('GET',		'/arrays1',				'router_test.php', 	'arrays1');																								
$router->add('POST',	'/arrays1',				'router_test.php', 	'arrays1fn',
	array(
		'testarray2'	=>array('array',null,'double'),
		'testarray2a'	=>array('array',13,'int'),
		'testarray2b'	=>array('array',null,array('int',136),'u_int')
	));


$router->add('GET',		'/arrays2',				'router_test.php', 	'arrays2');																								
$router->add('POST',	'/arrays2',				'router_test.php', 	'arrays2fn',
	array(
		'testarray3'	=>array('array',null,array('array','blah','string')),
		'testarray3a'	=>array('array',null,array('array',3,'int'))
	));


$router->add('GET',		'/arrays3',				'router_test.php', 	'arrays3');																								
$router->add('POST',	'/arrays3',				'router_test.php', 	'arrays3fn',
	array(
		'testarray4'	=>array('array',null,array('array',null,array('array',null,array('array',3,'int'))))
	));
