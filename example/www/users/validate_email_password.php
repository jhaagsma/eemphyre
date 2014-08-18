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

All files are licensed under the MIT License.

First release, September 3, 2012
---------------------------------------------------*/

/**
--> BORROWED FROM http://www.linuxjournal.com/article/9585
Validate an email address.
Provide email address (raw input)
Returns true if the email address has the email 
address format and the domain exists.
*/
function valid_email($email){
	$isValid = true;
	$atIndex = strrpos($email, "@");
	if(is_bool($atIndex) && !$atIndex){
		$isValid = false;
	}else{
		$domain = substr($email, $atIndex+1);
		$local = substr($email, 0, $atIndex);
		$localLen = strlen($local);
		$domainLen = strlen($domain);
		if ($localLen < 1 || $localLen > 64){
			// local part length exceeded
			$isValid = false;
		}elseif ($domainLen < 1 || $domainLen > 255){
			// domain part length exceeded
			$isValid = false;
		}elseif ($local[0] == '.' || $local[$localLen-1] == '.'){
			// local part starts or ends with '.'
			$isValid = false;
		}elseif (preg_match('/\\.\\./', $local)){
			// local part has two consecutive dots
			$isValid = false;
		}elseif (!preg_match('/^[A-Za-z0-9\\-\\.]+$/', $domain)){
			// character not valid in domain part
			$isValid = false;
		}elseif (preg_match('/\\.\\./', $domain)){
			// domain part has two consecutive dots
			$isValid = false;
		}elseif(!preg_match('/^(\\\\.|[A-Za-z0-9!#%&`_=\\/$\'*+?^{}|~.-])+$/', str_replace("\\\\","",$local))){
			// character not valid in local part unless 
			// local part is quoted
			if (!preg_match('/^"(\\\\"|[^"])+"$/',str_replace("\\\\","",$local))){
				$isValid = false;
			}
		}
	
		if($isValid && !(checkdnsrr($domain,"MX") || checkdnsrr($domain,"A"))){
			// domain not found in DNS
			$isValid = false;
		}
	}
	
	return $isValid;
}

function good_password($pwd){
	if(strlen($pwd) < 8){
		return false;
	}elseif(!(preg_match("/[A-z]/",$pwd) && preg_match("/[0-9]/",$pwd))){ // && preg_match("/[^A-Za-z0-9]/",$pwd) //add symbols if you want
		return false;
	}
	return true;
}
