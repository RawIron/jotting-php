<?php

class keyValueStoreArray {

    public function connect() {
        return;
    }
    
    public function save($job) {
        return;
    }
    
    public function timeLookUpOfKeys($job) {
      $firstRead = true;
      $start = microtime();      

    	foreach ($job->lookUpKeys as $key) {
    		if (!$firstRead) {
    	       $start = microtime();
    		}
    	  $text = $job->keyValueArray[$key][$job->attributeName];
    		$end = microtime();
    		$duration[] = number_format($end - $start,6);
    		
    		if ($job->pause['max'] > 0) {
        		$randomWait = mt_rand($job->pause['min'], $job->pause['max']);
        		usleep($randomWait);
    		}

    		$firstRead = false;
    	}
    	return $duration;
    }
}
