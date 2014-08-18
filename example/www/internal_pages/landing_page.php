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
function landing_page($data,&$path,&$user){
	echo <<<END
This is a landing page!<br />
<a href='/internal/users'>Look at Users Page!</a>
END;

echo ($user->administrator ? "<p><a href='internal/admin'>Administrators Area!</a></p>" : null);

	echo <<<END
<br /><br />
<a href='/router_test'>Router Test!</a><br /><br />
<a href='/'>Go Home!</a><br /><br />
<a href='/logout'>Logout!</a>

END;
}


function users($data,&$path,&$user){
	global $db;
	$showusers = $db->query("SELECT userid, displayname, registered, logintime FROM users LIMIT 0, 30")->fetchrowset();
	
	echo "This Page shows up to the first 30 users, as an example; pagination is left as an exercise to the dev...";
	echo "<table><tr><th>Display Name</th><th>Registered</th><th>Last Login</th></tr>\n";
	foreach($showusers as $u){
		echo '<tr><td><a href="/internal/users/', $u['userid'], '">', $u['displayname'], '</a></td><td>', date('M d Y', $u['registered']), '</td><td>', datetime($u['logintime']), "</td></tr>\n";
	}
	echo "</table>";
	echo '<br /><br /><a href="/internal">Back to Internal</a>';
}

function userpage($data,&$path,&$user){
	$puser = make_puser($path->variables,'userid',$user); //in general.php -- THIS CHECKS TO SEE IF $path->variables['id'] exists, and if it does, returns a user object for it
	
	echo "User ", $puser->displayname, " registered on ", date('M d Y', $puser->registered), " and last logged in ", datetime($puser->logintime), '.';
	echo '<br /><br /><a href="/internal/users">Back to Users Page</a>';
}


function admin($data,&$path,&$user){
	echo <<<END
This is the admin page!
<a href='/internal/admin/show_routing_tree'>Show Routing Tree!</a>
<br />
<br />
<br />
<a href='/internal'>Go to Internal!</a>

END;

}
