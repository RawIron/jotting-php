<?php


class Config {
	public $dsConnections = array();
	public $mqConnections = array();	
	public $dsFactoryMap = array();
	public $logger = null;
		 	
	public function setup() {
		$this->setupDsConnections();
		$this->setupMqConnections();
		$this->setupFactoryMap();
	}
	
	public function setupDsConnections() {
		$this->dsConnections[] = array(
			'driver' => 'Mysql', 
			'host' => '127.0.0.1', 'port' => 3306, 
			'user' => 'logger', 'password' => 'l0gg3r',);
		$this->dsConnections[] = array(
			'driver' => 'MysqlFbCredits', 
			'host' => '127.0.0.1', 'port' => 3306, 
			'user' => 'logger', 'password' => 'l0gg3r',);
	}
	
	public function setupMqConnections() {
		$this->mqConnections[] = array(
			'driver' => 'gearmand',
			'uri' => 'gearmand://127.0.0.1:4730',);	
	}
		
	public function setupFactoryMap() {
		$this->dsFactoryMap['IngameEventStore'] = $this->dsConnections[0];
		$this->dsFactoryMap['IngameEventErrorStore'] = $this->dsConnections[0];
		$this->dsFactoryMap['FacebookEventStore'] = $this->dsConnections[1];
		$this->dsFactoryMap['StoreEventStore'] = $this->dsConnections[0];			
	}
	
	public function setLogger($logger) {
		$this->logger = $logger;
	}
	public function logger() {
		return $this->logger;
	}
}


class ConfigTest extends Config {
	public function setupDsConnections() {
		$this->dsConnections[] = array(
			'driver' => 'TestDriver', 
			'host' => '127.0.0.1', 'port' => 3306, 
			'user' => 'logger', 'password' => 'testlogger');
		$this->dsConnections[] = array(
			'driver' => 'TestDriver', 
			'host' => '127.0.0.1', 'port' => 3306, 
			'user' => 'logger', 'password' => 'testlogger');
	}
}

class ConfigLocalTest extends Config {
	public function setupDsConnections() {
		$this->dsConnections[] = array(
			'driver' => 'Mysql', 
			'host' => '127.0.0.1', 'port' => 3306, 
			'user' => 'logger', 'password' => 'l0gg3r');
		$this->dsConnections[] = array(
			'driver' => 'MysqlFbCredits', 
			'host' => '127.0.0.1', 'port' => 3306, 
			'user' => 'logger', 'password' => 'l0gg3r');
	}
}

