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
class User{
	public $userid;

	function __construct($userid,$clearcache=false){
		$this->userid = $userid;
		if($clearcache)
			$this->apc_del_userline();
		
		$this->get_values();
	}
	
	function get_values(){
		$this->ul = $this->apc_userline();
		if(!$this->ul){
			$this->userid = null;
			return;
		}
		unset($this->ul['userid']);
		array_to_obj_vals(&$this, $this->ul);
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

		if($partcount != $checkcount)  //this should never happen, but we should check regardless
			return false;

		$row = $db->pquery_array($call_args);

		if($row->affectedrows()){
			$this->refresh_values($this->userid);
			return true;
		}
		else
			return false;	
	}
	
	private function apc_userline($column=null){ //deliberately errors if column isn't in the array
		global $cache, $db;
		$user_line = $cache->fetch('ul:'.$this->userid);
		if(!$user_line){
			$user_line = $db->pquery('SELECT * FROM users WHERE userid = ?',$this->userid)->fetchrow(); //this shouldn't select *, but for the example I'll do that, given I haven't built a db for it
			$cache->store('ul:'.$this->userid, $user_line, 1800);
		}
		return ($column ? $user_line[$column] : $user_line); 
	}

	private function apc_del_userline(){
		global $cache;
		$cache->delete('ul:'.$this->userid);
	}
	
}
