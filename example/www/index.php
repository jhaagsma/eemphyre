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

//--START MICROTIMER
if(empty($time_start))
	$time_start = microtime(true);
//--END MICROTIMER START

//--ERROR LOGGING START!
error_reporting(E_ALL); //basic error handling for now... to use the below you MUST make a folder with error logs
/*
global $errorlogging;
$errorlogging = 1;
include('../empiresphpframework/core/errorlog.php'); //THIS NEEDS TO BE AT THE TOP FOR ERRORS TO BE HANDLED BY THE ERROR HANDLER AS EARLY AS POSSIBLE
*/
//--END ERROR LOGGING

//--TIMEZONE START!
date_default_timezone_set('GMT'); //SET THE TIMEZONE FIRST //GMT BY DEFAULT
//--END TIMEZONE

//INCLUDE THE APC CORE TO ALLOW CACHING OF THE ROUTING OBJECT
include('../empiresphpframework/core/apc.php');
$cache = new Cache();

$registries = array(
	'./internal_pages/internal_registry.php',
	'./public_pages/moved_registry.php',
	'./public_pages/public_registry.php',
	'./static/static_registry.php'
);//put these in config?

include('../empiresphpframework/core/router.php');
$filetime = filemtime('../empiresphpframework/core/router.php'); //make sure we haven't changed the router object since it was last cached
foreach($registries as $r)
	$filetime = max($filetime, filemtime($r)); //make sure we haven't changed the registry files since the router was last cached

$router = $cache->fetch('r:' . $_SERVER['HTTP_HOST']);
if(!$router || $router->time < $filetime){ //registries file time
	$router = new PHPRouter($filetime);

	foreach($registries as $r)
		include_once($r);

	$cache->store('r:' . $_SERVER['HTTP_HOST'],$router,86400*2);
}

/*//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PUT IN YOUR CONFIG FILE PATH HERE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PUT IN YOUR CONFIG FILE PATH HERE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PUT IN YOUR CONFIG FILE PATH HERE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PUT IN YOUR CONFIG FILE PATH HERE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
/*//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PUT IN YOUR CONFIG FILE PATH HERE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<*/
//MUST INCLUDE config.php BECAUSE IT HAS UN/PWD STUFF FOR SQL 
include('/var/empiresphpframework/config.php');   //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<< PUT IN YOUR CONFIG FILE PATH HERE <<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<

//INCLUDE THE MYSQL CORE
include('../empiresphpframework/core/mysql.php');
$db = new MysqlDb($db['host'], $db['db'], $db['db_un'], $db['db_pwd'], false, 'counters', true);


//THIS FILE INCLUDES GENERAL FUNCTIONS THAT ARE USED EVERYWHERE
include_once('./general.php');

$route = $router->route(); //CREATE THE ROUTE OBJECT --> THIS CHECKS THE REGISTRY FOR THE PATHS & CLEANS VARIALBES AND STUFF
$user = $auth_opt = $auth_opt2 = null;

if($route->auth){ //IF AUTH IS SET, CHECK IF THERE'S A USER AND AUTH HIM
	$sessiontime = 2*3600; //THIS IS HOW LONG WE WANT LOGIN SESSIONS TO LAST IN SECONDS (obivously)
	include('./users/user_class.php');
	include('./users/auth_fns.php');
	$user = do_user_check($sessiontime);
	
	//The & on auth_opt is because passing by reference doesn't work right in call_user_func apparently; though it seems to be working for the route->data and route->path and $user
	//see http://ca2.php.net/manual/en/language.references.pass.php#99549
	$auth_opt = call_user_func($route->auth,$route->data,$route->path,$user,&$auth_opt2); //This has redirects inside of it; can return optional $auth_opt; add more auth_opts if you need more
	//auth_opt is typically $server, auth_opt2 is for $gc and $mc
}

ob_start();  //START THE OUTPUT BUFFER
if($route->file)
	require_once($route->file); //INCLUDE THE FILE THAT HAS THE FUNCTION

$ret = call_user_func($route->function, $route->data, $route->path, $user, $auth_opt, $auth_opt2);  //CALL THE ACTUAL FUNCTION

////SKINNING//////
include('./skins/skin_class.php');

$skin = new Skin($route->path->skin, is_mobile());
$skin->include_and_reset_buffer(ob_get_length(), ob_get_clean(), $ret, $route->data, $route->path, $user, $auth_opt, $auth_opt2);

echo ob_get_clean();

exit;


function def( & $var, $def){
	return (isset($var) ? $var : $def);
}

function is_mobile(){
	return false; // stub until we have a better mobile detection one; the one in Earth Empires is probably not appropriate for distribution heh
}

