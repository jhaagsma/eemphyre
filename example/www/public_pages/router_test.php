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

All files are licensed under the GPLv2.

First release, September 3, 2012
---------------------------------------------------*/



include_once('./include/dBug.php');
	//router_test.php
function start($data){
	echo<<<END
<a href='/router_test/basic'>Basic Test</a><br /><br />
<a href='/router_test/defaults'>Defaults Test</a><br /><br />
<a href='/router_test/arrays1'>Simple Array Test</a><br /><br />
<a href='/router_test/arrays2'>2D Array Test</a><br /><br />
<a href='/router_test/arrays3'>Multidim Array Test</a><br /><br />


<br />
<br />
<br />

<a href='/router_test'>Go Back to the Router Test!</a><br /><br />
<a href='/'>Go Back to the Homepage!</a><br /><br />
END;
}

function basic($data){

echo <<<END
This is a test page!<br />
<br />
<pre>
The inputs are defined as follows:

'test_u_int' =>'u_int',
'test_int' =>'int',
'test_bool' =>'bool',
'test_float' =>'float',
'test_double' =>'double',
'test_string' =>'string'
</pre>	

Basic Test<br />
<form action='/router_test/basic' method='post'><br />
test u_int	<br /><input type='text' name='test_u_int' value='-3'><br />
test int	<br /><input type='text' name='test_int' value='3.1'><br />
test bool	<br /><input type='text' name='test_bool' value='1'><br />
test float	<br /><input type='text' name='test_float' value='hello'><br />
test double	<br /><input type='text' name='test_double' value='4.54454'><br />
test string	<br /><input type='text' name='test_string' value='hi hi'><br />
not listed	<br /><input type='text' name='not_listed' value='this should not come through'><br />
<input type='submit'><br />

</form><br />
<br />
<br />
<br />
<br />

<a href='/router_test'>Go Back to the Router Test!</a><br /><br />
<a href='/'>Go Back to the Homepage!</a><br /><br />
END;
}

function basicfn($data){
	new dBug($data);  //this is from http://dbug.ospinto.com/ and is highly recommended for debugging
	echo "<a href='/router_test'>Go Back to the Router Test!</a><br /><br /><br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />";
}


function defaults($data){

echo <<<END

<pre>
The inputs are defined as follows:

'test_u_int' =>array('u_int',1337),
'test_int' =>array('int',-1337),
'test_bool' =>array('u_int',false),
'test_float' =>array('float',3.1415926),
'test_double' =>array('double',3.14159261415926141592614159261415926),
'test_string' =>array('string','kitchen sink is 3.141')
</pre>	

Defaults Test1<br />
<form action='/router_test/defaults' method='post'><br />
test_u_int	<br ><input type='text' name='test_u_int'><br />
test_int	<br ><input type='text' name='test_int'><br />
test_bool	<br ><input type='text' name='test_bool'><br />
test_float	<br ><input type='text' name='test_float'><br />
test_double	<br ><input type='text' name='test_double'><br />
test_string	<br ><input type='text' name='test_string'><br />
<input type='submit'><br />
</form><br />

<a href='/router_test'>Go Back to the Router Test!</a><br /><br />
<a href='/'>Go Back to the Homepage!</a><br /><br />
END;
}

function defaultsfn($data){
	new dBug($data);
	
	echo "<a href='/router_test'>Go Back to the Router Test!</a><br /><br /><br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />";
}



function arrays1($data){

echo <<<END
<pre>
The inputs are defined as follows:
'testarray2'	=>array('array',null,'double'),
'testarray2a'	=>array('array',13,'int'),
'testarray2b'	=>array('array',null,array('int',136),'u_int')
</pre>	

Simple Arrays<br />
<form action='/router_test/arrays1' method='post'><br />
testarray2<br />
testarray2[]		<input type=text name=testarray2[] value='text!'><br />
testarray2[]		<input type=text name=testarray2[] value='2'><br />
testarray2[]		<input type=text name=testarray2[] value='3.1'> <br />
testarray2[]		<input type=text name=testarray2[]><br />
testarray2[]		<input type=text name=testarray2[] value='3.1'><br />
<br />
<br />
testarray2a<br />
testarray2a[a]		<input type=text name=testarray2a[a] value='text!'><br />
testarray2a[b]		<input type=text name=testarray2a[b] value='2'><br />
testarray2a[c]		<input type=text name=testarray2a[c] value='3.1'><br />
<br />
<br />
testarray2b<br />
testarray2b[2.0]	<input type=text name=testarray2b[2.0] value='text!'><br />
testarray2b[3]		<input type=text name=testarray2b[3] value='2'><br />
testarray2b[4.1]	<input type=text name=testarray2b[4.1] value='3.1'><br />
testarray2b[5]		<input type=text name=testarray2b[5] value='3.1'><br />
<br />
<input type='submit'><br />
</form><br />
<a href='/router_test'>Go Back to the Router Test!</a><br /><br />
<a href='/'>Go Back to the Homepage!</a><br /><br />
END;
}

function arrays1fn($data){
	new dBug($data);
	echo "<a href='/router_test'>Go Back to the Router Test!</a><br /><br /><br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />";
}



function arrays2($data){

echo <<<END
<pre>
The inputs are defined as follows:
		'testarray3'	=>array('array',null,array('array','blah','string')),
		'testarray3a'	=>array('array',null,array('array',3,'int'))
</pre>	

2D Arrays<br />
<form action='/router_test/arrays2' method='post'><br />
<br />
testarray3<br />
testarray3[0][text]	<input type=text name=testarray3[0][text] value='Text for a link!'><br />
testarray3[1][text]	<input type=text name=testarray3[1][text] value='Text for a different link!'><br />
testarray3[2][text]	<input type=text name=testarray3[2][text] value='Stuff!'><br />
testarray3[3][text]	<input type=text name=testarray3[3][text]><br />
testarray3[4]		<input type=text name=testarray3[4] value='a!'><br />
<br />
testarray3[0][url]	<input type=text name=testarray3[0][url] value='http://url.example.com'><br />
testarray3[1][url]	<input type=text name=testarray3[1][url] value='http://example.com'><br />
testarray3[2][url]	<input type=text name=testarray3[2][url] value='somethign else'><br />
<br />
<br />

testarray3a<br />
testarray3a[0][0]	<input type=text name=testarray3a[0][0] value='text!'><br />
testarray3a[0][1]	<input type=text name=testarray3a[0][1] value='2'><br />
<br />
testarray3a[1][0]	<input type=text name=testarray3a[1][0] value='3.1'><br />
testarray3a[1][1]	<input type=text name=testarray3a[1][1] value='99'><br />
<br /><br />
<input type='submit'><br />
</form><br />


<a href='/router_test'>Go Back to the Router Test!</a><br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />
END;
}

function arrays2fn($data){
	new dBug($data);
	echo "<a href='/router_test'>Go Back to the Router Test!</a><br /><br /><br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />";
}



function arrays3($data){

echo <<<END

<pre>
The inputs are defined as follows:
		'testarray4'	=>array('array',null,array('array',null,array('array',null,array('array',3,'int'))))
</pre>	

2D Arrays<br />
<br />
<form action='/router_test/arrays3' method='post'><br />
<br />
testarray3<br />
END;

for($i = 0; $i < 3; $i++){
	for($j = 0; $j < 3; $j++){
		for($k = 0; $k < 3; $k++){
			for($l = 0; $l < 3; $l++){
				$rand = rand();
				echo "testarray4[$i][$j][$k][$l]<input type='text' name='testarray4[$i][$j][$k][$l]' value='$rand'><br />\n";

			}
			echo "<br />\n";
		}
		echo "<br />\n";
	}
	echo "<br />\n";
}
echo "<br />\n";


echo <<<END

<input type='submit'><br />
</form><br />
<a href='/router_test'>Go Back to the Router Test!</a><br /><br />
<br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />
		
END;
	}



function arrays3fn($data){
	new dBug($data);
	echo "<a href='/router_test'>Go Back to the Router Test!</a><br /><br /><br /><br /><a href='/'>Go Back to the Homepage!</a><br /><br />";
}
