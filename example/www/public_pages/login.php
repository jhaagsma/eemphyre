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


function login($data,&$path,&$user){
	if($data['error'])
		echo "ERROR ", $data['error'];
		
	echo <<<END
			<br />
			You can login with the test administrator account to examine the debug functionalities &etc; or you can register an account and login.<br />
			username: administrator password: hellothere11
			<br />
			<br />
			<form action='/login' method='post'>
			<table style='margin-lefT:auto;margin-right:auto;text-align:left;width:200px;'>
				<tr>
					<td colspan='2'>
						<h1>Log In</h1>
					</td>
				</tr>
				<tr>
					<td class='login_title' align='right'>
						USERNAME
					</td>
					<td class='login_input'>
						<input type='text' name='username' class='login_box' />
					</td>
				</tr>
				<tr>
					<td class='login_title' align='right'>
						PASSWORD
					</td>
					<td class='login_input'>
						<input type='password' name='password' id='password' class='login_box' />
					</td>
				</tr>
				<tr>
					<td colspan='2' align='center'>
						<input type='submit' value='Login' class='login_button' name='Login' />
					</td>
				</tr>
			</table>
		</form>
		<br />
		Don't have an account? <a href='/register'>Register!</a>
END;
}

function loginfn($data,$path,$user){
	global $db, $extra_salt;
	

	$time=time();
	$IP = $_SERVER['REMOTE_ADDR'];

	$user = $db->pquery("SELECT userid, salt, password, disabled FROM users WHERE username = ?", $data['username'])->fetchrow();

	if(!isset($user['userid'])){
		$db->pquery("INSERT INTO login_attempts SET time = ?, username = ?, userip = ?, success = ?", $time, $data['username'], $IP, false);
		redirect("/login?error=1");
	}
	elseif(hash('sha512',$data['password'] . $user['salt'] . $extra_salt) != $user['password']){
		$db->pquery("INSERT INTO login_attempts SET time = ?, username = ?, userip = ?, success = ?", $time, $data['username'], $IP, false);
		redirect("/login?error=2");
	}	
	elseif($user['disabled']){
		$db->pquery("INSERT INTO login_attempts SET time = ?, username = ?, userip = ?, success = ?", $time, $data['username'], $IP, false);
		redirect("/login?error=3");
	}

	
	include_once('./users/user_class.php');
	$user = new User($user['userid'],true); //true forces clear cache
	if(!$user)
		redirect("/login?error=4");	//this should not be possible
		
	$user->logintime = $time;
	$user->last_ip = $IP;
	$user->commit();
	
	$sessiontime = 3600;
	$key = md5(rand());
	$expire = $time+$sessiontime;
	$expireday = $time+24*3600;
	
	//new activesession && logins && add loginattempt
	$db->pquery("UPDATE active_sessions SET expiretime = ? WHERE userid = ?", $time-300,$user->userid);
	
	include_once('./users/sessions.php');
	clear_old_sessions(true);
	
	$session_id = $db->pquery("INSERT INTO active_sessions SET userid = ?, `key` = ?, userip = ?, expiretime = ?, logintime = ?", $user->userid, $key, $IP, $expire, $time)->insertid();
	
	$user->last_ip = $IP;
	$user->lastlogin = $time;
	$user->commit();
	
	$db->pquery("INSERT INTO login_attempts SET time = ?, username = ?, userip = ?, success = ?", $time, $data['username'], $IP, true);
	
	setcookie("framework_example", "{$user->userid}:$key", $expireday, "/", $_SERVER['SERVER_NAME'], false, true);
	redirect('/internal');
}

function logout($data,$path,$user){
	global $db;
	$cookiebits = explode(":", getCOOKIEval("framework_example"));
	
	include_once('./users/sessions.php');
	clear_active_session($cookiebits);
	$IP = $_SERVER['REMOTE_ADDR'];
	$clearid = $db->pquery("SELECT userid, logintime FROM active_sessions WHERE userid = ? and `key` = ? LIMIT 1", $cookiebits[0], $cookiebits[1])->fetchrow();
	//clear the cookie
	setcookie("framework_example",'',1);

	if(!$clearid['userid'])
		redirect('/login');
	
	$db->pquery("DELETE FROM active_sessions WHERE userid = ? and `key` = ? LIMIT 1", $clearid['userid'], $cookiebits[1]);

	redirect('/login');
}

