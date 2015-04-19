<?php

date_default_timezone_set('America/Vancouver');


class Gearman {
	protected $_gmWorker  = false;
	private   $_settings  = false;
	private   $_logHandle = false;
	
	public function __construct($config) {	  
		$this->_settings = $config;
	}
	
	public function setup() {		
		$this->setLogger($this->_settings->logger());
		$this->createWorker();
		$this->connect();
		$this->logStart();
	}

	public function setLogger($logger) {
		$this->_logHandle = $logger;
	}
	
	public function createWorker() {
		$this->_gmWorker= new GearmanWorker();
	}
	
	public function connect() {
		foreach ($this->_settings->mqConnections as $mqServer) {
			if ($mqServer['driver'] !== 'gearmand') {
				continue;
			}
			$serverConnect = parse_url($mqServer['uri']);
			$serverList[]  = $serverConnect['host'] . ':' . $serverConnect['port'];
		}
		$serverString = implode(',', $serverList);

		$this->_gmWorker->addServers($serverString);
		if ( $this->_gmWorker->returnCode() != GEARMAN_SUCCESS ) {
			$message = $this->_gmWorker->error();
			throw new Exception($message);
		}	
	}
	
	public function logStart() {
		$this->_logHandle->append("\n===============================\n");
		$this->_logHandle->append(date(DATE_RFC822) . "\tStarted\n");
		$this->_logHandle->append(date(DATE_RFC822) . "\tWaiting..\n");
	}
	
	
	/**
	 *  register event and callback at gearmand
	 */
	public function register($event, $workerFunction, $caller) {
		$this->_gmWorker->addFunction($event, $workerFunction, $caller);

		if ( $this->_gmWorker->returnCode() != GEARMAN_SUCCESS ) {
			$message = $this->_gmWorker->error();
			throw new Exception($message);
		}
	}
	
	/**
	 * get next job from queue
	 * @todo if something fails react to it
	 */
	public function start() {
		// read from gearman queue
		// Sit in the loop till there is a FAILED return
		while($this->_gmWorker->work()) {
		    if ($this->_gmWorker->returnCode() != GEARMAN_SUCCESS) {
    			$this->_logHandle->append(date(DATE_RFC822) . "\tFAILED: " . $this->_gmWorker->error());
    			break;
		    }
		}
	}	
}
