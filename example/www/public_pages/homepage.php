<?
//homepage for the example site
function homepage($data,&$path,&$user){ //portal has no server
	echo <<<END
	This is the homepage!
	<p>This site is an extremely basic example which is a starting point from which to build a website using the empiresPHPframework.  See the github link for the code.</p>
	<p>It also attempts to showcase some of the routers <a href='/router_test'>data handling</a></p>
	<p><a href='/login'>Login page!</a></p>
	<p><a href='/register'>Register page!</a></p>
END;
	echo '<p>You are ', ($user ? ' logged in as ' . $user->displayname : 'not logged in'), '!</p>';

echo <<<END
	<br /><br /><br />
	<p><a href='/404'>Custom 404 page!</a></p>
	<p><a href='/internal'>Internal Landing page!</a> <span style="font-size:10px">(redirects with appropriate headers & displays error message if you're not logged in)</span></p>
END;
}
