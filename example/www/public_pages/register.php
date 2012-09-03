<?php

if($use_recaptcha){
	include('./include/recaptchalib.php');  //this is not distributed with the empiresPHPframework; get it from http://www.google.com/recaptcha
}

include_once('./users/validate_email_password.php');

function register($data,&$path,&$user){
	if($user){
		echo "You are logged in, therefore you already have an account!!!";
		return;
	}
	
    $retstr=null;
    $path->meta_desc = "Register to user this empiresPHPframework example website!";
    $path->page_title = "Register";
	if($data['success']){
		echo "Thank you for registering! <br /><a href='/'>Back to homepage!</a>";
		return;
	}
	
	global $use_recaptcha, $recaptcha;
	$captcha = $error = null;
	if($use_recaptcha){
		foreach($recaptcha as $site => $info){
			if(stristr($_SERVER['HTTP_HOST'],$site))
				$captcha = recaptcha_get_html($info['public_key'], $error);
		}
	}
	
	$errorstr= "<span style='color:red; font-weight: bold'>";
	switch($data['error']){
		case 1: $errorstr.= "Passwords don't match"; break;
		case 2: $errorstr.= "Emails don't match"; break;
		case 3: $errorstr.= "Username already taken"; break;
		case 4: $errorstr.= "Email already registered"; break;
		case 5: $errorstr.= "Invalid Email"; break;
		case 6: $errorstr.= "Invalid Captcha Response"; break;
		case 7: $errorstr.= "Not on valid host?"; break;
		case 8: $errorstr.= "Please fill out ALL fields"; break;
		case 9: $errorstr.= "Please choose a stronger password with at least one number AND letter that is at least 6 characters long"; break; // and special (ie NOT a letter or number)
		case 10: $errorstr.= "Mail failed to send?"; break;
		case 11: $errorstr.= "Someone recently registered an account from this IP; Please wait 10 minutes and try again"; break;	
		case 12: $errorstr.= "Database error?"; break;
		case 13: $errorstr.= "You must agree to play by the rules!"; break;		
	}
	$errorstr.="</span>";

	echo <<<END
<div style=''>
    <p style='margin-left:10px;margin-right:10px;'>
        Welcome to this empiresPHPframework example website! Sign up for an account and you will be able to login!
    </p>
</div>
<div style=''>
	<p style='margin-left:10px;margin-right:10px;'>
		$errorstr
	</p>
</div>
<div id='register'>
	<form method='post' action='register'>
		<table id='register_table'>
			<tr>
				<td style=''>Username<span class='required'>*</span></td>
				<td style='width:1px'>:</td>
				<td style='width:120px'><input name="username" type="text" value="$data[username]" /></td>
			</tr>
			<tr>
			<tr>
				<td colspan='1'>Display Name</td>
				<td>:</td>
				<td><input type='text' name='displayname' value=''/></td>
			</tr>
			<tr>
				<td>Email Address<span class='required'>*</span></td>
				<td>:</td>
				<td><input name="email" type="text" id="email" value="$data[email]" /></td>
			</tr>
			<tr>
				<td>Confirm Email<span class='required'>*</span></td>
				<td>:</td>
				<td><input name="emailtwo" type="text" id="emailtwo" value="$data[emailtwo]" /></td>
			</tr>
			<tr>
				<td>Password <span style='font-size:10px'>(min 8 chars, letters & numbers)</span><span class='required'>*</span></td>
				<td>:</td>
				<td><input name="password" type="password" id="password" /></td>
			</tr>
			<tr>
				<td>Confirm Password<span class='required'>*</span></td>
				<td>:</td>
				<td><input name="passwordtwo" type="password" id="passwordtwo" /></td>
			</tr>
			<tr>
				<td>Do you agree to follow the rules?<span class='required'>*</span></td>
				<td></td>
				<td><input type='radio' name='rules' value='1' /> Yes &nbsp;&nbsp; <input type='radio' name='rules' value='0' checked='checked' /> No </td>
			</tr>
			<tr>
				<td colspan='3' style='padding-top:25px;'>
					$captcha
				</td>
			</tr>
			<tr>
				<td colspan='3' style='text-align:center;'><input name="Register" type="submit" value="Register" /></td>
			</tr>	
		</table>
	</form>
</div>
        
<br /><div style='text-align:center;'><a href='/'>Back to homepage</a></div>
END;
	return;
}

function registerfn($data,&$path){
	global $db, $use_recaptcha, $recaptcha, $can_send_mail, $extra_salt;
	$prv = "&username=$data[username]&email=$data[email]&emailtwo=$data[emailtwo]&displayname=$data[displayname]";
	if($use_recaptcha){
		foreach($recaptcha as $site => $info){
			if(stristr($_SERVER['HTTP_HOST'],$site)){
				$resp = recaptcha_check_answer($info['private_key'], $_SERVER["REMOTE_ADDR"], $data['recaptcha_challenge_field'], $data['recaptcha_response_field']);
				if(!$resp->is_valid)
					redirect($server->make_url('/register?error=6$prv'));
			
				$match = true;
			}
		}
	}
	if(!$data['username'] || !$data['password'] || !$data['passwordtwo'] || !$data['email'] || !$data['emailtwo']){
		redirect("/register?error=8$prv");
		return false;
	}elseif($data['password'] != $data['passwordtwo']){
		redirect("/register?error=1$prv");
		return false;
	}elseif($data['email'] != $data['emailtwo']){
		redirect("/register?error=2$prv");
		return false;
	}elseif(!valid_email($data['email'])){
		redirect("/register?error=5$prv");
		return false;
	}elseif(!$data['rules']){
		redirect("/register?error=13$prv");
		return false;
	}
	
	if(!good_password($data['password'])){
		redirect("/register?error=9$prv");
		return false;
	}
	
	$exists = $db->pquery("SELECT userid FROM users WHERE username = ? LIMIT 1", $data['username'])->fetchfield();
	if($exists){
		redirect("/register?error=3$prv");
		return false;
	}
	$exists = $db->pquery("SELECT userid FROM users WHERE email = ? LIMIT 1", $data['email'])->fetchfield();
	if($exists){
		redirect("/register?error=4$prv");
		return false;
	}
	
	$IP = $_SERVER['REMOTE_ADDR'];
	$recent = $db->pquery("SELECT registered FROM users WHERE last_ip = ? LIMIT 1", $IP)->fetchfield();
	if($recent + 600 > time()){
		redirect("/register?error=11$prv");
		return false;
	}
	
	$code = substr(md5(rand()),0,5);

	if($can_send_mail && !emailcode($data['email'], $code)){
		redirect("/register?error=10");
		return false;	
	}
	
	//salt
	$salt = substr(md5(uniqid('stuff', true)), 0, 8);
	$password = hash('sha512',$data['password'] . $salt . $extra_salt);
	
	$inz = $db->pquery("INSERT INTO users SET username = ?, password = ?, salt = ?, email = ?, displayname = ?, last_ip = ?, registered = ?",
		$data['username'], $password, $salt, $data['email'], $data['displayname'], $IP, time())->insertid();
	
	if(!isset($inz)){
		redirect("/register?error=12$prv");
		return false;
	}	
	
	redirect("/register?success=1");
	return false;
}


function emailcode($to,$code){
	$subject = 'empiresPHPframework Confirmation Code';
	$message = "Thank you for registering with empiresPHPframework.\r\n";
	$headers = 'From: empiresPHPframework <empiresPHPframework@example.com>';

	if(!mail($to, $subject, $message, $headers))
		return false;

	return true;
}
