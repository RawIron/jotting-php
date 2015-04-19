<?php

/**
 *
 * Gearman Writer
 *
 * receives an array with data
 * PreCondition: the data in the array MUST be UTF-8 encoded
 * encodes the data as a JSON object and sent it to the gearman servers
 */

class GearmanWriter {
	/**
	 *  the json object built from the work array
	 */
	private $_jsonObject = false;

	/**
	 *  gearman server passes the work to a worker
	 */
	private $_worker  = false;
	private $_work    = false;

	/**
	 *  array of available gearman servers
	 */
	private $_gearmanServers = false;
	private $_gearmanWriter  = false;


	public function __construct($gearmanServers = false) {
		$this->_gearmanServers = $gearmanServers;
	}

	public function init() {
		if ($this->createWriter()) {
			return $this->addServersToWriter();
		}
		return false;
	}
	
	private function createWriter() {
		$this->_gearmanWriter = new GearmanClient();
		if ( !$this->_gearmanWriter ) {
			return false;
		}
		return true;
	}
	
	private function addServersToWriter() {	
		if ( !is_array($this->_gearmanServers) ) {
			return false;
		}		
		// at least one must succeed
		$addedServer = 0;
		foreach ($this->_gearmanServers AS $id => $server) {
			shuffle($this->_gearmanServers);
			if ( $this->_gearmanWriter->addServer($server['host'], $server['port']) ) {
				$addedServer++;
			}
		}
		if ( !($addedServer>0) ) {
			return false;
		}
		return true;		
	}


	/**
	 * call the Gearman Job Server async
	 * select the gearman worker
	 * give the worker an array to work on
	 *
	 * @param worker name
	 * @param work for the worker as an associative array
	 */
	public function sent($worker, $work) {
		if ( !(isset($worker) && is_string($worker)) ) {
			return false;
		}
		if ( !(isset($work) && is_array($work)) ) {
			return false;
		}

		// add worker and work
		$this->_worker = $worker;
		$this->_work   = $work;
                $this->_jsonObject = false;

		if ( $this->encode() === false ) {
		    return false;
		}

		// do the work, send background info to gearman worker
		$result = $this->_gearmanWriter->doBackground(
		    $this->_worker,
		    $this->_jsonObject
		);

		// @todo log exception.. though it was the logger that failed
		// @todo get a syslog file pointer in the $settings
		// @todo write error to the syslog
	}

	/**
	 *  encode work array of data in standard json format
	 *  store it in the jsonObject
	 *  WARNING: json_encode() requires UTF-8 encoded data
	 *  @todo add a checksum like CRC32 of the jsonObject to the jsonObject
	 *
	 *  @return true
	 */
	private function encode() {
		if ($this->_jsonObject === false) {
		    $this->_jsonObject = json_encode($this->_work);
		}

		if ($this->_jsonObject === null) {
			return false;
		}

		return true;
	}
}
