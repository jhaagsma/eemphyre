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

global $errorlogging;

if($errorlogging){
	function userErrorHandler($errno, $errmsg, $filename, $linenum, $vars){
		global $base_dir, $user, $debug;
		$userid = (get_class($user) == "User" ? $user->userid : 0);
		if(error_reporting() == 0) //likely disabled with the @ operator
			return;

		$time = gmdate("M d Y H:i:s");

	// Get the error type from the error number
		static $errortype = array ( 1   => "Error",
									2   => "Warning",
									4   => "Parsing Error",
									8   => "Notice",
									16  => "Core Error",
									32  => "Core Warning",
									64  => "Compile Error",
									128 => "Compile Warning",
									256 => "User Error",
									512 => "User Warning",
									1024=> "User Notice",
									2048=> "PHP Strict",
								);
		$errlevel=$errortype[$errno];

	//Write error to log file (CSV format)
		if($errno <= 128)
			$file = "$sitebasedir/logs/errors.csv";
		elseif($errno == 256)
			$file = "$sitebasedir/logs/usererrors.csv";
		else
			$file = "$sitebasedir/logs/userwarnings.csv";

		$user = (isset($userid) ? $userid : 0);
		$ip = $_SERVER['REMOTE_ADDR'];

		if(strpos($filename, $sitebasedir) === 0)
			$filename = substr($filename, strlen($sitebasedir));

		$errmsg = preg_replace("/^(.*)\[<(.*)>\](.*)$/", "\\1\\3", $errmsg);

		$backoutput = "";

		if(function_exists('debug_backtrace')){
			$backtrace = debug_backtrace();

			//ignore $backtrace[0] as that is this function, the errorlogger
			
			for($i = 1; $i < 5 && $i < count($backtrace); $i++){ //only show 4 levels deep
				$errfile = (isset($backtrace[$i]['file']) ? $backtrace[$i]['file'] : '');
				
				if(strpos($errfile, $sitebasedir) === 0)
					$errfile = substr($errfile, strlen($sitebasedir));
				
				$line = (isset($backtrace[$i]['line']) ? $backtrace[$i]['line'] : '');
				$function = (isset($backtrace[$i]['function']) ? $backtrace[$i]['function'] : '');
				$args = (isset($backtrace[$i]['args']) ? count($backtrace[$i]['args']) : '');
				
				$backoutput .= "$errfile:$line:$function($args)";
				
				if($i+1 < count($backtrace)) //show if there are more levels that were cut off
					$backoutput .= "<-";
			}  
		}

		$str = "\"$time\",\"$_SERVER[PHP_SELF]\",\"$user\",\"$ip\",\"$filename: $linenum\",\"($errlevel)\",\"$errmsg\",\"$backoutput\"\r\n";
	
		if(isset($debug) && $debug)
			echo $str;
			
		$errfile=fopen($file,"a");
		fputs($errfile,$str);
		fclose($errfile);
		
		if($output_errors_to_irc){ //THIS PART IS ONLY FOR IF YOU HAVE THE BOT CLUSTER WORKING, TO SEND ERRORS TO IRC!
			if(strncmp($errmsg,'{NODETAIL_IRC}',14) == 0)
				$str2 = str_replace('{NODETAIL_IRC}', null, $errmsg);
			else
				$str2 = "($errlevel) $errmsg,$filename:$linenum,$_SERVER[PHP_SELF],$user,$ip\r\n";
			
			$comm = new CommClient();
			$comm->send('forward', array('type' => 'website_error', 'data' =>$str2));
		}
	//Terminate script if fatal error
		if($errno != 2 && $errno != 8 && $errno != 512 && $errno != 1024 && $errno != 2048){
			if($errorlogging >= 2 || $user && $debug)
				die("A fatal error has occured. Script execution has been aborted:<br>\n$str");
			else
				die("A fatal error has occured. Script execution has been aborted");
		}
	}

	set_error_handler("userErrorHandler");
}
