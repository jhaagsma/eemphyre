<?

class User{
	public $userid;
	public $mc;
	
	//template stuff
	public $header_menu_scroll, $ad_style;
	
	function __construct($userid,$clearcache=false){
		$this->userid = $userid;
		if($clearcache)
			apc_del_userline($this->userid);
		
		$this->get_values();
	}
	
	function get_values(){
		$this->ul = apc_userline($this->userid);//this is temporary until we integrate/clean things up
		if(!$this->ul){
			$this->userid = null;
			return;
		}
		unset($this->ul['userid']);
		array_to_obj_vals(&$this, $this->ul);
		
		$this->handles = apc_get_handles($this->userid);
		
		if($this->default_handle)
			$this->display = $this->handles[$this->default_handle]['handle'];
		
		$this->mc = null;
	}
	
	function refresh_values(){
		$this->apc_del_userline();
		$this->get_values();
	}
	
	function commit(){
		global $db;
		unset($this->ul['userid']);
		$partcount = 0;
		$partsA = $partsB = array();
		foreach($this->ul as $k => $v){
			if($v != $this->$k){
				$partsA[] = "$k = ?";
				$partsB[] = $this->$k;
				$partcount++;
			}
		}
		//check if there are things to update
		if(count($partsA)==0)
			return false;

		$query = "UPDATE users SET " . implode(", ", $partsA) . " WHERE userid = ?";
		$call_args = array();
		$call_args[] = $query;
		$checkcount = 0;
		foreach($partsB as $p){
			$call_args[] = $p;
			$checkcount++;
		}
		$call_args[] = $this->userid;

		if($partcount != $checkcount)
			return false;

		$row = $db->pquery_array($call_args);

		if($row->affectedrows()){
			$this->refresh_values($this->userid);
			return true;
		}
		else
			return false;	
	}
	
	function is_alpha(){
		return $this->alpha_servers;
	}
	
	function is_mod(){
		return is_mod($this->userid); //this is an APC function probs?
	}
	
	function is_mod_array(){
		if(!isset($this->mod_array))
			$this->mod_array = is_mod_array($this->userid); //this is an APC function probs?
		return $this->mod_array;
	}
	
	function can_mod($serverid){
		return in_array($serverid, $this->is_mod_array());
	}
	
	function is_facebook(){ //stub
		return false;
	}
	
	function get_default_mainffa(){
		switch($this->ffa_style){
			case(0): return 'control';
			case(1): return 'control/economy';
			case(2): return 'control/war';
			case(3): return 'control/custom';
			default: return 'control';
		}
	}
	
	function get_value_from_mainffa($page){
		switch(ltrim($page,'/')){
			case('control'):			return 0;
			case('control/economy'):	return 1;
			case('control/war'):		return 2;
			case('control/custom'):		return 3;
			default:					return 0;
		}
	}
	
	function get_default_mainffa_link(){
		return '/' . $this->get_default_mainffa();
	}	
	
	function min_time_for_bonus(){
		return (time() > 86400 + min_time_for_bonus($userid) ? true : false);
	}
	
	function get_all_countryids(){
		global $db;
		return $db->pquery('SELECT countryid FROM country_owners WHERE userid = ?', $this->userid)->fetchfieldset();		
	}
	
	function bonus_available(){
		return 86400 + min_time_for_bonus($this->userid);
	}
	
	function is_new(){
		return (check_country_count($this->userid)==0 ? true : false);
	}

	function get_email_values(){
		global $db;
		$email_prefs = $db->pquery('SELECT email_rank, email_inactive, email_announcements FROM users WHERE userid=?', $this->userid)->fetchrow();
		array_to_obj_vals(&$this, $email_prefs);
		foreach($email_prefs as $name => $value) //THIS IS TO ALLLOW COMMITTING ABOVE! YOU MUST STORE AT LEAST NAMES OF VARIABLES TO BE COMMITTED!!
			$this->ul[$name] = $value;  //though we could just list them somewhere i suppose; or do a quick db call before we commit
	}
	
	///This is bad in terms of organization (imho anyway) but i'll put it here because we intend to somehow merge the inboxes at some point...
	function has_new_messages($cid){
		return get_inbox($cid);
	}	


	//APC functions //these are temporary until we integrate/clean things up
	function delete_user_values_this_server(&$server){ //this maybe should go in the GameServer class....
		delete_user_values_this_server($this->userid,$server->serverid);
	}
	
	function apc_del_userline(){
		apc_del_userline($this->userid); 
	}
	
	
}
