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
$router->dir_set('./internal_pages');
$router->default_auth('auth_login');
$router->default_skin('internal');
$router->area_set('internal');  // /internal
//main page
$router->add('GET', '/',							'landing_page.php',			'landing_page', array('error'=>'int'));
$router->add('GET', '/users',						'landing_page.php',			'users');
$router->add('GET', '/users/{userid=>u_int}',		'landing_page.php',			'userpage');

$router->area_push('admin');  // /internal/admin
$router->default_auth('auth_admin');
$router->default_skin('admin');

$router->add('GET', '/',							'landing_page.php',			'admin');
$router->add('GET', '/show_routing_tree',			'show_routing_tree.php',	'show_routing_tree');
