<?php

require_once('GearmanReader.php');

class TestClient {
	public function register($event, $callback, $caller) {
		
	}	
	public function receive($message) {
		
	}
}


class MessageQueueClientFactory {	
	public static function create($class) {
		$className = $class . 'Reader';
		if (class_exists($className)) {
			$mc = new $className();
			return $mc;
		}
	}
}
