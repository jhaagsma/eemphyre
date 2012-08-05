<?


function do_user_check($sessiontime){
	clear_old_sessions();
	if(!($cookiebits = getCOOKIEval('ee_choc_cookie'))) //THIS IS ASSIGNING COOKIE VAL, SO SINGLE EQUALS IS CORRECT
		return null;

	$cookiebits = explode(":", $cookiebits);
	if(!($activerow = active_session($cookiebits))) //THIS IS ASSIGNING ACTIVE ROW, SO SINGLE EQUALS IS CORRECT
		return null;
	
	$currentip = explode('.',$_SERVER['REMOTE_ADDR']);
	$sessionip = explode('.',$activerow['userip']);	
	
	if(!((time() < $activerow['expiretime']) && ($currentip[0] == $sessionip[0]) && ($currentip[1] == $sessionip[1])))
		return null;
	
	$expiretime = time()+$sessiontime;
	update_active_session($expiretime, $activerow);
	
	$userid = $cookiebits[0];
	return new User($userid);  //Create a User class!
}

function auth_public($data,$path,$user){
	return false;
}

function auth_login($data,$path,$user){
	if(!$user)
		redirect('/login?error=7');
		
	return false;
}

function auth_admin($data,$path,$user){
	auth_login($data,$path,$user); //this will now auto-redirect
	
	if(!$user->administrator)
		redirect('/portal?error=6');

	return false;
}

function auth_admin_server($data,&$path,&$user){
	auth_admin($data,$path,$user); //this will now auto-redirect
	return auth_server_public($data,$path,$user);
}

function auth_server_public($data,&$path,$user){
	if(!isset($path->variables['server']))
		redirect('/portal?error=1');
	$server = explode('-',$path->variables['server'],2);
	if(!is_a_server($server[0]))
		redirect('/portal?error=1');

	include_once('./include/server_class.php');
	$multi = def($server[1],null);
	return new GameServer($server[0],(isset($server[1]) ? $server[1] : null)); //return Server Object as auth_opt
}

function auth_server($data,&$path,&$user){
	auth_login($data,$path,$user); //this will now auto-redirect
	return auth_server_public($data,$path,$user); //return Server Object as auth_opt
}

function get_cid($data,$path,$user,&$server){
	if(!$server->multi_allowed)
		return $server->get_current_countryid($user);

	if(!$server->multi)
		redirect($server->make_url($user->get_default_mainffa()) . '?error=1');
		
	return get_countryid_from_multinum_ghost($server->multi, $user->userid, $server->serverid);
}

// only sane way to do kmsg... check if country is dead, but kmsg=0
// if so, redirect user to page displaying news, etc, and update kmsg=1
// if kmsg=1, kick player out of page
function auth_country($data,&$path,&$user,&$gc){ //$gc is null when it comes in, we fill it in here -- because it's by reference we pass it on
	$server = auth_server($data,$path,$user); //this will now auto-redirect
	$countryid = get_cid($data,$path,$user,$server); //this will now auto-redirect
	captcha($data,$path,$user,$server); //this will now auto-redirect
	
	if(server_not_started($serverid))
		redirect($server->make_url('/notstarted'));
	
	if(server_ended($serverid))
		redirect($server->make_url('/ended'));

	include('./game_engine/game.php');
	optional_logging();

	global $db;
	if(!$server->locked && !$db->pquery("SELECT GET_LOCK(?,1)",'cid' . $server->cid)->fetchfield())
		redirect($server->make_url('/toofast'));
		
	$server->locked = true;
	
	$gc = new Game($countryid,$server,$user);
		
	$path->variables['show_tutorial'] = $gc->country['show_tutorial'];

	$bonus = $gc->do_land_turns_marketreset(); //I kindof want to merge this into the Game construct
	
	$gc->init_topbar($path);
	
	if(!$gc->is_alive()) // this is a ghost country, so send it to main page and set kmsg=1
		redirect($server->make_url('/main?ghost=1'));
	
	if($gc->in_vacation()){ // shouldn't we put this in the cache?
		if($path->url != '/' . $server->server_name . '/leavevacation'){
			if(!$server->multi_allowed)
				redirect($server->make_url('/preferences?error=1'));
			else
				redirect($server->make_url('/control/war/vacation'));
		}
	}
	
	if($bonus)
		redirect($server->make_url('/main?bonus=1'));

	ignore_user_abort(true); // Ignore user aborts and allow the script to run forever
		
	return $server;
}

function captcha($data,&$path,$user,$server){ //auth_captcha($user);
	$captchatime = 30*3600;
	if($user->lastcaptcha + $captchatime < time()){
		if(!get_attacking_for_ad($server->cid))
			redirect($server->make_url('captcha/show'));
	}
	return false;
}

function auth_mod($data,&$path,&$user){ //$mc is returned as auth_opt2
	auth_login($data,$path,$user);

	if(!$user->is_mod())
		redirect('/portal?error=7');

	return;
}

function auth_mod_server($data,&$path,&$user,&$mc){ //$mc is returned as auth_opt2
	$server = auth_server_public($data,$path,$user); //need to make sure they have a server set...
	auth_mod($data,$path,$user); //check if they're a mod
	if(!$user->can_mod($server->serverid)) //check if they can mod THIS server...
		redirect('/portal?error=17');

	include_once("./moderator/moderator_class.php");
	$mc = new ServerMod($server);
	return $server; //this will now return $server as auth_opt
}

//make sure the requests are AJAX requests
function auth_country_ajax($data,&$path,$user){
	$server = auth_country($data,$path,$user); //this will now auto-redirect
	
	if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || ($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest'))
		redirect($server->make_url('/main'));
	
	return $server;
}

function auth_clans($data,&$path,$user){
	$server = auth_country($data,$path,$user); //this will now auto-redirect

	if(!$server->clans_allowed)
		redirect($server->make_url('/main'));
		
	return $server;
}

function auth_bot($data,&$path,$user){
	redirect("/portal"); // for now redirect to portal, because we have no auth_bot stuff 
}

/// v---- FUNCTIONS WHICH NEED CLEANING STILL ----v // preferably remove globals




/* THIS PART MAY NEED TO BE REWRITTEN TO USE THE NEW User Class

//auth bot doesn't deal with a template, so any errors should be 
//returned with a message or error object and should exit immediately
//the ai_api handles whether or not the request can be handled
function auth_bot($data,&$path,$user){
	global $db;
	//check if the bot is authorized
	$ai_user = get_ai_user($data['key']);
	if(!$ai_user){
		echo "AI Key is not valid!";
		exit;
	}
	
	//get the user associated with this bot and set the global $userid
	//the user account is used to associate
	global $userid;
	$userid = get_userid_by_botid($ai_user['keyid']);
	
	//log the request
	//not much for dev phase
	$db->pquery("UPDATE ai_keys SET lastaccess=? WHERE ai_key=?",time(),$data['key']);
	
	return false;
	
}*/



/*function checkredirect(){  //THIS FUNCTION IS NOW OBSOLETE
	$server_name = explode('.',$_SERVER['SERVER_NAME']);
	if(is_a_server($server_name[0])){
		unset($server_name[0]);
		$server_name = implode('.',$server_name);
		header("location: http://$server_name/portal");	
		return true;
	}
	return false;
}*/

/*function optional_logging(){  //I REALLY DON'T LIKE THIS ONE BUT IF WE EVER WANT TO DO LOGGING THEN......
	global $db, $loginid;
	global $LOG_ALL_INGAME,$LOG_AVERAGE_CLICK_RATE; //THESE ARE SET IN index.php
	
	if($LOG_ALL_INGAME == true){
		if(isset($_SERVER['HTTP_REFERER']))
			$referrer = str_replace('qzee2.evolution2025.com','q2',str_replace('qz.earthempires.com','q',str_replace('pangaea.earthempires.com','p',str_replace('slagpit.earthempires.com','s',str_replace('www.earthempires.com','LIVE',str_replace('earthempires.evolution2025.com','ALT',str_replace("http://",null,$_SERVER['HTTP_REFERER'])))))));
		else
			$referrer = "undefined";
	
		$stuff = null;
		if($_POST){
			foreach($_POST as $k => $v)
				$stuff .= "$k=>$v,";
		}
		$db->pquery("INSERT INTO log_pageloads SET time=?,userid=?,resetid=?,countryid=?,sessionid=?,getpost=?,self=?,referrer=?,query_string=?,_post=?",
			time(),$userid, apc_serverline($serverid, 'cur_resetid'), $cid, $loginid, ($_SERVER['REQUEST_METHOD'] == "GET" ? 1 : ($_SERVER['REQUEST_METHOD'] == "POST" ? 2 : 3)), $_SERVER['PHP_SELF'], $referrer, $_SERVER['QUERY_STRING'], $stuff);
	}
	
	if($LOG_AVERAGE_CLICK_RATE == true){
		$microtime = microtime(true);
		$previous_microtime = $db->pquery('SELECT last_microtime FROM users WHERE userid = ?', $userid)->fetchfield();
		$difference = $microtime - $previous_microtime;
		if($difference > 600) //ten minutes
			$db->pquery('UPDATE users SET last_microtime = ? WHERE userid = ?', (double)$microtime, $userid);
		else
			$db->pquery('UPDATE users SET last_microtime = ?, ingame_log_count = ingame_log_count + 1,
			ingame_log_s = ingame_log_s + (? - ingame_log_mean) * (? - ingame_log_mean + (? - ingame_log_mean) / ingame_log_count),
			ingame_log_mean = ingame_log_mean + (? - ingame_log_mean) / ingame_log_count,
			ingame_log_std_dev = SQRT(ingame_log_s / (ingame_log_count - 1))
			WHERE userid = ?', (double)$microtime, $difference, $difference, $difference, $difference, $userid);
	}
	return;
}*/
