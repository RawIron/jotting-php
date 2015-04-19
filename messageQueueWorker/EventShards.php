<?php

require_once('DataStoreFactory.php');

date_default_timezone_set('America/Vancouver');


class EventShards {
	public $shards = array();
	
	public function open() {
		$this->shards[] = new IngameEventStore();
		$this->shards[] = new IngameEventErrorStore();
		$this->shards[] = new FacebookEventStore();
		$this->shards[] = new StoreEventStore();
		
		foreach ($this->shards as $store) {
			$store->connect();
		}
	}
}


class IngameEventStore {
	public $ds = null;
	public $container = 'logging';
	public $collection = 'serviceEvents';
	public $goesInto = null;
	
	public function connect() {
		$this->ds = DataStoreFactory::create(get_class($this));
		$this->ds->connect($this->container(), $this->collection());
	}
	public function container() {
		return $this->container;
	}
	public function collection() {
		return $this->collection . date('ymd', time());
	}
	
	public function goesIntoBucketFor($data) {
		if ($data['eventId'] === 'actionlog'
				|| stristr($data['eventId'], 'ibryte') !== false) {
			return $this->ds;
		} else {
			return false;
		}
	}
}

class IngameEventErrorStore {
	public $ds = null;
	public $container = 'logging';
	public $collection = 'serviceErrors';
	public $goesInto = null;
	
	public function connect() {
		$this->ds = DataStoreFactory::create(get_class($this));
		$this->ds->connect($this->container(), $this->collection());
	}
	public function container() {
		return $this->container;
	}
	public function collection() {
		return $this->collection . date('ymd', time());
	}
	
	public function goesIntoBucketFor($data) {
		if (stristr($data['eventId'], 'err') !== false) {
			return $this->ds;
		} else {
			return false;
		}
	}
}

class FacebookEventStore {
	public $ds = null;
	public $container = 'logging';
	public $collection = 'facebookCreditsEvents';
	public $goesInto = null;
	
	public function connect() {
		$this->ds = DataStoreFactory::create(get_class($this));
		$this->ds->connect($this->container(), $this->collection());
	}
	public function container() {
		return $this->container;
	}
	public function collection() {
		return $this->collection;
	}
	
	public function goesIntoBucketFor($data) {
		if (stristr($data['eventId'], 'facebook credits') !== false) {
			return $this->ds;
		} else {
			return false;
		}
	}
}


class StoreEventStore {
	public $ds = null;
	public $container = 'logging';
	public $collection = 'storeEvents';
	public $goesInto = null;
	
	public function connect() {
		$this->ds = DataStoreFactory::create(get_class($this));
		$this->ds->connect($this->container(), $this->collection());
	}
	public function container() {
		return $this->container;
	}
	public function collection() {
		return $this->collection;
	}
	
	public function goesIntoBucketFor($data) {
		if (stristr($data['eventId'], 'store callback') !== false) {
			return $this->ds;
		} else {
			return false;
		}
	}
}

