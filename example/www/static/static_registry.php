<?php

$router->area_set(null);

 //These three files are special because they are expected to be there! DO **NOT** ADD MORE LIKE THIS!
$router->add('GET', '/favicon.ico',					'./static/static.php',		'favicon');
$router->add('GET',	'/sitemap.xml',					'./static/static.php',		'sitemap');
$router->add('GET',	'/robots.txt',					'./static/static.php',		'robots');

$router->add('GET', '/file/{filename=>string}',		'./static/static.php',		'display_file');
$router->add('GET', '/img/{filename=>string}',		'./static/static.php',		'display_img');
$router->add('GET', '/js/{filename=>string}',		'./static/static.php',		'display_js');
$router->add('GET', '/css/{filename=>string}',		'./static/static.php',		'display_css');
$router->add('GET', '/xml/{filename=>string}',		'./static/static.php',		'display_xml');
