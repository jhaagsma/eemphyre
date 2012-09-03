<?php

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
