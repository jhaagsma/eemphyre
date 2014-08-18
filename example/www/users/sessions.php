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

function active_session($cookiebits, $force = false){
	global $db, $cache;
	$activerow = $cache->fetch('em:as:' . $cookiebits[0] . '-' . $cookiebits[1]);

	if(!$activerow || $force){
		$activerow = $db->pquery('SELECT * FROM active_sessions WHERE userid = ? and `key` = ?', $cookiebits[0], $cookiebits[1])->fetchrow();
		if(!$activerow)
			return false;
		$activerow['lastreal'] = time();
		$cache->store('em:as:' . $cookiebits[0] . '-' . $cookiebits[1], $activerow, 120);
	}
	return $activerow;
}

function clear_old_sessions($forced = false){
	global $db,$cache;
	$not_due_yet = $cache->fetch('em:lcs');
	if(!$not_due_yet || $forced){
		$cache->store('em:lcs', true, 120);
		$clearids = $db->pquery('SELECT * FROM active_sessions WHERE expiretime < ?', time())->fetchrowset();
		foreach($clearids as $clearid){
			$cache->delete('em:as-' . $clearid['userid'] . '-' . $clearid['key']);
			$db->pquery('DELETE FROM active_sessions WHERE loginid = ?', $clearid['loginid']);
		}
		return true;
	}
	return false;
}

function update_active_session($expiretime, $activerow){
	global $db, $cache;
	$activerow['expiretime'] = $expiretime;
	if($activerow['lastreal'] + 120 < time()){
		$good = $db->pquery('UPDATE active_sessions SET expiretime = ? WHERE loginid = ?', $expiretime, $activerow['loginid'])->affectedrows();
		if($good)
			$activerow['lastreal'] = time();
		else
			$cache->delete('em:as-' . $activerow['userid'] .'-'. $activerow['key']);
	}
	$cache->store('em:as-' . $activerow['userid'] .'-'. $activerow['key'], $activerow, 120);
	return;
}

function clear_active_session($cookiebits){
	global $cache;
	$cache->delete('as-' . $cookiebits[0] . '-' . $cookiebits[1]);
}
