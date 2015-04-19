<?php

date_default_timezone_set('America/Vancouver');


class FileLogger {
	private $_home = '/var/log';
	private $_logfile = '';
	private $_fileHandle = null;
	
	public function logTo($folder, $file) {
		$this->_logfile = $this->_home . '/' . $folder . '/' . $file . '.log';
		$this->open();
	}
	
	public function open() {
		$this->_fileHandle = fopen($this->_logFile, "a+");
		if ( !$this->_fileHandle ) {
			$msg = 'Cannot open to append: ' . $this->_logFile;
			throw new Exception($msg);
		}
	}
	
	public function append($line) {
		if ( !(isset($line) && is_string($line)) ) {
			return false;
		}
		$result = fwrite($this->_fileHandle, $line . "\n");
		if ( $result === false ) {
			throw new Exception("cannot write to log file");
		}
	}
}

class TestLogger {
	public function logTo($container, $collection) {		
	}

	public function append($line) {
		print_r($line);
	}
}


class LoggerFactory {
	public static function create($class) {
		$className = $class . 'Logger';
		if (class_exists($className)) {
			return new $className();
		}
	}
}
