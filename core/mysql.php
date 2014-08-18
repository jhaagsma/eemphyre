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

define("DB_ASSOC", MYSQLI_ASSOC);
define("DB_NUM",   MYSQLI_NUM);
define("DB_BOTH",  MYSQLI_BOTH);

class MysqlDb {
	public $host;
	public $db;
	public $user;
	public $pass;
	public $persist;
	
	public $con;
	public $lasttime;
	public $queries;
	public $querystore;

	function __construct($host, $db, $user, $pass, $persist = false, $seqtable = null, $logqueries = false, $qlog_table = 'queries'){ //logqueries should really be false by default

		$this->host = $host;
		$this->db = $db;
		$this->user = $user;
		$this->pass = $pass;
		$this->persist = $persist;
		$this->seqtable = $seqtable;
		$this->logqueries = $logqueries;
		$this->plogged = false;
		$this->preparedq = false;
		$this->querystore = 150;
		$this->con = null;
		$this->lasttime = 0;
		$this->qlog = null;
		$this->qlog_table = $qlog_table;

		$this->queries = array();
		$this->count = 0;
		$this->querytime = 0;
	}

	function __destruct(){
		$this->close();
	}
	
	function can_connect(){
		if(!$this->persist)
			$this->con = new mysqli($this->host, $this->user, $this->pass, $this->db);
		else
			$this->con = new mysqli('p:' . $this->host, $this->user, $this->pass, $this->db);
			
		if($this->con->connect_errno)
			return false;
			
		return true;
	}
	
	function connect(){
		if($this->con){
			if($this->lasttime > time()-10)
				return $this->con->ping();

			return true;
		}
			
		if(!$this->can_connect()){
			trigger_error('Connect Error (' . $this->con->connect_errno . ') ' . $this->con->connect_error, E_USER_ERROR);
			$this->con = null;
			exit; //if the redirect doesn't exist??
		}

		return true;
	}

	function close(){
		if($this->con){
			$this->con->close();
			$this->con = null;
		}
	}
	
	function log_em($query,$qtime){
		if($this->logqueries && !$this->plogged){//SQRT(sq_total_time/total_num - total_time*total_time/(total_num*total_num))',
			$this->plogged = true;
			if($this->preparedq != false)
				$query = $this->preparedq;
			
			$this->qlog = 'INSERT INTO `' . $this->qlog_table . '`... ' . $query;
			$time = time();
			
			//We should check this function for accuracy again; I never properly checked it methinks
			$this->pquery('INSERT INTO `' . $this->qlog_table . '` (hash, strlen, last_time, total_num, total_time, min_time, max_time,avg_time,new_mean,new_s,new_stdev,query,last_page)
				VALUES (?,?,?,1,?,?,?,?,?,0,0,?,?) ON DUPLICATE KEY UPDATE
				last_time = ?, last_page = ?, total_num = total_num + 1, total_time = total_time + ?, min_time = if(? < min_time,?,min_time),
				max_time = if(max_time < ?, ?, max_time), avg_time = total_time/total_num, new_s = new_s + (? - new_mean) * (? - new_mean + (? - new_mean) / total_num),
				new_mean = new_mean + (? - new_mean) / total_num, new_stdev = SQRT(new_s / (total_num - 1))',
				md5($query), strlen($query), $time, $qtime,$qtime, $qtime, $qtime, $qtime, $query, (isset($_SERVER) ? $_SERVER['PHP_SELF'] : 'bot'),
				$time, (isset($_SERVER) ? $_SERVER['PHP_SELF'] : 'bot'), $qtime, $qtime, $qtime, $qtime, $qtime, $qtime, $qtime, $qtime, $qtime);
			//Donald Knuth's "The Art of Computer Programming, Volume 2: Seminumerical Algorithms", section 4.2.2.
			
			$this->plogged = false;
			$this->preparedq = false;
		}
		return;
	}
	
	
	function query($query,$logthis = true){
		$insertid = 0;
		$affectedrows = 0;
		$numrows = 0;
		$qt = 0;
	
		if(!$this->connect())
			return false;

		$start = microtime(true);
		
		$result = $this->con->query($query);
		
		if(!$result)
			trigger_error("Query Error: (" . $this->con->errno . ") " . $this->con->error . " : \"$query\"",E_USER_ERROR) && exit;
		
		$insertid = $this->con->insert_id;
		$affectedrows = $this->con->affected_rows;
		$numrows = (isset($result->num_rows) ? $result->num_rows : 0);

		$end = microtime(true);

		$this->lasttime = time();

		$this->count++;
		$qt = $this->querytime = ($end - $start);
		
		if($logthis){
			if($this->plogged)
				$this->queries[] = array($this->qlog, $this->querytime);
			else
				$this->queries[] = array($query, $this->querytime);
		}
		if(count($this->queries) > $this->querystore)
			array_shift($this->queries);
			
		global $debug;
		if(isset($debug) && $debug)
			$this->debug_query($query); //this returns a little table that does the EXPLAIN of a non-EXPLAIN query in the query list
		
		if($this->logqueries && substr($query, 0, 7) != "EXPLAIN")
			$this->log_em($query,$qt);
		
		return new MysqlDbResult($result, $this->con, $numrows, $affectedrows, $insertid, $qt);
	}

	function prepare(){
		if(!$this->connect())
			return false;

		$args = func_get_args();
		
		if(count($args) == 0)
			trigger_error("mysql: Bad number of args (No args)",E_USER_ERROR) && exit;
		
		if(count($args) == 1)
			return $args[0];

		$query = array_shift($args);
		$parts = explode('?', $query);
		$query = array_shift($parts);
		
		if(count($parts) != count($args))
			trigger_error("Wrong number of args to prepare for $query",E_USER_ERROR) && exit;
		
		for($i = 0; $i < count($args); $i++){
			$query .= $this->prepare_part($args[$i]) . $parts[$i];
		}
		
		return $query;
	}

	function prepare_arr($query, $array){
		return call_user_func_array($query, $array);
	}

	function prepare_part($part){

		
		switch(gettype($part)){
			case 'integer': return $part;
			case 'double':  return $part;
			case 'string':
				if(is_numeric($part))
					return $part;
				return "'" . $this->con->real_escape_string($part) . "'"; // mysql_real_escape_string($part, $this->con) 
			case 'boolean': return ($part ? 1 : 0);
			case 'NULL':	return 'NULL';
			case 'array':
				$ret = array();
				foreach($part as $v)
					$ret[] = $this->prepare_part($v);
				return implode(',', $ret);
			default:
				trigger_error("Bad type passed to the database!!!!", E_USER_ERROR) && exit;
		}
	}

	function pquery(){
		$args = func_get_args();
		$this->preparedq = $args[0];
		$query = call_user_func_array(array($this, 'prepare'), $args);
		
		return $this->query($query);
	}
	
	function pquery_array($args){
		$this->preparedq = $args[0];
		$query = call_user_func_array(array($this, 'prepare'), $args);
		
		return $this->query($query);
	}

	function getSeqID($id1, $id2, $area, $table = false, $start = false){
		if(!$table){
			$table = $this->seqtable;
			if(!$table)
				return false;
		}
		$inid = $this->pquery("UPDATE " . $table . " SET max = LAST_INSERT_ID(max+1) WHERE id1 = ? && id2 = ? && area = ?", $id1, $id2, $area)->insertid();
		if($inid)
			return $inid;
			
		if(!$start)
			$start = 1;
			
		$ignore = $this->pquery("INSERT IGNORE INTO " . $table . " SET max = ?, id1 = ?, id2 = ?, area = ?", $start, $id1, $id2, $area);
		if($ignore->affectedrows())
			return $start;
		else	
			return $this->getSeqID($id1, $id2, $area, $table, $start);
	}
	
	function debug_query($query){
		if(substr($query, 0, 7) != "EXPLAIN" && substr($query, 0, 6) == "SELECT"){
			$explain = $this->query('EXPLAIN ' . $query,false)->fetchrow();
			$text = 'EXPLAIN ' . "<br />\n<table><tr>";
			foreach($explain as $name => $var)
				$text .= '<td>' . $name . '</td>';
			$text .= '</tr><tr>';
			foreach($explain as $var)
				$text .= '<td>' . $var . '</td>';
			$text .= '</tr></table>';
			$this->queries[] = array($text, $this->querytime);
		}
		return;
	}
	
}

class MysqlDbResult {
	public $dbcon;
	public $result;
	public $numrows;
	public $affectedrows;
	public $insertid;
	public $querytime;

	function __construct($result, $dbcon, $numrows, $affectedrows, $insertid, $qt){
		$this->dbcon = $dbcon;
		$this->result = $result;
		$this->numrows = $numrows;
		$this->affectedrows = $affectedrows;
		$this->insertid = $insertid;
		$this->querytime = $qt;
	}
	
	function __destruct(){
		$this->free();
	}

	//one row at a time
	function fetchrow($type = DB_ASSOC){
		return $this->result->fetch_array($type);
	}

	//for queries with a single column in a single row
	function fetchfield(){
		$ret = $this->fetchrow(DB_NUM);
		return $ret[0];
	}

	//return the full set
	function fetchfieldset(){
		$ret = array();

		while($line = $this->fetchrow(DB_NUM)){
			if(count($line) == 1)
				$ret[] = $line[0];
			else
				$ret[$line[0]] = $line[1];
		}

		return $ret;
	}
	

	//return the full set
	function fetchrowset($col = null, $type = DB_ASSOC){
		$ret = array();

		while($line = $this->fetchrow($type))
			if($col)
				$ret[$line[$col]] = $line;
			else
				$ret[] = $line;

		return $ret;
	}

	function affectedrows(){
		return $this->affectedrows;
	}

	function insertid(){
		return $this->insertid;
	}
	
	function rows(){
		return $this->numrows;
	}

	function free(){
		if(!is_object($this->result))
			return false;
			
		return $this->result->free();
	}
}

