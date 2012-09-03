#!/usr/bin/php
<?php
error_reporting(E_ALL);
echo "\n\nHello! This will now set up a mysql example database for the example website!\n";

include("../empiresphpframework/core/mysql.php");
$config_location = "../config.php";
include($config_location); // you can changet this before running it to wherever you've moved the config file, if it's not in this location

if(!$db['db_un'] || !$db['db_pwd'] || !$db['host'] || !$db['db']){
	echo "You must fill out the config file with a database, and a database user and password! Currently looking in $config_location\n\n";
	exit;
}

$db = new MysqlDb($db['host'], $db['db'], $db['db_un'], $db['db_pwd']);

echo "\nCreating queries table...";

$db->query("CREATE TABLE IF NOT EXISTS `framework_ex`.`queries` (  `hash` char( 32  )  NOT  NULL ,
 `strlen` smallint( 4  )  unsigned NOT  NULL ,
 `last_time` int( 11  )  unsigned NOT  NULL ,
 `total_num` int( 11  )  NOT  NULL ,
 `total_time` float NOT  NULL ,
 `min_time` float NOT  NULL ,
 `max_time` float NOT  NULL ,
 `avg_time` float NOT  NULL ,
 `new_mean` float NOT  NULL ,
 `new_s` float NOT  NULL default  '0',
 `new_stdev` float NOT  NULL default  '0',
 `query` text,
 `last_page` text,
 PRIMARY  KEY (  `hash` ,  `strlen`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1");
 
echo "\nCreating users table with max email length of 40; anybody longer than that should get a new email address anyway heh...";

$db->query("CREATE TABLE IF NOT EXISTS `framework_ex`.`users` (  `userid` int( 10  )  unsigned NOT  NULL  auto_increment ,
 `username` char( 18  )  character  set utf8 collate utf8_bin NOT  NULL ,
 `password` char( 128  )  NOT  NULL ,
 `salt`  char( 8  ) NOT  NULL ,
 `email` char( 40 ) character  set utf8 collate utf8_bin NOT  NULL, 
 `displayname` CHAR( 18 ) character  set utf8 collate utf8_bin NOT NULL,
 `disabled`  BOOL NOT  NULL DEFAULT 0,
 `administrator` BOOL NOT NULL DEFAULT 0,
 `registered` int( 11  )  unsigned NOT  NULL ,
 `logintime` int( 11  )  unsigned NOT  NULL ,
 `last_ip` int( 11  )  unsigned NOT  NULL ,
 PRIMARY  KEY (  `userid`  ) ,
 UNIQUE  KEY  `username` (  `username`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;");

echo "\nCreating active_sessions table...";

$db->query("CREATE TABLE IF NOT EXISTS `framework_ex`.`active_sessions` (  `loginid` int( 11  )  unsigned NOT  NULL  auto_increment ,
 `userid` int( 11  )  unsigned NOT  NULL ,
 `key` char( 32  )  NOT  NULL ,
 `userip` char( 15  )  NOT  NULL ,
 `expiretime` int( 11  )  unsigned NOT  NULL ,
 `logintime` int( 11  )  unsigned NOT  NULL ,
 PRIMARY  KEY (  `loginid`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;");
 
echo "\nCreating login_attempts table...";

$db->query("CREATE  TABLE IF NOT EXISTS `framework_ex`.`login_attempts` (  `attemptid` int( 11  )  unsigned NOT  NULL  auto_increment ,
 `time` int( 11  )  unsigned NOT  NULL ,
 `username` char( 18  )  character  set utf8 collate utf8_bin NOT  NULL ,
 `userip` char( 15  )  NOT  NULL ,
 `success` BOOL  NOT  NULL ,
 PRIMARY  KEY (  `attemptid`  )  ) ENGINE  =  MyISAM  DEFAULT CHARSET  = latin1;");
 
echo "\nDone!\n\n";
exit;
