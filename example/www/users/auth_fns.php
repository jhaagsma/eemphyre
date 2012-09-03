<?

function do_user_check($sessiontime){
	include_once('./users/sessions.php');
	clear_old_sessions();
	if(!($cookiebits = getCOOKIEval('framework_example'))) //THIS IS ASSIGNING COOKIE VAL, SO SINGLE EQUALS IS CORRECT
		return null;

	$cookiebits = explode(":", $cookiebits);
	if(!($activerow = active_session($cookiebits))) //THIS IS ASSIGNING ACTIVE ROW, SO SINGLE EQUALS IS CORRECT
		return null;
	
	$currentip = explode('.',$_SERVER['REMOTE_ADDR']);
	$sessionip = explode('.',$activerow['userip']);	
	
	if(!((time() < $activerow['expiretime']) && ($currentip[0] == $sessionip[0]) && ($currentip[1] == $sessionip[1])))  //this requires 
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
		redirect('/internal?error=6');

	return false;
}
