<?php

require_once('Gearman.php');

date_default_timezone_set('America/Vancouver');


class GearmanReader {
	private $_gearman = null;
	private $_data;
	private $_header;
	private $_body;

	public function setup($config) {
		$this->_gearman = new Gearman($config);
		$this->_gearman->setup();
	}

	public function register($event, $callback, $caller) {
		$this->_gearman->register($event, $callback, $caller);
	}
	public function start() {
		$this->_gearman->start();
	}

	public function receive($message) {
		$json = $message->workload();
		$this->reset();
		$this->decode($json);
		$this->unserializeBody();
		$this->extractHeader();
		$this->clean();
		return $this->assembleEvent();
	}

	private function reset() {
		$this->_data = array();
		$this->_header = array();
		$this->_body = array();
	}

	private function decode($content) {
		$this->_data = json_decode($content, true);
		if ( $this->_data === null ) {
			$error = "decode failed";
			throw new Exception($error);
		}
	}

	private function unserializeBody() {
		if ( isset($this->_data['jsonString'])
		&& (strlen($this->_data['jsonString'])>0) ) {
			$this->_body = json_decode($this->_data['jsonString'], true);
		}

		if ( $this->_body === null ) {
			$this->_body = array();
		}
	}

	private function extractHeader() {
		$this->_header = $this->_data;
		if (isset($this->_data['jsonString'])) {
			unset($this->_header['jsonString']);
		}
	}

	private function clean() {
		if ( !(is_numeric($this->_header['applicationId'])
		&& is_numeric($this->_header['userId'])) ) {
			$message = "wrong data type in event";
			throw new Exception($message);
		}

		if ( is_numeric($this->_header['eventTime']) ) {
			$this->_header['eventTime'] = array(
				'd' => date('Y-m-d', $this->_header['eventTime']),
				't' => date('H:i:s', $this->_header['eventTime']));
		} else {
			$this->_header['eventTime'] = array(
				'd' => date('Y-m-d', time()), 
				't' => date('H:i:s', time()));
		}
	}

	private function assembleEvent() {
		$merged = array_merge($this->_header, $this->_body);
		$event = $merged;
		if (isset($this->_header['event'])) {
			$event['event'] = $this->_header['event'];
		} else if (isset($this->_body['event'])) {
			$event['event'] = $this->_body['event'];
		} else if (isset($this->_header['eventId'])) {
			$event['event'] = $this->_header['eventId'];
		} else {
			$event['event'] = $this->_body['eventId'];
		}
		$event['jsonString'] = json_encode($merged);
		return $event;
	}
}

