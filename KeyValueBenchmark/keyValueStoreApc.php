<?php

class keyValueStoreApc {

    public function connect() {
        return;
    }
    
    public function save($job) {
        return;
    }
    
    public function timeLookUpOfKeys($job) {
    	foreach ($job->lookUpKeys as $key) {
    	    $start = microtime();
    	    $result= apc_fetch("{$job->objectName}::" . $key);    	    
    	    $text = $result[$job->attributeName];
    		$end   = microtime();
    		$duration[] = number_format($end - $start,6);
    		
    		if ($job->pause['max'] > 0) {
        		$randomWait = mt_rand($job->pause['min'], $job->pause['max']);
        		usleep($randomWait);
    		}
    	}
    	return $duration;
    }
}
