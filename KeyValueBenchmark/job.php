<?php

class Job {
    public $name = '';
    public $keyValueArray = array();
    public $lookUpKeys = array();
    public $dataStore = null;
    public $attributeName = '';
}

class JobMySQL extends Job {
    public $tableName = '';
    public $keyName = '';     
}

class JobMemcached extends Job {
    public $objectName = '';    
}

