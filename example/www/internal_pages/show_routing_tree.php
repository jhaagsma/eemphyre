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

//comments on 'r': mapping
	//node aka o => 0
	//variable aka v =>1
	//static aka s =>2
	//name aka n => 3
	//type aka t => 4

function show_routing_tree($data,&$path,&$user){
	global $cache;
	$router = $cache->fetch('r:' . $_SERVER['HTTP_HOST']);
	echo "<pre>\n";
	foreach($router->paths as $type => $r){
		echo "$type\n";
		$offset = strlen($type);
		$bit = '/';
		$branch = $r;
		$urlify = '<a href="' . $bit . '">' . $bit . '</a>';
		echo str_repeat(' ',$offset), $urlify;
		if(isset($r[0]))
			pageline($branch, $offset, $bit);
		else
			echo "\n";
		if($type == "GET")
			display_branch($user,$r,$offset,null,true);
		else
			display_branch($user,$r,$offset,null,false);	
	}
	echo "</pre>";
	return;
}

function display_branch($user,&$r, $offset, $previd, $dourl){
	if(isset($r[2])){
		foreach($r[2] as $id=>$branch){
			$id = '/' . $id;
			$sty = null;
			if($dourl && isset($branch[0])){
				$sty = (stristr($branch[0][0],'pages_moved') ? ' style="color:#555555"' : null);
				$urlify = '<a href="' . $previd . $id . '"' . $sty . '>' . $id . '</a>';
			}
			else
				$urlify = $id;
			echo str_repeat(' ',$offset), $urlify;
			if(isset($branch[0]))
				pageline($branch, $offset, $id, $sty);
			else
				echo "\n";
			
			display_branch($user,$branch,$offset + strlen($id), $previd . $id, $dourl);
		}
	}
	if(isset($r[1])){
		//foreach($r[1] as $id=>$branch){
		//var_export($r[1]);
		$id = $r[1][3];
		$branch = $r[1];
		$type = $r[1][4];
		$bit = '/{' .  $id . '=>' .  $type . '}';
		
		switch($id){
			default; break;
		}
		
		if($dourl && isset($branch[0]))
			$urlify = '<a href="' . $previd . '/' . $id . '">' . $bit . '</a>';
		else
			$urlify = $bit;
		echo str_repeat(' ',$offset), $urlify;
		if(isset($branch[0]))
			pageline($branch, $offset, $bit);
		else
			echo "\n";
		
		display_branch($user,$branch,$offset + strlen($bit), $previd . '/' . $id, $dourl);
	}
}

function pageline($branch, $offset, $bit, $sty = null){
	echo "<span$sty>", str_repeat('.',max(0,62-$offset-strlen($bit))),  " ", (isset($branch[0][3]) ? $branch[0][3] : null),  str_repeat(' ',max((isset($branch[0][3]) ? 25-strlen($branch[0][3]) : 25),3)), ":: ", $branch[0][1],str_repeat(' ',max(32-strlen($branch[0][1]),3)), ":: ", $branch[0][0], "</span>\n";
}

