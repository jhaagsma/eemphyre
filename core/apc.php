<?

class Cache {
	public $count;
	public $queries;
	
	function __construct(){
		$this->queries = array();
		$this->count = 0;
	}

	function __destruct(){

	}
	
	function addquery($stuff){
		$this->queries[] = $stuff;
		$this->count++;
		if(count($this->queries) > 1000)
			array_shift($this->queries);
	}

	function add($key,$val,$ttl=0){
		$start = microtime(true);
		$success = apc_add($key,$val,$ttl);
		$this->addquery(array('add',$success,microtime(true)-$start,$key,$ttl));
		return $success;
	}
	
	function store($key,$val,$ttl=0){
		$start = microtime(true);
		$success = apc_store($key,$val,$ttl);
		$this->addquery(array('store',$success,microtime(true)-$start,$key,$ttl));
		return $success;
	}
	
	function fetch($key, $default = null){
		$start = microtime(true);
		$val = apc_fetch($key,$success);
		$this->addquery(array('fetch',$success,microtime(true)-$start,$key,null));
		return ($success ? $val : $default);
	}
	
	function multi_fetch($keys){
		$return = array();
		foreach($keys as $key){
			$start = microtime(true);
			$val = apc_fetch($key,$success);
			$this->addquery(array('fetch',$success,microtime(true)-$start,$key,null));
			if($success)
				$return[$key] = $val;
		}
		return $return;
	}
	
	function fetch_prefix_keys($prefix,$keys){
		$fetch = array();
		foreach($keys as $k)
			$fetch[] = $prefix.$k;
			
		return $this->multi_fetch($fetch);
	}
	
	function delete($key){
		$start = microtime(true);
		$success = apc_delete($key);
		$this->addquery(array('delete',$success,microtime(true)-$start,$key,null));
		return $success;
	}
	
	function clear_user_cache(){
		$start = microtime(true);
		$success = apc_clear_cache('user');
		$this->addquery(array('clear user cache',$success,microtime(true)-$start,null,null));
		return $success;
	}
	
	function clear_cache(){
		$start = microtime(true);
		$success = apc_clear_cache();
		$this->addquery(array('clear cache',$success,microtime(true)-$start,null,null));
		return $success;
	}
}
