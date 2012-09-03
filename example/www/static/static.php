<?php
/*
NOTES FROM QZ:

it handles even the things like war_page.min.js which i hadn't originally considered
so now it explodes on . takes the last piece as extension, checks the second last piece to see if it's numeric
(assuming it's also not the first piece) and removes it if it is numeric.... and continues from there
so you can optionally have the filetime in there or not and it will handle it (using the prep_file() function)
just don't call something like.... something.2.js
call it something_2.js or something2.js just don't add JUST a number after a . immediately before the extension
except that calling something 1.png or 2.png should still work (being the first piece)
*/

function favicon($path){  //These three files are special because they are expected to be there! DO **NOT** ADD MORE LIKE THIS!
	return generic_xsendfile('favicon.ico','img');
}

function sitemap($path){  //These three files are special because they are expected to be there! DO **NOT** ADD MORE LIKE THIS!
	return generic_xsendfile('sitemap.xml','xml');
}

function robots($path){  //These three files are special because they are expected to be there! DO **NOT** ADD MORE LIKE THIS!
	return generic_xsendfile('robots.txt','txt');
}

function display_img($data,$path){
	$f = basename_extension_remove_timestamp($path->variables['filename']);
	return generic_xsendfile(implode('.',$f),'img'); //this just forces the type, so you can call it whatever you want
}

function display_js($data,$path){
	$f = basename_extension_remove_timestamp($path->variables['filename']);
	return generic_xsendfile(implode('.',$f),'js'); //this just forces the type, so you can call it whatever you want
}

function display_css($data,$path){
	$f = basename_extension_remove_timestamp($path->variables['filename']);
	return generic_xsendfile(implode('.',$f),'css'); //this just forces the type, so you can call it whatever you want
}

function display_xml($data,$path){
	$f = basename_extension_remove_timestamp($path->variables['filename']);
	return generic_xsendfile(implode('.',$f),'xml'); //this just forces the type, so you can call it whatever you want
}


function display_file($data,$path){
	$f = basename_extension_remove_timestamp($path->variables['filename']);
	return generic_xsendfile(implode('.',$f),get_type($f[1]));
}

function basename_extension_remove_timestamp($url){
	$pieces = explode(".",ltrim($url,'/'));
	$c = count($pieces);

	$extension = $pieces[$c-1];
	unset($pieces[$c-1]);

	if($c-2 > 0 && is_numeric($pieces[$c-2]))
		unset($pieces[$c-2]);
	$basename = implode('.',$pieces);
	
	return array(0=>$basename,1=>$extension);
}

function get_type($extension){
	switch($extension){
		case 'css': return 'css';
		case 'js': return 'js';
		case 'png': return 'img';
		case 'gif': return 'img';
		case 'jpg': return 'img';
		case 'xml': return 'xml';
		case 'html': return 'html';
		case 'txt': return 'txt';
		default: return 'img';
	}
}

function make_path($file,$type){
	return './static/' . $type . '/' . $file;
}

function generic_xsendfile($file,$type='img',$path=null){
	if(!$path)
		$path = make_path($file,$type);
		
	if(!file_exists($path)){
		trigger_error('X-SENDFILE: File doesn\'t exist: ' . $path . (isset($_SERVER['HTTP_REFERER']) ? ' -- Referrer: ' . $_SERVER['HTTP_REFERER'] . ' --' : ' --'));
		return false;
	}
	
	$mtime = filemtime($path);
	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
		if($mtime <= strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
			header($_SERVER["SERVER_PROTOCOL"] . " 304 Not Modified");
			return true;
		}
	}

	
	header("X-Sendfile: $path");
	if($type == 'img')
		header("Content-Type: image/png");
	elseif($type == 'css')
		header("Content-Type: text/css");
	elseif($type == 'js')
		header("Content-Type: text/javascript");
	elseif($type == 'xml')
		header("Content-Type: text/xml");
	elseif($type == 'html')
		header("Content-Type: text/html");
	elseif($type == 'txt')
		header("Content-Type: text/txt");
		
	header('Content-Disposition: inline; filename=' . basename($file));
	header("Cache-Control: max-age=31536000");  //one year from now
	//header("Expires: " . date(DATE_RFC822, time() + 31536000) ); //Wed, 24 Nov 2004 11:55:45 GMT
	header("Last-Modified: " .date(DATE_RFC822, $mtime) . ' GMT');		

	return true; //RETURN TRUE IF SUCCESSFUL
}

function prep_file($url,$img_mirror = false){ // make this true when we go live (false means don't use the img.ee mirror)
	$f = basename_extension_remove_timestamp($url);
	$type = get_type($f[1]);
	$path = make_path(implode('.',$f), $type);
	if(!file_exists($path)){
		trigger_error("-- PREP FILE: File doesn't exist, returning /404 : $path"  . (isset($_SERVER['HTTP_REFERER']) ? ' -- Referrer: ' . $_SERVER['HTTP_REFERER'] . ' --' : ' --'));
		return "/404";
	}
	global $image_file_mirror;
	if($img_mirror)
		return rtrim($image_file_mirror,'/') . '/' . $type . '/' . $f[0] . '.' . filemtime($path) . '.' . $f[1];
	else
		return '/' . $type . '/' . $f[0] . '.' . filemtime($path) . '.' . $f[1];
}
