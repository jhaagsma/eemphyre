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

$router->area_set('internal');
//main page
$router->add('GET', '/',							'./internal_pages/landing_page.php', 'landing_page', array('error'=>'int'), 'auth_login','internal');
$router->add('GET', '/users',						'./internal_pages/landing_page.php', 'users',		null, 'auth_login','internal');
$router->add('GET', '/users/{userid=>u_int}',		'./internal_pages/landing_page.php', 'userpage',	null, 'auth_login','internal');

$router->area_push('admin');
$router->add('GET', '/',							'./internal_pages/landing_page.php', 'admin',		null, 'auth_admin','admin');
$router->add('GET', '/show_routing_tree',			'./internal_pages/show_routing_tree.php', 'show_routing_tree', null, 'auth_admin','admin');
