<?

//--These Functions Are Basic Functions Necessary For Routing & Basic Stuff

function redirect($loc){
	header("Location: $loc", true, 303);
	echo "Redirecting to: <a href='" . htmlentities($loc) . "'>$loc</a>";
	exit;
}

function moved($loc){
	header("Location: $loc", true, 301);
	echo "Redirecting to: <a href='" . htmlentities($loc) . "'>$loc</a>";
	exit;
}

function get_url_part($token_num){
	$parts = explode(".",$_SERVER['SERVER_NAME']);
	if($token_num<0)
		return def($parts[count($parts)+$token_num],false);
	else
		return def($parts[$token_num],false);
}

function getCOOKIEval($name, $type = 'string', $default = null){
	$var = (isset($_COOKIE[$name]) ? $_COOKIE[$name] : $default);
	settype($var, $type);
	return $var;
}


//FUNCTIONS FOR CLASSES

function array_to_obj_vals(&$obj, $array = array()){
	foreach($array as $key => $value)
		$obj->$key = $value;
	
	return;
}


//MAKE USER FUNCTION => This is for creating a User object for a user OTHER THAN the currently logged in user; ie, looking at a different users profile

function make_puser($var,$index,$user){ //don't need to pass by user, as we want to return a copy regardless
	if($index)	
		$id = (isset($var[$index]) ? $var[$index] : false);
	else
		$id = $var;
	return ($id ? new User($id) : ( $user ? $user : new User(null) ));
}

//TIME DISPLAY FUNCTIONS

function datetime($time){
	$timediff = time()-$time;

	if($timediff < 3600)
		$date = round($timediff/60,1) . " mins ago";
	elseif($timediff < 3600*24)
		$date = round($timediff/3600,1) . " hours ago";
	elseif($timediff < 3600*24*5)
		$date = round($timediff/(3600*24),1) . " days ago";
	else
		$date = date("M d, G:i",$time);

	return $date;
}

function timefromnow($time){
	$timediff = $time-time();
	if($timediff == 0)
		$date = "Now";
	elseif($timediff == 1)
		$date = $timediff . " second";
	elseif($timediff < 60)
		$date = $timediff . " seconds";
	elseif($timediff < 3600)
		$date = round($timediff/60,1) . " mins";
	elseif($timediff < 3600*24)
		$date = round($timediff/3600,1) . " hours";
	elseif($timediff < 3600*24*7)
		$date = round($timediff/(3600*24),1) . " days";
	else
		$date = date("M/d/y G:i",$time);
	
	return $date;
}

function timefromnow_past($time){
	$timediff = time()-$time;
	if($timediff == 1)
		$date = $timediff . " second";
	elseif($timediff < 60)
		$date = $timediff . " seconds";
	elseif($timediff < 3600)
		$date = round($timediff/60,1) . " mins";
	elseif($timediff < 3600*24)
		$date = round($timediff/3600,1) . " hours";
	elseif($timediff < 3600*24*7)
		$date = round($timediff/(3600*24),1) . " days";
	else
		$date = date("M/d/y G:i",$time);
	
	return $date;
}

function codetime($time_start,$detail=false){
	if($detail)
		return (microtime(true) - $time_start)*1000;
	else
		return ceil((microtime(true) - $time_start)*1000);
}

//OTHER FUNCTIONS

function cmp($a, $b){
	global $compon, $asc;
	if ($a[$compon] == $b[$compon])
		return 0;
	if($asc)
		return ($a[$compon] < $b[$compon] ? -1 : 1);
	else
		return ($a[$compon] < $b[$compon] ? 1 : -1);
}

function accept_gzip(){
	if(!isset($_SERVER['HTTP_ACCEPT_ENCODING']))
		return false;
	if(substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip'))
		return true;
	
	return false;
}

//if column is null, collapses a multidim array down to a single dim one
function copy_col_of_multidim_array($array, $column=null){
	$ret = array();
	if($column === null){
		foreach($array as $key=>$row)
			foreach($row as $r)
				$ret[] = $r;
	}
	else{
		foreach($array as $key=>$row)
			if(isset($row[$column]))
				$ret[$key] = $row[$column];
	}
	return $ret;
}


