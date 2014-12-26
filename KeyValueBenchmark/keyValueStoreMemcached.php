<?php


class keyValueStoreMemcached {
    protected $_prefix = 'bench';
    protected $_mc = false;
    
    public function connect() {
    	$this->_mc = memcache_connect('localhost', 11211);
    }
    
    public function save($job) {
    	foreach($job->keyValueArray as $key => $value) {
    		memcache_set($this->_mc, $this->buildKey($job->objectName, $key), $value, 0, 30);
    	}
    }
    
    public function timeLookUpOfKeys($job) {
      if (!$this->_mc) {
        return false;
      }
        
    	foreach ($job->lookUpKeys as $key) { 		
    		$start  = microtime();
    		$result = memcache_get($this->_mc, $this->buildKey($job->objectName, $key));
    		$text   = $result[$job->attributeName];
    		$end    = microtime();
    		$duration[] = number_format($end - $start,6);    		
            
            if ($job->pause['max'] > 0) {
                $randomWait = mt_rand($job->pause['min'], $job->pause['max']);
                usleep($randomWait);
            }    		
    	}
    	return $duration;
    }
    
    protected function buildKey($objectName, $key) {
        return $this->_prefix . objectName . $key;
    }
}
