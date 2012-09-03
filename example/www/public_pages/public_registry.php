<?php

$router->area_set(null);
//homepage
$router->add('GET', '/',			'./public_pages/homepage.php',	'homepage', null, 'auth_public','public');

$router->add('GET', '/login',		'./public_pages/login.php',		'login', array('error'=>'u_int'), 'auth_public','public');
$router->add('POST', '/login',		'./public_pages/login.php',		'loginfn', array('username'=>'string','password'=>'string'), null, null);
$router->add('GET', '/logout',		'./public_pages/login.php',		'logout', array('error'=>'u_int'), 'auth_public','public');

$router->add('GET',	'/register',	'./public_pages/register.php',	'register',
	array(
		'error'=>'int',
		'success'=>'int',
		'username'=>'string',
		'displayname' => 'string',
		'email'=>'string',
		'emailtwo'=>'string'
	),
'auth_public','public');

$router->add('POST','/register',	'./public_pages/register.php',	'registerfn', 
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
	)
);







$router->area_set("router_test");
$router->add('GET',		'/',					'./public_pages/router_test.php', 	'start', null, 'auth_public','public');
$router->add('GET',		'/basic',				'./public_pages/router_test.php', 	'basic', null, 'auth_public','public');
$router->add('POST',	'/basic',				'./public_pages/router_test.php', 	'basicfn', 	array(
		'test_u_int'	=>'u_int',
		'test_int'		=>'int',
		'test_bool'		=>'bool',
		'test_float'	=>'float',
		'test_double'	=>'double',
		'test_string'	=>'string'
	),
'auth_public','public');

$router->add('GET',		'/defaults',			'./public_pages/router_test.php', 	'defaults', null, 'auth_public','public');
$router->add('POST',	'/defaults',			'./public_pages/router_test.php', 	'defaultsfn', 	array(
		'test_u_int'	=>array('u_int',1337),
		'test_int'		=>array('int',-1337),
		'test_bool'		=>array('u_int',false),
		'test_float'	=>array('float',3.1415926),
		'test_double'	=>array('double',3.14159261415926141592614159261415926),
		'test_string'	=>array('string','kitchen sink is 3.141')
	),
'auth_public','public');

$router->add('GET',		'/arrays1',				'./public_pages/router_test.php', 	'arrays1', null, 'auth_public','public');																								
$router->add('POST',	'/arrays1',				'./public_pages/router_test.php', 	'arrays1fn', 	array(
		'testarray2'	=>array('array',null,'double'),
		'testarray2a'	=>array('array',13,'int'),
		'testarray2b'	=>array('array',null,array('int',136),'u_int')
	),
'auth_public','public');


$router->add('GET',		'/arrays2',				'./public_pages/router_test.php', 	'arrays2', null, 'auth_public','public');																								
$router->add('POST',	'/arrays2',				'./public_pages/router_test.php', 	'arrays2fn', 	array(
		'testarray3'	=>array('array',null,array('array','blah','string')),
		'testarray3a'	=>array('array',null,array('array',3,'int'))
	),
'auth_public','public');


$router->add('GET',		'/arrays3',				'./public_pages/router_test.php', 	'arrays3', null, 'auth_public','public');																								
$router->add('POST',	'/arrays3',				'./public_pages/router_test.php', 	'arrays3fn', 	array(
		'testarray4'	=>array('array',null,array('array',null,array('array',null,array('array',3,'int'))))
	),
'auth_public','public');
