<?php


class keyValueStoreMySQL {
    protected $_connection = false;
    
    public function connect() {
    	$this->_connection = mysql_connect('localhost:3306', 'bench', 'youcannotguessthis');
    	mysql_select_db('KeyValueBenchmark');
    }
    
    public function save($job) {
        return;
    }
        
    public function timeLookUpOfKeys($job) {
        if (!$this->_connection) {
            return false;
        }
        
    	foreach ($job->lookUpKeys as $key) {
    		$query = "SELECT {$job->attributeName} 
    				  FROM {$job->tableName} WHERE `{$job->keyName}`= '$key'";
    		
    		$start = microtime();
    		$resource  = mysql_query($query, $this->_connection);
    		$row       = mysql_fetch_assoc($resource);
    		$value     = $row[$job->attributeName];
    		$end   = microtime();
    		$duration[] = number_format($end - $start,6);
    		            
            if ($job->pause['max'] > 0) {
                $randomWait = mt_rand($job->pause['min'], $job->pause['max']);
                usleep($randomWait);
            };
    	}
    	return $duration;
    }
}

