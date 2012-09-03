<?php
	$meta_desc = (isset($path->meta_desc) ? "<meta name=\"description\" content=\"".$path->meta_desc."\" />": null);
	$innerpage_title = (isset($path->page_title) ? $path->page_title . " - empiresPHPframework" : "empiresPHPframework ADMIN AREA");

	include_once("./static/static.php");
	$internal_js = prep_file('internal_js.js');
	$internal_css = prep_file('internal_css.css');
	
	global $debug;
	$debug_info = null;
	if(true){ //demo the debug stuff
		include_once("./templates/debug_info.php");
		$debug_info = debug_info($size);
	}
	
/////////////// NOW DO THE HTML////////////////////
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=utf-8" />
	<title><?php echo $innerpage_title; ?></title>
	<?php echo $meta_desc; ?>
	<meta name='Keywords' content='empiresPHPframework, PHP, framework' />
	<link rel='stylesheet' href="<?php echo $internal_css; ?>" type='text/css' />
	<link rel='shortcut icon' type='image/x-icon' href='/favicon.ico' />
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.2.6/jquery.min.js"></script>
	<script type="text/javascript" src="<?php echo $internal_js; ?>"></script>
	<?php echo def($path->extra_header,null); ?>	
</head>
<body id='admin'>
	<div>
		<p>Admin area!</p>
		<div id='content'>
			<?php echo $body; ?>
		</div>
	</div>
	<?php echo $debug_info; ?>
</body>
</html>
