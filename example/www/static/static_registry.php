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
