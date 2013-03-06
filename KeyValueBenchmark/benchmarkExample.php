<?php

require_once(dirname(__FILE__) . '/' . 'keyValueStoreArray.php');
require_once(dirname(__FILE__) . '/' . 'job.php');
require_once(dirname(__FILE__) . '/' . 'runBench.php');

$example[] = array(
            'key1' => array('title' => 'remarkable', 'quantity' => 4),
            'key2' => array('title' => 'amazing', 'quantity' => 1),
            );

if ( !(isset($example) && is_array($example)) ) {
    return false;
}

$keySample = new KeySample();
$runner = new Runner();

$job = new Job();
$job->name = 'ExampleBenchmark';
$job->pause = array('min' =>10, 'max' =>250);
$job->sampleSize = 10;
$job->iterations = 5;
$job->keyValueArray = $example;
$job->lookUpKeys = $keySample->selectLookUpKeys($job);
$job->dataStore  = new keyValueStoreArray();
//$job->dataStore->save($job->keyValueArray);
$job->attributeName = 'title';


$duration = $runner->runIterations($job);
$metrics  = $runner->getRunTimeStats($duration);
$metrics['Data'] = $job->name;
print_r($metrics);

