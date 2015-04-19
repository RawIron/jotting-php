<?php

require_once('config.php');
require_once('GearmanWriter.php');
require_once('testEvents.php');


$config = new Config();
$config->setup();

$serverList = array();
foreach ($config->mqConnections as $mqServer) {
	if ($mqServer['driver'] !== 'gearmand') {
		continue;
	}
	$serverList[] = parse_url($mqServer['uri']);
}


$writer = new GearmanWriter($serverList);
$writer->init();

foreach ($events as $event) {
    $event['jsonString'] = utf8_encode(stripslashes($event['jsonString']));
    $writer->sent('json_event', $event);
}
