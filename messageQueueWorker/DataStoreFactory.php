<?php

require_once('Mysql.php');
require_once('MysqlFbCredits.php');

class TestDriver {
	public function open($credentials) {
		return;
	}
	public function connect($container, $collection) {
		return;
	}	
	public function save($data) {
		print_r($data);
	}
}


/*
 * "Borg" Design Pattern
 */
class DataStoreFactory {
	public static $storeMap = array();
	
	public static function setup($config) {
		self::$storeMap = $config->dsFactoryMap;
	}
	
	public static function create($class) {
		if ( isset(self::$storeMap[$class])
			 && class_exists(self::$storeMap[$class]['driver']) )
			$ds = new self::$storeMap[$class]['driver']();
			$ds->open(self::$storeMap[$class]);
			return $ds;
	}
}
