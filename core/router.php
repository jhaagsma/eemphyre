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

class PHPRouter {
	public $paths;

	function __construct($filetime = false){
		//trigger_error("{NODETAIL_IRC}Rebuilding Router Object for " . $_SERVER['SERVER_NAME']); //only for if you want Notices printed when the router is rebuilt
		$this->time = ($filetime ? $filetime : time()); //so we know if it's fresh
		//$this->paths = array("GET" => new UriPart(), "POST" =>  new UriPart());
		$this->paths = array("GET" => array(), "POST" =>  array());
		$this->clear_defaults();
	}
	
	function clear_defaults(){
		$this->area = array();
		$this->dir = null;
		$this->auth = null;
		$this->skin = null;
	}
	
	function dir_set($dir = null){
		$this->dir = rtrim($dir,'/');
	}
	
	function default_auth($auth = null){
		$this->auth = $auth;
	}
	
	function default_skin($skin = null){
		$this->skin = $skin;
	}
	
	function area_set($area = null){
		$this->area = array();
		if($area)
			$this->area_push($area);
	}

	function area_push($area){ //this function is to allow me to avoid putting in /{server=>string}/ for like 200 entries in the registry
		$this->area = array_merge($this->area,explode('/',trim($area,'/')));
	}
	
	function area_pop(){
		array_pop($this->area);
	}
    // Object version --- these are TOO SLOW!  //Leave in until git & svn tree merge...
	/*function add($type, $url, $f, $u, $i = null, $a = null, $s=null){
		if(!$this->prepends)
			$this->prepends = array('');
		foreach($this->prepends as $pre){
			$uri_parts = explode('/', ltrim($pre . $url,'/'));
			$node = new PathNode($f, $u, $i, $a, $s); //maybe array
			$this->buildbranch($uri_parts, $this->paths[$type], $node);
		}
	}
	
	function buildbranch($uri_parts, &$r, $node){
		$current = array_shift($uri_parts);
		if(!$current){ //ie we've moved to the end of the url
			if(!$r->o){
				$r->o = $node;
				return false;
			}
			else
				trigger_error("Different node already set for this path!") and die("Different node already set for this path!");
		}
		
		if($vinfo = $this->is_variable($current)){
			if(!isset($r->v))
				$r->v = new VUriPart($vinfo[1], $vinfo[2]);
			elseif($r->v->n != $vinfo[1])//this must be 1 because is_variable returns 0 for matches 1 for name and 2 for type in {name=>type}
				trigger_error("Different variable already set for this path!") and die("Different variable already set for this path!");
		
			return $this->buildbranch($uri_parts, $r->v, $node);
		}
		else{
			if(!isset($r->s[$current]))
				$r->s[$current] = new UriPart();
			
			return $this->buildbranch($uri_parts, $r->s[$current], $node);
		}
	}*/
	
	function add($type, $url, $file, $function, $inputs = null, $auth = false, $skin = false){ //Testing out array version
		$uri_parts = array_merge($this->area, explode('/', ltrim($url,'/')));
		$url = implode('/', $uri_parts);
		$file = ($this->dir && $file[0] != '.' ? $this->dir . '/' . ltrim($file,'/') : $file);
		$node = array(0=>$file, 1=>$function); //maybe array
		if($skin === false) //in other words, if they supplied no auth; if they supplied null, it should still set it as null, even if the default is not
			$skin = $this->skin;
		if($auth === false)
			$auth = $this->auth;
			
		if($skin){
			$node[2] = $inputs;
			$node[3] = $auth;
			$node[4] = $skin;
		}
		elseif($auth){
			$node[2] = $inputs;
			$node[3] = $auth;
		}
		elseif($inputs)
			$node[2] = $inputs;
		
		$this->buildbranch($uri_parts, $this->paths[$type], $node, $url);
	}
	
	function buildbranch($uri_parts, &$r, $node, $url){
		//comments on 'r': mapping
			//node aka o => 0
			//variable aka v =>1
			//static aka s =>2
			//name aka n => 3
			//type aka t => 4
		$current = array_shift($uri_parts);
		if(!$current){ //ie we've moved to the end of the url
			if(!isset($r[0])){
				$r[0] = $node;
				return false;
			}
			else{
				trigger_error("Ignoring Branch!: Different node already set for this path: $url");
				return;
			}
		}
		
		if($vinfo = $this->is_variable($current)){
			if(!isset($r[1]))
				$r[1] = array(3=>$vinfo[1], 4=>$vinfo[2]);
			elseif($r[1][3] != $vinfo[1]){//this must be 1 because is_variable returns 0 for matches 1 for name and 2 for type in {name=>type}
				trigger_error("Ignoring Branch!: Different variable already set for this path: $url");
				return;
			}
		
			return $this->buildbranch($uri_parts, $r[1], $node, $url);
		}
		else{
			if(!isset($r[2]))
				$r[2] = array();
				
			if(!isset($r[2][$current]))
				$r[2][$current] = array();
			
			return $this->buildbranch($uri_parts, $r[2][$current], $node, $url);
		}
	}
	
	function is_variable($string){
		preg_match("/{([A-z0-9]*)=>([A-z0-9]*)}/", $string, $matches);
		return $matches; //this returns empty array if nothing was matched
	}

	// OBJECT VERSIONS: these are TOO SLOW!
	/*function urlroute($s = array(), $r, &$path){
		$current = array_shift($s);
		if(!is_object($r))
			return false;
		elseif(!$current)
			return $r->o;
		elseif(isset($r->s[$current]))
			return $this->urlroute($s, $r->s[$current], $path);
		elseif($r->v){
			$path->v[$r->v->n] = $this->validate(array($current),0,$r->v->t);
			return $this->urlroute($s,$r->v,$path);
		}
		return false;
	}*/
	
	function urlroute($s = array(), $r, &$path){
		//comments on 'r': mapping
			//node aka o => 0
			//variable aka v =>1
			//static aka s =>2
			//name aka n => 3
			//type aka t => 4
		$current = array_shift($s);
		if(!$current && !isset($r[0]))
			return false;
		elseif(!$current)
			return new PathNode($r[0]); //maybe array;
		elseif(isset($r[2][$current]))
			return $this->urlroute($s, $r[2][$current], $path);
		elseif(isset($r[1])){
			$path->variables[$r[1][3]] = $this->validate(array($current),0,$r[1][4]);
			return $this->urlroute($s,$r[1],$path);
		}
		return false;
	}

	function route(){
		$type = $_SERVER['REQUEST_METHOD'];
		$uri  = $_SERVER['REQUEST_URI'];

		$url = explode('?', $uri, 2);
		
		$path = new Path($url = rtrim($url[0],'/'));
			
		$s = explode('/', ltrim($path->url,'/'));
		
		$data = array();
		$node = $this->urlroute($s, $this->paths[$type], $path);
		if(!$node)
			return new Route(false, array($this,"fourohfour"), $data, $path, false);
		
		if(is_array($node->inputs)){
			$source = ($type == "GET" ? $_GET : $_POST);

			foreach($node->inputs as $k => $v)
				$data[$k] = $this->validate($source, $k, $v);
		}
		
		$path->skin = $node->skin;
		return new Route($node->file, $node->function, $data, $path, $node->auth);
	}

	function validate($source, $key, $type){
		/*Added true multi-dim functionality
			Types Must be specified in the following manner (variable_name => type)
				//variable name with type
				'testuint'=>'u_int'
				
				//variable name with type and default
				'testuint'=>array('u_int',1337)
				
				//one dimensional array with default for base layer and type
				'testarray2'=>array('array',null,'int'),
				'testarray2a'=>array('array',13,'int'),
				
				//one dimensional array with default for base layer and type and type for array keys
				'testarray2'=>array('array',null,'int','u_int'),
				'testarray2a'=>array('array',13,'int','int'),
				
				//one dimensional array with default for base layer and type with default for array elements
				'testarray2b'=>array('array',null,array('int',136)),
				
				//two dimensoinal array with default bool for top layer, and key validation for both layers
				'countries'=>array('array', false, array('array', false, 'bool', 'u_int'), 'u_int')
				
				//two dimensional array with default for base layer and second layer and type for array elements WITHOUT default for array elements
				'testarray3'=>array('array',null,array('array',3,'int')),
				//two dimensional array with default for base layer and second layer and type for array elements with default for array elements
				'testarray3b'=>array('array',null,array('array',3,array('int',136))),
				
				//these can be extended to further dimensions as following (a four dimensional array in this case)
				'testarray4'=>array('array',null,array('array',null,array('array',null,array('array',3,'int'))))
		*/
		$default = $innertype = $keytype = null;
		if(is_array($type)){
			$default = (isset($type[1]) ? $type[1] : null);
			if($type[0] == 'array' && isset($type[2])){
				if(is_array($type[2]))
					$passarray = $type[2];
				else
					$innertype = $type[2];
				
				if(isset($type[3]))
					$keytype = $type[3];
			}
			$type = $type[0];
		}

		if($type == "str")
			$type = "string";

		switch($type){
			case "u_int":
				if(isset($source[$key]) && $source[$key] !== ""){
					$ret = $source[$key];
					
					if(!is_numeric($ret))
						$ret = $this->do_kmKM($ret); //make k's into 000's and m's into 000000
						
					if(settype($ret, 'int')){
						if($ret < 0)
							return 0;
						return $ret;
					}
				}
				settype($default, "int");
				return $default;
				
			case "int":
			case "integer":
			case "bool":
			case "boolean":
			case "float":
			case "double":
			case "string":
				if(isset($source[$key]) && $source[$key] !== ""){
					$ret = $source[$key];

					if(settype($ret, $type))
						return $ret;
				}
				settype($default, $type);
				return $default;
			
			case "array":
				if(isset($source[$key])){
					$ret = $source[$key];

					if(settype($ret, 'array')){
						//iff type is set as an array (eg 'countries'=>array('array', 0, 'int') ) with type array (which is how we got here) default, and internal type
						foreach($ret as $k => $v){
							if($keytype){ //validate the keys as well -- have to store the data in $temp while we rewrite the key
								$temp = $ret[$k];
								unset($ret[$k]);
								$k = $this->validate(array(0=>$k),0,$keytype); //this is kindof hack-ish, but I only just ran into wanting to validate the keys as well
								$ret[$k] = $temp;
							}
							$ret[$k] = $this->validate($ret, $k, (isset($passarray) ? $passarray : $innertype));
							if($ret[$k] === null)
								unset($ret[$k]);
						}

						return $ret;
					}
				}
				return def($default, array());

			case "file":
				return def($_FILES[$key], null);

			default:
				die("Unknown validation type: $type\n" . ($type ? null : "\n<br />Are you passing an array without specifying an inner default?"));
		}
	}
	
	function do_kmKM($ret){
		//make k's into 000's and m's into 000000
		$ret = str_replace(",","",$ret);
		$k=substr_count($ret,"k") + substr_count($ret,"K");
		$m=substr_count($ret,"m") + substr_count($ret,"M");
		$ret = str_ireplace("k","",$ret);
		$ret = str_ireplace("m","",$ret);
		$ret = (float)$ret*(pow(1000,$k));
		$ret = (float)$ret*(pow(1000000,$m));
		return (int)$ret;
	}

	function fourohfour($data, $path, $user){
		//trigger_error("404: " . $path->url); //Only set this if you are sending errors somewhere other than the page being displayed
		header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
		$path->url = htmlentities($path->url);
		echo <<<END
<html>
<head><title>404</title><link rel='stylesheet' href='/css/public_css.css' type='text/css' /></head>
<body>
<br /><br /><br />404 - not found<br /><br />The URL you attempted to access was: {$path->url}<br /><br /><a href='/'>Back to homepage!</a>
</div></div></body>
</html>
END;
	}
}


/*class VUriPart extends UriPart{ //switch to arrays as they are massively faster apparently
	public $n; //$name; //this is the NAME of a variable, if it is a variable
	public $t; //$type; //this is the TYPE of a variable, if it is a variable
	function __construct($n = null, $t = null){
		$this->n = $n;
		$this->t = $t;
		parent::__construct();
	}
}


class UriPart { //shortening names to speed up APC storage and retrieval
	public $o; //$node; //this stores the PathNode of the path in question
	public $s; //$static; //this is an array of UriParts of static uri's
	public $v; //$variable; //this holds the UriPart of a variable - we can only have 1 variable at a given /static/ point otherwise we don't know which it is
	function __construct(){
		$this->o = null;
		$this->s = array();
		$this->v = null;
	}
}*/

class PathNode { //shortening names to speed up APC storage and retrieval //debug test
	public $file; //$file;
	public $function; //$function;
	public $inputs; //$inputs;
	public $auth; //$auth;
	public $skin; //$skin_name;

	/*function __construct($file, $function, $inputs = null, $auth = null, $skin = null){
		$this->file = $file;
		$this->function = $function;
		$this->inputs = $inputs;
		$this->auth = $auth;
		$this->skin = $skin;
	}*/
	function __construct($r){
		$this->file = $r['0'];
		$this->function = $r['1'];
		$this->inputs = def($r['2'],null);
		$this->auth = def($r['3'],null);
		$this->skin = def($r['4'],null);
	}
}


class Route {
	public $file;
	public $function;
	public $path;
	public $auth;
	public $data;
	
	function __construct($file, $function, $data, $path = null, $auth = null){
		$this->file = $file;
		$this->function = $function;
		$this->data = $data;
		$this->path = $path;
		$this->auth = $auth;
	}
}

class Path {
	public $url;
	public $skin;
	public $page_title, $meta_desc, $language;
	public $variables;

	function __construct($url, $skin = null){
		$this->url = $url;
		$this->skin = null;
		$this->page_title = null;
		$this->meta_desc = null;
		$this->language = null;
		$this->variables = array();
	}
}

//--These functions are for use within the router--//


