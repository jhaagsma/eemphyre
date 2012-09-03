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

///////////////////////////////////
//////////  301 section  //////////  
///////////////////////////////////
$router->area_set(null); 
//this is to 301 the googlebot which keeps hitting /showclan &etc
$router->add('GET', '/some_page',		'./public/moved_pages.php',		'moved_some_page');

///////////////////////////////////
//////////    END 301s   //////////  
///////////////////////////////////
