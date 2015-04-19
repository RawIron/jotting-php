<?php

require_once 'EventShards.php';

date_default_timezone_set('America/Vancouver');


class EventWorker {
	private $_event = 'json_event';
	private $_callback = 'doJob';
	private $_messageClient = null;
	private $_logger = null;
	private $_ds = array();
	private $_settings = null;
	private $_shards = null;
	
	public function __construct($config) {
		$this->_settings = $config;
	}
	
	public function useMessage($client) {
		$this->_messageClient = $client;
	}

	public function useLog($server) {
		$this->_logger = $server;
	}

	public function openDataShards() {
		$this->_shards = new EventShards();
		$this->_shards->open();
	}	

	public function selectShardBy($key) {
		foreach ($this->_shards->shards as $shard) {
			if ($shard->goesIntoBucketFor($key)) {
				return $shard->ds;
			}
		}
		$this->_logger->append(date(DATE_RFC822) . "\tno shard found\n");
	}
	
	public function log($e) {
		$this->_logger->append($e);
	}
	public function receive($message) {
		return $this->_messageClient->receive($message);
	}	
	public function work() {
		try {
			$this->doWork();
		} catch (Exception $e) {
			$msg = "File:". $e->getFile() ." , Line:". $e->getLine() ." , Error:". $e->getMessage();
			$this->_logger->append(date(DATE_RFC822) . "\t" . $msg . "\n");
			$this->_logger->append(date(DATE_RFC822) . "\tStopped\n===============================\n");
			die();			
		}
	}
	public function doWork() {
		$this->openDataShards();
		$this->_messageClient->register($this->_event, $this->_callback, $this);
		$this->_messageClient->start();				
	}
}


function doJob($message, $caller) {
	try {
		$eventData = $caller->receive($message);
		$ds = $caller->selectShardBy($eventData);
		$ds->save($eventData);
	} catch (Exception $e) {
		$message = "File:". $e->getFile() ." , Line:". $e->getLine() ." , Error:". $e->getMessage();
		$caller->log(date(DATE_RFC822) . "\t" . $message . "\n");		
	}
}

