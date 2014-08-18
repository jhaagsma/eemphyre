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

class Skin{
	function __construct($skin = null, $mobile = false){
		$this->skin = $skin;
		$this->mobile = $mobile;
		$this->type = null;
		
		$this->is_homepage = false;
		$type_off = false;
		switch($skin){
			case 'homepage':
				$this->skin_group = 'public'; 
				$this->is_homepage = true;
				break;
			case 'public':
				$this->skin_group = 'public'; 
				break;
			case 'internal':
				$this->skin_group = 'internal';
				break;
			case 'admin':
				$this->skin_group = 'admin';
				$type_off = true;
				break;
			default:
				echo ob_get_clean();
				exit;
		}
		
		if(!$type_off)
			$this->type = ($mobile ? 'mobile' : 'web');
	}
	
	function file_function(){
		return ($this->type ? $this->type . '_' . $this->skin_group . ".php" : $this->skin_group . ".php");  //this returns the file of the template
	}
	
	function include_and_reset_buffer($size, $body, $ret, $data, &$path, &$user, &$auth_opt1, &$auth_opt2){
		ob_start();  //(RE)START THE OUTPUT BUFFER
		include($this->file_function());
	}
}
