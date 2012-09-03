<?php
$router->area_set('internal');
//main page
$router->add('GET', '/',							'./internal_pages/landing_page.php', 'landing_page', array('error'=>'int'), 'auth_login','internal');
$router->add('GET', '/users',						'./internal_pages/landing_page.php', 'users',		null, 'auth_login','internal');
$router->add('GET', '/users/{userid=>u_int}',		'./internal_pages/landing_page.php', 'userpage',	null, 'auth_login','internal');

$router->area_push('admin');
$router->add('GET', '/',							'./internal_pages/landing_page.php', 'admin',		null, 'auth_admin','admin');
$router->add('GET', '/show_routing_tree',			'./internal_pages/show_routing_tree.php', 'show_routing_tree', null, 'auth_admin','admin');
