<?php

require_once(dirname(__FILE__) . '/' . 'stats.php');


class KeySample {

    protected function generateRandomOrderOfKeys($array) {
        $keys = array_keys($array);
        shuffle($keys);
        return $keys;
    }
    
    protected function getSampleOfKeys($keys, $size) {
        return array_rand($keys, $size);
    }
    
    public function selectLookUpKeys($job) {
        if ($job->sampleSize > 0) {
            $keys = $this->getSampleOfKeys($job->keyValueArray, $job->sampleSize);
            if (!is_array($keys)) {
                $keys = array($keys);
            }
            return $keys;
        } else {
            return $this->generateRandomOrderOfKeys($job->keyValueArray);
        }
    }    
}


class Runner {

    public function run($job) {
        $job->dataStore->connect();
        $job->dataStore->save($job);
        $duration = $job->dataStore->timeLookUpOfKeys($job);
        return $duration;
    }
    
    public function runIterations($job) {
        $duration = array();    
        for ($i=0; $i<$job->iterations; $i++) {
            $runStats = $this->run($job);
            $duration = array_merge($duration, $runStats);
        }
        return $duration;    
    }
    
    public function getRunTimeStats($duration) {
        $stat = new Stats();
        $metrics = $stat->get($duration);
        return $metrics;
    }
}

